<?php

/**
 * The class that holds all shared slide format functionality.
 *
 * @since		1.1.0
 *
 * @package		ScreenWP
 * @subpackage	ScreenWP/includes
 */
class ScreenWP_Slide_Formats {

	/**
	 * Adds the Default slide format.
	 *
	 * @since	1.4.0	Default slide format is now also added through filter, instead of in ScreenWP_Slides.
	 * 					Added appropriate slide backgrounds to the properties of this slide format.
	 * @since	1.6.0	Added the HTML5 Video slide background to this slide format's list of available backgrounds.
	 *
	 * @param 	array	$slide_formats	The current slide formats.
	 * @return	array					The slide formats with the Default slide format added.
	 */
	static function add_default_slide_format( $slide_formats ) {

		$slide_format_backgrounds = array( 'image', 'html5-video', 'video' );

		/**
		 * Filter available slide backgrounds for this slide format.
		 *
		 * @since	1.4.0
		 * @param	array	$slide_format_backgrounds	The currently available slide backgrounds for this slide format.
		 */
		$slide_format_backgrounds = apply_filters( 'screenwp/slides/backgrounds/format=default', $slide_format_backgrounds );

		$slide_formats['default'] = array(
			'title' => _x( 'Default', 'slide-format', 'screenwp' ),
			'description' => __( 'Displays a background only.', 'screenwp' ),
			'slide_backgrounds' => $slide_format_backgrounds,
		);
		return $slide_formats;
	}

	/**
	 * Adds the Iframe slide format.
	 *
	 * @since	1.3.0
	 * @since	1.4.0	Added appropriate slide backgrounds to the properties of this slide format.
	 *
	 * @param 	array	$slide_formats	The current slide formats.
	 * @return	array					The slide formats with the Iframe slide format added.
	 */
	static function add_iframe_slide_format( $slide_formats ) {

		$slide_format_backgrounds = array( 'default' );

		/**
		 * Filter available slide backgrounds for this slide format.
		 *
		 * @since	1.4.0
		 * @param	array	$slide_format_backgrounds	The currently available slide backgrounds for this slide format.
		 */
		$slide_format_backgrounds = apply_filters( 'screenwp/slides/backgrounds/format=iframe', $slide_format_backgrounds );

		$slide_formats['iframe'] = array(
			'title' => _x( 'External web page', 'slide-format', 'screenwp' ),
			'description' => __( 'Displays a web page to your liking.', 'screenwp' ),
			'meta_box' => array( 'ScreenWP_Admin_Slide_Format_Iframe', 'slide_meta_box' ),
			'save_post' => array( 'ScreenWP_Admin_Slide_Format_Iframe', 'save_slide' ),
			'slide_backgrounds' => $slide_format_backgrounds,
		);
		return $slide_formats;
	}

	/**
	 * Adds the PDF slide format.
	 *
	 * @since	1.1.0
	 * @since	1.4.0	Added appropriate slide backgrounds to the properties of this slide format.
	 * @since	1.5.0	Added the stack property.
	 * @since	1.6.0	Added the HTML5 Video slide background to this slide format's list of available backgrounds.
	 *
	 * @param 	array	$slide_formats	The current slide formats.
	 * @return	array					The slide formats with the PDF slide format added.
	 */
	static function add_pdf_slide_format( $slide_formats ) {

		$slide_format_backgrounds = array( 'default', 'image', 'html5-video', 'video' );

		/**
		 * Filter available slide backgrounds for this slide format.
		 *
		 * @since	1.4.0
		 * @param	array	$slide_format_backgrounds	The currently available slide backgrounds for this slide format.
		 */
		$slide_format_backgrounds = apply_filters( 'screenwp/slides/backgrounds/format=default', $slide_format_backgrounds );

		$slide_formats['pdf'] = array(
			'title' => _x( 'PDF', 'slide-format', 'screenwp' ),
			'description' => __( 'Displays a slide for each page in the uploaded PDF.', 'screenwp' ),
			'meta_box' => array( 'ScreenWP_Admin_Slide_Format_PDF', 'slide_pdf_meta_box' ),
			'save_post' => array( 'ScreenWP_Admin_Slide_Format_PDF', 'save_slide_pdf' ),
			'slide_backgrounds' => $slide_format_backgrounds,
			'stack' => true,
		);
		return $slide_formats;
	}

	/**
	 * Adds the Post slide format.
	 *
	 * @since	1.5.0
	 * @since	1.6.0	Added the HTML5 Video slide background to this slide format's list of available backgrounds.
	 *
	 * @param 	array	$slide_formats	The current slide formats.
	 * @return	array					The slide formats with the Post slide format added.
	 */
	static function add_post_slide_format( $slide_formats ) {

		$slide_format_backgrounds = array( 'default', 'image', 'html5-video', 'video' );

		/**
		 * Filter available slide backgrounds for this slide format.
		 *
		 * @since	1.5.0
		 * @param	array	$slide_format_backgrounds	The currently available slide backgrounds for this slide format.
		 */
		$slide_format_backgrounds = apply_filters( 'screenwp/slides/backgrounds/format=post', $slide_format_backgrounds );

		$slide_formats['post'] = array(
			'title' => _x( 'Post', 'slide-format', 'screenwp' ),
			'description' => __( 'Displays title, date and content of a post.', 'screenwp' ),
			'meta_box' => array( 'ScreenWP_Admin_Slide_Format_Post', 'slide_meta_box' ),
			'save_post' => array( 'ScreenWP_Admin_Slide_Format_Post', 'save_slide' ),
			'slide_backgrounds' => $slide_format_backgrounds,
		);
		return $slide_formats;
	}

	/**
	 * Adds the Production slide format.
	 *
	 * @since	1.0.0
	 * @since	1.1.0	Moved here from ScreenWP_Theater, and changed to static.
	 * @since	1.2.6	Changed the displayed name from Production to Event, same terminology as in Theater for WordPress.
	 * @since	1.4.0	Added appropriate slide backgrounds to the properties of this slide format.
	 * @since	1.6.0	Added the HTML5 Video slide background to this slide format's list of available backgrounds.
	 *
	 * @param 	array	$slide_formats	The current slide formats.
	 * @return	array					The slide formats with the Production slide format added.
	 */
	static function add_production_slide_format( $slide_formats ) {

		if ( ! ScreenWP_Theater::is_theater_activated() ) {
			return $slide_formats;
		}

		$slide_format_backgrounds = array( 'default', 'image', 'html5-video', 'video' );

		/**
		 * Filter available slide backgrounds for this slide format.
		 *
		 * @since	1.4.0
		 * @param	array	$slide_format_backgrounds	The currently available slide backgrounds for this slide format.
		 */
		$slide_format_backgrounds = apply_filters( 'screenwp/slides/backgrounds/format=production', $slide_format_backgrounds );

		$slide_formats['production'] = array(
			'title' => _x( 'Event', 'slide-format', 'screenwp' ),
			'description' => __( 'Displays title and details of an event, with its image as default background.', 'screenwp' ),
			'meta_box' => array( 'ScreenWP_Admin_Slide_Format_Production', 'slide_production_meta_box' ),
			'save_post' => array( 'ScreenWP_Admin_Slide_Format_Production', 'save_slide_production' ),
			'slide_backgrounds' => $slide_format_backgrounds,
			'default_background_template' => true,
		);

		return $slide_formats;
	}

	/**
	 * Adds the Recent Posts slide format.
	 *
	 * @since	1.7.1
	 *
	 * @param 	array	$slide_formats	The current slide formats.
	 * @return	array					The slide formats with the Recent Posts slide format added.
	 */
	static function add_recent_posts_slide_format( $slide_formats ) {

		$slide_format_backgrounds = array( 'default', 'image' );

		/**
		 * Filter available slide backgrounds for this slide format.
		 *
		 * @since	1.7.0
		 * @param	array	$slide_format_backgrounds	The currently available slide backgrounds for this slide format.
		 */
		$slide_format_backgrounds = apply_filters( 'screenwp/slides/backgrounds/format=recent-posts', $slide_format_backgrounds );

		$slide_formats['recent-posts'] = array(
			'title' => 'Recent posts',
			'description' => 'Displays a slide for each recent post.',
			'meta_box' => array( 'ScreenWP_Admin_Slide_Format_Recent_Posts', 'slide_meta_box'),
			'save_post' => array( 'ScreenWP_Admin_Slide_Format_Recent_Posts', 'save_slide'),
			'slide_backgrounds' => $slide_format_backgrounds,
			'stack' => true,
		);

		return $slide_formats;
	}

	/**
	 * Adds the Text slide format.
	 *
	 * @since	1.5.0
	 * @since	1.5.1	Renamed the slide format from 'Manual text' to 'Text'.
	 * @since	1.6.0	Added the HTML5 Video slide background to this slide format's list of available backgrounds.
	 *
	 * @param 	array	$slide_formats	The current slide formats.
	 * @return	array					The slide formats with the Text slide format added.
	 */
	static function add_text_slide_format( $slide_formats ) {

		$slide_format_backgrounds = array( 'default', 'image', 'html5-video', 'video' );

		/**
		 * Filter available slide backgrounds for this slide format.
		 *
		 * @since	1.5.0
		 * @param	array	$slide_format_backgrounds	The currently available slide backgrounds for this slide format.
		 */
		$slide_format_backgrounds = apply_filters( 'screenwp/slides/backgrounds/format=text', $slide_format_backgrounds );

		$slide_formats['text'] = array(
			'title' => _x( 'Text', 'slide-format', 'screenwp' ),
			'description' => __( 'Displays some text.', 'screenwp' ),
			'meta_box' => array( 'ScreenWP_Admin_Slide_Format_Text', 'slide_meta_box' ),
			'save_post' => array( 'ScreenWP_Admin_Slide_Format_Text', 'save_slide' ),
			'slide_backgrounds' => $slide_format_backgrounds,
		);
		return $slide_formats;
	}

	/**
	 * Adds the Upcoming Productions slide format.
	 *
	 * @since	1.7.0
	 *
	 * @param 	array	$slide_formats	The current slide formats.
	 * @return	array					The slide formats with the Upcoming Productions slide format added.
	 */
	static function add_upcoming_productions_slide_format( $slide_formats ) {

		if ( ! ScreenWP_Theater::is_theater_activated() ) {
			return $slide_formats;
		}

		$slide_format_backgrounds = array( 'default' );

		/**
		 * Filter available slide backgrounds for this slide format.
		 *
		 * @since	1.7.0
		 * @param	array	$slide_format_backgrounds	The currently available slide backgrounds for this slide format.
		 */
		$slide_format_backgrounds = apply_filters( 'screenwp/slides/backgrounds/format=upcoming-productions', $slide_format_backgrounds );

		$slide_formats['upcoming-productions'] = array(
			'title' => 'Upcoming events',
			'description' => 'Displays a slide for each upcoming event.',
			'meta_box' => array( 'ScreenWP_Admin_Slide_Format_Upcoming_Productions', 'slide_meta_box'),
			'save_post' => array( 'ScreenWP_Admin_Slide_Format_Upcoming_Productions', 'save_slide'),
			'slide_backgrounds' => $slide_format_backgrounds,
			'default_background_template' => true,
			'stack' => true,
		);

		return $slide_formats;
	}
}
