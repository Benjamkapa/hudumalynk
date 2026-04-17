<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * Review — customer review for a completed order
 *
 * @property int    $id
 * @property int    $user_id
 * @property int    $provider_id
 * @property int    $order_id
 * @property int    $rating
 * @property string $title
 * @property string $comment
 * @property bool   $is_verified
 * @property string $status
 * @property string $created_at
 */
class Review extends ActiveRecord
{
    const STATUS_VISIBLE = 'visible';
    const STATUS_HIDDEN  = 'hidden';

    public static function tableName(): string
    {
        return '{{%reviews}}';
    }

    public function rules(): array
    {
        return [
            [['user_id', 'provider_id', 'order_id', 'rating'], 'required'],
            ['rating',   'integer', 'min' => 1, 'max' => 5],
            ['user_id',  'integer'],
            ['provider_id', 'integer'],
            ['order_id', 'integer'],
            ['order_id', 'unique', 'message' => 'You have already reviewed this order.'],
            ['title',    'string', 'max' => 255],
            ['comment',  'string'],
            ['is_verified', 'boolean'],
            ['is_verified', 'default', 'value' => true],
            ['status', 'in', 'range' => [self::STATUS_VISIBLE, self::STATUS_HIDDEN]],
            ['status', 'default', 'value' => self::STATUS_VISIBLE],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'          => 'ID',
            'user_id'     => 'Customer',
            'provider_id' => 'Provider',
            'order_id'    => 'Order',
            'rating'      => 'Rating (1–5)',
            'title'       => 'Review Title',
            'comment'     => 'Your Review',
            'is_verified' => 'Verified Purchase',
            'status'      => 'Status',
            'created_at'  => 'Reviewed On',
        ];
    }

    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);
        // Recalculate provider rating after every review save
        if ($this->provider) {
            $this->provider->recalculateRating();
        }
    }

    /** Star display helper: returns array of filled/empty booleans */
    public function stars(): array
    {
        return array_map(fn($i) => $i <= $this->rating, range(1, 5));
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getProvider()
    {
        return $this->hasOne(Provider::class, ['id' => 'provider_id']);
    }

    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }
}
