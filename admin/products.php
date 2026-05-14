<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../config/config.php';
if(!isAdmin()) { header('Location: login.php'); exit; }

// Add Product
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $description = $_POST['description'];
    $short_description = $_POST['short_description'];
    $price_usd = floatval($_POST['price_usd']);
    $compare_price_usd = !empty($_POST['compare_price_usd']) ? floatval($_POST['compare_price_usd']) : null;
    $category_id = intval($_POST['category_id']);
    $brand = $_POST['brand'];
    $sizes = $_POST['sizes'];
    $colors = $_POST['colors'];
    $stock_quantity = intval($_POST['stock_quantity']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $trending = isset($_POST['trending']) ? 1 : 0;
    $best_seller = isset($_POST['best_seller']) ? 1 : 0;
    $new_arrival = isset($_POST['new_arrival']) ? 1 : 0;
    
    $uploadedImages = [];
    $uploadDir = '../assets/uploads/';
    if(!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
    
    if(isset($_FILES['product_images']) && !empty($_FILES['product_images']['name'][0])) {
        $allowed = ['jpg','jpeg','png','gif','webp','bmp'];
        for($i = 0; $i < count($_FILES['product_images']['name']); $i++) {
            if($_FILES['product_images']['error'][$i] == 0) {
                $ext = strtolower(pathinfo($_FILES['product_images']['name'][$i], PATHINFO_EXTENSION));
                if(in_array($ext, $allowed)) {
                    $filename = time() . '_' . $i . '.' . $ext;
                    move_uploaded_file($_FILES['product_images']['tmp_name'][$i], $uploadDir . $filename);
                    $uploadedImages[] = $filename;
                }
            }
        }
    }
    
    $sku = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 5)) . '-' . time();
    $imagesJson = json_encode($uploadedImages);
    $stmt = $pdo->prepare("INSERT INTO products (name, slug, description, short_description, price_usd, compare_price_usd, category_id, brand, sizes, colors, stock_quantity, sku, images, featured, trending, best_seller, new_arrival) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute([$name, $slug, $description, $short_description, $price_usd, $compare_price_usd, $category_id, $brand, $sizes, $colors, $stock_quantity, $sku, $imagesJson, $featured, $trending, $best_seller, $new_arrival]);
    header('Location: products.php?msg=added');
    exit;
}

// Delete Product
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("SELECT images FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $p = $stmt->fetch();
    if($p && $p['images']) {
        $imgs = json_decode($p['images'], true);
        foreach($imgs as $img) { if(file_exists('../assets/uploads/'.$img)) unlink('../assets/uploads/'.$img); }
    }
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: products.php?msg=deleted');
    exit;
}

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - AfriMart Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-sidebar { background: #1a1a2e; min-height: 100vh; color: white; position: sticky; top: 0; }
        .admin-sidebar .logo { padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .admin-sidebar .logo h3 { color: #FF9900; margin: 0; }
        .admin-sidebar .nav-link { color: #ddd; padding: 12px 20px; display: flex; align-items: center; }
        .admin-sidebar .nav-link:hover, .admin-sidebar .nav-link.active { background: #FF9900; color: #111; }
        .admin-sidebar .nav-link i { width: 25px; margin-right: 10px; }
        .btn-amazon { background: #FF9900; color: #111; font-weight: 600; border: none; padding: 8px 20px; border-radius: 8px; }
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
                <a class="nav-link active" href="products.php"><i class="fas fa-box"></i> Products</a>
                <a class="nav-link" href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a class="nav-link" href="inventory.php"><i class="fas fa-warehouse"></i> Inventory</a>
                <a class="nav-link" href="ads.php"><i class="fas fa-bullhorn"></i> Ads & Offers</a>
                <a class="nav-link" href="profile.php"><i class="fas fa-user-cog"></i> Profile</a>
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </div>
        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between mb-4"><h2><i class="fas fa-box text-primary"></i> Products</h2><button class="btn btn-amazon" data-bs-toggle="modal" data-bs-target="#productModal"><i class="fas fa-plus"></i> Add Product</button></div>
            <?php if(isset($_GET['msg'])): ?><div class="alert alert-success">Product <?php echo $_GET['msg']; ?> successfully!</div><?php endif; ?>
            <div class="table-responsive"><table class="table table-bordered bg-white"><thead class="table-dark"><tr><th>ID</th><th>Image</th><th>Name</th><th>Price</th><th>Stock</th><th>Actions</th></tr></thead><tbody><?php foreach($products as $p): $imgs = $p['images'] ? json_decode($p['images'], true) : ['placeholder.jpg']; ?><tr><td><?php echo $p['id']; ?></td><td><img src="../assets/uploads/<?php echo $imgs[0]; ?>" width="50" height="50" style="object-fit:cover; border-radius:8px;"></td><td><?php echo $p['name']; ?></td><td>$<?php echo $p['price_usd']; ?></td><td><?php echo $p['stock_quantity']; ?></td><td><a href="?delete=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a></td></tr><?php endforeach; ?></tbody></table></div>
        </div>
    </div>
</div>

<div class="modal fade" id="productModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header bg-dark text-white"><h5>Add Product</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><form method="POST" enctype="multipart/form-data"><div class="modal-body"><div class="row"><div class="col-md-6"><input type="text" name="name" class="form-control mb-2" placeholder="Product Name" required></div><div class="col-md-6"><select name="category_id" class="form-control mb-2"><option value="">Select Category</option><?php foreach($categories as $c): ?><option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option><?php endforeach; ?></select></div></div><textarea name="description" class="form-control mb-2" rows="3" placeholder="Description"></textarea><input type="text" name="short_description" class="form-control mb-2" placeholder="Short Description"><div class="row"><div class="col-md-4"><input type="number" step="0.01" name="price_usd" class="form-control mb-2" placeholder="Price (USD)" required></div><div class="col-md-4"><input type="number" step="0.01" name="compare_price_usd" class="form-control mb-2" placeholder="Compare Price"></div><div class="col-md-4"><input type="number" name="stock_quantity" class="form-control mb-2" placeholder="Stock" required></div></div><input type="text" name="brand" class="form-control mb-2" placeholder="Brand"><input type="text" name="sizes" class="form-control mb-2" placeholder="Sizes (S,M,L)"><input type="text" name="colors" class="form-control mb-2" placeholder="Colors (Red,Blue)"><input type="file" name="product_images[]" class="form-control mb-2" accept="image/*" multiple><div class="row"><div class="col-md-3"><div class="form-check"><input type="checkbox" name="featured" class="form-check-input"><label>Featured</label></div></div><div class="col-md-3"><div class="form-check"><input type="checkbox" name="trending" class="form-check-input"><label>Trending</label></div></div><div class="col-md-3"><div class="form-check"><input type="checkbox" name="best_seller" class="form-check-input"><label>Best Seller</label></div></div><div class="col-md-3"><div class="form-check"><input type="checkbox" name="new_arrival" class="form-check-input"><label>New Arrival</label></div></div></div></div><div class="modal-footer"><button type="submit" name="add_product" class="btn btn-amazon">Save Product</button></div></form></div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>