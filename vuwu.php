<?php

/**
 * Plugin bootstrap for VUWU.
 *
 * This file registers the activation and deactivation hooks and starts the
 * plugin execution.
 *
 * @since 1.0.0
 * @package VUWU
 *
 * @wordpress-plugin
 * Plugin Name: VUWU
 * Description: Digital signage tools for WordPress.
 * Version: 1.8.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Tested up to: 6.8
 * Author: VUWU
 * Text Domain: vuwu
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Run activation logic for VUWU.
 */
function activate_vuwu() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-vuwu-activator.php';
	VUWU_Activator::activate();
}

/**
 * Run deactivation logic for VUWU.
 */
function deactivate_vuwu() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-vuwu-deactivator.php';
	VUWU_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_vuwu' );
register_deactivation_hook( __FILE__, 'deactivate_vuwu' );

/**
 * Load the core plugin class.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-vuwu.php';

/**
 * Begin executing the plugin.
 */
function run_vuwu() {

        define( 'VUWU_PLUGIN_VERSION', '1.8.0' ); // do not access directly
	define( 'VUWU_PLUGIN_NAME', 'vuwu' ); // do not access directly
	define( 'VUWU_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
	define( 'VUWU_PLUGIN_URL', trailingslashit( plugins_url( '', __FILE__ ) ) );
	define( 'VUWU_PLUGIN_FILE', __FILE__ );

	VUWU::init();
}

run_vuwu();
