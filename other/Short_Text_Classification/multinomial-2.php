  <?php

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

    $className = './class';
    $fd = openFileRead($className);

    //datatable 
    $i=0;
    while($str = fgets($fd)){
      $class[$i++] = trim($str);
    }

    
    $data = array();
    $count = array();

    /**
    content of data
    **/

    $db_data = new DB;
    $db_data->connect_db($db_server, $db_user, $db_passwd , "data");

    for($i=0;$i<count($class);$i++){
    
      $class_name = $class[$i];
      //echo $class_name.'</br></br></br>';
      $N = 0; //number of training data per class 
      $sql = "SELECT content FROM {$class_name}";
      $db_data->query($sql);
    
      while($str = $db_data->fetch_array()){ 
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


    $training = array();
    $testing = array();
    $number_of_document = 0;

  
    for($i=0;$i<count($class);$i++){
    
      $class_name = $class[$i];
      for($j=0;$j<$count[$class_name];$j++){
        if($j<$count[$class_name] * (9/10)){
          $training[$class_name][$j] = $data[$class_name][$j];
        }
        else{
          $n = ($count[$class_name]-1) - $j;
          $testing[$class_name][$n] = $data[$class_name][$j];
        }
      }

      $number_of_document += count($training[$class_name]);
    }

    echo $number_of_document.'</br>';
   
    /**
    vocabulary
    **/


    $number_of_vocabulary= 0;
    $length=array();
    $vocabulary = array();


    for($i=0;$i<count($class);$i++){  
      //bulid total vocabulary, bulid term vocabulary in each class, and calculate the length of text in each class
      bulid_vocabulary($class[$i],&$training,&$vocabulary,&$length,&$number_of_vocabulary);
    }
    
    //var_dump($vocabulary);



    /**
    alog for training
    **/


    $condprob = array();  //probability
    $prior = array();     //probability


    for($i=0;$i<count($class);$i++){

      $class_name = $class[$i];
      $prior[$class_name] = $count[$class_name] / $number_of_document;

      foreach($vocabulary as $word => $N){
        $condprob[$class_name][$word] = calculate_conprob($class_name,$word,$number_of_vocabulary,$length,&$vocabulary);
        //echo $word.': '.$condprob[$class_name][$word].'</br>';
      }
    }



    /**
    alog for testing
    **/

    for($i=0;$i<count($class);$i++){
      $class_name = $class[$i];
      $mobile=0;
      $camera=0;
      $movie=0;
    
      echo $class_name.': '.count($testing[$class_name]).'</br>';
      for($j=0;$j<count($testing[$class_name]);$j++){
        //echo $testing_array[$class_name][$j].'</br>';
        for($k=0;$k<count($class);$k++){
          $score[$class[$k]] = log($prior[$class[$k]]);
          $score[$class[$k]] += scoring($class[$k],$testing[$class_name][$j],&$condprob);  
          //echo $score[$k].'</br>'; 
        }

        $result = doublemax($score);
        //var_dump($result);

        //echo '</br>';

        if($result['i'] == 'mobilephone'){
          $mobile++;
        }
        else if($result['i'] == 'camera'){
          $camera++;
        }
        else if($result['i'] == 'movie'){
          $movie++;
        }
      }

      echo $mobile.'</br>';
      echo $camera.'</br>';
      echo $movie.'</br>';
    }
    //var_dump($condprob);



    echo "complete</br>";


?>