<?php
require_once 'auth.php'; require_once 'db.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){header('Location: manage_products.php');exit;}
$id=filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);if(!$id){header('Location: manage_products.php');exit;}
$q=$conn->prepare("SELECT product_image FROM products WHERE id=?");$q->execute([$id]);$p=$q->fetch();
if($p){$d=$conn->prepare("DELETE FROM products WHERE id=?");$d->execute([$id]);if(!empty($p['product_image'])){$path=__DIR__.'/src/'.basename($p['product_image']);if(is_file($path))unlink($path);}}
header('Location: manage_products.php?message='.urlencode('Product deleted'));exit;
?>
