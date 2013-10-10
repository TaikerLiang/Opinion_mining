<?php
/**
 * class_TF-IDF
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 *  
 * 26 Mar 2013
 **/


	class TFIDF {


		public $training = array();
		public $testing = array();
		private $class_name = array();
		private $term_number = array();
		private $number;
		private $total_of_document = array();
		private $count_term_in_document = array();
		private $tfidf_value_train = array();
		private $tfidf_value_test = array();


		public function __construct($class){
			$this->class_name = $class;
			$this->number = 1;
    	}

		//tf: term frequency
		//tf_idf = tf * log(idf)
		public function tf_idf() {

			//$fileNameOut = './New/'.$target;
 			//$fw = openFileWrite($fileNameOut);
 			//fwrite($fw, $string);
			for($i=0;$i<count($this->class_name);$i++){
				
				for($j=0;$j<count($this->training[$this->class_name[$i]]);$j++){
					
					$count_term = array();
					$doc = $this->training[$this->class_name[$i]][$j];
					//echo $doc.'</br>';
					$tmp = explode(" ", $doc);
					$length = count($tmp);
					for($k=0;$k<$length;$k++){
						//echo $tmp[$k];

						$term = string_process($tmp[$k]);
						if($term == 1) continue;
						
						if($count_term[$term]==NULL)
							$count_term[$term] = 1;
						else
							$count_term[$term]++;
					}

					foreach ($count_term as $term => $value) {

						if($this->tfidf_value_train[$term]['value']!=NULL) continue;

						$tf = $value / $length;
						$idf = log($this->total_of_document/$this->count_term_in_document[$term]);

						
						$this->tfidf_value_train[$term]['value'] = $tf * $idf;
						$this->tfidf_value_train[$term]['number'] = $this->term_number[$term];
							
					}
				}
			}

		}

		//inverse document frequency
		public function idf(){
			
			//echo $this->total_of_document.'</br>';

			for($i=0;$i<count($this->class_name);$i++){
				//echo $this->class_name[$i].'</br>';

				for($j=0;$j<count($this->training[$this->class_name[$i]]);$j++){
					$term_document = array();
					$doc = $this->training[$this->class_name[$i]][$j];
					//echo $doc.'</br>';
					$tmp = explode(" ", $doc);

					for($k=0;$k<count($tmp);$k++){
						//echo $tmp[$k];

						$term = string_process($tmp[$k]);
						if($term == 1) continue;
						//echo $term.' ';
						$term_document[$term] = 1;
					}

					foreach ($term_document as $key => $value) {
						# code...
						//echo $key.' ';
						if($this->term_number[$key]==NULL){
							$this->term_number[$key] = $this->number;
							$this->number++;
						}

						if($this->count_term_in_document[$key]==NULL){
							$this->count_term_in_document[$key] = 1;
						}
						else{
							$this->count_term_in_document[$key]++;
						}
					}
					//echo '<br>';
				}
			}
		}
		public function put_training_data($class, $training_data){
			
			 $number = array();

    		for($i=0;$i<count($class);$i++){
      			$number[$class[$i]] = 0;
    		}

			for($i=0;$i<count($training_data);$i++){
				$class_name = $training_data[$i]['class'];
				$this->training[$class_name][$number[$class_name]] = $training_data[$i]['content'];
				$number[$class_name]++;
			}
			$this->total_of_document = count($training_data);

		}
		public function set_testing_data($part ,$testing_data, $class, $round, $feature){
			
			$fileNameOut = "./libsvm/{$part}_test".$round;
			$fw = openFileWrite($fileNameOut);

			for($i=0;$i<count($testing_data);$i++){
				$term_document = array();
				$doc = $testing_data[$i]['content'];
				$tmp = explode(" ", $doc);
				$tmp_term = array();
				$string = "";
				$length = count($tmp);
				for($j=0;$j<count($class);$j++){
					$class_name = $class[$j];
					if($testing_data[$i]['class'] == $class_name){
						$num = $j+1;
						$string = "{$num} ";
					}
				}
				for($j=0;$j<count($tmp);$j++){
					//echo 'fuck</br>';
					$term = string_process($tmp[$j]);
					//echo $term.'</br>';
					if($term == 1) continue;
					// may be modify
					if($this->term_number[$term]==NULL) continue;

					if($feature[$term] == NULl && $feature!=NULL) continue;
					//echo $term.'</br>';

					$tmp_term[$term] = $this->term_number[$term];

					if($term_document[$term] == NULL)
						$term_document[$term] = 1;
					else
						$term_document[$term]++;
				}
				asort($tmp_term);
				foreach ($tmp_term as $key => $value) {
					//echo $key.'</br>';
					$string = $string.$tmp_term[$key].':';
					$tf = $term_document[$key] /$length;
					$idf = log($this->total_of_document/$this->count_term_in_document[$key]);
					$value = $tf * $idf;
					//echo $value.'</br>';
					$string = $string.$value.' ';
				}
				//var_dump($tmp_term);
				//echo $string.'</br>';
				fwrite($fw, $string."\n");
			}	
		}
		public function set_trainging_data($part ,$training_data, $class, $round, $feature){
			
			$fileNameOut = "./libsvm/{$part}_train".$round;
			$fw = openFileWrite($fileNameOut);

			for($i=0;$i<count($training_data);$i++){
          		$string = "";
          		for($j=0;$j<count($class);$j++){
					$class_name = $class[$j];
					if($training_data[$i]['class'] == $class_name){
						$num = $j+1;
						$string = "{$num} ";
					}
				}

				$tmp = explode(" ", $training_data[$i]['content']);
          		$tmp_term = array();
          		for($j=0;$j<count($tmp);$j++){
            		$term = string_process($tmp[$j]);
            		if($term == 1) continue;
            		if($feature != NULL){
              			if($feature[$term] != NULL)
                			$tmp_term[$term] = $this->tfidf_value_train[$term]['number'];
            		}
            		else{
              			$tmp_term[$term] = $this->tfidf_value_train[$term]['number'];
            		}
          		}
          
          		asort($tmp_term);

          		foreach ($tmp_term as $key => $value) {
            		$string = $string.$value.':';
           	 		$string = $string.$this->tfidf_value_train[$key]['value'].' ';
          		}
          		
          		$string = $string."\n";
          		fwrite($fw, $string);
          	}
		}
		public function get_training_data(){
			return $this->training;
		}
		public function get_tfidf_value_train(){
			return $this->tfidf_value_train;
		}
		private function string_process($string){
			
			$string = str_replace("(","",$string);
			$string = str_replace(")","",$string);
			$string = str_replace("?"," ? ",$string);
	
			$tmp = explode(",", $string);

			if($tmp[3]=="ST" || trim($tmp[0])==NULL || $tmp[1]==":") 
				return 1;

			/*if($tmp[0] == "the" || $tmp[0] == "a" || trim($tmp[0])==NULL)
				return 1;*/
		
			$term = strtolower($tmp[0]);
			//echo $term.'</br>';
		
			return trim($term);
		}
	}

?>