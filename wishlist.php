<?php 
require_once 'config/config.php'; 
$sessionId = getSessionId();
$items = getWishlist($pdo, $sessionId);
?>
<!DOCTYPE html>
<html lang="en">
<head><title>My Wishlist - <?php echo SITE_NAME; ?></title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"><link rel="stylesheet" href="assets/css/style.css"></head>
<body>
<?php include 'includes/header.php'; ?>
<div class="container my-5"><h1>My Wishlist</h1><div class="row"><?php if(empty($items)): ?><div class="col-12"><div class="alert alert-info text-center py-5"><i class="fas fa-heart fa-3x mb-3"></i><h4>Your wishlist is empty</h4><a href="shop.php" class="btn btn-primary">Start Shopping</a></div></div><?php else: ?><?php foreach($items as $item): $price = convertPrice($item['price_usd'], $currentCurrency); ?><div class="col-md-3 col-6 mb-4"><div class="product-card"><div class="product-image"><img src="assets/uploads/<?php echo $item['first_image']; ?>" onerror="this.src='https://via.placeholder.com/250'"><div class="product-actions"><button class="btn btn-primary btn-sm add-to-cart" data-id="<?php echo $item['product_id']; ?>"><i class="fas fa-cart-plus"></i> Add</button><button class="btn btn-danger btn-sm remove-wishlist" data-id="<?php echo $item['product_id']; ?>"><i class="fas fa-trash"></i></button></div></div><div class="product-info"><div class="product-title"><a href="product.php?slug=<?php echo $item['slug']; ?>"><?php echo $item['name']; ?></a></div><div class="product-price"><?php echo formatPrice($price); ?></div></div></div></div><?php endforeach; ?><?php endif; ?></div></div>
<?php include 'includes/footer.php'; ?>
</body>
</html>