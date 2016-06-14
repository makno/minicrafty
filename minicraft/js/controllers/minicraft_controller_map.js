/*

	minicraft_controller_map.js

*/

// Controller der Kartenanwendung
app.controller("mcMapCtrl", function($scope,$http, $interval){
	
	// EIGENSCHAFTEN /////////////////////////////////////////////////////////////////////////////////
	
	// Objekt zum Umwandeln von XML in JSON und zurück!
	$scope.x2js = new X2JS();	
	
	// Starting Time
	$scope.timeStart;
	
	// Time in Seconds
	$scope.timeStopped = false;
	$scope.timeRound = 60;
	$scope.timeDelta = 0;
	
	// Interval of overall updates in ms
	$scope.updateInterval = 1000;
	
	// Resources
	$scope.resources = [];
	
	// Fields
	$scope.fieldtypes = [];
	
	// Map
	$scope.map = null;
	
	// Map Init
	$scope.initMap = function(mapurl){
		$scope.getResources();
		$scope.getFieldtypes();	
		$scope.map = new Map(mapurl, $scope.resources, $scope.fieldtypes);
		$scope.getResourcesPlayer();
    };
	
	// Vorheriges Feld
	$scope.fieldSaved = {};
	// Aktuell ausgewähltes Feld
	$scope.fieldCurrent = {};
	
	// INIT METHODE ////////////////////////////////////////////////// wird ganz am Ende ausgeführt !
	
	$scope.initialize = function(){
		// Is done in initMap() !
	}
	
	// UPDATE ////////////////////////////////////////////////////////////////////////////////////
	
	 $interval(function() {
		 	if(!$scope.timeStopped){
			 	if($scope.timeDelta > $scope.timeRound ){
			 		$scope.timeStopped = true;
			 		$scope.timeDelta = 0;
			 		$scope.roundcircle.animate(0); 
			 		$scope.showRoundbutton();
			 	}else{
			 		$scope.timeDelta += 1;
			 		$scope.roundcircle.animate( $scope.timeDelta / $scope.timeRound  ); 
			 	}
		 	}
	      }, $scope.updateInterval);
	

	// EVENTS ////////////////////////////////////////////////////////////////////////////////////

	// Zeit synchronisieren bzw. setzen
	$scope.updateStatistics = function(){
		if($scope.resources){
			$http.get("index.php?action=ajax&method=updateStatistics").then(function(response) {
				if(response.data.success){
					$scope.showRoundcircle();
					$scope.getResourcesPlayer();
					$scope.timeStopped = false;
				}else{
					alert(response.data.message);
					location.reload();
				}
		    });
		}
	};
	
	// Logik um das Klicken auf die Karte!
	$scope.clickMap = function($event){
		$scope.fieldCurrent = {};
		// Position des Mauszeigers auf der Karte bestimmen
		var offsetTop = $event.currentTarget.offsetTop; 
		var offsetLeft = $event.currentTarget.offsetLeft; 
		var offsetWidth = $event.currentTarget.offsetWidth; 
		var relX = Math.round($event.pageX - offsetLeft) - (offsetWidth/2);
		var relY = Math.round($event.pageY - offsetTop)-64;
		// Objekt bestimmen, über dem der Mauszeiger ist
		var coordX = Math.floor((relX / 64 + relY / 32) /2);
		var coordY = Math.floor((relY / 32 -(relX / 64)) /2);
		// Feld bestimmen - über jQuery - wir haben hier ein jQuery Objekt mit spezifischer Funktionalität!
		$scope.fieldCurrent = $scope.map.fields["field"+coordX+''+coordY];
		
		$( "#marker" ).remove();
		// Detailbox aufbauen
		if($scope.fieldCurrent!=undefined){
			$scope.fieldCurrent.element = $("#field"+coordX+coordY); 
			//console.log("Field X=" + coordX + " Y=" + coordY + " clicked! Id:" + $scope.fieldCurrent.id + " Type:" + $scope.fieldCurrent.type.name);
			$scope.fieldCurrent.element.append( '<img id="marker" src="img/gui/feld_markiert.png" style="position: absolute; top: 0px; left: 0px;"/>' );
			// Sichere das aktuelle Feld
			$scope.fieldSaved = $scope.fieldCurrent;
		// Wenn kein Feld unter der Maus ist sollen die Details verschwinden 
		}else{
			// leereDetailbox();
			$scope.fieldCurrent={};
			$scope.fieldSaved.element = undefined;
		}
	};

	 // Upgrade kaufen
	$scope.clickBuy = function(fieldid, upgradeid){
		console.log(fieldid+" "+upgradeid );
		 var data = $.param({
            'upgradeid': upgradeid,
            'fieldid': fieldid
        });
        var config = {
            headers : {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        }
		$http.post("index.php?action=ajax&method=buyUpgrade",data,config).then(function(response) {
			if(response.data.success){
				$scope.getResourcesPlayer();
				$scope.updateField(fieldid, upgradeid);	
			}else{
				alert(response.data.message);
				location.reload();
			}
	    });
	};
	
	$scope.updateField = function(fieldid, upgradeid){
		var field = $scope.map.fields[fieldid];
		for(var i=0;i<$scope.fieldtypes.length;i++){
			if($scope.fieldtypes[i].id==upgradeid){
				field.type = $scope.fieldtypes[i];
				field.element.find("img").eq(0).attr("src",$scope.fieldtypes[i].picture);
			}
		}
	}
	
	// EVENTS ENDE /////////////////////////////////////////////////////////////////////////////
	
	// Prüft, ob der Spieler von einer Resource ausreichend zur Verfügung hat
	$scope.isAffordableResource = function(resource){
		var resources_player = $scope.map.getResources();
		for(var k=0;k<resources_player.length;k++){
			if(resources_player[k].id==resource.id){
				if(parseInt(resource.amount)<=parseInt(resources_player[k].amount)){
					return true;
				}
			}
		}
		return false;
	};
	
	// Prüft, ob sich der Spieler ein Feld leisten kann
	$scope.isAffordableField = function(field){
		var costs = field.getCosts();
		for(var j=0;j<costs.length;j++){
			if(!this.isAffordableResource(costs[j])){
				return false;
			}
		}
		return true;
	};
	
	// Gibt Inhalt eines style Attributs auf Basis eines boolschen parameters zurück
	$scope.getAffordableCostStyle = function(affordable){
		if(affordable)
			return "color:green;";
		else
			return "color:red;";
	};
	
	// Holt aktuelle Spielerressourcen nach $scope.obj_map.getResourcesPlayer()
	$scope.getResourcesPlayer = function(){
		$http.get("index.php?action=ajax&method=getResourcesPlayer").then(function(response) {
			if(response.data.success==true){
				var resp = JSON.parse( response.data.result );
				$scope.map.clearResources();
				
				for(var i=0;i<$scope.resources.length;i++){
					if( resp[$scope.resources[i].id] !== null && typeof resp[$scope.resources[i].id] === 'object'){
						var resourceNew = new Resource($scope.resources[i].xml);
						resourceNew.amount=resp[$scope.resources[i].id].amount;
						$scope.map.addResources(resourceNew);	
					}
				}
			}else{
				$scope.map.clearResources();
				alert(response.data.message);
				location.reload();
			}
	    });
	};
	

	// Ressourcen des Spiels laden (wird in initialize() aufgerufen)
	$scope.getResources = function(){
		var xhttp = new XMLHttpRequest();
		xhttp.open("GET", "xml/ressourcen.xml", false);
		xhttp.send();
		if (xhttp.readyState == 4 && xhttp.status == 200) {
			var ressourcen = xhttp.responseXML.getElementsByTagName("ressource");
			for(var i=0;i<ressourcen.length;i++){
				$scope.resources.push(new Resource(ressourcen[i]));
			}
		}
	};
	
	// Feldarten des Spiels laden (wird in initialize() aufgerufen)
	$scope.getFieldtypes = function(){
		var xhttp = new XMLHttpRequest();
		xhttp.open("GET", "xml/felder.xml", false);
		xhttp.send();
		if (xhttp.readyState == 4 && xhttp.status == 200) {
			var felder = xhttp.responseXML.getElementsByTagName("feld");
			for(var i=0;i<felder.length;i++){
				$scope.fieldtypes.push(new Fieldtype(felder[i],$scope.resources));
			}
		}
	};
	
	$scope.showRoundbutton = function(){
		$("#mcRoundcircle").hide();
		$("#mcRoundbutton").show();
	};
	
	$scope.showRoundcircle = function(){
		$("#mcRoundbutton").hide();
		$("#mcRoundcircle").show();
	};
	

	$scope.roundcircleSettings = {
		  color: '#000',
		  // This has to be the same size as the maximum width to prevent clipping
		  strokeWidth: 10,
		  trailWidth: 10,
		  easing: 'easeInOut',
		  duration: 1000,
		  text: { autoStyleContainer: false },
		  from: { color: '#aaa', width: 1 },
		  to: { color: '#333', width: 4 },
		  // Set default step function for all animate calls
		  step: function(state, circle) {
		    // circle.path.setAttribute('stroke', state.color);
		    //circle.path.setAttribute('stroke-width', state.width);

		    var value = Math.round(circle.value() * $scope.timeRound);
		    if (value === 0) {
		      circle.setText($scope.timeRound+"s");
		    }else if(value >= $scope.timeRound){
		    	circle.setText("0s");
		    } else {
		      circle.setText(($scope.timeRound-value)+"s");
		    }

		  }
		};
	$scope.roundcircle = new ProgressBar.Circle(mcRoundcircle, $scope.roundcircleSettings);
	$scope.roundcircle.text.style.fontFamily = 'Arial, Calibri, sans-serif';
	$scope.roundcircle.text.style.fontSize = '2rem';
	
	// AUSFÜHRUNG BEI START /////////////////////////////////////////////////////////////////////////////////

	$scope.initialize();
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////

	
});