<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use common\models\Order;
use common\models\Review;

class ReviewController extends Controller
{
    public $layout = 'main';

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [[
                    'allow' => true,
                    'roles' => ['@'],
                    'matchCallback' => fn() => Yii::$app->user->identity->isCustomer(),
                ]],
            ],
        ];
    }

    public function actionCreate(int $order_id)
    {
        $this->view->title = 'Write a Review';
        $order = Order::findOne(['id' => $order_id, 'user_id' => Yii::$app->user->id]);

        if (!$order) {
            throw new NotFoundHttpException('Order not found.');
        }

        if (!$order->canLeaveReview()) {
            throw new ForbiddenHttpException('You can only review completed orders.');
        }

        if ($order->review) {
            Yii::$app->session->setFlash('info', 'You have already reviewed this order.');
            return $this->redirect(['/orders/' . $order->id]);
        }

        $review = new Review();
        $review->user_id      = Yii::$app->user->id;
        $review->provider_id  = $order->provider_id;
        $review->order_id     = $order->id;
        $review->is_verified  = true; // since it's linked to an order
        $review->status       = Review::STATUS_VISIBLE;

        if ($review->load(Yii::$app->request->post()) && $review->validate()) {
            if ($review->save()) {
                Yii::$app->session->setFlash('success', 'Thank you! Your verified review has been submitted.');
                return $this->redirect(['/orders/' . $order->id]);
            }
        }

        return $this->render('create', compact('order', 'review'));
    }
}
