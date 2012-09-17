<?php
/**
 * get_all_adjetive
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * Get all adjectives in tweets.
 * 
 * 12 June 2012
 */
	
	require_once("../../Library/Input/read_file.php");
 	require_once('./function.php');

 	ini_set('memory_limit', '256M');     //memory size
  	set_time_limit(0);                   //time

  	$dirName = '../Data/TagTweets/';
 	$dirList = openDirectory($dirName);

 	$log = '../Log/Analysis/log_get_all_adjective';
	$fwlog = openFileWrite($log);

	$fileNameOut = '../Data/total_adjective_table.txt';
	$fw = openFileWrite($fileNameOut);

	$total_adjective = array();

	foreach($dirList as $key => $N){
  		if($key >= 2){
  			$fileName = $dirName.$dirList[$key];
   			$fd = openFileRead($fileName);
			if(!$fd) die('can not open the file');

			while($str = fgets($fd)){

				$tmp = explode("\t", $str);

				if(filterOutOfAdjective($tmp[1])){
					//echo $str.'</br>';
					$word = trim($tmp[0]);
					$word = strtolower($word);
					if($total_adjective[$word] == NULL){
						$total_adjective[$word] = 1;
					}
					else{
						$total_adjective[$word]++;
					}
				}
			}
  		}
  		writeLog($dirList[$key],$fwlog);
  	}

  	foreach($total_adjective as $key => $value){
  		if($value >= 5){
  			//echo $key." ".$value.'</br>';
  			fwrite($fw, $key."\n");
  		}
  	}

  	echo "complete</br>";

?>