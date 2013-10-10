<?php
/**
 * function_others
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * something for testing and evaluation
 *
 *
 * 
 * 4 Mar 2013
 **/



	function check_emotion_meaning($data){

		//var_dump($data);
		$count=0;
		$S=0;
		$NS=0;
		$correct = 0;
		$wrong =0;
		for($i=0;$i<count($data);$i++){
			$decision = $data[$i]['decision'];
			$str = $data[$i]['emotion_meaning'];
			$opin = $data[$i]['class'];

			if($opin != "positive" && $opin != "negative") continue;

			$tmp = explode(" ", $str);
			$meaning_direction = "";
			$score = 0;

			if(count($tmp)>1){
				for($j=0;$j<count($tmp);$j++){
					if($tmp[$j] == "P")
						$score++;
					else
						$score--;
				}
				//echo $score.' dfs</br>';
				if($score > 0)
					$meaning_direction = "positive";
				else if($score < 0)
					$meaning_direction = "negative";
				else 
					$meaning_direction = "";
			}
			else{
				if($tmp[0] == "P")
					$meaning_direction = "positive";
				else
					$meaning_direction = "negative";
			}

			


			if($meaning_direction == $decision){

				
				$S++;
				//echo $S.'S '.$decision.' '.$str.'</br>';
			}
			else{
				if($decision == $opin)
					$correct++;
				else
					$wrong++;
				
				$NS++;
				//echo $NS.'NS '.$decision.$str.'</br>';
			}
				

			
			





			/*if($decision == $){

			}
			//if($opin != "positive" && $opin != "negative") continue;

			
			if($decision != $opin){

				//echo $decision.' ';
				//echo $opin.' ';
				//echo $str.'-----</br>';
				$count++;
				echo $count.' '.$decision.' '.$str.'</br>'; 
			}*/
				

			








		}

		echo 'same: '.$S.'</br>';
		echo 'not the same'.$NS.'</br>';
		echo 'correct: '.$correct.'</br>';
		echo 'wrong: '.$wrong.'</br>';
		echo '</br>';
		echo 'check_emotion_meaning complete</br>';

	}






?>