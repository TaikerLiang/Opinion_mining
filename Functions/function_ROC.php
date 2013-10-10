<?php
/**
 * ROC (Receiver Operating Characteristic)
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * generate the data for ROC(Medcalc)
 * 
 * 18 APR 2013
 */


	function ROC($data, $class){

		//var_dump($data).'dfdf';

		for($i=0;$i<count($data);$i++){

			for($j=0;$j<count($class);$j++){
				$name_of_class = $class[$j];
				
				if($name_of_class == $data[$i]['class']){
					//echo $j.'</br>';
					//echo $data[$i]['value'].'</br>';
				}

			}
		}

		//echo '</br>-----</br>';

		for($i=0;$i<count($data);$i++){

			for($j=0;$j<count($class);$j++){
				$name_of_class = $class[$j];
				
				if($name_of_class == $data[$i]['class']){
					//echo $j.'</br>';
					//echo $data[$i]['value'].'</br>';
				}

			}
		}

	}


?>