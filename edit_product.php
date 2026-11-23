<?php
require_once 'auth.php'; require_once 'db.php';
$id = filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
if(!$id){header('Location: manage_products.php');exit;}
$q=$conn->prepare("SELECT * FROM products WHERE id=?");$q->execute([$id]);$p=$q->fetch();
if(!$p){header('Location: manage_products.php');exit;}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
$name=trim($_POST['product_name']??'');$cat=trim($_POST['cat']??'');$price=filter_var($_POST['price']??'',FILTER_VALIDATE_FLOAT);$discount=filter_var($_POST['discount']??'0',FILTER_VALIDATE_FLOAT);$qty=filter_var($_POST['quantity']??'',FILTER_VALIDATE_INT);$size=trim($_POST['size']??'');
if($name===''||$cat===''||$price===false||$price<0||$discount===false||$discount<0||$discount>100||$qty===false||$qty<0){$error='Please enter valid product information.';}
else{
$image=$p['product_image'];$newPath=null;
if(isset($_FILES['product_image'])&&$_FILES['product_image']['error']!==UPLOAD_ERR_NO_FILE){
$tmp=$_FILES['product_image']['tmp_name'];$mime=(new finfo(FILEINFO_MIME_TYPE))->file($tmp);$types=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
if($_FILES['product_image']['error']!==UPLOAD_ERR_OK||$_FILES['product_image']['size']>5*1024*1024||!isset($types[$mime])||getimagesize($tmp)===false){$error='Choose a valid JPG, PNG or WEBP image under 5MB.';}
else{$image=bin2hex(random_bytes(16)).'.'.$types[$mime];$newPath=__DIR__.'/src/'.$image;if(!move_uploaded_file($tmp,$newPath)){$error='Image upload failed.';}}
}
if($error===''){try{$u=$conn->prepare("UPDATE products SET product_name=?,cat=?,price=?,discount=?,product_image=?,quantity=?,size=? WHERE id=?");$u->execute([$name,$cat,$price,$discount,$image,$qty,$size,$id]);if($newPath&&!empty($p['product_image'])){$old=__DIR__.'/src/'.basename($p['product_image']);if(is_file($old))unlink($old);}header('Location: manage_products.php?message='.urlencode('Product updated'));exit;}catch(PDOException $e){if($newPath&&is_file($newPath))unlink($newPath);$error='Update failed. Check database_update.sql was imported.';}}
}}
function oldv($key,$p){return htmlspecialchars($_POST[$key]??$p[$key]??'',ENT_QUOTES,'UTF-8');}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Update Product</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-gray-100 min-h-screen"><header class="bg-gray-950 text-white p-5 flex justify-between"><h1 class="font-bold text-xl">Update Product</h1><a href="manage_products.php" class="bg-gray-700 px-4 py-2 rounded">Back</a></header><main class="max-w-2xl mx-auto p-6"><form method="POST" enctype="multipart/form-data" class="bg-white p-8 rounded-2xl shadow space-y-4"><?php if($error): ?><p class="bg-red-100 text-red-700 p-3 rounded"><?= htmlspecialchars($error,ENT_QUOTES,'UTF-8') ?></p><?php endif; ?>
<div><label class="block font-semibold mb-2">Product Name</label><input name="product_name" required class="w-full border rounded p-3" value="<?= oldv('product_name',$p) ?>"></div>
<div><label class="block font-semibold mb-2">Category</label><input name="cat" required class="w-full border rounded p-3" value="<?= oldv('cat',$p) ?>"></div>
<div><label class="block font-semibold mb-2">Price ($)</label><input type="number" min="0" step="0.01" name="price" required class="w-full border rounded p-3" value="<?= oldv('price',$p) ?>"></div>
<div><label class="block font-semibold mb-2">Discount (%)</label><input type="number" min="0" max="100" step="0.01" name="discount" class="w-full border rounded p-3" value="<?= oldv('discount',$p) ?>"></div>
<div><label class="block font-semibold mb-2">Size</label><input name="size" class="w-full border rounded p-3" value="<?= oldv('size',$p) ?>"></div>
<div><label class="block font-semibold mb-2">Quantity / Stock</label><input type="number" min="0" name="quantity" required class="w-full border rounded p-3" value="<?= oldv('quantity',$p) ?>"></div>
<div><label class="block font-semibold mb-2">Current Image</label><img src="src/<?= htmlspecialchars(basename($p['product_image']??''),ENT_QUOTES,'UTF-8') ?>" class="w-32 h-32 object-contain border rounded p-2" alt=""></div>
<div><label class="block font-semibold mb-2">Upload replacement image (optional)</label><input type="file" name="product_image" accept=".jpg,.jpeg,.png,.webp" class="w-full border rounded p-3"></div>
<button class="w-full bg-blue-600 text-white font-bold p-3 rounded">Update Product</button></form></main></body></html>
