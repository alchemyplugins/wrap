<?php


function __($str, $ns = null){
	return $str;
}
function _x($str, $context, $ns = null){
	return $str;
}
function esc_attr($str) {
	return $str;
}
class WP_Widget {
	function __construct() {

	}
	function get_field_id( $str ) {
		return $str;
	}
	function get_field_name( $str ) {
		return $str;
	}
}
//class WP_Query {}

require_once dirname( __FILE__ ) . '/../build/assets/inc/WrapWidget.php';

//use AspectMock\Test as test;

class MockMock {
	public $class;
	public $last_func;
	function __construct($class) {
		$this->class = $class;
	}
}

class MockIt {

	public static $class;

	public $func;

	public static $instance;

	public static $stats = array();

	public static $manager = array();

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	public static function getManager($class) {
		return self::$manager[$class];
	}
	public static function addManager($class) {
		self::$manager[$class] = new MockMock($class);
		return self::$manager[$class];
	}
	function getMethodArgs($func) {
		return self::$stats[self::$class][$func]["args"];
	}
	function hasMethod($func) {
		$this->func = $func;
		self::$stats[self::$class][$func] = array("args"=>array(), "return" => null);
		return $this;
	}
	function willReturn($return) {
		self::$stats[self::$class][$this->func]["return"] = $return;
		return $this;
	}
	public static function double( $class ) {

		$mm = self::addManager($class);

		self::$class = $class;

		// class A extends B
		$content = 'class ' . $class . ' {
			public $_mockit;
			function __construct() {
				$m = MockIt::get_instance();
				//$m = MockIt::getManager("'. $class .'");
				$m::$stats["' . $class . '"]["__construct"]["args"] = func_get_args();
			}
			function __call( $func, $args ) {
				$m = MockIt::get_instance();
				$m::$stats["' . $class . '"][$func]["args"] = $args;
				return isset( $m::$stats["' . $class . '"][$func]["return"] ) ? $m::$stats["' . $class . '"][$func]["return"] : null;
			}
		}';
		eval( $content );

		return self::get_instance();
		//file_put_contents( 'mockit.php', $content );
	}
}

class WidgetTest extends TestCase {

	public $widget = null;

	function tearDown() {
		//test::clean();
		parent::tearDown();
	}

	function xtest_construct() {
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

	function test_form_output() {



		//$mock->withArgs(1,2);
		//$mock


		//$wp_query_mock2 = \Mockery::mock('WP_Query');
		//$wp_query_mock->shouldReceive('have_posts')->once();

		//$m = Mockit::double('WP_Query');

		//$m->hasMethod('have_posts')->willReturn(true);

		//$m->hasMethod('__construct');

		/*$my_query = new \WP_Query( array(
			'post_type' => 'post',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'ignore_sticky_posts' => true
		) );
		*/

		//$args = $m->getMethodArgs('__construct');

		//var_dump($args);

		//var_dump($my_query->have_posts());

		//$this->assertInstanceOf('WP_Query', $my_query);


		$mock = \Mockery::mock('WP_Query');
		$mock->shouldReceive('have_posts')->andReturn(true);

		$this->assertTrue($mock->have_posts());

		$q = new WP_Query();

		$this->assertTrue($q->have_posts());

		$instance = array(
			'post_id' => 100,
			'title' => 'foo',
			'vars' => array(),
		);

		$widget  = new AlchemyPlugins\WrapWidget;

		//$widget->form( $instance );



		/*
		$widget_mock = \Mockery::mock('AlchemyPlugins\WrapWidget')->makePartial();


		ob_start();
		$widget_mock->form( $instance );
		$output = ob_get_contents();
		ob_end_clean();

		$this->assertEquals( 'baz', $widget_mock );
		*/
	}
}
