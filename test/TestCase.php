<?php

class TestCase extends PHPUnit_Framework_TestCase {

	function setUp() {
		\WP_Mock::setUp();
		\WP_Mock::wpPassthruFunction( '__', array(
			'args' => array(
				\WP_Mock\Functions::type( 'string' ),
				'ap-wrap',
			),
		) );
		\WP_Mock::wpPassthruFunction( '_x', array(
			'args' => array(
				\WP_Mock\Functions::type( 'string' ),
				\WP_Mock\Functions::type( 'string' ),
				'ap-wrap',
			),
		) );
	}

	function tearDown() {
		\WP_Mock::tearDown();
		\Mockery::close();
	}
}
