<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use common\models\User;

class AccountController extends Controller
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
                ]],
            ],
        ];
    }

    public function actionIndex()
    {
        $this->view->title = 'My Account';
        $user = Yii::$app->user->identity;

        if ($user->load(Yii::$app->request->post()) && $user->save()) {
            Yii::$app->session->setFlash('success', 'Your profile has been updated successfully.');
            return $this->refresh();
        }

        return $this->render('index', compact('user'));
    }

    public function actionNotifications()
    {
        $this->view->title = 'Notifications';
        $user = Yii::$app->user->identity;
        $notifications = $user->notifications;

        return $this->render('notifications', compact('user', 'notifications'));
    }

    public function actionMarkNotifRead(int $id)
    {
        $notif = \common\models\Notification::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
        if ($notif) {
            $notif->markRead();
        }
        return $this->redirect(['/account/notifications']);
    }

    public function actionPassword()
    {
        $this->view->title = 'Change Password';
        $user = Yii::$app->user->identity;
        $user->scenario = User::SCENARIO_CHANGE_PASSWORD;

        if ($user->load(Yii::$app->request->post())) {
            if ($user->validate()) {
                $user->setPassword($user->password);
                if ($user->save(false)) {
                    Yii::$app->session->setFlash('success', 'Password updated successfully.');
                    return $this->redirect(['/account']);
                }
            }
        }

        return $this->render('password', compact('user'));
    }
}
