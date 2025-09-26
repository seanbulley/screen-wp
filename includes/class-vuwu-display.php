<?php

/**
 * The display object model.
 *
 * @since		1.0.0
 *
 * @package		VUWU
 * @subpackage	VUWU/includes
 */
class VUWU_Display {

	/**
	 * The VUWU Display post type name.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $post_type_name    The VUWU Display post type name.
	 */
	const post_type_name = 'vuwu_display';

	public $ID;
	private $post;

	/**
	 * The currently active channel of this display.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $channel    The currently active channel of this display.
	 */
	private $active_channel;

	/**
	 * The default channel of this display.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $channel    The default channel of this display.
	 */
	private $default_channel;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since	1.0.0
	 * @param	int or WP_Post	$ID		The id or the WP_Post object of the display.
	 */
	public function __construct( $ID = false ) {

		if ( $ID instanceof WP_Post ) {
			// $ID is a WP_Post object
			$this->post = $ID;
			$ID = $ID->ID;
		}

		$this->ID = $ID;
	}

	/**
	 * Adds a request for the display to be reset.
	 *
	 * @since	1.4.0
	 *
	 * @return 	void
	 */
	public function add_reset_request() {
		update_post_meta( $this->ID, 'vuwu_reset_display', 1 );
	}

	/**
	 * Outputs the display classes for use in the template.
	 *
	 * The output is escaped, so this method can be used in templates without further escaping.
	 *
	 * @since	1.4.0
	 *
	 * @param 	array 	$classes
	 * @return 	void
	 */
	public function classes( $classes = array() ) {

		$classes[] = 'vuwu-display';

                $preview_flag = isset( $_GET['vuwu-preview'] ) ? sanitize_text_field( wp_unslash( $_GET['vuwu-preview'] ) ) : '';

                if ( $this->is_reset_requested() && empty( $preview_flag ) ) {
			// Reset is requested and we are not previewing, add class to invoke reset
			$classes[] = 'vuwu-reset-display';

			// Display will be reset, delete reset request
			$this->delete_reset_request();
		}

		if ( empty( $classes ) ) {
			return;
		}

		?> class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" <?php
	}

	/**
	 * Deletes the request for the display to be reset.
	 *
	 * @since	1.4.0
	 *
	 * @return 	void
	 */
	public function delete_reset_request() {
		delete_post_meta( $this->ID, 'vuwu_reset_display' );
	}

	/**
	 * Get the currently active channel for this display.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Only uses a schedule if the schedule's channel is set and published.
	 *
	 * @access	public
	 * @return	VUWU_Channel	The currently active channel for this display.
	 */
        public function get_active_channel() {

                if ( ! isset( $this->active_channel ) ) {

                        $active_channel = $this->get_default_channel();
                        $this->active_channel = $active_channel;

                        $schedule = $this->get_schedule();

                        if ( empty( $schedule ) ) {
                                return $this->active_channel;
                        }

                        $matching_channels = array();
                        $now               = $this->get_current_time();

                        foreach ( $schedule as $scheduled_channel ) {

                                $channel_id = isset( $scheduled_channel['channel'] ) ? intval( $scheduled_channel['channel'] ) : 0;

                                if ( empty( $channel_id ) ) {
                                        continue;
                                }

                                if ( 'publish' !== get_post_status( $channel_id ) ) {
                                        continue;
                                }

                                if ( ! $this->is_schedule_active( $scheduled_channel, $now ) ) {
                                        continue;
                                }

                                $priority = isset( $scheduled_channel['priority'] ) ? intval( $scheduled_channel['priority'] ) : 0;

                                $matching_channels[] = array(
                                        'channel'  => $channel_id,
                                        'priority' => $priority,
                                );
                        }

                        if ( ! empty( $matching_channels ) ) {
                                usort( $matching_channels, array( $this, 'sort_channels_by_priority' ) );
                                $this->active_channel = $matching_channels[0]['channel'];
                        }
                }

                return $this->active_channel;
        }


	/**
	 * Get the default channel for this display.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Only returns a channel if it is published.
	 *
	 * @access	public
	 * @return	VUWU_Channel	The default channel for this display.
	 */
	public function get_default_channel() {

		if ( ! isset( $this->default_channel ) ) {

			$default_channel = get_post_meta( $this->ID, VUWU_Channel::post_type_name, true );

			// Only use channel with post status 'publish'
			if ( 'publish' != get_post_status( $default_channel ) ) {
				$this->default_channel = false;
			}
			else {
				$this->default_channel = $default_channel;
			}
		}

		return $this->default_channel;
	}

	/**
	 * Gets all scheduled channels for this display.
	 *
	 * @since	1.0.0
	 * @return 	array|string	All scheduled channels or an empty string if no channels are scheduled.
	 */
        public function get_schedule() {
                $schedule = get_post_meta( $this->ID, 'vuwu_display_schedule', true );

                if ( empty( $schedule ) ) {
                        $legacy_schedule = get_post_meta( $this->ID, 'vuwu_display_schedule', false );

                        if ( ! empty( $legacy_schedule ) ) {
                                $schedule = array();

                                foreach ( $legacy_schedule as $legacy_entry ) {
                                        if ( ! is_array( $legacy_entry ) ) {
                                                continue;
                                        }

                                        $start_datetime = isset( $legacy_entry['start'] ) ? $this->convert_timestamp_to_datetime( $legacy_entry['start'] ) : null;
                                        $end_datetime   = isset( $legacy_entry['end'] ) ? $this->convert_timestamp_to_datetime( $legacy_entry['end'] ) : null;

                                        $schedule[] = array(
                                                'channel'    => isset( $legacy_entry['channel'] ) ? intval( $legacy_entry['channel'] ) : 0,
                                                'priority'   => 0,
                                                'date_start' => $start_datetime ? $start_datetime->format( 'Y-m-d' ) : '',
                                                'date_end'   => $end_datetime ? $end_datetime->format( 'Y-m-d' ) : '',
                                                'time_start' => $start_datetime ? $start_datetime->format( 'H:i' ) : '',
                                                'time_end'   => $end_datetime ? $end_datetime->format( 'H:i' ) : '',
                                                'days'       => array(),
                                        );
                                }
                        }
                }

                if ( ! is_array( $schedule ) ) {
                        return array();
                }

                $schedule = array_map( array( $this, 'normalize_schedule_entry' ), $schedule );

                return array_values( array_filter( $schedule ) );
        }

        private function get_current_time() {
                return new DateTimeImmutable( 'now', $this->get_site_timezone() );
        }

        private function normalize_schedule_entry( $entry ) {
                if ( ! is_array( $entry ) ) {
                        return null;
                }

                $channel = isset( $entry['channel'] ) ? intval( $entry['channel'] ) : 0;

                $priority = isset( $entry['priority'] ) ? intval( $entry['priority'] ) : 0;

                $date_start = isset( $entry['date_start'] ) ? sanitize_text_field( $entry['date_start'] ) : '';
                $date_end   = isset( $entry['date_end'] ) ? sanitize_text_field( $entry['date_end'] ) : '';
                $time_start = isset( $entry['time_start'] ) ? sanitize_text_field( $entry['time_start'] ) : '';
                $time_end   = isset( $entry['time_end'] ) ? sanitize_text_field( $entry['time_end'] ) : '';

                $days = array();
                if ( isset( $entry['days'] ) ) {
                        if ( is_array( $entry['days'] ) ) {
                                $days = array_map( 'intval', $entry['days'] );
                        } else {
                                $days = array_map( 'intval', explode( ',', $entry['days'] ) );
                        }
                }

                $days = array_values( array_intersect( range( 0, 6 ), $days ) );

                return array(
                        'channel'    => $channel,
                        'priority'   => $priority,
                        'date_start' => $date_start,
                        'date_end'   => $date_end,
                        'time_start' => $time_start,
                        'time_end'   => $time_end,
                        'days'       => $days,
                );
        }

        private function is_schedule_active( $schedule, DateTimeImmutable $now ) {
                $date_start = $this->create_date_from_string( $schedule['date_start'], 'start' );
                $date_end   = $this->create_date_from_string( $schedule['date_end'], 'end' );

                if ( $date_start && $now < $date_start ) {
                        return false;
                }

                if ( $date_end && $now > $date_end ) {
                        return false;
                }

                if ( ! empty( $schedule['days'] ) ) {
                        $weekday = intval( $now->format( 'w' ) );
                        if ( ! in_array( $weekday, $schedule['days'], true ) ) {
                                return false;
                        }
                }

                $time_start = $this->parse_time_to_seconds( $schedule['time_start'], 0 );
                $time_end   = $this->parse_time_to_seconds( $schedule['time_end'], DAY_IN_SECONDS );

                if ( $time_end <= $time_start ) {
                        return false;
                }

                $current_seconds = intval( $now->format( 'H' ) ) * HOUR_IN_SECONDS + intval( $now->format( 'i' ) ) * MINUTE_IN_SECONDS;

                return ( $current_seconds >= $time_start && $current_seconds < $time_end );
        }

        private function create_date_from_string( $date_string, $context = 'start' ) {
                if ( empty( $date_string ) ) {
                        return null;
                }

                $format = 'Y-m-d';
                $timezone = $this->get_site_timezone();
                $date = DateTimeImmutable::createFromFormat( $format, $date_string, $timezone );

                if ( ! $date ) {
                        return null;
                }

                if ( 'end' === $context ) {
                        return $date->setTime( 23, 59, 59 );
                }

                return $date->setTime( 0, 0, 0 );
        }

        private function parse_time_to_seconds( $time_string, $default ) {
                if ( empty( $time_string ) ) {
                        return $default;
                }

                $date = DateTimeImmutable::createFromFormat( 'H:i', $time_string, $this->get_site_timezone() );

                if ( ! $date ) {
                        return $default;
                }

                return intval( $date->format( 'H' ) ) * HOUR_IN_SECONDS + intval( $date->format( 'i' ) ) * MINUTE_IN_SECONDS;
        }

        private function convert_timestamp_to_datetime( $timestamp ) {
                if ( empty( $timestamp ) ) {
                        return null;
                }

                try {
                        $date = new DateTimeImmutable( '@' . intval( $timestamp ) );
                } catch ( Exception $exception ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
                        return null;
                }

                return $date->setTimezone( $this->get_site_timezone() );
        }

        private function sort_channels_by_priority( $a, $b ) {
                if ( $a['priority'] === $b['priority'] ) {
                        return 0;
                }

                return ( $a['priority'] > $b['priority'] ) ? -1 : 1;
        }

        private function get_site_timezone() {
                if ( function_exists( 'wp_timezone' ) ) {
                        return wp_timezone();
                }

                $timezone_string = get_option( 'timezone_string' );

                if ( ! empty( $timezone_string ) ) {
                        try {
                                return new DateTimeZone( $timezone_string );
                        } catch ( Exception $exception ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
                                // Fallback to offset handling below.
                        }
                }

                $offset  = floatval( get_option( 'gmt_offset', 0 ) );
                $hours   = (int) $offset;
                $minutes = abs( $offset - $hours ) * 60;
                $sign    = ( $offset < 0 ) ? '-' : '+';
                $timezone_offset = sprintf( '%s%02d:%02d', $sign, abs( $hours ), $minutes );

                try {
                        return new DateTimeZone( $timezone_offset );
                } catch ( Exception $exception ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
                        return new DateTimeZone( 'UTC' );
                }
        }

	/**
	 * Checks if a reset is requested for this display.
	 *
	 * @since	1.4.0
	 *
	 * @return 	bool	True if reset is requested for this display, false otherwise.
	 */
	private function is_reset_requested() {
		return (bool) get_post_meta( $this->ID, 'vuwu_reset_display', true );
	}
}
