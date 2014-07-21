<?php
	class search_control {
		private $db;
        private $accObj;

        private $locationObj;
        private $listingObj;
        private $photoObj;

        private $thread;
        private $request;
        
        //Search Parameters
        private $location;
        private $category;
        private $string;

        public function __construct(&$db, &$accObj) {
            $this->db = $db;
            $this->accObj = $accObj;

            //Instantiation of Objects
            $this->listingObj = new Listing($this->db);

            //POST
            $this->request = isset($_POST["request"]) ? lcfirst($_POST["request"]) : null;

            //GET thread and default
            $this->thread = isset($_GET["thread"]) && !empty($_GET["thread"]) ? $_GET["thread"] : "search";

            //Search Parameters
            $this->location = isset($_GET["l"]) && !empty($_GET["l"]) ? $_GET["l"] : null;
            $this->category = isset($_GET["c"]) && !empty($_GET["c"]) ? $_GET["c"] : null;
            $this->string = isset($_GET["s"]) && !empty($_GET["s"]) ? $_GET["s"] : null;

            

        }

        public function ajax() {

        }

        public function post() {

        }

        public function start() {
        	switch($this->thread){
        		case 'search':
        		default:
        			require(ROOT . 'Page/search/searchtest.php');
        			break;
        	}
        }

        public function filterLocation($data) {
        	if(($this->location)) {

        	}
    	}



	}
?>