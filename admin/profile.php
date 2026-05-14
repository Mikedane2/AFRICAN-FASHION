<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../config/config.php';
if(!isAdmin()) { header('Location: login.php'); exit; }

$message = '';
$error = '';

$stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
$stmt->execute([$_SESSION['admin_user']]);
$admin = $stmt->fetch();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['update_profile'])) {
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $stmt = $pdo->prepare("UPDATE admin_users SET full_name = ?, email = ? WHERE id = ?");
        if($stmt->execute([$full_name, $email, $admin['id']])) {
            $message = "Profile updated successfully!";
            $admin['full_name'] = $full_name;
            $admin['email'] = $email;
        } else { $error = "Failed to update profile"; }
    }
    
    if(isset($_POST['change_password'])) {
        $current = $_POST['current_password'];
        $new = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];
        if(!password_verify($current, $admin['password'])) {
            $error = "Current password is incorrect";
        } elseif(strlen($new) < 6) {
            $error = "Password must be at least 6 characters";
        } elseif($new !== $confirm) {
            $error = "Passwords do not match";
        } else {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
            if($stmt->execute([$hashed, $admin['id']])) {
                $message = "Password changed successfully!";
            } else { $error = "Failed to change password"; }
        }
    }
    
    if(isset($_POST['change_username'])) {
        $new_username = $_POST['new_username'];
        $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username = ? AND id != ?");
        $stmt->execute([$new_username, $admin['id']]);
        if($stmt->fetch()) {
            $error = "Username already taken";
        } else {
            $stmt = $pdo->prepare("UPDATE admin_users SET username = ? WHERE id = ?");
            if($stmt->execute([$new_username, $admin['id']])) {
                $message = "Username changed! Please login again.";
                $_SESSION['admin_user'] = $new_username;
                $admin['username'] = $new_username;
            } else { $error = "Failed to change username"; }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - AfriMart Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-sidebar { background: #1a1a2e; min-height: 100vh; color: white; position: sticky; top: 0; }
        .admin-sidebar .logo { padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .admin-sidebar .logo h3 { color: #FF9900; margin: 0; }
        .admin-sidebar .nav-link { color: #ddd; padding: 12px 20px; display: flex; align-items: center; }
        .admin-sidebar .nav-link:hover, .admin-sidebar .nav-link.active { background: #FF9900; color: #111; }
        .admin-sidebar .nav-link i { width: 25px; margin-right: 10px; }
        .profile-card { background: white; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .btn-amazon { background: #FF9900; color: #111; font-weight: 600; border: none; padding: 10px 25px; border-radius: 8px; }
        .btn-amazon:hover { background: #ff8c00; }
        @media (max-width: 768px) { .admin-sidebar { min-height: auto; position: relative; } }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 p-0 admin-sidebar">
            <div class="logo text-center"><h3><i class="fas fa-store"></i> AfriMart</h3><p>Admin Panel</p></div>
            <nav class="nav flex-column">
                <a class="nav-link" href="index.php"><i class="fas fa-chart-line"></i> Dashboard</a>
                <a class="nav-link" href="products.php"><i class="fas fa-box"></i> Products</a>
                <a class="nav-link" href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a class="nav-link" href="inventory.php"><i class="fas fa-warehouse"></i> Inventory</a>
                <a class="nav-link" href="ads.php"><i class="fas fa-bullhorn"></i> Ads & Offers</a>
                <a class="nav-link active" href="profile.php"><i class="fas fa-user-cog"></i> Profile</a>
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </div>
        <div class="col-md-10 p-4">
            <h2 class="mb-4"><i class="fas fa-user-cog text-primary"></i> Admin Profile</h2>
            <?php if($message): ?><div class="alert alert-success"><?php echo $message; ?></div><?php endif; ?>
            <?php if($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
            
            <div class="row">
                <div class="col-md-6"><div class="profile-card"><h4><i class="fas fa-user"></i> Profile Information</h4><hr><form method="POST"><div class="mb-3"><label class="fw-bold">Full Name</label><input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($admin['full_name']); ?>" required></div><div class="mb-3"><label class="fw-bold">Email</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($admin['email']); ?>" required></div><div class="mb-3"><label class="fw-bold">Role</label><input type="text" class="form-control" value="<?php echo ucfirst($admin['role']); ?>" disabled></div><button type="submit" name="update_profile" class="btn btn-amazon">Update Profile</button></form></div></div>
                <div class="col-md-6"><div class="profile-card"><h4><i class="fas fa-signature"></i> Change Username</h4><hr><form method="POST"><div class="mb-3"><label class="fw-bold">Current Username</label><input type="text" class="form-control" value="<?php echo htmlspecialchars($admin['username']); ?>" disabled></div><div class="mb-3"><label class="fw-bold">New Username</label><input type="text" name="new_username" class="form-control" required></div><button type="submit" name="change_username" class="btn btn-amazon">Change Username</button></form></div></div>
                <div class="col-md-12"><div class="profile-card"><h4><i class="fas fa-lock"></i> Change Password</h4><hr><form method="POST"><div class="row"><div class="col-md-4"><div class="mb-3"><label class="fw-bold">Current Password</label><input type="password" name="current_password" class="form-control" required></div></div><div class="col-md-4"><div class="mb-3"><label class="fw-bold">New Password</label><input type="password" name="new_password" class="form-control" required><small class="text-muted">Minimum 6 characters</small></div></div><div class="col-md-4"><div class="mb-3"><label class="fw-bold">Confirm Password</label><input type="password" name="confirm_password" class="form-control" required></div></div></div><button type="submit" name="change_password" class="btn btn-amazon">Change Password</button></form></div></div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>