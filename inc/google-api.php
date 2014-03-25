<?php

// set_include_path("../src/");
// require_once 'google-api-php-client/src/Google/Client.php';
// require_once 'google-api-php-client/src/Google/Service/Urlshortener.php';

class GPlus_API {

	// default credentials
	var $cred = array(
		'client-id' => '',
		'client-secret' => ''
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