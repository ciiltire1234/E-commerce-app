<?php
require_once 'auth.php';
require_once 'db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['product_name'] ?? '');
    $cat = trim($_POST['cat'] ?? '');
    $price = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_FLOAT);
    $discount = filter_var($_POST['discount'] ?? '0', FILTER_VALIDATE_FLOAT);
    $quantity = filter_var($_POST['quantity'] ?? '', FILTER_VALIDATE_INT);
    $size = trim($_POST['size'] ?? '');

    if ($name === '' || $cat === '' || $price === false || $price < 0 || $discount === false || $discount < 0 || $discount > 100 || $quantity === false || $quantity < 0) {
        $error = 'Please enter valid product details.';
    } elseif (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please select a product image.';
    } else {
        $dir = __DIR__ . '/src/';
        if (!is_dir($dir) || !is_writable($dir)) {
            $error = 'The src folder must exist and be writable.';
        } else {
            $tmp = $_FILES['product_image']['tmp_name'];
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
            $types = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
            if ($_FILES['product_image']['size'] > 5*1024*1024) {
                $error = 'Image must be 5MB or smaller.';
            } elseif (!isset($types[$mime]) || getimagesize($tmp) === false) {
                $error = 'Only valid JPG, PNG and WEBP images are accepted.';
            } else {
                $image = bin2hex(random_bytes(16)).'.'.$types[$mime];
                if (move_uploaded_file($tmp, $dir.$image)) {
                    try {
                        $q = $conn->prepare("INSERT INTO products (product_name, cat, price, discount, product_image, quantity, size) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $q->execute([$name,$cat,$price,$discount,$image,$quantity,$size]);
                        header('Location: manage_products.php?message='.urlencode('Product added successfully'));
                        exit;
                    } catch (PDOException $e) {
                        if (is_file($dir.$image)) unlink($dir.$image);
                        $error = 'Database save failed. Run the database_update.sql file first.';
                    }
                } else $error = 'Image upload failed.';
            }
        }
    }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Add Product | Electro_ecom</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gray-100 min-h-screen"><header class="bg-gray-950 text-white p-5 flex justify-between"><h1 class="font-bold text-xl">Add Product</h1><a href="admin_dashboard.php" class="bg-gray-700 px-4 py-2 rounded">Dashboard</a></header>
<main class="max-w-2xl mx-auto p-6"><form method="POST" enctype="multipart/form-data" class="bg-white p-8 rounded-2xl shadow space-y-4">
<?php if($error): ?><p class="bg-red-100 text-red-700 p-3 rounded"><?= htmlspecialchars($error,ENT_QUOTES,'UTF-8') ?></p><?php endif; ?>
<div><label class="block font-semibold mb-2">Product Name</label><input name="product_name" required maxlength="255" class="w-full border rounded-lg p-3"></div>
<div><label class="block font-semibold mb-2">Category</label><input name="cat" required maxlength="150" class="w-full border rounded-lg p-3"></div>
<div><label class="block font-semibold mb-2">Price ($)</label><input type="number" name="price" min="0" step="0.01" required class="w-full border rounded-lg p-3"></div>
<div><label class="block font-semibold mb-2">Discount (%)</label><input type="number" name="discount" min="0" max="100" step="0.01" value="0" class="w-full border rounded-lg p-3"></div>
<div><label class="block font-semibold mb-2">Size</label><input name="size" maxlength="100" placeholder="e.g. Small, Medium, Large, 128GB" class="w-full border rounded-lg p-3"></div>
<div><label class="block font-semibold mb-2">Available Quantity</label><input type="number" name="quantity" min="0" step="1" required value="1" class="w-full border rounded-lg p-3"></div>
<div><label class="block font-semibold mb-2">Upload Product Image (JPG/PNG/WEBP, max 5MB)</label><input type="file" name="product_image" accept=".jpg,.jpeg,.png,.webp" required class="w-full border rounded-lg p-3"></div>
<button class="w-full bg-green-600 hover:bg-green-700 text-white font-bold p-3 rounded-lg">Save Product</button>
</form></main></body></html>
