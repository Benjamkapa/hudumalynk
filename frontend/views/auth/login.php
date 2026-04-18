<?php
/** @var yii\web\View $this */
/** @var frontend\models\LoginForm $model */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>
<div class="hl-auth-wrap" style="padding-top:calc(var(--navbar-h) + 2rem);">
  <div class="hl-auth-card">
    <div class="hl-auth-header">
      <img src="/img/auth-logo.png" alt="HudumaLynk"
        style="height: 56px; width: auto; display: block; margin: 0 auto 1.25rem auto;"
        onerror="this.style.display='none'">
      <h2>Customer Sign In</h2>
      <p>Access your orders, saved listings, and account details.</p>
    </div>
    <div class="hl-auth-body">

      <?php $form = ActiveForm::begin(['id' => 'login-form', 'options' => ['novalidate' => true]]); ?>

      <div class="hl-form-group">
        <label class="hl-label">Email or Phone Number</label>
        <div class="hl-input-icon">
          <i class="bi bi-person"></i>
          <?= $form->field($model, 'identifier', ['template' => '{input}{error}'])->textInput([
            'class' => 'hl-input',
            'id' => 'login-identifier',
            'placeholder' => 'e.g. you@email.com or 0712345678',
            'autofocus' => true,
          ]) ?>
        </div>
      </div>

      <div class="hl-form-group">
        <div class="d-flex justify-content-between align-items-center mb-1">
          <label class="hl-label mb-0">Password</label>
          <a href="<?= Url::to(['/forgot-password']) ?>" style="font-size:0.82rem;color:var(--hl-blue);">Forgot
            password?</a>
        </div>
        <div class="hl-input-icon">
          <i class="bi bi-lock"></i>
          <?= $form->field($model, 'password', ['template' => '{input}{error}'])->passwordInput([
            'class' => 'hl-input',
            'id' => 'login-password',
            'placeholder' => 'Enter your password',
          ]) ?>
        </div>
      </div>

      <div class="d-flex align-items-center gap-2 mb-4">
        <?= $form->field($model, 'rememberMe', ['template' => '{input}{label}'])->checkbox([
          'id' => 'login-remember',
          'style' => 'width:16px;height:16px;accent-color:var(--hl-blue);cursor:pointer;',
        ])->label(' ', ['style' => 'font-size:0.875rem;cursor:pointer;margin-bottom:0;']) ?>
      </div>

      <?= Html::submitButton('<i class="bi bi-box-arrow-in-right"></i> Sign In', [
        'class' => 'btn-hl-primary w-100',
        'style' => 'justify-content:center;padding:0.8rem;font-size:1rem;',
        'id' => 'login-submit',
      ]) ?>

      <?php ActiveForm::end(); ?>
    </div>
    <div class="hl-auth-footer">
      Don't have an account?&nbsp;
      <a href="<?= Url::to(['/register']) ?>">Create one free</a>
      &nbsp;|&nbsp;
      <a href="<?= Yii::$app->params['backendUrl'] . '/login' ?>">Provider portal</a>
    </div>
  </div>
</div>