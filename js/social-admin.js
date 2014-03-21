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
	
	// wrap everything in an anonymous function and call immediately
	// this allows us to safely use variable '$' for jQuery in 'noConflict' mode
	( function ( $ ) {

		// default profile
		var profile = new social.Profile();

		var form = $('form.social'),
			h3 = form.find('div.section.titles h3 span'),
			activeSection;

		// onclick actions for Twitter/Facebook/Google+
		h3.click( function ( e ) {

			target = e.target.className.split('fa-')[1];

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
							var profile = new social.TwitterProfile( json );
						}
					});

					// add the fields to the 'section-fields' div elements
					form.find('div.section.fields ul.bg li.bottom')
						.show()
						.append( html )

					var dots = $("#api-tool-tip").find("ul.nav-dots li"),
						tips = $("#api-tool-tip").find("div.tool-tip_container div.tool-tip");
					
					function swap( index ){

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
					}

					swap(0);

					dots.click( function ( e ) {
						swap( dots.index(this) );
					});

					// 
					form.find('div.tool-tip table input')
						.focus( function () {
							$(this).parent().parent().find('th').addClass('th-focus',250);
						})
						.blur( function () {
							$(this).parent().parent().find('th').removeClass('th-focus',250);
						});
				}
			});
		}); // end onclick actions

	})( jQuery );
});