<?php

class Recspectra_Ajax_UnitTestCase extends WP_Ajax_UnitTestCase {

	function setUp() {
		parent::setUp();

		$slide_args = array(
			'post_type' => Recspectra_Slide::post_type_name,
		);
		$channel_args = array(
			'post_type' => Recspectra_Channel::post_type_name,
		);

		/* Create slides */
		$this->slide1 = $this->factory->post->create( $slide_args );
		$this->slide2 = $this->factory->post->create( $slide_args );
		$this->slide3 = $this->factory->post->create( $slide_args );

		/* Create channel with two slides */
		$this->channel1 = $this->factory->post->create( $channel_args );
		add_post_meta( $this->channel1, Recspectra_Slide::post_type_name, array( $this->slide1, $this->slide2 ) );

		/* Create channel with one slide */
		$this->channel2 = $this->factory->post->create( $channel_args );
		add_post_meta( $this->channel2, Recspectra_Slide::post_type_name, array( $this->slide1 ) );
	}
}