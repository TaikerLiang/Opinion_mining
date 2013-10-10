<?php

/**
 * w
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 *
 * 
 * 24 OCT 2012
 */


  $path = '/Applications/MAMP/htdocs/Library/';
  set_include_path(get_include_path() . PATH_SEPARATOR . $path);

  require_once("Input/read_file.php");
  require_once("Database/mysql_connect.inc.php");
  require_once("Database/DB_class.php");

  require_once("function_of_classification.php");

  ini_set('memory_limit', '2048M');     //memory size
  set_time_limit(0);                    //time limit
   
  $log = '../Log/Naive bayes classifier/bernoulli';
  $fwlog = openFileWrite($log);

  $className = '../class';
  $fd = openFileRead($className);

  //datatable 
  $i=0;
  while($str = fgets($fd)){
    $class[$i++] = trim($str);
  }

  $number_of_document = 0;
  $trainging_array = array();
  $count = array();
  $prior = array();     //probability
  $condprob = array();  //probability
  $vocabulary = array();

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
  //var_dump($trainging_array['mobilephone']);

  /**
  count the number of documents in class containing term
  **/
  for($i=0;$i<count($class);$i++){  

  	CountDocInClassContainingTerm($class[$i],&$trainging_array,&$vocabulary);
  
  }

  //var_dump($vocabulary);

  //var_dump($vocabulary['htc']);

  /**
  alog for training
  **/

  for($i=0;$i<count($class);$i++){

    $class_name = $class[$i];
    $prior[$class_name] = $count[$class_name] / $number_of_document;

    foreach($vocabulary as $word => $N){
    	//echo $word.'</br>';
      	$condprob[$class_name][$word] = calculate_conprob_bernoulli($class_name,$word,&$vocabulary,$number_of_document);
      	//echo $word.': '.$condprob[$class_name][$word].'</br>';
    }
  }


  /**
  content of testing
  **/

  unset($count);
  $testing_array = array();

  $db_testing = new DB;
  $db_testing->connect_db($db_server, $db_user, $db_passwd , "testing");
  
  for($i=0;$i<count($class);$i++){
    $class_name = $class[$i];
    $N = 0;
    $sql = "SELECT content FROM {$class_name}";
    $db_testing->query($sql);
   
    while($str = $db_testing->fetch_array()){
      $testing_array[$class_name][$N++] = $str['content'];
    }

  }

  /**
  alog for testing
  **/

  for($i=0;$i<count($class);$i++){
    $class_name = $class[$i];
    $mobile=0;
    $camera=0;
    //echo 
   
    echo $class_name.': '.count($testing_array[$class_name]).'</br>';

    for($j=0;$j<count($testing_array[$class_name]);$j++){
      //echo $testing_array[$class_name][$j].'</br>';
      for($k=0;$k<count($class);$k++){
      	//echo $class[$k].'</br>';
        $score[$k] = log($prior[$class[$k]]);
        $score[$k] += scoring_bernoulli($class[$k],$testing_array[$class_name][$j],&$condprob,&$vocabulary);  
        //echo $score[$k].'</br>'; 
      }

      //need to modify
      if($score[0] > $score[1]){
        $mobile++;
      }
      else{
        $camera++;
      }
    }

    echo "mobile: ".$mobile.'</br>';
    echo "camera: ".$camera.'</br>';
  }

  //var_dump($condprob);

  //var_dump($testing_array);

  /**
  analysis
  **/

  /*foreach ($vocabulary as $key => $value) {
    
    echo $key.' ';
    echo $condprob['camera'][$key].'</br>';

  }*/




  echo "complete</br>";

?>