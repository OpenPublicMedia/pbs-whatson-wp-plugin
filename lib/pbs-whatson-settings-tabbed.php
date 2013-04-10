<?php
//
/* begin plugin settings */
//

add_action('admin_init', 'pbs_whatson_options_init' );
add_action('admin_menu', 'pbs_whatson_options_add_page');

/**
* Init plugin options to white list our options
*
*
*
* @param pbs_whatson_options = A settings group name. Must exist prior to the register_setting call.
* @param pbs_whatson_settings = The name of the options that we store.
*/


/////// http://wp.tutsplus.com/tutorials/the-complete-guide-to-the-wordpress-settings-api-part-2-sections-fields-and-settings/ 
function pbs_whatson_options_init(){
	register_setting( 'pbs_whatson_options', 'pbs_whatson_settings' );
	// First, we register a section. This is necessary since all future options must belong to one.  
    add_settings_section(  
        'pbs_whatson_options_basic',         	// ID used to identify this section and with which to register options  
        'PBS What\'s On Options',               // Title to be displayed on the administration page  
        'pbs_whatson_general_options_callback', // Callback used to render the description of the section  
        'pbs_whatson_options_display'          	// Page on which to add this section of options  
    );
    // Next, we will introduce the fields for toggling the visibility of content elements.  
	add_settings_field(   
    'show_header',                      // ID used to identify the field throughout the theme  
    'Header',                           // The label to the left of the option interface element  
    'pbs_whatson_general_options_callback',   // The name of the function responsible for rendering the option interface  
    'pbs_whatson_options_display',                          // The page on which this option will be displayed  
    'pbs_whatson_options_basic',         // The name of the section to which this field belongs  
    array(                              // The array of arguments to pass to the callback. In this case, just a description.  
        'Activate this setting to display the header.'  
    )  
);  
	
	
}
/* ------------------------------------------------------------------------ * 
 * Section Callbacks 
 * ------------------------------------------------------------------------ */   
  
/** 
 * This function provides a simple description for the General Options page.  
 * 
 * It is called from the 'pbs_whatson_options_init' function by being passed as a parameter 
 * in the add_settings_section function. 
 */  
function pbs_whatson_general_options_callback() {  
    echo '<p>Select which areas of content you wish to display.</p>';  
} // end sandbox_general_options_callback  
   

// Add menu page
function pbs_whatson_options_add_page() {
    if (current_user_can('manage_options')) {
		add_options_page('PBS Whats\'s On Options', 'PBS What\'s On', 'manage_options', 'pbs-whatson-options', 'pbs_whatson_options_display');
    }
}

// Draw the menu page itself
function pbs_whatson_options_display() {

?>

	<div class="wrap">
		<h2>PBS What's On Options</h2>
		<?php  
		if( isset( $_GET[ 'tab' ] ) ) {  
			$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'default_options';  
		} // end if  
		?>  		
		<h2 class="nav-tab-wrapper">  
            <a href="?page=pbs_whatson_options&tab=default_options" class="nav-tab <?php echo $active_tab == 'default_options' ? 'nav-tab-active' : ''; ?>">Default Options</a>  
            <a href="?page=pbs_whatson_options&tab=editor" class="nav-tab <?php echo $active_tab == 'editor' ? 'nav-tab-active' : ''; ?>">Editor</a>
            <a href="?page=pbs_whatson_options&tab=test_options" class="nav-tab <?php echo $active_tab == 'test_options' ? 'nav-tab-active' : ''; ?>">Test Stations</a>  
        </h2>  
		<form method="post" action="options.php">
			<?php

				if( $active_tab == 'display_options' ) {  
					settings_fields('pbs_whatson_options_basic');
					do_settings_sections('pbs_whatson_options_basic');
				} else if ($active_tab == 'editor') {
					settings_fields('pbs_whatson_options_editor');
					do_settings_sections('pbs_whatson_options_editor');
				} else {
					settings_fields('pbs_whatson_options_testing');
					do_settings_sections('pbs_whatson_options_testing');
				}
			?>
		
		<?php submit_button(); ?>

		</form>			



			<?php $options = get_option('pbs_whatson_settings'); ?>
                <fieldset>
                <table id="pbs-whatson-options">
                <tr>
                <td class="content">
                <div class="options-liquid-left" style="clear:left; float:left; margin-right:-425px; width:100%;">
                <div class="options-left" style="margin-left:5px; margin-right:425px;">

			<table width="100%" class="form-table">
				<tr valign="top"><th scope="row">API Key</th>
					<td><input name="pbs_whatson_settings[api_key]" type="text" size="60" value="<?php echo $options['api_key']; ?>" />
					<p>Your assigned PBS TV Schedules API key. (click <a href="http://open.pbs.org/tools/pbs-api-key-request/" target="_blank">here</a> to request a key)</p>
					</td>
				</tr>
				<tr valign="top"><th scope="row">Cache TTL</th>
					<td><input name="pbs_whatson_settings[cache_ttl]" type="text" size="6" value="<?php echo $options['cache_ttl']; ?>" />
					<p>Time (in seconds) that an API request remains valid. 600 recomended.</p>
					</td>
				</tr>
				<tr valign="top"><th scope="row">Default Program ID</th>
					<td><input name="pbs_whatson_settings[default_program]" type="text" size="10" value="<?php echo $options['default_program']; ?>" />
					<p>The PBS SODOR Program ID to use by default when requesting airdates and times.</p>
					</td>
				</tr>
				<tr valign="top"><th scope="row">Test Station</th>
					<td><input name="pbs_whatson_settings[test_station]" type="text" size="10" value="<?php echo $options['test_station']; ?>" />
					<p>Entering a Test Station allows you to see what airdates are available for other stations within the system</p>
					</td>
				</tr>
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
                </div>
                </div>
                <div class="options-liquid-right" style="clear:right; float:right; width:400px;">
                <div class="options-right" style="margin: 0 auto; width: 385px;">

				<?php pbs_whatson_admin_display(); ?>
                </div>
                </div>

                </td>
        </tr>
        </table>
        </fieldset>

	</div>
<?php

}

?>
