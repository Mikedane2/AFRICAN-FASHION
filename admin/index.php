<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../config/config.php';
if(!isAdmin()) { header('Location: login.php'); exit; }

$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status='pending'")->fetchColumn();
$lowStock = $pdo->query("SELECT COUNT(*) FROM products WHERE stock_quantity<10 AND stock_quantity>0")->fetchColumn();
$outOfStock = $pdo->query("SELECT COUNT(*) FROM products WHERE stock_quantity=0")->fetchColumn();
$totalSales = $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE payment_status='completed'")->fetchColumn();
$recentOrders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AfriMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; }
        .admin-sidebar { background: #1a1a2e; min-height: 100vh; color: white; position: sticky; top: 0; }
        .admin-sidebar .logo { padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .admin-sidebar .logo h3 { color: #FF9900; margin: 0; font-weight: 700; }
        .admin-sidebar .nav-link { color: #ddd; padding: 12px 20px; transition: 0.3s; display: flex; align-items: center; }
        .admin-sidebar .nav-link:hover, .admin-sidebar .nav-link.active { background: #FF9900; color: #111; }
        .admin-sidebar .nav-link i { width: 25px; margin-right: 10px; }
        .stat-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); transition: 0.3s; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .stat-card h3 { font-size: 32px; font-weight: 700; margin: 10px 0 0; color: #FF9900; }
        @media (max-width: 768px) { .admin-sidebar { min-height: auto; position: relative; } }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 p-0 admin-sidebar">
            <div class="logo text-center"><h3><i class="fas fa-store"></i> AfriMart</h3><p>Admin Panel</p></div>
            <nav class="nav flex-column">
                <a class="nav-link active" href="index.php"><i class="fas fa-chart-line"></i> Dashboard</a>
                <a class="nav-link" href="products.php"><i class="fas fa-box"></i> Products</a>
                <a class="nav-link" href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a class="nav-link" href="inventory.php"><i class="fas fa-warehouse"></i> Inventory</a>
                <a class="nav-link" href="ads.php"><i class="fas fa-bullhorn"></i> Ads & Offers</a>
                <a class="nav-link" href="profile.php"><i class="fas fa-user-cog"></i> Profile</a>
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </div>
        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4"><h2><i class="fas fa-chart-line text-primary"></i> Dashboard</h2><div class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['admin_user']); ?></div></div>
            <div class="row mb-4">
                <div class="col-md-3"><div class="stat-card"><div class="d-flex justify-content-between"><div><p>Total Products</p><h3><?php echo $totalProducts; ?></h3></div><i class="fas fa-box fa-2x opacity-50"></i></div></div></div>
                <div class="col-md-3"><div class="stat-card"><div class="d-flex justify-content-between"><div><p>Total Orders</p><h3><?php echo $totalOrders; ?></h3></div><i class="fas fa-shopping-cart fa-2x opacity-50"></i></div></div></div>
                <div class="col-md-3"><div class="stat-card"><div class="d-flex justify-content-between"><div><p>Pending Orders</p><h3><?php echo $pendingOrders; ?></h3></div><i class="fas fa-clock fa-2x opacity-50"></i></div></div></div>
                <div class="col-md-3"><div class="stat-card"><div class="d-flex justify-content-between"><div><p>Total Revenue</p><h3>KES <?php echo number_format($totalSales, 0); ?></h3></div><i class="fas fa-chart-line fa-2x opacity-50"></i></div></div></div>
            </div>
            <div class="row">
                <div class="col-md-6"><div class="stat-card"><h5><i class="fas fa-exclamation-triangle text-warning"></i> Stock Alerts</h5><hr><p>Low Stock (&lt;10): <?php echo $lowStock; ?> products</p><p>Out of Stock: <?php echo $outOfStock; ?> products</p><a href="inventory.php" class="btn btn-warning btn-sm">Manage Inventory</a></div></div>
                <div class="col-md-6"><div class="stat-card"><h5><i class="fas fa-clock text-primary"></i> Recent Orders</h5><hr><?php foreach($recentOrders as $order): ?><div class="d-flex justify-content-between mb-2 pb-2 border-bottom"><div><strong><?php echo $order['order_number']; ?></strong><br><small><?php echo $order['customer_name']; ?></small></div><div class="text-end"><span class="badge bg-secondary"><?php echo $order['payment_status']; ?></span><br><small><?php echo date('M d, H:i', strtotime($order['created_at'])); ?></small></div></div><?php endforeach; ?><a href="orders.php" class="btn btn-primary btn-sm mt-2">View All Orders</a></div></div>
            </div>
        </div>
    </div>
</div>
</body>
</html>