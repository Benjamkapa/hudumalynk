<?php
/** @var yii\web\View $this */
/** @var array $stats */
/** @var array $trends */
/** @var array $distribution */
/** @var array $latestUsers */
/** @var array $topProviders */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Dashboard';
?>

<style>
.dash-greeting { margin-bottom: 20px; }
.dash-greeting h1 { font-size: 22px; font-weight: 800; color: var(--text1); letter-spacing: -.03em; }
.dash-greeting p  { font-size: 12px; color: var(--text3); margin-top: 3px; }

/* ── Top 3 hero cards ── */
.hero-cards { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 12px; margin-bottom: 16px; }
.hero-card  { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 18px 20px 14px; }
.hero-card .hc-label { font-size: 11px; color: var(--text3); margin-bottom: 6px; text-transform: uppercase; letter-spacing: .07em; font-weight: 600; }
.hero-card .hc-value { font-size: 32px; font-weight: 800; letter-spacing: -.04em; line-height: 1.1; margin-bottom: 5px; display: flex; align-items: center; gap: 8px; }
.hero-card .hc-value .arrow-up { font-size: 20px; color: var(--teal); }
.hero-card .hc-sub  { font-size: 12px; color: var(--text3); margin-bottom: 18px; }
.hero-card .hc-link { font-size: 12px; font-weight: 600; color: var(--acc); }
.hero-card .hc-link:hover { text-decoration: underline; }
.goal-wrap { display: flex; align-items: center; justify-content: center; position: relative; margin: 4px 0 12px; }
.goal-wrap svg { width: 100px; height: 100px; }
.goal-val  { position: absolute; font-size: 22px; font-weight: 800; letter-spacing: -.04em; color: var(--text1); }

/* ── Middle row: customers + growth chart ── */
.mid-row { display: grid; grid-template-columns: minmax(0,1.5fr) minmax(0,1fr); gap: 12px; margin-bottom: 16px; }

/* Customer table */
.cust-row { display: flex; align-items: center; gap: 11px; padding: 9px 10px; border-radius: var(--r-md); cursor: pointer; transition: background .13s; }
.cust-row:hover  { background: var(--bg); }
.cust-row.active { background: var(--acc-pale2, rgba(108,92,231,.14)); }
.cust-row.active::before { content: none; }
.cust-avatar { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; color: #fff; flex-shrink: 0; }
.cust-name { font-size: 13px; font-weight: 600; color: var(--text1); }
.cust-biz  { font-size: 11px; color: var(--text3); }
.cust-actions { margin-left: auto; display: flex; gap: 5px; align-items: center; opacity: 0; transition: opacity .15s; }
.cust-row.active .cust-actions, .cust-row:hover .cust-actions { opacity: 1; }
.ca-btn { width: 28px; height: 28px; border-radius: 7px; background: var(--bg2); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text2); transition: background .13s; }
.ca-btn:hover { background: var(--bg3); }
.ca-btn svg { width: 12px; height: 12px; }
.view-all-link { display: block; text-align: center; font-size: 12px; font-weight: 600; color: var(--acc); margin-top: 12px; }
.view-all-link:hover { text-decoration: underline; }

/* ── Bottom mini-stats ── */
.mini-stats { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 12px; margin-bottom: 16px; }
.mini-stat  { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 14px 16px; }
.ms-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: var(--text3); margin-bottom: 6px; }
.ms-val   { font-size: 24px; font-weight: 800; letter-spacing: -.03em; line-height: 1.15; }
.ms-val.teal   { color: var(--teal); }
.ms-val.purple { color: var(--acc2); }
.ms-sub { font-size: 11px; color: var(--text3); margin-top: 3px; }

/* ── Bottom 3-col row ── */
.bot-row { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 12px; }

/* Chats */
.avatar-stack { display: flex; margin-bottom: 12px; }
.avatar-stack .cust-avatar { width: 30px; height: 30px; border: 2px solid var(--bg2); margin-left: -6px; font-size: 10px; }
.avatar-stack .cust-avatar:first-child { margin-left: 0; }
.chat-item { display: flex; align-items: center; gap: 9px; padding: 7px 0; border-bottom: 1px solid var(--border); }
.chat-item:last-child { border-bottom: none; }
.chat-name { font-size: 12px; font-weight: 600; color: var(--text1); }
.chat-preview { font-size: 11px; color: var(--text3); }
.chat-badge { margin-left: auto; background: var(--acc); color: #fff; font-size: 10px; font-weight: 700; border-radius: 10px; padding: 2px 7px; }

/* County bars */
.bar-wrap { display: flex; flex-direction: column; gap: 8px; margin-top: 4px; }
.bar-row  { display: flex; align-items: center; gap: 8px; font-size: 12px; }
.bar-lbl  { width: 68px; color: var(--text2); font-size: 11px; }
.bar-track { flex: 1; height: 7px; background: var(--bg3); border-radius: 4px; overflow: hidden; }
.bar-fill  { height: 100%; border-radius: 4px; }
.bar-count { width: 34px; text-align: right; color: var(--text3); font-size: 11px; }

/* New vendors */
.vendor-tags { display: flex; flex-wrap: wrap; gap: 7px; margin-top: 6px; }
.vendor-tag  { display: inline-flex; align-items: center; gap: 5px; background: var(--bg); border: 1px solid var(--border); border-radius: 20px; padding: 5px 11px; font-size: 11.5px; color: var(--text1); }
.vtag-plus   { width: 16px; height: 16px; border-radius: 50%; background: var(--acc-pale); color: var(--acc); display: flex; align-items: center; justify-content: center; font-size: 13px; line-height: 1; font-weight: 700; flex-shrink: 0; }

@media (max-width: 900px) {
    .hero-cards { grid-template-columns: 1fr; }
    .mid-row    { grid-template-columns: 1fr; }
    .mini-stats { grid-template-columns: 1fr 1fr; }
    .bot-row    { grid-template-columns: 1fr; }
}
</style>

<div class="dash-greeting">
    <h1>Good Morning, <?= Html::encode(explode(' ', Yii::$app->user->identity->getFullName())[0]) ?></h1>
    <p>Home &rsaquo; Dashboard &mdash; <?= date('l, d F Y') ?></p>
</div>

<!-- ── Hero Cards ── -->
<div class="hero-cards">
    <div class="hero-card">
        <div class="hc-label">Revenues</div>
        <div class="hc-value">15% <span class="arrow-up">↗</span></div>
        <div class="hc-sub">Increase compared to last week</div>
        <a class="hc-link" href="<?= Url::to(['/admin/earnings']) ?>">Revenues report →</a>
    </div>
    <div class="hero-card">
        <div class="hc-label">Lost deals</div>
        <div class="hc-value">4%</div>
        <div class="hc-sub">You closed 96 out of 100 deals</div>
        <a class="hc-link" href="<?= Url::to(['/admin/orders']) ?>">All deals →</a>
    </div>
    <div class="hero-card">
        <div class="hc-label">Quarter goal</div>
        <div class="goal-wrap">
            <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="40" fill="none" stroke="rgba(108,92,231,.12)" stroke-width="9"/>
                <circle cx="50" cy="50" r="40" fill="none" stroke="#A29BFE" stroke-width="9"
                    stroke-dasharray="251.2" stroke-dashoffset="40" stroke-linecap="round"
                    transform="rotate(-90 50 50)"/>
            </svg>
            <span class="goal-val">84%</span>
        </div>
        <a class="hc-link" style="display:block;text-align:center;" href="<?= Url::to(['/admin/analytics']) ?>">All goals →</a>
    </div>
</div>

<!-- ── Middle Row ── -->
<div class="mid-row">
    <!-- Customers list -->
    <div class="hl-card">
        <div class="hl-card-head">
            <span class="hl-card-title">Customers</span>
            <span style="font-size:11.5px; color:var(--text3); cursor:pointer;">Sort by Newest ⌄</span>
        </div>
        <div class="hl-card-body" style="padding:8px 10px;">
            <?php
            $avatarColors = ['#3B82F6','#6C5CE7','#00B894','#FDCB6E','#E17055'];
            $colorTextOverride = ['#FDCB6E' => '#7a5c00'];
            foreach (array_slice($latestUsers, 0, 4) as $i => $user):
                $name = $user->getFullName();
                $initials = strtoupper(substr($name, 0, 1) . (strpos($name, ' ') !== false ? substr($name, strpos($name, ' ')+1, 1) : ''));
                $bg = $avatarColors[$i % count($avatarColors)];
                $textColor = $colorTextOverride[$bg] ?? '#fff';
                $isActive = $i === 1 ? 'active' : '';
            ?>
            <div class="cust-row <?= $isActive ?>" onclick="this.closest('.hl-card-body').querySelectorAll('.cust-row').forEach(r=>r.classList.remove('active'));this.classList.add('active')">
                <div class="cust-avatar" style="background:<?= $bg ?>;color:<?= $textColor ?>"><?= Html::encode($initials) ?></div>
                <div>
                    <div class="cust-name"><?= Html::encode($name) ?></div>
                    <div class="cust-biz"><?= Html::encode($user->email) ?></div>
                </div>
                <div class="cust-actions">
                    <div class="ca-btn" title="Message">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <div class="ca-btn" title="Star">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </div>
                    <div class="ca-btn" title="Edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </div>
                    <div class="ca-btn" title="More">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <a class="view-all-link" href="<?= Url::to(['/admin/users']) ?>">All customers →</a>
    </div>

    <!-- Growth chart -->
    <div class="hl-card">
        <div class="hl-card-head">
            <span class="hl-card-title">Growth</span>
            <span style="font-size:11.5px; color:var(--text3); cursor:pointer;">Yearly ⌄</span>
        </div>
        <div id="growthChart" style="height:260px;"></div>
    </div>
</div>

<!-- ── Mini Stats ── -->
<div class="mini-stats">
    <div class="mini-stat">
        <div class="ms-label">Top month</div>
        <div class="ms-val teal">November<br>2025</div>
    </div>
    <div class="mini-stat">
        <div class="ms-label">Top year</div>
        <div class="ms-val purple">2025</div>
        <div class="ms-sub">96K sold so far</div>
    </div>
    <div class="mini-stat">
        <div class="ms-label">Top vendor</div>
        <?php if (!empty($topProviders[0])): ?>
        <div style="display:flex;align-items:center;gap:9px;margin-top:6px;">
            <div class="cust-avatar" style="background:#6C5CE7;width:34px;height:34px;font-size:11px;flex-shrink:0;">
                <?= strtoupper(substr($topProviders[0]['business_name'], 0, 2)) ?>
            </div>
            <div>
                <div style="font-size:13px;font-weight:600;color:var(--text1);"><?= Html::encode($topProviders[0]['business_name']) ?></div>
                <div style="font-size:11px;color:var(--text3);"><?= $topProviders[0]['order_count'] ?> orders</div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ── Bottom Row ── -->
<div class="bot-row">
    <!-- Chats -->
    <div class="hl-card">
        <div class="hl-card-head" style="flex-direction:column;align-items:flex-start;gap:2px;">
            <span class="hl-card-title">Chats</span>
            <span style="font-size:11px;color:var(--text3);">2 unread messages</span>
        </div>
        <div class="hl-card-body" style="padding-top:0;">
            <div class="avatar-stack">
                <?php foreach (array_slice($latestUsers, 0, 4) as $i => $u):
                    $n = $u->getFullName();
                    $init = strtoupper(substr($n,0,1).(strpos($n,' ')!==false?substr($n,strpos($n,' ')+1,1):''));
                    $bg = $avatarColors[$i % count($avatarColors)];
                    $tc = $colorTextOverride[$bg] ?? '#fff';
                ?>
                <div class="cust-avatar" style="background:<?= $bg ?>;color:<?= $tc ?>"><?= Html::encode($init) ?></div>
                <?php endforeach; ?>
            </div>
            <?php foreach (array_slice($latestUsers, 0, 2) as $i => $u):
                $n = $u->getFullName();
                $init = strtoupper(substr($n,0,1).(strpos($n,' ')!==false?substr($n,strpos($n,' ')+1,1):''));
                $bg = $avatarColors[$i % count($avatarColors)];
                $tc = $colorTextOverride[$bg] ?? '#fff';
            ?>
            <div class="chat-item">
                <div class="cust-avatar" style="background:<?= $bg ?>;color:<?= $tc ?>;width:28px;height:28px;font-size:10px;flex-shrink:0;"><?= Html::encode($init) ?></div>
                <div>
                    <div class="chat-name"><?= Html::encode($n) ?></div>
                    <div class="chat-preview"><?= Html::encode($u->email) ?></div>
                </div>
                <?php if ($i === 0): ?><span class="chat-badge">2</span><?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Top counties bar chart -->
    <div class="hl-card">
        <div class="hl-card-head"><span class="hl-card-title">Top counties</span></div>
        <div class="hl-card-body">
            <div class="bar-wrap">
                <?php
                $counties = [
                    ['Nairobi', 90, '120K', '#6C5CE7'],
                    ['Mombasa', 65, '80K',  '#00B894'],
                    ['Kisumu',  55, '70K',  '#A29BFE'],
                    ['Nakuru',  40, '50K',  '#FDCB6E'],
                ];
                foreach ($counties as [$lbl, $pct, $cnt, $clr]):
                ?>
                <div class="bar-row">
                    <span class="bar-lbl"><?= $lbl ?></span>
                    <div class="bar-track"><div class="bar-fill" style="width:<?= $pct ?>%;background:<?= $clr ?>;"></div></div>
                    <span class="bar-count"><?= $cnt ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- New vendors -->
    <div class="hl-card">
        <div class="hl-card-head"><span class="hl-card-title">New vendors</span></div>
        <div class="hl-card-body">
            <div class="vendor-tags">
                <?php foreach (array_slice($topProviders, 0, 6) as $p): ?>
                <div class="vendor-tag">
                    <span class="vtag-plus">+</span>
                    <?= Html::encode($p['business_name']) ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Charts JS -->
<?php $this->registerJsFile('https://cdn.jsdelivr.net/npm/apexcharts'); ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var isDark = document.documentElement.getAttribute('data-hl-t') === 'dark';
    var gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
    var tickColor = isDark ? '#55556A' : '#9898B8';

    var salesOptions = {
        series: [{ name: 'GMV (KES)', data: <?= json_encode($trends['sales'] ?? []) ?> }],
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
        yaxis: { labels: { style: { colors: tickColor, fontSize: '10px' }, formatter: v => (v/1000).toFixed(0)+'K' } },
        theme: { mode: isDark ? 'dark' : 'light' }
    };
    new ApexCharts(document.querySelector("#growthChart"), salesOptions).render();
});
</script>