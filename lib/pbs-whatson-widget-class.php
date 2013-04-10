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
		global $programAirdates;
		
		extract( $args );
		$title = apply_filters('widget_title', $instance['title'] );  

		/* Before widget (defined by themes). */
		echo $before_widget;

		echo '<div id="text-5" class="widget whatson">';
		echo '<div class="widget-wrap">';
		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		
		$programAirdates = pbs_whatson_getlistings();
//		var_dump($programAirdates);
		pbs_whatson_widget_display();		  
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