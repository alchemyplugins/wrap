<?php

class PluginTest extends AP_UnitTestCase
{
	function test_props()
	{
		$data = get_plugin_data( dirname( __FILE__ ) . '/../build/wrap.php' );

		$this->assertEquals($data['Name'], 'Wrap by AlchemyPlugins');
	}

	public function test_that_plugin_name_is_correct()
	{
		$this->assertEquals('ap-wrap', '');
	}

	/**
	 * @expectedException WPDieException
	 */
	public function test_that_wpdie_throws_an_exception()
	{
		wp_die();
	}
}
