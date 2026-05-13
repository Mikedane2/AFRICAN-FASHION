<?php
function getProducts($pdo, $limit = null, $category = null, $search = null, $sort = 'newest') {
    $sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.status = 'active'";
    $params = [];
    
    if($category) {
        $sql .= " AND (p.category_id = ? OR c.parent_id = ?)";
        $params[] = $category;
        $params[] = $category;
    }
    
    if($search) {
        $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.brand LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    switch($sort) {
        case 'price_low': $sql .= " ORDER BY p.price_usd ASC"; break;
        case 'price_high': $sql .= " ORDER BY p.price_usd DESC"; break;
        case 'popular': $sql .= " ORDER BY p.sold_count DESC"; break;
        default: $sql .= " ORDER BY p.created_at DESC";
    }
    
    if($limit !== null && $limit > 0) {
        $sql .= " LIMIT " . intval($limit);
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
    
    foreach($products as &$p) {
        $p['images_array'] = $p['images'] ? json_decode($p['images'], true) : ['placeholder.jpg'];
        if(empty($p['images_array'])) $p['images_array'] = ['placeholder.jpg'];
    }
    return $products;
}

function getProductById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $p = $stmt->fetch();
    if($p) {
        $p['images_array'] = $p['images'] ? json_decode($p['images'], true) : ['placeholder.jpg'];
        if(empty($p['images_array'])) $p['images_array'] = ['placeholder.jpg'];
    }
    return $p;
}

function getProductBySlug($pdo, $slug) {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.slug = ? AND p.status = 'active'");
    $stmt->execute([$slug]);
    $p = $stmt->fetch();
    if($p) {
        $p['images_array'] = $p['images'] ? json_decode($p['images'], true) : ['placeholder.jpg'];
        if(empty($p['images_array'])) $p['images_array'] = ['placeholder.jpg'];
        $upd = $pdo->prepare("UPDATE products SET views = views + 1 WHERE id = ?");
        $upd->execute([$p['id']]);
    }
    return $p;
}

function getCategories($pdo, $parent = null) {
    if($parent === null) {
        $stmt = $pdo->query("SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name");
    } else {
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE parent_id = ? ORDER BY name");
        $stmt->execute([$parent]);
    }
    return $stmt->fetchAll();
}

function getFeaturedProducts($pdo, $limit = 8) {
    $limit = intval($limit);
    $stmt = $pdo->prepare("SELECT * FROM products WHERE status = 'active' AND featured = 1 ORDER BY created_at DESC LIMIT " . $limit);
    $stmt->execute();
    $products = $stmt->fetchAll();
    foreach($products as &$p) {
        $p['images_array'] = $p['images'] ? json_decode($p['images'], true) : ['placeholder.jpg'];
        if(empty($p['images_array'])) $p['images_array'] = ['placeholder.jpg'];
    }
    return $products;
}

function getTrendingProducts($pdo, $limit = 8) {
    $limit = intval($limit);
    $stmt = $pdo->prepare("SELECT * FROM products WHERE status = 'active' AND trending = 1 ORDER BY RAND() LIMIT " . $limit);
    $stmt->execute();
    $products = $stmt->fetchAll();
    foreach($products as &$p) {
        $p['images_array'] = $p['images'] ? json_decode($p['images'], true) : ['placeholder.jpg'];
        if(empty($p['images_array'])) $p['images_array'] = ['placeholder.jpg'];
    }
    return $products;
}

function getNewArrivals($pdo, $limit = 8) {
    $limit = intval($limit);
    $stmt = $pdo->prepare("SELECT * FROM products WHERE status = 'active' AND new_arrival = 1 ORDER BY created_at DESC LIMIT " . $limit);
    $stmt->execute();
    $products = $stmt->fetchAll();
    foreach($products as &$p) {
        $p['images_array'] = $p['images'] ? json_decode($p['images'], true) : ['placeholder.jpg'];
        if(empty($p['images_array'])) $p['images_array'] = ['placeholder.jpg'];
    }
    return $products;
}

function getSaleProducts($pdo, $limit = null) {
    $sql = "SELECT * FROM products WHERE status = 'active' AND compare_price_usd IS NOT NULL AND compare_price_usd > price_usd ORDER BY ((compare_price_usd - price_usd) / compare_price_usd * 100) DESC";
    if($limit !== null && $limit > 0) {
        $sql .= " LIMIT " . intval($limit);
    }
    $stmt = $pdo->query($sql);
    $products = $stmt->fetchAll();
    foreach($products as &$p) {
        $p['images_array'] = $p['images'] ? json_decode($p['images'], true) : ['placeholder.jpg'];
        if(empty($p['images_array'])) $p['images_array'] = ['placeholder.jpg'];
    }
    return $products;
}

function getCartItems($pdo, $sessionId) {
    $stmt = $pdo->prepare("SELECT c.*, p.name, p.price_usd, p.images, p.stock_quantity FROM cart_sessions c JOIN products p ON c.product_id = p.id WHERE c.session_id = ?");
    $stmt->execute([$sessionId]);
    $items = $stmt->fetchAll();
    foreach($items as &$item) {
        $images = $item['images'] ? json_decode($item['images'], true) : ['placeholder.jpg'];
        $item['first_image'] = is_array($images) ? ($images[0] ?? 'placeholder.jpg') : 'placeholder.jpg';
    }
    return $items;
}

function addToCart($pdo, $productId, $quantity, $sessionId, $size = null, $color = null) {
    $stmt = $pdo->prepare("SELECT * FROM cart_sessions WHERE session_id = ? AND product_id = ? AND (size = ? OR (size IS NULL AND ? IS NULL))");
    $stmt->execute([$sessionId, $productId, $size, $size]);
    $existing = $stmt->fetch();
    
    if($existing) {
        $newQty = $existing['quantity'] + $quantity;
        $upd = $pdo->prepare("UPDATE cart_sessions SET quantity = ? WHERE id = ?");
        return $upd->execute([$newQty, $existing['id']]);
    } else {
        $ins = $pdo->prepare("INSERT INTO cart_sessions (session_id, product_id, quantity, size, color) VALUES (?, ?, ?, ?, ?)");
        return $ins->execute([$sessionId, $productId, $quantity, $size, $color]);
    }
}

function updateCartQuantity($pdo, $cartId, $quantity) {
    if($quantity <= 0) {
        $del = $pdo->prepare("DELETE FROM cart_sessions WHERE id = ?");
        return $del->execute([$cartId]);
    } else {
        $upd = $pdo->prepare("UPDATE cart_sessions SET quantity = ? WHERE id = ?");
        return $upd->execute([$quantity, $cartId]);
    }
}

function getCartTotal($pdo, $sessionId, $currency) {
    $items = getCartItems($pdo, $sessionId);
    $totalUSD = 0;
    foreach($items as $item) {
        $totalUSD += $item['price_usd'] * $item['quantity'];
    }
    return convertPrice($totalUSD, $currency);
}

function clearCart($pdo, $sessionId) {
    $del = $pdo->prepare("DELETE FROM cart_sessions WHERE session_id = ?");
    return $del->execute([$sessionId]);
}

function getSessionId() {
    if(!isset($_SESSION['cart_id'])) {
        $_SESSION['cart_id'] = session_id() . '_' . uniqid();
    }
    return $_SESSION['cart_id'];
}

function addToWishlist($pdo, $sessionId, $productId) {
    try {
        $ins = $pdo->prepare("INSERT IGNORE INTO wishlist (session_id, product_id) VALUES (?, ?)");
        return $ins->execute([$sessionId, $productId]);
    } catch(PDOException $e) { 
        return false; 
    }
}

function removeFromWishlist($pdo, $sessionId, $productId) {
    $del = $pdo->prepare("DELETE FROM wishlist WHERE session_id = ? AND product_id = ?");
    return $del->execute([$sessionId, $productId]);
}

function getWishlist($pdo, $sessionId) {
    $stmt = $pdo->prepare("SELECT w.*, p.name, p.price_usd, p.images, p.slug FROM wishlist w JOIN products p ON w.product_id = p.id WHERE w.session_id = ?");
    $stmt->execute([$sessionId]);
    $items = $stmt->fetchAll();
    foreach($items as &$item) {
        $images = $item['images'] ? json_decode($item['images'], true) : ['placeholder.jpg'];
        $item['first_image'] = is_array($images) ? ($images[0] ?? 'placeholder.jpg') : 'placeholder.jpg';
    }
    return $items;
}

function isInWishlist($pdo, $sessionId, $productId) {
    $stmt = $pdo->prepare("SELECT id FROM wishlist WHERE session_id = ? AND product_id = ?");
    $stmt->execute([$sessionId, $productId]);
    return $stmt->fetch() ? true : false;
}

function createOrder($pdo, $cartItems, $data, $paymentMethod, $currency) {
    $orderNum = 'AFR-' . date('Ymd') . '-' . rand(1000, 9999);
    $totalUSD = 0;
    foreach($cartItems as $item) {
        $totalUSD += $item['price_usd'] * $item['quantity'];
    }
    $totalAmount = convertPrice($totalUSD, $currency);
    
    $ins = $pdo->prepare("INSERT INTO orders (order_number, customer_name, customer_email, customer_phone, shipping_address, city, country, total_amount, currency_code, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $ins->execute([$orderNum, $data['name'], $data['email'], $data['phone'], $data['address'], $data['city'] ?? '', $data['country'] ?? '', $totalAmount, $currency, $paymentMethod]);
    $orderId = $pdo->lastInsertId();
    
    foreach($cartItems as $item) {
        $itm = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price_at_time, size, color) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $itm->execute([$orderId, $item['product_id'], $item['name'], $item['quantity'], $item['price_usd'], $item['size'] ?? null, $item['color'] ?? null]);
        $upd = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ?, sold_count = sold_count + ? WHERE id = ?");
        $upd->execute([$item['quantity'], $item['quantity'], $item['product_id']]);
    }
    return $orderNum;
}

function isAdmin() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}
?>