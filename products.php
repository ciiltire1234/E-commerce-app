
<?php
require_once 'db.php';

$stmt = $conn->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Products | Electro_ecom</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-white text-gray-900">

<!-- NAVBAR -->
<header class="bg-white sticky top-0 z-50 shadow-sm">

    <nav class="max-w-7xl mx-auto px-6 py-4
                flex justify-between items-center">

        <a href="index.php" class="flex items-center gap-2">

            <img src="src/logo.png"
                 class="w-11 h-11 object-contain"
                 alt="Electro Logo">

            <span class="text-2xl font-black">
                Electro_ecom<span class="text-blue-600">.</span>
            </span>

        </a>

        <div class="hidden md:flex items-center gap-8
                    text-sm font-semibold">

            <a href="index.php" class="hover:text-blue-600">Home</a>

            <a href="products.php" class="text-blue-600">Products</a>

            <a href="index.php#about" class="hover:text-blue-600">About Us</a>

            <a href="#" class="hover:text-blue-600">Services</a>

            <a href="#" class="hover:text-blue-600">Contact</a>

            <a href="#" class="hover:text-blue-600">Blog</a>

        </div>

        <a href="products.php"
           class="bg-blue-600 text-white px-6 py-3
                  rounded-full text-sm font-bold">
            Shop Now
        </a>

    </nav>

</header>


<!-- BANNER -->
<section class="relative h-80 overflow-hidden">

    <img src="src/banner.jpg"
         alt="Electronics collection"
         class="absolute inset-0 w-full h-full object-cover">

    <div class="absolute inset-0 bg-gray-950/70"></div>

    <div class="relative z-10 h-full flex flex-col
                justify-center items-center text-center text-white px-5">

        <p class="text-blue-400 font-bold uppercase
                  tracking-[4px] mb-4">
            Discover Electro_ecom
        </p>

        <h1 class="text-4xl md:text-6xl font-black mb-5">
            Our Products<span class="text-blue-400">.</span>
        </h1>

        <p class="text-gray-300 max-w-xl">
            Explore our collection of premium electronics
            and discover your next favorite device.
        </p>

    </div>

</section>


<!-- PRODUCTS -->
<section class="max-w-7xl mx-auto px-6 py-20">

    <div class="mb-12">

        <p class="text-blue-600 text-sm font-bold
                  uppercase tracking-widest mb-3">
            Shop Collection
        </p>

        <h2 class="text-4xl font-black">
            All Products
        </h2>

        <p class="text-gray-500 mt-3">
            <?= count($products) ?> products available
        </p>

    </div>


    <!-- PRODUCT GALLERY -->
    <div class="grid grid-cols-1 sm:grid-cols-2
                lg:grid-cols-3 gap-8">

        <?php foreach ($products as $product): ?>

            <?php
                $image = basename($product['product_image'] ?? '');
                $name = htmlspecialchars($product['product_name']);
                $category = htmlspecialchars($product['cat']);
                $price = (float)$product['price'];
                $discount = (float)$product['discount'];
                $finalPrice = $price - ($price * $discount / 100);
            ?>

            <div class="group bg-white rounded-2xl
                        border border-gray-100
                        overflow-hidden hover:shadow-xl
                        transition duration-300">

                <div class="relative bg-gray-50 h-64 overflow-hidden">

                    <img src="src/<?= htmlspecialchars($image) ?>"
                         alt="<?= $name ?>"
                         class="w-full h-full object-contain p-6
                                group-hover:scale-110 transition duration-500">

                    <?php if ($discount > 0): ?>

                        <span class="absolute top-4 left-4
                                     bg-red-500 text-white
                                     text-xs font-bold px-3 py-2
                                     rounded-full">
                            <?= $discount ?>% OFF
                        </span>

                    <?php endif; ?>

                </div>

                <div class="p-6">

                    <p class="text-blue-600 text-xs font-bold
                              uppercase tracking-widest">
                        <?= $category ?>
                    </p>

                    <h3 class="font-bold text-xl mt-2 mb-4">
                        <?= $name ?>
                    </h3>

                    <div class="flex items-center justify-between">

                        <div>

                            <span class="text-2xl font-black">
                                $<?= number_format($finalPrice, 2) ?>
                            </span>

                            <?php if ($discount > 0): ?>

                                <p class="text-sm text-gray-400 line-through">
                                    $<?= number_format($price, 2) ?>
                                </p>

                            <?php endif; ?>

                        </div>

                        <a href="index.php"
                           class="bg-gray-900 text-white
                                  px-5 py-3 rounded-full
                                  text-sm font-bold
                                  hover:bg-blue-600 transition">
                            View Product
                        </a>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>


    <?php if (empty($products)): ?>

        <div class="text-center py-20">
            <h3 class="text-2xl font-bold">No Products Found</h3>
            <p class="text-gray-500 mt-3">
                Please add products to your database.
            </p>
        </div>

    <?php endif; ?>

</section>


<!-- FOOTER -->
<footer class="bg-gray-950 text-white py-10">

    <div class="max-w-7xl mx-auto px-6 text-center">

        <h2 class="text-2xl font-black mb-3">
            Elecrt_ecom<span class="text-blue-500">.</span>
        </h2>

        <p class="text-gray-400 text-sm">
            Your destination for modern technology.
        </p>

        <p class="text-gray-600 text-xs mt-6">
            © 2026 Electro_ecom. All rights reserved.
        </p>

    </div>

</footer>

</body>
</html>
