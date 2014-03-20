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
				$user_profile = $this->facebook->api('/me');
				// var_dump($user_profile);

			} catch (FacebookApiException $e) {
				error_log($e);
				$user = null;
			}
		}

		// sanitize $instance

		echo "<div class=\"hullabaloo facebook\">";
		echo "<h1><span class='fa-facebook'></span></h1>";
		echo "<h2>{$user_profile['name']}</h2>";
		echo "<h3>{$user_profile['location']}</h3>";

		// $this->do_profile_pic( $tweets[0]->user->profile_image_url );

		// $this->do_tweet_text( $tweets[0]->text, $tweets[0]->user, $tweets[0]->entities ); 

		// $this->do_user_info_bar( $tweets[0]->user );
			
		echo '</div>';

	}

}


// Login or logout url will be needed depending on current user state.
// if ($user) {
// 	$logoutUrl = $facebook->getLogoutUrl();
// } else {
// 	$statusUrl = $facebook->getLoginStatusUrl();
// 	$loginUrl = $facebook->getLoginUrl();
// }

// $loginUrl = $this->facebook->getLoginUrl(array(
// 	'scope' => 'publish_stream,read_stream,offline_access,manage_pages',
// 	'fbconnect' =>  1,
// 	'redirect_uri' => 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']
// ));


// $instance = wp_parse_args( (array) $instance, array( 'screen_name' => 'twitterapi', 'count' => 1, 'class_name' => 'magnum-opus' ) );

// $tweets = $this->twitter_api->get_user_timeline( array( 
// 	'count' => $instance['count'],
// 	'screen_name' => $instance['screen_name']
// ));