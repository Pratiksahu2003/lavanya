<?php
$admin_page_title = 'Settings';
$admin_active     = 'settings';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../functions/settings.php';
require_once __DIR__ . '/../../functions/uploads.php';

$db      = getDB();
$success = [];
$errors  = [];

// ── Ensure setting_group column exists (safe migration guard)
try {
    $db->exec("ALTER TABLE settings ADD COLUMN IF NOT EXISTS setting_group VARCHAR(60) DEFAULT 'general' AFTER label");
} catch (Exception $e) { /* ignore */ }

// ── Helper: upsert a setting key/value
function saveSetting(PDO $db, string $key, string $value, string $group = 'general'): void {
    $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_val, setting_group) VALUES (:k,:v,:g)
                          ON DUPLICATE KEY UPDATE setting_val=:v2, setting_group=:g2");
    $stmt->execute([':k'=>$key,':v'=>$value,':g'=>$group,':v2'=>$value,':g2'=>$group]);
}

// ── Handle each section POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section = $_POST['_section'] ?? '';

    /* ── BUSINESS INFO ── */
    if ($section === 'business') {
        $name    = trim($_POST['site_name']    ?? '');
        $tagline = trim($_POST['tagline']      ?? '');
        $biz_hrs = trim($_POST['business_hours']?? '');
        $ann     = trim($_POST['announcement_bar']?? '');
        if (!$name) { $errors['business'] = 'Business name is required.'; }
        else {
            saveSetting($db,'site_name',    $name,    'business');
            saveSetting($db,'tagline',      $tagline, 'business');
            saveSetting($db,'business_hours',$biz_hrs,'business');
            saveSetting($db,'announcement_bar',$ann,  'business');
            $success['business'] = 'Business information saved.';
        }
    }

    /* ── CONTACT INFO ── */
    if ($section === 'contact') {
        $phone1   = trim($_POST['phone1']   ?? '');
        $phone2   = trim($_POST['phone2']   ?? '');
        $email    = trim($_POST['email']    ?? '');
        $whatsapp = preg_replace('/\D/','',$_POST['whatsapp']??'');
        $address  = trim($_POST['address']  ?? '');
        saveSetting($db,'phone1',   $phone1,   'contact');
        saveSetting($db,'phone2',   $phone2,   'contact');
        saveSetting($db,'email',    $email,    'contact');
        saveSetting($db,'whatsapp', $whatsapp, 'contact');
        saveSetting($db,'address',  $address,  'contact');
        $success['contact'] = 'Contact information saved.';
    }

    /* ── BRANDING ── */
    if ($section === 'branding') {
        $current_logo    = getSetting('company_logo',    '');
        $current_favicon = getSetting('favicon',         '');

        if (!empty($_FILES['company_logo']['name'])) {
            $res = uploadImage($_FILES['company_logo'], 'branding');
            if ($res['success']) {
                if ($current_logo) deleteUpload($current_logo);
                saveSetting($db,'company_logo',$res['filename'],'branding');
            } else { $errors['branding'] = $res['error']; }
        }
        if (!empty($_FILES['favicon']['name'])) {
            $res = uploadImage($_FILES['favicon'], 'branding');
            if ($res['success']) {
                if ($current_favicon) deleteUpload($current_favicon);
                saveSetting($db,'favicon',$res['filename'],'branding');
            } else { $errors['branding'] = ($errors['branding']??'') . ' ' . $res['error']; }
        }
        if (empty($errors['branding'])) $success['branding'] = 'Branding saved.';
    }

    /* ── HOMEPAGE ── */
    if ($section === 'homepage') {
        saveSetting($db,'hero_title',    trim($_POST['hero_title']   ??''),'homepage');
        saveSetting($db,'hero_subtitle', trim($_POST['hero_subtitle']??''),'homepage');
        saveSetting($db,'hero_cta_text', trim($_POST['hero_cta_text']??''),'homepage');
        saveSetting($db,'about_text',    trim($_POST['about_text']   ??''),'homepage');
        saveSetting($db,'collections_eyebrow', trim($_POST['collections_eyebrow'] ??''),'homepage');
        saveSetting($db,'collections_title', trim($_POST['collections_title'] ??''),'homepage');
        saveSetting($db,'collections_text', trim($_POST['collections_text'] ??''),'homepage');
        saveSetting($db,'collections_cta_text', trim($_POST['collections_cta_text'] ??''),'homepage');
        saveSetting($db,'collections_cta_link', trim($_POST['collections_cta_link'] ??''),'homepage');
        saveSetting($db,'about_preview_title', trim($_POST['about_preview_title'] ??''),'homepage');
        saveSetting($db,'about_preview', trim($_POST['about_preview'] ??''),'homepage');
        saveSetting($db,'about_preview_quote', trim($_POST['about_preview_quote'] ??''),'homepage');
        saveSetting($db,'about_preview_extra_1', trim($_POST['about_preview_extra_1'] ??''),'homepage');
        saveSetting($db,'about_preview_extra_2', trim($_POST['about_preview_extra_2'] ??''),'homepage');
        saveSetting($db,'about_preview_year', trim($_POST['about_preview_year'] ??''),'homepage');
        saveSetting($db,'industries_eyebrow', trim($_POST['industries_eyebrow'] ??''),'homepage');
        saveSetting($db,'industries_title', trim($_POST['industries_title'] ??''),'homepage');
        saveSetting($db,'industries_text', trim($_POST['industries_text'] ??''),'homepage');
        saveSetting($db,'why_choose_title', trim($_POST['why_choose_title'] ??''),'homepage');
        saveSetting($db,'why_choose_points', trim($_POST['why_choose_points'] ??''),'homepage');

        foreach (['about_preview_image_main', 'about_preview_image_accent'] as $imageKey) {
            if (!empty($_FILES[$imageKey]['name'])) {
                $current = getSetting($imageKey, '');
                $res = uploadImage($_FILES[$imageKey], 'branding');
                if ($res['success']) {
                    if ($current) deleteUpload($current);
                    saveSetting($db, $imageKey, $res['filename'], 'homepage');
                } else {
                    $errors['homepage'] = $res['error'];
                }
            }
        }
        if (empty($errors['homepage'])) $success['homepage'] = 'Homepage content saved.';
    }

    /* ── SEO ── */
    if ($section === 'seo') {
        saveSetting($db,'meta_title',       trim($_POST['meta_title']      ??''),'seo');
        saveSetting($db,'meta_desc',        trim($_POST['meta_desc']       ??''),'seo');
        saveSetting($db,'meta_keywords',    trim($_POST['meta_keywords']   ??''),'seo');
        saveSetting($db,'google_analytics', trim($_POST['google_analytics']??''),'seo');
        saveSetting($db,'fb_pixel',         trim($_POST['fb_pixel']        ??''),'seo');
        $success['seo'] = 'SEO settings saved.';
    }

    /* ── SOCIAL MEDIA ── */
    if ($section === 'social') {
        saveSetting($db,'facebook',  trim($_POST['facebook'] ??''),'social');
        saveSetting($db,'instagram', trim($_POST['instagram']??''),'social');
        saveSetting($db,'linkedin',  trim($_POST['linkedin'] ??''),'social');
        saveSetting($db,'youtube',   trim($_POST['youtube']  ??''),'social');
        $success['social'] = 'Social media links saved.';
    }

    /* ── EMAIL ── */
    if ($section === 'email') {
        saveSetting($db,'smtp_host',     trim($_POST['smtp_host']    ??''),'email');
        saveSetting($db,'smtp_port',     trim($_POST['smtp_port']    ??''),'email');
        saveSetting($db,'smtp_user',     trim($_POST['smtp_user']    ??''),'email');
        saveSetting($db,'smtp_pass',     trim($_POST['smtp_pass']    ??''),'email');
        saveSetting($db,'smtp_from',     trim($_POST['smtp_from']    ??''),'email');
        saveSetting($db,'smtp_fromname', trim($_POST['smtp_fromname']??''),'email');
        $success['email'] = 'Email settings saved.';
    }

    /* ── FOOTER ── */
    if ($section === 'footer') {
        saveSetting($db,'footer_about',     trim($_POST['footer_about']    ??''),'footer');
        saveSetting($db,'footer_copyright', trim($_POST['footer_copyright']??''),'footer');
        if (!empty($_FILES['footer_background_image']['name'])) {
            $current = getSetting('footer_background_image', '');
            $res = uploadImage($_FILES['footer_background_image'], 'branding');
            if ($res['success']) {
                if ($current) deleteUpload($current);
                saveSetting($db,'footer_background_image',$res['filename'],'footer');
            } else {
                $errors['footer'] = $res['error'];
            }
        }
        if (empty($errors['footer'])) $success['footer'] = 'Footer content saved.';
    }
}

// ── Load all current settings
$all = $db->query("SELECT setting_key, setting_val FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
function sv(array $all, string $key, string $default = ''): string {
    return htmlspecialchars($all[$key] ?? $default);
}

// Seed defaults if missing
$defaults = [
    'site_name'      => 'Lavanyaa Creation',
    'tagline'        => 'Premium Furniture Services',
    'phone1'         => '+91 8796591267',
    'address'        => "Than Singh Nagar, Anand Parbat, Near Saraswati Memorial Hospital, Central Delhi, Delhi - 110005",
    'business_hours' => 'Mon–Sat, 10am–7pm',
    'announcement_bar'=> 'Complimentary Delivery & Professional Installation Across India',
    'hero_title'     => 'Crafted for the Extraordinary',
    'hero_subtitle'  => 'Where Spaces Find Their Soul',
    'meta_title'     => 'Lavanyaa Creation — Premium Furniture Services',
    'meta_desc'      => 'Distinguished name in premium furniture design and bespoke interior solutions since 2019.',
    'footer_copyright'=> '© ' . date('Y') . ' Lavanyaa Creation. All Rights Reserved.',
];
foreach ($defaults as $k => $v) {
    if (!isset($all[$k]) || $all[$k] === '') {
        saveSetting($db, $k, $v);
        $all[$k] = $v;
    }
}

include __DIR__ . '/../layout.php';
?>

<div class="adm-page-header">
  <div>
    <h1>Settings</h1>
    <div class="adm-ph-sub">Manage all website settings — each section saves independently</div>
  </div>
</div>

<?php foreach ($success as $s): ?>
<div class="adm-flash adm-flash-success"><i class="bi bi-check-circle-fill"></i><?php echo htmlspecialchars($s); ?></div>
<?php endforeach; ?>
<?php foreach ($errors as $e): ?>
<div class="adm-flash adm-flash-error"><i class="bi bi-exclamation-circle-fill"></i><?php echo htmlspecialchars($e); ?></div>
<?php endforeach; ?>

<?php
// Render one accordion section
function settingsSection(string $id, string $icon, string $title, string $description, string $content): void {
    echo <<<HTML
    <div class="adm-settings-section" id="section-{$id}">
      <div class="adm-settings-header">
        <h5><i class="bi {$icon}"></i>{$title} <small style="font-size:.72rem;font-weight:400;color:var(--lc-text-lt);margin-left:8px;">{$description}</small></h5>
        <i class="bi bi-chevron-down lc-toggle-icon"></i>
      </div>
      <div class="adm-settings-body">
        {$content}
      </div>
    </div>
    HTML;
}
$base = BASE_URL;
?>

<!-- ── 1. BUSINESS INFORMATION ── -->
<?php ob_start(); ?>
<form method="POST">
  <input type="hidden" name="_section" value="business">
  <div class="row g-3">
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">Business Name <span class="required">*</span></label>
      <input type="text" name="site_name" class="adm-input" required value="<?php echo sv($all,'site_name','Lavanyaa Creation'); ?>">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">Tagline</label>
      <input type="text" name="tagline" class="adm-input" value="<?php echo sv($all,'tagline','Premium Furniture Services'); ?>">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">Business Hours</label>
      <input type="text" name="business_hours" class="adm-input" value="<?php echo sv($all,'business_hours','Mon–Sat, 10am–7pm'); ?>" placeholder="Mon–Sat, 10am–7pm">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">Announcement Bar Text</label>
      <input type="text" name="announcement_bar" class="adm-input" value="<?php echo sv($all,'announcement_bar'); ?>" placeholder="Top bar text">
    </div>
    <div class="col-12 adm-form-group">
      <label class="adm-label">Footer Background Image</label>
      <?php $footerBg = $all['footer_background_image'] ?? ''; if ($footerBg): ?>
      <img src="<?php echo htmlspecialchars(getImageUrl($footerBg)); ?>" class="adm-img-preview" style="margin-bottom:8px;width:160px;height:90px;object-fit:cover;" alt="">
      <?php endif; ?>
      <input type="file" name="footer_background_image" class="adm-input" accept="image/*">
      <div class="adm-input-hint">Optional background image for the website footer.</div>
    </div>
  </div>
  <div class="adm-settings-save-row">
    <button type="submit" class="adm-btn adm-btn-primary"><i class="bi bi-check-circle"></i> Save Business Info</button>
  </div>
</form>
<?php settingsSection('business','bi-building','Business Information','Company name, tagline, hours',ob_get_clean()); ?>

<!-- ── 2. CONTACT INFORMATION ── -->
<?php ob_start(); ?>
<form method="POST">
  <input type="hidden" name="_section" value="contact">
  <div class="row g-3">
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">Phone 1</label>
      <input type="text" name="phone1" class="adm-input" value="<?php echo sv($all,'phone1'); ?>" placeholder="+91 XXXXX XXXXX">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">Phone 2</label>
      <input type="text" name="phone2" class="adm-input" value="<?php echo sv($all,'phone2'); ?>" placeholder="+91 XXXXX XXXXX">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">Email Address</label>
      <input type="email" name="email" class="adm-input" value="<?php echo sv($all,'email'); ?>" placeholder="info@lavanyaacreation.com">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">WhatsApp Number</label>
      <input type="text" name="whatsapp" class="adm-input" value="<?php echo sv($all,'whatsapp'); ?>" placeholder="91XXXXXXXXXX (digits only)">
      <div class="adm-input-hint">Include country code, digits only. E.g. 918796591267</div>
    </div>
    <div class="col-12 adm-form-group">
      <label class="adm-label">Address</label>
      <textarea name="address" class="adm-input adm-textarea" rows="3" placeholder="Full address..."><?php echo sv($all,'address'); ?></textarea>
    </div>
  </div>
  <div class="adm-settings-save-row">
    <button type="submit" class="adm-btn adm-btn-primary"><i class="bi bi-check-circle"></i> Save Contact Info</button>
  </div>
</form>
<?php settingsSection('contact','bi-telephone','Contact Information','Phone, email, WhatsApp, address',ob_get_clean()); ?>

<!-- ── 3. BRANDING ── -->
<?php ob_start(); ?>
<form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="_section" value="branding">
  <div class="row g-4">
    <div class="col-md-6">
      <label class="adm-label">Header Logo / Site Logo</label>
      <?php $logo = getSettingImage('company_logo',''); if($logo && $logo !== '/assets/images/lc-logo.png'): ?>
      <div style="margin-bottom:10px;padding:12px;background:var(--lc-bg);border:1px solid var(--lc-border);border-radius:var(--lc-radius);text-align:center;">
        <img src="<?php echo htmlspecialchars($logo); ?>" style="max-height:80px;max-width:100%;object-fit:contain;" alt="Current Logo">
        <div style="font-size:.72rem;color:var(--lc-text-lt);margin-top:6px;">Current logo</div>
      </div>
      <?php endif; ?>
      <input type="file" name="company_logo" class="adm-input" accept="image/*">
      <div class="adm-input-hint">PNG with transparent background recommended. Max 2MB.</div>
    </div>
    <div class="col-md-6">
      <label class="adm-label">Favicon</label>
      <?php $fav = getSettingImage('favicon',''); if($fav): ?>
      <div style="margin-bottom:10px;padding:12px;background:var(--lc-bg);border:1px solid var(--lc-border);border-radius:var(--lc-radius);text-align:center;">
        <img src="<?php echo htmlspecialchars($fav); ?>" style="max-height:48px;max-width:48px;object-fit:contain;" alt="Favicon">
        <div style="font-size:.72rem;color:var(--lc-text-lt);margin-top:6px;">Current favicon</div>
      </div>
      <?php endif; ?>
      <input type="file" name="favicon" class="adm-input" accept="image/*">
      <div class="adm-input-hint">ICO, PNG or SVG — 32×32px recommended. Max 1MB.</div>
    </div>
  </div>
  <div class="adm-settings-save-row">
    <button type="submit" class="adm-btn adm-btn-primary"><i class="bi bi-check-circle"></i> Save Branding</button>
  </div>
</form>
<?php settingsSection('branding','bi-palette','Branding','Logo, favicon, visual identity',ob_get_clean()); ?>

<!-- ── 4. HOMEPAGE CONTENT ── -->
<?php ob_start(); ?>
<form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="_section" value="homepage">
  <div class="row g-3">
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">Hero Title</label>
      <input type="text" name="hero_title" class="adm-input" value="<?php echo sv($all,'hero_title'); ?>" placeholder="Main headline">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">Hero Subtitle</label>
      <input type="text" name="hero_subtitle" class="adm-input" value="<?php echo sv($all,'hero_subtitle'); ?>" placeholder="Secondary line">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">Primary CTA Button Text</label>
      <input type="text" name="hero_cta_text" class="adm-input" value="<?php echo sv($all,'hero_cta_text','Explore Collections'); ?>">
    </div>
    <div class="col-12 adm-form-group">
      <label class="adm-label">Brand Story / About Text</label>
      <textarea name="about_text" class="adm-input adm-textarea" rows="6"><?php echo sv($all,'about_text'); ?></textarea>
      <div class="adm-input-hint">Displayed in the brand story section on the homepage.</div>
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">Collections Eyebrow</label>
      <input type="text" name="collections_eyebrow" class="adm-input" value="<?php echo sv($all,'collections_eyebrow','Our Collections'); ?>">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">Collections Title</label>
      <input type="text" name="collections_title" class="adm-input" value="<?php echo sv($all,'collections_title','Explore Every Space'); ?>">
    </div>
    <div class="col-12 adm-form-group">
      <label class="adm-label">Collections Description</label>
      <textarea name="collections_text" class="adm-input adm-textarea" rows="2"><?php echo sv($all,'collections_text'); ?></textarea>
      <div class="adm-input-hint">Cards use category title, image, description, order and status from Categories.</div>
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">Collections CTA Text</label>
      <input type="text" name="collections_cta_text" class="adm-input" value="<?php echo sv($all,'collections_cta_text','View All Collections'); ?>">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">Collections CTA Link</label>
      <input type="text" name="collections_cta_link" class="adm-input" value="<?php echo sv($all,'collections_cta_link',BASE_URL . '/category.php?cat=all'); ?>">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">About Section Title</label>
      <input type="text" name="about_preview_title" class="adm-input" value="<?php echo sv($all,'about_preview_title','A Legacy Built on Craftsmanship and Vision'); ?>">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">About Founded Year</label>
      <input type="text" name="about_preview_year" class="adm-input" value="<?php echo sv($all,'about_preview_year','2019'); ?>">
    </div>
    <div class="col-12 adm-form-group">
      <label class="adm-label">About Lead Text</label>
      <textarea name="about_preview" class="adm-input adm-textarea" rows="3"><?php echo sv($all,'about_preview'); ?></textarea>
    </div>
    <div class="col-12 adm-form-group">
      <label class="adm-label">About Quote</label>
      <input type="text" name="about_preview_quote" class="adm-input" value="<?php echo sv($all,'about_preview_quote','Great spaces deserve exceptional furniture — that belief drives everything we create.'); ?>">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">About Paragraph 2</label>
      <textarea name="about_preview_extra_1" class="adm-input adm-textarea" rows="3"><?php echo sv($all,'about_preview_extra_1'); ?></textarea>
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">About Paragraph 3</label>
      <textarea name="about_preview_extra_2" class="adm-input adm-textarea" rows="3"><?php echo sv($all,'about_preview_extra_2'); ?></textarea>
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">About Main Image</label>
      <?php $aboutMain = $all['about_preview_image_main'] ?? ''; if ($aboutMain): ?>
      <img src="<?php echo htmlspecialchars(getImageUrl($aboutMain)); ?>" class="adm-img-preview" style="margin-bottom:8px;width:120px;height:80px;object-fit:cover;" alt="">
      <?php endif; ?>
      <input type="file" name="about_preview_image_main" class="adm-input" accept="image/*">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">About Accent Image</label>
      <?php $aboutAccent = $all['about_preview_image_accent'] ?? ''; if ($aboutAccent): ?>
      <img src="<?php echo htmlspecialchars(getImageUrl($aboutAccent)); ?>" class="adm-img-preview" style="margin-bottom:8px;width:120px;height:80px;object-fit:cover;" alt="">
      <?php endif; ?>
      <input type="file" name="about_preview_image_accent" class="adm-input" accept="image/*">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">Industries Eyebrow</label>
      <input type="text" name="industries_eyebrow" class="adm-input" value="<?php echo sv($all,'industries_eyebrow','Sectors We Transform'); ?>">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">Industries Title</label>
      <input type="text" name="industries_title" class="adm-input" value="<?php echo sv($all,'industries_title','Spaces We Specialise In'); ?>">
    </div>
    <div class="col-12 adm-form-group">
      <label class="adm-label">Industries Description</label>
      <textarea name="industries_text" class="adm-input adm-textarea" rows="2"><?php echo sv($all,'industries_text','From corporate campuses to boutique hospitality — complete furniture solutions for every premium environment.'); ?></textarea>
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">Why Lavanyaa Title</label>
      <input type="text" name="why_choose_title" class="adm-input" value="<?php echo sv($all,'why_choose_title','Why Lavanyaa Creation'); ?>">
    </div>
    <div class="col-12 adm-form-group">
      <label class="adm-label">Why Lavanyaa Selling Points</label>
      <textarea name="why_choose_points" class="adm-input adm-textarea" rows="6"><?php echo sv($all,'why_choose_points',"Premium Quality Products\nExclusive & Trendy Designs\nAffordable Prices\nSuperior Craftsmanship\nTimely Delivery\nCustomer-First Approach\nSecure Shopping Experience\nTrusted Quality & Reliability\nDedicated Customer Support\n100% Customer Satisfaction"); ?></textarea>
      <div class="adm-input-hint">One feature per line.</div>
    </div>
  </div>
  <div class="adm-settings-save-row">
    <button type="submit" class="adm-btn adm-btn-primary"><i class="bi bi-check-circle"></i> Save Homepage</button>
  </div>
</form>
<?php settingsSection('homepage','bi-house','Homepage Content','Hero text, brand story, CTA buttons',ob_get_clean()); ?>

<!-- ── 5. FOOTER ── -->
<?php ob_start(); ?>
<form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="_section" value="footer">
  <div class="row g-3">
    <div class="col-12 adm-form-group">
      <label class="adm-label">Footer About Text</label>
      <textarea name="footer_about" class="adm-input adm-textarea" rows="3"><?php echo sv($all,'footer_about'); ?></textarea>
    </div>
    <div class="col-12 adm-form-group">
      <label class="adm-label">Copyright Line</label>
      <input type="text" name="footer_copyright" class="adm-input" value="<?php echo sv($all,'footer_copyright'); ?>" placeholder="© 2025 Lavanyaa Creation. All Rights Reserved.">
    </div>
  </div>
  <div class="adm-settings-save-row">
    <button type="submit" class="adm-btn adm-btn-primary"><i class="bi bi-check-circle"></i> Save Footer</button>
  </div>
</form>
<?php settingsSection('footer','bi-layout-text-sidebar','Footer','Footer text and copyright',ob_get_clean()); ?>

<!-- ── 6. SEO ── -->
<?php ob_start(); ?>
<form method="POST">
  <input type="hidden" name="_section" value="seo">
  <div class="row g-3">
    <div class="col-12 adm-form-group">
      <label class="adm-label">Default Meta Title</label>
      <input type="text" name="meta_title" class="adm-input" value="<?php echo sv($all,'meta_title'); ?>" maxlength="70">
      <div class="adm-input-hint">Recommended: 50–60 characters.</div>
    </div>
    <div class="col-12 adm-form-group">
      <label class="adm-label">Default Meta Description</label>
      <textarea name="meta_desc" class="adm-input adm-textarea" rows="2" maxlength="160"><?php echo sv($all,'meta_desc'); ?></textarea>
      <div class="adm-input-hint">Recommended: 120–155 characters.</div>
    </div>
    <div class="col-12 adm-form-group">
      <label class="adm-label">Meta Keywords</label>
      <input type="text" name="meta_keywords" class="adm-input" value="<?php echo sv($all,'meta_keywords'); ?>" placeholder="premium furniture, luxury interior, bespoke furniture">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">Google Analytics ID</label>
      <input type="text" name="google_analytics" class="adm-input" value="<?php echo sv($all,'google_analytics'); ?>" placeholder="G-XXXXXXXXXX or UA-XXXXXXXX">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">Facebook Pixel ID</label>
      <input type="text" name="fb_pixel" class="adm-input" value="<?php echo sv($all,'fb_pixel'); ?>" placeholder="XXXXXXXXXXXXXXXXXX">
    </div>
  </div>
  <div class="adm-settings-save-row">
    <button type="submit" class="adm-btn adm-btn-primary"><i class="bi bi-check-circle"></i> Save SEO</button>
  </div>
</form>
<?php settingsSection('seo','bi-search','SEO & Analytics','Meta tags, Google Analytics, Pixel',ob_get_clean()); ?>

<!-- ── 7. SOCIAL MEDIA ── -->
<?php ob_start(); ?>
<form method="POST">
  <input type="hidden" name="_section" value="social">
  <div class="row g-3">
    <div class="col-md-6 adm-form-group">
      <label class="adm-label"><i class="bi bi-facebook" style="color:#1877F2;margin-right:5px;"></i>Facebook URL</label>
      <input type="url" name="facebook" class="adm-input" value="<?php echo sv($all,'facebook'); ?>" placeholder="https://facebook.com/...">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label"><i class="bi bi-instagram" style="color:#E1306C;margin-right:5px;"></i>Instagram URL</label>
      <input type="url" name="instagram" class="adm-input" value="<?php echo sv($all,'instagram'); ?>" placeholder="https://instagram.com/...">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label"><i class="bi bi-linkedin" style="color:#0A66C2;margin-right:5px;"></i>LinkedIn URL</label>
      <input type="url" name="linkedin" class="adm-input" value="<?php echo sv($all,'linkedin'); ?>" placeholder="https://linkedin.com/company/...">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label"><i class="bi bi-youtube" style="color:#FF0000;margin-right:5px;"></i>YouTube URL</label>
      <input type="url" name="youtube" class="adm-input" value="<?php echo sv($all,'youtube'); ?>" placeholder="https://youtube.com/...">
    </div>
  </div>
  <div class="adm-settings-save-row">
    <button type="submit" class="adm-btn adm-btn-primary"><i class="bi bi-check-circle"></i> Save Social Media</button>
  </div>
</form>
<?php settingsSection('social','bi-share','Social Media','Facebook, Instagram, LinkedIn, YouTube',ob_get_clean()); ?>

<!-- ── 8. EMAIL / SMTP ── -->
<?php ob_start(); ?>
<form method="POST">
  <input type="hidden" name="_section" value="email">
  <div class="adm-flash adm-flash-info" style="margin-bottom:16px;">
    <i class="bi bi-info-circle-fill"></i> SMTP credentials are stored encrypted. Leave password blank to keep existing.
  </div>
  <div class="row g-3">
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">SMTP Host</label>
      <input type="text" name="smtp_host" class="adm-input" value="<?php echo sv($all,'smtp_host'); ?>" placeholder="smtp.gmail.com">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">SMTP Port</label>
      <input type="text" name="smtp_port" class="adm-input" value="<?php echo sv($all,'smtp_port','587'); ?>" placeholder="587">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">SMTP Username</label>
      <input type="text" name="smtp_user" class="adm-input" value="<?php echo sv($all,'smtp_user'); ?>" placeholder="your@gmail.com">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">SMTP Password</label>
      <input type="password" name="smtp_pass" class="adm-input" placeholder="Leave blank to keep current">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">From Email</label>
      <input type="email" name="smtp_from" class="adm-input" value="<?php echo sv($all,'smtp_from'); ?>" placeholder="noreply@lavanyaacreation.com">
    </div>
    <div class="col-md-6 adm-form-group">
      <label class="adm-label">From Name</label>
      <input type="text" name="smtp_fromname" class="adm-input" value="<?php echo sv($all,'smtp_fromname','Lavanyaa Creation'); ?>">
    </div>
  </div>
  <div class="adm-settings-save-row">
    <button type="submit" class="adm-btn adm-btn-primary"><i class="bi bi-check-circle"></i> Save Email Settings</button>
  </div>
</form>
<?php settingsSection('email','bi-envelope','Email / SMTP','Outgoing mail configuration',ob_get_clean()); ?>

<?php include __DIR__ . '/../layout_footer.php'; ?>
