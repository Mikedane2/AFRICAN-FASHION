<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../config/config.php';
if(!isAdmin()) { header('Location: login.php'); exit; }

if(isset($_POST['update_order'])) {
    $stmt = $pdo->prepare("UPDATE orders SET order_status=?, payment_status=? WHERE id=?");
    $stmt->execute([$_POST['order_status'], $_POST['payment_status'], $_POST['order_id']]);
    header('Location: orders.php?msg=updated');
    exit;
}

if(isset($_POST['clear_payment'])) {
    $stmt = $pdo->prepare("UPDATE orders SET payment_status='completed', order_status='processing' WHERE id=?");
    $stmt->execute([$_POST['order_id']]);
    header('Location: orders.php?msg=cleared');
    exit;
}

$orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();
$pendingCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status='pending'")->fetchColumn();
$processingCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status='processing'")->fetchColumn();
$deliveredCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status='delivered'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - AfriMart Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-sidebar { background: #1a1a2e; min-height: 100vh; color: white; position: sticky; top: 0; }
        .admin-sidebar .logo { padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .admin-sidebar .logo h3 { color: #FF9900; margin: 0; }
        .admin-sidebar .nav-link { color: #ddd; padding: 12px 20px; display: flex; align-items: center; }
        .admin-sidebar .nav-link:hover, .admin-sidebar .nav-link.active { background: #FF9900; color: #111; }
        .admin-sidebar .nav-link i { width: 25px; margin-right: 10px; }
        .stat-card { background: white; border-radius: 12px; padding: 20px; margin-bottom: 20px; }
        .data-table { background: white; border-radius: 12px; overflow: hidden; }
        .data-table th { background: #232F3E; color: white; padding: 12px; }
        .data-table td { padding: 12px; vertical-align: middle; }
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
                <a class="nav-link active" href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a class="nav-link" href="inventory.php"><i class="fas fa-warehouse"></i> Inventory</a>
                <a class="nav-link" href="ads.php"><i class="fas fa-bullhorn"></i> Ads & Offers</a>
                <a class="nav-link" href="profile.php"><i class="fas fa-user-cog"></i> Profile</a>
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </div>
        <div class="col-md-10 p-4">
            <h2><i class="fas fa-shopping-cart text-primary"></i> Order Management</h2>
            
            <div class="row mb-4">
                <div class="col-md-4"><div class="stat-card"><h3><?php echo $pendingCount; ?></h3><p class="text-muted">Pending Orders</p></div></div>
                <div class="col-md-4"><div class="stat-card"><h3><?php echo $processingCount; ?></h3><p class="text-muted">Processing</p></div></div>
                <div class="col-md-4"><div class="stat-card"><h3><?php echo $deliveredCount; ?></h3><p class="text-muted">Delivered</p></div></div>
            </div>
            
            <?php if(isset($_GET['msg'])): ?><div class="alert alert-success">Order updated successfully!</div><?php endif; ?>
            
            <div class="data-table">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>Order #</th><th>Customer</th><th>Amount</th><th>Payment</th><th>Status</th><th>Date</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $o): ?>
                        <tr>
                            <td><strong><?php echo $o['order_number']; ?></strong></td>
                            <td><?php echo $o['customer_name']; ?><br><small><?php echo $o['customer_email']; ?></small><br><small><?php echo $o['customer_phone']; ?></small></td>
                            <td><?php echo $o['total_amount']; ?> <?php echo $o['currency_code']; ?></td>
                            <td><span class="badge bg-<?php echo $o['payment_status']=='completed'?'success':'warning'; ?>"><?php echo $o['payment_status']; ?></span></td>
                            <td><span class="badge bg-<?php echo $o['order_status']=='delivered'?'success':($o['order_status']=='cancelled'?'danger':'secondary'); ?>"><?php echo $o['order_status']; ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal<?php echo $o['id']; ?>"><i class="fas fa-edit"></i> Update</button>
                                <?php if($o['payment_status']=='pending'): ?>
                                <form method="POST" style="display:inline-block" onsubmit="return confirm('Clear payment for this order?')">
                                    <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                    <button type="submit" name="clear_payment" class="btn btn-sm btn-success"><i class="fas fa-check"></i> Clear</button>
                                </form>
                                <?php endif; ?>
                                <div class="modal fade" id="modal<?php echo $o['id']; ?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header bg-dark text-white"><h5>Update Order <?php echo $o['order_number']; ?></h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><form method="POST"><div class="modal-body"><input type="hidden" name="order_id" value="<?php echo $o['id']; ?>"><div class="mb-3"><label>Order Status</label><select name="order_status" class="form-control"><option <?php echo $o['order_status']=='pending'?'selected':''; ?>>pending</option><option <?php echo $o['order_status']=='processing'?'selected':''; ?>>processing</option><option <?php echo $o['order_status']=='shipped'?'selected':''; ?>>shipped</option><option <?php echo $o['order_status']=='delivered'?'selected':''; ?>>delivered</option><option <?php echo $o['order_status']=='cancelled'?'selected':''; ?>>cancelled</option></select></div><div class="mb-3"><label>Payment Status</label><select name="payment_status" class="form-control"><option <?php echo $o['payment_status']=='pending'?'selected':''; ?>>pending</option><option <?php echo $o['payment_status']=='completed'?'selected':''; ?>>completed</option><option <?php echo $o['payment_status']=='failed'?'selected':''; ?>>failed</option></select></div></div><div class="modal-footer"><button type="submit" name="update_order" class="btn btn-primary">Save Changes</button></div></form></div></div></div>
                             </div>
                         </div>
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