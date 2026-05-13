<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config/config.php'; 
$sessionId = getSessionId();
$items = getCartItems($pdo, $sessionId);
if(empty($items)) header('Location: cart.php');
$total = getCartTotal($pdo, $sessionId, $currentCurrency);
$shipping = convertPrice(10, $currentCurrency);
$grandTotal = $total + $shipping;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f5f5f5; }
        .checkout-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .checkout-form { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .order-summary { background: white; padding: 25px; border-radius: 12px; position: sticky; top: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .payment-method { border: 2px solid #eee; border-radius: 8px; padding: 15px; margin-bottom: 15px; cursor: pointer; transition: all 0.3s; }
        .payment-method:hover { border-color: #FF9900; background: #fff8f0; }
        .payment-method.selected { border-color: #FF9900; background: #fff8f0; }
        .payment-method input { margin-right: 10px; }
        .btn-checkout { background: #FF9900; border: none; padding: 15px; font-weight: 600; font-size: 18px; color: #111; width: 100%; border-radius: 8px; }
        .btn-checkout:hover { background: #ff8c00; color: #111; }
        @media (max-width: 768px) { .order-summary { position: relative; top: 0; margin-top: 20px; } }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="checkout-container">
    <h2 class="mb-4">Checkout</h2>
    
    <div class="row">
        <div class="col-md-7">
            <div class="checkout-form">
                <h4 class="mb-3">Shipping Information</h4>
                <form id="checkoutForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone *</label>
                            <input type="tel" name="phone" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Country</label>
                            <select name="country" class="form-control">
                                <option>Kenya</option><option>Nigeria</option><option>South Africa</option>
                                <option>Ghana</option><option>Egypt</option><option>Morocco</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address *</label>
                        <textarea name="address" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Zip Code</label>
                            <input type="text" name="zip" class="form-control">
                        </div>
                    </div>
                    
                    <h4 class="mb-3 mt-4">Payment Method</h4>
                    <div id="paymentMethods">
                        <div class="payment-method" data-method="paypal">
                            <input type="radio" name="payment_method" value="paypal" id="paypal" checked>
                            <label for="paypal"><i class="fab fa-paypal fa-2x me-2" style="color: #003087;"></i> PayPal</label>
                        </div>
                        <div class="payment-method" data-method="mpesa">
                            <input type="radio" name="payment_method" value="mpesa" id="mpesa">
                            <label for="mpesa"><i class="fas fa-mobile-alt fa-2x me-2" style="color: #4CAF50;"></i> M-Pesa (Kenya)</label>
                            <div id="mpesaDetails" style="display: none; margin-top: 10px;">
                                <input type="tel" name="mpesa_phone" class="form-control" placeholder="Enter M-Pesa phone number (e.g., 2547XXXXXXXX)">
                            </div>
                        </div>
                        <div class="payment-method" data-method="card">
                            <input type="radio" name="payment_method" value="card" id="card">
                            <label for="card"><i class="fab fa-cc-visa fa-2x me-2"></i> <i class="fab fa-cc-mastercard fa-2x"></i> Credit / Debit Card</label>
                            <div id="cardDetails" style="display: none; margin-top: 10px;">
                                <input type="text" class="form-control mb-2" placeholder="Card Number">
                                <div class="row">
                                    <div class="col-6"><input type="text" class="form-control" placeholder="MM/YY"></div>
                                    <div class="col-6"><input type="text" class="form-control" placeholder="CVV"></div>
                                </div>
                            </div>
                        </div>
                        <div class="payment-method" data-method="airtel">
                            <input type="radio" name="payment_method" value="airtel" id="airtel">
                            <label for="airtel"><i class="fas fa-wifi fa-2x me-2" style="color: #E60000;"></i> Airtel Money</label>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn-checkout"><i class="fas fa-lock me-2"></i> Place Order</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="col-md-5">
            <div class="order-summary">
                <h4 class="mb-3">Order Summary</h4>
                <?php foreach($items as $item): ?>
                <div class="d-flex justify-content-between mb-2">
                    <span><?php echo $item['name']; ?> x <?php echo $item['quantity']; ?></span>
                    <span><?php echo formatPrice(convertPrice($item['price_usd'], $currentCurrency) * $item['quantity']); ?></span>
                </div>
                <?php endforeach; ?>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span><?php echo formatPrice($total); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Shipping (Africa-wide):</span>
                    <span><?php echo formatPrice($shipping); ?></span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <strong>Total:</strong>
                    <strong class="h4 text-primary"><?php echo formatPrice($grandTotal); ?></strong>
                </div>
                
                <div class="alert alert-info small">
                    <i class="fas fa-shield-alt"></i> Secure payment processing. Your information is safe with us.
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Payment method selection styling
    $('.payment-method').click(function() {
        $('.payment-method').removeClass('selected');
        $(this).addClass('selected');
        $(this).find('input[type="radio"]').prop('checked', true);
        
        // Show/hide details
        $('#mpesaDetails, #cardDetails').hide();
        if($(this).data('method') === 'mpesa') {
            $('#mpesaDetails').show();
        } else if($(this).data('method') === 'card') {
            $('#cardDetails').show();
        }
    });
    
    // Form submission
    $('#checkoutForm').submit(function(e) {
        e.preventDefault();
        
        // Show loading state
        const $btn = $(this).find('button[type="submit"]');
        const originalText = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop('disabled', true);
        
        // Collect form data
        const formData = {
            name: $('input[name="name"]').val(),
            email: $('input[name="email"]').val(),
            phone: $('input[name="phone"]').val(),
            address: $('textarea[name="address"]').val(),
            city: $('input[name="city"]').val(),
            country: $('select[name="country"]').val(),
            payment_method: $('input[name="payment_method"]:checked').val()
        };
        
        // Validate
        if(!formData.name || !formData.email || !formData.phone || !formData.address) {
            alert('Please fill all required fields');
            $btn.html(originalText).prop('disabled', false);
            return;
        }
        
        // Simulate order processing (in production, send to server)
        setTimeout(function() {
            // Generate random order number
            const orderNumber = 'AFR-' + new Date().getTime() + '-' + Math.floor(Math.random() * 1000);
            
            // Show success message
            alert('Order placed successfully!\nOrder Number: ' + orderNumber + '\n\nThank you for shopping with AfriMart!');
            
            // Redirect to home
            window.location.href = 'index.php';
        }, 1500);
    });
});
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>