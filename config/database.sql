-- DROP AND CREATE DATABASE
DROP DATABASE IF EXISTS african_fashion_db;
CREATE DATABASE african_fashion_db;
USE african_fashion_db;

-- CATEGORIES TABLE
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    parent_id INT DEFAULT NULL,
    featured BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO categories (name, slug, featured) VALUES
('Men\'s Fashion', 'mens-fashion', 1),
('Women\'s Fashion', 'womens-fashion', 1),
('Shoes', 'shoes', 1),
('Bags', 'bags', 1),
('Accessories', 'accessories', 1),
('Traditional Wear', 'traditional-wear', 1);

-- ALL AFRICAN CURRENCIES (54 COUNTRIES)
CREATE TABLE currencies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    country_code VARCHAR(5) UNIQUE NOT NULL,
    country_name VARCHAR(100) NOT NULL,
    currency_code VARCHAR(3) NOT NULL,
    currency_symbol VARCHAR(10) NOT NULL,
    currency_name VARCHAR(50) NOT NULL,
    rate_to_usd DECIMAL(15,6) NOT NULL,
    is_default BOOLEAN DEFAULT 0
);

INSERT INTO currencies (country_code, country_name, currency_code, currency_symbol, currency_name, rate_to_usd, is_default) VALUES
('DZ', 'Algeria', 'DZD', 'د.ج', 'Algerian Dinar', 134.500000, 0),
('AO', 'Angola', 'AOA', 'Kz', 'Angolan Kwanza', 828.000000, 0),
('BJ', 'Benin', 'XOF', 'CFA', 'West African CFA', 610.000000, 0),
('BW', 'Botswana', 'BWP', 'P', 'Botswana Pula', 13.500000, 0),
('BF', 'Burkina Faso', 'XOF', 'CFA', 'West African CFA', 610.000000, 0),
('BI', 'Burundi', 'BIF', 'FBu', 'Burundian Franc', 2840.000000, 0),
('CV', 'Cabo Verde', 'CVE', 'Esc', 'Cape Verdean Escudo', 101.000000, 0),
('CM', 'Cameroon', 'XAF', 'FCFA', 'Central African CFA', 610.000000, 0),
('CF', 'Central African Republic', 'XAF', 'FCFA', 'Central African CFA', 610.000000, 0),
('TD', 'Chad', 'XAF', 'FCFA', 'Central African CFA', 610.000000, 0),
('KM', 'Comoros', 'KMF', 'CF', 'Comorian Franc', 460.000000, 0),
('CG', 'Congo', 'XAF', 'FCFA', 'Central African CFA', 610.000000, 0),
('CD', 'DR Congo', 'CDF', 'FC', 'Congolese Franc', 2500.000000, 0),
('CI', 'Ivory Coast', 'XOF', 'CFA', 'West African CFA', 610.000000, 0),
('DJ', 'Djibouti', 'DJF', 'Fdj', 'Djiboutian Franc', 177.500000, 0),
('EG', 'Egypt', 'EGP', 'E£', 'Egyptian Pound', 48.500000, 0),
('GQ', 'Equatorial Guinea', 'XAF', 'FCFA', 'Central African CFA', 610.000000, 0),
('ER', 'Eritrea', 'ERN', 'Nfk', 'Eritrean Nakfa', 15.000000, 0),
('SZ', 'Eswatini', 'SZL', 'L', 'Swazi Lilangeni', 18.500000, 0),
('ET', 'Ethiopia', 'ETB', 'Br', 'Ethiopian Birr', 56.000000, 0),
('GA', 'Gabon', 'XAF', 'FCFA', 'Central African CFA', 610.000000, 0),
('GM', 'Gambia', 'GMD', 'D', 'Gambian Dalasi', 68.000000, 0),
('GH', 'Ghana', 'GHS', '₵', 'Ghanaian Cedi', 15.200000, 0),
('GN', 'Guinea', 'GNF', 'FG', 'Guinean Franc', 8600.000000, 0),
('GW', 'Guinea-Bissau', 'XOF', 'CFA', 'West African CFA', 610.000000, 0),
('KE', 'Kenya', 'KES', 'KSh', 'Kenyan Shilling', 130.500000, 0),
('LS', 'Lesotho', 'LSL', 'L', 'Lesotho Loti', 18.500000, 0),
('LR', 'Liberia', 'LRD', 'L$', 'Liberian Dollar', 185.000000, 0),
('LY', 'Libya', 'LYD', 'LD', 'Libyan Dinar', 4.800000, 0),
('MG', 'Madagascar', 'MGA', 'Ar', 'Malagasy Ariary', 4450.000000, 0),
('MW', 'Malawi', 'MWK', 'MK', 'Malawian Kwacha', 1060.000000, 0),
('ML', 'Mali', 'XOF', 'CFA', 'West African CFA', 610.000000, 0),
('MR', 'Mauritania', 'MRU', 'UM', 'Mauritanian Ouguiya', 37.000000, 0),
('MU', 'Mauritius', 'MUR', '₨', 'Mauritian Rupee', 45.000000, 0),
('MA', 'Morocco', 'MAD', 'DH', 'Moroccan Dirham', 10.000000, 0),
('MZ', 'Mozambique', 'MZN', 'MT', 'Mozambican Metical', 64.000000, 0),
('NA', 'Namibia', 'NAD', 'N$', 'Namibian Dollar', 18.500000, 0),
('NE', 'Niger', 'XOF', 'CFA', 'West African CFA', 610.000000, 0),
('NG', 'Nigeria', 'NGN', '₦', 'Nigerian Naira', 1500.000000, 0),
('RW', 'Rwanda', 'RWF', 'FRw', 'Rwandan Franc', 1300.000000, 0),
('ST', 'Sao Tome', 'STN', 'Db', 'Sao Tome Dobra', 23.000000, 0),
('SN', 'Senegal', 'XOF', 'CFA', 'West African CFA', 610.000000, 0),
('SC', 'Seychelles', 'SCR', '₨', 'Seychellois Rupee', 13.500000, 0),
('SL', 'Sierra Leone', 'SLE', 'Le', 'Sierra Leonean Leone', 22.000000, 0),
('SO', 'Somalia', 'SOS', 'Sh', 'Somali Shilling', 570.000000, 0),
('ZA', 'South Africa', 'ZAR', 'R', 'South African Rand', 18.500000, 0),
('SS', 'South Sudan', 'SSP', '£', 'South Sudanese Pound', 300.000000, 0),
('SD', 'Sudan', 'SDG', 'ج.س', 'Sudanese Pound', 600.000000, 0),
('TZ', 'Tanzania', 'TZS', 'TSh', 'Tanzanian Shilling', 2600.000000, 0),
('TG', 'Togo', 'XOF', 'CFA', 'West African CFA', 610.000000, 0),
('TN', 'Tunisia', 'TND', 'DT', 'Tunisian Dinar', 3.100000, 0),
('UG', 'Uganda', 'UGX', 'USh', 'Ugandan Shilling', 3800.000000, 0),
('ZM', 'Zambia', 'ZMW', 'ZK', 'Zambian Kwacha', 21.500000, 0),
('ZW', 'Zimbabwe', 'USD', '$', 'US Dollar', 1.000000, 1);

-- PRODUCTS TABLE
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    short_description VARCHAR(500),
    price_usd DECIMAL(10,2) NOT NULL,
    compare_price_usd DECIMAL(10,2) DEFAULT NULL,
    category_id INT,
    brand VARCHAR(100),
    sizes VARCHAR(200),
    colors VARCHAR(200),
    stock_quantity INT DEFAULT 0,
    sku VARCHAR(100) UNIQUE,
    images TEXT,
    featured BOOLEAN DEFAULT 0,
    trending BOOLEAN DEFAULT 0,
    best_seller BOOLEAN DEFAULT 0,
    new_arrival BOOLEAN DEFAULT 0,
    sold_count INT DEFAULT 0,
    views INT DEFAULT 0,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- ORDERS TABLE
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(50),
    shipping_address TEXT,
    city VARCHAR(100),
    country VARCHAR(100),
    total_amount DECIMAL(10,2),
    currency_code VARCHAR(3),
    payment_method VARCHAR(50),
    payment_status ENUM('pending','completed','failed') DEFAULT 'pending',
    order_status ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
    tracking_number VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ORDER ITEMS TABLE
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    product_name VARCHAR(255),
    quantity INT,
    price_at_time DECIMAL(10,2),
    size VARCHAR(50),
    color VARCHAR(50),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- CART SESSIONS TABLE
CREATE TABLE cart_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    product_id INT,
    quantity INT DEFAULT 1,
    size VARCHAR(50),
    color VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_session_id (session_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- WISHLIST TABLE
CREATE TABLE wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    product_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_wishlist (session_id, product_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ADMIN USERS TABLE
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    full_name VARCHAR(100),
    role ENUM('super_admin','admin','manager') DEFAULT 'admin',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- DEFAULT ADMIN (password: admin123)
INSERT INTO admin_users (username, password, email, full_name, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@africanfashion.com', 'Super Administrator', 'super_admin');

-- SAMPLE PRODUCTS
INSERT INTO products (name, slug, description, short_description, price_usd, compare_price_usd, category_id, brand, sizes, colors, stock_quantity, sku, images, featured, trending, best_seller, new_arrival) VALUES
('African Dashiki Shirt', 'african-dashiki-shirt', 'Traditional African dashiki shirt made from premium cotton fabric. Perfect for weddings, parties, and cultural events.', 'Traditional African dashiki - premium quality', 49.99, 89.99, 6, 'AfriTrend', 'S,M,L,XL,XXL', 'Red,Gold,Blue,Green', 200, 'DSH001', '["dashiki.jpg"]', 1, 1, 1, 1),
('Ankara Maxi Dress', 'ankara-maxi-dress', 'Beautiful Ankara print maxi dress with modern African design. Comfortable and stylish for any occasion.', 'Ankara print maxi dress', 59.99, 99.99, 2, 'AnkaraQueen', 'XS,S,M,L,XL', 'Multi,Red,Blue,Yellow', 150, 'ANK001', '["ankara-dress.jpg"]', 1, 1, 1, 1),
('Kente Print Shirt', 'kente-print-shirt', 'Vibrant Kente pattern shirt, handcrafted with authentic African design.', 'Kente pattern - handcrafted', 44.99, 79.99, 1, 'KenteKing', 'S,M,L,XL', 'Gold,Green,Red,Blue', 120, 'KNT001', '["kente-shirt.jpg"]', 1, 1, 0, 1),
('Beaded Sandals', 'beaded-sandals', 'Hand-beaded leather sandals with traditional African patterns.', 'Hand-beaded leather sandals', 34.99, 59.99, 3, 'AfriFoot', '36,37,38,39,40,41,42', 'Brown,Black,Multi', 100, 'SND001', '["sandals.jpg"]', 1, 0, 1, 0),
('African Print Bag', 'african-print-bag', 'Stylish tote bag with vibrant African print design.', 'African print tote bag', 29.99, 49.99, 4, 'BagAfri', 'One Size', 'Multi,Red,Blue', 80, 'BAG001', '["bag.jpg"]', 0, 1, 0, 1),
('Beaded Necklace', 'beaded-necklace', 'Traditional African beaded necklace made by local artisans.', 'Handmade beaded necklace', 19.99, 39.99, 5, 'AfriJewel', 'One Size', 'Multi,Gold,Red', 300, 'JWL001', '["necklace.jpg"]', 0, 1, 0, 1);