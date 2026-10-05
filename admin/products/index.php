<?php
$admin_page_title = 'Products';
$admin_active     = 'products';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../functions/products.php';
require_once __DIR__ . '/../../functions/uploads.php';

$db = getDB();

// Bulk delete
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['bulk_delete'])) {
    $ids = array_filter(array_map('intval', $_POST['ids'] ?? []));
    if ($ids) {
        $in = implode(',', $ids);
        $imgs = $db->query("SELECT image FROM product_images WHERE product_id IN ($in)")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($imgs as $img) deleteUpload($img);
        $db->exec("DELETE FROM product_images WHERE product_id IN ($in)");
        $db->exec("DELETE FROM product_features WHERE product_id IN ($in)");
        $db->exec("DELETE FROM products WHERE id IN ($in)");
    }
    header('Location: index.php?bulk_deleted=1'); exit;
}

// Single delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $pid = (int)$_GET['delete'];
    $imgs = $db->prepare("SELECT image FROM product_images WHERE product_id=:id");
    $imgs->execute([':id'=>$pid]);
    foreach ($imgs->fetchAll(PDO::FETCH_COLUMN) as $img) deleteUpload($img);
    $db->prepare("DELETE FROM product_images   WHERE product_id=:id")->execute([':id'=>$pid]);
    $db->prepare("DELETE FROM product_features WHERE product_id=:id")->execute([':id'=>$pid]);
    $db->prepare("DELETE FROM products         WHERE id=:id")        ->execute([':id'=>$pid]);
    header('Location: index.php?deleted=1'); exit;
}

// Toggle status
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $db->prepare("UPDATE products SET status = IF(status='active','inactive','active') WHERE id=:id")
       ->execute([':id'=>$_GET['toggle']]);
    header('Location: index.php?saved=1'); exit;
}

// Filters
$search     = trim($_GET['search'] ?? '');
$cat_filter = $_GET['cat'] ?? '';
$status_f   = $_GET['status'] ?? '';
$page       = max(1,(int)($_GET['page'] ?? 1));
$per_page   = 20;
$offset     = ($page - 1) * $per_page;

$where  = ['1=1']; $params = [];
if ($search) { $where[] = '(p.name LIKE :s OR p.product_code LIKE :s2)'; $params[':s']="%$search%"; $params[':s2']="%$search%"; }
if ($cat_filter) { $where[] = 'c.slug=:cat'; $params[':cat']=$cat_filter; }
if ($status_f) { $where[] = 'p.status=:status'; $params[':status']=$status_f; }

$whereStr = implode(' AND ',$where);
$total = $db->prepare("SELECT COUNT(*) FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE $whereStr");
$total->execute($params); $total = (int)$total->fetchColumn();
$pages = (int)ceil($total / $per_page);

$stmt = $db->prepare("SELECT p.*, c.name AS cat_name,
       (SELECT image FROM product_images WHERE product_id=p.id AND is_primary=1 LIMIT 1) AS thumb
       FROM products p LEFT JOIN categories c ON c.id=p.category_id
       WHERE $whereStr ORDER BY p.id DESC LIMIT $per_page OFFSET $offset");
$stmt->execute($params);
$products = $stmt->fetchAll();

$cats = $db->query("SELECT id,name,slug FROM categories ORDER BY name")->fetchAll();

include __DIR__ . '/../layout.php';
?>

<div class="adm-page-header">
  <div>
    <h1>Products</h1>
    <div class="adm-ph-sub"><?php echo $total; ?> product<?php echo $total!==1?'s':''; ?> found</div>
  </div>
  <a href="add.php" class="adm-btn adm-btn-primary"><i class="bi bi-plus-circle"></i> Add Product</a>
</div>

<?php foreach (['deleted'=>'Product deleted.','saved'=>'Status updated.','bulk_deleted'=>'Products deleted.','added'=>'Product added successfully.','updated'=>'Product updated successfully.'] as $k=>$v): ?>
<?php if (isset($_GET[$k])): ?><div class="adm-flash adm-flash-<?php echo $k==='deleted'||$k==='bulk_deleted'?'error':'success'; ?>"><i class="bi bi-<?php echo strpos($k,'delete')!==false?'trash-fill':'check-circle-fill'; ?>"></i><?php echo $v; ?></div><?php endif; ?>
<?php endforeach; ?>

<!-- FILTER BAR -->
<div class="adm-card mb-4" style="padding:16px 20px;">
  <form method="GET" class="row g-2 align-items-end">
    <div class="col-md-4">
      <div class="adm-search-bar">
        <i class="bi bi-search"></i>
        <input type="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search name or code…" data-autosearch>
      </div>
    </div>
    <div class="col-md-3">
      <select name="cat" class="adm-input adm-select" onchange="this.form.submit()">
        <option value="">All Categories</option>
        <?php foreach ($cats as $c): ?>
        <option value="<?php echo $c['slug']; ?>" <?php echo $cat_filter===$c['slug']?'selected':''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2">
      <select name="status" class="adm-input adm-select" onchange="this.form.submit()">
        <option value="">All Status</option>
        <option value="active" <?php echo $status_f==='active'?'selected':''; ?>>Active</option>
        <option value="inactive" <?php echo $status_f==='inactive'?'selected':''; ?>>Inactive</option>
      </select>
    </div>
    <div class="col-md-2">
      <button type="submit" class="adm-btn adm-btn-primary" style="width:100%;justify-content:center;"><i class="bi bi-funnel"></i> Filter</button>
    </div>
    <?php if ($search||$cat_filter||$status_f): ?>
    <div class="col-md-1">
      <a href="index.php" class="adm-btn adm-btn-secondary" style="width:100%;justify-content:center;" title="Clear"><i class="bi bi-x-circle"></i></a>
    </div>
    <?php endif; ?>
  </form>
</div>

<!-- TABLE -->
<form method="POST" id="bulk-form">
<div class="adm-table">
  <div style="padding:12px 20px;border-bottom:1px solid var(--lc-border);display:flex;align-items:center;gap:10px;">
    <input type="checkbox" id="select-all" onchange="document.querySelectorAll('input[name=\'ids[]\']').forEach(c=>c.checked=this.checked)">
    <label for="select-all" style="font-size:.8rem;font-weight:600;color:var(--lc-text-md);">Select All</label>
    <button type="submit" name="bulk_delete" value="1" class="adm-btn adm-btn-danger adm-btn-sm"
            onclick="return confirm('Delete all selected products? This cannot be undone.')">
      <i class="bi bi-trash"></i> Delete Selected
    </button>
  </div>
  <div class="adm-table-wrap">
  <table>
    <thead>
      <tr>
        <th style="width:32px;"></th>
        <th>Product</th>
        <th>Code</th>
        <th>Category</th>
        <th>Featured</th>
        <th>Status</th>
        <th style="text-align:right;">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $p): ?>
      <tr>
        <td><input type="checkbox" name="ids[]" value="<?php echo $p['id']; ?>"></td>
        <td>
          <div style="display:flex;align-items:center;gap:10px;">
            <?php if (!empty($p['thumb'])): ?>
            <img src="<?php echo htmlspecialchars(getImageUrl($p['thumb'])); ?>" class="adm-img-preview" style="width:44px;height:44px;" alt="">
            <?php else: ?>
            <div style="width:44px;height:44px;background:var(--lc-bg);border-radius:8px;border:1px solid var(--lc-border);display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="bi bi-image" style="color:#ccc;font-size:.9rem;"></i></div>
            <?php endif; ?>
            <div>
              <div style="font-weight:600;font-size:.86rem;line-height:1.3;"><?php echo htmlspecialchars($p['name']); ?></div>
              <div style="font-size:.72rem;color:var(--lc-text-lt);margin-top:2px;"><?php echo htmlspecialchars(substr($p['description']??'',0,60)); ?></div>
            </div>
          </div>
        </td>
        <td><code style="font-size:.76rem;background:#F3F4F6;padding:2px 7px;border-radius:4px;color:var(--lc-text-md);"><?php echo htmlspecialchars($p['product_code']); ?></code></td>
        <td style="font-size:.8rem;color:var(--lc-text-lt);"><?php echo htmlspecialchars($p['cat_name']??'—'); ?></td>
        <td>
          <?php if ($p['is_featured']): ?><span class="badge-active">Featured</span>
          <?php elseif ($p['is_new']): ?><span class="badge-info">New</span>
          <?php elseif ($p['is_bestseller']): ?><span class="badge-primary">Bestseller</span>
          <?php else: ?><span style="color:var(--lc-text-lt);font-size:.78rem;">—</span><?php endif; ?>
        </td>
        <td>
          <a href="?toggle=<?php echo $p['id']; ?>" class="adm-toggle <?php echo $p['status']==='active'?'on':''; ?>"
             title="Toggle Status" style="text-decoration:none;" onclick="return confirm('Toggle status?')"></a>
        </td>
        <td>
          <div style="display:flex;gap:5px;justify-content:flex-end;">
            <a href="<?php echo BASE_URL; ?>/product.php?slug=<?php echo urlencode($p['slug']); ?>" target="_blank"
               class="adm-btn adm-btn-secondary adm-btn-sm adm-btn-icon" title="View on Site"><i class="bi bi-box-arrow-up-right"></i></a>
            <a href="edit.php?id=<?php echo $p['id']; ?>" class="adm-btn adm-btn-secondary adm-btn-sm adm-btn-icon" title="Edit"><i class="bi bi-pencil"></i></a>
            <a href="?delete=<?php echo $p['id']; ?>" class="adm-btn adm-btn-danger adm-btn-sm adm-btn-icon"
               data-confirm="Delete '<?php echo addslashes($p['name']); ?>'? This cannot be undone." title="Delete"><i class="bi bi-trash"></i></a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($products)): ?>
      <tr><td colspan="7">
        <div class="adm-empty"><i class="bi bi-box-seam"></i><h4>No products found</h4>
          <p><?php echo $search ? 'Try different search terms.' : 'Add your first product.'; ?></p>
          <a href="add.php" class="adm-btn adm-btn-primary" style="margin-top:12px;"><i class="bi bi-plus-circle"></i> Add Product</a>
        </div>
      </td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  </div>
</div>
</form>

<!-- PAGINATION -->
<?php if ($pages > 1): ?>
<div class="adm-pagination" style="justify-content:center;padding-top:20px;">
  <?php if ($page>1): ?><a class="adm-page-btn" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&cat=<?php echo $cat_filter; ?>&status=<?php echo $status_f; ?>"><i class="bi bi-chevron-left"></i></a><?php endif; ?>
  <?php for($i=max(1,$page-2);$i<=min($pages,$page+2);$i++): ?>
  <a class="adm-page-btn <?php echo $i===$page?'active':''; ?>" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&cat=<?php echo $cat_filter; ?>&status=<?php echo $status_f; ?>"><?php echo $i; ?></a>
  <?php endfor; ?>
  <?php if ($page<$pages): ?><a class="adm-page-btn" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&cat=<?php echo $cat_filter; ?>&status=<?php echo $status_f; ?>"><i class="bi bi-chevron-right"></i></a><?php endif; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../layout_footer.php'; ?>
