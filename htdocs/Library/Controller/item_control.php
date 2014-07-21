<?php
	/*	Tasks that this Control should handle:
 	 *	-creating Item
 	 *	-preview of existing item
 	 *  -managing existing items
 	 *	-comments of items fetching/updating/deleting
	 */
	class item_control {
		private $db;
		private $thread;

		private $accObj;

		private $locationObj;
		private $photoObj;
		private $listingObj;
		private $mailObj;

		public function __construct(&$db, &$accObj) {
			$this->db = $db;
			$this->accObj = $accObj;

			$locationObj = new Location($this->db); //geocoding, address verification
			$photoObj = new Photo($this->db);
			$listingObj = new Listing($this->db);
			$mailObj = new Mail($this->db);

			//POST
            $this->request = isset($_POST["request"]) ? lcfirst($_POST["request"]) : null;
			
			//redirects user to search if no thread is set
			$this->thread = isset($_GET['thread']) && !empty($_GET['thread']) ? $_GET['thread'] : "search";
		}



		public function ajax() {

		}

		public function post() {
			if($this->request != null) {
				switch($this->request) {
					case 'item_new':
						
						break;
				}
			}
		}

		public function start() {
			switch($this->thread) {

				case 'new':
					require(ROOT . 'Page/item/newitem.php');
					break;

				case 'search':
				default:
					System::redirect(URL . "?process=search");
					break;


			}
		}


		/* Item Create / Modify / Delete
		 =============================== */



	}
?>