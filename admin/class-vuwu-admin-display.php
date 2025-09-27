<?php

/**
 * The display admin-specific functionality of the plugin.
 *
 * @since		1.0.0
 * @since		1.3.2	Refactored class from object to static methods.
 *
 * @package		VUWU
 * @subpackage	VUWU/admin
 */
class VUWU_Admin_Display {

	/**
	 * Adds Default Channel and Active Channel columns to the Displays admin table.
	 *
	 * Also removes the Date column.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param 	array	$columns	The current columns.
	 * @return	array				The new columns.
	 */
	static function add_channel_columns($columns) {
		unset($columns['date']);
		return array_merge($columns,
			array(
				'default_channel' => __('Default channel', 'vuwu'),
				'active_channel' => __('Active channel', 'vuwu'),
			)
		);
	}

	/**
	 * Adds the channel editor meta box to the display admin page.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 * @since	1.5.1	Added context to the translatable string 'Channel' to make translation easier.
	 */
	static function add_channel_editor_meta_box() {
		add_meta_box(
			'vuwu_channel_editor',
			_x( 'Channel', 'channel cpt', 'vuwu' ),
			array( __CLASS__, 'channel_editor_meta_box' ),
			VUWU_Display::post_type_name,
			'normal',
			'high'
		);
	}

	/**
	 * Adds the channel scheduler meta box to the display admin page.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 */
        static function add_channel_scheduler_meta_box() {
                add_meta_box(
                        'vuwu_channel_scheduler',
                        __( 'Channel schedule' , 'vuwu' ),
                        array( __CLASS__, 'channel_scheduler_meta_box' ),
                        VUWU_Display::post_type_name,
                        'normal',
                        'high'
                );
        }

	/**
	 * Outputs the content of the channel editor meta box.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Sanitized the output.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param	WP_Post		$post	The post object of the current display.
	 */
	static function channel_editor_meta_box( $post ) {

		wp_nonce_field( VUWU_Display::post_type_name, VUWU_Display::post_type_name.'_nonce' );

		ob_start();

		?>
			<input type="hidden" id="vuwu_channel_editor_<?php echo VUWU_Display::post_type_name; ?>"
				name="vuwu_channel_editor_<?php echo VUWU_Display::post_type_name; ?>" value="<?php echo intval( $post->ID ); ?>">

			<table class="vuwu_meta_box_form form-table vuwu_channel_editor_form" data-display-id="<?php echo intval( $post->ID ); ?>">
				<tbody>
					<?php

						echo self::get_default_channel_html( $post );

					?>
				</tbody>
			</table>

		<?php

		$html = ob_get_clean();

		echo $html;
	}

	/**
	 * Outputs the content of the channel scheduler meta box.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Sanitized the output.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param	WP_Post		$post	The post object of the current display.
	 */
	static function channel_scheduler_meta_box( $post ) {

		wp_nonce_field( VUWU_Display::post_type_name, VUWU_Display::post_type_name.'_nonce' );

		ob_start();

		?>
			<input type="hidden" id="vuwu_channel_editor_<?php echo VUWU_Display::post_type_name; ?>"
				name="vuwu_channel_editor_<?php echo VUWU_Display::post_type_name; ?>" value="<?php echo intval( $post->ID ); ?>">

			<table class="vuwu_meta_box_form form-table vuwu_channel_editor_form" data-display-id="<?php echo intval( $post->ID ); ?>">
				<tbody>
					<?php

						echo self::get_scheduled_channel_html( $post );

					?>
				</tbody>
			</table>

		<?php

		$html = ob_get_clean();

		echo $html;
	}

	/**
	 * Outputs the Active Channel and Defaults Channel columns.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Escaped the output.
	 * @since	1.3.2	Changed method to static.
	 *					Used post_id param instead of get_the_id() to allow for testing.
	 *					Outputs 'None' if no channel is set. Fixes #10.
	 *
	 * @param 	string	$column		The current column that needs output.
	 * @param 	int 	$post_id 	The current display ID.
	 * @return	void
	 */
	static function do_channel_columns( $column, $post_id ) {

	    switch ( $column ) {

		    case 'active_channel' :

				$display = new VUWU_Display( $post_id );

				if ( ! $active_channel_id = $display->get_active_channel() ) {
					_e( 'None', 'vuwu' );
					break;
				}

				$channel = new VUWU_Channel( $active_channel_id );

				?><a href="<?php echo esc_url( get_edit_post_link( $channel->ID ) ); ?>"><?php
					echo esc_html( get_the_title( $channel->ID ) );
				?></a><?php

		        break;

		    case 'default_channel' :

				$display = new VUWU_Display( $post_id );

				if ( ! $default_channel_id = $display->get_default_channel() ) {
					_e( 'None', 'vuwu' );
					break;
				}

				$channel = new VUWU_Channel( $default_channel_id );

				?><a href="<?php echo esc_url( get_edit_post_link( $channel->ID ) ); ?>"><?php
					echo esc_html( get_the_title( $channel->ID ) );
				?></a><?php

		        break;
	    }
	}

	/**
	 * Gets the defaults to be used in the channel scheduler.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 *
	 * @return	string	The defaults to be used in the channel scheduler.
	 */
	static function get_channel_scheduler_defaults() {
		$language_parts = explode( '-', get_bloginfo( 'language' ) );

		$defaults = array(
			'datetime_format' => 'Y-m-d H:i',
			'duration' => 1 * 60 * 60, // one hour in seconds
			'locale' => $language_parts[0], // locale formatted as 'en' instead of 'en-US'
			'start_of_week' => get_option( 'start_of_week' ),
		);

		/**
		 * Filters the channel scheduler defaults.
		 *
		 * @since 1.0.0
		 *
		 * @param array $defaults	The current defaults to be used in the channel scheduler.
		 */
		return apply_filters( 'vuwu/channel_scheduler/defaults', $defaults );
	}

	/**
	 * Gets the HTML that lists the default channel in the channel editor.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Escaped and sanitized the output.
	 * @since	1.2.3	Changed the list of available channels from limited to unlimited.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param	WP_Post	$post
	 * @return	string	$html	The HTML that lists the default channel in the channel editor.
	 */
	static function get_default_channel_html( $post ) {

		$display = new VUWU_Display( $post );
		$default_channel = $display->get_default_channel();

		ob_start();

		?>
			<tr>
				<th>
					<label for="vuwu_channel_editor_default_channel">
						<?php echo esc_html__( 'Default channel', 'vuwu' ); ?>
					</label>
				</th>
				<td>
					<select id="vuwu_channel_editor_default_channel" name="vuwu_channel_editor_default_channel">
						<option value="">(<?php echo esc_html__( 'Select a channel', 'vuwu' ); ?>)</option>
						<?php
							$channels = VUWU_Channels::get_posts();
							foreach ( $channels as $channel ) {
								$checked = '';
								if ( $default_channel == $channel->ID ) {
									$checked = 'selected="selected"';
								}
							?>
								<option value="<?php echo intval( $channel->ID ); ?>" <?php echo $checked; ?>><?php echo esc_html( get_the_title( $channel->ID ) ); ?></option>
							<?php
							}
						?>
					</select>
				</td>
			</tr>
		<?php

		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Gets the HTML that lists the scheduled channels in the channel scheduler.
	 *
	 * Currently limited to only one scheduled channel.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Escaped and sanitized the output.
	 * @since	1.2.3	Changed the list of available channels from limited to unlimited.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param	WP_Post	$post
	 * @return	string	$html	The HTML that lists the scheduled channels in the channel scheduler.
	 */
        static function get_scheduled_channel_html( $post ) {

                $display  = new VUWU_Display( $post );
                $schedule = $display->get_schedule();
                $channels = VUWU_Channels::get_posts();

                if ( empty( $schedule ) ) {
                        $schedule = array(
                                array(
                                        'channel'    => '',
                                        'priority'   => 0,
                                        'date_start' => '',
                                        'date_end'   => '',
                                        'time_start' => '',
                                        'time_end'   => '',
                                        'days'       => array(),
                                ),
                        );
                }

                ob_start();

                ?>
                        <tr>
                                <th><?php echo esc_html__( 'Schedules', 'vuwu' ); ?></th>
                                <td>
                                        <table class="vuwu-channel-schedule-table widefat">
                                                <thead>
                                                        <tr>
                                                                <th><?php echo esc_html__( 'Channel', 'vuwu' ); ?></th>
                                                                <th><?php echo esc_html__( 'Priority', 'vuwu' ); ?></th>
                                                                <th><?php echo esc_html__( 'Date range', 'vuwu' ); ?></th>
                                                                <th><?php echo esc_html__( 'Time window', 'vuwu' ); ?></th>
                                                                <th><?php echo esc_html__( 'Days of week', 'vuwu' ); ?></th>
                                                                <th class="vuwu-channel-schedule-actions">&nbsp;</th>
                                                        </tr>
                                                </thead>
                                                <tbody class="vuwu-channel-schedule-rows">
                                                        <?php
                                                        foreach ( $schedule as $index => $rule ) {
                                                                echo self::render_schedule_row( $index, $rule, $channels );
                                                        }
                                                        ?>
                                                </tbody>
                                        </table>
                                        <p>
                                                <button type="button" class="button button-secondary vuwu-add-schedule-row"><?php echo esc_html__( 'Add schedule', 'vuwu' ); ?></button>
                                        </p>
                                        <script type="text/html" id="vuwu-channel-schedule-row-template">
                                                <?php echo self::render_schedule_row( '__INDEX__', array( 'channel' => '', 'priority' => 0, 'date_start' => '', 'date_end' => '', 'time_start' => '', 'time_end' => '', 'days' => array() ), $channels, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                        </script>
                                </td>
                        </tr>
                <?php

                $html = ob_get_clean();

                return $html;
        }

        private static function render_schedule_row( $index, $rule, $channels, $is_template = false ) {

                $index_attr = $is_template ? '__INDEX__' : intval( $index );

                $channel_id = isset( $rule['channel'] ) ? intval( $rule['channel'] ) : 0;
                $priority   = isset( $rule['priority'] ) ? intval( $rule['priority'] ) : 0;
                $date_start = isset( $rule['date_start'] ) ? $rule['date_start'] : '';
                $date_end   = isset( $rule['date_end'] ) ? $rule['date_end'] : '';
                $time_start = isset( $rule['time_start'] ) ? $rule['time_start'] : '';
                $time_end   = isset( $rule['time_end'] ) ? $rule['time_end'] : '';
                $days       = isset( $rule['days'] ) && is_array( $rule['days'] ) ? array_map( 'intval', $rule['days'] ) : array();

                $days_of_week = array(
                        1 => esc_html__( 'Monday', 'vuwu' ),
                        2 => esc_html__( 'Tuesday', 'vuwu' ),
                        3 => esc_html__( 'Wednesday', 'vuwu' ),
                        4 => esc_html__( 'Thursday', 'vuwu' ),
                        5 => esc_html__( 'Friday', 'vuwu' ),
                        6 => esc_html__( 'Saturday', 'vuwu' ),
                        0 => esc_html__( 'Sunday', 'vuwu' ),
                );

                ob_start();

                $summary_text = self::get_schedule_summary_text(
                        array(
                                'date_start' => $date_start,
                                'date_end'   => $date_end,
                                'time_start' => $time_start,
                                'time_end'   => $time_end,
                                'days'       => $days,
                        )
                );

                ?>
                <tr class="vuwu-channel-schedule-row" data-index="<?php echo esc_attr( $index_attr ); ?>">
                        <td>
                                <select name="vuwu_channel_schedule[<?php echo esc_attr( $index_attr ); ?>][channel]">
                                        <option value="">(<?php echo esc_html__( 'Select a channel', 'vuwu' ); ?>)</option>
                                        <?php foreach ( $channels as $channel ) : ?>
                                                <option value="<?php echo intval( $channel->ID ); ?>" <?php selected( $channel_id, $channel->ID ); ?>><?php echo esc_html( get_the_title( $channel->ID ) ); ?></option>
                                        <?php endforeach; ?>
                                </select>
                        </td>
                        <td>
                                <input type="number" class="small-text" name="vuwu_channel_schedule[<?php echo esc_attr( $index_attr ); ?>][priority]" value="<?php echo esc_attr( $priority ); ?>" />
                        </td>
                        <td>
                                <label>
                                        <span class="screen-reader-text"><?php echo esc_html__( 'Start date', 'vuwu' ); ?></span>
                                        <input type="date" name="vuwu_channel_schedule[<?php echo esc_attr( $index_attr ); ?>][date_start]" value="<?php echo esc_attr( $date_start ); ?>" />
                                </label>
                                <label>
                                        <span class="screen-reader-text"><?php echo esc_html__( 'End date', 'vuwu' ); ?></span>
                                        <input type="date" name="vuwu_channel_schedule[<?php echo esc_attr( $index_attr ); ?>][date_end]" value="<?php echo esc_attr( $date_end ); ?>" />
                                </label>
                        </td>
                        <td>
                                <label>
                                        <span class="screen-reader-text"><?php echo esc_html__( 'Start time', 'vuwu' ); ?></span>
                                        <input type="time" name="vuwu_channel_schedule[<?php echo esc_attr( $index_attr ); ?>][time_start]" value="<?php echo esc_attr( $time_start ); ?>" />
                                </label>
                                <label>
                                        <span class="screen-reader-text"><?php echo esc_html__( 'End time', 'vuwu' ); ?></span>
                                        <input type="time" name="vuwu_channel_schedule[<?php echo esc_attr( $index_attr ); ?>][time_end]" value="<?php echo esc_attr( $time_end ); ?>" />
                                </label>
                        </td>
                        <td class="vuwu-channel-schedule-days">
                                <?php foreach ( $days_of_week as $day_index => $day_label ) : ?>
                                        <label class="vuwu-channel-schedule-day">
                                                <input type="checkbox" value="<?php echo esc_attr( $day_index ); ?>" name="vuwu_channel_schedule[<?php echo esc_attr( $index_attr ); ?>][days][]" <?php checked( in_array( $day_index, $days, true ), true ); ?> />
                                                <span><?php echo esc_html( $day_label ); ?></span>
                                        </label>
                                <?php endforeach; ?>
                        </td>
                        <td class="vuwu-channel-schedule-actions">
                                <button type="button" class="button-link delete vuwu-remove-schedule-row">
                                        <?php echo esc_html__( 'Remove', 'vuwu' ); ?>
                                </button>
                        </td>
                </tr>
                <tr class="vuwu-channel-schedule-row-summary" data-index="<?php echo esc_attr( $index_attr ); ?>">
                        <td colspan="6">
                                <div class="vuwu-channel-schedule-summary">
                                        <?php echo esc_html( $summary_text ); ?>
                                </div>
                        </td>
                </tr>

                <?php

                return ob_get_clean();
        }

        /**
         * Generates a human-friendly summary of the supplied schedule rule.
         *
         * @since 1.8.0
         *
         * @param array $rule Schedule rule values.
         * @return string
         */
        private static function get_schedule_summary_text( array $rule ) {
                $days_text = self::format_schedule_days( isset( $rule['days'] ) ? (array) $rule['days'] : array() );
                $time_text = self::format_schedule_time_window(
                        isset( $rule['time_start'] ) ? $rule['time_start'] : '',
                        isset( $rule['time_end'] ) ? $rule['time_end'] : ''
                );
                $date_text = self::format_schedule_date_window(
                        isset( $rule['date_start'] ) ? $rule['date_start'] : '',
                        isset( $rule['date_end'] ) ? $rule['date_end'] : ''
                );

                $parts = array_filter( array( $days_text, $time_text, $date_text ) );

                if ( empty( $parts ) ) {
                        return esc_html__( 'No scheduling constraints.', 'vuwu' );
                }

                $sentence = trim( preg_replace( '/\s+/', ' ', implode( ' ', $parts ) ) );

                if ( '' === $sentence ) {
                        $sentence = esc_html__( 'No scheduling constraints.', 'vuwu' );
                }

                if ( '.' !== substr( $sentence, -1 ) ) {
                        $sentence .= '.';
                }

                return $sentence;
        }

        /**
         * Formats the days component of a schedule summary.
         *
         * @since 1.8.0
         *
         * @param array $days Selected days.
         * @return string
         */
        private static function format_schedule_days( array $days ) {
                $available_days = array( 1, 2, 3, 4, 5, 6, 0 );
                $days           = array_values( array_unique( array_map( 'intval', $days ) ) );
                sort( $days );

                if ( empty( $days ) || count( array_intersect( $available_days, $days ) ) === count( $available_days ) ) {
                        return esc_html__( 'Every day', 'vuwu' );
                }

                $labels = array(
                        1 => esc_html__( 'Mondays', 'vuwu' ),
                        2 => esc_html__( 'Tuesdays', 'vuwu' ),
                        3 => esc_html__( 'Wednesdays', 'vuwu' ),
                        4 => esc_html__( 'Thursdays', 'vuwu' ),
                        5 => esc_html__( 'Fridays', 'vuwu' ),
                        6 => esc_html__( 'Saturdays', 'vuwu' ),
                        0 => esc_html__( 'Sundays', 'vuwu' ),
                );

                $ordered_labels = array();
                foreach ( $available_days as $day ) {
                        if ( in_array( $day, $days, true ) && isset( $labels[ $day ] ) ) {
                                $ordered_labels[] = $labels[ $day ];
                        }
                }

                if ( empty( $ordered_labels ) ) {
                        return esc_html__( 'Every day', 'vuwu' );
                }

                return self::humanize_list( $ordered_labels );
        }

        /**
         * Formats the time window component of a schedule summary.
         *
         * @since 1.8.0
         *
         * @param string $start Start time (H:i).
         * @param string $end   End time (H:i).
         * @return string
         */
        private static function format_schedule_time_window( $start, $end ) {
                $start_formatted = self::format_time_value( $start );
                $end_formatted   = self::format_time_value( $end );

                if ( $start_formatted && $end_formatted ) {
                        return ucfirst( sprintf( esc_html__( 'from %1$s to %2$s', 'vuwu' ), $start_formatted, $end_formatted ) );
                }

                if ( $start_formatted ) {
                        return ucfirst( sprintf( esc_html__( 'from %s onward', 'vuwu' ), $start_formatted ) );
                }

                if ( $end_formatted ) {
                        return ucfirst( sprintf( esc_html__( 'until %s', 'vuwu' ), $end_formatted ) );
                }

                return esc_html__( 'All Day', 'vuwu' );
        }

        /**
         * Formats the date window component of a schedule summary.
         *
         * @since 1.8.0
         *
         * @param string $start Start date (Y-m-d).
         * @param string $end   End date (Y-m-d).
         * @return string
         */
        private static function format_schedule_date_window( $start, $end ) {
                $start_timestamp = self::parse_date_value( $start );
                $end_timestamp   = self::parse_date_value( $end );

                if ( $start_timestamp && $end_timestamp ) {
                        $start_formatted = self::format_date_value( $start_timestamp, $end_timestamp );
                        $end_formatted   = self::format_date_value( $end_timestamp, $start_timestamp );

                        return sprintf( esc_html__( 'between %1$s and %2$s', 'vuwu' ), $start_formatted, $end_formatted );
                }

                if ( $start_timestamp ) {
                        return ucfirst( sprintf( esc_html__( 'starting %s', 'vuwu' ), self::format_date_value( $start_timestamp ) ) );
                }

                if ( $end_timestamp ) {
                        return ucfirst( sprintf( esc_html__( 'until %s', 'vuwu' ), self::format_date_value( $end_timestamp ) ) );
                }

                return '';
        }

        /**
         * Formats a list of strings into a human-friendly phrase.
         *
         * @since 1.8.0
         *
         * @param array $items Items to humanize.
         * @return string
         */
        private static function humanize_list( array $items ) {
                $items = array_values( array_filter( $items ) );
                $count = count( $items );

                if ( 0 === $count ) {
                        return '';
                }

                if ( 1 === $count ) {
                        return $items[0];
                }

                if ( 2 === $count ) {
                        return sprintf( esc_html__( '%1$s and %2$s', 'vuwu' ), $items[0], $items[1] );
                }

                $last  = array_pop( $items );
                $first = implode( esc_html__( ', ', 'vuwu' ), $items );

                return sprintf( esc_html__( '%1$s, and %2$s', 'vuwu' ), $first, $last );
        }

        /**
         * Formats a stored time for display within the schedule summary.
         *
         * @since 1.8.0
         *
         * @param string $value Time string in H:i format.
         * @return string
         */
        private static function format_time_value( $value ) {
                if ( empty( $value ) ) {
                        return '';
                }

                $timezone = function_exists( 'wp_timezone' ) ? wp_timezone() : null;
                $datetime = date_create_from_format( 'H:i', $value, $timezone );

                if ( ! $datetime ) {
                        return '';
                }

                return self::format_with_wp_date( get_option( 'time_format', 'g:i a' ), $datetime->getTimestamp() );
        }

        /**
         * Parses a stored date string into a timestamp.
         *
         * @since 1.8.0
         *
         * @param string $value Date string in Y-m-d format.
         * @return int|false
         */
        private static function parse_date_value( $value ) {
                if ( empty( $value ) ) {
                        return false;
                }

                $timezone = function_exists( 'wp_timezone' ) ? wp_timezone() : null;
                $datetime = date_create_from_format( '!Y-m-d', $value, $timezone );

                if ( ! $datetime ) {
                        return false;
                }

                return $datetime->getTimestamp();
        }

        /**
         * Formats a timestamp into a friendly date for the schedule summary.
         *
         * @since 1.8.0
         *
         * @param int      $timestamp           Timestamp to format.
         * @param int|bool $comparison_timestamp Optional comparison timestamp to determine whether a year should be displayed.
         * @return string
         */
        private static function format_date_value( $timestamp, $comparison_timestamp = false ) {
                if ( ! $timestamp ) {
                        return '';
                }

                $format       = 'jS F';
                $current_year = intval( self::format_with_wp_date( 'Y', time() ) );
                $year         = intval( self::format_with_wp_date( 'Y', $timestamp ) );
                $compare_year = $comparison_timestamp ? intval( self::format_with_wp_date( 'Y', $comparison_timestamp ) ) : $year;

                if ( $year !== $current_year || ( $comparison_timestamp && $year !== $compare_year ) ) {
                        $format .= ' Y';
                }

                return self::format_with_wp_date( $format, $timestamp );
        }

        /**
         * Formats a timestamp using wp_date when available, falling back to date_i18n.
         *
         * @since 1.8.0
         *
         * @param string $format    Date format string.
         * @param int    $timestamp Timestamp to format.
         * @return string
         */
        private static function format_with_wp_date( $format, $timestamp ) {
                if ( function_exists( 'wp_date' ) ) {
                        return wp_date( $format, $timestamp );
                }

                return date_i18n( $format, $timestamp );
        }


	/**
	 * Localizes the JavaScript for the display admin area.
	 *
	 * @since	1.0.0
	 * @since	1.3.1	Changed handle of script to {plugin_name}-admin.
	 * @since	1.3.2	Changed method to static.
	 */
	static function localize_scripts() {

                $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

                if ( empty( $screen ) || VUWU_Display::post_type_name !== $screen->post_type ) {
                        return;
                }

                $channel_scheduler_defaults = self::get_channel_scheduler_defaults();
                wp_localize_script( VUWU::get_plugin_name() . '-admin', 'vuwu_channel_scheduler_defaults', $channel_scheduler_defaults );

                $summary_strings = array(
                        'days'      => array(
                                1 => esc_html__( 'Mondays', 'vuwu' ),
                                2 => esc_html__( 'Tuesdays', 'vuwu' ),
                                3 => esc_html__( 'Wednesdays', 'vuwu' ),
                                4 => esc_html__( 'Thursdays', 'vuwu' ),
                                5 => esc_html__( 'Fridays', 'vuwu' ),
                                6 => esc_html__( 'Saturdays', 'vuwu' ),
                                0 => esc_html__( 'Sundays', 'vuwu' ),
                        ),
                        'every_day' => esc_html__( 'Every day', 'vuwu' ),
                        'list'      => array(
                                'two'    => esc_html__( '%1$s and %2$s', 'vuwu' ),
                                'serial' => esc_html__( '%1$s, and %2$s', 'vuwu' ),
                                'join'   => esc_html__( ', ', 'vuwu' ),
                        ),
                        'time'      => array(
                                'all_day' => esc_html__( 'All Day', 'vuwu' ),
                                'from_to' => esc_html__( 'from %1$s to %2$s', 'vuwu' ),
                                'from'    => esc_html__( 'from %s onward', 'vuwu' ),
                                'until'   => esc_html__( 'until %s', 'vuwu' ),
                        ),
                        'dates'     => array(
                                'between' => esc_html__( 'between %1$s and %2$s', 'vuwu' ),
                                'starting' => esc_html__( 'starting %s', 'vuwu' ),
                                'until'   => esc_html__( 'until %s', 'vuwu' ),
                        ),
                );

                wp_localize_script( VUWU::get_plugin_name() . '-display-schedule', 'vuwu_channel_schedule_summary', $summary_strings );

        }

	/**
	 * Saves all custom fields for a display.
	 *
	 * Triggered when a display is submitted from the display admin form.
	 *
	 * @since 	1.0.0
	 * @since	1.0.1	Improved validating & sanitizing of the user input.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param 	int		$post_id	The channel id.
	 * @return void
	 */
	static function save_display( $post_id ) {

		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		/* Check if our nonce is set */
                if ( ! isset( $_POST[VUWU_Display::post_type_name.'_nonce'] ) ) {
                        return $post_id;
                }

                $nonce = sanitize_text_field( wp_unslash( $_POST[VUWU_Display::post_type_name.'_nonce'] ) );

		/* Verify that the nonce is valid */
		if ( ! wp_verify_nonce( $nonce, VUWU_Display::post_type_name ) ) {
			return $post_id;
		}

		/* If this is an autosave, our form has not been submitted, so we don't want to do anything */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		/* Check the user's permissions */
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		/* Input validation */
		/* See: https://codex.wordpress.org/Data_Validation#Input_Validation */
                if ( ! isset( $_POST['vuwu_channel_editor_' . VUWU_Display::post_type_name] ) ) {
                        return $post_id;
                }

                $channel = isset( $_POST['vuwu_channel_editor_default_channel'] ) ? intval( wp_unslash( $_POST['vuwu_channel_editor_default_channel'] ) ) : 0;
                $display_id = intval( wp_unslash( $_POST['vuwu_channel_editor_' . VUWU_Display::post_type_name] ) );

		if ( empty( $display_id ) ) {
			return $post_id;
		}

		if ( ! empty( $channel ) ) {
			update_post_meta( $display_id, VUWU_Channel::post_type_name, $channel );
		}
		else {
			delete_post_meta( $display_id, VUWU_Channel::post_type_name );
		}

		/**
		 * Save schedule for temporary channels.
		 */
		self::save_schedule( $post_id );

	}

	/**
	 * Save all scheduled channels for this display.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Improved validating & sanitizing of the user input.
	 * @since	1.0.1	Removed the $values param that contained $_POST, to always be aware
	 * 					we're working with $_POST data.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @access	private
	 * @param 	array	$values			All form values that were submitted from the display admin page.
	 * @param 	int		$display_id		The ID of the display that is being saved.
	 * @return 	void
	 */
	private static function save_schedule( $display_id ) {

		delete_post_meta( $display_id, 'vuwu_display_schedule' );

		if ( ! isset( $_POST['vuwu_channel_schedule'] ) || ! is_array( $_POST['vuwu_channel_schedule'] ) ) {
			return;
		}

		$submitted_schedule = wp_unslash( $_POST['vuwu_channel_schedule'] );
		$sanitized_schedule = array();

		foreach ( $submitted_schedule as $rule ) {
			if ( ! is_array( $rule ) ) {
				continue;
			}

			$channel_id = isset( $rule['channel'] ) ? intval( $rule['channel'] ) : 0;

			if ( empty( $channel_id ) ) {
				continue;
			}

			$priority	 = isset( $rule['priority'] ) ? intval( $rule['priority'] ) : 0;
			$date_start = self::sanitize_date( isset( $rule['date_start'] ) ? $rule['date_start'] : '' );
			$date_end	 = self::sanitize_date( isset( $rule['date_end'] ) ? $rule['date_end'] : '' );
			$time_start = self::sanitize_time( isset( $rule['time_start'] ) ? $rule['time_start'] : '' );
			$time_end	 = self::sanitize_time( isset( $rule['time_end'] ) ? $rule['time_end'] : '' );

			$days = array();
			if ( isset( $rule['days'] ) && is_array( $rule['days'] ) ) {
				foreach ( $rule['days'] as $day ) {
					$day = intval( $day );
					if ( $day >= 0 && $day <= 6 ) {
						$days[] = $day;
					}
				}
			}

			$days = array_values( array_unique( $days ) );

			$sanitized_schedule[] = array(
				'channel'	 => $channel_id,
				'priority'	=> $priority,
				'date_start' => $date_start,
				'date_end'	 => $date_end,
				'time_start' => $time_start,
				'time_end'	 => $time_end,
				'days'	 => $days,
			);
		}

		if ( empty( $sanitized_schedule ) ) {
			return;
		}

		update_post_meta( $display_id, 'vuwu_display_schedule', $sanitized_schedule );
	}

	private static function sanitize_date( $value ) {
		$value = sanitize_text_field( $value );

		if ( empty( $value ) ) {
			return '';
		}

		$date = DateTimeImmutable::createFromFormat( 'Y-m-d', $value );

		if ( ! $date ) {
			return '';
		}

		return $date->format( 'Y-m-d' );
	}

	private static function sanitize_time( $value ) {
		$value = sanitize_text_field( $value );

		if ( empty( $value ) ) {
			return '';
		}

		$time = DateTimeImmutable::createFromFormat( 'H:i', $value );

		if ( ! $time ) {
			return '';
		}

		return $time->format( 'H:i' );
	}
}
