<?php
/**
 * class_data_set
 * copyright (C) 2012 - 2013  @author liang - liang0816tw@gmail.com
 * written in PHP.
 * 
 * get the content of data from database
 * cross vaildation
 *
 * 
 * 26 Jun 2013
 **/

	class dataSet {

    private $data;
    private $training_data;
    private $testing_data;
    private $category;


    public function __construct($category){
      //initial
      $this->data = array();
      $this->category = $category;
    }

    public function getData(){
      
      include("Database/mysql_connect.inc.php");

      //initail
      $count=0;

      //connect to database(testing)
      $db_testing = new DB;
      $db_testing->connect_db(DB_SERVER, DB_USER, DB_PWD , "testing");

      for($i=0;$i<count($this->category);$i++){

        $category_name = $this->category[$i];

        $sql = "SELECT original_tweet ,content , opinion, emotion_meaning FROM {$category_name}";
        $db_testing->query($sql);

        while($str = $db_testing->fetch_array()){
            //$this->data[$count]['original_tweet'] = $str['original_tweet'];
            $this->data[$count]['content'] = $str['content'];
            $this->data[$count]['opinion'] = $str['opinion'];
            $this->data[$count]['category'] = $category_name;
            $this->data[$count]['emotion_meaning'] = $str['emotion_meaning'];
            $count++;
        }
      }

      //connect to database(training)
      $db_training = new DB;    
      $db_training->connect_db(DB_SERVER, DB_USER, DB_PWD , "training");


      for($i=0;$i<count($this->category);$i++){

        $category_name = $this->category[$i];

        $sql = "SELECT original_tweet ,content , opinion, emotion_meaning FROM {$category_name}";
        $db_testing->query($sql);

        while($str = $db_testing->fetch_array()){
            //$this->data[$count]['original_tweet'] = $str['original_tweet'];
            $this->data[$count]['content'] = $str['content'];
            $this->data[$count]['opinion'] = $str['opinion'];
            $this->data[$count]['category'] = $category_name;
            //$this->data[$count]['emotion_meaning'] = $str['emotion_meaning'];
            $count++;
        }
      } 

      //var_dump($this->data);
      $this->informationOfData();
      return $this->data;
    }
    private function informationOfData(){
      echo '--- Information of data set ---</br></br>';
      echo 'total of data: '.count($this->data).'</br>';
      echo '</br>--- End information of testing data ---</br></br>';
    }

  
  }
	
?>