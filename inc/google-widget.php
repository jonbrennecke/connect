<?php 

/**
 * Google Plus Widget Class
 *
 * @package WordPress
 * @subpackage Social
 * @since Social 1.0
 */


// include the api class
require_once 'google-api.php';

class GPlus_Widget extends WP_Widget {

	var $api;

	/**
	 * Register widget with WordPress.
	 */

	public function __construct() {

		$widget_ops = array('description' => __( "Google + Widget" ) );
		parent::__construct('gplus', __('Google + Widget'), $widget_ops);

		// establish an api connection to google plus with user provided credentials from the WP database 
		$this->api = new GPlus_API( array(
			'client-id'  => get_option('google-app-id'),
			'client-secret' => get_option('google-app-secret'),
			'csrf' => get_option('gooogle-csrf-code')
		));

	}

	/**
	 * Front-end display of widget.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */

	public function widget( $args, $instance ) {

		$this->api->get_access_token();

	}

	public function login_redirect() {
		return $this->api->login_redirect();
	}

}