<?php
/**
 * Apoorv's method (Sentiment Analysis of Twitter Data)
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 *
 * 
 * 24 July 2013
 */

	
	$path = '/Applications/MAMP/htdocs/Library/';
  	set_include_path(get_include_path() . PATH_SEPARATOR . $path);

	require_once("Input/read_file.php");
	require_once("Database/mysql_connect.inc.php");
	require_once("Database/DB_class.php");
	
	require_once("../parameter.php");
	require_once("../Functions/function_cross_validation.php");
	require_once('../Functions/function_naive_base_classifier.php');
	require_once("../Class/class_data_set.php");
	require_once("../Class/class_tfidf.php");

	ini_set('memory_limit', '2048M');     //memory size
	set_time_limit(0);                    //time limit

	


	function string_process($string){
			
		$string = str_replace("(","",$string);
		$string = str_replace(")","",$string);
		$string = str_replace("?"," ? ",$string);
	
		$tmp = explode(",", $string);

		//if($tmp[3]=="ST" || trim($tmp[0])==NULL || $tmp[1]==":") 
			//return 1;

		/*if($tmp[0] == "the" || $tmp[0] == "a" || trim($tmp[0])==NULL)
			return 1;*/
		
		$term = strtolower($tmp[0]);

		//echo $term.'</br>';
		
		return trim($term);
	}

	function setVocabulary($class, $training){

		$vocabulary = array();
		$count = 6;
		for($i=0;$i<count($training);$i++){
			$doc = $training[$i]['content'];
			$tmp = explode(" ", $doc);
			for($j=0;$j<count($tmp);$j++){
				$word = string_process($tmp[$j]);
				if($vocabulary[$word] == NULL)
					$vocabulary[$word] = $count++;
			}
		}

		return $vocabulary;
	}
	/*
        features: 
        0: count of negation words
       	1: count of positive words
        2: count of negative words
        3: count of positive emoticons
        4: count of negative emoticons
        5: prior polarity scores of all the words
    */

	function setTrainingData($class, $training, $vocabulary, $DAL, $round){

		$fileNameOut = "../libsvm/Apoorv_train".$round;
		$fw = openFileWrite($fileNameOut);

		for($i=0;$i<count($training);$i++){
          	
          	$string = "";
          	for($j=0;$j<count($class);$j++){
				$class_name = $class[$j];
				if($training[$i]['class'] == $class_name){
					$num = $j+1;
					$string = "{$num} ";
				}
			}

			$tmp = explode(" ", $training[$i]['content']);
			$negation_words = 0;
			$positive_words = 0;
			$negative_words = 0;
			$polarity_scores = 0;
			$document = array();

			for($j=0;$j<count($tmp);$j++){
				$word = string_process($tmp[$j]);
				if(trim($word) == "") continue;
				//echo $word.'</br>';
				//feature 0
				$tmp2 = explode("+", $word);
				if(count($tmp2) == 2)
					$negation_words++;
				//feature 1 & 2
				if($DAL[$word] >= 0.8)
					$positive_words++;
				else if($DAL[$word] < 0.5)
					$negative_words++;

				$polarity_scores+= $DAL[$word];

				if($document[$word]!=NULL)
					$document[$word]++;
				else
					$document[$word] = 1;
				
			}
			$string = $string."1:{$negation_words} ";
			$string = $string."2:{$positive_words} ";
			$string = $string."3:{$negative_words} ";
			//echo $negation_words.' '.$positive_words.' '.$negative_words.'</br>';
			//echo $polarity_scores.'</br>';
			//feature 3 & 4
			$positive_emoticons = 0;
			$negative_emoticons = 0;
			$tmp = explode(" ", $training[$i]['emotion_meaning']);
			for($j=0;$j<count($tmp);$j++){
				//echo $tmp[$j].'</br>';
				if($tmp[$j] == "P")
					$positive_emoticons++;
				else
					$negative_emoticons++;
			}
			$string = $string."4:{$positive_emoticons} ";
			$string = $string."5:{$negative_emoticons} ";
			$string = $string."6:{$polarity_scores} ";

			$tmp = array();
			foreach ($document as $word => $value) {
				$tmp[$vocabulary[$word]] = $value;
			}

			ksort($tmp);
			foreach ($tmp as $key => $value) {
				$string = $string."{$key}:{$value} ";
				# code...
			}
			//sort($tmp);
			//var_dump($tmp);
			//echo $positive_emoticons.' '.$negative_emoticons.'</br>';
			//echo $string.'</br>';

			fwrite($fw, $string."\n");
		}

	}

	function setTestingData($class, $testing, $vocabulary, $DAL, $round){

		$fileNameOut = "../libsvm/Apoorv_test".$round;
		$fw = openFileWrite($fileNameOut);

		for($i=0;$i<count($testing);$i++){
          	
          	$string = "";
          	for($j=0;$j<count($class);$j++){
				$class_name = $class[$j];
				if($testing[$i]['class'] == $class_name){
					$num = $j+1;
					$string = "{$num} ";
				}
			}

			$tmp = explode(" ", $testing[$i]['content']);
			$negation_words = 0;
			$positive_words = 0;
			$negative_words = 0;
			$polarity_scores = 0;
			$document = array();

			for($j=0;$j<count($tmp);$j++){
				$word = string_process($tmp[$j]);
				if(trim($word) == "") continue;
				//echo $word.'</br>';
				//feature 0
				$tmp2 = explode("+", $word);
				if(count($tmp2) == 2)
					$negation_words++;
				//feature 1 & 2
				if($DAL[$word] >= 0.8)
					$positive_words++;
				else if($DAL[$word] < 0.5)
					$negative_words++;

				$polarity_scores+= $DAL[$word];

				if($document[$word]!=NULL)
					$document[$word]++;
				else
					$document[$word] = 1;
				
			}
			$string = $string."1:{$negation_words} ";
			$string = $string."2:{$positive_words} ";
			$string = $string."3:{$negative_words} ";
			//echo $negation_words.' '.$positive_words.' '.$negative_words.'</br>';
			//echo $polarity_scores.'</br>';
			//feature 3 & 4
			$positive_emoticons = 0;
			$negative_emoticons = 0;
			$tmp = explode(" ", $testing[$i]['emotion_meaning']);
			for($j=0;$j<count($tmp);$j++){
				//echo $tmp[$j].'</br>';
				if($tmp[$j] == "P")
					$positive_emoticons++;
				else
					$negative_emoticons++;
			}
			$string = $string."4:{$positive_emoticons} ";
			$string = $string."5:{$negative_emoticons} ";
			$string = $string."6:{$polarity_scores} ";
			
			$tmp = array();
			foreach ($document as $word => $value) {
				if($vocabulary[$word] == NULL)
					continue;
				$tmp[$vocabulary[$word]] = $value;
			}

			ksort($tmp);
			foreach ($tmp as $key => $value) {
				$string = $string."{$key}:{$value} ";
			}

			fwrite($fw, $string."\n");
		}

	}

	function processOfDAL($response){

		$tmp = explode(" ", $response);
		$tmp2 = explode("=", $tmp[2]);
		$value = str_replace('"', "", $tmp2[1]);
		//echo $value.'</br>';

		return $value;
		
	}

	
  	/*
  		build the ditionary of DAL and save int the DB. Just do for once
  	*/

  	/*	
  		$fileNameOut = "./Data/ditionary_DAL.txt";
		$fw = openFileWrite($fileNameOut);

    	$data = array();
    	$data_set = new dataSet($CATEGORY);
    	$data = $data_set->getData();
    	$fold = crossValidation(FOLDNUM, $data);


    	//connect to db to get old data information
  		$db = new DB;
   		$db->connect_db(DB_SERVER, DB_USER, DB_PWD, "DAL");
    	
    	$vocabulary = array();
		$count = 7;
		$valence = array();
		for($i=0;$i<count($data);$i++){
			$doc = $data[$i]['content'];
			$tmp = explode(" ", $doc);
			for($j=0;$j<count($tmp);$j++){
				$word = string_process($tmp[$j]);
				if(trim($word)=="") continue;
				if($vocabulary[$word] == NULL){
					$vocabulary[$word] = $count++;
					$url = "http://compling.org/cgi-bin/DAL_sentence_xml.cgi?sentence={$word}";
					$response = file_get_contents($url);
					$value = processOfDAL($response);
					if(trim($value) == "") continue;
					$valence[$word] = $value/3;
				}
			}
		}

		foreach ($valence as $word => $value) {
			$string = $word." ".$value."\n";
			fwrite($fw, $string);
			$word = str_replace("'", "''", $word);
			$sql = "INSERT INTO dalTable (word, valence) 
  					VALUES ('{$word}','{$value}')";
  			$db->query($sql);
		}
	*/

	
	/*
		get the DAL from DB
	*/
		$db = new DB;
   		$db->connect_db(DB_SERVER, DB_USER, DB_PWD, "DAL");
   		$DAL = array();

   		$sql = "SELECT word, valence FROM dalTable";
        $db->query($sql);

        while ($str = $db->fetch_array()) {
        	$DAL[$str['word']] = $str['valence'];
        }

        //var_dump($DAL);



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
	              $testing[$number_of_testing]['emotion_meaning'] = $data[$id]['emotion_meaning'];
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
	              $training[$number_of_training]['emotion_meaning'] = $data[$id]['emotion_meaning'];
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
        		use the count of each word to generate the data of svm
    		*/    


        		//get vocabulary
        		$vocabulary = setVocabulary($class, $training);
        		
        		//var_dump($vocabulary);
        		


        		setTrainingData($class, $training, $vocabulary, $DAL, $i);
        		setTestingData($class, $testing, $vocabulary, $DAL, $i);
	
        		$string_name = "Apoorv";
            	$svmPath = "/Applications/MAMP/htdocs/Twitter/libsvm/";
            	$string_train = $svmPath."svm-train -s 0 -b 1 {$svmPath}{$string_name}_train{$i}";
            	shell_exec($string_train);
            	$string_predict = $svmPath."svm-predict -b 1 {$svmPath}{$string_name}_test{$i} {$string_name}_train{$i}.model {$svmPath}result{$i}.txt";
            	echo shell_exec($string_predict).'</br>';
            	


    	}//i


    	echo '--- unigram model SVM complete ---</br>'

?>