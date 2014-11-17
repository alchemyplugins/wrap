<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once __DIR__ . '/../vendor/autoload.php';
/*
$kernel = \AspectMock\Kernel::getInstance();
$kernel->init([
	'debug' => true,
	'includePaths' => [ __DIR__ . '/../build' ]
]);
*/
require_once __DIR__ . '/../../../wordpress/develop/tests/phpunit/includes/bootstrap.php';

require_once 'TestCase.php';
require_once __DIR__ . '/../build/assets/inc/Wrap.php';
