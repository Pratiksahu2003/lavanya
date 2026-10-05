<?php
$admin_page_title = 'Reviews';
$admin_active     = 'reviews';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../functions/uploads.php';

$db = getDB();

$edit_review = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM reviews WHERE id = :id");
    $stmt->execute([':id' => (int)$_GET['edit']]);
    $edit_review = $stmt->fetch();
}

if (isset($_GET['approve'])) {
    $db->prepare("UPDATE reviews SET status='approved' WHERE id=:id")->execute([':id'=>$_GET['approve']]);
    header('Location: reviews.php?saved=1'); exit;
}
if (isset($_GET['reject'])) {
    $db->prepare("UPDATE reviews SET status='rejected' WHERE id=:id")->execute([':id'=>$_GET['reject']]);
    header('Location: reviews.php?saved=1'); exit;
}
if (isset($_GET['delete'])) {
    $r = $db->prepare("SELECT image FROM reviews WHERE id=:id"); $r->execute([':id'=>$_GET['delete']]);
    $rev = $r->fetch(); if ($rev && !empty($rev['image'])) deleteUpload($rev['image']);
    $db->prepare("DELETE FROM reviews WHERE id=:id")->execute([':id'=>$_GET['delete']]);
    header('Location: reviews.php?deleted=1'); exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $review_id = (int)($_POST['review_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $company_name = trim($_POST['company_name'] ?? '');
    $designation = trim($_POST['designation'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
    $review = trim($_POST['review'] ?? '');
    $status = in_array($_POST['status'] ?? 'pending', ['pending','approved','rejected'], true) ? $_POST['status'] : 'pending';
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $image = null;

    if (!$name) $errors[] = 'Customer name is required.';
    if (!$review) $errors[] = 'Review text is required.';

    if ($review_id) {
        $row = $db->prepare("SELECT image FROM reviews WHERE id = :id");
        $row->execute([':id' => $review_id]);
        $image = ($row->fetch())['image'] ?? null;
    }

    if (!empty($_FILES['image']['name'])) {
        $res = uploadImage($_FILES['image'], 'clients');
        if ($res['success']) {
            if ($image) deleteUpload($image);
            $image = $res['filename'];
        } else {
            $errors[] = $res['error'];
        }
    }

    if (empty($errors)) {
        if ($review_id) {
            $db->prepare("UPDATE reviews SET name=:name, company_name=:company_name, designation=:designation, city=:city, rating=:rating, review=:review, image=:image, status=:status, sort_order=:sort_order WHERE id=:id")
                ->execute([':name'=>$name,':company_name'=>$company_name,':designation'=>$designation,':city'=>$city,':rating'=>$rating,':review'=>$review,':image'=>$image,':status'=>$status,':sort_order'=>$sort_order,':id'=>$review_id]);
        } else {
            $db->prepare("INSERT INTO reviews (name, company_name, designation, city, rating, review, image, status, sort_order) VALUES (:name,:company_name,:designation,:city,:rating,:review,:image,:status,:sort_order)")
                ->execute([':name'=>$name,':company_name'=>$company_name,':designation'=>$designation,':city'=>$city,':rating'=>$rating,':review'=>$review,':image'=>$image,':status'=>$status,':sort_order'=>$sort_order]);
        }
        header('Location: reviews.php?saved=1'); exit;
    }
}

$status = in_array(($_GET['status'] ?? ''), ['pending', 'approved', 'rejected'], true) ? $_GET['status'] : '';
$where  = $status ? "WHERE r.status = " . $db->quote($status) : '';
$reviews = $db->query(
    "SELECT r.*, p.name AS product_name FROM reviews r
     LEFT JOIN products p ON p.id = r.product_id
     $where ORDER BY r.sort_order ASC, r.created_at DESC LIMIT 100"
)->fetchAll();

include __DIR__ . '/../layout.php';
?>

<div class="adm-page-header">
  <h1>Customer Reviews</h1>
  <div class="d-flex gap-2">
    <?php foreach ([''=>'All','pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected'] as $k=>$l): ?>
    <a href="?status=<?php echo $k; ?>" class="adm-btn <?php echo $status===$k?'adm-btn-primary':'adm-btn-secondary'; ?> adm-btn-sm"><?php echo $l; ?></a>
    <?php endforeach; ?>
  </div>
</div>

<?php if (isset($_GET['saved'])): ?><div class="adm-flash" style="background:rgba(25,180,80,0.1);color:#12a04a;border-radius:9px;padding:11px 16px;margin-bottom:16px;">Review updated.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><div class="adm-flash" style="background:rgba(200,0,0,0.1);color:#c00;border-radius:9px;padding:11px 16px;margin-bottom:16px;">Review deleted.</div><?php endif; ?>

<div class="adm-card mb-4">
  <h5 style="font-weight:700;margin-bottom:16px;"><?php echo $edit_review ? 'Edit Review' : 'Add Review'; ?></h5>
  <?php foreach ($errors as $e): ?><div style="color:#c00;font-size:0.85rem;margin-bottom:8px;"><i class="bi bi-exclamation-circle me-1"></i><?php echo htmlspecialchars($e); ?></div><?php endforeach; ?>
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="review_id" value="<?php echo (int)($edit_review['id'] ?? 0); ?>">
    <div class="row g-3">
      <div class="col-md-4"><label class="adm-label">Customer Name</label><input type="text" name="name" class="adm-input" required value="<?php echo htmlspecialchars($edit_review['name'] ?? ''); ?>"></div>
      <div class="col-md-4"><label class="adm-label">Company Name</label><input type="text" name="company_name" class="adm-input" value="<?php echo htmlspecialchars($edit_review['company_name'] ?? ''); ?>"></div>
      <div class="col-md-4"><label class="adm-label">Designation</label><input type="text" name="designation" class="adm-input" value="<?php echo htmlspecialchars($edit_review['designation'] ?? ''); ?>"></div>
      <div class="col-md-4"><label class="adm-label">City</label><input type="text" name="city" class="adm-input" value="<?php echo htmlspecialchars($edit_review['city'] ?? ''); ?>"></div>
      <div class="col-md-2"><label class="adm-label">Rating</label><select name="rating" class="adm-input"><option value="1" <?php echo (($edit_review['rating'] ?? 5) == 1 ? 'selected' : ''); ?>>1</option><option value="2" <?php echo (($edit_review['rating'] ?? 5) == 2 ? 'selected' : ''); ?>>2</option><option value="3" <?php echo (($edit_review['rating'] ?? 5) == 3 ? 'selected' : ''); ?>>3</option><option value="4" <?php echo (($edit_review['rating'] ?? 5) == 4 ? 'selected' : ''); ?>>4</option><option value="5" <?php echo (($edit_review['rating'] ?? 5) == 5 ? 'selected' : ''); ?>>5</option></select></div>
      <div class="col-md-2"><label class="adm-label">Display Order</label><input type="number" name="sort_order" class="adm-input" value="<?php echo (int)($edit_review['sort_order'] ?? 0); ?>"></div>
      <div class="col-md-4"><label class="adm-label">Status</label><select name="status" class="adm-input"><option value="pending" <?php echo (($edit_review['status'] ?? 'pending') === 'pending' ? 'selected' : ''); ?>>Pending</option><option value="approved" <?php echo (($edit_review['status'] ?? 'pending') === 'approved' ? 'selected' : ''); ?>>Approved</option><option value="rejected" <?php echo (($edit_review['status'] ?? 'pending') === 'rejected' ? 'selected' : ''); ?>>Rejected</option></select></div>
      <div class="col-12"><label class="adm-label">Review</label><textarea name="review" class="adm-input" rows="4" required><?php echo htmlspecialchars($edit_review['review'] ?? ''); ?></textarea></div>
      <div class="col-md-6"><label class="adm-label">Customer Image</label><?php if (!empty($edit_review['image'])): ?><img src="<?php echo htmlspecialchars(getImageUrl($edit_review['image'])); ?>" class="adm-img-preview" style="margin-bottom:8px;width:80px;height:80px;object-fit:cover;" alt=""><?php endif; ?><input type="file" name="image" class="adm-input" accept="image/jpeg,image/png,image/webp"></div>
      <div class="col-12 d-flex gap-2"><button type="submit" class="adm-btn adm-btn-primary"><i class="bi bi-check-circle"></i> Save Review</button><?php if ($edit_review): ?><a href="reviews.php" class="adm-btn adm-btn-secondary">Cancel</a><?php endif; ?></div>
    </div>
  </form>
</div>

<div class="adm-table">
  <table>
    <thead><tr><th>Reviewer</th><th>Product</th><th>Rating</th><th>Review</th><th>Status</th><th>Date</th><th style="text-align:right;">Actions</th></tr></thead>
    <tbody>
      <?php foreach ($reviews as $r): ?>
      <tr>
        <td style="font-weight:600;"><?php echo htmlspecialchars($r['name']); ?><br><span style="font-size:0.78rem;color:#888;font-weight:400;"><?php echo htmlspecialchars($r['company_name'] ?: ($r['city'] ?: '')); ?></span></td>
        <td style="font-size:0.82rem;color:#555;"><?php echo htmlspecialchars($r['product_name'] ?? '—'); ?></td>
        <td style="color:#f4b942;"><?php echo str_repeat('★', (int)$r['rating']); ?></td>
        <td style="font-size:0.82rem;max-width:220px;"><?php echo htmlspecialchars(substr($r['review'], 0, 100)) . (strlen($r['review'])>100?'…':''); ?></td>
        <td><?php
          echo match($r['status']) {
            'approved' => '<span class="badge-active">Approved</span>',
            'rejected' => '<span class="badge-inactive">Rejected</span>',
            default    => '<span class="badge-pending">Pending</span>',
          };
        ?></td>
        <td style="font-size:0.78rem;color:#888;"><?php echo date('d M Y', strtotime($r['created_at'])); ?></td>
        <td style="text-align:right;">
          <div class="d-flex gap-2 justify-content-end">
            <a href="?edit=<?php echo $r['id']; ?>" class="adm-btn adm-btn-secondary adm-btn-sm"><i class="bi bi-pencil"></i></a>
            <?php if ($r['status'] !== 'approved'): ?><a href="?approve=<?php echo $r['id']; ?>" class="adm-btn adm-btn-sm" style="background:#12a04a;color:#fff;"><i class="bi bi-check-circle"></i></a><?php endif; ?>
            <?php if ($r['status'] !== 'rejected'): ?><a href="?reject=<?php echo $r['id']; ?>" class="adm-btn adm-btn-secondary adm-btn-sm"><i class="bi bi-x-circle"></i></a><?php endif; ?>
            <a href="?delete=<?php echo $r['id']; ?>" class="adm-btn adm-btn-danger adm-btn-sm" data-confirm="Delete this review?"><i class="bi bi-trash"></i></a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($reviews)): ?><tr><td colspan="7" style="text-align:center;padding:40px;color:#888;">No reviews found.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/../layout_footer.php'; ?>
