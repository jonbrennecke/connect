<?php

/**
 * Plugin Name: Social
 * Plugin URI: http://jonbrennecke.github.io/
 * Version: v1.00
 * Author: <a href="http://jonbrennecke.github.io/">Jon Brennecke</a>
 * Description: UI elements for social networking 
 *
 * @package WordPress
 * @subpackage Dimension
 * @since Dimension 1.0
 *
 */

namespace social;

// Escape if accessed directly
if(!function_exists('add_action')){
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

require_once dirname( __FILE__ ) . '\functions.php';

add_actions();


?>