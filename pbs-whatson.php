<?php
/*
Plugin Name: PBS What's On
Plugin URI: http://github.com/openpublicmedia/pbs-whatson-wp-plugin/
Description: Implement basic air date and time information for a given program
Author: Thomas Crenshaw (Crenshawed Solutions) for KQED
Version: 1.0
Author URI: http://crenshawed.com
*/

/*  Copyright 2013  Thomas Crenshaw  (email : thomascrenshaw@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if (!function_exists ('add_action')){
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
/**
 * Register Globals
 **/
$plugin_loc = plugin_dir_url( __FILE__ );
$pluginName = 'PBS What\'s On';
$pluginShortName = 'pbs_whatson';
$the_web_url = home_url('/');
$the_site_name = get_bloginfo('name');
$the_default_email = get_bloginfo('admin_email');
if ( preg_match( '/^https/', $plugin_loc ) && !preg_match( '/^https/', home_url() ) )
	$plugin_loc = preg_replace( '/^https/', 'http', $plugin_loc );
$dataError = FALSE;
$dataErrorType = '';
$programAirdates = Array();

/**
 * Define Globals
 **/
define( 'PBS_WHATSON_FRONT_URL', 	$plugin_loc);
define( 'PBS_WHATSON_URL',          plugin_dir_url(__FILE__) );
define( 'PBS_WHATSON_PATH',         plugin_dir_path(__FILE__) );
define( 'PBS_WHATSON_BASENAME',     plugin_basename( __FILE__ ) );
define( 'PBS_WHATSON_WEB_URL',      $the_web_url );
define( 'PBS_WHATSON_NAME',         $pluginName );
define( 'PBS_WHATSON_S_NAME',       $pluginShortName );
define( 'PBS_WHATSON_DEFAULT_EMAIL',$the_default_email );
define( 'PBS_WHATSON_VERSION', 		'1.0.0' );
define( 'PBS_WHATSON_PREFIX' , 		'pbswo_');
define( 'PBS_WHATSON_DATA_ERROR',	'' );

/** 
 * include the necessary files for the plugin
 **/
define( 'PBSWHATSON_PATH', plugin_dir_path(__FILE__) );
require_once(PBSWHATSON_PATH.'lib/includes/pbs-sodor-api.php');
require_once(PBSWHATSON_PATH.'lib/pbs-whatson-settings.php');
require_once(PBSWHATSON_PATH.'lib/pbs-whatson-admin-class.php');
require_once(PBSWHATSON_PATH.'lib/pbs-whatson-widget-class.php');

/** set the options that are stored from the admin page */
$options = get_option('pbs_whatson_settings');
$api_key = $options['api_key'];
$cache_ttl = $options['cache_ttl'];
$default_program = $options['default_program'];
$test_station = $options['test_station'];
$test_zipcode = $options['test_zipcode'];
$test_ipaddress = $options['test_ipaddress'];
$show_zipcode_field = $options['show_zipcode_field'];

function pbs_whatson_getlistings () {
	global $api_key, $default_program, $test_station, $test_zipcode, $test_ipaddress;
    $sodor = new PBSSodorAPI($api_key);
	
//	echo 'test_ipaddress '.$test_ipaddress.'<br/>';
//	echo 'test_zipcode '.$test_zipcode.'<br/>';

	if ($test_zipcode == '' || $test_ipaddress == '') {
//		echo 'test_zipcode and test_ipaddress are empty, getting zip from sodor->getZipByIP<br/>';
		$myZip = $sodor->getZipByIP();
	} elseif ($test_ipaddress != '') {
//		echo 'test_ipaddress is empty, getting IP getIPAddress<br/>';
		$myIP = $sodor->getIPAddress();
//		echo'myIP is '.$myIP.'<br/>';
		$sodor->setUserZipCode($test_zipcode);
		$myZip = $sodor->userZipCode;
	}

	/** now that ZIP is set, lets find the stations for the ZIP and getAirdates */
	if ($myZip) {
		$myStations = $sodor->getStationList();

		if (count($myStations > 0)) {
			$programAirdates = $sodor->getProgramAirdates(TRUE);
			if (count($programAirdates) > 0) {
				return $programAirdates;
			} else {
				$dataError = TRUE;
				$dataErrorType = "NOLISTINGS";
				pbs_whatson_error_handling($dataErrorType);
			}
		} else {
			$dataError = TRUE;
			$dataErrorType = "NOSTATIONS";
			pbs_whatson_error_handling($dataErrorType);
		}
	} else {
		$dataError = TRUE;
		$dataErrorType = "NOZIP";
		pbs_whatson_error_handling($dataErrorType);
	}
	$sodor->closeCURL();
}

function pbs_whatson_error_handling($dataErrorType) {
//	echo 'dataErrorType is ' . $dataErrorType . '<br/><br/>';
//	PBS_WHATSON_DATA_ERROR = $dataErrorType;
}

function pbs_whatson_localize_viewer () {
	$sodor = new PBSSodorAPI();
	// take ZIP input from viewer
	// validate ZIP
	// if valid
	//		get stations
	//		set stations
	// else
	//		display error message regarding invalid ZIP code
	//

}

function pbs_whatson_display_airdates () {


}

function pbs_whatson_housekeeping () {

    global $api_key, $cache_ttl, $default_program, $default_station;

    $transient_key = 'sodor_api'.md5($api_key);

    $cache = get_site_transient($transient_key);

    if (isset($cache->{'results'})) {
        return $cache;
    }

    $sodor = new PBSSodorAPI($api_key);
	// 


    $json = $requestor->make_request($request_url);

    if ($json == FALSE)
    {
        $data = json_decode('{"status": "Request failed."}');
        return false;
    }

    $data = json_decode($json);

    $data->{'status'} = 'Last Updated: '.current_time('mysql');

    $cache_stat = set_site_transient($transient_key, $data, $cache_ttl);

    return $data;
}


/**
 * 
 * 
 * 
 * 
 * @return array - internal links to program/episode/chapter pages
 */
function pbs_whatson_getprogramlinks() {
	$filename = PBSWHATSON_PATH.'lib/assets/data/episodes.inc';
	if(is_file($filename)) {
		return include $filename;
	} else {
		return false;
	}
}
	
?>
