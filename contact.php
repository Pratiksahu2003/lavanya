<?php
/**
 * LAVANYAA CREATION — Contact Page
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/functions/inquiries.php';
require_once __DIR__ . '/functions/settings.php';

$page_title  = 'Contact Us — Lavanyaa Creation';
$page_desc   = 'Get in touch with Lavanyaa Creation for furniture enquiries, bulk orders, and bespoke interior projects.';
$active_page = 'contact';

$success = false;
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$message) {
        $error = 'Name, email and message are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $ok = saveContactMessage(compact('name','email','phone','subject','message'));
        $success = $ok;
        if (!$ok) $error = 'Something went wrong. Please try again.';
    }
}

$wa  = getSetting('whatsapp', '917042704454');
$ph1 = getSetting('phone1');
$ph2 = getSetting('phone2');
$em  = getSetting('email');
$adr = getSetting('address');

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<!-- PAGE HERO -->
<section class="lc-page-hero">
  <div class="lc-page-hero-inner">
    <div class="lc-breadcrumb">
      <a href="<?php echo BASE_URL; ?>/index.php">Home</a>
      <span class="lc-breadcrumb-sep">/</span>
      <span style="color:rgba(255,255,255,.6);">Contact</span>
    </div>
    <h1>Let's Start a Conversation</h1>
    <p>Whether it's a single statement piece or a full commercial project — our design team is ready to help.</p>
  </div>
</section>

<div class="lc-contact-grid">

  <!-- INFO -->
  <div class="lc-contact-info fade-up">
    <div class="lc-eyebrow">Get In Touch</div>
    <h2 class="lc-heading">We'd Love to Hear<br>From You</h2>
    <p>Share your requirements with us and our team will respond within 2 business hours with tailored recommendations.</p>

    <div class="lc-contact-details">
      <?php if ($adr): ?>
      <div class="lc-ci">
        <div class="lc-ci-icon"><i class="bi bi-geo-alt-fill"></i></div>
        <div class="lc-ci-body"><label>Our Showroom</label><span><?php echo htmlspecialchars($adr); ?></span></div>
      </div>
      <?php endif; ?>
      <div class="lc-ci">
        <div class="lc-ci-icon"><i class="bi bi-telephone-fill"></i></div>
        <div class="lc-ci-body">
          <label>Call Us</label>
          <?php if ($ph1): ?><a href="tel:<?php echo preg_replace('/\s/','',$ph1); ?>"><?php echo $ph1; ?></a><?php endif; ?>
          <?php if ($ph2): ?><a href="tel:<?php echo preg_replace('/\s/','',$ph2); ?>"><?php echo $ph2; ?></a><?php endif; ?>
        </div>
      </div>
      <?php if ($em): ?>
      <div class="lc-ci">
        <div class="lc-ci-icon"><i class="bi bi-envelope-fill"></i></div>
        <div class="lc-ci-body"><label>Email Us</label><a href="mailto:<?php echo $em; ?>"><?php echo $em; ?></a></div>
      </div>
      <?php endif; ?>
      <div class="lc-ci">
        <div class="lc-ci-icon"><i class="bi bi-clock-fill"></i></div>
        <div class="lc-ci-body"><label>Business Hours</label><span><?php echo getSetting('business_hours','Mon–Sat, 10am–7pm'); ?></span></div>
      </div>
    </div>

    <a href="https://wa.me/<?php echo $wa; ?>" target="_blank" rel="noopener" class="btn-primary-lc" style="margin-top:32px;background:#25d366;border-color:#25d366;">
      <i class="bi bi-whatsapp"></i> Chat on WhatsApp
    </a>
  </div>

  <!-- FORM -->
  <div class="lc-contact-form fade-up fade-up-d2">
    <h3 style="font-family:var(--serif);font-size:1.5rem;font-weight:400;margin-bottom:6px;">Send a Message</h3>
    <p style="color:var(--text-light);font-size:.85rem;margin-bottom:28px;">Fill in the details below and we'll get right back to you.</p>

    <?php if ($success): ?>
    <div class="quote-modal-alert quote-modal-alert-success"><i class="bi bi-check-circle-fill"></i> <strong>Message sent successfully!</strong> Our team will get back to you shortly.</div>
    <?php elseif ($error): ?>
    <div class="quote-modal-alert quote-modal-alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="row g-3">
        <div class="col-md-6 lc-form-group">
          <label>Full Name *</label>
          <input type="text" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" placeholder="Your full name">
        </div>
        <div class="col-md-6 lc-form-group">
          <label>Contact Number</label>
          <input type="tel" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" placeholder="+91 XXXXX XXXXX">
        </div>
        <div class="col-12 lc-form-group">
          <label>Email Address *</label>
          <input type="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" placeholder="your@email.com">
        </div>
        <div class="col-12 lc-form-group">
          <label>Requirement / Purpose</label>
          <select name="subject">
            <option value="">Select a topic...</option>
            <option>Residential Furniture</option>
            <option>Commercial / Bulk Order</option>
            <option>Hospitality Project</option>
            <option>Bespoke / Custom Furniture</option>
            <option>Delivery &amp; Installation</option>
            <option>General Enquiry</option>
          </select>
        </div>
        <div class="col-12 lc-form-group">
          <label>Description / Message *</label>
          <textarea name="message" required rows="5" placeholder="Describe what you need — product details, quantity, delivery location..."><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
        </div>
        <div class="col-12">
          <button type="submit" class="btn-primary-lc" style="width:100%;justify-content:center;padding:15px;">Send Message <i class="bi bi-send"></i></button>
        </div>
      </div>
    </form>
  </div>

</div>

<!-- MAP / SHOWROOM STRIP -->
<section class="lc-contact-banner">
  <div class="lc-contact-banner-content">
    <i class="bi bi-geo-alt-fill"></i>
    <p>Visit our showroom by appointment</p>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
