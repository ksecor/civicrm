// Original Author: Deepak Srivastava, CiviCRM Team
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
	widgetsInTemplate : true,
	
	name : "",
	url1 : "",
	url2 : "",
	url3 : "",
	default1 : "",
	default2 : "",
	default3 : "",
	innerLabel : "",
	firstInList : false,

        templateString : "<span class=\"tundra\"><span dojoType=\"dojox.data.QueryReadStore\" jsId=\"storeOptionsSel1\" url=${url1} doClientPaging=\"false\"></span><select dojoType=\"dijit.form.FilteringSelect\" store=\"storeOptionsSel1\" name=${name}[0] id=\"id_civicrm_hierselect_0\" autocomplete=\"true\" dojoAttachEvent=\"onChange:onSelectionOne\"></select>\t\t<label id=\"id_inner_label\" for=\"innerLabel\"></label>\t\t<span id=\"storeOption2\" dojoType=\"dojox.data.QueryReadStore\" jsId=\"storeOptionsSel2\" url=${url2} doClientPaging=\"false\"></span></span>",

	//templatePath: dojo.moduleUrl("civicrm","templates/HierSelect.html")

	// insert innerlabel between the two selectors
	// set default for selector 1
	postCreate: function() {
		var container = document.getElementById('civicrm_HierSelect_0');

		if (!eval('document.getElementById("id_civicrm_hierselect_1")')) {
			var newAnchor = document.createElement("span");
			newAnchor.setAttribute("id", "id_civicrm_hierselect_1");
			container.appendChild(newAnchor);
		}
		var selector2 = new dijit.form.FilteringSelect(
		{store:storeOptionsSel2,name:this.name+"[1]",autocomplete:true},
		dojo.byId("id_civicrm_hierselect_1"));

		if (this.url3 != "") {
			if (!eval('document.getElementById("id_civicrm_hierselect_2")')) {
				var newAnchor = document.createElement("span");
				newAnchor.setAttribute("id", "id_civicrm_hierselect_2");
				container.appendChild(newAnchor);
			}

			storeOption3 = new dojox.data.QueryReadStore(
			{url:this.url3,doClientPaging:false});

			var selector3 = new dijit.form.FilteringSelect(
			{store:storeOption3,name:this.name+"[2]",autocomplete:true},
			dojo.byId("id_civicrm_hierselect_2"));

			dojo.connect(dijit.byId('id_civicrm_hierselect_1'), 'onChange', this, 
			function(e) {
				var node1 = dijit.byId('id_civicrm_hierselect_0').getValue();
				storeOption3.url = storeOption3.url.split("?")[0] + "?node1=" + node1 + "&node2=" + e;
				
				if (selector3.disabled == true && /^\w+$/.test(e) && (typeof e != 'undefined')) {
					selector3.setDisabled(false);
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
				}
			   	if ( d3 != "" ) {
				   storeOption3.fetch(
				   {
				      query: {default:true},
				      onComplete: function(items, request) {
					       selector3.setValue(d3);
				      }
				   });
				}
				this.default3="";
				console.log("node1=",node1," node2=",e);
			});
		}

		if (this.innerLabel != "") {
			document.getElementById("id_inner_label").appendChild(document.createTextNode(this.innerLabel));
		}

		var d1 = this.default1;
		if (/^\w+$/.test(d1) && (typeof d1 != 'undefined')) {
		        var newValue1 = storeOptionsSel1.fetch(
		        {
			      query: {},
			      onComplete: function(items, request) {
				     dijit.byId( 'id_civicrm_hierselect_0' ).setValue(d1);
			      }
			});
		} else {
		        dijit.byId( 'id_civicrm_hierselect_1' ).setDisabled( true );
		        if(this.url3 != "") {dijit.byId( 'id_civicrm_hierselect_2' ).setDisabled( true );}
		}
		//console.log("postCreate");
	},

	onSelectionOne : function(e) {
		storeOptionsSel2.url = storeOptionsSel2.url.split("?")[0] + "?node1=" + e;
		var selector2 = dijit.byId( 'id_civicrm_hierselect_1' );
		
		if (selector2.disabled == true) {
		      selector2.setDisabled(false);
		}
		var d2 = this.default2;
		var firstInList = this.firstInList;

		if (d2 == "") {
		   storeOptionsSel2.fetch(
		   {
		      query: {},
		      onComplete: function(items, request) {
			   if (firstInList == false) {
			       selector2.setValue('');
			   } else {
			       selector2.setValue(storeOptionsSel2.getValues(items[0], 'value'));
			   }
		      }
		   });
		} else if (d2 != "") {
		   storeOptionsSel2.fetch(
		   {
		      query: {default:true},
		      onComplete: function(items, request) {
			   selector2.setValue(d2);
		      }
		   });
		}
		this.default2="";
		//console.log(e);
	}
    }
);