<?php
    class user_control
    {
        private $db;
        private $accObj;
        private $photoObj;

        private $thread;
        private $request;
    
        public function __construct(&$db, &$accObj) {
            $this->db = $db;
            $this->accObj = $accObj;

            $this->photoObj = new Photo($db);

            //POST
            $this->request = isset($_POST['request']) ? lcfirst($_POST['request']) : null;
            
            /* Add in ACL system, check user credentials and permissions
             * Check thread <--> user privilege level
             ************/
            //GET thread and default
            $this->thread = ( isset($_GET["thread"]) && !empty($_GET["thread"]) ) ? $_GET["thread"] : null;
            $this->accessControl();
        }
        
        public function ajax() {
            $ajax = ( isset($_POST["ajax"]) && !empty($_POST["ajax"]) ) ? $_POST["ajax"] : null;
            if($ajax != null) {
                switch($ajax) {

                    case "cancel_profilePic":
                        unlink(ROOT . 'TempStore' . strrchr($_POST['img'], '/'));
                        exit;


                    case "edit_profilePic":
                        $response = null;
                        $src = ROOT . 'TempStore' . strrchr($_POST['img'], '/');
                        $dest = ROOT . 'PictureStore/' .$_SESSION['uid'];

                        //checking if user has a folder available
                        if(!file_exists($dest)) {
                            mkdir($dest, 0777);
                        }
                        $dest .= strrchr($_POST['img'], '/');
                        //checking if user has an image of the same name
                        if(!file_exists($dest)) {
                            if(rename($src, $dest)) {
                                $response["success"] = $dest;
                            }
                            else
                                $response["error"] = "Could not move the uploaded file";
                        }
                        else{
                            $response["error"] = "File of the same name exists";
                        }
                        
                        echo json_encode($response);
                        exit;


                    case "valid_username":
                        //need to replace this with something that doesn't give user id
                        if($this->accObj->usernameExists($_POST["input_value"]))
                            $arr = array('valid' => false);
                        else
                            $arr = array('valid' => true);
                        echo json_encode($arr);
                        exit;


                    case "valid_email":
                        if($this->accObj->emailExists($_POST["input_value"]))
                            $arr = array('valid' => false);
                        else
                            $arr = array('valid' => true);
                        echo json_encode($arr);
                        exit;


                    default:
                        exit;
                }
            }
        }
        
        public function post() {   
            if($this->request != null) {
                switch ($this->request) {

                    case 'login':
                        if($this->accObj->login($_POST['email'], $_POST['password']))
                        {
                            System::redirect(URL . '?process=user&thread=profile');
                        }
                        else{
                            System::redirect(URL . '?process=user&thread=login');
                        }
                        break;


                    case 'signup':
                        if($this->processSignUp()){
                            if($this->accObj->login($_POST['email'], $_POST['password'][0]))
                                System::redirect(URL . '?process=user&thread=profile');
                        } 
                        else{
                            //Save user's input to be re-populated on reload
                            System::redirect(URL . "?process=user&thread=register");
                        }
                        break;


                    case 'settings':
                        if($this->settingsControl()){
                            redirect(URL . "?process=user&thread=profile");
                        }
                        break;


                    default:
                    
                        break;
                        
                }
            }
        }
        
        public function start()
        {
            switch($this->thread)
            {
                case "login":
                    require(ROOT . "Page/user/login.php");
                break;
                case "logout":
                    $this->accObj->logout();
                    System::redirect(URL);
                break;
                case "profile":
                    require(ROOT . "Page/user/profile.php");
                break;
                case "settings":
                    require(ROOT . "Page/user/settings.php");
                break;
                case "register":
                    require(ROOT . "Page/user/register.php");
                break;
                default:
                    require(ROOT . "Page/error/404.html");
                break;

            }
        }

        /*
         * PURPOSE: Thread is responsible for serving the page. This func'
         * checks the user's permissions to access requested pages
         * Only pages that should be let through is: login, register 
         * RETURN: modifies $this->thread
         **********/
        private function accessControl(){
            if(!isset($_SESSION["uid"]))
            {
                if($this->thread == "login" || $this->thread == "register")
                    return;
            }
            else if(isset($_SESSION["uid"]))
            {
                if($this->thread == null)
                    $this->thread = "profile";
                return;
            }

            $this->thread = "login";
        }

        private function processSignUp(){
            $registerData = null;
            
            /*
             *  Essentials
             **************************/
            if(!empty($_POST['username']) && (strlen($_POST['username']) >= 4 && strlen($_POST['username']) < 32) ){
                $registerData['username'] = $_POST['username'];
            }
            else
                return false;

            if(!empty($_POST['email']) && (strlen($_POST['email']) >= 8 && strlen($_POST['email']) <= 100) ){
                $registerData['email'] = $_POST['email'];
            }
            else
                return false;

            if(!empty($_POST['password']) && (strlen($_POST['password'][0]) >= 8 && strlen($_POST['password'][0]) <= 32) && ($_POST['password'][0] == $_POST['password'][1]) ){
                $registerData['password'] = $_POST['password'][0];
            }
            else
                return false;

            /*
             *  Non-Essentials
             **************************/
            if(!empty($_POST['first']) && (strlen($_POST['first']) >= 2 && strlen($_POST['first']) <= 20)){
                $registerData['first'] = $_POST['first'];
            }
            if(!empty($_POST['last']) && (strlen($_POST['last']) >= 1 && strlen($_POST['last']) <= 50)){
                $registerData['last'] = $_POST['last'];
            }
            if(!empty($_POST['gender'])) {
                if($_POST['gender'] >= 0 && $_POST['gender'] <= 2) {
                    $registerData['gender'] = $_POST['gender'];
                }
            }
            if(!empty($_POST['month']) && !empty($_POST['day']) && !empty($_POST['year'])) {
                $month = intval($_POST['month']);
                $day = intval($_POST['day']);
                $year = intval($_POST['year']);
                if(checkdate($month, $day, $year)) {
                    $registerData['birth_month'] = $_POST['month'];
                    $registerData['birth_day'] = $_POST['day'];
                    $registerData['birth_year'] = $_POST['year'];
                }
            }

            if($last_insert_id = $this->accObj->register($registerData['username'], $registerData['email'], $registerData['password']))
            {
                $registerData = array_splice($registerData, 3, count($registerData));
                if(count($registerData) > 0){
                    if(!$this->accObj->updateAccountInfo($registerData))
                        return false;
                }
                return true;
            }
            return false;
        }

    }//end user_control 
?>