<?php
/** @var common\models\User $user */
/** @var common\models\Notification[] $notifications */
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="row g-4">
  <!-- Account Sidebar -->
  <div class="col-lg-3">
    <div class="hl-card" style="padding:1.5rem; position:sticky; top:calc(var(--navbar-h) + 1.5rem);">
      <div class="d-flex align-items-center gap-3 mb-4 pb-3" style="border-bottom:1px solid var(--border);">
        <div style="width:48px;height:48px;border-radius:50%;background:var(--hl-gradient);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:700;">
          <?= Html::encode($user->getInitials()) ?>
        </div>
        <div>
          <div style="font-weight:700;line-height:1.2;"><?= Html::encode($user->getFullName()) ?></div>
          <div style="font-size:0.75rem;color:var(--text-muted);"><?= ucfirst($user->role) ?> Member</div>
        </div>
      </div>

      <nav class="d-flex flex-column gap-1">
        <a href="<?= Url::to(['/account']) ?>" class="btn-hl-ghost justify-content-start w-100" style="padding:0.75rem 1rem;">
          <i class="bi bi-person me-2"></i> Profile
        </a>
        <a href="<?= Url::to(['/orders']) ?>" class="btn-hl-ghost justify-content-start w-100" style="padding:0.75rem 1rem;">
          <i class="bi bi-bag me-2"></i> My Orders
        </a>
        <a href="<?= Url::to(['/account/notifications']) ?>" class="btn-hl-primary justify-content-start w-100" style="padding:0.75rem 1rem;">
          <i class="bi bi-bell me-2"></i> Notifications
        </a>
        <a href="<?= Url::to(['/account/password']) ?>" class="btn-hl-ghost justify-content-start w-100" style="padding:0.75rem 1rem;">
          <i class="bi bi-shield-lock me-2"></i> Security
        </a>
        <hr>
        <?= Html::a('<i class="bi bi-box-arrow-right me-2"></i>Sign Out', Url::to(['/logout']), [
            'class' => 'btn-hl-ghost text-danger justify-content-start w-100',
            'data-method' => 'post',
            'style' => 'padding:0.75rem 1rem;'
        ]) ?>
      </nav>
    </div>
  </div>

  <!-- Notifications List -->
  <div class="col-lg-9">
    <div class="mb-4 d-flex align-items-center justify-content-between">
      <div>
        <h3 style="font-weight:800;letter-spacing:-0.5px;margin:0;">Notifications</h3>
        <p style="color:var(--text-muted);margin:0;">Updates on your orders and account activity</p>
      </div>
    </div>

    <?php if (empty($notifications)): ?>
      <div class="hl-card text-center" style="padding:5rem 2rem;">
        <div style="width:80px;height:80px;background:var(--bg-alt);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:1.5rem;">
          <i class="bi bi-bell-slash" style="font-size:2.5rem;color:var(--text-muted);"></i>
        </div>
        <h4>No notifications yet</h4>
        <p style="color:var(--text-muted);max-width:320px;margin:0 auto;">When you place orders or receive updates, they'll appear here.</p>
        <a href="<?= Url::to(['/browse']) ?>" class="btn-hl-primary mt-4">Browse Marketplace</a>
      </div>
    <?php else: ?>
      <div class="d-flex flex-column gap-3">
        <?php foreach ($notifications as $n): ?>
          <div class="hl-card notification-item <?= $n->is_read ? 'read' : 'unread' ?>" style="padding:1.25rem; transition:transform 0.2s ease;">
            <div class="d-flex gap-3">
              <div class="notif-icon-box <?= $n->type ?>">
                <?php
                $icon = 'bi-info-circle';
                if (str_contains($n->type, 'order')) $icon = 'bi-bag-check';
                if (str_contains($n->type, 'payment')) $icon = 'bi-credit-card';
                if (str_contains($n->type, 'provider')) $icon = 'bi-briefcase';
                ?>
                <i class="bi <?= $icon ?>"></i>
              </div>
              <div style="flex:1;">
                <div class="d-flex justify-content-between align-items-start mb-1">
                  <h6 style="margin:0;font-weight:700;"><?= Html::encode($n->title) ?></h6>
                  <span style="font-size:0.7rem;color:var(--text-muted);"><?= Yii::$app->formatter->asRelativeTime($n->created_at) ?></span>
                </div>
                <p style="font-size:0.9rem;color:var(--text-muted);margin:0 0 1rem 0;"><?= Html::encode($n->message) ?></p>
                
                <?php if (!$n->is_read): ?>
                  <a href="<?= Url::to(['/account/mark-notif-read', 'id' => $n->id]) ?>" class="btn-hl-ghost" style="font-size:0.75rem;padding:0.25rem 0.6rem;height:auto;">
                    <i class="bi bi-check2"></i> Mark as read
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<style>
.notification-item.unread { border-left: 4px solid var(--hl-blue); background: rgba(var(--hl-blue-rgb), 0.02); }
.notification-item.read { opacity: 0.8; }
.notification-item:hover { transform: translateY(-2px); border-color: var(--hl-blue); }

.notif-icon-box {
  width: 42px; height: 42px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.25rem; flex-shrink: 0;
}
.notif-icon-box.general { background: #E3F2FD; color: #1976D2; }
.notif-icon-box.order_placed, .notif-icon-box.order_accepted { background: #E8F5E9; color: #388E3C; }
.notif-icon-box.payment_confirmed { background: #FFF8E1; color: #FBC02D; }
.notif-icon-box.order_rejected, .notif-icon-box.payment_failed { background: #FFEBEE; color: #D32F2F; }
</style>
