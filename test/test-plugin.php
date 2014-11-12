<?php

class PluginTest extends PHPUnit_Framework_TestCase {

	public $plugin = null;

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
		$this->plugin = new AlchemyPlugins\Wrap;
	}

	function tearDown() {
		unset( $this->plugin );
		\WP_Mock::tearDown();
	}

	function test_setup_cpt() {
		\WP_Mock::expectActionAdded( 'init', array( $this->plugin, 'register_cpt' ) );
		\WP_Mock::expectActionAdded( 'add_meta_boxes', array( $this->plugin, 'adjust_meta_boxes' ), 0 );
		\WP_Mock::expectActionAdded( 'default_hidden_meta_boxes', array( $this->plugin, 'set_default_hidden_meta_boxes' ), 10, 2 );
		\WP_Mock::wpFunction( 'is_admin', array(
			'times' => 1,
			'return' => true
		) );
		\WP_Mock::expectFilterAdded( 'post_row_actions', array( $this->plugin, 'disable_quick_edit' ), 10, 2 );
		$this->plugin->setup_cpt();
	}

	function test_register_cpt() {
		\WP_Mock::wpFunction( 'register_post_type', array(
			'times' => 1,
		) );
		$this->plugin->register_cpt();
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
		\WP_Mock::wpFunction( 'remove_meta_box', array(
			'times' => 1,
			'args' => array( 'slugdiv', 'ap_wrap3', 'normal' )
		) );
		\WP_Mock::wpFunction( 'add_meta_box', array(
			'times' => 1,
			'args' => array( 'slugdiv', 'Content Name', 'post_slug_meta_box', 'ap_wrap3', 'side' )
		) );
		\WP_Mock::wpFunction( 'remove_meta_box', array(
			'times' => 1,
			'args' => array( 'authordiv', 'ap_wrap3', 'normal' )
		) );
		\WP_Mock::wpFunction( 'add_meta_box', array(
			'times' => 1,
			'args' => array( 'authordiv', 'Author', 'post_author_meta_box', 'ap_wrap3', 'side' )
		) );
		$this->plugin->adjust_meta_boxes();
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

	function test_setup_shortcode() {
		\WP_Mock::wpFunction( 'add_shortcode', array(
			'times' => 1,
			'args' => array( 'wrapper', array( $this->plugin, 'wrapper_shortcode' ) )
		) );
		$this->plugin->setup_shortcode();
	}

	/**
	 * @dataProvider wrapper_shortcode_id
	 */
	function test_wrapper_shortcode($id) {
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
			array( 'id-slug' ),
		);
	}
}
