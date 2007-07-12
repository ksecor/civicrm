/*
	Copyright (c) 2004-2006, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/community/licensing.shtml
*/


dojo.provide("dojo.validate.web");
dojo.require("dojo.validate.common");
dojo.validate.isIpAddress=function(_1,_2){
var re=new RegExp("^"+dojo.regexp.ipAddress(_2)+"$","i");
return re.test(_1);
};
dojo.validate.isUrl=function(_4,_5){
var re=new RegExp("^"+dojo.regexp.url(_5)+"$","i");
return re.test(_4);
};
dojo.validate.isEmailAddress=function(_7,_8){
var re=new RegExp("^"+dojo.regexp.emailAddress(_8)+"$","i");
return re.test(_7);
};
dojo.validate.isEmailAddressList=function(_a,_b){
var re=new RegExp("^"+dojo.regexp.emailAddressList(_b)+"$","i");
return re.test(_a);
};
dojo.validate.getEmailAddressList=function(_d,_e){
if(!_e){
_e={};
}
if(!_e.listSeparator){
_e.listSeparator="\\s;,";
}
if(dojo.validate.isEmailAddressList(_d,_e)){
return _d.split(new RegExp("\\s*["+_e.listSeparator+"]\\s*"));
}
return [];
};
