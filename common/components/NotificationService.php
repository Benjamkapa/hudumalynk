<?php

namespace common\components;

use Yii;
use yii\base\Component;
use common\models\Notification;
use common\models\User;

/**
 * NotificationService — sends in-app, SMS, and email notifications
 *
 * Usage:  Yii::$app->sms->notify($user, Notification::TYPE_ORDER_PLACED, [...])
 */
class NotificationService extends Component
{
    /** @var SmsInterface|null Injected SMS driver */
    public ?SmsInterface $smsDriver = null;

    public bool $sendSms = true;
    public bool $sendEmail = true;

    // public function init(): void
    // {
    //     parent::init();

    //     // Auto-wire SMS driver from config
    //     if ($this->smsDriver === null) {
    //         $provider = $_ENV['SMS_PROVIDER'] ?? 'africastalking';
    //         $this->smsDriver = match ($provider) {
    //             'africastalking' => new AfricasTalkingSms(),
    //             default          => null, // Add other drivers here
    //         };
    //     }
    // }

    public function init(): void
    {
        parent::init();

        if ($this->smsDriver === null) {
            $provider = $_ENV['SMS_PROVIDER'] ?? 'africastalking';
            if ($provider === 'africastalking') {
                $this->smsDriver = new AfricasTalkingSms();
            }
            // Add other drivers here
        }
    }

    // ── Core dispatcher ───────────────────────────────────────────────────────

    /**
     * Create an in-app notification and optionally send SMS + email.
     *
     * @param User   $user
     * @param string $type    One of Notification::TYPE_* constants
     * @param array  $data    Context data (order_id, etc.)
     * @param bool   $viaSms
     * @param bool   $viaEmail
     */
    public function notify(
        User $user,
        string $type,
        array $data = [],
        bool $viaSms = false,
        bool $viaEmail = false
    ): void {
        [$title, $message] = $this->buildMessage($type, $data);

        // 1. In-app notification
        $n = new Notification();
        $n->user_id = $user->id;
        $n->type = $type;
        $n->title = $title;
        $n->message = $message;
        $n->data = $data ? json_encode($data) : null;
        $n->sent_via_sms = false;
        $n->sent_via_email = false;

        // 2. SMS
        if ($viaSms && $this->sendSms && $this->smsDriver && $user->phone) {
            $n->sent_via_sms = $this->smsDriver->send($user->phone, $message);
        }

        // 3. Email
        if ($viaEmail && $this->sendEmail && $user->email) {
            try {
                Yii::$app->mailer->compose(['html' => '@common/mail/notification', 'text' => '@common/mail/notification-text'], [
                    'user' => $user,
                    'title' => $title,
                    'message' => $message,
                    'data' => $data,
                ])
                    ->setTo([$user->email => $user->getFullName()])
                    ->setSubject($title . ' — HudumaLynk')
                    ->send();
                $n->sent_via_email = true;
            } catch (\Exception $e) {
                Yii::error('[Email] Failed to send to ' . $user->email . ': ' . $e->getMessage(), 'email');
            }
        }

        $n->save(false);
    }

    // ── Convenience shortcuts ─────────────────────────────────────────────────

    public function orderPlaced(User $customer, User $provider, \common\models\Order $order): void
    {
        $data = ['order_id' => $order->id, 'reference' => $order->reference];
        $this->notify($customer, Notification::TYPE_ORDER_PLACED, $data, true, true);
        $this->notify($provider, Notification::TYPE_ORDER_PLACED, $data, true, false);
    }

    public function orderAccepted(User $customer, \common\models\Order $order): void
    {
        $this->notify(
            $customer,
            Notification::TYPE_ORDER_ACCEPTED,
            ['order_id' => $order->id, 'reference' => $order->reference],
            true,
            true
        );
    }

    public function orderCompleted(User $customer, \common\models\Order $order): void
    {
        $this->notify(
            $customer,
            Notification::TYPE_ORDER_COMPLETED,
            ['order_id' => $order->id, 'reference' => $order->reference],
            true,
            true
        );
    }

    public function paymentConfirmed(User $user, \common\models\Order $order): void
    {
        $this->notify(
            $user,
            Notification::TYPE_PAYMENT_CONFIRMED,
            ['order_id' => $order->id, 'amount' => $order->total_amount],
            true,
            true
        );
    }

    public function subscriptionExpiring(User $user, int $daysLeft): void
    {
        $this->notify(
            $user,
            Notification::TYPE_SUB_EXPIRING,
            ['days_left' => $daysLeft],
            true,
            true
        );
    }

    public function providerApproved(User $user): void
    {
        $this->notify($user, Notification::TYPE_PROVIDER_APPROVED, [], true, true);
    }

    public function providerRejected(User $user, string $reason): void
    {
        $this->notify($user, Notification::TYPE_PROVIDER_REJECTED, ['reason' => $reason], true, true);
    }

    // ── Message builder ───────────────────────────────────────────────────────

    // private function buildMessage(string $type, array $data): array
    // {
    //     $ref = $data['reference'] ?? '#' . ($data['order_id'] ?? '');
    //     return match ($type) {
    //         Notification::TYPE_ORDER_PLACED      => ['New Order Received',          "Order {$ref} has been placed successfully."],
    //         Notification::TYPE_ORDER_ACCEPTED    => ['Order Accepted',              "Great news! Order {$ref} has been accepted and is being processed."],
    //         Notification::TYPE_ORDER_REJECTED    => ['Order Rejected',              "Unfortunately, order {$ref} was rejected by the provider."],
    //         Notification::TYPE_ORDER_COMPLETED   => ['Order Completed',             "Order {$ref} is complete. Please leave a review!"],
    //         Notification::TYPE_ORDER_CANCELLED   => ['Order Cancelled',             "Order {$ref} has been cancelled."],
    //         Notification::TYPE_PAYMENT_CONFIRMED => ['Payment Confirmed',           'Your payment of KES ' . number_format($data['amount'] ?? 0, 2) . ' has been confirmed.'],
    //         Notification::TYPE_PAYMENT_FAILED    => ['Payment Failed',              'Your payment could not be processed. Please try again.'],
    //         Notification::TYPE_REVIEW_RECEIVED   => ['New Review',                 'You have received a new review on your business.'],
    //         Notification::TYPE_SUB_EXPIRING      => ['Subscription Expiring Soon',  'Your HudumaLynk subscription expires in ' . ($data['days_left'] ?? '') . ' day(s). Renew now to keep your listings active.'],
    //         Notification::TYPE_SUB_EXPIRED       => ['Subscription Expired',        'Your subscription has expired. Your listings are now inactive. Please renew to continue receiving orders.'],
    //         Notification::TYPE_PROVIDER_APPROVED => ['Account Approved! 🎉',        'Congratulations! Your provider account on HudumaLynk has been approved. You can now subscribe and start listing.'],
    //         Notification::TYPE_PROVIDER_REJECTED => ['Account Not Approved',        'Your provider application was not approved. Reason: ' . ($data['reason'] ?? 'Contact support.')],
    //         default                              => ['HudumaLynk Notification',     $data['message'] ?? ''],
    //     };
    // }

    private function buildMessage(string $type, array $data): array
    {
        $ref = $data['reference'] ?? '#' . ($data['order_id'] ?? '');

        $messages = [
            Notification::TYPE_ORDER_PLACED => ['New Order Received', "Order {$ref} has been placed successfully."],
            Notification::TYPE_ORDER_ACCEPTED => ['Order Accepted', "Great news! Order {$ref} has been accepted and is being processed."],
            Notification::TYPE_ORDER_REJECTED => ['Order Rejected', "Unfortunately, order {$ref} was rejected by the provider."],
            Notification::TYPE_ORDER_COMPLETED => ['Order Completed', "Order {$ref} is complete. Please leave a review!"],
            Notification::TYPE_ORDER_CANCELLED => ['Order Cancelled', "Order {$ref} has been cancelled."],
            Notification::TYPE_PAYMENT_CONFIRMED => ['Payment Confirmed', 'Your payment of KES ' . number_format($data['amount'] ?? 0, 2) . ' has been confirmed.'],
            Notification::TYPE_PAYMENT_FAILED => ['Payment Failed', 'Your payment could not be processed. Please try again.'],
            Notification::TYPE_REVIEW_RECEIVED => ['New Review', 'You have received a new review on your business.'],
            Notification::TYPE_SUB_EXPIRING => ['Subscription Expiring Soon', 'Your HudumaLynk subscription expires in ' . ($data['days_left'] ?? '') . ' day(s). Renew now to keep your listings active.'],
            Notification::TYPE_SUB_EXPIRED => ['Subscription Expired', 'Your subscription has expired. Your listings are now inactive. Please renew to continue receiving orders.'],
            Notification::TYPE_PROVIDER_APPROVED => ['Account Approved! 🎉', 'Congratulations! Your provider account on HudumaLynk has been approved. You can now subscribe and start listing.'],
            Notification::TYPE_PROVIDER_REJECTED => ['Account Not Approved', 'Your provider application was not approved. Reason: ' . ($data['reason'] ?? 'Contact support.')],
        ];

        return $messages[$type] ?? ['HudumaLynk Notification', $data['message'] ?? ''];
    }
}
