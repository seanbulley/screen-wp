<?php

class Test_Recspectra_Public_Templates_Slides_Backgrounds_Video extends Recspectra_UnitTestCase {

	/**
	 * @since	1.4.0
	 * @since	1.5.1	Now also tests if the new slide_bg_video_enable_sound option is saved.
	 */
	function test_are_all_slide_background_video_properties_included_in_slide() {

		$this->assume_role( 'administrator' );

		$video_id = 'r9tbusKyvMY';
		$video_url = 'https://youtu.be/' . $video_id;
		$video_start = '35';
		$video_end = '60';
		$hold_slide = '1';
		$enable_sound = '1';

		update_post_meta( $this->slide1, 'slide_format', '' );
		update_post_meta( $this->slide1, 'slide_background', 'video' );
		update_post_meta( $this->slide1, 'slide_bg_video_video_url', $video_url );
		update_post_meta( $this->slide1, 'slide_bg_video_video_start', $video_start );
		update_post_meta( $this->slide1, 'slide_bg_video_video_end', $video_end );
		update_post_meta( $this->slide1, 'slide_bg_video_hold_slide', $hold_slide );
		update_post_meta( $this->slide1, 'slide_bg_video_enable_sound', $enable_sound );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Recspectra_Templates::get_template('partials/slide.php');
		$actual = ob_get_clean();

		$expected = 'data-recspectra-video-id="' . $video_id . '"';
		$this->assertContains( $expected, $actual );

		$expected = 'data-recspectra-video-start="' . $video_start . '"';
		$this->assertContains( $expected, $actual );

		$expected = 'data-recspectra-video-end="' . $video_end . '"';
		$this->assertContains( $expected, $actual );

		$expected = 'data-recspectra-hold-slide="' . $hold_slide . '"';
		$this->assertContains( $expected, $actual );

		$expected = 'data-recspectra-output-sound="' . $enable_sound . '"';
		$this->assertContains( $expected, $actual );
	}
}

