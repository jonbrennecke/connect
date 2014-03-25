<?php

/**
 * Instagram API Class
 *
 * @package WordPress
 * @subpackage Social
 * @since Social 1.0
 */

class Instagram_API {

	// default credentials
	var $cred = array(
		'client-id' => '',
		'client-secret' => '',
		'csrf' => '',
		'access-token' => ''
	);


	/**
	 * Constructor
	 *
	 * @param
	 */

	public function __construct( $cred = array( ) ) {
		
		// merge supplied credentials with default arguments
		if ( is_array($cred) && !empty($cred) ) {
			$this->cred = array_merge($this->cred, $cred);
		}

	}

	public function login_url( ) {

		// build url
		$url = 'https://instagram.com/oauth/authorize/?';
		$url .= http_build_query( array( 
			'client_id' => $this->cred['client-id'], 
			'redirect_uri' => plugins_url( '/inc/redirect.php?s=instagram', dirname(__FILE__) ),
			'response_type' => 'code' ) );

		return $url;
	}


	/**
	 * Get the OAuth 2.0 access token
	 * 
	 */
	public function get_access_token() {

		// arguments for wp_remote_post
		$post_args = array(
			'method' => 'POST',
			'httpversion' => '1.1',
			'headers' => array( 
				'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
				'Accept-Encoding' =>  'allpication/json'
			),
			'body' => array( 
				'grant_type' => 'authorization_code', 
				'client_id' => $this->cred['client-id'],  
				'client_secret' => $this->cred['client-secret'],
				'redirect_uri' => plugins_url( '/inc/redirect.php?s=instagram', dirname(__FILE__) ),
				'code' => $this->cred['csrf']
			)
		);
		
		// send an HTTPS POST request to Twitter
		$response = wp_remote_post( 'https://api.instagram.com/oauth/access_token', $post_args );

		// is the post request successful?
		if( is_wp_error( $response ) ) {
			die();
		}
			
		// decode the JSON string in 'body' into a php 'stdClass' object
		$body = json_decode( $response['body'] );

		if ( isset( $body->access_token ) ) {
			return $body->access_token;
		}
	}

	public function feed( /* polymorphic */ ) {

		if ( func_num_args() > 0 ) {
			// user
			// count
		}
		else {
			$user = "self";
		}

		// arguments for wp_remote_post
		$get_args = array(
			'method' => 'GET',
			'httpversion' => '1.1',
			'headers' => array( 
				'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
				'Accept-Encoding' =>  'gzip'
			),
		);

		$url = "https://api.instagram.com/v1/users/self/media/recent";

		$url .= "?" . http_build_query( array(
			'access_token' => $this->cred['access-token'],
			'count' => 1,
		));
		
		// send an HTTPS GET request to Instagram
		$response = wp_remote_get( $url, $get_args );

		// is the request successful?
		if( is_wp_error( $response ) ) {
			die();
		}

		$body = json_decode( $response['body'] );
	
		var_dump( $body );

	}
}

?>