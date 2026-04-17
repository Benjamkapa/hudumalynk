<?php

namespace console\commands;

use Yii;
use yii\console\Controller;
use common\models\Provider;
use yii\db\Exception;

class MapDataController extends Controller
{
    public function actionPopulateCoords()
    {
        $nairobiCoords = [
            ['lat' => -1.286389, 'lng' => 36.817223], // Nairobi City Center
            ['lat' => -1.2921, 'lng' => 36.8219], // CBD
            ['lat' => -1.2921, 'lng' => 36.8436], // Westlands
            ['lat' => -1.3038, 'lng' => 36.7881], // Kilimani
            ['lat' => -1.2615, 'lng' => 36.7988], // Upper Hill
            ['lat' => -1.2833, 'lng' => 36.8168], // Parklands
            ['lat' => -1.3075, 'lng' => 36.8387], // Hurlingham
            ['lat' => -1.3149, 'lng' => 36.7908], // Ngong Road
            ['lat' => -1.2588, 'lng' => 36.9289], // Thika Road
            ['lat' => -1.2950, 'lng' => 36.7818], // Ngara
            ['lat' => -1.2800, 'lng' => 36.8000], // Eastleigh
        ];

        $providers = Provider::find()->where(['lat' => null])->limit(20)->all();

        foreach ($providers as $i => $provider) {
            if (isset($nairobiCoords[$i])) {
                $provider->lat = $nairobiCoords[$i]['lat'];
                $provider->lng = $nairobiCoords[$i]['lng'];
                $provider->save(false);
                echo "Updated {$provider->business_name}: {$provider->lat}, {$provider->lng}\n";
            }
        }

        echo "Populated " . count($providers) . " providers with Nairobi coords.\n";
        echo "Refresh /admin/map to see pins.\n";
    }
}
?>

