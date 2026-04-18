<?php
/** @var yii\web\View $this */

use common\models\Setting;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Platform Settings';

$bool = static function (string $key, $default = '0'): bool {
    return (string) Setting::get($key, $default) === '1';
};
?>

<style>
.as-shell{display:flex;flex-direction:column;gap:16px;}
.as-title{font-size:24px;font-weight:800;color:var(--text1);letter-spacing:-.03em;}
.as-sub{font-size:12px;color:var(--text3);margin-top:4px;line-height:1.5;max-width:780px;}
.as-grid{display:grid;grid-template-columns:1.15fr .95fr;gap:16px;align-items:start;}
.as-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden;}
.as-head{padding:16px 18px;border-bottom:1px solid var(--border);}
.as-head h2{font-size:15px;font-weight:800;color:var(--text1);margin:0 0 4px;}
.as-head p{font-size:11.5px;color:var(--text3);margin:0;}
.as-body{padding:18px;}
.as-stack{display:flex;flex-direction:column;gap:16px;}
.as-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;}
.as-field{display:flex;flex-direction:column;gap:6px;}
.as-field.full{grid-column:1 / -1;}
.as-field label{font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--text3);}
.as-field input,.as-field select{height:42px;border:1px solid var(--border);border-radius:var(--r-md);background:var(--bg);padding:0 12px;font-size:13px;color:var(--text1);font-family:var(--font);}
.as-toggle-row{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:14px 0;border-bottom:1px solid var(--border);}
.as-toggle-row:last-child{border-bottom:none;}
.as-toggle-copy strong{display:block;font-size:13px;color:var(--text1);margin-bottom:3px;}
.as-toggle-copy span{display:block;font-size:11.5px;color:var(--text3);line-height:1.45;}
.as-toggle{width:56px;height:32px;border-radius:999px;background:var(--bg3);border:1px solid var(--border);position:relative;flex-shrink:0;}
.as-toggle input{position:absolute;inset:0;opacity:0;cursor:pointer;}
.as-toggle span{position:absolute;top:3px;left:3px;width:24px;height:24px;border-radius:50%;background:#fff;box-shadow:0 2px 6px rgba(0,0,0,.12);}
.as-toggle:has(input:checked){background:var(--teal);}
.as-toggle input:checked + span{left:27px;}
.as-actions{display:flex;justify-content:flex-end;gap:8px;flex-wrap:wrap;}
.as-note{font-size:11.5px;color:var(--text3);line-height:1.5;}
@media(max-width:980px){.as-grid,.as-fields{grid-template-columns:1fr;}}
</style>

<div class="as-shell">
    <div>
        <div class="as-title">Platform Settings</div>
        <div class="as-sub">Tune marketplace behaviour, order policy, notifications, mapping defaults, and onboarding rules from one place. All values below are stored in the shared settings table.</div>
    </div>

    <form method="post" action="<?= Url::to(['/admin/settings']) ?>" class="as-grid">
        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

        <div class="as-stack">
            <div class="as-card">
                <div class="as-head">
                    <h2>Brand & Contact</h2>
                    <p>Customer-facing identity and support channels.</p>
                </div>
                <div class="as-body as-fields">
                    <div class="as-field">
                        <label>Platform Name</label>
                        <input type="text" name="settings[platform_name]" value="<?= Html::encode(Setting::get('platform_name', 'HudumaLynk')) ?>">
                    </div>
                    <div class="as-field">
                        <label>Platform Tagline</label>
                        <input type="text" name="settings[platform_tagline]" value="<?= Html::encode(Setting::get('platform_tagline', 'Nairobi service marketplace')) ?>">
                    </div>
                    <div class="as-field">
                        <label>Support Email</label>
                        <input type="email" name="settings[support_email]" value="<?= Html::encode(Setting::get('support_email', 'support@hudumalynk.co.ke')) ?>">
                    </div>
                    <div class="as-field">
                        <label>Support Phone</label>
                        <input type="text" name="settings[support_phone]" value="<?= Html::encode(Setting::get('support_phone', '+254700000000')) ?>">
                    </div>
                    <div class="as-field">
                        <label>Frontend URL</label>
                        <input type="text" name="settings[frontend_url]" value="<?= Html::encode(Setting::get('frontend_url', Yii::$app->params['frontendUrl'] ?? 'http://localhost:8080')) ?>">
                    </div>
                    <div class="as-field">
                        <label>Default Currency</label>
                        <select name="settings[default_currency]">
                            <?php foreach (['KES', 'USD'] as $currency): ?>
                                <option value="<?= $currency ?>" <?= Setting::get('default_currency', 'KES') === $currency ? 'selected' : '' ?>><?= $currency ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="as-card">
                <div class="as-head">
                    <h2>Commerce Rules</h2>
                    <p>Core marketplace behaviour for orders, payouts, and commissions.</p>
                </div>
                <div class="as-body as-fields">
                    <div class="as-field">
                        <label>Commission Rate (%)</label>
                        <input type="number" step="0.1" min="0" name="settings[commission_rate]" value="<?= Html::encode(Setting::get('commission_rate', '10')) ?>">
                    </div>
                    <div class="as-field">
                        <label>COD Limit (KES)</label>
                        <input type="number" step="100" min="0" name="settings[order_cod_limit]" value="<?= Html::encode(Setting::get('order_cod_limit', '5000')) ?>">
                    </div>
                    <div class="as-field">
                        <label>Deposit Percent (%)</label>
                        <input type="number" step="1" min="0" max="100" name="settings[order_deposit_percent]" value="<?= Html::encode(Setting::get('order_deposit_percent', '30')) ?>">
                    </div>
                    <div class="as-field">
                        <label>Payout Batch Day</label>
                        <select name="settings[payout_batch_day]">
                            <?php foreach (['Monday','Tuesday','Wednesday','Thursday','Friday'] as $day): ?>
                                <option value="<?= $day ?>" <?= Setting::get('payout_batch_day', 'Friday') === $day ? 'selected' : '' ?>><?= $day ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="as-field">
                        <label>Invoice Prefix</label>
                        <input type="text" name="settings[invoice_prefix]" value="<?= Html::encode(Setting::get('invoice_prefix', 'HL-INV')) ?>">
                    </div>
                    <div class="as-field">
                        <label>Max Upload MB</label>
                        <input type="number" step="1" min="1" name="settings[max_upload_mb]" value="<?= Html::encode(Setting::get('max_upload_mb', '5')) ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="as-stack">
            <div class="as-card">
                <div class="as-head">
                    <h2>Automation & Access</h2>
                    <p>System switches for onboarding, moderation, and alerts.</p>
                </div>
                <div class="as-body">
                    <?php
                    $toggles = [
                        ['registration_open', 'Open registrations', 'Allow new customer and provider signups.'],
                        ['provider_auto_approval', 'Auto-approve providers', 'Bypass manual approval for new provider accounts.'],
                        ['maintenance_mode', 'Maintenance mode', 'Temporarily lock customer-facing actions while you service the platform.'],
                        ['notify_admin_new_provider', 'Notify admins on new provider', 'Alert admin accounts when a provider signs up.'],
                        ['notify_provider_new_order', 'Notify providers on new orders', 'Send provider alerts as soon as orders arrive.'],
                        ['analytics_tracking_enabled', 'Enable analytics tracking', 'Collect metrics used in dashboards and reports.'],
                    ];
                    ?>
                    <?php foreach ($toggles as [$key, $label, $desc]): ?>
                        <div class="as-toggle-row">
                            <div class="as-toggle-copy">
                                <strong><?= Html::encode($label) ?></strong>
                                <span><?= Html::encode($desc) ?></span>
                            </div>
                            <label class="as-toggle">
                                <input type="hidden" name="settings[<?= Html::encode($key) ?>]" value="0">
                                <input type="checkbox" name="settings[<?= Html::encode($key) ?>]" value="1" <?= $bool($key) ? 'checked' : '' ?>>
                                <span></span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="as-card">
                <div class="as-head">
                    <h2>Map Defaults</h2>
                    <p>Baseline coordinates and zoom used by map-heavy admin screens.</p>
                </div>
                <div class="as-body as-fields">
                    <div class="as-field">
                        <label>Latitude</label>
                        <input type="number" step="0.0001" name="settings[map_default_lat]" value="<?= Html::encode(Setting::get('map_default_lat', '-1.2921')) ?>">
                    </div>
                    <div class="as-field">
                        <label>Longitude</label>
                        <input type="number" step="0.0001" name="settings[map_default_lng]" value="<?= Html::encode(Setting::get('map_default_lng', '36.8219')) ?>">
                    </div>
                    <div class="as-field full">
                        <label>Default Zoom</label>
                        <input type="number" step="1" min="1" max="18" name="settings[map_default_zoom]" value="<?= Html::encode(Setting::get('map_default_zoom', '12')) ?>">
                    </div>
                </div>
            </div>

            <div class="as-card">
                <div class="as-head">
                    <h2>Operating Notes</h2>
                    <p>Quick reminders before you commit changes.</p>
                </div>
                <div class="as-body">
                    <p class="as-note">MPesa credentials and callback endpoints can also be updated here by saving custom keys such as `mpesa_shortcode`, `mpesa_passkey`, `mpesa_consumer_key`, and `mpesa_callback_url` if you need to rotate production values without touching `.env`.</p>
                    <div class="as-actions" style="margin-top:14px;">
                        <a href="<?= Url::to(['/admin/analytics']) ?>" class="hl-btn-g">Open Analytics</a>
                        <button type="submit" class="hl-btn-p">Save Settings</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
