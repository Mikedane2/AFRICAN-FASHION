<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../config/config.php';
if(!isAdmin()) { header('Location: login.php'); exit; }

// ADD PRODUCT
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $description = $_POST['description'];
    $short_description = $_POST['short_description'];
    $price_usd = floatval($_POST['price_usd']);
    $compare_price_usd = !empty($_POST['compare_price_usd']) ? floatval($_POST['compare_price_usd']) : null;
    $category_id = intval($_POST['category_id']);
    $brand = $_POST['brand'];
    $sizes = $_POST['sizes'];
    $colors = $_POST['colors'];
    $stock_quantity = intval($_POST['stock_quantity']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $trending = isset($_POST['trending']) ? 1 : 0;
    $best_seller = isset($_POST['best_seller']) ? 1 : 0;
    $new_arrival = isset($_POST['new_arrival']) ? 1 : 0;
    
    // Handle multiple image uploads
    $uploadedImages = [];
    $uploadDir = '../assets/uploads/';
    
    // Create uploads directory if not exists
    if(!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Process uploaded files
    if(isset($_FILES['product_images']) && !empty($_FILES['product_images']['name'][0])) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/bmp'];
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        
        for($i = 0; $i < count($_FILES['product_images']['name']); $i++) {
            if($_FILES['product_images']['error'][$i] == 0) {
                $fileName = $_FILES['product_images']['name'][$i];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $fileType = $_FILES['product_images']['type'][$i];
                $fileSize = $_FILES['product_images']['size'][$i];
                
                // Validate file
                if(in_array($fileExt, $allowedExt) && in_array($fileType, $allowedTypes) && $fileSize < 5000000) { // 5MB max
                    $newFileName = time() . '_' . $i . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $fileName);
                    $destination = $uploadDir . $newFileName;
                    
                    if(move_uploaded_file($_FILES['product_images']['tmp_name'][$i], $destination)) {
                        $uploadedImages[] = $newFileName;
                    }
                }
            }
        }
    }
    
    // Generate SKU
    $sku = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 5)) . '-' . time();
    
    // Save to database
    $imagesJson = json_encode($uploadedImages);
    $stmt = $pdo->prepare("INSERT INTO products (name, slug, description, short_description, price_usd, compare_price_usd, category_id, brand, sizes, colors, stock_quantity, sku, images, featured, trending, best_seller, new_arrival) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([$name, $slug, $description, $short_description, $price_usd, $compare_price_usd, $category_id, $brand, $sizes, $colors, $stock_quantity, $sku, $imagesJson, $featured, $trending, $best_seller, $new_arrival]);
    
    header('Location: products.php?msg=added');
    exit;
}

// UPDATE PRODUCT
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
    $id = intval($_POST['product_id']);
    $name = trim($_POST['name']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $description = $_POST['description'];
    $short_description = $_POST['short_description'];
    $price_usd = floatval($_POST['price_usd']);
    $compare_price_usd = !empty($_POST['compare_price_usd']) ? floatval($_POST['compare_price_usd']) : null;
    $category_id = intval($_POST['category_id']);
    $brand = $_POST['brand'];
    $sizes = $_POST['sizes'];
    $colors = $_POST['colors'];
    $stock_quantity = intval($_POST['stock_quantity']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $trending = isset($_POST['trending']) ? 1 : 0;
    $best_seller = isset($_POST['best_seller']) ? 1 : 0;
    $new_arrival = isset($_POST['new_arrival']) ? 1 : 0;
    
    // Get existing images
    $stmt = $pdo->prepare("SELECT images FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $existing = $stmt->fetch();
    $existingImages = $existing['images'] ? json_decode($existing['images'], true) : [];
    
    $uploadDir = '../assets/uploads/';
    if(!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
    
    // Handle deletion of existing images
    if(isset($_POST['delete_images']) && !empty($_POST['delete_images'])) {
        $toDelete = explode(',', $_POST['delete_images']);
        foreach($toDelete as $img) {
            $img = trim($img);
            if(in_array($img, $existingImages)) {
                $filePath = $uploadDir . $img;
                if(file_exists($filePath)) {
                    unlink($filePath);
                }
                $key = array_search($img, $existingImages);
                if($key !== false) {
                    unset($existingImages[$key]);
                }
            }
        }
        $existingImages = array_values($existingImages);
    }
    
    // Upload new images
    if(isset($_FILES['product_images']) && !empty($_FILES['product_images']['name'][0])) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/bmp'];
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        
        for($i = 0; $i < count($_FILES['product_images']['name']); $i++) {
            if($_FILES['product_images']['error'][$i] == 0) {
                $fileName = $_FILES['product_images']['name'][$i];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $fileType = $_FILES['product_images']['type'][$i];
                $fileSize = $_FILES['product_images']['size'][$i];
                
                if(in_array($fileExt, $allowedExt) && in_array($fileType, $allowedTypes) && $fileSize < 5000000) {
                    $newFileName = time() . '_' . $i . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $fileName);
                    $destination = $uploadDir . $newFileName;
                    
                    if(move_uploaded_file($_FILES['product_images']['tmp_name'][$i], $destination)) {
                        $existingImages[] = $newFileName;
                    }
                }
            }
        }
    }
    
    $imagesJson = json_encode($existingImages);
    
    $stmt = $pdo->prepare("UPDATE products SET name=?, slug=?, description=?, short_description=?, price_usd=?, compare_price_usd=?, category_id=?, brand=?, sizes=?, colors=?, stock_quantity=?, images=?, featured=?, trending=?, best_seller=?, new_arrival=? WHERE id=?");
    $stmt->execute([$name, $slug, $description, $short_description, $price_usd, $compare_price_usd, $category_id, $brand, $sizes, $colors, $stock_quantity, $imagesJson, $featured, $trending, $best_seller, $new_arrival, $id]);
    
    header('Location: products.php?msg=updated');
    exit;
}

// DELETE PRODUCT
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Get images to delete from server
    $stmt = $pdo->prepare("SELECT images FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    
    if($product && $product['images']) {
        $images = json_decode($product['images'], true);
        foreach($images as $image) {
            $filePath = '../assets/uploads/' . $image;
            if(file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }
    
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    
    header('Location: products.php?msg=deleted');
    exit;
}

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$editProduct = null;
if(isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editProduct = $stmt->fetch();
    if($editProduct) {
        $editProduct['images_array'] = $editProduct['images'] ? json_decode($editProduct['images'], true) : [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - AfriMart Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #232F3E;
        }
        .sidebar .nav-link {
            color: white;
            padding: 12px 20px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: #FF9900;
            color: #111;
        }
        .product-image-preview {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            margin: 5px;
            border: 1px solid #ddd;
        }
        .image-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        .image-item {
            position: relative;
            display: inline-block;
        }
        .image-item .remove-image {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 10px;
            text-align: center;
            line-height: 18px;
            cursor: pointer;
            border: none;
        }
        .stock-low {
            color: #ffc107;
            font-weight: bold;
        }
        .stock-out {
            color: #dc3545;
            font-weight: bold;
        }
        .stock-good {
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 p-0 sidebar">
            <div class="text-center py-3 border-bottom border-secondary">
                <h5 class="text-white"><i class="fas fa-store"></i> AfriMart Admin</h5>
            </div>
            <nav class="nav flex-column">
                <a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a class="nav-link active" href="products.php"><i class="fas fa-box"></i> Products</a>
                <a class="nav-link" href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a class="nav-link" href="inventory.php"><i class="fas fa-warehouse"></i> Inventory</a>
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-box"></i> Product Management</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" onclick="resetForm()">
                    <i class="fas fa-plus"></i> Add New Product
                </button>
            </div>
            
            <?php if(isset($_GET['msg'])): ?>
                <?php if($_GET['msg'] == 'added'): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle"></i> Product added successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif($_GET['msg'] == 'updated'): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle"></i> Product updated successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif($_GET['msg'] == 'deleted'): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle"></i> Product deleted successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <!-- Products Table -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">All Products</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Product Name</th>
                                    <th>Price (USD)</th>
                                    <th>Stock</th>
                                    <th>Sold</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($products as $product): 
                                    $images = $product['images'] ? json_decode($product['images'], true) : ['placeholder.jpg'];
                                    $stockClass = $product['stock_quantity'] == 0 ? 'stock-out' : ($product['stock_quantity'] < 10 ? 'stock-low' : 'stock-good');
                                    $stockText = $product['stock_quantity'] == 0 ? 'Out of Stock' : ($product['stock_quantity'] < 10 ? 'Low Stock' : 'In Stock');
                                ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td>
                                        <img src="../assets/uploads/<?php echo $images[0]; ?>" 
                                             width="60" height="60" style="object-fit: cover; border-radius: 8px;" 
                                             onerror="this.src='https://via.placeholder.com/60'">
                                    </div>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                        <br><small class="text-muted">SKU: <?php echo $product['sku']; ?></small>
                                        </div>
                                    </div>
                                    <td>
                                    <td class="<?php echo $stockClass; ?>">
                                        <?php echo $product['stock_quantity']; ?> units
                                        <br><small><?php echo $stockText; ?></small>
                                    </div>
                                    <td><?php echo $product['sold_count']; ?> units</div>
                                    <td>
                                        <?php if($product['featured']): ?>
                                            <span class="badge bg-primary">Featured</span>
                                        <?php endif; ?>
                                        <?php if($product['best_seller']): ?>
                                            <span class="badge bg-success">Best Seller</span>
                                        <?php endif; ?>
                                        <?php if($product['trending']): ?>
                                            <span class="badge bg-info">Trending</span>
                                        <?php endif; ?>
                                        <?php if($product['new_arrival']): ?>
                                            <span class="badge bg-warning">New</span>
                                        <?php endif; ?>
                                    </div>
                                    <td>
                                        <button class="btn btn-sm btn-warning mb-1" onclick='editProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)'>
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <a href="?delete=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                   </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background: #232F3E; color: white;">
                <h5 class="modal-title" id="modalTitle"><i class="fas fa-plus"></i> Add New Product</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" id="productForm">
                <div class="modal-body">
                    <input type="hidden" name="product_id" id="product_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Product Name *</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Category *</label>
                                <select name="category_id" id="category_id" class="form-control" required>
                                    <option value="">Select Category</option>
                                    <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Brand</label>
                                <input type="text" name="brand" id="brand" class="form-control" placeholder="e.g., Nike, Adidas">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Short Description</label>
                        <input type="text" name="short_description" id="short_description" class="form-control" placeholder="Brief product description for listing">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Description</label>
                        <textarea name="description" id="description" class="form-control" rows="4" placeholder="Detailed product description..."></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Price (USD) *</label>
                                <input type="number" step="0.01" name="price_usd" id="price_usd" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Compare Price (USD)</label>
                                <input type="number" step="0.01" name="compare_price_usd" id="compare_price_usd" class="form-control" placeholder="Original price for sale">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Stock Quantity *</label>
                                <input type="number" name="stock_quantity" id="stock_quantity" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Sizes</label>
                                <input type="text" name="sizes" id="sizes" class="form-control" placeholder="S,M,L,XL or 28,30,32,34">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Colors</label>
                                <input type="text" name="colors" id="colors" class="form-control" placeholder="Red,Blue,Black,White">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Product Images *</label>
                                <input type="file" name="product_images[]" id="product_images" class="form-control" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/bmp" multiple>
                                <small class="text-muted">Supported formats: JPG, PNG, GIF, WebP, BMP. Max size: 5MB each. You can select multiple images.</small>
                                <div id="imagePreviewContainer" class="image-preview-container mt-2"></div>
                                <div id="existingImagesContainer" class="image-preview-container mt-2"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check">
                                <input type="checkbox" name="featured" id="featured" class="form-check-input">
                                <label class="form-check-label">Featured Product</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input type="checkbox" name="trending" id="trending" class="form-check-input">
                                <label class="form-check-label">Trending</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input type="checkbox" name="best_seller" id="best_seller" class="form-check-input">
                                <label class="form-check-label">Best Seller</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input type="checkbox" name="new_arrival" id="new_arrival" class="form-check-input">
                                <label class="form-check-label">New Arrival</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_product" id="submitBtn" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Image preview for new uploads
document.getElementById('product_images').addEventListener('change', function(e) {
    const previewContainer = document.getElementById('imagePreviewContainer');
    previewContainer.innerHTML = '';
    
    if(this.files) {
        Array.from(this.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'image-item';
                div.innerHTML = `
                    <img src="${e.target.result}" class="product-image-preview">
                    <button type="button" class="remove-image" data-index="${index}">×</button>
                `;
                previewContainer.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    }
});

// Remove image preview
$(document).on('click', '.remove-image', function() {
    const input = document.getElementById('product_images');
    const dt = new DataTransfer();
    const files = Array.from(input.files);
    const index = $(this).data('index');
    
    files.splice(index, 1);
    files.forEach(file => dt.items.add(file));
    input.files = dt.files;
    
    $(this).closest('.image-item').remove();
});

function resetForm() {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus"></i> Add New Product';
    document.getElementById('product_id').value = '';
    document.getElementById('name').value = '';
    document.getElementById('category_id').value = '';
    document.getElementById('brand').value = '';
    document.getElementById('short_description').value = '';
    document.getElementById('description').value = '';
    document.getElementById('price_usd').value = '';
    document.getElementById('compare_price_usd').value = '';
    document.getElementById('stock_quantity').value = '';
    document.getElementById('sizes').value = '';
    document.getElementById('colors').value = '';
    document.getElementById('featured').checked = false;
    document.getElementById('trending').checked = false;
    document.getElementById('best_seller').checked = false;
    document.getElementById('new_arrival').checked = false;
    document.getElementById('imagePreviewContainer').innerHTML = '';
    document.getElementById('existingImagesContainer').innerHTML = '';
    document.getElementById('productForm').reset();
    document.getElementById('submitBtn').name = 'add_product';
    document.getElementById('submitBtn').innerHTML = 'Add Product';
}

function editProduct(product) {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit"></i> Edit Product';
    document.getElementById('product_id').value = product.id;
    document.getElementById('name').value = product.name;
    document.getElementById('category_id').value = product.category_id;
    document.getElementById('brand').value = product.brand || '';
    document.getElementById('short_description').value = product.short_description || '';
    document.getElementById('description').value = product.description || '';
    document.getElementById('price_usd').value = product.price_usd;
    document.getElementById('compare_price_usd').value = product.compare_price_usd || '';
    document.getElementById('stock_quantity').value = product.stock_quantity;
    document.getElementById('sizes').value = product.sizes || '';
    document.getElementById('colors').value = product.colors || '';
    document.getElementById('featured').checked = product.featured == 1;
    document.getElementById('trending').checked = product.trending == 1;
    document.getElementById('best_seller').checked = product.best_seller == 1;
    document.getElementById('new_arrival').checked = product.new_arrival == 1;
    
    // Show existing images
    if(product.images_array && product.images_array.length > 0) {
        let container = document.getElementById('existingImagesContainer');
        container.innerHTML = '<div class="w-100 mb-2"><strong>Existing Images:</strong></div>';
        
        product.images_array.forEach((img, index) => {
            const div = document.createElement('div');
            div.className = 'image-item';
            div.innerHTML = `
                <img src="../assets/uploads/${img}" class="product-image-preview">
                <button type="button" class="btn btn-danger btn-sm remove-existing-image" data-image="${img}" style="position: absolute; top: -8px; right: -8px; width: 20px; height: 20px; border-radius: 50%; padding: 0; font-size: 10px;">×</button>
                <input type="hidden" name="existing_images[]" value="${img}">
            `;
            container.appendChild(div);
        });
        
        // Create hidden input for images to delete
        let deleteInput = document.createElement('input');
        deleteInput.type = 'hidden';
        deleteInput.name = 'delete_images';
        deleteInput.id = 'delete_images';
        container.appendChild(deleteInput);
    }
    
    document.getElementById('submitBtn').name = 'update_product';
    document.getElementById('submitBtn').innerHTML = 'Update Product';
    
    new bootstrap.Modal(document.getElementById('productModal')).show();
}

// Remove existing image
$(document).on('click', '.remove-existing-image', function() {
    const imageName = $(this).data('image');
    const deleteInput = document.getElementById('delete_images');
    let currentDeletes = deleteInput.value ? deleteInput.value.split(',') : [];
    currentDeletes.push(imageName);
    deleteInput.value = currentDeletes.join(',');
    $(this).closest('.image-item').remove();
});

// Form validation before submit
$('#productForm').on('submit', function(e) {
    const name = $('#name').val().trim();
    const price = $('#price_usd').val();
    const stock = $('#stock_quantity').val();
    
    if(!name) {
        alert('Please enter product name');
        e.preventDefault();
        return false;
    }
    if(!price || price <= 0) {
        alert('Please enter a valid price');
        e.preventDefault();
        return false;
    }
    if(stock === '' || stock < 0) {
        alert('Please enter valid stock quantity');
        e.preventDefault();
        return false;
    }
    
    return true;
});
</script>
</body>
</html>          