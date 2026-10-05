<?php
$admin_page_title = 'Collections';
$admin_active     = 'collections';
require_once __DIR__ . '/../../config/db.php';

$db = getDB();

if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM collections WHERE id=:id")->execute([':id'=>$_GET['delete']]);
    header('Location: index.php?deleted=1'); exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $s = $db->prepare("SELECT * FROM collections WHERE id=:id"); $s->execute([':id'=>$_GET['edit']]);
    $edit = $s->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $edit_id = (int)($_POST['edit_id'] ?? 0);
    $name    = trim($_POST['name'] ?? '');
    $slug    = trim($_POST['slug'] ?? '') ?: strtolower(preg_replace('/[^a-z0-9]+/i','-',$name));
    $desc    = trim($_POST['description'] ?? '');
    $sort    = (int)($_POST['sort_order'] ?? 0);
    $active  = isset($_POST['is_active']) ? 1 : 0;

    if ($edit_id) {
        $db->prepare("UPDATE collections SET name=:n, slug=:s, description=:d, sort_order=:so, is_active=:a WHERE id=:id")
           ->execute([':n'=>$name,':s'=>$slug,':d'=>$desc,':so'=>$sort,':a'=>$active,':id'=>$edit_id]);
    } else {
        $db->prepare("INSERT INTO collections (name,slug,description,sort_order,is_active) VALUES (:n,:s,:d,:so,:a)")
           ->execute([':n'=>$name,':s'=>$slug,':d'=>$desc,':so'=>$sort,':a'=>$active]);
    }
    header('Location: index.php?saved=1'); exit;
}

$collections = $db->query("SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.collection_id=c.id) AS product_count FROM collections c ORDER BY c.sort_order ASC")->fetchAll();
include __DIR__ . '/../layout.php';
?>

<div class="adm-page-header">
  <h1>Collections</h1>
  <a href="?add=1" class="adm-btn adm-btn-primary"><i class="bi bi-plus-circle"></i> Add Collection</a>
</div>

<?php if (isset($_GET['saved'])): ?><div class="adm-flash" style="background:rgba(25,180,80,0.1);color:#12a04a;border-radius:9px;padding:11px 16px;margin-bottom:16px;">Saved.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><div class="adm-flash" style="background:rgba(200,0,0,0.1);color:#c00;border-radius:9px;padding:11px 16px;margin-bottom:16px;">Deleted.</div><?php endif; ?>

<?php if (isset($_GET['add']) || $edit): ?>
<div class="adm-card mb-4">
  <h5 style="font-weight:700;margin-bottom:20px;"><?php echo $edit ? 'Edit Collection' : 'New Collection'; ?></h5>
  <form method="POST">
    <input type="hidden" name="edit_id" value="<?php echo $edit['id'] ?? 0; ?>">
    <div class="row g-3">
      <div class="col-md-5"><label class="adm-label">Name *</label><input type="text" name="name" class="adm-input" required value="<?php echo htmlspecialchars($edit['name'] ?? ''); ?>"></div>
      <div class="col-md-4"><label class="adm-label">Slug</label><input type="text" name="slug" class="adm-input" value="<?php echo htmlspecialchars($edit['slug'] ?? ''); ?>"></div>
      <div class="col-md-2"><label class="adm-label">Sort</label><input type="number" name="sort_order" class="adm-input" value="<?php echo $edit['sort_order'] ?? 0; ?>"></div>
      <div class="col-md-1" style="display:flex;align-items:end;"><label style="display:flex;align-items:center;gap:5px;font-size:0.82rem;cursor:pointer;"><input type="checkbox" name="is_active" value="1" <?php echo ($edit['is_active'] ?? 1)?'checked':''; ?> style="accent-color:var(--brown);"> Active</label></div>
      <div class="col-12"><label class="adm-label">Description</label><input type="text" name="description" class="adm-input" value="<?php echo htmlspecialchars($edit['description'] ?? ''); ?>"></div>
      <div class="col-12 d-flex gap-2">
        <button type="submit" class="adm-btn adm-btn-primary"><i class="bi bi-check-circle"></i> Save</button>
        <a href="index.php" class="adm-btn adm-btn-secondary">Cancel</a>
      </div>
    </div>
  </form>
</div>
<?php endif; ?>

<div class="adm-table">
  <table>
    <thead><tr><th>Name</th><th>Slug</th><th>Products</th><th>Status</th><th style="text-align:right;">Actions</th></tr></thead>
    <tbody>
      <?php foreach ($collections as $c): ?>
      <tr>
        <td style="font-weight:600;"><?php echo htmlspecialchars($c['name']); ?></td>
        <td><code style="font-size:0.78rem;"><?php echo htmlspecialchars($c['slug']); ?></code></td>
        <td style="color:#666;"><?php echo $c['product_count']; ?></td>
        <td><?php echo $c['is_active'] ? '<span class="badge-active">Active</span>' : '<span class="badge-inactive">Inactive</span>'; ?></td>
        <td style="text-align:right;">
          <div class="d-flex gap-2 justify-content-end">
            <a href="?edit=<?php echo $c['id']; ?>" class="adm-btn adm-btn-secondary adm-btn-sm"><i class="bi bi-pencil"></i></a>
            <a href="?delete=<?php echo $c['id']; ?>" class="adm-btn adm-btn-danger adm-btn-sm" data-confirm="Delete '<?php echo addslashes($c['name']); ?>'?"><i class="bi bi-trash"></i></a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/../layout_footer.php'; ?>
