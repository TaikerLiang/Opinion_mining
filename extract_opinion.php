<?php

/**
 * extact_opnion 
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * naive bayes claasifiler & support vector machine
 * use to extract text contains opinion
 * 
 *
 *
 * 3 Jun 2012
 */

  //naive base
  function extract_opinion_NB($training_array, $testing_array, $count_of_correct){
  //function extract_opinion_NB($data, $round, $fold, $foldNum, $class){
    


    //var_dump($testing_array);
    echo "</br>/******************** Extract opinion ********************/</br>";

    $class = array(0 => "opinion", 1  => "non-opinion");

    /*
        translate testing data(opinion / non-opinion)
    */
    

        for($i=0;$i<count($training_array);$i++){
          if($training_array[$i]['opinion'] == "non-opinion")
            $training_array[$i]['class'] = "non-opinion";
          else
            $training_array[$i]['class'] = "opinion";
        }


    /*
        translate testing data(opinion / non-opinion)
    */

        for($i=0;$i<count($testing_array);$i++){
          if($testing_array[$i]['opinion'] == "non-opinion")
            $testing_array[$i]['class'] = "non-opinion";
          else
            $testing_array[$i]['class'] = "opinion";
        }

        echo count($testing_array).'</br>';
        //var_dump($testing_array);

        
    /*
        oversample & undersample
    */
   
        //$training_array = undersampling($training_array, $class, $number);
        //$training_array = oversampling($training_array, $class, $number);

        //echo count($training);

    /*
        feature selection
    */
        //unset($feature);
        $k = 16000;
        $feature = mutual_information($training_array, $class, 1, $k);

    /*
        naive base classifiler 
    */
      
        $prior = array();
        $cond_prob = array();

        $prior = trainPrior($training_array, $class);

        $N = 1;
    
        $cond_prob = trainCondProb($training_array, $class, $N);

        $result = applyNB($testing_array, $class, $prior, $cond_prob, $N, $feature, &$count_of_correct);


    echo "</br>/******************** Extract opinion complete ********************/</br></br>";

    return $result;

  }





  //support vector machine
  function extract_opinion_SVM($training_array, $testing_array, $round){
    
    

    /*
        translate training data(opinion / non-opinion)
    */
    
        $class = array(0 => "opinion", 1  => "non-opinion");

        //initial
        $number = array();

        for($i=0;$i<count($class);$i++){
          $number[$class[$i]] = 0;
        }

        //main
        for($i=0;$i<count($training_array);$i++){
          
          if($training_array[$i]['opinion'] == "positive"){
            $training_array[$i]['class'] = "opinion";
            $number['opinion']++;
          }
          else if($training_array[$i]['opinion'] == "negative"){
            $training_array[$i]['class'] = "opinion";
            $number['opinion']++;
          }
          else{
            $training_array[$i]['class'] = "non-opinion";
            $number['non-opinion']++;
          }      
        }

        echo "</br>--- Training information ---</br>";
        echo "training of opinion: ".$number['opinion'].'</br>';
        echo "training of non_opinion: ".$number['non-opinion'].'</br>';

    /*
        translate testing data(opinion / non-opinion)
    */

        for($i=0;$i<count($testing_array);$i++){
          if($testing_array[$i]['opinion'] == "non-opinion")
            $testing_array[$i]['class'] = "non-opinion";
          else
            $testing_array[$i]['class'] = "opinion";
        }

    /*
        oversample & undersample
    */
   
        //$training_array = undersampling($training_array, $class, $number);
        //$training_array = oversampling($training_array, $class, $number);    


    /*
        feature selection
    */

        $k = 9000;
        $feature = mutual_information($training_array, $class, 1, $k);
        //var_dump($feature);
      

    /* 
        use the value of tf-idf to generate the data of svm
    */

        $TFIDF = new TFIDF($class);
        $TFIDF->put_training_data($class, $training_array);
        $TFIDF->idf();
        $TFIDF->tf_idf();
        $TFIDF->set_testing_data("extract_opinion",$testing_array, $class, $round, $feature);
        $TFIDF->set_trainging_data("extract_opinion", $training_array, $class, $round, $feature);
        
       
  }




?>