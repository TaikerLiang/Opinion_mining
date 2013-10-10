<?php
/**
 * opinion_lexicon
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * upload the opinion lexicon to database
 * 
 * 15 Aug 2012
 */
  


 $path = '/Applications/MAMP/htdocs/Library/';
 set_include_path(get_include_path() . PATH_SEPARATOR . $path);

 require_once("Input/read_file.php");
 require_once("Database/mysql_connect.inc.php");
 require_once("Database/DB_class.php");


 ini_set('memory_limit', '512M');     //memory size
 set_time_limit(0);                   //time limit

 $log = '../Log/Knowledge_Database/opinion_lexicon';
 $fwlog = openFileWrite($log);

 $dirName = './opinion_lexicon/';
 $dirList = openDirectory($dirName);

 $db = new DB;
 $db->connect_db($db_server, $db_user, $db_passwd , "knowledge");
 $ID = 0;

 foreach($dirList as $key => $N){

 	if($key > 2){
 		

 		$fileName = $dirName.$dirList[$key];
   		$fd = openFileRead($fileName);
		if(!$fd) die('can not open the file');

		$opinion_direction = str_replace(".txt", "", $dirList[$key]);
		echo $opinion_direction.'</br>';
		
		while($str = fgets($fd)){
			$ID++;
			$str = str_replace("'","''", $str);
			$str = trim($str);
			$sql = "INSERT INTO opinion_lexicon(ID, content, opinion_direction) VALUES ('{$ID}','{$str}','{$opinion_direction}')";
			$db->query($sql);
			//echo $str.'</br>';
		} 		
 	}
 	writeLog($dirList[$key]." successful\n",$fwlog);

 }

 echo "complete</br>";


?>