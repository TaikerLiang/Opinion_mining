<?php
/**
 * function_feature_selection
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 *
 * training: training data for naive base(array)
 * testing: testing data for naive base(array)
 * category: name of category wanting to classify(category)
 * N: n-gram feature select, n=1 => unigram, n=2 => bigram, n=3 => trigram(int)
 * K: top k feature will be selected
 * 
 * 16 Feb 2013
 **/


	function mutual_information($training, $category, $N, $K){
		
    echo '</br>----- feature selection -----</br>';
		
    //var_dump($training);

    $feature_candidate = array();
		$sentences = array();
		$number_of_document_in_category = array();
		$dependence = array();
		

		$total_of_document = count($training);
		
		for($i=0;$i<count($category);$i++){
  		$name_of_category = $category[$i];
  		$number_of_document_in_category[$name_of_category] = 0;
  	}



		for($i=0;$i<count($training);$i++){

  			$str = $training[$i]['content'];
  			$name_of_category = $training[$i]['class'];
  			$number_of_document_in_category[$name_of_category]++;

  			$tmp = explode(" ", $str);
  			$c=0;$term = "";
  			$not_stop_word = array();
  			for($j=0;$j<count($tmp);$j++){
  				
  				if($tmp[$j] == "") continue;
        		$word = string_process($tmp[$j]);
        		if($word == 1) continue;

        		//echo $word.'</br>';
        		$not_stop_word[$c++] = $word;
        	}

        	for($j=0;$j<count($not_stop_word)-($N-1);$j++){
        		for($k=$j;$k<$j+$N;$k++)
              		$term = $term.' '.$not_stop_word[$k];

              	$term = trim($term);

              	if($vocabulary[$term][$name_of_category]!=NULL){
              		$vocabulary[$term][$name_of_category]++;
              	}
              	else{
              		$vocabulary[$term][$name_of_category] = 1;
              	}
              	$term=" ";       	
        	}        	
  		}


  		foreach ($vocabulary as $term => $value) {
  			
  			//echo $term.'</br>';
  			$documents_contains_term = 0;
  			for($i=0;$i<count($category);$i++){
  				$name_of_category = $category[$i];
  				$documents_contains_term += $vocabulary[$term][$name_of_category];
  				//echo $vocabulary[$term][$name_of_category].'</br>';
  			}

  			for($i=0;$i<count($category);$i++){
  				$name_of_category = $category[$i];
  				//N11:number the co-occurrence of the feature F and the class C
        	//N10:number of documents contains the feature F but is not in C
        	//N01:number of documents in class C but doesn't contain F
        	//N00:number of documents not in C and doesn't contain F

  				if($vocabulary[$term][$name_of_category] == NULL)
  					$vocabulary[$term][$name_of_category] = 1;

        	$N11 = $vocabulary[$term][$name_of_category];
          $N10 = ($documents_contains_term - $vocabulary[$term][$name_of_category]);
          $N01 = ($number_of_document_in_category[$name_of_category] - $vocabulary[$term][$name_of_category]);
          $N00 = ($total_of_document - $number_of_document_in_category[$name_of_category] - $documents_contains_term + $N10);

          //echo $N11.' '.$N10.' '.$N01.' '.$N00.'<br>';
          $part1 = ($N11/$total_of_document) * log(($total_of_document*$N11+1)/(($N11+$N10)*($N11+$N01)));
          $part2 = ($N01/$total_of_document) * log(($total_of_document*$N01+1)/(($N00+$N01)*($N11+$N01)));
          $part3 = ($N10/$total_of_document) * log(($total_of_document*$N10+1)/(($N11+$N10)*($N00+$N10)));
          $part4 = ($N00/$total_of_document) * log(($total_of_document*$N00+1)/(($N01+$N00)*($N10+$N00)));

          $dependence[$term][$class_name] = $part1 + $part2 + $part3 + $part4;

          	//echo $dependence[$term][$class_name].'</br>';
  			}

  			$result = doublemax($dependence[$term]);
        $feature_candidate[$term] = $result['m'];
        //var_dump($result);
        //echo '</br>';
        unset($dependence);    
  		}
		
  		arsort($feature_candidate);
  		//$k = 7000; 
      echo "Top K : ".$K.'</br>';
      $c =0;
      $feature = array();

      foreach ($feature_candidate as $term => $value) {
        //echo $c.' '.$term.' '.$value.'</br>';
        $feature[$term] = $value;
        $c++;
        if($c==$K) break;
      }
      //var_dump($feature);
      return $feature;
	}




?>