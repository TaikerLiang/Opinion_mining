<?php

/**
 * Retrieve a list of friends for the authenticating user and then lookup
 * their details using users/lookup. If you want to retrieve followers you
 * can change the URL from '1/friends/ids' to '1/followers/ids'.
 *
 * Although this example uses your user token/secret, you can use
 * the user token/secret of any user who has authorised your application.
 *
 * Instructions:
 * 1) If you don't have one already, create a Twitter application on
 *      https://dev.twitter.com/apps
 * 2) From the application details page copy the consumer key and consumer
 *      secret into the place in this code marked with (YOUR_CONSUMER_KEY
 *      and YOUR_CONSUMER_SECRET)
 * 3) From the application details page copy the access token and access token
 *      secret into the place in this code marked with (A_USER_TOKEN
 *      and A_USER_SECRET)
 * 4) Visit this page using your web browser.
 *
 * @author themattharris
 */

	define('LOOKUP_SIZE', 100);
	
	require '../tmhOAuth.php';
	require '../tmhUtilities.php';
	
	$tmhOAuth = new tmhOAuth(array(
	  'consumer_key'    => 'jIQMGEqohOkoR8Adlb8lHA',
	  'consumer_secret' => '1LxOasQlJBmlQZlGcXafHuD2uqQtQMp7qDnUPLIno8c',
	  'user_token'      => '392516961-mCMeGzPQusO6XNw6uqOcLJgUq8oNBe7swPplRiQA',
	  'user_secret'     => '8PaDbdXQG8eTP0oGAgd7LER2fdsaA8Ay0FXQrcjb1c',
	));

	function check_rate_limit($response) {
	  	$headers = $response['headers'];
	  	if ($headers['x_ratelimit_remaining'] == 0) :
	    	$reset = $headers['x_ratelimit_reset'];
	    	$sleep = time() - $reset;
	    	echo 'rate limited. reset time is ' . $reset . PHP_EOL;
	    	echo 'sleeping for ' . $sleep . ' seconds';
	    	sleep($sleep);
	  	endif;
	}

	$cursor = '-1';
	$ids = array();
	while (true) :
		  if ($cursor == '0')
	 		   break;

  		  $tmhOAuth->request('GET', $tmhOAuth->url('1/friends/ids'), array(
   			 'cursor' => $cursor
  	      ));

  		   // check the rate limit
  		   check_rate_limit($tmhOAuth->response);

  		  if ($tmhOAuth->response['code'] == 200) {
			    $data = json_decode($tmhOAuth->response['response'], true);
			    
			    //echo $data.'SDA</br>'; //test
			    //var_dump($data);
			    
			    $ids = array_merge($ids, $data['ids']);
			    $cursor = $data['next_cursor_str'];
  		 }	 
  		else {
    		echo $tmhOAuth->response['response'];
  		    break;
  		}
  		usleep(500000);
	 endwhile;

	 //lookup users
	$paging = ceil(count($ids) / LOOKUP_SIZE);
	$users = array();
	for ($i=0; $i < $paging ; $i++) {
	  $set = array_slice($ids, $i*LOOKUP_SIZE, LOOKUP_SIZE);
	
	  $tmhOAuth->request('GET', $tmhOAuth->url('1/users/lookup'), array(
	    'user_id' => implode(',', $set)
	  ));
	
	  // check the rate limit
	  check_rate_limit($tmhOAuth->response);
	
	  if ($tmhOAuth->response['code'] == 200) {
	    $data = json_decode($tmhOAuth->response['response'], true);
	    $users = array_merge($users, $data);
	  } 
	  else {
	    echo $tmhOAuth->response['response'];
	    break;
	  }
	}
	//var_dump($users);
	//open file
	$count =0;
	 
	for($i=0;$i<count($users);$i++){
		
		$fileName = trim($users[$i]['name']);
		$fileName = str_replace("#" , "", $fileName);
		$fileName = str_replace("@" , "", $fileName);
		$fileName = str_replace("^" , "", $fileName);
		$fileName = str_replace("+" , "", $fileName);

		$filenameOut = "../User Information/".$fileName.'.txt';  
	    $fw = fopen ($filenameOut, "w");	
		
		fwrite($fw,"Name: ".$users[$i]['name']."\n");
		fwrite($fw,"Screen_Name: ".$users[$i]['screen_name']."\n");
		fwrite($fw,"Statuses_Count: ".$users[$i]['statuses_count']."\n");
		fwrite($fw,"Id_Str: ".$users[$i]['id_str']."\n");
		fwrite($fw,"Location: ".$users[$i]['location']."\n");
		fwrite($fw,"Friends_Count: ".$users[$i]['friends_count']."\n");
		fwrite($fw,"Followers_Count: ".$users[$i]['followers_count']."\n");
		
		/*echo "name:".$users[$i]['name'].'</br>';                //need
		echo $users[$i]['screen_name'].'</br>';    //need
		echo $users[$i]['statuses_count'].'</br>'; //need
		echo $users[$i]['id_str'].'</br>';
		echo $users[$i]['location'].'</br>';
		echo $users[$i]['friends_count'].'</br>';
		echo $users[$i]['followers_count'].'</br>';
		echo '-----------------------------------</br>';*/
		
		$count += $users[$i]['statuses_count'];
	}
	echo $count;
	

?>