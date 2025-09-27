<?php
/**
 * Post slide format template.
 *
 * @since	1.5.0
 * @since	1.5.1	Switched to using the new 'vuwu' image size.
 */

$slide = new VUWU_Slide( get_the_id() );

$slide_post_id = get_post_meta( $slide->ID, 'slide_post_post_id', true );
$slide_post = get_post( $slide_post_id );

$slide_post_display_thumbnail = get_post_meta( $slide->ID, 'slide_post_display_thumbnail', true );
$slide_post_use_excerpt = get_post_meta( $slide->ID, 'slide_post_use_excerpt', true );

if ( ! empty( $slide_post_use_excerpt ) ) {
	$content = apply_filters( 'the_content', $slide_post->post_excerpt );
}
else {
	$content = apply_filters( 'the_content', $slide_post->post_content );
}

?><div<?php $slide->classes(); ?><?php $slide->data_attr(); ?>>
	<div class="inner">
		<?php if ( ! empty( $slide_post->ID ) ) { ?>
			<?php if ( ! empty( $slide_post_display_thumbnail ) && $slide_post_attachment_id = get_post_thumbnail_id( $slide_post->ID ) ) { ?>
				<figure>
					<?php echo wp_get_attachment_image( $slide_post_attachment_id, 'vuwu' ); ?>
				</figure>
			<?php } ?>
			<div class="vuwu-slide-fields">
				<div class="vuwu-slide-field vuwu-slide-field-title"><span><?php echo get_the_title( $slide_post->ID ); ?></span></div>
				<div class="vuwu-slide-field vuwu-slide-field-date"><span><?php echo get_the_date( false, $slide_post->ID ); ?></span></div>
				<?php if ( ! empty( $content ) ) { ?>
					<div class="vuwu-slide-field vuwu-slide-field-content"><?php echo $content; ?></div>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
	<?php $slide->background(); ?>
</div>