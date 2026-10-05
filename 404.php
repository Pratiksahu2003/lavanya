<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/functions/settings.php';
$page_title = 'Page Not Found — Lavanyaa Creation';
$active_page = '';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>
<section class="lc-404">
  <div>
    <h1>404</h1>
    <h2>This Space Doesn't Exist</h2>
    <p>The page you're looking for has been moved, renamed, or never existed. Let's get you back on track.</p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
      <a href="<?php echo BASE_URL; ?>/index.php" class="btn-primary-lc">Go Home <i class="bi bi-house"></i></a>
      <a href="<?php echo BASE_URL; ?>/category.php?cat=all" class="btn-outline-lc">Browse Collections</a>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
