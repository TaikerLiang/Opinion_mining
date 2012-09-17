<?php

/**
 * upload_orignal_tweet
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * upload tweets to database(trainging)
 * if tweet's length < 5 & tweet contain URL then discard it.
 * 
 * 15 Aug 2012
 */

	
	ini_set('include_path','.:/Applications/MAMP/htdocs/Library/');

	require_once("Input/read_file.php");
	require_once("Database/mysql_connect.inc.php");
    require_once("Database/DB_class.php");

    ini_set('memory_limit', '1024M');     //memory size
  	set_time_limit(0);                    //time limit

  	$dirName = './Data/OriginalTweets/';
  	$dirList = openDirectory($dirName);	
  	$log = './Log/upload_orignal_tweet.txt';
	$fwlog = openFileWrite($log);
	$url = 0; //contain url

	$db = new DB;
    $db->connect_db($db_server, $db_user, $db_passwd , "training");


    	foreach($dirList as $key => $N){
		if($key > 2){
			$fileName = $dirName.$dirList[$key];
   			$fd = openFileRead($fileName);
			if(!$fd) die('can not open the file');

			$ID = 1;
			$tableName = $dirList[$key];

			echo $tableName.'</br>';

			while($str = fgets($fd)){
				$tmp = explode(" ", $str);
				//If number of words in tweet < 5,then discard it.
				if(count($tmp) <= 5){
					continue;
				}
				else{		
					for($i=0;$i<count($tmp);$i++){
						if(substr($tmp[$i],0,4)=="http"){
								$url = 1;
								break;
								//$str = str_replace($tmp[$i],"URL",$str); 
						}
					}
					if($url == 0){
						//echo $str.'</br>';
						$str = str_replace("'","''", $str);
						$sql = "INSERT INTO $tableName(ID, original_tweet) VALUES ('{$ID}','{$str}') ";
						$db->query($sql);
						$ID++;
					}
					else{
						$url = 0;
					}
				}
			}
		}//if
		//write some information into log file.
		writeLog($dirList[$key],$fwlog);	
	}//foreach

	echo "complete</br>";

?>