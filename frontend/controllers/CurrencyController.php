<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;

class CurrencyController extends Controller
{
    /**
     * Switch display currency and redirect back
     */
    public function actionSwitch(string $code)
    {
        Yii::$app->currency->setDisplayCurrency($code);
        return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
    }
}
