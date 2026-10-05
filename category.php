<?php
/**
 * LAVANYAA CREATION — Collection / Product Listing Page
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/functions/products.php';
require_once __DIR__ . '/functions/categories.php';
require_once __DIR__ . '/functions/settings.php';
require_once __DIR__ . '/functions/uploads.php';

$cat_slug  = $_GET['cat'] ?? 'all';
$sub_slug  = $_GET['sub'] ?? '';
$search    = trim($_GET['search'] ?? '');
$page_num  = max(1, (int)($_GET['page'] ?? 1));
$per_page  = 12;
$offset    = ($page_num - 1) * $per_page;

$category    = ($cat_slug !== 'all') ? getCategoryBySlug($cat_slug) : null;
$subcategory = $sub_slug ? getSubcategoryBySlug($sub_slug) : null;
$all_cats    = getAllCategories();
$sub_cats    = $category ? getSubcategoriesByCategory($category['id']) : [];

$filters = [];
if ($cat_slug !== 'all' && $category) $filters['category_slug'] = $cat_slug;
if ($sub_slug && $subcategory)        $filters['subcategory_slug'] = $sub_slug;
if ($search)                          $filters['search'] = $search;

$total    = countProducts($filters);
$products = getAllProducts($filters, $per_page, $offset);
$pages    = (int)ceil($total / $per_page);

$page_title  = ($category ? htmlspecialchars($category['name']) . ' Collection' : 'Full Catalogue') . ' — Lavanyaa Creation';
$page_desc   = $category['description'] ?? 'Browse the complete premium furniture catalogue from Lavanyaa Creation.';
$active_page = 'category';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';

$hero_imgs = [
  'living'=>'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=1400&q=80',
  'bedroom'=>'https://images.unsplash.com/photo-1505693314120-0d443867891c?w=1400&q=80',
  'dining'=>'https://images.unsplash.com/photo-1617806118233-18e1de247200?w=1400&q=80',
  'office'=>'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1400&q=80',
  'commercial'=>'https://images.unsplash.com/photo-1559315033-a8c7e3e12804?w=1400&q=80',
];
$hero_banner = $subcategory['image'] ?? $category['image'] ?? null;
$hero_img = getImageUrl($hero_banner ?? ($hero_imgs[$cat_slug] ?? 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=1400&q=80'));
?>

<!-- PAGE HERO -->
<section class="lc-page-hero lc-dynamic-banner" style="background-image:none;">
  <div class="lc-page-hero-bg" style="background-image:url('<?php echo htmlspecialchars($hero_img); ?>');"></div>
  <div class="lc-page-hero-inner">
    <div class="lc-breadcrumb">
      <a href="<?php echo BASE_URL; ?>/index.php">Home</a>
      <span class="lc-breadcrumb-sep">/</span>
      <a href="<?php echo BASE_URL; ?>/category.php?cat=all">Catalogue</a>
      <?php if ($category): ?>
      <span class="lc-breadcrumb-sep">/</span>
      <?php if ($subcategory): ?>
      <a href="<?php echo BASE_URL; ?>/category.php?cat=<?php echo urlencode($cat_slug); ?>" style="color:var(--accent);"><?php echo htmlspecialchars($category['name']); ?></a>
      <span class="lc-breadcrumb-sep">/</span>
      <span style="color:rgba(255,255,255,.6);"><?php echo htmlspecialchars($subcategory['name']); ?></span>
      <?php else: ?>
      <span style="color:rgba(255,255,255,.6);"><?php echo htmlspecialchars($category['name']); ?></span>
      <?php endif; ?>
      <?php endif; ?>
    </div>
    <h1><?php echo $category ? htmlspecialchars($category['name']) : 'The Complete Catalogue'; ?><?php if ($subcategory): ?> <span style="color:var(--accent);">/ <?php echo htmlspecialchars($subcategory['name']); ?></span><?php endif; ?></h1>
    <p><?php echo $category['description'] ?? 'Discover our full range of premium, handcrafted furniture pieces.'; ?></p>
  </div>
</section>

<div class="lc-cat-layout">

  <!-- SIDEBAR -->
  <aside class="lc-sidebar">

    <div class="lc-sidebar-section">
      <label class="lc-sidebar-label">Search</label>
      <form method="GET" action="<?php echo BASE_URL; ?>/category.php" class="lc-sidebar-search">
        <?php if ($cat_slug !== 'all'): ?><input type="hidden" name="cat" value="<?php echo htmlspecialchars($cat_slug); ?>"><?php endif; ?>
        <input type="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search products...">
        <button type="submit"><i class="bi bi-search"></i></button>
      </form>
    </div>

    <div class="lc-sidebar-section">
      <label class="lc-sidebar-label">Collections</label>
      <div class="lc-cat-list">
        <a href="<?php echo BASE_URL; ?>/category.php?cat=all" class="lc-cat-link <?php echo $cat_slug==='all'?'active':''; ?>">
          <i class="bi bi-grid"></i> All Products <span><?php echo $total; ?></span>
        </a>
        <?php foreach ($all_cats as $c): ?>
        <a href="<?php echo BASE_URL; ?>/category.php?cat=<?php echo urlencode($c['slug']); ?>" class="lc-cat-link <?php echo $cat_slug===$c['slug']?'active':''; ?>">
          <i class="bi <?php echo htmlspecialchars($c['icon'] ?? 'bi-grid'); ?>"></i> <?php echo htmlspecialchars($c['name']); ?>
        </a>
        <?php endforeach; ?>
      </div>
    </div>

    <?php if (!empty($sub_cats)): ?>
    <div class="lc-sidebar-section">
      <label class="lc-sidebar-label">Refine By</label>
      <div class="lc-cat-list">
        <a href="<?php echo BASE_URL; ?>/category.php?cat=<?php echo urlencode($cat_slug); ?>" class="lc-cat-link <?php echo !$sub_slug?'active':''; ?>">
          All <?php echo htmlspecialchars($category['name'] ?? ''); ?>
        </a>
        <?php foreach ($sub_cats as $s): ?>
        <a href="<?php echo BASE_URL; ?>/category.php?cat=<?php echo urlencode($cat_slug); ?>&sub=<?php echo urlencode($s['slug']); ?>" class="lc-cat-link <?php echo $sub_slug===$s['slug']?'active':''; ?>">
          <?php echo htmlspecialchars($s['name']); ?>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <div class="lc-sidebar-section" style="background:var(--text);margin:0 -24px -24px;padding:28px 24px;border-radius:0 0 4px 4px;">
      <p style="font-family:var(--serif);font-size:1rem;color:var(--white);font-style:italic;margin-bottom:14px;">Need a custom solution?</p>
      <a href="#" class="btn-primary-lc" style="width:100%;justify-content:center;padding:12px;font-size:.65rem;" data-bs-toggle="modal" data-bs-target="#quoteInquiryModal">Request a Quote</a>
    </div>

  </aside>

  <!-- PRODUCT GRID -->
  <div style="">
    <div class="lc-list-header">
      <div>
        <h1 class="lc-list-title"><?php echo $category ? htmlspecialchars($category['name']) : 'All Products'; ?><?php if ($subcategory): ?> <span style="color:var(--accent);">/ <?php echo htmlspecialchars($subcategory['name']); ?></span><?php endif; ?></h1>
        <p class="lc-list-count"><?php echo $total; ?> piece<?php echo $total!==1?'s':''; ?> found<?php echo $search ? ' for "'.htmlspecialchars($search).'"' : ''; ?></p>
      </div>
      <?php if ($search): ?>
      <a href="<?php echo BASE_URL; ?>/category.php?cat=<?php echo urlencode($cat_slug); ?>" style="color:var(--primary);font-size:.8rem;"><i class="bi bi-x-circle"></i> Clear Search</a>
      <?php endif; ?>
    </div>

    <?php if (empty($products)): ?>
    <div style="text-align:center;padding:100px 20px;">
      <i class="bi bi-search" style="font-size:3rem;color:var(--secondary);display:block;margin-bottom:18px;"></i>
      <h3 style="font-family:var(--serif);font-weight:400;color:var(--text);">No products found</h3>
      <p style="color:var(--text-light);font-size:.88rem;">Try adjusting your filters or <a href="<?php echo BASE_URL; ?>/category.php?cat=all" style="color:var(--primary);">browse the full catalogue</a>.</p>
    </div>
    <?php else: ?>

    <div class="lc-prod-grid">
      <?php foreach ($products as $i => $p):
        $img = getImageUrl($p['primary_image'] ?? 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&q=80');
        $d   = 'fade-up-d' . (($i % 3) + 1);
      ?>
      <div class="lc-prod-card fade-up <?php echo $d; ?>">
        <a href="<?php echo BASE_URL; ?>/product.php?slug=<?php echo urlencode($p['slug']); ?>" class="lc-prod-img-wrap">
          <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" loading="lazy">
          <?php if ($p['is_new']): ?>
          <span class="lc-prod-badge">New</span>
          <?php elseif ($p['is_bestseller']): ?>
          <span class="lc-prod-badge accent">Best Seller</span>
          <?php elseif ($p['is_featured']): ?>
          <span class="lc-prod-badge">Featured</span>
          <?php endif; ?>
        </a>
        <div class="lc-prod-body">
          <div class="lc-prod-meta"><?php echo htmlspecialchars($p['product_code']); ?> · <?php echo htmlspecialchars($p['category_name'] ?? ''); ?></div>
          <a href="<?php echo BASE_URL; ?>/product.php?slug=<?php echo urlencode($p['slug']); ?>" class="lc-prod-name"><?php echo htmlspecialchars($p['name']); ?></a>
          <p class="lc-prod-desc"><?php echo htmlspecialchars($p['short_desc'] ?? substr($p['description'] ?? '', 0, 120)); ?></p>
          <div class="lc-prod-actions">
            <a href="https://wa.me/<?php echo getSetting('whatsapp','917042704454'); ?>?text=<?php echo rawurlencode("Hi! I'm interested in the {$p['name']} (Code: {$p['product_code']}). Can you share details?"); ?>"
               target="_blank" class="btn-enquire"><i class="bi bi-whatsapp"></i> Enquire</a>
            <button class="btn-cart"
                    data-product-id="<?php echo $p['id']; ?>"
                    data-product-code="<?php echo htmlspecialchars($p['product_code']); ?>"
                    data-product-name="<?php echo htmlspecialchars($p['name']); ?>"
                    data-product-image="<?php echo htmlspecialchars($img); ?>">
              <i class="bi bi-bag-plus"></i>
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- PAGINATION -->
    <?php if ($pages > 1): ?>
    <nav class="lc-pagination" aria-label="Products pagination">
      <?php if ($page_num > 1): ?>
      <a class="lc-page-btn" href="?cat=<?php echo $cat_slug; ?><?php echo $sub_slug?'&sub='.$sub_slug:''; ?>&page=<?php echo $page_num-1; ?>"><i class="bi bi-chevron-left"></i></a>
      <?php endif; ?>
      <?php for ($p = 1; $p <= min(5, $pages); $p++): ?>
      <a class="lc-page-btn <?php echo $p===$page_num?'active':''; ?>" href="?cat=<?php echo $cat_slug; ?><?php echo $sub_slug?'&sub='.$sub_slug:''; ?>&page=<?php echo $p; ?>"><?php echo $p; ?></a>
      <?php endfor; ?>
      <?php if ($page_num < $pages): ?>
      <a class="lc-page-btn" href="?cat=<?php echo $cat_slug; ?><?php echo $sub_slug?'&sub='.$sub_slug:''; ?>&page=<?php echo $page_num+1; ?>"><i class="bi bi-chevron-right"></i></a>
      <?php endif; ?>
    </nav>
    <?php endif; ?>

    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
