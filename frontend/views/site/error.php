<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error" style="padding: 100px 15px; min-height: 80vh; display: flex; align-items: center; justify-content: center; background: var(--surface-2);">
    <div style="max-width: 520px; width: 100%; text-align: center; padding: 40px 30px; background: var(--surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border);">
        <div style="width: 72px; height: 72px; background: var(--hl-blue-light); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 32px; height: 32px; color: var(--hl-blue);">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>
        
        <h1 style="font-weight: 800;font-size: 24px; margin-bottom: 12px; color: var(--text-primary); letter-spacing: -0.03em;"><?= Html::encode($this->title) ?></h1>

        <p style="font-size: 14px; color: var(--text-secondary); margin-bottom: 24px; line-height: 1.6;">
            <?= nl2br(Html::encode($message)) ?>
        </p>

        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 30px;">
            The requested page could not be found or a server error occurred. Please verify the URL or return to home.
        </p>

        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <a href="/" class="btn-hl-primary" style="padding: 10px 20px; font-size: 14px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px; margin-right: 6px;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Go Home
            </a>
            <a href="javascript:history.back()" class="btn-hl-ghost" style="padding: 10px 20px; font-size: 14px;">
                 <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px; margin-right: 6px;"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                 Go Back
            </a>
        </div>
    </div>
</div>
