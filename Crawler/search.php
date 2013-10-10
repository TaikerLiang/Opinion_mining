<?php

/**
 * search
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * crawler data of products from twitter
 * 
 * 15 Aug 2012
 */

	$path = '/Applications/MAMP/htdocs/Library/';
 	set_include_path(get_include_path() . PATH_SEPARATOR . $path);

 	require_once("Input/read_file.php");
	
	require_once('./TwitterSearch.php');

	ini_set('memory_limit', '2048M');     //memory size
  	set_time_limit(0);                    //time limit


  	$log = '../Log/Crawler/search';
	$fwlog = openFileWrite($log);

 	$fileName = './product';
 	$fd = openFileRead($fileName);
 	if(!$fd) die('can not open the file'); 		

 	while ($str = fgets($fd)) {
 		
 		echo $str.'</br>';
 		$target = trim($str);

 		$fileNameOut = './New/'.$target;
 		$fw = openFileWrite($fileNameOut);
 		
 		for($j=1;$j<3;$j++){
			$search = new TwitterSearch($target);
			$search -> contains(":)");
			$search -> contains("");
			$search -> lang("en");
			$search -> page($j);
			$search -> rpp(10);
    		$results = $search->results();
    	

    		for($i=0;$i<count($results);$i++){
    			//echo $results[$i]->text.'</br>';
    			$string = $results[$i]->text."\n";
    			fwrite($fw, $string);
   			}
    	}

    	echo "----------------------------</br>";

		for($j=1;$j<3;$j++){
			$search = new TwitterSearch($target);
			$search -> contains(":(");
			$search -> contains("");
			$search -> lang("en");
			$search -> page($j);
			$search -> rpp(10);
    		$results = $search->results();
    	

    		for($i=0;$i<count($results);$i++){
    			//echo $results[$i]->text.'</br>';
    			$string = $results[$i]->text."\n";
    			fwrite($fw, $string);
   			}
    	}

 		writeLog($target." successful\n",$fwlog);
 	}

	

  
	echo "complete</br>";

?>