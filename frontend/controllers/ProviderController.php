<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\models\Provider;

class ProviderController extends Controller
{
    public function actionView($id)
    {
        $provider = Provider::find()
            ->with(['listings', 'user'])
            ->where(['id' => $id])
            ->one();

        if (!$provider) {
            throw new NotFoundHttpException('Provider not found.');
        }

        $this->view->title = $provider->business_name;
        return $this->render('view', ['provider' => $provider, 'listings' => $provider->activeListings]);
    }

    public function actionApi($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $provider = Provider::find()
            ->select(['id', 'business_name', 'description', 'city', 'address', 'phone', 'logo', 'is_verified', 'rating', 'total_reviews'])
            ->where(['id' => $id])
            ->asArray()
            ->one();

        if (!$provider) {
            return ['success' => false, 'message' => 'Provider not found'];
        }

        $provider['slug'] = Yii::$app->slug->slugify($provider['business_name']);
        return ['success' => true, 'provider' => $provider];
    }
}
?>

