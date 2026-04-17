<?php
/** @var common\models\Provider $provider */
/** @var common\models\Review[] $reviews */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'My Reviews';
?>

<div class="hl-pg-head">
    <div>
        <h1 class="hl-pg-title">My Reviews</h1>
        <div class="hl-pg-sub">Monitor customer feedback on your listings</div>
    </div>
</div>

<div class="hl-card">
    <div class="hl-card-head">
        <span class="hl-card-title">All Customer Reviews</span>
        <span style="font-size:11px;color:var(--text3);"><?= count($reviews) ?> total</span>
    </div>
    <table class="hl-tbl">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Order Ref</th>
                <th>Rating</th>
                <th>Review Comment</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reviews)): ?>
                <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text3);">No reviews have been published for your listings yet.</td></tr>
            <?php else: ?>
                <?php foreach ($reviews as $r): ?>
                <tr>
                    <td>
                        <div style="font-size:12.5px;font-weight:600;color:var(--text1);"><?= Html::encode($r->user->getFullName() ?? 'Unknown') ?></div>
                    </td>
                    <td>
                        <div style="font-size:12px;font-weight:600;color:var(--text2);"><?= Html::encode($r->order->reference ?? '—') ?></div>
                    </td>
                    <td>
                        <div style="font-size:13px;font-weight:700;color:var(--amber);">
                           <svg viewBox="0 0 24 24" fill="currentColor" style="width:12px; height:12px; margin-bottom:-1px; margin-right:2px;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                           <?= $r->rating ?>.0
                        </div>
                    </td>
                    <td style="font-size:12px;color:var(--text2);max-width:300px;">
                        <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="<?= Html::encode($r->comment) ?>">
                            <?= Html::encode($r->comment) ?: '<span style="color:var(--text3);font-style:italic;">No written comment</span>' ?>
                        </div>
                    </td>
                    <td style="font-size:11.5px;color:var(--text3);">
                        <?= Yii::$app->formatter->asDate($r->created_at, 'php:d M Y') ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
