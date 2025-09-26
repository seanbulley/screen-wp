<?php

/**
 * The slide object model.
 *
 * @since		1.0.0
 *
 * @package		ScreenWP
 * @subpackage	ScreenWP/includes
 */
class ScreenWP_Slide {

	/**
	 * The ScreenWP Slide post type name.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $post_type_name    The ScreenWP Slide post type name.
	 */
	const post_type_name = 'screenwp_slide';

	public $ID;
	private $post;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since	1.0.0
	 * @param	int or WP_Post	$ID		The id or the WP_Post object of the slide.
	 */
	public function __construct( $ID = false ) {

		if ( $ID instanceof WP_Post ) {
			// $ID is a WP_Post object
			$this->post = $ID;
			$ID = $ID->ID;
		}

		$this->ID = $ID;
	}

	/**
	 * Outputs the background template HTML for use in the slide format template.
	 *
	 * @since	1.4.0
	 * @since	1.5.7	Added support for template args.
	 *
	 * @return	string	The background template HTML.
	 */
	public function background( $template_args = false ) {
		ScreenWP_Templates::get_template( 'slides/backgrounds/' . $this->get_background() . '.php', $template_args );
	}

	/**
	 * Outputs the slide background classes for use in the template.
	 *
	 * The output is escaped, so this method can be used in templates without further escaping.
	 *
	 * @since	1.4.0
	 *
	 * @param 	array 	$classes
	 * @return 	void
	 */
	public function background_classes( $classes = array() ) {

		$classes[] = 'screenwp-slide-background';
		$classes[] = 'screenwp-slide-background-' . $this->get_background();

		if ( empty( $classes ) ) {
			return;
		}

		?> class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" <?php
	}

	/**
	 * Outputs the slide background data attributes for use in the template.
	 *
	 * The output is escaped, so this method can be used in templates without further escaping.
	 *
	 * @since	1.4.0
	 *
	 * @param 	array 	$data
	 * @return 	string
	 */
	public function background_data_attr( $data = array() ) {

		if ( empty( $data ) ) {
			return;
		}

		foreach ( $data as $key => $value ) {
			?> data-<?php echo esc_attr( $key ); ?>="<?php echo esc_attr( $value ); ?>"<?php
		}
	}

	/**
	 * Outputs the slide classes for use in the template.
	 *
	 * The output is escaped, so this method can be used in templates without further escaping.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Escaped the output.
	 * @since	1.4.0	Added slide background to the classes.
	 *
	 * @param 	array 	$classes
	 * @return 	void
	 */
	public function classes( $classes = array() ) {

		$classes[] = 'screenwp-slide';
		$classes[] = 'screenwp-slide-' . $this->get_format();
		$classes[] = 'screenwp-slide-background-' . $this->get_background();

		if ( ScreenWP_Channel::post_type_name == get_post_type( get_queried_object_id() ) ) {
			$channel = new ScreenWP_Channel( get_queried_object_id() );
		}

		if ( ScreenWP_Display::post_type_name == get_post_type( get_queried_object_id() ) ) {
			$display = new ScreenWP_Display( get_queried_object_id() );
			$channel = new ScreenWP_Channel( $display->get_active_channel() );
		}

		if ( ! empty ( $channel ) ) {
			$slides = $channel->get_slides();
			if ( ! empty( $slides ) && $this->ID == $slides[0]->ID ) {
				$classes[] = 'next';
			}
		}

		if ( empty( $classes ) ) {
			return;
		}

		?> class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" <?php
	}

	/**
	 * Outputs the slide format's default background template HTML, if it is known to have one.
	 *
	 * Used in the Default slide background template.
	 *
	 * @since	1.4.0
	 * @since	1.5.7	Added support for template args.
	 *
	 * @return	string	The slide format's default background template HTML.
	 */
	public function default_background( $template_args = false ) {
		if ( ScreenWP_Slides::slide_format_has_default_background_template( self::get_format() ) ) {
			ScreenWP_Templates::get_template( 'slides/backgrounds/default-' . $this->get_format() . '.php', $template_args );
		}
	}

	/**
	 * Outputs the slide data attributes for use in the template.
	 *
	 * The output is escaped, so this method can be used in templates without further escaping.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Escaped the output.
	 *
	 * @param 	array 	$data
	 * @return 	string
	 */
	public function data_attr( $data = array() ) {

		if ( ScreenWP_Channel::post_type_name == get_post_type( get_queried_object_id() ) ) {
			$channel = new ScreenWP_Channel( get_queried_object_id() );
		}

		if ( ScreenWP_Display::post_type_name == get_post_type( get_queried_object_id() ) ) {
			$display = new ScreenWP_Display( get_queried_object_id() );
			$channel = new ScreenWP_Channel( $display->get_active_channel() );
		}

		if (!empty ($channel) )	{
			$data['screenwp-slide-duration'] = $channel->get_slides_duration();
		}

		if (empty($data)) {
			return;
		}

		foreach ( $data as $key=>$value ) {
			?> data-<?php echo esc_attr( $key ); ?>="<?php echo esc_attr( $value ); ?>"<?php
		}
	}

	/**
	 * Gets the background of the slide.
	 *
	 * @since	1.4.0
	 *
	 * @return	string	The background key.
	 */
	public function get_background() {

		$slide_background = get_post_meta( $this->ID, 'slide_background', true );

		$slide_background_keys = array_keys( ScreenWP_Slides::get_slide_backgrounds() );

		if ( empty ( $slide_background ) || ! in_array( $slide_background, $slide_background_keys ) ) {
			$slide_background = $slide_background_keys[0];
		}

		return $slide_background;
	}

	/**
	 * Gets the format of the slide.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Renamed from format() to get_format().
	 *
	 * @return	string	The format key.
	 */
	public function get_format() {

		$slide_format = get_post_meta( $this->ID, 'slide_format', true );

		$slide_format_keys = array_keys( ScreenWP_Slides::get_slide_formats() );

		if ( empty ( $slide_format ) || ! in_array( $slide_format, $slide_format_keys ) ) {
			$slide_format = $slide_format_keys[0];
		}

		return $slide_format;
	}

	/**
	 * Gets the URL of the slide image.
	 *
	 * @deprecated	1.4.0
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Renamed from image() to get_image_url().
	 * @since	1.3.1	Now returns the image uploaded on the production slide, for production slides.
	 * @since	1.4.0	Now returns the background image, instead of the default slide or production slide image.
	 *
	 * @return	string	The URL of the slide image.
	 */
	public function get_image_url() {
		_deprecated_function( 'ScreenWP_Slide::get_image_url()', '1.4.0', '' );

		$attachment_id = get_post_meta( $this->ID, 'slide_bg_image_image', true );
		$attachment_src = wp_get_attachment_image_src( $attachment_id, 'screenwp_fhd_square' );

		if ( empty ( $attachment_src[0] ) ) {
			return false;
		}

		return $attachment_src[0];
	}

	/**
	 * Outputs the URL of the slide image.
	 *
	 * The output is escaped, so this method can be used in templates without further escaping.
	 *
	 * @deprecated	1.4.0
	 *
	 * @since	1.0.1
	 * @return	void
	 */
	public function image_url() {
		_deprecated_function( 'ScreenWP_Slide::image_url()', '1.4.0', '' );
		echo esc_url( $this->get_image_url() );
	}

	/**
	 * Checks if the slide results in a stack of slides.
	 *
	 * @since	1.5.0
	 *
	 * @return	bool	True if the slide is a stack, false otherwise.
	 */
	public function is_stack() {
		return ScreenWP_Slides::slide_format_is_stack( self::get_format() );
	}
}
