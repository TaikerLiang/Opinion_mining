<?php
/**
 * build_training_data
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * read the lexicon and bulid the training data for LDA
 * format: term_ID : count
 * 
 * 23 July 2012
 */

	require_once("../Library/Input/read_file.php");
	require_once("./function.php");

	ini_set('memory_limit', '256M');     //memory size
  	set_time_limit(0);                   //time limit

  	
  	$log = './Log/Preprocessing/build_training_data';
	$fwlog = openFileWrite($log);

	$filenameout = './Data/train.txt';
	$fw = openFileWrite($filenameout);

	$filename = './Data/lexicon.txt';
	$fd = openFileRead($filename);

	$lexicon = array();
	$order = 0;

	$count = array();

	while($str = fgets($fd)){
		
		//if(trim($str)==" ")
		//	continue;

		$lexicon[trim($str)] = $order;
		$order++;
		//echo $str.'</br>';
	}

	foreach ($lexicon as $key => $value) {
		
		echo $key." ".$value.'</br>';
	}

	initialArray(&$count,count($lexicon),0);

	fclose($fd);

	$ID = 0;
	$dirName = './Data/Training/';
  	$dirList = openDirectory($dirName);	
  	$output = "";

  	foreach($dirList as $key => $N){
  		if($key > 2){
  			
  			$fileName = $dirName.$dirList[$key];
   			$fd = openFileRead($fileName);
			if(!$fd) die('can not open the file');

			while($str = fgets($fd)){
				//echo $str.'</br>';
				$tmp = explode(" ", $str);

				for($i=0;$i<count($tmp);$i++){
					
					$tmp[$i] = str_replace(",","",$tmp[$i]);
					$tmp[$i] = str_replace(".","",$tmp[$i]);
					$tmp[$i] = str_replace("\"","",$tmp[$i]);
					$tmp[$i] = str_replace("?","",$tmp[$i]);
					$tmp[$i] = str_replace("!","",$tmp[$i]);
					$tmp[$i] = str_replace("#","",$tmp[$i]);

					//if(strtolower(trim($tmp[$i])) == " ")
					//	continue;

					$ID = $lexicon[strtolower(trim($tmp[$i]))];
					//echo $ID.'</br>';
					$count[$ID]++;

				}

				for($i=0;$i<count($count);$i++){
					if($count[$i] > 0 ){
						$output = $output.($i+1).":".$count[$i]." ";
						//echo ($i+1).":".$count[$i]." ";
					}
				}
				
				/*echo $output;
				echo '</br>------------------</br>';*/

				fwrite($fw, $output."\n");
				$output = "";
				clearArray(&$count);
			}

			writeLog($dirList[$key],$fwlog);
  		}
  	}


	//var_dump($count);

	echo "complete</br>";


?>