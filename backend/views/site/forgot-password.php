<?php
/** @var yii\web\View $this */
/** @var string $email */
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="hl-auth-wrap">
  <div class="hl-auth-card">
    <div class="hl-auth-header">
      <img src="/img/auth-logo.png" alt="HudumaLynk" style="height: 56px; width: auto; display: block; margin: 0 auto 1.25rem auto;" onerror="this.style.display='none'">
      <h2>Reset Your Password</h2>
      <p>Enter your email address and we'll send you a link to reset your password.</p>
    </div>
    <div class="hl-auth-body">

      <form method="post" novalidate>
        <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">

        <div class="hl-form-group">
          <label class="hl-label">Email Address</label>
          <div class="hl-input-icon">
            <i class="bi bi-envelope"></i>
            <input type="email" name="email" class="hl-input" id="fp-email" placeholder="you@email.com" value="<?= Html::encode($email) ?>" required autofocus>
          </div>
        </div>

        <button type="submit" class="btn-hl-primary w-100" style="justify-content:center;padding:0.8rem;font-size:1rem;">
          <i class="bi bi-envelope-arrow-up"></i> Send Reset Link
        </button>
      </form>
    </div>
    <div class="hl-auth-footer">
      Remember your password? <a href="<?= Url::to(['/login']) ?>">Sign in</a>
      &nbsp;|&nbsp;
      <a href="<?= Url::to(['/register']) ?>">Create account</a>
    </div>
  </div>
</div>