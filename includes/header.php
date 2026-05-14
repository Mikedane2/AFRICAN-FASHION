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
        .top-bar a:hover { text-decoration: underline; }
        
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
        .search-form button:hover {
            background: #ff8c00;
        }
        
        .nav-bar {
            background: var(--amazon-light);
            padding: 8px 0;
        }
        .nav-bar .nav-link {
            color: white !important;
            font-size: 14px;
            padding: 5px 12px !important;
            text-decoration: none;
        }
        .nav-bar .nav-link:hover {
            border: 1px solid white;
            padding: 4px 11px !important;
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
        
        .btn-primary {
            background: var(--amazon-orange);
            border-color: var(--amazon-orange);
            color: #111;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: #ff8c00;
            border-color: #ff8c00;
            color: #111;
        }
        
        @media (max-width: 768px) {
            .logo h2 { font-size: 22px; }
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
                    <button class="btn btn-link dropdown-toggle text-white text-decoration-none" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i> Account
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Sign In</a></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#registerModal">Register</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="wishlist.php">Your Wishlist</a></li>
                        <li><a class="dropdown-item" href="#">Your Orders</a></li>
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
                        <input type="text" name="search" class="form-control" placeholder="Search for dashiki, kente, ankara...">
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
            <a class="nav-link" href="customer-service.php">Customer Service</a>
        </div>
    </div>
</nav>

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