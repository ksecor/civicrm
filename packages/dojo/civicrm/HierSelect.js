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
	default1: "",
	default2: "",
	default3: "",
	hsTheme: "tundra",
	storeOption1: {},
	storeOption2: {},
	storeOption3: {},
	innerLabel12: "",
	innerLabel23: "",
	firstInList: false,
	jsEvent1 : "onChange",
	jsMethod1: "",
	jsEvent2 : "onChange",
	jsMethod2: "",
	jsEvent3 : "onChange",
	jsMethod3: "",
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
			newAnchor.setAttribute("id", "id_" + this.name + "_label_12");
			container.appendChild(newAnchor);
			if (this.innerLabel12 == "") {
				newAnchor.appendChild(document.createTextNode("\t"));
			} else {
				newAnchor.appendChild(document.createTextNode(this.innerLabel12));
			}

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
				newAnchor.setAttribute("id", "id_" + this.name + "_label_23");
				container.appendChild(newAnchor);
				if (this.innerLabel23 == "") {
					newAnchor.appendChild(document.createTextNode("\t"));
				} else {
					newAnchor.appendChild(document.createTextNode(this.innerLabel23));
				}

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
				storeOption3.url = storeOption3.url.split("?")[0] + "?node1=" + selector1.getValue() + "&node2=" + e;
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
				   storeOption3.fetch(
				   {
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
				   storeOption3.fetch(
				   {
				      query: {default:true},
				      onComplete: function(items, request) {
					       selector3.setValue(d3);
				      }
				   });
				   this.default3="";
				}
				console.log("node1=",selector1.getValue()," node2=",e);
			});
		}

		var d1 = this.default1;
		if (/^\w+$/.test(d1) && (typeof d1 != 'undefined')) {
		        this.storeOption1.fetch(
		        {
			      query: {},
			      onComplete: function(items, request) {
				       selector1.setValue(d1);
			      }
			});
		} else {
			selector2.setAttribute('disabled',true);
		        if(this.url3 != "") {selector3.setAttribute('disabled',true);}
		}

		if (this.freezeAll) {
			selector1.setAttribute('disabled',true);
			selector2.setAttribute('disabled',true);
			if(this.url3 != "") {selector3.setAttribute('disabled',true);}
		}
		//console.log("postCreate");
	},

	onSelectionOne : function(e) {
		var storeOption2 = this.storeOption2;
		storeOption2.url = storeOption2.url.split("?")[0] + "?node1=" + e;
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
		      query: {default:true},
		      onComplete: function(items, request) {
			   selector2.setValue(d2);
		      }
		   });
		   this.default2="";
		}
		//console.log(e);
	}
    }
);

}
