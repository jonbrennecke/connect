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
						},
						error : ajaxError
					});

					// add the fields to the 'section-fields' div elements
					form.find('div.section.fields ul.bg li.bottom')
						.show()
						.append( html )
						.find('tr')
						.each( function ( i ) {

							// animate each row sliding in at an incremental delay
							$(this)
								.delay( i * 100 )
								.animate( { left : '0%' }, { duration : 300, easing : 'easeInOutQuad' } );
						});

					form.find('div.section.fields input')
						.focus( function () {
							$(this).parent().parent().find('th').addClass('th-focus',250);
						})
						.blur( function () {
							$(this).parent().parent().find('th').removeClass('th-focus',250);
						});
				},
				error : ajaxError
			});
		}); // end onclick actions for Twitter/Facebook/Google+

		// // on form change
		// $('.wrap form.social').on('change', function(){

		// 	// animate submit button
		// 	$(this)
		// 		.find('p.save')
		// 		.animate( { height : '4em'}, { duration : 200, easing : 'easeInQuad' } );

		// });

		// // form submit
		// $('.wrap form.social p.save').on('click', function(){

		// 	$.ajax({
		// 		type : 'post',
		// 		url : ajaxurl,
		// 		data : { 
		// 			action : 'save_settings_fields', 
		// 			data : { 
		// 				form : $('.wrap form.social').serialize(),
		// 				section : activeSection.className
		// 			}  
		// 		},
		// 		dataType : 'html',
		// 		success : function ( response ) {

		// 			// if successfully saved, the servers responds '1'
		// 			// otherwise the response from the server is blank
		// 			if ( response ) {

		// 				// make the save button green
		// 				$('input#save')
		// 					.animate( { 'backgroundColor' : '#00a651' }, { duration : 200 } )
		// 					.attr( 'value', 'Saved' )
		// 					.parent()
		// 					.delay( 1000 )
		// 					.animate( { height : 0 }, { duration : 300, easing : 'easeInQuad' } )
		// 					.promise()
		// 					.done( function(){
		// 						$('input#save').css('backgroundColor','#222').attr('value','Save');
		// 					});
							
		// 			}
		// 		},
		// 		error : function () {
		// 			console.error( 'AJAX request to server failed.' );
		// 		}
		// 	});

		// });

	})( jQuery );
});

function ajaxError( msg ){
	console.log( msg );
}



