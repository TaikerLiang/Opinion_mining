<?php

/**
 * unigram model
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 *
 * 
 * 18 Dec 2012
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
   

  /**
      content of data set
  **/
  
      $data = array();
      $data_set = new dataSet($CATEGORY);
      $data = $data_set->getData();
      $fold = crossValidation(FOLDNUM, $data);


  /**
      unigram_model + n-cross vaildation
  **/

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
        
            /*var_dump($prior);
            echo '</br>';
            var_dump($cond_prob);*/

            $result = applyNB($testing, $class, $prior, $cond_prob, $N, NULL);
        
            //var_dump($result);
            //echo '</br>';
            //calculate_accuracy($result, $category);
      }//i

      echo "--- unigram model complelte</br> ---";


?>