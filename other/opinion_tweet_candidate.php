<?php

/**
 * opinion_tweet_candidate
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * get sentences which contains adjective from Tagtweet,and filter web-address
 * 
 * 21 May 2012
 */

	require_once("../Library/Input/read_file.php");

	ini_set('memory_limit', '256M');     //memory size
  set_time_limit(0);                   //time
   
  $log = './log_opinion_tweet_candidate.txt';
  $fwlog = openFileWrite($log);

  $dirName = './Tagger/Test/';
  $dirList = openDirectory($dirName);
  $i=0;
	
  $adjective = 0;   //indicate whether there is a adjective in the sentence
  $sentence = "";
  $web_address = 0; //indicate whrther there is a web_address in the sentence

  foreach($dirList as $key => $N){
  	
    if($key > 2){

  		$fileName = $dirName.$dirList[$key];
   		$fd = openFileRead($fileName);

      $filenameout = './OpinionTweetsCandidate/'.$dirList[$key];
      $fw = openFileWrite($filenameout);

   		if(!$fd) die('can not open the file');
      //$sentence = array();
      $order = 0;
   		while($str = fgets($fd)){
   			
        //$setence = array();
        //echo $str.'</br>';

        $tmp = explode("\t", $str);


        //echo $tmp[1].'</br>';
        
        if(substr($tmp[0],0,4)=="http"){
            $web_address = 1;
           // echo $tmp[0].'</br>';
            continue;
        }

        if(substr($tmp[0],0,1)=="@"){
         // echo "FUCK";
          //$web_address = 1;
           // echo $tmp[0].'</br>';
            continue;
        }

        if($tmp[0] == "RT"){
          //echo $str.'</br>';
          //$tmp[0]="";
          continue;
          //echo "FUCK</br>";
        } 
        //continue;
        
        if($tmp[1] == "SENT" && $tmp[0] == "."){


          //problem
          $count = 0;
          while($str = fgets($fd)){
            $count++;
            $tmp = explode("\t", $str);
            if($tmp[0] != "."){
               break;
            }
          }

          if($count == 1){
              $sentence = $sentence.$tmp[0]." ";
          }
          else{
              if($adjective == 1 && $web_address != 1){        //if the sentense contains an adjective
               //echo $sentence.'</br>';
                  fwrite($fw,$sentence."\n");
              }
              
              $web_address = 0;
              $adjective = 0;
              $sentence = "";
              //$sentence = "";
          
              if($tmp[0] != "RT" && substr($tmp[0],0,1)!="@"){
                  $sentence = $sentence.$tmp[0]." ";
              }
              else if(substr($tmp[0],0,1)!="@"){   
                  $str = fgets($fd);
                  $str = fgets($fd);
                  $tmp = explode("\t", $str);  
                  //echo $str.'</br>';  
                  if($tmp[0]!=":" && $tmp[0]!="-" && substr($tmp[0],0,1)!="@"){
                     $sentence = $sentence.$tmp[0]." ";
                  }
              }
          }
        }
        else if($tmp[1] == "SENT"){
          $sentence = $sentence.$tmp[0];
        }
        else{

          if($tmp[1] == "JJ" || $tmp[1] == "JJR" || $tmp[1] == "JJS" ){
            $adjective = 1;
            //echo $str.'</br>';
          }
          $sentence = $sentence.$tmp[0]." ";
          //$sentence[$order++] = $tmp[0];
        }

   		}//while
      
      //echo "-----------------------</br>";
      fwrite($fwlog,$dirList[$key]." complete\n");
  	}//if
  }//foreach

  echo "complete</br>";
  
?>