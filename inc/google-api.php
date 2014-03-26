<?php

// set_include_path("../src/");
// require_once 'google-api-php-client/src/Google/Client.php';
// require_once 'google-api-php-client/src/Google/Service/Urlshortener.php';

class GPlus_API {

	// default credentials
	var $cred = array(
		'client-id' => '',
		'client-secret' => '',
		'csrf' => '',
		'code' => ''
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

	public function login_redirect() {

		$url = "https://accounts.google.com/o/oauth2/auth";
		$url .= "?" . http_build_query(array(
			"client_id" => $this->cred["client-id"],
			"scope" => "openid email", 
			"response_type" => "code",
			"access_type" => "offline",
			"redirect_uri" => plugins_url( '/inc/redirect.php?s=google-plus', dirname(__FILE__) ),
			"state" => $this->cred['csrf']
		));

		return $url;

	}

	public function get_access_token() {

		// arguments for wp_remote_post
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
				'redirect_uri' => plugins_url( '/inc/redirect.php?s=google-plus', dirname(__FILE__) ),
				'code' => $this->cred['code'],
				'grant_type' => 'authorization_code', 
			)
		);
		
		var_dump($post_args);

		// send an HTTPS POST request to Google
		$response = wp_remote_post( 'https://accounts.google.com/o/oauth2/token', $post_args );

		return $response;
	}


	/**
	 * Cross-Site Request Forgery tokens
	 *
	 * @see https://developers.google.com/+/web/signin/server-side-flow
	 */

	private function csrf_token() {

		// // Create a state token to prevent request forgery.
		// // Store it in the session for later validation.
		// $state = md5( rand() );
		// $app['session']->set('state', $state);

		// // Set the client ID, token state, and application name in the HTML while
		// // serving it.
		// return $app['twig']->render('index.html', array(
		// 	'CLIENT_ID' => CLIENT_ID,
		// 	'STATE' => $state,
		// 	'APPLICATION_NAME' => APPLICATION_NAME
		// ));


	}

}

?>