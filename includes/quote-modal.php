<?php
/**
 * LAVANYAA CREATION — Shared quote inquiry modal
 */
require_once __DIR__ . '/../functions/settings.php';

$quote_wa_number = preg_replace('/[^0-9]/', '', getSetting('whatsapp', '917042704454'));
$quote_status    = $_GET['inquiry'] ?? '';
?>

<div class="modal fade quote-modal" id="quoteInquiryModal" tabindex="-1" aria-labelledby="quoteInquiryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <button type="button" class="btn-close quote-modal-close" data-bs-dismiss="modal" aria-label="Close">
        <i class="bi bi-x-lg" aria-hidden="true"></i>
      </button>

      <div class="quote-modal-header">
        <span class="quote-modal-kicker">Commercial Inquiry</span>
        <h2 id="quoteInquiryModalLabel">Get a Free Quote</h2>
        <p>Tell us what your business needs. Our team will respond with pricing, options and delivery support.</p>
      </div>

      <?php if ($quote_status === 'sent'): ?>
      <div class="quote-modal-alert quote-modal-alert-success">
        <i class="bi bi-check-circle-fill"></i>
        Inquiry sent. Our team will get back to you shortly.
      </div>
      <?php elseif ($quote_status === 'error'): ?>
      <div class="quote-modal-alert quote-modal-alert-error">
        <i class="bi bi-exclamation-circle-fill"></i>
        Please enter your name, phone number and business type.
      </div>
      <?php endif; ?>

      <form class="quote-inquiry-form" method="POST" action="<?php echo BASE_URL; ?>/inquiry-submit.php">
        <input type="hidden" name="source" value="quote_modal">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="quoteName">Name</label>
            <input type="text" id="quoteName" name="name" placeholder="Your name" required>
          </div>
          <div class="col-md-6">
            <label for="quotePhone">Phone Number</label>
            <input type="tel" id="quotePhone" name="phone" placeholder="+91 XXXXX XXXXX" required>
          </div>
          <div class="col-12">
            <label for="quoteBusinessType">Business Type</label>
            <select id="quoteBusinessType" name="business_type" required>
              <option value="">Select business type</option>
              <option>Office / Corporate</option>
              <option>Hotel / Hospitality</option>
              <option>Cafe / Restaurant</option>
              <option>School / Institute</option>
              <option>Hospital / Clinic</option>
              <option>Retail / Showroom</option>
              <option>Interior Designer / Contractor</option>
              <option>Other Commercial Project</option>
            </select>
          </div>
          <div class="col-12">
            <label for="quoteMessage">Message</label>
            <textarea id="quoteMessage" name="message" rows="4" placeholder="Share product type, quantity, location or project details..."></textarea>
          </div>
        </div>

        <div class="quote-modal-actions">
          <button type="submit" class="btn-primary-brown quote-submit-btn">
            <i class="bi bi-send"></i> Send Inquiry
          </button>
          <a href="https://wa.me/<?php echo $quote_wa_number; ?>" target="_blank" rel="noopener" class="btn-whatsapp quote-whatsapp-btn" data-whatsapp-number="<?php echo $quote_wa_number; ?>">
            <i class="bi bi-whatsapp"></i> WhatsApp Inquiry
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
