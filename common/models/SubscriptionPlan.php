<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * SubscriptionPlan — plan definition (Basic / Professional / Premium)
 *
 * @property int    $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property float  $price_kes
 * @property int    $duration_days
 * @property int    $max_products
 * @property int    $max_services
 * @property int    $featured_slots
 * @property bool   $is_popular
 * @property string $status
 * @property string $created_at
 */
class SubscriptionPlan extends ActiveRecord
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function tableName(): string
    {
        return '{{%subscription_plans}}';
    }

    public function rules(): array
    {
        return [
            [['name', 'price_kes', 'duration_days'], 'required'],
            ['name',           'string', 'max' => 100],
            ['slug',           'string', 'max' => 100],
            ['description',    'string'],
            ['price_kes',      'number', 'min' => 0],
            ['duration_days',  'integer', 'min' => 1],
            ['max_products',   'integer', 'min' => 0],
            ['max_services',   'integer', 'min' => 0],
            ['featured_slots', 'integer', 'min' => 0],
            ['is_popular',     'boolean'],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'             => 'ID',
            'name'           => 'Plan Name',
            'price_kes'      => 'Price (KES/month)',
            'duration_days'  => 'Duration (Days)',
            'max_products'   => 'Max Products',
            'max_services'   => 'Max Services',
            'featured_slots' => 'Featured Slots',
            'is_popular'     => 'Mark as Popular',
            'status'         => 'Status',
        ];
    }

    /** Price formatted in KES */
    public function priceKes(): string
    {
        return 'KES ' . number_format($this->price_kes, 0);
    }

    /** Price formatted in USD */
    public function priceUsd(): string
    {
        $rate = (float)(\Yii::$app->params['kesToUsdRate'] ?? 0.0077);
        return 'USD ' . number_format($this->price_kes * $rate, 2);
    }

    public function isUnlimited(): bool
    {
        return $this->hasUnlimitedProducts() || $this->hasUnlimitedServices();
    }

    public function hasUnlimitedProducts(): bool
    {
        return (int)$this->max_products >= 999;
    }

    public function hasUnlimitedServices(): bool
    {
        return (int)$this->max_services >= 999;
    }

    /** Ordered active plans for display */
    public static function activePlans(): array
    {
        return static::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->orderBy(['price_kes' => SORT_ASC])
            ->all();
    }

    public function getSubscriptions()
    {
        return $this->hasMany(Subscription::class, ['plan_id' => 'id']);
    }
}
