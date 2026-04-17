<?php
/** @var yii\web\View $this */
/** @var common\models\Provider $vendor */
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Edit Vendor';
$isNew = $vendor->isNewRecord;
?>
<style>
.form-section{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:20px;margin-bottom:12px;}
.form-section-title{font-size:12.5px;font-weight:700;color:var(--text1);margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid var(--border);}
.form-grid2{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.form-grid3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;}
.fg{display:flex;flex-direction:column;gap:4px;}
.fg label{font-size:10.5px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.08em;}
.fg input,.fg select,.fg textarea{width:100%;background:var(--bg);border:1px solid var(--border);border-radius:var(--r-md);padding:8px 11px;font-size:13px;color:var(--text1);font-family:var(--font);outline:none;transition:border-color .13s;resize:vertical;}
.fg input:focus,.fg select:focus,.fg textarea:focus{border-color:var(--acc);}
.fg textarea{min-height:80px;}
.fg .error{font-size:11px;color:var(--rose);margin-top:2px;}
@media(max-width:600px){.form-grid2,.form-grid3{grid-template-columns:1fr;}}
</style>

<div class="hl-pg-head">
    <div style="display:flex;align-items:center;gap:10px;">
        <a href="<?= Url::to(['/admin/vendor-view', 'id' => $vendor->id]) ?>" class="hl-btn-g" style="padding:6px 10px;font-size:11px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;"><polyline points="15 18 9 12 15 6"/></svg> Back
        </a>
        <h1 class="hl-pg-title">Edit Vendor</h1>
    </div>
</div>

<?php $form = \yii\widgets\ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<div class="form-section">
    <div class="form-section-title">Business Information</div>
    <div class="form-grid2">
        <div class="fg">
            <label>Business Name</label>
            <?= $form->field($vendor, 'business_name', ['template' => '{input}{error}'])->textInput(['placeholder' => 'Business name']) ?>
        </div>
        <div class="fg">
            <label>City</label>
            <?= $form->field($vendor, 'city', ['template' => '{input}{error}'])->textInput(['placeholder' => 'e.g. Nairobi']) ?>
        </div>
        <div class="fg">
            <label>Business Phone</label>
            <?= $form->field($vendor, 'phone', ['template' => '{input}{error}'])->textInput(['placeholder' => '+254...']) ?>
        </div>
        <div class="fg">
            <label>Business Email</label>
            <?= $form->field($vendor, 'email', ['template' => '{input}{error}'])->input('email', ['placeholder' => 'business@email.com']) ?>
        </div>
    </div>
    <div class="fg" style="margin-top:12px;">
        <label>Address</label>
        <?= $form->field($vendor, 'address', ['template' => '{input}{error}'])->textInput(['placeholder' => 'Street address']) ?>
    </div>
    <div class="fg" style="margin-top:12px;">
        <label>Website</label>
        <?= $form->field($vendor, 'website', ['template' => '{input}{error}'])->input('url', ['placeholder' => 'https://...']) ?>
    </div>
    <div class="fg" style="margin-top:12px;">
        <label>About / Description</label>
        <?= $form->field($vendor, 'description', ['template' => '{input}{error}'])->textarea(['rows' => 4, 'placeholder' => 'Describe this business…']) ?>
    </div>
</div>

<div class="form-section">
    <div class="form-section-title">Account Status</div>
    <div class="form-grid2">
        <div class="fg">
            <label>Status</label>
            <?= $form->field($vendor, 'status', ['template' => '{input}{error}'])->dropDownList([
                'pending'   => 'Pending',
                'active'    => 'Active',
                'suspended' => 'Suspended',
                'rejected'  => 'Rejected',
            ]) ?>
        </div>
        <div class="fg">
            <label>Verified</label>
            <?= $form->field($vendor, 'is_verified', ['template' => '{input}{error}'])->dropDownList([
                '0' => 'No',
                '1' => 'Yes — Verified',
            ]) ?>
        </div>
        <div class="fg">
            <label>Latitude (optional)</label>
            <?= $form->field($vendor, 'lat', ['template' => '{input}{error}'])->input('number', ['step' => 'any', 'placeholder' => '-1.2921']) ?>
        </div>
        <div class="fg">
            <label>Longitude (optional)</label>
            <?= $form->field($vendor, 'lng', ['template' => '{input}{error}'])->input('number', ['step' => 'any', 'placeholder' => '36.8219']) ?>
        </div>
    </div>
</div>

<div style="display:flex;justify-content:flex-end;gap:8px;mt-4">
    <a href="<?= Url::to(['/admin/vendor-view', 'id' => $vendor->id]) ?>" class="hl-btn-g">Cancel</a>
    <button type="submit" class="hl-btn-p">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;"><polyline points="20 6 9 17 4 12"/></svg>
        Save Changes
    </button>
</div>

<?php \yii\widgets\ActiveForm::end(); ?>
