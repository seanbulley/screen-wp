<?php

/**
 * The class that holds all general (not public/admin) setup functionality.
 *
 * @since		1.0.0
 * @since		1.3.2	Refactored class from object to static methods.
 *
 * @package		Recspectra
 * @subpackage	Recspectra/includes
 */
class Recspectra_Setup {

	/**
	 * Registers the custom post type for slides and channels.
	 *
	 * @since 	1.0.0
	 * @since	1.3.2	Changed method to static.
	 *
	 * @return void
	 */
	static function register_post_types() {

		register_post_type( Recspectra_Display::post_type_name,
			array(
				'labels' => array(
					'name' => _x( 'Displays', 'display cpt', 'recspectra' ),
					'singular_name' => _x( 'Display', 'display cpt', 'recspectra'),
					'add_new' =>  _x( 'Add New', 'display cpt', 'recspectra'),
					'new_item' => _x( 'New display', 'display cpt', 'recspectra' ),
					'view_item' => _x( 'View display', 'display cpt', 'recspectra' ),
					'add_new_item' => _x( 'Add new display', 'display cpt', 'recspectra' ),
					'edit_item' => _x( 'Edit display', 'display cpt', 'recspectra' ),
				),
				'public' => true,
				'has_archive' => false,
				'show_in_menu' => 'recspectra',
				'show_in_admin_bar' => true,
	  			'supports' => array( 'title' ),
	  			'taxonomies' => array(),
	  			'rewrite' => array( 'slug' => 'recspectra' ),
			)
		);

		register_post_type( Recspectra_Channel::post_type_name,
			array(
				'labels' => array(
					'name' => _x( 'Channels', 'channel cpt', 'recspectra' ),
					'singular_name' => _x( 'Channel', 'channel cpt', 'recspectra'),
					'add_new' =>  _x( 'Add New', 'channel cpt', 'recspectra'),
					'new_item' => _x( 'New channel', 'channel cpt', 'recspectra' ),
					'view_item' => _x( 'View channel', 'channel cpt', 'recspectra' ),
					'add_new_item' => _x( 'Add new channel', 'channel cpt', 'recspectra' ),
					'edit_item' => _x( 'Edit channel', 'channel cpt', 'recspectra' ),
				),
				'public' => true,
				'has_archive' => false,
				'show_in_menu' => 'recspectra',
				'show_in_admin_bar' => true,
	  			'supports' => array( 'title' ),
	  			'taxonomies' => array(),
	  			'rewrite' => false,
			)
		);

		register_post_type( Recspectra_Slide::post_type_name,
			array(
				'labels' => array(
					'name' => _x( 'Slides', 'slide cpt', 'recspectra' ),
					'singular_name' => _x( 'Slide', 'slide cpt', 'recspectra' ),
					'add_new' =>  _x( 'Add New', 'slide cpt', 'recspectra'),
					'new_item' => _x( 'New slide', 'slide cpt', 'recspectra' ),
					'view_item' => _x( 'View slide', 'slide cpt', 'recspectra' ),
					'add_new_item' => _x( 'Add new slide', 'slide cpt', 'recspectra' ),
					'edit_item' => _x( 'Edit slide', 'slide cpt', 'recspectra' ),
				),
				'public' => true,
				'has_archive' => false,
				'show_in_menu' => 'recspectra',
				'show_in_admin_bar' => true,
	  			'supports' => array( 'title' ),
	  			'taxonomies' => array(),
	  			'rewrite' => false,
			)
		);
	}
}
