<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $stats */

use yii\helpers\Html;

$this->title = 'Messages';
?>

<style>
.am-shell{display:flex;flex-direction:column;gap:16px;}
.am-title{font-size:24px;font-weight:800;color:var(--text1);letter-spacing:-.03em;}
.am-sub{font-size:12px;color:var(--text3);margin-top:4px;line-height:1.5;}
.am-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;}
.am-stat{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:14px 15px;}
.am-stat .lbl{font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--text3);margin-bottom:5px;}
.am-stat .val{font-size:24px;font-weight:800;color:var(--text1);}
.am-grid{display:grid;grid-template-columns:.95fr 1.05fr;gap:16px;align-items:start;}
.am-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden;}
.am-head{padding:16px 18px;border-bottom:1px solid var(--border);}
.am-head strong{font-size:15px;color:var(--text1);}
.am-head span{display:block;font-size:11px;color:var(--text3);margin-top:4px;}
.am-list{display:flex;flex-direction:column;}
.am-item{display:flex;gap:12px;padding:15px 16px;border-bottom:1px solid var(--border);}
.am-item:last-child{border-bottom:none;}
.am-avatar{width:38px;height:38px;border-radius:50%;background:var(--acc);color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;flex-shrink:0;}
.am-copy{flex:1;min-width:0;}
.am-copy strong{display:block;font-size:13px;color:var(--text1);margin-bottom:4px;}
.am-copy p{margin:0;font-size:12px;color:var(--text2);line-height:1.5;}
.am-copy .meta{font-size:10.5px;color:var(--text3);margin-top:6px;}
.am-panel{padding:18px;display:flex;flex-direction:column;gap:12px;}
.am-chip{background:var(--bg);border:1px solid var(--border);border-radius:var(--r-md);padding:12px 13px;}
.am-chip strong{display:block;font-size:12px;color:var(--text1);margin-bottom:3px;}
.am-chip span{display:block;font-size:11px;color:var(--text3);line-height:1.45;}
@media(max-width:940px){.am-stats,.am-grid{grid-template-columns:1fr;}}
</style>

<div class="am-shell">
    <div>
        <div class="am-title">Messages</div>
        <div class="am-sub">High-level inbox for platform communication. This screen surfaces the latest user-facing messages and operational nudges until a full threaded chat system is introduced.</div>
    </div>

    <div class="am-stats">
        <div class="am-stat"><div class="lbl">Total Conversations</div><div class="val"><?= number_format($stats['total'] ?? 0) ?></div></div>
        <div class="am-stat"><div class="lbl">Unread Messages</div><div class="val"><?= number_format($stats['unread'] ?? 0) ?></div></div>
        <div class="am-stat"><div class="lbl">Active Today</div><div class="val"><?= number_format($stats['today'] ?? 0) ?></div></div>
    </div>

    <div class="am-grid">
        <div class="am-card">
            <div class="am-head">
                <strong>Recent Inbox Items</strong>
                <span>Newest message-like entries from the notification stream.</span>
            </div>
            <div class="am-list">
                <?php if ($dataProvider->getCount() === 0): ?>
                    <div class="am-panel" style="color:var(--text3);font-size:12px;">No message activity yet.</div>
                <?php endif; ?>
                <?php foreach ($dataProvider->getModels() as $message): ?>
                    <div class="am-item">
                        <div class="am-avatar"><?= Html::encode(strtoupper(substr($message->user?->getFullName() ?: $message->title ?: 'M', 0, 1))) ?></div>
                        <div class="am-copy">
                            <strong><?= Html::encode($message->title ?: 'Platform Message') ?></strong>
                            <p><?= Html::encode($message->message ?: '') ?></p>
                            <div class="meta">
                                <?= Html::encode($message->user?->getFullName() ?: 'System') ?> · <?= date('d M Y · H:i', strtotime($message->created_at ?: 'now')) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="am-card">
            <div class="am-head">
                <strong>Ops Playbook</strong>
                <span>Recommended admin follow-up patterns for message-heavy moments.</span>
            </div>
            <div class="am-panel">
                <div class="am-chip">
                    <strong>New provider onboarding</strong>
                    <span>Respond quickly to registration friction, document verification, and incomplete profiles.</span>
                </div>
                <div class="am-chip">
                    <strong>Order dispute handling</strong>
                    <span>Prioritise payment complaints and failed fulfilment cases before general chat.</span>
                </div>
                <div class="am-chip">
                    <strong>Announcement hygiene</strong>
                    <span>Use the notifications page for broad updates; reserve direct messages for escalations and manual intervention.</span>
                </div>
            </div>
        </div>
    </div>
</div>
