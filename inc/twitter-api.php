<?php

/**
 * Twitter API Class
 *
 * @package WordPress
 * @subpackage Social
 * @since Social 1.0
 */

class Twitter_API {

	// credentials
	var $cred = array(
		'api-key' => 'default_api_key',
		'api-secret' => 'default_api_secret',
		'access-token' => 'default_access_token',
		'access-secret' => 'default_access_secret'
	);

	var $bearer_token;

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

		// retrieve a bearer token
		$this->get_bearer_token();
	}

	/**
	 * Returns an HTTP 200 OK response code and a representation of the requesting user 
	 * if authentication was successful; returns a 401 status code and an error message 
	 * if not. Use this method to test if supplied user credentials are valid.
	 * 
 	 * @see https://dev.twitter.com/docs/api/1.1/get/account/verify_credentials
	 */
	public function verify( $args = array() ) {

		$default = array( 
			'entities' => false,
			'status' => true
		);

		// merge with the default arguments
		if ( is_array($args) && !empty($args) ) {
			$params = array_merge($default, $args);
		}

		// arguments for wp_remote_get
		$get_args = array(
			'method' => 'GET',
			'httpversion' => '1.0',
			'headers' => array( 
				'Authorization' => 'Bearer ' . $this->bearer_token,
				'Accept-Encoding' => 'gzip'
			)
		);

		// build url
		$url = 'https://api.twitter.com/1.1/account/verify_credentials.json';
		// $url .= '?' . http_build_query( array( 'entities' => $params['entities'], 'skip_status' => $params['status'] ) );

		// post request to Twitter
		$response = wp_remote_post( $url, $get_args );

		// is the post request successful?
		if( is_wp_error( $response ) ) {
			die();
		}
			
		// decode the JSON string in 'body' into a php 'stdClass' object
		$body = json_decode( $response['body'] );

		return $response;
	}

	/**
	 * get the provided user's timeline from twitter
	 * 
 	 * @see https://dev.twitter.com/docs/auth/application-only-auth
	 */
	public function timeline( $args = array() ) {

		$default = array( 
			'count' => 1, 
			'screen_name' => 'twitterapi' 
		);

		// merge with the default arguments
		if ( is_array($args) && !empty($args) ) {
			$params = array_merge($default, $args);
		}

		// arguments for wp_remote_get
		$get_args = array(
			'method' => 'GET',
			'httpversion' => '1.1',
			'headers' => array( 
				'Authorization' => 'Bearer ' . $this->bearer_token,
				'Accept-Encoding' => 'gzip'
			)
		);

		// build url
		$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
		$url .= '?' . http_build_query( array( 'count' => $params['count'], 'screen_name' => $params['screen_name'] ) );

		// post request to Twitter
		$response = wp_remote_post( $url, $get_args );

		// is the post request successful?
		if( is_wp_error( $response ) ) {
			die();
		}
			
		// decode the JSON string in 'body' into a php 'stdClass' object
		$body = json_decode( $response['body'] );

		return $body;
	}


	/**
	 *
	 * Returns a variety of information about the user specified by the required user_id 
	 * or screen_name parameter. The author's most recent Tweet will be returned inline when possible.
	 *
	 * @see https://dev.twitter.com/docs/api/1.1/get/users/show
	 */
	public function user( $screen_name = 'twitterapi' ) {

		// arguments for wp_remote_get
		$get_args = array(
			'method' => 'GET',
			'httpversion' => '1.1',
			'headers' => array( 
				'Authorization' => 'Bearer ' . $this->bearer_token,
				'Accept-Encoding' => 'gzip'
			)
		);

		// build url
		$url = 'https://api.twitter.com/1.1/users/show.json';
		$url .= '?' . http_build_query( array( 'screen_name' => $screen_name, 'include_entities' => true ) );

		// post request to Twitter
		$response = wp_remote_post( $url, $get_args );

		// is the post request successful?
		if( is_wp_error( $response ) ) {
			die();
		}
			
		// decode the JSON string in 'body' into a php 'stdClass' object
		$body = json_decode( $response['body'] );

		return $body;
	}


	/**
	 *
	 * get the OAuth 2 bearer token from twitter
	 *
	 * @see https://dev.twitter.com/docs/auth/application-only-auth
	 */
	private function get_bearer_token() {

		// URL encode the consumer key and the consumer secret and concatenate
		// the encoded consumer key and the encoded consumer with a ':' between them. 
		$bearer_token = urlencode( $this->cred['api-key'] ) . ':' . urlencode( $this->cred['api-secret'] );
		
		// base 64 encode the result. 
		$bearer_token_64 = base64_encode( $bearer_token );

		// arguments for wp_remote_post
		$post_args = array(
			'method' => 'POST',
			'httpversion' => '1.1',
			'headers' => array( 
				'Authorization' => 'Basic ' . $bearer_token_64,
				'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
				'Accept-Encoding' => 'gzip'
			),
			'body' => array( 'grant_type' => 'client_credentials' )
		);
		
		// send an HTTPS POST request to Twitter
		$response = wp_remote_post( 'https://api.twitter.com/oauth2/token', $post_args );

		// is the post request successful?
		if( is_wp_error( $response ) ) {
			die();
		}
			
		// decode the JSON string in 'body' into a php 'stdClass' object
		$body = json_decode( $response['body'] );

		// validate 'token_type'
		if ( !isset($body->token_type) || $body->token_type != 'bearer' ) {
			die();
		}

		// return the access token or die if it's empty
		if ( isset($body->access_token) && !empty($body->access_token) ) {
			$this->bearer_token = $body->access_token;
		}
		else die();

	}

}

?>