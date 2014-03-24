<?php

/**
 * Twitter API Class
 *
 * @package WordPress
 * @subpackage Social
 * @since Social 1.0
 */

class Instagram_API {

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

	public function login_url( ) {

		// build url
		$url = 'https://instagram.com/oauth/authorize/?';
		$url .= http_build_query( array( 
			'client_id' => $this->cred['client-id'], 
			'redirect_uri' => 'http://' . $_SERVER['HTTP_HOST'],
			'response_type' => 'code' ) );

		return $url;
	}
}

?>