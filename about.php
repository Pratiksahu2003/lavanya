<?php
/**
 * LAVANYAA CREATION — Our Story
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/functions/settings.php';

$page_title  = 'Our Story — Lavanyaa Creation';
$page_desc   = 'Established in 2019, Lavanyaa Creation crafts premium furniture and bespoke interior solutions for luxury residences, corporate offices, hotels, and hospitality spaces.';
$active_page = 'about';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<!-- DARK LUXURY DESIGN SYSTEM & ELEVATION -->
<style>
  /* ── TOKENS ── */
  :root {
    --bg:           #0E0E0F;
    --primary:      #C9A15B;
    --primary-dk:   var(--primary);
    --secondary:    #171719;
    --accent:       #C9A15B;
    --accent-lt:    #D9B97B;
    --text:         #F7F6F3;
    --text-mid:     #B8B8B8;
    --text-light:   #B8B8B8;
    --white:        #222225;
    --border:       #343437;
    --border-lt:    #28282B;
    --serif:        'Cormorant Garamond', Georgia, serif;
    --sans:         'Jost', system-ui, sans-serif;
    --ease-lux:     cubic-bezier(0.22, 1, 0.36, 1);
    --ease-out:     cubic-bezier(0.4, 0, 0.2, 1);
    --t:            0.35s;
    --r-sm:         4px;
    --r-md:         8px;
    --r-lg:         16px;
    --sh-sm:        0 10px 28px rgba(0,0,0,.28);
    --sh-md:        0 18px 48px rgba(0,0,0,.38);
    --sh-lg:        0 28px 90px rgba(0,0,0,.5);
  }

  /* ── SECTION 1: HERO BANNER ── */
  .lc-about-hero {
    position: relative;
    min-height: 75vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(180deg, rgba(14,14,15,0.35) 0%, rgba(14,14,15,0.85) 100%);
                /* url('https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?q=80&w=2000&auto=format&fit=crop') center/cover no-repeat; */
    padding: 140px 24px 90px;
    text-align: center;
    border-bottom: 1px solid var(--border-lt);
  }
  .lc-about-hero-inner {
    max-width: 860px;
    margin: 0 auto;
    z-index: 2;
  }
  .lc-breadcrumb {
    font-family: var(--sans);
    font-size: 0.8rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    margin-bottom: 28px;
  }
  .lc-breadcrumb a {
    color: var(--text-mid);
    text-decoration: none;
    transition: color var(--t) var(--ease-out);
  }
  .lc-breadcrumb a:hover {
    color: var(--primary);
  }
  .lc-breadcrumb-sep {
    margin: 0 10px;
    color: var(--border);
  }
  .lc-eyebrow {
    font-family: var(--sans);
    font-size: 0.75rem;
    letter-spacing: 0.35em;
    text-transform: uppercase;
    font-weight: 500;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .lc-about-hero h1 {
    font-family: var(--serif);
    font-size: clamp(2.8rem, 5vw, 4.5rem);
    line-height: 1.12;
    font-weight: 300;
    color: var(--text);
    margin-bottom: 24px;
    letter-spacing: -0.01em;
  }
  .lc-about-hero h1 em {
    font-style: italic;
    font-weight: 400;
    color: var(--primary);
  }
  .lc-about-hero p {
    font-family: var(--sans);
    font-size: clamp(1rem, 1.5vw, 1.2rem);
    line-height: 1.8;
    color: var(--text-mid);
    max-width: 620px;
    margin: 0 auto;
    font-weight: 300;
  }

  /* ── SECTION 2: EDITORIAL STORY ── */
  .section-pad {
    padding: 120px 0;
  }
  .lc-about-copy p {
    font-family: var(--sans);
    font-size: 1.08rem;
    line-height: 2;
    color: var(--text-light);
    margin-bottom: 28px;
    font-weight: 300;
    letter-spacing: 0.01em;
  }
  .lc-story-body p:first-of-type::first-letter {
    font-family: var(--serif);
    font-size: 3.8rem;
    float: left;
    line-height: 0.8;
    padding-right: 16px;
    padding-top: 4px;
    color: var(--primary);
  }
  /* Architectural Quote Highlight */
  .lc-about-copy > div:first-child p {
    padding: 36px 40px;
    background: var(--secondary);
    border-left: 2px solid var(--primary);
    border-radius: var(--r-sm);
    box-shadow: var(--sh-sm);
    color: var(--text) !important;
  }

  /* ── SECTION 3: CORE VALUES ── */
  .lc-heading {
    font-family: var(--serif);
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: 300;
    color: var(--text);
    margin-bottom: 12px;
  }
  .lc-why-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 32px;
    background: transparent !important;
  }
  .lc-why-item {
    background: var(--secondary) !important;
    padding: 52px 36px;
    border: 1px solid var(--border-lt);
    border-radius: var(--r-md);
    transition: transform 0.6s var(--ease-lux), border-color 0.6s var(--ease-lux), box-shadow 0.6s var(--ease-lux);
    position: relative;
    box-shadow: var(--sh-sm);
  }
  .lc-why-item:hover {
    transform: translateY(-8px);
    border-color: var(--primary);
    box-shadow: var(--sh-lg);
  }
  .lc-why-icon {
    font-size: 2.2rem;
    margin-bottom: 28px;
    display: inline-block;
  }
  .lc-why-item h4 {
    font-family: var(--serif);
    font-size: 1.5rem;
    font-weight: 400;
    margin-bottom: 16px;
    color: var(--text) !important;
  }
  .lc-why-item p {
    font-family: var(--sans);
    font-size: 0.95rem;
    line-height: 1.8;
    color: var(--text-mid) !important;
    margin: 0;
    font-weight: 300;
  }

  /* ── SECTION 4: EQUAL & PARALLEL GALLERY ── */
  .section-pad-sm {
    padding: 120px 0 !important;
    background: var(--bg) !important;
  }
  /* Force 3 equal columns and uniform spacing */
  .section-pad-sm > .lc-container > div {
    display: grid !important;
    grid-template-columns: repeat(3, 1fr) !important;
    gap: 28px !important;
    align-items: stretch !important;
  }
  /* Perfectly equal, parallel image cards */
  .section-pad-sm img {
    width: 100% !important;
    height: 440px !important;
    object-fit: cover !important;
    margin: 0 !important;
    transition: transform 0.8s var(--ease-lux), filter 0.8s var(--ease-lux), box-shadow 0.8s var(--ease-lux), border-color 0.8s var(--ease-lux);
    border: 1px solid var(--border-lt);
    box-shadow: var(--sh-md);
    border-radius: var(--r-md) !important;
    filter: saturate(0.8) brightness(0.9) !important;
  }
  .section-pad-sm img:hover {
    transform: translateY(-8px);
    filter: saturate(1) brightness(1) !important;
    box-shadow: var(--sh-lg);
    border-color: var(--primary);
  }

  /* ── SECTION 5: CTA ── */
  .lc-cta {
    padding: 120px 24px;
    background: linear-gradient(180deg, var(--bg) 0%, var(--secondary) 100%);
    border-top: 1px solid var(--border-lt);
    text-align: center;
  }
  .lc-cta-inner {
    max-width: 740px;
    margin: 0 auto;
  }
  .lc-cta h2 {
    font-family: var(--serif);
    font-size: clamp(2.2rem, 4vw, 3.5rem);
    font-weight: 300;
    line-height: 1.15;
    color: var(--text);
    margin-bottom: 20px;
  }
  .lc-cta p {
    font-family: var(--sans);
    font-size: 1.05rem;
    line-height: 1.8;
    color: var(--text-mid);
    margin-bottom: 40px;
    font-weight: 300;
  }
  .lc-cta-btns {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
  }
  .btn-primary-lc {
    background: var(--primary);
    color: #000000 !important;
    padding: 18px 38px;
    font-family: var(--sans);
    font-size: 0.8rem;
    font-weight: 500;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    text-decoration: none;
    border-radius: var(--r-sm);
    transition: all var(--t) var(--ease-out);
    display: inline-flex;
    align-items: center;
    gap: 10px;
    box-shadow: var(--sh-sm);
  }
  .btn-primary-lc:hover {
    background: var(--accent-lt);
    transform: translateY(-2px);
    box-shadow: var(--sh-md);
  }
  .btn-ghost-lc {
    background: transparent;
    border: 1px solid var(--border);
    color: var(--text) !important;
    padding: 18px 38px;
    font-family: var(--sans);
    font-size: 0.8rem;
    font-weight: 400;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    text-decoration: none;
    border-radius: var(--r-sm);
    transition: all var(--t) var(--ease-out);
  }
  .btn-ghost-lc:hover {
    border-color: var(--primary);
    color: var(--primary) !important;
    background: rgba(201, 161, 91, 0.05);
  }

  /* ── RESPONSIVE OVERRIDES ── */
  @media (max-width: 992px) {
    .lc-why-grid { grid-template-columns: 1fr; gap: 24px; }
    .section-pad-sm > .lc-container > div { grid-template-columns: 1fr !important; gap: 24px !important; }
    .section-pad-sm img { height: 380px !important; margin: 0 !important; }
    .lc-about-copy > div:first-child p { padding: 28px 24px; }
  }
  
</style>

<!-- ABOUT HERO -->
<section class="lc-about-hero">
  <div class="lc-about-hero-inner">
    <div class="lc-breadcrumb fade-up">
      <a href="<?php echo BASE_URL; ?>/index.php">Home</a>
      <span class="lc-breadcrumb-sep">/</span>
      <span style="color:rgba(255,255,255,.6);">Our Story</span>
    </div>
    <div class="lc-eyebrow fade-up fade-up-d1" style="color:var(--accent-lt);">Since 2019</div>
    <h1 class="fade-up fade-up-d2">A Passion for <em>Creating Spaces<br>People Truly Love</em></h1>
    <p class="fade-up fade-up-d3">Established with a vision to redefine modern spaces through thoughtfully crafted furniture and innovative interior solutions.</p>
  </div>
</section>

<!-- STORY NARRATIVE -->
<section class="section-pad" style="background:var(--white);">
  <div class="lc-container">
    <div class="lc-about-copy" style="max-width:780px;margin:0 auto;">

      <div class="fade-up" style="margin-bottom:48px;">
        <p style="font-family:var(--serif);font-size:1.5rem;line-height:1.6;color:var(--text);font-weight:400;font-style:italic;">
          "Our journey began with a simple belief — great spaces deserve exceptional furniture."
        </p>
      </div>

      <div class="lc-story-body fade-up fade-up-d1">
        <p>What started as a vision to bring thoughtfully designed furniture into modern spaces has gradually evolved into a brand committed to quality, craftsmanship, and timeless design. From the very beginning, our focus has been clear — to create furniture that not only enhances the beauty of a space but also adds comfort, functionality, and lasting value.</p>

        <p>Over the years, we have worked closely with clients across residential, corporate, and hospitality sectors, understanding their unique requirements and transforming ideas into reality. Every project has contributed to our growth, helping us refine our expertise in design, material selection, and execution.</p>

        <p>At Lavanyaa Creation, we believe furniture is more than just décor — it shapes the way people live, work, and connect. This belief drives us to create pieces that balance aesthetics with purpose.</p>

        <p>Our strength lies in our attention to detail, premium-quality materials, and commitment to excellence. From luxurious living spaces and elegant dining areas to sophisticated office environments and hospitality projects, every creation reflects our passion for craftsmanship.</p>

        <p>As we continue to grow, our mission remains unchanged — to deliver furniture solutions that inspire, perform, and stand the test of time.</p>
      </div>

      <div class="fade-up fade-up-d2" style="text-align:center;padding:40px 0 0;border-top:1px solid var(--border-lt);margin-top:40px;">
        <p style="font-family:var(--serif);font-size:1.3rem;color:var(--primary);font-style:italic;margin-bottom:6px;">For us, this is not just business.<br>It is a passion for creating spaces people truly love.</p>
        <p style="font-size:.75rem;letter-spacing:.18em;text-transform:uppercase;color:var(--accent);">— This is the story of Lavanyaa Creation</p>
      </div>

    </div>
  </div>
</section>

<!-- VALUES STRIP -->
<section class="section-pad" style="background:var(--bg);">
  <div class="lc-container">
    <div style="text-align:center;margin-bottom:56px;">
      <div class="lc-eyebrow fade-up" style="justify-content:center;">What Drives Us</div>
      <h2 class="lc-heading fade-up fade-up-d1" style="text-align:center;">Our Core Values</h2>
    </div>
    <div class="lc-why-grid" style="background:var(--border-lt);">
      <div class="lc-why-item fade-up fade-up-d1" style="background:var(--white);">
        <div class="lc-why-icon" style="color:var(--primary);"><i class="bi bi-award"></i></div>
        <h4 style="color:var(--text);">Uncompromising Quality</h4>
        <p style="color:var(--text-dark);">We carefully select premium-quality materials that enhance durability and elevate the final product's elegance and character.</p>
      </div>
      <div class="lc-why-item fade-up fade-up-d2" style="background:var(--white);">
        <div class="lc-why-icon" style="color:var(--primary);"><i class="bi bi-lightbulb"></i></div>
        <h4 style="color:var(--text);">Continuous Innovation</h4>
        <p style="color:var(--text-dark);">We continuously evolve with emerging design trends, developing contemporary furniture that redefines modern living and workspaces.</p>
      </div>
      <div class="lc-why-item fade-up fade-up-d3" style="background:var(--white);">
        <div class="lc-why-icon" style="color:var(--primary);"><i class="bi bi-people"></i></div>
        <h4 style="color:var(--text);">Trusted Partnership</h4>
        <p style="color:var(--text-dark);">A trusted partner for architects, designers, and clients seeking world-class furniture solutions, built on years of expertise.</p>
      </div>
    </div>
  </div>
</section>

<!-- GALLERY STRIP -->
<section class="section-pad-sm" style="background:var(--white);padding-bottom:0;">
  <div class="lc-container">
  <div class="about-images">

    <div class="about-image">
      <img
        src="assets/images/about1.jpeg"
        alt="Premium Living Space"
        loading="lazy"
      >
    </div>

    <div class="about-image">
      <img
        src="assets/images/about2.jpeg"
        alt="Elegant Dining"
        loading="lazy"
      >
    </div>

    <div class="about-image">
      <img
        src="assets/images/about3.jpeg"
        alt="Office Interior"
        loading="lazy"
      >
    </div>

  </div>

</div>



  </div>
</section>

<!-- CTA -->
<section class="lc-cta">
  <div class="lc-cta-inner">
    <div class="lc-eyebrow fade-up">Work With Us</div>
    <h2 class="lc-heading fade-up fade-up-d1">Let's Create Something Extraordinary</h2>
    <p class="fade-up fade-up-d2">Whether it's a single statement piece or a complete commercial fit-out, our team is ready to bring your vision to life.</p>
    <div class="lc-cta-btns fade-up fade-up-d3">
      <a href="<?php echo BASE_URL; ?>/contact.php" class="btn-primary-lc">Contact Us <i class="bi bi-arrow-right"></i></a>
      <a href="<?php echo BASE_URL; ?>/category.php?cat=all" class="btn-ghost-lc">View Collections</a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>