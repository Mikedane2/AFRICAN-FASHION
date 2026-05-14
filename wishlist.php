<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config/config.php'; 
$sessionId = getSessionId();
$items = getWishlist($pdo, $sessionId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f5f5f5; font-family: 'Inter', sans-serif; }
        .wishlist-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .product-card { background: white; border-radius: 8px; overflow: hidden; transition: 0.2s; margin-bottom: 20px; border: 1px solid #ddd; height: 100%; }
        .product-card:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .product-image { height: 180px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; padding: 15px; }
        .product-image img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .product-info { padding: 12px; border-top: 1px solid #eee; }
        .product-title { font-size: 14px; font-weight: 500; height: 40px; overflow: hidden; }
        .product-title a { color: #007185; text-decoration: none; }
        .product-price { font-size: 16px; font-weight: 700; color: #B12704; }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="wishlist-container">
    <h2 class="mb-4">My Wishlist</h2>
    
    <?php if(empty($items)): ?>
    <div class="text-center py-5" style="background: white; border-radius: 12px;">
        <i class="fas fa-heart fa-4x mb-3" style="color: #ccc;"></i>
        <h4>Your wishlist is empty</h4>
        <p>Save your favorite items here</p>
        <a href="shop.php" class="btn btn-primary">Start Shopping</a>
    </div>
    <?php else: ?>
    <div class="row">
        <?php foreach($items as $item): $price = convertPrice($item['price_usd'], $currentCurrency); ?>
        <div class="col-md-3 col-6 mb-4">
            <div class="product-card">
                <div class="product-image">
                    <img src="assets/uploads/<?php echo $item['first_image']; ?>" onerror="this.src='https://via.placeholder.com/150'">
                </div>
                <div class="product-info">
                    <div class="product-title"><a href="product.php?slug=<?php echo $item['slug']; ?>"><?php echo $item['name']; ?></a></div>
                    <div class="product-price"><?php echo formatPrice($price); ?></div>
                    <button class="btn btn-primary btn-sm w-100 mt-2 add-to-cart" data-id="<?php echo $item['product_id']; ?>">
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </button>
                    <button class="btn btn-danger btn-sm w-100 mt-1 remove-wishlist" data-id="<?php echo $item['product_id']; ?>">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>