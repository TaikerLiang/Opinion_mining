<?php

/**
 * lexicon
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * use opinion lexicon to extact tweets contained opinion
 *
 *  Minqing Hu and Bing Liu. "Mining and Summarizing Customer Reviews." 
 *      Proceedings of the ACM SIGKDD International Conference on Knowledge 
 *      Discovery and Data Mining (KDD-2004), Aug 22-25, 2004, Seattle, 
 *      Washington, USA, 
 *  Bing Liu, Minqing Hu and Junsheng Cheng. "Opinion Observer: Analyzing 
 *      and Comparing Opinions on the Web." Proceedings of the 14th 
 *      International World Wide Web conference (WWW-2005), May 10-14, 
 *      2005, Chiba, Japan.
 * 
 * 15 Aug 2012
 */



	
	ini_set('include_path','.:/Applications/MAMP/htdocs/Library/');

	require_once("Input/read_file.php");
	require_once("Database/mysql_connect.inc.php");
    require_once("Database/DB_class.php");

    ini_set('memory_limit', '1024M');     //memory size
  	set_time_limit(0);                    //time limit


  	function string_process($string){
		
		$string = str_replace("(","",$string);
		$string = str_replace(")","",$string);
		$string = str_replace("?","",$string);
		
		$tmp = explode(",", $string);
		$term = strtolower($tmp[0]);
		//echo $term.'</br>';
		
		return trim($term);
	}



	function extract_opinion($array,$opinion_word){

		//var_dump($array);
		$count = 0;
		for($i=0;$i<count($array);$i++){
			$tmp = explode(" ", $array[$i]);

			for($j=0;$j<count($tmp);$j++){
				$term = string_process($tmp[$j]);
				if(trim($term) == "") continue;
				//echo $term.'</br>';

				if($opinion_word[$term] == 1){
					$count++;
					//echo $i.'yes!</br>';
					break;
				}
			}
		}

		echo $count.'</br>';

	}

/**
 	extract opinion word
**/

  	$opinion_word = array();

  	$db_opinion_lexicon = new DB;
   	$db_opinion_lexicon->connect_db($db_server, $db_user, $db_passwd , "knowledge");
   	$db_opinion_lexicon->query("SELECT content FROM opinion_lexicon");
	while ($str = $db_opinion_lexicon->fetch_array()){
		$opinion_word[$str['content']] = 1;
		//echo $str['content'].'</br>';
	}

	//var_dump($opinion_word);


/**
	content of testing data
**/
	
	$db_testing = new DB;
   	$db_testing->connect_db($db_server, $db_user, $db_passwd , "testing");

/**
	opinion
**/
		
	$testing_array = array();
	$count = 0;

   	$db_testing->query("SELECT content FROM opinion");
   	while ($str = $db_testing->fetch_array()){
		$testing_array[$count++] = $str['content'];
		//echo $str['content'].'</br>';
	}

	//var_dump($testing_array);
	echo "opinion: ".$count.'</br>';
	extract_opinion($testing_array,&$opinion_word);
	unset($testing_array);



/**
	non_opinion
**/	
	$testing_array = array();
	$count = 0;

   	$db_testing->query("SELECT content FROM non_opinion");
   	while ($str = $db_testing->fetch_array()){
		$testing_array[$count++] = $str['content'];
		//echo $str['content'].'</br>';
	}

	//var_dump($testing_array);
	echo "non_opinion: ".$count.'</br>';
	extract_opinion($testing_array,&$opinion_word);
	unset($testing_array);





	echo "complete</br>";
		
?>