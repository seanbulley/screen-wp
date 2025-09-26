<?php

/**
 * The core plugin class.
 *
 * This is used to load general dependencies, register general hooks, and load and init
 * the admin and public parts of the plugin.
 *
 * @since		1.0.0
 * @since		1.3.2	Refactored class from object to static methods.
 *						Switched from using a central ScreenWP_Loader class to registering hooks directly
 *						on init of ScreenWP, ScreenWP_Admin and ScreenWP_Public.
 *
 * @package		ScreenWP
 * @subpackage	ScreenWP/includes
 */
class ScreenWP {

	/**
	 * Initializes the plugin.
	 *
	 * Loads dependencies, defines the locale and registers all of the hooks related to the
	 * general functionality of the plugin (not public/admin specific).
	 *
	 * @since	1.3.2	Changed method to static.
	 * @since	1.4.0	Registered hooks for slide backgrounds.
	 *					Changed priority of slide format filters to make sure they are triggered before
	 *					filters with default priority.
	 * @since	1.5.3	Changed priority of ScreenWP_Setup::register_post_types() on init to 5 to make sure it is
	 *					triggered before filters with default priority, and before the occasional flush_rewrite_rules()
	 *					after updating.
	 * @since	1.6.0	Registered a hook that adds the HTML5 Video slide background.
	 * @since	1.7.0	Registered a hook that adds the Upcoming Events slide format.
	 * @since	1.7.1	Registered a hook that adds the Recent Posts slide format.
	 * @since	1.7.2	Registered a hook to trigger the 'screenwp_loaded' action that can be used by add-ons.
	 */
	static function init() {

		self::load_dependencies();

		/* ScreenWP_Updater */
		add_action( 'plugins_loaded', array( 'ScreenWP_Updater', 'update' ) );

		/* ScreenWP_i18n */
		add_action( 'plugins_loaded', array( 'ScreenWP_i18n', 'load_plugin_textdomain' ) );

		/* ScreenWP_Addons */
		add_action( 'plugins_loaded', array( 'ScreenWP_Addons', 'trigger_screenwp_loaded' ) );

		/* ScreenWP_Setup */
		add_action( 'init', array( 'ScreenWP_Setup', 'register_post_types' ), 5 );

		/* ScreenWP_Slide_Backgrounds */
		add_filter( 'screenwp/slides/backgrounds', array( 'ScreenWP_Slide_Backgrounds', 'add_default_slide_background' ), 5 );
		add_filter( 'screenwp/slides/backgrounds', array( 'ScreenWP_Slide_Backgrounds', 'add_image_slide_background' ), 5 );
		add_filter( 'screenwp/slides/backgrounds', array( 'ScreenWP_Slide_Backgrounds', 'add_video_slide_background' ), 5 );
		add_filter( 'screenwp/slides/backgrounds', array( 'ScreenWP_Slide_Backgrounds', 'add_html5_video_slide_background' ), 5 );

		/* ScreenWP_Slide_Formats */
		add_filter( 'screenwp/slides/formats', array( 'ScreenWP_Slide_Formats', 'add_default_slide_format' ), 5 );
		add_filter( 'screenwp/slides/formats', array( 'ScreenWP_Slide_Formats', 'add_text_slide_format' ), 5 );
		add_filter( 'screenwp/slides/formats', array( 'ScreenWP_Slide_Formats', 'add_post_slide_format' ), 5 );
		add_filter( 'screenwp/slides/formats', array( 'ScreenWP_Slide_Formats', 'add_production_slide_format' ), 5 );
		add_filter( 'screenwp/slides/formats', array( 'ScreenWP_Slide_Formats', 'add_iframe_slide_format' ), 5 );
		add_filter( 'screenwp/slides/formats', array( 'ScreenWP_Slide_Formats', 'add_recent_posts_slide_format' ), 5 );
		add_filter( 'screenwp/slides/formats', array( 'ScreenWP_Slide_Formats', 'add_upcoming_productions_slide_format' ), 5 );
		add_filter( 'screenwp/slides/formats', array( 'ScreenWP_Slide_Formats', 'add_pdf_slide_format' ), 5 );
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 *					Now uses a named constant.
	 *
	 * @return	string	The name of the plugin.
	 */
	static function get_plugin_name() {
		return SCREENWP_PLUGIN_NAME;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 * 					Now uses a named constant.
	 *
	 * @return	string	The version of the plugin.
	 */
	static function get_version() {
		return SCREENWP_PLUGIN_VERSION;
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Includes the following files that make up the plugin:
	 *
	 * - All general (not public/admin) classes.
	 * - ScreenWP_Admin: Defines all functionality for the admin area and registers its hooks.
	 * - ScreenWP_Public: Defines all functionality for the public side of the site and registers its hooks.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 * @since	1.4.0	Included includes/class-screenwp-slide-backgrounds.php.
	 * 					Included includes/class-screenwp-updater.php.
	 * 					Included includes/class-screenwp-displays.php.
	 * 					Included includes/class-screenwp-channels.php.
	 * @since	1.7.2	Included includes/class-screenwp-addons.php.
	 *
	 * @access	private
	 */
	private static function load_dependencies() {

		/**
		 * ------ General (not public/admin) ------
		 */

		/* Display, channel and slide models. */
		require_once SCREENWP_PLUGIN_PATH . 'includes/class-screenwp-display.php';
		require_once SCREENWP_PLUGIN_PATH . 'includes/class-screenwp-channel.php';
		require_once SCREENWP_PLUGIN_PATH . 'includes/class-screenwp-slide.php';

		/* Display, channel and slide helper functions. */
		require_once SCREENWP_PLUGIN_PATH . 'includes/class-screenwp-displays.php';
		require_once SCREENWP_PLUGIN_PATH . 'includes/class-screenwp-channels.php';
		require_once SCREENWP_PLUGIN_PATH . 'includes/class-screenwp-slides.php';

		/* Database updater. */
		require_once SCREENWP_PLUGIN_PATH . 'includes/class-screenwp-updater.php';

		/* Setup of internationalization. */
		require_once SCREENWP_PLUGIN_PATH . 'includes/class-screenwp-i18n.php';

		/* Add-ons. */
		require_once SCREENWP_PLUGIN_PATH . 'includes/class-screenwp-addons.php';

		/* General (not public/admin) setup actions. */
		require_once SCREENWP_PLUGIN_PATH . 'includes/class-screenwp-setup.php';

		/* Slide backgrounds. */
		require_once SCREENWP_PLUGIN_PATH . 'includes/class-screenwp-slide-backgrounds.php';

		/* Slide formats. */
		require_once SCREENWP_PLUGIN_PATH . 'includes/class-screenwp-slide-formats.php';

		/* Theater for WordPress helper functions. */
		require_once SCREENWP_PLUGIN_PATH . 'includes/class-screenwp-theater.php';


		/**
		 * ------ Admin ------
		 */

		require_once SCREENWP_PLUGIN_PATH . 'admin/class-screenwp-admin.php';
		ScreenWP_Admin::init();

		/**
		 * ------ Public ------
		 */

		require_once SCREENWP_PLUGIN_PATH . 'public/class-screenwp-public.php';
		ScreenWP_Public::init();
	}
}
