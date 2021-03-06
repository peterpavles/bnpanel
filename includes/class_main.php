<?php/* For licensing terms, see /license.txt *//** * @todo This class should be changed to static because we don't need an instance of this object * @todo Rename the class to App, so we can load functions like this: App::getCurrentUser  *  */ 
class main {	//Post variables
	public $postvar = array();		//Get Variables	public $getvar  = array(); 		public function __construct() {	}		public function get_variable($variable) {		if (isset($this->getvar[$variable])) {					return $this->getvar[$variable];		}		return false;	}		public function post_variable($variable) {		if (isset($this->postvar[$variable])) {			return $this->postvar[$variable];		}		return false;	}			public function cron() {		global $db;				//@todo clean logs				/*		$result = $db->query("SELECT id  FROM `<PRE>logs` ORDER BY id DESC LIMIT 1");		$log_count = $db->fetch_array($result);				if ($log_count['id'] > 1000) {						//$sql = "DELETE FROM  `<PRE>logs` WHERE id > ";		//	$db->query($sql);		}		*/			}			public static function stringToArray($string) {        $args = $string;        if(count($args) == 1 && !is_array($args)){        (array)$args = array_unique(array_map('trim',array_diff(explode(',',strtr($args.',',';|-',',,,')),array(''))));        }        return $args;    }    public static function toArray() {        $args = func_get_args();        return is_array($args[0]) ? $args[0] : (func_num_args() === 1 ? $this->stringToArray($args[0]) : $args);    }
	public function cleaninteger($var){ # Transforms an Integer Value (1/0) to a Friendly version (Yes/No)
	     $patterns[0] = '/0/';
         $patterns[1] = '/1/';
         $replacements[0] = 'No';
         $replacements[1] = 'Yes';
         return preg_replace($patterns, $replacements, $var);
	}
	public function cleanwip($var){ # Cleans v* from the version Number so we can work
	     if(preg_match('/v/', $var)) {
	     	$wip[0] = '/v/';
	     	$wipr[0] = '';
	     	$cleaned = preg_replace($wip, $wipr, $var);
	     	return $cleaned;
	     } else {	     	return $var; #Untouched
	     }	}
	public function error($array) {
		echo "<strong>ERROR<br /></strong>";
		foreach($array as $key => $data) {
			echo "<strong>". $key . ":</strong> ". $data ."<br />";
		}
		echo "<br />";
	}	
	public function redirect($url = '', $headers = 0, $long = 0) { # Redirects user, default headers
		if(!$headers) {			global $main, $db;			$main->clearToken();			if(empty($url)) {			
				$url = $db->config('url');			}			header("Location: ". $url);	# Redirect with headers			exit;
		} else {
			echo '<meta http-equiv="REFRESH" content="'.$long.';url='.$url.'">'; # HTML Headers
		}
	}
		/**	 *  Shows error default, sets error if $error set	 */
	public function errors($error = null, $clean = false) {		global $style;			if ($clean) {			$_SESSION['errors'] = null;		}	
		if(!$error) {
			if (isset($_SESSION['errors'])) {
				return $_SESSION['errors'];
			}
		} else {			if(!empty($error)) {				$other = $_SESSION['errors'];				if (!empty($other)) {					$other .='<br />';							}
				$_SESSION['errors'] = $other.$error;			}
		}		$error = '<div class="alert-message info">'.$error.'</div><div style="clear:both"></div>';				$style->messages[] = $error;		$style->assign('messages', $style->messages);	}	
	public function table($header, $content = 0, $width = 0, $height = 0) { // Returns the HTML for a Table
		global $style;		$props = '';
		if ($width) {
			$props = "width:".$width.";";		}		
		if ($height) {
			$props .= "height:".$height.";";
		}
		$array['PROPS'] 	= $props;
		$array['HEADER'] 	= $header;
		$array['CONTENT'] 	= $content;
		$array['ID'] =rand(0,999999);
		$link = INCLUDES."../themes/". THEME ."/tpl/table.tpl";
		if (file_exists($link)) {
			$tbl = $style->replaceVar("../themes/". THEME ."/tpl/table.tpl", $array);
		} else {
			$tbl = $style->replaceVar("tpl/table.tpl", $array);
		}
		return $tbl;
	}
	public function sub($left, $right) { # Returns the HTML for a THT table
		global $style;
		$array['LEFT'] = $left;
		$array['RIGHT'] = $right;
		$link = INCLUDES."../themes/". THEME ."/tpl/sub.tpl";
		if(file_exists($link)) {
			$tbl = $style->replaceVar("../themes/". THEME ."/tpl/sub.tpl", $array);
		} else {
			$tbl = $style->replaceVar("tpl/sub.tpl", $array);
		}
		return $tbl;
	}

	public function done() { # Redirects the user to the right part
		global $main;
		foreach($main->getvar as $key => $value) {
			if($key != "do") {
				if($i) {
					$i = "&";
				} else {
					$i = "?";
				}
				$url .= $i . $key . "=" . $value;
			}		}
		$main->redirect($url);
	}	
	public function check_email($email) {
		if($this->validEmail($email)) {
			return true;
		} else {
			return false;
		}
	}
	/**	 * Creates an input	 * @param string	label	 * @param string	name	 * @param bool		true if the checkbox will be checked	 * @return string html	 * 	 */	public function createInput($label, $name, $value) {		$html = $label.' <input type="text" name="'.$name.'" value="'.$value.'"> <br/>';		return $html;	}		/**	 * Creates a checkbox	 * @param string	label	 * @param string	name	 * @param bool		true if the checkbox will be checked	 * @return string html	 * 	 */	public function createCheckbox($label, $name, $checked = false) {		if ($checked == true) {			$checked = 'checked="'.$checked.'"';		} else {			$checked = '';		}		if(empty($label)) {			$label = '';		} else {			$label = $label.': ';		}		$html = $label.'<input type="checkbox" name="'.$name.'" '.$checked.' ><br />';				//$html = $label.'<label for="'.$name.'">'.$label.'</label><input type="checkbox"  id="'.$name.'" name="'.$name.'" '.$checked.' >';		return $html;	}		/**	 * @todo Function deprecated use createSelect instead	 */
	public function dropDown($name, $values, $default = 0, $top = 1, $class = "", $parameter_list = array()) { # Returns HTML for a drop down menu with all values and selected		$html = '';			if ($top) {			$extra = '';			foreach($parameter_list as $key=>$parameter) {				$extra .= $key.'="'.$parameter.'"';			}
			$html = '<select name="'.$name.'" id="'.$name.'" class="'.$class.'" '.$extra.'>';
		}
		if($values) {
			foreach($values as $key => $value) {
				$html .= '<option value="'.$value[1].'"';
				if($default == $value[1]) {
					$html .= 'selected="selected"';				}
				$html .= '>'.$value[0].'</option>';
			}
		}
		if($top) {
			$html .= '</select>';
		}
		return $html;
	}		/**	 * New simpler version of the dropDown function	 * @param 	string	name of the select tag	 * @param	array	values with this structure array(1=>'Item 1', 2=>'Item 2')	 * @param 	array	extra information to add in the select i.e onclick, onBlur, etc	 * @param	bool	show or not a blank item	 * @return	html	returns the select html  	 */	public function createSelect($name, $values, $default = 0, $parameter_list = array(), $show_blank_item = true) {				$extra = '';		foreach($parameter_list as $key=>$parameter) {			$extra .= $key.'="'.$parameter.'"';		}		$html = '<select name="'.$name.'" id="'.$name.'" '.$extra.'>';			if ($show_blank_item) {			$html .= '<option value="">-- Select --</option>';		}		if($values) {			foreach($values as $key => $value) {				if(is_array($value) && isset($value['name'])) {					$value = $value['name'];				}				$html .= '<option value="'.$key.'"';				if($default == $key) {					$html .= 'selected="selected"';				}				$html .= '>'.$value.'</option>';			}		}				$html .= '</select>';				return $html;	}		
	public function folderFiles($link) { # Returns the filenames of a content in a folder
		$folder = $link;
		if ($handle = opendir($folder)) { # Open the folder
			while (false !== ($file = readdir($handle))) { # Read the files
				if($file != "." && $file != ".." && $file != ".svn" && $file != "index.html") { # Check aren't these names
					$values[] = $file;
				}
			}
		}
		closedir($handle); #Close the folder
		return $values;
	}
	public function checkIP($ip) { # Returns boolean for ip. Checks if exists
		global $db, $main;
		$query = $db->query("SELECT id FROM `<PRE>users` WHERE ip = '{$db->strip($ip)}'");
		if($db->num_rows($query) > 0) {
			return false;
		}else {
			return true;	
		}
	}
		/**	 * Checks the staff permissions for a nav item	 * @param	int		permission id	 * @param 	int		user id	 */	 
	public function checkPerms($id, $user = 0) {
		global $main, $db, $staff;
		if(empty($user)) {
			$user =  $main->getCurrentStaffId();
		}						//Use now session to avoid useless query calls to the DB		if (isset($_SESSION['user_permissions'])) {								foreach($_SESSION['user_permissions'] as $value) {								if($value == $id) {								return false;					}			}			return true;		} else {						$staff_info = $staff->getStaffById($user);			
			if (!empty($staff_info)) {										$perms = explode(",", $staff_info['perms']);												$_SESSION['user_permissions'] = $perms;								foreach($perms as $value) {					if($value == $id) {												return false;						}									}					return true;	
			} else {				$array['Error'] = "Staff member not found";				$array['Staff ID'] = $id;				$main->error($array);
				return true;
			}		}
	}		/**	 * Checks the credentails of the client and logs in, returns true or false	 * 	 */
	public function clientLogin($username, $pass) {
		global $db, $main, $user;			
		if(isset($user) && isset($pass) && $user->validateUserName($username) && $user->validatePassword($pass)) {			$user_info	= $user->getUserByUserName($username);						if (is_array($user_info) && !empty($user_info)) {				if ($user_info['status'] == USER_STATUS_ACTIVE) {										if(md5(md5($pass).md5($user_info['salt'])) == $user_info['password']) {													//Regenerate the session id						session_regenerate_id();																	$_SESSION['clogged'] 	= 1;											$data['password'] 		= null;						$data['salt'] 			= null;						//Save all user in this session						$_SESSION['cuser'] 		= $user_info; 						$this->addLog("USER LOGIN SUCCESSFUL $username");																							return true;					} else { 						$main->errors('Incorrect password!');					}				} else {					$main->errors('Your account is not active');				}			} else {				$main->errors('User does not exist');			}		}				$this->addLog("USER LOGIN FAILED $username");			return false;			}
	public function staffLogin($user, $pass) { # Checks the credentials of a staff member and returns true or false
		global $main, $staff;			$date = time();
		if(!empty($user) && !empty($pass)) {
			$staff_info = $staff->getStaffUserByUserName($user);
			if(!empty($staff_info)) {				
				if(md5(md5($main->postvar['pass']) . md5($staff_info['salt'])) == $staff_info['password']) {					//Regenerate the session id					session_regenerate_id();						
					$_SESSION['logged'] 		= 1;														$staff_info['password'] 	= null;					$staff_info['salt'] 		= null;					$_SESSION['user'] 			= $staff_info;															$main->addLog("STAFF LOGIN SUCCESSFUL $user");					
					return true;
				}
			}
		}		$main->addLog("STAFF LOGIN FAILED $user");
		return false;				
	}
	
	/**
	* Validate an email address.
	* Provide email address (raw input)
	* Returns true if the email address has the email 
	* address format and the domain exists.
	* Thank you, Linux Journal!
	* http://www.linuxjournal.com/article/9585
	*/
	public function validEmail($email) {
	   $isValid = true;
	   $atIndex = strrpos($email, "@");
	   if (is_bool($atIndex) && !$atIndex)   {
		  $isValid = false;
	   }   else   {
		  $domain = substr($email, $atIndex+1);
		  $local = substr($email, 0, $atIndex);
		  $localLen = strlen($local);
		  $domainLen = strlen($domain);
		  if ($localLen < 1 || $localLen > 64)
		  {
			 // local part length exceeded
			 $isValid = false;		  }
		  else if ($domainLen < 1 || $domainLen > 255)
		  {
			 // domain part length exceeded
			 $isValid = false;
		  }
		  else if ($local[0] == '.' || $local[$localLen-1] == '.')
		  {
			 // local part starts or ends with '.'
			 $isValid = false;
		  }
		  else if (preg_match('/\\.\\./', $local))
		  {
			 // local part has two consecutive dots
			 $isValid = false;
		  }
		  else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
		  {
			 // character not valid in domain part
			 $isValid = false;
		  }
		  else if (preg_match('/\\.\\./', $domain))
		  {
			 // domain part has two consecutive dots
			 $isValid = false;
		  }
		  else if(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
					 str_replace("\\\\","",$local)))  {
			 // character not valid in local part unless			 // local part is quoted
			 if (!preg_match('/^"(\\\\"|[^"])+"$/',
				 str_replace("\\\\","",$local)))
			 {
				$isValid = false;
			 }
		  }
		  if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {
			 // domain not found in DNS
			 $isValid = false;
		  }
	   }
	   return $isValid;
	}
	
			/**	 * Generates a contry select	 * @param	string	code of the country	 * @return	string	html content	 */	public function	countrySelect($selected_item = '') {		//@todo this will be move into other place something like includes/library/text.lib.php		require_once 'text.php';		$selected_item = strtoupper($selected_item);		return $this->createSelect('country', $country, $selected_item);	}		// Order status	public function getOrderStatusList() {		return array(			ORDER_STATUS_ACTIVE						=> 'Active', 			ORDER_STATUS_WAITING_USER_VALIDATION 	=> 'Waiting user validation',						ORDER_STATUS_WAITING_ADMIN_VALIDATION	=> 'Waiting admin validation',			ORDER_STATUS_CANCELLED 					=> 'Cancelled',  			ORDER_STATUS_FAILED						=> 'Not syncronized with Panel', 			ORDER_STATUS_DELETED					=> 'Deleted', 			);	}		public function getInvoiceStatusList() {		return array(			INVOICE_STATUS_PAID				=> 'Paid', 			INVOICE_STATUS_CANCELLED		=> 'Cancelled',						INVOICE_STATUS_WAITING_PAYMENT	=> 'Pending', 			INVOICE_STATUS_DELETED			=> 'Deleted'			);	}		public function getUserStatusList() {		return array(			USER_STATUS_ACTIVE						=> 'Active', 			USER_STATUS_SUSPENDED 					=> 'Suspend', 			USER_STATUS_WAITING_ADMIN_VALIDATION	=> 'Waiting admin validation',  			//USER_STATUS_WAITING_PAYMENT				=> 'Waiting payment',  //should be remove only added for backward comptability			USER_STATUS_DELETED						=> 'Deleted', 			);	}			public function getTicketUrgencyList() {		return array(			TICKET_URGENCY_VERY_HIGH				=> array('name' =>'Very High', 	'color'=>'#FF4040'),			TICKET_URGENCY_HIGH 					=> array('name' =>'High',  		'color'=>'#FFB769'),			TICKET_URGENCY_MEDIUM					=> array('name' =>'Medium',   	'color'=>'#FFF988'),			TICKET_URGENCY_LOW						=> array('name' =>'Low',  		'color'=>'#FFFACD'),			);	}		public function getTicketStatusList() {		return array(			TICKET_STATUS_OPEN						=> 'Open', 			TICKET_STATUS_ON_HOLD 					=> 'On Hold', 			TICKET_STATUS_CLOSED					=> 'Closed', 			);	}				/**	 * Gets current user info 	 */	public function getCurrentUserInfo() {		if (isset($_SESSION['clogged']) && $_SESSION['clogged']) {			if (isset($_SESSION['cuser']) && is_array($_SESSION['cuser'])) {				return $_SESSION['cuser'];			}						}		return false;	}		/**	 * Gets the curren user id 	 */	public function getCurrentUserId() {		if (isset($_SESSION['clogged'])) {			if (isset($_SESSION['cuser']) && is_array($_SESSION['cuser'])) {				return intval($_SESSION['cuser']['id']);			}					}		return false;	}			/**	 * Gets current staff info 	 */	public function getCurrentStaffInfo() {		if ($_SESSION['logged']) {			if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {				return $_SESSION['user'];			}		}		return false;	}		/**	 * Gets the curren staff id 	 */	public function getCurrentStaffId() {		if (isset($_SESSION['logged'])) {			if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {				return intval($_SESSION['user']['id']);			}						}		return false;				}		public function getSupportNavigation() {		global $db;					$result = $db->query('SELECT * FROM <PRE>supportnav');		$menu_list = $db->store_result($result);		$new_menu_list = array();		foreach($menu_list as $menu) {			$new_menu_list[$menu['link']] = $menu;		}		return $new_menu_list;			}		public function getMainNavigation($force = false) {		global $db;			if (isset($_SESSION['main_menu']) && count($_SESSION['main_menu']) > 0 && $force == false) {			return $_SESSION['main_menu'];					} else {								$result = $db->query('SELECT * FROM <PRE>navbar');			$menu_list = $db->store_result($result);			$new_menu_list = array();			foreach($menu_list as $menu) {				$new_menu_list[$menu['link']] = $menu;			}			$_SESSION['main_menu'] = $new_menu_list;			return $new_menu_list;		}					}			public function getClientNavigation() {		global $db;		$sql = 'SELECT * FROM <PRE>clientnav'; 		$result = $db->query($sql);		$client_nav = array();		while ($row = $db->fetch_array($result, 'ASSOC')) {			$client_nav[$row['link']] = $row;		}				return $client_nav;	}		public function getAdminNavigation() {		if (isset($_SESSION['admin_menu']) && count($_SESSION['admin_menu']) > 0 ) {			return $_SESSION['admin_menu'];					} else {			global $db;						$result = $db->query('SELECT * FROM <PRE>acpnav');			$menu_list = $db->store_result($result);			$new_menu_list = array();			foreach($menu_list as $menu) {				$new_menu_list[$menu['link']] = $menu;			}						$_SESSION['admin_menu'] = $new_menu_list;			return $new_menu_list;		}	}		/**	 * Check if the link exist in the admin menu	 * @param	string	link 	 * @return  bool	true if sucess		 */	public function linkAdminMenuExists($link) {		$list = $this->getAdminNavigation();		if (isset($list[$link]) && $list[$link]['link'] == $link) {			return true;		} else {			//somebody is trying to hack			return false;		}	}		/**	 * Gets the list of subdomains by server id	 * @param	int		server id		 * @return	array	list of subdomains	 */	public function getSubDomainByServer($server_id) {		global $db;		$server_id = intval($server_id);		$sql = "SELECT id, subdomain FROM <PRE>subdomains WHERE server = {$server_id} ";		$result = $db->query($sql);		$array = array();		while($data = $db->fetch_array($result, 'ASSOC')) {			$array[$data['id']] = $data['subdomain'];		}			return $array;			}		public function getSubDomains() {		global $db;		$sql = "SELECT id, subdomain FROM <PRE>subdomains";		$result = $db->query($sql);		$array = array();		while($data = $db->fetch_array($result, 'ASSOC')) {			$array[$data['id']] = $data['subdomain'];		}			return $array;			}		function toDate($d, $format= 'Y-m-d') {				if (strtotime($d) === false || strtotime($d) === -1)			return $d;		return date($format, strtotime($d));	}		function getDateArray($date) {		return getdate(strtotime($date));	}		/**	 * Generates a random password	 */	public function generatePassword() {		$passwd = '';		for ($digit = 0; $digit < 6; $digit++) {			$r = rand(0,1);			$c = ($r==0)? rand(65,90) : rand(97,122);			$passwd .= chr($c);		}		return $passwd;	}		/**	 * Generates a random username	 */	public function generateUsername() {		$t = 6;		$user = '';		for ($digit = 0; $digit < $t; $digit++) {						$c =  rand(97,122);			$user .= chr($c);		}		return $user;	}		/**	 * Adds a log	 * @param	string	message to save in the log	 */			public function addLog($message, $show_message = false, $error_log = false) {		global $db;				$date = time();		//Tries to save the log as a staff user				if($this->getCurrentStaffId() != false) {			$user_id   	= $this->getCurrentStaffId();			$user_name 	= $this->getCurrentStaffInfo();			$user_name 	= $user_name['user'];		} elseif ($this->getCurrentUserId() != false) {			$user_id	= $this->getCurrentUserId();			$user_name 	= $this->getCurrentUserInfo();			$user_name 	= $user_name['user'];		/*} elseif (CRON == 1) {			$user_id = '0';			$user_name = 'Cron';*/		} else {			$user_id = '0';			$user_name = 'Anonymous';		}					$ip   		= $this->removeXSS($_SERVER['REMOTE_ADDR']);				$message 	= $db->strip($message);				if ($error_log) {			error_log("$date :: User id: $user_id, Username: $user_name, Message $message, IP $ip");		} else {			//Fires the database!						$db->query("INSERT INTO <PRE>logs (uid, loguser, logtime, ip, message) VALUES (				'{$user_id}',				'{$user_name}',				'{$date}',				'{$ip}',				'{$message}')");		}						if ($show_message) {			echo '<h3>'.$message.'</h3><br />';		}	}		/**	 * Gets the current token	 * @return	string	token string	 */	 	public function generateToken() {	//	if ($this->tokenExists()) {		//	$token = $this->getToken();	//	} else {			$token = md5(uniqid(rand(),TRUE));			$_SESSION['sec_token'] = $token;	//	}		return $token;	}			public function tokenExists() {		if (isset($_SESSION['sec_token']) && !empty($_SESSION['sec_token'])) {			return true;		}		return false;	}		public function getToken() {		if ($this->tokenExists()) {			return $_SESSION['sec_token'];		} else {		    return $this->generateToken();		}		return false;	}		/**	 * Cleans the token from the session  	 */	 	public function clearToken() {		$_SESSION['sec_token'] = null;		unset($_SESSION['sec_token']);		//$this->generateToken();	}			/**	 * Validates the token	 * @param	bool	clean the token or not 	 * @return	bool	true if sucess	 * 	 **/	 	 public function checkToken($clean_token = true) {			if (isset($this->postvar['_post_token'])) {					//var_dump('checkToken-->>'.$this->postvar['_post_token']); var_dump('qsess->'.$_SESSION['sec_token']);			if (isset($_SESSION['sec_token']) && isset($this->postvar['_post_token']) && $_SESSION['sec_token'] === $this->postvar['_post_token']) {				if($clean_token) {					$this->clearToken();					//$this->generateToken();				}				return true;			}		} elseif(isset($this->getvar['_get_token'])) {						if (isset($_SESSION['sec_token']) && isset($this->getvar['_get_token']) && $_SESSION['sec_token'] === $this->getvar['_get_token']) {				if ($clean_token) {					$this->clearToken();				}				return true;			}								}		if ($clean_token) {						$this->clearToken();		}		return false;	}		/**	 * Log outs a user/admin from the session	 */	 	public function logout($user_type) {		if ($user_type == 'client') {			$user_id = $this->getCurrentUserId();			$this->addLog('main:logout user_id #'.$user_id);		} else {			$user_id = $this->getCurrentStaffId();			$this->addLog('main:logout staff_id #'.$user_id);			$this->addLog('main:logout');			}						session_destroy();			}		function parseUrl($url) {	    $r  = "^(?:(?P<scheme>\w+)://)?";	    $r .= "(?:(?P<login>\w+):(?P<pass>\w+)@)?";	    $r .= "(?P<host>(?:(?P<subdomain>[\w\.]+)\.)?" . "(?P<domain>\w+\.(?P<extension>\w+)))";	    $r .= "(?::(?P<port>\d+))?";	    $r .= "(?P<path>[\w/]*/(?P<file>\w+(?:\.\w+)?)?)?";	    $r .= "(?:\?(?P<arg>[\w=&]+))?";	    $r .= "(?:#(?P<anchor>\w+))?";	    $r = "!$r!"; // Delimiters	   	    preg_match ( $r, $url, $out );    	return $out;	}		/**	 * Adds htmlentities to avoid XSS problems	 * @param	string	variable to clean	 * @return	string	clean variable	 */	function removeXSS($var) {		return htmlentities($var,ENT_QUOTES, 'UTF-8');	}			/**	 * Checks if the string is a valid md5	 * @param	string	 * @return	bool 	 */	function isValidMd5($md5) {		return !empty($md5) && preg_match('/^[a-f0-9]{32}$/', $md5);	}		/**	 * Checks if this is an AJAX request	 * @return	bool	true if success	 */	function isXmlHttpRequest() {		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';	}		/**	 * Checks the current user agent	 * @return	bool		 */	function checkUserAgent() {		if (isset($_SESSION['HTTP_USER_AGENT'])) {	    	if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT'])) {	        	/* Prompt for password */	        	return false;	    	}	    	return true;		} else {				    	$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);	    		    	return true;		}	}		/**	 * Validates a domain name	 * @param	string 	domain name	 * @return	bool	true if success	 */	function validDomain($domain) {		if (preg_match('/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $domain)) {		   return true;		} else {		   return false;		}				}			public function checkDir($dir) {		global $style;    	if (is_dir($dir)) {     		return $style->returnMessage('Your install directory still exists. Delete or rename it now', 'warning');		} else {			return "";		}	}		public function checkFilePermission($file){		global $style;		$filechk = substr(sprintf('%o', fileperms($file)), -3);				if ($filechk != 444) {			return $style->returnMessage('Configuration file (conf.inc.php) is still writable, please chmod it to 444!', 'warning');		} else {			return "";		}	}		/***	 * Used to generate the api	 */    public function randomString($length = 8, $possible = '0123456789bcdfghjkmnpqrstvwxyz') {            $string = "";            $i = 0;            while ($i < $length) {                $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);                if (!strstr($string, $char)) {                    $string .= $char;                    $i++;                }            }            return $string;    }        public function loadHookFiles($my_class_name) {    	$available_objects_to_hook = array('invoice', 'order', 'billing', 'addon');    	$classes = array();    	if (in_array($my_class_name, $available_objects_to_hook)) {    		    		require_once INCLUDES.'hooks/hook.php';    		$dir = INCLUDES.'hooks';    		if (is_dir($dir)) {	    		$modules = scandir($dir);	    						if (!empty($modules)) {		    		foreach($modules as $module) {		    			$module_dir = INCLUDES.'hooks/'.$module;		    					    					    			if (is_dir($module_dir) && !in_array($module, array('.', '..'))) {		    							    			$dir = INCLUDES.'hooks/'.$module;			    			$files = scandir($module_dir);			    			//main class i.e hooks/dolibarr/dolibarr.php			    			/*$module_main_class = INCLUDES.'hooks/'.$module.'/'.$module.'.php';			    			if (file_exists($module_main_class)) {			    				require_once $module_main_class;			    			}*/			    			if (!empty($files)) {				    			foreach($files as $file) {				    				$file = pathinfo($file);				    				if ($file['extension'] == 'php' && $file['filename'] != $module) {				    								    					$classes[] = 'hook_'.$module.'_'.$my_class_name;  					    					//Classes are loaded thanks to the autoload in compiler.php			    									    					/*$class_dir = $module_dir.'/'.$file['basename'];				    									    									    					require_once $class_dir;*/				    									    				}				    			}			    			}		    			}		    		}				}	    			    		return $classes;    		}    	}    	return false;    	    }}