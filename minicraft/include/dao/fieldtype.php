<?php

if(! defined('MINICRAFT')) die;

class Fieldtype{
	public static $xpath;
	private static $list = array();
	private static $listRecourceFields = array();
	private static $listInitialFields = array();
	
	public $id = null;
	public $name = null;
	public $picture = null;
	public $initial = 0;
	public $maximum = 0;
	public $upgrades = array();
	public $resources = array();
	public $costs = array();
	public $xml = null;
	
	function __construct(){
	}
	
	// STATIC
	
	public static function load(){
		$xml = new DOMDocument();
		$xml->load(IConfiguration::MC_LOC_XML_FIELDS, LIBXML_DTDVALID);
		Fieldtype::$xpath = new DOMXpath($xml);
		$fieldsXml = Fieldtype::$xpath->query("//feld");
		// Add fields to list
		foreach($fieldsXml as $fieldXml){
			$field = new Fieldtype();
			$field->id = Fieldtype::$xpath->query("@id",$fieldXml)->item(0)->nodeValue;
			$field->name = Fieldtype::$xpath->query("name", $fieldXml)->item(0)->nodeValue;
			$field->picture = IConfiguration::MC_LOC_IMG_FIELDS."/".Fieldtype::$xpath->query("bild/@datei", $fieldXml)->item(0)->nodeValue;
			if(Fieldtype::$xpath->query("@initial", $fieldXml)->length){
				$field->initial = Fieldtype::$xpath->query("@initial", $fieldXml)->item(0)->nodeValue;
				if($field->initial > 0){
					Fieldtype::$listInitialFields[] = $field;
				}
			}
			if(Fieldtype::$xpath->query("@maximum", $fieldXml)->length)
				$field->maximum = Fieldtype::$xpath->query("@maximum", $fieldXml)->item(0)->nodeValue;
			$field->xml = $fieldXml;
			// Resources
			$resources = Fieldtype::$xpath->query("ressource", $field->xml);
			foreach($resources as $resource){
				$field->resources[] = new Resource(Resourcetype::getList()[$resource->attributes->getNamedItem("id")->nodeValue], $resource->nodeValue);
			}
			if(count($field->resources) > 0){
				Fieldtype::$listRecourceFields[] = $field;
			}
			// Costs
			$costs = Fieldtype::$xpath->query("kosten/ressource", $field->xml);
			foreach($costs as $cost){
				$field->costs[] = new Resource(Resourcetype::getList()[$cost->attributes->getNamedItem("id")->nodeValue], $cost->nodeValue);
			}
			Fieldtype::$list[$field->id]=$field;
		}
		// Upgrades
		foreach(Fieldtype::$list as $field){
			$upgrades = Fieldtype::$xpath->query("upgrade", $field->xml);
			foreach($upgrades as $upgrade){
				$field->upgrades[] = Fieldtype::$list[$upgrade->nodeValue];
			}
		}	
	}
	
	public static function getList(){
		return Fieldtype::$list;
	}
	
	public static function getResourceFields(){
		return Fieldtype::$listRecourceFields;
	}
	
	public static function getInitialFields(){
		return Fieldtype::$listInitialFields;
	}
	
	// OBJECT
	
	public function getId(){
		return $this->id;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getPicture(){
		return $this->picture;
	}
	
	public function getInitial(){
		return $this->initial;
	}
	
	public function getMaximum(){
		return $this->maximum;
	}
	
	public function getUpgrades(){
		return $this->upgrades;
	}
	
	public function getResources(){
		return $this->resources;
	}
	
	public function getCosts(){
		return $this->costs;
	}
	
	public function getXML(){
		return $this->xml;
	}
	
}

?>