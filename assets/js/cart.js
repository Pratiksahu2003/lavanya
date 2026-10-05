/**
 * LAVANYAA CREATION — Cart JavaScript (Session-based via AJAX)
 */

(function () {
  'use strict';

  const BASE = (window.LAVANYAA_BASE || document.querySelector('meta[name="base-url"]')?.content || '').replace(/\/$/, '');

  // ── Cart state (synced from server via page load or AJAX) ──
  function updateCartBadge(count) {
    const badges = document.querySelectorAll('#cart-count, .cart-badge');
    badges.forEach(b => { b.textContent = count; });
  }

  // ── Add to cart ──
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-cart');
    if (!btn) return;

    const productId   = btn.dataset.productId;
    const productCode = btn.dataset.productCode;
    const productName = btn.dataset.productName;
    const productImg  = btn.dataset.productImage || '';
    const originalHtml = btn.innerHTML;

    if (!productCode) return;

    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i>';

    fetch(BASE + '/cart-action.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({
        action: 'add',
        product_id: productId,
        product_code: productCode,
        product_name: productName,
        product_image: productImg,
      }),
    })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          updateCartBadge(data.count);
          btn.innerHTML = '<i class="bi bi-check-lg"></i>';
          showToast('"' + productName + '" added to cart!');
          setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
          }, 1500);
        }
      })
      .catch(() => {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
      });
  });

  // ── Remove from cart ──
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-cart-remove');
    if (!btn) return;
    const code = btn.dataset.code;
    fetch(BASE + '/cart-action.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ action: 'remove', product_code: code }),
    })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          updateCartBadge(data.count);
          const row = btn.closest('[data-cart-row]');
          if (row) row.remove();
          if (data.count === 0 && window.location.pathname.endsWith('/cart.php')) {
            window.location.reload();
          }
        }
      });
  });

  // ── Toast notification ──
  function showToast(message) {
    let toast = document.getElementById('nhz-toast');
    if (!toast) {
      toast = document.createElement('div');
      toast.id = 'nhz-toast';
      toast.style.cssText =
        'position:fixed;bottom:30px;right:30px;background:var(--primary,#C9A15B);color:#0E0E0F;' +
        'padding:14px 22px;border-radius:4px;font-size:0.88rem;font-weight:600;' +
        'box-shadow:0 18px 48px rgba(0,0,0,0.38);z-index:9999;opacity:0;transition:opacity 0.3s;' +
        'display:flex;align-items:center;gap:10px;max-width:320px;';
      document.body.appendChild(toast);
    }
    toast.innerHTML = '<i class="bi bi-bag-check-fill"></i> ' + message;
    toast.style.opacity = '1';
    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => { toast.style.opacity = '0'; }, 3000);
  }

  // ── Search redirect ──
  function bindSearch(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && this.value.trim()) {
        window.location.href = BASE + '/category.php?cat=all&search=' + encodeURIComponent(this.value.trim());
      }
    });
  }
  bindSearch('site-search');
  bindSearch('mobile-search');

})();
