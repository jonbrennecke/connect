<?php 

/**
 * Facebook Widget Class
 *
 * @package WordPress
 * @subpackage Social
 * @since Social 1.0
 */


// include the sdk class
// @see https://github.com/facebook/facebook-php-sdk
require_once 'facebook-sdk/facebook.php';

class Facebook_Widget extends WP_Widget {

	var $facebook;
	var $user;

	/**
	 * Register widget with WordPress.
	 */

	public function __construct() {

		$widget_ops = array('description' => __( "Facebook Widget" ) );
		parent::__construct('facebook', __('Facebook Widget'), $widget_ops);

		// establish an api connection to facebook with user provided credentials from the WP database 
		$this->facebook = new Facebook( array(
			'appId'  => get_option('facebook-app-id'),
			'secret' => get_option('facebook-app-secret'),
			'cookie' => true
		));

		// get user ID
		$this->user = $this->facebook->getUser();
	}

	/**
	 * Front-end display of widget.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */

	public function widget( $args, $instance ) {
		if ($this->user) {
			try {
				// Proceed knowing you have a logged in user who's authenticated.
				$user_profile = $this->facebook->api('/me');
				var_dump($user_profile);

			} catch (FacebookApiException $e) {
				error_log($e);
				$this->user = null;
			}
		}

		// var_dump($this->user);

		echo $this->user;

	}

}