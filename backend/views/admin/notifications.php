<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $stats */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Notifications';
?>

<style>
.an-shell{display:flex;flex-direction:column;gap:16px;}
.an-title{font-size:24px;font-weight:800;color:var(--text1);letter-spacing:-.03em;}
.an-sub{font-size:12px;color:var(--text3);margin-top:4px;line-height:1.5;}
.an-grid{display:grid;grid-template-columns:1.05fr .95fr;gap:16px;align-items:start;}
.an-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden;}
.an-head{padding:16px 18px;border-bottom:1px solid var(--border);}
.an-head strong{font-size:15px;color:var(--text1);}
.an-head span{display:block;font-size:11px;color:var(--text3);margin-top:4px;}
.an-body{padding:18px;}
.an-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;}
.an-stat{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:14px 15px;}
.an-stat .lbl{font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--text3);margin-bottom:5px;}
.an-stat .val{font-size:24px;font-weight:800;color:var(--text1);}
.an-fields{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.an-field{display:flex;flex-direction:column;gap:6px;}
.an-field.full{grid-column:1 / -1;}
.an-field label{font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--text3);}
.an-field input,.an-field select,.an-field textarea{width:100%;border:1px solid var(--border);border-radius:var(--r-md);background:var(--bg);padding:10px 12px;font-size:13px;color:var(--text1);font-family:var(--font);}
.an-field textarea{min-height:120px;resize:vertical;}
.an-list{display:flex;flex-direction:column;}
.an-item{display:flex;gap:12px;padding:15px 16px;border-bottom:1px solid var(--border);}
.an-item:last-child{border-bottom:none;}
.an-icon{width:38px;height:38px;border-radius:50%;background:var(--acc-pale);color:var(--acc);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex-shrink:0;}
.an-copy{flex:1;min-width:0;}
.an-copy strong{display:block;font-size:13px;color:var(--text1);margin-bottom:4px;}
.an-copy p{margin:0;font-size:12px;color:var(--text2);line-height:1.5;}
.an-meta{display:flex;gap:10px;flex-wrap:wrap;margin-top:6px;font-size:10.5px;color:var(--text3);}
@media(max-width:980px){.an-grid,.an-fields,.an-stats{grid-template-columns:1fr;}}
</style>

<div class="an-shell">
    <div>
        <div class="an-title">Notifications</div>
        <div class="an-sub">Compose platform-wide alerts, tune delivery channels, and review the outbound notification stream in one workspace.</div>
    </div>

    <div class="an-stats">
        <div class="an-stat"><div class="lbl">Total Sent</div><div class="val"><?= number_format($stats['total'] ?? 0) ?></div></div>
        <div class="an-stat"><div class="lbl">Unread</div><div class="val"><?= number_format($stats['unread'] ?? 0) ?></div></div>
        <div class="an-stat"><div class="lbl">SMS Today</div><div class="val"><?= number_format($stats['sms_today'] ?? 0) ?></div></div>
    </div>

    <div class="an-grid">
        <div class="an-card">
            <div class="an-head">
                <strong>Compose Broadcast</strong>
                <span>Send a targeted announcement to users, customers, or providers.</span>
            </div>
            <div class="an-body">
                <form method="post" action="<?= Url::to(['/admin/notifications']) ?>">
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                    <div class="an-fields">
                        <div class="an-field">
                            <label>Recipient</label>
                            <select name="recipient">
                                <option value="all_users">All users</option>
                                <option value="all_vendors">All vendors</option>
                                <option value="all_customers">All customers</option>
                            </select>
                        </div>
                        <div class="an-field">
                            <label>Channel</label>
                            <select name="channel">
                                <option value="in_app">In-app</option>
                                <option value="sms">SMS</option>
                                <option value="email">Email</option>
                                <option value="all_channels">All channels</option>
                            </select>
                        </div>
                        <div class="an-field full">
                            <label>Title</label>
                            <input type="text" name="title" placeholder="System announcement title">
                        </div>
                        <div class="an-field full">
                            <label>Message</label>
                            <textarea name="message" placeholder="Write the customer-facing message here"></textarea>
                        </div>
                    </div>
                    <div style="display:flex;justify-content:flex-end;gap:8px;flex-wrap:wrap;margin-top:12px;">
                        <a href="<?= Url::to(['/admin/notifications/mark-all-read']) ?>" class="hl-btn-g">Mark All Read</a>
                        <button type="submit" class="hl-btn-p">Send Notification</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="an-card">
            <div class="an-head">
                <strong>Notification Log</strong>
                <span>Latest outbound notifications with delivery hints.</span>
            </div>
            <div class="an-list">
                <?php if ($dataProvider->getCount() === 0): ?>
                    <div class="an-body" style="color:var(--text3);font-size:12px;">No notifications have been sent yet.</div>
                <?php endif; ?>
                <?php foreach ($dataProvider->getModels() as $notification): ?>
                    <div class="an-item">
                        <div class="an-icon"><?= Html::encode(strtoupper(substr($notification->title ?: 'N', 0, 1))) ?></div>
                        <div class="an-copy">
                            <strong><?= Html::encode($notification->title ?: 'Notification') ?></strong>
                            <p><?= Html::encode($notification->message ?: '') ?></p>
                            <div class="an-meta">
                                <span><?= Html::encode($notification->type ?: 'general') ?></span>
                                <span><?= date('d M Y · H:i', strtotime($notification->created_at ?: 'now')) ?></span>
                                <span><?= $notification->sent_via_sms ? 'SMS' : 'In-app' ?><?= $notification->sent_via_email ? ' + Email' : '' ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
