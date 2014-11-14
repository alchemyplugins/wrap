<?php

class WP_Widget {}

require_once dirname( __FILE__ ) . '/../build/assets/inc/WrapWidget.php';

class WidgetTest extends TestCase {

	function test_construct() {
		$mock = \Mockery::mock('AlchemyPlugins\WrapWidget')->makePartial();
		$mock
			->shouldReceive('init')
			->once()
			->with(
				\Mockery::type('string'),
				\Mockery::type('string'),
				\Mockery::hasKey('description')
			);
		$mock->__construct();
	}
}
