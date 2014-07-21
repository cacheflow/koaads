<?php
	class Location {
		private $db;
		private $tablename = "address";

		//GEOCODING
		private $geocode_key = "AIzaSyDauwi318AkQs8zXElFgPx0ibn5i8SMW1A"; //this is my own
		private $gecode_map_host = "maps.google.com";


		public function __construct(&$db = null) {
			$this->db = $db == null ? new Database() : $db; 
		}


		public function getAddress($cols = "*", $where = null) {
			$queryStr = "SELECT {$cols} FROM {$this->tablename}" . ($where != null ? " WHERE {$where}" : "");

			$data = null;
			if($result = $this->db->result($queryStr)) {
				while($row = $result->fetch_assoc()) {
					$data[] = $row;
				}
			}
			return $data;
		}

		public function createAddress($keyVal, $geocodify = false) {
			$queryStr = "INSERT INTO {$this->tablename} (";
			$values = ") VALUES (";
			foreach($keyVal as $key => $val) {
				$queryStr .= "`{$key}`,";
				$values .= "'{$val}',";
			}
			if($geocodify && !empty($keyVal['address']) && !empty($keyVal['city']) && !empty($keyVal['state']) && !empty($keyVal['zipcode'])) {
				$geoid = $this->createGeocode("{$keyVal['address']} {$keyVal['city']},{$keyVal['state']} {$keyVal['zipcode']}");
				$queryStr = "{$queryStr} `geoid`) {$values} '{$geoid}')" ;
			}
			else
				$queryStr = substr($queryStr, 0 , strlen($queryStr) - 1) . substr($values, 0, strlen($values) - 1) . ")";

			if($this->db->result($queryStr)) return $this->db->insert_id;
			return false;

		}

		public function updateAddress($keyVal, $where) {
			$queryStr = "UPDATE {$this->tablename} SET ";
			foreach($keyVal as $key => $val)
				$queryStr .= "`{$key}`='{$val}',"; 
			$queryStr = substr($queryStr, 0, strlen($queryStr) - 1 ) . " WHERE {$where}";

			if($this->db->result($queryStr)) return true;
			return false;
		}

		public function deleteAddress($where){
			$queryStr = "DELETE FROM {$this->tablename} WHERE {$where}";
			if($this->db->result($queryStr)) return true;
			return false;
		}

	/********************************
	 *								*
	 *	   	Geocode API			    *
	 *								*
	 *******************************/
		public function createGeocode($address){
			$delay = 0;
			$base_url = "http://{$this->geocode_map_host}/maps/geo?output=xml&key={$this->geocode_key}";
			$request_url = $base_url . "&q=" . urlencode($address);
			$pending = 0;

			while($pending > 30) {
				$xml = simplexml_load_file($request_url) or die("Error: Url Cannot be Loaded");
				$status = $xml->Response->Status->code;			

				if($status == '200') { //SUCCESS
					$pending = false;
					$coordinates = $xml->Response->Placemark->Point->coordinates;
					$coordinates = split(",", $coordinates);
					
					//insert into database
					$queryStr = "INSERT INTO geocodes (`lat`,`lng`) VALUES('{$coordinates[1]}','$coordinates[0]')";
					$this->db->result($queryStr);
					return $this->db->insert_id;
				}
				else if($status == '620') {
					$pending++;
					$delay += 100000; // 100000 microS = 1/10 S
				}
				else
					return false;
		
				usleep($delay);
			}
			return false;
		}

		public function getGeocode($addressid) {
			$queryStr = "SELECT t2.lat, t2.lng FROM address AS t1, geocodes AS t2 WHERE t1.geoid = t2.id AND t1.id='{$addressid}'";
			$result = $this->db->result($queryStr);
			if($result->num_rows() > 0) {
				while($row = $result->fetch_assoc()) {
					return $row;
				}
			}
			return false;
		}

		public function updateGeocode($keyVal, $where) {
			$queryStr = "UPDATE {$this->tablename} SET ";
			foreach($keyVal as $key => $val) 
				$queryStr .= "`{$key}`='{$val}',";
			$queryStr = substr($queryStr, 0, strlen($queryStr) - 1 ) . " WHERE {$where}";
			if($this->db->result($queryStr)) return true;
			return false;
		}

		public function deleteGeocode($where) {
			$queryStr = "DELETE FROM {$this->tablename} WHERE {$where}";
			if($this->db->result($queryStr)) return true;
			return false;
		}

	/********************************
	 *								*
	 *		Zipcodes Interface		*
	 *								*
	 *******************************/
		public function validZipcode($zipNum, $state = null, $city = null) {
			$queryStr = "SELECT `id` FROM zipcodes WHERE `zipcode`='{$zipcodeNum}'";
			$queryStr .= ( $state == null ? "" : " AND `state`='{strtoupper($state)}'" );
			$queryStr .= ( $city == null ? "" : " AND `city`='{strtolower($city)}'" );
	
			$result = $this->db->result($queryStr);
			if($result->num_rows() > 0) return true;
			return false;
		}

		public function getZipcode($cols = '*', $filter = null) {
			$queryStr = "SELECT {$cols} FROM zipcodes" . ( $filter != null ? "WHERE {$filter}" : "");
			$response = null;
			if($response = $this->db->result($queryStr))
				while($row = $response->fetch_assoc())
					$response[] = $row;
			return $response;
		}
		
		public function insertZipcode($zipcode, $city, $state, $county, $type){
			$queryStr = "INSERT INTO zipcodes (`zipcode`, `city`, `state`, `county`, `type`) 
				VALUES ('{$zipcode}','{$city}','{$state}','{$county}','{$type}')";
			if($this->db->result($queryStr)) return true;
			return false;
		}

		public function updateZipcode($keyVal, $filter) {
			$queryStr = "UPDATE zipcodes SET ";
			foreach($keyVal as $key => $val)
				$queryStr .= "`{$key}`='{$val}',";

			$queryStr = substr($queryStr, 0, strlen($queryStr) - 1) . $filter;

			if($this->db->result($queryStr)) return true;
			return false;
		}

		public function deleteZipcode($filter = null) {
			if($filter == null) return false; //For your protection

			$queryStr = "DELETE FROM zipcodes WHERE {$filter}";
			if($this->db->result($queryStr)) return true;
			return false;		
		}

	}
?>