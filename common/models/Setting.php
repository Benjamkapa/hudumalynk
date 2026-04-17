<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Setting — admin-configurable key/value store
 *
 * @property int    $id
 * @property string $key
 * @property string $value
 * @property string $description
 * @property string $updated_at
 */
class Setting extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%settings}}';
    }

    public function rules(): array
    {
        return [
            [['key'], 'required'],
            ['key',         'string', 'max' => 100],
            ['value',       'string'],
            ['description', 'string', 'max' => 500],
        ];
    }

    // ── Static helpers ────────────────────────────────────────────────────────

    /** Get a setting value by key, with optional default */
    public static function get(string $key, $default = null)
    {
        $cacheKey = 'setting_' . $key;
        $cached   = Yii::$app->cache->get($cacheKey);
        if ($cached !== false) {
            return $cached;
        }
        $record = static::findOne(['key' => $key]);
        $value  = $record ? $record->value : $default;
        Yii::$app->cache->set($cacheKey, $value, 600); // cache 10 min
        return $value;
    }

    /** Set a setting value by key */
    public static function set(string $key, $value): bool
    {
        $record = static::findOne(['key' => $key]) ?? new static(['key' => $key]);
        $record->value      = (string) $value;
        $record->updated_at = date('Y-m-d H:i:s');
        Yii::$app->cache->delete('setting_' . $key);
        return $record->save(false);
    }

    /** Bulk-load all settings into params (call during app bootstrap) */
    public static function loadIntoParams(): void
    {
        $rows = static::find()->select(['key', 'value'])->asArray()->all();
        foreach ($rows as $row) {
            Yii::$app->params['setting_' . $row['key']] = $row['value'];
        }
    }
}
