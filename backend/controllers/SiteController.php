<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\User;
use frontend\models\LoginForm; // Reuse the frontend login model
use frontend\models\ProviderRegisterForm;

class SiteController extends Controller
{
    public $layout = 'main';

    public function beforeAction($action): bool
    {
        if ($action->id === 'logout') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }


    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'register', 'forgot-password', 'reset-password', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post', 'get'],
                ],
            ],
        ];
    }

    public function actions(): array
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
        ];
    }

    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        if ($user->isAdmin()) {
            return $this->redirect(['/admin/dashboard']);
        } elseif ($user->isProvider()) {
            return $this->redirect(['/provider/dashboard']);
        }
        
        // Smart Redirect: Send customers/guests back to frontend
        return $this->redirect(Yii::$app->params['frontendUrl']);
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['index']);
        }

        // We can use a simple custom login form directly or reuse frontend's
        // Let's use standard Yii login handling
        $model = new \frontend\models\LoginForm();
        $model->identifier = (string)Yii::$app->request->get('identifier', $model->identifier);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $user = $model->getUser();
                if ($user && $user->isCustomer()) {
                    Yii::$app->session->setFlash('warning', 'Customer accounts sign in through the marketplace.');
                    return $this->redirect(rtrim(Yii::$app->params['frontendUrl'], '/') . '/login?identifier=' . urlencode($model->identifier));
                }

                if ($model->login()) {
                    return $this->redirect(['index']);
                }
            }

            $model->password = '';
        }
        
        $this->layout = 'blank'; // ensure no sidebar is shown
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(['login']);
    }

    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['index']);
        }

        $model = new ProviderRegisterForm();
        if ($model->load(Yii::$app->request->post())) {
            $model->id_document = \yii\web\UploadedFile::getInstance($model, 'id_document');
            $user = $model->register();
            if ($user) {
                Yii::$app->user->login($user, 3600 * 24 * 30);
                Yii::$app->session->setFlash('success',
                    'Registration successful! Your provider account is pending approval. We\'ll notify you once it\'s reviewed (usually within 24 hours).');
                return $this->redirect(['index']);
            }
            Yii::$app->session->setFlash('danger', 'Registration failed. Please check the form and try again.');
        }

        $this->layout = 'blank';
        $this->view->title = 'Provider Registration';
        return $this->render('register', ['model' => $model]);
    }

    public function actionForgotPassword()
    {
        $this->view->title = 'Reset Password';
        $email = '';
        if (Yii::$app->request->isPost) {
            $email = Yii::$app->request->post('email', '');
            $user  = User::findOne(['email' => $email]);
            if ($user && ($user->isAdmin() || $user->isProvider())) {
                $user->generatePasswordResetToken();
                $user->save(false);
                $resetLink = Yii::$app->params['backendUrl'] . '/reset-password/' . $user->password_reset_token;
                try {
                    Yii::$app->mailer->compose()
                        ->setTo($user->email)
                        ->setSubject('Reset your HudumaLynk password')
                        ->setHtmlBody("
                            <p>Hi {$user->first_name},</p>
                            <p>Click the link below to reset your password. This link expires in 1 hour.</p>
                            <p><a href=\"{$resetLink}\" style=\"background:#1A8FE3;color:#fff;padding:10px 20px;text-decoration:none;border-radius:6px;\">Reset Password</a></p>
                            <p>If you didn't request this, ignore this email.</p>
                        ")
                        ->send();
                } catch (\Exception $e) {
                    Yii::error('Reset email failed: ' . $e->getMessage());
                }
            }
            // Always show success (prevent email enumeration)
            Yii::$app->session->setFlash('success', 'If that email is registered, you\'ll receive a reset link shortly.');
            return $this->redirect(['forgot-password']);
        }

        $this->layout = 'blank';
        return $this->render('forgot-password', ['email' => $email]);
    }

    public function actionResetPassword(string $token)
    {
        $user = User::findByPasswordResetToken($token);
        if (!$user || !$user->isAdmin() && !$user->isProvider()) {
            Yii::$app->session->setFlash('danger', 'This reset link is invalid or has expired.');
            return $this->redirect(['forgot-password']);
        }

        $newPassword = '';
        if (Yii::$app->request->isPost) {
            $newPassword = Yii::$app->request->post('password', '');
            $confirm     = Yii::$app->request->post('password_confirm', '');
            if (strlen($newPassword) < 8) {
                Yii::$app->session->setFlash('danger', 'Password must be at least 8 characters.');
            } elseif ($newPassword !== $confirm) {
                Yii::$app->session->setFlash('danger', 'Passwords do not match.');
            } else {
                $user->setPassword($newPassword);
                $user->removePasswordResetToken();
                $user->generateAuthKey();
                $user->save(false);
                Yii::$app->session->setFlash('success', 'Password updated! Please sign in with your new password.');
                return $this->redirect(['login']);
            }
        }

        $this->layout = 'blank';
        $this->view->title = 'Set New Password';
        return $this->render('reset-password', ['token' => $token]);
    }
}
