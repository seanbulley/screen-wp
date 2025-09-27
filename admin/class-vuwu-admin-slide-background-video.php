<?php

/**
 * Adds admin functionality for the YouTube Video slide background.
 *
 * Functionality was copied from VUWU_Admin_Slide_Format_Video (removed in 1.4.0).
 *
 * @since		1.4.0
 *
 * @package		VUWU
 * @subpackage	VUWU/admin
 */
class VUWU_Admin_Slide_Background_Video {

	/**
	 * Saves additional data for the Video slide background.
	 *
	 * Functionality was copied from VUWU_Admin_Slide_Format_Video (removed).
	 *
	 * @since	1.4.0
	 * @since	1.5.1	Added saving of the slide_bg_video_enable_sound option.
	 *
	 * @param	int		$post_id	The ID of the post being saved.
	 * @return	void
	 */
	static function save_slide_background( $post_id ) {
                $slide_bg_video_video_url = '';
                if ( isset( $_POST['slide_bg_video_video_url'] ) ) {
                        $slide_bg_video_video_url = sanitize_text_field( wp_unslash( $_POST['slide_bg_video_video_url'] ) );
                }

                $slide_bg_video_video_start = '';
                if ( isset( $_POST['slide_bg_video_video_start'] ) ) {
                        $slide_bg_video_video_start = intval( wp_unslash( $_POST['slide_bg_video_video_start'] ) );
                        if ( empty( $slide_bg_video_video_start ) ) {
                                $slide_bg_video_video_start = '';
                        }
                }

                $slide_bg_video_video_end = '';
                if ( isset( $_POST['slide_bg_video_video_end'] ) ) {
                        $slide_bg_video_video_end = intval( wp_unslash( $_POST['slide_bg_video_video_end'] ) );
                        if ( empty( $slide_bg_video_video_end ) ) {
                                $slide_bg_video_video_end = '';
                        }
                }

                $slide_bg_video_hold_slide = '';
                if ( isset( $_POST['slide_bg_video_hold_slide'] ) ) {
                        $slide_bg_video_hold_slide = intval( wp_unslash( $_POST['slide_bg_video_hold_slide'] ) );
                        if ( empty( $slide_bg_video_hold_slide ) ) {
                                $slide_bg_video_hold_slide = '';
                        }
                }

                $slide_bg_video_enable_sound = '';
                if ( isset( $_POST['slide_bg_video_enable_sound'] ) ) {
                        $slide_bg_video_enable_sound = intval( wp_unslash( $_POST['slide_bg_video_enable_sound'] ) );
                        if ( empty( $slide_bg_video_enable_sound ) ) {
                                $slide_bg_video_enable_sound = '';
                        }
                }

		update_post_meta( $post_id, 'slide_bg_video_video_url', $slide_bg_video_video_url );
		update_post_meta( $post_id, 'slide_bg_video_video_start', $slide_bg_video_video_start );
		update_post_meta( $post_id, 'slide_bg_video_video_end', $slide_bg_video_video_end );
		update_post_meta( $post_id, 'slide_bg_video_hold_slide', $slide_bg_video_hold_slide );
		update_post_meta( $post_id, 'slide_bg_video_enable_sound', $slide_bg_video_enable_sound );
	}

	/**
	 * Outputs the meta box for the Video slide background.
	 *
	 * @since	1.4.0
	 * @since	1.5.1	Added a slide_bg_video_enable_sound option.
	 * @since	1.6.0	Displayed the YouTube video URL input with class large-text.
	 *
	 * @param	WP_Post	$post	The post of the current slide.
	 * @return	void
	 */
	static function slide_background_meta_box( $post ) {

		$slide_bg_video_video_url = get_post_meta( $post->ID, 'slide_bg_video_video_url', true );
		$slide_bg_video_video_start = get_post_meta( $post->ID, 'slide_bg_video_video_start', true );
		$slide_bg_video_video_end = get_post_meta( $post->ID, 'slide_bg_video_video_end', true );
		$slide_bg_video_hold_slide = get_post_meta( $post->ID, 'slide_bg_video_hold_slide', true );
		$slide_bg_video_enable_sound = get_post_meta( $post->ID, 'slide_bg_video_enable_sound', true );

		?><table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="slide_bg_video_video_url"><?php _e('YouTube video URL', 'vuwu'); ?></label>
					</th>
					<td>
						<input type="hidden" name="slide_bg_video_video_id" id="slide_bg_video_video_id" value="" />
						<input type="text" name="slide_bg_video_video_url" id="slide_bg_video_video_url" class="large-text"
							value="<?php echo $slide_bg_video_video_url; ?>" />
						<p class="wp-ui-text-notification hidden" id="slide_bg_video_video_url_notification">
							<?php printf( esc_html__( 'Not a valid YouTube video URL, eg. %s', 'vuwu' ), 'https://youtu.be/MlQunle406U' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="slide_bg_video_video_start"><?php _e('Start at', 'vuwu'); ?></label>
					</th>
					<td>
						<input type="text" name="slide_bg_video_video_start" id="slide_bg_video_video_start" class="small-text"
							value="<?php echo $slide_bg_video_video_start; ?>" />
						<span><?php _e( 'seconds', 'vuwu' ); ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="slide_bg_video_video_end"><?php _e('End at', 'vuwu'); ?></label>
					</th>
					<td>
						<input type="text" name="slide_bg_video_video_end" id="slide_bg_video_video_end" class="small-text"
							value="<?php echo $slide_bg_video_video_end; ?>" />
						<span><?php _e( 'seconds', 'vuwu' ); ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="slide_bg_video_hold_slide"><?php _e('Hold slide until end?', 'vuwu'); ?></label>
					</th>
					<td>
						<input type="checkbox" name="slide_bg_video_hold_slide" id="slide_bg_video_hold_slide"
							value="1" <?php checked( $slide_bg_video_hold_slide, 1 ); ?> />
						<span><?php _e('Yes, hold the slide until the end of the video.', 'vuwu'); ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="slide_bg_video_enable_sound"><?php _e('Enable sound?', 'vuwu'); ?></label>
					</th>
					<td>
						<input type="checkbox" name="slide_bg_video_enable_sound" id="slide_bg_video_enable_sound"
							value="1" <?php checked( $slide_bg_video_enable_sound, 1 ); ?> />
						<span><?php _e('Yes, enable sound for this video.', 'vuwu'); ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="slide_bg_video_video_preview"><?php _e('Preview', 'vuwu'); ?></label>
					</th>
					<td>
						<div class="youtube-video-container" id="vuwu-admin-video-preview"></div>
					</td>
				</tr>
			</tbody>
		</table><?php
	}
}
