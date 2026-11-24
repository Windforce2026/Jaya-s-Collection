<?php
/**
 * Admin configuration.
 *
 * The first-run password setup writes admin/data/config.json with the
 * admin username and a bcrypt password hash. Paths below are relative to
 * this file so the folder can be dropped into any cPanel/Hostinger account.
 */

$cfg = array(
    'admin_user'       => 'admin',
    'password_hash'    => '',
    'data_file'        => __DIR__ . '/data/store.json',
    'config_file'      => __DIR__ . '/data/config.json',
    'public_data_file' => dirname(__DIR__) . '/js/data.js',
    'upload_dir'       => dirname(__DIR__) . '/assets/img',
    'upload_url'       => 'assets/img/',
    'max_upload_mb'    => 3,
    'session_name'     => 'jc_admin',
);

$override = __DIR__ . '/data/config.json';
if (is_file($override)) {
    $saved = json_decode((string)@file_get_contents($override), true);
    if (is_array($saved)) {
        foreach (array('admin_user', 'password_hash') as $key) {
            if (array_key_exists($key, $saved)) {
                $cfg[$key] = $saved[$key];
            }
        }
    }
}

return $cfg;
