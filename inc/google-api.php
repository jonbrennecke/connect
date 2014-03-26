<?php


require_once 'oauth.php';

class GPlus_API extends OAuth {

	/**
	* @see 'oauth.php'
	*
	*/
	public function __construct( $cred = array() ) {
		parent::__construct( $cred );
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

		var_dump( $this->__get_access_token( 'https://accounts.google.com/o/oauth2/token', $post_args ) );
	}

}

?>