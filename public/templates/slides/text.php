<?php
/**
 * Text slide format template.
 *
 * @since	1.5.0
 */

$slide = new Recspectra_Slide( get_the_id() );

$slide_text_pretitle = get_post_meta( $slide->ID, 'slide_text_pretitle', true );
$slide_text_title = get_post_meta( $slide->ID, 'slide_text_title', true );
$slide_text_subtitle = get_post_meta( $slide->ID, 'slide_text_subtitle', true );
$slide_text_content = get_post_meta( $slide->ID, 'slide_text_content', true );

?><div<?php $slide->classes(); ?><?php $slide->data_attr(); ?>>
	<div class="inner">
		<div class="recspectra-slide-fields">
			<?php if ( ! empty( $slide_text_pretitle ) ) { ?>
				<div class="recspectra-slide-field recspectra-slide-field-pretitle"><span><?php echo $slide_text_pretitle; ?></span></div>
			<?php } ?>
			<?php if ( ! empty( $slide_text_title ) ) { ?>
				<div class="recspectra-slide-field recspectra-slide-field-title"><span><?php echo $slide_text_title; ?></span></div>
			<?php } ?>
			<?php if ( ! empty( $slide_text_subtitle ) ) { ?>
				<div class="recspectra-slide-field recspectra-slide-field-subtitle"><span><?php echo $slide_text_subtitle; ?></span></div>
			<?php } ?>
			<?php if ( ! empty( $slide_text_content ) ) { ?>
				<div class="recspectra-slide-field recspectra-slide-field-content"><?php echo wpautop( $slide_text_content ); ?></div>
			<?php } ?>
		</div>
	</div>
	<?php $slide->background(); ?>
</div>