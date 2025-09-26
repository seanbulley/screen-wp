<?php
/**
 * Production slide format template.
 *
 * @since	1.0.0
 * @since	1.0.1	Sanitized the output.
 * @since	1.4.0	Rewrote the template to work with slide background.
 * @since	1.7.3	Fixed an issue where developers could not use HTML in the production title.
 */

$slide = new Recspectra_Slide( get_the_id() );

$production_id = get_post_meta( $slide->ID, 'slide_production_production_id', true );
$production = new WPT_Production( $production_id );

?><div<?php $slide->classes(); ?><?php $slide->data_attr();?>>
	<div class="inner">
		<?php if ( ! empty( $production_id ) ) { ?>
			<div class="recspectra-slide-fields">
				<div class="recspectra-slide-field recspectra-slide-field-title"><?php echo $production->title(); ?></div>
				<div class="recspectra-slide-field recspectra-slide-field-date"><?php echo $production->dates_html(); ?></div>
			</div>
		<?php } ?>
	</div>
	<?php $slide->background(); ?>
</div>