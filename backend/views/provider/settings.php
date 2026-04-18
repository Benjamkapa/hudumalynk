<?php
/** @var yii\web\View $this */
/** @var common\models\Provider $provider */
/** @var array $settings */

use yii\helpers\Html;

$this->title = 'Settings';

$toggleSections = [
    'Store Operations' => [
        ['vacation_mode', 'Vacation mode', 'Pause active order intake while you reorganise your shop.'],
        ['listing_auto_pause', 'Auto-pause inactive listings', 'Temporarily hide listings that are not converting well.'],
        ['auto_accept_paid', 'Auto-accept fully paid orders', 'Move paid orders into processing immediately.'],
        ['allow_cod', 'Allow cash on delivery', 'Keep low-friction payment options enabled for customers.'],
    ],
    'Notifications' => [
        ['email_notifications', 'Email notifications', 'Receive operational alerts in your email inbox.'],
        ['sms_notifications', 'SMS notifications', 'Receive urgent order and payout updates by SMS.'],
        ['review_notifications', 'Review notifications', 'Get alerted whenever a new rating or review is posted.'],
        ['daily_digest', 'Daily digest', 'Bundle non-urgent activity into one daily summary.'],
        ['weekly_summary', 'Weekly summary', 'Send a weekly performance recap with trends.'],
    ],
    'Visibility & Trust' => [
        ['show_public_phone', 'Show public phone number', 'Display your support line on your public storefront.'],
        ['show_precise_location', 'Show precise map location', 'Let customers find you using exact coordinates on the map.'],
    ],
];
?>

<style>
    .st-shell {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .st-hero {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .st-title {
        font-size: 24px;
        font-weight: 800;
        color: var(--text1);
        letter-spacing: -.03em;
    }

    .st-sub {
        font-size: 12px;
        color: var(--text3);
        margin-top: 5px;
        max-width: 760px;
        line-height: 1.5;
    }

    .st-grid {
        display: grid;
        grid-template-columns: 1.25fr .95fr;
        gap: 16px;
        align-items: start;
    }

    .st-card {
        background: var(--bg2);
        border: 1px solid var(--border);
        border-radius: var(--r-lg);
        overflow: hidden;
    }

    .st-head {
        padding: 16px 18px;
        border-bottom: 1px solid var(--border);
    }

    .st-head h2 {
        font-size: 15px;
        font-weight: 800;
        color: var(--text1);
        margin: 0 0 4px;
    }

    .st-head p {
        font-size: 11.5px;
        color: var(--text3);
        margin: 0;
    }

    .st-body {
        padding: 18px;
    }

    .st-stack {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .st-section {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .st-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 14px 0;
        border-bottom: 1px solid var(--border);
    }

    .st-row:last-child {
        border-bottom: none;
    }

    .st-row-copy {
        flex: 1;
        min-width: 0;
    }

    .st-row-copy strong {
        display: block;
        font-size: 13px;
        color: var(--text1);
        margin-bottom: 3px;
    }

    .st-row-copy span {
        display: block;
        font-size: 11.5px;
        color: var(--text3);
        line-height: 1.45;
    }

    .st-toggle {
        width: 56px;
        height: 32px;
        border-radius: 999px;
        background: var(--bg3);
        border: 1px solid var(--border);
        position: relative;
        flex-shrink: 0;
    }

    .st-toggle input {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }

    .st-toggle span {
        position: absolute;
        top: 3px;
        left: 3px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .12);
        transition: all .18s ease;
    }

    .st-toggle input:checked+span {
        left: 27px;
        background: #fff;
    }

    .st-toggle:has(input:checked) {
        background: var(--teal);
    }

    .st-field-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .st-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .st-field label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--text3);
    }

    .st-field input,
    .st-field select {
        width: 100%;
        height: 42px;
        border: 1px solid var(--border);
        border-radius: var(--r-md);
        background: var(--bg);
        padding: 0 12px;
        font-size: 13px;
        color: var(--text1);
        font-family: var(--font);
    }

    .st-chip-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .st-chip {
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: var(--r-md);
        padding: 12px 13px;
    }

    .st-chip .lbl {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--text3);
        margin-bottom: 5px;
    }

    .st-chip .val {
        font-size: 18px;
        font-weight: 800;
        color: var(--text1);
    }

    .st-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
        padding-top: 6px;
    }

    .st-note {
        font-size: 11.5px;
        color: var(--text3);
        line-height: 1.5;
    }

    @media(max-width:920px) {
        .st-grid {
            grid-template-columns: 1fr;
        }

        .st-field-grid,
        .st-chip-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="st-shell">
    <div class="st-hero">
        <div>
            <div class="st-title">Store Settings</div>
            <div class="st-sub">Tune how <?= Html::encode($provider->business_name) ?> handles orders, visibility,
                communication, and service targets. These controls are saved specifically for this provider account.
            </div>
        </div>
    </div>

    <form method="post" class="st-grid">
        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

        <div class="st-stack">
            <?php foreach ($toggleSections as $sectionTitle => $rows): ?>
                    <div class="st-card">
                    <div class="st-head">
                        <h2><?= Html::encode($sectionTitle) ?></h2>
                        <p><?= Html::encode($sectionTitle === 'Store Operations' ? 'Control order intake, automation, and storefront activity.' : ($sectionTitle === 'Notifications' ? 'Decide which alerts should interrupt you and which can wait.' : 'Choose how much business detail customers can see.')) ?>
                        </p>
                    </div>
                    <div class="st-body">
                        <?php foreach ($rows as [$key, $label, $desc]): ?>
                            <div class="st-row">
                                <div class="st-row-copy">
                                    <strong><?= Html::encode($label) ?></strong>
                                    <span><?= Html::encode($desc) ?></span>
                                </div>
                                <label class="st-toggle">
                                    <input type="hidden" name="settings[<?= Html::encode($key) ?>]" value="0">
                                    <input type="checkbox" name="settings[<?= Html::encode($key) ?>]" value="1"
                                        <?= !empty($settings[$key]) ? 'checked' : '' ?>>
                                    <span></span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="st-stack">
            <div class="st-card">
                <div class="st-head">
                    <h2>Service Targets</h2>
                    <p>Set the timing, support, and payout thresholds your store should operate with.</p>
                </div>
                <div class="st-body">
                    <div class="st-field-grid">
                        <div class="st-field">
                            <label for="timezone">Timezone</label>
                            <select id="timezone" name="settings[timezone]">
                                <?php foreach (['Africa/Nairobi', 'UTC', 'Europe/London', 'Asia/Dubai'] as $timezone): ?>
                                    <option value="<?= Html::encode($timezone) ?>" <?= ($settings['timezone'] ?? '') === $timezone ? 'selected' : '' ?>><?= Html::encode($timezone) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="st-field">
                            <label for="lead_time_hours">Lead Time Hours</label>
                            <input id="lead_time_hours" type="number" min="1" step="1" name="settings[lead_time_hours]"
                                value="<?= Html::encode($settings['lead_time_hours'] ?? 24) ?>">
                        </div>
                        <div class="st-field">
                            <label for="order_buffer_minutes">Order Buffer Minutes</label>
                            <input id="order_buffer_minutes" type="number" min="0" step="5"
                                name="settings[order_buffer_minutes]"
                                value="<?= Html::encode($settings['order_buffer_minutes'] ?? 30) ?>">
                        </div>
                        <div class="st-field">
                            <label for="response_target_mins">Response Target Minutes</label>
                            <input id="response_target_mins" type="number" min="1" step="1"
                                name="settings[response_target_mins]"
                                value="<?= Html::encode($settings['response_target_mins'] ?? 15) ?>">
                        </div>
                        <div class="st-field">
                            <label for="payout_threshold">Payout Threshold</label>
                            <input id="payout_threshold" type="number" min="0" step="100"
                                name="settings[payout_threshold]"
                                value="<?= Html::encode($settings['payout_threshold'] ?? 1000) ?>">
                        </div>
                        <div class="st-field">
                            <label for="support_phone">Support Phone</label>
                            <input id="support_phone" type="text" name="settings[support_phone]"
                                value="<?= Html::encode($settings['support_phone'] ?? '') ?>">
                        </div>
                        <div class="st-field" style="grid-column:1 / -1;">
                            <label for="support_email">Support Email</label>
                            <input id="support_email" type="email" name="settings[support_email]"
                                value="<?= Html::encode($settings['support_email'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="st-card">
                <div class="st-head">
                    <h2>Operational Snapshot</h2>
                    <p>Quick references for the settings most likely to affect conversions and response time.</p>
                </div>
                <div class="st-body">
                    <div class="st-chip-grid">
                        <div class="st-chip">
                            <div class="lbl">Notifications</div>
                            <div class="val">
                                <?= !empty($settings['email_notifications']) || !empty($settings['sms_notifications']) ? 'Live' : 'Muted' ?>
                            </div>
                        </div>
                        <div class="st-chip">
                            <div class="lbl">Lead Time</div>
                            <div class="val"><?= (int) ($settings['lead_time_hours'] ?? 24) ?>h</div>
                        </div>
                        <div class="st-chip">
                            <div class="lbl">Payout Threshold</div>
                            <div class="val">KES
                                <?= number_format((float) ($settings['payout_threshold'] ?? 1000), 0) ?></div>
                        </div>
                        <div class="st-chip">
                            <div class="lbl">Store Mode</div>
                            <div class="val"><?= !empty($settings['vacation_mode']) ? 'Paused' : 'Active' ?></div>
                        </div>
                    </div>
                    <p class="st-note" style="margin-top:14px;">Profile edits such as business name, logo, coordinates,
                        and description still live under your profile page. These settings focus on store behaviour and
                        system tuning.</p>
                    <div class="st-actions">
                        <a href="<?= yii\helpers\Url::to(['/provider/profile']) ?>" class="hl-btn-g">Open Profile</a>
                        <button type="submit" class="hl-btn-p">Save Settings</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>