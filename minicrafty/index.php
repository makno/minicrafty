<?php 
/* -----------------------------------
 * - Minicraft                       -
 * - ITM15, SWD15, MaKno (C) in 2016 -
 * -----------------------------------
 */

// Spielkonstante // Game Constant
define('MINICRAFT','');
define('VERSION','V0.1.0alpha');

// Sitzung anlegen/weiterführen // Start session or continue
session_start();

// Konfigurationsdaten inkludieren // Include configuration
include_once("include/configuration.php");
include_once("include/model.php");
include_once("include/view.php");
include_once("include/controller.php");

// Konfigurationsobjekt // Configuration object
$config = new Configuration();

// The one who controls them all!
$controller = new Controller();
$controller->startGame();		

/* Songs listened to while progging:
 * "War rages on" Alex Cage
 * "Run boy run" Woodkind
 * "Radioactive" Imagine Dragons
 * "Love me again" John Newman
 
 * 'May the code be with you!' - MaKno
 * (That's a quote - no song)

 * For all of those who don't speak
 * german: Sorry that most of the
 * comments is in German because this
 * work is done for my students- and
 * they are better off in German in
 * the 1st semester
 */
?>
