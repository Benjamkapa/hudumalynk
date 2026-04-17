<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\models\Listing;
use common\models\Review;

class ListingController extends Controller
{
    public $layout = 'main';

    public function actionView(int $id, string $slug = '')
    {
        $listing = Listing::find()
            ->with(['provider', 'provider.subscriptions', 'category', 'images'])
            ->where(['listings.id' => $id, 'listings.status' => Listing::STATUS_ACTIVE])
            ->one();

        if (!$listing) {
            throw new NotFoundHttpException('This listing is no longer available.');
        }

        // Redirect to canonical slug URL
        if ($slug !== $listing->slug) {
            return $this->redirect(['/listing/' . $id . '/' . $listing->slug], 301);
        }

        $listing->incrementViews();

        $reviews = Review::find()
            ->with(['user'])
            ->where(['provider_id' => $listing->provider_id, 'status' => Review::STATUS_VISIBLE])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(10)
            ->all();

        $prices = Yii::$app->currency->both($listing->price);

        $this->view->title = $listing->name . ' — ' . ($listing->provider->business_name ?? 'HudumaLynk');
        $this->view->params['metaDescription'] = substr(strip_tags($listing->description ?? ''), 0, 160);

        return $this->render('view', compact('listing', 'reviews', 'prices'));
    }
}
