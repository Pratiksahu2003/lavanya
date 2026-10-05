/**
 * LAVANYAA CREATION — Premium Admin JS
 */
(function () {
  'use strict';

  /* ── SIDEBAR TOGGLE ── */
  const menuBtn = document.getElementById('adm-menu-btn');
  const sidebar = document.getElementById('adm-sidebar');
  if (menuBtn && sidebar) {
    menuBtn.addEventListener('click', () => sidebar.classList.toggle('open'));
    document.addEventListener('click', e => {
      if (sidebar.classList.contains('open') && !sidebar.contains(e.target) && !menuBtn.contains(e.target)) {
        sidebar.classList.remove('open');
      }
    });
  }

  /* ── FLASH AUTO-DISMISS ── */
  document.querySelectorAll('.adm-flash').forEach(el => {
    setTimeout(() => {
      el.style.transition = 'opacity .5s ease, max-height .5s ease';
      el.style.opacity = '0';
      el.style.maxHeight = '0';
      el.style.overflow = 'hidden';
      el.style.paddingTop = '0';
      el.style.paddingBottom = '0';
      el.style.marginBottom = '0';
      setTimeout(() => el.remove(), 520);
    }, 4000);
  });

  /* ── CUSTOM CONFIRM DIALOG ── */
  window.admConfirm = function (message, onConfirm) {
    const existing = document.getElementById('adm-confirm-overlay');
    if (existing) existing.remove();

    const overlay = document.createElement('div');
    overlay.id = 'adm-confirm-overlay';
    overlay.className = 'adm-confirm-modal';
    overlay.innerHTML = `
      <div class="adm-confirm-box">
        <i class="bi bi-exclamation-triangle-fill adm-confirm-icon"></i>
        <h5>Confirm Action</h5>
        <p>${message}</p>
        <div class="adm-confirm-btns">
          <button class="adm-btn adm-btn-danger" id="adm-confirm-yes" style="flex:1">Delete</button>
          <button class="adm-btn adm-btn-secondary" id="adm-confirm-no" style="flex:1">Cancel</button>
        </div>
      </div>`;
    document.body.appendChild(overlay);

    document.getElementById('adm-confirm-yes').addEventListener('click', () => { overlay.remove(); onConfirm(); });
    document.getElementById('adm-confirm-no').addEventListener('click', () => overlay.remove());
    overlay.addEventListener('click', e => { if (e.target === overlay) overlay.remove(); });
    document.addEventListener('keydown', function esc(e) { if (e.key === 'Escape') { overlay.remove(); document.removeEventListener('keydown', esc); } });
  };

  /* ── INTERCEPT [data-confirm] LINKS ── */
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', function (e) {
      e.preventDefault();
      const href = this.getAttribute('href') || this.getAttribute('data-href');
      admConfirm(this.dataset.confirm || 'Are you sure? This cannot be undone.', () => {
        window.location.href = href;
      });
    });
  });

  /* ── SETTINGS ACCORDION ── */
  document.querySelectorAll('.adm-settings-header').forEach(header => {
    const section = header.closest('.adm-settings-section');
    header.addEventListener('click', () => {
      const isOpen = section.classList.contains('open');
      // Close all, open clicked
      document.querySelectorAll('.adm-settings-section').forEach(s => s.classList.remove('open'));
      if (!isOpen) section.classList.add('open');
    });
  });
  // Open first section by default
  const firstSection = document.querySelector('.adm-settings-section');
  if (firstSection) firstSection.classList.add('open');

  /* ── SETTINGS INDEPENDENT SAVE (AJAX) ── */
  document.querySelectorAll('.adm-settings-section form').forEach(form => {
    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      const btn = this.querySelector('[type="submit"]');
      const originalText = btn ? btn.innerHTML : '';
      if (btn) { btn.innerHTML = '<span class="adm-spinner"></span> Saving…'; btn.disabled = true; }

      try {
        const resp = await fetch(this.action || window.location.href, {
          method: 'POST',
          body: new FormData(this)
        });
        const text = await resp.text();

        // Show inline success
        let msgEl = this.querySelector('.adm-settings-ajax-msg');
        if (!msgEl) {
          msgEl = document.createElement('div');
          msgEl.className = 'adm-flash adm-flash-success adm-settings-ajax-msg';
          this.querySelector('.adm-settings-save-row')?.insertAdjacentElement('beforebegin', msgEl);
        }
        msgEl.innerHTML = '<i class="bi bi-check-circle-fill"></i> Saved successfully.';
        msgEl.style.display = 'flex';
        setTimeout(() => { msgEl.style.opacity='0'; setTimeout(()=>msgEl.remove(),500); }, 3000);
      } catch (err) {
        alert('Save failed. Please try again.');
      } finally {
        if (btn) { btn.innerHTML = originalText; btn.disabled = false; }
      }
    });
  });

  /* ── IMAGE PREVIEW ON FILE SELECT ── */
  document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
    input.addEventListener('change', function () {
      const preview = document.getElementById(this.dataset.preview);
      if (!preview || !this.files[0]) return;
      const reader = new FileReader();
      reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
      reader.readAsDataURL(this.files[0]);
    });
  });

  /* ── GENERIC IMAGE PREVIEW (any input[type=file] near adm-img-preview) ── */
  document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function () {
      if (!this.files[0]) return;
      const wrap = this.closest('.adm-form-group') || this.parentElement;
      const preview = wrap?.querySelector('.adm-img-preview, .adm-img-preview-lg');
      if (!preview) return;
      const reader = new FileReader();
      reader.onload = e => { preview.src = e.target.result; };
      reader.readAsDataURL(this.files[0]);
    });
  });

  /* ── STATUS TOGGLE (AJAX) ── */
  document.querySelectorAll('.adm-toggle').forEach(toggle => {
    toggle.addEventListener('click', async function () {
      const url = this.dataset.url;
      if (!url) return;
      this.classList.toggle('on');
      try {
        await fetch(url, { method: 'GET' });
      } catch (e) { this.classList.toggle('on'); }
    });
  });

  /* ── SUBCATEGORY LAZY LOAD ── */
  const catSelect = document.getElementById('category_id');
  const subSelect = document.getElementById('subcategory_id');
  if (catSelect && subSelect) {
    catSelect.addEventListener('change', async function () {
      const catId = this.value;
      subSelect.innerHTML = '<option value="">Loading…</option>';
      subSelect.disabled = true;
      if (!catId) { subSelect.innerHTML = '<option value="">— None —</option>'; subSelect.disabled = false; return; }
      try {
        const base = document.querySelector('meta[name="base-url"]')?.content || '';
        const resp = await fetch(base + '/admin/ajax/get-subcategories.php?cat=' + catId);
        const data = await resp.json();
        subSelect.innerHTML = '<option value="">— None —</option>';
        data.forEach(s => {
          const opt = document.createElement('option');
          opt.value = s.id; opt.textContent = s.name;
          if (s.id == subSelect.dataset.selected) opt.selected = true;
          subSelect.appendChild(opt);
        });
      } catch (e) { subSelect.innerHTML = '<option value="">Error loading</option>'; }
      finally { subSelect.disabled = false; }
    });
  }

  /* ── INLINE SORT ORDER (drag handles) ── */
  // Future enhancement placeholder

  /* ── SEARCH FILTER DEBOUNCE ── */
  const searchInputs = document.querySelectorAll('.adm-search-bar input[data-autosearch]');
  searchInputs.forEach(input => {
    let timer;
    input.addEventListener('input', function () {
      clearTimeout(timer);
      timer = setTimeout(() => this.closest('form').submit(), 500);
    });
  });

})();
