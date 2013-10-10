<?php
/**
 * term_document_matrix
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * create a matrix of document & term relation
 * row:documents, colum:adjectivs.
 * 
 * 12 June 2012
 */

	ini_set('include_path','.:/Applications/MAMP/htdocs/Library/');

	require_once("Input/read_file.php");
	require_once("Database/mysql_connect.inc.php");
    require_once("Database/DB_class.php");

    ini_set('memory_limit', '1024M');     //memory size
  	set_time_limit(0);                    //time limit

	$log = '/Applications/MAMP/htdocs/Twitter/Log/Latent_semantic_analysis/term_document_matrix';
	$fwlog = openFileWrite($log);


	$fileName = '/Applications/MAMP/htdocs/Twitter/Data/lexicon.txt';
	$fd = openFileRead($fileName);
	if(!$fd) die('can not open the file');
	$order = 0;
	$adjective = array();
	while($str = fgets($fd)){
		$lexicon[trim($str)] = $order;
		$order++;
		//echo $str.'</br>';
	}

	writeLog("load lexicon",$fwlog);

  	$filenameout = '/Applications/MAMP/htdocs/Twitter/Data/matrix.txt';
  	$fw = openFileWrite($filenameout);

  	$matrix = array();
  	$order = 0;           //documents

  	/**
  	content of training data
  	**/
  	//set class
  	//$class = "mobilephone";
  	//$class = "camera";
  	//$class = "opinion";
  	$class = "non_opinion";

  	//set training or testing
  	//$target = "training";
  	$target = "testing";


  $count = 0;
  $db_trainging = new DB;
  $db_trainging->connect_db($db_server, $db_user, $db_passwd , $target);
  $db_trainging->query("SELECT original_tweet FROM {$class}");
  while($str = $db_trainging->fetch_array()){
    $original_tweet[$count++] = $str['original_tweet'];
  }




  	
	echo "complete</br>";
?>