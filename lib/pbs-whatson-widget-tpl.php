<?php

function whatson_displayProgramAirdates($programAirdates, $programTitle, $instance, $episode_links) {
	global $options;

	if (count($episode_links != 0)) {
		$use_episode_links = TRUE;
	}

	$title = apply_filters('widget_title', $instance['title'] );  

	/* 6/3/13 Phil - Temporarily set $before_widget, $after_widget, $before_title, $after_title here until we figure out why they're not working on their own. */
	$before_widget = '<div id="pbs-whats-on" class="widget widget_listings"><div class="widget-wrap">';
	$after_widget = '</div></div>';
	$before_title = '<h3 class="widgettitle"><span>';
	$after_title = '</span></h3>';

	/* Before widget (defined by themes). */
	echo $before_widget;
	/* Display the widget title if one was input (before and after defined by themes). */
	if ( $title ) {
		echo $before_title . $title . $after_title;
	}

	if (count($programAirdates) != 0) {
		foreach ($programAirdates['upcoming_episodes'] as $upe) {
			if ($upe['feed']['digital_channel']) {
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
?>
			<ul class="listing">
				<li class="station"><strong>Station</strong>: <?php echo $airing_station; ?></li>
				<li class="airing"><strong>Time</strong>: <?php echo $airdate; ?> <?php echo $airtime ?><span id="whatson-timezone"> (<?php echo $timezone; ?>)</span></li>
				<li class="episode_title"><strong>Episode Title</strong>: <?php echo $episode_title; ?></li>
			</ul>
<?php
			}
		}
		unset($upe);
	} else {
?>
			<ul class="listing">
				<li><a href="http://www.kqed.org/tv/programs/index.jsp?pgmid=21385"  target="_blank">Get KQED broadcast listings.</a></li>
				<li><a href="http://www.pbs.org/tv_schedules/" onclick="javascript:_gaq.push(['_trackEvent','outbound-widget','http://www.pbs.org']);" target="_blank">Check your local listings.</a></li>
				<li><a href="http://video.kqed.org/program/film-school-shorts/"  target="_blank">Watch exclusive short films in their original cut.</a></li>
			</ul>
<?php
	}

	if (strtolower($options['show_zipcode_field']) == 'true') {
?>
			<form id="zipsearchform" method="get" action="#">
				<h4>Find listings for a different location:</h4>
				<input type="text" value="Enter Zip Code" onfocus="if (this.value == 'Enter Zip Code') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Enter Zip Code';}" size="18" maxlength="5" name="zlf" id="zipsearchfield">
				<input type="submit" value="Submit" id="zipsearchsubmit">
			</form>
<?php 
	}
	echo $after_widget;
}
?>