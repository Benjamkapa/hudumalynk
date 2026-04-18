<?php
/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Account Pending Approval';
?>

<style>
.pending-wrap { max-width: 500px; margin: 60px auto; text-align: center; }
.pending-icon { width: 80px; height: 80px; margin: 0 auto 24px; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; color: #fff; }
.pending-title { font-size: 24px; font-weight: 800; color: var(--text1); margin-bottom: 12px; }
.pending-text { font-size: 14px; color: var(--text3); line-height: 1.5; margin-bottom: 24px; }
.pending-btn { display: inline-block; background: var(--acc); color: #fff; border: none; border-radius: var(--r-md); padding: 12px 24px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; }
.pending-btn:hover { background: var(--acc2); }
</style>

<div class="pending-wrap">
    <div class="pending-icon">⏳</div>
    <h1 class="pending-title">Account Under Review</h1>
    <p class="pending-text">
        Thank you for registering with HudumaLynk! Your provider account is currently under review by our team.
        We'll notify you via email once your account is approved and you can start publishing listings and receiving orders.
    </p>
    <a href="<?= \yii\helpers\Url::to(['/site/logout']) ?>" class="pending-btn">Logout</a>
</div>
