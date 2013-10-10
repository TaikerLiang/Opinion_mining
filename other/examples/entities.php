<!DOCTYPE html>
<head>
  <meta charset="UTF-8" />
</head>
<body>
<?php

/**
 * Render a very rough timeline with entities included.
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

    require '../tmhOAuth.php';
	require '../tmhUtilities.php';
	
	$dirname = "../User Information/";
	$dirlist = scandir ($dirname);
	
	//echo $dirlist;
	set_time_limit(0);            
	$totalcount=0;
	//$dateTime;
	foreach($dirlist as $key => $N){
		$tmp = array();
	        if($key >= 2){
			
				$filename = $dirname.$dirlist[$key];     
				$fd = fopen ($filename, "r");             
			
				$file = file($filename);
				echo $file[0].'</br>';
				$tmp = explode(":",$file[0]);
				$name = $tmp[1];
				//echo $name; 
				
				$newName = str_replace(" ","_",$name);
				$fileName = str_replace("#" , "", $fileName);
				$fileName = str_replace("@" , "", $fileName);
				$fileName = str_replace("^" , "", $fileName);
				$fileName = str_replace("+" , "", $fileName);
				$filenameOut = "../Tagger/Tweet/".trim($newName).'.txt';  
				$fw = fopen ($filenameOut, "a");	

				
				//echo $file[1].'</br>';                //get screen_name
				$tmp = explode(":",$file[1]);
				//echo $tmp[1].'</br>';
				$screen_name = $tmp[1];
				
				echo $file[2].'</br>';                  //get statuses_count
				$tmp = explode(":",$file[2]);
				//echo $tmp[1].'</br>';
				$statuses_count = $tmp[1];
				

				date_default_timezone_set('UTC');


				$tmhOAuth = new tmhOAuth(array(
					  'consumer_key'    => 'jIQMGEqohOkoR8Adlb8lHA',
					  'consumer_secret' => '1LxOasQlJBmlQZlGcXafHuD2uqQtQMp7qDnUPLIno8c',
					  'user_token'      => '392516961-mCMeGzPQusO6XNw6uqOcLJgUq8oNBe7swPplRiQA',
					  'user_secret'     => '8PaDbdXQG8eTP0oGAgd7LER2fdsaA8Ay0FXQrcjb1c',
				));

				$count = floor($statuses_count / 10);        //crawlering the tweet per request  is maximum of 200
				echo $totalcount.'</br>';
				echo '--------------------</br>';
				
				for ($i=0; $i < $count  ; $i++) { 
					echo $i.'</br>';
					
					if($totalcount == 349){
						echo date('F.Y.h:i:s.A ')."<br />";
						echo "Sleep</br>";
					}
					if($totalcount >= 350){
						
						sleep(60*60);          //wait 1 hr
						echo date('F.Y.h:i:s.A ')."<br />";
						echo "Wake up</br>";               
						$totalcount=0;
						fclose($fw);
						break;
					}
					$totalcount++;
						
					$user = array();
					$user['include_entities'] = '1';
					$user['include_rts'] = '1';
					$user['screen_name'] = trim($screen_name);
					$user['count'] = 10;
					$user['page'] = (1 + $i);
								
					$code = $tmhOAuth->request('GET', $tmhOAuth->url('1/statuses/user_timeline'), $user);
				
					/*$code = $tmhOAuth->request('GET', $tmhOAuth->url('1/statuses/user_timeline'), array(
				  	'include_entities' => '1',
				  	'include_rts'      => '1',
				  	'screen_name'      => $screen_name,    //'themattharris',
				  	'count'            => 10,
					));*/

					// if code = 200, find website Successful
					if ($code == 200) {
					
						$timeline = json_decode($tmhOAuth->response['response'], true);
						foreach ($timeline as $tweet) :
							$entified_tweet = tmhUtilities::entify_with_options($tweet);
							$is_retweet = isset($tweet['retweeted_status']);

							$diff = time() - strtotime($tweet['created_at']);
							if ($diff < 60*60)
								$created_at = floor($diff/60) . ' minutes ago';
							elseif ($diff < 60*60*24)
								$created_at = floor($diff/(60*60)) . ' hours ago';
							else
								$created_at = date('d M', strtotime($tweet['created_at']));

							$permalink  = str_replace(
								array(
									'%screen_name%',
									'%id%',
									'%created_at%'
								),
								array(
									$tweet['user']['screen_name'],
									$tweet['id_str'],
									$created_at,
								),
								'<a href="https://twitter.com/%screen_name%/%id%">%created_at%</a>'
								//var_dump($tweet['user']);
								//echo  $tweet['user']['name'].'</br>';
							);
				
							fwrite($fw,$tweet['text']."\n");
  ?>
  <!--<div id="<?php echo $tweet['id_str']; ?>" style="margin-bottom: 1em">
    <span>ID: <?php echo $tweet['id_str']; ?></span><br>
    <span>Orig: <?php echo $tweet['text']; ?></span><br>
    <span>Entitied: <?php echo $entified_tweet ?></span><br>
    <small><?php echo $permalink ?><?php if ($is_retweet) : ?>is retweet<?php endif; ?>
    <span>via <?php echo $tweet['source']?></span></small>
  </div>-->
<?php
							endforeach;
					}//code == 200;
					else {
						tmhUtilities::pr($tmhOAuth->response);
					}
				}//for
			}//foreach key
	}
	
	echo "totalcount: ".$totalcount.'</br>';
?>
</body>
</html>