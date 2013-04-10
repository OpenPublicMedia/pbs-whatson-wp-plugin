<?php

function pbs_whatson_admin_display () {

    global $default_program, $api_key, $test_station;
	$stationList = array();
		
	$sodor = new PBSSodorAPI($api_key);

	if ('' == $test_station) {
		$myIP = $sodor->getIPAddress();
		$myZIP = $sodor->getZipByIP();
		echo 'IP is '.$myIP.'<br/>';
		echo 'ZIP is '.$myZIP.'<br/>';
	}
	$myStations = $sodor->getStationList();
	$programAirdates = $sodor->getProgramAirdates(TRUE);
	$sodor->closeCURL();
	
	if ($test_station) {
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
		echo 'No episodes are airing on your local stations';	
	}
}

