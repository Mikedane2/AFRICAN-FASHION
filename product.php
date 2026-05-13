<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config/config.php'; 
$slug = $_GET['slug'] ?? '';
$product = getProductBySlug($pdo, $slug);
if(!$product) { header('HTTP/1.0 404 Not Found'); die('<h1>Product Not Found</h1>'); }
$price = convertPrice($product['price_usd'], $currentCurrency);
$compare = $product['compare_price_usd'] ? convertPrice($product['compare_price_usd'], $currentCurrency) : null;
$sizes = $product['sizes'] ? explode(',', $product['sizes']) : [];
$colors = $product['colors'] ? explode(',', $product['colors']) : [];
$img = $product['images_array'][0];
$discount = $compare ? round((($product['compare_price_usd'] - $product['price_usd']) / $product['compare_price_usd']) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f5f5f5; }
        .product-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .product-gallery { background: white; border-radius: 12px; padding: 20px; text-align: center; }
        .main-image { max-width: 100%; max-height: 400px; object-fit: contain; }
        .thumbnail { width: 80px; height: 80px; object-fit: cover; margin: 5px; cursor: pointer; border: 2px solid transparent; border-radius: 8px; }
        .thumbnail.active { border-color: #FF9900; }
        .product-details { background: white; border-radius: 12px; padding: 30px; }
        .product-price { font-size: 28px; font-weight: 700; color: #B12704; }
        .size-btn, .color-btn { margin: 5px; cursor: pointer; }
        .size-btn.active { background: #FF9900; color: white; border-color: #FF9900; }
        .quantity-selector { display: flex; align-items: center; gap: 10px; }
        .quantity-btn { width: 35px; height: 35px; border: 1px solid #ddd; background: white; cursor: pointer; }
        .quantity-input { width: 60px; text-align: center; border: 1px solid #ddd; padding: 5px; }
        .btn-add-cart { background: #FF9900; border: none; padding: 12px 30px; font-weight: 600; color: #111; border-radius: 8px; }
        .btn-add-cart:hover { background: #ff8c00; }
        @media (max-width: 768px) { .product-details { margin-top: 20px; } }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="product-container">
    <div class="row">
        <div class="col-md-6">
            <div class="product-gallery">
                <img src="assets/uploads/<?php echo $img; ?>" class="main-image" id="mainImage" onerror="this.src='https://via.placeholder.com/400'">
                <?php if(count($product['images_array']) > 1): ?>
                <div class="mt-3">
                    <?php foreach($product['images_array'] as $thumb): ?>
                    <img src="assets/uploads/<?php echo $thumb; ?>" class="thumbnail" onclick="document.getElementById('mainImage').src='assets/uploads/<?php echo $thumb; ?>'">
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="product-details">
                <h1><?php echo $product['name']; ?></h1>
                <div class="product-price mb-3">
                    <?php echo formatPrice($price); ?>
                    <?php if($compare): ?>
                        <span class="text-muted text-decoration-line-through ms-2"><?php echo formatPrice($compare); ?></span>
                        <span class="badge bg-danger ms-2">-<?php echo $discount; ?>%</span>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <span class="text-muted">Availability:</span>
                    <?php if($product['stock_quantity'] > 0): ?>
                        <span class="text-success">In Stock (<?php echo $product['stock_quantity']; ?> items)</span>
                    <?php else: ?>
                        <span class="text-danger">Out of Stock</span>
                    <?php endif; ?>
                </div>
                
                <div class="mb-4">
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>
                
                <?php if($sizes): ?>
                <div class="mb-3">
                    <label class="fw-bold">Size:</label>
                    <div>
                        <?php foreach($sizes as $s): ?>
                        <button class="btn btn-outline-secondary size-btn" data-size="<?php echo trim($s); ?>"><?php echo trim($s); ?></button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if($colors): ?>
                <div class="mb-3">
                    <label class="fw-bold">Color:</label>
                    <div>
                        <?php foreach($colors as $c): ?>
                        <button class="btn color-btn" data-color="<?php echo trim($c); ?>" style="background: <?php echo strtolower(trim($c)); ?>; width: 35px; height: 35px; border-radius: 50%; margin: 5px;"></button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="mb-3">
                    <label class="fw-bold">Quantity:</label>
                    <div class="quantity-selector">
                        <button class="quantity-btn quantity-minus">-</button>
                        <input type="number" id="qty" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" class="quantity-input">
                        <button class="quantity-btn quantity-plus">+</button>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button class="btn-add-cart add-to-cart" data-id="<?php echo $product['id']; ?>">
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </button>
                    <button class="btn btn-outline-danger ms-2 add-to-wishlist" data-id="<?php echo $product['id']; ?>">
                        <i class="far fa-heart"></i> Wishlist
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.size-btn').click(function() {
        $('.size-btn').removeClass('active btn-primary').addClass('btn-outline-secondary');
        $(this).removeClass('btn-outline-secondary').addClass('active btn-primary');
    });
    
    $('.color-btn').click(function() {
        $('.color-btn').removeClass('active border-primary');
        $(this).addClass('active border-primary');
    });
    
    $('.quantity-plus').click(function() {
        var input = $('#qty');
        input.val(parseInt(input.val()) + 1);
    });
    
    $('.quantity-minus').click(function() {
        var input = $('#qty');
        if(parseInt(input.val()) > 1) input.val(parseInt(input.val()) - 1);
    });
    
    $('.add-to-cart').click(function() {
        var $btn = $(this);
        var productId = $btn.data('id');
        var quantity = $('#qty').val();
        var size = $('.size-btn.active').data('size');
        var color = $('.color-btn.active').data('color');
        
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Adding...').prop('disabled', true);
        $.ajax({
            url: 'includes/cart.php',
            method: 'POST',
            data: { action: 'add', product_id: productId, quantity: quantity, size: size, color: color },
            success: function(res) {
                var data = JSON.parse(res);
                if(data.success) {
                    $('#cart-count').text(data.cart_count);
                    alert('Added to cart!');
                } else {
                    alert(data.message);
                }
                $btn.html('<i class="fas fa-cart-plus"></i> Add to Cart').prop('disabled', false);
            },
            error: function() {
                alert('Error adding to cart');
                $btn.html('<i class="fas fa-cart-plus"></i> Add to Cart').prop('disabled', false);
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>