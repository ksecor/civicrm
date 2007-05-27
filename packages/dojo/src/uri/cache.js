/*
	Copyright (c) 2004-2006, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/community/licensing.shtml
*/


dojo.provide("dojo.uri.cache");
dojo.uri.cache={_cache:{},set:function(_1,_2){
this._cache[_1.toString()]=_2;
return _1;
},remove:function(_3){
delete this._cache[_3.toString()];
},get:function(_4){
var _5=_4.toString();
var _6=this._cache[_5];
if(!_6){
_6=dojo.hostenv.getText(_5);
if(_6){
this._cache[_5]=_6;
}
}
return _6;
},allow:function(_7){
return _7;
}};
