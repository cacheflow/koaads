<?php
class Account
{
	private $db;
	private $tablename = "accounts";

	//OBJECTS
	private $aclObj = null;
	private $settingObj = null;
	private $messageObj = null;


	//ATTRIBUTES
	private $userid = null;
	private $username = null;
	private $email = null;

		private $first = null;
		private $last = null;
		private $birth_month = null;
		private $birth_day = null;
		private $birth_year = null;
		private $gender = null;

/************************ 
*						*
*      CONSTRUCTOR		*
*						*
************************/
	public function __construct(&$db = null){
		session_start();

		$this->db = $db == null ? new Database() : $db; 
		if(isset($_SESSION['uid'])) {
			$this->userid = $_SESSION['uid'];	
			$this->initValues();
					
			//Subclasses
			$this->aclObj = new ACL($db, $this->userid);
			$this->settingObj = new Setting($db, $this->userid);
			$this->messageObj = new Message($db);
		}

		//Adapters (however these are not needed everywhere)
		//$this->photoObj = new Photo($db);
		//$this->locationObj = new Location($db);
		//$this->listingObj = new Listing($db);

	}	

/************************ 
*						*
*       ACCESSORS		*
*						*
************************/
	public function __get($attr){
		return $this->$attr;
	}

/****************************** 
*							  *	
*	   SESSION MANAGEMENT	  *
*							  *
******************************/

	/* FUNCTION: createFromSession
	 * ARGUMENTS: (optional) &$db::Object - An instance of database object. If null, account will create a new handle.
	 * PURPOSE: Attempt to initialize an Account [object] from session
	 * RETURN: Account Object
	 * NOTES:
	 *******************************************/
	public static function createFromSession(&$db = null){
		$accObj = new Account($db);
		
		//User is logged in b/c thats the only function that creates fingerprint
		if(isset($_SESSION['fingerprint']))
		{
			//Checks to see if this login is not a browser hijack
			if(!Account::checkDigiPrint()) {
				$accObj->logout();

				if($_GET['thread'] != "login") {
					System::redirect(URL . "user/login");
				}
			}
					
		}

		return $accObj;
	}

	/* FUNCTION: createDigiPrint [PRIVATE]
	 * ARGUMENTS: $vals::String - values to make fingerprint with 
	 * PURPOSE: makes a fingerprint and registers it to $_SESSION
	 * RETURN: boolean::success/fail
	 * NOTES: Should be used in login, later we can salt it to prevent brute force and rainbow hash tables
	 *******************************************/
	private function createDigiPrint($vals = null){
		$sessid = session_id();
		if($vals == null){
			$vals = array($_SERVER['HTTP_USER_AGENT'], $_SERVER['HTTP_ACCEPT_LANGUAGE'], $_SERVER['HTTP_ACCEPT_CHARSET']);
		}

		if( $key = md5(implode(" ", $vals) . '_' . $sessid) ){
			$_SESSION['fingerprint'] = $key;
			return true;
		}
		
		return false;
	}

	/* FUNCTION: checkDigiPrint
	 * ARGUMENTS: $vals::String values to check $_SESSION['fingerprint'] with
	 * RETURN: boolean::success/fail
	 * NOTES: 
	 *******************************************/
	public function checkDigiPrint($vals = null){
		if($vals == null){
			$vals = array($_SERVER['HTTP_USER_AGENT'], $_SERVER['HTTP_ACCEPT_LANGUAGE'], $_SERVER['HTTP_ACCEPT_CHARSET']);
		}

		if(md5(implode(" " , $vals) . '_' . session_id()) == $_SESSION['fingerprint']){
			return true;
		}
		return false;
	}


/**********************
*				      *	
*	   INTERFACE	  *
*					  *
**********************/
	/* FUNCTION: login
	 * ARGUMENTS: 	 $email::String - user's email
	 *				 $password::String - plain text string password
	 *	  (optional) $remember::boolean - remember user next login 
	 * PURPOSE: makes a fingerprint and registers it to $_SESSION
	 *			creates $_SESSION['uid'] - user's id
	 *			creates $_SESSION['pid'] - user's privilege level
	 * RETURN: boolean - success/fail
	 * NOTES: Passwords are stored in sha1 format in database
	 ***************************************************************/
	public function login($email, $password){
		$q = "SELECT `id` FROM {$this->tablename} WHERE `email`='{$email}' AND `password`=sha1('{$password}')";

		if($result = $this->db->result($q)) {
			if($row = $result->fetch_assoc()) {
				session_regenerate_id(true);					

				//setting session variables
				$_SESSION['uid'] = $row['id'];
				$this->userid = $row['id'];
				if($this->createDigiPrint()) {
					return true;
				}
			}
		}
		return false;
	}

	public function logout(){
		unset($_SESSION['uid']);
		unset($_SESSION['fingerprint']);

		if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        // Finally, destroy the session.
        session_destroy();

		//Objects
        $this->aclObj = null;
 		$this->settingObj = null;       
		$this->messageObj = null;
        
        //Attributes
        $this->userid = null;
        $this->username = null;
		$this->email = null;

		$this->first = null;
		$this->last = null;
		$this->birth_month = null;
		$this->birth_day = null;
		$this->birth_year = null;
		$this->gender = null;

		return true;
	}

	public function register($username, $email, $pass){
        $queryStr = "INSERT INTO {$this->tablename} (`username`, `email`, `password`, `token` , `roleid`, `create_date`, `mod_date`)";
        $queryStr .= " VALUES ('{$username}', '{$email}', sha1('{$pass}'), '" . $this->generateToken() . "', '4', CURDATE(), CURDATE())";

        if ($this->db->result($queryStr)){
        	$this->userid = $this->db->insert_id;
            //creates a default settings entry with userid as key
            $this->settingObj->createSetting($this->userid);
            return true;
        }
        return false; 
	}

	public function updateAccountInfo($keyVal){
		$queryStr = "UPDATE {$this->tablename} SET `mod_date`=CURDATE()";
		foreach ($keyVal as $key => $val){
				$queryStr .= ",`{$key}`='{$val}'";
		}
		$queryStr .= " WHERE `id`='{$this->userid}'";
		if($this->db->result($queryStr)) return true;
		return false;
	}

	public function changePassword($newpass){
		$queryStr = "UPDATE {$this->tablename} SET `password` = SHA1('{$newpass}'), `mod_date`=CURDATE() WHERE `id`='{$this->userid}'";
		if($result = $this->db->result($queryStr)) return true;
		return false;
	}

	public function deleteUser($userid){
        $queryStr = "DELETE FROM {$this->table} WHERE `id`='{$userid}'";
        if ($this->db->result($queryStr)) return true;
        return false; 
    }

/******************************
*				      		  *	
*	    ACL INTERFACE   	  *
*					  		  *
******************************/
	public function checkPermissions($permKey) {
		return $this->aclObj->checkPermissions($permKey);
	}
/******************************
*				      		  *	
*	 SETTINGS INTERFACE 	  *
*					  		  *
******************************/
	public function getSetting($name) {
		return $this->settingObj->$name;
	}

	public function toggleSetting($name) {
		switch($name) {
			case 'showEmail':
			case 'showName':
			case 'showBirthday':
			case 'showGender':
				$this->settingObj->update(array($name => !$this->settingObj->$name));
				return true;
		}
		return false;
	}
/******************************
*				      		  *	
*	    MSG INTERFACE	  	  *
*					  		  *
******************************/
	public function createMessage($subject, $body, $to = null, $replyid = null) {
		$newMsg = array('from' => $this->userid, 'subject' => $subject, 'body' => $body);
		if($to != null) $newMsg['to'] = $to;
		if($replyid != null) $newMsg['replyid'] = $replyid;
		if($result = $this->messageObj->createMessage($newMsg)) return $result;
		return false; 
	}

	public function sendMessage($messageid, $to = null, $replyid = null) {
		$sendMsg = array('sent' => 1);
		if($replyid != null)
			$sendMsg['replyid'] = $replyid;
		if($to != null)
			$sendMsg['to'] = $to;

		$where = "`id`='{$messageid}'";
		return $this->messageObj->updateMessage($sendMsg, $where);
	}

	public function getInbox($messageid = null) { 
		$where = "`to`='{$this->userid}'" . ($messageid != null ? " AND `id`='{$messageid}'" : ""); 
		return $this->messageObj->getMessage("*", $where); 
	}

	public function getUnread() {
		$where = "`to`='{$this->userid}' AND `read`='0'";
		return $this->messageObj->getMessage("*", $where);
	}
	public function getSent() {
		$where = "`from`='{$this->userid}' AND `sent`='0'";
		return $this->messageObj->getMessage("*", $where);
	}

	public function getSpam() {
		$where = "`to`='{$this->userid}' AND `spam`='1'";
		return $this->messageObj->getMessage("*", $where);
	}

	public function getTrash() {
		$where = "`to`='{$this->userid}' AND `trash`='1'";
		return $this->messageObj->getMessage("*", $where);
	}

	public function editMessage($messageid, $subject, $body, $to = null, $replyid = null) {
		$editMsg = array('from' => $this->userid, 'subject' => $subject, 'body' => $body);
		if($to != null) $editMsg['to'] = $to;
		if($replyid != null) $editMsg['replyid'] = $replyid;
		$where = "`id`='{$messageid}'";
		if($this->messageObj->updateMessage($newMsg, $where)) return true;
		return false; 
	}

	public function markTrash($messageid) {
		$where = "`id`='{$messageid}'";
		if($this->messageObj->updateMessage(array("trash" => "1"), $where)) return true;
		return false;
	}

	public function unmarkTrash($messageid) {
		$where = "`id`='{$messageid}'";
		if($this->messageObj->updateMessage(array("trash" => "0"), $where)) return true;
		return false;
	}

	public function markSpam($messageid) {
		$where = "`id`='{$messageid}'";
		if($this->messageObj->updateMessage(array("spam" => "1"), $where)) return true;
		return false;
	}

	public function unmarkSpam($messageid) {
		$where = "`id`='{$messageid}'";
		if($this->messageObj->updateMessage(array("spam" => "0"), $where)) return true;
		return false;
	}

	public function deleteMessage($messageid) {
		$where = "`id`='{$messageid}'";
		return $this->messageObj->deleteMessage($where);
	}

	public function emptyTrash() {
		$where = "`trash`='1' AND `to`='{$this->userid}'";
		if($this->messageObj->deleteMessage($where)) return true;
		return false;
	}

/********************
*				    *	
*	   HELPERS	    *
*				    *
********************/
	private function initValues() {
		$queryStr = "SELECT `username`,`email`,`first`,`last`,`birth_month`,`birth_day`,`birth_year`,`gender`
					 FROM {$this->tablename} WHERE `id`='{$this->userid}' LIMIT 1";
		if($result = $this->db->result($queryStr)) {
			if($row = $result->fetch_assoc()) {
				$this->username = $row['username'];
				$this->email = $row['email'];

				$this->first = $row['first'];
				$this->last = $row['last'];
				$this->birth_month = $row['birth_month'];
				$this->birth_day = $row['birth_day'];
				$this->birth_year = $row['birth_year'];
				$this->gender = $row['gender'];
				return true;
			}
		}
		return false;
	}    

	private function generateToken() {
        $gen = md5(uniqid(mt_rand(), false));
        while($this->dupeToken($gen))
        	$gen = md5(uniqid(mt_rand(), false));
        return $gen;    
    }

    public function verifyToken($userid, $token) {
    	$queryStr = "SELECT `id` FROM {$this->tablename} WHERE `token`='{$token}' AND `id`='{$userid}'";
    	if($result = $this->db->result($queryStr)) return true;
    	return false;
    }

	private function dupeToken($token) {      
        $queryStr = "SELECT `id` FROM {$this->tablename} WHERE `token`='{$token}'";
        if($result = $this->db->result($queryStr))
        	if($result->num_rows > 0) return true;
        return false;
    }

/**************************
*				      	  *	
*	   STATIC HELPERS	  *
*					  	  *
**************************/
	public static function emailDupe($email) {
		$queryStr = "SELECT `email` FROM {$this->tablename} WHERE `email`='{$email}' LIMIT 1";
		if($this->db->result($queryStr)) return true;
		return false;
	}

	public static function usernameDupe($username) {
		$queryStr = "SELECT `username` FROM {$this->tablename} WHERE `username`='{$username}' LIMIT 1";
		if($this->db->result($queryStr)) return true;
		return false;
	}

}
?>