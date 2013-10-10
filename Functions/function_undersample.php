<?php
/**
 * undersample
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * used to solve imbalanced data problem
 * 
 * 25 APR 2013
 */


	function undersampling($training, $class, $count){

		echo "</br>---undersampling---</br>";
		$count = array();
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
		}

		//var_dump($tmp_training['non-opinion']);

		$majority = array();
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

		$number = array();

		for($i=0;$i<$minority['number'];$i++){

			$tmp = rand(0, $majority['number']-1);
			if($number[$tmp] != 1){
				$number[$tmp] = 1;
			}
			else{
				$i--;
			}
		}

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
				}
			}
			else{
				for($j=0;$j<count($tmp_training[$name_of_class]);$j++){
					if($number[$j] ==  1){
						$new_training[$c]['content'] = $tmp_training[$name_of_class][$j]['content'];
						$new_training[$c]['class'] = $name_of_class;
						$c++;
						$n++;
					}
				}
			}
		}


		//var_dump($new_training);
		//echo '</br>';
		//echo rand(5, 15).'</br>';

		return $new_training;


	}

?>