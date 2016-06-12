<?php

if(! defined('MINICRAFT')) die;

class Resourcetype{
	public static $xpath;
	private static $list;
	private static $listInitialResources = array();
	
	public $id = null;
	public $name = null;
	public $picture = null;
	public $initial = null;
	public $xml = null;
	
	function __construct(){
	}
	
	// STATIC
	
	public static function load(){
		$xml = new DOMDocument();
		$xml->load(IConfiguration::MC_LOC_XML_RESOURCES, LIBXML_DTDVALID);
		Resourcetype::$xpath = new DOMXpath($xml);
		// Resources
		$resourcesXml = Resourcetype::$xpath->query("//ressource");
		foreach($resourcesXml as $resourceXml){
			$resource = new Resourcetype();
			$resource->id = Resourcetype::$xpath->query("@id",$resourceXml)->item(0)->nodeValue;
			$resource->name = Resourcetype::$xpath->query("name", $resourceXml)->item(0)->nodeValue;
			$resource->picture = IConfiguration::MC_LOC_IMG_RESOURCES."/".Resourcetype::$xpath->query("bild/@datei", $resourceXml)->item(0)->nodeValue;
			if(Resourcetype::$xpath->query("@initial", $resourceXml)->length)
				$resource->initial = Resourcetype::$xpath->query("@initial", $resourceXml)->item(0)->nodeValue;
			if($resource->initial > 0){
				Resourcetype::$listInitialResources[] = $resource;
			}
			Resourcetype::$list[$resource->id]=$resource;
		}
	}
	
	public static function getList(){
		return Resourcetype::$list;	
	}
	
	public static function getInitialElements(){
		return Resourcetype::$xml->query("//ressource[@initial]");
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
	
	public function getXml(){
		return $this->xml;
	}
	
}

?>