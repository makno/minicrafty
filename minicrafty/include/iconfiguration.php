<?php

if(! defined('MINICRAFT')) die;

// Interface for configuration
interface IConfiguration{
	
	const MC_ROUND_TIME = 60; // Sekunden
	
	const MC_MAP_WIDTH = 7;
	const MC_MAP_HEIGHT = 7;
	
	const MC_LOC_XML_FIELDS = "xml/felder.xml";
	const MC_LOC_IMG_FIELDS = "img/fields";
	const MC_LOC_XML_RESOURCES = "xml/ressourcen.xml";
	const MC_LOC_IMG_RESOURCES = "img/resources";
	const MC_LOC_XML_MAPS = "maps";
	
	const MC_MAP_XPATH_ID = "//spieler/id";
	const MC_MAP_XPATH_TIME = "//spieler/zeitstempel";
	const MC_MAP_XPATH_RESSOURCES = "//ressourcen/ressource";
	
	const MC_STATE_NONE = 0;
	const MC_STATE_LOGIN = 1;
	const MC_STATE_MAP = 2;
	const MC_STATE_MAP_FAIL = 3;
	const MC_STATE_MAP_FAIL_TOO_MUCH = 4;
	const MC_STATE_MAP_NEW = 5;
	const MC_STATE_MAP_GENERATE = 6;
	
	const MC_ACTION_LOGOUT = 'logout';
	const MC_ACTION_AJAX = 'ajax';
	const MC_ACTION_MAP_NEW = 'map.new';
	
	const MC_AJAX_METHOD_UPDATE_STATISTICS = 'updateStatistics';
	const MC_AJAX_METHOD_GET_RESOURCES = 'getResourcesPlayer';
	const MC_ACTION_AJAX_METHOD_BUY = 'buyUpgrade';
	const MC_ACTION_AJAX_METHOD_TIME = 'geTime';
}

?>