<?php

if(! defined('MINICRAFT')) die;

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Minicraft</title>
		<link rel="shortcut icon" href="favicon.ico">
		<meta http-equiv="content-type" content="text/php; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
		<link rel="stylesheet" type="text/css" href="css/jquery-ui.min.css"/>
		<link rel="stylesheet" type="text/css" href="css/minicraft_common.css"/>
		<link rel="stylesheet" type="text/css" href="css/minicraft_map.css"/>
		<link rel="stylesheet" type="text/css" href="css/minicraft_detailbox.css"/>
		<link rel="stylesheet"  type="text/css"href="css/minicraft_ressourcebox.css"/>
		<script src="js/libs/jquery-2.2.2.min.js" type="text/javascript" ></script>
		<script src="js/libs/jquery-ui.min.js" type="text/javascript" ></script>
		<script src="js/libs/bootstrap.min.js" type="text/javascript" ></script> 
		<script src="js/libs/angular.js" type="text/javascript" ></script>
		<script src="js/libs/xml2json.min.js" type="text/javascript" ></script>
	</head>
	<body>
		<div style="margin-bottom:15px;" id="header">
			<img src="img/gui/minicraft_banner.png"/><br>
			Version <?php echo VERSION; ?>
		</div>