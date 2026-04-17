<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;
use common\models\Listing;
use common\models\Order;
use common\models\OrderItem;
use common\models\Payment;
use common\models\Commission;

class OrderController extends Controller
{
    public $layout = 'main';

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [[
                    'allow' => true,
                    'roles' => ['@'],
                ]],
            ],
        ];
    }

    /** Step 1 — Review listing and choose payment type */
    public function actionCreate()
    {
        $listingId = (int)Yii::$app->request->get('listing_id');
        $listing   = Listing::find()->with(['provider', 'images'])->where(['id' => $listingId, 'status' => Listing::STATUS_ACTIVE])->one();

        if (!$listing) {
            throw new NotFoundHttpException('Listing not found.');
        }
        if (!$listing->provider || !$listing->provider->canPublishListings()) {
            throw new NotFoundHttpException('This listing is temporarily unavailable.');
        }

        $quantity    = max(1, (int)Yii::$app->request->post('quantity', 1));
        $totalAmount = $listing->price * $quantity;
        $paymentType = Order::resolvePaymentType($totalAmount);
        $deposit     = $paymentType === Order::PAYMENT_PARTIAL ? Order::calculateDeposit($totalAmount) : 0;
        $prices      = Yii::$app->currency->both($totalAmount);

        if (Yii::$app->request->isPost && Yii::$app->request->post('confirm')) {
            $chosenType = Yii::$app->request->post('payment_type', $paymentType);
            $address    = Yii::$app->request->post('delivery_address', '');
            $notes      = Yii::$app->request->post('notes', '');

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $order                 = new Order();
                $order->user_id        = Yii::$app->user->id;
                $order->provider_id    = $listing->provider_id;
                $order->total_amount   = $totalAmount;
                $order->payment_type   = $chosenType;
                $order->deposit_amount = $chosenType === Order::PAYMENT_PARTIAL ? Order::calculateDeposit($totalAmount) : 0;
                $order->balance_amount = $chosenType === Order::PAYMENT_PARTIAL ? $totalAmount - $order->deposit_amount : 0;
                $order->delivery_address = $address;
                $order->notes          = $notes;
                $order->status         = $chosenType === Order::PAYMENT_DELIVERY
                    ? Order::STATUS_PROCESSING
                    : Order::STATUS_AWAITING_PAYMENT;
                $order->payment_status = Order::PAYMENT_STATUS_UNPAID;
                $order->save(false);

                $item             = new OrderItem();
                $item->order_id   = $order->id;
                $item->listing_id = $listing->id;
                $item->name       = $listing->name;
                $item->type       = $listing->type;
                $item->quantity   = $quantity;
                $item->price      = $listing->price;
                $item->save(false);

                $transaction->commit();

                // Notify provider
                Yii::$app->sms->orderPlaced(
                    Yii::$app->user->identity,
                    $listing->provider->user,
                    $order
                );

                if ($chosenType === Order::PAYMENT_DELIVERY) {
                    Yii::$app->session->setFlash('success', 'Order placed! Since you chose "Pay on Delivery", the provider will contact you shortly. You will pay KES ' . number_format($totalAmount, 2) . ' directly to them upon completion/delivery.');
                    return $this->redirect(['/orders/' . $order->id]);
                }

                return $this->redirect(['/order/pay', 'id' => $order->id]);

            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::error('[Order] Fatal error during creation: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
                Yii::$app->session->setFlash('danger', 'Could not place order due to a system error. Please contact support.');
            }
        }

        $this->view->title = 'Place Order — ' . $listing->name;
        return $this->render('create', compact('listing', 'quantity', 'totalAmount', 'paymentType', 'deposit', 'prices'));
    }

    /** Step 2 — Payment (M-Pesa STK Push or Flutterwave) */
    public function actionPay(int $id)
    {
        $order = $this->findOrder($id);
        if ($order->isPaid()) {
            return $this->redirect(['/orders/' . $id]);
        }

        $amountToPay = $order->payment_type === Order::PAYMENT_PARTIAL
            ? $order->deposit_amount
            : $order->total_amount;

        $prices = Yii::$app->currency->both($amountToPay);

        if (Yii::$app->request->isPost) {
            $method = Yii::$app->request->post('method', 'mpesa');
            $phone  = Yii::$app->request->post('phone', Yii::$app->user->identity->phone);
            $stage  = $order->payment_type === Order::PAYMENT_PARTIAL ? Payment::STAGE_DEPOSIT : Payment::STAGE_FULL;

            if ($method === 'mpesa') {
                $result = Yii::$app->mpesa->stkPush($order, $phone, $amountToPay, $stage);
                if ($result['success']) {
                    Yii::$app->session->set('mpesa_checkout_id', $result['checkout_request_id']);
                    return $this->render('pay-waiting', [
                        'order'              => $order,
                        'checkoutRequestId'  => $result['checkout_request_id'],
                        'amountToPay'        => $amountToPay,
                        'prices'             => $prices,
                    ]);
                }
                Yii::$app->session->setFlash('danger', $result['message']);
            }
        }

        $this->view->title = 'Pay for Order ' . $order->reference;
        return $this->render('pay', compact('order', 'amountToPay', 'prices'));
    }

    /** M-Pesa async callback (called by Safaricom) */
    public function actionMpesaCallback()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $raw  = Yii::$app->request->rawBody;
        $data = json_decode($raw, true) ?? [];
        Yii::$app->mpesa->handleCallback($data);
        return ['ResultCode' => 0, 'ResultDesc' => 'Success'];
    }

    /** Ajax: poll payment status */
    public function actionPaymentStatus()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $checkoutId = Yii::$app->request->get('id');
        $payment    = Payment::findOne(['transaction_id' => $checkoutId]);

        if (!$payment) return ['paid' => false, 'failed' => false];

        return [
            'paid'   => $payment->isCompleted(),
            'failed' => $payment->status === Payment::STATUS_FAILED,
        ];
    }

    /** My orders list */
    public function actionIndex()
    {
        $orders = Order::find()
            ->with(['items', 'payments'])
            ->where(['user_id' => Yii::$app->user->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        $this->view->title = 'My Orders';
        return $this->render('index', compact('orders'));
    }

    /** Single order detail */
    public function actionView(int $id)
    {
        $order = $this->findOrder($id);
        $this->view->title = 'Order ' . $order->reference;
        return $this->render('view', [
            'order'  => $order,
            'prices' => Yii::$app->currency->both($order->total_amount),
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function findOrder(int $id): Order
    {
        $order = Order::find()->with(['items', 'provider', 'payments'])->where(['id' => $id, 'user_id' => Yii::$app->user->id])->one();
        if (!$order) throw new NotFoundHttpException('Order not found.');
        return $order;
    }
}
