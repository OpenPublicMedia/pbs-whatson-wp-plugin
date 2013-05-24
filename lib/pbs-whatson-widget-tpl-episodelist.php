<?php

function whatson_displayProgramAirdates($programAirdates, $programTitle, $instance, $episode_links) {
	global $options;
echo 'this is the whatson_displayProgramAirdates function<br/>';
var_dump($programAirdates);
	if (count($episode_links != 0)) {
		$use_episode_links = TRUE;
	}

	$title = apply_filters('widget_title', $instance['title'] );  
	/* Before widget (defined by themes). */
	echo $before_widget;
	/* Display the widget title if one was input (before and after defined by themes). */
	if ( $title ) {
		echo $before_title . $title . $after_title;
	}
	
	$episode_list = array();

	if (count($programAirdates) != 0) {
		foreach ($programAirdates['upcoming_episodes'] as $upe) {
			/** check to be sure it is a digital channel as there are analog channels in the API */
 			if ($upe['feed']['digital_channel']) {
				if ($upe['episode_title'] != '') {
					/** check to see if we have already stored this episode */
					$episode_title = $upe['episode_title'];
					if (array_key_exists($episode_title, $episode_list)) {
						/** we have seen this episode already so let's add to the airings array */
						$episode_airings_airings[count($episode_airings)]['airing_station'] = $upe['feed']['airing_station'];
						$episode_airings[count($episode_airings)]['airdate'] = $upe['airdate'];
						$episode_airings[count($episode_airings)]['airtime'] = $upe['airtime'];
						$episode_airings[count($episode_airings)]['timezone'] = $upe['airTimezone'];
						$episode_list[$episode_title] = $episode_airings[count($episode_airings)];
					} else {
						$episode_airings = array();
						$episode_airings[0]['airing_station'] = $upe['feed']['airing_station'];
						$episode_airings[0]['airdate'] = $upe['airdate'];
						$episode_airings[0]['airtime'] = $upe['airtime'];
						$episode_airings[0]['timezone'] = $upe['airTimezone'];
						$episode_list[$episode_title] = $episode_airings[0];
//						echo 'havent seen this episode before '.$episode_title.' so we just created an array of goodies</br>';
					}
				} else {
					echo 'episode_title is EMPTY which sucks for this process<br/>';
				}
/*				
				$airing_station = $upe['feed']['airing_station'];
				$airdate = $upe['airdate'];
				$airtime = $upe['airtime'];
				$timezone = $upe['airTimezone'];
				if ($upe['episode_title'] != '') {
					$episode_title = $upe['episode_title'];
					if ($use_episode_links) {
						if ($episode_links[strtolower($episode_title)]) {
							$episode_title = '<a href="'.$episode_links[strtolower($episode_title)].'">'.$episode_title.'</a>';
						}
					}
				} else {
					$episode_title = "TBD";
				}
*/
?>
			<ul id="whatson">
				<li class="station"><strong>Station</strong>: <?php echo $airing_station; ?></li>
				<li class="airing"><strong>Time</strong>: <?php echo $airdate; ?> <?php echo $airtime ?><span id="whatson-timezone">(<?php echo $timezone; ?>)</span></li>
				<li class="episode_title"><strong>Episode Title</strong>: <?php echo $episode_title; ?></li>
			</ul>
<?php
			}
		}
		unset($upe);
	} else {
?>
			<ul id="whatson">
				<li><a href="http://www.kqed.org/tv/programs/index.jsp?pgmid=21385"  target="_blank">Get KQED broadcast listings.</a></li>
				<li><a href="http://www.pbs.org/tv_schedules/" onclick="javascript:_gaq.push(['_trackEvent','outbound-widget','http://www.pbs.org']);" target="_blank">Check your local listings.</a></li>
				<li><a href="http://video.kqed.org/program/film-school-shorts/"  target="_blank">Watch exclusive short films in their original cut.</a></li>
			</ul>
<?php
	}
	var_dump($episode_list);
?>
			<form id="zipsearchform" method="get" action="#"><input type="text" value="Enter Zip Code" onfocus="if (this.value == 'Enter Zip Code') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Enter Zip code';}" size="18" maxlength="5" name="zlf" id="zipsearchfield">
			<input type="submit" value="Lookup Zip" id="zipsearchsubmit"></form>
<?php 
	echo $after_widget;
}
?>