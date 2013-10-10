<?php
/**
 * stop_word
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * the list of stop_word
 * data source : http://www.webconfs.com/stop-words.php
 *
 * 13 August 2012
 */

	ini_set('include_path','.:/Applications/MAMP/htdocs/Library/');

	require_once("Input/read_file.php");
	require_once("Database/mysql_connect.inc.php");

	ini_set('memory_limit', '256M');     //memory size
  	set_time_limit(0);                   //time limit

  	$log = '../Log/Knowledge_Database/stop_word';
	$fwlog = openFileWrite($log);

	$filename = './stop words.txt';
	$fd = openFileRead($filename);

	connect_DB("twitter");

	$ID = 1;
	while($str = fgets($fd)){
		$str = str_replace("'","''", $str);
		$sql = "INSERT INTO stop_words(ID, word) VALUES ('{$ID}','{$str}')";
		mysql_query($sql);
		$ID++;
		//echo $str.'</br>';
	}

	writeLog("complete",$fwlog);

  	echo "complete</br>";


?>