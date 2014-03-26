<?php

/**
 * OAuth API Base Class
 *
 * @package WordPress
 * @subpackage Social
 * @since Social 1.0
 *
 *
 * OAuth (or 'Open Authentication') 2.0 is a standard protocol for applications (such as this one!)
 * to access information on behalf of a user. Essentially, it allows users to share specific data 
 * from their profiles while keeping their usernames, passwords, and other sensitive information private.
 *
 * 
 * @see http://en.wikipedia.org/wiki/OAuth
 *
 */

class OAuth {

	/*
	 * default credentials
	 *
	 */
	var $cred = array(
		'client-id' => '',
		'client-secret' => '',
		'csrf' => '',
		'code' => '',
		'access-token'
	);


	/**
	 * Constructor
	 *
	 * @param $cred - array of credentials
	 */

	public function __construct( $cred = array( ) ) {
		
		// merge supplied credentials with default arguments
		if ( is_array($cred) && !empty($cred) ) {
			$this->cred = array_merge($this->cred, $cred);
		}

	}

	/**
	 * in the (explicit) OAuth 2.0 flow, the user is redirected to a page where he/she may choose
	 * whether or not to consent to the application's request for access to the user's data.
	 *
	 * @return ( string ) URI to which the authentication server will redirect the user
	 * once the user consents to the requested api privileges
	 *
	 */
	public function login_redirect() {

		/**
		 * this function is overwritten in the inheriting classes
		 */
	}

	/**
	 *
	 * OAuth requires a user to sign API requests with an access token.
	 * The access token is retrieved by making an HTTPS POST request to a special
	 * API endpoint (usually something like '/oauth/token' )
	 *
	 * In classes extending this one, this function should call the private method '__get_access_token'
	 * with the right url and parameters for the POST request
	 *
	 * @return ( string ) the access token
	 */
	public function get_access_token() {

		/**
		 * this function is overwritten in the inheriting classes
		 */

	}


	/**
	 *
	 * The access token is retrieved by making an HTTPS POST request to a special
	 * API endpoint (usually something like '/oauth/token' )
	 *
	 * This function handles the POST and error handling.
	 *
	 * @param $url (string) - base url to which to make the HTTPS POST request
	 * @param $post_args (array) - arguments for the call to the wordpress function wp_remote_post
	 *
	 * @return (string) - the access token
	 * 
	 */
	protected function __get_access_token( $url = "", $post_args = array() ) {

		if ( empty($post_args) ) {

			// default post args
			$post_args = array(
				'method' => 'POST',
				'httpversion' => '1.1',
				'headers' => array( 
					'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
					'Accept-Encoding' =>  'application/json',
				),
				'body' => array( 
					'client_id' => $this->cred['client-id'],  
					'client_secret' => $this->cred['client-secret'],
					'redirect_uri' => plugins_url( '/inc/redirect.php', dirname(__FILE__) ),
					'code' => $this->cred['code'],
					'grant_type' => 'authorization_code', 
				)
			);
		}

		// send an HTTPS POST request to the url
		$response = wp_remote_post( $url, $post_args );

		// is the post request successful?
		if( is_wp_error( $response ) ) {
			die();
		}
			
		// decode the JSON string in 'body' into a php 'stdClass' object
		return json_decode( $response['body'] );
	}
}

?>