<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\models\Notification;

class NotificationController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'mark-read' => ['POST'],
                    'mark-all-read' => ['POST'],
                ],
            ],
        ];
    }

    /** AJAX: List recent unread notifications */
    public function actionList(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $notifications = Notification::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(10)
            ->all();

        $data = [];
        foreach ($notifications as $n) {
            $data[] = [
                'id' => $n->id,
                'title' => $n->title,
                'message' => mb_strimwidth($n->message, 0, 80, '...'),
                'is_read' => (bool)$n->is_read,
                'time' => Yii::$app->formatter->asRelativeTime($n->created_at),
                'type' => $n->type,
            ];
        }

        return [
            'success' => true,
            'notifications' => $data,
            'unreadCount' => Yii::$app->user->identity->getUnreadNotificationsCount(),
        ];
    }

    /** AJAX: Mark as read */
    public function actionMarkRead(int $id): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $notification = Notification::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
        if (!$notification) {
            throw new NotFoundHttpException('Notification not found.');
        }

        $notification->markRead();
        
        return [
            'success' => true,
            'unreadCount' => Yii::$app->user->identity->getUnreadNotificationsCount(),
        ];
    }

    /** AJAX: Mark all as read */
    public function actionMarkAllRead(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        Notification::updateAll(['is_read' => true], ['user_id' => Yii::$app->user->id, 'is_read' => false]);
        
        return [
            'success' => true,
            'unreadCount' => 0,
        ];
    }
}
