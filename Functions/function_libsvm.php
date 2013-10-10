<?php
/**
 * function_libsvm.php
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * need to download the libsvm first http://www.csie.ntu.edu.tw/~cjlin/libsvm/
 * call the libsvm
 *
 * 
 * 9 Apr 2013
 **/
	
	function libsvm_vocabulary($training){

		$termID = array();
		$number = 0;
		for($i=0;$i<count($training);$i++){
			
			$doc = $training[$i]['content'];
			$tmp = explode(" ", $doc);
			for($j=0;$j<count($tmp);$j++){
				$term = string_process($tmp[$j]);
				if($term == 1) continue;

				if($termID[$term]==null){
					$termID[$term] = $number;
					$number++;
				}
			}			
		}

		//var_dump($termID);
		return $termID;

	}

	function libsvm_generated_train($termID , $training){
		
		$fileNameOut = './libsvm/train.txt';
    	$fw = openFileWrite($fileNameOut);

		for($i=0;$i<count($training);$i++){
			
			$doc = $training[$i]['content'];
			$tmp = explode(" ", $doc);
			
			$count = array();

			for($j=0;$j<count($tmp);$j++){
				$term = string_process($tmp[$j]);
				if($term == 1) continue;

				$ID = $termID[$term];

				if($count[$ID] == NULL)
					$count[$ID] = 1;
				else
					$count[$ID]++;
			}			
			ksort($count);
			//var_dump($count);
			//echo '</br>';

			$string = "";
			if($training[$i]['class'] == "opinion")
        		$string = "1 ";
      		else
        		$string = "-1 ";

			foreach ($count as $ID => $value) {
        		$string = $string.$ID.':';
        		$string = $string.$value.' ';
      		}
      		$string = $string."\n";
      		//echo $string.'</br>';
      		fwrite($fw, $string);
		}
	}	
	
	function libsvm_generated_test($termID , $testing){
		
		//var_dump($testing);
		$fileNameOut = './libsvm/test.txt';
    	$fw = openFileWrite($fileNameOut);

    	echo 'numbet of testing data: '.count($testing).'</br>';
		
		for($i=0;$i<count($testing);$i++){
			$doc = $testing[$i]['content'];
			$tmp = explode(" ", $doc);
			//echo $testing[$i]['content'].'</br>';

			$count = array();

			for($j=0;$j<count($tmp);$j++){
					
				$term = string_process($tmp[$j]);
				if($term == 1) continue;

				$ID = $termID[$term];
				if($ID == "") continue;



				if($count[$ID] == NULL)
					$count[$ID] = 1;
				else
					$count[$ID]++;
			}

			ksort($count);
			//var_dump($count);
			//echo '</br>';

			$string = "";
			if($testing[$i]['opin'] == "non-opinion")
        		$string = "-1 ";
      		else
        		$string = "1 ";

			foreach ($count as $ID => $value) {
        		$string = $string.$ID.':';
        		$string = $string.$value.' ';
      		}
      		$string = $string."\n";
      		//echo $string.'</br>';	
      		fwrite($fw, $string);	
		}
		
	}

	function decode_svm($part, $testing, $round, $count_of_correct){

		$fileName = "./libsvm/{$part}_test".$round;
   		$fd = openFileRead($fileName);
   		if(!$fd) die('can not open the file'); 	

   		$count = 0;

   		while ($str = fgets($fd)) {
   			$tmp = explode(" ", $str);
   			$answer[$count++] = trim($tmp[0]);
   		}

   		fclose($fd);

   		$fileName = './libsvm/result'.$round.'.txt';
   		$fd = openFileRead($fileName);
   		if(!$fd) die('can not open the file'); 	

   		$count = 0;

   		while ($str = fgets($fd)) {
   			$result[$count++] = trim($str);
   		}

   		fclose($fd);
   		$count = 0;
   		$returnDataID = array();
   		//echo count($answer).'</br>';
   		//echo count($result).'</br>';
   		
	   		$true_positive = 0;
			$false_posiitve = 0;
			$true_negative = 0;
			$false_negative = 0;

	   		for($i=0;$i<count($answer);$i++){
	   			if($result[$i] == 1){
	   				$returnDataID[$count++] = $i;
	   			}

	   			if($answer[$i] == 1){
	   				if($answer[$i] == $result[$i]){
	   					$true_positive++;
	   				}
	   				else{
	   					$false_posiitve++;
	   				}
	   			}
	   			else{
	   				if($answer[$i] == $result[$i]){
	   					$true_negative++;
	   				}
	   				else{
	   					$false_negative++;
	   				}
	   			}
	   		}



	   		echo "TN: ".$true_negative.'</br>';

	   		
	   		if($part == "extract_opinion")
	   			$count_of_correct += $true_negative;
	   		else
	   			$count_of_correct += ($true_positive + $true_negative);
	   		

	   		$accuracy = ($true_positive + $true_negative) / ($true_positive + $true_negative + $false_negative + $false_posiitve);

	   		echo '</br>Accuracy: '.$accuracy.'</br>';

	   		//calculate sensitivity and specificity 
			$sensitivity = 0;
			$specificity = 0;

			//sensitivity: TP/(TP + FN)
			$sensitivity = $true_positive / ($true_positive + $false_negative);
			//specificity: TN/(TN + FP)
			$specificity = $true_negative / ($true_negative + $false_posiitve);

			//precision: TP/(TP + FP)
			$precision = $true_positive / ($true_positive + $false_posiitve);
			//recall: TP/(TP + FN)
			$recall = $true_positive / ($true_positive + $false_negative);
				
			echo '</br>precision: '.$precision.'</br>';
			echo '</br>recall: '.$recall.'</br>';

			F_measure($precision, $recall);
			G_means($sensitivity,$specificity);

			echo '</br>--- decode_svm complete ---</br>';


   		for($i=0;$i<count($returnDataID);$i++){
   			$ID = $returnDataID[$i];
   			$returnData[$i]['content'] = $testing[$ID]['content'];
   			$returnData[$i]['opinion'] = $testing[$ID]['opinion'];
   			$returnData[$i]['category'] = $testing[$ID]['category'];
   			$returnData[$i]['decision'] = "opinion";
   		}
   		return $returnData;
	}

	function decode_svm_muti($part, $testing, $round, $class){

		$fileName = "./libsvm/{$part}_test".$round;
   		$fd = openFileRead($fileName);
   		if(!$fd) die('can not open the file'); 	

   		$count = 0;

   		while ($str = fgets($fd)) {
   			$tmp = explode(" ", $str);
   			$answer[$count++] = trim($tmp[0]);
   		}

   		fclose($fd);

   		$fileName = './libsvm/result'.$round.'.txt';
   		$fd = openFileRead($fileName);
   		if(!$fd) die('can not open the file'); 	

   		$count = 0;

   		while ($str = fgets($fd)) {
   			$tmp = explode(" ", $str);
   			$result[$count++] = trim($tmp[0]);
   		}

   		fclose($fd);
   		$count = 0;

   		for($i=0;$i<count($result);$i++){
   		
   			$returnData[$i]['content'] = $testing[$i]['content'];
   			$returnData[$i]['opinion'] = $testing[$i]['opinion'];
   			$returnData[$i]['category'] = $testing[$i]['category'];
   			$returnData[$i]['decision'] = $class[$result[$i]];
   		}
   		return $returnData;
	}


?>