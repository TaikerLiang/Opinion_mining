<?php

/**
 * preprocessing
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * Filter RT,
 * replace username with USER
 * discard the tweet which contains web address
 * If number of words in tweet < 5,then discard it.
 * 
 * 15 Aug 2012
 */

require_once("./function_of_preprocessing.php");

function preprocessing($emotions_dictionary,$rule,$stop_word,$newdata,$category,$db_slang,$mobile,$camera){	

  for($i=0;$i<count($category);$i++){
    
    for($j=0;$j<count($newdata[$category[$i]]);$j++){
      //echo $newdata[$category[$i]][$j]['content'].'</br>';
      $str = $newdata[$category[$i]][$j]['original_tweet'];
      $str = trim($str);

      $totalString="";
      $string="";
      $emotions = "";
      $emotions_meaning = "";
      $tag = "";
      //echo $str.'</br>';
      $tmp = explode(" ", $str);
     

      for($k=0;$k<count($tmp);$k++){
        //echo $tmp[$k].'</br>';
        if(strtolower($tmp[$k]) == "no" || strtolower($tmp[$k]) == "not"){
          
          $com = $tmp[$k]." ".$tmp[$k+1];
          $tmp[$k+1] = $tmp[$k]."+".$tmp[$k+1];
          $str = str_replace($com, $tmp[$k+1], $str);
          continue;
        }
       
        //replace user name
        $str = replace_username($str,trim($tmp[$k]));
        //remove some special character
        $str = remove_Special_character($str,trim($tmp[$k]));
        //extract all of emotions
        $str = extract_emotions($str,&$emotions_dictionary,trim($tmp[$k]),&$emotions,&$emotions_meaning);
        //extract tag
        $str = extract_tag($str,trim($tmp[$k]),&$tag);
      }
      //echo $str.'</br>';
      
      $str = remove_character($str);
      $output = POS_tagger($str);
      $postag = explode("\n", $output);

      //translate the term into format (Word, POS, English_or_not, Stop_word)
      for($k=0;$k<count($postag);$k++){
        if(trim($postag[$k]==NULL))
          continue;

         
        $result['Word'] = "";
        $result['POS'] = "";
        $result['English'] = "";
        $result['Stop_word'] = "";
      
        tagging($postag[$k],&$result);
        

        //var_dump($result);
        /**
        English or not
        **/
        //determine the word is stop word or not
        if(determine_stop_word($result['Word'],&$stop_word)){
          //if true then it's stop word & English word
          $result['Stop_word'] = "ST";
          $result['English'] = "EN";
          //echo $tmp[$i].'</br>';
        }
        else{   
          $result['Stop_word'] = "NST";
          //check if a word is an English word 0:no, 1;yes
          if(check_word_english($result['Word']))
            $result['English'] = "EN";  //yes
          else
            $result['English'] = "NEN"; //no
        }

        /**
        repeated letter
        **/
        if($result['English'] == 'NEN'){
          $letterOrder = check_rule($result['Word']);

          if($rule[strtolower($letterOrder)]!=null){
          
            $result['English'] = "EN";
            $result['Word'] = $rule[$letterOrder];
            if(determine_stop_word($result['Word'],&$stop_word)){
              $result['Stop_word'] = "ST";
            }
            else{
              $result['Stop_word'] = "NST";
            }
            //echo $rule[$letterOrder].'</br>';
          }
          else{
            repeated_letter($result['Word']);
          }
        }

        /**
        special word
        **/

        if($result['English'] == 'NEN'){

          if($mobile[strtolower($result['Word'])]!=NULL || $camera[strtolower($result['Word'])]!=NULL){
            //check
            $result['English'] = "SPE";
            $result['Stop_word'] = "NST";
          }
        }

        /**
        slang
        **/
        //process slang & repeated letter
        if($result['English'] == "NEN"){
         
          process_slang(&$result,$db_slang);
          $string = "(".$result['Word'].",".$result["POS"].",".$result['English'].",".$result['Stop_word'].")";
          //echo $string.'</br>';
        }

        $string = "(".$result['Word'].",".$result["POS"].",".$result['English'].",".$result['Stop_word'].")";
        $totalString = $totalString." ".$string;
        //echo $string.'</br>';
      }
      //for test 
      /*echo $newdata[$category[$i]][$j]['ID'].'</br>';
      echo $totalString.'</br>';
      echo $emotions." ".$emotions_meaning.'</br>';
      echo $tag.'</br>';
      echo '------</br>';*/


      $totalString = str_replace("'","''", $totalString);
      $emotions = str_replace("'","''", $emotions);   
      $tag = str_replace("'","''", $tag);
      
      $newdata[$category[$i]][$j]['content'] = $totalString;
      $newdata[$category[$i]][$j]['emotions'] = $emotions;
      $newdata[$category[$i]][$j]['emotions_meaning'] = $emotions_meaning;
      $newdata[$category[$i]][$j]['tag'] = $tag;  
    }
  }
	  echo "preprocessing complete</br>";
    return $newdata;
}

?>