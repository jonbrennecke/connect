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

"use strict";

var social = social || { META : 'social namespace' };

jQuery(document).ready( function () {
	( function ( $ ) {
		
		/**
		 * To individually transform text (eg rotating letters on a circle) we might need 
		 * to apply css styles to each letter individually. So this functions takes the innerText
		 * of an element and replaces it with a span around each character.
		 *
		 * this could be accomplished by lettering.js, but I'd rather not include 
		 * a whole library for one function
		 */

		$.fn.spans = function() {
			return $( this ).each( function(){
				var text = $( this ).text(), 
					spans = "";

				for (var i=0;i<text.length;i++){
					spans += "<span class='char" + ( i + 1 ) + "'>" + text[i] + "</span>";
				} 

				$( this ).html( spans );
			});
		}

		

		$('div.italia').find('h2.info_name').spans();




	})( jQuery );
});