<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $stats */
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Messages';
?>
<div class="hl-stats-grid" style="grid-template-columns: repeat(3, minmax(0,1fr));">
    <div class="hl-scard">
        <div class="hl-scard-lbl">Total Conversations</div>
        <div class="hl-scard-val"><?= number_format($stats['total'] ?? 0) ?></div>
    </div>
    <div class="hl-scard">
        <div class="hl-scard-lbl">Unread Messages</div>
        <div class="hl-scard-val" style="color:var(--amber);"><?= number_format($stats['unread'] ?? 0) ?></div>
    </div>
    <div class="hl-scard">
        <div class="hl-scard-lbl">Active Today</div>
        <div class="hl-scard-val" style="color:var(--teal);"><?= number_format($stats['today'] ?? 0) ?></div>
    </div>
</div>

<div class="hl-card" style="margin-top:15px;">
    <div class="hl-card-head">
        <span class="hl-card-title">Inbox</span>
    </div>
    <div class="hl-card-body" style="text-align:center;padding:40px 20px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:40px;height:40px;color:var(--text3);margin-bottom:10px;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        <p style="color:var(--text2);font-size:13px;font-weight:600;">No active chats right now</p>
        <p style="color:var(--text3);font-size:11px;margin-top:5px;">Real-time messaging history will appear here once users interact.</p>
    </div>
</div>
