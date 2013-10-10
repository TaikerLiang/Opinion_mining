<?php
/**
 * lexicon
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * bulid the lexicon
 * 
 * 23 July 2012
 */

	
	require_once("../Library/Input/read_file.php");

	ini_set('memory_limit', '256M');     //memory size
  set_time_limit(0);                   //time limit

  $dirName = './Data/Training/';
  $dirList = openDirectory($dirName);	
  $log = './Log/Preprocessing/lexicon';
	$fwlog = openFileWrite($log);


	$filenameout = '/Applications/MAMP/htdocs/Twitter/Data/lexicon.txt';
	$fw = openFileWrite($filenameout);

	$lexicon = array();

  	foreach($dirList as $key => $N){
  		if($key > 2){
  			
  			//echo $dirList[$key].'</br>';
  			$fileName = $dirName.$dirList[$key];
   			$fd = openFileRead($fileName);
			if(!$fd) die('can not open the file');

			while($str = fgets($fd)){
				$tmp = explode(" ", $str);

				for($i=0;$i<count($tmp);$i++){
					$tmp[$i] = str_replace(",","",$tmp[$i]);
					$tmp[$i] = str_replace(".","",$tmp[$i]);
					$tmp[$i] = str_replace("\"","",$tmp[$i]);
					$tmp[$i] = str_replace("?","",$tmp[$i]);
					$tmp[$i] = str_replace("!","",$tmp[$i]);
					$tmp[$i] = str_replace("#","",$tmp[$i]);


					$lexicon[strtolower(trim($tmp[$i]))] = 1;
				}

			}


  			writeLog($dirList[$key],$fwlog);

  		}
  	}
  	ksort($lexicon);
  	foreach ($lexicon as $key => $value) {
  		echo $key.'</br>';
  		//fwrite($fw, $key."\n");
  	}

  	//var_dump($lexicon);
  	echo "complete</br>";









?>