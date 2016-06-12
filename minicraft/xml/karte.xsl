<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:output method="html" encoding="utf-8" indent="yes"/>

	<!-- Root Element -->
	<xsl:template match="/">
		<script src="js/models/minicraft_resources.js" type="text/javascript" defer="defer"/>
		<script src="js/models/minicraft_fields.js" type="text/javascript" defer="defer"/>
		<script src="js/models/minicraft_map.js" type="text/javascript" defer="defer"/>
		<script src="js/libs/progressbar.min.js" type="text/javascript" defer="defer"/>
		
		<!-- <div id="infobox"><b>Karteninfo:</b> <span></span></div> -->
		<div id="mcMapApp" data-ng-app="mcMapApp" data-ng-controller="mcMapCtrl as ctrl" class="wrapper">

			<xsl:attribute name="data-ng-init">initMap('maps/map_<xsl:value-of select="karte/spieler/name/."/>.xml')</xsl:attribute>
		
			<div class="mc_map">
				
				<div class="mc_map_box" data-ng-if="map.getResources()">
					 <!-- AngularJS Implementierung der Spielerressourcen -->	
					<div class="mcPlayerRessources">
						<div class="mcPlayerRessource" data-ng-repeat="resource in map.getResources()" data-ng-if="resource.amount>0">
							<img class="mcPlayerRessourceImage" data-ng-src="{{{{resource.picture}}}}" />
							<div class="mcPlayerRessourceText">{{resource.amount}}</div>
						</div>
						
					</div>
				</div>	
				<h1 class="mcPlayerRessource">Karte</h1>
				<div style="font-size: 20px;font-weight: nromal">
					<div id="mcRoundcircle"></div>
					<!-- Update in {{timeDelta}} Sekunden -->
					<div id="mcRoundbutton"><button data-ng-click="updateStatistics()"><img src="img/gui/round.png"/><br/>Harvest</button></div>
				</div>
				<xsl:apply-templates select="karte"/>
			</div>

			<div class="mc_box" data-ng-if="fieldCurrent.id">
				<h1>Details</h1>
				<div class="mc_box_content">
					<div class="field"> 
						<span><b>{{fieldCurrent.type.name}}</b> (Id:{{fieldCurrent.id}},Type:{{fieldCurrent.type.id}})</span><br />
						<img data-ng-if="fieldCurrent.type.picture" data-ng-src="{{{{fieldCurrent.type.picture}}}}" /><br/><br/>
						<div class="field_text" data-ng-if="fieldCurrent.type.getResources().length"><span data-ng-repeat="res in fieldCurrent.type.getResources()"><img  data-ng-if="res.picture" data-ng-attr-title="{{{{res.name}}}}" data-ng-src="{{{{res.picture}}}}"/>{{res.amount}}</span></div>
					</div>
					<div class="field_options" data-ng-if="fieldCurrent.type.getUpgrades().length">
						<h1>Bauoptionen</h1>
						<div id="mcUpgrades">
							<div class="mcUpgrade" data-ng-repeat="upgrade in fieldCurrent.type.getUpgrades()">
								<span class="mcUpgradeName"><b>{{upgrade.name}}</b> (Id:{{upgrade.id}})</span><br />
								<img class="mcUpgradeImage" data-ng-if="upgrade.picture" data-ng-src="{{{{upgrade.picture}}}}" />
								<div class="mcUpgradeCosts">
									<div class="mcUpgradeRessource" data-ng-repeat="cost in upgrade.getCosts()">
										<img data-ng-if="cost.picture" class="mcUpgradeRessourceImage" width="25px" data-ng-src="{{{{cost.picture}}}}"/><br />
										<span class="mcUpgradeRessourceText" data-ng-attr-style="{{{{getAffordableCostStyle(isAffordableResource(cost))}}}}">{{cost.amount}}</span>
									</div>
									<div data-ng-if="isAffordableField(upgrade)">
										<button data-ng-click="clickBuy(fieldCurrent.id, upgrade.id);"><img src="img/gui/bauen.png"/></button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
		</div>
		<!-- Angular Script --> 
		<script src="js/minicraft_app.js" type="text/javascript" defer="defer"/>
		<script src="js/controllers/minicraft_controller_map.js" type="text/javascript" defer="defer"/>
	</xsl:template>

	<!-- Element: Spielplan "karte" -->
	<xsl:template match="karte">
		<div id="mcMap" data-ng-click="clickMap($event)">
			<xsl:variable name="groesse"><xsl:value-of select="count(reihe)"/></xsl:variable>
			<xsl:attribute name="style">width:<xsl:value-of select="$groesse*128"/>px;height:<xsl:value-of select="$groesse*64+96"/>px;</xsl:attribute>
			<xsl:for-each select="reihe">	
				<xsl:variable name="y"><xsl:value-of select="position()-1"/></xsl:variable>
				<xsl:for-each select="feld">	
					<xsl:variable name="x"><xsl:value-of select="position()-1"/></xsl:variable>
					<xsl:variable name="feld_id"><xsl:value-of select="text()"/></xsl:variable>
					<div class="feld">
						<xsl:attribute name="id">field<xsl:value-of select="$x"/><xsl:value-of select="$y"/></xsl:attribute>
						<xsl:attribute name="data-x"><xsl:value-of select="$x"/></xsl:attribute>
						<xsl:attribute name="data-y"><xsl:value-of select="$y"/></xsl:attribute>
						<xsl:attribute name="data-feld-id"><xsl:value-of select="$feld_id"/></xsl:attribute>
						<xsl:attribute name="data-name"><xsl:value-of select="document('felder.xml')/felder/feld[@id=$feld_id]/name"/></xsl:attribute>
						<xsl:attribute name="style">left:<xsl:value-of select="(($groesse - 1) * 64) + 64 * ($x - $y)"/>px;top:<xsl:value-of select="32 * ($x + $y)"/>px;</xsl:attribute>
							<img style="position: relative; top: 0; left: 0;">
								<xsl:attribute name="src"><xsl:value-of select="document('felder.xml')/felder/feld[@id=$feld_id]/bild/@datei"/></xsl:attribute>
							</img>
					</div>
				</xsl:for-each>
			</xsl:for-each>
		</div>		
	</xsl:template>
	
</xsl:stylesheet>
