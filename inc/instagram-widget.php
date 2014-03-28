<?php 

/**
 * Instagram Widget Class
 *
 * @package WordPress
 * @subpackage Social
 * @since Social 1.0
 *
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

		$widget_ops = array('description' => __( "Instagram Widget" ) );
		parent::__construct('instagram', __("Instagram Widget"), $widget_ops);

		// establish an api connection to facebook with user provided credentials from the WP database 
		$this->api = new Instagram_API( array(
			'client-id'  => get_option('instagram-id'),
			'client-secret' => get_option('instagram-secret'),
			'csrf' => get_option( 'instagram-csrf-code'),
			'access-token' => get_option( 'instagram-access-token' )
		));
	}

	/**
	 * Front-end display of widget.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */

	public function widget( $args, $instance ) {

		$data = $this->api->feed()->data;	
		$profile = $this->api->profile()->data;

		echo $args['before_widget'];

		?>
			<div class="instagram2">
			<?php foreach ($data as $i => $obj) : ?>
				<div class='img-container'>
					<img class='main-img' src="<?php echo $obj->images->standard_resolution->url; ?>" />
				</div>
				<div class='info-container'>
					<div class='info_user-stats'>
						<h1 class="following"><?php echo $profile->counts->follows; ?><span class='label'>Following</span></h1>
						<h1 class="followers"><?php echo $profile->counts->followed_by; ?><span class='label'>Followers</span></h1>
					</div>
					<div class='info_text'>
						<h1><?php echo $obj->user->full_name; ?></h1>
						<h2><?php echo $obj->caption->text; ?></h2>
						<div class='profile-pic'>
							<img class='profile-pic' src="<?php echo $obj->user->profile_picture; ?>" />
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		 	</div>
	 	<?php

	 	echo $args['after_widget'];

	}

	/**
	 * 
	 *
	 */
	public function login_redirect() {
		return $this->api->login_redirect();
	}


}