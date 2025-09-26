<?php

/**
 * Defines the admin-specific functionality of the plugin.
 *
 * @since		1.0.0
 * @since		1.3.2	Refactored class from object to static methods.
 *						Switched from using a central ScreenWP_Loader class to registering hooks directly
 *						on init of ScreenWP, ScreenWP_Admin and ScreenWP_Public.
 *
 * @package		ScreenWP
 * @subpackage	ScreenWP/admin
 */
class ScreenWP_Admin {

	/**
	 * Loads dependencies and registers hooks for the admin-facing side of the plugin.
	 *
	 * @since	1.3.2
	 */
	static function init() {
		self::load_dependencies();

		/* ScreenWP_Admin */
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );

		/* ScreenWP_Admin_Display */
		add_action( 'admin_enqueue_scripts', array( 'ScreenWP_Admin_Display', 'localize_scripts' ) );
		add_action( 'add_meta_boxes', array( 'ScreenWP_Admin_Display', 'add_channel_editor_meta_box' ) );
		add_action( 'add_meta_boxes', array( 'ScreenWP_Admin_Display', 'add_channel_scheduler_meta_box' ) );
		add_action( 'save_post', array( 'ScreenWP_Admin_Display', 'save_display' ) );
		add_filter( 'manage_'.ScreenWP_Display::post_type_name.'_posts_columns', array( 'ScreenWP_Admin_Display', 'add_channel_columns' ) );
		add_action( 'manage_'.ScreenWP_Display::post_type_name.'_posts_custom_column', array( 'ScreenWP_Admin_Display', 'do_channel_columns' ), 10, 2 );
		/* ScreenWP_Admin_Channel */
		add_action( 'admin_enqueue_scripts', array( 'ScreenWP_Admin_Channel', 'localize_scripts' ) );
		add_action( 'add_meta_boxes', array( 'ScreenWP_Admin_Channel', 'add_slides_editor_meta_box' ), 20 );
		add_action( 'add_meta_boxes', array( 'ScreenWP_Admin_Channel', 'add_slides_settings_meta_box' ), 40 );
		add_action( 'save_post', array( 'ScreenWP_Admin_Channel', 'save_channel' ) );
		add_action( 'wp_ajax_screenwp_slides_editor_add_slide', array( 'ScreenWP_Admin_Channel', 'add_slide_over_ajax' ) );
		add_action( 'wp_ajax_screenwp_slides_editor_remove_slide', array( 'ScreenWP_Admin_Channel', 'remove_slide_over_ajax' ) );
		add_action( 'wp_ajax_screenwp_slides_editor_reorder_slides', array( 'ScreenWP_Admin_Channel', 'reorder_slides_over_ajax' ) );
		add_filter( 'get_sample_permalink_html', array( 'ScreenWP_Admin_Channel', 'remove_sample_permalink' ) );
		add_filter( 'manage_'.ScreenWP_Channel::post_type_name.'_posts_columns', array( 'ScreenWP_Admin_Channel', 'add_slides_count_column' ) );
		add_action( 'manage_'.ScreenWP_Channel::post_type_name.'_posts_custom_column', array( 'ScreenWP_Admin_Channel', 'do_slides_count_column' ), 10, 2 );

		/* ScreenWP_Admin_Slide */
		add_action( 'admin_enqueue_scripts', array( 'ScreenWP_Admin_Slide', 'localize_scripts' ) );
		add_action( 'add_meta_boxes', array( 'ScreenWP_Admin_Slide', 'add_slide_editor_meta_boxes' ) );
		add_action( 'save_post', array( 'ScreenWP_Admin_Slide', 'save_slide' ) );
		add_filter( 'get_sample_permalink_html', array( 'ScreenWP_Admin_Slide', 'remove_sample_permalink' ) );
		add_filter( 'manage_'.ScreenWP_Slide::post_type_name.'_posts_columns', array( 'ScreenWP_Admin_Slide', 'add_slide_format_column' ) );
		add_action( 'manage_'.ScreenWP_Slide::post_type_name.'_posts_custom_column', array( 'ScreenWP_Admin_Slide', 'do_slide_format_column' ), 10, 2 );

		/* ScreenWP_Admin_Preview */
		add_action( 'wp_enqueue_scripts', array( 'ScreenWP_Admin_Preview', 'enqueue_scripts' ) );
		add_filter( 'show_admin_bar', array( 'ScreenWP_Admin_Preview', 'hide_admin_bar' ) );
		add_action( 'wp_ajax_screenwp_preview_save_orientation_choice', array( 'ScreenWP_Admin_Preview', 'save_orientation_choice' ) );
		add_action( 'wp_ajax_nopriv_screenwp_preview_save_orientation_choice', array( 'ScreenWP_Admin_Preview', 'save_orientation_choice' ) );

		/* ScreenWP_Admin_Slide_Format_PDF */
		add_filter( 'wp_image_editors', array( 'ScreenWP_Admin_Slide_Format_PDF', 'add_screenwp_imagick_image_editor' ) );
		add_action( 'delete_attachment', array( 'ScreenWP_Admin_Slide_Format_PDF', 'delete_pdf_images_for_attachment' ) );
		add_action( 'admin_notices', array( 'ScreenWP_Admin_Slide_Format_PDF', 'display_admin_notice' ) );
	}

	/**
	 * Adds the top-level ScreenWP admin menu item.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 *					Added context for translations.
	 * @since	1.5.1	Improved the context of the translatable string 'ScreenWP' to make translation easier.
	 */
	static function admin_menu() {
		add_menu_page(
			_x( 'ScreenWP', 'plugin name in admin menu', 'screenwp' ),
			_x( 'ScreenWP', 'plugin name in admin menu', 'screenwp' ),
			'edit_posts',
			'screenwp',
			array(),
			'dashicons-welcome-view-site',
			31
		);
	}

	/**
	 * Enqueues the JavaScript for the admin area.
	 *
	 * @since	1.0.0
	 * @since	1.2.5	Register scripts before they are enqueued.
	 *					Makes it possible to enqueue ScreenWP scripts outside of the ScreenWP plugin.
	 *					Changed handle of script to {plugin_name}-admin.
	 * @since	1.3.2	Changed method to static.
	 */
	static function enqueue_scripts() {

		wp_register_script( ScreenWP::get_plugin_name() . '-admin', plugin_dir_url( __FILE__ ) . 'js/screenwp-admin-min.js', array( 'jquery', 'jquery-ui-sortable' ), ScreenWP::get_version(), false );
		wp_enqueue_script( ScreenWP::get_plugin_name() . '-admin' );
	}

	/**
	 * Enqueues the stylesheets for the admin area.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 */
	static function enqueue_styles() {

		wp_enqueue_style( ScreenWP::get_plugin_name(), plugin_dir_url( __FILE__ ) . 'css/screenwp-admin.css', array(), ScreenWP::get_version(), 'all' );
	}

	/**
	 * Loads the required dependencies for the admin-facing side of the plugin.
	 *
	 * @since	1.3.2
	 * @since	1.4.0	Included admin/class-screenwp-admin-slide-background-image.php.
	 *					Included admin/class-screenwp-admin-slide-background-video.php.
	 *					Removed include admin/class-screenwp-admin-slide-format-video.php.
	 * @since	1.6.0	Included the HTML5 Video slide background admin.
	 * @since	1.7.0	Included the Upcoming Productions slide background admin.
	 *
	 * @access	private
	 */
	private static function load_dependencies() {

		/**
		 * Admin area functionality for display, channel and slide.
		 */
		require_once SCREENWP_PLUGIN_PATH . 'admin/class-screenwp-admin-display.php';
		require_once SCREENWP_PLUGIN_PATH . 'admin/class-screenwp-admin-channel.php';
		require_once SCREENWP_PLUGIN_PATH . 'admin/class-screenwp-admin-slide.php';
		require_once SCREENWP_PLUGIN_PATH . 'admin/class-screenwp-admin-preview.php';

		/**
		 * Admin area functionality for specific slide backgrounds.
		 */
		require_once SCREENWP_PLUGIN_PATH . 'admin/class-screenwp-admin-slide-background-image.php';
		require_once SCREENWP_PLUGIN_PATH . 'admin/class-screenwp-admin-slide-background-video.php';
		require_once SCREENWP_PLUGIN_PATH . 'admin/class-screenwp-admin-slide-background-html5-video.php';

		/**
		 * Admin area functionality for specific slide formats.
		 */
		require_once SCREENWP_PLUGIN_PATH . 'admin/class-screenwp-admin-slide-format-iframe.php';
		require_once SCREENWP_PLUGIN_PATH . 'admin/class-screenwp-admin-slide-format-pdf.php';
		require_once SCREENWP_PLUGIN_PATH . 'admin/class-screenwp-admin-slide-format-post.php';
		require_once SCREENWP_PLUGIN_PATH . 'admin/class-screenwp-admin-slide-format-production.php';
		require_once SCREENWP_PLUGIN_PATH . 'admin/class-screenwp-admin-slide-format-recent-posts.php';
		require_once SCREENWP_PLUGIN_PATH . 'admin/class-screenwp-admin-slide-format-text.php';
		require_once SCREENWP_PLUGIN_PATH . 'admin/class-screenwp-admin-slide-format-upcoming-productions.php';
	}
}
