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

var social = social || { META : 'social namespace' };

jQuery(document).ready( function () {
	( function ( $ ) {

		"use strict";

		if ( typeof PATH !== "undefined" ) {
			var profile = new social.Profile();
		}

			var form = $('form.social'),
				icons = form.find('div.section.titles h3 span'),
				activeSection;

		/**
		 * onclick actions for Twitter/Facebook etc 
		 *
		 */
		icons.click( function ( e ) {

			var target = e.target.className.split('fa-')[1];

			// get the html for the matching settings section
			$.ajax({
				type : 'post',
				url : ajaxurl,
				data : { action : 'get_settings_fields', sectionName : target  },
				dataType : 'html',
				success : function ( html ) {

					$.ajax({
						type : 'post',
						url : ajaxurl,
						data : { action : 'get_profile', sectionName : target  },
						dataType : 'html',
						success : function ( json ) {

							console.log(json);
							var profile = new social.TwitterProfile( json );
						}
					});

					// add the fields to the 'section-fields' div elements
					form.find('div.section.fields ul.bg li.bottom')
						.show()
						.append( html )

					var dots = $("#api-tool-tip").find("ul.nav-dots li"),
						tips = $("#api-tool-tip").find("div.tool-tip_container div.tool-tip"),
						swap;

					/**
					 * tooltip swap animation
					 *
					 */
					( swap = function ( index ){

						tips.filter(".c").addClass("l",500,"easeInOutQuad").removeClass("c");
						
						$( tips.get( index ) ).removeClass("l r").addClass("c", 500, "easeInOutQuad");

						$( dots ).removeClass( 'selected' );
						$( dots.get(index) ).addClass('selected',500);

						if ( index + 1 === tips.length ) {

							$( tips.get( index ) )
								.find('tr')
								.removeClass('c')
								.addClass('r')
								.each( function ( i ) {

									// animate each row sliding in at an incremental delay
									$(this).delay( i * 100 ).removeClass('r').addClass('c',1000,"easeInOutQuad");
								});
						}
					})(0);

					dots.click( function ( e ) {
						swap( dots.index(this) );
					});

					/**
					 * focus actions for API key inputs
					 * 
					 */
					form.find('div.tool-tip table input')
						.focus( function () {
							$(this).parent().parent().find('th').addClass('th-focus',250);
						})
						.blur( function () {
							$(this).parent().parent().find('th').removeClass('th-focus',250);
						});


					/**
					 * click action for the SAVE button
					 *
					 */
					$('#save').click( function ( e ) {

						$.ajax({
							type : 'post',
							url : ajaxurl,
							data : { 
								action : 'save_settings_fields', 
								data : { 'section' : target, 'form' : form.serialize() } 
							},
							success : function ( url ) {

								if ( url ) {

									/**
									 * TODO - OO in 'ConfirmWindow' 
									 *
									 */ 

							        var tab = window.open( url, "Confirm" );
							 		tab.focus();

								}
							}
						});

					});
				}
			});
		}); // end account icon onclick click actions

	})( jQuery );
});