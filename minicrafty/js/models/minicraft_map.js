/*
	MAP
*/
function Map(path,resources,fields){
	this.path = path;
	this.resourcetypes = resources;
	this.resources = [];
	this.fieldtypes = fields;
	if(this.load()){
		this.isInitialized = true;
	}
}
// Main class members
Map.p = Map.prototype; // Short cut to prototype
Map.p.constructor = Map; // Short cut to access class
// Object settings
Map.p.fieldtypes = null;
Map.p.path = null;
Map.p.xml = null;
Map.p.resourcetypes = [];
Map.p.resources = [];
Map.p.fieldtypes = [];
Map.p.fields = [];
Map.p.isInitialized = false;

//Methods
Map.p.load = function (){
	var xhttp = new XMLHttpRequest();
	xhttp.open("GET", this.path, false);
	xhttp.send();
	if (xhttp.readyState == 4 && xhttp.status == 200) {
		this.xml = xhttp.responseXML;
		var ressourcen = this.xml.getElementsByTagName("ressource");
		for(var i=0;i<ressourcen.length;i++){
			var resourceNew = new Resource(ressourcen[i]);
			resourceNew.amount=ressourcen[i].nodeValue;
			this.resources[ressourcen[i].attributes.getNamedItem("id").nodeValue] = resourceNew;
		}
		var rows = this.xml.getElementsByTagName("reihe");
		for(var i=0;i<rows.length;i++){
			var fields = rows[i].children;
			for(var j=0;j<fields.length;j++){
				for(var k=0;k<this.fieldtypes.length;k++){
					if(this.fieldtypes[k].id==fields[j].innerHTML){
						this.fields[('field'+j+''+i)] = new Field(('field'+j+''+i), fields[j], this, this.fieldtypes[k]);
					}
				}
			}
		}
		return true;
	}
	return false;
};
Map.p.clearResources = function (){
	this.resources = [];
};
Map.p.getResources = function (){
	return this.resources;
};
Map.p.setResources = function (resources){
	this.resources = resources;
};
Map.p.addResources = function (resource){
	this.resources.push(resource);
};
Map.p.getPath = function(){
	return this.path;
};


