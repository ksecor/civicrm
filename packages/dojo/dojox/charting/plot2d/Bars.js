/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojox.charting.plot2d.Bars"]){
dojo._hasResource["dojox.charting.plot2d.Bars"]=true;
dojo.provide("dojox.charting.plot2d.Bars");
dojo.require("dojox.charting.plot2d.common");
dojo.require("dojox.charting.plot2d.Base");
dojo.require("dojox.lang.utils");
dojo.require("dojox.lang.functional");
dojo.require("dojox.lang.functional.reversed");
(function(){
var df=dojox.lang.functional,du=dojox.lang.utils,dc=dojox.charting.plot2d.common,_4=df.lambda("item.purgeGroup()");
dojo.declare("dojox.charting.plot2d.Bars",dojox.charting.plot2d.Base,{defaultParams:{hAxis:"x",vAxis:"y",gap:0,shadows:null},optionalParams:{},constructor:function(_5,_6){
this.opt=dojo.clone(this.defaultParams);
du.updateWithObject(this.opt,_6);
this.series=[];
this.hAxis=this.opt.hAxis;
this.vAxis=this.opt.vAxis;
},calculateAxes:function(_7){
var _8=dc.collectSimpleStats(this.series),t;
_8.hmin-=0.5;
_8.hmax+=0.5;
t=_8.hmin,_8.hmin=_8.vmin,_8.vmin=t;
t=_8.hmax,_8.hmax=_8.vmax,_8.vmax=t;
this._calc(_7,_8);
return this;
},render:function(_a,_b){
if(this.dirty){
dojo.forEach(this.series,_4);
this.cleanGroup();
var s=this.group;
df.forEachRev(this.series,function(_d){
_d.cleanGroup(s);
});
}
var t=this.chart.theme,_f,_10,_11,f,gap=this.opt.gap<this._vScaler.scale/3?this.opt.gap:0;
for(var i=this.series.length-1;i>=0;--i){
var run=this.series[i];
if(!this.dirty&&!run.dirty){
continue;
}
run.cleanGroup();
var s=run.group;
if(!run.fill||!run.stroke){
_f=run.dyn.color=new dojo.Color(t.next("color"));
}
_10=run.stroke?run.stroke:dc.augmentStroke(t.series.stroke,_f);
_11=run.fill?run.fill:dc.augmentFill(t.series.fill,_f);
var _16=Math.max(0,this._hScaler.bounds.lower),_17=_b.l+this._hScaler.scale*(_16-this._hScaler.bounds.lower),_18=_a.height-_b.b-this._vScaler.scale*(1.5-this._vScaler.bounds.lower)+gap;
for(var j=0;j<run.data.length;++j){
var v=run.data[j],_1b=this._hScaler.scale*(v-_16),_1c=this._vScaler.scale-2*gap,w=Math.abs(_1b);
if(w>=1&&_1c>=1){
var _1e=s.createRect({x:_17+(_1b<0?_1b:0),y:_18-this._vScaler.scale*j,width:w,height:_1c}).setFill(_11).setStroke(_10);
run.dyn.fill=_1e.getFill();
run.dyn.stroke=_1e.getStroke();
}
}
run.dirty=false;
}
this.dirty=false;
return this;
}});
})();
}
