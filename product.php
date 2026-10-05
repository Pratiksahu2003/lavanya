<?php
/**
 * LAVANYAA CREATION — Product Detail Page
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/functions/products.php';
require_once __DIR__ . '/functions/categories.php';
require_once __DIR__ . '/functions/settings.php';
require_once __DIR__ . '/functions/inquiries.php';
require_once __DIR__ . '/functions/uploads.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) { header('Location: ' . BASE_URL . '/category.php?cat=all'); exit; }

$product = getProductBySlug($slug);
if (!$product) { header('HTTP/1.0 404 Not Found'); include '404.php'; exit; }

$related  = getRelatedProducts($product['id'], $product['category_id'] ?? 0, 4);
$wa_num   = getSetting('whatsapp', '917042704454');
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? '';
$request_uri = $_SERVER['REQUEST_URI'] ?? ('/product.php?slug=' . urlencode($product['slug']));
$product_url = $host ? ($scheme . '://' . $host . $request_uri) : (BASE_URL . '/product.php?slug=' . urlencode($product['slug']));
$wa_msg = rawurlencode(
    "Hi, I'm interested in this product.\n\n" .
    "Product: {$product['name']}\n" .
    "Product Link: {$product_url}\n\n" .
    "Please share more details."
);

// Handle inquiry form
$inquiry_success = false;
$inquiry_error   = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_inquiry'])) {
    if (empty($_POST['inq_name']) || empty($_POST['inq_phone'])) {
        $inquiry_error = 'Name and Phone are required.';
    } else {
        $ok = saveInquiry([
            'type'         => 'product',
            'product_id'   => $product['id'],
            'product_name' => $product['name'],
            'product_code' => $product['product_code'],
            'name'         => $_POST['inq_name'] ?? '',
            'email'        => $_POST['inq_email'] ?? '',
            'phone'        => $_POST['inq_phone'] ?? '',
            'message'      => $_POST['inq_message'] ?? '',
        ]);
        if ($ok) { $inquiry_success = true; }
        else { $inquiry_error = 'Something went wrong. Please try again.'; }
    }
}

$page_title  = htmlspecialchars($product['name']) . ' — Lavanyaa Creation';
$page_desc   = $product['meta_desc'] ?? $product['short_desc'] ?? $product['description'] ?? '';
$active_page = 'category';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';

$primary = '';
$thumbs  = [];
foreach ($product['images'] as $img) {
    if ($img['is_primary']) $primary = $img['image'];
    else $thumbs[] = $img;
}
if (!$primary && !empty($product['images'])) $primary = $product['images'][0]['image'];
if (!$primary) $primary = 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=900&q=85';
$primaryUrl = getImageUrl($primary);
?>
<style>
 @media (min-width: 1200px) {
  .lc-prod-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}
</style>
<div class="lc-container" style="padding-top:24px;">
  <div class="lc-breadcrumb" style="color:var(--text-light);">
    <a href="<?php echo BASE_URL; ?>/index.php">Home</a>
    <span class="lc-breadcrumb-sep">/</span>
    <a href="<?php echo BASE_URL; ?>/category.php?cat=all">Catalogue</a>
    <?php if ($product['category_slug']): ?>
    <span class="lc-breadcrumb-sep">/</span>
    <a href="<?php echo BASE_URL; ?>/category.php?cat=<?php echo urlencode($product['category_slug']); ?>"><?php echo htmlspecialchars($product['category_name']); ?></a>
    <?php endif; ?>
    <span class="lc-breadcrumb-sep">/</span>
    <span style="color:var(--text-mid);"><?php echo htmlspecialchars($product['name']); ?></span>
  </div>
</div>

<!-- PRODUCT DETAIL -->
<div class="lc-pd-layout">

  <!-- GALLERY -->
  <div class="lc-pd-gallery fade-up">
    <img id="main-product-img" src="<?php echo htmlspecialchars($primaryUrl); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="lc-pd-main-img">
    <?php if (!empty($thumbs) || count($product['images']) > 1): ?>
    <div class="lc-pd-thumbs" id="pd-thumbs">
      <img src="<?php echo htmlspecialchars($primaryUrl); ?>" alt="View 1" class="lc-pd-thumb active" onclick="document.getElementById('main-product-img').src=this.src;document.querySelectorAll('.lc-pd-thumb').forEach(t=>t.classList.remove('active'));this.classList.add('active');">
      <?php foreach ($thumbs as $t): ?>
      <img src="<?php echo htmlspecialchars(getImageUrl($t['image'] ?? '')); ?>" alt="<?php echo htmlspecialchars($t['alt_text'] ?? 'Product view'); ?>" class="lc-pd-thumb" onclick="document.getElementById('main-product-img').src=this.src;document.querySelectorAll('.lc-pd-thumb').forEach(t=>t.classList.remove('active'));this.classList.add('active');">
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- INFO -->
  <div class="lc-pd-info fade-up fade-up-d2">
    <div class="lc-pd-code"><?php echo htmlspecialchars($product['product_code']); ?> · <?php echo htmlspecialchars($product['category_name'] ?? ''); ?></div>
    <h1 class="lc-pd-title"><?php echo htmlspecialchars($product['name']); ?></h1>

    <div class="lc-pd-badges">
      <span class="lc-pd-badge custom">Bespoke Orders Available</span>
      <?php if ($product['status']==='active'): ?>
      <span class="lc-pd-badge avail"><i class="bi bi-check-circle-fill me-1"></i>In Stock</span>
      <?php else: ?>
      <span class="lc-pd-badge" style="background:rgba(200,0,0,.08);color:#c00;">Out of Stock</span>
      <?php endif; ?>
      <span class="lc-pd-badge free">Free Delivery</span>
    </div>

    <?php if ($product['description']): ?>
    <p class="lc-pd-desc"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
    <?php endif; ?>

    <?php if (!empty($product['features'])): ?>
    <div class="lc-pd-feats">
      <h6>Key Features</h6>
      <ul class="lc-pd-feat-list">
        <?php foreach ($product['features'] as $feat): ?>
        <li><i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars($feat); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>

    <?php if ($product['material'] || $product['dimensions']): ?>
    <div class="lc-pd-specs">
      <?php if ($product['material']): ?>
      <div class="lc-pd-spec"><span class="lc-pd-spec-label">Material</span><span class="lc-pd-spec-val"><?php echo htmlspecialchars($product['material']); ?></span></div>
      <?php endif; ?>
      <?php if ($product['dimensions']): ?>
      <div class="lc-pd-spec"><span class="lc-pd-spec-label">Dimensions</span><span class="lc-pd-spec-val"><?php echo htmlspecialchars($product['dimensions']); ?></span></div>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="lc-pd-actions">
      <a href="https://wa.me/<?php echo $wa_num; ?>?text=<?php echo $wa_msg; ?>" target="_blank" rel="noopener" class="btn-enquire">
        <i class="bi bi-whatsapp"></i> Enquire on WhatsApp
      </a>
      <button class="btn-cart"
              data-product-id="<?php echo $product['id']; ?>"
              data-product-code="<?php echo htmlspecialchars($product['product_code']); ?>"
              data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
              data-product-image="<?php echo htmlspecialchars($primaryUrl); ?>">
        <i class="bi bi-bag-plus"></i> Add to Wishlist
      </button>
    </div>

    <div class="lc-pd-trust">
      <div class="lc-pd-trust-item"><i class="bi bi-truck"></i><span>Free Delivery</span></div>
      <div class="lc-pd-trust-item"><i class="bi bi-tools"></i><span>Free Installation</span></div>
      <div class="lc-pd-trust-item"><i class="bi bi-shield-check"></i><span>Quality Warranty</span></div>
    </div>
  </div>
</div>

<!-- INQUIRY + REVIEWS -->
<div class="lc-pd-inquiry">
  <div class="lc-inq-card fade-up">
    <h4>Send a Product Enquiry</h4>
    <p>We'll get back to you within 2 hours (Mon–Sat, 10am–7pm)</p>

    <?php if ($inquiry_success): ?>
    <div class="quote-modal-alert quote-modal-alert-success"><i class="bi bi-check-circle-fill"></i> Thank you! Your enquiry has been received. We'll contact you shortly.</div>
    <?php elseif ($inquiry_error): ?>
    <div class="quote-modal-alert quote-modal-alert-error"><?php echo htmlspecialchars($inquiry_error); ?></div>
    <?php endif; ?>

    <form method="POST">
      <input type="hidden" name="product_inquiry" value="1">
      <div class="row g-3">
        <div class="col-md-6 lc-form-group">
          <label>Full Name *</label>
          <input type="text" name="inq_name" required placeholder="Your Name">
        </div>
        <div class="col-md-6 lc-form-group">
          <label>Phone *</label>
          <input type="tel" name="inq_phone" required placeholder="+91 XXXXX XXXXX">
        </div>
        <div class="col-12 lc-form-group">
          <label>Email</label>
          <input type="email" name="inq_email" placeholder="your@email.com">
        </div>
        <div class="col-12 lc-form-group">
          <label>Message</label>
          <textarea name="inq_message" rows="3" placeholder="Any specific requirements, quantity, delivery details..."></textarea>
        </div>
        <div class="col-12">
          <button type="submit" class="btn-primary-lc" style="width:100%;justify-content:center;">Send Enquiry <i class="bi bi-send"></i></button>
        </div>
      </div>
    </form>
  </div>

  <?php if (!empty($product['reviews'])): ?>
  <div class="fade-up fade-up-d2">
    <h5 style="font-family:var(--serif);font-weight:400;font-size:1.3rem;margin-bottom:18px;">Customer Reviews</h5>
    <?php foreach ($product['reviews'] as $rev): ?>
    <div style="background:var(--bg);border-radius:8px;padding:18px 20px;margin-bottom:12px;border:1px solid var(--border-lt);">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
        <span style="font-family:var(--serif);font-weight:500;font-size:.95rem;"><?php echo htmlspecialchars($rev['name']); ?></span>
        <span style="color:var(--accent);font-size:.85rem;"><?php for($r=0;$r<$rev['rating'];$r++) echo '★'; ?></span>
      </div>
      <p style="font-size:.85rem;color:var(--text-mid);margin:0;"><?php echo htmlspecialchars($rev['review']); ?></p>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<!-- RELATED -->
<?php if (!empty($related)): ?>
<section class="section-pad-sm" style="background:var(--bg);">
  <div class="lc-container">
    <div class="lc-eyebrow fade-up">You May Also Like</div>
    <h3 class="lc-heading fade-up fade-up-d1" style="margin-bottom:36px;">Related Pieces</h3>
    <div class="lc-prod-grid">
      <?php foreach ($related as $i => $rp):
        $rimg = getImageUrl($rp['primary_image'] ?? 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&q=80');
        $d    = 'fade-up-d' . (($i%4)+1);
      ?>
      <div class="lc-prod-card fade-up <?php echo $d; ?>">
        <a href="<?php echo BASE_URL; ?>/product.php?slug=<?php echo urlencode($rp['slug']); ?>" class="lc-prod-img-wrap">
          <img src="<?php echo htmlspecialchars($rimg); ?>" alt="<?php echo htmlspecialchars($rp['name']); ?>" loading="lazy">
        </a>
        <div class="lc-prod-body">
          <div class="lc-prod-meta"><?php echo htmlspecialchars($rp['product_code']); ?></div>
          <a href="<?php echo BASE_URL; ?>/product.php?slug=<?php echo urlencode($rp['slug']); ?>" class="lc-prod-name"><?php echo htmlspecialchars($rp['name']); ?></a>
          <div class="lc-prod-actions">
            <a href="<?php echo BASE_URL; ?>/product.php?slug=<?php echo urlencode($rp['slug']); ?>" class="btn-enquire"><i class="bi bi-eye"></i> View Details</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
