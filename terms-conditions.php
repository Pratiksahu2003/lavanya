<?php
/**
 * Lavanyaa Creation Terms and Conditions
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/functions/settings.php';

$page_title  = 'Terms & Conditions - Lavanyaa Creation';
$page_desc   = 'Review the Lavanyaa Creation terms and conditions for products, services, pricing, orders, delivery, returns and website usage.';
$active_page = 'terms';

$sections = [
    [
        'title' => 'Acceptance of Terms',
        'body'  => 'By accessing the Lavanyaa Creation website, placing an inquiry, requesting a quotation or purchasing from us, you agree to follow these Terms & Conditions.',
    ],
    [
        'title' => 'Products & Services',
        'body'  => 'Lavanyaa Creation provides furniture and interior solutions for residential, commercial and institutional requirements, including:',
        'items' => [
            'Residential furniture',
            'Commercial furniture',
            'Office solutions',
            'Hospitality furniture',
            'School furniture',
            'Healthcare furniture',
        ],
    ],
    [
        'title' => 'Pricing',
        'body'  => 'Product and project prices may vary based on product specifications, site requirements and order scope. Pricing may be affected by:',
        'items' => [
            'Material',
            'Customization',
            'Quantity',
            'Project requirements',
        ],
    ],
    [
        'title' => 'Orders',
        'body'  => 'Orders are processed after confirmation of product details, quantities, delivery information and payment terms. Lavanyaa Creation may contact customers to verify requirements before dispatch or production.',
    ],
    [
        'title' => 'Delivery & Installation',
        'body'  => 'Delivery and installation timelines depend on product availability, customization requirements, customer location and site readiness. Customers are requested to provide accurate delivery details and ensure access for installation teams where applicable.',
    ],
    [
        'title' => 'Returns & Refunds',
        'body'  => 'Damaged or incorrect products must be reported within 48 hours of delivery. Customized products are generally non-returnable unless they are defective, damaged or supplied incorrectly.',
    ],
    [
        'title' => 'Intellectual Property',
        'body'  => 'All website content, branding, images, product presentation, design elements and written material belong to Lavanyaa Creation or its respective licensors. They may not be copied, reproduced or used commercially without permission.',
    ],
    [
        'title' => 'Limitation of Liability',
        'body'  => 'Lavanyaa Creation is not liable for indirect, incidental or consequential losses arising from website use, product delays, third party services or circumstances beyond reasonable control.',
    ],
    [
        'title' => 'Changes to Terms',
        'body'  => 'Lavanyaa Creation may update these Terms & Conditions from time to time. Continued use of the website or services after updates means you accept the revised terms.',
    ],
    [
        'title' => 'Governing Law',
        'body'  => 'These Terms & Conditions are governed by the laws of India.',
    ],
];

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main class="legal-page">
  <section class="legal-hero">
    <div class="container">
      <div class="legal-hero-inner fade-up">
        <span class="legal-kicker">Lavanyaa Creation Legal</span>
        <h1>Terms &amp; Conditions</h1>
        <p>Guidelines for using Lavanyaa Creation services.</p>
        <div class="legal-updated">Last Updated: June 2026</div>
      </div>
    </div>
  </section>

  <section class="legal-content-section">
    <div class="container">
      <div class="legal-card fade-up fade-up-d1">
        <?php foreach ($sections as $index => $section): ?>
        <article class="legal-section">
          <div class="legal-section-number"><?php echo str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT); ?></div>
          <div class="legal-section-copy">
            <h2><?php echo htmlspecialchars($section['title']); ?></h2>
            <p><?php echo htmlspecialchars($section['body']); ?></p>
            <?php if (!empty($section['items'])): ?>
            <ul>
              <?php foreach ($section['items'] as $item): ?>
              <li><?php echo htmlspecialchars($item); ?></li>
              <?php endforeach; ?>
            </ul>
            <?php endif; ?>
          </div>
        </article>
        <?php endforeach; ?>

        <article class="legal-section legal-contact">
          <div class="legal-section-number">11</div>
          <div class="legal-section-copy">
            <h2>Contact Information</h2>
            <p>For questions about these Terms & Conditions, please contact Lavanyaa Creation.</p>
            <div class="legal-contact-box">
              <strong><?php echo htmlspecialchars(getSetting('site_name', 'Lavanyaa Creation')); ?></strong>
              <a href="<?php echo htmlspecialchars(getSetting('site_url', BASE_URL)); ?>" target="_blank" rel="noopener">Website: <?php echo htmlspecialchars(getSetting('site_url', BASE_URL)); ?></a>
              <a href="mailto:<?php echo htmlspecialchars(getSetting('email', 'info@lavanyaacreation.in')); ?>">Email: <?php echo htmlspecialchars(getSetting('email', 'info@lavanyaacreation.in')); ?></a>
            </div>
          </div>
        </article>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
