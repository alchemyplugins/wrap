<?php
/**
 * Plugin Name: <%= pkg.title %>
 * Plugin URI:  <%= pkg.homepage %>
 * Description: <%= pkg.description %>
 * Version:     <%= pkg.version %>
 * Author:      <%= pkg.author.name %>
 * Author URI:  <%= pkg.author.url %>
 * Text Domain: <%= pkg.name %>
 * Domain Path: /languages
 */

require_once dirname(__FILE__) . '/assets/inc/Wrap.php';
require_once dirname(__FILE__) . '/assets/inc/WrapWidget.php';

$wrap = new AlchemyPlugins\Wrap;
$wrap->init();
$wrap->init_widget( 'AlchemyPlugins\WrapWidget' );

define( 'AP_WRAP_VERSION', '<%= pkg.version %>' );
define( 'AP_WRAP_URL',     plugin_dir_url( __FILE__ ) );
define( 'AP_WRAP_PATH',    dirname( __FILE__ ) . '/' );

$cpt = 'ap_wrap3';

// enqueue style and script, inject my.ajaxurl
add_action( 'admin_enqueue_scripts', '<%= pkg.settings.namespace %>_enqueue_scripts' );
function <%= pkg.settings.namespace %>_enqueue_scripts() {
	wp_enqueue_style( '<%= pkg.name %>-css', AP_WRAP_URL . 'assets/style.min.css', false,  AP_WRAP_VERSION, false );
	$handle = '<%= pkg.name %>-js';
	wp_enqueue_script( $handle, AP_WRAP_URL . 'assets/script.min.js', array('jquery'), AP_WRAP_VERSION, true );
	wp_localize_script( $handle, 'my', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' )
	) );
}

// finds
// https://lkwdwrd.com/wp-shortcode-wp-html-wordpress-shortcodes-javascript/
// http://twig.sensiolabs.org

// similar plugins:
// https://wordpress.org/plugins/global-content-blocks/
// https://wordpress.org/plugins/isw-blocks/screenshots/
// https://wordpress.org/plugins/custom-post-widget/screenshots/

// import/export feature
// modify shortcode output via filter
// add a wrapper widget
// option to remove wordpress wpautop and texturize from content
// default translations: spanish, French, German, Polish, Russian, Chinese, Japanese
// custom menu icon
// use template engine (Twig, Handlebars, ...);
