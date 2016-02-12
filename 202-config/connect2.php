<?php

$version = '1.7.2';

@ini_set('auto_detect_line_endings', TRUE);
@ini_set('register_globals', 0);
@ini_set('display_errors', 'On');
@ini_set('error_reporting', 6135);
@ini_set('safe_mode', 'Off');

$_SERVER['HTTP_X_FORWARDED_FOR'] = $_SERVER['REMOTE_ADDR'];

include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/functions-auth.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config.php');

//our own die, that will display the them around the error message
function _die($message) { 

	//info_top();
	echo $message;
	//info_bottom();
	die();
}

//this funciton delays an SQL statement, puts in in a mysql table, to be cronjobed out every 5 minutes
function delay_sql($delayed_sql) {
	
	$mysql['delayed_sql'] = str_replace("'","''",$delayed_sql);
	$mysql['delayed_time'] = time();
	
	$delayed_sql="INSERT INTO  202_delayed_sqls 
					
					(
						delayed_sql ,
						delayed_time
					)
					
					VALUES 
					(
						'".$mysql['delayed_sql'] ."',
						'".$mysql['delayed_time']."'
					);";
	
	$delayed_result = _mysql_query($delayed_sql) ; //($delayed_sql);
}

function geoLocationDatabaseInstalled() { 
	
	$sql = "SELECT COUNT(*) FROM 202_locations";
	$result = _mysql_query($sql);
	$count = mysql_result($result, 0, 0); 
	if ($count != 161877) return false;
	
	$sql = "SELECT COUNT(*) FROM 202_locations_block";
	$result = _mysql_query($sql);
	$count = mysql_result($result, 0, 0); 
	if ($count != 1593228) return false;
	
	$sql = "SELECT COUNT(*) FROM 202_locations_city";
	$result = _mysql_query($sql);
	$count = mysql_result($result, 0, 0); 
	if ($count != 101332) return false;
	
	$sql = "SELECT COUNT(*) FROM 202_locations_coordinates";
	$result = _mysql_query($sql);
	$count = mysql_result($result, 0, 0);
	if ($count != 125204) return false;
	
	$sql = "SELECT COUNT(*) FROM 202_locations_country";
	$result = _mysql_query($sql);
	$count = mysql_result($result, 0, 0);
	if ($count != 235) return false;
	
	$sql = "SELECT COUNT(*) FROM 202_locations_region";
	$result = _mysql_query($sql);
	$count = mysql_result($result, 0, 0); 
	if ($count != 396) return false;
	
	#if no return false
	return true;	
}

function getLocationDatabasedOn() { 
	
	return false;
	
}
class FILTER {
	
	function startFilter($click_id, $ip_id, $ip_address, $user_id) {
		 
		//we only do the other checks, if the first ones have failed.
		//we will return the variable filter, if the $filter returns TRUE, when the click is inserted and recorded we will insert the new click already inserted,
		//what was lagign this query is before it would insert a click, then scan it and then update the click, the updating later on was lagging, now we will just insert and it will not stop the clicks from being redirected becuase of a slow update.
			
		//check the user
		$filter = FILTER::checkUserIP($click_id, $ip_id, $user_id);
		if ($filter == false) {
			
			//check the netrange  
			$filter = FILTER::checkNetrange($click_id, $ip_address);
			if ($filter == false) {  
			
				$filter = FILTER::checkLastIps($user_id, $ip_id);
				
				/*
				//check the configurations   
				$filter = FILTER::checkIPTiming($click_id, $ip_id, $user_id, $click_time, 1, 150); if ($filter == false) { 
				$filter = FILTER::checkIPTiming($click_id, $ip_id, $user_id, $click_time, 20, 3600); if ($filter == false) {  
				$filter = FILTER::checkIPTiming($click_id, $ip_id, $user_id, $click_time, 50, 86400); if ($filter == false) {  
				$filter = FILTER::checkIPTiming($click_id, $ip_id, $user_id, $click_time, 100, 2629743); if ($filter == false) {  
				$filter = FILTER::checkIPTiming($click_id, $ip_id, $user_id, $click_time, 1000, 7889231); if ($filter == false) {  
				}}}}}
				*/
			}
		}
		
		if ($filter == true) { 
			return 1;    
		} else { 
			return 0;    
		}
	}
	
	function checkUserIP($click_id, $ip_id, $user_id) {
	 
		$mysql['ip_id'] = mysql_real_escape_string($ip_id);      
		$mysql['user_id'] = mysql_real_escape_string($user_id);    
		
		$count_sql = "SELECT    COUNT(*) 
					  FROM      202_users 
					  WHERE     user_id='".$mysql['user_id']."' 
					  AND       user_last_login_ip_id='".$mysql['ip_id']."'";
		$count_result = _mysql_query($count_sql) ; //($count_sql);
	
		//if the click_id's ip address, is the same ip adddress of the click_id's owner's last logged in ip, filter this.  This means if the ip hit on the page was the same as the owner of the click affiliate program, we want to filter out the clicks by the owner when he/she  is trying to test 
		if (mysql_result($count_result,0,0) > 0) { 
			
			return true;  
		}
		return false;    
	}
	
	function checkNetrange($click_id, $ip_address) {
	
		$ip_address = ip2long($ip_address);
		
		//check each netrange
		/*google1 */ if (($ip_address >= 1208926208) and ($ip_address <= 1208942591)) { return true;  }
		/*MSN */ if (($ip_address >= 1093926912) and ($ip_address <= 1094189055)) { return true;  }
		/*google2 */ if (($ip_address >= 3512041472) and ($ip_address <= 3512074239)) { return true;  }
		/*Yahoo */ if (($ip_address >= 3640418304) and ($ip_address <= 3640426495)) { return true;  }
		/*google3 */ if (($ip_address >= 1123631104) and ($ip_address <= 1123639295)) { return true;  }
		/*level 3 communications */ if (($ip_address >= 1094189056) and ($ip_address <= 1094451199)) { return true;  }
		/*yahoo2 */ if (($ip_address >= 3515031552) and ($ip_address <= 3515039743)) { return true;  }
		/*Yahoo3 */ if (($ip_address >= 3633393664) and ($ip_address <= 3633397759)) { return true;  }
		/*Google5 */ if (($ip_address >= 1089052672) and ($ip_address <= 1089060863)) { return true;  }
		/*Yahoo */ if (($ip_address >= 1209925632) and ($ip_address <= 1209991167)) { return true;  }
		/*Yahoo */ if (($ip_address >= 1241907200) and ($ip_address <= 1241972735)) { return true;  }
		/*Performance Systems International Inc. */ if (($ip_address >= 637534208) and ($ip_address <= 654311423)) { return true;  }
		/*Microsoft */ if (($ip_address >= 3475898368) and ($ip_address <= 3475963903)) { return true;  }
		/*googleNew */ if (($ip_address >= -782925824) and ($ip_address <= -782893057)) { return true;  }
		
		//if it was none of theses, return false
		return false;           
	}  
	
	//this will filter out a click if it the IP WAS RECORDED, for a particular user within the last 24 hours, if it existed before, filter out this click.
	function checkLastIps($user_id, $ip_id) {

		$mysql['user_id'] = mysql_real_escape_string($user_id);
		$mysql['ip_id'] = mysql_real_escape_string($ip_id);
		
		$check_sql = "SELECT COUNT(*) AS count FROM 202_last_ips WHERE user_id='".$mysql['user_id']."' AND ip_id='".$mysql['ip_id']."'";
		$check_result = _mysql_query($check_sql) ; //($check_sql);
		$check_row = mysql_fetch_assoc($check_result);
		$count = $check_row['count'];
		 
		if ($count > 0) {
			//if this ip has been seen within the last 24 hours, filter it out. 
			return true;
		} else {
			
			//else if this ip has not been recorded, record it now
			$mysql['time'] = time();
			$insert_sql = "INSERT INTO 202_last_ips SET user_id='".$mysql['user_id']."', ip_id='".$mysql['ip_id']."', time='".$mysql['time']."'";
			$insert_result = _mysql_query($insert_sql) ; //($insert_sql);
			return false;	
		}
		
	}
	
}

function rotateTrackerUrl($tracker_row) { 
	
	if (!$tracker_row['aff_campaign_rotate']) return $tracker_row['aff_campaign_url'];
	
	$mysql['aff_campaign_id'] = mysql_real_escape_string($tracker_row['aff_campaign_id']);
	$urls = array();
	array_push($urls, $tracker_row['aff_campaign_url']);

	
	if ($tracker_row['aff_campaign_url_2']) array_push($urls, $tracker_row['aff_campaign_url_2']);
	if ($tracker_row['aff_campaign_url_3']) array_push($urls, $tracker_row['aff_campaign_url_3']);
	if ($tracker_row['aff_campaign_url_4']) array_push($urls, $tracker_row['aff_campaign_url_4']);
	if ($tracker_row['aff_campaign_url_5']) array_push($urls, $tracker_row['aff_campaign_url_5']);
	
	$count = count($urls);
	
	$sql5 = "SELECT rotation_num FROM 202_rotations WHERE aff_campaign_id='".$mysql['aff_campaign_id']."'";
	$result5 = _mysql_query($sql5);
	$row5 = mysql_fetch_assoc($result5);
	if ($row5) { 
		
		$old_num = $row5['rotation_num'];
		if ($old_num >= ($count - 1))		$num = 0;
		else 						$num = $old_num + 1;
		
		$mysql['num'] = mysql_real_escape_string($num);
		$sql5 = " UPDATE 202_rotations SET rotation_num='".$mysql['num']."' WHERE aff_campaign_id='".$mysql['aff_campaign_id']."'";
		$result5 = _mysql_query($sql5);
	
	} else {
		//insert the rotation
		$num = 0;
		$mysql['num'] = mysql_real_escape_string($num);
		$sql5 = " INSERT INTO 202_rotations SET aff_campaign_id='".$mysql['aff_campaign_id']."',  rotation_num='".$mysql['num']."' ";
		$result5 = _mysql_query($sql5);
		$rotation_num = 0;
	}
	
	$url = $urls[$num];
	return $url;
}

function replaceTrackerPlaceholders($url,$click_id) {
	//get the tracker placeholder values
	$mysql['click_id'] = mysql_real_escape_string($click_id);
	
	if(preg_match('/\[\[c1\]\]/', $url) || preg_match('/\[\[c2\]\]/', $url) || preg_match('/\[\[c3\]\]/', $url) || preg_match('/\[\[c4\]\]/', $url)) {
		$click_sql = "
			SELECT
				2c.click_id,
				2tc1.c1,
				2tc2.c2,
				2tc3.c3,
				2tc4.c4
			FROM
				202_clicks AS 2c
				LEFT OUTER JOIN 202_clicks_tracking AS 2ct ON (2ct.click_id = 2c.click_id)
				LEFT OUTER JOIN 202_tracking_c1 AS 2tc1 ON (2ct.c1_id = 2tc1.c1_id)
				LEFT OUTER JOIN 202_tracking_c2 AS 2tc2 ON (2ct.c2_id = 2tc2.c2_id)
				LEFT OUTER JOIN 202_tracking_c3 AS 2tc3 ON (2ct.c3_id = 2tc3.c3_id)
				LEFT OUTER JOIN 202_tracking_c4 AS 2tc4 ON (2ct.c4_id = 2tc4.c4_id)
			WHERE
				2c.click_id='".$mysql['click_id']."'
		";
		$click_result = _mysql_query($click_sql);
		$click_row = mysql_fetch_assoc($click_result);
		
		$url = preg_replace('/\[\[c1\]\]/', $click_row['c1'], $url);
		$url = preg_replace('/\[\[c2\]\]/', $click_row['c2'], $url);
		$url = preg_replace('/\[\[c3\]\]/', $click_row['c3'], $url);
		$url = preg_replace('/\[\[c4\]\]/', $click_row['c4'], $url);
	}
	
	$url = preg_replace('/\[\[subid\]\]/', $mysql['click_id'], $url);
	
	return $url;
}

function setClickIdCookie($click_id,$campaign_id=0) {
	//set the cookie for the PIXEL to fire, expire in 30 days
	$expire = time() + 2592000;
	setcookie('tracking202subid',$click_id,$expire,'/', $_SERVER['SERVER_NAME']);
	setcookie('tracking202subid_a_' . $campaign_id,$click_id,$expire,'/', $_SERVER['SERVER_NAME']);
}

class Browser {
		private $_agent = '';
		private $_browser_name = '';
		private $_version = '';
		private $_platform = '';
		private $_os = '';
		private $_is_aol = false;
		private $_is_mobile = false;
		private $_is_robot = false;
		private $_aol_version = '';
		
		var $Platform = "";
		var $Browser = "";
		

		const BROWSER_UNKNOWN = 'unknown';
		const VERSION_UNKNOWN = 'unknown';

		const BROWSER_OPERA = 'Opera';                            // http://www.opera.com/
		const BROWSER_OPERA_MINI = 'Opera Mini';                  // http://www.opera.com/mini/
		const BROWSER_WEBTV = 'WebTV';                            // http://www.webtv.net/pc/
		const BROWSER_IE = 'Internet Explorer';                   // http://www.microsoft.com/ie/
		const BROWSER_POCKET_IE = 'Pocket Internet Explorer';     // http://en.wikipedia.org/wiki/Internet_Explorer_Mobile
		const BROWSER_KONQUEROR = 'Konqueror';                    // http://www.konqueror.org/
		const BROWSER_ICAB = 'iCab';                              // http://www.icab.de/
		const BROWSER_OMNIWEB = 'OmniWeb';                        // http://www.omnigroup.com/applications/omniweb/
		const BROWSER_FIREBIRD = 'Firebird';                      // http://www.ibphoenix.com/
		const BROWSER_FIREFOX = 'Firefox';                        // http://www.mozilla.com/en-US/firefox/firefox.html
		const BROWSER_ICEWEASEL = 'Iceweasel';                    // http://www.geticeweasel.org/
		const BROWSER_SHIRETOKO = 'Shiretoko';                    // http://wiki.mozilla.org/Projects/shiretoko
		const BROWSER_MOZILLA = 'Mozilla';                        // http://www.mozilla.com/en-US/
		const BROWSER_AMAYA = 'Amaya';                            // http://www.w3.org/Amaya/
		const BROWSER_LYNX = 'Lynx';                              // http://en.wikipedia.org/wiki/Lynx
		const BROWSER_SAFARI = 'Safari';                          // http://apple.com
		const BROWSER_IPHONE = 'iPhone';                          // http://apple.com
		const BROWSER_IPOD = 'iPod';                              // http://apple.com
		const BROWSER_IPAD = 'iPad';                              // http://apple.com
		const BROWSER_CHROME = 'Chrome';                          // http://www.google.com/chrome
		const BROWSER_ANDROID = 'Android';                        // http://www.android.com/
		const BROWSER_GOOGLEBOT = 'GoogleBot';                    // http://en.wikipedia.org/wiki/Googlebot
		const BROWSER_SLURP = 'Yahoo! Slurp';                     // http://en.wikipedia.org/wiki/Yahoo!_Slurp
		const BROWSER_W3CVALIDATOR = 'W3C Validator';             // http://validator.w3.org/
		const BROWSER_BLACKBERRY = 'BlackBerry';                  // http://www.blackberry.com/
		const BROWSER_ICECAT = 'IceCat';                          // http://en.wikipedia.org/wiki/GNU_IceCat
		const BROWSER_NOKIA_S60 = 'Nokia S60 OSS Browser';        // http://en.wikipedia.org/wiki/Web_Browser_for_S60
		const BROWSER_NOKIA = 'Nokia Browser';                    // * all other WAP-based browsers on the Nokia Platform
		const BROWSER_MSN = 'MSN Browser';                        // http://explorer.msn.com/
		const BROWSER_MSNBOT = 'MSN Bot';                         // http://search.msn.com/msnbot.htm
		                                                          // http://en.wikipedia.org/wiki/Msnbot  (used for Bing as well)
		
		const BROWSER_NETSCAPE_NAVIGATOR = 'Netscape Navigator';  // http://browser.netscape.com/ (DEPRECATED)
		const BROWSER_GALEON = 'Galeon';                          // http://galeon.sourceforge.net/ (DEPRECATED)
		const BROWSER_NETPOSITIVE = 'NetPositive';                // http://en.wikipedia.org/wiki/NetPositive (DEPRECATED)
		const BROWSER_PHOENIX = 'Phoenix';                        // http://en.wikipedia.org/wiki/History_of_Mozilla_Firefox (DEPRECATED)

		const PLATFORM_UNKNOWN = 'unknown';
		const PLATFORM_WINDOWS = 'Windows';
		const PLATFORM_WINDOWS_CE = 'Windows CE';
		const PLATFORM_APPLE = 'Apple';
		const PLATFORM_LINUX = 'Linux';
		const PLATFORM_OS2 = 'OS/2';
		const PLATFORM_BEOS = 'BeOS';
		const PLATFORM_IPHONE = 'iPhone';
		const PLATFORM_IPOD = 'iPod';
		const PLATFORM_IPAD = 'iPad';
		const PLATFORM_BLACKBERRY = 'BlackBerry';
		const PLATFORM_NOKIA = 'Nokia';
		const PLATFORM_FREEBSD = 'FreeBSD';
		const PLATFORM_OPENBSD = 'OpenBSD';
		const PLATFORM_NETBSD = 'NetBSD';
		const PLATFORM_SUNOS = 'SunOS';
		const PLATFORM_OPENSOLARIS = 'OpenSolaris';
		const PLATFORM_ANDROID = 'Android';
		
		const OPERATING_SYSTEM_UNKNOWN = 'unknown';

		public function Browser($useragent="") {
			$this->reset();
			if( $useragent != "" ) {
				$this->setUserAgent($useragent);
			}
			else {
				$this->determine();
			}
		}

		/**
		* Reset all properties
		*/
		public function reset() {
			$this->_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
			$this->_browser_name = self::BROWSER_UNKNOWN;
			$this->_version = self::VERSION_UNKNOWN;
			$this->_platform = self::PLATFORM_UNKNOWN;
			$this->_os = self::OPERATING_SYSTEM_UNKNOWN;
			$this->_is_aol = false;
			$this->_is_mobile = false;
			$this->_is_robot = false;
			$this->_aol_version = self::VERSION_UNKNOWN;
		}

		/**
		* Check to see if the specific browser is valid
		* @param string $browserName
		* @return True if the browser is the specified browser
		*/
		function isBrowser($browserName) { return( 0 == strcasecmp($this->_browser_name, trim($browserName))); }

		/**
		* The name of the browser.  All return types are from the class contants
		* @return string Name of the browser
		*/
		public function getBrowser() { return $this->_browser_name; }
		/**
		* Set the name of the browser
		* @param $browser The name of the Browser
		*/
		public function setBrowser($browser) { return $this->_browser_name = $browser; }
		/**
		* The name of the platform.  All return types are from the class contants
		* @return string Name of the browser
		*/
		public function getPlatform() { return $this->_platform; }
		/**
		* Set the name of the platform
		* @param $platform The name of the Platform
		*/
		public function setPlatform($platform) { return $this->_platform = $platform; }
		/**
		* The version of the browser.
		* @return string Version of the browser (will only contain alpha-numeric characters and a period)
		*/
		public function getVersion() { return $this->_version; }
		/**
		* Set the version of the browser
		* @param $version The version of the Browser
		*/
		public function setVersion($version) { $this->_version = preg_replace('/[^0-9,.,a-z,A-Z-]/','',$version); }
		/**
		* The version of AOL.
		* @return string Version of AOL (will only contain alpha-numeric characters and a period)
		*/
		public function getAolVersion() { return $this->_aol_version; }
		/**
		* Set the version of AOL
		* @param $version The version of AOL
		*/
		public function setAolVersion($version) { $this->_aol_version = preg_replace('/[^0-9,.,a-z,A-Z]/','',$version); }
		/**
		* Is the browser from AOL?
		* @return boolean True if the browser is from AOL otherwise false
		*/
		public function isAol() { return $this->_is_aol; }
		/**
		* Is the browser from a mobile device?
		* @return boolean True if the browser is from a mobile device otherwise false
		*/
		public function isMobile() { return $this->_is_mobile; }
		/**
		* Is the browser from a robot (ex Slurp,GoogleBot)?
		* @return boolean True if the browser is from a robot otherwise false
		*/
		public function isRobot() { return $this->_is_robot; }
		/**
		* Set the browser to be from AOL
		* @param $isAol
		*/
		public function setAol($isAol) { $this->_is_aol = $isAol; }
		/**
		 * Set the Browser to be mobile
		 * @param boolean $value is the browser a mobile brower or not
		 */
		protected function setMobile($value=true) { $this->_is_mobile = $value; }
		/**
		 * Set the Browser to be a robot
		 * @param boolean $value is the browser a robot or not
		 */
		protected function setRobot($value=true) { $this->_is_robot = $value; }
		/**
		* Get the user agent value in use to determine the browser
		* @return string The user agent from the HTTP header
		*/
		public function getUserAgent() { return $this->_agent; }
		/**
		* Set the user agent value (the construction will use the HTTP header value - this will overwrite it)
		* @param $agent_string The value for the User Agent
		*/
		public function setUserAgent($agent_string) {
			$this->reset();
			$this->_agent = $agent_string;
			$this->determine();
		}
		/**
		 * Used to determine if the browser is actually "chromeframe"
		 * @since 1.7
		 * @return boolean True if the browser is using chromeframe
		 */
		public function isChromeFrame() {
			return( strpos($this->_agent,"chromeframe") !== false );
		}
		/**
		* Returns a formatted string with a summary of the details of the browser.
		* @return string formatted string with a summary of the browser
		*/
		public function __toString() {
			return "<strong>Browser Name:</strong>{$this->getBrowser()}<br/>\n" .
			       "<strong>Browser Version:</strong>{$this->getVersion()}<br/>\n" .
			       "<strong>Browser User Agent String:</strong>{$this->getUserAgent()}<br/>\n" .
			       "<strong>Platform:</strong>{$this->getPlatform()}<br/>";
		}
		/**
		 * Protected routine to calculate and determine what the browser is in use (including platform)
		 */
		protected function determine() {
			$this->checkPlatform();
			$this->checkBrowsers();
			$this->checkForAol();
		}
		/**
		 * Protected routine to determine the browser type
		 * @return boolean True if the browser was detected otherwise false
		 */
		 protected function checkBrowsers() {
			return (
				// well-known, well-used
				// Special Notes:
				// (1) Opera must be checked before FireFox due to the odd
				//     user agents used in some older versions of Opera
				// (2) WebTV is strapped onto Internet Explorer so we must
				//     check for WebTV before IE
				// (3) (deprecated) Galeon is based on Firefox and needs to be
				//     tested before Firefox is tested
				// (4) OmniWeb is based on Safari so OmniWeb check must occur
				//     before Safari
				// (5) Netscape 9+ is based on Firefox so Netscape checks
				//     before FireFox are necessary
				$this->checkBrowserWebTv() ||
				$this->checkBrowserInternetExplorer() ||
				$this->checkBrowserOpera() ||
				$this->checkBrowserGaleon() ||
				$this->checkBrowserNetscapeNavigator9Plus() ||
				$this->checkBrowserFirefox() ||
				$this->checkBrowserChrome() ||
				$this->checkBrowserOmniWeb() ||

				// common mobile
				$this->checkBrowserAndroid() ||
				$this->checkBrowseriPad() ||
				$this->checkBrowseriPod() ||
				$this->checkBrowseriPhone() ||
				$this->checkBrowserBlackBerry() ||
				$this->checkBrowserNokia() ||

				// common bots
				$this->checkBrowserGoogleBot() ||
				$this->checkBrowserMSNBot() ||
				$this->checkBrowserSlurp() ||

				// WebKit base check (post mobile and others)
				$this->checkBrowserSafari() ||
				
				// everyone else
				$this->checkBrowserNetPositive() ||
				$this->checkBrowserFirebird() ||
				$this->checkBrowserKonqueror() ||
				$this->checkBrowserIcab() ||
				$this->checkBrowserPhoenix() ||
				$this->checkBrowserAmaya() ||
				$this->checkBrowserLynx() ||
				$this->checkBrowserShiretoko() ||
				$this->checkBrowserIceCat() ||
				$this->checkBrowserW3CValidator() ||
				$this->checkBrowserMozilla() /* Mozilla is such an open standard that you must check it last */
			);
	    }

	    /**
	     * Determine if the user is using a BlackBerry (last updated 1.7)
	     * @return boolean True if the browser is the BlackBerry browser otherwise false
	     */
	    protected function checkBrowserBlackBerry() {
		    if( stripos($this->_agent,'blackberry') !== false ) {
			    $aresult = explode("/",stristr($this->_agent,"BlackBerry"));
			    $aversion = explode(' ',$aresult[1]);
			    $this->setVersion($aversion[0]);
			    $this->_browser_name = self::BROWSER_BLACKBERRY;
			    $this->setMobile(true);
			    $this->Browser=29;
			    return true;
		    }
		    return false;
	    }

	    /**
	     * Determine if the user is using an AOL User Agent (last updated 1.7)
	     * @return boolean True if the browser is from AOL otherwise false
	     */
	    protected function checkForAol() {
			$this->setAol(false);
			$this->setAolVersion(self::VERSION_UNKNOWN);

			if( stripos($this->_agent,'aol') !== false ) {
			    $aversion = explode(' ',stristr($this->_agent, 'AOL'));
			    $this->setAol(true);
			    $this->setAolVersion(preg_replace('/[^0-9\.a-z]/i', '', $aversion[1]));
			    $this->Browser=8;
			    return true;
		    }
		    return false;
	    }

	    /**
	     * Determine if the browser is the GoogleBot or not (last updated 1.7)
	     * @return boolean True if the browser is the GoogletBot otherwise false
	     */
	    protected function checkBrowserGoogleBot() {
		    if( stripos($this->_agent,'googlebot') !== false ) {
				$aresult = explode('/',stristr($this->_agent,'googlebot'));
				$aversion = explode(' ',$aresult[1]);
				$this->setVersion(str_replace(';','',$aversion[0]));
				$this->_browser_name = self::BROWSER_GOOGLEBOT;
				$this->setRobot(true);
				$this->Browser=26;
				return true;
		    }
		    return false;
	    }

		/**
	     * Determine if the browser is the MSNBot or not (last updated 1.9)
	     * @return boolean True if the browser is the MSNBot otherwise false
	     */
		protected function checkBrowserMSNBot() {
			if( stripos($this->_agent,"msnbot") !== false ) {
				$aresult = explode("/",stristr($this->_agent,"msnbot"));
				$aversion = explode(" ",$aresult[1]);
				$this->setVersion(str_replace(";","",$aversion[0]));
				$this->_browser_name = self::BROWSER_MSNBOT;
				$this->setRobot(true);
				$this->Browser=34;
				return true;
			}
			return false;
		}	    
	    
	    /**
	     * Determine if the browser is the W3C Validator or not (last updated 1.7)
	     * @return boolean True if the browser is the W3C Validator otherwise false
	     */
	    protected function checkBrowserW3CValidator() {
		    if( stripos($this->_agent,'W3C-checklink') !== false ) {
			    $aresult = explode('/',stristr($this->_agent,'W3C-checklink'));
			    $aversion = explode(' ',$aresult[1]);
			    $this->setVersion($aversion[0]);
			    $this->_browser_name = self::BROWSER_W3CVALIDATOR;
			    $this->Browser=28;
			    return true;
		    }
		    else if( stripos($this->_agent,'W3C_Validator') !== false ) {
				// Some of the Validator versions do not delineate w/ a slash - add it back in
				$ua = str_replace("W3C_Validator ", "W3C_Validator/", $this->_agent);
			    $aresult = explode('/',stristr($ua,'W3C_Validator'));
			    $aversion = explode(' ',$aresult[1]);
			    $this->setVersion($aversion[0]);
			    $this->_browser_name = self::BROWSER_W3CVALIDATOR;
			    $this->Browser=28;
			    return true;
		    }
		    return false;
	    }

	    /**
	     * Determine if the browser is the Yahoo! Slurp Robot or not (last updated 1.7)
	     * @return boolean True if the browser is the Yahoo! Slurp Robot otherwise false
	     */
	    protected function checkBrowserSlurp() {
		    if( stripos($this->_agent,'slurp') !== false ) {
			    $aresult = explode('/',stristr($this->_agent,'Slurp'));
			    $aversion = explode(' ',$aresult[1]);
			    $this->setVersion($aversion[0]);
			    $this->_browser_name = self::BROWSER_SLURP;
				$this->setRobot(true);
				$this->setMobile(false);
				$this->Browser=27;
			    return true;
		    }
		    return false;
	    }

	    /**
	     * Determine if the browser is Internet Explorer or not (last updated 1.7)
	     * @return boolean True if the browser is Internet Explorer otherwise false
	     */
	    protected function checkBrowserInternetExplorer() {

		    // Test for v1 - v1.5 IE
		    if( stripos($this->_agent,'microsoft internet explorer') !== false ) {
			    $this->setBrowser(self::BROWSER_IE);
			    $this->Browser=1;
			    $this->setVersion('1.0');
			    $aresult = stristr($this->_agent, '/');
			    if( preg_match('/308|425|426|474|0b1/i', $aresult) ) {
				    $this->setVersion('1.5');
			    }
				return true;
		    }
		    // Test for versions > 1.5
		    else if( stripos($this->_agent,'msie') !== false && stripos($this->_agent,'opera') === false ) {
		    	// See if the browser is the odd MSN Explorer
		    	if( stripos($this->_agent,'msnb') !== false ) {
			    	$aresult = explode(' ',stristr(str_replace(';','; ',$this->_agent),'MSN'));
				    $this->setBrowser( self::BROWSER_MSN );
				    $this->Browser=33;
				    $this->setVersion(str_replace(array('(',')',';'),'',$aresult[1]));
				    return true;
		    	}
		    	$aresult = explode(' ',stristr(str_replace(';','; ',$this->_agent),'msie'));
		    	$this->setBrowser( self::BROWSER_IE );
		    	$this->Browser=1;
		    	$this->setVersion(str_replace(array('(',')',';'),'',$aresult[1]));
		    	return true;
		    }
		    // Test for Pocket IE
		    else if( stripos($this->_agent,'mspie') !== false || stripos($this->_agent,'pocket') !== false ) {
			    $aresult = explode(' ',stristr($this->_agent,'mspie'));
			    $this->setPlatform( self::PLATFORM_WINDOWS_CE );
			    $this->setBrowser( self::BROWSER_POCKET_IE );
			    $this->Browser=14;
			    $this->setMobile(true);

			    if( stripos($this->_agent,'mspie') !== false ) {
				    $this->setVersion($aresult[1]);
			    }
			    else {
				    $aversion = explode('/',$this->_agent);
				    $this->setVersion($aversion[1]);
			    }
			    return true;
		    }
			return false;
	    }

	    /**
	     * Determine if the browser is Opera or not (last updated 1.7)
	     * @return boolean True if the browser is Opera otherwise false
	     */
	    protected function checkBrowserOpera() {
		    if( stripos($this->_agent,'opera mini') !== false ) {
			    $resultant = stristr($this->_agent, 'opera mini');
			    if( preg_match('/\//',$resultant) ) {
				    $aresult = explode('/',$resultant);
				    $aversion = explode(' ',$aresult[1]);
				    $this->setVersion($aversion[0]);
				}
			    else {
				    $aversion = explode(' ',stristr($resultant,'opera mini'));
				    $this->setVersion($aversion[1]);
			    }
			    $this->_browser_name = self::BROWSER_OPERA_MINI;
			    $this->Browser=12;
				$this->setMobile(true);
				return true;
		    }
		    else if( stripos($this->_agent,'opera') !== false ) {
			    $resultant = stristr($this->_agent, 'opera');
			    if( preg_match('/Version\/(10.*)$/',$resultant,$matches) ) {
				    $this->setVersion($matches[1]);
			    }
			    else if( preg_match('/\//',$resultant) ) {
				    $aresult = explode('/',str_replace("("," ",$resultant));
				    $aversion = explode(' ',$aresult[1]);
				    $this->setVersion($aversion[0]);
			    }
			    else {
				    $aversion = explode(' ',stristr($resultant,'opera'));
				    $this->setVersion(isset($aversion[1])?$aversion[1]:"");
			    }
			    $this->_browser_name = self::BROWSER_OPERA;
			    $this->Browser=6;
			    return true;
		    }
			return false;
	    }

	    /**
	     * Determine if the browser is Chrome or not (last updated 1.7)
	     * @return boolean True if the browser is Chrome otherwise false
	     */
	    protected function checkBrowserChrome() {
		    if( stripos($this->_agent,'Chrome') !== false ) {
			    $aresult = explode('/',stristr($this->_agent,'Chrome'));
			    $aversion = explode(' ',$aresult[1]);
			    $this->setVersion($aversion[0]);
			    $this->setBrowser(self::BROWSER_CHROME);
			    $this->Browser=9;
			    return true;
		    }
		    return false;
	    }


	    /**
	     * Determine if the browser is WebTv or not (last updated 1.7)
	     * @return boolean True if the browser is WebTv otherwise false
	     */
	    protected function checkBrowserWebTv() {
		    if( stripos($this->_agent,'webtv') !== false ) {
			    $aresult = explode('/',stristr($this->_agent,'webtv'));
			    $aversion = explode(' ',$aresult[1]);
			    $this->setVersion($aversion[0]);
			    $this->setBrowser(self::BROWSER_WEBTV);
			    $this->Browser=13;
			    return true;
		    }
		    return false;
	    }

	    /**
	     * Determine if the browser is NetPositive or not (last updated 1.7)
	     * @return boolean True if the browser is NetPositive otherwise false
	     */
	    protected function checkBrowserNetPositive() {
		    if( stripos($this->_agent,'NetPositive') !== false ) {
			    $aresult = explode('/',stristr($this->_agent,'NetPositive'));
			    $aversion = explode(' ',$aresult[1]);
			    $this->setVersion(str_replace(array('(',')',';'),'',$aversion[0]));
			    $this->setBrowser(self::BROWSER_NETPOSITIVE);
			    $this->Browser=36;
			    return true;
		    }
		    return false;
	    }

	    /**
	     * Determine if the browser is Galeon or not (last updated 1.7)
	     * @return boolean True if the browser is Galeon otherwise false
	     */
	    protected function checkBrowserGaleon() {
		    if( stripos($this->_agent,'galeon') !== false ) {
			    $aresult = explode(' ',stristr($this->_agent,'galeon'));
			    $aversion = explode('/',$aresult[0]);
			    $this->setVersion($aversion[1]);
			    $this->setBrowser(self::BROWSER_GALEON);
			    $this->Browser=35;
			    return true;
		    }
		    return false;
	    }

	    /**
	     * Determine if the browser is Konqueror or not (last updated 1.7)
	     * @return boolean True if the browser is Konqueror otherwise false
	     */
	    protected function checkBrowserKonqueror() {
		    if( stripos($this->_agent,'Konqueror') !== false ) {
			    $aresult = explode(' ',stristr($this->_agent,'Konqueror'));
			    $aversion = explode('/',$aresult[0]);
			    $this->setVersion($aversion[1]);
			    $this->setBrowser(self::BROWSER_KONQUEROR);
			    $this->Browser=3;
			    return true;
		    }
		    return false;
	    }

	    /**
	     * Determine if the browser is iCab or not (last updated 1.7)
	     * @return boolean True if the browser is iCab otherwise false
	     */
	    protected function checkBrowserIcab() {
		    if( stripos($this->_agent,'icab') !== false ) {
			    $aversion = explode(' ',stristr(str_replace('/',' ',$this->_agent),'icab'));
			    $this->setVersion($aversion[1]);
			    $this->setBrowser(self::BROWSER_ICAB);
			    $this->Browser=15;
			    return true;
		    }
		    return false;
	    }

	    /**
	     * Determine if the browser is OmniWeb or not (last updated 1.7)
	     * @return boolean True if the browser is OmniWeb otherwise false
	     */
	    protected function checkBrowserOmniWeb() {
		    if( stripos($this->_agent,'omniweb') !== false ) {
			    $aresult = explode('/',stristr($this->_agent,'omniweb'));
			    $aversion = explode(' ',isset($aresult[1])?$aresult[1]:"");
			    $this->setVersion($aversion[0]);
			    $this->setBrowser(self::BROWSER_OMNIWEB);
			    $this->Browser=5;
			    return true;
		    }
		    return false;
	    }

	    /**
	     * Determine if the browser is Phoenix or not (last updated 1.7)
	     * @return boolean True if the browser is Phoenix otherwise false
	     */
	    protected function checkBrowserPhoenix() {
		    if( stripos($this->_agent,'Phoenix') !== false ) {
			    $aversion = explode('/',stristr($this->_agent,'Phoenix'));
			    $this->setVersion($aversion[1]);
			    $this->setBrowser(self::BROWSER_PHOENIX);
			    $this->Browser=37;
			    return true;
		    }
		    return false;
	    }

	    /**
	     * Determine if the browser is Firebird or not (last updated 1.7)
	     * @return boolean True if the browser is Firebird otherwise false
	     */
	    protected function checkBrowserFirebird() {
		    if( stripos($this->_agent,'Firebird') !== false ) {
			    $aversion = explode('/',stristr($this->_agent,'Firebird'));
			    $this->setVersion($aversion[1]);
			    $this->setBrowser(self::BROWSER_FIREBIRD);
			    $this->Browser=16;
				return true;
		    }
		    return false;
	    }

	    /**
	     * Determine if the browser is Netscape Navigator 9+ or not (last updated 1.7)
		 * NOTE: (http://browser.netscape.com/ - Official support ended on March 1st, 2008)
	     * @return boolean True if the browser is Netscape Navigator 9+ otherwise false
	     */
	    protected function checkBrowserNetscapeNavigator9Plus() {
		    if( stripos($this->_agent,'Firefox') !== false && preg_match('/Navigator\/([^ ]*)/i',$this->_agent,$matches) ) {
			    $this->setVersion($matches[1]);
			    $this->setBrowser(self::BROWSER_NETSCAPE_NAVIGATOR);
			    $this->Browser=4;
			    return true;
		    }
		    else if( stripos($this->_agent,'Firefox') === false && preg_match('/Netscape6?\/([^ ]*)/i',$this->_agent,$matches) ) {
			    $this->setVersion($matches[1]);
			    $this->setBrowser(self::BROWSER_NETSCAPE_NAVIGATOR);
			    $this->Browser=4;
			    return true;
		    }
		    return false;
	    }

	    /**
	     * Determine if the browser is Shiretoko or not (https://wiki.mozilla.org/Projects/shiretoko) (last updated 1.7)
	     * @return boolean True if the browser is Shiretoko otherwise false
	     */
	    protected function checkBrowserShiretoko() {
		    if( stripos($this->_agent,'Mozilla') !== false && preg_match('/Shiretoko\/([^ ]*)/i',$this->_agent,$matches) ) {
			    $this->setVersion($matches[1]);
			    $this->setBrowser(self::BROWSER_SHIRETOKO);
			    $this->Browser=18;
			    return true;
		    }
		    return false;
	    }

	    /**
	     * Determine if the browser is Ice Cat or not (http://en.wikipedia.org/wiki/GNU_IceCat) (last updated 1.7)
	     * @return boolean True if the browser is Ice Cat otherwise false
	     */
	    protected function checkBrowserIceCat() {
		    if( stripos($this->_agent,'Mozilla') !== false && preg_match('/IceCat\/([^ ]*)/i',$this->_agent,$matches) ) {
			    $this->setVersion($matches[1]);
			    $this->setBrowser(self::BROWSER_ICECAT);
			    $this->Browser=30;
			    return true;
		    }
		    return false;
	    }

	    /**
	     * Determine if the browser is Nokia or not (last updated 1.7)
	     * @return boolean True if the browser is Nokia otherwise false
	     */
	    protected function checkBrowserNokia() {
		    if( preg_match("/Nokia([^\/]+)\/([^ SP]+)/i",$this->_agent,$matches) ) {
			    $this->setVersion($matches[2]);
				if( stripos($this->_agent,'Series60') !== false || strpos($this->_agent,'S60') !== false ) {
					$this->setBrowser(self::BROWSER_NOKIA_S60);
					$this->Browser=31;
				}
				else {
					$this->setBrowser( self::BROWSER_NOKIA );
					$this->Browser=32;
				}
			    $this->setMobile(true);
			    return true;
		    }
			return false;
	    }

	    /**
	     * Determine if the browser is Firefox or not (last updated 1.7)
	     * @return boolean True if the browser is Firefox otherwise false
	     */
	    protected function checkBrowserFirefox() {
		    if( stripos($this->_agent,'safari') === false ) {
				if( preg_match("/Firefox[\/ \(]([^ ;\)]+)/i",$this->_agent,$matches) ) {
					$this->setVersion($matches[1]);
					$this->setBrowser(self::BROWSER_FIREFOX);
					$this->Browser=2;
					return true;
				}
				else if( preg_match("/Firefox$/i",$this->_agent,$matches) ) {
					$this->setVersion("");
					$this->setBrowser(self::BROWSER_FIREFOX);
					$this->Browser=2;
					return true;
				}
			}
		    return false;
	    }

		/**
	     * Determine if the browser is Firefox or not (last updated 1.7)
	     * @return boolean True if the browser is Firefox otherwise false
	     */
	    protected function checkBrowserIceweasel() {
			if( stripos($this->_agent,'Iceweasel') !== false ) {
				$aresult = explode('/',stristr($this->_agent,'Iceweasel'));
				$aversion = explode(' ',$aresult[1]);
				$this->setVersion($aversion[0]);
				$this->setBrowser(self::BROWSER_ICEWEASEL);
				$this->Browser=17;
				return true;
			}
			return false;
		}
	    /**
	     * Determine if the browser is Mozilla or not (last updated 1.7)
	     * @return boolean True if the browser is Mozilla otherwise false
	     */
	    protected function checkBrowserMozilla() {
		    if( stripos($this->_agent,'mozilla') !== false  && preg_match('/rv:[0-9].[0-9][a-b]?/i',$this->_agent) && stripos($this->_agent,'netscape') === false) {
			    $aversion = explode(' ',stristr($this->_agent,'rv:'));
			    preg_match('/rv:[0-9].[0-9][a-b]?/i',$this->_agent,$aversion);
			    $this->setVersion(str_replace('rv:','',$aversion[0]));
			    $this->setBrowser(self::BROWSER_MOZILLA);
			    $this->Browser=19;
			    return true;
		    }
		    else if( stripos($this->_agent,'mozilla') !== false && preg_match('/rv:[0-9]\.[0-9]/i',$this->_agent) && stripos($this->_agent,'netscape') === false ) {
			    $aversion = explode('',stristr($this->_agent,'rv:'));
			    $this->setVersion(str_replace('rv:','',$aversion[0]));
			    $this->setBrowser(self::BROWSER_MOZILLA);
			    $this->Browser=19;
			    return true;
		    }
		    else if( stripos($this->_agent,'mozilla') !== false  && preg_match('/mozilla\/([^ ]*)/i',$this->_agent,$matches) && stripos($this->_agent,'netscape') === false ) {
			    $this->setVersion($matches[1]);
			    $this->setBrowser(self::BROWSER_MOZILLA);
			    $this->Browser=19;
			    return true;
		    }
			return false;
	    }

	    /**
	     * Determine if the browser is Lynx or not (last updated 1.7)
	     * @return boolean True if the browser is Lynx otherwise false
	     */
	    protected function checkBrowserLynx() {
		    if( stripos($this->_agent,'lynx') !== false ) {
			    $aresult = explode('/',stristr($this->_agent,'Lynx'));
			    $aversion = explode(' ',(isset($aresult[1])?$aresult[1]:""));
			    $this->setVersion($aversion[0]);
			    $this->setBrowser(self::BROWSER_LYNX);
			    $this->Browser=21;
			    return true;
		    }
		    return false;
	    }

	    /**
	     * Determine if the browser is Amaya or not (last updated 1.7)
	     * @return boolean True if the browser is Amaya otherwise false
	     */
	    protected function checkBrowserAmaya() {
		    if( stripos($this->_agent,'amaya') !== false ) {
			    $aresult = explode('/',stristr($this->_agent,'Amaya'));
			    $aversion = explode(' ',$aresult[1]);
			    $this->setVersion($aversion[0]);
			    $this->setBrowser(self::BROWSER_AMAYA);
			    $this->Browser=20;
			    return true;
		    }
		    return false;
	    }

	    /**
	     * Determine if the browser is Safari or not (last updated 1.7)
	     * @return boolean True if the browser is Safari otherwise false
	     */
	    protected function checkBrowserSafari() {
		    if( stripos($this->_agent,'Safari') !== false && stripos($this->_agent,'iPhone') === false && stripos($this->_agent,'iPod') === false ) {
			    $aresult = explode('/',stristr($this->_agent,'Version'));
			    if( isset($aresult[1]) ) {
				    $aversion = explode(' ',$aresult[1]);
				    $this->setVersion($aversion[0]);
			    }
			    else {
				    $this->setVersion(self::VERSION_UNKNOWN);
			    }
			    $this->setBrowser(self::BROWSER_SAFARI);
			    $this->Browser=7;
			    return true;
		    }
		    return false;
	    }

	    /**
	     * Determine if the browser is iPhone or not (last updated 1.7)
	     * @return boolean True if the browser is iPhone otherwise false
	     */
	    protected function checkBrowseriPhone() {
		    if( stripos($this->_agent,'iPhone') !== false ) {
			    $aresult = explode('/',stristr($this->_agent,'Version'));
			    if( isset($aresult[1]) ) {
				    $aversion = explode(' ',$aresult[1]);
				    $this->setVersion($aversion[0]);
			    }
			    else {
				    $this->setVersion(self::VERSION_UNKNOWN);
			    }
			    $this->setMobile(true);
			    $this->setBrowser(self::BROWSER_IPHONE);
			    $this->Browser=22;
			    return true;
		    }
		    return false;
	    }

	    /**
	     * Determine if the browser is iPod or not (last updated 1.7)
	     * @return boolean True if the browser is iPod otherwise false
	     */
	    protected function checkBrowseriPad() {
		    if( stripos($this->_agent,'iPad') !== false ) {
			    $aresult = explode('/',stristr($this->_agent,'Version'));
			    if( isset($aresult[1]) ) {
				    $aversion = explode(' ',$aresult[1]);
				    $this->setVersion($aversion[0]);
			    }
			    else {
				    $this->setVersion(self::VERSION_UNKNOWN);
			    }
			    $this->setMobile(true);
			    $this->setBrowser(self::BROWSER_IPAD);
			    $this->Browser=24;
			    return true;
		    }
		    return false;
	    }

	    /**
	     * Determine if the browser is iPod or not (last updated 1.7)
	     * @return boolean True if the browser is iPod otherwise false
	     */
	    protected function checkBrowseriPod() {
		    if( stripos($this->_agent,'iPod') !== false ) {
			    $aresult = explode('/',stristr($this->_agent,'Version'));
			    if( isset($aresult[1]) ) {
				    $aversion = explode(' ',$aresult[1]);
				    $this->setVersion($aversion[0]);
			    }
			    else {
				    $this->setVersion(self::VERSION_UNKNOWN);
			    }
			    $this->setMobile(true);
			    $this->setBrowser(self::BROWSER_IPOD);
			    $this->Browser=23;
			    return true;
		    }
		    return false;
	    }

	    /**
	     * Determine if the browser is Android or not (last updated 1.7)
	     * @return boolean True if the browser is Android otherwise false
	     */
	    protected function checkBrowserAndroid() {
		    if( stripos($this->_agent,'Android') !== false ) {
			    $aresult = explode(' ',stristr($this->_agent,'Android'));
			    if( isset($aresult[1]) ) {
				    $aversion = explode(' ',$aresult[1]);
				    $this->setVersion($aversion[0]);
			    }
			    else {
				    $this->setVersion(self::VERSION_UNKNOWN);
			    }
			    $this->setMobile(true);
			    $this->setBrowser(self::BROWSER_ANDROID);
			    $this->Browser=25;
			    return true;
		    }
		    return false;
	    }

	    /**
	     * Determine the user's platform (last updated 1.7)
	     */
	    protected function checkPlatform() {
		    if( stripos($this->_agent, 'windows') !== false ) {
			    $this->_platform = self::PLATFORM_WINDOWS;
			    $this->Platform=1;
		    }
		    else if( stripos($this->_agent, 'iPad') !== false ) {
			    $this->_platform = self::PLATFORM_IPAD;
			    $this->Platform=7;
		    }
		    else if( stripos($this->_agent, 'iPod') !== false ) {
			    $this->_platform = self::PLATFORM_IPOD;
			    $this->Platform=6;
		    }
		    else if( stripos($this->_agent, 'iPhone') !== false ) {
			    $this->_platform = self::PLATFORM_IPHONE;
			    $this->Platform=6;
		    }
		    elseif( stripos($this->_agent, 'mac') !== false ) {
			    $this->_platform = self::PLATFORM_APPLE;
			    $this->Platform=2;
		    }
		    elseif( stripos($this->_agent, 'android') !== false ) {
			    $this->_platform = self::PLATFORM_ANDROID;
			    $this->Platform=6;
		    }
		    elseif( stripos($this->_agent, 'linux') !== false ) {
			    $this->_platform = self::PLATFORM_LINUX;
			    $this->Platform=3;
		    }
		    else if( stripos($this->_agent, 'Nokia') !== false ) {
			    $this->_platform = self::PLATFORM_NOKIA;
			    $this->Platform=6;
		    }
		    else if( stripos($this->_agent, 'BlackBerry') !== false ) {
			    $this->_platform = self::PLATFORM_BLACKBERRY;
			    $this->Platform=6;
		    }
		    elseif( stripos($this->_agent,'FreeBSD') !== false ) {
			    $this->_platform = self::PLATFORM_FREEBSD;
			    $this->Platform=3;
		    }
		    elseif( stripos($this->_agent,'OpenBSD') !== false ) {
			    $this->_platform = self::PLATFORM_OPENBSD;
			    $this->Platform=3;
		    }
		    elseif( stripos($this->_agent,'NetBSD') !== false ) {
			    $this->_platform = self::PLATFORM_NETBSD;
			    $this->Platform=3;
		    }
		    elseif( stripos($this->_agent, 'OpenSolaris') !== false ) {
			    $this->_platform = self::PLATFORM_OPENSOLARIS;
			    $this->Platform=3;
		    }
		    elseif( stripos($this->_agent, 'SunOS') !== false ) {
			    $this->_platform = self::PLATFORM_SUNOS;
			    $this->Platform=3;
		    }
		    elseif( stripos($this->_agent, 'OS\/2') !== false ) {
			    $this->_platform = self::PLATFORM_OS2;
			    $this->Platform=4;
		    }
		    elseif( stripos($this->_agent, 'BeOS') !== false ) {
			    $this->_platform = self::PLATFORM_BEOS;
			    $this->Platform=5;
		    }
		    elseif( stripos($this->_agent, 'win') !== false ) {
			    $this->_platform = self::PLATFORM_WINDOWS;
			    $this->Platform=1;
		    }

	    }
	    
    }
class INDEXES {
	
		
	//this returns the ip_id, when a ip_address is given
	function get_ip_id($ip_address) {
	
		$mysql['ip_address'] = mysql_real_escape_string($ip_address);
		
		$ip_sql = "SELECT ip_id FROM 202_ips WHERE ip_address='".$mysql['ip_address']."'";
		$ip_result = _mysql_query($ip_sql);
		$ip_row = mysql_fetch_assoc($ip_result); 
		if ($ip_row) {
			//if this ip already exists, return the ip_id for it.
			$ip_id = $ip_row['ip_id'];
			
			return $ip_id;    
		} else {
			//else if this  doesn't exist, insert the new iprow, and return the_id for this new row we found
			//but before we do this, we need to grab the location_id
			$location_id = INDEXES::get_location_id($ip_address); 
			$mysql['location_id'] = mysql_real_escape_string($location_id);    
			$ip_sql = "INSERT INTO 202_ips SET ip_address='".$mysql['ip_address']."', location_id='".$mysql['location_id']."'";
			$ip_result = _mysql_query($ip_sql) ; //($ip_sql);
			$ip_id = mysql_insert_id();
			
			return $ip_id;    
		}
	}   
	
	//this returns the site_url_id, when a site_url_address is given
	function get_site_url_id($site_url_address) { 
	
		$mysql['site_url_address'] = mysql_real_escape_string($site_url_address);
		$site_domain_id = INDEXES::get_site_domain_id($site_url_address); 
		$mysql['site_domain_id'] = mysql_real_escape_string($site_domain_id);    
		$site_url_sql = "INSERT INTO 202_site_urls SET site_domain_id='".$mysql['site_domain_id']."', site_url_address='".$mysql['site_url_address']."'"; 
		$site_url_result = _mysql_query($site_url_sql) ; //($site_url_sql);
		$site_url_id = mysql_insert_id();
		return $site_url_id;
	}    
	
	//this returns the site_domain_id, when a site_url_address is given
	function get_site_domain_id($site_url_address) { 
	
		$parsed_url = @parse_url($site_url_address);
		$site_domain_host = $parsed_url['host'];
		$site_domain_host = str_replace('www.','',$site_domain_host);
		$mysql['site_domain_host'] = mysql_real_escape_string($site_domain_host);
		
		$site_domain_sql = "SELECT site_domain_id FROM 202_site_domains WHERE site_domain_host='".$mysql['site_domain_host']."'"; 
		$site_domain_result = _mysql_query($site_domain_sql);
		$site_domain_row = mysql_fetch_assoc($site_domain_result);
		if ($site_domain_row) {
			//if this site_domain_id already exists, return the site_domain_id for it.
			$site_domain_id = $site_domain_row['site_domain_id'];
			return $site_domain_id;    
		} else {
			//else if this  doesn't exist, insert the new iprow, and return the_id for this new row we found
			$site_domain_sql = "INSERT INTO 202_site_domains SET site_domain_host='".$mysql['site_domain_host']."'"; 
			$site_domain_result = _mysql_query($site_domain_sql) ; //($site_domain_sql);
			$site_domain_id = mysql_insert_id();
			return $site_domain_id;    
		}    
	} 
	
	//this returns the keyword_id
	function get_keyword_id($keyword) {
		
		//only grab the first 255 charactesr of keyword
		$keyword = substr($keyword, 0, 255);
		$mysql['keyword'] = mysql_real_escape_string($keyword);
		
		$keyword_sql = "SELECT keyword_id FROM 202_keywords WHERE keyword='".$mysql['keyword']."'";
		$keyword_result = _mysql_query($keyword_sql);
		$keyword_row = mysql_fetch_assoc($keyword_result);
		if ($keyword_row) {
			//if this already exists, return the id for it
			$keyword_id = $keyword_row['keyword_id'];
			return $keyword_id;    
		} else {
			//else if this ip doesn't exist, insert the row and grab the id for it
			$keyword_sql = "INSERT INTO 202_keywords SET keyword='".$mysql['keyword']."'";
			$keyword_result = _mysql_query($keyword_sql) ; //($keyword_sql);
			$keyword_id = mysql_insert_id();
			return $keyword_id;    
		}
	}
	
	//this returns the c1 id
	function get_c1_id($c1) {
		
		//only grab the first 50 charactesr of c1
		$c1 = substr($c1, 0, 50);
		$mysql['c1'] = mysql_real_escape_string($c1);
		
		$c1_sql = "SELECT c1_id FROM 202_tracking_c1 WHERE c1='".$mysql['c1']."'";
		$c1_result = _mysql_query($c1_sql);
		$c1_row = mysql_fetch_assoc($c1_result);
		if ($c1_row) {
			//if this already exists, return the id for it
			$c1_id = $c1_row['c1_id'];
			return $c1_id;    
		} else {
			//else if this ip doesn't exist, insert the row and grab the id for it
			$c1_sql = "INSERT INTO 202_tracking_c1 SET c1='".$mysql['c1']."'";
			$c1_result = _mysql_query($c1_sql) ; //($c1_sql);
			$c1_id = mysql_insert_id();
			return $c1_id;     
		}
	}
	
	//this returns the c2 id
	function get_c2_id($c2) {
		
		//only grab the first 50 charactesr of c2
		$c2 = substr($c2, 0, 50);
		$mysql['c2'] = mysql_real_escape_string($c2);
		
		$c2_sql = "SELECT c2_id FROM 202_tracking_c2 WHERE c2='".$mysql['c2']."'";
		$c2_result = _mysql_query($c2_sql);
		$c2_row = mysql_fetch_assoc($c2_result);
		if ($c2_row) {
			//if this already exists, return the id for it
			$c2_id = $c2_row['c2_id'];
			return $c2_id;    
		} else {
			//else if this ip doesn't exist, insert the row and grab the id for it
			$c2_sql = "INSERT INTO 202_tracking_c2 SET c2='".$mysql['c2']."'";
			$c2_result = _mysql_query($c2_sql) ; //($c2_sql);
			$c2_id = mysql_insert_id();
			return $c2_id;    
		}
	}
	
	//this returns the c3 id
	function get_c3_id($c3) {
		
		//only grab the first 50 charactesr of c3
		$c3 = substr($c3, 0, 50);
		$mysql['c3'] = mysql_real_escape_string($c3);
		
		$c3_sql = "SELECT c3_id FROM 202_tracking_c3 WHERE c3='".$mysql['c3']."'";
		$c3_result = _mysql_query($c3_sql);
		$c3_row = mysql_fetch_assoc($c3_result);
		if ($c3_row) {
			//if this already exists, return the id for it
			$c3_id = $c3_row['c3_id'];
			return $c3_id;    
		} else {
			//else if this ip doesn't exist, insert the row and grab the id for it
			$c3_sql = "INSERT INTO 202_tracking_c3 SET c3='".$mysql['c3']."'";
			$c3_result = _mysql_query($c3_sql) ; //($c3_sql);
			$c3_id = mysql_insert_id();
			return $c3_id;    
		}
	}
	
	//this returns the c4 id
	function get_c4_id($c4) {
		
		//only grab the first 50 charactesr of c4
		$c4 = substr($c4, 0, 50);
		$mysql['c4'] = mysql_real_escape_string($c4);
		
		$c4_sql = "SELECT c4_id FROM 202_tracking_c4 WHERE c4='".$mysql['c4']."'";
		$c4_result = _mysql_query($c4_sql);
		$c4_row = mysql_fetch_assoc($c4_result);
		if ($c4_row) {
			//if this already exists, return the id for it
			$c4_id = $c4_row['c4_id'];
			return $c4_id;    
		} else {
			//else if this ip doesn't exist, insert the row and grab the id for it
			$c4_sql = "INSERT INTO 202_tracking_c4 SET c4='".$mysql['c4']."'";
			$c4_result = _mysql_query($c4_sql) ; //($c4_sql);
			$c4_id = mysql_insert_id();
			return $c4_id;    
		}
	}
	
	//this returns the location_id
	function get_location_id($ip_address) {

		if (geoLocationDatabaseInstalled() == true) { 
			$clean['ip_address'] = ip2long($ip_address);
			$mysql['ip_address'] = mysql_real_escape_string($clean['ip_address']);
			$location_sql = "SELECT location_id FROM 202_locations_block WHERE location_block_ip_from >= '".$mysql['ip_address']."' AND location_block_ip_to <= '".$mysql['ip_address'] ."'";
			$location_row = memcache_mysql_fetch_assoc($location_sql);
			$location_id = $location_row['location_id'];
			return $location_id;
		} else {
			return 0;	
		}
	}
	
	function get_platform_and_browser_id() {
		$br = new Browser;
		$id['platform'] = $br->Platform;
		$id['browser'] = $br->Browser; 
		return $id;      
	}
}
function _mysql_query($sql) {
	
	$result = mysql_query($sql) or die(mysql_error() . '<br/><br/>' . $sql);
	return $result;
	
}

//for the memcache functions, we want to make a function that will be able to store al the memcache keys for a specific user, so when they update it, we can clear out all the associated memcache keys for that user, so we need two functions one to record all the use memcache keys, and another to delete all those user memcahces keys, will associate it in an array and use the main user_id for the identifier.


function memcache_set_user_key($sql) { 

	if (AUTH::logged_in() == true) { 
	
		global $memcache;
	
		$sql = md5($sql);
		$user_id = $_SESSION['user_id'];
		
		$getCache = $memcache -> get($user_id);
		
		$queries = explode(",",$getCache);
		
		if (!in_array( $sql, $queries ) ) {
		
			$queries[] = $sql;
		
		}
		
		$queries = implode(",", $queries);
		
		$setCache = $memcache -> set ($user_id, $queries);
		
	}	

}


function memcache_delete_user_keys() {

	/*global $memcache;

	$user_id = $_SESSION['user_id'];
	
	$queryKeys = explode(",", $memcache -> get($user_id));
	
	foreach ($queryKeys as $deletedKey) {
		if ($deletedKey != '') { 
			$memcache -> delete($deletedKey);
		}
	}*/

}


function memcache_mysql_fetch_assoc( $sql, $allowCaching = 1, $minutes = 5 ) {
	
	global $memcacheWorking, $memcache;

	if ($memcacheWorking == false) { 
		
		$result = _mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		return $row;
	} else {

		if( $allowCaching == 0 ) {
			$result = _mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			return $row;
		} else {

			// Check if its set
			$getCache = $memcache->get( md5( $sql ) );
			
			if( $getCache === false ) {
				// cache this data
				$fetchArray = mysql_fetch_assoc( _mysql_query( $sql ) );
				$setCache = $memcache->set( md5( $sql ), serialize( $fetchArray ), false, 60*$minutes  );
				
				//store all this users memcache keys, so we can delete them fast later on
				memcache_set_user_key($sql);
				
				return $fetchArray;
			
			} else {
			
				// Data Cached
				return unserialize( $getCache );
			}
		}
	}
}

function foreach_memcache_mysql_fetch_assoc( $sql, $allowCaching = 1 ) {
	 
	global $memcacheWorking, $memcache;
	
	if ($memcacheWorking == false) { 
		$row = array();
		$result = _mysql_query($sql) ; //($sql);
		while ($fetch = mysql_fetch_assoc($result)) {
			$row[] = $fetch;
		}
		return $row;
	} else {

		if( $allowCaching == 0 ) {
			$row = array();
			$result = _mysql_query($sql) ; //($sql);
			while ($fetch = mysql_fetch_assoc($result)) {
				$row[] = $fetch;
			}
			return $row;
		} else {
			
			$getCache = $memcache->get( md5( $sql ) );
			if( $getCache === false ) { 
				//if data is NOT cache, cache this data
				$row = array();     
				$result = _mysql_query($sql) ; //($sql);
				while ($fetch = mysql_fetch_assoc($result)) {
					$row[] = $fetch;
				}
				$setCache = $memcache->set( md5( $sql ), serialize( $row ), false, 60*5 );  
				
				//store all this users memcache keys, so we can delete them fast later on
				memcache_set_user_key($sql);
				
				return $row;
			} else {
				//if data is cached, returned the cache data Data Cached
				return unserialize( $getCache );
			}
		}
	}
}

//try to connect to memcache server
if ( ini_get('memcache.default_port') ) { 
	
	$memcacheInstalled = true;
	$memcache = new Memcache;
	if ( @$memcache->connect($mchost, 11211) )  	$memcacheWorking = true;
	else 												$memcacheWorking = false;
	
}


//connect to the mysql database, if it couldn't connect error 
$dbconnect = @mysql_connect($dbhost,$dbuser,$dbpass); 
if (!$dbconnect) {  
	_die("<h2>Error establishing a database connection</h2>
			<p>This either means that the username and password information in your <code>202-config.php</code> file is incorrect or we can't contact the database server at <code>$dbhost</code>. This could mean your host's database server is down.</p>
			<ul> 
				<li>Are you sure you have the correct username and password?</li>
				<li>Are you sure that you have typed the correct hostname?</li>
				<li>Are you sure that the database server is running?</li>
			</ul> 
			<p>If you're unsure what these terms mean you should probably contact your host. If you still need help you can always visit the <a href='http://Prosper202.com/forum/'>Prosper202 Support Forums</a>.</p>
			"); }

//connect to the mysql database, if couldn't connect error
$dbselect = @mysql_select_db($dbname);
if (!$dbselect) { 
		_die("
				<h2>Can&#8217;t select database</h2>
				<p>We were able to connect to the database server (which means your username and password is okay) but not able to select the <code>$dbname</code> database.</p>
				<ul>
				<li>Are you sure it exists?</li>
				<li>Does the user <code>$dbuser</code> have permission to use the <code>$dbname</code> database?</li>
				<li>On some systems the name of your database is prefixed with your username, so it would be like username_Prosper202. Could that be the problem?</li>
				</ul>
				<p>If you don't know how to setup a database you should <strong>contact your host</strong>. If all else fails you may find help at the <a href='http://Prosper202.com/forum/'>Prosper202 Support Forums</a>.</p>
			    "); }



