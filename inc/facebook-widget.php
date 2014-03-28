<?php 

/**
 * Facebook Widget Class
 *
 * @package WordPress
 * @subpackage Social
 * @since Social 1.0
 */


/** 
 * include the sdk class
 * @see https://github.com/facebook/facebook-php-sdk
 */
require_once 'facebook-sdk/facebook.php';

class Facebook_Widget extends WP_Widget {

	var $facebook;

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
	}

	/**
	 * Front-end display of widget.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */

	public function widget( $args, $instance ) {

		// get user ID
		$user = $this->facebook->getUser();

		if ($user) {
			try {
				// Proceed with a logged in user who's authenticated.
				$statuses = $this->facebook->api( '/me/statuses/' );

			} catch ( FacebookApiException $e ) {
				error_log($e);
				$user = null;
			}
		}

		// sanitize $instance

		echo $args['before_widget'];

		echo "<div class=\"hullabaloo facebook\">";
		echo "<h1><span class='fa-facebook'></span>Facebook</h1>";
		echo "<h2>{$statuses['data'][0]['from']['name']}</h2>";
		echo "<h3>\"{$statuses['data'][0]['message']}\"</h3>";
		echo '</div>';

	 	echo $args['after_widget'];

	}

	public function login_redirect() {

		// get user ID
		$user = $this->facebook->getUser();

		$loginUrl = $this->facebook->getLoginUrl( array(
			'scope' => 'read_stream,offline_access',
			'fbconnect' => 1,
			'redirect_uri' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
			'display' => 'popup'
		));
	
		return $loginUrl;
	}


}