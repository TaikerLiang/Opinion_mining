<?php
/**
 * evaluation
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 *
 * 
 * 1 July 2013
 */

	  $path = '/Applications/MAMP/htdocs/Library/';
  	set_include_path(get_include_path() . PATH_SEPARATOR . $path);

  	require_once("Input/read_file.php");
  	require_once("Database/mysql_connect.inc.php");
  	require_once("Database/DB_class.php");



  	$fileName = './Result/movie.txt';
   	$fd = openFileRead($fileName);

   	if(!$fd) die('can not open the file'); 	

   	$evaluation = array();

   	while ($str = fgets($fd)) {

   		if(trim($str)==NUll)
   			continue;

   		if(is_numeric(trim($str)) == 1)
   			$round = (int)$str;

   		$tmp = explode(" ", $str);
   		$tmp[0] = str_replace(":", "", $tmp[0]);
   		
   		if($tmp[0] == "Accuracy")
   			$evaluation['Accuracy'][$round] = $tmp[1];
   		else if($tmp[0] == "recall")
   			$evaluation['recall'][$round] = $tmp[1];
   		else if($tmp[0] == "precision")
   			$evaluation['precision'][$round] = $tmp[1];
   		else if($tmp[0] == "F-measure")
   			$evaluation['F-measure'][$round] = $tmp[1];
   		else if($tmp[0] == "G-means")
   			$evaluation['G-means'][$round] = $tmp[1];

   		//echo $str.'</br>';
   	}

   	//var_dump($evaluation);


   	foreach ($evaluation as $key => $value) {
   		echo $key.'</br>';
   		$total = 0;
   		for($i=0;$i<count($evaluation[$key]);$i++){
   			$total += $evaluation[$key][$i];
   		}
   		echo $total/count($evaluation[$key]).'</br>';
   	}






?>