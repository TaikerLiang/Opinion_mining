<?

/**
 * naive base classifiler
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * Naive base classifier
 *
 * training: training data for naive base(array)
 * testing: testing data for naive base(array)
 * class: name of class wanting to classify(class)
 * N: n-gram feature select, n=1 => unigram, n=2 => bigram, n=3 => trigram(int)
 *
 * 
 * 7 Feb 2013
 **/


	function trainPrior($training, $class){

		$number_of_document = array();

		for($i=0;$i<count($class);$i++){
			$name_of_class = $class[$i];
			$number_of_document[$name_of_class] = 0;
		}

  	for($i=0;$i<count($training);$i++){
  		$number_of_document[$training[$i]['class']]++;
  	}


  	for($i=0;$i<count($class);$i++){
  		$name_of_class = $class[$i];
  		$prior[$name_of_class] = $number_of_document[$name_of_class] / count($training);
  	}
  		return $prior;
	}

	function trainCondProb($training, $class, $N){

		  //var_dump($class);
  		$number_of_term=0;
  		$count = array();
  		$vocabulary = array();
  		$length = array();

  		for($i=0;$i<count($class);$i++){
  			$name_of_class = $class[$i];
  			$length[$name_of_class] = 0;
  		}

  		for($i=0;$i<count($training);$i++){

  			$str = $training[$i]['content'];
  			$name_of_class = $training[$i]['class'];

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
            $length[$name_of_class]++;

            if($vocabulary[$term][$name_of_class]!=NULL){
              $vocabulary[$term][$name_of_class]++;
            }
            else{
              $vocabulary[$term][$name_of_class] = 1;
            }
            $term=" ";       	
        	}        	
  		}

  		$condprob = array();

  		$total_of_term = count($vocabulary);

  		foreach ($vocabulary as $term => $value) {
  			//echo $term.'</br>';
  			for($i=0;$i<count($class);$i++){ 
  				$name_of_class = $class[$i];
  				//echo $name_of_class.'</br>';
  				if($vocabulary[$term][$name_of_class]==NULL){
  					$condprob[$term][$name_of_class] = 1 / ($length[$name_of_class] + $total_of_term);
  				}
  				else{
  					$condprob[$term][$name_of_class] = ($vocabulary[$term][$name_of_class] + 1) / ($length[$name_of_class] + $total_of_term); 
  				}
  			}
  		}

  		//var_dump($condprob);
  		return $condprob;
	}

	function applyNB($testing ,$class ,$prior, $cond_prob, $N, $feature, $count_of_correct){

    //var_dump($feature);
		//var_dump($class);
		//var_dump($testing);
		//var_dump($prior);
		//var_dump($cond_prob);
    //echo count($class).'</br>';

		$count_roc = 0;
		$result = array();
    $roc_data = array();


		for($i=0;$i<count($class);$i++){
			for($j=0;$j<count($class);$j++){
				$result[$class[$i]][$class[$j]] = 0;
			}	
		}
		//var_dump($result);

		for($i=0;$i<count($testing);$i++){

			$name_of_class = $testing[$i]['class'];
			$str = $testing[$i]['content'];
			
			//echo $testing[$i]['content'].'</br>';
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
        	
      for($l=0;$l<count($class);$l++){
        $score[$class[$l]] = log($prior[$class[$l]]);
        	   	
        for($j=0;$j<count($not_stop_word)-($N-1);$j++){
        	for($k=$j;$k<$j+$N;$k++)
            $term = $term.' '.$not_stop_word[$k];

          $term = trim($term);
          //echo $term.'</br>';
         	
          if($feature!=NULL){
            if($cond_prob[$term][$class[$l]] != NULL && $feature[$term] != NULL){
                //echo $class[$l].'</br>';
                //echo $term.' '.$cond_prob[$term][$class[$l]].'</br>';
                $score[$class[$l]] += log($cond_prob[$term][$class[$l]]);
              }
          }
          else{
            if($cond_prob[$term][$class[$l]] != NULL){
              $score[$class[$l]] += log($cond_prob[$term][$class[$l]]);
            }
          }
                
          $term=" ";
        }
        //echo '-------</br>';
      }

      if(count($class)==2){
        $tmp_roc = 0;
        foreach ($score as $key => $value) {
          if($tmp_roc == 0)
            $tmp_roc = $value;
          else
            $tmp_roc = $tmp_roc - $value; 
        }
            
        $roc_data[$count_roc]['value'] = $tmp_roc;
        $roc_data[$count_roc]['class'] = $name_of_class;
        $count_roc++;
            //echo $name_of_class.' '.$tmp_roc.'</br>';   
      }
        
      $tmp = doublemax($score);
      $decision = $tmp['i'];
      $result[$name_of_class][$decision]++;
 	
      $returnData[$i]['original_tweet'] = $testing[$i]['original_tweet'];
      $returnData[$i]['content'] = $testing[$i]['content'];
      $returnData[$i]['opinion'] = $testing[$i]['opinion'];
      $returnData[$i]['category'] = $testing[$i]['category'];
      $returnData[$i]['class'] = $testing[$i]['class'];
      $returnData[$i]['emotion_meaning'] = $testing[$i]['emotion_meaning'];
      $returnData[$i]['decision'] = $decision;

         
      //$count++;
		}
		//var_dump($result);



    echo "</br>--- Analysis ---</br>";
		calculate_accuracy($result, $class);


    //evaluation
    if(count($class)==2){
      for($i=0;$i<count($class);$i++){
        if($class[$i]=="opinion") continue;

        $count_of_correct += $result[$class[$i]][$class[$i]];
      }
      evaluation($result, $class);
      ROC($roc_data, $class);
      //echo "{$count_of_correct}</br>";
    }		

    
		//var_dump($returnData);
		unset($result);


    //check_emotion_meaning($returnData);

		return $returnData;
	}

	function calculate_accuracy($result, $class){
    

    //echo "</br>--- Testing information ---</br>";
		$correct = 0;
		$total_of_document = 0;
		for($i=0;$i<count($class);$i++){

			$name_of_class = $class[$i];
			echo $name_of_class.' ';
			$tmp = 0;
			for($j=0;$j<count($class);$j++){
				$tmp += $result[$name_of_class][$class[$j]];
			}
			//echo $tmp.'</br>';
			$total_of_document += $tmp;
			$correct += $result[$name_of_class][$name_of_class];
		}

    echo '</br></br>';
		$accuracy = $correct / $total_of_document;
		echo 'Accuracy: '.$accuracy.'</br></br>';
		//var_dump($result);
		echo '</br>';
	}


?>