<?php
/** @var common\models\User $user */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>
<div style="padding-top:var(--navbar-h);background:var(--surface-2);min-height:100vh;">
<div class="hl-section" style="max-width:800px;padding-top:2.5rem;">

  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <h3 style="margin:0;">My Account</h3>
      <p style="color:var(--text-muted);font-size:0.875rem;margin:0;">Manage your personal details</p>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-md-4">
      <div class="hl-card text-center" style="padding:2rem;">
        <div style="width:90px;height:90px;border-radius:50%;background:linear-gradient(135deg,var(--hl-blue),var(--hl-green));color:#fff;display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:700;margin:0 auto 1rem;">
          <?= Html::encode($user->getInitials()) ?>
        </div>
        <h4 style="margin-bottom:0.25rem;"><?= Html::encode($user->getFullName()) ?></h4>
        <div style="font-size:0.85rem;color:var(--text-muted);margin-bottom:1rem;"><?= Html::encode($user->email) ?></div>
        <span class="badge-status badge-primary">Customer Account</span>
      </div>
    </div>

    <div class="col-md-8">
      <div class="hl-card">
        <div class="hl-card-header"><h4>Personal Details</h4></div>
        <div class="hl-card-body" style="padding:2rem;">
          <?php $form = ActiveForm::begin(); ?>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="hl-label">First Name</label>
              <?= $form->field($user, 'first_name', ['template' => '{input}{error}'])->textInput(['class' => 'hl-input']) ?>
            </div>
            <div class="col-md-6">
              <label class="hl-label">Last Name</label>
              <?= $form->field($user, 'last_name', ['template' => '{input}{error}'])->textInput(['class' => 'hl-input']) ?>
            </div>
            <div class="col-md-6">
              <label class="hl-label">Email Address</label>
              <?= $form->field($user, 'email', ['template' => '{input}{error}'])->textInput(['class' => 'hl-input']) ?>
            </div>
            <div class="col-md-6">
              <label class="hl-label">Phone Number</label>
              <?= $form->field($user, 'phone', ['template' => '{input}{error}'])->textInput(['class' => 'hl-input']) ?>
            </div>
            
            <div class="col-12 mt-4 pt-3" style="border-top:1px solid var(--border);">
              <?= Html::submitButton('<i class="bi bi-save"></i> Save Changes', ['class' => 'btn-hl-primary px-4']) ?>
            </div>
          </div>

          <?php ActiveForm::end(); ?>
        </div>
      </div>
    </div>
  </div>

</div>
</div>
