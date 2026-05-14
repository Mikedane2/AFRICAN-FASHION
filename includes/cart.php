<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../config/config.php';
header('Content-Type: application/json');

$sessionId = getSessionId();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch($action) {
    case 'add':
        $pid = intval($_POST['product_id']);
        $qty = intval($_POST['quantity'] ?? 1);
        $size = $_POST['size'] ?? null;
        $color = $_POST['color'] ?? null;
        
        $product = getProductById($pdo, $pid);
        if(!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }
        if($product['stock_quantity'] < $qty) {
            echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
            exit;
        }
        
        $success = addToCart($pdo, $pid, $qty, $sessionId, $size, $color);
        $count = getCartCount($pdo);
        echo json_encode(['success' => $success, 'cart_count' => $count]);
        break;
        
    case 'update':
        $cid = intval($_POST['cart_id']);
        $qty = intval($_POST['quantity']);
        if($qty < 1) $qty = 1;
        $success = updateCartQuantity($pdo, $cid, $qty);
        echo json_encode(['success' => $success]);
        break;
        
    case 'remove':
        $cid = intval($_POST['cart_id']);
        $success = updateCartQuantity($pdo, $cid, 0);
        echo json_encode(['success' => $success]);
        break;
        
    case 'count':
        echo json_encode(['count' => getCartCount($pdo)]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>