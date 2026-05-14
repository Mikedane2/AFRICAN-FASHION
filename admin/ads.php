<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../config/config.php';
if(!isAdmin()) { header('Location: login.php'); exit; }

// Add/Edit Ad
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $discount = intval($_POST['discount']);
    $display_order = intval($_POST['display_order']);
    $active = isset($_POST['active']) ? 1 : 0;
    $start_date = $_POST['start_date'] ?: null;
    $end_date = $_POST['end_date'] ?: null;
    
    $uploadDir = '../assets/uploads/ads/';
    if(!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
    
    $image = '';
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if(in_array($ext, $allowed)) {
            $image = time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
        }
    }
    
    if(isset($_POST['ad_id']) && $_POST['ad_id'] > 0) {
        $id = intval($_POST['ad_id']);
        if($image) {
            $stmt = $pdo->prepare("UPDATE ads SET title=?, description=?, image=?, type=?, discount_percentage=?, display_order=?, active=?, start_date=?, end_date=? WHERE id=?");
            $stmt->execute([$title, $description, $image, $type, $discount, $display_order, $active, $start_date, $end_date, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE ads SET title=?, description=?, type=?, discount_percentage=?, display_order=?, active=?, start_date=?, end_date=? WHERE id=?");
            $stmt->execute([$title, $description, $type, $discount, $display_order, $active, $start_date, $end_date, $id]);
        }
        header('Location: ads.php?msg=updated');
    } else {
        $stmt = $pdo->prepare("INSERT INTO ads (title, description, image, type, discount_percentage, display_order, active, start_date, end_date) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$title, $description, $image, $type, $discount, $display_order, $active, $start_date, $end_date]);
        header('Location: ads.php?msg=added');
    }
    exit;
}

// Delete Ad
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("SELECT image FROM ads WHERE id = ?");
    $stmt->execute([$id]);
    $ad = $stmt->fetch();
    if($ad && $ad['image'] && file_exists('../assets/uploads/ads/'.$ad['image'])) {
        unlink('../assets/uploads/ads/'.$ad['image']);
    }
    $stmt = $pdo->prepare("DELETE FROM ads WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: ads.php?msg=deleted');
    exit;
}

// Toggle Ad Status
if(isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $stmt = $pdo->prepare("UPDATE ads SET active = NOT active WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: ads.php');
    exit;
}

$ads = $pdo->query("SELECT * FROM ads ORDER BY display_order ASC, id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ads & Offers - AfriMart Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-sidebar { background: #1a1a2e; min-height: 100vh; color: white; position: sticky; top: 0; }
        .admin-sidebar .logo { padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .admin-sidebar .logo h3 { color: #FF9900; margin: 0; }
        .admin-sidebar .nav-link { color: #ddd; padding: 12px 20px; display: flex; align-items: center; }
        .admin-sidebar .nav-link:hover, .admin-sidebar .nav-link.active { background: #FF9900; color: #111; }
        .admin-sidebar .nav-link i { width: 25px; margin-right: 10px; }
        .data-table { background: white; border-radius: 12px; overflow: hidden; }
        .data-table th { background: #232F3E; color: white; padding: 12px; }
        .data-table td { padding: 12px; vertical-align: middle; }
        .btn-amazon { background: #FF9900; color: #111; font-weight: 600; border: none; padding: 8px 20px; border-radius: 8px; }
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
                <a class="nav-link active" href="ads.php"><i class="fas fa-bullhorn"></i> Ads & Offers</a>
                <a class="nav-link" href="profile.php"><i class="fas fa-user-cog"></i> Profile</a>
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </div>
        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between mb-4"><h2><i class="fas fa-bullhorn text-primary"></i> Ads & Offers</h2><button class="btn btn-amazon" data-bs-toggle="modal" data-bs-target="#adModal" onclick="resetForm()"><i class="fas fa-plus"></i> Create Ad</button></div>
            <?php if(isset($_GET['msg'])): ?><div class="alert alert-success">Ad <?php echo $_GET['msg']; ?> successfully!</div><?php endif; ?>
            <div class="data-table"><table class="table table-hover mb-0"><thead><tr><th>Image</th><th>Title</th><th>Type</th><th>Discount</th><th>Status</th><th>Order</th><th>Actions</th></tr></thead><tbody><?php foreach($ads as $ad): ?><tr><td><?php if($ad['image'] && file_exists('../assets/uploads/ads/'.$ad['image'])): ?><img src="../assets/uploads/ads/<?php echo $ad['image']; ?>" width="50" height="50" style="object-fit:cover; border-radius:8px;"><?php else: ?><div style="width:50px;height:50px;background:#f0f0f0;border-radius:8px;"></div><?php endif; ?></td><td><strong><?php echo htmlspecialchars($ad['title']); ?></strong><br><small><?php echo substr($ad['description'], 0, 50); ?></small></td><td><span class="badge bg-secondary"><?php echo ucfirst($ad['type']); ?></span></td><td><?php echo $ad['discount_percentage'] > 0 ? '<span class="badge bg-danger">-'.$ad['discount_percentage'].'%</span>' : '-'; ?></td><td><span class="badge bg-'.($ad['active'] ? 'success' : 'secondary').'"><?php echo $ad['active'] ? 'Active' : 'Inactive'; ?></span></td><td><?php echo $ad['display_order']; ?></td><td><button class="btn btn-sm btn-warning" onclick='editAd(<?php echo json_encode($ad); ?>)'><i class="fas fa-edit"></i></button><a href="?toggle=<?php echo $ad['id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-<?php echo $ad['active'] ? 'pause' : 'play'; ?>"></i></a><a href="?delete=<?php echo $ad['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this ad?')"><i class="fas fa-trash"></i></a></td></tr><?php endforeach; ?></tbody></table></div>
        </div>
    </div>
</div>

<div class="modal fade" id="adModal" tabindex="-1" data-bs-backdrop="static"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header bg-dark text-white"><h5 class="modal-title" id="modalTitle">Create Ad</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><form method="POST" enctype="multipart/form-data"><div class="modal-body"><input type="hidden" name="ad_id" id="ad_id"><div class="row"><div class="col-md-8"><div class="mb-3"><label class="fw-bold">Title *</label><input type="text" name="title" id="title" class="form-control" required></div><div class="mb-3"><label class="fw-bold">Description</label><textarea name="description" id="description" class="form-control" rows="3"></textarea></div><div class="row"><div class="col-md-6"><div class="mb-3"><label class="fw-bold">Ad Type</label><select name="type" id="type" class="form-control"><option value="hero">Hero Banner</option><option value="banner">Standard Banner</option><option value="sidebar">Sidebar Ad</option><option value="popup">Popup Ad</option></select></div></div><div class="col-md-6"><div class="mb-3"><label class="fw-bold">Display Order</label><input type="number" name="display_order" id="display_order" class="form-control" value="0"></div></div></div><div class="row"><div class="col-md-6"><div class="mb-3"><label class="fw-bold">Discount %</label><input type="number" name="discount" id="discount" class="form-control" value="0" min="0" max="100"></div></div><div class="col-md-6"><div class="mb-3"><div class="form-check mt-4"><input type="checkbox" name="active" id="active" class="form-check-input" checked><label class="form-check-label fw-bold">Active</label></div></div></div></div><div class="row"><div class="col-md-6"><div class="mb-3"><label class="fw-bold">Start Date</label><input type="date" name="start_date" id="start_date" class="form-control"></div></div><div class="col-md-6"><div class="mb-3"><label class="fw-bold">End Date</label><input type="date" name="end_date" id="end_date" class="form-control"></div></div></div></div><div class="col-md-4"><div class="mb-3"><label class="fw-bold">Ad Image</label><input type="file" name="image" id="image" class="form-control" accept="image/*"><small class="text-muted">Recommended: 1200x400px for hero</small><div id="imagePreview" class="mt-2 text-center"><img id="previewImg" src="" style="max-width:100%; max-height:150px; display:none; border-radius:8px;"></div></div></div></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" name="submit" class="btn btn-amazon" id="submitBtn">Create Ad</button></div></form></div></div></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function resetForm() {
    document.getElementById('modalTitle').innerText = 'Create Ad';
    document.getElementById('ad_id').value = '';
    document.getElementById('title').value = '';
    document.getElementById('description').value = '';
    document.getElementById('type').value = 'hero';
    document.getElementById('display_order').value = '0';
    document.getElementById('discount').value = '0';
    document.getElementById('active').checked = true;
    document.getElementById('start_date').value = '';
    document.getElementById('end_date').value = '';
    document.getElementById('previewImg').style.display = 'none';
    document.getElementById('submitBtn').innerText = 'Create Ad';
}

function editAd(ad) {
    document.getElementById('modalTitle').innerText = 'Edit Ad';
    document.getElementById('ad_id').value = ad.id;
    document.getElementById('title').value = ad.title;
    document.getElementById('description').value = ad.description || '';
    document.getElementById('type').value = ad.type;
    document.getElementById('display_order').value = ad.display_order;
    document.getElementById('discount').value = ad.discount_percentage;
    document.getElementById('active').checked = ad.active == 1;
    document.getElementById('start_date').value = ad.start_date || '';
    document.getElementById('end_date').value = ad.end_date || '';
    if(ad.image) { document.getElementById('previewImg').src = '../assets/uploads/ads/' + ad.image; document.getElementById('previewImg').style.display = 'block'; }
    document.getElementById('submitBtn').innerText = 'Update Ad';
    new bootstrap.Modal(document.getElementById('adModal')).show();
}

document.getElementById('image')?.addEventListener('change', function(e) {
    const reader = new FileReader();
    reader.onload = function(e) { const preview = document.getElementById('previewImg'); preview.src = e.target.result; preview.style.display = 'block'; }
    if(this.files[0]) reader.readAsDataURL(this.files[0]);
});
</script>
</body>
</html>