<?php
/**
 * LAVANYAA CREATION — Navbar
 */
require_once __DIR__ . '/../functions/categories.php';
require_once __DIR__ . '/../functions/settings.php';
require_once __DIR__ . '/../functions/cart.php';
require_once __DIR__ . '/../functions/uploads.php';

$active_page  = $active_page ?? '';
$nav_cats     = getNavCategories();
$wa_number    = getSetting('whatsapp', '8796591267');
$phone1       = getSetting('phone1', '+91 8796591267');
$biz_hours    = getSetting('business_hours', 'Mon–Sat, 10am–7pm');
$announcement = getSetting('announcement_bar', 'Complimentary Delivery & Professional Installation Across India');
$instagram    = getSetting('instagram', '#');
$facebook     = getSetting('facebook', '#');
$linkedin     = getSetting('linkedin', '#');
$youtube      = getSetting('youtube', '#');
$cart_count   = cartCount();

$cat_icons = [
  'living'=>'bi-house-heart','dining'=>'bi-egg-fried','bedroom'=>'bi-moon-stars',
  'office'=>'bi-briefcase','commercial'=>'bi-building','decor'=>'bi-stars',
];
$cat_showcases = [
  'living'     => ['img'=>'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=500&q=80','title'=>'Living Collection','sub'=>'Comfort meets refined aesthetics.'],
  'dining'     => ['img'=>'https://images.unsplash.com/photo-1617806118233-18e1de247200?w=500&q=80','title'=>'Dining Collection','sub'=>'Where every meal becomes a memory.'],
  'bedroom'    => ['img'=>'https://images.unsplash.com/photo-1505693314120-0d443867891c?w=500&q=80','title'=>'Bedroom Collection','sub'=>'Designed for rest, crafted for dreams.'],
  'office'     => ['img'=>'https://images.unsplash.com/photo-1497366216548-37526070297c?w=500&q=80','title'=>'Office Solutions','sub'=>'Precision crafted for productivity.'],
  'commercial' => ['img'=>'https://images.unsplash.com/photo-1559315033-a8c7e3e12804?w=500&q=80','title'=>'Commercial Solutions','sub'=>'End-to-end for hospitality & business.'],
  'decor'      => ['img'=>'https://images.unsplash.com/photo-1532372576444-dda954194ad0?w=500&q=80','title'=>'Decor Collection','sub'=>'Accents that elevate every space.'],
];
?>

<!-- TOP BAR -->
<div class="top-bar" role="banner">
  <div class="top-bar-inner">
    <a class="top-bar-phone" href="tel:<?php echo htmlspecialchars(preg_replace('/\D/','',$phone1)); ?>"><i class="bi bi-telephone-fill"></i> <?php echo htmlspecialchars($phone1); ?></a>
    <span class="top-bar-dot d-none d-md-inline">·</span>
    <span class="top-bar-text"><?php echo htmlspecialchars($announcement); ?></span>
    <span class="top-bar-dot d-none d-md-inline">·</span>
        <span
          class="top-bar-social"
          style="display: inline-flex; gap: 10px;"
        >
          <a href="https://www.facebook.com/profile.php?id=61590605110120" target="_blank" rel="noopener" aria-label="Facebook"><i class="bi bi-facebook"></i></a> <a href="https://www.instagram.com/lavanyaacreation" target="_blank" rel="noopener" aria-label="Instagram"><i class="bi bi-instagram"></i></a> <a href="https://www.linkedin.com/company/lavanyaa-creation" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a> <a href="https://youtube.com/@lavanyacreation-z8o?si=NwNaD2KG2iOaSutO" target="_blank" rel="noopener" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
        
          <a href="https://wa.me/<?php echo htmlspecialchars(preg_replace('/\D/','',$wa_number)); ?>" target="_blank" rel="noopener" aria-label="WhatsApp">
            <i class="bi bi-whatsapp"></i>
          </a>
        </span>
  </div>
</div>

<!-- MAIN NAVBAR -->
<nav class="main-navbar" role="navigation" aria-label="Main navigation">
  <div class="main-navbar-inner">
    <a href="<?php echo BASE_URL; ?>/index.php" class="navbar-logo" aria-label="Lavanyaa Creation Home">
      <img src="<?php echo htmlspecialchars(getSettingImage('company_logo', '/assets/images/lavanya-lc.png')); ?>" alt="Lavanyaa Creation">
    </a>
    <div class="navbar-search desktop-search" role="search">
      <i class="bi bi-search search-icon" aria-hidden="true"></i>
      <input type="search" id="site-search" placeholder="Search collections, products..." autocomplete="off" aria-label="Search products">
    </div>
    <div class="navbar-actions">
      <a href="#" class="btn-get-quote d-none d-lg-inline-flex" data-bs-toggle="modal" data-bs-target="#quoteInquiryModal">Request a Quote</a>
      <a href="https://wa.me/<?php echo $wa_number; ?>" target="_blank" rel="noopener" class="navbar-icon-btn whatsapp-icon d-none d-md-flex" aria-label="WhatsApp">
        <i class="bi bi-whatsapp"></i>
      </a>
      <button class="navbar-icon-btn" id="cart-btn" aria-label="Wishlist / Cart">
        <i class="bi bi-bag"></i>
        <span class="cart-badge" id="cart-count"><?php echo $cart_count; ?></span>
      </button>
      <button class="hamburger" id="mobile-menu-toggle" aria-label="Menu" aria-expanded="false">
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
      </button>
    </div>
  </div>
  <div class="mobile-search-wrap">
    <div class="navbar-search w-100" style="max-width:100%;">
      <i class="bi bi-search search-icon"></i>
      <input type="search" id="mobile-search" placeholder="Search products..." aria-label="Search">
    </div>
  </div>
</nav>

<!-- SECONDARY NAV -->
<nav class="secondary-nav d-none d-lg-block" role="navigation" aria-label="Category navigation">
  <ul class="sec-nav-list">
    <li class="sec-nav-item <?php echo $active_page==='index'?'is-active':''; ?>">
      <a class="sec-nav-link" href="<?php echo BASE_URL; ?>/index.php">Home</a>
    </li>
    <li class="sec-nav-item <?php echo $active_page==='about'?'is-active':''; ?>">
      <a class="sec-nav-link" href="<?php echo BASE_URL; ?>/about.php">Our Story</a>
    </li>
    <?php foreach ($nav_cats as $cat):
      $slug    = $cat['slug'];
      $sc      = $cat_showcases[$slug] ?? ['img'=>'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=500&q=80','title'=>$cat['name'].' Collection','sub'=>'Explore our premium collection.'];
      $hasSubs = !empty($cat['subcategories']);
      if (in_array($slug, ['office', 'commercial'], true)) continue;
    ?>
    <li class="sec-nav-item <?php echo $hasSubs?'has-mega':''; ?>">
      <a class="sec-nav-link" href="<?php echo BASE_URL; ?>/category.php?cat=<?php echo $slug; ?>" <?php echo $hasSubs?'aria-haspopup="true" aria-expanded="false"':''; ?>>
        <?php echo htmlspecialchars($cat['name']); ?>
        <?php if ($hasSubs): ?><i class="bi bi-chevron-down mega-caret"></i><?php endif; ?>
      </a>
      <?php if ($hasSubs): ?>
      <div class="mega-panel">
        <div class="mega-inner">
          <div class="mega-showcase">
            <img src="<?php echo htmlspecialchars(getImageUrl($sc['img'])); ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" loading="lazy">
            <div class="mega-showcase-info">
              <strong><?php echo htmlspecialchars($sc['title']); ?></strong>
              <em><?php echo htmlspecialchars($sc['sub']); ?></em>
              <a href="<?php echo BASE_URL; ?>/category.php?cat=<?php echo $slug; ?>" class="mega-view-all">View All →</a>
            </div>
          </div>
          <div class="mega-links">
            <?php foreach ($cat['subcategories'] as $sub): ?>
            <a href="<?php echo BASE_URL; ?>/category.php?cat=<?php echo $slug; ?>&sub=<?php echo $sub['slug']; ?>" class="mega-link">
              <i class="bi bi-arrow-right-short"></i><?php echo htmlspecialchars($sub['name']); ?>
            </a>
            <?php endforeach; ?>
            <a href="<?php echo BASE_URL; ?>/category.php?cat=<?php echo $slug; ?>" class="mega-link view-all-link">View All →</a>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </li>
    <?php endforeach; ?>
    <?php
    $comm = null;
    foreach ($nav_cats as $c) { if ($c['slug']==='commercial') { $comm=$c; break; } }
    if (false && $comm): ?>
    <li class="sec-nav-item has-mega">
      <a class="sec-nav-link" href="<?php echo BASE_URL; ?>/category.php?cat=commercial" aria-haspopup="true" aria-expanded="false">
        Hospitality & Office <i class="bi bi-chevron-down mega-caret"></i>
      </a>
      <div class="mega-panel mega-panel-wide">
        <div class="mega-inner" style="display:block;">
          <div class="mega-commercial">
            <div class="mega-commercial-header">
              <strong>Commercial &amp; Hospitality Solutions</strong>
              <em>End-to-end furniture for businesses, institutions &amp; hospitality spaces.</em>
            </div>
            <div class="mega-commercial-grid">
              <?php
              $comm_icons=['hotel'=>'🏨','office-bulk'=>'🏢','cafe'=>'☕','hospital'=>'🏥','school'=>'🏫'];
              $comm_descs=['hotel'=>['Hotels & Hospitality','Lobbies, rooms, banquet halls'],'office-bulk'=>['Offices & Startups','Workstations, cabins, reception'],'cafe'=>['Cafes & Restaurants','Tables, chairs, bar counters'],'hospital'=>['Hospitals & Clinics','Patient seating, ward furniture'],'school'=>['Schools & Institutes','Classroom, lab, library sets']];
              foreach ($comm['subcategories'] as $sub):
                $si=$comm_icons[$sub['slug']]??'🏢';
                $sd=$comm_descs[$sub['slug']]??[$sub['name'],''];
              ?>
              <a href="<?php echo BASE_URL; ?>/category.php?cat=commercial&sub=<?php echo $sub['slug']; ?>" class="mega-commercial-card">
                <span class="mega-com-icon"><?php echo $si; ?></span>
                <strong><?php echo $sd[0]; ?></strong>
                <em><?php echo $sd[1]; ?></em>
              </a>
              <?php endforeach; ?>
              <a href="#" class="mega-commercial-card mega-cta-card" data-bs-toggle="modal" data-bs-target="#quoteInquiryModal">
                <span class="mega-com-icon"><i class="bi bi-clipboard-check"></i></span>
                <strong>Get a Bulk Quote</strong>
                <em>Custom pricing for large orders</em>
              </a>
            </div>
          </div>
        </div>
      </div>
    </li>
    <?php endif; ?>
    <li class="sec-nav-item">
      <a class="sec-nav-link" href="<?php echo BASE_URL; ?>/category.php?cat=all">Catalogue</a>
    </li>
    <li class="sec-nav-item <?php echo $active_page==='contact'?'is-active':''; ?>">
      <a class="sec-nav-link" href="<?php echo BASE_URL; ?>/contact.php">Contact</a>
    </li>
  </ul>
</nav>

<!-- MOBILE DRAWER -->
<div id="mobile-nav" class="mobile-nav-drawer" aria-hidden="true" role="dialog" aria-label="Mobile navigation">
  <div class="mobile-nav-header">
    <img src="<?php echo htmlspecialchars(getSettingImage('company_logo', '/assets/images/lc-logo.png')); ?>" alt="Lavanyaa Creation">
    <button class="mobile-nav-close" id="mobile-nav-close" aria-label="Close"><i class="bi bi-x-lg"></i></button>
  </div>
  <div class="mobile-nav-body">
    <ul class="mobile-nav-list">
      <li><a href="<?php echo BASE_URL; ?>/index.php" class="mobile-nav-link <?php echo $active_page==='index'?'is-active':''; ?>"><i class="bi bi-house-door"></i> Home</a></li>
      <li><a href="<?php echo BASE_URL; ?>/about.php" class="mobile-nav-link <?php echo $active_page==='about'?'is-active':''; ?>"><i class="bi bi-book"></i> Our Story</a></li>
      <?php foreach ($nav_cats as $cat):
        $slug=$cat['slug']; $icon=$cat_icons[$slug]??'bi-grid'; $hasSubs=!empty($cat['subcategories']);
        if (in_array($slug, ['office', 'commercial'], true)) continue;
      ?>
      <?php if ($hasSubs): ?>
      <li class="mobile-acc-item">
        <button class="mobile-acc-trigger" aria-expanded="false">
          <span><i class="bi <?php echo $icon; ?>"></i> <?php echo htmlspecialchars($cat['name']); ?></span>
          <i class="bi bi-chevron-down acc-caret"></i>
        </button>
        <ul class="mobile-acc-panel">
          <?php foreach ($cat['subcategories'] as $sub): ?>
          <li><a href="<?php echo BASE_URL; ?>/category.php?cat=<?php echo $slug; ?>&sub=<?php echo $sub['slug']; ?>"><?php echo htmlspecialchars($sub['name']); ?></a></li>
          <?php endforeach; ?>
          <li><a href="<?php echo BASE_URL; ?>/category.php?cat=<?php echo $slug; ?>" style="color:var(--accent);">View All →</a></li>
        </ul>
      </li>
      <?php else: ?>
      <li><a href="<?php echo BASE_URL; ?>/category.php?cat=<?php echo $slug; ?>" class="mobile-nav-link"><i class="bi <?php echo $icon; ?>"></i> <?php echo htmlspecialchars($cat['name']); ?></a></li>
      <?php endif; ?>
      <?php endforeach; ?>
      <li><a href="<?php echo BASE_URL; ?>/category.php?cat=all" class="mobile-nav-link"><i class="bi bi-grid-3x3"></i> Catalogue</a></li>
      <li><a href="<?php echo BASE_URL; ?>/contact.php" class="mobile-nav-link <?php echo $active_page==='contact'?'is-active':''; ?>"><i class="bi bi-envelope"></i> Contact</a></li>
    </ul>
  </div>
  <div class="mobile-nav-footer">
    <a href="https://wa.me/<?php echo $wa_number; ?>" target="_blank" rel="noopener" class="mobile-whatsapp-btn">
      <i class="bi bi-whatsapp"></i> Chat on WhatsApp
    </a>
  </div>
</div>
<div class="mobile-overlay" id="mobile-overlay"></div>
