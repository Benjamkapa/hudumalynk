<?php
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
.dash-greeting { margin-bottom: 20px; }
.dash-greeting h1 { font-size: 22px; font-weight: 800; color: var(--text1); letter-spacing: -.03em; }
.dash-greeting p  { font-size: 12px; color: var(--text3); margin-top: 3px; }

/* ── Hero Cards ── */
.hero-cards { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 12px; margin-bottom: 16px; }
.hero-card  { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 18px 20px 14px; }
.hero-card .hc-label { font-size: 10px; color: var(--text3); margin-bottom: 6px; text-transform: uppercase; letter-spacing: .1em; font-weight: 700; }
.hero-card .hc-value { font-size: 28px; font-weight: 800; letter-spacing: -.04em; line-height: 1.1; margin-bottom: 5px; }
.hero-card .hc-value.teal   { color: var(--teal); }
.hero-card .hc-value.purple { color: var(--acc2); }
.hero-card .hc-sub  { font-size: 11.5px; color: var(--text3); margin-bottom: 18px; }
.hero-card .hc-link { font-size: 12px; font-weight: 600; color: var(--acc); display: inline-block; }
.hero-card .hc-link:hover { text-decoration: underline; }
.arrow-up { color: var(--teal); font-size: 18px; }

/* Plan ring */
.plan-ring-wrap { display: flex; align-items: center; justify-content: center; position: relative; margin: 4px 0 10px; }
.plan-ring-wrap svg { width: 100px; height: 100px; }
.plan-ring-val  { position: absolute; font-size: 13px; font-weight: 800; color: var(--text1); text-align: center; line-height: 1.2; }

/* ── Mid row ── */
.mid-row { display: grid; grid-template-columns: minmax(0,1.6fr) minmax(0,1fr); gap: 12px; margin-bottom: 16px; }

/* Orders table */
.ord-row { display: flex; align-items: center; gap: 11px; padding: 9px 10px; border-radius: var(--r-md); cursor: pointer; transition: background .13s; border-bottom: 1px solid var(--border); }
.ord-row:last-child { border-bottom: none; }
.ord-row:hover { background: var(--bg); }
.ord-ref  { font-size: 12px; font-weight: 700; color: var(--text1); }
.ord-cust { font-size: 11px; color: var(--text3); }
.ord-amt  { margin-left: auto; font-size: 13px; font-weight: 700; color: var(--text1); white-space: nowrap; }
.ord-badge { display: inline-block; padding: 3px 9px; border-radius: 20px; font-size: 10px; font-weight: 700; }
.ord-badge.completed { background: var(--teal-pale); color: var(--teal); }
.ord-badge.pending   { background: var(--amber-pale); color: #7a5500; }
[data-hl-t="dark"] .ord-badge.pending { color: var(--amber); }
.ord-badge.cancelled { background: var(--rose-pale); color: var(--rose); }
.view-all-link { display: block; text-align: center; font-size: 12px; font-weight: 600; color: var(--acc); margin-top: 10px; }
.view-all-link:hover { text-decoration: underline; }

/* ── Mini stats ── */
.mini-stats { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 12px; margin-bottom: 16px; }
.mini-stat  { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 14px 16px; }
.ms-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: var(--text3); margin-bottom: 5px; }
.ms-val   { font-size: 22px; font-weight: 800; letter-spacing: -.03em; }
.ms-val.teal   { color: var(--teal); }
.ms-val.purple { color: var(--acc2); }
.ms-val.amber  { color: #FDCB6E; }
.ms-sub { font-size: 11px; color: var(--text3); margin-top: 3px; }
.star-row { display: flex; gap: 2px; margin-top: 4px; }
.star-ico { width: 13px; height: 13px; fill: #FDCB6E; }

/* ── Bottom row ── */
.bot-row { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 12px; }

/* Reviews */
.review-item { display: flex; gap: 10px; padding: 10px 0; border-bottom: 1px solid var(--border); }
.review-item:last-child { border-bottom: none; }
.rev-avatar { width: 34px; height: 34px; border-radius: var(--r-md); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; color: #fff; flex-shrink: 0; }
.rev-name  { font-size: 12.5px; font-weight: 600; color: var(--text1); }
.rev-date  { font-size: 10px; color: var(--text3); margin-top: 1px; }
.rev-comment { font-size: 12px; color: var(--text2); margin-top: 4px; line-height: 1.45; font-style: italic; }

/* Quick actions */
.qa-btn { display: flex; align-items: center; gap: 10px; padding: 11px 13px; border-radius: var(--r-md); border: 1px solid var(--border); background: var(--bg); cursor: pointer; margin-bottom: 8px; transition: background .13s, border-color .13s; text-decoration: none; }
.qa-btn:hover { background: var(--bg3); border-color: var(--acc); }
.qa-icon { width: 30px; height: 30px; border-radius: var(--r-sm); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.qa-icon svg { width: 14px; height: 14px; }
.qa-label { font-size: 12.5px; font-weight: 600; color: var(--text1); }
.qa-sub   { font-size: 10.5px; color: var(--text3); }
.qa-arr   { margin-left: auto; color: var(--text3); font-size: 14px; }

/* Bar chart */
.bar-wrap { display: flex; flex-direction: column; gap: 8px; margin-top: 4px; }
.bar-row  { display: flex; align-items: center; gap: 8px; font-size: 12px; }
.bar-lbl  { width: 65px; color: var(--text2); font-size: 11px; }
.bar-track { flex: 1; height: 7px; background: var(--bg3); border-radius: 4px; overflow: hidden; }
.bar-fill  { height: 100%; border-radius: 4px; }
.bar-count { width: 38px; text-align: right; color: var(--text3); font-size: 11px; }

@media (max-width: 900px) {
    .hero-cards { grid-template-columns: 1fr 1fr; }
    .mid-row    { grid-template-columns: 1fr; }
    .mini-stats { grid-template-columns: 1fr 1fr; }
    .bot-row    { grid-template-columns: 1fr; }
}
@media (max-width: 560px) {
    .hero-cards { grid-template-columns: 1fr; }
}
</style>

<?php if ($provider->isActive()): ?>

<?php
$sub       = $provider->activeSubscription;
$firstName = explode(' ', Yii::$app->user->identity->getFullName())[0];
$avatarColors = ['#6C5CE7','#3B82F6','#00B894','#E17055','#FDCB6E'];
$colorText    = ['#FDCB6E' => '#7a5c00'];

$daysLeft = 0;
$daysTotal = 30;
if ($sub) {
    $daysLeft  = max(0, (int)((strtotime($sub->end_date) - time()) / 86400));
    $daysTotal = 30;
}
$planPct    = $daysTotal > 0 ? min(100, round(($daysLeft / $daysTotal) * 100)) : 0;
$dashOffset = round(251.2 * (1 - $planPct / 100));
?>

<div class="dash-greeting">
    <h1>Good Morning, <?= Html::encode($firstName) ?></h1>
    <p>Home &rsaquo; Dashboard &mdash; <?= date('l, d F Y') ?></p>
</div>

<!-- ── Hero Cards ── -->
<div class="hero-cards">
    <div class="hero-card">
        <div class="hc-label">My Balance</div>
        <div class="hc-value teal">KES <?= number_format($stats['balance']) ?></div>
        <div class="hc-sub">Available for withdrawal</div>
        <a class="hc-link" href="<?= Url::to(['/provider/payouts']) ?>">Request payout →</a>
    </div>
    <div class="hero-card">
        <div class="hc-label">Total Orders</div>
        <div class="hc-value"><?= number_format($stats['total_orders']) ?> <span class="arrow-up">↗</span></div>
        <div class="hc-sub">2 new today</div>
        <a class="hc-link" href="<?= Url::to(['/provider/orders']) ?>">All orders →</a>
    </div>
    <div class="hero-card">
        <div class="hc-label">Active plan</div>
        <div class="plan-ring-wrap">
            <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="40" fill="none" stroke="rgba(108,92,231,.12)" stroke-width="9"/>
                <circle cx="50" cy="50" r="40" fill="none" stroke="#A29BFE" stroke-width="9"
                    stroke-dasharray="251.2" stroke-dashoffset="<?= $dashOffset ?>"
                    stroke-linecap="round" transform="rotate(-90 50 50)"/>
            </svg>
            <div class="plan-ring-val">
                <?= $sub ? Html::encode($sub->plan->name) : 'None' ?><br>
                <span style="font-size:10px;font-weight:400;color:var(--text3);"><?= $daysLeft ?>d left</span>
            </div>
        </div>
        <a class="hc-link" style="display:block;text-align:center;" href="<?= Url::to(['/provider/subscription']) ?>">Manage plan →</a>
    </div>
</div>

<!-- ── Mid Row ── -->
<div class="mid-row">
    <!-- Recent Orders -->
    <div class="hl-card">
        <div class="hl-card-head">
            <span class="hl-card-title">Recent Transactions</span>
            <a href="<?= Url::to(['/provider/orders']) ?>" class="hl-btn-p" style="font-size:11px;padding:6px 12px;">Show All</a>
        </div>
        <div class="hl-card-body" style="padding:6px 10px;">
            <?php foreach ($recentOrders as $i => $order):
                $statusClass = in_array($order->status, ['completed','pending','cancelled']) ? $order->status : 'pending';
            ?>
            <div class="ord-row">
                <div class="cust-avatar" style="background:<?= $avatarColors[$i % count($avatarColors)] ?>;width:32px;height:32px;font-size:10px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;flex-shrink:0;">
                    <?= strtoupper(substr($order->user->getFullName(), 0, 2)) ?>
                </div>
                <div>
                    <div class="ord-ref">ORD-<?= strtoupper($order->reference) ?></div>
                    <div class="ord-cust"><?= Html::encode($order->user->getFullName()) ?></div>
                </div>
                <div style="margin-left:auto;display:flex;flex-direction:column;align-items:flex-end;gap:4px;">
                    <span class="ord-amt">KES <?= number_format($order->total_amount) ?></span>
                    <span class="ord-badge <?= $statusClass ?>"><?= ucfirst($order->status) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <a class="view-all-link" href="<?= Url::to(['/provider/orders']) ?>">All transactions →</a>
    </div>

    <!-- Earnings Chart -->
    <div class="hl-card">
        <div class="hl-card-head">
            <span class="hl-card-title">Earnings trend</span>
            <span style="font-size:11.5px;color:var(--text3);cursor:pointer;">Monthly ⌄</span>
        </div>
        <div id="providerEarningsChart" style="height:260px;"></div>
    </div>
</div>

<!-- ── Mini Stats ── -->
<div class="mini-stats">
    <div class="mini-stat">
        <div class="ms-label">Active listings</div>
        <div class="ms-val purple"><?= number_format($stats['active_listings']) ?></div>
        <div class="ms-sub">published & visible</div>
    </div>
    <div class="mini-stat">
        <div class="ms-label">Success rating</div>
        <div class="ms-val amber"><?= number_format($stats['avg_rating'], 1) ?> / 5.0</div>
        <div class="star-row">
            <?php for ($s = 1; $s <= 5; $s++): ?>
            <svg class="star-ico" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
                 style="fill:<?= $s <= round($stats['avg_rating']) ? '#FDCB6E' : 'var(--bg3)' ?>">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
            <?php endfor; ?>
        </div>
    </div>
    <div class="mini-stat">
        <div class="ms-label">Subscription</div>
        <div class="ms-val teal"><?= $sub ? Html::encode($sub->plan->name) : 'No plan' ?></div>
        <div class="ms-sub">Expires <?= $sub ? date('d M Y', strtotime($sub->end_date)) : 'N/A' ?></div>
    </div>
</div>

<!-- ── Bottom Row ── -->
<div class="bot-row">
    <!-- Customer Reviews -->
    <div class="hl-card">
        <div class="hl-card-head"><span class="hl-card-title">Customer reviews</span></div>
        <div class="hl-card-body" style="padding-top:0;">
            <?php if (empty($recentReviews)): ?>
                <div style="text-align:center;padding:30px 0;color:var(--text3);font-size:13px;">No reviews yet.</div>
            <?php else: ?>
                <?php foreach (array_slice($recentReviews, 0, 3) as $i => $review):
                    $rn = $review->user->getFullName();
                    $ri = strtoupper(substr($rn,0,1).(strpos($rn,' ')!==false?substr($rn,strpos($rn,' ')+1,1):''));
                    $rb = $avatarColors[$i % count($avatarColors)];
                ?>
                <div class="review-item">
                    <div class="rev-avatar" style="background:<?= $rb ?>"><?= Html::encode($ri) ?></div>
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:6px;">
                            <span class="rev-name"><?= Html::encode($rn) ?></span>
                            <div style="display:flex;gap:2px;flex-shrink:0;">
                                <?php for ($s=1;$s<=5;$s++): ?>
                                <svg style="width:10px;height:10px;fill:<?= $s<=$review->rating?'#FDCB6E':'var(--bg3)' ?>" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="rev-date"><?= date('d M Y', strtotime($review->created_at)) ?></div>
                        <div class="rev-comment">"<?= Html::encode($review->comment) ?>"</div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Monthly Earnings Bar -->
    <div class="hl-card">
        <div class="hl-card-head"><span class="hl-card-title">Earnings by month</span></div>
        <div class="hl-card-body">
            <div class="bar-wrap">
                <?php
                $months   = $trends['months'] ?? [];
                $earnings = $trends['earnings'] ?? [];
                $maxEarn  = max(array_merge($earnings, [1]));
                $barColors = ['#6C5CE7','#00B894','#A29BFE','#FDCB6E','#3B82F6','#E17055'];
                foreach (array_slice(array_reverse($months), 0, 5) as $mi => $month):
                    $ei  = count($months) - 1 - $mi;
                    $val = $earnings[$ei] ?? 0;
                    $pct = $maxEarn > 0 ? round(($val / $maxEarn) * 100) : 0;
                    $clr = $barColors[$mi % count($barColors)];
                ?>
                <div class="bar-row">
                    <span class="bar-lbl"><?= Html::encode($month) ?></span>
                    <div class="bar-track"><div class="bar-fill" style="width:<?= $pct ?>%;background:<?= $clr ?>;"></div></div>
                    <span class="bar-count"><?= number_format($val/1000, 0) ?>K</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="hl-card">
        <div class="hl-card-head"><span class="hl-card-title">Quick actions</span></div>
        <div class="hl-card-body" style="padding-top:4px;">
            <a href="<?= Url::to(['/provider/listings/create']) ?>" class="qa-btn">
                <div class="qa-icon" style="background:var(--acc-pale);color:var(--acc);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </div>
                <div><div class="qa-label">Add listing</div><div class="qa-sub">Publish a new service</div></div>
                <span class="qa-arr">›</span>
            </a>
            <a href="<?= Url::to(['/provider/orders']) ?>" class="qa-btn">
                <div class="qa-icon" style="background:var(--amber-pale);color:#7a5c00;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                </div>
                <div><div class="qa-label">View orders</div><div class="qa-sub">Manage pending jobs</div></div>
                <span class="qa-arr">›</span>
            </a>
            <a href="<?= Url::to(['/provider/payouts']) ?>" class="qa-btn">
                <div class="qa-icon" style="background:var(--teal-pale);color:var(--teal);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <div><div class="qa-label">Request payout</div><div class="qa-sub">Withdraw via M-Pesa</div></div>
                <span class="qa-arr">›</span>
            </a>
            <a href="<?= Url::to(['/provider/profile']) ?>" class="qa-btn">
                <div class="qa-icon" style="background:var(--acc-pale2, rgba(108,92,231,.18));color:var(--acc2);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div><div class="qa-label">Edit profile</div><div class="qa-sub">Update business info</div></div>
                <span class="qa-arr">›</span>
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var isDark = document.documentElement.getAttribute('data-hl-t') === 'dark';
    var gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
    var tickColor = isDark ? '#55556A' : '#9898B8';

    new ApexCharts(document.querySelector("#providerEarningsChart"), {
        series: [{ name: 'Earnings (KES)', data: <?= json_encode($trends['earnings'] ?? []) ?> }],
        chart: { type: 'area', height: 260, toolbar: { show: false }, background: 'transparent', zoom: { enabled: false } },
        colors: ['#00B894'],
        stroke: { curve: 'smooth', width: 3 },
        fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0 } },
        dataLabels: { enabled: false },
        grid: { borderColor: gridColor, strokeDashArray: 4 },
        xaxis: {
            categories: <?= json_encode($trends['months'] ?? []) ?>,
            axisBorder: { show: false }, axisTicks: { show: false },
            labels: { style: { colors: tickColor, fontSize: '10px' } }
        },
        yaxis: {
            labels: {
                style: { colors: tickColor, fontSize: '10px' },
                formatter: function(v) { return 'KES ' + (v/1000).toFixed(0) + 'K'; }
            }
        },
        theme: { mode: isDark ? 'dark' : 'light' }
    }).render();
});
</script>

<?php else: ?>

<div class="hl-card" style="text-align:center;padding:60px 24px;">
    <div style="width:72px;height:72px;background:var(--acc-pale);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:20px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="var(--acc)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:32px;height:32px;">
            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
        </svg>
    </div>
    <h2 style="font-size:20px;font-weight:800;letter-spacing:-.03em;color:var(--text1);margin-bottom:10px;">Business Profile Under Review</h2>
    <p style="color:var(--text3);max-width:440px;margin:0 auto 28px;font-size:13px;line-height:1.6;">
        Our moderation team is reviewing your provider profile and identity documents.
        Once approved you will be notified via SMS and full access will be granted.
    </p>
    <a href="<?= Html::encode(Yii::$app->params['frontendUrl']) ?>" class="hl-btn-p">Return to Homepage</a>
</div>

<?php endif; ?>