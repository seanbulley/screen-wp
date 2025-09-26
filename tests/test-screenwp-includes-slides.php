<?php

class Test_ScreenWP_Slides extends ScreenWP_UnitTestCase {

	function add_not_registered_background_to_default_format( $slide_format_backgrounds ) {
		$slide_format_backgrounds[] = 'not-registered';
		return $slide_format_backgrounds;
	}

	/* Tests */

	function test_get_slide_formats() {
		$actual = ScreenWP_Slides::get_slide_formats();
		$this->assertNotEmpty( $actual );
	}

	/**
	 * @since	1.4.0
	 */
	function test_get_slide_backgrounds() {
		$actual = ScreenWP_Slides::get_slide_backgrounds();
		$this->assertNotEmpty( $actual );
	}

	/**
	 * @since	1.4.0
	 */
	function test_get_slide_format_backgrounds_by_slug() {
		$actual = ScreenWP_Slides::get_slide_format_backgrounds_by_slug( 'default' );
		$this->assertNotEmpty( $actual );
	}

	/**
	 * @since	1.4.0
	 */
	function test_get_slide_format_backgrounds_by_slug_does_not_return_not_registered_backgrounds() {

		add_filter( 'screenwp/slides/backgrounds/format=default', array( $this, 'add_not_registered_background_to_default_format' ) );

		$actual = array_keys( ScreenWP_Slides::get_slide_format_backgrounds_by_slug( 'default' ) );

		$this->assertNotContains( 'not-registered', $actual );
	}
}
