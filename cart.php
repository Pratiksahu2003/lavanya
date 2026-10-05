<?php
/**
 * LAVANYAA CREATION — Wishlist / Enquiry Cart Page
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/functions/cart.php';
require_once __DIR__ . '/functions/settings.php';
require_once __DIR__ . '/functions/uploads.php';

$page_title  = 'Your Wishlist — Lavanyaa Creation';
$page_desc   = 'Review your selected furniture pieces and send an enquiry.';
$active_page = '';

$cart = cartGet();
$wa   = getSetting('whatsapp', '917042704454');

$wa_items = [];
foreach ($cart as $item) {
    $wa_items[] = "• {$item['product_name']} (Code: {$item['product_code']}) x{$item['quantity']}";
}
$wa_msg = "Hi! I'm interested in the following pieces from Lavanyaa Creation:\n" . implode("\n", $wa_items) . "\nPlease share pricing and availability.";

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<section class="lc-page-hero" style="padding:64px 0 48px;">
  <div class="lc-page-hero-inner">
    <div class="lc-breadcrumb">
      <a href="<?php echo BASE_URL; ?>/index.php">Home</a>
      <span class="lc-breadcrumb-sep">/</span>
      <span style="color:rgba(255,255,255,.6);">Your Wishlist</span>
    </div>
    <h1>Your Wishlist</h1>
    <p>The pieces you've selected — ready when you are.</p>
  </div>
</section>

<div class="lc-container" style="padding:48px 24px;">

  <?php if (empty($cart)): ?>
  <div style="text-align:center;padding:80px 20px;">
    <i class="bi bi-bag-heart" style="font-size:3.5rem;color:var(--secondary);display:block;margin-bottom:18px;"></i>
    <h3 style="font-family:var(--serif);font-weight:400;color:var(--text);">Your wishlist is empty</h3>
    <p style="color:var(--text-light);font-size:.88rem;">Browse our collections and save pieces you're interested in.</p>
    <a href="<?php echo BASE_URL; ?>/category.php?cat=all" class="btn-primary-lc" style="display:inline-flex;margin-top:18px;">Browse Collections <i class="bi bi-arrow-right"></i></a>
  </div>
  <?php else: ?>

  <div class="lc-cart-layout">
    <div>
      <?php foreach ($cart as $code => $item): ?>
      <div class="lc-cart-item" data-cart-row>
        <?php if (!empty($item['image'])): ?>
        <img src="<?php echo htmlspecialchars(getImageUrl($item['image'])); ?>" alt="">
        <?php endif; ?>
        <div style="flex:1;">
          <div style="font-family:var(--serif);font-weight:500;font-size:1rem;color:var(--text);"><?php echo htmlspecialchars($item['product_name']); ?></div>
          <div style="color:var(--accent);font-size:.72rem;font-weight:600;margin-top:3px;letter-spacing:.06em;"><?php echo htmlspecialchars($item['product_code']); ?></div>
        </div>
        <span style="background:var(--bg);padding:6px 16px;border-radius:100px;font-weight:600;font-size:.8rem;border:1px solid var(--border-lt);">Qty <?php echo $item['quantity']; ?></span>
        <button class="btn-cart-remove" data-code="<?php echo htmlspecialchars($code); ?>" style="background:none;border:none;color:var(--text-light);font-size:1.2rem;padding:6px;">
          <i class="bi bi-x-circle"></i>
        </button>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="lc-cart-summary">
      <h5 style="font-family:var(--serif);font-size:1.2rem;font-weight:400;margin-bottom:18px;">Enquiry Summary</h5>
      <div style="background:var(--bg);border:1px solid var(--border-lt);border-radius:4px;padding:14px;margin-bottom:20px;font-size:.82rem;color:var(--text-mid);">
        <i class="bi bi-info-circle-fill me-2" style="color:var(--accent);"></i>
        Lavanyaa Creation is an enquiry-based showroom. Send your wishlist and we'll respond with pricing &amp; availability.
      </div>
      <div style="margin-bottom:16px;font-size:.85rem;color:var(--text-mid);">Total pieces: <strong style="color:var(--text);"><?php echo array_sum(array_column($cart,'quantity')); ?></strong></div>
      <a href="https://wa.me/<?php echo $wa; ?>?text=<?php echo rawurlencode($wa_msg); ?>" target="_blank" rel="noopener" class="btn-enquire" style="width:100%;padding:13px;margin-bottom:10px;">
        <i class="bi bi-whatsapp"></i> Send via WhatsApp
      </a>
      <a href="<?php echo BASE_URL; ?>/contact.php" class="btn-outline-lc" style="width:100%;justify-content:center;">Send via Contact Form</a>
    </div>
  </div>

  <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
