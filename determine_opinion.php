<?php
/**
 * determine_opinion
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * naive base
 * 
 * 25 Jun 2013
 */

	function determine_opinion_NB($training_array, $testing_array, $category, $count_of_correct){

    echo "</br>/******************** Determine Opinion ********************/</br>";

    $class = array(0 => "positive", 1 => "negative");



    for($i=0;$i<count($category);$i++){

      unset($training);
      unset($testing);
      
      for($j=0;$j<count($class);$j++){
        $class_name = $class[$j];
        $number[$class_name] = 0;
      }


      $category_name = $category[$i];
      echo '</br>'.$category_name.'</br>';

      /*
          translate training data(opinion / non-opinion)
      */
          $count = 0;
          for($j=0;$j<count($training_array);$j++){
            if($training_array[$j]['category'] == $category_name && $training_array[$j]['opinion']!="non-opinion"){
              $training[$count]['class'] = $training_array[$j]['opinion'];
              $training[$count]['content'] = $training_array[$j]['content'];
              $training[$count]['category'] = $training_array[$j]['category'];
              for($k=0;$k<count($class);$k++){
                $class_name = $class[$k];
                if($training[$count]['class'] == $class_name) 
                  $number[$class_name]++;
              }
              $count++;
            }
         }
        
        //var_dump($number);
        //var_dump($training);
      /*
          translate testing data(opinion / non-opinion)
      */
          $count = 0;
          for($j=0;$j<count($testing_array);$j++){
            if($testing_array[$j]['category'] == $category_name){
              $testing[$count]['class'] = $testing_array[$j]['opinion'];
              $testing[$count]['content'] = $testing_array[$j]['content'];  
              $testing[$count]['category'] = $testing_array[$j]['category'];    
              $count++;
            }
          }
      
      /*
          oversample & undersample
      */
          
          //$training = undersampling($training, $class, $number);
          $training = oversampling($training, $class, $number);

      /*
          feature selection
      */
          //unset($feature);
          //$k = 13000;
          //$feature = mutual_information($training_array, $class, 1, $k);

      /*
          algo for n-gram feature + naive base classifiler 
      */

          $prior = trainPrior($training, $class);

          $N = 1;
      
          $cond_prob = trainCondProb($training, $class, $N);

          $result = applyNB($testing, $class, $prior, $cond_prob, $N, $feature, &$count_of_correct);

    }

 
		
		echo "</br>/******************** Opinion Mining Complete ********************/</br>";

		return $correct;

	}

  function determine_opinion_SVM($training_array, $testing_array, $category, $round, $SVMPATH, &$count_of_correct){

      
      echo "</br>/******************** Determine Opinion ********************/</br>";

      $class = array(0 => "positive", 1 => "negative");

      for($i=0;$i<count($category);$i++){

        unset($training);
        unset($testing);
      
        for($j=0;$j<count($class);$j++){
          $class_name = $class[$j];
          $number[$class_name] = 0;
        }
        
        $category_name = $category[$i];
        echo '</br>'.$category_name.'</br>';

        /*
            translate training data(opinion / non-opinion)
        */
          
            $count = 0;
            for($j=0;$j<count($training_array);$j++){
              if($training_array[$j]['category'] == $category_name && $training_array[$j]['opinion']!="non-opinion"){           
                $training[$count]['class'] = $training_array[$j]['opinion'];
                $training[$count]['content'] = $training_array[$j]['content'];
                $training[$count]['category'] = $training_array[$j]['category'];
                for($k=0;$k<count($class);$k++){
                  $class_name = $class[$k];
                  if($training[$count]['class'] == $class_name) 
                    $number[$class_name]++;
                }
                $count++;
              }
            }
      

        /*
            translate testing data(opinion / non-opinion)
        */

            $count = 0;
            for($j=0;$j<count($testing_array);$j++){
              if($testing_array[$j]['opinion'] != "non-opinion"){
                if($testing_array[$j]['category'] == $category_name){
                  $testing[$count]['class'] = $testing_array[$j]['opinion'];
                  $testing[$count]['content'] = $testing_array[$j]['content'];  
                  $testing[$count]['category'] = $testing_array[$j]['category'];    
                  $count++;
                }
              }
            }


        /*
            oversample & undersample
        */
          
            //$training = undersampling($training, $class, $number);
            $training = oversampling($training, $class, $number);

        /*
            feature selection
        */
            //unset($feature);
            //$k = 13000;
            //$feature = mutual_information($training_array, $class, 1, $k);


        /*
            main algorithm svm
        */

            $TFIDF = new TFIDF($class);
            $TFIDF->put_training_data($class, $training);
            $TFIDF->idf();
            $TFIDF->tf_idf();
            $TFIDF->set_testing_data("determine_opinion_{$category[$i]}",$testing, $class, $round);
            $TFIDF->set_trainging_data("determine_opinion_{$category[$i]}", $training, $class, $round);
    
            $string_train = "{$SVMPATH}svm-train -c 4 -g 1/2 {$SVMPATH}determine_opinion_{$category[$i]}_train{$round}";
            shell_exec($string_train);
            //"{$SVMPATH}svm-predict {$SVMPATH}extract_opinion_test{$i} extract_opinion_train{$i}.model {$SVMPATH}result{$i}.txt";
            $string_predict = "{$SVMPATH}svm-predict {$SVMPATH}determine_opinion_{$category[$i]}_test{$round} determine_opinion_{$category[$i]}_train{$round}.model {$SVMPATH}result{$round}.txt";
            //echo $string_predict.'</br>';
            //$string_predict = $svmPath."svm-predict -b 1 {$svmPath}short_text_test{$i} short_text_train{$i}.model {$svmPath}result{$i}.txt";
            echo shell_exec($string_predict);
            decode_svm("determine_opinion_{$category[$i]}",$testing ,$round, &$count_of_correct);
    }



  }





?>