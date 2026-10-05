<?php
/**
 * LAVANYAA CREATION — Homepage
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/functions/products.php';
require_once __DIR__ . '/functions/categories.php';
require_once __DIR__ . '/functions/settings.php';
require_once __DIR__ . '/functions/uploads.php';

$page_title   = 'Lavanyaa Creation — Premium Furniture Services';
$page_desc    = 'Distinguished name in premium furniture design and bespoke interior solutions since 2019. Transforming residences, offices and hospitality spaces across India.';
$active_page  = 'index';

$hero_kicker  = getSetting('hero_kicker', 'Est. 2019 · Premium Furniture Services');
$hero_heading = getSetting('hero_heading', "Crafted for the\nExtraordinary");
$hero_subheading = getSetting('hero_subheading', 'Premium furniture design and bespoke interior solutions for luxury residences, corporate offices, and world-class hospitality spaces.');
$hero_btn_text = getSetting('hero_button_text', 'Explore Collections');
$hero_btn_link = getSetting('hero_button_link', 'category.php?cat=all');
$hero_second_btn_text = getSetting('hero_secondary_button_text', 'Request a Quote');
$hero_second_btn_link = getSetting('hero_secondary_button_link', '#quote');
$about_title = getSetting('about_preview_title', 'A Legacy Built on Craftsmanship and Vision');
$about_preview = getSetting('about_preview', 'Lavanyaa Creation is a distinguished name in the world of premium furniture design and bespoke interior solutions, specializing in transforming modern environments into sophisticated, functional, and inspiring spaces.');
$about_quote = getSetting('about_preview_quote', 'Great spaces deserve exceptional furniture — that belief drives everything we create.');
$about_extra_1 = getSetting('about_preview_extra_1', 'From corporate offices and luxury residences to hotels, restaurants, and large-scale commercial projects, we craft furniture that seamlessly blends aesthetics, comfort, and performance.');
$about_extra_2 = getSetting('about_preview_extra_2', 'Built on strong values, technical expertise, and years of industry experience, Lavanyaa Creation has established itself as a trusted partner for architects, designers, and clients seeking world-class furniture solutions.');
$about_image_main = getImageUrl(
    getSetting(
        'about_preview_image_main',
        BASE_URL . '/assets/images/big.jpg'
    )
);

$about_image_accent = getImageUrl(
    getSetting(
        'about_preview_image_accent',
        BASE_URL . '/assets/images/small.jpg'
    )
);
$about_year = getSetting('about_preview_year', '2019');
$why_title = getSetting('why_choose_title', 'Why Lavanyaa Creation');
$why_items = getJsonSetting('why_choose_items', []);
$why_points_raw = getSetting('why_choose_points', "Premium Quality Products\nExclusive & Trendy Designs\nAffordable Prices\nSuperior Craftsmanship\nTimely Delivery\nCustomer-First Approach\nSecure Shopping Experience\nTrusted Quality & Reliability\nDedicated Customer Support\n100% Customer Satisfaction");
$why_points = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $why_points_raw))));
$collections_eyebrow = getSetting('collections_eyebrow', 'Our Collections');
$collections_title = getSetting('collections_title', 'Explore Every Space');
$collections_text = getSetting('collections_text', '');
$collections_cta_text = getSetting('collections_cta_text', 'View All Collections');
$collections_cta_link = getSetting('collections_cta_link', BASE_URL . '/category.php?cat=all');
$industries_eyebrow = getSetting('industries_eyebrow', 'Sectors We Transform');
$industries_title = getSetting('industries_title', 'Spaces We Specialise In');
$industries_text = getSetting('industries_text', 'From corporate campuses to boutique hospitality — complete furniture solutions for every premium environment.');
$inquiry_title = getSetting('inquiry_section_title', 'Transform Your Space Today');
$inquiry_text = getSetting('inquiry_section_text', 'Share your vision with our design consultants. We will craft a bespoke furniture solution from concept to installation.');
$banners      = getHomepageBanners();
$banner       = $banners[0] ?? null;
$hero_img     = getImageUrl($banner['image'] ?? 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?w=1800&q=85');
$hero_title   = $banner['title'] ?? $hero_heading;
$hero_label   = $banner['label'] ?? $hero_kicker;
$hero_secondary_text = $banner['subtitle'] ?? 'Where Spaces Find Their Soul';
$hero_primary_text = $banner['btn1_text'] ?? $hero_btn_text;
$hero_primary_link = $banner['btn1_url'] ?? $hero_btn_link;
$hero_secondary_btn_text = $banner['btn2_text'] ?? $hero_second_btn_text;
$hero_secondary_btn_link = $banner['btn2_url'] ?? $hero_second_btn_link;
$featured     = getFeaturedProducts(8);
$categories   = getAllCategories();
$industries   = getIndustries();
$testimonials = getApprovedReviews(4);
if (empty($testimonials)) {
  $testimonials = [];
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>
<style>
.lc-clients-carousel {
  width: 100%;
  overflow: hidden;
}

/* =========================
   INITIAL LOGOS
   ========================= */

.lc-client-initial {
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
  overflow: hidden;
  padding: 10px 20px;
  box-sizing: border-box;
}

.lc-client-initial .lc-client-slide {
  display: flex;
  width: 160px;
  flex: 0 0 160px;
  justify-content: center;
  align-items: center;
}


/* =========================
   MOVING MARQUEE
   ========================= */

.lc-client-moving {
  display: none;
  width: 100%;
  overflow: hidden;
}

.lc-client-marquee {
  width: 100%;
}


/* =========================
   LOGO CARD
   ========================= */

.lc-client-slide {
  display: inline-block;
  width: 250px;
  margin-right: 20px;
  vertical-align: top;
}

.lc-client-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 150px;
}

.lc-client-logo-wrap {
  width: 150px;
  height: 80px;

  display: flex;
  align-items: center;
  justify-content: center;
}

.lc-client-logo {
  max-width: 100%;
  max-height: 70px;

  width: auto;
  height: auto;

  object-fit: contain;
}

.lc-client-name {
  margin-top: 12px;
  text-align: center;
  display: block;
}


/* =========================
   MOBILE
   ========================= */

@media (max-width: 768px) {

  .lc-client-initial {
    gap: 10px;
    padding: 10px;
  }

  .lc-client-initial .lc-client-slide {
    width: 140px;
    flex: 0 0 140px;
  }

  .lc-client-slide {
    width: 150px;
    margin-right: 10px;
  }

  .lc-client-logo-wrap {
    width: 130px;
    height: 70px;
  }

  .lc-client-logo {
    max-width: 100%;
    max-height: 60px;
  }

  .lc-client-item {
    min-height: 130px;
  }

  .lc-client-name {
    margin-top: 8px;
    font-size: 14px;
  }
}
</style>

<!-- ═══════════════════════════════════════
     HERO
════════════════════════════════════════ -->
<section class="lc-hero">
  <div class="lc-hero-bg" id="hero-bg" style="background-image:url('<?php echo htmlspecialchars($hero_img); ?>');"></div>
  <div class="lc-hero-overlay"></div>
  <div class="lc-hero-content">
    <div class="lc-hero-kicker lc-hero-reveal"><span></span><?php echo htmlspecialchars($hero_label); ?></div>
    <h1 class="lc-hero-reveal lc-hero-reveal-d1"><?php echo nl2br(htmlspecialchars($hero_title)); ?></h1>
    <p class="lc-hero-tagline lc-hero-reveal lc-hero-reveal-d2">Luxury furniture, crafted for spaces with presence.</p>
    <p class="lc-hero-sub lc-hero-reveal lc-hero-reveal-d3"><?php echo htmlspecialchars($hero_secondary_text); ?></p>
    <div class="lc-hero-btns lc-hero-reveal lc-hero-reveal-d4">
      <a href="<?php echo htmlspecialchars($hero_primary_link); ?>" class="btn-primary-lc"><?php echo htmlspecialchars($hero_primary_text); ?> <i class="bi bi-arrow-right"></i></a>
      <a href="<?php echo htmlspecialchars($hero_secondary_btn_link); ?>" class="btn-outline-lc"><?php echo htmlspecialchars($hero_secondary_btn_text); ?></a>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════
     MARQUEE
════════════════════════════════════════ -->
<div class="lc-marquee" aria-hidden="true">
  <div class="lc-marquee-track">
    <span>LAVANYAA CREATION</span><span class="dot">✦</span>
    <span>PREMIUM FURNITURE</span><span class="dot">✦</span>
    <span>BESPOKE INTERIORS</span><span class="dot">✦</span>
    <span>EST. 2019</span><span class="dot">✦</span>
    <span>PAN INDIA DELIVERY</span><span class="dot">✦</span>
    <span>LUXURY CRAFTSMANSHIP</span><span class="dot">✦</span>
    <span>LAVANYAA CREATION</span><span class="dot">✦</span>
    <span>PREMIUM FURNITURE</span><span class="dot">✦</span>
    <span>BESPOKE INTERIORS</span><span class="dot">✦</span>
    <span>EST. 2019</span><span class="dot">✦</span>
    <span>PAN INDIA DELIVERY</span><span class="dot">✦</span>
    <span>LUXURY CRAFTSMANSHIP</span><span class="dot">✦</span>
  </div>
</div>

<!-- ═══════════════════════════════════════
     BRAND INTRO NUMBERS
════════════════════════════════════════ -->
<div class="lc-brand-intro">
  <div class="lc-intro-grid">
    <div class="lc-intro-cell fade-up">
      <div class="num">5+</div>
      <h4>Years of Excellence</h4>
      <p>Established in 2019, crafting premium furniture with uncompromising quality standards.</p>
    </div>
    <div class="lc-intro-cell fade-up fade-up-d1">
      <div class="num">700+</div>
      <h4>Premium Products</h4>
      <p>An expansive catalogue spanning living, dining, bedroom, office and hospitality furniture.</p>
    </div>
    <div class="lc-intro-cell fade-up fade-up-d2">
      <div class="num">✨</div>
      <h4>Custom Design Solutions</h4>
      <p>Tailor-made furniture designed to perfectly match your space, lifestyle, and vision.</p>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════
     COLLECTIONS GRID
════════════════════════════════════════ -->
<section class="lc-collections">
  <div class="lc-collections-hd">
    <div>
      <div class="lc-eyebrow fade-up"><?php echo htmlspecialchars($collections_eyebrow); ?></div>
      <h2 class="lc-heading fade-up fade-up-d1"><?php echo htmlspecialchars($collections_title); ?></h2>
      <?php if ($collections_text): ?><p class="lc-sub fade-up fade-up-d2"><?php echo htmlspecialchars($collections_text); ?></p><?php endif; ?>
    </div>
    <a href="<?php echo htmlspecialchars($collections_cta_link); ?>" class="btn-outline-lc fade-up fade-up-d2"><?php echo htmlspecialchars($collections_cta_text); ?> <i class="bi bi-arrow-right"></i></a>
  </div>

  <?php
  // $cat_imgs = [
  //   'living'    => 'https://plus.unsplash.com/premium_photo-1684338795288-097525d127f0?q=80&w=871',
  //   'bedroom'   => 'https://images.unsplash.com/photo-1680503146476-abb8c752e1f4?q=80&w=870&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
  //   'dining'    => 'https://plus.unsplash.com/premium_photo-1684445034959-b3faeb4597d2?q=80&w=387&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
  //   'office'    => 'https://images.unsplash.com/photo-1571624436279-b272aff752b5?q=80&w=872&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
  //   'commercial'=> 'https://images.unsplash.com/photo-1591944173662-85fbc5a495a1?q=80&w=435&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
  //   'decor'     => 'https://images.unsplash.com/photo-1532372576444-dda954194ad0?w=800&q=85',
  // ];
  $cat_imgs = [
    'living'     => BASE_URL . '/assets/images/living.jpeg',
    'bedroom'    => BASE_URL . '/assets/images/bedroom.jpeg',
    'dining'     => BASE_URL . '/assets/images/dining.jpg',
    'office'     => BASE_URL . '/assets/images/office.jpg',
    'commercial' => BASE_URL . '/assets/images/hospitality.jpg',
    'decor'      => BASE_URL . '/assets/images/decor.jpeg',
    'hospitality' => BASE_URL . '/assets/images/hospitality.jpg',
];
  ?>
  <div class="lc-coll-grid">
    <?php foreach (array_slice($categories,0,5) as $i => $c):
      $imgUrl = getImageUrl($c['image'] ?? ($cat_imgs[$c['slug']] ?? 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&q=85'));
    ?>
    <a href="<?php echo BASE_URL; ?>/category.php?cat=<?php echo urlencode($c['slug']); ?>" class="lc-coll-card fade-up fade-up-d<?php echo min($i+1,4); ?>">
      <img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="<?php echo htmlspecialchars($c['name']); ?>" loading="<?php echo $i===0?'eager':'lazy'; ?>">
      <div class="lc-coll-overlay">
        <div class="lc-coll-name"><?php echo htmlspecialchars($c['name']); ?></div>
        <?php if (!empty($c['description'])): ?><div class="lc-coll-desc"><?php echo htmlspecialchars($c['description']); ?></div><?php endif; ?>
        <div class="lc-coll-cta">Explore <i class="bi bi-arrow-right"></i></div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- ═══════════════════════════════════════
     BRAND STORY
════════════════════════════════════════ -->
<section class="lc-story">
  <div class="lc-story-grid lc-container">
    <div class="lc-story-imgs fade-up">
      <div class="lc-story-year">
        <span class="y"><?php echo htmlspecialchars($about_year); ?></span>
        <span class="l">Founded</span>
      </div>
      <img
  src="<?php echo htmlspecialchars($about_image_main); ?>"
  alt="Lavanyaa Creation Showroom"
  class="lc-story-img-main"
  loading="lazy"
  style="transform: scaleX(-1);"
>
      <img src="<?php echo htmlspecialchars($about_image_accent); ?>" alt="Luxury Interior" class="lc-story-img-accent" loading="lazy">
    </div>
    <div class="lc-story-copy fade-up fade-up-d2">
      <div class="lc-eyebrow">Our Philosophy</div>
      <h2 class="lc-heading"><?php echo htmlspecialchars($about_title); ?></h2>
      <div class="lc-divider"></div>
      <div class="lc-story-body">
        <p><?php echo htmlspecialchars($about_preview); ?></p>
        <div class="lc-story-quote">"<?php echo htmlspecialchars($about_quote); ?>"</div>
        <?php if ($about_extra_1): ?><p><?php echo htmlspecialchars($about_extra_1); ?></p><?php endif; ?>
        <?php if ($about_extra_2): ?><p><?php echo htmlspecialchars($about_extra_2); ?></p><?php endif; ?>
      </div>
      <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:36px;">
        <a href="<?php echo BASE_URL; ?>/about.php" class="btn-primary-lc">Our Story <i class="bi bi-arrow-right"></i></a>
        <a href="<?php echo BASE_URL; ?>/contact.php" class="btn-outline-lc">Get in Touch</a>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════
     FEATURED PRODUCTS
════════════════════════════════════════ -->
<?php if (!empty($featured)): ?>
<section class="lc-products-section">
  <div class="lc-products-hd">
    <div>
      <div class="lc-eyebrow fade-up">Editor's Choice</div>
      <h2 class="lc-heading fade-up fade-up-d1">Featured Creations</h2>
    </div>
    <a href="<?php echo BASE_URL; ?>/category.php?cat=all" class="btn-outline-lc fade-up fade-up-d2">View All <i class="bi bi-arrow-right"></i></a>
  </div>
  <div class="lc-products-grid" style="max-width:1380px;margin:0 auto;padding:0 24px;">
    <?php foreach ($featured as $i => $p):
      $img = getImageUrl($p['primary_image'] ?? 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&q=80');
      $d   = 'fade-up-d'.(($i%4)+1);
    ?>
    <div class="lc-prod-card fade-up <?php echo $d; ?>">
      <a href="<?php echo BASE_URL; ?>/product.php?slug=<?php echo urlencode($p['slug']); ?>" class="lc-prod-img-wrap">
        <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" loading="lazy">
        <?php if ($p['is_new']): ?>
        <span class="lc-prod-badge">New</span>
        <?php elseif ($p['is_bestseller']): ?>
        <span class="lc-prod-badge accent">Bestseller</span>
        <?php endif; ?>
      </a>
      <div class="lc-prod-body">
        <div class="lc-prod-meta"><?php echo htmlspecialchars($p['product_code']); ?> · <?php echo htmlspecialchars($p['category_name']??''); ?></div>
        <a href="<?php echo BASE_URL; ?>/product.php?slug=<?php echo urlencode($p['slug']); ?>" class="lc-prod-name"><?php echo htmlspecialchars($p['name']); ?></a>
        <p class="lc-prod-desc"><?php echo htmlspecialchars($p['short_desc'] ?? substr($p['description']??'',0,110)); ?></p>
        <div class="lc-prod-actions">
          <a href="https://wa.me/<?php echo getSetting('whatsapp','917042704454'); ?>?text=<?php echo rawurlencode("Hi! I'm interested in {$p['name']} (Code: {$p['product_code']}). Please share details."); ?>"
             target="_blank" class="btn-enquire"><i class="bi bi-whatsapp"></i> Enquire</a>
          <button class="btn-cart"
                  data-product-id="<?php echo $p['id']; ?>"
                  data-product-code="<?php echo htmlspecialchars($p['product_code']); ?>"
                  data-product-name="<?php echo htmlspecialchars($p['name']); ?>"
                  data-product-image="<?php echo htmlspecialchars($img); ?>"
                  aria-label="Add to cart">
            <i class="bi bi-bag-plus"></i>
          </button>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════
     INDUSTRIES
════════════════════════════════════════ -->
<section class="lc-industries">
  <div class="lc-container">
    <div class="lc-industries-head">
      <div>
        <div class="lc-eyebrow fade-up"><?php echo htmlspecialchars($industries_eyebrow); ?></div>
        <h2 class="lc-heading fade-up fade-up-d1"><?php echo htmlspecialchars($industries_title); ?></h2>
        <div class="lc-divider fade-up fade-up-d2"></div>
        <p class="lc-sub fade-up fade-up-d3"><?php echo htmlspecialchars($industries_text); ?></p>
      </div>
      <div class="text-end fade-up fade-up-d2">
        <a href="#" class="btn-outline-lc" data-bs-toggle="modal" data-bs-target="#quoteInquiryModal">Bulk Project Enquiry <i class="bi bi-arrow-right"></i></a>
      </div>
    </div>
    <div class="lc-ind-grid">
      <?php foreach ($industries as $i => $ind): $d='fade-up-d'.(($i%3)+1); ?>
      <div class="lc-ind-card fade-up <?php echo $d; ?>">
        <div class="lc-ind-icon"><?php echo $ind['icon']; ?></div>
        <h5><?php echo htmlspecialchars($ind['name']); ?></h5>
        <p><?php echo htmlspecialchars($ind['description']); ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="lc-why">
    <div class="lc-why-inner">

        <div class="lc-eyebrow">Our Difference</div>

        <h2 class="lc-heading">
            <?php echo htmlspecialchars($why_title); ?>
        </h2>

        <p class="lc-why-desc">
            Premium furniture solutions shaped around quality, trust and a seamless client experience.
        </p>

        <div class="lc-why-grid">

            <?php foreach(array_slice($why_points,0,10) as $point): ?>

            <div class="lc-why-card">

                <div class="icon">
                    <i class="bi bi-check2"></i>
                </div>

                <h5><?php echo htmlspecialchars($point); ?></h5>

            </div>

            <?php endforeach; ?>

        </div>

    </div>
</section>

<!-- ═══════════════════════════════════════
     TESTIMONIALS
════════════════════════════════════════ -->
<?php if (!empty($testimonials)): ?>
<section class="lc-testimonials">
  <div class="lc-container">
    <div style="text-align:center;margin-bottom:0;">
      <div class="lc-eyebrow fade-up" style="justify-content:center;">Client Stories</div>
      <h2 class="lc-heading fade-up fade-up-d1" style="text-align:center;">What Our Clients Say</h2>
    </div>
    <div class="lc-test-grid">
      <?php foreach ($testimonials as $i => $t): $d='fade-up-d'.(($i%4)+1); ?>
      <div class="lc-test-card fade-up <?php echo $d; ?>">
        <div class="lc-test-stars"><?php for($r=0;$r<(int)$t['rating'];$r++) echo '★'; ?></div>
        <p class="lc-test-text"><?php echo htmlspecialchars(preg_replace('/nova\s*homz/i', 'Lavanyaa Creation', $t['review'])); ?></p>
        <div class="lc-test-name"><?php echo htmlspecialchars($t['name']); ?></div>
        <?php if (!empty($t['company_name'])): ?><div class="lc-test-company"><?php echo htmlspecialchars($t['company_name']); ?></div><?php endif; ?>
        <?php if (!empty($t['designation'])): ?><div class="lc-test-company" style="color:var(--accent);"><i class="bi bi-person-badge-fill me-1"></i><?php echo htmlspecialchars($t['designation']); ?></div><?php endif; ?>
        <?php if (!empty($t['city'])): ?><div class="lc-test-company" style="color:var(--accent);"><i class="bi bi-geo-alt-fill me-1"></i><?php echo htmlspecialchars($t['city']); ?></div><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════
     PREMIUM CLIENTS
════════════════════════════════════════ -->
<section class="lc-clients">
  <div class="lc-clients-inner">
    <div style="text-align:center;">
      <div class="lc-eyebrow fade-up" style="justify-content:center;">Trusted By</div>
      <h2 class="lc-heading fade-up fade-up-d1" style="text-align:center;">Our Premium Clients</h2>
      <div class="lc-divider fade-up fade-up-d2" style="margin:20px auto;"></div>
      <p class="lc-sub fade-up fade-up-d3" style="text-align:center;margin:0 auto;">Spaces we have transformed for India's most discerning brands and institutions.</p>
    </div>
    <?php
    $clients = [];
    try {
      $clients = getDB()->query("SELECT * FROM premium_clients WHERE status = 1 ORDER BY display_order ASC, id ASC")->fetchAll();
    } catch (PDOException $e) {
      error_log('LAVANYAA CREATION clients query: ' . $e->getMessage());
    }
    if (empty($clients)) {
      $clients = [
        ['name' => 'The Oberoi', 'logo' => null],
        ['name' => 'IBIS Hotel', 'logo' => null],
        ['name' => 'Lemon Tree', 'logo' => null],
        ['name' => 'Vision Hospitality', 'logo' => null],
        ['name' => 'CPRI', 'logo' => null],
        ['name' => 'Sun Glassworks Pvt Ltd', 'logo' => null],
        ['name' => 'Honda India Powder Products Ltd', 'logo' => null],
      ];
    }
    ?>
<div class="lc-clients-carousel">

  <!-- Initial logos: visible in center -->
  <div class="lc-client-initial" id="clientsInitial">

    <?php foreach ($clients as $cl): ?>

      <span class="lc-client-slide">

        <span class="lc-client-item">

          <span class="lc-client-logo-wrap">

            <?php if (!empty($cl['logo'])): ?>

              <img
                class="lc-client-logo"
                src="<?php echo htmlspecialchars(getImageUrl($cl['logo'])); ?>"
                alt="<?php echo htmlspecialchars($cl['name']); ?>"
              >

            <?php else: ?>

              <span class="lc-client-placeholder">
                <?php echo strtoupper(substr($cl['name'], 0, 1)); ?>
              </span>

            <?php endif; ?>

          </span>

          <span class="lc-client-name">
            <?php echo htmlspecialchars($cl['name']); ?>
          </span>

        </span>

      </span>

    <?php endforeach; ?>

  </div>


  <!-- Moving marquee -->
  <div class="lc-client-moving" id="clientsMoving">

    <marquee
      class="lc-client-marquee"
      direction="left"
      behavior="scroll"
      scrollamount="6"
      scrolldelay="0"
      loop="-1"
    >

      <?php foreach ($clients as $cl): ?>

        <span class="lc-client-slide">

          <span class="lc-client-item">

            <span class="lc-client-logo-wrap">

              <?php if (!empty($cl['logo'])): ?>

                <img
                  class="lc-client-logo"
                  src="<?php echo htmlspecialchars(getImageUrl($cl['logo'])); ?>"
                  alt="<?php echo htmlspecialchars($cl['name']); ?>"
                >

              <?php else: ?>

                <span class="lc-client-placeholder">
                  <?php echo strtoupper(substr($cl['name'], 0, 1)); ?>
                </span>

              <?php endif; ?>

            </span>

            <span class="lc-client-name">
              <?php echo htmlspecialchars($cl['name']); ?>
            </span>

          </span>

        </span>

      <?php endforeach; ?>

    </marquee>

  </div>

</div>


</section>

<!-- ═══════════════════════════════════════
     CTA
════════════════════════════════════════ -->
<section class="lc-cta">
  <div class="lc-cta-inner">
    <div class="lc-eyebrow fade-up">Begin Your Journey</div>
    <h2 class="lc-heading fade-up fade-up-d1"><?php echo htmlspecialchars($inquiry_title); ?></h2>
    <p class="fade-up fade-up-d2"><?php echo htmlspecialchars($inquiry_text); ?></p>
    <div class="lc-cta-btns fade-up fade-up-d3">
      <a href="#" class="btn-primary-lc" data-bs-toggle="modal" data-bs-target="#quoteInquiryModal">Request a Quote <i class="bi bi-arrow-right"></i></a>
      <a href="https://wa.me/<?php echo getSetting('whatsapp',' 8796591267'); ?>" target="_blank" class="btn-ghost-lc">
        <i class="bi bi-whatsapp"></i> WhatsApp Us
      </a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
<script>
document.addEventListener("DOMContentLoaded", function () {

  const initialLogos = document.getElementById("clientsInitial");
  const movingLogos = document.getElementById("clientsMoving");

  // Wait 2.5 seconds before starting
  setTimeout(function () {

    initialLogos.style.display = "none";
    movingLogos.style.display = "block";

  }, 2500);

});
</script>