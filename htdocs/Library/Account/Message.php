<?php

	class Message {
		private $db;
		private $tablename = "messages";

		public function __construct(&$db = null) {
			$this->db = $db == null ? new Database () : $db;
		}

		public function getMessage($cols = "*", $where = null) {
			$queryStr = "SELECT {$cols} FROM {$this->tablename}" . ($where == null ? "" : " WHERE {$where}");
			$data = null;
			if($result = $this->db->result($queryStr)) {
				$index = 0;

				while($row = $result->fetch_assoc()) {
					foreach($row as $key => $val) {
						$data[$index][$key] = $val;
					}
					$index++;
				}
			}
			return $data;
		}

		public function createMessage($keyVal) {
			$queryStr = "INSERT INTO {$this->tablename} (`create_date`";
			$insertVal = " VALUES (CURDATE()";
			foreach($keyVal as $key => $val) {
				$queryStr .= ",`{$key}`";
				$insertVal .= ",'{$val}'";
			}
			$queryStr .= ")" .$insertVal . ")";
			if( $this->db->result($queryStr) ) return $this->db->insert_id;
			return false;
		}

		public function updateMessage($keyVal, $where) {
			$queryStr = "UPDATE {$this->tablename} SET ";
			foreach($keyVal as $key => $val) {
				$queryStr .= "`{$key}`='{$val}',";
			}
			$queryStr = substr($queryStr, 0, strlen($queryStr) - 1) . " WHERE {$where}";
			if($this->db->result($queryStr)) return true;
			return false;
		}

		public function delete($where) {
			$queryStr = "DELETE FROM {$this->tablename} WHERE {$where}";
			if($this->db->result($queryStr)) return true;
			return false;
		}	
	}
?>