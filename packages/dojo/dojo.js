/*
	Copyright (c) 2004-2006, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/community/licensing.shtml
*/

/*
	This is a compiled version of Dojo, built for deployment and not for
	development. To get an editable version, please visit:

		http://dojotoolkit.org

	for documentation and information on getting the source.
*/

if(typeof dojo=="undefined"){
var dj_global=this;
var dj_currentContext=this;
function dj_undef(_1,_2){
return (typeof (_2||dj_currentContext)[_1]=="undefined");
}
if(dj_undef("djConfig",this)){
var djConfig={};
}
if(dj_undef("dojo",this)){
var dojo={};
}
dojo.global=function(){
return dj_currentContext;
};
dojo.locale=djConfig.locale;
dojo.version={major:0,minor:0,patch:0,flag:"dev",revision:Number("$Rev: 6258 $".match(/[0-9]+/)[0]),toString:function(){
with(dojo.version){
return major+"."+minor+"."+patch+flag+" ("+revision+")";
}
}};
dojo.evalProp=function(_3,_4,_5){
if((!_4)||(!_3)){
return undefined;
}
if(!dj_undef(_3,_4)){
return _4[_3];
}
return (_5?(_4[_3]={}):undefined);
};
dojo.parseObjPath=function(_6,_7,_8){
var _9=(_7||dojo.global());
var _a=_6.split(".");
var _b=_a.pop();
for(var i=0,l=_a.length;i<l&&_9;i++){
_9=dojo.evalProp(_a[i],_9,_8);
}
return {obj:_9,prop:_b};
};
dojo.evalObjPath=function(_e,_f){
if(typeof _e!="string"){
return dojo.global();
}
if(_e.indexOf(".")==-1){
return dojo.evalProp(_e,dojo.global(),_f);
}
var ref=dojo.parseObjPath(_e,dojo.global(),_f);
if(ref){
return dojo.evalProp(ref.prop,ref.obj,_f);
}
return null;
};
dojo.errorToString=function(_11){
if(!dj_undef("message",_11)){
return _11.message;
}else{
if(!dj_undef("description",_11)){
return _11.description;
}else{
return _11;
}
}
};
dojo.raise=function(_12,_13){
if(_13){
_12=_12+": "+dojo.errorToString(_13);
}
try{
if(djConfig.isDebug){
dojo.hostenv.println("FATAL exception raised: "+_12);
}
}
catch(e){
}
throw _13||Error(_12);
};
dojo.debug=function(){
};
dojo.debugShallow=function(obj){
};
dojo.profile={start:function(){
},end:function(){
},stop:function(){
},dump:function(){
}};
function dj_eval(_15){
return dj_global.eval?dj_global.eval(_15):eval(_15);
}
dojo.unimplemented=function(_16,_17){
var _18="'"+_16+"' not implemented";
if(_17!=null){
_18+=" "+_17;
}
dojo.raise(_18);
};
dojo.deprecated=function(_19,_1a,_1b){
var _1c="DEPRECATED: "+_19;
if(_1a){
_1c+=" "+_1a;
}
if(_1b){
_1c+=" -- will be removed in version: "+_1b;
}
dojo.debug(_1c);
};
dojo.render=(function(){
function vscaffold(_1d,_1e){
var tmp={capable:false,support:{builtin:false,plugin:false},prefixes:_1d};
for(var i=0;i<_1e.length;i++){
tmp[_1e[i]]=false;
}
return tmp;
}
return {name:"",ver:dojo.version,os:{win:false,linux:false,osx:false},html:vscaffold(["html"],["ie","opera","khtml","safari","moz"]),svg:vscaffold(["svg"],["corel","adobe","batik"]),vml:vscaffold(["vml"],["ie"]),swf:vscaffold(["Swf","Flash","Mm"],["mm"]),swt:vscaffold(["Swt"],["ibm"])};
})();
dojo.hostenv=(function(){
var _21={isDebug:false,allowQueryConfig:false,baseScriptUri:"",baseRelativePath:"",libraryScriptUri:"",iePreventClobber:false,ieClobberMinimal:true,preventBackButtonFix:true,delayMozLoadingFix:false,searchIds:[],parseWidgets:true};
if(typeof djConfig=="undefined"){
djConfig=_21;
}else{
for(var _22 in _21){
if(typeof djConfig[_22]=="undefined"){
djConfig[_22]=_21[_22];
}
}
}
return {name_:"(unset)",version_:"(unset)",getName:function(){
return this.name_;
},getVersion:function(){
return this.version_;
},getText:function(uri){
dojo.unimplemented("getText","uri="+uri);
}};
})();
dojo.hostenv.getBaseScriptUri=function(){
if(djConfig.baseScriptUri.length){
return djConfig.baseScriptUri;
}
var uri=new String(djConfig.libraryScriptUri||djConfig.baseRelativePath);
if(!uri){
dojo.raise("Nothing returned by getLibraryScriptUri(): "+uri);
}
var _25=uri.lastIndexOf("/");
djConfig.baseScriptUri=djConfig.baseRelativePath;
return djConfig.baseScriptUri;
};
(function(){
var _26={pkgFileName:"__package__",loading_modules_:{},loaded_modules_:{},addedToLoadingCount:[],removedFromLoadingCount:[],inFlightCount:0,modulePrefixes_:{dojo:{name:"dojo",value:"src"}},setModulePrefix:function(_27,_28){
this.modulePrefixes_[_27]={name:_27,value:_28};
},moduleHasPrefix:function(_29){
var mp=this.modulePrefixes_;
return Boolean(mp[_29]&&mp[_29].value);
},getModulePrefix:function(_2b){
if(this.moduleHasPrefix(_2b)){
return this.modulePrefixes_[_2b].value;
}
return _2b;
},getTextStack:[],loadUriStack:[],loadedUris:[],post_load_:false,modulesLoadedListeners:[],unloadListeners:[],loadNotifying:false};
for(var _2c in _26){
dojo.hostenv[_2c]=_26[_2c];
}
})();
dojo.hostenv.loadPath=function(_2d,_2e,cb){
var uri;
if(_2d.charAt(0)=="/"||_2d.match(/^\w+:/)){
uri=_2d;
}else{
uri=this.getBaseScriptUri()+_2d;
}
if(djConfig.cacheBust&&dojo.render.html.capable){
uri+="?"+String(djConfig.cacheBust).replace(/\W+/g,"");
}
try{
return !_2e?this.loadUri(uri,cb):this.loadUriAndCheck(uri,_2e,cb);
}
catch(e){
dojo.debug(e);
return false;
}
};
dojo.hostenv.loadUri=function(uri,cb){
if(this.loadedUris[uri]){
return true;
}
var _33=this.getText(uri,null,true);
if(!_33){
return false;
}
this.loadedUris[uri]=true;
if(cb){
_33="("+_33+")";
}
var _34=dj_eval(_33);
if(cb){
cb(_34);
}
return true;
};
dojo.hostenv.loadUriAndCheck=function(uri,_36,cb){
var ok=true;
try{
ok=this.loadUri(uri,cb);
}
catch(e){
dojo.debug("failed loading ",uri," with error: ",e);
}
return Boolean(ok&&this.findModule(_36,false));
};
dojo.loaded=function(){
};
dojo.unloaded=function(){
};
dojo.hostenv.loaded=function(){
this.loadNotifying=true;
this.post_load_=true;
var mll=this.modulesLoadedListeners;
for(var x=0;x<mll.length;x++){
mll[x]();
}
this.modulesLoadedListeners=[];
this.loadNotifying=false;
dojo.loaded();
};
dojo.hostenv.unloaded=function(){
var mll=this.unloadListeners;
while(mll.length){
(mll.pop())();
}
dojo.unloaded();
};
dojo.addOnLoad=function(obj,_3d){
var dh=dojo.hostenv;
if(arguments.length==1){
dh.modulesLoadedListeners.push(obj);
}else{
if(arguments.length>1){
dh.modulesLoadedListeners.push(function(){
obj[_3d]();
});
}
}
if(dh.post_load_&&dh.inFlightCount==0&&!dh.loadNotifying){
dh.callLoaded();
}
};
dojo.addOnUnload=function(obj,_40){
var dh=dojo.hostenv;
if(arguments.length==1){
dh.unloadListeners.push(obj);
}else{
if(arguments.length>1){
dh.unloadListeners.push(function(){
obj[_40]();
});
}
}
};
dojo.hostenv.modulesLoaded=function(){
if(this.post_load_){
return;
}
if(this.loadUriStack.length==0&&this.getTextStack.length==0){
if(this.inFlightCount>0){
dojo.debug("files still in flight!");
return;
}
dojo.hostenv.callLoaded();
}
};
dojo.hostenv.callLoaded=function(){
if(typeof setTimeout=="object"){
setTimeout("dojo.hostenv.loaded();",0);
}else{
dojo.hostenv.loaded();
}
};
dojo.hostenv.getModuleSymbols=function(_42){
var _43=_42.split(".");
for(var i=_43.length;i>0;i--){
var _45=_43.slice(0,i).join(".");
if((i==1)&&!this.moduleHasPrefix(_45)){
_43[0]="../"+_43[0];
}else{
var _46=this.getModulePrefix(_45);
if(_46!=_45){
_43.splice(0,i,_46);
break;
}
}
}
return _43;
};
dojo.hostenv._global_omit_module_check=false;
dojo.hostenv.loadModule=function(_47,_48,_49){
if(!_47){
return;
}
_49=this._global_omit_module_check||_49;
var _4a=this.findModule(_47,false);
if(_4a){
return _4a;
}
if(dj_undef(_47,this.loading_modules_)){
this.addedToLoadingCount.push(_47);
}
this.loading_modules_[_47]=1;
var _4b=_47.replace(/\./g,"/")+".js";
var _4c=_47.split(".");
var _4d=this.getModuleSymbols(_47);
var _4e=((_4d[0].charAt(0)!="/")&&!_4d[0].match(/^\w+:/));
var _4f=_4d[_4d.length-1];
var ok;
if(_4f=="*"){
_47=_4c.slice(0,-1).join(".");
while(_4d.length){
_4d.pop();
_4d.push(this.pkgFileName);
_4b=_4d.join("/")+".js";
if(_4e&&_4b.charAt(0)=="/"){
_4b=_4b.slice(1);
}
ok=this.loadPath(_4b,!_49?_47:null);
if(ok){
break;
}
_4d.pop();
}
}else{
_4b=_4d.join("/")+".js";
_47=_4c.join(".");
var _51=!_49?_47:null;
ok=this.loadPath(_4b,_51);
if(!ok&&!_48){
_4d.pop();
while(_4d.length){
_4b=_4d.join("/")+".js";
ok=this.loadPath(_4b,_51);
if(ok){
break;
}
_4d.pop();
_4b=_4d.join("/")+"/"+this.pkgFileName+".js";
if(_4e&&_4b.charAt(0)=="/"){
_4b=_4b.slice(1);
}
ok=this.loadPath(_4b,_51);
if(ok){
break;
}
}
}
if(!ok&&!_49){
dojo.raise("Could not load '"+_47+"'; last tried '"+_4b+"'");
}
}
if(!_49&&!this["isXDomain"]){
_4a=this.findModule(_47,false);
if(!_4a){
dojo.raise("symbol '"+_47+"' is not defined after loading '"+_4b+"'");
}
}
return _4a;
};
dojo.hostenv.startPackage=function(_52){
var _53=String(_52);
var _54=_53;
var _55=_52.split(/\./);
if(_55[_55.length-1]=="*"){
_55.pop();
_54=_55.join(".");
}
var _56=dojo.evalObjPath(_54,true);
this.loaded_modules_[_53]=_56;
this.loaded_modules_[_54]=_56;
return _56;
};
dojo.hostenv.findModule=function(_57,_58){
var lmn=String(_57);
if(this.loaded_modules_[lmn]){
return this.loaded_modules_[lmn];
}
if(_58){
dojo.raise("no loaded module named '"+_57+"'");
}
return null;
};
dojo.kwCompoundRequire=function(_5a){
var _5b=_5a["common"]||[];
var _5c=_5a[dojo.hostenv.name_]?_5b.concat(_5a[dojo.hostenv.name_]||[]):_5b.concat(_5a["default"]||[]);
for(var x=0;x<_5c.length;x++){
var _5e=_5c[x];
if(_5e.constructor==Array){
dojo.hostenv.loadModule.apply(dojo.hostenv,_5e);
}else{
dojo.hostenv.loadModule(_5e);
}
}
};
dojo.require=function(_5f){
dojo.hostenv.loadModule.apply(dojo.hostenv,arguments);
};
dojo.requireIf=function(_60,_61){
var _62=arguments[0];
if((_62===true)||(_62=="common")||(_62&&dojo.render[_62].capable)){
var _63=[];
for(var i=1;i<arguments.length;i++){
_63.push(arguments[i]);
}
dojo.require.apply(dojo,_63);
}
};
dojo.requireAfterIf=dojo.requireIf;
dojo.provide=function(_65){
return dojo.hostenv.startPackage.apply(dojo.hostenv,arguments);
};
dojo.registerModulePath=function(_66,_67){
return dojo.hostenv.setModulePrefix(_66,_67);
};
dojo.setModulePrefix=function(_68,_69){
dojo.deprecated("dojo.setModulePrefix(\""+_68+"\", \""+_69+"\")","replaced by dojo.registerModulePath","0.5");
return dojo.registerModulePath(_68,_69);
};
dojo.exists=function(obj,_6b){
var p=_6b.split(".");
for(var i=0;i<p.length;i++){
if(!obj[p[i]]){
return false;
}
obj=obj[p[i]];
}
return true;
};
dojo.hostenv.normalizeLocale=function(_6e){
return _6e?_6e.toLowerCase():dojo.locale;
};
dojo.hostenv.searchLocalePath=function(_6f,_70,_71){
_6f=dojo.hostenv.normalizeLocale(_6f);
var _72=_6f.split("-");
var _73=[];
for(var i=_72.length;i>0;i--){
_73.push(_72.slice(0,i).join("-"));
}
_73.push(false);
if(_70){
_73.reverse();
}
for(var j=_73.length-1;j>=0;j--){
var loc=_73[j]||"ROOT";
var _77=_71(loc);
if(_77){
break;
}
}
};
dojo.hostenv.localesGenerated;
dojo.hostenv.registerNlsPrefix=function(){
dojo.registerModulePath("nls","nls");
};
dojo.hostenv.preloadLocalizations=function(){
if(dojo.hostenv.localesGenerated){
dojo.hostenv.registerNlsPrefix();
function preload(_78){
_78=dojo.hostenv.normalizeLocale(_78);
dojo.hostenv.searchLocalePath(_78,true,function(loc){
for(var i=0;i<dojo.hostenv.localesGenerated.length;i++){
if(dojo.hostenv.localesGenerated[i]==loc){
dojo["require"]("nls.dojo_"+loc);
return true;
}
}
return false;
});
}
preload();
var _7b=djConfig.extraLocale||[];
for(var i=0;i<_7b.length;i++){
preload(_7b[i]);
}
}
dojo.hostenv.preloadLocalizations=function(){
};
};
dojo.requireLocalization=function(_7d,_7e,_7f){
dojo.hostenv.preloadLocalizations();
var _80=[_7d,"nls",_7e].join(".");
var _81=dojo.hostenv.findModule(_80);
if(_81){
if(djConfig.localizationComplete&&_81._built){
return;
}
var _82=dojo.hostenv.normalizeLocale(_7f).replace("-","_");
var _83=_80+"."+_82;
if(dojo.hostenv.findModule(_83)){
return;
}
}
_81=dojo.hostenv.startPackage(_80);
var _84=dojo.hostenv.getModuleSymbols(_7d);
var _85=_84.concat("nls").join("/");
var _86;
dojo.hostenv.searchLocalePath(_7f,false,function(loc){
var _88=loc.replace("-","_");
var _89=_80+"."+_88;
var _8a=false;
if(!dojo.hostenv.findModule(_89)){
dojo.hostenv.startPackage(_89);
var _8b=[_85];
if(loc!="ROOT"){
_8b.push(loc);
}
_8b.push(_7e);
var _8c=_8b.join("/")+".js";
_8a=dojo.hostenv.loadPath(_8c,null,function(_8d){
var _8e=function(){
};
_8e.prototype=_86;
_81[_88]=new _8e();
for(var j in _8d){
_81[_88][j]=_8d[j];
}
});
}else{
_8a=true;
}
if(_8a&&_81[_88]){
_86=_81[_88];
}else{
_81[_88]=_86;
}
});
};
(function(){
var _90=djConfig.extraLocale;
if(_90){
if(!_90 instanceof Array){
_90=[_90];
}
var req=dojo.requireLocalization;
dojo.requireLocalization=function(m,b,_94){
req(m,b,_94);
if(_94){
return;
}
for(var i=0;i<_90.length;i++){
req(m,b,_90[i]);
}
};
}
})();
}
if(typeof window!="undefined"){
(function(){
if(djConfig.allowQueryConfig){
var _96=document.location.toString();
var _97=_96.split("?",2);
if(_97.length>1){
var _98=_97[1];
var _99=_98.split("&");
for(var x in _99){
var sp=_99[x].split("=");
if((sp[0].length>9)&&(sp[0].substr(0,9)=="djConfig.")){
var opt=sp[0].substr(9);
try{
djConfig[opt]=eval(sp[1]);
}
catch(e){
djConfig[opt]=sp[1];
}
}
}
}
}
if(((djConfig["baseScriptUri"]=="")||(djConfig["baseRelativePath"]==""))&&(document&&document.getElementsByTagName)){
var _9d=document.getElementsByTagName("script");
var _9e=/(__package__|dojo|bootstrap1)\.js([\?\.]|$)/i;
for(var i=0;i<_9d.length;i++){
var src=_9d[i].getAttribute("src");
if(!src){
continue;
}
var m=src.match(_9e);
if(m){
var _a2=src.substring(0,m.index);
if(src.indexOf("bootstrap1")>-1){
_a2+="../";
}
if(!this["djConfig"]){
djConfig={};
}
if(djConfig["baseScriptUri"]==""){
djConfig["baseScriptUri"]=_a2;
}
if(djConfig["baseRelativePath"]==""){
djConfig["baseRelativePath"]=_a2;
}
break;
}
}
}
var dr=dojo.render;
var drh=dojo.render.html;
var drs=dojo.render.svg;
var dua=(drh.UA=navigator.userAgent);
var dav=(drh.AV=navigator.appVersion);
var t=true;
var f=false;
drh.capable=t;
drh.support.builtin=t;
dr.ver=parseFloat(drh.AV);
dr.os.mac=dav.indexOf("Macintosh")>=0;
dr.os.win=dav.indexOf("Windows")>=0;
dr.os.linux=dav.indexOf("X11")>=0;
drh.opera=dua.indexOf("Opera")>=0;
drh.khtml=(dav.indexOf("Konqueror")>=0)||(dav.indexOf("Safari")>=0);
drh.safari=dav.indexOf("Safari")>=0;
var _aa=dua.indexOf("Gecko");
drh.mozilla=drh.moz=(_aa>=0)&&(!drh.khtml);
if(drh.mozilla){
drh.geckoVersion=dua.substring(_aa+6,_aa+14);
}
drh.ie=(document.all)&&(!drh.opera);
drh.ie50=drh.ie&&dav.indexOf("MSIE 5.0")>=0;
drh.ie55=drh.ie&&dav.indexOf("MSIE 5.5")>=0;
drh.ie60=drh.ie&&dav.indexOf("MSIE 6.0")>=0;
drh.ie70=drh.ie&&dav.indexOf("MSIE 7.0")>=0;
var cm=document["compatMode"];
drh.quirks=(cm=="BackCompat")||(cm=="QuirksMode")||drh.ie55||drh.ie50;
dojo.locale=dojo.locale||(drh.ie?navigator.userLanguage:navigator.language).toLowerCase();
dr.vml.capable=drh.ie;
drs.capable=f;
drs.support.plugin=f;
drs.support.builtin=f;
var _ac=window["document"];
var tdi=_ac["implementation"];
if((tdi)&&(tdi["hasFeature"])&&(tdi.hasFeature("org.w3c.dom.svg","1.0"))){
drs.capable=t;
drs.support.builtin=t;
drs.support.plugin=f;
}
if(drh.safari){
var tmp=dua.split("AppleWebKit/")[1];
var ver=parseFloat(tmp.split(" ")[0]);
if(ver>=420){
drs.capable=t;
drs.support.builtin=t;
drs.support.plugin=f;
}
}
})();
dojo.hostenv.startPackage("dojo.hostenv");
dojo.render.name=dojo.hostenv.name_="browser";
dojo.hostenv.searchIds=[];
dojo.hostenv._XMLHTTP_PROGIDS=["Msxml2.XMLHTTP","Microsoft.XMLHTTP","Msxml2.XMLHTTP.4.0"];
dojo.hostenv.getXmlhttpObject=function(){
var _b0=null;
var _b1=null;
try{
_b0=new XMLHttpRequest();
}
catch(e){
}
if(!_b0){
for(var i=0;i<3;++i){
var _b3=dojo.hostenv._XMLHTTP_PROGIDS[i];
try{
_b0=new ActiveXObject(_b3);
}
catch(e){
_b1=e;
}
if(_b0){
dojo.hostenv._XMLHTTP_PROGIDS=[_b3];
break;
}
}
}
if(!_b0){
return dojo.raise("XMLHTTP not available",_b1);
}
return _b0;
};
dojo.hostenv._blockAsync=false;
dojo.hostenv.getText=function(uri,_b5,_b6){
if(!_b5){
this._blockAsync=true;
}
var _b7=this.getXmlhttpObject();
function isDocumentOk(_b8){
var _b9=_b8["status"];
return Boolean((!_b9)||((200<=_b9)&&(300>_b9))||(_b9==304));
}
if(_b5){
var _ba=this,_bb=null,gbl=dojo.global();
var xhr=dojo.evalObjPath("dojo.io.XMLHTTPTransport");
_b7.onreadystatechange=function(){
if(_bb){
gbl.clearTimeout(_bb);
_bb=null;
}
if(_ba._blockAsync||(xhr&&xhr._blockAsync)){
_bb=gbl.setTimeout(function(){
_b7.onreadystatechange.apply(this);
},10);
}else{
if(4==_b7.readyState){
if(isDocumentOk(_b7)){
_b5(_b7.responseText);
}
}
}
};
}
_b7.open("GET",uri,_b5?true:false);
try{
_b7.send(null);
if(_b5){
return null;
}
if(!isDocumentOk(_b7)){
var err=Error("Unable to load "+uri+" status:"+_b7.status);
err.status=_b7.status;
err.responseText=_b7.responseText;
throw err;
}
}
catch(e){
this._blockAsync=false;
if((_b6)&&(!_b5)){
return null;
}else{
throw e;
}
}
this._blockAsync=false;
return _b7.responseText;
};
dojo.hostenv.defaultDebugContainerId="dojoDebug";
dojo.hostenv._println_buffer=[];
dojo.hostenv._println_safe=false;
dojo.hostenv.println=function(_bf){
if(!dojo.hostenv._println_safe){
dojo.hostenv._println_buffer.push(_bf);
}else{
try{
var _c0=document.getElementById(djConfig.debugContainerId?djConfig.debugContainerId:dojo.hostenv.defaultDebugContainerId);
if(!_c0){
_c0=dojo.body();
}
var div=document.createElement("div");
div.appendChild(document.createTextNode(_bf));
_c0.appendChild(div);
}
catch(e){
try{
document.write("<div>"+_bf+"</div>");
}
catch(e2){
window.status=_bf;
}
}
}
};
dojo.addOnLoad(function(){
dojo.hostenv._println_safe=true;
while(dojo.hostenv._println_buffer.length>0){
dojo.hostenv.println(dojo.hostenv._println_buffer.shift());
}
});
function dj_addNodeEvtHdlr(_c2,_c3,fp,_c5){
var _c6=_c2["on"+_c3]||function(){
};
_c2["on"+_c3]=function(){
fp.apply(_c2,arguments);
_c6.apply(_c2,arguments);
};
return true;
}
function dj_load_init(e){
var _c8=(e&&e.type)?e.type.toLowerCase():"load";
if(arguments.callee.initialized||(_c8!="domcontentloaded"&&_c8!="load")){
return;
}
arguments.callee.initialized=true;
if(typeof (_timer)!="undefined"){
clearInterval(_timer);
delete _timer;
}
var _c9=function(){
if(dojo.render.html.ie){
dojo.hostenv.makeWidgets();
}
};
if(dojo.hostenv.inFlightCount==0){
_c9();
dojo.hostenv.modulesLoaded();
}else{
dojo.addOnLoad(_c9);
}
}
if(document.addEventListener){
if(dojo.render.html.opera||(dojo.render.html.moz&&!djConfig.delayMozLoadingFix)){
document.addEventListener("DOMContentLoaded",dj_load_init,null);
}
window.addEventListener("load",dj_load_init,null);
}
if(dojo.render.html.ie&&dojo.render.os.win){
document.attachEvent("onreadystatechange",function(e){
if(document.readyState=="complete"){
dj_load_init();
}
});
}
if(/(WebKit|khtml)/i.test(navigator.userAgent)){
var _timer=setInterval(function(){
if(/loaded|complete/.test(document.readyState)){
dj_load_init();
}
},10);
}
if(dojo.render.html.ie){
dj_addNodeEvtHdlr(window,"beforeunload",function(){
dojo.hostenv._unloading=true;
window.setTimeout(function(){
dojo.hostenv._unloading=false;
},0);
});
}
dj_addNodeEvtHdlr(window,"unload",function(){
dojo.hostenv.unloaded();
if((!dojo.render.html.ie)||(dojo.render.html.ie&&dojo.hostenv._unloading)){
dojo.hostenv.unloaded();
}
});
dojo.hostenv.makeWidgets=function(){
var _cb=[];
if(djConfig.searchIds&&djConfig.searchIds.length>0){
_cb=_cb.concat(djConfig.searchIds);
}
if(dojo.hostenv.searchIds&&dojo.hostenv.searchIds.length>0){
_cb=_cb.concat(dojo.hostenv.searchIds);
}
if((djConfig.parseWidgets)||(_cb.length>0)){
if(dojo.evalObjPath("dojo.widget.Parse")){
var _cc=new dojo.xml.Parse();
if(_cb.length>0){
for(var x=0;x<_cb.length;x++){
var _ce=document.getElementById(_cb[x]);
if(!_ce){
continue;
}
var _cf=_cc.parseElement(_ce,null,true);
dojo.widget.getParser().createComponents(_cf);
}
}else{
if(djConfig.parseWidgets){
var _cf=_cc.parseElement(dojo.body(),null,true);
dojo.widget.getParser().createComponents(_cf);
}
}
}
}
};
dojo.addOnLoad(function(){
if(!dojo.render.html.ie){
dojo.hostenv.makeWidgets();
}
});
try{
if(dojo.render.html.ie){
document.namespaces.add("v","urn:schemas-microsoft-com:vml");
document.createStyleSheet().addRule("v\\:*","behavior:url(#default#VML)");
}
}
catch(e){
}
dojo.hostenv.writeIncludes=function(){
};
if(!dj_undef("document",this)){
dj_currentDocument=this.document;
}
dojo.doc=function(){
return dj_currentDocument;
};
dojo.body=function(){
return dojo.doc().body||dojo.doc().getElementsByTagName("body")[0];
};
dojo.byId=function(id,doc){
if((id)&&((typeof id=="string")||(id instanceof String))){
if(!doc){
doc=dj_currentDocument;
}
var ele=doc.getElementById(id);
if(ele&&(ele.id!=id)&&doc.all){
ele=null;
eles=doc.all[id];
if(eles){
if(eles.length){
for(var i=0;i<eles.length;i++){
if(eles[i].id==id){
ele=eles[i];
break;
}
}
}else{
ele=eles;
}
}
}
return ele;
}
return id;
};
dojo.setContext=function(_d4,_d5){
dj_currentContext=_d4;
dj_currentDocument=_d5;
};
dojo._fireCallback=function(_d6,_d7,_d8){
if((_d7)&&((typeof _d6=="string")||(_d6 instanceof String))){
_d6=_d7[_d6];
}
return (_d7?_d6.apply(_d7,_d8||[]):_d6());
};
dojo.withGlobal=function(_d9,_da,_db,_dc){
var _dd;
var _de=dj_currentContext;
var _df=dj_currentDocument;
try{
dojo.setContext(_d9,_d9.document);
_dd=dojo._fireCallback(_da,_db,_dc);
}
finally{
dojo.setContext(_de,_df);
}
return _dd;
};
dojo.withDoc=function(_e0,_e1,_e2,_e3){
var _e4;
var _e5=dj_currentDocument;
try{
dj_currentDocument=_e0;
_e4=dojo._fireCallback(_e1,_e2,_e3);
}
finally{
dj_currentDocument=_e5;
}
return _e4;
};
}
(function(){
if(typeof dj_usingBootstrap!="undefined"){
return;
}
var _e6=false;
var _e7=false;
var _e8=false;
if((typeof this["load"]=="function")&&((typeof this["Packages"]=="function")||(typeof this["Packages"]=="object"))){
_e6=true;
}else{
if(typeof this["load"]=="function"){
_e7=true;
}else{
if(window.widget){
_e8=true;
}
}
}
var _e9=[];
if((this["djConfig"])&&((djConfig["isDebug"])||(djConfig["debugAtAllCosts"]))){
_e9.push("debug.js");
}
if((this["djConfig"])&&(djConfig["debugAtAllCosts"])&&(!_e6)&&(!_e8)){
_e9.push("browser_debug.js");
}
var _ea=djConfig["baseScriptUri"];
if((this["djConfig"])&&(djConfig["baseLoaderUri"])){
_ea=djConfig["baseLoaderUri"];
}
for(var x=0;x<_e9.length;x++){
var _ec=_ea+"src/"+_e9[x];
if(_e6||_e7){
load(_ec);
}else{
try{
document.write("<scr"+"ipt type='text/javascript' src='"+_ec+"'></scr"+"ipt>");
}
catch(e){
var _ed=document.createElement("script");
_ed.src=_ec;
document.getElementsByTagName("head")[0].appendChild(_ed);
}
}
}
})();
dojo.provide("dojo.string.common");
dojo.string.trim=function(str,wh){
if(!str.replace){
return str;
}
if(!str.length){
return str;
}
var re=(wh>0)?(/^\s+/):(wh<0)?(/\s+$/):(/^\s+|\s+$/g);
return str.replace(re,"");
};
dojo.string.trimStart=function(str){
return dojo.string.trim(str,1);
};
dojo.string.trimEnd=function(str){
return dojo.string.trim(str,-1);
};
dojo.string.repeat=function(str,_f4,_f5){
var out="";
for(var i=0;i<_f4;i++){
out+=str;
if(_f5&&i<_f4-1){
out+=_f5;
}
}
return out;
};
dojo.string.pad=function(str,len,c,dir){
var out=String(str);
if(!c){
c="0";
}
if(!dir){
dir=1;
}
while(out.length<len){
if(dir>0){
out=c+out;
}else{
out+=c;
}
}
return out;
};
dojo.string.padLeft=function(str,len,c){
return dojo.string.pad(str,len,c,1);
};
dojo.string.padRight=function(str,len,c){
return dojo.string.pad(str,len,c,-1);
};
dojo.provide("dojo.string");
dojo.provide("dojo.lang.common");
dojo.lang.inherits=function(_103,_104){
if(typeof _104!="function"){
dojo.raise("dojo.inherits: superclass argument ["+_104+"] must be a function (subclass: ["+_103+"']");
}
_103.prototype=new _104();
_103.prototype.constructor=_103;
_103.superclass=_104.prototype;
_103["super"]=_104.prototype;
};
dojo.lang._mixin=function(obj,_106){
var tobj={};
for(var x in _106){
if((typeof tobj[x]=="undefined")||(tobj[x]!=_106[x])){
obj[x]=_106[x];
}
}
if(dojo.render.html.ie&&(typeof (_106["toString"])=="function")&&(_106["toString"]!=obj["toString"])&&(_106["toString"]!=tobj["toString"])){
obj.toString=_106.toString;
}
return obj;
};
dojo.lang.mixin=function(obj,_10a){
for(var i=1,l=arguments.length;i<l;i++){
dojo.lang._mixin(obj,arguments[i]);
}
return obj;
};
dojo.lang.extend=function(_10d,_10e){
for(var i=1,l=arguments.length;i<l;i++){
dojo.lang._mixin(_10d.prototype,arguments[i]);
}
return _10d;
};
dojo.inherits=dojo.lang.inherits;
dojo.mixin=dojo.lang.mixin;
dojo.extend=dojo.lang.extend;
dojo.lang.find=function(_111,_112,_113,_114){
if(!dojo.lang.isArrayLike(_111)&&dojo.lang.isArrayLike(_112)){
dojo.deprecated("dojo.lang.find(value, array)","use dojo.lang.find(array, value) instead","0.5");
var temp=_111;
_111=_112;
_112=temp;
}
var _116=dojo.lang.isString(_111);
if(_116){
_111=_111.split("");
}
if(_114){
var step=-1;
var i=_111.length-1;
var end=-1;
}else{
var step=1;
var i=0;
var end=_111.length;
}
if(_113){
while(i!=end){
if(_111[i]===_112){
return i;
}
i+=step;
}
}else{
while(i!=end){
if(_111[i]==_112){
return i;
}
i+=step;
}
}
return -1;
};
dojo.lang.indexOf=dojo.lang.find;
dojo.lang.findLast=function(_11a,_11b,_11c){
return dojo.lang.find(_11a,_11b,_11c,true);
};
dojo.lang.lastIndexOf=dojo.lang.findLast;
dojo.lang.inArray=function(_11d,_11e){
return dojo.lang.find(_11d,_11e)>-1;
};
dojo.lang.isObject=function(it){
if(typeof it=="undefined"){
return false;
}
return (typeof it=="object"||it===null||dojo.lang.isArray(it)||dojo.lang.isFunction(it));
};
dojo.lang.isArray=function(it){
return (it&&it instanceof Array||typeof it=="array");
};
dojo.lang.isArrayLike=function(it){
if((!it)||(dojo.lang.isUndefined(it))){
return false;
}
if(dojo.lang.isString(it)){
return false;
}
if(dojo.lang.isFunction(it)){
return false;
}
if(dojo.lang.isArray(it)){
return true;
}
if((it.tagName)&&(it.tagName.toLowerCase()=="form")){
return false;
}
if(dojo.lang.isNumber(it.length)&&isFinite(it.length)){
return true;
}
return false;
};
dojo.lang.isFunction=function(it){
if(!it){
return false;
}
if((typeof (it)=="function")&&(it=="[object NodeList]")){
return false;
}
return (it instanceof Function||typeof it=="function");
};
dojo.lang.isString=function(it){
return (typeof it=="string"||it instanceof String);
};
dojo.lang.isAlien=function(it){
if(!it){
return false;
}
return !dojo.lang.isFunction()&&/\{\s*\[native code\]\s*\}/.test(String(it));
};
dojo.lang.isBoolean=function(it){
return (it instanceof Boolean||typeof it=="boolean");
};
dojo.lang.isNumber=function(it){
return (it instanceof Number||typeof it=="number");
};
dojo.lang.isUndefined=function(it){
return ((typeof (it)=="undefined")&&(it==undefined));
};
dojo.provide("dojo.lang.extras");
dojo.lang.setTimeout=function(func,_129){
var _12a=window,_12b=2;
if(!dojo.lang.isFunction(func)){
_12a=func;
func=_129;
_129=arguments[2];
_12b++;
}
if(dojo.lang.isString(func)){
func=_12a[func];
}
var args=[];
for(var i=_12b;i<arguments.length;i++){
args.push(arguments[i]);
}
return dojo.global().setTimeout(function(){
func.apply(_12a,args);
},_129);
};
dojo.lang.clearTimeout=function(_12e){
dojo.global().clearTimeout(_12e);
};
dojo.lang.getNameInObj=function(ns,item){
if(!ns){
ns=dj_global;
}
for(var x in ns){
if(ns[x]===item){
return new String(x);
}
}
return null;
};
dojo.lang.shallowCopy=function(obj,deep){
var i,ret;
if(obj===null){
return null;
}
if(dojo.lang.isObject(obj)){
ret=new obj.constructor();
for(i in obj){
if(dojo.lang.isUndefined(ret[i])){
ret[i]=deep?dojo.lang.shallowCopy(obj[i],deep):obj[i];
}
}
}else{
if(dojo.lang.isArray(obj)){
ret=[];
for(i=0;i<obj.length;i++){
ret[i]=deep?dojo.lang.shallowCopy(obj[i],deep):obj[i];
}
}else{
ret=obj;
}
}
return ret;
};
dojo.lang.firstValued=function(){
for(var i=0;i<arguments.length;i++){
if(typeof arguments[i]!="undefined"){
return arguments[i];
}
}
return undefined;
};
dojo.lang.getObjPathValue=function(_137,_138,_139){
with(dojo.parseObjPath(_137,_138,_139)){
return dojo.evalProp(prop,obj,_139);
}
};
dojo.lang.setObjPathValue=function(_13a,_13b,_13c,_13d){
if(arguments.length<4){
_13d=true;
}
with(dojo.parseObjPath(_13a,_13c,_13d)){
if(obj&&(_13d||(prop in obj))){
obj[prop]=_13b;
}
}
};
dojo.provide("dojo.io.common");
dojo.io.transports=[];
dojo.io.hdlrFuncNames=["load","error","timeout"];
dojo.io.Request=function(url,_13f,_140,_141){
if((arguments.length==1)&&(arguments[0].constructor==Object)){
this.fromKwArgs(arguments[0]);
}else{
this.url=url;
if(_13f){
this.mimetype=_13f;
}
if(_140){
this.transport=_140;
}
if(arguments.length>=4){
this.changeUrl=_141;
}
}
};
dojo.lang.extend(dojo.io.Request,{url:"",mimetype:"text/plain",method:"GET",content:undefined,transport:undefined,changeUrl:undefined,formNode:undefined,sync:false,bindSuccess:false,useCache:false,preventCache:false,load:function(type,data,_144,_145){
},error:function(type,_147,_148,_149){
},timeout:function(type,_14b,_14c,_14d){
},handle:function(type,data,_150,_151){
},timeoutSeconds:0,abort:function(){
},fromKwArgs:function(_152){
if(_152["url"]){
_152.url=_152.url.toString();
}
if(_152["formNode"]){
_152.formNode=dojo.byId(_152.formNode);
}
if(!_152["method"]&&_152["formNode"]&&_152["formNode"].method){
_152.method=_152["formNode"].method;
}
if(!_152["handle"]&&_152["handler"]){
_152.handle=_152.handler;
}
if(!_152["load"]&&_152["loaded"]){
_152.load=_152.loaded;
}
if(!_152["changeUrl"]&&_152["changeURL"]){
_152.changeUrl=_152.changeURL;
}
_152.encoding=dojo.lang.firstValued(_152["encoding"],djConfig["bindEncoding"],"");
_152.sendTransport=dojo.lang.firstValued(_152["sendTransport"],djConfig["ioSendTransport"],false);
var _153=dojo.lang.isFunction;
for(var x=0;x<dojo.io.hdlrFuncNames.length;x++){
var fn=dojo.io.hdlrFuncNames[x];
if(_152[fn]&&_153(_152[fn])){
continue;
}
if(_152["handle"]&&_153(_152["handle"])){
_152[fn]=_152.handle;
}
}
dojo.lang.mixin(this,_152);
}});
dojo.io.Error=function(msg,type,num){
this.message=msg;
this.type=type||"unknown";
this.number=num||0;
};
dojo.io.transports.addTransport=function(name){
this.push(name);
this[name]=dojo.io[name];
};
dojo.io.bind=function(_15a){
if(!(_15a instanceof dojo.io.Request)){
try{
_15a=new dojo.io.Request(_15a);
}
catch(e){
dojo.debug(e);
}
}
var _15b="";
if(_15a["transport"]){
_15b=_15a["transport"];
if(!this[_15b]){
dojo.io.sendBindError(_15a,"No dojo.io.bind() transport with name '"+_15a["transport"]+"'.");
return _15a;
}
if(!this[_15b].canHandle(_15a)){
dojo.io.sendBindError(_15a,"dojo.io.bind() transport with name '"+_15a["transport"]+"' cannot handle this type of request.");
return _15a;
}
}else{
for(var x=0;x<dojo.io.transports.length;x++){
var tmp=dojo.io.transports[x];
if((this[tmp])&&(this[tmp].canHandle(_15a))){
_15b=tmp;
break;
}
}
if(_15b==""){
dojo.io.sendBindError(_15a,"None of the loaded transports for dojo.io.bind()"+" can handle the request.");
return _15a;
}
}
this[_15b].bind(_15a);
_15a.bindSuccess=true;
return _15a;
};
dojo.io.sendBindError=function(_15e,_15f){
if((typeof _15e.error=="function"||typeof _15e.handle=="function")&&(typeof setTimeout=="function"||typeof setTimeout=="object")){
var _160=new dojo.io.Error(_15f);
setTimeout(function(){
_15e[(typeof _15e.error=="function")?"error":"handle"]("error",_160,null,_15e);
},50);
}else{
dojo.raise(_15f);
}
};
dojo.io.queueBind=function(_161){
if(!(_161 instanceof dojo.io.Request)){
try{
_161=new dojo.io.Request(_161);
}
catch(e){
dojo.debug(e);
}
}
var _162=_161.load;
_161.load=function(){
dojo.io._queueBindInFlight=false;
var ret=_162.apply(this,arguments);
dojo.io._dispatchNextQueueBind();
return ret;
};
var _164=_161.error;
_161.error=function(){
dojo.io._queueBindInFlight=false;
var ret=_164.apply(this,arguments);
dojo.io._dispatchNextQueueBind();
return ret;
};
dojo.io._bindQueue.push(_161);
dojo.io._dispatchNextQueueBind();
return _161;
};
dojo.io._dispatchNextQueueBind=function(){
if(!dojo.io._queueBindInFlight){
dojo.io._queueBindInFlight=true;
if(dojo.io._bindQueue.length>0){
dojo.io.bind(dojo.io._bindQueue.shift());
}else{
dojo.io._queueBindInFlight=false;
}
}
};
dojo.io._bindQueue=[];
dojo.io._queueBindInFlight=false;
dojo.io.argsFromMap=function(map,_167,last){
var enc=/utf/i.test(_167||"")?encodeURIComponent:dojo.string.encodeAscii;
var _16a=[];
var _16b=new Object();
for(var name in map){
var _16d=function(elt){
var val=enc(name)+"="+enc(elt);
_16a[(last==name)?"push":"unshift"](val);
};
if(!_16b[name]){
var _170=map[name];
if(dojo.lang.isArray(_170)){
dojo.lang.forEach(_170,_16d);
}else{
_16d(_170);
}
}
}
return _16a.join("&");
};
dojo.io.setIFrameSrc=function(_171,src,_173){
try{
var r=dojo.render.html;
if(!_173){
if(r.safari){
_171.location=src;
}else{
frames[_171.name].location=src;
}
}else{
var idoc;
if(r.ie){
idoc=_171.contentWindow.document;
}else{
if(r.safari){
idoc=_171.document;
}else{
idoc=_171.contentWindow;
}
}
if(!idoc){
_171.location=src;
return;
}else{
idoc.location.replace(src);
}
}
}
catch(e){
dojo.debug(e);
dojo.debug("setIFrameSrc: "+e);
}
};
dojo.provide("dojo.lang.array");
dojo.lang.has=function(obj,name){
try{
return typeof obj[name]!="undefined";
}
catch(e){
return false;
}
};
dojo.lang.isEmpty=function(obj){
if(dojo.lang.isObject(obj)){
var tmp={};
var _17a=0;
for(var x in obj){
if(obj[x]&&(!tmp[x])){
_17a++;
break;
}
}
return _17a==0;
}else{
if(dojo.lang.isArrayLike(obj)||dojo.lang.isString(obj)){
return obj.length==0;
}
}
};
dojo.lang.map=function(arr,obj,_17e){
var _17f=dojo.lang.isString(arr);
if(_17f){
arr=arr.split("");
}
if(dojo.lang.isFunction(obj)&&(!_17e)){
_17e=obj;
obj=dj_global;
}else{
if(dojo.lang.isFunction(obj)&&_17e){
var _180=obj;
obj=_17e;
_17e=_180;
}
}
if(Array.map){
var _181=Array.map(arr,_17e,obj);
}else{
var _181=[];
for(var i=0;i<arr.length;++i){
_181.push(_17e.call(obj,arr[i]));
}
}
if(_17f){
return _181.join("");
}else{
return _181;
}
};
dojo.lang.reduce=function(arr,_184,obj,_186){
var _187=_184;
var ob=obj?obj:dj_global;
dojo.lang.map(arr,function(val){
_187=_186.call(ob,_187,val);
});
return _187;
};
dojo.lang.forEach=function(_18a,_18b,_18c){
if(dojo.lang.isString(_18a)){
_18a=_18a.split("");
}
if(Array.forEach){
Array.forEach(_18a,_18b,_18c);
}else{
if(!_18c){
_18c=dj_global;
}
for(var i=0,l=_18a.length;i<l;i++){
_18b.call(_18c,_18a[i],i,_18a);
}
}
};
dojo.lang._everyOrSome=function(_18f,arr,_191,_192){
if(dojo.lang.isString(arr)){
arr=arr.split("");
}
if(Array.every){
return Array[_18f?"every":"some"](arr,_191,_192);
}else{
if(!_192){
_192=dj_global;
}
for(var i=0,l=arr.length;i<l;i++){
var _195=_191.call(_192,arr[i],i,arr);
if(_18f&&!_195){
return false;
}else{
if((!_18f)&&(_195)){
return true;
}
}
}
return Boolean(_18f);
}
};
dojo.lang.every=function(arr,_197,_198){
return this._everyOrSome(true,arr,_197,_198);
};
dojo.lang.some=function(arr,_19a,_19b){
return this._everyOrSome(false,arr,_19a,_19b);
};
dojo.lang.filter=function(arr,_19d,_19e){
var _19f=dojo.lang.isString(arr);
if(_19f){
arr=arr.split("");
}
var _1a0;
if(Array.filter){
_1a0=Array.filter(arr,_19d,_19e);
}else{
if(!_19e){
if(arguments.length>=3){
dojo.raise("thisObject doesn't exist!");
}
_19e=dj_global;
}
_1a0=[];
for(var i=0;i<arr.length;i++){
if(_19d.call(_19e,arr[i],i,arr)){
_1a0.push(arr[i]);
}
}
}
if(_19f){
return _1a0.join("");
}else{
return _1a0;
}
};
dojo.lang.unnest=function(){
var out=[];
for(var i=0;i<arguments.length;i++){
if(dojo.lang.isArrayLike(arguments[i])){
var add=dojo.lang.unnest.apply(this,arguments[i]);
out=out.concat(add);
}else{
out.push(arguments[i]);
}
}
return out;
};
dojo.lang.toArray=function(_1a5,_1a6){
var _1a7=[];
for(var i=_1a6||0;i<_1a5.length;i++){
_1a7.push(_1a5[i]);
}
return _1a7;
};
dojo.provide("dojo.lang.func");
dojo.lang.hitch=function(_1a9,_1aa){
var fcn=(dojo.lang.isString(_1aa)?_1a9[_1aa]:_1aa)||function(){
};
return function(){
return fcn.apply(_1a9,arguments);
};
};
dojo.lang.anonCtr=0;
dojo.lang.anon={};
dojo.lang.nameAnonFunc=function(_1ac,_1ad,_1ae){
var nso=(_1ad||dojo.lang.anon);
if((_1ae)||((dj_global["djConfig"])&&(djConfig["slowAnonFuncLookups"]==true))){
for(var x in nso){
try{
if(nso[x]===_1ac){
return x;
}
}
catch(e){
}
}
}
var ret="__"+dojo.lang.anonCtr++;
while(typeof nso[ret]!="undefined"){
ret="__"+dojo.lang.anonCtr++;
}
nso[ret]=_1ac;
return ret;
};
dojo.lang.forward=function(_1b2){
return function(){
return this[_1b2].apply(this,arguments);
};
};
dojo.lang.curry=function(ns,func){
var _1b5=[];
ns=ns||dj_global;
if(dojo.lang.isString(func)){
func=ns[func];
}
for(var x=2;x<arguments.length;x++){
_1b5.push(arguments[x]);
}
var _1b7=(func["__preJoinArity"]||func.length)-_1b5.length;
function gather(_1b8,_1b9,_1ba){
var _1bb=_1ba;
var _1bc=_1b9.slice(0);
for(var x=0;x<_1b8.length;x++){
_1bc.push(_1b8[x]);
}
_1ba=_1ba-_1b8.length;
if(_1ba<=0){
var res=func.apply(ns,_1bc);
_1ba=_1bb;
return res;
}else{
return function(){
return gather(arguments,_1bc,_1ba);
};
}
}
return gather([],_1b5,_1b7);
};
dojo.lang.curryArguments=function(ns,func,args,_1c2){
var _1c3=[];
var x=_1c2||0;
for(x=_1c2;x<args.length;x++){
_1c3.push(args[x]);
}
return dojo.lang.curry.apply(dojo.lang,[ns,func].concat(_1c3));
};
dojo.lang.tryThese=function(){
for(var x=0;x<arguments.length;x++){
try{
if(typeof arguments[x]=="function"){
var ret=(arguments[x]());
if(ret){
return ret;
}
}
}
catch(e){
dojo.debug(e);
}
}
};
dojo.lang.delayThese=function(farr,cb,_1c9,_1ca){
if(!farr.length){
if(typeof _1ca=="function"){
_1ca();
}
return;
}
if((typeof _1c9=="undefined")&&(typeof cb=="number")){
_1c9=cb;
cb=function(){
};
}else{
if(!cb){
cb=function(){
};
if(!_1c9){
_1c9=0;
}
}
}
setTimeout(function(){
(farr.shift())();
cb();
dojo.lang.delayThese(farr,cb,_1c9,_1ca);
},_1c9);
};
dojo.provide("dojo.string.extras");
dojo.string.substituteParams=function(_1cb,hash){
var map=(typeof hash=="object")?hash:dojo.lang.toArray(arguments,1);
return _1cb.replace(/\%\{(\w+)\}/g,function(_1ce,key){
if(typeof (map[key])!="undefined"&&map[key]!=null){
return map[key];
}
dojo.raise("Substitution not found: "+key);
});
};
dojo.string.capitalize=function(str){
if(!dojo.lang.isString(str)){
return "";
}
if(arguments.length==0){
str=this;
}
var _1d1=str.split(" ");
for(var i=0;i<_1d1.length;i++){
_1d1[i]=_1d1[i].charAt(0).toUpperCase()+_1d1[i].substring(1);
}
return _1d1.join(" ");
};
dojo.string.isBlank=function(str){
if(!dojo.lang.isString(str)){
return true;
}
return (dojo.string.trim(str).length==0);
};
dojo.string.encodeAscii=function(str){
if(!dojo.lang.isString(str)){
return str;
}
var ret="";
var _1d6=escape(str);
var _1d7,re=/%u([0-9A-F]{4})/i;
while((_1d7=_1d6.match(re))){
var num=Number("0x"+_1d7[1]);
var _1da=escape("&#"+num+";");
ret+=_1d6.substring(0,_1d7.index)+_1da;
_1d6=_1d6.substring(_1d7.index+_1d7[0].length);
}
ret+=_1d6.replace(/\+/g,"%2B");
return ret;
};
dojo.string.escape=function(type,str){
var args=dojo.lang.toArray(arguments,1);
switch(type.toLowerCase()){
case "xml":
case "html":
case "xhtml":
return dojo.string.escapeXml.apply(this,args);
case "sql":
return dojo.string.escapeSql.apply(this,args);
case "regexp":
case "regex":
return dojo.string.escapeRegExp.apply(this,args);
case "javascript":
case "jscript":
case "js":
return dojo.string.escapeJavaScript.apply(this,args);
case "ascii":
return dojo.string.encodeAscii.apply(this,args);
default:
return str;
}
};
dojo.string.escapeXml=function(str,_1df){
str=str.replace(/&/gm,"&amp;").replace(/</gm,"&lt;").replace(/>/gm,"&gt;").replace(/"/gm,"&quot;");
if(!_1df){
str=str.replace(/'/gm,"&#39;");
}
return str;
};
dojo.string.escapeSql=function(str){
return str.replace(/'/gm,"''");
};
dojo.string.escapeRegExp=function(str){
return str.replace(/\\/gm,"\\\\").replace(/([\f\b\n\t\r[\^$|?*+(){}])/gm,"\\$1");
};
dojo.string.escapeJavaScript=function(str){
return str.replace(/(["'\f\b\n\t\r])/gm,"\\$1");
};
dojo.string.escapeString=function(str){
return ("\""+str.replace(/(["\\])/g,"\\$1")+"\"").replace(/[\f]/g,"\\f").replace(/[\b]/g,"\\b").replace(/[\n]/g,"\\n").replace(/[\t]/g,"\\t").replace(/[\r]/g,"\\r");
};
dojo.string.summary=function(str,len){
if(!len||str.length<=len){
return str;
}
return str.substring(0,len).replace(/\.+$/,"")+"...";
};
dojo.string.endsWith=function(str,end,_1e8){
if(_1e8){
str=str.toLowerCase();
end=end.toLowerCase();
}
if((str.length-end.length)<0){
return false;
}
return str.lastIndexOf(end)==str.length-end.length;
};
dojo.string.endsWithAny=function(str){
for(var i=1;i<arguments.length;i++){
if(dojo.string.endsWith(str,arguments[i])){
return true;
}
}
return false;
};
dojo.string.startsWith=function(str,_1ec,_1ed){
if(_1ed){
str=str.toLowerCase();
_1ec=_1ec.toLowerCase();
}
return str.indexOf(_1ec)==0;
};
dojo.string.startsWithAny=function(str){
for(var i=1;i<arguments.length;i++){
if(dojo.string.startsWith(str,arguments[i])){
return true;
}
}
return false;
};
dojo.string.has=function(str){
for(var i=1;i<arguments.length;i++){
if(str.indexOf(arguments[i])>-1){
return true;
}
}
return false;
};
dojo.string.normalizeNewlines=function(text,_1f3){
if(_1f3=="\n"){
text=text.replace(/\r\n/g,"\n");
text=text.replace(/\r/g,"\n");
}else{
if(_1f3=="\r"){
text=text.replace(/\r\n/g,"\r");
text=text.replace(/\n/g,"\r");
}else{
text=text.replace(/([^\r])\n/g,"$1\r\n").replace(/\r([^\n])/g,"\r\n$1");
}
}
return text;
};
dojo.string.splitEscaped=function(str,_1f5){
var _1f6=[];
for(var i=0,_1f8=0;i<str.length;i++){
if(str.charAt(i)=="\\"){
i++;
continue;
}
if(str.charAt(i)==_1f5){
_1f6.push(str.substring(_1f8,i));
_1f8=i+1;
}
}
_1f6.push(str.substr(_1f8));
return _1f6;
};
dojo.provide("dojo.dom");
dojo.dom.ELEMENT_NODE=1;
dojo.dom.ATTRIBUTE_NODE=2;
dojo.dom.TEXT_NODE=3;
dojo.dom.CDATA_SECTION_NODE=4;
dojo.dom.ENTITY_REFERENCE_NODE=5;
dojo.dom.ENTITY_NODE=6;
dojo.dom.PROCESSING_INSTRUCTION_NODE=7;
dojo.dom.COMMENT_NODE=8;
dojo.dom.DOCUMENT_NODE=9;
dojo.dom.DOCUMENT_TYPE_NODE=10;
dojo.dom.DOCUMENT_FRAGMENT_NODE=11;
dojo.dom.NOTATION_NODE=12;
dojo.dom.dojoml="http://www.dojotoolkit.org/2004/dojoml";
dojo.dom.xmlns={svg:"http://www.w3.org/2000/svg",smil:"http://www.w3.org/2001/SMIL20/",mml:"http://www.w3.org/1998/Math/MathML",cml:"http://www.xml-cml.org",xlink:"http://www.w3.org/1999/xlink",xhtml:"http://www.w3.org/1999/xhtml",xul:"http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul",xbl:"http://www.mozilla.org/xbl",fo:"http://www.w3.org/1999/XSL/Format",xsl:"http://www.w3.org/1999/XSL/Transform",xslt:"http://www.w3.org/1999/XSL/Transform",xi:"http://www.w3.org/2001/XInclude",xforms:"http://www.w3.org/2002/01/xforms",saxon:"http://icl.com/saxon",xalan:"http://xml.apache.org/xslt",xsd:"http://www.w3.org/2001/XMLSchema",dt:"http://www.w3.org/2001/XMLSchema-datatypes",xsi:"http://www.w3.org/2001/XMLSchema-instance",rdf:"http://www.w3.org/1999/02/22-rdf-syntax-ns#",rdfs:"http://www.w3.org/2000/01/rdf-schema#",dc:"http://purl.org/dc/elements/1.1/",dcq:"http://purl.org/dc/qualifiers/1.0","soap-env":"http://schemas.xmlsoap.org/soap/envelope/",wsdl:"http://schemas.xmlsoap.org/wsdl/",AdobeExtensions:"http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"};
dojo.dom.isNode=function(wh){
if(typeof Element=="function"){
try{
return wh instanceof Element;
}
catch(E){
}
}else{
return wh&&!isNaN(wh.nodeType);
}
};
dojo.dom.getUniqueId=function(){
var _1fa=dojo.doc();
do{
var id="dj_unique_"+(++arguments.callee._idIncrement);
}while(_1fa.getElementById(id));
return id;
};
dojo.dom.getUniqueId._idIncrement=0;
dojo.dom.firstElement=dojo.dom.getFirstChildElement=function(_1fc,_1fd){
var node=_1fc.firstChild;
while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE){
node=node.nextSibling;
}
if(_1fd&&node&&node.tagName&&node.tagName.toLowerCase()!=_1fd.toLowerCase()){
node=dojo.dom.nextElement(node,_1fd);
}
return node;
};
dojo.dom.lastElement=dojo.dom.getLastChildElement=function(_1ff,_200){
var node=_1ff.lastChild;
while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE){
node=node.previousSibling;
}
if(_200&&node&&node.tagName&&node.tagName.toLowerCase()!=_200.toLowerCase()){
node=dojo.dom.prevElement(node,_200);
}
return node;
};
dojo.dom.nextElement=dojo.dom.getNextSiblingElement=function(node,_203){
if(!node){
return null;
}
do{
node=node.nextSibling;
}while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE);
if(node&&_203&&_203.toLowerCase()!=node.tagName.toLowerCase()){
return dojo.dom.nextElement(node,_203);
}
return node;
};
dojo.dom.prevElement=dojo.dom.getPreviousSiblingElement=function(node,_205){
if(!node){
return null;
}
if(_205){
_205=_205.toLowerCase();
}
do{
node=node.previousSibling;
}while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE);
if(node&&_205&&_205.toLowerCase()!=node.tagName.toLowerCase()){
return dojo.dom.prevElement(node,_205);
}
return node;
};
dojo.dom.moveChildren=function(_206,_207,trim){
var _209=0;
if(trim){
while(_206.hasChildNodes()&&_206.firstChild.nodeType==dojo.dom.TEXT_NODE){
_206.removeChild(_206.firstChild);
}
while(_206.hasChildNodes()&&_206.lastChild.nodeType==dojo.dom.TEXT_NODE){
_206.removeChild(_206.lastChild);
}
}
while(_206.hasChildNodes()){
_207.appendChild(_206.firstChild);
_209++;
}
return _209;
};
dojo.dom.copyChildren=function(_20a,_20b,trim){
var _20d=_20a.cloneNode(true);
return this.moveChildren(_20d,_20b,trim);
};
dojo.dom.removeChildren=function(node){
var _20f=node.childNodes.length;
while(node.hasChildNodes()){
node.removeChild(node.firstChild);
}
return _20f;
};
dojo.dom.replaceChildren=function(node,_211){
dojo.dom.removeChildren(node);
node.appendChild(_211);
};
dojo.dom.removeNode=function(node){
if(node&&node.parentNode){
return node.parentNode.removeChild(node);
}
};
dojo.dom.getAncestors=function(node,_214,_215){
var _216=[];
var _217=(_214&&(_214 instanceof Function||typeof _214=="function"));
while(node){
if(!_217||_214(node)){
_216.push(node);
}
if(_215&&_216.length>0){
return _216[0];
}
node=node.parentNode;
}
if(_215){
return null;
}
return _216;
};
dojo.dom.getAncestorsByTag=function(node,tag,_21a){
tag=tag.toLowerCase();
return dojo.dom.getAncestors(node,function(el){
return ((el.tagName)&&(el.tagName.toLowerCase()==tag));
},_21a);
};
dojo.dom.getFirstAncestorByTag=function(node,tag){
return dojo.dom.getAncestorsByTag(node,tag,true);
};
dojo.dom.isDescendantOf=function(node,_21f,_220){
if(_220&&node){
node=node.parentNode;
}
while(node){
if(node==_21f){
return true;
}
node=node.parentNode;
}
return false;
};
dojo.dom.innerXML=function(node){
if(node.innerXML){
return node.innerXML;
}else{
if(node.xml){
return node.xml;
}else{
if(typeof XMLSerializer!="undefined"){
return (new XMLSerializer()).serializeToString(node);
}
}
}
};
dojo.dom.createDocument=function(){
var doc=null;
var _223=dojo.doc();
if(!dj_undef("ActiveXObject")){
var _224=["MSXML2","Microsoft","MSXML","MSXML3"];
for(var i=0;i<_224.length;i++){
try{
doc=new ActiveXObject(_224[i]+".XMLDOM");
}
catch(e){
}
if(doc){
break;
}
}
}else{
if((_223.implementation)&&(_223.implementation.createDocument)){
doc=_223.implementation.createDocument("","",null);
}
}
return doc;
};
dojo.dom.createDocumentFromText=function(str,_227){
if(!_227){
_227="text/xml";
}
if(!dj_undef("DOMParser")){
var _228=new DOMParser();
return _228.parseFromString(str,_227);
}else{
if(!dj_undef("ActiveXObject")){
var _229=dojo.dom.createDocument();
if(_229){
_229.async=false;
_229.loadXML(str);
return _229;
}else{
dojo.debug("toXml didn't work?");
}
}else{
var _22a=dojo.doc();
if(_22a.createElement){
var tmp=_22a.createElement("xml");
tmp.innerHTML=str;
if(_22a.implementation&&_22a.implementation.createDocument){
var _22c=_22a.implementation.createDocument("foo","",null);
for(var i=0;i<tmp.childNodes.length;i++){
_22c.importNode(tmp.childNodes.item(i),true);
}
return _22c;
}
return ((tmp.document)&&(tmp.document.firstChild?tmp.document.firstChild:tmp));
}
}
}
return null;
};
dojo.dom.prependChild=function(node,_22f){
if(_22f.firstChild){
_22f.insertBefore(node,_22f.firstChild);
}else{
_22f.appendChild(node);
}
return true;
};
dojo.dom.insertBefore=function(node,ref,_232){
if(_232!=true&&(node===ref||node.nextSibling===ref)){
return false;
}
var _233=ref.parentNode;
_233.insertBefore(node,ref);
return true;
};
dojo.dom.insertAfter=function(node,ref,_236){
var pn=ref.parentNode;
if(ref==pn.lastChild){
if((_236!=true)&&(node===ref)){
return false;
}
pn.appendChild(node);
}else{
return this.insertBefore(node,ref.nextSibling,_236);
}
return true;
};
dojo.dom.insertAtPosition=function(node,ref,_23a){
if((!node)||(!ref)||(!_23a)){
return false;
}
switch(_23a.toLowerCase()){
case "before":
return dojo.dom.insertBefore(node,ref);
case "after":
return dojo.dom.insertAfter(node,ref);
case "first":
if(ref.firstChild){
return dojo.dom.insertBefore(node,ref.firstChild);
}else{
ref.appendChild(node);
return true;
}
break;
default:
ref.appendChild(node);
return true;
}
};
dojo.dom.insertAtIndex=function(node,_23c,_23d){
var _23e=_23c.childNodes;
if(!_23e.length){
_23c.appendChild(node);
return true;
}
var _23f=null;
for(var i=0;i<_23e.length;i++){
var _241=_23e.item(i)["getAttribute"]?parseInt(_23e.item(i).getAttribute("dojoinsertionindex")):-1;
if(_241<_23d){
_23f=_23e.item(i);
}
}
if(_23f){
return dojo.dom.insertAfter(node,_23f);
}else{
return dojo.dom.insertBefore(node,_23e.item(0));
}
};
dojo.dom.textContent=function(node,text){
if(arguments.length>1){
var _244=dojo.doc();
dojo.dom.replaceChildren(node,_244.createTextNode(text));
return text;
}else{
if(node.textContent!=undefined){
return node.textContent;
}
var _245="";
if(node==null){
return _245;
}
for(var i=0;i<node.childNodes.length;i++){
switch(node.childNodes[i].nodeType){
case 1:
case 5:
_245+=dojo.dom.textContent(node.childNodes[i]);
break;
case 3:
case 2:
case 4:
_245+=node.childNodes[i].nodeValue;
break;
default:
break;
}
}
return _245;
}
};
dojo.dom.hasParent=function(node){
return node&&node.parentNode&&dojo.dom.isNode(node.parentNode);
};
dojo.dom.isTag=function(node){
if(node&&node.tagName){
for(var i=1;i<arguments.length;i++){
if(node.tagName==String(arguments[i])){
return String(arguments[i]);
}
}
}
return "";
};
dojo.dom.setAttributeNS=function(elem,_24b,_24c,_24d){
if(elem==null||((elem==undefined)&&(typeof elem=="undefined"))){
dojo.raise("No element given to dojo.dom.setAttributeNS");
}
if(!((elem.setAttributeNS==undefined)&&(typeof elem.setAttributeNS=="undefined"))){
elem.setAttributeNS(_24b,_24c,_24d);
}else{
var _24e=elem.ownerDocument;
var _24f=_24e.createNode(2,_24c,_24b);
_24f.nodeValue=_24d;
elem.setAttributeNode(_24f);
}
};
dojo.provide("dojo.undo.browser");
try{
if((!djConfig["preventBackButtonFix"])&&(!dojo.hostenv.post_load_)){
document.write("<iframe style='border: 0px; width: 1px; height: 1px; position: absolute; bottom: 0px; right: 0px; visibility: visible;' name='djhistory' id='djhistory' src='"+(dojo.hostenv.getBaseScriptUri()+"iframe_history.html")+"'></iframe>");
}
}
catch(e){
}
if(dojo.render.html.opera){
dojo.debug("Opera is not supported with dojo.undo.browser, so back/forward detection will not work.");
}
dojo.undo.browser={initialHref:window.location.href,initialHash:window.location.hash,moveForward:false,historyStack:[],forwardStack:[],historyIframe:null,bookmarkAnchor:null,locationTimer:null,setInitialState:function(args){
this.initialState=this._createState(this.initialHref,args,this.initialHash);
},addToHistory:function(args){
this.forwardStack=[];
var hash=null;
var url=null;
if(!this.historyIframe){
this.historyIframe=window.frames["djhistory"];
}
if(!this.bookmarkAnchor){
this.bookmarkAnchor=document.createElement("a");
dojo.body().appendChild(this.bookmarkAnchor);
this.bookmarkAnchor.style.display="none";
}
if(args["changeUrl"]){
hash="#"+((args["changeUrl"]!==true)?args["changeUrl"]:(new Date()).getTime());
if(this.historyStack.length==0&&this.initialState.urlHash==hash){
this.initialState=this._createState(url,args,hash);
return;
}else{
if(this.historyStack.length>0&&this.historyStack[this.historyStack.length-1].urlHash==hash){
this.historyStack[this.historyStack.length-1]=this._createState(url,args,hash);
return;
}
}
this.changingUrl=true;
setTimeout("window.location.href = '"+hash+"'; dojo.undo.browser.changingUrl = false;",1);
this.bookmarkAnchor.href=hash;
if(dojo.render.html.ie){
url=this._loadIframeHistory();
var _254=args["back"]||args["backButton"]||args["handle"];
var tcb=function(_256){
if(window.location.hash!=""){
setTimeout("window.location.href = '"+hash+"';",1);
}
_254.apply(this,[_256]);
};
if(args["back"]){
args.back=tcb;
}else{
if(args["backButton"]){
args.backButton=tcb;
}else{
if(args["handle"]){
args.handle=tcb;
}
}
}
var _257=args["forward"]||args["forwardButton"]||args["handle"];
var tfw=function(_259){
if(window.location.hash!=""){
window.location.href=hash;
}
if(_257){
_257.apply(this,[_259]);
}
};
if(args["forward"]){
args.forward=tfw;
}else{
if(args["forwardButton"]){
args.forwardButton=tfw;
}else{
if(args["handle"]){
args.handle=tfw;
}
}
}
}else{
if(dojo.render.html.moz){
if(!this.locationTimer){
this.locationTimer=setInterval("dojo.undo.browser.checkLocation();",200);
}
}
}
}else{
url=this._loadIframeHistory();
}
this.historyStack.push(this._createState(url,args,hash));
},checkLocation:function(){
if(!this.changingUrl){
var hsl=this.historyStack.length;
if((window.location.hash==this.initialHash||window.location.href==this.initialHref)&&(hsl==1)){
this.handleBackButton();
return;
}
if(this.forwardStack.length>0){
if(this.forwardStack[this.forwardStack.length-1].urlHash==window.location.hash){
this.handleForwardButton();
return;
}
}
if((hsl>=2)&&(this.historyStack[hsl-2])){
if(this.historyStack[hsl-2].urlHash==window.location.hash){
this.handleBackButton();
return;
}
}
}
},iframeLoaded:function(evt,_25c){
if(!dojo.render.html.opera){
var _25d=this._getUrlQuery(_25c.href);
if(_25d==null){
if(this.historyStack.length==1){
this.handleBackButton();
}
return;
}
if(this.moveForward){
this.moveForward=false;
return;
}
if(this.historyStack.length>=2&&_25d==this._getUrlQuery(this.historyStack[this.historyStack.length-2].url)){
this.handleBackButton();
}else{
if(this.forwardStack.length>0&&_25d==this._getUrlQuery(this.forwardStack[this.forwardStack.length-1].url)){
this.handleForwardButton();
}
}
}
},handleBackButton:function(){
var _25e=this.historyStack.pop();
if(!_25e){
return;
}
var last=this.historyStack[this.historyStack.length-1];
if(!last&&this.historyStack.length==0){
last=this.initialState;
}
if(last){
if(last.kwArgs["back"]){
last.kwArgs["back"]();
}else{
if(last.kwArgs["backButton"]){
last.kwArgs["backButton"]();
}else{
if(last.kwArgs["handle"]){
last.kwArgs.handle("back");
}
}
}
}
this.forwardStack.push(_25e);
},handleForwardButton:function(){
var last=this.forwardStack.pop();
if(!last){
return;
}
if(last.kwArgs["forward"]){
last.kwArgs.forward();
}else{
if(last.kwArgs["forwardButton"]){
last.kwArgs.forwardButton();
}else{
if(last.kwArgs["handle"]){
last.kwArgs.handle("forward");
}
}
}
this.historyStack.push(last);
},_createState:function(url,args,hash){
return {"url":url,"kwArgs":args,"urlHash":hash};
},_getUrlQuery:function(url){
var _265=url.split("?");
if(_265.length<2){
return null;
}else{
return _265[1];
}
},_loadIframeHistory:function(){
var url=dojo.hostenv.getBaseScriptUri()+"iframe_history.html?"+(new Date()).getTime();
this.moveForward=true;
dojo.io.setIFrameSrc(this.historyIframe,url,false);
return url;
}};
dojo.provide("dojo.io.BrowserIO");
dojo.io.checkChildrenForFile=function(node){
var _268=false;
var _269=node.getElementsByTagName("input");
dojo.lang.forEach(_269,function(_26a){
if(_268){
return;
}
if(_26a.getAttribute("type")=="file"){
_268=true;
}
});
return _268;
};
dojo.io.formHasFile=function(_26b){
return dojo.io.checkChildrenForFile(_26b);
};
dojo.io.updateNode=function(node,_26d){
node=dojo.byId(node);
var args=_26d;
if(dojo.lang.isString(_26d)){
args={url:_26d};
}
args.mimetype="text/html";
args.load=function(t,d,e){
while(node.firstChild){
if(dojo["event"]){
try{
dojo.event.browser.clean(node.firstChild);
}
catch(e){
}
}
node.removeChild(node.firstChild);
}
node.innerHTML=d;
};
dojo.io.bind(args);
};
dojo.io.formFilter=function(node){
var type=(node.type||"").toLowerCase();
return !node.disabled&&node.name&&!dojo.lang.inArray(["file","submit","image","reset","button"],type);
};
dojo.io.encodeForm=function(_274,_275,_276){
if((!_274)||(!_274.tagName)||(!_274.tagName.toLowerCase()=="form")){
dojo.raise("Attempted to encode a non-form element.");
}
if(!_276){
_276=dojo.io.formFilter;
}
var enc=/utf/i.test(_275||"")?encodeURIComponent:dojo.string.encodeAscii;
var _278=[];
for(var i=0;i<_274.elements.length;i++){
var elm=_274.elements[i];
if(!elm||elm.tagName.toLowerCase()=="fieldset"||!_276(elm)){
continue;
}
var name=enc(elm.name);
var type=elm.type.toLowerCase();
if(type=="select-multiple"){
for(var j=0;j<elm.options.length;j++){
if(elm.options[j].selected){
_278.push(name+"="+enc(elm.options[j].value));
}
}
}else{
if(dojo.lang.inArray(["radio","checkbox"],type)){
if(elm.checked){
_278.push(name+"="+enc(elm.value));
}
}else{
_278.push(name+"="+enc(elm.value));
}
}
}
var _27e=_274.getElementsByTagName("input");
for(var i=0;i<_27e.length;i++){
var _27f=_27e[i];
if(_27f.type.toLowerCase()=="image"&&_27f.form==_274&&_276(_27f)){
var name=enc(_27f.name);
_278.push(name+"="+enc(_27f.value));
_278.push(name+".x=0");
_278.push(name+".y=0");
}
}
return _278.join("&")+"&";
};
dojo.io.FormBind=function(args){
this.bindArgs={};
if(args&&args.formNode){
this.init(args);
}else{
if(args){
this.init({formNode:args});
}
}
};
dojo.lang.extend(dojo.io.FormBind,{form:null,bindArgs:null,clickedButton:null,init:function(args){
var form=dojo.byId(args.formNode);
if(!form||!form.tagName||form.tagName.toLowerCase()!="form"){
throw new Error("FormBind: Couldn't apply, invalid form");
}else{
if(this.form==form){
return;
}else{
if(this.form){
throw new Error("FormBind: Already applied to a form");
}
}
}
dojo.lang.mixin(this.bindArgs,args);
this.form=form;
this.connect(form,"onsubmit","submit");
for(var i=0;i<form.elements.length;i++){
var node=form.elements[i];
if(node&&node.type&&dojo.lang.inArray(["submit","button"],node.type.toLowerCase())){
this.connect(node,"onclick","click");
}
}
var _285=form.getElementsByTagName("input");
for(var i=0;i<_285.length;i++){
var _286=_285[i];
if(_286.type.toLowerCase()=="image"&&_286.form==form){
this.connect(_286,"onclick","click");
}
}
},onSubmit:function(form){
return true;
},submit:function(e){
e.preventDefault();
if(this.onSubmit(this.form)){
dojo.io.bind(dojo.lang.mixin(this.bindArgs,{formFilter:dojo.lang.hitch(this,"formFilter")}));
}
},click:function(e){
var node=e.currentTarget;
if(node.disabled){
return;
}
this.clickedButton=node;
},formFilter:function(node){
var type=(node.type||"").toLowerCase();
var _28d=false;
if(node.disabled||!node.name){
_28d=false;
}else{
if(dojo.lang.inArray(["submit","button","image"],type)){
if(!this.clickedButton){
this.clickedButton=node;
}
_28d=node==this.clickedButton;
}else{
_28d=!dojo.lang.inArray(["file","submit","reset","button"],type);
}
}
return _28d;
},connect:function(_28e,_28f,_290){
if(dojo.evalObjPath("dojo.event.connect")){
dojo.event.connect(_28e,_28f,this,_290);
}else{
var fcn=dojo.lang.hitch(this,_290);
_28e[_28f]=function(e){
if(!e){
e=window.event;
}
if(!e.currentTarget){
e.currentTarget=e.srcElement;
}
if(!e.preventDefault){
e.preventDefault=function(){
window.event.returnValue=false;
};
}
fcn(e);
};
}
}});
dojo.io.XMLHTTPTransport=new function(){
var _293=this;
var _294={};
this.useCache=false;
this.preventCache=false;
function getCacheKey(url,_296,_297){
return url+"|"+_296+"|"+_297.toLowerCase();
}
function addToCache(url,_299,_29a,http){
_294[getCacheKey(url,_299,_29a)]=http;
}
function getFromCache(url,_29d,_29e){
return _294[getCacheKey(url,_29d,_29e)];
}
this.clearCache=function(){
_294={};
};
function doLoad(_29f,http,url,_2a2,_2a3){
if(((http.status>=200)&&(http.status<300))||(http.status==304)||(location.protocol=="file:"&&(http.status==0||http.status==undefined))||(location.protocol=="chrome:"&&(http.status==0||http.status==undefined))){
var ret;
if(_29f.method.toLowerCase()=="head"){
var _2a5=http.getAllResponseHeaders();
ret={};
ret.toString=function(){
return _2a5;
};
var _2a6=_2a5.split(/[\r\n]+/g);
for(var i=0;i<_2a6.length;i++){
var pair=_2a6[i].match(/^([^:]+)\s*:\s*(.+)$/i);
if(pair){
ret[pair[1]]=pair[2];
}
}
}else{
if(_29f.mimetype=="text/javascript"){
try{
ret=dj_eval(http.responseText);
}
catch(e){
dojo.debug(e);
dojo.debug(http.responseText);
ret=null;
}
}else{
if(_29f.mimetype=="text/json"||_29f.mimetype=="application/json"){
try{
ret=dj_eval("("+http.responseText+")");
}
catch(e){
dojo.debug(e);
dojo.debug(http.responseText);
ret=false;
}
}else{
if((_29f.mimetype=="application/xml")||(_29f.mimetype=="text/xml")){
ret=http.responseXML;
if(!ret||typeof ret=="string"||!http.getResponseHeader("Content-Type")){
ret=dojo.dom.createDocumentFromText(http.responseText);
}
}else{
ret=http.responseText;
}
}
}
}
if(_2a3){
addToCache(url,_2a2,_29f.method,http);
}
_29f[(typeof _29f.load=="function")?"load":"handle"]("load",ret,http,_29f);
}else{
var _2a9=new dojo.io.Error("XMLHttpTransport Error: "+http.status+" "+http.statusText);
_29f[(typeof _29f.error=="function")?"error":"handle"]("error",_2a9,http,_29f);
}
}
function setHeaders(http,_2ab){
if(_2ab["headers"]){
for(var _2ac in _2ab["headers"]){
if(_2ac.toLowerCase()=="content-type"&&!_2ab["contentType"]){
_2ab["contentType"]=_2ab["headers"][_2ac];
}else{
http.setRequestHeader(_2ac,_2ab["headers"][_2ac]);
}
}
}
}
this.inFlight=[];
this.inFlightTimer=null;
this.startWatchingInFlight=function(){
if(!this.inFlightTimer){
this.inFlightTimer=setTimeout("dojo.io.XMLHTTPTransport.watchInFlight();",10);
}
};
this.watchInFlight=function(){
var now=null;
if(!dojo.hostenv._blockAsync&&!_293._blockAsync){
for(var x=this.inFlight.length-1;x>=0;x--){
try{
var tif=this.inFlight[x];
if(!tif||tif.http._aborted||!tif.http.readyState){
this.inFlight.splice(x,1);
continue;
}
if(4==tif.http.readyState){
this.inFlight.splice(x,1);
doLoad(tif.req,tif.http,tif.url,tif.query,tif.useCache);
}else{
if(tif.startTime){
if(!now){
now=(new Date()).getTime();
}
if(tif.startTime+(tif.req.timeoutSeconds*1000)<now){
if(typeof tif.http.abort=="function"){
tif.http.abort();
}
this.inFlight.splice(x,1);
tif.req[(typeof tif.req.timeout=="function")?"timeout":"handle"]("timeout",null,tif.http,tif.req);
}
}
}
}
catch(e){
try{
var _2b0=new dojo.io.Error("XMLHttpTransport.watchInFlight Error: "+e);
tif.req[(typeof tif.req.error=="function")?"error":"handle"]("error",_2b0,tif.http,tif.req);
}
catch(e2){
dojo.debug("XMLHttpTransport error callback failed: "+e2);
}
}
}
}
clearTimeout(this.inFlightTimer);
if(this.inFlight.length==0){
this.inFlightTimer=null;
return;
}
this.inFlightTimer=setTimeout("dojo.io.XMLHTTPTransport.watchInFlight();",10);
};
var _2b1=dojo.hostenv.getXmlhttpObject()?true:false;
this.canHandle=function(_2b2){
return _2b1&&dojo.lang.inArray(["text/plain","text/html","application/xml","text/xml","text/javascript","text/json","application/json"],(_2b2["mimetype"].toLowerCase()||""))&&!(_2b2["formNode"]&&dojo.io.formHasFile(_2b2["formNode"]));
};
this.multipartBoundary="45309FFF-BD65-4d50-99C9-36986896A96F";
this.bind=function(_2b3){
if(!_2b3["url"]){
if(!_2b3["formNode"]&&(_2b3["backButton"]||_2b3["back"]||_2b3["changeUrl"]||_2b3["watchForURL"])&&(!djConfig.preventBackButtonFix)){
dojo.deprecated("Using dojo.io.XMLHTTPTransport.bind() to add to browser history without doing an IO request","Use dojo.undo.browser.addToHistory() instead.","0.4");
dojo.undo.browser.addToHistory(_2b3);
return true;
}
}
var url=_2b3.url;
var _2b5="";
if(_2b3["formNode"]){
var ta=_2b3.formNode.getAttribute("action");
if((ta)&&(!_2b3["url"])){
url=ta;
}
var tp=_2b3.formNode.getAttribute("method");
if((tp)&&(!_2b3["method"])){
_2b3.method=tp;
}
_2b5+=dojo.io.encodeForm(_2b3.formNode,_2b3.encoding,_2b3["formFilter"]);
}
if(url.indexOf("#")>-1){
dojo.debug("Warning: dojo.io.bind: stripping hash values from url:",url);
url=url.split("#")[0];
}
if(_2b3["file"]){
_2b3.method="post";
}
if(!_2b3["method"]){
_2b3.method="get";
}
if(_2b3.method.toLowerCase()=="get"){
_2b3.multipart=false;
}else{
if(_2b3["file"]){
_2b3.multipart=true;
}else{
if(!_2b3["multipart"]){
_2b3.multipart=false;
}
}
}
if(_2b3["backButton"]||_2b3["back"]||_2b3["changeUrl"]){
dojo.undo.browser.addToHistory(_2b3);
}
var _2b8=_2b3["content"]||{};
if(_2b3.sendTransport){
_2b8["dojo.transport"]="xmlhttp";
}
do{
if(_2b3.postContent){
_2b5=_2b3.postContent;
break;
}
if(_2b8){
_2b5+=dojo.io.argsFromMap(_2b8,_2b3.encoding);
}
if(_2b3.method.toLowerCase()=="get"||!_2b3.multipart){
break;
}
var t=[];
if(_2b5.length){
var q=_2b5.split("&");
for(var i=0;i<q.length;++i){
if(q[i].length){
var p=q[i].split("=");
t.push("--"+this.multipartBoundary,"Content-Disposition: form-data; name=\""+p[0]+"\"","",p[1]);
}
}
}
if(_2b3.file){
if(dojo.lang.isArray(_2b3.file)){
for(var i=0;i<_2b3.file.length;++i){
var o=_2b3.file[i];
t.push("--"+this.multipartBoundary,"Content-Disposition: form-data; name=\""+o.name+"\"; filename=\""+("fileName" in o?o.fileName:o.name)+"\"","Content-Type: "+("contentType" in o?o.contentType:"application/octet-stream"),"",o.content);
}
}else{
var o=_2b3.file;
t.push("--"+this.multipartBoundary,"Content-Disposition: form-data; name=\""+o.name+"\"; filename=\""+("fileName" in o?o.fileName:o.name)+"\"","Content-Type: "+("contentType" in o?o.contentType:"application/octet-stream"),"",o.content);
}
}
if(t.length){
t.push("--"+this.multipartBoundary+"--","");
_2b5=t.join("\r\n");
}
}while(false);
var _2be=_2b3["sync"]?false:true;
var _2bf=_2b3["preventCache"]||(this.preventCache==true&&_2b3["preventCache"]!=false);
var _2c0=_2b3["useCache"]==true||(this.useCache==true&&_2b3["useCache"]!=false);
if(!_2bf&&_2c0){
var _2c1=getFromCache(url,_2b5,_2b3.method);
if(_2c1){
doLoad(_2b3,_2c1,url,_2b5,false);
return;
}
}
var http=dojo.hostenv.getXmlhttpObject(_2b3);
var _2c3=false;
if(_2be){
var _2c4=this.inFlight.push({"req":_2b3,"http":http,"url":url,"query":_2b5,"useCache":_2c0,"startTime":_2b3.timeoutSeconds?(new Date()).getTime():0});
this.startWatchingInFlight();
}else{
_293._blockAsync=true;
}
if(_2b3.method.toLowerCase()=="post"){
if(!_2b3.user){
http.open("POST",url,_2be);
}else{
http.open("POST",url,_2be,_2b3.user,_2b3.password);
}
setHeaders(http,_2b3);
http.setRequestHeader("Content-Type",_2b3.multipart?("multipart/form-data; boundary="+this.multipartBoundary):(_2b3.contentType||"application/x-www-form-urlencoded"));
try{
http.send(_2b5);
}
catch(e){
if(typeof http.abort=="function"){
http.abort();
}
doLoad(_2b3,{status:404},url,_2b5,_2c0);
}
}else{
var _2c5=url;
if(_2b5!=""){
_2c5+=(_2c5.indexOf("?")>-1?"&":"?")+_2b5;
}
if(_2bf){
_2c5+=(dojo.string.endsWithAny(_2c5,"?","&")?"":(_2c5.indexOf("?")>-1?"&":"?"))+"dojo.preventCache="+new Date().valueOf();
}
if(!_2b3.user){
http.open(_2b3.method.toUpperCase(),_2c5,_2be);
}else{
http.open(_2b3.method.toUpperCase(),_2c5,_2be,_2b3.user,_2b3.password);
}
setHeaders(http,_2b3);
try{
http.send(null);
}
catch(e){
if(typeof http.abort=="function"){
http.abort();
}
doLoad(_2b3,{status:404},url,_2b5,_2c0);
}
}
if(!_2be){
doLoad(_2b3,http,url,_2b5,_2c0);
_293._blockAsync=false;
}
_2b3.abort=function(){
try{
http._aborted=true;
}
catch(e){
}
return http.abort();
};
return;
};
dojo.io.transports.addTransport("XMLHTTPTransport");
};
dojo.provide("dojo.io.cookie");
dojo.io.cookie.setCookie=function(name,_2c7,days,path,_2ca,_2cb){
var _2cc=-1;
if(typeof days=="number"&&days>=0){
var d=new Date();
d.setTime(d.getTime()+(days*24*60*60*1000));
_2cc=d.toGMTString();
}
_2c7=escape(_2c7);
document.cookie=name+"="+_2c7+";"+(_2cc!=-1?" expires="+_2cc+";":"")+(path?"path="+path:"")+(_2ca?"; domain="+_2ca:"")+(_2cb?"; secure":"");
};
dojo.io.cookie.set=dojo.io.cookie.setCookie;
dojo.io.cookie.getCookie=function(name){
var idx=document.cookie.lastIndexOf(name+"=");
if(idx==-1){
return null;
}
var _2d0=document.cookie.substring(idx+name.length+1);
var end=_2d0.indexOf(";");
if(end==-1){
end=_2d0.length;
}
_2d0=_2d0.substring(0,end);
_2d0=unescape(_2d0);
return _2d0;
};
dojo.io.cookie.get=dojo.io.cookie.getCookie;
dojo.io.cookie.deleteCookie=function(name){
dojo.io.cookie.setCookie(name,"-",0);
};
dojo.io.cookie.setObjectCookie=function(name,obj,days,path,_2d7,_2d8,_2d9){
if(arguments.length==5){
_2d9=_2d7;
_2d7=null;
_2d8=null;
}
var _2da=[],_2db,_2dc="";
if(!_2d9){
_2db=dojo.io.cookie.getObjectCookie(name);
}
if(days>=0){
if(!_2db){
_2db={};
}
for(var prop in obj){
if(prop==null){
delete _2db[prop];
}else{
if(typeof obj[prop]=="string"||typeof obj[prop]=="number"){
_2db[prop]=obj[prop];
}
}
}
prop=null;
for(var prop in _2db){
_2da.push(escape(prop)+"="+escape(_2db[prop]));
}
_2dc=_2da.join("&");
}
dojo.io.cookie.setCookie(name,_2dc,days,path,_2d7,_2d8);
};
dojo.io.cookie.getObjectCookie=function(name){
var _2df=null,_2e0=dojo.io.cookie.getCookie(name);
if(_2e0){
_2df={};
var _2e1=_2e0.split("&");
for(var i=0;i<_2e1.length;i++){
var pair=_2e1[i].split("=");
var _2e4=pair[1];
if(isNaN(_2e4)){
_2e4=unescape(pair[1]);
}
_2df[unescape(pair[0])]=_2e4;
}
}
return _2df;
};
dojo.io.cookie.isSupported=function(){
if(typeof navigator.cookieEnabled!="boolean"){
dojo.io.cookie.setCookie("__TestingYourBrowserForCookieSupport__","CookiesAllowed",90,null);
var _2e5=dojo.io.cookie.getCookie("__TestingYourBrowserForCookieSupport__");
navigator.cookieEnabled=(_2e5=="CookiesAllowed");
if(navigator.cookieEnabled){
this.deleteCookie("__TestingYourBrowserForCookieSupport__");
}
}
return navigator.cookieEnabled;
};
if(!dojo.io.cookies){
dojo.io.cookies=dojo.io.cookie;
}
dojo.provide("dojo.io.*");
dojo.provide("dojo.io");
dojo.deprecated("dojo.io","replaced by dojo.io.*","0.5");
dojo.provide("dojo.event.common");
dojo.event=new function(){
this._canTimeout=dojo.lang.isFunction(dj_global["setTimeout"])||dojo.lang.isAlien(dj_global["setTimeout"]);
function interpolateArgs(args,_2e7){
var dl=dojo.lang;
var ao={srcObj:dj_global,srcFunc:null,adviceObj:dj_global,adviceFunc:null,aroundObj:null,aroundFunc:null,adviceType:(args.length>2)?args[0]:"after",precedence:"last",once:false,delay:null,rate:0,adviceMsg:false};
switch(args.length){
case 0:
return;
case 1:
return;
case 2:
ao.srcFunc=args[0];
ao.adviceFunc=args[1];
break;
case 3:
if((dl.isObject(args[0]))&&(dl.isString(args[1]))&&(dl.isString(args[2]))){
ao.adviceType="after";
ao.srcObj=args[0];
ao.srcFunc=args[1];
ao.adviceFunc=args[2];
}else{
if((dl.isString(args[1]))&&(dl.isString(args[2]))){
ao.srcFunc=args[1];
ao.adviceFunc=args[2];
}else{
if((dl.isObject(args[0]))&&(dl.isString(args[1]))&&(dl.isFunction(args[2]))){
ao.adviceType="after";
ao.srcObj=args[0];
ao.srcFunc=args[1];
var _2ea=dl.nameAnonFunc(args[2],ao.adviceObj,_2e7);
ao.adviceFunc=_2ea;
}else{
if((dl.isFunction(args[0]))&&(dl.isObject(args[1]))&&(dl.isString(args[2]))){
ao.adviceType="after";
ao.srcObj=dj_global;
var _2ea=dl.nameAnonFunc(args[0],ao.srcObj,_2e7);
ao.srcFunc=_2ea;
ao.adviceObj=args[1];
ao.adviceFunc=args[2];
}
}
}
}
break;
case 4:
if((dl.isObject(args[0]))&&(dl.isObject(args[2]))){
ao.adviceType="after";
ao.srcObj=args[0];
ao.srcFunc=args[1];
ao.adviceObj=args[2];
ao.adviceFunc=args[3];
}else{
if((dl.isString(args[0]))&&(dl.isString(args[1]))&&(dl.isObject(args[2]))){
ao.adviceType=args[0];
ao.srcObj=dj_global;
ao.srcFunc=args[1];
ao.adviceObj=args[2];
ao.adviceFunc=args[3];
}else{
if((dl.isString(args[0]))&&(dl.isFunction(args[1]))&&(dl.isObject(args[2]))){
ao.adviceType=args[0];
ao.srcObj=dj_global;
var _2ea=dl.nameAnonFunc(args[1],dj_global,_2e7);
ao.srcFunc=_2ea;
ao.adviceObj=args[2];
ao.adviceFunc=args[3];
}else{
if((dl.isString(args[0]))&&(dl.isObject(args[1]))&&(dl.isString(args[2]))&&(dl.isFunction(args[3]))){
ao.srcObj=args[1];
ao.srcFunc=args[2];
var _2ea=dl.nameAnonFunc(args[3],dj_global,_2e7);
ao.adviceObj=dj_global;
ao.adviceFunc=_2ea;
}else{
if(dl.isObject(args[1])){
ao.srcObj=args[1];
ao.srcFunc=args[2];
ao.adviceObj=dj_global;
ao.adviceFunc=args[3];
}else{
if(dl.isObject(args[2])){
ao.srcObj=dj_global;
ao.srcFunc=args[1];
ao.adviceObj=args[2];
ao.adviceFunc=args[3];
}else{
ao.srcObj=ao.adviceObj=ao.aroundObj=dj_global;
ao.srcFunc=args[1];
ao.adviceFunc=args[2];
ao.aroundFunc=args[3];
}
}
}
}
}
}
break;
case 6:
ao.srcObj=args[1];
ao.srcFunc=args[2];
ao.adviceObj=args[3];
ao.adviceFunc=args[4];
ao.aroundFunc=args[5];
ao.aroundObj=dj_global;
break;
default:
ao.srcObj=args[1];
ao.srcFunc=args[2];
ao.adviceObj=args[3];
ao.adviceFunc=args[4];
ao.aroundObj=args[5];
ao.aroundFunc=args[6];
ao.once=args[7];
ao.delay=args[8];
ao.rate=args[9];
ao.adviceMsg=args[10];
break;
}
if(dl.isFunction(ao.aroundFunc)){
var _2ea=dl.nameAnonFunc(ao.aroundFunc,ao.aroundObj,_2e7);
ao.aroundFunc=_2ea;
}
if(dl.isFunction(ao.srcFunc)){
ao.srcFunc=dl.getNameInObj(ao.srcObj,ao.srcFunc);
}
if(dl.isFunction(ao.adviceFunc)){
ao.adviceFunc=dl.getNameInObj(ao.adviceObj,ao.adviceFunc);
}
if((ao.aroundObj)&&(dl.isFunction(ao.aroundFunc))){
ao.aroundFunc=dl.getNameInObj(ao.aroundObj,ao.aroundFunc);
}
if(!ao.srcObj){
dojo.raise("bad srcObj for srcFunc: "+ao.srcFunc);
}
if(!ao.adviceObj){
dojo.raise("bad adviceObj for adviceFunc: "+ao.adviceFunc);
}
if(!ao.adviceFunc){
dojo.debug("bad adviceFunc for srcFunc: "+ao.srcFunc);
dojo.debugShallow(ao);
}
return ao;
}
this.connect=function(){
if(arguments.length==1){
var ao=arguments[0];
}else{
var ao=interpolateArgs(arguments,true);
}
if(dojo.lang.isString(ao.srcFunc)&&(ao.srcFunc.toLowerCase()=="onkey")){
if(dojo.render.html.ie){
ao.srcFunc="onkeydown";
this.connect(ao);
}
ao.srcFunc="onkeypress";
}
if(dojo.lang.isArray(ao.srcObj)&&ao.srcObj!=""){
var _2ec={};
for(var x in ao){
_2ec[x]=ao[x];
}
var mjps=[];
dojo.lang.forEach(ao.srcObj,function(src){
if((dojo.render.html.capable)&&(dojo.lang.isString(src))){
src=dojo.byId(src);
}
_2ec.srcObj=src;
mjps.push(dojo.event.connect.call(dojo.event,_2ec));
});
return mjps;
}
var mjp=dojo.event.MethodJoinPoint.getForMethod(ao.srcObj,ao.srcFunc);
if(ao.adviceFunc){
var mjp2=dojo.event.MethodJoinPoint.getForMethod(ao.adviceObj,ao.adviceFunc);
}
mjp.kwAddAdvice(ao);
return mjp;
};
this.log=function(a1,a2){
var _2f4;
if((arguments.length==1)&&(typeof a1=="object")){
_2f4=a1;
}else{
_2f4={srcObj:a1,srcFunc:a2};
}
_2f4.adviceFunc=function(){
var _2f5=[];
for(var x=0;x<arguments.length;x++){
_2f5.push(arguments[x]);
}
dojo.debug("("+_2f4.srcObj+")."+_2f4.srcFunc,":",_2f5.join(", "));
};
this.kwConnect(_2f4);
};
this.connectBefore=function(){
var args=["before"];
for(var i=0;i<arguments.length;i++){
args.push(arguments[i]);
}
return this.connect.apply(this,args);
};
this.connectAround=function(){
var args=["around"];
for(var i=0;i<arguments.length;i++){
args.push(arguments[i]);
}
return this.connect.apply(this,args);
};
this.connectOnce=function(){
var ao=interpolateArgs(arguments,true);
ao.once=true;
return this.connect(ao);
};
this._kwConnectImpl=function(_2fc,_2fd){
var fn=(_2fd)?"disconnect":"connect";
if(typeof _2fc["srcFunc"]=="function"){
_2fc.srcObj=_2fc["srcObj"]||dj_global;
var _2ff=dojo.lang.nameAnonFunc(_2fc.srcFunc,_2fc.srcObj,true);
_2fc.srcFunc=_2ff;
}
if(typeof _2fc["adviceFunc"]=="function"){
_2fc.adviceObj=_2fc["adviceObj"]||dj_global;
var _2ff=dojo.lang.nameAnonFunc(_2fc.adviceFunc,_2fc.adviceObj,true);
_2fc.adviceFunc=_2ff;
}
_2fc.srcObj=_2fc["srcObj"]||dj_global;
_2fc.adviceObj=_2fc["adviceObj"]||_2fc["targetObj"]||dj_global;
_2fc.adviceFunc=_2fc["adviceFunc"]||_2fc["targetFunc"];
return dojo.event[fn](_2fc);
};
this.kwConnect=function(_300){
return this._kwConnectImpl(_300,false);
};
this.disconnect=function(){
if(arguments.length==1){
var ao=arguments[0];
}else{
var ao=interpolateArgs(arguments,true);
}
if(!ao.adviceFunc){
return;
}
if(dojo.lang.isString(ao.srcFunc)&&(ao.srcFunc.toLowerCase()=="onkey")){
if(dojo.render.html.ie){
ao.srcFunc="onkeydown";
this.disconnect(ao);
}
ao.srcFunc="onkeypress";
}
var mjp=dojo.event.MethodJoinPoint.getForMethod(ao.srcObj,ao.srcFunc);
return mjp.removeAdvice(ao.adviceObj,ao.adviceFunc,ao.adviceType,ao.once);
};
this.kwDisconnect=function(_303){
return this._kwConnectImpl(_303,true);
};
};
dojo.event.MethodInvocation=function(_304,obj,args){
this.jp_=_304;
this.object=obj;
this.args=[];
for(var x=0;x<args.length;x++){
this.args[x]=args[x];
}
this.around_index=-1;
};
dojo.event.MethodInvocation.prototype.proceed=function(){
this.around_index++;
if(this.around_index>=this.jp_.around.length){
return this.jp_.object[this.jp_.methodname].apply(this.jp_.object,this.args);
}else{
var ti=this.jp_.around[this.around_index];
var mobj=ti[0]||dj_global;
var meth=ti[1];
return mobj[meth].call(mobj,this);
}
};
dojo.event.MethodJoinPoint=function(obj,_30c){
this.object=obj||dj_global;
this.methodname=_30c;
this.methodfunc=this.object[_30c];
this.squelch=false;
};
dojo.event.MethodJoinPoint.getForMethod=function(obj,_30e){
if(!obj){
obj=dj_global;
}
if(!obj[_30e]){
obj[_30e]=function(){
};
if(!obj[_30e]){
dojo.raise("Cannot set do-nothing method on that object "+_30e);
}
}else{
if((!dojo.lang.isFunction(obj[_30e]))&&(!dojo.lang.isAlien(obj[_30e]))){
return null;
}
}
var _30f=_30e+"$joinpoint";
var _310=_30e+"$joinpoint$method";
var _311=obj[_30f];
if(!_311){
var _312=false;
if(dojo.event["browser"]){
if((obj["attachEvent"])||(obj["nodeType"])||(obj["addEventListener"])){
_312=true;
dojo.event.browser.addClobberNodeAttrs(obj,[_30f,_310,_30e]);
}
}
var _313=obj[_30e].length;
obj[_310]=obj[_30e];
_311=obj[_30f]=new dojo.event.MethodJoinPoint(obj,_310);
obj[_30e]=function(){
var args=[];
if((_312)&&(!arguments.length)){
var evt=null;
try{
if(obj.ownerDocument){
evt=obj.ownerDocument.parentWindow.event;
}else{
if(obj.documentElement){
evt=obj.documentElement.ownerDocument.parentWindow.event;
}else{
if(obj.event){
evt=obj.event;
}else{
evt=window.event;
}
}
}
}
catch(e){
evt=window.event;
}
if(evt){
args.push(dojo.event.browser.fixEvent(evt,this));
}
}else{
for(var x=0;x<arguments.length;x++){
if((x==0)&&(_312)&&(dojo.event.browser.isEvent(arguments[x]))){
args.push(dojo.event.browser.fixEvent(arguments[x],this));
}else{
args.push(arguments[x]);
}
}
}
return _311.run.apply(_311,args);
};
obj[_30e].__preJoinArity=_313;
}
return _311;
};
dojo.lang.extend(dojo.event.MethodJoinPoint,{unintercept:function(){
this.object[this.methodname]=this.methodfunc;
this.before=[];
this.after=[];
this.around=[];
},disconnect:dojo.lang.forward("unintercept"),run:function(){
var obj=this.object||dj_global;
var args=arguments;
var _319=[];
for(var x=0;x<args.length;x++){
_319[x]=args[x];
}
var _31b=function(marr){
if(!marr){
dojo.debug("Null argument to unrollAdvice()");
return;
}
var _31d=marr[0]||dj_global;
var _31e=marr[1];
if(!_31d[_31e]){
dojo.raise("function \""+_31e+"\" does not exist on \""+_31d+"\"");
}
var _31f=marr[2]||dj_global;
var _320=marr[3];
var msg=marr[6];
var _322;
var to={args:[],jp_:this,object:obj,proceed:function(){
return _31d[_31e].apply(_31d,to.args);
}};
to.args=_319;
var _324=parseInt(marr[4]);
var _325=((!isNaN(_324))&&(marr[4]!==null)&&(typeof marr[4]!="undefined"));
if(marr[5]){
var rate=parseInt(marr[5]);
var cur=new Date();
var _328=false;
if((marr["last"])&&((cur-marr.last)<=rate)){
if(dojo.event._canTimeout){
if(marr["delayTimer"]){
clearTimeout(marr.delayTimer);
}
var tod=parseInt(rate*2);
var mcpy=dojo.lang.shallowCopy(marr);
marr.delayTimer=setTimeout(function(){
mcpy[5]=0;
_31b(mcpy);
},tod);
}
return;
}else{
marr.last=cur;
}
}
if(_320){
_31f[_320].call(_31f,to);
}else{
if((_325)&&((dojo.render.html)||(dojo.render.svg))){
dj_global["setTimeout"](function(){
if(msg){
_31d[_31e].call(_31d,to);
}else{
_31d[_31e].apply(_31d,args);
}
},_324);
}else{
if(msg){
_31d[_31e].call(_31d,to);
}else{
_31d[_31e].apply(_31d,args);
}
}
}
};
var _32b=function(){
if(this.squelch){
try{
return _31b.apply(this,arguments);
}
catch(e){
dojo.debug(e);
}
}else{
return _31b.apply(this,arguments);
}
};
if((this["before"])&&(this.before.length>0)){
dojo.lang.forEach(this.before.concat(new Array()),_32b);
}
var _32c;
try{
if((this["around"])&&(this.around.length>0)){
var mi=new dojo.event.MethodInvocation(this,obj,args);
_32c=mi.proceed();
}else{
if(this.methodfunc){
_32c=this.object[this.methodname].apply(this.object,args);
}
}
}
catch(e){
if(!this.squelch){
dojo.raise(e);
}
}
if((this["after"])&&(this.after.length>0)){
dojo.lang.forEach(this.after.concat(new Array()),_32b);
}
return (this.methodfunc)?_32c:null;
},getArr:function(kind){
var type="after";
if((typeof kind=="string")&&(kind.indexOf("before")!=-1)){
type="before";
}else{
if(kind=="around"){
type="around";
}
}
if(!this[type]){
this[type]=[];
}
return this[type];
},kwAddAdvice:function(args){
this.addAdvice(args["adviceObj"],args["adviceFunc"],args["aroundObj"],args["aroundFunc"],args["adviceType"],args["precedence"],args["once"],args["delay"],args["rate"],args["adviceMsg"]);
},addAdvice:function(_331,_332,_333,_334,_335,_336,once,_338,rate,_33a){
var arr=this.getArr(_335);
if(!arr){
dojo.raise("bad this: "+this);
}
var ao=[_331,_332,_333,_334,_338,rate,_33a];
if(once){
if(this.hasAdvice(_331,_332,_335,arr)>=0){
return;
}
}
if(_336=="first"){
arr.unshift(ao);
}else{
arr.push(ao);
}
},hasAdvice:function(_33d,_33e,_33f,arr){
if(!arr){
arr=this.getArr(_33f);
}
var ind=-1;
for(var x=0;x<arr.length;x++){
var aao=(typeof _33e=="object")?(new String(_33e)).toString():_33e;
var a1o=(typeof arr[x][1]=="object")?(new String(arr[x][1])).toString():arr[x][1];
if((arr[x][0]==_33d)&&(a1o==aao)){
ind=x;
}
}
return ind;
},removeAdvice:function(_345,_346,_347,once){
var arr=this.getArr(_347);
var ind=this.hasAdvice(_345,_346,_347,arr);
if(ind==-1){
return false;
}
while(ind!=-1){
arr.splice(ind,1);
if(once){
break;
}
ind=this.hasAdvice(_345,_346,_347,arr);
}
return true;
}});
dojo.provide("dojo.event.topic");
dojo.event.topic=new function(){
this.topics={};
this.getTopic=function(_34b){
if(!this.topics[_34b]){
this.topics[_34b]=new this.TopicImpl(_34b);
}
return this.topics[_34b];
};
this.registerPublisher=function(_34c,obj,_34e){
var _34c=this.getTopic(_34c);
_34c.registerPublisher(obj,_34e);
};
this.subscribe=function(_34f,obj,_351){
var _34f=this.getTopic(_34f);
_34f.subscribe(obj,_351);
};
this.unsubscribe=function(_352,obj,_354){
var _352=this.getTopic(_352);
_352.unsubscribe(obj,_354);
};
this.destroy=function(_355){
this.getTopic(_355).destroy();
delete this.topics[_355];
};
this.publishApply=function(_356,args){
var _356=this.getTopic(_356);
_356.sendMessage.apply(_356,args);
};
this.publish=function(_358,_359){
var _358=this.getTopic(_358);
var args=[];
for(var x=1;x<arguments.length;x++){
args.push(arguments[x]);
}
_358.sendMessage.apply(_358,args);
};
};
dojo.event.topic.TopicImpl=function(_35c){
this.topicName=_35c;
this.subscribe=function(_35d,_35e){
var tf=_35e||_35d;
var to=(!_35e)?dj_global:_35d;
return dojo.event.kwConnect({srcObj:this,srcFunc:"sendMessage",adviceObj:to,adviceFunc:tf});
};
this.unsubscribe=function(_361,_362){
var tf=(!_362)?_361:_362;
var to=(!_362)?null:_361;
return dojo.event.kwDisconnect({srcObj:this,srcFunc:"sendMessage",adviceObj:to,adviceFunc:tf});
};
this._getJoinPoint=function(){
return dojo.event.MethodJoinPoint.getForMethod(this,"sendMessage");
};
this.setSquelch=function(_365){
this._getJoinPoint().squelch=_365;
};
this.destroy=function(){
this._getJoinPoint().disconnect();
};
this.registerPublisher=function(_366,_367){
dojo.event.connect(_366,_367,this,"sendMessage");
};
this.sendMessage=function(_368){
};
};
dojo.provide("dojo.event.browser");
dojo._ie_clobber=new function(){
this.clobberNodes=[];
function nukeProp(node,prop){
try{
node[prop]=null;
}
catch(e){
}
try{
delete node[prop];
}
catch(e){
}
try{
node.removeAttribute(prop);
}
catch(e){
}
}
this.clobber=function(_36b){
var na;
var tna;
if(_36b){
tna=_36b.all||_36b.getElementsByTagName("*");
na=[_36b];
for(var x=0;x<tna.length;x++){
if(tna[x]["__doClobber__"]){
na.push(tna[x]);
}
}
}else{
try{
window.onload=null;
}
catch(e){
}
na=(this.clobberNodes.length)?this.clobberNodes:document.all;
}
tna=null;
var _36f={};
for(var i=na.length-1;i>=0;i=i-1){
var el=na[i];
try{
if(el&&el["__clobberAttrs__"]){
for(var j=0;j<el.__clobberAttrs__.length;j++){
nukeProp(el,el.__clobberAttrs__[j]);
}
nukeProp(el,"__clobberAttrs__");
nukeProp(el,"__doClobber__");
}
}
catch(e){
}
}
na=null;
};
};
if(dojo.render.html.ie){
dojo.addOnUnload(function(){
dojo._ie_clobber.clobber();
try{
if((dojo["widget"])&&(dojo.widget["manager"])){
dojo.widget.manager.destroyAll();
}
}
catch(e){
}
try{
window.onload=null;
}
catch(e){
}
try{
window.onunload=null;
}
catch(e){
}
dojo._ie_clobber.clobberNodes=[];
});
}
dojo.event.browser=new function(){
var _373=0;
this.normalizedEventName=function(_374){
switch(_374){
case "CheckboxStateChange":
case "DOMAttrModified":
case "DOMMenuItemActive":
case "DOMMenuItemInactive":
case "DOMMouseScroll":
case "DOMNodeInserted":
case "DOMNodeRemoved":
case "RadioStateChange":
return _374;
break;
default:
return _374.toLowerCase();
break;
}
};
this.clean=function(node){
if(dojo.render.html.ie){
dojo._ie_clobber.clobber(node);
}
};
this.addClobberNode=function(node){
if(!dojo.render.html.ie){
return;
}
if(!node["__doClobber__"]){
node.__doClobber__=true;
dojo._ie_clobber.clobberNodes.push(node);
node.__clobberAttrs__=[];
}
};
this.addClobberNodeAttrs=function(node,_378){
if(!dojo.render.html.ie){
return;
}
this.addClobberNode(node);
for(var x=0;x<_378.length;x++){
node.__clobberAttrs__.push(_378[x]);
}
};
this.removeListener=function(node,_37b,fp,_37d){
if(!_37d){
var _37d=false;
}
_37b=dojo.event.browser.normalizedEventName(_37b);
if((_37b=="onkey")||(_37b=="key")){
if(dojo.render.html.ie){
this.removeListener(node,"onkeydown",fp,_37d);
}
_37b="onkeypress";
}
if(_37b.substr(0,2)=="on"){
_37b=_37b.substr(2);
}
if(node.removeEventListener){
node.removeEventListener(_37b,fp,_37d);
}
};
this.addListener=function(node,_37f,fp,_381,_382){
if(!node){
return;
}
if(!_381){
var _381=false;
}
_37f=dojo.event.browser.normalizedEventName(_37f);
if((_37f=="onkey")||(_37f=="key")){
if(dojo.render.html.ie){
this.addListener(node,"onkeydown",fp,_381,_382);
}
_37f="onkeypress";
}
if(_37f.substr(0,2)!="on"){
_37f="on"+_37f;
}
if(!_382){
var _383=function(evt){
if(!evt){
evt=window.event;
}
var ret=fp(dojo.event.browser.fixEvent(evt,this));
if(_381){
dojo.event.browser.stopEvent(evt);
}
return ret;
};
}else{
_383=fp;
}
if(node.addEventListener){
node.addEventListener(_37f.substr(2),_383,_381);
return _383;
}else{
if(typeof node[_37f]=="function"){
var _386=node[_37f];
node[_37f]=function(e){
_386(e);
return _383(e);
};
}else{
node[_37f]=_383;
}
if(dojo.render.html.ie){
this.addClobberNodeAttrs(node,[_37f]);
}
return _383;
}
};
this.isEvent=function(obj){
return (typeof obj!="undefined")&&(typeof Event!="undefined")&&(obj.eventPhase);
};
this.currentEvent=null;
this.callListener=function(_389,_38a){
if(typeof _389!="function"){
dojo.raise("listener not a function: "+_389);
}
dojo.event.browser.currentEvent.currentTarget=_38a;
return _389.call(_38a,dojo.event.browser.currentEvent);
};
this._stopPropagation=function(){
dojo.event.browser.currentEvent.cancelBubble=true;
};
this._preventDefault=function(){
dojo.event.browser.currentEvent.returnValue=false;
};
this.keys={KEY_BACKSPACE:8,KEY_TAB:9,KEY_CLEAR:12,KEY_ENTER:13,KEY_SHIFT:16,KEY_CTRL:17,KEY_ALT:18,KEY_PAUSE:19,KEY_CAPS_LOCK:20,KEY_ESCAPE:27,KEY_SPACE:32,KEY_PAGE_UP:33,KEY_PAGE_DOWN:34,KEY_END:35,KEY_HOME:36,KEY_LEFT_ARROW:37,KEY_UP_ARROW:38,KEY_RIGHT_ARROW:39,KEY_DOWN_ARROW:40,KEY_INSERT:45,KEY_DELETE:46,KEY_HELP:47,KEY_LEFT_WINDOW:91,KEY_RIGHT_WINDOW:92,KEY_SELECT:93,KEY_NUMPAD_0:96,KEY_NUMPAD_1:97,KEY_NUMPAD_2:98,KEY_NUMPAD_3:99,KEY_NUMPAD_4:100,KEY_NUMPAD_5:101,KEY_NUMPAD_6:102,KEY_NUMPAD_7:103,KEY_NUMPAD_8:104,KEY_NUMPAD_9:105,KEY_NUMPAD_MULTIPLY:106,KEY_NUMPAD_PLUS:107,KEY_NUMPAD_ENTER:108,KEY_NUMPAD_MINUS:109,KEY_NUMPAD_PERIOD:110,KEY_NUMPAD_DIVIDE:111,KEY_F1:112,KEY_F2:113,KEY_F3:114,KEY_F4:115,KEY_F5:116,KEY_F6:117,KEY_F7:118,KEY_F8:119,KEY_F9:120,KEY_F10:121,KEY_F11:122,KEY_F12:123,KEY_F13:124,KEY_F14:125,KEY_F15:126,KEY_NUM_LOCK:144,KEY_SCROLL_LOCK:145};
this.revKeys=[];
for(var key in this.keys){
this.revKeys[this.keys[key]]=key;
}
this.fixEvent=function(evt,_38d){
if(!evt){
if(window["event"]){
evt=window.event;
}
}
if((evt["type"])&&(evt["type"].indexOf("key")==0)){
evt.keys=this.revKeys;
for(var key in this.keys){
evt[key]=this.keys[key];
}
if(evt["type"]=="keydown"&&dojo.render.html.ie){
switch(evt.keyCode){
case evt.KEY_SHIFT:
case evt.KEY_CTRL:
case evt.KEY_ALT:
case evt.KEY_CAPS_LOCK:
case evt.KEY_LEFT_WINDOW:
case evt.KEY_RIGHT_WINDOW:
case evt.KEY_SELECT:
case evt.KEY_NUM_LOCK:
case evt.KEY_SCROLL_LOCK:
case evt.KEY_NUMPAD_0:
case evt.KEY_NUMPAD_1:
case evt.KEY_NUMPAD_2:
case evt.KEY_NUMPAD_3:
case evt.KEY_NUMPAD_4:
case evt.KEY_NUMPAD_5:
case evt.KEY_NUMPAD_6:
case evt.KEY_NUMPAD_7:
case evt.KEY_NUMPAD_8:
case evt.KEY_NUMPAD_9:
case evt.KEY_NUMPAD_PERIOD:
break;
case evt.KEY_NUMPAD_MULTIPLY:
case evt.KEY_NUMPAD_PLUS:
case evt.KEY_NUMPAD_ENTER:
case evt.KEY_NUMPAD_MINUS:
case evt.KEY_NUMPAD_DIVIDE:
break;
case evt.KEY_PAUSE:
case evt.KEY_TAB:
case evt.KEY_BACKSPACE:
case evt.KEY_ENTER:
case evt.KEY_ESCAPE:
case evt.KEY_PAGE_UP:
case evt.KEY_PAGE_DOWN:
case evt.KEY_END:
case evt.KEY_HOME:
case evt.KEY_LEFT_ARROW:
case evt.KEY_UP_ARROW:
case evt.KEY_RIGHT_ARROW:
case evt.KEY_DOWN_ARROW:
case evt.KEY_INSERT:
case evt.KEY_DELETE:
case evt.KEY_F1:
case evt.KEY_F2:
case evt.KEY_F3:
case evt.KEY_F4:
case evt.KEY_F5:
case evt.KEY_F6:
case evt.KEY_F7:
case evt.KEY_F8:
case evt.KEY_F9:
case evt.KEY_F10:
case evt.KEY_F11:
case evt.KEY_F12:
case evt.KEY_F12:
case evt.KEY_F13:
case evt.KEY_F14:
case evt.KEY_F15:
case evt.KEY_CLEAR:
case evt.KEY_HELP:
evt.key=evt.keyCode;
break;
default:
if(evt.ctrlKey||evt.altKey){
var _38f=evt.keyCode;
if(_38f>=65&&_38f<=90&&evt.shiftKey==false){
_38f+=32;
}
if(_38f>=1&&_38f<=26&&evt.ctrlKey){
_38f+=96;
}
evt.key=String.fromCharCode(_38f);
}
}
}else{
if(evt["type"]=="keypress"){
if(dojo.render.html.opera){
if(evt.which==0){
evt.key=evt.keyCode;
}else{
if(evt.which>0){
switch(evt.which){
case evt.KEY_SHIFT:
case evt.KEY_CTRL:
case evt.KEY_ALT:
case evt.KEY_CAPS_LOCK:
case evt.KEY_NUM_LOCK:
case evt.KEY_SCROLL_LOCK:
break;
case evt.KEY_PAUSE:
case evt.KEY_TAB:
case evt.KEY_BACKSPACE:
case evt.KEY_ENTER:
case evt.KEY_ESCAPE:
evt.key=evt.which;
break;
default:
var _38f=evt.which;
if((evt.ctrlKey||evt.altKey||evt.metaKey)&&(evt.which>=65&&evt.which<=90&&evt.shiftKey==false)){
_38f+=32;
}
evt.key=String.fromCharCode(_38f);
}
}
}
}else{
if(dojo.render.html.ie){
if(!evt.ctrlKey&&!evt.altKey&&evt.keyCode>=evt.KEY_SPACE){
evt.key=String.fromCharCode(evt.keyCode);
}
}else{
if(dojo.render.html.safari){
switch(evt.keyCode){
case 63232:
evt.key=evt.KEY_UP_ARROW;
break;
case 63233:
evt.key=evt.KEY_DOWN_ARROW;
break;
case 63234:
evt.key=evt.KEY_LEFT_ARROW;
break;
case 63235:
evt.key=evt.KEY_RIGHT_ARROW;
break;
default:
evt.key=evt.charCode>0?String.fromCharCode(evt.charCode):evt.keyCode;
}
}else{
evt.key=evt.charCode>0?String.fromCharCode(evt.charCode):evt.keyCode;
}
}
}
}
}
}
if(dojo.render.html.ie){
if(!evt.target){
evt.target=evt.srcElement;
}
if(!evt.currentTarget){
evt.currentTarget=(_38d?_38d:evt.srcElement);
}
if(!evt.layerX){
evt.layerX=evt.offsetX;
}
if(!evt.layerY){
evt.layerY=evt.offsetY;
}
var doc=(evt.srcElement&&evt.srcElement.ownerDocument)?evt.srcElement.ownerDocument:document;
var _391=((dojo.render.html.ie55)||(doc["compatMode"]=="BackCompat"))?doc.body:doc.documentElement;
if(!evt.pageX){
evt.pageX=evt.clientX+(_391.scrollLeft||0);
}
if(!evt.pageY){
evt.pageY=evt.clientY+(_391.scrollTop||0);
}
if(evt.type=="mouseover"){
evt.relatedTarget=evt.fromElement;
}
if(evt.type=="mouseout"){
evt.relatedTarget=evt.toElement;
}
this.currentEvent=evt;
evt.callListener=this.callListener;
evt.stopPropagation=this._stopPropagation;
evt.preventDefault=this._preventDefault;
}
return evt;
};
this.stopEvent=function(evt){
if(window.event){
evt.returnValue=false;
evt.cancelBubble=true;
}else{
evt.preventDefault();
evt.stopPropagation();
}
};
};
dojo.provide("dojo.event.*");
dojo.provide("dojo.gfx.color");
dojo.gfx.color.Color=function(r,g,b,a){
if(dojo.lang.isArray(r)){
this.r=r[0];
this.g=r[1];
this.b=r[2];
this.a=r[3]||1;
}else{
if(dojo.lang.isString(r)){
var rgb=dojo.gfx.color.extractRGB(r);
this.r=rgb[0];
this.g=rgb[1];
this.b=rgb[2];
this.a=g||1;
}else{
if(r instanceof dojo.gfx.color.Color){
this.r=r.r;
this.b=r.b;
this.g=r.g;
this.a=r.a;
}else{
this.r=r;
this.g=g;
this.b=b;
this.a=a;
}
}
}
};
dojo.gfx.color.Color.fromArray=function(arr){
return new dojo.gfx.color.Color(arr[0],arr[1],arr[2],arr[3]);
};
dojo.extend(dojo.gfx.color.Color,{toRgb:function(_399){
if(_399){
return this.toRgba();
}else{
return [this.r,this.g,this.b];
}
},toRgba:function(){
return [this.r,this.g,this.b,this.a];
},toHex:function(){
return dojo.gfx.color.rgb2hex(this.toRgb());
},toCss:function(){
return "rgb("+this.toRgb().join()+")";
},toString:function(){
return this.toHex();
},blend:function(_39a,_39b){
var rgb=null;
if(dojo.lang.isArray(_39a)){
rgb=_39a;
}else{
if(_39a instanceof dojo.gfx.color.Color){
rgb=_39a.toRgb();
}else{
rgb=new dojo.gfx.color.Color(_39a).toRgb();
}
}
return dojo.gfx.color.blend(this.toRgb(),rgb,_39b);
}});
dojo.gfx.color.named={white:[255,255,255],black:[0,0,0],red:[255,0,0],green:[0,255,0],lime:[0,255,0],blue:[0,0,255],navy:[0,0,128],gray:[128,128,128],silver:[192,192,192]};
dojo.gfx.color.blend=function(a,b,_39f){
if(typeof a=="string"){
return dojo.gfx.color.blendHex(a,b,_39f);
}
if(!_39f){
_39f=0;
}
_39f=Math.min(Math.max(-1,_39f),1);
_39f=((_39f+1)/2);
var c=[];
for(var x=0;x<3;x++){
c[x]=parseInt(b[x]+((a[x]-b[x])*_39f));
}
return c;
};
dojo.gfx.color.blendHex=function(a,b,_3a4){
return dojo.gfx.color.rgb2hex(dojo.gfx.color.blend(dojo.gfx.color.hex2rgb(a),dojo.gfx.color.hex2rgb(b),_3a4));
};
dojo.gfx.color.extractRGB=function(_3a5){
var hex="0123456789abcdef";
_3a5=_3a5.toLowerCase();
if(_3a5.indexOf("rgb")==0){
var _3a7=_3a5.match(/rgba*\((\d+), *(\d+), *(\d+)/i);
var ret=_3a7.splice(1,3);
return ret;
}else{
var _3a9=dojo.gfx.color.hex2rgb(_3a5);
if(_3a9){
return _3a9;
}else{
return dojo.gfx.color.named[_3a5]||[255,255,255];
}
}
};
dojo.gfx.color.hex2rgb=function(hex){
var _3ab="0123456789ABCDEF";
var rgb=new Array(3);
if(hex.indexOf("#")==0){
hex=hex.substring(1);
}
hex=hex.toUpperCase();
if(hex.replace(new RegExp("["+_3ab+"]","g"),"")!=""){
return null;
}
if(hex.length==3){
rgb[0]=hex.charAt(0)+hex.charAt(0);
rgb[1]=hex.charAt(1)+hex.charAt(1);
rgb[2]=hex.charAt(2)+hex.charAt(2);
}else{
rgb[0]=hex.substring(0,2);
rgb[1]=hex.substring(2,4);
rgb[2]=hex.substring(4);
}
for(var i=0;i<rgb.length;i++){
rgb[i]=_3ab.indexOf(rgb[i].charAt(0))*16+_3ab.indexOf(rgb[i].charAt(1));
}
return rgb;
};
dojo.gfx.color.rgb2hex=function(r,g,b){
if(dojo.lang.isArray(r)){
g=r[1]||0;
b=r[2]||0;
r=r[0]||0;
}
var ret=dojo.lang.map([r,g,b],function(x){
x=new Number(x);
var s=x.toString(16);
while(s.length<2){
s="0"+s;
}
return s;
});
ret.unshift("#");
return ret.join("");
};
dojo.provide("dojo.lfx.Animation");
dojo.lfx.Line=function(_3b4,end){
this.start=_3b4;
this.end=end;
if(dojo.lang.isArray(_3b4)){
var diff=[];
dojo.lang.forEach(this.start,function(s,i){
diff[i]=this.end[i]-s;
},this);
this.getValue=function(n){
var res=[];
dojo.lang.forEach(this.start,function(s,i){
res[i]=(diff[i]*n)+s;
},this);
return res;
};
}else{
var diff=end-_3b4;
this.getValue=function(n){
return (diff*n)+this.start;
};
}
};
dojo.lfx.easeDefault=function(n){
if(dojo.render.html.khtml){
return (parseFloat("0.5")+((Math.sin((n+parseFloat("1.5"))*Math.PI))/2));
}else{
return (0.5+((Math.sin((n+1.5)*Math.PI))/2));
}
};
dojo.lfx.easeIn=function(n){
return Math.pow(n,3);
};
dojo.lfx.easeOut=function(n){
return (1-Math.pow(1-n,3));
};
dojo.lfx.easeInOut=function(n){
return ((3*Math.pow(n,2))-(2*Math.pow(n,3)));
};
dojo.lfx.IAnimation=function(){
};
dojo.lang.extend(dojo.lfx.IAnimation,{curve:null,duration:1000,easing:null,repeatCount:0,rate:25,handler:null,beforeBegin:null,onBegin:null,onAnimate:null,onEnd:null,onPlay:null,onPause:null,onStop:null,play:null,pause:null,stop:null,connect:function(evt,_3c3,_3c4){
if(!_3c4){
_3c4=_3c3;
_3c3=this;
}
_3c4=dojo.lang.hitch(_3c3,_3c4);
var _3c5=this[evt]||function(){
};
this[evt]=function(){
var ret=_3c5.apply(this,arguments);
_3c4.apply(this,arguments);
return ret;
};
return this;
},fire:function(evt,args){
if(this[evt]){
this[evt].apply(this,(args||[]));
}
return this;
},repeat:function(_3c9){
this.repeatCount=_3c9;
return this;
},_active:false,_paused:false});
dojo.lfx.Animation=function(_3ca,_3cb,_3cc,_3cd,_3ce,rate){
dojo.lfx.IAnimation.call(this);
if(dojo.lang.isNumber(_3ca)||(!_3ca&&_3cb.getValue)){
rate=_3ce;
_3ce=_3cd;
_3cd=_3cc;
_3cc=_3cb;
_3cb=_3ca;
_3ca=null;
}else{
if(_3ca.getValue||dojo.lang.isArray(_3ca)){
rate=_3cd;
_3ce=_3cc;
_3cd=_3cb;
_3cc=_3ca;
_3cb=null;
_3ca=null;
}
}
if(dojo.lang.isArray(_3cc)){
this.curve=new dojo.lfx.Line(_3cc[0],_3cc[1]);
}else{
this.curve=_3cc;
}
if(_3cb!=null&&_3cb>0){
this.duration=_3cb;
}
if(_3ce){
this.repeatCount=_3ce;
}
if(rate){
this.rate=rate;
}
if(_3ca){
dojo.lang.forEach(["handler","beforeBegin","onBegin","onEnd","onPlay","onStop","onAnimate"],function(item){
if(_3ca[item]){
this.connect(item,_3ca[item]);
}
},this);
}
if(_3cd&&dojo.lang.isFunction(_3cd)){
this.easing=_3cd;
}
};
dojo.inherits(dojo.lfx.Animation,dojo.lfx.IAnimation);
dojo.lang.extend(dojo.lfx.Animation,{_startTime:null,_endTime:null,_timer:null,_percent:0,_startRepeatCount:0,play:function(_3d1,_3d2){
if(_3d2){
clearTimeout(this._timer);
this._active=false;
this._paused=false;
this._percent=0;
}else{
if(this._active&&!this._paused){
return this;
}
}
this.fire("handler",["beforeBegin"]);
this.fire("beforeBegin");
if(_3d1>0){
setTimeout(dojo.lang.hitch(this,function(){
this.play(null,_3d2);
}),_3d1);
return this;
}
this._startTime=new Date().valueOf();
if(this._paused){
this._startTime-=(this.duration*this._percent/100);
}
this._endTime=this._startTime+this.duration;
this._active=true;
this._paused=false;
var step=this._percent/100;
var _3d4=this.curve.getValue(step);
if(this._percent==0){
if(!this._startRepeatCount){
this._startRepeatCount=this.repeatCount;
}
this.fire("handler",["begin",_3d4]);
this.fire("onBegin",[_3d4]);
}
this.fire("handler",["play",_3d4]);
this.fire("onPlay",[_3d4]);
this._cycle();
return this;
},pause:function(){
clearTimeout(this._timer);
if(!this._active){
return this;
}
this._paused=true;
var _3d5=this.curve.getValue(this._percent/100);
this.fire("handler",["pause",_3d5]);
this.fire("onPause",[_3d5]);
return this;
},gotoPercent:function(pct,_3d7){
clearTimeout(this._timer);
this._active=true;
this._paused=true;
this._percent=pct;
if(_3d7){
this.play();
}
return this;
},stop:function(_3d8){
clearTimeout(this._timer);
var step=this._percent/100;
if(_3d8){
step=1;
}
var _3da=this.curve.getValue(step);
this.fire("handler",["stop",_3da]);
this.fire("onStop",[_3da]);
this._active=false;
this._paused=false;
return this;
},status:function(){
if(this._active){
return this._paused?"paused":"playing";
}else{
return "stopped";
}
return this;
},_cycle:function(){
clearTimeout(this._timer);
if(this._active){
var curr=new Date().valueOf();
var step=(curr-this._startTime)/(this._endTime-this._startTime);
if(step>=1){
step=1;
this._percent=100;
}else{
this._percent=step*100;
}
if((this.easing)&&(dojo.lang.isFunction(this.easing))){
step=this.easing(step);
}
var _3dd=this.curve.getValue(step);
this.fire("handler",["animate",_3dd]);
this.fire("onAnimate",[_3dd]);
if(step<1){
this._timer=setTimeout(dojo.lang.hitch(this,"_cycle"),this.rate);
}else{
this._active=false;
this.fire("handler",["end"]);
this.fire("onEnd");
if(this.repeatCount>0){
this.repeatCount--;
this.play(null,true);
}else{
if(this.repeatCount==-1){
this.play(null,true);
}else{
if(this._startRepeatCount){
this.repeatCount=this._startRepeatCount;
this._startRepeatCount=0;
}
}
}
}
}
return this;
}});
dojo.lfx.Combine=function(_3de){
dojo.lfx.IAnimation.call(this);
this._anims=[];
this._animsEnded=0;
var _3df=arguments;
if(_3df.length==1&&(dojo.lang.isArray(_3df[0])||dojo.lang.isArrayLike(_3df[0]))){
_3df=_3df[0];
}
dojo.lang.forEach(_3df,function(anim){
this._anims.push(anim);
anim.connect("onEnd",dojo.lang.hitch(this,"_onAnimsEnded"));
},this);
};
dojo.inherits(dojo.lfx.Combine,dojo.lfx.IAnimation);
dojo.lang.extend(dojo.lfx.Combine,{_animsEnded:0,play:function(_3e1,_3e2){
if(!this._anims.length){
return this;
}
this.fire("beforeBegin");
if(_3e1>0){
setTimeout(dojo.lang.hitch(this,function(){
this.play(null,_3e2);
}),_3e1);
return this;
}
if(_3e2||this._anims[0].percent==0){
this.fire("onBegin");
}
this.fire("onPlay");
this._animsCall("play",null,_3e2);
return this;
},pause:function(){
this.fire("onPause");
this._animsCall("pause");
return this;
},stop:function(_3e3){
this.fire("onStop");
this._animsCall("stop",_3e3);
return this;
},_onAnimsEnded:function(){
this._animsEnded++;
if(this._animsEnded>=this._anims.length){
this.fire("onEnd");
}
return this;
},_animsCall:function(_3e4){
var args=[];
if(arguments.length>1){
for(var i=1;i<arguments.length;i++){
args.push(arguments[i]);
}
}
var _3e7=this;
dojo.lang.forEach(this._anims,function(anim){
anim[_3e4](args);
},_3e7);
return this;
}});
dojo.lfx.Chain=function(_3e9){
dojo.lfx.IAnimation.call(this);
this._anims=[];
this._currAnim=-1;
var _3ea=arguments;
if(_3ea.length==1&&(dojo.lang.isArray(_3ea[0])||dojo.lang.isArrayLike(_3ea[0]))){
_3ea=_3ea[0];
}
var _3eb=this;
dojo.lang.forEach(_3ea,function(anim,i,_3ee){
this._anims.push(anim);
if(i<_3ee.length-1){
anim.connect("onEnd",dojo.lang.hitch(this,"_playNext"));
}else{
anim.connect("onEnd",dojo.lang.hitch(this,function(){
this.fire("onEnd");
}));
}
},this);
};
dojo.inherits(dojo.lfx.Chain,dojo.lfx.IAnimation);
dojo.lang.extend(dojo.lfx.Chain,{_currAnim:-1,play:function(_3ef,_3f0){
if(!this._anims.length){
return this;
}
if(_3f0||!this._anims[this._currAnim]){
this._currAnim=0;
}
var _3f1=this._anims[this._currAnim];
this.fire("beforeBegin");
if(_3ef>0){
setTimeout(dojo.lang.hitch(this,function(){
this.play(null,_3f0);
}),_3ef);
return this;
}
if(_3f1){
if(this._currAnim==0){
this.fire("handler",["begin",this._currAnim]);
this.fire("onBegin",[this._currAnim]);
}
this.fire("onPlay",[this._currAnim]);
_3f1.play(null,_3f0);
}
return this;
},pause:function(){
if(this._anims[this._currAnim]){
this._anims[this._currAnim].pause();
this.fire("onPause",[this._currAnim]);
}
return this;
},playPause:function(){
if(this._anims.length==0){
return this;
}
if(this._currAnim==-1){
this._currAnim=0;
}
var _3f2=this._anims[this._currAnim];
if(_3f2){
if(!_3f2._active||_3f2._paused){
this.play();
}else{
this.pause();
}
}
return this;
},stop:function(){
var _3f3=this._anims[this._currAnim];
if(_3f3){
_3f3.stop();
this.fire("onStop",[this._currAnim]);
}
return _3f3;
},_playNext:function(){
if(this._currAnim==-1||this._anims.length==0){
return this;
}
this._currAnim++;
if(this._anims[this._currAnim]){
this._anims[this._currAnim].play(null,true);
}
return this;
}});
dojo.lfx.combine=function(_3f4){
var _3f5=arguments;
if(dojo.lang.isArray(arguments[0])){
_3f5=arguments[0];
}
if(_3f5.length==1){
return _3f5[0];
}
return new dojo.lfx.Combine(_3f5);
};
dojo.lfx.chain=function(_3f6){
var _3f7=arguments;
if(dojo.lang.isArray(arguments[0])){
_3f7=arguments[0];
}
if(_3f7.length==1){
return _3f7[0];
}
return new dojo.lfx.Chain(_3f7);
};
dojo.provide("dojo.uri.Uri");
dojo.uri=new function(){
this.dojoUri=function(uri){
return new dojo.uri.Uri(dojo.hostenv.getBaseScriptUri(),uri);
};
this.moduleUri=function(_3f9,uri){
var loc=dojo.hostenv.getModulePrefix(_3f9);
if(!loc){
return null;
}
if(loc.lastIndexOf("/")!=loc.length-1){
loc+="/";
}
return new dojo.uri.Uri(dojo.hostenv.getBaseScriptUri()+loc,uri);
};
this.Uri=function(){
var uri=arguments[0];
for(var i=1;i<arguments.length;i++){
if(!arguments[i]){
continue;
}
var _3fe=new dojo.uri.Uri(arguments[i].toString());
var _3ff=new dojo.uri.Uri(uri.toString());
if((_3fe.path=="")&&(_3fe.scheme==null)&&(_3fe.authority==null)&&(_3fe.query==null)){
if(_3fe.fragment!=null){
_3ff.fragment=_3fe.fragment;
}
_3fe=_3ff;
}else{
if(_3fe.scheme==null){
_3fe.scheme=_3ff.scheme;
if(_3fe.authority==null){
_3fe.authority=_3ff.authority;
if(_3fe.path.charAt(0)!="/"){
var path=_3ff.path.substring(0,_3ff.path.lastIndexOf("/")+1)+_3fe.path;
var segs=path.split("/");
for(var j=0;j<segs.length;j++){
if(segs[j]=="."){
if(j==segs.length-1){
segs[j]="";
}else{
segs.splice(j,1);
j--;
}
}else{
if(j>0&&!(j==1&&segs[0]=="")&&segs[j]==".."&&segs[j-1]!=".."){
if(j==segs.length-1){
segs.splice(j,1);
segs[j-1]="";
}else{
segs.splice(j-1,2);
j-=2;
}
}
}
}
_3fe.path=segs.join("/");
}
}
}
}
uri="";
if(_3fe.scheme!=null){
uri+=_3fe.scheme+":";
}
if(_3fe.authority!=null){
uri+="//"+_3fe.authority;
}
uri+=_3fe.path;
if(_3fe.query!=null){
uri+="?"+_3fe.query;
}
if(_3fe.fragment!=null){
uri+="#"+_3fe.fragment;
}
}
this.uri=uri.toString();
var _403="^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\\?([^#]*))?(#(.*))?$";
var r=this.uri.match(new RegExp(_403));
this.scheme=r[2]||(r[1]?"":null);
this.authority=r[4]||(r[3]?"":null);
this.path=r[5];
this.query=r[7]||(r[6]?"":null);
this.fragment=r[9]||(r[8]?"":null);
if(this.authority!=null){
_403="^((([^:]+:)?([^@]+))@)?([^:]*)(:([0-9]+))?$";
r=this.authority.match(new RegExp(_403));
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
dojo.provide("dojo.html.style");
dojo.html.getClass=function(node){
node=dojo.byId(node);
if(!node){
return "";
}
var cs="";
if(node.className){
cs=node.className;
}else{
if(dojo.html.hasAttribute(node,"class")){
cs=dojo.html.getAttribute(node,"class");
}
}
return cs.replace(/^\s+|\s+$/g,"");
};
dojo.html.getClasses=function(node){
var c=dojo.html.getClass(node);
return (c=="")?[]:c.split(/\s+/g);
};
dojo.html.hasClass=function(node,_40a){
return (new RegExp("(^|\\s+)"+_40a+"(\\s+|$)")).test(dojo.html.getClass(node));
};
dojo.html.prependClass=function(node,_40c){
_40c+=" "+dojo.html.getClass(node);
return dojo.html.setClass(node,_40c);
};
dojo.html.addClass=function(node,_40e){
if(dojo.html.hasClass(node,_40e)){
return false;
}
_40e=(dojo.html.getClass(node)+" "+_40e).replace(/^\s+|\s+$/g,"");
return dojo.html.setClass(node,_40e);
};
dojo.html.setClass=function(node,_410){
node=dojo.byId(node);
var cs=new String(_410);
try{
if(typeof node.className=="string"){
node.className=cs;
}else{
if(node.setAttribute){
node.setAttribute("class",_410);
node.className=cs;
}else{
return false;
}
}
}
catch(e){
dojo.debug("dojo.html.setClass() failed",e);
}
return true;
};
dojo.html.removeClass=function(node,_413,_414){
try{
if(!_414){
var _415=dojo.html.getClass(node).replace(new RegExp("(^|\\s+)"+_413+"(\\s+|$)"),"$1$2");
}else{
var _415=dojo.html.getClass(node).replace(_413,"");
}
dojo.html.setClass(node,_415);
}
catch(e){
dojo.debug("dojo.html.removeClass() failed",e);
}
return true;
};
dojo.html.replaceClass=function(node,_417,_418){
dojo.html.removeClass(node,_418);
dojo.html.addClass(node,_417);
};
dojo.html.classMatchType={ContainsAll:0,ContainsAny:1,IsOnly:2};
dojo.html.getElementsByClass=function(_419,_41a,_41b,_41c,_41d){
_41d=false;
var _41e=dojo.doc();
_41a=dojo.byId(_41a)||_41e;
var _41f=_419.split(/\s+/g);
var _420=[];
if(_41c!=1&&_41c!=2){
_41c=0;
}
var _421=new RegExp("(\\s|^)(("+_41f.join(")|(")+"))(\\s|$)");
var _422=_41f.join(" ").length;
var _423=[];
if(!_41d&&_41e.evaluate){
var _424=".//"+(_41b||"*")+"[contains(";
if(_41c!=dojo.html.classMatchType.ContainsAny){
_424+="concat(' ',@class,' '), ' "+_41f.join(" ') and contains(concat(' ',@class,' '), ' ")+" ')";
if(_41c==2){
_424+=" and string-length(@class)="+_422+"]";
}else{
_424+="]";
}
}else{
_424+="concat(' ',@class,' '), ' "+_41f.join(" ') or contains(concat(' ',@class,' '), ' ")+" ')]";
}
var _425=_41e.evaluate(_424,_41a,null,XPathResult.ANY_TYPE,null);
var _426=_425.iterateNext();
while(_426){
try{
_423.push(_426);
_426=_425.iterateNext();
}
catch(e){
break;
}
}
return _423;
}else{
if(!_41b){
_41b="*";
}
_423=_41a.getElementsByTagName(_41b);
var node,i=0;
outer:
while(node=_423[i++]){
var _429=dojo.html.getClasses(node);
if(_429.length==0){
continue outer;
}
var _42a=0;
for(var j=0;j<_429.length;j++){
if(_421.test(_429[j])){
if(_41c==dojo.html.classMatchType.ContainsAny){
_420.push(node);
continue outer;
}else{
_42a++;
}
}else{
if(_41c==dojo.html.classMatchType.IsOnly){
continue outer;
}
}
}
if(_42a==_41f.length){
if((_41c==dojo.html.classMatchType.IsOnly)&&(_42a==_429.length)){
_420.push(node);
}else{
if(_41c==dojo.html.classMatchType.ContainsAll){
_420.push(node);
}
}
}
}
return _420;
}
};
dojo.html.getElementsByClassName=dojo.html.getElementsByClass;
dojo.html.toCamelCase=function(_42c){
var arr=_42c.split("-"),cc=arr[0];
for(var i=1;i<arr.length;i++){
cc+=arr[i].charAt(0).toUpperCase()+arr[i].substring(1);
}
return cc;
};
dojo.html.toSelectorCase=function(_430){
return _430.replace(/([A-Z])/g,"-$1").toLowerCase();
};
dojo.html.getComputedStyle=function(node,_432,_433){
node=dojo.byId(node);
var _432=dojo.html.toSelectorCase(_432);
var _434=dojo.html.toCamelCase(_432);
if(!node||!node.style){
return _433;
}else{
if(document.defaultView&&dojo.html.isDescendantOf(node,node.ownerDocument)){
try{
var cs=document.defaultView.getComputedStyle(node,"");
if(cs){
return cs.getPropertyValue(_432);
}
}
catch(e){
if(node.style.getPropertyValue){
return node.style.getPropertyValue(_432);
}else{
return _433;
}
}
}else{
if(node.currentStyle){
return node.currentStyle[_434];
}
}
}
if(node.style.getPropertyValue){
return node.style.getPropertyValue(_432);
}else{
return _433;
}
};
dojo.html.getStyleProperty=function(node,_437){
node=dojo.byId(node);
return (node&&node.style?node.style[dojo.html.toCamelCase(_437)]:undefined);
};
dojo.html.getStyle=function(node,_439){
var _43a=dojo.html.getStyleProperty(node,_439);
return (_43a?_43a:dojo.html.getComputedStyle(node,_439));
};
dojo.html.setStyle=function(node,_43c,_43d){
node=dojo.byId(node);
if(node&&node.style){
var _43e=dojo.html.toCamelCase(_43c);
node.style[_43e]=_43d;
}
};
dojo.html.setStyleText=function(_43f,text){
try{
_43f.style.cssText=text;
}
catch(e){
_43f.setAttribute("style",text);
}
};
dojo.html.copyStyle=function(_441,_442){
if(!_442.style.cssText){
_441.setAttribute("style",_442.getAttribute("style"));
}else{
_441.style.cssText=_442.style.cssText;
}
dojo.html.addClass(_441,dojo.html.getClass(_442));
};
dojo.html.getUnitValue=function(node,_444,_445){
var s=dojo.html.getComputedStyle(node,_444);
if((!s)||((s=="auto")&&(_445))){
return {value:0,units:"px"};
}
var _447=s.match(/(\-?[\d.]+)([a-z%]*)/i);
if(!_447){
return dojo.html.getUnitValue.bad;
}
return {value:Number(_447[1]),units:_447[2].toLowerCase()};
};
dojo.html.getUnitValue.bad={value:NaN,units:""};
dojo.html.getPixelValue=function(node,_449,_44a){
var _44b=dojo.html.getUnitValue(node,_449,_44a);
if(isNaN(_44b.value)){
return 0;
}
if((_44b.value)&&(_44b.units!="px")){
return NaN;
}
return _44b.value;
};
dojo.html.setPositivePixelValue=function(node,_44d,_44e){
if(isNaN(_44e)){
return false;
}
node.style[_44d]=Math.max(0,_44e)+"px";
return true;
};
dojo.html.styleSheet=null;
dojo.html.insertCssRule=function(_44f,_450,_451){
if(!dojo.html.styleSheet){
if(document.createStyleSheet){
dojo.html.styleSheet=document.createStyleSheet();
}else{
if(document.styleSheets[0]){
dojo.html.styleSheet=document.styleSheets[0];
}else{
return null;
}
}
}
if(arguments.length<3){
if(dojo.html.styleSheet.cssRules){
_451=dojo.html.styleSheet.cssRules.length;
}else{
if(dojo.html.styleSheet.rules){
_451=dojo.html.styleSheet.rules.length;
}else{
return null;
}
}
}
if(dojo.html.styleSheet.insertRule){
var rule=_44f+" { "+_450+" }";
return dojo.html.styleSheet.insertRule(rule,_451);
}else{
if(dojo.html.styleSheet.addRule){
return dojo.html.styleSheet.addRule(_44f,_450,_451);
}else{
return null;
}
}
};
dojo.html.removeCssRule=function(_453){
if(!dojo.html.styleSheet){
dojo.debug("no stylesheet defined for removing rules");
return false;
}
if(dojo.render.html.ie){
if(!_453){
_453=dojo.html.styleSheet.rules.length;
dojo.html.styleSheet.removeRule(_453);
}
}else{
if(document.styleSheets[0]){
if(!_453){
_453=dojo.html.styleSheet.cssRules.length;
}
dojo.html.styleSheet.deleteRule(_453);
}
}
return true;
};
dojo.html._insertedCssFiles=[];
dojo.html.insertCssFile=function(URI,doc,_456,_457){
if(!URI){
return;
}
if(!doc){
doc=document;
}
var _458=dojo.hostenv.getText(URI,false,_457);
if(_458===null){
return;
}
_458=dojo.html.fixPathsInCssText(_458,URI);
if(_456){
var idx=-1,node,ent=dojo.html._insertedCssFiles;
for(var i=0;i<ent.length;i++){
if((ent[i].doc==doc)&&(ent[i].cssText==_458)){
idx=i;
node=ent[i].nodeRef;
break;
}
}
if(node){
var _45d=doc.getElementsByTagName("style");
for(var i=0;i<_45d.length;i++){
if(_45d[i]==node){
return;
}
}
dojo.html._insertedCssFiles.shift(idx,1);
}
}
var _45e=dojo.html.insertCssText(_458);
dojo.html._insertedCssFiles.push({"doc":doc,"cssText":_458,"nodeRef":_45e});
if(_45e&&djConfig.isDebug){
_45e.setAttribute("dbgHref",URI);
}
return _45e;
};
dojo.html.insertCssText=function(_45f,doc,URI){
if(!_45f){
return;
}
if(!doc){
doc=document;
}
if(URI){
_45f=dojo.html.fixPathsInCssText(_45f,URI);
}
var _462=doc.createElement("style");
_462.setAttribute("type","text/css");
var head=doc.getElementsByTagName("head")[0];
if(!head){
dojo.debug("No head tag in document, aborting styles");
return;
}else{
head.appendChild(_462);
}
if(_462.styleSheet){
_462.styleSheet.cssText=_45f;
}else{
var _464=doc.createTextNode(_45f);
_462.appendChild(_464);
}
return _462;
};
dojo.html.fixPathsInCssText=function(_465,URI){
function iefixPathsInCssText(){
var _467=/AlphaImageLoader\(src\=['"]([\t\s\w()\/.\\'"-:#=&?~]*)['"]/;
while(_468=_467.exec(_465)){
url=_468[1].replace(_46a,"$2");
if(!_46b.exec(url)){
url=(new dojo.uri.Uri(URI,url).toString());
}
str+=_465.substring(0,_468.index)+"AlphaImageLoader(src='"+url+"'";
_465=_465.substr(_468.index+_468[0].length);
}
return str+_465;
}
if(!_465||!URI){
return;
}
var _468,str="",url="";
var _46d=/url\(\s*([\t\s\w()\/.\\'"-:#=&?]+)\s*\)/;
var _46b=/(file|https?|ftps?):\/\//;
var _46a=/^[\s]*(['"]?)([\w()\/.\\'"-:#=&?]*)\1[\s]*?$/;
if(dojo.render.html.ie55||dojo.render.html.ie60){
_465=iefixPathsInCssText();
}
while(_468=_46d.exec(_465)){
url=_468[1].replace(_46a,"$2");
if(!_46b.exec(url)){
url=(new dojo.uri.Uri(URI,url).toString());
}
str+=_465.substring(0,_468.index)+"url("+url+")";
_465=_465.substr(_468.index+_468[0].length);
}
return str+_465;
};
dojo.html.setActiveStyleSheet=function(_46e){
var i=0,a,els=dojo.doc().getElementsByTagName("link");
while(a=els[i++]){
if(a.getAttribute("rel").indexOf("style")!=-1&&a.getAttribute("title")){
a.disabled=true;
if(a.getAttribute("title")==_46e){
a.disabled=false;
}
}
}
};
dojo.html.getActiveStyleSheet=function(){
var i=0,a,els=dojo.doc().getElementsByTagName("link");
while(a=els[i++]){
if(a.getAttribute("rel").indexOf("style")!=-1&&a.getAttribute("title")&&!a.disabled){
return a.getAttribute("title");
}
}
return null;
};
dojo.html.getPreferredStyleSheet=function(){
var i=0,a,els=dojo.doc().getElementsByTagName("link");
while(a=els[i++]){
if(a.getAttribute("rel").indexOf("style")!=-1&&a.getAttribute("rel").indexOf("alt")==-1&&a.getAttribute("title")){
return a.getAttribute("title");
}
}
return null;
};
dojo.html.applyBrowserClass=function(node){
var drh=dojo.render.html;
var _47a={dj_ie:drh.ie,dj_ie55:drh.ie55,dj_ie6:drh.ie60,dj_ie7:drh.ie70,dj_iequirks:drh.ie&&drh.quirks,dj_opera:drh.opera,dj_opera8:drh.opera&&(Math.floor(dojo.render.version)==8),dj_opera9:drh.opera&&(Math.floor(dojo.render.version)==9),dj_khtml:drh.khtml,dj_safari:drh.safari,dj_gecko:drh.mozilla};
for(var p in _47a){
if(_47a[p]){
dojo.html.addClass(node,p);
}
}
};
dojo.provide("dojo.html.display");
dojo.html._toggle=function(node,_47d,_47e){
node=dojo.byId(node);
_47e(node,!_47d(node));
return _47d(node);
};
dojo.html.show=function(node){
node=dojo.byId(node);
if(dojo.html.getStyleProperty(node,"display")=="none"){
dojo.html.setStyle(node,"display",(node.dojoDisplayCache||""));
node.dojoDisplayCache=undefined;
}
};
dojo.html.hide=function(node){
node=dojo.byId(node);
if(typeof node["dojoDisplayCache"]=="undefined"){
var d=dojo.html.getStyleProperty(node,"display");
if(d!="none"){
node.dojoDisplayCache=d;
}
}
dojo.html.setStyle(node,"display","none");
};
dojo.html.setShowing=function(node,_483){
dojo.html[(_483?"show":"hide")](node);
};
dojo.html.isShowing=function(node){
return (dojo.html.getStyleProperty(node,"display")!="none");
};
dojo.html.toggleShowing=function(node){
return dojo.html._toggle(node,dojo.html.isShowing,dojo.html.setShowing);
};
dojo.html.displayMap={tr:"",td:"",th:"",img:"inline",span:"inline",input:"inline",button:"inline"};
dojo.html.suggestDisplayByTagName=function(node){
node=dojo.byId(node);
if(node&&node.tagName){
var tag=node.tagName.toLowerCase();
return (tag in dojo.html.displayMap?dojo.html.displayMap[tag]:"block");
}
};
dojo.html.setDisplay=function(node,_489){
dojo.html.setStyle(node,"display",((_489 instanceof String||typeof _489=="string")?_489:(_489?dojo.html.suggestDisplayByTagName(node):"none")));
};
dojo.html.isDisplayed=function(node){
return (dojo.html.getComputedStyle(node,"display")!="none");
};
dojo.html.toggleDisplay=function(node){
return dojo.html._toggle(node,dojo.html.isDisplayed,dojo.html.setDisplay);
};
dojo.html.setVisibility=function(node,_48d){
dojo.html.setStyle(node,"visibility",((_48d instanceof String||typeof _48d=="string")?_48d:(_48d?"visible":"hidden")));
};
dojo.html.isVisible=function(node){
return (dojo.html.getComputedStyle(node,"visibility")!="hidden");
};
dojo.html.toggleVisibility=function(node){
return dojo.html._toggle(node,dojo.html.isVisible,dojo.html.setVisibility);
};
dojo.html.setOpacity=function(node,_491,_492){
node=dojo.byId(node);
var h=dojo.render.html;
if(!_492){
if(_491>=1){
if(h.ie){
dojo.html.clearOpacity(node);
return;
}else{
_491=0.999999;
}
}else{
if(_491<0){
_491=0;
}
}
}
if(h.ie){
if(node.nodeName.toLowerCase()=="tr"){
var tds=node.getElementsByTagName("td");
for(var x=0;x<tds.length;x++){
tds[x].style.filter="Alpha(Opacity="+_491*100+")";
}
}
node.style.filter="Alpha(Opacity="+_491*100+")";
}else{
if(h.moz){
node.style.opacity=_491;
node.style.MozOpacity=_491;
}else{
if(h.safari){
node.style.opacity=_491;
node.style.KhtmlOpacity=_491;
}else{
node.style.opacity=_491;
}
}
}
};
dojo.html.clearOpacity=function(node){
node=dojo.byId(node);
var ns=node.style;
var h=dojo.render.html;
if(h.ie){
try{
if(node.filters&&node.filters.alpha){
ns.filter="";
}
}
catch(e){
}
}else{
if(h.moz){
ns.opacity=1;
ns.MozOpacity=1;
}else{
if(h.safari){
ns.opacity=1;
ns.KhtmlOpacity=1;
}else{
ns.opacity=1;
}
}
}
};
dojo.html.getOpacity=function(node){
node=dojo.byId(node);
var h=dojo.render.html;
if(h.ie){
var opac=(node.filters&&node.filters.alpha&&typeof node.filters.alpha.opacity=="number"?node.filters.alpha.opacity:100)/100;
}else{
var opac=node.style.opacity||node.style.MozOpacity||node.style.KhtmlOpacity||1;
}
return opac>=0.999999?1:Number(opac);
};
dojo.provide("dojo.html.color");
dojo.html.getBackgroundColor=function(node){
node=dojo.byId(node);
var _49d;
do{
_49d=dojo.html.getStyle(node,"background-color");
if(_49d.toLowerCase()=="rgba(0, 0, 0, 0)"){
_49d="transparent";
}
if(node==document.getElementsByTagName("body")[0]){
node=null;
break;
}
node=node.parentNode;
}while(node&&dojo.lang.inArray(["transparent",""],_49d));
if(_49d=="transparent"){
_49d=[255,255,255,0];
}else{
_49d=dojo.gfx.color.extractRGB(_49d);
}
return _49d;
};
dojo.provide("dojo.html.common");
dojo.lang.mixin(dojo.html,dojo.dom);
dojo.html.body=function(){
dojo.deprecated("dojo.html.body() moved to dojo.body()","0.5");
return dojo.body();
};
dojo.html.getEventTarget=function(evt){
if(!evt){
evt=dojo.global().event||{};
}
var t=(evt.srcElement?evt.srcElement:(evt.target?evt.target:null));
while((t)&&(t.nodeType!=1)){
t=t.parentNode;
}
return t;
};
dojo.html.getViewport=function(){
var _4a0=dojo.global();
var _4a1=dojo.doc();
var w=0;
var h=0;
if(dojo.render.html.mozilla){
w=_4a1.documentElement.clientWidth;
h=_4a0.innerHeight;
}else{
if(!dojo.render.html.opera&&_4a0.innerWidth){
w=_4a0.innerWidth;
h=_4a0.innerHeight;
}else{
if(!dojo.render.html.opera&&dojo.exists(_4a1,"documentElement.clientWidth")){
var w2=_4a1.documentElement.clientWidth;
if(!w||w2&&w2<w){
w=w2;
}
h=_4a1.documentElement.clientHeight;
}else{
if(dojo.body().clientWidth){
w=dojo.body().clientWidth;
h=dojo.body().clientHeight;
}
}
}
}
return {width:w,height:h};
};
dojo.html.getScroll=function(){
var _4a5=dojo.global();
var _4a6=dojo.doc();
var top=_4a5.pageYOffset||_4a6.documentElement.scrollTop||dojo.body().scrollTop||0;
var left=_4a5.pageXOffset||_4a6.documentElement.scrollLeft||dojo.body().scrollLeft||0;
return {top:top,left:left,offset:{x:left,y:top}};
};
dojo.html.getParentByType=function(node,type){
var _4ab=dojo.doc();
var _4ac=dojo.byId(node);
type=type.toLowerCase();
while((_4ac)&&(_4ac.nodeName.toLowerCase()!=type)){
if(_4ac==(_4ab["body"]||_4ab["documentElement"])){
return null;
}
_4ac=_4ac.parentNode;
}
return _4ac;
};
dojo.html.getAttribute=function(node,attr){
node=dojo.byId(node);
if((!node)||(!node.getAttribute)){
return null;
}
var ta=typeof attr=="string"?attr:new String(attr);
var v=node.getAttribute(ta.toUpperCase());
if((v)&&(typeof v=="string")&&(v!="")){
return v;
}
if(v&&v.value){
return v.value;
}
if((node.getAttributeNode)&&(node.getAttributeNode(ta))){
return (node.getAttributeNode(ta)).value;
}else{
if(node.getAttribute(ta)){
return node.getAttribute(ta);
}else{
if(node.getAttribute(ta.toLowerCase())){
return node.getAttribute(ta.toLowerCase());
}
}
}
return null;
};
dojo.html.hasAttribute=function(node,attr){
return dojo.html.getAttribute(dojo.byId(node),attr)?true:false;
};
dojo.html.getCursorPosition=function(e){
e=e||dojo.global().event;
var _4b4={x:0,y:0};
if(e.pageX||e.pageY){
_4b4.x=e.pageX;
_4b4.y=e.pageY;
}else{
var de=dojo.doc().documentElement;
var db=dojo.body();
_4b4.x=e.clientX+((de||db)["scrollLeft"])-((de||db)["clientLeft"]);
_4b4.y=e.clientY+((de||db)["scrollTop"])-((de||db)["clientTop"]);
}
return _4b4;
};
dojo.html.isTag=function(node){
node=dojo.byId(node);
if(node&&node.tagName){
for(var i=1;i<arguments.length;i++){
if(node.tagName.toLowerCase()==String(arguments[i]).toLowerCase()){
return String(arguments[i]).toLowerCase();
}
}
}
return "";
};
if(dojo.render.html.ie&&!dojo.render.html.ie70){
if(window.location.href.substr(0,6).toLowerCase()!="https:"){
(function(){
var _4b9=dojo.doc().createElement("script");
_4b9.src="javascript:'dojo.html.createExternalElement=function(doc, tag){ return doc.createElement(tag); }'";
dojo.doc().getElementsByTagName("head")[0].appendChild(_4b9);
})();
}
}else{
dojo.html.createExternalElement=function(doc,tag){
return doc.createElement(tag);
};
}
dojo.html._callDeprecated=function(_4bc,_4bd,args,_4bf,_4c0){
dojo.deprecated("dojo.html."+_4bc,"replaced by dojo.html."+_4bd+"("+(_4bf?"node, {"+_4bf+": "+_4bf+"}":"")+")"+(_4c0?"."+_4c0:""),"0.5");
var _4c1=[];
if(_4bf){
var _4c2={};
_4c2[_4bf]=args[1];
_4c1.push(args[0]);
_4c1.push(_4c2);
}else{
_4c1=args;
}
var ret=dojo.html[_4bd].apply(dojo.html,args);
if(_4c0){
return ret[_4c0];
}else{
return ret;
}
};
dojo.html.getViewportWidth=function(){
return dojo.html._callDeprecated("getViewportWidth","getViewport",arguments,null,"width");
};
dojo.html.getViewportHeight=function(){
return dojo.html._callDeprecated("getViewportHeight","getViewport",arguments,null,"height");
};
dojo.html.getViewportSize=function(){
return dojo.html._callDeprecated("getViewportSize","getViewport",arguments);
};
dojo.html.getScrollTop=function(){
return dojo.html._callDeprecated("getScrollTop","getScroll",arguments,null,"top");
};
dojo.html.getScrollLeft=function(){
return dojo.html._callDeprecated("getScrollLeft","getScroll",arguments,null,"left");
};
dojo.html.getScrollOffset=function(){
return dojo.html._callDeprecated("getScrollOffset","getScroll",arguments,null,"offset");
};
dojo.provide("dojo.html.layout");
dojo.html.sumAncestorProperties=function(node,prop){
node=dojo.byId(node);
if(!node){
return 0;
}
var _4c6=0;
while(node){
if(dojo.html.getComputedStyle(node,"position")=="fixed"){
return 0;
}
var val=node[prop];
if(val){
_4c6+=val-0;
if(node==dojo.body()){
break;
}
}
node=node.parentNode;
}
return _4c6;
};
dojo.html.setStyleAttributes=function(node,_4c9){
node=dojo.byId(node);
var _4ca=_4c9.replace(/(;)?\s*$/,"").split(";");
for(var i=0;i<_4ca.length;i++){
var _4cc=_4ca[i].split(":");
var name=_4cc[0].replace(/\s*$/,"").replace(/^\s*/,"").toLowerCase();
var _4ce=_4cc[1].replace(/\s*$/,"").replace(/^\s*/,"");
switch(name){
case "opacity":
dojo.html.setOpacity(node,_4ce);
break;
case "content-height":
dojo.html.setContentBox(node,{height:_4ce});
break;
case "content-width":
dojo.html.setContentBox(node,{width:_4ce});
break;
case "outer-height":
dojo.html.setMarginBox(node,{height:_4ce});
break;
case "outer-width":
dojo.html.setMarginBox(node,{width:_4ce});
break;
default:
node.style[dojo.html.toCamelCase(name)]=_4ce;
}
}
};
dojo.html.boxSizing={MARGIN_BOX:"margin-box",BORDER_BOX:"border-box",PADDING_BOX:"padding-box",CONTENT_BOX:"content-box"};
dojo.html.getAbsolutePosition=dojo.html.abs=function(node,_4d0,_4d1){
node=dojo.byId(node,node.ownerDocument);
var ret={x:0,y:0};
var bs=dojo.html.boxSizing;
if(!_4d1){
_4d1=bs.CONTENT_BOX;
}
var _4d4=2;
var _4d5;
switch(_4d1){
case bs.MARGIN_BOX:
_4d5=3;
break;
case bs.BORDER_BOX:
_4d5=2;
break;
case bs.PADDING_BOX:
default:
_4d5=1;
break;
case bs.CONTENT_BOX:
_4d5=0;
break;
}
var h=dojo.render.html;
var db=document["body"]||document["documentElement"];
if(h.ie){
with(node.getBoundingClientRect()){
ret.x=left-2;
ret.y=top-2;
}
}else{
if(document.getBoxObjectFor){
_4d4=1;
try{
var bo=document.getBoxObjectFor(node);
ret.x=bo.x-dojo.html.sumAncestorProperties(node,"scrollLeft");
ret.y=bo.y-dojo.html.sumAncestorProperties(node,"scrollTop");
}
catch(e){
}
}else{
if(node["offsetParent"]){
var _4d9;
if((h.safari)&&(node.style.getPropertyValue("position")=="absolute")&&(node.parentNode==db)){
_4d9=db;
}else{
_4d9=db.parentNode;
}
if(node.parentNode!=db){
var nd=node;
if(dojo.render.html.opera){
nd=db;
}
ret.x-=dojo.html.sumAncestorProperties(nd,"scrollLeft");
ret.y-=dojo.html.sumAncestorProperties(nd,"scrollTop");
}
var _4db=node;
do{
var n=_4db["offsetLeft"];
if(!h.opera||n>0){
ret.x+=isNaN(n)?0:n;
}
var m=_4db["offsetTop"];
ret.y+=isNaN(m)?0:m;
_4db=_4db.offsetParent;
}while((_4db!=_4d9)&&(_4db!=null));
}else{
if(node["x"]&&node["y"]){
ret.x+=isNaN(node.x)?0:node.x;
ret.y+=isNaN(node.y)?0:node.y;
}
}
}
}
if(_4d0){
var _4de=dojo.html.getScroll();
ret.y+=_4de.top;
ret.x+=_4de.left;
}
var _4df=[dojo.html.getPaddingExtent,dojo.html.getBorderExtent,dojo.html.getMarginExtent];
if(_4d4>_4d5){
for(var i=_4d5;i<_4d4;++i){
ret.y+=_4df[i](node,"top");
ret.x+=_4df[i](node,"left");
}
}else{
if(_4d4<_4d5){
for(var i=_4d5;i>_4d4;--i){
ret.y-=_4df[i-1](node,"top");
ret.x-=_4df[i-1](node,"left");
}
}
}
ret.top=ret.y;
ret.left=ret.x;
return ret;
};
dojo.html.isPositionAbsolute=function(node){
return (dojo.html.getComputedStyle(node,"position")=="absolute");
};
dojo.html._sumPixelValues=function(node,_4e3,_4e4){
var _4e5=0;
for(var x=0;x<_4e3.length;x++){
_4e5+=dojo.html.getPixelValue(node,_4e3[x],_4e4);
}
return _4e5;
};
dojo.html.getMargin=function(node){
return {width:dojo.html._sumPixelValues(node,["margin-left","margin-right"],(dojo.html.getComputedStyle(node,"position")=="absolute")),height:dojo.html._sumPixelValues(node,["margin-top","margin-bottom"],(dojo.html.getComputedStyle(node,"position")=="absolute"))};
};
dojo.html.getBorder=function(node){
return {width:dojo.html.getBorderExtent(node,"left")+dojo.html.getBorderExtent(node,"right"),height:dojo.html.getBorderExtent(node,"top")+dojo.html.getBorderExtent(node,"bottom")};
};
dojo.html.getBorderExtent=function(node,side){
return (dojo.html.getStyle(node,"border-"+side+"-style")=="none"?0:dojo.html.getPixelValue(node,"border-"+side+"-width"));
};
dojo.html.getMarginExtent=function(node,side){
return dojo.html._sumPixelValues(node,["margin-"+side],dojo.html.isPositionAbsolute(node));
};
dojo.html.getPaddingExtent=function(node,side){
return dojo.html._sumPixelValues(node,["padding-"+side],true);
};
dojo.html.getPadding=function(node){
return {width:dojo.html._sumPixelValues(node,["padding-left","padding-right"],true),height:dojo.html._sumPixelValues(node,["padding-top","padding-bottom"],true)};
};
dojo.html.getPadBorder=function(node){
var pad=dojo.html.getPadding(node);
var _4f2=dojo.html.getBorder(node);
return {width:pad.width+_4f2.width,height:pad.height+_4f2.height};
};
dojo.html.getBoxSizing=function(node){
var h=dojo.render.html;
var bs=dojo.html.boxSizing;
if((h.ie)||(h.opera)){
var cm=document["compatMode"];
if((cm=="BackCompat")||(cm=="QuirksMode")){
return bs.BORDER_BOX;
}else{
return bs.CONTENT_BOX;
}
}else{
if(arguments.length==0){
node=document.documentElement;
}
var _4f7=dojo.html.getStyle(node,"-moz-box-sizing");
if(!_4f7){
_4f7=dojo.html.getStyle(node,"box-sizing");
}
return (_4f7?_4f7:bs.CONTENT_BOX);
}
};
dojo.html.isBorderBox=function(node){
return (dojo.html.getBoxSizing(node)==dojo.html.boxSizing.BORDER_BOX);
};
dojo.html.getBorderBox=function(node){
node=dojo.byId(node);
return {width:node.offsetWidth,height:node.offsetHeight};
};
dojo.html.getPaddingBox=function(node){
var box=dojo.html.getBorderBox(node);
var _4fc=dojo.html.getBorder(node);
return {width:box.width-_4fc.width,height:box.height-_4fc.height};
};
dojo.html.getContentBox=function(node){
node=dojo.byId(node);
var _4fe=dojo.html.getPadBorder(node);
return {width:node.offsetWidth-_4fe.width,height:node.offsetHeight-_4fe.height};
};
dojo.html.setContentBox=function(node,args){
node=dojo.byId(node);
var _501=0;
var _502=0;
var isbb=dojo.html.isBorderBox(node);
var _504=(isbb?dojo.html.getPadBorder(node):{width:0,height:0});
var ret={};
if(typeof args.width!="undefined"){
_501=args.width+_504.width;
ret.width=dojo.html.setPositivePixelValue(node,"width",_501);
}
if(typeof args.height!="undefined"){
_502=args.height+_504.height;
ret.height=dojo.html.setPositivePixelValue(node,"height",_502);
}
return ret;
};
dojo.html.getMarginBox=function(node){
var _507=dojo.html.getBorderBox(node);
var _508=dojo.html.getMargin(node);
return {width:_507.width+_508.width,height:_507.height+_508.height};
};
dojo.html.setMarginBox=function(node,args){
node=dojo.byId(node);
var _50b=0;
var _50c=0;
var isbb=dojo.html.isBorderBox(node);
var _50e=(!isbb?dojo.html.getPadBorder(node):{width:0,height:0});
var _50f=dojo.html.getMargin(node);
var ret={};
if(typeof args.width!="undefined"){
_50b=args.width-_50e.width;
_50b-=_50f.width;
ret.width=dojo.html.setPositivePixelValue(node,"width",_50b);
}
if(typeof args.height!="undefined"){
_50c=args.height-_50e.height;
_50c-=_50f.height;
ret.height=dojo.html.setPositivePixelValue(node,"height",_50c);
}
return ret;
};
dojo.html.getElementBox=function(node,type){
var bs=dojo.html.boxSizing;
switch(type){
case bs.MARGIN_BOX:
return dojo.html.getMarginBox(node);
case bs.BORDER_BOX:
return dojo.html.getBorderBox(node);
case bs.PADDING_BOX:
return dojo.html.getPaddingBox(node);
case bs.CONTENT_BOX:
default:
return dojo.html.getContentBox(node);
}
};
dojo.html.toCoordinateObject=dojo.html.toCoordinateArray=function(_514,_515,_516){
if(_514 instanceof Array||typeof _514=="array"){
dojo.deprecated("dojo.html.toCoordinateArray","use dojo.html.toCoordinateObject({left: , top: , width: , height: }) instead","0.5");
while(_514.length<4){
_514.push(0);
}
while(_514.length>4){
_514.pop();
}
var ret={left:_514[0],top:_514[1],width:_514[2],height:_514[3]};
}else{
if(!_514.nodeType&&!(_514 instanceof String||typeof _514=="string")&&("width" in _514||"height" in _514||"left" in _514||"x" in _514||"top" in _514||"y" in _514)){
var ret={left:_514.left||_514.x||0,top:_514.top||_514.y||0,width:_514.width||0,height:_514.height||0};
}else{
var node=dojo.byId(_514);
var pos=dojo.html.abs(node,_515,_516);
var _51a=dojo.html.getMarginBox(node);
var ret={left:pos.left,top:pos.top,width:_51a.width,height:_51a.height};
}
}
ret.x=ret.left;
ret.y=ret.top;
return ret;
};
dojo.html.setMarginBoxWidth=dojo.html.setOuterWidth=function(node,_51c){
return dojo.html._callDeprecated("setMarginBoxWidth","setMarginBox",arguments,"width");
};
dojo.html.setMarginBoxHeight=dojo.html.setOuterHeight=function(){
return dojo.html._callDeprecated("setMarginBoxHeight","setMarginBox",arguments,"height");
};
dojo.html.getMarginBoxWidth=dojo.html.getOuterWidth=function(){
return dojo.html._callDeprecated("getMarginBoxWidth","getMarginBox",arguments,null,"width");
};
dojo.html.getMarginBoxHeight=dojo.html.getOuterHeight=function(){
return dojo.html._callDeprecated("getMarginBoxHeight","getMarginBox",arguments,null,"height");
};
dojo.html.getTotalOffset=function(node,type,_51f){
return dojo.html._callDeprecated("getTotalOffset","getAbsolutePosition",arguments,null,type);
};
dojo.html.getAbsoluteX=function(node,_521){
return dojo.html._callDeprecated("getAbsoluteX","getAbsolutePosition",arguments,null,"x");
};
dojo.html.getAbsoluteY=function(node,_523){
return dojo.html._callDeprecated("getAbsoluteY","getAbsolutePosition",arguments,null,"y");
};
dojo.html.totalOffsetLeft=function(node,_525){
return dojo.html._callDeprecated("totalOffsetLeft","getAbsolutePosition",arguments,null,"left");
};
dojo.html.totalOffsetTop=function(node,_527){
return dojo.html._callDeprecated("totalOffsetTop","getAbsolutePosition",arguments,null,"top");
};
dojo.html.getMarginWidth=function(node){
return dojo.html._callDeprecated("getMarginWidth","getMargin",arguments,null,"width");
};
dojo.html.getMarginHeight=function(node){
return dojo.html._callDeprecated("getMarginHeight","getMargin",arguments,null,"height");
};
dojo.html.getBorderWidth=function(node){
return dojo.html._callDeprecated("getBorderWidth","getBorder",arguments,null,"width");
};
dojo.html.getBorderHeight=function(node){
return dojo.html._callDeprecated("getBorderHeight","getBorder",arguments,null,"height");
};
dojo.html.getPaddingWidth=function(node){
return dojo.html._callDeprecated("getPaddingWidth","getPadding",arguments,null,"width");
};
dojo.html.getPaddingHeight=function(node){
return dojo.html._callDeprecated("getPaddingHeight","getPadding",arguments,null,"height");
};
dojo.html.getPadBorderWidth=function(node){
return dojo.html._callDeprecated("getPadBorderWidth","getPadBorder",arguments,null,"width");
};
dojo.html.getPadBorderHeight=function(node){
return dojo.html._callDeprecated("getPadBorderHeight","getPadBorder",arguments,null,"height");
};
dojo.html.getBorderBoxWidth=dojo.html.getInnerWidth=function(){
return dojo.html._callDeprecated("getBorderBoxWidth","getBorderBox",arguments,null,"width");
};
dojo.html.getBorderBoxHeight=dojo.html.getInnerHeight=function(){
return dojo.html._callDeprecated("getBorderBoxHeight","getBorderBox",arguments,null,"height");
};
dojo.html.getContentBoxWidth=dojo.html.getContentWidth=function(){
return dojo.html._callDeprecated("getContentBoxWidth","getContentBox",arguments,null,"width");
};
dojo.html.getContentBoxHeight=dojo.html.getContentHeight=function(){
return dojo.html._callDeprecated("getContentBoxHeight","getContentBox",arguments,null,"height");
};
dojo.html.setContentBoxWidth=dojo.html.setContentWidth=function(node,_531){
return dojo.html._callDeprecated("setContentBoxWidth","setContentBox",arguments,"width");
};
dojo.html.setContentBoxHeight=dojo.html.setContentHeight=function(node,_533){
return dojo.html._callDeprecated("setContentBoxHeight","setContentBox",arguments,"height");
};
dojo.provide("dojo.lfx.html");
dojo.lfx.html._byId=function(_534){
if(!_534){
return [];
}
if(dojo.lang.isArrayLike(_534)){
if(!_534.alreadyChecked){
var n=[];
dojo.lang.forEach(_534,function(node){
n.push(dojo.byId(node));
});
n.alreadyChecked=true;
return n;
}else{
return _534;
}
}else{
var n=[];
n.push(dojo.byId(_534));
n.alreadyChecked=true;
return n;
}
};
dojo.lfx.html.propertyAnimation=function(_537,_538,_539,_53a,_53b){
_537=dojo.lfx.html._byId(_537);
var _53c={"propertyMap":_538,"nodes":_537,"duration":_539,"easing":_53a||dojo.lfx.easeDefault};
var _53d=function(args){
if(args.nodes.length==1){
var pm=args.propertyMap;
if(!dojo.lang.isArray(args.propertyMap)){
var parr=[];
for(var _541 in pm){
pm[_541].property=_541;
parr.push(pm[_541]);
}
pm=args.propertyMap=parr;
}
dojo.lang.forEach(pm,function(prop){
if(dj_undef("start",prop)){
if(prop.property!="opacity"){
prop.start=parseInt(dojo.html.getComputedStyle(args.nodes[0],prop.property));
}else{
prop.start=dojo.html.getOpacity(args.nodes[0]);
}
}
});
}
};
var _543=function(_544){
var _545=[];
dojo.lang.forEach(_544,function(c){
_545.push(Math.round(c));
});
return _545;
};
var _547=function(n,_549){
n=dojo.byId(n);
if(!n||!n.style){
return;
}
for(var s in _549){
if(s=="opacity"){
dojo.html.setOpacity(n,_549[s]);
}else{
n.style[s]=_549[s];
}
}
};
var _54b=function(_54c){
this._properties=_54c;
this.diffs=new Array(_54c.length);
dojo.lang.forEach(_54c,function(prop,i){
if(dojo.lang.isFunction(prop.start)){
prop.start=prop.start(prop,i);
}
if(dojo.lang.isFunction(prop.end)){
prop.end=prop.end(prop,i);
}
if(dojo.lang.isArray(prop.start)){
this.diffs[i]=null;
}else{
if(prop.start instanceof dojo.gfx.color.Color){
prop.startRgb=prop.start.toRgb();
prop.endRgb=prop.end.toRgb();
}else{
this.diffs[i]=prop.end-prop.start;
}
}
},this);
this.getValue=function(n){
var ret={};
dojo.lang.forEach(this._properties,function(prop,i){
var _553=null;
if(dojo.lang.isArray(prop.start)){
}else{
if(prop.start instanceof dojo.gfx.color.Color){
_553=(prop.units||"rgb")+"(";
for(var j=0;j<prop.startRgb.length;j++){
_553+=Math.round(((prop.endRgb[j]-prop.startRgb[j])*n)+prop.startRgb[j])+(j<prop.startRgb.length-1?",":"");
}
_553+=")";
}else{
_553=((this.diffs[i])*n)+prop.start+(prop.property!="opacity"?prop.units||"px":"");
}
}
ret[dojo.html.toCamelCase(prop.property)]=_553;
},this);
return ret;
};
};
var anim=new dojo.lfx.Animation({beforeBegin:function(){
_53d(_53c);
anim.curve=new _54b(_53c.propertyMap);
},onAnimate:function(_556){
dojo.lang.forEach(_53c.nodes,function(node){
_547(node,_556);
});
}},_53c.duration,null,_53c.easing);
if(_53b){
for(var x in _53b){
if(dojo.lang.isFunction(_53b[x])){
anim.connect(x,anim,_53b[x]);
}
}
}
return anim;
};
dojo.lfx.html._makeFadeable=function(_559){
var _55a=function(node){
if(dojo.render.html.ie){
if((node.style.zoom.length==0)&&(dojo.html.getStyle(node,"zoom")=="normal")){
node.style.zoom="1";
}
if((node.style.width.length==0)&&(dojo.html.getStyle(node,"width")=="auto")){
node.style.width="auto";
}
}
};
if(dojo.lang.isArrayLike(_559)){
dojo.lang.forEach(_559,_55a);
}else{
_55a(_559);
}
};
dojo.lfx.html.fade=function(_55c,_55d,_55e,_55f,_560){
_55c=dojo.lfx.html._byId(_55c);
var _561={property:"opacity"};
if(!dj_undef("start",_55d)){
_561.start=_55d.start;
}else{
_561.start=function(){
return dojo.html.getOpacity(_55c[0]);
};
}
if(!dj_undef("end",_55d)){
_561.end=_55d.end;
}else{
dojo.raise("dojo.lfx.html.fade needs an end value");
}
var anim=dojo.lfx.propertyAnimation(_55c,[_561],_55e,_55f);
anim.connect("beforeBegin",function(){
dojo.lfx.html._makeFadeable(_55c);
});
if(_560){
anim.connect("onEnd",function(){
_560(_55c,anim);
});
}
return anim;
};
dojo.lfx.html.fadeIn=function(_563,_564,_565,_566){
return dojo.lfx.html.fade(_563,{end:1},_564,_565,_566);
};
dojo.lfx.html.fadeOut=function(_567,_568,_569,_56a){
return dojo.lfx.html.fade(_567,{end:0},_568,_569,_56a);
};
dojo.lfx.html.fadeShow=function(_56b,_56c,_56d,_56e){
_56b=dojo.lfx.html._byId(_56b);
dojo.lang.forEach(_56b,function(node){
dojo.html.setOpacity(node,0);
});
var anim=dojo.lfx.html.fadeIn(_56b,_56c,_56d,_56e);
anim.connect("beforeBegin",function(){
if(dojo.lang.isArrayLike(_56b)){
dojo.lang.forEach(_56b,dojo.html.show);
}else{
dojo.html.show(_56b);
}
});
return anim;
};
dojo.lfx.html.fadeHide=function(_571,_572,_573,_574){
var anim=dojo.lfx.html.fadeOut(_571,_572,_573,function(){
if(dojo.lang.isArrayLike(_571)){
dojo.lang.forEach(_571,dojo.html.hide);
}else{
dojo.html.hide(_571);
}
if(_574){
_574(_571,anim);
}
});
return anim;
};
dojo.lfx.html.wipeIn=function(_576,_577,_578,_579){
_576=dojo.lfx.html._byId(_576);
var _57a=[];
dojo.lang.forEach(_576,function(node){
var _57c={};
dojo.html.show(node);
var _57d=dojo.html.getBorderBox(node).height;
dojo.html.hide(node);
var anim=dojo.lfx.propertyAnimation(node,{"height":{start:1,end:function(){
return _57d;
}}},_577,_578);
anim.connect("beforeBegin",function(){
_57c.overflow=node.style.overflow;
_57c.height=node.style.height;
with(node.style){
overflow="hidden";
_57d="1px";
}
dojo.html.show(node);
});
anim.connect("onEnd",function(){
with(node.style){
overflow=_57c.overflow;
_57d=_57c.height;
}
if(_579){
_579(node,anim);
}
});
_57a.push(anim);
});
return dojo.lfx.combine(_57a);
};
dojo.lfx.html.wipeOut=function(_57f,_580,_581,_582){
_57f=dojo.lfx.html._byId(_57f);
var _583=[];
dojo.lang.forEach(_57f,function(node){
var _585={};
var anim=dojo.lfx.propertyAnimation(node,{"height":{start:function(){
return dojo.html.getContentBox(node).height;
},end:1}},_580,_581,{"beforeBegin":function(){
_585.overflow=node.style.overflow;
_585.height=node.style.height;
with(node.style){
overflow="hidden";
}
dojo.html.show(node);
},"onEnd":function(){
dojo.html.hide(node);
with(node.style){
overflow=_585.overflow;
height=_585.height;
}
if(_582){
_582(node,anim);
}
}});
_583.push(anim);
});
return dojo.lfx.combine(_583);
};
dojo.lfx.html.slideTo=function(_587,_588,_589,_58a,_58b){
_587=dojo.lfx.html._byId(_587);
var _58c=[];
var _58d=dojo.html.getComputedStyle;
if(dojo.lang.isArray(_588)){
dojo.deprecated("dojo.lfx.html.slideTo(node, array)","use dojo.lfx.html.slideTo(node, {top: value, left: value});","0.5");
_588={top:_588[0],left:_588[1]};
}
dojo.lang.forEach(_587,function(node){
var top=null;
var left=null;
var init=(function(){
var _592=node;
return function(){
var pos=_58d(_592,"position");
top=(pos=="absolute"?node.offsetTop:parseInt(_58d(node,"top"))||0);
left=(pos=="absolute"?node.offsetLeft:parseInt(_58d(node,"left"))||0);
if(!dojo.lang.inArray(["absolute","relative"],pos)){
var ret=dojo.html.abs(_592,true);
dojo.html.setStyleAttributes(_592,"position:absolute;top:"+ret.y+"px;left:"+ret.x+"px;");
top=ret.y;
left=ret.x;
}
};
})();
init();
var anim=dojo.lfx.propertyAnimation(node,{"top":{start:top,end:(_588.top||0)},"left":{start:left,end:(_588.left||0)}},_589,_58a,{"beforeBegin":init});
if(_58b){
anim.connect("onEnd",function(){
_58b(_587,anim);
});
}
_58c.push(anim);
});
return dojo.lfx.combine(_58c);
};
dojo.lfx.html.slideBy=function(_596,_597,_598,_599,_59a){
_596=dojo.lfx.html._byId(_596);
var _59b=[];
var _59c=dojo.html.getComputedStyle;
if(dojo.lang.isArray(_597)){
dojo.deprecated("dojo.lfx.html.slideBy(node, array)","use dojo.lfx.html.slideBy(node, {top: value, left: value});","0.5");
_597={top:_597[0],left:_597[1]};
}
dojo.lang.forEach(_596,function(node){
var top=null;
var left=null;
var init=(function(){
var _5a1=node;
return function(){
var pos=_59c(_5a1,"position");
top=(pos=="absolute"?node.offsetTop:parseInt(_59c(node,"top"))||0);
left=(pos=="absolute"?node.offsetLeft:parseInt(_59c(node,"left"))||0);
if(!dojo.lang.inArray(["absolute","relative"],pos)){
var ret=dojo.html.abs(_5a1,true);
dojo.html.setStyleAttributes(_5a1,"position:absolute;top:"+ret.y+"px;left:"+ret.x+"px;");
top=ret.y;
left=ret.x;
}
};
})();
init();
var anim=dojo.lfx.propertyAnimation(node,{"top":{start:top,end:top+(_597.top||0)},"left":{start:left,end:left+(_597.left||0)}},_598,_599).connect("beforeBegin",init);
if(_59a){
anim.connect("onEnd",function(){
_59a(_596,anim);
});
}
_59b.push(anim);
});
return dojo.lfx.combine(_59b);
};
dojo.lfx.html.explode=function(_5a5,_5a6,_5a7,_5a8,_5a9){
var h=dojo.html;
_5a5=dojo.byId(_5a5);
_5a6=dojo.byId(_5a6);
var _5ab=h.toCoordinateObject(_5a5,true);
var _5ac=document.createElement("div");
h.copyStyle(_5ac,_5a6);
if(_5a6.explodeClassName){
_5ac.className=_5a6.explodeClassName;
}
with(_5ac.style){
position="absolute";
display="none";
}
dojo.body().appendChild(_5ac);
with(_5a6.style){
visibility="hidden";
display="block";
}
var _5ad=h.toCoordinateObject(_5a6,true);
with(_5a6.style){
display="none";
visibility="visible";
}
var _5ae={opacity:{start:0.5,end:1}};
dojo.lang.forEach(["height","width","top","left"],function(type){
_5ae[type]={start:_5ab[type],end:_5ad[type]};
});
var anim=new dojo.lfx.propertyAnimation(_5ac,_5ae,_5a7,_5a8,{"beforeBegin":function(){
h.setDisplay(_5ac,"block");
},"onEnd":function(){
h.setDisplay(_5a6,"block");
_5ac.parentNode.removeChild(_5ac);
}});
if(_5a9){
anim.connect("onEnd",function(){
_5a9(_5a6,anim);
});
}
return anim;
};
dojo.lfx.html.implode=function(_5b1,end,_5b3,_5b4,_5b5){
var h=dojo.html;
_5b1=dojo.byId(_5b1);
end=dojo.byId(end);
var _5b7=dojo.html.toCoordinateObject(_5b1,true);
var _5b8=dojo.html.toCoordinateObject(end,true);
var _5b9=document.createElement("div");
dojo.html.copyStyle(_5b9,_5b1);
if(_5b1.explodeClassName){
_5b9.className=_5b1.explodeClassName;
}
dojo.html.setOpacity(_5b9,0.3);
with(_5b9.style){
position="absolute";
display="none";
backgroundColor=h.getStyle(_5b1,"background-color").toLowerCase();
}
dojo.body().appendChild(_5b9);
var _5ba={opacity:{start:1,end:0.5}};
dojo.lang.forEach(["height","width","top","left"],function(type){
_5ba[type]={start:_5b7[type],end:_5b8[type]};
});
var anim=new dojo.lfx.propertyAnimation(_5b9,_5ba,_5b3,_5b4,{"beforeBegin":function(){
dojo.html.hide(_5b1);
dojo.html.show(_5b9);
},"onEnd":function(){
_5b9.parentNode.removeChild(_5b9);
}});
if(_5b5){
anim.connect("onEnd",function(){
_5b5(_5b1,anim);
});
}
return anim;
};
dojo.lfx.html.highlight=function(_5bd,_5be,_5bf,_5c0,_5c1){
_5bd=dojo.lfx.html._byId(_5bd);
var _5c2=[];
dojo.lang.forEach(_5bd,function(node){
var _5c4=dojo.html.getBackgroundColor(node);
var bg=dojo.html.getStyle(node,"background-color").toLowerCase();
var _5c6=dojo.html.getStyle(node,"background-image");
var _5c7=(bg=="transparent"||bg=="rgba(0, 0, 0, 0)");
while(_5c4.length>3){
_5c4.pop();
}
var rgb=new dojo.gfx.color.Color(_5be);
var _5c9=new dojo.gfx.color.Color(_5c4);
var anim=dojo.lfx.propertyAnimation(node,{"background-color":{start:rgb,end:_5c9}},_5bf,_5c0,{"beforeBegin":function(){
if(_5c6){
node.style.backgroundImage="none";
}
node.style.backgroundColor="rgb("+rgb.toRgb().join(",")+")";
},"onEnd":function(){
if(_5c6){
node.style.backgroundImage=_5c6;
}
if(_5c7){
node.style.backgroundColor="transparent";
}
if(_5c1){
_5c1(node,anim);
}
}});
_5c2.push(anim);
});
return dojo.lfx.combine(_5c2);
};
dojo.lfx.html.unhighlight=function(_5cb,_5cc,_5cd,_5ce,_5cf){
_5cb=dojo.lfx.html._byId(_5cb);
var _5d0=[];
dojo.lang.forEach(_5cb,function(node){
var _5d2=new dojo.gfx.color.Color(dojo.html.getBackgroundColor(node));
var rgb=new dojo.gfx.color.Color(_5cc);
var _5d4=dojo.html.getStyle(node,"background-image");
var anim=dojo.lfx.propertyAnimation(node,{"background-color":{start:_5d2,end:rgb}},_5cd,_5ce,{"beforeBegin":function(){
if(_5d4){
node.style.backgroundImage="none";
}
node.style.backgroundColor="rgb("+_5d2.toRgb().join(",")+")";
},"onEnd":function(){
if(_5cf){
_5cf(node,anim);
}
}});
_5d0.push(anim);
});
return dojo.lfx.combine(_5d0);
};
dojo.lang.mixin(dojo.lfx,dojo.lfx.html);
dojo.provide("dojo.lfx.*");
dojo.provide("dojo.xml.Parse");
dojo.xml.Parse=function(){
function getTagName(node){
return ((node)&&(node.tagName)?node.tagName.toLowerCase():"");
}
function getDojoTagName(node){
var _5d8=getTagName(node);
if(!_5d8){
return "";
}
if((dojo.widget)&&(dojo.widget.tags[_5d8])){
return _5d8;
}
var p=_5d8.indexOf(":");
if(p>=0){
return _5d8;
}
if(_5d8.substr(0,5)=="dojo:"){
return _5d8;
}
if(dojo.render.html.capable&&dojo.render.html.ie&&node.scopeName!="HTML"){
return node.scopeName.toLowerCase()+":"+_5d8;
}
if(_5d8.substr(0,4)=="dojo"){
return "dojo:"+_5d8.substring(4);
}
var djt=node.getAttribute("dojoType")||node.getAttribute("dojotype");
if(djt){
if(djt.indexOf(":")<0){
djt="dojo:"+djt;
}
return djt.toLowerCase();
}
djt=node.getAttributeNS&&node.getAttributeNS(dojo.dom.dojoml,"type");
if(djt){
return "dojo:"+djt.toLowerCase();
}
try{
djt=node.getAttribute("dojo:type");
}
catch(e){
}
if(djt){
return "dojo:"+djt.toLowerCase();
}
if((!dj_global["djConfig"])||(djConfig["ignoreClassNames"])){
var _5db=node.className||node.getAttribute("class");
if((_5db)&&(_5db.indexOf)&&(_5db.indexOf("dojo-")!=-1)){
var _5dc=_5db.split(" ");
for(var x=0,c=_5dc.length;x<c;x++){
if(_5dc[x].slice(0,5)=="dojo-"){
return "dojo:"+_5dc[x].substr(5).toLowerCase();
}
}
}
}
return "";
}
this.parseElement=function(node,_5e0,_5e1,_5e2){
var _5e3={};
var _5e4=getTagName(node);
if((_5e4)&&(_5e4.indexOf("/")==0)){
return null;
}
var _5e5=true;
if(_5e1){
var _5e6=getDojoTagName(node);
_5e4=_5e6||_5e4;
_5e5=Boolean(_5e6);
}
if(node&&node.getAttribute&&node.getAttribute("parseWidgets")&&node.getAttribute("parseWidgets")=="false"){
return {};
}
_5e3[_5e4]=[];
var pos=_5e4.indexOf(":");
if(pos>0){
var ns=_5e4.substring(0,pos);
_5e3["ns"]=ns;
if((dojo.ns)&&(!dojo.ns.allow(ns))){
_5e5=false;
}
}
if(_5e5){
var _5e9=this.parseAttributes(node);
for(var attr in _5e9){
if((!_5e3[_5e4][attr])||(typeof _5e3[_5e4][attr]!="array")){
_5e3[_5e4][attr]=[];
}
_5e3[_5e4][attr].push(_5e9[attr]);
}
_5e3[_5e4].nodeRef=node;
_5e3.tagName=_5e4;
_5e3.index=_5e2||0;
}
var _5eb=0;
for(var i=0;i<node.childNodes.length;i++){
var tcn=node.childNodes.item(i);
switch(tcn.nodeType){
case dojo.dom.ELEMENT_NODE:
_5eb++;
var ctn=getDojoTagName(tcn)||getTagName(tcn);
if(!_5e3[ctn]){
_5e3[ctn]=[];
}
_5e3[ctn].push(this.parseElement(tcn,true,_5e1,_5eb));
if((tcn.childNodes.length==1)&&(tcn.childNodes.item(0).nodeType==dojo.dom.TEXT_NODE)){
_5e3[ctn][_5e3[ctn].length-1].value=tcn.childNodes.item(0).nodeValue;
}
break;
case dojo.dom.TEXT_NODE:
if(node.childNodes.length==1){
_5e3[_5e4].push({value:node.childNodes.item(0).nodeValue});
}
break;
default:
break;
}
}
return _5e3;
};
this.parseAttributes=function(node){
var _5f0={};
var atts=node.attributes;
var _5f2,i=0;
while((_5f2=atts[i++])){
if((dojo.render.html.capable)&&(dojo.render.html.ie)){
if(!_5f2){
continue;
}
if((typeof _5f2=="object")&&(typeof _5f2.nodeValue=="undefined")||(_5f2.nodeValue==null)||(_5f2.nodeValue=="")){
continue;
}
}
var nn=_5f2.nodeName.split(":");
nn=(nn.length==2)?nn[1]:_5f2.nodeName;
_5f0[nn]={value:_5f2.nodeValue};
}
return _5f0;
};
};
dojo.provide("dojo.lang.declare");
dojo.lang.declare=function(_5f5,_5f6,init,_5f8){
if((dojo.lang.isFunction(_5f8))||((!_5f8)&&(!dojo.lang.isFunction(init)))){
var temp=_5f8;
_5f8=init;
init=temp;
}
var _5fa=[];
if(dojo.lang.isArray(_5f6)){
_5fa=_5f6;
_5f6=_5fa.shift();
}
if(!init){
init=dojo.evalObjPath(_5f5,false);
if((init)&&(!dojo.lang.isFunction(init))){
init=null;
}
}
var ctor=dojo.lang.declare._makeConstructor();
var scp=(_5f6?_5f6.prototype:null);
if(scp){
scp.prototyping=true;
ctor.prototype=new _5f6();
scp.prototyping=false;
}
ctor.superclass=scp;
ctor.mixins=_5fa;
for(var i=0,l=_5fa.length;i<l;i++){
dojo.lang.extend(ctor,_5fa[i].prototype);
}
ctor.prototype.initializer=null;
ctor.prototype.declaredClass=_5f5;
if(dojo.lang.isArray(_5f8)){
dojo.lang.extend.apply(dojo.lang,[ctor].concat(_5f8));
}else{
dojo.lang.extend(ctor,(_5f8)||{});
}
dojo.lang.extend(ctor,dojo.lang.declare._common);
ctor.prototype.constructor=ctor;
ctor.prototype.initializer=(ctor.prototype.initializer)||(init)||(function(){
});
dojo.lang.setObjPathValue(_5f5,ctor,null,true);
return ctor;
};
dojo.lang.declare._makeConstructor=function(){
return function(){
var self=this._getPropContext();
var s=self.constructor.superclass;
if((s)&&(s.constructor)){
if(s.constructor==arguments.callee){
this._inherited("constructor",arguments);
}else{
this._contextMethod(s,"constructor",arguments);
}
}
var ms=(self.constructor.mixins)||([]);
for(var i=0,m;(m=ms[i]);i++){
(((m.prototype)&&(m.prototype.initializer))||(m)).apply(this,arguments);
}
if((!this.prototyping)&&(self.initializer)){
self.initializer.apply(this,arguments);
}
};
};
dojo.lang.declare._common={_getPropContext:function(){
return (this.___proto||this);
},_contextMethod:function(_604,_605,args){
var _607,_608=this.___proto;
this.___proto=_604;
try{
_607=_604[_605].apply(this,(args||[]));
}
catch(e){
throw e;
}
finally{
this.___proto=_608;
}
return _607;
},_inherited:function(prop,args){
var p=this._getPropContext();
do{
if((!p.constructor)||(!p.constructor.superclass)){
return;
}
p=p.constructor.superclass;
}while(!(prop in p));
return (dojo.lang.isFunction(p[prop])?this._contextMethod(p,prop,args):p[prop]);
}};
dojo.declare=dojo.lang.declare;
dojo.provide("dojo.ns");
dojo.ns={namespaces:{},failed:{},loading:{},loaded:{},register:function(name,_60d,_60e,_60f){
if(!_60f||!this.namespaces[name]){
this.namespaces[name]=new dojo.ns.Ns(name,_60d,_60e);
}
},allow:function(name){
if(this.failed[name]){
return false;
}
if((djConfig.excludeNamespace)&&(dojo.lang.inArray(djConfig.excludeNamespace,name))){
return false;
}
return ((name==this.dojo)||(!djConfig.includeNamespace)||(dojo.lang.inArray(djConfig.includeNamespace,name)));
},get:function(name){
return this.namespaces[name];
},require:function(name){
var ns=this.namespaces[name];
if((ns)&&(this.loaded[name])){
return ns;
}
if(!this.allow(name)){
return false;
}
if(this.loading[name]){
dojo.debug("dojo.namespace.require: re-entrant request to load namespace \""+name+"\" must fail.");
return false;
}
var req=dojo.require;
this.loading[name]=true;
try{
if(name=="dojo"){
req("dojo.namespaces.dojo");
}else{
if(!dojo.hostenv.moduleHasPrefix(name)){
dojo.registerModulePath(name,"../"+name);
}
req([name,"manifest"].join("."),false,true);
}
if(!this.namespaces[name]){
this.failed[name]=true;
}
}
finally{
this.loading[name]=false;
}
return this.namespaces[name];
}};
dojo.ns.Ns=function(name,_616,_617){
this.name=name;
this.module=_616;
this.resolver=_617;
this._loaded=[];
this._failed=[];
};
dojo.ns.Ns.prototype.resolve=function(name,_619,_61a){
if(!this.resolver||djConfig["skipAutoRequire"]){
return false;
}
var _61b=this.resolver(name,_619);
if((_61b)&&(!this._loaded[_61b])&&(!this._failed[_61b])){
var req=dojo.require;
req(_61b,false,true);
if(dojo.hostenv.findModule(_61b,false)){
this._loaded[_61b]=true;
}else{
if(!_61a){
dojo.raise("dojo.ns.Ns.resolve: module '"+_61b+"' not found after loading via namespace '"+this.name+"'");
}
this._failed[_61b]=true;
}
}
return Boolean(this._loaded[_61b]);
};
dojo.registerNamespace=function(name,_61e,_61f){
dojo.ns.register.apply(dojo.ns,arguments);
};
dojo.registerNamespaceResolver=function(name,_621){
var n=dojo.ns.namespaces[name];
if(n){
n.resolver=_621;
}
};
dojo.registerNamespaceManifest=function(_623,path,name,_626,_627){
dojo.registerModulePath(name,path);
dojo.registerNamespace(name,_626,_627);
};
dojo.registerNamespace("dojo","dojo.widget");
dojo.provide("dojo.widget.Manager");
dojo.widget.manager=new function(){
this.widgets=[];
this.widgetIds=[];
this.topWidgets={};
var _628={};
var _629=[];
this.getUniqueId=function(_62a){
var _62b;
do{
_62b=_62a+"_"+(_628[_62a]!=undefined?++_628[_62a]:_628[_62a]=0);
}while(this.getWidgetById(_62b));
return _62b;
};
this.add=function(_62c){
this.widgets.push(_62c);
if(!_62c.extraArgs["id"]){
_62c.extraArgs["id"]=_62c.extraArgs["ID"];
}
if(_62c.widgetId==""){
if(_62c["id"]){
_62c.widgetId=_62c["id"];
}else{
if(_62c.extraArgs["id"]){
_62c.widgetId=_62c.extraArgs["id"];
}else{
_62c.widgetId=this.getUniqueId(_62c.widgetType);
}
}
}
if(this.widgetIds[_62c.widgetId]){
dojo.debug("widget ID collision on ID: "+_62c.widgetId);
}
this.widgetIds[_62c.widgetId]=_62c;
};
this.destroyAll=function(){
for(var x=this.widgets.length-1;x>=0;x--){
try{
this.widgets[x].destroy(true);
delete this.widgets[x];
}
catch(e){
}
}
};
this.remove=function(_62e){
if(dojo.lang.isNumber(_62e)){
var tw=this.widgets[_62e].widgetId;
delete this.widgetIds[tw];
this.widgets.splice(_62e,1);
}else{
this.removeById(_62e);
}
};
this.removeById=function(id){
if(!dojo.lang.isString(id)){
id=id["widgetId"];
if(!id){
dojo.debug("invalid widget or id passed to removeById");
return;
}
}
for(var i=0;i<this.widgets.length;i++){
if(this.widgets[i].widgetId==id){
this.remove(i);
break;
}
}
};
this.getWidgetById=function(id){
if(dojo.lang.isString(id)){
return this.widgetIds[id];
}
return id;
};
this.getWidgetsByType=function(type){
var lt=type.toLowerCase();
var _635=(type.indexOf(":")<0?function(x){
return x.widgetType.toLowerCase();
}:function(x){
return x.getNamespacedType();
});
var ret=[];
dojo.lang.forEach(this.widgets,function(x){
if(_635(x)==lt){
ret.push(x);
}
});
return ret;
};
this.getWidgetsByFilter=function(_63a,_63b){
var ret=[];
dojo.lang.every(this.widgets,function(x){
if(_63a(x)){
ret.push(x);
if(_63b){
return false;
}
}
return true;
});
return (_63b?ret[0]:ret);
};
this.getAllWidgets=function(){
return this.widgets.concat();
};
this.getWidgetByNode=function(node){
var w=this.getAllWidgets();
node=dojo.byId(node);
for(var i=0;i<w.length;i++){
if(w[i].domNode==node){
return w[i];
}
}
return null;
};
this.byId=this.getWidgetById;
this.byType=this.getWidgetsByType;
this.byFilter=this.getWidgetsByFilter;
this.byNode=this.getWidgetByNode;
var _641={};
var _642=["dojo.widget"];
for(var i=0;i<_642.length;i++){
_642[_642[i]]=true;
}
this.registerWidgetPackage=function(_644){
if(!_642[_644]){
_642[_644]=true;
_642.push(_644);
}
};
this.getWidgetPackageList=function(){
return dojo.lang.map(_642,function(elt){
return (elt!==true?elt:undefined);
});
};
this.getImplementation=function(_646,_647,_648,ns){
var impl=this.getImplementationName(_646,ns);
if(impl){
var ret=_647?new impl(_647):new impl();
return ret;
}
};
function buildPrefixCache(){
for(var _64c in dojo.render){
if(dojo.render[_64c]["capable"]===true){
var _64d=dojo.render[_64c].prefixes;
for(var i=0;i<_64d.length;i++){
_629.push(_64d[i].toLowerCase());
}
}
}
}
var _64f=function(_650,_651){
if(!_651){
return null;
}
for(var i=0,l=_629.length,_654;i<=l;i++){
_654=(i<l?_651[_629[i]]:_651);
if(!_654){
continue;
}
for(var name in _654){
if(name.toLowerCase()==_650){
return _654[name];
}
}
}
return null;
};
var _656=function(_657,_658){
var _659=dojo.evalObjPath(_658,false);
return (_659?_64f(_657,_659):null);
};
this.getImplementationName=function(_65a,ns){
var _65c=_65a.toLowerCase();
ns=ns||"dojo";
var imps=_641[ns]||(_641[ns]={});
var impl=imps[_65c];
if(impl){
return impl;
}
if(!_629.length){
buildPrefixCache();
}
var _65f=dojo.ns.get(ns);
if(!_65f){
dojo.ns.register(ns,ns+".widget");
_65f=dojo.ns.get(ns);
}
if(_65f){
_65f.resolve(_65a);
}
impl=_656(_65c,_65f.module);
if(impl){
return (imps[_65c]=impl);
}
_65f=dojo.ns.require(ns);
if((_65f)&&(_65f.resolver)){
_65f.resolve(_65a);
impl=_656(_65c,_65f.module);
if(impl){
return (imps[_65c]=impl);
}
}
dojo.deprecated("dojo.widget.Manager.getImplementationName","Could not locate widget implementation for \""+_65a+"\" in \""+_65f.module+"\" registered to namespace \""+_65f.name+"\". "+"Developers must specify correct namespaces for all non-Dojo widgets","0.5");
for(var i=0;i<_642.length;i++){
impl=_656(_65c,_642[i]);
if(impl){
return (imps[_65c]=impl);
}
}
throw new Error("Could not locate widget implementation for \""+_65a+"\" in \""+_65f.module+"\" registered to namespace \""+_65f.name+"\"");
};
this.resizing=false;
this.onWindowResized=function(){
if(this.resizing){
return;
}
try{
this.resizing=true;
for(var id in this.topWidgets){
var _662=this.topWidgets[id];
if(_662.checkSize){
_662.checkSize();
}
}
}
catch(e){
}
finally{
this.resizing=false;
}
};
if(typeof window!="undefined"){
dojo.addOnLoad(this,"onWindowResized");
dojo.event.connect(window,"onresize",this,"onWindowResized");
}
};
(function(){
var dw=dojo.widget;
var dwm=dw.manager;
var h=dojo.lang.curry(dojo.lang,"hitch",dwm);
var g=function(_667,_668){
dw[(_668||_667)]=h(_667);
};
g("add","addWidget");
g("destroyAll","destroyAllWidgets");
g("remove","removeWidget");
g("removeById","removeWidgetById");
g("getWidgetById");
g("getWidgetById","byId");
g("getWidgetsByType");
g("getWidgetsByFilter");
g("getWidgetsByType","byType");
g("getWidgetsByFilter","byFilter");
g("getWidgetByNode","byNode");
dw.all=function(n){
var _66a=dwm.getAllWidgets.apply(dwm,arguments);
if(arguments.length>0){
return _66a[n];
}
return _66a;
};
g("registerWidgetPackage");
g("getImplementation","getWidgetImplementation");
g("getImplementationName","getWidgetImplementationName");
dw.widgets=dwm.widgets;
dw.widgetIds=dwm.widgetIds;
dw.root=dwm.root;
})();
dojo.provide("dojo.uri.*");
dojo.provide("dojo.a11y");
dojo.a11y={imgPath:dojo.uri.dojoUri("src/widget/templates/images"),doAccessibleCheck:true,accessible:null,checkAccessible:function(){
if(this.accessible===null){
this.accessible=false;
if(this.doAccessibleCheck==true){
this.accessible=this.testAccessible();
}
}
return this.accessible;
},testAccessible:function(){
this.accessible=false;
if(dojo.render.html.ie||dojo.render.html.mozilla){
var div=document.createElement("div");
div.style.backgroundImage="url(\""+this.imgPath+"/tab_close.gif\")";
dojo.body().appendChild(div);
var _66c=null;
if(window.getComputedStyle){
var _66d=getComputedStyle(div,"");
_66c=_66d.getPropertyValue("background-image");
}else{
_66c=div.currentStyle.backgroundImage;
}
var _66e=false;
if(_66c!=null&&(_66c=="none"||_66c=="url(invalid-url:)")){
this.accessible=true;
}
dojo.body().removeChild(div);
}
return this.accessible;
},setCheckAccessible:function(_66f){
this.doAccessibleCheck=_66f;
},setAccessibleMode:function(){
if(this.accessible===null){
if(this.checkAccessible()){
dojo.render.html.prefixes.unshift("a11y");
}
}
return this.accessible;
}};
dojo.provide("dojo.widget.Widget");
dojo.declare("dojo.widget.Widget",null,function(){
this.children=[];
this.extraArgs={};
},{parent:null,children:[],extraArgs:{},isTopLevel:false,isModal:false,isEnabled:true,isHidden:false,isContainer:false,widgetId:"",widgetType:"Widget",ns:"dojo",getNamespacedType:function(){
return (this.ns?this.ns+":"+this.widgetType:this.widgetType).toLowerCase();
},toString:function(){
return "[Widget "+this.getNamespacedType()+", "+(this.widgetId||"NO ID")+"]";
},repr:function(){
return this.toString();
},enable:function(){
this.isEnabled=true;
},disable:function(){
this.isEnabled=false;
},hide:function(){
this.isHidden=true;
},show:function(){
this.isHidden=false;
},onResized:function(){
this.notifyChildrenOfResize();
},notifyChildrenOfResize:function(){
for(var i=0;i<this.children.length;i++){
var _671=this.children[i];
if(_671.onResized){
_671.onResized();
}
}
},create:function(args,_673,_674,ns){
if(ns){
this.ns=ns;
}
this.satisfyPropertySets(args,_673,_674);
this.mixInProperties(args,_673,_674);
this.postMixInProperties(args,_673,_674);
dojo.widget.manager.add(this);
this.buildRendering(args,_673,_674);
this.initialize(args,_673,_674);
this.postInitialize(args,_673,_674);
this.postCreate(args,_673,_674);
return this;
},destroy:function(_676){
this.destroyChildren();
this.uninitialize();
this.destroyRendering(_676);
dojo.widget.manager.removeById(this.widgetId);
},destroyChildren:function(){
var _677;
var i=0;
while(this.children.length>i){
_677=this.children[i];
if(_677 instanceof dojo.widget.Widget){
this.removeChild(_677);
_677.destroy();
continue;
}
i++;
}
},getChildrenOfType:function(type,_67a){
var ret=[];
var _67c=dojo.lang.isFunction(type);
if(!_67c){
type=type.toLowerCase();
}
for(var x=0;x<this.children.length;x++){
if(_67c){
if(this.children[x] instanceof type){
ret.push(this.children[x]);
}
}else{
if(this.children[x].widgetType.toLowerCase()==type){
ret.push(this.children[x]);
}
}
if(_67a){
ret=ret.concat(this.children[x].getChildrenOfType(type,_67a));
}
}
return ret;
},getDescendants:function(){
var _67e=[];
var _67f=[this];
var elem;
while((elem=_67f.pop())){
_67e.push(elem);
if(elem.children){
dojo.lang.forEach(elem.children,function(elem){
_67f.push(elem);
});
}
}
return _67e;
},isFirstChild:function(){
return this===this.parent.children[0];
},isLastChild:function(){
return this===this.parent.children[this.parent.children.length-1];
},satisfyPropertySets:function(args){
return args;
},mixInProperties:function(args,frag){
if((args["fastMixIn"])||(frag["fastMixIn"])){
for(var x in args){
this[x]=args[x];
}
return;
}
var _686;
var _687=dojo.widget.lcArgsCache[this.widgetType];
if(_687==null){
_687={};
for(var y in this){
_687[((new String(y)).toLowerCase())]=y;
}
dojo.widget.lcArgsCache[this.widgetType]=_687;
}
var _689={};
for(var x in args){
if(!this[x]){
var y=_687[(new String(x)).toLowerCase()];
if(y){
args[y]=args[x];
x=y;
}
}
if(_689[x]){
continue;
}
_689[x]=true;
if((typeof this[x])!=(typeof _686)){
if(typeof args[x]!="string"){
this[x]=args[x];
}else{
if(dojo.lang.isString(this[x])){
this[x]=args[x];
}else{
if(dojo.lang.isNumber(this[x])){
this[x]=new Number(args[x]);
}else{
if(dojo.lang.isBoolean(this[x])){
this[x]=(args[x].toLowerCase()=="false")?false:true;
}else{
if(dojo.lang.isFunction(this[x])){
if(args[x].search(/[^\w\.]+/i)==-1){
this[x]=dojo.evalObjPath(args[x],false);
}else{
var tn=dojo.lang.nameAnonFunc(new Function(args[x]),this);
dojo.event.kwConnect({srcObj:this,srcFunc:x,adviceObj:this,adviceFunc:tn});
}
}else{
if(dojo.lang.isArray(this[x])){
this[x]=args[x].split(";");
}else{
if(this[x] instanceof Date){
this[x]=new Date(Number(args[x]));
}else{
if(typeof this[x]=="object"){
if(this[x] instanceof dojo.uri.Uri){
this[x]=args[x];
}else{
var _68b=args[x].split(";");
for(var y=0;y<_68b.length;y++){
var si=_68b[y].indexOf(":");
if((si!=-1)&&(_68b[y].length>si)){
this[x][_68b[y].substr(0,si).replace(/^\s+|\s+$/g,"")]=_68b[y].substr(si+1);
}
}
}
}else{
this[x]=args[x];
}
}
}
}
}
}
}
}
}else{
this.extraArgs[x.toLowerCase()]=args[x];
}
}
},postMixInProperties:function(args,frag,_68f){
},initialize:function(args,frag,_692){
return false;
},postInitialize:function(args,frag,_695){
return false;
},postCreate:function(args,frag,_698){
return false;
},uninitialize:function(){
return false;
},buildRendering:function(args,frag,_69b){
dojo.unimplemented("dojo.widget.Widget.buildRendering, on "+this.toString()+", ");
return false;
},destroyRendering:function(){
dojo.unimplemented("dojo.widget.Widget.destroyRendering");
return false;
},cleanUp:function(){
dojo.unimplemented("dojo.widget.Widget.cleanUp");
return false;
},addedTo:function(_69c){
},addChild:function(_69d){
dojo.unimplemented("dojo.widget.Widget.addChild");
return false;
},removeChild:function(_69e){
for(var x=0;x<this.children.length;x++){
if(this.children[x]===_69e){
this.children.splice(x,1);
break;
}
}
return _69e;
},resize:function(_6a0,_6a1){
this.setWidth(_6a0);
this.setHeight(_6a1);
},setWidth:function(_6a2){
if((typeof _6a2=="string")&&(_6a2.substr(-1)=="%")){
this.setPercentageWidth(_6a2);
}else{
this.setNativeWidth(_6a2);
}
},setHeight:function(_6a3){
if((typeof _6a3=="string")&&(_6a3.substr(-1)=="%")){
this.setPercentageHeight(_6a3);
}else{
this.setNativeHeight(_6a3);
}
},setPercentageHeight:function(_6a4){
return false;
},setNativeHeight:function(_6a5){
return false;
},setPercentageWidth:function(_6a6){
return false;
},setNativeWidth:function(_6a7){
return false;
},getPreviousSibling:function(){
var idx=this.getParentIndex();
if(idx<=0){
return null;
}
return this.parent.children[idx-1];
},getSiblings:function(){
return this.parent.children;
},getParentIndex:function(){
return dojo.lang.indexOf(this.parent.children,this,true);
},getNextSibling:function(){
var idx=this.getParentIndex();
if(idx==this.parent.children.length-1){
return null;
}
if(idx<0){
return null;
}
return this.parent.children[idx+1];
}});
dojo.widget.lcArgsCache={};
dojo.widget.tags={};
dojo.widget.tags.addParseTreeHandler=function(type){
dojo.deprecated("addParseTreeHandler",". ParseTreeHandlers are now reserved for components. Any unfiltered DojoML tag without a ParseTreeHandler is assumed to be a widget","0.5");
};
dojo.widget.tags["dojo:propertyset"]=function(_6ab,_6ac,_6ad){
var _6ae=_6ac.parseProperties(_6ab["dojo:propertyset"]);
};
dojo.widget.tags["dojo:connect"]=function(_6af,_6b0,_6b1){
var _6b2=_6b0.parseProperties(_6af["dojo:connect"]);
};
dojo.widget.buildWidgetFromParseTree=function(type,frag,_6b5,_6b6,_6b7,_6b8){
dojo.a11y.setAccessibleMode();
var _6b9=type.split(":");
_6b9=(_6b9.length==2)?_6b9[1]:type;
var _6ba=_6b8||_6b5.parseProperties(frag[frag["ns"]+":"+_6b9]);
var _6bb=dojo.widget.manager.getImplementation(_6b9,null,null,frag["ns"]);
if(!_6bb){
throw new Error("cannot find \""+type+"\" widget");
}else{
if(!_6bb.create){
throw new Error("\""+type+"\" widget object has no \"create\" method and does not appear to implement *Widget");
}
}
_6ba["dojoinsertionindex"]=_6b7;
var ret=_6bb.create(_6ba,frag,_6b6,frag["ns"]);
return ret;
};
dojo.widget.defineWidget=function(_6bd,_6be,_6bf,init,_6c1){
if(dojo.lang.isString(arguments[3])){
dojo.widget._defineWidget(arguments[0],arguments[3],arguments[1],arguments[4],arguments[2]);
}else{
var args=[arguments[0]],p=3;
if(dojo.lang.isString(arguments[1])){
args.push(arguments[1],arguments[2]);
}else{
args.push("",arguments[1]);
p=2;
}
if(dojo.lang.isFunction(arguments[p])){
args.push(arguments[p],arguments[p+1]);
}else{
args.push(null,arguments[p]);
}
dojo.widget._defineWidget.apply(this,args);
}
};
dojo.widget.defineWidget.renderers="html|svg|vml";
dojo.widget._defineWidget=function(_6c4,_6c5,_6c6,init,_6c8){
var _6c9=_6c4.split(".");
var type=_6c9.pop();
var regx="\\.("+(_6c5?_6c5+"|":"")+dojo.widget.defineWidget.renderers+")\\.";
var r=_6c4.search(new RegExp(regx));
_6c9=(r<0?_6c9.join("."):_6c4.substr(0,r));
dojo.widget.manager.registerWidgetPackage(_6c9);
var pos=_6c9.indexOf(".");
var _6ce=(pos>-1)?_6c9.substring(0,pos):_6c9;
_6c8=(_6c8)||{};
_6c8.widgetType=type;
if((!init)&&(_6c8["classConstructor"])){
init=_6c8.classConstructor;
delete _6c8.classConstructor;
}
dojo.declare(_6c4,_6c6,init,_6c8);
};
dojo.provide("dojo.widget.Parse");
dojo.widget.Parse=function(_6cf){
this.propertySetsList=[];
this.fragment=_6cf;
this.createComponents=function(frag,_6d1){
var _6d2=[];
var _6d3=false;
try{
if((frag)&&(frag["tagName"])&&(frag!=frag["nodeRef"])){
var _6d4=dojo.widget.tags;
var tna=String(frag["tagName"]).split(";");
for(var x=0;x<tna.length;x++){
var ltn=(tna[x].replace(/^\s+|\s+$/g,"")).toLowerCase();
frag.tagName=ltn;
if(_6d4[ltn]){
_6d3=true;
var ret=_6d4[ltn](frag,this,_6d1,frag["index"]);
_6d2.push(ret);
}else{
if(ltn.indexOf(":")==-1){
ltn="dojo:"+ltn;
}
var ret=dojo.widget.buildWidgetFromParseTree(ltn,frag,this,_6d1,frag["index"]);
if(ret){
_6d3=true;
_6d2.push(ret);
}
}
}
}
}
catch(e){
dojo.debug("dojo.widget.Parse: error:"+e);
}
if(!_6d3){
_6d2=_6d2.concat(this.createSubComponents(frag,_6d1));
}
return _6d2;
};
this.createSubComponents=function(_6d9,_6da){
var frag,_6dc=[];
for(var item in _6d9){
frag=_6d9[item];
if((frag)&&(typeof frag=="object")&&(frag!=_6d9.nodeRef)&&(frag!=_6d9["tagName"])){
_6dc=_6dc.concat(this.createComponents(frag,_6da));
}
}
return _6dc;
};
this.parsePropertySets=function(_6de){
return [];
};
this.parseProperties=function(_6df){
var _6e0={};
for(var item in _6df){
if((_6df[item]==_6df["tagName"])||(_6df[item]==_6df.nodeRef)){
}else{
if((_6df[item]["tagName"])&&(dojo.widget.tags[_6df[item].tagName.toLowerCase()])){
}else{
if((_6df[item][0])&&(_6df[item][0].value!="")&&(_6df[item][0].value!=null)){
try{
if(item.toLowerCase()=="dataprovider"){
var _6e2=this;
this.getDataProvider(_6e2,_6df[item][0].value);
_6e0.dataProvider=this.dataProvider;
}
_6e0[item]=_6df[item][0].value;
var _6e3=this.parseProperties(_6df[item]);
for(var _6e4 in _6e3){
_6e0[_6e4]=_6e3[_6e4];
}
}
catch(e){
dojo.debug(e);
}
}
}
switch(item.toLowerCase()){
case "checked":
case "disabled":
if(typeof _6e0[item]!="boolean"){
_6e0[item]=true;
}
break;
}
}
}
return _6e0;
};
this.getDataProvider=function(_6e5,_6e6){
dojo.io.bind({url:_6e6,load:function(type,_6e8){
if(type=="load"){
_6e5.dataProvider=_6e8;
}
},mimetype:"text/javascript",sync:true});
};
this.getPropertySetById=function(_6e9){
for(var x=0;x<this.propertySetsList.length;x++){
if(_6e9==this.propertySetsList[x]["id"][0].value){
return this.propertySetsList[x];
}
}
return "";
};
this.getPropertySetsByType=function(_6eb){
var _6ec=[];
for(var x=0;x<this.propertySetsList.length;x++){
var cpl=this.propertySetsList[x];
var cpcc=cpl["componentClass"]||cpl["componentType"]||null;
var _6f0=this.propertySetsList[x]["id"][0].value;
if((cpcc)&&(_6f0==cpcc[0].value)){
_6ec.push(cpl);
}
}
return _6ec;
};
this.getPropertySets=function(_6f1){
var ppl="dojo:propertyproviderlist";
var _6f3=[];
var _6f4=_6f1["tagName"];
if(_6f1[ppl]){
var _6f5=_6f1[ppl].value.split(" ");
for(var _6f6 in _6f5){
if((_6f6.indexOf("..")==-1)&&(_6f6.indexOf("://")==-1)){
var _6f7=this.getPropertySetById(_6f6);
if(_6f7!=""){
_6f3.push(_6f7);
}
}else{
}
}
}
return (this.getPropertySetsByType(_6f4)).concat(_6f3);
};
this.createComponentFromScript=function(_6f8,_6f9,_6fa,ns){
_6fa.fastMixIn=true;
var ltn=(ns||"dojo")+":"+_6f9.toLowerCase();
if(dojo.widget.tags[ltn]){
return [dojo.widget.tags[ltn](_6fa,this,null,null,_6fa)];
}
return [dojo.widget.buildWidgetFromParseTree(ltn,_6fa,this,null,null,_6fa)];
};
};
dojo.widget._parser_collection={"dojo":new dojo.widget.Parse()};
dojo.widget.getParser=function(name){
if(!name){
name="dojo";
}
if(!this._parser_collection[name]){
this._parser_collection[name]=new dojo.widget.Parse();
}
return this._parser_collection[name];
};
dojo.widget.createWidget=function(name,_6ff,_700,_701){
var _702=false;
var _703=(typeof name=="string");
if(_703){
var pos=name.indexOf(":");
var ns=(pos>-1)?name.substring(0,pos):"dojo";
if(pos>-1){
name=name.substring(pos+1);
}
var _706=name.toLowerCase();
var _707=ns+":"+_706;
_702=(dojo.byId(name)&&(!dojo.widget.tags[_707]));
}
if((arguments.length==1)&&((_702)||(!_703))){
var xp=new dojo.xml.Parse();
var tn=(_702)?dojo.byId(name):name;
return dojo.widget.getParser().createComponents(xp.parseElement(tn,null,true))[0];
}
function fromScript(_70a,name,_70c,ns){
_70c[_707]={dojotype:[{value:_706}],nodeRef:_70a,fastMixIn:true};
_70c.ns=ns;
return dojo.widget.getParser().createComponentFromScript(_70a,name,_70c,ns);
}
_6ff=_6ff||{};
var _70e=false;
var tn=null;
var h=dojo.render.html.capable;
if(h){
tn=document.createElement("span");
}
if(!_700){
_70e=true;
_700=tn;
if(h){
dojo.body().appendChild(_700);
}
}else{
if(_701){
dojo.dom.insertAtPosition(tn,_700,_701);
}else{
tn=_700;
}
}
var _710=fromScript(tn,name.toLowerCase(),_6ff,ns);
if((!_710)||(!_710[0])||(typeof _710[0].widgetType=="undefined")){
throw new Error("createWidget: Creation of \""+name+"\" widget failed.");
}
try{
if(_70e){
if(_710[0].domNode.parentNode){
_710[0].domNode.parentNode.removeChild(_710[0].domNode);
}
}
}
catch(e){
dojo.debug(e);
}
return _710[0];
};
dojo.provide("dojo.widget.DomWidget");
dojo.widget._cssFiles={};
dojo.widget._cssStrings={};
dojo.widget._templateCache={};
dojo.widget.defaultStrings={dojoRoot:dojo.hostenv.getBaseScriptUri(),baseScriptUri:dojo.hostenv.getBaseScriptUri()};
dojo.widget.fillFromTemplateCache=function(obj,_712,_713,_714){
var _715=_712||obj.templatePath;
var _716=dojo.widget._templateCache;
if(!obj["widgetType"]){
do{
var _717="__dummyTemplate__"+dojo.widget._templateCache.dummyCount++;
}while(_716[_717]);
obj.widgetType=_717;
}
var wt=obj.widgetType;
var ts=_716[wt];
if(!ts){
_716[wt]={"string":null,"node":null};
if(_714){
ts={};
}else{
ts=_716[wt];
}
}
if((!obj.templateString)&&(!_714)){
obj.templateString=_713||ts["string"];
}
if((!obj.templateNode)&&(!_714)){
obj.templateNode=ts["node"];
}
if((!obj.templateNode)&&(!obj.templateString)&&(_715)){
var _71a=dojo.hostenv.getText(_715);
if(_71a){
_71a=_71a.replace(/^\s*<\?xml(\s)+version=[\'\"](\d)*.(\d)*[\'\"](\s)*\?>/im,"");
var _71b=_71a.match(/<body[^>]*>\s*([\s\S]+)\s*<\/body>/im);
if(_71b){
_71a=_71b[1];
}
}else{
_71a="";
}
obj.templateString=_71a;
if(!_714){
_716[wt]["string"]=_71a;
}
}
if((!ts["string"])&&(!_714)){
ts.string=obj.templateString;
}
};
dojo.widget._templateCache.dummyCount=0;
dojo.widget.attachProperties=["dojoAttachPoint","id"];
dojo.widget.eventAttachProperty="dojoAttachEvent";
dojo.widget.onBuildProperty="dojoOnBuild";
dojo.widget.waiNames=["waiRole","waiState"];
dojo.widget.wai={waiRole:{name:"waiRole","namespace":"http://www.w3.org/TR/xhtml2",alias:"x2",prefix:"wairole:"},waiState:{name:"waiState","namespace":"http://www.w3.org/2005/07/aaa",alias:"aaa",prefix:""},setAttr:function(node,ns,attr,_71f){
if(dojo.render.html.ie){
node.setAttribute(this[ns].alias+":"+attr,this[ns].prefix+_71f);
}else{
node.setAttributeNS(this[ns]["namespace"],attr,this[ns].prefix+_71f);
}
},getAttr:function(node,ns,attr){
if(dojo.render.html.ie){
return node.getAttribute(this[ns].alias+":"+attr);
}else{
return node.getAttributeNS(this[ns]["namespace"],attr);
}
},removeAttr:function(node,ns,attr){
var _726=true;
if(dojo.render.html.ie){
_726=node.removeAttribute(this[ns].alias+":"+attr);
}else{
node.removeAttributeNS(this[ns]["namespace"],attr);
}
return _726;
}};
dojo.widget.attachTemplateNodes=function(_727,_728,_729){
var _72a=dojo.dom.ELEMENT_NODE;
function trim(str){
return str.replace(/^\s+|\s+$/g,"");
}
if(!_727){
_727=_728.domNode;
}
if(_727.nodeType!=_72a){
return;
}
var _72c=_727.all||_727.getElementsByTagName("*");
var _72d=_728;
for(var x=-1;x<_72c.length;x++){
var _72f=(x==-1)?_727:_72c[x];
var _730=[];
if(!_728.widgetsInTemplate||!_72f.getAttribute("dojoType")){
for(var y=0;y<this.attachProperties.length;y++){
var _732=_72f.getAttribute(this.attachProperties[y]);
if(_732){
_730=_732.split(";");
for(var z=0;z<_730.length;z++){
if(dojo.lang.isArray(_728[_730[z]])){
_728[_730[z]].push(_72f);
}else{
_728[_730[z]]=_72f;
}
}
break;
}
}
var _734=_72f.getAttribute(this.eventAttachProperty);
if(_734){
var evts=_734.split(";");
for(var y=0;y<evts.length;y++){
if((!evts[y])||(!evts[y].length)){
continue;
}
var _736=null;
var tevt=trim(evts[y]);
if(evts[y].indexOf(":")>=0){
var _738=tevt.split(":");
tevt=trim(_738[0]);
_736=trim(_738[1]);
}
if(!_736){
_736=tevt;
}
var tf=function(){
var ntf=new String(_736);
return function(evt){
if(_72d[ntf]){
_72d[ntf](dojo.event.browser.fixEvent(evt,this));
}
};
}();
dojo.event.browser.addListener(_72f,tevt,tf,false,true);
}
}
for(var y=0;y<_729.length;y++){
var _73c=_72f.getAttribute(_729[y]);
if((_73c)&&(_73c.length)){
var _736=null;
var _73d=_729[y].substr(4);
_736=trim(_73c);
var _73e=[_736];
if(_736.indexOf(";")>=0){
_73e=dojo.lang.map(_736.split(";"),trim);
}
for(var z=0;z<_73e.length;z++){
if(!_73e[z].length){
continue;
}
var tf=function(){
var ntf=new String(_73e[z]);
return function(evt){
if(_72d[ntf]){
_72d[ntf](dojo.event.browser.fixEvent(evt,this));
}
};
}();
dojo.event.browser.addListener(_72f,_73d,tf,false,true);
}
}
}
}
var _741=_72f.getAttribute(this.templateProperty);
if(_741){
_728[_741]=_72f;
}
dojo.lang.forEach(dojo.widget.waiNames,function(name){
var wai=dojo.widget.wai[name];
var val=_72f.getAttribute(wai.name);
if(val){
if(val.indexOf("-")==-1){
dojo.widget.wai.setAttr(_72f,wai.name,"role",val);
}else{
var _745=val.split("-");
dojo.widget.wai.setAttr(_72f,wai.name,_745[0],_745[1]);
}
}
},this);
var _746=_72f.getAttribute(this.onBuildProperty);
if(_746){
eval("var node = baseNode; var widget = targetObj; "+_746);
}
}
};
dojo.widget.getDojoEventsFromStr=function(str){
var re=/(dojoOn([a-z]+)(\s?))=/gi;
var evts=str?str.match(re)||[]:[];
var ret=[];
var lem={};
for(var x=0;x<evts.length;x++){
if(evts[x].length<1){
continue;
}
var cm=evts[x].replace(/\s/,"");
cm=(cm.slice(0,cm.length-1));
if(!lem[cm]){
lem[cm]=true;
ret.push(cm);
}
}
return ret;
};
dojo.declare("dojo.widget.DomWidget",dojo.widget.Widget,function(){
if((arguments.length>0)&&(typeof arguments[0]=="object")){
this.create(arguments[0]);
}
},{templateNode:null,templateString:null,templateCssString:null,preventClobber:false,domNode:null,containerNode:null,widgetsInTemplate:false,addChild:function(_74e,_74f,pos,ref,_752){
if(!this.isContainer){
dojo.debug("dojo.widget.DomWidget.addChild() attempted on non-container widget");
return null;
}else{
if(_752==undefined){
_752=this.children.length;
}
this.addWidgetAsDirectChild(_74e,_74f,pos,ref,_752);
this.registerChild(_74e,_752);
}
return _74e;
},addWidgetAsDirectChild:function(_753,_754,pos,ref,_757){
if((!this.containerNode)&&(!_754)){
this.containerNode=this.domNode;
}
var cn=(_754)?_754:this.containerNode;
if(!pos){
pos="after";
}
if(!ref){
if(!cn){
cn=dojo.body();
}
ref=cn.lastChild;
}
if(!_757){
_757=0;
}
_753.domNode.setAttribute("dojoinsertionindex",_757);
if(!ref){
cn.appendChild(_753.domNode);
}else{
if(pos=="insertAtIndex"){
dojo.dom.insertAtIndex(_753.domNode,ref.parentNode,_757);
}else{
if((pos=="after")&&(ref===cn.lastChild)){
cn.appendChild(_753.domNode);
}else{
dojo.dom.insertAtPosition(_753.domNode,cn,pos);
}
}
}
},registerChild:function(_759,_75a){
_759.dojoInsertionIndex=_75a;
var idx=-1;
for(var i=0;i<this.children.length;i++){
if(this.children[i].dojoInsertionIndex<=_75a){
idx=i;
}
}
this.children.splice(idx+1,0,_759);
_759.parent=this;
_759.addedTo(this,idx+1);
delete dojo.widget.manager.topWidgets[_759.widgetId];
},removeChild:function(_75d){
dojo.dom.removeNode(_75d.domNode);
return dojo.widget.DomWidget.superclass.removeChild.call(this,_75d);
},getFragNodeRef:function(frag){
if(!frag){
return null;
}
if(!frag[this.getNamespacedType()]){
dojo.raise("Error: no frag for widget type "+this.getNamespacedType()+", id "+this.widgetId+" (maybe a widget has set it's type incorrectly)");
}
return frag[this.getNamespacedType()]["nodeRef"];
},postInitialize:function(args,frag,_761){
var _762=this.getFragNodeRef(frag);
if(_761&&(_761.snarfChildDomOutput||!_762)){
_761.addWidgetAsDirectChild(this,"","insertAtIndex","",args["dojoinsertionindex"],_762);
}else{
if(_762){
if(this.domNode&&(this.domNode!==_762)){
var _763=_762.parentNode.replaceChild(this.domNode,_762);
}
}
}
if(_761){
_761.registerChild(this,args.dojoinsertionindex);
}else{
dojo.widget.manager.topWidgets[this.widgetId]=this;
}
if(this.widgetsInTemplate){
var _764=new dojo.xml.Parse();
var _765;
var _766=this.domNode.getElementsByTagName("*");
for(var i=0;i<_766.length;i++){
if(_766[i].getAttribute("dojoAttachPoint")=="subContainerWidget"){
_765=_766[i];
}
if(_766[i].getAttribute("dojoType")){
_766[i].setAttribute("_isSubWidget",true);
}
}
if(this.isContainer&&!this.containerNode){
if(_765){
var src=this.getFragNodeRef(frag);
if(src){
dojo.dom.moveChildren(src,_765);
frag["dojoDontFollow"]=true;
}
}else{
dojo.debug("No subContainerWidget node can be found in template file for widget "+this);
}
}
var _769=_764.parseElement(this.domNode,null,true);
dojo.widget.getParser().createSubComponents(_769,this);
var _76a=[];
var _76b=[this];
var w;
while((w=_76b.pop())){
for(var i=0;i<w.children.length;i++){
var _76d=w.children[i];
if(_76d._processedSubWidgets||!_76d.extraArgs["_issubwidget"]){
continue;
}
_76a.push(_76d);
if(_76d.isContainer){
_76b.push(_76d);
}
}
}
for(var i=0;i<_76a.length;i++){
var _76e=_76a[i];
if(_76e._processedSubWidgets){
dojo.debug("This should not happen: widget._processedSubWidgets is already true!");
return;
}
_76e._processedSubWidgets=true;
if(_76e.extraArgs["dojoattachevent"]){
var evts=_76e.extraArgs["dojoattachevent"].split(";");
for(var j=0;j<evts.length;j++){
var _771=null;
var tevt=dojo.string.trim(evts[j]);
if(tevt.indexOf(":")>=0){
var _773=tevt.split(":");
tevt=dojo.string.trim(_773[0]);
_771=dojo.string.trim(_773[1]);
}
if(!_771){
_771=tevt;
}
if(dojo.lang.isFunction(_76e[tevt])){
dojo.event.kwConnect({srcObj:_76e,srcFunc:tevt,targetObj:this,targetFunc:_771});
}else{
alert(tevt+" is not a function in widget "+_76e);
}
}
}
if(_76e.extraArgs["dojoattachpoint"]){
this[_76e.extraArgs["dojoattachpoint"]]=_76e;
}
}
}
if(this.isContainer&&!frag["dojoDontFollow"]){
dojo.widget.getParser().createSubComponents(frag,this);
}
},buildRendering:function(args,frag){
var ts=dojo.widget._templateCache[this.widgetType];
if(args["templatecsspath"]){
args["templateCssPath"]=args["templatecsspath"];
}
var _777=args["templateCssPath"]||this.templateCssPath;
if(_777&&!dojo.widget._cssFiles[_777.toString()]){
if((!this.templateCssString)&&(_777)){
this.templateCssString=dojo.hostenv.getText(_777);
this.templateCssPath=null;
}
dojo.widget._cssFiles[_777.toString()]=true;
}
if((this["templateCssString"])&&(!this.templateCssString["loaded"])){
dojo.html.insertCssText(this.templateCssString,null,_777);
if(!this.templateCssString){
this.templateCssString="";
}
this.templateCssString.loaded=true;
}
if((!this.preventClobber)&&((this.templatePath)||(this.templateNode)||((this["templateString"])&&(this.templateString.length))||((typeof ts!="undefined")&&((ts["string"])||(ts["node"]))))){
this.buildFromTemplate(args,frag);
}else{
this.domNode=this.getFragNodeRef(frag);
}
this.fillInTemplate(args,frag);
},buildFromTemplate:function(args,frag){
var _77a=false;
if(args["templatepath"]){
_77a=true;
args["templatePath"]=args["templatepath"];
}
dojo.widget.fillFromTemplateCache(this,args["templatePath"],null,_77a);
var ts=dojo.widget._templateCache[this.widgetType];
if((ts)&&(!_77a)){
if(!this.templateString.length){
this.templateString=ts["string"];
}
if(!this.templateNode){
this.templateNode=ts["node"];
}
}
var _77c=false;
var node=null;
var tstr=this.templateString;
if((!this.templateNode)&&(this.templateString)){
_77c=this.templateString.match(/\$\{([^\}]+)\}/g);
if(_77c){
var hash=this.strings||{};
for(var key in dojo.widget.defaultStrings){
if(dojo.lang.isUndefined(hash[key])){
hash[key]=dojo.widget.defaultStrings[key];
}
}
for(var i=0;i<_77c.length;i++){
var key=_77c[i];
key=key.substring(2,key.length-1);
var kval=(key.substring(0,5)=="this.")?dojo.lang.getObjPathValue(key.substring(5),this):hash[key];
var _783;
if((kval)||(dojo.lang.isString(kval))){
_783=new String((dojo.lang.isFunction(kval))?kval.call(this,key,this.templateString):kval);
while(_783.indexOf("\"")>-1){
_783=_783.replace("\"","&quot;");
}
tstr=tstr.replace(_77c[i],_783);
}
}
}else{
this.templateNode=this.createNodesFromText(this.templateString,true)[0];
if(!_77a){
ts.node=this.templateNode;
}
}
}
if((!this.templateNode)&&(!_77c)){
dojo.debug("DomWidget.buildFromTemplate: could not create template");
return false;
}else{
if(!_77c){
node=this.templateNode.cloneNode(true);
if(!node){
return false;
}
}else{
node=this.createNodesFromText(tstr,true)[0];
}
}
this.domNode=node;
this.attachTemplateNodes();
if(this.isContainer&&this.containerNode){
var src=this.getFragNodeRef(frag);
if(src){
dojo.dom.moveChildren(src,this.containerNode);
}
}
},attachTemplateNodes:function(_785,_786){
if(!_785){
_785=this.domNode;
}
if(!_786){
_786=this;
}
return dojo.widget.attachTemplateNodes(_785,_786,dojo.widget.getDojoEventsFromStr(this.templateString));
},fillInTemplate:function(){
},destroyRendering:function(){
try{
delete this.domNode;
}
catch(e){
}
},cleanUp:function(){
},getContainerHeight:function(){
dojo.unimplemented("dojo.widget.DomWidget.getContainerHeight");
},getContainerWidth:function(){
dojo.unimplemented("dojo.widget.DomWidget.getContainerWidth");
},createNodesFromText:function(){
dojo.unimplemented("dojo.widget.DomWidget.createNodesFromText");
}});
dojo.provide("dojo.html.util");
dojo.html.getElementWindow=function(_787){
return dojo.html.getDocumentWindow(_787.ownerDocument);
};
dojo.html.getDocumentWindow=function(doc){
if(dojo.render.html.safari&&!doc._parentWindow){
var fix=function(win){
win.document._parentWindow=win;
for(var i=0;i<win.frames.length;i++){
fix(win.frames[i]);
}
};
fix(window.top);
}
if(dojo.render.html.ie&&window!==document.parentWindow&&!doc._parentWindow){
doc.parentWindow.execScript("document._parentWindow = window;","Javascript");
var win=doc._parentWindow;
doc._parentWindow=null;
return win;
}
return doc._parentWindow||doc.parentWindow||doc.defaultView;
};
dojo.html.gravity=function(node,e){
node=dojo.byId(node);
var _78f=dojo.html.getCursorPosition(e);
with(dojo.html){
var _790=getAbsolutePosition(node,true);
var bb=getBorderBox(node);
var _792=_790.x+(bb.width/2);
var _793=_790.y+(bb.height/2);
}
with(dojo.html.gravity){
return ((_78f.x<_792?WEST:EAST)|(_78f.y<_793?NORTH:SOUTH));
}
};
dojo.html.gravity.NORTH=1;
dojo.html.gravity.SOUTH=1<<1;
dojo.html.gravity.EAST=1<<2;
dojo.html.gravity.WEST=1<<3;
dojo.html.overElement=function(_794,e){
_794=dojo.byId(_794);
var _796=dojo.html.getCursorPosition(e);
var bb=dojo.html.getBorderBox(_794);
var _798=dojo.html.getAbsolutePosition(_794,true,dojo.html.boxSizing.BORDER_BOX);
var top=_798.y;
var _79a=top+bb.height;
var left=_798.x;
var _79c=left+bb.width;
return (_796.x>=left&&_796.x<=_79c&&_796.y>=top&&_796.y<=_79a);
};
dojo.html.renderedTextContent=function(node){
node=dojo.byId(node);
var _79e="";
if(node==null){
return _79e;
}
for(var i=0;i<node.childNodes.length;i++){
switch(node.childNodes[i].nodeType){
case 1:
case 5:
var _7a0="unknown";
try{
_7a0=dojo.html.getStyle(node.childNodes[i],"display");
}
catch(E){
}
switch(_7a0){
case "block":
case "list-item":
case "run-in":
case "table":
case "table-row-group":
case "table-header-group":
case "table-footer-group":
case "table-row":
case "table-column-group":
case "table-column":
case "table-cell":
case "table-caption":
_79e+="\n";
_79e+=dojo.html.renderedTextContent(node.childNodes[i]);
_79e+="\n";
break;
case "none":
break;
default:
if(node.childNodes[i].tagName&&node.childNodes[i].tagName.toLowerCase()=="br"){
_79e+="\n";
}else{
_79e+=dojo.html.renderedTextContent(node.childNodes[i]);
}
break;
}
break;
case 3:
case 2:
case 4:
var text=node.childNodes[i].nodeValue;
var _7a2="unknown";
try{
_7a2=dojo.html.getStyle(node,"text-transform");
}
catch(E){
}
switch(_7a2){
case "capitalize":
var _7a3=text.split(" ");
for(var i=0;i<_7a3.length;i++){
_7a3[i]=_7a3[i].charAt(0).toUpperCase()+_7a3[i].substring(1);
}
text=_7a3.join(" ");
break;
case "uppercase":
text=text.toUpperCase();
break;
case "lowercase":
text=text.toLowerCase();
break;
default:
break;
}
switch(_7a2){
case "nowrap":
break;
case "pre-wrap":
break;
case "pre-line":
break;
case "pre":
break;
default:
text=text.replace(/\s+/," ");
if(/\s$/.test(_79e)){
text.replace(/^\s/,"");
}
break;
}
_79e+=text;
break;
default:
break;
}
}
return _79e;
};
dojo.html.createNodesFromText=function(txt,trim){
if(trim){
txt=txt.replace(/^\s+|\s+$/g,"");
}
var tn=dojo.doc().createElement("div");
tn.style.visibility="hidden";
dojo.body().appendChild(tn);
var _7a7="none";
if((/^<t[dh][\s\r\n>]/i).test(txt.replace(/^\s+/))){
txt="<table><tbody><tr>"+txt+"</tr></tbody></table>";
_7a7="cell";
}else{
if((/^<tr[\s\r\n>]/i).test(txt.replace(/^\s+/))){
txt="<table><tbody>"+txt+"</tbody></table>";
_7a7="row";
}else{
if((/^<(thead|tbody|tfoot)[\s\r\n>]/i).test(txt.replace(/^\s+/))){
txt="<table>"+txt+"</table>";
_7a7="section";
}
}
}
tn.innerHTML=txt;
if(tn["normalize"]){
tn.normalize();
}
var _7a8=null;
switch(_7a7){
case "cell":
_7a8=tn.getElementsByTagName("tr")[0];
break;
case "row":
_7a8=tn.getElementsByTagName("tbody")[0];
break;
case "section":
_7a8=tn.getElementsByTagName("table")[0];
break;
default:
_7a8=tn;
break;
}
var _7a9=[];
for(var x=0;x<_7a8.childNodes.length;x++){
_7a9.push(_7a8.childNodes[x].cloneNode(true));
}
tn.style.display="none";
dojo.body().removeChild(tn);
return _7a9;
};
dojo.html.placeOnScreen=function(node,_7ac,_7ad,_7ae,_7af,_7b0,_7b1){
if(_7ac instanceof Array||typeof _7ac=="array"){
_7b1=_7b0;
_7b0=_7af;
_7af=_7ae;
_7ae=_7ad;
_7ad=_7ac[1];
_7ac=_7ac[0];
}
if(_7b0 instanceof String||typeof _7b0=="string"){
_7b0=_7b0.split(",");
}
if(!isNaN(_7ae)){
_7ae=[Number(_7ae),Number(_7ae)];
}else{
if(!(_7ae instanceof Array||typeof _7ae=="array")){
_7ae=[0,0];
}
}
var _7b2=dojo.html.getScroll().offset;
var view=dojo.html.getViewport();
node=dojo.byId(node);
var _7b4=node.style.display;
node.style.display="";
var bb=dojo.html.getBorderBox(node);
var w=bb.width;
var h=bb.height;
node.style.display=_7b4;
if(!(_7b0 instanceof Array||typeof _7b0=="array")){
_7b0=["TL"];
}
var _7b8,_7b9,_7ba=Infinity,_7bb;
for(var _7bc=0;_7bc<_7b0.length;++_7bc){
var _7bd=_7b0[_7bc];
var _7be=true;
var tryX=_7ac-(_7bd.charAt(1)=="L"?0:w)+_7ae[0]*(_7bd.charAt(1)=="L"?1:-1);
var tryY=_7ad-(_7bd.charAt(0)=="T"?0:h)+_7ae[1]*(_7bd.charAt(0)=="T"?1:-1);
if(_7af){
tryX-=_7b2.x;
tryY-=_7b2.y;
}
if(tryX<0){
tryX=0;
_7be=false;
}
if(tryY<0){
tryY=0;
_7be=false;
}
var x=tryX+w;
if(x>view.width){
x=view.width-w;
_7be=false;
}else{
x=tryX;
}
x=Math.max(_7ae[0],x)+_7b2.x;
var y=tryY+h;
if(y>view.height){
y=view.height-h;
_7be=false;
}else{
y=tryY;
}
y=Math.max(_7ae[1],y)+_7b2.y;
if(_7be){
_7b8=x;
_7b9=y;
_7ba=0;
_7bb=_7bd;
break;
}else{
var dist=Math.pow(x-tryX-_7b2.x,2)+Math.pow(y-tryY-_7b2.y,2);
if(_7ba>dist){
_7ba=dist;
_7b8=x;
_7b9=y;
_7bb=_7bd;
}
}
}
if(!_7b1){
node.style.left=_7b8+"px";
node.style.top=_7b9+"px";
}
return {left:_7b8,top:_7b9,x:_7b8,y:_7b9,dist:_7ba,corner:_7bb};
};
dojo.html.placeOnScreenPoint=function(node,_7c5,_7c6,_7c7,_7c8){
dojo.deprecated("dojo.html.placeOnScreenPoint","use dojo.html.placeOnScreen() instead","0.5");
return dojo.html.placeOnScreen(node,_7c5,_7c6,_7c7,_7c8,["TL","TR","BL","BR"]);
};
dojo.html.placeOnScreenAroundElement=function(node,_7ca,_7cb,_7cc,_7cd,_7ce){
var best,_7d0=Infinity;
_7ca=dojo.byId(_7ca);
var _7d1=_7ca.style.display;
_7ca.style.display="";
var mb=dojo.html.getElementBox(_7ca,_7cc);
var _7d3=mb.width;
var _7d4=mb.height;
var _7d5=dojo.html.getAbsolutePosition(_7ca,true,_7cc);
_7ca.style.display=_7d1;
for(var _7d6 in _7cd){
var pos,_7d8,_7d9;
var _7da=_7cd[_7d6];
_7d8=_7d5.x+(_7d6.charAt(1)=="L"?0:_7d3);
_7d9=_7d5.y+(_7d6.charAt(0)=="T"?0:_7d4);
pos=dojo.html.placeOnScreen(node,_7d8,_7d9,_7cb,true,_7da,true);
if(pos.dist==0){
best=pos;
break;
}else{
if(_7d0>pos.dist){
_7d0=pos.dist;
best=pos;
}
}
}
if(!_7ce){
node.style.left=best.left+"px";
node.style.top=best.top+"px";
}
return best;
};
dojo.html.scrollIntoView=function(node){
if(!node){
return;
}
if(dojo.render.html.ie){
if(dojo.html.getBorderBox(node.parentNode).height<node.parentNode.scrollHeight){
node.scrollIntoView(false);
}
}else{
if(dojo.render.html.mozilla){
node.scrollIntoView(false);
}else{
var _7dc=node.parentNode;
var _7dd=_7dc.scrollTop+dojo.html.getBorderBox(_7dc).height;
var _7de=node.offsetTop+dojo.html.getMarginBox(node).height;
if(_7dd<_7de){
_7dc.scrollTop+=(_7de-_7dd);
}else{
if(_7dc.scrollTop>node.offsetTop){
_7dc.scrollTop-=(_7dc.scrollTop-node.offsetTop);
}
}
}
}
};
dojo.provide("dojo.lfx.toggle");
dojo.lfx.toggle.plain={show:function(node,_7e0,_7e1,_7e2){
dojo.html.show(node);
if(dojo.lang.isFunction(_7e2)){
_7e2();
}
},hide:function(node,_7e4,_7e5,_7e6){
dojo.html.hide(node);
if(dojo.lang.isFunction(_7e6)){
_7e6();
}
}};
dojo.lfx.toggle.fade={show:function(node,_7e8,_7e9,_7ea){
dojo.lfx.fadeShow(node,_7e8,_7e9,_7ea).play();
},hide:function(node,_7ec,_7ed,_7ee){
dojo.lfx.fadeHide(node,_7ec,_7ed,_7ee).play();
}};
dojo.lfx.toggle.wipe={show:function(node,_7f0,_7f1,_7f2){
dojo.lfx.wipeIn(node,_7f0,_7f1,_7f2).play();
},hide:function(node,_7f4,_7f5,_7f6){
dojo.lfx.wipeOut(node,_7f4,_7f5,_7f6).play();
}};
dojo.lfx.toggle.explode={show:function(node,_7f8,_7f9,_7fa,_7fb){
dojo.lfx.explode(_7fb||{x:0,y:0,width:0,height:0},node,_7f8,_7f9,_7fa).play();
},hide:function(node,_7fd,_7fe,_7ff,_800){
dojo.lfx.implode(node,_800||{x:0,y:0,width:0,height:0},_7fd,_7fe,_7ff).play();
}};
dojo.provide("dojo.widget.HtmlWidget");
dojo.declare("dojo.widget.HtmlWidget",dojo.widget.DomWidget,{widgetType:"HtmlWidget",templateCssPath:null,templatePath:null,lang:"",toggle:"plain",toggleDuration:150,animationInProgress:false,initialize:function(args,frag){
},postMixInProperties:function(args,frag){
if(this.lang===""){
this.lang=null;
}
this.toggleObj=dojo.lfx.toggle[this.toggle.toLowerCase()]||dojo.lfx.toggle.plain;
},getContainerHeight:function(){
dojo.unimplemented("dojo.widget.HtmlWidget.getContainerHeight");
},getContainerWidth:function(){
return this.parent.domNode.offsetWidth;
},setNativeHeight:function(_805){
var ch=this.getContainerHeight();
},createNodesFromText:function(txt,wrap){
return dojo.html.createNodesFromText(txt,wrap);
},destroyRendering:function(_809){
try{
if(!_809&&this.domNode){
dojo.event.browser.clean(this.domNode);
}
this.domNode.parentNode.removeChild(this.domNode);
delete this.domNode;
}
catch(e){
}
},isShowing:function(){
return dojo.html.isShowing(this.domNode);
},toggleShowing:function(){
if(this.isHidden){
this.show();
}else{
this.hide();
}
},show:function(){
this.animationInProgress=true;
this.isHidden=false;
this.toggleObj.show(this.domNode,this.toggleDuration,null,dojo.lang.hitch(this,this.onShow),this.explodeSrc);
},onShow:function(){
this.animationInProgress=false;
this.checkSize();
},hide:function(){
this.animationInProgress=true;
this.isHidden=true;
this.toggleObj.hide(this.domNode,this.toggleDuration,null,dojo.lang.hitch(this,this.onHide),this.explodeSrc);
},onHide:function(){
this.animationInProgress=false;
},_isResized:function(w,h){
if(!this.isShowing()){
return false;
}
var wh=dojo.html.getMarginBox(this.domNode);
var _80d=w||wh.width;
var _80e=h||wh.height;
if(this.width==_80d&&this.height==_80e){
return false;
}
this.width=_80d;
this.height=_80e;
return true;
},checkSize:function(){
if(!this._isResized()){
return;
}
this.onResized();
},resizeTo:function(w,h){
dojo.html.setMarginBox(this.domNode,{width:w,height:h});
if(this.isShowing()){
this.onResized();
}
},resizeSoon:function(){
if(this.isShowing()){
dojo.lang.setTimeout(this,this.onResized,0);
}
},onResized:function(){
dojo.lang.forEach(this.children,function(_811){
if(_811.checkSize){
_811.checkSize();
}
});
}});
dojo.provide("dojo.widget.*");
dojo.provide("dojo.html.*");
dojo.provide("dojo.html.iframe");
dojo.html.iframeContentWindow=function(_812){
var win=dojo.html.getDocumentWindow(dojo.html.iframeContentDocument(_812))||dojo.html.iframeContentDocument(_812).__parent__||(_812.name&&document.frames[_812.name])||null;
return win;
};
dojo.html.iframeContentDocument=function(_814){
var doc=_814.contentDocument||((_814.contentWindow)&&(_814.contentWindow.document))||((_814.name)&&(document.frames[_814.name])&&(document.frames[_814.name].document))||null;
return doc;
};
dojo.html.BackgroundIframe=function(node){
if(dojo.render.html.ie55||dojo.render.html.ie60){
var html="<iframe src='javascript:false'"+"' style='position: absolute; left: 0px; top: 0px; width: 100%; height: 100%;"+"z-index: -1; filter:Alpha(Opacity=\"0\");' "+">";
this.iframe=dojo.doc().createElement(html);
this.iframe.tabIndex=-1;
if(node){
node.appendChild(this.iframe);
this.domNode=node;
}else{
dojo.body().appendChild(this.iframe);
this.iframe.style.display="none";
}
}
};
dojo.lang.extend(dojo.html.BackgroundIframe,{iframe:null,onResized:function(){
if(this.iframe&&this.domNode&&this.domNode.parentNode){
var _818=dojo.html.getMarginBox(this.domNode);
if(_818.width==0||_818.height==0){
dojo.lang.setTimeout(this,this.onResized,100);
return;
}
this.iframe.style.width=_818.width+"px";
this.iframe.style.height=_818.height+"px";
}
},size:function(node){
if(!this.iframe){
return;
}
var _81a=dojo.html.toCoordinateObject(node,true,dojo.html.boxSizing.BORDER_BOX);
this.iframe.style.width=_81a.width+"px";
this.iframe.style.height=_81a.height+"px";
this.iframe.style.left=_81a.left+"px";
this.iframe.style.top=_81a.top+"px";
},setZIndex:function(node){
if(!this.iframe){
return;
}
if(dojo.dom.isNode(node)){
this.iframe.style.zIndex=dojo.html.getStyle(node,"z-index")-1;
}else{
if(!isNaN(node)){
this.iframe.style.zIndex=node;
}
}
},show:function(){
if(!this.iframe){
return;
}
this.iframe.style.display="block";
},hide:function(){
if(!this.iframe){
return;
}
this.iframe.style.display="none";
},remove:function(){
dojo.html.removeNode(this.iframe);
}});
dojo.provide("dojo.widget.html.stabile");
dojo.widget.html.stabile={_sqQuotables:new RegExp("([\\\\'])","g"),_depth:0,_recur:false,depthLimit:2};
dojo.widget.html.stabile.getState=function(id){
dojo.widget.html.stabile.setup();
return dojo.widget.html.stabile.widgetState[id];
};
dojo.widget.html.stabile.setState=function(id,_81e,_81f){
dojo.widget.html.stabile.setup();
dojo.widget.html.stabile.widgetState[id]=_81e;
if(_81f){
dojo.widget.html.stabile.commit(dojo.widget.html.stabile.widgetState);
}
};
dojo.widget.html.stabile.setup=function(){
if(!dojo.widget.html.stabile.widgetState){
var text=dojo.widget.html.stabile.getStorage().value;
dojo.widget.html.stabile.widgetState=text?dj_eval("("+text+")"):{};
}
};
dojo.widget.html.stabile.commit=function(_821){
dojo.widget.html.stabile.getStorage().value=dojo.widget.html.stabile.description(_821);
};
dojo.widget.html.stabile.description=function(v,_823){
var _824=dojo.widget.html.stabile._depth;
var _825=function(){
return this.description(this,true);
};
try{
if(v===void (0)){
return "undefined";
}
if(v===null){
return "null";
}
if(typeof (v)=="boolean"||typeof (v)=="number"||v instanceof Boolean||v instanceof Number){
return v.toString();
}
if(typeof (v)=="string"||v instanceof String){
var v1=v.replace(dojo.widget.html.stabile._sqQuotables,"\\$1");
v1=v1.replace(/\n/g,"\\n");
v1=v1.replace(/\r/g,"\\r");
return "'"+v1+"'";
}
if(v instanceof Date){
return "new Date("+d.getFullYear+","+d.getMonth()+","+d.getDate()+")";
}
var d;
if(v instanceof Array||v.push){
if(_824>=dojo.widget.html.stabile.depthLimit){
return "[ ... ]";
}
d="[";
var _828=true;
dojo.widget.html.stabile._depth++;
for(var i=0;i<v.length;i++){
if(_828){
_828=false;
}else{
d+=",";
}
d+=arguments.callee(v[i],_823);
}
return d+"]";
}
if(v.constructor==Object||v.toString==_825){
if(_824>=dojo.widget.html.stabile.depthLimit){
return "{ ... }";
}
if(typeof (v.hasOwnProperty)!="function"&&v.prototype){
throw new Error("description: "+v+" not supported by script engine");
}
var _828=true;
d="{";
dojo.widget.html.stabile._depth++;
for(var key in v){
if(v[key]==void (0)||typeof (v[key])=="function"){
continue;
}
if(_828){
_828=false;
}else{
d+=", ";
}
var kd=key;
if(!kd.match(/^[a-zA-Z_][a-zA-Z0-9_]*$/)){
kd=arguments.callee(key,_823);
}
d+=kd+": "+arguments.callee(v[key],_823);
}
return d+"}";
}
if(_823){
if(dojo.widget.html.stabile._recur){
var _82c=Object.prototype.toString;
return _82c.apply(v,[]);
}else{
dojo.widget.html.stabile._recur=true;
return v.toString();
}
}else{
throw new Error("Unknown type: "+v);
return "'unknown'";
}
}
finally{
dojo.widget.html.stabile._depth=_824;
}
};
dojo.widget.html.stabile.getStorage=function(){
if(dojo.widget.html.stabile.dataField){
return dojo.widget.html.stabile.dataField;
}
var form=document.forms._dojo_form;
return dojo.widget.html.stabile.dataField=form?form.stabile:{value:""};
};
dojo.provide("dojo.html.selection");
dojo.html.selectionType={NONE:0,TEXT:1,CONTROL:2};
dojo.html.clearSelection=function(){
var _82e=dojo.global();
var _82f=dojo.doc();
try{
if(_82e["getSelection"]){
if(dojo.render.html.safari){
_82e.getSelection().collapse();
}else{
_82e.getSelection().removeAllRanges();
}
}else{
if(_82f.selection){
if(_82f.selection.empty){
_82f.selection.empty();
}else{
if(_82f.selection.clear){
_82f.selection.clear();
}
}
}
}
return true;
}
catch(e){
dojo.debug(e);
return false;
}
};
dojo.html.disableSelection=function(_830){
_830=dojo.byId(_830)||dojo.body();
var h=dojo.render.html;
if(h.mozilla){
_830.style.MozUserSelect="none";
}else{
if(h.safari){
_830.style.KhtmlUserSelect="none";
}else{
if(h.ie){
_830.unselectable="on";
}else{
return false;
}
}
}
return true;
};
dojo.html.enableSelection=function(_832){
_832=dojo.byId(_832)||dojo.body();
var h=dojo.render.html;
if(h.mozilla){
_832.style.MozUserSelect="";
}else{
if(h.safari){
_832.style.KhtmlUserSelect="";
}else{
if(h.ie){
_832.unselectable="off";
}else{
return false;
}
}
}
return true;
};
dojo.html.selectElement=function(_834){
dojo.deprecated("dojo.html.selectElement","replaced by dojo.html.selection.selectElementChildren",0.5);
};
dojo.html.selectInputText=function(_835){
var _836=dojo.global();
var _837=dojo.doc();
_835=dojo.byId(_835);
if(_837["selection"]&&dojo.body()["createTextRange"]){
var _838=_835.createTextRange();
_838.moveStart("character",0);
_838.moveEnd("character",_835.value.length);
_838.select();
}else{
if(_836["getSelection"]){
var _839=_836.getSelection();
_835.setSelectionRange(0,_835.value.length);
}
}
_835.focus();
};
dojo.html.isSelectionCollapsed=function(){
dojo.deprecated("dojo.html.isSelectionCollapsed","replaced by dojo.html.selection.isCollapsed",0.5);
return dojo.html.selection.isCollapsed();
};
dojo.lang.mixin(dojo.html.selection,{getType:function(){
if(dojo.doc()["selection"]){
return dojo.html.selectionType[dojo.doc().selection.type.toUpperCase()];
}else{
var _83a=dojo.html.selectionType.TEXT;
var oSel;
try{
oSel=dojo.global().getSelection();
}
catch(e){
}
if(oSel&&oSel.rangeCount==1){
var _83c=oSel.getRangeAt(0);
if(_83c.startContainer==_83c.endContainer&&(_83c.endOffset-_83c.startOffset)==1&&_83c.startContainer.nodeType!=dojo.dom.TEXT_NODE){
_83a=dojo.html.selectionType.CONTROL;
}
}
return _83a;
}
},isCollapsed:function(){
var _83d=dojo.global();
var _83e=dojo.doc();
if(_83e["selection"]){
return _83e.selection.createRange().text=="";
}else{
if(_83d["getSelection"]){
var _83f=_83d.getSelection();
if(dojo.lang.isString(_83f)){
return _83f=="";
}else{
return _83f.isCollapsed||_83f.toString()=="";
}
}
}
},getSelectedElement:function(){
if(dojo.html.selection.getType()==dojo.html.selectionType.CONTROL){
if(dojo.doc()["selection"]){
var _840=dojo.doc().selection.createRange();
if(_840&&_840.item){
return dojo.doc().selection.createRange().item(0);
}
}else{
var _841=dojo.global().getSelection();
return _841.anchorNode.childNodes[_841.anchorOffset];
}
}
},getParentElement:function(){
if(dojo.html.selection.getType()==dojo.html.selectionType.CONTROL){
var p=dojo.html.selection.getSelectedElement();
if(p){
return p.parentNode;
}
}else{
if(dojo.doc()["selection"]){
return dojo.doc().selection.createRange().parentElement();
}else{
var _843=dojo.global().getSelection();
if(_843){
var node=_843.anchorNode;
while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE){
node=node.parentNode;
}
return node;
}
}
}
},getSelectedText:function(){
if(dojo.doc()["selection"]){
if(dojo.html.selection.getType()==dojo.html.selectionType.CONTROL){
return null;
}
return dojo.doc().selection.createRange().text;
}else{
var _845=dojo.global().getSelection();
if(_845){
return _845.toString();
}
}
},getSelectedHtml:function(){
if(dojo.doc()["selection"]){
if(dojo.html.selection.getType()==dojo.html.selectionType.CONTROL){
return null;
}
return dojo.doc().selection.createRange().htmlText;
}else{
var _846=dojo.global().getSelection();
if(_846&&_846.rangeCount){
var frag=_846.getRangeAt(0).cloneContents();
var div=document.createElement("div");
div.appendChild(frag);
return div.innerHTML;
}
return null;
}
},hasAncestorElement:function(_849){
return (dojo.html.selection.getAncestorElement.apply(this,arguments)!=null);
},getAncestorElement:function(_84a){
var node=dojo.html.selection.getSelectedElement()||dojo.html.selection.getParentElement();
while(node){
if(dojo.html.selection.isTag(node,arguments).length>0){
return node;
}
node=node.parentNode;
}
return null;
},isTag:function(node,tags){
if(node&&node.tagName){
for(var i=0;i<tags.length;i++){
if(node.tagName.toLowerCase()==String(tags[i]).toLowerCase()){
return String(tags[i]).toLowerCase();
}
}
}
return "";
},selectElement:function(_84f){
var _850=dojo.global();
var _851=dojo.doc();
_84f=dojo.byId(_84f);
if(_851.selection&&dojo.body().createTextRange){
try{
var _852=dojo.body().createControlRange();
_852.addElement(_84f);
_852.select();
}
catch(e){
dojo.html.selection.selectElementChildren(_84f);
}
}else{
if(_850["getSelection"]){
var _853=_850.getSelection();
if(_853["removeAllRanges"]){
var _852=_851.createRange();
_852.selectNode(_84f);
_853.removeAllRanges();
_853.addRange(_852);
}
}
}
},selectElementChildren:function(_854){
var _855=dojo.global();
var _856=dojo.doc();
_854=dojo.byId(_854);
if(_856.selection&&dojo.body().createTextRange){
var _857=dojo.body().createTextRange();
_857.moveToElementText(_854);
_857.select();
}else{
if(_855["getSelection"]){
var _858=_855.getSelection();
if(_858["setBaseAndExtent"]){
_858.setBaseAndExtent(_854,0,_854,_854.innerText.length-1);
}else{
if(_858["selectAllChildren"]){
_858.selectAllChildren(_854);
}
}
}
}
},getBookmark:function(){
var _859;
var _85a=dojo.doc();
if(_85a["selection"]){
var _85b=_85a.selection.createRange();
_859=_85b.getBookmark();
}else{
var _85c;
try{
_85c=dojo.global().getSelection();
}
catch(e){
}
if(_85c){
var _85b=_85c.getRangeAt(0);
_859=_85b.cloneRange();
}else{
dojo.debug("No idea how to store the current selection for this browser!");
}
}
return _859;
},moveToBookmark:function(_85d){
var _85e=dojo.doc();
if(_85e["selection"]){
var _85f=_85e.selection.createRange();
_85f.moveToBookmark(_85d);
_85f.select();
}else{
var _860;
try{
_860=dojo.global().getSelection();
}
catch(e){
}
if(_860&&_860["removeAllRanges"]){
_860.removeAllRanges();
_860.addRange(_85d);
}else{
dojo.debug("No idea how to restore selection for this browser!");
}
}
},collapse:function(_861){
if(dojo.global()["getSelection"]){
var _862=dojo.global().getSelection();
if(_862.removeAllRanges){
if(_861){
_862.collapseToStart();
}else{
_862.collapseToEnd();
}
}else{
dojo.global().getSelection().collapse(_861);
}
}else{
if(dojo.doc().selection){
var _863=dojo.doc().selection.createRange();
_863.collapse(_861);
_863.select();
}
}
},remove:function(){
if(dojo.doc().selection){
var _864=dojo.doc().selection;
if(_864.type.toUpperCase()!="NONE"){
_864.clear();
}
return _864;
}else{
var _864=dojo.global().getSelection();
for(var i=0;i<_864.rangeCount;i++){
_864.getRangeAt(i).deleteContents();
}
return _864;
}
}});
dojo.provide("dojo.widget.PopupContainer");
dojo.declare("dojo.widget.PopupContainerBase",null,function(){
this.queueOnAnimationFinish=[];
},{isContainer:true,templateString:"<div dojoAttachPoint=\"containerNode\" style=\"display:none;position:absolute;\" class=\"dojoPopupContainer\" ></div>",isShowingNow:false,currentSubpopup:null,beginZIndex:1000,parentPopup:null,parent:null,popupIndex:0,aroundBox:dojo.html.boxSizing.BORDER_BOX,openedForWindow:null,processKey:function(evt){
return false;
},applyPopupBasicStyle:function(){
with(this.domNode.style){
display="none";
position="absolute";
}
},aboutToShow:function(){
},open:function(x,y,_869,_86a,_86b,_86c){
if(this.isShowingNow){
return;
}
this.aboutToShow();
if(this.animationInProgress){
this.queueOnAnimationFinish.push(this.open,arguments);
return;
}
this.parent=_869;
var _86d=false,node,_86f;
if(typeof x=="object"){
node=x;
_86f=_86a;
_86a=_869;
_869=y;
_86d=true;
}
dojo.body().appendChild(this.domNode);
_86a=_86a||_869["domNode"]||[];
var _870=null;
this.isTopLevel=true;
while(_869){
if(_869!==this&&(_869.setOpenedSubpopup!=undefined&&_869.applyPopupBasicStyle!=undefined)){
_870=_869;
this.isTopLevel=false;
_870.setOpenedSubpopup(this);
break;
}
_869=_869.parent;
}
this.parentPopup=_870;
this.popupIndex=_870?_870.popupIndex+1:1;
if(this.isTopLevel){
var _871=dojo.html.isNode(_86a)?_86a:null;
dojo.widget.PopupManager.opened(this,_871);
}
if(this.isTopLevel&&!dojo.withGlobal(this.openedForWindow||dojo.global(),dojo.html.selection.isCollapsed)){
this._bookmark=dojo.withGlobal(this.openedForWindow||dojo.global(),dojo.html.selection.getBookmark);
}else{
this._bookmark=null;
}
if(_86a instanceof Array){
_86a={left:_86a[0],top:_86a[1],width:0,height:0};
}
with(this.domNode.style){
display="";
zIndex=this.beginZIndex+this.popupIndex;
}
if(_86d){
this.move(node,_86c,_86f);
}else{
this.move(x,y,_86c,_86b);
}
this.domNode.style.display="none";
this.explodeSrc=_86a;
this.show();
this.isShowingNow=true;
},move:function(x,y,_874,_875){
var _876=(typeof x=="object");
if(_876){
var _877=_874;
var node=x;
_874=y;
if(!_877){
_877={"BL":"TL","TL":"BL"};
}
dojo.html.placeOnScreenAroundElement(this.domNode,node,_874,this.aroundBox,_877);
}else{
if(!_875){
_875="TL,TR,BL,BR";
}
dojo.html.placeOnScreen(this.domNode,x,y,_874,true,_875);
}
},close:function(_879){
if(_879){
this.domNode.style.display="none";
}
if(this.animationInProgress){
this.queueOnAnimationFinish.push(this.close,[]);
return;
}
this.closeSubpopup(_879);
this.hide();
if(this.bgIframe){
this.bgIframe.hide();
this.bgIframe.size({left:0,top:0,width:0,height:0});
}
if(this.isTopLevel){
dojo.widget.PopupManager.closed(this);
}
this.isShowingNow=false;
try{
this.parent.domNode.focus();
}
catch(e){
}
if(this._bookmark&&dojo.withGlobal(this.openedForWindow||dojo.global(),dojo.html.selection.isCollapsed)){
if(this.openedForWindow){
this.openedForWindow.focus();
}
dojo.withGlobal(this.openedForWindow||dojo.global(),"moveToBookmark",dojo.html.selection,[this._bookmark]);
}
this._bookmark=null;
},closeAll:function(_87a){
if(this.parentPopup){
this.parentPopup.closeAll(_87a);
}else{
this.close(_87a);
}
},setOpenedSubpopup:function(_87b){
this.currentSubpopup=_87b;
},closeSubpopup:function(_87c){
if(this.currentSubpopup==null){
return;
}
this.currentSubpopup.close(_87c);
this.currentSubpopup=null;
},onShow:function(){
dojo.widget.PopupContainer.superclass.onShow.apply(this,arguments);
this.openedSize={w:this.domNode.style.width,h:this.domNode.style.height};
if(dojo.render.html.ie){
if(!this.bgIframe){
this.bgIframe=new dojo.html.BackgroundIframe();
this.bgIframe.setZIndex(this.domNode);
}
this.bgIframe.size(this.domNode);
this.bgIframe.show();
}
this.processQueue();
},processQueue:function(){
if(!this.queueOnAnimationFinish.length){
return;
}
var func=this.queueOnAnimationFinish.shift();
var args=this.queueOnAnimationFinish.shift();
func.apply(this,args);
},onHide:function(){
dojo.widget.HtmlWidget.prototype.onHide.call(this);
if(this.openedSize){
with(this.domNode.style){
width=this.openedSize.w;
height=this.openedSize.h;
}
}
this.processQueue();
}});
dojo.widget.defineWidget("dojo.widget.PopupContainer",[dojo.widget.HtmlWidget,dojo.widget.PopupContainerBase],{});
dojo.widget.PopupManager=new function(){
this.currentMenu=null;
this.currentButton=null;
this.currentFocusMenu=null;
this.focusNode=null;
this.registeredWindows=[];
this.registerWin=function(win){
if(!win.__PopupManagerRegistered){
dojo.event.connect(win.document,"onmousedown",this,"onClick");
dojo.event.connect(win,"onscroll",this,"onClick");
dojo.event.connect(win.document,"onkey",this,"onKey");
win.__PopupManagerRegistered=true;
this.registeredWindows.push(win);
}
};
this.registerAllWindows=function(_880){
if(!_880){
_880=dojo.html.getDocumentWindow(window.top&&window.top.document||window.document);
}
this.registerWin(_880);
for(var i=0;i<_880.frames.length;i++){
try{
var win=dojo.html.getDocumentWindow(_880.frames[i].document);
if(win){
this.registerAllWindows(win);
}
}
catch(e){
}
}
};
this.unRegisterWin=function(win){
if(win.__PopupManagerRegistered){
dojo.event.disconnect(win.document,"onmousedown",this,"onClick");
dojo.event.disconnect(win,"onscroll",this,"onClick");
dojo.event.disconnect(win.document,"onkey",this,"onKey");
win.__PopupManagerRegistered=false;
}
};
this.unRegisterAllWindows=function(){
for(var i=0;i<this.registeredWindows.length;++i){
this.unRegisterWin(this.registeredWindows[i]);
}
this.registeredWindows=[];
};
dojo.addOnLoad(this,"registerAllWindows");
dojo.addOnUnload(this,"unRegisterAllWindows");
this.closed=function(menu){
if(this.currentMenu==menu){
this.currentMenu=null;
this.currentButton=null;
this.currentFocusMenu=null;
}
};
this.opened=function(menu,_887){
if(menu==this.currentMenu){
return;
}
if(this.currentMenu){
this.currentMenu.close();
}
this.currentMenu=menu;
this.currentFocusMenu=menu;
this.currentButton=_887;
};
this.setFocusedMenu=function(menu){
this.currentFocusMenu=menu;
};
this.onKey=function(e){
if(!e.key){
return;
}
if(!this.currentMenu||!this.currentMenu.isShowingNow){
return;
}
var m=this.currentFocusMenu;
while(m){
if(m.processKey(e)){
e.preventDefault();
e.stopPropagation();
break;
}
m=m.parentPopup;
}
},this.onClick=function(e){
if(!this.currentMenu){
return;
}
var _88c=dojo.html.getScroll().offset;
var m=this.currentMenu;
while(m){
if(dojo.html.overElement(m.domNode,e)||dojo.html.isDescendantOf(e.target,m.domNode)){
return;
}
m=m.currentSubpopup;
}
if(this.currentButton&&dojo.html.overElement(this.currentButton,e)){
return;
}
this.currentMenu.close();
};
};
dojo.provide("dojo.widget.ComboBox");
dojo.widget.incrementalComboBoxDataProvider=function(url,_88f,_890){
this.searchUrl=url;
this.inFlight=false;
this.activeRequest=null;
this.allowCache=false;
this.cache={};
this.init=function(cbox){
this.searchUrl=cbox.dataUrl;
};
this.addToCache=function(_892,data){
if(this.allowCache){
this.cache[_892]=data;
}
};
this.startSearch=function(_894,type,_896){
if(this.inFlight){
}
var tss=encodeURIComponent(_894);
var _898=dojo.string.substituteParams(this.searchUrl,{"searchString":tss});
var _899=this;
var _89a=dojo.io.bind({url:_898,method:"get",mimetype:"text/json",load:function(type,data,evt){
_899.inFlight=false;
if(!dojo.lang.isArray(data)){
var _89e=[];
for(var key in data){
_89e.push([data[key],key]);
}
data=_89e;
}
_899.addToCache(_894,data);
_899.provideSearchResults(data);
}});
this.inFlight=true;
};
};
dojo.widget.ComboBoxDataProvider=function(_8a0,_8a1,_8a2){
this.data=[];
this.searchTimeout=_8a2||500;
this.searchLimit=_8a1||30;
this.searchType="STARTSTRING";
this.caseSensitive=false;
this._lastSearch="";
this._lastSearchResults=null;
this.init=function(cbox,node){
if(!dojo.string.isBlank(cbox.dataUrl)){
this.getData(cbox.dataUrl);
}else{
if((node)&&(node.nodeName.toLowerCase()=="select")){
var opts=node.getElementsByTagName("option");
var ol=opts.length;
var data=[];
for(var x=0;x<ol;x++){
var text=opts[x].textContent||opts[x].innerText||opts[x].innerHTML;
var _8aa=[String(text),String(opts[x].value)];
data.push(_8aa);
if(opts[x].selected){
cbox.setAllValues(_8aa[0],_8aa[1]);
}
}
this.setData(data);
}
}
};
this.getData=function(url){
dojo.io.bind({url:url,load:dojo.lang.hitch(this,function(type,data,evt){
if(!dojo.lang.isArray(data)){
var _8af=[];
for(var key in data){
_8af.push([data[key],key]);
}
data=_8af;
}
this.setData(data);
}),mimetype:"text/json"});
};
this.startSearch=function(_8b1,type,_8b3){
this._preformSearch(_8b1,type,_8b3);
};
this._preformSearch=function(_8b4,type,_8b6){
var st=type||this.searchType;
var ret=[];
if(!this.caseSensitive){
_8b4=_8b4.toLowerCase();
}
for(var x=0;x<this.data.length;x++){
if((!_8b6)&&(ret.length>=this.searchLimit)){
break;
}
var _8ba=new String((!this.caseSensitive)?this.data[x][0].toLowerCase():this.data[x][0]);
if(_8ba.length<_8b4.length){
continue;
}
if(st=="STARTSTRING"){
if(_8b4==_8ba.substr(0,_8b4.length)){
ret.push(this.data[x]);
}
}else{
if(st=="SUBSTRING"){
if(_8ba.indexOf(_8b4)>=0){
ret.push(this.data[x]);
}
}else{
if(st=="STARTWORD"){
var idx=_8ba.indexOf(_8b4);
if(idx==0){
ret.push(this.data[x]);
}
if(idx<=0){
continue;
}
var _8bc=false;
while(idx!=-1){
if(" ,/(".indexOf(_8ba.charAt(idx-1))!=-1){
_8bc=true;
break;
}
idx=_8ba.indexOf(_8b4,idx+1);
}
if(!_8bc){
continue;
}else{
ret.push(this.data[x]);
}
}
}
}
}
this.provideSearchResults(ret);
};
this.provideSearchResults=function(_8bd){
};
this.addData=function(_8be){
this.data=this.data.concat(_8be);
};
this.setData=function(_8bf){
this.data=_8bf;
};
if(_8a0){
this.setData(_8a0);
}
};
dojo.widget.defineWidget("dojo.widget.ComboBox",dojo.widget.HtmlWidget,{isContainer:false,forceValidOption:false,searchType:"stringstart",dataProvider:null,startSearch:function(_8c0){
},selectNextResult:function(){
},selectPrevResult:function(){
},setSelectedResult:function(){
},autoComplete:true,name:"",textInputNode:null,comboBoxValue:null,comboBoxSelectionValue:null,optionsListWrapper:null,optionsListNode:null,downArrowNode:null,searchTimer:null,searchDelay:100,dataUrl:"",fadeTime:200,disabled:false,maxListLength:8,mode:"local",selectedResult:null,_highlighted_option:null,_prev_key_backspace:false,_prev_key_esc:false,_gotFocus:false,_mouseover_list:false,dataProviderClass:"dojo.widget.ComboBoxDataProvider",buttonSrc:dojo.uri.dojoUri("src/widget/templates/images/combo_box_arrow.png"),dropdownToggle:"fade",templatePath:dojo.uri.dojoUri("src/widget/templates/ComboBox.html"),templateCssPath:dojo.uri.dojoUri("src/widget/templates/ComboBox.css"),setValue:function(_8c1){
this.comboBoxValue.value=_8c1;
if(this.textInputNode.value!=_8c1){
this.textInputNode.value=_8c1;
dojo.widget.html.stabile.setState(this.widgetId,this.getState(),true);
this.onValueChanged(_8c1);
}
},onValueChanged:function(){
},getValue:function(){
return this.comboBoxValue.value;
},getState:function(){
return {value:this.getValue()};
},setState:function(_8c2){
this.setValue(_8c2.value);
},enable:function(){
this.disabled=false;
this.isEnabled=true;
this.textInputNode.removeAttribute("disabled");
},disable:function(){
this.disabled=true;
this.isEnabled=false;
this.textInputNode.setAttribute("disabled",true);
},getCaretPos:function(_8c3){
if(dojo.lang.isNumber(_8c3.selectionStart)){
return _8c3.selectionStart;
}else{
if(dojo.render.html.ie){
var tr=document.selection.createRange().duplicate();
var ntr=_8c3.createTextRange();
tr.move("character",0);
ntr.move("character",0);
try{
ntr.setEndPoint("EndToEnd",tr);
return String(ntr.text).replace(/\r/g,"").length;
}
catch(e){
return 0;
}
}
}
},setCaretPos:function(_8c6,_8c7){
_8c7=parseInt(_8c7);
this.setSelectedRange(_8c6,_8c7,_8c7);
},setSelectedRange:function(_8c8,_8c9,end){
if(!end){
end=_8c8.value.length;
}
if(_8c8.setSelectionRange){
_8c8.focus();
_8c8.setSelectionRange(_8c9,end);
}else{
if(_8c8.createTextRange){
var _8cb=_8c8.createTextRange();
with(_8cb){
collapse(true);
moveEnd("character",end);
moveStart("character",_8c9);
select();
}
}else{
_8c8.value=_8c8.value;
_8c8.blur();
_8c8.focus();
var dist=parseInt(_8c8.value.length)-end;
var _8cd=String.fromCharCode(37);
var tcc=_8cd.charCodeAt(0);
for(var x=0;x<dist;x++){
var te=document.createEvent("KeyEvents");
te.initKeyEvent("keypress",true,true,null,false,false,false,false,tcc,tcc);
_8c8.dispatchEvent(te);
}
}
}
},_handleKeyEvents:function(evt){
if(evt.ctrlKey||evt.altKey||!evt.key){
return;
}
this._prev_key_backspace=false;
this._prev_key_esc=false;
var k=dojo.event.browser.keys;
var _8d3=true;
switch(evt.key){
case k.KEY_DOWN_ARROW:
if(!this.popupWidget.isShowingNow){
this.startSearchFromInput();
}
this.highlightNextOption();
dojo.event.browser.stopEvent(evt);
return;
case k.KEY_UP_ARROW:
this.highlightPrevOption();
dojo.event.browser.stopEvent(evt);
return;
case k.KEY_TAB:
if(!this.autoComplete&&this.popupWidget.isShowingNow&&this._highlighted_option){
dojo.event.browser.stopEvent(evt);
this.selectOption({"target":this._highlighted_option,"noHide":false});
this.setSelectedRange(this.textInputNode,this.textInputNode.value.length,null);
}else{
this.selectOption();
return;
}
break;
case k.KEY_ENTER:
if(this.popupWidget.isShowingNow){
dojo.event.browser.stopEvent(evt);
}
if(this.autoComplete){
this.selectOption();
return;
}
case " ":
if(this.popupWidget.isShowingNow&&this._highlighted_option){
dojo.event.browser.stopEvent(evt);
this.selectOption();
this.hideResultList();
return;
}
break;
case k.KEY_ESCAPE:
this.hideResultList();
this._prev_key_esc=true;
return;
case k.KEY_BACKSPACE:
this._prev_key_backspace=true;
if(!this.textInputNode.value.length){
this.setAllValues("","");
this.hideResultList();
_8d3=false;
}
break;
case k.KEY_RIGHT_ARROW:
case k.KEY_LEFT_ARROW:
_8d3=false;
break;
default:
if(evt.charCode==0){
_8d3=false;
}
}
if(this.searchTimer){
clearTimeout(this.searchTimer);
}
if(_8d3){
this.blurOptionNode();
this.searchTimer=setTimeout(dojo.lang.hitch(this,this.startSearchFromInput),this.searchDelay);
}
},compositionEnd:function(evt){
evt.key=evt.keyCode;
this._handleKeyEvents(evt);
},onKeyUp:function(evt){
this.setValue(this.textInputNode.value);
},setSelectedValue:function(_8d6){
this.comboBoxSelectionValue.value=_8d6;
},setAllValues:function(_8d7,_8d8){
this.setSelectedValue(_8d8);
this.setValue(_8d7);
},focusOptionNode:function(node){
if(this._highlighted_option!=node){
this.blurOptionNode();
this._highlighted_option=node;
dojo.html.addClass(this._highlighted_option,"dojoComboBoxItemHighlight");
}
},blurOptionNode:function(){
if(this._highlighted_option){
dojo.html.removeClass(this._highlighted_option,"dojoComboBoxItemHighlight");
this._highlighted_option=null;
}
},highlightNextOption:function(){
if((!this._highlighted_option)||!this._highlighted_option.parentNode){
this.focusOptionNode(this.optionsListNode.firstChild);
}else{
if(this._highlighted_option.nextSibling){
this.focusOptionNode(this._highlighted_option.nextSibling);
}
}
dojo.html.scrollIntoView(this._highlighted_option);
},highlightPrevOption:function(){
if(this._highlighted_option&&this._highlighted_option.previousSibling){
this.focusOptionNode(this._highlighted_option.previousSibling);
}else{
this._highlighted_option=null;
this.hideResultList();
return;
}
dojo.html.scrollIntoView(this._highlighted_option);
},itemMouseOver:function(evt){
if(evt.target===this.optionsListNode){
return;
}
this.focusOptionNode(evt.target);
dojo.html.addClass(this._highlighted_option,"dojoComboBoxItemHighlight");
},itemMouseOut:function(evt){
if(evt.target===this.optionsListNode){
return;
}
this.blurOptionNode();
},onResize:function(){
var _8dc=dojo.html.getContentBox(this.textInputNode);
if(_8dc.height==0){
dojo.lang.setTimeout(this,"onResize",50);
return;
}
var _8dd={width:_8dc.height,height:_8dc.height};
dojo.html.setContentBox(this.downArrowNode,_8dd);
},fillInTemplate:function(args,frag){
dojo.html.applyBrowserClass(this.domNode);
var _8e0=this.getFragNodeRef(frag);
if(!this.name&&_8e0.name){
this.name=_8e0.name;
}
this.comboBoxValue.name=this.name;
this.comboBoxSelectionValue.name=this.name+"_selected";
dojo.html.copyStyle(this.domNode,_8e0);
dojo.html.copyStyle(this.textInputNode,_8e0);
dojo.html.copyStyle(this.downArrowNode,_8e0);
with(this.downArrowNode.style){
width="0px";
height="0px";
}
var _8e1;
if(this.mode=="remote"){
_8e1=dojo.widget.incrementalComboBoxDataProvider;
}else{
if(typeof this.dataProviderClass=="string"){
_8e1=dojo.evalObjPath(this.dataProviderClass);
}else{
_8e1=this.dataProviderClass;
}
}
this.dataProvider=new _8e1();
this.dataProvider.init(this,this.getFragNodeRef(frag));
this.popupWidget=new dojo.widget.createWidget("PopupContainer",{toggle:this.dropdownToggle,toggleDuration:this.toggleDuration});
dojo.event.connect(this,"destroy",this.popupWidget,"destroy");
this.optionsListNode=this.popupWidget.domNode;
this.domNode.appendChild(this.optionsListNode);
dojo.html.addClass(this.optionsListNode,"dojoComboBoxOptions");
dojo.event.connect(this.optionsListNode,"onclick",this,"selectOption");
dojo.event.connect(this.optionsListNode,"onmouseover",this,"_onMouseOver");
dojo.event.connect(this.optionsListNode,"onmouseout",this,"_onMouseOut");
dojo.event.connect(this.optionsListNode,"onmouseover",this,"itemMouseOver");
dojo.event.connect(this.optionsListNode,"onmouseout",this,"itemMouseOut");
},focus:function(){
this.tryFocus();
},openResultList:function(_8e2){
if(!this.isEnabled){
return;
}
this.clearResultList();
if(!_8e2.length){
this.hideResultList();
}
if((this.autoComplete)&&(_8e2.length)&&(!this._prev_key_backspace)&&(this.textInputNode.value.length>0)){
var cpos=this.getCaretPos(this.textInputNode);
if((cpos+1)>this.textInputNode.value.length){
this.textInputNode.value+=_8e2[0][0].substr(cpos);
this.setSelectedRange(this.textInputNode,cpos,this.textInputNode.value.length);
}
}
var even=true;
while(_8e2.length){
var tr=_8e2.shift();
if(tr){
var td=document.createElement("div");
td.appendChild(document.createTextNode(tr[0]));
td.setAttribute("resultName",tr[0]);
td.setAttribute("resultValue",tr[1]);
td.className="dojoComboBoxItem "+((even)?"dojoComboBoxItemEven":"dojoComboBoxItemOdd");
even=(!even);
this.optionsListNode.appendChild(td);
}
}
this.showResultList();
},onFocusInput:function(){
this._hasFocus=true;
},onBlurInput:function(){
this._hasFocus=false;
this._handleBlurTimer(true,500);
},_handleBlurTimer:function(_8e7,_8e8){
if(this.blurTimer&&(_8e7||_8e8)){
clearTimeout(this.blurTimer);
}
if(_8e8){
this.blurTimer=dojo.lang.setTimeout(this,"checkBlurred",_8e8);
}
},_onMouseOver:function(evt){
if(!this._mouseover_list){
this._handleBlurTimer(true,0);
this._mouseover_list=true;
}
},_onMouseOut:function(evt){
var _8eb=evt.relatedTarget;
if(!_8eb||_8eb.parentNode!=this.optionsListNode){
this._mouseover_list=false;
this._handleBlurTimer(true,100);
this.tryFocus();
}
},_isInputEqualToResult:function(_8ec){
var _8ed=this.textInputNode.value;
if(!this.dataProvider.caseSensitive){
_8ed=_8ed.toLowerCase();
_8ec=_8ec.toLowerCase();
}
return (_8ed==_8ec);
},_isValidOption:function(){
var tgt=dojo.html.firstElement(this.optionsListNode);
var _8ef=false;
while(!_8ef&&tgt){
if(this._isInputEqualToResult(tgt.getAttribute("resultName"))){
_8ef=true;
}else{
tgt=dojo.html.nextElement(tgt);
}
}
return _8ef;
},checkBlurred:function(){
if(!this._hasFocus&&!this._mouseover_list){
this.hideResultList();
if(!this.textInputNode.value.length){
this.setAllValues("","");
return;
}
var _8f0=this._isValidOption();
if(this.forceValidOption&&!_8f0){
this.setAllValues("","");
return;
}
if(!_8f0){
this.setSelectedValue("");
}
}
},sizeBackgroundIframe:function(){
var mb=dojo.html.getMarginBox(this.optionsListNode);
if(mb.width==0||mb.height==0){
dojo.lang.setTimeout(this,"sizeBackgroundIframe",100);
return;
}
},selectOption:function(evt){
var tgt=null;
if(!evt){
evt={target:this._highlighted_option};
}
if(!dojo.html.isDescendantOf(evt.target,this.optionsListNode)){
if(!this.textInputNode.value.length){
return;
}
tgt=dojo.html.firstElement(this.optionsListNode);
if(!tgt||!this._isInputEqualToResult(tgt.getAttribute("resultName"))){
return;
}
}else{
tgt=evt.target;
}
while((tgt.nodeType!=1)||(!tgt.getAttribute("resultName"))){
tgt=tgt.parentNode;
if(tgt===dojo.body()){
return false;
}
}
this.selectedResult=[tgt.getAttribute("resultName"),tgt.getAttribute("resultValue")];
this.setAllValues(tgt.getAttribute("resultName"),tgt.getAttribute("resultValue"));
if(!evt.noHide){
this.hideResultList();
this.setSelectedRange(this.textInputNode,0,null);
}
this.tryFocus();
},clearResultList:function(){
if(this.optionsListNode.innerHTML){
this.optionsListNode.innerHTML="";
}
},hideResultList:function(){
this.popupWidget.close();
},showResultList:function(){
var _8f4=this.optionsListNode.childNodes;
if(_8f4.length){
var _8f5=this.maxListLength;
if(_8f4.length<_8f5){
_8f5=_8f4.length;
}
with(this.optionsListNode.style){
display="";
if(_8f5==_8f4.length){
height="";
}else{
height=_8f5*dojo.html.getMarginBox(_8f4[0]).height+"px";
}
width=(dojo.html.getMarginBox(this.domNode).width-2)+"px";
}
this.popupWidget.open(this.domNode,this,this.downArrowNode);
}else{
this.hideResultList();
}
},handleArrowClick:function(){
this._handleBlurTimer(true,0);
this.tryFocus();
if(this.popupWidget.isShowingNow){
this.hideResultList();
}else{
this.startSearch("");
}
},tryFocus:function(){
try{
this.textInputNode.focus();
}
catch(e){
}
},startSearchFromInput:function(){
this.startSearch(this.textInputNode.value);
},postCreate:function(){
this.onResize();
dojo.event.connect(this,"startSearch",this.dataProvider,"startSearch");
dojo.event.connect(this.dataProvider,"provideSearchResults",this,"openResultList");
dojo.event.connect(this.textInputNode,"onblur",this,"onBlurInput");
dojo.event.connect(this.textInputNode,"onfocus",this,"onFocusInput");
if(this.disabled){
this.disable();
}
var s=dojo.widget.html.stabile.getState(this.widgetId);
if(s){
this.setState(s);
}
}});
dojo.provide("dojo.widget.PageContainer");
dojo.widget.defineWidget("dojo.widget.PageContainer",dojo.widget.HtmlWidget,{isContainer:true,doLayout:true,templateString:"<div dojoAttachPoint='containerNode'></div>",selectedChild:"",fillInTemplate:function(args,frag){
var _8f9=this.getFragNodeRef(frag);
dojo.html.copyStyle(this.domNode,_8f9);
dojo.widget.PageContainer.superclass.fillInTemplate.apply(this,arguments);
},postCreate:function(args,frag){
if(this.children.length){
dojo.lang.forEach(this.children,this._setupChild,this);
var _8fc;
if(this.selectedChild){
this.selectChild(this.selectedChild);
}else{
for(var i=0;i<this.children.length;i++){
if(this.children[i].selected){
this.selectChild(this.children[i]);
break;
}
}
if(!this.selectedChildWidget){
this.selectChild(this.children[0]);
}
}
}
},addChild:function(_8fe){
dojo.widget.PageContainer.superclass.addChild.apply(this,arguments);
this._setupChild(_8fe);
this.onResized();
if(!this.selectedChildWidget){
this.selectChild(_8fe);
}
},_setupChild:function(page){
page.hide();
dojo.event.topic.publish(this.widgetId+"-addChild",page);
},removeChild:function(page){
dojo.widget.PageContainer.superclass.removeChild.apply(this,arguments);
if(this._beingDestroyed){
return;
}
dojo.event.topic.publish(this.widgetId+"-removeChild",page);
if(this.selectedChildWidget===page){
this.selectedChildWidget=undefined;
if(this.children.length>0){
this.selectChild(this.children[0],true);
}
}
},selectChild:function(page,_902){
page=dojo.widget.byId(page);
this.correspondingPageButton=_902;
if(this.selectedChildWidget){
this._hideChild(this.selectedChildWidget);
}
this.selectedChildWidget=page;
this._showChild(page);
page.isFirstChild=(page==this.children[0]);
page.isLastChild=(page==this.children[this.children.length-1]);
dojo.event.topic.publish(this.widgetId+"-selectChild",page);
},forward:function(){
var _903=dojo.lang.find(this.children,this.selectedChildWidget);
this.selectChild(this.children[_903+1]);
},back:function(){
var _904=dojo.lang.find(this.children,this.selectedChildWidget);
this.selectChild(this.children[_904-1]);
},onResized:function(){
if(this.doLayout&&this.selectedChildWidget){
with(this.selectedChildWidget.domNode.style){
top=dojo.html.getPixelValue(this.containerNode,"padding-top",true);
left=dojo.html.getPixelValue(this.containerNode,"padding-left",true);
}
var _905=dojo.html.getContentBox(this.containerNode);
this.selectedChildWidget.resizeTo(_905.width,_905.height);
}
},_showChild:function(page){
if(this.doLayout){
var _907=dojo.html.getContentBox(this.containerNode);
page.resizeTo(_907.width,_907.height);
}
page.selected=true;
page.show();
},_hideChild:function(page){
page.selected=false;
page.hide();
},closeChild:function(page){
var _90a=page.onClose(this,page);
if(_90a){
this.removeChild(page);
page.destroy();
}
},destroy:function(){
this._beingDestroyed=true;
dojo.event.topic.destroy(this.widgetId+"-addChild");
dojo.event.topic.destroy(this.widgetId+"-removeChild");
dojo.event.topic.destroy(this.widgetId+"-selectChild");
dojo.widget.PageContainer.superclass.destroy.apply(this,arguments);
}});
dojo.widget.defineWidget("dojo.widget.PageController",dojo.widget.HtmlWidget,{templateString:"<span wairole='tablist' dojoAttachEvent='onKey'></span>",isContainer:true,containerId:"",buttonWidget:"PageButton","class":"dojoPageController",fillInTemplate:function(){
dojo.html.addClass(this.domNode,this["class"]);
dojo.widget.wai.setAttr(this.domNode,"waiRole","role","tablist");
},postCreate:function(){
this.pane2button={};
var _90b=dojo.widget.byId(this.containerId);
if(_90b){
dojo.lang.forEach(_90b.children,this.onAddChild,this);
}
dojo.event.topic.subscribe(this.containerId+"-addChild",this,"onAddChild");
dojo.event.topic.subscribe(this.containerId+"-removeChild",this,"onRemoveChild");
dojo.event.topic.subscribe(this.containerId+"-selectChild",this,"onSelectChild");
},destroy:function(){
dojo.event.topic.unsubscribe(this.containerId+"-addChild",this,"onAddChild");
dojo.event.topic.unsubscribe(this.containerId+"-removeChild",this,"onRemoveChild");
dojo.event.topic.unsubscribe(this.containerId+"-selectChild",this,"onSelectChild");
dojo.widget.PageController.superclass.destroy.apply(this,arguments);
},onAddChild:function(page){
var _90d=dojo.widget.createWidget(this.buttonWidget,{label:page.label,closeButton:page.closable});
this.addChild(_90d);
this.domNode.appendChild(_90d.domNode);
this.pane2button[page]=_90d;
page.controlButton=_90d;
var _90e=this;
dojo.event.connect(_90d,"onClick",function(){
_90e.onButtonClick(page);
});
dojo.event.connect(_90d,"onCloseButtonClick",function(){
_90e.onCloseButtonClick(page);
});
},onRemoveChild:function(page){
if(this._currentChild==page){
this._currentChild=null;
}
var _910=this.pane2button[page];
if(_910){
_910.destroy();
}
this.pane2button[page]=null;
},onSelectChild:function(page){
if(this._currentChild){
var _912=this.pane2button[this._currentChild];
_912.clearSelected();
}
var _913=this.pane2button[page];
_913.setSelected();
this._currentChild=page;
},onButtonClick:function(page){
var _915=dojo.widget.byId(this.containerId);
_915.selectChild(page,false,this);
},onCloseButtonClick:function(page){
var _917=dojo.widget.byId(this.containerId);
_917.closeChild(page);
},onKey:function(evt){
if((evt.keyCode==evt.KEY_RIGHT_ARROW)||(evt.keyCode==evt.KEY_LEFT_ARROW)){
var _919=0;
var next=null;
var _919=dojo.lang.find(this.children,this.pane2button[this._currentChild]);
if(evt.keyCode==evt.KEY_RIGHT_ARROW){
next=this.children[(_919+1)%this.children.length];
}else{
next=this.children[(_919+(this.children.length-1))%this.children.length];
}
dojo.event.browser.stopEvent(evt);
next.onClick();
}
}});
dojo.widget.defineWidget("dojo.widget.PageButton",dojo.widget.HtmlWidget,{templateString:"<span class='item'>"+"<span dojoAttachEvent='onClick' dojoAttachPoint='titleNode' class='selectButton'>${this.label}</span>"+"<span dojoAttachEvent='onClick:onCloseButtonClick' class='closeButton'>[X]</span>"+"</span>",label:"foo",closeButton:false,onClick:function(){
this.focus();
},onCloseButtonMouseOver:function(){
dojo.html.addClass(this.closeButtonNode,"closeHover");
},onCloseButtonMouseOut:function(){
dojo.html.removeClass(this.closeButtonNode,"closeHover");
},onCloseButtonClick:function(evt){
},setSelected:function(){
dojo.html.addClass(this.domNode,"current");
this.titleNode.setAttribute("tabIndex","0");
},clearSelected:function(){
dojo.html.removeClass(this.domNode,"current");
this.titleNode.setAttribute("tabIndex","-1");
},focus:function(){
if(this.titleNode.focus){
this.titleNode.focus();
}
}});
dojo.lang.extend(dojo.widget.Widget,{label:"",selected:false,closable:false,onClose:function(){
return true;
}});
dojo.provide("dojo.widget.html.layout");
dojo.widget.html.layout=function(_91c,_91d,_91e){
dojo.html.addClass(_91c,"dojoLayoutContainer");
_91d=dojo.lang.filter(_91d,function(_91f,idx){
_91f.idx=idx;
return dojo.lang.inArray(["top","bottom","left","right","client","flood"],_91f.layoutAlign);
});
if(_91e&&_91e!="none"){
var rank=function(_922){
switch(_922.layoutAlign){
case "flood":
return 1;
case "left":
case "right":
return (_91e=="left-right")?2:3;
case "top":
case "bottom":
return (_91e=="left-right")?3:2;
default:
return 4;
}
};
_91d.sort(function(a,b){
return (rank(a)-rank(b))||(a.idx-b.idx);
});
}
var f={top:dojo.html.getPixelValue(_91c,"padding-top",true),left:dojo.html.getPixelValue(_91c,"padding-left",true)};
dojo.lang.mixin(f,dojo.html.getContentBox(_91c));
dojo.lang.forEach(_91d,function(_926){
var elm=_926.domNode;
var pos=_926.layoutAlign;
with(elm.style){
left=f.left+"px";
top=f.top+"px";
bottom="auto";
right="auto";
}
dojo.html.addClass(elm,"dojoAlign"+dojo.string.capitalize(pos));
if((pos=="top")||(pos=="bottom")){
dojo.html.setMarginBox(elm,{width:f.width});
var h=dojo.html.getMarginBox(elm).height;
f.height-=h;
if(pos=="top"){
f.top+=h;
}else{
elm.style.top=f.top+f.height+"px";
}
}else{
if(pos=="left"||pos=="right"){
var w=dojo.html.getMarginBox(elm).width;
dojo.html.setMarginBox(elm,{width:w,height:f.height});
f.width-=w;
if(pos=="left"){
f.left+=w;
}else{
elm.style.left=f.left+f.width+"px";
}
}else{
if(pos=="flood"||pos=="client"){
dojo.html.setMarginBox(elm,{width:f.width,height:f.height});
}
}
}
if(_926.onResized){
_926.onResized();
}
});
};
dojo.html.insertCssText(".dojoLayoutContainer{ position: relative; display: block; }\n"+"body .dojoAlignTop, body .dojoAlignBottom, body .dojoAlignLeft, body .dojoAlignRight { position: absolute; overflow: hidden; }\n"+"body .dojoAlignClient { position: absolute }\n"+".dojoAlignClient { overflow: auto; }\n");
dojo.provide("dojo.widget.TabContainer");
dojo.widget.defineWidget("dojo.widget.TabContainer",dojo.widget.PageContainer,{labelPosition:"top",closeButton:"none",templateString:null,templatePath:dojo.uri.dojoUri("src/widget/templates/TabContainer.html"),templateCssPath:dojo.uri.dojoUri("src/widget/templates/TabContainer.css"),selectedTab:"",postMixInProperties:function(){
if(this.selectedTab){
dojo.deprecated("selectedTab deprecated, use selectedChild instead, will be removed in","0.5");
this.selectedChild=this.selectedTab;
}
if(this.closeButton!="none"){
dojo.deprecated("closeButton deprecated, use closable='true' on each child instead, will be removed in","0.5");
}
dojo.widget.TabContainer.superclass.postMixInProperties.apply(this,arguments);
},fillInTemplate:function(){
this.tablist=dojo.widget.createWidget("TabController",{id:this.widgetId+"_tablist",labelPosition:this.labelPosition,doLayout:this.doLayout,containerId:this.widgetId},this.tablistNode);
dojo.widget.TabContainer.superclass.fillInTemplate.apply(this,arguments);
},postCreate:function(args,frag){
dojo.widget.TabContainer.superclass.postCreate.apply(this,arguments);
this.onResized();
},_setupChild:function(tab){
if(this.closeButton=="tab"||this.closeButton=="pane"){
tab.closable=true;
}
dojo.html.addClass(tab.domNode,"dojoTabPane");
dojo.widget.TabContainer.superclass._setupChild.apply(this,arguments);
},onResized:function(){
if(!this.doLayout){
return;
}
var _92e=this.labelPosition.replace(/-h/,"");
var _92f=[{domNode:this.tablist.domNode,layoutAlign:_92e},{domNode:this.containerNode,layoutAlign:"client"}];
dojo.widget.html.layout(this.domNode,_92f);
if(this.selectedChildWidget){
var _930=dojo.html.getContentBox(this.containerNode);
this.selectedChildWidget.resizeTo(_930.width,_930.height);
}
},selectTab:function(tab,_932){
dojo.deprecated("use selectChild() rather than selectTab(), selectTab() will be removed in","0.5");
this.selectChild(tab,_932);
},onKey:function(e){
if(e.keyCode==e.KEY_UP_ARROW&&e.ctrlKey){
var _934=this.correspondingTabButton||this.selectedTabWidget.tabButton;
_934.focus();
dojo.event.browser.stopEvent(e);
}else{
if(e.keyCode==e.KEY_DELETE&&e.altKey){
if(this.selectedChildWidget.closable){
this.closeChild(this.selectedChildWidget);
dojo.event.browser.stopEvent(e);
}
}
}
},destroy:function(){
this.tablist.destroy();
dojo.widget.TabContainer.superclass.destroy.apply(this,arguments);
}});
dojo.widget.defineWidget("dojo.widget.TabController",dojo.widget.PageController,{templateString:"<div wairole='tablist' dojoAttachEvent='onKey'></div>",labelPosition:"top",doLayout:true,"class":"",buttonWidget:"TabButton",postMixInProperties:function(){
if(!this["class"]){
this["class"]="dojoTabLabels-"+this.labelPosition+(this.doLayout?"":" dojoTabNoLayout");
}
dojo.widget.TabController.superclass.postMixInProperties.apply(this,arguments);
}});
dojo.widget.defineWidget("dojo.widget.TabButton",dojo.widget.PageButton,{templateString:"<div class='dojoTab' dojoAttachEvent='onClick'>"+"<div dojoAttachPoint='innerDiv'>"+"<span dojoAttachPoint='titleNode' tabIndex='-1' waiRole='tab'>${this.label}</span>"+"<span dojoAttachPoint='closeButtonNode' class='close closeImage' style='${this.closeButtonStyle}'"+"    dojoAttachEvent='onMouseOver:onCloseButtonMouseOver; onMouseOut:onCloseButtonMouseOut; onClick:onCloseButtonClick'></span>"+"</div>"+"</div>",postMixInProperties:function(){
this.closeButtonStyle=this.closeButton?"":"display: none";
dojo.widget.TabButton.superclass.postMixInProperties.apply(this,arguments);
},fillInTemplate:function(){
dojo.html.disableSelection(this.titleNode);
dojo.widget.TabButton.superclass.fillInTemplate.apply(this,arguments);
}});
dojo.widget.defineWidget("dojo.widget.a11y.TabButton",dojo.widget.TabButton,{imgPath:dojo.uri.dojoUri("src/widget/templates/images/tab_close.gif"),templateString:"<div class='dojoTab' dojoAttachEvent='onClick;onKey'>"+"<div dojoAttachPoint='innerDiv'>"+"<span dojoAttachPoint='titleNode' tabIndex='-1' waiRole='tab'>${this.label}</span>"+"<img class='close' src='${this.imgPath}' alt='[x]' style='${this.closeButtonStyle}'"+"    dojoAttachEvent='onClick:onCloseButtonClick'>"+"</div>"+"</div>"});
dojo.provide("dojo.widget.Select");
dojo.widget.defineWidget("dojo.widget.Select",dojo.widget.ComboBox,{forceValidOption:true,setValue:function(_935){
this.comboBoxValue.value=_935;
dojo.widget.html.stabile.setState(this.widgetId,this.getState(),true);
this.onValueChanged(_935);
},setLabel:function(_936){
this.comboBoxSelectionValue.value=_936;
if(this.textInputNode.value!=_936){
this.textInputNode.value=_936;
}
},getLabel:function(){
return this.comboBoxSelectionValue.value;
},getState:function(){
return {value:this.getValue(),label:this.getLabel()};
},onKeyUp:function(evt){
this.setLabel(this.textInputNode.value);
},setState:function(_938){
this.setValue(_938.value);
this.setLabel(_938.label);
},setAllValues:function(_939,_93a){
this.setLabel(_939);
this.setValue(_93a);
}});
dojo.provide("dojo.widget.ContentPane");
dojo.widget.defineWidget("dojo.widget.ContentPane",dojo.widget.HtmlWidget,function(){
this._styleNodes=[];
this._onLoadStack=[];
this._onUnloadStack=[];
this._callOnUnload=false;
this._ioBindObj;
this.scriptScope;
this.bindArgs={};
},{isContainer:true,adjustPaths:true,href:"",extractContent:true,parseContent:true,cacheContent:true,preload:false,refreshOnShow:false,handler:"",executeScripts:false,scriptSeparation:true,loadingMessage:"Loading...",isLoaded:false,postCreate:function(args,frag,_93d){
if(this.handler!==""){
this.setHandler(this.handler);
}
if(this.isShowing()||this.preload){
this.loadContents();
}
},show:function(){
if(this.refreshOnShow){
this.refresh();
}else{
this.loadContents();
}
dojo.widget.ContentPane.superclass.show.call(this);
},refresh:function(){
this.isLoaded=false;
this.loadContents();
},loadContents:function(){
if(this.isLoaded){
return;
}
if(dojo.lang.isFunction(this.handler)){
this._runHandler();
}else{
if(this.href!=""){
this._downloadExternalContent(this.href,this.cacheContent&&!this.refreshOnShow);
}
}
},setUrl:function(url){
this.href=url;
this.isLoaded=false;
if(this.preload||this.isShowing()){
this.loadContents();
}
},abort:function(){
var bind=this._ioBindObj;
if(!bind||!bind.abort){
return;
}
bind.abort();
delete this._ioBindObj;
},_downloadExternalContent:function(url,_941){
this.abort();
this._handleDefaults(this.loadingMessage,"onDownloadStart");
var self=this;
this._ioBindObj=dojo.io.bind(this._cacheSetting({url:url,mimetype:"text/html",handler:function(type,data,xhr){
delete self._ioBindObj;
if(type=="load"){
self.onDownloadEnd.call(self,url,data);
}else{
var e={responseText:xhr.responseText,status:xhr.status,statusText:xhr.statusText,responseHeaders:xhr.getAllResponseHeaders(),text:"Error loading '"+url+"' ("+xhr.status+" "+xhr.statusText+")"};
self._handleDefaults.call(self,e,"onDownloadError");
self.onLoad();
}
}},_941));
},_cacheSetting:function(_947,_948){
for(var x in this.bindArgs){
if(dojo.lang.isUndefined(_947[x])){
_947[x]=this.bindArgs[x];
}
}
if(dojo.lang.isUndefined(_947.useCache)){
_947.useCache=_948;
}
if(dojo.lang.isUndefined(_947.preventCache)){
_947.preventCache=!_948;
}
if(dojo.lang.isUndefined(_947.mimetype)){
_947.mimetype="text/html";
}
return _947;
},onLoad:function(e){
this._runStack("_onLoadStack");
this.isLoaded=true;
},onUnLoad:function(e){
dojo.deprecated(this.widgetType+".onUnLoad, use .onUnload (lowercased load)",0.5);
},onUnload:function(e){
this._runStack("_onUnloadStack");
delete this.scriptScope;
if(this.onUnLoad!==dojo.widget.ContentPane.prototype.onUnLoad){
this.onUnLoad.apply(this,arguments);
}
},_runStack:function(_94d){
var st=this[_94d];
var err="";
var _950=this.scriptScope||window;
for(var i=0;i<st.length;i++){
try{
st[i].call(_950);
}
catch(e){
err+="\n"+st[i]+" failed: "+e.description;
}
}
this[_94d]=[];
if(err.length){
var name=(_94d=="_onLoadStack")?"addOnLoad":"addOnUnLoad";
this._handleDefaults(name+" failure\n "+err,"onExecError","debug");
}
},addOnLoad:function(obj,func){
this._pushOnStack(this._onLoadStack,obj,func);
},addOnUnload:function(obj,func){
this._pushOnStack(this._onUnloadStack,obj,func);
},addOnUnLoad:function(){
dojo.deprecated(this.widgetType+".addOnUnLoad, use addOnUnload instead. (lowercased Load)",0.5);
this.addOnUnload.apply(this,arguments);
},_pushOnStack:function(_957,obj,func){
if(typeof func=="undefined"){
_957.push(obj);
}else{
_957.push(function(){
obj[func]();
});
}
},destroy:function(){
this.onUnload();
dojo.widget.ContentPane.superclass.destroy.call(this);
},onExecError:function(e){
},onContentError:function(e){
},onDownloadError:function(e){
},onDownloadStart:function(e){
},onDownloadEnd:function(url,data){
data=this.splitAndFixPaths(data,url);
this.setContent(data);
},_handleDefaults:function(e,_961,_962){
if(!_961){
_961="onContentError";
}
if(dojo.lang.isString(e)){
e={text:e};
}
if(!e.text){
e.text=e.toString();
}
e.toString=function(){
return this.text;
};
if(typeof e.returnValue!="boolean"){
e.returnValue=true;
}
if(typeof e.preventDefault!="function"){
e.preventDefault=function(){
this.returnValue=false;
};
}
this[_961](e);
if(e.returnValue){
switch(_962){
case true:
case "alert":
alert(e.toString());
break;
case "debug":
dojo.debug(e.toString());
break;
default:
if(this._callOnUnload){
this.onUnload();
}
this._callOnUnload=false;
if(arguments.callee._loopStop){
dojo.debug(e.toString());
}else{
arguments.callee._loopStop=true;
this._setContent(e.toString());
}
}
}
arguments.callee._loopStop=false;
},splitAndFixPaths:function(s,url){
var _965=[],_966=[],tmp=[];
var _968=[],_969=[],attr=[],_96b=[];
var str="",path="",fix="",_96f="",tag="",_971="";
if(!url){
url="./";
}
if(s){
var _972=/<title[^>]*>([\s\S]*?)<\/title>/i;
while(_968=_972.exec(s)){
_965.push(_968[1]);
s=s.substring(0,_968.index)+s.substr(_968.index+_968[0].length);
}
if(this.adjustPaths){
var _973=/<[a-z][a-z0-9]*[^>]*\s(?:(?:src|href|style)=[^>])+[^>]*>/i;
var _974=/\s(src|href|style)=(['"]?)([\w()\[\]\/.,\\'"-:;#=&?\s@]+?)\2/i;
var _975=/^(?:[#]|(?:(?:https?|ftps?|file|javascript|mailto|news):))/;
while(tag=_973.exec(s)){
str+=s.substring(0,tag.index);
s=s.substring((tag.index+tag[0].length),s.length);
tag=tag[0];
_96f="";
while(attr=_974.exec(tag)){
path="";
_971=attr[3];
switch(attr[1].toLowerCase()){
case "src":
case "href":
if(_975.exec(_971)){
path=_971;
}else{
path=(new dojo.uri.Uri(url,_971).toString());
}
break;
case "style":
path=dojo.html.fixPathsInCssText(_971,url);
break;
default:
path=_971;
}
fix=" "+attr[1]+"="+attr[2]+path+attr[2];
_96f+=tag.substring(0,attr.index)+fix;
tag=tag.substring((attr.index+attr[0].length),tag.length);
}
str+=_96f+tag;
}
s=str+s;
}
_972=/(?:<(style)[^>]*>([\s\S]*?)<\/style>|<link ([^>]*rel=['"]?stylesheet['"]?[^>]*)>)/i;
while(_968=_972.exec(s)){
if(_968[1]&&_968[1].toLowerCase()=="style"){
_96b.push(dojo.html.fixPathsInCssText(_968[2],url));
}else{
if(attr=_968[3].match(/href=(['"]?)([^'">]*)\1/i)){
_96b.push({path:attr[2]});
}
}
s=s.substring(0,_968.index)+s.substr(_968.index+_968[0].length);
}
var _972=/<script([^>]*)>([\s\S]*?)<\/script>/i;
var _976=/src=(['"]?)([^"']*)\1/i;
var _977=/.*(\bdojo\b\.js(?:\.uncompressed\.js)?)$/;
var _978=/(?:var )?\bdjConfig\b(?:[\s]*=[\s]*\{[^}]+\}|\.[\w]*[\s]*=[\s]*[^;\n]*)?;?|dojo\.hostenv\.writeIncludes\(\s*\);?/g;
var _979=/dojo\.(?:(?:require(?:After)?(?:If)?)|(?:widget\.(?:manager\.)?registerWidgetPackage)|(?:(?:hostenv\.)?setModulePrefix|registerModulePath)|defineNamespace)\((['"]).*?\1\)\s*;?/;
while(_968=_972.exec(s)){
if(this.executeScripts&&_968[1]){
if(attr=_976.exec(_968[1])){
if(_977.exec(attr[2])){
dojo.debug("Security note! inhibit:"+attr[2]+" from  being loaded again.");
}else{
_966.push({path:attr[2]});
}
}
}
if(_968[2]){
var sc=_968[2].replace(_978,"");
if(!sc){
continue;
}
while(tmp=_979.exec(sc)){
_969.push(tmp[0]);
sc=sc.substring(0,tmp.index)+sc.substr(tmp.index+tmp[0].length);
}
if(this.executeScripts){
_966.push(sc);
}
}
s=s.substr(0,_968.index)+s.substr(_968.index+_968[0].length);
}
if(this.extractContent){
_968=s.match(/<body[^>]*>\s*([\s\S]+)\s*<\/body>/im);
if(_968){
s=_968[1];
}
}
if(this.executeScripts&&this.scriptSeparation){
var _972=/(<[a-zA-Z][a-zA-Z0-9]*\s[^>]*?\S=)((['"])[^>]*scriptScope[^>]*>)/;
var _97b=/([\s'";:\(])scriptScope(.*)/;
str="";
while(tag=_972.exec(s)){
tmp=((tag[3]=="'")?"\"":"'");
fix="";
str+=s.substring(0,tag.index)+tag[1];
while(attr=_97b.exec(tag[2])){
tag[2]=tag[2].substring(0,attr.index)+attr[1]+"dojo.widget.byId("+tmp+this.widgetId+tmp+").scriptScope"+attr[2];
}
str+=tag[2];
s=s.substr(tag.index+tag[0].length);
}
s=str+s;
}
}
return {"xml":s,"styles":_96b,"titles":_965,"requires":_969,"scripts":_966,"url":url};
},_setContent:function(cont){
this.destroyChildren();
for(var i=0;i<this._styleNodes.length;i++){
if(this._styleNodes[i]&&this._styleNodes[i].parentNode){
this._styleNodes[i].parentNode.removeChild(this._styleNodes[i]);
}
}
this._styleNodes=[];
var node=this.containerNode||this.domNode;
while(node.firstChild){
try{
dojo.event.browser.clean(node.firstChild);
}
catch(e){
}
node.removeChild(node.firstChild);
}
try{
if(typeof cont!="string"){
node.innerHTML="";
node.appendChild(cont);
}else{
node.innerHTML=cont;
}
}
catch(e){
e.text="Couldn't load content:"+e.description;
this._handleDefaults(e,"onContentError");
}
},setContent:function(data){
this.abort();
if(this._callOnUnload){
this.onUnload();
}
this._callOnUnload=true;
if(!data||dojo.html.isNode(data)){
this._setContent(data);
this.onResized();
this.onLoad();
}else{
if(typeof data.xml!="string"){
this.href="";
data=this.splitAndFixPaths(data);
}
this._setContent(data.xml);
for(var i=0;i<data.styles.length;i++){
if(data.styles[i].path){
this._styleNodes.push(dojo.html.insertCssFile(data.styles[i].path));
}else{
this._styleNodes.push(dojo.html.insertCssText(data.styles[i]));
}
}
if(this.parseContent){
for(var i=0;i<data.requires.length;i++){
try{
eval(data.requires[i]);
}
catch(e){
e.text="ContentPane: error in package loading calls, "+(e.description||e);
this._handleDefaults(e,"onContentError","debug");
}
}
}
var _981=this;
function asyncParse(){
if(_981.executeScripts){
_981._executeScripts(data.scripts);
}
if(_981.parseContent){
var node=_981.containerNode||_981.domNode;
var _983=new dojo.xml.Parse();
var frag=_983.parseElement(node,null,true);
dojo.widget.getParser().createSubComponents(frag,_981);
}
_981.onResized();
_981.onLoad();
}
if(dojo.hostenv.isXDomain&&data.requires.length){
dojo.addOnLoad(asyncParse);
}else{
asyncParse();
}
}
},setHandler:function(_985){
var fcn=dojo.lang.isFunction(_985)?_985:window[_985];
if(!dojo.lang.isFunction(fcn)){
this._handleDefaults("Unable to set handler, '"+_985+"' not a function.","onExecError",true);
return;
}
this.handler=function(){
return fcn.apply(this,arguments);
};
},_runHandler:function(){
var ret=true;
if(dojo.lang.isFunction(this.handler)){
this.handler(this,this.domNode);
ret=false;
}
this.onLoad();
return ret;
},_executeScripts:function(_988){
var self=this;
var tmp="",code="";
for(var i=0;i<_988.length;i++){
if(_988[i].path){
dojo.io.bind(this._cacheSetting({"url":_988[i].path,"load":function(type,_98e){
dojo.lang.hitch(self,tmp=";"+_98e);
},"error":function(type,_990){
_990.text=type+" downloading remote script";
self._handleDefaults.call(self,_990,"onExecError","debug");
},"mimetype":"text/plain","sync":true},this.cacheContent));
code+=tmp;
}else{
code+=_988[i];
}
}
try{
if(this.scriptSeparation){
delete this.scriptScope;
this.scriptScope=new (new Function("_container_",code+"; return this;"))(self);
}else{
var djg=dojo.global();
if(djg.execScript){
djg.execScript(code);
}else{
var djd=dojo.doc();
var sc=djd.createElement("script");
sc.appendChild(djd.createTextNode(code));
(this.containerNode||this.domNode).appendChild(sc);
}
}
}
catch(e){
e.text="Error running scripts from content:\n"+e.description;
this._handleDefaults(e,"onExecError","debug");
}
}});
dojo.provide("dojo.widget.Tooltip");
dojo.widget.defineWidget("dojo.widget.Tooltip",[dojo.widget.ContentPane,dojo.widget.PopupContainerBase],{isContainer:true,caption:"",showDelay:500,hideDelay:100,connectId:"",templateCssPath:dojo.uri.dojoUri("src/widget/templates/TooltipTemplate.css"),fillInTemplate:function(args,frag){
if(this.caption!=""){
this.domNode.appendChild(document.createTextNode(this.caption));
}
this._connectNode=dojo.byId(this.connectId);
dojo.widget.Tooltip.superclass.fillInTemplate.call(this,args,frag);
this.addOnLoad(this,"_loadedContent");
dojo.html.addClass(this.domNode,"dojoTooltip");
var _996=this.getFragNodeRef(frag);
dojo.html.copyStyle(this.domNode,_996);
this.applyPopupBasicStyle();
},postCreate:function(args,frag){
dojo.event.connect(this._connectNode,"onmouseover",this,"onMouseOver");
dojo.widget.Tooltip.superclass.postCreate.call(this,args,frag);
},onMouseOver:function(e){
this._mouse={x:e.pageX,y:e.pageY};
if(!this._tracking){
dojo.event.connect(document.documentElement,"onmousemove",this,"onMouseMove");
this.tracking=true;
}
this._onHover(e);
},onMouseMove:function(e){
this._mouse={x:e.pageX,y:e.pageY};
if(dojo.html.overElement(this._connectNode,e)||dojo.html.overElement(this.domNode,e)){
this._onHover(e);
}else{
this._onUnHover(e);
}
},_onHover:function(e){
if(this._hover){
return;
}
this._hover=true;
if(this._hideTimer){
clearTimeout(this._hideTimer);
delete this._hideTimer;
}
if(!this.isShowingNow&&!this._showTimer){
this._showTimer=setTimeout(dojo.lang.hitch(this,"open"),this.showDelay);
}
},_onUnHover:function(e){
if(!this._hover){
return;
}
this._hover=false;
if(this._showTimer){
clearTimeout(this._showTimer);
delete this._showTimer;
}
if(this.isShowingNow&&!this._hideTimer){
this._hideTimer=setTimeout(dojo.lang.hitch(this,"close"),this.hideDelay);
}
if(!this.isShowingNow){
dojo.event.disconnect(document.documentElement,"onmousemove",this,"onMouseMove");
this._tracking=false;
}
},open:function(){
if(this.isShowingNow){
return;
}
dojo.widget.PopupContainerBase.prototype.open.call(this,this._mouse.x,this._mouse.y,null,[this._mouse.x,this._mouse.y],"TL,TR,BL,BR",[10,15]);
},close:function(){
if(this.isShowingNow){
if(this._showTimer){
clearTimeout(this._showTimer);
delete this._showTimer;
}
if(this._hideTimer){
clearTimeout(this._hideTimer);
delete this._hideTimer;
}
dojo.event.disconnect(document.documentElement,"onmousemove",this,"onMouseMove");
this._tracking=false;
dojo.widget.PopupContainerBase.prototype.close.call(this);
}
},_position:function(){
this.move(this._mouse.x,this._mouse.y,[10,15],"TL,TR,BL,BR");
},_loadedContent:function(){
if(this.isShowingNow){
this._position();
}
},checkSize:function(){
},uninitialize:function(){
this.close();
dojo.event.disconnect(this._connectNode,"onmouseover",this,"onMouseOver");
}});
if(!this["dojo"]){
alert("\"dojo/__package__.js\" is now located at \"dojo/dojo.js\". Please update your includes accordingly");
}
dojo.provide("dojo.string.Builder");
dojo.string.Builder=function(str){
this.arrConcat=(dojo.render.html.capable&&dojo.render.html["ie"]);
var a=[];
var b="";
var _9a0=this.length=b.length;
if(this.arrConcat){
if(b.length>0){
a.push(b);
}
b="";
}
this.toString=this.valueOf=function(){
return (this.arrConcat)?a.join(""):b;
};
this.append=function(){
for(var x=0;x<arguments.length;x++){
var s=arguments[x];
if(dojo.lang.isArrayLike(s)){
this.append.apply(this,s);
}else{
if(this.arrConcat){
a.push(s);
}else{
b+=s;
}
_9a0+=s.length;
this.length=_9a0;
}
}
return this;
};
this.clear=function(){
a=[];
b="";
_9a0=this.length=0;
return this;
};
this.remove=function(f,l){
var s="";
if(this.arrConcat){
b=a.join("");
}
a=[];
if(f>0){
s=b.substring(0,(f-1));
}
b=s+b.substring(f+l);
_9a0=this.length=b.length;
if(this.arrConcat){
a.push(b);
b="";
}
return this;
};
this.replace=function(o,n){
if(this.arrConcat){
b=a.join("");
}
a=[];
b=b.replace(o,n);
_9a0=this.length=b.length;
if(this.arrConcat){
a.push(b);
b="";
}
return this;
};
this.insert=function(idx,s){
if(this.arrConcat){
b=a.join("");
}
a=[];
if(idx==0){
b=s+b;
}else{
var t=b.split("");
t.splice(idx,0,s);
b=t.join("");
}
_9a0=this.length=b.length;
if(this.arrConcat){
a.push(b);
b="";
}
return this;
};
this.append.apply(this,arguments);
};
dojo.provide("dojo.string.*");
dojo.provide("dojo.widget.ProgressBar");
dojo.widget.defineWidget("dojo.widget.ProgressBar",dojo.widget.HtmlWidget,{progressValue:"0",maxProgressValue:100,width:300,height:30,frontPercentClass:"frontPercent",backPercentClass:"backPercent",frontBarClass:"frontBar",backBarClass:"backBar",hasText:"false",isVertical:"false",showOnlyIntegers:"false",dataSource:"",pollInterval:"3000",duration:"1000",templatePath:dojo.uri.dojoUri("src/widget/templates/ProgressBar.html"),templateCssPath:dojo.uri.dojoUri("src/widget/templates/ProgressBar.css"),containerNode:null,internalProgress:null,_pixelUnitRatio:0,_pixelPercentRatio:0,_unitPercentRatio:0,_unitPixelRatio:0,_floatDimension:0,_intDimension:0,_progressPercentValue:"0%",_floatMaxProgressValue:0,_dimension:"width",_pixelValue:0,_oInterval:null,_animation:null,_animationStopped:true,_progressValueBak:false,_hasTextBak:false,fillInTemplate:function(args,frag){
this.internalProgress.className=this.frontBarClass;
this.containerNode.className=this.backBarClass;
if(this.isVertical=="true"){
this.internalProgress.style.bottom="0px";
this.internalProgress.style.left="0px";
this._dimension="height";
}else{
this.internalProgress.style.top="0px";
this.internalProgress.style.left="0px";
this._dimension="width";
}
this.frontPercentLabel.className=this.frontPercentClass;
this.backPercentLabel.className=this.backPercentClass;
this.progressValue=""+this.progressValue;
this.domNode.style.height=this.height+"px";
this.domNode.style.width=this.width+"px";
this._intDimension=parseInt("0"+eval("this."+this._dimension));
this._floatDimension=parseFloat("0"+eval("this."+this._dimension));
this._pixelPercentRatio=this._floatDimension/100;
this.setMaxProgressValue(this.maxProgressValue,true);
this.setProgressValue(dojo.string.trim(this.progressValue),true);
dojo.debug("float dimension: "+this._floatDimension);
dojo.debug("this._unitPixelRatio: "+this._unitPixelRatio);
this.showText(this.hasText);
},showText:function(_9ad){
if(_9ad=="true"){
this.backPercentLabel.style.display="block";
this.frontPercentLabel.style.display="block";
}else{
this.backPercentLabel.style.display="none";
this.frontPercentLabel.style.display="none";
}
this.hasText=_9ad;
},postCreate:function(args,frag){
this.render();
},_backupValues:function(){
this._progressValueBak=this.progressValue;
this._hasTextBak=this.hasText;
},_restoreValues:function(){
this.setProgressValue(this._progressValueBak);
this.showText(this._hasTextBak);
},_setupAnimation:function(){
var _9b0=this;
dojo.debug("internalProgress width: "+this.internalProgress.style.width);
this._animation=dojo.lfx.html.slideTo(this.internalProgress,{top:0,left:parseInt(this.width)-parseInt(this.internalProgress.style.width)},parseInt(this.duration),null,function(){
var _9b1=dojo.lfx.html.slideTo(_9b0.internalProgress,{top:0,left:0},parseInt(_9b0.duration));
dojo.event.connect(_9b1,"onEnd",function(){
if(!_9b0._animationStopped){
_9b0._animation.play();
}
});
if(!_9b0._animationStopped){
_9b1.play();
}
_9b1=null;
});
},getMaxProgressValue:function(){
return this.maxProgressValue;
},setMaxProgressValue:function(_9b2,_9b3){
if(!this._animationStopped){
return;
}
this.maxProgressValue=_9b2;
this._floatMaxProgressValue=parseFloat("0"+this.maxProgressValue);
this._pixelUnitRatio=this._floatDimension/this.maxProgressValue;
this._unitPercentRatio=this._floatMaxProgressValue/100;
this._unitPixelRatio=this._floatMaxProgressValue/this._floatDimension;
this.setProgressValue(this.progressValue,true);
if(!_9b3){
this.render();
}
},setProgressValue:function(_9b4,_9b5){
if(!this._animationStopped){
return;
}
this._progressPercentValue="0%";
var _9b6=dojo.string.trim(""+_9b4);
var _9b7=parseFloat("0"+_9b6);
var _9b8=parseInt("0"+_9b6);
var _9b9=0;
if(dojo.string.endsWith(_9b6,"%",false)){
this._progressPercentValue=Math.min(_9b7.toFixed(1),100)+"%";
_9b6=Math.min((_9b7)*this._unitPercentRatio,this.maxProgressValue);
_9b9=Math.min((_9b7)*this._pixelPercentRatio,eval("this."+this._dimension));
}else{
this.progressValue=Math.min(_9b7,this.maxProgressValue);
this._progressPercentValue=Math.min((_9b7/this._unitPercentRatio).toFixed(1),100)+"%";
_9b9=Math.min(_9b7/this._unitPixelRatio,eval("this."+this._dimension));
}
this.progressValue=dojo.string.trim(_9b6);
this._pixelValue=_9b9;
if(!_9b5){
this.render();
}
},setCurrentPercentProgress:function(_9ba){
this._setCurrentPixelProgress(_9ba);
},getProgressValue:function(){
return this.progressValue;
},getProgressPercentValue:function(){
return this._progressPercentValue;
},setDataSource:function(_9bb){
this.dataSource=_9bb;
},setPollInterval:function(_9bc){
this.pollInterval=_9bc;
},start:function(){
var _9bd=dojo.lang.hitch(this,this._showRemoteProgress);
this._oInterval=setInterval(_9bd,this.pollInterval);
},startAnimation:function(){
if(this._animationStopped){
this._backupValues();
this.setProgressValue("10%");
this._animationStopped=false;
this._setupAnimation();
this.showText(false);
this.internalProgress.style.height="105%";
this._animation.play();
}
},stopAnimation:function(){
if(this._animation){
this._animationStopped=true;
this._animation.stop();
this.internalProgress.style.height="100%";
this.internalProgress.style.left="0px";
this._restoreValues();
this._setLabelPosition();
}
},_showRemoteProgress:function(){
var _9be=this;
if((this.getMaxProgressValue()==this.getProgressValue())&&this._oInterval){
clearInterval(this._oInterval);
this._oInterval=null;
this.setProgressValue("100%");
return;
}
var _9bf={url:_9be.dataSource,method:"POST",mimetype:"text/json",error:function(type,_9c1){
dojo.debug("[ProgressBar] showRemoteProgress error");
},load:function(type,data,evt){
_9be.setProgressValue((_9be._oInterval?data["progress"]:"100%"));
}};
dojo.io.bind(_9bf);
},render:function(){
this._setPercentLabel(dojo.string.trim(this._progressPercentValue));
this._setPixelValue(this._pixelValue);
this._setLabelPosition();
},_setLabelPosition:function(){
var _9c5=dojo.html.getContentBox(this.frontPercentLabel).width;
var _9c6=dojo.html.getContentBox(this.frontPercentLabel).height;
var _9c7=dojo.html.getContentBox(this.backPercentLabel).width;
var _9c8=dojo.html.getContentBox(this.backPercentLabel).height;
var _9c9=(parseInt(this.width)-_9c5)/2+"px";
var _9ca=(parseInt(this.height)-parseInt(_9c6))/2+"px";
var _9cb=(parseInt(this.width)-_9c7)/2+"px";
var _9cc=(parseInt(this.height)-parseInt(_9c8))/2+"px";
this.frontPercentLabel.style.left=_9c9;
this.backPercentLabel.style.left=_9cb;
this.frontPercentLabel.style.bottom=_9ca;
this.backPercentLabel.style.bottom=_9cc;
},_setPercentLabel:function(_9cd){
dojo.dom.removeChildren(this.frontPercentLabel);
dojo.dom.removeChildren(this.backPercentLabel);
var _9ce=this.showOnlyIntegers=="false"?_9cd:parseInt(_9cd)+"%";
this.frontPercentLabel.appendChild(document.createTextNode(_9ce));
this.backPercentLabel.appendChild(document.createTextNode(_9ce));
},_setPixelValue:function(_9cf){
eval("this.internalProgress.style."+this._dimension+" = "+_9cf+" + 'px'");
this.onChange();
},onChange:function(){
}});
dojo.provide("dojo.widget.LinkPane");
dojo.widget.defineWidget("dojo.widget.LinkPane",dojo.widget.ContentPane,{templateString:"<div class=\"dojoLinkPane\"></div>",fillInTemplate:function(args,frag){
var _9d2=this.getFragNodeRef(frag);
this.label+=_9d2.innerHTML;
var _9d2=this.getFragNodeRef(frag);
dojo.html.copyStyle(this.domNode,_9d2);
}});

