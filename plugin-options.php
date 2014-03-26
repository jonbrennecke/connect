<?php

/**
 * Plugin Name: Social
 * Plugin URI: http://jonbrennecke.github.io/
 * Version: v1.00
 * Author: <a href="http://jonbrennecke.github.io/">Jon Brennecke</a>
 * Description: Social
 *
 *
 *
 * @package WordPress
 * @subpackage Social
 * @since Social 1.0
 *
 */

namespace social;

require_once 'ajax.php';


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
		'google-plus' => array(
			'google-app-id' => 'Client ID Email',
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

?>