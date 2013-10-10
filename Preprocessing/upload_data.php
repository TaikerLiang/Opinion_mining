<?php
/**
 * upload_data
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * upload tweets to database after preprocessing
 * 
 * 19 Dec 2012
 */



  	$path = '/Applications/MAMP/htdocs/Library/';
  	set_include_path(get_include_path() . PATH_SEPARATOR . $path);

	  require_once("Input/read_file.php");
	  require_once("Database/mysql_connect.inc.php");
  	require_once("Database/DB_class.php");
  	require_once("Database/knowledge_DB_class.php");
  	require_once("Database/rule_DB_class.php");
	
	  require_once("check_new_data.php");
	  require_once("preprocessing.php");


	  ini_set('memory_limit', '2048M');     //memory size
  	set_time_limit(0);                    //time limit



    //parameter
    $database = "testing";
    $table = "camera";

    $category = array(0 => "mobilephone", 1  => "camera", 2  => "movie");

    $directory_address = '../Data/'.$database.'/'.$table.'/';


    //connect to db to get old data information
  	$db = new DB;
   	$db->connect_db(DB_SERVER, DB_USER, DB_PWD , $database);

   	//fotmat: $newdata[class_name][number][id/content]
	  $newdata = checkNewData($db, $directory_address, $table);
	  //echo count($newdata['movie']);
	  //var_dump($newdata);

 	  //connect to db to get some information
  	$db_knowledge = new knowledge_DB(DB_SERVER, DB_USER, DB_PWD);
  	$emotions_dictionary = $db_knowledge->emotions();
    $stop_word = $db_knowledge->stop_word();
  	$mobile = $db_knowledge->special_word_mobile();
  	$camera = $db_knowledge->special_word_camera();

  	
  	$db_rule = new rule_DB(DB_SERVER, DB_USER, DB_PWD);
  	$rule = $db_rule->rule();

  	$db_slang = new DB;
    $db_slang->connect_db(DB_SERVER, DB_USER, DB_PWD , "slang_dictionary");


  	$newdata = preprocessing($emotions_dictionary,$rule,$stop_word,$newdata,$category,$db_slang,&$mobile,&$camera);
  	
  	$db_update = new DB;
   	$db_update->connect_db(DB_SERVER, DB_USER, DB_PWD , $database);


  	//var_dump($newdata);

  	for($i=0;$i<count($category);$i++){
  		$table = $category[$i];
  		for($j=0;$j<count($newdata[$table]);$j++){
  			$ID = $newdata[$table][$j]['ID'];
  			$original_tweet = $newdata[$table][$j]['original_tweet'];
  			$content = $newdata[$table][$j]['content'];
  			$emotions = $newdata[$table][$j]['emotions'];
  			$emotions_meaning = $newdata[$table][$j]['emotions_meaning'];
  			$tag = $newdata[$table][$j]['tag'];
        $opin = $newdata[$table][$j]['opin'];


  			$sql = "INSERT INTO $table(ID, original_tweet, content, emotions,emotion_meaning,tag, opinion) 
  					VALUES ('{$ID}','{$original_tweet}','{$content}','{$emotions}','{$emotions_meaning}','{$tag}','{$opin}')";
        
  			//echo $sql.'</br></br>';			
  			$db_update->query($sql);
  		}
  	}

    /*$sql = "INSERT INTO $table(ID, original_tweet, content, emotions,emotion_meaning,tag) 
          VALUES ('{$ID}','{$original_tweet}','{$content}','{$emotions}','{$emotions_meaning}','{$tag}')";*/
  	echo "upload_data complete</br>";




?>