<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../config/config.php';
if(!isAdmin()) { header('Location: login.php'); exit; }

if(isset($_POST['update_stock'])) {
    $stmt = $pdo->prepare("UPDATE products SET stock_quantity=? WHERE id=?");
    $stmt->execute([$_POST['stock_quantity'], $_POST['product_id']]);
    header('Location: inventory.php?msg=updated');
    exit;
}

$products = $pdo->query("SELECT * FROM products ORDER BY stock_quantity ASC")->fetchAll();
$lowStock = array_filter($products, function($p) { return $p['stock_quantity'] < 10 && $p['stock_quantity'] > 0; });
$outOfStock = array_filter($products, function($p) { return $p['stock_quantity'] == 0; });
$inStock = array_filter($products, function($p) { return $p['stock_quantity'] >= 10; });
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - AfriMart Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-sidebar { background: #1a1a2e; min-height: 100vh; color: white; position: sticky; top: 0; }
        .admin-sidebar .logo { padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .admin-sidebar .logo h3 { color: #FF9900; margin: 0; }
        .admin-sidebar .nav-link { color: #ddd; padding: 12px 20px; display: flex; align-items: center; }
        .admin-sidebar .nav-link:hover, .admin-sidebar .nav-link.active { background: #FF9900; color: #111; }
        .admin-sidebar .nav-link i { width: 25px; margin-right: 10px; }
        .stat-card { background: white; border-radius: 12px; padding: 20px; margin-bottom: 20px; text-align: center; }
        .data-table { background: white; border-radius: 12px; overflow: hidden; }
        .data-table th { background: #232F3E; color: white; padding: 12px; }
        .data-table td { padding: 12px; vertical-align: middle; }
        .stock-low { background-color: #fff3cd; }
        .stock-out { background-color: #f8d7da; }
        @media (max-width: 768px) { .admin-sidebar { min-height: auto; position: relative; } }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 p-0 admin-sidebar">
            <div class="logo text-center"><h3><i class="fas fa-store"></i> AfriMart</h3><p>Admin Panel</p></div>
            <nav class="nav flex-column">
                <a class="nav-link" href="index.php"><i class="fas fa-chart-line"></i> Dashboard</a>
                <a class="nav-link" href="products.php"><i class="fas fa-box"></i> Products</a>
                <a class="nav-link" href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a class="nav-link active" href="inventory.php"><i class="fas fa-warehouse"></i> Inventory</a>
                <a class="nav-link" href="ads.php"><i class="fas fa-bullhorn"></i> Ads & Offers</a>
                <a class="nav-link" href="profile.php"><i class="fas fa-user-cog"></i> Profile</a>
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </div>
        <div class="col-md-10 p-4">
            <h2><i class="fas fa-warehouse text-primary"></i> Inventory Management</h2>
            
            <div class="row mb-4">
                <div class="col-md-4"><div class="stat-card"><h3 class="text-success"><?php echo count($inStock); ?></h3><p>In Stock (≥10)</p></div></div>
                <div class="col-md-4"><div class="stat-card"><h3 class="text-warning"><?php echo count($lowStock); ?></h3><p>Low Stock (<10)</p></div></div>
                <div class="col-md-4"><div class="stat-card"><h3 class="text-danger"><?php echo count($outOfStock); ?></h3><p>Out of Stock</p></div></div>
            </div>
            
            <?php if(isset($_GET['msg'])): ?><div class="alert alert-success">Stock updated successfully!</div><?php endif; ?>
            
            <?php if(count($lowStock) > 0): ?>
            <div class="alert alert-warning"><h5><i class="fas fa-exclamation-triangle"></i> Low Stock Alert!</h5><?php foreach($lowStock as $p): ?><div><strong><?php echo htmlspecialchars($p['name']); ?></strong> - Only <?php echo $p['stock_quantity']; ?> units remaining</div><?php endforeach; ?></div>
            <?php endif; ?>
            
            <div class="data-table">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>Product</th><th>Current Stock</th><th>Units Sold</th><th>Status</th><th>Update Stock</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $p): 
                            $rowClass = $p['stock_quantity']==0?'stock-out':($p['stock_quantity']<10?'stock-low':'');
                            $stockText = $p['stock_quantity']==0?'OUT OF STOCK':($p['stock_quantity']<10?'LOW STOCK':'IN STOCK');
                            $stockBadge = $p['stock_quantity']==0?'danger':($p['stock_quantity']<10?'warning':'success');
                        ?>
                        <tr class="<?php echo $rowClass; ?>">
                            <td><?php echo htmlspecialchars($p['name']); ?></div>
                            <td><strong><?php echo $p['stock_quantity']; ?></strong> units</div>
                            <td><?php echo $p['sold_count']; ?> units</div>
                            <td><span class="badge bg-<?php echo $stockBadge; ?>"><?php echo $stockText; ?></span></div>
                            <td><form method="POST" class="d-flex"><input type="hidden" name="product_id" value="<?php echo $p['id']; ?>"><input type="number" name="stock_quantity" value="<?php echo $p['stock_quantity']; ?>" class="form-control me-2" style="width:100px" min="0"><button type="submit" name="update_stock" class="btn btn-primary btn-sm">Update</button></form></div>
                         </tr>
                        <?php endforeach; ?>
                    </tbody>
                 </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>