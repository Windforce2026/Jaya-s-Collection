<?php
/* ==========================================================================
   Jaya's Collection — admin helpers
   ========================================================================== */

function cfg()
{
    static $cfg = null;
    if ($cfg === null) {
        $cfg = require __DIR__ . '/config.php';
    }
    return $cfg;
}

/* --------------------------------------------------------------------------
   Session, security and small helpers
   -------------------------------------------------------------------------- */

function admin_session_start()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $cfg = cfg();
    session_name($cfg['session_name']);
    $secure = !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off';

    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params(array(
            'lifetime' => 0,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure'   => $secure,
        ));
    } else {
        session_set_cookie_params(0, '/', '', $secure, true);
    }
    session_start();
}

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function post_or($key, $default = '')
{
    return array_key_exists($key, $_POST) && !is_array($_POST[$key])
        ? (string)$_POST[$key]
        : $default;
}

function get_or($key, $default = '')
{
    return array_key_exists($key, $_GET) && !is_array($_GET[$key])
        ? (string)$_GET[$key]
        : $default;
}

function csrf_token()
{
    admin_session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field()
{
    echo '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_valid()
{
    $sent = isset($_POST['csrf']) ? $_POST['csrf'] : (isset($_GET['csrf']) ? $_GET['csrf'] : '');
    return is_string($sent) && $sent !== '' && hash_equals(csrf_token(), $sent);
}

function require_csrf()
{
    if (!csrf_valid()) {
        http_response_code(403);
        exit('Invalid or expired form token. Please reload the page and try again.');
    }
}

function is_setup()
{
    return strlen(trim((string)cfg()['password_hash'])) > 0;
}

function is_logged_in()
{
    admin_session_start();
    return !empty($_SESSION['admin_user']);
}

function require_login()
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function flash($message)
{
    admin_session_start();
    $_SESSION['flash'] = $message;
}

function take_flash()
{
    admin_session_start();
    if (empty($_SESSION['flash'])) {
        return '';
    }
    $message = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $message;
}

function flash_html()
{
    $message = take_flash();
    if ($message !== '') {
        echo '<div class="flash">' . e($message) . '</div>';
    }
}

function atomic_write($path, $content)
{
    $dir = dirname($path);
    if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
        return false;
    }
    $tmp = $dir . '/.' . basename($path) . '.' . bin2hex(random_bytes(4)) . '.tmp';
    if (@file_put_contents($tmp, $content, LOCK_EX) === false) {
        return false;
    }
    if (file_exists($path)) {
        @unlink($path);
    }
    if (!@rename($tmp, $path)) {
        @unlink($tmp);
        return false;
    }
    @chmod($path, 0644);
    return true;
}

function save_admin_credentials($user, $password)
{
    $payload = array(
        'admin_user'    => $user,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    );
    $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    return $json !== false && atomic_write(cfg()['config_file'], $json . "\n");
}

function verify_credentials($user, $password)
{
    $cfg = cfg();
    if ($user !== $cfg['admin_user']) {
        return false;
    }
    $hash = (string)$cfg['password_hash'];
    return $hash !== '' && password_verify($password, $hash);
}

/* --------------------------------------------------------------------------
   Data helpers
   -------------------------------------------------------------------------- */

function default_settings()
{
    return array(
        'whatsapp' => '919831917368',
        'store'    => array(
            'phone'     => '+91 98319 17368',
            'phoneHref' => 'tel:+919831917368',
            'email'     => 'jayalahiri1972@gmail.com',
            'address'   => array(
                'en' => 'Ballygunge New Market (AC), 1st Floor, Shop 56, near Gariahat More, Kolkata 700029',
                'bn' => 'বালিগঞ্জ নিউ মার্কেট (এসি), ১ম তলা, দোকান ৫৬, গড়িয়াহাট মোড়ের কাছে, কলকাতা ৭০০০১৯',
            ),
            'map'      => 'https://www.google.com/maps/place/Jaya%27s+Collection/@22.5212639,88.3648577,17z/data=!4m6!3m5!1s0x3a02777497674ea1:0xa027e36d886186dc!8m2!3d22.5212639!4d88.3648577!16s%2Fg%2F11mzmw6kbf',
            'facebook' => 'https://www.facebook.com/people/Jayas-Collection/61576726888077/',
        ),
    );
}

function load_store()
{
    $defaults = array('categories' => array(), 'products' => array(), 'settings' => default_settings());
    $raw = @file_get_contents(cfg()['data_file']);
    if ($raw === false || trim($raw) === '') {
        return $defaults;
    }
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        return $defaults;
    }
    $data['categories'] = isset($data['categories']) && is_array($data['categories']) ? $data['categories'] : array();
    $data['products']   = isset($data['products']) && is_array($data['products']) ? $data['products'] : array();
    $settings           = isset($data['settings']) && is_array($data['settings']) ? $data['settings'] : array();
    $defaults['settings'] = array_merge($defaults['settings'], $settings);
    if (!isset($defaults['settings']['store']) || !is_array($defaults['settings']['store'])) {
        $defaults['settings']['store'] = default_settings()['store'];
    }
    $data['settings'] = $defaults['settings'];
    return $data;
}

function bilingual_value($value)
{
    $value = is_array($value) ? $value : array();
    $en = trim((string)(isset($value['en']) ? $value['en'] : ''));
    $bn = trim((string)(isset($value['bn']) ? $value['bn'] : ''));
    if ($bn === '') {
        $bn = $en; // site policy: never leave the Bengali string empty
    }
    return array('en' => $en, 'bn' => $bn);
}

function slugify($value)
{
    $value = strtolower(trim((string)$value));
    $value = preg_replace('/[^\p{L}\p{N}]+/u', '-', $value);
    $value = trim((string)$value, '-');
    return $value !== '' ? $value : 'item';
}

function unique_slug($slug, $products, $ignore = '')
{
    $used = array();
    foreach ($products as $product) {
        if (isset($product['slug']) && $product['slug'] !== $ignore) {
            $used[$product['slug']] = true;
        }
    }
    $base = $slug;
    $n = 2;
    while (isset($used[$slug])) {
        $slug = $base . '-' . $n;
        $n++;
    }
    return $slug;
}

function normalize_store($store)
{
    $categories = array();
    $usedCat = array();
    foreach ((array)(isset($store['categories']) ? $store['categories'] : array()) as $cat) {
        if (!is_array($cat) || empty($cat['id'])) {
            continue;
        }
        $id = slugify($cat['id']);
        if (isset($usedCat[$id])) {
            continue;
        }
        $usedCat[$id] = true;
        $categories[] = array(
            'id'    => $id,
            'name'  => bilingual_value(isset($cat['name']) ? $cat['name'] : array()),
            'blurb' => bilingual_value(isset($cat['blurb']) ? $cat['blurb'] : array()),
            'image' => trim((string)(isset($cat['image']) ? $cat['image'] : '')),
        );
    }

    $products = array();
    $usedSlug = array();
    foreach ((array)(isset($store['products']) ? $store['products'] : array()) as $product) {
        if (!is_array($product)) {
            continue;
        }
        $slug = slugify(isset($product['slug']) ? $product['slug'] : '');
        if (isset($usedSlug[$slug])) {
            $slug = unique_slug($slug, $products);
        }
        $usedSlug[$slug] = true;
        $badge = isset($product['badge']) ? (string)$product['badge'] : '';
        if (!in_array($badge, array('', 'new', 'bestseller', 'bridal'), true)) {
            $badge = '';
        }
        $products[] = array(
            'slug'     => $slug,
            'category' => trim((string)(isset($product['category']) ? $product['category'] : '')),
            'name'     => bilingual_value(isset($product['name']) ? $product['name'] : array()),
            'badge'    => $badge,
            'image'    => trim((string)(isset($product['image']) ? $product['image'] : '')),
            'desc'     => bilingual_value(isset($product['desc']) ? $product['desc'] : array()),
            'style'    => bilingual_value(isset($product['style']) ? $product['style'] : array()),
            'occasion' => bilingual_value(isset($product['occasion']) ? $product['occasion'] : array()),
        );
    }

    $settings = isset($store['settings']) && is_array($store['settings']) ? $store['settings'] : array();
    $defaults = default_settings();
    $settings = array_merge($defaults, $settings);
    if (!isset($settings['store']) || !is_array($settings['store'])) {
        $settings['store'] = $defaults['store'];
    }

    return array(
        'categories' => $categories,
        'products'   => $products,
        'settings'   => $settings,
    );
}

function json_flags()
{
    $flags = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
    if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
        $flags |= JSON_INVALID_UTF8_SUBSTITUTE;
    }
    return $flags;
}

function js_literal($value)
{
    $json = json_encode($value, json_flags());
    return $json === false ? 'null' : $json;
}

function regenerate_public_data($store)
{
    $header = "/* ==========================================================================\n"
        . "   Jaya's Collection — product & collection data\n"
        . "   Generated by the admin panel on " . date('c') . ". Do not edit by hand.\n"
        . "   Use admin/ to change products, categories and store details.\n"
        . "   ========================================================================== */\n\n";

    $js = $header
        . "const CATEGORIES = " . js_literal($store['categories']) . ";\n\n"
        . "const PRODUCTS = " . js_literal($store['products']) . ";\n\n"
        . "const WHATSAPP_NUMBER = " . js_literal($store['settings']['whatsapp']) . ";\n\n"
        . "const STORE = " . js_literal($store['settings']['store']) . ";\n";

    return atomic_write(cfg()['public_data_file'], $js);
}

function save_store($store)
{
    $store = normalize_store($store);
    $json = json_encode($store, json_flags());
    if ($json === false || !atomic_write(cfg()['data_file'], $json . "\n")) {
        return false;
    }
    return regenerate_public_data($store);
}

/* --------------------------------------------------------------------------
   Images
   -------------------------------------------------------------------------- */

function list_images()
{
    $cfg = cfg();
    $dir = $cfg['upload_dir'];
    $images = array();
    if (!is_dir($dir)) {
        return $images;
    }
    foreach (scandir($dir) as $file) {
        if ($file === '.' || $file === '..' || $file[0] === '.') {
            continue;
        }
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (!in_array($ext, array('jpg', 'jpeg', 'png', 'webp', 'gif'), true)) {
            continue;
        }
        $images[] = $cfg['upload_url'] . $file;
    }
    sort($images, SORT_NATURAL | SORT_FLAG_CASE);
    return $images;
}

function handle_image_upload($file, $fallbackPath, $prefix = 'product')
{
    if (!is_array($file) || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return array($fallbackPath, null);
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return array(null, 'Upload failed with error code ' . (int)$file['error'] . '.');
    }

    $cfg = cfg();
    $maxBytes = (int)$cfg['max_upload_mb'] * 1024 * 1024;
    if ((int)$file['size'] > $maxBytes) {
        return array(null, 'Image must be smaller than ' . (int)$cfg['max_upload_mb'] . ' MB.');
    }

    $tmp = $file['tmp_name'];
    $mime = '';
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $mime = (string)finfo_file($finfo, $tmp);
            finfo_close($finfo);
        }
    }
    if ($mime === '' && function_exists('mime_content_type')) {
        $mime = (string)mime_content_type($tmp);
    }
    if ($mime === '') {
        $info = @getimagesize($tmp);
        if (is_array($info) && isset($info['mime'])) {
            $mime = (string)$info['mime'];
        }
    }

    $allowed = array(
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    );
    if (!isset($allowed[$mime])) {
        return array(null, 'Only JPG, PNG, WebP or GIF images are allowed.');
    }
    if (@getimagesize($tmp) === false) {
        return array(null, 'That file is not a valid image.');
    }

    if (!is_dir($cfg['upload_dir']) && !@mkdir($cfg['upload_dir'], 0775, true) && !is_dir($cfg['upload_dir'])) {
        return array(null, 'The image folder is missing and could not be created.');
    }
    if (!is_writable($cfg['upload_dir'])) {
        return array(null, 'The image folder is not writable. Set its permissions to 755 or 775.');
    }

    $base = slugify($prefix);
    $name = $base . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
    $dest = $cfg['upload_dir'] . DIRECTORY_SEPARATOR . $name;
    if (!move_uploaded_file($tmp, $dest)) {
        return array(null, 'The image could not be saved. Check folder permissions.');
    }
    @chmod($dest, 0644);

    return array($cfg['upload_url'] . $name, null);
}

/* --------------------------------------------------------------------------
   Shared page chrome
   -------------------------------------------------------------------------- */

function admin_header($title)
{
    admin_session_start();
    ?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?php echo e($title); ?> — Jaya's Collection Admin</title>
<style>
:root{--navy:#0B1233;--gold:#C9A227;--gold-light:#E9D19A;--ivory:#F8F3EC;--cream:#FCF9F4;--ink:#17172B;--muted:#6C6879;--line:rgba(11,18,51,.14);--rose:#C2185B}
*{box-sizing:border-box}
body{margin:0;font:15px/1.6 "Segoe UI",Arial,sans-serif;color:var(--ink);background:var(--ivory)}
a{color:var(--navy)}
.top{background:var(--navy);color:var(--ivory)}
.top__inner{max-width:1100px;margin:auto;padding:18px 24px;display:flex;flex-wrap:wrap;align-items:center;gap:16px}
.brand{font-family:Georgia,serif;font-size:22px;letter-spacing:.02em;color:var(--ivory);text-decoration:none}
.brand small{display:block;font:9px/1.4 "Segoe UI",sans-serif;letter-spacing:.32em;color:var(--gold-light)}
.top nav{margin-left:auto;display:flex;flex-wrap:wrap;align-items:center;gap:18px}
.top nav a{color:rgba(248,243,236,.82);font-size:12px;letter-spacing:.08em;text-transform:uppercase;text-decoration:none}
.top nav a:hover{color:var(--gold-light)}
.top nav form{margin:0}
.top nav button{background:none;border:0;color:rgba(248,243,236,.82);font:inherit;font-size:12px;letter-spacing:.08em;text-transform:uppercase;cursor:pointer;padding:0}
.wrap{max-width:1100px;margin:auto;padding:36px 24px 80px}
h1{font:500 34px/1.2 Georgia,serif;color:var(--navy);margin:0 0 8px}
h2{font:500 24px/1.2 Georgia,serif;color:var(--navy);margin:0}
p.lead{color:var(--muted);margin:0 0 28px;max-width:720px}
.flash{background:#e8f5e9;border:1px solid #b7dfbd;color:#1b5e20;padding:12px 16px;margin:0 0 22px}
.error{background:#fdecea;border:1px solid #f3b6ae;color:#9b1c1c;padding:12px 16px;margin:0 0 22px}
.panel{background:#fff;border:1px solid var(--line);padding:22px;margin:0 0 24px}
.panel__head{display:flex;flex-wrap:wrap;align-items:baseline;gap:12px;margin-bottom:16px}
.panel__head small{color:var(--muted)}
.toolbar{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:26px}
.button,button.button{display:inline-block;background:var(--navy);color:#fff;border:1px solid var(--navy);padding:10px 18px;font-size:12px;letter-spacing:.08em;text-transform:uppercase;text-decoration:none;cursor:pointer}
.button:hover,button.button:hover{background:var(--gold);border-color:var(--gold);color:var(--navy)}
.button--ghost{background:transparent;color:var(--navy)}
.button--danger{background:transparent;border-color:#c9b6b6;color:#9b1c1c}
table{width:100%;border-collapse:collapse}
th,td{text-align:left;padding:12px 10px;border-bottom:1px solid var(--line-soft);vertical-align:middle}
th{font-size:10px;letter-spacing:.14em;text-transform:uppercase;color:var(--muted)}
td img{width:46px;height:58px;object-fit:cover;background:var(--ivory)}
.badge{display:inline-block;font-size:10px;letter-spacing:.08em;text-transform:uppercase;padding:2px 8px;border:1px solid var(--line)}
.badge--new{border-color:var(--gold);color:#8a6d00}.badge--bestseller{border-color:#d99ab0;color:#a32355}.badge--bridal{border-color:var(--navy);color:var(--navy)}
.row-actions{display:flex;gap:10px;align-items:center}
.row-actions form{margin:0}
.link{font-size:12px;color:var(--navy)}
.danger{color:#9b1c1c;background:none;border:0;padding:0;font:inherit;font-size:12px;cursor:pointer}
.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}
.field{display:flex;flex-direction:column;gap:6px;margin-bottom:18px}
.field--full{grid-column:1/-1}
label{font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:var(--muted)}
input,select,textarea{width:100%;font:inherit;padding:10px 12px;border:1px solid var(--line);background:#fff;color:var(--ink)}
input:focus,select:focus,textarea:focus{outline:2px solid var(--gold);outline-offset:1px;border-color:var(--gold)}
textarea{min-height:96px;resize:vertical}
.hint{font-size:12px;color:var(--muted)}
.preview{width:90px;height:112px;object-fit:cover;border:1px solid var(--line);background:var(--ivory)}
.inline{display:inline}
.muted{color:var(--muted)}
@media(max-width:720px){.grid{grid-template-columns:1fr}.top nav{margin-left:0}table{font-size:13px}th:nth-child(3),td:nth-child(3){display:none}}
</style>
</head>
<body>
<div class="top">
  <div class="top__inner">
    <a class="brand" href="index.php">Jaya's Collection<small>Admin panel</small></a>
    <?php if (is_logged_in()) { ?>
    <nav>
      <a href="index.php">Products</a>
      <a href="categories.php">Categories</a>
      <a href="settings.php">Store details</a>
      <a href="password.php">Password</a>
      <form method="post" action="logout.php"><?php csrf_field(); ?><button type="submit">Log out</button></form>
    </nav>
    <?php } ?>
  </div>
</div>
<div class="wrap">
<?php
}

function admin_footer()
{
    echo '</div></body></html>';
}
