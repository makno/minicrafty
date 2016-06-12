<?php

if(! defined('MINICRAFT')) die;

class Field{
	
	public $type = null;
	public $x = null;
	public $y = null;
	
	function __construct($type,$x,$y){
		$this->type = $type;
		$this->x = $x;
		$this->y = $y;
	}
		
	public function getId(){
		return "field.".$this->x.$this->y;
	}
	
	public function getType(){
		return $this->type;
	}
	
	public function getX(){
		return $this->x;
	}
	
	public function getY(){
		return $this->y;
	}
	
	
}

?>