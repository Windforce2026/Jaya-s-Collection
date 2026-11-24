<?php
require __DIR__ . '/lib.php';
require_login();

$store = load_store();
$settings = $store['settings'];
$shop = $settings['store'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $whatsapp = (string)preg_replace('/[^0-9]/', '', post_or('whatsapp'));
    $phone = trim(post_or('phone'));
    $phoneHref = trim(post_or('phone_href'));
    $email = trim(post_or('email'));

    if ($whatsapp === '' || $phone === '' || $phoneHref === '' || $email === '') {
        $error = 'WhatsApp number, phone, tel: link and email are required.';
    } else {
        $store['settings'] = array(
            'whatsapp' => $whatsapp,
            'store'    => array(
                'phone'     => $phone,
                'phoneHref' => $phoneHref,
                'email'     => $email,
                'address'   => bilingual_value(array(
                    'en' => post_or('address_en'),
                    'bn' => post_or('address_bn'),
                )),
                'map'      => trim(post_or('map')),
                'facebook' => trim(post_or('facebook')),
            ),
        );
        if (save_store($store)) {
            flash('Store details updated. The site is up to date.');
            header('Location: settings.php');
            exit;
        }
        $error = 'Could not save. Check that admin/data/ and js/data.js are writable.';
    }
}

admin_header('Store details');
flash_html();
?>
<h1>Store details</h1>
<p class="lead">These values are written into <code>js/data.js</code>. The WhatsApp number here is used by the product page and enquiry form.</p>

<?php if ($error !== '') { ?><div class="error"><?php echo e($error); ?></div><?php } ?>

<div class="panel">
<form method="post" action="settings.php">
  <?php csrf_field(); ?>
  <div class="grid">
    <div class="field">
      <label for="whatsapp">WhatsApp number (digits only, with country code)</label>
      <input type="text" id="whatsapp" name="whatsapp" value="<?php echo e($settings['whatsapp']); ?>" placeholder="919831917368">
    </div>
    <div class="field">
      <label for="phone">Phone shown on the site</label>
      <input type="text" id="phone" name="phone" value="<?php echo e($shop['phone']); ?>" placeholder="+91 98319 17368">
    </div>
    <div class="field">
      <label for="phone_href">tel: link</label>
      <input type="text" id="phone_href" name="phone_href" value="<?php echo e($shop['phoneHref']); ?>" placeholder="tel:+919831917368">
    </div>
    <div class="field">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" value="<?php echo e($shop['email']); ?>">
    </div>
    <div class="field field--full">
      <label for="address_en">Address — English</label>
      <input type="text" id="address_en" name="address_en" value="<?php echo e($shop['address']['en']); ?>">
    </div>
    <div class="field field--full">
      <label for="address_bn">Address — Bengali</label>
      <input type="text" id="address_bn" name="address_bn" lang="bn" value="<?php echo e($shop['address']['bn']); ?>">
    </div>
    <div class="field field--full">
      <label for="map">Google Maps place URL</label>
      <input type="url" id="map" name="map" value="<?php echo e($shop['map']); ?>">
    </div>
    <div class="field field--full">
      <label for="facebook">Facebook page URL</label>
      <input type="url" id="facebook" name="facebook" value="<?php echo e($shop['facebook']); ?>">
    </div>
  </div>
  <div class="toolbar" style="margin:8px 0 0">
    <button class="button" type="submit">Save store details</button>
    <a class="button button--ghost" href="index.php">Back to products</a>
  </div>
</form>
</div>
<?php admin_footer(); ?>
