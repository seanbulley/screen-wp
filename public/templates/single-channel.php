<?php
/**
 * Channel template.
 *
 * @since	1.0.0
 */

?><html>
	<head><?php
		wp_head( );
	?></head>
	<body <?php body_class();?>><?php
		ScreenWP_Templates::get_template('partials/channel.php');
		wp_footer();
	?></body>
</html>


