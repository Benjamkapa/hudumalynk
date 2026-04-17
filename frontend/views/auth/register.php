<?php
/** @var yii\web\View $this */
/** @var frontend\models\RegisterForm $model */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>
<div class="hl-auth-wrap" style="padding-top:calc(var(--navbar-h) + 2rem);">
  <div class="hl-auth-card" style="max-width:520px;">
    <div class="hl-auth-header">
      <img src="/img/auth-logo.png" alt="HudumaLynk" style="height: 56px; width: auto; display: block; margin: 0 auto 1.25rem auto;" onerror="this.style.display='none'">
      <h2>Create Your Account</h2>
      <p>Join thousands of customers on HudumaLynk</p>
    </div>
    <div class="hl-auth-body">
      <?php $form = ActiveForm::begin(['id' => 'register-form', 'options' => ['novalidate' => true]]); ?>

      <div class="row g-3 mb-0">
        <div class="col-6">
          <div class="hl-form-group">
            <label class="hl-label">First Name</label>
            <?= $form->field($model, 'first_name', ['template' => '{input}{error}'])->textInput([
              'class' => 'hl-input',
              'placeholder' => 'John',
              'id' => 'reg-first-name',
            ]) ?>
          </div>
        </div>
        <div class="col-6">
          <div class="hl-form-group">
            <label class="hl-label">Last Name</label>
            <?= $form->field($model, 'last_name', ['template' => '{input}{error}'])->textInput([
              'class' => 'hl-input',
              'placeholder' => 'Doe',
              'id' => 'reg-last-name',
            ]) ?>
          </div>
        </div>
      </div>

      <div class="hl-form-group">
        <label class="hl-label">Email Address</label>
        <div class="hl-input-icon">
          <i class="bi bi-envelope"></i>
          <?= $form->field($model, 'email', ['template' => '{input}{error}'])->input('email', [
            'class' => 'hl-input',
            'placeholder' => 'you@email.com',
            'id' => 'reg-email',
          ]) ?>
        </div>
      </div>

      <div class="hl-form-group">
        <label class="hl-label">Phone Number</label>
        <div class="hl-input-icon">
          <i class="bi bi-phone"></i>
          <?= $form->field($model, 'phone', ['template' => '{input}{error}'])->textInput([
            'class' => 'hl-input',
            'placeholder' => '0712 345 678',
            'id' => 'reg-phone',
          ]) ?>
        </div>
      </div>

      <div class="hl-form-group">
        <label class="hl-label">Password <span style="color:var(--text-muted);font-weight:400;">(min. 8 characters)</span></label>
        <div class="hl-input-icon">
          <i class="bi bi-lock"></i>
          <?= $form->field($model, 'password', ['template' => '{input}{error}'])->passwordInput([
            'class' => 'hl-input',
            'placeholder' => 'Create a strong password',
            'id' => 'reg-password',
          ]) ?>
        </div>
      </div>

      <div class="hl-form-group">
        <label class="hl-label">Confirm Password</label>
        <div class="hl-input-icon">
          <i class="bi bi-lock-fill"></i>
          <?= $form->field($model, 'password_confirm', ['template' => '{input}{error}'])->passwordInput([
            'class' => 'hl-input',
            'placeholder' => 'Repeat your password',
            'id' => 'reg-confirm',
          ]) ?>
        </div>
      </div>

      <p style="font-size:0.78rem;color:var(--text-muted);margin-bottom:1.25rem;">
        By creating an account you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
      </p>

      <?= Html::submitButton('<i class="bi bi-person-check"></i> Create Account', [
        'class' => 'btn-hl-primary w-100',
        'style' => 'justify-content:center;padding:0.8rem;font-size:1rem;',
        'id' => 'reg-submit',
      ]) ?>

      <?php ActiveForm::end(); ?>
    </div>
    <div class="hl-auth-footer">
      Already have an account? <a href="<?= Url::to(['/login']) ?>">Sign in</a>
      &nbsp;|&nbsp;
      <a href="<?= Yii::$app->params['backendUrl'] . '/login' ?>">Provider or admin portal</a>
    </div>
  </div>
</div>
