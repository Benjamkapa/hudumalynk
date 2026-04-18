<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Inflector;

/**
 * Listing — unified model for both products and services
 *
 * @property int    $id
 * @property int    $provider_id
 * @property int    $category_id
 * @property string $type
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property float  $price
 * @property int    $stock_quantity
 * @property string $availability
 * @property string $location
 * @property string $primary_image
 * @property bool   $is_featured
 * @property int    $views
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class Listing extends ActiveRecord
{
    /** @var \yii\web\UploadedFile */
    public $imageFile;

    /** @var \yii\web\UploadedFile[] */
    public $imageFiles;

    /** @var string Virtual attribute for primary image path */
    public $primary_image;

    const TYPE_PRODUCT = 'product';
    const TYPE_SERVICE = 'service';

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DRAFT    = 'draft';

    public static function tableName(): string
    {
        return '{{%listings}}';
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
            [['provider_id', 'category_id', 'type', 'name', 'price'], 'required'],
            ['name',        'string', 'max' => 255],
            ['description', 'string'],
            ['price',       'number', 'min' => 0],
            ['type',   'in', 'range' => [self::TYPE_PRODUCT, self::TYPE_SERVICE]],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DRAFT]],
            ['status', 'default', 'value' => self::STATUS_DRAFT],

            // Product-specific
            ['stock_quantity', 'integer', 'min' => 0,
                'when' => function () { return $this->type === self::TYPE_PRODUCT; }],

            // Service-specific
            ['availability', 'string', 'max' => 255,
                'when' => function () { return $this->type === self::TYPE_SERVICE; }],

            ['location',      'string', 'max' => 255],
            ['primary_image', 'string', 'max' => 255],
            ['imageFile',     'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, webp, GIF, gif, JPG, JPEG, WEBP', 'maxSize' => 5*1024*1024],
            [['imageFiles'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, webp, GIF, gif, JPG, JPEG, WEBP', 'maxFiles' => 5],
            ['is_featured',   'boolean'],
            ['views',         'integer', 'min' => 0],
            ['provider_id',   'integer'],
            ['category_id',   'integer'],
            ['category_id',   'exist', 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'             => 'ID',
            'provider_id'    => 'Provider',
            'category_id'    => 'Category',
            'type'           => 'Type',
            'name'           => 'Listing Name',
            'slug'           => 'URL Slug',
            'description'    => 'Description',
            'price'          => 'Price (KES)',
            'stock_quantity' => 'Stock',
            'availability'   => 'Availability',
            'location'       => 'Service Location / Delivery Area',
            'is_featured'    => 'Featured',
            'views'          => 'Views',
            'status'         => 'Status',
            'created_at'     => 'Listed On',
        ];
    }

    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            if ($insert || $this->isAttributeChanged('name')) {
                $this->slug = Inflector::slug($this->name);
            }
            return true;
        }
        return false;
    }

    // ── Type helpers ──────────────────────────────────────────────────────────

    public function isProduct(): bool { return $this->type === self::TYPE_PRODUCT; }
    public function isService(): bool { return $this->type === self::TYPE_SERVICE; }
    public function isActive(): bool  { return $this->status === self::STATUS_ACTIVE; }

    // ── Price display helpers ─────────────────────────────────────────────────

    public function priceKes(): string
    {
        return 'KES ' . number_format($this->price, 2);
    }

    public function priceUsd(): string
    {
        $rate = (float)(Yii::$app->params['kesToUsdRate'] ?? 0.0077);
        return 'USD ' . number_format($this->price * $rate, 2);
    }

    // ── Primary image ─────────────────────────────────────────────────────────

    public function getPrimaryImageUrl(): string
    {
        $baseUrl = rtrim(Yii::$app->params['frontendUrl'] ?? '', '/');

        if ($this->primary_image) {
            return $baseUrl . '/uploads/' . ltrim($this->primary_image, '/');
        }

        $img = ListingImage::find()
            ->where(['listing_id' => $this->id, 'is_primary' => true])
            ->one();
        if (!$img) {
            $img = ListingImage::find()->where(['listing_id' => $this->id])->orderBy(['sort_order' => SORT_ASC])->one();
        }
        return $img ? $img->getUrl() : $baseUrl . '/img/placeholder.png';
    }

    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->primary_image) {
            // Ensure this image is in ListingImage table and marked as primary
            $img = ListingImage::findOne(['listing_id' => $this->id, 'image_path' => $this->primary_image]) 
                 ?? new ListingImage(['listing_id' => $this->id, 'image_path' => $this->primary_image]);
            
            $img->is_primary = true;
            $img->save();

            // Unmark other primary images for this listing
            ListingImage::updateAll(['is_primary' => false], [
                'and',
                ['listing_id' => $this->id],
                ['!=', 'id', $img->id]
            ]);
        }
    }

    // ── Increment views ───────────────────────────────────────────────────────

    public function incrementViews(): void
    {
        static::updateAllCounters(['views' => 1], ['id' => $this->id]);
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function getProvider()
    {
        return $this->hasOne(Provider::class, ['id' => 'provider_id']);
    }

    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    public function getImages()
    {
        return $this->hasMany(ListingImage::class, ['listing_id' => 'id'])->orderBy(['sort_order' => SORT_ASC]);
    }

    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::class, ['listing_id' => 'id']);
    }
}
