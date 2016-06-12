<?php

if(! defined('MINICRAFT')) die;

class Controller{
	
	// Session
	private $login;
	// Wie oft ein Benutzer versucht hat sich anzumelden
	private $login_count;
	// Zustand der Webanwendung
	private $state = IConfiguration::MC_STATE_NONE;
	// Aktuelle Aktion eines Benutzers
	private $action;
	// Daten, die über Ajax übergeben wurden
	private $data;
	
	// Anmeldename
	private $name;
	// Anmeldekennwort
	private $password;
	
	// Datenmodel mit Feldern, Ressourcen und der Karte
	private $model;
	// Ansicht der Dateien - Anzeige 
	private $view;
		
	// Konstruktor
	function __construct(){
		$this->checkPOST();
		$this->checkGET();
		$this->checkSession();
		$this->model = new Model($this->name,$this->password);
		$this->view = new View($this->model);	
	}
	
	///////////////////
	// Hauptfunktion //
	///////////////////
	public function startGame(){
		
		// Setzt Status auf Basis einer Aktion
		switch($this->action){
			// Generieren einer neuen karte
			case IConfiguration::MC_ACTION_MAP_NEW:
				$this->state = IConfiguration::MC_STATE_MAP_GENERATE;
			break;
			
			default:
		}
		
		// Führt statusgemäß Operationen durch ...
		switch($this->state){		
			
			// ----> Anmeldung noch nicht erfolgt
			/////////////////////////////////////
			case IConfiguration::MC_STATE_NONE:
				// Initiale Logik zum Anmelden
				if( $this->login == null){
					$this->view->showHTML("showLogin");
				}
				break;
			
			// ----> Anmeldung erfolgt - automatisches Neuladen durch checkPOST !
			/////////////////////////////////////////////////////////////////////
			case IConfiguration::MC_STATE_LOGIN:
				// Benutzer hat Karte und das Kennwort passt!
				if($this->model->getMap()->isAuthenticated()){
					$this->setState(IConfiguration::MC_STATE_MAP);
				// Benutzer hat Karte, aber das Kennwort hat nicht gepasst...
				}elseif($this->model->getMap()->hasMapfile()){
					$_SESSION['login_count'] += 1;
					if($_SESSION['login_count']>2){
						$this->setState(IConfiguration::MC_STATE_MAP_FAIL_TOO_MUCH);
					}else{
						$this->setState(IConfiguration::MC_STATE_MAP_FAIL);
					}
				// Benutzer hat noch keine Karte
				}else{
					$this->setState(IConfiguration::MC_STATE_MAP_NEW);			
				}
				// Auf diese Seite nochmal verweisen - ohne POST Paramter,
				// sodass ein Aktualisieren der Seite nicht wieder die
				// Bearbeitung des POST auslöst!
				header("Location: " . $_SERVER['REQUEST_URI']);
				break;	
				
			// ----> Anzeige der Karte und eventuell Anfragen über AJAX
			///////////////////////////////////////////////////////////
			case IConfiguration::MC_STATE_MAP:
				$this->update();
				if($this->action==IConfiguration::MC_ACTION_AJAX)
					$this->handleAjax();
				else
					$this->view->showHTML("showMap");
				break;
				
			// ----> Frage zum Generieren einer neuen Karte 
			///////////////////////////////////////////////
			case IConfiguration::MC_STATE_MAP_NEW:
				$this->view->showHTML("showMapNew");
				break;
				
			// ----> Generieren einer neuen Karte
			/////////////////////////////////////
			case IConfiguration::MC_STATE_MAP_GENERATE:
				$this->model->getMap()->generate();
				$this->view->showHTML("showMap");
				break;
				
			// ----> Fehlerfall - Falsches Kennwort
			////////////////////////////////////////
			case IConfiguration::MC_STATE_MAP_FAIL:
				$this->view->showHTML("showMapFail");
				break;
				
			// ----> Fehlerfall - Zo oft falsches Kennwort angegeben
			////////////////////////////////////////////////////////
			case IConfiguration::MC_STATE_MAP_FAIL_TOO_MUCH:
				$this->view->showHTML("showMapFailTooMuch");
				break;
				
			//  ----> Standard: Anmeldeformular
			///////////////////////////////////
			default:
				unset($_SESSION);
				$this->view->showHTML("showLogin");
		}
	}
	
	////////////////////////////////////////// AJAX ANFANG /////////
	
	// Bearbeitet Aktionen - API
	private function handleAjax(){
		$ret = array('success'=>false);
		if(isset($_GET['method'])){
			switch($_GET['method']){
				
				// ----> Runde beenden
				case IConfiguration::MC_AJAX_METHOD_UPDATE_STATISTICS:
					$updateOK = $this->getModel()->getMap()->update(true);
					$ret['success'] = $updateOK;
					break;
				// ----> Resourcen des Spieleres bereitstellen	
				case IConfiguration::MC_AJAX_METHOD_GET_RESOURCES:
					$ret['success'] = true;
					$ret['result'] =  json_encode($this->getModel()->getMap()->getResources());
					break;
				//  ----> Ein Upgrade kaufen	
				case IConfiguration::MC_ACTION_AJAX_METHOD_BUY:
					$ret['success'] = $this->buyUpgrade();
					break;
				//  ----> Zeitabfrage
				case IConfiguration::MC_ACTION_AJAX_METHOD_TIME:
					$ret['success'] = true;
					$ret['result'] = $this->getModel()->getMap()->setTime();
					break;
				default:
			}
		}
		// Im HTTP header JSOn einstellen
		header('Content-Type: application/json');
		// Array als JSON ausgeben (keine View notwendig)
		echo json_encode($ret);
	}
	
	////////////////////////////////////////// AJAX METHODEN ////////
	
	// Upgrade kaufen
	private function buyUpgrade(){
		$fieldid = $_POST["fieldid"];
		$upgradeid = $_POST["upgradeid"];
		return $this->getModel()->upgrade($fieldid,$upgradeid);
	}
	
	
	////////////////////////////////////////// AJAX ENDE ////////////
	
	
	/////////////////////////////////////////// UPDATE /////////////
	
	private function update($forcenextround=false){
		$dirMaps = IConfiguration::MC_LOC_XML_MAPS;
		$mapfiles= scandir($dirMaps);
		if($mapfiles!=FALSE){
			foreach($mapfiles as $mapfile){
				$file_parts = pathinfo($mapfile);
				if($file_parts['extension']=="xml")
					Map::loadFile($mapfile,$this->getModel())->update();
			}
		}	
	}
	
	
	/////////////////////////////////////////// UPDATE ENDE ////////
	
	// POST Parameter auslesen -> Benutzer und AJAX Daten
	private function checkPOST(){
		if( isset($_POST['user']) ){
			$this->name = htmlspecialchars($_POST['user']);
			$pwd = $_POST['password'];
			$this->password = crypt($pwd,'$5$saltycrackers!!!'); //SHA512
			// Sessionvariable 'loggedin' und andere werden gesetzt!
			if(!isset($_SESSION['login'])){
				$_SESSION['login'] = true;
				$_SESSION['login_count'] = 0;
			}
			$_SESSION['name']= $this->name;
			$_SESSION['password']= $this->password;
			$_SESSION['state']= IConfiguration::MC_STATE_LOGIN;
		}
		if( isset($_POST['data']) ){
			$this->data = $_POST['data'];
		}
	}
	
	// GET Parameter auslesen -> Aktionen: action
	private function checkGET(){
		$this->action = "";
		if(isset($_GET['action'])){
			$this->action = $_GET['action'];
			// Logout
			if($this->action==IConfiguration::MC_ACTION_LOGOUT){
				$_SESSION['login'] = false;
				$this->killSession();
			}
		}
	}
	
	// SESSION Daten überprüfen
	private function checkSession(){
		if(isset($_SESSION['login']) && $_SESSION['login']==true){
			$this->login = $_SESSION['login'];
			$this->login_count = $_SESSION['login_count'];
			$this->state = $_SESSION['state'];
			$this->name = $_SESSION['name'];
			$this->password = $_SESSION['password'];
		}
	}
	
	// Setzt den Status in der Session
	private function setState($state){
		$_SESSION['state'] = $state;
	}
	
	// Falls die Session gelöscht werden soll, löschen Sie auch das Session-Cookie.
	private function killSession(){
		// Achtung: Damit wird die Session gelöscht, nicht nur die Session-Daten!
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000, $params["path"],
					$params["domain"], $params["secure"], $params["httponly"]
					);
		}
		// Zum Schluß, löschen der Session.
		session_destroy();
	}
	
	public function getModel(){
		return $this->model;
	}
	
}


?>