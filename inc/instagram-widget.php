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

		$data = $this->api->feed();	

		echo '<div class="instagram">';
		foreach ($data as $i => $obj) {
			echo "<img src=\"{$obj->images->low_resolution->url}\" >";
			echo "<h1>{$obj->user->full_name}</h1>";
			echo "<h2>{$obj->likes->count} Likes</h2>";
			echo "<h2>{$obj->comments->count} Comments</h2>";
			echo "<img src=\"{$obj->user->profile_picture}\" >";
		}
	 	echo '</div>';

		// echo get_option( 'instagram-access-token' );

	}

	public function login_redirect( ) {
	
		return $this->api->login_redirect();

	}


}