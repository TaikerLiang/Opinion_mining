<?php
/**
 * decode_svm
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 *
 * 
 * 25 Jun 2013
 */

	
	$path = '/Applications/MAMP/htdocs/Library/';
	set_include_path(get_include_path() . PATH_SEPARATOR . $path);

  	require_once("Input/read_file.php");
  	require_once("Database/mysql_connect.inc.php");
  	require_once("Database/DB_class.php");


  	$fileName = './libsvm/test';
   	$fd = openFileRead($fileName);
   	if(!$fd) die('can not open the file'); 	

   	while ($str = fgets($fd)) {
   		echo $str.'</br>';
   	}

   	echo '--- decode_svm complete ---</br>';
?>