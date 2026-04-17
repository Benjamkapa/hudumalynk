<?php

use yii\db\Migration;

/**
 * Add lat/lng to providers for map locations
 */
class m241014_000001_add_lat_lng_providers extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%providers}}', 'lat', $this->decimal(10, 8)->defaultValue(null)->after('address'));
        $this->addColumn('{{%providers}}', 'lng', $this->decimal(11, 8)->defaultValue(null)->after('lat'));
        
        // Default Nairobi for existing
        $this->update('{{%providers}}', ['lat' => -1.2921, 'lng' => 36.8219], ['lat' => null]);
    }

    public function safeDown()
    {
        $this->dropColumn('{{%providers}}', 'lng');
        $this->dropColumn('{{%providers}}', 'lat');
    }
}

