<?php

require_once dirname( __FILE__ ) . '/../build/assets/inc/WrapWidget.php';

class WidgetTest extends WP_UnitTestCase {

	public $plugin;

	function setUp() {
		parent::setUp();
		$this->plugin = new AlchemyPlugins\Wrap;
	}

	function tearDown() {
		unset( $this->plugin );
		parent::tearDown();
	}

	function test_register_widget() {

		global $wp_widget_factory;

		$key = 'AlchemyPlugins\WrapWidget';

		$this->assertArrayNotHasKey( $key, $wp_widget_factory->widgets, 'widget is not registered' );

		$this->plugin->widget_class = $key;
		$this->plugin->register_widget();

		$this->assertArrayHasKey( $key, $wp_widget_factory->widgets, 'widget is registered' );
	}

	function test_frontend_output() {

		$instance = array(
			'post_id' => 100,
			'title' => 'foo',
			'vars' => array(),
		);

		$args = array(
			'before_widget' => 'BW',
			'after_widget' => 'AW',
			'before_title' => 'BT',
			'after_title' => 'AT'
		);

		$widget = new AlchemyPlugins\WrapWidget;

		ob_start();
		$widget->widget( $args, $instance );
		$output = ob_get_contents();
		ob_end_clean();

		$this->assertContains( 'BTfooAT', $output );
	}

	function test_admin_output() {

		$post_ids = $this->factory->post->create_many( 10, array( 'post_type' => $this->plugin->cpt) );

		$instance = array(
			'post_id' => $post_ids[0],
			'title' => 'foo',
			'vars' => array(),
		);

		$widget = new AlchemyPlugins\WrapWidget;

		ob_start();
		$widget->form( $instance );
		$output = ob_get_contents();
		ob_end_clean();

		$this->assertContains( 'widget-form', $output );

		print_r($output);
		echo "ok";
	}
}
