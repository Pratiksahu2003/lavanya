<?php
/**
 * NOVAHOMZ — Cart AJAX Action Handler
 * Handles add/remove/count via POST
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/functions/cart.php';
require_once __DIR__ . '/functions/products.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

switch ($action) {

    case 'add':
        $product_id   = (int)($_POST['product_id'] ?? 0);
        $product_code = trim($_POST['product_code'] ?? '');
        $product_name = trim($_POST['product_name'] ?? '');
        $product_img  = trim($_POST['product_image'] ?? '');

        if (!$product_code) {
            echo json_encode(['success' => false, 'error' => 'Invalid product']);
            exit;
        }

        cartAdd([
            'id'           => $product_id,
            'product_code' => $product_code,
            'name'         => $product_name,
            'primary_image'=> $product_img,
        ]);
        echo json_encode(['success' => true, 'count' => cartCount()]);
        break;

    case 'remove':
        $code = trim($_POST['product_code'] ?? '');
        cartRemove($code);
        echo json_encode(['success' => true, 'count' => cartCount()]);
        break;

    case 'count':
        echo json_encode(['count' => cartCount()]);
        break;

    case 'clear':
        cartClear();
        echo json_encode(['success' => true, 'count' => 0]);
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Unknown action']);
}
