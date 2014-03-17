<?php

/**
 * Plugin Name: Stats
 * Plugin URI: http://jonbrennecke.github.io/
 * Version: v1.00
 * Author: <a href="http://jonbrennecke.github.io/">Jon Brennecke</a>
 * Description: Infographics
 *
 * @package WordPress
 * @subpackage Social
 * @since Social 1.0
 *
 */

namespace social;

/**
 * add actions to their appropriate WP hooks
 */
function add_actions() {

	// back-end actions
	if ( is_admin() ) {

		require_once dirname( __FILE__ ) . '\plugin-options.php';

		// queue admin scripts and stylesheet
		add_action('admin_enqueue_scripts','social\admin_enqueue_scripts');

		// have WP create the plugin options page on the back-end
		add_action( 'admin_menu', 'social\add_plugin_page' );

		// register settings fields with WP
		add_action( 'admin_init', 'social\register_options');

		// register ajax functions with WP
		add_action('wp_ajax_get_settings_fields', 'social\get_settings_fields');
		add_action('wp_ajax_save_settings_fields', 'social\save_settings_fields');
	}

	// front-end actions
	else {

		// enqueue scripts and stylesheet
		add_action('wp_enqueue_scripts','social\enqueue_scripts');
	}

	// actions for both front-end and back-end

	// register the widgets
	add_action( 'widgets_init', 'social\register_widgets' );

}


/**
 * enqueue scripts and stylesheets for the back-end
 */
function admin_enqueue_scripts() {

	// admin stylesheet
	wp_enqueue_style('social-admin-css', plugins_url( '/social/css/social-admin-style.css', dirname(__FILE__)));

	// admin javascript (needs jQuery UI)
	wp_enqueue_script('social-js',plugins_url('/social/js/social-admin.js', dirname(__FILE__)), array('jquery','jquery-ui-core','jquery-effects-core') );

}

/**
 * enqueue scripts and stylesheets for the front-end
 */
function enqueue_scripts() {

	// stylesheet
	wp_enqueue_style('social-css', plugins_url( '/social/css/social-style.css', dirname(__FILE__)));
	
}

/**
 *
 *
 */
function register_widgets() { 

	require_once 'inc/twitter-widget.php';
	require_once 'inc/facebook-widget.php';

	register_widget( 'Twitter_Widget' ); 
	register_widget( 'Facebook_Widget' ); 
}

?>