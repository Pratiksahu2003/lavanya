<?php
$admin_page_title = 'Media Library';
$admin_active     = 'media';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../functions/uploads.php';

$db = getDB();

// Handle Delete
if (isset($_GET['delete'])) {
    $file = basename($_GET['delete']);
    // Find the subfolder
    $upload_root = __DIR__ . '/../../uploads/';
    $found = false;
    foreach (['products','categories','banners','branding','clients','subcategories'] as $folder) {
        $path = $upload_root . $folder . '/' . $file;
        if (file_exists($path)) {
            unlink($path);
            $found = true;
            break;
        }
    }
    // Also try without folder prefix
    if (!$found && file_exists($upload_root . $file)) {
        unlink($upload_root . $file);
    }
    header('Location: index.php?deleted=1'); exit;
}

// Handle Upload
$upload_errors = [];
$upload_success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['media_file']['name'])) {
    $folder = $_POST['folder'] ?? 'products';
    $allowed = ['products','categories','banners','branding','clients'];
    if (!in_array($folder, $allowed)) $folder = 'products';
    $res = uploadImage($_FILES['media_file'], $folder);
    if ($res['success']) {
        $upload_success = true;
    } else {
        $upload_errors[] = $res['error'];
    }
}

// Scan uploads folder
$upload_root = rtrim(__DIR__ . '/../../uploads', '/') . '/';
$all_files = [];
$folders = ['products','categories','banners','branding','clients','subcategories'];
foreach ($folders as $folder) {
    $dir = $upload_root . $folder . '/';
    if (!is_dir($dir)) continue;
    foreach (glob($dir . '*.{jpg,jpeg,png,webp,gif,svg}', GLOB_BRACE) as $f) {
        $all_files[] = [
            'path'     => $folder . '/' . basename($f),
            'name'     => basename($f),
            'folder'   => $folder,
            'size'     => filesize($f),
            'modified' => filemtime($f),
            'url'      => getImageUrl($folder . '/' . basename($f)),
        ];
    }
}
// Sort newest first
usort($all_files, fn($a, $b) => $b['modified'] - $a['modified']);

// Filter
$filter_folder = $_GET['folder'] ?? '';
if ($filter_folder) {
    $all_files = array_filter($all_files, fn($f) => $f['folder'] === $filter_folder);
    $all_files = array_values($all_files);
}

$total_size = array_sum(array_column($all_files, 'size'));

include __DIR__ . '/../layout.php';
?>

<div class="adm-page-header">
  <div>
    <h1>Media Library</h1>
    <div class="adm-ph-sub"><?php echo count($all_files); ?> files &middot; <?php echo round($total_size/1024/1024, 1); ?> MB used</div>
  </div>
  <button class="adm-btn adm-btn-primary" onclick="document.getElementById('upload-panel').style.display=document.getElementById('upload-panel').style.display==='none'?'block':'none'">
    <i class="bi bi-upload"></i> Upload Files
  </button>
</div>

<?php if (isset($_GET['deleted'])): ?>
<div class="adm-flash adm-flash-error"><i class="bi bi-trash-fill"></i> File deleted.</div>
<?php endif; ?>
<?php if ($upload_success): ?>
<div class="adm-flash adm-flash-success"><i class="bi bi-check-circle-fill"></i> File uploaded successfully.</div>
<?php endif; ?>
<?php foreach ($upload_errors as $e): ?>
<div class="adm-flash adm-flash-error"><i class="bi bi-exclamation-circle-fill"></i><?php echo htmlspecialchars($e); ?></div>
<?php endforeach; ?>

<!-- UPLOAD PANEL -->
<div id="upload-panel" style="display:none;" class="adm-card mb-4">
  <h5 class="adm-section-title"><i class="bi bi-upload" style="margin-right:6px;color:var(--lc-primary);"></i>Upload New File</h5>
  <form method="POST" enctype="multipart/form-data">
    <div class="row g-3 align-items-end">
      <div class="col-md-5">
        <label class="adm-label">Select File</label>
        <div class="adm-upload-zone" onclick="this.querySelector('input').click()">
          <i class="bi bi-cloud-upload"></i>
          <div>Click to select image</div>
          <div style="font-size:.72rem;margin-top:4px;">JPG, PNG, WEBP — max 5MB</div>
          <input type="file" name="media_file" accept="image/*" style="display:none;" required
                 onchange="this.closest('.adm-upload-zone').querySelector('div').textContent=this.files[0].name;">
        </div>
      </div>
      <div class="col-md-4">
        <label class="adm-label">Upload To Folder</label>
        <select name="folder" class="adm-input adm-select">
          <option value="products">Products</option>
          <option value="categories">Categories</option>
          <option value="banners">Banners</option>
          <option value="branding">Branding</option>
          <option value="clients">Clients</option>
        </select>
      </div>
      <div class="col-md-3">
        <button type="submit" class="adm-btn adm-btn-primary" style="width:100%;justify-content:center;"><i class="bi bi-upload"></i> Upload</button>
      </div>
    </div>
  </form>
</div>

<!-- FILTER BAR -->
<div class="adm-card mb-4" style="padding:14px 20px;">
  <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
    <span style="font-size:.78rem;font-weight:700;color:var(--lc-text-lt);text-transform:uppercase;letter-spacing:.08em;">Filter:</span>
    <a href="index.php" class="adm-btn adm-btn-sm <?php echo !$filter_folder?'adm-btn-primary':'adm-btn-secondary'; ?>">All</a>
    <?php foreach ($folders as $f): ?>
    <a href="?folder=<?php echo $f; ?>" class="adm-btn adm-btn-sm <?php echo $filter_folder===$f?'adm-btn-primary':'adm-btn-secondary'; ?>"><?php echo ucfirst($f); ?></a>
    <?php endforeach; ?>
  </div>
</div>

<!-- MEDIA GRID -->
<?php if (empty($all_files)): ?>
<div class="adm-card">
  <div class="adm-empty">
    <i class="bi bi-images"></i>
    <h4>No files found</h4>
    <p>Upload your first file using the button above.</p>
  </div>
</div>
<?php else: ?>
<div class="adm-media-grid">
  <?php foreach ($all_files as $f): ?>
  <div class="adm-media-item">
    <a href="<?php echo htmlspecialchars($f['url']); ?>" target="_blank">
      <img src="<?php echo htmlspecialchars($f['url']); ?>" alt="<?php echo htmlspecialchars($f['name']); ?>" loading="lazy">
    </a>
    <div class="adm-media-info">
      <strong title="<?php echo htmlspecialchars($f['name']); ?>"><?php echo htmlspecialchars($f['name']); ?></strong>
      <span><?php echo round($f['size']/1024,1); ?> KB &middot; <?php echo $f['folder']; ?></span>
    </div>
    <div class="adm-media-actions">
      <a href="<?php echo htmlspecialchars($f['url']); ?>" target="_blank" class="adm-btn adm-btn-secondary adm-btn-sm" style="flex:1;justify-content:center;" title="View Full Size"><i class="bi bi-arrows-fullscreen"></i></a>
      <a href="?delete=<?php echo urlencode($f['name']); ?>" class="adm-btn adm-btn-danger adm-btn-sm adm-btn-icon"
         data-confirm="Delete '<?php echo addslashes($f['name']); ?>'? This cannot be undone."
         title="Delete"><i class="bi bi-trash"></i></a>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../layout_footer.php'; ?>
