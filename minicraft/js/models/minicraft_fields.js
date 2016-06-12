/*

FIELDTYPE

*/

function Fieldtype(xml, resources){
	try{
		this.id = xml.attributes.getNamedItem("id").nodeValue;
		this.name = xml.getElementsByTagName("name")[0].innerHTML;
		this.picture = xml.getElementsByTagName("bild")[0].attributes.getNamedItem("datei").nodeValue;
		if(xml.attributes.getNamedItem("initial"))
			this.initial = xml.attributes.getNamedItem("initial").nodeValue;
		if(xml.attributes.getNamedItem("maximum"))
			this.maximum = xml.attributes.getNamedItem("maximum").nodeValue;
		this.xml = xml.cloneNode(true);
		Fieldtype.types.push(this);
		if(Fieldtype.resources==null){
			Fieldtype.resources = resources;
		}
		this.initialized = true;
	}catch(err){
		this.error = err;
	}
}
//Main class members
Fieldtype.p = Fieldtype.prototype; // Short cut to prototype
Fieldtype.p.constructor = Fieldtype; // Short cut to access class
Fieldtype.p.constructor.types = [];
Fieldtype.p.constructor.resources = null;
//Objekteigenschaften
Fieldtype.p.id = null;
Fieldtype.p.name = null;
Fieldtype.p.picture = null;
Fieldtype.p.initial = 0;
Fieldtype.p.maximum = 0;
Fieldtype.p.upgrades = null;
Fieldtype.p.resources = null;
Fieldtype.p.costs = null;
Fieldtype.p.xml = null;
//
Fieldtype.p.getUpgrades = function(){
	if(this.upgrades==null){
		var ret = [];
		var upgrades = this.xml.getElementsByTagName("upgrade");
		for(var i=0;i<upgrades.length;i++){
			for(var j=0;j<Fieldtype.types.length;j++){
				if(Fieldtype.types[j].id==upgrades[i].innerHTML)
					ret.push(Fieldtype.types[j]);
			}
		}
		this.upgrades = ret;
	}
	return this.upgrades;
};
Fieldtype.p.getResources = function(){
	if(this.resources==null){
		var ret = [];
		var resources = this.xml.children;
		for(var i=0;i<resources.length;i++){
			if(resources[i].nodeName=='ressource'){
				for(var j=0;j<Fieldtype.resources.length;j++){
					if(Fieldtype.resources[j].id==resources[i].attributes.getNamedItem("id").nodeValue){
						var resNew = new Resource(Fieldtype.resources[j].xml);
						resNew.amount = resources[i].innerHTML;
						ret.push(resNew);
					}
				}
			}
		}
		this.resources = ret;
	}
	return this.resources;
};
Fieldtype.p.getCosts = function(){
	if(this.costs==null){
		var ret = [];
		if(this.xml.getElementsByTagName("kosten").length){
			var resources = this.xml.getElementsByTagName("kosten")[0].children;
			for(var i=0;i<resources.length;i++){
				for(var j=0; j<Fieldtype.resources.length;j++){
					if(resources[i].attributes.getNamedItem("id").nodeValue==Fieldtype.resources[j].id){
						var resCost = new Resource(Fieldtype.resources[j].xml);
						resCost.amount = resources[i].innerHTML;
						ret.push(resCost);
					}
				}
			}
		}
		this.costs = ret;
	}
	return this.costs;
};

/*

	FIELD

*/

function Field(id, xml, map, type){
	this.id = id;
	this.xml = xml;
	this.map = map;
	this.type = type;
	this.isInitialized = true;
}
//Main class members
Field.p = Field.prototype; // Short cut to prototype
Field.p.constructor = Field; // Short cut to access class
// Objekteigenschaften
Field.p.id = null;
Field.p.xml = null;
Field.p.map = null;
Field.p.type = null;








