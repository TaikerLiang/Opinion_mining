<?php
/**
 * matrix
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * create a matrix of document & term relation
 * row:documents, colum:adjectivs.
 * 
 * 12 June 2012
 */

	ini_set('memory_limit', '2048M');     //memory size
  	set_time_limit(0);                   //time

	require_once("../../Library/Input/read_file.php");
	require_once('./function.php');

	$log = '../Log/Analysis/log_matrix';
	$fwlog = openFileWrite($log);


	$fileName = '../Data/total_adjective_table.txt';
	$fd = openFileRead($fileName);
	if(!$fd) die('can not open the file');
	$order = 0;
	$adjective = array();
	while($str = fgets($fd)){
		$adjective[$order] = trim($str);
		$order++;
		//echo $str.'</br>';
	}

	writeLog("load adjective_table",$fwlog);

	$dirName = '../Data/CandidateOpinion/';
  	$dirList = openDirectory($dirName);

  	$filenameout = '../Data/matrix.txt';
  	$fw = openFileWrite($filenameout);

  	$matrix[][count($adjective)] = array();
  	$order = 0;       //documents
  	$contain_ajective = 0;

  	foreach($dirList as $key => $N){
  		if($key >= 2){
  			//initial matrix of row
  			//echo count($adjective).'</br>';
  			$fileName = $dirName.$dirList[$key];
   			$fd = openFileRead($fileName);
			if(!$fd) die('can not open the file');

			while($str = fgets($fd)){

				for($k=0;$k<count($adjective);$k++){
  					$matrix[$order][$k] = 0;
  				}

				$tmp = explode(" ",trim($str));
				//echo $str.'</br>';
				for($i=0;$i<count($tmp);$i++){
					//echo $tmp[$i].'</br>';
					for($j=0;$j<count($adjective);$j++){
						if($tmp[$i] == $adjective[$j]){
							//echo $tmp[$i].'</br>';
							$matrix[$order][$j]++;
							$contain_ajective = 1;
						}
					}
				}

				if($contain_ajective==1){
					$string = "";
					for($i=0;$i<count($adjective)-1;$i++){
						$string = $string.$matrix[$order][$i]." ";
					}
					$string = $string.$matrix[$order][count($adjective)-1];
					//echo $string.'</br>';
					//echo "-----------------------</br>";
					fwrite($fw, $string."\n");
					writeLog($order,$fwlog);
					$order++;
					$contain_ajective = 0;
				}
			}
  		}//if
  	}//foreach

  	//echo $order;
	echo "complete</br>";
?>