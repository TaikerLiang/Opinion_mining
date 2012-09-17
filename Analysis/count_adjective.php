<?php

   require_once("../../Library/Input/read_file.php");
   require_once('./tagClass.php');
   require_once('./function.php');
   
   ini_set('memory_limit', '256M');     //memory size
   set_time_limit(0);                   //time
   
   $dirName = './Tagger/TagTweet/';
   $dirList = openDirectory($dirName);
   $i=0;
   
   foreach($dirList as $key => $N){	
   		if($key >= 2){
   			$fileName = $dirName.$dirList[$key];
   			$fd = openFileRead($fileName);
   			
   			if(!$fd) die('can not open the file');
   			  				
   			while( $str = fgets($fd) ){
   			
   				$tmp = explode("	",$str);
   				//echo $tmp[0].'</br>';
   				if( trim($tmp[1]) == "SENT") continue;
   				
   				if( filterOutOfAdjective($tmp[1]) ){	
   					if($i>0){
   						if(search($ADJ, $i, trim(strtolower($tmp[0]))) == 0){
							$ADJ[$i] = new Adjective();
   							$ADJ[$i]->setWord(trim(strtolower($tmp[0])));
   							$ADJ[$i]->setTag($tmp[1]);
   							$ADJ[$i]->setCount();
   							$i++;
   						}
   					}
   					else{
						//echo trim(strtolower($tmp[0]));
						//echo '</br>';
						//echo "fuck</br>";
						$ADJ[$i] = new Adjective();
   						$ADJ[$i]->setWord(trim(strtolower($tmp[0])));
   						$ADJ[$i]->setTag($tmp[1]);
   						$ADJ[$i]->setCount();
   						$i++;
   					}
   			 	}//if
   			}//while
   		}//if			
   	}//foreach
   	
   for($j =0 ; $j<$i ; $j++){
		/*if($ADJ[$j]->getWord()=="bad"){
			echo $ADJ[$j]->getWord().'</br>';
			echo $ADJ[$j]->getTag().'</br>';
			echo $ADJ[$j]->getCount().'</br>';
			echo "----------------</br>";
		}
		if($ADJ[$j]->getWord()=="nasty"){
			echo $ADJ[$j]->getWord().'</br>';
			echo $ADJ[$j]->getTag().'</br>';
			echo $ADJ[$j]->getCount().'</br>';
			echo "----------------</br>";
		}
		if($ADJ[$j]->getWord()=="poor"){
			echo $ADJ[$j]->getWord().'</br>';
			echo $ADJ[$j]->getTag().'</br>';
			echo $ADJ[$j]->getCount().'</br>';
			echo "----------------</br>";
		}
		if($ADJ[$j]->getWord()=="negative"){
			echo $ADJ[$j]->getWord().'</br>';
			echo $ADJ[$j]->getTag().'</br>';
			echo $ADJ[$j]->getCount().'</br>';
			echo "----------------</br>";
		}
		if($ADJ[$j]->getWord()=="unfortunate"){
			echo $ADJ[$j]->getWord().'</br>';
			echo $ADJ[$j]->getTag().'</br>';
			echo $ADJ[$j]->getCount().'</br>';
			echo "----------------</br>";
		}
		if($ADJ[$j]->getWord()=="wrong"){
			echo $ADJ[$j]->getWord().'</br>';
			echo $ADJ[$j]->getTag().'</br>';
			echo $ADJ[$j]->getCount().'</br>';
			echo "----------------</br>";
		}
		if($ADJ[$j]->getWord()=="inferior"){
			echo $ADJ[$j]->getWord().'</br>';
			echo $ADJ[$j]->getTag().'</br>';
			echo $ADJ[$j]->getCount().'</br>';
			echo "----------------</br>";
		}
		if($ADJ[$j]->getWord()=="good"){
			echo $ADJ[$j]->getWord().'</br>';
			echo $ADJ[$j]->getTag().'</br>';
			echo $ADJ[$j]->getCount().'</br>';
			echo "----------------</br>";	
		}
		if($ADJ[$j]->getWord()=="nice"){
			echo $ADJ[$j]->getWord().'</br>';
			echo $ADJ[$j]->getTag().'</br>';
			echo $ADJ[$j]->getCount().'</br>';
			echo "----------------</br>";
		}
		if($ADJ[$j]->getWord()=="positve"){
			echo $ADJ[$j]->getWord().'</br>';
			echo $ADJ[$j]->getTag().'</br>';
			echo $ADJ[$j]->getCount().'</br>';
			echo "----------------</br>";
		}
		if($ADJ[$j]->getWord()=="excellent"){
			echo $ADJ[$j]->getWord().'</br>';
			echo $ADJ[$j]->getTag().'</br>';
			echo $ADJ[$j]->getCount().'</br>';
			echo "----------------</br>";
		}
		if($ADJ[$j]->getWord()=="fortunate"){
			echo $ADJ[$j]->getWord().'</br>';
			echo $ADJ[$j]->getTag().'</br>';
			echo $ADJ[$j]->getCount().'</br>';
			echo "----------------</br>";
		}
		if($ADJ[$j]->getWord()=="correct"){
			echo $ADJ[$j]->getWord().'</br>';
			echo $ADJ[$j]->getTag().'</br>';
			echo $ADJ[$j]->getCount().'</br>';
			echo "----------------</br>";
		}
		if($ADJ[$j]->getWord()=="superior"){
			echo $ADJ[$j]->getWord().'</br>';
			echo $ADJ[$j]->getTag().'</br>';
			echo $ADJ[$j]->getCount().'</br>';
			echo "----------------</br>";
		}*/

		if($ADJ[$j]->getCount() >= 100){
			echo $ADJ[$j]->getWord().'</br>';
			echo $ADJ[$j]->getTag().'</br>';
			echo $ADJ[$j]->getCount().'</br>';
			echo "----------------</br>";
		}
   	}
   	
?>