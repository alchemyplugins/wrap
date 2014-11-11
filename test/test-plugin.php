<?php

class PluginTest extends PHPUnit_Framework_TestCase
{
	public function setUp() {
        \WP_Mock::setUp();
    }

    public function tearDown() {
        \WP_Mock::tearDown();
    }

	public function test_init()
	{
		$p = new AP_Wrap;

		\WP_Mock::expectActionAdded( 'init', array( $p, 'register_cpt' ) );

		$p->init();
	}
}
