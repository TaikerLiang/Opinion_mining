<?

/*function string_process($string){
	$string = str_replace("(","",$string);
	$string = str_replace(")","",$string);
	$string = str_replace("?","",$string);
	
	$tmp = explode(",", $string);
	
	if($tmp[3]=="ST" || trim($tmp[0])==NULL) 
		return 1;

	$term = strtolower($tmp[0]);
	//echo $term.'</br>';
	
	return trim($term);
}*/

//$class_name:string. $training,$vocabulary,$term_in_class,$length:array.
/*function bulid_vocabulary($class_name,$training,$vocabulary,$length,$number_of_vocabulary){
	
	$len = 0;
	for($i=0;$i<count($training[$class_name]);$i++){

		$tmp = explode(" ", trim($training[$class_name][$i]));
		for($j=0;$j<count($tmp);$j++){
			$term = string_process($tmp[$j]);
			if($term == 1) continue;
			//something wrong
			if($vocabulary[$term][$class_name] == ""){
				$vocabulary[$term][$class_name] = 1;
				$number_of_vocabulary++;
			}
			else{
				$vocabulary[$term][$class_name]++;
			}
			$len++;
		}
	}
	$length[$class_name] = $len;
}*/

/*function calculate_probability($term_in_class,$condprob,$lernth_of_total_text_in_class,$count_of_vocabulary){

	//echo $lernth_of_total_text_in_class.'</br>';
	//echo $count_of_vocabulary.'</br>';

	foreach($term_in_class as $term => $N){

		$condprob[$term] = $N / ($lernth_of_total_text_in_class+$count_of_vocabulary);
		//echo $term." ".$condprob[$term].'</br>';
	}

}*/
/*function scoring($class_name,$text,$condprob){

	$score = 0;
	
	//var_dump($condprob);
	//echo $array[$i].'</br>';
	$tmp = explode(" ", trim($text));
	
	for($j=0;$j<count($tmp);$j++){
		$term = string_process($tmp[$j]);
		if($term == 1) continue;
		//echo $term.'</br>';
		if($condprob[$class_name][$term] != "")
			$score += log($condprob[$class_name][$term]);
	    	
	}
	//echo $condprob[$class]['t2i'].'**'.'</br>';
	//echo $score.'</br>';
	return $score;
}*/
//$class_name,$word:string. $number_of_vocabulary:integer. $length,&$vocabulary:array.
/*function calculate_conprob($class_name,$word,$number_of_vocabulary,$length,&$vocabulary){

	//echo $word." ".$vocabulary[$word][$class_name].'</br>';
	
	$p = 0; //probability

	if($vocabulary[$word][$class_name]==NULL)
		$p = 1 / ($length[$class_name] + $number_of_vocabulary);
	else
		$p = ($vocabulary[$word][$class_name] + 1) / ($length[$class_name] + $number_of_vocabulary);
	return $p;
}*/
//
/*function CountDocInClassContainingTerm($class_name,$training,$vocabulary){

	for($i=0;$i<count($training[$class_name]);$i++){
		$occurrence = array();
		
		$tmp = explode(" ", trim($training[$class_name][$i]));
		for($j=0;$j<count($tmp);$j++){

			$term = string_process($tmp[$j]);
			if($term == 1) continue;
			//echo $term.'</br>';
			$occurrence[$term] = 1;
			//do it
		}


		//var_dump($occurrence);
		foreach ($occurrence as $key => $value) {
			# code...
			if($vocabulary[$key][$class_name] == ""){
				$vocabulary[$key][$class_name] = 1;
			}
			else{
				$vocabulary[$key][$class_name]++;
			}
		}
		//var_dump($vocabulary);
		//echo '</br></br></br>';
		unset($occurrence);	
	}
}*/
function degree_of_difference($value){

	$diff_value = 0;
	for($i=0;$i<count($value);$i++){
		for($j=$i+1;$j<count($value);$j++){
			$diff = $value[$i] - $value[$j];
			$diff_square = $diff * $diff;
			$diff_value += $diff_square;
		}
	}
	//echo $diff_value.'</br>';
	return $diff_value;
}

/*function doublemax($mylist){
  
  $maxvalue=max($mylist);
  while(list($key,$value)=each($mylist)){
    if($value==$maxvalue)$maxindex=$key;
  }
  return array("m"=>$maxvalue,"i"=>$maxindex);
}*/

function detetmine_category($string,&$difference,&$word_category,$threshold,$class_testing){

	$score = array();


	for($i=0;$i<count($class_testing);$i++){
		//echo $class_testing[$i].'</br>';
		$score[$class_testing[$i]] = 0;
	}

	//echo $string.'</br>';
	$tmp = explode(" ", trim($string));
	
	for($j=0;$j<count($tmp);$j++){
		$word = string_process($tmp[$j]);
		if($word == 1) continue;
		
		if($difference[$word] < $threshold) continue;

		//echo $word.' '.$difference[$word].'</br>';

		$score[$word_category[$word]] += $difference[$word];
	}

	return $score;
}

?>