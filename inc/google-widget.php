<?php 

/**
 * Google Plus Widget Class
 *
 * @package WordPress
 * @subpackage Social
 * @since Social 1.0
 */


// include the sdk class
// @see https://github.com/facebook/facebook-php-sdk
// require_once 'facebook-sdk/facebook.php';

class GPlus_Widget extends WP_Widget {

	var $gplus;

	/**
	 * Register widget with WordPress.
	 */

	public function __construct() {

		$widget_ops = array('description' => __( "Google + Widget" ) );
		parent::__construct('gplus', __('Google + Widget'), $widget_ops);

		// // establish an api connection to facebook with user provided credentials from the WP database 
		// $this->facebook = new Facebook( array(
		// 	'appId'  => get_option('facebook-app-id'),
		// 	'secret' => get_option('facebook-app-secret'),
		// 	'cookie' => true
		// ));

	}

	/**
	 * Front-end display of widget.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */

	public function widget( $args, $instance ) {




	}

}