<?php
/**
 * LAVANYAA CREATION — Footer
 */
require_once __DIR__ . '/../functions/settings.php';
require_once __DIR__ . '/../functions/categories.php';
require_once __DIR__ . '/../functions/uploads.php';

$footer_cats = getAllCategories();
$s_address   = getSetting('address');
$s_phone1    = getSetting('phone1');
$s_phone2    = getSetting('phone2');
$s_email     = getSetting('email');
$s_wa        = getSetting('whatsapp');
$s_ig        = getSetting('instagram','#');
$s_fb        = getSetting('facebook','#');
$s_yt        = getSetting('youtube','#');
$s_li        = getSetting('linkedin','#');
$s_wa_digits = preg_replace('/\D/', '', $s_wa);
$footer_bg_val = getSetting('footer_background_image', '');
$footer_bg   = $footer_bg_val ? getImageUrl($footer_bg_val) : '';
$footer_about = getSetting('footer_about', 'A distinguished name in premium furniture design and bespoke interior solutions. Transforming modern environments since 2019.');
$footer_copyright = getSetting('footer_copyright', '&copy; ' . date('Y') . ' Lavanyaa Creation. All Rights Reserved.');
?>

<footer class="lc-footer" <?php if ($footer_bg): ?>style="--footer-bg-image:url('<?php echo htmlspecialchars($footer_bg); ?>');"<?php endif; ?>>
  <div class="lc-footer-inner">

    <!-- Brand -->
    <div class="lc-footer-col">
      <div class="lc-footer-logo">
        <a href="<?php echo BASE_URL; ?>/index.php">
          <img src="<?php echo htmlspecialchars(getSettingImage('company_logo', '/assets/images/lc-logo.png')); ?>" alt="Lavanyaa Creation">
        </a>
      </div>
      <p class="lc-footer-about"><?php echo htmlspecialchars($footer_about); ?></p>
      <p style="font-size: 12px">Your Vision, Our Creation. <br>
Turning imagination into something extraordinary</p>
      <div class="lc-footer-social">
        <a href="https://www.facebook.com/profile.php?id=61590605110120" target="_blank" rel="noopener" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
        <a href="https://www.instagram.com/lavanyaacreation" target="_blank" rel="noopener" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
        <a href="https://www.linkedin.com/company/lavanyaa-creation" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
        <a href="https://youtube.com/@lavanyacreation-z8o?si=NwNaD2KG2iOaSutO" target="_blank" rel="noopener" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
        <a href="https://wa.me/<?php echo htmlspecialchars($s_wa_digits); ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
      </div>
    </div>

    <!-- Quick Links -->
    <div class="lc-footer-col">
      <h5>Navigate</h5>
      <div class="lc-footer-links">
        <a href="<?php echo BASE_URL; ?>/index.php">Home</a>
        <a href="<?php echo BASE_URL; ?>/about.php">Our Story</a>
        <a href="<?php echo BASE_URL; ?>/category.php?cat=all">Catalogue</a>
        <a href="<?php echo BASE_URL; ?>/contact.php">Contact</a>
        <a href="<?php echo BASE_URL; ?>/privacy-policy.php">Privacy Policy</a>
        <a href="<?php echo BASE_URL; ?>/terms-conditions.php">Terms &amp; Conditions</a>
      </div>
    </div>

    <!-- Collections -->
    <div class="lc-footer-col">
      <h5>Collections</h5>
      <div class="lc-footer-links">
        <?php foreach ($footer_cats as $c): ?>
        <a href="<?php echo BASE_URL; ?>/category.php?cat=<?php echo urlencode($c['slug']); ?>"><?php echo htmlspecialchars($c['name']); ?></a>
        <?php endforeach; ?>
      </div>
    </div>
 
    <!-- Services -->
    <div class="lc-footer-col">
      <h5>Services</h5>
      <div class="lc-footer-links">
        <a href="<?php echo BASE_URL; ?>/category.php?cat=all">Bespoke Furniture</a>
        <a href="<?php echo BASE_URL; ?>/contact.php">Interior Consultation</a>
        <a href="<?php echo BASE_URL; ?>/category.php?cat=commercial">Commercial Projects</a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#quoteInquiryModal">Bulk Enquiry</a>
        <a href="<?php echo BASE_URL; ?>/contact.php">Delivery &amp; Installation</a>
        <a href="<?php echo BASE_URL; ?>/contact.php">After-Sales Care</a>
      </div>
    </div>

    <!-- Contact -->
    <div class="lc-footer-col">
      <h5>Get in Touch</h5>
      <div class="lc-footer-contact">
        <?php if ($s_address): ?>
        <div class="lc-footer-ci"><i class="bi bi-geo-alt"></i><span><?php echo htmlspecialchars($s_address); ?></span></div>
        <?php endif; ?>
        <div class="lc-footer-ci">
          <i class="bi bi-telephone"></i>
          <span>
            <?php if ($s_phone1): ?><a href="tel:<?php echo preg_replace('/\s/','',$s_phone1); ?>"><?php echo $s_phone1; ?></a><br><?php endif; ?>
            <?php if ($s_phone2): ?><a href="tel:<?php echo preg_replace('/\s/','',$s_phone2); ?>"><?php echo $s_phone2; ?></a><?php endif; ?>
          </span>
        </div>
        <?php if ($s_email): ?>
        <div class="lc-footer-ci"><i class="bi bi-envelope"></i><span><a href="mailto:<?php echo $s_email; ?>"><?php echo $s_email; ?></a></span></div>
        <?php endif; ?>
        <div class="lc-footer-ci"><i class="bi bi-whatsapp"></i><span><a href="https://wa.me/<?php echo htmlspecialchars($s_wa_digits); ?>" target="_blank" rel="noopener">Chat on WhatsApp</a></span></div>
      </div>
    </div>

  </div>

  <div class="lc-footer-bottom">
    <p style="margin:0;"><?php echo $footer_copyright; ?></p>
    <p style="margin:0;">Designed &amp; Developed by <a href="https://www.topnexmedia.com/" target="_blank" rel="noopener">Topnex Media</a></p>
  </div>
</footer>

<a href="https://wa.me/<?php echo htmlspecialchars($s_wa_digits); ?>" target="_blank" rel="noopener" class="lc-whatsapp-float" aria-label="WhatsApp">
  <i class="bi bi-whatsapp"></i>
</a>

<?php include __DIR__ . '/quote-modal.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo BASE_URL; ?>/assets/js/main.js?v=<?php echo filemtime(__DIR__ . '/../assets/js/main.js'); ?>"></script>
<script src="<?php echo BASE_URL; ?>/assets/js/cart.js?v=<?php echo filemtime(__DIR__ . '/../assets/js/cart.js'); ?>"></script>
