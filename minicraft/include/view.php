<?php

if(! defined('MINICRAFT')) die;

class View{
	
	private $model;
	
	function __construct($model){
		$this->model = $model;
	}
	
	// Funktion zum Aufruf von Seiten mittels Funktion in Funktion
	public function showHTML($function){
		// Kopf der Seite
		include("include/html/header.php");
		// Function - Wieso so lösen - weil's geht ;-)
		$this->$function();
		// Fuß der Seite
		include("include/html/footer.php");
	}
	
	private function showMap(){	
		?>
			Willkommen zu Minicraft, <b><?=ucfirst($_SESSION['name'])?></b>! Vom Spiel <a href="index.php?action=logout">abmelden</a>.
			<br>
		<?php 
		// Lade XSL in DOMDocument
		$xsl = new DOMDocument;
		$xsl->load('xml/karte.xsl');
		// Transformator initialisieren
		// http://php.net/manual/de/class.xsltprocessor.php
		$proc = new XSLTProcessor;
		// Import von XSL in den Transformator
		$proc->importStyleSheet($xsl);
		// Ausgabe als "XML" - eigentlich HTML
		// echo "<input id=\"mcMapName\" type=\"hidden\" value=\"".IConfiguration::MC_LOC_XML_MAPS . "/".$this->model->getMap()->getMapFileName()."\"/>";
		echo $proc->transformToXML($this->model->getMap()->getDom());
	}
	
	private function showMapNew(){
		?>
			<div>
				Unter dem Namen <?=$_SESSION['name']?> wurde noch keine Karte gefunden!<br>
				Willst Du eine neue Karte generieren?<br>
				<a href="?action=<?=IConfiguration::MC_ACTION_MAP_NEW?>"><img src="img/gui/icon_yes.png"/></a> <a href="?action=<?=IConfiguration::MC_ACTION_LOGOUT?>"><img src="img/gui/icon_no.png"/></a>
			</div>
		<?php 	
	}
	
	private function showMapFail(){
		?>
			<div>
				Du hast <b><?=$_SESSION['login_count']?>x ein falsches Kennwort</b> für den Benutzer <?=$_SESSION['name']?> angegeben!<br>
				Versuch es noch einmal! Du hast insgesamt 3 Versuche!<br>
				<?php $this->showLogin(); ?>
			</div>			
		<?php 
	}
	
	private function showLogin(){
		?>
			<h2>Anmeldung</h2>
			<div>
				<form action="?" method="POST">
					<input name="user" type="text" placeholder="username" value="<?=(isset($_SESSION['name'])?$_SESSION['name']:"")?>">
					<input name="password" type="password" placeholder="password">
					<button type="submit"><img src="img/gui/icon_yes_small.png"/></button>
				</form>
			</div>
		<?php 
	}
	
	private function showMapFailTooMuch(){
		?>
			<h2>Fail!</h2>
			<div>
				Zu viele Versuche!<br>
				Wende Dich an den Serveradmin, wenn Du Dein Kennwort zurücksetzen willst!<br>
				<a href="index.php?action=logout"><button>Session zurücksetzen</button></a>
			</div>
		<?php 
	}
}
?>