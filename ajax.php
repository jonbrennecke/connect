<?php

namespace social;

/**
 * AJAX function to retrieve the relevant form fields for a specific section
 */
function get_settings_fields() {
	
	if( isset( $_POST['sectionName'] ) && !empty( $_POST['sectionName'] ) && validate_section( $_POST['sectionName'] ) ) {

		$tooltips = array( 
			"twitter" => array(
				0 => "To connect Wordpress with Twitter, <em>API keys</em> are used to authenticate requests for your tweets. API keys are basically like passwords that are safer to transfer over the web.",
				1 => "To begin, you will need to register your Wordpress website with Twitter. Open <a target='_blank' href='https://apps.twitter.com/'>this link</a> to do that, then click on 'Create New App'.",
				2 => "You're almost done, just copy and paste the keys into the fields below and click <em>'Save'</em>."
			),
			"facebook" => array(
				0 => "To connect Wordpress with Facebook, <em>API keys</em> are used to authenticate requests for your tweets. API keys are basically like passwords that are safer to transfer over the web.",
				1 => "To begin, you will need to register your Wordpress website with Facebook. Open <a target='_blank' href='https://apps.twitter.com/'>this link</a> to do that, then click on 'Create New App'.",
				2 => "You're almost done, just copy and paste the keys into the fields below and click <em>'Save'</em>."
			),
			"google-plus" => array(
				0 => "To connect Wordpress with Facebook, <em>API keys</em> are used to authenticate requests for your tweets. API keys are basically like passwords that are safer to transfer over the web.",
				1 => "To begin, you will need to register your Wordpress website with Facebook. Open <a target='_blank' href='https://apps.twitter.com/'>this link</a> to do that, then click on 'Create New App'.",
				2 => "When Instagram prompts you to enter a \"OAuth Redirect URI\", paste in this link <em>" . plugins_url( '/social/inc/redirect.php?s=google-plus', dirname(__FILE__) ) . "</em>",
				3 => "You're almost done, just copy and paste the keys into the fields below and click <em>'Save'</em>."
			),
			"instagram" => array(
				0 => "To connect Wordpress with Instagram, <em>API keys</em> are used to authenticate requests for your tweets. API keys are basically like passwords that are safer to transfer over the web.",
				1 => "To begin, you will need to register your Wordpress website with Instagram. Open <a target='_blank' href='http://instagram.com/developer/'>this link</a> to do that, then click on 'Register Your Application'.",
				2 => "When Instagram prompts you to enter a \"OAuth Redirect URI\", paste in this link <em>" . plugins_url( '/social/inc/redirect.php', dirname(__FILE__) ) . "</em>",
				3 => "You're almost done, just copy and paste the keys into the fields below and click <em>'Save'</em>."
			)
		);

		?>
		<div id="api-tool-tip">
			<h1>Welcome</h1>
			<?php echo "<ul class='nav-dots'>" . str_repeat("<li></li>", count( $tooltips[ $_POST['sectionName'] ] ) ) . "</ul>"; ?>
			<div class="tool-tip_container">
				<div class="tool-tip_container_abs">
					<?php 

						//  echo the tool tip text (say that 10 times fast!)
						foreach ( $tooltips[ $_POST['sectionName'] ] as $key => $tip) {
							echo "<div class='tool-tip r'><p>{$tip}</p><span class='prev'></span><span class='next'></span>";
							if ( $key + 1 == count($tooltips[ $_POST['sectionName'] ]) ) {
								echo "<table class='form-table'>";
								get_inputs( $_POST['sectionName'] );
								echo "</table><p class='save'><input type='button' id='save' value='Save' /></p>";
							}
							echo "</div>";
						}

					?>
				</div>
			</div>
		</div>
	<?php
	}

	die();
}

/**
 *
 *
 */
function get_inputs( $section ){
	if ( validate_section( $section ) ) {
		do_settings_fields( 'social_settings_page', $section . '_section' );
	}
}

/**
 * A convenience method for validating the section name
 *
 */
function validate_section( $section ) {
	return in_array( $section, array( 
		'twitter', 
		'facebook', 
		'instagram', 
		'google-plus' 
	));
}


/**
 * Retrieves the user's personal profile by calling one of several 
 * profile generating functions.
 *
 */
function get_profile() {
	
	if( isset( $_POST['sectionName'] ) && !empty( $_POST['sectionName'] ) ) {

		switch ( $_POST['sectionName'] ) {

			case 'twitter':
				twitter_profile();
				break;

			case 'facebook':
				// TODO
				break;

			case 'google-plus':
				// TODO
				break;

			case 'instagram':
				// TODO
				break;
			
			default:
				break;
		}
	}
	die();
}

/**
 * Retrieves a JSON string of the user's profile
 *
 */
function twitter_profile(){

	require_once( 'inc/twitter-api.php' );

	// create a new Twitter_API object (in the global namespace)
	$twitter = new \Twitter_API( array(
		'api-key' => get_option( 'twitter-api-key' ),
		'api-secret' => get_option( 'twitter-api-secret' ),
		'access-token' => get_option( 'twitter-access-token' ),
		'access-secret' => get_option( 'twitter-access-secret' )
	));

	// get the users profile
	// TODO fix this to use either an option or correctly use 'verify_credential'
	$user = $twitter->user( 'jonbrennecke' );

	echo json_encode($user);
}

/**
 * Save form fields by decoding the serialized form
 *
 */
function save_settings_fields() {
	
	if( isset( $_POST['data'] ) && !empty( $_POST['data'] ) && is_array( $_POST['data'] ) 
		&& array_key_exists('form', $_POST['data']) && array_key_exists('section', $_POST['data']) ) {
			
		// convert the jQuery serialized array to a PHP array
		$params = array();
		parse_str( $_POST['data']['form'], $params );

		// if the serialized form data matches $wp_settings_fields, then update the options in the database

		global $wp_settings_fields;

		$section = $_POST['data']['section'] . '_section';

		if ( array_key_exists( $section, $wp_settings_fields['social_settings_page'] ) ) {
			foreach ($params as $option => $value) {
				if( array_key_exists($option, $wp_settings_fields['social_settings_page'][ $section ] ) ) {
					
					// update the option in the WP database
					// returns true if the option has changed
					if ( update_option( $option, $value ) ) {
						login_url( $_POST['data']['section'] );
					}
				}
			}
		}

	}
	die();
}

/**
 *
 * In the (explicit) OAuth 2.0 flow, the user is redirected to a page where he/she may choose
 * whether or not to consent to the application's request for access to the user's data.
 * This function returns the login URL for each service account
 *
 * @param ( string ) $section - name of the service account
 * @return ( string ) URI to which the authentication server will redirect the user
 * once the user consents to the requested api privileges
 *
 */
function login_url( $section ) {
	switch ( $section ) {

		case 'twitter':

			/**
			* Twitter uses OAuth 1.0 for authentication, and doesn't require a login page
			*
			*/

			break;

		case 'facebook':

			require_once( 'inc/facebook-widget.php' );

			$widget = new \Facebook_Widget();
			echo $widget->login_redirect();

			break;

		case 'google-plus':

			require_once( 'inc/google-widget.php' );

			/**
			 * Google's API requires a special value, called a 'state' token to be created on the server 
			 * and passed to the API endpoint for retrieving a login URL.  When google redirects from the
			 * login page, the state token is passed back to our redirect URI where it is compared against 
			 * the option stored in the WP database
			 *
			 */

			// create a state token (or 'CSRF' code)
			$state = md5( rand() );

			// and save it to the database
			update_option( 'gooogle-csrf-code', $state );

			$widget = new \GPlus_Widget();
			echo $widget->login_redirect();
			
			break;

		case 'instagram':

			require_once( 'inc/instagram-widget.php' );

			$widget = new \Instagram_Widget();
			echo $widget->login_redirect();
			break;
		
		default:
			break;
	}
}


?>