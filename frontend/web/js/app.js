/**
 * HudumaLynk — Global Frontend JS
 * Currency toggle | Navbar scroll state | Flash auto-dismiss | Utilities
 */

// ── Currency Toggle ──────────────────────────────────────────────────────────
const CURRENCY_KEY = 'hl_currency';

function getCurrency() {
  return localStorage.getItem(CURRENCY_KEY) || 'KES';
}

function setCurrency(cur) {
  localStorage.setItem(CURRENCY_KEY, cur);
  document.body.setAttribute('data-currency', cur);

  // Update all toggle buttons
  document.querySelectorAll('.hl-currency-toggle button').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.cur === cur);
  });

  // Update large payment display panels
  document.querySelectorAll('.hl-currency-display').forEach(el => {
    el.querySelector('.amount-kes')?.classList.toggle('d-none', cur === 'USD');
    el.querySelector('.amount-usd')?.classList.toggle('d-none', cur === 'KES');
  });

  // Dispatch event so any page can react
  document.dispatchEvent(new CustomEvent('hl:currency-changed', { detail: { currency: cur } }));
}

function initCurrencyToggle() {
  const saved = getCurrency();
  document.body.setAttribute('data-currency', saved);

  document.querySelectorAll('.hl-currency-toggle').forEach(toggle => {
    toggle.querySelectorAll('button').forEach(btn => {
      btn.classList.toggle('active', btn.dataset.cur === saved);
      btn.addEventListener('click', () => setCurrency(btn.dataset.cur));
    });
  });
}

// ── Navbar scroll shadow ─────────────────────────────────────────────────────
function initNavbar() {
  const navbar = document.querySelector('.hl-navbar');
  if (!navbar) return;
  const onScroll = () => navbar.classList.toggle('scrolled', window.scrollY > 10);
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
}

// ── Flash message auto-dismiss ───────────────────────────────────────────────
function initFlashMessages() {
  document.querySelectorAll('.hl-alert[data-auto-dismiss]').forEach(el => {
    const delay = parseInt(el.dataset.autoDismiss) || 5000;
    setTimeout(() => {
      el.style.transition = 'opacity 0.5s';
      el.style.opacity = '0';
      setTimeout(() => el.remove(), 500);
    }, delay);
  });
}

// ── Confirm dialog helper ────────────────────────────────────────────────────
function hlConfirm(message, onConfirm) {
  if (window.confirm(message)) onConfirm();
}

// ── Listing image carousel (on listing detail page) ──────────────────────────
function initListingGallery() {
  const thumbs = document.querySelectorAll('.hl-gallery-thumb');
  const main   = document.querySelector('.hl-gallery-main img');
  if (!thumbs.length || !main) return;

  thumbs.forEach(thumb => {
    thumb.addEventListener('click', () => {
      main.src = thumb.dataset.src;
      thumbs.forEach(t => t.classList.remove('active'));
      thumb.classList.add('active');
    });
  });
}

// ── STK Push polling (payment page) ─────────────────────────────────────────
function initMpesaPolling(checkoutRequestId, statusUrl, redirectUrl) {
  if (!checkoutRequestId) return;

  let attempts = 0;
  const maxAttempts = 24; // poll for up to 2 minutes (24 × 5s)
  const statusEl = document.getElementById('mpesa-status-msg');

  const poll = setInterval(async () => {
    attempts++;
    try {
      const res  = await fetch(statusUrl + '?id=' + encodeURIComponent(checkoutRequestId));
      const data = await res.json();

      if (data.paid) {
        clearInterval(poll);
        if (statusEl) statusEl.innerHTML = '<span class="text-success">✓ Payment confirmed! Redirecting…</span>';
        setTimeout(() => window.location.href = redirectUrl, 1500);
      } else if (data.failed) {
        clearInterval(poll);
        if (statusEl) statusEl.innerHTML = '<span class="text-danger">✗ Payment failed or cancelled. Please try again.</span>';
      } else if (statusEl) {
        statusEl.innerHTML = 'Waiting for M-Pesa confirmation… (' + attempts + ')';
      }
    } catch (e) {
      console.warn('[Mpesa polling] Error:', e);
    }

    if (attempts >= maxAttempts) {
      clearInterval(poll);
      if (statusEl) statusEl.innerHTML = 'Payment verification timed out. Please refresh to check your order status.';
    }
  }, 5000);
}

// ── Init ─────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  initCurrencyToggle();
  initNavbar();
  initFlashMessages();
  initListingGallery();

  // Expose utilities globally
  window.HL = { setCurrency, getCurrency, hlConfirm, initMpesaPolling };
});
