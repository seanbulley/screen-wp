<?php

/**
 * Plugin bootstrap for Recspectra.
 *
 * This file registers the activation and deactivation hooks and starts the
 * plugin execution.
 *
 * @since 1.0.0
 * @package Recspectra
 *
 * @wordpress-plugin
 * Plugin Name: Recspectra
 * Description: Digital signage tools for WordPress.
 * Version: 1.7.5
 * Author: Recspectra
 * Text Domain: recspectra
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Run activation logic for Recspectra.
 */
function activate_recspectra() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-recspectra-activator.php';
	Recspectra_Activator::activate();
}

/**
 * Run deactivation logic for Recspectra.
 */
function deactivate_recspectra() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-recspectra-deactivator.php';
	Recspectra_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_recspectra' );
register_deactivation_hook( __FILE__, 'deactivate_recspectra' );

/**
 * Load the core plugin class.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-recspectra.php';

/**
 * Begin executing the plugin.
 */
function run_recspectra() {

	define( 'RECSPECTRA_PLUGIN_VERSION', '1.7.5' ); // do not access directly
	define( 'RECSPECTRA_PLUGIN_NAME', 'recspectra' ); // do not access directly
	define( 'RECSPECTRA_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
	define( 'RECSPECTRA_PLUGIN_URL', trailingslashit( plugins_url( '', __FILE__ ) ) );
	define( 'RECSPECTRA_PLUGIN_FILE', __FILE__ );

	Recspectra::init();
}

run_recspectra();
