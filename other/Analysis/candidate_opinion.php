<?php
/**
 * candidate_opinion
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * select the sentence which contains adjective in adjective_table 
 *
 * 12 June 2012
 */


	require_once("../../Library/Input/read_file.php");
	require_once('./function.php');

	$log = '../Log/Analysis/log_candidate_opinion';
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
	/*foreach($adjective as $key => $value){
		echo $key." ".$value.'</br>';
	}*/

	$contain_adjective = 0;
	$dirName = '../Data/AfterPreprocessing/';
  	$dirList = openDirectory($dirName);

  	foreach($dirList as $key => $N){
  		if($key >= 2){
  			$fileName = $dirName.$dirList[$key];
   			$fd = openFileRead($fileName);
			if(!$fd) die('can not open the file');

			$filenameout = '../Data/CandidateOpinion/'.$dirList[$key];
			$fw = openFileWrite($filenameout); 

			while($str = fgets($fd)){
				$tmp = explode(" ", trim($str));
				if(count($tmp)<5) continue;
				for($i=0;$i<count($tmp);$i++){
					for($j=0;$j<count($adjective);$j++){
						if($tmp[$i]==$adjective[$j]){
							$contain_adjective = 1;
							break;
						}
					}
					if($contain_adjective==1)
						break;
				}

				if($contain_adjective==1 ){
					fwrite($fw, $str);
					//echo $str.'</br>';
				}
			}
			writeLog($dirList[$key],$fwlog);
  		}//if
  	}//foreach

  	echo "complete</br>";
?>