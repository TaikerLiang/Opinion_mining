<?php
/**
 * oversample
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * used to solve imbalanced data problem
 * 
 * 25 APR 2013
 */


	function oversampling($training, $class, $number){

		//var_dump($training);
		echo "</br>--- oversampling ---</br>";

		$max = 0;
		$count = array();
		for($i=0;$i<count($class);$i++){
			$name_of_class = $class[$i];
			if($number[$name_of_class] > $max){
				$max = $number[$name_of_class];
				$max_name = $name_of_class;
			}	
		}

		//echo $max_name.'</br>';
		$minority = array();
		$count = 0;
		for($i=0;$i<count($training);$i++){
			if($training[$i]['class'] != $max_name){
				$minority[$count]['content'] = $training[$i]['content'];
				$minority[$count]['class'] = $training[$i]['class']; 
				$minority[$count]['category'] = $training[$i]['category'];
				$count++;
			}
		}

		$count = count($training);
		for($i=0;$i<$max-count($minority);$i++){
			$tmp = rand(0, count($minority)-1);
			$training[$count]['content'] = $minority[$tmp]['content'];
			$training[$count]['class'] = $minority[$tmp]['class'];
			$training[$count]['category'] = $minority[$tmp]['category'];
			$count++;
		}

		return $training;

		/*$count = array();
		$tmp_training = array();

		for($i=0;$i<count($class);$i++){
			$name_of_class = $class[$i];
			$count[$name_of_class] = 0;
		}

		for($i=0;$i<count($training);$i++){
			for($j=0;$j<count($class);$j++){
				if($training[$i]['class'] == $class[$j]){
					$tmp_training[$class[$j]][$count[$class[$j]]]['content'] = $training[$i]['content'];
					$count[$class[$j]]++;
				}
			}
		}*/

		/*$majority = array();
		$minority = array();

		for($i=0;$i<count($class);$i++){
			if($i==0){
				$tmp = $count[$class[$i]];
				$tmp_name = $class[$i];
			}
			else{
				if($count[$class[$i]] < $tmp){
					$minority['number'] = $count[$class[$i]];
					$minority['class'] = $class[$i];
					$majority['number'] = $tmp;
					$majority['class'] = $tmp_name;
				}
				else{
					$minority['number'] = $tmp;
					$minority['class'] = $tmp_name;
					$majority['number'] = $count[$class[$i]];
					$majority['class'] = $class[$i];
				}
			}
		}

		//var_dump($minority);

		$number = array();

		for($i=0;$i<$majority['number'] - $minority['number'];$i++){
			$tmp = rand(0, $minority['number']-1);
			if($number[$tmp] != 1){
				$number[$tmp] = 1;
			}
			else{
				$i--;
			}
		}


		//var_dump($number);

		$new_training = array();
		$c=0;

		$p=0;
		$n=0;

		for($i=0;$i<count($class);$i++){
			$name_of_class = $class[$i];
			if($name_of_class == $minority['class']){
				for($j=0;$j<count($tmp_training[$name_of_class]);$j++){
					$new_training[$c]['content'] = $tmp_training[$name_of_class][$j]['content'];
					$new_training[$c]['class'] = $name_of_class;
					$c++;
					$p++;
					if($number[$j] ==  1){
						$new_training[$c]['content'] = $tmp_training[$name_of_class][$j]['content'];
						$new_training[$c]['class'] = $name_of_class;
						$c++;
						$p++;
					}
				}
			}
			else{
				for($j=0;$j<count($tmp_training[$name_of_class]);$j++){
					$new_training[$c]['content'] = $tmp_training[$name_of_class][$j]['content'];
					$new_training[$c]['class'] = $name_of_class;
					$c++;
					$n++;
				}
			}
		}*/


		//var_dump($new_training);

		return $new_training;
	}

?>