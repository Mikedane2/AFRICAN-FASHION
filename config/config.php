<?php
// Fix session start - check if session already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'african_fashion_db');
define('SITE_URL', 'http://localhost/africanfashion/');
define('SITE_NAME', 'AfriMart - African Marketplace');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/currency.php';

function getCartCount($pdo) {
    $sessionId = getSessionId();
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(quantity), 0) as total FROM cart_sessions WHERE session_id = ?");
    $stmt->execute([$sessionId]);
    $result = $stmt->fetch();
    return $result['total'];
}

$cartCount = getCartCount($pdo);
?>