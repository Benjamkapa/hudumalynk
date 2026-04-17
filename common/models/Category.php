<?php

namespace common\models;

use yii\db\ActiveRecord;
use yii\helpers\Inflector;

/**
 * Category model — hierarchical listing categories
 *
 * @property int    $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $icon
 * @property string $image
 * @property int    $parent_id
 * @property int    $sort_order
 * @property string $type
 * @property string $status
 * @property string $created_at
 */
class Category extends ActiveRecord
{
    const TYPE_SERVICE = 'service';
    const TYPE_PRODUCT = 'product';
    const TYPE_BOTH    = 'both';

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function tableName(): string
    {
        return '{{%categories}}';
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            ['name',       'string', 'max' => 100],
            ['icon',       'string', 'max' => 100],
            ['image',      'string', 'max' => 255],
            ['description','string'],
            ['sort_order', 'integer', 'min' => 0],
            ['parent_id',  'integer'],
            ['parent_id',  'exist', 'targetClass' => self::class, 'targetAttribute' => 'id',
                'when' => function () { return $this->parent_id !== null; }],
            ['type',   'in', 'range' => [self::TYPE_SERVICE, self::TYPE_PRODUCT, self::TYPE_BOTH]],
            ['type',   'default', 'value' => self::TYPE_BOTH],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'          => 'ID',
            'name'        => 'Category Name',
            'slug'        => 'URL Slug',
            'description' => 'Description',
            'icon'        => 'Icon Class',
            'image'       => 'Category Image',
            'parent_id'   => 'Parent Category',
            'type'        => 'Type',
            'sort_order'  => 'Sort Order',
            'status'      => 'Status',
        ];
    }

    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            if ($insert || $this->isAttributeChanged('name')) {
                $this->slug = $this->generateUniqueSlug();
            }
            return true;
        }
        return false;
    }

    private function generateUniqueSlug(): string
    {
        $base = Inflector::slug($this->name);
        $slug = $base;
        $i    = 1;
        while (static::find()->where(['slug' => $slug])->andWhere(['!=', 'id', $this->id ?? 0])->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    // ── Static helpers ────────────────────────────────────────────────────────

    /** Returns [id => name] map for dropdowns */
    public static function dropdown(): array
    {
        return static::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->orderBy(['sort_order' => SORT_ASC, 'name' => SORT_ASC])
            ->select(['name', 'id'])
            ->indexBy('id')
            ->column();
    }

    /** Alias for dropdown() used by controllers */
    public static function flatList(): array
    {
        return static::dropdown();
    }

    /** Active top-level categories */
    public static function topLevel(): array
    {
        return static::find()
            ->where(['status' => self::STATUS_ACTIVE, 'parent_id' => null])
            ->orderBy(['sort_order' => SORT_ASC])
            ->all();
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function getParent()
    {
        return $this->hasOne(self::class, ['id' => 'parent_id']);
    }

    public function getChildren()
    {
        return $this->hasMany(self::class, ['parent_id' => 'id'])
            ->andWhere(['status' => self::STATUS_ACTIVE])
            ->orderBy(['sort_order' => SORT_ASC]);
    }

    public function getListings()
    {
        return $this->hasMany(Listing::class, ['category_id' => 'id']);
    }
}
