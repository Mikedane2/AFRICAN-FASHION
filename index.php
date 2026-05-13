<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config/config.php'; 
?>
<?php include 'includes/header.php'; ?>

<!-- Hero Banner - IKEA/Amazon Style -->
<div class="hero-banner">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h1>Discover African <br>Fashion & Culture</h1>
                <p class="lead mb-4">Authentic Dashiki, Ankara, Kente, and traditional wear from across Africa. Free shipping across all 54 African nations!</p>
                <a href="shop.php" class="btn btn-primary btn-lg">Shop Now <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
            <div class="col-md-5 text-center">
                <i class="fas fa-tshirt fa-4x me-3"></i>
                <i class="fas fa-shoe-prints fa-4x me-3"></i>
                <i class="fas fa-gem fa-4x"></i>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Featured Products - Amazon Style -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="section-title d-flex justify-content-between align-items-center">
                <h2>Featured Products</h2>
                <a href="shop.php">See more <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        <?php $featured = getFeaturedProducts($pdo, 8); ?>
        <?php foreach($featured as $product): 
            $price = convertPrice($product['price_usd'], $currentCurrency); 
            $compare = isset($product['compare_price_usd']) && $product['compare_price_usd'] ? convertPrice($product['compare_price_usd'], $currentCurrency) : null; 
            $img = isset($product['images_array'][0]) ? $product['images_array'][0] : 'placeholder.jpg';
            $ratingCount = isset($product['rating_count']) ? $product['rating_count'] : rand(10, 500);
        ?>
        <div class="col-md-3 col-6">
            <div class="product-card">
                <?php if($compare): $discount = round((($product['compare_price_usd'] - $product['price_usd']) / $product['compare_price_usd']) * 100); ?>
                <div class="product-badge">-<?php echo $discount; ?>%</div>
                <?php endif; ?>
                <div class="product-image">
                    <img src="assets/uploads/<?php echo $img; ?>" onerror="this.src='https://via.placeholder.com/200'">
                </div>
                <div class="product-info">
                    <div class="product-title"><a href="product.php?slug=<?php echo $product['slug']; ?>"><?php echo $product['name']; ?></a></div>
                    <div class="product-price"><?php echo formatPrice($price); ?>
                        <?php if($compare): ?><span class="product-old-price"><?php echo formatPrice($compare); ?></span><?php endif; ?>
                    </div>
                    <div class="product-rating">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                        <span>(<?php echo $ratingCount; ?>)</span>
                    </div>
                    <div class="mt-2">
                        <button class="btn btn-primary btn-sm w-100 add-to-cart" data-id="<?php echo $product['id']; ?>">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Today's Deals Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="section-title d-flex justify-content-between align-items-center">
                <h2>Today's Deals</h2>
                <a href="sale.php">See more <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        <?php $sale = getSaleProducts($pdo, 4); ?>
        <?php foreach($sale as $product): 
            $price = convertPrice($product['price_usd'], $currentCurrency); 
            $compare = isset($product['compare_price_usd']) && $product['compare_price_usd'] ? convertPrice($product['compare_price_usd'], $currentCurrency) : null; 
            $img = isset($product['images_array'][0]) ? $product['images_array'][0] : 'placeholder.jpg';
        ?>
        <div class="col-md-3 col-6">
            <div class="product-card">
                <div class="product-badge">Limited Deal</div>
                <div class="product-image">
                    <img src="assets/uploads/<?php echo $img; ?>" onerror="this.src='https://via.placeholder.com/200'">
                </div>
                <div class="product-info">
                    <div class="product-title"><a href="product.php?slug=<?php echo $product['slug']; ?>"><?php echo $product['name']; ?></a></div>
                    <div class="product-price"><?php echo formatPrice($price); ?>
                        <?php if($compare): ?><span class="product-old-price"><?php echo formatPrice($compare); ?></span><?php endif; ?>
                    </div>
                    <div class="mt-2">
                        <button class="btn btn-primary btn-sm w-100 add-to-cart" data-id="<?php echo $product['id']; ?>">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Best Sellers Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="section-title d-flex justify-content-between align-items-center">
                <h2>Best Sellers</h2>
                <a href="shop.php">See more <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        <?php $trending = getTrendingProducts($pdo, 4); ?>
        <?php foreach($trending as $product): 
            $price = convertPrice($product['price_usd'], $currentCurrency); 
            $img = isset($product['images_array'][0]) ? $product['images_array'][0] : 'placeholder.jpg';
        ?>
        <div class="col-md-3 col-6">
            <div class="product-card">
                <div class="product-image">
                    <img src="assets/uploads/<?php echo $img; ?>" onerror="this.src='https://via.placeholder.com/200'">
                </div>
                <div class="product-info">
                    <div class="product-title"><a href="product.php?slug=<?php echo $product['slug']; ?>"><?php echo $product['name']; ?></a></div>
                    <div class="product-price"><?php echo formatPrice($price); ?></div>
                    <div class="product-rating">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <span>Best Seller</span>
                    </div>
                    <div class="mt-2">
                        <button class="btn btn-primary btn-sm w-100 add-to-cart" data-id="<?php echo $product['id']; ?>">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.add-to-cart').click(function() {
        var $btn = $(this);
        var productId = $btn.data('id');
        var quantity = 1;
        
        $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
        $.ajax({
            url: 'includes/cart.php',
            method: 'POST',
            data: { action: 'add', product_id: productId, quantity: quantity },
            success: function(res) {
                var data = JSON.parse(res);
                if(data.success) {
                    $('#cart-count').text(data.cart_count);
                    alert('Added to cart!');
                } else {
                    alert(data.message || 'Error adding to cart');
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