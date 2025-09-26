<?php

class Test_ScreenWP_Slide_Formats extends ScreenWP_UnitTestCase {

	function replace_format_backgrounds( $slide_format_backgrounds ) {
		return array( 'default' );
	}


	/* Tests */

	function test_is_pdf_slide_format_registered() {
		$slide_format = ScreenWP_Slides::get_slide_format_by_slug( 'pdf' );
		$this->assertNotEmpty( $slide_format );
	}

	function test_is_production_slide_format_not_registered() {
		// Theater plugin is not loaded by default
		$slide_format = ScreenWP_Slides::get_slide_format_by_slug( 'production' );
		$this->assertEmpty( $slide_format );
	}

	/**
	 * @since	1.?
	 * @since	1.4.0	Updated to work with slide backgrounds.
	 */
	function test_is_deprecated_video_slide_format_not_registered() {
		$slide_format = ScreenWP_Slides::get_slide_format_by_slug( 'video' );
		$this->assertEmpty( $slide_format );
	}

	function test_is_iframe_slide_format_registered() {
		$slide_format = ScreenWP_Slides::get_slide_format_by_slug( 'iframe' );
		$this->assertNotEmpty( $slide_format );
	}

	function test_is_default_slide_format_registered() {
		$slide_format = ScreenWP_Slides::get_slide_format_by_slug( 'default' );
		$this->assertNotEmpty( $slide_format );
	}

	/**
	 * @since	1.4.0
	 */
	function test_are_backgrounds_for_default_slide_format_registered() {
		$expected = array( 'image', 'html5-video', 'video' );
		$actual = array_keys( ScreenWP_Slides::get_slide_format_backgrounds_by_slug( 'default' ) );

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * @since	1.4.0
	 */
	function test_are_backgrounds_for_default_slide_format_filtered() {
		add_filter( 'screenwp/slides/backgrounds/format=default', array( $this, 'replace_format_backgrounds' ) );

		$expected = array( 'default' );
		$actual = array_keys( ScreenWP_Slides::get_slide_format_backgrounds_by_slug( 'default' ) );

		$this->assertEquals( $expected, $actual );

		remove_filter( 'screenwp/slides/backgrounds/format=default', array( $this, 'remove_default_background_from_format_backgrounds' ) );
	}

	/**
	 * @since	1.5.0
	 */
	function test_is_post_slide_format_registered() {
		$slide_format = ScreenWP_Slides::get_slide_format_by_slug( 'post' );
		$this->assertNotEmpty( $slide_format );
	}

	/**
	 * @since	1.5.0
	 */
	function test_is_text_slide_format_registered() {
		$slide_format = ScreenWP_Slides::get_slide_format_by_slug( 'text' );
		$this->assertNotEmpty( $slide_format );
	}

	/**
	 * @since	1.7.0
	 */
	function test_is_upcoming_productions_slide_format_not_registered() {
		// Theater plugin is not loaded by default
		$slide_format = ScreenWP_Slides::get_slide_format_by_slug( 'upcoming-productions' );
		$this->assertEmpty( $slide_format );
	}

	/**
	 * @since	1.7.1
	 */
	function test_is_recent_posts_slide_format_registered() {
		$slide_format = ScreenWP_Slides::get_slide_format_by_slug( 'recent-posts' );
		$this->assertNotEmpty( $slide_format );
	}
}

/**
 * @group theater
 *
 * Excluded when running phpunit by default. Use 'phpunit --group theater' to test.
 */
class Test_ScreenWP_Slide_Formats_Theater extends ScreenWP_Theater_UnitTestCase {

	/**
	 * @since	1.?
	 */
	function test_is_production_slide_format_registered_when_theater_is_active() {
		$slide_format = ScreenWP_Slides::get_slide_format_by_slug( 'production' );
		$this->assertNotEmpty( $slide_format );
	}

	/**
	 * @since	1.7.0
	 */
	function test_is_upcoming_productions_slide_format_registered_when_theater_is_active() {
		$slide_format = ScreenWP_Slides::get_slide_format_by_slug( 'upcoming-productions' );
		$this->assertNotEmpty( $slide_format );
	}
}
