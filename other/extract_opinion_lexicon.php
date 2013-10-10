<?php

/**
 * lexicon
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 *
 * 
 * 18 Dec 2012
 */
	
  	function extract_opinion_lexicon($testing_array){
    
  		include("Database/mysql_connect.inc.php");

    	/**
      	content of training data
    	**/

    	$category = array(0 => "mobilephone", 1  => "camera", 2  => "movie");

	    $training_array = array();
	    $count = 0;
	    $number_of_opinion = 0;
	    $number_of_non_opinion = 0;

	    $db_training = new DB;
	    $db_training->connect_db(DB_SERVER, DB_USER, DB_PWD , "training"); 

	    for($i=0;$i<count($category);$i++){
	    
	      $name_of_category = $category[$i];

	      $sql = "SELECT original_tweet, content, opinion FROM {$name_of_category}";
	      $db_training->query($sql);
	        
	      while($str = $db_training->fetch_array()){ 
	        //transfer category
	        $training_array[$count]['original_tweet'] = $str['original_tweet'];

	        if($str['opinion'] == "positive"){
	          $training_array[$count]['content'] = $str['content'];
	          $training_array[$count]['class'] = "opinion";
	          $number_of_opinion++;
	        }
	        else if($str['opinion'] == "negative"){
	          $training_array[$count]['content'] = $str['content'];
	          $training_array[$count]['class'] = "opinion";
	          $number_of_opinion++;
	        }
	        else if($str['opinion'] == "non-opinion"){
	          $training_array[$count]['content'] = $str['content'];
	          $training_array[$count]['class'] = "non-opinion";
	          $number_of_non_opinion++;
	        }
	        $count++;
	      }
	    }

	    echo "training of opinion: ".$number_of_opinion.'</br>';
	    echo "training of non_opinion: ".$number_of_non_opinion.'</br>';



	    $opinion_lexicon =array();
	    $db_knowledge = new DB;
	    $db_knowledge->connect_db(DB_SERVER, DB_USER, DB_PWD , "knowledge");


	    //get opinion word from DB
	    $sql = "SELECT content FROM opinion_lexicon"; 
	    $db_knowledge->query($sql);

	    while($str = $db_knowledge->fetch_array()){ 
	    	$tmp = $str['content'];
	    	//echo $tmp.'</br>';
	    	$opinion_lexicon[$tmp] = 1;
	    }


	    //var_dump($opinion_lexicon);
	    $count_of_opinion_word = array();
	    

	    for($i=0;$i<count($training_array);$i++){
	    	$str = $training_array[$i]['content'];
  			$name_of_class = $training_array[$i]['class'];

  			//echo $training_array[$i]['original_tweet'].'</br>';
  			$count_of_opinion_word[$i] = 0;

  			$tmp = explode(" ", $str);
  			for($j=0;$j<count($tmp);$j++){
  				if($tmp[$j] == "") continue;
        		$word = string_process($tmp[$j]);
        		if($word == 1) continue;
        		//echo $word.'</br>';

        		if($opinion_lexicon[$word]!=NULL)
        			$count_of_opinion_word[$i]++;
  			}
  			echo $name_of_class.' '.$count_of_opinion_word[$i].'</br>';


	    }
	    echo '-----extract_opinion_lexicon complete------</br>';


	}

?>