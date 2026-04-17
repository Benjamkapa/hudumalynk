<?php
/** @var yii\web\View $this */
/** @var frontend\models\LoginForm $model */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Business Portal Login';
?>

<style>
    :root {
        --login-bg: #0F172A;
        --card-bg: #1E293B;
        --primary: #06B6D4;
        --primary-hover: #0891B2;
        --secondary: #94A3B8;
        --input-border: rgba(255, 255, 255, 0.1);
        --input-bg: #0B1120;
        --text: #F8FAFC;
    }

    body {
        margin: 0;
        padding: 0;
        min-height: 100vh;
        background: linear-gradient(135deg, #0B1120 0%, #0F172A 100%);
        font-family: 'Inter', 'Source Sans 3', sans-serif;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text);
    }

    .auth-container {
        width: 100%;
        max-width: 420px;
        padding: 1rem;
        position: relative;
        z-index: 1;
    }

    /* Ambient Glow Behind Card */
    .auth-container::before {
        content: '';
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        width: 120%; height: 120%;
        background: radial-gradient(circle, rgba(6, 182, 212, 0.15) 0%, transparent 60%);
        z-index: -1;
        pointer-events: none;
    }

    .auth-card {
        background: var(--card-bg);
        border: 1px solid rgba(255, 255, 255, 0.08);
        padding: 3rem 2.5rem;
        border-radius: 20px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
        text-align: center;
    }

    .auth-logo {
        font-size: 2rem;
        font-weight: 800;
        color: var(--primary);
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .auth-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 0.5rem;
        letter-spacing: -0.02em;
    }

    .auth-subtitle {
        color: var(--secondary);
        font-size: 0.875rem;
        margin-bottom: 2.5rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
        text-align: left;
    }

    .form-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--secondary);
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control-hl {
        width: 100%;
        padding: 0.875rem 1.25rem;
        background: var(--input-bg);
        border: 1px solid var(--input-border);
        border-radius: 12px;
        font-size: 0.95rem;
        color: var(--text);
        transition: all 0.2s;
    }

    .form-control-hl:focus {
        background: rgba(0, 0, 0, 0.2);
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.15);
    }

    .btn-hl-login {
        background: linear-gradient(90deg, #06B6D4 0%, #3B82F6 100%);
        color: white;
        width: 100%;
        padding: 1rem;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        margin-top: 1rem;
        box-shadow: 0 8px 25px -4px rgba(6, 182, 212, 0.4);
    }

    .btn-hl-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px -4px rgba(6, 182, 212, 0.5);
    }

    .btn-hl-login:active {
        transform: translateY(0);
    }

    .footer-links {
        margin-top: 2rem;
        font-size: 0.875rem;
        color: var(--secondary);
    }

    .footer-links a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
    }

    .footer-links a:hover {
        color: #60A5FA;
    }

    .help-block-error {
        font-size: 0.75rem;
        color: #ef4444;
        margin-top: 0.4rem;
        font-weight: 500;
        padding-left: 0.5rem;
    }
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-logo">
            <img src="/img/auth-logo.png" alt="HudumaLynk" style="height: 56px; width: auto; max-width: 100%; display: block; margin: 0 auto;"
                onerror="this.outerHTML='<i class=\" bi bi-shield-lock-fill\"></i>
        </div>

        <h1 class="auth-title">Provider &amp; Admin Portal</h1>
        <p class="auth-subtitle">Sign in to manage listings, orders, and operations.</p>

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => true]); ?>

        <div class="form-group">
            <label class="form-label">Identifier</label>
            <?= $form->field($model, 'identifier', ['template' => '{input}{error}'])
                ->textInput(['class' => 'form-control-hl', 'placeholder' => 'Email or Phone', 'autofocus' => true]) ?>
        </div>

        <div class="form-group">
            <div class="d-flex justify-content-between align-items-center">
                <label class="form-label">Password</label>
            </div>
            <?= $form->field($model, 'password', ['template' => '{input}{error}'])
                ->passwordInput(['class' => 'form-control-hl', 'placeholder' => '••••••••']) ?>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4"
            style="font-size: 0.85rem; color: var(--secondary);">
            <label class="d-flex align-items-center gap-2 cursor-pointer">
                <?= Html::activeCheckbox($model, 'rememberMe', ['label' => false]) ?> Remember me
            </label>
            <a href="<?= Yii::$app->params['frontendUrl'] . '/forgot-password' ?>"
                style="color: var(--primary); font-weight: 600; text-decoration: none;">Forgot password?</a>
        </div>

        <button type="submit" class="btn-hl-login">Sign In Securely</button>

        <?php ActiveForm::end(); ?>

        <div class="footer-links">
            Need a customer account instead? <br>
            <a href="<?= Yii::$app->params['frontendUrl'] . '/login' ?>">Go to customer sign in</a>
        </div>
    </div>
</div>