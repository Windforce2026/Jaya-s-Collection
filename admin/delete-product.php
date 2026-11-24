<?php
require __DIR__ . '/lib.php';
require_login();
require_csrf();

$slug = trim(post_or('slug'));
$store = load_store();
$remaining = array();
$removed = false;

foreach ($store['products'] as $product) {
    if (!$removed && $product['slug'] === $slug) {
        $removed = true;
        continue;
    }
    $remaining[] = $product;
}

if (!$removed) {
    flash('That product could not be found.');
    header('Location: index.php');
    exit;
}

$store['products'] = $remaining;
if (save_store($store)) {
    flash('Product deleted. The site is up to date.');
} else {
    flash('The product was removed from the list, but js/data.js could not be regenerated. Check file permissions.');
}
header('Location: index.php');
exit;
