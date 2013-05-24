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
function pbs_whatson_options_init(){
	register_setting( 'pbs_whatson_options', 'pbs_whatson_settings' );
}

// Add menu page
function pbs_whatson_options_add_page() {
    global $whatson_settings;
    
    if (current_user_can('manage_options')) {
		$whatson_settings = add_options_page('PBS Whats\'s On Options', 'PBS What\'s On', 'manage_options', 'pbs-whatson-options', 'pbs_whatson_options_page');
    }
}

// Draw the menu page itself
function pbs_whatson_options_page() {

?>

	<div class="wrap">
		<h2>PBS What's On Options</h2>

		<form method="post" action="options.php">
			<?php settings_fields('pbs_whatson_options'); ?>
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
				<tr valign="top"><th scope="row">Test IP Address</th>
					<td><input name="pbs_whatson_settings[test_ipaddress]" type="text" size="10" value="<?php echo $options['test_ipaddress']; ?>" />
					<p>In order to use the 'Test Station' or 'Test ZIP' functionality, you must enter your IP address here</p>
					</td>
				</tr>
<!-- test station was here -->
				<tr valign="top"><th scope="row">Test ZIP</th>
					<td><input name="pbs_whatson_settings[test_zipcode]" type="text" size="10" value="<?php echo $options['test_zipcode']; ?>" />
					<p>Entering a Test ZIP code allows you to see what airdates are available for regions within the U.S.</p>
					</td>
				</tr>
				<tr valign="top"><th scope="row">Show ZIP Code Field</th>
					<td><input name="pbs_whatson_settings[show_zipcode_field]" type="text" size="10" value="<?php echo $options['show_zipcode_field']; ?>" />
					<p>Enter TRUE to show the ZIP code field. This is largely a testing field for when we introduce the ZIP code field</p>
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
		</form>
                </td>
        </tr>
        </table>
        </fieldset>

	</div>
<?php

}
/*
function pbs_whatson_options_load_scripts($hook) {
	global $whatson_settings;
	if ($hook != $whatson_settings) {
		return;
	}
	wp_enqueue_script('pbswo-ajax', 'http://crenshawed.com/wp-content/plugins/pbs-whatson-wp-plugin/lib/assets/js/pbs-whatson.js', array('jQuery'));

}
add_action('admin_enqueue_scripts', 'pbs_whatson_options_load_scripts');
*/

?>
