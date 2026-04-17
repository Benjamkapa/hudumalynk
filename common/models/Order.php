<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * Order model — central order record
 *
 * @property int    $id
 * @property int    $user_id
 * @property int    $provider_id
 * @property string $reference
 * @property float  $total_amount
 * @property float  $deposit_amount
 * @property float  $balance_amount
 * @property string $payment_type
 * @property string $status
 * @property string $payment_status
 * @property string $delivery_address
 * @property string $notes
 * @property string $currency
 * @property string $created_at
 * @property string $updated_at
 */
class Order extends ActiveRecord
{
    // ── Payment types ─────────────────────────────────────────────────────────
    const PAYMENT_FULL = 'full';
    const PAYMENT_PARTIAL = 'partial';
    const PAYMENT_DELIVERY = 'delivery';

    // ── Order statuses ────────────────────────────────────────────────────────
    const STATUS_PENDING = 'pending';
    const STATUS_AWAITING_PAYMENT = 'awaiting_payment';
    const STATUS_AWAITING_DEPOSIT = 'awaiting_deposit';
    const STATUS_DEPOSIT_PAID = 'deposit_paid';
    const STATUS_PROCESSING = 'processing';
    const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_FAILED = 'failed';

    // ── Payment statuses ──────────────────────────────────────────────────────
    const PAYMENT_STATUS_UNPAID = 'unpaid';
    const PAYMENT_STATUS_PARTIAL = 'partial';
    const PAYMENT_STATUS_PAID = 'paid';

    public static function tableName(): string
    {
        return '{{%orders}}';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['user_id', 'provider_id', 'total_amount', 'payment_type'], 'required'],
            [['user_id', 'provider_id'], 'integer'],
            [['total_amount', 'deposit_amount', 'balance_amount'], 'number', 'min' => 0],
            ['payment_type', 'in', 'range' => [self::PAYMENT_FULL, self::PAYMENT_PARTIAL, self::PAYMENT_DELIVERY]],
            [
                'status',
                'in',
                'range' => [
                    self::STATUS_PENDING,
                    self::STATUS_AWAITING_PAYMENT,
                    self::STATUS_AWAITING_DEPOSIT,
                    self::STATUS_DEPOSIT_PAID,
                    self::STATUS_PROCESSING,
                    self::STATUS_OUT_FOR_DELIVERY,
                    self::STATUS_COMPLETED,
                    self::STATUS_CANCELLED,
                    self::STATUS_FAILED,
                ]
            ],
            [
                'payment_status',
                'in',
                'range' => [
                    self::PAYMENT_STATUS_UNPAID,
                    self::PAYMENT_STATUS_PARTIAL,
                    self::PAYMENT_STATUS_PAID,
                ]
            ],
            ['delivery_address', 'string'],
            ['notes', 'string'],
            ['currency', 'string', 'max' => 3],
            ['currency', 'in', 'range' => ['KES', 'USD']],
            ['currency', 'default', 'value' => 'KES'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'Order ID',
            'reference' => 'Reference',
            'user_id' => 'Customer',
            'provider_id' => 'Provider',
            'total_amount' => 'Total',
            'deposit_amount' => 'Deposit',
            'balance_amount' => 'Balance',
            'payment_type' => 'Payment Type',
            'status' => 'Status',
            'payment_status' => 'Payment Status',
            'delivery_address' => 'Delivery Address',
            'notes' => 'Notes',
            'created_at' => 'Placed On',
        ];
    }

    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->reference = $this->generateReference();
            }
            return true;
        }
        return false;
    }

    // ── Reference generation (HL-YYYYMMDD-XXXXX) ─────────────────────────────

    private function generateReference(): string
    {
        do {
            $ref = 'HL-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
        } while (static::find()->where(['reference' => $ref])->exists());
        return $ref;
    }

    // ── Payment type resolver based on amount and config ─────────────────────

    public static function resolvePaymentType(float $amount): string
    {
        $t = Yii::$app->params['orderThresholds'];
        if ($amount <= $t['cod_max']) {
            return self::PAYMENT_DELIVERY;
        }
        if ($amount <= $t['deposit_max']) {
            return self::PAYMENT_PARTIAL;
        }
        return self::PAYMENT_FULL;
    }

    public static function calculateDeposit(float $total): float
    {
        $percent = Yii::$app->params['orderThresholds']['deposit_percent'] ?? 30;
        return round($total * $percent / 100, 2);
    }

    // ── Status helpers ────────────────────────────────────────────────────────

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_STATUS_PAID;
    }
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_AWAITING_PAYMENT,
            self::STATUS_AWAITING_DEPOSIT,
        ]);
    }

    public function canLeaveReview(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function statusBadgeClass(): string
    {
        if ($this->status === self::STATUS_COMPLETED) {
            return 'success';
        }
        if (
            in_array($this->status, [
                self::STATUS_PROCESSING,
                self::STATUS_DEPOSIT_PAID,
                self::STATUS_OUT_FOR_DELIVERY,
            ])
        ) {
            return 'primary';
        }
        if (
            in_array($this->status, [
                self::STATUS_CANCELLED,
                self::STATUS_FAILED,
            ])
        ) {
            return 'danger';
        }
        return 'warning';
    }

    public function statusLabel(): string
    {
        $labels = [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_AWAITING_PAYMENT => 'Awaiting Payment',
            self::STATUS_AWAITING_DEPOSIT => 'Awaiting Deposit',
            self::STATUS_DEPOSIT_PAID => 'Deposit Paid',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_OUT_FOR_DELIVERY => 'Out for Delivery',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_FAILED => 'Failed',
        ];
        return $labels[$this->status] ?? ucfirst($this->status);
    }

    // ── Amount display ────────────────────────────────────────────────────────

    public function formattedTotal(string $currency = 'KES'): string
    {
        if ($currency === 'USD') {
            $rate = (float) (Yii::$app->params['kesToUsdRate'] ?? 0.0077);
            return 'USD ' . number_format($this->total_amount * $rate, 2);
        }
        return 'KES ' . number_format($this->total_amount, 2);
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getProvider()
    {
        return $this->hasOne(Provider::class, ['id' => 'provider_id']);
    }

    public function getItems()
    {
        return $this->hasMany(OrderItem::class, ['order_id' => 'id']);
    }

    public function getPayments()
    {
        return $this->hasMany(Payment::class, ['order_id' => 'id'])->orderBy(['created_at' => SORT_DESC]);
    }

    public function getReview()
    {
        return $this->hasOne(Review::class, ['order_id' => 'id']);
    }

    public function getDelivery()
    {
        return $this->hasOne(Delivery::class, ['order_id' => 'id']);
    }

    public function getCommission()
    {
        return $this->hasOne(Commission::class, ['order_id' => 'id']);
    }
}
