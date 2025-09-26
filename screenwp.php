<?php

/**
 * Plugin bootstrap for ScreenWP.
 *
 * This file registers the activation and deactivation hooks and starts the
 * plugin execution.
 *
 * @since 1.0.0
 * @package ScreenWP
 *
 * @wordpress-plugin
 * Plugin Name: ScreenWP
 * Description: Digital signage tools for WordPress.
 * Version: 1.7.5
 * Author: ScreenWP
 * Text Domain: screenwp
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Run activation logic for ScreenWP.
 */
function activate_screenwp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-screenwp-activator.php';
	ScreenWP_Activator::activate();
}

/**
 * Run deactivation logic for ScreenWP.
 */
function deactivate_screenwp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-screenwp-deactivator.php';
	ScreenWP_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_screenwp' );
register_deactivation_hook( __FILE__, 'deactivate_screenwp' );

/**
 * Load the core plugin class.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-screenwp.php';

/**
 * Begin executing the plugin.
 */
function run_screenwp() {

	define( 'SCREENWP_PLUGIN_VERSION', '1.7.5' ); // do not access directly
	define( 'SCREENWP_PLUGIN_NAME', 'screenwp' ); // do not access directly
	define( 'SCREENWP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
	define( 'SCREENWP_PLUGIN_URL', trailingslashit( plugins_url( '', __FILE__ ) ) );
	define( 'SCREENWP_PLUGIN_FILE', __FILE__ );

	ScreenWP::init();
}

run_screenwp();
