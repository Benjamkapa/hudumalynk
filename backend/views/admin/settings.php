<?php
/** @var yii\web\View $this */
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Setting;
$this->title = 'Platform Settings';
?>
<div class="hl-card">
    <div class="hl-card-head">
        <span class="hl-card-title">General Preferences</span>
    </div>
    <div class="hl-card-body">
        <form method="post" action="<?= Url::to(['/admin/settings']) ?>">
            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
            
            <div style="margin-bottom:15px;">
                <label style="display:block;font-size:12px;font-weight:700;color:var(--text1);margin-bottom:5px;">Platform Name</label>
                <input type="text" name="settings[site_name]" value="<?= Html::encode(Setting::get('site_name', 'HudumaLynk')) ?>" style="background:var(--bg);border:1px solid var(--border);padding:8px 12px;border-radius:var(--r-md);width:100%;font-size:13px;color:var(--text1);outline:none;">
            </div>
            
            <div style="margin-bottom:15px;">
                <label style="display:block;font-size:12px;font-weight:700;color:var(--text1);margin-bottom:5px;">Support Email</label>
                <input type="email" name="settings[support_email]" value="<?= Html::encode(Setting::get('support_email', 'support@hudumalyk.co.ke')) ?>" style="background:var(--bg);border:1px solid var(--border);padding:8px 12px;border-radius:var(--r-md);width:100%;font-size:13px;color:var(--text1);outline:none;">
            </div>

            <button type="submit" class="hl-btn-p">Save Settings</button>
        </form>
    </div>
</div>