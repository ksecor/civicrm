/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dijit._editor.selection"]){
dojo._hasResource["dijit._editor.selection"]=true;
dojo.provide("dijit._editor.selection");
dojo.mixin(dijit._editor.selection,{getType:function(){
if(dojo.doc.selection){
return dojo.doc.selection.type.toLowerCase();
}else{
var _1="text";
var _2;
try{
_2=dojo.global.getSelection();
}
catch(e){
}
if(_2&&_2.rangeCount==1){
var _3=_2.getRangeAt(0);
if((_3.startContainer==_3.endContainer)&&((_3.endOffset-_3.startOffset)==1)&&(_3.startContainer.nodeType!=3)){
_1="control";
}
}
return _1;
}
},getSelectedText:function(){
if(dojo.doc.selection){
if(dijit._editor.selection.getType()=="control"){
return null;
}
return dojo.doc.selection.createRange().text;
}else{
var _4=dojo.global.getSelection();
if(_4){
return _4.toString();
}
}
return "";
},getSelectedHtml:function(){
if(dojo.doc.selection){
if(dijit._editor.selection.getType()=="control"){
return null;
}
return dojo.doc.selection.createRange().htmlText;
}else{
var _5=dojo.global.getSelection();
if(_5&&_5.rangeCount){
var _6=_5.getRangeAt(0).cloneContents();
var _7=dojo.doc.createElement("div");
_7.appendChild(_6);
return _7.innerHTML;
}
return null;
}
},getSelectedElement:function(){
if(this.getType()=="control"){
if(dojo.doc.selection){
var _8=dojo.doc.selection.createRange();
if(_8&&_8.item){
return dojo.doc.selection.createRange().item(0);
}
}else{
var _9=dojo.global.getSelection();
return _9.anchorNode.childNodes[_9.anchorOffset];
}
}
return null;
},getParentElement:function(){
if(this.getType()=="control"){
var p=this.getSelectedElement();
if(p){
return p.parentNode;
}
}else{
if(dojo.doc.selection){
return dojo.doc.selection.createRange().parentElement();
}else{
var _b=dojo.global.getSelection();
if(_b){
var _c=_b.anchorNode;
while(_c&&(_c.nodeType!=1)){
_c=_c.parentNode;
}
return _c;
}
}
}
return null;
},hasAncestorElement:function(_d){
return this.getAncestorElement.apply(this,arguments)!=null;
},getAncestorElement:function(_e){
var _f=this.getSelectedElement()||this.getParentElement();
return this.getParentOfType(_f,arguments);
},isTag:function(_10,_11){
if(_10&&_10.tagName){
var _12=_10.tagName.toLowerCase();
for(var i=0;i<_11.length;i++){
var _14=String(_11[i]).toLowerCase();
if(_12==_14){
return _14;
}
}
}
return "";
},getParentOfType:function(_15,_16){
while(_15){
if(this.isTag(_15,_16).length){
return _15;
}
_15=_15.parentNode;
}
return null;
},collapse:function(_17){
if(window["getSelection"]){
var _18=dojo.global.getSelection();
if(_18.removeAllRanges){
if(_17){
_18.collapseToStart();
}else{
_18.collapseToEnd();
}
}else{
_18.collapse(_17);
}
}else{
if(dojo.doc.selection){
var _19=dojo.doc.selection.createRange();
_19.collapse(_17);
_19.select();
}
}
},remove:function(){
var _s=dojo.doc.selection;
if(_s){
if(_s.type.toLowerCase()!="none"){
_s.clear();
}
return _s;
}else{
_s=dojo.global.getSelection();
_s.deleteFromDocument();
return _s;
}
},selectElementChildren:function(_1b,_1c){
var _1d=dojo.global;
var _1e=dojo.doc;
_1b=dojo.byId(_1b);
if(_1e.selection&&dojo.body().createTextRange){
var _1f=_1b.ownerDocument.body.createTextRange();
_1f.moveToElementText(_1b);
if(!_1c){
try{
_1f.select();
}
catch(e){
}
}
}else{
if(_1d.getSelection){
var _20=_1d.getSelection();
if(_20.setBaseAndExtent){
_20.setBaseAndExtent(_1b,0,_1b,_1b.innerText.length-1);
}else{
if(_20.selectAllChildren){
_20.selectAllChildren(_1b);
}
}
}
}
},selectElement:function(_21,_22){
var _23,_24=dojo.doc;
_21=dojo.byId(_21);
if(_24.selection&&dojo.body().createTextRange){
try{
_23=dojo.body().createControlRange();
_23.addElement(_21);
if(!_22){
_23.select();
}
}
catch(e){
this.selectElementChildren(_21,_22);
}
}else{
if(dojo.global.getSelection){
var _25=dojo.global.getSelection();
if(_25.removeAllRanges){
_23=_24.createRange();
_23.selectNode(_21);
_25.removeAllRanges();
_25.addRange(_23);
}
}
}
}});
}
