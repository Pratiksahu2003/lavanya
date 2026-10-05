<?php
$admin_page_title = 'Premium Clients';
$admin_active     = 'clients';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../functions/uploads.php';

$db = getDB();

// Ensure table exists (migration guard)
$db->exec("CREATE TABLE IF NOT EXISTS `premium_clients` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200) NOT NULL,
  `logo` VARCHAR(255) DEFAULT NULL,
  `website` VARCHAR(255) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `display_order` INT NOT NULL DEFAULT 0,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Handle Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $r = $db->prepare("SELECT logo FROM premium_clients WHERE id=:id");
    $r->execute([':id'=>$_GET['delete']]);
    $row = $r->fetch();
    if ($row && !empty($row['logo'])) deleteUpload($row['logo']);
    $db->prepare("DELETE FROM premium_clients WHERE id=:id")->execute([':id'=>$_GET['delete']]);
    header('Location: index.php?deleted=1'); exit;
}

// Handle Toggle Status
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $db->prepare("UPDATE premium_clients SET status = 1 - status WHERE id=:id")->execute([':id'=>$_GET['toggle']]);
    header('Location: index.php?saved=1'); exit;
}

// Handle Edit Load
$edit = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $s = $db->prepare("SELECT * FROM premium_clients WHERE id=:id");
    $s->execute([':id'=>$_GET['edit']]);
    $edit = $s->fetch();
}

$errors = [];
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $edit_id     = (int)($_POST['edit_id'] ?? 0);
    $client_name = trim($_POST['client_name'] ?? '');
    $website     = trim($_POST['website'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $display_order = (int)($_POST['display_order'] ?? 0);
    $status      = isset($_POST['status']) ? 1 : 0;

    if (!$client_name) $errors[] = 'Client name is required.';

    $logo = null;
    if ($edit_id) {
        $r = $db->prepare("SELECT logo FROM premium_clients WHERE id=:id");
        $r->execute([':id'=>$edit_id]);
        $logo = ($r->fetch())['logo'] ?? null;
    }
    if (!empty($_FILES['client_logo']['name'])) {
        $res = uploadImage($_FILES['client_logo'], 'clients');
        if ($res['success']) {
            if ($logo) deleteUpload($logo);
            $logo = $res['filename'];
        } else {
            $errors[] = $res['error'];
        }
    }

    if (empty($errors)) {
        if ($edit_id) {
            $db->prepare("UPDATE premium_clients SET name=:n, logo=:l, website=:w, description=:d, display_order=:o, status=:s WHERE id=:id")
               ->execute([':n'=>$client_name,':l'=>$logo,':w'=>$website,':d'=>$description,':o'=>$display_order,':s'=>$status,':id'=>$edit_id]);
        } else {
            $db->prepare("INSERT INTO premium_clients (name,logo,website,description,display_order,status) VALUES (:n,:l,:w,:d,:o,:s)")
               ->execute([':n'=>$client_name,':l'=>$logo,':w'=>$website,':d'=>$description,':o'=>$display_order,':s'=>$status]);
        }
        header('Location: index.php?saved=1'); exit;
    }
}

$clients = $db->query("SELECT * FROM premium_clients ORDER BY display_order ASC, name ASC")->fetchAll();
include __DIR__ . '/../layout.php';
?>

<div class="adm-page-header">
  <div>
    <h1>Premium Clients</h1>
    <div class="adm-ph-sub"><?php echo count($clients); ?> clients configured</div>
  </div>
  <a href="index.php" class="adm-btn adm-btn-primary"><i class="bi bi-plus-circle"></i> Add Client</a>
</div>

<?php if (isset($_GET['saved'])): ?>
<div class="adm-flash adm-flash-success"><i class="bi bi-check-circle-fill"></i> Client saved successfully.</div>
<?php endif; ?>
<?php if (isset($_GET['deleted'])): ?>
<div class="adm-flash adm-flash-error"><i class="bi bi-trash-fill"></i> Client deleted.</div>
<?php endif; ?>
<?php foreach ($errors as $e): ?>
<div class="adm-flash adm-flash-error"><i class="bi bi-exclamation-circle-fill"></i><?php echo htmlspecialchars($e); ?></div>
<?php endforeach; ?>

<div class="row g-4">
  <!-- FORM -->
  <div class="col-lg-4">
    <div class="adm-card">
      <h5 class="adm-section-title"><?php echo $edit ? 'Edit Client' : 'Add New Client'; ?></h5>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="edit_id" value="<?php echo $edit ? (int)$edit['id'] : 0; ?>">

        <div class="adm-form-group">
          <label class="adm-label">Client Name <span class="required">*</span></label>
          <input type="text" name="client_name" class="adm-input" required
                 value="<?php echo htmlspecialchars($edit['name'] ?? ($_POST['client_name'] ?? '')); ?>"
                 placeholder="e.g. The Oberoi">
        </div>

        <div class="adm-form-group">
          <label class="adm-label">Logo / Image</label>
          <?php if ($edit && !empty($edit['logo'])): ?>
          <img src="<?php echo htmlspecialchars(getImageUrl($edit['logo'])); ?>" class="adm-img-preview" style="margin-bottom:10px;" alt="Current logo">
          <?php endif; ?>
          <input type="file" name="client_logo" class="adm-input" accept="image/jpeg,image/png,image/webp,image/svg+xml">
          <div class="adm-input-hint">JPG, PNG, WEBP or SVG. Max 2MB.</div>
        </div>

        <div class="adm-form-group">
          <label class="adm-label">Website URL</label>
          <input type="url" name="website" class="adm-input"
                 value="<?php echo htmlspecialchars($edit['website'] ?? ''); ?>"
                 placeholder="https://www.example.com">
        </div>

        <div class="adm-form-group">
          <label class="adm-label">Description</label>
          <textarea name="description" class="adm-input adm-textarea" rows="2"><?php echo htmlspecialchars($edit['description'] ?? ''); ?></textarea>
        </div>

        <div class="row g-3">
          <div class="col-6 adm-form-group">
            <label class="adm-label">Display Order</label>
            <input type="number" name="display_order" class="adm-input" min="0"
                   value="<?php echo (int)($edit['display_order'] ?? 0); ?>">
          </div>
          <div class="col-6 adm-form-group">
            <label class="adm-label">Status</label>
            <div class="adm-form-check" style="margin-top:10px;">
              <input type="checkbox" name="status" id="client_status" value="1"
                     <?php echo ($edit['status'] ?? 1) ? 'checked' : ''; ?>>
              <label for="client_status">Active</label>
            </div>
          </div>
        </div>

        <button type="submit" class="adm-btn adm-btn-primary" style="width:100%;justify-content:center;">
          <i class="bi bi-<?php echo $edit ? 'check-circle' : 'plus-circle'; ?>"></i>
          <?php echo $edit ? 'Update Client' : 'Add Client'; ?>
        </button>
        <?php if ($edit): ?>
        <a href="index.php" class="adm-btn adm-btn-secondary" style="width:100%;justify-content:center;margin-top:8px;">Cancel</a>
        <?php endif; ?>
      </form>
    </div>
  </div>

  <!-- TABLE -->
  <div class="col-lg-8">
    <div class="adm-table">
      <div style="padding:16px 20px;border-bottom:1px solid var(--lc-border);display:flex;align-items:center;justify-content:space-between;">
        <strong>All Premium Clients</strong>
        <span style="font-size:.78rem;color:var(--lc-text-lt);"><?php echo count($clients); ?> total</span>
      </div>
      <div class="adm-table-wrap">
      <table>
        <thead>
          <tr>
            <th>Client</th>
            <th>Website</th>
            <th>Order</th>
            <th>Status</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($clients as $c): ?>
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:12px;">
                <?php if (!empty($c['logo'])): ?>
                <img src="<?php echo htmlspecialchars(getImageUrl($c['logo'])); ?>" class="adm-img-preview" style="width:42px;height:42px;object-fit:contain;padding:4px;" alt="">
                <?php else: ?>
                <div class="adm-client-logo-placeholder"><?php echo strtoupper(substr($c['name'],0,2)); ?></div>
                <?php endif; ?>
                <div>
                  <div style="font-weight:600;font-size:.86rem;"><?php echo htmlspecialchars($c['name']); ?></div>
                  <?php if ($c['description']): ?><div style="font-size:.74rem;color:var(--lc-text-lt);"><?php echo htmlspecialchars(substr($c['description'],0,60)).'…'; ?></div><?php endif; ?>
                </div>
              </div>
            </td>
            <td style="font-size:.8rem;">
              <?php if ($c['website']): ?>
              <a href="<?php echo htmlspecialchars($c['website']); ?>" target="_blank" style="color:var(--lc-info);"><?php echo htmlspecialchars(parse_url($c['website'],PHP_URL_HOST) ?: $c['website']); ?></a>
              <?php else: ?><span style="color:var(--lc-text-lt);">—</span><?php endif; ?>
            </td>
            <td><span style="background:var(--lc-bg);padding:3px 10px;border-radius:6px;font-size:.76rem;font-weight:600;"><?php echo (int)$c['display_order']; ?></span></td>
            <td>
              <a href="?toggle=<?php echo $c['id']; ?>" class="adm-toggle <?php echo $c['status']?'on':''; ?>" title="Toggle Status" style="text-decoration:none;"></a>
            </td>
            <td>
              <div style="display:flex;gap:5px;justify-content:flex-end;">
                <a href="?edit=<?php echo $c['id']; ?>" class="adm-btn adm-btn-secondary adm-btn-sm adm-btn-icon" title="Edit"><i class="bi bi-pencil"></i></a>
                <a href="?delete=<?php echo $c['id']; ?>" class="adm-btn adm-btn-danger adm-btn-sm adm-btn-icon"
                   data-confirm="Delete '<?php echo addslashes($c['name']); ?>'? This cannot be undone." title="Delete"><i class="bi bi-trash"></i></a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($clients)): ?>
          <tr><td colspan="5"><div class="adm-empty"><i class="bi bi-buildings"></i><h4>No clients yet</h4><p>Add your first premium client using the form.</p></div></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layout_footer.php'; ?>
