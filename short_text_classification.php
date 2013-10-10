<?php
/**
 * short_text_classification
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * Naive base classifier, N = 1(unigorm) feature selction
 * mutual infomation feature selection method and select Top k feature
 *
 * 9 Jan 2013
 **/


  function short_text_classification_NB($training_array, $testing_array){


      echo "</br>/******************** Short Text Classfication ********************/</br>";

      
      $class = array(0 => "mobilephone", 1  => "camera", 2  => "movie");


      /*
          translate testing data(opinion / non-opinion)
      */

          for($i=0;$i<count($training_array);$i++){
            $training_array[$i]['class'] = $training_array[$i]['category'];
          }

      /*
          translate testing data(opinion / non-opinion)
      */
          for($i=0;$i<count($testing_array);$i++){
            $testing_array[$i]['class'] = $testing_array[$i]['category'];
          }



  
      /**
        naive base classifier
      **/

      $prior = trainPrior($training_array, $class);

      //1:unigram, 2:bigram, 3:trigram
      $N = 1;

      $cond_prob = trainCondProb($training_array, $class, $N);
    
      $result = applyNB($testing_array, $class, $prior, $cond_prob, $N, NULL,NULL);

      //calculate_accuracy($result, $category);
      //var_dump($result);

      echo "</br>/******************** Short Text Classfication Complelte ********************/</br>";

      return $result;

  }


  function short_text_classification_SVM($training_array, $testing_array, $round, $svmPath){

    echo "</br>/******************** Short Text Classfication SVM ********************/</br>";

    $class = array(0 => "mobilephone", 1  => "camera", 2  => "movie");

    //initial
    $number = array();

    for($i=0;$i<count($class);$i++){
      $number[$class[$i]] = 0;
    }


    /*
        translate training data(opinion / non-opinion)
    */
        
        for($i=0;$i<count($training_array);$i++){
          $class_name = $training_array[$i]['category'];
          $training_array[$i]['class'] = $class_name;
          $number[$class_name]++;
        }

        var_dump($number);
        echo '</br>';

    /*
        translate testing data(opinion / non-opinion)
    */

        for($i=0;$i<count($testing_array);$i++){
          $testing_array[$i]['class'] = $testing_array[$i]['category'];
        }

    /*
        oversample & undersample
    */
   
        //$training_array = undersampling($training_array, $class, $number);
        //$training_array = oversampling($training_array, $class, $number);    


    /*
        feature selection
    */

        //$k = 11000;
        //$feature = mutual_information($training_array, $class, 1, $k);
        //var_dump($feature);  

    /* 
        use the value of tf-idf to generate the data of svm
    */    
        $TFIDF_ST = new TFIDF($class);
        $TFIDF_ST->put_training_data($class, $training_array);
        $TFIDF_ST->idf();
        $TFIDF_ST->tf_idf();
        $TFIDF_ST->set_testing_data("short_text",$testing_array, $class, $round);
        $TFIDF_ST->set_trainging_data("short_text", $training_array, $class, $round);

        //var_dump($testing);

        $tfidf_value = $TFIDF_ST->get_tfidf_value_train();

        
        $string_train = $svmPath."svm-train -s 0 -b 1 {$svmPath}short_text_train{$round}";
        shell_exec($string_train);
        $string_predict = $svmPath."svm-predict -b 1 {$svmPath}short_text_test{$round} short_text_train{$round}.model {$svmPath}result{$round}.txt";
        echo shell_exec($string_predict);
        $tmp = decode_svm_muti("short_text", $testing_array, $round, $class);

        return $tmp;
  } 

?>