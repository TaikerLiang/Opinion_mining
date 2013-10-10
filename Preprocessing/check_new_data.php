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

function checkNewData($db, $dirName, $table){

  	//training
  	$dirList = openDirectory($dirName);
	$url = 0; //contain url
	
	$sql = "SELECT COUNT(*) FROM {$table}";
	$db->query($sql);
	$str = $db->fetch_array();
	$total = $str['COUNT(*)'];
	$text = array();
	$ID = 0;
	$count=0;
	
    foreach($dirList as $key => $N){
		if($key > 2){

			$fileName = $dirName.$dirList[$key];
			//echo $fileName.'</br>';
   			$fd = openFileRead($fileName);
			if(!$fd) die('can not open the file');

			$name = $dirList[$key];
			$name = str_replace(".txt", "", $name);
			echo $name.'</br>';
			
			
			while($str = fgets($fd)){
				//echo $str.'</br>';
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
						if($text[$table][trim($str)]!=NULL)
							continue;
						else{
							$ID++;
							$text[$table][trim($str)] = $ID;	
							//echo $ID.'</br>';	
						}
					}
					else
						$url = 0;
				}
			}

			//var_dump($text);
			echo count($text[$table]).'</br>';	
			asort($text[$table]);
			foreach ($text[$table] as $key => $value) {

				if($value<=$total) continue;

				//echo $value.' '.$key.'</br></br>';
				$newdata[$table][$count]['original_tweet'] = $key;
				$newdata[$table][$count]['ID'] = $value;
				$newdata[$table][$count]['opin'] = $name; 

				$count++;
				//$sql = "INSERT INTO $name(ID, original_tweet) VALUES ('{$ID}','{$key}')";
				//$db->query($sql);
			}
			unset($text);
		}//if			
	}//foreach

	echo "checkNewData complete</br>";

	//var_dump($newdata);

	return $newdata;
}
?>