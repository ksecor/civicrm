/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojo.fx"]){
dojo._hasResource["dojo.fx"]=true;
dojo.provide("dojo.fx");
dojo.provide("dojo.fx.Toggler");
(function(){
var _1={_fire:function(_2,_3){
if(this[_2]){
this[_2].apply(this,_3||[]);
}
return this;
}};
var _4=function(_5){
this._index=-1;
this._animations=_5||[];
this._current=this._onAnimateCtx=this._onEndCtx=null;
this.duration=0;
dojo.forEach(this._animations,function(a){
this.duration+=a.duration;
if(a.delay){
this.duration+=a.delay;
}
},this);
};
dojo.extend(_4,{_onAnimate:function(){
this._fire("onAnimate",arguments);
},_onEnd:function(){
dojo.disconnect(this._onAnimateCtx);
dojo.disconnect(this._onEndCtx);
this._onAnimateCtx=this._onEndCtx=null;
if(this._index+1==this._animations.length){
this._fire("onEnd");
}else{
this._current=this._animations[++this._index];
this._onAnimateCtx=dojo.connect(this._current,"onAnimate",this,"_onAnimate");
this._onEndCtx=dojo.connect(this._current,"onEnd",this,"_onEnd");
this._current.play(0,true);
}
},play:function(_7,_8){
if(!this._current){
this._current=this._animations[this._index=0];
}
if(!_8&&this._current.status()=="playing"){
return this;
}
var _9=dojo.connect(this._current,"beforeBegin",this,function(){
this._fire("beforeBegin");
}),_a=dojo.connect(this._current,"onBegin",this,function(_b){
this._fire("onBegin",arguments);
}),_c=dojo.connect(this._current,"onPlay",this,function(_d){
this._fire("onPlay",arguments);
dojo.disconnect(_9);
dojo.disconnect(_a);
dojo.disconnect(_c);
});
if(this._onAnimateCtx){
dojo.disconnect(this._onAnimateCtx);
}
this._onAnimateCtx=dojo.connect(this._current,"onAnimate",this,"_onAnimate");
if(this._onEndCtx){
dojo.disconnect(this._onEndCtx);
}
this._onEndCtx=dojo.connect(this._current,"onEnd",this,"_onEnd");
this._current.play.apply(this._current,arguments);
return this;
},pause:function(){
if(this._current){
var e=dojo.connect(this._current,"onPause",this,function(_f){
this._fire("onPause",arguments);
dojo.disconnect(e);
});
this._current.pause();
}
return this;
},gotoPercent:function(_10,_11){
this.pause();
var _12=this.duration*_10;
this._current=null;
dojo.some(this._animations,function(a){
if(a.duration<=_12){
this._current=a;
return true;
}
_12-=a.duration;
return false;
});
if(this._current){
this._current.gotoPercent(_12/_current.duration,_11);
}
return this;
},stop:function(_14){
if(this._current){
if(_14){
for(;this._index+1<this._animations.length;++this._index){
this._animations[this._index].stop(true);
}
this._current=this._animations[this._index];
}
var e=dojo.connect(this._current,"onStop",this,function(arg){
this._fire("onStop",arguments);
dojo.disconnect(e);
});
this._current.stop();
}
return this;
},status:function(){
return this._current?this._current.status():"stopped";
},destroy:function(){
if(this._onAnimateCtx){
dojo.disconnect(this._onAnimateCtx);
}
if(this._onEndCtx){
dojo.disconnect(this._onEndCtx);
}
}});
dojo.extend(_4,_1);
dojo.fx.chain=function(_17){
return new _4(_17);
};
var _18=function(_19){
this._animations=_19||[];
this._connects=[];
this._finished=0;
this.duration=0;
dojo.forEach(_19,function(a){
var _1b=a.duration;
if(a.delay){
_1b+=a.delay;
}
if(this.duration<_1b){
this.duration=_1b;
}
this._connects.push(dojo.connect(a,"onEnd",this,"_onEnd"));
},this);
this._pseudoAnimation=new dojo._Animation({curve:[0,1],duration:this.duration});
dojo.forEach(["beforeBegin","onBegin","onPlay","onAnimate","onPause","onStop"],function(evt){
this._connects.push(dojo.connect(this._pseudoAnimation,evt,dojo.hitch(this,"_fire",evt)));
},this);
};
dojo.extend(_18,{_doAction:function(_1d,_1e){
dojo.forEach(this._animations,function(a){
a[_1d].apply(a,_1e);
});
return this;
},_onEnd:function(){
if(++this._finished==this._animations.length){
this._fire("onEnd");
}
},_call:function(_20,_21){
var t=this._pseudoAnimation;
t[_20].apply(t,_21);
},play:function(_23,_24){
this._finished=0;
this._doAction("play",arguments);
this._call("play",arguments);
return this;
},pause:function(){
this._doAction("pause",arguments);
this._call("pause",arguments);
return this;
},gotoPercent:function(_25,_26){
var ms=this.duration*_25;
dojo.forEach(this._animations,function(a){
a.gotoPercent(a.duration<ms?1:(ms/a.duration),_26);
});
this._call("gotoProcent",arguments);
return this;
},stop:function(_29){
this._doAction("stop",arguments);
this._call("stop",arguments);
return this;
},status:function(){
return this._pseudoAnimation.status();
},destroy:function(){
dojo.forEach(this._connects,dojo.disconnect);
}});
dojo.extend(_18,_1);
dojo.fx.combine=function(_2a){
return new _18(_2a);
};
})();
dojo.declare("dojo.fx.Toggler",null,{constructor:function(_2b){
var _t=this;
dojo.mixin(_t,_2b);
_t.node=_2b.node;
_t._showArgs=dojo.mixin({},_2b);
_t._showArgs.node=_t.node;
_t._showArgs.duration=_t.showDuration;
_t.showAnim=_t.showFunc(_t._showArgs);
_t._hideArgs=dojo.mixin({},_2b);
_t._hideArgs.node=_t.node;
_t._hideArgs.duration=_t.hideDuration;
_t.hideAnim=_t.hideFunc(_t._hideArgs);
dojo.connect(_t.showAnim,"beforeBegin",dojo.hitch(_t.hideAnim,"stop",true));
dojo.connect(_t.hideAnim,"beforeBegin",dojo.hitch(_t.showAnim,"stop",true));
},node:null,showFunc:dojo.fadeIn,hideFunc:dojo.fadeOut,showDuration:200,hideDuration:200,show:function(_2d){
return this.showAnim.play(_2d||0);
},hide:function(_2e){
return this.hideAnim.play(_2e||0);
}});
dojo.fx.wipeIn=function(_2f){
_2f.node=dojo.byId(_2f.node);
var _30=_2f.node,s=_30.style;
var _32=dojo.animateProperty(dojo.mixin({properties:{height:{start:function(){
s.overflow="hidden";
if(s.visibility=="hidden"||s.display=="none"){
s.height="1px";
s.display="";
s.visibility="";
return 1;
}else{
var _33=dojo.style(_30,"height");
return Math.max(_33,1);
}
},end:function(){
return _30.scrollHeight;
}}}},_2f));
dojo.connect(_32,"onEnd",function(){
s.height="auto";
});
return _32;
};
dojo.fx.wipeOut=function(_34){
var _35=_34.node=dojo.byId(_34.node);
var s=_35.style;
var _37=dojo.animateProperty(dojo.mixin({properties:{height:{end:1}}},_34));
dojo.connect(_37,"beforeBegin",function(){
s.overflow="hidden";
s.display="";
});
dojo.connect(_37,"onEnd",function(){
s.height="auto";
s.display="none";
});
return _37;
};
dojo.fx.slideTo=function(_38){
var _39=(_38.node=dojo.byId(_38.node));
var top=null;
var _3b=null;
var _3c=(function(n){
return function(){
var cs=dojo.getComputedStyle(n);
var pos=cs.position;
top=(pos=="absolute"?n.offsetTop:parseInt(cs.top)||0);
_3b=(pos=="absolute"?n.offsetLeft:parseInt(cs.left)||0);
if(pos!="absolute"&&pos!="relative"){
var ret=dojo.coords(n,true);
top=ret.y;
_3b=ret.x;
n.style.position="absolute";
n.style.top=top+"px";
n.style.left=_3b+"px";
}
};
})(_39);
_3c();
var _41=dojo.animateProperty(dojo.mixin({properties:{top:{end:_38.top||0},left:{end:_38.left||0}}},_38));
dojo.connect(_41,"beforeBegin",_41,_3c);
return _41;
};
}
