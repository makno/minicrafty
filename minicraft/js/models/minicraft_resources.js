/*
 
 	RESOURCE
 
 */

function Resource(xml){
	try{
		this.id = xml.attributes.getNamedItem('id').nodeValue;
		if(xml.attributes.getNamedItem("initial"))
			this.initial = xml.attributes.getNamedItem("initial").nodeValue;
		this.name = xml.getElementsByTagName('name')[0].innerHTML;
		this.picture = xml.getElementsByTagName('bild')[0].attributes.getNamedItem('datei').nodeValue;
		this.xml = xml.cloneNode(true);
		this.initialized = true;
	}catch(err) {
	    this.error = err;
	}
}
//Main class members
Resource.p = Resource.prototype; // Short cut to prototype
Resource.p.constructor = Resource; // Short cut to access class
Resource.p.initialized = false;
Resource.p.id = null;
Resource.p.initial = null;
Resource.p.name = null;
Resource.p.picture = null;
Resource.p.xml = null;
Resource.p.amount = 0;


