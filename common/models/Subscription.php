<?php

namespace common\models;

use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * Subscription — a provider's active subscription record
 *
 * @property int    $id
 * @property int    $provider_id
 * @property int    $plan_id
 * @property string $start_date
 * @property string $end_date
 * @property string $status
 * @property string $payment_method
 * @property string $transaction_id
 * @property float  $amount_paid
 * @property string $created_at
 */
class Subscription extends ActiveRecord
{
    const STATUS_ACTIVE    = 'active';
    const STATUS_EXPIRED   = 'expired';
    const STATUS_CANCELLED = 'cancelled';

    public static function tableName(): string
    {
        return '{{%subscriptions}}';
    }

    public function rules(): array
    {
        return [
            [['provider_id', 'plan_id', 'start_date', 'end_date'], 'required'],
            ['provider_id',    'integer'],
            ['plan_id',        'integer'],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_EXPIRED, self::STATUS_CANCELLED]],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['payment_method', 'in', 'range' => ['mpesa', 'card', 'cash', 'manual']],
            ['transaction_id', 'string', 'max' => 255],
            ['amount_paid',    'number', 'min' => 0],
            [['start_date', 'end_date'], 'safe'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'             => 'ID',
            'provider_id'    => 'Provider',
            'plan_id'        => 'Plan',
            'start_date'     => 'Start Date',
            'end_date'       => 'Expiry Date',
            'status'         => 'Status',
            'payment_method' => 'Payment Method',
            'transaction_id' => 'Transaction ID',
            'amount_paid'    => 'Amount Paid',
            'created_at'     => 'Subscribed On',
        ];
    }

    // ── Factory: create a new active subscription ─────────────────────────────

    public static function createFor(Provider $provider, SubscriptionPlan $plan, array $paymentData = []): self
    {
        $sub                 = new self();
        $sub->provider_id    = $provider->id;
        $sub->plan_id        = $plan->id;
        $sub->start_date     = date('Y-m-d H:i:s');
        $sub->end_date       = date('Y-m-d H:i:s', strtotime('+' . $plan->duration_days . ' days'));
        $sub->status         = self::STATUS_ACTIVE;
        $sub->amount_paid    = $plan->price_kes;
        $sub->payment_method = $paymentData['method']         ?? 'mpesa';
        $sub->transaction_id = $paymentData['transaction_id'] ?? null;
        return $sub;
    }

    // ── Status helpers ────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE && strtotime($this->end_date) > time();
    }

    public function daysRemaining(): int
    {
        return max(0, (int)ceil((strtotime($this->end_date) - time()) / 86400));
    }

    public function isExpiringSoon(int $days = 7): bool
    {
        return $this->isActive() && $this->daysRemaining() <= $days;
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function getProvider()
    {
        return $this->hasOne(Provider::class, ['id' => 'provider_id']);
    }

    public function getPlan()
    {
        return $this->hasOne(SubscriptionPlan::class, ['id' => 'plan_id']);
    }
}
