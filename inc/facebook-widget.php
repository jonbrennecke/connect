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

		// $instance = wp_parse_args( (array) $instance, array( 'screen_name' => 'twitterapi', 'count' => 1, 'class_name' => 'social-wrap' ) );

		// get user ID
		$profile = $this->get_profile();

		echo $args['before_widget'];

		if ( $profile ) {

			$cover = $this->do_cover_photo( $profile );

			?>
				<div class="italia facebook">
					<div class="info-container">
						<h1><span class='fa-facebook'></span><span class="section-title">Facebook</span></h1>
						<h2 class="info_name"><?php echo $profile['name']; ?></h2>
						<h3 class="info_status-text"><?php echo $profile['statuses']['data'][0]['message']; ?></h3>
						<h4 class="info_timestamp"><?php $this->do_timestamp( $profile['statuses']['data'][0]['updated_time'] ); ?></h4>
					</div>
					<div class="cover-photo-container"><img class="cover-photo" src="<?php echo $cover ?>"></div>
				</div>
			<?php
		}

	 	echo $args['after_widget'];

	}


	/**
	 *
	 * @see https://developers.facebook.com/docs/graph-api
	 */
	private function get_profile() {
		$user = $this->facebook->getUser();

		if ( $user ) {
			try {
				// Proceed with a logged in user who's authenticated.
				return $this->facebook->api( '/me?fields=albums.fields(name,photos.limit(1)),name,picture,statuses.limit(1)' );

			} catch ( FacebookApiException $e ) {
				error_log($e);
				$user = null;
			}
		}
	}


	/**
	 * converts the date passed back from the API to a human-readable string like "2 days ago" or "5 min ago"
	 *
	 * @see http://www.php.net/manual/en/datetime.php
	 */
	private function do_timestamp( $time_str ) {
		$dtime = new DateTime( $time_str );
		$delta = (new DateTime("now"))->diff($dtime);

		if ( $delta->days == 0 ) {
			echo $delta->format('%i min ago');	
		}
		else {
			echo $delta->format('%a days ago');
		}
	}


	/**
	 *
	 * 
	 *
	 */
	public function login_redirect() {

		// get user ID
		$user = $this->facebook->getUser();

		// the only permissions we require are
		// read_stream - to retrieve statuses and posts
		// offline_access - so the user doesn't need to be logged in for the access token to work
		// user_photos - to retrieve albums and cover photo (any photos other than the users profile picture)
		$loginUrl = $this->facebook->getLoginUrl( array(
			'scope' => 'read_stream,offline_access,user_photos',
			'fbconnect' => 1,
			'redirect_uri' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
			'display' => 'popup'
		));
	
		return $loginUrl;
	}


	/**
	 *
	 * 
	 *
	 */
	private function do_cover_photo( $profile ) {

		foreach ($profile['albums']['data'] as $i => $album) {
			if ($album['name'] == 'Cover Photos') {
				$cover = $album['photos']['data'][0]['picture'];
			}
		}

		// retrieve the link to a larger image
		return preg_replace('/_s\./i', '_n.', $cover);
	}


}