<?php 

/**
 * Instagram Widget Class
 *
 * @package WordPress
 * @subpackage Social
 * @since Social 1.0
 */


/** 
 * include the sdk class
 *
 */
require_once 'instagram-api.php';

class Instagram_Widget extends WP_Widget {

	var $api;

	/**
	 * Register widget with WordPress.
	 */

	public function __construct() {

		$widget_ops = array('description' => __( "Facebook Widget" ) );
		parent::__construct('facebook', __('Facebook Widget'), $widget_ops);

		// establish an api connection to facebook with user provided credentials from the WP database 
		$this->api = new Instagram_API( array(
			'client-id'  => get_option('instagram-id'),
			'client-secret' => get_option('instagram-secret'),
		));
	}

	/**
	 * Front-end display of widget.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */

	public function widget( $args, $instance ) {

		

	}

	public function login_redirect( ) {
	
		return $this->api->login_url();
		
	}


}