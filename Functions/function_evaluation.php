<?php
/**
 * evaluation
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * G-means, F-measure
 * 
 * 18 APR 2013
 */


	function evaluation($result, $class){

		//calculate True positive,  False positive, True negative, False negative
		$true_positive = 0;
		$false_posiitve = 0;
		$true_negative = 0;
		$false_negative = 0;

		for($i=0;$i<count($class);$i++){
			//echo $class[$i].'</br>';
			$count = 0;
			foreach ($result[$class[$i]] as $key => $value) {
				if($i==0){
					if($count==0)
						$true_positive = $value;
					else
						$false_negative = $value;
				}
				else{
					if($count==0)
						$false_posiitve = $value;
					else
						$true_negative = $value;
				}
				$count++;
				//echo $key.' '.$value.'</br>';
			}
		}

		//var_dump($result);
		/*echo '</br>';

		echo 'true_positive: '.$true_positive.'</br>';
		echo 'false_posiitve: '.$false_posiitve.'</br>';
		echo 'true_negative: '.$true_negative.'</br>';	
		echo 'false_negative: '.$false_negative.'</br>';*/


		//echo '</br>test: '.(0.5 * ($true_positive/($true_positive + $false_posiitve)) + 0.5 * ($true_negative/($true_negative + $false_negative)));

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
	}

	function G_means($sensitivity,$specificity){
		echo '</br>G-means: '.sqrt($sensitivity * $specificity).'</br>';
	}

	function F_measure($precision, $recall){
		echo '</br>F-measure: '.(2 * $precision * $recall) / ($precision + $recall).'</br>';
	}	



?>