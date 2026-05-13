<?php
session_start();
require_once '../config/config.php';
if(isAdmin()) { header('Location: index.php'); exit; }
$error = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$_POST['username']]);
    $user = $stmt->fetch();
    if($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $user['username'];
        $_SESSION['admin_role'] = $user['role'];
        $upd = $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
        $upd->execute([$user['id']]);
        header('Location: index.php');
        exit;
    } else { $error = "Invalid username or password"; }
}
?>
<!DOCTYPE html>
<html><head><title>Admin Login - African Fashion</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"><style>body{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);height:100vh;}.login-card{margin-top:100px;border-radius:15px;box-shadow:0 10px 40px rgba(0,0,0,0.2);}</style></head>
<body><div class="container"><div class="row justify-content-center"><div class="col-md-4"><div class="card login-card"><div class="card-header bg-dark text-white text-center"><h4><i class="fas fa-tshirt"></i> African Fashion Admin</h4></div><div class="card-body"><?php if($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?><form method="POST"><div class="mb-3"><label>Username</label><input type="text" name="username" class="form-control" required></div><div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div><button type="submit" class="btn btn-dark w-100">Login</button></form><div class="mt-3 text-muted small text-center">Default: admin / admin123</div></div></div></div></div></div></body>
</html>