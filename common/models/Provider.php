<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Inflector;

/**
 * Provider — business profile linked to a User
 *
 * @property int    $id
 * @property int    $user_id
 * @property string $business_name
 * @property string $slug
 * @property string $description
 * @property string $city
 * @property string $address
 * @property string $phone
 * @property string $email
 * @property string $website
 * @property string $logo
 * @property string $id_document
 * @property bool   $is_verified
 * @property float  $rating
 * @property int    $total_reviews
 * @property string $status

 * @property string $rejection_reason
 * @property float  $lat
 * @property float  $lng
 * @property string $created_at
 * @property string $updated_at
 */
class Provider extends ActiveRecord
{
    const STATUS_PENDING   = 'pending';
    const STATUS_ACTIVE    = 'active';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_REJECTED  = 'rejected';

    public $logoFile;

    public static function tableName(): string
    {
        return '{{%providers}}';
    }

    public function behaviors(): array
    {
        return [
            [
                'class'              => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => new Expression('NOW()'),
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['user_id', 'business_name', 'city'], 'required'],
            ['business_name', 'string', 'max' => 255],
            ['city',          'string', 'max' => 100],
            ['address',       'string', 'max' => 1000],
            ['phone',         'match', 'pattern' => '/^\+?[0-9]{9,15}$/'],
            ['email',         'email'],
            ['website',       'url'],
            ['status', 'in',  'range' => [
                self::STATUS_PENDING, self::STATUS_ACTIVE,
                self::STATUS_SUSPENDED, self::STATUS_REJECTED,
            ]],
            ['status',    'default', 'value' => self::STATUS_PENDING],
            ['rating',    'number',  'min' => 0, 'max' => 5],
            ['user_id',   'integer'],
            ['is_verified','boolean'],
            [['lat', 'lng'], 'number'],
            [['lat', 'lng'], 'default', 'value' => null],
            [['logoFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, webp, GIF, gif, JPG, JPEG, WEBP'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'               => 'ID',
            'user_id'          => 'User',
            'business_name'    => 'Business Name',
            'slug'             => 'Slug',
            'description'      => 'About Your Business',
            'city'             => 'City',
            'address'          => 'Address',
            'phone'            => 'Business Phone',
            'email'            => 'Business Email',
            'website'          => 'Website',
            'logo'             => 'Business Logo',
            'id_document'      => 'ID / Business Document',
            'lat'              => 'Latitude',
            'lng'              => 'Longitude',
            'is_verified'      => 'Verified',
            'rating'           => 'Rating',
            'total_reviews'    => 'Reviews',
            'status'           => 'Status',
            'rejection_reason' => 'Rejection Reason',
            'created_at'       => 'Registered',
        ];
    }

    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            if ($insert || $this->isAttributeChanged('business_name')) {
                $this->slug = $this->generateUniqueSlug();
            }
            return true;
        }
        return false;
    }

    private function generateUniqueSlug(): string
    {
        $base = Inflector::slug($this->business_name);
        $slug = $base;
        $i    = 1;
        while (static::find()->where(['slug' => $slug])->andWhere(['!=', 'id', $this->id ?? 0])->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    // ── Image helpers ─────────────────────────────────────────────────────────

    public function getLogoUrl(): string
    {
        $baseUrl = rtrim(Yii::$app->params['frontendUrl'] ?? 'http://localhost:8080', '/');
        if ($this->logo) {
            return $baseUrl . '/uploads/' . ltrim($this->logo, '/');
        }
        return '';
    }

    // ── Subscription helpers ──────────────────────────────────────────────────

    public function getActiveSubscription(): ?Subscription
    {
        return Subscription::find()
            ->where(['provider_id' => $this->id, 'status' => Subscription::STATUS_ACTIVE])
            ->andWhere(['>', 'end_date', new Expression('NOW()')])
            ->one();
    }

    public function hasActiveSubscription(): bool
    {
        return $this->getActiveSubscription() !== null;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function canPublishListings(): bool
    {
        return $this->isApproved() && $this->hasActiveSubscription();
    }

    /** Return current plan limits or defaults if no subscription */
    public function getPlanLimits(): array
    {
        $sub = $this->getActiveSubscription();
        if ($sub && $sub->plan) {
            return [
                'max_products'   => $sub->plan->max_products,
                'max_services'   => $sub->plan->max_services,
                'featured_slots' => $sub->plan->featured_slots,
            ];
        }
        return ['max_products' => 0, 'max_services' => 0, 'featured_slots' => 0];
    }

    /** Recalculate rating from reviews and persist */
    public function recalculateRating(): void
    {
        $stats = Review::find()
            ->select(['AVG(rating) AS avg_rating', 'COUNT(*) AS total'])
            ->where(['provider_id' => $this->id, 'status' => Review::STATUS_VISIBLE])
            ->asArray()
            ->one();

        $this->rating       = round((float)($stats['avg_rating'] ?? 0), 2);
        $this->total_reviews = (int)($stats['total'] ?? 0);
        $this->save(false, ['rating', 'total_reviews']);
    }

    public function getTotalEarnings(): float
    {
        // Total completed order value minus total commission amount
        $sales = (float)Order::find()
            ->where(['provider_id' => $this->id, 'status' => Order::STATUS_COMPLETED])
            ->sum('total_amount') ?? 0;

        $commissions = (float)Commission::find()
            ->where(['provider_id' => $this->id])
            ->sum('amount') ?? 0;

        return $sales - $commissions;
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getSubscriptions()
    {
        return $this->hasMany(Subscription::class, ['provider_id' => 'id'])->orderBy(['created_at' => SORT_DESC]);
    }

    public function getListings()
    {
        return $this->hasMany(Listing::class, ['provider_id' => 'id']);
    }

    public function getActiveListings()
    {
        return $this->hasMany(Listing::class, ['provider_id' => 'id'])
            ->andWhere(['status' => Listing::STATUS_ACTIVE]);
    }

    public function getOrders()
    {
        return $this->hasMany(Order::class, ['provider_id' => 'id'])->orderBy(['created_at' => SORT_DESC]);
    }

    public function getReviews()
    {
        return $this->hasMany(Review::class, ['provider_id' => 'id'])->orderBy(['created_at' => SORT_DESC]);
    }

    public function getCommissions()
    {
        return $this->hasMany(Commission::class, ['provider_id' => 'id']);
    }
}
