<?php

/**
 * unigram_model_SVM
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 *
 * 
 * 9 July 2013
 */

	$path = '/Applications/MAMP/htdocs/Library/';
  	set_include_path(get_include_path() . PATH_SEPARATOR . $path);

	require_once("Input/read_file.php");
	require_once("Database/mysql_connect.inc.php");
	require_once("Database/DB_class.php");
	require_once("parameter.php");

	require_once("./Functions/function_cross_validation.php");
	require_once('./Functions/function_naive_base_classifier.php');
	require_once('./Functions/function_string_process.php');
	require_once("./Class/class_data_set.php");
	require_once("./Class/class_tfidf.php");

	ini_set('memory_limit', '2048M');     //memory size
	set_time_limit(0);                    //time limit


	/*
      content of data set
  	*/
  
    	$data = array();
    	$data_set = new dataSet($CATEGORY);
    	$data = $data_set->getData();
    	$fold = crossValidation(FOLDNUM, $data);


    /*
      	unigram_model_SVM + n-cross vaildation
  	*/


      	$class = array(0 => "positive", 1 => "negative", 2 => "non-opinion");

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

            /* 
        		use the value of tf-idf to generate the data of svm
    		*/    

        		$string_name = "unigram_model_SVM";
		        $TFIDF = new TFIDF($class);
		        $TFIDF->put_training_data($class, $training);
		        $TFIDF->idf();
		        $TFIDF->tf_idf();
		        $TFIDF->set_testing_data($string_name,$testing, $class, $i, NULL);
		        $TFIDF->set_trainging_data($string_name, $training, $class, $i, NULL);

            	$svmPath = "./libsvm/";
            	$string_train = $svmPath."svm-train -s 0 -b 1 {$svmPath}{$string_name}_train{$i}";
            	shell_exec($string_train);
            	$string_predict = $svmPath."svm-predict -b 1 {$svmPath}{$string_name}_test{$i} {$string_name}_train{$i}.model {$svmPath}result{$i}.txt";
            	echo shell_exec($string_predict).'</br>';


    	}//i


    	echo '--- unigram model SVM complete ---</br>'

?>