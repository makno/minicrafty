<?php

if(! defined('MINICRAFT')) die;

class Resource{
	
	public $type = null;
	public $amount = null;
	
	function __construct($type, $amount){
		$this->type = $type;
		$this->amount = $amount;
	}
	
	// OBJECT
	
	public function getType(){
		return $this->type;
	}
	
	public function getAmount(){
		return $this->amount;
	}
	
}

?>