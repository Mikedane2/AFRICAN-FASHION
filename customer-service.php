<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config/config.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Service - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f5f5f5; }
        .help-center { background: linear-gradient(135deg, #232F3E 0%, #37475A 100%); color: white; padding: 60px 0; text-align: center; }
        .help-center h1 { font-size: 48px; font-weight: 700; margin-bottom: 20px; }
        .search-box { max-width: 600px; margin: 0 auto; }
        .search-box input { height: 50px; border-radius: 8px 0 0 8px; border: none; }
        .search-box button { background: #FF9900; border: none; border-radius: 0 8px 8px 0; padding: 0 30px; color: #111; font-weight: 600; }
        .faq-card { background: white; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: 0.3s; }
        .faq-card:hover { box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .contact-card { background: white; border-radius: 12px; padding: 30px; text-align: center; height: 100%; transition: 0.3s; }
        .contact-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .contact-card i { font-size: 48px; color: #FF9900; margin-bottom: 20px; }
        .live-chat-btn { background: #FF9900; color: #111; padding: 12px 30px; border-radius: 8px; font-weight: 600; border: none; }
        .track-order { background: white; border-radius: 12px; padding: 30px; margin-top: 30px; }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="help-center">
    <div class="container">
        <h1>How can we help you?</h1>
        <p class="lead">Find answers to common questions or contact our support team</p>
        <div class="search-box mt-4">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search for answers..." id="faqSearch">
                <button class="btn" onclick="searchFaqs()"><i class="fas fa-search"></i> Search</button>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="faq-section py-5">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2>Frequently Asked Questions</h2>
                <p class="text-muted">Quick answers to common questions</p>
            </div>
        </div>
        <div class="row" id="faqContainer">
            <div class="col-md-6"><div class="faq-card"><h4><i class="fas fa-truck text-primary me-2"></i> Shipping & Delivery</h4><p>We ship to all 54 African countries. Standard shipping takes 3-7 business days.</p><a href="#" data-bs-toggle="modal" data-bs-target="#faqModal1" class="text-decoration-none">Read More <i class="fas fa-arrow-right"></i></a></div></div>
            <div class="col-md-6"><div class="faq-card"><h4><i class="fas fa-undo-alt text-primary me-2"></i> Returns & Refunds</h4><p>30-day return policy for all products. Learn about our hassle-free return process.</p><a href="#" data-bs-toggle="modal" data-bs-target="#faqModal2" class="text-decoration-none">Read More <i class="fas fa-arrow-right"></i></a></div></div>
            <div class="col-md-6"><div class="faq-card"><h4><i class="fas fa-credit-card text-primary me-2"></i> Payment Methods</h4><p>We accept PayPal, M-Pesa, Airtel Money, Credit/Debit cards, and bank transfers.</p><a href="#" data-bs-toggle="modal" data-bs-target="#faqModal3" class="text-decoration-none">Read More <i class="fas fa-arrow-right"></i></a></div></div>
            <div class="col-md-6"><div class="faq-card"><h4><i class="fas fa-box text-primary me-2"></i> Order Tracking</h4><p>Track your order status in real-time. Enter your order number below.</p><a href="#" data-bs-toggle="modal" data-bs-target="#faqModal4" class="text-decoration-none">Read More <i class="fas fa-arrow-right"></i></a></div></div>
        </div>
    </div>

    <div class="track-order mb-5">
        <div class="row align-items-center">
            <div class="col-md-6"><h3><i class="fas fa-search-location text-primary"></i> Track Your Order</h3><p>Enter your order number to get real-time status updates</p><div class="input-group"><input type="text" id="trackOrderNumber" class="form-control" placeholder="e.g., AFR-20241201-1234"><button class="btn btn-primary" onclick="trackOrder()">Track Order</button></div><div id="trackResult" class="mt-3"></div></div>
            <div class="col-md-6 text-center"><i class="fas fa-shipping-fast fa-4x text-muted"></i></div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-12 text-center mb-4"><h2>Contact Us</h2><p>Choose your preferred way to reach us</p></div>
        <div class="col-md-4 mb-3"><div class="contact-card"><i class="fas fa-comments"></i><h4>Live Chat</h4><p>Chat with our support team<br>Available 24/7</p><button class="live-chat-btn" onclick="startLiveChat()">Start Chat</button></div></div>
        <div class="col-md-4 mb-3"><div class="contact-card"><i class="fas fa-envelope"></i><h4>Email Support</h4><p>Get response within 24 hours</p><a href="mailto:support@afrimart.com" class="btn btn-outline-primary mt-3">support@afrimart.com</a></div></div>
        <div class="col-md-4 mb-3"><div class="contact-card"><i class="fas fa-phone-alt"></i><h4>Phone Support</h4><p>Mon-Fri, 9AM - 6PM</p><h4 class="text-primary">+254 700 000 000</h4></div></div>
    </div>
</div>

<div class="modal fade" id="faqModal1" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header bg-dark text-white"><h5>Shipping & Delivery</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body"><p>We ship to all 54 African countries. Standard shipping takes 3-7 business days. Express shipping (1-3 days) available at checkout.</p><strong>Shipping Costs:</strong><ul><li>Standard: Calculated at checkout</li><li>Express: Additional fee applies</li><li>Free shipping on orders over KSh 5,000 equivalent</li></ul></div></div></div></div>
<div class="modal fade" id="faqModal2" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header bg-dark text-white"><h5>Returns & Refunds</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body"><p>30-Day Return Policy: You can return any item within 30 days of delivery for a full refund. Items must be unused and in original packaging.</p><strong>How to Return:</strong><ol><li>Contact our support team</li><li>Print return label</li><li>Ship item back</li><li>Refund processed within 5-7 days</li></ol></div></div></div></div>
<div class="modal fade" id="faqModal3" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header bg-dark text-white"><h5>Payment Methods</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body"><p><strong>Accepted Payment Methods:</strong></p><ul><li>PayPal</li><li>M-Pesa (Kenya)</li><li>Airtel Money</li><li>Visa/Mastercard</li><li>Bank Transfer</li></ul><p>All payments are secure and encrypted.</p></div></div></div></div>
<div class="modal fade" id="faqModal4" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header bg-dark text-white"><h5>Order Tracking</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body"><p><strong>How to track your order:</strong></p><ol><li>Log into your account</li><li>Go to "My Orders"</li><li>Click "Track Order"</li><li>Or use the tracking number in shipping email</li></ol></div></div></div></div>

<script>
function searchFaqs() { let term = document.getElementById('faqSearch').value.toLowerCase(); document.querySelectorAll('.faq-card').forEach(card => { card.style.display = card.innerText.toLowerCase().includes(term) ? 'block' : 'none'; }); }
function trackOrder() { let orderNum = document.getElementById('trackOrderNumber').value; if(orderNum) { document.getElementById('trackResult').innerHTML = `<div class="alert alert-success"><i class="fas fa-check-circle"></i> Order ${orderNum} is being processed.</div>`; } else { document.getElementById('trackResult').innerHTML = `<div class="alert alert-warning">Please enter an order number</div>`; } }
function startLiveChat() { alert('Live chat support is available 24/7. A representative will assist you shortly.'); }
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>