<?php
/**
 * NOVAHOMZ Admin — Add / Edit Product
 */
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../functions/products.php';
require_once __DIR__ . '/../../functions/categories.php';
require_once __DIR__ . '/../../functions/uploads.php';

$db       = getDB();
$id       = (int)($_GET['id'] ?? 0);
$is_edit  = $id > 0;
$product  = null;
$features = [];
$images   = [];
$errors   = [];
$success  = false;

if ($is_edit) {
    $stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch();
    if (!$product) { header('Location: index.php'); exit; }
    $features = getProductFeatures($id);
    $images   = getProductImages($id);
}

$admin_page_title = $is_edit ? 'Edit Product' : 'Add Product';
$admin_active     = 'products';

$categories    = getAllCategories(false);
$subcategories = [];
if ($is_edit && !empty($product['category_id'])) {
    $subcategories = getSubcategoriesByCategory((int)$product['category_id']);
}
$collections   = getAllCollections(false);

// ─── Handle Form Submit ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $code    = strtoupper(trim($_POST['product_code'] ?? ''));
    $cat_id  = (int)($_POST['category_id'] ?? 0) ?: null;
    $sub_id  = (int)($_POST['subcategory_id'] ?? 0) ?: null;
    $col_id  = (int)($_POST['collection_id'] ?? 0) ?: null;

    if (!$name)  $errors[] = 'Product name is required.';
    if (!$code)  $errors[] = 'Product code is required.';

    if ($sub_id) {
        $sub_chk = $db->prepare("SELECT id FROM subcategories WHERE id = :sub_id AND category_id = :category_id");
        $sub_chk->execute([':sub_id' => $sub_id, ':category_id' => $cat_id]);
        if (!$sub_chk->fetch()) $errors[] = 'Selected subcategory does not belong to the selected category.';
    }

    // Check code uniqueness within the selected category/subcategory.
    if ($code) {
        $chk = $db->prepare(
            "SELECT id FROM products
             WHERE product_code = :code
               AND category_id <=> :category_id
               AND subcategory_id <=> :subcategory_id
               AND id != :id"
        );
        $chk->execute([
            ':code' => $code,
            ':category_id' => $cat_id,
            ':subcategory_id' => $sub_id,
            ':id' => $id,
        ]);
        if ($chk->fetch()) $errors[] = "Product code '$code' is already in use for this category/subcategory.";
    }

    if (empty($errors)) {
        $slug = getUniqueSlug($name, $id);
        $data = [
            ':name'           => $name,
            ':slug'           => $slug,
            ':product_code'   => $code,
            ':category_id'    => $cat_id,
            ':subcategory_id' => $sub_id,
            ':collection_id'  => $col_id,
            ':description'    => trim($_POST['description'] ?? ''),
            ':short_desc'     => trim($_POST['short_desc'] ?? ''),
            ':material'       => trim($_POST['material'] ?? ''),
            ':dimensions'     => trim($_POST['dimensions'] ?? ''),
            ':colors'         => trim($_POST['colors'] ?? ''),
            ':is_featured'    => isset($_POST['is_featured']) ? 1 : 0,
            ':is_new'         => isset($_POST['is_new']) ? 1 : 0,
            ':is_bestseller'  => isset($_POST['is_bestseller']) ? 1 : 0,
            ':status'         => $_POST['status'] ?? 'active',
            ':meta_title'     => trim($_POST['meta_title'] ?? ''),
            ':meta_desc'      => trim($_POST['meta_desc'] ?? ''),
        ];

        if ($is_edit) {
            $sql = "UPDATE products SET
                name=:name, slug=:slug, product_code=:product_code,
                category_id=:category_id, subcategory_id=:subcategory_id, collection_id=:collection_id,
                description=:description, short_desc=:short_desc, material=:material,
                dimensions=:dimensions, colors=:colors, is_featured=:is_featured,
                is_new=:is_new, is_bestseller=:is_bestseller, status=:status,
                meta_title=:meta_title, meta_desc=:meta_desc
                WHERE id=:id";
            $data[':id'] = $id;
        } else {
            $sql = "INSERT INTO products
                (name, slug, product_code, category_id, subcategory_id, collection_id,
                 description, short_desc, material, dimensions, colors, is_featured,
                 is_new, is_bestseller, status, meta_title, meta_desc)
                VALUES
                (:name, :slug, :product_code, :category_id, :subcategory_id, :collection_id,
                 :description, :short_desc, :material, :dimensions, :colors, :is_featured,
                 :is_new, :is_bestseller, :status, :meta_title, :meta_desc)";
        }

        $db->prepare($sql)->execute($data);
        $product_id = $is_edit ? $id : (int)$db->lastInsertId();

        // Features
        $db->prepare("DELETE FROM product_features WHERE product_id = :id")->execute([':id' => $product_id]);
        $feat_lines = array_filter(array_map('trim', explode("\n", $_POST['features'] ?? '')));
        foreach ($feat_lines as $i => $line) {
            $db->prepare("INSERT INTO product_features (product_id, feature, sort_order) VALUES (:pid, :f, :s)")
               ->execute([':pid' => $product_id, ':f' => $line, ':s' => $i]);
        }

        // Image uploads
        if (!empty($_FILES['images']['name'][0])) {
            $primary_set = !empty($images); // if already has images, don't overwrite primary
            foreach ($_FILES['images']['name'] as $i => $fname) {
                if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;
                $file = [
                    'name'     => $fname,
                    'tmp_name' => $_FILES['images']['tmp_name'][$i],
                    'size'     => $_FILES['images']['size'][$i],
                    'error'    => $_FILES['images']['error'][$i],
                ];
                $result = uploadImage($file, 'products');
                if ($result['success']) {
                    $isPrimary = (!$primary_set && $i === 0) ? 1 : 0;
                    $db->prepare("INSERT INTO product_images (product_id, image, is_primary, sort_order) VALUES (:pid, :img, :primary, :sort)")
                       ->execute([':pid' => $product_id, ':img' => $result['filename'], ':primary' => $isPrimary, ':sort' => $i]);
                    $primary_set = true;
                }
            }
        }

        // Handle primary image change
        if (isset($_POST['set_primary'])) {
            $img_id = (int)$_POST['set_primary'];
            $db->prepare("UPDATE product_images SET is_primary = 0 WHERE product_id = :pid")->execute([':pid' => $product_id]);
            $db->prepare("UPDATE product_images SET is_primary = 1 WHERE id = :id AND product_id = :pid")->execute([':id' => $img_id, ':pid' => $product_id]);
        }

        // Handle image delete
        if (isset($_POST['delete_image'])) {
            $img_id = (int)$_POST['delete_image'];
            $row = $db->prepare("SELECT * FROM product_images WHERE id = :id AND product_id = :pid");
            $row->execute([':id' => $img_id, ':pid' => $product_id]);
            $img_row = $row->fetch();
            if ($img_row) {
                deleteUpload($img_row['image']);
                $db->prepare("DELETE FROM product_images WHERE id = :id")->execute([':id' => $img_id]);
            }
        }

        header('Location: index.php?saved=1');
        exit;
    }
}

include __DIR__ . '/../layout.php';
?>

<div class="adm-page-header">
  <h1><?php echo $is_edit ? 'Edit Product' : 'Add New Product'; ?></h1>
  <a href="index.php" class="adm-btn adm-btn-secondary"><i class="bi bi-arrow-left"></i> Back to Products</a>
</div>

<?php if ($errors): ?>
<div style="background:rgba(200,0,0,0.08);color:#c00;border:1px solid rgba(200,0,0,0.2);border-radius:10px;padding:14px 18px;margin-bottom:20px;">
  <?php foreach ($errors as $e): ?><div><i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($e); ?></div><?php endforeach; ?>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
<div class="row g-4">

  <!-- Left Column -->
  <div class="col-lg-8">

    <!-- Basic Info -->
    <div class="adm-card mb-4">
      <h5 style="font-weight:700;margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid #f0f0f0;">Product Information</h5>
      <div class="mb-3">
        <label class="adm-label">Product Name *</label>
        <input type="text" name="name" class="adm-input" required value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" placeholder="e.g. Luxury Sofa Set">
      </div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="adm-label">Product Code *</label>
          <input type="text" name="product_code" class="adm-input" required value="<?php echo htmlspecialchars($product['product_code'] ?? ''); ?>" placeholder="e.g. SF001">
        </div>
        <div class="col-md-6">
          <label class="adm-label">Status</label>
          <select name="status" class="adm-input">
            <option value="active"        <?php echo ($product['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
            <option value="inactive"      <?php echo ($product['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
            <option value="out_of_stock"  <?php echo ($product['status'] ?? '') === 'out_of_stock' ? 'selected' : ''; ?>>Out of Stock</option>
          </select>
        </div>
      </div>
      <div class="mb-3">
        <label class="adm-label">Short Description</label>
        <input type="text" name="short_desc" class="adm-input" value="<?php echo htmlspecialchars($product['short_desc'] ?? ''); ?>" placeholder="One-line summary shown in product cards">
      </div>
      <div class="mb-3">
        <label class="adm-label">Full Description</label>
        <textarea name="description" class="adm-input" rows="5" placeholder="Detailed product description..."><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
      </div>
      <div class="row g-3">
        <div class="col-md-4">
          <label class="adm-label">Material</label>
          <input type="text" name="material" class="adm-input" value="<?php echo htmlspecialchars($product['material'] ?? ''); ?>" placeholder="e.g. Solid wood, PU Leather">
        </div>
        <div class="col-md-4">
          <label class="adm-label">Dimensions</label>
          <input type="text" name="dimensions" class="adm-input" value="<?php echo htmlspecialchars($product['dimensions'] ?? ''); ?>" placeholder="L x W x H cm">
        </div>
        <div class="col-md-4">
          <label class="adm-label">Colors Available</label>
          <input type="text" name="colors" class="adm-input" value="<?php echo htmlspecialchars($product['colors'] ?? ''); ?>" placeholder="e.g. Brown, Cream, Grey">
        </div>
      </div>
    </div>

    <!-- Features -->
    <div class="adm-card mb-4">
      <h5 style="font-weight:700;margin-bottom:6px;">Key Features</h5>
      <p style="color:#888;font-size:0.82rem;margin-bottom:14px;">One feature per line. These appear as bullet points on the product page.</p>
      <textarea name="features" class="adm-input" rows="6" placeholder="Premium fabric upholstery&#10;Solid hardwood frame&#10;High-density foam cushions&#10;Available in custom colors"><?php echo htmlspecialchars(implode("\n", $features)); ?></textarea>
    </div>

    <!-- SEO -->
    <div class="adm-card mb-4">
      <h5 style="font-weight:700;margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid #f0f0f0;">SEO Settings</h5>
      <div class="mb-3">
        <label class="adm-label">Meta Title</label>
        <input type="text" name="meta_title" class="adm-input" value="<?php echo htmlspecialchars($product['meta_title'] ?? ''); ?>" placeholder="Leave blank to auto-generate">
      </div>
      <div>
        <label class="adm-label">Meta Description</label>
        <textarea name="meta_desc" class="adm-input" rows="2" placeholder="Leave blank to use short description"><?php echo htmlspecialchars($product['meta_desc'] ?? ''); ?></textarea>
      </div>
    </div>

    <!-- Images -->
    <div class="adm-card mb-4">
      <h5 style="font-weight:700;margin-bottom:6px;">Product Images</h5>
      <p style="color:#888;font-size:0.82rem;margin-bottom:16px;">Upload multiple images. JPG, PNG or WEBP. Max 5MB each.</p>

      <?php if (!empty($images)): ?>
      <div class="d-flex flex-wrap gap-3 mb-3">
        <?php foreach ($images as $img): ?>
        <div style="position:relative;display:inline-block;">
          <img src="<?php echo htmlspecialchars(getImageUrl($img['image'] ?? '')); ?>" style="width:90px;height:90px;object-fit:cover;border-radius:10px;border:<?php echo $img['is_primary'] ? '2px solid var(--brown)' : '2px solid #e0e0e0'; ?>;" alt="">
          <?php if ($img['is_primary']): ?>
          <span style="position:absolute;top:4px;left:4px;background:var(--brown);color:#fff;font-size:0.6rem;padding:2px 5px;border-radius:4px;font-weight:700;">PRIMARY</span>
          <?php endif; ?>
          <div style="position:absolute;bottom:4px;right:4px;display:flex;gap:3px;">
            <?php if (!$img['is_primary']): ?>
            <button type="submit" name="set_primary" value="<?php echo $img['id']; ?>" title="Set as primary"
                    style="background:var(--brown);color:#fff;border:none;border-radius:4px;width:22px;height:22px;font-size:0.65rem;cursor:pointer;display:flex;align-items:center;justify-content:center;">★</button>
            <?php endif; ?>
            <button type="submit" name="delete_image" value="<?php echo $img['id']; ?>"
                    onclick="return confirm('Delete this image?')"
                    style="background:#c00;color:#fff;border:none;border-radius:4px;width:22px;height:22px;font-size:0.65rem;cursor:pointer;display:flex;align-items:center;justify-content:center;">✕</button>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="adm-input" style="padding:8px;">
    </div>
  </div>

  <!-- Right Column -->
  <div class="col-lg-4">

    <!-- Organisation -->
    <div class="adm-card mb-4">
      <h5 style="font-weight:700;margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid #f0f0f0;">Organisation</h5>
      <div class="mb-3">
        <label class="adm-label">Category</label>
        <select name="category_id" class="adm-input" id="cat-select">
          <option value="">— No Category —</option>
          <?php foreach ($categories as $c): ?>
          <option value="<?php echo $c['id']; ?>" <?php echo ($product['category_id'] ?? 0) == $c['id'] ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($c['name']); ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="adm-label">Subcategory</label>
        <select name="subcategory_id" class="adm-input" id="sub-select">
          <option value="">— No Subcategory —</option>
          <?php foreach ($subcategories as $s): ?>
          <option value="<?php echo $s['id']; ?>" <?php echo ($product['subcategory_id'] ?? 0) == $s['id'] ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($s['name']); ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="adm-label">Collection</label>
        <select name="collection_id" class="adm-input">
          <option value="">— No Collection —</option>
          <?php foreach ($collections as $col): ?>
          <option value="<?php echo $col['id']; ?>" <?php echo ($product['collection_id'] ?? 0) == $col['id'] ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($col['name']); ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <!-- Flags -->
    <div class="adm-card mb-4">
      <h5 style="font-weight:700;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid #f0f0f0;">Product Flags</h5>
      <?php foreach (['is_featured' => '★ Featured Product', 'is_new' => '🆕 New Arrival', 'is_bestseller' => '🔥 Best Seller'] as $key => $label): ?>
      <div style="display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid #f8f8f8;">
        <input type="checkbox" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="1"
               <?php echo !empty($product[$key]) ? 'checked' : ''; ?>
               style="width:16px;height:16px;accent-color:var(--brown);">
        <label for="<?php echo $key; ?>" style="margin:0;font-size:0.88rem;font-weight:600;cursor:pointer;"><?php echo $label; ?></label>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Submit -->
    <div class="adm-card">
      <button type="submit" class="adm-btn adm-btn-primary" style="width:100%;justify-content:center;padding:12px;">
        <i class="bi bi-check-circle"></i> <?php echo $is_edit ? 'Update Product' : 'Save Product'; ?>
      </button>
      <?php if ($is_edit): ?>
      <a href="<?php echo BASE_URL; ?>/product.php?slug=<?php echo urlencode($product['slug'] ?? ''); ?>" target="_blank"
         class="adm-btn adm-btn-secondary" style="width:100%;justify-content:center;margin-top:10px;">
        <i class="bi bi-eye"></i> View on Website
      </a>
      <?php endif; ?>
    </div>
  </div>
</div>
</form>

<script>
(function () {
  const catSelect = document.getElementById('cat-select');
  const subSelect = document.getElementById('sub-select');
  const currentSubId = '<?php echo isset($product['subcategory_id']) ? (int)$product['subcategory_id'] : 0; ?>';

  function loadSubcategories(catId, selectedId) {
    subSelect.innerHTML = '<option value="">Loading...</option>';
    if (!catId) {
      subSelect.innerHTML = '<option value="">— No Subcategory —</option>';
      return;
    }
    fetch('<?php echo BASE_URL; ?>/admin/ajax/get-subcategories.php?cat_id=' + catId)
      .then(function (r) { return r.json(); })
      .then(function (data) {
        subSelect.innerHTML = '<option value="">— No Subcategory —</option>';
        data.forEach(function (s) {
          const selected = String(s.id) === String(selectedId) ? 'selected' : '';
          subSelect.innerHTML += '<option value="' + s.id + '" ' + selected + '>' + s.name + '</option>';
        });
      });
  }

  if (catSelect) {
    catSelect.addEventListener('change', function () {
      loadSubcategories(this.value, 0);
    });
    if (catSelect.value) {
      loadSubcategories(catSelect.value, currentSubId);
    }
  }
})();
</script>

<?php include __DIR__ . '/../layout_footer.php'; ?>
