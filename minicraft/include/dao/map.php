<?php

if(! defined('MINICRAFT')) die;

class Map{
	
	private $model;
	private $dom;
	private $xpath;
	
	public $name;
	public $filename;
	public $id;
	
	public $resources = array();
	public $fields = array();
	
	private $hasmapfile = false;
	private $isauthenticated = false;

	function __construct($model, $name=null, $id=null ){
		$this->model = $model;
		$this->name = $name;
		$this->id = $id;
		$this->filename = "map_".$name.".xml";
		if($this->name!=null && $this->id!=null)
			$this->checkMapFile();
	}
	
	public static function loadFile($file,$model){
		$map = new Map($model);
		$map->filename = $file;
		$map->hasmapfile = true;
		$map->load();
		return $map;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function getMapFileName(){
		return $this->filename;
	}
	
	public function hasMapfile(){
		return $this->hasmapfile;
	}
	
	public function isAuthenticated(){
		return $this->isauthenticated;
	}
	
	public function getDom(){
		return $this->dom;
	}
	
	public function getResources(){
		return $this->resources;
	}
	
	public function load(){
		$this->dom = new DOMDocument;
		$this->dom->load(IConfiguration::MC_LOC_XML_MAPS . "/".$this->getMapFileName());
		// FIELDS
		$rows = $this->getXPath()->query("//reihe");
		for($i=0;$i<$rows->length;$i++){
			$fields = $this->getXPath()->query("feld", $rows->item($i));
			for($j=0;$j<$fields->length;$j++){
				$this->fields[('field'.$j.$i)]= new Field(Fieldtype::getList()[$fields->item($j)->nodeValue],$j,$i);
			}
		}
		// RESOURCES
		$resources = $this->getXPath()->query(IConfiguration::MC_MAP_XPATH_RESSOURCES);
		foreach($resources as $resource){
			$this->resources[(int)$resource->getAttribute("id")] =  new Resource(Resourcetype::getList()[$resource->getAttribute("id")],(int)$resource->nodeValue);
		}
	}
	
	public function save(){
		$this->dom->save(IConfiguration::MC_LOC_XML_MAPS . "/" . $this->getMapFileName(), LIBXML_DTDVALID);
	}
	
	public function update(){
		$timeXML = $this->getTime();
		$timeCurrent = time();
		if($timeCurrent>=$timeXML+IConfiguration::MC_ROUND_TIME && $this->dom){
			// Update time
			$this->setTime($timeCurrent);
			
			// Update resources
			$resources = $this->getResources();
			$resourceFields = Fieldtype::getResourceFields(); 
			foreach($this->fields as $field){
				foreach($field->type->resources as $resource){
					$isCalculated = false;
					foreach($resources as $resource_player){
						if($resource->type->id==$resource_player->type->id){
							$resource_player->amount += $resource->amount;
							$isCalculated = true;
						}
					}
					if(!$isCalculated){
						$resources[] = $resource;
					}
				}
			}
			$this->setResources($resources);
			return true;
		}
		return false;	
	}
	
	public function setResources($resources){
		$ressourcen = $this->getXPath()->query("//ressourcen")->item(0);
		$ressourcenNew =  $this->dom->createElement("ressourcen");
		foreach($resources as $key => $value){
			$ressource = $this->dom->createElement("ressource",$value->amount);
			$ressource->setAttribute("id", $value->type->id);
			$ressourcenNew->appendChild($ressource);
		}
		$ressourcen->parentNode->replaceChild($ressourcenNew, $ressourcen);
		$this->save();
	}
	
	public function generate(){
		global $config;
		// Eine Instanz der DOMImplementation Klasse
		$imp = new DOMImplementation;
		// Dokumententyp erstellen
		$dtd = $imp->createDocumentType('karte', '', 'xml/karte.dtd');
		// DOM Dokument selbst mittels DTD erstellen
		$this->dom = $imp->createDocument("", "", $dtd);
		// Eigenschaften, wei Zeichenckodierung einstellen
		$this->dom->encoding = 'UTF-8';
		$this->dom->standalone = false;
		$this->dom->preserveWhiteSpace = true;
		$this->dom->formatOutput = true;
		$elementMap = $this->dom->createElement("karte");
		
		// Spieler
		$spieler = $this->dom->createElement("spieler");
		$spieler->appendChild($this->dom->createElement("name",$this->getName()));
		$spieler->appendChild($this->dom->createElement("id",$this->getId()));
		$spieler->appendChild($this->dom->createElement("zeitstempel",time()));
		$elementMap->appendChild($spieler);
		
		// Ressourcen
		$ressourcen = $this->dom->createElement("ressourcen");
		
		// Alle Element, deren Attribut initial ist holen
		$elementsInitial = Resourcetype::getInitialElements();
		// Elemente durchgehen und einen neuen Array erstellen mit id => anzahl, $arrayChecker wir implizit erstellt!
		// (Anmerkung: Feld Id und wie oft das Feld maximal in der Karte vorkommmen darf ...)
		foreach($elementsInitial as $element){
			$id = $element->getAttribute('id');
			$anzahl = (int) $element->getAttribute('initial');
			$ressource = $this->dom->createElement("ressource",$anzahl);
			$ressource->setAttribute("id", $id);
			$ressourcen->appendChild($ressource);
		}
		$elementMap->appendChild($ressourcen);
		
		// Alle Element, deren Attribut initial ist holen
		$elementsInitial = Fieldtype::getInitialFields();
		// Elemente durchgehen und einen neuen Array erstellen mit id => anzahl, $arrayChecker wir implizit erstellt!
		// (Anmerkung: Feld Id und wie oft das Feld maximal in der Karte vorkommmen darf ...)
		$arrayBaseFields = array();
		foreach($elementsInitial as $element){
			$id = $element->getId();
			$anzahl = (int) $element->getInitial();
			if($anzahl==0)
				$arrayBaseFields[$id] = $anzahl;
			else
				$arraySpecialFields[$id] = $anzahl;
		}
		// Speichern der Basisfelder in jedes einzelne Element
		$number_of_fields = $config->getHeight() * $config->getWidth();
		for($i=0;$i<$number_of_fields;$i++){
			$rand = array_rand($arrayBaseFields);
			$fields[$i] = $this->dom->createElement("feld", $rand);
		}
		$countSpecialFields = 0;
		foreach($arraySpecialFields as $keyField => $anzahl)
			$countSpecialFields += $anzahl;
			// Zuweisung der einzelnen Spezialfelder per Zufall ...
			$arrayFieldSelection = array_rand($fields, $countSpecialFields);
			foreach($arrayFieldSelection as $key){
				foreach($arraySpecialFields as $keyField => $anzahl){
					if($anzahl>0){
						$fields[$key]->nodeValue = $keyField;
						$arraySpecialFields[$keyField]--;
						break;
					}
				}
			}
			// Reihen und Felder durchgehen - Anzahl der Elemente kommt aus configfuration.php
			for($i=0;$i<$config->getHeight();$i++){
				$elementRow = $this->dom->createElement("reihe");
				for($j=0;$j<$config->getWidth();$j++){
					$elementRow->appendChild($fields[$i*$config->getWidth()+$j]);
				}
				$elementMap->appendChild($elementRow);
			}
			$this->dom->appendChild($elementMap);
			$this->save();
	}
	
	private function checkMapFile(){
		$dirMaps = IConfiguration::MC_LOC_XML_MAPS;
		$mapfiles= scandir($dirMaps);
		if($mapfiles!=FALSE){
			foreach($mapfiles as $mapfile){
				$pos = strpos($mapfile, $this->getMapFileName());
				if($pos!==false && !is_dir($mapfile)){
					$this->hasmapfile = true;
					$this->load();
					$this->isauthenticated = $this->getId() == $this->getXPath()->query(IConfiguration::MC_MAP_XPATH_ID)->item(0)->nodeValue;
					if($this->isauthenticated)
						return true;
				}
			}
		}
		return false;
	}
	
	public function getXPath(){
		if($this->xpath==null){
			$this->xpath = new DOMXpath($this->dom);
		}
		return $this->xpath;
	}
	
	public function getTime(){
		return $this->getXPath()->query(IConfiguration::MC_MAP_XPATH_TIME)->item(0)->nodeValue;
	}
	
	private function setTime($time){
		$this->getXPath()->query(IConfiguration::MC_MAP_XPATH_TIME)->item(0)->nodeValue = $time;
	}
}


?>