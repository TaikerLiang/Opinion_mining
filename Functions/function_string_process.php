<?
/**
 * functions_string_process
 *
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * process string
 * 
 * 7 Feb 2013
 */

	/**
		string process
	**/
	

	function string_process($string){
		$string = str_replace("(","",$string);
		$string = str_replace(")","",$string);
		$string = str_replace("?"," ? ",$string);
	
		$tmp = explode(",", $string);

		/*if($tmp[3]=="ST" || trim($tmp[0])==NULL || $tmp[1]==":") 
			return 1;*/

		/*if($tmp[0] == "the" || $tmp[0] == "a" || trim($tmp[0])==NULL)
			return 1;*/
	
		$term = strtolower($tmp[0]);
		//echo $term.'</br>';
	
		return trim($term);
	}

	function string_process_svm($string){
		$string = str_replace("(","",$string);
		$string = str_replace(")","",$string);
		$string = str_replace("?"," ? ",$string);
	
		$tmp = explode(",", $string);
	
		$term = strtolower($tmp[0]);
		//echo $term.'</br>';
	
		return trim($term);
	}

	/**
		max
	**/
	
	function doublemax($mylist){
  
  		$maxvalue=max($mylist);
  		while(list($key,$value)=each($mylist)){
    		if($value==$maxvalue)$maxindex=$key;
  		}
  		
  		return array("m"=>$maxvalue,"i"=>$maxindex);
	}



?>