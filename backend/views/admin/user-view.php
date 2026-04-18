<?php
/** @var yii\web\View $this */
/** @var common\models\User $user */
/** @var common\models\Order[] $orders */
/** @var common\models\Review[] $reviews */
/** @var common\models\Provider|null $provider */
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'User: ' . $user->getFullName();
$colors = ['#6C5CE7','#3B82F6','#00B894','#E17055','#FDCB6E','#A29BFE'];
$roleCols = ['customer' => '#3B82F6', 'provider' => '#00B894', 'admin' => '#EF4444'];
$roleCol  = $roleCols[$user->role ?? 'customer'] ?? '#3B82F6';
$init     = strtoupper(substr($user->first_name ?? 'U', 0, 1) . substr($user->last_name ?? '', 0, 1));
$lastActivityAt = $user->hasAttribute('last_login_at') ? $user->getAttribute('last_login_at') : ($user->updated_at ?? null);
?>
<style>
.uv-grid{display:grid;grid-template-columns:280px 1fr;gap:14px;align-items:start;}
.uv-profile{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:20px;text-align:center;}
.uv-avatar{width:68px;height:68px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;color:#fff;margin:0 auto 12px;}
.uv-row{display:flex;justify-content:space-between;align-items:center;font-size:12px;padding:5px 0;border-bottom:1px solid var(--border);}
.uv-row:last-child{border-bottom:none;}
.uv-row .lbl{color:var(--text3);}
.uv-row .val{color:var(--text1);font-weight:600;text-align:right;max-width:60%;}
@media(max-width:768px){.uv-grid{grid-template-columns:1fr;}}
</style>

<div class="hl-pg-head">
    <div style="display:flex;align-items:center;gap:10px;">
        <a href="<?= Url::to(['/admin/users']) ?>" class="hl-btn-g" style="padding:6px 10px;font-size:11px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;"><polyline points="15 18 9 12 15 6"/></svg> Users
        </a>
        <h1 class="hl-pg-title"><?= Html::encode($user->getFullName()) ?></h1>
        <span style="display:inline-block;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;background:rgba(<?= $roleCol ?>,0.12);color:<?= $roleCol ?>;"><?= ucfirst($user->role ?? 'customer') ?></span>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="<?= Url::to(['/admin/user-edit', 'id' => $user->id]) ?>" class="hl-btn-g offcanvas-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit
        </a>
        <?php if ($user->id !== Yii::$app->user->id): ?>
        <form method="post" action="<?= Url::to(['/admin/user-suspend', 'id' => $user->id]) ?>" style="display:inline;" onsubmit="return confirm('<?= $user->status === 'suspended' ? 'Re-activate' : 'Suspend' ?> this user?')">
            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
            <button type="submit" class="hl-btn-g" style="color:<?= $user->status === 'suspended' ? 'var(--teal)' : 'var(--rose)' ?>;border-color:<?= $user->status === 'suspended' ? 'var(--teal-pale)' : 'var(--rose-pale)' ?>;">
                <?= $user->status === 'suspended' ? 'Re-activate' : 'Suspend' ?>
            </button>
        </form>
        <?php endif; ?>
    </div>
</div>

<div class="uv-grid">
    <div style="display:flex;flex-direction:column;gap:12px;">
        <div class="uv-profile">
            <div class="uv-avatar" style="background:<?= $roleCol ?>;"><?= Html::encode($init) ?></div>
            <div style="font-size:16px;font-weight:800;color:var(--text1);letter-spacing:-.02em;"><?= Html::encode($user->getFullName()) ?></div>
            <div style="font-size:11.5px;color:var(--text3);margin-top:3px;"><?= Html::encode($user->email ?? '—') ?></div>
            <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;background:<?= $user->status === 'suspended' ? 'var(--rose-pale)' : 'var(--teal-pale)' ?>;color:<?= $user->status === 'suspended' ? 'var(--rose)' : 'var(--teal)' ?>;margin-top:8px;">
                <?= ucfirst($user->status ?? 'active') ?>
            </span>
            <div style="text-align:left;margin-top:14px;">
                <div class="uv-row"><span class="lbl">Phone</span><span class="val"><?= Html::encode($user->phone ?? '—') ?></span></div>
                <div class="uv-row"><span class="lbl">Role</span><span class="val"><?= ucfirst($user->role ?? 'customer') ?></span></div>
                <div class="uv-row"><span class="lbl">Joined</span><span class="val"><?= date('d M Y', strtotime($user->created_at ?? 'now')) ?></span></div>
                <div class="uv-row"><span class="lbl">Last activity</span><span class="val"><?= $lastActivityAt ? date('d M Y', strtotime($lastActivityAt)) : 'Never' ?></span></div>
            </div>
        </div>

        <?php if ($provider): ?>
        <div class="hl-card">
            <div class="hl-card-head"><span class="hl-card-title">Vendor Profile</span></div>
            <div style="padding:14px;">
                <div style="font-size:13.5px;font-weight:700;color:var(--acc);margin-bottom:6px;"><?= Html::encode($provider->business_name) ?></div>
                <div style="font-size:11.5px;color:var(--text3);margin-bottom:4px;"><?= Html::encode($provider->city ?? '') ?> · Rating: <?= number_format($provider->rating, 1) ?> ★</div>
                <span style="display:inline-block;padding:2px 8px;border-radius:20px;font-size:9.5px;font-weight:700;background:var(--teal-pale);color:var(--teal);"><?= ucfirst($provider->status) ?></span>
                <div style="margin-top:10px;">
                    <a href="<?= Url::to(['/admin/vendor-view', 'id' => $provider->id]) ?>" class="hl-btn-g offcanvas-link" style="font-size:11px;padding:6px;justify-content:center;display:flex;">View vendor profile</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div style="display:flex;flex-direction:column;gap:12px;">
        <!-- Recent orders -->
        <div class="hl-card">
            <div class="hl-card-head">
                <span class="hl-card-title">Recent Orders</span>
                <a href="<?= Url::to(['/admin/orders']) ?>" class="hl-card-link">All orders →</a>
            </div>
            <?php if (empty($orders)): ?>
            <div style="padding:24px;text-align:center;color:var(--text3);font-size:12px;">No orders placed yet</div>
            <?php else: ?>
            <table class="hl-tbl">
                <thead><tr><th>Order</th><th>Vendor</th><th>Amount</th><th>Status</th><th>Date</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($orders as $i => $o):
                    $stCls = match($o->status) { 'completed' => 'paid', 'pending','awaiting_payment' => 'pend', default => 'new' };
                ?>
                <tr>
                    <td style="font-family:monospace;font-size:12px;font-weight:700;color:var(--text1);">#<?= sprintf('%06d', $o->id) ?></td>
                    <td style="font-size:12px;color:var(--text2);"><?= Html::encode($o->provider->business_name ?? '—') ?></td>
                    <td style="font-weight:700;font-size:12px;">KES <?= number_format($o->total_amount) ?></td>
                    <td><span class="hl-badge <?= $stCls ?>"><?= ucfirst(str_replace('_',' ',$o->status)) ?></span></td>
                    <td style="font-size:11px;color:var(--text3);"><?= date('d M Y', strtotime($o->created_at)) ?></td>
                    <td><a href="<?= Url::to(['/admin/order-view', 'id' => $o->id]) ?>" class="hl-btn-g offcanvas-link" style="padding:4px 9px;font-size:11px;">View</a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <!-- Reviews left by user -->
        <div class="hl-card">
            <div class="hl-card-head"><span class="hl-card-title">Reviews Written</span></div>
            <?php if (empty($reviews)): ?>
            <div style="padding:24px;text-align:center;color:var(--text3);font-size:12px;">No reviews written</div>
            <?php else: ?>
            <div>
            <?php foreach ($reviews as $i => $r):
                $rating = (int)($r->rating ?? 5);
            ?>
            <div style="display:flex;align-items:flex-start;gap:10px;padding:12px 15px;border-bottom:1px solid var(--border);">
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                        <span style="font-size:12.5px;font-weight:600;color:var(--text1);"><?= Html::encode($r->provider->business_name ?? '—') ?></span>
                        <span style="font-size:11px;color:#FDCB6E;"><?= str_repeat('★', $rating) ?><span style="color:var(--text3);"><?= str_repeat('★', 5 - $rating) ?></span></span>
                        <span style="font-size:10.5px;color:var(--text3);margin-left:auto;"><?= date('d M Y', strtotime($r->created_at)) ?></span>
                    </div>
                    <?php if ($r->comment): ?><div style="font-size:12px;color:var(--text2);font-style:italic;">"<?= Html::encode(mb_strimwidth($r->comment, 0, 120, '…')) ?>"</div><?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
