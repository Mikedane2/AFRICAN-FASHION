<?php 
require_once 'config/config.php'; 
$slug = $_GET['slug'] ?? '';
$product = getProductBySlug($pdo, $slug);
if(!$product) { header('HTTP/1.0 404 Not Found'); die('<h1>Product Not Found</h1>'); }
$price = convertPrice($product['price_usd'], $currentCurrency);
$compare = $product['compare_price_usd'] ? convertPrice($product['compare_price_usd'], $currentCurrency) : null;
$sizes = $product['sizes'] ? explode(',', $product['sizes']) : [];
$colors = $product['colors'] ? explode(',', $product['colors']) : [];
$img = $product['images_array'][0];
?>
<!DOCTYPE html>
<html lang="en">
<head><title><?php echo $product['name']; ?> - <?php echo SITE_NAME; ?></title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"><link rel="stylesheet" href="assets/css/style.css"></head>
<body>
<?php include 'includes/header.php'; ?>
<div class="container my-5">
    <div class="row">
        <div class="col-md-6">
            <img src="assets/uploads/<?php echo $img; ?>" class="img-fluid rounded" onerror="this.src='https://via.placeholder.com/500'">
        </div>
        <div class="col-md-6">
            <h1><?php echo $product['name']; ?></h1>
            <div class="product-price h2 mb-3"><?php echo formatPrice($price); ?><?php if($compare): ?><span class="text-muted text-decoration-line-through ms-2"><?php echo formatPrice($compare); ?></span><?php endif; ?></div>
            <p class="mb-4"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            <p><strong>Availability:</strong> <?php echo $product['stock_quantity'] > 0 ? "<span class='text-success'>In Stock ({$product['stock_quantity']} items)</span>" : "<span class='text-danger'>Out of Stock</span>"; ?></p>
            <?php if($sizes): ?><div class="mb-3"><label class="fw-bold">Size:</label><div class="mt-2"><?php foreach($sizes as $s): ?><button class="btn btn-outline-secondary btn-sm me-2 size-btn" data-size="<?php echo trim($s); ?>"><?php echo trim($s); ?></button><?php endforeach; ?></div></div><?php endif; ?>
            <?php if($colors): ?><div class="mb-3"><label class="fw-bold">Color:</label><div class="mt-2"><?php foreach($colors as $c): $bg = strtolower(trim($c)); ?><button class="btn color-btn me-2" data-color="<?php echo trim($c); ?>" style="background:<?php echo $bg; ?>; width:35px; height:35px; border-radius:50%; border:1px solid #ddd;"></button><?php endforeach; ?></div></div><?php endif; ?>
            <div class="mb-3"><label class="fw-bold">Quantity:</label><div class="d-flex align-items-center mt-2"><button class="btn btn-outline-secondary quantity-minus">-</button><input type="number" id="qty" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" class="form-control text-center mx-2" style="width:80px"><button class="btn btn-outline-secondary quantity-plus">+</button></div></div>
            <div class="mt-4"><button class="btn btn-primary btn-lg add-to-cart" data-id="<?php echo $product['id']; ?>"><i class="fas fa-cart-plus"></i> Add to Cart</button><button class="btn btn-outline-danger btn-lg add-to-wishlist ms-2" data-id="<?php echo $product['id']; ?>"><i class="far fa-heart"></i> Wishlist</button></div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
</body>
</html>