<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\models\Category;
use common\models\Listing;
use common\models\Provider;

class SiteController extends Controller
{
    public $layout = 'main';

    public function actionBackendRedirect()
    {
        $path = trim(Yii::$app->request->pathInfo, '/');

        if ($path === 'dashboard') {
            $identity = Yii::$app->user->identity;
            if ($identity?->isAdmin()) {
                $path = 'admin/dashboard';
            } elseif ($identity?->isProvider()) {
                $path = 'provider/dashboard';
            }
        }

        $backendUrl = rtrim(Yii::$app->params['backendUrl'] ?? '', '/');
        if ($backendUrl === '') {
            throw new \yii\web\ServerErrorHttpException('Backend URL is not configured.');
        }

        $query = Yii::$app->request->queryString;
        $target = $backendUrl . '/' . $path;
        if ($query !== '') {
            $target .= '?' . $query;
        }

        return $this->redirect($target);
    }

    public function actionIndex()
    {
        $this->view->title = null; // Will be set in layout to default title

        $categories = Category::find()
            ->where(['status' => Category::STATUS_ACTIVE, 'parent_id' => null])
            ->orderBy(['sort_order' => SORT_ASC])
            ->limit(12)
            ->all();

        $featuredListings = Listing::find()
            ->with(['provider', 'category', 'images'])
            ->where(['status' => Listing::STATUS_ACTIVE, 'is_featured' => true])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(8)
            ->all();

        $latestListings = Listing::find()
            ->with(['provider', 'category', 'images'])
            ->where(['status' => Listing::STATUS_ACTIVE])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(8)
            ->all();

        // Quick platform stats
        $stats = [
            'providers' => Provider::find()->where(['status' => Provider::STATUS_ACTIVE])->count(),
            'listings'  => Listing::find()->where(['status' => Listing::STATUS_ACTIVE])->count(),
            'categories'=> Category::find()->where(['status' => Category::STATUS_ACTIVE])->count(),
        ];

        return $this->render('index', compact('categories', 'featuredListings', 'latestListings', 'stats'));
    }

    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return $this->render('error', ['exception' => $exception]);
        }
        return $this->redirect('/');
    }

    public function actionAbout()
    {
        $this->view->title = 'About HudumaLynk';
        return $this->render('about');
    }

    public function actionContact()
    {
        $this->view->title = 'Contact Us';
        return $this->render('contact');
    }
    public function actionJoin()
    {
        $this->view->title = 'Join as a Provider';
        return $this->render('onboard-provider');
    }
}
