<?php

class Test_VUWU_Public extends VUWU_UnitTestCase {

	function test_are_scripts_and_styles_not_enqueued_on_regular_post() {

		/* Create a regular post */
		$post_args = array(
			'post_type' => 'post',
		);
		$post_id = $this->factory->post->create( $post_args );

		$this->go_to( get_permalink( $post_id ) );

		$actual = get_echo( 'wp_head' );

		$this->assertNotContains( 'vuwu-public-min.js', $actual );
		$this->assertNotContains( 'vuwu-public.css', $actual );
	}

	function test_are_scripts_and_styles_enqueued_on_vuwu_display() {

		$this->go_to( get_permalink( $this->display1 ) );

		$actual = get_echo( 'wp_head' );

		$this->assertContains( 'vuwu-public-min.js', $actual );
		$this->assertContains( 'vuwu-public.css', $actual );
	}
}
