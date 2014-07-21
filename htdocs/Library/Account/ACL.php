<?php
	class ACL
	{
		private $db = null;

		private $userid = 0;
		private $userRole = 5;
		private $role_perms = array();
		private $user_perms = array();
		
		public function __construct(&$db = null, $userid = null){
			$this->db = $db == null ? new Database() : $db;

			//Checking for a logged in member or everybody else
			if(isset($_SESSION['uid']))
			{
				$this->userid = ( $userid == null ? $_SESSION['uid'] : intval($userid) );
		        $this->userRole = $this->getUserRole($userid);  
	    	}
	        $this->buildACL(); 
		}

		private function buildACL(){
			//5 is associated with everybody else
			if($this->userRole != 5 )
				$this->user_perms = $this->initUserPerms($this->userid);	
			$this->role_perms = $this->initRolePerms($this->userRole);
		}


/****************************
 *							*
 *     PERM INTERFACE 		*
 *							*
 ***************************/
	public function checkPermissions($key) {
		//check user permissions first since it takes precedence over inherited role permissions
		if(isset($this->user_perms["{$key}"])) {
			return true;
		}
		else if(isset($this->role_perms["{$key}"])) {
			return true;
		}
		return false;
	}
	

/*********************************
 *								 *
 *		Permission Builders		 *
 *								 *
 *********************************/
		private function getUserRole($userid){
			$queryStr = "SELECT `roleid` FROM accounts WHERE `id` = '{$userid}'";
			if($result = $this->db->result($queryStr)){
				if($row = $result->fetch_assoc())
					return $row['roleid'];
			}
			return false;
		}

		//Creates a default set of permissions on selected hierachial tier
			//where this forms the subset of the total summation of permissions
			//in accordance with 2003 RBAC standard for `limited role hierarchies`
		private function initRolePerms($roleid){
			$queryStr = "SELECT perm.id, perm.name, perm.key
							FROM acl_roles AS role, acl_permissions AS perm, acl_role_permissions AS role_perm 
								WHERE role.id=role_perm.roleid AND perm.id = role_perm.permid AND role.id = '{$roleid}'";

			$data = null;
			if($result = $this->db->result($queryStr)){
				while($row = $result->fetch_assoc()){
					$data[$row['key']]['id'] = $row['id'];
					$data[$row['key']]['name'] = $row['name'];
				}
			}
			return $data;
		}


		private function initUserPerms($userid){
			$queryStr = "SELECT perm.id, perm.name, perm.key
						 FROM  acl_permissions AS perm, acl_user_permissions AS user_perm
			 			 WHERE user_perm.userid = '{$userid}' AND perm.id = user_perm.permid";
			$data = null;
			if($result = $this->db->result($queryStr)){
				while($row = $result->fetch_assoc()){
//Check permissions with isset($role_perms['permKey'])
					$data[$row['key']]['id'] = $row['id'];
					$data[$row['key']]['name'] = $row['name']; 
				}
				return $data;
			}
			return $data;
		}


/****************************
 *							*
 *     ACL_PERMS TABLE 		*
 *							*
 ***************************/
		public function getPermission($permid = null) {
			//get specific or all if $permid == null
			$queryStr = "SELECT * FROM acl_permissions" . ( $permid != null ? " WHERE `id` = '{$permid}'" : "");
			$data = null;
			$index = 0;
			if($result = $this->db->result($queryStr)) {
				while($row = $result->fetch_assoc()) {
					$data[$index]['id'] = $row['id'];
					$data[$index]['key'] = $row['key'];
					$data[$index]['name'] = $row['name'];
					$index++;
				}
			}
			return $data;			
		}

		public function createPermission($permName, $permKey = null) {
			$permKey = ($permKey == null ? str_replace(" ", "_", strtolower($permName)) : $permKey);
			$queryStr = "INSERT INTO acl_permissions(`key`,`name`) VALUES ('{$permKey}', '{$permName}')";

			if($result = $this->db->result($queryStr)) return $this->db->insert_id;
			return false;
		}

		public function deletePermission($permid) {
			$queryStr = "DELETE FROM acl_permissions WHERE `id`='{$permid}'";
			if($this->db->result($queryStr)) return true;
			return false;
		}

		public function updatePermissions($permid, $keyVal) {
			$queryStr = "UPDATE acl_permissions SET";
			foreach ($keyVal as $key => $val) {
				$queryStr .= " `{$key}`='{$val}',";
			}
			$queryStr = rtrim($queryStr , ",") . " WHERE `id`='{$permid}'";
			if($this->db->result($queryStr)) return true;
			return false;
		}


/****************************
 *							*
 *     ACL_ROLES TABLE 		*
 *							*
 ***************************/
		public function getRole($roleid = null) {
			//get specific or all if $roleID == null
			$queryStr = "SELECT * FROM acl_roles" . ( $roleID != null ? " WHERE `id` = '{$roleid}'" : "");
			$data = null;
			$index = 0;
			if($result = $this->db->result($queryStr)) {
				while($row = $result->fetch_assoc()) {
					$data[$index]['id'] = $row['id'];
					$data[$index]['name'] = $row['name'];
					$index++;
				}
			}

			return $data;
		}

		public function createNewRole($name){
			$queryStr = "INSERT INTO acl_roles(`name`) VALUES ('{$name}')";
			if($result = $this->db->result($queryStr)) return $this->db->insert_id;
			return false;
		}

		public function deleteRole($roleid){
			$queryStr = "DELETE FROM acl_roles WHERE `id` = {'$roleid'}";
			if($this->db->result($queryStr)) return true;
			return false;
		}

		public function updateRole($roleid, $keyVal) {
			$queryStr = "UPDATE acl_roles SET";
			foreach ($keyVal as $key => $val) {
				$queryStr .= " `{$key}`='{$val}',";
			}
			$queryStr = rtrim($queryStr , ",") . " WHERE `id` = '{$roleid}'";
			if($this->db->result($queryStr)) return true;
			return false;
		}


/**********************************
 *								  *
 *     	ACL_ROLE_PERMS TABLE 	  *
 *								  *
 *********************************/
		public function getRolePerm($role_permid = null) {
			$queryStr = "SELECT role_perm.*, perm.key, perm.name 
						 FROM acl_role_permissions AS role_perm, acl_permissions AS perm 
						 WHERE role_perm.permid = perm.id" . ($role_permid != null ? " AND role_perm.id = '{$role_permid}'" : "");
			$data = null;
			$index = 0;
			if($result = $this->db->result($queryStr)) {
				while($row = $result->fetch_assoc()) {
					$data[$index]['id'] = $row['id'];
					$data[$index]['roleid'] = $row['roleid'];
					$data[$index]['permid'] = $row['permid'];
					$data[$index]['addedDate'] = $row['addedDate'];
					$data[$index]['key'] = $row['key'];
					$data[$index]['name'] = $row['name'];

					$index++;
				}
			}
			return $data;
		}

		public function updateRolePerm($role_permid, $keyVal){
			$queryStr = "UPDATE acl_role_permissions SET";
			foreach ($keyVal as $key => $val){
				$queryStr .= " `{$key}`='{$val}',";
			}

			$queryStr = rtrim($queryStr , ",") . " WHERE `id`='{$role_permid}'";
			if($this->db->result($queryStr)) return true;
			return false;
		}

		public function addRolePerm($roleid, $permid) {
			$queryStr = "INSERT INTO acl_role_permissions(`roleid`, `permid`, `addedDate`) VALUES ('{roleid}','{permid}', CURDATE())";
			if($this->db->result($queryStr)) return $this->db->insert_id;
			return false;
		}

		public function deleteRolePerm($role_permid) {
			$queryStr = "DELETE FROM acl_roles_permissions WHERE `id`='{$role_permid}'";
			if($this->db->result($queryStr)) return true;
			return false;
		}


/**********************************
 *								  *
 *     	ACL_USER_PERMS TABLE 	  *
 *								  *
 *********************************/
		public function getUserPerm($user_permid = null){
			$queryStr = "SELECT user_perm.*, perm.key, perm.name 
						 FROM acl_user_permissions AS user_perm, acl_permissions AS perm
						 WHERE user_perm.permid = perm.id" . ($user_permid == null ? " AND `id`='{$user_permid}'" : "");
			$data = null;
			$index = 0;
			if($result = $this->db->result($queryStr)){
				while($row = $result->fetch_assoc()){
					$data[$index]['id'] = $row['id'];
					$data[$index]['userid'] = $row['userid'];
					$data[$index]['permid'] = $row['permid'];
					$data[$index]['key'] = $row['key'];
					$data[$index]['name'] = $row['name'];
					$data[$index]['value'] = $row['value'];
					
					$index++;
				}
			}
			return $data;		
		}
	
		public function updateUserPerm($user_permid, $keyVal) {
			$queryStr = "UPDATE acl_user_permissions SET";
			foreach ($keyVal as $key => $val) {
				$queryStr .= " `{$key}`='{$val}',";
			}
			$queryStr = rtrim($queryStr , ",") . " WHERE `id`='{$user_permid}'";
			if($this->db->result($queryStr)) return true;
			return false;
		}

		public function addUserPerm($userid, $permid) {
			$queryStr = "INSERT INTO acl_user_permissions(`userid`, `permid`, `addedDate`, `value`) VALUES ('{userid}','{permid}', CURDATE(), '1')";
			if($result = $this->db->result($queryStr)) return $this->db->insert_id;
			return false;
		}

		public function deleteUserPerm($user_permid) {
			$queryStr = "DELETE FROM acl_user_permissions WHERE `id`='{$user_permid}'";
			if($this->db->result($queryStr)) return true;
			return false;
		}
}