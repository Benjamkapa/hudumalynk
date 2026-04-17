<?php

/** @var yii\web\View $this */
/** @var string $content */

use frontend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Notification;

AppAsset::register($this);

$user = Yii::$app->user;
$identity = $user->identity;
$unread = $identity ? $identity->getUnreadNotificationsCount() : 0;
$isLoggedIn = !$user->isGuest;
$isProvider = $identity && $identity->isProvider();
$isAdmin = $identity && $identity->isAdmin();
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description"
    content="<?= Html::encode($this->params['metaDescription'] ?? 'HudumaLynk — Find trusted service providers and products in Nairobi. Book services, order products, pay securely.') ?>">
  <title>
    <?= Html::encode($this->title ? $this->title . ' — HudumaLynk' : "Nairobi's Premier Marketplace for Services & Products") ?>
  </title>
  <link rel="icon" href="/favicon.ico" type="image/x-icon">
  <style>
    .hl-notif-dropdown .dropdown-item {
      padding: 0.85rem 1rem;
      border-bottom: 1px solid var(--border);
      white-space: normal;
      transition: background 0.2s;
    }

    .hl-notif-dropdown .dropdown-item:last-child {
      border-bottom: none;
    }

    .hl-notif-dropdown .dropdown-item.unread {
      background: rgba(var(--hl-blue-rgb), 0.04);
    }

    .hl-notif-dropdown .dropdown-item:hover {
      background: var(--surface-2);
    }

    .hl-notif-dropdown .notif-title {
      font-weight: 700;
      font-size: 0.88rem;
      margin-bottom: 0.15rem;
      color: var(--text-main);
    }

    .hl-notif-dropdown .notif-msg {
      font-size: 0.8rem;
      color: var(--text-muted);
      line-height: 1.4;
      margin-bottom: 0.25rem;
    }

    .hl-notif-dropdown .notif-time {
      font-size: 0.72rem;
      color: var(--text-light);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .hl-notif-dropdown .mark-read-btn {
      border: none;
      background: none;
      color: var(--hl-blue);
      padding: 0;
      font-size: 0.75rem;
      opacity: 0;
      transition: opacity 0.2s;
    }

    .hl-notif-dropdown .dropdown-item.unread:hover .mark-read-btn {
      opacity: 1;
    }
  </style>
  <?php $this->head() ?>
</head>

<body>
  <?php $this->beginBody() ?>

  <!-- ── Navbar ─────────────────────────────────────────────────── -->
  <nav class="hl-navbar" id="hl-navbar">
    <div class="hl-navbar-inner">

      <!-- Logo -->
      <a href="<?= Url::to(['/']) ?>" class="hl-logo">
        <img src="/img/auth-logo.png" alt="HudumaLynk" style="height:38px; width:auto;"
          onerror="this.style.display='none'">
        HudumaLynk
      </a>

      <!-- Nav links -->
      <!-- <div class="hl-nav-links">
      <a href="<?= Url::to(['/browse']) ?>" class="<?= Yii::$app->controller->id === 'browse' ? 'active' : '' ?>">Explore</a>
      <?php if ($isLoggedIn && !$isAdmin): ?>
        <a href="<?= Url::to(['/orders']) ?>" class="<?= Yii::$app->controller->id === 'order' ? 'active' : '' ?>">My Orders</a>
      <?php endif; ?>
    </div> -->

      <!-- Right section -->
      <div class="hl-navbar-right">

        <?php if ($isLoggedIn): ?>
          <!-- Notification bell dropdown -->
          <div class="dropdown hl-notif-dropdown" id="notificationDropdown">
            <button class="hl-notif-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false"
              title="Notifications" id="notif-dropdown-btn">
              <i class="bi bi-bell"></i>
              <span class="hl-notif-badge <?= $unread > 0 ? '' : 'd-none' ?>"><?= $unread > 9 ? '9+' : $unread ?></span>
            </button>
            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0"
              style="width:320px; border-radius:var(--radius-lg); overflow:hidden;">
              <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom"
                style="background:var(--surface);">
                <span style="font-weight:700; font-size:0.9rem;color:#fff;">Notifications</span>
                <button class="btn btn-link btn-sm p-0 mb-0" style="font-size:0.75rem; text-decoration:none;"
                  onclick="markAllNotificationsRead(event)">Mark all as read</button>
              </div>
              <div id="hl-notif-list" style="max-height:350px; overflow-y:auto;">
                <div class="text-center py-4 text-muted">
                  <div class="spinner-border spinner-border-sm" role="status"></div>
                </div>
              </div>
              <a href="<?= Url::to(['/account/notifications']) ?>" class="dropdown-item text-center py-2 border-top"
                style="font-size:0.8rem; background:var(--surface-2); font-weight:600;color:#fff">View All Notifications</a>
            </div>
          </div>

          <!-- User dropdown -->
          <div class="dropdown">
            <button class="btn-hl-ghost dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"
              style="gap:0.5rem;padding:0.4rem 0.9rem;">
              <span
                style="width:32px;height:32px;border-radius:50%;background:var(--hl-gradient);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:700;">
                <?= Html::encode($identity->getInitials()) ?>
              </span>
              <span class="d-none d-md-inline"><?= Html::encode($identity->first_name) ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0"
              style="border-radius:var(--radius-lg);min-width:200px;padding:0.5rem;">
              <li class="px-3 py-2" style="border-bottom:1px solid var(--border);margin-bottom:0.25rem;">
                <div style="font-weight:700;font-size:0.9rem;"><?= Html::encode($identity->getFullName()) ?></div>
                <div style="font-size:0.75rem;color:var(--text-muted);"><?= ucfirst($identity->role) ?></div>
              </li>
              <?php if ($isAdmin): ?>
                <li><a class="dropdown-item" href="<?= Yii::$app->params['backendUrl'] . '/admin/dashboard' ?>"><i
                      class="bi bi-speedometer2 me-2"></i>Admin Panel</a></li>
              <?php elseif ($isProvider): ?>
                <li><a class="dropdown-item" href="<?= Yii::$app->params['backendUrl'] . '/provider/dashboard' ?>"><i
                      class="bi bi-grid me-2"></i>Provider Dashboard</a></li>
              <?php endif; ?>
              <li><a class="dropdown-item" href="<?= Url::to(['/account']) ?>"><i class="bi bi-person me-2"></i>My
                  Account</a></li>
              <li><a class="dropdown-item" href="<?= Url::to(['/orders']) ?>"><i class="bi bi-bag me-2"></i>My Orders</a>
              </li>
              <li>
                <hr class="dropdown-divider my-1">
              </li>
              <li>
                <?= Html::a(
                  '<i class="bi bi-box-arrow-right me-2"></i>Sign out',
                  Url::to(['/logout']),
                  ['class' => 'dropdown-item text-danger', 'data-method' => 'post', 'data-confirm' => 'Sign out of HudumaLynk?']
                ) ?>
              </li>
            </ul>
          </div>

        <?php else: ?>
          <a href="<?= Url::to(['/login']) ?>" class="btn-hl-primary w-100 text-center mt-2 d-none d-md-block">
            Sign in, make an Order
          </a>
        <?php endif; ?>

      </div><!-- /.hl-navbar-right -->

      <!-- Mobile hamburger -->
      <button class="d-md-none btn-hl-ghost ms-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu"
        style="padding:0.4rem 0.6rem;">
        <i class="bi bi-list" style="font-size:1.4rem;"></i>
      </button>

    </div><!-- /.hl-navbar-inner -->
  </nav>

  <!-- Mobile Off-canvas Menu -->
  <div class="offcanvas offcanvas-end" id="mobileMenu" style="max-width:300px;">
    <div class="offcanvas-header border-bottom">
      <img src="/img/auth-logo.png" alt="HudumaLynk" style="height:38px;" onerror="this.style.display='none'">
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
      <nav class="d-flex flex-column gap-1">
        <a href="<?= Url::to(['/browse']) ?>"
          style="padding:0.7rem 1rem;border-radius:var(--radius);display:block;">Marketplace</a>
        <a href="<?= Url::to(['/browse', 'type' => 'service']) ?>"
          style="padding:0.7rem 1rem;border-radius:var(--radius);display:block;">Services</a>
        <a href="<?= Url::to(['/browse', 'type' => 'product']) ?>"
          style="padding:0.7rem 1rem;border-radius:var(--radius);display:block;">Products</a>
        <?php if ($isLoggedIn): ?>
          <a href="<?= Url::to(['/orders']) ?>" style="padding:0.7rem 1rem;border-radius:var(--radius);display:block;">My
            Orders</a>
          <a href="<?= Url::to(['/account']) ?>"
            style="padding:0.7rem 1rem;border-radius:var(--radius);display:block;">Account</a>
          <hr>
          <?= Html::a('Sign Out', Url::to(['/logout']), ['data-method' => 'post', 'style' => 'padding:0.7rem 1rem;display:block;color:var(--danger);']) ?>
        <?php else: ?>
          <hr>
          <a href="<?= Url::to(['/login']) ?>" class="btn-hl-primary w-100 text-center mt-2">Sign In</a>
          <a href="<?= Yii::$app->params['backendUrl'] . '/login' ?>"
            class="btn-hl-outline w-100 text-center mt-2">Provider Portal</a>
        <?php endif; ?>
      </nav>
    </div>
  </div>

  <!-- ── Flash Messages ─────────────────────────────────────────── -->
  <?php foreach (Yii::$app->session->getAllFlashes() as $type => $messages): ?>
    <?php foreach ((array) $messages as $msg): ?>
      <div
        class="hl-alert hl-alert-<?= in_array($type, ['success', 'danger', 'warning', 'info']) ? $type : 'info' ?> mx-auto mt-3"
        style="max-width:900px;margin-top:calc(var(--navbar-h) + 1rem)!important;" data-auto-dismiss="5000">
        <i
          class="bi bi-<?= $type === 'success' ? 'check-circle' : ($type === 'danger' ? 'x-circle' : 'info-circle') ?>"></i>
        <span><?= Html::encode($msg) ?></span>
      </div>
    <?php endforeach; ?>
  <?php endforeach; ?>

  <!-- ── Main Content ───────────────────────────────────────────── -->
  <main class="hl-page">
    <?= $content ?>
  </main>

  <!-- ── Footer ────────────────────────────────────────────────── -->
  <footer class="hl-footer">
    <div class="hl-footer-inner">
      <div class="hl-footer-grid">
        <div class="hl-footer-brand">
          <div class="hl-logo" style="margin-bottom:0.75rem;">
            <img src="/img/auth-logo.png" alt="HudumaLynk" style="height:38px;" onerror="this.style.display='none'">
          </div>
          <p>Your trusted digital marketplace for services and products in Nairobi. Connecting providers with customers
            since 2026.</p>
          <div class="d-flex gap-2 mt-3">
            <a href="#"
              style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1rem;transition:all var(--transition);"
              onmouseover="this.style.background='#1877F2'"
              onmouseout="this.style.background='rgba(255,255,255,0.1)'"><i class="bi bi-facebook"></i></a>
            <a href="#"
              style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1rem;transition:all var(--transition);"
              onmouseover="this.style.background='#1DA1F2'"
              onmouseout="this.style.background='rgba(255,255,255,0.1)'"><i class="bi bi-twitter-x"></i></a>
            <a href="#"
              style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1rem;transition:all var(--transition);"
              onmouseover="this.style.background='#25D366'"
              onmouseout="this.style.background='rgba(255,255,255,0.1)'"><i class="bi bi-whatsapp"></i></a>
            <a href="#"
              style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1rem;transition:all var(--transition);"
              onmouseover="this.style.background='#E4405F'"
              onmouseout="this.style.background='rgba(255,255,255,0.1)'"><i class="bi bi-instagram"></i></a>
          </div>
        </div>
        <div class="hl-footer-col">
          <h5>Explore</h5>
          <a href="<?= Url::to(['/browse']) ?>">All Listings</a>
          <a href="<?= Url::to(['/browse', 'type' => 'service']) ?>">Services</a>
          <a href="<?= Url::to(['/browse', 'type' => 'product']) ?>">Products</a>
          <a href="<?= Url::to(['/browse', 'sort' => 'featured']) ?>">Featured Listings</a>
        </div>
        <div class="hl-footer-col">
          <h5>For Providers</h5>
          <a href="<?= Yii::$app->params['backendUrl'] . '/login' ?>">Portal Sign In</a>
          <a href="<?= Url::to(['/join']) ?>">Join as Provider</a>
          <a href="<?= Url::to(['/join', '#' => 'plans']) ?>">View Pricing Plans</a>
          <a href="<?= Url::to(['/join']) ?>">Provider Guidelines</a>
        </div>
        <div class="hl-footer-col">
          <h5>Support</h5>
          <a href="#">Help Center</a>
          <a href="#">Contact Us</a>
          <a href="#">Privacy Policy</a>
          <a href="#">Terms of Service</a>
        </div>
      </div>
      <div class="hl-footer-bottom">
        <span>© <?= date('Y') ?> HudumaLynk. All rights reserved.</span>
        <span>Built with ❤️ for Nairobi</span>
      </div>
    </div>
  </footer>

  <!-- Bootstrap Icons (CDN) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Navbar scroll effect
      const navbar = document.getElementById('hl-navbar');
      window.addEventListener('scroll', () => {
        if (window.scrollY > 20) {
          navbar.classList.add('scrolled');
        } else {
          navbar.classList.remove('scrolled');
        }
      });

      const notifBtn = document.getElementById('notif-dropdown-btn');
      if (notifBtn) {
        notifBtn.addEventListener('click', function () {
          if (!this.getAttribute('data-loaded')) {
            loadNotifications();
            this.setAttribute('data-loaded', 'true');
          }
        });
      }
    });

    function loadNotifications() {
      const list = document.getElementById('hl-notif-list');
      fetch('<?= Url::to(['/notification/list']) ?>')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            renderNotifications(data.notifications);
            updateNotifBadge(data.unreadCount);
          }
        });
    }

    function renderNotifications(notifs) {
      const list = document.getElementById('hl-notif-list');
      if (!notifs || notifs.length === 0) {
        list.innerHTML = '<div class="text-center py-5 text-muted"><i class="bi bi-bell-slash mb-2 d-block" style="font-size:1.5rem;"></i>No notifications yet</div>';
        return;
      }

      let html = '';
      notifs.forEach(n => {
        html += `
            <div class="dropdown-item ${n.is_read ? '' : 'unread'}" id="notif-${n.id}">
                <div class="notif-title">${n.title}</div>
                <div class="notif-msg">${n.message}</div>
                <div class="notif-time">
                    <span>${n.time}</span>
                    ${!n.is_read ? `<button class="mark-read-btn" onclick="markAsRead(${n.id}, event)"><i class="bi bi-check2-all me-1"></i>Mark as read</button>` : ''}
                </div>
            </div>
        `;
      });
      list.innerHTML = html;
    }

    function markAsRead(id, event) {
      event.preventDefault();
      event.stopPropagation();

      fetch('<?= Url::to(['/notification/mark-read']) ?>?id=' + id, {
        method: 'POST',
        headers: {
          'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>'
        }
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            const item = document.getElementById('notif-' + id);
            item.classList.remove('unread');
            const btn = item.querySelector('.mark-read-btn');
            if (btn) btn.remove();
            updateNotifBadge(data.unreadCount);
          }
        });
    }

    function markAllNotificationsRead(event) {
      event.preventDefault();
      event.stopPropagation();

      fetch('<?= Url::to(['/notification/mark-all-read']) ?>', {
        method: 'POST',
        headers: {
          'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>'
        }
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.querySelectorAll('.hl-notif-dropdown .unread').forEach(item => {
              item.classList.remove('unread');
              const btn = item.querySelector('.mark-read-btn');
              if (btn) btn.remove();
            });
            updateNotifBadge(0);
          }
        });
    }

    function updateNotifBadge(count) {
      const badges = document.querySelectorAll('.hl-notif-badge');
      badges.forEach(b => {
        if (count > 0) {
          b.textContent = count > 9 ? '9+' : count;
          b.classList.remove('d-none');
        } else {
          b.classList.add('d-none');
        }
      });
    }
  </script>
  <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>