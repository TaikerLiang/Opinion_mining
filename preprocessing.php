<?php

/**
 * preprocessing
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * Filter RT,
 * replace username with USER
 * discard the tweet which contains web address
 * If number of words in tweet < 5,then discard it.
 * 
 * 15 Aug 2012
 */
  
	//ini_set('include_path','.:/Applications/MAMP/htdocs/Library/.:/Applications/MAMP/svn/zendframework/trunk/library/Zend/');
  //ini_set('include_path','.:/Applications/MAMP/svn/zendframework/trunk/library/Zend/');
  $path = '/Applications/MAMP/htdocs/Library/';
  set_include_path(get_include_path() . PATH_SEPARATOR . $path);

	require_once("Input/read_file.php");
	require_once("Database/mysql_connect.inc.php");
  require_once("Database/DB_class.php");

  //ini_set('include_path','.:/Applications/MAMP/svn/zendframework/trunk/library/Zend/');
  
  require_once("./function_of_preprocessing.php");
  //require_once("./test.php");
  //require_once 'Zend/Loader.php';

	ini_set('memory_limit', '2048M');     //memory size
  set_time_limit(0);                    //time limit
   
  $dirName = './Data/OriginalTweets/';
  $dirList = openDirectory($dirName);	
  $log = './Log/Preprocessing/log_filtering';
	$fwlog = openFileWrite($log);

  function process_slang($result,$db_slang){

      $term = trim($result['Word']);
      
      /**
        not sure correct
      **/  
      if (preg_match("/^[a-zA-Z]/",$result['Word'])){
          $table = strtolower(substr(trim($result['Word']), 0, 1));
      }
      else{
          $table = "other";
      }
         
      $db_slang->query("SELECT meaning FROM $table WHERE slang = '{$term}' ");
      //the result of searching slang dictionary.
      $str = $db_slang->fetch_array();
      if($str['meaning']!=null){
            //echo $term.' '.$str['meaning'].'</br>';
            $result['Word'] = $str['meaning'];
            $result['English'] = "EN";
      }
  }




	
	$db_knowledge = new DB;
	$db_knowledge->connect_db($db_server, $db_user, $db_passwd , "knowledge");
	
  $db_knowledge->query("SELECT icon, meaning FROM emotions");
	while ($str = $db_knowledge->fetch_array()){
		$emotions_dictionary[$str['icon']] = $str['meaning'];
	}

	$db_knowledge->query("SELECT word FROM stop_words");
  while($str = $db_knowledge->fetch_array()){
    //echo $str['word'].'</br>';
    $stop_word[trim($str['word'])] = 1;
  }

  /*$db_slang = new DB;
  $db_slang->connect_db($db_server, $db_user, $db_passwd , "slang_dictionary");
  //$db_slang->query("SELECT ID FROM other ");
  $db_slang->query("SELECT meaning FROM l WHERE slang = 'lol' ");
  $str = $db_slang->fetch_array();
  echo $str['meaning'].'</br>';*/

	$db_trainging = new DB;
  $db_trainging->connect_db($db_server, $db_user, $db_passwd , "training");
  $db_trainging->query("SELECT original_tweet FROM mobilephone");
  //just for test
  $str = $db_trainging->fetch_array();
  $original_tweet = $str['original_tweet'];
    
  //echo $original_tweet.'</br>';

  //just for test
  $original_tweet = "I wanna win a Tegra 3 device from Canon @androidguys and @nvidiategra #AGTegra3 HTC one x please :) I need an upgrade asap :D";
  

  echo $original_tweet.'</br>';


  //這個while應該包在最外層
  /*while($result = $db_trainging->fetch_array()){
      // do something you want...
    	echo $result.'</br>';
  }*/

   
  //remove RT
  $original_tweet = remove_character_of_retweet($original_tweet);
  $tmp = explode(" ", $original_tweet);
  $emotions = "";
  $emotions_meaning = "";
  $tag = "";

  for($i=0;$i<count($tmp);$i++){
    //replace user name
    $original_tweet = replace_username($original_tweet,trim($tmp[$i]));
    //remove some special character
    $original_tweet = remove_Special_character($original_tweet,trim($tmp[$i]));
    //extract all of emotions
    $original_tweet = extract_emotions($original_tweet,&$emotions_dictionary,trim($tmp[$i]),&$emotions,&$emotions_meaning);
    //extract tag
    $original_tweet = extract_tag($original_tweet,trim($tmp[$i]),&$tag);
  }

  echo $original_tweet.'</br>';
  echo $emotions." ".$emotions_meaning.'</br>';
  echo $tag.'</br>';

  $result = "";
  $original_tweet = str_replace(",","",$original_tweet); 
  $original_tweet = str_replace("!","",$original_tweet);
  $original_tweet = str_replace(".","",$original_tweet);

  //echo $original_tweet.'</br>';

  //input origianl tweet and call tree tagger
  $output = POS_tagger($original_tweet);
  //echo $output.'</br>';

  $tag = explode("\n", $output);
  

  //translate the term into format (Word, POS, English_or_not, Stop_word)
  for($i=0;$i<count($tag);$i++){

    if(trim($tag[$i]==NULL))
      continue;

    $result['Word'] = "";
    $result['POS'] = "";
    $result['English'] = "";
    $result['Stop_word'] = "";
    
    $tmp = explode("\t", $tag[$i]);
    //echo $tmp[0]." ".$tmp[1]."</br>";
    
    //set Word
    $result['Word'] = $tmp[0];
    //set tagger
    $result['POS'] = $tmp[1];
    //determine the word is stop word or not

    if(determine_stop_word($result['Word'],&$stop_word)){
      //if true then it's stop word & English word
      $result['Stop_word'] = "ST";
      $result['English'] = "EN";
      //echo $tmp[$i].'</br>';
    }
    else{
     
      $result['Stop_word'] = "NST";
      //check if a word is an English word 0:no, 1;yes
      if(check_word_english($result['Word']))
        $result['English'] = "EN";  //yes
      else
        $result['English'] = "NEN"; //no
    }
   
    //process slang & repeated letter
    if($result['English'] == "NEN"){

      $string = "(".$result['Word'].",".$result["POS"].",".$result['English'].",".$result['Stop_word'].")";
      echo $string.'</br>';

      $db_slang = new DB;
      $db_slang->connect_db($db_server, $db_user, $db_passwd , "slang_dictionary");
      process_slang(&$result,$db_slang);
      $string = "(".$result['Word'].",".$result["POS"].",".$result['English'].",".$result['Stop_word'].")";
      echo $string.'</br>';
      /**
      repeated letter
      **/
      //repeated_letter();

      /*
        call wiki api and try to define the meaning of non-English word
      */
      if($result['English'] == 'NEN'){

        $db_knowledge = new DB;
        $db_knowledge->connect_db($db_server, $db_user, $db_passwd , "knowledge");
        
        $prefix = $result['Word']." mobile phones"; //Verify that it is related to the mobile phone
        
        if(define_non_English_word($prefix)){
          //insert to the table
          $db_knowledge->query("INSERT INTO keyword (Word,Category) VALUES ('{$result['Word']}','mobile phone') ");
        }
        $prefix = $result['Word']." cameras"; //Verify that it is related to the camera
        if(define_non_English_word($prefix)){
          //insert to the table
          $db_knowledge->query("INSERT INTO keyword (Word,Category) VALUES ('{$result['Word']}','camera')");
        }  
      }
    

       
    }

    $string = "(".$result['Word'].",".$result["POS"].",".$result['English'].",".$result['Stop_word'].")";
    echo $string.'</br>';
  }




	echo "complete</br>";

?>