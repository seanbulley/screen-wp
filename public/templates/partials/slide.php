<?php
/**
 * Partial that outputs a slide.
 *
 * Used in the single-slide template and the channel partial.
 *
 * @since	1.0.0
 */

$slide = new VUWU_Slide( get_the_id() );
VUWU_Templates::get_template('slides/'.$slide->get_format().'.php');

