<?php
require_once 'auth.php';
require_once 'db.php';

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

function getProduct(PDO $conn, int $id) {
    $s=$conn->prepare("SELECT * FROM products WHERE id=?");
    $s->execute([$id]);
    return $s->fetch();
}

$message='';

// Add item to cart
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='add') {
    $id=filter_var($_POST['product_id']??'',FILTER_VALIDATE_INT);
    $qty=filter_var($_POST['quantity']??1,FILTER_VALIDATE_INT);
    $size=trim($_POST['size']??'');
    $p=$id ? getProduct($conn,(int)$id) : false;
    if(!$p || $qty===false || $qty<1) {
        $message='Invalid product or quantity.';
    } elseif((int)$p['quantity'] < $qty) {
        $message='Not enough stock available.';
    } else {
        $key=(int)$p['id'].'|'.$size;
        $existing=(int)($_SESSION['cart'][$key]['quantity']??0);
        if($existing+$qty>(int)$p['quantity']) {
            $message='Requested quantity is greater than available stock.';
        } else {
            $_SESSION['cart'][$key]=[
                'product_id'=>(int)$p['id'],
                'quantity'=>$existing+$qty,
                'size'=>$size
            ];
            header('Location: cart.php?added=1'); exit;
        }
    }
}

// Update quantity or size
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='update') {
    $key=$_POST['key']??'';
    $qty=filter_var($_POST['quantity']??'',FILTER_VALIDATE_INT);
    if(isset($_SESSION['cart'][$key]) && $qty!==false && $qty>0) {
        $item=$_SESSION['cart'][$key]; $p=getProduct($conn,(int)$item['product_id']);
        if($p && $qty<=(int)$p['quantity']) {
            $_SESSION['cart'][$key]['quantity']=$qty;
            $_SESSION['cart'][$key]['size']=trim($_POST['size']??$item['size']);
        } else $message='Quantity exceeds available stock.';
    }
    if($message===''){header('Location: cart.php?updated=1');exit;}
}

// Remove item
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='remove') {
    $key=$_POST['key']??'';
    unset($_SESSION['cart'][$key]);
    header('Location: cart.php?removed=1'); exit;
}

$items=[]; $grandTotal=0;
foreach($_SESSION['cart'] as $key=>$item) {
    $p=getProduct($conn,(int)$item['product_id']);
    if(!$p) { unset($_SESSION['cart'][$key]); continue; }
    $unit=(float)$p['price']*(1-(float)$p['discount']/100);
    $line=$unit*(int)$item['quantity']; $grandTotal+=$line;
    $items[]=['key'=>$key,'product'=>$p,'quantity'=>(int)$item['quantity'],'size'=>$item['size'],'unit'=>$unit,'line'=>$line];
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Shopping Cart | Electro_ecom</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-gray-100 min-h-screen">
<header class="bg-gray-950 text-white p-4"><nav class="max-w-7xl mx-auto flex flex-wrap justify-between gap-3"><a href="index.php" class="text-2xl font-black">Electro_ecom.</a><div class="flex flex-wrap gap-4"><a href="index.php">Home</a><a href="about_us.php">About Us</a><a href="products.php">Products</a><a href="add_product.php">Add Product</a><a class="text-blue-300" href="cart.php">Cart</a><a href="admin_dashboard.php?logout=1">Logout</a></div></nav></header>
<main class="max-w-6xl mx-auto p-6"><h1 class="text-4xl font-black mb-7">Shopping Cart</h1>
<?php if(isset($_GET['added'])): ?><p class="bg-green-100 text-green-700 p-3 rounded mb-4">Product added to cart.</p><?php endif; ?>
<?php if(isset($_GET['updated'])): ?><p class="bg-green-100 text-green-700 p-3 rounded mb-4">Cart updated.</p><?php endif; ?>
<?php if(isset($_GET['removed'])): ?><p class="bg-green-100 text-green-700 p-3 rounded mb-4">Item removed from cart.</p><?php endif; ?>
<?php if($message): ?><p class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= htmlspecialchars($message,ENT_QUOTES,'UTF-8') ?></p><?php endif; ?>
<?php if(!$items): ?><div class="bg-white rounded-xl p-12 text-center"><p class="text-gray-500 text-lg">Your cart is empty.</p><a href="products.php" class="inline-block mt-5 bg-blue-600 text-white px-6 py-3 rounded-lg font-bold">Browse Products</a></div>
<?php else: ?><div class="space-y-4">
<?php foreach($items as $item): $p=$item['product']; ?>
<div class="bg-white rounded-xl shadow-sm p-5 grid md:grid-cols-[120px_1fr_auto] gap-5 items-center">
<img src="src/<?= htmlspecialchars(basename($p['product_image']??''),ENT_QUOTES,'UTF-8') ?>" class="w-28 h-28 object-contain bg-gray-50 rounded-lg" alt="">
<div><h2 class="font-bold text-xl"><?= htmlspecialchars($p['product_name'],ENT_QUOTES,'UTF-8') ?></h2><p class="text-gray-500">Unit price: $<?= number_format($item['unit'],2) ?></p><p class="text-gray-500">Line total: <strong class="text-gray-900">$<?= number_format($item['line'],2) ?></strong></p><p class="text-gray-500">Stock available: <?= (int)$p['quantity'] ?></p></div>
<div class="flex flex-col gap-2">
<form method="POST" class="flex flex-wrap items-center gap-2"><input type="hidden" name="action" value="update"><input type="hidden" name="key" value="<?= htmlspecialchars($item['key'],ENT_QUOTES,'UTF-8') ?>"><label class="text-sm">Qty</label><input type="number" name="quantity" min="1" max="<?= (int)$p['quantity'] ?>" value="<?= $item['quantity'] ?>" class="border rounded p-2 w-20"><label class="text-sm">Size</label><input name="size" value="<?= htmlspecialchars($item['size'],ENT_QUOTES,'UTF-8') ?>" class="border rounded p-2 w-24"><button class="bg-blue-600 text-white px-3 py-2 rounded">Update</button></form>
<form method="POST"><input type="hidden" name="action" value="remove"><input type="hidden" name="key" value="<?= htmlspecialchars($item['key'],ENT_QUOTES,'UTF-8') ?>"><button class="bg-red-600 text-white px-3 py-2 rounded w-full">Remove</button></form>
</div></div>
<?php endforeach; ?></div>
<div class="bg-white rounded-xl shadow p-6 mt-6 text-right"><p class="text-gray-500">Cart total</p><p class="text-3xl font-black">$<?= number_format($grandTotal,2) ?></p><p class="text-sm text-gray-500 mt-2">This demo cart is stored in the current PHP session. Checkout/payment is not connected.</p></div>
<?php endif; ?></main></body></html>
