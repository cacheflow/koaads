<?php
	class Setting {
		private $db;
		private $userid;
		
		private $showEmail = null;
		private $showName = null;
		private $showBirthday = null;
		private $showGender = null;

		private $warnings = 0;
		private $banned = 0;

		public function __construct(&$db, $userid){
			$this->db = $db == null ? new Database() : $db;
			$this->userid = $userid;

			$data = $this->select();
			$this->showEmail = $data[0]['showEmail'];
			$this->showName = $data[0]['showName'];
			$this->showBirthday = $data[0]['showBirthday'];
			$this->showGender = $data[0]['showGender'];

			$this->warnings = $data[0]['warnings'];
			$this->banned = $data[0]['banned'];
		}

		public function __get($attr){
			if(isset($this->attr))
				return $this->$attr;
			else
				return null;
		}

		public function select($cols = '*', $where = null){
			$queryStr = "SELECT {$cols} FROM settings WHERE " . ($where == null ? "`id`='{$this->userid}'" : $where);
			$data = null;
			$index = 0;
			if($result = $this->db->result($queryStr)){
				while($row = $result->fetch_assoc()){
					$data[] = $row;
				}
			}
			return $data;
		}

		public function createSetting($userid, $keyVal = null) {
			$queryStr = "INSERT INTO settings (`id`";
			$values = " VALUES ('{$userid}'";
			if($keyVal != null)
				foreach ($keyVal as $key => $val) {
					$queryStr .= ",`{$key}`";
					$values .= ",'{$val}'"; 
				}
			$queryStr .= ")" . $values . ")";

			if($this->db->result($queryStr)) {
				$this->userid = $userid;
				return true;
			}
			return false;
		}

		public function delete($userid = null){
			$queryStr = "DELETE FROM settings WHERE '" . ($userid == null ? $this->userid : $userid) . "'";
			if($this->db->result($queryStr)) return true;
			return false;
		}		

		public function update($keyVal, $userid = null){
			$queryStr = "UPDATE settings SET ";
			foreach ($keyVal as $key => $val) {
				$queryStr .= "`{$key}`='{$val}',";
			}
			$queryStr = rtrim($queryStr, ',');
			$queryStr .= " WHERE `setid` = '" . ($userid == null ? $this->userid : $userid) . "'"; 

			if($this->db->result($queryStr)) return true;
			return false;
		}
	}
?>