<?php
	class System
	{
		private $db;
		private $accObj;

		/* Constructor
		 *	-Creates Database Object 
		 *  -Creates Empty Account Object
		 *  -Cleans all incoming data, if any
		 * 	-[createBySession()] Try to initialize Account Object with existing PHPSESSID
		 * 		= session_start called here
		 *		= How to create new session id and check integrity?
		 ************************************************/
		public function __construct()
		{
            ob_start(); //output buffer          
			$this->db = new Database();
			//Filters out all $_GET, $_POST, $_COOKIE == $_REQUEST is safe
			$this->filterRequest();
            //Attempt to intialize a pre-existing session using session cookie
            $this->accObj = Account::createFromSession($this->db);           
		}


/***************************
*                          *
*        INITIALIZER       *
*                          *
***************************/
		public function start()
		{
            $controlStr = isset($_GET['process']) ? $_GET['process'] : 'home';
            switch($controlStr)
            {
                case "home":
                case "user":
                case "search":
                case "item":
                case "admin":
                case "nexus":
                    $controlStr .= "_control";
                    require_once(LIB . "Controller/{$controlStr}.php");
                    $controlObj = new $controlStr($this->db, $this->accObj);
                break;
                default:    
                    require_once(ROOT . "Page/error/404.html");
                    exit;
                break;

            }

            $controlObj->ajax();
            $controlObj->post();
            // ob_end_flush();//end output buffer
            require_once(ROOT . "Page/templates/header.php");
            //testing
            $controlObj->start();
            require_once(ROOT . "Page/templates/footer.php");  
        }

/************************
*                       *
*        SECURITY       *
*                       *
************************/
        private function filterRequest()
        {
            $_POST = array_map( array($this, 'filterData'), $_POST);
            $_GET = array_map( array($this, 'filterData'),$_GET);
            $_COOKIE = array_map( array($this, 'filterData'),$_COOKIE);          
        }
        
        //recursive scrubbing of inputs
        private function filterData($data)
        {
        	//IF::data has an inner array, go a level deeper
            if(is_array($data))
            {
                $data = array_map( array($this, 'filterData'),$data);
            }
            else
            {
                $data = $this->scrubbaDubDub($data);
            }
            return $data;            
        }
        
        //Does the actual scrubbing of inputs
        private function scrubbaDubDub($str)
        {
            $str = trim($str);

            /* @param1: first 3 for magic_quotes_gpc
             *          next 3 for regular backslashed newlines and carriage returns
             * @param2: replace all occurrences with <br />
             * @param3: string to evaluate
             *
             * NOTES: 
             *      nl2br runs first and is passed to str_replace
             *      sort of overdoing it but used function to replace all newlines to <br />
             ******/
            $str = str_replace(array('\\r\\n','\r\\n','r\\n','\r\n', '\n', '\r'), '<br />', nl2br($str));
            
            //check if php interpreter automatically backslashes quotes
            if(get_magic_quotes_gpc())
            {
            	//Strips any double slashes such as \\n => \n (correct format)
                $str = stripslashes($str);
            }

            //makes string mysql safe from SQL injection
            $str = $this->db->real_escape_string($str);

            //@param2 : allowed tags
            $str = strip_tags($str, '<b><i><u><div><center><blockquote><li><ul><a><p><br><br />');
            return $str;
        }

/****************************
*                           *
*       STATIC HELPERS      *
*                           *
****************************/
        public static function createCSRFToken(){
            $token = md5(uniqid(rand(), TRUE));
            $_SESSION['token'] = $token;
            $_SESSION['token_timestamp'] = time();
            echo '<input type="hidden" name="token" value="{$token}" />';
        }

        public static function checkCSRFToken(){
            if($_POST['token'] == $_SESSION['token'])
                return true;
            else
                return false;
        }

        public static function redirect($url)
        {
            header('Location: ' . $url);
            exit;
        }
	}
?>