<?php

namespace common\components;

use Yii;
use yii\base\Component;
use common\models\Order;
use common\models\Payment;

/**
 * MpesaService — Safaricom Daraja API integration (STK Push + Callback)
 *
 * Supports: sandbox and production
 * Configure via .env:  MPESA_ENV, MPESA_CONSUMER_KEY, MPESA_CONSUMER_SECRET,
 *                      MPESA_SHORTCODE, MPESA_PASSKEY, MPESA_CALLBACK_URL
 */
class MpesaService extends Component
{
    // ── Config (loaded from .env) ─────────────────────────────────────────────
    public string $env            = 'sandbox';
    public string $consumerKey    = '';
    public string $consumerSecret = '';
    public string $shortcode      = '174379';   // Daraja sandbox shortcode
    public string $passkey        = '';
    public string $callbackUrl    = '';
    public string $accountRef     = 'HudumaLynk';

    private string $sandboxBaseUrl    = 'https://sandbox.safaricom.co.ke';
    private string $productionBaseUrl = 'https://api.safaricom.co.ke';

    public function init(): void
    {
        parent::init();
        $this->env            = $_ENV['MPESA_ENV']            ?? 'sandbox';
        $this->consumerKey    = $_ENV['MPESA_CONSUMER_KEY']   ?? '';
        $this->consumerSecret = $_ENV['MPESA_CONSUMER_SECRET'] ?? '';
        $this->shortcode      = $_ENV['MPESA_SHORTCODE']      ?? '174379';
        $this->passkey        = $_ENV['MPESA_PASSKEY']        ?? '';
        $this->callbackUrl    = $_ENV['MPESA_CALLBACK_URL']   ?? '';
        $this->accountRef     = $_ENV['MPESA_ACCOUNT_REF']    ?? 'HudumaLynk';
    }

    private function baseUrl(): string
    {
        return $this->env === 'production' ? $this->productionBaseUrl : $this->sandboxBaseUrl;
    }

    // ── OAuth Token ───────────────────────────────────────────────────────────

    public function getAccessToken(): ?string
    {
        $cacheKey = 'mpesa_access_token';
        $cached   = Yii::$app->cache->get($cacheKey);
        if ($cached) {
            return $cached;
        }

        $url = $this->baseUrl() . '/oauth/v1/generate?grant_type=client_credentials';
        $ch  = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Basic ' . base64_encode($this->consumerKey . ':' . $this->consumerSecret)],
            CURLOPT_TIMEOUT        => 15,
        ]);
        $response = json_decode(curl_exec($ch), true);

        $token = $response['access_token'] ?? null;
        if ($token) {
            Yii::$app->cache->set($cacheKey, $token, 3540); // cache ~59 min (token valid 60 min)
        } else {
            Yii::error('[M-Pesa] Failed to get access token: ' . json_encode($response), 'mpesa');
        }
        return $token;
    }

    // ── STK Push (Lipa Na M-Pesa Online) ─────────────────────────────────────

    /**
     * Initiate an STK Push payment request.
     *
     * @param Order  $order         The order to pay for
     * @param string $phone         Customer phone (format: 2547XXXXXXXX)
     * @param float  $amount        Amount to collect (KES)
     * @param string $paymentStage  Payment::STAGE_*
     * @return array ['success' => bool, 'checkout_request_id' => string, 'message' => string]
     */
    public function stkPush(Order $order, string $phone, float $amount, string $paymentStage = Payment::STAGE_FULL): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return ['success' => false, 'message' => 'Could not connect to M-Pesa. Try again shortly.'];
        }

        $timestamp = date('YmdHis');
        $password  = base64_encode($this->shortcode . $this->passkey . $timestamp);
        $phone     = $this->normalizePhone($phone);
        $amount    = (int) ceil($amount); // M-Pesa requires integer KES amount

        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'TransactionType'   => 'CustomerPayBillOnline',
            'Amount'            => $amount,
            'PartyA'            => $phone,
            'PartyB'            => $this->shortcode,
            'PhoneNumber'       => $phone,
            'CallBackURL'       => $this->callbackUrl,
            'AccountReference'  => $this->accountRef . '-' . $order->reference,
            'TransactionDesc'   => 'Payment for order ' . $order->reference,
        ];

        $url = $this->baseUrl() . '/mpesa/stkpush/v1/processrequest';
        $ch  = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT        => 30,
        ]);
        $response = json_decode(curl_exec($ch), true);

        Yii::info('[M-Pesa] STK Push response: ' . json_encode($response), 'mpesa');

        if (($response['ResponseCode'] ?? '') === '0') {
            // Create a pending payment record
            $payment                = new Payment();
            $payment->order_id      = $order->id;
            $payment->amount        = $amount;
            $payment->payment_stage = $paymentStage;
            $payment->method        = Payment::METHOD_MPESA;
            $payment->transaction_id = $response['CheckoutRequestID'];
            $payment->phone_number  = $phone;
            $payment->status        = Payment::STATUS_PENDING;
            $payment->save(false);

            return [
                'success'              => true,
                'checkout_request_id'  => $response['CheckoutRequestID'],
                'message'              => 'STK Push sent. Check your phone and enter your M-Pesa PIN.',
            ];
        }

        return [
            'success' => false,
            'message' => $response['errorMessage']
                ?? ($response['ResponseDescription'] ?? 'M-Pesa request failed. Please try again.'),
        ];
    }

    // ── Callback Handler ──────────────────────────────────────────────────────

    /**
     * Process M-Pesa STK Push callback from Safaricom.
     * Call this from your callback controller action.
     *
     * @param array $callbackData  Raw decoded JSON from Safaricom
     * @return bool                True if payment was confirmed, false on failure/cancellation
     */
    public function handleCallback(array $callbackData): bool
    {
        Yii::info('[M-Pesa] Callback received: ' . json_encode($callbackData), 'mpesa');

        $body        = $callbackData['Body']['stkCallback'] ?? [];
        $resultCode  = $body['ResultCode']  ?? -1;
        $requestId   = $body['CheckoutRequestID'] ?? '';
        $resultDesc  = $body['ResultDesc'] ?? 'Unknown';

        $payment = Payment::findOne(['transaction_id' => $requestId, 'method' => Payment::METHOD_MPESA]);
        if (!$payment) {
            Yii::warning('[M-Pesa] Callback payment not found for ID: ' . $requestId, 'mpesa');
            return false;
        }

        if ($resultCode !== 0) {
            // Payment failed or cancelled
            $payment->markFailed($resultDesc);
            $this->onPaymentFailed($payment);
            return false;
        }

        // Extract M-Pesa receipt from callback metadata
        $items   = $body['CallbackMetadata']['Item'] ?? [];
        $meta    = array_column($items, 'Value', 'Name');
        $receipt = $meta['MpesaReceiptNumber'] ?? null;

        $payment->markCompleted($requestId, $receipt);
        $this->onPaymentSuccess($payment);
        return true;
    }

    // ── Payment outcome handlers ──────────────────────────────────────────────

    private function onPaymentSuccess(Payment $payment): void
    {
        $order = $payment->order;
        if (!$order) return;

        // Update order status based on payment stage
        switch ($payment->payment_stage) {
            case Payment::STAGE_FULL:
                $order->payment_status = Order::PAYMENT_STATUS_PAID;
                $order->status         = Order::STATUS_PROCESSING;
                break;
            case Payment::STAGE_DEPOSIT:
                $order->payment_status = Order::PAYMENT_STATUS_PARTIAL;
                $order->deposit_amount = $payment->amount;
                $order->balance_amount = $order->total_amount - $payment->amount;
                $order->status         = Order::STATUS_DEPOSIT_PAID;
                break;
            case Payment::STAGE_BALANCE:
                $order->payment_status = Order::PAYMENT_STATUS_PAID;
                $order->status         = Order::STATUS_PROCESSING;
                break;
        }
        $order->save(false);

        // Notify customer and provider
        if ($order->user && $order->provider && $order->provider->user) {
            Yii::$app->sms->paymentConfirmed($order->user, $order);
        }

        Yii::info('[M-Pesa] Payment confirmed for order ' . $order->reference, 'mpesa');
    }

    private function onPaymentFailed(Payment $payment): void
    {
        $order = $payment->order;
        if (!$order) return;

        if (!$order->isPaid()) {
            $order->status = Order::STATUS_FAILED;
            $order->save(false);
        }
        Yii::warning('[M-Pesa] Payment failed for order ' . ($order->reference ?? $order->id), 'mpesa');
    }

    // ── STK Push status query ─────────────────────────────────────────────────

    public function queryStatus(string $checkoutRequestId): array
    {
        $token = $this->getAccessToken();
        if (!$token) return ['success' => false, 'message' => 'Auth error.'];

        $timestamp = date('YmdHis');
        $password  = base64_encode($this->shortcode . $this->passkey . $timestamp);

        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'CheckoutRequestID' => $checkoutRequestId,
        ];

        $url = $this->baseUrl() . '/mpesa/stkpushquery/v1/query';
        $ch  = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $token, 'Content-Type: application/json'],
            CURLOPT_TIMEOUT        => 15,
        ]);
        $response = json_decode(curl_exec($ch), true);

        return [
            'success'     => ($response['ResultCode'] ?? -1) === 0,
            'result_code' => $response['ResultCode']  ?? -1,
            'message'     => $response['ResultDesc']  ?? 'Unknown status',
        ];
    }

    // ── Phone normalizer ──────────────────────────────────────────────────────

    /** Converts 07XXXXXXXX or +2547XXXXXXXX → 2547XXXXXXXX */
    public function normalizePhone(string $phone): string
    {
        return \common\models\User::normalizePhone($phone);
    }
}
