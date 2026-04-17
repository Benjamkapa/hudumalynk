<?php
/** @var common\models\Provider $provider */
/** @var common\models\Listing[] $listings */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'My Catalogue';
?>

<div class="hl-pg-head">
    <div>
        <h1 class="hl-pg-title">My Catalogue</h1>
        <div class="hl-pg-sub">Manage your service and product listings</div>
    </div>
    <div style="display:flex;gap:10px;">
        <div class="hl-search-wrap" style="height:36px;margin:0;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input class="hl-search-input" type="text" placeholder="Search Listings...">
        </div>
        <a href="<?= Url::to(['/provider/create-listing']) ?>" class="hl-btn-p offcanvas-link" style="height:36px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add New
        </a>
    </div>
</div>

<div class="hl-card">
    <div class="hl-card-head">
        <span class="hl-card-title">All listings</span>
        <span style="font-size:11px;color:var(--text3);"><?= count($listings) ?> total</span>
    </div>
    <table class="hl-tbl">
        <thead>
            <tr>
                <th>Preview</th>
                <th>Name & Category</th>
                <th>Type</th>
                <th>Price</th>
                <th>Status</th>
                <th>Performance</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listings)): ?>
                <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text3);">You haven't added any listings yet. Start showcasing your services!</td></tr>
            <?php else: ?>
                <?php foreach ($listings as $listing): 
                    $status = $listing->status ?? 'active';
                    $badgeCls = $status === 'active' ? 'paid' : ($status === 'draft' ? 'pend' : 'new');
                ?>
                <tr>
                    <td>
                        <div style="width:54px; height:40px; border-radius:var(--r-md); overflow:hidden; background:var(--bg3);">
                            <img src="<?= Html::encode($listing->getPrimaryImageUrl()) ?>" style="width:100%; height:100%; object-fit:cover;" alt="preview">
                        </div>
                    </td>
                    <td>
                        <div style="font-size:12.5px;font-weight:600;color:var(--text1);"><?= Html::encode($listing->name) ?></div>
                        <div style="font-size:10.5px;color:var(--text3);"><?= Html::encode($listing->category->name ?? '—') ?></div>
                    </td>
                    <td>
                        <span class="hl-badge" style="background:var(--bg);color:var(--text2);"><?= strtoupper($listing->type) ?></span>
                    </td>
                    <td style="font-weight:700;color:var(--text1);font-size:12.5px;">KES <?= number_format($listing->price, 0) ?></td>
                    <td>
                        <span class="hl-badge <?= $badgeCls ?>"><?= ucfirst($listing->status) ?></span>
                    </td>
                    <td style="font-size:11.5px;color:var(--text3);">
                        <svg style="width:12px;height:12px;margin-bottom:-2px;margin-right:2px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <?= $listing->views ?> views
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <a href="<?= Url::to(['/provider/edit-listing', 'id' => $listing->id]) ?>" class="hl-btn-g offcanvas-link" title="Edit" style="padding:6px;width:28px;height:28px;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <?= Html::a('<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>', 
                                Url::to(['/provider/delete-listing', 'id' => $listing->id]), 
                                [
                                    'class' => 'hl-btn-g', 
                                    'style' => 'padding:6px;width:28px;height:28px;color:var(--rose);',
                                    'data-method' => 'post',
                                    'data-confirm' => 'Deactivate this listing?',
                                    'title' => 'Delete'
                                ]) 
                            ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
