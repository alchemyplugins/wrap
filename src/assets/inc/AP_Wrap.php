<?php

class AP_Wrap {

	public $cpt = 'ap_wrap3';

	public function init() {
		$this->setup_cpt();
		$this->setup_shortcode();
	}

	/**
	 * Creates the custom post type and it's environment.
	 *
	 * @return void
	 */
	function setup_cpt() {
		add_action( 'init', array( $this, 'register_cpt' ) );
		add_action( 'add_meta_boxes', array( $this, 'adjust_meta_boxes' ), 0 );
		add_action( 'default_hidden_meta_boxes', array( $this, 'set_default_hidden_meta_boxes'), 10, 2 );
		if ( is_admin() ) {
			add_filter( 'post_row_actions', array( $this, 'disable_quick_edit' ), 10, 2 );
		}
	}

	function setup_shortcode() {
		add_shortcode( 'wrapper', array( $this, 'wrapper_shortcode' ) );
	}

	/**
	 * Register post type.
	 *
	 * @return void
	 */
	public function register_cpt() {
		$labels = array(
			'name'               => _x( 'Wraps', 'post type general name', '<%= pkg.name %>' ),
			'singular_name'      => _x( 'Wrap', 'post type singular name', '<%= pkg.name %>' ),
			//'menu_name'          => _x( 'Wraps3', 'admin menu', '<%= pkg.name %>' ),
			//'name_admin_bar'     => _x( 'Wrap', 'add new on admin bar', '<%= pkg.name %>' ),
			'add_new'            => _x( 'Add New', 'wrap', '<%= pkg.name %>' ),
			'add_new_item'       => __( 'Add New Wrap', '<%= pkg.name %>' ),
			'new_item'           => __( 'New Wrap', '<%= pkg.name %>' ),
			'edit_item'          => __( 'Edit Wrap', '<%= pkg.name %>' ),
			'view_item'          => __( 'View Wrap', '<%= pkg.name %>' ),
			'all_items'          => __( 'All Wraps', '<%= pkg.name %>' ),
			'search_items'       => __( 'Search Wraps', '<%= pkg.name %>' ),
			'parent_item_colon'  => __( 'Parent Wraps:', '<%= pkg.name %>' ),
			'not_found'          => __( 'No wraps found.', '<%= pkg.name %>' ),
			'not_found_in_trash' => __( 'No wraps found in Trash.', '<%= pkg.name %>' )
		);
		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author', 'revisions' )
		);
		register_post_type( $this->cpt, $args );
	}

	/**
	 * Filter to remove "Quick Edit" link from the post type.
	 *
	 * @param  array   $actions An array of action links per post.
	 * @param  WP_Post $post WP_Post object of the current post.
	 *
	 * @return array  Filtered array of action links per post.
	 */
	function disable_quick_edit( $actions, $post ) {
		if( $this->cpt === $post->post_type ) {
			unset($actions['inline hide-if-no-js']);
		}
		return $actions;
	}

	/**
	 * Moves the slug and author meta boxes to the side. The slug meta box title
	 * is also changed.
	 *
	 * @return void
	 */
	function adjust_meta_boxes() {
		remove_meta_box( 'slugdiv', $this->cpt, 'normal' );
		add_meta_box( 'slugdiv', __( 'Content Name', '<%= pkg.name %>' ), 'post_slug_meta_box', $this->cpt, 'side');
		remove_meta_box( 'authordiv', $this->cpt, 'normal' );
		add_meta_box('authordiv', __( 'Author', '<%= pkg.name %>' ), 'post_author_meta_box', $this->cpt, 'side');
	}

	/**
	 * Filter to add author meta box to be hidden by default.
	 *
	 * @param  array     $hidden An array of meta boxes hidden by default.
	 * @param  WP_Screen $screen WP_Screen object of the current screen.
	 *
	 * @return array    Filtered array of meta boxes hidden by default.
	 */
	function set_default_hidden_meta_boxes( $hidden, $screen ) {
		if ( $this->cpt === $screen->post_type ) {
			array_push( $hidden, 'authordiv' );
		}
		return $hidden;
	}

	/**
	* Shortcode handler.
	*
	* @param  array  $atts    An array of attributes.
	* @param  string $content Shortcode content.
	*
	* @return string Shortcode and content transformed.
	*/
	function wrapper_shortcode( $atts, $content = null ) {
		$default_atts = array(
			'id' => null
		);

		extract( shortcode_atts( $default_atts, $atts, 'wrapper' ) );

		unset( $atts['id'] );

		if ( is_numeric( $id ) ) {
			$post = get_post( $id );
		}
		else {
			$post = get_page_by_path( $id, 'OBJECT', $this->cpt );
		}

		$vars = array();

		if ( isset( $content ) ) {
			$vars['content'] = $content;
		}

		foreach( $atts as $key => $val ) {
			$vars[$key] = $val;
		}

		$post_content = apply_filters( 'ap_wrap_wrapper_content', $post->post_content, $vars );

		$post_content = $this->replace( $post_content, $vars );

		return do_shortcode( $post_content );
	}

	/**
	* Searches and replaces variables within content.
	*
	* @param  string $str A content string.
	* @param  array  $arr An assoc-array of variable and value pairs.
	*
	* @return string A filtered content string.
	*/
	private function replace( $str, $arr ) {
		$search = array();
		$replace = array();
		if ( is_array( $arr ) ) {
			foreach( $arr as $k => $v ) {
				array_push( $search, '{{' . $k . '}}' );
				array_push( $replace, $v );
			}
		}
		return str_replace( $search, $replace, $str );
	}
}
