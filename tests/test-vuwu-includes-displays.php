<?php

class Test_VUWU_Displays extends VUWU_UnitTestCase {

	/* Tests */

	/**
	 * @since	1.4.0
	 */
	function test_are_all_reset_requests_added() {

		/* Create many displays */
		$display_args = array(
			'post_type' => VUWU_Display::post_type_name,
		);
		$this->factory->post->create_many( 15, $display_args );

		$args = array(
			'post_type' => VUWU_Display::post_type_name,
			'posts_per_page' => -1,
		);
		$displays = get_posts( $args );

		/* Check that no reset requests are present */
		foreach ( $displays as $display ) {
			$this->assertEmpty( get_post_meta( $display->ID, 'vuwu_reset_display', true ) );
		}

		VUWU_Displays::reset_all_displays();

		/* Check that all reset requests were added */
		foreach ( $displays as $display ) {
			$this->assertNotEmpty( get_post_meta( $display->ID, 'vuwu_reset_display', true ) );
		}
	}
}
