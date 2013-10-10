<?php
/**
 * emotions
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * the list of emotions and its meaning
 * data source : wiki 
 *
 * 13 August 2012
 */


	ini_set('include_path','.:/Applications/MAMP/htdocs/Library/');

	require_once("Input/read_file.php");
	require_once("Database/mysql_connect.inc.php");

	ini_set('memory_limit', '256M');     //memory size
  	set_time_limit(0);                   //time limit

  	$log = '../Log/Knowledge_Database/emotions';
	$fwlog = openFileWrite($log);

	$filename = './emotions.txt';
	$fd = openFileRead($filename);

	connect_DB("twitter");

	$emotion = array();
	$positive_position=0;
	$negative_position;
	$neutral_position;

	$order = 0;

	while($str = fgets($fd)){

		//echo $str.'</br>';
		if(trim($str) == "positive"){
			
			$positive_position = $order;
			//echo "positive ".$positive_position.'</br>';
		}
		else if(trim($str) == "negative"){
			$negative_position = $order;
			//echo "negative ".$negative_position.'</br>';
		}
		else if(trim($str) == "neutral"){
			$neutral_position = $order;
			//echo "neutral ".$neutral_position.'</br>';
		}	
		else{
			$tmp = explode(" ", $str);

			for($i=0;$i<count($tmp);$i++){
				//echo $tmp[$i].'</br>';
				$emotion[$order] = $tmp[$i];
				$order++;
			}
		}
	}

	$meaning = "";
	$ID = 1;

	for($i=0;$i<count($emotion);$i++){
		
		if($i == $positive_position)
			$meaning = "P";
		else if($i == $negative_position)
			$meaning = "N";
		else if($i == $neutral_position)
			$meaning = "NE";

		$emotion[$i] = str_replace("'","''", $emotion[$i]);
		$icon = $emotion[$i];
		$sql = "INSERT INTO emotions(ID, icon ,meaning) VALUES ('{$ID}','{$icon}','{$meaning}') ";
		mysql_query($sql);
		$ID++;

		echo $meaning." ".$emotion[$i].'</br>';
	}

	writeLog("complete",$fwlog);

  	echo "complete</br>";
?>