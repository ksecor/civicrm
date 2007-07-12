/*
	Copyright (c) 2004-2006, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/community/licensing.shtml
*/


dojo.provide("dojo.widget.IntegerTextbox");
dojo.require("dojo.widget.ValidationTextbox");
dojo.require("dojo.validate.common");
dojo.widget.defineWidget("dojo.widget.IntegerTextbox",dojo.widget.ValidationTextbox,{mixInProperties:function(_1,_2){
dojo.widget.IntegerTextbox.superclass.mixInProperties.apply(this,arguments);
if((_1.signed=="true")||(_1.signed=="always")){
this.flags.signed=true;
}else{
if((_1.signed=="false")||(_1.signed=="never")){
this.flags.signed=false;
this.flags.min=0;
}else{
this.flags.signed=[true,false];
}
}
if(_1.separator){
this.flags.separator=_1.separator;
}
if(_1.min){
this.flags.min=parseInt(_1.min);
}
if(_1.max){
this.flags.max=parseInt(_1.max);
}
},isValid:function(){
return dojo.validate.isInteger(this.textbox.value,this.flags);
},isInRange:function(){
return dojo.validate.isInRange(this.textbox.value,this.flags);
}});
