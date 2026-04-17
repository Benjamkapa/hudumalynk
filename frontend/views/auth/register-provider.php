<?php
/** @var yii\web\View $this */
/** @var frontend\models\ProviderRegisterForm $model */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->registerCssFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
$this->registerJsFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', ['position' => \yii\web\View::POS_END]);
?>
<div style="padding-top:var(--navbar-h);background:linear-gradient(135deg,#F0F7FF 0%,#EDF8F0 100%);min-height:100vh;">

  <!-- Header Banner -->
  <div style="background:var(--hl-gradient-hero);padding:3.5rem 1.5rem;text-align:center;">
    <div class="hl-hero-tag" style="margin-bottom:1rem;">For Businesses & Individuals</div>
    <h1 style="color:#fff;font-size:clamp(1.6rem,4vw,2.4rem);margin-bottom:0.75rem;">Join HudumaLynk as a Provider</h1>
    <p style="color:rgba(255,255,255,0.82);max-width:520px;margin:0 auto;">Reach thousands of customers in Nairobi. Get your services or products listed in minutes.</p>

    <!-- Plan highlights -->
    <div class="d-flex gap-4 justify-content-center flex-wrap mt-3" style="color:rgba(255,255,255,0.75);font-size:0.85rem;">
      <span><i class="bi bi-check-circle-fill" style="color:var(--hl-green);"></i> Free registration</span>
      <span><i class="bi bi-check-circle-fill" style="color:var(--hl-green);"></i> Plans from KES 1,000/mo</span>
      <span><i class="bi bi-check-circle-fill" style="color:var(--hl-green);"></i> M-Pesa payments</span>
      <span><i class="bi bi-check-circle-fill" style="color:var(--hl-green);"></i> Verified badge</span>
    </div>
  </div>

  <div class="hl-section" style="padding-top:2.5rem;max-width:760px;">
    <div class="hl-card" style="border-radius:var(--radius-xl);overflow:hidden;">
      <!-- Tabs: Account Info / Business Info -->
      <div class="hl-card-header" style="background:var(--surface-3);border-bottom:2px solid var(--border);padding:1rem 2rem;gap:2rem;">
        <div style="display:flex;gap:2rem;font-size:0.9rem;font-weight:600;">
          <span style="color:var(--hl-blue);border-bottom:2px solid var(--hl-blue);padding-bottom:0.5rem;">1. Your Account</span>
          <span style="color:var(--text-muted);padding-bottom:0.5rem;">2. Business Details</span>
          <span style="color:var(--text-muted);padding-bottom:0.5rem;">3. Done ✓</span>
        </div>
      </div>

      <div class="hl-card-body" style="padding:2rem;">
        <?php $form = ActiveForm::begin([
          'id'      => 'provider-register-form',
          'options' => ['novalidate' => true, 'enctype' => 'multipart/form-data'],
        ]); ?>

        <!-- Section: Account -->
        <h4 style="margin-bottom:1.25rem;padding-bottom:0.75rem;border-bottom:1px solid var(--border);">
          <i class="bi bi-person-circle" style="color:var(--hl-blue);"></i> Your Account Details
        </h4>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="hl-label">First Name *</label>
            <?= $form->field($model, 'first_name', ['template' => '{input}{error}'])->textInput([
              'class' => 'hl-input', 'placeholder' => 'Jane', 'id' => 'pr-first',
            ]) ?>
          </div>
          <div class="col-md-6">
            <label class="hl-label">Last Name *</label>
            <?= $form->field($model, 'last_name', ['template' => '{input}{error}'])->textInput([
              'class' => 'hl-input', 'placeholder' => 'Wanjiku', 'id' => 'pr-last',
            ]) ?>
          </div>
          <div class="col-md-6">
            <label class="hl-label">Phone Number *</label>
            <?= $form->field($model, 'phone', ['template' => '{input}{error}'])->textInput([
              'class' => 'hl-input', 'placeholder' => '0712 345 678', 'id' => 'pr-phone',
            ]) ?>
          </div>
          <div class="col-md-6">
            <label class="hl-label">Email <span style="color:var(--text-muted);font-weight:400;">(optional)</span></label>
            <?= $form->field($model, 'email', ['template' => '{input}{error}'])->input('email', [
              'class' => 'hl-input', 'placeholder' => 'business@email.com', 'id' => 'pr-email',
            ]) ?>
          </div>
          <div class="col-md-6">
            <label class="hl-label">Password *</label>
            <?= $form->field($model, 'password', ['template' => '{input}{error}'])->passwordInput([
              'class' => 'hl-input', 'placeholder' => 'Min. 8 characters', 'id' => 'pr-pass',
            ]) ?>
          </div>
          <div class="col-md-6">
            <label class="hl-label">Confirm Password *</label>
            <?= $form->field($model, 'password_confirm', ['template' => '{input}{error}'])->passwordInput([
              'class' => 'hl-input', 'placeholder' => 'Repeat password', 'id' => 'pr-passconf',
            ]) ?>
          </div>
        </div>

        <!-- Section: Business -->
        <h4 style="margin:2rem 0 1.25rem;padding-bottom:0.75rem;border-bottom:1px solid var(--border);">
          <i class="bi bi-shop" style="color:var(--hl-green);"></i> Business Details
        </h4>
        <div class="row g-3">
          <div class="col-12">
            <label class="hl-label">Business Name *</label>
            <?= $form->field($model, 'business_name', ['template' => '{input}{error}'])->textInput([
              'class' => 'hl-input', 'placeholder' => 'e.g. Wanjiku Phone Repairs', 'id' => 'pr-biz',
            ]) ?>
          </div>
          <div class="col-12">
            <label class="hl-label">About Your Business</label>
            <?= $form->field($model, 'description', ['template' => '{input}{error}'])->textarea([
              'class' => 'hl-textarea', 'rows' => 4,
              'placeholder' => 'Describe your services or products, years of experience, specializations…',
              'id' => 'pr-desc',
            ]) ?>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="hl-label">City *</label>
              <?= $form->field($model, 'city', ['template' => '{input}{error}'])->textInput([
                'class' => 'hl-input', 'value' => 'Nairobi', 'id' => 'pr-city',
              ]) ?>
            </div>
            <div class="col-md-6">
              <label class="hl-label">Address / Area</label>
              <?= $form->field($model, 'address', ['template' => '{input}{error}'])->textInput([
                'class' => 'hl-input', 'placeholder' => 'e.g. Tom Mboya St, CBD', 'id' => 'pr-addr',
              ]) ?>
            </div>
          </div>
          <div class="col-12">
            <label class="hl-label">Business Location <span style="color:var(--text-muted);">(Click map or drag pin to set exact location)</span></label>
            <div id="providerLocationMap" style="height:280px;border:1px solid var(--border);border-radius:var(--r-md);margin-bottom:1rem;position:relative;overflow:hidden;">
              <div style="position:absolute;top:8px;right:8px;z-index:1000;background:var(--bg);border-radius:var(--r-sm);padding:0.4rem 0.8rem;font-size:0.8rem;font-weight:600;color:var(--text2);">Set location</div>
            </div>
            <?= $form->field($model, 'lat', ['template' => '{input}{error}'])->hiddenInput(['id' => 'pr-lat'])->label(false) ?>
            <?= $form->field($model, 'lng', ['template' => '{input}{error}'])->hiddenInput(['id' => 'pr-lng'])->label(false) ?>
            <small style="color:var(--text-muted);">Click or drag the pin to choose the exact business location before submitting.</small>
          </div>
          <div class="col-12">
            <label class="hl-label">ID / Business Document <span style="color:var(--text-muted);font-weight:400;">(optional — speeds up approval)</span></label>
            <?= $form->field($model, 'id_document', ['template' => '{input}{error}'])->fileInput([
              'class'  => 'form-control',
              'accept' => '.jpg,.jpeg,.png,.pdf',
              'id'     => 'pr-doc',
            ]) ?>
            <small style="color:var(--text-muted);">National ID, Business permit or certificate. Max 5MB.</small>
          </div>
        </div>

        <div class="hl-alert hl-alert-info mt-4" style="font-size:0.85rem;">
          <i class="bi bi-info-circle-fill"></i>
          <span>After registration your account will be reviewed by our team (usually within 24 hours) before you can publish listings. You'll be notified via SMS/email.</span>
        </div>

        <p style="font-size:0.78rem;color:var(--text-muted);margin:1rem 0;">
          By registering you agree to our <a href="#">Provider Terms</a> and <a href="#">Platform Policies</a>.
        </p>

        <?= Html::submitButton('<i class="bi bi-briefcase-fill"></i> Submit Registration', [
          'class' => 'btn-hl-green w-100',
          'style' => 'justify-content:center;padding:0.85rem;font-size:1rem;',
          'id'    => 'pr-submit',
        ]) ?>

        <?php ActiveForm::end(); ?>
      </div><!-- /.hl-card-body -->
    </div><!-- /.hl-card -->

    <!-- Already have account -->
    <p class="text-center mt-3" style="font-size:0.875rem;color:var(--text-muted);">
      Already registered? <a href="<?= Yii::$app->params['backendUrl'] . '/login' ?>">Go to provider portal</a>
    </p>

    <!-- Pricing preview -->
    <div id="plans" style="margin-top:3rem;">
      <h3 class="text-center mb-3">Choose a Plan After Approval</h3>
      <div class="row g-3">
        <?php
        $plans = [
          ['name'=>'Basic','price'=>'1,000','icon'=>'bi-stars','color'=>'var(--text-secondary)','features'=>['5 Products + 3 Services','Standard visibility','M-Pesa payments'],'popular'=>false],
          ['name'=>'Professional','price'=>'2,500','icon'=>'bi-lightning-charge-fill','color'=>'var(--hl-blue)','features'=>['20 Products + 10 Services','1 Featured slot','Verified badge','Priority listing'],'popular'=>true],
          ['name'=>'Premium','price'=>'5,000','icon'=>'bi-award-fill','color'=>'#F59E0B','features'=>['Unlimited listings','3 Featured slots','Verified badge','Priority support'],'popular'=>false],
        ];
        foreach ($plans as $plan): ?>
        <div class="col-md-4">
          <div class="hl-card" style="border:2px solid <?= $plan['popular'] ? 'var(--hl-blue)' : 'var(--border)' ?>;border-radius:var(--radius-lg);overflow:hidden;">
            <?php if ($plan['popular']): ?>
            <div style="background:var(--hl-blue);color:#fff;text-align:center;font-size:0.72rem;font-weight:700;letter-spacing:0.08em;padding:0.3rem;text-transform:uppercase;">⭐ Most Popular</div>
            <?php endif; ?>
            <div class="hl-card-body" style="padding:1.5rem;text-align:center;">
              <i class="<?= $plan['icon'] ?>" style="font-size:2rem;color:<?= $plan['color'] ?>;display:block;margin-bottom:0.5rem;"></i>
              <div style="font-size:1.1rem;font-weight:700;"><?= $plan['name'] ?></div>
              <div style="font-size:1.5rem;font-weight:800;color:var(--hl-blue);margin:0.5rem 0;">KES <?= $plan['price'] ?><span style="font-size:0.8rem;font-weight:400;color:var(--text-muted);">/mo</span></div>
              <?php foreach ($plan['features'] as $f): ?>
              <div style="font-size:0.82rem;color:var(--text-secondary);padding:0.2rem 0;"><i class="bi bi-check2" style="color:var(--hl-green);"></i> <?= $f ?></div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  if (typeof L === 'undefined') {
    console.error('Leaflet is not loaded.');
    return;
  }

  const latInput = document.getElementById('pr-lat');
  const lngInput = document.getElementById('pr-lng');
  const addressInput = document.getElementById('pr-addr');
  const cityInput = document.getElementById('pr-city');
  const coordsDisplay = document.createElement('div');
  coordsDisplay.style.cssText = 'position:absolute;bottom:12px;left:12px;z-index:1000;background:rgba(255,255,255,0.9);color:#111;border-radius:12px;padding:8px 12px;font-size:0.85rem;box-shadow:0 8px 24px rgba(0,0,0,0.12);pointer-events:none;';
  coordsDisplay.textContent = 'Pin location: -1.2921, 36.8219';
  document.getElementById('providerLocationMap').appendChild(coordsDisplay);

  const initialLat = -1.2921;
  const initialLng = 36.8219;

  const map = L.map('providerLocationMap').setView([initialLat, initialLng], 12);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
  }).addTo(map);

  const marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

  function updateLocation(latlng) {
    const lat = latlng.lat.toFixed(6);
    const lng = latlng.lng.toFixed(6);
    if (latInput) latInput.value = lat;
    if (lngInput) lngInput.value = lng;
    coordsDisplay.textContent = 'Pin location: ' + lat + ', ' + lng;
  }

  // Geocoding function using Nominatim API
  let geocodeTimeout;
  function geocodeAddress() {
    const address = addressInput?.value?.trim() || '';
    const city = cityInput?.value?.trim() || 'Nairobi';
    const fullAddress = address ? address + ', ' + city : city;

    if (!fullAddress || fullAddress.length < 3) {
      coordsDisplay.textContent = 'Enter an address to geocode';
      return;
    }

    coordsDisplay.textContent = 'Searching for address...';

    fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(fullAddress), {
      headers: { 'User-Agent': 'HudumaLynk-Provider-App' }
    })
      .then(response => response.json())
      .then(data => {
        if (data && data.length > 0) {
          const result = data[0];
          const lat = parseFloat(result.lat);
          const lng = parseFloat(result.lon);
          marker.setLatLng([lat, lng]);
          map.setView([lat, lng], 14);
          updateLocation({ lat: lat, lng: lng });
          coordsDisplay.textContent = '✓ Located: ' + (result.address || fullAddress);
        } else {
          coordsDisplay.textContent = '✗ Address not found. Check spelling or drag pin manually.';
        }
      })
      .catch(error => {
        console.error('Geocoding error:', error);
        coordsDisplay.textContent = '✗ Could not geocode. Drag pin manually.';
      });
  }

  // Debounced geocoding on address change
  if (addressInput) {
    addressInput.addEventListener('change', function () {
      clearTimeout(geocodeTimeout);
      geocodeTimeout = setTimeout(geocodeAddress, 800);
    });
  }

  marker.on('dragend', function () {
    updateLocation(marker.getLatLng());
  });

  map.on('click', function (e) {
    marker.setLatLng(e.latlng);
    updateLocation(e.latlng);
  });

  coordsDisplay.textContent = 'Enter address above or tap map/drag pin to set location.';
});
</script>


<style>
#providerLocationMap .leaflet-container { border-radius: var(--r-md); }
</style>
