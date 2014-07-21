<?php
/*
Uploadify
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/

if (!empty($_FILES)) {
	$filename = $_FILES['Filedata']['name'];

	//check for disguised scripts, in addition '.' are not allowed in names
	if(count(explode('.',$filename)) <= 2) {
		$src = $_FILES['Filedata']['tmp_name'];
		$dest = '/Applications/XAMPP/xamppfiles/htdocs/TempStore/' . $filename; 

		$size = $_FILES['Filedata']['size'];
		
		// Validate the file type
		$allowed = array('.jpg','.jpeg','.png');
		$type = strrchr($filename, '.');
		
		if (in_array($type, $allowed) && (($size / 1024) / 1024) <= 1.5) {
			if(move_uploaded_file($src,$dest)) {
				echo "http://localhost/TempStore/" . $filename;
			}
			else
				echo '0';
		} 
		else
			echo '0';
	}
	else
		echo '0';
}
else
	echo '0';

/*

$session_name = session_name();

if(!isset($_POST[$session_name])) {
	exit;
}
else {
	session_id($_POST[$session_name]);
	session_start();
	if(!isset($_SESSION['uid']))
		exit;
}

if (!empty($_FILES)) {
    $ext = strrchr($_FILES['Filedata']['name'], '.');             
    $size = $_FILES['Filedata']['size'];

    $tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = "/Applications/XAMPP/xamppfiles/htdocs/TempStore/";
	$targetFile = rtrim($targetPath,'/') . '/' . $_FILES['Filedata']['name'];

    $tmp = $_FILES['Filedata']['tmp_name'] . '/' . $name;
	$dest = "/Applications/XAMPP/xamppfiles/htdocs/TempStore/" . $name;

    // Validate the file type and size
    $fileTypes = array('.jpg','.jpeg','.png');
    if (in_array($fileParts['extension'], $fileTypes)) {
        move_uploaded_file($tempFile, $targetFile . $_FILES['Filedata']['name']);
        echo $dest;
    } 
    else{
    	echo "0<br />";
    	echo "TempFile: " . $tempFile . "<br />";
    	echo "TargetFile: " .$targetFile . "<br />";
    }
}
*/
?>