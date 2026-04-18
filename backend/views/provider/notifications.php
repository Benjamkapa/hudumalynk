<?php
/** @var yii\web\View $this */
/** @var common\models\Provider $provider */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $stats */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Notifications';
?>

<style>
.pn-shell{display:flex;flex-direction:column;gap:16px;}
.pn-hero{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;}
.pn-title{font-size:24px;font-weight:800;color:var(--text1);letter-spacing:-.03em;}
.pn-sub{font-size:12px;color:var(--text3);margin-top:4px;max-width:740px;line-height:1.5;}
.pn-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;}
.pn-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:14px 15px;}
.pn-card .lbl{font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.1em;margin-bottom:5px;}
.pn-card .val{font-size:24px;font-weight:800;color:var(--text1);line-height:1;}
.pn-card .val.teal{color:var(--teal);}.pn-card .val.amber{color:var(--amber);}.pn-card .val.purple{color:var(--acc2);}
.pn-list{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden;}
.pn-item{display:flex;align-items:flex-start;gap:12px;padding:15px 16px;border-bottom:1px solid var(--border);}
.pn-item:last-child{border-bottom:none;}
.pn-icon{width:38px;height:38px;border-radius:50%;background:var(--acc-pale);color:var(--acc);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:13px;font-weight:800;}
.pn-copy{flex:1;min-width:0;}
.pn-copy strong{display:block;font-size:13px;color:var(--text1);margin-bottom:4px;}
.pn-copy p{font-size:12px;color:var(--text2);line-height:1.5;margin:0;}
.pn-meta{display:flex;gap:10px;flex-wrap:wrap;margin-top:6px;font-size:10.5px;color:var(--text3);}
.pn-dot{width:8px;height:8px;border-radius:50%;background:var(--acc);margin-top:6px;flex-shrink:0;}
@media(max-width:900px){.pn-stats{grid-template-columns:1fr 1fr;}}
@media(max-width:540px){.pn-stats{grid-template-columns:1fr;}}
</style>

<div class="pn-shell">
    <div class="pn-hero">
        <div>
            <div class="pn-title">Notification Center</div>
            <div class="pn-sub">Track operational alerts, order updates, reviews, and system notices for <?= Html::encode($provider->business_name) ?>.</div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="<?= Url::to(['/provider/settings']) ?>" class="hl-btn-g">Notification Settings</a>
            <a href="<?= Url::to(['/provider/notifications/mark-all-read']) ?>" class="hl-btn-p">Mark All Read</a>
        </div>
    </div>

    <div class="pn-stats">
        <div class="pn-card"><div class="lbl">Total</div><div class="val"><?= number_format($stats['total'] ?? 0) ?></div></div>
        <div class="pn-card"><div class="lbl">Unread</div><div class="val amber"><?= number_format($stats['unread'] ?? 0) ?></div></div>
        <div class="pn-card"><div class="lbl">Order Alerts</div><div class="val teal"><?= number_format($stats['orders'] ?? 0) ?></div></div>
        <div class="pn-card"><div class="lbl">Review Alerts</div><div class="val purple"><?= number_format($stats['reviews'] ?? 0) ?></div></div>
    </div>

    <div class="pn-list">
        <?php if ($dataProvider->getCount() === 0): ?>
            <div style="padding:26px 18px;color:var(--text3);font-size:12px;">No notifications yet. New orders, review activity, and subscription alerts will appear here.</div>
        <?php endif; ?>
        <?php foreach ($dataProvider->getModels() as $notification): ?>
            <?php $unread = !$notification->is_read; ?>
            <div class="pn-item">
                <div class="pn-icon"><?= Html::encode(strtoupper(substr($notification->title ?: 'N', 0, 1))) ?></div>
                <div class="pn-copy">
                    <strong><?= Html::encode($notification->title ?: 'Notification') ?></strong>
                    <p><?= Html::encode($notification->message ?: 'No notification message available.') ?></p>
                    <div class="pn-meta">
                        <span><?= Html::encode($notification->type ?: 'general') ?></span>
                        <span><?= date('d M Y · H:i', strtotime($notification->created_at ?: 'now')) ?></span>
                        <span><?= $notification->sent_via_sms ? 'SMS sent' : 'In-app only' ?></span>
                    </div>
                </div>
                <?php if ($unread): ?><div class="pn-dot"></div><?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
