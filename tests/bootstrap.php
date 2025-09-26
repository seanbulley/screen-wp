<?php

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	require dirname( dirname( __FILE__ ) ) . '/recspectra.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';

require_once( dirname( __FILE__ ) . '/includes/class-recspectra-unittestcase.php' );
require_once( dirname( __FILE__ ) . '/includes/class-recspectra-theater-unittestcase.php' );
require_once( dirname( __FILE__ ) . '/includes/class-recspectra-ajax-unittestcase.php' );
