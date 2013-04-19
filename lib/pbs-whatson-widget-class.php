<?php
/**
 * Add function to widgets_init that'll load our widget.
 * 
 */
add_action( 'widgets_init', 'pbs_whatson_load_widgets' );

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function pbs_whatson_load_widgets() {
	register_widget( 'PBS_WhatsOn_Widget' );
}

/**
 * PBS Whats On Widget  class.
 * This class handles everything that needs to be handled with the widget
 * 
 *
 *
 */
class PBS_WhatsOn_Widget extends WP_Widget {

  /**	
   * Widget setup
   */
  function PBS_WhatsOn_Widget() {
    include PBSWHATSON_PATH.'lib/pbs-whatson-widget-setup.php';
  }

  /**
   * 
   * How to display the widget on the screen.
   * 
   */ 
   	function widget( $args, $instance ) {
		global $programAirdates,$programTitle;
		extract( $args );
		if ($options['show_program_name']) {
			$title = apply_filters('widget_title', $instance['title'].$programTitle );  
		} else {
			$title = apply_filters('widget_title', $instance['title'] );  
		}
		/* Before widget (defined by themes). */
		echo $before_widget;
		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		
		$programAirdates = pbs_whatson_getlistings();

		if (count($programAirdates) != 0) {
			foreach ($programAirdates['upcoming_episodes'] as $upe) {
				if ($upe['feed']['digital_channel']) {
					echo '<ul>';
					echo '<li>';
					echo '<strong>Station</strong>: '.$upe['feed']['airing_station'].'<br/>';
					echo '<strong>Date</strong>: '.$upe['airdate'].'<br/>';
					echo '<strong>Time:</strong> '.$upe['airtime'].' ('.$upe['airTimezone'].')<br/>';
					if ($upe['episode_title'] != '') {
						echo '<strong>Episode Title</strong>: '.$upe['episode_title'].'<br/>';
					} else {
						echo '<strong>Episode Title</strong>: TBD<br/>';
			
					}
					echo '</li>';
					echo '</ul>';
					/** not used
	//				if ($upe['episode_description'] != '') {
	//					echo '<b>Episode Description:</b>'.$upe['episode_description'].'<br/>';
	//				} else {
	//					echo '<b>Episode Description:</b> No Available Description<br/>';
	//				}
	//				echo('<b>Duration:</b> '.$upe['minutes'].' minutes<br/><br/>');
	//				echo('Broadcast on <b>'.$upe['feed']['full_name'].'</b><br/>');
	//				echo('Channel:</b> '.$upe['feed']['digital_channel'].'<br/><br/>');
					*/
				}
			}
			unset($upe);
		} else {
?>
			<ul>
				<li><a href="http://www.kqed.org/tv/programs/index.jsp?pgmid=21385"  target="_blank">Get KQED broadcast listings.</a></li>
				<li><a href="http://www.pbs.org/tv_schedules/" onclick="javascript:_gaq.push(['_trackEvent','outbound-widget','http://www.pbs.org']);" target="_blank">Check your local listings.</a></li>
				<li><a href="http://video.kqed.org/program/film-school-shorts/"  target="_blank">Watch exclusive short films in their original cut.</a></li>
			</ul>
<?php
		}
?>
		<form id="zipsearchform" method="get" action="#"><input type="text" value="Enter Zip Code" onfocus="if (this.value == 'Enter Zip Code') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Enter Zip code';}" size="18" maxlength="5" name="zlf" id="zipsearchfield">
		<input type="submit" value="Lookup Zip" id="zipsearchsubmit"></form>
<?php
		/* After widget (defined by themes). */
		echo $after_widget;
   	}
   	
   	/** 
	 * 	
	 * 	Update the widget settings.
	 */	
 	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		//Strip tags from title and name to remove HTML  
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}	 
	 
	/** Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {
		// Check values
		if( $instance) {
		     $title = esc_attr($instance['title']);
		} else {
		     $title = '';
		}
?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'wp_widget_plugin'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

<?php
	}
}
?>