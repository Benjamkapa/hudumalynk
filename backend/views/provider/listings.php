<?php
/** @var yii\web\View $this */
/** @var common\models\Provider $provider */
/** @var array $listings */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'My Listings';

// Calculate stats
$stats = [
    'total' => count($listings),
    'active' => count(array_filter($listings, fn($l) => $l->status === \common\models\Listing::STATUS_ACTIVE)),
    'draft' => count(array_filter($listings, fn($l) => $l->status === \common\models\Listing::STATUS_DRAFT)),
    'inactive' => count(array_filter($listings, fn($l) => $l->status === \common\models\Listing::STATUS_INACTIVE)),
];
?>

<style>
.listings-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
.listings-title { font-size: 24px; font-weight: 800; color: var(--text1); }
.add-listing-btn { padding: 10px 16px; background: var(--acc); color: #fff; border: none; border-radius: var(--r-md); font-size: 13px; font-weight: 600; cursor: pointer; transition: background .2s; }
.add-listing-btn:hover { background: var(--acc2); }

.hero-cards { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 12px; margin-bottom: 16px; }
.hero-card { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 18px 20px 14px; }
.hero-card .hc-label { font-size: 11px; color: var(--text3); text-transform: uppercase; letter-spacing: .07em; font-weight: 600; margin-bottom: 6px; }
.hero-card .hc-value { font-size: 32px; font-weight: 800; letter-spacing: -.04em; line-height: 1.1; margin-bottom: 5px; color: var(--text1); }
.hero-card .hc-sub { font-size: 12px; color: var(--text3); }

.listings-table { width: 100%; border-collapse: collapse; background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-lg); overflow: hidden; }
.listings-table thead th { background: var(--bg3); padding: 14px 16px; text-align: left; font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--text3); letter-spacing: .05em; }
.listings-table tbody td { padding: 14px 16px; border-bottom: 1px solid var(--border); font-size: 13px; }
.listings-table tbody tr:last-child td { border-bottom: none; }

.listing-info { display: flex; align-items: center; gap: 11px; }
.listing-image { width: 40px; height: 40px; border-radius: var(--r-md); overflow: hidden; background: var(--bg2); flex-shrink: 0; }
.listing-image img { width: 100%; height: 100%; object-fit: cover; }
.listing-avatar { width: 40px; height: 40px; border-radius: var(--r-md); background: linear-gradient(135deg, var(--acc), var(--acc2)); display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 800; color: #fff; flex-shrink: 0; }
.listing-name { font-size: 13px; font-weight: 600; color: var(--text1); }
.listing-id { font-size: 11px; color: var(--text3); }

.listing-category { color: var(--text2); font-size: 12px; }
.listing-price { font-weight: 700; color: var(--text1); font-size: 13px; }
.listing-views { color: var(--text2); font-size: 12px; }
.listing-status { font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 12px; width: fit-content; }
.status-active { background: rgba(34,197,94,.1); color: #22c55e; }
.status-draft { background: rgba(251,191,36,.1); color: #fbbf24; }
.status-inactive { background: rgba(239,68,68,.1); color: #ef4444; }

.listing-actions { display: flex; gap: 8px; }
.action-btn { padding: 6px 10px; border: 1px solid var(--border); background: var(--bg); border-radius: var(--r-md); font-size: 11px; font-weight: 600; color: var(--text2); cursor: pointer; transition: all .2s; text-decoration: none; }
.action-btn:hover { border-color: var(--acc); color: var(--acc); }
.action-btn.danger:hover { border-color: var(--rose); color: var(--rose); }

.empty-state { text-align: center; padding: 60px 20px; }
.empty-icon { font-size: 48px; margin-bottom: 16px; }
.empty-text { font-size: 14px; color: var(--text3); margin-bottom: 20px; }
.empty-action { display: inline-block; padding: 10px 16px; background: var(--acc); color: #fff; border-radius: var(--r-md); font-size: 13px; font-weight: 600; text-decoration: none; }

.modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,.5); z-index: 1000; display: none; }
.modal-panel { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: var(--bg); border-radius: var(--r-lg); width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto; z-index: 1001; display: none; }
.modal-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px; border-bottom: 1px solid var(--border); }
.modal-title { font-size: 18px; font-weight: 700; color: var(--text1); }
.modal-close { background: none; border: none; font-size: 24px; color: var(--text3); cursor: pointer; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; }
.modal-content { padding: 24px; }

@media (max-width: 900px) {
    .hero-cards { grid-template-columns: repeat(2, 1fr); }
    .listings-header { flex-direction: column; gap: 16px; align-items: flex-start; }
    .listings-table { font-size: 12px; }
    .listings-table thead th, .listings-table tbody td { padding: 10px 12px; }
}
</style>

<div class="hl-pg-head">
    <h1 class="hl-pg-title">My Listings</h1>
    <div class="hl-pg-sub">Manage your products and services</div>
</div>

<!-- Hero Stats -->
<div class="hero-cards">
    <div class="hero-card">
        <div class="hc-label">Total Listings</div>
        <div class="hc-value"><?= number_format($stats['total']) ?></div>
        <div class="hc-sub">All listings</div>
    </div>
    <div class="hero-card">
        <div class="hc-label">Active</div>
        <div class="hc-value" style="color: var(--teal);"><?= number_format($stats['active']) ?></div>
        <div class="hc-sub">Visible to buyers</div>
    </div>
    <div class="hero-card">
        <div class="hc-label">Draft</div>
        <div class="hc-value" style="color: #fbbf24;"><?= number_format($stats['draft']) ?></div>
        <div class="hc-sub">Not published</div>
    </div>
    <div class="hero-card">
        <div class="hc-label">Inactive</div>
        <div class="hc-value" style="color: var(--rose);"><?= number_format($stats['inactive']) ?></div>
        <div class="hc-sub">Hidden listings</div>
    </div>
</div>

<!-- Header with Add Button -->
<div class="listings-header">
    <h2 class="listings-title">All Listings</h2>
    <a href="<?= Url::to(['/provider/create-listing']) ?>" class="add-listing-btn offcanvas-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;margin-right:6px;">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Add Listing
    </a>
</div>

<?php if ($listings): ?>
    <table class="listings-table">
        <thead>
            <tr>
                <th>Listing</th>
                <th>Category</th>
                <th>Price</th>
                <th>Views</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listings as $listing):
                $statusClass = 'status-' . $listing->status;
            ?>
                <tr>
                    <td>
                        <div class="listing-info">
                            <div class="listing-image">
                                <?php if ($listing->getPrimaryImageUrl()): ?>
                                    <img src="<?= Html::encode($listing->getPrimaryImageUrl()) ?>" alt="<?= Html::encode($listing->name) ?>" loading="lazy">
                                <?php else: ?>
                                    <?php
                                        $colors = ['#6C5CE7', '#3B82F6', '#00B894', '#E17055', '#FDCB6E', '#A29BFE'];
                                        $bg = $colors[array_rand($colors)];
                                        $init = strtoupper(substr($listing->name, 0, 2));
                                    ?>
                                    <div class="listing-avatar" style="background: <?= $bg ?>">
                                        <?= Html::encode($init) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="listing-name"><?= Html::encode($listing->name) ?></div>
                                <div class="listing-id">#<?= $listing->id ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="listing-category"><?= Html::encode($listing->category->name ?? '—') ?></td>
                    <td class="listing-price">KSh <?= number_format($listing->price, 0) ?></td>
                    <td class="listing-views">0</td>
                    <td><span class="listing-status <?= $statusClass ?>"><?= ucfirst($listing->status) ?></span></td>
                    <td><?= date('M d, Y', strtotime($listing->created_at)) ?></td>
                    <td>
                        <div class="listing-actions">
                            <a href="<?= Url::to(['/provider/edit-listing', 'id' => $listing->id]) ?>" class="action-btn offcanvas-link">Edit</a>
                            <form method="post" action="<?= Url::to(['/provider/toggle-listing', 'id' => $listing->id]) ?>" style="display:inline;">
                                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                <button type="submit" class="action-btn">
                                    <?= $listing->status === \common\models\Listing::STATUS_ACTIVE ? 'Deactivate' : 'Activate' ?>
                                </button>
                            </form>
                            <form method="post" action="<?= Url::to(['/provider/delete-listing', 'id' => $listing->id]) ?>" style="display:inline;" onsubmit="return confirm('Delete this listing?')">
                                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                <button type="submit" class="action-btn danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="empty-state">
        <div class="empty-icon">📦</div>
        <div class="empty-text">You haven't created any listings yet. Start by adding your first product or service.</div>
        <a href="<?= Url::to(['/provider/create-listing']) ?>" class="empty-action offcanvas-link">Create Your First Listing</a>
    </div>
<?php endif; ?>

<script>
// Close modal on Escape key (for any remaining modals)
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        // This will be handled by the offcanvas system
    }
});
</script>