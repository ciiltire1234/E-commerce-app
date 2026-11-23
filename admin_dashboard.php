<?php
session_start();
require_once 'db.php';

$error = '';

if (isset($_GET['logout'])) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    header("Location: admin_dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Demo credentials matching the original project. Change before deployment.
    if ($username === 'admin' && $password === '1234') {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = 1;
        $_SESSION['admin_username'] = 'admin';
        header("Location: admin_dashboard.php");
        exit;
    }
    $error = 'Invalid username or password!';
}

$loggedIn = isset($_SESSION['admin_id']);
$totalProducts = 0;
$totalStock = 0;
if ($loggedIn) {
    $stats = $conn->query("SELECT COUNT(*) AS total_products, COALESCE(SUM(quantity),0) AS total_stock FROM products")->fetch();
    $totalProducts = (int)$stats['total_products'];
    $totalStock = (int)$stats['total_stock'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Electro_ecom</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<?php if (!$loggedIn): ?>
    <div class="bg-gray-950 min-h-screen flex items-center justify-center p-5">
        <div class="bg-white w-full max-w-md p-8 rounded-2xl shadow-xl">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-black">Electro_ecom<span class="text-green-600">.</span></h1>
                <p class="text-gray-500 mt-2">Administrator Login</p>
            </div>
            <?php if ($error): ?>
                <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-5"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <form method="POST">
                <label class="block font-semibold mb-2">Username</label>
                <input type="text" name="username" required autocomplete="username" class="w-full border rounded-lg p-3 mb-5" placeholder="Enter username">
                <label class="block font-semibold mb-2">Password</label>
                <input type="password" name="password" required autocomplete="current-password" class="w-full border rounded-lg p-3 mb-6" placeholder="Enter password">
                <button class="w-full bg-green-600 hover:bg-green-700 text-white font-bold p-3 rounded-lg" type="submit">Login to Dashboard</button>
            </form>
            <p class="text-xs text-gray-400 mt-5 text-center">Demo login: admin / 1234 — change before publishing.</p>
        </div>
    </div>
<?php else: ?>
    <header class="bg-gray-950 text-white px-5 py-4">
        <div class="max-w-7xl mx-auto flex flex-wrap justify-between items-center gap-4">
            <a href="admin_dashboard.php" class="text-xl font-black">Electro_ecom<span class="text-green-500">.</span> Admin</a>
            <div class="flex flex-wrap gap-2 text-sm">
                <a href="index.php" class="px-3 py-2 rounded-lg hover:bg-gray-800">Home</a>
                <a href="about_us.php" class="px-3 py-2 rounded-lg hover:bg-gray-800">About Us</a>
                <a href="products.php" class="px-3 py-2 rounded-lg hover:bg-gray-800">Products</a>
                <a href="add_product.php" class="px-3 py-2 rounded-lg hover:bg-gray-800">Add Product</a>
                <a href="cart.php" class="px-3 py-2 rounded-lg hover:bg-gray-800">Cart</a>
                <a href="admin_dashboard.php?logout=1" class="bg-red-600 hover:bg-red-700 px-3 py-2 rounded-lg">Logout</a>
            </div>
        </div>
    </header>
    <main class="max-w-7xl mx-auto p-6 md:p-8">
        <h1 class="text-3xl font-black">Welcome, <?= htmlspecialchars($_SESSION['admin_username'], ENT_QUOTES, 'UTF-8') ?>!</h1>
        <p class="text-gray-600 mt-2">Manage your Electro_ecom store from one place.</p>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 mt-8">
            <a href="index.php" class="bg-white p-6 rounded-2xl shadow hover:shadow-lg">
                <div class="text-blue-600 text-2xl">⌂</div><h2 class="font-bold text-xl mt-3">Home</h2><p class="text-gray-500 mt-1">Open the store home page.</p>
            </a>
            <a href="about_us.php" class="bg-white p-6 rounded-2xl shadow hover:shadow-lg">
                <div class="text-blue-600 text-2xl">ⓘ</div><h2 class="font-bold text-xl mt-3">About Us</h2><p class="text-gray-500 mt-1">View the store information page.</p>
            </a>
            <a href="manage_products.php" class="bg-white p-6 rounded-2xl shadow hover:shadow-lg">
                <div class="text-blue-600 text-2xl">▦</div><h2 class="font-bold text-xl mt-3">Products</h2><p class="text-gray-500 mt-1">View, update and delete products.</p><p class="font-bold mt-3"><?= $totalProducts ?> products · <?= $totalStock ?> in stock</p>
            </a>
            <a href="add_product.php" class="bg-white p-6 rounded-2xl shadow hover:shadow-lg">
                <div class="text-green-600 text-2xl">＋</div><h2 class="font-bold text-xl mt-3">Add Product</h2><p class="text-gray-500 mt-1">Add product details, size, quantity and image.</p>
            </a>
            <a href="cart.php" class="bg-white p-6 rounded-2xl shadow hover:shadow-lg">
                <div class="text-blue-600 text-2xl">🛒</div><h2 class="font-bold text-xl mt-3">Cart</h2><p class="text-gray-500 mt-1">View items added to the shopping cart.</p>
            </a>
        </div>
    </main>
<?php endif; ?>
</body>
</html>
