<?php
/**
 * system
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * main function of whole system.
 * 
 * 25 Jun 2013
 */


	  $path = '/Applications/MAMP/htdocs/Library/';
	  set_include_path(get_include_path() . PATH_SEPARATOR . $path);

  	require_once("Input/read_file.php");
  	require_once("Database/mysql_connect.inc.php");
  	require_once("Database/DB_class.php");
    
    require_once("parameter.php");
    require_once("extract_opinion.php");
    require_once("short_text_classification.php");
    require_once("determine_opinion.php");

    require_once("./Class/class_data_set.php");
    require_once("./Class/class_tfidf.php");
    require_once('./Functions/function_naive_base_classifier.php');
    require_once('./Functions/function_string_process.php');
    require_once('./Functions/function_feature_selection.php');
    require_once("./Functions/function_others.php");
    require_once("./Functions/function_libsvm.php");
    require_once("./Functions/function_evaluation.php");
    require_once("./Functions/function_ROC.php");
    require_once("./Functions/function_undersample.php");
    require_once("./Functions/function_oversample.php");
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
       set training & testing data set
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

        $count_of_testing = count($testing);
        $count_of_correct = 0;


        /*
            Extract tweets containing Opinion NB
        */
            //$tmp = extract_opinion_NB(&$training, $testing, &$count_of_correct);

            echo $count_of_testing.'</br>';
        /*
            Extract tweets containing Opinion SVM
        */
            
            
            extract_opinion_SVM(&$training, $testing, $i);
            $string_train = "{$SVMPATH}svm-train -c 4 -g 1/2 {$SVMPATH}extract_opinion_train{$i}";
            shell_exec($string_train);
            $string_predict = "{$SVMPATH}svm-predict {$SVMPATH}extract_opinion_test{$i} extract_opinion_train{$i}.model {$SVMPATH}result{$i}.txt";
            echo shell_exec($string_predict).'</br>';
            $tmp = decode_svm("extract_opinion",$testing ,$i, &$count_of_correct);
            echo $count_of_correct.'</br>';
            


            unset($testing);
            $count = 0;

            for($j=0;$j<count($tmp);$j++){
              if($tmp[$j]['decision'] == "opinion"){
                $testing[$count]['content'] = $tmp[$j]['content'];
                $testing[$count]['opinion'] = $tmp[$j]['opinion'];
                $testing[$count]['category'] = $tmp[$j]['category'];
                $count++;
              }
            }

            unset($tmp);
            //var_dump($testing);

        /*
            Short text classification
        */
            //naive bayes
            //$tmp = short_text_classification_NB(&$training, $testing);
            
            //SVM(just for get information of result, can't used for whole system)
            
            //$tmp = short_text_classification_SVM(&$training, $testing, $i, $SVMPATH);
            
            

            /*unset($testing);
            $testing = $tmp;
            unset($tmp);*/
            //var_dump($testing);


        
        /*
            Training Multiple Classifiers in Distinct Categories
        */
            //NB
            //$tmp = determine_opinion_NB($training, $testing, $CATEGORY, &$count_of_correct);

            //SVM
            //echo $SVMPATH.'</br>';
            //$tmp = determine_opinion_SVM(&$training, $testing, $CATEGORY, $i, $SVMPATH, &$count_of_correct);


        /*
            The accuracy of whole system
        */

            $final_accuracy = $count_of_correct/$count_of_testing;
            echo "</br>Final Accuracy: {$final_accuracy}</br>";

            $result[$i] = $final_accuracy;
    }
    
    for($i=0;$i<count($result);$i++){
      $tmp += $result[$i];
    }

    $average_accuracy = $tmp/FOLDNUM;
    echo "</br>The average accuracy after cross-validation: {$average_accuracy}</br>";

    echo "</br> ------system complete------ </br>";


?>