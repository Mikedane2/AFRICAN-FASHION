<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
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
    <title>Shop - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f5f5f5; }
        .product-card { background: white; border-radius: 8px; overflow: hidden; transition: 0.2s; margin-bottom: 20px; border: 1px solid #ddd; height: 100%; position: relative; }
        .product-card:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.1); transform: translateY(-2px); }
        .product-image { height: 200px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; padding: 15px; }
        .product-image img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .product-info { padding: 12px; border-top: 1px solid #eee; }
        .product-title { font-size: 14px; font-weight: 500; height: 40px; overflow: hidden; }
        .product-title a { color: #007185; text-decoration: none; }
        .product-price { font-size: 18px; font-weight: 700; color: #B12704; }
        .filter-sidebar { background: white; border-radius: 8px; padding: 20px; position: sticky; top: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .filter-sidebar h5 { font-size: 16px; font-weight: 700; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #eee; }
        @media (max-width: 768px) { .filter-sidebar { position: relative; top: 0; margin-bottom: 20px; } }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="container my-4">
    <div class="row">
        <div class="col-md-3">
            <div class="filter-sidebar">
                <h5><i class="fas fa-filter"></i> Filter & Sort</h5>
                <label class="form-label fw-bold">Sort By</label>
                <select id="sort-by" class="form-control mb-3">
                    <option value="newest" <?php echo $sort=='newest'?'selected':''; ?>>Newest First</option>
                    <option value="price_low" <?php echo $sort=='price_low'?'selected':''; ?>>Price: Low to High</option>
                    <option value="price_high" <?php echo $sort=='price_high'?'selected':''; ?>>Price: High to Low</option>
                    <option value="popular" <?php echo $sort=='popular'?'selected':''; ?>>Most Popular</option>
                </select>
                <hr>
                <h5><i class="fas fa-tag"></i> Categories</h5>
                <div class="list-group">
                    <a href="shop.php" class="list-group-item list-group-item-action">All Products</a>
                    <?php $cats = getCategories($pdo); ?>
                    <?php foreach($cats as $cat): ?>
                    <a href="shop.php?category=<?php echo $cat['slug']; ?>" class="list-group-item list-group-item-action"><?php echo $cat['name']; ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <h1 class="mb-4"><?php echo $search ? "Search: $search" : ($category ? ucfirst(str_replace('-',' ',$category)) : 'All Products'); ?></h1>
            <div class="row">
                <?php if(empty($products)): ?>
                <div class="col-12"><div class="alert alert-info text-center py-5">No products found. <a href="shop.php">View all products</a></div></div>
                <?php else: ?>
                <?php foreach($products as $p): $price = convertPrice($p['price_usd'], $currentCurrency); $img = isset($p['images_array'][0]) ? $p['images_array'][0] : 'placeholder.jpg'; ?>
                <div class="col-md-4 col-6 mb-4">
                    <div class="product-card">
                        <div class="product-image">
                            <img src="assets/uploads/<?php echo $img; ?>" onerror="this.src='https://via.placeholder.com/200'">
                        </div>
                        <div class="product-info">
                            <div class="product-title"><a href="product.php?slug=<?php echo $p['slug']; ?>"><?php echo $p['name']; ?></a></div>
                            <div class="product-price"><?php echo formatPrice($price); ?></div>
                            <button class="btn btn-primary btn-sm w-100 mt-2 add-to-cart" data-id="<?php echo $p['id']; ?>">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('sort-by')?.addEventListener('change', function() {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', this.value);
    window.location.href = url.toString();
});
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>