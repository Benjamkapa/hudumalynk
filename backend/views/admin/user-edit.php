<?php
/** @var yii\web\View $this */
/** @var common\models\User $user */
use yii\helpers\Html;
use yii\helpers\Url;
$isNew = $user->isNewRecord;
$this->title = $isNew ? 'Add User' : 'Edit User';
?>
<style>
.form-section{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:20px;margin-bottom:12px;}
.form-section-title{font-size:12.5px;font-weight:700;color:var(--text1);margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid var(--border);}
.form-grid2{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.fg{display:flex;flex-direction:column;gap:4px;}
.fg label{font-size:10.5px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.08em;}
.fg input,.fg select{width:100%;background:var(--bg);border:1px solid var(--border);border-radius:var(--r-md);padding:8px 11px;font-size:13px;color:var(--text1);font-family:var(--font);outline:none;transition:border-color .13s;}
.fg input:focus,.fg select:focus{border-color:var(--acc);}
.fg .hint{font-size:10.5px;color:var(--text3);margin-top:3px;}
@media(max-width:600px){.form-grid2{grid-template-columns:1fr;}}
</style>

<div class="hl-pg-head">
    <div style="display:flex;align-items:center;gap:10px;">
        <a href="<?= Url::to(['/admin/users']) ?>" class="hl-btn-g" style="padding:6px 10px;font-size:11px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;"><polyline points="15 18 9 12 15 6"/></svg> Users
        </a>
        <h1 class="hl-pg-title"><?= $isNew ? 'Add New User' : 'Edit User: ' . Html::encode($user->getFullName()) ?></h1>
    </div>
</div>

<?php $form = \yii\widgets\ActiveForm::begin(['options' => ['novalidate' => true]]); ?>

<div class="form-section">
    <div class="form-section-title">Personal Information</div>
    <div class="form-grid2">
        <div class="fg">
            <label>First name <span style="color:var(--rose);">*</span></label>
            <?= $form->field($user, 'first_name', ['template' => '{input}{error}'])->textInput(['placeholder' => 'First name']) ?>
        </div>
        <div class="fg">
            <label>Last name <span style="color:var(--rose);">*</span></label>
            <?= $form->field($user, 'last_name', ['template' => '{input}{error}'])->textInput(['placeholder' => 'Last name']) ?>
        </div>
        <div class="fg">
            <label>Email address <span style="color:var(--rose);">*</span></label>
            <?= $form->field($user, 'email', ['template' => '{input}{error}'])->input('email', ['placeholder' => 'user@example.com']) ?>
        </div>
        <div class="fg">
            <label>Phone number</label>
            <?= $form->field($user, 'phone', ['template' => '{input}{error}'])->textInput(['placeholder' => '+254...']) ?>
        </div>
    </div>
</div>

<div class="form-section">
    <div class="form-section-title">Account Settings</div>
    <div class="form-grid2">
        <div class="fg">
            <label>Role <span style="color:var(--rose);">*</span></label>
            <?= $form->field($user, 'role', ['template' => '{input}{error}'])->dropDownList([
                'customer' => 'Customer',
                'provider' => 'Provider / Vendor',
                'admin'    => 'Admin',
            ]) ?>
        </div>
        <div class="fg">
            <label>Status</label>
            <?= $form->field($user, 'status', ['template' => '{input}{error}'])->dropDownList([
                'active'    => 'Active',
                'suspended' => 'Suspended',
            ]) ?>
        </div>
        <div class="fg">
            <label><?= $isNew ? 'Password' : 'New password' ?> <?= $isNew ? '<span style="color:var(--rose);">*</span>' : '' ?></label>
            <?= $form->field($user, 'password', ['template' => '{input}{error}'])->input('password', ['placeholder' => $isNew ? 'Set a password' : 'Leave blank to keep current']) ?>
            <?php if (!$isNew): ?><span class="hint">Leave blank to keep existing password</span><?php endif; ?>
        </div>
    </div>
</div>

<div style="display:flex;justify-content:flex-end;gap:8px;">
    <a href="<?= Url::to(['/admin/users']) ?>" class="hl-btn-g">Cancel</a>
    <button type="submit" class="hl-btn-p">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;"><polyline points="20 6 9 17 4 12"/></svg>
        <?= $isNew ? 'Create User' : 'Save Changes' ?>
    </button>
</div>

<?php \yii\widgets\ActiveForm::end(); ?>
