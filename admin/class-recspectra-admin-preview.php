<?php

/**
 * The preview functionality for Displays, Channels and Slides.
 *
 * @since		1.0.0
 * @since		1.3.2	Refactored class from object to static methods.
 *
 * @package		Recspectra
 * @subpackage	Recspectra/admin
 */
class Recspectra_Admin_Preview {

	/**
	 * Enqueues the admin javascript when previewing a slide.
	 *
	 * @since	1.0.0
	 * @since	1.2.5	Register scripts before they are enqueued.
	 *					Makes it possible to enqueue recspectra scripts outside of the recspectra plugin.
	 * @since	1.3.2	Changed method to static.
	 *
	 * return	void
	 */
	static function enqueue_scripts() {

                wp_register_script( Recspectra::get_plugin_name() . '-admin', plugin_dir_url( __FILE__ ) . 'js/recspectra-admin-min.js', array( 'jquery', 'jquery-ui-sortable' ), Recspectra::get_version(), true );

                wp_localize_script( Recspectra::get_plugin_name() . '-admin', 'recspectra_preview', array(
                        'ajax_url'    => admin_url( 'admin-ajax.php' ),
                        'object_id'   => get_the_ID(),
                        'orientations' => self::get_orientations(),
                        'nonce'       => wp_create_nonce( 'recspectra_preview_orientation' ),
                ) );

                if ( ! is_user_logged_in() ) {
                        return;
                }

                $is_preview = isset( $_GET['recspectra-preview'] ) ? sanitize_text_field( wp_unslash( $_GET['recspectra-preview'] ) ) : '';

                if ( ! empty( $is_preview ) ) {
                        return;
                }

                if ( ! is_singular( array( Recspectra_Display::post_type_name, Recspectra_Channel::post_type_name, Recspectra_Slide::post_type_name) ) ) {
                        return;
		}

		wp_enqueue_script( Recspectra::get_plugin_name() . '-admin' );
	}

	/**
	 * Get the current user's orientation choice for a Display, Channel or Slide.
	 *
	 * @since	1.0.0
	 * @param 	int	$object_id
	 * @return	string
	 */
	static function get_orientation_choice( $object_id ) {

		$default_orientation_choice = '16-9';

		if ( !is_user_logged_in( ) ) {
			return $default_orientation_choice;
		}

		$orientation_choices = get_user_meta( get_current_user_id( ), 'recspectra_preview_orientation_choices', true );

		if ( empty( $orientation_choices[ $object_id ] ) ) {
			return $default_orientation_choice;
		}

		return $orientation_choices[ $object_id ];
	}

	/**
	 * Gets all available preview orientations.
	 *
	 * @since	1.0.0
	 * @return	array
	 */
	static function get_orientations() {

		$orientations = array(
			'16-9' => __( 'Landscape', 'recspectra' ),
			'9-16' => __( 'Portrait', 'recspectra' ),
		);

		return $orientations;
	}

	/**
	 * Hides the admin bar when a Display, Channel of Slides is shown inside a preview iframe.
	 *
	 * @since	1.0.0
	 * @return	bool
	 */
	static function hide_admin_bar( $show_admin_bar ) {

		// Leave alone if admin bar is already hidden.
		if ( !$show_admin_bar ) {
			return $show_admin_bar;
		}

		// Don't hide if not inside preview iframe.
                $is_preview = isset( $_GET['recspectra-preview'] ) ? sanitize_text_field( wp_unslash( $_GET['recspectra-preview'] ) ) : '';

                if ( empty( $is_preview ) ) {
                        return true;
                }

		// Don't hide if not viewing a Display, Channel of Slide.
		if (!is_singular( array( Recspectra_Display::post_type_name, Recspectra_Channel::post_type_name, Recspectra_Slide::post_type_name) ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Save a user's orientation choice for a Display, Channel of Slide.
	 *
	 * Hooked to orientation button via AJAX.
	 *
	 * @since	1.0.0
	 * @since	1.0.1	Improved validating & sanitizing of the user input.
	 * @since	1.3.2	Changed method to static.
	 *
	 * @return	void
	 */
	static function save_orientation_choice( ) {

                if ( ! is_user_logged_in() ) {
                        wp_send_json_error( array(), 403 );
                }

                check_ajax_referer( 'recspectra_preview_orientation', 'nonce' );

                $orientation = isset( $_POST['orientation'] ) ? sanitize_title( wp_unslash( $_POST['orientation'] ) ) : '';
                if ( empty( $orientation ) ) {
                        wp_send_json_error();
                }

                $object_id = isset( $_POST['object_id'] ) ? intval( wp_unslash( $_POST['object_id'] ) ) : 0;
                if ( empty( $object_id ) ) {
                        wp_send_json_error();
                }

                $orientation_choices = get_user_meta( get_current_user_id(), 'recspectra_preview_orientation_choices', true );

                if ( empty( $orientation_choices ) ) {
                        $orientation_choices = array();
                }

                $orientation_choices[ $object_id ] = $orientation;

                update_user_meta( get_current_user_id(), 'recspectra_preview_orientation_choices', $orientation_choices );

                wp_send_json_success();
        }
}
