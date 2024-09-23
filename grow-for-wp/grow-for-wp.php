<?php
/**
 * Plugin Name:         Grow for WP
 * Plugin URI:          https://grow.me/publishers
 * Description:         Integrate your WordPress Site with Grow
 * Version:             1.5.0
 * Requires at least:   5.2
 * Requires PHP:        7.4
 * Author:              Grow
 * Text Domain:         grow-for-wp
 * License:             GPL2
 */

// Prevent direct access
use Grow\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This plugin requires WordPress' );
}

require_once __DIR__ . '/inc/functions-requirements.php';
require_once __DIR__ . '/constants.php';

if ( ! grow_for_wp_is_compatible() ) {
	deactivate_plugins( GROW_PLUGIN_BASENAME );
	die();
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Kicks off the entire plugin
 *
 * @return Plugin
 */
function grow_for_wp_bootstrap() {
	return new Plugin();
}


$grow_for_wp = grow_for_wp_bootstrap();
