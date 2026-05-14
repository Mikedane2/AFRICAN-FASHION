<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config/config.php';
header('Content-Type: application/json');
$sessionId = getSessionId();
$count = count(getWishlist($pdo, $sessionId));
echo json_encode(['count' => $count]);
?>