<?php

class Test_Recspectra_Display extends Recspectra_UnitTestCase {

	function test_is_default_channel_used_when_schedule_has_no_channel() {

		$channel_title = 'Plain default channel';

		/* Create channel */
		$channel_args = array(
			'post_type' => Recspectra_Channel::post_type_name,
			'post_title' => $channel_title,
		);

		$channel_id = $this->factory->post->create( $channel_args );

		/* Create display with our channel as default, and a faulty schedule without channel */
		$display_args = array(
			'post_type' => Recspectra_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );
		add_post_meta( $display_id, Recspectra_Channel::post_type_name, $channel_id );

		$schedule = array(
			array(
				'channel'    => 0,
				'priority'   => 0,
				'date_start' => '',
				'date_end'   => '',
				'time_start' => '',
				'time_end'   => '',
				'days'       => array(),
			),
		);
		update_post_meta( $display_id, 'recspectra_display_schedule', $schedule );

		$display = new Recspectra_Display( $display_id );

		$actual = $display->get_active_channel();

		$this->assertEquals( $channel_id, $actual );
	}

	function test_is_default_channel_used_when_schedule_has_not_published_channel() {

		/* Create published channel */
		$channel_args = array(
			'post_type' => Recspectra_Channel::post_type_name,
		);

		$channel_1_id = $this->factory->post->create( $channel_args );

		/* Create trashed channel */
		$channel_args = array(
			'post_type' => Recspectra_Channel::post_type_name,
			'post_status' => 'trash',
		);

		$channel_2_id = $this->factory->post->create( $channel_args );

		/* Create display with our published channel as default, and a schedule with trashed channel */
		$display_args = array(
			'post_type' => Recspectra_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );
		add_post_meta( $display_id, Recspectra_Channel::post_type_name, $channel_1_id );

		$schedule = array(
			array(
				'channel'    => $channel_2_id,
				'priority'   => 0,
				'date_start' => '',
				'date_end'   => '',
				'time_start' => '',
				'time_end'   => '',
				'days'       => array(),
			),
		);
		update_post_meta( $display_id, 'recspectra_display_schedule', $schedule );

		$display = new Recspectra_Display( $display_id );

		$actual = $display->get_active_channel();

		$this->assertEquals( $channel_1_id, $actual );
	}

	function test_is_scheduled_channel_used_when_schedule_has_published_channel() {

		/* Create published channels */
		$channel_args = array(
			'post_type' => Recspectra_Channel::post_type_name,
		);

		$channel_1_id = $this->factory->post->create( $channel_args );
		$channel_2_id = $this->factory->post->create( $channel_args );

		/* Create display with our published channel as default, and a schedule with the other published channel */
		$display_args = array(
			'post_type' => Recspectra_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );
		add_post_meta( $display_id, Recspectra_Channel::post_type_name, $channel_1_id );

		$schedule = array(
			array(
				'channel'    => $channel_2_id,
				'priority'   => 0,
				'date_start' => '',
				'date_end'   => '',
				'time_start' => '',
				'time_end'   => '',
				'days'       => array(),
			),
		);
		update_post_meta( $display_id, 'recspectra_display_schedule', $schedule );

		$display = new Recspectra_Display( $display_id );

		$actual = $display->get_active_channel();

		$this->assertEquals( $channel_2_id, $actual );
	}

	function test_is_default_channel_not_returned_when_channel_is_not_published() {

		/* Create trashed channel */
		$channel_args = array(
			'post_type' => Recspectra_Channel::post_type_name,
			'post_status' => 'trash',
		);

		$channel_id = $this->factory->post->create( $channel_args );

		/* Create display with our trashed channel as default */
		$display_args = array(
			'post_type' => Recspectra_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );
		add_post_meta( $display_id, Recspectra_Channel::post_type_name, $channel_id );

		$display = new Recspectra_Display( $display_id );

		$actual = $display->get_active_channel();

		$this->assertEquals( false, $actual );
	}

        function test_is_default_channel_returned_when_channel_is_published() {

                /* Create published channel */
                $channel_args = array(
                        'post_type' => Recspectra_Channel::post_type_name,
		);

		$channel_id = $this->factory->post->create( $channel_args );

		/* Create display with our published channel as default */
		$display_args = array(
			'post_type' => Recspectra_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );
		add_post_meta( $display_id, Recspectra_Channel::post_type_name, $channel_id );

                $display = new Recspectra_Display( $display_id );

                $actual = $display->get_active_channel();

                $this->assertEquals( $channel_id, $actual );
        }

        function test_highest_priority_channel_is_returned() {

                $this->assume_role( 'administrator' );

                $channel_args = array(
                        'post_type' => Recspectra_Channel::post_type_name,
                );

                $low_priority_channel = $this->factory->post->create( $channel_args );
                $high_priority_channel = $this->factory->post->create( $channel_args );

                $display_args = array(
                        'post_type' => Recspectra_Display::post_type_name,
                );

                $display_id = $this->factory->post->create( $display_args );

                $_POST[ Recspectra_Display::post_type_name.'_nonce' ] = wp_create_nonce( Recspectra_Display::post_type_name );
                $_POST['recspectra_channel_editor_' . Recspectra_Display::post_type_name] = $display_id;
                $_POST['recspectra_channel_editor_default_channel'] = $low_priority_channel;

                $schedule = array(
                        array(
                                'channel'    => $low_priority_channel,
                                'priority'   => 1,
                                'date_start' => '',
                                'date_end'   => '',
                                'time_start' => '',
                                'time_end'   => '',
                                'days'       => array(),
                        ),
                        array(
                                'channel'    => $high_priority_channel,
                                'priority'   => 10,
                                'date_start' => '',
                                'date_end'   => '',
                                'time_start' => '',
                                'time_end'   => '',
                                'days'       => array(),
                        ),
                );

                $_POST['recspectra_channel_schedule'] = $schedule;

                Recspectra_Admin_Display::save_display( $display_id );

                $display = new Recspectra_Display( $display_id );

                $this->assertEquals( $high_priority_channel, $display->get_active_channel() );
        }

        function test_schedule_respects_days_of_week() {

                $this->assume_role( 'administrator' );

                $channel_args = array(
                        'post_type' => Recspectra_Channel::post_type_name,
                );

                $default_channel = $this->factory->post->create( $channel_args );
                $scheduled_channel = $this->factory->post->create( $channel_args );

                $display_args = array(
                        'post_type' => Recspectra_Display::post_type_name,
                );

                $display_id = $this->factory->post->create( $display_args );

                $_POST[ Recspectra_Display::post_type_name.'_nonce' ] = wp_create_nonce( Recspectra_Display::post_type_name );
                $_POST['recspectra_channel_editor_' . Recspectra_Display::post_type_name] = $display_id;
                $_POST['recspectra_channel_editor_default_channel'] = $default_channel;

                $now = current_time( 'timestamp' );
                $current_day = intval( wp_date( 'w', $now ) );
                $current_date = wp_date( 'Y-m-d', $now );
                $time_start = wp_date( 'H:i', $now - 5 * MINUTE_IN_SECONDS );
                $time_end = wp_date( 'H:i', $now + 5 * MINUTE_IN_SECONDS );

                $_POST['recspectra_channel_schedule'] = array(
                        array(
                                'channel'    => $scheduled_channel,
                                'priority'   => 5,
                                'date_start' => $current_date,
                                'date_end'   => $current_date,
                                'time_start' => $time_start,
                                'time_end'   => $time_end,
                                'days'       => array( $current_day ),
                        ),
                );

                Recspectra_Admin_Display::save_display( $display_id );

                $display = new Recspectra_Display( $display_id );

                $this->assertEquals( $scheduled_channel, $display->get_active_channel() );

                $_POST['recspectra_channel_schedule'] = array(
                        array(
                                'channel'    => $scheduled_channel,
                                'priority'   => 5,
                                'date_start' => $current_date,
                                'date_end'   => $current_date,
                                'time_start' => $time_start,
                                'time_end'   => $time_end,
                                'days'       => array( ( $current_day + 1 ) % 7 ),
                        ),
                );

                Recspectra_Admin_Display::save_display( $display_id );

                $display = new Recspectra_Display( $display_id );

                $this->assertEquals( $default_channel, $display->get_active_channel() );
        }

	function test_is_scheduled_channel_used_when_schedule_is_now() {

		$this->assume_role( 'administrator' );

		/* Create channels */
		$channel_args = array(
			'post_type' => Recspectra_Channel::post_type_name,
		);

		$channel_1_id = $this->factory->post->create( $channel_args );
		$channel_2_id = $this->factory->post->create( $channel_args );

		/* Create display */
		$display_args = array(
			'post_type' => Recspectra_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );

		$default_channel = $channel_1_id;
		$scheduled_channel = $channel_2_id;
                $now = current_time( 'timestamp' );
                $schedule_date = wp_date( 'Y-m-d', $now );
                $schedule_start = wp_date( 'H:i', $now - 10 * MINUTE_IN_SECONDS );
                $schedule_end = wp_date( 'H:i', $now + 10 * MINUTE_IN_SECONDS );

                $_POST[ Recspectra_Display::post_type_name.'_nonce' ] = wp_create_nonce( Recspectra_Display::post_type_name );
                $_POST['recspectra_channel_editor_' . Recspectra_Display::post_type_name] = $display_id;
                $_POST['recspectra_channel_editor_default_channel'] = $default_channel;
                $_POST['recspectra_channel_schedule'] = array(
                        array(
                                'channel'    => $scheduled_channel,
                                'priority'   => 0,
                                'date_start' => $schedule_date,
                                'date_end'   => $schedule_date,
                                'time_start' => $schedule_start,
                                'time_end'   => $schedule_end,
                                'days'       => array(),
                        ),
                );

		Recspectra_Admin_Display::save_display( $display_id );

		$display = new Recspectra_Display( $display_id );

		$actual = $display->get_active_channel();

		$this->assertEquals( $scheduled_channel, $actual );
	}

	function test_is_default_channel_used_when_schedule_is_not_now() {

		$this->assume_role( 'administrator' );

		/* Create channels */
		$channel_args = array(
			'post_type' => Recspectra_Channel::post_type_name,
		);

		$channel_1_id = $this->factory->post->create( $channel_args );
		$channel_2_id = $this->factory->post->create( $channel_args );

		/* Create display */
		$display_args = array(
			'post_type' => Recspectra_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );

		$default_channel = $channel_1_id;
		$scheduled_channel = $channel_2_id;
                $now = current_time( 'timestamp' );
                $schedule_date = wp_date( 'Y-m-d', $now );
                $schedule_start = wp_date( 'H:i', $now + 10 * MINUTE_IN_SECONDS );
                $schedule_end = wp_date( 'H:i', $now + 20 * MINUTE_IN_SECONDS );

                $_POST[ Recspectra_Display::post_type_name.'_nonce' ] = wp_create_nonce( Recspectra_Display::post_type_name );
                $_POST['recspectra_channel_editor_' . Recspectra_Display::post_type_name] = $display_id;
                $_POST['recspectra_channel_editor_default_channel'] = $default_channel;
                $_POST['recspectra_channel_schedule'] = array(
                        array(
                                'channel'    => $scheduled_channel,
                                'priority'   => 0,
                                'date_start' => $schedule_date,
                                'date_end'   => $schedule_date,
                                'time_start' => $schedule_start,
                                'time_end'   => $schedule_end,
                                'days'       => array(),
                        ),
                );

		Recspectra_Admin_Display::save_display( $display_id );

		$display = new Recspectra_Display( $display_id );

		$actual = $display->get_active_channel();

		$this->assertEquals( $default_channel, $actual );
	}

	function test_is_scheduled_channel_used_when_schedule_is_now_and_timezone_set() {
		$timezone_offset = 5;
		update_option( 'gmt_offset', $timezone_offset );

		$this->assume_role( 'administrator' );

		/* Create channels */
		$channel_args = array(
			'post_type' => Recspectra_Channel::post_type_name,
		);

		$channel_1_id = $this->factory->post->create( $channel_args );
		$channel_2_id = $this->factory->post->create( $channel_args );

		/* Create display */
		$display_args = array(
			'post_type' => Recspectra_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );

		$default_channel = $channel_1_id;
		$scheduled_channel = $channel_2_id;
                $now = current_time( 'timestamp' );
                $schedule_date = wp_date( 'Y-m-d', $now );
                $schedule_start = wp_date( 'H:i', $now - 10 * MINUTE_IN_SECONDS );
                $schedule_end = wp_date( 'H:i', $now + 10 * MINUTE_IN_SECONDS );

		$_POST[ Recspectra_Display::post_type_name.'_nonce' ] = wp_create_nonce( Recspectra_Display::post_type_name );
		$_POST['recspectra_channel_editor_' . Recspectra_Display::post_type_name] = $display_id;
		$_POST['recspectra_channel_editor_default_channel'] = $default_channel;
                $_POST['recspectra_channel_schedule'] = array(
                        array(
                                'channel'    => $scheduled_channel,
                                'priority'   => 0,
                                'date_start' => $schedule_date,
                                'date_end'   => $schedule_date,
                                'time_start' => $schedule_start,
                                'time_end'   => $schedule_end,
                                'days'       => array(),
                        ),
                );

		Recspectra_Admin_Display::save_display( $display_id );

		$display = new Recspectra_Display( $display_id );

		$actual = $display->get_active_channel();

		$this->assertEquals( $scheduled_channel, $actual );
	}

	function test_is_default_channel_used_when_schedule_is_not_now_and_timezone_set() {
		$timezone_offset = 5;
		update_option( 'gmt_offset', $timezone_offset );

		$this->assume_role( 'administrator' );

		/* Create channels */
		$channel_args = array(
			'post_type' => Recspectra_Channel::post_type_name,
		);

		$channel_1_id = $this->factory->post->create( $channel_args );
		$channel_2_id = $this->factory->post->create( $channel_args );

		/* Create display */
		$display_args = array(
			'post_type' => Recspectra_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );

		$default_channel = $channel_1_id;
		$scheduled_channel = $channel_2_id;
                $now = current_time( 'timestamp' );
                $schedule_date = wp_date( 'Y-m-d', $now );
                $schedule_start = wp_date( 'H:i', $now + 10 * MINUTE_IN_SECONDS );
                $schedule_end = wp_date( 'H:i', $now + 20 * MINUTE_IN_SECONDS );

		$_POST[ Recspectra_Display::post_type_name.'_nonce' ] = wp_create_nonce( Recspectra_Display::post_type_name );
		$_POST['recspectra_channel_editor_' . Recspectra_Display::post_type_name] = $display_id;
		$_POST['recspectra_channel_editor_default_channel'] = $default_channel;
                $_POST['recspectra_channel_schedule'] = array(
                        array(
                                'channel'    => $scheduled_channel,
                                'priority'   => 0,
                                'date_start' => $schedule_date,
                                'date_end'   => $schedule_date,
                                'time_start' => $schedule_start,
                                'time_end'   => $schedule_end,
                                'days'       => array(),
                        ),
                );

		Recspectra_Admin_Display::save_display( $display_id );

		$display = new Recspectra_Display( $display_id );

		$actual = $display->get_active_channel();

		$this->assertEquals( $default_channel, $actual );
	}

	/**
	 * @since	1.4.0
	 */
	function test_is_reset_request_added() {

		/* Create display */
		$display_args = array(
			'post_type' => Recspectra_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );

		/* Check that no reset request is present */
		$this->assertEmpty( get_post_meta( $display_id, 'recspectra_reset_display', true ) );

		$display = new Recspectra_Display( $display_id );
		$display->add_reset_request();

		/* Check that reset request was added */
		$this->assertNotEmpty( get_post_meta( $display_id, 'recspectra_reset_display', true ) );
	}

	/**
	 * @since	1.4.0
	 */
	function test_is_reset_request_deleted() {

		/* Create display */
		$display_args = array(
			'post_type' => Recspectra_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );

		$display = new Recspectra_Display( $display_id );
		$display->add_reset_request();

		/* Check that reset request was added */
		$this->assertNotEmpty( get_post_meta( $display_id, 'recspectra_reset_display' ), true );

		$display->delete_reset_request();

		/* Check that no reset request is present after delete */
		$this->assertEmpty( get_post_meta( $display_id, 'recspectra_reset_display' ), true );
	}

	/**
	 * @since	1.4.0
	 */
	function test_is_recspectra_reset_display_class_added_when_reset_is_requested() {

		/* Create display */
		$display_args = array(
			'post_type' => Recspectra_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );

		$display = new Recspectra_Display( $display_id );

		/* Check that no recspectra-reset-display class is present by default */
		ob_start();
		$display->classes();
		$actual = ob_get_clean();

		$expected = 'recspectra-reset-display';
		$this->assertNotContains( $expected, $actual );

		$display->add_reset_request();

		/* Check that recspectra-reset-display class is added */
		ob_start();
		$display->classes();
		$actual = ob_get_clean();

		$expected = 'recspectra-reset-display';
		$this->assertContains( $expected, $actual );
	}

	/**
	 * @since	1.4.0
	 */
	function test_is_recspectra_reset_display_class_not_added_when_reset_is_requested_and_previewing() {

		// We are previewing
		$_GET['recspectra-preview'] = 1;

		/* Create display */
		$display_args = array(
			'post_type' => Recspectra_Display::post_type_name,
		);

		$display_id = $this->factory->post->create( $display_args );

		$display = new Recspectra_Display( $display_id );
		$display->add_reset_request();

		/* Check that recspectra-reset-display class is not added */
		ob_start();
		$display->classes();
		$actual = ob_get_clean();

		$expected = 'recspectra-reset-display';
		$this->assertNotContains( $expected, $actual );
	}
}
