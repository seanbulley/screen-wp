<?php

class Test_ScreenWP_Slide_Backgrounds extends ScreenWP_UnitTestCase {

	/**
	 * @since	1.4.0
	 */
	function test_is_default_slide_background_registered() {
		$slide_background = ScreenWP_Slides::get_slide_background_by_slug( 'default' );
		$this->assertNotEmpty( $slide_background );
	}

	/**
	 * @since	1.4.0
	 */
	function test_is_image_slide_background_registered() {
		$slide_background = ScreenWP_Slides::get_slide_background_by_slug( 'image' );
		$this->assertNotEmpty( $slide_background );
	}

	/**
	 * @since	1.4.0
	 */
	function test_is_video_slide_background_registered() {
		$slide_background = ScreenWP_Slides::get_slide_background_by_slug( 'video' );
		$this->assertNotEmpty( $slide_background );
	}

	/**
	 * @since	1.6.0
	 */
	function test_is_html5_video_slide_background_registered() {
		$slide_background = ScreenWP_Slides::get_slide_background_by_slug( 'html5-video' );
		$this->assertNotEmpty( $slide_background );
	}
}
