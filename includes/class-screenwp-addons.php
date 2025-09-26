<?php

/**
 * The class that holds all functionality to support add-ons.
 *
 * @since		1.7.2
 *
 * @package		ScreenWP
 * @subpackage	ScreenWP/includes
 */
class ScreenWP_Addons {

	/**
	 * Triggers the 'screenwp_loaded' action so add-ons can initialize.
	 *
	 * @since 	1.7.2
	 *
	 * @return void
	 */
	static function trigger_screenwp_loaded() {
		do_action( 'screenwp_loaded' );
	}
}
