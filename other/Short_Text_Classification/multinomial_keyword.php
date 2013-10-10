<?php

/**
 * multinimial
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
   
  $log = '../Log/Naive bayes classifier/multinomial';
  $fwlog = openFileWrite($log);

  $filename = '../class';
  $fd = openFileRead($filename);

  //datatable 
  $i=0;
  while($str = fgets($fd)){
    $class[$i++] = trim($str);
  }
    
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
  $difference = array(); 
  $word_category = array();
  //$training_socre = array();
 


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
  vocabulary
  **/
  for($i=0;$i<count($class);$i++){  
    //bulid total vocabulary, bulid term vocabulary in each class, and calculate the length of text in each class
    bulid_vocabulary($class[$i],&$trainging_array,&$vocabulary,&$length,&$number_of_vocabulary);
  }
 
  
  /**
  save vocabulary
  **/

  $filenameout = '/Applications/MAMP/htdocs/Twitter/Data/lexicon.txt';
  $fw = openFileWrite($filenameout);

  foreach ($vocabulary as $key => $v) {
    //echo trim($key).'</br>';
    $key = trim($key);
    fwrite($fw, $key."\n");
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



  //var_dump($condprob);
  /**
  build the keyword list
  **/

  //return all index in array
  $keys = array_keys($vocabulary);
  //var_dump($keys);

  foreach ($keys as $word) {
    if(strlen($word) <= 1) continue;
    
    //echo $word.'</br>';
    for($j=0;$j<count($class);$j++){
      $class_name = $class[$j];
      //echo $condprob[$class_name][trim($word)].' ';
      $value[$j] = log($condprob[$class_name][$word]);
     
    }

    $tmp = doublemax($value);

    //var_dump($tmp);
    $word_category[$word] = $class[$tmp['i']];
    $difference[$word] = degree_of_difference($value);
    //var_dump($value);
    //echo '</br>';
      
    unset($value);
  }

  
  $top = 50;
  $threshold;
  $camera_list = array();
  $mobile_list = array();
  $movie_list = array();



  //var_dump($word_category);
  //var_dump($difference);
  arsort($difference);


  $i=0;
  foreach ($difference as $word => $v) {

    if($word_category[$word] == 'mobilephone'){
      $j = count($mobile_list);
      $mobile_list[$j] = $word;
    }
    else if($word_category[$word] == 'camera'){
      $j = count($camera_list);
      $camera_list[$j] = $word;
    }
    else if($word_category[$word] == 'movie'){
      $j = count($movie_list);
      $movie_list[$j] = $word;
    }
    $i++;

    if(count($mobile_list) >= $top && count($camera_list) >= $top && count($movie_list) >= $top){

      //echo $word.' '.$difference[$word].'</br>'; 
      $threshold = $difference[$word];
      break;
    }
    
    //echo $word.' '.$difference[$word].'</br>'; 
    //echo $word.' '.$word_category[$word].' '.$v.'</br>';

  }

  for($i=0;$i<20;$i++)
    echo $mobile_list[$i].'</br>';
  echo '-------</br>';
  for($i=0;$i<21;$i++)
    echo $camera_list[$i].'</br>';
  echo '-------</br>';
  for($i=0;$i<20;$i++)
    echo $movie_list[$i].'</br>';
  /*var_dump($mobile_list);
  echo '</br>';*/
  /*var_dump($camera_list);
  echo '</br>';
  var_dump($movie_list);
  echo '</br>';*/

  /**
  content of testing
  **/

  $class_testing = array();

  $filename_testing = '../class_testing';
  $fd = openFileRead($filename_testing);

  //datatable 
  $i=0;
  while($str = fgets($fd)){
    $class_testing[$i++] = trim($str);
  }

  //var_dump($class_testing);


  unset($count);
  $testing_array = array();

  $db_testing = new DB;
  $db_testing->connect_db($db_server, $db_user, $db_passwd , "testing");
  
  for($i=0;$i<count($class_testing);$i++){
    //$class_name = 'other';
    $class_name = $class_testing[$i];
    $N = 0;
    $sql = "SELECT content FROM {$class_name}";
    $db_testing->query($sql);
   
    while($str = $db_testing->fetch_array()){
      $testing_array[$class_name][$N++] = $str['content'];
    }

  }
  
  //var_dump($testing_array['camera']);

  /**
  alog for testing
  **/



  for($i=0;$i<count($class_testing);$i++){
    $correct = 0;
    $wrong = 0;
    $class_name = $class_testing[$i];
    echo $class_name.': '.count($testing_array[$class_name]).'</br>';
    for($j=0;$j<count($testing_array[$class_name]);$j++){
      //echo $testing_array[$class_name][$j].'</br></br>';
      $score = detetmine_category($testing_array[$class_name][$j],&$difference,&$word_category,$threshold,&$class_testing);

      //var_dump($score);
      //echo '</br>';

      $result = doublemax($score);
      //var_dump($result);
     
      if($result['m'] == 0){
        //echo $class_name.'</br>';
        //echo $testing_array[$class_name][$j].'</br></br>';
        if($class_name == 'other')
          $correct++;
        else
          $wrong++;
      }
      else{
        //echo $result['i'].'</br>';
        if($result['i'] == $class_name)
          $correct++;
        else
          $wrong++;
      }
      unset($score);
    }

    echo 'correct '.$correct.'</br>';
    echo 'wrong '.$wrong.'</br>';
  }


  /*for($i=0;$i<count($class_testing);$i++){
    $correct = 0;
    $wrong = 0;
    $class_name = $class_testing[$i];
    echo $class_name.': '.count($testing_array[$class_name]).'</br>';
    for($j=0;$j<count($testing_array[$class_name]);$j++){
      //echo $testing_array[$class_name][$j].'</br>';
      for($k=0;$k<count($class);$k++){
        //echo $class[$k].' ';
        $score[$class[$k]] = log($prior[$class[$k]]);
        $score[$class[$k]] += scoring_Standard_Deviation($class[$k],$testing_array[$class_name][$j],&$condprob);
        //echo $score[$class[$k]].'</br>'; 
      }
     
    }

  }*/

  //var_dump($condprob);

  /**
  analysis
  **/

  /*foreach ($vocabulary as $key => $value) {
    
    echo $key.' ';
    echo $condprob['mobilephone'][$key].'</br>';

  }*/



 


  echo "complete</br>";

?>