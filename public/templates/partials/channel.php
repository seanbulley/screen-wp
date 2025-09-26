<?php
/**
 * Partial that outputs a channel.
 *
 * Used in the single-channel and single-display templates.
 *
 * @since	1.0.0
 * @since	1.0.1			Switched to the new and escaped classes() method.
 */

global $post;

$channel = new Recspectra_Channel( get_the_id() );
?><div<?php $channel->classes(); ?>>
	<div class="recspectra-slides"><?php

		foreach( $channel->get_slides() as $slide ) {

			$post = get_post( $slide->ID );
			setup_postdata( $post );

			Recspectra_Templates::get_template('partials/slide.php');

			wp_reset_postdata();
		}

	?></div>
</div>