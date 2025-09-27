<?php

/**
 * The core plugin class.
 *
 * This is used to load general dependencies, register general hooks, and load and init
 * the admin and public parts of the plugin.
 *
 * @since		1.0.0
 * @since		1.3.2	Refactored class from object to static methods.
 *						Switched from using a central VUWU_Loader class to registering hooks directly
 *						on init of VUWU, VUWU_Admin and VUWU_Public.
 *
 * @package		VUWU
 * @subpackage	VUWU/includes
 */
class VUWU {

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
	 * @since	1.5.3	Changed priority of VUWU_Setup::register_post_types() on init to 5 to make sure it is
	 *					triggered before filters with default priority, and before the occasional flush_rewrite_rules()
	 *					after updating.
	 * @since	1.6.0	Registered a hook that adds the HTML5 Video slide background.
	 * @since	1.7.0	Registered a hook that adds the Upcoming Events slide format.
	 * @since	1.7.1	Registered a hook that adds the Recent Posts slide format.
	 * @since	1.7.2	Registered a hook to trigger the 'vuwu_loaded' action that can be used by add-ons.
	 */
	static function init() {

		self::load_dependencies();

		/* VUWU_Updater */
		add_action( 'plugins_loaded', array( 'VUWU_Updater', 'update' ) );

		/* VUWU_i18n */
		add_action( 'plugins_loaded', array( 'VUWU_i18n', 'load_plugin_textdomain' ) );

		/* VUWU_Addons */
		add_action( 'plugins_loaded', array( 'VUWU_Addons', 'trigger_vuwu_loaded' ) );

		/* VUWU_Setup */
		add_action( 'init', array( 'VUWU_Setup', 'register_post_types' ), 5 );

		/* VUWU_Slide_Backgrounds */
		add_filter( 'vuwu/slides/backgrounds', array( 'VUWU_Slide_Backgrounds', 'add_default_slide_background' ), 5 );
		add_filter( 'vuwu/slides/backgrounds', array( 'VUWU_Slide_Backgrounds', 'add_image_slide_background' ), 5 );
		add_filter( 'vuwu/slides/backgrounds', array( 'VUWU_Slide_Backgrounds', 'add_video_slide_background' ), 5 );
		add_filter( 'vuwu/slides/backgrounds', array( 'VUWU_Slide_Backgrounds', 'add_html5_video_slide_background' ), 5 );

		/* VUWU_Slide_Formats */
		add_filter( 'vuwu/slides/formats', array( 'VUWU_Slide_Formats', 'add_default_slide_format' ), 5 );
		add_filter( 'vuwu/slides/formats', array( 'VUWU_Slide_Formats', 'add_text_slide_format' ), 5 );
		add_filter( 'vuwu/slides/formats', array( 'VUWU_Slide_Formats', 'add_post_slide_format' ), 5 );
		add_filter( 'vuwu/slides/formats', array( 'VUWU_Slide_Formats', 'add_production_slide_format' ), 5 );
		add_filter( 'vuwu/slides/formats', array( 'VUWU_Slide_Formats', 'add_iframe_slide_format' ), 5 );
		add_filter( 'vuwu/slides/formats', array( 'VUWU_Slide_Formats', 'add_recent_posts_slide_format' ), 5 );
		add_filter( 'vuwu/slides/formats', array( 'VUWU_Slide_Formats', 'add_upcoming_productions_slide_format' ), 5 );
		add_filter( 'vuwu/slides/formats', array( 'VUWU_Slide_Formats', 'add_pdf_slide_format' ), 5 );
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
		return VUWU_PLUGIN_NAME;
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
		return VUWU_PLUGIN_VERSION;
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Includes the following files that make up the plugin:
	 *
	 * - All general (not public/admin) classes.
	 * - VUWU_Admin: Defines all functionality for the admin area and registers its hooks.
	 * - VUWU_Public: Defines all functionality for the public side of the site and registers its hooks.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 * @since	1.4.0	Included includes/class-vuwu-slide-backgrounds.php.
	 * 					Included includes/class-vuwu-updater.php.
	 * 					Included includes/class-vuwu-displays.php.
	 * 					Included includes/class-vuwu-channels.php.
	 * @since	1.7.2	Included includes/class-vuwu-addons.php.
	 *
	 * @access	private
	 */
	private static function load_dependencies() {

		/**
		 * ------ General (not public/admin) ------
		 */

		/* Display, channel and slide models. */
		require_once VUWU_PLUGIN_PATH . 'includes/class-vuwu-display.php';
		require_once VUWU_PLUGIN_PATH . 'includes/class-vuwu-channel.php';
		require_once VUWU_PLUGIN_PATH . 'includes/class-vuwu-slide.php';

		/* Display, channel and slide helper functions. */
		require_once VUWU_PLUGIN_PATH . 'includes/class-vuwu-displays.php';
		require_once VUWU_PLUGIN_PATH . 'includes/class-vuwu-channels.php';
		require_once VUWU_PLUGIN_PATH . 'includes/class-vuwu-slides.php';

		/* Database updater. */
		require_once VUWU_PLUGIN_PATH . 'includes/class-vuwu-updater.php';

		/* Setup of internationalization. */
		require_once VUWU_PLUGIN_PATH . 'includes/class-vuwu-i18n.php';

		/* Add-ons. */
		require_once VUWU_PLUGIN_PATH . 'includes/class-vuwu-addons.php';

		/* General (not public/admin) setup actions. */
		require_once VUWU_PLUGIN_PATH . 'includes/class-vuwu-setup.php';

		/* Slide backgrounds. */
		require_once VUWU_PLUGIN_PATH . 'includes/class-vuwu-slide-backgrounds.php';

		/* Slide formats. */
		require_once VUWU_PLUGIN_PATH . 'includes/class-vuwu-slide-formats.php';

		/* Theater for WordPress helper functions. */
		require_once VUWU_PLUGIN_PATH . 'includes/class-vuwu-theater.php';


		/**
		 * ------ Admin ------
		 */

		require_once VUWU_PLUGIN_PATH . 'admin/class-vuwu-admin.php';
		VUWU_Admin::init();

		/**
		 * ------ Public ------
		 */

		require_once VUWU_PLUGIN_PATH . 'public/class-vuwu-public.php';
		VUWU_Public::init();
	}
}
