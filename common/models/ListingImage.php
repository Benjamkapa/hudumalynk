<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * ListingImage — images attached to a listing
 *
 * @property int    $id
 * @property int    $listing_id
 * @property string $image_path
 * @property bool   $is_primary
 * @property int    $sort_order
 * @property string $created_at
 */
class ListingImage extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%listing_images}}';
    }

    public function rules(): array
    {
        return [
            [['listing_id', 'image_path'], 'required'],
            ['listing_id', 'integer'],
            ['image_path', 'string', 'max' => 500],
            ['is_primary', 'boolean'],
            ['sort_order', 'integer', 'min' => 0],
        ];
    }

    /** Full web-accessible URL for the image */
    public function getUrl(): string
    {
        $baseUrl = rtrim(\Yii::$app->params['frontendUrl'] ?? '', '/');
        return $baseUrl . '/uploads/' . ltrim($this->image_path, '/');
    }

    public function getListing()
    {
        return $this->hasOne(Listing::class, ['id' => 'listing_id']);
    }
}
