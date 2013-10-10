<?php

	ini_set('include_path','.:/Applications/MAMP/htdocs/Library/');

	require_once("Input/read_file.php");
	require_once("Database/mysql_connect.inc.php");
    require_once("Database/DB_class.php");

    ini_set('memory_limit', '1024M');     //memory size
  	set_time_limit(0);                    //time limit


  	$fileName = './Fuck.txt';
  	$fw = openFileRead($fileName);

  	$fileNameOut = './Hello.txt';
  	$fwout = openFileWritePlus($fileNameOut);


  	while($str = fgets($fw)){

  		fwrite($fwout,$str);
  	} 

  	echo "complete</br>";

?>