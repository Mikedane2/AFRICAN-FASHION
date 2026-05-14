<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config/config.php'; 

// Get active hero ad
$heroAd = $pdo->query("SELECT * FROM ads WHERE type = 'hero' AND active = 1 AND (start_date <= CURDATE() OR start_date IS NULL) AND (end_date >= CURDATE() OR end_date IS NULL) ORDER BY display_order LIMIT 1")->fetch();
?>
<?php include 'includes/header.php'; ?>

<?php if($heroAd): ?>
<div class="hero-banner" style="background: linear-gradient(135deg, #232F3E 0%, #37475A 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h1><?php echo $heroAd['title']; ?></h1>
                <p class="lead mb-4"><?php echo $heroAd['description']; ?></p>
                <?php if($heroAd['discount_percentage'] > 0): ?>
                    <div class="mb-3">
                        <span class="badge bg-danger fs-5">-<?php echo $heroAd['discount_percentage']; ?>% OFF</span>
                    </div>
                <?php endif; ?>
                <a href="shop.php" class="btn btn-primary btn-lg">Shop Now <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
            <div class="col-md-5 text-center">
                <?php if($heroAd['image'] && file_exists('assets/uploads/ads/'.$heroAd['image'])): ?>
                    <img src="assets/uploads/ads/<?php echo $heroAd['image']; ?>" class="img-fluid" style="max-height: 250px;">
                <?php else: ?>
                    <i class="fas fa-tags fa-5x"></i>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="hero-banner">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h1>Discover African Fashion & Culture</h1>
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
<?php endif; ?>

<div class="container">
    <!-- Featured Products -->
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

    <!-- Today's Deals -->
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

    <!-- Best Sellers -->
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

<style>
.hero-banner { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 50px 0; margin-bottom: 30px; }
.hero-banner h1 { font-size: 42px; font-weight: 700; margin-bottom: 20px; }
.product-card { background: white; border-radius: 8px; overflow: hidden; transition: 0.2s; margin-bottom: 20px; border: 1px solid #ddd; height: 100%; position: relative; }
.product-card:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.1); transform: translateY(-2px); }
.product-image { height: 200px; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #f8f9fa; padding: 15px; }
.product-image img { max-width: 100%; max-height: 100%; object-fit: contain; }
.product-badge { position: absolute; top: 10px; left: 10px; background: #CC0C39; color: white; padding: 4px 8px; font-size: 12px; font-weight: 600; border-radius: 4px; z-index: 1; }
.product-info { padding: 12px; border-top: 1px solid #eee; }
.product-title { font-size: 14px; font-weight: 500; margin-bottom: 8px; height: 40px; overflow: hidden; }
.product-title a { color: #007185; text-decoration: none; }
.product-price { font-size: 18px; font-weight: 700; color: #B12704; }
.product-old-price { font-size: 13px; color: #565959; text-decoration: line-through; margin-left: 8px; font-weight: 400; }
.product-rating { color: #FFA41C; font-size: 12px; margin-bottom: 5px; }
.section-title { margin-bottom: 20px; position: relative; }
.section-title h2 { font-size: 22px; font-weight: 700; color: #111; margin: 0; }
.section-title a { font-size: 14px; color: #007185; text-decoration: none; }
@media (max-width: 768px) { .hero-banner h1 { font-size: 28px; } .product-image { height: 150px; } }
</style>

<?php include 'includes/footer.php'; ?>