/*
	Copyright (c) 2004-2006, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/community/licensing.shtml
*/


dojo.provide("dojo.widget.Select");
dojo.require("dojo.widget.ComboBox");
dojo.require("dojo.widget.*");
dojo.require("dojo.widget.html.stabile");
dojo.widget.defineWidget("dojo.widget.Select",dojo.widget.ComboBox,{forceValidOption:true,setValue:function(_1){
this.comboBoxValue.value=_1;
dojo.widget.html.stabile.setState(this.widgetId,this.getState(),true);
this.onValueChanged(_1);
},setLabel:function(_2){
this.comboBoxSelectionValue.value=_2;
if(this.textInputNode.value!=_2){
this.textInputNode.value=_2;
}
},getLabel:function(){
return this.comboBoxSelectionValue.value;
},getState:function(){
return {value:this.getValue(),label:this.getLabel()};
},onKeyUp:function(_3){
this.setLabel(this.textInputNode.value);
},setState:function(_4){
this.setValue(_4.value);
this.setLabel(_4.label);
},setAllValues:function(_5,_6){
this.setLabel(_5);
this.setValue(_6);
}});
