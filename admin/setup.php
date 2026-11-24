<?php
require __DIR__ . '/lib.php';
admin_session_start();

if (is_setup()) {
    header('Location: login.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $user = trim(post_or('username'));
    $pass = post_or('password');
    $confirm = post_or('confirm');

    if ($user === '' || strlen($user) < 3) {
        $error = 'Please choose a username of at least 3 characters.';
    } elseif (strlen($pass) < 8) {
        $error = 'Please choose a password of at least 8 characters.';
    } elseif ($pass !== $confirm) {
        $error = 'The two passwords do not match.';
    } elseif (!save_admin_credentials($user, $pass)) {
        $error = 'Could not save admin/data/config.json. Check that admin/data/ is writable (755 or 775).';
    } else {
        flash('Admin password created. Please log in.');
        header('Location: login.php');
        exit;
    }
}

admin_header('First-time setup');
flash_html();
?>
<h1>Set up the admin</h1>
<p class="lead">This one-time step creates the login for the product admin. Please use a strong password and keep it safe.</p>

<?php if ($error !== '') { ?><div class="error"><?php echo e($error); ?></div><?php } ?>

<div class="panel" style="max-width:520px">
  <form method="post" action="setup.php">
    <?php csrf_field(); ?>
    <div class="field">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" autocomplete="username" value="<?php echo e(post_or('username')); ?>" required>
    </div>
    <div class="field">
      <label for="password">Password (8+ characters)</label>
      <input type="password" id="password" name="password" autocomplete="new-password" required>
    </div>
    <div class="field">
      <label for="confirm">Confirm password</label>
      <input type="password" id="confirm" name="confirm" autocomplete="new-password" required>
    </div>
    <button class="button" type="submit">Create admin login</button>
  </form>
</div>
<?php admin_footer(); ?>
