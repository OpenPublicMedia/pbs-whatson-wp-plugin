<?php

function pbs_whatson_admin_display () {

    global $default_program, $api_key, $test_station, $test_zipcode, $test_ipaddress;
	$stationList = array();
		
	$sodor = new PBSSodorAPI($api_key);

	/** get the admin's IP address **/
	$myIP = $sodor->getIPAddress();
	echo 'Your IP is '.$myIP.'<br/><br/>';
	/** check and see if an admin user is testing by verifying the test IP matches the user's IP **/
	if ($test_zipcode != '' && $test_ipaddress == $myIP) {
		echo 'Using Test ZIP: '.$test_zipcode.'<br/>';
		$sodor->setUserZipCode($test_zipcode);
		/** verify that userZipCode has been set properly */
		$myZip = $sodor->userZipCode;
	} else {
		$myZip = $sodor->getZipByIP();
	}
	if ($myZip !='') {
		echo 'Detected ZIP is '.$myZip.'<br/><br/>';
	} else {
		if ($test_ipaddress != '' && $test_ipaddress != $myIP) {
			echo 'Test IP Address does not match your IP address<br/>';
		} else {
			echo 'Unable to determine ZIP based on IP<br/>';
		}
	}
	$myStations = $sodor->getStationList();

	$programAirdates = $sodor->getProgramAirdates(TRUE);
	$sodor->closeCURL();
	
	if ($test_station != '' && $test_ipaddress == $myIP) {
		echo 'Test station: '.strtoupper($test_station).'<br/><br/>';
	} else {
		foreach ($myStations as $station) {
			echo 'Available Station: '.$station['flagship'].'<br/>';
		}
		unset($station);
	}
	echo '<br/>';
	if (count($programAirdates) != 0) {
		foreach ($programAirdates['upcoming_episodes'] as $upe) {
			if ($upe['feed']['digital_channel']) {
				echo '<b>PBS Station:</b> '.$upe['feed']['airing_station'].'<br/>';
				echo '<b>Time:</b> '.$upe['airdate'].' '.$upe['airtime'].' ('.$upe['airTimezone'].')<br/>';
				if ($upe['episode_title'] != '') {
					echo '<b>Episode Title:</b> '.$upe['episode_title'].'<br/>';
				} else {
					echo '<b>Episode Title:</b> No Available Title<br/>';
			
				}
				if ($upe['episode_description'] != '') {
					echo '<b>Episode Description:</b>'.$upe['episode_description'].'<br/>';
				} else {
					echo '<b>Episode Description:</b> No Available Description<br/>';
				}
				echo('<b>Duration:</b> '.$upe['minutes'].' minutes<br/><br/>');
	//			echo('Broadcast on <b>'.$upe['feed']['full_name'].'</b><br/>');
	//			echo('Channel:</b> '.$upe['feed']['digital_channel'].'<br/><br/>');
			}
		}
		unset($upe);
	} else {
		echo 'No episodes are airing in your area';	
	}
}

