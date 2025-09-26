<?php

/**
 * The display admin-specific functionality of the plugin.
 *
 * @since		1.0.0
 * @since		1.3.2	Refactored class from object to static methods.
 *
 * @package		Recspectra
 * @subpackage	Recspectra/admin
 */
class Recspectra_Admin_Display {

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
				'default_channel' => __('Default channel', 'recspectra'),
				'active_channel' => __('Active channel', 'recspectra'),
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
			'recspectra_channel_editor',
			_x( 'Channel', 'channel cpt', 'recspectra' ),
			array( __CLASS__, 'channel_editor_meta_box' ),
			Recspectra_Display::post_type_name,
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
                        'recspectra_channel_scheduler',
                        __( 'Channel schedule' , 'recspectra' ),
                        array( __CLASS__, 'channel_scheduler_meta_box' ),
                        Recspectra_Display::post_type_name,
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

		wp_nonce_field( Recspectra_Display::post_type_name, Recspectra_Display::post_type_name.'_nonce' );

		ob_start();

		?>
			<input type="hidden" id="recspectra_channel_editor_<?php echo Recspectra_Display::post_type_name; ?>"
				name="recspectra_channel_editor_<?php echo Recspectra_Display::post_type_name; ?>" value="<?php echo intval( $post->ID ); ?>">

			<table class="recspectra_meta_box_form form-table recspectra_channel_editor_form" data-display-id="<?php echo intval( $post->ID ); ?>">
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

		wp_nonce_field( Recspectra_Display::post_type_name, Recspectra_Display::post_type_name.'_nonce' );

		ob_start();

		?>
			<input type="hidden" id="recspectra_channel_editor_<?php echo Recspectra_Display::post_type_name; ?>"
				name="recspectra_channel_editor_<?php echo Recspectra_Display::post_type_name; ?>" value="<?php echo intval( $post->ID ); ?>">

			<table class="recspectra_meta_box_form form-table recspectra_channel_editor_form" data-display-id="<?php echo intval( $post->ID ); ?>">
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

				$display = new Recspectra_Display( $post_id );

				if ( ! $active_channel_id = $display->get_active_channel() ) {
					_e( 'None', 'recspectra' );
					break;
				}

				$channel = new Recspectra_Channel( $active_channel_id );

				?><a href="<?php echo esc_url( get_edit_post_link( $channel->ID ) ); ?>"><?php
					echo esc_html( get_the_title( $channel->ID ) );
				?></a><?php

		        break;

		    case 'default_channel' :

				$display = new Recspectra_Display( $post_id );

				if ( ! $default_channel_id = $display->get_default_channel() ) {
					_e( 'None', 'recspectra' );
					break;
				}

				$channel = new Recspectra_Channel( $default_channel_id );

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
		return apply_filters( 'recspectra/channel_scheduler/defaults', $defaults );
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

		$display = new Recspectra_Display( $post );
		$default_channel = $display->get_default_channel();

		ob_start();

		?>
			<tr>
				<th>
					<label for="recspectra_channel_editor_default_channel">
						<?php echo esc_html__( 'Default channel', 'recspectra' ); ?>
					</label>
				</th>
				<td>
					<select id="recspectra_channel_editor_default_channel" name="recspectra_channel_editor_default_channel">
						<option value="">(<?php echo esc_html__( 'Select a channel', 'recspectra' ); ?>)</option>
						<?php
							$channels = Recspectra_Channels::get_posts();
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

                $display  = new Recspectra_Display( $post );
                $schedule = $display->get_schedule();
                $channels = Recspectra_Channels::get_posts();

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
                                <th><?php echo esc_html__( 'Schedules', 'recspectra' ); ?></th>
                                <td>
                                        <table class="recspectra-channel-schedule-table widefat">
                                                <thead>
                                                        <tr>
                                                                <th><?php echo esc_html__( 'Channel', 'recspectra' ); ?></th>
                                                                <th><?php echo esc_html__( 'Priority', 'recspectra' ); ?></th>
                                                                <th><?php echo esc_html__( 'Date range', 'recspectra' ); ?></th>
                                                                <th><?php echo esc_html__( 'Time window', 'recspectra' ); ?></th>
                                                                <th><?php echo esc_html__( 'Days of week', 'recspectra' ); ?></th>
                                                                <th class="recspectra-channel-schedule-actions">&nbsp;</th>
                                                        </tr>
                                                </thead>
                                                <tbody class="recspectra-channel-schedule-rows">
                                                        <?php
                                                        foreach ( $schedule as $index => $rule ) {
                                                                echo self::render_schedule_row( $index, $rule, $channels );
                                                        }
                                                        ?>
                                                </tbody>
                                        </table>
                                        <p>
                                                <button type="button" class="button button-secondary recspectra-add-schedule-row"><?php echo esc_html__( 'Add schedule', 'recspectra' ); ?></button>
                                        </p>
                                        <script type="text/html" id="recspectra-channel-schedule-row-template">
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
                        1 => esc_html__( 'Monday', 'recspectra' ),
                        2 => esc_html__( 'Tuesday', 'recspectra' ),
                        3 => esc_html__( 'Wednesday', 'recspectra' ),
                        4 => esc_html__( 'Thursday', 'recspectra' ),
                        5 => esc_html__( 'Friday', 'recspectra' ),
                        6 => esc_html__( 'Saturday', 'recspectra' ),
                        0 => esc_html__( 'Sunday', 'recspectra' ),
                );

                ob_start();

                ?>
                <tr class="recspectra-channel-schedule-row" data-index="<?php echo esc_attr( $index_attr ); ?>">
                        <td>
                                <select name="recspectra_channel_schedule[<?php echo esc_attr( $index_attr ); ?>][channel]">
                                        <option value="">(<?php echo esc_html__( 'Select a channel', 'recspectra' ); ?>)</option>
                                        <?php foreach ( $channels as $channel ) : ?>
                                                <option value="<?php echo intval( $channel->ID ); ?>" <?php selected( $channel_id, $channel->ID ); ?>><?php echo esc_html( get_the_title( $channel->ID ) ); ?></option>
                                        <?php endforeach; ?>
                                </select>
                        </td>
                        <td>
                                <input type="number" class="small-text" name="recspectra_channel_schedule[<?php echo esc_attr( $index_attr ); ?>][priority]" value="<?php echo esc_attr( $priority ); ?>" />
                        </td>
                        <td>
                                <label>
                                        <span class="screen-reader-text"><?php echo esc_html__( 'Start date', 'recspectra' ); ?></span>
                                        <input type="date" name="recspectra_channel_schedule[<?php echo esc_attr( $index_attr ); ?>][date_start]" value="<?php echo esc_attr( $date_start ); ?>" />
                                </label>
                                <label>
                                        <span class="screen-reader-text"><?php echo esc_html__( 'End date', 'recspectra' ); ?></span>
                                        <input type="date" name="recspectra_channel_schedule[<?php echo esc_attr( $index_attr ); ?>][date_end]" value="<?php echo esc_attr( $date_end ); ?>" />
                                </label>
                        </td>
                        <td>
                                <label>
                                        <span class="screen-reader-text"><?php echo esc_html__( 'Start time', 'recspectra' ); ?></span>
                                        <input type="time" name="recspectra_channel_schedule[<?php echo esc_attr( $index_attr ); ?>][time_start]" value="<?php echo esc_attr( $time_start ); ?>" />
                                </label>
                                <label>
                                        <span class="screen-reader-text"><?php echo esc_html__( 'End time', 'recspectra' ); ?></span>
                                        <input type="time" name="recspectra_channel_schedule[<?php echo esc_attr( $index_attr ); ?>][time_end]" value="<?php echo esc_attr( $time_end ); ?>" />
                                </label>
                        </td>
                        <td class="recspectra-channel-schedule-days">
                                <?php foreach ( $days_of_week as $day_index => $day_label ) : ?>
                                        <label class="recspectra-channel-schedule-day">
                                                <input type="checkbox" value="<?php echo esc_attr( $day_index ); ?>" name="recspectra_channel_schedule[<?php echo esc_attr( $index_attr ); ?>][days][]" <?php checked( in_array( $day_index, $days, true ), true ); ?> />
                                                <span><?php echo esc_html( $day_label ); ?></span>
                                        </label>
                                <?php endforeach; ?>
                        </td>
                        <td class="recspectra-channel-schedule-actions">
                                <button type="button" class="button-link delete recspectra-remove-schedule-row">
                                        <?php echo esc_html__( 'Remove', 'recspectra' ); ?>
                                </button>
                        </td>
                </tr>
                <?php

                return ob_get_clean();
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

                if ( empty( $screen ) || Recspectra_Display::post_type_name !== $screen->post_type ) {
                        return;
                }

                $channel_scheduler_defaults = self::get_channel_scheduler_defaults();
                wp_localize_script( Recspectra::get_plugin_name() . '-admin', 'recspectra_channel_scheduler_defaults', $channel_scheduler_defaults );
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
                if ( ! isset( $_POST[Recspectra_Display::post_type_name.'_nonce'] ) ) {
                        return $post_id;
                }

                $nonce = sanitize_text_field( wp_unslash( $_POST[Recspectra_Display::post_type_name.'_nonce'] ) );

		/* Verify that the nonce is valid */
		if ( ! wp_verify_nonce( $nonce, Recspectra_Display::post_type_name ) ) {
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
                if ( ! isset( $_POST['recspectra_channel_editor_' . Recspectra_Display::post_type_name] ) ) {
                        return $post_id;
                }

                $channel = isset( $_POST['recspectra_channel_editor_default_channel'] ) ? intval( wp_unslash( $_POST['recspectra_channel_editor_default_channel'] ) ) : 0;
                $display_id = intval( wp_unslash( $_POST['recspectra_channel_editor_' . Recspectra_Display::post_type_name] ) );

		if ( empty( $display_id ) ) {
			return $post_id;
		}

		if ( ! empty( $channel ) ) {
			update_post_meta( $display_id, Recspectra_Channel::post_type_name, $channel );
		}
		else {
			delete_post_meta( $display_id, Recspectra_Channel::post_type_name );
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

		delete_post_meta( $display_id, 'recspectra_display_schedule' );

		if ( ! isset( $_POST['recspectra_channel_schedule'] ) || ! is_array( $_POST['recspectra_channel_schedule'] ) ) {
			return;
		}

		$submitted_schedule = wp_unslash( $_POST['recspectra_channel_schedule'] );
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

		update_post_meta( $display_id, 'recspectra_display_schedule', $sanitized_schedule );
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
