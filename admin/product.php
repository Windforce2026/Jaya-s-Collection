<?php
require __DIR__ . '/lib.php';
require_login();

$store = load_store();
$categories = $store['categories'];
$products = $store['products'];

$originalSlug = '';
$editing = false;

if (get_or('slug') !== '') {
    $editing = true;
    $found = null;
    foreach ($products as $product) {
        if ($product['slug'] === get_or('slug')) {
            $found = $product;
            break;
        }
    }
    if ($found === null) {
        flash('That product could not be found.');
        header('Location: index.php');
        exit;
    }
    $originalSlug = $found['slug'];
    $values = array(
        'slug'      => $found['slug'],
        'category'  => $found['category'],
        'badge'     => $found['badge'],
        'image'     => $found['image'],
        'name_en'   => $found['name']['en'],
        'name_bn'   => $found['name']['bn'],
        'desc_en'   => $found['desc']['en'],
        'desc_bn'   => $found['desc']['bn'],
        'style_en'  => $found['style']['en'],
        'style_bn'  => $found['style']['bn'],
        'occasion_en' => $found['occasion']['en'],
        'occasion_bn' => $found['occasion']['bn'],
    );
} else {
    $values = array(
        'slug'      => '',
        'category'  => get_or('category'),
        'badge'     => '',
        'image'     => '',
        'name_en'   => '',
        'name_bn'   => '',
        'desc_en'   => '',
        'desc_bn'   => '',
        'style_en'  => '',
        'style_bn'  => '',
        'occasion_en' => '',
        'occasion_bn' => '',
    );
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $values = array(
        'slug'        => trim(post_or('slug')),
        'category'    => trim(post_or('category')),
        'badge'       => trim(post_or('badge')),
        'image'       => trim(post_or('image')),
        'name_en'     => trim(post_or('name_en')),
        'name_bn'     => trim(post_or('name_bn')),
        'desc_en'     => trim(post_or('desc_en')),
        'desc_bn'     => trim(post_or('desc_bn')),
        'style_en'    => trim(post_or('style_en')),
        'style_bn'    => trim(post_or('style_bn')),
        'occasion_en' => trim(post_or('occasion_en')),
        'occasion_bn' => trim(post_or('occasion_bn')),
    );

    $validCategory = false;
    foreach ($categories as $cat) {
        if ($cat['id'] === $values['category']) {
            $validCategory = true;
            break;
        }
    }

    if ($values['name_en'] === '') {
        $error = 'Please enter the English product name.';
    } elseif (!$validCategory) {
        $error = 'Please choose a category.';
    } elseif ($values['desc_en'] === '') {
        $error = 'Please add a short English description.';
    } elseif ($values['image'] === '' && (!isset($_FILES['image_file']) || $_FILES['image_file']['error'] === UPLOAD_ERR_NO_FILE)) {
        $error = 'Please choose an existing photo or upload a new one.';
    }

    if ($error === '') {
        $slugSource = $values['slug'] !== '' ? $values['slug'] : $values['name_en'];
        $slug = unique_slug(slugify($slugSource), $products, $originalSlug);

        list($image, $uploadError) = handle_image_upload(
            isset($_FILES['image_file']) ? $_FILES['image_file'] : array(),
            $values['image'],
            $slug
        );
        if ($uploadError !== null) {
            $error = $uploadError;
        } else {
            $values['image'] = $image;
            $values['slug'] = $slug;

            $product = array(
                'slug'     => $slug,
                'category' => $values['category'],
                'name'     => bilingual_value(array('en' => $values['name_en'], 'bn' => $values['name_bn'])),
                'badge'    => in_array($values['badge'], array('', 'new', 'bestseller', 'bridal'), true) ? $values['badge'] : '',
                'image'    => $image,
                'desc'     => bilingual_value(array('en' => $values['desc_en'], 'bn' => $values['desc_bn'])),
                'style'    => bilingual_value(array('en' => $values['style_en'], 'bn' => $values['style_bn'])),
                'occasion' => bilingual_value(array('en' => $values['occasion_en'], 'bn' => $values['occasion_bn'])),
            );

            if ($editing) {
                foreach ($products as $i => $existing) {
                    if ($existing['slug'] === $originalSlug) {
                        $products[$i] = $product;
                        break;
                    }
                }
            } else {
                $products[] = $product;
            }

            $store['products'] = $products;
            if (save_store($store)) {
                flash($editing ? 'Product updated. The site is up to date.' : 'Product added. The site is up to date.');
                header('Location: index.php');
                exit;
            }
            $error = 'Could not save the product. Check that admin/data/ and js/data.js are writable.';
        }
    }
}

$v = function ($key) use ($values) {
    return isset($values[$key]) ? $values[$key] : '';
};

$images = list_images();
if ($v('image') !== '' && !in_array($v('image'), $images, true)) {
    $images[] = $v('image');
}

admin_header($editing ? 'Edit product' : 'Add product');
flash_html();
?>
<h1><?php echo $editing ? 'Edit product' : 'Add product'; ?></h1>
<p class="lead">English text is required. If you leave the Bengali field empty, the English text is used as a safe fallback.</p>

<?php if ($error !== '') { ?><div class="error"><?php echo e($error); ?></div><?php } ?>

<div class="panel">
<form method="post" action="product.php<?php echo $editing ? '?slug=' . e($originalSlug) : ''; ?>" enctype="multipart/form-data">
  <?php csrf_field(); ?>
  <div class="grid">
    <div class="field">
      <label for="name_en">Product name — English *</label>
      <input type="text" id="name_en" name="name_en" value="<?php echo e($v('name_en')); ?>" required>
    </div>
    <div class="field">
      <label for="name_bn">Product name — Bengali</label>
      <input type="text" id="name_bn" name="name_bn" lang="bn" value="<?php echo e($v('name_bn')); ?>">
    </div>

    <div class="field">
      <label for="category">Category *</label>
      <select id="category" name="category" required>
        <option value="">Choose a category…</option>
        <?php foreach ($categories as $cat) { ?>
          <option value="<?php echo e($cat['id']); ?>" <?php echo $v('category') === $cat['id'] ? 'selected' : ''; ?>>
            <?php echo e(isset($cat['name']['en']) ? $cat['name']['en'] : $cat['id']); ?>
          </option>
        <?php } ?>
      </select>
    </div>
    <div class="field">
      <label for="badge">Badge</label>
      <select id="badge" name="badge">
        <?php foreach (array('' => 'None', 'new' => 'New', 'bestseller' => 'Bestseller', 'bridal' => 'Bridal') as $key => $label) { ?>
          <option value="<?php echo e($key); ?>" <?php echo $v('badge') === $key ? 'selected' : ''; ?>><?php echo e($label); ?></option>
        <?php } ?>
      </select>
    </div>

    <div class="field field--full">
      <label for="slug">Link slug <span class="hint">(blank = generated from the English name)</span></label>
      <input type="text" id="slug" name="slug" value="<?php echo e($v('slug')); ?>" placeholder="noor-choker">
      <span class="hint">Used in <code>product.html?p=slug</code>. Changed slugs get a number added if they clash.</span>
    </div>

    <div class="field">
      <label for="image">Photo from the site</label>
      <select id="image" name="image">
        <option value="">Choose a photo…</option>
        <?php foreach ($images as $image) { ?>
          <option value="<?php echo e($image); ?>" <?php echo $v('image') === $image ? 'selected' : ''; ?>><?php echo e(basename($image)); ?></option>
        <?php } ?>
      </select>
    </div>
    <div class="field">
      <label for="image_file">Or upload a new photo</label>
      <input type="file" id="image_file" name="image_file" accept="image/jpeg,image/png,image/webp,image/gif">
      <span class="hint">JPG, PNG, WebP or GIF · max <?php echo (int)cfg()['max_upload_mb']; ?> MB · saved to assets/img/</span>
    </div>
    <div class="field field--full">
      <img id="imagePreview" class="preview" alt="Selected photo preview" src="<?php echo $v('image') !== '' ? '../' . e($v('image')) : ''; ?>">
    </div>

    <div class="field field--full">
      <label for="desc_en">Description — English *</label>
      <textarea id="desc_en" name="desc_en" required><?php echo e($v('desc_en')); ?></textarea>
    </div>
    <div class="field field--full">
      <label for="desc_bn">Description — Bengali</label>
      <textarea id="desc_bn" name="desc_bn" lang="bn"><?php echo e($v('desc_bn')); ?></textarea>
    </div>

    <div class="field">
      <label for="style_en">Style — English</label>
      <input type="text" id="style_en" name="style_en" value="<?php echo e($v('style_en')); ?>" placeholder="Choker · Floral">
    </div>
    <div class="field">
      <label for="style_bn">Style — Bengali</label>
      <input type="text" id="style_bn" name="style_bn" lang="bn" value="<?php echo e($v('style_bn')); ?>">
    </div>
    <div class="field">
      <label for="occasion_en">Occasion — English</label>
      <input type="text" id="occasion_en" name="occasion_en" value="<?php echo e($v('occasion_en')); ?>" placeholder="Festive & evening">
    </div>
    <div class="field">
      <label for="occasion_bn">Occasion — Bengali</label>
      <input type="text" id="occasion_bn" name="occasion_bn" lang="bn" value="<?php echo e($v('occasion_bn')); ?>">
    </div>
  </div>

  <div class="toolbar" style="margin:8px 0 0">
    <button class="button" type="submit"><?php echo $editing ? 'Save changes' : 'Add product'; ?></button>
    <a class="button button--ghost" href="index.php">Cancel</a>
  </div>
</form>
</div>

<script>
(function () {
  var select = document.getElementById('image');
  var file = document.getElementById('image_file');
  var preview = document.getElementById('imagePreview');
  select.addEventListener('change', function () {
    preview.src = this.value ? '../' + this.value : '';
  });
  file.addEventListener('change', function () {
    if (!this.files || !this.files[0]) return;
    var reader = new FileReader();
    reader.onload = function (e) { preview.src = e.target.result; };
    reader.readAsDataURL(this.files[0]);
  });
})();
</script>
<?php admin_footer(); ?>
