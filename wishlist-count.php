<?php
require_once 'config/config.php';
header('Content-Type: application/json');
$sessionId = getSessionId();
$count = count(getWishlist($pdo, $sessionId));
echo json_encode(['count' => $count]);
?>