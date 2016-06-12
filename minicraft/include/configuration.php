<?php

if(! defined('MINICRAFT')) die;

// Include interface with crucial data like file paths
include_once("include/iconfiguration.php");

// Class implementing interface and its methods
class Configuration implements IConfiguration{
	
	private $height = IConfiguration::MC_MAP_WIDTH;
	private $width = IConfiguration::MC_MAP_HEIGHT;
	
	// Getter & Setter: Height
	function getHeight(){
		return $this->height;
	}
	function setHeight($height){
		$this->height = $height;
	}
	
	// Getter & Setter: Width
	function getWidth(){
		return $this->width;
	}
	
	function setWidth($width){
		$this->width = $width;
	}
	
}

?>