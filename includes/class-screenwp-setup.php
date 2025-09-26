<?php

/**
 * The class that holds all general (not public/admin) setup functionality.
 *
 * @since		1.0.0
 * @since		1.3.2	Refactored class from object to static methods.
 *
 * @package		ScreenWP
 * @subpackage	ScreenWP/includes
 */
class ScreenWP_Setup {

	/**
	 * Registers the custom post type for slides and channels.
	 *
	 * @since 	1.0.0
	 * @since	1.3.2	Changed method to static.
	 *
	 * @return void
	 */
	static function register_post_types() {

		register_post_type( ScreenWP_Display::post_type_name,
			array(
				'labels' => array(
					'name' => _x( 'Displays', 'display cpt', 'screenwp' ),
					'singular_name' => _x( 'Display', 'display cpt', 'screenwp'),
					'add_new' =>  _x( 'Add New', 'display cpt', 'screenwp'),
					'new_item' => _x( 'New display', 'display cpt', 'screenwp' ),
					'view_item' => _x( 'View display', 'display cpt', 'screenwp' ),
					'add_new_item' => _x( 'Add new display', 'display cpt', 'screenwp' ),
					'edit_item' => _x( 'Edit display', 'display cpt', 'screenwp' ),
				),
				'public' => true,
				'has_archive' => false,
				'show_in_menu' => 'screenwp',
				'show_in_admin_bar' => true,
	  			'supports' => array( 'title' ),
	  			'taxonomies' => array(),
	  			'rewrite' => array( 'slug' => 'screenwp' ),
			)
		);

		register_post_type( ScreenWP_Channel::post_type_name,
			array(
				'labels' => array(
					'name' => _x( 'Channels', 'channel cpt', 'screenwp' ),
					'singular_name' => _x( 'Channel', 'channel cpt', 'screenwp'),
					'add_new' =>  _x( 'Add New', 'channel cpt', 'screenwp'),
					'new_item' => _x( 'New channel', 'channel cpt', 'screenwp' ),
					'view_item' => _x( 'View channel', 'channel cpt', 'screenwp' ),
					'add_new_item' => _x( 'Add new channel', 'channel cpt', 'screenwp' ),
					'edit_item' => _x( 'Edit channel', 'channel cpt', 'screenwp' ),
				),
				'public' => true,
				'has_archive' => false,
				'show_in_menu' => 'screenwp',
				'show_in_admin_bar' => true,
	  			'supports' => array( 'title' ),
	  			'taxonomies' => array(),
	  			'rewrite' => false,
			)
		);

		register_post_type( ScreenWP_Slide::post_type_name,
			array(
				'labels' => array(
					'name' => _x( 'Slides', 'slide cpt', 'screenwp' ),
					'singular_name' => _x( 'Slide', 'slide cpt', 'screenwp' ),
					'add_new' =>  _x( 'Add New', 'slide cpt', 'screenwp'),
					'new_item' => _x( 'New slide', 'slide cpt', 'screenwp' ),
					'view_item' => _x( 'View slide', 'slide cpt', 'screenwp' ),
					'add_new_item' => _x( 'Add new slide', 'slide cpt', 'screenwp' ),
					'edit_item' => _x( 'Edit slide', 'slide cpt', 'screenwp' ),
				),
				'public' => true,
				'has_archive' => false,
				'show_in_menu' => 'screenwp',
				'show_in_admin_bar' => true,
	  			'supports' => array( 'title' ),
	  			'taxonomies' => array(),
	  			'rewrite' => false,
			)
		);
	}
}
