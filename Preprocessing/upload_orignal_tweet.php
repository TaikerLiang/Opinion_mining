<?php

/**
 * upload_orignal_tweet
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * upload tweets to database(trainging)
 * if tweet's length < 5 & tweet contain URL then discard it.
 * 
 * 15 Aug 2012
 */

function checkNewData(){
	
	ini_set('include_path','.:/Applications/MAMP/htdocs/Library/');

	require_once("Input/read_file.php");
	require_once("Database/mysql_connect.inc.php");
    require_once("Database/DB_class.php");

    ini_set('memory_limit', '1024M');     //memory size
  	set_time_limit(0);                    //time limit

  	//training
  	$dirName = './Data/Training/opinion/';
  	$dirList = openDirectory($dirName);
  	
  	$db = new DB;
   	$db->connect_db($db_server, $db_user, $db_passwd , "data");
	
	$url = 0; //contain url
	
	//var_dump($dirList);
	
    foreach($dirList as $key => $N){
		if($key > 1){

			$fileName = $dirName.$dirList[$key];
   			$fd = openFileRead($fileName);
			if(!$fd) die('can not open the file');
			
			$name = $dirList[$key];
			//echo $name.'</br>';
			
			$sql = "SELECT COUNT(*) FROM {$name}";
			$db->query($sql);
			$str = $db->fetch_array();
			$total = $str['COUNT(*)'];
			$text = array();
			$ID = 0;

			while($str = fgets($fd)){
				$tmp = explode(" ", $str);
				//If number of words in tweet < 5,then discard it.
				if(count($tmp) <= 5)
					continue;
				else{		
					for($i=0;$i<count($tmp);$i++){
						if(substr($tmp[$i],0,4)=="http"){
								$url = 1;
								break;
								//$str = str_replace($tmp[$i],"URL",$str); 
						}
					}
					if($url == 0){
						$str = str_replace("&amp;","",$str);
    					$str = str_replace("&lt;3","",$str);
						$str = str_replace("'","''", $str);
						$str = trim($str);
						

						//Confirm whether there are repeated
						if($text[$name][trim($str)]!=NULL)
							continue;
						else{
							$ID++;
							$text[$name][trim($str)] = $ID;	
							//echo $ID.'</br>';	
						}
					}
					else
						$url = 0;
				}
			}
			echo count($text[$name]).'</br>';	
			asort($text[$name]);
			$count=1;
			$i=0;
			foreach ($text[$name] as $key => $value) {

				if($value<$total) continue;

				//echo $value.' '.$key.'</br></br>';
				$newdata[$name][$i]['content'] = $key;
				$newdata[$name][$i]['ID'] = $value;
				$i++;
				//$sql = "INSERT INTO $name(ID, original_tweet) VALUES ('{$ID}','{$key}')";
				//$db->query($sql);
			}
			unset($text);
		}//if			
	}//foreach

	echo "checkNewData complete</br>";

	var_dump($newdata);

	return 0;
}
?>