<?php

	$dirname = "../Tagger/Tweet/";
	$dirlist = scandir ($dirname);
	
	
	
	$filename =  "./FileName.txt";
	 $fw = fopen ($filename, "w");
	
	foreach($dirlist as $key => $N){
	    if($key>2){
		
			echo $dirlist[$key];
			echo '</br>';
			
			fwrite($fw,$dirlist[$key]."\n");
			
		}
	}
	
	
?>