/*
	Copyright (c) 2004-2006, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/community/licensing.shtml
*/


dojo.provide("dojo.uuid.NilGenerator");
dojo.uuid.NilGenerator=new function(){
this.generate=function(_1){
var _2="00000000-0000-0000-0000-000000000000";
if(_1&&(_1!=String)){
_2=new _1(_2);
}
return _2;
};
}();
