
<?php
require_once 'db.php';

$stmt = $conn->query("SELECT * FROM products ORDER BY id DESC LIMIT 6");
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Electro_ecom | Premium Electronics Store</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .hero-overlay {
            background: linear-gradient(
                90deg,
                rgba(5, 12, 28, 0.90),
                rgba(5, 12, 28, 0.30)
            );
        }
    </style>
</head>

<body class="bg-white text-gray-900">

<!-- NAVBAR -->
<header class="bg-white sticky top-0 z-50 shadow-sm">
    <nav class="max-w-7xl mx-auto px-6 py-4 flex
                justify-between items-center">

        <!-- LOGO -->
        <a href="index.php" class="flex items-center gap-2">
            <img src="src/logo.png"
                 alt="Electro Logo"
                 class="w-11 h-11 object-contain">

            <span class="text-2xl font-black tracking-tight">
                Electro_ecom<span class="text-blue-600">.</span>
            </span>
        </a>

        <!-- LINKS -->
        <div class="hidden md:flex items-center gap-8
                    text-sm font-semibold text-gray-700">

            <a href="index.php" class="text-blue-600">Home</a>
            <a href="products.php" class="hover:text-blue-600">Products</a>
            <a href="#about" class="hover:text-blue-600">About Us</a>
            <a href="#" class="hover:text-blue-600">Services</a>
            <a href="#" class="hover:text-blue-600">Contact</a>
            <a href="#" class="hover:text-blue-600">Blog</a>

        </div>

        <a href="products.php"
           class="bg-blue-600 text-white px-6 py-3 rounded-full
                  text-sm font-bold hover:bg-blue-700 transition">
            Shop Now
        </a>

    </nav>
</header>


<!-- HERO SECTION -->
<section class="relative h-[620px] flex items-center overflow-hidden">

    <!-- VIDEO -->
    <video autoplay muted loop playsinline
           class="absolute inset-0 w-full h-full object-cover">

        <source src="src/hero.mp4" type="video/mp4">

    </video>

    <!-- OVERLAY -->
    <div class="absolute inset-0 hero-overlay"></div>

    <!-- CONTENT -->
    <div class="relative z-10 max-w-7xl mx-auto px-6 w-full">

        <div class="max-w-2xl text-white">

            <p class="text-blue-400 font-bold uppercase
                      tracking-[4px] mb-6 text-sm">
                The Future Is Here
            </p>

            <h1 class="text-5xl md:text-7xl font-black
                       leading-tight mb-6">

                Electro_ecom
                <span class="text-blue-400">That</span>
                Inspires.

            </h1>

            <p class="text-gray-300 text-lg leading-8 mb-9">
                Discover premium smartphones, laptops, smartwatches
                and the latest electronics. Upgrade your lifestyle
                with technology made for you.
            </p>

            <div class="flex flex-wrap gap-4">

                <a href="products.php"
                   class="bg-blue-600 px-8 py-4 rounded-full
                          font-bold hover:bg-blue-700 transition">
                    Explore Products →
                </a>

                <a href="#featured"
                   class="border border-white/50 px-8 py-4
                          rounded-full font-bold hover:bg-white
                          hover:text-gray-900 transition">
                    Discover More
                </a>

            </div>

        </div>

    </div>

</section>


<!-- FEATURES -->
<section class="border-b border-gray-100">

    <div class="max-w-7xl mx-auto px-6 py-7 grid
                grid-cols-1 sm:grid-cols-3 gap-6 text-center">

        <div>
            <h3 class="font-bold">Premium Quality</h3>
            <p class="text-sm text-gray-500 mt-1">
                Quality products you can trust
            </p>
        </div>

        <div>
            <h3 class="font-bold">Latest   Electro_ecom  products</h3>
            <p class="text-sm text-gray-500 mt-1">
                Discover modern electronics
            </p>
        </div>

        <div>
            <h3 class="font-bold">Great Prices</h3>
            <p class="text-sm text-gray-500 mt-1">
                Electro_ecom for every budget
            </p>
        </div>

    </div>

</section>


<!-- FEATURED PRODUCTS -->
<section id="featured" class="max-w-7xl mx-auto px-6 py-20">

    <div class="flex flex-col md:flex-row
                justify-between md:items-end gap-5 mb-12">

        <div>
            <p class="text-blue-600 font-bold uppercase
                      tracking-widest text-sm mb-3">
                Our Collection
            </p>

            <h2 class="text-4xl font-black">
                Featured Products<span class="text-blue-600">.</span>
            </h2>

            <p class="text-gray-500 mt-3">
                Explore our latest electronic collections.
            </p>
        </div>

        <a href="products.php"
           class="font-bold text-blue-600 hover:text-blue-800">
            Explore All Products →
        </a>

    </div>


    <!-- PRODUCT GRID -->
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

            <div class="group bg-white rounded-2xl border
                        border-gray-100 overflow-hidden
                        hover:shadow-xl transition duration-300">

                <!-- IMAGE -->
                <div class="relative bg-gray-50 h-64
                            overflow-hidden">

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

                <!-- DETAILS -->
                <div class="p-6">

                    <p class="text-blue-600 text-xs
                              font-bold uppercase tracking-widest">
                        <?= $category ?>
                    </p>

                    <h3 class="font-bold text-xl mt-2 mb-4">
                        <?= $name ?>
                    </h3>

                    <div class="flex items-center
                                justify-between">

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

                        <a href="products.php"
                           class="bg-gray-900 text-white px-5 py-3
                                  rounded-full text-sm font-bold
                                  hover:bg-blue-600 transition">
                            View Product
                        </a>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

    <?php if (empty($products)): ?>
        <p class="text-center text-gray-500 py-10">
            No products available.
        </p>
    <?php endif; ?>

    <div class="text-center mt-14">

        <a href="products.php"
           class="inline-block bg-blue-600 text-white
                  px-10 py-4 rounded-full font-bold
                  hover:bg-blue-700 transition">
            Explore More Products →
        </a>

    </div>

</section>


<!-- ABOUT SECTION -->
<section id="about" class="bg-gray-950 text-white py-20">

    <div class="max-w-7xl mx-auto px-6 text-center">

        <p class="text-blue-400 uppercase tracking-widest
                  text-sm font-bold mb-4">
            About Our Store
        </p>

        <h2 class="text-4xl font-black mb-5">
            Welcom To Electro_ecom
        </h2>

        <p class="text-gray-400 max-w-2xl mx-auto leading-7">
            We bring together modern electronics and innovative
            technology to make your everyday life better.
        </p>

    </div>

</section>


<!-- FOOTER -->
<footer class="bg-black text-white py-10">

    <div class="max-w-7xl mx-auto px-6
                flex flex-col md:flex-row
                justify-between items-center gap-5">

        <h2 class="text-2xl font-black">
            Electro_ecom<span class="text-blue-500">.</span>
        </h2>

        <p class="text-gray-500 text-sm">
            © 2026 Electro_ecom All rights reserved.

        </p>

        <a href="products.php"
           class="text-blue-400 hover:text-white">
            Shop Now →
        </a>

    </div>

</footer>

</body>
</html>
