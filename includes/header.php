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
        
        /* Amazon/IKEA Color Scheme */
        :root {
            --amazon-orange: #FF9900;
            --amazon-dark: #232F3E;
            --amazon-light: #37475A;
            --ikea-blue: #0058AB;
            --ikea-yellow: #FFDA1A;
            --gray-bg: #EAEDED;
            --text-dark: #111;
            --text-gray: #565959;
        }
        
        /* Top Bar - Amazon Style */
        .top-bar {
            background: var(--amazon-dark);
            color: white;
            padding: 6px 0;
            font-size: 13px;
        }
        .top-bar a { color: white; text-decoration: none; }
        .top-bar a:hover { text-decoration: underline; }
        
        /* Main Header - Amazon Style */
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
        
        /* Search Bar - Amazon Style */
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
        
        /* Navigation - Amazon Style */
        .nav-bar {
            background: var(--amazon-light);
            padding: 8px 0;
        }
        .nav-bar .nav-link {
            color: white !important;
            font-size: 14px;
            padding: 5px 12px !important;
        }
        .nav-bar .nav-link:hover {
            border: 1px solid white;
            padding: 4px 11px !important;
        }
        
        /* Hero Banner - IKEA Style */
        .hero-banner {
            background: linear-gradient(135deg, var(--ikea-blue) 0%, #003d7a 100%);
            color: white;
            padding: 50px 0;
            margin-bottom: 30px;
        }
        .hero-banner h1 { font-size: 42px; font-weight: 700; margin-bottom: 20px; }
        .hero-banner .btn-primary {
            background: var(--amazon-orange);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            color: #111;
        }
        
        /* Product Card - Amazon Style */
        .product-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            transition: 0.2s;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
        .product-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .product-image {
            height: 220px;
            overflow: hidden;
            position: relative;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
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
        }
        .product-info {
            padding: 15px;
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
        .product-title a:hover {
            color: #C45500;
            text-decoration: underline;
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
        .product-rating {
            color: #FFA41C;
            font-size: 12px;
            margin-bottom: 5px;
        }
        .product-rating span {
            color: #007185;
            margin-left: 5px;
        }
        
        /* Section Title - Amazon Style */
        .section-title {
            margin-bottom: 20px;
            position: relative;
        }
        .section-title h2 {
            font-size: 22px;
            font-weight: 700;
            color: #111;
            margin: 0;
        }
        .section-title a {
            font-size: 14px;
            color: #007185;
            text-decoration: none;
        }
        .section-title a:hover {
            color: #C45500;
            text-decoration: underline;
        }
        
        /* Cart Badge */
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
        
        /* Buttons */
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
        .btn-outline-primary {
            border-color: var(--amazon-orange);
            color: var(--amazon-orange);
        }
        .btn-outline-primary:hover {
            background: var(--amazon-orange);
            border-color: var(--amazon-orange);
            color: #111;
        }
        
        /* Footer - Amazon Style */
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
        .footer a:hover {
            color: white;
            text-decoration: underline;
        }
        
        /* Cart Page */
        .cart-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        .cart-table th {
            background: var(--gray-bg);
            border: none;
        }
        
        /* Checkout */
        .checkout-form {
            background: white;
            padding: 25px;
            border-radius: 8px;
        }
        .order-summary {
            background: white;
            padding: 20px;
            border-radius: 8px;
            position: sticky;
            top: 20px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-banner h1 { font-size: 28px; }
            .product-image { height: 160px; }
        }
    </style>
</head>
<body>

<!-- Top Bar - Amazon Style -->
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
                        <i class="fas fa-user"></i> Hello, Sign in
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">Your Account</a></li>
                        <li><a class="dropdown-item" href="wishlist.php">Your Wishlist</a></li>
                        <li><a class="dropdown-item" href="#">Your Orders</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#">Sign out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Header - Amazon Style -->
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
                        <button class="btn" type="submit"><i class="fas fa-search"></i></button>
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

<!-- Navigation - Amazon Style -->
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