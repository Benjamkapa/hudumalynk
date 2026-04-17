<?php
/** @var yii\web\View $this */
/** @var frontend\models\ProviderRegisterForm $model */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->registerCssFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
$this->registerJsFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', ['position' => \yii\web\View::POS_END]);
?>
<div class="hl-auth-wrap">
  <div class="hl-auth-card" style="max-width:600px;">
    <div class="hl-auth-header">
      <img src="/img/auth-logo.png" alt="HudumaLynk" style="height: 56px; width: auto; display: block; margin: 0 auto 1.25rem auto;" onerror="this.style.display='none'">
      <h2>Join as a Provider</h2>
      <p>Register your business and start listing services/products.</p>
    </div>
    <div class="hl-auth-body">

      <?php $form = ActiveForm::begin([
        'id'      => 'provider-register-form',
        'options' => ['novalidate' => true, 'enctype' => 'multipart/form-data'],
      ]); ?>

      <div class="row g-3 mb-0">
        <div class="col-md-6">
          <div class="hl-form-group">
            <label class="hl-label">First Name *</label>
            <?= $form->field($model, 'first_name', ['template' => '{input}{error}'])->textInput([
              'class' => 'hl-input', 'placeholder' => 'Jane', 'id' => 'pr-first',
            ]) ?>
          </div>
        </div>
        <div class="col-md-6">
          <div class="hl-form-group">
            <label class="hl-label">Last Name *</label>
            <?= $form->field($model, 'last_name', ['template' => '{input}{error}'])->textInput([
              'class' => 'hl-input', 'placeholder' => 'Wanjiku', 'id' => 'pr-last',
            ]) ?>
          </div>
        </div>
        <div class="col-md-6">
          <div class="hl-form-group">
            <label class="hl-label">Phone Number *</label>
            <?= $form->field($model, 'phone', ['template' => '{input}{error}'])->textInput([
              'class' => 'hl-input', 'placeholder' => '0712 345 678', 'id' => 'pr-phone',
            ]) ?>
          </div>
        </div>
        <div class="col-md-6">
          <div class="hl-form-group">
            <label class="hl-label">Email <span style="color:var(--text-muted);font-weight:400;">(optional)</span></label>
            <?= $form->field($model, 'email', ['template' => '{input}{error}'])->input('email', [
              'class' => 'hl-input', 'placeholder' => 'business@email.com', 'id' => 'pr-email',
            ]) ?>
          </div>
        </div>
        <div class="col-md-6">
          <div class="hl-form-group">
            <label class="hl-label">Password *</label>
            <?= $form->field($model, 'password', ['template' => '{input}{error}'])->passwordInput([
              'class' => 'hl-input', 'placeholder' => 'Min. 8 characters', 'id' => 'pr-pass',
            ]) ?>
          </div>
        </div>
        <div class="col-md-6">
          <div class="hl-form-group">
            <label class="hl-label">Confirm Password *</label>
            <?= $form->field($model, 'password_confirm', ['template' => '{input}{error}'])->passwordInput([
              'class' => 'hl-input', 'placeholder' => 'Repeat password', 'id' => 'pr-passconf',
            ]) ?>
          </div>
        </div>
      </div>

      <div class="hl-form-group">
        <label class="hl-label">Business Name *</label>
        <?= $form->field($model, 'business_name', ['template' => '{input}{error}'])->textInput([
          'class' => 'hl-input', 'placeholder' => 'e.g. Wanjiku Phone Repairs', 'id' => 'pr-biz',
        ]) ?>
      </div>

      <div class="hl-form-group">
        <label class="hl-label">About Your Business</label>
        <?= $form->field($model, 'description', ['template' => '{input}{error}'])->textarea([
          'class' => 'hl-textarea', 'rows' => 3,
          'placeholder' => 'Describe your services or products, years of experience…',
          'id' => 'pr-desc',
        ]) ?>
      </div>

      <div class="row g-3">
        <div class="col-md-6">
          <div class="hl-form-group">
            <label class="hl-label">City *</label>
            <?= $form->field($model, 'city', ['template' => '{input}{error}'])->textInput([
              'class' => 'hl-input', 'value' => 'Nairobi', 'id' => 'pr-city',
            ]) ?>
          </div>
        </div>
        <div class="col-md-6">
          <div class="hl-form-group">
            <label class="hl-label">Address / Area</label>
            <?= $form->field($model, 'address', ['template' => '{input}{error}'])->textInput([
              'class' => 'hl-input', 'placeholder' => 'e.g. Tom Mboya St, CBD', 'id' => 'pr-addr',
            ]) ?>
          </div>
        </div>
      </div>

      <div class="hl-form-group">
        <label class="hl-label">Business Location <span style="color:var(--text-muted);">(Drag pin to exact location)</span></label>
        <div id="providerLocationMap" style="height:200px;border:1px solid var(--border);border-radius:var(--r-md);margin-bottom:1rem;position:relative;overflow:hidden;">
          <div style="position:absolute;top:8px;right:8px;z-index:1000;background:var(--bg);border-radius:var(--r-sm);padding:0.4rem 0.8rem;font-size:0.8rem;font-weight:600;color:var(--text2);">Drag pin</div>
        </div>
        <?= Html::hiddenInput('ProviderRegisterForm[lat]', -1.2921, ['id' => 'pr-lat']) ?>
        <?= Html::hiddenInput('ProviderRegisterForm[lng]', 36.8219, ['id' => 'pr-lng']) ?>
        <small style="color:var(--text-muted);">Default Nairobi center. Drag pin for accurate customer map matching.</small>
      </div>

      <div class="hl-form-group">
        <label class="hl-label">ID / Business Document <span style="color:var(--text-muted);font-weight:400;">(optional — speeds up approval)</span></label>
        <?= $form->field($model, 'id_document', ['template' => '{input}{error}'])->fileInput([
          'class'  => 'form-control',
          'accept' => '.jpg,.jpeg,.png,.pdf',
          'id'     => 'pr-doc',
        ]) ?>
        <small style="color:var(--text-muted);">National ID, Business permit or certificate. Max 5MB.</small>
      </div>

      <div class="hl-alert hl-alert-info" style="font-size:0.85rem;">
        <i class="bi bi-info-circle-fill"></i>
        <span>After registration your account will be reviewed by our team (usually within 24 hours) before you can publish listings. You'll be notified via SMS/email.</span>
      </div>

      <p style="font-size:0.78rem;color:var(--text-muted);margin:1rem 0;">
        By registering you agree to our <a href="#">Provider Terms</a> and <a href="#">Platform Policies</a>.
      </p>

      <?= Html::submitButton('<i class="bi bi-briefcase-fill"></i> Submit Registration', [
        'class' => 'btn-hl-primary w-100',
        'style' => 'justify-content:center;padding:0.8rem;font-size:1rem;',
        'id'    => 'pr-submit',
      ]) ?>

      <?php ActiveForm::end(); ?>
    </div>
    <div class="hl-auth-footer">
      Already have an account? <a href="<?= Url::to(['/login']) ?>">Sign in</a>
      &nbsp;|&nbsp;
      <a href="<?= Yii::$app->params['frontendUrl'] . '/login' ?>">Customer portal</a>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  const map = L.map('providerLocationMap').setView([-1.2921, 36.8219], 12);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);

  let marker = L.marker([-1.2921, 36.8219], { draggable: true }).addTo(map);

  marker.on('dragend', function(e) {
    const pos = marker.getLatLng();
    $('#pr-lat').val(pos.lat);
    $('#pr-lng').val(pos.lng);
  });
});
</script>