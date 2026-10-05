<?php
$admin_page_title = 'Sub Categories';
$admin_active     = 'subcategories';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../functions/uploads.php';

$db = getDB();

function makeSubcategorySlug(string $name): string {
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
    return trim($slug, '-') ?: 'subcategory';
}

function getUniqueSubcategorySlug(PDO $db, string $slug, int $excludeId = 0): string {
    $base = $slug;
    $i = 2;

    while (true) {
        $sql = "SELECT id FROM subcategories WHERE slug = :slug";
        $params = [':slug' => $slug];
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        if (!$stmt->fetch()) return $slug;

        $slug = $base . '-' . $i++;
    }
}

// Handle Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];

    $count = $db->prepare("SELECT COUNT(*) FROM products WHERE subcategory_id = :id");
    $count->execute([':id' => $delete_id]);

    if ((int)$count->fetchColumn() > 0) {
        header('Location: index.php?delete_blocked=1');
        exit;
    }

    $db->prepare("DELETE FROM subcategories WHERE id = :id")->execute([':id' => $delete_id]);
    header('Location: index.php?deleted=1');
    exit;
}

// Handle Save (Add/Edit)
$edit_subcategory = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $s = $db->prepare("SELECT * FROM subcategories WHERE id = :id");
    $s->execute([':id' => (int)$_GET['edit']]);
    $edit_subcategory = $s->fetch();
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $edit_id     = (int)($_POST['edit_id'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $name        = trim($_POST['name'] ?? '');
    $slug        = trim($_POST['slug'] ?? '');
    $sort        = (int)($_POST['sort_order'] ?? 0);
    $active      = isset($_POST['is_active']) ? 1 : 0;

    if (!$category_id) $errors[] = 'Parent category is required.';
    if (!$name) $errors[] = 'Subcategory name is required.';

    if ($category_id) {
        $cat_check = $db->prepare("SELECT id FROM categories WHERE id = :id");
        $cat_check->execute([':id' => $category_id]);
        if (!$cat_check->fetch()) $errors[] = 'Selected parent category was not found.';
    }

    $image = null;
    if ($edit_id) {
        $row = $db->prepare("SELECT image FROM subcategories WHERE id = :id");
        $row->execute([':id' => $edit_id]);
        $image = ($row->fetch())['image'] ?? null;
    }
    if (!empty($_FILES['image']['name'])) {
        $res = uploadImage($_FILES['image'], 'subcategories');
        if ($res['success']) {
            if ($image) deleteUpload($image);
            $image = $res['filename'];
        } else {
            $errors[] = $res['error'];
        }
    }

    if (empty($errors)) {
        $slug = getUniqueSubcategorySlug($db, makeSubcategorySlug($slug ?: $name), $edit_id);

        if ($edit_id) {
            $db->prepare("UPDATE subcategories SET category_id=:cat, name=:n, slug=:s, image=:img, sort_order=:so, is_active=:a WHERE id=:id")
               ->execute([':cat'=>$category_id, ':n'=>$name, ':s'=>$slug, ':img'=>$image, ':so'=>$sort, ':a'=>$active, ':id'=>$edit_id]);
        } else {
            $db->prepare("INSERT INTO subcategories (category_id,name,slug,image,sort_order,is_active) VALUES (:cat,:n,:s,:img,:so,:a)")
               ->execute([':cat'=>$category_id, ':n'=>$name, ':s'=>$slug, ':img'=>$image, ':so'=>$sort, ':a'=>$active]);
        }

        header('Location: index.php?saved=1');
        exit;
    }
}

$categories = $db->query("SELECT id, name FROM categories ORDER BY sort_order ASC, name ASC")->fetchAll();

$subcategories = $db->query(
    "SELECT sc.*, c.name AS category_name,
            (SELECT COUNT(*) FROM products p WHERE p.subcategory_id = sc.id) AS product_count
     FROM subcategories sc
     LEFT JOIN categories c ON c.id = sc.category_id
     ORDER BY c.sort_order ASC, c.name ASC, sc.sort_order ASC, sc.name ASC"
)->fetchAll();

include __DIR__ . '/../layout.php';
?>

<div class="adm-page-header">
  <h1>Sub Categories</h1>
  <a href="?add=1" class="adm-btn adm-btn-primary"><i class="bi bi-plus-circle"></i> Add Sub Category</a>
</div>

<?php if (isset($_GET['saved'])): ?><div class="adm-flash" style="background:rgba(25,180,80,0.1);color:#12a04a;border-radius:9px;padding:11px 16px;margin-bottom:16px;">Saved successfully.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><div class="adm-flash" style="background:rgba(200,0,0,0.1);color:#c00;border-radius:9px;padding:11px 16px;margin-bottom:16px;">Subcategory deleted.</div><?php endif; ?>
<?php if (isset($_GET['delete_blocked'])): ?><div class="adm-flash" style="background:rgba(245,160,0,0.12);color:#c87800;border-radius:9px;padding:11px 16px;margin-bottom:16px;">Cannot delete this subcategory because products are using it.</div><?php endif; ?>

<?php if (isset($_GET['add']) || $edit_subcategory): ?>
<div class="adm-card mb-4">
  <h5 style="font-weight:700;margin-bottom:20px;"><?php echo $edit_subcategory ? 'Edit Sub Category' : 'New Sub Category'; ?></h5>
  <?php foreach ($errors as $e): ?><div style="color:#c00;font-size:0.85rem;margin-bottom:8px;"><i class="bi bi-exclamation-circle me-1"></i><?php echo htmlspecialchars($e); ?></div><?php endforeach; ?>
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="edit_id" value="<?php echo $edit_subcategory['id'] ?? 0; ?>">
    <div class="row g-3">
      <div class="col-md-4">
        <label class="adm-label">Parent Category *</label>
        <select name="category_id" class="adm-input" required>
          <option value="">Select Category</option>
          <?php foreach ($categories as $c): ?>
          <option value="<?php echo $c['id']; ?>" <?php echo ($edit_subcategory['category_id'] ?? 0) == $c['id'] ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($c['name']); ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <label class="adm-label">Subcategory Name *</label>
        <input type="text" name="name" class="adm-input" required value="<?php echo htmlspecialchars($edit_subcategory['name'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
        <label class="adm-label">Slug</label>
        <input type="text" name="slug" class="adm-input" value="<?php echo htmlspecialchars($edit_subcategory['slug'] ?? ''); ?>" placeholder="auto-generated">
      </div>
      <div class="col-md-2">
        <label class="adm-label">Sort Order</label>
        <input type="number" name="sort_order" class="adm-input" value="<?php echo $edit_subcategory['sort_order'] ?? 0; ?>">
      </div>
      <div class="col-md-4">
        <label class="adm-label">Banner Image</label>
        <?php if (!empty($edit_subcategory['image'])): ?>
        <img src="<?php echo htmlspecialchars(getImageUrl($edit_subcategory['image'])); ?>" class="adm-img-preview" style="margin-bottom:8px;width:100%;height:80px;object-fit:cover;" alt="">
        <?php endif; ?>
        <input type="file" name="image" class="adm-input" accept="image/jpeg,image/png,image/webp">
      </div>
      <div class="col-md-3" style="display:flex;align-items:end;">
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:0.88rem;">
          <input type="checkbox" name="is_active" value="1" <?php echo ($edit_subcategory['is_active'] ?? 1) ? 'checked' : ''; ?> style="accent-color:var(--brown);"> Active
        </label>
      </div>
      <div class="col-12 d-flex gap-2">
        <button type="submit" class="adm-btn adm-btn-primary"><i class="bi bi-check-circle"></i> Save Sub Category</button>
        <a href="index.php" class="adm-btn adm-btn-secondary">Cancel</a>
      </div>
    </div>
  </form>
</div>
<?php endif; ?>

<div class="adm-table">
  <table>
    <thead><tr><th>Subcategory</th><th>Parent Category</th><th>Sort Order</th><th>Products</th><th>Status</th><th style="text-align:right;">Actions</th></tr></thead>
    <tbody>
      <?php foreach ($subcategories as $s): ?>
      <tr>
        <td style="font-weight:600;"><?php echo htmlspecialchars($s['name']); ?></td>
        <td><?php echo htmlspecialchars($s['category_name'] ?? ''); ?></td>
        <td style="color:#666;"><?php echo (int)$s['sort_order']; ?></td>
        <td style="color:#666;"><?php echo (int)$s['product_count']; ?></td>
        <td><?php echo $s['is_active'] ? '<span class="badge-active">Active</span>' : '<span class="badge-inactive">Inactive</span>'; ?></td>
        <td style="text-align:right;">
          <div class="d-flex gap-2 justify-content-end">
            <a href="?edit=<?php echo $s['id']; ?>" class="adm-btn adm-btn-secondary adm-btn-sm"><i class="bi bi-pencil"></i></a>
            <a href="?delete=<?php echo $s['id']; ?>" class="adm-btn adm-btn-danger adm-btn-sm" data-confirm="Delete subcategory '<?php echo addslashes($s['name']); ?>'?"><i class="bi bi-trash"></i></a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/../layout_footer.php'; ?>
