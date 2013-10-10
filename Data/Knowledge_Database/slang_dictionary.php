<?php
/**
 * slang dictionary
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * the list of slang and its meanting
 * data source : http://www.noslang.com/ 
 *
 * 13 August 2012
 */

	$path = '/Applications/MAMP/htdocs/Library/';
  	set_include_path(get_include_path() . PATH_SEPARATOR . $path);

	require_once("Input/read_file.php");
	require_once("Database/mysql_connect.inc.php");
	require_once("Database/DB_class.php");

	ini_set('memory_limit', '256M');     //memory size
  	set_time_limit(0);                   //time limit

  	$log = '../Log/Knowledge_Database/slang_dictionary';
	$fwlog = openFileWrite($log);

	$dirName = './Slang Dictionary/';
  	$dirList = openDirectory($dirName);	

  	//connect_DB("slang_dictionary");

  	$tableName = "";

  	foreach($dirList as $key => $N){
  		if($key > 2){
  			$ID = 1;
  			$tableName = $dirList[$key];
  			echo $tableName.'</br>';
  			
  			/*$sql= "create table $tableName(
  				ID int(10),
  				slang text,
  				meaning text
  			)";
			
			mysql_query($sql);*/
			$db = new DB;
			$db->connect_db($db_server, $db_user, $db_passwd , "slang_dictionary");

  			$filename = $dirName.$dirList[$key];
			$fd = openFileRead($filename);

			while($str = fgets($fd)){

				$str = trim($str);
				$str = str_replace("'","''", $str);
				//echo $str.'</br>';
				$tmp = explode(" ", $str);
				$slang = $tmp[0];
				$meaning = $tmp[3];
				//echo $slang.' '.$meaning.'</br>';
				$slang = str_replace("_"," ", $slang);

				$sql = "INSERT INTO $table(ID, slang ,meaning) VALUES ('{$ID}','{$slang}','{$meaning}') ";
				echo $sql.'</br>';
				$db->query($sql);
				$ID++;
			}
  		}
  	}
	writeLog("complete",$fwlog);

  	echo "complete</br>";

?>