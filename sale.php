<?php 
require_once 'config/config.php'; 
$products = getSaleProducts($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head><title>Sale - <?php echo SITE_NAME; ?></title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"><link rel="stylesheet" href="assets/css/style.css"></head>
<body>
<?php include 'includes/header.php'; ?>
<div class="container my-5"><div class="bg-danger text-white text-center p-5 rounded mb-5"><h1 class="display-4"><i class="fas fa-tags"></i> African Fashion Sale!</h1><p class="lead">Up to 50% off on selected Dashiki, Ankara, and Traditional Wear</p><p>Limited time offer - Shop now and save!</p></div><div class="row"><?php if(empty($products)): ?><div class="col-12"><div class="alert alert-info">No sale products at the moment. Check back soon!</div></div><?php else: ?><?php foreach($products as $p): $price = convertPrice($p['price_usd'], $currentCurrency); $compare = convertPrice($p['compare_price_usd'], $currentCurrency); $discount = round((($p['compare_price_usd'] - $p['price_usd']) / $p['compare_price_usd']) * 100); ?><div class="col-md-3 col-6 mb-4"><div class="product-card"><div class="product-badge"><span class="badge bg-danger">-<?php echo $discount; ?>% OFF</span></div><div class="product-image"><img src="assets/uploads/<?php echo $p['images_array'][0]; ?>" onerror="this.src='https://via.placeholder.com/250'"><div class="product-actions"><button class="btn btn-primary btn-sm add-to-cart" data-id="<?php echo $p['id']; ?>"><i class="fas fa-cart-plus"></i> Add</button></div></div><div class="product-info"><div class="product-title"><a href="product.php?slug=<?php echo $p['slug']; ?>"><?php echo $p['name']; ?></a></div><div class="product-price"><?php echo formatPrice($price); ?> <span class="product-old-price"><?php echo formatPrice($compare); ?></span></div></div></div></div><?php endforeach; ?><?php endif; ?></div></div>
<?php include 'includes/footer.php'; ?>
</body>
</html>