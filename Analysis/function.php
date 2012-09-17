<?php

require_once("../../Library/Input/read_file.php");
require_once('./TagClass.php');

function filterOutOfAdjective($tag){
		
		switch ($tag) {
   		 	case 'JJ':              //Adjective
        		return 1;
    	    case 'JJR':			    //Adjective, comparative
        	 	return 1;
    		case 'JJS':				//Adjective, superlative
        		return 1;
        	default:
        		return 0;
	   }
}

function search($ADJ , $i , $word){
	
	for($j=0; $j<$i; $j++){
		if ($ADJ[$j]->getWord() == $word){
			$ADJ[$j]->add();
			return 1;
		}
	}
	return 0;

}
?>