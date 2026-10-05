<?php
$admin_page_title = 'Inquiries';
$admin_active     = 'inquiries';
require_once __DIR__ . '/../../config/db.php';

$db = getDB();

// Mark as read
if (isset($_GET['read']) && is_numeric($_GET['read'])) {
    $db->prepare("UPDATE inquiries SET is_read=1 WHERE id=:id")->execute([':id'=>$_GET['read']]);
}
// Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $db->prepare("DELETE FROM inquiries WHERE id=:id")->execute([':id'=>$_GET['delete']]);
    header('Location: index.php?deleted=1'); exit;
}
// Mark all read
if (isset($_GET['read_all'])) {
    $db->exec("UPDATE inquiries SET is_read=1 WHERE is_read=0");
    header('Location: index.php?saved=1'); exit;
}
// Bulk delete
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['bulk_delete'])) {
    $ids = array_filter(array_map('intval', $_POST['ids'] ?? []));
    if ($ids) $db->exec("DELETE FROM inquiries WHERE id IN (".implode(',',$ids).")");
    header('Location: index.php?deleted=1'); exit;
}

// Detail view
$view = null;
if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $s = $db->prepare("SELECT * FROM inquiries WHERE id=:id");
    $s->execute([':id'=>$_GET['view']]); $view = $s->fetch();
    if ($view && !$view['is_read']) {
        $db->prepare("UPDATE inquiries SET is_read=1 WHERE id=:id")->execute([':id'=>$view['id']]);
    }
}

// Filters
$type_f   = $_GET['type'] ?? '';
$read_f   = $_GET['read'] ?? '';
$search   = trim($_GET['search'] ?? '');
$page     = max(1,(int)($_GET['page']??1));
$per_page = 20; $offset = ($page-1)*$per_page;

$where = ['1=1']; $params = [];
if ($type_f)   { $where[] = 'type=:type';  $params[':type']=$type_f; }
if ($read_f!=='') { $where[] = 'is_read=:ir'; $params[':ir']=(int)$read_f; }
if ($search)   { $where[] = '(name LIKE :s OR phone LIKE :s2 OR email LIKE :s3)'; $params[':s']="%$search%"; $params[':s2']="%$search%"; $params[':s3']="%$search%"; }
$whereStr = implode(' AND ', $where);

$total = (int)$db->prepare("SELECT COUNT(*) FROM inquiries WHERE $whereStr")->execute($params) && ($t=$db->prepare("SELECT COUNT(*) FROM inquiries WHERE $whereStr")) && $t->execute($params) ? $t->fetchColumn() : 0;
$total = (function() use ($db,$whereStr,$params){ $s=$db->prepare("SELECT COUNT(*) FROM inquiries WHERE $whereStr"); $s->execute($params); return (int)$s->fetchColumn(); })();
$pages = (int)ceil($total/$per_page);

$stmt = $db->prepare("SELECT * FROM inquiries WHERE $whereStr ORDER BY created_at DESC LIMIT $per_page OFFSET $offset");
$stmt->execute($params); $inquiries = $stmt->fetchAll();

$unread = (int)$db->query("SELECT COUNT(*) FROM inquiries WHERE is_read=0")->fetchColumn();

include __DIR__ . '/../layout.php';
?>

<div class="adm-page-header">
  <div>
    <h1>Inquiries</h1>
    <div class="adm-ph-sub"><?php echo $total; ?> total <?php if($unread): ?>&middot; <span style="color:var(--lc-danger);font-weight:700;"><?php echo $unread; ?> unread</span><?php endif; ?></div>
  </div>
  <div style="display:flex;gap:8px;">
    <?php if ($unread): ?><a href="?read_all=1" class="adm-btn adm-btn-secondary" onclick="return confirm('Mark all as read?')"><i class="bi bi-check-all"></i> Mark All Read</a><?php endif; ?>
  </div>
</div>

<?php if (isset($_GET['deleted'])): ?><div class="adm-flash adm-flash-error"><i class="bi bi-trash-fill"></i> Inquiry deleted.</div><?php endif; ?>
<?php if (isset($_GET['saved'])): ?><div class="adm-flash adm-flash-success"><i class="bi bi-check-circle-fill"></i> Done.</div><?php endif; ?>

<div class="row g-4">
  <?php if ($view): ?>
  <!-- DETAIL VIEW -->
  <div class="col-lg-5">
    <div class="adm-card">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
        <h5 style="margin:0;font-size:.95rem;">Inquiry #<?php echo $view['id']; ?></h5>
        <div style="display:flex;gap:6px;">
          <a href="index.php" class="adm-btn adm-btn-secondary adm-btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
          <a href="?delete=<?php echo $view['id']; ?>" class="adm-btn adm-btn-danger adm-btn-sm"
             data-confirm="Delete this inquiry?"><i class="bi bi-trash"></i></a>
        </div>
      </div>
      <table style="width:100%;font-size:.85rem;border-collapse:collapse;">
        <?php foreach([
          'Name'     => $view['name'],
          'Phone'    => $view['phone'],
          'Email'    => $view['email'],
          'Type'     => ucfirst($view['type']),
          'Purpose'  => $view['purpose'] ?? '—',
          'Address'  => $view['address'] ?? '—',
          'Product'  => $view['product_name'] ?? '—',
          'Received' => date('d M Y, h:i A', strtotime($view['created_at'])),
        ] as $label => $val): ?>
        <tr style="border-bottom:1px solid var(--lc-border);">
          <td style="padding:9px 12px 9px 0;font-weight:700;color:var(--lc-text-lt);text-transform:uppercase;font-size:.68rem;letter-spacing:.08em;white-space:nowrap;width:100px;"><?php echo $label; ?></td>
          <td style="padding:9px 0;color:var(--lc-text-md);"><?php echo htmlspecialchars($val); ?></td>
        </tr>
        <?php endforeach; ?>
      </table>
      <?php if (!empty($view['message']) || !empty($view['description'])): ?>
      <div style="margin-top:16px;background:var(--lc-bg);border-radius:var(--lc-radius);padding:16px;">
        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--lc-text-lt);margin-bottom:8px;">Message / Requirements</div>
        <p style="margin:0;font-size:.86rem;color:var(--lc-text-md);line-height:1.7;"><?php echo nl2br(htmlspecialchars($view['message']??$view['description']??'')); ?></p>
      </div>
      <?php endif; ?>
      <?php if (!empty($view['phone'])): ?>
      <div style="display:flex;gap:8px;margin-top:16px;">
        <a href="https://wa.me/<?php echo preg_replace('/\D/','',$view['phone']); ?>" target="_blank"
           class="adm-btn adm-btn-accent" style="flex:1;justify-content:center;">
          <i class="bi bi-whatsapp"></i> WhatsApp
        </a>
        <?php if (!empty($view['email'])): ?>
        <a href="mailto:<?php echo htmlspecialchars($view['email']); ?>" class="adm-btn adm-btn-secondary" style="flex:1;justify-content:center;">
          <i class="bi bi-envelope"></i> Reply
        </a>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- LIST -->
  <div class="<?php echo $view?'col-lg-7':'col-12'; ?>">
    <!-- FILTER -->
    <div class="adm-card mb-4" style="padding:14px 18px;">
      <form method="GET" class="row g-2">
        <div class="col-md-4">
          <div class="adm-search-bar">
            <i class="bi bi-search"></i>
            <input type="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Name, phone, email…">
          </div>
        </div>
        <div class="col-md-3">
          <select name="type" class="adm-input adm-select" onchange="this.form.submit()">
            <option value="">All Types</option>
            <option value="product" <?php echo $type_f==='product'?'selected':''; ?>>Product</option>
            <option value="quote" <?php echo $type_f==='quote'?'selected':''; ?>>Quote</option>
            <option value="general" <?php echo $type_f==='general'?'selected':''; ?>>General</option>
          </select>
        </div>
        <div class="col-md-3">
          <select name="read" class="adm-input adm-select" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="0" <?php echo $read_f==='0'?'selected':''; ?>>Unread</option>
            <option value="1" <?php echo $read_f==='1'?'selected':''; ?>>Read</option>
          </select>
        </div>
        <div class="col-md-2">
          <button type="submit" class="adm-btn adm-btn-primary" style="width:100%;justify-content:center;"><i class="bi bi-funnel"></i></button>
        </div>
      </form>
    </div>

    <form method="POST">
    <div class="adm-table">
      <div style="padding:12px 20px;border-bottom:1px solid var(--lc-border);display:flex;align-items:center;gap:10px;">
        <input type="checkbox" id="inq-sel-all" onchange="document.querySelectorAll('input[name=\'ids[]\']').forEach(c=>c.checked=this.checked)">
        <label for="inq-sel-all" style="font-size:.8rem;font-weight:600;color:var(--lc-text-md);">Select All</label>
        <button type="submit" name="bulk_delete" value="1" class="adm-btn adm-btn-danger adm-btn-sm"
                onclick="return confirm('Delete selected?')"><i class="bi bi-trash"></i> Delete</button>
      </div>
      <div class="adm-table-wrap">
      <table>
        <thead><tr><th></th><th>Name</th><th>Contact</th><th>Type</th><th>Date</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($inquiries as $inq): ?>
          <tr style="<?php echo !$inq['is_read']?'background:rgba(21,112,239,.03);font-weight:600;':''; ?>">
            <td><input type="checkbox" name="ids[]" value="<?php echo $inq['id']; ?>"></td>
            <td>
              <div style="display:flex;align-items:center;gap:8px;">
                <?php if (!$inq['is_read']): ?><span style="width:7px;height:7px;background:var(--lc-info);border-radius:50%;flex-shrink:0;display:block;"></span><?php endif; ?>
                <div>
                  <div style="font-size:.86rem;"><?php echo htmlspecialchars($inq['name']); ?></div>
                  <?php if (!empty($inq['product_name'])): ?><div style="font-size:.72rem;color:var(--lc-text-lt);"><?php echo htmlspecialchars($inq['product_name']); ?></div><?php endif; ?>
                </div>
              </div>
            </td>
            <td style="font-size:.8rem;">
              <div><?php echo htmlspecialchars($inq['phone']); ?></div>
              <div style="color:var(--lc-text-lt);"><?php echo htmlspecialchars($inq['email']??''); ?></div>
            </td>
            <td><span class="badge-pending"><?php echo ucfirst($inq['type']); ?></span></td>
            <td style="font-size:.76rem;color:var(--lc-text-lt);white-space:nowrap;"><?php echo date('d M, h:ia', strtotime($inq['created_at'])); ?></td>
            <td>
              <div style="display:flex;gap:4px;">
                <a href="?view=<?php echo $inq['id']; ?>" class="adm-btn adm-btn-secondary adm-btn-sm adm-btn-icon" title="View"><i class="bi bi-eye"></i></a>
                <a href="?delete=<?php echo $inq['id']; ?>" class="adm-btn adm-btn-danger adm-btn-sm adm-btn-icon"
                   data-confirm="Delete this inquiry?" title="Delete"><i class="bi bi-trash"></i></a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($inquiries)): ?>
          <tr><td colspan="6"><div class="adm-empty"><i class="bi bi-chat-dots"></i><h4>No inquiries found</h4></div></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
      </div>
    </div>
    </form>

    <?php if ($pages > 1): ?>
    <div class="adm-pagination" style="justify-content:center;">
      <?php if ($page>1): ?><a class="adm-page-btn" href="?page=<?php echo $page-1; ?>&type=<?php echo $type_f; ?>&search=<?php echo urlencode($search); ?>"><i class="bi bi-chevron-left"></i></a><?php endif; ?>
      <?php for($i=max(1,$page-2);$i<=min($pages,$page+2);$i++): ?><a class="adm-page-btn <?php echo $i===$page?'active':''; ?>" href="?page=<?php echo $i; ?>&type=<?php echo $type_f; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a><?php endfor; ?>
      <?php if ($page<$pages): ?><a class="adm-page-btn" href="?page=<?php echo $page+1; ?>&type=<?php echo $type_f; ?>&search=<?php echo urlencode($search); ?>"><i class="bi bi-chevron-right"></i></a><?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/../layout_footer.php'; ?>
