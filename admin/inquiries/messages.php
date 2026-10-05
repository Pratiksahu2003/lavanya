<?php
$admin_page_title = 'Contact Messages';
$admin_active     = 'messages';
require_once __DIR__ . '/../../config/db.php';

$db = getDB();

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $db->prepare("DELETE FROM contact_messages WHERE id=:id")->execute([':id'=>$_GET['delete']]);
    header('Location: messages.php?deleted=1'); exit;
}
if (isset($_GET['read_all'])) {
    $db->exec("UPDATE contact_messages SET is_read=1 WHERE is_read=0");
    header('Location: messages.php?saved=1'); exit;
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['bulk_delete'])) {
    $ids = array_filter(array_map('intval', $_POST['ids'] ?? []));
    if ($ids) $db->exec("DELETE FROM contact_messages WHERE id IN (".implode(',',$ids).")");
    header('Location: messages.php?deleted=1'); exit;
}

$view = null;
if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $s = $db->prepare("SELECT * FROM contact_messages WHERE id=:id");
    $s->execute([':id'=>$_GET['view']]); $view = $s->fetch();
    if ($view && !$view['is_read']) {
        $db->prepare("UPDATE contact_messages SET is_read=1 WHERE id=:id")->execute([':id'=>$view['id']]);
    }
}

$page = max(1,(int)($_GET['page']??1)); $per_page=20; $offset=($page-1)*$per_page;
$total = (int)$db->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
$pages = (int)ceil($total/$per_page);
$messages = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT $per_page OFFSET $offset")->fetchAll();
$unread = (int)$db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetchColumn();

include __DIR__ . '/../layout.php';
?>

<div class="adm-page-header">
  <div><h1>Contact Messages</h1><div class="adm-ph-sub"><?php echo $total; ?> total <?php if($unread): ?>&middot; <span style="color:var(--lc-danger);font-weight:700;"><?php echo $unread; ?> unread</span><?php endif; ?></div></div>
  <?php if($unread): ?><a href="?read_all=1" class="adm-btn adm-btn-secondary" onclick="return confirm('Mark all as read?')"><i class="bi bi-check-all"></i> Mark All Read</a><?php endif; ?>
</div>

<?php if (isset($_GET['deleted'])): ?><div class="adm-flash adm-flash-error"><i class="bi bi-trash-fill"></i> Message deleted.</div><?php endif; ?>

<div class="row g-4">
  <?php if ($view): ?>
  <div class="col-lg-5">
    <div class="adm-card">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
        <h5 style="margin:0;font-size:.95rem;">Message #<?php echo $view['id']; ?></h5>
        <div style="display:flex;gap:6px;">
          <a href="messages.php" class="adm-btn adm-btn-secondary adm-btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
          <a href="?delete=<?php echo $view['id']; ?>" class="adm-btn adm-btn-danger adm-btn-sm" data-confirm="Delete this message?"><i class="bi bi-trash"></i></a>
        </div>
      </div>
      <table style="width:100%;font-size:.85rem;border-collapse:collapse;">
        <?php foreach(['Name'=>$view['name'],'Email'=>$view['email'],'Phone'=>$view['phone']??'—','Subject'=>$view['subject']??'—','Received'=>date('d M Y, h:i A',strtotime($view['created_at']))] as $l=>$v): ?>
        <tr style="border-bottom:1px solid var(--lc-border);">
          <td style="padding:9px 12px 9px 0;font-weight:700;color:var(--lc-text-lt);text-transform:uppercase;font-size:.68em;letter-spacing:.08em;width:90px;"><?php echo $l; ?></td>
          <td style="padding:9px 0;color:var(--lc-text-md);"><?php echo htmlspecialchars($v); ?></td>
        </tr>
        <?php endforeach; ?>
      </table>
      <div style="margin-top:16px;background:var(--lc-bg);border-radius:var(--lc-radius);padding:16px;">
        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--lc-text-lt);margin-bottom:8px;">Message</div>
        <p style="margin:0;font-size:.86rem;color:var(--lc-text-md);line-height:1.7;"><?php echo nl2br(htmlspecialchars($view['message']??'')); ?></p>
      </div>
      <?php if ($view['email']): ?>
      <a href="mailto:<?php echo htmlspecialchars($view['email']); ?>" class="adm-btn adm-btn-primary" style="width:100%;justify-content:center;margin-top:14px;">
        <i class="bi bi-envelope"></i> Reply by Email
      </a>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>

  <div class="<?php echo $view?'col-lg-7':'col-12'; ?>">
    <form method="POST">
    <div class="adm-table">
      <div style="padding:12px 20px;border-bottom:1px solid var(--lc-border);display:flex;gap:10px;align-items:center;">
        <input type="checkbox" id="msg-sel-all" onchange="document.querySelectorAll('input[name=\'ids[]\']').forEach(c=>c.checked=this.checked)">
        <label for="msg-sel-all" style="font-size:.8rem;font-weight:600;color:var(--lc-text-md);">Select All</label>
        <button type="submit" name="bulk_delete" value="1" class="adm-btn adm-btn-danger adm-btn-sm" onclick="return confirm('Delete selected?')"><i class="bi bi-trash"></i> Delete</button>
      </div>
      <div class="adm-table-wrap">
      <table>
        <thead><tr><th></th><th>From</th><th>Subject</th><th>Date</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($messages as $m): ?>
          <tr style="<?php echo !$m['is_read']?'font-weight:600;background:rgba(21,112,239,.03);':''; ?>">
            <td><input type="checkbox" name="ids[]" value="<?php echo $m['id']; ?>"></td>
            <td>
              <div style="display:flex;align-items:center;gap:7px;">
                <?php if (!$m['is_read']): ?><span style="width:7px;height:7px;background:var(--lc-info);border-radius:50%;flex-shrink:0;"></span><?php endif; ?>
                <div>
                  <div style="font-size:.86rem;"><?php echo htmlspecialchars($m['name']); ?></div>
                  <div style="font-size:.74rem;color:var(--lc-text-lt);"><?php echo htmlspecialchars($m['email']??''); ?></div>
                </div>
              </div>
            </td>
            <td style="font-size:.82rem;color:var(--lc-text-md);"><?php echo htmlspecialchars($m['subject']??'—'); ?></td>
            <td style="font-size:.76rem;color:var(--lc-text-lt);white-space:nowrap;"><?php echo date('d M, h:ia',strtotime($m['created_at'])); ?></td>
            <td>
              <div style="display:flex;gap:4px;">
                <a href="?view=<?php echo $m['id']; ?>" class="adm-btn adm-btn-secondary adm-btn-sm adm-btn-icon" title="View"><i class="bi bi-eye"></i></a>
                <a href="?delete=<?php echo $m['id']; ?>" class="adm-btn adm-btn-danger adm-btn-sm adm-btn-icon" data-confirm="Delete this message?" title="Delete"><i class="bi bi-trash"></i></a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($messages)): ?>
          <tr><td colspan="5"><div class="adm-empty"><i class="bi bi-envelope"></i><h4>No messages yet</h4></div></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
      </div>
    </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../layout_footer.php'; ?>
