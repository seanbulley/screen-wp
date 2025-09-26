<?php

class Test_ScreenWP_Admin extends ScreenWP_UnitTestCase {

	function test_are_scripts_and_styles_enqueued_on_screenwp_admin_screen() {

		$this->assume_role( 'administrator' );

		set_current_screen( 'edit.php?post_type=screenwp_display' );

		$actual = get_echo( 'wp_head' );

//		@todo: make this work
//		$this->assertContains( 'screenwp-admin-min.js', $actual );
//		$this->assertContains( 'screenwp-admin.css', $actual );
	}
}
