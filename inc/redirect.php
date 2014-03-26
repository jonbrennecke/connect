<?php

/**
* OAuth 2.0 Redirect Destination
*
*
*/

// locate the main wordpress directory
$wp_dir = preg_replace('/wordpress\\\wp-content\\\(.*)/', 'wordpress\\', dirname( __FILE__ ) ); 

// load the wordpress toolchain
// this will include all the basic wordpress functions
// and enable us to write to the WP database
require_once $wp_dir . 'wp-load.php';

if ( isset( $_GET['s'] ) && isset( $_GET['code'] ) && !empty( $_GET['code'] ) ) {
	
	/**
	 *  ~~~~~~~~~~ INSTAGRAM ~~~~~~~~~~
	 * 
	 */
	if ( $_GET['s'] == 'instagram' ) {
		
		// save the csrf code
		update_option( 'instagram-csrf-code', $_GET['code'] );

		// the access token has no expiry, but the CSRF code is only good for a 
		// short time, so we need to get an access token and save it right away

		require_once "instagram-api.php";

		$api = new Instagram_API( array(
			'client-id'  => get_option('instagram-id'),
			'client-secret' => get_option('instagram-secret'),
			'csrf' => get_option( 'instagram-csrf-code')
		));

		$token = $api->get_access_token();

		if ( $token ) {
			update_option( 'instagram-access-token', $token );
		}

	}

	/**
	 *  ~~~~~~~~~~ GOOGLE PLUS ~~~~~~~~~~
	 * 
	 */
	if ( $_GET['s'] == 'google-plus' && isset( $_GET['state'] ) && !empty($_GET['state']) ) {

		// save the 'code' value
		// the api class will later exchange this for an access token
		update_option( 'google-code', $_GET['code'] );

		// confirm anti-forgery 'state' token ('CSRF' token) w/ the value stored in the WP database
		$state = get_option( 'gooogle-csrf-code' );

		if ( $state == $_GET['state'] ) {

			// if the CSRF tokens match, the next step is to get and save an access token

			require_once "google-api.php";

			$api = new GPlus_API( array(
				'client-id'  => get_option('google-app-id'),
				'client-secret' => get_option('google-app-secret'),
				'code' => $_GET['code']
			));

			$token = $api->get_access_token();

			if ( isset( $token->access_token ) && isset( $token->token_type ) && $token->token_type == "Bearer" ) {
				update_option( 'google-access-token', $token->access_token );

				echo 'here';
			}

		}

	}

	/**
	 *  ~~~~~~~~~~ FACEBOOK ~~~~~~~~~~
	 * 
	 * Facebook also uses OAuth 2.0, however, for Facebook we're using it's pre-packaged sdk files
	 *
	 * @see inc/facebook-sdk/
	 *
	 */
}

?>