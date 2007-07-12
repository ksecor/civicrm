/*
	Copyright (c) 2004-2006, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/community/licensing.shtml
*/


dojo.provide("dojo.widget.RealNumberTextbox");
dojo.require("dojo.widget.IntegerTextbox");
dojo.require("dojo.validate.common");
dojo.widget.defineWidget("dojo.widget.RealNumberTextbox",dojo.widget.IntegerTextbox,{mixInProperties:function(_1,_2){
dojo.widget.RealNumberTextbox.superclass.mixInProperties.apply(this,arguments);
if(_1.places){
this.flags.places=Number(_1.places);
}
if((_1.exponent=="true")||(_1.exponent=="always")){
this.flags.exponent=true;
}else{
if((_1.exponent=="false")||(_1.exponent=="never")){
this.flags.exponent=false;
}else{
this.flags.exponent=[true,false];
}
}
if((_1.esigned=="true")||(_1.esigned=="always")){
this.flags.eSigned=true;
}else{
if((_1.esigned=="false")||(_1.esigned=="never")){
this.flags.eSigned=false;
}else{
this.flags.eSigned=[true,false];
}
}
if(_1.min){
this.flags.min=parseFloat(_1.min);
}
if(_1.max){
this.flags.max=parseFloat(_1.max);
}
},isValid:function(){
return dojo.validate.isRealNumber(this.textbox.value,this.flags);
},isInRange:function(){
return dojo.validate.isInRange(this.textbox.value,this.flags);
}});
