<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $stats */
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Notifications';
?>
<style>
.pg-hero{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:11px;margin-bottom:16px;}
.pg-hcard{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:14px 15px;}
.pg-hc-lbl{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text3);margin-bottom:5px;}
.pg-hc-val{font-size:24px;font-weight:800;letter-spacing:-.04em;color:var(--text1);line-height:1;}
.pg-hc-sub{font-size:11px;color:var(--text3);margin-top:4px;}
.pg-hc-val.amber{color:#FDCB6E;}
.notif-list{display:flex;flex-direction:column;gap:0;}
.notif-item{display:flex;align-items:flex-start;gap:12px;padding:14px 16px;border-bottom:1px solid var(--border);cursor:pointer;transition:background .13s;position:relative;}
.notif-item:hover{background:var(--bg);}
.notif-item.unread{background:var(--acc-pale2,rgba(108,92,231,.07));}
.notif-item:last-child{border-bottom:none;}
.notif-icon{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.notif-icon svg{width:16px;height:16px;}
.notif-icon.order{background:var(--teal-pale);color:var(--teal);}
.notif-icon.vendor{background:var(--acc-pale);color:var(--acc);}
.notif-icon.payment{background:var(--amber-pale);color:#7a5500;}
.notif-icon.alert{background:var(--rose-pale);color:var(--rose);}
[data-hl-t="dark"] .notif-icon.payment{color:var(--amber);}
.notif-title{font-size:12.5px;font-weight:600;color:var(--text1);margin-bottom:2px;}
.notif-body{font-size:11.5px;color:var(--text2);line-height:1.4;}
.notif-time{font-size:10.5px;color:var(--text3);margin-top:4px;}
.notif-unread-dot{position:absolute;right:14px;top:50%;transform:translateY(-50%);width:7px;height:7px;border-radius:50%;background:var(--acc);}
@media(max-width:600px){.pg-hero{grid-template-columns:1fr;}}
</style>

<div class="hl-pg-head">
    <div>
        <h1 class="hl-pg-title">Notifications</h1>
        <div class="hl-pg-sub">Stay updated with platform activity</div>
    </div>
    <?php if (($stats['unread'] ?? 0) > 0): ?>
    <a href="<?= Url::to(['/provider/notifications/mark-all-read']) ?>" class="hl-btn-g">
        Mark all read (<?= $stats['unread'] ?>)
    </a>
    <?php endif; ?>
</div>

<div class="pg-hero">
    <div class="pg-hcard"><div class="pg-hc-lbl">Total</div><div class="pg-hc-val"><?= number_format($stats['total'] ?? 0) ?></div><div class="pg-hc-sub">All notifications</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Unread</div><div class="pg-hc-val amber"><?= number_format($stats['unread'] ?? 0) ?></div><div class="pg-hc-sub">New alerts</div></div>
</div>

<div class="hl-card">
    <div class="hl-card-head">
        <span class="hl-card-title">Recent activity</span>
        <span style="font-size:11px;color:var(--text3);">Showing recent <?= $dataProvider->pagination ? $dataProvider->pagination->pageSize : 15 ?> items</span>
    </div>
    <div class="notif-list">
    <?php
    $notifTypes = [
        ['icon'=>'order','svg'=>'<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>'],
        ['icon'=>'vendor','svg'=>'<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
        ['icon'=>'payment','svg'=>'<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'],
        ['icon'=>'alert','svg'=>'<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>'],
    ];
    foreach ($dataProvider->getModels() as $ni => $notif):
        $nt      = $notifTypes[$ni % count($notifTypes)];
        $isUnread = !$notif->is_read;
    ?>
    <div class="notif-item <?= $isUnread?'unread':'' ?>">
        <div class="notif-icon <?= $nt['icon'] ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?= $nt['svg'] ?></svg>
        </div>
        <div style="flex:1;min-width:0;">
            <div class="notif-title"><?= Html::encode($notif->title ?? 'Notification') ?></div>
            <div class="notif-body"><?= Html::encode($notif->message ?? '') ?></div>
            <div class="notif-time"><?= date('d M Y · H:i', strtotime($notif->created_at ?? 'now')) ?></div>
        </div>
        <?php if ($isUnread): ?><div class="notif-unread-dot"></div><?php endif; ?>
    </div>
    <?php endforeach; ?>
    <?php if (empty($dataProvider->models)): ?>
    <div style="padding:40px;text-align:center;color:var(--text3);">
        <div style="width:48px;height:48px;margin:0 auto 16px;background:var(--bg3);border-radius:50%;display:flex;align-items:center;justify-content:center;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        </div>
        <div style="font-size:14px;font-weight:500;margin-bottom:4px;">No notifications</div>
        <div style="font-size:12px;">You'll see updates here when there’s activity.</div>
    </div>
    <?php endif; ?>
    </div>
    <?php if ($dataProvider->pagination && $dataProvider->pagination->pageCount > 1): ?>
    <div style="display:flex;justify-content:center;padding:14px;gap:6px;">
        <?php for($p=1;$p<=$dataProvider->pagination->pageCount;$p++): ?>
        <a href="?page=<?= $p ?>" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:var(--r-md);border:1px solid var(--border);font-size:12px;font-weight:600;color:<?= $p==$dataProvider->pagination->page+1?'var(--acc)':'var(--text2)'?>;background:<?= $p==$dataProvider->pagination->page+1?'var(--acc-pale)':'var(--bg2)'?>;"><?= $p ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>
