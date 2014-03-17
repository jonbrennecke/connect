<?php 

/**
 * Twitter Widget Class
 *
 * @package WordPress
 * @subpackage Social
 * @since Social 1.0
 */


// include the api class
require_once 'twitter-api.php';

class Twitter_Widget extends WP_Widget {

	 // Twitter_API instance (@see definition in 'twitter-api.php')
	var $twitter_api;

	/**
	 * Register widget with WordPress.
	 */

	public function __construct() {

		$widget_ops = array('description' => __( "Twitter Widget" ) );
		parent::__construct('twitter', __('Twitter Widget'), $widget_ops);

		// establish an api connection to twitter with user provided credentials from the WP database 
		$this->twitter_api = new Twitter_API( array(
			'api-key' => get_option( 'twitter-api-key' ),
			'api-secret' => get_option( 'twitter-api-secret' ),
			'access-token' => get_option( 'twitter-access-token' ),
			'access-secret' => get_option( 'twitter-access-secret' )
		));
	}

	/**
	 * Front-end display of widget.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */

	public function widget( $args, $instance ) {

		$instance = wp_parse_args( (array) $instance, array( 'screen_name' => 'twitterapi', 'count' => 1, 'class_name' => 'social-wrap' ) );

		$tweets = $this->twitter_api->get_user_timeline( array( 
			'count' => $instance['count'],
			'screen_name' => $instance['screen_name']
		));

		echo "<div class=\"{$instance['class_name']} twitter\">";

		$this->do_profile_pic( $tweets[0]->user->profile_image_url );

		$this->do_tweet_text( $tweets[0]->text, $tweets[0]->user, $tweets[0]->entities ); 

		$this->do_user_info_bar( $tweets[0]->user );
			
		echo '</div>';
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */

	public function form( $instance ) {
	
		$instance = wp_parse_args( (array) $instance, array( 'screen_name' => 'twitterapi', 'count' => 1, 'class_name' => 'social-wrap' ) );

		?>

		<!-- screen name -->
		<p><label for="<?php echo $this->get_field_id('screen_name'); ?>"><?php _e('Twitter Handle:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('screen_name'); ?>" name="<?php echo $this->get_field_name('screen_name'); ?>" type="text" value="<?php echo $instance['screen_name']; ?>" /></p>

		<!-- tweet count -->
		<p><label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Count:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo $instance['count']; ?>" /></p>

		<!-- custom CSS class name -->
		<p><label for="<?php echo $this->get_field_id('class_name'); ?>"><?php _e('Custom CSS Class Name (Advanced) :'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('class_name'); ?>" name="<?php echo $this->get_field_name('class_name'); ?>" type="text" value="<?php echo $instance['class_name']; ?>" /></p>

		<?php

	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// remove @ sign
		$instance['screen_name'] = preg_replace('/@/i', '', $new_instance['screen_name'] );

		// ensure that 'count' is numeric
		if ( @is_numeric( $new_instance['count'] ) ) {
			$instance['count'] = intval( $new_instance['count'] );
		} 
		else {
			$instance['count'] = 1;
		}

		$instance['class_name'] = $new_instance['class_name'];
		
		return $instance;
	}

	/**
	* sanitize the given tweet text by replacing urls, hashtags ('#') 
	* and user-mentions ('@') with html hyperlinks.
	*
	*/

	private function sanitize_tweet( $text = null, $entities = null ) {
		$text = $this->replace_urls( $text );
		$text = $this->replace_hashtags( $text, $entities->hashtags );
		$text = $this->replace_mentions( $text, $entities->user_mentions );
		return $text; 
	}

	/**
	* replace hashtags with hyperlinks
	*
	*/

	private function replace_hashtags( $text = null, $hashtags = null ) {

		// replace each hashtag
		foreach ($hashtags as $key => $hashtag ) {
			$text = preg_replace("/#$hashtag->text/i", 
				"<a href=\"https://twitter.com/search?q=%23$hashtag->text&src=hash\" target=\"_blank\">#$hashtag->text</a>", $text);
		}
		return $text; 
	}


	/**
	* replace user mentions with hyperlinks
	*
	*/

	private function replace_mentions( $text = null, $mentions = null ) {

		// replace each user mention
		foreach ($mentions as $key => $mention ) {
			$text = preg_replace("/@$mention->screen_name/i", 
				"<a href=\"https://twitter.com/$mention->screen_name\" target=\"_blank\">@$mention->screen_name</a>", $text);
		}
		return $text; 
	}


	/**
	 * Replaces a string like 'http://www.google.com/' with an HTML hyperlink, eg. <a href="http://www.google.com/">
	 * The inner text/html and the closing '</a>' are not appended.
	 *
	 * based on a Stack Exchange Q/A in the following thread.
	 * @see http://stackoverflow.com/questions/17854971/preg-replace-to-replace-string-for-matching-url
	 */
	private function replace_urls( $text = null ) {
		$regex  = '/((http|ftp|https):\/\/)?[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?/';
		return preg_replace_callback( $regex, function( $m ) {
			$link = $name = $m[0];
			if ( empty( $m[1] ) ) {
				$link = "http://" . $link;
			}
			return "<a href={$link} target=\"_blank\" rel=\"nofollow\">{$name}</a>";
		}, $text );
	}

	/**
	* abbreviates a string to '$length' and, if the string is longer than '$length', inserts a elipsis at the end
	*
	*/

	private function abbrev_str( $text = null, $length ) {
		$str = substr( $text, 0, $length );
		$str .= strlen( $text ) < $length ? '' : '...';
		return $str; 
	}

	/**
	* echos the user's profile picture as an img html tag
	* 
	*/

	private function do_profile_pic( $image_url = null ) {

		// see if a larger size of the image exists
		if ( @getimagesize( preg_replace('/_normal/i', '', $image_url ) ) ) {
			$image_url = preg_replace('/_normal/i', '', $image_url );
		}

		echo "<div class='profile_pic_wrap'><img class='profile_pic' src={$image_url} /></div>";

	}

	/**
	* sanitize and insert markup in the tweet text; echo html
	* 
	*/

	private function do_tweet_text( $text = null, $user = null, $entities = null ) {

		// replace urls in the title with hyperlinks
		$text = $this->sanitize_tweet( $text, $entities );

		echo "<div class='tweet_text_wrap'><h2 class='tweet_text'>{$text}";
		echo "<span class='attrib'> — {$user->name}, <a href={$user->url}>@{$user->screen_name}</a></span></h2></div>";
	}


	/**
	* echo the user info bar with information about the users followers and tweet statistics.
	* 
	*/

	private function do_user_info_bar( $user = null ) {
		?>
		<ul class="social-menu-bar">
			<li class="location"><span class="marker"></span><?php echo $user->location; ?></li>
			<li><span class="number"><?php echo $user->friends_count; ?></span> <span class="label">Following</span></li>
			<li><span class="number"><?php echo $user->followers_count; ?></span> <span class="label">Followers</span></li>
			<li><span class="number"><?php echo $user->statuses_count; ?></span> <span class="label">Tweets</span></li>
		</ul>
		<?php
	}
		
} // end class Twitter-Widget

?>