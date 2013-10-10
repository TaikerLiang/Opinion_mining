<?

function string_process($string){
	$string = str_replace("(","",$string);
	$string = str_replace(")","",$string);
	$string = str_replace("?","",$string);
	
	$tmp = explode(",", $string);
	if($tmp[3]=="ST" || trim($tmp[0])==NULL || $tmp[1]==":") 
		return 1;
	//if($tmp[1]=="NP" || $tmp[1]=="NN" || $tmp[1]=="NNS" || $tmp[1]=="PP" ||$tmp[1]=="CD" || $tmp[1]=="SYM")
		//return 1;
	//if($tmp[1]=="IN" || $tmp[1]=="PP$")
		//return 1;

	$term = strtolower($tmp[0]);
	//echo $term." ".$tmp[1].'</br>';
	
	return trim($term);
}

//$class_name:string. $training,$vocabulary,$term_in_class,$length:array.
function bulid_vocabulary($class_name,$training,$vocabulary,$length,$number_of_vocabulary){
	
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
}

function calculate_probability($term_in_class,$condprob,$lernth_of_total_text_in_class,$count_of_vocabulary){

	/*echo $lernth_of_total_text_in_class.'</br>';
	echo $count_of_vocabulary.'</br>';*/

	foreach($term_in_class as $term => $N){

		$condprob[$term] = $N / ($lernth_of_total_text_in_class+$count_of_vocabulary);
		//echo $term." ".$condprob[$term].'</br>';
	}

}
function scoring($class_name,$text,$condprob,$feature){

	$score = 0;
	//echo "fuck";
	//var_dump($condprob);
	//echo $array[$i].'</br>';
	$tmp = explode(" ", trim($text));

	
	for($j=0;$j<count($tmp);$j++){
		$term = string_process($tmp[$j]);
		if($term == 1) continue;
		//echo $term.'</br>';
		$term = trim($term);
		if($condprob[trim($term)][$class_name] != "" && $feature[$term]!=NULL)
			$score += log($condprob[trim($term)][$class_name]);
		//echo $condprob[$class_name]['i'];
		//echo $score.'</br>';
	    	
	}
	//echo $condprob[$class]['t2i'].'**'.'</br>';
	//echo $score.'</br>';
	return $score;
}
//$class_name,$word:string. $number_of_vocabulary:integer. $length,&$vocabulary:array.
function calculate_conprob($class_name,$word,$number_of_vocabulary,$length,&$vocabulary){

	//echo $word." ".$vocabulary[$word][$class_name].'</br>';
	
	$p = 0; //probability

	if($vocabulary[$word][$class_name]==NULL)
		$p = 1 / ($length[$class_name] + $number_of_vocabulary);
	else
		$p = ($vocabulary[$word][$class_name] + 1) / ($length[$class_name] + $number_of_vocabulary);
	return $p;
}
//
function CountDocInClassContainingTerm($class_name,$training,$vocabulary){

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
}
function calculate_conprob_bernoulli($class_name,$word,&$vocabulary,$number_of_document){

	$p = 0; //probability

	if($vocabulary[$word][$class_name]==NULL)
		$p = 1 / ($number_of_document + 2);
	else
		$p = ($vocabulary[$word][$class_name]+1) / ($number_of_document + 2);
	return $p;

}
function scoring_bernoulli($class_name,$text,$condprob,$vocabulary){

	$score = 0;
	$occurrence = array();
	

	//echo $text.'</br>';
	$tmp = explode(" ", trim($text));
	for($j=0;$j<count($tmp);$j++){
		$term = string_process($tmp[$j]);
		if($term == 1) continue;
		$occurrence[$term] = 1;
	}

	//var_dump($occurrence);
	//echo '</br></br>';
	foreach ($vocabulary as $key => $value) {
		if($occurrence[$key] != ""){
			//echo $key." ".$condprob[$class_name][$key].'</br>';
			$score += log($condprob[$class_name][$term]);
			
		}
		else if($condprob[$class_name][$key]!=""){
			//echo $key." ".$condprob[$class_name][$key].'</br>';
			$score += log(1-$condprob[$class_name][$term]);
			//$score += log($condprob[$class_name][$term]);
		}
	}	
	unset($occurrence);	
	return $score;
}
function doublemax($mylist){
  
  $maxvalue=max($mylist);
  while(list($key,$value)=each($mylist)){
    if($value==$maxvalue)$maxindex=$key;
  }
  return array("m"=>$maxvalue,"i"=>$maxindex);
}

?>