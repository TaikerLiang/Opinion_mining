<?php

/**
 * multinimial 
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * bayes claasifiler
 * use to extract text contains opinion
 * 
 * 9 Nov 2012
 */


  $path = '/Applications/MAMP/htdocs/Library/';
  set_include_path(get_include_path() . PATH_SEPARATOR . $path);

  require_once("Input/read_file.php");
  require_once("Database/mysql_connect.inc.php");
  require_once("Database/DB_class.php");

  require_once("./function_of_classification.php");

  ini_set('memory_limit', '2048M');     //memory size
  set_time_limit(0);                    //time limit
   
  /*$log = '../Log//multinomial';
  $fwlog = openFileWrite($log);*/


  $class[0] = "opinion";
  $class[1] = "non_opinion";

  $new_data_type = array(0 => "mobilephone", 1  => "camera", 2  => "movie");

   
  //declaration
  $number_of_vocabulary= 0;
  $number_of_document = 0;
  $trainging_array = array();
  $vocabulary = array();
  //$term_in_class = array();
  $length = array();
  $count = array();
  $prior = array();     //probability
  $condprob = array();  //probability
  $score = array();



  /**
  content of trainging
  **/
 

  $db_trainging = new DB;
  $db_trainging->connect_db($db_server, $db_user, $db_passwd , "training");

  for($i=0;$i<count($class);$i++){
    
    $class_name = $class[$i];
    $N = 0; //number of training data per class 
    $sql = "SELECT content FROM {$class_name}";
    $db_trainging->query($sql);
    
    while($str = $db_trainging->fetch_array()){ 
      $trainging_array[$class_name][$N++] = $str['content'];
    }
    //number of document in class
    $count[$class_name] = $N;
    $number_of_document += $count[$class_name];
  }

  //var_dump($count);
  
  /**
  vocabulary
  **/
  for($i=0;$i<count($class);$i++){  
    //bulid total vocabulary, bulid term vocabulary in each class, and calculate the length of text in each class
    bulid_vocabulary($class[$i],&$trainging_array,&$vocabulary,&$length,&$number_of_vocabulary);
  }


  /**
  alog for training
  **/

  for($i=0;$i<count($class);$i++){

    $class_name = $class[$i];
    $prior[$class_name] = $count[$class_name] / $number_of_document;

    foreach($vocabulary as $word => $N){
      $condprob[$class_name][$word] = calculate_conprob($class_name,$word,$number_of_vocabulary,$length,&$vocabulary);
      //echo $word.': '.$condprob[$class_name][$word].'</br>';
    }
  }



  /**
  content of new data
  **/
  unset($count);
  $new_data = array();

  $db_data = new DB;
  $db_data->connect_db($db_server, $db_user, $db_passwd , "data");
  

  for($i=0;$i<count($new_data_type);$i++){
    $type = $new_data_type[$i];
    $N = 0;
    $sql = "SELECT original_tweet,content FROM {$type}";
    $db_data->query($sql);
   
    while($str = $db_data->fetch_array()){
      $new_data[$type][$N]['original_tweet'] = $str['original_tweet'];
      $new_data[$type][$N]['content'] = $str['content'];
      $N++;
    }
  }


  /**
  alog for classification(opinion/non opinion)
  **/
  $opin = array();
  
  for($i=0;$i<count($new_data_type);$i++){
    $type = $new_data_type[$i];
    $count_opinion=0;
    echo $type.': '.count($new_data[$type]).'</br>';

    $fileNameOut = './opinion_'.$type.'.txt';
    $fw = openFileWrite($fileNameOut);

    echo $fileNameOut.'</br>';

    for($j=0;$j<count($new_data[$type]);$j++){
      for($k=0;$k<count($class);$k++){
        $score[$k] = log($prior[$class[$k]]);
        $score[$k] += scoring($class[$k],$new_data[$type][$j]['content'],&$condprob);  
      }

      //var_dump($score);
      //echo '</br></br>';
      if($score[0] >= $score[1]){
        //echo $new_data[$type][$j]['original_tweet'].'</br></br>';
        //$opin[$type][$count_opinion++] = $new_data[$type][$j]['original_tweet'];
        fwrite($fw, $new_data[$type][$j]['original_tweet']."\n");

      }
    }

    //var_dump($opin[$type]);
    //echo '</br></br>';
  }
  echo "complete</br>";

?>