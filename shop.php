<?php 
require_once 'config/config.php'; 
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? 'newest';
$products = getProducts($pdo, null, $category, $search, $sort);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop African Fashion - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="container my-5">
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-primary text-white"><h5 class="mb-0"><i class="fas fa-filter"></i> Filter</h5></div>
                <div class="card-body">
                    <label class="form-label fw-bold">Sort By</label>
                    <select id="sort-by" class="form-control">
                        <option value="newest" <?php echo $sort=='newest'?'selected':''; ?>>Newest First</option>
                        <option value="price_low" <?php echo $sort=='price_low'?'selected':''; ?>>Price: Low to High</option>
                        <option value="price_high" <?php echo $sort=='price_high'?'selected':''; ?>>Price: High to Low</option>
                        <option value="popular" <?php echo $sort=='popular'?'selected':''; ?>>Most Popular</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <h1 class="mb-4"><?php echo $search ? "Search Results: $search" : ($category ? ucfirst(str_replace('-',' ',$category)) : 'All African Fashion'); ?></h1>
            <div class="row">
                <?php if(empty($products)): ?>
                <div class="col-12"><div class="alert alert-info text-center py-5">No products found. <a href="shop.php">View all products</a></div></div>
                <?php else: ?>
                <?php foreach($products as $p): $price = convertPrice($p['price_usd'], $currentCurrency); $img = $p['images_array'][0]; ?>
                <div class="col-md-4 col-6 mb-4">
                    <div class="product-card">
                        <div class="product-image">
                            <img src="assets/uploads/<?php echo $img; ?>" onerror="this.src='https://via.placeholder.com/250'">
                            <div class="product-actions">
                                <button class="btn btn-primary btn-sm quick-add-cart" data-id="<?php echo $p['id']; ?>"><i class="fas fa-cart-plus"></i> Add</button>
                            </div>
                        </div>
                        <div class="product-info">
                            <div class="product-title"><a href="product.php?slug=<?php echo $p['slug']; ?>"><?php echo $p['name']; ?></a></div>
                            <div class="product-price"><?php echo formatPrice($price); ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
</body>
</html>