<?php
/** @var yii\web\View $this */
/** @var common\models\Provider $provider */
/** @var array $stats */
/** @var array $trends */
/** @var array $recentOrders */
/** @var array $recentReviews */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Dashboard';
?>

<style>
.hero-cards { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 12px; margin-bottom: 16px; }
.hero-card { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 18px 20px 14px; }
.hero-card .hc-label { font-size: 11px; color: var(--text3); text-transform: uppercase; letter-spacing: .07em; font-weight: 600; margin-bottom: 6px; }
.hero-card .hc-value { font-size: 32px; font-weight: 800; letter-spacing: -.04em; line-height: 1.1; margin-bottom: 5px; color: var(--text1); }
.hero-card .hc-sub { font-size: 12px; color: var(--text3); margin-bottom: 12px; }
.hero-card .hc-link { font-size: 12px; font-weight: 600; color: var(--acc); text-decoration: none; }
.hero-card .hc-link:hover { text-decoration: underline; }
.mid-row { display: grid; grid-template-columns: 1.5fr 1fr; gap: 12px; margin-bottom: 16px; }
#earningsChart { width: 100%; height: 200px; }
.order-item { display: flex; align-items: center; gap: 11px; padding: 9px 0; border-bottom: 1px solid var(--border); }
.order-item:last-child { border-bottom: none; }
.order-avatar { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; color: #fff; flex-shrink: 0; }
.order-name { font-size: 13px; font-weight: 600; color: var(--text1); }
.order-details { font-size: 11px; color: var(--text3); }
.order-status { margin-left: auto; font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 12px; }
.bot-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.review-item { padding: 12px 0; border-bottom: 1px solid var(--border); }
.review-item:last-child { border-bottom: none; }
.review-header { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
.review-avatar { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 800; color: #fff; }
.review-name { font-size: 12px; font-weight: 600; color: var(--text1); }
.review-rating { font-size: 11px; color: var(--text3); margin-left: auto; }
.review-text { font-size: 12px; color: var(--text2); line-height: 1.4; }
.history-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--border); }
.history-item:last-child { border-bottom: none; }
.history-label { font-size: 12px; color: var(--text2); }
.history-value { font-size: 13px; font-weight: 600; color: var(--text1); }
@media (max-width: 900px) { .hero-cards { grid-template-columns: repeat(2, 1fr); } .mid-row { grid-template-columns: 1fr; } .bot-row { grid-template-columns: 1fr; } }
</style>

<div class="hl-pg-head">
    <h1 class="hl-pg-title">Welcome back, <?= Html::encode($provider->business_name) ?></h1>
    <div class="hl-pg-sub"><?= date('l, d F Y') ?></div>
</div>

<div class="hero-cards">
    <div class="hero-card"><div class="hc-label">Total Earnings</div><div class="hc-value">KSh <?= number_format($stats['balance'], 0) ?></div><div class="hc-sub">Paid orders</div><a class="hc-link" href="<?= Url::to(['/provider/orders']) ?>">View all →</a></div>
    <div class="hero-card"><div class="hc-label">Active Listings</div><div class="hc-value"><?= $stats['active_listings'] ?></div><div class="hc-sub">Published</div><a class="hc-link" href="<?= Url::to(['/provider/listings']) ?>">Manage →</a></div>
    <div class="hero-card"><div class="hc-label">Total Orders</div><div class="hc-value"><?= $stats['total_orders'] ?></div><div class="hc-sub">All time</div><a class="hc-link" href="<?= Url::to(['/provider/orders']) ?>">View →</a></div>
    <div class="hero-card"><div class="hc-label">Average Rating</div><div class="hc-value">★ <?= number_format($stats['avg_rating'], 1) ?></div><div class="hc-sub">Feedback</div><a class="hc-link" href="<?= Url::to(['/provider/reviews']) ?>">Reviews →</a></div>
</div>

<div class="mid-row">
    <div class="hl-card">
        <div class="hl-card-head"><span class="hl-card-title">Earnings (Last 6 Months)</span></div>
        <div class="hl-card-body"><canvas id="earningsChart"></canvas></div>
    </div>
    <div class="hl-card">
        <div class="hl-card-head"><span class="hl-card-title">Recent Orders</span></div>
        <div class="hl-card-body" style="padding: 8px 16px;">
            <?php if ($recentOrders): ?>
                <?php foreach ($recentOrders as $order): ?>
                    <div class="order-item">
                        <div class="order-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"><?= strtoupper(substr($order->user->getFullName(), 0, 1)) ?></div>
                        <div><div class="order-name"><?= Html::encode($order->user->getFullName()) ?></div><div class="order-details">KSh <?= number_format($order->total_amount, 0) ?> • <?= date('M d', strtotime($order->created_at)) ?></div></div>
                        <div class="order-status" style="background: rgba(34,197,94,.1); color: #22c55e;"><?= ucfirst(str_replace('_', ' ', $order->status)) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: var(--text3); font-size: 12px;">No recent orders</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="bot-row">
    <div class="hl-card">
        <div class="hl-card-head"><span class="hl-card-title">Recent Reviews</span></div>
        <div class="hl-card-body" style="padding: 8px 16px;">
            <?php if ($recentReviews): ?>
                <?php foreach ($recentReviews as $review): ?>
                    <div class="review-item">
                        <div class="review-header">
                            <div class="review-avatar" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);"><?= strtoupper(substr($review->user->getFullName(), 0, 1)) ?></div>
                            <div class="review-name"><?= Html::encode($review->user->getFullName()) ?></div>
                            <div class="review-rating">★ <?= $review->rating ?>/5</div>
                        </div>
                        <div class="review-text"><?= Html::encode($review->comment) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: var(--text3); font-size: 12px;">No reviews yet</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="hl-card">
        <div class="hl-card-head"><span class="hl-card-title">Quick Stats</span></div>
        <div class="hl-card-body" style="padding: 8px 16px;">
            <div class="history-item"><span class="history-label">This Month</span><span class="history-value">KSh <?= number_format($trends['earnings'][5] ?? 0, 0) ?></span></div>
            <div class="history-item"><span class="history-label">Pending</span><span class="history-value"><?php echo \common\models\Order::find()->where(['provider_id' => $provider->id, 'status' => 'pending'])->count(); ?></span></div>
            <div class="history-item"><span class="history-label">Completed</span><span class="history-value"><?php echo \common\models\Order::find()->where(['provider_id' => $provider->id, 'status' => 'completed'])->count(); ?></span></div>
            <div class="history-item"><span class="history-label">Subscription</span><span class="history-value"><?php echo \common\models\Subscription::find()->where(['provider_id' => $provider->id, 'status' => 'active'])->one() ? 'Active' : 'Inactive'; ?></span></div>
        </div>
    </div>
</div>

<?php $this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js'); ?>
<?php $this->registerJs("setTimeout(function(){const ctx=document.getElementById('earningsChart');if(ctx&&typeof Chart!=='undefined'){new Chart(ctx,{type:'line',data:{labels:".json_encode($trends['months']).",datasets:[{label:'Earnings (KSh)',data:".json_encode($trends['earnings']).",borderColor:'#6C5CE7',backgroundColor:'rgba(108, 92, 231, 0.1)',tension:0.4,fill:true,pointRadius:4,pointBackgroundColor:'#6C5CE7'}]},options:{responsive:true,maintainAspectRatio:false,scales:{y:{beginAtZero:true,ticks:{callback:function(v){return'KSh '+v.toLocaleString()}}}},plugins:{legend:{display:false}}}});}},200);", \yii\web\View::POS_READY); ?>

