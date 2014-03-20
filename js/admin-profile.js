var social = social || { META : 'social namespace' };

jQuery(document).ready( function () {
	( function ( $ ) {

		/**
		 * create a user profile from the server's response
		 *
		 */
		social.Profile = function ( json ) {

			if ( json ) {
				this.user = JSON.parse( json );
			}
			else {
				this.image = PATH + "/images/llama.jpg";
				this.name = "Llama McWordpress";
				this.status = 'Connect your accounts by clicking one of the icons to the left!';
			}
		};

		social.Profile.prototype = {

			// getter accessors

			get status () { return this.statusText; },

			get image () { return this.img.src; },

			get name () { return this.nameText; },

			// setter accessors

			set status ( text ) {
				this.statusText = text;
				$("#profile_status").html( text );
			}, 
			
			set image ( url ) {
				this.img = new Image();
				this.img.src = url; 
				$("#profile_pic").empty().append( this.img );
			},

			set name ( name ) {
				this.nameText = name;
				$("#profile_name").text( name );
			}

		};

		/**
		 * create a user profile based on the Twitter API's response,
		 * 
		 * inherits from 'social.Profile'
		 *
		 */
		social.TwitterProfile = function ( json ) {
			social.Profile.call( this, json );

			this.getImage();
			this.getStatus();
			this.getName();
		};

		social.TwitterProfile.prototype = Object.create( social.Profile.prototype );

		social.TwitterProfile.prototype.getImage = function () {
			if (!this.user.default_profile_image ) {
				try {
					// the default profile image returned from the Twitter API is tiny.
					// A larger one usually exists, so look for it instead, and then default
					// to the small image if unsucessful
					this.image = this.user.profile_image_url.replace(/_normal/,''); 
				}
				catch (err) {
					this.image = this.user.profile_image_url;
				}
			}
		}

		social.TwitterProfile.prototype.getStatus = function(){

			var status = this.user.status;

			// loop through the hashtags, symbols, urls and user_mentions and replace them with hyperlinks
			for ( var entity in status.entities ) {
				
				// the entities are stored as an array
				if ( status.entities[entity].length ) {
					for (var j=0; j< status.entities[entity].length; j++ ) {

						var token = status.entities[entity][j], re;

						switch ( entity ) {
							case 'user_mentions' :
								re = new RegExp( '@' + token.screen_name );
								status.text = status.text.replace( re, "<a href='https://twitter.com/account/redirect_by_id/" + token.id_str + "' >@" + token.screen_name + "</a>");
								break;

							case 'symbols' :
								re = new RegExp( '[$]' + token.text );
								status.text = status.text.replace( re, "<a href='https://twitter.com/search?q=%24" + token.text + "&src=ctag'>$" + token.text + "</a>");
								break;

							case 'hashtags' :
								re = new RegExp( '#' + token.text );
								status.text = status.text.replace( re, "<a href='https://twitter.com/search?q=%23" + token.text + "&src=hash'>#" + token.text + "</a>");
								break;

							case 'urls' :
								re = new RegExp( token.url );
								status.text = status.text.replace( re, "<a href='" + token.url + "'>" + token.display_url + "</a>");
								break;

							default :
								// default to the raw text
								break;
						}
					}
				}
			}

			this.status = status.text;
		}

		social.TwitterProfile.prototype.getName = function ( ) {
			this.name = this.user.name;
		}

	})( jQuery );
});