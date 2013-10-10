<?php
/**
 * multinomial_FS(2)
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * Naive base classifier, N = 1(unigorm) feature selction
 * x^2 feature selection method and select Top k feature
 *
 * 9 Jan 2013
 **/

      
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

    //var_dump($count);
    //echo '</br>';

    
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
        echo 'training data of '.$class_name.' : '.$number_of_document[$class_name].'</br>';

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
    $sentences = array();

    for($i=0;$i<count($category);$i++){

      $class_name = $category[$i];
      $length[$class_name] = 0;
      //echo $class_name.'</br>';
      
      $total_of_document += $number_of_document[$class_name];

      for($j=0;$j<count($training[$class_name]);$j++){
        //echo $training[$class_name][$j].'</br>';
        $tmp = explode(" ", $training[$class_name][$j]);
        $c=0;$term = "";
        $word_of_sentences = array();
        $not_stop_word = array();
        for($k=0;$k<count($tmp);$k++){
        
          if($tmp[$k] == "") continue;
          $word = string_process($tmp[$k]);
          if($word == 1) continue;
          
          $not_stop_word[$c++] = $word;
        }

       

        for($k=0;$k<count($not_stop_word)-($N-1);$k++){
          for($l=$k;$l<$k+$N;$l++){
            $term = $term.' '.$not_stop_word[$l];
          }

          

          if($count[$term]!=NULL){
            $count[$term][$class_name]++;
            $word_of_sentences[$term] = 1;
            $length[$class_name]++;
          }
          else{
            //echo $term.'</br>';
            $vocabulary[$term] = 1;
            $count[$term][$class_name] = 1;
            $word_of_sentences[$term] = 1;
            $length[$class_name]++;
          }

          //echo $term.'</br>';
          $term = "";
        }



        foreach ($word_of_sentences as $term => $value) {
          # code...
          //echo $term.'</br>';
          if($sentences[$term][$class_name]==NULL){
             $sentences[$term][$class_name] = 1;
          }
          else{
             $sentences[$term][$class_name] += $value;
          } 
        }
      }
    }
    
    //echo $total_of_document.'</br>';
    $total_of_term = count($vocabulary);
    echo count($vocabulary).'</br>';
    //var_dump($vocabulary);
    //echo '</br>';

    var_dump($length);
    echo '</br>';




    for($i=0;$i<count($category);$i++){
    $class_name = $category[$i];
    $prior[$class_name] = $number_of_document[$class_name] / $total_of_document;

      foreach ($vocabulary as $term => $value) {
        //echo $term.' ';
        if($count[$term][$class_name]==NULL)
          $condprob[$term][$class_name] = 1 / ($length[$class_name] + $total_of_term);
        else
          $condprob[$term][$class_name] = ($count[$term][$class_name] + 1) / ($length[$class_name] + $total_of_term); 
        //echo $condprob[$class_name][$term].'</br>';
      }
    }

    //var_dump($condprob);
  /**
    x^2 Feature selection
  **/

    $dependence = array();
    $feature_candidate = array();

    foreach ($vocabulary as $key => $value) {
   
      $term = $key;
      $number_of_sentences = 0;
      for($j=0;$j<count($category);$j++){
        $class_name = $category[$j];
        if($count[$term][$class_name]=="")
          $count[$term][$class_name] = 0;
        if($sentences[$term][$class_name]=="")
          $sentences[$term][$class_name] = 0;
          //echo $count[$term][$class_name]." ";
          $number_of_sentences += $sentences[$term][$class_name];
      }
        
      //echo $term.' FFFF</br>';
      for($j=0;$j<count($category);$j++){
        //N11:number the co-occurrence of the feature F and the class C
        //N10:number of documents contains the feature F but is not in C
        //N01:number of sentences in class C but doesn't contain F
        //N00:number of sentences not in C and doesn't contain F
        /*數字爆掉了 所以把它個除100做normalize*/
        $class_name = $category[$j];
        $N11 = $sentences[$term][$class_name];
        $N10 = ($number_of_sentences - $sentences[$term][$class_name]);
        $N01 = ($number_of_document[$class_name] - $sentences[$term][$class_name]);
        $N00 = ($total_of_document - $number_of_document[$class_name] - $N10);

        //formula
        $molecular = 0; $denominator =0; $tmp=0;
        $tmp = ($N11*$N00 - $N10*$N01);
        $tmp = $tmp * $tmp;
        $molecular = $total_of_document * $tmp  /10000;
        $denominator = ($N11+$N01)*($N11+$N10)*($N10+$N00)*($N01+$N00) /10000;
        
        $dependence[$term][$class_name] = $molecular/$denominator;

        //echo $class_name.': ';
        //echo $dependence[$term][$class_name].'</br>';

      }

      $result = doublemax($dependence[$term]);
      $feature_candidate[$term] = $result['m'];
      //var_dump($result);
      //echo '</br>';
      unset($dependence);    
    }


    //var_dump($keyword_candidate);
    arsort($feature_candidate);

    $k = 100; //top k feature will be selected
    echo "Top K : ".$k.'</br>';

    $c =0;
    $feature = array();

    foreach ($feature_candidate as $term => $value) {
      # code...
      //echo $term.' '.$value.'</br>';
      //$feature[$term] = $value;
      $c++;
      if($c==$k) break;
    }


  /**
    alog for testing
  **/

  $molecular = 0; //分子
  $denominator = 0; //分母

  for($i=0;$i<count($category);$i++){
    $class_name = $category[$i];
    echo $class_name.'</br>';
    echo count($testing[$class_name]).'</br>';
    $denominator += count($testing[$class_name]);
    $score = array();

    unset($result);
    $result =array();
    //$result = array(0 => "mobilephone", 1  => "camera", 2  => "movie");
    $result['mobilephone'] = 0; $result['camera'] = 0; $result['movie'] = 0;    

    for($j=0;$j<count($testing[$class_name]);$j++){
      echo $testing[$class_name][$j].'</br>';
      
      $tmp = explode(" ", $testing[$class_name][$j]); 
      $c=0; //for not_stop_word
      $term = "";
      
      for($l=0;$l<count($category);$l++){
        $score[$category[$l]] = log($prior[$category[$l]]);
      }
     
        
      for($k=0;$k<count($tmp);$k++){
        
        if($tmp[$k] == "") continue;
        $word = string_process($tmp[$k]);
        if($word == 1) continue;
          
        $not_stop_word[$c++] = $word;
      }

      for($k=0;$k<count($not_stop_word)-($N-1);$k++){
          
        for($l=$k;$l<$k+$N;$l++){
          $term = $term.' '.$not_stop_word[$l];
        }


        echo $term.'</br>';
        for($l=0;$l<count($category);$l++){
          //echo log($condprob[$term][$category[$l]]).'</br>';
          if($condprob[$term][$category[$l]]!="" /*&& $feature[$term]!=NULL*/)
            $score[$category[$l]] += log($condprob[$term][$category[$l]]);   
        }

        //echo $term.'</br>';
        $term = "";
      }


      //var_dump($score);
      //echo '</br>';

      $tmp = doublemax($score);
      $result[$tmp['i']]++;
      unset($score);
    } 
    var_dump($result);
    echo '</br></br>';
    $molecular += $result[$class_name];
  }
    


  echo "accuracy: ".($molecular/$denominator).'</br>';
  echo "-------short text classfication complelte--------</br>";



?>