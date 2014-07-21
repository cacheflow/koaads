<?php
    class home_control
    {
        private $db;
        private $accObj;

        private $locationObj;
        private $listingObj;
        private $photoObj;
        private $mailObj;

        private $thread;

        public function __construct(&$db, &$accObj)
        {
            $this->db = $db;
            $this->accObj = $accObj;

            $this->locationObj = new Location($db);
            $this->listingObj = new Listing($db);
            $this->photoObj = new Photo($db);
            $this->mailObj = new Mail($db); 

            //POST
            $this->request = isset($_POST["request"]) ? lcfirst($_POST["request"]) : null;

            //GET thread and default
            $this->thread = isset($_GET["thread"]) && !empty($_GET["thread"]) ? $_GET["thread"] : "landing";
        }
        
        public function ajax()
        {
            $ajax = ( isset($_POST["ajax"]) && !empty($_POST["ajax"]) ) ? $_POST["ajax"] : null;
            if($ajax != null) {
                switch($ajax) {


                    case 'location_autoComplete':
                        $input = $_POST['input'];
                        $limit = $_POST['limit'];
                        $where = null;
                        if(is_int($input)) {
                            $where = "`zipcode` REGEXP '^({$input})' LIMIT {$limit}";
                        }
                        else if(is_string($input)) {
                            $where = "`city` REGEXP '^({$input})' LIMIT {$limit}";
                        }
                        else {
                            echo "No suggestions";
                            exit;
                        }
                        $data = $this->searchObj->getZipcode('DISTINCT city, state', $where);
                        for($i = 0; $i < count($data); $i++) {
                            $data[$i] = implode(', ', $data[$i]);
                        }
                        echo  $_GET['callback']."(".json_encode($data).")";
                        exit;

                        
                    default:
                        exit;
                }
            }
        }
        
        public function post()
        {   

        }
        
        public function start()
        {
            switch($this->thread)
            {
                case "debug":
                    require_once(ROOT . "Page/home/debug.php");
                break;
                case "landing":
                default:
                    require(ROOT . "Page/home/landing.php");
                break;
            }
        }
    }
?>