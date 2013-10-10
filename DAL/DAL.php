<?php
/**
 * DAL
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 *
 * 
 * 25 Jun 2013
 */
	
	//header('Content-type: application/xml');

 	$path = '/Applications/MAMP/htdocs/Library/';
	set_include_path(get_include_path() . PATH_SEPARATOR . $path);

  	require_once("Input/read_file.php");
  	require_once("Database/mysql_connect.inc.php");
  	require_once("Database/DB_class.php");

	$url = "http://compling.org/cgi-bin/DAL_sentence_xml.cgi?sentence=sad";
	
	

		
		function download_page($path){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$path);
			curl_setopt($ch, CURLOPT_FAILONERROR,1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 15);
			$retValue = curl_exec($ch);			 
			curl_close($ch);
			return $retValue;
		}

		$sXML = download_page($url);
		$response = file_get_contents($url);

		$words = explode(" ", $response);

		for($i=0;$i<count($words);$i++){
			//echo $words[$i].'</br>';
		}
		//echo $sXML;

		$tmp = explode(" ", $sXML);
		for($i=0;$i<count($tmp);$i++){
			$tmp[$i] = str_replace('type="DAL"', "", $tmp[$i]);
			$tmp[$i] = str_replace("/>", "", $tmp[$i]);		
			echo $tmp[$i].'</br>';
		}
		//echo count($tmp);
		//$oXML = new SimpleXMLElement($sXML);

		//foreach($oXML->entry as $oEntry){
			//echo $oEntry->title . "\n";
		//}

	
?>