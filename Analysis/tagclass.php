<?php

class Adjective{
	
	Public $word;
	Public $tag;
	Public $count;
	
	public function __constrcut(){
		$this->count = 0;
	}

	public function add(){
		$this->count++;
	}
	public function setWord($word){
		$this->word = $word;
	}
	public function getWord(){
		return $this->word;
	}
	public function setTag($tag){
		$this->tag = $tag;
	}
	public function getTag(){
		return $this->tag;
	}
	public function setCount(){
		$this->count = 1;
	}
	public function getCount(){
		return $this->count;
	}
	
}

?>