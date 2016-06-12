<?php

if(! defined('MINICRAFT')) die;

include_once("dao/fieldtype.php");
include_once("dao/field.php");
include_once("dao/resourcetype.php");
include_once("dao/resource.php");
include_once("dao/map.php");

class Model{
	
	private $fieldtypes;
	private $resourcetypes;
	private $map;

	function __construct($name, $id){
		Resourcetype::load();
		Fieldtype::load();
		$this->map = new Map($this, $name, $id);
	}
		
	public function getFieldtypes(){
		return Fieldtype::getList();
	}	
	
	public function getResourcetypes(){
		return Resourcetype::getList();
	}
	
	public function getMap(){
		return $this->map;
	}
	
	public function upgrade($fieldId, $upgradeId){
		$field = $this->getMap()->fields[$fieldId];
		if(!empty($field)){
			$upgrades = $field->type->getUpgrades();
			$resources = array();
			foreach ($this->getMap()->getResources() as $k => $v) {
				$resources[$k] = clone $v;
			}
			foreach($upgrades as $upgrade){
				if($upgrade->id==$upgradeId){
					foreach($upgrade->getCosts() as $cost){
						$paid = false;
						foreach($resources as $resource){
							if($cost->type->id==$resource->type->id){
								if($cost->amount<=$resource->amount){
									$resource->amount -= $cost->amount;
									$paid = true;
								}
							}
						}
						if(!$paid)
							return false;
					}
					$field->type = $upgrade;
					$fieldXml = $this->getMap()->getXPath()->query("//reihe[".($field->getY()+1)."]/feld[".($field->getX()+1)."]")->item(0);
					$fieldXml->nodeValue = $upgrade->id;
					$this->getMap()->setResources($resources);
					return true;
				}
			}
		}
		return false;
	}
	
}



?>