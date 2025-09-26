<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * Extend this file to remove any stored plugin data during uninstall.
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
