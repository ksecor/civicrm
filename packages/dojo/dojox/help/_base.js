/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojox.help._base"]){
dojo._hasResource["dojox.help._base"]=true;
dojo.provide("dojox.help._base");
dojo.require("dojox.rpc.Service");
dojo.require("dojo.io.script");
dojo.experimental("dojox.help");
console.warn("Script causes side effects (on numbers, strings, and booleans). Call dojox.help.noConflict() if you plan on executing code.");
dojox.help={locate:function(_1,_2,_3){
_3=_3||20;
var _4=[];
var _5={};
var _6;
if(_2){
if(!dojo.isArray(_2)){
_2=[_2];
}
for(var i=0,_8;_8=_2[i];i++){
_6=_8;
if(dojo.isString(_8)){
_8=dojo.getObject(_8);
if(!_8){
continue;
}
}else{
if(dojo.isObject(_8)){
_6=_8.__name__;
}else{
continue;
}
}
_4.push(_8);
if(_6){
_6=_6.split(".")[0];
if(!_5[_6]&&dojo.indexOf(dojox.help._namespaces,_6)==-1){
dojox.help.refresh(_6);
}
_5[_6]=true;
}
}
}
if(!_4.length){
_4.push({__name__:"window"});
dojo.forEach(dojox.help._namespaces,function(_9){
_5[_9]=true;
});
}
var _a=[];
out:
for(var i=0,_8;_8=_4[i];i++){
var _b=_8.__name__||"";
var _c=dojo.some(_4,function(_d){
_d=_d.__name__||"";
return (_b.indexOf(_d+".")==0);
});
if(_b&&!_c){
_6=_b.split(".")[0];
var _e=[];
if(_b=="window"){
for(_6 in dojox.help._names){
if(dojo.isArray(dojox.help._names[_6])){
_e=_e.concat(dojox.help._names[_6]);
}
}
}else{
_e=dojox.help._names[_6];
}
for(var j=0,_10;_10=_e[j];j++){
if((_b=="window"||_10.indexOf(_b+".")==0)&&_10.toLowerCase().indexOf(_1)!=-1){
if(_10.slice(-10)==".prototype"){
continue;
}
var obj=dojo.getObject(_10);
if(obj){
_a.push([_10,obj]);
if(_a.length==_3){
break out;
}
}
}
}
}
}
dojox.help._displayLocated(_a);
if(!+dojo.isFF){
return "";
}
},refresh:function(_12,_13){
if(arguments.length<2){
_13=true;
}
dojox.help._recurse(_12,_13);
},noConflict:function(_14){
if(arguments.length){
return dojox.help._noConflict(_14);
}else{
while(dojox.help._overrides.length){
var _15=dojox.help._overrides.pop();
var _16=_15[0];
var key=_15[1];
var _18=_16[key];
_16[key]=dojox.help._noConflict(_18);
}
}
},init:function(_19,_1a){
if(_19){
dojox.help._namespaces.concat(_19);
}
dojo.addOnLoad(function(){
dojo.require=(function(_1b){
return function(){
dojox.help.noConflict();
_1b.apply(dojo,arguments);
if(dojox.help._timer){
clearTimeout(dojox.help._timer);
}
dojox.help._timer=setTimeout(function(){
dojo.addOnLoad(function(){
dojox.help.refresh();
dojox.help._timer=false;
});
},500);
};
})(dojo.require);
dojox.help._recurse();
});
},_noConflict:function(_1c){
if(_1c instanceof String){
return _1c.toString();
}else{
if(_1c instanceof Number){
return +_1c;
}else{
if(_1c instanceof Boolean){
return (_1c==true);
}else{
if(dojo.isObject(_1c)){
delete _1c.__name__;
delete _1c.help;
}
}
}
}
return _1c;
},_namespaces:["dojo","dojox","dijit","djConfig"],_rpc:new dojox.rpc.Service(dojo.moduleUrl("dojox.rpc","documentation.smd")),_attributes:["summary","type","returns","parameters"],_clean:function(_1d){
var obj={};
for(var i=0,_20;_20=dojox.help._attributes[i];i++){
var _21=_1d["__"+_20+"__"];
if(_21){
obj[_20]=_21;
}
}
return obj;
},_displayLocated:function(_22){
throw new Error("_displayLocated should be overridden in one of the dojox.help packages");
},_displayHelp:function(_23,obj){
throw new Error("_displayHelp should be overridden in one of the dojox.help packages");
},_addVersion:function(obj){
if(obj.name){
obj.version=[dojo.version.major,dojo.version.minor,dojo.version.patch].join(".");
var _26=obj.name.split(".");
if(_26[0]=="dojo"||_26[0]=="dijit"||_26[0]=="dojox"){
obj.project=_26[0];
}
}
return obj;
},_stripPrototype:function(_27){
var _28=_27.replace(/\.prototype(\.|$)/g,".");
var _29=_28;
if(_28.slice(-1)=="."){
_29=_28=_28.slice(0,-1);
}else{
_28=_27;
}
return [_29,_28];
},_help:function(){
var _2a=this.__name__;
var _2b=dojox.help._stripPrototype(_2a)[0];
var _2c=[];
for(var i=0,_2e;_2e=dojox.help._attributes[i];i++){
if(!this["__"+_2e+"__"]){
_2c.push(_2e);
}
}
dojox.help._displayHelp(true,{name:this.__name__});
if(!_2c.length||this.__searched__){
dojox.help._displayHelp(false,dojox.help._clean(this));
}else{
this.__searched__=true;
dojox.help._rpc.get(dojox.help._addVersion({name:_2b,exact:true,attributes:_2c})).addCallback(this,function(_2f){
if(this.toString===dojox.help._toString){
this.toString(_2f);
}
if(_2f&&_2f.length){
_2f=_2f[0];
for(var i=0,_2e;_2e=dojox.help._attributes[i];i++){
if(_2f[_2e]){
this["__"+_2e+"__"]=_2f[_2e];
}
}
dojox.help._displayHelp(false,dojox.help._clean(this));
}else{
dojox.help._displayHelp(false,false);
}
});
}
if(!+dojo.isFF){
return "";
}
},_parse:function(_31){
delete this.__searching__;
if(_31&&_31.length){
var _32=_31[0].parameters;
if(_32){
var _33=["function ",this.__name__,"("];
this.__parameters__=_32;
for(var i=0,_35;_35=_32[i];i++){
if(i){
_33.push(", ");
}
_33.push(_35.name);
if(_35.types){
var _36=[];
for(var j=0,_38;_38=_35.types[j];j++){
_36.push(_38.title);
}
if(_36.length){
_33.push(": ");
_33.push(_36.join("|"));
}
}
if(_35.repeating){
_33.push("...");
}
if(_35.optional){
_33.push("?");
}
}
_33.push(")");
this.__source__=this.__source__.replace(/function[^\(]*\([^\)]*\)/,_33.join(""));
}
if(this.__output__){
delete this.__output__;
console.log(this);
}
}else{
dojox.help._displayHelp(false,false);
}
},_toStrings:{},_toString:function(_39){
if(!this.__source__){
return this.__name__;
}
var _3a=(!this.__parameters__);
this.__parameters__=[];
if(_39){
dojox.help._parse.call(this,_39);
}else{
if(_3a){
this.__searching__=true;
dojox.help._toStrings[dojox.help._stripPrototype(this.__name__)[0]]=this;
if(dojox.help._toStringTimer){
clearTimeout(dojox.help._toStringTimer);
}
dojox.help._toStringTimer=setTimeout(function(){
dojox.help.__toString();
},50);
}
}
if(!_3a||!this.__searching__){
return this.__source__;
}
var _3b="function Loading info for "+this.__name__+"... (watch console for result) {}";
if(!+dojo.isFF){
this.__output__=true;
return _3b;
}
return {toString:dojo.hitch(this,function(){
this.__output__=true;
return _3b;
})};
},__toString:function(){
if(dojox.help._toStringTimer){
clearTimeout(dojox.help._toStringTimer);
}
var _3c=[];
dojox.help.noConflict(dojox.help._toStrings);
for(var _3d in dojox.help._toStrings){
_3c.push(_3d);
}
while(_3c.length){
dojox.help._rpc.batch(dojox.help._addVersion({names:_3c.splice(-50,50),exact:true,attributes:["parameters"]})).addCallback(this,function(_3e){
for(var i=0,_40;_40=_3e[i];i++){
fn=dojox.help._toStrings[_40.name];
if(fn){
dojox.help._parse.call(fn,[_40]);
delete dojox.help._toStrings[_40.name];
}
}
});
}
},_overrides:[],_recursions:[],_names:{},_recurse:function(_41,_42){
if(arguments.length<2){
_42=true;
}
var _43=[];
if(_41&&dojo.isString(_41)){
dojox.help.__recurse(dojo.getObject(_41),_41,_41,_43,_42);
}else{
for(var i=0,_41;_41=dojox.help._namespaces[i];i++){
if(window[_41]){
dojox.help._recursions.push([window[_41],_41,_41]);
window[_41].__name__=_41;
if(!window[_41].help){
window[_41].help=dojox.help._help;
}
}
}
}
while(dojox.help._recursions.length){
var _45=dojox.help._recursions.shift();
dojox.help.__recurse(_45[0],_45[1],_45[2],_43,_42);
}
for(var i=0,_46;_46=_43[i];i++){
delete _46.__seen__;
}
},__recurse:function(_47,_48,_49,_4a,_4b){
for(var key in _47){
if(key.match(/([^\w_.$]|__[\w_.$]+__)/)){
continue;
}
var _4d=_47[key];
if(typeof _4d=="undefined"||_4d===document||_4d===window||_4d===dojox.help._toString||_4d===dojox.help._help||_4d===null||(+dojo.isIE&&_4d.tagName)||_4d.__seen__){
continue;
}
var _4e=dojo.isFunction(_4d);
var _4f=dojo.isObject(_4d)&&!dojo.isArray(_4d)&&!_4d.nodeType;
var _50=(_49)?(_49+"."+key):key;
if(_50=="dojo._blockAsync"){
continue;
}
if(!_4d.__name__){
var _51=null;
if(dojo.isString(_4d)){
_51=String;
}else{
if(typeof _4d=="number"){
_51=Number;
}else{
if(typeof _4d=="boolean"){
_51=Boolean;
}
}
}
if(_51){
_4d=_47[key]=new _51(_4d);
}
}
_4d.__seen__=true;
_4d.__name__=_50;
(dojox.help._names[_48]=dojox.help._names[_48]||[]).push(_50);
_4a.push(_4d);
if(!_4e){
dojox.help._overrides.push([_47,key]);
}
if((_4e||_4f)&&_4b){
dojox.help._recursions.push([_4d,_48,_50]);
}
if(_4e){
if(!_4d.__source__){
_4d.__source__=_4d.toString().replace(/^function\b ?/,"function "+_50);
}
if(_4d.toString===Function.prototype.toString){
_4d.toString=dojox.help._toString;
}
}
if(!_4d.help){
_4d.help=dojox.help._help;
}
}
}};
}
