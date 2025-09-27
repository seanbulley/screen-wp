<?php

/**
 * The class that holds all functionality to support add-ons.
 *
 * @since		1.7.2
 *
 * @package		VUWU
 * @subpackage	VUWU/includes
 */
class VUWU_Addons {

	/**
	 * Triggers the 'vuwu_loaded' action so add-ons can initialize.
	 *
	 * @since 	1.7.2
	 *
	 * @return void
	 */
	static function trigger_vuwu_loaded() {
		do_action( 'vuwu_loaded' );
	}
}
