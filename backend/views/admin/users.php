<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $stats */
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Users';
$currentRole = Yii::$app->request->get('role');
$currentQ    = Yii::$app->request->get('q');
$colors = ['#6C5CE7','#3B82F6','#00B894','#E17055','#FDCB6E','#A29BFE'];
?>
<style>
.pg-hero{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:11px;margin-bottom:16px;}
.pg-hcard{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:14px 15px;}
.pg-hc-lbl{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text3);margin-bottom:5px;}
.pg-hc-val{font-size:24px;font-weight:800;letter-spacing:-.04em;color:var(--text1);line-height:1;}
.pg-hc-sub{font-size:11px;color:var(--text3);margin-top:4px;}
.pg-hc-val.teal{color:var(--teal);}.pg-hc-val.purple{color:var(--acc2);}.pg-hc-val.amber{color:#FDCB6E;}
.filter-row{display:flex;align-items:center;gap:8px;margin-bottom:14px;flex-wrap:wrap;}
.filter-pill{display:inline-flex;align-items:center;gap:5px;padding:6px 13px;border-radius:20px;font-size:11.5px;font-weight:600;border:1px solid var(--border);background:var(--bg2);color:var(--text2);transition:all .13s;text-decoration:none;}
.filter-pill:hover{border-color:var(--acc);color:var(--acc);}
.filter-pill.active{background:var(--acc-pale);border-color:var(--acc);color:var(--acc);}
.role-badge{display:inline-block;padding:2px 8px;border-radius:20px;font-size:9.5px;font-weight:700;}
.role-badge.customer{background:var(--acc-pale);color:var(--acc);}
.role-badge.provider{background:var(--teal-pale);color:var(--teal);}
.role-badge.admin{background:var(--rose-pale);color:var(--rose);}
.status-dot{width:7px;height:7px;border-radius:50%;display:inline-block;flex-shrink:0;}
.action-form{display:inline;}
@media(max-width:900px){.pg-hero{grid-template-columns:1fr 1fr;}}
</style>

<div class="hl-pg-head">
    <div>
        <h1 class="hl-pg-title">Users</h1>
        <div class="hl-pg-sub">Manage all registered platform users</div>
    </div>
    <a href="<?= Url::to(['/admin/user-edit', 'id' => 0]) ?>" class="hl-btn-p offcanvas-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add User
    </a>
</div>

<div class="pg-hero">
    <div class="pg-hcard"><div class="pg-hc-lbl">Total users</div><div class="pg-hc-val"><?= number_format($stats['total'] ?? 0) ?></div><div class="pg-hc-sub">Registered accounts</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Customers</div><div class="pg-hc-val teal"><?= number_format($stats['customers'] ?? 0) ?></div><div class="pg-hc-sub">Buyers on platform</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">Providers</div><div class="pg-hc-val purple"><?= number_format($stats['providers'] ?? 0) ?></div><div class="pg-hc-sub">Vendor accounts</div></div>
    <div class="pg-hcard"><div class="pg-hc-lbl">New this week</div><div class="pg-hc-val amber"><?= number_format($stats['new_week'] ?? 0) ?></div><div class="pg-hc-sub">Joined last 7 days</div></div>
</div>

<div class="filter-row">
    <a href="<?= Url::to(['/admin/users']) ?>"                            class="filter-pill <?= !$currentRole ? 'active' : '' ?>">All users</a>
    <a href="<?= Url::to(['/admin/users', 'role' => 'customer']) ?>"     class="filter-pill <?= $currentRole === 'customer' ? 'active' : '' ?>">Customers</a>
    <a href="<?= Url::to(['/admin/users', 'role' => 'provider']) ?>"     class="filter-pill <?= $currentRole === 'provider' ? 'active' : '' ?>">Providers</a>
    <a href="<?= Url::to(['/admin/users', 'role' => 'admin']) ?>"        class="filter-pill <?= $currentRole === 'admin'    ? 'active' : '' ?>">Admins</a>
    <div style="margin-left:auto;">
        <form method="get" action="<?= Url::to(['/admin/users']) ?>" style="display:flex;gap:8px;">
            <?php if ($currentRole): ?><input type="hidden" name="role" value="<?= Html::encode($currentRole) ?>"><?php endif; ?>
            <div class="hl-search-wrap" style="margin:0;max-width:240px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;color:var(--text3);flex-shrink:0;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input class="hl-search-input" type="text" name="q" value="<?= Html::encode($currentQ) ?>" placeholder="Name, email or phone…">
            </div>
        </form>
    </div>
</div>

<div class="hl-card">
    <div class="hl-card-head">
        <span class="hl-card-title">All users</span>
        <span style="font-size:11px;color:var(--text3);"><?= number_format($dataProvider->totalCount) ?> total</span>
    </div>
    <table class="hl-tbl">
        <thead><tr>
            <th>User</th><th>Phone</th><th>Role</th><th>Status</th><th>Joined</th><th></th>
        </tr></thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $i => $user):
            $bg     = $colors[$i % count($colors)];
            $name   = $user->getFullName();
            $init   = strtoupper(substr($user->first_name ?? 'U', 0, 1) . substr($user->last_name ?? '', 0, 1));
            $isActive = $user->status !== 'suspended';
        ?>
        <tr>
            <td>
                <div style="display:flex;align-items:center;gap:9px;">
                    <div style="width:34px;height:34px;border-radius:50%;background:<?= $bg ?>;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;color:#fff;flex-shrink:0;"><?= Html::encode($init) ?></div>
                    <div>
                        <div style="font-size:12.5px;font-weight:600;color:var(--text1);"><?= Html::encode($name) ?></div>
                        <div style="font-size:10.5px;color:var(--text3);"><?= Html::encode($user->email ?? '—') ?></div>
                    </div>
                </div>
            </td>
            <td style="font-size:12px;color:var(--text2);font-family:monospace;"><?= Html::encode($user->phone ?? '—') ?></td>
            <td><span class="role-badge <?= Html::encode($user->role ?? 'customer') ?>"><?= ucfirst($user->role ?? 'customer') ?></span></td>
            <td>
                <div style="display:flex;align-items:center;gap:6px;">
                    <span class="status-dot" style="background:<?= $isActive ? 'var(--teal)' : 'var(--rose)' ?>;"></span>
                    <span style="font-size:12px;color:var(--text2);"><?= $isActive ? 'Active' : 'Suspended' ?></span>
                </div>
            </td>
            <td style="font-size:11.5px;color:var(--text3);"><?= date('d M Y', strtotime($user->created_at ?? 'now')) ?></td>
            <td>
                <div style="display:flex;gap:5px;">
                    <a href="<?= Url::to(['/admin/user-view', 'id' => $user->id]) ?>" class="hl-btn-g offcanvas-link" style="padding:5px 9px;font-size:11px;">View</a>
                    <a href="<?= Url::to(['/admin/user-edit', 'id' => $user->id]) ?>" class="hl-btn-g offcanvas-link" style="padding:5px 9px;font-size:11px;">Edit</a>
                    <?php if ($user->id !== Yii::$app->user->id): ?>
                    <form class="action-form" method="post" action="<?= Url::to(['/admin/user-suspend', 'id' => $user->id]) ?>" onsubmit="return confirm('<?= $isActive ? 'Suspend' : 'Re-activate' ?> this user?')">
                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                        <button type="submit" class="hl-btn-g" style="padding:5px 9px;font-size:11px;<?= $isActive ? 'color:var(--rose);border-color:var(--rose-pale);' : 'color:var(--teal);border-color:var(--teal-pale);' ?>">
                            <?= $isActive ? 'Suspend' : 'Activate' ?>
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if ($dataProvider->totalCount === 0): ?>
        <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--text3);font-size:12px;">No users found</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <?php if ($dataProvider->pagination->pageCount > 1): ?>
    <div style="display:flex;justify-content:center;padding:14px;gap:6px;">
        <?php for ($p = 1; $p <= $dataProvider->pagination->pageCount; $p++):
            $active = $p === $dataProvider->pagination->page + 1;
            $params = array_merge(Yii::$app->request->queryParams, ['page' => $p]);
        ?>
        <a href="?<?= http_build_query($params) ?>" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:var(--r-md);border:1px solid <?= $active?'var(--acc)':'var(--border)'?>;font-size:12px;font-weight:600;color:<?= $active?'var(--acc)':'var(--text2)'?>;background:<?= $active?'var(--acc-pale)':'var(--bg2)'?>;text-decoration:none;"><?= $p ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>