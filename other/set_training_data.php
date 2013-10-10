<?php
/**
 * set_training_data
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * put the training data into database 
 *
 * 12 June 2012
 */


	require_once("../Library/Input/read_file.php");
	require_once("../Library/Database/mysql_connect.inc.php");
	require_once("../Library/Database/sql_function.php");
	$dirName = './Data/Training/';
  	$dirList = openDirectory($dirName);

  	connect_DB("training");   //connect to training database


  	$class_ID = 0;
  	foreach($dirList as $key => $N){
  		if($key > 2){
  			
  			$name = str_replace(".txt","", $dirList[$key]);
  			echo $name.'</br>';
  			$log = './Log/training_data/'.$dirList[$key];  //open log file
			$fwlog = openFileWrite($log);

			$fileName = $dirName.$dirList[$key];
			$fd = openFileRead($fileName);
			if(!$fd) die('can not open the file');

			$order = 0;
		
			while($str = fgets($fd)){
				$str = str_replace("'","''", $str);
				echo $str.'</br>';
				echo $order.'</br>';
				$sql = "INSERT INTO {$name} (ID, content, class_ID) VALUES ('{$order}','{$str}','{$class_ID}') ";
				mysql_query($sql);
				$order++;
			}

			echo "---------------</br></br>";
			$class_ID++;
  		}
  		
  	}


	connect_DB("training");   //connect to training database


	echo "complete</br>";


?>