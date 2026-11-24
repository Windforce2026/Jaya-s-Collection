<?php
require __DIR__ . '/lib.php';
admin_session_start();

if (!is_setup()) {
    header('Location: setup.php');
    exit;
}
if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $user = trim(post_or('username'));
    $pass = post_or('password');

    if (verify_credentials($user, $pass)) {
        session_regenerate_id(true);
        $_SESSION['admin_user'] = $user;
        flash('Welcome back.');
        header('Location: index.php');
        exit;
    }
    $error = 'Those details did not match. Please try again.';
}

admin_header('Admin login');
flash_html();
?>
<h1>Admin login</h1>
<p class="lead">Manage products, categories and photos for Jaya's Collection.</p>

<?php if ($error !== '') { ?><div class="error"><?php echo e($error); ?></div><?php } ?>

<div class="panel" style="max-width:520px">
  <form method="post" action="login.php">
    <?php csrf_field(); ?>
    <div class="field">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" autocomplete="username" value="<?php echo e(post_or('username')); ?>" required>
    </div>
    <div class="field">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" autocomplete="current-password" required>
    </div>
    <button class="button" type="submit">Log in</button>
  </form>
</div>
<?php admin_footer(); ?>
