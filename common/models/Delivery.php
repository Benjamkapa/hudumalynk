<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * Delivery — basic delivery tracking record (Phase 1)
 *
 * @property int    $id
 * @property int    $order_id
 * @property string $driver_name
 * @property string $driver_phone
 * @property string $status
 * @property string $notes
 * @property string $estimated_time
 * @property string $created_at
 * @property string $updated_at
 */
class Delivery extends ActiveRecord
{
    const STATUS_PENDING          = 'pending';
    const STATUS_PICKED           = 'picked';
    const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    const STATUS_DELIVERED        = 'delivered';
    const STATUS_FAILED           = 'failed';

    public static function tableName(): string
    {
        return '{{%deliveries}}';
    }

    public function rules(): array
    {
        return [
            [['order_id'], 'required'],
            ['order_id',       'integer'],
            ['driver_name',    'string', 'max' => 150],
            ['driver_phone',   'string', 'max' => 20],
            ['notes',          'string'],
            ['estimated_time', 'string', 'max' => 100],
            ['status', 'in', 'range' => [
                self::STATUS_PENDING, self::STATUS_PICKED,
                self::STATUS_OUT_FOR_DELIVERY, self::STATUS_DELIVERED, self::STATUS_FAILED,
            ]],
            ['status', 'default', 'value' => self::STATUS_PENDING],
        ];
    }

    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }
}
