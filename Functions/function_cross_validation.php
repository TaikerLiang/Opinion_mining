<?php
/**
 * cross_validation
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 *
 * 
 * 27 Jun 2013
 */
	
	function crossValidation($foldNum, $data){

		for($i=0;$i<$foldNum;$i++){
			$number_fold[$i] = 0;
		}

		for($i=0;$i<count($data);$i++){
			$id = rand(0,4);
			if($number_fold[$id] >= (count($data)/$foldNum)){
					$i--;
					continue;
			}
			$fold[$id][$number_fold[$id]++] = $i;
		}

		return $fold;
	}
	function crossValidation_extract_opinion($foldNum, $class, $data){

		$number = array();
		for($i=0;$i<count($class);$i++){
			$number[$class[$i]] = 0;
		}

		for($i=0;$i<count($data);$i++){

			$opinion = $data[$i]['opin'];

			if($opinion == 'non-opinion')
				$array['non-opinion'][$number['non-opinion']++] = $i;
			else
				$array['opinion'][$number['opinion']++] = $i;
		}

		var_dump($number);
		//echo count($array['non-opinion']).'</br>';

		$fold = array();

		for($i=0;$i<count($class);$i++){
			$class_name = $class[$i];
			$tmp = array();
			$number_fold = array();

			for($j=0;$j<$foldNum;$j++){
				$number_fold[$i] = 0;
			}
			for($j=0;$j<$number[$class_name];$j++){
				$id = rand(0,4);
				if($number_fold[$id] >= ($number[$class_name]/$foldNum)){
					$j--;
					continue;
				}
				$fold[$class_name][$id][$number_fold[$id]++] = $array[$class_name][$j];
			}
		}

		return $fold;
	}

	function crossValidation_short_text_classification($foldNum, $class, $data){
		$number = array();
		for($i=0;$i<count($class);$i++){
			$number[$class[$i]] = 0;
		}

		for($i=0;$i<count($data);$i++){

			$category = $data[$i]['category'];
			
			if($category == 'mobilephone')
				$array['mobilephone'][$number['mobilephone']++] = $i;
			else if($category == 'movie')
				$array['movie'][$number['movie']++] = $i;
			else
				$array['camera'][$number['camera']++] = $i;
		}

		var_dump($number);

		$fold = array();

		for($i=0;$i<count($class);$i++){
			$class_name = $class[$i];
			$tmp = array();
			$number_fold = array();

			for($j=0;$j<$foldNum;$j++){
				$number_fold[$i] = 0;
			}
			for($j=0;$j<$number[$class_name];$j++){
				$id = rand(0,4);
				if($number_fold[$id] >= ($number[$class_name]/$foldNum)){
					$j--;
					continue;
				}
				$fold[$class_name][$id][$number_fold[$id]++] = $array[$class_name][$j];
			}
		}

		return $fold;
	}

	function crossValidation_determine_opinion($foldNum, $class, $data, $category_name){

		$number = array();
		for($i=0;$i<count($class);$i++){
			$number[$class[$i]] = 0;
		}

		for($i=0;$i<count($data);$i++){

			$opinion = $data[$i]['opin'];
			$category = $data[$i]['category'];
			
			if($category_name != $category)
				continue;

			if($opinion == 'positive')
				$array['positive'][$number['positive']++] = $i;
			else if($opinion == 'negative')
				$array['negative'][$number['negative']++] = $i;
		}

		var_dump($number);
		echo '</br>';

		for($i=0;$i<count($class);$i++){
			$class_name = $class[$i];
			$tmp = array();
			$number_fold = array();

			for($j=0;$j<$foldNum;$j++){
				$number_fold[$i] = 0;
			}
			for($j=0;$j<$number[$class_name];$j++){
				$id = rand(0,4);
				if($number_fold[$id] >= ($number[$class_name]/$foldNum)){
					$j--;
					continue;
				}
				$fold[$class_name][$id][$number_fold[$id]++] = $array[$class_name][$j];
			}
		}

		return $fold;
	}






?>