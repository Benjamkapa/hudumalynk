<?php
/** @var yii\web\View $this */
/** @var common\models\Provider[] $providers */
/** @var string $status */
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Approval Queue';

$svgCheck = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;"><polyline points="20 6 9 17 4 12"/></svg>';
$svgX     = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
$svgEye   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
$colors   = ['#6C5CE7','#3B82F6','#00B894','#E17055','#FDCB6E','#A29BFE'];
?>
<style>
.filter-row{display:flex;align-items:center;gap:8px;margin-bottom:14px;flex-wrap:wrap;}
.filter-pill{display:inline-flex;align-items:center;gap:5px;padding:6px 13px;border-radius:20px;font-size:11.5px;font-weight:600;border:1px solid var(--border);background:var(--bg2);color:var(--text2);cursor:pointer;transition:all .13s;text-decoration:none;}
.filter-pill:hover{border-color:var(--acc);color:var(--acc);}
.filter-pill.active{background:var(--acc-pale);border-color:var(--acc);color:var(--acc);}
.prov-badge{display:inline-block;padding:2px 8px;border-radius:20px;font-size:9.5px;font-weight:700;}
.prov-badge.active{background:var(--teal-pale);color:var(--teal);}
.prov-badge.pending{background:var(--amber-pale);color:#7a5500;}
.prov-badge.rejected{background:var(--rose-pale);color:var(--rose);}
.prov-badge.suspended{background:var(--bg3);color:var(--text3);}
[data-hl-t="dark"] .prov-badge.pending{color:var(--amber);}
.action-form{display:inline;}
.empty-state{text-align:center;padding:48px 0;color:var(--text3);}
</style>

<div class="hl-pg-head">
    <div>
        <h1 class="hl-pg-title">Provider Approval Queue</h1>
        <div class="hl-pg-sub">Review and approve new vendor applications</div>
    </div>
    <a href="<?= Url::to(['/admin/vendors']) ?>" class="hl-btn-g">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        All Vendors
    </a>
</div>

<div class="filter-row">
    <a href="<?= Url::to(['/admin/providers', 'status' => 'pending']) ?>"  class="filter-pill <?= $status === 'pending'  ? 'active' : '' ?>">Pending</a>
    <a href="<?= Url::to(['/admin/providers', 'status' => 'active']) ?>"   class="filter-pill <?= $status === 'active'   ? 'active' : '' ?>">Active</a>
    <a href="<?= Url::to(['/admin/providers', 'status' => 'rejected']) ?>" class="filter-pill <?= $status === 'rejected' ? 'active' : '' ?>">Rejected</a>
    <a href="<?= Url::to(['/admin/providers', 'status' => 'suspended']) ?>" class="filter-pill <?= $status === 'suspended'? 'active' : '' ?>">Suspended</a>
</div>

<div class="hl-card">
    <div class="hl-card-head">
        <span class="hl-card-title"><?= ucfirst($status) ?> providers</span>
        <span style="font-size:11px;color:var(--text3);"><?= count($providers) ?> total</span>
    </div>
    <?php if (empty($providers)): ?>
    <div class="empty-state">
        <div style="font-size:13px;font-weight:600;color:var(--text2);margin-bottom:4px;">No <?= $status ?> providers</div>
        <div style="font-size:11.5px;">Nothing to action right now.</div>
    </div>
    <?php else: ?>
    <table class="hl-tbl">
        <thead><tr>
            <th>Business</th>
            <th>Owner</th>
            <th>Phone</th>
            <th>City</th>
            <th>Joined</th>
            <th>Status</th>
            <th></th>
        </tr></thead>
        <tbody>
        <?php foreach ($providers as $i => $p):
            $bg   = $colors[$i % count($colors)];
            $init = strtoupper(substr($p->business_name, 0, 2));
        ?>
        <tr>
            <td>
                <div style="display:flex;align-items:center;gap:9px;">
                    <div style="width:34px;height:34px;border-radius:var(--r-md);background:<?= $p->logo ? "url('/uploads/logos/{$p->logo}')" : $bg ?>;background-size:cover;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#fff;flex-shrink:0;"><?= $p->logo ? '' : Html::encode($init) ?></div>
                    <div>
                        <div style="font-size:12.5px;font-weight:700;color:var(--text1);">
                            <?= Html::encode($p->business_name) ?>
                            <?php if ($p->is_verified): ?>
                                <span style="display:inline-block;width:14px;height:14px;background:var(--acc);border-radius:50%;font-size:8px;color:#fff;text-align:center;line-height:14px;margin-left:4px;">✓</span>
                            <?php endif; ?>
                        </div>
                        <div style="font-size:10.5px;color:var(--text3);"><?= Html::encode($p->description ? mb_strimwidth($p->description, 0, 40, '…') : 'No description') ?></div>
                    </div>
                </div>
            </td>
            <td style="font-size:12px;color:var(--text2);"><?= Html::encode($p->user->email ?? '—') ?></td>
            <td style="font-size:12px;color:var(--text2);font-family:monospace;"><?= Html::encode($p->phone ?: '—') ?></td>
            <td style="font-size:12px;color:var(--text2);"><?= Html::encode($p->city ?: '—') ?></td>
            <td style="font-size:11.5px;color:var(--text3);"><?= date('d M Y', strtotime($p->created_at)) ?></td>
            <td><span class="prov-badge <?= Html::encode($p->status) ?>"><?= ucfirst($p->status) ?></span></td>
            <td>
                <div style="display:flex;gap:5px;align-items:center;">
                    <a href="<?= Url::to(['/admin/vendor-view', 'id' => $p->id]) ?>" class="hl-btn-g" style="padding:5px 9px;font-size:11px;"><?= $svgEye ?> View</a>
                    <?php if ($p->status === 'pending'): ?>
                    <form class="action-form" method="post" action="<?= Url::to(['/admin/approve-provider', 'id' => $p->id]) ?>">
                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                        <button type="submit" class="hl-btn-p" style="padding:5px 10px;font-size:11px;background:var(--teal);gap:4px;" title="Approve">
                            <?= $svgCheck ?> Approve
                        </button>
                    </form>
                    <form class="action-form" method="post" action="<?= Url::to(['/admin/reject-provider', 'id' => $p->id]) ?>" onsubmit="return confirm('Reject this vendor application?')">
                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                        <button type="submit" class="hl-btn-g" style="padding:5px 10px;font-size:11px;color:var(--rose);border-color:var(--rose-pale);" title="Reject">
                            <?= $svgX ?> Reject
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
