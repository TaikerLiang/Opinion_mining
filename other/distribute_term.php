<?php
/**
 * distribute_term
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * read the lexicon and model.beta.
 * according to the result of model.beta and distribute term to topics.
 * 
 * 25 July 2012
 */

	
	require_once("../Library/Input/read_file.php");
	require_once("./function.php");

	ini_set('memory_limit', '256M');     //memory size
  	set_time_limit(0);                   //time limit


  	$latentClass;     // k topic

  	function findMin($array){
  		$threshold = 10;

  		//if difference of value > threshold, then find max
  		$pos=0;
  		$min = $array[0];
  		$secondMin = 0;

  		for($i=1;$i<count($array);$i++){
  			if($array[$i] <= $min){
  				$pos = $i;
  				$secondMin = $min;
  				$min = $array[$i];
  			}
  			else{
  				if($secondMin == 0)
  					$secondMin = $array[$i];

  				if($array[$i] <= $secondMin){
  					$secondMin = $array[$i];
  				}
  			}
  		}

  		if(abs($secondMin - $min) <= $threshold){
  			//echo "fuck";
  			return -1;
  		}

  		return $pos;
  	}


  	$filename = './lda-0.1/model.beta';
	$fd = openFileRead($filename);

	$filenameLexicon = './Data/lexicon.txt';
	$fdLex = openFileRead($filenameLexicon);

	$lexicon = array();
	$order = 0;

	

	while($str = fgets($fdLex)){
		
		$lexicon[$order] = trim($str);
		//$lexicon[trim($str)] = $order;
		$order++;
		//echo $str.'</br>';
	}

	$order=0;

	$value = array();
	$topic = array();
	
	/*while($str = fgets($fd)){
		echo $lexicon[$order].':  </br>';
		$tmp = explode("   ", $str);
		//echo $tmp[1].'</br>';

		$latentClass = count($tmp);     // k topic

		for($i=0;$i<count($tmp);$i++){
			$pos = strpos ($tmp[$i], "e"); //position
			$value[$i] = (int)substr($tmp[$i],$pos+2 , strlen($tmp[$i]));
			//echo $value[$i].'</br>';
		}	
		$order++;
		
		for($i=0;$i<$latentClass;$i++){
			echo $value[$i]." ";
		}
		echo "</br>";
		//echo $value[0]." ".$value[1].'</br>';
		


		$result = findMin($value);
		echo $result.'</br>';

		if($result != -1){
			$topic[$result] = $topic[$result].", ".$lexicon[$order];
		}
		
	}*/



	while($str = fgets($fd)){
		$tmp = explode("   ", $str);
		$latentClass = count($tmp);     // k topic
		for($i=0;$i<count($tmp);$i++){
			$value[$i][(string)$order]= (float)$tmp[$i];
			//echo $value[$i]." ";
		}
		$order++;
		//echo "</br>";

	}

	arsort($value[0]);
	$count = 1;
	foreach ($value[0] as $key => $value) {
		echo $lexicon[$key].' -- '.$value.'</br>';
		$count++;
		if($count==20)
			break;
	}
	//var_dump($value);

	/*for($i=0;$i<$latentClass;$i++){
		
		echo $topic[$i].'</br>';
		echo "</br>----------------</br>";
	}*/

	echo "</br>complete</br>";




?>