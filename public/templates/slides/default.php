<?php
/**
 * Default slide format template.
 *
 * @since	1.0.0
 * @since	1.4.0	Rewrote the template to work with slide background.
 */

$slide = new ScreenWP_Slide( get_the_id() );

?><div<?php $slide->classes(); ?><?php $slide->data_attr();?>>
	<div class="inner">
	</div>
	<?php $slide->background(); ?>
</div>