/*
	Copyright (c) 2004-2006, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/community/licensing.shtml
*/


dojo.provide("dojo.date.format");
dojo.require("dojo.date.common");
dojo.require("dojo.date.supplemental");
dojo.require("dojo.lang.array");
dojo.require("dojo.lang.common");
dojo.require("dojo.lang.func");
dojo.require("dojo.string.common");
dojo.require("dojo.i18n.common");
dojo.requireLocalization("dojo.i18n.calendar","gregorian",null,"de,en,es,fi,fr,ROOT,hu,it,ja,ko,nl,pt,pt-br,sv,zh,zh-cn,zh-hk,zh-tw");
dojo.requireLocalization("dojo.i18n.calendar","gregorianExtras",null,"ROOT,ja,zh");
(function(){
dojo.date.format=function(_1,_2){
if(typeof _2=="string"){
dojo.deprecated("dojo.date.format","To format dates with POSIX-style strings, please use dojo.date.strftime instead","0.5");
return dojo.date.strftime(_1,_2);
}
function formatPattern(_3,_4){
return _4.replace(/([a-z])\1*/ig,function(_5){
var s;
var c=_5.charAt(0);
var l=_5.length;
var _9;
var _a=["abbr","wide","narrow"];
switch(c){
case "G":
if(l>3){
dojo.unimplemented("Era format not implemented");
}
s=_b.eras[_3.getFullYear()<0?1:0];
break;
case "y":
s=_3.getFullYear();
switch(l){
case 1:
break;
case 2:
s=String(s).substr(-2);
break;
default:
_9=true;
}
break;
case "Q":
case "q":
s=Math.ceil((_3.getMonth()+1)/3);
switch(l){
case 1:
case 2:
_9=true;
break;
case 3:
case 4:
dojo.unimplemented("Quarter format not implemented");
}
break;
case "M":
case "L":
var m=_3.getMonth();
var _d;
switch(l){
case 1:
case 2:
s=m+1;
_9=true;
break;
case 3:
case 4:
case 5:
_d=_a[l-3];
break;
}
if(_d){
var _e=(c=="L")?"standalone":"format";
var _f=["months",_e,_d].join("-");
s=_b[_f][m];
}
break;
case "w":
var _10=0;
s=dojo.date.getWeekOfYear(_3,_10);
_9=true;
break;
case "d":
s=_3.getDate();
_9=true;
break;
case "D":
s=dojo.date.getDayOfYear(_3);
_9=true;
break;
case "E":
case "e":
case "c":
var d=_3.getDay();
var _d;
switch(l){
case 1:
case 2:
if(c=="e"){
var _12=dojo.date.getFirstDayOfWeek(_2.locale);
d=(d-_12+7)%7;
}
if(c!="c"){
s=d+1;
_9=true;
break;
}
case 3:
case 4:
case 5:
_d=_a[l-3];
break;
}
if(_d){
var _e=(c=="c")?"standalone":"format";
var _f=["days",_e,_d].join("-");
s=_b[_f][d];
}
break;
case "a":
var _13=(_3.getHours()<12)?"am":"pm";
s=_b[_13];
break;
case "h":
case "H":
case "K":
case "k":
var h=_3.getHours();
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
_9=true;
break;
case "m":
s=_3.getMinutes();
_9=true;
break;
case "s":
s=_3.getSeconds();
_9=true;
break;
case "S":
s=Math.round(_3.getMilliseconds()*Math.pow(10,l-3));
break;
case "v":
case "z":
s=dojo.date.getTimezoneName(_3);
if(s){
break;
}
l=4;
case "Z":
var _15=_3.getTimezoneOffset();
var tz=[(_15<=0?"+":"-"),dojo.string.pad(Math.floor(Math.abs(_15)/60),2),dojo.string.pad(Math.abs(_15)%60,2)];
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
dojo.debug(_5+" modifier not yet implemented");
s="?";
break;
default:
dojo.raise("dojo.date.format: invalid pattern char: "+_4);
}
if(_9){
s=dojo.string.pad(s,l);
}
return s;
});
}
_2=_2||{};
var _17=dojo.hostenv.normalizeLocale(_2.locale);
var _18=_2.formatLength||"full";
var _b=dojo.date._getGregorianBundle(_17);
var str=[];
var _1a=dojo.lang.curry(this,formatPattern,_1);
if(_2.selector!="timeOnly"){
var _1b=_2.datePattern||_b["dateFormat-"+_18];
if(_1b){
str.push(_processPattern(_1b,_1a));
}
}
if(_2.selector!="dateOnly"){
var _1c=_2.timePattern||_b["timeFormat-"+_18];
if(_1c){
str.push(_processPattern(_1c,_1a));
}
}
var _1d=str.join(" ");
return _1d;
};
dojo.date.parse=function(_1e,_1f){
_1f=_1f||{};
var _20=dojo.hostenv.normalizeLocale(_1f.locale);
var _21=dojo.date._getGregorianBundle(_20);
var _22=_1f.formatLength||"full";
if(!_1f.selector){
_1f.selector="dateOnly";
}
var _23=_1f.datePattern||_21["dateFormat-"+_22];
var _24=_1f.timePattern||_21["timeFormat-"+_22];
var _25;
if(_1f.selector=="dateOnly"){
_25=_23;
}else{
if(_1f.selector=="timeOnly"){
_25=_24;
}else{
if(_1f.selector=="dateTime"){
_25=_23+" "+_24;
}else{
var msg="dojo.date.parse: Unknown selector param passed: '"+_1f.selector+"'.";
msg+=" Defaulting to date pattern.";
dojo.debug(msg);
_25=_23;
}
}
}
var _27=[];
var _28=_processPattern(_25,dojo.lang.curry(this,_buildDateTimeRE,_27,_21,_1f));
var _29=new RegExp("^"+_28+"$");
var _2a=_29.exec(_1e);
if(!_2a){
return null;
}
var _2b=["abbr","wide","narrow"];
var _2c=new Date(1972,0);
var _2d={};
for(var i=1;i<_2a.length;i++){
var grp=_27[i-1];
var l=grp.length;
var v=_2a[i];
switch(grp.charAt(0)){
case "y":
if(l!=2){
_2c.setFullYear(v);
_2d.year=v;
}else{
if(v<100){
v=Number(v);
var _32=""+new Date().getFullYear();
var _33=_32.substring(0,2)*100;
var _34=Number(_32.substring(2,4));
var _35=Math.min(_34+20,99);
var num=(v<_35)?_33+v:_33-100+v;
_2c.setFullYear(num);
_2d.year=num;
}else{
if(_1f.strict){
return null;
}
_2c.setFullYear(v);
_2d.year=v;
}
}
break;
case "M":
if(l>2){
if(!_1f.strict){
v=v.replace(/\./g,"");
v=v.toLowerCase();
}
var _37=_21["months-format-"+_2b[l-3]].concat();
for(var j=0;j<_37.length;j++){
if(!_1f.strict){
_37[j]=_37[j].toLowerCase();
}
if(v==_37[j]){
_2c.setMonth(j);
_2d.month=j;
break;
}
}
if(j==_37.length){
dojo.debug("dojo.date.parse: Could not parse month name: '"+v+"'.");
return null;
}
}else{
_2c.setMonth(v-1);
_2d.month=v-1;
}
break;
case "E":
case "e":
if(!_1f.strict){
v=v.toLowerCase();
}
var _39=_21["days-format-"+_2b[l-3]].concat();
for(var j=0;j<_39.length;j++){
if(!_1f.strict){
_39[j]=_39[j].toLowerCase();
}
if(v==_39[j]){
break;
}
}
if(j==_39.length){
dojo.debug("dojo.date.parse: Could not parse weekday name: '"+v+"'.");
return null;
}
break;
case "d":
_2c.setDate(v);
_2d.date=v;
break;
case "a":
var am=_1f.am||_21.am;
var pm=_1f.pm||_21.pm;
if(!_1f.strict){
v=v.replace(/\./g,"").toLowerCase();
am=am.replace(/\./g,"").toLowerCase();
pm=pm.replace(/\./g,"").toLowerCase();
}
if(_1f.strict&&v!=am&&v!=pm){
dojo.debug("dojo.date.parse: Could not parse am/pm part.");
return null;
}
var _3c=_2c.getHours();
if(v==pm&&_3c<12){
_2c.setHours(_3c+12);
}else{
if(v==am&&_3c==12){
_2c.setHours(0);
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
_2c.setHours(v);
break;
case "m":
_2c.setMinutes(v);
break;
case "s":
_2c.setSeconds(v);
break;
case "S":
_2c.setMilliseconds(v);
break;
default:
dojo.unimplemented("dojo.date.parse: unsupported pattern char="+grp.charAt(0));
}
}
if(_2d.year&&_2c.getFullYear()!=_2d.year){
dojo.debug("Parsed year: '"+_2c.getFullYear()+"' did not match input year: '"+_2d.year+"'.");
return null;
}
if(_2d.month&&_2c.getMonth()!=_2d.month){
dojo.debug("Parsed month: '"+_2c.getMonth()+"' did not match input month: '"+_2d.month+"'.");
return null;
}
if(_2d.date&&_2c.getDate()!=_2d.date){
dojo.debug("Parsed day of month: '"+_2c.getDate()+"' did not match input day of month: '"+_2d.date+"'.");
return null;
}
return _2c;
};
function _processPattern(_3d,_3e,_3f,_40){
var _41=function(x){
return x;
};
_3e=_3e||_41;
_3f=_3f||_41;
_40=_40||_41;
var _43=_3d.match(/(''|[^'])+/g);
var _44=false;
for(var i=0;i<_43.length;i++){
if(!_43[i]){
_43[i]="";
}else{
_43[i]=(_44?_3f:_3e)(_43[i]);
_44=!_44;
}
}
return _40(_43.join(""));
}
function _buildDateTimeRE(_46,_47,_48,_49){
return _49.replace(/([a-z])\1*/ig,function(_4a){
var s;
var c=_4a.charAt(0);
var l=_4a.length;
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
var am=_48.am||_47.am||"AM";
var pm=_48.pm||_47.pm||"PM";
if(_48.strict){
s=am+"|"+pm;
}else{
s=am;
s+=(am!=am.toLowerCase())?"|"+am.toLowerCase():"";
s+="|";
s+=(pm!=pm.toLowerCase())?pm+"|"+pm.toLowerCase():pm;
}
break;
default:
dojo.unimplemented("parse of date format, pattern="+_49);
}
if(_46){
_46.push(_4a);
}
return "\\s*("+s+")\\s*";
});
}
})();
dojo.date.strftime=function(_50,_51,_52){
var _53=null;
function _(s,n){
return dojo.string.pad(s,n||2,_53||"0");
}
var _56=dojo.date._getGregorianBundle(_52);
function $(_57){
switch(_57){
case "a":
return dojo.date.getDayShortName(_50,_52);
case "A":
return dojo.date.getDayName(_50,_52);
case "b":
case "h":
return dojo.date.getMonthShortName(_50,_52);
case "B":
return dojo.date.getMonthName(_50,_52);
case "c":
return dojo.date.format(_50,{locale:_52});
case "C":
return _(Math.floor(_50.getFullYear()/100));
case "d":
return _(_50.getDate());
case "D":
return $("m")+"/"+$("d")+"/"+$("y");
case "e":
if(_53==null){
_53=" ";
}
return _(_50.getDate());
case "f":
if(_53==null){
_53=" ";
}
return _(_50.getMonth()+1);
case "g":
break;
case "G":
dojo.unimplemented("unimplemented modifier 'G'");
break;
case "F":
return $("Y")+"-"+$("m")+"-"+$("d");
case "H":
return _(_50.getHours());
case "I":
return _(_50.getHours()%12||12);
case "j":
return _(dojo.date.getDayOfYear(_50),3);
case "k":
if(_53==null){
_53=" ";
}
return _(_50.getHours());
case "l":
if(_53==null){
_53=" ";
}
return _(_50.getHours()%12||12);
case "m":
return _(_50.getMonth()+1);
case "M":
return _(_50.getMinutes());
case "n":
return "\n";
case "p":
return _56[_50.getHours()<12?"am":"pm"];
case "r":
return $("I")+":"+$("M")+":"+$("S")+" "+$("p");
case "R":
return $("H")+":"+$("M");
case "S":
return _(_50.getSeconds());
case "t":
return "\t";
case "T":
return $("H")+":"+$("M")+":"+$("S");
case "u":
return String(_50.getDay()||7);
case "U":
return _(dojo.date.getWeekOfYear(_50));
case "V":
return _(dojo.date.getIsoWeekOfYear(_50));
case "W":
return _(dojo.date.getWeekOfYear(_50,1));
case "w":
return String(_50.getDay());
case "x":
return dojo.date.format(_50,{selector:"dateOnly",locale:_52});
case "X":
return dojo.date.format(_50,{selector:"timeOnly",locale:_52});
case "y":
return _(_50.getFullYear()%100);
case "Y":
return String(_50.getFullYear());
case "z":
var _58=_50.getTimezoneOffset();
return (_58>0?"-":"+")+_(Math.floor(Math.abs(_58)/60))+":"+_(Math.abs(_58)%60);
case "Z":
return dojo.date.getTimezoneName(_50);
case "%":
return "%";
}
}
var _59="";
var i=0;
var _5b=0;
var _5c=null;
while((_5b=_51.indexOf("%",i))!=-1){
_59+=_51.substring(i,_5b++);
switch(_51.charAt(_5b++)){
case "_":
_53=" ";
break;
case "-":
_53="";
break;
case "0":
_53="0";
break;
case "^":
_5c="upper";
break;
case "*":
_5c="lower";
break;
case "#":
_5c="swap";
break;
default:
_53=null;
_5b--;
break;
}
var _5d=$(_51.charAt(_5b++));
switch(_5c){
case "upper":
_5d=_5d.toUpperCase();
break;
case "lower":
_5d=_5d.toLowerCase();
break;
case "swap":
var _5e=_5d.toLowerCase();
var _5f="";
var j=0;
var ch="";
while(j<_5d.length){
ch=_5d.charAt(j);
_5f+=(ch==_5e.charAt(j))?ch.toUpperCase():ch.toLowerCase();
j++;
}
_5d=_5f;
break;
default:
break;
}
_5c=null;
_59+=_5d;
i=_5b;
}
_59+=_51.substring(i);
return _59;
};
(function(){
var _62=[];
dojo.date.addCustomFormats=function(_63,_64){
_62.push({pkg:_63,name:_64});
};
dojo.date._getGregorianBundle=function(_65){
var _66={};
dojo.lang.forEach(_62,function(_67){
var _68=dojo.i18n.getLocalization(_67.pkg,_67.name,_65);
_66=dojo.lang.mixin(_66,_68);
},this);
return _66;
};
})();
dojo.date.addCustomFormats("dojo.i18n.calendar","gregorian");
dojo.date.addCustomFormats("dojo.i18n.calendar","gregorianExtras");
dojo.date.getNames=function(_69,_6a,use,_6c){
var _6d;
var _6e=dojo.date._getGregorianBundle(_6c);
var _6f=[_69,use,_6a];
if(use=="standAlone"){
_6d=_6e[_6f.join("-")];
}
_6f[1]="format";
return (_6d||_6e[_6f.join("-")]).concat();
};
dojo.date.getDayName=function(_70,_71){
return dojo.date.getNames("days","wide","format",_71)[_70.getDay()];
};
dojo.date.getDayShortName=function(_72,_73){
return dojo.date.getNames("days","abbr","format",_73)[_72.getDay()];
};
dojo.date.getMonthName=function(_74,_75){
return dojo.date.getNames("months","wide","format",_75)[_74.getMonth()];
};
dojo.date.getMonthShortName=function(_76,_77){
return dojo.date.getNames("months","abbr","format",_77)[_76.getMonth()];
};
dojo.date.toRelativeString=function(_78){
var now=new Date();
var _7a=(now-_78)/1000;
var end=" ago";
var _7c=false;
if(_7a<0){
_7c=true;
end=" from now";
_7a=-_7a;
}
if(_7a<60){
_7a=Math.round(_7a);
return _7a+" second"+(_7a==1?"":"s")+end;
}
if(_7a<60*60){
_7a=Math.round(_7a/60);
return _7a+" minute"+(_7a==1?"":"s")+end;
}
if(_7a<60*60*24){
_7a=Math.round(_7a/3600);
return _7a+" hour"+(_7a==1?"":"s")+end;
}
if(_7a<60*60*24*7){
_7a=Math.round(_7a/(3600*24));
if(_7a==1){
return _7c?"Tomorrow":"Yesterday";
}else{
return _7a+" days"+end;
}
}
return dojo.date.format(_78);
};
dojo.date.toSql=function(_7d,_7e){
return dojo.date.strftime(_7d,"%F"+!_7e?" %T":"");
};
dojo.date.fromSql=function(_7f){
var _80=_7f.split(/[\- :]/g);
while(_80.length<6){
_80.push(0);
}
return new Date(_80[0],(parseInt(_80[1],10)-1),_80[2],_80[3],_80[4],_80[5]);
};
