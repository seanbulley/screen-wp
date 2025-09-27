<?php

class Test_VUWU_Channel extends VUWU_UnitTestCase {

	function test_are_all_published_slides_included_in_slides() {

		/* Create three slides */
		$slide_args = array(
			'post_type' => VUWU_Slide::post_type_name,
		);

		$slide_1_id = $this->factory->post->create( $slide_args );
		$slide_2_id = $this->factory->post->create( $slide_args );
		$slide_3_id = $this->factory->post->create( $slide_args );

		/* Create channel with all three slides */
		$channel_args = array(
			'post_type' => VUWU_Channel::post_type_name,
		);

		$channel_id = $this->factory->post->create( $channel_args );
		add_post_meta( $channel_id, VUWU_Slide::post_type_name, array( $slide_1_id, $slide_2_id, $slide_3_id ) );

		$channel = new VUWU_Channel( $channel_id );

		$expected = array(
			new VUWU_Slide( $slide_1_id ),
			new VUWU_Slide( $slide_2_id ),
			new VUWU_Slide( $slide_3_id ),
		);
		$actual = $channel->get_slides();

		$this->assertEquals( $expected, $actual );
	}

	function test_is_trashed_slide_excluded_from_slides() {

		/* Create three slides */
		$slide_args = array(
			'post_type' => VUWU_Slide::post_type_name,
		);

		$slide_1_id = $this->factory->post->create( $slide_args );
		$slide_2_id = $this->factory->post->create( $slide_args );
		$slide_3_id = $this->factory->post->create( $slide_args );

		/* Create channel with all three slides */
		$channel_args = array(
			'post_type' => VUWU_Channel::post_type_name,
		);

		$channel_id = $this->factory->post->create( $channel_args );
		add_post_meta( $channel_id, VUWU_Slide::post_type_name, array( $slide_1_id, $slide_2_id, $slide_3_id ) );

		// Trash one of the posts
		$args = array(
			'ID' => $slide_2_id,
			'post_status' => 'trash',
		);
		wp_update_post( $args );

		$channel = new VUWU_Channel( $channel_id );

		$expected = array(
			new VUWU_Slide( $slide_1_id ),
			new VUWU_Slide( $slide_3_id ),
		);
		$actual = $channel->get_slides();

		$this->assertEquals( $expected, $actual );
	}
}
