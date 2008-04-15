/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dijit.form.FilteringSelect"]){
dojo._hasResource["dijit.form.FilteringSelect"]=true;
dojo.provide("dijit.form.FilteringSelect");
dojo.require("dijit.form.ComboBox");
dojo.declare("dijit.form.FilteringSelect",[dijit.form.MappedTextBox,dijit.form.ComboBoxMixin],{labelAttr:"",labelType:"text",_isvalid:true,_lastDisplayedValue:"",isValid:function(){
return this._isvalid;
},_callbackSetLabel:function(_1,_2,_3){
if(_2&&_2.query[this.searchAttr]!=this._lastQuery){
return;
}
if(!_1.length){
if(!this._focused){
this.valueNode.value="";
}
dijit.form.TextBox.superclass.setValue.call(this,undefined,!this._focused);
this._isvalid=false;
this.validate(this._focused);
}else{
this._setValueFromItem(_1[0],_3);
}
},_openResultList:function(_4,_5){
if(_5.query[this.searchAttr]!=this._lastQuery){
return;
}
this._isvalid=_4.length!=0;
this.validate(true);
dijit.form.ComboBoxMixin.prototype._openResultList.apply(this,arguments);
},getValue:function(){
return this.valueNode.value;
},_getValueField:function(){
return "value";
},_setValue:function(_6,_7,_8){
this.valueNode.value=_6;
dijit.form.FilteringSelect.superclass.setValue.call(this,_6,_8,_7);
this._lastDisplayedValue=_7;
},setValue:function(_9,_a){
var _b=this;
var _c=function(_d,_e){
if(_d){
if(_b.store.isItemLoaded(_d)){
_b._callbackSetLabel([_d],undefined,_e);
}else{
_b.store.loadItem({item:_d,onItem:function(_f,_10){
_b._callbackSetLabel(_f,_10,_e);
}});
}
}else{
_b._isvalid=false;
_b.validate(false);
}
};
this.store.fetchItemByIdentity({identity:_9,onItem:function(_11){
_c(_11,_a);
}});
},_setValueFromItem:function(_12,_13){
this._isvalid=true;
this._setValue(this.store.getIdentity(_12),this.labelFunc(_12,this.store),_13);
},labelFunc:function(_14,_15){
return _15.getValue(_14,this.searchAttr);
},_doSelect:function(tgt){
this.item=tgt.item;
this._setValueFromItem(tgt.item,true);
},setDisplayedValue:function(_17,_18){
if(this.store){
var _19=dojo.clone(this.query);
this._lastQuery=_19[this.searchAttr]=_17;
this.textbox.value=_17;
this._lastDisplayedValue=_17;
var _1a=this;
this.store.fetch({query:_19,queryOptions:{ignoreCase:this.ignoreCase,deep:true},onComplete:function(_1b,_1c){
dojo.hitch(_1a,"_callbackSetLabel")(_1b,_1c,_18);
},onError:function(_1d){
console.error("dijit.form.FilteringSelect: "+_1d);
dojo.hitch(_1a,"_setValue")(undefined,_17,false);
}});
}
},_getMenuLabelFromItem:function(_1e){
if(this.labelAttr){
return {html:this.labelType=="html",label:this.store.getValue(_1e,this.labelAttr)};
}else{
return dijit.form.ComboBoxMixin.prototype._getMenuLabelFromItem.apply(this,arguments);
}
},postMixInProperties:function(){
dijit.form.ComboBoxMixin.prototype.postMixInProperties.apply(this,arguments);
dijit.form.MappedTextBox.prototype.postMixInProperties.apply(this,arguments);
},postCreate:function(){
dijit.form.ComboBoxMixin.prototype._postCreate.apply(this,arguments);
dijit.form.MappedTextBox.prototype.postCreate.apply(this,arguments);
},setAttribute:function(_1f,_20){
dijit.form.MappedTextBox.prototype.setAttribute.apply(this,arguments);
dijit.form.ComboBoxMixin.prototype._setAttribute.apply(this,arguments);
},undo:function(){
this.setDisplayedValue(this._lastDisplayedValue);
},_valueChanged:function(){
return this.getDisplayedValue()!=this._lastDisplayedValue;
}});
}
