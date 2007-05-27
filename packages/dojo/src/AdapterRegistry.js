/*
	Copyright (c) 2004-2006, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/community/licensing.shtml
*/


dojo.provide("dojo.AdapterRegistry");
dojo.require("dojo.lang.func");
dojo.AdapterRegistry=function(_1){
this.pairs=[];
this.returnWrappers=_1||false;
};
dojo.lang.extend(dojo.AdapterRegistry,{register:function(_2,_3,_4,_5,_6){
var _7=(_6)?"unshift":"push";
this.pairs[_7]([_2,_3,_4,_5]);
},match:function(){
for(var i=0;i<this.pairs.length;i++){
var _9=this.pairs[i];
if(_9[1].apply(this,arguments)){
if((_9[3])||(this.returnWrappers)){
return _9[2];
}else{
return _9[2].apply(this,arguments);
}
}
}
throw new Error("No match found");
},unregister:function(_a){
for(var i=0;i<this.pairs.length;i++){
var _c=this.pairs[i];
if(_c[0]==_a){
this.pairs.splice(i,1);
return true;
}
}
return false;
}});
