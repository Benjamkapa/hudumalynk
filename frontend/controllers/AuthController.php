<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use frontend\models\LoginForm;
use frontend\models\RegisterForm;
use frontend\models\ProviderRegisterForm;
use common\models\User;

class AuthController extends Controller
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
                'only'  => ['logout'],
                'rules' => [['actions' => ['logout'], 'allow' => true, 'roles' => ['@']]],
            ],
            'verbs'  => [
                'class'   => VerbFilter::class,
                'actions' => ['logout' => ['post', 'get']],
            ],
        ];
    }

    // ── Login ─────────────────────────────────────────────────────────────────

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect($this->roleRedirect());
        }

        $model = new LoginForm();
        $model->identifier = (string)Yii::$app->request->get('identifier', $model->identifier);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $user = $model->getUser();
                if ($user && ($user->isAdmin() || $user->isProvider())) {
                    Yii::$app->session->setFlash('info', 'Provider and admin accounts sign in through the business portal.');
                    return $this->redirect(rtrim(Yii::$app->params['backendUrl'], '/') . '/login?identifier=' . urlencode($model->identifier));
                }

                if ($model->login()) {
                    Yii::$app->session->setFlash('success', 'Welcome back, ' . Yii::$app->user->identity->first_name . '!');
                    return $this->redirect($this->roleRedirect());
                }
            }

            $model->password = '';
        }

        $this->view->title = 'Sign In';
        return $this->render('login', ['model' => $model]);
    }

    // ── Logout ────────────────────────────────────────────────────────────────

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect('/');
    }

    // ── Customer Register ─────────────────────────────────────────────────────

    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect('/');
        }

        $model = new RegisterForm();
        if ($model->load(Yii::$app->request->post())) {
            $user = $model->register();
            if ($user) {
                Yii::$app->user->login($user, 3600 * 24 * 30);
                Yii::$app->session->setFlash('success', 'Welcome to HudumaLynk, ' . $user->first_name . '!');
                return $this->redirect('/');
            }
        }

        $this->view->title = 'Create Your Account';
        return $this->render('register', ['model' => $model]);
    }

    // ── Provider Register ─────────────────────────────────────────────────────

    public function actionRegisterProvider()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect('/');
        }

        $model = new ProviderRegisterForm();
        if ($model->load(Yii::$app->request->post())) {
            $model->id_document = \yii\web\UploadedFile::getInstance($model, 'id_document');
            $user = $model->register();
            if ($user) {
                Yii::$app->user->login($user, 3600 * 24 * 30);
                Yii::$app->session->setFlash('success',
                    'Registration successful! Your provider account is pending approval. We\'ll notify you once it\'s reviewed (usually within 24 hours).');
                return $this->redirect('/');
            }
            Yii::$app->session->setFlash('danger', 'Registration failed. Please check the form and try again.');
        }

        $this->view->title = 'Join as a Provider';
        return $this->render('register-provider', ['model' => $model]);
    }

    // ── Forgot Password ───────────────────────────────────────────────────────

    public function actionForgotPassword()
    {
        $this->view->title = 'Reset Password';
        $email = '';
        if (Yii::$app->request->isPost) {
            $email = Yii::$app->request->post('email', '');
            $user  = User::findOne(['email' => $email]);
            if ($user) {
                $user->generatePasswordResetToken();
                $user->save(false);
                $resetLink = Yii::$app->params['frontendUrl'] . '/reset-password/' . $user->password_reset_token;
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
            return $this->redirect('/forgot-password');
        }
        return $this->render('forgot-password', ['email' => $email]);
    }

    // ── Reset Password ────────────────────────────────────────────────────────

    public function actionResetPassword(string $token)
    {
        $user = User::findByPasswordResetToken($token);
        if (!$user) {
            Yii::$app->session->setFlash('danger', 'This reset link is invalid or has expired.');
            return $this->redirect('/forgot-password');
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
                return $this->redirect('/login');
            }
        }
        $this->view->title = 'Set New Password';
        return $this->render('reset-password', ['token' => $token]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function roleRedirect(): string
    {
        $identity = Yii::$app->user->identity;
        if ($identity->isAdmin()) {
            return rtrim(Yii::$app->params['backendUrl'], '/') . '/admin/dashboard';
        }
        if ($identity->isProvider()) {
            return rtrim(Yii::$app->params['backendUrl'], '/') . '/provider/dashboard';
        }
        $returnUrl = Yii::$app->user->returnUrl;
        return ($returnUrl && $returnUrl !== '/') ? $returnUrl : '/';
    }
}
