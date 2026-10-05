<?php
/**
 * LAVANYAA CREATION — Premium Admin CMS Layout
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../functions/uploads.php';
require_once __DIR__ . '/../functions/settings.php';
requireAdminLogin();

$admin     = currentAdmin();
$a_title   = $admin_page_title ?? 'Dashboard';
$a_active  = $admin_active ?? '';
$brandName = getSetting('site_name', 'LAVANYAA CREATION');
$brandLogo = getSettingImage('company_logo');
$favicon   = getSettingImage('favicon', '/assets/images/lc-logo.png');

require_once __DIR__ . '/../functions/inquiries.php';
$unread_inq = countUnreadInquiries();
$unread_msg = countUnreadMessages();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="base-url" content="<?php echo htmlspecialchars(BASE_URL); ?>">
  <title><?php echo htmlspecialchars($a_title); ?> — <?php echo htmlspecialchars($brandName); ?> CMS</title>
  <link rel="icon" href="<?php echo htmlspecialchars($favicon); ?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/admin.css">
</head>
<body>

<!-- SIDEBAR -->
<aside class="adm-sidebar" id="adm-sidebar">
  <div class="adm-sidebar-logo">
    <img src="<?php echo htmlspecialchars($brandLogo); ?>" alt="<?php echo htmlspecialchars($brandName); ?>">
    <div>
      <div class="lc-brand-name"><?php echo htmlspecialchars($brandName); ?></div>
      <div class="lc-brand-tag">Admin CMS</div>
    </div>
  </div>

  <nav class="adm-nav">
    <div class="adm-nav-section">Overview</div>
    <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="<?php echo $a_active==='dashboard'?'active':''; ?>">
      <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <div class="adm-nav-section">Content</div>
    <a href="<?php echo BASE_URL; ?>/admin/products/index.php" class="<?php echo $a_active==='products'?'active':''; ?>">
      <i class="bi bi-box-seam"></i> Products
    </a>
    <a href="<?php echo BASE_URL; ?>/admin/categories/index.php" class="<?php echo $a_active==='categories'?'active':''; ?>">
      <i class="bi bi-grid-3x3"></i> Categories
    </a>
    <a href="<?php echo BASE_URL; ?>/admin/subcategories/index.php" class="<?php echo $a_active==='subcategories'?'active':''; ?>">
      <i class="bi bi-diagram-3"></i> Sub Categories
    </a>
    <a href="<?php echo BASE_URL; ?>/admin/collections/index.php" class="<?php echo $a_active==='collections'?'active':''; ?>">
      <i class="bi bi-collection"></i> Collections
    </a>
    <a href="<?php echo BASE_URL; ?>/admin/banners/index.php" class="<?php echo $a_active==='banners'?'active':''; ?>">
      <i class="bi bi-image"></i> Banners
    </a>
    <a href="<?php echo BASE_URL; ?>/admin/clients/index.php" class="<?php echo $a_active==='clients'?'active':''; ?>">
      <i class="bi bi-buildings"></i> Premium Clients
    </a>
    <a href="<?php echo BASE_URL; ?>/admin/media/index.php" class="<?php echo $a_active==='media'?'active':''; ?>">
      <i class="bi bi-images"></i> Media Library
    </a>

    <div class="adm-nav-section">CRM</div>
    <a href="<?php echo BASE_URL; ?>/admin/inquiries/index.php" class="<?php echo $a_active==='inquiries'?'active':''; ?>">
      <i class="bi bi-chat-dots"></i> Inquiries
      <?php if ($unread_inq > 0): ?><span class="adm-badge"><?php echo $unread_inq; ?></span><?php endif; ?>
    </a>
    <a href="<?php echo BASE_URL; ?>/admin/inquiries/messages.php" class="<?php echo $a_active==='messages'?'active':''; ?>">
      <i class="bi bi-envelope"></i> Messages
      <?php if ($unread_msg > 0): ?><span class="adm-badge"><?php echo $unread_msg; ?></span><?php endif; ?>
    </a>
    <a href="<?php echo BASE_URL; ?>/admin/inquiries/reviews.php" class="<?php echo $a_active==='reviews'?'active':''; ?>">
      <i class="bi bi-star"></i> Reviews
    </a>

    <div class="adm-nav-section">System</div>
    <a href="<?php echo BASE_URL; ?>/admin/settings/index.php" class="<?php echo $a_active==='settings'?'active':''; ?>">
      <i class="bi bi-gear"></i> Settings
    </a>
  </nav>

  <div class="adm-sidebar-footer">
    <a href="<?php echo BASE_URL; ?>/index.php" target="_blank" rel="noopener">
      <i class="bi bi-box-arrow-up-right"></i> View Website
    </a>
    <a href="<?php echo BASE_URL; ?>/admin/logout.php" class="lc-logout-link">
      <i class="bi bi-box-arrow-right"></i> Logout
    </a>
  </div>
</aside>

<!-- MAIN -->
<main class="adm-main">
  <header class="adm-topbar">
    <div class="adm-topbar-left">
      <button type="button" class="adm-menu-btn" id="adm-menu-btn" aria-label="Toggle menu">
        <i class="bi bi-list" style="font-size:1.15rem;"></i>
      </button>
      <div class="adm-topbar-title"><?php echo htmlspecialchars($a_title); ?></div>
    </div>
    <div class="adm-topbar-right">
      <?php if ($unread_inq + $unread_msg > 0): ?>
      <a href="<?php echo BASE_URL; ?>/admin/inquiries/index.php" class="adm-notif-btn" title="<?php echo $unread_inq+$unread_msg; ?> unread">
        <i class="bi bi-bell"></i>
        <span class="adm-notif-dot"></span>
      </a>
      <?php endif; ?>
      <div class="adm-admin-chip">
        <div class="adm-admin-avatar"><?php echo htmlspecialchars(strtoupper(substr($admin['name'] ?: 'A', 0, 1))); ?></div>
        <span class="adm-admin-name"><?php echo htmlspecialchars($admin['name'] ?: 'Admin'); ?></span>
      </div>
    </div>
  </header>
  <div class="adm-content">
