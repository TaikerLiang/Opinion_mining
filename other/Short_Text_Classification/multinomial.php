<?php
/**
 * multinomail
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * Naive base classifier, N = 1(unigorm) feature selction
 *
 * 9 Jan 2013
 */

      
    $path = '/Applications/MAMP/htdocs/Library/';
    set_include_path(get_include_path() . PATH_SEPARATOR . $path);

    require_once("Input/read_file.php");
    require_once("Database/mysql_connect.inc.php");
    require_once("Database/DB_class.php");
    require_once("../Extract_Opinion_Tweets/function_of_classification.php");

    require_once("function_of_classification.php");

    ini_set('memory_limit', '2048M');     //memory size
    set_time_limit(0);                    //time limit
     
    $log = '../Log/Naive bayes classifier/multinomial';
    $fwlog = openFileWrite($log);


    

    /**
      get content of data
    **/

    $category = array(0 => "mobilephone", 1  => "camera", 2  => "movie", 3 => "neutral");

    $db = new DB;
    $db->connect_db($db_server, $db_user, $db_passwd , "data");
    
    $data = array();

    for($i=0;$i<count($category);$i++){
    
      $class_name = $category[$i];
      $N = 0; //number of training data per class 
      $sql = "SELECT content FROM {$class_name}";
      $db->query($sql);
    
      while($str = $db->fetch_array()){ 
        //echo $str['content'].'</br>';
        $data[$class_name][$N++] = $str['content'];
      }
      //number of document in class
      $count[$class_name] = $N;
      //$number_of_document += $count[$class_name];
    }

    var_dump($count);
    echo '</br>';

    
    /**
      content of training & testing
    **/

    $category = array(0 => "mobilephone", 1  => "camera", 2  => "movie");
    $training = array();
    $testing = array();
    $number_of_document = array();


    for($i=0;$i<count($category);$i++){
      $class_name = $category[$i];
      //echo $class_name.'</br>';
    
      for($j=0;$j<count($data[$class_name]);$j++){
      
        if($j<count($data[$class_name]) * (9/10)){
          $training[$class_name][$j] = $data[$class_name][$j];
        }
        else{
          $n = (count($data[$class_name])-1) - $j;
          $testing[$class_name][$n] = $data[$class_name][$j];
        }
      }
        $number_of_document[$class_name] = count($training[$class_name]);
    }

    var_dump($number_of_document);
    echo '</br>';



/**
  algo for n-gram feature + naive base classifiler
**/

  $N = 1; //unigram

  $length = array();
  $number_of_term=0;
  $count = array();
  $vocabulary = array();

  for($i=0;$i<count($category);$i++){

    $class_name = $category[$i];
    echo $class_name.'</br>';
    $length[$class_name] = 0;
    $total_of_document += $number_of_document[$class_name];

    for($j=0;$j<count($training[$class_name]);$j++){
      echo $training[$class_name][$j].'</br>';
      $tmp = explode(" ", $training[$class_name][$j]);
      $c=0;
      $term = "";
      for($k=0;$k<count($tmp)-($N-1);$k++){
        
        if($tmp[$k] == "") continue;
        $word = string_process($tmp[$k]);
        
        if($word == 1){
          continue;
        }

        if($c<$N){
          $term = $term.' '.$word;
          $c++;
          continue;
        }

        echo $term.'</br>';

        $length[$class_name]++;
        //echo $term.'</br>';
        
        if($count[$term]!=NULL){
          $count[$term][$class_name]++;
        }
        else{
          //echo $term.'</br>';
          $vocabulary[$term] = 1;
          $count[$term][$class_name] = 1;
        }
        $c=0;
        $term = "";
      }
      //echo '</br>';
    }
  }
  var_dump($length);
  echo '</br>';
  //var_dump($count);
  //var_dump($vocabulary);
  //echo $total_of_document.'</br>';
  $number_of_term = count($vocabulary);
  echo count($vocabulary).'</br>';

  for($i=0;$i<count($category);$i++){
    $class_name = $category[$i];
    $prior[$class_name] = $number_of_document[$class_name] / $total_of_document;

    foreach ($vocabulary as $term => $value) {
      //echo $term.' ';
      if($count[$term][$class_name]==NULL)
        $condprob[$class_name][$term] = 1 / ($length[$class_name] + $number_of_term);
      else
        $condprob[$class_name][$term] = ($count[$term][$class_name] + 1) / ($length[$class_name] + $number_of_term); 
      //echo $condprob[$class_name][$term].'</br>';
    }

  }
  //var_dump($condprob);
  //var_dump($prior);
  //echo '</br>';
  /**
    alog for testing
  **/


  for($i=0;$i<count($category);$i++){
    $class_name = $category[$i];
    echo $class_name.'############</br>';
    echo count($testing[$class_name]).'</br>';
    $score = array();

    //$result = array(0 => "mobilephone", 1  => "camera", 2  => "movie");
    $result['mobilephone'] = 0; $result['camera'] = 0; $result['movie'] = 0;    

    for($j=0;$j<count($testing[$class_name]);$j++){
      //echo $testing[$class_name][$j].'</br>';
      
      $tmp = explode(" ", $testing[$class_name][$j]); 
      $c=0;
      $term = "";
      
      for($l=0;$l<count($category);$l++){
        $score[$category[$l]] = log($prior[$category[$l]]);
      }
     
      for($k=0;$k<count($tmp)-($N-1);$k++){
        if($tmp[$k] == "") continue;
        $word = string_process($tmp[$k]);
        
        if($word == 1) continue;

        if($c<$N){
          $term = $term.' '.$word;
          $c++;
          continue;
        }

        //echo $term.'</br>';
        for($l=0;$l<count($category);$l++){
          //echo log($condprob[$category[$l]][$term]).'</br>';
          if($condprob[$category[$l]][$term]!="")
            $score[$category[$l]] += log($condprob[$category[$l]][$term]);   
        }
            
        $c=0;
        $term = "";        
      }

      //var_dump($score);
      //echo '</br>';

      $tmp = doublemax($score);
      $result[$tmp['i']]++;
      
      unset($score);
    } 
    var_dump($result);
    echo '</br>';
  }


  writeLog("successful\n",$fwlog);
  echo "complelte</br>";



?>