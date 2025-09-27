<?php

class Test_VUWU_Admin extends VUWU_UnitTestCase {

	function test_are_scripts_and_styles_enqueued_on_vuwu_admin_screen() {

		$this->assume_role( 'administrator' );

		set_current_screen( 'edit.php?post_type=vuwu_display' );

		$actual = get_echo( 'wp_head' );

//		@todo: make this work
//		$this->assertContains( 'vuwu-admin-min.js', $actual );
//		$this->assertContains( 'vuwu-admin.css', $actual );
	}
}
