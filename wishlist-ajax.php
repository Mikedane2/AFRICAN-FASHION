<?php
require_once 'config/config.php';
header('Content-Type: application/json');

$sessionId = getSessionId();
$productId = intval($_POST['product_id'] ?? 0);

if($productId) {
    if(isInWishlist($pdo, $sessionId, $productId)) {
        removeFromWishlist($pdo, $sessionId, $productId);
        $added = false;
    } else {
        addToWishlist($pdo, $sessionId, $productId);
        $added = true;
    }
    $count = count(getWishlist($pdo, $sessionId));
    echo json_encode(['added' => $added, 'count' => $count]);
} else {
    echo json_encode(['added' => false, 'count' => 0]);
}
?>