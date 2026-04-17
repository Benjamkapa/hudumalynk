<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error" style="padding: 60px 15px; min-height: 70vh; display: flex; align-items: center; justify-content: center;">
    <div style="max-width: 480px; width: 100%; text-align: center; padding: 40px 30px; background: var(--bg2, #fff); border-radius: var(--r-lg, 10px); box-shadow: 0 4px 20px rgba(0,0,0,0.04); border: 1px solid var(--border, #E2E8F0);">
        <div style="width: 64px; height: 64px; background: var(--acc-pale, rgba(59, 130, 246, 0.12)); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 24px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 28px; height: 28px; color: var(--acc, #1D4ED8);">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>
        
        <h1 style="font-weight: 800; font-size: 22px; margin-bottom: 12px; color: var(--text1, #0F172A); letter-spacing: -0.03em;"><?= Html::encode($this->title) ?></h1>

        <p style="font-size: 13px; color: var(--text2, #475569); margin-bottom: 20px; line-height: 1.6;">
            <?= nl2br(Html::encode($message)) ?>
        </p>

        <p style="font-size: 11px; color: var(--text3, #64748B); margin-bottom: 30px;">
            The requested page could not be found or a server error occurred.
        </p>

        <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
            <a href="<?= Yii::$app->homeUrl ?>" class="hl-btn-p">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; margin-right: 4px;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Dashboard
            </a>
            <a href="javascript:history.back()" class="hl-btn-g">
                 <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; margin-right: 4px;"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                 Go Back
            </a>
        </div>
    </div>
</div>
