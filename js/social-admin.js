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

jQuery(document).ready( function () {
	
	// wrap everything in an anonymous function and call immediately
	// this allows us to safely use variable '$' for jQuery in 'noConflict' mode
	( function ( $ ) {

		var activeSection;

		// onclick actions for Twitter/Facebook/Google+
		$('.wrap form.social h3 span').click( function ( e ) {

			activeSection = e.target;

			// animate sliding the spans out at an incremental delay
			$('.wrap form.social h3 span')
				.each( function ( i ) {
					$(this)
						.delay( i * 100 )
						.animate({'left' : '-=100%'}, { duration : 300, easing : 'easeInQuad' });
				})
				.promise().done( function(){ // wait til after that animation has finished

					// change background-color to match the color of the clicked element
					$('div.social_sections')
						.animate( { 'backgroundColor' : window.getComputedStyle( e.target ).backgroundColor }, { duration : 600 } )

					// get the matching settings section
					$.ajax({
						type : 'post',
						url : ajaxurl,
						data : { action : 'get_settings_fields', sectionName : e.target.className  },
						dataType : 'html',
						success : function ( html ) {

							// add the fields to the 'div.settings_fields'
							$('div.social_sections div.settings_fields')
								.show()
								.append( html )
								.find('tr')
								.each( function ( i ) {

									// animate each row sliding in at an incremental delay
									$(this)
										.delay( i * 100 )
										.animate( { left : '0%' }, { duration : 300, easing : 'easeInOutQuad' } );

								});
						},
						error : function () {
							console.error( 'AJAX request to server failed.' );
						}
					});
				});
		}); // end onclick actions for Twitter/Facebook/Google+

		// on form change
		$('.wrap form.social').on('change', function(){

			// animate submit button
			$(this)
				.find('p.save')
				.animate( { height : '4em'}, { duration : 200, easing : 'easeInQuad' } );

		});

		// form submit
		$('.wrap form.social p.save').on('click', function(){

			$.ajax({
				type : 'post',
				url : ajaxurl,
				data : { 
					action : 'save_settings_fields', 
					data : { 
						form : $('.wrap form.social').serialize(),
						section : activeSection.className
					}  
				},
				dataType : 'html',
				success : function ( response ) {

					// if successfully saved, the servers responds '1'
					// otherwise the response from the server is blank
					if ( response ) {

						// make the save button green
						$('input#save')
							.animate( { 'backgroundColor' : '#00a651' }, { duration : 200 } )
							.attr( 'value', 'Saved' )
							.parent()
							.delay( 1000 )
							.animate( { height : 0 }, { duration : 300, easing : 'easeInQuad' } )
							.promise()
							.done( function(){
								$('input#save').css('backgroundColor','#222').attr('value','Save');
							});
							
					}
				},
				error : function () {
					console.error( 'AJAX request to server failed.' );
				}
			});

		});

	})( jQuery );
});