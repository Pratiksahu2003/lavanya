<?php
/**
 * LAVANYAA CREATION — HTML Head
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../functions/settings.php';
require_once __DIR__ . '/../functions/cart.php';
require_once __DIR__ . '/../functions/uploads.php';

$_page_title = $page_title ?? getSetting('meta_title', 'Lavanyaa Creation');
$_page_desc  = $page_desc  ?? getSetting('meta_desc', 'Premium Furniture Services — Lavanyaa Creation crafts bespoke furniture for luxury residences, corporate offices, and hospitality spaces.');
$_site_name  = 'Lavanyaa Creation';
cartStart();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($_page_title); ?> — Lavanyaa Creation</title>
  <meta name="description" content="<?php echo htmlspecialchars($_page_desc); ?>">
  <meta name="theme-color" content="#0E0E0F">
  <link rel="icon" href="<?php echo BASE_URL; ?>/assets/images/lavanya-lc.png" type="image/png">
  <!-- OG -->
  <meta property="og:title" content="<?php echo htmlspecialchars($_page_title); ?> — Lavanyaa Creation">
  <meta property="og:description" content="<?php echo htmlspecialchars($_page_desc); ?>">
  <meta property="og:type" content="website">
  <meta property="og:image" content="<?php echo BASE_URL; ?>/assets/images/lavanya-lc.png">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Main CSS -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/../assets/css/style.css'); ?>">
  <meta name="base-url" content="<?php echo htmlspecialchars(BASE_URL); ?>">
  <script>window.LAVANYAA_BASE = <?php echo json_encode(BASE_URL); ?>;</script>
  <!-- Meta Pixel -->
  <script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','2017436595827489');fbq('track','PageView');</script>
  <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=2017436595827489&ev=PageView&noscript=1"/></noscript>
</head>
<body>

<!-- Loader -->
<div id="lc-loader">
  <img src="<?php echo BASE_URL; ?>/assets/images/lavanya-lc.png" alt="Lavanyaa Creation">
  <div id="lc-loader-line"></div>
  <div id="lc-loader-tag">Premium Furniture Services</div>
</div>
<!-- Scroll Progress -->
<div id="lc-progress"></div>
