<?php
/** @var yii\web\View $this */
/** @var common\models\Provider $provider */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'My Business Profile';
?>

<style>
.profile-header { margin-bottom: 32px; }
.profile-title { font-size: 24px; font-weight: 800; color: var(--text1); margin-bottom: 8px; }
.profile-wrapper { display: grid; grid-template-columns: minmax(0, 280px) minmax(0, 1fr); gap: 24px; }
.profile-sidebar { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 24px; }
.logo-section { text-align: center; }
.logo-preview { width: 140px; height: 140px; margin: 0 auto 16px; border: 2px solid var(--border); border-radius: var(--r-lg); display: flex; align-items: center; justify-content: center; background: var(--bg3); }
.logo-preview img { width: 100%; height: 100%; object-fit: contain; border-radius: var(--r-md); }
.logo-upload { font-size: 12px; }
.logo-input { display: none; }
.logo-btn { display: inline-block; background: var(--acc); color: #fff; border: none; border-radius: var(--r-md); padding: 8px 14px; font-size: 12px; font-weight: 600; cursor: pointer; }
.logo-btn:hover { background: var(--acc2); }
.profile-stats { border-top: 1px solid var(--border); padding-top: 16px; margin-top: 16px; }
.stat-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border); }
.stat-item:last-child { border-bottom: none; }
.stat-label { font-size: 12px; color: var(--text3); }
.stat-value { font-size: 13px; font-weight: 700; color: var(--text1); }
.profile-form { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 24px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; font-size: 13px; font-weight: 600; color: var(--text1); margin-bottom: 8px; }
.form-group input, .form-group textarea { width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: var(--r-md); font-size: 14px; color: var(--text1); background: var(--bg); font-family: inherit; }
.form-group input:focus, .form-group textarea:focus { outline: none; border-color: var(--acc); box-shadow: 0 0 0 3px rgba(108,92,231,.1); }
.form-group textarea { resize: vertical; min-height: 100px; }
.form-helper { font-size: 12px; color: var(--text3); margin-top: 4px; }
.form-actions { display: flex; gap: 12px; margin-top: 24px; }
.btn { padding: 12px 24px; border: none; border-radius: var(--r-md); font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; }
.btn-primary { background: var(--acc); color: #fff; }
.btn-primary:hover { background: var(--acc2); }
.btn-secondary { background: var(--bg3); color: var(--text1); border: 1px solid var(--border); }
.btn-secondary:hover { background: var(--border); }
</style>

<div class="profile-header">
    <h1 class="profile-title">My Business Profile</h1>
</div>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<div class="profile-wrapper">
    <!-- Sidebar -->
    <div class="profile-sidebar">
        <div class="logo-section">
            <div class="logo-preview">
                <?php if ($provider->logo): ?>
                    <img src="<?= Html::encode(rtrim(Yii::$app->params['frontendUrl'], '/') . '/uploads/' . $provider->logo) ?>" alt="Logo">
                <?php else: ?>
                    <span style="font-size: 32px;">📦</span>
                <?php endif; ?>
            </div>
            <div class="logo-upload">
                <input type="file" id="logoInput" name="Provider[logoFile]" class="logo-input" accept="image/*">
                <button type="button" class="logo-btn" onclick="document.getElementById('logoInput').click();">Upload Logo</button>
            </div>
        </div>

        <div class="profile-stats">
            <div class="stat-item">
                <span class="stat-label">Status</span>
                <span class="stat-value" style="color: <?= $provider->status === 'active' ? '#22c55e' : '#fbbf24' ?>;">
                    <?= ucfirst($provider->status) ?>
                </span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Rating</span>
                <span class="stat-value">★ <?= number_format($provider->rating, 1) ?>/5</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Joined</span>
                <span class="stat-value"><?= date('M d, Y', strtotime($provider->created_at)) ?></span>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div>
        <div class="profile-form">
            <div class="form-group">
                <?= $form->field($provider, 'business_name')->textInput()->label('Business Name') ?>
            </div>

            <div class="form-group">
                <?= $form->field($provider, 'email')->textInput(['type' => 'email'])->label('Email Address') ?>
            </div>

            <div class="form-group">
                <?= $form->field($provider, 'phone')->textInput()->label('Phone Number') ?>
            </div>

            <div class="form-group">
                <?= $form->field($provider, 'description')->textarea(['rows' => 4])->label('About Your Business') ?>
                <div class="form-helper">Tell customers about your business, experience, and services</div>
            </div>

            <div class="form-group">
                <?= $form->field($provider, 'city')->textInput()->label('City/County') ?>
            </div>

            <div class="form-group">
                <?= $form->field($provider, 'address')->textInput()->label('Business Address') ?>
            </div>

            <div class="form-group">
                <?= $form->field($provider, 'lat')->textInput(['type' => 'number', 'step' => '0.0001'])->label('Latitude') ?>
                <div class="form-helper">Click "Get Location" button below to auto-fill coordinates</div>
            </div>

            <div class="form-group">
                <?= $form->field($provider, 'lng')->textInput(['type' => 'number', 'step' => '0.0001'])->label('Longitude') ?>
            </div>

            <button type="button" class="btn btn-secondary" id="getLocBtn">📍 Get My Location</button>

            <div class="form-actions" style="margin-top: 24px;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php
$this->registerJs("
document.getElementById('getLocBtn').addEventListener('click', function() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('provider-lat').value = position.coords.latitude.toFixed(6);
            document.getElementById('provider-lng').value = position.coords.longitude.toFixed(6);
            alert('Location captured!');
        }, function(error) {
            alert('Unable to get location: ' + error.message);
        });
    } else {
        alert('Geolocation is not supported by your browser');
    }
});
", \yii\web\View::POS_READY);
?>