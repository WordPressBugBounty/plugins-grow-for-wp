<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This plugin requires WordPress' );
}

// Environment setup.
define('GROW_PLUGIN_FILE', __DIR__ . '/grow-for-wp.php');
define('GROW_PLUGIN_BASENAME', plugin_basename(__DIR__ . '/grow-for-wp.php'));
define('GROW_PLUGIN_DIR', __DIR__);
define('GROW_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
define('GROW_TEXT_DOMAIN', 'grow');
