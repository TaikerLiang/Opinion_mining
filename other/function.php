<?php
	
	require_once("../Library/Input/read_file.php");

	function initialArray($array,$len,$initialValue){
		for($i=0;$i<$len;$i++){
			$array[$i] = $initialValue;
		}
	}

	function clearArray($array){
		for($i=0;$i<count($array);$i++){
			$array[$i] = 0;
		}
	}


?>