<?php
	class Listing {
		private $db;
		private $tablename = "listings";
		
		public function __construct(&$db) { 
			$this->db = $db;
		}

		//active || is for if user wants to make it live right away or save it
	/********************************
	 *								*
	 *		Listing Interface		*
	 *								*
	 *******************************/
		public function getListing($cols = "*", $where = null) {

			$data = null;
			$index = 0;
			$queryStr = "SELECT {$cols} FROM {$this->tablename}";
			$queryStr .= ($where != null ? " WHERE {$where}" : "");

			if($listResult = $this->db->result($queryStr)) {
				while($listRow = $listResult->fetch_assoc()) {
					if($listRow['active'] == 1) {
						//initial listing info
						$data[$index] = $listRow;
						
						//Selecting listing author
						$queryStr = "SELECT `username` FROM accounts WHERE `id` = {$listRow['id']}";
						$result = $this->db->result($queryStr); //apparently chaining is broke
						$row = $result->fetch_assoc();//
						$data[$index]['author'] = $row['username'];
						
						//Selecting categoryname 
						$queryStr = "SELECT t1.parentid, t1.name, t2.name AS parentname FROM categories AS t1, categories AS t2 WHERE t1.id = '{$listRow['categoryid']}' AND t1.parentid = t2.id";
						$result = $this->db->result($queryStr); //
						$row = $result->fetch_assoc();//
						$data[$index]['categoryname'] = $row['name'];
						$data[$index]['parentid'] =  $row['parentid'];
						$data[$index]['parentname'] = $row['parentname'];

						//Selecting address
						if($listRow['addressid'] > 0) {
							$queryStr = "SELECT `name`, `type`, `address`, `city`, `state`, `zipcode`, `geoid` FROM address WHERE `id`='{$listRow['addressid']}'";
							$result = $this->db->result($queryStr); //
							$row = $result->fetch_assoc();//
							$data[$index]['address_name'] = $row['name'];
							$data[$index]['address_type'] = $row['type'];
							$data[$index]['address_street'] = $row['address'];
							$data[$index]['address_city'] = $row['city'];
							$data[$index]['address_state'] = $row['state'];
							$data[$index]['address_zipcode'] = $row['zipcode'];

							if($row['geoid'] > 0) {
								$queryStr = "SELECT `lat`, `lng` FROM geocodes WHERE `id` = '{$row['geoid']}'";
								$result = $this->db->result($queryStr); //
								$row = $result->fetch_assoc();//
								$data[$index]['lat'] = $row['lat'];
								$data[$index]['lng'] = $row['lng']; 
							}
						}

						//Selecting pictures (since there can be mutiple, we run an inner loop) [Worst Case: Big O(n^2)]  not accounting the looping that will take place in front
							$queryStr = "SELECT `id`,`path`,`caption` FROM pictures WHERE "; 
							if($listRow['pictureid'] != 1)
								$queryStr .= "`userid`='{$listRow['userid']}' AND `listingid`='{$listRow['id']}' ORDER BY `id`='{$listRow['pictureid']}' DESC, `id` ASC";
							else
								$queryStr .= "`id`='1'";
							$result = $this->db->result($queryStr);
							while($row = $result->fetch_assoc()) {
								//This is the chosen cover picture
								$data[$index]['picture_id'][] = $row['id'];
								$data[$index]['picture_path'][] = $row['path'];
								$data[$index]['picture_caption'][] = $row['caption'];
							}

						//whew! finished getting ONE listing item 
						//wow this algorithm is going to fail fantastically and miserably in scaling, but it will work
						$index++;
					}
				}
			}

			return $data;
		}

		public function newListing($keyVal)
		{
			$queryStr = "INSERT INTO {$this->tablename} (`create_date`";
			$values = " ) VALUES ( CURDATE()";
			foreach($keyVal as $key => $val) {
				$queryStr .= ",`{$key}`";
				$values .= ",'{$val}'";
			}
			$queryStr .= "{$values})";
			if($this->db->result($queryStr)) return $this->db->insert_id;
			return false;
		}

		public function updateListing($keyVal, $where) {
			$queryStr = "UPDATE {$this->tablename} SET ";
			foreach($keyVal as $key => $val)
				$queryStr .= "`{$key}`='{$val}',";

			$queryStr = substr($queryStr, 0, strlen($queryStr) - 1) . " WHERE {$where}";

			if($this->db->result($queryStr)) return true;
			return false;
		}

		public function deleteListing($where) {
			$queryStr = "DELETE FROM {$this->tablename} WHERE {$where}";
			if($this->db->result($queryStr)) return true;
			return false;		
		}
	}
?>