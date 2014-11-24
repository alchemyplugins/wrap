<?php

class PluginTest extends WP_UnitTestCase {

	public $plugin = null;

	public $post_type = 'ap_wrap3';

	function setUp() {
		parent::setUp();
		$this->plugin = new AlchemyPlugins\Wrap;
	}

	function tearDown() {
		unset( $this->plugin );
		\Mockery::close();
		\DryRun::clean();
		parent::tearDown();
	}

	function set_is_admin_truthy() {
		set_current_screen( 'edit.php' );
		$this->assertTrue( is_admin(), 'is admin screen' );
	}

	function test_init() {

		$this->set_is_admin_truthy();

		$data = array(
			array( 'init', array( $this->plugin, 'register_cpt' ), 10 ),
			array( 'add_meta_boxes', array( $this->plugin, 'adjust_meta_boxes' ), 0 ),
			array( 'default_hidden_meta_boxes', array( $this->plugin, 'set_default_hidden_meta_boxes' ), 10 ),
			array( 'post_row_actions', array( $this->plugin, 'disable_quick_edit' ), 10 ),
		);

		foreach( $data as $row ) {
			list( $filter, $func ) = $row;
			$this->assertFalse( has_action( $filter, $func ), sprintf( 'Filter/action %s is not added yet', $filter ) );
		}

		$this->plugin->init();

		foreach( $data as $row ) {
			list( $filter, $func, $expected ) = $row;
			$this->assertEquals( $expected, has_action( $filter, $func ), sprintf( 'Filter/action %s is added', $filter ) );
		}

		$post_id = $this->factory->post->create();
		$this->assertEquals( 'Post content 1', do_shortcode( sprintf( '[wrapper id="%s"]', $post_id ) ), 'shortcode should return "Post content 1"' );
	}

	function test_register_cpt() {
		$this->assertFalse( post_type_exists( $this->post_type ), 'post type not created yet' );
		$this->plugin->register_cpt();
		$this->assertTrue( post_type_exists( $this->post_type ), 'post type created');
	}

	function test_disable_quick_edit() {
		$actions = array( 'foo' => 'bar', 'inline hide-if-no-js' => 'baz' );
		$post = new stdClass;
		$post->post_type = 'ap_wrap3';
		$result = $this->plugin->disable_quick_edit( $actions, $post );
		$this->assertTrue( is_array( $result ) );
		$this->assertEquals( 1, count( $result ) );
		$post->post_type = 'foobar';
		$result = $this->plugin->disable_quick_edit( $actions, $post );
		$this->assertTrue( is_array( $result ) );
		$this->assertEquals( 2, count( $result ) );
	}

	function test_adjust_meta_boxes() {

		$remove_meta_box_spy = new DryRun\Spy( 'remove_meta_box' );

		$remove_meta_box_spy->on( 'add_meta_box' );

		$add_meta_box_spy = new DryRun\Spy( 'add_meta_box' );

		$this->plugin->adjust_meta_boxes();

		$this->assertEquals( 2, $remove_meta_box_spy->called() );

		$this->assertEquals( array( 'slugdiv', 'ap_wrap3', 'normal' ), $remove_meta_box_spy->called(0) );

		$this->assertEquals( array( 'authordiv', 'ap_wrap3', 'normal' ), $remove_meta_box_spy->called(1) );

		$this->assertEquals( 2, $add_meta_box_spy->called() );

		$this->assertEquals( array( 'slugdiv', 'Content Name', 'post_slug_meta_box', 'ap_wrap3', 'side' ), $add_meta_box_spy->called(0) );

		$this->assertEquals( array( 'authordiv', 'Author', 'post_author_meta_box', 'ap_wrap3', 'side' ), $add_meta_box_spy->called(1) );
	}

	function test_set_default_hidden_meta_boxes() {
		$hidden = array( 'foo', 'bar' );
		$post = new stdClass;
		$post->post_type = 'ap_wrap3';
		$result = $this->plugin->set_default_hidden_meta_boxes( $hidden, $post );
		$this->assertTrue( is_array( $result ) );
		$this->assertEquals( 3, count( $result ) );
		$this->assertContains( 'authordiv', $result );
		$post->post_type = 'foobar';
		$result = $this->plugin->set_default_hidden_meta_boxes( $hidden, $post );
		$this->assertTrue( is_array( $result ) );
		$this->assertEquals( 2, count( $result ) );
		$this->assertNotContains( 'authordiv', $result );
	}

	/**
	 * @dataProvider wrapper_shortcode_id
	 */
/*	function test_wrapper_shortcode($id) {
		$post = new stdClass;
		$post->post_content = 'foo{{var}} {{content}}';
		\WP_Mock::wpFunction( 'get_post', array(
			'return' => $post
		) );
		\WP_Mock::wpFunction( 'get_page_by_path', array(
			'return' => $post
		) );
		$atts = array( 'id' => $id, 'var' => 'bar' );
		\WP_Mock::wpFunction( 'shortcode_atts', array(
			'return' => $atts
		) );
		\WP_Mock::wpPassthruFunction( 'do_shortcode' );
		\WP_Mock::onFilter( 'ap_wrap_wrapper_content' );
		$content = $this->plugin->wrapper_shortcode( $atts, 'baz' );
		$this->assertEquals( 'foobar baz', $content );
	}

	function wrapper_shortcode_id() {
		return array(
			array( 1 ),
			array( 'slug' ),
		);
	}

	function test_init_widget() {
		\WP_Mock::expectActionAdded( 'widgets_init', array( $this->plugin, 'register_widget' ) );
		\WP_Mock::expectActionAdded( 'wp_ajax_ap_get_wrapper_vars', array( $this->plugin, 'widget_ajax' ) );
		$widget = 'foo\bar';
		$this->plugin->init_widget( $widget );
		$this->assertEquals( $widget, $this->plugin->widget_class );
	}

	function test_register_widget() {
		$key = 'foo\bar';
		$this->plugin->widget_class = $key;
		\WP_Mock::wpFunction( 'register_widget', array(
			'times' => 1,
			'args' => array( $key )
		) );
		$this->plugin->register_widget();
	}

	function test_widget_ajax() {
		$mock = \Mockery::mock('foo\bar');
		$mock->shouldReceive('get_var_fields')->andReturn('baz');
		$this->plugin->widget_class = $mock;
		\WP_Mock::wpFunction( 'wp_die', array(
			'times' => 1
		) );
		ob_start();
		$this->plugin->widget_ajax();
		$output = ob_get_contents();
		ob_end_clean();
		$this->assertEquals( 'baz', $output );
	}
	*/
}
