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
 *  register the admin-side plugin page
 */
function add_plugin_page() {
	add_plugins_page( 
		__('Social Plugin Options'), 
		'Social Plugin Options', 
		'edit_pages', 
		'social_settings_page', 
		'social\create_plugin_page' 
	);
}

/**
 * create the content of the admin-side plugin options page
 */
function create_plugin_page() {
	?>
		<div class="wrap">
			<?php screen_icon(); // TODO necessary? ?>
			<h2 class="page-title">Social Plugin Options</h2>
			<form class="social" method="post" action="">
				
				<?php settings_fields( 'twitter_settings' ); ?>
				
				<div class="social_sections">
				<h2>Connect your account...</h2>

				<?php do_settings_sections_title( 'social_settings_page' ); ?>
				
				<div class="settings_fields"></div></div>
				<p class="save"><input type="button" id="save" value="Save" /></p>
			</form>
		</div>
	<?php
}

/**
 * Basically the same as the WP function 'do_settings_section' from 'wp-admin/includes/template.php'
 * but only prints the section title, not section contents
 */
function do_settings_sections_title( $page ) {
	global $wp_settings_sections, $wp_settings_fields;

	foreach ( (array) $wp_settings_sections[$page] as $section ) {
		if ( $section['title'] )
			echo "<h3>{$section['title']}</h3>\n";
	}
}

/**
 * register the settings sections
 */
function register_options() {

	add_settings_section(
		'facebook_section',
		'<span class="facebook">Facebook</span>',
		null,
		'social_settings_page'
	);

	add_settings_section(
		'twitter_section',
		'<span class="twitter">Twitter</span>',
		null,
		'social_settings_page'
	);

	add_settings_section(
		'google_section',
		'<span class="google">Google +</span>',
		null,
		'social_settings_page'
	);

	// $fields = array( 
	// 	'twitter-api-key' => 'API Key',
	// 	'twitter-api-secret' => 'API Secret',
	// 	'twitter-access-token' => 'Access Token',
	// 	'twitter-access-secret' => 'Access Token Secret' );

	// foreach ($fields as $id => $title) {

	// 	// register the field with WP
	// 	register_setting( 'twitter_settings', $id );

	// 	// and create a settings field
	// 	add_settings_field( $id, $title, 'social\input_callback', 'social_settings_page', 
	// 		'twitter_section', array( 'id' => $id, 'value' => get_option( $id ) ) 
	// 	);
	// }

	$sections = array( 
		'twitter' => array(
			'twitter-api-key' => 'API Key',
			'twitter-api-secret' => 'API Secret',
			'twitter-access-token' => 'Access Token',
			'twitter-access-secret' => 'Access Token Secret'
		),
		'facebook' => array(
			'facebook-app-id' => 'App ID',
			'facebook-app-secret' => 'App Secret'
		),
		'google' => array(
			'google-app-id' => 'App ID',
			'google-app-secret' => 'App Secret'
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
	printf('<input type="text" id="%s" name="%s" value="%s" />', $args['id'], $args['id'], $args['value']);
}

/**
 * AJAX function to retrieve form fields for a specific section
 */
function get_settings_fields() {
	
	if( isset( $_POST['sectionName'] ) && !empty( $_POST['sectionName'] ) ) {

		switch ( $_POST['sectionName'] ) {

			case 'twitter':
				do_settings_fields( 'social_settings_page', 'twitter_section' );
				break;

			case 'facebook':
				do_settings_fields( 'social_settings_page', 'facebook_section' );
				break;

			case 'google':
				do_settings_fields( 'social_settings_page', 'google_section' );
				break;
			
			default:
				break;
		}
	}
	die();
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
						
						// finally, update the option in the WP database
						echo update_option( $option, $value );
					}
				}
			}
	}
	die();
}

?>