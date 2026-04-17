<?php

namespace common\models;

use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * Payment — transaction record (supports deposit, balance, full, delivery)
 *
 * @property int    $id
 * @property int    $order_id
 * @property float  $amount
 * @property string $currency
 * @property string $payment_stage
 * @property string $method
 * @property string $transaction_id
 * @property string $phone_number
 * @property string $mpesa_receipt
 * @property string $status
 * @property string $failure_reason
 * @property string $paid_at
 * @property string $created_at
 */
class Payment extends ActiveRecord
{
    const STAGE_DEPOSIT  = 'deposit';
    const STAGE_BALANCE  = 'balance';
    const STAGE_FULL     = 'full';
    const STAGE_DELIVERY = 'delivery';

    const METHOD_MPESA = 'mpesa';
    const METHOD_CARD  = 'card';
    const METHOD_CASH  = 'cash';

    const STATUS_PENDING   = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED    = 'failed';
    const STATUS_REFUNDED  = 'refunded';

    public static function tableName(): string
    {
        return '{{%payments}}';
    }

    public function rules(): array
    {
        return [
            [['order_id', 'amount', 'payment_stage', 'method'], 'required'],
            ['order_id',      'integer'],
            ['amount',        'number', 'min' => 0.01],
            ['currency',      'in', 'range' => ['KES', 'USD']],
            ['currency',      'default', 'value' => 'KES'],
            ['payment_stage', 'in', 'range' => [self::STAGE_DEPOSIT, self::STAGE_BALANCE, self::STAGE_FULL, self::STAGE_DELIVERY]],
            ['method',        'in', 'range' => [self::METHOD_MPESA, self::METHOD_CARD, self::METHOD_CASH]],
            ['status',        'in', 'range' => [self::STATUS_PENDING, self::STATUS_COMPLETED, self::STATUS_FAILED, self::STATUS_REFUNDED]],
            ['status',        'default', 'value' => self::STATUS_PENDING],
            ['transaction_id','string', 'max' => 255],
            ['phone_number',  'string', 'max' => 20],
            ['mpesa_receipt', 'string', 'max' => 100],
            ['failure_reason','string', 'max' => 500],
            ['paid_at',       'safe'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'             => 'Payment ID',
            'order_id'       => 'Order',
            'amount'         => 'Amount',
            'currency'       => 'Currency',
            'payment_stage'  => 'Stage',
            'method'         => 'Method',
            'transaction_id' => 'Transaction ID',
            'mpesa_receipt'  => 'M-Pesa Receipt',
            'status'         => 'Status',
            'paid_at'        => 'Paid At',
        ];
    }

    public function isCompleted(): bool { return $this->status === self::STATUS_COMPLETED; }
    public function isPending(): bool   { return $this->status === self::STATUS_PENDING; }

    public function markCompleted(string $transactionId, ?string $mpesaReceipt = null): bool
    {
        $this->status         = self::STATUS_COMPLETED;
        $this->transaction_id = $transactionId;
        $this->mpesa_receipt  = $mpesaReceipt;
        $this->paid_at        = (new Expression('NOW()'))->expression;
        return $this->save(false);
    }

    public function markFailed(string $reason): bool
    {
        $this->status         = self::STATUS_FAILED;
        $this->failure_reason = $reason;
        return $this->save(false);
    }

public function methodLabel(): string
    {
        if ($this->method === self::METHOD_MPESA) return 'M-Pesa';
        if ($this->method === self::METHOD_CARD) return 'Card';
        if ($this->method === self::METHOD_CASH) return 'Cash';
        return ucfirst($this->method);
    }

public function stageLabel(): string
    {
        if ($this->payment_stage === self::STAGE_DEPOSIT) return 'Deposit';
        if ($this->payment_stage === self::STAGE_BALANCE) return 'Balance';
        if ($this->payment_stage === self::STAGE_FULL) return 'Full Payment';
        if ($this->payment_stage === self::STAGE_DELIVERY) return 'Payment on Delivery';
        return ucfirst($this->payment_stage);
    }

    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }
}
