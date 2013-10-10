<?php
/**
 * Unigram-ES
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * Naive bayes + salience or entropy
 * 
 * 25 Jun 2013
 */


	$path = '/Applications/MAMP/htdocs/Library/';
  	set_include_path(get_include_path() . PATH_SEPARATOR . $path);

  	require_once("Input/read_file.php");
  	require_once("Database/mysql_connect.inc.php");
  	require_once("Database/DB_class.php");
  	require_once("parameter.php");

  	require_once('./Functions/function_naive_base_classifier.php');
  	require_once('./Functions/function_string_process.php');
  	require_once("./Class/class_data_set.php");
  	require_once("./Class/class_tfidf.php");
  	require_once("./Functions/function_cross_validation.php");

  	ini_set('memory_limit', '2048M');     //memory size
  	set_time_limit(0);                    //time limit
   

  	function tagProcess($string){

  		$string = str_replace("(","",$string);
		$string = str_replace(")","",$string);
		$string = str_replace("?"," ? ",$string);
		$tmp = explode(",", $string);

  		return $tmp[1];
  	}

  	//train the conditional probability of pos-tag
  	function trainCondProbTag($training, $class, $N){

  		$vocabulary = array();
  		for($i=0;$i<count($training);$i++){

  			$tmp = explode(" ", $training[$i]['content']);
  			$class_name = $training[$i]['class'];

  			for($j=0;$j<count($tmp);$j++){
  				if(trim($tmp[$j]) == NULL) continue;
  				
  				$tag = tagProcess($tmp[$j]);
  				if(trim($tag) == NULL) continue;
  				if(trim($tag) == ":") continue;

  				if($vocabulary[$tag] != NULL){
  					$vocabulary[$tag][$class_name]++;
  				}
  				else{
  					for($k=0;$k<count($class);$k++){
  						$vocabulary[$tag][$class[$k]] = 1;
  					}
  				}
  				//echo $tag.'</br>';
  			}
  		}//i

  		$cond_prob_tag = array();
  		foreach ($vocabulary as $tag => $value) {
  			//echo $tag.'</br>';
  			$count = 0;
  			for($i=0;$i<count($class);$i++){
  				$count += $vocabulary[$tag][$class[$i]];
  			}
  			//echo $count.'</br>';
  			for($i=0;$i<count($class);$i++){
  				$class_name = $class[$i];
  				$cond_prob_tag[$tag][$class_name] = $vocabulary[$tag][$class_name] / $count;
  			}
  		}

  		//var_dump($cond_prob_tag);
  		return $cond_prob_tag;
  	}
  	function calEntropy($cond_prob, $class){
  		
  		$entropy = array();
  		foreach ($cond_prob as $term => $value) {
  			//echo $term.'</br>';
  			$entropy[$term] = 0;
  			for($i=0;$i<count($class);$i++){
  				$class_name = $class[$i];
  				$value = $cond_prob[$term][$class_name];
  				$entropy[$term] += $value * log($value); 
  			}
  			$entropy[$term] = (-1)*$entropy[$term];
  			//echo $entropy[$term].'</br>';
  		}
  		return $entropy;
  	}

  	function setEntropy($entropy, $threshold){
  		$feature = array();
  		foreach ($entropy as $term => $value) {
  			if($value <= $threshold)
  				$feature[$term] = $value;
  		}
  		return $feature;
  	}

  	function calSalience($cond_prob, $class){

  		$salience = array();
  		foreach ($cond_prob as $term => $value) {
  			
  			//echo $term.'</br>';
  			$salience[$term] = 0;
  			for($i=0;$i<count($class)-1;$i++){
  				for($j=$i+1;$j<count($class);$j++){
  					$value = min($cond_prob[$term][$class[$i]],$cond_prob[$term][$class[$j]]) / max($cond_prob[$term][$class[$i]],$cond_prob[$term][$class[$j]]);
  					$salience[$term] += (1 - $value);
  				}
  			}

  			$saliencep[$term] = ($saliencep[$term] / count($class));
  			//echo $salience[$term].'</br>';
  		}

  		return $salience;
  	}

  	function setSalience($salience, $threshold){
  		$feature = array();
  		foreach ($salience as $term => $value) {
  			if($value >= $threshold)
  				$feature[$term] = $value;
  		}
  		return $feature;

  	}

  	function logLikelihood($testing, $prior, $cond_prob, $cond_prob_tag, $class, $feature){
  		
  		$result = array();
  		for($i=0;$i<count($testing);$i++){

  			for($j=0;$j<count($class);$j++){
  				$class_name = $class[$j];
  				$result[$i][$class_name] = 0;
  				//$result[$i][$class_name] = $prior[$class_name];
  			}

  			$tmp = explode(" ", $testing[$i]['content']);

  			for($j=0;$j<count($tmp);$j++){
  				if(trim($tmp[$j]) == NULL) continue;
  				$term = string_process($tmp[$j]);
  				$tag = tagProcess($tmp[$j]);
  				if(trim($tag) == NULL) continue;
  				if(trim($tag) == ":") continue;
  				if(trim($term) == 1) continue;
  				
  				for($k=0;$k<count($class);$k++){

  					$class_name = $class[$k];
  					if($cond_prob[$term] == NULL) continue;
  					if($feature[$term] == NULL) continue;
  					$result[$i][$class_name] += log($cond_prob[$term][$class_name]);
  					//$result[$i][$class_name] += log($cond_prob_tag[$tag][$class_name]);
  				}
  				//echo $term." ".$tag.'</br>';
  			}
  		}

  		return $result;

  	}

  	function decode($testing, $result){
  		$correct = 0;
  		
  		for($i=0;$i<count($testing);$i++){
  			$tmp = doublemax($result[$i]);
  			$decision = $tmp['i'];

  			//echo $testing[$i]['class']." ";
  			//echo $decision.'</br>';
  			if($testing[$i]['class'] == $decision)
  				$correct++;
  		}

  		echo "Acurracy: ".($correct/count($testing)).'</br>';
  	}

  	/*
      	content of data set
  	*/
  
      $data = array();
      $data_set = new dataSet($CATEGORY);
      $data = $data_set->getData();
      $fold = crossValidation(FOLDNUM, $data);


  	/*
      	unigram_model + n-cross vaildation
  	*/

      	for($i=0;$i<FOLDNUM;$i++){
        
        	$number_of_training = 0;
        	$number_of_testing = 0;
        	unset($training);
        	unset($testing);
        
        	for($j=0;$j<FOLDNUM;$j++){
          		if($i == $j){
            		for($k=0;$k<count($fold[$j]);$k++){
              			$id = $fold[$j][$k];
			            $testing[$number_of_testing]['content'] = $data[$id]['content'];
			            //$testing[$number_of_testing]['original_tweet'] = $data[$id]['original_tweet'];
			            $testing[$number_of_testing]['opinion'] = $data[$id]['opinion'];
			            //$testing[$number_of_testing]['emotion_meaning'] = $data[$id]['emotion_meaning'];
			            $testing[$number_of_testing]['category'] = $data[$id]['category'];
			            $number_of_testing++;
		            }
          		}
          		else{
            		for($k=0;$k<count($fold[$j]);$k++){
			            $id = $fold[$j][$k];
			            $training[$number_of_training]['content'] = $data[$id]['content'];
			            //$training[$number_of_training]['original_tweet'] = $data[$id]['original_tweet'];
			            $training[$number_of_training]['opinion'] = $data[$id]['opinion'];
			            //$training[$number_of_training]['emotion_meaning'] = $data[$id]['emotion_meaning'];
			            $training[$number_of_training]['category'] = $data[$id]['category'];
			            $number_of_training++;
            		}
          		}
        	}//j

        
        	/*
            	translate training & testing data
        	*/

            	for($j=0;$j<count($training);$j++){
              		$training[$j]['class'] = $training[$j]['opinion'];
            	}

            	for($j=0;$j<count($testing);$j++){
              		$testing[$j]['class'] = $testing[$j]['opinion'];
            	}

            	//var_dump($training);

        	/*
            	algo for n-gram feature + naive base classifiler
        	*/

	            $class = array(0 => "positive", 1 => "negative", 2 => "non-opinion");


	            $prior = array();
	            $cond_prob = array();
	            $prior = trainPrior($training, $class);  
	            //1:unigram, 2:bigram, 3:trigram
	            $N = 1;

	            $cond_prob = trainCondProb($training, $class, $N);
	        	//$cond_prob_tag = trainCondProbTag($training, $class, $N);
	            
	        	//feature selection entropy
	        	//$entropy = calEntropy($cond_prob, $class);
	        	//$feature = setEntropy($entropy, 0.03);
	        	
	        	//feature selection salience
	        	$salience = calSalience($cond_prob, $class);
	        	$feature = setSalience($salience, 1.5);
	        	//var_dump($feature);

	        	$result = logLikelihood($testing, $prior, $cond_prob, $cond_prob_tag, $class, $feature);
            
	        	decode($testing, $result);
            
      }//i

      echo "</br>--- unigram-ES model complelte ---</br>";

