<?php

class Test_ScreenWP_Public extends ScreenWP_UnitTestCase {

	function test_are_scripts_and_styles_not_enqueued_on_regular_post() {

		/* Create a regular post */
		$post_args = array(
			'post_type' => 'post',
		);
		$post_id = $this->factory->post->create( $post_args );

		$this->go_to( get_permalink( $post_id ) );

		$actual = get_echo( 'wp_head' );

		$this->assertNotContains( 'screenwp-public-min.js', $actual );
		$this->assertNotContains( 'screenwp-public.css', $actual );
	}

	function test_are_scripts_and_styles_enqueued_on_screenwp_display() {

		$this->go_to( get_permalink( $this->display1 ) );

		$actual = get_echo( 'wp_head' );

		$this->assertContains( 'screenwp-public-min.js', $actual );
		$this->assertContains( 'screenwp-public.css', $actual );
	}
}
