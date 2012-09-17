<?

	require_once('./TwitterSearch.php');
	
	$target = "Olympus";

	for($j=1;$j<3;$j++){
		$search = new TwitterSearch($target);
		//$search -> contains(":)");
		$search -> contains(":)");
		$search -> lang("en");
		$search -> page($j);
		$search -> rpp(30);
    	$results = $search->results();
    	

    	for($i=0;$i<count($results);$i++){
    		echo $results[$i]->text.'</br>';
   		}
    }

    echo "----------------------------</br>";

	for($j=1;$j<3;$j++){
		$search = new TwitterSearch($target);
		//$search -> contains(":)");
		$search -> contains(":(");
		$search -> lang("en");
		$search -> page($j);
		$search -> rpp(30);
    	$results = $search->results();
    	

    	for($i=0;$i<count($results);$i++){
    		echo $results[$i]->text.'</br>';
   		}
    }

  
	echo "complete</br>";

?>