<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function getCountryFromIP() {
    $ip = $_SERVER['REMOTE_ADDR'];
    if($ip == '127.0.0.1' || $ip == '::1') {
        return 'KE';
    }
    return 'KE';
}

function getCurrencyByCountry($countryCode) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT currency_code, currency_symbol, rate_to_usd FROM currencies WHERE country_code = ? LIMIT 1");
    $stmt->execute([$countryCode]);
    $result = $stmt->fetch();
    if($result) {
        return $result;
    }
    return ['currency_code' => 'USD', 'currency_symbol' => '$', 'rate_to_usd' => 1];
}

function convertPrice($priceUSD, $targetCurrency) {
    global $pdo;
    if(!$priceUSD) return 0;
    if($targetCurrency == 'USD') return round($priceUSD, 2);
    $stmt = $pdo->prepare("SELECT rate_to_usd FROM currencies WHERE currency_code = ?");
    $stmt->execute([$targetCurrency]);
    $c = $stmt->fetch();
    if($c) {
        return round($priceUSD * $c['rate_to_usd'], 2);
    }
    return round($priceUSD, 2);
}

function getCurrencySymbol($code) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT currency_symbol FROM currencies WHERE currency_code = ?");
    $stmt->execute([$code]);
    $c = $stmt->fetch();
    return $c ? $c['currency_symbol'] : '$';
}

function formatPrice($amount, $currency = null) {
    global $currentCurrency;
    $curr = $currency ?? $currentCurrency;
    $symbol = getCurrencySymbol($curr);
    
    $currenciesWithoutDecimals = ['KES', 'TZS', 'UGX', 'BIF', 'DJF', 'GNF', 'RWF'];
    if(in_array($curr, $currenciesWithoutDecimals)) {
        return $symbol . " " . number_format($amount, 0);
    }
    if($curr == 'NGN') {
        return $symbol . number_format($amount, 0);
    }
    return $symbol . number_format($amount, 2);
}

function getAllAfricanCurrencies() {
    global $pdo;
    $stmt = $pdo->query("SELECT country_code, country_name, currency_code, currency_symbol FROM currencies ORDER BY country_name");
    return $stmt->fetchAll();
}

function getUserCurrency() {
    if(isset($_COOKIE['preferred_currency'])) {
        return $_COOKIE['preferred_currency'];
    }
    $countryCode = getCountryFromIP();
    $currency = getCurrencyByCountry($countryCode);
    return $currency['currency_code'];
}

if(!isset($_SESSION['user_currency'])) {
    $_SESSION['user_currency'] = getUserCurrency();
}

if(isset($_POST['change_currency'])) {
    $_SESSION['user_currency'] = $_POST['change_currency'];
    setcookie('preferred_currency', $_POST['change_currency'], time() + 86400 * 30, '/');
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
}

$currentCurrency = $_SESSION['user_currency'];
?>