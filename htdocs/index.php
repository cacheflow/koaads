<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	//define("ROOT","/var/www/");
	define("ROOT", "/Applications/XAMPP/xamppfiles/htdocs/");
	//define("URL","http://www.koaads.com/");
	define("URL", "http://localhost/");

	//Global/Static Classes
	define("LIB", ROOT . "Library/");
		define("INC", LIB . "Include/");
	
	//Stylesheets and Images
	define("THEME", URL . "Theme/");

	//Essential Includes
	require_once(ROOT . "Config/db_config.php");
	require_once(INC . "Database.php");
	require_once(INC . "System.php");

	//Model Includes
	require_once(INC . "Photo.php");
	require_once(INC . "Mail.php");
	require_once(INC . "Location.php");
	require_once(INC . "Listing.php");

	//Account Includes
	require_once(LIB . "Account/Account.php");	
	require_once(LIB . "Account/ACL.php");
	require_once(LIB . "Account/Message.php");
	require_once(LIB . "Account/Setting.php");

	
	$system = new System();
	$system->start();
?>