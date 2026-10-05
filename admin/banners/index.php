<?php
$admin_page_title = 'Homepage Banners';
$admin_active     = 'banners';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../functions/uploads.php';

$db = getDB();

if (isset($_GET['delete'])) {
    $r = $db->prepare("SELECT image FROM homepage_banners WHERE id=:id"); $r->execute([':id'=>$_GET['delete']]);
    $b = $r->fetch(); if ($b && !empty($b['image'])) deleteUpload($b['image']);
    $db->prepare("DELETE FROM homepage_banners WHERE id=:id")->execute([':id'=>$_GET['delete']]);
    header('Location: index.php?deleted=1'); exit;
}

if (isset($_GET['toggle'])) {
    $db->prepare("UPDATE homepage_banners SET is_active = NOT is_active WHERE id=:id")->execute([':id'=>$_GET['toggle']]);
    header('Location: index.php?saved=1'); exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $s = $db->prepare("SELECT * FROM homepage_banners WHERE id=:id"); $s->execute([':id'=>$_GET['edit']]);
    $edit = $s->fetch();
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $edit_id  = (int)($_POST['edit_id'] ?? 0);
    $title    = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $label    = trim($_POST['label'] ?? '');
    $btn1t    = trim($_POST['btn1_text'] ?? '');
    $btn1u    = trim($_POST['btn1_url'] ?? '');
    $btn2t    = trim($_POST['btn2_text'] ?? '');
    $btn2u    = trim($_POST['btn2_url'] ?? '');
    $sort     = (int)($_POST['sort_order'] ?? 0);
    $active   = isset($_POST['is_active']) ? 1 : 0;

    if (!$title) $errors[] = 'Title is required.';

    $image = null;
    if ($edit_id) {
        $r = $db->prepare("SELECT image FROM homepage_banners WHERE id=:id"); $r->execute([':id'=>$edit_id]);
        $image = ($r->fetch())['image'] ?? null;
    }

    // Handle image URL or upload
    $image_url = trim($_POST['image_url'] ?? '');
    if ($image_url) {
        $image = $image_url;
    } elseif (!empty($_FILES['image']['name'])) {
        $res = uploadImage($_FILES['image'], 'banners');
        if ($res['success']) $image = $res['filename'];
        else $errors[] = $res['error'];
    }

    if (empty($errors)) {
        if ($edit_id) {
            $db->prepare("UPDATE homepage_banners SET title=:t, subtitle=:s, label=:l, image=:img, btn1_text=:b1t, btn1_url=:b1u, btn2_text=:b2t, btn2_url=:b2u, sort_order=:so, is_active=:a WHERE id=:id")
               ->execute([':t'=>$title,':s'=>$subtitle,':l'=>$label,':img'=>$image,':b1t'=>$btn1t,':b1u'=>$btn1u,':b2t'=>$btn2t,':b2u'=>$btn2u,':so'=>$sort,':a'=>$active,':id'=>$edit_id]);
        } else {
            $db->prepare("INSERT INTO homepage_banners (title,subtitle,label,image,btn1_text,btn1_url,btn2_text,btn2_url,sort_order,is_active) VALUES (:t,:s,:l,:img,:b1t,:b1u,:b2t,:b2u,:so,:a)")
               ->execute([':t'=>$title,':s'=>$subtitle,':l'=>$label,':img'=>$image,':b1t'=>$btn1t,':b1u'=>$btn1u,':b2t'=>$btn2t,':b2u'=>$btn2u,':so'=>$sort,':a'=>$active]);
        }
        header('Location: index.php?saved=1'); exit;
    }
}

$banners = $db->query("SELECT * FROM homepage_banners ORDER BY sort_order ASC, id ASC")->fetchAll();
include __DIR__ . '/../layout.php';
?>

<div class="adm-page-header">
  <h1>Homepage Banners</h1>
  <a href="?add=1" class="adm-btn adm-btn-primary"><i class="bi bi-plus-circle"></i> Add Banner</a>
</div>

<?php if (isset($_GET['saved'])): ?><div class="adm-flash" style="background:rgba(25,180,80,0.1);color:#12a04a;border-radius:9px;padding:11px 16px;margin-bottom:16px;">Saved successfully.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><div class="adm-flash" style="background:rgba(200,0,0,0.1);color:#c00;border-radius:9px;padding:11px 16px;margin-bottom:16px;">Deleted.</div><?php endif; ?>

<!-- Add/Edit Form -->
<?php if (isset($_GET['add']) || $edit): ?>
<div class="adm-card mb-4">
  <h5 style="font-weight:700;margin-bottom:20px;"><?php echo $edit ? 'Edit Banner' : 'New Banner'; ?></h5>
  <?php foreach ($errors as $e): ?><div style="color:#c00;font-size:0.85rem;margin-bottom:8px;"><?php echo htmlspecialchars($e); ?></div><?php endforeach; ?>
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="edit_id" value="<?php echo $edit['id'] ?? 0; ?>">
    <div class="row g-3">
      <div class="col-md-8">
        <label class="adm-label">Hero Title *</label>
        <input type="text" name="title" class="adm-input" required value="<?php echo htmlspecialchars($edit['title'] ?? ''); ?>" placeholder="Premium Spaces Begin Here">
      </div>
      <div class="col-md-4">
        <label class="adm-label">Label Tag</label>
        <input type="text" name="label" class="adm-input" value="<?php echo htmlspecialchars($edit['label'] ?? ''); ?>" placeholder="Commercial & Residential">
      </div>
      <div class="col-12">
        <label class="adm-label">Subtitle</label>
        <textarea name="subtitle" class="adm-input" rows="2"><?php echo htmlspecialchars($edit['subtitle'] ?? ''); ?></textarea>
      </div>
      <div class="col-md-3"><label class="adm-label">Button 1 Text</label><input type="text" name="btn1_text" class="adm-input" value="<?php echo htmlspecialchars($edit['btn1_text'] ?? 'Explore Collections'); ?>"></div>
      <div class="col-md-3"><label class="adm-label">Button 1 URL</label><input type="text" name="btn1_url" class="adm-input" value="<?php echo htmlspecialchars($edit['btn1_url'] ?? 'category.php?cat=all'); ?>"></div>
      <div class="col-md-3"><label class="adm-label">Button 2 Text</label><input type="text" name="btn2_text" class="adm-input" value="<?php echo htmlspecialchars($edit['btn2_text'] ?? 'Get Free Quote'); ?>"></div>
      <div class="col-md-3"><label class="adm-label">Button 2 URL</label><input type="text" name="btn2_url" class="adm-input" value="<?php echo htmlspecialchars($edit['btn2_url'] ?? 'contact.php'); ?>"></div>
      <div class="col-md-6">
        <label class="adm-label">Image URL (paste Unsplash or CDN link)</label>
        <input type="text" name="image_url" class="adm-input" value="<?php echo htmlspecialchars($edit['image'] ?? ''); ?>" placeholder="https://...">
      </div>
      <div class="col-md-3"><label class="adm-label">Upload Image</label><input type="file" name="image" accept="image/*" class="adm-input" style="padding:7px;"></div>
      <div class="col-md-1"><label class="adm-label">Order</label><input type="number" name="sort_order" class="adm-input" value="<?php echo $edit['sort_order'] ?? 0; ?>"></div>
      <div class="col-md-2" style="display:flex;align-items:end;">
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:0.88rem;">
          <input type="checkbox" name="is_active" value="1" <?php echo ($edit['is_active'] ?? 1) ? 'checked' : ''; ?> style="accent-color:var(--brown);"> Active
        </label>
      </div>
      <div class="col-12 d-flex gap-2">
        <button type="submit" class="adm-btn adm-btn-primary"><i class="bi bi-check-circle"></i> Save Banner</button>
        <a href="index.php" class="adm-btn adm-btn-secondary">Cancel</a>
      </div>
    </div>
  </form>
</div>
<?php endif; ?>

<!-- Banner List -->
<div class="row g-3">
  <?php foreach ($banners as $b): ?>
  <div class="col-lg-6">
    <div class="adm-card" style="display:flex;gap:16px;align-items:flex-start;">
      <?php if (!empty($b['image'])): ?>
      <img src="<?php echo htmlspecialchars(getImageUrl($b['image'])); ?>" style="width:100px;height:64px;object-fit:cover;border-radius:8px;flex-shrink:0;" alt="">
      <?php endif; ?>
      <div style="flex:1;min-width:0;">
        <div style="font-weight:700;font-size:0.9rem;margin-bottom:4px;"><?php echo htmlspecialchars($b['title']); ?></div>
        <div style="font-size:0.78rem;color:#888;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo htmlspecialchars($b['subtitle'] ?? ''); ?></div>
        <div style="display:flex;gap:8px;margin-top:10px;align-items:center;">
          <?php echo $b['is_active'] ? '<span class="badge-active">Active</span>' : '<span class="badge-inactive">Inactive</span>'; ?>
          <a href="?edit=<?php echo $b['id']; ?>" class="adm-btn adm-btn-secondary adm-btn-sm"><i class="bi bi-pencil"></i></a>
          <a href="?toggle=<?php echo $b['id']; ?>" class="adm-btn adm-btn-secondary adm-btn-sm"><?php echo $b['is_active'] ? 'Deactivate' : 'Activate'; ?></a>
          <a href="?delete=<?php echo $b['id']; ?>" class="adm-btn adm-btn-danger adm-btn-sm" data-confirm="Delete this banner?"><i class="bi bi-trash"></i></a>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <?php if (empty($banners)): ?><div class="col-12"><div style="text-align:center;padding:40px;color:#888;">No banners yet. Add one above.</div></div><?php endif; ?>
</div>

<?php include __DIR__ . '/../layout_footer.php'; ?>
