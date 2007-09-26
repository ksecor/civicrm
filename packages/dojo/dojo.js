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
dojo.version={major:0,minor:0,patch:0,flag:"dev",revision:Number("$Rev: 8616 $".match(/[0-9]+/)[0]),toString:function(){
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
}else{
_12=dojo.errorToString(_12);
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
if(typeof setTimeout=="object"||(djConfig["useXDomain"]&&dojo.render.html.opera)){
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
if(djConfig["modulePaths"]){
for(var param in djConfig["modulePaths"]){
dojo.registerModulePath(param,djConfig["modulePaths"][param]);
}
}
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
var _6f=_6e?_6e.toLowerCase():dojo.locale;
if(_6f=="root"){
_6f="ROOT";
}
return _6f;
};
dojo.hostenv.searchLocalePath=function(_70,_71,_72){
_70=dojo.hostenv.normalizeLocale(_70);
var _73=_70.split("-");
var _74=[];
for(var i=_73.length;i>0;i--){
_74.push(_73.slice(0,i).join("-"));
}
_74.push(false);
if(_71){
_74.reverse();
}
for(var j=_74.length-1;j>=0;j--){
var loc=_74[j]||"ROOT";
var _78=_72(loc);
if(_78){
break;
}
}
};
dojo.hostenv.localesGenerated=["ROOT","es-es","es","it-it","pt-br","de","fr-fr","zh-cn","pt","en-us","zh","fr","zh-tw","it","en-gb","xx","de-de","ko-kr","ja-jp","ko","en","ja"];
dojo.hostenv.registerNlsPrefix=function(){
dojo.registerModulePath("nls","nls");
};
dojo.hostenv.preloadLocalizations=function(){
if(dojo.hostenv.localesGenerated){
dojo.hostenv.registerNlsPrefix();
function preload(_79){
_79=dojo.hostenv.normalizeLocale(_79);
dojo.hostenv.searchLocalePath(_79,true,function(loc){
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
var _7c=djConfig.extraLocale||[];
for(var i=0;i<_7c.length;i++){
preload(_7c[i]);
}
}
dojo.hostenv.preloadLocalizations=function(){
};
};
dojo.requireLocalization=function(_7e,_7f,_80,_81){
dojo.hostenv.preloadLocalizations();
var _82=dojo.hostenv.normalizeLocale(_80);
var _83=[_7e,"nls",_7f].join(".");
var _84="";
if(_81){
var _85=_81.split(",");
for(var i=0;i<_85.length;i++){
if(_82.indexOf(_85[i])==0){
if(_85[i].length>_84.length){
_84=_85[i];
}
}
}
if(!_84){
_84="ROOT";
}
}
var _87=_81?_84:_82;
var _88=dojo.hostenv.findModule(_83);
var _89=null;
if(_88){
if(djConfig.localizationComplete&&_88._built){
return;
}
var _8a=_87.replace("-","_");
var _8b=_83+"."+_8a;
_89=dojo.hostenv.findModule(_8b);
}
if(!_89){
_88=dojo.hostenv.startPackage(_83);
var _8c=dojo.hostenv.getModuleSymbols(_7e);
var _8d=_8c.concat("nls").join("/");
var _8e;
dojo.hostenv.searchLocalePath(_87,_81,function(loc){
var _90=loc.replace("-","_");
var _91=_83+"."+_90;
var _92=false;
if(!dojo.hostenv.findModule(_91)){
dojo.hostenv.startPackage(_91);
var _93=[_8d];
if(loc!="ROOT"){
_93.push(loc);
}
_93.push(_7f);
var _94=_93.join("/")+".js";
_92=dojo.hostenv.loadPath(_94,null,function(_95){
var _96=function(){
};
_96.prototype=_8e;
_88[_90]=new _96();
for(var j in _95){
_88[_90][j]=_95[j];
}
});
}else{
_92=true;
}
if(_92&&_88[_90]){
_8e=_88[_90];
}else{
_88[_90]=_8e;
}
if(_81){
return true;
}
});
}
if(_81&&_82!=_84){
_88[_82.replace("-","_")]=_88[_84.replace("-","_")];
}
};
(function(){
var _98=djConfig.extraLocale;
if(_98){
if(!_98 instanceof Array){
_98=[_98];
}
var req=dojo.requireLocalization;
dojo.requireLocalization=function(m,b,_9c,_9d){
req(m,b,_9c,_9d);
if(_9c){
return;
}
for(var i=0;i<_98.length;i++){
req(m,b,_98[i],_9d);
}
};
}
})();
}
if(typeof window!="undefined"){
(function(){
if(djConfig.allowQueryConfig){
var _9f=document.location.toString();
var _a0=_9f.split("?",2);
if(_a0.length>1){
var _a1=_a0[1];
var _a2=_a1.split("&");
for(var x in _a2){
var sp=_a2[x].split("=");
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
var _a6=document.getElementsByTagName("script");
var _a7=/(__package__|dojo|bootstrap1)\.js([\?\.]|$)/i;
for(var i=0;i<_a6.length;i++){
var src=_a6[i].getAttribute("src");
if(!src){
continue;
}
var m=src.match(_a7);
if(m){
var _ab=src.substring(0,m.index);
if(src.indexOf("bootstrap1")>-1){
_ab+="../";
}
if(!this["djConfig"]){
djConfig={};
}
if(djConfig["baseScriptUri"]==""){
djConfig["baseScriptUri"]=_ab;
}
if(djConfig["baseRelativePath"]==""){
djConfig["baseRelativePath"]=_ab;
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
var _b3=dua.indexOf("Gecko");
drh.mozilla=drh.moz=(_b3>=0)&&(!drh.khtml);
if(drh.mozilla){
drh.geckoVersion=dua.substring(_b3+6,_b3+14);
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
var _b5=window["document"];
var tdi=_b5["implementation"];
if(drh.ie&&(window.location.protocol=="file:")){
djConfig.ieForceActiveXXhr=true;
}
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
}else{
}
})();
dojo.hostenv.startPackage("dojo.hostenv");
dojo.render.name=dojo.hostenv.name_="browser";
dojo.hostenv.searchIds=[];
dojo.hostenv._XMLHTTP_PROGIDS=["Msxml2.XMLHTTP","Microsoft.XMLHTTP","Msxml2.XMLHTTP.4.0"];
dojo.hostenv.getXmlhttpObject=function(){
var _b9=null;
var _ba=null;
if(!dojo.render.html.ie||!djConfig.ieForceActiveXXhr){
try{
_b9=new XMLHttpRequest();
}
catch(e){
}
}
if(!_b9){
for(var i=0;i<3;++i){
var _bc=dojo.hostenv._XMLHTTP_PROGIDS[i];
try{
_b9=new ActiveXObject(_bc);
}
catch(e){
_ba=e;
}
if(_b9){
dojo.hostenv._XMLHTTP_PROGIDS=[_bc];
break;
}
}
}
if(!_b9){
return dojo.raise("XMLHTTP not available",_ba);
}
return _b9;
};
dojo.hostenv._blockAsync=false;
dojo.hostenv.getText=function(uri,_be,_bf){
if(!_be){
this._blockAsync=true;
}
var _c0=this.getXmlhttpObject();
function isDocumentOk(_c1){
var _c2=_c1["status"];
return Boolean((!_c2)||((200<=_c2)&&(300>_c2))||(_c2==304));
}
if(_be){
var _c3=this,_c4=null,gbl=dojo.global();
var xhr=dojo.evalObjPath("dojo.io.XMLHTTPTransport");
_c0.onreadystatechange=function(){
if(_c4){
gbl.clearTimeout(_c4);
_c4=null;
}
if(_c3._blockAsync||(xhr&&xhr._blockAsync)){
_c4=gbl.setTimeout(function(){
_c0.onreadystatechange.apply(this);
},10);
}else{
if(4==_c0.readyState){
if(isDocumentOk(_c0)){
_be(_c0.responseText);
}
}
}
};
}
_c0.open("GET",uri,_be?true:false);
try{
_c0.send(null);
if(_be){
return null;
}
if(!isDocumentOk(_c0)){
var err=Error("Unable to load "+uri+" status:"+_c0.status);
err.status=_c0.status;
err.responseText=_c0.responseText;
throw err;
}
}
catch(e){
this._blockAsync=false;
if((_bf)&&(!_be)){
return null;
}else{
throw e;
}
}
this._blockAsync=false;
return _c0.responseText;
};
dojo.hostenv.defaultDebugContainerId="dojoDebug";
dojo.hostenv._println_buffer=[];
dojo.hostenv._println_safe=false;
dojo.hostenv.println=function(_c8){
if(!dojo.hostenv._println_safe){
dojo.hostenv._println_buffer.push(_c8);
}else{
try{
var _c9=document.getElementById(djConfig.debugContainerId?djConfig.debugContainerId:dojo.hostenv.defaultDebugContainerId);
if(!_c9){
_c9=dojo.body();
}
var div=document.createElement("div");
div.appendChild(document.createTextNode(_c8));
_c9.appendChild(div);
}
catch(e){
try{
document.write("<div>"+_c8+"</div>");
}
catch(e2){
window.status=_c8;
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
function dj_addNodeEvtHdlr(_cb,_cc,fp){
var _ce=_cb["on"+_cc]||function(){
};
_cb["on"+_cc]=function(){
fp.apply(_cb,arguments);
_ce.apply(_cb,arguments);
};
return true;
}
dojo.hostenv._djInitFired=false;
function dj_load_init(e){
dojo.hostenv._djInitFired=true;
var _d0=(e&&e.type)?e.type.toLowerCase():"load";
if(arguments.callee.initialized||(_d0!="domcontentloaded"&&_d0!="load")){
return;
}
arguments.callee.initialized=true;
if(typeof (_timer)!="undefined"){
clearInterval(_timer);
delete _timer;
}
var _d1=function(){
if(dojo.render.html.ie){
dojo.hostenv.makeWidgets();
}
};
if(dojo.hostenv.inFlightCount==0){
_d1();
dojo.hostenv.modulesLoaded();
}else{
dojo.hostenv.modulesLoadedListeners.unshift(_d1);
}
}
if(document.addEventListener){
if(dojo.render.html.opera||(dojo.render.html.moz&&(djConfig["enableMozDomContentLoaded"]===true))){
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
var _d3=[];
if(djConfig.searchIds&&djConfig.searchIds.length>0){
_d3=_d3.concat(djConfig.searchIds);
}
if(dojo.hostenv.searchIds&&dojo.hostenv.searchIds.length>0){
_d3=_d3.concat(dojo.hostenv.searchIds);
}
if((djConfig.parseWidgets)||(_d3.length>0)){
if(dojo.evalObjPath("dojo.widget.Parse")){
var _d4=new dojo.xml.Parse();
if(_d3.length>0){
for(var x=0;x<_d3.length;x++){
var _d6=document.getElementById(_d3[x]);
if(!_d6){
continue;
}
var _d7=_d4.parseElement(_d6,null,true);
dojo.widget.getParser().createComponents(_d7);
}
}else{
if(djConfig.parseWidgets){
var _d7=_d4.parseElement(dojo.body(),null,true);
dojo.widget.getParser().createComponents(_d7);
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
dojo.setContext=function(_dc,_dd){
dj_currentContext=_dc;
dj_currentDocument=_dd;
};
dojo._fireCallback=function(_de,_df,_e0){
if((_df)&&((typeof _de=="string")||(_de instanceof String))){
_de=_df[_de];
}
return (_df?_de.apply(_df,_e0||[]):_de());
};
dojo.withGlobal=function(_e1,_e2,_e3,_e4){
var _e5;
var _e6=dj_currentContext;
var _e7=dj_currentDocument;
try{
dojo.setContext(_e1,_e1.document);
_e5=dojo._fireCallback(_e2,_e3,_e4);
}
finally{
dojo.setContext(_e6,_e7);
}
return _e5;
};
dojo.withDoc=function(_e8,_e9,_ea,_eb){
var _ec;
var _ed=dj_currentDocument;
try{
dj_currentDocument=_e8;
_ec=dojo._fireCallback(_e9,_ea,_eb);
}
finally{
dj_currentDocument=_ed;
}
return _ec;
};
}
dojo.requireIf((djConfig["isDebug"]||djConfig["debugAtAllCosts"]),"dojo.debug");
dojo.requireIf(djConfig["debugAtAllCosts"]&&!window.widget&&!djConfig["useXDomain"],"dojo.browser_debug");
dojo.requireIf(djConfig["debugAtAllCosts"]&&!window.widget&&djConfig["useXDomain"],"dojo.browser_debug_xd");
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
if(!dojo.lang.isFunction(_104)){
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
return (it instanceof Function||typeof it=="function");
};
(function(){
if((dojo.render.html.capable)&&(dojo.render.html["safari"])){
dojo.lang.isFunction=function(it){
if((typeof (it)=="function")&&(it=="[object NodeList]")){
return false;
}
return (it instanceof Function||typeof it=="function");
};
}
})();
dojo.lang.isString=function(it){
return (typeof it=="string"||it instanceof String);
};
dojo.lang.isAlien=function(it){
if(!it){
return false;
}
return !dojo.lang.isFunction(it)&&/\{\s*\[native code\]\s*\}/.test(String(it));
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
dojo.lang.setTimeout=function(func,_12a){
var _12b=window,_12c=2;
if(!dojo.lang.isFunction(func)){
_12b=func;
func=_12a;
_12a=arguments[2];
_12c++;
}
if(dojo.lang.isString(func)){
func=_12b[func];
}
var args=[];
for(var i=_12c;i<arguments.length;i++){
args.push(arguments[i]);
}
return dojo.global().setTimeout(function(){
func.apply(_12b,args);
},_12a);
};
dojo.lang.clearTimeout=function(_12f){
dojo.global().clearTimeout(_12f);
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
dojo.lang.getObjPathValue=function(_138,_139,_13a){
with(dojo.parseObjPath(_138,_139,_13a)){
return dojo.evalProp(prop,obj,_13a);
}
};
dojo.lang.setObjPathValue=function(_13b,_13c,_13d,_13e){
dojo.deprecated("dojo.lang.setObjPathValue","use dojo.parseObjPath and the '=' operator","0.6");
if(arguments.length<4){
_13e=true;
}
with(dojo.parseObjPath(_13b,_13d,_13e)){
if(obj&&(_13e||(prop in obj))){
obj[prop]=_13c;
}
}
};
dojo.provide("dojo.io.common");
dojo.io.transports=[];
dojo.io.hdlrFuncNames=["load","error","timeout"];
dojo.io.Request=function(url,_140,_141,_142){
if((arguments.length==1)&&(arguments[0].constructor==Object)){
this.fromKwArgs(arguments[0]);
}else{
this.url=url;
if(_140){
this.mimetype=_140;
}
if(_141){
this.transport=_141;
}
if(arguments.length>=4){
this.changeUrl=_142;
}
}
};
dojo.lang.extend(dojo.io.Request,{url:"",mimetype:"text/plain",method:"GET",content:undefined,transport:undefined,changeUrl:undefined,formNode:undefined,sync:false,bindSuccess:false,useCache:false,preventCache:false,jsonFilter:function(_143){
if((this.mimetype=="text/json-comment-filtered")||(this.mimetype=="application/json-comment-filtered")){
var _144=_143.indexOf("/*");
var _145=_143.lastIndexOf("*/");
if((_144==-1)||(_145==-1)){
dojo.debug("your JSON wasn't comment filtered!");
return "";
}
return _143.substring(_144+2,_145);
}
dojo.debug("please consider using a mimetype of text/json-comment-filtered to avoid potential security issues with JSON endpoints");
return _143;
},load:function(type,data,_148,_149){
},error:function(type,_14b,_14c,_14d){
},timeout:function(type,_14f,_150,_151){
},handle:function(type,data,_154,_155){
},timeoutSeconds:0,abort:function(){
},fromKwArgs:function(_156){
if(_156["url"]){
_156.url=_156.url.toString();
}
if(_156["formNode"]){
_156.formNode=dojo.byId(_156.formNode);
}
if(!_156["method"]&&_156["formNode"]&&_156["formNode"].method){
_156.method=_156["formNode"].method;
}
if(!_156["handle"]&&_156["handler"]){
_156.handle=_156.handler;
}
if(!_156["load"]&&_156["loaded"]){
_156.load=_156.loaded;
}
if(!_156["changeUrl"]&&_156["changeURL"]){
_156.changeUrl=_156.changeURL;
}
_156.encoding=dojo.lang.firstValued(_156["encoding"],djConfig["bindEncoding"],"");
_156.sendTransport=dojo.lang.firstValued(_156["sendTransport"],djConfig["ioSendTransport"],false);
var _157=dojo.lang.isFunction;
for(var x=0;x<dojo.io.hdlrFuncNames.length;x++){
var fn=dojo.io.hdlrFuncNames[x];
if(_156[fn]&&_157(_156[fn])){
continue;
}
if(_156["handle"]&&_157(_156["handle"])){
_156[fn]=_156.handle;
}
}
dojo.lang.mixin(this,_156);
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
dojo.io.bind=function(_15e){
if(!(_15e instanceof dojo.io.Request)){
try{
_15e=new dojo.io.Request(_15e);
}
catch(e){
dojo.debug(e);
}
}
var _15f="";
if(_15e["transport"]){
_15f=_15e["transport"];
if(!this[_15f]){
dojo.io.sendBindError(_15e,"No dojo.io.bind() transport with name '"+_15e["transport"]+"'.");
return _15e;
}
if(!this[_15f].canHandle(_15e)){
dojo.io.sendBindError(_15e,"dojo.io.bind() transport with name '"+_15e["transport"]+"' cannot handle this type of request.");
return _15e;
}
}else{
for(var x=0;x<dojo.io.transports.length;x++){
var tmp=dojo.io.transports[x];
if((this[tmp])&&(this[tmp].canHandle(_15e))){
_15f=tmp;
break;
}
}
if(_15f==""){
dojo.io.sendBindError(_15e,"None of the loaded transports for dojo.io.bind()"+" can handle the request.");
return _15e;
}
}
this[_15f].bind(_15e);
_15e.bindSuccess=true;
return _15e;
};
dojo.io.sendBindError=function(_162,_163){
if((typeof _162.error=="function"||typeof _162.handle=="function")&&(typeof setTimeout=="function"||typeof setTimeout=="object")){
var _164=new dojo.io.Error(_163);
setTimeout(function(){
_162[(typeof _162.error=="function")?"error":"handle"]("error",_164,null,_162);
},50);
}else{
dojo.raise(_163);
}
};
dojo.io.queueBind=function(_165){
if(!(_165 instanceof dojo.io.Request)){
try{
_165=new dojo.io.Request(_165);
}
catch(e){
dojo.debug(e);
}
}
var _166=_165.load;
_165.load=function(){
dojo.io._queueBindInFlight=false;
var ret=_166.apply(this,arguments);
dojo.io._dispatchNextQueueBind();
return ret;
};
var _168=_165.error;
_165.error=function(){
dojo.io._queueBindInFlight=false;
var ret=_168.apply(this,arguments);
dojo.io._dispatchNextQueueBind();
return ret;
};
dojo.io._bindQueue.push(_165);
dojo.io._dispatchNextQueueBind();
return _165;
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
dojo.io.argsFromMap=function(map,_16b,last){
var enc=/utf/i.test(_16b||"")?encodeURIComponent:dojo.string.encodeAscii;
var _16e=[];
var _16f=new Object();
for(var name in map){
var _171=function(elt){
var val=enc(name)+"="+enc(elt);
_16e[(last==name)?"push":"unshift"](val);
};
if(!_16f[name]){
var _174=map[name];
if(dojo.lang.isArray(_174)){
dojo.lang.forEach(_174,_171);
}else{
_171(_174);
}
}
}
return _16e.join("&");
};
dojo.io.setIFrameSrc=function(_175,src,_177){
try{
var r=dojo.render.html;
if(!_177){
if(r.safari){
_175.location=src;
}else{
frames[_175.name].location=src;
}
}else{
var idoc;
if(r.ie){
idoc=_175.contentWindow.document;
}else{
if(r.safari){
idoc=_175.document;
}else{
idoc=_175.contentWindow;
}
}
if(!idoc){
_175.location=src;
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
dojo.lang.mixin(dojo.lang,{has:function(obj,name){
try{
return typeof obj[name]!="undefined";
}
catch(e){
return false;
}
},isEmpty:function(obj){
if(dojo.lang.isObject(obj)){
var tmp={};
var _17e=0;
for(var x in obj){
if(obj[x]&&(!tmp[x])){
_17e++;
break;
}
}
return _17e==0;
}else{
if(dojo.lang.isArrayLike(obj)||dojo.lang.isString(obj)){
return obj.length==0;
}
}
},map:function(arr,obj,_182){
var _183=dojo.lang.isString(arr);
if(_183){
arr=arr.split("");
}
if(dojo.lang.isFunction(obj)&&(!_182)){
_182=obj;
obj=dj_global;
}else{
if(dojo.lang.isFunction(obj)&&_182){
var _184=obj;
obj=_182;
_182=_184;
}
}
if(Array.map){
var _185=Array.map(arr,_182,obj);
}else{
var _185=[];
for(var i=0;i<arr.length;++i){
_185.push(_182.call(obj,arr[i]));
}
}
if(_183){
return _185.join("");
}else{
return _185;
}
},reduce:function(arr,_188,obj,_18a){
var _18b=_188;
if(arguments.length==2){
_18a=_188;
_18b=arr[0];
arr=arr.slice(1);
}else{
if(arguments.length==3){
if(dojo.lang.isFunction(obj)){
_18a=obj;
obj=null;
}
}else{
if(dojo.lang.isFunction(obj)){
var tmp=_18a;
_18a=obj;
obj=tmp;
}
}
}
var ob=obj||dj_global;
dojo.lang.map(arr,function(val){
_18b=_18a.call(ob,_18b,val);
});
return _18b;
},forEach:function(_18f,_190,_191){
if(dojo.lang.isString(_18f)){
_18f=_18f.split("");
}
if(Array.forEach){
Array.forEach(_18f,_190,_191);
}else{
if(!_191){
_191=dj_global;
}
for(var i=0,l=_18f.length;i<l;i++){
_190.call(_191,_18f[i],i,_18f);
}
}
},_everyOrSome:function(_194,arr,_196,_197){
if(dojo.lang.isString(arr)){
arr=arr.split("");
}
if(Array.every){
return Array[_194?"every":"some"](arr,_196,_197);
}else{
if(!_197){
_197=dj_global;
}
for(var i=0,l=arr.length;i<l;i++){
var _19a=_196.call(_197,arr[i],i,arr);
if(_194&&!_19a){
return false;
}else{
if((!_194)&&(_19a)){
return true;
}
}
}
return Boolean(_194);
}
},every:function(arr,_19c,_19d){
return this._everyOrSome(true,arr,_19c,_19d);
},some:function(arr,_19f,_1a0){
return this._everyOrSome(false,arr,_19f,_1a0);
},filter:function(arr,_1a2,_1a3){
var _1a4=dojo.lang.isString(arr);
if(_1a4){
arr=arr.split("");
}
var _1a5;
if(Array.filter){
_1a5=Array.filter(arr,_1a2,_1a3);
}else{
if(!_1a3){
if(arguments.length>=3){
dojo.raise("thisObject doesn't exist!");
}
_1a3=dj_global;
}
_1a5=[];
for(var i=0;i<arr.length;i++){
if(_1a2.call(_1a3,arr[i],i,arr)){
_1a5.push(arr[i]);
}
}
}
if(_1a4){
return _1a5.join("");
}else{
return _1a5;
}
},unnest:function(){
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
},toArray:function(_1aa,_1ab){
var _1ac=[];
for(var i=_1ab||0;i<_1aa.length;i++){
_1ac.push(_1aa[i]);
}
return _1ac;
}});
dojo.provide("dojo.lang.func");
dojo.lang.hitch=function(_1ae,_1af){
var args=[];
for(var x=2;x<arguments.length;x++){
args.push(arguments[x]);
}
var fcn=(dojo.lang.isString(_1af)?_1ae[_1af]:_1af)||function(){
};
return function(){
var ta=args.concat([]);
for(var x=0;x<arguments.length;x++){
ta.push(arguments[x]);
}
return fcn.apply(_1ae,ta);
};
};
dojo.lang.anonCtr=0;
dojo.lang.anon={};
dojo.lang.nameAnonFunc=function(_1b5,_1b6,_1b7){
var nso=(_1b6||dojo.lang.anon);
if((_1b7)||((dj_global["djConfig"])&&(djConfig["slowAnonFuncLookups"]==true))){
for(var x in nso){
try{
if(nso[x]===_1b5){
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
nso[ret]=_1b5;
return ret;
};
dojo.lang.forward=function(_1bb){
return function(){
return this[_1bb].apply(this,arguments);
};
};
dojo.lang.curry=function(_1bc,func){
var _1be=[];
_1bc=_1bc||dj_global;
if(dojo.lang.isString(func)){
func=_1bc[func];
}
for(var x=2;x<arguments.length;x++){
_1be.push(arguments[x]);
}
var _1c0=(func["__preJoinArity"]||func.length)-_1be.length;
function gather(_1c1,_1c2,_1c3){
var _1c4=_1c3;
var _1c5=_1c2.slice(0);
for(var x=0;x<_1c1.length;x++){
_1c5.push(_1c1[x]);
}
_1c3=_1c3-_1c1.length;
if(_1c3<=0){
var res=func.apply(_1bc,_1c5);
_1c3=_1c4;
return res;
}else{
return function(){
return gather(arguments,_1c5,_1c3);
};
}
}
return gather([],_1be,_1c0);
};
dojo.lang.curryArguments=function(_1c8,func,args,_1cb){
var _1cc=[];
var x=_1cb||0;
for(x=_1cb;x<args.length;x++){
_1cc.push(args[x]);
}
return dojo.lang.curry.apply(dojo.lang,[_1c8,func].concat(_1cc));
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
dojo.lang.delayThese=function(farr,cb,_1d2,_1d3){
if(!farr.length){
if(typeof _1d3=="function"){
_1d3();
}
return;
}
if((typeof _1d2=="undefined")&&(typeof cb=="number")){
_1d2=cb;
cb=function(){
};
}else{
if(!cb){
cb=function(){
};
if(!_1d2){
_1d2=0;
}
}
}
setTimeout(function(){
(farr.shift())();
cb();
dojo.lang.delayThese(farr,cb,_1d2,_1d3);
},_1d2);
};
dojo.provide("dojo.string.extras");
dojo.string.substituteParams=function(_1d4,hash){
var map=(typeof hash=="object")?hash:dojo.lang.toArray(arguments,1);
return _1d4.replace(/\%\{(\w+)\}/g,function(_1d7,key){
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
var _1da=str.split(" ");
for(var i=0;i<_1da.length;i++){
_1da[i]=_1da[i].charAt(0).toUpperCase()+_1da[i].substring(1);
}
return _1da.join(" ");
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
var _1df=escape(str);
var _1e0,re=/%u([0-9A-F]{4})/i;
while((_1e0=_1df.match(re))){
var num=Number("0x"+_1e0[1]);
var _1e3=escape("&#"+num+";");
ret+=_1df.substring(0,_1e0.index)+_1e3;
_1df=_1df.substring(_1e0.index+_1e0[0].length);
}
ret+=_1df.replace(/\+/g,"%2B");
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
dojo.string.escapeXml=function(str,_1e8){
str=str.replace(/&/gm,"&amp;").replace(/</gm,"&lt;").replace(/>/gm,"&gt;").replace(/"/gm,"&quot;");
if(!_1e8){
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
dojo.string.endsWith=function(str,end,_1f1){
if(_1f1){
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
dojo.string.startsWith=function(str,_1f5,_1f6){
if(_1f6){
str=str.toLowerCase();
_1f5=_1f5.toLowerCase();
}
return str.indexOf(_1f5)==0;
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
dojo.string.normalizeNewlines=function(text,_1fc){
if(_1fc=="\n"){
text=text.replace(/\r\n/g,"\n");
text=text.replace(/\r/g,"\n");
}else{
if(_1fc=="\r"){
text=text.replace(/\r\n/g,"\r");
text=text.replace(/\n/g,"\r");
}else{
text=text.replace(/([^\r])\n/g,"$1\r\n").replace(/\r([^\n])/g,"\r\n$1");
}
}
return text;
};
dojo.string.splitEscaped=function(str,_1fe){
var _1ff=[];
for(var i=0,_201=0;i<str.length;i++){
if(str.charAt(i)=="\\"){
i++;
continue;
}
if(str.charAt(i)==_1fe){
_1ff.push(str.substring(_201,i));
_201=i+1;
}
}
_1ff.push(str.substr(_201));
return _1ff;
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
catch(e){
}
}else{
return wh&&!isNaN(wh.nodeType);
}
};
dojo.dom.getUniqueId=function(){
var _203=dojo.doc();
do{
var id="dj_unique_"+(++arguments.callee._idIncrement);
}while(_203.getElementById(id));
return id;
};
dojo.dom.getUniqueId._idIncrement=0;
dojo.dom.firstElement=dojo.dom.getFirstChildElement=function(_205,_206){
var node=_205.firstChild;
while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE){
node=node.nextSibling;
}
if(_206&&node&&node.tagName&&node.tagName.toLowerCase()!=_206.toLowerCase()){
node=dojo.dom.nextElement(node,_206);
}
return node;
};
dojo.dom.lastElement=dojo.dom.getLastChildElement=function(_208,_209){
var node=_208.lastChild;
while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE){
node=node.previousSibling;
}
if(_209&&node&&node.tagName&&node.tagName.toLowerCase()!=_209.toLowerCase()){
node=dojo.dom.prevElement(node,_209);
}
return node;
};
dojo.dom.nextElement=dojo.dom.getNextSiblingElement=function(node,_20c){
if(!node){
return null;
}
do{
node=node.nextSibling;
}while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE);
if(node&&_20c&&_20c.toLowerCase()!=node.tagName.toLowerCase()){
return dojo.dom.nextElement(node,_20c);
}
return node;
};
dojo.dom.prevElement=dojo.dom.getPreviousSiblingElement=function(node,_20e){
if(!node){
return null;
}
if(_20e){
_20e=_20e.toLowerCase();
}
do{
node=node.previousSibling;
}while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE);
if(node&&_20e&&_20e.toLowerCase()!=node.tagName.toLowerCase()){
return dojo.dom.prevElement(node,_20e);
}
return node;
};
dojo.dom.moveChildren=function(_20f,_210,trim){
var _212=0;
if(trim){
while(_20f.hasChildNodes()&&_20f.firstChild.nodeType==dojo.dom.TEXT_NODE){
_20f.removeChild(_20f.firstChild);
}
while(_20f.hasChildNodes()&&_20f.lastChild.nodeType==dojo.dom.TEXT_NODE){
_20f.removeChild(_20f.lastChild);
}
}
while(_20f.hasChildNodes()){
_210.appendChild(_20f.firstChild);
_212++;
}
return _212;
};
dojo.dom.copyChildren=function(_213,_214,trim){
var _216=_213.cloneNode(true);
return this.moveChildren(_216,_214,trim);
};
dojo.dom.replaceChildren=function(node,_218){
var _219=[];
if(dojo.render.html.ie){
for(var i=0;i<node.childNodes.length;i++){
_219.push(node.childNodes[i]);
}
}
dojo.dom.removeChildren(node);
node.appendChild(_218);
for(var i=0;i<_219.length;i++){
dojo.dom.destroyNode(_219[i]);
}
};
dojo.dom.removeChildren=function(node){
var _21c=node.childNodes.length;
while(node.hasChildNodes()){
dojo.dom.removeNode(node.firstChild);
}
return _21c;
};
dojo.dom.replaceNode=function(node,_21e){
return node.parentNode.replaceChild(_21e,node);
};
dojo.dom.destroyNode=function(node){
if(node.parentNode){
node=dojo.dom.removeNode(node);
}
if(node.nodeType!=3){
if(dojo.evalObjPath("dojo.event.browser.clean",false)){
dojo.event.browser.clean(node);
}
if(dojo.render.html.ie){
node.outerHTML="";
}
}
};
dojo.dom.removeNode=function(node){
if(node&&node.parentNode){
return node.parentNode.removeChild(node);
}
};
dojo.dom.getAncestors=function(node,_222,_223){
var _224=[];
var _225=(_222&&(_222 instanceof Function||typeof _222=="function"));
while(node){
if(!_225||_222(node)){
_224.push(node);
}
if(_223&&_224.length>0){
return _224[0];
}
node=node.parentNode;
}
if(_223){
return null;
}
return _224;
};
dojo.dom.getAncestorsByTag=function(node,tag,_228){
tag=tag.toLowerCase();
return dojo.dom.getAncestors(node,function(el){
return ((el.tagName)&&(el.tagName.toLowerCase()==tag));
},_228);
};
dojo.dom.getFirstAncestorByTag=function(node,tag){
return dojo.dom.getAncestorsByTag(node,tag,true);
};
dojo.dom.isDescendantOf=function(node,_22d,_22e){
if(_22e&&node){
node=node.parentNode;
}
while(node){
if(node==_22d){
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
var _231=dojo.doc();
if(!dj_undef("ActiveXObject")){
var _232=["MSXML2","Microsoft","MSXML","MSXML3"];
for(var i=0;i<_232.length;i++){
try{
doc=new ActiveXObject(_232[i]+".XMLDOM");
}
catch(e){
}
if(doc){
break;
}
}
}else{
if((_231.implementation)&&(_231.implementation.createDocument)){
doc=_231.implementation.createDocument("","",null);
}
}
return doc;
};
dojo.dom.createDocumentFromText=function(str,_235){
if(!_235){
_235="text/xml";
}
if(!dj_undef("DOMParser")){
var _236=new DOMParser();
return _236.parseFromString(str,_235);
}else{
if(!dj_undef("ActiveXObject")){
var _237=dojo.dom.createDocument();
if(_237){
_237.async=false;
_237.loadXML(str);
return _237;
}else{
dojo.debug("toXml didn't work?");
}
}else{
var _238=dojo.doc();
if(_238.createElement){
var tmp=_238.createElement("xml");
tmp.innerHTML=str;
if(_238.implementation&&_238.implementation.createDocument){
var _23a=_238.implementation.createDocument("foo","",null);
for(var i=0;i<tmp.childNodes.length;i++){
_23a.importNode(tmp.childNodes.item(i),true);
}
return _23a;
}
return ((tmp.document)&&(tmp.document.firstChild?tmp.document.firstChild:tmp));
}
}
}
return null;
};
dojo.dom.prependChild=function(node,_23d){
if(_23d.firstChild){
_23d.insertBefore(node,_23d.firstChild);
}else{
_23d.appendChild(node);
}
return true;
};
dojo.dom.insertBefore=function(node,ref,_240){
if((_240!=true)&&(node===ref||node.nextSibling===ref)){
return false;
}
var _241=ref.parentNode;
_241.insertBefore(node,ref);
return true;
};
dojo.dom.insertAfter=function(node,ref,_244){
var pn=ref.parentNode;
if(ref==pn.lastChild){
if((_244!=true)&&(node===ref)){
return false;
}
pn.appendChild(node);
}else{
return this.insertBefore(node,ref.nextSibling,_244);
}
return true;
};
dojo.dom.insertAtPosition=function(node,ref,_248){
if((!node)||(!ref)||(!_248)){
return false;
}
switch(_248.toLowerCase()){
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
dojo.dom.insertAtIndex=function(node,_24a,_24b){
var _24c=_24a.childNodes;
if(!_24c.length||_24c.length==_24b){
_24a.appendChild(node);
return true;
}
if(_24b==0){
return dojo.dom.prependChild(node,_24a);
}
return dojo.dom.insertAfter(node,_24c[_24b-1]);
};
dojo.dom.textContent=function(node,text){
if(arguments.length>1){
var _24f=dojo.doc();
dojo.dom.replaceChildren(node,_24f.createTextNode(text));
return text;
}else{
if(node.textContent!=undefined){
return node.textContent;
}
var _250="";
if(node==null){
return _250;
}
for(var i=0;i<node.childNodes.length;i++){
switch(node.childNodes[i].nodeType){
case 1:
case 5:
_250+=dojo.dom.textContent(node.childNodes[i]);
break;
case 3:
case 2:
case 4:
_250+=node.childNodes[i].nodeValue;
break;
default:
break;
}
}
return _250;
}
};
dojo.dom.hasParent=function(node){
return Boolean(node&&node.parentNode&&dojo.dom.isNode(node.parentNode));
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
dojo.dom.setAttributeNS=function(elem,_256,_257,_258){
if(elem==null||((elem==undefined)&&(typeof elem=="undefined"))){
dojo.raise("No element given to dojo.dom.setAttributeNS");
}
if(!((elem.setAttributeNS==undefined)&&(typeof elem.setAttributeNS=="undefined"))){
elem.setAttributeNS(_256,_257,_258);
}else{
var _259=elem.ownerDocument;
var _25a=_259.createNode(2,_257,_256);
_25a.nodeValue=_258;
elem.setAttributeNode(_25a);
}
};
dojo.provide("dojo.undo.browser");
try{
if((!djConfig["preventBackButtonFix"])&&(!dojo.hostenv.post_load_)){
document.write("<iframe style='border: 0px; width: 1px; height: 1px; position: absolute; bottom: 0px; right: 0px; visibility: visible;' name='djhistory' id='djhistory' src='"+(djConfig["dojoIframeHistoryUrl"]||dojo.hostenv.getBaseScriptUri()+"iframe_history.html")+"'></iframe>");
}
}
catch(e){
}
if(dojo.render.html.opera){
dojo.debug("Opera is not supported with dojo.undo.browser, so back/forward detection will not work.");
}
dojo.undo.browser={initialHref:(!dj_undef("window"))?window.location.href:"",initialHash:(!dj_undef("window"))?window.location.hash:"",moveForward:false,historyStack:[],forwardStack:[],historyIframe:null,bookmarkAnchor:null,locationTimer:null,setInitialState:function(args){
this.initialState=this._createState(this.initialHref,args,this.initialHash);
},addToHistory:function(args){
this.forwardStack=[];
var hash=null;
var url=null;
if(!this.historyIframe){
if(djConfig["useXDomain"]&&!djConfig["dojoIframeHistoryUrl"]){
dojo.debug("dojo.undo.browser: When using cross-domain Dojo builds,"+" please save iframe_history.html to your domain and set djConfig.dojoIframeHistoryUrl"+" to the path on your domain to iframe_history.html");
}
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
var _25f=args["back"]||args["backButton"]||args["handle"];
var tcb=function(_261){
if(window.location.hash!=""){
setTimeout("window.location.href = '"+hash+"';",1);
}
_25f.apply(this,[_261]);
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
var _262=args["forward"]||args["forwardButton"]||args["handle"];
var tfw=function(_264){
if(window.location.hash!=""){
window.location.href=hash;
}
if(_262){
_262.apply(this,[_264]);
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
},iframeLoaded:function(evt,_267){
if(!dojo.render.html.opera){
var _268=this._getUrlQuery(_267.href);
if(_268==null){
if(this.historyStack.length==1){
this.handleBackButton();
}
return;
}
if(this.moveForward){
this.moveForward=false;
return;
}
if(this.historyStack.length>=2&&_268==this._getUrlQuery(this.historyStack[this.historyStack.length-2].url)){
this.handleBackButton();
}else{
if(this.forwardStack.length>0&&_268==this._getUrlQuery(this.forwardStack[this.forwardStack.length-1].url)){
this.handleForwardButton();
}
}
}
},handleBackButton:function(){
var _269=this.historyStack.pop();
if(!_269){
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
this.forwardStack.push(_269);
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
var _270=url.split("?");
if(_270.length<2){
return null;
}else{
return _270[1];
}
},_loadIframeHistory:function(){
var url=(djConfig["dojoIframeHistoryUrl"]||dojo.hostenv.getBaseScriptUri()+"iframe_history.html")+"?"+(new Date()).getTime();
this.moveForward=true;
dojo.io.setIFrameSrc(this.historyIframe,url,false);
return url;
}};
dojo.provide("dojo.io.BrowserIO");
if(!dj_undef("window")){
dojo.io.checkChildrenForFile=function(node){
var _273=false;
var _274=node.getElementsByTagName("input");
dojo.lang.forEach(_274,function(_275){
if(_273){
return;
}
if(_275.getAttribute("type")=="file"){
_273=true;
}
});
return _273;
};
dojo.io.formHasFile=function(_276){
return dojo.io.checkChildrenForFile(_276);
};
dojo.io.updateNode=function(node,_278){
node=dojo.byId(node);
var args=_278;
if(dojo.lang.isString(_278)){
args={url:_278};
}
args.mimetype="text/html";
args.load=function(t,d,e){
while(node.firstChild){
dojo.dom.destroyNode(node.firstChild);
}
node.innerHTML=d;
};
dojo.io.bind(args);
};
dojo.io._getAttribute=function(node,key){
var _27f=node.getAttributeNode(key);
if(_27f){
return _27f.value;
}
return null;
};
dojo.io.formFilter=function(node){
var type=(node.type||"").toLowerCase();
return !node.disabled&&node.name&&!dojo.lang.inArray(["file","submit","image","reset","button"],type);
};
dojo.io.encodeForm=function(_282,_283,_284){
if((!_282)||(!_282.tagName)||(!_282.tagName.toLowerCase()=="form")){
dojo.raise("Attempted to encode a non-form element.");
}
if(!_284){
_284=dojo.io.formFilter;
}
var enc=/utf/i.test(_283||"")?encodeURIComponent:dojo.string.encodeAscii;
var _286=[];
for(var i=0;i<_282.elements.length;i++){
var elm=_282.elements[i];
if(!elm||elm.tagName.toLowerCase()=="fieldset"||!_284(elm)){
continue;
}
var name=enc(elm.name);
var type=elm.type.toLowerCase();
if(type=="select-multiple"){
for(var j=0;j<elm.options.length;j++){
if(elm.options[j].selected){
_286.push(name+"="+enc(elm.options[j].value));
}
}
}else{
if(dojo.lang.inArray(["radio","checkbox"],type)){
if(elm.checked){
_286.push(name+"="+enc(elm.value));
}
}else{
_286.push(name+"="+enc(elm.value));
}
}
}
var _28c=_282.getElementsByTagName("input");
for(var i=0;i<_28c.length;i++){
var _28d=_28c[i];
if(_28d.type.toLowerCase()=="image"&&_28d.form==_282&&_284(_28d)){
var name=enc(_28d.name);
_286.push(name+"="+enc(_28d.value));
_286.push(name+".x=0");
_286.push(name+".y=0");
}
}
return _286.join("&")+"&";
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
var _293=form.getElementsByTagName("input");
for(var i=0;i<_293.length;i++){
var _294=_293[i];
if(_294.type.toLowerCase()=="image"&&_294.form==form){
this.connect(_294,"onclick","click");
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
var _29b=false;
if(node.disabled||!node.name){
_29b=false;
}else{
if(dojo.lang.inArray(["submit","button","image"],type)){
if(!this.clickedButton){
this.clickedButton=node;
}
_29b=node==this.clickedButton;
}else{
_29b=!dojo.lang.inArray(["file","submit","reset","button"],type);
}
}
return _29b;
},connect:function(_29c,_29d,_29e){
if(dojo.evalObjPath("dojo.event.connect")){
dojo.event.connect(_29c,_29d,this,_29e);
}else{
var fcn=dojo.lang.hitch(this,_29e);
_29c[_29d]=function(e){
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
var _2a1=this;
var _2a2={};
this.useCache=false;
this.preventCache=false;
function getCacheKey(url,_2a4,_2a5){
return url+"|"+_2a4+"|"+_2a5.toLowerCase();
}
function addToCache(url,_2a7,_2a8,http){
_2a2[getCacheKey(url,_2a7,_2a8)]=http;
}
function getFromCache(url,_2ab,_2ac){
return _2a2[getCacheKey(url,_2ab,_2ac)];
}
this.clearCache=function(){
_2a2={};
};
function doLoad(_2ad,http,url,_2b0,_2b1){
if(((http.status>=200)&&(http.status<300))||(http.status==304)||(http.status==1223)||(location.protocol=="file:"&&(http.status==0||http.status==undefined))||(location.protocol=="chrome:"&&(http.status==0||http.status==undefined))){
var ret;
if(_2ad.method.toLowerCase()=="head"){
var _2b3=http.getAllResponseHeaders();
ret={};
ret.toString=function(){
return _2b3;
};
var _2b4=_2b3.split(/[\r\n]+/g);
for(var i=0;i<_2b4.length;i++){
var pair=_2b4[i].match(/^([^:]+)\s*:\s*(.+)$/i);
if(pair){
ret[pair[1]]=pair[2];
}
}
}else{
if(_2ad.mimetype=="text/javascript"){
try{
ret=dj_eval(http.responseText);
}
catch(e){
dojo.debug(e);
dojo.debug(http.responseText);
ret=null;
}
}else{
if(_2ad.mimetype.substr(0,9)=="text/json"||_2ad.mimetype.substr(0,16)=="application/json"){
try{
ret=dj_eval("("+_2ad.jsonFilter(http.responseText)+")");
}
catch(e){
dojo.debug(e);
dojo.debug(http.responseText);
ret=false;
}
}else{
if((_2ad.mimetype=="application/xml")||(_2ad.mimetype=="text/xml")){
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
if(_2b1){
addToCache(url,_2b0,_2ad.method,http);
}
_2ad[(typeof _2ad.load=="function")?"load":"handle"]("load",ret,http,_2ad);
}else{
var _2b7=new dojo.io.Error("XMLHttpTransport Error: "+http.status+" "+http.statusText);
_2ad[(typeof _2ad.error=="function")?"error":"handle"]("error",_2b7,http,_2ad);
}
}
function setHeaders(http,_2b9){
if(_2b9["headers"]){
for(var _2ba in _2b9["headers"]){
if(_2ba.toLowerCase()=="content-type"&&!_2b9["contentType"]){
_2b9["contentType"]=_2b9["headers"][_2ba];
}else{
http.setRequestHeader(_2ba,_2b9["headers"][_2ba]);
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
if(!dojo.hostenv._blockAsync&&!_2a1._blockAsync){
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
var _2be=new dojo.io.Error("XMLHttpTransport.watchInFlight Error: "+e);
tif.req[(typeof tif.req.error=="function")?"error":"handle"]("error",_2be,tif.http,tif.req);
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
var _2bf=dojo.hostenv.getXmlhttpObject()?true:false;
this.canHandle=function(_2c0){
var mlc=_2c0["mimetype"].toLowerCase()||"";
return _2bf&&((dojo.lang.inArray(["text/plain","text/html","application/xml","text/xml","text/javascript"],mlc))||(mlc.substr(0,9)=="text/json"||mlc.substr(0,16)=="application/json"))&&!(_2c0["formNode"]&&dojo.io.formHasFile(_2c0["formNode"]));
};
this.multipartBoundary="45309FFF-BD65-4d50-99C9-36986896A96F";
this.bind=function(_2c2){
if(!_2c2["url"]){
if(!_2c2["formNode"]&&(_2c2["backButton"]||_2c2["back"]||_2c2["changeUrl"]||_2c2["watchForURL"])&&(!djConfig.preventBackButtonFix)){
dojo.deprecated("Using dojo.io.XMLHTTPTransport.bind() to add to browser history without doing an IO request","Use dojo.undo.browser.addToHistory() instead.","0.4");
dojo.undo.browser.addToHistory(_2c2);
return true;
}
}
var url=_2c2.url;
var _2c4="";
if(_2c2["formNode"]){
var ta=dojo.io._getAttribute(_2c2.formNode,"action");
if((ta)&&(!_2c2["url"])){
url=ta;
}
var tp=dojo.io._getAttribute(_2c2.formNode,"method");
if((tp)&&(!_2c2["method"])){
_2c2.method=tp;
}
_2c4+=dojo.io.encodeForm(_2c2.formNode,_2c2.encoding,_2c2["formFilter"]);
}
if(url.indexOf("#")>-1){
dojo.debug("Warning: dojo.io.bind: stripping hash values from url:",url);
url=url.split("#")[0];
}
if(_2c2["file"]){
_2c2.method="post";
}
if(!_2c2["method"]){
_2c2.method="get";
}
if(_2c2.method.toLowerCase()=="get"){
_2c2.multipart=false;
}else{
if(_2c2["file"]){
_2c2.multipart=true;
}else{
if(!_2c2["multipart"]){
_2c2.multipart=false;
}
}
}
if(_2c2["backButton"]||_2c2["back"]||_2c2["changeUrl"]){
dojo.undo.browser.addToHistory(_2c2);
}
var _2c7=_2c2["content"]||{};
if(_2c2.sendTransport){
_2c7["dojo.transport"]="xmlhttp";
}
do{
if(_2c2.postContent){
_2c4=_2c2.postContent;
break;
}
if(_2c7){
_2c4+=dojo.io.argsFromMap(_2c7,_2c2.encoding);
}
if(_2c2.method.toLowerCase()=="get"||!_2c2.multipart){
break;
}
var t=[];
if(_2c4.length){
var q=_2c4.split("&");
for(var i=0;i<q.length;++i){
if(q[i].length){
var p=q[i].split("=");
t.push("--"+this.multipartBoundary,"Content-Disposition: form-data; name=\""+p[0]+"\"","",p[1]);
}
}
}
if(_2c2.file){
if(dojo.lang.isArray(_2c2.file)){
for(var i=0;i<_2c2.file.length;++i){
var o=_2c2.file[i];
t.push("--"+this.multipartBoundary,"Content-Disposition: form-data; name=\""+o.name+"\"; filename=\""+("fileName" in o?o.fileName:o.name)+"\"","Content-Type: "+("contentType" in o?o.contentType:"application/octet-stream"),"",o.content);
}
}else{
var o=_2c2.file;
t.push("--"+this.multipartBoundary,"Content-Disposition: form-data; name=\""+o.name+"\"; filename=\""+("fileName" in o?o.fileName:o.name)+"\"","Content-Type: "+("contentType" in o?o.contentType:"application/octet-stream"),"",o.content);
}
}
if(t.length){
t.push("--"+this.multipartBoundary+"--","");
_2c4=t.join("\r\n");
}
}while(false);
var _2cd=_2c2["sync"]?false:true;
var _2ce=_2c2["preventCache"]||(this.preventCache==true&&_2c2["preventCache"]!=false);
var _2cf=_2c2["useCache"]==true||(this.useCache==true&&_2c2["useCache"]!=false);
if(!_2ce&&_2cf){
var _2d0=getFromCache(url,_2c4,_2c2.method);
if(_2d0){
doLoad(_2c2,_2d0,url,_2c4,false);
return;
}
}
var http=dojo.hostenv.getXmlhttpObject(_2c2);
var _2d2=false;
if(_2cd){
var _2d3=this.inFlight.push({"req":_2c2,"http":http,"url":url,"query":_2c4,"useCache":_2cf,"startTime":_2c2.timeoutSeconds?(new Date()).getTime():0});
this.startWatchingInFlight();
}else{
_2a1._blockAsync=true;
}
if(_2c2.method.toLowerCase()=="post"){
if(!_2c2.user){
http.open("POST",url,_2cd);
}else{
http.open("POST",url,_2cd,_2c2.user,_2c2.password);
}
setHeaders(http,_2c2);
http.setRequestHeader("Content-Type",_2c2.multipart?("multipart/form-data; boundary="+this.multipartBoundary):(_2c2.contentType||"application/x-www-form-urlencoded"));
try{
http.send(_2c4);
}
catch(e){
if(typeof http.abort=="function"){
http.abort();
}
doLoad(_2c2,{status:404},url,_2c4,_2cf);
}
}else{
var _2d4=url;
if(_2c4!=""){
_2d4+=(_2d4.indexOf("?")>-1?"&":"?")+_2c4;
}
if(_2ce){
_2d4+=(dojo.string.endsWithAny(_2d4,"?","&")?"":(_2d4.indexOf("?")>-1?"&":"?"))+"dojo.preventCache="+new Date().valueOf();
}
if(!_2c2.user){
http.open(_2c2.method.toUpperCase(),_2d4,_2cd);
}else{
http.open(_2c2.method.toUpperCase(),_2d4,_2cd,_2c2.user,_2c2.password);
}
setHeaders(http,_2c2);
try{
http.send(null);
}
catch(e){
if(typeof http.abort=="function"){
http.abort();
}
doLoad(_2c2,{status:404},url,_2c4,_2cf);
}
}
if(!_2cd){
doLoad(_2c2,http,url,_2c4,_2cf);
_2a1._blockAsync=false;
}
_2c2.abort=function(){
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
}
dojo.provide("dojo.io.cookie");
dojo.io.cookie.setCookie=function(name,_2d6,days,path,_2d9,_2da){
var _2db=-1;
if((typeof days=="number")&&(days>=0)){
var d=new Date();
d.setTime(d.getTime()+(days*24*60*60*1000));
_2db=d.toGMTString();
}
_2d6=escape(_2d6);
document.cookie=name+"="+_2d6+";"+(_2db!=-1?" expires="+_2db+";":"")+(path?"path="+path:"")+(_2d9?"; domain="+_2d9:"")+(_2da?"; secure":"");
};
dojo.io.cookie.set=dojo.io.cookie.setCookie;
dojo.io.cookie.getCookie=function(name){
var idx=document.cookie.lastIndexOf(name+"=");
if(idx==-1){
return null;
}
var _2df=document.cookie.substring(idx+name.length+1);
var end=_2df.indexOf(";");
if(end==-1){
end=_2df.length;
}
_2df=_2df.substring(0,end);
_2df=unescape(_2df);
return _2df;
};
dojo.io.cookie.get=dojo.io.cookie.getCookie;
dojo.io.cookie.deleteCookie=function(name){
dojo.io.cookie.setCookie(name,"-",0);
};
dojo.io.cookie.setObjectCookie=function(name,obj,days,path,_2e6,_2e7,_2e8){
if(arguments.length==5){
_2e8=_2e6;
_2e6=null;
_2e7=null;
}
var _2e9=[],_2ea,_2eb="";
if(!_2e8){
_2ea=dojo.io.cookie.getObjectCookie(name);
}
if(days>=0){
if(!_2ea){
_2ea={};
}
for(var prop in obj){
if(obj[prop]==null){
delete _2ea[prop];
}else{
if((typeof obj[prop]=="string")||(typeof obj[prop]=="number")){
_2ea[prop]=obj[prop];
}
}
}
prop=null;
for(var prop in _2ea){
_2e9.push(escape(prop)+"="+escape(_2ea[prop]));
}
_2eb=_2e9.join("&");
}
dojo.io.cookie.setCookie(name,_2eb,days,path,_2e6,_2e7);
};
dojo.io.cookie.getObjectCookie=function(name){
var _2ee=null,_2ef=dojo.io.cookie.getCookie(name);
if(_2ef){
_2ee={};
var _2f0=_2ef.split("&");
for(var i=0;i<_2f0.length;i++){
var pair=_2f0[i].split("=");
var _2f3=pair[1];
if(isNaN(_2f3)){
_2f3=unescape(pair[1]);
}
_2ee[unescape(pair[0])]=_2f3;
}
}
return _2ee;
};
dojo.io.cookie.isSupported=function(){
if(typeof navigator.cookieEnabled!="boolean"){
dojo.io.cookie.setCookie("__TestingYourBrowserForCookieSupport__","CookiesAllowed",90,null);
var _2f4=dojo.io.cookie.getCookie("__TestingYourBrowserForCookieSupport__");
navigator.cookieEnabled=(_2f4=="CookiesAllowed");
if(navigator.cookieEnabled){
this.deleteCookie("__TestingYourBrowserForCookieSupport__");
}
}
return navigator.cookieEnabled;
};
if(!dojo.io.cookies){
dojo.io.cookies=dojo.io.cookie;
}
dojo.kwCompoundRequire({common:["dojo.io.common"],rhino:["dojo.io.RhinoIO"],browser:["dojo.io.BrowserIO","dojo.io.cookie"],dashboard:["dojo.io.BrowserIO","dojo.io.cookie"]});
dojo.provide("dojo.io.*");
dojo.provide("dojo.io");
dojo.deprecated("dojo.io","replaced by dojo.io.*","0.5");
dojo.provide("dojo.event.common");
dojo.event=new function(){
this._canTimeout=dojo.lang.isFunction(dj_global["setTimeout"])||dojo.lang.isAlien(dj_global["setTimeout"]);
function interpolateArgs(args,_2f6){
var dl=dojo.lang;
var ao={srcObj:dj_global,srcFunc:null,adviceObj:dj_global,adviceFunc:null,aroundObj:null,aroundFunc:null,adviceType:(args.length>2)?args[0]:"after",precedence:"last",once:false,delay:null,rate:0,adviceMsg:false,maxCalls:-1};
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
var _2f9=dl.nameAnonFunc(args[2],ao.adviceObj,_2f6);
ao.adviceFunc=_2f9;
}else{
if((dl.isFunction(args[0]))&&(dl.isObject(args[1]))&&(dl.isString(args[2]))){
ao.adviceType="after";
ao.srcObj=dj_global;
var _2f9=dl.nameAnonFunc(args[0],ao.srcObj,_2f6);
ao.srcFunc=_2f9;
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
var _2f9=dl.nameAnonFunc(args[1],dj_global,_2f6);
ao.srcFunc=_2f9;
ao.adviceObj=args[2];
ao.adviceFunc=args[3];
}else{
if((dl.isString(args[0]))&&(dl.isObject(args[1]))&&(dl.isString(args[2]))&&(dl.isFunction(args[3]))){
ao.srcObj=args[1];
ao.srcFunc=args[2];
var _2f9=dl.nameAnonFunc(args[3],dj_global,_2f6);
ao.adviceObj=dj_global;
ao.adviceFunc=_2f9;
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
ao.maxCalls=(!isNaN(parseInt(args[11])))?args[11]:-1;
break;
}
if(dl.isFunction(ao.aroundFunc)){
var _2f9=dl.nameAnonFunc(ao.aroundFunc,ao.aroundObj,_2f6);
ao.aroundFunc=_2f9;
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
var _2fb={};
for(var x in ao){
_2fb[x]=ao[x];
}
var mjps=[];
dojo.lang.forEach(ao.srcObj,function(src){
if((dojo.render.html.capable)&&(dojo.lang.isString(src))){
src=dojo.byId(src);
}
_2fb.srcObj=src;
mjps.push(dojo.event.connect.call(dojo.event,_2fb));
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
var _303;
if((arguments.length==1)&&(typeof a1=="object")){
_303=a1;
}else{
_303={srcObj:a1,srcFunc:a2};
}
_303.adviceFunc=function(){
var _304=[];
for(var x=0;x<arguments.length;x++){
_304.push(arguments[x]);
}
dojo.debug("("+_303.srcObj+")."+_303.srcFunc,":",_304.join(", "));
};
this.kwConnect(_303);
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
this.connectRunOnce=function(){
var ao=interpolateArgs(arguments,true);
ao.maxCalls=1;
return this.connect(ao);
};
this._kwConnectImpl=function(_30c,_30d){
var fn=(_30d)?"disconnect":"connect";
if(typeof _30c["srcFunc"]=="function"){
_30c.srcObj=_30c["srcObj"]||dj_global;
var _30f=dojo.lang.nameAnonFunc(_30c.srcFunc,_30c.srcObj,true);
_30c.srcFunc=_30f;
}
if(typeof _30c["adviceFunc"]=="function"){
_30c.adviceObj=_30c["adviceObj"]||dj_global;
var _30f=dojo.lang.nameAnonFunc(_30c.adviceFunc,_30c.adviceObj,true);
_30c.adviceFunc=_30f;
}
_30c.srcObj=_30c["srcObj"]||dj_global;
_30c.adviceObj=_30c["adviceObj"]||_30c["targetObj"]||dj_global;
_30c.adviceFunc=_30c["adviceFunc"]||_30c["targetFunc"];
return dojo.event[fn](_30c);
};
this.kwConnect=function(_310){
return this._kwConnectImpl(_310,false);
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
if(!ao.srcObj[ao.srcFunc]){
return null;
}
var mjp=dojo.event.MethodJoinPoint.getForMethod(ao.srcObj,ao.srcFunc,true);
mjp.removeAdvice(ao.adviceObj,ao.adviceFunc,ao.adviceType,ao.once);
return mjp;
};
this.kwDisconnect=function(_313){
return this._kwConnectImpl(_313,true);
};
};
dojo.event.MethodInvocation=function(_314,obj,args){
this.jp_=_314;
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
dojo.event.MethodJoinPoint=function(obj,_31c){
this.object=obj||dj_global;
this.methodname=_31c;
this.methodfunc=this.object[_31c];
this.squelch=false;
};
dojo.event.MethodJoinPoint.getForMethod=function(obj,_31e){
if(!obj){
obj=dj_global;
}
var ofn=obj[_31e];
if(!ofn){
ofn=obj[_31e]=function(){
};
if(!obj[_31e]){
dojo.raise("Cannot set do-nothing method on that object "+_31e);
}
}else{
if((typeof ofn!="function")&&(!dojo.lang.isFunction(ofn))&&(!dojo.lang.isAlien(ofn))){
return null;
}
}
var _320=_31e+"$joinpoint";
var _321=_31e+"$joinpoint$method";
var _322=obj[_320];
if(!_322){
var _323=false;
if(dojo.event["browser"]){
if((obj["attachEvent"])||(obj["nodeType"])||(obj["addEventListener"])){
_323=true;
dojo.event.browser.addClobberNodeAttrs(obj,[_320,_321,_31e]);
}
}
var _324=ofn.length;
obj[_321]=ofn;
_322=obj[_320]=new dojo.event.MethodJoinPoint(obj,_321);
if(!_323){
obj[_31e]=function(){
return _322.run.apply(_322,arguments);
};
}else{
obj[_31e]=function(){
var args=[];
if(!arguments.length){
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
if((x==0)&&(dojo.event.browser.isEvent(arguments[x]))){
args.push(dojo.event.browser.fixEvent(arguments[x],this));
}else{
args.push(arguments[x]);
}
}
}
return _322.run.apply(_322,args);
};
}
obj[_31e].__preJoinArity=_324;
}
return _322;
};
dojo.lang.extend(dojo.event.MethodJoinPoint,{squelch:false,unintercept:function(){
this.object[this.methodname]=this.methodfunc;
this.before=[];
this.after=[];
this.around=[];
},disconnect:dojo.lang.forward("unintercept"),run:function(){
var obj=this.object||dj_global;
var args=arguments;
var _32a=[];
for(var x=0;x<args.length;x++){
_32a[x]=args[x];
}
var _32c=function(marr){
if(!marr){
dojo.debug("Null argument to unrollAdvice()");
return;
}
var _32e=marr[0]||dj_global;
var _32f=marr[1];
if(!_32e[_32f]){
dojo.raise("function \""+_32f+"\" does not exist on \""+_32e+"\"");
}
var _330=marr[2]||dj_global;
var _331=marr[3];
var msg=marr[6];
var _333=marr[7];
if(_333>-1){
if(_333==0){
return;
}
marr[7]--;
}
var _334;
var to={args:[],jp_:this,object:obj,proceed:function(){
return _32e[_32f].apply(_32e,to.args);
}};
to.args=_32a;
var _336=parseInt(marr[4]);
var _337=((!isNaN(_336))&&(marr[4]!==null)&&(typeof marr[4]!="undefined"));
if(marr[5]){
var rate=parseInt(marr[5]);
var cur=new Date();
var _33a=false;
if((marr["last"])&&((cur-marr.last)<=rate)){
if(dojo.event._canTimeout){
if(marr["delayTimer"]){
clearTimeout(marr.delayTimer);
}
var tod=parseInt(rate*2);
var mcpy=dojo.lang.shallowCopy(marr);
marr.delayTimer=setTimeout(function(){
mcpy[5]=0;
_32c(mcpy);
},tod);
}
return;
}else{
marr.last=cur;
}
}
if(_331){
_330[_331].call(_330,to);
}else{
if((_337)&&((dojo.render.html)||(dojo.render.svg))){
dj_global["setTimeout"](function(){
if(msg){
_32e[_32f].call(_32e,to);
}else{
_32e[_32f].apply(_32e,args);
}
},_336);
}else{
if(msg){
_32e[_32f].call(_32e,to);
}else{
_32e[_32f].apply(_32e,args);
}
}
}
};
var _33d=function(){
if(this.squelch){
try{
return _32c.apply(this,arguments);
}
catch(e){
dojo.debug(e);
}
}else{
return _32c.apply(this,arguments);
}
};
if((this["before"])&&(this.before.length>0)){
dojo.lang.forEach(this.before.concat(new Array()),_33d);
}
var _33e;
try{
if((this["around"])&&(this.around.length>0)){
var mi=new dojo.event.MethodInvocation(this,obj,args);
_33e=mi.proceed();
}else{
if(this.methodfunc){
_33e=this.object[this.methodname].apply(this.object,args);
}
}
}
catch(e){
if(!this.squelch){
dojo.debug(e,"when calling",this.methodname,"on",this.object,"with arguments",args);
dojo.raise(e);
}
}
if((this["after"])&&(this.after.length>0)){
dojo.lang.forEach(this.after.concat(new Array()),_33d);
}
return (this.methodfunc)?_33e:null;
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
this.addAdvice(args["adviceObj"],args["adviceFunc"],args["aroundObj"],args["aroundFunc"],args["adviceType"],args["precedence"],args["once"],args["delay"],args["rate"],args["adviceMsg"],args["maxCalls"]);
},addAdvice:function(_343,_344,_345,_346,_347,_348,once,_34a,rate,_34c,_34d){
var arr=this.getArr(_347);
if(!arr){
dojo.raise("bad this: "+this);
}
var ao=[_343,_344,_345,_346,_34a,rate,_34c,_34d];
if(once){
if(this.hasAdvice(_343,_344,_347,arr)>=0){
return;
}
}
if(_348=="first"){
arr.unshift(ao);
}else{
arr.push(ao);
}
},hasAdvice:function(_350,_351,_352,arr){
if(!arr){
arr=this.getArr(_352);
}
var ind=-1;
for(var x=0;x<arr.length;x++){
var aao=(typeof _351=="object")?(new String(_351)).toString():_351;
var a1o=(typeof arr[x][1]=="object")?(new String(arr[x][1])).toString():arr[x][1];
if((arr[x][0]==_350)&&(a1o==aao)){
ind=x;
}
}
return ind;
},removeAdvice:function(_358,_359,_35a,once){
var arr=this.getArr(_35a);
var ind=this.hasAdvice(_358,_359,_35a,arr);
if(ind==-1){
return false;
}
while(ind!=-1){
arr.splice(ind,1);
if(once){
break;
}
ind=this.hasAdvice(_358,_359,_35a,arr);
}
return true;
}});
dojo.provide("dojo.event.topic");
dojo.event.topic=new function(){
this.topics={};
this.getTopic=function(_35e){
if(!this.topics[_35e]){
this.topics[_35e]=new this.TopicImpl(_35e);
}
return this.topics[_35e];
};
this.registerPublisher=function(_35f,obj,_361){
var _35f=this.getTopic(_35f);
_35f.registerPublisher(obj,_361);
};
this.subscribe=function(_362,obj,_364){
var _362=this.getTopic(_362);
_362.subscribe(obj,_364);
};
this.unsubscribe=function(_365,obj,_367){
var _365=this.getTopic(_365);
_365.unsubscribe(obj,_367);
};
this.destroy=function(_368){
this.getTopic(_368).destroy();
delete this.topics[_368];
};
this.publishApply=function(_369,args){
var _369=this.getTopic(_369);
_369.sendMessage.apply(_369,args);
};
this.publish=function(_36b,_36c){
var _36b=this.getTopic(_36b);
var args=[];
for(var x=1;x<arguments.length;x++){
args.push(arguments[x]);
}
_36b.sendMessage.apply(_36b,args);
};
};
dojo.event.topic.TopicImpl=function(_36f){
this.topicName=_36f;
this.subscribe=function(_370,_371){
var tf=_371||_370;
var to=(!_371)?dj_global:_370;
return dojo.event.kwConnect({srcObj:this,srcFunc:"sendMessage",adviceObj:to,adviceFunc:tf});
};
this.unsubscribe=function(_374,_375){
var tf=(!_375)?_374:_375;
var to=(!_375)?null:_374;
return dojo.event.kwDisconnect({srcObj:this,srcFunc:"sendMessage",adviceObj:to,adviceFunc:tf});
};
this._getJoinPoint=function(){
return dojo.event.MethodJoinPoint.getForMethod(this,"sendMessage");
};
this.setSquelch=function(_378){
this._getJoinPoint().squelch=_378;
};
this.destroy=function(){
this._getJoinPoint().disconnect();
};
this.registerPublisher=function(_379,_37a){
dojo.event.connect(_379,_37a,this,"sendMessage");
};
this.sendMessage=function(_37b){
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
this.clobber=function(_37e){
var na;
var tna;
if(_37e){
tna=_37e.all||_37e.getElementsByTagName("*");
na=[_37e];
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
var _382={};
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
if(dojo.widget){
for(var name in dojo.widget._templateCache){
if(dojo.widget._templateCache[name].node){
dojo.dom.destroyNode(dojo.widget._templateCache[name].node);
dojo.widget._templateCache[name].node=null;
delete dojo.widget._templateCache[name].node;
}
}
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
var _387=0;
this.normalizedEventName=function(_388){
switch(_388){
case "CheckboxStateChange":
case "DOMAttrModified":
case "DOMMenuItemActive":
case "DOMMenuItemInactive":
case "DOMMouseScroll":
case "DOMNodeInserted":
case "DOMNodeRemoved":
case "RadioStateChange":
return _388;
break;
default:
var lcn=_388.toLowerCase();
return (lcn.indexOf("on")==0)?lcn.substr(2):lcn;
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
this.addClobberNodeAttrs=function(node,_38d){
if(!dojo.render.html.ie){
return;
}
this.addClobberNode(node);
for(var x=0;x<_38d.length;x++){
node.__clobberAttrs__.push(_38d[x]);
}
};
this.removeListener=function(node,_390,fp,_392){
if(!_392){
var _392=false;
}
_390=dojo.event.browser.normalizedEventName(_390);
if(_390=="key"){
if(dojo.render.html.ie){
this.removeListener(node,"onkeydown",fp,_392);
}
_390="keypress";
}
if(node.removeEventListener){
node.removeEventListener(_390,fp,_392);
}
};
this.addListener=function(node,_394,fp,_396,_397){
if(!node){
return;
}
if(!_396){
var _396=false;
}
_394=dojo.event.browser.normalizedEventName(_394);
if(_394=="key"){
if(dojo.render.html.ie){
this.addListener(node,"onkeydown",fp,_396,_397);
}
_394="keypress";
}
if(!_397){
var _398=function(evt){
if(!evt){
evt=window.event;
}
var ret=fp(dojo.event.browser.fixEvent(evt,this));
if(_396){
dojo.event.browser.stopEvent(evt);
}
return ret;
};
}else{
_398=fp;
}
if(node.addEventListener){
node.addEventListener(_394,_398,_396);
return _398;
}else{
_394="on"+_394;
if(typeof node[_394]=="function"){
var _39b=node[_394];
node[_394]=function(e){
_39b(e);
return _398(e);
};
}else{
node[_394]=_398;
}
if(dojo.render.html.ie){
this.addClobberNodeAttrs(node,[_394]);
}
return _398;
}
};
this.isEvent=function(obj){
return (typeof obj!="undefined")&&(obj)&&(typeof Event!="undefined")&&(obj.eventPhase);
};
this.currentEvent=null;
this.callListener=function(_39e,_39f){
if(typeof _39e!="function"){
dojo.raise("listener not a function: "+_39e);
}
dojo.event.browser.currentEvent.currentTarget=_39f;
return _39e.call(_39f,dojo.event.browser.currentEvent);
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
this.fixEvent=function(evt,_3a2){
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
var _3a4=evt.keyCode;
if(_3a4>=65&&_3a4<=90&&evt.shiftKey==false){
_3a4+=32;
}
if(_3a4>=1&&_3a4<=26&&evt.ctrlKey){
_3a4+=96;
}
evt.key=String.fromCharCode(_3a4);
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
var _3a4=evt.which;
if((evt.ctrlKey||evt.altKey||evt.metaKey)&&(evt.which>=65&&evt.which<=90&&evt.shiftKey==false)){
_3a4+=32;
}
evt.key=String.fromCharCode(_3a4);
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
case 25:
evt.key=evt.KEY_TAB;
evt.shift=true;
break;
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
case 63236:
evt.key=evt.KEY_F1;
break;
case 63237:
evt.key=evt.KEY_F2;
break;
case 63238:
evt.key=evt.KEY_F3;
break;
case 63239:
evt.key=evt.KEY_F4;
break;
case 63240:
evt.key=evt.KEY_F5;
break;
case 63241:
evt.key=evt.KEY_F6;
break;
case 63242:
evt.key=evt.KEY_F7;
break;
case 63243:
evt.key=evt.KEY_F8;
break;
case 63244:
evt.key=evt.KEY_F9;
break;
case 63245:
evt.key=evt.KEY_F10;
break;
case 63246:
evt.key=evt.KEY_F11;
break;
case 63247:
evt.key=evt.KEY_F12;
break;
case 63250:
evt.key=evt.KEY_PAUSE;
break;
case 63272:
evt.key=evt.KEY_DELETE;
break;
case 63273:
evt.key=evt.KEY_HOME;
break;
case 63275:
evt.key=evt.KEY_END;
break;
case 63276:
evt.key=evt.KEY_PAGE_UP;
break;
case 63277:
evt.key=evt.KEY_PAGE_DOWN;
break;
case 63302:
evt.key=evt.KEY_INSERT;
break;
case 63248:
case 63249:
case 63289:
break;
default:
evt.key=evt.charCode>=evt.KEY_SPACE?String.fromCharCode(evt.charCode):evt.keyCode;
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
evt.currentTarget=(_3a2?_3a2:evt.srcElement);
}
if(!evt.layerX){
evt.layerX=evt.offsetX;
}
if(!evt.layerY){
evt.layerY=evt.offsetY;
}
var doc=(evt.srcElement&&evt.srcElement.ownerDocument)?evt.srcElement.ownerDocument:document;
var _3a6=((dojo.render.html.ie55)||(doc["compatMode"]=="BackCompat"))?doc.body:doc.documentElement;
if(!evt.pageX){
evt.pageX=evt.clientX+(_3a6.scrollLeft||0);
}
if(!evt.pageY){
evt.pageY=evt.clientY+(_3a6.scrollTop||0);
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
evt.cancelBubble=true;
evt.returnValue=false;
}else{
evt.preventDefault();
evt.stopPropagation();
}
};
};
dojo.kwCompoundRequire({common:["dojo.event.common","dojo.event.topic"],browser:["dojo.event.browser"],dashboard:["dojo.event.browser"]});
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
dojo.extend(dojo.gfx.color.Color,{toRgb:function(_3ae){
if(_3ae){
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
},blend:function(_3af,_3b0){
var rgb=null;
if(dojo.lang.isArray(_3af)){
rgb=_3af;
}else{
if(_3af instanceof dojo.gfx.color.Color){
rgb=_3af.toRgb();
}else{
rgb=new dojo.gfx.color.Color(_3af).toRgb();
}
}
return dojo.gfx.color.blend(this.toRgb(),rgb,_3b0);
}});
dojo.gfx.color.named={white:[255,255,255],black:[0,0,0],red:[255,0,0],green:[0,255,0],lime:[0,255,0],blue:[0,0,255],navy:[0,0,128],gray:[128,128,128],silver:[192,192,192]};
dojo.gfx.color.blend=function(a,b,_3b4){
if(typeof a=="string"){
return dojo.gfx.color.blendHex(a,b,_3b4);
}
if(!_3b4){
_3b4=0;
}
_3b4=Math.min(Math.max(-1,_3b4),1);
_3b4=((_3b4+1)/2);
var c=[];
for(var x=0;x<3;x++){
c[x]=parseInt(b[x]+((a[x]-b[x])*_3b4));
}
return c;
};
dojo.gfx.color.blendHex=function(a,b,_3b9){
return dojo.gfx.color.rgb2hex(dojo.gfx.color.blend(dojo.gfx.color.hex2rgb(a),dojo.gfx.color.hex2rgb(b),_3b9));
};
dojo.gfx.color.extractRGB=function(_3ba){
var hex="0123456789abcdef";
_3ba=_3ba.toLowerCase();
if(_3ba.indexOf("rgb")==0){
var _3bc=_3ba.match(/rgba*\((\d+), *(\d+), *(\d+)/i);
var ret=_3bc.splice(1,3);
return ret;
}else{
var _3be=dojo.gfx.color.hex2rgb(_3ba);
if(_3be){
return _3be;
}else{
return dojo.gfx.color.named[_3ba]||[255,255,255];
}
}
};
dojo.gfx.color.hex2rgb=function(hex){
var _3c0="0123456789ABCDEF";
var rgb=new Array(3);
if(hex.indexOf("#")==0){
hex=hex.substring(1);
}
hex=hex.toUpperCase();
if(hex.replace(new RegExp("["+_3c0+"]","g"),"")!=""){
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
rgb[i]=_3c0.indexOf(rgb[i].charAt(0))*16+_3c0.indexOf(rgb[i].charAt(1));
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
dojo.lfx.Line=function(_3c9,end){
this.start=_3c9;
this.end=end;
if(dojo.lang.isArray(_3c9)){
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
var diff=end-_3c9;
this.getValue=function(n){
return (diff*n)+this.start;
};
}
};
if((dojo.render.html.khtml)&&(!dojo.render.html.safari)){
dojo.lfx.easeDefault=function(n){
return (parseFloat("0.5")+((Math.sin((n+parseFloat("1.5"))*Math.PI))/2));
};
}else{
dojo.lfx.easeDefault=function(n){
return (0.5+((Math.sin((n+1.5)*Math.PI))/2));
};
}
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
dojo.lang.extend(dojo.lfx.IAnimation,{curve:null,duration:1000,easing:null,repeatCount:0,rate:10,handler:null,beforeBegin:null,onBegin:null,onAnimate:null,onEnd:null,onPlay:null,onPause:null,onStop:null,play:null,pause:null,stop:null,connect:function(evt,_3d9,_3da){
if(!_3da){
_3da=_3d9;
_3d9=this;
}
_3da=dojo.lang.hitch(_3d9,_3da);
var _3db=this[evt]||function(){
};
this[evt]=function(){
var ret=_3db.apply(this,arguments);
_3da.apply(this,arguments);
return ret;
};
return this;
},fire:function(evt,args){
if(this[evt]){
this[evt].apply(this,(args||[]));
}
return this;
},repeat:function(_3df){
this.repeatCount=_3df;
return this;
},_active:false,_paused:false});
dojo.lfx.Animation=function(_3e0,_3e1,_3e2,_3e3,_3e4,rate){
dojo.lfx.IAnimation.call(this);
if(dojo.lang.isNumber(_3e0)||(!_3e0&&_3e1.getValue)){
rate=_3e4;
_3e4=_3e3;
_3e3=_3e2;
_3e2=_3e1;
_3e1=_3e0;
_3e0=null;
}else{
if(_3e0.getValue||dojo.lang.isArray(_3e0)){
rate=_3e3;
_3e4=_3e2;
_3e3=_3e1;
_3e2=_3e0;
_3e1=null;
_3e0=null;
}
}
if(dojo.lang.isArray(_3e2)){
this.curve=new dojo.lfx.Line(_3e2[0],_3e2[1]);
}else{
this.curve=_3e2;
}
if(_3e1!=null&&_3e1>0){
this.duration=_3e1;
}
if(_3e4){
this.repeatCount=_3e4;
}
if(rate){
this.rate=rate;
}
if(_3e0){
dojo.lang.forEach(["handler","beforeBegin","onBegin","onEnd","onPlay","onStop","onAnimate"],function(item){
if(_3e0[item]){
this.connect(item,_3e0[item]);
}
},this);
}
if(_3e3&&dojo.lang.isFunction(_3e3)){
this.easing=_3e3;
}
};
dojo.inherits(dojo.lfx.Animation,dojo.lfx.IAnimation);
dojo.lang.extend(dojo.lfx.Animation,{_startTime:null,_endTime:null,_timer:null,_percent:0,_startRepeatCount:0,play:function(_3e7,_3e8){
if(_3e8){
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
if(_3e7>0){
setTimeout(dojo.lang.hitch(this,function(){
this.play(null,_3e8);
}),_3e7);
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
var _3ea=this.curve.getValue(step);
if(this._percent==0){
if(!this._startRepeatCount){
this._startRepeatCount=this.repeatCount;
}
this.fire("handler",["begin",_3ea]);
this.fire("onBegin",[_3ea]);
}
this.fire("handler",["play",_3ea]);
this.fire("onPlay",[_3ea]);
this._cycle();
return this;
},pause:function(){
clearTimeout(this._timer);
if(!this._active){
return this;
}
this._paused=true;
var _3eb=this.curve.getValue(this._percent/100);
this.fire("handler",["pause",_3eb]);
this.fire("onPause",[_3eb]);
return this;
},gotoPercent:function(pct,_3ed){
clearTimeout(this._timer);
this._active=true;
this._paused=true;
this._percent=pct;
if(_3ed){
this.play();
}
return this;
},stop:function(_3ee){
clearTimeout(this._timer);
var step=this._percent/100;
if(_3ee){
step=1;
}
var _3f0=this.curve.getValue(step);
this.fire("handler",["stop",_3f0]);
this.fire("onStop",[_3f0]);
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
var _3f3=this.curve.getValue(step);
this.fire("handler",["animate",_3f3]);
this.fire("onAnimate",[_3f3]);
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
dojo.lfx.Combine=function(_3f4){
dojo.lfx.IAnimation.call(this);
this._anims=[];
this._animsEnded=0;
var _3f5=arguments;
if(_3f5.length==1&&(dojo.lang.isArray(_3f5[0])||dojo.lang.isArrayLike(_3f5[0]))){
_3f5=_3f5[0];
}
dojo.lang.forEach(_3f5,function(anim){
this._anims.push(anim);
anim.connect("onEnd",dojo.lang.hitch(this,"_onAnimsEnded"));
},this);
};
dojo.inherits(dojo.lfx.Combine,dojo.lfx.IAnimation);
dojo.lang.extend(dojo.lfx.Combine,{_animsEnded:0,play:function(_3f7,_3f8){
if(!this._anims.length){
return this;
}
this.fire("beforeBegin");
if(_3f7>0){
setTimeout(dojo.lang.hitch(this,function(){
this.play(null,_3f8);
}),_3f7);
return this;
}
if(_3f8||this._anims[0].percent==0){
this.fire("onBegin");
}
this.fire("onPlay");
this._animsCall("play",null,_3f8);
return this;
},pause:function(){
this.fire("onPause");
this._animsCall("pause");
return this;
},stop:function(_3f9){
this.fire("onStop");
this._animsCall("stop",_3f9);
return this;
},_onAnimsEnded:function(){
this._animsEnded++;
if(this._animsEnded>=this._anims.length){
this.fire("onEnd");
}
return this;
},_animsCall:function(_3fa){
var args=[];
if(arguments.length>1){
for(var i=1;i<arguments.length;i++){
args.push(arguments[i]);
}
}
var _3fd=this;
dojo.lang.forEach(this._anims,function(anim){
anim[_3fa](args);
},_3fd);
return this;
}});
dojo.lfx.Chain=function(_3ff){
dojo.lfx.IAnimation.call(this);
this._anims=[];
this._currAnim=-1;
var _400=arguments;
if(_400.length==1&&(dojo.lang.isArray(_400[0])||dojo.lang.isArrayLike(_400[0]))){
_400=_400[0];
}
var _401=this;
dojo.lang.forEach(_400,function(anim,i,_404){
this._anims.push(anim);
if(i<_404.length-1){
anim.connect("onEnd",dojo.lang.hitch(this,"_playNext"));
}else{
anim.connect("onEnd",dojo.lang.hitch(this,function(){
this.fire("onEnd");
}));
}
},this);
};
dojo.inherits(dojo.lfx.Chain,dojo.lfx.IAnimation);
dojo.lang.extend(dojo.lfx.Chain,{_currAnim:-1,play:function(_405,_406){
if(!this._anims.length){
return this;
}
if(_406||!this._anims[this._currAnim]){
this._currAnim=0;
}
var _407=this._anims[this._currAnim];
this.fire("beforeBegin");
if(_405>0){
setTimeout(dojo.lang.hitch(this,function(){
this.play(null,_406);
}),_405);
return this;
}
if(_407){
if(this._currAnim==0){
this.fire("handler",["begin",this._currAnim]);
this.fire("onBegin",[this._currAnim]);
}
this.fire("onPlay",[this._currAnim]);
_407.play(null,_406);
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
var _408=this._anims[this._currAnim];
if(_408){
if(!_408._active||_408._paused){
this.play();
}else{
this.pause();
}
}
return this;
},stop:function(){
var _409=this._anims[this._currAnim];
if(_409){
_409.stop();
this.fire("onStop",[this._currAnim]);
}
return _409;
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
dojo.lfx.combine=function(_40a){
var _40b=arguments;
if(dojo.lang.isArray(arguments[0])){
_40b=arguments[0];
}
if(_40b.length==1){
return _40b[0];
}
return new dojo.lfx.Combine(_40b);
};
dojo.lfx.chain=function(_40c){
var _40d=arguments;
if(dojo.lang.isArray(arguments[0])){
_40d=arguments[0];
}
if(_40d.length==1){
return _40d[0];
}
return new dojo.lfx.Chain(_40d);
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
var _410=dojo.global();
var _411=dojo.doc();
var w=0;
var h=0;
if(dojo.render.html.mozilla){
w=_411.documentElement.clientWidth;
h=_410.innerHeight;
}else{
if(!dojo.render.html.opera&&_410.innerWidth){
w=_410.innerWidth;
h=_410.innerHeight;
}else{
if(!dojo.render.html.opera&&dojo.exists(_411,"documentElement.clientWidth")){
var w2=_411.documentElement.clientWidth;
if(!w||w2&&w2<w){
w=w2;
}
h=_411.documentElement.clientHeight;
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
var _415=dojo.global();
var _416=dojo.doc();
var top=_415.pageYOffset||_416.documentElement.scrollTop||dojo.body().scrollTop||0;
var left=_415.pageXOffset||_416.documentElement.scrollLeft||dojo.body().scrollLeft||0;
return {top:top,left:left,offset:{x:left,y:top}};
};
dojo.html.getParentByType=function(node,type){
var _41b=dojo.doc();
var _41c=dojo.byId(node);
type=type.toLowerCase();
while((_41c)&&(_41c.nodeName.toLowerCase()!=type)){
if(_41c==(_41b["body"]||_41b["documentElement"])){
return null;
}
_41c=_41c.parentNode;
}
return _41c;
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
var _424={x:0,y:0};
if(e.pageX||e.pageY){
_424.x=e.pageX;
_424.y=e.pageY;
}else{
var de=dojo.doc().documentElement;
var db=dojo.body();
_424.x=e.clientX+((de||db)["scrollLeft"])-((de||db)["clientLeft"]);
_424.y=e.clientY+((de||db)["scrollTop"])-((de||db)["clientTop"]);
}
return _424;
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
var _429=dojo.doc().createElement("script");
_429.src="javascript:'dojo.html.createExternalElement=function(doc, tag){ return doc.createElement(tag); }'";
dojo.doc().getElementsByTagName("head")[0].appendChild(_429);
})();
}
}else{
dojo.html.createExternalElement=function(doc,tag){
return doc.createElement(tag);
};
}
dojo.html._callDeprecated=function(_42c,_42d,args,_42f,_430){
dojo.deprecated("dojo.html."+_42c,"replaced by dojo.html."+_42d+"("+(_42f?"node, {"+_42f+": "+_42f+"}":"")+")"+(_430?"."+_430:""),"0.5");
var _431=[];
if(_42f){
var _432={};
_432[_42f]=args[1];
_431.push(args[0]);
_431.push(_432);
}else{
_431=args;
}
var ret=dojo.html[_42d].apply(dojo.html,args);
if(_430){
return ret[_430];
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
dojo.provide("dojo.uri.Uri");
dojo.uri=new function(){
this.dojoUri=function(uri){
return new dojo.uri.Uri(dojo.hostenv.getBaseScriptUri(),uri);
};
this.moduleUri=function(_435,uri){
var loc=dojo.hostenv.getModuleSymbols(_435).join("/");
if(!loc){
return null;
}
if(loc.lastIndexOf("/")!=loc.length-1){
loc+="/";
}
var _438=loc.indexOf(":");
var _439=loc.indexOf("/");
if(loc.charAt(0)!="/"&&(_438==-1||_438>_439)){
loc=dojo.hostenv.getBaseScriptUri()+loc;
}
return new dojo.uri.Uri(loc,uri);
};
this.Uri=function(){
var uri=arguments[0];
for(var i=1;i<arguments.length;i++){
if(!arguments[i]){
continue;
}
var _43c=new dojo.uri.Uri(arguments[i].toString());
var _43d=new dojo.uri.Uri(uri.toString());
if((_43c.path=="")&&(_43c.scheme==null)&&(_43c.authority==null)&&(_43c.query==null)){
if(_43c.fragment!=null){
_43d.fragment=_43c.fragment;
}
_43c=_43d;
}else{
if(_43c.scheme==null){
_43c.scheme=_43d.scheme;
if(_43c.authority==null){
_43c.authority=_43d.authority;
if(_43c.path.charAt(0)!="/"){
var path=_43d.path.substring(0,_43d.path.lastIndexOf("/")+1)+_43c.path;
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
_43c.path=segs.join("/");
}
}
}
}
uri="";
if(_43c.scheme!=null){
uri+=_43c.scheme+":";
}
if(_43c.authority!=null){
uri+="//"+_43c.authority;
}
uri+=_43c.path;
if(_43c.query!=null){
uri+="?"+_43c.query;
}
if(_43c.fragment!=null){
uri+="#"+_43c.fragment;
}
}
this.uri=uri.toString();
var _441="^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\\?([^#]*))?(#(.*))?$";
var r=this.uri.match(new RegExp(_441));
this.scheme=r[2]||(r[1]?"":null);
this.authority=r[4]||(r[3]?"":null);
this.path=r[5];
this.query=r[7]||(r[6]?"":null);
this.fragment=r[9]||(r[8]?"":null);
if(this.authority!=null){
_441="^((([^:]+:)?([^@]+))@)?([^:]*)(:([0-9]+))?$";
r=this.authority.match(new RegExp(_441));
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
if(typeof node.className=="string"){
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
dojo.html.hasClass=function(node,_448){
return (new RegExp("(^|\\s+)"+_448+"(\\s+|$)")).test(dojo.html.getClass(node));
};
dojo.html.prependClass=function(node,_44a){
_44a+=" "+dojo.html.getClass(node);
return dojo.html.setClass(node,_44a);
};
dojo.html.addClass=function(node,_44c){
if(dojo.html.hasClass(node,_44c)){
return false;
}
_44c=(dojo.html.getClass(node)+" "+_44c).replace(/^\s+|\s+$/g,"");
return dojo.html.setClass(node,_44c);
};
dojo.html.setClass=function(node,_44e){
node=dojo.byId(node);
var cs=new String(_44e);
try{
if(typeof node.className=="string"){
node.className=cs;
}else{
if(node.setAttribute){
node.setAttribute("class",_44e);
try{
node.className=cs;
}
catch(e){
}
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
dojo.html.removeClass=function(node,_451,_452){
try{
if(!_452){
var _453=dojo.html.getClass(node).replace(new RegExp("(^|\\s+)"+_451+"(\\s+|$)"),"$1$2");
}else{
var _453=dojo.html.getClass(node).replace(_451,"");
}
dojo.html.setClass(node,_453);
}
catch(e){
dojo.debug("dojo.html.removeClass() failed",e);
}
return true;
};
dojo.html.replaceClass=function(node,_455,_456){
dojo.html.removeClass(node,_456);
dojo.html.addClass(node,_455);
};
dojo.html.classMatchType={ContainsAll:0,ContainsAny:1,IsOnly:2};
dojo.html.getElementsByClass=function(_457,_458,_459,_45a,_45b){
_45b=false;
var _45c=dojo.doc();
_458=dojo.byId(_458)||_45c;
var _45d=_457.split(/\s+/g);
var _45e=[];
if(_45a!=1&&_45a!=2){
_45a=0;
}
var _45f=new RegExp("(\\s|^)(("+_45d.join(")|(")+"))(\\s|$)");
var _460=_45d.join(" ").length;
var _461=[];
if(!_45b&&_45c.evaluate){
var _462=".//"+(_459||"*")+"[contains(";
if(_45a!=dojo.html.classMatchType.ContainsAny){
_462+="concat(' ',@class,' '), ' "+_45d.join(" ') and contains(concat(' ',@class,' '), ' ")+" ')";
if(_45a==2){
_462+=" and string-length(@class)="+_460+"]";
}else{
_462+="]";
}
}else{
_462+="concat(' ',@class,' '), ' "+_45d.join(" ') or contains(concat(' ',@class,' '), ' ")+" ')]";
}
var _463=_45c.evaluate(_462,_458,null,XPathResult.ANY_TYPE,null);
var _464=_463.iterateNext();
while(_464){
try{
_461.push(_464);
_464=_463.iterateNext();
}
catch(e){
break;
}
}
return _461;
}else{
if(!_459){
_459="*";
}
_461=_458.getElementsByTagName(_459);
var node,i=0;
outer:
while(node=_461[i++]){
var _467=dojo.html.getClasses(node);
if(_467.length==0){
continue outer;
}
var _468=0;
for(var j=0;j<_467.length;j++){
if(_45f.test(_467[j])){
if(_45a==dojo.html.classMatchType.ContainsAny){
_45e.push(node);
continue outer;
}else{
_468++;
}
}else{
if(_45a==dojo.html.classMatchType.IsOnly){
continue outer;
}
}
}
if(_468==_45d.length){
if((_45a==dojo.html.classMatchType.IsOnly)&&(_468==_467.length)){
_45e.push(node);
}else{
if(_45a==dojo.html.classMatchType.ContainsAll){
_45e.push(node);
}
}
}
}
return _45e;
}
};
dojo.html.getElementsByClassName=dojo.html.getElementsByClass;
dojo.html.toCamelCase=function(_46a){
var arr=_46a.split("-"),cc=arr[0];
for(var i=1;i<arr.length;i++){
cc+=arr[i].charAt(0).toUpperCase()+arr[i].substring(1);
}
return cc;
};
dojo.html.toSelectorCase=function(_46e){
return _46e.replace(/([A-Z])/g,"-$1").toLowerCase();
};
if(dojo.render.html.ie){
dojo.html.getComputedStyle=function(node,_470,_471){
node=dojo.byId(node);
if(!node||!node.currentStyle){
return _471;
}
return node.currentStyle[dojo.html.toCamelCase(_470)];
};
dojo.html.getComputedStyles=function(node){
return node.currentStyle;
};
}else{
dojo.html.getComputedStyle=function(node,_474,_475){
node=dojo.byId(node);
if(!node||!node.style){
return _475;
}
var s=document.defaultView.getComputedStyle(node,null);
return (s&&s[dojo.html.toCamelCase(_474)])||"";
};
dojo.html.getComputedStyles=function(node){
return document.defaultView.getComputedStyle(node,null);
};
}
dojo.html.getStyleProperty=function(node,_479){
node=dojo.byId(node);
return (node&&node.style?node.style[dojo.html.toCamelCase(_479)]:undefined);
};
dojo.html.getStyle=function(node,_47b){
var _47c=dojo.html.getStyleProperty(node,_47b);
return (_47c?_47c:dojo.html.getComputedStyle(node,_47b));
};
dojo.html.setStyle=function(node,_47e,_47f){
node=dojo.byId(node);
if(node&&node.style){
var _480=dojo.html.toCamelCase(_47e);
node.style[_480]=_47f;
}
};
dojo.html.setStyleText=function(_481,text){
try{
_481.style.cssText=text;
}
catch(e){
_481.setAttribute("style",text);
}
};
dojo.html.copyStyle=function(_483,_484){
if(!_484.style.cssText){
_483.setAttribute("style",_484.getAttribute("style"));
}else{
_483.style.cssText=_484.style.cssText;
}
dojo.html.addClass(_483,dojo.html.getClass(_484));
};
dojo.html.getUnitValue=function(node,_486,_487){
var s=dojo.html.getComputedStyle(node,_486);
if((!s)||((s=="auto")&&(_487))){
return {value:0,units:"px"};
}
var _489=s.match(/(\-?[\d.]+)([a-z%]*)/i);
if(!_489){
return dojo.html.getUnitValue.bad;
}
return {value:Number(_489[1]),units:_489[2].toLowerCase()};
};
dojo.html.getUnitValue.bad={value:NaN,units:""};
if(dojo.render.html.ie){
dojo.html.toPixelValue=function(_48a,_48b){
if(!_48b){
return 0;
}
if(_48b.slice(-2)=="px"){
return parseFloat(_48b);
}
var _48c=0;
with(_48a){
var _48d=style.left;
var _48e=runtimeStyle.left;
runtimeStyle.left=currentStyle.left;
try{
style.left=_48b||0;
_48c=style.pixelLeft;
style.left=_48d;
runtimeStyle.left=_48e;
}
catch(e){
}
}
return _48c;
};
}else{
dojo.html.toPixelValue=function(_48f,_490){
return (_490&&(_490.slice(-2)=="px")?parseFloat(_490):0);
};
}
dojo.html.getPixelValue=function(node,_492,_493){
return dojo.html.toPixelValue(node,dojo.html.getComputedStyle(node,_492));
};
dojo.html.setPositivePixelValue=function(node,_495,_496){
if(isNaN(_496)){
return false;
}
node.style[_495]=Math.max(0,_496)+"px";
return true;
};
dojo.html.styleSheet=null;
dojo.html.insertCssRule=function(_497,_498,_499){
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
_499=dojo.html.styleSheet.cssRules.length;
}else{
if(dojo.html.styleSheet.rules){
_499=dojo.html.styleSheet.rules.length;
}else{
return null;
}
}
}
if(dojo.html.styleSheet.insertRule){
var rule=_497+" { "+_498+" }";
return dojo.html.styleSheet.insertRule(rule,_499);
}else{
if(dojo.html.styleSheet.addRule){
return dojo.html.styleSheet.addRule(_497,_498,_499);
}else{
return null;
}
}
};
dojo.html.removeCssRule=function(_49b){
if(!dojo.html.styleSheet){
dojo.debug("no stylesheet defined for removing rules");
return false;
}
if(dojo.render.html.ie){
if(!_49b){
_49b=dojo.html.styleSheet.rules.length;
dojo.html.styleSheet.removeRule(_49b);
}
}else{
if(document.styleSheets[0]){
if(!_49b){
_49b=dojo.html.styleSheet.cssRules.length;
}
dojo.html.styleSheet.deleteRule(_49b);
}
}
return true;
};
dojo.html._insertedCssFiles=[];
dojo.html.insertCssFile=function(URI,doc,_49e,_49f){
if(!URI){
return;
}
if(!doc){
doc=document;
}
var _4a0=dojo.hostenv.getText(URI,false,_49f);
if(_4a0===null){
return;
}
_4a0=dojo.html.fixPathsInCssText(_4a0,URI);
if(_49e){
var idx=-1,node,ent=dojo.html._insertedCssFiles;
for(var i=0;i<ent.length;i++){
if((ent[i].doc==doc)&&(ent[i].cssText==_4a0)){
idx=i;
node=ent[i].nodeRef;
break;
}
}
if(node){
var _4a5=doc.getElementsByTagName("style");
for(var i=0;i<_4a5.length;i++){
if(_4a5[i]==node){
return;
}
}
dojo.html._insertedCssFiles.shift(idx,1);
}
}
var _4a6=dojo.html.insertCssText(_4a0,doc);
dojo.html._insertedCssFiles.push({"doc":doc,"cssText":_4a0,"nodeRef":_4a6});
if(_4a6&&djConfig.isDebug){
_4a6.setAttribute("dbgHref",URI);
}
return _4a6;
};
dojo.html.insertCssText=function(_4a7,doc,URI){
if(!_4a7){
return;
}
if(!doc){
doc=document;
}
if(URI){
_4a7=dojo.html.fixPathsInCssText(_4a7,URI);
}
var _4aa=doc.createElement("style");
_4aa.setAttribute("type","text/css");
var head=doc.getElementsByTagName("head")[0];
if(!head){
dojo.debug("No head tag in document, aborting styles");
return;
}else{
head.appendChild(_4aa);
}
if(_4aa.styleSheet){
var _4ac=function(){
try{
_4aa.styleSheet.cssText=_4a7;
}
catch(e){
dojo.debug(e);
}
};
if(_4aa.styleSheet.disabled){
setTimeout(_4ac,10);
}else{
_4ac();
}
}else{
var _4ad=doc.createTextNode(_4a7);
_4aa.appendChild(_4ad);
}
return _4aa;
};
dojo.html.fixPathsInCssText=function(_4ae,URI){
if(!_4ae||!URI){
return;
}
var _4b0,str="",url="",_4b3="[\\t\\s\\w\\(\\)\\/\\.\\\\'\"-:#=&?~]+";
var _4b4=new RegExp("url\\(\\s*("+_4b3+")\\s*\\)");
var _4b5=/(file|https?|ftps?):\/\//;
regexTrim=new RegExp("^[\\s]*(['\"]?)("+_4b3+")\\1[\\s]*?$");
if(dojo.render.html.ie55||dojo.render.html.ie60){
var _4b6=new RegExp("AlphaImageLoader\\((.*)src=['\"]("+_4b3+")['\"]");
while(_4b0=_4b6.exec(_4ae)){
url=_4b0[2].replace(regexTrim,"$2");
if(!_4b5.exec(url)){
url=(new dojo.uri.Uri(URI,url).toString());
}
str+=_4ae.substring(0,_4b0.index)+"AlphaImageLoader("+_4b0[1]+"src='"+url+"'";
_4ae=_4ae.substr(_4b0.index+_4b0[0].length);
}
_4ae=str+_4ae;
str="";
}
while(_4b0=_4b4.exec(_4ae)){
url=_4b0[1].replace(regexTrim,"$2");
if(!_4b5.exec(url)){
url=(new dojo.uri.Uri(URI,url).toString());
}
str+=_4ae.substring(0,_4b0.index)+"url("+url+")";
_4ae=_4ae.substr(_4b0.index+_4b0[0].length);
}
return str+_4ae;
};
dojo.html.setActiveStyleSheet=function(_4b7){
var i=0,a,els=dojo.doc().getElementsByTagName("link");
while(a=els[i++]){
if(a.getAttribute("rel").indexOf("style")!=-1&&a.getAttribute("title")){
a.disabled=true;
if(a.getAttribute("title")==_4b7){
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
var _4c3={dj_ie:drh.ie,dj_ie55:drh.ie55,dj_ie6:drh.ie60,dj_ie7:drh.ie70,dj_iequirks:drh.ie&&drh.quirks,dj_opera:drh.opera,dj_opera8:drh.opera&&(Math.floor(dojo.render.version)==8),dj_opera9:drh.opera&&(Math.floor(dojo.render.version)==9),dj_khtml:drh.khtml,dj_safari:drh.safari,dj_gecko:drh.mozilla};
for(var p in _4c3){
if(_4c3[p]){
dojo.html.addClass(node,p);
}
}
};
dojo.provide("dojo.html.display");
dojo.html._toggle=function(node,_4c6,_4c7){
node=dojo.byId(node);
_4c7(node,!_4c6(node));
return _4c6(node);
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
dojo.html.setShowing=function(node,_4cc){
dojo.html[(_4cc?"show":"hide")](node);
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
dojo.html.setDisplay=function(node,_4d2){
dojo.html.setStyle(node,"display",((_4d2 instanceof String||typeof _4d2=="string")?_4d2:(_4d2?dojo.html.suggestDisplayByTagName(node):"none")));
};
dojo.html.isDisplayed=function(node){
return (dojo.html.getComputedStyle(node,"display")!="none");
};
dojo.html.toggleDisplay=function(node){
return dojo.html._toggle(node,dojo.html.isDisplayed,dojo.html.setDisplay);
};
dojo.html.setVisibility=function(node,_4d6){
dojo.html.setStyle(node,"visibility",((_4d6 instanceof String||typeof _4d6=="string")?_4d6:(_4d6?"visible":"hidden")));
};
dojo.html.isVisible=function(node){
return (dojo.html.getComputedStyle(node,"visibility")!="hidden");
};
dojo.html.toggleVisibility=function(node){
return dojo.html._toggle(node,dojo.html.isVisible,dojo.html.setVisibility);
};
dojo.html.setOpacity=function(node,_4da,_4db){
node=dojo.byId(node);
var h=dojo.render.html;
if(!_4db){
if(_4da>=1){
if(h.ie){
dojo.html.clearOpacity(node);
return;
}else{
_4da=0.999999;
}
}else{
if(_4da<0){
_4da=0;
}
}
}
if(h.ie){
if(node.nodeName.toLowerCase()=="tr"){
var tds=node.getElementsByTagName("td");
for(var x=0;x<tds.length;x++){
tds[x].style.filter="Alpha(Opacity="+_4da*100+")";
}
}
node.style.filter="Alpha(Opacity="+_4da*100+")";
}else{
if(h.moz){
node.style.opacity=_4da;
node.style.MozOpacity=_4da;
}else{
if(h.safari){
node.style.opacity=_4da;
node.style.KhtmlOpacity=_4da;
}else{
node.style.opacity=_4da;
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
var _4e6;
do{
_4e6=dojo.html.getStyle(node,"background-color");
if(_4e6.toLowerCase()=="rgba(0, 0, 0, 0)"){
_4e6="transparent";
}
if(node==document.getElementsByTagName("body")[0]){
node=null;
break;
}
node=node.parentNode;
}while(node&&dojo.lang.inArray(["transparent",""],_4e6));
if(_4e6=="transparent"){
_4e6=[255,255,255,0];
}else{
_4e6=dojo.gfx.color.extractRGB(_4e6);
}
return _4e6;
};
dojo.provide("dojo.html.layout");
dojo.html.sumAncestorProperties=function(node,prop){
node=(dojo.byId(node)||0).parentNode;
if(!node){
return 0;
}
var _4e9=0;
while(node){
if(dojo.html.getComputedStyle(node,"position")=="fixed"){
return 0;
}
var val=node[prop];
if(val){
_4e9+=val-0;
if(node==dojo.body()){
break;
}
}
node=node.parentNode;
}
return _4e9;
};
dojo.html.setStyleAttributes=function(node,_4ec){
node=dojo.byId(node);
var _4ed=_4ec.replace(/(;)?\s*$/,"").split(";");
for(var i=0;i<_4ed.length;i++){
var _4ef=_4ed[i].split(":");
var name=_4ef[0].replace(/\s*$/,"").replace(/^\s*/,"").toLowerCase();
var _4f1=_4ef[1].replace(/\s*$/,"").replace(/^\s*/,"");
switch(name){
case "opacity":
dojo.html.setOpacity(node,_4f1);
break;
case "content-height":
dojo.html.setContentBox(node,{height:_4f1});
break;
case "content-width":
dojo.html.setContentBox(node,{width:_4f1});
break;
case "outer-height":
dojo.html.setMarginBox(node,{height:_4f1});
break;
case "outer-width":
dojo.html.setMarginBox(node,{width:_4f1});
break;
default:
node.style[dojo.html.toCamelCase(name)]=_4f1;
}
}
};
dojo.html.boxSizing={MARGIN_BOX:"margin-box",BORDER_BOX:"border-box",PADDING_BOX:"padding-box",CONTENT_BOX:"content-box"};
dojo.html.getAbsolutePosition=dojo.html.abs=function(node,_4f3,_4f4){
node=dojo.byId(node,node.ownerDocument);
var ret={x:0,y:0};
var bs=dojo.html.boxSizing;
if(!_4f4){
_4f4=bs.CONTENT_BOX;
}
var _4f7=2;
var _4f8;
switch(_4f4){
case bs.MARGIN_BOX:
_4f8=3;
break;
case bs.BORDER_BOX:
_4f8=2;
break;
case bs.PADDING_BOX:
default:
_4f8=1;
break;
case bs.CONTENT_BOX:
_4f8=0;
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
_4f7=1;
try{
var bo=document.getBoxObjectFor(node);
ret.x=bo.x-dojo.html.sumAncestorProperties(node,"scrollLeft");
ret.y=bo.y-dojo.html.sumAncestorProperties(node,"scrollTop");
}
catch(e){
}
}else{
if(node["offsetParent"]){
var _4fc;
if((h.safari)&&(node.style.getPropertyValue("position")=="absolute")&&(node.parentNode==db)){
_4fc=db;
}else{
_4fc=db.parentNode;
}
if(node.parentNode!=db){
var nd=node;
if(dojo.render.html.opera){
nd=db;
}
ret.x-=dojo.html.sumAncestorProperties(nd,"scrollLeft");
ret.y-=dojo.html.sumAncestorProperties(nd,"scrollTop");
}
var _4fe=node;
do{
var n=_4fe["offsetLeft"];
if(!h.opera||n>0){
ret.x+=isNaN(n)?0:n;
}
var m=_4fe["offsetTop"];
ret.y+=isNaN(m)?0:m;
_4fe=_4fe.offsetParent;
}while((_4fe!=_4fc)&&(_4fe!=null));
}else{
if(node["x"]&&node["y"]){
ret.x+=isNaN(node.x)?0:node.x;
ret.y+=isNaN(node.y)?0:node.y;
}
}
}
}
if(_4f3){
var _501=dojo.html.getScroll();
ret.y+=_501.top;
ret.x+=_501.left;
}
var _502=[dojo.html.getPaddingExtent,dojo.html.getBorderExtent,dojo.html.getMarginExtent];
if(_4f7>_4f8){
for(var i=_4f8;i<_4f7;++i){
ret.y+=_502[i](node,"top");
ret.x+=_502[i](node,"left");
}
}else{
if(_4f7<_4f8){
for(var i=_4f8;i>_4f7;--i){
ret.y-=_502[i-1](node,"top");
ret.x-=_502[i-1](node,"left");
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
dojo.html._sumPixelValues=function(node,_506,_507){
var _508=0;
for(var x=0;x<_506.length;x++){
_508+=dojo.html.getPixelValue(node,_506[x],_507);
}
return _508;
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
var _515=dojo.html.getBorder(node);
return {width:pad.width+_515.width,height:pad.height+_515.height};
};
dojo.html.getBoxSizing=function(node){
var h=dojo.render.html;
var bs=dojo.html.boxSizing;
if(((h.ie)||(h.opera))&&node.nodeName.toLowerCase()!="img"){
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
var _51a;
if(!h.ie){
_51a=dojo.html.getStyle(node,"-moz-box-sizing");
if(!_51a){
_51a=dojo.html.getStyle(node,"box-sizing");
}
}
return (_51a?_51a:bs.CONTENT_BOX);
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
var _51f=dojo.html.getBorder(node);
return {width:box.width-_51f.width,height:box.height-_51f.height};
};
dojo.html.getContentBox=function(node){
node=dojo.byId(node);
var _521=dojo.html.getPadBorder(node);
return {width:node.offsetWidth-_521.width,height:node.offsetHeight-_521.height};
};
dojo.html.setContentBox=function(node,args){
node=dojo.byId(node);
var _524=0;
var _525=0;
var isbb=dojo.html.isBorderBox(node);
var _527=(isbb?dojo.html.getPadBorder(node):{width:0,height:0});
var ret={};
if(typeof args.width!="undefined"){
_524=args.width+_527.width;
ret.width=dojo.html.setPositivePixelValue(node,"width",_524);
}
if(typeof args.height!="undefined"){
_525=args.height+_527.height;
ret.height=dojo.html.setPositivePixelValue(node,"height",_525);
}
return ret;
};
dojo.html.getMarginBox=function(node){
var _52a=dojo.html.getBorderBox(node);
var _52b=dojo.html.getMargin(node);
return {width:_52a.width+_52b.width,height:_52a.height+_52b.height};
};
dojo.html.setMarginBox=function(node,args){
node=dojo.byId(node);
var _52e=0;
var _52f=0;
var isbb=dojo.html.isBorderBox(node);
var _531=(!isbb?dojo.html.getPadBorder(node):{width:0,height:0});
var _532=dojo.html.getMargin(node);
var ret={};
if(typeof args.width!="undefined"){
_52e=args.width-_531.width;
_52e-=_532.width;
ret.width=dojo.html.setPositivePixelValue(node,"width",_52e);
}
if(typeof args.height!="undefined"){
_52f=args.height-_531.height;
_52f-=_532.height;
ret.height=dojo.html.setPositivePixelValue(node,"height",_52f);
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
dojo.html.toCoordinateObject=dojo.html.toCoordinateArray=function(_537,_538,_539){
if(_537 instanceof Array||typeof _537=="array"){
dojo.deprecated("dojo.html.toCoordinateArray","use dojo.html.toCoordinateObject({left: , top: , width: , height: }) instead","0.5");
while(_537.length<4){
_537.push(0);
}
while(_537.length>4){
_537.pop();
}
var ret={left:_537[0],top:_537[1],width:_537[2],height:_537[3]};
}else{
if(!_537.nodeType&&!(_537 instanceof String||typeof _537=="string")&&("width" in _537||"height" in _537||"left" in _537||"x" in _537||"top" in _537||"y" in _537)){
var ret={left:_537.left||_537.x||0,top:_537.top||_537.y||0,width:_537.width||0,height:_537.height||0};
}else{
var node=dojo.byId(_537);
var pos=dojo.html.abs(node,_538,_539);
var _53d=dojo.html.getMarginBox(node);
var ret={left:pos.left,top:pos.top,width:_53d.width,height:_53d.height};
}
}
ret.x=ret.left;
ret.y=ret.top;
return ret;
};
dojo.html.setMarginBoxWidth=dojo.html.setOuterWidth=function(node,_53f){
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
dojo.html.getTotalOffset=function(node,type,_542){
return dojo.html._callDeprecated("getTotalOffset","getAbsolutePosition",arguments,null,type);
};
dojo.html.getAbsoluteX=function(node,_544){
return dojo.html._callDeprecated("getAbsoluteX","getAbsolutePosition",arguments,null,"x");
};
dojo.html.getAbsoluteY=function(node,_546){
return dojo.html._callDeprecated("getAbsoluteY","getAbsolutePosition",arguments,null,"y");
};
dojo.html.totalOffsetLeft=function(node,_548){
return dojo.html._callDeprecated("totalOffsetLeft","getAbsolutePosition",arguments,null,"left");
};
dojo.html.totalOffsetTop=function(node,_54a){
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
dojo.html.setContentBoxWidth=dojo.html.setContentWidth=function(node,_554){
return dojo.html._callDeprecated("setContentBoxWidth","setContentBox",arguments,"width");
};
dojo.html.setContentBoxHeight=dojo.html.setContentHeight=function(node,_556){
return dojo.html._callDeprecated("setContentBoxHeight","setContentBox",arguments,"height");
};
dojo.provide("dojo.lfx.html");
dojo.lfx.html._byId=function(_557){
if(!_557){
return [];
}
if(dojo.lang.isArrayLike(_557)){
if(!_557.alreadyChecked){
var n=[];
dojo.lang.forEach(_557,function(node){
n.push(dojo.byId(node));
});
n.alreadyChecked=true;
return n;
}else{
return _557;
}
}else{
var n=[];
n.push(dojo.byId(_557));
n.alreadyChecked=true;
return n;
}
};
dojo.lfx.html.propertyAnimation=function(_55a,_55b,_55c,_55d,_55e){
_55a=dojo.lfx.html._byId(_55a);
var _55f={"propertyMap":_55b,"nodes":_55a,"duration":_55c,"easing":_55d||dojo.lfx.easeDefault};
var _560=function(args){
if(args.nodes.length==1){
var pm=args.propertyMap;
if(!dojo.lang.isArray(args.propertyMap)){
var parr=[];
for(var _564 in pm){
pm[_564].property=_564;
parr.push(pm[_564]);
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
var _566=function(_567){
var _568=[];
dojo.lang.forEach(_567,function(c){
_568.push(Math.round(c));
});
return _568;
};
var _56a=function(n,_56c){
n=dojo.byId(n);
if(!n||!n.style){
return;
}
for(var s in _56c){
try{
if(s=="opacity"){
dojo.html.setOpacity(n,_56c[s]);
}else{
n.style[s]=_56c[s];
}
}
catch(e){
dojo.debug(e);
}
}
};
var _56e=function(_56f){
this._properties=_56f;
this.diffs=new Array(_56f.length);
dojo.lang.forEach(_56f,function(prop,i){
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
var _576=null;
if(dojo.lang.isArray(prop.start)){
}else{
if(prop.start instanceof dojo.gfx.color.Color){
_576=(prop.units||"rgb")+"(";
for(var j=0;j<prop.startRgb.length;j++){
_576+=Math.round(((prop.endRgb[j]-prop.startRgb[j])*n)+prop.startRgb[j])+(j<prop.startRgb.length-1?",":"");
}
_576+=")";
}else{
_576=((this.diffs[i])*n)+prop.start+(prop.property!="opacity"?prop.units||"px":"");
}
}
ret[dojo.html.toCamelCase(prop.property)]=_576;
},this);
return ret;
};
};
var anim=new dojo.lfx.Animation({beforeBegin:function(){
_560(_55f);
anim.curve=new _56e(_55f.propertyMap);
},onAnimate:function(_579){
dojo.lang.forEach(_55f.nodes,function(node){
_56a(node,_579);
});
}},_55f.duration,null,_55f.easing);
if(_55e){
for(var x in _55e){
if(dojo.lang.isFunction(_55e[x])){
anim.connect(x,anim,_55e[x]);
}
}
}
return anim;
};
dojo.lfx.html._makeFadeable=function(_57c){
var _57d=function(node){
if(dojo.render.html.ie){
if((node.style.zoom.length==0)&&(dojo.html.getStyle(node,"zoom")=="normal")){
node.style.zoom="1";
}
if((node.style.width.length==0)&&(dojo.html.getStyle(node,"width")=="auto")){
node.style.width="auto";
}
}
};
if(dojo.lang.isArrayLike(_57c)){
dojo.lang.forEach(_57c,_57d);
}else{
_57d(_57c);
}
};
dojo.lfx.html.fade=function(_57f,_580,_581,_582,_583){
_57f=dojo.lfx.html._byId(_57f);
var _584={property:"opacity"};
if(!dj_undef("start",_580)){
_584.start=_580.start;
}else{
_584.start=function(){
return dojo.html.getOpacity(_57f[0]);
};
}
if(!dj_undef("end",_580)){
_584.end=_580.end;
}else{
dojo.raise("dojo.lfx.html.fade needs an end value");
}
var anim=dojo.lfx.propertyAnimation(_57f,[_584],_581,_582);
anim.connect("beforeBegin",function(){
dojo.lfx.html._makeFadeable(_57f);
});
if(_583){
anim.connect("onEnd",function(){
_583(_57f,anim);
});
}
return anim;
};
dojo.lfx.html.fadeIn=function(_586,_587,_588,_589){
return dojo.lfx.html.fade(_586,{end:1},_587,_588,_589);
};
dojo.lfx.html.fadeOut=function(_58a,_58b,_58c,_58d){
return dojo.lfx.html.fade(_58a,{end:0},_58b,_58c,_58d);
};
dojo.lfx.html.fadeShow=function(_58e,_58f,_590,_591){
_58e=dojo.lfx.html._byId(_58e);
dojo.lang.forEach(_58e,function(node){
dojo.html.setOpacity(node,0);
});
var anim=dojo.lfx.html.fadeIn(_58e,_58f,_590,_591);
anim.connect("beforeBegin",function(){
if(dojo.lang.isArrayLike(_58e)){
dojo.lang.forEach(_58e,dojo.html.show);
}else{
dojo.html.show(_58e);
}
});
return anim;
};
dojo.lfx.html.fadeHide=function(_594,_595,_596,_597){
var anim=dojo.lfx.html.fadeOut(_594,_595,_596,function(){
if(dojo.lang.isArrayLike(_594)){
dojo.lang.forEach(_594,dojo.html.hide);
}else{
dojo.html.hide(_594);
}
if(_597){
_597(_594,anim);
}
});
return anim;
};
dojo.lfx.html.wipeIn=function(_599,_59a,_59b,_59c){
_599=dojo.lfx.html._byId(_599);
var _59d=[];
dojo.lang.forEach(_599,function(node){
var _59f={};
var _5a0,_5a1,_5a2;
with(node.style){
_5a0=top;
_5a1=left;
_5a2=position;
top="-9999px";
left="-9999px";
position="absolute";
display="";
}
var _5a3=dojo.html.getBorderBox(node).height;
with(node.style){
top=_5a0;
left=_5a1;
position=_5a2;
display="none";
}
var anim=dojo.lfx.propertyAnimation(node,{"height":{start:1,end:function(){
return _5a3;
}}},_59a,_59b);
anim.connect("beforeBegin",function(){
_59f.overflow=node.style.overflow;
_59f.height=node.style.height;
with(node.style){
overflow="hidden";
height="1px";
}
dojo.html.show(node);
});
anim.connect("onEnd",function(){
with(node.style){
overflow=_59f.overflow;
height=_59f.height;
}
if(_59c){
_59c(node,anim);
}
});
_59d.push(anim);
});
return dojo.lfx.combine(_59d);
};
dojo.lfx.html.wipeOut=function(_5a5,_5a6,_5a7,_5a8){
_5a5=dojo.lfx.html._byId(_5a5);
var _5a9=[];
dojo.lang.forEach(_5a5,function(node){
var _5ab={};
var anim=dojo.lfx.propertyAnimation(node,{"height":{start:function(){
return dojo.html.getContentBox(node).height;
},end:1}},_5a6,_5a7,{"beforeBegin":function(){
_5ab.overflow=node.style.overflow;
_5ab.height=node.style.height;
with(node.style){
overflow="hidden";
}
dojo.html.show(node);
},"onEnd":function(){
dojo.html.hide(node);
with(node.style){
overflow=_5ab.overflow;
height=_5ab.height;
}
if(_5a8){
_5a8(node,anim);
}
}});
_5a9.push(anim);
});
return dojo.lfx.combine(_5a9);
};
dojo.lfx.html.slideTo=function(_5ad,_5ae,_5af,_5b0,_5b1){
_5ad=dojo.lfx.html._byId(_5ad);
var _5b2=[];
var _5b3=dojo.html.getComputedStyle;
if(dojo.lang.isArray(_5ae)){
dojo.deprecated("dojo.lfx.html.slideTo(node, array)","use dojo.lfx.html.slideTo(node, {top: value, left: value});","0.5");
_5ae={top:_5ae[0],left:_5ae[1]};
}
dojo.lang.forEach(_5ad,function(node){
var top=null;
var left=null;
var init=(function(){
var _5b8=node;
return function(){
var pos=_5b3(_5b8,"position");
top=(pos=="absolute"?node.offsetTop:parseInt(_5b3(node,"top"))||0);
left=(pos=="absolute"?node.offsetLeft:parseInt(_5b3(node,"left"))||0);
if(!dojo.lang.inArray(["absolute","relative"],pos)){
var ret=dojo.html.abs(_5b8,true);
dojo.html.setStyleAttributes(_5b8,"position:absolute;top:"+ret.y+"px;left:"+ret.x+"px;");
top=ret.y;
left=ret.x;
}
};
})();
init();
var anim=dojo.lfx.propertyAnimation(node,{"top":{start:top,end:(_5ae.top||0)},"left":{start:left,end:(_5ae.left||0)}},_5af,_5b0,{"beforeBegin":init});
if(_5b1){
anim.connect("onEnd",function(){
_5b1(_5ad,anim);
});
}
_5b2.push(anim);
});
return dojo.lfx.combine(_5b2);
};
dojo.lfx.html.slideBy=function(_5bc,_5bd,_5be,_5bf,_5c0){
_5bc=dojo.lfx.html._byId(_5bc);
var _5c1=[];
var _5c2=dojo.html.getComputedStyle;
if(dojo.lang.isArray(_5bd)){
dojo.deprecated("dojo.lfx.html.slideBy(node, array)","use dojo.lfx.html.slideBy(node, {top: value, left: value});","0.5");
_5bd={top:_5bd[0],left:_5bd[1]};
}
dojo.lang.forEach(_5bc,function(node){
var top=null;
var left=null;
var init=(function(){
var _5c7=node;
return function(){
var pos=_5c2(_5c7,"position");
top=(pos=="absolute"?node.offsetTop:parseInt(_5c2(node,"top"))||0);
left=(pos=="absolute"?node.offsetLeft:parseInt(_5c2(node,"left"))||0);
if(!dojo.lang.inArray(["absolute","relative"],pos)){
var ret=dojo.html.abs(_5c7,true);
dojo.html.setStyleAttributes(_5c7,"position:absolute;top:"+ret.y+"px;left:"+ret.x+"px;");
top=ret.y;
left=ret.x;
}
};
})();
init();
var anim=dojo.lfx.propertyAnimation(node,{"top":{start:top,end:top+(_5bd.top||0)},"left":{start:left,end:left+(_5bd.left||0)}},_5be,_5bf).connect("beforeBegin",init);
if(_5c0){
anim.connect("onEnd",function(){
_5c0(_5bc,anim);
});
}
_5c1.push(anim);
});
return dojo.lfx.combine(_5c1);
};
dojo.lfx.html.explode=function(_5cb,_5cc,_5cd,_5ce,_5cf){
var h=dojo.html;
_5cb=dojo.byId(_5cb);
_5cc=dojo.byId(_5cc);
var _5d1=h.toCoordinateObject(_5cb,true);
var _5d2=document.createElement("div");
h.copyStyle(_5d2,_5cc);
if(_5cc.explodeClassName){
_5d2.className=_5cc.explodeClassName;
}
with(_5d2.style){
position="absolute";
display="none";
var _5d3=h.getStyle(_5cb,"background-color");
backgroundColor=_5d3?_5d3.toLowerCase():"transparent";
backgroundColor=(backgroundColor=="transparent")?"rgb(221, 221, 221)":backgroundColor;
}
dojo.body().appendChild(_5d2);
with(_5cc.style){
visibility="hidden";
display="block";
}
var _5d4=h.toCoordinateObject(_5cc,true);
with(_5cc.style){
display="none";
visibility="visible";
}
var _5d5={opacity:{start:0.5,end:1}};
dojo.lang.forEach(["height","width","top","left"],function(type){
_5d5[type]={start:_5d1[type],end:_5d4[type]};
});
var anim=new dojo.lfx.propertyAnimation(_5d2,_5d5,_5cd,_5ce,{"beforeBegin":function(){
h.setDisplay(_5d2,"block");
},"onEnd":function(){
h.setDisplay(_5cc,"block");
_5d2.parentNode.removeChild(_5d2);
}});
if(_5cf){
anim.connect("onEnd",function(){
_5cf(_5cc,anim);
});
}
return anim;
};
dojo.lfx.html.implode=function(_5d8,end,_5da,_5db,_5dc){
var h=dojo.html;
_5d8=dojo.byId(_5d8);
end=dojo.byId(end);
var _5de=dojo.html.toCoordinateObject(_5d8,true);
var _5df=dojo.html.toCoordinateObject(end,true);
var _5e0=document.createElement("div");
dojo.html.copyStyle(_5e0,_5d8);
if(_5d8.explodeClassName){
_5e0.className=_5d8.explodeClassName;
}
dojo.html.setOpacity(_5e0,0.3);
with(_5e0.style){
position="absolute";
display="none";
backgroundColor=h.getStyle(_5d8,"background-color").toLowerCase();
}
dojo.body().appendChild(_5e0);
var _5e1={opacity:{start:1,end:0.5}};
dojo.lang.forEach(["height","width","top","left"],function(type){
_5e1[type]={start:_5de[type],end:_5df[type]};
});
var anim=new dojo.lfx.propertyAnimation(_5e0,_5e1,_5da,_5db,{"beforeBegin":function(){
dojo.html.hide(_5d8);
dojo.html.show(_5e0);
},"onEnd":function(){
_5e0.parentNode.removeChild(_5e0);
}});
if(_5dc){
anim.connect("onEnd",function(){
_5dc(_5d8,anim);
});
}
return anim;
};
dojo.lfx.html.highlight=function(_5e4,_5e5,_5e6,_5e7,_5e8){
_5e4=dojo.lfx.html._byId(_5e4);
var _5e9=[];
dojo.lang.forEach(_5e4,function(node){
var _5eb=dojo.html.getBackgroundColor(node);
var bg=dojo.html.getStyle(node,"background-color").toLowerCase();
var _5ed=dojo.html.getStyle(node,"background-image");
var _5ee=(bg=="transparent"||bg=="rgba(0, 0, 0, 0)");
while(_5eb.length>3){
_5eb.pop();
}
var rgb=new dojo.gfx.color.Color(_5e5);
var _5f0=new dojo.gfx.color.Color(_5eb);
var anim=dojo.lfx.propertyAnimation(node,{"background-color":{start:rgb,end:_5f0}},_5e6,_5e7,{"beforeBegin":function(){
if(_5ed){
node.style.backgroundImage="none";
}
node.style.backgroundColor="rgb("+rgb.toRgb().join(",")+")";
},"onEnd":function(){
if(_5ed){
node.style.backgroundImage=_5ed;
}
if(_5ee){
node.style.backgroundColor="transparent";
}
if(_5e8){
_5e8(node,anim);
}
}});
_5e9.push(anim);
});
return dojo.lfx.combine(_5e9);
};
dojo.lfx.html.unhighlight=function(_5f2,_5f3,_5f4,_5f5,_5f6){
_5f2=dojo.lfx.html._byId(_5f2);
var _5f7=[];
dojo.lang.forEach(_5f2,function(node){
var _5f9=new dojo.gfx.color.Color(dojo.html.getBackgroundColor(node));
var rgb=new dojo.gfx.color.Color(_5f3);
var _5fb=dojo.html.getStyle(node,"background-image");
var anim=dojo.lfx.propertyAnimation(node,{"background-color":{start:_5f9,end:rgb}},_5f4,_5f5,{"beforeBegin":function(){
if(_5fb){
node.style.backgroundImage="none";
}
node.style.backgroundColor="rgb("+_5f9.toRgb().join(",")+")";
},"onEnd":function(){
if(_5f6){
_5f6(node,anim);
}
}});
_5f7.push(anim);
});
return dojo.lfx.combine(_5f7);
};
dojo.lang.mixin(dojo.lfx,dojo.lfx.html);
dojo.kwCompoundRequire({browser:["dojo.lfx.html"],dashboard:["dojo.lfx.html"]});
dojo.provide("dojo.lfx.*");
dojo.provide("dojo.xml.Parse");
dojo.xml.Parse=function(){
var isIE=((dojo.render.html.capable)&&(dojo.render.html.ie));
function getTagName(node){
try{
return node.tagName.toLowerCase();
}
catch(e){
return "";
}
}
function getDojoTagName(node){
var _600=getTagName(node);
if(!_600){
return "";
}
if((dojo.widget)&&(dojo.widget.tags[_600])){
return _600;
}
var p=_600.indexOf(":");
if(p>=0){
return _600;
}
if(_600.substr(0,5)=="dojo:"){
return _600;
}
if(dojo.render.html.capable&&dojo.render.html.ie&&node.scopeName!="HTML"){
return node.scopeName.toLowerCase()+":"+_600;
}
if(_600.substr(0,4)=="dojo"){
return "dojo:"+_600.substring(4);
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
if((dj_global["djConfig"])&&(!djConfig["ignoreClassNames"])){
var _603=node.className||node.getAttribute("class");
if((_603)&&(_603.indexOf)&&(_603.indexOf("dojo-")!=-1)){
var _604=_603.split(" ");
for(var x=0,c=_604.length;x<c;x++){
if(_604[x].slice(0,5)=="dojo-"){
return "dojo:"+_604[x].substr(5).toLowerCase();
}
}
}
}
return "";
}
this.parseElement=function(node,_608,_609,_60a){
var _60b=getTagName(node);
if(isIE&&_60b.indexOf("/")==0){
return null;
}
try{
var attr=node.getAttribute("parseWidgets");
if(attr&&attr.toLowerCase()=="false"){
return {};
}
}
catch(e){
}
var _60d=true;
if(_609){
var _60e=getDojoTagName(node);
_60b=_60e||_60b;
_60d=Boolean(_60e);
}
var _60f={};
_60f[_60b]=[];
var pos=_60b.indexOf(":");
if(pos>0){
var ns=_60b.substring(0,pos);
_60f["ns"]=ns;
if((dojo.ns)&&(!dojo.ns.allow(ns))){
_60d=false;
}
}
if(_60d){
var _612=this.parseAttributes(node);
for(var attr in _612){
if((!_60f[_60b][attr])||(typeof _60f[_60b][attr]!="array")){
_60f[_60b][attr]=[];
}
_60f[_60b][attr].push(_612[attr]);
}
_60f[_60b].nodeRef=node;
_60f.tagName=_60b;
_60f.index=_60a||0;
}
var _613=0;
for(var i=0;i<node.childNodes.length;i++){
var tcn=node.childNodes.item(i);
switch(tcn.nodeType){
case dojo.dom.ELEMENT_NODE:
var ctn=getDojoTagName(tcn)||getTagName(tcn);
if(!_60f[ctn]){
_60f[ctn]=[];
}
_60f[ctn].push(this.parseElement(tcn,true,_609,_613));
if((tcn.childNodes.length==1)&&(tcn.childNodes.item(0).nodeType==dojo.dom.TEXT_NODE)){
_60f[ctn][_60f[ctn].length-1].value=tcn.childNodes.item(0).nodeValue;
}
_613++;
break;
case dojo.dom.TEXT_NODE:
if(node.childNodes.length==1){
_60f[_60b].push({value:node.childNodes.item(0).nodeValue});
}
break;
default:
break;
}
}
return _60f;
};
this.parseAttributes=function(node){
var _618={};
var atts=node.attributes;
var _61a,i=0;
while((_61a=atts[i++])){
if(isIE){
if(!_61a){
continue;
}
if((typeof _61a=="object")&&(typeof _61a.nodeValue=="undefined")||(_61a.nodeValue==null)||(_61a.nodeValue=="")){
continue;
}
}
var nn=_61a.nodeName.split(":");
nn=(nn.length==2)?nn[1]:_61a.nodeName;
_618[nn]={value:_61a.nodeValue};
}
return _618;
};
};
dojo.provide("dojo.lang.declare");
dojo.lang.declare=function(_61d,_61e,init,_620){
if((dojo.lang.isFunction(_620))||((!_620)&&(!dojo.lang.isFunction(init)))){
var temp=_620;
_620=init;
init=temp;
}
var _622=[];
if(dojo.lang.isArray(_61e)){
_622=_61e;
_61e=_622.shift();
}
if(!init){
init=dojo.evalObjPath(_61d,false);
if((init)&&(!dojo.lang.isFunction(init))){
init=null;
}
}
var ctor=dojo.lang.declare._makeConstructor();
var scp=(_61e?_61e.prototype:null);
if(scp){
scp.prototyping=true;
ctor.prototype=new _61e();
scp.prototyping=false;
}
ctor.superclass=scp;
ctor.mixins=_622;
for(var i=0,l=_622.length;i<l;i++){
dojo.lang.extend(ctor,_622[i].prototype);
}
ctor.prototype.initializer=null;
ctor.prototype.declaredClass=_61d;
if(dojo.lang.isArray(_620)){
dojo.lang.extend.apply(dojo.lang,[ctor].concat(_620));
}else{
dojo.lang.extend(ctor,(_620)||{});
}
dojo.lang.extend(ctor,dojo.lang.declare._common);
ctor.prototype.constructor=ctor;
ctor.prototype.initializer=(ctor.prototype.initializer)||(init)||(function(){
});
var _627=dojo.parseObjPath(_61d,null,true);
_627.obj[_627.prop]=ctor;
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
},_contextMethod:function(_62d,_62e,args){
var _630,_631=this.___proto;
this.___proto=_62d;
try{
_630=_62d[_62e].apply(this,(args||[]));
}
catch(e){
throw e;
}
finally{
this.___proto=_631;
}
return _630;
},_inherited:function(prop,args){
var p=this._getPropContext();
do{
if((!p.constructor)||(!p.constructor.superclass)){
return;
}
p=p.constructor.superclass;
}while(!(prop in p));
return (dojo.lang.isFunction(p[prop])?this._contextMethod(p,prop,args):p[prop]);
},inherited:function(prop,args){
dojo.deprecated("'inherited' method is dangerous, do not up-call! 'inherited' is slated for removal in 0.5; name your super class (or use superclass property) instead.","0.5");
this._inherited(prop,args);
}};
dojo.declare=dojo.lang.declare;
dojo.provide("dojo.ns");
dojo.ns={namespaces:{},failed:{},loading:{},loaded:{},register:function(name,_638,_639,_63a){
if(!_63a||!this.namespaces[name]){
this.namespaces[name]=new dojo.ns.Ns(name,_638,_639);
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
dojo.ns.Ns=function(name,_641,_642){
this.name=name;
this.module=_641;
this.resolver=_642;
this._loaded=[];
this._failed=[];
};
dojo.ns.Ns.prototype.resolve=function(name,_644,_645){
if(!this.resolver||djConfig["skipAutoRequire"]){
return false;
}
var _646=this.resolver(name,_644);
if((_646)&&(!this._loaded[_646])&&(!this._failed[_646])){
var req=dojo.require;
req(_646,false,true);
if(dojo.hostenv.findModule(_646,false)){
this._loaded[_646]=true;
}else{
if(!_645){
dojo.raise("dojo.ns.Ns.resolve: module '"+_646+"' not found after loading via namespace '"+this.name+"'");
}
this._failed[_646]=true;
}
}
return Boolean(this._loaded[_646]);
};
dojo.registerNamespace=function(name,_649,_64a){
dojo.ns.register.apply(dojo.ns,arguments);
};
dojo.registerNamespaceResolver=function(name,_64c){
var n=dojo.ns.namespaces[name];
if(n){
n.resolver=_64c;
}
};
dojo.registerNamespaceManifest=function(_64e,path,name,_651,_652){
dojo.registerModulePath(name,path);
dojo.registerNamespace(name,_651,_652);
};
dojo.registerNamespace("dojo","dojo.widget");
dojo.provide("dojo.widget.Manager");
dojo.widget.manager=new function(){
this.widgets=[];
this.widgetIds=[];
this.topWidgets={};
var _653={};
var _654=[];
this.getUniqueId=function(_655){
var _656;
do{
_656=_655+"_"+(_653[_655]!=undefined?++_653[_655]:_653[_655]=0);
}while(this.getWidgetById(_656));
return _656;
};
this.add=function(_657){
this.widgets.push(_657);
if(!_657.extraArgs["id"]){
_657.extraArgs["id"]=_657.extraArgs["ID"];
}
if(_657.widgetId==""){
if(_657["id"]){
_657.widgetId=_657["id"];
}else{
if(_657.extraArgs["id"]){
_657.widgetId=_657.extraArgs["id"];
}else{
_657.widgetId=this.getUniqueId(_657.ns+"_"+_657.widgetType);
}
}
}
if(this.widgetIds[_657.widgetId]){
dojo.debug("widget ID collision on ID: "+_657.widgetId);
}
this.widgetIds[_657.widgetId]=_657;
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
this.remove=function(_659){
if(dojo.lang.isNumber(_659)){
var tw=this.widgets[_659].widgetId;
delete this.topWidgets[tw];
delete this.widgetIds[tw];
this.widgets.splice(_659,1);
}else{
this.removeById(_659);
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
var _660=(type.indexOf(":")<0?function(x){
return x.widgetType.toLowerCase();
}:function(x){
return x.getNamespacedType();
});
var ret=[];
dojo.lang.forEach(this.widgets,function(x){
if(_660(x)==lt){
ret.push(x);
}
});
return ret;
};
this.getWidgetsByFilter=function(_665,_666){
var ret=[];
dojo.lang.every(this.widgets,function(x){
if(_665(x)){
ret.push(x);
if(_666){
return false;
}
}
return true;
});
return (_666?ret[0]:ret);
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
var _66c={};
var _66d=["dojo.widget"];
for(var i=0;i<_66d.length;i++){
_66d[_66d[i]]=true;
}
this.registerWidgetPackage=function(_66f){
if(!_66d[_66f]){
_66d[_66f]=true;
_66d.push(_66f);
}
};
this.getWidgetPackageList=function(){
return dojo.lang.map(_66d,function(elt){
return (elt!==true?elt:undefined);
});
};
this.getImplementation=function(_671,_672,_673,ns){
var impl=this.getImplementationName(_671,ns);
if(impl){
var ret=_672?new impl(_672):new impl();
return ret;
}
};
function buildPrefixCache(){
for(var _677 in dojo.render){
if(dojo.render[_677]["capable"]===true){
var _678=dojo.render[_677].prefixes;
for(var i=0;i<_678.length;i++){
_654.push(_678[i].toLowerCase());
}
}
}
}
var _67a=function(_67b,_67c){
if(!_67c){
return null;
}
for(var i=0,l=_654.length,_67f;i<=l;i++){
_67f=(i<l?_67c[_654[i]]:_67c);
if(!_67f){
continue;
}
for(var name in _67f){
if(name.toLowerCase()==_67b){
return _67f[name];
}
}
}
return null;
};
var _681=function(_682,_683){
var _684=dojo.evalObjPath(_683,false);
return (_684?_67a(_682,_684):null);
};
this.getImplementationName=function(_685,ns){
var _687=_685.toLowerCase();
ns=ns||"dojo";
var imps=_66c[ns]||(_66c[ns]={});
var impl=imps[_687];
if(impl){
return impl;
}
if(!_654.length){
buildPrefixCache();
}
var _68a=dojo.ns.get(ns);
if(!_68a){
dojo.ns.register(ns,ns+".widget");
_68a=dojo.ns.get(ns);
}
if(_68a){
_68a.resolve(_685);
}
impl=_681(_687,_68a.module);
if(impl){
return (imps[_687]=impl);
}
_68a=dojo.ns.require(ns);
if((_68a)&&(_68a.resolver)){
_68a.resolve(_685);
impl=_681(_687,_68a.module);
if(impl){
return (imps[_687]=impl);
}
}
dojo.deprecated("dojo.widget.Manager.getImplementationName","Could not locate widget implementation for \""+_685+"\" in \""+_68a.module+"\" registered to namespace \""+_68a.name+"\". "+"Developers must specify correct namespaces for all non-Dojo widgets","0.5");
for(var i=0;i<_66d.length;i++){
impl=_681(_687,_66d[i]);
if(impl){
return (imps[_687]=impl);
}
}
throw new Error("Could not locate widget implementation for \""+_685+"\" in \""+_68a.module+"\" registered to namespace \""+_68a.name+"\"");
};
this.resizing=false;
this.onWindowResized=function(){
if(this.resizing){
return;
}
try{
this.resizing=true;
for(var id in this.topWidgets){
var _68d=this.topWidgets[id];
if(_68d.checkSize){
_68d.checkSize();
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
var g=function(_692,_693){
dw[(_693||_692)]=h(_692);
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
var _695=dwm.getAllWidgets.apply(dwm,arguments);
if(arguments.length>0){
return _695[n];
}
return _695;
};
g("registerWidgetPackage");
g("getImplementation","getWidgetImplementation");
g("getImplementationName","getWidgetImplementationName");
dw.widgets=dwm.widgets;
dw.widgetIds=dwm.widgetIds;
dw.root=dwm.root;
})();
dojo.kwCompoundRequire({common:[["dojo.uri.Uri",false,false]]});
dojo.provide("dojo.uri.*");
dojo.provide("dojo.a11y");
dojo.a11y={imgPath:dojo.uri.moduleUri("dojo.widget","templates/images"),doAccessibleCheck:true,accessible:null,checkAccessible:function(){
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
var div=dojo.doc().createElement("div");
div.style.backgroundImage="url(\""+this.imgPath+"/tab_close.gif\")";
dojo.body().appendChild(div);
var _697=null;
if(window.getComputedStyle){
var _698=getComputedStyle(div,"");
if(_698&&_698!=null){
_697=_698.getPropertyValue("background-image");
}
}else{
_697=div.currentStyle.backgroundImage;
}
var _699=false;
if(_697!=null&&(_697=="none"||_697=="url(invalid-url:)")){
this.accessible=true;
}
dojo.html.destroyNode(div);
}
return this.accessible;
},setCheckAccessible:function(_69a){
this.doAccessibleCheck=_69a;
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
},{parent:null,isTopLevel:false,disabled:false,isContainer:false,widgetId:"",widgetType:"Widget",ns:"dojo",getNamespacedType:function(){
return (this.ns?this.ns+":"+this.widgetType:this.widgetType).toLowerCase();
},toString:function(){
return "[Widget "+this.getNamespacedType()+", "+(this.widgetId||"NO ID")+"]";
},repr:function(){
return this.toString();
},enable:function(){
this.disabled=false;
},disable:function(){
this.disabled=true;
},onResized:function(){
this.notifyChildrenOfResize();
},notifyChildrenOfResize:function(){
for(var i=0;i<this.children.length;i++){
var _69c=this.children[i];
if(_69c.onResized){
_69c.onResized();
}
}
},create:function(args,_69e,_69f,ns){
if(ns){
this.ns=ns;
}
this.satisfyPropertySets(args,_69e,_69f);
this.mixInProperties(args,_69e,_69f);
this.postMixInProperties(args,_69e,_69f);
dojo.widget.manager.add(this);
this.buildRendering(args,_69e,_69f);
this.initialize(args,_69e,_69f);
this.postInitialize(args,_69e,_69f);
this.postCreate(args,_69e,_69f);
return this;
},destroy:function(_6a1){
if(this.parent){
this.parent.removeChild(this);
}
this.destroyChildren();
this.uninitialize();
this.destroyRendering(_6a1);
dojo.widget.manager.removeById(this.widgetId);
},destroyChildren:function(){
var _6a2;
var i=0;
while(this.children.length>i){
_6a2=this.children[i];
if(_6a2 instanceof dojo.widget.Widget){
this.removeChild(_6a2);
_6a2.destroy();
continue;
}
i++;
}
},getChildrenOfType:function(type,_6a5){
var ret=[];
var _6a7=dojo.lang.isFunction(type);
if(!_6a7){
type=type.toLowerCase();
}
for(var x=0;x<this.children.length;x++){
if(_6a7){
if(this.children[x] instanceof type){
ret.push(this.children[x]);
}
}else{
if(this.children[x].widgetType.toLowerCase()==type){
ret.push(this.children[x]);
}
}
if(_6a5){
ret=ret.concat(this.children[x].getChildrenOfType(type,_6a5));
}
}
return ret;
},getDescendants:function(){
var _6a9=[];
var _6aa=[this];
var elem;
while((elem=_6aa.pop())){
_6a9.push(elem);
if(elem.children){
dojo.lang.forEach(elem.children,function(elem){
_6aa.push(elem);
});
}
}
return _6a9;
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
var _6b1;
var _6b2=dojo.widget.lcArgsCache[this.widgetType];
if(_6b2==null){
_6b2={};
for(var y in this){
_6b2[((new String(y)).toLowerCase())]=y;
}
dojo.widget.lcArgsCache[this.widgetType]=_6b2;
}
var _6b4={};
for(var x in args){
if(!this[x]){
var y=_6b2[(new String(x)).toLowerCase()];
if(y){
args[y]=args[x];
x=y;
}
}
if(_6b4[x]){
continue;
}
_6b4[x]=true;
if((typeof this[x])!=(typeof _6b1)){
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
this[x]=dojo.uri.dojoUri(args[x]);
}else{
var _6b6=args[x].split(";");
for(var y=0;y<_6b6.length;y++){
var si=_6b6[y].indexOf(":");
if((si!=-1)&&(_6b6[y].length>si)){
this[x][_6b6[y].substr(0,si).replace(/^\s+|\s+$/g,"")]=_6b6[y].substr(si+1);
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
},postMixInProperties:function(args,frag,_6ba){
},initialize:function(args,frag,_6bd){
return false;
},postInitialize:function(args,frag,_6c0){
return false;
},postCreate:function(args,frag,_6c3){
return false;
},uninitialize:function(){
return false;
},buildRendering:function(args,frag,_6c6){
dojo.unimplemented("dojo.widget.Widget.buildRendering, on "+this.toString()+", ");
return false;
},destroyRendering:function(){
dojo.unimplemented("dojo.widget.Widget.destroyRendering");
return false;
},addedTo:function(_6c7){
},addChild:function(_6c8){
dojo.unimplemented("dojo.widget.Widget.addChild");
return false;
},removeChild:function(_6c9){
for(var x=0;x<this.children.length;x++){
if(this.children[x]===_6c9){
this.children.splice(x,1);
_6c9.parent=null;
break;
}
}
return _6c9;
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
dojo.widget.tags["dojo:propertyset"]=function(_6ce,_6cf,_6d0){
var _6d1=_6cf.parseProperties(_6ce["dojo:propertyset"]);
};
dojo.widget.tags["dojo:connect"]=function(_6d2,_6d3,_6d4){
var _6d5=_6d3.parseProperties(_6d2["dojo:connect"]);
};
dojo.widget.buildWidgetFromParseTree=function(type,frag,_6d8,_6d9,_6da,_6db){
dojo.a11y.setAccessibleMode();
var _6dc=type.split(":");
_6dc=(_6dc.length==2)?_6dc[1]:type;
var _6dd=_6db||_6d8.parseProperties(frag[frag["ns"]+":"+_6dc]);
var _6de=dojo.widget.manager.getImplementation(_6dc,null,null,frag["ns"]);
if(!_6de){
throw new Error("cannot find \""+type+"\" widget");
}else{
if(!_6de.create){
throw new Error("\""+type+"\" widget object has no \"create\" method and does not appear to implement *Widget");
}
}
_6dd["dojoinsertionindex"]=_6da;
var ret=_6de.create(_6dd,frag,_6d9,frag["ns"]);
return ret;
};
dojo.widget.defineWidget=function(_6e0,_6e1,_6e2,init,_6e4){
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
dojo.widget._defineWidget=function(_6e7,_6e8,_6e9,init,_6eb){
var _6ec=_6e7.split(".");
var type=_6ec.pop();
var regx="\\.("+(_6e8?_6e8+"|":"")+dojo.widget.defineWidget.renderers+")\\.";
var r=_6e7.search(new RegExp(regx));
_6ec=(r<0?_6ec.join("."):_6e7.substr(0,r));
dojo.widget.manager.registerWidgetPackage(_6ec);
var pos=_6ec.indexOf(".");
var _6f1=(pos>-1)?_6ec.substring(0,pos):_6ec;
_6eb=(_6eb)||{};
_6eb.widgetType=type;
if((!init)&&(_6eb["classConstructor"])){
init=_6eb.classConstructor;
delete _6eb.classConstructor;
}
dojo.declare(_6e7,_6e9,init,_6eb);
};
dojo.provide("dojo.widget.Parse");
dojo.widget.Parse=function(_6f2){
this.propertySetsList=[];
this.fragment=_6f2;
this.createComponents=function(frag,_6f4){
var _6f5=[];
var _6f6=false;
try{
if(frag&&frag.tagName&&(frag!=frag.nodeRef)){
var _6f7=dojo.widget.tags;
var tna=String(frag.tagName).split(";");
for(var x=0;x<tna.length;x++){
var ltn=tna[x].replace(/^\s+|\s+$/g,"").toLowerCase();
frag.tagName=ltn;
var ret;
if(_6f7[ltn]){
_6f6=true;
ret=_6f7[ltn](frag,this,_6f4,frag.index);
_6f5.push(ret);
}else{
if(ltn.indexOf(":")==-1){
ltn="dojo:"+ltn;
}
ret=dojo.widget.buildWidgetFromParseTree(ltn,frag,this,_6f4,frag.index);
if(ret){
_6f6=true;
_6f5.push(ret);
}
}
}
}
}
catch(e){
dojo.debug("dojo.widget.Parse: error:",e);
}
if(!_6f6){
_6f5=_6f5.concat(this.createSubComponents(frag,_6f4));
}
return _6f5;
};
this.createSubComponents=function(_6fc,_6fd){
var frag,_6ff=[];
for(var item in _6fc){
frag=_6fc[item];
if(frag&&typeof frag=="object"&&(frag!=_6fc.nodeRef)&&(frag!=_6fc.tagName)&&(!dojo.dom.isNode(frag))){
_6ff=_6ff.concat(this.createComponents(frag,_6fd));
}
}
return _6ff;
};
this.parsePropertySets=function(_701){
return [];
};
this.parseProperties=function(_702){
var _703={};
for(var item in _702){
if((_702[item]==_702.tagName)||(_702[item]==_702.nodeRef)){
}else{
var frag=_702[item];
if(frag.tagName&&dojo.widget.tags[frag.tagName.toLowerCase()]){
}else{
if(frag[0]&&frag[0].value!=""&&frag[0].value!=null){
try{
if(item.toLowerCase()=="dataprovider"){
var _706=this;
this.getDataProvider(_706,frag[0].value);
_703.dataProvider=this.dataProvider;
}
_703[item]=frag[0].value;
var _707=this.parseProperties(frag);
for(var _708 in _707){
_703[_708]=_707[_708];
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
if(typeof _703[item]!="boolean"){
_703[item]=true;
}
break;
}
}
}
return _703;
};
this.getDataProvider=function(_709,_70a){
dojo.io.bind({url:_70a,load:function(type,_70c){
if(type=="load"){
_709.dataProvider=_70c;
}
},mimetype:"text/javascript",sync:true});
};
this.getPropertySetById=function(_70d){
for(var x=0;x<this.propertySetsList.length;x++){
if(_70d==this.propertySetsList[x]["id"][0].value){
return this.propertySetsList[x];
}
}
return "";
};
this.getPropertySetsByType=function(_70f){
var _710=[];
for(var x=0;x<this.propertySetsList.length;x++){
var cpl=this.propertySetsList[x];
var cpcc=cpl.componentClass||cpl.componentType||null;
var _714=this.propertySetsList[x]["id"][0].value;
if(cpcc&&(_714==cpcc[0].value)){
_710.push(cpl);
}
}
return _710;
};
this.getPropertySets=function(_715){
var ppl="dojo:propertyproviderlist";
var _717=[];
var _718=_715.tagName;
if(_715[ppl]){
var _719=_715[ppl].value.split(" ");
for(var _71a in _719){
if((_71a.indexOf("..")==-1)&&(_71a.indexOf("://")==-1)){
var _71b=this.getPropertySetById(_71a);
if(_71b!=""){
_717.push(_71b);
}
}else{
}
}
}
return this.getPropertySetsByType(_718).concat(_717);
};
this.createComponentFromScript=function(_71c,_71d,_71e,ns){
_71e.fastMixIn=true;
var ltn=(ns||"dojo")+":"+_71d.toLowerCase();
if(dojo.widget.tags[ltn]){
return [dojo.widget.tags[ltn](_71e,this,null,null,_71e)];
}
return [dojo.widget.buildWidgetFromParseTree(ltn,_71e,this,null,null,_71e)];
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
dojo.widget.createWidget=function(name,_723,_724,_725){
var _726=false;
var _727=(typeof name=="string");
if(_727){
var pos=name.indexOf(":");
var ns=(pos>-1)?name.substring(0,pos):"dojo";
if(pos>-1){
name=name.substring(pos+1);
}
var _72a=name.toLowerCase();
var _72b=ns+":"+_72a;
_726=(dojo.byId(name)&&!dojo.widget.tags[_72b]);
}
if((arguments.length==1)&&(_726||!_727)){
var xp=new dojo.xml.Parse();
var tn=_726?dojo.byId(name):name;
return dojo.widget.getParser().createComponents(xp.parseElement(tn,null,true))[0];
}
function fromScript(_72e,name,_730,ns){
_730[_72b]={dojotype:[{value:_72a}],nodeRef:_72e,fastMixIn:true};
_730.ns=ns;
return dojo.widget.getParser().createComponentFromScript(_72e,name,_730,ns);
}
_723=_723||{};
var _732=false;
var tn=null;
var h=dojo.render.html.capable;
if(h){
tn=document.createElement("span");
}
if(!_724){
_732=true;
_724=tn;
if(h){
dojo.body().appendChild(_724);
}
}else{
if(_725){
dojo.dom.insertAtPosition(tn,_724,_725);
}else{
tn=_724;
}
}
var _734=fromScript(tn,name.toLowerCase(),_723,ns);
if((!_734)||(!_734[0])||(typeof _734[0].widgetType=="undefined")){
throw new Error("createWidget: Creation of \""+name+"\" widget failed.");
}
try{
if(_732&&_734[0].domNode.parentNode){
_734[0].domNode.parentNode.removeChild(_734[0].domNode);
}
}
catch(e){
dojo.debug(e);
}
return _734[0];
};
dojo.provide("dojo.widget.DomWidget");
dojo.widget._cssFiles={};
dojo.widget._cssStrings={};
dojo.widget._templateCache={};
dojo.widget.defaultStrings={dojoRoot:dojo.hostenv.getBaseScriptUri(),dojoWidgetModuleUri:dojo.uri.moduleUri("dojo.widget"),baseScriptUri:dojo.hostenv.getBaseScriptUri()};
dojo.widget.fillFromTemplateCache=function(obj,_736,_737,_738){
var _739=_736||obj.templatePath;
var _73a=dojo.widget._templateCache;
if(!_739&&!obj["widgetType"]){
do{
var _73b="__dummyTemplate__"+dojo.widget._templateCache.dummyCount++;
}while(_73a[_73b]);
obj.widgetType=_73b;
}
var wt=_739?_739.toString():obj.widgetType;
var ts=_73a[wt];
if(!ts){
_73a[wt]={"string":null,"node":null};
if(_738){
ts={};
}else{
ts=_73a[wt];
}
}
if((!obj.templateString)&&(!_738)){
obj.templateString=_737||ts["string"];
}
if(obj.templateString){
obj.templateString=this._sanitizeTemplateString(obj.templateString);
}
if((!obj.templateNode)&&(!_738)){
obj.templateNode=ts["node"];
}
if((!obj.templateNode)&&(!obj.templateString)&&(_739)){
var _73e=this._sanitizeTemplateString(dojo.hostenv.getText(_739));
obj.templateString=_73e;
if(!_738){
_73a[wt]["string"]=_73e;
}
}
if((!ts["string"])&&(!_738)){
ts.string=obj.templateString;
}
};
dojo.widget._sanitizeTemplateString=function(_73f){
if(_73f){
_73f=_73f.replace(/^\s*<\?xml(\s)+version=[\'\"](\d)*.(\d)*[\'\"](\s)*\?>/im,"");
var _740=_73f.match(/<body[^>]*>\s*([\s\S]+)\s*<\/body>/im);
if(_740){
_73f=_740[1];
}
}else{
_73f="";
}
return _73f;
};
dojo.widget._templateCache.dummyCount=0;
dojo.widget.attachProperties=["dojoAttachPoint","id"];
dojo.widget.eventAttachProperty="dojoAttachEvent";
dojo.widget.onBuildProperty="dojoOnBuild";
dojo.widget.waiNames=["waiRole","waiState"];
dojo.widget.wai={waiRole:{name:"waiRole","namespace":"http://www.w3.org/TR/xhtml2",alias:"x2",prefix:"wairole:"},waiState:{name:"waiState","namespace":"http://www.w3.org/2005/07/aaa",alias:"aaa",prefix:""},setAttr:function(node,ns,attr,_744){
if(dojo.render.html.ie){
node.setAttribute(this[ns].alias+":"+attr,this[ns].prefix+_744);
}else{
node.setAttributeNS(this[ns]["namespace"],attr,this[ns].prefix+_744);
}
},getAttr:function(node,ns,attr){
if(dojo.render.html.ie){
return node.getAttribute(this[ns].alias+":"+attr);
}else{
return node.getAttributeNS(this[ns]["namespace"],attr);
}
},removeAttr:function(node,ns,attr){
var _74b=true;
if(dojo.render.html.ie){
_74b=node.removeAttribute(this[ns].alias+":"+attr);
}else{
node.removeAttributeNS(this[ns]["namespace"],attr);
}
return _74b;
}};
dojo.widget.attachTemplateNodes=function(_74c,_74d,_74e){
var _74f=dojo.dom.ELEMENT_NODE;
function trim(str){
return str.replace(/^\s+|\s+$/g,"");
}
if(!_74c){
_74c=_74d.domNode;
}
if(_74c.nodeType!=_74f){
return;
}
var _751=_74c.all||_74c.getElementsByTagName("*");
var _752=_74d;
for(var x=-1;x<_751.length;x++){
var _754=(x==-1)?_74c:_751[x];
var _755=[];
if(!_74d.widgetsInTemplate||!_754.getAttribute("dojoType")){
for(var y=0;y<this.attachProperties.length;y++){
var _757=_754.getAttribute(this.attachProperties[y]);
if(_757){
_755=_757.split(";");
for(var z=0;z<_755.length;z++){
if(dojo.lang.isArray(_74d[_755[z]])){
_74d[_755[z]].push(_754);
}else{
_74d[_755[z]]=_754;
}
}
break;
}
}
var _759=_754.getAttribute(this.eventAttachProperty);
if(_759){
var evts=_759.split(";");
for(var y=0;y<evts.length;y++){
if((!evts[y])||(!evts[y].length)){
continue;
}
var _75b=null;
var tevt=trim(evts[y]);
if(evts[y].indexOf(":")>=0){
var _75d=tevt.split(":");
tevt=trim(_75d[0]);
_75b=trim(_75d[1]);
}
if(!_75b){
_75b=tevt;
}
var tf=function(){
var ntf=new String(_75b);
return function(evt){
if(_752[ntf]){
_752[ntf](dojo.event.browser.fixEvent(evt,this));
}
};
}();
dojo.event.browser.addListener(_754,tevt,tf,false,true);
}
}
for(var y=0;y<_74e.length;y++){
var _761=_754.getAttribute(_74e[y]);
if((_761)&&(_761.length)){
var _75b=null;
var _762=_74e[y].substr(4);
_75b=trim(_761);
var _763=[_75b];
if(_75b.indexOf(";")>=0){
_763=dojo.lang.map(_75b.split(";"),trim);
}
for(var z=0;z<_763.length;z++){
if(!_763[z].length){
continue;
}
var tf=function(){
var ntf=new String(_763[z]);
return function(evt){
if(_752[ntf]){
_752[ntf](dojo.event.browser.fixEvent(evt,this));
}
};
}();
dojo.event.browser.addListener(_754,_762,tf,false,true);
}
}
}
}
var _766=_754.getAttribute(this.templateProperty);
if(_766){
_74d[_766]=_754;
}
dojo.lang.forEach(dojo.widget.waiNames,function(name){
var wai=dojo.widget.wai[name];
var val=_754.getAttribute(wai.name);
if(val){
if(val.indexOf("-")==-1){
dojo.widget.wai.setAttr(_754,wai.name,"role",val);
}else{
var _76a=val.split("-");
dojo.widget.wai.setAttr(_754,wai.name,_76a[0],_76a[1]);
}
}
},this);
var _76b=_754.getAttribute(this.onBuildProperty);
if(_76b){
eval("var node = baseNode; var widget = targetObj; "+_76b);
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
},{templateNode:null,templateString:null,templateCssString:null,preventClobber:false,domNode:null,containerNode:null,widgetsInTemplate:false,addChild:function(_773,_774,pos,ref,_777){
if(!this.isContainer){
dojo.debug("dojo.widget.DomWidget.addChild() attempted on non-container widget");
return null;
}else{
if(_777==undefined){
_777=this.children.length;
}
this.addWidgetAsDirectChild(_773,_774,pos,ref,_777);
this.registerChild(_773,_777);
}
return _773;
},addWidgetAsDirectChild:function(_778,_779,pos,ref,_77c){
if((!this.containerNode)&&(!_779)){
this.containerNode=this.domNode;
}
var cn=(_779)?_779:this.containerNode;
if(!pos){
pos="after";
}
if(!ref){
if(!cn){
cn=dojo.body();
}
ref=cn.lastChild;
}
if(!_77c){
_77c=0;
}
_778.domNode.setAttribute("dojoinsertionindex",_77c);
if(!ref){
cn.appendChild(_778.domNode);
}else{
if(pos=="insertAtIndex"){
dojo.dom.insertAtIndex(_778.domNode,ref.parentNode,_77c);
}else{
if((pos=="after")&&(ref===cn.lastChild)){
cn.appendChild(_778.domNode);
}else{
dojo.dom.insertAtPosition(_778.domNode,cn,pos);
}
}
}
},registerChild:function(_77e,_77f){
_77e.dojoInsertionIndex=_77f;
var idx=-1;
for(var i=0;i<this.children.length;i++){
if(this.children[i].dojoInsertionIndex<=_77f){
idx=i;
}
}
this.children.splice(idx+1,0,_77e);
_77e.parent=this;
_77e.addedTo(this,idx+1);
delete dojo.widget.manager.topWidgets[_77e.widgetId];
},removeChild:function(_782){
dojo.dom.removeNode(_782.domNode);
return dojo.widget.DomWidget.superclass.removeChild.call(this,_782);
},getFragNodeRef:function(frag){
if(!frag){
return null;
}
if(!frag[this.getNamespacedType()]){
dojo.raise("Error: no frag for widget type "+this.getNamespacedType()+", id "+this.widgetId+" (maybe a widget has set it's type incorrectly)");
}
return frag[this.getNamespacedType()]["nodeRef"];
},postInitialize:function(args,frag,_786){
var _787=this.getFragNodeRef(frag);
if(_786&&(_786.snarfChildDomOutput||!_787)){
_786.addWidgetAsDirectChild(this,"","insertAtIndex","",args["dojoinsertionindex"],_787);
}else{
if(_787){
if(this.domNode&&(this.domNode!==_787)){
this._sourceNodeRef=dojo.dom.replaceNode(_787,this.domNode);
}
}
}
if(_786){
_786.registerChild(this,args.dojoinsertionindex);
}else{
dojo.widget.manager.topWidgets[this.widgetId]=this;
}
if(this.widgetsInTemplate){
var _788=new dojo.xml.Parse();
var _789;
var _78a=this.domNode.getElementsByTagName("*");
for(var i=0;i<_78a.length;i++){
if(_78a[i].getAttribute("dojoAttachPoint")=="subContainerWidget"){
_789=_78a[i];
}
if(_78a[i].getAttribute("dojoType")){
_78a[i].setAttribute("isSubWidget",true);
}
}
if(this.isContainer&&!this.containerNode){
if(_789){
var src=this.getFragNodeRef(frag);
if(src){
dojo.dom.moveChildren(src,_789);
frag["dojoDontFollow"]=true;
}
}else{
dojo.debug("No subContainerWidget node can be found in template file for widget "+this);
}
}
var _78d=_788.parseElement(this.domNode,null,true);
dojo.widget.getParser().createSubComponents(_78d,this);
var _78e=[];
var _78f=[this];
var w;
while((w=_78f.pop())){
for(var i=0;i<w.children.length;i++){
var _791=w.children[i];
if(_791._processedSubWidgets||!_791.extraArgs["issubwidget"]){
continue;
}
_78e.push(_791);
if(_791.isContainer){
_78f.push(_791);
}
}
}
for(var i=0;i<_78e.length;i++){
var _792=_78e[i];
if(_792._processedSubWidgets){
dojo.debug("This should not happen: widget._processedSubWidgets is already true!");
return;
}
_792._processedSubWidgets=true;
if(_792.extraArgs["dojoattachevent"]){
var evts=_792.extraArgs["dojoattachevent"].split(";");
for(var j=0;j<evts.length;j++){
var _795=null;
var tevt=dojo.string.trim(evts[j]);
if(tevt.indexOf(":")>=0){
var _797=tevt.split(":");
tevt=dojo.string.trim(_797[0]);
_795=dojo.string.trim(_797[1]);
}
if(!_795){
_795=tevt;
}
if(dojo.lang.isFunction(_792[tevt])){
dojo.event.kwConnect({srcObj:_792,srcFunc:tevt,targetObj:this,targetFunc:_795});
}else{
alert(tevt+" is not a function in widget "+_792);
}
}
}
if(_792.extraArgs["dojoattachpoint"]){
this[_792.extraArgs["dojoattachpoint"]]=_792;
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
var _79b=args["templateCssPath"]||this.templateCssPath;
if(_79b&&!dojo.widget._cssFiles[_79b.toString()]){
if((!this.templateCssString)&&(_79b)){
this.templateCssString=dojo.hostenv.getText(_79b);
this.templateCssPath=null;
}
dojo.widget._cssFiles[_79b.toString()]=true;
}
if((this["templateCssString"])&&(!dojo.widget._cssStrings[this.templateCssString])){
dojo.html.insertCssText(this.templateCssString,null,_79b);
dojo.widget._cssStrings[this.templateCssString]=true;
}
if((!this.preventClobber)&&((this.templatePath)||(this.templateNode)||((this["templateString"])&&(this.templateString.length))||((typeof ts!="undefined")&&((ts["string"])||(ts["node"]))))){
this.buildFromTemplate(args,frag);
}else{
this.domNode=this.getFragNodeRef(frag);
}
this.fillInTemplate(args,frag);
},buildFromTemplate:function(args,frag){
var _79e=false;
if(args["templatepath"]){
args["templatePath"]=args["templatepath"];
}
dojo.widget.fillFromTemplateCache(this,args["templatePath"],null,_79e);
var ts=dojo.widget._templateCache[this.templatePath?this.templatePath.toString():this.widgetType];
if((ts)&&(!_79e)){
if(!this.templateString.length){
this.templateString=ts["string"];
}
if(!this.templateNode){
this.templateNode=ts["node"];
}
}
var _7a0=false;
var node=null;
var tstr=this.templateString;
if((!this.templateNode)&&(this.templateString)){
_7a0=this.templateString.match(/\$\{([^\}]+)\}/g);
if(_7a0){
var hash=this.strings||{};
for(var key in dojo.widget.defaultStrings){
if(dojo.lang.isUndefined(hash[key])){
hash[key]=dojo.widget.defaultStrings[key];
}
}
for(var i=0;i<_7a0.length;i++){
var key=_7a0[i];
key=key.substring(2,key.length-1);
var kval=(key.substring(0,5)=="this.")?dojo.lang.getObjPathValue(key.substring(5),this):hash[key];
var _7a7;
if((kval)||(dojo.lang.isString(kval))){
_7a7=new String((dojo.lang.isFunction(kval))?kval.call(this,key,this.templateString):kval);
while(_7a7.indexOf("\"")>-1){
_7a7=_7a7.replace("\"","&quot;");
}
tstr=tstr.replace(_7a0[i],_7a7);
}
}
}else{
this.templateNode=this.createNodesFromText(this.templateString,true)[0];
if(!_79e){
ts.node=this.templateNode;
}
}
}
if((!this.templateNode)&&(!_7a0)){
dojo.debug("DomWidget.buildFromTemplate: could not create template");
return false;
}else{
if(!_7a0){
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
},attachTemplateNodes:function(_7a9,_7aa){
if(!_7a9){
_7a9=this.domNode;
}
if(!_7aa){
_7aa=this;
}
return dojo.widget.attachTemplateNodes(_7a9,_7aa,dojo.widget.getDojoEventsFromStr(this.templateString));
},fillInTemplate:function(){
},destroyRendering:function(){
try{
dojo.dom.destroyNode(this.domNode);
delete this.domNode;
}
catch(e){
}
if(this._sourceNodeRef){
try{
dojo.dom.destroyNode(this._sourceNodeRef);
}
catch(e){
}
}
},createNodesFromText:function(){
dojo.unimplemented("dojo.widget.DomWidget.createNodesFromText");
}});
dojo.provide("dojo.html.util");
dojo.html.getElementWindow=function(_7ab){
return dojo.html.getDocumentWindow(_7ab.ownerDocument);
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
var _7b3=dojo.html.getCursorPosition(e);
with(dojo.html){
var _7b4=getAbsolutePosition(node,true);
var bb=getBorderBox(node);
var _7b6=_7b4.x+(bb.width/2);
var _7b7=_7b4.y+(bb.height/2);
}
with(dojo.html.gravity){
return ((_7b3.x<_7b6?WEST:EAST)|(_7b3.y<_7b7?NORTH:SOUTH));
}
};
dojo.html.gravity.NORTH=1;
dojo.html.gravity.SOUTH=1<<1;
dojo.html.gravity.EAST=1<<2;
dojo.html.gravity.WEST=1<<3;
dojo.html.overElement=function(_7b8,e){
_7b8=dojo.byId(_7b8);
var _7ba=dojo.html.getCursorPosition(e);
var bb=dojo.html.getBorderBox(_7b8);
var _7bc=dojo.html.getAbsolutePosition(_7b8,true,dojo.html.boxSizing.BORDER_BOX);
var top=_7bc.y;
var _7be=top+bb.height;
var left=_7bc.x;
var _7c0=left+bb.width;
return (_7ba.x>=left&&_7ba.x<=_7c0&&_7ba.y>=top&&_7ba.y<=_7be);
};
dojo.html.renderedTextContent=function(node){
node=dojo.byId(node);
var _7c2="";
if(node==null){
return _7c2;
}
for(var i=0;i<node.childNodes.length;i++){
switch(node.childNodes[i].nodeType){
case 1:
case 5:
var _7c4="unknown";
try{
_7c4=dojo.html.getStyle(node.childNodes[i],"display");
}
catch(E){
}
switch(_7c4){
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
_7c2+="\n";
_7c2+=dojo.html.renderedTextContent(node.childNodes[i]);
_7c2+="\n";
break;
case "none":
break;
default:
if(node.childNodes[i].tagName&&node.childNodes[i].tagName.toLowerCase()=="br"){
_7c2+="\n";
}else{
_7c2+=dojo.html.renderedTextContent(node.childNodes[i]);
}
break;
}
break;
case 3:
case 2:
case 4:
var text=node.childNodes[i].nodeValue;
var _7c6="unknown";
try{
_7c6=dojo.html.getStyle(node,"text-transform");
}
catch(E){
}
switch(_7c6){
case "capitalize":
var _7c7=text.split(" ");
for(var i=0;i<_7c7.length;i++){
_7c7[i]=_7c7[i].charAt(0).toUpperCase()+_7c7[i].substring(1);
}
text=_7c7.join(" ");
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
switch(_7c6){
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
if(/\s$/.test(_7c2)){
text.replace(/^\s/,"");
}
break;
}
_7c2+=text;
break;
default:
break;
}
}
return _7c2;
};
dojo.html.createNodesFromText=function(txt,trim){
if(trim){
txt=txt.replace(/^\s+|\s+$/g,"");
}
var tn=dojo.doc().createElement("div");
tn.style.visibility="hidden";
dojo.body().appendChild(tn);
var _7cb="none";
if((/^<t[dh][\s\r\n>]/i).test(txt.replace(/^\s+/))){
txt="<table><tbody><tr>"+txt+"</tr></tbody></table>";
_7cb="cell";
}else{
if((/^<tr[\s\r\n>]/i).test(txt.replace(/^\s+/))){
txt="<table><tbody>"+txt+"</tbody></table>";
_7cb="row";
}else{
if((/^<(thead|tbody|tfoot)[\s\r\n>]/i).test(txt.replace(/^\s+/))){
txt="<table>"+txt+"</table>";
_7cb="section";
}
}
}
tn.innerHTML=txt;
if(tn["normalize"]){
tn.normalize();
}
var _7cc=null;
switch(_7cb){
case "cell":
_7cc=tn.getElementsByTagName("tr")[0];
break;
case "row":
_7cc=tn.getElementsByTagName("tbody")[0];
break;
case "section":
_7cc=tn.getElementsByTagName("table")[0];
break;
default:
_7cc=tn;
break;
}
var _7cd=[];
for(var x=0;x<_7cc.childNodes.length;x++){
_7cd.push(_7cc.childNodes[x].cloneNode(true));
}
tn.style.display="none";
dojo.html.destroyNode(tn);
return _7cd;
};
dojo.html.placeOnScreen=function(node,_7d0,_7d1,_7d2,_7d3,_7d4,_7d5){
if(_7d0 instanceof Array||typeof _7d0=="array"){
_7d5=_7d4;
_7d4=_7d3;
_7d3=_7d2;
_7d2=_7d1;
_7d1=_7d0[1];
_7d0=_7d0[0];
}
if(_7d4 instanceof String||typeof _7d4=="string"){
_7d4=_7d4.split(",");
}
if(!isNaN(_7d2)){
_7d2=[Number(_7d2),Number(_7d2)];
}else{
if(!(_7d2 instanceof Array||typeof _7d2=="array")){
_7d2=[0,0];
}
}
var _7d6=dojo.html.getScroll().offset;
var view=dojo.html.getViewport();
node=dojo.byId(node);
var _7d8=node.style.display;
node.style.display="";
var bb=dojo.html.getBorderBox(node);
var w=bb.width;
var h=bb.height;
node.style.display=_7d8;
if(!(_7d4 instanceof Array||typeof _7d4=="array")){
_7d4=["TL"];
}
var _7dc,_7dd,_7de=Infinity,_7df;
for(var _7e0=0;_7e0<_7d4.length;++_7e0){
var _7e1=_7d4[_7e0];
var _7e2=true;
var tryX=_7d0-(_7e1.charAt(1)=="L"?0:w)+_7d2[0]*(_7e1.charAt(1)=="L"?1:-1);
var tryY=_7d1-(_7e1.charAt(0)=="T"?0:h)+_7d2[1]*(_7e1.charAt(0)=="T"?1:-1);
if(_7d3){
tryX-=_7d6.x;
tryY-=_7d6.y;
}
if(tryX<0){
tryX=0;
_7e2=false;
}
if(tryY<0){
tryY=0;
_7e2=false;
}
var x=tryX+w;
if(x>view.width){
x=view.width-w;
_7e2=false;
}else{
x=tryX;
}
x=Math.max(_7d2[0],x)+_7d6.x;
var y=tryY+h;
if(y>view.height){
y=view.height-h;
_7e2=false;
}else{
y=tryY;
}
y=Math.max(_7d2[1],y)+_7d6.y;
if(_7e2){
_7dc=x;
_7dd=y;
_7de=0;
_7df=_7e1;
break;
}else{
var dist=Math.pow(x-tryX-_7d6.x,2)+Math.pow(y-tryY-_7d6.y,2);
if(_7de>dist){
_7de=dist;
_7dc=x;
_7dd=y;
_7df=_7e1;
}
}
}
if(!_7d5){
node.style.left=_7dc+"px";
node.style.top=_7dd+"px";
}
return {left:_7dc,top:_7dd,x:_7dc,y:_7dd,dist:_7de,corner:_7df};
};
dojo.html.placeOnScreenPoint=function(node,_7e9,_7ea,_7eb,_7ec){
dojo.deprecated("dojo.html.placeOnScreenPoint","use dojo.html.placeOnScreen() instead","0.5");
return dojo.html.placeOnScreen(node,_7e9,_7ea,_7eb,_7ec,["TL","TR","BL","BR"]);
};
dojo.html.placeOnScreenAroundElement=function(node,_7ee,_7ef,_7f0,_7f1,_7f2){
var best,_7f4=Infinity;
_7ee=dojo.byId(_7ee);
var _7f5=_7ee.style.display;
_7ee.style.display="";
var mb=dojo.html.getElementBox(_7ee,_7f0);
var _7f7=mb.width;
var _7f8=mb.height;
var _7f9=dojo.html.getAbsolutePosition(_7ee,true,_7f0);
_7ee.style.display=_7f5;
for(var _7fa in _7f1){
var pos,_7fc,_7fd;
var _7fe=_7f1[_7fa];
_7fc=_7f9.x+(_7fa.charAt(1)=="L"?0:_7f7);
_7fd=_7f9.y+(_7fa.charAt(0)=="T"?0:_7f8);
pos=dojo.html.placeOnScreen(node,_7fc,_7fd,_7ef,true,_7fe,true);
if(pos.dist==0){
best=pos;
break;
}else{
if(_7f4>pos.dist){
_7f4=pos.dist;
best=pos;
}
}
}
if(!_7f2){
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
if(dojo.html.getBorderBox(node.parentNode).height<=node.parentNode.scrollHeight){
node.scrollIntoView(false);
}
}else{
if(dojo.render.html.mozilla){
node.scrollIntoView(false);
}else{
var _800=node.parentNode;
var _801=_800.scrollTop+dojo.html.getBorderBox(_800).height;
var _802=node.offsetTop+dojo.html.getMarginBox(node).height;
if(_801<_802){
_800.scrollTop+=(_802-_801);
}else{
if(_800.scrollTop>node.offsetTop){
_800.scrollTop-=(_800.scrollTop-node.offsetTop);
}
}
}
}
};
dojo.provide("dojo.lfx.toggle");
dojo.lfx.toggle.plain={show:function(node,_804,_805,_806){
dojo.html.show(node);
if(dojo.lang.isFunction(_806)){
_806();
}
},hide:function(node,_808,_809,_80a){
dojo.html.hide(node);
if(dojo.lang.isFunction(_80a)){
_80a();
}
}};
dojo.lfx.toggle.fade={show:function(node,_80c,_80d,_80e){
dojo.lfx.fadeShow(node,_80c,_80d,_80e).play();
},hide:function(node,_810,_811,_812){
dojo.lfx.fadeHide(node,_810,_811,_812).play();
}};
dojo.lfx.toggle.wipe={show:function(node,_814,_815,_816){
dojo.lfx.wipeIn(node,_814,_815,_816).play();
},hide:function(node,_818,_819,_81a){
dojo.lfx.wipeOut(node,_818,_819,_81a).play();
}};
dojo.lfx.toggle.explode={show:function(node,_81c,_81d,_81e,_81f){
dojo.lfx.explode(_81f||{x:0,y:0,width:0,height:0},node,_81c,_81d,_81e).play();
},hide:function(node,_821,_822,_823,_824){
dojo.lfx.implode(node,_824||{x:0,y:0,width:0,height:0},_821,_822,_823).play();
}};
dojo.provide("dojo.widget.HtmlWidget");
dojo.declare("dojo.widget.HtmlWidget",dojo.widget.DomWidget,{templateCssPath:null,templatePath:null,lang:"",toggle:"plain",toggleDuration:150,initialize:function(args,frag){
},postMixInProperties:function(args,frag){
if(this.lang===""){
this.lang=null;
}
this.toggleObj=dojo.lfx.toggle[this.toggle.toLowerCase()]||dojo.lfx.toggle.plain;
},createNodesFromText:function(txt,wrap){
return dojo.html.createNodesFromText(txt,wrap);
},destroyRendering:function(_82b){
try{
if(this.bgIframe){
this.bgIframe.remove();
delete this.bgIframe;
}
if(!_82b&&this.domNode){
dojo.event.browser.clean(this.domNode);
}
dojo.widget.HtmlWidget.superclass.destroyRendering.call(this);
}
catch(e){
}
},isShowing:function(){
return dojo.html.isShowing(this.domNode);
},toggleShowing:function(){
if(this.isShowing()){
this.hide();
}else{
this.show();
}
},show:function(){
if(this.isShowing()){
return;
}
this.animationInProgress=true;
this.toggleObj.show(this.domNode,this.toggleDuration,null,dojo.lang.hitch(this,this.onShow),this.explodeSrc);
},onShow:function(){
this.animationInProgress=false;
this.checkSize();
},hide:function(){
if(!this.isShowing()){
return;
}
this.animationInProgress=true;
this.toggleObj.hide(this.domNode,this.toggleDuration,null,dojo.lang.hitch(this,this.onHide),this.explodeSrc);
},onHide:function(){
this.animationInProgress=false;
},_isResized:function(w,h){
if(!this.isShowing()){
return false;
}
var wh=dojo.html.getMarginBox(this.domNode);
var _82f=w||wh.width;
var _830=h||wh.height;
if(this.width==_82f&&this.height==_830){
return false;
}
this.width=_82f;
this.height=_830;
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
dojo.lang.forEach(this.children,function(_833){
if(_833.checkSize){
_833.checkSize();
}
});
}});
dojo.kwCompoundRequire({common:["dojo.xml.Parse","dojo.widget.Widget","dojo.widget.Parse","dojo.widget.Manager"],browser:["dojo.widget.DomWidget","dojo.widget.HtmlWidget"],dashboard:["dojo.widget.DomWidget","dojo.widget.HtmlWidget"],svg:["dojo.widget.SvgWidget"],rhino:["dojo.widget.SwtWidget"]});
dojo.provide("dojo.widget.*");
dojo.kwCompoundRequire({common:["dojo.html.common","dojo.html.style"]});
dojo.provide("dojo.html.*");
dojo.provide("dojo.widget.html.stabile");
dojo.widget.html.stabile={_sqQuotables:new RegExp("([\\\\'])","g"),_depth:0,_recur:false,depthLimit:2};
dojo.widget.html.stabile.getState=function(id){
dojo.widget.html.stabile.setup();
return dojo.widget.html.stabile.widgetState[id];
};
dojo.widget.html.stabile.setState=function(id,_836,_837){
dojo.widget.html.stabile.setup();
dojo.widget.html.stabile.widgetState[id]=_836;
if(_837){
dojo.widget.html.stabile.commit(dojo.widget.html.stabile.widgetState);
}
};
dojo.widget.html.stabile.setup=function(){
if(!dojo.widget.html.stabile.widgetState){
var text=dojo.widget.html.stabile._getStorage().value;
dojo.widget.html.stabile.widgetState=text?dj_eval("("+text+")"):{};
}
};
dojo.widget.html.stabile.commit=function(_839){
dojo.widget.html.stabile._getStorage().value=dojo.widget.html.stabile.description(_839);
};
dojo.widget.html.stabile.description=function(v,_83b){
var _83c=dojo.widget.html.stabile._depth;
var _83d=function(){
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
if(_83c>=dojo.widget.html.stabile.depthLimit){
return "[ ... ]";
}
d="[";
var _840=true;
dojo.widget.html.stabile._depth++;
for(var i=0;i<v.length;i++){
if(_840){
_840=false;
}else{
d+=",";
}
d+=arguments.callee(v[i],_83b);
}
return d+"]";
}
if(v.constructor==Object||v.toString==_83d){
if(_83c>=dojo.widget.html.stabile.depthLimit){
return "{ ... }";
}
if(typeof (v.hasOwnProperty)!="function"&&v.prototype){
throw new Error("description: "+v+" not supported by script engine");
}
var _840=true;
d="{";
dojo.widget.html.stabile._depth++;
for(var key in v){
if(v[key]==void (0)||typeof (v[key])=="function"){
continue;
}
if(_840){
_840=false;
}else{
d+=", ";
}
var kd=key;
if(!kd.match(/^[a-zA-Z_][a-zA-Z0-9_]*$/)){
kd=arguments.callee(key,_83b);
}
d+=kd+": "+arguments.callee(v[key],_83b);
}
return d+"}";
}
if(_83b){
if(dojo.widget.html.stabile._recur){
var _844=Object.prototype.toString;
return _844.apply(v,[]);
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
dojo.widget.html.stabile._depth=_83c;
}
};
dojo.widget.html.stabile._getStorage=function(){
if(dojo.widget.html.stabile.dataField){
return dojo.widget.html.stabile.dataField;
}
var form=document.forms._dojo_form;
return dojo.widget.html.stabile.dataField=form?form.stabile:{value:""};
};
dojo.provide("dojo.html.selection");
dojo.html.selectionType={NONE:0,TEXT:1,CONTROL:2};
dojo.html.clearSelection=function(){
var _846=dojo.global();
var _847=dojo.doc();
try{
if(_846["getSelection"]){
if(dojo.render.html.safari){
_846.getSelection().collapse();
}else{
_846.getSelection().removeAllRanges();
}
}else{
if(_847.selection){
if(_847.selection.empty){
_847.selection.empty();
}else{
if(_847.selection.clear){
_847.selection.clear();
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
dojo.html.disableSelection=function(_848){
_848=dojo.byId(_848)||dojo.body();
var h=dojo.render.html;
if(h.mozilla){
_848.style.MozUserSelect="none";
}else{
if(h.safari){
_848.style.KhtmlUserSelect="none";
}else{
if(h.ie){
_848.unselectable="on";
}else{
return false;
}
}
}
return true;
};
dojo.html.enableSelection=function(_84a){
_84a=dojo.byId(_84a)||dojo.body();
var h=dojo.render.html;
if(h.mozilla){
_84a.style.MozUserSelect="";
}else{
if(h.safari){
_84a.style.KhtmlUserSelect="";
}else{
if(h.ie){
_84a.unselectable="off";
}else{
return false;
}
}
}
return true;
};
dojo.html.selectElement=function(_84c){
dojo.deprecated("dojo.html.selectElement","replaced by dojo.html.selection.selectElementChildren",0.5);
};
dojo.html.selectInputText=function(_84d){
var _84e=dojo.global();
var _84f=dojo.doc();
_84d=dojo.byId(_84d);
if(_84f["selection"]&&dojo.body()["createTextRange"]){
var _850=_84d.createTextRange();
_850.moveStart("character",0);
_850.moveEnd("character",_84d.value.length);
_850.select();
}else{
if(_84e["getSelection"]){
var _851=_84e.getSelection();
_84d.setSelectionRange(0,_84d.value.length);
}
}
_84d.focus();
};
dojo.html.isSelectionCollapsed=function(){
dojo.deprecated("dojo.html.isSelectionCollapsed","replaced by dojo.html.selection.isCollapsed",0.5);
return dojo.html.selection.isCollapsed();
};
dojo.lang.mixin(dojo.html.selection,{getType:function(){
if(dojo.doc()["selection"]){
return dojo.html.selectionType[dojo.doc().selection.type.toUpperCase()];
}else{
var _852=dojo.html.selectionType.TEXT;
var oSel;
try{
oSel=dojo.global().getSelection();
}
catch(e){
}
if(oSel&&oSel.rangeCount==1){
var _854=oSel.getRangeAt(0);
if(_854.startContainer==_854.endContainer&&(_854.endOffset-_854.startOffset)==1&&_854.startContainer.nodeType!=dojo.dom.TEXT_NODE){
_852=dojo.html.selectionType.CONTROL;
}
}
return _852;
}
},isCollapsed:function(){
var _855=dojo.global();
var _856=dojo.doc();
if(_856["selection"]){
return _856.selection.createRange().text=="";
}else{
if(_855["getSelection"]){
var _857=_855.getSelection();
if(dojo.lang.isString(_857)){
return _857=="";
}else{
return _857.isCollapsed||_857.toString()=="";
}
}
}
},getSelectedElement:function(){
if(dojo.html.selection.getType()==dojo.html.selectionType.CONTROL){
if(dojo.doc()["selection"]){
var _858=dojo.doc().selection.createRange();
if(_858&&_858.item){
return dojo.doc().selection.createRange().item(0);
}
}else{
var _859=dojo.global().getSelection();
return _859.anchorNode.childNodes[_859.anchorOffset];
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
var _85b=dojo.global().getSelection();
if(_85b){
var node=_85b.anchorNode;
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
var _85d=dojo.global().getSelection();
if(_85d){
return _85d.toString();
}
}
},getSelectedHtml:function(){
if(dojo.doc()["selection"]){
if(dojo.html.selection.getType()==dojo.html.selectionType.CONTROL){
return null;
}
return dojo.doc().selection.createRange().htmlText;
}else{
var _85e=dojo.global().getSelection();
if(_85e&&_85e.rangeCount){
var frag=_85e.getRangeAt(0).cloneContents();
var div=document.createElement("div");
div.appendChild(frag);
return div.innerHTML;
}
return null;
}
},hasAncestorElement:function(_861){
return (dojo.html.selection.getAncestorElement.apply(this,arguments)!=null);
},getAncestorElement:function(_862){
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
},selectElement:function(_867){
var _868=dojo.global();
var _869=dojo.doc();
_867=dojo.byId(_867);
if(_869.selection&&dojo.body().createTextRange){
try{
var _86a=dojo.body().createControlRange();
_86a.addElement(_867);
_86a.select();
}
catch(e){
dojo.html.selection.selectElementChildren(_867);
}
}else{
if(_868["getSelection"]){
var _86b=_868.getSelection();
if(_86b["removeAllRanges"]){
var _86a=_869.createRange();
_86a.selectNode(_867);
_86b.removeAllRanges();
_86b.addRange(_86a);
}
}
}
},selectElementChildren:function(_86c){
var _86d=dojo.global();
var _86e=dojo.doc();
_86c=dojo.byId(_86c);
if(_86e.selection&&dojo.body().createTextRange){
var _86f=dojo.body().createTextRange();
_86f.moveToElementText(_86c);
_86f.select();
}else{
if(_86d["getSelection"]){
var _870=_86d.getSelection();
if(_870["setBaseAndExtent"]){
_870.setBaseAndExtent(_86c,0,_86c,_86c.innerText.length-1);
}else{
if(_870["selectAllChildren"]){
_870.selectAllChildren(_86c);
}
}
}
}
},getBookmark:function(){
var _871;
var _872=dojo.doc();
if(_872["selection"]){
var _873=_872.selection.createRange();
_871=_873.getBookmark();
}else{
var _874;
try{
_874=dojo.global().getSelection();
}
catch(e){
}
if(_874){
var _873=_874.getRangeAt(0);
_871=_873.cloneRange();
}else{
dojo.debug("No idea how to store the current selection for this browser!");
}
}
return _871;
},moveToBookmark:function(_875){
var _876=dojo.doc();
if(_876["selection"]){
var _877=_876.selection.createRange();
_877.moveToBookmark(_875);
_877.select();
}else{
var _878;
try{
_878=dojo.global().getSelection();
}
catch(e){
}
if(_878&&_878["removeAllRanges"]){
_878.removeAllRanges();
_878.addRange(_875);
}else{
dojo.debug("No idea how to restore selection for this browser!");
}
}
},collapse:function(_879){
if(dojo.global()["getSelection"]){
var _87a=dojo.global().getSelection();
if(_87a.removeAllRanges){
if(_879){
_87a.collapseToStart();
}else{
_87a.collapseToEnd();
}
}else{
dojo.global().getSelection().collapse(_879);
}
}else{
if(dojo.doc().selection){
var _87b=dojo.doc().selection.createRange();
_87b.collapse(_879);
_87b.select();
}
}
},remove:function(){
if(dojo.doc().selection){
var _87c=dojo.doc().selection;
if(_87c.type.toUpperCase()!="NONE"){
_87c.clear();
}
return _87c;
}else{
var _87c=dojo.global().getSelection();
for(var i=0;i<_87c.rangeCount;i++){
_87c.getRangeAt(i).deleteContents();
}
return _87c;
}
}});
dojo.provide("dojo.html.iframe");
dojo.html.iframeContentWindow=function(_87e){
var win=dojo.html.getDocumentWindow(dojo.html.iframeContentDocument(_87e))||dojo.html.iframeContentDocument(_87e).__parent__||(_87e.name&&document.frames[_87e.name])||null;
return win;
};
dojo.html.iframeContentDocument=function(_880){
var doc=_880.contentDocument||((_880.contentWindow)&&(_880.contentWindow.document))||((_880.name)&&(document.frames[_880.name])&&(document.frames[_880.name].document))||null;
return doc;
};
dojo.html.BackgroundIframe=function(node){
if(dojo.render.html.ie55||dojo.render.html.ie60){
var html="<iframe src='javascript:false'"+" style='position: absolute; left: 0px; top: 0px; width: 100%; height: 100%;"+"z-index: -1; filter:Alpha(Opacity=\"0\");' "+">";
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
var _884=dojo.html.getMarginBox(this.domNode);
if(_884.width==0||_884.height==0){
dojo.lang.setTimeout(this,this.onResized,100);
return;
}
this.iframe.style.width=_884.width+"px";
this.iframe.style.height=_884.height+"px";
}
},size:function(node){
if(!this.iframe){
return;
}
var _886=dojo.html.toCoordinateObject(node,true,dojo.html.boxSizing.BORDER_BOX);
with(this.iframe.style){
width=_886.width+"px";
height=_886.height+"px";
left=_886.left+"px";
top=_886.top+"px";
}
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
if(this.iframe){
this.iframe.style.display="block";
}
},hide:function(){
if(this.iframe){
this.iframe.style.display="none";
}
},remove:function(){
if(this.iframe){
dojo.html.removeNode(this.iframe,true);
delete this.iframe;
this.iframe=null;
}
}});
dojo.provide("dojo.widget.PopupContainer");
dojo.declare("dojo.widget.PopupContainerBase",null,function(){
this.queueOnAnimationFinish=[];
},{isShowingNow:false,currentSubpopup:null,beginZIndex:1000,parentPopup:null,parent:null,popupIndex:0,aroundBox:dojo.html.boxSizing.BORDER_BOX,openedForWindow:null,processKey:function(evt){
return false;
},applyPopupBasicStyle:function(){
with(this.domNode.style){
display="none";
position="absolute";
}
},aboutToShow:function(){
},open:function(x,y,_88b,_88c,_88d,_88e){
if(this.isShowingNow){
return;
}
if(this.animationInProgress){
this.queueOnAnimationFinish.push(this.open,arguments);
return;
}
this.aboutToShow();
var _88f=false,node,_891;
if(typeof x=="object"){
node=x;
_891=_88c;
_88c=_88b;
_88b=y;
_88f=true;
}
this.parent=_88b;
dojo.body().appendChild(this.domNode);
_88c=_88c||_88b["domNode"]||[];
var _892=null;
this.isTopLevel=true;
while(_88b){
if(_88b!==this&&(_88b.setOpenedSubpopup!=undefined&&_88b.applyPopupBasicStyle!=undefined)){
_892=_88b;
this.isTopLevel=false;
_892.setOpenedSubpopup(this);
break;
}
_88b=_88b.parent;
}
this.parentPopup=_892;
this.popupIndex=_892?_892.popupIndex+1:1;
if(this.isTopLevel){
var _893=dojo.html.isNode(_88c)?_88c:null;
dojo.widget.PopupManager.opened(this,_893);
}
if(this.isTopLevel&&!dojo.withGlobal(this.openedForWindow||dojo.global(),dojo.html.selection.isCollapsed)){
this._bookmark=dojo.withGlobal(this.openedForWindow||dojo.global(),dojo.html.selection.getBookmark);
}else{
this._bookmark=null;
}
if(_88c instanceof Array){
_88c={left:_88c[0],top:_88c[1],width:0,height:0};
}
with(this.domNode.style){
display="";
zIndex=this.beginZIndex+this.popupIndex;
}
if(_88f){
this.move(node,_88e,_891);
}else{
this.move(x,y,_88e,_88d);
}
this.domNode.style.display="none";
this.explodeSrc=_88c;
this.show();
this.isShowingNow=true;
},move:function(x,y,_896,_897){
var _898=(typeof x=="object");
if(_898){
var _899=_896;
var node=x;
_896=y;
if(!_899){
_899={"BL":"TL","TL":"BL"};
}
dojo.html.placeOnScreenAroundElement(this.domNode,node,_896,this.aroundBox,_899);
}else{
if(!_897){
_897="TL,TR,BL,BR";
}
dojo.html.placeOnScreen(this.domNode,x,y,_896,true,_897);
}
},close:function(_89b){
if(_89b){
this.domNode.style.display="none";
}
if(this.animationInProgress){
this.queueOnAnimationFinish.push(this.close,[]);
return;
}
this.closeSubpopup(_89b);
this.hide();
if(this.bgIframe){
this.bgIframe.hide();
this.bgIframe.size({left:0,top:0,width:0,height:0});
}
if(this.isTopLevel){
dojo.widget.PopupManager.closed(this);
}
this.isShowingNow=false;
if(this.parent){
setTimeout(dojo.lang.hitch(this,function(){
try{
if(this.parent["focus"]){
this.parent.focus();
}else{
this.parent.domNode.focus();
}
}
catch(e){
dojo.debug("No idea how to focus to parent",e);
}
}),10);
}
if(this._bookmark&&dojo.withGlobal(this.openedForWindow||dojo.global(),dojo.html.selection.isCollapsed)){
if(this.openedForWindow){
this.openedForWindow.focus();
}
try{
dojo.withGlobal(this.openedForWindow||dojo.global(),"moveToBookmark",dojo.html.selection,[this._bookmark]);
}
catch(e){
}
}
this._bookmark=null;
},closeAll:function(_89c){
if(this.parentPopup){
this.parentPopup.closeAll(_89c);
}else{
this.close(_89c);
}
},setOpenedSubpopup:function(_89d){
this.currentSubpopup=_89d;
},closeSubpopup:function(_89e){
if(this.currentSubpopup==null){
return;
}
this.currentSubpopup.close(_89e);
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
dojo.widget.defineWidget("dojo.widget.PopupContainer",[dojo.widget.HtmlWidget,dojo.widget.PopupContainerBase],{isContainer:true,fillInTemplate:function(){
this.applyPopupBasicStyle();
dojo.widget.PopupContainer.superclass.fillInTemplate.apply(this,arguments);
}});
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
this.registerAllWindows=function(_8a2){
if(!_8a2){
_8a2=dojo.html.getDocumentWindow(window.top&&window.top.document||window.document);
}
this.registerWin(_8a2);
for(var i=0;i<_8a2.frames.length;i++){
try{
var win=dojo.html.getDocumentWindow(_8a2.frames[i].document);
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
this.opened=function(menu,_8a9){
if(menu==this.currentMenu){
return;
}
if(this.currentMenu){
this.currentMenu.close();
}
this.currentMenu=menu;
this.currentFocusMenu=menu;
this.currentButton=_8a9;
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
m=m.parentPopup||m.parentMenu;
}
},this.onClick=function(e){
if(!this.currentMenu){
return;
}
var _8ae=dojo.html.getScroll().offset;
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
this.currentMenu.closeAll(true);
};
};
dojo.provide("dojo.widget.ComboBox");
dojo.declare("dojo.widget.incrementalComboBoxDataProvider",null,function(_8b0){
this.searchUrl=_8b0.dataUrl;
this._cache={};
this._inFlight=false;
this._lastRequest=null;
this.allowCache=false;
},{_addToCache:function(_8b1,data){
if(this.allowCache){
this._cache[_8b1]=data;
}
},startSearch:function(_8b3,_8b4){
if(this._inFlight){
}
var tss=encodeURIComponent(_8b3);
var _8b6=dojo.string.substituteParams(this.searchUrl,{"searchString":tss});
var _8b7=this;
var _8b8=this._lastRequest=dojo.io.bind({url:_8b6,method:"get",mimetype:"text/json",load:function(type,data,evt){
_8b7._inFlight=false;
if(!dojo.lang.isArray(data)){
var _8bc=[];
for(var key in data){
_8bc.push([data[key],key]);
}
data=_8bc;
}
_8b7._addToCache(_8b3,data);
if(_8b8==_8b7._lastRequest){
_8b4(data);
}
}});
this._inFlight=true;
}});
dojo.declare("dojo.widget.basicComboBoxDataProvider",null,function(_8be,node){
this._data=[];
this.searchLimit=30;
this.searchType="STARTSTRING";
this.caseSensitive=false;
if(!dj_undef("dataUrl",_8be)&&!dojo.string.isBlank(_8be.dataUrl)){
this._getData(_8be.dataUrl);
}else{
if((node)&&(node.nodeName.toLowerCase()=="select")){
var opts=node.getElementsByTagName("option");
var ol=opts.length;
var data=[];
for(var x=0;x<ol;x++){
var text=opts[x].textContent||opts[x].innerText||opts[x].innerHTML;
var _8c5=[String(text),String(opts[x].value)];
data.push(_8c5);
if(opts[x].selected){
_8be.setAllValues(_8c5[0],_8c5[1]);
}
}
this.setData(data);
}
}
},{_getData:function(url){
dojo.io.bind({url:url,load:dojo.lang.hitch(this,function(type,data,evt){
if(!dojo.lang.isArray(data)){
var _8ca=[];
for(var key in data){
_8ca.push([data[key],key]);
}
data=_8ca;
}
this.setData(data);
}),mimetype:"text/json"});
},startSearch:function(_8cc,_8cd){
this._performSearch(_8cc,_8cd);
},_performSearch:function(_8ce,_8cf){
var st=this.searchType;
var ret=[];
if(!this.caseSensitive){
_8ce=_8ce.toLowerCase();
}
for(var x=0;x<this._data.length;x++){
if((this.searchLimit>0)&&(ret.length>=this.searchLimit)){
break;
}
var _8d3=new String((!this.caseSensitive)?this._data[x][0].toLowerCase():this._data[x][0]);
if(_8d3.length<_8ce.length){
continue;
}
if(st=="STARTSTRING"){
if(_8ce==_8d3.substr(0,_8ce.length)){
ret.push(this._data[x]);
}
}else{
if(st=="SUBSTRING"){
if(_8d3.indexOf(_8ce)>=0){
ret.push(this._data[x]);
}
}else{
if(st=="STARTWORD"){
var idx=_8d3.indexOf(_8ce);
if(idx==0){
ret.push(this._data[x]);
}
if(idx<=0){
continue;
}
var _8d5=false;
while(idx!=-1){
if(" ,/(".indexOf(_8d3.charAt(idx-1))!=-1){
_8d5=true;
break;
}
idx=_8d3.indexOf(_8ce,idx+1);
}
if(!_8d5){
continue;
}else{
ret.push(this._data[x]);
}
}
}
}
}
_8cf(ret);
},setData:function(_8d6){
this._data=_8d6;
}});
dojo.widget.defineWidget("dojo.widget.ComboBox",dojo.widget.HtmlWidget,{forceValidOption:false,searchType:"stringstart",dataProvider:null,autoComplete:true,searchDelay:100,dataUrl:"",fadeTime:200,maxListLength:8,mode:"local",selectedResult:null,dataProviderClass:"",buttonSrc:dojo.uri.moduleUri("dojo.widget","templates/images/combo_box_arrow.png"),dropdownToggle:"fade",templateString:"<span _=\"whitespace and CR's between tags adds &nbsp; in FF\"\n\tclass=\"dojoComboBoxOuter\"\n\t><input style=\"display:none\"  tabindex=\"-1\" name=\"\" value=\"\" \n\t\tdojoAttachPoint=\"comboBoxValue\"\n\t><input style=\"display:none\"  tabindex=\"-1\" name=\"\" value=\"\" \n\t\tdojoAttachPoint=\"comboBoxSelectionValue\"\n\t><input type=\"text\" autocomplete=\"off\" class=\"dojoComboBox\"\n\t\tdojoAttachEvent=\"key:_handleKeyEvents; keyUp: onKeyUp; compositionEnd; onResize;\"\n\t\tdojoAttachPoint=\"textInputNode\"\n\t><img hspace=\"0\"\n\t\tvspace=\"0\"\n\t\tclass=\"dojoComboBox\"\n\t\tdojoAttachPoint=\"downArrowNode\"\n\t\tdojoAttachEvent=\"onMouseUp: handleArrowClick; onResize;\"\n\t\tsrc=\"${this.buttonSrc}\"\n></span>\n",templateCssString:".dojoComboBoxOuter {\n\tborder: 0px !important;\n\tmargin: 0px !important;\n\tpadding: 0px !important;\n\tbackground: transparent !important;\n\twhite-space: nowrap !important;\n}\n\n.dojoComboBox {\n\tborder: 1px inset #afafaf;\n\tmargin: 0px;\n\tpadding: 0px;\n\tvertical-align: middle !important;\n\tfloat: none !important;\n\tposition: static !important;\n\tdisplay: inline !important;\n}\n\n/* the input box */\ninput.dojoComboBox {\n\tborder-right-width: 0px !important; \n\tmargin-right: 0px !important;\n\tpadding-right: 0px !important;\n}\n\n/* the down arrow */\nimg.dojoComboBox {\n\tborder-left-width: 0px !important;\n\tpadding-left: 0px !important;\n\tmargin-left: 0px !important;\n}\n\n/* IE vertical-alignment calculations can be off by +-1 but these margins are collapsed away */\n.dj_ie img.dojoComboBox {\n\tmargin-top: 1px; \n\tmargin-bottom: 1px; \n}\n\n/* the drop down */\n.dojoComboBoxOptions {\n\tfont-family: Verdana, Helvetica, Garamond, sans-serif;\n\t/* font-size: 0.7em; */\n\tbackground-color: white;\n\tborder: 1px solid #afafaf;\n\tposition: absolute;\n\tz-index: 1000; \n\toverflow: auto;\n\tcursor: default;\n}\n\n.dojoComboBoxItem {\n\tpadding-left: 2px;\n\tpadding-top: 2px;\n\tmargin: 0px;\n}\n\n.dojoComboBoxItemEven {\n\tbackground-color: #f4f4f4;\n}\n\n.dojoComboBoxItemOdd {\n\tbackground-color: white;\n}\n\n.dojoComboBoxItemHighlight {\n\tbackground-color: #63709A;\n\tcolor: white;\n}\n",templateCssPath:dojo.uri.moduleUri("dojo.widget","templates/ComboBox.css"),setValue:function(_8d7){
this.comboBoxValue.value=_8d7;
if(this.textInputNode.value!=_8d7){
this.textInputNode.value=_8d7;
dojo.widget.html.stabile.setState(this.widgetId,this.getState(),true);
this.onValueChanged(_8d7);
}
},onValueChanged:function(_8d8){
},getValue:function(){
return this.comboBoxValue.value;
},getState:function(){
return {value:this.getValue()};
},setState:function(_8d9){
this.setValue(_8d9.value);
},enable:function(){
this.disabled=false;
this.textInputNode.removeAttribute("disabled");
},disable:function(){
this.disabled=true;
this.textInputNode.setAttribute("disabled",true);
},_getCaretPos:function(_8da){
if(dojo.lang.isNumber(_8da.selectionStart)){
return _8da.selectionStart;
}else{
if(dojo.render.html.ie){
var tr=document.selection.createRange().duplicate();
var ntr=_8da.createTextRange();
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
},_setCaretPos:function(_8dd,_8de){
_8de=parseInt(_8de);
this._setSelectedRange(_8dd,_8de,_8de);
},_setSelectedRange:function(_8df,_8e0,end){
if(!end){
end=_8df.value.length;
}
if(_8df.setSelectionRange){
_8df.focus();
_8df.setSelectionRange(_8e0,end);
}else{
if(_8df.createTextRange){
var _8e2=_8df.createTextRange();
with(_8e2){
collapse(true);
moveEnd("character",end);
moveStart("character",_8e0);
select();
}
}else{
_8df.value=_8df.value;
_8df.blur();
_8df.focus();
var dist=parseInt(_8df.value.length)-end;
var _8e4=String.fromCharCode(37);
var tcc=_8e4.charCodeAt(0);
for(var x=0;x<dist;x++){
var te=document.createEvent("KeyEvents");
te.initKeyEvent("keypress",true,true,null,false,false,false,false,tcc,tcc);
_8df.dispatchEvent(te);
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
var _8ea=true;
switch(evt.key){
case k.KEY_DOWN_ARROW:
if(!this.popupWidget.isShowingNow){
this._startSearchFromInput();
}
this._highlightNextOption();
dojo.event.browser.stopEvent(evt);
return;
case k.KEY_UP_ARROW:
this._highlightPrevOption();
dojo.event.browser.stopEvent(evt);
return;
case k.KEY_TAB:
if(!this.autoComplete&&this.popupWidget.isShowingNow&&this._highlighted_option){
dojo.event.browser.stopEvent(evt);
this._selectOption({"target":this._highlighted_option,"noHide":false});
this._setSelectedRange(this.textInputNode,this.textInputNode.value.length,null);
}else{
this._selectOption();
return;
}
break;
case k.KEY_ENTER:
if(this.popupWidget.isShowingNow){
dojo.event.browser.stopEvent(evt);
}
if(this.autoComplete){
this._selectOption();
return;
}
case " ":
if(this.popupWidget.isShowingNow&&this._highlighted_option){
dojo.event.browser.stopEvent(evt);
this._selectOption();
this._hideResultList();
return;
}
break;
case k.KEY_ESCAPE:
this._hideResultList();
this._prev_key_esc=true;
return;
case k.KEY_BACKSPACE:
this._prev_key_backspace=true;
if(!this.textInputNode.value.length){
this.setAllValues("","");
this._hideResultList();
_8ea=false;
}
break;
case k.KEY_RIGHT_ARROW:
case k.KEY_LEFT_ARROW:
_8ea=false;
break;
default:
if(evt.charCode==0){
_8ea=false;
}
}
if(this.searchTimer){
clearTimeout(this.searchTimer);
}
if(_8ea){
this._blurOptionNode();
this.searchTimer=setTimeout(dojo.lang.hitch(this,this._startSearchFromInput),this.searchDelay);
}
},compositionEnd:function(evt){
evt.key=evt.keyCode;
this._handleKeyEvents(evt);
},onKeyUp:function(evt){
this.setValue(this.textInputNode.value);
},setSelectedValue:function(_8ed){
this.comboBoxSelectionValue.value=_8ed;
},setAllValues:function(_8ee,_8ef){
this.setSelectedValue(_8ef);
this.setValue(_8ee);
},_focusOptionNode:function(node){
if(this._highlighted_option!=node){
this._blurOptionNode();
this._highlighted_option=node;
dojo.html.addClass(this._highlighted_option,"dojoComboBoxItemHighlight");
}
},_blurOptionNode:function(){
if(this._highlighted_option){
dojo.html.removeClass(this._highlighted_option,"dojoComboBoxItemHighlight");
this._highlighted_option=null;
}
},_highlightNextOption:function(){
if((!this._highlighted_option)||!this._highlighted_option.parentNode){
this._focusOptionNode(this.optionsListNode.firstChild);
}else{
if(this._highlighted_option.nextSibling){
this._focusOptionNode(this._highlighted_option.nextSibling);
}
}
dojo.html.scrollIntoView(this._highlighted_option);
},_highlightPrevOption:function(){
if(this._highlighted_option&&this._highlighted_option.previousSibling){
this._focusOptionNode(this._highlighted_option.previousSibling);
}else{
this._highlighted_option=null;
this._hideResultList();
return;
}
dojo.html.scrollIntoView(this._highlighted_option);
},_itemMouseOver:function(evt){
if(evt.target===this.optionsListNode){
return;
}
this._focusOptionNode(evt.target);
dojo.html.addClass(this._highlighted_option,"dojoComboBoxItemHighlight");
},_itemMouseOut:function(evt){
if(evt.target===this.optionsListNode){
return;
}
this._blurOptionNode();
},onResize:function(){
var _8f3=dojo.html.getContentBox(this.textInputNode);
if(_8f3.height<=0){
dojo.lang.setTimeout(this,"onResize",100);
return;
}
var _8f4={width:_8f3.height,height:_8f3.height};
dojo.html.setContentBox(this.downArrowNode,_8f4);
},fillInTemplate:function(args,frag){
dojo.html.applyBrowserClass(this.domNode);
var _8f7=this.getFragNodeRef(frag);
if(!this.name&&_8f7.name){
this.name=_8f7.name;
}
this.comboBoxValue.name=this.name;
this.comboBoxSelectionValue.name=this.name+"_selected";
dojo.html.copyStyle(this.domNode,_8f7);
dojo.html.copyStyle(this.textInputNode,_8f7);
dojo.html.copyStyle(this.downArrowNode,_8f7);
with(this.downArrowNode.style){
width="0px";
height="0px";
}
var _8f8;
if(this.dataProviderClass){
if(typeof this.dataProviderClass=="string"){
_8f8=dojo.evalObjPath(this.dataProviderClass);
}else{
_8f8=this.dataProviderClass;
}
}else{
if(this.mode=="remote"){
_8f8=dojo.widget.incrementalComboBoxDataProvider;
}else{
_8f8=dojo.widget.basicComboBoxDataProvider;
}
}
this.dataProvider=new _8f8(this,this.getFragNodeRef(frag));
this.popupWidget=new dojo.widget.createWidget("PopupContainer",{toggle:this.dropdownToggle,toggleDuration:this.toggleDuration});
dojo.event.connect(this,"destroy",this.popupWidget,"destroy");
this.optionsListNode=this.popupWidget.domNode;
this.domNode.appendChild(this.optionsListNode);
dojo.html.addClass(this.optionsListNode,"dojoComboBoxOptions");
dojo.event.connect(this.optionsListNode,"onclick",this,"_selectOption");
dojo.event.connect(this.optionsListNode,"onmouseover",this,"_onMouseOver");
dojo.event.connect(this.optionsListNode,"onmouseout",this,"_onMouseOut");
dojo.event.connect(this.optionsListNode,"onmouseover",this,"_itemMouseOver");
dojo.event.connect(this.optionsListNode,"onmouseout",this,"_itemMouseOut");
},_openResultList:function(_8f9){
if(this.disabled){
return;
}
this._clearResultList();
if(!_8f9.length){
this._hideResultList();
}
if((this.autoComplete)&&(_8f9.length)&&(!this._prev_key_backspace)&&(this.textInputNode.value.length>0)){
var cpos=this._getCaretPos(this.textInputNode);
if((cpos+1)>this.textInputNode.value.length){
this.textInputNode.value+=_8f9[0][0].substr(cpos);
this._setSelectedRange(this.textInputNode,cpos,this.textInputNode.value.length);
}
}
var even=true;
while(_8f9.length){
var tr=_8f9.shift();
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
this._showResultList();
},_onFocusInput:function(){
this._hasFocus=true;
},_onBlurInput:function(){
this._hasFocus=false;
this._handleBlurTimer(true,500);
},_handleBlurTimer:function(_8fe,_8ff){
if(this.blurTimer&&(_8fe||_8ff)){
clearTimeout(this.blurTimer);
}
if(_8ff){
this.blurTimer=dojo.lang.setTimeout(this,"_checkBlurred",_8ff);
}
},_onMouseOver:function(evt){
if(!this._mouseover_list){
this._handleBlurTimer(true,0);
this._mouseover_list=true;
}
},_onMouseOut:function(evt){
var _902=evt.relatedTarget;
try{
if(!_902||_902.parentNode!=this.optionsListNode){
this._mouseover_list=false;
this._handleBlurTimer(true,100);
this._tryFocus();
}
}
catch(e){
}
},_isInputEqualToResult:function(_903){
var _904=this.textInputNode.value;
if(!this.dataProvider.caseSensitive){
_904=_904.toLowerCase();
_903=_903.toLowerCase();
}
return (_904==_903);
},_isValidOption:function(){
var tgt=dojo.html.firstElement(this.optionsListNode);
var _906=false;
while(!_906&&tgt){
if(this._isInputEqualToResult(tgt.getAttribute("resultName"))){
_906=true;
}else{
tgt=dojo.html.nextElement(tgt);
}
}
return _906;
},_checkBlurred:function(){
if(!this._hasFocus&&!this._mouseover_list){
this._hideResultList();
if(!this.textInputNode.value.length){
this.setAllValues("","");
return;
}
var _907=this._isValidOption();
if(this.forceValidOption&&!_907){
this.setAllValues("","");
return;
}
if(!_907){
//commented this because on "tab" event null values are submitted
//this.setSelectedValue("");
}
}
},_selectOption:function(evt){
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
this._hideResultList();
this._setSelectedRange(this.textInputNode,0,null);
}
this._tryFocus();
},_clearResultList:function(){
if(this.optionsListNode.innerHTML){
this.optionsListNode.innerHTML="";
}
},_hideResultList:function(){
this.popupWidget.close();
},_showResultList:function(){
var _90a=this.optionsListNode.childNodes;
if(_90a.length){
var _90b=Math.min(_90a.length,this.maxListLength);
with(this.optionsListNode.style){
display="";
if(_90b==_90a.length){
height="";
}else{
height=_90b*dojo.html.getMarginBox(_90a[0]).height+"px";
}
width=(dojo.html.getMarginBox(this.domNode).width-2)+"px";
}
this.popupWidget.open(this.domNode,this,this.downArrowNode);
}else{
this._hideResultList();
}
},handleArrowClick:function(){
this._handleBlurTimer(true,0);
this._tryFocus();
if(this.popupWidget.isShowingNow){
this._hideResultList();
}else{
this._startSearch("");
}
},_tryFocus:function(){
try{
this.textInputNode.focus();
}
catch(e){
}
},_startSearchFromInput:function(){
this._startSearch(this.textInputNode.value);
},_startSearch:function(key){
this.dataProvider.startSearch(key,dojo.lang.hitch(this,"_openResultList"));
},postCreate:function(){
this.onResize();
dojo.event.connect(this.textInputNode,"onblur",this,"_onBlurInput");
dojo.event.connect(this.textInputNode,"onfocus",this,"_onFocusInput");
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
var _910=this.getFragNodeRef(frag);
dojo.html.copyStyle(this.domNode,_910);
dojo.widget.PageContainer.superclass.fillInTemplate.apply(this,arguments);
},postCreate:function(args,frag){
if(this.children.length){
dojo.lang.forEach(this.children,this._setupChild,this);
var _913;
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
},addChild:function(_915){
dojo.widget.PageContainer.superclass.addChild.apply(this,arguments);
this._setupChild(_915);
this.onResized();
if(!this.selectedChildWidget){
this.selectChild(_915);
}
},_setupChild:function(page){
page.hide();
page.domNode.style.position="relative";
dojo.event.topic.publish(this.widgetId+"-addChild",page);
},removeChild:function(page){
dojo.widget.PageContainer.superclass.removeChild.apply(this,arguments);
if(this._beingDestroyed){
return;
}
dojo.event.topic.publish(this.widgetId+"-removeChild",page);
this.onResized();
if(this.selectedChildWidget===page){
this.selectedChildWidget=undefined;
if(this.children.length>0){
this.selectChild(this.children[0],true);
}
}
},selectChild:function(page,_919){
page=dojo.widget.byId(page);
this.correspondingPageButton=_919;
if(this.selectedChildWidget){
this._hideChild(this.selectedChildWidget);
}
this.selectedChildWidget=page;
this.selectedChild=page.widgetId;
this._showChild(page);
page.isFirstChild=(page==this.children[0]);
page.isLastChild=(page==this.children[this.children.length-1]);
dojo.event.topic.publish(this.widgetId+"-selectChild",page);
},forward:function(){
var _91a=dojo.lang.find(this.children,this.selectedChildWidget);
this.selectChild(this.children[_91a+1]);
},back:function(){
var _91b=dojo.lang.find(this.children,this.selectedChildWidget);
this.selectChild(this.children[_91b-1]);
},onResized:function(){
if(this.doLayout&&this.selectedChildWidget){
with(this.selectedChildWidget.domNode.style){
top=dojo.html.getPixelValue(this.containerNode,"padding-top",true);
left=dojo.html.getPixelValue(this.containerNode,"padding-left",true);
}
var _91c=dojo.html.getContentBox(this.containerNode);
this.selectedChildWidget.resizeTo(_91c.width,_91c.height);
}
},_showChild:function(page){
if(this.doLayout){
var _91e=dojo.html.getContentBox(this.containerNode);
page.resizeTo(_91e.width,_91e.height);
}
page.selected=true;
page.show();
},_hideChild:function(page){
page.selected=false;
page.hide();
},closeChild:function(page){
var _921=page.onClose(this,page);
if(_921){
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
var _922=dojo.widget.byId(this.containerId);
if(_922){
dojo.lang.forEach(_922.children,this.onAddChild,this);
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
var _924=dojo.widget.createWidget(this.buttonWidget,{label:page.label,closeButton:page.closable});
this.addChild(_924);
this.domNode.appendChild(_924.domNode);
this.pane2button[page]=_924;
page.controlButton=_924;
var _925=this;
dojo.event.connect(_924,"onClick",function(){
_925.onButtonClick(page);
});
dojo.event.connect(_924,"onCloseButtonClick",function(){
_925.onCloseButtonClick(page);
});
},onRemoveChild:function(page){
if(this._currentChild==page){
this._currentChild=null;
}
var _927=this.pane2button[page];
if(_927){
_927.destroy();
}
this.pane2button[page]=null;
},onSelectChild:function(page){
if(this._currentChild){
var _929=this.pane2button[this._currentChild];
_929.clearSelected();
}
var _92a=this.pane2button[page];
_92a.setSelected();
this._currentChild=page;
},onButtonClick:function(page){
var _92c=dojo.widget.byId(this.containerId);
_92c.selectChild(page,false,this);
},onCloseButtonClick:function(page){
var _92e=dojo.widget.byId(this.containerId);
_92e.closeChild(page);
},onKey:function(evt){
if((evt.keyCode==evt.KEY_RIGHT_ARROW)||(evt.keyCode==evt.KEY_LEFT_ARROW)){
var _930=0;
var next=null;
var _930=dojo.lang.find(this.children,this.pane2button[this._currentChild]);
if(evt.keyCode==evt.KEY_RIGHT_ARROW){
next=this.children[(_930+1)%this.children.length];
}else{
next=this.children[(_930+(this.children.length-1))%this.children.length];
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
dojo.html.addClass(this.domNode,"currentTab");
this.titleNode.setAttribute("tabIndex","0");
},clearSelected:function(){
dojo.html.removeClass(this.domNode,"currentTab");
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
dojo.widget.html.layout=function(_933,_934,_935){
dojo.html.addClass(_933,"dojoLayoutContainer");
_934=dojo.lang.filter(_934,function(_936,idx){
_936.idx=idx;
return dojo.lang.inArray(["top","bottom","left","right","client","flood"],_936.layoutAlign);
});
if(_935&&_935!="none"){
var rank=function(_939){
switch(_939.layoutAlign){
case "flood":
return 1;
case "left":
case "right":
return (_935=="left-right")?2:3;
case "top":
case "bottom":
return (_935=="left-right")?3:2;
default:
return 4;
}
};
_934.sort(function(a,b){
return (rank(a)-rank(b))||(a.idx-b.idx);
});
}
var f={top:dojo.html.getPixelValue(_933,"padding-top",true),left:dojo.html.getPixelValue(_933,"padding-left",true)};
dojo.lang.mixin(f,dojo.html.getContentBox(_933));
dojo.lang.forEach(_934,function(_93d){
var elm=_93d.domNode;
var pos=_93d.layoutAlign;
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
if(_93d.onResized){
_93d.onResized();
}
}else{
if(pos=="left"||pos=="right"){
var w=dojo.html.getMarginBox(elm).width;
if(_93d.resizeTo){
_93d.resizeTo(w,f.height);
}else{
dojo.html.setMarginBox(elm,{width:w,height:f.height});
}
f.width-=w;
if(pos=="left"){
f.left+=w;
}else{
elm.style.left=f.left+f.width+"px";
}
}else{
if(pos=="flood"||pos=="client"){
if(_93d.resizeTo){
_93d.resizeTo(f.width,f.height);
}else{
dojo.html.setMarginBox(elm,{width:f.width,height:f.height});
}
}
}
}
});
};
dojo.html.insertCssText(".dojoLayoutContainer{ position: relative; display: block; overflow: hidden; }\n"+"body .dojoAlignTop, body .dojoAlignBottom, body .dojoAlignLeft, body .dojoAlignRight { position: absolute; overflow: hidden; }\n"+"body .dojoAlignClient { position: absolute }\n"+".dojoAlignClient { overflow: auto; }\n");
dojo.provide("dojo.widget.TabContainer");
dojo.widget.defineWidget("dojo.widget.TabContainer",dojo.widget.PageContainer,{labelPosition:"top",closeButton:"none",templateString:null,templateString:"<div id=\"${this.widgetId}\" class=\"dojoTabContainer\">\n\t<div dojoAttachPoint=\"tablistNode\"></div>\n\t<div class=\"dojoTabPaneWrapper\" dojoAttachPoint=\"containerNode\" dojoAttachEvent=\"onKey\" waiRole=\"tabpanel\"></div>\n</div>\n",templateCssString:".dojoTabContainer {\n\tposition : relative;\n}\n\n.dojoTabPaneWrapper {\n\tborder : 1px solid #6290d2;\n\t_zoom: 1; /* force IE6 layout mode so top border doesnt disappear */\n\tdisplay: block;\n\tclear: both;\n\toverflow: hidden;\n}\n\n.dojoTabLabels-top {\n\tposition : relative;\n\ttop : 0px;\n\tleft : 0px;\n\toverflow : visible;\n\tmargin-bottom : -1px;\n\twidth : 100%;\n\tz-index: 2;\t/* so the bottom of the tab label will cover up the border of dojoTabPaneWrapper */\n}\n\n.dojoTabNoLayout.dojoTabLabels-top .dojoTab {\n\tmargin-bottom: -1px;\n\t_margin-bottom: 0px; /* IE filter so top border lines up correctly */\n}\n\n.dojoTab {\n\tposition : relative;\n\tfloat : left;\n\tpadding-left : 9px;\n\tborder-bottom : 1px solid #6290d2;\n\tbackground : url(images/tab_left.gif) no-repeat left top;\n\tcursor: pointer;\n\twhite-space: nowrap;\n\tz-index: 3;\n}\n\n.dojoTab div {\n\tdisplay : block;\n\tpadding : 4px 15px 4px 6px;\n\tbackground : url(images/tab_top_right.gif) no-repeat right top;\n\tcolor : #333;\n\tfont-size : 90%;\n}\n\n.dojoTab .close {\n\tdisplay : inline-block;\n\theight : 12px;\n\twidth : 12px;\n\tpadding : 0 12px 0 0;\n\tmargin : 0 -10px 0 10px;\n\tcursor : default;\n\tfont-size: small;\n}\n\n.dojoTab .closeImage {\n\tbackground : url(images/tab_close.gif) no-repeat right top;\n}\n\n.dojoTab .closeHover {\n\tbackground-image : url(images/tab_close_h.gif);\n}\n\n.dojoTab.currentTab {\n\tpadding-bottom : 1px;\n\tborder-bottom : 0;\n\tbackground-position : 0 -150px;\n}\n\n.dojoTab.currentTab div {\n\tpadding-bottom : 5px;\n\tmargin-bottom : -1px;\n\tbackground-position : 100% -150px;\n}\n\n/* bottom tabs */\n\n.dojoTabLabels-bottom {\n\tposition : relative;\n\tbottom : 0px;\n\tleft : 0px;\n\toverflow : visible;\n\tmargin-top : -1px;\n\twidth : 100%;\n\tz-index: 2;\n}\n\n.dojoTabNoLayout.dojoTabLabels-bottom {\n\tposition : relative;\n}\n\n.dojoTabLabels-bottom .dojoTab {\n\tborder-top :  1px solid #6290d2;\n\tborder-bottom : 0;\n\tbackground : url(images/tab_bot_left.gif) no-repeat left bottom;\n}\n\n.dojoTabLabels-bottom .dojoTab div {\n\tbackground : url(images/tab_bot_right.gif) no-repeat right bottom;\n}\n\n.dojoTabLabels-bottom .dojoTab.currentTab {\n\tborder-top : 0;\n\tbackground : url(images/tab_bot_left_curr.gif) no-repeat left bottom;\n}\n\n.dojoTabLabels-bottom .dojoTab.currentTab div {\n\tpadding-top : 4px;\n\tbackground : url(images/tab_bot_right_curr.gif) no-repeat right bottom;\n}\n\n/* right-h tabs */\n\n.dojoTabLabels-right-h {\n\toverflow : visible;\n\tmargin-left : -1px;\n\tz-index: 2;\n}\n\n.dojoTabLabels-right-h .dojoTab {\n\tpadding-left : 0;\n\tborder-left :  1px solid #6290d2;\n\tborder-bottom : 0;\n\tbackground : url(images/tab_bot_right.gif) no-repeat right bottom;\n\tfloat : none;\n}\n\n.dojoTabLabels-right-h .dojoTab div {\n\tpadding : 4px 15px 4px 15px;\n}\n\n.dojoTabLabels-right-h .dojoTab.currentTab {\n\tborder-left :  0;\n\tborder-bottom :  1px solid #6290d2;\n}\n\n/* left-h tabs */\n\n.dojoTabLabels-left-h {\n\toverflow : visible;\n\tmargin-right : -1px;\n\tz-index: 2;\n}\n\n.dojoTabLabels-left-h .dojoTab {\n\tborder-right :  1px solid #6290d2;\n\tborder-bottom : 0;\n\tfloat : none;\n\tbackground : url(images/tab_top_left.gif) no-repeat left top;\n}\n\n.dojoTabLabels-left-h .dojoTab.currentTab {\n\tborder-right : 0;\n\tborder-bottom :  1px solid #6290d2;\n\tpadding-bottom : 0;\n\tbackground : url(images/tab_top_left.gif) no-repeat 0 -150px;\n}\n\n.dojoTabLabels-left-h .dojoTab div {\n\tbackground : 0;\n\tborder-bottom :  1px solid #6290d2;\n}\n",templateCssPath:dojo.uri.moduleUri("dojo.widget","templates/TabContainer.css"),selectedTab:"",postMixInProperties:function(){
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
var _945=this.labelPosition.replace(/-h/,"");
var _946=[{domNode:this.tablist.domNode,layoutAlign:_945},{domNode:this.containerNode,layoutAlign:"client"}];
dojo.widget.html.layout(this.domNode,_946);
if(this.selectedChildWidget){
var _947=dojo.html.getContentBox(this.containerNode);
this.selectedChildWidget.resizeTo(_947.width,_947.height);
}
},selectTab:function(tab,_949){
dojo.deprecated("use selectChild() rather than selectTab(), selectTab() will be removed in","0.5");
this.selectChild(tab,_949);
},onKey:function(e){
if(e.keyCode==e.KEY_UP_ARROW&&e.ctrlKey){
var _94b=this.correspondingTabButton||this.selectedTabWidget.tabButton;
_94b.focus();
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
},onCloseButtonClick:function(evt){
evt.stopPropagation();
dojo.widget.TabButton.superclass.onCloseButtonClick.apply(this,arguments);
}});
dojo.widget.defineWidget("dojo.widget.a11y.TabButton",dojo.widget.TabButton,{imgPath:dojo.uri.moduleUri("dojo.widget","templates/images/tab_close.gif"),templateString:"<div class='dojoTab' dojoAttachEvent='onClick;onKey'>"+"<div dojoAttachPoint='innerDiv'>"+"<span dojoAttachPoint='titleNode' tabIndex='-1' waiRole='tab'>${this.label}</span>"+"<img class='close' src='${this.imgPath}' alt='[x]' style='${this.closeButtonStyle}'"+"    dojoAttachEvent='onClick:onCloseButtonClick'>"+"</div>"+"</div>"});
dojo.provide("dojo.widget.Select");
dojo.widget.defineWidget("dojo.widget.Select",dojo.widget.ComboBox,{forceValidOption:true,setValue:function(_94d){
this.comboBoxValue.value=_94d;
dojo.widget.html.stabile.setState(this.widgetId,this.getState(),true);
this.onValueChanged(_94d);
},setLabel:function(_94e){
this.comboBoxSelectionValue.value=_94e;
if(this.textInputNode.value!=_94e){
this.textInputNode.value=_94e;
}
},getLabel:function(){
return this.comboBoxSelectionValue.value;
},getState:function(){
return {value:this.getValue(),label:this.getLabel()};
},onKeyUp:function(evt){
this.setLabel(this.textInputNode.value);
},setState:function(_950){
this.setValue(_950.value);
this.setLabel(_950.label);
},setAllValues:function(_951,_952){
this.setLabel(_951);
this.setValue(_952);
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
},{isContainer:true,adjustPaths:true,href:"",extractContent:true,parseContent:true,cacheContent:true,preload:false,refreshOnShow:false,handler:"",executeScripts:false,scriptSeparation:true,loadingMessage:"Loading...",isLoaded:false,postCreate:function(args,frag,_955){
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
},_downloadExternalContent:function(url,_959){
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
}},_959));
},_cacheSetting:function(_95f,_960){
for(var x in this.bindArgs){
if(dojo.lang.isUndefined(_95f[x])){
_95f[x]=this.bindArgs[x];
}
}
if(dojo.lang.isUndefined(_95f.useCache)){
_95f.useCache=_960;
}
if(dojo.lang.isUndefined(_95f.preventCache)){
_95f.preventCache=!_960;
}
if(dojo.lang.isUndefined(_95f.mimetype)){
_95f.mimetype="text/html";
}
return _95f;
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
},_runStack:function(_965){
var st=this[_965];
var err="";
var _968=this.scriptScope||window;
for(var i=0;i<st.length;i++){
try{
st[i].call(_968);
}
catch(e){
err+="\n"+st[i]+" failed: "+e.description;
}
}
this[_965]=[];
if(err.length){
var name=(_965=="_onLoadStack")?"addOnLoad":"addOnUnLoad";
this._handleDefaults(name+" failure\n "+err,"onExecError","debug");
}
},addOnLoad:function(obj,func){
this._pushOnStack(this._onLoadStack,obj,func);
},addOnUnload:function(obj,func){
this._pushOnStack(this._onUnloadStack,obj,func);
},addOnUnLoad:function(){
dojo.deprecated(this.widgetType+".addOnUnLoad, use addOnUnload instead. (lowercased Load)",0.5);
this.addOnUnload.apply(this,arguments);
},_pushOnStack:function(_96f,obj,func){
if(typeof func=="undefined"){
_96f.push(obj);
}else{
_96f.push(function(){
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
},_handleDefaults:function(e,_979,_97a){
if(!_979){
_979="onContentError";
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
this[_979](e);
if(e.returnValue){
switch(_97a){
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
var _97d=[],_97e=[],tmp=[];
var _980=[],_981=[],attr=[],_983=[];
var str="",path="",fix="",_987="",tag="",_989="";
if(!url){
url="./";
}
if(s){
var _98a=/<title[^>]*>([\s\S]*?)<\/title>/i;
while(_980=_98a.exec(s)){
_97d.push(_980[1]);
s=s.substring(0,_980.index)+s.substr(_980.index+_980[0].length);
}
if(this.adjustPaths){
var _98b=/<[a-z][a-z0-9]*[^>]*\s(?:(?:src|href|style)=[^>])+[^>]*>/i;
var _98c=/\s(src|href|style)=(['"]?)([\w()\[\]\/.,\\'"-:;#=&?\s@]+?)\2/i;
var _98d=/^(?:[#]|(?:(?:https?|ftps?|file|javascript|mailto|news):))/;
while(tag=_98b.exec(s)){
str+=s.substring(0,tag.index);
s=s.substring((tag.index+tag[0].length),s.length);
tag=tag[0];
_987="";
while(attr=_98c.exec(tag)){
path="";
_989=attr[3];
switch(attr[1].toLowerCase()){
case "src":
case "href":
if(_98d.exec(_989)){
path=_989;
}else{
path=(new dojo.uri.Uri(url,_989).toString());
}
break;
case "style":
path=dojo.html.fixPathsInCssText(_989,url);
break;
default:
path=_989;
}
fix=" "+attr[1]+"="+attr[2]+path+attr[2];
_987+=tag.substring(0,attr.index)+fix;
tag=tag.substring((attr.index+attr[0].length),tag.length);
}
str+=_987+tag;
}
s=str+s;
}
_98a=/(?:<(style)[^>]*>([\s\S]*?)<\/style>|<link ([^>]*rel=['"]?stylesheet['"]?[^>]*)>)/i;
while(_980=_98a.exec(s)){
if(_980[1]&&_980[1].toLowerCase()=="style"){
_983.push(dojo.html.fixPathsInCssText(_980[2],url));
}else{
if(attr=_980[3].match(/href=(['"]?)([^'">]*)\1/i)){
_983.push({path:attr[2]});
}
}
s=s.substring(0,_980.index)+s.substr(_980.index+_980[0].length);
}
var _98a=/<script([^>]*)>([\s\S]*?)<\/script>/i;
var _98e=/src=(['"]?)([^"']*)\1/i;
var _98f=/.*(\bdojo\b\.js(?:\.uncompressed\.js)?)$/;
var _990=/(?:var )?\bdjConfig\b(?:[\s]*=[\s]*\{[^}]+\}|\.[\w]*[\s]*=[\s]*[^;\n]*)?;?|dojo\.hostenv\.writeIncludes\(\s*\);?/g;
var _991=/dojo\.(?:(?:require(?:After)?(?:If)?)|(?:widget\.(?:manager\.)?registerWidgetPackage)|(?:(?:hostenv\.)?setModulePrefix|registerModulePath)|defineNamespace)\((['"]).*?\1\)\s*;?/;
while(_980=_98a.exec(s)){
if(this.executeScripts&&_980[1]){
if(attr=_98e.exec(_980[1])){
if(_98f.exec(attr[2])){
dojo.debug("Security note! inhibit:"+attr[2]+" from  being loaded again.");
}else{
_97e.push({path:attr[2]});
}
}
}
if(_980[2]){
var sc=_980[2].replace(_990,"");
if(!sc){
continue;
}
while(tmp=_991.exec(sc)){
_981.push(tmp[0]);
sc=sc.substring(0,tmp.index)+sc.substr(tmp.index+tmp[0].length);
}
if(this.executeScripts){
_97e.push(sc);
}
}
s=s.substr(0,_980.index)+s.substr(_980.index+_980[0].length);
}
if(this.extractContent){
_980=s.match(/<body[^>]*>\s*([\s\S]+)\s*<\/body>/im);
if(_980){
s=_980[1];
}
}
if(this.executeScripts&&this.scriptSeparation){
var _98a=/(<[a-zA-Z][a-zA-Z0-9]*\s[^>]*?\S=)((['"])[^>]*scriptScope[^>]*>)/;
var _993=/([\s'";:\(])scriptScope(.*)/;
str="";
while(tag=_98a.exec(s)){
tmp=((tag[3]=="'")?"\"":"'");
fix="";
str+=s.substring(0,tag.index)+tag[1];
while(attr=_993.exec(tag[2])){
tag[2]=tag[2].substring(0,attr.index)+attr[1]+"dojo.widget.byId("+tmp+this.widgetId+tmp+").scriptScope"+attr[2];
}
str+=tag[2];
s=s.substr(tag.index+tag[0].length);
}
s=str+s;
}
}
return {"xml":s,"styles":_983,"titles":_97d,"requires":_981,"scripts":_97e,"url":url};
},_setContent:function(cont){
this.destroyChildren();
for(var i=0;i<this._styleNodes.length;i++){
if(this._styleNodes[i]&&this._styleNodes[i].parentNode){
this._styleNodes[i].parentNode.removeChild(this._styleNodes[i]);
}
}
this._styleNodes=[];
try{
var node=this.containerNode||this.domNode;
while(node.firstChild){
dojo.html.destroyNode(node.firstChild);
}
if(typeof cont!="string"){
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
this._styleNodes.push(dojo.html.insertCssFile(data.styles[i].path,dojo.doc(),false,true));
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
var _999=this;
function asyncParse(){
if(_999.executeScripts){
_999._executeScripts(data.scripts);
}
if(_999.parseContent){
var node=_999.containerNode||_999.domNode;
var _99b=new dojo.xml.Parse();
var frag=_99b.parseElement(node,null,true);
dojo.widget.getParser().createSubComponents(frag,_999);
}
_999.onResized();
_999.onLoad();
}
if(dojo.hostenv.isXDomain&&data.requires.length){
dojo.addOnLoad(asyncParse);
}else{
asyncParse();
}
}
},setHandler:function(_99d){
var fcn=dojo.lang.isFunction(_99d)?_99d:window[_99d];
if(!dojo.lang.isFunction(fcn)){
this._handleDefaults("Unable to set handler, '"+_99d+"' not a function.","onExecError",true);
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
},_executeScripts:function(_9a0){
var self=this;
var tmp="",code="";
for(var i=0;i<_9a0.length;i++){
if(_9a0[i].path){
dojo.io.bind(this._cacheSetting({"url":_9a0[i].path,"load":function(type,_9a6){
dojo.lang.hitch(self,tmp=";"+_9a6);
},"error":function(type,_9a8){
_9a8.text=type+" downloading remote script";
self._handleDefaults.call(self,_9a8,"onExecError","debug");
},"mimetype":"text/plain","sync":true},this.cacheContent));
code+=tmp;
}else{
code+=_9a0[i];
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
dojo.widget.defineWidget("dojo.widget.Tooltip",[dojo.widget.ContentPane,dojo.widget.PopupContainerBase],{caption:"",showDelay:500,hideDelay:100,connectId:"",templateCssString:".dojoTooltip {\n\tborder: solid black 1px;\n\tbackground: beige;\n\tcolor: black;\n\tposition: absolute;\n\tfont-size: small;\n\tpadding: 2px 2px 2px 2px;\n\tz-index: 10;\n\tdisplay: block;\n}\n",templateCssPath:dojo.uri.moduleUri("dojo.widget","templates/TooltipTemplate.css"),fillInTemplate:function(args,frag){
if(this.caption!=""){
this.domNode.appendChild(document.createTextNode(this.caption));
}
this._connectNode=dojo.byId(this.connectId);
dojo.widget.Tooltip.superclass.fillInTemplate.call(this,args,frag);
this.addOnLoad(this,"_loadedContent");
dojo.html.addClass(this.domNode,"dojoTooltip");
var _9ae=this.getFragNodeRef(frag);
dojo.html.copyStyle(this.domNode,_9ae);
this.applyPopupBasicStyle();
},postCreate:function(args,frag){
dojo.event.connect(this._connectNode,"onmouseover",this,"_onMouseOver");
dojo.widget.Tooltip.superclass.postCreate.call(this,args,frag);
},_onMouseOver:function(e){
this._mouse={x:e.pageX,y:e.pageY};
if(!this._tracking){
dojo.event.connect(document.documentElement,"onmousemove",this,"_onMouseMove");
this._tracking=true;
}
this._onHover(e);
},_onMouseMove:function(e){
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
dojo.event.disconnect(document.documentElement,"onmousemove",this,"_onMouseMove");
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
dojo.event.disconnect(document.documentElement,"onmousemove",this,"_onMouseMove");
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
dojo.event.disconnect(this._connectNode,"onmouseover",this,"_onMouseOver");
}});
dojo.provide("dojo.string.Builder");
dojo.string.Builder=function(str){
this.arrConcat=(dojo.render.html.capable&&dojo.render.html["ie"]);
var a=[];
var b="";
var _9b8=this.length=b.length;
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
_9b8+=s.length;
this.length=_9b8;
}
}
return this;
};
this.clear=function(){
a=[];
b="";
_9b8=this.length=0;
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
_9b8=this.length=b.length;
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
_9b8=this.length=b.length;
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
_9b8=this.length=b.length;
if(this.arrConcat){
a.push(b);
b="";
}
return this;
};
this.append.apply(this,arguments);
};
dojo.kwCompoundRequire({common:["dojo.string","dojo.string.common","dojo.string.extras","dojo.string.Builder"]});
dojo.provide("dojo.string.*");
dojo.provide("dojo.widget.ProgressBar");
dojo.require("dojo.event");
dojo.widget.defineWidget("dojo.widget.ProgressBar",dojo.widget.HtmlWidget,{progressValue:0,maxProgressValue:100,width:300,height:30,frontPercentClass:"frontPercent",backPercentClass:"backPercent",frontBarClass:"frontBar",backBarClass:"backBar",hasText:false,isVertical:false,showOnlyIntegers:false,dataSource:"",pollInterval:3000,duration:1000,templateString:"<div dojoAttachPoint=\"containerNode\" style=\"position:relative;overflow:hidden\">\n\t<div style=\"position:absolute;display:none;width:100%;text-align:center\" dojoAttachPoint=\"backPercentLabel\" class=\"dojoBackPercentLabel\"></div>\n\t<div style=\"position:absolute;overflow:hidden;width:100%;height:100%\" dojoAttachPoint=\"internalProgress\">\n\t<div style=\"position:absolute;display:none;width:100%;text-align:center\" dojoAttachPoint=\"frontPercentLabel\" class=\"dojoFrontPercentLabel\"></div></div>\n</div>\n",templateCssString:".backBar{\n\tborder:1px solid #84a3d1;\n}\n.frontBar{\n\tbackground:url(\"images/bar.gif\") repeat bottom left;\n\tbackground-attachment: fixed;\n}\n.h-frontBar{\n\tbackground:url(\"images/h-bar.gif\") repeat bottom left;\n\tbackground-attachment: fixed;\n}\n.simpleFrontBar{\n\tbackground: red;\n}\n.frontPercent,.backPercent{\n\tfont:bold 13px helvetica;\n}\n.backPercent{\n\tcolor:#293a4b;\n}\n.frontPercent{\n\tcolor:#fff;\n}\n",templateCssPath:dojo.uri.moduleUri("dojo.widget","templates/ProgressBar.css"),containerNode:null,internalProgress:null,_pixelUnitRatio:0,_pixelPercentRatio:0,_unitPercentRatio:0,_unitPixelRatio:0,_floatDimension:0,_intDimension:0,_progressPercentValue:"0%",_floatMaxProgressValue:0,_dimension:"width",_pixelValue:0,_oInterval:null,_animation:null,_animationStopped:true,_progressValueBak:false,_hasTextBak:false,fillInTemplate:function(args,frag){
this.internalProgress.className=this.frontBarClass;
this.containerNode.className=this.backBarClass;
if(this.isVertical){
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
},showText:function(_9c5){
if(_9c5){
this.backPercentLabel.style.display="block";
this.frontPercentLabel.style.display="block";
}else{
this.backPercentLabel.style.display="none";
this.frontPercentLabel.style.display="none";
}
this.hasText=_9c5;
},postCreate:function(args,frag){
this.render();
},_backupValues:function(){
this._progressValueBak=this.progressValue;
this._hasTextBak=this.hasText;
},_restoreValues:function(){
this.setProgressValue(this._progressValueBak);
this.showText(this._hasTextBak);
},_setupAnimation:function(){
var _9c8=this;
dojo.debug("internalProgress width: "+this.internalProgress.style.width);
this._animation=dojo.lfx.html.slideTo(this.internalProgress,{top:0,left:parseInt(this.width)-parseInt(this.internalProgress.style.width)},parseInt(this.duration),null,function(){
var _9c9=dojo.lfx.html.slideTo(_9c8.internalProgress,{top:0,left:0},parseInt(_9c8.duration));
dojo.event.connect(_9c9,"onEnd",function(){
if(!_9c8._animationStopped){
_9c8._animation.play();
}
});
if(!_9c8._animationStopped){
_9c9.play();
}
_9c9=null;
});
},getMaxProgressValue:function(){
return this.maxProgressValue;
},setMaxProgressValue:function(_9ca,_9cb){
if(!this._animationStopped){
return;
}
this.maxProgressValue=_9ca;
this._floatMaxProgressValue=parseFloat("0"+this.maxProgressValue);
this._pixelUnitRatio=this._floatDimension/this.maxProgressValue;
this._unitPercentRatio=this._floatMaxProgressValue/100;
this._unitPixelRatio=this._floatMaxProgressValue/this._floatDimension;
this.setProgressValue(this.progressValue,true);
if(!_9cb){
this.render();
}
},setProgressValue:function(_9cc,_9cd){
if(!this._animationStopped){
return;
}
this._progressPercentValue="0%";
var _9ce=dojo.string.trim(""+_9cc);
var _9cf=parseFloat("0"+_9ce);
var _9d0=parseInt("0"+_9ce);
var _9d1=0;
if(dojo.string.endsWith(_9ce,"%",false)){
this._progressPercentValue=Math.min(_9cf.toFixed(1),100)+"%";
_9ce=Math.min((_9cf)*this._unitPercentRatio,this.maxProgressValue);
_9d1=Math.min((_9cf)*this._pixelPercentRatio,eval("this."+this._dimension));
}else{
this.progressValue=Math.min(_9cf,this.maxProgressValue);
this._progressPercentValue=Math.min((_9cf/this._unitPercentRatio).toFixed(1),100)+"%";
_9d1=Math.min(_9cf/this._unitPixelRatio,eval("this."+this._dimension));
}
this.progressValue=dojo.string.trim(_9ce);
this._pixelValue=_9d1;
if(!_9cd){
this.render();
}
},getProgressValue:function(){
return this.progressValue;
},getProgressPercentValue:function(){
return this._progressPercentValue;
},setDataSource:function(_9d2){
this.dataSource=_9d2;
},setPollInterval:function(_9d3){
this.pollInterval=_9d3;
},start:function(){
var _9d4=dojo.lang.hitch(this,this._showRemoteProgress);
this._oInterval=setInterval(_9d4,this.pollInterval);
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
var _9d5=this;
if((this.getMaxProgressValue()==this.getProgressValue())&&this._oInterval){
clearInterval(this._oInterval);
this._oInterval=null;
this.setProgressValue("100%");
return;
}
var _9d6={url:_9d5.dataSource,method:"POST",mimetype:"text/json",error:function(type,_9d8){
dojo.debug("[ProgressBar] showRemoteProgress error");
},load:function(type,data,evt){
_9d5.setProgressValue((_9d5._oInterval?data["progress"]:"100%"));
}};
dojo.io.bind(_9d6);
},render:function(){
this._setPercentLabel(dojo.string.trim(this._progressPercentValue));
this._setPixelValue(this._pixelValue);
this._setLabelPosition();
},_setLabelPosition:function(){
var _9dc=dojo.html.getContentBox(this.frontPercentLabel).width;
var _9dd=dojo.html.getContentBox(this.frontPercentLabel).height;
var _9de=dojo.html.getContentBox(this.backPercentLabel).width;
var _9df=dojo.html.getContentBox(this.backPercentLabel).height;
var _9e0=(parseInt(this.width)-_9dc)/2+"px";
var _9e1=(parseInt(this.height)-parseInt(_9dd))/2+"px";
var _9e2=(parseInt(this.width)-_9de)/2+"px";
var _9e3=(parseInt(this.height)-parseInt(_9df))/2+"px";
this.frontPercentLabel.style.left=_9e0;
this.backPercentLabel.style.left=_9e2;
this.frontPercentLabel.style.bottom=_9e1;
this.backPercentLabel.style.bottom=_9e3;
},_setPercentLabel:function(_9e4){
dojo.dom.removeChildren(this.frontPercentLabel);
dojo.dom.removeChildren(this.backPercentLabel);
var _9e5=this.showOnlyIntegers==false?_9e4:parseInt(_9e4)+"%";
this.frontPercentLabel.appendChild(document.createTextNode(_9e5));
this.backPercentLabel.appendChild(document.createTextNode(_9e5));
},_setPixelValue:function(_9e6){
eval("this.internalProgress.style."+this._dimension+" = "+_9e6+" + 'px'");
this.onChange();
},onChange:function(){
}});
dojo.provide("dojo.widget.LinkPane");
dojo.widget.defineWidget("dojo.widget.LinkPane",dojo.widget.ContentPane,{templateString:"<div class=\"dojoLinkPane\"></div>",fillInTemplate:function(args,frag){
var _9e9=this.getFragNodeRef(frag);
this.label+=_9e9.innerHTML;
var _9e9=this.getFragNodeRef(frag);
dojo.html.copyStyle(this.domNode,_9e9);
}});
dojo.provide("dojo.date.common");
dojo.date.setDayOfYear=function(_9ea,_9eb){
_9ea.setMonth(0);
_9ea.setDate(_9eb);
return _9ea;
};
dojo.date.getDayOfYear=function(_9ec){
var _9ed=_9ec.getFullYear();
var _9ee=new Date(_9ed-1,11,31);
return Math.floor((_9ec.getTime()-_9ee.getTime())/86400000);
};
dojo.date.setWeekOfYear=function(_9ef,week,_9f1){
if(arguments.length==1){
_9f1=0;
}
dojo.unimplemented("dojo.date.setWeekOfYear");
};
dojo.date.getWeekOfYear=function(_9f2,_9f3){
if(arguments.length==1){
_9f3=0;
}
var _9f4=new Date(_9f2.getFullYear(),0,1);
var day=_9f4.getDay();
_9f4.setDate(_9f4.getDate()-day+_9f3-(day>_9f3?7:0));
return Math.floor((_9f2.getTime()-_9f4.getTime())/604800000);
};
dojo.date.setIsoWeekOfYear=function(_9f6,week,_9f8){
if(arguments.length==1){
_9f8=1;
}
dojo.unimplemented("dojo.date.setIsoWeekOfYear");
};
dojo.date.getIsoWeekOfYear=function(_9f9,_9fa){
if(arguments.length==1){
_9fa=1;
}
dojo.unimplemented("dojo.date.getIsoWeekOfYear");
};
dojo.date.shortTimezones=["IDLW","BET","HST","MART","AKST","PST","MST","CST","EST","AST","NFT","BST","FST","AT","GMT","CET","EET","MSK","IRT","GST","AFT","AGTT","IST","NPT","ALMT","MMT","JT","AWST","JST","ACST","AEST","LHST","VUT","NFT","NZT","CHAST","PHOT","LINT"];
dojo.date.timezoneOffsets=[-720,-660,-600,-570,-540,-480,-420,-360,-300,-240,-210,-180,-120,-60,0,60,120,180,210,240,270,300,330,345,360,390,420,480,540,570,600,630,660,690,720,765,780,840];
dojo.date.getDaysInMonth=function(_9fb){
var _9fc=_9fb.getMonth();
var days=[31,28,31,30,31,30,31,31,30,31,30,31];
if(_9fc==1&&dojo.date.isLeapYear(_9fb)){
return 29;
}else{
return days[_9fc];
}
};
dojo.date.isLeapYear=function(_9fe){
var year=_9fe.getFullYear();
return (year%400==0)?true:(year%100==0)?false:(year%4==0)?true:false;
};
dojo.date.getTimezoneName=function(_a00){
var str=_a00.toString();
var tz="";
var _a03;
var pos=str.indexOf("(");
if(pos>-1){
pos++;
tz=str.substring(pos,str.indexOf(")"));
}else{
var pat=/([A-Z\/]+) \d{4}$/;
if((_a03=str.match(pat))){
tz=_a03[1];
}else{
str=_a00.toLocaleString();
pat=/ ([A-Z\/]+)$/;
if((_a03=str.match(pat))){
tz=_a03[1];
}
}
}
return tz=="AM"||tz=="PM"?"":tz;
};
dojo.date.getOrdinal=function(_a06){
var date=_a06.getDate();
if(date%100!=11&&date%10==1){
return "st";
}else{
if(date%100!=12&&date%10==2){
return "nd";
}else{
if(date%100!=13&&date%10==3){
return "rd";
}else{
return "th";
}
}
}
};
dojo.date.compareTypes={DATE:1,TIME:2};
dojo.date.compare=function(_a08,_a09,_a0a){
var dA=_a08;
var dB=_a09||new Date();
var now=new Date();
with(dojo.date.compareTypes){
var opt=_a0a||(DATE|TIME);
var d1=new Date((opt&DATE)?dA.getFullYear():now.getFullYear(),(opt&DATE)?dA.getMonth():now.getMonth(),(opt&DATE)?dA.getDate():now.getDate(),(opt&TIME)?dA.getHours():0,(opt&TIME)?dA.getMinutes():0,(opt&TIME)?dA.getSeconds():0);
var d2=new Date((opt&DATE)?dB.getFullYear():now.getFullYear(),(opt&DATE)?dB.getMonth():now.getMonth(),(opt&DATE)?dB.getDate():now.getDate(),(opt&TIME)?dB.getHours():0,(opt&TIME)?dB.getMinutes():0,(opt&TIME)?dB.getSeconds():0);
}
if(d1.valueOf()>d2.valueOf()){
return 1;
}
if(d1.valueOf()<d2.valueOf()){
return -1;
}
return 0;
};
dojo.date.dateParts={YEAR:0,MONTH:1,DAY:2,HOUR:3,MINUTE:4,SECOND:5,MILLISECOND:6,QUARTER:7,WEEK:8,WEEKDAY:9};
dojo.date.add=function(dt,_a12,incr){
if(typeof dt=="number"){
dt=new Date(dt);
}
function fixOvershoot(){
if(sum.getDate()<dt.getDate()){
sum.setDate(0);
}
}
var sum=new Date(dt);
with(dojo.date.dateParts){
switch(_a12){
case YEAR:
sum.setFullYear(dt.getFullYear()+incr);
fixOvershoot();
break;
case QUARTER:
incr*=3;
case MONTH:
sum.setMonth(dt.getMonth()+incr);
fixOvershoot();
break;
case WEEK:
incr*=7;
case DAY:
sum.setDate(dt.getDate()+incr);
break;
case WEEKDAY:
var dat=dt.getDate();
var _a16=0;
var days=0;
var strt=0;
var trgt=0;
var adj=0;
var mod=incr%5;
if(mod==0){
days=(incr>0)?5:-5;
_a16=(incr>0)?((incr-5)/5):((incr+5)/5);
}else{
days=mod;
_a16=parseInt(incr/5);
}
strt=dt.getDay();
if(strt==6&&incr>0){
adj=1;
}else{
if(strt==0&&incr<0){
adj=-1;
}
}
trgt=(strt+days);
if(trgt==0||trgt==6){
adj=(incr>0)?2:-2;
}
sum.setDate(dat+(7*_a16)+days+adj);
break;
case HOUR:
sum.setHours(sum.getHours()+incr);
break;
case MINUTE:
sum.setMinutes(sum.getMinutes()+incr);
break;
case SECOND:
sum.setSeconds(sum.getSeconds()+incr);
break;
case MILLISECOND:
sum.setMilliseconds(sum.getMilliseconds()+incr);
break;
default:
break;
}
}
return sum;
};
dojo.date.diff=function(dtA,dtB,_a1e){
if(typeof dtA=="number"){
dtA=new Date(dtA);
}
if(typeof dtB=="number"){
dtB=new Date(dtB);
}
var _a1f=dtB.getFullYear()-dtA.getFullYear();
var _a20=(dtB.getMonth()-dtA.getMonth())+(_a1f*12);
var _a21=dtB.getTime()-dtA.getTime();
var _a22=_a21/1000;
var _a23=_a22/60;
var _a24=_a23/60;
var _a25=_a24/24;
var _a26=_a25/7;
var _a27=0;
with(dojo.date.dateParts){
switch(_a1e){
case YEAR:
_a27=_a1f;
break;
case QUARTER:
var mA=dtA.getMonth();
var mB=dtB.getMonth();
var qA=Math.floor(mA/3)+1;
var qB=Math.floor(mB/3)+1;
qB+=(_a1f*4);
_a27=qB-qA;
break;
case MONTH:
_a27=_a20;
break;
case WEEK:
_a27=parseInt(_a26);
break;
case DAY:
_a27=_a25;
break;
case WEEKDAY:
var days=Math.round(_a25);
var _a2d=parseInt(days/7);
var mod=days%7;
if(mod==0){
days=_a2d*5;
}else{
var adj=0;
var aDay=dtA.getDay();
var bDay=dtB.getDay();
_a2d=parseInt(days/7);
mod=days%7;
var _a32=new Date(dtA);
_a32.setDate(_a32.getDate()+(_a2d*7));
var _a33=_a32.getDay();
if(_a25>0){
switch(true){
case aDay==6:
adj=-1;
break;
case aDay==0:
adj=0;
break;
case bDay==6:
adj=-1;
break;
case bDay==0:
adj=-2;
break;
case (_a33+mod)>5:
adj=-2;
break;
default:
break;
}
}else{
if(_a25<0){
switch(true){
case aDay==6:
adj=0;
break;
case aDay==0:
adj=1;
break;
case bDay==6:
adj=2;
break;
case bDay==0:
adj=1;
break;
case (_a33+mod)<0:
adj=2;
break;
default:
break;
}
}
}
days+=adj;
days-=(_a2d*2);
}
_a27=days;
break;
case HOUR:
_a27=_a24;
break;
case MINUTE:
_a27=_a23;
break;
case SECOND:
_a27=_a22;
break;
case MILLISECOND:
_a27=_a21;
break;
default:
break;
}
}
return Math.round(_a27);
};
dojo.provide("dojo.date.supplemental");
dojo.date.getFirstDayOfWeek=function(_a34){
var _a35={mv:5,ae:6,af:6,bh:6,dj:6,dz:6,eg:6,er:6,et:6,iq:6,ir:6,jo:6,ke:6,kw:6,lb:6,ly:6,ma:6,om:6,qa:6,sa:6,sd:6,so:6,tn:6,ye:6,as:0,au:0,az:0,bw:0,ca:0,cn:0,fo:0,ge:0,gl:0,gu:0,hk:0,ie:0,il:0,is:0,jm:0,jp:0,kg:0,kr:0,la:0,mh:0,mo:0,mp:0,mt:0,nz:0,ph:0,pk:0,sg:0,th:0,tt:0,tw:0,um:0,us:0,uz:0,vi:0,za:0,zw:0,et:0,mw:0,ng:0,tj:0,gb:0,sy:4};
_a34=dojo.hostenv.normalizeLocale(_a34);
var _a36=_a34.split("-")[1];
var dow=_a35[_a36];
return (typeof dow=="undefined")?1:dow;
};
dojo.date.getWeekend=function(_a38){
var _a39={eg:5,il:5,sy:5,"in":0,ae:4,bh:4,dz:4,iq:4,jo:4,kw:4,lb:4,ly:4,ma:4,om:4,qa:4,sa:4,sd:4,tn:4,ye:4};
var _a3a={ae:5,bh:5,dz:5,iq:5,jo:5,kw:5,lb:5,ly:5,ma:5,om:5,qa:5,sa:5,sd:5,tn:5,ye:5,af:5,ir:5,eg:6,il:6,sy:6};
_a38=dojo.hostenv.normalizeLocale(_a38);
var _a3b=_a38.split("-")[1];
var _a3c=_a39[_a3b];
var end=_a3a[_a3b];
if(typeof _a3c=="undefined"){
_a3c=6;
}
if(typeof end=="undefined"){
end=0;
}
return {start:_a3c,end:end};
};
dojo.date.isWeekend=function(_a3e,_a3f){
var _a40=dojo.date.getWeekend(_a3f);
var day=(_a3e||new Date()).getDay();
if(_a40.end<_a40.start){
_a40.end+=7;
if(day<_a40.start){
day+=7;
}
}
return day>=_a40.start&&day<=_a40.end;
};
dojo.provide("dojo.i18n.common");
dojo.i18n.getLocalization=function(_a42,_a43,_a44){
dojo.hostenv.preloadLocalizations();
_a44=dojo.hostenv.normalizeLocale(_a44);
var _a45=_a44.split("-");
var _a46=[_a42,"nls",_a43].join(".");
var _a47=dojo.hostenv.findModule(_a46,true);
var _a48;
for(var i=_a45.length;i>0;i--){
var loc=_a45.slice(0,i).join("_");
if(_a47[loc]){
_a48=_a47[loc];
break;
}
}
if(!_a48){
_a48=_a47.ROOT;
}
if(_a48){
var _a4b=function(){
};
_a4b.prototype=_a48;
return new _a4b();
}
dojo.raise("Bundle not found: "+_a43+" in "+_a42+" , locale="+_a44);
};
dojo.i18n.isLTR=function(_a4c){
var lang=dojo.hostenv.normalizeLocale(_a4c).split("-")[0];
var RTL={ar:true,fa:true,he:true,ur:true,yi:true};
return !RTL[lang];
};
dojo.provide("dojo.date.format");
(function(){
dojo.date.format=function(_a4f,_a50){
if(typeof _a50=="string"){
dojo.deprecated("dojo.date.format","To format dates with POSIX-style strings, please use dojo.date.strftime instead","0.5");
return dojo.date.strftime(_a4f,_a50);
}
function formatPattern(_a51,_a52){
return _a52.replace(/([a-z])\1*/ig,function(_a53){
var s;
var c=_a53.charAt(0);
var l=_a53.length;
var pad;
var _a58=["abbr","wide","narrow"];
switch(c){
case "G":
if(l>3){
dojo.unimplemented("Era format not implemented");
}
s=_a59.eras[_a51.getFullYear()<0?1:0];
break;
case "y":
s=_a51.getFullYear();
switch(l){
case 1:
break;
case 2:
s=String(s);
s=s.substr(s.length-2);
break;
default:
pad=true;
}
break;
case "Q":
case "q":
s=Math.ceil((_a51.getMonth()+1)/3);
switch(l){
case 1:
case 2:
pad=true;
break;
case 3:
case 4:
dojo.unimplemented("Quarter format not implemented");
}
break;
case "M":
case "L":
var m=_a51.getMonth();
var _a5b;
switch(l){
case 1:
case 2:
s=m+1;
pad=true;
break;
case 3:
case 4:
case 5:
_a5b=_a58[l-3];
break;
}
if(_a5b){
var type=(c=="L")?"standalone":"format";
var prop=["months",type,_a5b].join("-");
s=_a59[prop][m];
}
break;
case "w":
var _a5e=0;
s=dojo.date.getWeekOfYear(_a51,_a5e);
pad=true;
break;
case "d":
s=_a51.getDate();
pad=true;
break;
case "D":
s=dojo.date.getDayOfYear(_a51);
pad=true;
break;
case "E":
case "e":
case "c":
var d=_a51.getDay();
var _a5b;
switch(l){
case 1:
case 2:
if(c=="e"){
var _a60=dojo.date.getFirstDayOfWeek(_a50.locale);
d=(d-_a60+7)%7;
}
if(c!="c"){
s=d+1;
pad=true;
break;
}
case 3:
case 4:
case 5:
_a5b=_a58[l-3];
break;
}
if(_a5b){
var type=(c=="c")?"standalone":"format";
var prop=["days",type,_a5b].join("-");
s=_a59[prop][d];
}
break;
case "a":
var _a61=(_a51.getHours()<12)?"am":"pm";
s=_a59[_a61];
break;
case "h":
case "H":
case "K":
case "k":
var h=_a51.getHours();
switch(c){
case "h":
s=(h%12)||12;
break;
case "H":
s=h;
break;
case "K":
s=(h%12);
break;
case "k":
s=h||24;
break;
}
pad=true;
break;
case "m":
s=_a51.getMinutes();
pad=true;
break;
case "s":
s=_a51.getSeconds();
pad=true;
break;
case "S":
s=Math.round(_a51.getMilliseconds()*Math.pow(10,l-3));
break;
case "v":
case "z":
s=dojo.date.getTimezoneName(_a51);
if(s){
break;
}
l=4;
case "Z":
var _a63=_a51.getTimezoneOffset();
var tz=[(_a63<=0?"+":"-"),dojo.string.pad(Math.floor(Math.abs(_a63)/60),2),dojo.string.pad(Math.abs(_a63)%60,2)];
if(l==4){
tz.splice(0,0,"GMT");
tz.splice(3,0,":");
}
s=tz.join("");
break;
case "Y":
case "u":
case "W":
case "F":
case "g":
case "A":
s="?";
break;
default:
dojo.raise("dojo.date.format: invalid pattern char: "+_a52);
}
if(pad){
s=dojo.string.pad(s,l);
}
return s;
});
}
_a50=_a50||{};
var _a65=dojo.hostenv.normalizeLocale(_a50.locale);
var _a66=_a50.formatLength||"short";
var _a59=dojo.date._getGregorianBundle(_a65);
var str=[];
var _a68=dojo.lang.curry(this,formatPattern,_a4f);
if(_a50.selector=="yearOnly"){
var year=_a4f.getFullYear();
if(_a65.match(/^zh|^ja/)){
year+="\u5e74";
}
return year;
}
if(_a50.selector!="timeOnly"){
var _a6a=_a50.datePattern||_a59["dateFormat-"+_a66];
if(_a6a){
str.push(_processPattern(_a6a,_a68));
}
}
if(_a50.selector!="dateOnly"){
var _a6b=_a50.timePattern||_a59["timeFormat-"+_a66];
if(_a6b){
str.push(_processPattern(_a6b,_a68));
}
}
var _a6c=str.join(" ");
return _a6c;
};
dojo.date.parse=function(_a6d,_a6e){
_a6e=_a6e||{};
var _a6f=dojo.hostenv.normalizeLocale(_a6e.locale);
var info=dojo.date._getGregorianBundle(_a6f);
var _a71=_a6e.formatLength||"full";
if(!_a6e.selector){
_a6e.selector="dateOnly";
}
var _a72=_a6e.datePattern||info["dateFormat-"+_a71];
var _a73=_a6e.timePattern||info["timeFormat-"+_a71];
var _a74;
if(_a6e.selector=="dateOnly"){
_a74=_a72;
}else{
if(_a6e.selector=="timeOnly"){
_a74=_a73;
}else{
if(_a6e.selector=="dateTime"){
_a74=_a72+" "+_a73;
}else{
var msg="dojo.date.parse: Unknown selector param passed: '"+_a6e.selector+"'.";
msg+=" Defaulting to date pattern.";
dojo.debug(msg);
_a74=_a72;
}
}
}
var _a76=[];
var _a77=_processPattern(_a74,dojo.lang.curry(this,_buildDateTimeRE,_a76,info,_a6e));
var _a78=new RegExp("^"+_a77+"$");
var _a79=_a78.exec(_a6d);
if(!_a79){
return null;
}
var _a7a=["abbr","wide","narrow"];
var _a7b=new Date(1972,0);
var _a7c={};
for(var i=1;i<_a79.length;i++){
var grp=_a76[i-1];
var l=grp.length;
var v=_a79[i];
switch(grp.charAt(0)){
case "y":
if(l!=2){
_a7b.setFullYear(v);
_a7c.year=v;
}else{
if(v<100){
v=Number(v);
var year=""+new Date().getFullYear();
var _a82=year.substring(0,2)*100;
var _a83=Number(year.substring(2,4));
var _a84=Math.min(_a83+20,99);
var num=(v<_a84)?_a82+v:_a82-100+v;
_a7b.setFullYear(num);
_a7c.year=num;
}else{
if(_a6e.strict){
return null;
}
_a7b.setFullYear(v);
_a7c.year=v;
}
}
break;
case "M":
if(l>2){
if(!_a6e.strict){
v=v.replace(/\./g,"");
v=v.toLowerCase();
}
var _a86=info["months-format-"+_a7a[l-3]].concat();
for(var j=0;j<_a86.length;j++){
if(!_a6e.strict){
_a86[j]=_a86[j].toLowerCase();
}
if(v==_a86[j]){
_a7b.setMonth(j);
_a7c.month=j;
break;
}
}
if(j==_a86.length){
dojo.debug("dojo.date.parse: Could not parse month name: '"+v+"'.");
return null;
}
}else{
_a7b.setMonth(v-1);
_a7c.month=v-1;
}
break;
case "E":
case "e":
if(!_a6e.strict){
v=v.toLowerCase();
}
var days=info["days-format-"+_a7a[l-3]].concat();
for(var j=0;j<days.length;j++){
if(!_a6e.strict){
days[j]=days[j].toLowerCase();
}
if(v==days[j]){
break;
}
}
if(j==days.length){
dojo.debug("dojo.date.parse: Could not parse weekday name: '"+v+"'.");
return null;
}
break;
case "d":
_a7b.setDate(v);
_a7c.date=v;
break;
case "a":
var am=_a6e.am||info.am;
var pm=_a6e.pm||info.pm;
if(!_a6e.strict){
v=v.replace(/\./g,"").toLowerCase();
am=am.replace(/\./g,"").toLowerCase();
pm=pm.replace(/\./g,"").toLowerCase();
}
if(_a6e.strict&&v!=am&&v!=pm){
dojo.debug("dojo.date.parse: Could not parse am/pm part.");
return null;
}
var _a8b=_a7b.getHours();
if(v==pm&&_a8b<12){
_a7b.setHours(_a8b+12);
}else{
if(v==am&&_a8b==12){
_a7b.setHours(0);
}
}
break;
case "K":
if(v==24){
v=0;
}
case "h":
case "H":
case "k":
if(v>23){
dojo.debug("dojo.date.parse: Illegal hours value");
return null;
}
_a7b.setHours(v);
break;
case "m":
_a7b.setMinutes(v);
break;
case "s":
_a7b.setSeconds(v);
break;
case "S":
_a7b.setMilliseconds(v);
break;
default:
dojo.unimplemented("dojo.date.parse: unsupported pattern char="+grp.charAt(0));
}
}
if(_a7c.year&&_a7b.getFullYear()!=_a7c.year){
dojo.debug("Parsed year: '"+_a7b.getFullYear()+"' did not match input year: '"+_a7c.year+"'.");
return null;
}
if(_a7c.month&&_a7b.getMonth()!=_a7c.month){
dojo.debug("Parsed month: '"+_a7b.getMonth()+"' did not match input month: '"+_a7c.month+"'.");
return null;
}
if(_a7c.date&&_a7b.getDate()!=_a7c.date){
dojo.debug("Parsed day of month: '"+_a7b.getDate()+"' did not match input day of month: '"+_a7c.date+"'.");
return null;
}
return _a7b;
};
function _processPattern(_a8c,_a8d,_a8e,_a8f){
var _a90=function(x){
return x;
};
_a8d=_a8d||_a90;
_a8e=_a8e||_a90;
_a8f=_a8f||_a90;
var _a92=_a8c.match(/(''|[^'])+/g);
var _a93=false;
for(var i=0;i<_a92.length;i++){
if(!_a92[i]){
_a92[i]="";
}else{
_a92[i]=(_a93?_a8e:_a8d)(_a92[i]);
_a93=!_a93;
}
}
return _a8f(_a92.join(""));
}
function _buildDateTimeRE(_a95,info,_a97,_a98){
return _a98.replace(/([a-z])\1*/ig,function(_a99){
var s;
var c=_a99.charAt(0);
var l=_a99.length;
switch(c){
case "y":
s="\\d"+((l==2)?"{2,4}":"+");
break;
case "M":
s=(l>2)?"\\S+":"\\d{1,2}";
break;
case "d":
s="\\d{1,2}";
break;
case "E":
s="\\S+";
break;
case "h":
case "H":
case "K":
case "k":
s="\\d{1,2}";
break;
case "m":
case "s":
s="[0-5]\\d";
break;
case "S":
s="\\d{1,3}";
break;
case "a":
var am=_a97.am||info.am||"AM";
var pm=_a97.pm||info.pm||"PM";
if(_a97.strict){
s=am+"|"+pm;
}else{
s=am;
s+=(am!=am.toLowerCase())?"|"+am.toLowerCase():"";
s+="|";
s+=(pm!=pm.toLowerCase())?pm+"|"+pm.toLowerCase():pm;
}
break;
default:
dojo.unimplemented("parse of date format, pattern="+_a98);
}
if(_a95){
_a95.push(_a99);
}
return "\\s*("+s+")\\s*";
});
}
})();
dojo.date.strftime=function(_a9f,_aa0,_aa1){
var _aa2=null;
function _(s,n){
return dojo.string.pad(s,n||2,_aa2||"0");
}
var info=dojo.date._getGregorianBundle(_aa1);
function $(_aa6){
switch(_aa6){
case "a":
return dojo.date.getDayShortName(_a9f,_aa1);
case "A":
return dojo.date.getDayName(_a9f,_aa1);
case "b":
case "h":
return dojo.date.getMonthShortName(_a9f,_aa1);
case "B":
return dojo.date.getMonthName(_a9f,_aa1);
case "c":
return dojo.date.format(_a9f,{locale:_aa1});
case "C":
return _(Math.floor(_a9f.getFullYear()/100));
case "d":
return _(_a9f.getDate());
case "D":
return $("m")+"/"+$("d")+"/"+$("y");
case "e":
if(_aa2==null){
_aa2=" ";
}
return _(_a9f.getDate());
case "f":
if(_aa2==null){
_aa2=" ";
}
return _(_a9f.getMonth()+1);
case "g":
break;
case "G":
dojo.unimplemented("unimplemented modifier 'G'");
break;
case "F":
return $("Y")+"-"+$("m")+"-"+$("d");
case "H":
return _(_a9f.getHours());
case "I":
return _(_a9f.getHours()%12||12);
case "j":
return _(dojo.date.getDayOfYear(_a9f),3);
case "k":
if(_aa2==null){
_aa2=" ";
}
return _(_a9f.getHours());
case "l":
if(_aa2==null){
_aa2=" ";
}
return _(_a9f.getHours()%12||12);
case "m":
return _(_a9f.getMonth()+1);
case "M":
return _(_a9f.getMinutes());
case "n":
return "\n";
case "p":
return info[_a9f.getHours()<12?"am":"pm"];
case "r":
return $("I")+":"+$("M")+":"+$("S")+" "+$("p");
case "R":
return $("H")+":"+$("M");
case "S":
return _(_a9f.getSeconds());
case "t":
return "\t";
case "T":
return $("H")+":"+$("M")+":"+$("S");
case "u":
return String(_a9f.getDay()||7);
case "U":
return _(dojo.date.getWeekOfYear(_a9f));
case "V":
return _(dojo.date.getIsoWeekOfYear(_a9f));
case "W":
return _(dojo.date.getWeekOfYear(_a9f,1));
case "w":
return String(_a9f.getDay());
case "x":
return dojo.date.format(_a9f,{selector:"dateOnly",locale:_aa1});
case "X":
return dojo.date.format(_a9f,{selector:"timeOnly",locale:_aa1});
case "y":
return _(_a9f.getFullYear()%100);
case "Y":
return String(_a9f.getFullYear());
case "z":
var _aa7=_a9f.getTimezoneOffset();
return (_aa7>0?"-":"+")+_(Math.floor(Math.abs(_aa7)/60))+":"+_(Math.abs(_aa7)%60);
case "Z":
return dojo.date.getTimezoneName(_a9f);
case "%":
return "%";
}
}
var _aa8="";
var i=0;
var _aaa=0;
var _aab=null;
while((_aaa=_aa0.indexOf("%",i))!=-1){
_aa8+=_aa0.substring(i,_aaa++);
switch(_aa0.charAt(_aaa++)){
case "_":
_aa2=" ";
break;
case "-":
_aa2="";
break;
case "0":
_aa2="0";
break;
case "^":
_aab="upper";
break;
case "*":
_aab="lower";
break;
case "#":
_aab="swap";
break;
default:
_aa2=null;
_aaa--;
break;
}
var _aac=$(_aa0.charAt(_aaa++));
switch(_aab){
case "upper":
_aac=_aac.toUpperCase();
break;
case "lower":
_aac=_aac.toLowerCase();
break;
case "swap":
var _aad=_aac.toLowerCase();
var _aae="";
var j=0;
var ch="";
while(j<_aac.length){
ch=_aac.charAt(j);
_aae+=(ch==_aad.charAt(j))?ch.toUpperCase():ch.toLowerCase();
j++;
}
_aac=_aae;
break;
default:
break;
}
_aab=null;
_aa8+=_aac;
i=_aaa;
}
_aa8+=_aa0.substring(i);
return _aa8;
};
(function(){
var _ab1=[];
dojo.date.addCustomFormats=function(_ab2,_ab3){
_ab1.push({pkg:_ab2,name:_ab3});
};
dojo.date._getGregorianBundle=function(_ab4){
var _ab5={};
dojo.lang.forEach(_ab1,function(desc){
var _ab7=dojo.i18n.getLocalization(desc.pkg,desc.name,_ab4);
_ab5=dojo.lang.mixin(_ab5,_ab7);
},this);
return _ab5;
};
})();
dojo.date.addCustomFormats("dojo.i18n.calendar","gregorian");
dojo.date.addCustomFormats("dojo.i18n.calendar","gregorianExtras");
dojo.date.getNames=function(item,type,use,_abb){
var _abc;
var _abd=dojo.date._getGregorianBundle(_abb);
var _abe=[item,use,type];
if(use=="standAlone"){
_abc=_abd[_abe.join("-")];
}
_abe[1]="format";
return (_abc||_abd[_abe.join("-")]).concat();
};
dojo.date.getDayName=function(_abf,_ac0){
return dojo.date.getNames("days","wide","format",_ac0)[_abf.getDay()];
};
dojo.date.getDayShortName=function(_ac1,_ac2){
return dojo.date.getNames("days","abbr","format",_ac2)[_ac1.getDay()];
};
dojo.date.getMonthName=function(_ac3,_ac4){
return dojo.date.getNames("months","wide","format",_ac4)[_ac3.getMonth()];
};
dojo.date.getMonthShortName=function(_ac5,_ac6){
return dojo.date.getNames("months","abbr","format",_ac6)[_ac5.getMonth()];
};
dojo.date.toRelativeString=function(_ac7){
var now=new Date();
var diff=(now-_ac7)/1000;
var end=" ago";
var _acb=false;
if(diff<0){
_acb=true;
end=" from now";
diff=-diff;
}
if(diff<60){
diff=Math.round(diff);
return diff+" second"+(diff==1?"":"s")+end;
}
if(diff<60*60){
diff=Math.round(diff/60);
return diff+" minute"+(diff==1?"":"s")+end;
}
if(diff<60*60*24){
diff=Math.round(diff/3600);
return diff+" hour"+(diff==1?"":"s")+end;
}
if(diff<60*60*24*7){
diff=Math.round(diff/(3600*24));
if(diff==1){
return _acb?"Tomorrow":"Yesterday";
}else{
return diff+" days"+end;
}
}
return dojo.date.format(_ac7);
};
dojo.date.toSql=function(_acc,_acd){
return dojo.date.strftime(_acc,"%F"+!_acd?" %T":"");
};
dojo.date.fromSql=function(_ace){
var _acf=_ace.split(/[\- :]/g);
while(_acf.length<6){
_acf.push(0);
}
return new Date(_acf[0],(parseInt(_acf[1],10)-1),_acf[2],_acf[3],_acf[4],_acf[5]);
};
dojo.provide("dojo.widget.SortableTable");
dojo.deprecated("SortableTable will be removed in favor of FilteringTable.","0.5");
dojo.widget.defineWidget("dojo.widget.SortableTable",dojo.widget.HtmlWidget,function(){
this.data=[];
this.selected=[];
this.columns=[];
},{enableMultipleSelect:false,maximumNumberOfSelections:0,enableAlternateRows:false,minRows:0,defaultDateFormat:"%D",sortIndex:0,sortDirection:0,valueField:"Id",headClass:"",tbodyClass:"",headerClass:"",headerSortUpClass:"selected",headerSortDownClass:"selected",rowClass:"",rowAlternateClass:"alt",rowSelectedClass:"selected",columnSelected:"sorted-column",isContainer:false,templatePath:null,templateCssPath:null,getTypeFromString:function(s){
var _acd=s.split("."),i=0,obj=dj_global;
do{
obj=obj[_acd[i++]];
}while(i<_acd.length&&obj);
return (obj!=dj_global)?obj:null;
},compare:function(o1,o2){
for(var p in o1){
if(!(p in o2)){
return false;
}
if(o1[p].valueOf()!=o2[p].valueOf()){
return false;
}
}
return true;
},isSelected:function(o){
for(var i=0;i<this.selected.length;i++){
if(this.compare(this.selected[i],o)){
return true;
}
}
return false;
},removeFromSelected:function(o){
var idx=-1;
for(var i=0;i<this.selected.length;i++){
if(this.compare(this.selected[i],o)){
idx=i;
break;
}
}
if(idx>=0){
this.selected.splice(idx,1);
}
},getSelection:function(){
return this.selected;
},getValue:function(){
var a=[];
for(var i=0;i<this.selected.length;i++){
if(this.selected[i][this.valueField]){
a.push(this.selected[i][this.valueField]);
}
}
return a.join();
},reset:function(){
this.columns=[];
this.data=[];
this.resetSelections(this.domNode.getElementsByTagName("tbody")[0]);
},resetSelections:function(body){
this.selected=[];
var idx=0;
var rows=body.getElementsByTagName("tr");
for(var i=0;i<rows.length;i++){
if(rows[i].parentNode==body){
rows[i].removeAttribute("selected");
if(this.enableAlternateRows&&idx%2==1){
rows[i].className=this.rowAlternateClass;
}else{
rows[i].className="";
}
idx++;
}
}
},getObjectFromRow:function(row){
var _adf=row.getElementsByTagName("td");
var o={};
for(var i=0;i<this.columns.length;i++){
if(this.columns[i].sortType=="__markup__"){
o[this.columns[i].getField()]=_adf[i].innerHTML;
}else{
var text=dojo.html.renderedTextContent(_adf[i]);
var val=text;
if(this.columns[i].getType()!=String){
var val=new (this.columns[i].getType())(text);
}
o[this.columns[i].getField()]=val;
}
}
if(dojo.html.hasAttribute(row,"value")){
o[this.valueField]=dojo.html.getAttribute(row,"value");
}
return o;
},setSelectionByRow:function(row){
var o=this.getObjectFromRow(row);
var b=false;
for(var i=0;i<this.selected.length;i++){
if(this.compare(this.selected[i],o)){
b=true;
break;
}
}
if(!b){
this.selected.push(o);
}
},parseColumns:function(node){
this.reset();
var row=node.getElementsByTagName("tr")[0];
var _aea=row.getElementsByTagName("td");
if(_aea.length==0){
_aea=row.getElementsByTagName("th");
}
for(var i=0;i<_aea.length;i++){
var o={field:null,format:null,noSort:false,sortType:"String",dataType:String,sortFunction:null,label:null,align:"left",valign:"middle",getField:function(){
return this.field||this.label;
},getType:function(){
return this.dataType;
}};
if(dojo.html.hasAttribute(_aea[i],"align")){
o.align=dojo.html.getAttribute(_aea[i],"align");
}
if(dojo.html.hasAttribute(_aea[i],"valign")){
o.valign=dojo.html.getAttribute(_aea[i],"valign");
}
if(dojo.html.hasAttribute(_aea[i],"nosort")){
o.noSort=dojo.html.getAttribute(_aea[i],"nosort")=="true";
}
if(dojo.html.hasAttribute(_aea[i],"sortusing")){
var _aed=dojo.html.getAttribute(_aea[i],"sortusing");
var f=this.getTypeFromString(_aed);
if(f!=null&&f!=window&&typeof (f)=="function"){
o.sortFunction=f;
}
}
if(dojo.html.hasAttribute(_aea[i],"field")){
o.field=dojo.html.getAttribute(_aea[i],"field");
}
if(dojo.html.hasAttribute(_aea[i],"format")){
o.format=dojo.html.getAttribute(_aea[i],"format");
}
if(dojo.html.hasAttribute(_aea[i],"dataType")){
var _aef=dojo.html.getAttribute(_aea[i],"dataType");
if(_aef.toLowerCase()=="html"||_aef.toLowerCase()=="markup"){
o.sortType="__markup__";
o.noSort=true;
}else{
var type=this.getTypeFromString(_aef);
if(type){
o.sortType=_aef;
o.dataType=type;
}
}
}
o.label=dojo.html.renderedTextContent(_aea[i]);
this.columns.push(o);
if(dojo.html.hasAttribute(_aea[i],"sort")){
this.sortIndex=i;
var dir=dojo.html.getAttribute(_aea[i],"sort");
if(!isNaN(parseInt(dir))){
dir=parseInt(dir);
this.sortDirection=(dir!=0)?1:0;
}else{
this.sortDirection=(dir.toLowerCase()=="desc")?1:0;
}
}
}
},parseData:function(data){
this.data=[];
this.selected=[];
for(var i=0;i<data.length;i++){
var o={};
for(var j=0;j<this.columns.length;j++){
var _af6=this.columns[j].getField();
if(this.columns[j].sortType=="__markup__"){
o[_af6]=String(data[i][_af6]);
}else{
var type=this.columns[j].getType();
var val=data[i][_af6];
var t=this.columns[j].sortType.toLowerCase();
if(type==String){
o[_af6]=val;
}else{
if(val!=null){
o[_af6]=new type(val);
}else{
o[_af6]=new type();
}
}
}
}
if(data[i][this.valueField]&&!o[this.valueField]){
o[this.valueField]=data[i][this.valueField];
}
this.data.push(o);
}
},parseDataFromTable:function(_afa){
this.data=[];
this.selected=[];
var rows=_afa.getElementsByTagName("tr");
for(var i=0;i<rows.length;i++){
if(dojo.html.getAttribute(rows[i],"ignoreIfParsed")=="true"){
continue;
}
var o={};
var _afe=rows[i].getElementsByTagName("td");
for(var j=0;j<this.columns.length;j++){
var _b00=this.columns[j].getField();
if(this.columns[j].sortType=="__markup__"){
o[_b00]=_afe[j].innerHTML;
}else{
var type=this.columns[j].getType();
var val=dojo.html.renderedTextContent(_afe[j]);
if(type==String){
o[_b00]=val;
}else{
if(val!=null){
o[_b00]=new type(val);
}else{
o[_b00]=new type();
}
}
}
}
if(dojo.html.hasAttribute(rows[i],"value")&&!o[this.valueField]){
o[this.valueField]=dojo.html.getAttribute(rows[i],"value");
}
this.data.push(o);
if(dojo.html.getAttribute(rows[i],"selected")=="true"){
this.selected.push(o);
}
}
},showSelections:function(){
var body=this.domNode.getElementsByTagName("tbody")[0];
var rows=body.getElementsByTagName("tr");
var idx=0;
for(var i=0;i<rows.length;i++){
if(rows[i].parentNode==body){
if(dojo.html.getAttribute(rows[i],"selected")=="true"){
rows[i].className=this.rowSelectedClass;
}else{
if(this.enableAlternateRows&&idx%2==1){
rows[i].className=this.rowAlternateClass;
}else{
rows[i].className="";
}
}
idx++;
}
}
},render:function(_b07){
var data=[];
var body=this.domNode.getElementsByTagName("tbody")[0];
if(!_b07){
this.parseDataFromTable(body);
}
for(var i=0;i<this.data.length;i++){
data.push(this.data[i]);
}
var col=this.columns[this.sortIndex];
if(!col.noSort){
var _b0c=col.getField();
if(col.sortFunction){
var sort=col.sortFunction;
}else{
var sort=function(a,b){
if(a[_b0c]>b[_b0c]){
return 1;
}
if(a[_b0c]<b[_b0c]){
return -1;
}
return 0;
};
}
data.sort(sort);
if(this.sortDirection!=0){
data.reverse();
}
}
while(body.childNodes.length>0){
body.removeChild(body.childNodes[0]);
}
for(var i=0;i<data.length;i++){
var row=document.createElement("tr");
dojo.html.disableSelection(row);
if(data[i][this.valueField]){
row.setAttribute("value",data[i][this.valueField]);
}
if(this.isSelected(data[i])){
row.className=this.rowSelectedClass;
row.setAttribute("selected","true");
}else{
if(this.enableAlternateRows&&i%2==1){
row.className=this.rowAlternateClass;
}
}
for(var j=0;j<this.columns.length;j++){
var cell=document.createElement("td");
cell.setAttribute("align",this.columns[j].align);
cell.setAttribute("valign",this.columns[j].valign);
dojo.html.disableSelection(cell);
if(this.sortIndex==j){
cell.className=this.columnSelected;
}
if(this.columns[j].sortType=="__markup__"){
cell.innerHTML=data[i][this.columns[j].getField()];
for(var k=0;k<cell.childNodes.length;k++){
var node=cell.childNodes[k];
if(node&&node.nodeType==dojo.html.ELEMENT_NODE){
dojo.html.disableSelection(node);
}
}
}else{
if(this.columns[j].getType()==Date){
var _b15=this.defaultDateFormat;
if(this.columns[j].format){
_b15=this.columns[j].format;
}
cell.appendChild(document.createTextNode(dojo.date.strftime(data[i][this.columns[j].getField()],_b15)));
}else{
cell.appendChild(document.createTextNode(data[i][this.columns[j].getField()]));
}
}
row.appendChild(cell);
}
body.appendChild(row);
dojo.event.connect(row,"onclick",this,"onUISelect");
}
var _b16=parseInt(this.minRows);
if(!isNaN(_b16)&&_b16>0&&data.length<_b16){
var mod=0;
if(data.length%2==0){
mod=1;
}
var _b18=_b16-data.length;
for(var i=0;i<_b18;i++){
var row=document.createElement("tr");
row.setAttribute("ignoreIfParsed","true");
if(this.enableAlternateRows&&i%2==mod){
row.className=this.rowAlternateClass;
}
for(var j=0;j<this.columns.length;j++){
var cell=document.createElement("td");
cell.appendChild(document.createTextNode("\xa0"));
row.appendChild(cell);
}
body.appendChild(row);
}
}
},onSelect:function(e){
},onUISelect:function(e){
var row=dojo.html.getParentByType(e.target,"tr");
var body=dojo.html.getParentByType(row,"tbody");
if(this.enableMultipleSelect){
if(e.metaKey||e.ctrlKey){
if(this.isSelected(this.getObjectFromRow(row))){
this.removeFromSelected(this.getObjectFromRow(row));
row.removeAttribute("selected");
}else{
this.setSelectionByRow(row);
row.setAttribute("selected","true");
}
}else{
if(e.shiftKey){
var _b1d;
var rows=body.getElementsByTagName("tr");
for(var i=0;i<rows.length;i++){
if(rows[i].parentNode==body){
if(rows[i]==row){
break;
}
if(dojo.html.getAttribute(rows[i],"selected")=="true"){
_b1d=rows[i];
}
}
}
if(!_b1d){
_b1d=row;
for(;i<rows.length;i++){
if(dojo.html.getAttribute(rows[i],"selected")=="true"){
row=rows[i];
break;
}
}
}
this.resetSelections(body);
if(_b1d==row){
row.setAttribute("selected","true");
this.setSelectionByRow(row);
}else{
var _b20=false;
for(var i=0;i<rows.length;i++){
if(rows[i].parentNode==body){
rows[i].removeAttribute("selected");
if(rows[i]==_b1d){
_b20=true;
}
if(_b20){
this.setSelectionByRow(rows[i]);
rows[i].setAttribute("selected","true");
}
if(rows[i]==row){
_b20=false;
}
}
}
}
}else{
this.resetSelections(body);
row.setAttribute("selected","true");
this.setSelectionByRow(row);
}
}
}else{
this.resetSelections(body);
row.setAttribute("selected","true");
this.setSelectionByRow(row);
}
this.showSelections();
this.onSelect(e);
e.stopPropagation();
},onHeaderClick:function(e){
var _b22=this.sortIndex;
var _b23=this.sortDirection;
var _b24=e.target;
var row=dojo.html.getParentByType(_b24,"tr");
var _b26="td";
if(row.getElementsByTagName(_b26).length==0){
_b26="th";
}
var _b27=row.getElementsByTagName(_b26);
var _b28=dojo.html.getParentByType(_b24,_b26);
for(var i=0;i<_b27.length;i++){
if(_b27[i]==_b28){
if(i!=_b22){
this.sortIndex=i;
this.sortDirection=0;
_b27[i].className=this.headerSortDownClass;
}else{
this.sortDirection=(_b23==0)?1:0;
if(this.sortDirection==0){
_b27[i].className=this.headerSortDownClass;
}else{
_b27[i].className=this.headerSortUpClass;
}
}
}else{
_b27[i].className=this.headerClass;
}
}
this.render();
},postCreate:function(){
var _b2a=this.domNode.getElementsByTagName("thead")[0];
if(this.headClass.length>0){
_b2a.className=this.headClass;
}
dojo.html.disableSelection(this.domNode);
this.parseColumns(_b2a);
var _b2b="td";
if(_b2a.getElementsByTagName(_b2b).length==0){
_b2b="th";
}
var _b2c=_b2a.getElementsByTagName(_b2b);
for(var i=0;i<_b2c.length;i++){
if(!this.columns[i].noSort){
dojo.event.connect(_b2c[i],"onclick",this,"onHeaderClick");
}
if(this.sortIndex==i){
if(this.sortDirection==0){
_b2c[i].className=this.headerSortDownClass;
}else{
_b2c[i].className=this.headerSortUpClass;
}
}
}
var _b2e=this.domNode.getElementsByTagName("tbody")[0];
if(this.tbodyClass.length>0){
_b2e.className=this.tbodyClass;
}
this.parseDataFromTable(_b2e);
this.render(true);
}});
dojo.provide("dojo.math");
dojo.math.degToRad=function(x){
return (x*Math.PI)/180;
};
dojo.math.radToDeg=function(x){
return (x*180)/Math.PI;
};
dojo.math.factorial=function(n){
if(n<1){
return 0;
}
var _b37=1;
for(var i=1;i<=n;i++){
_b37*=i;
}
return _b37;
};
dojo.math.permutations=function(n,k){
if(n==0||k==0){
return 1;
}
return (dojo.math.factorial(n)/dojo.math.factorial(n-k));
};
dojo.math.combinations=function(n,r){
if(n==0||r==0){
return 1;
}
return (dojo.math.factorial(n)/(dojo.math.factorial(n-r)*dojo.math.factorial(r)));
};
dojo.math.bernstein=function(t,n,i){
return (dojo.math.combinations(n,i)*Math.pow(t,i)*Math.pow(1-t,n-i));
};
dojo.math.gaussianRandom=function(){
var k=2;
do{
var i=2*Math.random()-1;
var j=2*Math.random()-1;
k=i*i+j*j;
}while(k>=1);
k=Math.sqrt((-2*Math.log(k))/k);
return i*k;
};
dojo.math.mean=function(){
var _b43=dojo.lang.isArray(arguments[0])?arguments[0]:arguments;
var mean=0;
for(var i=0;i<_b43.length;i++){
mean+=_b43[i];
}
return mean/_b43.length;
};
dojo.math.round=function(_b46,_b47){
if(!_b47){
var _b48=1;
}else{
var _b48=Math.pow(10,_b47);
}
return Math.round(_b46*_b48)/_b48;
};
dojo.math.sd=dojo.math.standardDeviation=function(){
var _b49=dojo.lang.isArray(arguments[0])?arguments[0]:arguments;
return Math.sqrt(dojo.math.variance(_b49));
};
dojo.math.variance=function(){
var _b4a=dojo.lang.isArray(arguments[0])?arguments[0]:arguments;
var mean=0,_b4c=0;
for(var i=0;i<_b4a.length;i++){
mean+=_b4a[i];
_b4c+=Math.pow(_b4a[i],2);
}
return (_b4c/_b4a.length)-Math.pow(mean/_b4a.length,2);
};
dojo.math.range=function(a,b,step){
if(arguments.length<2){
b=a;
a=0;
}
if(arguments.length<3){
step=1;
}
var _b51=[];
if(step>0){
for(var i=a;i<b;i+=step){
_b51.push(i);
}
}else{
if(step<0){
for(var i=a;i>b;i+=step){
_b51.push(i);
}
}else{
throw new Error("dojo.math.range: step must be non-zero");
}
}
return _b51;
};
dojo.provide("dojo.collections.Store");
dojo.collections.Store=function(_b53){
var data=[];
var _b55={};
this.keyField="Id";
this.get=function(){
return data;
};
this.getByKey=function(key){
return _b55[key];
};
this.getByIndex=function(idx){
return data[idx];
};
this.getIndexOf=function(key){
for(var i=0;i<data.length;i++){
if(data[i].key==key){
return i;
}
}
return -1;
};
this.getData=function(){
var arr=[];
for(var i=0;i<data.length;i++){
arr.push(data[i].src);
}
return arr;
};
this.getDataByKey=function(key){
if(_b55[key]!=null){
return _b55[key].src;
}
return null;
};
this.getIndexOfData=function(obj){
for(var i=0;i<data.length;i++){
if(data[i].src==obj){
return i;
}
}
return -1;
};
this.getDataByIndex=function(idx){
if(data[idx]){
return data[idx].src;
}
return null;
};
this.update=function(obj,_b61,val,_b63){
var _b64=_b61.split("."),i=0,o=obj,_b67;
if(_b64.length>1){
_b67=_b64.pop();
do{
if(_b64[i].indexOf("()")>-1){
var temp=_b64[i++].split("()")[0];
if(!o[temp]){
dojo.raise("dojo.collections.Store.getField(obj, '"+_b67+"'): '"+temp+"' is not a property of the passed object.");
}else{
o=o[temp]();
}
}else{
o=o[_b64[i++]];
}
}while(i<_b64.length&&o!=null);
}else{
_b67=_b64[0];
}
obj[_b67]=val;
if(!_b63){
this.onUpdateField(obj,_b61,val);
}
};
this.forEach=function(fn){
if(Array.forEach){
Array.forEach(data,fn,this);
}else{
for(var i=0;i<data.length;i++){
fn.call(this,data[i]);
}
}
};
this.forEachData=function(fn){
if(Array.forEach){
Array.forEach(this.getData(),fn,this);
}else{
var a=this.getData();
for(var i=0;i<a.length;i++){
fn.call(this,a[i]);
}
}
};
this.setData=function(arr,_b6f){
data=[];
for(var i=0;i<arr.length;i++){
var o={key:arr[i][this.keyField],src:arr[i]};
data.push(o);
_b55[o.key]=o;
}
if(!_b6f){
this.onSetData();
}
};
this.clearData=function(_b72){
data=[];
_b55={};
if(!_b72){
this.onClearData();
}
};
this.addData=function(obj,key,_b75){
var k=key||obj[this.keyField];
if(_b55[k]!=null){
var o=_b55[k];
o.src=obj;
}else{
var o={key:k,src:obj};
data.push(o);
_b55[o.key]=o;
}
if(!_b75){
this.onAddData(o);
}
};
this.addDataRange=function(arr,_b79){
var _b7a=[];
for(var i=0;i<arr.length;i++){
var k=arr[i][this.keyField];
if(_b55[k]!=null){
var o=_b55[k];
o.src=arr[i];
}else{
var o={key:k,src:arr[i]};
data.push(o);
_b55[k]=o;
}
_b7a.push(o);
}
if(!_b79){
this.onAddDataRange(_b7a);
}
};
this.addDataByIndex=function(obj,idx,key,_b81){
var k=key||obj[this.keyField];
if(_b55[k]!=null){
var i=this.getIndexOf(k);
var o=data.splice(i,1);
o.src=obj;
}else{
var o={key:k,src:obj};
_b55[k]=o;
}
data.splice(idx,0,o);
if(!_b81){
this.onAddData(o);
}
};
this.addDataRangeByIndex=function(arr,idx,_b87){
var _b88=[];
for(var i=0;i<arr.length;i++){
var k=arr[i][this.keyField];
if(_b55[k]!=null){
var j=this.getIndexOf(k);
var o=data.splice(j,1);
o.src=arr[i];
}else{
var o={key:k,src:arr[i]};
_b55[k]=o;
}
_b88.push(o);
}
data.splice(idx,0,_b88);
if(!_b87){
this.onAddDataRange(_b88);
}
};
this.removeData=function(obj,_b8e){
var idx=-1;
var o=null;
for(var i=0;i<data.length;i++){
if(data[i].src==obj){
idx=i;
o=data[i];
break;
}
}
if(!_b8e){
this.onRemoveData(o);
}
if(idx>-1){
data.splice(idx,1);
delete _b55[o.key];
}
};
this.removeDataRange=function(idx,_b93,_b94){
var ret=data.splice(idx,_b93);
for(var i=0;i<ret.length;i++){
delete _b55[ret[i].key];
}
if(!_b94){
this.onRemoveDataRange(ret);
}
return ret;
};
this.removeDataByKey=function(key,_b98){
this.removeData(this.getDataByKey(key),_b98);
};
this.removeDataByIndex=function(idx,_b9a){
this.removeData(this.getDataByIndex(idx),_b9a);
};
if(_b53&&_b53.length&&_b53[0]){
this.setData(_b53,true);
}
};
dojo.extend(dojo.collections.Store,{getField:function(obj,_b9c){
var _b9d=_b9c.split("."),i=0,o=obj;
do{
if(_b9d[i].indexOf("()")>-1){
var temp=_b9d[i++].split("()")[0];
if(!o[temp]){
dojo.raise("dojo.collections.Store.getField(obj, '"+_b9c+"'): '"+temp+"' is not a property of the passed object.");
}else{
o=o[temp]();
}
}else{
o=o[_b9d[i++]];
}
}while(i<_b9d.length&&o!=null);
if(i<_b9d.length){
dojo.raise("dojo.collections.Store.getField(obj, '"+_b9c+"'): '"+_b9c+"' is not a property of the passed object.");
}
return o;
},getFromHtml:function(meta,body,_ba3){
var rows=body.rows;
var ctor=function(row){
var obj={};
for(var i=0;i<meta.length;i++){
var o=obj;
var data=row.cells[i].innerHTML;
var p=meta[i].getField();
if(p.indexOf(".")>-1){
p=p.split(".");
while(p.length>1){
var pr=p.shift();
o[pr]={};
o=o[pr];
}
p=p[0];
}
var type=meta[i].getType();
if(type==String){
o[p]=data;
}else{
if(data){
o[p]=new type(data);
}else{
o[p]=new type();
}
}
}
return obj;
};
var arr=[];
for(var i=0;i<rows.length;i++){
var o=ctor(rows[i]);
if(_ba3){
_ba3(o,rows[i]);
}
arr.push(o);
}
return arr;
},onSetData:function(){
},onClearData:function(){
},onAddData:function(obj){
},onAddDataRange:function(arr){
},onRemoveData:function(obj){
},onRemoveDataRange:function(arr){
},onUpdateField:function(obj,_bb6,val){
}});
dojo.provide("dojo.widget.FilteringTable");
dojo.widget.defineWidget("dojo.widget.FilteringTable",dojo.widget.HtmlWidget,function(){
this.store=new dojo.collections.Store();
this.valueField="Id";
this.multiple=false;
this.maxSelect=0;
this.maxSortable=1;
this.minRows=0;
this.defaultDateFormat="%D";
this.isInitialized=false;
this.alternateRows=false;
this.columns=[];
this.sortInformation=[{index:0,direction:0}];
this.headClass="";
this.tbodyClass="";
this.headerClass="";
this.headerUpClass="selectedUp";
this.headerDownClass="selectedDown";
this.rowClass="";
this.rowAlternateClass="alt";
this.rowSelectedClass="selected";
this.columnSelected="sorted-column";
},{isContainer:false,templatePath:null,templateCssPath:null,getTypeFromString:function(s){
var _bb9=s.split("."),i=0,obj=dj_global;
do{
obj=obj[_bb9[i++]];
}while(i<_bb9.length&&obj);
return (obj!=dj_global)?obj:null;
},getByRow:function(row){
return this.store.getByKey(dojo.html.getAttribute(row,"value"));
},getDataByRow:function(row){
return this.store.getDataByKey(dojo.html.getAttribute(row,"value"));
},getRow:function(obj){
var rows=this.domNode.tBodies[0].rows;
for(var i=0;i<rows.length;i++){
if(this.store.getDataByKey(dojo.html.getAttribute(rows[i],"value"))==obj){
return rows[i];
}
}
return null;
},getColumnIndex:function(_bc1){
for(var i=0;i<this.columns.length;i++){
if(this.columns[i].getField()==_bc1){
return i;
}
}
return -1;
},getSelectedData:function(){
var data=this.store.get();
var a=[];
for(var i=0;i<data.length;i++){
if(data[i].isSelected){
a.push(data[i].src);
}
}
if(this.multiple){
return a;
}else{
return a[0];
}
},isSelected:function(obj){
var data=this.store.get();
for(var i=0;i<data.length;i++){
if(data[i].src==obj){
return true;
}
}
return false;
},isValueSelected:function(val){
var v=this.store.getByKey(val);
if(v){
return v.isSelected;
}
return false;
},isIndexSelected:function(idx){
var v=this.store.getByIndex(idx);
if(v){
return v.isSelected;
}
return false;
},isRowSelected:function(row){
var v=this.getByRow(row);
if(v){
return v.isSelected;
}
return false;
},reset:function(){
this.store.clearData();
this.columns=[];
this.sortInformation=[{index:0,direction:0}];
this.resetSelections();
this.isInitialized=false;
this.onReset();
},resetSelections:function(){
this.store.forEach(function(_bcf){
_bcf.isSelected=false;
});
},onReset:function(){
},select:function(obj){
var data=this.store.get();
for(var i=0;i<data.length;i++){
if(data[i].src==obj){
data[i].isSelected=true;
break;
}
}
this.onDataSelect(obj);
},selectByValue:function(val){
this.select(this.store.getDataByKey(val));
},selectByIndex:function(idx){
this.select(this.store.getDataByIndex(idx));
},selectByRow:function(row){
this.select(this.getDataByRow(row));
},selectAll:function(){
this.store.forEach(function(_bd6){
_bd6.isSelected=true;
});
},onDataSelect:function(obj){
},toggleSelection:function(obj){
var data=this.store.get();
for(var i=0;i<data.length;i++){
if(data[i].src==obj){
data[i].isSelected=!data[i].isSelected;
break;
}
}
this.onDataToggle(obj);
},toggleSelectionByValue:function(val){
this.toggleSelection(this.store.getDataByKey(val));
},toggleSelectionByIndex:function(idx){
this.toggleSelection(this.store.getDataByIndex(idx));
},toggleSelectionByRow:function(row){
this.toggleSelection(this.getDataByRow(row));
},toggleAll:function(){
this.store.forEach(function(_bde){
_bde.isSelected=!_bde.isSelected;
});
},onDataToggle:function(obj){
},_meta:{field:null,format:null,filterer:null,noSort:false,sortType:"String",dataType:String,sortFunction:null,filterFunction:null,label:null,align:"left",valign:"middle",getField:function(){
return this.field||this.label;
},getType:function(){
return this.dataType;
}},createMetaData:function(obj){
for(var p in this._meta){
if(!obj[p]){
obj[p]=this._meta[p];
}
}
if(!obj.label){
obj.label=obj.field;
}
if(!obj.filterFunction){
obj.filterFunction=this._defaultFilter;
}
return obj;
},parseMetadata:function(head){
this.columns=[];
this.sortInformation=[];
var row=head.getElementsByTagName("tr")[0];
var _be4=row.getElementsByTagName("td");
if(_be4.length==0){
_be4=row.getElementsByTagName("th");
}
for(var i=0;i<_be4.length;i++){
var o=this.createMetaData({});
if(dojo.html.hasAttribute(_be4[i],"align")){
o.align=dojo.html.getAttribute(_be4[i],"align");
}
if(dojo.html.hasAttribute(_be4[i],"valign")){
o.valign=dojo.html.getAttribute(_be4[i],"valign");
}
if(dojo.html.hasAttribute(_be4[i],"nosort")){
o.noSort=(dojo.html.getAttribute(_be4[i],"nosort")=="true");
}
if(dojo.html.hasAttribute(_be4[i],"sortusing")){
var _be7=dojo.html.getAttribute(_be4[i],"sortusing");
var f=this.getTypeFromString(_be7);
if(f!=null&&f!=window&&typeof (f)=="function"){
o.sortFunction=f;
}
}
o.label=dojo.html.renderedTextContent(_be4[i]);
if(dojo.html.hasAttribute(_be4[i],"field")){
o.field=dojo.html.getAttribute(_be4[i],"field");
}else{
if(o.label.length>0){
o.field=o.label;
}else{
o.field="field"+i;
}
}
if(dojo.html.hasAttribute(_be4[i],"format")){
o.format=dojo.html.getAttribute(_be4[i],"format");
}
if(dojo.html.hasAttribute(_be4[i],"dataType")){
var _be9=dojo.html.getAttribute(_be4[i],"dataType");
if(_be9.toLowerCase()=="html"||_be9.toLowerCase()=="markup"){
o.sortType="__markup__";
}else{
var type=this.getTypeFromString(_be9);
if(type){
o.sortType=_be9;
o.dataType=type;
}
}
}
if(dojo.html.hasAttribute(_be4[i],"filterusing")){
var _be7=dojo.html.getAttribute(_be4[i],"filterusing");
var f=this.getTypeFromString(_be7);
if(f!=null&&f!=window&&typeof (f)=="function"){
o.filterFunction=f;
}
}
this.columns.push(o);
if(dojo.html.hasAttribute(_be4[i],"sort")){
var info={index:i,direction:0};
var dir=dojo.html.getAttribute(_be4[i],"sort");
if(!isNaN(parseInt(dir))){
dir=parseInt(dir);
info.direction=(dir!=0)?1:0;
}else{
info.direction=(dir.toLowerCase()=="desc")?1:0;
}
this.sortInformation.push(info);
}
}
if(this.sortInformation.length==0){
this.sortInformation.push({index:0,direction:0});
}else{
if(this.sortInformation.length>this.maxSortable){
this.sortInformation.length=this.maxSortable;
}
}
},parseData:function(body){
if(body.rows.length==0&&this.columns.length==0){
return;
}
var self=this;
this["__selected__"]=[];
var arr=this.store.getFromHtml(this.columns,body,function(obj,row){
if(typeof (obj[self.valueField])=="undefined"||obj[self.valueField]==null){
obj[self.valueField]=dojo.html.getAttribute(row,"value");
}
if(dojo.html.getAttribute(row,"selected")=="true"){
self["__selected__"].push(obj);
}
});
this.store.setData(arr,true);
this.render();
for(var i=0;i<this["__selected__"].length;i++){
this.select(this["__selected__"][i]);
}
this.renderSelections();
delete this["__selected__"];
this.isInitialized=true;
},onSelect:function(e){
var row=dojo.html.getParentByType(e.target,"tr");
if(dojo.html.hasAttribute(row,"emptyRow")){
return;
}
var body=dojo.html.getParentByType(row,"tbody");
if(this.multiple){
if(e.shiftKey){
var _bf6;
var rows=body.rows;
for(var i=0;i<rows.length;i++){
if(rows[i]==row){
break;
}
if(this.isRowSelected(rows[i])){
_bf6=rows[i];
}
}
if(!_bf6){
_bf6=row;
for(;i<rows.length;i++){
if(this.isRowSelected(rows[i])){
row=rows[i];
break;
}
}
}
this.resetSelections();
if(_bf6==row){
this.toggleSelectionByRow(row);
}else{
var _bf9=false;
for(var i=0;i<rows.length;i++){
if(rows[i]==_bf6){
_bf9=true;
}
if(_bf9){
this.selectByRow(rows[i]);
}
if(rows[i]==row){
_bf9=false;
}
}
}
}else{
this.toggleSelectionByRow(row);
}
}else{
this.resetSelections();
this.toggleSelectionByRow(row);
}
this.renderSelections();
},onSort:function(e){
var _bfb=this.sortIndex;
var _bfc=this.sortDirection;
var _bfd=e.target;
var row=dojo.html.getParentByType(_bfd,"tr");
var _bff="td";
if(row.getElementsByTagName(_bff).length==0){
_bff="th";
}
var _c00=row.getElementsByTagName(_bff);
var _c01=dojo.html.getParentByType(_bfd,_bff);
for(var i=0;i<_c00.length;i++){
dojo.html.setClass(_c00[i],this.headerClass);
if(_c00[i]==_c01){
if(this.sortInformation[0].index!=i){
this.sortInformation.unshift({index:i,direction:0});
}else{
this.sortInformation[0]={index:i,direction:(~this.sortInformation[0].direction)&1};
}
}
}
this.sortInformation.length=Math.min(this.sortInformation.length,this.maxSortable);
for(var i=0;i<this.sortInformation.length;i++){
var idx=this.sortInformation[i].index;
var dir=(~this.sortInformation[i].direction)&1;
dojo.html.setClass(_c00[idx],dir==0?this.headerDownClass:this.headerUpClass);
}
this.render();
},onFilter:function(){
},_defaultFilter:function(obj){
return true;
},setFilter:function(_c06,fn){
for(var i=0;i<this.columns.length;i++){
if(this.columns[i].getField()==_c06){
this.columns[i].filterFunction=fn;
break;
}
}
this.applyFilters();
},setFilterByIndex:function(idx,fn){
this.columns[idx].filterFunction=fn;
this.applyFilters();
},clearFilter:function(_c0b){
for(var i=0;i<this.columns.length;i++){
if(this.columns[i].getField()==_c0b){
this.columns[i].filterFunction=this._defaultFilter;
break;
}
}
this.applyFilters();
},clearFilterByIndex:function(idx){
this.columns[idx].filterFunction=this._defaultFilter;
this.applyFilters();
},clearFilters:function(){
for(var i=0;i<this.columns.length;i++){
this.columns[i].filterFunction=this._defaultFilter;
}
var rows=this.domNode.tBodies[0].rows;
for(var i=0;i<rows.length;i++){
rows[i].style.display="";
if(this.alternateRows){
dojo.html[((i%2==1)?"addClass":"removeClass")](rows[i],this.rowAlternateClass);
}
}
this.onFilter();
},applyFilters:function(){
var alt=0;
var rows=this.domNode.tBodies[0].rows;
for(var i=0;i<rows.length;i++){
var b=true;
var row=rows[i];
for(var j=0;j<this.columns.length;j++){
var _c16=this.store.getField(this.getDataByRow(row),this.columns[j].getField());
if(this.columns[j].getType()==Date&&_c16!=null&&!_c16.getYear){
_c16=new Date(_c16);
}
if(!this.columns[j].filterFunction(_c16)){
b=false;
break;
}
}
row.style.display=(b?"":"none");
if(b&&this.alternateRows){
dojo.html[((alt++%2==1)?"addClass":"removeClass")](row,this.rowAlternateClass);
}
}
this.onFilter();
},createSorter:function(info){
var self=this;
var _c19=[];
function createSortFunction(_c1a,dir){
var meta=self.columns[_c1a];
var _c1d=meta.getField();
return function(rowA,rowB){
if(dojo.html.hasAttribute(rowA,"emptyRow")){
return 1;
}
if(dojo.html.hasAttribute(rowB,"emptyRow")){
return -1;
}
var a=self.store.getField(self.getDataByRow(rowA),_c1d);
var b=self.store.getField(self.getDataByRow(rowB),_c1d);
var ret=0;
if(a>b){
ret=1;
}
if(a<b){
ret=-1;
}
return dir*ret;
};
}
var _c23=0;
var max=Math.min(info.length,this.maxSortable,this.columns.length);
while(_c23<max){
var _c25=(info[_c23].direction==0)?1:-1;
_c19.push(createSortFunction(info[_c23].index,_c25));
_c23++;
}
return function(rowA,rowB){
var idx=0;
while(idx<_c19.length){
var ret=_c19[idx++](rowA,rowB);
if(ret!=0){
return ret;
}
}
return 0;
};
},createRow:function(obj){
var row=document.createElement("tr");
dojo.html.disableSelection(row);
if(obj.key!=null){
row.setAttribute("value",obj.key);
}
for(var j=0;j<this.columns.length;j++){
var cell=document.createElement("td");
cell.setAttribute("align",this.columns[j].align);
cell.setAttribute("valign",this.columns[j].valign);
dojo.html.disableSelection(cell);
var val=this.store.getField(obj.src,this.columns[j].getField());
if(typeof (val)=="undefined"){
val="";
}
this.fillCell(cell,this.columns[j],val);
row.appendChild(cell);
}
return row;
},fillCell:function(cell,meta,val){
if(meta.sortType=="__markup__"){
cell.innerHTML=val;
}else{
if(meta.getType()==Date){
val=new Date(val);
if(!isNaN(val)){
var _c32=this.defaultDateFormat;
if(meta.format){
_c32=meta.format;
}
cell.innerHTML=dojo.date.strftime(val,_c32);
}else{
cell.innerHTML=val;
}
}else{
if("Number number int Integer float Float".indexOf(meta.getType())>-1){
if(val.length==0){
val="0";
}
var n=parseFloat(val,10)+"";
if(n.indexOf(".")>-1){
n=dojo.math.round(parseFloat(val,10),2);
}
cell.innerHTML=n;
}else{
cell.innerHTML=val;
}
}
}
},prefill:function(){
this.isInitialized=false;
var body=this.domNode.tBodies[0];
while(body.childNodes.length>0){
body.removeChild(body.childNodes[0]);
}
if(this.minRows>0){
for(var i=0;i<this.minRows;i++){
var row=document.createElement("tr");
if(this.alternateRows){
dojo.html[((i%2==1)?"addClass":"removeClass")](row,this.rowAlternateClass);
}
row.setAttribute("emptyRow","true");
for(var j=0;j<this.columns.length;j++){
var cell=document.createElement("td");
cell.innerHTML="&nbsp;";
row.appendChild(cell);
}
body.appendChild(row);
}
}
},init:function(){
this.isInitialized=false;
var head=this.domNode.getElementsByTagName("thead")[0];
if(head.getElementsByTagName("tr").length==0){
var row=document.createElement("tr");
for(var i=0;i<this.columns.length;i++){
var cell=document.createElement("td");
cell.setAttribute("align",this.columns[i].align);
cell.setAttribute("valign",this.columns[i].valign);
dojo.html.disableSelection(cell);
cell.innerHTML=this.columns[i].label;
row.appendChild(cell);
if(!this.columns[i].noSort){
dojo.event.connect(cell,"onclick",this,"onSort");
}
}
dojo.html.prependChild(row,head);
}
if(this.store.get().length==0){
return false;
}
var idx=this.domNode.tBodies[0].rows.length;
if(!idx||idx==0||this.domNode.tBodies[0].rows[0].getAttribute("emptyRow")=="true"){
idx=0;
var body=this.domNode.tBodies[0];
while(body.childNodes.length>0){
body.removeChild(body.childNodes[0]);
}
var data=this.store.get();
for(var i=0;i<data.length;i++){
var row=this.createRow(data[i]);
body.appendChild(row);
idx++;
}
}
if(this.minRows>0&&idx<this.minRows){
idx=this.minRows-idx;
for(var i=0;i<idx;i++){
row=document.createElement("tr");
row.setAttribute("emptyRow","true");
for(var j=0;j<this.columns.length;j++){
cell=document.createElement("td");
cell.innerHTML="&nbsp;";
row.appendChild(cell);
}
body.appendChild(row);
}
}
var row=this.domNode.getElementsByTagName("thead")[0].rows[0];
var _c41="td";
if(row.getElementsByTagName(_c41).length==0){
_c41="th";
}
var _c42=row.getElementsByTagName(_c41);
for(var i=0;i<_c42.length;i++){
dojo.html.setClass(_c42[i],this.headerClass);
}
for(var i=0;i<this.sortInformation.length;i++){
var idx=this.sortInformation[i].index;
var dir=(~this.sortInformation[i].direction)&1;
dojo.html.setClass(_c42[idx],dir==0?this.headerDownClass:this.headerUpClass);
}
this.isInitialized=true;
return this.isInitialized;
},render:function(){
if(!this.isInitialized){
var b=this.init();
if(!b){
this.prefill();
return;
}
}
var rows=[];
var body=this.domNode.tBodies[0];
var _c47=-1;
for(var i=0;i<body.rows.length;i++){
rows.push(body.rows[i]);
}
var _c49=this.createSorter(this.sortInformation);
if(_c49){
rows.sort(_c49);
}
for(var i=0;i<rows.length;i++){
if(this.alternateRows){
dojo.html[((i%2==1)?"addClass":"removeClass")](rows[i],this.rowAlternateClass);
}
dojo.html[(this.isRowSelected(body.rows[i])?"addClass":"removeClass")](body.rows[i],this.rowSelectedClass);
body.appendChild(rows[i]);
}
},renderSelections:function(){
var body=this.domNode.tBodies[0];
for(var i=0;i<body.rows.length;i++){
dojo.html[(this.isRowSelected(body.rows[i])?"addClass":"removeClass")](body.rows[i],this.rowSelectedClass);
}
},initialize:function(){
var self=this;
dojo.event.connect(this.store,"onSetData",function(){
self.store.forEach(function(_c4d){
_c4d.isSelected=false;
});
self.isInitialized=false;
var body=self.domNode.tBodies[0];
if(body){
while(body.childNodes.length>0){
body.removeChild(body.childNodes[0]);
}
}
self.render();
});
dojo.event.connect(this.store,"onClearData",function(){
self.isInitialized=false;
self.render();
});
dojo.event.connect(this.store,"onAddData",function(_c4f){
var row=self.createRow(_c4f);
self.domNode.tBodies[0].appendChild(row);
self.render();
});
dojo.event.connect(this.store,"onAddDataRange",function(arr){
for(var i=0;i<arr.length;i++){
arr[i].isSelected=false;
var row=self.createRow(arr[i]);
self.domNode.tBodies[0].appendChild(row);
}
self.render();
});
dojo.event.connect(this.store,"onRemoveData",function(_c54){
var rows=self.domNode.tBodies[0].rows;
for(var i=0;i<rows.length;i++){
if(self.getDataByRow(rows[i])==_c54.src){
rows[i].parentNode.removeChild(rows[i]);
break;
}
}
self.render();
});
dojo.event.connect(this.store,"onUpdateField",function(obj,_c58,val){
var row=self.getRow(obj);
var idx=self.getColumnIndex(_c58);
if(row&&row.cells[idx]&&self.columns[idx]){
self.fillCell(row.cells[idx],self.columns[idx],val);
}
});
},postCreate:function(){
this.store.keyField=this.valueField;
if(this.domNode){
if(this.domNode.nodeName.toLowerCase()!="table"){
}
if(this.domNode.getElementsByTagName("thead")[0]){
var head=this.domNode.getElementsByTagName("thead")[0];
if(this.headClass.length>0){
head.className=this.headClass;
}
dojo.html.disableSelection(this.domNode);
this.parseMetadata(head);
var _c5d="td";
if(head.getElementsByTagName(_c5d).length==0){
_c5d="th";
}
var _c5e=head.getElementsByTagName(_c5d);
for(var i=0;i<_c5e.length;i++){
if(!this.columns[i].noSort){
dojo.event.connect(_c5e[i],"onclick",this,"onSort");
}
}
}else{
this.domNode.appendChild(document.createElement("thead"));
}
if(this.domNode.tBodies.length<1){
var body=document.createElement("tbody");
this.domNode.appendChild(body);
}else{
var body=this.domNode.tBodies[0];
}
if(this.tbodyClass.length>0){
body.className=this.tbodyClass;
}
dojo.event.connect(body,"onclick",this,"onSelect");
this.parseData(body);
}
}});
dojo.provide("dojo.widget.TitlePane");
dojo.widget.defineWidget("dojo.widget.TitlePane",dojo.widget.ContentPane,{labelNodeClass:"",containerNodeClass:"",label:"",open:true,templateString:"<div dojoAttachPoint=\"domNode\">\n<div dojoAttachPoint=\"labelNode\" dojoAttachEvent=\"onclick: onLabelClick\"></div>\n<div dojoAttachPoint=\"containerNode\"></div>\n</div>\n",postCreate:function(){
if(this.label){
this.labelNode.appendChild(document.createTextNode(this.label));
}
if(this.labelNodeClass){
dojo.html.addClass(this.labelNode,this.labelNodeClass);
}
if(this.containerNodeClass){
dojo.html.addClass(this.containerNode,this.containerNodeClass);
}
if(!this.open){
dojo.html.hide(this.containerNode);
}
dojo.widget.TitlePane.superclass.postCreate.apply(this,arguments);
},onLabelClick:function(){
if(this.open){
dojo.lfx.wipeOut(this.containerNode,250).play();
this.open=false;
}else{
dojo.lfx.wipeIn(this.containerNode,250).play();
this.open=true;
}
},setLabel:function(_c61){
this.labelNode.innerHTML=_c61;
}});
dojo.provide("dojo.Deferred");
dojo.Deferred=function(_c62){
this.chain=[];
this.id=this._nextId();
this.fired=-1;
this.paused=0;
this.results=[null,null];
this.canceller=_c62;
this.silentlyCancelled=false;
};
dojo.lang.extend(dojo.Deferred,{getFunctionFromArgs:function(){
var a=arguments;
if((a[0])&&(!a[1])){
if(dojo.lang.isFunction(a[0])){
return a[0];
}else{
if(dojo.lang.isString(a[0])){
return dj_global[a[0]];
}
}
}else{
if((a[0])&&(a[1])){
return dojo.lang.hitch(a[0],a[1]);
}
}
return null;
},makeCalled:function(){
var _c64=new dojo.Deferred();
_c64.callback();
return _c64;
},repr:function(){
var _c65;
if(this.fired==-1){
_c65="unfired";
}else{
if(this.fired==0){
_c65="success";
}else{
_c65="error";
}
}
return "Deferred("+this.id+", "+_c65+")";
},toString:dojo.lang.forward("repr"),_nextId:(function(){
var n=1;
return function(){
return n++;
};
})(),cancel:function(){
if(this.fired==-1){
if(this.canceller){
this.canceller(this);
}else{
this.silentlyCancelled=true;
}
if(this.fired==-1){
this.errback(new Error(this.repr()));
}
}else{
if((this.fired==0)&&(this.results[0] instanceof dojo.Deferred)){
this.results[0].cancel();
}
}
},_pause:function(){
this.paused++;
},_unpause:function(){
this.paused--;
if((this.paused==0)&&(this.fired>=0)){
this._fire();
}
},_continue:function(res){
this._resback(res);
this._unpause();
},_resback:function(res){
this.fired=((res instanceof Error)?1:0);
this.results[this.fired]=res;
this._fire();
},_check:function(){
if(this.fired!=-1){
if(!this.silentlyCancelled){
dojo.raise("already called!");
}
this.silentlyCancelled=false;
return;
}
},callback:function(res){
this._check();
this._resback(res);
},errback:function(res){
this._check();
if(!(res instanceof Error)){
res=new Error(res);
}
this._resback(res);
},addBoth:function(cb,cbfn){
var _c6d=this.getFunctionFromArgs(cb,cbfn);
if(arguments.length>2){
_c6d=dojo.lang.curryArguments(null,_c6d,arguments,2);
}
return this.addCallbacks(_c6d,_c6d);
},addCallback:function(cb,cbfn){
var _c70=this.getFunctionFromArgs(cb,cbfn);
if(arguments.length>2){
_c70=dojo.lang.curryArguments(null,_c70,arguments,2);
}
return this.addCallbacks(_c70,null);
},addErrback:function(cb,cbfn){
var _c73=this.getFunctionFromArgs(cb,cbfn);
if(arguments.length>2){
_c73=dojo.lang.curryArguments(null,_c73,arguments,2);
}
return this.addCallbacks(null,_c73);
return this.addCallbacks(null,cbfn);
},addCallbacks:function(cb,eb){
this.chain.push([cb,eb]);
if(this.fired>=0){
this._fire();
}
return this;
},_fire:function(){
var _c76=this.chain;
var _c77=this.fired;
var res=this.results[_c77];
var self=this;
var cb=null;
while(_c76.length>0&&this.paused==0){
var pair=_c76.shift();
var f=pair[_c77];
if(f==null){
continue;
}
try{
res=f(res);
_c77=((res instanceof Error)?1:0);
if(res instanceof dojo.Deferred){
cb=function(res){
self._continue(res);
};
this._pause();
}
}
catch(err){
_c77=1;
res=err;
}
}
this.fired=_c77;
this.results[_c77]=res;
if((cb)&&(this.paused)){
res.addBoth(cb);
}
}});
dojo.provide("dojo.widget.RichText");
if(!djConfig["useXDomain"]||djConfig["allowXdRichTextSave"]){
if(dojo.hostenv.post_load_){
(function(){
var _c7e=dojo.doc().createElement("textarea");
_c7e.id="dojo.widget.RichText.savedContent";
_c7e.style="display:none;position:absolute;top:-100px;left:-100px;height:3px;width:3px;overflow:hidden;";
dojo.body().appendChild(_c7e);
})();
}else{
try{
dojo.doc().write("<textarea id=\"dojo.widget.RichText.savedContent\" "+"style=\"display:none;position:absolute;top:-100px;left:-100px;height:3px;width:3px;overflow:hidden;\"></textarea>");
}
catch(e){
}
}
}
dojo.widget.defineWidget("dojo.widget.RichText",dojo.widget.HtmlWidget,function(){
this.contentPreFilters=[];
this.contentPostFilters=[];
this.contentDomPreFilters=[];
this.contentDomPostFilters=[];
this.editingAreaStyleSheets=[];
if(dojo.render.html.moz){
this.contentPreFilters.push(this._fixContentForMoz);
}
this._keyHandlers={};
if(dojo.Deferred){
this.onLoadDeferred=new dojo.Deferred();
}
},{inheritWidth:false,focusOnLoad:false,saveName:"",styleSheets:"",_content:"",height:"",minHeight:"1em",isClosed:true,isLoaded:false,useActiveX:false,relativeImageUrls:false,_SEPARATOR:"@@**%%__RICHTEXTBOUNDRY__%%**@@",onLoadDeferred:null,fillInTemplate:function(){
dojo.event.topic.publish("dojo.widget.RichText::init",this);
this.open();
dojo.event.connect(this,"onKeyPressed",this,"afterKeyPress");
dojo.event.connect(this,"onKeyPress",this,"keyPress");
dojo.event.connect(this,"onKeyDown",this,"keyDown");
dojo.event.connect(this,"onKeyUp",this,"keyUp");
this.setupDefaultShortcuts();
},setupDefaultShortcuts:function(){
var ctrl=this.KEY_CTRL;
var exec=function(cmd,arg){
return arguments.length==1?function(){
this.execCommand(cmd);
}:function(){
this.execCommand(cmd,arg);
};
};
this.addKeyHandler("b",ctrl,exec("bold"));
this.addKeyHandler("i",ctrl,exec("italic"));
this.addKeyHandler("u",ctrl,exec("underline"));
this.addKeyHandler("a",ctrl,exec("selectall"));
this.addKeyHandler("s",ctrl,function(){
this.save(true);
});
this.addKeyHandler("1",ctrl,exec("formatblock","h1"));
this.addKeyHandler("2",ctrl,exec("formatblock","h2"));
this.addKeyHandler("3",ctrl,exec("formatblock","h3"));
this.addKeyHandler("4",ctrl,exec("formatblock","h4"));
this.addKeyHandler("\\",ctrl,exec("insertunorderedlist"));
if(!dojo.render.html.ie){
this.addKeyHandler("Z",ctrl,exec("redo"));
}
},events:["onBlur","onFocus","onKeyPress","onKeyDown","onKeyUp","onClick"],open:function(_c83){
if(this.onLoadDeferred.fired>=0){
this.onLoadDeferred=new dojo.Deferred();
}
var h=dojo.render.html;
if(!this.isClosed){
this.close();
}
dojo.event.topic.publish("dojo.widget.RichText::open",this);
this._content="";
if((arguments.length==1)&&(_c83["nodeName"])){
this.domNode=_c83;
}
if((this.domNode["nodeName"])&&(this.domNode.nodeName.toLowerCase()=="textarea")){
this.textarea=this.domNode;
var html=this._preFilterContent(this.textarea.value);
this.domNode=dojo.doc().createElement("div");
dojo.html.copyStyle(this.domNode,this.textarea);
var _c86=dojo.lang.hitch(this,function(){
with(this.textarea.style){
display="block";
position="absolute";
left=top="-1000px";
if(h.ie){
this.__overflow=overflow;
overflow="hidden";
}
}
});
if(h.ie){
setTimeout(_c86,10);
}else{
_c86();
}
if(!h.safari){
dojo.html.insertBefore(this.domNode,this.textarea);
}
if(this.textarea.form){
dojo.event.connect("before",this.textarea.form,"onsubmit",dojo.lang.hitch(this,function(){
this.textarea.value=this.getEditorContent();
}));
}
var _c87=this;
dojo.event.connect(this,"postCreate",function(){
dojo.html.insertAfter(_c87.textarea,_c87.domNode);
});
}else{
var html=this._preFilterContent(dojo.string.trim(this.domNode.innerHTML));
}
if(html==""){
html="&nbsp;";
}
var _c88=dojo.html.getContentBox(this.domNode);
this._oldHeight=_c88.height;
this._oldWidth=_c88.width;
this._firstChildContributingMargin=this._getContributingMargin(this.domNode,"top");
this._lastChildContributingMargin=this._getContributingMargin(this.domNode,"bottom");
this.savedContent=html;
this.domNode.innerHTML="";
this.editingArea=dojo.doc().createElement("div");
this.domNode.appendChild(this.editingArea);
if((this.domNode["nodeName"])&&(this.domNode.nodeName=="LI")){
this.domNode.innerHTML=" <br>";
}
if(this.saveName!=""&&(!djConfig["useXDomain"]||djConfig["allowXdRichTextSave"])){
var _c89=dojo.doc().getElementById("dojo.widget.RichText.savedContent");
if(_c89.value!=""){
var _c8a=_c89.value.split(this._SEPARATOR);
for(var i=0;i<_c8a.length;i++){
var data=_c8a[i].split(":");
if(data[0]==this.saveName){
html=data[1];
_c8a.splice(i,1);
break;
}
}
}
dojo.event.connect("before",window,"onunload",this,"_saveContent");
}
if(h.ie70&&this.useActiveX){
dojo.debug("activeX in ie70 is not currently supported, useActiveX is ignored for now.");
this.useActiveX=false;
}
if(this.useActiveX&&h.ie){
var self=this;
setTimeout(function(){
self._drawObject(html);
},0);
}else{
if(h.ie||this._safariIsLeopard()||h.opera){
this.iframe=dojo.doc().createElement("iframe");
this.iframe.src="javascript:void(0)";
this.editorObject=this.iframe;
with(this.iframe.style){
border="0";
width="100%";
}
this.iframe.frameBorder=0;
this.editingArea.appendChild(this.iframe);
this.window=this.iframe.contentWindow;
this.document=this.window.document;
this.document.open();
this.document.write("<html><head><style>body{margin:0;padding:0;border:0;overflow:hidden;}</style></head><body><div></div></body></html>");
this.document.close();
this.editNode=this.document.body.firstChild;
this.editNode.contentEditable=true;
with(this.iframe.style){
if(h.ie70){
if(this.height){
height=this.height;
}
if(this.minHeight){
minHeight=this.minHeight;
}
}else{
height=this.height?this.height:this.minHeight;
}
}
var _c8e=["p","pre","address","h1","h2","h3","h4","h5","h6","ol","div","ul"];
var _c8f="";
for(var i=0;i<_c8e.length;i++){
if(_c8e[i].charAt(1)!="l"){
_c8f+="<"+_c8e[i]+"><span>content</span></"+_c8e[i]+">";
}else{
_c8f+="<"+_c8e[i]+"><li>content</li></"+_c8e[i]+">";
}
}
with(this.editNode.style){
position="absolute";
left="-2000px";
top="-2000px";
}
this.editNode.innerHTML=_c8f;
var node=this.editNode.firstChild;
while(node){
dojo.withGlobal(this.window,"selectElement",dojo.html.selection,[node.firstChild]);
var _c91=node.tagName.toLowerCase();
this._local2NativeFormatNames[_c91]=this.queryCommandValue("formatblock");
this._native2LocalFormatNames[this._local2NativeFormatNames[_c91]]=_c91;
node=node.nextSibling;
}
with(this.editNode.style){
position="";
left="";
top="";
}
this.editNode.innerHTML=html;
if(this.height){
this.document.body.style.overflowY="scroll";
}
dojo.lang.forEach(this.events,function(e){
dojo.event.connect(this.editNode,e.toLowerCase(),this,e);
},this);
this.onLoad();
}else{
this._drawIframe(html);
this.editorObject=this.iframe;
}
}
if(this.domNode.nodeName=="LI"){
this.domNode.lastChild.style.marginTop="-1.2em";
}
dojo.html.addClass(this.domNode,"RichTextEditable");
this.isClosed=false;
},_hasCollapseableMargin:function(_c93,side){
if(dojo.html.getPixelValue(_c93,"border-"+side+"-width",false)){
return false;
}else{
if(dojo.html.getPixelValue(_c93,"padding-"+side,false)){
return false;
}else{
return true;
}
}
},_getContributingMargin:function(_c95,_c96){
if(_c96=="top"){
var _c97="previousSibling";
var _c98="nextSibling";
var _c99="firstChild";
var _c9a="margin-top";
var _c9b="margin-bottom";
}else{
var _c97="nextSibling";
var _c98="previousSibling";
var _c99="lastChild";
var _c9a="margin-bottom";
var _c9b="margin-top";
}
var _c9c=dojo.html.getPixelValue(_c95,_c9a,false);
function isSignificantNode(_c9d){
return !(_c9d.nodeType==3&&dojo.string.isBlank(_c9d.data))&&dojo.html.getStyle(_c9d,"display")!="none"&&!dojo.html.isPositionAbsolute(_c9d);
}
var _c9e=0;
var _c9f=_c95[_c99];
while(_c9f){
while((!isSignificantNode(_c9f))&&_c9f[_c98]){
_c9f=_c9f[_c98];
}
_c9e=Math.max(_c9e,dojo.html.getPixelValue(_c9f,_c9a,false));
if(!this._hasCollapseableMargin(_c9f,_c96)){
break;
}
_c9f=_c9f[_c99];
}
if(!this._hasCollapseableMargin(_c95,_c96)){
return parseInt(_c9e);
}
var _ca0=0;
var _ca1=_c95[_c97];
while(_ca1){
if(isSignificantNode(_ca1)){
_ca0=dojo.html.getPixelValue(_ca1,_c9b,false);
break;
}
_ca1=_ca1[_c97];
}
if(!_ca1){
_ca0=dojo.html.getPixelValue(_c95.parentNode,_c9a,false);
}
if(_c9e>_c9c){
return parseInt(Math.max((_c9e-_c9c)-_ca0,0));
}else{
return 0;
}
},_drawIframe:function(html){
var _ca3=Boolean(dojo.render.html.moz&&(typeof window.XML=="undefined"));
if(!this.iframe){
var _ca4=(new dojo.uri.Uri(dojo.doc().location)).host;
this.iframe=dojo.doc().createElement("iframe");
with(this.iframe){
style.border="none";
style.lineHeight="0";
style.verticalAlign="bottom";
scrolling=this.height?"auto":"no";
}
}
if(djConfig["useXDomain"]&&!djConfig["dojoRichTextFrameUrl"]){
dojo.debug("dojo.widget.RichText: When using cross-domain Dojo builds,"+" please save src/widget/templates/richtextframe.html to your domain and set djConfig.dojoRichTextFrameUrl"+" to the path on your domain to richtextframe.html");
}
this.iframe.src=(djConfig["dojoRichTextFrameUrl"]||dojo.uri.moduleUri("dojo.widget","templates/richtextframe.html"))+((dojo.doc().domain!=_ca4)?("#"+dojo.doc().domain):"");
this.iframe.width=this.inheritWidth?this._oldWidth:"100%";
if(this.height){
this.iframe.style.height=this.height;
}else{
var _ca5=this._oldHeight;
if(this._hasCollapseableMargin(this.domNode,"top")){
_ca5+=this._firstChildContributingMargin;
}
if(this._hasCollapseableMargin(this.domNode,"bottom")){
_ca5+=this._lastChildContributingMargin;
}
this.iframe.height=_ca5;
}
var _ca6=dojo.doc().createElement("div");
_ca6.innerHTML=html;
this.editingArea.appendChild(_ca6);
if(this.relativeImageUrls){
var imgs=_ca6.getElementsByTagName("img");
for(var i=0;i<imgs.length;i++){
imgs[i].src=(new dojo.uri.Uri(dojo.global().location,imgs[i].src)).toString();
}
html=_ca6.innerHTML;
}
var _ca9=dojo.html.firstElement(_ca6);
var _caa=dojo.html.lastElement(_ca6);
if(_ca9){
_ca9.style.marginTop=this._firstChildContributingMargin+"px";
}
if(_caa){
_caa.style.marginBottom=this._lastChildContributingMargin+"px";
}
this.editingArea.appendChild(this.iframe);
if(dojo.render.html.safari){
this.iframe.src=this.iframe.src;
}
var _cab=false;
var _cac=dojo.lang.hitch(this,function(){
if(!_cab){
_cab=true;
}else{
return;
}
if(!this.editNode){
if(this.iframe.contentWindow){
this.window=this.iframe.contentWindow;
this.document=this.iframe.contentWindow.document;
}else{
if(this.iframe.contentDocument){
this.window=this.iframe.contentDocument.window;
this.document=this.iframe.contentDocument;
}
}
var _cad=(function(_cae){
return function(_caf){
return dojo.html.getStyle(_cae,_caf);
};
})(this.domNode);
var font=_cad("font-weight")+" "+_cad("font-size")+" "+_cad("font-family");
var _cb1="1.0";
var _cb2=dojo.html.getUnitValue(this.domNode,"line-height");
if(_cb2.value&&_cb2.units==""){
_cb1=_cb2.value;
}
dojo.html.insertCssText("body,html{background:transparent;padding:0;margin:0;}"+"body{top:0;left:0;right:0;"+(((this.height)||(dojo.render.html.opera))?"":"position:fixed;")+"font:"+font+";"+"min-height:"+this.minHeight+";"+"line-height:"+_cb1+"}"+"p{margin: 1em 0 !important;}"+"body > *:first-child{padding-top:0 !important;margin-top:"+this._firstChildContributingMargin+"px !important;}"+"body > *:last-child{padding-bottom:0 !important;margin-bottom:"+this._lastChildContributingMargin+"px !important;}"+"li > ul:-moz-first-node, li > ol:-moz-first-node{padding-top:1.2em;}\n"+"li{min-height:1.2em;}"+"",this.document);
dojo.html.removeNode(_ca6);
this.document.body.innerHTML=html;
if(_ca3||dojo.render.html.safari){
this.document.designMode="on";
}
this.onLoad();
}else{
dojo.html.removeNode(_ca6);
this.editNode.innerHTML=html;
this.onDisplayChanged();
}
});
if(this.editNode){
_cac();
}else{
if(dojo.render.html.moz){
this.iframe.onload=function(){
setTimeout(_cac,250);
};
}else{
this.iframe.onload=_cac;
}
}
},_applyEditingAreaStyleSheets:function(){
var _cb3=[];
if(this.styleSheets){
_cb3=this.styleSheets.split(";");
this.styleSheets="";
}
_cb3=_cb3.concat(this.editingAreaStyleSheets);
this.editingAreaStyleSheets=[];
if(_cb3.length>0){
for(var i=0;i<_cb3.length;i++){
var url=_cb3[i];
if(url){
this.addStyleSheet(dojo.uri.dojoUri(url));
}
}
}
},addStyleSheet:function(uri){
var url=uri.toString();
if(dojo.lang.find(this.editingAreaStyleSheets,url)>-1){
dojo.debug("dojo.widget.RichText.addStyleSheet: Style sheet "+url+" is already applied to the editing area!");
return;
}
if(url.charAt(0)=="."||(url.charAt(0)!="/"&&!uri.host)){
url=(new dojo.uri.Uri(dojo.global().location,url)).toString();
}
this.editingAreaStyleSheets.push(url);
if(this.document.createStyleSheet){
this.document.createStyleSheet(url);
}else{
var head=this.document.getElementsByTagName("head")[0];
var _cb9=this.document.createElement("link");
with(_cb9){
rel="stylesheet";
type="text/css";
href=url;
}
head.appendChild(_cb9);
}
},removeStyleSheet:function(uri){
var url=uri.toString();
if(url.charAt(0)=="."||(url.charAt(0)!="/"&&!uri.host)){
url=(new dojo.uri.Uri(dojo.global().location,url)).toString();
}
var _cbc=dojo.lang.find(this.editingAreaStyleSheets,url);
if(_cbc==-1){
dojo.debug("dojo.widget.RichText.removeStyleSheet: Style sheet "+url+" is not applied to the editing area so it can not be removed!");
return;
}
delete this.editingAreaStyleSheets[_cbc];
var _cbd=this.document.getElementsByTagName("link");
for(var i=0;i<_cbd.length;i++){
if(_cbd[i].href==url){
if(dojo.render.html.ie){
_cbd[i].href="";
}
dojo.html.removeNode(_cbd[i]);
break;
}
}
},_drawObject:function(html){
this.object=dojo.html.createExternalElement(dojo.doc(),"object");
with(this.object){
classid="clsid:2D360201-FFF5-11D1-8D03-00A0C959BC0A";
width=this.inheritWidth?this._oldWidth:"100%";
style.height=this.height?this.height:(this._oldHeight+"px");
Scrollbars=this.height?true:false;
Appearance=this._activeX.appearance.flat;
}
this.editorObject=this.object;
this.editingArea.appendChild(this.object);
this.object.attachEvent("DocumentComplete",dojo.lang.hitch(this,"onLoad"));
dojo.lang.forEach(this.events,function(e){
this.object.attachEvent(e.toLowerCase(),dojo.lang.hitch(this,e));
},this);
this.object.DocumentHTML="<!doctype HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">"+"<html><title></title>"+"<style type=\"text/css\">"+"    body,html { padding: 0; margin: 0; }"+(this.height?"":"    body,  { overflow: hidden; }")+"</style>"+"<body><div>"+html+"<div></body></html>";
this._cacheLocalBlockFormatNames();
},_local2NativeFormatNames:{},_native2LocalFormatNames:{},_cacheLocalBlockFormatNames:function(){
if(!this._native2LocalFormatNames["p"]){
var obj=this.object;
var _cc2=false;
if(!obj){
try{
obj=dojo.html.createExternalElement(dojo.doc(),"object");
obj.classid="clsid:2D360201-FFF5-11D1-8D03-00A0C959BC0A";
dojo.body().appendChild(obj);
obj.DocumentHTML="<html><head></head><body></body></html>";
}
catch(e){
_cc2=true;
}
}
try{
var _cc3=new ActiveXObject("DEGetBlockFmtNamesParam.DEGetBlockFmtNamesParam");
obj.ExecCommand(this._activeX.command["getblockformatnames"],0,_cc3);
var _cc4=new VBArray(_cc3.Names);
var _cc5=_cc4.toArray();
var _cc6=["p","pre","address","h1","h2","h3","h4","h5","h6","ol","ul","","","","","div"];
for(var i=0;i<_cc6.length;++i){
if(_cc6[i].length>0){
this._local2NativeFormatNames[_cc5[i]]=_cc6[i];
this._native2LocalFormatNames[_cc6[i]]=_cc5[i];
}
}
}
catch(e){
_cc2=true;
}
if(obj&&!this.object){
dojo.body().removeChild(obj);
}
}
return !_cc2;
},_isResized:function(){
return false;
},onLoad:function(e){
this.isLoaded=true;
if(this.object){
this.document=this.object.DOM;
this.window=this.document.parentWindow;
this.editNode=this.document.body.firstChild;
this.editingArea.style.height=this.height?this.height:this.minHeight;
if(!this.height){
this.connect(this,"onDisplayChanged","_updateHeight");
}
this.window._frameElement=this.object;
}else{
if(this.iframe&&!dojo.render.html.ie){
this.editNode=this.document.body;
if(!this.height){
this.connect(this,"onDisplayChanged","_updateHeight");
}
try{
this.document.execCommand("useCSS",false,true);
this.document.execCommand("styleWithCSS",false,false);
}
catch(e2){
}
if(dojo.render.html.safari){
this.connect(this.editNode,"onblur","onBlur");
this.connect(this.editNode,"onfocus","onFocus");
this.connect(this.editNode,"onclick","onFocus");
this.interval=setInterval(dojo.lang.hitch(this,"onDisplayChanged"),750);
}else{
if(dojo.render.html.mozilla||dojo.render.html.opera){
var doc=this.document;
var _cca=dojo.event.browser.addListener;
var self=this;
dojo.lang.forEach(this.events,function(e){
var l=_cca(self.document,e.substr(2).toLowerCase(),dojo.lang.hitch(self,e));
if(e=="onBlur"){
var _cce={unBlur:function(e){
dojo.event.browser.removeListener(doc,"blur",l);
}};
dojo.event.connect("before",self,"close",_cce,"unBlur");
}
});
}
}
}else{
if(dojo.render.html.ie){
if(!this.height){
this.connect(this,"onDisplayChanged","_updateHeight");
}
this.editNode.style.zoom=1;
}
}
}
this._applyEditingAreaStyleSheets();
if(this.focusOnLoad){
this.focus();
}
this.onDisplayChanged(e);
if(this.onLoadDeferred){
this.onLoadDeferred.callback(true);
}
},onKeyDown:function(e){
if((!e)&&(this.object)){
e=dojo.event.browser.fixEvent(this.window.event);
}
if((dojo.render.html.ie)&&(e.keyCode==e.KEY_TAB)){
e.preventDefault();
e.stopPropagation();
this.execCommand((e.shiftKey?"outdent":"indent"));
}else{
if(dojo.render.html.ie){
if((65<=e.keyCode)&&(e.keyCode<=90)){
e.charCode=e.keyCode;
this.onKeyPress(e);
}
}
}
},onKeyUp:function(e){
return;
},KEY_CTRL:1,onKeyPress:function(e){
if((!e)&&(this.object)){
e=dojo.event.browser.fixEvent(this.window.event);
}
var _cd3=e.ctrlKey?this.KEY_CTRL:0;
if(this._keyHandlers[e.key]){
var _cd4=this._keyHandlers[e.key],i=0,_cd6;
while(_cd6=_cd4[i++]){
if(_cd3==_cd6.modifiers){
e.preventDefault();
_cd6.handler.call(this);
break;
}
}
}
dojo.lang.setTimeout(this,this.onKeyPressed,1,e);
},addKeyHandler:function(key,_cd8,_cd9){
if(!(this._keyHandlers[key] instanceof Array)){
this._keyHandlers[key]=[];
}
this._keyHandlers[key].push({modifiers:_cd8||0,handler:_cd9});
},onKeyPressed:function(e){
this.onDisplayChanged();
},onClick:function(e){
this.onDisplayChanged(e);
},onBlur:function(e){
},_initialFocus:true,onFocus:function(e){
if((dojo.render.html.mozilla)&&(this._initialFocus)){
this._initialFocus=false;
if(dojo.string.trim(this.editNode.innerHTML)=="&nbsp;"){
this.placeCursorAtStart();
}
}
},blur:function(){
if(this.iframe){
this.window.blur();
}else{
if(this.object){
this.document.body.blur();
}else{
if(this.editNode){
this.editNode.blur();
}
}
}
},focus:function(){
if(this.iframe&&!dojo.render.html.ie){
this.window.focus();
}else{
if(this.object){
this.document.focus();
}else{
if(this.editNode&&this.editNode.focus){
this.editNode.focus();
}else{
dojo.debug("Have no idea how to focus into the editor!");
}
}
}
},onDisplayChanged:function(e){
},_activeX:{command:{bold:5000,italic:5023,underline:5048,justifycenter:5024,justifyleft:5025,justifyright:5026,cut:5003,copy:5002,paste:5032,"delete":5004,undo:5049,redo:5033,removeformat:5034,selectall:5035,unlink:5050,indent:5018,outdent:5031,insertorderedlist:5030,insertunorderedlist:5051,inserttable:5022,insertcell:5019,insertcol:5020,insertrow:5021,deletecells:5005,deletecols:5006,deleterows:5007,mergecells:5029,splitcell:5047,setblockformat:5043,getblockformat:5011,getblockformatnames:5012,setfontname:5044,getfontname:5013,setfontsize:5045,getfontsize:5014,setbackcolor:5042,getbackcolor:5010,setforecolor:5046,getforecolor:5015,findtext:5008,font:5009,hyperlink:5016,image:5017,lockelement:5027,makeabsolute:5028,sendbackward:5036,bringforward:5037,sendbelowtext:5038,bringabovetext:5039,sendtoback:5040,bringtofront:5041,properties:5052},ui:{"default":0,prompt:1,noprompt:2},status:{notsupported:0,disabled:1,enabled:3,latched:7,ninched:11},appearance:{flat:0,inset:1},state:{unchecked:0,checked:1,gray:2}},_normalizeCommand:function(cmd){
var drh=dojo.render.html;
var _ce1=cmd.toLowerCase();
if(_ce1=="formatblock"){
if(drh.safari){
_ce1="heading";
}
}else{
if(this.object){
switch(_ce1){
case "createlink":
_ce1="hyperlink";
break;
case "insertimage":
_ce1="image";
break;
}
}else{
if(_ce1=="hilitecolor"&&!drh.mozilla){
_ce1="backcolor";
}
}
}
return _ce1;
},_safariIsLeopard:function(){
var _ce2=false;
if(dojo.render.html.safari){
var tmp=dojo.render.html.UA.split("AppleWebKit/")[1];
var ver=parseFloat(tmp.split(" ")[0]);
if(ver>=420){
_ce2=true;
}
}
return _ce2;
},queryCommandAvailable:function(_ce5){
var ie=1;
var _ce7=1<<1;
var _ce8=1<<2;
var _ce9=1<<3;
var _cea=1<<4;
var _ceb=this._safariIsLeopard();
function isSupportedBy(_cec){
return {ie:Boolean(_cec&ie),mozilla:Boolean(_cec&_ce7),safari:Boolean(_cec&_ce8),safari420:Boolean(_cec&_cea),opera:Boolean(_cec&_ce9)};
}
var _ced=null;
switch(_ce5.toLowerCase()){
case "bold":
case "italic":
case "underline":
case "subscript":
case "superscript":
case "fontname":
case "fontsize":
case "forecolor":
case "hilitecolor":
case "justifycenter":
case "justifyfull":
case "justifyleft":
case "justifyright":
case "delete":
case "selectall":
_ced=isSupportedBy(_ce7|ie|_ce8|_ce9);
break;
case "createlink":
case "unlink":
case "removeformat":
case "inserthorizontalrule":
case "insertimage":
case "insertorderedlist":
case "insertunorderedlist":
case "indent":
case "outdent":
case "formatblock":
case "inserthtml":
case "undo":
case "redo":
case "strikethrough":
_ced=isSupportedBy(_ce7|ie|_ce9|_cea);
break;
case "blockdirltr":
case "blockdirrtl":
case "dirltr":
case "dirrtl":
case "inlinedirltr":
case "inlinedirrtl":
_ced=isSupportedBy(ie);
break;
case "cut":
case "copy":
case "paste":
_ced=isSupportedBy(ie|_ce7|_cea);
break;
case "inserttable":
_ced=isSupportedBy(_ce7|(this.object?ie:0));
break;
case "insertcell":
case "insertcol":
case "insertrow":
case "deletecells":
case "deletecols":
case "deleterows":
case "mergecells":
case "splitcell":
_ced=isSupportedBy(this.object?ie:0);
break;
default:
return false;
}
return (dojo.render.html.ie&&_ced.ie)||(dojo.render.html.mozilla&&_ced.mozilla)||(dojo.render.html.safari&&_ced.safari)||(_ceb&&_ced.safari420)||(dojo.render.html.opera&&_ced.opera);
},execCommand:function(_cee,_cef){
var _cf0;
this.focus();
_cee=this._normalizeCommand(_cee);
if(_cef!=undefined){
if(_cee=="heading"){
throw new Error("unimplemented");
}else{
if(_cee=="formatblock"){
if(this.object){
_cef=this._native2LocalFormatNames[_cef];
}else{
if(dojo.render.html.ie){
_cef="<"+_cef+">";
}
}
}
}
}
if(this.object){
switch(_cee){
case "hilitecolor":
_cee="setbackcolor";
break;
case "forecolor":
case "backcolor":
case "fontsize":
case "fontname":
_cee="set"+_cee;
break;
case "formatblock":
_cee="setblockformat";
}
if(_cee=="strikethrough"){
_cee="inserthtml";
var _cf1=this.document.selection.createRange();
if(!_cf1.htmlText){
return;
}
_cef=_cf1.htmlText.strike();
}else{
if(_cee=="inserthorizontalrule"){
_cee="inserthtml";
_cef="<hr>";
}
}
if(_cee=="inserthtml"){
var _cf1=this.document.selection.createRange();
if(this.document.selection.type.toUpperCase()=="CONTROL"){
for(var i=0;i<_cf1.length;i++){
_cf1.item(i).outerHTML=_cef;
}
}else{
_cf1.pasteHTML(_cef);
_cf1.select();
}
_cf0=true;
}else{
if(arguments.length==1){
_cf0=this.object.ExecCommand(this._activeX.command[_cee],this._activeX.ui.noprompt);
}else{
_cf0=this.object.ExecCommand(this._activeX.command[_cee],this._activeX.ui.noprompt,_cef);
}
}
}else{
if(_cee=="inserthtml"){
if(dojo.render.html.ie){
var _cf3=this.document.selection.createRange();
_cf3.pasteHTML(_cef);
_cf3.select();
return true;
}else{
return this.document.execCommand(_cee,false,_cef);
}
}else{
if((_cee=="unlink")&&(this.queryCommandEnabled("unlink"))&&(dojo.render.html.mozilla)){
var _cf4=this.window.getSelection();
var _cf5=_cf4.getRangeAt(0);
var _cf6=_cf5.startContainer;
var _cf7=_cf5.startOffset;
var _cf8=_cf5.endContainer;
var _cf9=_cf5.endOffset;
var a=dojo.withGlobal(this.window,"getAncestorElement",dojo.html.selection,["a"]);
dojo.withGlobal(this.window,"selectElement",dojo.html.selection,[a]);
_cf0=this.document.execCommand("unlink",false,null);
var _cf5=this.document.createRange();
_cf5.setStart(_cf6,_cf7);
_cf5.setEnd(_cf8,_cf9);
_cf4.removeAllRanges();
_cf4.addRange(_cf5);
return _cf0;
}else{
if((_cee=="hilitecolor")&&(dojo.render.html.mozilla)){
this.document.execCommand("useCSS",false,false);
_cf0=this.document.execCommand(_cee,false,_cef);
this.document.execCommand("useCSS",false,true);
}else{
if((dojo.render.html.ie)&&((_cee=="backcolor")||(_cee=="forecolor"))){
_cef=arguments.length>1?_cef:null;
_cf0=this.document.execCommand(_cee,false,_cef);
}else{
_cef=arguments.length>1?_cef:null;
if(_cef||_cee!="createlink"){
_cf0=this.document.execCommand(_cee,false,_cef);
}
}
}
}
}
}
this.onDisplayChanged();
return _cf0;
},queryCommandEnabled:function(_cfb){
_cfb=this._normalizeCommand(_cfb);
if(this.object){
switch(_cfb){
case "hilitecolor":
_cfb="setbackcolor";
break;
case "forecolor":
case "backcolor":
case "fontsize":
case "fontname":
_cfb="set"+_cfb;
break;
case "formatblock":
_cfb="setblockformat";
break;
case "strikethrough":
_cfb="bold";
break;
case "inserthorizontalrule":
return true;
}
if(typeof this._activeX.command[_cfb]=="undefined"){
return false;
}
var _cfc=this.object.QueryStatus(this._activeX.command[_cfb]);
return ((_cfc!=this._activeX.status.notsupported)&&(_cfc!=this._activeX.status.disabled));
}else{
if(dojo.render.html.mozilla){
if(_cfb=="unlink"){
return dojo.withGlobal(this.window,"hasAncestorElement",dojo.html.selection,["a"]);
}else{
if(_cfb=="inserttable"){
return true;
}
}
}
var elem=(dojo.render.html.ie)?this.document.selection.createRange():this.document;
return elem.queryCommandEnabled(_cfb);
}
},queryCommandState:function(_cfe){
_cfe=this._normalizeCommand(_cfe);
if(this.object){
if(_cfe=="forecolor"){
_cfe="setforecolor";
}else{
if(_cfe=="backcolor"){
_cfe="setbackcolor";
}else{
if(_cfe=="strikethrough"){
return dojo.withGlobal(this.window,"hasAncestorElement",dojo.html.selection,["strike"]);
}else{
if(_cfe=="inserthorizontalrule"){
return false;
}
}
}
}
if(typeof this._activeX.command[_cfe]=="undefined"){
return null;
}
var _cff=this.object.QueryStatus(this._activeX.command[_cfe]);
return ((_cff==this._activeX.status.latched)||(_cff==this._activeX.status.ninched));
}else{
return this.document.queryCommandState(_cfe);
}
},queryCommandValue:function(_d00){
_d00=this._normalizeCommand(_d00);
if(this.object){
switch(_d00){
case "forecolor":
case "backcolor":
case "fontsize":
case "fontname":
_d00="get"+_d00;
return this.object.execCommand(this._activeX.command[_d00],this._activeX.ui.noprompt);
case "formatblock":
var _d01=this.object.execCommand(this._activeX.command["getblockformat"],this._activeX.ui.noprompt);
if(_d01){
return this._local2NativeFormatNames[_d01];
}
}
}else{
if(dojo.render.html.ie&&_d00=="formatblock"){
return this._local2NativeFormatNames[this.document.queryCommandValue(_d00)]||this.document.queryCommandValue(_d00);
}
return this.document.queryCommandValue(_d00);
}
},placeCursorAtStart:function(){
this.focus();
if(dojo.render.html.moz&&this.editNode.firstChild&&this.editNode.firstChild.nodeType!=dojo.dom.TEXT_NODE){
dojo.withGlobal(this.window,"selectElementChildren",dojo.html.selection,[this.editNode.firstChild]);
}else{
dojo.withGlobal(this.window,"selectElementChildren",dojo.html.selection,[this.editNode]);
}
dojo.withGlobal(this.window,"collapse",dojo.html.selection,[true]);
},placeCursorAtEnd:function(){
this.focus();
if(dojo.render.html.moz&&this.editNode.lastChild&&this.editNode.lastChild.nodeType!=dojo.dom.TEXT_NODE){
dojo.withGlobal(this.window,"selectElementChildren",dojo.html.selection,[this.editNode.lastChild]);
}else{
dojo.withGlobal(this.window,"selectElementChildren",dojo.html.selection,[this.editNode]);
}
dojo.withGlobal(this.window,"collapse",dojo.html.selection,[false]);
},replaceEditorContent:function(html){
html=this._preFilterContent(html);
if(this.isClosed){
this.domNode.innerHTML=html;
}else{
if(this.window&&this.window.getSelection&&!dojo.render.html.moz){
this.editNode.innerHTML=html;
}else{
if((this.window&&this.window.getSelection)||(this.document&&this.document.selection)){
this.execCommand("selectall");
if(dojo.render.html.moz&&!html){
html="&nbsp;";
}
this.execCommand("inserthtml",html);
}
}
}
},_preFilterContent:function(html){
var ec=html;
dojo.lang.forEach(this.contentPreFilters,function(ef){
ec=ef(ec);
});
if(this.contentDomPreFilters.length>0){
var dom=dojo.doc().createElement("div");
dom.style.display="none";
dojo.body().appendChild(dom);
dom.innerHTML=ec;
dojo.lang.forEach(this.contentDomPreFilters,function(ef){
dom=ef(dom);
});
ec=dom.innerHTML;
dojo.body().removeChild(dom);
}
return ec;
},_postFilterContent:function(html){
var ec=html;
if(this.contentDomPostFilters.length>0){
var dom=this.document.createElement("div");
dom.innerHTML=ec;
dojo.lang.forEach(this.contentDomPostFilters,function(ef){
dom=ef(dom);
});
ec=dom.innerHTML;
}
dojo.lang.forEach(this.contentPostFilters,function(ef){
ec=ef(ec);
});
return ec;
},_lastHeight:0,_updateHeight:function(){
if(!this.isLoaded){
return;
}
if(this.height){
return;
}
var _d0d=dojo.html.getBorderBox(this.editNode).height;
if(!_d0d){
_d0d=dojo.html.getBorderBox(this.document.body).height;
}
if(_d0d==0){
dojo.debug("Can not figure out the height of the editing area!");
return;
}
this._lastHeight=_d0d;
this.editorObject.style.height=this._lastHeight+"px";
this.window.scrollTo(0,0);
},_saveContent:function(e){
var _d0f=dojo.doc().getElementById("dojo.widget.RichText.savedContent");
_d0f.value+=this._SEPARATOR+this.saveName+":"+this.getEditorContent();
},getEditorContent:function(){
var ec="";
try{
ec=(this._content.length>0)?this._content:this.editNode.innerHTML;
if(dojo.string.trim(ec)=="&nbsp;"){
ec="";
}
}
catch(e){
}
if(dojo.render.html.ie&&!this.object){
var re=new RegExp("(?:<p>&nbsp;</p>[\n\r]*)+$","i");
ec=ec.replace(re,"");
}
ec=this._postFilterContent(ec);
if(this.relativeImageUrls){
var _d12=dojo.global().location.protocol+"//"+dojo.global().location.host;
var _d13=dojo.global().location.pathname;
if(_d13.match(/\/$/)){
}else{
var _d14=_d13.split("/");
if(_d14.length){
_d14.pop();
}
_d13=_d14.join("/")+"/";
}
var _d15=new RegExp("(<img[^>]* src=[\"'])("+_d12+"("+_d13+")?)","ig");
ec=ec.replace(_d15,"$1");
}
if(!ec.replace(/^(?:\s|\xA0)+/g,"").replace(/(?:\s|\xA0)+$/g,"").length){
ec="";
}
return ec;
},close:function(save,_d17){
if(this.isClosed){
return false;
}
if(arguments.length==0){
save=true;
}
this._content=this._postFilterContent(this.editNode.innerHTML);
var _d18=(this.savedContent!=this._content);
if(this.interval){
clearInterval(this.interval);
}
if(dojo.render.html.ie&&!this.object){
dojo.event.browser.clean(this.editNode);
}
if(this.iframe){
delete this.iframe;
}
if(this.textarea){
with(this.textarea.style){
position="";
left=top="";
if(dojo.render.html.ie){
overflow=this.__overflow;
this.__overflow=null;
}
}
if(save){
this.textarea.value=this._content;
}else{
this.textarea.value=this.savedContent;
}
dojo.html.removeNode(this.domNode);
this.domNode=this.textarea;
}else{
if(save){
if(dojo.render.html.moz){
var nc=dojo.doc().createElement("span");
this.domNode.appendChild(nc);
nc.innerHTML=this.editNode.innerHTML;
}else{
this.domNode.innerHTML=this._content;
}
}else{
this.domNode.innerHTML=this.savedContent;
}
}
dojo.html.removeClass(this.domNode,"RichTextEditable");
this.isClosed=true;
this.isLoaded=false;
delete this.editNode;
if(this.window._frameElement){
this.window._frameElement=null;
}
this.window=null;
this.document=null;
this.object=null;
this.editingArea=null;
this.editorObject=null;
return _d18;
},destroyRendering:function(){
},destroy:function(){
this.destroyRendering();
if(!this.isClosed){
this.close(false);
}
dojo.widget.RichText.superclass.destroy.call(this);
},connect:function(_d1a,_d1b,_d1c){
dojo.event.connect(_d1a,_d1b,this,_d1c);
},disconnect:function(_d1d,_d1e,_d1f){
dojo.event.disconnect(_d1d,_d1e,this,_d1f);
},disconnectAllWithRoot:function(_d20){
dojo.deprecated("disconnectAllWithRoot","is deprecated. No need to disconnect manually","0.5");
},_fixContentForMoz:function(html){
html=html.replace(/<strong([ \>])/gi,"<b$1");
html=html.replace(/<\/strong>/gi,"</b>");
html=html.replace(/<em([ \>])/gi,"<i$1");
html=html.replace(/<\/em>/gi,"</i>");
return html;
}});
dojo.provide("dojo.lang.type");
dojo.lang.whatAmI=function(_d22){
dojo.deprecated("dojo.lang.whatAmI","use dojo.lang.getType instead","0.5");
return dojo.lang.getType(_d22);
};
dojo.lang.whatAmI.custom={};
dojo.lang.getType=function(_d23){
try{
if(dojo.lang.isArray(_d23)){
return "array";
}
if(dojo.lang.isFunction(_d23)){
return "function";
}
if(dojo.lang.isString(_d23)){
return "string";
}
if(dojo.lang.isNumber(_d23)){
return "number";
}
if(dojo.lang.isBoolean(_d23)){
return "boolean";
}
if(dojo.lang.isAlien(_d23)){
return "alien";
}
if(dojo.lang.isUndefined(_d23)){
return "undefined";
}
for(var name in dojo.lang.whatAmI.custom){
if(dojo.lang.whatAmI.custom[name](_d23)){
return name;
}
}
if(dojo.lang.isObject(_d23)){
return "object";
}
}
catch(e){
}
return "unknown";
};
dojo.lang.isNumeric=function(_d25){
return (!isNaN(_d25)&&isFinite(_d25)&&(_d25!=null)&&!dojo.lang.isBoolean(_d25)&&!dojo.lang.isArray(_d25)&&!/^\s*$/.test(_d25));
};
dojo.lang.isBuiltIn=function(_d26){
return (dojo.lang.isArray(_d26)||dojo.lang.isFunction(_d26)||dojo.lang.isString(_d26)||dojo.lang.isNumber(_d26)||dojo.lang.isBoolean(_d26)||(_d26==null)||(_d26 instanceof Error)||(typeof _d26=="error"));
};
dojo.lang.isPureObject=function(_d27){
return ((_d27!=null)&&dojo.lang.isObject(_d27)&&_d27.constructor==Object);
};
dojo.lang.isOfType=function(_d28,type,_d2a){
var _d2b=false;
if(_d2a){
_d2b=_d2a["optional"];
}
if(_d2b&&((_d28===null)||dojo.lang.isUndefined(_d28))){
return true;
}
if(dojo.lang.isArray(type)){
var _d2c=type;
for(var i in _d2c){
var _d2e=_d2c[i];
if(dojo.lang.isOfType(_d28,_d2e)){
return true;
}
}
return false;
}else{
if(dojo.lang.isString(type)){
type=type.toLowerCase();
}
switch(type){
case Array:
case "array":
return dojo.lang.isArray(_d28);
case Function:
case "function":
return dojo.lang.isFunction(_d28);
case String:
case "string":
return dojo.lang.isString(_d28);
case Number:
case "number":
return dojo.lang.isNumber(_d28);
case "numeric":
return dojo.lang.isNumeric(_d28);
case Boolean:
case "boolean":
return dojo.lang.isBoolean(_d28);
case Object:
case "object":
return dojo.lang.isObject(_d28);
case "pureobject":
return dojo.lang.isPureObject(_d28);
case "builtin":
return dojo.lang.isBuiltIn(_d28);
case "alien":
return dojo.lang.isAlien(_d28);
case "undefined":
return dojo.lang.isUndefined(_d28);
case null:
case "null":
return (_d28===null);
case "optional":
dojo.deprecated("dojo.lang.isOfType(value, [type, \"optional\"])","use dojo.lang.isOfType(value, type, {optional: true} ) instead","0.5");
return ((_d28===null)||dojo.lang.isUndefined(_d28));
default:
if(dojo.lang.isFunction(type)){
return (_d28 instanceof type);
}else{
dojo.raise("dojo.lang.isOfType() was passed an invalid type");
}
}
}
dojo.raise("If we get here, it means a bug was introduced above.");
};
dojo.lang.getObject=function(str){
var _d30=str.split("."),i=0,obj=dj_global;
do{
obj=obj[_d30[i++]];
}while(i<_d30.length&&obj);
return (obj!=dj_global)?obj:null;
};
dojo.lang.doesObjectExist=function(str){
var _d34=str.split("."),i=0,obj=dj_global;
do{
obj=obj[_d34[i++]];
}while(i<_d34.length&&obj);
return (obj&&obj!=dj_global);
};
dojo.provide("dojo.lang.assert");
dojo.lang.assert=function(_d37,_d38){
if(!_d37){
var _d39="An assert statement failed.\n"+"The method dojo.lang.assert() was called with a 'false' value.\n";
if(_d38){
_d39+="Here's the assert message:\n"+_d38+"\n";
}
throw new Error(_d39);
}
};
dojo.lang.assertType=function(_d3a,type,_d3c){
if(dojo.lang.isString(_d3c)){
dojo.deprecated("dojo.lang.assertType(value, type, \"message\")","use dojo.lang.assertType(value, type) instead","0.5");
}
if(!dojo.lang.isOfType(_d3a,type,_d3c)){
if(!dojo.lang.assertType._errorMessage){
dojo.lang.assertType._errorMessage="Type mismatch: dojo.lang.assertType() failed.";
}
dojo.lang.assert(false,dojo.lang.assertType._errorMessage);
}
};
dojo.lang.assertValidKeywords=function(_d3d,_d3e,_d3f){
var key;
if(!_d3f){
if(!dojo.lang.assertValidKeywords._errorMessage){
dojo.lang.assertValidKeywords._errorMessage="In dojo.lang.assertValidKeywords(), found invalid keyword:";
}
_d3f=dojo.lang.assertValidKeywords._errorMessage;
}
if(dojo.lang.isArray(_d3e)){
for(key in _d3d){
if(!dojo.lang.inArray(_d3e,key)){
dojo.lang.assert(false,_d3f+" "+key);
}
}
}else{
for(key in _d3d){
if(!(key in _d3e)){
dojo.lang.assert(false,_d3f+" "+key);
}
}
}
};
dojo.provide("dojo.AdapterRegistry");
dojo.AdapterRegistry=function(_d41){
this.pairs=[];
this.returnWrappers=_d41||false;
};
dojo.lang.extend(dojo.AdapterRegistry,{register:function(name,_d43,wrap,_d45,_d46){
var type=(_d46)?"unshift":"push";
this.pairs[type]([name,_d43,wrap,_d45]);
},match:function(){
for(var i=0;i<this.pairs.length;i++){
var pair=this.pairs[i];
if(pair[1].apply(this,arguments)){
if((pair[3])||(this.returnWrappers)){
return pair[2];
}else{
return pair[2].apply(this,arguments);
}
}
}
throw new Error("No match found");
},unregister:function(name){
for(var i=0;i<this.pairs.length;i++){
var pair=this.pairs[i];
if(pair[0]==name){
this.pairs.splice(i,1);
return true;
}
}
return false;
}});
dojo.provide("dojo.lang.repr");
dojo.lang.reprRegistry=new dojo.AdapterRegistry();
dojo.lang.registerRepr=function(name,_d4e,wrap,_d50){
dojo.lang.reprRegistry.register(name,_d4e,wrap,_d50);
};
dojo.lang.repr=function(obj){
if(typeof (obj)=="undefined"){
return "undefined";
}else{
if(obj===null){
return "null";
}
}
try{
if(typeof (obj["__repr__"])=="function"){
return obj["__repr__"]();
}else{
if((typeof (obj["repr"])=="function")&&(obj.repr!=arguments.callee)){
return obj["repr"]();
}
}
return dojo.lang.reprRegistry.match(obj);
}
catch(e){
if(typeof (obj.NAME)=="string"&&(obj.toString==Function.prototype.toString||obj.toString==Object.prototype.toString)){
return obj.NAME;
}
}
if(typeof (obj)=="function"){
obj=(obj+"").replace(/^\s+/,"");
var idx=obj.indexOf("{");
if(idx!=-1){
obj=obj.substr(0,idx)+"{...}";
}
}
return obj+"";
};
dojo.lang.reprArrayLike=function(arr){
try{
var na=dojo.lang.map(arr,dojo.lang.repr);
return "["+na.join(", ")+"]";
}
catch(e){
}
};
(function(){
var m=dojo.lang;
m.registerRepr("arrayLike",m.isArrayLike,m.reprArrayLike);
m.registerRepr("string",m.isString,m.reprString);
m.registerRepr("numbers",m.isNumber,m.reprNumber);
m.registerRepr("boolean",m.isBoolean,m.reprNumber);
})();
dojo.kwCompoundRequire({common:["dojo.lang.common","dojo.lang.assert","dojo.lang.array","dojo.lang.type","dojo.lang.func","dojo.lang.extras","dojo.lang.repr","dojo.lang.declare"]});
dojo.provide("dojo.lang.*");
dojo.provide("dojo.widget.ColorPalette");
dojo.widget.defineWidget("dojo.widget.ColorPalette",dojo.widget.HtmlWidget,{palette:"7x10",_palettes:{"7x10":[["fff","fcc","fc9","ff9","ffc","9f9","9ff","cff","ccf","fcf"],["ccc","f66","f96","ff6","ff3","6f9","3ff","6ff","99f","f9f"],["c0c0c0","f00","f90","fc6","ff0","3f3","6cc","3cf","66c","c6c"],["999","c00","f60","fc3","fc0","3c0","0cc","36f","63f","c3c"],["666","900","c60","c93","990","090","399","33f","60c","939"],["333","600","930","963","660","060","366","009","339","636"],["000","300","630","633","330","030","033","006","309","303"]],"3x4":[["ffffff","00ff00","008000","0000ff"],["c0c0c0","ffff00","ff00ff","000080"],["808080","ff0000","800080","000000"]]},buildRendering:function(){
this.domNode=document.createElement("table");
dojo.html.disableSelection(this.domNode);
dojo.event.connect(this.domNode,"onmousedown",function(e){
e.preventDefault();
});
with(this.domNode){
cellPadding="0";
cellSpacing="1";
border="1";
style.backgroundColor="white";
}
var _d57=this._palettes[this.palette];
for(var i=0;i<_d57.length;i++){
var tr=this.domNode.insertRow(-1);
for(var j=0;j<_d57[i].length;j++){
if(_d57[i][j].length==3){
_d57[i][j]=_d57[i][j].replace(/(.)(.)(.)/,"$1$1$2$2$3$3");
}
var td=tr.insertCell(-1);
with(td.style){
backgroundColor="#"+_d57[i][j];
border="1px solid gray";
width=height="15px";
fontSize="1px";
}
td.color="#"+_d57[i][j];
td.onmouseover=function(e){
this.style.borderColor="white";
};
td.onmouseout=function(e){
this.style.borderColor="gray";
};
dojo.event.connect(td,"onmousedown",this,"onClick");
td.innerHTML="&nbsp;";
}
}
},onClick:function(e){
this.onColorSelect(e.currentTarget.color);
e.currentTarget.style.borderColor="gray";
},onColorSelect:function(_d5f){
}});
dojo.provide("dojo.widget.Editor2Toolbar");
dojo.lang.declare("dojo.widget.HandlerManager",null,function(){
this._registeredHandlers=[];
},{registerHandler:function(obj,func){
if(arguments.length==2){
this._registeredHandlers.push(function(){
return obj[func].apply(obj,arguments);
});
}else{
this._registeredHandlers.push(obj);
}
},removeHandler:function(func){
for(var i=0;i<this._registeredHandlers.length;i++){
if(func===this._registeredHandlers[i]){
delete this._registeredHandlers[i];
return;
}
}
dojo.debug("HandlerManager handler "+func+" is not registered, can not remove.");
},destroy:function(){
for(var i=0;i<this._registeredHandlers.length;i++){
delete this._registeredHandlers[i];
}
}});
dojo.widget.Editor2ToolbarItemManager=new dojo.widget.HandlerManager;
dojo.lang.mixin(dojo.widget.Editor2ToolbarItemManager,{getToolbarItem:function(name){
var item;
name=name.toLowerCase();
for(var i=0;i<this._registeredHandlers.length;i++){
item=this._registeredHandlers[i](name);
if(item){
return item;
}
}
switch(name){
case "bold":
case "copy":
case "cut":
case "delete":
case "indent":
case "inserthorizontalrule":
case "insertorderedlist":
case "insertunorderedlist":
case "italic":
case "justifycenter":
case "justifyfull":
case "justifyleft":
case "justifyright":
case "outdent":
case "paste":
case "redo":
case "removeformat":
case "selectall":
case "strikethrough":
case "subscript":
case "superscript":
case "underline":
case "undo":
case "unlink":
case "createlink":
case "insertimage":
case "htmltoggle":
item=new dojo.widget.Editor2ToolbarButton(name);
break;
case "forecolor":
case "hilitecolor":
item=new dojo.widget.Editor2ToolbarColorPaletteButton(name);
break;
case "plainformatblock":
item=new dojo.widget.Editor2ToolbarFormatBlockPlainSelect("formatblock");
break;
case "formatblock":
item=new dojo.widget.Editor2ToolbarFormatBlockSelect("formatblock");
break;
case "fontsize":
item=new dojo.widget.Editor2ToolbarFontSizeSelect("fontsize");
break;
case "fontname":
item=new dojo.widget.Editor2ToolbarFontNameSelect("fontname");
break;
case "inserttable":
case "insertcell":
case "insertcol":
case "insertrow":
case "deletecells":
case "deletecols":
case "deleterows":
case "mergecells":
case "splitcell":
dojo.debug(name+" is implemented in dojo.widget.Editor2Plugin.TableOperation, please require it first.");
break;
case "inserthtml":
case "blockdirltr":
case "blockdirrtl":
case "dirltr":
case "dirrtl":
case "inlinedirltr":
case "inlinedirrtl":
dojo.debug("Not yet implemented toolbar item: "+name);
break;
default:
dojo.debug("dojo.widget.Editor2ToolbarItemManager.getToolbarItem: Unknown toolbar item: "+name);
}
return item;
}});
dojo.addOnUnload(dojo.widget.Editor2ToolbarItemManager,"destroy");
dojo.declare("dojo.widget.Editor2ToolbarButton",null,function(name){
this._name=name;
},{create:function(node,_d6a,_d6b){
this._domNode=node;
var cmd=_d6a.parent.getCommand(this._name);
if(cmd){
this._domNode.title=cmd.getText();
}
this.disableSelection(this._domNode);
this._parentToolbar=_d6a;
dojo.event.connect(this._domNode,"onclick",this,"onClick");
if(!_d6b){
dojo.event.connect(this._domNode,"onmouseover",this,"onMouseOver");
dojo.event.connect(this._domNode,"onmouseout",this,"onMouseOut");
}
},disableSelection:function(_d6d){
dojo.html.disableSelection(_d6d);
var _d6e=_d6d.all||_d6d.getElementsByTagName("*");
for(var x=0;x<_d6e.length;x++){
dojo.html.disableSelection(_d6e[x]);
}
},onMouseOver:function(){
var _d70=dojo.widget.Editor2Manager.getCurrentInstance();
if(_d70){
var _d71=_d70.getCommand(this._name);
if(_d71&&_d71.getState()!=dojo.widget.Editor2Manager.commandState.Disabled){
this.highlightToolbarItem();
}
}
},onMouseOut:function(){
this.unhighlightToolbarItem();
},destroy:function(){
this._domNode=null;
this._parentToolbar=null;
},onClick:function(e){
if(this._domNode&&!this._domNode.disabled&&this._parentToolbar.checkAvailability()){
e.preventDefault();
e.stopPropagation();
var _d73=dojo.widget.Editor2Manager.getCurrentInstance();
if(_d73){
var _d74=_d73.getCommand(this._name);
if(_d74){
_d74.execute();
}
}
}
},refreshState:function(){
var _d75=dojo.widget.Editor2Manager.getCurrentInstance();
var em=dojo.widget.Editor2Manager;
if(_d75){
var _d77=_d75.getCommand(this._name);
if(_d77){
var _d78=_d77.getState();
if(_d78!=this._lastState){
switch(_d78){
case em.commandState.Latched:
this.latchToolbarItem();
break;
case em.commandState.Enabled:
this.enableToolbarItem();
break;
case em.commandState.Disabled:
default:
this.disableToolbarItem();
}
this._lastState=_d78;
}
}
}
return em.commandState.Enabled;
},latchToolbarItem:function(){
this._domNode.disabled=false;
this.removeToolbarItemStyle(this._domNode);
dojo.html.addClass(this._domNode,this._parentToolbar.ToolbarLatchedItemStyle);
},enableToolbarItem:function(){
this._domNode.disabled=false;
this.removeToolbarItemStyle(this._domNode);
dojo.html.addClass(this._domNode,this._parentToolbar.ToolbarEnabledItemStyle);
},disableToolbarItem:function(){
this._domNode.disabled=true;
this.removeToolbarItemStyle(this._domNode);
dojo.html.addClass(this._domNode,this._parentToolbar.ToolbarDisabledItemStyle);
},highlightToolbarItem:function(){
dojo.html.addClass(this._domNode,this._parentToolbar.ToolbarHighlightedItemStyle);
},unhighlightToolbarItem:function(){
dojo.html.removeClass(this._domNode,this._parentToolbar.ToolbarHighlightedItemStyle);
},removeToolbarItemStyle:function(){
dojo.html.removeClass(this._domNode,this._parentToolbar.ToolbarEnabledItemStyle);
dojo.html.removeClass(this._domNode,this._parentToolbar.ToolbarLatchedItemStyle);
dojo.html.removeClass(this._domNode,this._parentToolbar.ToolbarDisabledItemStyle);
this.unhighlightToolbarItem();
}});
dojo.declare("dojo.widget.Editor2ToolbarDropDownButton",dojo.widget.Editor2ToolbarButton,{onClick:function(){
if(this._domNode&&!this._domNode.disabled&&this._parentToolbar.checkAvailability()){
if(!this._dropdown){
this._dropdown=dojo.widget.createWidget("PopupContainer",{});
this._domNode.appendChild(this._dropdown.domNode);
}
if(this._dropdown.isShowingNow){
this._dropdown.close();
}else{
this.onDropDownShown();
this._dropdown.open(this._domNode,null,this._domNode);
}
}
},destroy:function(){
this.onDropDownDestroy();
if(this._dropdown){
this._dropdown.destroy();
}
dojo.widget.Editor2ToolbarDropDownButton.superclass.destroy.call(this);
},onDropDownShown:function(){
},onDropDownDestroy:function(){
}});
dojo.declare("dojo.widget.Editor2ToolbarColorPaletteButton",dojo.widget.Editor2ToolbarDropDownButton,{onDropDownShown:function(){
if(!this._colorpalette){
this._colorpalette=dojo.widget.createWidget("ColorPalette",{});
this._dropdown.addChild(this._colorpalette);
this.disableSelection(this._dropdown.domNode);
this.disableSelection(this._colorpalette.domNode);
dojo.event.connect(this._colorpalette,"onColorSelect",this,"setColor");
dojo.event.connect(this._dropdown,"open",this,"latchToolbarItem");
dojo.event.connect(this._dropdown,"close",this,"enableToolbarItem");
}
},setColor:function(_d79){
this._dropdown.close();
var _d7a=dojo.widget.Editor2Manager.getCurrentInstance();
if(_d7a){
var _d7b=_d7a.getCommand(this._name);
if(_d7b){
_d7b.execute(_d79);
}
}
}});
dojo.declare("dojo.widget.Editor2ToolbarFormatBlockPlainSelect",dojo.widget.Editor2ToolbarButton,{create:function(node,_d7d){
this._domNode=node;
this._parentToolbar=_d7d;
this._domNode=node;
this.disableSelection(this._domNode);
dojo.event.connect(this._domNode,"onchange",this,"onChange");
},destroy:function(){
this._domNode=null;
},onChange:function(){
if(this._parentToolbar.checkAvailability()){
var sv=this._domNode.value.toLowerCase();
var _d7f=dojo.widget.Editor2Manager.getCurrentInstance();
if(_d7f){
var _d80=_d7f.getCommand(this._name);
if(_d80){
_d80.execute(sv);
}
}
}
},refreshState:function(){
if(this._domNode){
dojo.widget.Editor2ToolbarFormatBlockPlainSelect.superclass.refreshState.call(this);
var _d81=dojo.widget.Editor2Manager.getCurrentInstance();
if(_d81){
var _d82=_d81.getCommand(this._name);
if(_d82){
var _d83=_d82.getValue();
if(!_d83){
_d83="";
}
dojo.lang.forEach(this._domNode.options,function(item){
if(item.value.toLowerCase()==_d83.toLowerCase()){
item.selected=true;
}
});
}
}
}
}});
dojo.declare("dojo.widget.Editor2ToolbarComboItem",dojo.widget.Editor2ToolbarDropDownButton,{href:null,create:function(node,_d86){
dojo.widget.Editor2ToolbarComboItem.superclass.create.apply(this,arguments);
if(!this._contentPane){
this._contentPane=dojo.widget.createWidget("ContentPane",{preload:"true"});
this._contentPane.addOnLoad(this,"setup");
this._contentPane.setUrl(this.href);
}
},onMouseOver:function(e){
if(this._lastState!=dojo.widget.Editor2Manager.commandState.Disabled){
dojo.html.addClass(e.currentTarget,this._parentToolbar.ToolbarHighlightedSelectStyle);
}
},onMouseOut:function(e){
dojo.html.removeClass(e.currentTarget,this._parentToolbar.ToolbarHighlightedSelectStyle);
},onDropDownShown:function(){
if(!this._dropdown.__addedContentPage){
this._dropdown.addChild(this._contentPane);
this._dropdown.__addedContentPage=true;
}
},setup:function(){
},onChange:function(e){
if(this._parentToolbar.checkAvailability()){
var name=e.currentTarget.getAttribute("dropDownItemName");
var _d8b=dojo.widget.Editor2Manager.getCurrentInstance();
if(_d8b){
var _d8c=_d8b.getCommand(this._name);
if(_d8c){
_d8c.execute(name);
}
}
}
this._dropdown.close();
},onMouseOverItem:function(e){
dojo.html.addClass(e.currentTarget,this._parentToolbar.ToolbarHighlightedSelectItemStyle);
},onMouseOutItem:function(e){
dojo.html.removeClass(e.currentTarget,this._parentToolbar.ToolbarHighlightedSelectItemStyle);
}});
dojo.declare("dojo.widget.Editor2ToolbarFormatBlockSelect",dojo.widget.Editor2ToolbarComboItem,{href:dojo.uri.moduleUri("dojo.widget","templates/Editor2/EditorToolbar_FormatBlock.html"),setup:function(){
dojo.widget.Editor2ToolbarFormatBlockSelect.superclass.setup.call(this);
var _d8f=this._contentPane.domNode.all||this._contentPane.domNode.getElementsByTagName("*");
this._blockNames={};
this._blockDisplayNames={};
for(var x=0;x<_d8f.length;x++){
var node=_d8f[x];
dojo.html.disableSelection(node);
var name=node.getAttribute("dropDownItemName");
if(name){
this._blockNames[name]=node;
var _d93=node.getElementsByTagName(name);
this._blockDisplayNames[name]=_d93[_d93.length-1].innerHTML;
}
}
for(var name in this._blockNames){
dojo.event.connect(this._blockNames[name],"onclick",this,"onChange");
dojo.event.connect(this._blockNames[name],"onmouseover",this,"onMouseOverItem");
dojo.event.connect(this._blockNames[name],"onmouseout",this,"onMouseOutItem");
}
},onDropDownDestroy:function(){
if(this._blockNames){
for(var name in this._blockNames){
delete this._blockNames[name];
delete this._blockDisplayNames[name];
}
}
},refreshState:function(){
dojo.widget.Editor2ToolbarFormatBlockSelect.superclass.refreshState.call(this);
if(this._lastState!=dojo.widget.Editor2Manager.commandState.Disabled){
var _d95=dojo.widget.Editor2Manager.getCurrentInstance();
if(_d95){
var _d96=_d95.getCommand(this._name);
if(_d96){
var _d97=_d96.getValue();
if(_d97==this._lastSelectedFormat&&this._blockDisplayNames){
return this._lastState;
}
this._lastSelectedFormat=_d97;
var _d98=this._domNode.getElementsByTagName("label")[0];
var _d99=false;
if(this._blockDisplayNames){
for(var name in this._blockDisplayNames){
if(name==_d97){
_d98.innerHTML=this._blockDisplayNames[name];
_d99=true;
break;
}
}
if(!_d99){
_d98.innerHTML="&nbsp;";
}
}
}
}
}
return this._lastState;
}});
dojo.declare("dojo.widget.Editor2ToolbarFontSizeSelect",dojo.widget.Editor2ToolbarComboItem,{href:dojo.uri.moduleUri("dojo.widget","templates/Editor2/EditorToolbar_FontSize.html"),setup:function(){
dojo.widget.Editor2ToolbarFormatBlockSelect.superclass.setup.call(this);
var _d9b=this._contentPane.domNode.all||this._contentPane.domNode.getElementsByTagName("*");
this._fontsizes={};
this._fontSizeDisplayNames={};
for(var x=0;x<_d9b.length;x++){
var node=_d9b[x];
dojo.html.disableSelection(node);
var name=node.getAttribute("dropDownItemName");
if(name){
this._fontsizes[name]=node;
this._fontSizeDisplayNames[name]=node.getElementsByTagName("font")[0].innerHTML;
}
}
for(var name in this._fontsizes){
dojo.event.connect(this._fontsizes[name],"onclick",this,"onChange");
dojo.event.connect(this._fontsizes[name],"onmouseover",this,"onMouseOverItem");
dojo.event.connect(this._fontsizes[name],"onmouseout",this,"onMouseOutItem");
}
},onDropDownDestroy:function(){
if(this._fontsizes){
for(var name in this._fontsizes){
delete this._fontsizes[name];
delete this._fontSizeDisplayNames[name];
}
}
},refreshState:function(){
dojo.widget.Editor2ToolbarFormatBlockSelect.superclass.refreshState.call(this);
if(this._lastState!=dojo.widget.Editor2Manager.commandState.Disabled){
var _da0=dojo.widget.Editor2Manager.getCurrentInstance();
if(_da0){
var _da1=_da0.getCommand(this._name);
if(_da1){
var size=_da1.getValue();
if(size==this._lastSelectedSize&&this._fontSizeDisplayNames){
return this._lastState;
}
this._lastSelectedSize=size;
var _da3=this._domNode.getElementsByTagName("label")[0];
var _da4=false;
if(this._fontSizeDisplayNames){
for(var name in this._fontSizeDisplayNames){
if(name==size){
_da3.innerHTML=this._fontSizeDisplayNames[name];
_da4=true;
break;
}
}
if(!_da4){
_da3.innerHTML="&nbsp;";
}
}
}
}
}
return this._lastState;
}});
dojo.declare("dojo.widget.Editor2ToolbarFontNameSelect",dojo.widget.Editor2ToolbarFontSizeSelect,{href:dojo.uri.moduleUri("dojo.widget","templates/Editor2/EditorToolbar_FontName.html")});
dojo.widget.defineWidget("dojo.widget.Editor2Toolbar",dojo.widget.HtmlWidget,function(){
dojo.event.connect(this,"fillInTemplate",dojo.lang.hitch(this,function(){
if(dojo.render.html.ie){
this.domNode.style.zoom=1;
}
}));
},{templateString:"<div dojoAttachPoint=\"domNode\" class=\"EditorToolbarDomNode\" unselectable=\"on\">\n\t<table cellpadding=\"3\" cellspacing=\"0\" border=\"0\">\n\t\t<!--\n\t\t\tour toolbar should look something like:\n\n\t\t\t+=======+=======+=======+=============================================+\n\t\t\t| w   w | style | copy  | bo | it | un | le | ce | ri |\n\t\t\t| w w w | style |=======|==============|==============|\n\t\t\t|  w w  | style | paste |  undo | redo | change style |\n\t\t\t+=======+=======+=======+=============================================+\n\t\t-->\n\t\t<tbody>\n\t\t\t<tr valign=\"top\">\n\t\t\t\t<td rowspan=\"2\">\n\t\t\t\t\t<div class=\"bigIcon\" dojoAttachPoint=\"wikiWordButton\"\n\t\t\t\t\t\tdojoOnClick=\"wikiWordClick; buttonClick;\">\n\t\t\t\t\t\t<span style=\"font-size: 30px; margin-left: 5px;\">\n\t\t\t\t\t\t\tW\n\t\t\t\t\t\t</span>\n\t\t\t\t\t</div>\n\t\t\t\t</td>\n\t\t\t\t<td rowspan=\"2\">\n\t\t\t\t\t<div class=\"bigIcon\" dojoAttachPoint=\"styleDropdownButton\"\n\t\t\t\t\t\tdojoOnClick=\"styleDropdownClick; buttonClick;\">\n\t\t\t\t\t\t<span unselectable=\"on\"\n\t\t\t\t\t\t\tstyle=\"font-size: 30px; margin-left: 5px;\">\n\t\t\t\t\t\t\tS\n\t\t\t\t\t\t</span>\n\t\t\t\t\t</div>\n\t\t\t\t\t<div class=\"StyleDropdownContainer\" style=\"display: none;\"\n\t\t\t\t\t\tdojoAttachPoint=\"styleDropdownContainer\">\n\t\t\t\t\t\t<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"\n\t\t\t\t\t\t\theight=\"100%\" width=\"100%\">\n\t\t\t\t\t\t\t<tr valign=\"top\">\n\t\t\t\t\t\t\t\t<td rowspan=\"2\">\n\t\t\t\t\t\t\t\t\t<div style=\"height: 245px; overflow: auto;\">\n\t\t\t\t\t\t\t\t\t\t<div class=\"headingContainer\"\n\t\t\t\t\t\t\t\t\t\t\tunselectable=\"on\"\n\t\t\t\t\t\t\t\t\t\t\tdojoOnClick=\"normalTextClick\">normal</div>\n\t\t\t\t\t\t\t\t\t\t<h1 class=\"headingContainer\"\n\t\t\t\t\t\t\t\t\t\t\tunselectable=\"on\"\n\t\t\t\t\t\t\t\t\t\t\tdojoOnClick=\"h1TextClick\">Heading 1</h1>\n\t\t\t\t\t\t\t\t\t\t<h2 class=\"headingContainer\"\n\t\t\t\t\t\t\t\t\t\t\tunselectable=\"on\"\n\t\t\t\t\t\t\t\t\t\t\tdojoOnClick=\"h2TextClick\">Heading 2</h2>\n\t\t\t\t\t\t\t\t\t\t<h3 class=\"headingContainer\"\n\t\t\t\t\t\t\t\t\t\t\tunselectable=\"on\"\n\t\t\t\t\t\t\t\t\t\t\tdojoOnClick=\"h3TextClick\">Heading 3</h3>\n\t\t\t\t\t\t\t\t\t\t<h4 class=\"headingContainer\"\n\t\t\t\t\t\t\t\t\t\t\tunselectable=\"on\"\n\t\t\t\t\t\t\t\t\t\t\tdojoOnClick=\"h4TextClick\">Heading 4</h4>\n\t\t\t\t\t\t\t\t\t\t<div class=\"headingContainer\"\n\t\t\t\t\t\t\t\t\t\t\tunselectable=\"on\"\n\t\t\t\t\t\t\t\t\t\t\tdojoOnClick=\"blahTextClick\">blah</div>\n\t\t\t\t\t\t\t\t\t\t<div class=\"headingContainer\"\n\t\t\t\t\t\t\t\t\t\t\tunselectable=\"on\"\n\t\t\t\t\t\t\t\t\t\t\tdojoOnClick=\"blahTextClick\">blah</div>\n\t\t\t\t\t\t\t\t\t\t<div class=\"headingContainer\"\n\t\t\t\t\t\t\t\t\t\t\tunselectable=\"on\"\n\t\t\t\t\t\t\t\t\t\t\tdojoOnClick=\"blahTextClick\">blah</div>\n\t\t\t\t\t\t\t\t\t\t<div class=\"headingContainer\">blah</div>\n\t\t\t\t\t\t\t\t\t\t<div class=\"headingContainer\">blah</div>\n\t\t\t\t\t\t\t\t\t\t<div class=\"headingContainer\">blah</div>\n\t\t\t\t\t\t\t\t\t\t<div class=\"headingContainer\">blah</div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t<!--\n\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t<span class=\"iconContainer\" dojoOnClick=\"buttonClick;\">\n\t\t\t\t\t\t\t\t\t\t<span class=\"icon justifyleft\" \n\t\t\t\t\t\t\t\t\t\t\tstyle=\"float: left;\">&nbsp;</span>\n\t\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t<span class=\"iconContainer\" dojoOnClick=\"buttonClick;\">\n\t\t\t\t\t\t\t\t\t\t<span class=\"icon justifycenter\" \n\t\t\t\t\t\t\t\t\t\t\tstyle=\"float: left;\">&nbsp;</span>\n\t\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t<span class=\"iconContainer\" dojoOnClick=\"buttonClick;\">\n\t\t\t\t\t\t\t\t\t\t<span class=\"icon justifyright\" \n\t\t\t\t\t\t\t\t\t\t\tstyle=\"float: left;\">&nbsp;</span>\n\t\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t<span class=\"iconContainer\" dojoOnClick=\"buttonClick;\">\n\t\t\t\t\t\t\t\t\t\t<span class=\"icon justifyfull\" \n\t\t\t\t\t\t\t\t\t\t\tstyle=\"float: left;\">&nbsp;</span>\n\t\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t-->\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t\t<tr valign=\"top\">\n\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\tthud\n\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t</table>\n\t\t\t\t\t</div>\n\t\t\t\t</td>\n\t\t\t\t<td>\n\t\t\t\t\t<!-- copy -->\n\t\t\t\t\t<span class=\"iconContainer\" dojoAttachPoint=\"copyButton\"\n\t\t\t\t\t\tunselectable=\"on\"\n\t\t\t\t\t\tdojoOnClick=\"copyClick; buttonClick;\">\n\t\t\t\t\t\t<span class=\"icon copy\" \n\t\t\t\t\t\t\tunselectable=\"on\"\n\t\t\t\t\t\t\tstyle=\"float: left;\">&nbsp;</span> copy\n\t\t\t\t\t</span>\n\t\t\t\t\t<!-- \"droppable\" options -->\n\t\t\t\t\t<span class=\"iconContainer\" dojoAttachPoint=\"boldButton\"\n\t\t\t\t\t\tunselectable=\"on\"\n\t\t\t\t\t\tdojoOnClick=\"boldClick; buttonClick;\">\n\t\t\t\t\t\t<span class=\"icon bold\" unselectable=\"on\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t\t<span class=\"iconContainer\" dojoAttachPoint=\"italicButton\"\n\t\t\t\t\t\tdojoOnClick=\"italicClick; buttonClick;\">\n\t\t\t\t\t\t<span class=\"icon italic\" unselectable=\"on\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t\t<span class=\"iconContainer\" dojoAttachPoint=\"underlineButton\"\n\t\t\t\t\t\tdojoOnClick=\"underlineClick; buttonClick;\">\n\t\t\t\t\t\t<span class=\"icon underline\" unselectable=\"on\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t\t<span class=\"iconContainer\" dojoAttachPoint=\"leftButton\"\n\t\t\t\t\t\tdojoOnClick=\"leftClick; buttonClick;\">\n\t\t\t\t\t\t<span class=\"icon justifyleft\" unselectable=\"on\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t\t<span class=\"iconContainer\" dojoAttachPoint=\"fullButton\"\n\t\t\t\t\t\tdojoOnClick=\"fullClick; buttonClick;\">\n\t\t\t\t\t\t<span class=\"icon justifyfull\" unselectable=\"on\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t\t<span class=\"iconContainer\" dojoAttachPoint=\"rightButton\"\n\t\t\t\t\t\tdojoOnClick=\"rightClick; buttonClick;\">\n\t\t\t\t\t\t<span class=\"icon justifyright\" unselectable=\"on\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t<tr>\n\t\t\t\t<td>\n\t\t\t\t\t<!-- paste -->\n\t\t\t\t\t<span class=\"iconContainer\" dojoAttachPoint=\"pasteButton\"\n\t\t\t\t\t\tdojoOnClick=\"pasteClick; buttonClick;\" unselectable=\"on\">\n\t\t\t\t\t\t<span class=\"icon paste\" style=\"float: left;\" unselectable=\"on\">&nbsp;</span> paste\n\t\t\t\t\t</span>\n\t\t\t\t\t<!-- \"droppable\" options -->\n\t\t\t\t\t<span class=\"iconContainer\" dojoAttachPoint=\"undoButton\"\n\t\t\t\t\t\tdojoOnClick=\"undoClick; buttonClick;\" unselectable=\"on\">\n\t\t\t\t\t\t<span class=\"icon undo\" style=\"float: left;\" unselectable=\"on\">&nbsp;</span> undo\n\t\t\t\t\t</span>\n\t\t\t\t\t<span class=\"iconContainer\" dojoAttachPoint=\"redoButton\"\n\t\t\t\t\t\tdojoOnClick=\"redoClick; buttonClick;\" unselectable=\"on\">\n\t\t\t\t\t\t<span class=\"icon redo\" style=\"float: left;\" unselectable=\"on\">&nbsp;</span> redo\n\t\t\t\t\t</span>\n\t\t\t\t</td>\t\n\t\t\t</tr>\n\t\t</tbody>\n\t</table>\n</div>\n",templateCssString:".StyleDropdownContainer {\n\tposition: absolute;\n\tz-index: 1000;\n\toverflow: auto;\n\tcursor: default;\n\twidth: 250px;\n\theight: 250px;\n\tbackground-color: white;\n\tborder: 1px solid black;\n}\n\n.ColorDropdownContainer {\n\tposition: absolute;\n\tz-index: 1000;\n\toverflow: auto;\n\tcursor: default;\n\twidth: 250px;\n\theight: 150px;\n\tbackground-color: white;\n\tborder: 1px solid black;\n}\n\n.EditorToolbarDomNode {\n\tbackground-image: url(buttons/bg-fade.png);\n\tbackground-repeat: repeat-x;\n\tbackground-position: 0px -50px;\n}\n\n.EditorToolbarSmallBg {\n\tbackground-image: url(images/toolbar-bg.gif);\n\tbackground-repeat: repeat-x;\n\tbackground-position: 0px 0px;\n}\n\n/*\nbody {\n\tbackground:url(images/blank.gif) fixed;\n}*/\n\n.IEFixedToolbar {\n\tposition:absolute;\n\t/* top:0; */\n\ttop: expression(eval((document.documentElement||document.body).scrollTop));\n}\n\ndiv.bigIcon {\n\twidth: 40px;\n\theight: 40px; \n\t/* background-color: white; */\n\t/* border: 1px solid #a6a7a3; */\n\tfont-family: Verdana, Trebuchet, Tahoma, Arial;\n}\n\n.iconContainer {\n\tfont-family: Verdana, Trebuchet, Tahoma, Arial;\n\tfont-size: 13px;\n\tfloat: left;\n\theight: 18px;\n\tdisplay: block;\n\t/* background-color: white; */\n\tcursor: pointer;\n\tpadding: 1px 4px 1px 1px; /* almost the same as a transparent border */\n\tborder: 0px;\n}\n\n.dojoE2TBIcon {\n\tdisplay: block;\n\ttext-align: center;\n\tmin-width: 18px;\n\twidth: 18px;\n\theight: 18px;\n\t/* background-color: #a6a7a3; */\n\tbackground-repeat: no-repeat;\n\tbackground-image: url(buttons/aggregate.gif);\n}\n\n\n.dojoE2TBIcon[class~=dojoE2TBIcon] {\n}\n\n.ToolbarButtonLatched {\n    border: #316ac5 1px solid; !important;\n    padding: 0px 3px 0px 0px; !important; /* make room for border */\n    background-color: #c1d2ee;\n}\n\n.ToolbarButtonHighlighted {\n    border: #316ac5 1px solid; !important;\n    padding: 0px 3px 0px 0px; !important; /* make room for border */\n    background-color: #dff1ff;\n}\n\n.ToolbarButtonDisabled{\n    filter: gray() alpha(opacity=30); /* IE */\n    opacity: 0.30; /* Safari, Opera and Mozilla */\n}\n\n.headingContainer {\n\twidth: 150px;\n\theight: 30px;\n\tmargin: 0px;\n\t/* padding-left: 5px; */\n\toverflow: hidden;\n\tline-height: 25px;\n\tborder-bottom: 1px solid black;\n\tborder-top: 1px solid white;\n}\n\n.EditorToolbarDomNode select {\n\tfont-size: 14px;\n}\n \n.dojoE2TBIcon_Sep { width: 5px; min-width: 5px; max-width: 5px; background-position: 0px 0px}\n.dojoE2TBIcon_Backcolor { background-position: -18px 0px}\n.dojoE2TBIcon_Bold { background-position: -36px 0px}\n.dojoE2TBIcon_Cancel { background-position: -54px 0px}\n.dojoE2TBIcon_Copy { background-position: -72px 0px}\n.dojoE2TBIcon_Link { background-position: -90px 0px}\n.dojoE2TBIcon_Cut { background-position: -108px 0px}\n.dojoE2TBIcon_Delete { background-position: -126px 0px}\n.dojoE2TBIcon_TextColor { background-position: -144px 0px}\n.dojoE2TBIcon_BackgroundColor { background-position: -162px 0px}\n.dojoE2TBIcon_Indent { background-position: -180px 0px}\n.dojoE2TBIcon_HorizontalLine { background-position: -198px 0px}\n.dojoE2TBIcon_Image { background-position: -216px 0px}\n.dojoE2TBIcon_NumberedList { background-position: -234px 0px}\n.dojoE2TBIcon_Table { background-position: -252px 0px}\n.dojoE2TBIcon_BulletedList { background-position: -270px 0px}\n.dojoE2TBIcon_Italic { background-position: -288px 0px}\n.dojoE2TBIcon_CenterJustify { background-position: -306px 0px}\n.dojoE2TBIcon_BlockJustify { background-position: -324px 0px}\n.dojoE2TBIcon_LeftJustify { background-position: -342px 0px}\n.dojoE2TBIcon_RightJustify { background-position: -360px 0px}\n.dojoE2TBIcon_left_to_right { background-position: -378px 0px}\n.dojoE2TBIcon_list_bullet_indent { background-position: -396px 0px}\n.dojoE2TBIcon_list_bullet_outdent { background-position: -414px 0px}\n.dojoE2TBIcon_list_num_indent { background-position: -432px 0px}\n.dojoE2TBIcon_list_num_outdent { background-position: -450px 0px}\n.dojoE2TBIcon_Outdent { background-position: -468px 0px}\n.dojoE2TBIcon_Paste { background-position: -486px 0px}\n.dojoE2TBIcon_Redo { background-position: -504px 0px}\ndojoE2TBIcon_RemoveFormat { background-position: -522px 0px}\n.dojoE2TBIcon_right_to_left { background-position: -540px 0px}\n.dojoE2TBIcon_Save { background-position: -558px 0px}\n.dojoE2TBIcon_Space { background-position: -576px 0px}\n.dojoE2TBIcon_StrikeThrough { background-position: -594px 0px}\n.dojoE2TBIcon_Subscript { background-position: -612px 0px}\n.dojoE2TBIcon_Superscript { background-position: -630px 0px}\n.dojoE2TBIcon_Underline { background-position: -648px 0px}\n.dojoE2TBIcon_Undo { background-position: -666px 0px}\n.dojoE2TBIcon_WikiWord { background-position: -684px 0px}\n\n",templateCssPath:dojo.uri.moduleUri("dojo.widget","templates/EditorToolbar.css"),ToolbarLatchedItemStyle:"ToolbarButtonLatched",ToolbarEnabledItemStyle:"ToolbarButtonEnabled",ToolbarDisabledItemStyle:"ToolbarButtonDisabled",ToolbarHighlightedItemStyle:"ToolbarButtonHighlighted",ToolbarHighlightedSelectStyle:"ToolbarSelectHighlighted",ToolbarHighlightedSelectItemStyle:"ToolbarSelectHighlightedItem",postCreate:function(){
var _da6=dojo.html.getElementsByClass("dojoEditorToolbarItem",this.domNode);
this.items={};
for(var x=0;x<_da6.length;x++){
var node=_da6[x];
var _da9=node.getAttribute("dojoETItemName");
if(_da9){
var item=dojo.widget.Editor2ToolbarItemManager.getToolbarItem(_da9);
if(item){
item.create(node,this);
this.items[_da9.toLowerCase()]=item;
}else{
node.style.display="none";
}
}
}
},update:function(){
for(var cmd in this.items){
this.items[cmd].refreshState();
}
},shareGroup:"",checkAvailability:function(){
if(!this.shareGroup){
this.parent.focus();
return true;
}
var _dac=dojo.widget.Editor2Manager.getCurrentInstance();
if(this.shareGroup==_dac.toolbarGroup){
return true;
}
return false;
},destroy:function(){
for(var it in this.items){
this.items[it].destroy();
delete this.items[it];
}
dojo.widget.Editor2Toolbar.superclass.destroy.call(this);
}});
dojo.provide("dojo.uri.cache");
dojo.uri.cache={_cache:{},set:function(uri,_daf){
this._cache[uri.toString()]=_daf;
return uri;
},remove:function(uri){
delete this._cache[uri.toString()];
},get:function(uri){
var key=uri.toString();
var _db3=this._cache[key];
if(!_db3){
_db3=dojo.hostenv.getText(key);
if(_db3){
this._cache[key]=_db3;
}
}
return _db3;
},allow:function(uri){
return uri;
}};
dojo.provide("dojo.lfx.shadow");
dojo.lfx.shadow=function(node){
this.shadowPng=dojo.uri.moduleUri("dojo.html","images/shadow");
this.shadowThickness=8;
this.shadowOffset=15;
this.init(node);
};
dojo.extend(dojo.lfx.shadow,{init:function(node){
this.node=node;
this.pieces={};
var x1=-1*this.shadowThickness;
var y0=this.shadowOffset;
var y1=this.shadowOffset+this.shadowThickness;
this._makePiece("tl","top",y0,"left",x1);
this._makePiece("l","top",y1,"left",x1,"scale");
this._makePiece("tr","top",y0,"left",0);
this._makePiece("r","top",y1,"left",0,"scale");
this._makePiece("bl","top",0,"left",x1);
this._makePiece("b","top",0,"left",0,"crop");
this._makePiece("br","top",0,"left",0);
},_makePiece:function(name,_dbb,_dbc,_dbd,_dbe,_dbf){
var img;
var url=this.shadowPng+name.toUpperCase()+".png";
if(dojo.render.html.ie55||dojo.render.html.ie60){
img=dojo.doc().createElement("div");
img.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+url+"'"+(_dbf?", sizingMethod='"+_dbf+"'":"")+")";
}else{
img=dojo.doc().createElement("img");
img.src=url;
}
img.style.position="absolute";
img.style[_dbb]=_dbc+"px";
img.style[_dbd]=_dbe+"px";
img.style.width=this.shadowThickness+"px";
img.style.height=this.shadowThickness+"px";
this.pieces[name]=img;
this.node.appendChild(img);
},size:function(_dc2,_dc3){
var _dc4=_dc3-(this.shadowOffset+this.shadowThickness+1);
if(_dc4<0){
_dc4=0;
}
if(_dc3<1){
_dc3=1;
}
if(_dc2<1){
_dc2=1;
}
with(this.pieces){
l.style.height=_dc4+"px";
r.style.height=_dc4+"px";
b.style.width=(_dc2-1)+"px";
bl.style.top=(_dc3-1)+"px";
b.style.top=(_dc3-1)+"px";
br.style.top=(_dc3-1)+"px";
tr.style.left=(_dc2-1)+"px";
r.style.left=(_dc2-1)+"px";
br.style.left=(_dc2-1)+"px";
}
}});
dojo.provide("dojo.dnd.DragAndDrop");
dojo.declare("dojo.dnd.DragSource",null,{type:"",onDragEnd:function(evt){
},onDragStart:function(evt){
},onSelected:function(evt){
},unregister:function(){
dojo.dnd.dragManager.unregisterDragSource(this);
},reregister:function(){
dojo.dnd.dragManager.registerDragSource(this);
}});
dojo.declare("dojo.dnd.DragObject",null,{type:"",register:function(){
var dm=dojo.dnd.dragManager;
if(dm["registerDragObject"]){
dm.registerDragObject(this);
}
},onDragStart:function(evt){
},onDragMove:function(evt){
},onDragOver:function(evt){
},onDragOut:function(evt){
},onDragEnd:function(evt){
},onDragLeave:dojo.lang.forward("onDragOut"),onDragEnter:dojo.lang.forward("onDragOver"),ondragout:dojo.lang.forward("onDragOut"),ondragover:dojo.lang.forward("onDragOver")});
dojo.declare("dojo.dnd.DropTarget",null,{acceptsType:function(type){
if(!dojo.lang.inArray(this.acceptedTypes,"*")){
if(!dojo.lang.inArray(this.acceptedTypes,type)){
return false;
}
}
return true;
},accepts:function(_dcf){
if(!dojo.lang.inArray(this.acceptedTypes,"*")){
for(var i=0;i<_dcf.length;i++){
if(!dojo.lang.inArray(this.acceptedTypes,_dcf[i].type)){
return false;
}
}
}
return true;
},unregister:function(){
dojo.dnd.dragManager.unregisterDropTarget(this);
},onDragOver:function(evt){
},onDragOut:function(evt){
},onDragMove:function(evt){
},onDropStart:function(evt){
},onDrop:function(evt){
},onDropEnd:function(){
}},function(){
this.acceptedTypes=[];
});
dojo.dnd.DragEvent=function(){
this.dragSource=null;
this.dragObject=null;
this.target=null;
this.eventStatus="success";
};
dojo.declare("dojo.dnd.DragManager",null,{selectedSources:[],dragObjects:[],dragSources:[],registerDragSource:function(_dd6){
},dropTargets:[],registerDropTarget:function(_dd7){
},lastDragTarget:null,currentDragTarget:null,onKeyDown:function(){
},onMouseOut:function(){
},onMouseMove:function(){
},onMouseUp:function(){
}});
dojo.provide("dojo.dnd.HtmlDragManager");
dojo.declare("dojo.dnd.HtmlDragManager",dojo.dnd.DragManager,{disabled:false,nestedTargets:false,mouseDownTimer:null,dsCounter:0,dsPrefix:"dojoDragSource",dropTargetDimensions:[],currentDropTarget:null,previousDropTarget:null,_dragTriggered:false,selectedSources:[],dragObjects:[],dragSources:[],dropTargets:[],currentX:null,currentY:null,lastX:null,lastY:null,mouseDownX:null,mouseDownY:null,threshold:7,dropAcceptable:false,cancelEvent:function(e){
e.stopPropagation();
e.preventDefault();
},registerDragSource:function(ds){
if(ds["domNode"]){
var dp=this.dsPrefix;
var _ddb=dp+"Idx_"+(this.dsCounter++);
ds.dragSourceId=_ddb;
this.dragSources[_ddb]=ds;
ds.domNode.setAttribute(dp,_ddb);
if(dojo.render.html.ie){
dojo.event.browser.addListener(ds.domNode,"ondragstart",this.cancelEvent);
}
}
},unregisterDragSource:function(ds){
if(ds["domNode"]){
var dp=this.dsPrefix;
var _dde=ds.dragSourceId;
delete ds.dragSourceId;
delete this.dragSources[_dde];
ds.domNode.setAttribute(dp,null);
if(dojo.render.html.ie){
dojo.event.browser.removeListener(ds.domNode,"ondragstart",this.cancelEvent);
}
}
},registerDropTarget:function(dt){
this.dropTargets.push(dt);
},unregisterDropTarget:function(dt){
var _de1=dojo.lang.find(this.dropTargets,dt,true);
if(_de1>=0){
this.dropTargets.splice(_de1,1);
}
},getDragSource:function(e){
var tn=e.target;
if(tn===dojo.body()){
return;
}
var ta=dojo.html.getAttribute(tn,this.dsPrefix);
while((!ta)&&(tn)){
tn=tn.parentNode;
if((!tn)||(tn===dojo.body())){
return;
}
ta=dojo.html.getAttribute(tn,this.dsPrefix);
}
return this.dragSources[ta];
},onKeyDown:function(e){
},onMouseDown:function(e){
if(this.disabled){
return;
}
if(dojo.render.html.ie){
if(e.button!=1){
return;
}
}else{
if(e.which!=1){
return;
}
}
var _de7=e.target.nodeType==dojo.html.TEXT_NODE?e.target.parentNode:e.target;
if(dojo.html.isTag(_de7,"button","textarea","input","select","option")){
return;
}
var ds=this.getDragSource(e);
if(!ds){
return;
}
if(!dojo.lang.inArray(this.selectedSources,ds)){
this.selectedSources.push(ds);
ds.onSelected();
}
this.mouseDownX=e.pageX;
this.mouseDownY=e.pageY;
e.preventDefault();
dojo.event.connect(document,"onmousemove",this,"onMouseMove");
},onMouseUp:function(e,_dea){
if(this.selectedSources.length==0){
return;
}
this.mouseDownX=null;
this.mouseDownY=null;
this._dragTriggered=false;
e.dragSource=this.dragSource;
if((!e.shiftKey)&&(!e.ctrlKey)){
if(this.currentDropTarget){
this.currentDropTarget.onDropStart();
}
dojo.lang.forEach(this.dragObjects,function(_deb){
var ret=null;
if(!_deb){
return;
}
if(this.currentDropTarget){
e.dragObject=_deb;
var ce=this.currentDropTarget.domNode.childNodes;
if(ce.length>0){
e.dropTarget=ce[0];
while(e.dropTarget==_deb.domNode){
e.dropTarget=e.dropTarget.nextSibling;
}
}else{
e.dropTarget=this.currentDropTarget.domNode;
}
if(this.dropAcceptable){
ret=this.currentDropTarget.onDrop(e);
}else{
this.currentDropTarget.onDragOut(e);
}
}
e.dragStatus=this.dropAcceptable&&ret?"dropSuccess":"dropFailure";
dojo.lang.delayThese([function(){
try{
_deb.dragSource.onDragEnd(e);
}
catch(err){
var _dee={};
for(var i in e){
if(i=="type"){
_dee.type="mouseup";
continue;
}
_dee[i]=e[i];
}
_deb.dragSource.onDragEnd(_dee);
}
},function(){
_deb.onDragEnd(e);
}]);
},this);
this.selectedSources=[];
this.dragObjects=[];
this.dragSource=null;
if(this.currentDropTarget){
this.currentDropTarget.onDropEnd();
}
}else{
}
dojo.event.disconnect(document,"onmousemove",this,"onMouseMove");
this.currentDropTarget=null;
},onScroll:function(){
for(var i=0;i<this.dragObjects.length;i++){
if(this.dragObjects[i].updateDragOffset){
this.dragObjects[i].updateDragOffset();
}
}
if(this.dragObjects.length){
this.cacheTargetLocations();
}
},_dragStartDistance:function(x,y){
if((!this.mouseDownX)||(!this.mouseDownX)){
return;
}
var dx=Math.abs(x-this.mouseDownX);
var dx2=dx*dx;
var dy=Math.abs(y-this.mouseDownY);
var dy2=dy*dy;
return parseInt(Math.sqrt(dx2+dy2),10);
},cacheTargetLocations:function(){
dojo.profile.start("cacheTargetLocations");
this.dropTargetDimensions=[];
dojo.lang.forEach(this.dropTargets,function(_df7){
var tn=_df7.domNode;
if(!tn||!_df7.accepts([this.dragSource])){
return;
}
var abs=dojo.html.getAbsolutePosition(tn,true);
var bb=dojo.html.getBorderBox(tn);
this.dropTargetDimensions.push([[abs.x,abs.y],[abs.x+bb.width,abs.y+bb.height],_df7]);
},this);
dojo.profile.end("cacheTargetLocations");
},onMouseMove:function(e){
if((dojo.render.html.ie)&&(e.button!=1)){
this.currentDropTarget=null;
this.onMouseUp(e,true);
return;
}
if((this.selectedSources.length)&&(!this.dragObjects.length)){
var dx;
var dy;
if(!this._dragTriggered){
this._dragTriggered=(this._dragStartDistance(e.pageX,e.pageY)>this.threshold);
if(!this._dragTriggered){
return;
}
dx=e.pageX-this.mouseDownX;
dy=e.pageY-this.mouseDownY;
}
this.dragSource=this.selectedSources[0];
dojo.lang.forEach(this.selectedSources,function(_dfe){
if(!_dfe){
return;
}
var tdo=_dfe.onDragStart(e);
if(tdo){
tdo.onDragStart(e);
tdo.dragOffset.y+=dy;
tdo.dragOffset.x+=dx;
tdo.dragSource=_dfe;
this.dragObjects.push(tdo);
}
},this);
this.previousDropTarget=null;
this.cacheTargetLocations();
}
dojo.lang.forEach(this.dragObjects,function(_e00){
if(_e00){
_e00.onDragMove(e);
}
});
if(this.currentDropTarget){
var c=dojo.html.toCoordinateObject(this.currentDropTarget.domNode,true);
var dtp=[[c.x,c.y],[c.x+c.width,c.y+c.height]];
}
if((!this.nestedTargets)&&(dtp)&&(this.isInsideBox(e,dtp))){
if(this.dropAcceptable){
this.currentDropTarget.onDragMove(e,this.dragObjects);
}
}else{
var _e03=this.findBestTarget(e);
if(_e03.target===null){
if(this.currentDropTarget){
this.currentDropTarget.onDragOut(e);
this.previousDropTarget=this.currentDropTarget;
this.currentDropTarget=null;
}
this.dropAcceptable=false;
return;
}
if(this.currentDropTarget!==_e03.target){
if(this.currentDropTarget){
this.previousDropTarget=this.currentDropTarget;
this.currentDropTarget.onDragOut(e);
}
this.currentDropTarget=_e03.target;
e.dragObjects=this.dragObjects;
this.dropAcceptable=this.currentDropTarget.onDragOver(e);
}else{
if(this.dropAcceptable){
this.currentDropTarget.onDragMove(e,this.dragObjects);
}
}
}
},findBestTarget:function(e){
var _e05=this;
var _e06=new Object();
_e06.target=null;
_e06.points=null;
dojo.lang.every(this.dropTargetDimensions,function(_e07){
if(!_e05.isInsideBox(e,_e07)){
return true;
}
_e06.target=_e07[2];
_e06.points=_e07;
return Boolean(_e05.nestedTargets);
});
return _e06;
},isInsideBox:function(e,_e09){
if((e.pageX>_e09[0][0])&&(e.pageX<_e09[1][0])&&(e.pageY>_e09[0][1])&&(e.pageY<_e09[1][1])){
return true;
}
return false;
},onMouseOver:function(e){
},onMouseOut:function(e){
}});
dojo.dnd.dragManager=new dojo.dnd.HtmlDragManager();
(function(){
var d=document;
var dm=dojo.dnd.dragManager;
dojo.event.connect(d,"onkeydown",dm,"onKeyDown");
dojo.event.connect(d,"onmouseover",dm,"onMouseOver");
dojo.event.connect(d,"onmouseout",dm,"onMouseOut");
dojo.event.connect(d,"onmousedown",dm,"onMouseDown");
dojo.event.connect(d,"onmouseup",dm,"onMouseUp");
dojo.event.connect(window,"onscroll",dm,"onScroll");
})();
dojo.provide("dojo.dnd.HtmlDragAndDrop");
dojo.declare("dojo.dnd.HtmlDragSource",dojo.dnd.DragSource,{dragClass:"",onDragStart:function(){
var _e0e=new dojo.dnd.HtmlDragObject(this.dragObject,this.type);
if(this.dragClass){
_e0e.dragClass=this.dragClass;
}
if(this.constrainToContainer){
_e0e.constrainTo(this.constrainingContainer||this.domNode.parentNode);
}
return _e0e;
},setDragHandle:function(node){
node=dojo.byId(node);
dojo.dnd.dragManager.unregisterDragSource(this);
this.domNode=node;
dojo.dnd.dragManager.registerDragSource(this);
},setDragTarget:function(node){
this.dragObject=node;
},constrainTo:function(_e11){
this.constrainToContainer=true;
if(_e11){
this.constrainingContainer=_e11;
}
},onSelected:function(){
for(var i=0;i<this.dragObjects.length;i++){
dojo.dnd.dragManager.selectedSources.push(new dojo.dnd.HtmlDragSource(this.dragObjects[i]));
}
},addDragObjects:function(el){
for(var i=0;i<arguments.length;i++){
this.dragObjects.push(dojo.byId(arguments[i]));
}
}},function(node,type){
node=dojo.byId(node);
this.dragObjects=[];
this.constrainToContainer=false;
if(node){
this.domNode=node;
this.dragObject=node;
this.type=(type)||(this.domNode.nodeName.toLowerCase());
dojo.dnd.DragSource.prototype.reregister.call(this);
}
});
dojo.declare("dojo.dnd.HtmlDragObject",dojo.dnd.DragObject,{dragClass:"",opacity:0.5,createIframe:true,disableX:false,disableY:false,createDragNode:function(){
var node=this.domNode.cloneNode(true);
if(this.dragClass){
dojo.html.addClass(node,this.dragClass);
}
if(this.opacity<1){
dojo.html.setOpacity(node,this.opacity);
}
var ltn=node.tagName.toLowerCase();
var isTr=(ltn=="tr");
if((isTr)||(ltn=="tbody")){
var doc=this.domNode.ownerDocument;
var _e1b=doc.createElement("table");
if(isTr){
var _e1c=doc.createElement("tbody");
_e1b.appendChild(_e1c);
_e1c.appendChild(node);
}else{
_e1b.appendChild(node);
}
var _e1d=((isTr)?this.domNode:this.domNode.firstChild);
var _e1e=((isTr)?node:node.firstChild);
var _e1f=_e1d.childNodes;
var _e20=_e1e.childNodes;
for(var i=0;i<_e1f.length;i++){
if((_e20[i])&&(_e20[i].style)){
_e20[i].style.width=dojo.html.getContentBox(_e1f[i]).width+"px";
}
}
node=_e1b;
}
if((dojo.render.html.ie55||dojo.render.html.ie60)&&this.createIframe){
with(node.style){
top="0px";
left="0px";
}
var _e22=document.createElement("div");
_e22.appendChild(node);
this.bgIframe=new dojo.html.BackgroundIframe(_e22);
_e22.appendChild(this.bgIframe.iframe);
node=_e22;
}
node.style.zIndex=999;
return node;
},onDragStart:function(e){
dojo.html.clearSelection();
this.scrollOffset=dojo.html.getScroll().offset;
this.dragStartPosition=dojo.html.getAbsolutePosition(this.domNode,true);
this.dragOffset={y:this.dragStartPosition.y-e.pageY,x:this.dragStartPosition.x-e.pageX};
this.dragClone=this.createDragNode();
this.containingBlockPosition=this.domNode.offsetParent?dojo.html.getAbsolutePosition(this.domNode.offsetParent,true):{x:0,y:0};
if(this.constrainToContainer){
this.constraints=this.getConstraints();
}
with(this.dragClone.style){
position="absolute";
top=this.dragOffset.y+e.pageY+"px";
left=this.dragOffset.x+e.pageX+"px";
}
dojo.body().appendChild(this.dragClone);
dojo.event.topic.publish("dragStart",{source:this});
},getConstraints:function(){
if(this.constrainingContainer.nodeName.toLowerCase()=="body"){
var _e24=dojo.html.getViewport();
var _e25=_e24.width;
var _e26=_e24.height;
var _e27=dojo.html.getScroll().offset;
var x=_e27.x;
var y=_e27.y;
}else{
var _e2a=dojo.html.getContentBox(this.constrainingContainer);
_e25=_e2a.width;
_e26=_e2a.height;
x=this.containingBlockPosition.x+dojo.html.getPixelValue(this.constrainingContainer,"padding-left",true)+dojo.html.getBorderExtent(this.constrainingContainer,"left");
y=this.containingBlockPosition.y+dojo.html.getPixelValue(this.constrainingContainer,"padding-top",true)+dojo.html.getBorderExtent(this.constrainingContainer,"top");
}
var mb=dojo.html.getMarginBox(this.domNode);
return {minX:x,minY:y,maxX:x+_e25-mb.width,maxY:y+_e26-mb.height};
},updateDragOffset:function(){
var _e2c=dojo.html.getScroll().offset;
if(_e2c.y!=this.scrollOffset.y){
var diff=_e2c.y-this.scrollOffset.y;
this.dragOffset.y+=diff;
this.scrollOffset.y=_e2c.y;
}
if(_e2c.x!=this.scrollOffset.x){
var diff=_e2c.x-this.scrollOffset.x;
this.dragOffset.x+=diff;
this.scrollOffset.x=_e2c.x;
}
},onDragMove:function(e){
this.updateDragOffset();
var x=this.dragOffset.x+e.pageX;
var y=this.dragOffset.y+e.pageY;
if(this.constrainToContainer){
if(x<this.constraints.minX){
x=this.constraints.minX;
}
if(y<this.constraints.minY){
y=this.constraints.minY;
}
if(x>this.constraints.maxX){
x=this.constraints.maxX;
}
if(y>this.constraints.maxY){
y=this.constraints.maxY;
}
}
this.setAbsolutePosition(x,y);
dojo.event.topic.publish("dragMove",{source:this});
},setAbsolutePosition:function(x,y){
if(!this.disableY){
this.dragClone.style.top=y+"px";
}
if(!this.disableX){
this.dragClone.style.left=x+"px";
}
},onDragEnd:function(e){
switch(e.dragStatus){
case "dropSuccess":
dojo.html.removeNode(this.dragClone);
this.dragClone=null;
break;
case "dropFailure":
var _e34=dojo.html.getAbsolutePosition(this.dragClone,true);
var _e35={left:this.dragStartPosition.x+1,top:this.dragStartPosition.y+1};
var anim=dojo.lfx.slideTo(this.dragClone,_e35,300);
var _e37=this;
dojo.event.connect(anim,"onEnd",function(e){
dojo.html.removeNode(_e37.dragClone);
_e37.dragClone=null;
});
anim.play();
break;
}
dojo.event.topic.publish("dragEnd",{source:this});
},constrainTo:function(_e39){
this.constrainToContainer=true;
if(_e39){
this.constrainingContainer=_e39;
}else{
this.constrainingContainer=this.domNode.parentNode;
}
}},function(node,type){
this.domNode=dojo.byId(node);
this.type=type;
this.constrainToContainer=false;
this.dragSource=null;
dojo.dnd.DragObject.prototype.register.call(this);
});
dojo.declare("dojo.dnd.HtmlDropTarget",dojo.dnd.DropTarget,{vertical:false,onDragOver:function(e){
if(!this.accepts(e.dragObjects)){
return false;
}
this.childBoxes=[];
for(var i=0,_e3e;i<this.domNode.childNodes.length;i++){
_e3e=this.domNode.childNodes[i];
if(_e3e.nodeType!=dojo.html.ELEMENT_NODE){
continue;
}
var pos=dojo.html.getAbsolutePosition(_e3e,true);
var _e40=dojo.html.getBorderBox(_e3e);
this.childBoxes.push({top:pos.y,bottom:pos.y+_e40.height,left:pos.x,right:pos.x+_e40.width,height:_e40.height,width:_e40.width,node:_e3e});
}
return true;
},_getNodeUnderMouse:function(e){
for(var i=0,_e43;i<this.childBoxes.length;i++){
with(this.childBoxes[i]){
if(e.pageX>=left&&e.pageX<=right&&e.pageY>=top&&e.pageY<=bottom){
return i;
}
}
}
return -1;
},createDropIndicator:function(){
this.dropIndicator=document.createElement("div");
with(this.dropIndicator.style){
position="absolute";
zIndex=999;
if(this.vertical){
borderLeftWidth="1px";
borderLeftColor="black";
borderLeftStyle="solid";
height=dojo.html.getBorderBox(this.domNode).height+"px";
top=dojo.html.getAbsolutePosition(this.domNode,true).y+"px";
}else{
borderTopWidth="1px";
borderTopColor="black";
borderTopStyle="solid";
width=dojo.html.getBorderBox(this.domNode).width+"px";
left=dojo.html.getAbsolutePosition(this.domNode,true).x+"px";
}
}
},onDragMove:function(e,_e45){
var i=this._getNodeUnderMouse(e);
if(!this.dropIndicator){
this.createDropIndicator();
}
var _e47=this.vertical?dojo.html.gravity.WEST:dojo.html.gravity.NORTH;
var hide=false;
if(i<0){
if(this.childBoxes.length){
var _e49=(dojo.html.gravity(this.childBoxes[0].node,e)&_e47);
if(_e49){
hide=true;
}
}else{
var _e49=true;
}
}else{
var _e4a=this.childBoxes[i];
var _e49=(dojo.html.gravity(_e4a.node,e)&_e47);
if(_e4a.node===_e45[0].dragSource.domNode){
hide=true;
}else{
var _e4b=_e49?(i>0?this.childBoxes[i-1]:_e4a):(i<this.childBoxes.length-1?this.childBoxes[i+1]:_e4a);
if(_e4b.node===_e45[0].dragSource.domNode){
hide=true;
}
}
}
if(hide){
this.dropIndicator.style.display="none";
return;
}else{
this.dropIndicator.style.display="";
}
this.placeIndicator(e,_e45,i,_e49);
if(!dojo.html.hasParent(this.dropIndicator)){
dojo.body().appendChild(this.dropIndicator);
}
},placeIndicator:function(e,_e4d,_e4e,_e4f){
var _e50=this.vertical?"left":"top";
var _e51;
if(_e4e<0){
if(this.childBoxes.length){
_e51=_e4f?this.childBoxes[0]:this.childBoxes[this.childBoxes.length-1];
}else{
this.dropIndicator.style[_e50]=dojo.html.getAbsolutePosition(this.domNode,true)[this.vertical?"x":"y"]+"px";
}
}else{
_e51=this.childBoxes[_e4e];
}
if(_e51){
this.dropIndicator.style[_e50]=(_e4f?_e51[_e50]:_e51[this.vertical?"right":"bottom"])+"px";
if(this.vertical){
this.dropIndicator.style.height=_e51.height+"px";
this.dropIndicator.style.top=_e51.top+"px";
}else{
this.dropIndicator.style.width=_e51.width+"px";
this.dropIndicator.style.left=_e51.left+"px";
}
}
},onDragOut:function(e){
if(this.dropIndicator){
dojo.html.removeNode(this.dropIndicator);
delete this.dropIndicator;
}
},onDrop:function(e){
this.onDragOut(e);
var i=this._getNodeUnderMouse(e);
var _e55=this.vertical?dojo.html.gravity.WEST:dojo.html.gravity.NORTH;
if(i<0){
if(this.childBoxes.length){
if(dojo.html.gravity(this.childBoxes[0].node,e)&_e55){
return this.insert(e,this.childBoxes[0].node,"before");
}else{
return this.insert(e,this.childBoxes[this.childBoxes.length-1].node,"after");
}
}
return this.insert(e,this.domNode,"append");
}
var _e56=this.childBoxes[i];
if(dojo.html.gravity(_e56.node,e)&_e55){
return this.insert(e,_e56.node,"before");
}else{
return this.insert(e,_e56.node,"after");
}
},insert:function(e,_e58,_e59){
var node=e.dragObject.domNode;
if(_e59=="before"){
return dojo.html.insertBefore(node,_e58);
}else{
if(_e59=="after"){
return dojo.html.insertAfter(node,_e58);
}else{
if(_e59=="append"){
_e58.appendChild(node);
return true;
}
}
}
return false;
}},function(node,_e5c){
if(arguments.length==0){
return;
}
this.domNode=dojo.byId(node);
dojo.dnd.DropTarget.call(this);
if(_e5c&&dojo.lang.isString(_e5c)){
_e5c=[_e5c];
}
this.acceptedTypes=_e5c||[];
dojo.dnd.dragManager.registerDropTarget(this);
});
dojo.kwCompoundRequire({common:["dojo.dnd.DragAndDrop"],browser:["dojo.dnd.HtmlDragAndDrop"],dashboard:["dojo.dnd.HtmlDragAndDrop"]});
dojo.provide("dojo.dnd.*");
dojo.provide("dojo.dnd.HtmlDragMove");
dojo.declare("dojo.dnd.HtmlDragMoveSource",dojo.dnd.HtmlDragSource,{onDragStart:function(){
var _e5d=new dojo.dnd.HtmlDragMoveObject(this.dragObject,this.type);
if(this.constrainToContainer){
_e5d.constrainTo(this.constrainingContainer);
}
return _e5d;
},onSelected:function(){
for(var i=0;i<this.dragObjects.length;i++){
dojo.dnd.dragManager.selectedSources.push(new dojo.dnd.HtmlDragMoveSource(this.dragObjects[i]));
}
}});
dojo.declare("dojo.dnd.HtmlDragMoveObject",dojo.dnd.HtmlDragObject,{onDragStart:function(e){
dojo.html.clearSelection();
this.dragClone=this.domNode;
if(dojo.html.getComputedStyle(this.domNode,"position")!="absolute"){
this.domNode.style.position="relative";
}
var left=parseInt(dojo.html.getComputedStyle(this.domNode,"left"));
var top=parseInt(dojo.html.getComputedStyle(this.domNode,"top"));
this.dragStartPosition={x:isNaN(left)?0:left,y:isNaN(top)?0:top};
this.scrollOffset=dojo.html.getScroll().offset;
this.dragOffset={y:this.dragStartPosition.y-e.pageY,x:this.dragStartPosition.x-e.pageX};
this.containingBlockPosition={x:0,y:0};
if(this.constrainToContainer){
this.constraints=this.getConstraints();
}
dojo.event.connect(this.domNode,"onclick",this,"_squelchOnClick");
},onDragEnd:function(e){
},setAbsolutePosition:function(x,y){
if(!this.disableY){
this.domNode.style.top=y+"px";
}
if(!this.disableX){
this.domNode.style.left=x+"px";
}
},_squelchOnClick:function(e){
dojo.event.browser.stopEvent(e);
dojo.event.disconnect(this.domNode,"onclick",this,"_squelchOnClick");
}});
dojo.provide("dojo.widget.Dialog");
dojo.declare("dojo.widget.ModalDialogBase",null,{isContainer:true,focusElement:"",bgColor:"black",bgOpacity:0.4,followScroll:true,closeOnBackgroundClick:false,trapTabs:function(e){
if(e.target==this.tabStartOuter){
if(this._fromTrap){
this.tabStart.focus();
this._fromTrap=false;
}else{
this._fromTrap=true;
this.tabEnd.focus();
}
}else{
if(e.target==this.tabStart){
if(this._fromTrap){
this._fromTrap=false;
}else{
this._fromTrap=true;
this.tabEnd.focus();
}
}else{
if(e.target==this.tabEndOuter){
if(this._fromTrap){
this.tabEnd.focus();
this._fromTrap=false;
}else{
this._fromTrap=true;
this.tabStart.focus();
}
}else{
if(e.target==this.tabEnd){
if(this._fromTrap){
this._fromTrap=false;
}else{
this._fromTrap=true;
this.tabStart.focus();
}
}
}
}
}
},clearTrap:function(e){
var _e68=this;
setTimeout(function(){
_e68._fromTrap=false;
},100);
},postCreate:function(){
with(this.domNode.style){
position="absolute";
zIndex=999;
display="none";
overflow="visible";
}
var b=dojo.body();
b.appendChild(this.domNode);
this.bg=document.createElement("div");
this.bg.className="dialogUnderlay";
with(this.bg.style){
position="absolute";
left=top="0px";
zIndex=998;
display="none";
}
b.appendChild(this.bg);
this.setBackgroundColor(this.bgColor);
this.bgIframe=new dojo.html.BackgroundIframe();
if(this.bgIframe.iframe){
with(this.bgIframe.iframe.style){
position="absolute";
left=top="0px";
zIndex=90;
display="none";
}
}
if(this.closeOnBackgroundClick){
dojo.event.kwConnect({srcObj:this.bg,srcFunc:"onclick",adviceObj:this,adviceFunc:"onBackgroundClick",once:true});
}
},uninitialize:function(){
this.bgIframe.remove();
dojo.html.removeNode(this.bg,true);
},setBackgroundColor:function(_e6a){
if(arguments.length>=3){
_e6a=new dojo.gfx.color.Color(arguments[0],arguments[1],arguments[2]);
}else{
_e6a=new dojo.gfx.color.Color(_e6a);
}
this.bg.style.backgroundColor=_e6a.toString();
return this.bgColor=_e6a;
},setBackgroundOpacity:function(op){
if(arguments.length==0){
op=this.bgOpacity;
}
dojo.html.setOpacity(this.bg,op);
try{
this.bgOpacity=dojo.html.getOpacity(this.bg);
}
catch(e){
this.bgOpacity=op;
}
return this.bgOpacity;
},_sizeBackground:function(){
if(this.bgOpacity>0){
var _e6c=dojo.html.getViewport();
var h=_e6c.height;
var w=_e6c.width;
with(this.bg.style){
width=w+"px";
height=h+"px";
}
var _e6f=dojo.html.getScroll().offset;
this.bg.style.top=_e6f.y+"px";
this.bg.style.left=_e6f.x+"px";
var _e6c=dojo.html.getViewport();
if(_e6c.width!=w){
this.bg.style.width=_e6c.width+"px";
}
if(_e6c.height!=h){
this.bg.style.height=_e6c.height+"px";
}
}
this.bgIframe.size(this.bg);
},_showBackground:function(){
if(this.bgOpacity>0){
this.bg.style.display="block";
}
if(this.bgIframe.iframe){
this.bgIframe.iframe.style.display="block";
}
},placeModalDialog:function(){
var _e70=dojo.html.getScroll().offset;
var _e71=dojo.html.getViewport();
var mb;
if(this.isShowing()){
mb=dojo.html.getMarginBox(this.domNode);
}else{
dojo.html.setVisibility(this.domNode,false);
dojo.html.show(this.domNode);
mb=dojo.html.getMarginBox(this.domNode);
dojo.html.hide(this.domNode);
dojo.html.setVisibility(this.domNode,true);
}
var x=_e70.x+(_e71.width-mb.width)/2;
var y=_e70.y+(_e71.height-mb.height)/2;
with(this.domNode.style){
left=x+"px";
top=y+"px";
}
},_onKey:function(evt){
if(evt.key){
var node=evt.target;
while(node!=null){
if(node==this.domNode){
return;
}
node=node.parentNode;
}
if(evt.key!=evt.KEY_TAB){
dojo.event.browser.stopEvent(evt);
}else{
if(!dojo.render.html.opera){
try{
this.tabStart.focus();
}
catch(e){
}
}
}
}
},showModalDialog:function(){
if(this.followScroll&&!this._scrollConnected){
this._scrollConnected=true;
dojo.event.connect(window,"onscroll",this,"_onScroll");
}
dojo.event.connect(document.documentElement,"onkey",this,"_onKey");
this.placeModalDialog();
this.setBackgroundOpacity();
this._sizeBackground();
this._showBackground();
this._fromTrap=true;
setTimeout(dojo.lang.hitch(this,function(){
try{
this.tabStart.focus();
}
catch(e){
}
}),50);
},hideModalDialog:function(){
if(this.focusElement){
dojo.byId(this.focusElement).focus();
dojo.byId(this.focusElement).blur();
}
this.bg.style.display="none";
this.bg.style.width=this.bg.style.height="1px";
if(this.bgIframe.iframe){
this.bgIframe.iframe.style.display="none";
}
dojo.event.disconnect(document.documentElement,"onkey",this,"_onKey");
if(this._scrollConnected){
this._scrollConnected=false;
dojo.event.disconnect(window,"onscroll",this,"_onScroll");
}
},_onScroll:function(){
var _e77=dojo.html.getScroll().offset;
this.bg.style.top=_e77.y+"px";
this.bg.style.left=_e77.x+"px";
this.placeModalDialog();
},checkSize:function(){
if(this.isShowing()){
this._sizeBackground();
this.placeModalDialog();
this.onResized();
}
},onBackgroundClick:function(){
if(this.lifetime-this.timeRemaining>=this.blockDuration){
return;
}
this.hide();
}});
dojo.widget.defineWidget("dojo.widget.Dialog",[dojo.widget.ContentPane,dojo.widget.ModalDialogBase],{templateString:"<div id=\"${this.widgetId}\" class=\"dojoDialog\" dojoattachpoint=\"wrapper\">\n\t<span dojoattachpoint=\"tabStartOuter\" dojoonfocus=\"trapTabs\" dojoonblur=\"clearTrap\"\ttabindex=\"0\"></span>\n\t<span dojoattachpoint=\"tabStart\" dojoonfocus=\"trapTabs\" dojoonblur=\"clearTrap\" tabindex=\"0\"></span>\n\t<div dojoattachpoint=\"containerNode\" style=\"position: relative; z-index: 2;\"></div>\n\t<span dojoattachpoint=\"tabEnd\" dojoonfocus=\"trapTabs\" dojoonblur=\"clearTrap\" tabindex=\"0\"></span>\n\t<span dojoattachpoint=\"tabEndOuter\" dojoonfocus=\"trapTabs\" dojoonblur=\"clearTrap\" tabindex=\"0\"></span>\n</div>\n",blockDuration:0,lifetime:0,closeNode:"",postMixInProperties:function(){
dojo.widget.Dialog.superclass.postMixInProperties.apply(this,arguments);
if(this.closeNode){
this.setCloseControl(this.closeNode);
}
},postCreate:function(){
dojo.widget.Dialog.superclass.postCreate.apply(this,arguments);
dojo.widget.ModalDialogBase.prototype.postCreate.apply(this,arguments);
},show:function(){
if(this.lifetime){
this.timeRemaining=this.lifetime;
if(this.timerNode){
this.timerNode.innerHTML=Math.ceil(this.timeRemaining/1000);
}
if(this.blockDuration&&this.closeNode){
if(this.lifetime>this.blockDuration){
this.closeNode.style.visibility="hidden";
}else{
this.closeNode.style.display="none";
}
}
if(this.timer){
clearInterval(this.timer);
}
this.timer=setInterval(dojo.lang.hitch(this,"_onTick"),100);
}
this.showModalDialog();
dojo.widget.Dialog.superclass.show.call(this);
},onLoad:function(){
this.placeModalDialog();
dojo.widget.Dialog.superclass.onLoad.call(this);
},fillInTemplate:function(){
},hide:function(){
this.hideModalDialog();
dojo.widget.Dialog.superclass.hide.call(this);
if(this.timer){
clearInterval(this.timer);
}
},setTimerNode:function(node){
this.timerNode=node;
},setCloseControl:function(node){
this.closeNode=dojo.byId(node);
dojo.event.connect(this.closeNode,"onclick",this,"hide");
},setShowControl:function(node){
node=dojo.byId(node);
dojo.event.connect(node,"onclick",this,"show");
},_onTick:function(){
if(this.timer){
this.timeRemaining-=100;
if(this.lifetime-this.timeRemaining>=this.blockDuration){
if(this.closeNode){
this.closeNode.style.visibility="visible";
}
}
if(!this.timeRemaining){
clearInterval(this.timer);
this.hide();
}else{
if(this.timerNode){
this.timerNode.innerHTML=Math.ceil(this.timeRemaining/1000);
}
}
}
}});
dojo.provide("dojo.widget.ResizeHandle");
dojo.widget.defineWidget("dojo.widget.ResizeHandle",dojo.widget.HtmlWidget,{targetElmId:"",templateCssString:".dojoHtmlResizeHandle {\n\tfloat: right;\n\tposition: absolute;\n\tright: 2px;\n\tbottom: 2px;\n\twidth: 13px;\n\theight: 13px;\n\tz-index: 20;\n\tcursor: nw-resize;\n\tbackground-image: url(grabCorner.gif);\n\tline-height: 0px;\n}\n",templateCssPath:dojo.uri.moduleUri("dojo.widget","templates/ResizeHandle.css"),templateString:"<div class=\"dojoHtmlResizeHandle\"><div></div></div>",postCreate:function(){
dojo.event.connect(this.domNode,"onmousedown",this,"_beginSizing");
},_beginSizing:function(e){
if(this._isSizing){
return false;
}
this.targetWidget=dojo.widget.byId(this.targetElmId);
this.targetDomNode=this.targetWidget?this.targetWidget.domNode:dojo.byId(this.targetElmId);
if(!this.targetDomNode){
return;
}
this._isSizing=true;
this.startPoint={"x":e.clientX,"y":e.clientY};
var mb=dojo.html.getMarginBox(this.targetDomNode);
this.startSize={"w":mb.width,"h":mb.height};
dojo.event.kwConnect({srcObj:dojo.body(),srcFunc:"onmousemove",targetObj:this,targetFunc:"_changeSizing",rate:25});
dojo.event.connect(dojo.body(),"onmouseup",this,"_endSizing");
e.preventDefault();
},_changeSizing:function(e){
try{
if(!e.clientX||!e.clientY){
return;
}
}
catch(e){
return;
}
var dx=this.startPoint.x-e.clientX;
var dy=this.startPoint.y-e.clientY;
var newW=this.startSize.w-dx;
var newH=this.startSize.h-dy;
if(this.minSize){
var mb=dojo.html.getMarginBox(this.targetDomNode);
if(newW<this.minSize.w){
newW=mb.width;
}
if(newH<this.minSize.h){
newH=mb.height;
}
}
if(this.targetWidget){
this.targetWidget.resizeTo(newW,newH);
}else{
dojo.html.setMarginBox(this.targetDomNode,{width:newW,height:newH});
}
e.preventDefault();
},_endSizing:function(e){
dojo.event.disconnect(dojo.body(),"onmousemove",this,"_changeSizing");
dojo.event.disconnect(dojo.body(),"onmouseup",this,"_endSizing");
this._isSizing=false;
}});
dojo.provide("dojo.widget.FloatingPane");
dojo.declare("dojo.widget.FloatingPaneBase",null,{title:"",iconSrc:"",hasShadow:false,constrainToContainer:false,taskBarId:"",resizable:true,titleBarDisplay:true,windowState:"normal",displayCloseAction:false,displayMinimizeAction:false,displayMaximizeAction:false,_max_taskBarConnectAttempts:5,_taskBarConnectAttempts:0,templateString:"<div id=\"${this.widgetId}\" dojoAttachEvent=\"onMouseDown\" class=\"dojoFloatingPane\">\n\t<div dojoAttachPoint=\"titleBar\" class=\"dojoFloatingPaneTitleBar\"  style=\"display:none\">\n\t  \t<img dojoAttachPoint=\"titleBarIcon\"  class=\"dojoFloatingPaneTitleBarIcon\">\n\t\t<div dojoAttachPoint=\"closeAction\" dojoAttachEvent=\"onClick:closeWindow\"\n   \t  \t\tclass=\"dojoFloatingPaneCloseIcon\"></div>\n\t\t<div dojoAttachPoint=\"restoreAction\" dojoAttachEvent=\"onClick:restoreWindow\"\n   \t  \t\tclass=\"dojoFloatingPaneRestoreIcon\"></div>\n\t\t<div dojoAttachPoint=\"maximizeAction\" dojoAttachEvent=\"onClick:maximizeWindow\"\n   \t  \t\tclass=\"dojoFloatingPaneMaximizeIcon\"></div>\n\t\t<div dojoAttachPoint=\"minimizeAction\" dojoAttachEvent=\"onClick:minimizeWindow\"\n   \t  \t\tclass=\"dojoFloatingPaneMinimizeIcon\"></div>\n\t  \t<div dojoAttachPoint=\"titleBarText\" class=\"dojoFloatingPaneTitleText\">${this.title}</div>\n\t</div>\n\n\t<div id=\"${this.widgetId}_container\" dojoAttachPoint=\"containerNode\" class=\"dojoFloatingPaneClient\"></div>\n\n\t<div dojoAttachPoint=\"resizeBar\" class=\"dojoFloatingPaneResizebar\" style=\"display:none\"></div>\n</div>\n",templateCssString:"\n/********** Outer Window ***************/\n\n.dojoFloatingPane {\n\t/* essential css */\n\tposition: absolute;\n\toverflow: visible;\t\t/* so drop shadow is displayed */\n\tz-index: 10;\n\n\t/* styling css */\n\tborder: 1px solid;\n\tborder-color: ThreeDHighlight ThreeDShadow ThreeDShadow ThreeDHighlight;\n\tbackground-color: ThreeDFace;\n}\n\n\n/********** Title Bar ****************/\n\n.dojoFloatingPaneTitleBar {\n\tvertical-align: top;\n\tmargin: 2px 2px 2px 2px;\n\tz-index: 10;\n\tbackground-color: #7596c6;\n\tcursor: default;\n\toverflow: hidden;\n\tborder-color: ThreeDHighlight ThreeDShadow ThreeDShadow ThreeDHighlight;\n\tvertical-align: middle;\n}\n\n.dojoFloatingPaneTitleText {\n\tfloat: left;\n\tpadding: 2px 4px 2px 2px;\n\twhite-space: nowrap;\n\tcolor: CaptionText;\n\tfont: small-caption;\n}\n\n.dojoTitleBarIcon {\n\tfloat: left;\n\theight: 22px;\n\twidth: 22px;\n\tvertical-align: middle;\n\tmargin-right: 5px;\n\tmargin-left: 5px;\n}\n\n.dojoFloatingPaneActions{\n\tfloat: right;\n\tposition: absolute;\n\tright: 2px;\n\ttop: 2px;\n\tvertical-align: middle;\n}\n\n\n.dojoFloatingPaneActionItem {\n\tvertical-align: middle;\n\tmargin-right: 1px;\n\theight: 22px;\n\twidth: 22px;\n}\n\n\n.dojoFloatingPaneTitleBarIcon {\n\t/* essential css */\n\tfloat: left;\n\n\t/* styling css */\n\tmargin-left: 2px;\n\tmargin-right: 4px;\n\theight: 22px;\n}\n\n/* minimize/maximize icons are specified by CSS only */\n.dojoFloatingPaneMinimizeIcon,\n.dojoFloatingPaneMaximizeIcon,\n.dojoFloatingPaneRestoreIcon,\n.dojoFloatingPaneCloseIcon {\n\tvertical-align: middle;\n\theight: 22px;\n\twidth: 22px;\n\tfloat: right;\n}\n.dojoFloatingPaneMinimizeIcon {\n\tbackground-image: url(images/floatingPaneMinimize.gif);\n}\n.dojoFloatingPaneMaximizeIcon {\n\tbackground-image: url(images/floatingPaneMaximize.gif);\n}\n.dojoFloatingPaneRestoreIcon {\n\tbackground-image: url(images/floatingPaneRestore.gif);\n}\n.dojoFloatingPaneCloseIcon {\n\tbackground-image: url(images/floatingPaneClose.gif);\n}\n\n/* bar at bottom of window that holds resize handle */\n.dojoFloatingPaneResizebar {\n\tz-index: 10;\n\theight: 13px;\n\tbackground-color: ThreeDFace;\n}\n\n/************* Client Area ***************/\n\n.dojoFloatingPaneClient {\n\tposition: relative;\n\tz-index: 10;\n\tborder: 1px solid;\n\tborder-color: ThreeDShadow ThreeDHighlight ThreeDHighlight ThreeDShadow;\n\tmargin: 2px;\n\tbackground-color: ThreeDFace;\n\tpadding: 8px;\n\tfont-family: Verdana, Helvetica, Garamond, sans-serif;\n\tfont-size: 12px;\n\toverflow: auto;\n}\n\n",templateCssPath:dojo.uri.moduleUri("dojo.widget","templates/FloatingPane.css"),fillInFloatingPaneTemplate:function(args,frag){
var _e86=this.getFragNodeRef(frag);
dojo.html.copyStyle(this.domNode,_e86);
dojo.body().appendChild(this.domNode);
if(!this.isShowing()){
this.windowState="minimized";
}
if(this.iconSrc==""){
dojo.html.removeNode(this.titleBarIcon);
}else{
this.titleBarIcon.src=this.iconSrc.toString();
}
if(this.titleBarDisplay){
this.titleBar.style.display="";
dojo.html.disableSelection(this.titleBar);
this.titleBarIcon.style.display=(this.iconSrc==""?"none":"");
this.minimizeAction.style.display=(this.displayMinimizeAction?"":"none");
this.maximizeAction.style.display=(this.displayMaximizeAction&&this.windowState!="maximized"?"":"none");
this.restoreAction.style.display=(this.displayMaximizeAction&&this.windowState=="maximized"?"":"none");
this.closeAction.style.display=(this.displayCloseAction?"":"none");
this.drag=new dojo.dnd.HtmlDragMoveSource(this.domNode);
if(this.constrainToContainer){
this.drag.constrainTo();
}
this.drag.setDragHandle(this.titleBar);
var self=this;
dojo.event.topic.subscribe("dragMove",function(info){
if(info.source.domNode==self.domNode){
dojo.event.topic.publish("floatingPaneMove",{source:self});
}
});
}
if(this.resizable){
this.resizeBar.style.display="";
this.resizeHandle=dojo.widget.createWidget("ResizeHandle",{targetElmId:this.widgetId,id:this.widgetId+"_resize"});
this.resizeBar.appendChild(this.resizeHandle.domNode);
}
if(this.hasShadow){
this.shadow=new dojo.lfx.shadow(this.domNode);
}
this.bgIframe=new dojo.html.BackgroundIframe(this.domNode);
if(this.taskBarId){
this._taskBarSetup();
}
dojo.body().removeChild(this.domNode);
},postCreate:function(){
if(dojo.hostenv.post_load_){
this._setInitialWindowState();
}else{
dojo.addOnLoad(this,"_setInitialWindowState");
}
},maximizeWindow:function(evt){
var mb=dojo.html.getMarginBox(this.domNode);
this.previous={width:mb.width||this.width,height:mb.height||this.height,left:this.domNode.style.left,top:this.domNode.style.top,bottom:this.domNode.style.bottom,right:this.domNode.style.right};
if(this.domNode.parentNode.style.overflow.toLowerCase()!="hidden"){
this.parentPrevious={overflow:this.domNode.parentNode.style.overflow};
dojo.debug(this.domNode.parentNode.style.overflow);
this.domNode.parentNode.style.overflow="hidden";
}
this.domNode.style.left=dojo.html.getPixelValue(this.domNode.parentNode,"padding-left",true)+"px";
this.domNode.style.top=dojo.html.getPixelValue(this.domNode.parentNode,"padding-top",true)+"px";
if((this.domNode.parentNode.nodeName.toLowerCase()=="body")){
var _e8b=dojo.html.getViewport();
var _e8c=dojo.html.getPadding(dojo.body());
this.resizeTo(_e8b.width-_e8c.width,_e8b.height-_e8c.height);
}else{
var _e8d=dojo.html.getContentBox(this.domNode.parentNode);
this.resizeTo(_e8d.width,_e8d.height);
}
this.maximizeAction.style.display="none";
this.restoreAction.style.display="";
if(this.resizeHandle){
this.resizeHandle.domNode.style.display="none";
}
this.drag.setDragHandle(null);
this.windowState="maximized";
},minimizeWindow:function(evt){
this.hide();
for(var attr in this.parentPrevious){
this.domNode.parentNode.style[attr]=this.parentPrevious[attr];
}
this.lastWindowState=this.windowState;
this.windowState="minimized";
},restoreWindow:function(evt){
if(this.windowState=="minimized"){
this.show();
if(this.lastWindowState=="maximized"){
this.domNode.parentNode.style.overflow="hidden";
this.windowState="maximized";
}else{
this.windowState="normal";
}
}else{
if(this.windowState=="maximized"){
for(var attr in this.previous){
this.domNode.style[attr]=this.previous[attr];
}
for(var attr in this.parentPrevious){
this.domNode.parentNode.style[attr]=this.parentPrevious[attr];
}
this.resizeTo(this.previous.width,this.previous.height);
this.previous=null;
this.parentPrevious=null;
this.restoreAction.style.display="none";
this.maximizeAction.style.display=this.displayMaximizeAction?"":"none";
if(this.resizeHandle){
this.resizeHandle.domNode.style.display="";
}
this.drag.setDragHandle(this.titleBar);
this.windowState="normal";
}else{
}
}
},toggleDisplay:function(){
if(this.windowState=="minimized"){
this.restoreWindow();
}else{
this.minimizeWindow();
}
},closeWindow:function(evt){
dojo.html.removeNode(this.domNode);
this.destroy();
},onMouseDown:function(evt){
this.bringToTop();
},bringToTop:function(){
var _e94=dojo.widget.manager.getWidgetsByType(this.widgetType);
var _e95=[];
for(var x=0;x<_e94.length;x++){
if(this.widgetId!=_e94[x].widgetId){
_e95.push(_e94[x]);
}
}
_e95.sort(function(a,b){
return a.domNode.style.zIndex-b.domNode.style.zIndex;
});
_e95.push(this);
var _e99=100;
for(x=0;x<_e95.length;x++){
_e95[x].domNode.style.zIndex=_e99+x*2;
}
},_setInitialWindowState:function(){
if(this.isShowing()){
this.width=-1;
var mb=dojo.html.getMarginBox(this.domNode);
this.resizeTo(mb.width,mb.height);
}
if(this.windowState=="maximized"){
this.maximizeWindow();
this.show();
return;
}
if(this.windowState=="normal"){
this.show();
return;
}
if(this.windowState=="minimized"){
this.hide();
return;
}
this.windowState="minimized";
},_taskBarSetup:function(){
var _e9b=dojo.widget.getWidgetById(this.taskBarId);
if(!_e9b){
if(this._taskBarConnectAttempts<this._max_taskBarConnectAttempts){
dojo.lang.setTimeout(this,this._taskBarSetup,50);
this._taskBarConnectAttempts++;
}else{
dojo.debug("Unable to connect to the taskBar");
}
return;
}
_e9b.addChild(this);
},showFloatingPane:function(){
this.bringToTop();
},onFloatingPaneShow:function(){
var mb=dojo.html.getMarginBox(this.domNode);
this.resizeTo(mb.width,mb.height);
},resizeTo:function(_e9d,_e9e){
dojo.html.setMarginBox(this.domNode,{width:_e9d,height:_e9e});
dojo.widget.html.layout(this.domNode,[{domNode:this.titleBar,layoutAlign:"top"},{domNode:this.resizeBar,layoutAlign:"bottom"},{domNode:this.containerNode,layoutAlign:"client"}]);
dojo.widget.html.layout(this.containerNode,this.children,"top-bottom");
this.bgIframe.onResized();
if(this.shadow){
this.shadow.size(_e9d,_e9e);
}
this.onResized();
},checkSize:function(){
},destroyFloatingPane:function(){
if(this.resizeHandle){
this.resizeHandle.destroy();
this.resizeHandle=null;
}
}});
dojo.widget.defineWidget("dojo.widget.FloatingPane",[dojo.widget.ContentPane,dojo.widget.FloatingPaneBase],{fillInTemplate:function(args,frag){
this.fillInFloatingPaneTemplate(args,frag);
dojo.widget.FloatingPane.superclass.fillInTemplate.call(this,args,frag);
},postCreate:function(){
dojo.widget.FloatingPaneBase.prototype.postCreate.apply(this,arguments);
dojo.widget.FloatingPane.superclass.postCreate.apply(this,arguments);
},show:function(){
dojo.widget.FloatingPane.superclass.show.apply(this,arguments);
this.showFloatingPane();
},onShow:function(){
dojo.widget.FloatingPane.superclass.onShow.call(this);
this.onFloatingPaneShow();
},destroy:function(){
this.destroyFloatingPane();
dojo.widget.FloatingPane.superclass.destroy.apply(this,arguments);
}});
dojo.widget.defineWidget("dojo.widget.ModalFloatingPane",[dojo.widget.FloatingPane,dojo.widget.ModalDialogBase],{windowState:"minimized",displayCloseAction:true,postCreate:function(){
dojo.widget.ModalDialogBase.prototype.postCreate.call(this);
dojo.widget.ModalFloatingPane.superclass.postCreate.call(this);
},show:function(){
this.showModalDialog();
dojo.widget.ModalFloatingPane.superclass.show.apply(this,arguments);
this.bg.style.zIndex=this.domNode.style.zIndex-1;
},hide:function(){
this.hideModalDialog();
dojo.widget.ModalFloatingPane.superclass.hide.apply(this,arguments);
},closeWindow:function(){
this.hide();
dojo.widget.ModalFloatingPane.superclass.closeWindow.apply(this,arguments);
}});
dojo.provide("dojo.widget.Editor2Plugin.AlwaysShowToolbar");
dojo.event.topic.subscribe("dojo.widget.Editor2::onLoad",function(_ea1){
if(_ea1.toolbarAlwaysVisible){
var p=new dojo.widget.Editor2Plugin.AlwaysShowToolbar(_ea1);
}
});
dojo.declare("dojo.widget.Editor2Plugin.AlwaysShowToolbar",null,function(_ea3){
this.editor=_ea3;
this.editor.registerLoadedPlugin(this);
this.setup();
},{_scrollSetUp:false,_fixEnabled:false,_scrollThreshold:false,_handleScroll:true,setup:function(){
var tdn=this.editor.toolbarWidget;
if(!tdn.tbBgIframe){
tdn.tbBgIframe=new dojo.html.BackgroundIframe(tdn.domNode);
tdn.tbBgIframe.onResized();
}
this.scrollInterval=setInterval(dojo.lang.hitch(this,"globalOnScrollHandler"),100);
dojo.event.connect("before",this.editor.toolbarWidget,"destroy",this,"destroy");
},globalOnScrollHandler:function(){
var isIE=dojo.render.html.ie;
if(!this._handleScroll){
return;
}
var dh=dojo.html;
var tdn=this.editor.toolbarWidget.domNode;
var db=dojo.body();
if(!this._scrollSetUp){
this._scrollSetUp=true;
var _ea9=dh.getMarginBox(this.editor.domNode).width;
this._scrollThreshold=dh.abs(tdn,true).y;
if((isIE)&&(db)&&(dh.getStyle(db,"background-image")=="none")){
with(db.style){
backgroundImage="url("+dojo.uri.moduleUri("dojo.widget","templates/images/blank.gif")+")";
backgroundAttachment="fixed";
}
}
}
var _eaa=(window["pageYOffset"])?window["pageYOffset"]:(document["documentElement"]||document["body"]).scrollTop;
if(_eaa>this._scrollThreshold){
if(!this._fixEnabled){
var _eab=dojo.html.getMarginBox(tdn);
this.editor.editorObject.style.marginTop=_eab.height+"px";
if(isIE){
tdn.style.left=dojo.html.abs(tdn,dojo.html.boxSizing.MARGIN_BOX).x;
if(tdn.previousSibling){
this._IEOriginalPos=["after",tdn.previousSibling];
}else{
if(tdn.nextSibling){
this._IEOriginalPos=["before",tdn.nextSibling];
}else{
this._IEOriginalPos=["",tdn.parentNode];
}
}
dojo.body().appendChild(tdn);
dojo.html.addClass(tdn,"IEFixedToolbar");
}else{
with(tdn.style){
position="fixed";
top="0px";
}
}
tdn.style.width=_eab.width+"px";
tdn.style.zIndex=1000;
this._fixEnabled=true;
}
if(!dojo.render.html.safari){
var _eac=(this.height)?parseInt(this.editor.height):this.editor._lastHeight;
if(_eaa>(this._scrollThreshold+_eac)){
tdn.style.display="none";
}else{
tdn.style.display="";
}
}
}else{
if(this._fixEnabled){
(this.editor.object||this.editor.iframe).style.marginTop=null;
with(tdn.style){
position="";
top="";
zIndex="";
display="";
}
if(isIE){
tdn.style.left="";
dojo.html.removeClass(tdn,"IEFixedToolbar");
if(this._IEOriginalPos){
dojo.html.insertAtPosition(tdn,this._IEOriginalPos[1],this._IEOriginalPos[0]);
this._IEOriginalPos=null;
}else{
dojo.html.insertBefore(tdn,this.editor.object||this.editor.iframe);
}
}
tdn.style.width="";
this._fixEnabled=false;
}
}
},destroy:function(){
this._IEOriginalPos=null;
this._handleScroll=false;
clearInterval(this.scrollInterval);
this.editor.unregisterLoadedPlugin(this);
if(dojo.render.html.ie){
dojo.html.removeClass(this.editor.toolbarWidget.domNode,"IEFixedToolbar");
}
}});
dojo.provide("dojo.widget.Editor2");
dojo.widget.Editor2Manager=new dojo.widget.HandlerManager;
dojo.lang.mixin(dojo.widget.Editor2Manager,{_currentInstance:null,commandState:{Disabled:0,Latched:1,Enabled:2},getCurrentInstance:function(){
return this._currentInstance;
},setCurrentInstance:function(inst){
this._currentInstance=inst;
},getCommand:function(_eae,name){
var _eb0;
name=name.toLowerCase();
for(var i=0;i<this._registeredHandlers.length;i++){
_eb0=this._registeredHandlers[i](_eae,name);
if(_eb0){
return _eb0;
}
}
switch(name){
case "htmltoggle":
_eb0=new dojo.widget.Editor2BrowserCommand(_eae,name);
break;
case "formatblock":
_eb0=new dojo.widget.Editor2FormatBlockCommand(_eae,name);
break;
case "anchor":
_eb0=new dojo.widget.Editor2Command(_eae,name);
break;
case "createlink":
_eb0=new dojo.widget.Editor2DialogCommand(_eae,name,{contentFile:"dojo.widget.Editor2Plugin.CreateLinkDialog",contentClass:"Editor2CreateLinkDialog",title:"Insert/Edit Link",width:"300px",height:"200px"});
break;
case "insertimage":
_eb0=new dojo.widget.Editor2DialogCommand(_eae,name,{contentFile:"dojo.widget.Editor2Plugin.InsertImageDialog",contentClass:"Editor2InsertImageDialog",title:"Insert/Edit Image",width:"400px",height:"270px"});
break;
default:
var _eb2=this.getCurrentInstance();
if((_eb2&&_eb2.queryCommandAvailable(name))||(!_eb2&&dojo.widget.Editor2.prototype.queryCommandAvailable(name))){
_eb0=new dojo.widget.Editor2BrowserCommand(_eae,name);
}else{
dojo.debug("dojo.widget.Editor2Manager.getCommand: Unknown command "+name);
return;
}
}
return _eb0;
},destroy:function(){
this._currentInstance=null;
dojo.widget.HandlerManager.prototype.destroy.call(this);
}});
dojo.addOnUnload(dojo.widget.Editor2Manager,"destroy");
dojo.lang.declare("dojo.widget.Editor2Command",null,function(_eb3,name){
this._editor=_eb3;
this._updateTime=0;
this._name=name;
},{_text:"Unknown",execute:function(para){
dojo.unimplemented("dojo.widget.Editor2Command.execute");
},getText:function(){
return this._text;
},getState:function(){
return dojo.widget.Editor2Manager.commandState.Enabled;
},destroy:function(){
}});
dojo.widget.Editor2BrowserCommandNames={"bold":"Bold","copy":"Copy","cut":"Cut","Delete":"Delete","indent":"Indent","inserthorizontalrule":"Horizental Rule","insertorderedlist":"Numbered List","insertunorderedlist":"Bullet List","italic":"Italic","justifycenter":"Align Center","justifyfull":"Justify","justifyleft":"Align Left","justifyright":"Align Right","outdent":"Outdent","paste":"Paste","redo":"Redo","removeformat":"Remove Format","selectall":"Select All","strikethrough":"Strikethrough","subscript":"Subscript","superscript":"Superscript","underline":"Underline","undo":"Undo","unlink":"Remove Link","createlink":"Create Link","insertimage":"Insert Image","htmltoggle":"HTML Source","forecolor":"Foreground Color","hilitecolor":"Background Color","plainformatblock":"Paragraph Style","formatblock":"Paragraph Style","fontsize":"Font Size","fontname":"Font Name"};
dojo.lang.declare("dojo.widget.Editor2BrowserCommand",dojo.widget.Editor2Command,function(_eb6,name){
var text=dojo.widget.Editor2BrowserCommandNames[name.toLowerCase()];
if(text){
this._text=text;
}
},{execute:function(para){
this._editor.execCommand(this._name,para);
},getState:function(){
if(this._editor._lastStateTimestamp>this._updateTime||this._state==undefined){
this._updateTime=this._editor._lastStateTimestamp;
try{
if(this._editor.queryCommandEnabled(this._name)){
if(this._editor.queryCommandState(this._name)){
this._state=dojo.widget.Editor2Manager.commandState.Latched;
}else{
this._state=dojo.widget.Editor2Manager.commandState.Enabled;
}
}else{
this._state=dojo.widget.Editor2Manager.commandState.Disabled;
}
}
catch(e){
this._state=dojo.widget.Editor2Manager.commandState.Enabled;
}
}
return this._state;
},getValue:function(){
try{
return this._editor.queryCommandValue(this._name);
}
catch(e){
}
}});
dojo.lang.declare("dojo.widget.Editor2FormatBlockCommand",dojo.widget.Editor2BrowserCommand,{});
dojo.widget.defineWidget("dojo.widget.Editor2Dialog",[dojo.widget.HtmlWidget,dojo.widget.FloatingPaneBase,dojo.widget.ModalDialogBase],{templateString:"<div id=\"${this.widgetId}\" class=\"dojoFloatingPane\">\n\t<span dojoattachpoint=\"tabStartOuter\" dojoonfocus=\"trapTabs\" dojoonblur=\"clearTrap\"\ttabindex=\"0\"></span>\n\t<span dojoattachpoint=\"tabStart\" dojoonfocus=\"trapTabs\" dojoonblur=\"clearTrap\" tabindex=\"0\"></span>\n\t<div dojoAttachPoint=\"titleBar\" class=\"dojoFloatingPaneTitleBar\"  style=\"display:none\">\n\t  \t<img dojoAttachPoint=\"titleBarIcon\"  class=\"dojoFloatingPaneTitleBarIcon\">\n\t\t<div dojoAttachPoint=\"closeAction\" dojoAttachEvent=\"onClick:hide\"\n   \t  \t\tclass=\"dojoFloatingPaneCloseIcon\"></div>\n\t\t<div dojoAttachPoint=\"restoreAction\" dojoAttachEvent=\"onClick:restoreWindow\"\n   \t  \t\tclass=\"dojoFloatingPaneRestoreIcon\"></div>\n\t\t<div dojoAttachPoint=\"maximizeAction\" dojoAttachEvent=\"onClick:maximizeWindow\"\n   \t  \t\tclass=\"dojoFloatingPaneMaximizeIcon\"></div>\n\t\t<div dojoAttachPoint=\"minimizeAction\" dojoAttachEvent=\"onClick:minimizeWindow\"\n   \t  \t\tclass=\"dojoFloatingPaneMinimizeIcon\"></div>\n\t  \t<div dojoAttachPoint=\"titleBarText\" class=\"dojoFloatingPaneTitleText\">${this.title}</div>\n\t</div>\n\n\t<div id=\"${this.widgetId}_container\" dojoAttachPoint=\"containerNode\" class=\"dojoFloatingPaneClient\"></div>\n\t<span dojoattachpoint=\"tabEnd\" dojoonfocus=\"trapTabs\" dojoonblur=\"clearTrap\" tabindex=\"0\"></span>\n\t<span dojoattachpoint=\"tabEndOuter\" dojoonfocus=\"trapTabs\" dojoonblur=\"clearTrap\" tabindex=\"0\"></span>\n\t<div dojoAttachPoint=\"resizeBar\" class=\"dojoFloatingPaneResizebar\" style=\"display:none\"></div>\n</div>\n",modal:true,width:"",height:"",windowState:"minimized",displayCloseAction:true,contentFile:"",contentClass:"",fillInTemplate:function(args,frag){
this.fillInFloatingPaneTemplate(args,frag);
dojo.widget.Editor2Dialog.superclass.fillInTemplate.call(this,args,frag);
},postCreate:function(){
if(this.contentFile){
dojo.require(this.contentFile);
}
if(this.modal){
dojo.widget.ModalDialogBase.prototype.postCreate.call(this);
}else{
with(this.domNode.style){
zIndex=999;
display="none";
}
}
dojo.widget.FloatingPaneBase.prototype.postCreate.apply(this,arguments);
dojo.widget.Editor2Dialog.superclass.postCreate.call(this);
if(this.width&&this.height){
with(this.domNode.style){
width=this.width;
height=this.height;
}
}
},createContent:function(){
if(!this.contentWidget&&this.contentClass){
this.contentWidget=dojo.widget.createWidget(this.contentClass);
this.addChild(this.contentWidget);
}
},show:function(){
if(!this.contentWidget){
dojo.widget.Editor2Dialog.superclass.show.apply(this,arguments);
this.createContent();
dojo.widget.Editor2Dialog.superclass.hide.call(this);
}
if(!this.contentWidget||!this.contentWidget.loadContent()){
return;
}
this.showFloatingPane();
dojo.widget.Editor2Dialog.superclass.show.apply(this,arguments);
if(this.modal){
this.showModalDialog();
}
if(this.modal){
this.bg.style.zIndex=this.domNode.style.zIndex-1;
}
},onShow:function(){
dojo.widget.Editor2Dialog.superclass.onShow.call(this);
this.onFloatingPaneShow();
},closeWindow:function(){
this.hide();
dojo.widget.Editor2Dialog.superclass.closeWindow.apply(this,arguments);
},hide:function(){
if(this.modal){
this.hideModalDialog();
}
dojo.widget.Editor2Dialog.superclass.hide.call(this);
},checkSize:function(){
if(this.isShowing()){
if(this.modal){
this._sizeBackground();
}
this.placeModalDialog();
this.onResized();
}
}});
dojo.widget.defineWidget("dojo.widget.Editor2DialogContent",dojo.widget.HtmlWidget,{widgetsInTemplate:true,loadContent:function(){
return true;
},cancel:function(){
this.parent.hide();
}});
dojo.lang.declare("dojo.widget.Editor2DialogCommand",dojo.widget.Editor2BrowserCommand,function(_ebc,name,_ebe){
this.dialogParas=_ebe;
},{execute:function(){
if(!this.dialog){
if(!this.dialogParas.contentFile||!this.dialogParas.contentClass){
alert("contentFile and contentClass should be set for dojo.widget.Editor2DialogCommand.dialogParas!");
return;
}
this.dialog=dojo.widget.createWidget("Editor2Dialog",this.dialogParas);
dojo.body().appendChild(this.dialog.domNode);
dojo.event.connect(this,"destroy",this.dialog,"destroy");
}
this.dialog.show();
},getText:function(){
return this.dialogParas.title||dojo.widget.Editor2DialogCommand.superclass.getText.call(this);
}});
dojo.widget.Editor2ToolbarGroups={};
dojo.widget.defineWidget("dojo.widget.Editor2",dojo.widget.RichText,function(){
this._loadedCommands={};
},{toolbarAlwaysVisible:false,toolbarWidget:null,scrollInterval:null,toolbarTemplatePath:dojo.uri.cache.set(dojo.uri.moduleUri("dojo.widget","templates/EditorToolbarOneline.html"), "<div class=\"EditorToolbarDomNode EditorToolbarSmallBg\">\n\t<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"dojoEditor\">\n\t\t<tbody>\n\t\t\t<tr valign=\"top\" align=\"left\">\n\t\t\t\t<td class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer dojoEditorToolbarItem\" dojoETItemName=\"htmltoggle\">\n\t\t\t\t\t\t<span class=\"dojoE2TBIcon\" \n\t\t\t\t\t\tstyle=\"background-image: none; width: 30px;\" >&lt;h&gt;</span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer dojoEditorToolbarItem\" dojoETItemName=\"copy\">\n\t\t\t\t\t\t<span class=\"dojoE2TBIcon dojoE2TBIcon_Copy\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer dojoEditorToolbarItem\" dojoETItemName=\"paste\">\n\t\t\t\t\t\t<span class=\"dojoE2TBIcon dojoE2TBIcon_Paste\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer dojoEditorToolbarItem\" dojoETItemName=\"undo\">\n\t\t\t\t\t\t<!-- FIXME: should we have the text \"undo\" here? -->\n\t\t\t\t\t\t<span class=\"dojoE2TBIcon dojoE2TBIcon_Undo\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer dojoEditorToolbarItem\" dojoETItemName=\"redo\">\n\t\t\t\t\t\t<span class=\"dojoE2TBIcon dojoE2TBIcon_Redo\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td isSpacer=\"true\" class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer\">\n\t\t\t\t\t\t<span class=\"dojoE2TBIcon dojoE2TBIcon_Sep\" style=\"width: 5px; min-width: 5px;\"></span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer dojoEditorToolbarItem\" dojoETItemName=\"createlink\">\n\t\t\t\t\t\t<span class=\"dojoE2TBIcon dojoE2TBIcon_Link\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer dojoEditorToolbarItem\" dojoETItemName=\"insertimage\">\n\t\t\t\t\t\t<span class=\"dojoE2TBIcon dojoE2TBIcon_Image\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer dojoEditorToolbarItem\" dojoETItemName=\"inserthorizontalrule\">\n\t\t\t\t\t\t<span class=\"dojoE2TBIcon dojoE2TBIcon_HorizontalLine \">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer dojoEditorToolbarItem\" dojoETItemName=\"bold\">\n\t\t\t\t\t\t<span class=\"dojoE2TBIcon dojoE2TBIcon_Bold\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer dojoEditorToolbarItem\" dojoETItemName=\"italic\">\n\t\t\t\t\t\t<span class=\"dojoE2TBIcon dojoE2TBIcon_Italic\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer dojoEditorToolbarItem\" dojoETItemName=\"underline\">\n\t\t\t\t\t\t<span class=\"dojoE2TBIcon dojoE2TBIcon_Underline\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer dojoEditorToolbarItem\" dojoETItemName=\"strikethrough\">\n\t\t\t\t\t\t<span \n\t\t\t\t\t\t\tclass=\"dojoE2TBIcon dojoE2TBIcon_StrikeThrough\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td isSpacer=\"true\" class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer\">\n\t\t\t\t\t\t<span class=\"dojoE2TBIcon dojoE2TBIcon_Sep\" \n\t\t\t\t\t\t\tstyle=\"width: 5px; min-width: 5px;\"></span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer dojoEditorToolbarItem\" dojoETItemName=\"insertunorderedlist\">\n\t\t\t\t\t\t<span \n\t\t\t\t\t\t\tclass=\"dojoE2TBIcon dojoE2TBIcon_BulletedList\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer dojoEditorToolbarItem\" dojoETItemName=\"insertorderedlist\">\n\t\t\t\t\t\t<span \n\t\t\t\t\t\t\tclass=\"dojoE2TBIcon dojoE2TBIcon_NumberedList\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td isSpacer=\"true\" class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer\">\n\t\t\t\t\t\t<span class=\"dojoE2TBIcon dojoE2TBIcon_Sep\" style=\"width: 5px; min-width: 5px;\"></span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer dojoEditorToolbarItem\" dojoETItemName=\"indent\">\n\t\t\t\t\t\t<span class=\"dojoE2TBIcon dojoE2TBIcon_Indent\" \n\t\t\t\t\t\t\tunselectable=\"on\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer dojoEditorToolbarItem\" dojoETItemName=\"outdent\">\n\t\t\t\t\t\t<span class=\"dojoE2TBIcon dojoE2TBIcon_Outdent\" \n\t\t\t\t\t\t\tunselectable=\"on\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td isSpacer=\"true\" class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer\">\n\t\t\t\t\t\t<span class=\"dojoE2TBIcon dojoE2TBIcon_Sep\" style=\"width: 5px; min-width: 5px;\"></span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer dojoEditorToolbarItem\" dojoETItemName=\"forecolor\">\n\t\t\t\t\t\t<span class=\"dojoE2TBIcon dojoE2TBIcon_TextColor\" \n\t\t\t\t\t\t\tunselectable=\"on\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer dojoEditorToolbarItem\" dojoETItemName=\"hilitecolor\">\n\t\t\t\t\t\t<span class=\"dojoE2TBIcon dojoE2TBIcon_BackgroundColor\" \n\t\t\t\t\t\t\tunselectable=\"on\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td isSpacer=\"true\"  class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer\">\n\t\t\t\t\t\t<span class=\"dojoE2TBIcon dojoE2TBIcon_Sep\" style=\"width: 5px; min-width: 5px;\"></span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer dojoEditorToolbarItem\" dojoETItemName=\"justifyleft\">\n\t\t\t\t\t\t<span class=\"dojoE2TBIcon dojoE2TBIcon_LeftJustify\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td  class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer dojoEditorToolbarItem\" dojoETItemName=\"justifycenter\">\n\t\t\t\t\t\t<span class=\"dojoE2TBIcon dojoE2TBIcon_CenterJustify\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td  class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer dojoEditorToolbarItem\" dojoETItemName=\"justifyright\">\n\t\t\t\t\t\t<span class=\"dojoE2TBIcon dojoE2TBIcon_RightJustify\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\n\t\t\t\t<td class=\"dojoEditor\">\n\t\t\t\t\t<span class=\"iconContainer dojoEditorToolbarItem\" dojoETItemName=\"justifyfull\">\n\t\t\t\t\t\t<span class=\"dojoE2TBIcon dojoE2TBIcon_BlockJustify\">&nbsp;</span>\n\t\t\t\t\t</span>\n\t\t\t\t</td>\t\n\t\t\t\t<td class=\"dojoEditor\">\n\t\t\t\t\t<select class=\"dojoEditorToolbarItem\" dojoETItemName=\"plainformatblock\">\n\t\t\t\t\t\t<!-- FIXME: using \"p\" here inserts a paragraph in most cases! -->\n\t\t\t\t\t\t<option value=\"\">-- format --</option>\n\t\t\t\t\t\t<option value=\"p\">Normal</option>\n\t\t\t\t\t\t<option value=\"pre\">Fixed Font</option>\n\t\t\t\t\t\t<option value=\"h1\">Main Heading</option>\n\t\t\t\t\t\t<option value=\"h2\">Section Heading</option>\n\t\t\t\t\t\t<option value=\"h3\">Sub-Heading</option>\n\t\t\t\t\t\t<!-- <option value=\"blockquote\">Block Quote</option> -->\n\t\t\t\t\t</select>\n\t\t\t\t</td>\n\t\t\t\t<!--td> uncomment to enable save button -->\n\t\t\t\t\t<!-- save -->\n\t\t\t\t\t<!--span class=\"iconContainer dojoEditorToolbarItem\" dojoETItemName=\"save\">\n\t\t\t\t\t\t<span class=\"dojoE2TBIcon dojoE2TBIcon_Save\">&nbsp;</span>\n\t\t\t\t\t</span-->\n\t\t\t\t<!--/td -->\n\t\t\t\t<td width=\"*\">&nbsp;</td>\n\t\t\t</tr>\n\t\t</tbody>\n\t</table>\n</div>\n"),toolbarTemplateCssPath:null,toolbarPlaceHolder:"",_inSourceMode:false,_htmlEditNode:null,toolbarGroup:"",shareToolbar:false,contextMenuGroupSet:"",editorOnLoad:function(){
dojo.event.topic.publish("dojo.widget.Editor2::preLoadingToolbar",this);
if(this.toolbarAlwaysVisible){
}
if(this.toolbarWidget){
this.toolbarWidget.show();
dojo.html.insertBefore(this.toolbarWidget.domNode,this.domNode.firstChild);
}else{
if(this.shareToolbar){
dojo.deprecated("Editor2:shareToolbar is deprecated in favor of toolbarGroup","0.5");
this.toolbarGroup="defaultDojoToolbarGroup";
}
if(this.toolbarGroup){
if(dojo.widget.Editor2ToolbarGroups[this.toolbarGroup]){
this.toolbarWidget=dojo.widget.Editor2ToolbarGroups[this.toolbarGroup];
}
}
if(!this.toolbarWidget){
var _ebf={shareGroup:this.toolbarGroup,parent:this};
_ebf.templateString=dojo.uri.cache.get(this.toolbarTemplatePath);
if(this.toolbarTemplateCssPath){
_ebf.templateCssPath=this.toolbarTemplateCssPath;
_ebf.templateCssString=dojo.uri.cache.get(this.toolbarTemplateCssPath);
}
if(this.toolbarPlaceHolder){
this.toolbarWidget=dojo.widget.createWidget("Editor2Toolbar",_ebf,dojo.byId(this.toolbarPlaceHolder),"after");
}else{
this.toolbarWidget=dojo.widget.createWidget("Editor2Toolbar",_ebf,this.domNode.firstChild,"before");
}
if(this.toolbarGroup){
dojo.widget.Editor2ToolbarGroups[this.toolbarGroup]=this.toolbarWidget;
}
dojo.event.connect(this,"close",this.toolbarWidget,"hide");
this.toolbarLoaded();
}
}
dojo.event.topic.registerPublisher("Editor2.clobberFocus",this,"clobberFocus");
dojo.event.topic.subscribe("Editor2.clobberFocus",this,"setBlur");
dojo.event.topic.publish("dojo.widget.Editor2::onLoad",this);
},toolbarLoaded:function(){
},registerLoadedPlugin:function(obj){
if(!this.loadedPlugins){
this.loadedPlugins=[];
}
this.loadedPlugins.push(obj);
},unregisterLoadedPlugin:function(obj){
for(var i in this.loadedPlugins){
if(this.loadedPlugins[i]===obj){
delete this.loadedPlugins[i];
return;
}
}
dojo.debug("dojo.widget.Editor2.unregisterLoadedPlugin: unknow plugin object: "+obj);
},execCommand:function(_ec3,_ec4){
switch(_ec3.toLowerCase()){
case "htmltoggle":
this.toggleHtmlEditing();
break;
default:
dojo.widget.Editor2.superclass.execCommand.apply(this,arguments);
}
},queryCommandEnabled:function(_ec5,_ec6){
switch(_ec5.toLowerCase()){
case "htmltoggle":
return true;
default:
if(this._inSourceMode){
return false;
}
return dojo.widget.Editor2.superclass.queryCommandEnabled.apply(this,arguments);
}
},queryCommandState:function(_ec7,_ec8){
switch(_ec7.toLowerCase()){
case "htmltoggle":
return this._inSourceMode;
default:
return dojo.widget.Editor2.superclass.queryCommandState.apply(this,arguments);
}
},onClick:function(e){
dojo.widget.Editor2.superclass.onClick.call(this,e);
if(dojo.widget.PopupManager){
if(!e){
e=this.window.event;
}
dojo.widget.PopupManager.onClick(e);
}
},clobberFocus:function(){
},toggleHtmlEditing:function(){
if(this===dojo.widget.Editor2Manager.getCurrentInstance()){
if(!this._inSourceMode){
var html=this.getEditorContent();
this._inSourceMode=true;
if(!this._htmlEditNode){
this._htmlEditNode=dojo.doc().createElement("textarea");
dojo.html.insertAfter(this._htmlEditNode,this.editorObject);
}
this._htmlEditNode.style.display="";
this._htmlEditNode.style.width="100%";
this._htmlEditNode.style.height=dojo.html.getBorderBox(this.editNode).height+"px";
this._htmlEditNode.value=html;
with(this.editorObject.style){
position="absolute";
left="-2000px";
top="-2000px";
}
}else{
this._inSourceMode=false;
this._htmlEditNode.blur();
with(this.editorObject.style){
position="";
left="";
top="";
}
var html=this._htmlEditNode.value;
dojo.lang.setTimeout(this,"replaceEditorContent",1,html);
this._htmlEditNode.style.display="none";
this.focus();
}
this.onDisplayChanged(null,true);
}
},setFocus:function(){
if(dojo.widget.Editor2Manager.getCurrentInstance()===this){
return;
}
this.clobberFocus();
dojo.widget.Editor2Manager.setCurrentInstance(this);
},setBlur:function(){
},saveSelection:function(){
this._bookmark=null;
this._bookmark=dojo.withGlobal(this.window,dojo.html.selection.getBookmark);
},restoreSelection:function(){
if(this._bookmark){
this.focus();
dojo.withGlobal(this.window,"moveToBookmark",dojo.html.selection,[this._bookmark]);
this._bookmark=null;
}else{
dojo.debug("restoreSelection: no saved selection is found!");
}
},_updateToolbarLastRan:null,_updateToolbarTimer:null,_updateToolbarFrequency:500,updateToolbar:function(_ecb){
if((!this.isLoaded)||(!this.toolbarWidget)){
return;
}
var diff=new Date()-this._updateToolbarLastRan;
if((!_ecb)&&(this._updateToolbarLastRan)&&((diff<this._updateToolbarFrequency))){
clearTimeout(this._updateToolbarTimer);
var _ecd=this;
this._updateToolbarTimer=setTimeout(function(){
_ecd.updateToolbar();
},this._updateToolbarFrequency/2);
return;
}else{
this._updateToolbarLastRan=new Date();
}
if(dojo.widget.Editor2Manager.getCurrentInstance()!==this){
return;
}
this.toolbarWidget.update();
},destroy:function(_ece){
this._htmlEditNode=null;
dojo.event.disconnect(this,"close",this.toolbarWidget,"hide");
if(!_ece){
this.toolbarWidget.destroy();
}
dojo.widget.Editor2.superclass.destroy.call(this);
},_lastStateTimestamp:0,onDisplayChanged:function(e,_ed0){
this._lastStateTimestamp=(new Date()).getTime();
dojo.widget.Editor2.superclass.onDisplayChanged.call(this,e);
this.updateToolbar(_ed0);
},onLoad:function(){
try{
dojo.widget.Editor2.superclass.onLoad.call(this);
}
catch(e){
dojo.debug(e);
}
this.editorOnLoad();
},onFocus:function(){
dojo.widget.Editor2.superclass.onFocus.call(this);
this.setFocus();
},getEditorContent:function(){
if(this._inSourceMode){
return this._htmlEditNode.value;
}
return dojo.widget.Editor2.superclass.getEditorContent.call(this);
},replaceEditorContent:function(html){
if(this._inSourceMode){
this._htmlEditNode.value=html;
return;
}
dojo.widget.Editor2.superclass.replaceEditorContent.apply(this,arguments);
},getCommand:function(name){
if(this._loadedCommands[name]){
return this._loadedCommands[name];
}
var cmd=dojo.widget.Editor2Manager.getCommand(this,name);
this._loadedCommands[name]=cmd;
return cmd;
},shortcuts:[["bold"],["italic"],["underline"],["selectall","a"],["insertunorderedlist","\\"]],setupDefaultShortcuts:function(){
var exec=function(cmd){
return function(){
cmd.execute();
};
};
var self=this;
dojo.lang.forEach(this.shortcuts,function(item){
var cmd=self.getCommand(item[0]);
if(cmd){
self.addKeyHandler(item[1]?item[1]:item[0].charAt(0),item[2]==undefined?self.KEY_CTRL:item[2],exec(cmd));
}
});
}});

