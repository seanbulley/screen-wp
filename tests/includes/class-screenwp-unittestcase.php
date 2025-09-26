<?php

class ScreenWP_UnitTestCase extends WP_UnitTestCase {

	function setUp() {
		parent::setUp();

		$slide_args = array(
			'post_type' => ScreenWP_Slide::post_type_name,
		);
		$channel_args = array(
			'post_type' => ScreenWP_Channel::post_type_name,
		);
		$display_args = array(
			'post_type' => ScreenWP_Display::post_type_name,
		);

		/* Create slides */
		$this->slide1 = $this->factory->post->create( $slide_args );
		$this->slide2 = $this->factory->post->create( $slide_args );
		$this->slide3 = $this->factory->post->create( $slide_args );

		/* Create channel with two slides */
		$this->channel1 = $this->factory->post->create( $channel_args );
		add_post_meta( $this->channel1, ScreenWP_Slide::post_type_name, array( $this->slide1, $this->slide2 ) );

		/* Create channel with one slide */
		$this->channel2 = $this->factory->post->create( $channel_args );
		add_post_meta( $this->channel2, ScreenWP_Slide::post_type_name, array( $this->slide1 ) );

		/* Create display with a default channel */
		$this->display1 = $this->factory->post->create( $display_args );
		add_post_meta( $this->display1, ScreenWP_Channel::post_type_name, $this->channel1 );

	}

	function assume_role( $role = 'author' ) {
		$user = new WP_User( $this->factory->user->create( array( 'role' => $role ) ) );
		wp_set_current_user( $user->ID );
		return $user;
	}
}