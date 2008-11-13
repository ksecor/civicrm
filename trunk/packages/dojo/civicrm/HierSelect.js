if(!dojo._hasResource["civicrm.HierSelect"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["civicrm.HierSelect"] = true;
// Author: Deepak Srivastava, CiviCRM Team
// Blog: http://civicrm.org/node/320 
// site: http://civicrm.org

dojo.provide("civicrm.HierSelect");

dojo.require("dijit._Widget");
dojo.require("dijit._Templated");

dojo.require("dijit.form.FilteringSelect");
dojo.require("dojox.data.QueryReadStore");

dojo.declare(
    "civicrm.HierSelect",
    //What it descends from
    [dijit._Widget, dijit._Templated],
    {
	widgetsInTemplate: true,
	
	name: "",
	url1: "",
	url2: "",
	url3: "",
	url4: "",
	default1: "",
	default2: "",
	default3: "",
	default4: "",
	hsTheme: "tundra",
	storeOption1: {},
	storeOption2: {},
	storeOption3: {},
	storeOption4: {},
	firstInList: false,
	jsEvent1 : "onChange",
	jsMethod1: "",
	jsEvent2 : "onChange",
	jsMethod2: "",
	jsEvent3 : "onChange",
	jsMethod3: "",
	jsEvent4 : "onChange",
	jsMethod4: "",
	freezeAll: false,

        templateString: "<span class=${hsTheme}></span>",

	postMixInProperties: function() {
		if (this.name == "" || this.url1 == "" || this.url2 == "") {
			console.log ("required attributes missing.");
			exit();
			//Note: exit is not recognized by javascript, but atleast would not allow 
			//      the parser to go ahead (by throwing a undefined error).
		}
	},

	postCreate: function() {
		var container = document.getElementById(this.id);

		var newAnchor = document.createElement("span");
		newAnchor.setAttribute("id", "id_" + this.name + "_0");
		container.appendChild(newAnchor);

		this.storeOption1 = new dojox.data.QueryReadStore(
		{url:this.url1,doClientPaging:false});

		var selector1 = new dijit.form.FilteringSelect(
		{store:this.storeOption1,name:this.name+"[0]",autocomplete:true},
		dojo.byId("id_" + this.name + "_0"));

		dojo.connect(selector1, 'onChange', this, 'onSelectionOne');
		if (this.jsMethod1 != "") {
		    dojo.connect(selector1, this.jsEvent1, this, function(e){eval(this.jsMethod1);});
		}

		if (!eval('document.getElementById("id_" + this.name + "_1")')) {
		    newAnchor = document.createElement("span");
		    newAnchor.setAttribute("id", "id_" + this.name + "_1");
		    container.appendChild(newAnchor);
		}
		this.storeOption2 = new dojox.data.QueryReadStore(
		{url:this.url2,doClientPaging:false});

		var selector2 = new dijit.form.FilteringSelect(
		{store:this.storeOption2,name:this.name+"[1]",autocomplete:true},
		dojo.byId("id_" + this.name + "_1"));

		if (this.jsMethod2 != "") {
		    dojo.connect(selector2, this.jsEvent2, this, function(e){eval(this.jsMethod2);});
		}

		if (this.url3 != "") {
		    if (!eval('document.getElementById("id_" + this.name + "_2")')) {
			newAnchor = document.createElement("span");
			newAnchor.setAttribute("id", "id_" + this.name + "_2");
			container.appendChild(newAnchor);
		    }

		    this.storeOption3 = new dojox.data.QueryReadStore(
		    {url:this.url3,doClientPaging:false});

		    var storeOption3 = this.storeOption3;

		    var selector3 = new dijit.form.FilteringSelect(
		    {store:storeOption3,name:this.name+"[2]",autocomplete:true},
		    dojo.byId("id_" + this.name + "_2"));

		    if (this.jsMethod3 != "") {
			dojo.connect(selector3, this.jsEvent3, this, function(e){eval(this.jsMethod3);});
		    }

		    dojo.connect(selector2, 'onChange', this, 
		    function(e) {
		    	urlNameValPairs = this.getUrlNameValPairs( storeOption3.url );
			urlNameValPairs['node1'] = selector1.getValue();
			urlNameValPairs['node2'] = e;
	    		urlQuery = this.getUrlQuery( urlNameValPairs );

			storeOption3.url = storeOption3.url.split("?")[0] + "?" + urlQuery;
			if (e && (e != "")) {
			    if (selector3.disabled == true && !this.freezeAll) {
				selector3.setAttribute('disabled',false);
			    }
			} else {
			    selector3.setAttribute('disabled',true);
			}

			var d3 = this.default3;
			var firstInList = this.firstInList;

			if (d3 == "") {
			    storeOption3.fetch({
				   query: {},
				   onComplete: function(items, request) {
					if (firstInList == false) {
					      selector3.setValue('');
					    } else {
					       selector3.setValue(storeOption3.getValues(items[0], 'value'));
					    }
				   }
			    });
			} else {
			    storeOption3.fetch({
				   query: {'default': 'true'},
				   onComplete: function(items, request) {
					selector3.setValue(d3);
				   }
		            });
			    this.default3="";
			}
			console.log("node1=",selector1.getValue()," node2=",e);
		    });

		    if (this.url4 != "") {
			if (!eval('document.getElementById("id_" + this.name + "_3")')) {
			     newAnchor = document.createElement("span");
			     newAnchor.setAttribute("id", "id_" + this.name + "_3");
			     container.appendChild(newAnchor);
			}

			this.storeOption4 = new dojox.data.QueryReadStore(
			{url:this.url4,doClientPaging:false});

			var storeOption4 = this.storeOption4;

			var selector4 = new dijit.form.FilteringSelect(
			{store:storeOption4,name:this.name+"[3]",autocomplete:true},
			dojo.byId("id_" + this.name + "_3"));

			if (this.jsMethod4 != "") {
			    dojo.connect(selector4, this.jsEvent4, this, function(e){eval(this.jsMethod4);});
			}

			dojo.connect(selector3, 'onChange', this, 
			function(e) {
			    urlNameValPairs = this.getUrlNameValPairs( storeOption3.url );
			    urlNameValPairs['node1'] = selector1.getValue();
			    urlNameValPairs['node2'] = selector2.getValue();
			    urlNameValPairs['node3'] = e;
	    		    urlQuery = this.getUrlQuery( urlNameValPairs );
	
			    storeOption4.url = storeOption4.url.split("?")[0] + "?" + urlQuery;
			    if (e && (e != "")) {
				if (selector4.disabled == true && !this.freezeAll) {
				    selector4.setAttribute('disabled',false);
				}
			    } else {
				selector4.setAttribute('disabled',true);
			    }

			    var d4 = this.default4;
			    var firstInList = this.firstInList;

			    if (d4 == "") {
				storeOption4.fetch({
				     query: {},
				     onComplete: function(items, request) {
					  if (firstInList == false) {
					      selector4.setValue('');
					  } else {
					      selector4.setValue(storeOption4.getValues(items[0], 'value'));
					  }
				     }
				});
			    } else {
				storeOption4.fetch({
				     query: {'default': 'true'},
				     onComplete: function(items, request) {
					  selector4.setValue(d4);
				     }
				});
				this.default4="";
			    }
			    console.log("node1=",selector1.getValue()," node2=",selector2.getValue()," node3=",e);
			});
		    }
		}

		var d1 = this.default1;
		if (/^\w+$/.test(d1) && (typeof d1 != 'undefined')) {
		    this.storeOption1.fetch({
			 query: {},
			 onComplete: function(items, request) {
			      selector1.setValue(d1);
			 }
		    });
		} else {
		    selector2.setAttribute('disabled',true);
		    if(this.url3 != "") {selector3.setAttribute('disabled',true);}
		    if(this.url4 != "") {selector4.setAttribute('disabled',true);}
		}

		if (this.freezeAll) {
		    selector1.setAttribute('disabled',true);
		    selector2.setAttribute('disabled',true);
		    if(this.url3 != "") {selector3.setAttribute('disabled',true);}
		    if(this.url4 != "") {selector4.setAttribute('disabled',true);}
		}
		
		// cleanup
		delete selector1;delete selector2; delete d1;
 	        if(this.url3 != "") {delete selector3; delete storeOption3}
		if(this.url4 != "") {delete selector4; delete storeOption4}
	},

	onSelectionOne : function(e) {
		var storeOption2 = this.storeOption2;
		
	    	urlNameValPairs = this.getUrlNameValPairs( storeOption2.url );
		urlNameValPairs['node1'] = e;
	    	urlQuery = this.getUrlQuery( urlNameValPairs );

		storeOption2.url = storeOption2.url.split("?")[0] + "?" + urlQuery;

		var selector2 = dijit.byId( "id_" + this.name + "_1" );
		
		if (e && (e != "")) {
		    if (selector2.disabled == true && !this.freezeAll) {
			selector2.setAttribute('disabled',false);
		    }
		} else {
			selector2.setAttribute('disabled',true);
		}
		var d2 = this.default2;
		var firstInList = this.firstInList;

		if (d2 == "") {
		   storeOption2.fetch(
		   {
		      query: {},
		      onComplete: function(items, request) {
			   if (firstInList == false) {
			       selector2.setValue('');
			   } else {
			       selector2.setValue(storeOption2.getValues(items[0], 'value'));
			   }
		      }
		   });
		} else {
		   storeOption2.fetch(
		   {
		      query: {'default': 'true'},
		      onComplete: function(items, request) {
			   selector2.setValue(d2);
		      }
		   });
		   this.default2="";
		}
		//console.log(e);
	},

	getUrlNameValPairs : function( url ) {
	    var urlNameValPairs = new Array();
	    var urlQueryVars = url.split("?")[1].split("&");
	    for (var i=0; i < urlQueryVars.length; i++) {
		urlNameValPairs[urlQueryVars[i].split("=")[0]] = urlQueryVars[i].split("=")[1];
	    }

	    return urlNameValPairs;
	},

	getUrlQuery : function( urlNameValPairs ) {
	    urlQueryVars = new Array();
	    for (var i in urlNameValPairs) {
		if ( typeof urlNameValPairs[i] == "string" ) {
		    urlQueryVars[urlQueryVars.length] = i + "=" + urlNameValPairs[i];
		}
	    }
	    return urlQueryVars.join('&');	
	}
    }
);
}
