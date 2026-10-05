<?php
/**
 * Lavanyaa Creation Privacy Policy
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/functions/settings.php';

$page_title  = 'Privacy Policy - Lavanyaa Creation';
$page_desc   = 'Read the Lavanyaa Creation privacy policy to understand how customer information is collected, used, protected and shared.';
$active_page = 'privacy';

$sections = [
    [
        'title' => 'Information We Collect',
        'body'  => 'Lavanyaa Creation may collect information shared directly by customers or generated through website usage. This can include:',
        'items' => [
            'Name',
            'Email Address',
            'Phone Number',
            'Address',
            'Business / Project Details',
            'Payment Information if applicable',
            'Website usage data',
            'IP and browser information',
        ],
    ],
    [
        'title' => 'How We Use Your Information',
        'body'  => 'We use customer information only for legitimate business purposes connected with our products, services and customer support, including:',
        'items' => [
            'Responding to inquiries',
            'Providing quotations',
            'Processing orders',
            'Improving services',
            'Customer communication',
            'Legal compliance',
        ],
    ],
    [
        'title' => 'Sharing of Information',
        'body'  => 'Lavanyaa Creation does not sell personal information. We may share necessary information with trusted parties only where required to serve customers or meet legal obligations.',
        'items' => [
            'Delivery partners',
            'Payment providers',
            'Technical providers',
            'Legal authorities when required',
        ],
    ],
    [
        'title' => 'Cookies Policy',
        'body'  => 'Our website may use cookies or similar technologies to improve browsing, remember preferences, understand visitor behavior and enhance the overall shopping experience. You may control cookie preferences through your browser settings.',
    ],
    [
        'title' => 'Data Security',
        'body'  => 'We use reasonable administrative, technical and operational safeguards to protect customer information from unauthorized access, misuse, alteration or disclosure. No online system is completely risk-free, but we work to keep your information handled with care.',
    ],
    [
        'title' => 'Third Party Links',
        'body'  => 'The Lavanyaa Creation website may contain links to third party websites or services. We are not responsible for the privacy practices, content or security standards of external websites.',
    ],
    [
        'title' => 'User Rights',
        'body'  => 'Customers may request access, correction or deletion of personal information where applicable by contacting Lavanyaa Creation. We may need to verify your identity before processing such requests.',
    ],
    [
        'title' => 'Policy Updates',
        'body'  => 'Lavanyaa Creation may update this Privacy Policy from time to time to reflect changes in services, business practices or legal requirements. Updated versions will be posted on this page with the latest revision date.',
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
        <h1>Privacy Policy</h1>
        <p>Your trust and privacy matter to Lavanyaa Creation.</p>
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
          <div class="legal-section-number">09</div>
          <div class="legal-section-copy">
            <h2>Contact</h2>
            <p>For privacy-related questions or requests, please contact Lavanyaa Creation.</p>
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
