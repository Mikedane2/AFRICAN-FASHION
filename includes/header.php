<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - African Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f5f5f5; }
        
        :root {
            --amazon-orange: #FF9900;
            --amazon-dark: #232F3E;
            --amazon-light: #37475A;
        }
        
        .top-bar {
            background: var(--amazon-dark);
            color: white;
            padding: 6px 0;
            font-size: 13px;
        }
        .top-bar a { color: white; text-decoration: none; }
        
        .main-header {
            background: var(--amazon-dark);
            padding: 12px 0;
        }
        .logo h2 {
            margin: 0;
            color: white;
            font-size: 28px;
            font-weight: 700;
        }
        .logo span { color: var(--amazon-orange); }
        
        .search-form .input-group {
            box-shadow: none;
        }
        .search-form input {
            border: none;
            height: 45px;
            font-size: 14px;
        }
        .search-form button {
            background: var(--amazon-orange);
            border: none;
            height: 45px;
            color: #111;
            font-weight: 600;
        }
        
        .nav-bar {
            background: var(--amazon-light);
            padding: 8px 0;
        }
        .nav-bar .nav-link {
            color: white !important;
            font-size: 14px;
            padding: 5px 12px !important;
        }
        
        /* Fixed Product Card */
        .product-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            transition: 0.2s;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            height: 100%;
        }
        .product-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .product-image {
            height: 200px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            padding: 15px;
        }
        .product-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .product-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #CC0C39;
            color: white;
            padding: 4px 8px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 4px;
            z-index: 1;
        }
        .product-info {
            padding: 12px;
            border-top: 1px solid #eee;
        }
        .product-title {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            height: 40px;
            overflow: hidden;
        }
        .product-title a {
            color: #007185;
            text-decoration: none;
        }
        .product-price {
            font-size: 18px;
            font-weight: 700;
            color: #B12704;
        }
        .product-old-price {
            font-size: 13px;
            color: #565959;
            text-decoration: line-through;
            margin-left: 8px;
            font-weight: 400;
        }
        .cart-count-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #CC0C39;
            color: white;
            border-radius: 50%;
            font-size: 10px;
            padding: 2px 6px;
            min-width: 18px;
            text-align: center;
        }
        
        /* Fixed Filter Bar - Moved to sidebar */
        .filter-sidebar {
            background: white;
            border-radius: 8px;
            padding: 20px;
            position: sticky;
            top: 20px;
        }
        .filter-sidebar h5 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .footer {
            background: var(--amazon-dark);
            color: #ddd;
            padding: 40px 0 20px;
            margin-top: 50px;
        }
        .footer h5 {
            color: white;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        .footer a {
            color: #ddd;
            text-decoration: none;
            font-size: 13px;
        }
        
        @media (max-width: 768px) {
            .product-image { height: 150px; }
            .filter-sidebar { position: relative; top: 0; margin-bottom: 20px; }
        }
    </style>
</head>
<body>

<div class="top-bar">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <span><i class="fas fa-truck"></i> Free shipping across Africa</span>
                <span class="ms-3"><i class="fas fa-undo"></i> 30-day returns</span>
            </div>
            <div class="col-md-6 text-end">
                <div class="dropdown d-inline-block">
                    <button class="btn btn-link dropdown-toggle text-white text-decoration-none" data-bs-toggle="dropdown">
                        <i class="fas fa-money-bill-wave"></i> <?php echo $currentCurrency; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="max-height: 350px; overflow-y: auto;">
                        <?php $currencies = getAllAfricanCurrencies(); ?>
                        <?php foreach($currencies as $c): ?>
                        <li><a class="dropdown-item change-currency" data-currency="<?php echo $c['currency_code']; ?>" href="#"><?php echo $c['currency_code']; ?> - <?php echo $c['country_name']; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="dropdown d-inline-block ms-3">
                    <button class="btn btn-link dropdown-toggle text-white text-decoration-none" data-bs-toggle="dropdown" id="accountDropdown">
                        <i class="fas fa-user"></i> Account
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#loginModal"><i class="fas fa-sign-in-alt"></i> Sign In</a></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#registerModal"><i class="fas fa-user-plus"></i> Register</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="wishlist.php"><i class="fas fa-heart"></i> Your Wishlist</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-box"></i> Your Orders</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<header class="main-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-3">
                <a href="index.php" class="logo text-decoration-none">
                    <h2><i class="fas fa-store"></i> Afri<span>Mart</span></h2>
                </a>
            </div>
            <div class="col-md-6">
                <form class="search-form" action="shop.php" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search products...">
                        <button class="btn" type="submit"><i class="fas fa-search"></i> Search</button>
                    </div>
                </form>
            </div>
            <div class="col-md-3 text-end">
                <a href="wishlist.php" class="btn btn-outline-light me-2 position-relative">
                    <i class="far fa-heart"></i> Wishlist
                    <span id="wishlist-count" class="cart-count-badge">0</span>
                </a>
                <a href="cart.php" class="btn btn-warning position-relative">
                    <i class="fas fa-shopping-cart"></i> Cart
                    <span id="cart-count" class="cart-count-badge"><?php echo $cartCount; ?></span>
                </a>
            </div>
        </div>
    </div>
</header>

<nav class="nav-bar">
    <div class="container">
        <div class="d-flex flex-wrap">
            <a class="nav-link" href="index.php">Home</a>
            <a class="nav-link" href="shop.php">Best Sellers</a>
            <a class="nav-link" href="shop.php">Today's Deals</a>
            <a class="nav-link" href="shop.php">New Arrivals</a>
            <a class="nav-link" href="sale.php">Sale</a>
            <a class="nav-link" href="#">Customer Service</a>
        </div>
    </div>
</nav>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="fas fa-sign-in-alt"></i> Sign In</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="loginForm">
                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="email" class="form-control" id="loginEmail" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" id="loginPassword" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Sign In</button>
                </form>
                <div class="text-center mt-3">
                    <small>New to AfriMart? <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#registerModal">Create an account</a></small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Register Modal -->
<div class="modal fade" id="registerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="fas fa-user-plus"></i> Create Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="registerForm">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="regName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="email" class="form-control" id="regEmail" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="regPhone" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" id="regPassword" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Create Account</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Account Modal JavaScript
document.getElementById('loginForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Demo: Login functionality would connect to database');
    bootstrap.Modal.getInstance(document.getElementById('loginModal')).hide();
});

document.getElementById('registerForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Demo: Registration functionality would connect to database');
    bootstrap.Modal.getInstance(document.getElementById('registerModal')).hide();
});
</script>
<script>
$(document).ready(function() {
    $('.add-to-cart').click(function(e) {
        e.preventDefault();
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