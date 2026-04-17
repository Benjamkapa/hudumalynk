<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * Commission — platform earnings per completed order
 *
 * @property int    $id
 * @property int    $order_id
 * @property int    $provider_id
 * @property float  $order_amount
 * @property float  $rate
 * @property float  $amount
 * @property string $status
 * @property string $paid_at
 * @property string $created_at
 */
class Commission extends ActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_PAID    = 'paid';

    public static function tableName(): string
    {
        return '{{%commissions}}';
    }

    public function rules(): array
    {
        return [
            [['order_id', 'provider_id', 'order_amount', 'rate', 'amount'], 'required'],
            ['order_id',    'integer'],
            ['provider_id', 'integer'],
            ['order_amount','number', 'min' => 0],
            ['rate',        'number', 'min' => 0, 'max' => 100],
            ['amount',      'number', 'min' => 0],
            ['status', 'in', 'range' => [self::STATUS_PENDING, self::STATUS_PAID]],
            ['status', 'default', 'value' => self::STATUS_PENDING],
            ['paid_at', 'safe'],
        ];
    }

    /** Factory: build commission from an order */
    public static function createFromOrder(Order $order): self
    {
        $rate           = (float)(\Yii::$app->params['commissionRate'] ?? 10);
        $c              = new self();
        $c->order_id    = $order->id;
        $c->provider_id = $order->provider_id;
        $c->order_amount = $order->total_amount;
        $c->rate        = $rate;
        $c->amount      = round($order->total_amount * $rate / 100, 2);
        return $c;
    }

    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    public function getProvider()
    {
        return $this->hasOne(Provider::class, ['id' => 'provider_id']);
    }
}
