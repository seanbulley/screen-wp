<?php

class Test_Recspectra_Templates extends Recspectra_UnitTestCase {

	/**
	 * @since	1.7.2
	 */
	function test_are_plugin_template_paths_registered() {

		$plugin_template_path1 = '/just/a/path';
		Recspectra_Templates::register_plugin_template_path( $plugin_template_path1 );

		$plugin_template_path2 = '/just/another/path';
		Recspectra_Templates::register_plugin_template_path( $plugin_template_path2 );

		$plugin_template_paths = Recspectra_Templates::get_plugin_template_paths();

		$this->assertContains( $plugin_template_path1, $plugin_template_paths );
		$this->assertContains( $plugin_template_path2, $plugin_template_paths );
	}
}
