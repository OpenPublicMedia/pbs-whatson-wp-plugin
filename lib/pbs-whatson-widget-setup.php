<?php
	
		/* Widget settings. */
		$widget_ops = array( 
			'classname' => 'pbswhatson', 
			'description' => 'A widget that displays the localized program airtimes.' );

		/* Widget control settings. */
		$control_ops = array('id_base' => 'pbs-whatson-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'pbs-whatson-widget', PBS_WHATSON_NAME, $widget_ops, $control_ops );
	