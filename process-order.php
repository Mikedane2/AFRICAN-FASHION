<?php
require_once 'config/config.php';
header('Content-Type: application/json');

$sessionId = getSessionId();
$items = getCartItems($pdo, $sessionId);

if(empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

$data = [
    'name' => $_POST['name'] ?? '',
    'email' => $_POST['email'] ?? '',
    'phone' => $_POST['phone'] ?? '',
    'address' => $_POST['address'] ?? '',
    'city' => $_POST['city'] ?? '',
    'country' => $_POST['country'] ?? 'Kenya'
];

if(empty($data['name']) || empty($data['email']) || empty($data['phone']) || empty($data['address'])) {
    echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
    exit;
}

$paymentMethod = $_POST['payment_method'] ?? 'paypal';

try {
    $orderNumber = createOrder($pdo, $items, $data, $paymentMethod, $currentCurrency);
    clearCart($pdo, $sessionId);
    echo json_encode(['success' => true, 'order_number' => $orderNumber]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Order failed: ' . $e->getMessage()]);
}
?>