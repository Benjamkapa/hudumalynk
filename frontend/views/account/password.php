<?php
/** @var common\models\User $user */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<div class="row g-4">
  <!-- Account Sidebar -->
  <div class="col-lg-3">
    <div class="hl-card" style="padding:1.5rem; position:sticky; top:calc(var(--navbar-h) + 1.5rem);">
      <div class="d-flex align-items-center gap-3 mb-4 pb-3" style="border-bottom:1px solid var(--border);">
        <div style="width:48px;height:48px;border-radius:50%;background:var(--hl-gradient);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:700;">
          <?= Html::encode($user->getInitials()) ?>
        </div>
        <div>
          <div style="font-weight:700;line-height:1.2;"><?= Html::encode($user->getFullName()) ?></div>
          <div style="font-size:0.75rem;color:var(--text-muted);"><?= ucfirst($user->role) ?> Member</div>
        </div>
      </div>

      <nav class="d-flex flex-column gap-1">
        <a href="<?= Url::to(['/account']) ?>" class="btn-hl-ghost justify-content-start w-100" style="padding:0.75rem 1rem;">
          <i class="bi bi-person me-2"></i> Profile
        </a>
        <a href="<?= Url::to(['/orders']) ?>" class="btn-hl-ghost justify-content-start w-100" style="padding:0.75rem 1rem;">
          <i class="bi bi-bag me-2"></i> My Orders
        </a>
        <a href="<?= Url::to(['/account/notifications']) ?>" class="btn-hl-ghost justify-content-start w-100" style="padding:0.75rem 1rem;">
          <i class="bi bi-bell me-2"></i> Notifications
        </a>
        <a href="<?= Url::to(['/account/password']) ?>" class="btn-hl-primary justify-content-start w-100" style="padding:0.75rem 1rem;">
          <i class="bi bi-shield-lock me-2"></i> Security
        </a>
        <hr>
        <?= Html::a('<i class="bi bi-box-arrow-right me-2"></i>Sign Out', Url::to(['/logout']), [
            'class' => 'btn-hl-ghost text-danger justify-content-start w-100',
            'data-method' => 'post',
            'style' => 'padding:0.75rem 1rem;'
        ]) ?>
      </nav>
    </div>
  </div>

  <!-- Password Form -->
  <div class="col-lg-7">
    <div class="mb-4">
      <h3 style="font-weight:800;letter-spacing:-0.5px;margin:0;">Change Password</h3>
      <p style="color:var(--text-muted);margin:0;">Keep your account secure by using a strong password</p>
    </div>

    <div class="hl-card" style="padding:2.5rem;">
      <?php $form = ActiveForm::begin(); ?>

      <div class="row g-3">
        <div class="col-12">
          <?= $form->field($user, 'password', [
              'options' => ['class' => 'mb-3'],
              'labelOptions' => ['class' => 'hl-label']
          ])->passwordInput(['class' => 'hl-input', 'placeholder' => 'Enter new password'])->label('New Password') ?>
        </div>

        <div class="col-12">
          <?= $form->field($user, 'password_confirm', [
              'options' => ['class' => 'mb-4'],
              'labelOptions' => ['class' => 'hl-label']
          ])->passwordInput(['class' => 'hl-input', 'placeholder' => 'Confirm new password'])->label('Confirm New Password') ?>
        </div>

        <div class="col-12 pt-2" style="border-top:1px solid var(--border);">
          <button type="submit" class="btn-hl-primary px-4">
            <i class="bi bi-save me-2"></i> Update Password
          </button>
        </div>
      </div>

      <?php ActiveForm::end(); ?>
    </div>
  </div>
</div>
