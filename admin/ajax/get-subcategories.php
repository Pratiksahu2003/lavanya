<?php
/**
 * AJAX endpoint — returns subcategories for a given category id
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
if (!isAdminLoggedIn()) { echo json_encode([]); exit; }
$cat_id = (int)($_GET['cat_id'] ?? $_GET['cat'] ?? 0);
if (!$cat_id) { echo json_encode([]); exit; }
$db   = getDB();
$stmt = $db->prepare("SELECT id, name FROM subcategories WHERE category_id=:cid ORDER BY sort_order ASC, name ASC");
$stmt->execute([':cid'=>$cat_id]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
