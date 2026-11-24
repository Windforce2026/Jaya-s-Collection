<?php
require __DIR__ . '/lib.php';
require_login();

$error = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $current = post_or('current');
    $new = post_or('new');
    $confirm = post_or('confirm');

    if (!verify_credentials(cfg()['admin_user'], $current)) {
        $error = 'Your current password is not correct.';
    } elseif (strlen($new) < 8) {
        $error = 'The new password must be at least 8 characters.';
    } elseif ($new !== $confirm) {
        $error = 'The two new passwords do not match.';
    } elseif (!save_admin_credentials(cfg()['admin_user'], $new)) {
        $error = 'Could not save the new password. Check that admin/data/ is writable (755 or 775).';
    } else {
        $message = 'Password changed successfully.';
    }
}

admin_header('Change password');
flash_html();
?>
<h1>Change password</h1>
<p class="lead">Use a strong password and store it safely. There is no password-reset email on this setup.</p>

<?php if ($error !== '') { ?><div class="error"><?php echo e($error); ?></div><?php } ?>
<?php if ($message !== '') { ?><div class="flash"><?php echo e($message); ?></div><?php } ?>

<div class="panel" style="max-width:520px">
  <form method="post" action="password.php">
    <?php csrf_field(); ?>
    <div class="field">
      <label for="current">Current password</label>
      <input type="password" id="current" name="current" autocomplete="current-password" required>
    </div>
    <div class="field">
      <label for="new">New password (8+ characters)</label>
      <input type="password" id="new" name="new" autocomplete="new-password" required>
    </div>
    <div class="field">
      <label for="confirm">Confirm new password</label>
      <input type="password" id="confirm" name="confirm" autocomplete="new-password" required>
    </div>
    <button class="button" type="submit">Change password</button>
  </form>
</div>
<?php admin_footer(); ?>
