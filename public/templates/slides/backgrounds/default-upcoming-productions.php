<?php
/**
 * Default slide background template for the Upcoming Productions slide format.
 *
 * @since	1.7.0
 */

$slide = new Recspectra_Slide( get_the_id() );

if ( ! empty( $production ) && $production_attachment_id = $production->thumbnail() ) {

	?><div<?php $slide->background_classes(); ?><?php $slide->background_data_attr();?>>
		<figure>
			<?php echo wp_get_attachment_image( $production_attachment_id, 'recspectra' ); ?>
		</figure>
	</div><?php

}
