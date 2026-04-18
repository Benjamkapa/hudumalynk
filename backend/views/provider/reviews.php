<?php
/** @var yii\web\View $this */
/** @var common\models\Provider $provider */
/** @var array $reviews */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'My Reviews';
?>

<style>
.reviews-header { margin-bottom: 24px; }
.reviews-title { font-size: 24px; font-weight: 800; color: var(--text1); margin-bottom: 8px; }
.reviews-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 24px; }
.stat-card { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 16px; text-align: center; }
.stat-label { font-size: 11px; color: var(--text3); text-transform: uppercase; letter-spacing: .1em; font-weight: 600; margin-bottom: 8px; }
.stat-value { font-size: 28px; font-weight: 800; color: var(--text1); }
.rating-distribution { margin-top: 24px; }
.rating-bar { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
.rating-label { width: 60px; font-size: 12px; text-align: right; font-weight: 600; color: var(--text2); }
.rating-track { flex: 1; height: 8px; background: var(--bg3); border-radius: 10px; overflow: hidden; }
.rating-fill { height: 100%; background: var(--teal); border-radius: 10px; }
.rating-count { width: 40px; text-align: right; font-size: 12px; color: var(--text3); }
.reviews-list { margin-top: 24px; }
.review-card { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 18px; margin-bottom: 12px; }
.review-header { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
.review-avatar { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 800; color: #fff; flex-shrink: 0; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.review-meta { flex: 1; }
.review-name { font-size: 13px; font-weight: 700; color: var(--text1); }
.review-date { font-size: 11px; color: var(--text3); }
.review-rating { font-size: 13px; font-weight: 600; color: #fbbf24; }
.review-content { font-size: 13px; color: var(--text2); line-height: 1.5; margin-bottom: 12px; }
.review-order { font-size: 11px; color: var(--text3); }
.empty-state { text-align: center; padding: 60px 20px; }
.empty-icon { font-size: 48px; margin-bottom: 16px; }
.empty-text { font-size: 14px; color: var(--text3); }
</style>

<div class="reviews-header">
    <h1 class="reviews-title">My Reviews</h1>
</div>

<?php
$totalReviews = count($reviews);
$avgRating = $reviews ? array_sum(array_map(fn($r) => $r->rating, $reviews)) / count($reviews) : 0;
?>

<div class="reviews-stats">
    <div class="stat-card">
        <div class="stat-label">Average Rating</div>
        <div class="stat-value">★ <?= number_format($avgRating, 1) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Reviews</div>
        <div class="stat-value"><?= $totalReviews ?></div>
    </div>
</div>

<?php if ($reviews): ?>
    <!-- Rating Distribution -->
    <div class="rating-distribution">
        <?php
        $distribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($reviews as $review) {
            $distribution[$review->rating]++;
        }
        for ($i = 5; $i >= 1; $i--):
            $count = $distribution[$i];
            $percent = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
        ?>
            <div class="rating-bar">
                <div class="rating-label">★ <?= $i ?></div>
                <div class="rating-track">
                    <div class="rating-fill" style="width: <?= $percent ?>%"></div>
                </div>
                <div class="rating-count"><?= $count ?></div>
            </div>
        <?php endfor; ?>
    </div>

    <!-- Reviews List -->
    <div class="reviews-list">
        <?php foreach ($reviews as $review): ?>
            <div class="review-card">
                <div class="review-header">
                    <div class="review-avatar">
                        <?= strtoupper(substr($review->user->getFullName(), 0, 1)) ?>
                    </div>
                    <div class="review-meta">
                        <div class="review-name"><?= Html::encode($review->user->getFullName()) ?></div>
                        <div class="review-date"><?= date('M d, Y', strtotime($review->created_at)) ?></div>
                    </div>
                    <div class="review-rating">★ <?= $review->rating ?>/5</div>
                </div>
                <div class="review-content"><?= Html::encode($review->comment) ?></div>
                <div class="review-order">Order #<?= $review->order_id ?></div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="empty-state">
        <div class="empty-icon">⭐</div>
        <div class="empty-text">No reviews yet. Complete orders to receive reviews from customers!</div>
    </div>
<?php endif; ?>