<?php

/**
 * The class that holds all general (not public/admin) setup functionality.
 *
 * @since		1.0.0
 * @since		1.3.2	Refactored class from object to static methods.
 *
 * @package		VUWU
 * @subpackage	VUWU/includes
 */
class VUWU_Setup {

	/**
	 * Registers the custom post type for slides and channels.
	 *
	 * @since 	1.0.0
	 * @since	1.3.2	Changed method to static.
	 *
	 * @return void
	 */
	static function register_post_types() {

		register_post_type( VUWU_Display::post_type_name,
			array(
				'labels' => array(
					'name' => _x( 'Displays', 'display cpt', 'vuwu' ),
					'singular_name' => _x( 'Display', 'display cpt', 'vuwu'),
					'add_new' =>  _x( 'Add New', 'display cpt', 'vuwu'),
					'new_item' => _x( 'New display', 'display cpt', 'vuwu' ),
					'view_item' => _x( 'View display', 'display cpt', 'vuwu' ),
					'add_new_item' => _x( 'Add new display', 'display cpt', 'vuwu' ),
					'edit_item' => _x( 'Edit display', 'display cpt', 'vuwu' ),
				),
				'public' => true,
				'has_archive' => false,
				'show_in_menu' => 'vuwu',
				'show_in_admin_bar' => true,
	  			'supports' => array( 'title' ),
	  			'taxonomies' => array(),
	  			'rewrite' => array( 'slug' => 'vuwu' ),
			)
		);

		register_post_type( VUWU_Channel::post_type_name,
			array(
				'labels' => array(
					'name' => _x( 'Channels', 'channel cpt', 'vuwu' ),
					'singular_name' => _x( 'Channel', 'channel cpt', 'vuwu'),
					'add_new' =>  _x( 'Add New', 'channel cpt', 'vuwu'),
					'new_item' => _x( 'New channel', 'channel cpt', 'vuwu' ),
					'view_item' => _x( 'View channel', 'channel cpt', 'vuwu' ),
					'add_new_item' => _x( 'Add new channel', 'channel cpt', 'vuwu' ),
					'edit_item' => _x( 'Edit channel', 'channel cpt', 'vuwu' ),
				),
				'public' => true,
				'has_archive' => false,
				'show_in_menu' => 'vuwu',
				'show_in_admin_bar' => true,
	  			'supports' => array( 'title' ),
	  			'taxonomies' => array(),
	  			'rewrite' => false,
			)
		);

		register_post_type( VUWU_Slide::post_type_name,
			array(
				'labels' => array(
                                       'name' => _x( 'Content', 'slide cpt', 'vuwu' ),
					'singular_name' => _x( 'Slide', 'slide cpt', 'vuwu' ),
					'add_new' =>  _x( 'Add New', 'slide cpt', 'vuwu'),
					'new_item' => _x( 'New slide', 'slide cpt', 'vuwu' ),
					'view_item' => _x( 'View slide', 'slide cpt', 'vuwu' ),
					'add_new_item' => _x( 'Add new slide', 'slide cpt', 'vuwu' ),
					'edit_item' => _x( 'Edit slide', 'slide cpt', 'vuwu' ),
				),
				'public' => true,
				'has_archive' => false,
				'show_in_menu' => 'vuwu',
				'show_in_admin_bar' => true,
	  			'supports' => array( 'title' ),
	  			'taxonomies' => array(),
	  			'rewrite' => false,
			)
		);
	}
}
