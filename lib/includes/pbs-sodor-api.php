<?php

/**
 * This class implements the PBS SODOR (Locator and TV Schedules v2) API
 * The usage is straightforward:
 *
 *    $api_key = <Your TVSS API KEY>

 *    $requestor = new COVE_API_Request($api_key);
 *    $json = $requestor->makeRequest("http://services.pbs.org/callsigns/zip/ZIPCODE.json'");
 * 	  
 * URLs in use
 *    Get Stations By ZIP - http://services.pbs.org/callsigns/zip/ZIPCODE.json
 *    Get ZIP code by IP Address - http://services.pbs.org/zipcodes/ip/IPADDRESS.json
 *    Get Program - http://services.pbs.org/tvss/STATION/upcoming/program/PROGRAMID
 *
 * requires PHP version >= 5.2.0 for validating IP address
 *
 *
 */
class PBSSodorAPI
{
    protected $api_key,
    		  $apiURL = 'http://services.pbs.org/',
    		  $cURLHandle;

	private $jArray = Array(),
		    $availStations;
		    
	public $defaultStationList = Array(),
		   $objStationsArray = Array(),
		   $stationList = Array(),
		   $rawProgramData = Array(),
		   $prettyProgramData = Array(),
		   $programData = Array(),
		   $userIP,
		   $userZipCode,
		   $objZipCode,
		   $objStations,
		   $zipToStationURL,
   		   $bUsingDefaultStation = FALSE,
   		   $bUsingTestSTation = FALSE;

    /**
     * Class can be constructed with our without passing in API key
     *
     * @param string $api_key
     */
    public function __construct($api_key=null) {
        if ($api_key) {
            $this->setAPIKey($api_key);
        }
    }
    
    /**
     * This function can be used to change the credentials without
     * creating a new object
     *
     * @param string $api_key
     */
    public function setAPIKey($api_key) {
        $this->m_api_key = $api_key;
    }
    /**
     *
     * This function can be used to change the ZIP code without
     * creating a new object
     *
     * @param string $zipcode
     */     
    public function setUserZipCode($zipcode) {
    	$this->userZipCode = $zipcode;
    }

/** Locator Functions Start Here */
/** --------------------------------------------------------------------------------------- */
	/** 
	 * protected function setIPAddress
	 * 
	 * Function attempts to automatically determine the users public IP address
	 * from Environment and _SERVER parameters
	 *
	 * No SODOR API call necessary to get user's IP address
	 *
	 * 
	 * @return string $userIP (that has been validated or set to 0.0.0.0)
	 */
	protected function setIPAddress() {
//		echo '<strong>setIPAddress called</strong><br/>';
		if (isSet($_SERVER)) {
			if (isSet($_SERVER["HTTP_X_FORWARDED_FOR"])) {
				$this->userIP = $_SERVER["HTTP_X_FORWARDED_FOR"];
			} elseif (isSet($_SERVER["HTTP_CLIENT_IP"])) {
				$this->userIP = $_SERVER["HTTP_CLIENT_IP"];
			} else {
				$this->userIP = $_SERVER["REMOTE_ADDR"];
			}
		} else {
			if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
				$this->userIP = getenv( 'HTTP_X_FORWARDED_FOR' );
			} elseif ( getenv( 'HTTP_CLIENT_IP' ) ) {
				$this->userIP = getenv( 'HTTP_CLIENT_IP' );
			} else {
				$this->userIP = getenv( 'REMOTE_ADDR' );
			}
		}
		if ($this->validateIPAddress($this->userIP)) {
//			echo '<strong>this is '.$this->userIP.'</strong><br/>';
			return $this->userIP;
		} else {
			$this->userIP = "0.0.0.0";
		}
	}
	/**
	 * public function getIPAddress
	 * 
	 * Makes request to setter function for user's IP address
	 * 
	 * @return string userIP
	 */
	public function getIPAddress() {
//		echo '<strong>getIPAddress called</strong><br/>';
		if(!$this->userIP) {
			$this->setIPAddress();
		}
		return $this->userIP;
	}
/** --------------------------------------------------------------------------------------- */

/** --------------------------------------------------------------------------------------- */
	/**
 	 * Makes request to PBS SODOR API for all available stations in a given IP address
	 *
	 * SODOR API key is not required for this request
	 *
	 *
	 * @return Array of station objects based on user IP addres
	 */
	protected function setZipByIP() {
		$requestURL = $this->apiURL.'zipcodes/ip/'.$this->userIP.'.json';
		
		$data = $this->makeCURLRequest($requestURL, TRUE);

		if (is_object($data)) {
			$this->objZipCode = $data;
		}
		if (strtolower($this->objZipCode->{'$elements'}) == 'zipcode') {
			$this->userZipCode = $this->objZipCode->{'$items'}[0]->zipcode;
			$this->zipToStationURL = $this->objZipCode->{'$items'}[0]->{'$links'}[0]->{'$self'};
		}
//		$this->userZipCode = 94598;
		return $this->userZipCode;
	}

	/**
	 * public function getIPAddress
	 * 
	 * Makes request to setter function for user's IP address
	 * 
	 * @return string userIP
	 */
	public function getZipByIP() {
		if($this->userIP) {
			if (!$this->userZIP) {
				$this->setZipByIP();
			}
		} else {
			$this->getIPAddress();
			$this->setZipByIP();
		}
		if ($this->validateZIPCode($this->userZipCode)) {
			return $this->userZipCode;
		} else {
			return '';
		}
	}
/** --------------------------------------------------------------------------------------- */

/** --------------------------------------------------------------------------------------- */
	/**
	 * protected function setStationsByZip
	 * 
	 * Makes request to PBS SODOR API for all available stations in a given ZIP code
	 *
	 * @return Array of station objects based on ZIP code
	 */
	protected function setStationsByZip() {

		$this->zipToStationURL = $this->apiURL.'callsigns/zip/'.$this->userZipCode.'.json';
		if (isset($this->zipToStationURL)) {
			$data = $this->makeCURLRequest($this->zipToStationURL, TRUE);
		} else {
			$data = $this->makeCURLRequest($requestURL, TRUE);
		}
		if (is_object($data) && strtolower($data->{'$elements'}) == 'callsign2zipmapping') {
			$this->objStationsArray = $data;
		} else {
			/** TODO -- add better error handling */
			if ($_GET['debug']) {
				echo 'there is a problem with the station objects array<br/><br/>';
			}
		}
		/** this->objStationsArray should now be set and available for use */
//		$this->debugArray($this->objStationsArray, 'Station Object Array (setStationsByZIP)');
	}

	/**
	 * public function getStationsByZip
	 * 
	 * Calls setStationsByZip if stationArray is not populated
	 *
	 * @return Array of station objects based on ZIP code
	 */
	public function getStationsByZip() {
		if (!$this->objStationsArray) {
			$this->setStationsByZip();
		}
		return $this->objStationsArray;
	}
/** --------------------------------------------------------------------------------------- */

/** --------------------------------------------------------------------------------------- */
	/**
	* protected function 
	* 
	* parses the stationList that is returned 
	* 
	* function to pull hardcoded array elements based on knowledge of JSON
	* 
	*/
	protected function setStationList() {
		global $test_station;

		/** if test station is set, create this->stationList */
		if ($test_station) {
			$stationArray = Array('flagship'=>strtoupper($test_station),'confidence'=>'100','rank'=>'1','short_common_name'=>strtoupper($test_station));
			$this->stationList = Array(strtoupper($test_station)=>$stationArray);
			$this->bUsingTestStation = TRUE;
			return;
		}

		if (!$this->objStationsArray) {
			$this->getStationsByZip();
		}
		// TODO: need additional error handling here in case the array is empty
		if (count($this->objStationsArray->{'$items'}) != 0) {
			foreach ($this->objStationsArray->{'$items'} as $callsign_map) {
				$callsign = $callsign_map->{'$links'}[0];
				$owner_station = $callsign->{'$links'}[0];		
				$owner_station_callsign = '';
				foreach($owner_station->{'$links'} as $rel) {
					if ($rel->{'$relationship'} == 'flagship') {
						$owner_station_callsign = $rel->{'callsign'};
					}
					if ($owner_station_callsign != '') {
						// Check to see if we've seen this station before
						if (array_key_exists($owner_station_callsign, $this->stationList)) {
							// we have seen this station before so reprocess it
							$station = $this->stationList[$owner_station_callsign];
							// Check to see if we've moved up the ranking
							if ($callsign_map->{'rank'} != '' && $station['rank'] > $callsign_map->{'rank'}) {
								// this callsign has a lower (better) ranking so use it
								$station['rank'] = $callsign_map['rank'];
							}
							if ($callsign_map->{'confidence'} != '' && $station['confidence'] < $callsign_map->{'confidence'}) {
								// this callsign has a higher (better) confidence so use it
								$station['confidence'] = $callsign_map->{'confidence'};
							}
							// add this callsign to the existing list for this owner station
							$station['callsigns'][]  = $callsign->{'callsign'};
							$this->stationList[$owner_station_callsign] = $station;
						} else {
							// create a new station entry and add it to the stations list
							$station = Array();
							$station['flagship'] = $owner_station_callsign;
							$station['confidence'] = $callsign_map->{'confidence'};
							$station['rank'] = $callsign_map->{'rank'};
							$station['short_common_name'] = $owner_station->{'short_common_name'};
							// get SODOR station ID from URL
							$station['id'] = $this->getIDFromURL($owner_station->{'$self'});
							$station['callsigns'][] = $callsign->{'callsign'};
							$this->stationList[$owner_station_callsign] = $station;
						}
					}
				}
				unset($rel);
			}
			unset($callsign_map);
		}
		return $this->stationList;
	}
	/**
	 * public function getStationList
	 * 
	 * Calls setStationList if stationList is not populated
	 *
	 * @return Array (multidimensional) stationList
	 *     owner_station_callsign - key to 2nd level array
	 *	   flagship				primary FCC license holder callsign
	 *     confidence			numeric value between 0 and 100
	 *     rank					Nielsen based sorting criteria
	 *     short_common_name	callsign for the flaghship station
	 *     id					SODOR station ID for the flagship
	 *     callsigns 			array of applicable callsigns for the given flagship
	 *     
	 */	
	 public function getStationList() {
		if (!$this->stationList) {
			$this->stationList = $this->setStationList();
		}
		return $this->stationList;
	}
/** --------------------------------------------------------------------------------------- */
/** Locator Functions End Here                                                              */
/** --------------------------------------------------------------------------------------- */

/** --------------------------------------------------------------------------------------- */
/** TV Schedules Functions Start Here                                                       */
/** --------------------------------------------------------------------------------------- */
	/**
	* protected function setProgramAirdates
	* 
	* 
	* 
	* 
	* 
	*/
	protected function setProgramAirdates($bDecodeJSON, $bDisplayAirdates) {
		// ex. http://services.pbs.org/tvss/weta/upcoming/program/752
		global $default_program;
		$this->defaultProgram = $default_program;
		
		$listingCount = 0;

		if (count($this->stationList) > 0) {
			foreach($this->stationList as $station) {
				$this->rawAirdatesData = $this->getAirdatesData($station['flagship'], $this->defaultProgram);
				if ($this->rawAirdatesData['upcoming_episodes'][0] != '') {
					$this->airdatesData = $this->formatAirdatesData($this->rawAirdatesData, $station['flagship']);
				}
				if ($bDisplayAirdates) {
					if ($listingCount > 0) {
						$this->displayAirdates($station['flagship'], $this->prettyAirdatesData, FALSE);
					} else {
						$this->displayAirdates($station['flagship'], $this->prettyAirdatesData);
					}
				}
				$listingCount++;
			}
			unset($station);		
		}
	}


	/**
	* public function getProgramAirdates
	* 
	* gets the program airdates 
	* 
	* function to pull hardcoded array elements based on knowledge of JSON
	* 
	* @param bool $decodeJSON - determines whether JSON is returned or not 
	* @param bool $displayAirdates - when set to TRUE, uses the classes built in display function
	* @return NULL
	*/
	public function getProgramAirdates($bDecodeJSON = FALSE, $bDisplayAirdates = FALSE, $bSortAirdates = FALSE) {
		if (!$this->programData) {
			$this->setProgramAirdates($bDecodeJSON, $bDisplayAirdates);
//			if (!$bSortAirdates) {
//				$this->sortAirdates();
//			}
		}
		return $this->airdatesData;
	}
    /**
     *
     *
     * 
     * 
     */     
	public function getAirdatesData($station, $programID) {
		// ex. http://services.pbs.org/tvss/weta/upcoming/program/752
		$requestURL = $this->apiURL.'tvss/'.$station.'/upcoming/program/'.$programID;
		$data = $this->makeRequest($requestURL, TRUE, TRUEf);
//		$data = $this->makeCURLRequest($requestURL,FALSE,TRUE);
//		$this->debugArray($data, 'Airdates Data');
		return $data;
	}

	public function sortAirdates($sortCriteria="date") {
		$airdatesUnSorted = $this->airdatesData;

//		for each ($airdatesUnSorted['upcoming_episodes'] as $x) {
		for ($x=0; $x < count($airdatesUnSorted['upcoming_episodes']); $x++) {
//			$this->debugArray($airdatesUnSorted['upcoming_episodes'][$x], 'unsorted episodes');
		}
	}
	
	public function formatAirdatesData($rawProgramData, $station = '') {
		// update the time code information for more user friendly display
		// desired output Time: Thursday, Mar 21 at 05:00 AM
		$count = 0;
		foreach($rawProgramData['upcoming_episodes'] as $l) {
			$rawAirdate = strtotime($l['day']);
			$airdate = date('l, M j', $rawAirdate); 
			$rawProgramData['upcoming_episodes'][$count]['airdate'] = $airdate;	

			$rawAirtime = strtotime($l['start_time']);
			$airtime = date('g:i A', $rawAirtime);
			$rawProgramData['upcoming_episodes'][$count]['airtime'] = $airtime;

			date_default_timezone_set($l['feed']['timezone']);
			$airTimezone = date('T');
			$rawProgramData['upcoming_episodes'][$count]['airTimezone'] = $airTimezone;		
			
			if ($station) {
				$rawProgramData['upcoming_episodes'][$count]['feed']['airing_station'] = $station;
			}
			$count++;
		}
	//	$this->debugArray($programData);
		return $rawProgramData;
	}
	
	public function displayAirdates($station, $programData, $useHeader=TRUE) {
		if ($useHeader) {
			echo("<div class='container'>");
			echo("<h1>Program: ".$programData['title']."</h1>");
			echo("<p class='lead'>".$programData['description']."</p>");
			echo("<h3>Upcoming Episodes in your area</h3>");
		}
		echo("<p><table class='table table-bordered table-condensed' border='1'>");
		echo("<tbody>");
		echo('<tr><td colspan="2">Broadcasting Station: '.$station.'</td></tr>');
		foreach($programData['upcoming_episodes'] as $upe) {
			echo("<tr><td><b>Time:</b> ".$upe['airdate']." ".$upe['airtime']." (".$upe['airTimezone'].")<br/>");
			if ($upe['episode_title'] != "") {
				echo("<b>Episode Title: </b>");
				//http://bluebell.open.pbs.org/show/episode_20396/KQED/
				echo("<a href='http://bluebell.open.pbs.org/show/".$upe['show_id']."/".$default_station."/'>".$upe['episode_title']."</a><br/>");
			}
			if ($upe['episode_description'] != "") {
				echo("<b>Episode Description:</b>".$upe['episode_description']."<br/>");
			}
			echo("<b>Duration:</b> ".$upe['minutes']." minutes<br/>");
			echo("Broadcast on <b>".$upe['feed']['full_name']."</b><br/>");
			if ($upe['feed']['analog_channel']) {
				echo("Channel ".$upe['feed']['analog_channel']." (analog)");
			} else {
				echo("Channel ".$upe['feed']['digital_channel']." (digital)");
			}
			echo "</td></tr>";
		} // end foreach(programdata)
		echo "</table>";
	//	debugArray($programData);
	} // end function displayProgramTimes
	


/** --------------------------------------------------------------------------------------- */
/** Service functions for validation and retreiving data                                    */
/** --------------------------------------------------------------------------------------- */
    /**
	 * user regular expression to validate ZIP code
	 *
	 * @param string $zipcode
	 * @return int $match
	 *     1 is match
	 *     0 is not a match
	 */
    public function validateZIPCode($zipcode) {
    	if ($zipcode == '') {
    		return 0;
    	}
    	$match = preg_match("#[0-9]{5}#", $zipcode);
    	return $match;
    }
	/**
	 * validates IP address using built-in PHP function (requires PHP >= 5.2.0)
	 *
	 * @param string $ip
	 * @return bool
	 *     TRUE if $ip is a valid external IP (e.g. no private IP addresses allowed)
	 *     FALSE if $ip is invalid, either because doesn't match the format or is a private IP    
	 */
	public function validateIPAddress($ip) {
		if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
			return TRUE;
		}
		else {
			return FALSE;	
		}
	}
	/**
	* takes a known URL pattern from the JSON and pulls the station id for later use
	*
	* Process description
	*     strrchr pulls the file name (e.g. 107.json)
	*     substr removes the '/'
	*     explode on the '.' to create an array
	*     extract the 0 level of the array
	* 
	* @param string $url
	* @return string $stationID or not a stationID
	*/
	public function getIDFromURL($url) {
		// check to be sure it is a URL and if not, return 'NONE'
		if(filter_var($url, FILTER_VALIDATE_URL) === FALSE)	{
			return 'not a stationID';
		} else {
			// if it is a URL, extract the ID
			// example http://services.pbs.org/station/107.json
			// get the characters between the last / and the .
			$rawStationID = explode('.', substr(strrchr($url, "/"), 1));
			$stationID = $rawStationID[0];
			return $stationID;
		}
	}    

	/**
	* pretty output of the passed in array
	* 
	* @param array $var
	* @param string $title
	* @return
	*/
	public function debugArray($var = false, $title = '') {
		echo "\n<pre style=\"background: #FFFF99; font-size: 10px;\">\n";
		if ($title) {
			echo '<h3>'.$title.'</h3>';
		}
		$var = print_r($var, true);
		echo $var . "\n</pre>\n";
	}
	private function objectToArray($object) {
	
	}
	
	private function arrayToOjbect($array) {
	
	}


 /** --------------------------------------------------------------------------------------- */
	/** 
	* Make request to PBS SODOR API
	*
	* Some proxies/firewalls and/or PHP configurations have problems using the
	* headers. However, if you are caching the API calls, it may be more advantageous to
	* utilize the header version.
	* 
	* Returns the JSON response
	* 
	* @param string $requestURL
	* @param bool $auth_using_headers
	* @return the JSON response
	*/
	private function makeRequest ($requestURL, $decodeJSON=FALSE, $authUsingHeaders=FALSE) {
		/** if the request needs to send the API Key in the headers */
		if ($authUsingHeaders) {
			/** Put the API Key into the HTTP headers */
			$opts = array(
				'http'=>array(
					'method'=>"GET",
					'header'=>"Accept-language: en\r\n" .
								"X-PBSAUTH: ". $this->m_api_key ."\r\n"
					)
				);
			$context = stream_context_create($opts);
			$jData = (file_get_contents($requestURL, FALSE, $context));       
		} else {
			$jData = file_get_contents($requestURL);
		}
		if ($jData == FALSE) {
			$jArray = json_decode('{"status": "Request failed."}');
			return $jArray;
		}
		if ($decodeJSON) {
//			echo 'decodeJSON is ' . $decodeJSON . '<br/><br/>';
			$result = json_decode($jData, TRUE);
		} else {
			$result = $jData;
		}
		return $result;

	}
	
	private function makeCURLRequest($requestURL, $decodeJSON=FALSE, $authUsingHeaders=FALSE) {
		// Open a curl session for making the call 
		// if cURL handle does not exist already, go ahead and open a cURL session
		if (!$this->cURLHandle) {
			$this->cURLHandle = curl_init();
			// Tell curl not to return headers, but do return the response 
			curl_setopt($this->cURLHandle, CURLOPT_HEADER, false); 
			curl_setopt($this->cURLHandle, CURLOPT_RETURNTRANSFER, true); 

			// Set the arguments that are passed to the server 
			if ($authUsingHeaders) {
				curl_setopt($this->cURLHandle, CURLOPT_HTTPHEADER, array(
					'X-PBSAUTH: ' . $this->m_api_key . '\r\n',
					'Accept-language: en\r\n',
					'Connection: Keep-Alive',
					'Keep-Alive: 300'
				));
			}
		} 

		// set the request URL for cURL
		curl_setopt($this->cURLHandle, CURLOPT_URL, $requestURL);

		// Make the REST call, returning the result 
		$myData = curl_exec( $this->cURLHandle ); 

		// Convert the result from JSON format to a PHP array 
		if ($decodeJSON) {
			$result = json_decode( $myData ); 
		} else {
			$result = $myData; 
		}
		return $result;
	}
	
	public function closeCURL(){
		if ( $this->cURLHandle ) {
			curl_close( $this->cURLHandle );
		}
	}
	
	/**
	 * 
	 * 20130406 - Function not in use but need to keep the code around to implement later
	 * 
	 * useDefaultStation
	 * 
	 * 
	 */	 	
	protected function useDefaultStation() {
		/** check to see if default station is set --> used in conjunction with user_default station to display a station */
		/** check to see if use_default_station is set -> this is used in case no stations are available, can show as default in place of "contact local station */
		/** check to see if we have available stations */
		/** No stations for a given ZIP but use_default_station is set */
		if ((count($this->stationList) == 0) && $use_default_station){
			if ($defaultStation) {
				$stationArray = Array('flagship'=>strtoupper($defaultStation),'confidence'=>'100','rank'=>'1','short_common_name'=>strtoupper($defaultStation));
				$this->stationList = Array(strtoupper($defaultStation)=>$stationArray);
			} else {
				/** request manual entry of ZIP code */
			}
			$this->bUsingDefaultStation = TRUE;
		}
	}
}

