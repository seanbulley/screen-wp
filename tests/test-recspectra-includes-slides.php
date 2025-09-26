<?php

class Test_Recspectra_Slides extends Recspectra_UnitTestCase {

	function add_not_registered_background_to_default_format( $slide_format_backgrounds ) {
		$slide_format_backgrounds[] = 'not-registered';
		return $slide_format_backgrounds;
	}

	/* Tests */

	function test_get_slide_formats() {
		$actual = Recspectra_Slides::get_slide_formats();
		$this->assertNotEmpty( $actual );
	}

	/**
	 * @since	1.4.0
	 */
	function test_get_slide_backgrounds() {
		$actual = Recspectra_Slides::get_slide_backgrounds();
		$this->assertNotEmpty( $actual );
	}

	/**
	 * @since	1.4.0
	 */
	function test_get_slide_format_backgrounds_by_slug() {
		$actual = Recspectra_Slides::get_slide_format_backgrounds_by_slug( 'default' );
		$this->assertNotEmpty( $actual );
	}

	/**
	 * @since	1.4.0
	 */
	function test_get_slide_format_backgrounds_by_slug_does_not_return_not_registered_backgrounds() {

		add_filter( 'recspectra/slides/backgrounds/format=default', array( $this, 'add_not_registered_background_to_default_format' ) );

		$actual = array_keys( Recspectra_Slides::get_slide_format_backgrounds_by_slug( 'default' ) );

		$this->assertNotContains( 'not-registered', $actual );
	}
}
