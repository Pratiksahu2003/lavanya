<?php
$admin_page_title = 'Dashboard';
$admin_active     = 'dashboard';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../functions/inquiries.php';
require_once __DIR__ . '/../functions/uploads.php';
require_once __DIR__ . '/../includes/auth.php';
$db = getDB();

$total_products = (int)$db->query("SELECT COUNT(*) FROM products")->fetchColumn();
$active_products= (int)$db->query("SELECT COUNT(*) FROM products WHERE status='active'")->fetchColumn();
$total_cats     = (int)$db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$total_inq      = (int)$db->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();
$total_msg      = (int)$db->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
$total_clients  = (int)$db->query("SELECT COUNT(*) FROM premium_clients WHERE status=1")->fetchColumn();
$unread_inq     = countUnreadInquiries();
$unread_msg     = countUnreadMessages();

// Today's inquiries
$today_inq = (int)$db->query("SELECT COUNT(*) FROM inquiries WHERE DATE(created_at) = CURDATE()")->fetchColumn();
$today_msg  = (int)$db->query("SELECT COUNT(*) FROM contact_messages WHERE DATE(created_at) = CURDATE()")->fetchColumn();

$recent_products = $db->query(
    "SELECT p.*, c.name AS category_name, pi.image AS primary_image
     FROM products p
     LEFT JOIN categories c ON c.id = p.category_id
     LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
     ORDER BY p.id DESC LIMIT 6"
)->fetchAll();

$recent_inq = $db->query(
    "SELECT * FROM inquiries ORDER BY created_at DESC LIMIT 8"
)->fetchAll();

include __DIR__ . '/layout.php';
?>

<!-- WELCOME BAR -->
<div class="adm-card mb-4" style="background:linear-gradient(135deg,#6E4B3A 0%,#4E3428 100%);border:none;padding:24px 28px;">
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
    <div>
      <div style="color:rgba(255,255,255,.65);font-size:.72rem;font-weight:600;letter-spacing:.14em;text-transform:uppercase;margin-bottom:4px;">Welcome back</div>
      <h2 style="color:#fff;margin:0;font-size:1.4rem;font-weight:800;"><?php echo htmlspecialchars(currentAdmin()['name'] ?: 'Administrator'); ?></h2>
      <div style="color:rgba(255,255,255,.55);font-size:.8rem;margin-top:4px;"><?php echo date('l, d F Y'); ?></div>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
      <a href="<?php echo BASE_URL; ?>/admin/products/add.php" class="adm-btn adm-btn-accent"><i class="bi bi-plus-circle"></i> Add Product</a>
      <a href="<?php echo BASE_URL; ?>/admin/inquiries/index.php" class="adm-btn" style="background:rgba(255,255,255,.12);color:#fff;border-color:rgba(255,255,255,.2);">
        <i class="bi bi-chat-dots"></i> View Inquiries
        <?php if ($unread_inq+$unread_msg > 0): ?><span style="background:#C7A86D;color:#1a0d06;border-radius:20px;padding:1px 7px;font-size:.65rem;font-weight:800;"><?php echo $unread_inq+$unread_msg; ?></span><?php endif; ?>
      </a>
    </div>
  </div>
</div>

<!-- STAT CARDS -->
<div class="row g-3 mb-4">
  <div class="col-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
      <div class="stat-body">
        <div class="stat-val"><?php echo $total_products; ?></div>
        <div class="stat-label">Total Products</div>
        <div class="stat-change"><i class="bi bi-circle-fill" style="font-size:.5rem;color:#12B76A;"></i> <?php echo $active_products; ?> active</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon blue"><i class="bi bi-chat-dots"></i></div>
      <div class="stat-body">
        <div class="stat-val"><?php echo $total_inq; ?></div>
        <div class="stat-label">Total Inquiries</div>
        <?php if ($unread_inq): ?><div class="stat-change" style="color:#DC6803;"><i class="bi bi-dot"></i><?php echo $unread_inq; ?> unread</div><?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon green"><i class="bi bi-envelope"></i></div>
      <div class="stat-body">
        <div class="stat-val"><?php echo $total_msg; ?></div>
        <div class="stat-label">Contact Messages</div>
        <?php if ($unread_msg): ?><div class="stat-change" style="color:#DC6803;"><i class="bi bi-dot"></i><?php echo $unread_msg; ?> unread</div><?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon orange"><i class="bi bi-buildings"></i></div>
      <div class="stat-body">
        <div class="stat-val"><?php echo $total_clients; ?></div>
        <div class="stat-label">Premium Clients</div>
        <div class="stat-change"><?php echo $total_cats; ?> categories</div>
      </div>
    </div>
  </div>
</div>

<!-- TODAY STRIP -->
<?php if ($today_inq + $today_msg > 0): ?>
<div class="adm-flash adm-flash-info mb-4">
  <i class="bi bi-info-circle-fill"></i>
  <strong>Today:</strong> <?php echo $today_inq; ?> new <?php echo $today_inq===1?'inquiry':'inquiries'; ?><?php if($today_msg): ?> + <?php echo $today_msg; ?> message<?php echo $today_msg!==1?'s':''; ?><?php endif; ?> received.
  <a href="<?php echo BASE_URL; ?>/admin/inquiries/index.php" style="margin-left:auto;font-weight:700;color:inherit;">View All →</a>
</div>
<?php endif; ?>

<!-- QUICK ACTIONS -->
<div class="adm-card mb-4">
  <div class="adm-card-header">
    <h5 class="adm-card-title"><i class="bi bi-lightning-charge" style="color:var(--lc-accent);margin-right:6px;"></i>Quick Actions</h5>
  </div>
  <div class="adm-quick-grid">
    <a href="<?php echo BASE_URL; ?>/admin/products/add.php" class="adm-quick-card"><i class="bi bi-plus-square"></i>Add Product</a>
    <a href="<?php echo BASE_URL; ?>/admin/categories/add.php" class="adm-quick-card"><i class="bi bi-folder-plus"></i>Add Category</a>
    <a href="<?php echo BASE_URL; ?>/admin/banners/index.php" class="adm-quick-card"><i class="bi bi-image"></i>Banners</a>
    <a href="<?php echo BASE_URL; ?>/admin/clients/index.php" class="adm-quick-card"><i class="bi bi-buildings"></i>Clients</a>
    <a href="<?php echo BASE_URL; ?>/admin/media/index.php" class="adm-quick-card"><i class="bi bi-images"></i>Media</a>
    <a href="<?php echo BASE_URL; ?>/admin/inquiries/index.php" class="adm-quick-card"><i class="bi bi-chat-dots"></i>Inquiries</a>
    <a href="<?php echo BASE_URL; ?>/admin/inquiries/messages.php" class="adm-quick-card"><i class="bi bi-envelope"></i>Messages</a>
    <a href="<?php echo BASE_URL; ?>/admin/settings/index.php" class="adm-quick-card"><i class="bi bi-gear"></i>Settings</a>
  </div>
</div>

<div class="row g-4">
  <!-- RECENT PRODUCTS -->
  <div class="col-lg-7">
    <div class="adm-table">
      <div style="padding:16px 20px;border-bottom:1px solid var(--lc-border);display:flex;align-items:center;justify-content:space-between;">
        <strong style="font-size:.9rem;">Recent Products</strong>
        <a href="<?php echo BASE_URL; ?>/admin/products/index.php" class="adm-btn adm-btn-ghost adm-btn-sm">View All <i class="bi bi-arrow-right"></i></a>
      </div>
      <div class="adm-table-wrap">
      <table>
        <thead>
          <tr>
            <th>Product</th>
            <th>Code</th>
            <th>Category</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recent_products as $p): ?>
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:10px;">
                <?php if (!empty($p['primary_image'])): ?>
                <img src="<?php echo htmlspecialchars(getImageUrl($p['primary_image'])); ?>" class="adm-img-preview" style="width:38px;height:38px;" alt="">
                <?php else: ?>
                <div style="width:38px;height:38px;background:var(--lc-bg);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:1px solid var(--lc-border);"><i class="bi bi-image" style="color:#ccc;font-size:.9rem;"></i></div>
                <?php endif; ?>
                <span style="font-weight:600;font-size:.84rem;"><?php echo htmlspecialchars($p['name']); ?></span>
              </div>
            </td>
            <td><code style="font-size:.76rem;background:#F3F4F6;padding:2px 6px;border-radius:4px;color:var(--lc-text-md);"><?php echo htmlspecialchars($p['product_code']); ?></code></td>
            <td style="font-size:.8rem;color:var(--lc-text-lt);"><?php echo htmlspecialchars($p['category_name'] ?? '—'); ?></td>
            <td><?php echo $p['status']==='active'?'<span class="badge-active">Active</span>':'<span class="badge-inactive">'.ucfirst($p['status']).'</span>'; ?></td>
            <td>
              <div style="display:flex;gap:5px;">
                <a href="<?php echo BASE_URL; ?>/product.php?slug=<?php echo urlencode($p['slug']); ?>" target="_blank" class="adm-btn adm-btn-secondary adm-btn-sm adm-btn-icon" title="View"><i class="bi bi-eye"></i></a>
                <a href="<?php echo BASE_URL; ?>/admin/products/edit.php?id=<?php echo $p['id']; ?>" class="adm-btn adm-btn-secondary adm-btn-sm adm-btn-icon" title="Edit"><i class="bi bi-pencil"></i></a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($recent_products)): ?>
          <tr><td colspan="5"><div class="adm-empty"><i class="bi bi-box-seam"></i><h4>No products yet</h4><p><a href="<?php echo BASE_URL; ?>/admin/products/add.php" style="color:var(--lc-primary);">Add your first product</a></p></div></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
      </div>
    </div>
  </div>

  <!-- RECENT INQUIRIES -->
  <div class="col-lg-5">
    <div class="adm-table">
      <div style="padding:16px 20px;border-bottom:1px solid var(--lc-border);display:flex;align-items:center;justify-content:space-between;">
        <strong style="font-size:.9rem;">Recent Inquiries</strong>
        <a href="<?php echo BASE_URL; ?>/admin/inquiries/index.php" class="adm-btn adm-btn-ghost adm-btn-sm">View All <i class="bi bi-arrow-right"></i></a>
      </div>
      <div class="adm-timeline" style="padding:0 20px;">
        <?php foreach ($recent_inq as $inq): ?>
        <div class="adm-tl-item" style="<?php echo !$inq['is_read']?'font-weight:600;':''; ?>">
          <div class="adm-tl-dot" style="<?php echo !$inq['is_read']?'background:var(--lc-danger);':''; ?>"></div>
          <div class="adm-tl-body">
            <div class="adm-tl-title">
              <?php echo htmlspecialchars($inq['name']); ?>
              <?php if (!$inq['is_read']): ?><span class="badge-info" style="font-size:.6rem;margin-left:6px;">New</span><?php endif; ?>
            </div>
            <div style="display:flex;gap:8px;align-items:center;margin-top:3px;">
              <span class="badge-pending"><?php echo ucfirst($inq['type']); ?></span>
              <span class="adm-tl-time"><?php echo date('d M, h:i A', strtotime($inq['created_at'])); ?></span>
            </div>
          </div>
          <a href="<?php echo BASE_URL; ?>/admin/inquiries/index.php?view=<?php echo $inq['id']; ?>" class="adm-btn adm-btn-ghost adm-btn-sm adm-btn-icon"><i class="bi bi-eye"></i></a>
        </div>
        <?php endforeach; ?>
        <?php if (empty($recent_inq)): ?>
        <div class="adm-empty"><i class="bi bi-chat-dots"></i><h4>No inquiries yet</h4></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/layout_footer.php'; ?>
