/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["civicrm.Tooltip"]){
dojo._hasResource["civicrm.Tooltip"]=true;
dojo.provide("civicrm.Tooltip");
dojo.require("dijit.Tooltip");
dojo.require("dijit.Dialog");
dojo.require("dojo.parser");
dojo.require("dijit.layout.TabContainer");
dojo.require("dijit.form.TextBox");
dojo.require("dijit.form.Button");
function adjustDimensions(_1,_2,_3){
var _4=_1.scrollHeight;
var _5=_1.scrollWidth;
console.log("height and width are "+_4+" and "+_5+" for "+_1+" with id "+_1.id);
if(_4>_3){
console.log("resizing to max height "+_3);
_1.style.height=_3+"px";
_1.style.overflow="auto";
}
if(_5>_2){
console.log("resizing to max width "+_2);
_1.style.width=_2+"px";
_1.style.overflow="auto";
}
};
function getFirstDivChild(_6){
var _7=null;
dojo.forEach(_6.childNodes,function(_8){
if(_8.nodeName=="DIV"){
if(_7==null){
_7=_8;
}
}
});
return _7;
};
var civicrmLoaders=[];
var civicrmTooltipLoaded=false;
civicrmAddOnLoad=function(f){
if(civicrmTooltipLoaded){
console.log("Calling the function",f);
f();
}else{
console.log("Pushing the function to queue",f);
civicrmLoaders.push(f);
}
};
dojo.addOnLoad(function(){
console.log("Will load the civicrm.Tooltip declaration now");
});
dojo.addOnLoad(function(){
dojo.provide("civicrm.Tooltip");
dojo.provide("civicrm.MasterTooltip");
dojo.declare("civicrm.MasterTooltip",dijit._MasterTooltip,{duration:50,parentTooltip:null,templateString:"<div id=\"dijitTooltip\" class=\"dijitTooltip dijitTooltipBelow\">\n\t<div class=\"dijitTooltipContainer dijitTooltipContents civicrmTooltipContainer\" id=\"dijitTooltipContainer\" dojoAttachPoint=\"containerNode\" waiRole='alert'></div>\n\t<div class=\"dijitTooltipConnector civicrmTooltipConnector\"></div>\n</div>\n",show:function(_a){
if(this.fadeOut.status()=="playing"){
this._onDeck=arguments;
return;
}
this.containerNode.innerHTML=this.parentTooltip.label;
this.domNode.style.top=(this.domNode.offsetTop+1)+"px";
var _b={"BL":"TL"};
var _c=dijit.placeOnScreen(this.domNode,{x:this.parentTooltip.mouseX,y:this.parentTooltip.mouseY},["TL"]);
this.domNode.className="dijitTooltip dijitTooltipBelow";
dojo.style(this.domNode,"opacity",0);
this.fadeIn.play();
this.isShowingNow=true;
var _d=getFirstDivChild(getFirstDivChild(this.domNode));
adjustDimensions(_d,this.parentTooltip.maxWidth,this.parentTooltip.maxHeight);
},refresh:function(){
this.containerNode.innerHTML=this.parentTooltip.label;
if(this.isShowingNow==true){
var _e=getFirstDivChild(getFirstDivChild(this.domNode));
adjustDimensions(_e,this.parentTooltip.maxWidth,this.parentTooltip.maxHeight);
}
},postCreate:function(){
dojo.body().appendChild(this.domNode);
this.bgIframe=new dijit.BackgroundIframe(this.domNode);
this.fadeIn=dojo.fadeIn({node:this.domNode,duration:this.duration,onEnd:dojo.hitch(this,"_onShow")});
this.fadeOut=dojo.fadeOut({node:this.domNode,duration:this.duration,onEnd:dojo.hitch(this,"_onHide")});
this.connect(this.domNode,"onmouseout","_onMouseOut");
},_onMouseOut:function(e){
this.parentTooltip._onMouseOut(e);
}});
dojo.declare("civicrm.Tooltip",dijit.Tooltip,{hasLoaded:false,href:"",maxWidth:400,maxHeight:100,mouseX:0,mouseY:0,label:"<div><img src=\"loading.gif\"> Loading...</div>",masterTooltip:null,loadContent:function(){
if(this.hasLoaded==false){
this.hasLoaded=true;
dojo.xhrGet({url:this.href,parentTooltip:this,load:function(_10,_11){
console.log("data received from ",this.url);
console.log("parent is "+this.parentTooltip);
this.parentTooltip.label=_10;
if(this.parentTooltip.isShowingNow){
this.parentTooltip.masterTooltip.refresh();
}
},handleAs:"text"});
}
},open:function(){
if(this.masterTooltip==null){
this.masterTooltip=new civicrm.MasterTooltip({parentTooltip:this});
}
this.loadContent();
if(this.isShowingNow){
return;
}
if(this._showTimer){
clearTimeout(this._showTimer);
delete this._showTimer;
}
this.masterTooltip.show(this._connectNode);
this.isShowingNow=true;
},close:function(){
if(!this.isShowingNow){
return;
}
if(this.masterTooltip!=null){
this.masterTooltip.hide();
}
this.isShowingNow=false;
},_onMouseOut:function(e){
var _13=e.pageX;
var _14=e.pageY;
var _15=Math.abs(this.mouseX-e.pageX)+Math.abs(this.mouseY-e.pageY);
if(_15<6){
return;
}
if(dojo.isDescendant(e.relatedTarget,this._connectNode)){
return;
}
if(this.masterTooltip!=null){
var c=dojo.coords(this.masterTooltip.domNode);
console.log("Tooltip coordinates",c,"Curr",_13,_14,"MouseXY",this.mouseX,this.mouseY);
if(this.mouseX<_13&&this.mouseX+c.w>_13&&this.mouseY<_14&&this.mouseY+c.h>_14){
return;
}
}
this._onUnHover(e);
},_onHover:function(e){
if(this._hover){
return;
}
this._hover=true;
this.mouseX=e.pageX;
this.mouseY=e.pageY;
console.log("Mouse X,Y is "+this.mouseX+", "+this.mouseY);
if(!this.isShowingNow&&!this._showTimer){
this._showTimer=setTimeout(dojo.hitch(this,"open"),this.showDelay);
}
},_onMouseOver:function(e){
this._onHover(e);
}});
civicrmTooltipLoaded=true;
dojo.forEach(civicrmLoaders,function(f){
f();
});
});
console.log("javascript loaded");
}
