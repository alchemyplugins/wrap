<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

$dir = dirname( __FILE__ );
//define( 'AP_TESTING_DIR', $dir . '/../../../wptest' );
//define( 'AP_PLUGIN_FILE', $dir . '/../build/wrap.php' );
//require_once AP_TESTING_DIR . '/includes/functions.php';
/*tests_add_filter( 'muplugins_loaded', function() {
	require AP_PLUGIN_FILE;
} );*/
//require_once AP_TESTING_DIR . '/includes/bootstrap.php';
require_once $dir . '/../vendor/autoload.php';
//require_once 'AP_UnitTestCase.php';
//require_once 'AP_BrowserUnitTestCase.php';

require_once $dir . '/../build/assets/inc/Wrap.php';
