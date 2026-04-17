<?php
/** @var common\models\Provider $provider */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Business Profile';
$this->registerCssFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
$this->registerJsFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', ['position' => \yii\web\View::POS_END]);
?>
<div class="hl-pg-head">
    <div>
        <h1 class="hl-pg-title">Business Profile</h1>
        <div class="hl-pg-sub">Update your public storefront details</div>
    </div>
</div>

<div style="display:flex; flex-wrap:wrap; gap:20px;">
  <div style="flex: 1 1 60%; min-width:300px;">
    <div class="hl-card" style="padding:24px;">
      <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

      <div style="display:flex; align-items:center; gap:20px; margin-bottom:24px;">
        <?php if ($provider->logo): ?>
          <img src="<?= Html::encode(rtrim(Yii::$app->params['frontendUrl'], '/') . '/uploads/' . $provider->logo) ?>" style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:2px solid var(--border);" alt="" onerror="this.style.display='none'">
        <?php else: ?>
          <div style="width:72px;height:72px;border-radius:50%;background:var(--acc-pale);color:var(--acc);display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:800;border:2px solid var(--border);">
            <?= strtoupper(substr($provider->business_name, 0, 2)) ?>
          </div>
        <?php endif; ?>
        <div>
          <label style="display:block; font-size:11px; font-weight:700; color:var(--text2); margin-bottom:6px; text-transform:uppercase; letter-spacing:0.05em;">Update Logo</label>
          <?= $form->field($provider, 'logoFile', ['template' => '{input}{error}'])->fileInput(['class' => 'hl-search-input', 'style' => 'width:100%;', 'accept' => 'image/*']) ?>
          <div style="font-size:10px; color:var(--text3); margin-top:4px;">Square image recommended. Max 2MB.</div>
        </div>
      </div>

      <div style="display:flex; flex-wrap:wrap; gap:16px;">
        <div style="flex: 1 1 45%;">
          <label style="display:block; font-size:11px; font-weight:700; color:var(--text2); margin-bottom:6px; text-transform:uppercase; letter-spacing:0.05em;">Business Name</label>
          <?= $form->field($provider, 'business_name', ['template' => '{input}{error}'])->textInput(['class' => 'hl-search-input', 'style' => 'width:100%; height:38px; border:1px solid var(--border); border-radius:var(--r-sm); padding:0 12px; background:var(--bg2);']) ?>
        </div>
        <div style="flex: 1 1 45%;">
          <label style="display:block; font-size:11px; font-weight:700; color:var(--text2); margin-bottom:6px; text-transform:uppercase; letter-spacing:0.05em;">Public Phone Number</label>
          <?= $form->field($provider, 'phone', ['template' => '{input}{error}'])->textInput(['class' => 'hl-search-input', 'style' => 'width:100%; height:38px; border:1px solid var(--border); border-radius:var(--r-sm); padding:0 12px; background:var(--bg2);']) ?>
        </div>
        <div style="flex: 1 1 45%;">
          <label style="display:block; font-size:11px; font-weight:700; color:var(--text2); margin-bottom:6px; text-transform:uppercase; letter-spacing:0.05em;">Public Email</label>
          <?= $form->field($provider, 'email', ['template' => '{input}{error}'])->textInput(['class' => 'hl-search-input', 'style' => 'width:100%; height:38px; border:1px solid var(--border); border-radius:var(--r-sm); padding:0 12px; background:var(--bg2);']) ?>
        </div>
        <div style="flex: 1 1 45%;">
          <label style="display:block; font-size:11px; font-weight:700; color:var(--text2); margin-bottom:6px; text-transform:uppercase; letter-spacing:0.05em;">City</label>
          <?= $form->field($provider, 'city', ['template' => '{input}{error}'])->textInput(['class' => 'hl-search-input', 'style' => 'width:100%; height:38px; border:1px solid var(--border); border-radius:var(--r-sm); padding:0 12px; background:var(--bg2);']) ?>
        </div>
        <div style="flex: 1 1 100%;">
          <label style="display:block; font-size:11px; font-weight:700; color:var(--text2); margin-bottom:6px; text-transform:uppercase; letter-spacing:0.05em;">Physical Address</label>
          <?= $form->field($provider, 'address', ['template' => '{input}{error}'])->textInput(['class' => 'hl-search-input', 'id' => 'pr-addr-backend', 'style' => 'width:100%; height:38px; border:1px solid var(--border); border-radius:var(--r-sm); padding:0 12px; background:var(--bg2);']) ?>
        </div>
        <div style="flex: 1 1 100%;">
          <label style="display:block; font-size:11px; font-weight:700; color:var(--text2); margin-bottom:6px; text-transform:uppercase; letter-spacing:0.05em;">Business Location Map</label>
          <div id="providerLocationMapBackend" style="height:280px;border:1px solid var(--border);border-radius:var(--r-sm);margin-bottom:12px;position:relative;overflow:hidden;">
            <div style="position:absolute;top:8px;right:8px;z-index:1000;background:var(--bg2);border-radius:var(--r-sm);padding:6px 10px;font-size:0.75rem;font-weight:600;color:var(--text2);">Set location</div>
          </div>
          <div style="font-size:0.8rem;color:var(--text3);padding:8px;background:var(--surface-2);border-radius:var(--r-sm);border-left:3px solid var(--acc);">
            💡 Enter address above and press Enter, or drag the pin to update your business location.
          </div>
          <?= Html::hiddenInput('Provider[lat]', $provider->lat, ['id' => 'pr-lat-backend']) ?>
          <?= Html::hiddenInput('Provider[lng]', $provider->lng, ['id' => 'pr-lng-backend']) ?>
        </div>
        <div style="flex: 1 1 100%;">
          <label style="display:block; font-size:11px; font-weight:700; color:var(--text2); margin-bottom:6px; text-transform:uppercase; letter-spacing:0.05em;">About Us (Store Description)</label>
          <?= $form->field($provider, 'description', ['template' => '{input}{error}'])->textarea(['rows' => 5, 'class' => 'hl-search-input', 'style' => 'width:100%; border:1px solid var(--border); border-radius:var(--r-sm); padding:10px 12px; background:var(--bg2);']) ?>
        </div>
        <div style="flex: 1 1 100%; margin-top:20px; border-top:1px solid var(--border); padding-top:20px;">
          <button type="submit" class="hl-btn-p">
             <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;margin-right:4px;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
             Save Profile
          </button>
        </div>
      </div>
      <?php ActiveForm::end(); ?>
    </div>
  </div>

  <div style="flex: 1 1 30%; min-width:260px;">
    <div class="hl-card">
      <div class="hl-card-head"><h4 class="hl-card-title">Profile Status</h4></div>
      <div class="hl-card-body" style="display:flex; flex-direction:column; gap:16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; padding-bottom:12px; border-bottom:1px solid var(--border);">
          <span style="font-size:12.5px;color:var(--text2);font-weight:600;">Account Status</span>
          <span class="hl-badge <?= $provider->isActive() ? 'paid' : 'pend' ?>"><?= strtoupper($provider->status) ?></span>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; padding-bottom:12px; border-bottom:1px solid var(--border);">
          <span style="font-size:12.5px;color:var(--text2);font-weight:600;">Verification</span>
          <?php if ($provider->is_verified): ?>
            <span style="font-size:11.5px;color:var(--acc);font-weight:700;"><svg viewBox="0 0 24 24" fill="currentColor" style="width:12px;height:12px;margin-bottom:-2px;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg> Verified</span>
          <?php else: ?>
            <span style="font-size:11.5px;color:var(--text3);font-weight:600;">Unverified</span>
          <?php endif; ?>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <span style="font-size:12.5px;color:var(--text2);font-weight:600;">Rating</span>
          <span style="font-size:13px;font-weight:700;color:var(--text1);"><svg viewBox="0 0 24 24" fill="var(--amber)" stroke="var(--amber)" style="width:12px;height:12px;margin-bottom:-2px;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg> <?= number_format($provider->rating, 1) ?></span>
        </div>
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

  const latInput = document.getElementById('pr-lat-backend');
  const lngInput = document.getElementById('pr-lng-backend');
  const addressInput = document.getElementById('pr-addr-backend');
  const cityInput = document.querySelector('[name="Provider[city]"]');

  // Initialize map with current coordinates or default to Nairobi
  const currentLat = parseFloat(latInput?.value) || -1.2921;
  const currentLng = parseFloat(lngInput?.value) || 36.8219;

  const map = L.map('providerLocationMapBackend').setView([currentLat, currentLng], 14);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
  }).addTo(map);

  const marker = L.marker([currentLat, currentLng], { draggable: true }).addTo(map);
  const coordsDisplay = document.createElement('div');
  coordsDisplay.style.cssText = 'position:absolute;bottom:12px;left:12px;z-index:1000;background:rgba(255,255,255,0.9);color:#111;border-radius:12px;padding:8px 12px;font-size:0.85rem;box-shadow:0 8px 24px rgba(0,0,0,0.12);pointer-events:none;';
  coordsDisplay.textContent = 'Location: ' + currentLat.toFixed(6) + ', ' + currentLng.toFixed(6);
  document.getElementById('providerLocationMapBackend').appendChild(coordsDisplay);

  function updateLocation(latlng) {
    const lat = latlng.lat.toFixed(6);
    const lng = latlng.lng.toFixed(6);
    if (latInput) latInput.value = lat;
    if (lngInput) lngInput.value = lng;
    coordsDisplay.textContent = 'Location: ' + lat + ', ' + lng;
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

    coordsDisplay.textContent = 'Searching...';

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
          coordsDisplay.textContent = '✗ Address not found';
        }
      })
      .catch(error => {
        console.error('Geocoding error:', error);
        coordsDisplay.textContent = '✗ Geocoding failed';
      });
  }

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
});
</script>
