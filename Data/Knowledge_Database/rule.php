<?php

/**
 * rule
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * build the rule for repeated letter
 * 
 * 23 OCT 2012
 */
  
	
  $path = '/Applications/MAMP/htdocs/Library/';
  set_include_path(get_include_path() . PATH_SEPARATOR . $path);

  require_once("Input/read_file.php");
  require_once("Database/mysql_connect.inc.php");
  require_once("Database/DB_class.php");


  $fileName = './rule.txt';
  $fd = openFileRead($fileName);
  $log = '../Log/Knowledge_Database/rule';
  $fwlog = openFileWrite($log);

  $db = new DB;
  $db->connect_db($db_server, $db_user, $db_passwd , "rule");
  $ID = 1;
  while($str = fgets($fd)){
  	$tmp = explode("-", $str);
  	$content = $tmp[0];
  	$letter_order = $tmp[1];
  	$meaning = $tmp[2];
  	/*need to modify*/
  	$sql = "INSERT INTO repeated_letter(ID, content, letter_order, meaning) VALUES ('{$ID}','{$content}','{$letter_order}','{$meaning}')";
	$db->query($sql);
	$ID++;
  }

  echo "complete</br>";

  ?>