<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config/config.php'; 
$sessionId = getSessionId();
$items = getCartItems($pdo, $sessionId);
$total = getCartTotal($pdo, $sessionId, $currentCurrency);
$shipping = convertPrice(10, $currentCurrency);
$grandTotal = $total + $shipping;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f5f5f5; font-family: 'Inter', sans-serif; }
        .cart-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .cart-table { background: white; border-radius: 12px; overflow: hidden; }
        .cart-table th { background: #232F3E; color: white; border: none; padding: 15px; }
        .cart-item-image { width: 80px; height: 80px; object-fit: contain; background: #f8f9fa; border-radius: 8px; padding: 5px; }
        .cart-qty { width: 70px; text-align: center; }
        .order-summary { background: white; border-radius: 12px; padding: 20px; position: sticky; top: 20px; }
        .btn-checkout { background: #FF9900; color: #111; font-weight: 600; padding: 12px; width: 100%; border: none; border-radius: 8px; }
        .btn-checkout:hover { background: #ff8c00; }
        @media (max-width: 768px) { .order-summary { position: relative; top: 0; margin-top: 20px; } }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="cart-container">
    <h2 class="mb-4">Shopping Cart</h2>
    
    <?php if(empty($items)): ?>
    <div class="text-center py-5" style="background: white; border-radius: 12px;">
        <i class="fas fa-shopping-cart fa-4x mb-3" style="color: #ccc;"></i>
        <h4>Your cart is empty</h4>
        <p>Add items to your cart and they will appear here</p>
        <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
    </div>
    <?php else: ?>
    <div class="row">
        <div class="col-md-8">
            <div class="cart-table">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $item): $price = convertPrice($item['price_usd'], $currentCurrency); $subtotal = $price * $item['quantity']; ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="assets/uploads/<?php echo $item['first_image']; ?>" class="cart-item-image me-3" onerror="this.src='https://via.placeholder.com/80'">
                                    <div>
                                        <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                        <?php if($item['size']): ?><br><small class="text-muted">Size: <?php echo $item['size']; ?></small><?php endif; ?>
                                        <?php if($item['color']): ?><br><small class="text-muted">Color: <?php echo $item['color']; ?></small><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <td><?php echo formatPrice($price); ?>
                            </div>
                            <td>
                                <input type="number" class="form-control cart-qty" data-id="<?php echo $item['id']; ?>" value="<?php echo $item['quantity']; ?>" min="1" style="width: 70px;">
                            </div>
                            <td><?php echo formatPrice($subtotal); ?></div>
                            <td>
                                <button class="btn btn-danger btn-sm remove-item" data-id="<?php echo $item['id']; ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <a href="shop.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="order-summary">
                <h4 class="mb-3">Order Summary</h4>
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal (<?php echo count($items); ?> items):</span>
                    <strong><?php echo formatPrice($total); ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Shipping:</span>
                    <strong><?php echo formatPrice($shipping); ?></strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <span>Total:</span>
                    <strong class="h4 text-primary"><?php echo formatPrice($grandTotal); ?></strong>
                </div>
                <a href="checkout.php" class="btn-checkout btn">Proceed to Checkout</a>
                
                <div class="mt-3 small text-muted text-center">
                    <i class="fas fa-lock"></i> Secure checkout
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    $('.cart-qty').change(function() {
        var cartId = $(this).data('id');
        var quantity = $(this).val();
        $.ajax({
            url: 'includes/cart.php',
            method: 'POST',
            data: { action: 'update', cart_id: cartId, quantity: quantity },
            success: function() { location.reload(); }
        });
    });
    
    $('.remove-item').click(function() {
        if(confirm('Remove item from cart?')) {
            var cartId = $(this).data('id');
            $.ajax({
                url: 'includes/cart.php',
                method: 'POST',
                data: { action: 'remove', cart_id: cartId },
                success: function() { location.reload(); }
            });
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>