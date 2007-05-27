/*
	Copyright (c) 2004-2006, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/community/licensing.shtml
*/


dojo.provide("dojo.uri.Uri");
dojo.uri=new function(){
this.dojoUri=function(_1){
return new dojo.uri.Uri(dojo.hostenv.getBaseScriptUri(),_1);
};
this.moduleUri=function(_2,_3){
var _4=dojo.hostenv.getModuleSymbols(_2).join("/");
if(!_4){
return null;
}
if(_4.lastIndexOf("/")!=_4.length-1){
_4+="/";
}
var _5=_4.indexOf(":");
var _6=_4.indexOf("/");
if(_4.charAt(0)!="/"&&(_5==-1||_5>_6)){
_4=dojo.hostenv.getBaseScriptUri()+_4;
}
return new dojo.uri.Uri(_4,_3);
};
this.Uri=function(){
var _7=arguments[0];
for(var i=1;i<arguments.length;i++){
if(!arguments[i]){
continue;
}
var _9=new dojo.uri.Uri(arguments[i].toString());
var _a=new dojo.uri.Uri(_7.toString());
if((_9.path=="")&&(_9.scheme==null)&&(_9.authority==null)&&(_9.query==null)){
if(_9.fragment!=null){
_a.fragment=_9.fragment;
}
_9=_a;
}else{
if(_9.scheme==null){
_9.scheme=_a.scheme;
if(_9.authority==null){
_9.authority=_a.authority;
if(_9.path.charAt(0)!="/"){
var _b=_a.path.substring(0,_a.path.lastIndexOf("/")+1)+_9.path;
var _c=_b.split("/");
for(var j=0;j<_c.length;j++){
if(_c[j]=="."){
if(j==_c.length-1){
_c[j]="";
}else{
_c.splice(j,1);
j--;
}
}else{
if(j>0&&!(j==1&&_c[0]=="")&&_c[j]==".."&&_c[j-1]!=".."){
if(j==_c.length-1){
_c.splice(j,1);
_c[j-1]="";
}else{
_c.splice(j-1,2);
j-=2;
}
}
}
}
_9.path=_c.join("/");
}
}
}
}
_7="";
if(_9.scheme!=null){
_7+=_9.scheme+":";
}
if(_9.authority!=null){
_7+="//"+_9.authority;
}
_7+=_9.path;
if(_9.query!=null){
_7+="?"+_9.query;
}
if(_9.fragment!=null){
_7+="#"+_9.fragment;
}
}
this.uri=_7.toString();
var _e="^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\\?([^#]*))?(#(.*))?$";
var r=this.uri.match(new RegExp(_e));
this.scheme=r[2]||(r[1]?"":null);
this.authority=r[4]||(r[3]?"":null);
this.path=r[5];
this.query=r[7]||(r[6]?"":null);
this.fragment=r[9]||(r[8]?"":null);
if(this.authority!=null){
_e="^((([^:]+:)?([^@]+))@)?([^:]*)(:([0-9]+))?$";
r=this.authority.match(new RegExp(_e));
this.user=r[3]||null;
this.password=r[4]||null;
this.host=r[5];
this.port=r[7]||null;
}
this.toString=function(){
return this.uri;
};
};
};
