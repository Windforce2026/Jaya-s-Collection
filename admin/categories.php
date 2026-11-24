<?php
require __DIR__ . '/lib.php';
require_login();

$store = load_store();
$categories = $store['categories'];
$products = $store['products'];
$images = list_images();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $action = post_or('action');
    $id = trim(post_or('id'));

    $nameEn = trim(post_or('name_en'));
    $nameBn = trim(post_or('name_bn'));
    $blurbEn = trim(post_or('blurb_en'));
    $blurbBn = trim(post_or('blurb_bn'));
    $image = trim(post_or('image'));

    if ($action === 'save') {
        $updated = false;
        foreach ($categories as $i => $cat) {
            if ($cat['id'] === $id) {
                $categories[$i]['name'] = bilingual_value(array('en' => $nameEn, 'bn' => $nameBn));
                $categories[$i]['blurb'] = bilingual_value(array('en' => $blurbEn, 'bn' => $blurbBn));
                $categories[$i]['image'] = $image;
                $updated = true;
                break;
            }
        }
        if (!$updated) {
            $error = 'That category could not be found.';
        } elseif ($nameEn === '') {
            $error = 'Please enter the English category name.';
        } else {
            $store['categories'] = $categories;
            if (save_store($store)) {
                flash('Category updated. The site is up to date.');
                header('Location: categories.php');
                exit;
            }
            $error = 'Could not save. Check that admin/data/ and js/data.js are writable.';
        }
    } elseif ($action === 'add') {
        $baseId = slugify(post_or('category_id') !== '' ? post_or('category_id') : $nameEn);
        $newId = $baseId;
        $n = 2;
        $idExists = function ($candidate) use ($categories) {
            foreach ($categories as $cat) {
                if ($cat['id'] === $candidate) {
                    return true;
                }
            }
            return false;
        };
        while ($idExists($newId)) {
            $newId = $baseId . '-' . $n;
            $n++;
        }
        if ($nameEn === '') {
            $error = 'Please enter the English category name.';
        } else {
            $categories[] = array(
                'id'    => $newId,
                'name'  => bilingual_value(array('en' => $nameEn, 'bn' => $nameBn)),
                'blurb' => bilingual_value(array('en' => $blurbEn, 'bn' => $blurbBn)),
                'image' => $image,
            );
            $store['categories'] = $categories;
            if (save_store($store)) {
                flash('Category added. It is now available in the product form and collection filters.');
                header('Location: categories.php');
                exit;
            }
            $error = 'Could not save. Check that admin/data/ and js/data.js are writable.';
        }
    } elseif ($action === 'delete') {
        $inUse = 0;
        foreach ($products as $product) {
            if ($product['category'] === $id) {
                $inUse++;
            }
        }
        if ($inUse > 0) {
            $error = 'This category still has ' . $inUse . ' product(s). Move or delete them first.';
        } elseif (count($categories) <= 1) {
            $error = 'At least one category is required.';
        } else {
            $remaining = array();
            foreach ($categories as $cat) {
                if ($cat['id'] !== $id) {
                    $remaining[] = $cat;
                }
            }
            $store['categories'] = $remaining;
            if (save_store($store)) {
                flash('Category deleted.');
                header('Location: categories.php');
                exit;
            }
            $error = 'Could not save. Check that admin/data/ and js/data.js are writable.';
        }
    }
}

$imageOptions = function ($selected) use ($images) {
    $all = $images;
    if ($selected !== '' && !in_array($selected, $all, true)) {
        $all[] = $selected;
    }
    echo '<option value="">Choose a photo…</option>';
    foreach ($all as $image) {
        echo '<option value="' . e($image) . '"' . ($selected === $image ? ' selected' : '') . '>' . e(basename($image)) . '</option>';
    }
};

admin_header('Categories');
flash_html();
?>
<h1>Categories</h1>
<p class="lead">The category ID is the link used in <code>collections.html#category</code>, so it cannot be changed after creation. Names, blurbs and photos can be edited any time.</p>

<?php if ($error !== '') { ?><div class="error"><?php echo e($error); ?></div><?php } ?>

<?php foreach ($categories as $cat) { ?>
  <section class="panel">
    <form method="post" action="categories.php">
      <?php csrf_field(); ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?php echo e($cat['id']); ?>">
      <div class="panel__head">
        <h2><?php echo e(isset($cat['name']['en']) ? $cat['name']['en'] : $cat['id']); ?></h2>
        <small><code><?php echo e($cat['id']); ?></code></small>
      </div>
      <div class="grid">
        <div class="field">
          <label>Name — English</label>
          <input type="text" name="name_en" value="<?php echo e($cat['name']['en']); ?>" required>
        </div>
        <div class="field">
          <label>Name — Bengali</label>
          <input type="text" name="name_bn" lang="bn" value="<?php echo e($cat['name']['bn']); ?>">
        </div>
        <div class="field field--full">
          <label>Short blurb — English</label>
          <input type="text" name="blurb_en" value="<?php echo e($cat['blurb']['en']); ?>">
        </div>
        <div class="field field--full">
          <label>Short blurb — Bengali</label>
          <input type="text" name="blurb_bn" lang="bn" value="<?php echo e($cat['blurb']['bn']); ?>">
        </div>
        <div class="field field--full">
          <label>Category photo</label>
          <select name="image"><?php $imageOptions($cat['image']); ?></select>
        </div>
      </div>
      <div class="toolbar" style="margin:0">
        <button class="button" type="submit">Save category</button>
      </div>
    </form>
    <form method="post" action="categories.php" style="margin-top:14px" onsubmit="return confirm('Delete this category? Products using it must be moved first.');">
      <?php csrf_field(); ?>
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="id" value="<?php echo e($cat['id']); ?>">
      <button class="button button--danger" type="submit">Delete category</button>
    </form>
  </section>
<?php } ?>

<section class="panel">
  <div class="panel__head"><h2>Add a category</h2></div>
  <form method="post" action="categories.php">
    <?php csrf_field(); ?>
    <input type="hidden" name="action" value="add">
    <div class="grid">
      <div class="field">
        <label>Name — English *</label>
        <input type="text" name="name_en" value="<?php echo e(post_or('name_en')); ?>" required>
      </div>
      <div class="field">
        <label>Name — Bengali</label>
        <input type="text" name="name_bn" lang="bn" value="<?php echo e(post_or('name_bn')); ?>">
      </div>
      <div class="field">
        <label>ID <span class="hint">(blank = from English name)</span></label>
        <input type="text" name="category_id" value="<?php echo e(post_or('category_id')); ?>" placeholder="watches">
      </div>
      <div class="field">
        <label>Category photo</label>
        <select name="image"><?php $imageOptions(post_or('image')); ?></select>
      </div>
      <div class="field field--full">
        <label>Short blurb — English</label>
        <input type="text" name="blurb_en" value="<?php echo e(post_or('blurb_en')); ?>">
      </div>
      <div class="field field--full">
        <label>Short blurb — Bengali</label>
        <input type="text" name="blurb_bn" lang="bn" value="<?php echo e(post_or('blurb_bn')); ?>">
      </div>
    </div>
    <button class="button" type="submit">Add category</button>
  </form>
</section>
<?php admin_footer(); ?>
