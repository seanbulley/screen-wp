<?php

/**
 * The class that holds all functionality to support add-ons.
 *
 * @since		1.7.2
 *
 * @package		Recspectra
 * @subpackage	Recspectra/includes
 */
class Recspectra_Addons {

	/**
	 * Triggers the 'recspectra_loaded' action so add-ons can initialize.
	 *
	 * @since 	1.7.2
	 *
	 * @return void
	 */
	static function trigger_recspectra_loaded() {
		do_action( 'recspectra_loaded' );
	}
}
