<?php
/**
 * copy
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * 
 * 12 June 2012
 */

	ini_set('include_path','.:/Applications/MAMP/htdocs/Library/');

	require_once("Input/read_file.php");
	require_once("Database/mysql_connect.inc.php");
    require_once("Database/DB_class.php");

    ini_set('memory_limit', '1024M');     //memory size
  	set_time_limit(0);                    //time limit


  	$dirName = '/Applications/MAMP/htdocs/Twitter/Crawler/New/';
  	$dirList = openDirectory($dirName);

  	foreach($dirList as $key => $N){
		if($key > 2){
			
			$fileName = $dirName.$dirList[$key];
   			$fd = openFileRead($fileName);
			if(!$fd) die('can not open the file');

			$fileNameOut = '/Users/user/Desktop/Training_DATA/New/'.$dirList[$key];
  			$fwout = openFileWritePlus($fileNameOut);

  			while($str = fgets($fd)){
  				//echo $str.'</br>';
  				fwrite($fwout,$str);
  			} 
		}
	}

  	echo "complete</br>";



?>