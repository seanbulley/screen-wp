<?php

class Test_Recspectra_Admin extends Recspectra_UnitTestCase {

	function test_are_scripts_and_styles_enqueued_on_recspectra_admin_screen() {

		$this->assume_role( 'administrator' );

		set_current_screen( 'edit.php?post_type=recspectra_display' );

		$actual = get_echo( 'wp_head' );

//		@todo: make this work
//		$this->assertContains( 'recspectra-admin-min.js', $actual );
//		$this->assertContains( 'recspectra-admin.css', $actual );
	}
}
