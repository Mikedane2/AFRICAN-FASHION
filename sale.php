<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config/config.php'; 
$products = getSaleProducts($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f5f5f5; font-family: 'Inter', sans-serif; }
        .sale-banner { background: linear-gradient(135deg, #CC0C39 0%, #E63946 100%); }
        .product-card { background: white; border-radius: 8px; overflow: hidden; transition: 0.2s; margin-bottom: 20px; border: 1px solid #ddd; height: 100%; }
        .product-card:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .product-image { height: 200px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; padding: 15px; position: relative; }
        .product-image img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .sale-badge { position: absolute; top: 10px; left: 10px; background: #CC0C39; color: white; padding: 5px 10px; font-size: 12px; font-weight: 600; border-radius: 4px; }
        .product-info { padding: 12px; border-top: 1px solid #eee; }
        .product-title { font-size: 14px; font-weight: 500; height: 40px; overflow: hidden; }
        .product-price { font-size: 18px; font-weight: 700; color: #B12704; }
        .product-old-price { font-size: 13px; color: #565959; text-decoration: line-through; margin-left: 8px; }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="container my-4">
    <div class="sale-banner text-white text-center p-5 rounded mb-5">
        <h1 class="display-4"><i class="fas fa-tags"></i> Mega Sale!</h1>
        <p class="lead">Up to 50% off on selected items</p>
        <p>Limited time offer - Shop now and save!</p>
    </div>
    
    <div class="row">
        <?php if(empty($products)): ?>
        <div class="col-12">
            <div class="alert alert-info text-center py-5">No sale products at the moment. Check back soon!</div>
        </div>
        <?php else: ?>
        <?php foreach($products as $p): $price = convertPrice($p['price_usd'], $currentCurrency); $compare = convertPrice($p['compare_price_usd'], $currentCurrency); $discount = round((($p['compare_price_usd'] - $p['price_usd']) / $p['compare_price_usd']) * 100); ?>
        <div class="col-md-3 col-6 mb-4">
            <div class="product-card">
                <div class="product-image">
                    <span class="sale-badge">-<?php echo $discount; ?>% OFF</span>
                    <img src="assets/uploads/<?php echo $p['images_array'][0]; ?>" onerror="this.src='https://via.placeholder.com/200'">
                </div>
                <div class="product-info">
                    <div class="product-title"><a href="product.php?slug=<?php echo $p['slug']; ?>"><?php echo $p['name']; ?></a></div>
                    <div class="product-price"><?php echo formatPrice($price); ?> <span class="product-old-price"><?php echo formatPrice($compare); ?></span></div>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.add-to-cart').click(function() {
        var $btn = $(this);
        var productId = $btn.data('id');
        $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
        $.ajax({
            url: 'includes/cart.php',
            method: 'POST',
            data: { action: 'add', product_id: productId, quantity: 1 },
            success: function(res) {
                var data = JSON.parse(res);
                if(data.success) {
                    $('#cart-count').text(data.cart_count);
                    alert('Added to cart!');
                }
                $btn.html('<i class="fas fa-cart-plus"></i> Add to Cart').prop('disabled', false);
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>