/**
 * LAVANYAA CREATION — Main JavaScript
 */
(function () {
  'use strict';

  const BASE = window.LAVANYAA_BASE || document.querySelector('meta[name="base-url"]')?.content || '';

  /* ── LUXURY LOADER ── */
  const loader = document.getElementById('lc-loader');
  if (loader) {
    const shown = sessionStorage.getItem('lc_loader_shown');
    if (shown) {
      loader.classList.add('hide');
    } else {
      sessionStorage.setItem('lc_loader_shown', '1');
      window.addEventListener('load', function () {
        setTimeout(function () { loader.classList.add('hide'); }, 1600);
      });
      setTimeout(function () { loader.classList.add('hide'); }, 2600);
    }
  }

  /* ── SCROLL PROGRESS BAR ── */
  const progressBar = document.getElementById('lc-progress');
  function updateProgress() {
    if (!progressBar) return;
    const h = document.documentElement;
    const scrolled = (h.scrollTop) / (h.scrollHeight - h.clientHeight) * 100;
    progressBar.style.width = scrolled + '%';
  }
  window.addEventListener('scroll', updateProgress, { passive: true });

  /* ── HERO BG REVEAL ── */
  const heroBg = document.getElementById('hero-bg');
  if (heroBg) { setTimeout(() => heroBg.classList.add('loaded'), 200); }

  /* ── NAVBAR SCROLL STATE ── */
  const navbar = document.querySelector('.main-navbar');
  function onScrollNav() {
    if (!navbar) return;
    if (window.scrollY > 40) navbar.classList.add('scrolled');
    else navbar.classList.remove('scrolled');
  }
  window.addEventListener('scroll', onScrollNav, { passive: true });
  onScrollNav();

  /* MOBILE NAV */
  const toggle   = document.getElementById('mobile-menu-toggle');
  const drawer   = document.getElementById('mobile-nav');
  const overlay  = document.getElementById('mobile-overlay');
  const closeBtn = document.getElementById('mobile-nav-close');

  function openNav()  { drawer?.classList.add('open'); overlay?.classList.add('show'); document.body.style.overflow='hidden'; }
  function closeNav() { drawer?.classList.remove('open'); overlay?.classList.remove('show'); document.body.style.overflow=''; }

  toggle?.addEventListener('click', openNav);
  closeBtn?.addEventListener('click', closeNav);
  overlay?.addEventListener('click', closeNav);
  document.addEventListener('keydown', e => { if (e.key==='Escape') closeNav(); });

  /* MOBILE ACCORDION */
  document.querySelectorAll('.mobile-acc-trigger').forEach(btn => {
    btn.addEventListener('click', function () {
      const item = this.closest('.mobile-acc-item');
      const panel = item.querySelector('.mobile-acc-panel');
      const open = item.classList.contains('open');
      document.querySelectorAll('.mobile-acc-item.open').forEach(el => {
        el.classList.remove('open');
        el.querySelector('.mobile-acc-panel').style.maxHeight = null;
      });
      if (!open) { item.classList.add('open'); panel.style.maxHeight = panel.scrollHeight + 'px'; }
    });
  });

  /* MEGA MENU */
  let megaTimer;
  document.querySelectorAll('.sec-nav-item.has-mega').forEach(item => {
    item.addEventListener('mouseenter', () => {
      clearTimeout(megaTimer);
      document.querySelectorAll('.sec-nav-item.has-mega').forEach(el => {
        if (el !== item) el.classList.remove('mega-open');
      });
      item.classList.add('mega-open');
    });
    item.addEventListener('mouseleave', () => {
      megaTimer = setTimeout(() => { item.classList.remove('mega-open'); }, 150);
    });
  });

  /* SCROLL REVEAL */
  if ('IntersectionObserver' in window) {
    const obs = new IntersectionObserver(entries => {
      entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); } });
    }, { threshold: 0.1, rootMargin: '0px 0px -60px 0px' });
    document.querySelectorAll('.fade-up').forEach(el => obs.observe(el));
  } else {
    document.querySelectorAll('.fade-up').forEach(el => el.classList.add('visible'));
  }

  /* CART BUTTON */
  document.getElementById('cart-btn')?.addEventListener('click', () => {
    window.location.href = BASE + '/cart.php';
  });

  /* MARQUEE PAUSE */
  const marquee = document.querySelector('.lc-marquee-track');
  marquee?.parentElement?.addEventListener('mouseenter', () => marquee.style.animationPlayState='paused');
  marquee?.parentElement?.addEventListener('mouseleave', () => marquee.style.animationPlayState='running');

  /* SEARCH */
  function bindSearch(id) {
    const input = document.getElementById(id);
    if (!input) return;
    input.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        const keyword = this.value.trim();
        if (keyword.length > 0) {
          const base = document.querySelector('meta[name="base-url"]')?.content || '';
          window.location.href = base + '/category.php?cat=all&search=' + encodeURIComponent(keyword);
        }
      }
    });
  }
  bindSearch('site-search');
  bindSearch('mobile-search');

  /* QUOTE MODAL */
  const quoteModalEl = document.getElementById('quoteInquiryModal');
  const quoteWhatsapp = document.querySelector('.quote-whatsapp-btn');
  const quoteModal = quoteModalEl && window.bootstrap ? window.bootstrap.Modal.getOrCreateInstance(quoteModalEl) : null;

  function toggleQuoteBodyScroll(lock) {
    document.body.classList.toggle('modal-open', lock);
    document.documentElement.classList.toggle('modal-open', lock);
  }

  if (quoteModalEl) {
    quoteModalEl.addEventListener('shown.bs.modal', () => toggleQuoteBodyScroll(true));
    quoteModalEl.addEventListener('hidden.bs.modal', () => toggleQuoteBodyScroll(false));
    quoteModalEl.addEventListener('click', function (e) {
      if (e.target === quoteModalEl) {
        quoteModal?.hide();
      }
    });
    quoteModalEl.querySelector('.quote-modal-close')?.addEventListener('click', function (e) {
      e.preventDefault();
      quoteModal?.hide();
    });
  }

  document.querySelectorAll('a[href="#quote"], a[href="#quoteInquiryModal"]').forEach(link => {
    link.setAttribute('data-bs-toggle', 'modal');
    link.setAttribute('data-bs-target', '#quoteInquiryModal');
    link.addEventListener('click', function (e) {
      if (!quoteModal) return;
      e.preventDefault();
      quoteModal.show();
    });
  });

  function getQuoteMessage() {
    const name = document.getElementById('quoteName')?.value.trim() || '';
    const phone = document.getElementById('quotePhone')?.value.trim() || '';
    const businessType = document.getElementById('quoteBusinessType')?.value.trim() || '';
    const message = document.getElementById('quoteMessage')?.value.trim() || '';
    const lines = [
      'Hi Lavanyaa Creation, I want a commercial furniture quote.',
      name ? 'Name: ' + name : '',
      phone ? 'Phone: ' + phone : '',
      businessType ? 'Business Type: ' + businessType : '',
      message ? 'Message: ' + message : ''
    ].filter(Boolean);
    return lines.join('\n');
  }

  quoteWhatsapp?.addEventListener('click', function () {
    const number = this.dataset.whatsappNumber || '';
    this.href = 'https://wa.me/' + number + '?text=' + encodeURIComponent(getQuoteMessage());
  });

  if (quoteModalEl && window.location.hash === '#quoteInquiryModal' && window.bootstrap) {
    quoteModal.show();
  }

  if (quoteModal && quoteModalEl && !window.location.hash) {
    const quoteSeenKey = 'lavanyaa_quote_popup_seen';
    const alreadySeen = localStorage.getItem(quoteSeenKey) || sessionStorage.getItem(quoteSeenKey);
    if (!alreadySeen) {
      window.addEventListener('load', function () {
        setTimeout(function () {
          if (!localStorage.getItem(quoteSeenKey)) {
            quoteModal.show();
            localStorage.setItem(quoteSeenKey, '1');
            sessionStorage.setItem(quoteSeenKey, '1');
          }
        }, 1800);
      });
    }
  }

  /* CLIENTS CAROUSEL */
  document.querySelectorAll('.lc-clients-carousel').forEach(carousel => {
    const track = carousel.querySelector('.lc-clients-track');
    const slides = Array.from(track?.children || []);
    const prevBtn = carousel.querySelector('.lc-client-nav.prev');
    const nextBtn = carousel.querySelector('.lc-client-nav.next');
    const dotsWrap = carousel.querySelector('.lc-client-dots');

    if (!track || slides.length === 0) return;

    let index = 0;
    let autoplayId = null;

    function getVisibleCount() {
      if (window.innerWidth < 768) return 1;
      if (window.innerWidth < 1100) return 2;
      return 3;
    }

    function setSlideSizes() {
      const visibleCount = Math.min(getVisibleCount(), slides.length);
      const gap = 20;
      const size = (track.clientWidth - gap * (visibleCount - 1)) / visibleCount;
      slides.forEach(slide => {
        slide.style.flex = `0 0 ${size}px`;
        slide.style.maxWidth = `${size}px`;
      });
    }

    function renderDots() {
      if (!dotsWrap) return;
      dotsWrap.innerHTML = '';
      slides.forEach((_, dotIndex) => {
        const dot = document.createElement('button');
        dot.className = 'lc-client-dot';
        dot.type = 'button';
        dot.setAttribute('aria-label', `Go to client ${dotIndex + 1}`);
        dot.addEventListener('click', () => {
          index = dotIndex;
          updatePosition();
          resetAutoplay();
        });
        dotsWrap.appendChild(dot);
      });
    }

    function updateDots() {
      if (!dotsWrap) return;
      Array.from(dotsWrap.children).forEach((dot, dotIndex) => {
        dot.classList.toggle('active', dotIndex === index);
      });
    }

    function updatePosition() {
      const visibleCount = Math.min(getVisibleCount(), slides.length);
      const gap = 20;
      const size = (track.clientWidth - gap * (visibleCount - 1)) / visibleCount;
      const offset = index * (size + gap);
      track.style.transform = `translateX(-${offset}px)`;
      updateDots();
    }

    function nextSlide() {
      index = (index + 1) % slides.length;
      updatePosition();
    }

    function prevSlide() {
      index = (index - 1 + slides.length) % slides.length;
      updatePosition();
    }

    function resetAutoplay() {
      clearInterval(autoplayId);
      if (carousel.dataset.autoplay === 'false') return;
      autoplayId = setInterval(nextSlide, 4500);
    }

    prevBtn?.addEventListener('click', () => { prevSlide(); resetAutoplay(); });
    nextBtn?.addEventListener('click', () => { nextSlide(); resetAutoplay(); });

    carousel.addEventListener('mouseenter', () => clearInterval(autoplayId));
    carousel.addEventListener('mouseleave', () => resetAutoplay());

    window.addEventListener('resize', () => {
      setSlideSizes();
      updatePosition();
    });

    renderDots();
    setSlideSizes();
    updatePosition();
    resetAutoplay();
  });

  /* PRODUCT CARD IMAGE DRIFT */
  if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
    document.querySelectorAll('.lc-prod-card, .lc-coll-card').forEach(card => {
      let raf = null;
      card.addEventListener('mousemove', function (e) {
        const img = card.querySelector('img');
        if (!img) return;
        const rect = card.getBoundingClientRect();
        const x = (e.clientX - rect.left) / rect.width - 0.5;
        const y = (e.clientY - rect.top) / rect.height - 0.5;
        if (raf) cancelAnimationFrame(raf);
        raf = requestAnimationFrame(() => {
          img.style.transform = `scale(1.035) translate(${x * 3}px, ${y * 2}px)`;
        });
      });
      card.addEventListener('mouseleave', function () {
        if (raf) cancelAnimationFrame(raf);
        const img = card.querySelector('img');
        if (img) img.style.transform = '';
      });
    });
  }

})();
