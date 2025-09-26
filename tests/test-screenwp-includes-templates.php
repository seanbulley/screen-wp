<?php

class Test_ScreenWP_Templates extends ScreenWP_UnitTestCase {

	/**
	 * @since	1.7.2
	 */
	function test_are_plugin_template_paths_registered() {

		$plugin_template_path1 = '/just/a/path';
		ScreenWP_Templates::register_plugin_template_path( $plugin_template_path1 );

		$plugin_template_path2 = '/just/another/path';
		ScreenWP_Templates::register_plugin_template_path( $plugin_template_path2 );

		$plugin_template_paths = ScreenWP_Templates::get_plugin_template_paths();

		$this->assertContains( $plugin_template_path1, $plugin_template_paths );
		$this->assertContains( $plugin_template_path2, $plugin_template_paths );
	}
}
