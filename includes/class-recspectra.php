<?php

/**
 * The core plugin class.
 *
 * This is used to load general dependencies, register general hooks, and load and init
 * the admin and public parts of the plugin.
 *
 * @since		1.0.0
 * @since		1.3.2	Refactored class from object to static methods.
 *						Switched from using a central Recspectra_Loader class to registering hooks directly
 *						on init of Recspectra, Recspectra_Admin and Recspectra_Public.
 *
 * @package		Recspectra
 * @subpackage	Recspectra/includes
 */
class Recspectra {

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
	 * @since	1.5.3	Changed priority of Recspectra_Setup::register_post_types() on init to 5 to make sure it is
	 *					triggered before filters with default priority, and before the occasional flush_rewrite_rules()
	 *					after updating.
	 * @since	1.6.0	Registered a hook that adds the HTML5 Video slide background.
	 * @since	1.7.0	Registered a hook that adds the Upcoming Events slide format.
	 * @since	1.7.1	Registered a hook that adds the Recent Posts slide format.
	 * @since	1.7.2	Registered a hook to trigger the 'recspectra_loaded' action that can be used by add-ons.
	 */
	static function init() {

		self::load_dependencies();

		/* Recspectra_Updater */
		add_action( 'plugins_loaded', array( 'Recspectra_Updater', 'update' ) );

		/* Recspectra_i18n */
		add_action( 'plugins_loaded', array( 'Recspectra_i18n', 'load_plugin_textdomain' ) );

		/* Recspectra_Addons */
		add_action( 'plugins_loaded', array( 'Recspectra_Addons', 'trigger_recspectra_loaded' ) );

		/* Recspectra_Setup */
		add_action( 'init', array( 'Recspectra_Setup', 'register_post_types' ), 5 );

		/* Recspectra_Slide_Backgrounds */
		add_filter( 'recspectra/slides/backgrounds', array( 'Recspectra_Slide_Backgrounds', 'add_default_slide_background' ), 5 );
		add_filter( 'recspectra/slides/backgrounds', array( 'Recspectra_Slide_Backgrounds', 'add_image_slide_background' ), 5 );
		add_filter( 'recspectra/slides/backgrounds', array( 'Recspectra_Slide_Backgrounds', 'add_video_slide_background' ), 5 );
		add_filter( 'recspectra/slides/backgrounds', array( 'Recspectra_Slide_Backgrounds', 'add_html5_video_slide_background' ), 5 );

		/* Recspectra_Slide_Formats */
		add_filter( 'recspectra/slides/formats', array( 'Recspectra_Slide_Formats', 'add_default_slide_format' ), 5 );
		add_filter( 'recspectra/slides/formats', array( 'Recspectra_Slide_Formats', 'add_text_slide_format' ), 5 );
		add_filter( 'recspectra/slides/formats', array( 'Recspectra_Slide_Formats', 'add_post_slide_format' ), 5 );
		add_filter( 'recspectra/slides/formats', array( 'Recspectra_Slide_Formats', 'add_production_slide_format' ), 5 );
		add_filter( 'recspectra/slides/formats', array( 'Recspectra_Slide_Formats', 'add_iframe_slide_format' ), 5 );
		add_filter( 'recspectra/slides/formats', array( 'Recspectra_Slide_Formats', 'add_recent_posts_slide_format' ), 5 );
		add_filter( 'recspectra/slides/formats', array( 'Recspectra_Slide_Formats', 'add_upcoming_productions_slide_format' ), 5 );
		add_filter( 'recspectra/slides/formats', array( 'Recspectra_Slide_Formats', 'add_pdf_slide_format' ), 5 );
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
		return RECSPECTRA_PLUGIN_NAME;
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
		return RECSPECTRA_PLUGIN_VERSION;
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Includes the following files that make up the plugin:
	 *
	 * - All general (not public/admin) classes.
	 * - Recspectra_Admin: Defines all functionality for the admin area and registers its hooks.
	 * - Recspectra_Public: Defines all functionality for the public side of the site and registers its hooks.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 * @since	1.4.0	Included includes/class-recspectra-slide-backgrounds.php.
	 * 					Included includes/class-recspectra-updater.php.
	 * 					Included includes/class-recspectra-displays.php.
	 * 					Included includes/class-recspectra-channels.php.
	 * @since	1.7.2	Included includes/class-recspectra-addons.php.
	 *
	 * @access	private
	 */
	private static function load_dependencies() {

		/**
		 * ------ General (not public/admin) ------
		 */

		/* Display, channel and slide models. */
		require_once RECSPECTRA_PLUGIN_PATH . 'includes/class-recspectra-display.php';
		require_once RECSPECTRA_PLUGIN_PATH . 'includes/class-recspectra-channel.php';
		require_once RECSPECTRA_PLUGIN_PATH . 'includes/class-recspectra-slide.php';

		/* Display, channel and slide helper functions. */
		require_once RECSPECTRA_PLUGIN_PATH . 'includes/class-recspectra-displays.php';
		require_once RECSPECTRA_PLUGIN_PATH . 'includes/class-recspectra-channels.php';
		require_once RECSPECTRA_PLUGIN_PATH . 'includes/class-recspectra-slides.php';

		/* Database updater. */
		require_once RECSPECTRA_PLUGIN_PATH . 'includes/class-recspectra-updater.php';

		/* Setup of internationalization. */
		require_once RECSPECTRA_PLUGIN_PATH . 'includes/class-recspectra-i18n.php';

		/* Add-ons. */
		require_once RECSPECTRA_PLUGIN_PATH . 'includes/class-recspectra-addons.php';

		/* General (not public/admin) setup actions. */
		require_once RECSPECTRA_PLUGIN_PATH . 'includes/class-recspectra-setup.php';

		/* Slide backgrounds. */
		require_once RECSPECTRA_PLUGIN_PATH . 'includes/class-recspectra-slide-backgrounds.php';

		/* Slide formats. */
		require_once RECSPECTRA_PLUGIN_PATH . 'includes/class-recspectra-slide-formats.php';

		/* Theater for WordPress helper functions. */
		require_once RECSPECTRA_PLUGIN_PATH . 'includes/class-recspectra-theater.php';


		/**
		 * ------ Admin ------
		 */

		require_once RECSPECTRA_PLUGIN_PATH . 'admin/class-recspectra-admin.php';
		Recspectra_Admin::init();

		/**
		 * ------ Public ------
		 */

		require_once RECSPECTRA_PLUGIN_PATH . 'public/class-recspectra-public.php';
		Recspectra_Public::init();
	}
}
