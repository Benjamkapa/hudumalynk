<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * OrderItem — line item snapshot in an order
 *
 * @property int    $id
 * @property int    $order_id
 * @property int    $listing_id
 * @property string $name
 * @property string $type
 * @property int    $quantity
 * @property float  $price
 * @property float  $total
 */
class OrderItem extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%order_items}}';
    }

    public function rules(): array
    {
        return [
            [['order_id', 'name', 'type', 'price'], 'required'],
            ['order_id',   'integer'],
            ['listing_id', 'integer'],
            ['quantity',   'integer', 'min' => 1],
            ['quantity',   'default', 'value' => 1],
            ['price',      'number', 'min' => 0],
            ['total',      'number', 'min' => 0],
            ['type',       'in', 'range' => [Listing::TYPE_PRODUCT, Listing::TYPE_SERVICE]],
            ['name',       'string', 'max' => 255],
        ];
    }

    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            $this->total = round($this->price * $this->quantity, 2);
            return true;
        }
        return false;
    }

    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    public function getListing()
    {
        return $this->hasOne(Listing::class, ['id' => 'listing_id']);
    }
}
