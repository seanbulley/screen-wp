<?php
/**
 * Preview template for Displays, Channels and Slides.
 *
 * @since	1.0.0
 */

?><html>
	<head><?php
		wp_head();
	?>
	</head>
	<body <?php body_class(); ?>><?php

		$preview_url = get_permalink( get_the_id() );
		$preview_url = add_query_arg( 'recspectra-preview', 1, $preview_url );

		$orientations = Recspectra_Admin_Preview::get_orientations();
		$orientation_choice = Recspectra_Admin_Preview::get_orientation_choice( get_the_id() );

		?><iframe src="<?php echo esc_url( $preview_url ); ?>" class="recspectra-preview recspectra-preview-<?php echo esc_attr( $orientation_choice ) ?>"></iframe>
		<div class="recspectra-preview-actions"><?php
			foreach ( $orientations as $orientation_key => $orientation_name ) {
				?><div class="recspectra-orientation-button<?php if ( $orientation_key == $orientation_choice ) { ?> active<?php } ?>" data-orientation="<?php echo esc_attr( $orientation_key ); ?>"><?php
					echo esc_html( $orientation_name );
				?></div><?php
			}
		?></div><?php
		wp_footer();
	?></body>
</html>
