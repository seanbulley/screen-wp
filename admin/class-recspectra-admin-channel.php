<?php

/**
 * The channel admin-specific functionality of the plugin.
 *
 * @since		1.0.0
 * @since		1.3.2	Refactored class from object to static methods.
 *
 * @package		Recspectra
 * @subpackage	Recspectra/admin
 */
class Recspectra_Admin_Channel {

	/**
	 * Adds a Slide Count column to the Channels admin table, just after the title column.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param 	array	$columns	The current columns.
	 * @return	array				The new columns.
	 */
	static function add_slides_count_column( $columns ) {
		$new_columns = array();

		foreach( $columns as $key => $title ) {
			$new_columns[$key] = $title;

			if ( 'title' == $key ) {
				// Add slides count column after the title column
				$new_columns['slides_count'] = __( 'Number of slides', 'recspectra' );
			}
		}
		return $new_columns;
	}

	/**
	 * Adds a slide over AJAX and outputs the updated slides list HTML.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Validated & sanitized the user input.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @return void
	 */
	static function add_slide_over_ajax() {

		check_ajax_referer( 'recspectra_slides_editor_ajax_nonce', 'nonce' , true );

                $channel_id = isset( $_POST['channel_id'] ) ? intval( wp_unslash( $_POST['channel_id'] ) ) : 0;
                $add_slide_id = isset( $_POST['slide_id'] ) ? intval( wp_unslash( $_POST['slide_id'] ) ) : 0;

                if ( empty( $channel_id ) || empty( $add_slide_id ) ) {
                        wp_die();
                }

                if ( ! current_user_can( 'edit_post', $channel_id ) ) {
                        wp_die();
                }

		/* Check if the channel post exists */
		if ( is_null( get_post( $channel_id  ) ) ) {
			wp_die();
		}

		$channel = new Recspectra_Channel( $channel_id );
		$slides = $channel->get_slides();

		$new_slides = array();
		foreach( $slides as $slide ) {
			$new_slides[] = $slide->ID;
		}

		$new_slides[] = $add_slide_id;

		update_post_meta( $channel_id, Recspectra_Slide::post_type_name, $new_slides );

		echo self::get_slides_list_html( get_post( $channel_id ) );
		wp_die();
	}

        /**
         * Adds the content editor meta box to the channel admin page.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 */
	static function add_slides_editor_meta_box() {
		add_meta_box(
			'recspectra_slides_editor',
                        _x( 'Content', 'slide cpt', 'recspectra' ),
			array( __CLASS__, 'slides_editor_meta_box' ),
			Recspectra_Channel::post_type_name,
			'normal',
			'high'
		);
	}

	/**
	 * Adds the settings meta box to the channel admin page.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 */
	static function add_slides_settings_meta_box() {
		add_meta_box(
			'recspectra_slides_settings',
			__( 'Slideshow settings' , 'recspectra' ),
			array( __CLASS__, 'slides_settings_meta_box' ),
			Recspectra_Channel::post_type_name,
			'normal',
			'high'
		);
	}

        /**
         * Outputs the Content Count column.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param 	string	$column		The current column that needs output.
	 * @param 	int 	$post_id 	The current display ID.
	 * @return	void
	 */
	static function do_slides_count_column( $column, $post_id ) {
		if ( 'slides_count' == $column ) {

			$channel = new Recspectra_Channel( get_the_id() );

			echo count( $channel->get_slides() );
	    }
	}

	/**
	 * Gets the HTML to add a slide in the slides editor.
	 *
	 * @since	1.0.0
	 * @since	1.0.1			Escaped and sanitized the output.
	 * @since	1.1.0			Fix: List of slides was limited to 5 items.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @return	string	$html	The HTML to add a slide in the slides editor.
	 */
	static function get_add_slide_html() {

		ob_start();

		?>
			<div class="recspectra_slides_editor_add">
				<table class="form-table">
					<tbody>
						<th>
							<label for="recspectra_slides_editor_add">
								<?php echo esc_html__( 'Add slide', 'recspectra' ); ?>
							</label>
						</th>
						<td>
							<select id="recspectra_slides_editor_add" class="recspectra_slides_editor_add_select">
								<option value="">(<?php echo esc_html__( 'Select a slide', 'recspectra' ); ?>)</option>
								<?php
									$slides = Recspectra_Slides::get_posts();
									foreach ( $slides as $slide ) {
									?>
										<option value="<?php echo intval( $slide->ID ); ?>"><?php echo esc_html( get_the_title( $slide->ID ) ); ?></option>
									<?php
									}
								?>
							</select>
						</td>
					</tbody>
				</table>
			</div>
		<?php

		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Gets the HTML to set the slides duration in the slides settings meta box.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Escaped the output.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param	WP_Post	$post	The post object of the current display.
	 * @return	string	$html	The HTML to set the slides duration in the slides settings meta box.
	 */
	static function get_set_duration_html( $post ) {

		$duration_options = self::get_slides_duration_options();
		$default_duration = Recspectra_Slides::get_default_slides_duration();

		$default_option_name = '(' . __( 'Default', 'recspectra' );
		if ( ! empty( $duration_options[ $default_duration ] ) ) {
			$default_option_name .= ' [' . $duration_options[ $default_duration ] . ']';
		}
		$default_option_name .= ')';

		$channel = new Recspectra_Channel( $post );
		$selected_duration = $channel->get_saved_slides_duration();

		ob_start();

		?>
			<tr>
				<th>
					<label for="recspectra_slides_settings_duration">
						<?php echo esc_html__( 'Duration', 'recspectra' ); ?>
					</label>
				</th>
				<td>
					<select id="recspectra_slides_settings_duration" name="recspectra_slides_settings_duration">
						<option value=""><?php echo esc_html( $default_option_name ); ?></option>
						<?php
							foreach ( $duration_options as $key => $name ) {
								$selected = '';
								if ( $selected_duration == $key ) {
									$selected = 'selected="selected"';
								}
								?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php echo $selected; ?>>
										<?php echo esc_html( $name ); ?>
									</option>
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
	 * Gets the HTML to set the slides transition in the slides settings meta box.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Escaped the output.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param	WP_Post	$post	The post object of the current display.
	 * @return	string	$html	The HTML to set the slides transition in the slides settings meta box.
	 */
	static function get_set_transition_html( $post ) {

		$transition_options = self::get_slides_transition_options();
		$default_transition = Recspectra_Slides::get_default_slides_transition();

		$default_option_name = '(' . __( 'Default', 'recspectra' );
		if ( ! empty( $transition_options[ $default_transition ] ) ) {
			$default_option_name .= ' [' . $transition_options[ $default_transition ] . ']';
		}
		$default_option_name .= ')';

		$channel = new Recspectra_Channel( $post );
		$selected_transition = $channel->get_saved_slides_transition();

		ob_start();

		?>
			<tr>
				<th>
					<label for="recspectra_slides_settings_transition">
						<?php echo esc_html__( 'Transition', 'recspectra' ); ?>
					</label>
				</th>
				<td>
					<select id="recspectra_slides_settings_transition" name="recspectra_slides_settings_transition">
						<option value=""><?php echo esc_html( $default_option_name ); ?></option>
						<?php
							foreach ( $transition_options as $key => $name ) {
								$selected = '';
								if ( $selected_transition == $key ) {
									$selected = 'selected="selected"';
								}
								?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php echo $selected; ?>>
										<?php echo esc_html( $name ); ?>
									</option>
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
	 * Gets the slides duration options.
	 *
	 * @since	1.0.0
	 * @since	1.2.4	Added longer slide durations, up to 120 seconds.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @return	array	The slides duration options.
	 */
	static function get_slides_duration_options() {

		for ( $sec = 2; $sec <= 20; $sec++ ) {
			$secs[] = $sec;
		}
		for ( $sec = 25; $sec <= 60; $sec += 5 ) {
			$secs[] = $sec;
		}
		for ( $sec = 90; $sec <= 120; $sec += 30 ) {
			$secs[] = $sec;
		}

		$slides_duration_options = array();
		foreach ( $secs as $sec ) {
			$slides_duration_options[ $sec ] = $sec . ' ' . _n( 'second', 'seconds', $sec, 'recspectra' );
		}

		/**
		 * Filter available slides duration options.
		 *
		 * @since	1.0.0
		 * @param	array	$slides_duration_options	The currently available slides duration options.
		 */
		$slides_duration_options = apply_filters( 'recspectra/slides/duration/options', $slides_duration_options );

		return $slides_duration_options;
	}

	/**
	 * Gets the HTML that lists all slides in the slides editor.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Escaped and sanitized the output.
	 * @since	1.3.2	Changed method to static.
	 * @since	1.5.0	Added a recspectra-slide-is-stack class to stack slides.
	 *					Added an overlay for slides containing slide title, format and background, to be shown on hover.
	 * @since	1.5.1	Removed the translatable string 'x' to make translation easier.
	 * @since	1.7.4	Added a filter that allows diplaying of slide previews to be disabled.
	 *
	 * @param	WP_Post	$post
	 * @return	string	$html	The HTML that lists all slides in the slides editor.
	 */
	static function get_slides_list_html( $post ) {

		$channel = new Recspectra_Channel( $post );
		$slides = $channel->get_slides();

		/**
		 * Filters whether to display slide previews.
		 *
		 * @since	1.7.4
		 *
		 * @param	bool	$display_slide_previews		Indicates whether to display slide previews on the channel
		 *												admin screen or not.
		 */
		$display_slide_previews = apply_filters( 'recspectra/admin/channel/display_slide_previews', true );

		ob_start();

		?>
			<div class="recspectra_slides_editor_slides">
				<?php

					if ( empty( $slides ) ) {
						?><p>
							<?php echo esc_html__( 'No slides in this channel yet.', 'recspectra' ); ?>
						</p><?php
					}
					else {

						$i = 0;
						foreach( $slides as $slide ) {

							$slide_url = get_permalink( $slide->ID );
							$slide_url = add_query_arg( 'recspectra-preview', 1, $slide_url );
							$slide_format_data = Recspectra_Slides::get_slide_format_by_slug( $slide->get_format() );
							$slide_background_data = Recspectra_Slides::get_slide_background_by_slug( $slide->get_background() );

							?>
								<div class="recspectra_slides_editor_slides_slide<?php
									if ( $slide->is_stack() ) { echo ' recspectra-slide-is-stack'; }
								?>"
									data-slide-id="<?php echo intval( $slide->ID ); ?>"
									data-slide-key="<?php echo $i; ?>"
								>
									<div class="recspectra_slides_editor_slides_slide_iframe_container">
										<div class="recspectra_slides_editor_slides_slide_iframe_container_overlay">
											<h4><?php echo esc_html( get_the_title( $slide->ID ) ); ?></h4>
											<dl>
												<dt><?php _e( 'Format', 'recspectra'); ?></dt>
												<dd><?php echo esc_html( $slide_format_data['title'] ); ?></dd>
											</dl>
											<dl>
												<dt><?php _e( 'Background', 'recspectra'); ?></dt>
												<dd><?php echo esc_html( $slide_background_data['title'] ); ?></dd>
											</dl>
										</div>
										<?php if ( $display_slide_previews ) { ?>
											<iframe src="<?php echo esc_url( $slide_url ); ?>" width="1080" height="1920"></iframe>
										<?php } ?>
									</div>
									<div class="recspectra_slides_editor_slides_slide_caption">
										<?php echo esc_html_x( 'Slide', 'slide cpt', 'recspectra' ) . ' ' . ( $i + 1 ); ?>
										(<a href="#" class="recspectra_slides_editor_slides_slide_remove">x</a>)
									</div>
								</div>
							<?php

							$i++;
						}
					}
				?>
			</div>
		<?php

		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Gets the slides transition options.
	 *
	 * @since	1.0.0
	 * @since	1.2.4	Added a ‘No transition’ option.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @return	array	The slides transition options.
	 */
	static function get_slides_transition_options() {

		$slides_transition_options = array(
			'fade' => __( 'Fade', 'recspectra' ),
			'slide' => __( 'Slide', 'recspectra' ),
			'none' => __( 'No transition', 'recspectra' ),
		);

		/**
		 * Filter available slides transition options.
		 *
		 * @since	1.0.0
		 * @param	array	$slides_transition_options	The currently available slides transition options.
		 */
		$slides_transition_options = apply_filters( 'recspectra/slides/transition/options', $slides_transition_options );

		return $slides_transition_options;
	}

	/**
	 * Localizes the JavaScript for the channel admin area.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Escaped the output.
	 * @since	1.2.6	Changed handle of script to {plugin_name}-admin.
	 * @since	1.3.2	Changed method to static.
	 */
	static function localize_scripts() {

                $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

                if ( empty( $screen ) || Recspectra_Channel::post_type_name !== $screen->post_type ) {
                        return;
                }

                $defaults = array( 'confirm_remove_message' => esc_html__( 'Are you sure you want to remove this slide from the channel?', 'recspectra' ) );
                wp_localize_script( Recspectra::get_plugin_name() . '-admin', 'recspectra_slides_editor_defaults', $defaults );

                $security = array( 'nonce' => wp_create_nonce( 'recspectra_slides_editor_ajax_nonce' ) );
                wp_localize_script( Recspectra::get_plugin_name() . '-admin', 'recspectra_slides_editor_security', $security );
        }

	/**
	 * Removes the sample permalink from the Channel edit screen.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param 	string	$sample_permalink
	 * @return 	string
	 */
	static function remove_sample_permalink( $sample_permalink ) {

		$screen = get_current_screen();

		// Bail if not on Channel edit screen.
		if ( empty( $screen ) || Recspectra_Channel::post_type_name != $screen->post_type ) {
			return $sample_permalink;
		}

		return '';
	}

	/**
	 * Removes a slide over AJAX and outputs the updated slides list HTML.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Validated & sanitized the user input.
	 * @since	1.2.4	You can now remove the first slide (slide_key 0) of a channel. Fixes #1.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @return	void
	 */
	static function remove_slide_over_ajax() {

		check_ajax_referer( 'recspectra_slides_editor_ajax_nonce', 'nonce' , true );

                $channel_id = isset( $_POST['channel_id'] ) ? intval( wp_unslash( $_POST['channel_id'] ) ) : 0;
                $remove_slide_key = isset( $_POST['slide_key'] ) ? intval( wp_unslash( $_POST['slide_key'] ) ) : 0;

                if ( empty( $channel_id ) ) {
                        wp_die();
                }

                if ( ! current_user_can( 'edit_post', $channel_id ) ) {
                        wp_die();
                }

		/* Check if this post exists */
		if ( is_null( get_post( $channel_id  ) ) ) {
			wp_die();
		}

		$channel = new Recspectra_Channel( $channel_id );
		$slides = $channel->get_slides();

		/* Check if the channel has slides */
		if ( empty( $slides ) ) {
			wp_die();
		}

		$new_slides = array();
		foreach( $slides as $slide ) {
			$new_slides[] = $slide->ID;
		}

		if ( ! isset( $new_slides[$remove_slide_key] ) ) {
			wp_die();
		}

		unset( $new_slides[$remove_slide_key] );
		update_post_meta( $channel_id, Recspectra_Slide::post_type_name, $new_slides );

		echo self::get_slides_list_html( get_post( $channel_id ) );
		wp_die();
	}

	/**
	 * Reorders slides over AJAX and outputs the updated slides list HTML.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Validated & sanitized the user input.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @return void
	 */
	static function reorder_slides_over_ajax() {

		check_ajax_referer( 'recspectra_slides_editor_ajax_nonce', 'nonce' , true );

                $channel_id = isset( $_POST['channel_id'] ) ? intval( wp_unslash( $_POST['channel_id'] ) ) : 0;
                $posted_slide_ids = isset( $_POST['slide_ids'] ) ? (array) wp_unslash( $_POST['slide_ids'] ) : array();
                $slide_ids = array_map( 'intval', $posted_slide_ids );

                if ( empty( $channel_id ) || empty( $slide_ids ) ) {
                        wp_die();
                }

                if ( ! current_user_can( 'edit_post', $channel_id ) ) {
                        wp_die();
                }

		/* Check if this post exists */
		if ( is_null( get_post( $channel_id  ) ) ) {
			wp_die();
		}

		$new_slides = array();
		foreach( $slide_ids as $slide_id ) {
			$new_slides[] = $slide_id;
		}

		update_post_meta( $channel_id, Recspectra_Slide::post_type_name, $new_slides );

		echo self::get_slides_list_html( get_post( $channel_id ) );
		wp_die();
	}

	/**
	 * Saves all custom fields for a channel.
	 *
	 * Triggered when a channel is submitted from the channel admin form.
	 *
	 * @since 	1.0.0
	 * @since	1.0.1	Validated & sanitized the user input.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param 	int		$post_id	The channel id.
	 * @return void
	 */
	static function save_channel( $post_id ) {

		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		/* Check if our nonce is set */
                if ( ! isset( $_POST[Recspectra_Channel::post_type_name.'_nonce'] ) ) {
                        return $post_id;
                }

                $nonce = sanitize_text_field( wp_unslash( $_POST[Recspectra_Channel::post_type_name.'_nonce'] ) );

		/* Verify that the nonce is valid */
		if ( ! wp_verify_nonce( $nonce, Recspectra_Channel::post_type_name ) ) {
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

		/* Check if slides settings are included (empty or not) in form */
		if (
			! isset( $_POST['recspectra_slides_settings_duration'] ) ||
			! isset( $_POST['recspectra_slides_settings_transition'] )
		) {
			return $post_id;
		}

                $recspectra_slides_settings_duration = intval( wp_unslash( $_POST['recspectra_slides_settings_duration'] ) );
                if ( empty( $recspectra_slides_settings_duration ) ) {
                        $recspectra_slides_settings_duration = '';
                }

                $recspectra_slides_settings_transition = sanitize_title( wp_unslash( $_POST['recspectra_slides_settings_transition'] ) );
		if ( empty( $recspectra_slides_settings_transition ) ) {
			$recspectra_slides_settings_transition = '';
		}

		update_post_meta( $post_id, Recspectra_Channel::post_type_name . '_slides_duration' , $recspectra_slides_settings_duration );
		update_post_meta( $post_id, Recspectra_Channel::post_type_name . '_slides_transition' , $recspectra_slides_settings_transition );
	}

	/**
	 * Outputs the content of the slides editor meta box.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Sanitized the output.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param	WP_Post		$post	The post object of the current channel.
	 */
	static function slides_editor_meta_box( $post ) {

		wp_nonce_field( Recspectra_Channel::post_type_name, Recspectra_Channel::post_type_name.'_nonce' );

		ob_start();

		?>
			<div class="recspectra_meta_box recspectra_slides_editor" data-channel-id="<?php echo intval( $post->ID ); ?>">

				<?php
					echo self::get_slides_list_html( $post );
					echo self::get_add_slide_html();
				?>

			</div>
		<?php

		$html = ob_get_clean();

		echo $html;
	}

	/**
	 * Outputs the content of the slides settings meta box.
	 *
	 * @since	1.0.0
	 * @since	1.3.2	Changed method to static.
	 *
	 * @param	WP_Post		$post	The post object of the current channel.
	 */
	static function slides_settings_meta_box( $post ) {

		wp_nonce_field( Recspectra_Channel::post_type_name, Recspectra_Channel::post_type_name.'_nonce' );

		ob_start();

		?>
			<table class="recspectra_meta_box_form form-table recspectra_slides_settings_form">
				<tbody>
					<?php

						echo self::get_set_duration_html( $post );
						echo self::get_set_transition_html( $post );

					?>
				</tbody>
			</table>
		<?php

		$html = ob_get_clean();

		echo $html;
	}
}
