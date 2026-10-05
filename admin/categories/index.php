<?php
$admin_page_title = 'Categories';
$admin_active     = 'categories';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../functions/uploads.php';

$db = getDB();

// Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $r = $db->prepare("SELECT image FROM categories WHERE id=:id");
    $r->execute([':id'=>$_GET['delete']]); $row = $r->fetch();
    if ($row && !empty($row['image'])) deleteUpload($row['image']);
    $db->prepare("DELETE FROM categories WHERE id=:id")->execute([':id'=>$_GET['delete']]);
    header('Location: index.php?deleted=1'); exit;
}
// Toggle
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $db->prepare("UPDATE categories SET is_active=1-is_active WHERE id=:id")->execute([':id'=>$_GET['toggle']]);
    header('Location: index.php?saved=1'); exit;
}

// Edit load
$edit = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $s = $db->prepare("SELECT * FROM categories WHERE id=:id");
    $s->execute([':id'=>$_GET['edit']]); $edit = $s->fetch();
}

$errors = []; $saved = false;
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $edit_id     = (int)($_POST['edit_id'] ?? 0);
    $name        = trim($_POST['name']        ?? '');
    $slug        = trim($_POST['slug']        ?? '');
    $description = trim($_POST['description'] ?? '');
    $icon        = trim($_POST['icon']        ?? '');
    $order       = (int)($_POST['display_order'] ?? 0);
    $status      = isset($_POST['status']) ? 1 : 0;

    if (!$name) $errors[] = 'Category name is required.';
    if (!$slug) $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/','-',$name));

    $image = null;
    if ($edit_id) {
        $r = $db->prepare("SELECT image FROM categories WHERE id=:id");
        $r->execute([':id'=>$edit_id]); $image = ($r->fetch())['image'] ?? null;
    }
    if (!empty($_FILES['image']['name'])) {
        $res = uploadImage($_FILES['image'], 'categories');
        if ($res['success']) { if($image) deleteUpload($image); $image = $res['filename']; }
        else $errors[] = $res['error'];
    }

    if (empty($errors)) {
        if ($edit_id) {
            $db->prepare("UPDATE categories SET name=:n,slug=:s,description=:d,icon=:ic,image=:img,sort_order=:o,is_active=:st WHERE id=:id")
               ->execute([':n'=>$name,':s'=>$slug,':d'=>$description,':ic'=>$icon,':img'=>$image,':o'=>$order,':st'=>$status,':id'=>$edit_id]);
        } else {
            $db->prepare("INSERT INTO categories (name,slug,description,icon,image,sort_order,is_active) VALUES (:n,:s,:d,:ic,:img,:o,:st)")
               ->execute([':n'=>$name,':s'=>$slug,':d'=>$description,':ic'=>$icon,':img'=>$image,':o'=>$order,':st'=>$status]);
        }
        header('Location: index.php?saved=1'); exit;
    }
}

$categories = $db->query("SELECT c.*,(SELECT COUNT(*) FROM subcategories s WHERE s.category_id=c.id) AS sub_count,(SELECT COUNT(*) FROM products p WHERE p.category_id=c.id) AS prod_count FROM categories c ORDER BY c.sort_order ASC, c.name ASC")->fetchAll();

include __DIR__ . '/../layout.php';
?>

<div class="adm-page-header">
  <div><h1>Categories</h1><div class="adm-ph-sub"><?php echo count($categories); ?> categories configured</div></div>
</div>

<?php if (isset($_GET['saved'])): ?><div class="adm-flash adm-flash-success"><i class="bi bi-check-circle-fill"></i> Saved successfully.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><div class="adm-flash adm-flash-error"><i class="bi bi-trash-fill"></i> Category deleted.</div><?php endif; ?>
<?php foreach ($errors as $e): ?><div class="adm-flash adm-flash-error"><i class="bi bi-exclamation-circle-fill"></i><?php echo htmlspecialchars($e); ?></div><?php endforeach; ?>

<div class="row g-4">
  <!-- FORM -->
  <div class="col-lg-4">
    <div class="adm-card">
      <h5 class="adm-section-title"><?php echo $edit?'Edit Category':'Add Category'; ?></h5>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="edit_id" value="<?php echo $edit?(int)$edit['id']:0; ?>">
        <div class="adm-form-group">
          <label class="adm-label">Name <span class="required">*</span></label>
          <input type="text" name="name" class="adm-input" required value="<?php echo htmlspecialchars($edit['name']??''); ?>"
                 oninput="if(!this.form.querySelector('[name=edit_id]').value||this.form.querySelector('[name=edit_id]').value=='0'){document.getElementById('slug-field').value=this.value.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'')}">
        </div>
        <div class="adm-form-group">
          <label class="adm-label">URL Slug</label>
          <input type="text" name="slug" id="slug-field" class="adm-input" value="<?php echo htmlspecialchars($edit['slug']??''); ?>" placeholder="auto-generated">
          <div class="adm-input-hint">Used in URL: /category.php?cat=<strong>slug</strong></div>
        </div>
        <div class="adm-form-group">
          <label class="adm-label">Description</label>
          <textarea name="description" class="adm-input adm-textarea" rows="2"><?php echo htmlspecialchars($edit['description']??''); ?></textarea>
        </div>
        <div class="adm-form-group">
          <label class="adm-label">Icon Class (Bootstrap Icons)</label>
          <input type="text" name="icon" class="adm-input" value="<?php echo htmlspecialchars($edit['icon']??''); ?>" placeholder="bi-house">
          <div class="adm-input-hint">e.g. bi-house, bi-briefcase</div>
        </div>
        <div class="adm-form-group">
          <label class="adm-label">Banner Image</label>
          <?php if ($edit && !empty($edit['image'])): ?>
          <img src="<?php echo htmlspecialchars(getImageUrl($edit['image'])); ?>" class="adm-img-preview" style="margin-bottom:8px;width:80px;height:60px;object-fit:cover;" alt="">
          <?php endif; ?>
          <input type="file" name="image" class="adm-input" accept="image/jpeg,image/png,image/webp">
        </div>
        <div class="row g-2">
          <div class="col-6 adm-form-group">
            <label class="adm-label">Display Order</label>
            <input type="number" name="display_order" class="adm-input" min="0" value="<?php echo (int)($edit['sort_order']??0); ?>">
          </div>
          <div class="col-6 adm-form-group">
            <label class="adm-label">Status</label>
            <div class="adm-form-check" style="margin-top:10px;">
              <input type="checkbox" name="status" id="cat_status" <?php echo ($edit['is_active']??1)?'checked':''; ?>>
              <label for="cat_status">Active</label>
            </div>
          </div>
        </div>
        <button type="submit" class="adm-btn adm-btn-primary" style="width:100%;justify-content:center;">
          <i class="bi bi-<?php echo $edit?'check-circle':'plus-circle'; ?>"></i> <?php echo $edit?'Update':'Add Category'; ?>
        </button>
        <?php if ($edit): ?><a href="index.php" class="adm-btn adm-btn-secondary" style="width:100%;justify-content:center;margin-top:8px;">Cancel</a><?php endif; ?>
      </form>
    </div>
  </div>

  <!-- TABLE -->
  <div class="col-lg-8">
    <div class="adm-table">
      <div style="padding:16px 20px;border-bottom:1px solid var(--lc-border);">
        <strong>All Categories</strong>
      </div>
      <div class="adm-table-wrap">
      <table>
        <thead><tr><th>Category</th><th>Slug</th><th>Subs</th><th>Products</th><th>Order</th><th>Status</th><th style="text-align:right;">Actions</th></tr></thead>
        <tbody>
          <?php foreach ($categories as $c): ?>
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:10px;">
                <?php if (!empty($c['image'])): ?>
                <img src="<?php echo htmlspecialchars(getImageUrl($c['image'])); ?>" class="adm-img-preview" style="width:36px;height:36px;object-fit:cover;" alt="">
                <?php else: ?>
                <div style="width:36px;height:36px;background:rgba(110,75,58,.08);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                  <i class="bi <?php echo htmlspecialchars($c['icon']??'bi-grid'); ?>" style="color:var(--lc-primary);font-size:.9rem;"></i>
                </div>
                <?php endif; ?>
                <span style="font-weight:600;font-size:.86rem;"><?php echo htmlspecialchars($c['name']); ?></span>
              </div>
            </td>
            <td><code style="font-size:.76rem;background:#F3F4F6;padding:2px 7px;border-radius:4px;"><?php echo htmlspecialchars($c['slug']); ?></code></td>
            <td><span class="badge-info"><?php echo $c['sub_count']; ?></span></td>
            <td><span class="badge-primary"><?php echo $c['prod_count']; ?></span></td>
            <td><?php echo (int)$c['sort_order']; ?></td>
            <td><a href="?toggle=<?php echo $c['id']; ?>" class="adm-toggle <?php echo $c['is_active']?'on':''; ?>" style="text-decoration:none;" onclick="return confirm('Toggle?')"></a></td>
            <td>
              <div style="display:flex;gap:5px;justify-content:flex-end;">
                <a href="?edit=<?php echo $c['id']; ?>" class="adm-btn adm-btn-secondary adm-btn-sm adm-btn-icon" title="Edit"><i class="bi bi-pencil"></i></a>
                <a href="?delete=<?php echo $c['id']; ?>" class="adm-btn adm-btn-danger adm-btn-sm adm-btn-icon"
                   data-confirm="Delete '<?php echo addslashes($c['name']); ?>'? Subcategories and products in this category will also be affected." title="Delete"><i class="bi bi-trash"></i></a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($categories)): ?>
          <tr><td colspan="7"><div class="adm-empty"><i class="bi bi-grid-3x3"></i><h4>No categories</h4></div></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layout_footer.php'; ?>
