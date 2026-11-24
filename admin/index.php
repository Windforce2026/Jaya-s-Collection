<?php
require __DIR__ . '/lib.php';
require_login();

$store = load_store();
$categories = $store['categories'];
$products = $store['products'];

$grouped = array();
foreach ($categories as $cat) {
    $grouped[$cat['id']] = array();
}
foreach ($products as $product) {
    $key = isset($product['category']) ? $product['category'] : '';
    if (!isset($grouped[$key])) {
        $grouped[$key] = array();
    }
    $grouped[$key][] = $product;
}

$nameOf = function ($value) {
    return is_array($value) && isset($value['en']) ? $value['en'] : '';
};
$bnOf = function ($value) {
    return is_array($value) && isset($value['bn']) ? $value['bn'] : '';
};

admin_header('Products');
flash_html();
?>
<h1>Products</h1>
<p class="lead">Products are grouped by category. Saving here updates the live <code>js/data.js</code> file immediately.</p>

<div class="toolbar">
  <a class="button" href="product.php">+ Add product</a>
  <a class="button button--ghost" href="categories.php">Manage categories</a>
  <a class="button button--ghost" href="settings.php">Store details</a>
  <a class="button button--ghost" href="password.php">Change password</a>
</div>

<?php foreach ($categories as $cat) { ?>
  <?php $list = isset($grouped[$cat['id']]) ? $grouped[$cat['id']] : array(); ?>
  <section class="panel">
    <div class="panel__head">
      <h2><?php echo e($nameOf($cat['name'])); ?></h2>
      <small><?php echo count($list); ?> product<?php echo count($list) === 1 ? '' : 's'; ?> · <code><?php echo e($cat['id']); ?></code></small>
      <a class="link" style="margin-left:auto" href="product.php?category=<?php echo e($cat['id']); ?>">+ Add here</a>
    </div>
    <?php if (count($list) === 0) { ?>
      <p class="muted">No products in this category yet.</p>
    <?php } else { ?>
      <table>
        <thead>
          <tr><th>Photo</th><th>Product</th><th>Slug</th><th>Badge</th><th style="text-align:right">Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($list as $product) { ?>
            <tr>
              <td><?php if (!empty($product['image'])) { ?><img src="../<?php echo e($product['image']); ?>" alt=""><?php } ?></td>
              <td>
                <strong><?php echo e($nameOf($product['name'])); ?></strong>
                <?php if ($bnOf($product['name']) !== '') { ?><br><span class="muted" lang="bn"><?php echo e($bnOf($product['name'])); ?></span><?php } ?>
              </td>
              <td><code><?php echo e($product['slug']); ?></code></td>
              <td>
                <?php if (!empty($product['badge'])) { ?>
                  <span class="badge badge--<?php echo e($product['badge']); ?>"><?php echo e($product['badge']); ?></span>
                <?php } else { ?><span class="muted">—</span><?php } ?>
              </td>
              <td style="text-align:right">
                <div class="row-actions" style="justify-content:flex-end">
                  <a class="link" href="product.php?slug=<?php echo e($product['slug']); ?>">Edit</a>
                  <form method="post" action="delete-product.php" onsubmit="return confirm('Delete this product? This cannot be undone.');">
                    <?php csrf_field(); ?>
                    <input type="hidden" name="slug" value="<?php echo e($product['slug']); ?>">
                    <button class="danger" type="submit">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } ?>
  </section>
<?php } ?>

<?php if (isset($grouped[''])) { ?>
  <section class="panel">
    <div class="panel__head"><h2>Unassigned products</h2><small><?php echo count($grouped['']); ?></small></div>
    <p class="muted">These products have a category that no longer exists. Edit them and choose a valid category.</p>
    <?php foreach ($grouped[''] as $product) { ?>
      <p><a class="link" href="product.php?slug=<?php echo e($product['slug']); ?>"><?php echo e($nameOf($product['name'])); ?></a></p>
    <?php } ?>
  </section>
<?php } ?>
<?php admin_footer(); ?>
