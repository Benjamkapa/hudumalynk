<?php
/** @var yii\web\View $this */
/** @var common\models\Provider $provider */
/** @var common\models\Notification[] $notifications */
/** @var common\models\Order[] $recentOrders */
/** @var common\models\Review[] $recentReviews */
/** @var array $stats */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Messages';
?>

<style>
.pm-shell{display:flex;flex-direction:column;gap:16px;}
.pm-title{font-size:24px;font-weight:800;color:var(--text1);letter-spacing:-.03em;}
.pm-sub{font-size:12px;color:var(--text3);margin-top:4px;line-height:1.5;max-width:760px;}
.pm-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;}
.pm-stat{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:14px 15px;}
.pm-stat .lbl{font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--text3);margin-bottom:5px;}
.pm-stat .val{font-size:24px;font-weight:800;color:var(--text1);}
.pm-grid{display:grid;grid-template-columns:1.2fr .9fr;gap:16px;}
.pm-card{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden;}
.pm-head{padding:15px 16px;border-bottom:1px solid var(--border);}
.pm-head strong{font-size:14px;color:var(--text1);}
.pm-head span{display:block;font-size:11px;color:var(--text3);margin-top:3px;}
.pm-list{display:flex;flex-direction:column;}
.pm-item{display:flex;gap:11px;padding:14px 16px;border-bottom:1px solid var(--border);}
.pm-item:last-child{border-bottom:none;}
.pm-avatar{width:34px;height:34px;border-radius:50%;background:var(--acc);color:#fff;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;flex-shrink:0;}
.pm-copy{flex:1;min-width:0;}
.pm-copy strong{display:block;font-size:12.5px;color:var(--text1);margin-bottom:3px;}
.pm-copy p{font-size:11.5px;color:var(--text2);line-height:1.45;margin:0;}
.pm-copy .meta{font-size:10.5px;color:var(--text3);margin-top:5px;}
@media(max-width:900px){.pm-stats,.pm-grid{grid-template-columns:1fr;}}
</style>

<div class="pm-shell">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div>
            <div class="pm-title">Inbox & Activity</div>
            <div class="pm-sub">This workspace combines customer order activity, latest reviews, and system notices so your team can respond quickly even before a full chat module is added.</div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="<?= Url::to(['/provider/orders']) ?>" class="hl-btn-g">Open Orders</a>
            <a href="<?= Url::to(['/provider/notifications']) ?>" class="hl-btn-p">View Alerts</a>
        </div>
    </div>

    <div class="pm-stats">
        <div class="pm-stat"><div class="lbl">Active Threads</div><div class="val"><?= number_format($stats['conversations'] ?? 0) ?></div></div>
        <div class="pm-stat"><div class="lbl">Unread Alerts</div><div class="val"><?= number_format($stats['unread'] ?? 0) ?></div></div>
        <div class="pm-stat"><div class="lbl">Today</div><div class="val"><?= number_format($stats['today'] ?? 0) ?></div></div>
    </div>

    <div class="pm-grid">
        <div class="pm-card">
            <div class="pm-head">
                <strong>Order Conversations</strong>
                <span>Recent customers who need attention or follow-up.</span>
            </div>
            <div class="pm-list">
                <?php if (!$recentOrders): ?>
                    <div class="pm-item"><div class="pm-copy"><p>No order activity yet. When customers place orders, the latest interactions will appear here.</p></div></div>
                <?php endif; ?>
                <?php foreach ($recentOrders as $order): ?>
                    <div class="pm-item">
                        <div class="pm-avatar"><?= Html::encode(strtoupper(substr($order->user?->getFullName() ?: 'U', 0, 1))) ?></div>
                        <div class="pm-copy">
                            <strong><?= Html::encode($order->user?->getFullName() ?: 'Unknown customer') ?></strong>
                            <p>Order #<?= (int) $order->id ?> is currently <?= Html::encode(str_replace('_', ' ', $order->status)) ?> for KES <?= number_format((float) $order->total_amount, 0) ?>.</p>
                            <div class="meta"><?= date('d M Y · H:i', strtotime($order->created_at ?: 'now')) ?> · <a href="<?= Url::to(['/provider/order-view', 'id' => $order->id]) ?>" class="offcanvas-link">Open order</a></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="pm-card">
                <div class="pm-head">
                    <strong>Latest Reviews</strong>
                    <span>Quality signals from customers.</span>
                </div>
                <div class="pm-list">
                    <?php if (!$recentReviews): ?>
                        <div class="pm-item"><div class="pm-copy"><p>No reviews have been posted yet.</p></div></div>
                    <?php endif; ?>
                    <?php foreach ($recentReviews as $review): ?>
                        <div class="pm-item">
                            <div class="pm-avatar"><?= Html::encode(strtoupper(substr($review->user?->getFullName() ?: 'R', 0, 1))) ?></div>
                            <div class="pm-copy">
                                <strong><?= Html::encode($review->user?->getFullName() ?: 'Anonymous') ?></strong>
                                <p>Rated you <?= (int) $review->rating ?>/5. <?= Html::encode(mb_strimwidth((string) $review->comment, 0, 90, '...')) ?></p>
                                <div class="meta"><?= date('d M Y', strtotime($review->created_at ?: 'now')) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="pm-card">
                <div class="pm-head">
                    <strong>System Notices</strong>
                    <span>Newest operational alerts and reminders.</span>
                </div>
                <div class="pm-list">
                    <?php if (!$notifications): ?>
                        <div class="pm-item"><div class="pm-copy"><p>No recent system notices.</p></div></div>
                    <?php endif; ?>
                    <?php foreach ($notifications as $notification): ?>
                        <div class="pm-item">
                            <div class="pm-avatar"><?= Html::encode(strtoupper(substr($notification->title ?: 'N', 0, 1))) ?></div>
                            <div class="pm-copy">
                                <strong><?= Html::encode($notification->title ?: 'Notice') ?></strong>
                                <p><?= Html::encode(mb_strimwidth((string) $notification->message, 0, 110, '...')) ?></p>
                                <div class="meta"><?= date('d M Y · H:i', strtotime($notification->created_at ?: 'now')) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
