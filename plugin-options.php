<?php

/**
 * Plugin Name: Social
 * Plugin URI: http://jonbrennecke.github.io/
 * Version: v1.00
 * Author: <a href="http://jonbrennecke.github.io/">Jon Brennecke</a>
 * Description: Social
 *
 * @package WordPress
 * @subpackage Social
 * @since Social 1.0
 *
 */

namespace social;


/**
 * create the content of the admin-side plugin options page
 */
function create_plugin_page() {
	?>
	<div class="wrap">
		<form class="social" method="post" action="">				
			<div class="social-sections">
				<div class="section titles">
					<?php do_settings_sections_title( 'social_settings_page' ); ?>
				</div>
				<div class="section fields">
					<ul class="bg">
						<li class="bg top"></li>
						<li class="bg mid">
							<div class="profile_pic">
								<div id="profile_pic"></div>
							</div>
							<div class="profile_info">
								<h1 id="profile_name"></h1>
								<h2 id="profile_status"></h2>
							</div>
							<div class="profile_stats"></div>
						</li>
						<li class="bg bottom"></li>
					</ul>
				</div>
			</div>
		</form>
	</div>
	<?php
}

/**
 * Basically the same as the WP function 'do_settings_section' from 'wp-admin/includes/template.php'
 * but only prints the section title, not section contents
 */
function do_settings_sections_title( $page ) {
	global $wp_settings_sections;

	foreach ( (array) $wp_settings_sections[$page] as $section ) {
		if ( $section['title'] ) {
			echo "<h3>{$section['title']}</h3>\n";
		}
	}
}

/**
 * register the settings sections
 */
function register_options() {

	add_settings_section(
		'facebook_section',
		'<span class="fa-facebook"></span>',
		null,
		'social_settings_page'
	);

	add_settings_section(
		'twitter_section',
		'<span class="fa-twitter"></span>',
		null,
		'social_settings_page'
	);

	add_settings_section(
		'google_section',
		'<span class="fa-google-plus"></span>',
		null,
		'social_settings_page'
	);

	add_settings_section(
		'instagram',
		'<span class="fa-instagram"></span>',
		null,
		'social_settings_page'
	);

	// sections and input fields
	$sections = array( 
		'twitter' => array(
			'twitter-api-key' => 'API Key',
			'twitter-api-secret' => 'API Secret'
		),
		'facebook' => array(
			'facebook-app-id' => 'App ID',
			'facebook-app-secret' => 'App Secret'
		),
		'instagram' => array(
			'instagram-id' => 'Client ID',
			'instagram-secret' => 'Client Secret'
		),
		'google' => array(
			'google-app-id' => 'Client ID',
			'google-app-secret' => 'Client Secret'
		)
	);

	foreach ($sections as $section => $fields) {
		foreach ($fields as $id => $title) {

			// register the field with WP
			register_setting( "{$section}_settings", $id );

			// and create a settings field
			add_settings_field( $id, $title, 'social\input_callback', 'social_settings_page', 
				"{$section}_section", array( 'id' => $id, 'value' => get_option( $id ) ) 
			);
		}
	}
}

/**
 *
 */
function input_callback( $args ){
	printf('<input type="text" spellcheck="false" id="%s" name="%s" value="%s" />', $args['id'], $args['id'], $args['value']);
}

/**
 * AJAX function to retrieve the relevant form fields for a specific section
 */
function get_settings_fields() {
	
	if( isset( $_POST['sectionName'] ) && !empty( $_POST['sectionName'] ) && validate_section( $_POST['sectionName'] ) ) {

		$tooltips = array( array(
				0 => "To connect Wordpress with Twitter, <em>API keys</em> are used to authenticate requests for your tweets. API keys are basically like passwords that are safer to transfer over the web.",
				1 => "To begin, you will need to register your Wordpress website with Twitter. Open <a target='_blank' href='https://apps.twitter.com/'>this link</a> to do that, then click on 'Create New App'.",
				2 => "You're almost done, just copy and paste the keys into the fields below and click <em>'Save'</em>."
			),
			"facebook" => array(
				0 => "To connect Wordpress with Facebook, <em>API keys</em> are used to authenticate requests for your tweets. API keys are basically like passwords that are safer to transfer over the web.",
				1 => "To begin, you will need to register your Wordpress website with Facebook. Open <a target='_blank' href='https://apps.twitter.com/'>this link</a> to do that, then click on 'Create New App'.",
				2 => "You're almost done, just copy and paste the keys into the fields below and click <em>'Save'</em>."
			),
			"google" => array(
			),
			"instagram" => array(
				0 => "To connect Wordpress with Instagram, <em>API keys</em> are used to authenticate requests for your tweets. API keys are basically like passwords that are safer to transfer over the web.",
				1 => "To begin, you will need to register your Wordpress website with Instagram. Open <a target='_blank' href='http://instagram.com/developer/'>this link</a> to do that, then click on 'Register Your Application'.",
				2 => "You're almost done, just copy and paste the keys into the fields below and click <em>'Save'</em>."
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
						foreach ($tooltips[ $_POST['sectionName'] ] as $key => $tip) {
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

function get_inputs( $section ){
	if ( validate_section( $section ) ) {
		do_settings_fields( 'social_settings_page', $section . '_section' );
	}
}

function validate_section( $section ) {
	return in_array( $section, array( 
		'twitter', 
		'facebook', 
		'instagram', 
		'google' 
	));
}


/**
 * AJAX function to retrieve the user's personal profile
 */
function get_profile() {
	
	if( isset( $_POST['sectionName'] ) && !empty( $_POST['sectionName'] ) ) {

		switch ( $_POST['sectionName'] ) {

			case 'twitter':
				twitter_profile();
				break;

			case 'facebook':
				break;

			case 'google':
				break;

			case 'instagram':
				break;
			
			default:
				break;
		}
	}
	die();
}

/**
 * AJAX function to retrieve a JSON string of the user's profile
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
 * AJAX function to save form fields
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
						login_redirect( $_POST['data']['section'] );
					}
				}
			}
		}

	}
	die();
}

function login_redirect( $section ) {
	switch ( $section ) {

		case 'twitter':
			break;

		case 'facebook':

			require_once( 'inc/facebook-widget.php' );

			$widget = new \Facebook_Widget();
			echo $widget->login_redirect();

			break;

		case 'google':
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