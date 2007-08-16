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

if(!dojo._hasResource["dojo.dnd.common"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.dnd.common"] = true;
dojo.provide("dojo.dnd.common");

dojo.dnd._copyKey = navigator.appVersion.indexOf("Macintosh") < 0 ? "ctrlKey" : "metaKey";

dojo.dnd.getCopyKeyState = function(e) {
	// summary: abstracts away the difference between selection on Mac and PC,
	//	and returns the state of the "copy" key to be pressed.
	// e: Event: mouse event
	return e[dojo.dnd._copyKey];	// Boolean
};

dojo.dnd._uniqueId = 0;
dojo.dnd.getUniqueId = function(){
	// summary: returns a unique string for use with any DOM element
	var id;
	do{
		id = "dojoUnique" + (++dojo.dnd._uniqueId);
	}while(dojo.byId(id));
	return id;
};

dojo.dnd._empty = {};

dojo.dnd.isFormElement = function(/*Event*/ e){
	// summary: returns true, if user clicked on a form element
	var t = e.target;
	if(t.nodeType == 3 /*TEXT_NODE*/){
		t = t.parentNode;
	}
	return " button textarea input select option ".indexOf(" " + t.tagName.toLowerCase() + " ") >= 0;	// Boolean
};

}

if(!dojo._hasResource["dojo.dnd.autoscroll"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.dnd.autoscroll"] = true;
dojo.provide("dojo.dnd.autoscroll");

dojo.dnd.getViewport = function(){
	// summary: returns a viewport size (visible part of the window)
	var d = dojo.doc, dd = d.documentElement, w = window, b = dojo.body();
	if(dojo.isMozilla){
		return {w: dd.clientWidth, h: w.innerHeight};	// Object
	}else if(!dojo.isOpera && w.innerWidth){
		return {w: w.innerWidth, h: w.innerHeight};		// Object
	}else if (!dojo.isOpera && dd && dd.clientWidth){
		return {w: dd.clientWidth, h: dd.clientHeight};	// Object
	}else if (b.clientWidth){
		return {w: b.clientWidth, h: b.clientHeight};	// Object
	}
	return null;	// Object
};

dojo.dnd.V_TRIGGER_AUTOSCROLL = 32;
dojo.dnd.H_TRIGGER_AUTOSCROLL = 32;

dojo.dnd.V_AUTOSCROLL_VALUE = 16;
dojo.dnd.H_AUTOSCROLL_VALUE = 16;

dojo.dnd.autoScroll = function(e){
	// summary: a handler for onmousemove event, which scrolls the window, if necesary
	// e: Event: onmousemove event
	var v = dojo.dnd.getViewport(), dx = 0, dy = 0;
	if(e.clientX < dojo.dnd.H_TRIGGER_AUTOSCROLL){
		dx = -dojo.dnd.H_AUTOSCROLL_VALUE;
	}else if(e.clientX > v.w - dojo.dnd.H_TRIGGER_AUTOSCROLL){
		dx = dojo.dnd.H_AUTOSCROLL_VALUE;
	}
	if(e.clientY < dojo.dnd.V_TRIGGER_AUTOSCROLL){
		dy = -dojo.dnd.V_AUTOSCROLL_VALUE;
	}else if(e.clientY > v.h - dojo.dnd.V_TRIGGER_AUTOSCROLL){
		dy = dojo.dnd.V_AUTOSCROLL_VALUE;
	}
	window.scrollBy(dx, dy);
};

dojo.dnd._validNodes = {"div": 1, "p": 1, "td": 1};
dojo.dnd._validOverflow = {"auto": 1, "scroll": 1};

dojo.dnd.autoScrollNodes = function(e){
	// summary: a handler for onmousemove event, which scrolls the first avaialble Dom element,
	//	it falls back to dojo.dnd.autoScroll()
	// e: Event: onmousemove event
	for(var n = e.target; n;){
		if(n.nodeType == 1 && (n.tagName.toLowerCase() in dojo.dnd._validNodes)){
			var s = dojo.getComputedStyle(n);
			if(s.overflow.toLowerCase() in dojo.dnd._validOverflow){
				var b = dojo._getContentBox(n, s), t = dojo._abs(n, true);
				console.debug(b.l, b.t, t.x, t.y, n.scrollLeft, n.scrollTop);
				b.l += t.x + n.scrollLeft;
				b.t += t.y + n.scrollTop;
				var w = Math.min(dojo.dnd.H_TRIGGER_AUTOSCROLL, b.w / 2), 
					h = Math.min(dojo.dnd.V_TRIGGER_AUTOSCROLL, b.h / 2),
					rx = e.pageX - b.l, ry = e.pageY - b.t, dx = 0, dy = 0;
				if(rx > 0 && rx < b.w){
					if(rx < w){
						dx = -dojo.dnd.H_AUTOSCROLL_VALUE;
					}else if(rx > b.w - w){
						dx = dojo.dnd.H_AUTOSCROLL_VALUE;
					}
				}
				//console.debug("ry =", ry, "b.h =", b.h, "h =", h);
				if(ry > 0 && ry < b.h){
					if(ry < h){
						dy = -dojo.dnd.V_AUTOSCROLL_VALUE;
					}else if(ry > b.h - h){
						dy = dojo.dnd.V_AUTOSCROLL_VALUE;
					}
				}
				var oldLeft = n.scrollLeft, oldTop = n.scrollTop;
				n.scrollLeft = n.scrollLeft + dx;
				n.scrollTop  = n.scrollTop  + dy;
				if(dx || dy) console.debug(oldLeft + ", " + oldTop + "\n" + dx + ", " + dy + "\n" + n.scrollLeft + ", " + n.scrollTop);
				if(oldLeft != n.scrollLeft || oldTop != n.scrollTop){ return; }
			}
		}
		try{
			n = n.parentNode;
		}catch(x){
			n = null;
		}
	}
	dojo.dnd.autoScroll(e);
};

}

if(!dojo._hasResource["dojo.dnd.move"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.dnd.move"] = true;
dojo.provide("dojo.dnd.move");




dojo.dnd.Mover = function(node, e){
	// summary: an object, which makes a node follow the mouse, 
	//	used as a default mover, and as a base class for custom movers
	// node: Node: a node (or node's id) to be moved
	// e: Event: a mouse event, which started the move;
	//	only pageX and pageY properties are used
	this.node = dojo.byId(node);
	this.marginBox = {l: e.pageX, t: e.pageY};
	var d = node.ownerDocument, firstEvent = dojo.connect(d, "onmousemove", this, "onFirstMove");
	this.events = [
		dojo.connect(d, "onmousemove", this, "onMouseMove"),
		dojo.connect(d, "onmouseup",   this, "destroy"),
		// cancel text selection and text dragging
		dojo.connect(d, "ondragstart",   dojo, "stopEvent"),
		dojo.connect(d, "onselectstart", dojo, "stopEvent"),
		firstEvent
	];
	// set globals to indicate that move has started
	dojo.publish("/dnd/move/start", [this.node]);
	dojo.addClass(dojo.body(), "dojoMove"); 
	dojo.addClass(this.node, "dojoMoveItem"); 
};

dojo.extend(dojo.dnd.Mover, {
	// mouse event processors
	onMouseMove: function(e){
		// summary: event processor for onmousemove
		// e: Event: mouse event
		dojo.dnd.autoScroll(e);
		var m = this.marginBox;
		dojo.marginBox(this.node, {l: m.l + e.pageX, t: m.t + e.pageY});
	},
	// utilities
	onFirstMove: function(){
		// summary: makes the node absolute; it is meant to be called only once
		this.node.style.position = "absolute";	// enforcing the absolute mode
		var m = dojo.marginBox(this.node);
		m.l -= this.marginBox.l;
		m.t -= this.marginBox.t;
		this.marginBox = m;
		dojo.disconnect(this.events.pop());
	},
	destroy: function(){
		// summary: stops the move, deletes all references, so the object can be garbage-collected
		dojo.forEach(this.events, dojo.disconnect);
		// undo global settings
		dojo.publish("/dnd/move/stop", [this.node]);
		dojo.removeClass(dojo.body(), "dojoMove");
		dojo.removeClass(this.node, "dojoMoveItem");
		// destroy objects
		this.events = this.node = null;
	}
});

dojo.dnd.Moveable = function(node, params){
	// summary: an object, which makes a node moveable
	// node: Node: a node (or node's id) to be moved
	// params: Object: an optional object with additional parameters;
	//	following parameters are recognized:
	//		handle: Node: a node (or node's id), which is used as a mouse handle
	//			if omitted, the node itself is used as a handle
	//		delay: Number: delay move by this number of pixels
	//		skip: Boolean: skip move of form elements
	//		mover: Object: a constructor of custom Mover
	this.node = dojo.byId(node);
	this.handle = (params && params.handle) ? dojo.byId(params.handle) : null;
	if(!this.handle){ this.handle = this.node; }
	this.delay = (params && params.delay > 0) ? params.delay : 0;
	this.skip  = params && params.skip;
	this.mover = (params && params.mover) ? params.mover : dojo.dnd.Mover;
	this.events = [
		dojo.connect(this.handle, "onmousedown", this, "onMouseDown"),
		// cancel text selection and text dragging
		dojo.connect(this.handle, "ondragstart",   dojo, "stopEvent"),
		dojo.connect(this.handle, "onselectstart", dojo, "stopEvent")
	];
};

dojo.extend(dojo.dnd.Moveable, {
	// object attributes (for markup)
	handle: "",
	delay: 0,
	skip: false,
	
	// markup methods
	markupFactory: function(params, node){
		return new dojo.dnd.Moveable(node, params);
	},

	// methods
	destroy: function(){
		// summary: stops watching for possible move, deletes all references, so the object can be garbage-collected
		dojo.forEach(this.events, dojo.disconnect);
		this.events = this.node = this.handle = null;
	},
	
	// mouse event processors
	onMouseDown: function(e){
		// summary: event processor for onmousedown, creates a Mover for the node
		// e: Event: mouse event
		if(this.skip && dojo.dnd.isFormElement(e)){ return; }
		if(this.delay){
			this.events.push(dojo.connect(this.handle, "onmousemove", this, "onMouseMove"));
			this.events.push(dojo.connect(this.handle, "onmouseup", this, "onMouseUp"));
			this._lastX = e.pageX;
			this._lastY = e.pageY;
		}else{
			new this.mover(this.node, e);
		}
		dojo.stopEvent(e);
	},
	onMouseMove: function(e){
		// summary: event processor for onmousemove, used only for delayed drags
		// e: Event: mouse event
		if(Math.abs(e.pageX - this._lastX) > this.delay || Math.abs(e.pageY - this._lastY) > this.delay){
			this.onMouseUp(e);
			new this.mover(this.node, e);
		}
		dojo.stopEvent(e);
	},
	onMouseUp: function(e){
		// summary: event processor for onmouseup, used only for delayed delayed drags
		// e: Event: mouse event
		dojo.disconnect(this.events.pop());
		dojo.disconnect(this.events.pop());
	}
});

dojo.dnd.constrainedMover = function(fun, within){
	// summary: returns a constrained version of dojo.dnd.Mover
	// description: this function produces n object, which will put a constraint on 
	//	the margin box of dragged object in absolute coordinates
	// fun: Function: called on drag, and returns a constraint box
	// within: Boolean: if true, constraints the whole dragged object withtin the rectangle, 
	//	otherwise the constraint is applied to the left-top corner
	var mover = function(node, e){
		dojo.dnd.Mover.call(this, node, e);
	};
	dojo.extend(mover, dojo.dnd.Mover.prototype);
	dojo.extend(mover, {
		onMouseMove: function(e){
			// summary: event processor for onmousemove
			// e: Event: mouse event
			var m = this.marginBox, c = this.constraintBox,
				l = m.l + e.pageX, t = m.t + e.pageY;
			l = l < c.l ? c.l : c.r < l ? c.r : l;
			t = t < c.t ? c.t : c.b < t ? c.b : t;
			dojo.marginBox(this.node, {l: l, t: t});
		},
		onFirstMove: function(){
			// summary: called once to initialize things; it is meant to be called only once
			dojo.dnd.Mover.prototype.onFirstMove.call(this);
			var c = this.constraintBox = fun.call(this), m = this.marginBox;
			c.r = c.l + c.w - (within ? m.w : 0);
			c.b = c.t + c.h - (within ? m.h : 0);
		}
	});
	return mover;	// Object
};

dojo.dnd.boxConstrainedMover = function(box, within){
	// summary: a specialization of dojo.dnd.constrainedMover, which constrains to the specified box
	// box: Object: a constraint box (l, t, w, h)
	// within: Boolean: if true, constraints the whole dragged object withtin the rectangle, 
	//	otherwise the constraint is applied to the left-top corner
	return dojo.dnd.constrainedMover(function(){ return box; }, within);	// Object
};

dojo.dnd.parentConstrainedMover = function(area, within){
	// summary: a specialization of dojo.dnd.constrainedMover, which constrains to the parent node
	// area: String: "margin" to constrain within the parent's margin box, "border" for the border box,
	//	"padding" for the padding box, and "content" for the content box; "content" is the default value.
	// within: Boolean: if true, constraints the whole dragged object withtin the rectangle, 
	//	otherwise the constraint is applied to the left-top corner
	var fun = function(){
		var n = this.node.parentNode, 
			s = dojo.getComputedStyle(n), 
			mb = dojo._getMarginBox(n, s);
		if(area == "margin"){
			return mb;	// Object
		}
		var t = dojo._getMarginExtents(n, s);
		mb.l += t.l, mb.t += t.t, mb.w -= t.w, mb.h -= t.h;
		if(area == "border"){
			return mb;	// Object
		}
		t = dojo._getBorderExtents(n, s);
		mb.l += t.l, mb.t += t.t, mb.w -= t.w, mb.h -= t.h;
		if(area == "padding"){
			return mb;	// Object
		}
		t = dojo._getPadExtents(n, s);
		mb.l += t.l, mb.t += t.t, mb.w -= t.w, mb.h -= t.h;
		return mb;	// Object
	};
	return dojo.dnd.constrainedMover(fun, within);	// Object
};

}

if(!dojo._hasResource["dojo.fx"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.fx"] = true;
dojo.provide("dojo.fx");
dojo.provide("dojo.fx.Toggler");

dojo.fx.chain = function(/*dojo._Animation[]*/ animations){
	// summary: Chain a list of _Animations to run in sequence
	var first = animations.shift();
	var previous = first;
	dojo.forEach(animations, function(current){
		dojo.connect(previous, "onEnd", current, "play");
		previous = current;
	});
	return first; // dojo._Animation
};

dojo.fx.combine = function(/*dojo._Animation[]*/ animations){
	// summary: Combine a list of _Animations to run in parallel

	var first = animations.shift();
	dojo.forEach(animations, function(current){
		dojo.forEach([

//FIXME: onEnd gets fired multiple times for each animation, not once for the combined animation
//	should we return to a "container" with its own unique events?

			"play", "pause", "stop"
		], function(event){
			if(current[event]){
				dojo.connect(first, event, current, event);
			}
		}, this);
	});
	return first; // dojo._Animation
};

dojo.declare("dojo.fx.Toggler", null, {
	constructor: function(args){
		// summary:
		//		class constructor for an animation toggler. It accepts a packed
		//		set of arguments about what type of animation to use in each
		//		direction, duration, etc.
		//	example:
		//		var t = new dojo.fx.Toggler({
		//			node: "nodeId",
		//			showDuration: 500,
		//			// hideDuration will default to "200"
		//			showFunc: dojo.wipeIn, 
		//			// hideFunc will default to "fadeOut"
		//		});
		//		t.show(100); // delay showing for 100ms
		//		// ...time passes...
		//		t.hide();

		// FIXME: need a policy for where the toggler should "be" the next
		// time show/hide are called if we're stopped somewhere in the
		// middle.

		var _t = this;

		dojo.mixin(_t, args);
		_t.node = args.node;
		_t._showArgs = dojo.mixin({}, args);
		_t._showArgs.node = _t.node;
		_t._showArgs.duration = _t.showDuration;
		_t.showAnim = _t.showFunc(_t._showArgs);

		_t._hideArgs = dojo.mixin({}, args);
		_t._hideArgs.node = _t.node;
		_t._hideArgs.duration = _t.hideDuration;
		_t.hideAnim = _t.hideFunc(_t._hideArgs);

		dojo.connect(_t.showAnim, "beforeBegin", dojo.hitch(_t.hideAnim, "stop", true));
		dojo.connect(_t.hideAnim, "beforeBegin", dojo.hitch(_t.showAnim, "stop", true));
	},
	
	node: null,
	showFunc: dojo.fadeIn,
	hideFunc: dojo.fadeOut,

	showDuration: 200,
	hideDuration: 200,

	_showArgs: null,
	_showAnim: null,

	_hideArgs: null,
	_hideAnim: null,

	_isShowing: false,
	_isHiding: false,

	show: function(delay){
		delay = delay||0;
		return this.showAnim.play(delay);
	},

	hide: function(delay){
		delay = delay||0;
		return this.hideAnim.play(delay);
	}
});

dojo.fx.wipeIn = function(/*Object*/ args){
	// summary
	//		Returns an animation that will expand the
	//		node defined in 'args' object from it's current height to
	//		it's natural height (with no scrollbar).
	//		Node must have no margin/border/padding.
	args.node = dojo.byId(args.node);
	var node = args.node, s = node.style;

	var anim = dojo.animateProperty(dojo.mixin({
		properties: {
			height: {
				// wrapped in functions so we wait till the last second to query (in case value has changed)
				start: function(){
					// start at current [computed] height, but use 1px rather than 0
					// because 0 causes IE to display the whole panel
					s.overflow="hidden";
					if(s.visibility=="hidden"||s.display=="none"){
						s.height="1px";
						s.display="";
						s.visibility="";
						return 1;
					}else{
						var height = dojo.style(node, "height");
						return Math.max(height, 1);
					}
				},
				end: function(){
					return node.scrollHeight;
				}
			}
		}
	}, args));

	dojo.connect(anim, "onEnd", anim, function(){ 
		s.height = "auto";
	});

	return anim; // dojo._Animation
}

dojo.fx.wipeOut = function(/*Object*/ args){
	// summary
	//		Returns an animation that will shrink node defined in "args"
	//		from it's current height to 1px, and then hide it.
	var node = (args.node = dojo.byId(args.node));

	var anim = dojo.animateProperty(dojo.mixin({
		properties: {
			height: {
				end: 1 // 0 causes IE to display the whole panel
			}
		}
	}, args));

	dojo.connect(anim, "beforeBegin", anim, function(){
		var s=node.style;
		s.overflow = "hidden";
		s.display = "";
	});
	dojo.connect(anim, "onEnd", anim, function(){
		var s=this.node.style;
		s.height = "auto";
		s.display = "none";
	});

	return anim; // dojo._Animation
}

dojo.fx.slideTo = function(/*Object?*/ args){
	// summary
	//		Returns an animation that will slide "node" 
	//		defined in args Object from its current position to
	//		the position defined by (args.left, args.top).

	var node = args.node = dojo.byId(args.node);
	var compute = dojo.getComputedStyle;
	
	var top = null;
	var left = null;
	
	var init = (function(){
		var innerNode = node;
		return function(){
			var pos = compute(innerNode).position;
			top = (pos == 'absolute' ? node.offsetTop : parseInt(compute(node).top) || 0);
			left = (pos == 'absolute' ? node.offsetLeft : parseInt(compute(node).left) || 0);

			if(pos != 'absolute' && pos != 'relative'){
				var ret = dojo.coords(innerNode, true);
				top = ret.y;
				left = ret.x;
				innerNode.style.position="absolute";
				innerNode.style.top=top+"px";
				innerNode.style.left=left+"px";
			}
		}
	})();
	init();

	var anim = dojo.animateProperty(dojo.mixin({
		properties: {
			top: { start: top, end: args.top||0 },
			left: { start: left, end: args.left||0 }
		}
	}, args));
	dojo.connect(anim, "beforeBegin", anim, init);

	return anim; // dojo._Animation
}

}

if(!dojo._hasResource["dijit._base.focus"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._base.focus"] = true;
dojo.provide("dijit._base.focus");

// summary:
//		These functions are used to query or set the focus and selection.
//
//		Also, they trace when widgets become actived/deactivated,
//		so that the widget can fire _onFocus/_onBlur events.
//		"Active" here means something similar to "focused", but
//		"focus" isn't quite the right word because we keep track of
//		a whole stack of "active" widgets.  Example:  Combobutton --> Menu -->
//		MenuItem.   The onBlur event for Combobutton doesn't fire due to focusing
//		on the Menu or a MenuItem, since they are considered part of the
//		Combobutton widget.  It only happens when focus is shifted
//		somewhere completely different.

dojo.mixin(dijit,
{
	// _curFocus: DomNode
	//		Currently focused item on screen
	_curFocus: null,
	
	// _prevFocus: DomNode
	//		Previously focused item on screen
	_prevFocus: null,

	isCollapsed: function(){
		// summary: tests whether the current selection is empty
		var _window = dojo.global;
		var _document = dojo.doc;
		if(_document.selection){ // IE
			return !_document.selection.createRange().text; // Boolean
		}else if(_window.getSelection){
			var selection = _window.getSelection();
			if(dojo.isString(selection)){ // Safari
				return !selection; // Boolean
			}else{ // Mozilla/W3
				return selection.isCollapsed || !selection.toString(); // Boolean
			}
		}
	},

	getBookmark: function(){
		// summary: Retrieves a bookmark that can be used with moveToBookmark to return to the same range
		var bookmark, selection = dojo.doc.selection;
		if(selection){ // IE
			var range = selection.createRange();
			if(selection.type.toUpperCase()=='CONTROL'){
				bookmark = range.length ? dojo._toArray(range) : null;
			}else{
				bookmark = range.getBookmark();
			}
		}else{
			if(dojo.global.getSelection){
				selection = dojo.global.getSelection();
				if(selection){
					var range = selection.getRangeAt(0);
					bookmark = range.cloneRange();
				}
			}else{
				console.debug("No idea how to store the current selection for this browser!");
			}
		}
		return bookmark; // Array
	},

	moveToBookmark: function(/*Object*/bookmark){
		// summary: Moves current selection to a bookmark
		// bookmark: this should be a returned object from dojo.html.selection.getBookmark()
		var _document = dojo.doc;
		if(_document.selection){ // IE
			var range;
			if(dojo.isArray(bookmark)){
				range = _document.body.createControlRange();
				dojo.forEach(bookmark, range.addElement);
			}else{
				range = _document.selection.createRange();
				range.moveToBookmark(bookmark);
			}
			range.select();
		}else{ //Moz/W3C
			var selection = dojo.global.getSelection && dojo.global.getSelection();
			if(selection && selection.removeAllRanges){
				selection.removeAllRanges();
				selection.addRange(bookmark);
			}else{
				console.debug("No idea how to restore selection for this browser!");
			}
		}
	},

	getFocus: function(/*Widget*/menu, /*Window*/ openedForWindow){
		// summary:
		//	Returns the current focus and selection.
		//	Called when a popup appears (either a top level menu or a dialog),
		//	or when a toolbar/menubar receives focus
		//
		// menu:
		//	the menu that's being opened
		//
		// openedForWindow:
		//	iframe in which menu was opened
		//
		// returns:
		//	a handle to restore focus/selection

		return {
			// Node to return focus to
			node: menu && dojo.isDescendant(dijit._curFocus, menu.domNode) ? dijit._prevFocus : dijit._curFocus,
			
			// Previously selected text
			bookmark: 
				!dojo.withGlobal(openedForWindow||dojo.global, dijit.isCollapsed) ?
				dojo.withGlobal(openedForWindow||dojo.global, dijit.getBookmark) :
				null,
				
			openedForWindow: openedForWindow
		}; // Object
	},

	focus: function(/*Object || DomNode */ handle){
		// summary:
		//		Sets the focused node and the selection according to argument.
		//		To set focus to an iframe's content, pass in the iframe itself.
		// handle:
		//		object returned by get(), or a DomNode

		if(!handle){ return; }

		var node = "node" in handle ? handle.node : handle,		// because handle is either DomNode or a composite object
			bookmark = handle.bookmark,
			openedForWindow = handle.openedForWindow;

		// Set the focus
		// Note that for iframe's we need to use the <iframe> to follow the parentNode chain,
		// but we need to set focus to iframe.contentWindow
		if(node){
			var focusNode = (node.tagName.toLowerCase()=="iframe") ? node.contentWindow : node;
			if(focusNode && focusNode.focus){
				try{
					// Gecko throws sometimes if setting focus is impossible,
					// node not displayed or something like that
					focusNode.focus();
				}catch(e){/*quiet*/}
			}			
			dijit._onFocusNode(node);
		}

		// set the selection
		// do not need to restore if current selection is not empty
		// (use keyboard to select a menu item)
		if(bookmark && dojo.withGlobal(openedForWindow||dojo.global, dijit.isCollapsed)){
			if(openedForWindow){
				openedForWindow.focus();
			}
			try{
				dojo.withGlobal(openedForWindow||dojo.global, moveToBookmark, null, [bookmark]);
			}catch(e){
				/*squelch IE internal error, see http://trac.dojotoolkit.org/ticket/1984 */
			}
		}
	},

	// List of currently active widgets (focused widget and it's ancestors)
	_activeStack: [],

	registerWin: function(/*Window?*/targetWindow){
		// summary:
		//		Registers listeners on the specified window (either the main
		//		window or an iframe) to detect when the user has clicked somewhere.
		//		Anyone that creates an iframe should call this function.

		if(!targetWindow){
			targetWindow = window;
		}

		dojo.connect(targetWindow.document, "onmousedown", null, function(evt){
			// this mouse down event will probably be immediately followed by a blur event; ignore it
			dijit._ignoreNextBlurEvent = true;
			setTimeout(function(){ dijit._ignoreNextBlurEvent = false; }, 0);
			dijit._onTouchNode(evt.target||evt.srcElement);
		});
		//dojo.connect(targetWindow, "onscroll", ???);

		// Listen for blur and focus events on targetWindow's body
		var body = targetWindow.document.body || targetWindow.document.getElementsByTagName("body")[0];
		if(body){
			if(dojo.isIE){
				body.attachEvent('onactivate', function(evt){
					if(evt.srcElement.tagName.toLowerCase() != "body"){
						dijit._onFocusNode(evt.srcElement);
					}
				});
				body.attachEvent('ondeactivate', function(evt){ dijit._onBlurNode(); });
			}else{
				body.addEventListener('focus', function(evt){ dijit._onFocusNode(evt.target); }, true);
				body.addEventListener('blur', function(evt){ dijit._onBlurNode(); }, true);
			}
		}
	},
	
	_onBlurNode: function(){
		// summary:
		// 		Called when focus leaves a node.
		//		Usually ignored, _unless_ it *isn't* follwed by touching another node,
		//		which indicates that we tabbed off the last field on the page,
		//		in which case everything is blurred
		if(dijit._ignoreNextBlurEvent){
			dijit._ignoreNextBlurEvent = false;
			return;
		}
		dijit._prevFocus = dijit._curFocus;
		dijit._curFocus = null;
		if(dijit._blurAllTimer){
			clearTimeout(dijit._blurAllTimer);
		}
		dijit._blurAllTimer = setTimeout(function(){ 
			delete dijit._blurAllTimer; dijit._setStack([]); }, 100);
	},

	_onTouchNode: function(/*DomNode*/ node){
		// summary
		//		Callback when node is focused or mouse-downed

		// ignore the recent blurNode event
		if(dijit._blurAllTimer){
			clearTimeout(dijit._blurAllTimer);
			delete dijit._blurAllTimer;
		}

		// compute stack of active widgets (ex: ComboButton --> Menu --> MenuItem)
		var newStack=[];
		try{
			while(node){
				if(node.dijitPopupParent){
					node=dijit.byId(node.dijitPopupParent).domNode;
				}else if(node.tagName && node.tagName.toLowerCase()=="body"){
					// is this the root of the document or just the root of an iframe?
					if(node===dojo.body()){
						// node is the root of the main document
						break;
					}
					// otherwise, find the iframe this node refers to (can't access it via parentNode,
					// need to do this trick instead) and continue tracing up the document
					node=dojo.query("iframe").filter(function(iframe){ return iframe.contentDocument.body===node; })[0];
				}else{
					var id = node.getAttribute && node.getAttribute("widgetId");
					if(id){
						newStack.unshift(id);
					}
					node=node.parentNode;
				}
			}
		}catch(e){ /* squelch */ }

		dijit._setStack(newStack);
	},

	_onFocusNode: function(/*DomNode*/ node){
		// summary
		//		Callback when node is focused
		if(node && node.tagName && node.tagName.toLowerCase() == "body"){
			return;
		}
		if(node==dijit._curFocus){ return; }
		dijit._prevFocus = dijit._curFocus;
		dijit._curFocus = node;
		dijit._onTouchNode(node);
		dojo.publish("focusNode", [node]);

		// handle focus/blur styling
		var w = dijit.byId(node.id);
		if (w && w._setStateClass){
			w._focused = true;
			w._setStateClass();
			// watch for a blur on the node that received focus
			var blurConnector = dojo.connect(node, "onblur", function(){
				w._focused = false;
				w._setStateClass();
				dojo.disconnect(blurConnector);
			});
		}
	},

	_setStack: function(newStack){
		// summary
		//	The stack of active widgets has changed.  Send out appropriate events and record new stack

		var stack = dijit._activeStack;

		// compare old stack to new stack to see how many elements they have in common
		for(var nCommon=0; nCommon<Math.min(stack.length, newStack.length); nCommon++){
			if(stack[nCommon] != newStack[nCommon]){
				break;
			}
		}

		// for all elements that have gone out of focus, send blur event
		for(var i=stack.length-1; i>=nCommon; i--){
			var widget = dijit.byId(stack[i]);
			if(widget){
				dojo.publish("widgetBlur", [widget]);
				if(widget._onBlur){
					widget._onBlur();
				}
			}
		}

		// for all element that have come into focus, send focus event
		for(var i=nCommon; i<newStack.length; i++){
			var widget = dijit.byId(newStack[i]);
			if(widget){
				dojo.publish("widgetFocus", [widget]);
				if(widget._onFocus){
					widget._onFocus();
				}
			}
		}
		
		dijit._activeStack = newStack;
	}
});

// register top window and all the iframes it contains
dojo.addOnLoad(dijit.registerWin);

}

if(!dojo._hasResource["dijit._base.manager"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._base.manager"] = true;
dojo.provide("dijit._base.manager");

dojo.declare("dijit.WidgetSet", null, {
	constructor: function(){
		// summary:
		//	A set of widgets indexed by id
		this._hash={};
	},
	
	add: function(/*Widget*/ widget){
		this._hash[widget.id]=widget;
	},
	
	remove: function(/*String*/ id){
		delete this._hash[id];
	},

	forEach: function(/*Function*/ func){
		for(var id in this._hash){
			func(this._hash[id]);
		}
	},
	
	filter: function(/*Function*/ filter){
		var res = new dijit.WidgetSet();
		this.forEach(function(widget){
			if(filter(widget)){ res.add(widget); }
		});
		return res;		// dijit.WidgetSet
	},
	
	byId: function(/*String*/ id){
		return this._hash[id];
	},

	byClass: function(/*String*/ cls){
		return this.filter(function(widget){ return widget.declaredClass==cls; });	// dijit.WidgetSet
	}
	});

// registry: list of all widgets on page
dijit.registry = new dijit.WidgetSet();

dijit._widgetTypeCtr = {};

dijit.getUniqueId = function(/*String*/widgetType){
	// summary
	//	Generates a unique id for a given widgetType

	var id;
	do{
		id = widgetType + "_" +
			(dijit._widgetTypeCtr[widgetType] !== undefined ?
				++dijit._widgetTypeCtr[widgetType] : dijit._widgetTypeCtr[widgetType] = 0);
	}while(dijit.byId(id));
	return id; // String
};


if(dojo.isIE){
	// Only run this for IE because we think it's only necessary in that case,
	// and because it causes problems on FF.  See bug #3531 for details.
	dojo.addOnUnload(function(){
		dijit.registry.forEach(function(widget){ widget.destroy(); });
	});
}

dijit.byId = function(/*String|Widget*/id){
	// summary:
	//		Returns a widget by its id, or if passed a widget, no-op (like dojo.byId())
	return (dojo.isString(id)) ? dijit.registry.byId(id) : id; // Widget
};

dijit.byNode = function(/* DOMNode */ node){
	// summary:
	//		Returns the widget as referenced by node
	return dijit.registry.byId(node.getAttribute("widgetId")); // Widget
};

}

if(!dojo._hasResource["dijit._base.place"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._base.place"] = true;
dojo.provide("dijit._base.place");

// ported from dojo.html.util

dijit.getViewport = function(){
	//	summary
	//	Returns the dimensions and scroll position of the viewable area of a browser window

	var _window = dojo.global;
	var _document = dojo.doc;

	// get viewport size
	var w = 0, h = 0;
	if(dojo.isMozilla){
		// mozilla
		w = _document.documentElement.clientWidth;
		h = _window.innerHeight;
	}else if(!dojo.isOpera && _window.innerWidth){
		//in opera9, dojo.body().clientWidth should be used, instead
		//of window.innerWidth/document.documentElement.clientWidth
		//so we have to check whether it is opera
		w = _window.innerWidth;
		h = _window.innerHeight;
	}else if(dojo.isIE && _document.documentElement && _document.documentElement.clientHeight){
		w = _document.documentElement.clientWidth;
		h = _document.documentElement.clientHeight;
	}else if(dojo.body().clientWidth){
		// IE5, Opera
		w = dojo.body().clientWidth;
		h = dojo.body().clientHeight;
	}
	
	// get scroll position
	var scroll = dojo._docScroll();

	return { w: w, h: h, l: scroll.x, t: scroll.y };	//	object
};

dijit.placeOnScreen = function(
	/* HTMLElement */	node,
	/* Object */		pos,
	/* Object */		corners,
	/* boolean? */		tryOnly){
	//	summary:
	//		Keeps 'node' in the visible area of the screen while trying to
	//		place closest to pos.x, pos.y. The input coordinates are
	//		expected to be the desired document position.
	//
	//		Set which corner(s) you want to bind to, such as
	//		
	//			placeOnScreen(node, {x: 10, y: 20}, ["TR", "BL"])
	//		
	//		The desired x/y will be treated as the topleft(TL)/topright(TR) or
	//		BottomLeft(BL)/BottomRight(BR) corner of the node. Each corner is tested
	//		and if a perfect match is found, it will be used. Otherwise, it goes through
	//		all of the specified corners, and choose the most appropriate one.
	//		
	//		NOTE: node is assumed to be absolutely or relatively positioned.
	
	var choices = dojo.map(corners, function(corner){ return { corner: corner, pos: pos }; });
	
	return dijit._place(node, choices);
}

dijit._place = function(/*HtmlElement*/ node, /* Array */ choices, /* Function */ layoutNode){
	// summary:
	//		Given a list of spots to put node, put it at the first spot where it fits,
	//		of if it doesn't fit anywhere then the place with the least overflow
	// choices: Array
	//		Array of elements like: {corner: 'TL', pos: {x: 10, y: 20} }
	//		Above example says to put the top-left corner of the node at (10,20)
	//	layoutNode: Function(node, orient)
	//		for things like tooltip, they are displayed differently (and have different dimensions)
	//		based on their orientation relative to the parent.   This adjusts the popup based on orientation.
			
	// get {x: 10, y: 10, w: 100, h:100} type obj representing position of
	// viewport over document
	var view = dijit.getViewport();

	// This won't work if the node is inside a <div style="position: relative">,
	// so reattach it to document.body.   (Otherwise, the positioning will be wrong
	// and also it might get cutoff)
	if(!node.parentNode || String(node.parentNode.tagName).toLowerCase() != "body"){
		dojo.body().appendChild(node);
	}

	var best=null;
	for(var i=0; i<choices.length; i++){
		var corner = choices[i].corner;
		var pos = choices[i].pos;

		// configure node to be displayed in given position relative to button
		// (need to do this in order to get an accurate size for the node, because
		// a tooltips size changes based on position, due to triangle)
		if(layoutNode){
			layoutNode(corner);
		}

		// get node's size
		var oldDisplay = node.style.display;
		var oldVis = node.style.visibility;
		node.style.visibility = "hidden";
		node.style.display = "";
		var mb = dojo.marginBox(node);
		node.style.display = oldDisplay;
		node.style.visibility = oldVis;

		// coordinates and size of node with specified corner placed at pos,
		// and clipped by viewport
		var startX = (corner.charAt(1)=='L' ? pos.x : Math.max(view.l, pos.x - mb.w)),
			startY = (corner.charAt(0)=='T' ? pos.y : Math.max(view.t, pos.y -  mb.h)),
			endX = (corner.charAt(1)=='L' ? Math.min(view.l+view.w, startX+mb.w) : pos.x),
			endY = (corner.charAt(0)=='T' ? Math.min(view.t+view.h, startY+mb.h) : pos.y),
			width = endX-startX,
			height = endY-startY,
			overflow = (mb.w-width) + (mb.h-height);
			
		if(best==null || overflow<best.overflow){
			best = {
				corner: corner,
				aroundCorner: choices[i].aroundCorner,
				x: startX,
				y: startY,
				w: width,
				h: height,
				overflow: overflow
			};
		}
		if(overflow==0){
			break;
		}
	}

	node.style.left = best.x + "px";
	node.style.top = best.y + "px";
	return best;
}

dijit.placeOnScreenAroundElement = function(
	/* HTMLElement */	node,
	/* HTMLElement */	aroundNode,
	/* Object */		aroundCorners,
	/* Function */		layoutNode){

	//	summary
	//	Like placeOnScreen, except it accepts aroundNode instead of x,y
	//	and attempts to place node around it.  Uses margin box dimensions.
	//
	//	aroundCorners
	//		specify Which corner of aroundNode should be
	//		used to place the node => which corner(s) of node to use (see the
	//		corners parameter in dijit.placeOnScreen)
	//		e.g. {'TL': 'BL', 'BL': 'TL'}
	//
	//	layoutNode: Function(node, orient)
	//		for things like tooltip, they are displayed differently (and have different dimensions)
	//		based on their orientation relative to the parent.   This adjusts the popup based on orientation.

	
	// get coordinates of aroundNode
	aroundNode = dojo.byId(aroundNode);
	var oldDisplay = aroundNode.style.display;
	aroundNode.style.display="";
	// #3172: use the slightly tighter border box instead of marginBox
	var aroundNodeW = aroundNode.offsetWidth; //mb.w;
	var aroundNodeH = aroundNode.offsetHeight; //mb.h;
	var aroundNodePos = dojo.coords(aroundNode, true);
	aroundNode.style.display=oldDisplay;

	// Generate list of possible positions for node
	var choices = [];
	for(var nodeCorner in aroundCorners){
		choices.push( {
			aroundCorner: nodeCorner,
			corner: aroundCorners[nodeCorner],
			pos: {
				x: aroundNodePos.x + (nodeCorner.charAt(1)=='L' ? 0 : aroundNodeW),
				y: aroundNodePos.y + (nodeCorner.charAt(0)=='T' ? 0 : aroundNodeH)
			}
		});
	}
	
	return dijit._place(node, choices, layoutNode);
}

}

if(!dojo._hasResource["dijit._base.window"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._base.window"] = true;
dojo.provide("dijit._base.window");

dijit.getDocumentWindow = function(doc){
	//	summary
	// 	Get window object associated with document doc

	// With Safari, there is not way to retrieve the window from the document, so we must fix it.
	if(dojo.isSafari && !doc._parentWindow){
		/*
			This is a Safari specific function that fix the reference to the parent
			window from the document object.
		*/
		var fix=function(win){
			win.document._parentWindow=win;
			for(var i=0; i<win.frames.length; i++){
				fix(win.frames[i]);
			}
		}
		fix(window.top);
	}

	//In some IE versions (at least 6.0), document.parentWindow does not return a
	//reference to the real window object (maybe a copy), so we must fix it as well
	//We use IE specific execScript to attach the real window reference to
	//document._parentWindow for later use
	if(dojo.isIE && window !== document.parentWindow && !doc._parentWindow){
		/*
		In IE 6, only the variable "window" can be used to connect events (others
		may be only copies).
		*/
		doc.parentWindow.execScript("document._parentWindow = window;", "Javascript");
		//to prevent memory leak, unset it after use
		//another possibility is to add an onUnload handler which seems overkill to me (liucougar)
		var win = doc._parentWindow;
		doc._parentWindow = null;
		return win;	//	Window
	}

	return doc._parentWindow || doc.parentWindow || doc.defaultView;	//	Window
}

}

if(!dojo._hasResource["dijit._base.popup"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._base.popup"] = true;
dojo.provide("dijit._base.popup");





dijit.popup = new function(){
	// summary:
	//		This class is used to show/hide widgets as popups.
	//

	var stack = [],
		beginZIndex=1000,
		idGen = 1;

	this.open = function(/*Object*/ args){
		// summary:
		//		Popup the widget at the specified position
		//
		// args: Object
		//		popup: Widget
		//			widget to display,
		//		parent: Widget
		//			the button etc. that is displaying this popup
		//		around: DomNode
		//			DOM node (typically a button); place popup relative to this node
		//		orient: Object
		//			structure specifying possible positions of popup relative to "around" node
		//		onCancel: Function
		//			callback when user has canceled the popup by 
		//          	1. hitting ESC or
		//				2. by using the popup widget's proprietary cancel mechanism (like a cancel button in a dialog);
		//				   ie: whenever popupWidget.onCancel() is called, args.onCancel is called
		//		onClose: Function
		//			callback whenever this popup is closed (via close(), closeAll(), or closeTo())
		//		submenu: Boolean
		//			Is this a submenu off of the existing popup?
		//		onExecute: Function
		//			callback when user "executed" on the popup/sub-popup by selecting a menu choice, etc. (top menu only)
		//
		// examples:
		//		1. opening at the mouse position
		//			dijit.popup.open({popup: menuWidget, x: evt.pageX, y: evt.pageY});
		//		2. opening the widget as a dropdown
		//			dijit.popup.open({parent: this, popup: menuWidget, around: this.domNode, onClose: function(){...}  });
		//
		//	Note that whatever widget called dijit.popup.open() should also listen to it's own _onBlur callback
		//	(fired from _base/focus.js) to know that focus has moved somewhere else and thus the popup should be closed.

		var widget = args.popup,
			orient = args.orient || {'BL':'TL', 'TL':'BL'},
			around = args.around,
			id = (args.around && args.around.id) ? (args.around.id+"_dropdown") : ("popup_"+idGen++);

		if(!args.submenu){
			this.closeAll();
		}

		// make wrapper div to hold widget and possibly hold iframe behind it.
		// we can't attach the iframe as a child of the widget.domNode because
		// widget.domNode might be a <table>, <ul>, etc.
		var wrapper = dojo.doc.createElement("div");
		wrapper.id = id;
		wrapper.className="dijitPopup";
		wrapper.style.zIndex = beginZIndex + stack.length;
		if(args.parent){
			wrapper.dijitPopupParent=args.parent.id;
		}
		dojo.body().appendChild(wrapper);

		widget.domNode.style.display="";
		wrapper.appendChild(widget.domNode);

		var iframe = new dijit.BackgroundIframe(wrapper);

		// position the wrapper node
		var best = around ?
			dijit.placeOnScreenAroundElement(wrapper, around, orient, widget.orient ? dojo.hitch(widget, "orient") : null) :
			dijit.placeOnScreen(wrapper, args, orient == 'R' ? ['TR','BR','TL','BL'] : ['TL','BL','TR','BR']);

		// TODO: use effects to fade in wrapper

		var handlers = [];

		// provide default escape key handling 
		handlers.push(dojo.connect(wrapper, "onkeypress", this, function(evt){
			if (evt.keyCode == dojo.keys.ESCAPE){
				args.onCancel();
			}
		}));

		// watch for cancel/execute events on the popup and notify the caller
		// (for a menu, "execute" means clicking an item)
		if(widget.onCancel){
			handlers.push(dojo.connect(widget, "onCancel", null, args.onCancel));
		}
		
		handlers.push(dojo.connect(widget, widget.onExecute ? "onExecute" : "onChange", null, function(){
			if(stack[0] && stack[0].onExecute){
				stack[0].onExecute();
			}
		}));

		stack.push({
			wrapper: wrapper,
			iframe: iframe,
			widget: widget,
			onExecute: args.onExecute,
			onCancel: args.onCancel,
 			onClose: args.onClose,
			handlers: handlers
		});

		if(widget.onOpen){
			widget.onOpen(best);
		}

		return best;
	};

	this.close = function(){
		// summary:
		//		Close popup on the top of the stack (the highest z-index popup)

		// this needs to happen before the stack is popped, because menu's
		// onClose calls closeTo(this)
		var widget = stack[stack.length-1].widget;
		if(widget.onClose){
			widget.onClose();
		}

		if(!stack.length){
			return;
		}
		var top = stack.pop();
		var wrapper = top.wrapper,
			iframe = top.iframe,
			widget = top.widget,
			onClose = top.onClose;

		dojo.forEach(top.handlers, dojo.disconnect);

		// #2685: check if the widget still has a domNode so ContentPane can change its URL without getting an error
		if(!widget||!widget.domNode){ return; }
		dojo.style(widget.domNode, "display", "none");
		dojo.body().appendChild(widget.domNode);
		iframe.destroy();
		dojo._destroyElement(wrapper);

		if(onClose){
			onClose();
		}
	};

	this.closeAll = function(){
		// summary: close every popup, from top of stack down to the first one
		while(stack.length){
			this.close();
		}
	};

	this.closeTo = function(/*Widget*/ widget){
		// summary: closes every popup above specified widget
		while(stack.length && stack[stack.length-1].widget.id != widget.id){
			this.close();
		}
	};

}();

dijit._frames = new function(){
	// summary: cache of iframes
	var queue = [];

	this.pop = function(){
		var iframe;
		if(queue.length){
			iframe = queue.pop();
			iframe.style.display="";
		}else{
			if(dojo.isIE){
				var html="<iframe src='javascript:\"\"'"
					+ " style='position: absolute; left: 0px; top: 0px;"
					+ "z-index: -1; filter:Alpha(Opacity=\"0\");'>";
				iframe = dojo.doc.createElement(html);
			}else{
			 	var iframe = dojo.doc.createElement("iframe");
				iframe.src = 'javascript:""';
				iframe.className = "dijitBackgroundIframe";
			}
			iframe.tabIndex = -1; // Magic to prevent iframe from getting focus on tab keypress - as style didnt work.
			dojo.body().appendChild(iframe);
		}
		return iframe;
	};
	
	this.push = function(iframe){
		iframe.style.display="";
		if(dojo.isIE){
			iframe.style.removeExpression("width");
			iframe.style.removeExpression("height");
		}
		queue.push(iframe);
	}
}();

// fill the queue
if(dojo.isIE && dojo.isIE < 7){
	dojo.addOnLoad(function(){
		var f = dijit._frames;
		dojo.forEach([f.pop()], f.push);
	});
}


dijit.BackgroundIframe = function(/* HTMLElement */node){
	//	summary:
	//		For IE z-index schenanigans. id attribute is required.
	//
	//	description:
	//		new dijit.BackgroundIframe(node)
	//			Makes a background iframe as a child of node, that fills
	//			area (and position) of node

	if(!node.id){ throw new Error("no id"); }
	if((dojo.isIE && dojo.isIE < 7) || (dojo.isFF && dojo.isFF < 3 && dojo.hasClass(dojo.body(), "dijit_a11y"))){
		var iframe = dijit._frames.pop();
		node.appendChild(iframe);
		if(dojo.isIE){
			iframe.style.setExpression("width", "document.getElementById('" + node.id + "').offsetWidth");
			iframe.style.setExpression("height", "document.getElementById('" + node.id + "').offsetHeight");
		}
		this.iframe = iframe;
	}
};

dojo.extend(dijit.BackgroundIframe, {
	destroy: function(){
		//	summary: destroy the iframe
		if(this.iframe){
			dijit._frames.push(this.iframe);
			delete this.iframe;
		}
	}
});

}

if(!dojo._hasResource["dijit._base.scroll"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._base.scroll"] = true;
dojo.provide("dijit._base.scroll");

dijit.scrollIntoView = function(/* HTMLElement */node){
	//	summary
	//	Scroll the passed node into view, if it is not.

	// don't rely on that node.scrollIntoView works just because the function is there
	// it doesnt work in Konqueror or Opera even though the function is there and probably
	// not safari either
	// dont like browser sniffs implementations but sometimes you have to use it
	if(dojo.isIE){
		//only call scrollIntoView if there is a scrollbar for this menu,
		//otherwise, scrollIntoView will scroll the window scrollbar
		if(dojo.marginBox(node.parentNode).h <= node.parentNode.scrollHeight){ //PORT was getBorderBox
			node.scrollIntoView(false);
		}
	}else if(dojo.isMozilla){
		node.scrollIntoView(false);
	}else{
		var parent = node.parentNode;
		var parentBottom = parent.scrollTop + dojo.marginBox(parent).h; //PORT was getBorderBox
		var nodeBottom = node.offsetTop + dojo.marginBox(node).h;
		if(parentBottom < nodeBottom){
			parent.scrollTop += (nodeBottom - parentBottom);
		}else if(parent.scrollTop > node.offsetTop){
			parent.scrollTop -= (parent.scrollTop - node.offsetTop);
		}
	}
};

}

if(!dojo._hasResource["dijit._base.sniff"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._base.sniff"] = true;
dojo.provide("dijit._base.sniff");

// ported from dojo.html.applyBrowserClass (style.js)

//	summary:
//		Applies pre-set class names based on browser & version to the
//		top-level HTML node.  Simply doing a require on this module will
//		establish this CSS.  Modified version of Morris' CSS hack.
(function(){
	var d = dojo;
	var ie = d.isIE;
	var opera = d.isOpera;
	var maj = Math.floor;
	var classes = {
		dj_ie: ie,
//		dj_ie55: ie == 5.5,
		dj_ie6: maj(ie) == 6,
		dj_ie7: maj(ie) == 7,
		dj_iequirks: ie && d.isQuirks,
// NOTE: Opera not supported by dijit
		dj_opera: opera,
		dj_opera8: maj(opera) == 8,
		dj_opera9: maj(opera) == 9,
		dj_khtml: d.isKhtml,
		dj_safari: d.isSafari,
		dj_gecko: d.isMozilla
	}; // no dojo unsupported browsers

	for(var p in classes){
		if(classes[p]){
			var html = dojo.doc.documentElement; //TODO browser-specific DOM magic needed?
			if(html.className){
				html.className += " " + p;
			}else{
				html.className = p;
			}
		}
	}
})();

}

if(!dojo._hasResource["dijit._base.bidi"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._base.bidi"] = true;
dojo.provide("dijit._base.bidi");

// summary: applies a class to the top of the document for right-to-left stylesheet rules

dojo.addOnLoad(function(){
	if(!dojo._isBodyLtr()){
		dojo.addClass(dojo.body(), "dijitRtl");
	}
});

}

if(!dojo._hasResource["dijit._base.typematic"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._base.typematic"] = true;
dojo.provide("dijit._base.typematic");

dijit.typematic = {
	// summary:
	//              These functions are used to repetitively call a user specified callback
	//		method when a specific key or mouse click over a specific DOM node is
	//		held down for a specific amount of time.
	//		Only 1 such event is allowed to occur on the browser page at 1 time.

	_fireEventAndReload: function(){
		this._timer = null;
		this._callback(++this._count, this._node, this._evt);
		this._currentTimeout = (this._currentTimeout < 0) ? this._initialDelay : ((this._subsequentDelay > 1) ? this._subsequentDelay : Math.round(this._currentTimeout * this._subsequentDelay));
		this._timer = setTimeout(dojo.hitch(this, "_fireEventAndReload"), this._currentTimeout);
	},

	trigger: function(/*Event*/ evt, /* Object */ _this, /*DOMNode*/ node, /* Function */ callback, /* Object */ obj, /* Number */ subsequentDelay, /* Number */ initialDelay){
		// summary:
		//      Start a timed, repeating callback sequence.
		//	If already started, the function call is ignored.
		//	This method is not normally called by the user but can be
		//	when the normal listener code is insufficient.
		//	_this: pointer to the user's widget space.
		//	callback: function name to call until the sequence is stopped.
		//	obj: any user space object to pass to the callback.
		//	subsequentDelay: if > 1, the number of milliseconds until the 3->n events occur
		//		or else the fractional time multiplier for the next event.
		//	initialDelay: the number of milliseconds until the 2nd event occurs.
		if (obj != this._obj){
			this.stop();
			this._initialDelay = initialDelay ? initialDelay : 500;
			this._subsequentDelay = subsequentDelay ? subsequentDelay : 0.90;
			this._obj = obj;
			this._evt = evt;
			this._node = node;
			this._currentTimeout = -1;
			this._count = -1;
			this._callback = dojo.hitch(_this, callback);
			this._fireEventAndReload();
		}
	},

	stop: function(){
		// summary:
		//      Stop an ongoing timed, repeating callback sequence.
		if(this._timer){
			clearTimeout(this._timer);
			this._timer = null;
		}
		if(this._obj){
			this._callback(-1, this._node, this._evt);
			this._obj = null;
		}
	},

	addKeyListener: function(/*DOMNode*/ node, /*Object*/ keyObject, /*Object*/ _this, /*Function*/ callback, /*Number*/ subsequentDelay, /*Number*/ initialDelay){
		// summary: Start listening for a specific typematic key.
		//	node: the DOM node object to listen on for key events.
		//	keyObject: an object defining the key to listen for.
		//		key: (mandatory) the keyCode (number) or character (string) to listen for.
		//		ctrlKey: desired ctrl key state to initiate the calback sequence:
		//			pressed (true)
		//			released (false)
		//			either (unspecified)
		//		altKey: same as ctrlKey but for the alt key
		//		shiftKey: same as ctrlKey but for the shift key
		//	See the trigger method for other parameters.
		var ary = [];
		ary.push(dojo.connect(node, "onkeypress", this, function(evt){
			if(evt.keyCode == keyObject.keyCode && (!keyObject.charCode || keyObject.charCode == evt.charCode)
			&& ((typeof keyObject.ctrlKey == "undefined") || keyObject.ctrlKey == evt.ctrlKey)
			&& ((typeof keyObject.altKey == "undefined") || keyObject.altKey == evt.ctrlKey)
			&& ((typeof keyObject.shiftKey == "undefined") || keyObject.shiftKey == evt.ctrlKey)){
				dojo.stopEvent(evt);
				dijit.typematic.trigger(keyObject, _this, node, callback, keyObject, subsequentDelay, initialDelay);
			}else if (dijit.typematic._obj == keyObject){
				dijit.typematic.stop();
			}
		}));
		ary.push(dojo.connect(node, "onkeyup", this, function(evt){
			if(dijit.typematic._obj == keyObject){
				dijit.typematic.stop();
			}
		}));
		return ary;
	},

	addMouseListener: function(/*DOMNode*/ node, /*Object*/ _this, /*Function*/ callback, /*Number*/ subsequentDelay, /*Number*/ initialDelay){
		// summary: Start listening for a typematic mouse click.
		//	node: the DOM node object to listen on for mouse events.
		//	See the trigger method for other parameters.
		var ary = [];
		ary.push(dojo.connect(node, "mousedown", this, function(evt){
			dojo.stopEvent(evt);
			dijit.typematic.trigger(evt, _this, node, callback, node, subsequentDelay, initialDelay);
		}));
		ary.push(dojo.connect(node, "mouseup", this, function(evt){
			dojo.stopEvent(evt);
			dijit.typematic.stop();
		}));
		ary.push(dojo.connect(node, "mouseout", this, function(evt){
			dojo.stopEvent(evt);
			dijit.typematic.stop();
		}));
		ary.push(dojo.connect(node, "mousemove", this, function(evt){
			dojo.stopEvent(evt);
		}));
		ary.push(dojo.connect(node, "dblclick", this, function(evt){
			dojo.stopEvent(evt);
			if(dojo.isIE){
				dijit.typematic.trigger(evt, _this, node, callback, node, subsequentDelay, initialDelay);
				setTimeout("dijit.typematic.stop()",50);
			}
		}));
		return ary;
	},

	addListener: function(/*Node*/ mouseNode, /*Node*/ keyNode, /*Object*/ keyObject, /*Object*/ _this, /*Function*/ callback, /*Number*/ subsequentDelay, /*Number*/ initialDelay){
		// summary: Start listening for a specific typematic key and mouseclick.
		//	This is a thin wrapper to addKeyListener and addMouseListener.
		//	mouseNode: the DOM node object to listen on for mouse events.
		//	keyNode: the DOM node object to listen on for key events.
		//	The mouseNode is used as the callback obj parameter.
		//	See the trigger method for other parameters.
		return this.addKeyListener(keyNode, keyObject, _this, callback, subsequentDelay, initialDelay).concat(
			this.addMouseListener(mouseNode, _this, callback, subsequentDelay, initialDelay));
	}
};

}

if(!dojo._hasResource["dijit._base.wai"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._base.wai"] = true;
dojo.provide("dijit._base.wai");

dijit.waiNames  = ["waiRole", "waiState"];

dijit.wai = {
	// summary: Contains functions to set accessibility roles and states
	//		onto widget elements
	waiRole: { 	
				// name: String:
				//		information for mapping accessibility role
				name: "waiRole",
				// namespace: String:
				//		URI of the namespace for the set of roles
				"namespace": "http://www.w3.org/TR/xhtml2",
				// alias: String:
				//		The alias to assign the namespace
				alias: "x2",
				// prefix: String:
				//		The prefix to assign to the role value
				prefix: "wairole:"
	},
	waiState: {
				// name: String:
				//		information for mapping accessibility state
				name: "waiState",
				// namespace: String:
				//		URI of the namespace for the set of states
				"namespace": "http://www.w3.org/2005/07/aaa",
				// alias: String:
				//		The alias to assign the namespace
				alias: "aaa",
				// prefix: String:
				//		empty string - state value does not require prefix
				prefix: ""
	},
	setAttr: function(/*DomNode*/node, /*String*/ ns, /*String*/ attr, /*String|Boolean*/value){
		// summary: Use appropriate API to set the role or state attribute onto the element.
		// description: In IE use the generic setAttribute() api.  Append a namespace
		//   alias to the attribute name and appropriate prefix to the value.
		//   Otherwise, use the setAttribueNS api to set the namespaced attribute. Also
		//   add the appropriate prefix to the attribute value.
		if(dojo.isIE){
			node.setAttribute(this[ns].alias+":"+ attr, this[ns].prefix+value);
		}else{
			node.setAttributeNS(this[ns]["namespace"], attr, this[ns].prefix+value);
		}
	},

	getAttr: function(/*DomNode*/ node, /*String*/ ns, /*String|Boolena*/ attr){
		// Summary:  Use the appropriate API to retrieve the role or state value
		// Description: In IE use the generic getAttribute() api.  An alias value
		// 	was added to the attribute name to simulate a namespace when the attribute
		//  was set.  Otherwise use the getAttributeNS() api to retrieve the state value
		if(dojo.isIE){
			return node.getAttribute(this[ns].alias+":"+attr);
		}else{
			return node.getAttributeNS(this[ns]["namespace"], attr);
		}
	},
	removeAttr: function(/*DomNode*/ node, /*String*/ ns, /*String|Boolena*/ attr){
		// summary:  Use the appropriate API to remove the role or state value
		// description: In IE use the generic removeAttribute() api.  An alias value
		// 	was added to the attribute name to simulate a namespace when the attribute
		//  was set.  Otherwise use the removeAttributeNS() api to remove the state value
		var success = true; //only IE returns a value
		if(dojo.isIE){
			 success = node.removeAttribute(this[ns].alias+":"+attr);
		}else{
			node.removeAttributeNS(this[ns]["namespace"], attr);
		}
		return success;
	},

	onload: function(){
		// summary:
		//		Function that detects if we are in high-contrast mode or not,
		//		and sets up a timer to periodically confirm the value.
		//		figure out the background-image style property
		//		and apply that to the image.src property.
		// description:
		//		This must be a named function and not an anonymous
		//		function, so that the widget parsing code can make sure it
		//		registers its onload function after this function.
		//		DO NOT USE "this" within this function.

		// create div for testing if high contrast mode is on or images are turned off
		var div = document.createElement("div");
		div.id = "a11yTestNode";
		div.style.cssText = 'border: 1px solid;'
			+ 'border-color:red green;'
			+ 'position: absolute;'
			+ 'left: -999px;'
			+ 'top: -999px;'
			+ 'background-image: url("' + dojo.moduleUrl("dijit", "form/templates/blank.gif") + '");';
		dojo.body().appendChild(div);

		// test it
		function check(){
			var cs = dojo.getComputedStyle(div);
			if(cs){
				var bkImg = cs.backgroundImage;
				var needsA11y = (cs.borderTopColor==cs.borderRightColor) || (bkImg != null && (bkImg == "none" || bkImg == "url(invalid-url:)" ));
				dojo[needsA11y ? "addClass" : "removeClass"](dojo.body(), "dijit_a11y");
			}
		}
		check();
		if(dojo.isIE){
			setInterval(check, 4000);
		}
	}
};

// Test if computer is in high contrast mode.
// Make sure the a11y test runs first, before widgets are instantiated.
if(dojo.isIE || dojo.isMoz){	// NOTE: checking in Safari messes things up
	dojo._loaders.unshift(dijit.wai.onload);
}

}

if(!dojo._hasResource["dijit._base"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._base"] = true;
dojo.provide("dijit._base");












}

if(!dojo._hasResource["dijit._Widget"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._Widget"] = true;
dojo.provide("dijit._Widget");



dojo.declare("dijit._Widget", null, {
	constructor: function(params, srcNodeRef){
		this.create(params, srcNodeRef);
	},
	// id: String
	//		a unique, opaque ID string that can be assigned by users or by the
	//		system. If the developer passes an ID which is known not to be
	//		unique, the specified ID is ignored and the system-generated ID is
	//		used instead.
	id: "",

	// lang: String
	//	Language to display this widget in (like en-us).
	//	Defaults to brower's specified preferred language (typically the language of the OS)
	lang: "",

	// dir: String
	//  Bi-directional support, as defined by the HTML DIR attribute. Either left-to-right "ltr" or right-to-left "rtl".
	dir: "",

	// srcNodeRef: DomNode
	//		pointer to original dom node
	srcNodeRef: null,

	// domNode DomNode:
	//		this is our visible representation of the widget! Other DOM
	//		Nodes may by assigned to other properties, usually through the
	//		template system's dojoAttachPonit syntax, but the domNode
	//		property is the canonical "top level" node in widget UI.
	domNode: null,

	//////////// INITIALIZATION METHODS ///////////////////////////////////////
	
	create: function(params, srcNodeRef) {
		// summary:
		//		To understand the process by which widgets are instantiated, it
		//		is critical to understand what other methods create calls and
		//		which of them you'll want to override. Of course, adventurous
		//		developers could override create entirely, but this should
		//		only be done as a last resort.
		//
		//		Below is a list of the methods that are called, in the order
		//		they are fired, along with notes about what they do and if/when
		//		you should over-ride them in your widget:
		//			
		//			postMixInProperties:
		//				a stub function that you can over-ride to modify
		//				variables that may have been naively assigned by
		//				mixInProperties
		//			# widget is added to manager object here
		//			buildRendering
		//				Subclasses use this method to handle all UI initialization
		//				Sets this.domNode.  Templated widgets do this automatically
		//				and otherwise it just uses the source dom node.
		//			postCreate
		//				a stub function that you can over-ride to modify take
		//				actions once the widget has been placed in the UI

		// store pointer to original dom tree
		this.srcNodeRef = dojo.byId(srcNodeRef);

		// For garbage collection.  An array of handles returned by Widget.connect()
		// Each handle returned from Widget.connect() is an array of handles from dojo.connect()
		this._connects=[];

		// _attaches: String[]
		// 		names of all our dojoAttachPoint variables
		this._attaches=[];
	
		//mixin our passed parameters
		if(this.srcNodeRef && (typeof this.srcNodeRef.id == "string")){ this.id = this.srcNodeRef.id; }
		if(params){
			dojo.mixin(this,params);
		}
		this.postMixInProperties();
		
		// generate an id for the widget if one wasn't specified
		// (be sure to do this before buildRendering() because that function might
		// expect the id to be there.
		if(!this.id){
			this.id=dijit.getUniqueId(this.declaredClass.replace(/\./g,"_"));
		}
		dijit.registry.add(this);

		this.buildRendering();
		if(this.domNode){
			this.domNode.setAttribute("widgetId", this.id);
			if(this.srcNodeRef && this.srcNodeRef.dir){
				this.domNode.dir = this.srcNodeRef.dir;
			}
		}
		this.postCreate();

		// If srcNodeRef has been processed and removed from the DOM (e.g. TemplatedWidget) then delete it to allow GC.
		if(this.srcNodeRef && !this.srcNodeRef.parentNode){
			delete this.srcNodeRef;
		}
	},

	postMixInProperties: function(){
		// summary
		//	Called after the parameters to the widget have been read-in,
		//	but before the widget template is instantiated.
		//	Especially useful to set properties that are referenced in the widget template.
	},

	buildRendering: function(){
		// summary:
		//		Construct the UI for this widget, setting this.domNode.
		//		Most widgets will mixin TemplatedWidget, which overrides this method.
		this.domNode = this.srcNodeRef;
	},

	postCreate: function(){
		// summary:
		//		Called after a widget's dom has been setup
	},

	startup: function(){
		// summary:
		//		Called after a widget's children, and other widgets on the page, have been created.
		//		Provides an opportunity to manipulate any children before they are displayed
		//		This is useful for composite widgets that need to control or layout sub-widgets
		//		Many layout widgets can use this as a wiring phase
	},

	//////////// DESTROY FUNCTIONS ////////////////////////////////

	destroyRecursive: function(/*Boolean*/ finalize){
		// summary:
		// 		Destroy this widget and it's descendants. This is the generic
		// 		"destructor" function that all widget users should call to
		// 		cleanly discard with a widget. Once a widget is destroyed, it's
		// 		removed from the manager object.
		// finalize: Boolean
		//		is this function being called part of global environment
		//		tear-down?

		this.destroyDescendants();
		this.destroy();
	},

	destroy: function(/*Boolean*/ finalize){
		// summary:
		// 		Destroy this widget, but not its descendants
		// finalize: Boolean
		//		is this function being called part of global environment
		//		tear-down?
		this.uninitialize();
		dojo.forEach(this._connects, function(array){
			dojo.forEach(array, dojo.disconnect);
		});
		this.destroyRendering(finalize);
		dijit.registry.remove(this.id);
	},

	destroyRendering: function(/*Boolean*/ finalize){
		// summary:
		//		Destroys the DOM nodes associated with this widget
		// finalize: Boolean
		//		is this function being called part of global environment
		//		tear-down?

		if(this.bgIframe){
			this.bgIframe.destroy();
			delete this.bgIframe;
		}

		if(this.domNode){
			dojo._destroyElement(this.domNode);
			delete this.domNode;
		}

		if(this.srcNodeRef){
			dojo._destroyElement(this.srcNodeRef);
			delete this.srcNodeRef;
		}
	},

	destroyDescendants: function(){
		// summary:
		//		Recursively destroy the children of this widget and their
		//		descendants.

		// TODO: should I destroy in the reverse order, to go bottom up?
		dojo.forEach(this.getDescendants(), function(widget){ widget.destroy(); });
	},

	uninitialize: function(){
		// summary:
		//		stub function. Over-ride to implement custom widget tear-down
		//		behavior.
		return false;
	},

	////////////////// MISCELLANEOUS METHODS ///////////////////

	toString: function(){
		// summary:
		//		returns a string that represents the widget. When a widget is
		//		cast to a string, this method will be used to generate the
		//		output. Currently, it does not implement any sort of reversable
		//		serialization.
		return '[Widget ' + this.declaredClass + ', ' + (this.id || 'NO ID') + ']'; // String
	},

	getDescendants: function(){
		// summary:
		//	return all the descendant widgets
		var list = dojo.query('[widgetId]', this.domNode);
		return list.map(dijit.byNode);		// Array
	},

	nodesWithKeyClick : ["input", "button"],

	connect: function(
			/*Object|null*/ obj,
			/*String*/ event,
			/*String|Function*/ method){

		// summary:
		//		Connects specified obj/event to specified method of this object
		//		and registers for disconnect() on widget destroy.
		//		Special event: "ondijitclick" triggers on a click or enter-down or space-up
		//		Similar to dojo.connect() but takes three arguments rather than four.
		var handles =[];
		if(event == "ondijitclick"){
			var w = this;
			// add key based click activation for unsupported nodes.
			if(!this.nodesWithKeyClick[obj.nodeName]){
				handles.push(dojo.connect(obj, "onkeydown", this,
					function(e){
						if(e.keyCode == dojo.keys.ENTER){
							return (dojo.isString(method))? 
								w[method](e) : method.call(w, e);
						}else if(e.keyCode == dojo.keys.SPACE){
							// stop space down as it causes IE to scroll
							// the browser window
							dojo.stopEvent(e);
						}
			 		}));
				handles.push(dojo.connect(obj, "onkeyup", this,
					function(e){
						if(e.keyCode == dojo.keys.SPACE){
							return dojo.isString(method) ? 
								w[method](e) : method.call(w, e);
						}
			 		}));
			}
			event = "onclick";
		}
		handles.push(dojo.connect(obj, event, this, method));

		// return handles for FormElement and ComboBox
		this._connects.push(handles);
		return handles;
	},

	disconnect: function(/*Object*/ handles){
		// summary:
		//		Disconnects handle created by this.connect.
		//		Also removes handle from this widget's list of connects
		for(var i=0; i<this._connects.length; i++){
			if(this._connects[i]==handles){
				dojo.forEach(handles, dojo.disconnect);
				this._connects.splice(i, 1);
				return;
			}
		}
	},

	isLeftToRight: function(){
		// summary:
		//		Checks the DOM to for the text direction for bi-directional support
		// description:
		//		This method cannot be used during widget construction because the widget
		//		must first be connected to the DOM tree.  Parent nodes are searched for the
		//		'dir' attribute until one is found, otherwise left to right mode is assumed.
		//		See HTML spec, DIR attribute for more information.

		if(typeof this._ltr == "undefined"){
			this._ltr = (this.dir || dojo.getComputedStyle(this.domNode).direction) != "rtl";
		}
		return this._ltr; //Boolean
	}
});

}

if(!dojo._hasResource["dojo.string"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.string"] = true;
dojo.provide("dojo.string");

dojo.string.pad = function(/*String*/text, /*int*/size, /*String?*/ch, /*boolean?*/end){
	// summary:
	//		Pad a string to guarantee that it is at least 'size' length by
	//		filling with the character 'c' at either the start or end of the
	//		string. Pads at the start, by default.
	// text: the string to pad
	// size: length to provide padding
	// ch: character to pad, defaults to '0'
	// end: adds padding at the end if true, otherwise pads at start

	var out = String(text);
	if(!ch){
		ch = '0';
	}
	while(out.length < size){
		if(end){
			out += ch;
		}else{
			out = ch + out;
		}
	}
	return out;	// String
};

dojo.string.substitute = function(	/*String*/template, 
									/*Object or Array*/map, 
									/*Function?*/transform, 
									/*Object?*/thisObject){
	// summary:
	//		Performs parameterized substitutions on a string. Throws an
	//		exception if any parameter is unmatched.
	// description:
	//		For example,
	//			dojo.string.substitute("File '${0}' is not found in directory '${1}'.",["foo.html","/temp"]);
	//			dojo.string.substitute("File '${name}' is not found in directory '${info.dir}'.",{name: "foo.html", info: {dir: "/temp"}});
	//		both return
	//			"File 'foo.html' is not found in directory '/temp'."
	// template: 
	//		a string with expressions in the form ${key} to be replaced or
	//		${key:format} which specifies a format function.  NOTE syntax has
	//		changed from %{key}
	// map: where to look for substitutions
	// transform: 
	//		a function to process all parameters before substitution takes
	//		place, e.g. dojo.string.encodeXML
	// thisObject: 
	//		where to look for optional format function; default to the global
	//		namespace

	return template.replace(/\$\{([^\s\:\}]+)(?:\:([^\s\:\}]+))?\}/g, function(match, key, format){
		var value = dojo.getObject(key,false,map);
		if(format){ value = dojo.getObject(format,false,thisObject)(value);}
		if(transform){ value = transform(value, key); }
		return value.toString();
	}); // string
};

dojo.string.trim = function(/*String*/ str){
	// summary: trims whitespaces from both sides of the string
	// description:
	//	This version of trim() was taken from Steven Levithan's blog: 
	//	http://blog.stevenlevithan.com/archives/faster-trim-javascript.
	//	The short yet good-performing version of this function is 
	//	dojo.trim(), which is part of the base.
	str = str.replace(/^\s+/, '');
	for(var i = str.length - 1; i > 0; i--){
		if(/\S/.test(str.charAt(i))){
			str = str.substring(0, i + 1);
			break;
		}
	}
	return str;	// String
};

}

if(!dojo._hasResource["dojo.date.stamp"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.date.stamp"] = true;
dojo.provide("dojo.date.stamp");

// Methods to convert dates to or from a wire (string) format using well-known conventions

dojo.date.stamp.fromISOString = function(/*String*/formattedString, /*Number?*/defaultTime){
	//	summary:
	//		Returns a Date object given a string formatted according to a subset of the ISO-8601 standard.
	//
	//	description:
	//		Accepts a string formatted according to a profile of ISO8601 as defined by
	//		RFC3339 (http://www.ietf.org/rfc/rfc3339.txt), except that partial input is allowed.
	//		Can also process dates as specified by http://www.w3.org/TR/NOTE-datetime
	//		The following combinations are valid:
	// 			* dates only
	//				yyyy
	//				yyyy-MM
	//				yyyy-MM-dd
	// 			* times only, with an optional time zone appended
	//				THH:mm
	//				THH:mm:ss
	//				THH:mm:ss.SSS
	// 			* and "datetimes" which could be any combination of the above
	//		timezones may be specified as Z (for UTC) or +/- followed by a time expression HH:mm
	//		Assumes the local time zone if not specified.  Does not validate.  Improperly formatted
	//		input may return null.  Arguments which are out of bounds will be handled
	// 		by the Date constructor (e.g. January 32nd typically gets resolved to February 1st)
	//
  	//	formattedString:
	//		A string such as 2005-06-30T08:05:00-07:00 or 2005-06-30 or T08:05:00
	//
	//	defaultTime:
	//		Used for defaults for fields omitted in the formattedString.
	//		Uses 1970-01-01T00:00:00.0Z by default.

	if(!dojo.date.stamp._isoRegExp){
		dojo.date.stamp._isoRegExp =
//TODO: could be more restrictive and check for 00-59, etc.
			/^(?:(\d{4})(?:-(\d{2})(?:-(\d{2}))?)?)?(?:T(\d{2}):(\d{2})(?::(\d{2})(.\d+)?)?((?:[+-](\d{2}):(\d{2}))|Z)?)?$/;
	}

	var match = dojo.date.stamp._isoRegExp.exec(formattedString);
	var result = null;

	if(match){
		match.shift();
		match[1] && match[1]--; // Javascript Date months are 0-based
		match[6] && (match[6] *= 1000); // Javascript Date expects fractional seconds as milliseconds

		if(defaultTime){
			// mix in defaultTime.  Relatively expensive, so use || operators for the fast path of defaultTime === 0
			defaultTime = new Date(defaultTime);
			dojo.map(["FullYear", "Month", "Date", "Hours", "Minutes", "Seconds", "Milliseconds"], function(prop){
				return defaultTime["get" + prop]();
			}).forEach(function(value, index){
				if(match[index] === undefined){
					match[index] = value;
				}
			});
		}
		result = new Date(match[0]||1970, match[1]||0, match[2]||0, match[3]||0, match[4]||0, match[5]||0, match[6]||0);

		var offset = 0;
		var zoneSign = match[7] && match[7].charAt(0);
		if(zoneSign != 'Z'){
			offset = ((match[8] || 0) * 60) + (Number(match[9]) || 0);
			if(zoneSign != '-'){ offset *= -1; }
		}
		if(zoneSign){
			offset -= result.getTimezoneOffset();
		}
		if(offset){
			result.setTime(result.getTime() + offset * 60000);
		}
	}

	return result; // Date or null
}

dojo.date.stamp.toISOString = function(/*Date*/dateObject, /*Object?*/options){
	//	summary:
	//		Format a Date object as a string according a subset of the ISO-8601 standard
	//
	//	description:
	//		When options.selector is omitted, output follows RFC3339 (http://www.ietf.org/rfc/rfc3339.txt)
	//		Times are formatted using the local time zone.  Does not check bounds.
	//
	//	dateObject:
	//		A Date object
	//
	//	object {selector: string, zulu: boolean, milliseconds: boolean}
	//		selector- "date" or "time" for partial formatting of the Date object.
	//			Both date and time will be formatted by default.
	//		zulu- if true, UTC/GMT is used for a timezone
	//		milliseconds- if true, output milliseconds

	var _ = function(n){ return (n < 10) ? "0" + n : n; }
	options = options || {};
	var formattedDate = [];
	var getter = options.zulu ? "getUTC" : "get";
	var date = "";
	if(options.selector != "time"){
		date = [dateObject[getter+"FullYear"](), _(dateObject[getter+"Month"]()+1), _(dateObject[getter+"Date"]())].join('-');
	}
	formattedDate.push(date);
	if(options.selector != "date"){
		var time = [_(dateObject[getter+"Hours"]()), _(dateObject[getter+"Minutes"]()), _(dateObject[getter+"Seconds"]())].join(':');
		var millis = dateObject[getter+"Milliseconds"]();
		if(options.milliseconds){
			time += "."+ (millis < 100 ? "0" : "") + _(millis);
		}
		if(options.zulu){
			time += "Z";
		}else{
			var timezoneOffset = dateObject.getTimezoneOffset();
			var absOffset = Math.abs(timezoneOffset);
			time += (timezoneOffset > 0 ? "-" : "+") + 
				_(Math.floor(absOffset/60)) + ":" + _(absOffset%60);
		}
		formattedDate.push(time);
	}
	return formattedDate.join('T'); // String
}

}

if(!dojo._hasResource["dojo.parser"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.parser"] = true;
dojo.provide("dojo.parser");


dojo.parser = new function(){

	var d = dojo;

	function val2type(/*Object*/ value){
		// summary:
		//		Returns name of type of given value.

		if(d.isString(value)){ return "string"; }
		if(typeof value == "number"){ return "number"; }
		if(typeof value == "boolean"){ return "boolean"; }
		if(d.isFunction(value)){ return "function"; }
		if(d.isArray(value)){ return "array"; } // typeof [] == "object"
		if(value instanceof Date) { return "date"; } // assume timestamp
		if(value instanceof d._Url){ return "url"; }
		return "object";
	}

	function str2obj(/*String*/ value, /*String*/ type){
		// summary:
		//		Convert given string value to given type
		switch(type){
			case "string":
				return value;
			case "number":
				return value.length ? Number(value) : NaN;
			case "boolean":
				// for checked/disabled value might be "" or "checked".  interpret as true.
				return typeof value == "boolean" ? value : !(value.toLowerCase()=="false");
			case "function":
				if(d.isFunction(value)){
					// IE gives us a function, even when we say something like onClick="foo"
					// (in which case it gives us an invalid function "function(){ foo }"). 
					//  Therefore, convert to string
					value=value.toString();
					value=d.trim(value.substring(value.indexOf('{')+1, value.length-1));
				}
				try{
					if(value.search(/[^\w\.]+/i) != -1){
						// TODO: "this" here won't work
						value = d.parser._nameAnonFunc(new Function(value), this);
					}
					return d.getObject(value, false);
				}catch(e){ return new Function(); }
			case "array":
				return value.split(/\s*,\s*/);
			case "date":
				switch(value){
					case "": return new Date("");	// the NaN of dates
					case "now": return new Date();	// current date
					default: return d.date.stamp.fromISOString(value);
				}
			case "url":
				return d.baseUrl + value;
			default:
				return d.fromJson(value);
		}
	}

	var instanceClasses = {
		// map from fully qualified name (like "dijit.Button") to structure like
		// { cls: dijit.Button, params: {label: "string", disabled: "boolean"} }
	};
	
	function getClassInfo(/*String*/ className){
		// className:
		//		fully qualified name (like "dijit.Button")
		// returns:
		//		structure like
		//			{ 
		//				cls: dijit.Button, 
		//				params: { label: "string", disabled: "boolean"}
		//			}

		if(!instanceClasses[className]){
			// get pointer to widget class
			var cls = d.getObject(className);
			if(!d.isFunction(cls)){
				throw new Error("Could not load class '" + className +
					"'. Did you spell the name correctly and use a full path, like 'dijit.form.Button'?");
			}
			var proto = cls.prototype;
	
			// get table of parameter names & types
			var params={};
			for(var name in proto){
				if(name.charAt(0)=="_"){ continue; } 	// skip internal properties
				var defVal = proto[name];
				params[name]=val2type(defVal);
			}

			instanceClasses[className] = { cls: cls, params: params };
		}
		return instanceClasses[className];
	}

	this._functionFromScript = function(script){
		var preamble = "";
		var suffix = "";
		var argsStr = script.getAttribute("args");
		if(argsStr){
			d.forEach(argsStr.split(/\s*,\s*/), function(part, idx){
				preamble += "var "+part+" = arguments["+idx+"]; ";
			});
		}
		var withStr = script.getAttribute("with");
		if(withStr && withStr.length){
			d.forEach(withStr.split(/\s*,\s*/), function(part){
				preamble += "with("+part+"){";
				suffix += "}";
			});
		}
		return new Function(preamble+script.innerHTML+suffix);
	}

	this._wireUpMethod = function(instance, script){
		var nf = this._functionFromScript(script);
		// if there's a destination, connect it to that, otherwise run it now
		var source = script.getAttribute("event");
		if(source){
			var mode = script.getAttribute("type");
			if(mode && (mode == "dojo/connect")){
				// FIXME: need to implement EL here!!
				d.connect(instance, source, instance, nf);
			}else{ 
				// otherwise we assume it's type "dojo/method", and therefore a
				// replacement
				instance[source] = nf;
			}
		}else{
			nf.call(instance);
		}
	}

	this.instantiate = function(nodes){
		// summary:
		//		Takes array of nodes, and turns them into class instances and
		//		potentially calls a layout method to allow them to connect with
		//		any children		
		var thelist = [];
		d.forEach(nodes, function(node){
			if(!node){ return; }
			var type = node.getAttribute("dojoType");
			if((!type)||(!type.length)){ return; }
			var clsInfo = getClassInfo(type);

			// read parameters (ie, attributes).
			// clsInfo.params lists expected params like {"checked": "boolean", "n": "number"}
			var params = {};
			var attributes = node.attributes;
			for(var name in clsInfo.params){
				var item = attributes.getNamedItem(name);
				if(!item || (!item.specified && (!dojo.isIE || name.toLowerCase()!="value"))){ continue; }
				var _type = clsInfo.params[name];
				params[name] = str2obj(item.value, _type);
			}

			// FIXME (perf): making two iterations of the DOM to find the
			// <script> elements feels dirty. Still need a separate iteration
			// if we do it another way, though, so we should probably benchmark
			// the various approaches at some point.

			// preambles are magic. Handle it.
			var preambles = d.query("> script[type='dojo/method'][event='preamble']", node).orphan();
			if(preambles.length){
				// we only support one preamble. So be it.
				params.preamble = d.parser._functionFromScript(preambles[0]);
			}

			// grab the rest of the scripts for processing later
			var scripts = d.query("> script[type^='dojo/']", node).orphan();

			var clazz = clsInfo.cls;
			var markupFactory = clazz["markupFactory"];
			if(!markupFactory && clazz["prototype"]){
				markupFactory = clazz.prototype["markupFactory"];
			}
			// create the instance
			var instance = markupFactory ? markupFactory(params, node, clazz) : new clazz(params, node);
			thelist.push(instance);

			// map it to the JS namespace if that makes sense
			var jsname = node.getAttribute("jsId");
			if(jsname){
				d.setObject(jsname, instance);
			}

			// check to see if we need to hook up events for non-declare()-built classes
			scripts.forEach(function(script){
				d.parser._wireUpMethod(instance, script);
			});
		});

		// Call startup on each top level instance if it makes sense (as for
		// widgets).  Parent widgets will recursively call startup on their
		// (non-top level) children
		d.forEach(thelist, function(instance){
			if(	instance  && 
				(instance.startup) && 
				((!instance.getParent) || (!instance.getParent()))
			){
				instance.startup();
			}
		});
		return thelist;
	};

	this.parse = function(/*DomNode?*/ rootNode){
		// summary:
		//		Search specified node (or root node) recursively for class instances,
		//		and instantiate them Searches for
		//		dojoType="qualified.class.name"
		var list = d.query('[dojoType]', rootNode);
		// go build the object instances
		var instances = this.instantiate(list);
		
		// FIXME: clean up any dangling scripts that we may need to run
		/*
		var scripts = d.query("script[type='dojo/method']", rootNode).orphan();
		scripts.forEach(function(script){
			wireUpMethod(instance, script);
		});
		*/

		return instances;
	};
}();

//Register the parser callback. It should be the first callback
//after the a11y test.

(function(){
	var parseRunner = function(){ 
		if(djConfig["parseOnLoad"] == true){
			dojo.parser.parse(); 
		}
	};

	// FIXME: need to clobber cross-dependency!!
	if(dojo.exists("dijit.wai.onload") && (dijit.wai.onload === dojo._loaders[0])){
		dojo._loaders.splice(1, 0, parseRunner);
	}else{
		dojo._loaders.unshift(parseRunner);
	}
})();

//TODO: ported from 0.4.x Dojo.  Can we reduce this?
dojo.parser._anonCtr = 0;
dojo.parser._anon = {}; // why is this property required?
dojo.parser._nameAnonFunc = function(/*Function*/anonFuncPtr, /*Object*/thisObj){
	// summary:
	//		Creates a reference to anonFuncPtr in thisObj with a completely
	//		unique name. The new name is returned as a String. 
	var jpn = "$joinpoint";
	var nso = (thisObj|| dojo.parser._anon);
	if(dojo.isIE){
		var cn = anonFuncPtr["__dojoNameCache"];
		if(cn && nso[cn] === anonFuncPtr){
			return anonFuncPtr["__dojoNameCache"];
		}
	}
	var ret = "__"+dojo.parser._anonCtr++;
	while(typeof nso[ret] != "undefined"){
		ret = "__"+dojo.parser._anonCtr++;
	}
	nso[ret] = anonFuncPtr;
	return ret; // String
}

}

if(!dojo._hasResource["dijit._Templated"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._Templated"] = true;
dojo.provide("dijit._Templated");






dojo.declare("dijit._Templated",
	null,
	{
		// summary:
		//		mixin for widgets that are instantiated from a template

		// templateNode: DomNode
		//		a node that represents the widget template. Pre-empts both templateString and templatePath.
		templateNode: null,

		// templateString String:
		//		a string that represents the widget template. Pre-empts the
		//		templatePath. In builds that have their strings "interned", the
		//		templatePath is converted to an inline templateString, thereby
		//		preventing a synchronous network call.
		templateString: null,

		// templatePath: String
		//	Path to template (HTML file) for this widget
		templatePath: null,

		// widgetsInTemplate Boolean:
		//		should we parse the template to find widgets that might be
		//		declared in markup inside it? false by default.
		widgetsInTemplate: false,

		// containerNode DomNode:
		//		holds child elements. "containerNode" is generally set via a
		//		dojoAttachPoint assignment and it designates where children of
		//		the src dom node will be placed
		containerNode: null,

		// method over-ride
		buildRendering: function(){
			// summary:
			//		Construct the UI for this widget from a template.
			// description:
			// Lookup cached version of template, and download to cache if it
			// isn't there already.  Returns either a DomNode or a string, depending on
			// whether or not the template contains ${foo} replacement parameters.

			var cached = dijit._Templated.getCachedTemplate(this.templatePath, this.templateString);

			var node;
			if(dojo.isString(cached)){
				var className = this.declaredClass, _this = this;
				// Cache contains a string because we need to do property replacement
				// do the property replacement
				var tstr = dojo.string.substitute(cached, this, function(value, key){
					if(key.charAt(0) == '!'){ value = _this[key.substr(1)]; }
					if(typeof value == "undefined"){ throw new Error(className+" template:"+key); } // a debugging aide

					// Substitution keys beginning with ! will skip the transform step,
					// in case a user wishes to insert unescaped markup, e.g. ${!foo}
					return key.charAt(0) == "!" ? value :
						// Safer substitution, see heading "Attribute values" in
						// http://www.w3.org/TR/REC-html40/appendix/notes.html#h-B.3.2
						value.toString().replace(/"/g,"&quot;"); //TODO: add &amp? use encodeXML method?
				}, this);

				node = dijit._Templated._createNodesFromText(tstr)[0];
			}else{
				// if it's a node, all we have to do is clone it
				node = cached.cloneNode(true);
			}

			// recurse through the node, looking for, and attaching to, our
			// attachment points which should be defined on the template node.
			this._attachTemplateNodes(node);
			if(this.srcNodeRef){
				dojo.style(this.styleNode || node, "cssText", this.srcNodeRef.style.cssText);
				if(this.srcNodeRef.className){
					node.className += " " + this.srcNodeRef.className;
				}
			}

			this.domNode = node;
			if(this.srcNodeRef && this.srcNodeRef.parentNode){
				this.srcNodeRef.parentNode.replaceChild(this.domNode, this.srcNodeRef);
			}
			if(this.widgetsInTemplate){
				var childWidgets = dojo.parser.parse(this.domNode);
				this._attachTemplateNodes(childWidgets, function(n,p){
					return n[p];
				});
			}

			this._fillContent(this.srcNodeRef);
		},

		_fillContent: function(/*DomNode*/ source){
			// summary:
			//		relocate source contents to templated container node
			//		this.containerNode must be able to receive children, or exceptions will be thrown
			var dest = this.containerNode;
			if(source && dest){
				while(source.hasChildNodes()){
					dest.appendChild(source.firstChild);
				}
			}
		},

		_attachTemplateNodes: function(rootNode, getAttrFunc){
			// summary:
			//		map widget properties and functions to the handlers specified in
			//		the dom node and it's descendants. This function iterates over all
			//		nodes and looks for these properties:
			//			* dojoAttachPoint
			//			* dojoAttachEvent	
			//			* waiRole
			//			* waiState
			// rootNode: DomNode|Array[Widgets]
			//		the node to search for properties. All children will be searched.
			// getAttrFunc: function?
			//		a function which will be used to obtain property for a given
			//		DomNode/Widget

			getAttrFunc = getAttrFunc || function(n,p){ return n.getAttribute(p); };

			var nodes = dojo.isArray(rootNode) ? rootNode : (rootNode.all || rootNode.getElementsByTagName("*"));
			var x=dojo.isArray(rootNode)?0:-1;
			for(; x<nodes.length; x++){
				var baseNode = (x == -1) ? rootNode : nodes[x];
				if(this.widgetsInTemplate && getAttrFunc(baseNode,'dojoType')){
					continue;
				}
				// Process dojoAttachPoint
				var attachPoint = getAttrFunc(baseNode, "dojoAttachPoint");
				if(attachPoint){
					var point, points = attachPoint.split(/\s*,\s*/);
					while(point=points.shift()){
						if(dojo.isArray(this[point])){
							this[point].push(baseNode);
						}else{
							this[point]=baseNode;
						}
					}
				}

				// Process dojoAttachEvent
				var attachEvent = getAttrFunc(baseNode, "dojoAttachEvent");
				if(attachEvent){
					// NOTE: we want to support attributes that have the form
					// "domEvent: nativeEvent; ..."
					var event, events = attachEvent.split(/\s*,\s*/);
					var trim = dojo.trim;
					while(event=events.shift()){
						if(event){
							var thisFunc = null;
							if(event.indexOf(":") != -1){
								// oh, if only JS had tuple assignment
								var funcNameArr = event.split(":");
								event = trim(funcNameArr[0]);
								thisFunc = trim(funcNameArr[1]);
							}else{
								event = trim(event);
							}
							if(!thisFunc){
								thisFunc = event;
							}
							this.connect(baseNode, event, thisFunc);
						}
					}
				}

				// waiRole, waiState
				var name, names = ["waiRole", "waiState"];
				while(name=names.shift()){
					var wai = dijit.wai[name];
					var values = getAttrFunc(baseNode, wai.name);
					if(values){
						var role = "role";
						var val;
						values = values.split(/\s*,\s*/);
						while(val=values.shift()){
							if(val.indexOf('-') != -1){
								// this is a state-value pair
								var statePair = val.split('-');
								role = statePair[0];
								val = statePair[1];
							}
							dijit.wai.setAttr(baseNode, wai.name, role, val);
						}
					}
				}

			}
		}
	}
);

// key is either templatePath or templateString; object is either string or DOM tree
dijit._Templated._templateCache = {};

dijit._Templated.getCachedTemplate = function(templatePath, templateString){
	// summary:
	//		static method to get a template based on the templatePath or
	//		templateString key
	// templatePath: String
	//		the URL to get the template from. dojo.uri.Uri is often passed as well.
	// templateString: String?
	//		a string to use in lieu of fetching the template from a URL
	// Returns:
	//	Either string (if there are ${} variables that need to be replaced) or just
	//	a DOM tree (if the node can be cloned directly)

	// is it already cached?
	var tmplts = dijit._Templated._templateCache;
	var key = templateString || templatePath;
	var cached = tmplts[key];
	if(cached){
		return cached;
	}

	// If necessary, load template string from template path
	if(!templateString){
		templateString = dijit._Templated._sanitizeTemplateString(dojo._getText(templatePath));
	}

	templateString = dojo.string.trim(templateString);

	if(templateString.match(/\$\{([^\}]+)\}/g)){
		// there are variables in the template so all we can do is cache the string
		return (tmplts[key] = templateString); //String
	}else{
		// there are no variables in the template so we can cache the DOM tree
		return (tmplts[key] = dijit._Templated._createNodesFromText(templateString)[0]); //Node
	}
};

dijit._Templated._sanitizeTemplateString = function(/*String*/tString){
	//summary: Strips <?xml ...?> declarations so that external SVG and XML
	//documents can be added to a document without worry. Also, if the string
	//is an HTML document, only the part inside the body tag is returned.
	if(tString){
		tString = tString.replace(/^\s*<\?xml(\s)+version=[\'\"](\d)*.(\d)*[\'\"](\s)*\?>/im, "");
		var matches = tString.match(/<body[^>]*>\s*([\s\S]+)\s*<\/body>/im);
		if(matches){
			tString = matches[1];
		}
	}else{
		tString = "";
	}
	return tString; //String
};


if(dojo.isIE){
	dojo.addOnUnload(function(){
		var cache = dijit._Templated._templateCache;
		for(var key in cache){
			var value = cache[key];
			if(!isNaN(value.nodeType)){ // isNode equivalent
				dojo._destroyElement(value);
			}
			cache[key] = null;
		}
	});
}

(function(){
	var tagMap = {
		cell: {re: /^<t[dh][\s\r\n>]/i, pre: "<table><tbody><tr>", post: "</tr></tbody></table>"},
		row: {re: /^<tr[\s\r\n>]/i, pre: "<table><tbody>", post: "</tbody></table>"},
		section: {re: /^<(thead|tbody|tfoot)[\s\r\n>]/i, pre: "<table>", post: "</table>"}
	};

	// dummy container node used temporarily to hold nodes being created
	var tn;

	dijit._Templated._createNodesFromText = function(/*String*/text){
		//	summary
		//	Attempts to create a set of nodes based on the structure of the passed text.

		if(!tn){
			tn = dojo.doc.createElement("div");
			tn.style.display="none";
		}
		var tableType = "none";
		var rtext = text.replace(/^\s+/, "");
		for(var type in tagMap){
			var map = tagMap[type];
			if(map.re.test(rtext)){
				tableType = type;
				text = map.pre + text + map.post;
				break;
			}
		}

		tn.innerHTML = text;
		dojo.body().appendChild(tn);
		if(tn.normalize){
			tn.normalize();
		}

		var tag = { cell: "tr", row: "tbody", section: "table" }[tableType];
		var _parent = (typeof tag != "undefined") ?
						tn.getElementsByTagName(tag)[0] :
						tn;

		var nodes = [];
		while(_parent.firstChild){
			nodes.push(_parent.removeChild(_parent.firstChild));
		}
		tn.innerHTML="";
		return nodes;	//	Array
	}
})();

// These arguments can be specified for widgets which are used in templates.
// Since any widget can be specified as sub widgets in template, mix it
// into the base widget class.  (This is a hack, but it's effective.)
dojo.extend(dijit._Widget,{
	dojoAttachEvent: "",
	dojoAttachPoint: "",
	waiRole: "",
	waiState:""
})

}

if(!dojo._hasResource["dojo.i18n"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.i18n"] = true;
dojo.provide("dojo.i18n");

dojo.i18n.getLocalization = function(/*String*/packageName, /*String*/bundleName, /*String?*/locale){
//	summary:
//		Returns an Object containing the localization for a given resource bundle
//		in a package, matching the specified locale.
//
//	description:
//		Returns a hash containing name/value pairs in its prototypesuch that values can be easily overridden.
//		Throws an exception if the bundle is not found.
//		Bundle must have already been loaded by dojo.requireLocalization() or by a build optimization step.
//
//	packageName: package which is associated with this resource
//	bundleName: the base filename of the resource bundle (without the ".js" suffix)
//	locale: the variant to load (optional).  By default, the locale defined by the
//		host environment: dojo.locale

	locale = dojo.i18n.normalizeLocale(locale);

	// look for nearest locale match
	var elements = locale.split('-');
	var module = [packageName,"nls",bundleName].join('.');
	var bundle = dojo._loadedModules[module];
	if(bundle){
		var localization;
		for(var i = elements.length; i > 0; i--){
			var loc = elements.slice(0, i).join('_');
			if(bundle[loc]){
				localization = bundle[loc];
				break;
			}
		}
		if(!localization){
			localization = bundle.ROOT;
		}

		// make a singleton prototype so that the caller won't accidentally change the values globally
		if(localization){
			var clazz = function(){};
			clazz.prototype = localization;
			return new clazz(); // Object
		}
	}

	throw new Error("Bundle not found: " + bundleName + " in " + packageName+" , locale=" + locale);
};

dojo.i18n.normalizeLocale = function(/*String?*/locale){
	//	summary:
	//		Returns canonical form of locale, as used by Dojo.
	//
	//  description:
	//		All variants are case-insensitive and are separated by '-' as specified in RFC 3066.
	//		If no locale is specified, the dojo.locale is returned.  dojo.locale is defined by
	//		the user agent's locale unless overridden by djConfig.

	var result = locale ? locale.toLowerCase() : dojo.locale;
	if(result == "root"){
		result = "ROOT";
	}
	return result; // String
};

dojo.i18n._requireLocalization = function(/*String*/moduleName, /*String*/bundleName, /*String?*/locale, /*String?*/availableFlatLocales){
	// summary:
	//	See dojo.requireLocalization()
	//
	// description:
	//  Called by the bootstrap, but factored out so that it is only included in the build when needed.

	var targetLocale = dojo.i18n.normalizeLocale(locale);
 	var bundlePackage = [moduleName, "nls", bundleName].join(".");
	// NOTE: 
	//		When loading these resources, the packaging does not match what is
	//		on disk.  This is an implementation detail, as this is just a
	//		private data structure to hold the loaded resources.  e.g.
	//		tests/hello/nls/en-us/salutations.js is loaded as the object
	//		tests.hello.nls.salutations.en_us={...} The structure on disk is
	//		intended to be most convenient for developers and translators, but
	//		in memory it is more logical and efficient to store in a different
	//		order.  Locales cannot use dashes, since the resulting path will
	//		not evaluate as valid JS, so we translate them to underscores.
	
	//Find the best-match locale to load if we have available flat locales.
	var bestLocale = "";
	if(availableFlatLocales){
		var flatLocales = availableFlatLocales.split(",");
		for(var i = 0; i < flatLocales.length; i++){
			//Locale must match from start of string.
			if(targetLocale.indexOf(flatLocales[i]) == 0){
				if(flatLocales[i].length > bestLocale.length){
					bestLocale = flatLocales[i];
				}
			}
		}
		if(!bestLocale){
			bestLocale = "ROOT";
		}		
	}

	//See if the desired locale is already loaded.
	var tempLocale = availableFlatLocales ? bestLocale : targetLocale;
	var bundle = dojo._loadedModules[bundlePackage];
	var localizedBundle = null;
	if(bundle){
		if(djConfig.localizationComplete && bundle._built){return;}
		var jsLoc = tempLocale.replace(/-/g, '_');
		var translationPackage = bundlePackage+"."+jsLoc;
		localizedBundle = dojo._loadedModules[translationPackage];
	}

	if(!localizedBundle){
		bundle = dojo["provide"](bundlePackage);
		var syms = dojo._getModuleSymbols(moduleName);
		var modpath = syms.concat("nls").join("/");
		var parent;

		dojo.i18n._searchLocalePath(tempLocale, availableFlatLocales, function(loc){
			var jsLoc = loc.replace(/-/g, '_');
			var translationPackage = bundlePackage + "." + jsLoc;
			var loaded = false;
			if(!dojo._loadedModules[translationPackage]){
				// Mark loaded whether it's found or not, so that further load attempts will not be made
				dojo["provide"](translationPackage);
				var module = [modpath];
				if(loc != "ROOT"){module.push(loc);}
				module.push(bundleName);
				var filespec = module.join("/") + '.js';
				loaded = dojo._loadPath(filespec, null, function(hash){
					// Use singleton with prototype to point to parent bundle, then mix-in result from loadPath
					var clazz = function(){};
					clazz.prototype = parent;
					bundle[jsLoc] = new clazz();
					for(var j in hash){ bundle[jsLoc][j] = hash[j]; }
				});
			}else{
				loaded = true;
			}
			if(loaded && bundle[jsLoc]){
				parent = bundle[jsLoc];
			}else{
				bundle[jsLoc] = parent;
			}
			
			if(availableFlatLocales){
				//Stop the locale path searching if we know the availableFlatLocales, since
				//the first call to this function will load the only bundle that is needed.
				return true;
			}
		});
	}

	//Save the best locale bundle as the target locale bundle when we know the
	//the available bundles.
	if(availableFlatLocales && targetLocale != bestLocale){
		bundle[targetLocale.replace(/-/g, '_')] = bundle[bestLocale.replace(/-/g, '_')];
	}
};

(function(){
	// If other locales are used, dojo.requireLocalization should load them as
	// well, by default. 
	// 
	// Override dojo.requireLocalization to do load the default bundle, then
	// iterate through the extraLocale list and load those translations as
	// well, unless a particular locale was requested.

	var extra = djConfig.extraLocale;
	if(extra){
		if(!extra instanceof Array){
			extra = [extra];
		}

		var req = dojo.i18n._requireLocalization;
		dojo.i18n._requireLocalization = function(m, b, locale, availableFlatLocales){
			req(m,b,locale, availableFlatLocales);
			if(locale){return;}
			for(var i=0; i<extra.length; i++){
				req(m,b,extra[i], availableFlatLocales);
			}
		};
	}
})();

dojo.i18n._searchLocalePath = function(/*String*/locale, /*Boolean*/down, /*Function*/searchFunc){
	//	summary:
	//		A helper method to assist in searching for locale-based resources.
	//		Will iterate through the variants of a particular locale, either up
	//		or down, executing a callback function.  For example, "en-us" and
	//		true will try "en-us" followed by "en" and finally "ROOT".

	locale = dojo.i18n.normalizeLocale(locale);

	var elements = locale.split('-');
	var searchlist = [];
	for(var i = elements.length; i > 0; i--){
		searchlist.push(elements.slice(0, i).join('-'));
	}
	searchlist.push(false);
	if(down){searchlist.reverse();}

	for(var j = searchlist.length - 1; j >= 0; j--){
		var loc = searchlist[j] || "ROOT";
		var stop = searchFunc(loc);
		if(stop){ break; }
	}
};

dojo.i18n._preloadLocalizations = function(/*String*/bundlePrefix, /*Array*/localesGenerated){
	// summary:
	//		Load built, flattened resource bundles, if available for all
	//		locales used in the page. Only called by built layer files.

	function preload(locale){
		locale = dojo.i18n.normalizeLocale(locale);
		dojo.i18n._searchLocalePath(locale, true, function(loc){
			for(var i=0; i<localesGenerated.length;i++){
				if(localesGenerated[i] == loc){
					dojo["require"](bundlePrefix+"_"+loc);
					return true; // Boolean
				}
			}
			return false; // Boolean
		});
	}
	preload();
	var extra = djConfig.extraLocale||[];
	for(var i=0; i<extra.length; i++){
		preload(extra[i]);
	}
};

}

if(!dojo._hasResource["dijit.layout.ContentPane"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.layout.ContentPane"] = true;
dojo.provide("dijit.layout.ContentPane");






dojo.declare(
	"dijit.layout.ContentPane",
	dijit._Widget,
{
	// summary:
	//		A widget that acts as a Container for other widgets, and includes a ajax interface
	// description:
	//		A widget that can be used as a standalone widget
	//		or as a baseclass for other widgets
	//		Handles replacement of document fragment using either external uri or javascript
	//		generated markup or DOM content, instantiating widgets within that content.
	//		Don't confuse it with an iframe, it only needs/wants document fragments.
	//		It's useful as a child of LayoutContainer, SplitContainer, or TabContainer.
	//		But note that those classes can contain any widget as a child.
	// usage:
	//		Some quick samples:
	//		To change the innerHTML use .setContent('<b>new content</b>')
	//
	//		Or you can send it a NodeList, .setContent(dojo.query('div [class=selected]', userSelection))
	//		please note that the nodes in NodeList will copied, not moved
	//
	//		To do a ajax update use .setHref('url')

	// href: String
	//		The href of the content that displays now.
	//		Set this at construction if you want to load data externally when the
	//		pane is shown.  (Set preload=true to load it immediately.)
	//		Changing href after creation doesn't have any effect; see setHref();
	href: "",

	// extractContent: Boolean
	//	Extract visible content from inside of <body> .... </body>
	extractContent: false,

	// parseOnLoad: Boolean
	//	parse content and create the widgets, if any
	parseOnLoad:	true,

	// preventCache: Boolean
	//		Cache content retreived externally
	preventCache:	false,

	// preload: Boolean
	//	Force load of data even if pane is hidden.
	preload: false,

	// refreshOnShow: Boolean
	//		Refresh (re-download) content when pane goes from hidden to shown
	refreshOnShow: false,

	// loadingMessage: String
	//	Message that shows while downloading
	loadingMessage: "<span class='dijitContentPaneLoading'>${loadingState}</span>", // TODO: consider a graphical representation for this state which does not require localization

	// errorMessage: String
	//	Message that shows if an error occurs
	errorMessage: "<span class='dijitContentPaneError'>${errorState}</span>", // TODO: consider a graphical representation for this state which does not require localization

	// isLoaded: Boolean
	//	Tells loading status see onLoad|onUnload for event hooks
	isLoaded: false,

	// class: String
	//	Class name to apply to ContentPane dom nodes
	"class": "dijitContentPane",

	postCreate: function(){
		// remove the title attribute so it doesn't show up when i hover
		// over a node
		this.domNode.title = "";

		if(this.preload){
			this._loadCheck();
		}

		var messages = dojo.i18n.getLocalization("dijit", "loading", this.lang);
		this.loadingMessage = dojo.string.substitute(this.loadingMessage, messages);
		this.errorMessage = dojo.string.substitute(this.errorMessage, messages);

		// for programatically created ContentPane (with <span> tag), need to muck w/CSS
		// or it's as though overflow:visible is set
		dojo.addClass(this.domNode, this["class"]);
	},

	startup: function(){
		if(!this._started){
			this._loadCheck();
			this._started = true;
		}
	},

	refresh: function(){
		// summary:
		//		Force a refresh (re-download) of content, be sure to turn off cache

		// we return result of _prepareLoad here to avoid code dup. in dojox.layout.ContentPane
		return this._prepareLoad(true);
	},

	setHref: function(/*String|Uri*/ href){
		// summary:
		//		Reset the (external defined) content of this pane and replace with new url
		//		Note: It delays the download until widget is shown if preload is false
		//	href:
		//		url to the page you want to get, must be within the same domain as your mainpage
		this.href = href;

		// we return result of _prepareLoad here to avoid code dup. in dojox.layout.ContentPane
		return this._prepareLoad();
	},

	setContent: function(/*String|DomNode|Nodelist*/data){
		// summary:
		//		Replaces old content with data content, include style classes from old content
		//	data:
		//		the new Content may be String, DomNode or NodeList
		//
		//		if data is a NodeList (or an array of nodes) nodes are copied
		//		so you can import nodes from another document implicitly

		// clear href so we cant run refresh and clear content
		// refresh should only work if we downloaded the content
		if(!this._isDownloaded){
			this.href = "";
			this._onUnloadHandler();
		}

		this._setContent(data || "");

		this._isDownloaded = false; // must be set after _setContent(..), pathadjust in dojox.layout.ContentPane

		if(this.parseOnLoad){
			this._createSubWidgets();
		}

		this._onLoadHandler();
	},

	cancel: function(){
		// summary
		//		Cancels a inflight download of content
		if(this._xhrDfd && (this._xhrDfd.fired == -1)){
			this._xhrDfd.cancel();
		}

		delete this._xhrDfd; // garbage collect
	},

	destroy: function(){
		// if we have multiple controllers destroying us, bail after the first
		if(this._beingDestroyed){
			return;
		}
		// make sure we call onUnload
		this._onUnloadHandler();
		this._beingDestroyed = true;
		dijit.layout.ContentPane.superclass.destroy.call(this);
	},

	resize: function(size){
		dojo.marginBox(this.domNode, size);
	},

	_prepareLoad: function(forceLoad){
		// sets up for a xhrLoad, load is deferred until widget onShow
		// cancels a inflight download
		this.cancel();
		this.isLoaded = false;
		this._loadCheck(forceLoad);
	},

	_loadCheck: function(forceLoad){
		// call this when you change onShow (onSelected) status when selected in parent container
		// it's used as a trigger for href download when this.domNode.display != 'none'

		// sequence:
		// if no href -> bail
		// forceLoad -> always load
		// this.preload -> load when download not in progress, domNode display doesn't matter
		// this.refreshOnShow -> load when download in progress bails, domNode display !='none' AND
		//						this.open !== false (undefined is ok), isLoaded doesn't matter
		// else -> load when download not in progress, if this.open !== false (undefined is ok) AND
		//						domNode display != 'none', isLoaded must be false

		var displayState = ((this.open !== false) && (this.domNode.style.display != 'none'));

		if(this.href &&	
			(forceLoad ||
				(this.preload && !this._xhrDfd) ||
				(this.refreshOnShow && displayState && !this._xhrDfd) ||
				(!this.isLoaded && displayState && !this._xhrDfd)
			)
		){
			this._downloadExternalContent();
		}
	},

	_downloadExternalContent: function(){
		this._onUnloadHandler();

		// display loading message
		// TODO: maybe we should just set a css class with a loading image as background?
		this._setContent(
			this.onDownloadStart.call(this)
		);

		var self = this;
		var getArgs = {
			preventCache: (this.preventCache || this.refreshOnShow),
			url: this.href,
			handleAs: "text"
		};
		if(dojo.isObject(this.ioArgs)){
			dojo.mixin(getArgs, this.ioArgs);
		}

		var hand = this._xhrDfd = (this.ioMethod || dojo.xhrGet)(getArgs);

		hand.addCallback(function(html){
			try{
				self.onDownloadEnd.call(self);
				self._isDownloaded = true;
				self.setContent.call(self, html); // onload event is called from here
			}catch(err){
				self._onError.call(self, 'Content', err); // onContentError
			}
			delete self._xhrDfd;
			return html;
		});

		hand.addErrback(function(err){
			if(!hand.cancelled){
				// show error message in the pane
				self._onError.call(self, 'Download', err); // onDownloadError
			}
			delete self._xhrDfd;
			return err;
		});
	},

	_onLoadHandler: function(){
		this.isLoaded = true;
		try{
			this.onLoad.call(this);
		}catch(e){
			console.error('Error '+this.widgetId+' running custom onLoad code');
		}
	},

	_onUnloadHandler: function(){
		this.isLoaded = false;
		this.cancel();
		try{
			this.onUnload.call(this);
		}catch(e){
			console.error('Error '+this.widgetId+' running custom onUnload code');
		}
	},

	_setContent: function(cont){
		this.destroyDescendants();

		try{
			var node = this.containerNode || this.domNode;
			while(node.firstChild){
				dojo._destroyElement(node.firstChild);
			}
			if(typeof cont == "string"){
				// dijit.ContentPane does only minimal fixes,
				// No pathAdjustments, script retrieval, style clean etc
				// some of these should be available in the dojox.layout.ContentPane
				if(this.extractContent){
					match = cont.match(/<body[^>]*>\s*([\s\S]+)\s*<\/body>/im);
					if(match){ cont = match[1]; }
				}
				node.innerHTML = cont;
			}else{
				// domNode or NodeList
				if(cont.nodeType){ // domNode (htmlNode 1 or textNode 3)
					node.appendChild(cont);
				}else{// nodelist or array such as dojo.Nodelist
					dojo.forEach(cont, function(n){
						node.appendChild(n.cloneNode(true));
					});
				}
			}
		}catch(e){
			// check if a domfault occurs when we are appending this.errorMessage
			// like for instance if domNode is a UL and we try append a DIV
			var errMess = this.onContentError(e);
			try{
				node.innerHTML = errMess;
			}catch(e){
				console.error('Fatal '+this.id+' could not change content due to '+e.message, e);
			}
		}
	},

	_onError: function(type, err, consoleText){
		// shows user the string that is returned by on[type]Error
		// overide on[type]Error and return your own string to customize
		var errText = this['on' + type + 'Error'].call(this, err);
		if(consoleText){
			console.error(consoleText, err);
		}else if(errText){// a empty string won't change current content
			this._setContent.call(this, errText);
		}
	},

	_createSubWidgets: function(){
		// summary: scan my contents and create subwidgets
		var rootNode = this.containerNode || this.domNode;
		try{
			dojo.parser.parse(rootNode, true);
		}catch(e){
			this._onError('Content', e, "Couldn't create widgets in "+this.id
				+(this.href ? " from "+this.href : ""));
		}
	},

	// EVENT's, should be overide-able
	onLoad: function(e){
		// summary:
		//		Event hook, is called after everything is loaded and widgetified
	},

	onUnload: function(e){
		// summary:
		//		Event hook, is called before old content is cleared
	},

	onDownloadStart: function(){
		// summary:
		//		called before download starts
		//		the string returned by this function will be the html
		//		that tells the user we are loading something
		//		override with your own function if you want to change text
		return this.loadingMessage;
	},

	onContentError: function(/*Error*/ error){
		// summary:
		//		called on DOM faults, require fault etc in content
		//		default is to display errormessage inside pane
	},

	onDownloadError: function(/*Error*/ error){
		// summary:
		//		Called when download error occurs, default is to display
		//		errormessage inside pane. Overide function to change that.
		//		The string returned by this function will be the html
		//		that tells the user a error happend
		return this.errorMessage;
	},

	onDownloadEnd: function(){
		// summary:
		//		called when download is finished
	}
});

}

if(!dojo._hasResource["dijit.form.Form"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.form.Form"] = true;
dojo.provide("dijit.form.Form");




dojo.declare("dijit.form._FormMixin", null,
	{
		/*
		summary: 
			Widget corresponding to <form> tag, for validation and serialization
		
		usage: 
			<form dojoType="dijit.form.Form" id="myForm">
				Name: <input type="text" name="name" />
			</form>
			myObj={name: "John Doe"};
			dijit.byId('myForm').setValues(myObj);

			myObj=dijit.byId('myForm').getValues();
		TODO:
		* Repeater
		* better handling for arrays.  Often form elements have names with [] like
		* people[3].sex (for a list of people [{name: Bill, sex: M}, ...])

		*/

		// execute: Function
		//	User defined function to do stuff when the user hits the submit button
		execute: function(/*Object*/ formContents){},

		// onCancel: Function
		//	Callback when user has canceled dialog, to notify container
		//	(user shouldn't override)
		onCancel: function(){},

		// onExecute: Function
		//	Callback when user is about to execute dialog, to notify container
		//	(user shouldn't override)
		onExecute: function(){},

		templateString: "<form dojoAttachPoint='containerNode' dojoAttachEvent='onsubmit:_onSubmit' enctype='multipart/form-data'></form>",

		_onSubmit: function(/*event*/e) {
			// summary: callback when user hits submit button
			dojo.stopEvent(e);
			this.onExecute();	// notify container that we are about to execute
			this.execute(this.getValues());
		},

		submit: function() {
			// summary: programatically submit form
			this.containerNode.submit();
		},

		setValues: function(/*object*/obj) {
			// summary: fill in form values from a JSON structure
			
			// generate map from name --> [list of widgets with that name]
			var map = {};
			dojo.forEach(this.getDescendants(), function(widget){
				if(!widget.name){ return; }
				var entry = map[widget.name] || (map[widget.name] = [] );
				entry.push(widget);
			});

			// call setValue() or setChecked() for each widget, according to obj
			for(var name in map){
				var widgets = map[name],						// array of widgets w/this name
					values = dojo.getObject(name, false, obj);	// list of values for those widgets
				if(!dojo.isArray(values)){
					values = [ values ];
				}
				if(widgets[0].setChecked){
					// for checkbox/radio, values is a list of which widgets should be checked
					dojo.forEach(widgets, function(w, i){
						w.setChecked(dojo.indexOf(values, w.value) != -1);
					});
				}else{
					// otherwise, values is a list of values to be assigned sequentially to each widget
					dojo.forEach(widgets, function(w, i){
						w.setValue(values[i]);
					});					
				}
			}
			
			/***
			 * 	TODO: code for plain input boxes (this shouldn't run for inputs that are part of widgets

			dojo.forEach(this.containerNode.elements, function(element){
				if (element.name == ''){return};	// like "continue"	
				var namePath = element.name.split(".");
				var myObj=obj;
				var name=namePath[namePath.length-1];
				for(var j=1,len2=namePath.length;j<len2;++j) {
					var p=namePath[j - 1];
					// repeater support block
					var nameA=p.split("[");
					if (nameA.length > 1) {
						if(typeof(myObj[nameA[0]]) == "undefined") {
							myObj[nameA[0]]=[ ];
						} // if

						nameIndex=parseInt(nameA[1]);
						if(typeof(myObj[nameA[0]][nameIndex]) == "undefined") {
							myObj[nameA[0]][nameIndex]={};
						}
						myObj=myObj[nameA[0]][nameIndex];
						continue;
					} // repeater support ends

					if(typeof(myObj[p]) == "undefined") {
						myObj=undefined;
						break;
					};
					myObj=myObj[p];
				}

				if (typeof(myObj) == "undefined") {
					return;		// like "continue"
				}
				if (typeof(myObj[name]) == "undefined" && this.ignoreNullValues) {
					return;		// like "continue"
				}

				// TODO: widget values (just call setValue() on the widget)

				switch(element.type) {
					case "checkbox":
						element.checked = (name in myObj) && 
							dojo.some(myObj[name], function(val){ return val==element.value; });
						break;
					case "radio":
						element.checked = (name in myObj) && myObj[name]==element.value;
						break;
					case "select-multiple":
						element.selectedIndex=-1;
						dojo.forEach(element.options, function(option){
							option.selected = dojo.some(myObj[name], function(val){ return option.value == val; });
						});
						break;
					case "select-one":
						element.selectedIndex="0";
						dojo.forEach(element.options, function(option){
							option.selected = option.value == myObj[name];
						});
						break;
					case "hidden":
					case "text":
					case "textarea":
					case "password":
						element.value = myObj[name] || "";
						break;
				}
      		});
      		*/
		},

		getValues: function() {
			// summary: generate JSON structure from form values

			// get widget values
			var obj = {};
			dojo.forEach(this.getDescendants(), function(widget){
				var value = widget.getValue ? widget.getValue() : widget.value;
				var name = widget.name;
				if(!name){ return; }

				// Store widget's value(s) as a scalar, except for checkboxes which are automatically arrays
				if(widget.setChecked){
					if(/Radio/.test(widget.declaredClass)){
						// radio button
						if(widget.checked){
							dojo.setObject(name, value, obj);
						}
					}else{
						// checkbox/toggle button
						var ary=dojo.getObject(name, false, obj);
						if(!ary){
							ary=[];
							dojo.setObject(name, ary, obj);
						}
						if(widget.checked){
							ary.push(value);
						}
					}
				}else{
					// plain input
					dojo.setObject(name, value, obj);
				}
			});

			/***
			 * code for plain input boxes (see also dojo.formToObject, can we use that instead of this code?
			 * but it doesn't understand [] notation, presumably)
			var obj = { };
			dojo.forEach(this.containerNode.elements, function(elm){
				if (!elm.name)	{
					return;		// like "continue"
				}
				var namePath = elm.name.split(".");
				var myObj=obj;
				var name=namePath[namePath.length-1];
				for(var j=1,len2=namePath.length;j<len2;++j) {
					var nameIndex = null;
					var p=namePath[j - 1];
					var nameA=p.split("[");
					if (nameA.length > 1) {
						if(typeof(myObj[nameA[0]]) == "undefined") {
							myObj[nameA[0]]=[ ];
						} // if
						nameIndex=parseInt(nameA[1]);
						if(typeof(myObj[nameA[0]][nameIndex]) == "undefined") {
							myObj[nameA[0]][nameIndex]={};
						}
					} else if(typeof(myObj[nameA[0]]) == "undefined") {
						myObj[nameA[0]]={}
					} // if

					if (nameA.length == 1) {
						myObj=myObj[nameA[0]];
					} else {
						myObj=myObj[nameA[0]][nameIndex];
					} // if
				} // for

				if ((elm.type != "select-multiple" && elm.type != "checkbox" && elm.type != "radio") || (elm.type=="radio" && elm.checked)) {
					if(name == name.split("[")[0]) {
						myObj[name]=elm.value;
					} else {
						// can not set value when there is no name
					}
				} else if (elm.type == "checkbox" && elm.checked) {
					if(typeof(myObj[name]) == 'undefined') {
						myObj[name]=[ ];
					}
					myObj[name].push(elm.value);
				} else if (elm.type == "select-multiple") {
					if(typeof(myObj[name]) == 'undefined') {
						myObj[name]=[ ];
					}
					for (var jdx=0,len3=elm.options.length; jdx<len3; ++jdx) {
						if (elm.options[jdx].selected) {
							myObj[name].push(elm.options[jdx].value);
						}
					}
				} // if
				name=undefined;
			}); // forEach
			***/
			return obj;
		},

	 	isValid: function() {
	 		// TODO: ComboBox might need time to process a recently input value.  This should be async?
	 		// make sure that every widget that has a validator function returns true
	 		return dojo.every(this.getDescendants(), function(widget){
	 			return !widget.isValid || widget.isValid();
	 		});
		}
	});
	
dojo.declare(
	"dijit.form.Form",
	[dijit._Widget, dijit._Templated, dijit.form._FormMixin],
	null
);

}

if(!dojo._hasResource["dijit.Dialog"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.Dialog"] = true;
dojo.provide("dijit.Dialog");









dojo.declare(
	"dijit.DialogUnderlay",
	[dijit._Widget, dijit._Templated],
	{
		// summary: the thing that grays out the screen behind the dialog
		
		// Template has two divs; outer div is used for fade-in/fade-out, and also to hold background iframe.
		// Inner div has opacity specified in CSS file.
		templateString: "<div class=dijitDialogUnderlayWrapper id='${id}_underlay'><div class=dijitDialogUnderlay dojoAttachPoint='node'></div></div>",
		
		postCreate: function(){
			dojo.body().appendChild(this.domNode);
			this.bgIframe = new dijit.BackgroundIframe(this.domNode);
		},

		layout: function(){
			// summary
			//		Sets the background to the size of the viewport (rather than the size
			//		of the document) since we need to cover the whole browser window, even
			//		if the document is only a few lines long.

			var viewport = dijit.getViewport();
			var is = this.node.style,
				os = this.domNode.style;

			os.top = viewport.t + "px";
			os.left = viewport.l + "px";
			is.width = viewport.w + "px";
			is.height = viewport.h + "px";
			
			// process twice since the scroll bar may have been removed
			// by the previous resizing
			var viewport2 = dijit.getViewport();
			if(viewport.w != viewport2.w){ is.width = viewport2.w + "px"; }
			if(viewport.h != viewport2.h){ is.height = viewport2.h + "px"; }
		},

		show: function(){
			this.domNode.style.display = "block";
			this.layout();
			if(this.bgIframe.iframe){
				this.bgIframe.iframe.style.display = "block";
			}
			this._resizeHandler = this.connect(window, "onresize", "layout");
		},

		hide: function(){
			this.domNode.style.display = "none";
			this.domNode.style.width = this.domNode.style.height = "1px";
			if(this.bgIframe.iframe){
				this.bgIframe.iframe.style.display = "none";
			}
			this.disconnect(this._resizeHandler);
		},

		uninitialize: function(){
			if(this.bgIframe){
				this.bgIframe.destroy();
			}
		}
	}
);
	
dojo.declare(
	"dijit.Dialog",
	[dijit.layout.ContentPane, dijit._Templated, dijit.form._FormMixin],
	{
		// summary:
		//		Pops up a modal dialog window, blocking access to the screen
		//		and also graying out the screen Dialog is extended from
		//		ContentPane so it supports all the same parameters (href, etc.)

		templateString: null,
		templateString:"<div class=\"dijitDialog\">\n\t\t<div dojoAttachPoint=\"titleBar\" class=\"dijitDialogTitleBar\" tabindex=\"0\" waiRole=\"dialog\" title=\"${title}\">\n\t\t<span dojoAttachPoint=\"titleNode\" class=\"dijitDialogTitle\">${title}</span>\n\t\t<span dojoAttachPoint=\"closeButtonNode\" class=\"dijitDialogCloseIcon\" dojoAttachEvent=\"onclick: hide\">\n\t\t\t<span dojoAttachPoint=\"closeText\" class=\"closeText\">x</span>\n\t\t</span>\n\t</div>\n\t\t<div dojoAttachPoint=\"containerNode\" class=\"dijitDialogPaneContent\"></div>\n\t<span dojoAttachPoint=\"tabEnd\" dojoAttachEvent=\"onfocus:_cycleFocus\" tabindex=\"0\"></span>\n</div>\n",

		// title: String
		//		Title of the dialog
		title: "",

		duration: 400,
		
		_lastFocusItem:null,
				
		postCreate: function(){
			dojo.body().appendChild(this.domNode);
			dijit.Dialog.superclass.postCreate.apply(this, arguments);
			this.domNode.style.display="none";
			this.connect(this, "onExecute", "hide");
			this.connect(this, "onCancel", "hide");
		},

		onLoad: function(){
			// when href is specified we need to reposition
			// the dialog after the data is loaded
			this._position();
			dijit.Dialog.superclass.onLoad.call(this);
		},

		_setup: function(){
			// summary:
			//		stuff we need to do before showing the Dialog for the first
			//		time (but we defer it until right beforehand, for
			//		performance reasons)

			this._modalconnects = [];

			if(this.titleBar){
				this._moveable = new dojo.dnd.Moveable(this.domNode, { handle: this.titleBar });
			}

			this._underlay = new dijit.DialogUnderlay();

			var node = this.domNode;
			this._fadeIn = dojo.fx.combine(
				[dojo.fadeIn({
					node: node,
					duration: this.duration
				 }),
				 dojo.fadeIn({
					node: this._underlay.domNode,
					duration: this.duration,
					onBegin: dojo.hitch(this._underlay, "show")
				 })
				]
			);

			this._fadeOut = dojo.fx.combine(
				[dojo.fadeOut({
					node: node,
					duration: this.duration,
					onEnd: function(){
						node.style.display="none";
					}
				 }),
				 dojo.fadeOut({
					node: this._underlay.domNode,
					duration: this.duration,
					onEnd: dojo.hitch(this._underlay, "hide")
				 })
				]
			);
		},

		uninitialize: function(){
			if(this._underlay){
				this._underlay.destroy();
			}
		},

		_position: function(){
			// summary: position modal dialog in center of screen

			var viewport = dijit.getViewport();
			var mb = dojo.marginBox(this.domNode);

			var style = this.domNode.style;
			style.left = (viewport.l + (viewport.w - mb.w)/2) + "px";
			style.top = (viewport.t + (viewport.h - mb.h)/2) + "px";
		},
		
		_findLastFocus: function(/*Event*/ evt){
			// summary:  called from onblur of dialog container to determine the last focusable item 
			this._lastFocused = evt.target;
		},
		
		_cycleFocus: function(/*Event*/ evt){
			// summary: when tabEnd receives focus, advance focus around to titleBar
			
			// on first focus to tabEnd, store the last focused item in dialog
			if(!this._lastFocusItem){
				this._lastFocusItem = this._lastFocused;
			}
			this.titleBar.focus();
		},

		_onKey: function(/*Event*/ evt){
			if(evt.keyCode){
				var node = evt.target;
				// see if we are shift-tabbing from titleBar
				if(node == this.titleBar && evt.shiftKey && evt.keyCode == dojo.keys.TAB){
					if (this._lastFocusItem){
						this._lastFocusItem.focus(); // send focus to last item in dialog if known
					}
					dojo.stopEvent(evt);
				}else{
					// see if the key is for the dialog
					while (node){
						if(node == this.domNode){
							if (evt.keyCode == dojo.keys.ESCAPE){
								this.hide(); 
							}else{
								return; // just let it go
							}
						}
						node = node.parentNode;
					}
					// this key is for the disabled document window
					if (evt.keyCode != dojo.keys.TAB){ // allow tabbing into the dialog for a11y
						dojo.stopEvent(evt);
					// opera won't tab to a div
					}else if (!dojo.isOpera){
						try{
							this.titleBar.focus();
						}catch(e){/*squelch*/}
					}
				}
			}
		},

		show: function(){
			// summary: display the dialog

			// first time we show the dialog, there's some initialization stuff to do			
			if(!this._alreadyInitialized){
				this._setup();
				this._alreadyInitialized=true;
			}

			if(this._fadeOut.status() == "playing"){
				this._fadeOut.stop();
			}

			this._modalconnects.push(dojo.connect(window, "onscroll", this, "layout"));
			this._modalconnects.push(dojo.connect(document.documentElement, "onkeypress", this, "_onKey"));
			
			// IE doesn't bubble onblur events - use ondeactivate instead
			var ev = typeof(document.ondeactivate) == "object" ? "ondeactivate" : "onblur";
			this._modalconnects.push(dojo.connect(this.containerNode, ev, this, "_findLastFocus"));
			
			
			dojo.style(this.domNode, "opacity", 0);
			this.domNode.style.display="block";

			this._loadCheck(); // lazy load trigger

			this._position();

			this._fadeIn.play();
			
			this._savedFocus = dijit.getFocus(this);
			
			// set timeout to allow the browser to render dialog
			setTimeout(dojo.hitch(this, function(){
				dijit.focus(this.titleBar);
			}), 50);
		},

		hide: function(){
			// summary
			//		Hide the dialog

			// if we haven't been initialized yet then we aren't showing and we can just return		
			if(!this._alreadyInitialized){
				return;
			}

			if(this._fadeIn.status() == "playing"){
				this._fadeIn.stop();
			}
			this._fadeOut.play();

			if (this._scrollConnected){
				this._scrollConnected = false;
			}
			dojo.forEach(this._modalconnects, dojo.disconnect);
			this._modalconnects = [];

			// TODO: this is failing on FF presumably because the DialogUnderlay hasn't disappeared yet?
			// Attach it to fire at the end of the animation
			dijit.focus(this._savedFocus);
		},

		layout: function() {
			if(this.domNode.style.display == "block"){
				this._underlay.layout();
				this._position();
			}
		}
	}
);
	
dojo.declare(
	"dijit.TooltipDialog",
	[dijit.layout.ContentPane, dijit._Templated, dijit.form._FormMixin],
	{
		// summary:
		//		Pops up a dialog that appears like a Tooltip

		// title: String
		// Description of tooltip dialog (required for a11Y)
		title: "",

		_lastFocusItem: null,

		templateString: null,
		templateString:"<div id=\"${id}\" class=\"dijitTooltipDialog\" >\n\t<div class=\"dijitTooltipContainer\">\n\t\t<div  class =\"dijitTooltipContents dijitTooltipFocusNode\" dojoAttachPoint=\"containerNode\" tabindex=\"0\" waiRole=\"dialog\"></div>\n\t</div>\n\t<span dojoAttachPoint=\"tabEnd\" tabindex=\"0\" dojoAttachEvent=\"focus:_cycleFocus\"></span>\n\t<div class=\"dijitTooltipConnector\" ></div>\n</div>\n",

		postCreate: function(){
			dijit.TooltipDialog.superclass.postCreate.apply(this, arguments);
			this.connect(this.containerNode, "onkeypress", "_onKey");

			// IE doesn't bubble onblur events - use ondeactivate instead
			var ev = typeof(document.ondeactivate) == "object" ? "ondeactivate" : "onblur";
			this.connect(this.containerNode, ev, "_findLastFocus");
			this.containerNode.title=this.title;
		},

		orient: function(/*Object*/ corner){
			// summary: configure widget to be displayed in given position relative to the button
			this.domNode.className="dijitTooltipDialog " +" dijitTooltipAB"+(corner.charAt(1)=='L'?"Left":"Right")+" dijitTooltip"+(corner.charAt(0)=='T' ? "Below" : "Above");
		},

		onOpen: function(/*Object*/ pos){
			// summary: called when dialog is displayed
			this.orient(pos.corner);
			this._loadCheck(); // lazy load trigger
			this.containerNode.focus();
		},
		
		_onKey: function(/*Event*/ evt){
			//summary: keep keyboard focus in dialog; close dialog on escape key
			if (evt.keyCode == dojo.keys.ESCAPE){
				this.onCancel();
			}else if (evt.target == this.containerNode && evt.shiftKey && evt.keyCode == dojo.keys.TAB){
				if (this._lastFocusItem){
					this._lastFocusItem.focus();
				}
				dojo.stopEvent(evt);
			}
		},
		
		_findLastFocus: function(/*Event*/ evt){
			// summary:  called from onblur of dialog container to determine the last focusable item 
			this._lastFocused = evt.target;
		},

		_cycleFocus: function(/*Event*/ evt){
			// summary: when tabEnd receives focus, advance focus around to containerNode
			
			// on first focus to tabEnd, store the last focused item in dialog
			if(!this._lastFocusItem){
				this._lastFocusItem = this._lastFocused;
			}
			this.containerNode.focus();
		}
	}	
);


}

if(!dojo._hasResource["dijit._editor.selection"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._editor.selection"] = true;
dojo.provide("dijit._editor.selection");

// FIXME:
//		all of these methods branch internally for IE. This is probably
//		sub-optimal in terms of runtime performance. We should investigate the
//		size difference for differentiating at definition time.

dojo.mixin(dijit._editor.selection, {
	getType: function(){
		// summary: Get the selection type (like document.select.type in IE).
		if(dojo.doc["selection"]){ //IE
			return dojo.doc.selection.type.toLowerCase();
		}else{
			var stype = "text";

			// Check if the actual selection is a CONTROL (IMG, TABLE, HR, etc...).
			var oSel;
			try{
				oSel = dojo.global.getSelection();
			}catch(e){ /*squelch*/ }

			if(oSel && oSel.rangeCount==1){
				var oRange = oSel.getRangeAt(0);
				if(	(oRange.startContainer == oRange.endContainer) &&
					((oRange.endOffset - oRange.startOffset) == 1) &&
					(oRange.startContainer.nodeType != 3 /* text node*/)
				){
					stype = "control";
				}
			}
			return stype;
		}
	},

	getSelectedText: function(){
		// summary:
		//		Return the text (no html tags) included in the current selection or null if no text is selected
		if(dojo.doc["selection"]){ //IE
			if(dijit._editor.selection.getType() == 'control'){
				return null;
			}
			return dojo.doc.selection.createRange().text;
		}else{
			var selection = dojo.global.getSelection();
			if(selection){
				return selection.toString();
			}
		}
	},

	getSelectedHtml: function(){
		// summary:
		//		Return the html of the current selection or null if unavailable
		if(dojo.doc["selection"]){ //IE
			if(dijit._editor.selection.getType() == 'control'){
				return null;
			}
			return dojo.doc.selection.createRange().htmlText;
		}else{
			var selection = dojo.global.getSelection();
			if(selection && selection.rangeCount){
				var frag = selection.getRangeAt(0).cloneContents();
				var div = document.createElement("div");
				div.appendChild(frag);
				return div.innerHTML;
			}
			return null;
		}
	},

	getSelectedElement: function(){
		// summary:
		//		Retrieves the selected element (if any), just in the case that
		//		a single element (object like and image or a table) is
		//		selected.
		if(this.getType() == "control"){
			if(dojo.doc["selection"]){ //IE
				var range = dojo.doc.selection.createRange();
				if(range && range.item){
					return dojo.doc.selection.createRange().item(0);
				}
			}else{
				var selection = dojo.global.getSelection();
				return selection.anchorNode.childNodes[ selection.anchorOffset ];
			}
		}
	},

	getParentElement: function(){
		// summary:
		//		Get the parent element of the current selection
		if(this.getType() == "control"){
			var p = this.getSelectedElement();
			if(p){ return p.parentNode; }
		}else{
			if(dojo.doc["selection"]){ //IE
				return dojo.doc.selection.createRange().parentElement();
			}else{
				var selection = dojo.global.getSelection();
				if(selection){
					var node = selection.anchorNode;

					while(node && (node.nodeType != 1)){ // not an element
						node = node.parentNode;
					}

					return node;
				}
			}
		}
	},

	hasAncestorElement: function(/*String*/tagName /* ... */){
		// summary:
		// 		Check whether current selection has a  parent element which is
		// 		of type tagName (or one of the other specified tagName)
		return (this.getAncestorElement.apply(this, arguments) != null);
	},

	getAncestorElement: function(/*String*/tagName /* ... */){
		// summary:
		//		Return the parent element of the current selection which is of
		//		type tagName (or one of the other specified tagName)

		var node = this.getSelectedElement() || this.getParentElement();
		return this.getParentOfType(node, arguments);
	},

	isTag: function(/*DomNode*/node, /*Array*/tags){
		if(node && node.tagName){
			var _nlc = node.tagName.toLowerCase();
			for(var i=0; i<tags.length; i++){
				var _tlc = String(tags[i]).toLowerCase();
				if(_nlc == _tlc){
					return _tlc;
				}
			}
		}
		return "";
	},

	getParentOfType: function(/*DomNode*/node, /*Array*/tags){
		while(node){
			if(this.isTag(node, tags).length){
				return node;
			}
			node = node.parentNode;
		}
		return null;
	},

	remove: function(){
		// summary: delete current selection
		var _s = dojo.doc.selection;
		if(_s){ //IE
			if(_s.type.toLowerCase() != "none"){
				_s.clear();
			}
			return _s;
		}else{
			_s = dojo.global.getSelection();
			_s.deleteFromDocument();
			return _s;
		}
	},

	selectElementChildren: function(/*DomNode*/element,/*Boolean?*/nochangefocus){
		// summary:
		//		clear previous selection and select the content of the node
		//		(excluding the node itself)
		var _window = dojo.global;
		var _document = dojo.doc;
		element = dojo.byId(element);
		if(_document.selection && dojo.body().createTextRange){ // IE
			var range = element.ownerDocument.body.createTextRange();
			range.moveToElementText(element);
			if(!nochangefocus){
				range.select();
			}
		}else if(_window["getSelection"]){
			var selection = _window.getSelection();
			if(selection["setBaseAndExtent"]){ // Safari
				selection.setBaseAndExtent(element, 0, element, element.innerText.length - 1);
			}else if(selection["selectAllChildren"]){ // Mozilla
				selection.selectAllChildren(element);
			}
		}
	},

	selectElement: function(/*DomNode*/element,/*Boolean?*/nochangefocus){
		// summary:
		//		clear previous selection and select element (including all its children)
		var _document = dojo.doc;
		element = dojo.byId(element);
		if(_document.selection && dojo.body().createTextRange){ // IE
			try{
				var range = dojo.body().createControlRange();
				range.addElement(element);
				if(!nochangefocus){
					range.select();
				}
			}catch(e){
				this.selectElementChildren(element,nochangefocus);
			}
		}else if(dojo.global["getSelection"]){
			var selection = dojo.global.getSelection();
			// FIXME: does this work on Safari?
			if(selection["removeAllRanges"]){ // Mozilla
				var range = _document.createRange() ;
				range.selectNode(element) ;
				selection.removeAllRanges() ;
				selection.addRange(range) ;
			}
		}
	}
});

}

if(!dojo._hasResource["dijit._editor.RichText"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._editor.RichText"] = true;
dojo.provide("dijit._editor.RichText");




// used to save content
// but do not try doing document.write if we are using xd loading.
// document.write will only work if RichText.js is included in the dojo.js
// file. If it is included in dojo.js and you want to allow rich text saving
// for back/forward actions, then set djConfig.allowXdRichTextSave = true.
if(!djConfig["useXDomain"] || djConfig["allowXdRichTextSave"]){
	if(dojo._post_load){
		(function(){
			var savetextarea = dojo.doc.createElement('textarea');
			savetextarea.id = "dijit._editor.RichText.savedContent";
			var s = savetextarea.style;
			s.display='none';
			s.position='absolute';
			s.top="-100px";
			s.left="-100px"
			s.height="3px";
			s.width="3px";
			dojo.body().appendChild(savetextarea);
		})();
	}else{
		//dojo.body() is not available before onLoad is fired
		try {
			dojo.doc.write('<textarea id="dijit._editor.RichText.savedContent" ' +
				'style="display:none;position:absolute;top:-100px;left:-100px;height:3px;width:3px;overflow:hidden;"></textarea>');
		}catch(e){ }
	}
}
dojo.declare("dijit._editor.RichText", [ dijit._Widget ], {
	preamble: function(){
		// summary:
		//		dijit._editor.RichText is the core of the WYSIWYG editor in dojo, which
		//		provides the basic editing features. It also encapsulates the differences
		//		of different js engines for various browsers

		// contentPreFilters: Array
		//		pre content filter function register array.
		//		these filters will be executed before the actual
		//		editing area get the html content
		this.contentPreFilters = [];

		// contentPostFilters: Array
		//		post content filter function register array.
		//		these will be used on the resulting html
		//		from contentDomPostFilters. The resuling
		//		content is the final html (returned by getValue())
		this.contentPostFilters = [];

		// contentDomPreFilters: Array
		//		pre content dom filter function register array.
		//		these filters are applied after the result from
		//		contentPreFilters are set to the editing area
		this.contentDomPreFilters = [];

		// contentDomPostFilters: Array
		//		post content dom filter function register array.
		//		these filters are executed on the editing area dom
		//		the result from these will be passed to contentPostFilters
		this.contentDomPostFilters = [];

		// editingAreaStyleSheets: Array
		//		array to store all the stylesheets applied to the editing area
		this.editingAreaStyleSheets=[];

		this._keyHandlers = {};
		this.contentPreFilters.push(dojo.hitch(this, "_preFixUrlAttributes"));
		if(dojo.isMoz){
			this.contentPreFilters.push(this._fixContentForMoz);
		}
		//this.contentDomPostFilters.push(this._postDomFixUrlAttributes);


		this.onLoadDeferred = new dojo.Deferred();
	},

	// inheritWidth: Boolean
	//		whether to inherit the parent's width or simply use 100%
	inheritWidth: false,

	// focusOnLoad: Boolean
	//		whether focusing into this instance of richtext when page onload
	focusOnLoad: false,

	// name: String
	//		If a save name is specified the content is saved and restored when the user
	//		leave this page can come back, or if the editor is not properly closed after
	//		editing has started.
	name: "",

	// styleSheets: String
	//		semicolon (";") separated list of css files for the editing area
	styleSheets: "",

	// _content: String
	//		temporary content storage
	_content: "",

	// height: String
	//		set height to fix the editor at a specific height, with scrolling
	height: "",

	// minHeight: String
	//		The minimum height that the editor should have
	minHeight: "1em",

	// isClosed: Boolean
	isClosed: true,

	// isLoaded: Boolean
	isLoaded: false,

	// _SEPARATOR: String
	//		used to concat contents from multiple textareas into a single string
	_SEPARATOR: "@@**%%__RICHTEXTBOUNDRY__%%**@@",

	// onLoadDeferred: dojo.Deferred
	//		deferred that can be used to connect to the onLoad function. This
	//		will only be set if dojo.Deferred is required
	onLoadDeferred: null,

	// Init
	postCreate: function(){
		dojo.publish("dijit._editor.RichText::init", [this]);
		this.open();
		this.setupDefaultShortcuts();
	},

	setupDefaultShortcuts: function(){
		// summary: add some default key handlers
		// description:
		// 		Overwrite this to setup your own handlers. The default
		// 		implementation does not use Editor commands, but directly
		//		executes the builtin commands within the underlying browser
		//		support.
		var ctrl = this.KEY_CTRL;
		var exec = function(cmd, arg){
			return arguments.length == 1 ? function(){ this.execCommand(cmd); } :
				function(){ this.execCommand(cmd, arg); }
		}
		this.addKeyHandler("b", ctrl, exec("bold"));
		this.addKeyHandler("i", ctrl, exec("italic"));
		this.addKeyHandler("u", ctrl, exec("underline"));
		this.addKeyHandler("a", ctrl, exec("selectall"));
		this.addKeyHandler("s", ctrl, function () { this.save(true); });

		this.addKeyHandler("1", ctrl, exec("formatblock", "h1"));
		this.addKeyHandler("2", ctrl, exec("formatblock", "h2"));
		this.addKeyHandler("3", ctrl, exec("formatblock", "h3"));
		this.addKeyHandler("4", ctrl, exec("formatblock", "h4"));

		this.addKeyHandler("\\", ctrl, exec("insertunorderedlist"));
		if(!dojo.isIE){
			this.addKeyHandler("Z", ctrl, exec("redo"));
		}
	},

	// events: Array
	//		 events which should be connected to the underlying editing area
	events: ["onKeyPress", "onKeyDown", "onKeyUp", "onClick"],

	// events: Array
	//		 events which should be connected to the underlying editing
	//		 area, events in this array will be addListener with
	//		 capture=true
	captureEvents: [],

	_editorCommandsLocalized: false,
	_localizeEditorCommands: function(){
		if(this._editorCommandsLocalized){
			return;
		}
		this._editorCommandsLocalized = true;
		
		//in IE, names for blockformat is locale dependent, so we cache the values here

		//if the normal way fails, we try the hard way to get the list
	
		//do not use _cacheLocalBlockFormatNames here, as it will
		//trigger security warning in IE7

		//in the array below, ul can not come directly after ol,
		//otherwise the queryCommandValue returns Normal for it
		var formats = ['p', 'pre', 'address', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ol', 'div', 'ul'];
		var localhtml = "", format, i=0;
		while(format=formats[i++]){
			if(format.charAt(1) != 'l'){
				localhtml += "<"+format+"><span>content</span></"+format+">";
			}else{
				localhtml += "<"+format+"><li>content</li></"+format+">";
			}
		}
		//queryCommandValue returns empty if we hide editNode, so move it out of screen temporary
		var div=document.createElement('div');
		div.style.position = "absolute";
		div.style.left = "-2000px";
		div.style.top = "-2000px";
		document.body.appendChild(div);
		div.innerHTML = localhtml;
		var node = div.firstChild;
		while(node){
			dijit._editor.selection.selectElement(node.firstChild);
			dojo.withGlobal(this.window, "selectElement", dijit._editor.selection, [node.firstChild]);
			var nativename = node.tagName.toLowerCase();
			this._local2NativeFormatNames[nativename] = document.queryCommandValue("formatblock");//this.queryCommandValue("formatblock");
			this._native2LocalFormatNames[this._local2NativeFormatNames[nativename]] = nativename;
			node = node.nextSibling;
		}
		document.body.removeChild(div);
	},

	open: function(/*DomNode, optional*/element){
		// summary:
		//		Transforms the node referenced in this.domNode into a rich text editing
		//		node. This will result in the creation and replacement with an <iframe>
		//		if designMode(FF)/contentEditable(IE) is used.
		
		if((!this.onLoadDeferred)||(this.onLoadDeferred.fired >= 0)){
			this.onLoadDeferred = new dojo.Deferred();
		}
		
		if(!this.isClosed){ this.close(); }
		dojo.publish("dijit._editor.RichText::open", [ this ]);
		
		this._content = "";
		if((arguments.length == 1)&&(element["nodeName"])){ this.domNode = element; } // else unchanged
		
		if(	(this.domNode["nodeName"])&&
			(this.domNode.nodeName.toLowerCase() == "textarea")){
			// if we were created from a textarea, then we need to create a
			// new editing harness node.
			this.textarea = this.domNode;
			this.name=this.textarea.name;
			var html = this._preFilterContent(this.textarea.value);
			this.domNode = dojo.doc.createElement("div");
			this.domNode.setAttribute('widgetId',this.id);
			this.textarea.removeAttribute('widgetId');
			this.domNode.cssText = this.textarea.cssText;
			this.domNode.className += " "+this.textarea.className;
			dojo.place(this.domNode, this.textarea, "before");
			var tmpFunc = dojo.hitch(this, function(){
				//some browsers refuse to submit display=none textarea, so
				//move the textarea out of screen instead
				with(this.textarea.style){
					display = "block";
					position = "absolute";
					left = top = "-1000px";

					if(dojo.isIE){ //nasty IE bug: abnormal formatting if overflow is not hidden
						this.__overflow = overflow;
						overflow = "hidden";
					}
				}
			});
			if(dojo.isIE){
				setTimeout(tmpFunc, 10);
			}else{
				tmpFunc();
			}

			// this.domNode.innerHTML = html;

//				if(this.textarea.form){
//					// FIXME: port: this used to be before advice!!!
//					dojo.connect(this.textarea.form, "onsubmit", this, function(){
//						// FIXME: should we be calling close() here instead?
//						this.textarea.value = this.getValue();
//					});
//				}
		}else{
			var html = this._preFilterContent(this.getNodeChildrenHtml(this.domNode));
			this.domNode.innerHTML = '';
		}
		if(html == ""){ html = "&nbsp;"; }

		var content = dojo.contentBox(this.domNode);
		// var content = dojo.contentBox(this.srcNodeRef);
		this._oldHeight = content.h;
		this._oldWidth = content.w;

		this.savedContent = html;

		// If we're a list item we have to put in a blank line to force the
		// bullet to nicely align at the top of text
		if(	(this.domNode["nodeName"]) &&
			(this.domNode.nodeName == "LI") ){
			this.domNode.innerHTML = " <br>";
		}

		this.editingArea = dojo.doc.createElement("div");
		this.domNode.appendChild(this.editingArea);

		if(this.name != "" && (!djConfig["useXDomain"] || djConfig["allowXdRichTextSave"])){
			var saveTextarea = dojo.byId("dijit._editor.RichText.savedContent");
			if(saveTextarea.value != ""){
				var datas = saveTextarea.value.split(this._SEPARATOR), i=0, dat;
				while(dat=datas[i++]){
					var data = dat.split(":");
					if(data[0] == this.name){
						html = data[1];
						datas.splice(i, 1);
						break;
					}
				}
			}
			// FIXME: need to do something different for Opera/Safari
			dojo.connect(window, "onbeforeunload", this, "_saveContent");
			// dojo.connect(window, "onunload", this, "_saveContent");
		}

		this.isClosed = false;
		// Safari's selections go all out of whack if we do it inline,
		// so for now IE is our only hero
		//if (typeof document.body.contentEditable != "undefined") {
		if(dojo.isIE || dojo.isSafari || dojo.isOpera){ // contentEditable, easy
			var ifr = this.iframe = dojo.doc.createElement('iframe');
			ifr.src = 'javascript:void(0)';
			this.editorObject = ifr;
			ifr.style.border = "none";
			ifr.style.width = "100%";
			ifr.frameBorder = 0;
//			ifr.style.scrolling = this.height ? "auto" : "vertical";
			this.editingArea.appendChild(ifr);
			this.window = ifr.contentWindow;
			this.document = this.window.document;
			this.document.open();
			this.document.write(this._getIframeDocTxt(html));
			this.document.close();

			if(dojo.isIE >= 7){
				if(this.height){
					ifr.style.height = this.height;
				}
				if(this.minHeight){
					ifr.style.minHeight = this.minHeight;
				}
			}else{
				ifr.style.height = this.height ? this.height : this.minHeight;
			}

			if(dojo.isIE){
				this._localizeEditorCommands();
			}
			
			this.onLoad();
		}else{ // designMode in iframe
			this._drawIframe(html);
		}

		// TODO: this is a guess at the default line-height, kinda works
		if(this.domNode.nodeName == "LI"){ this.domNode.lastChild.style.marginTop = "-1.2em"; }
		this.domNode.className += " RichTextEditable";
	},

	//static cache variables shared among all instance of this class
	_local2NativeFormatNames: {},
	_native2LocalFormatNames: {},

	_getIframeDocTxt: function(html){
		var _cs = dojo.getComputedStyle(this.domNode);
		if(!this.height){
			html="<div>"+html+"</div>";
		}
		var font = [ _cs.fontWeight, _cs.fontSize, _cs.fontFamily ].join(" ");

		// line height is tricky - applying a units value will mess things up.
		// if we can't get a non-units value, bail out.
		var lineHeight = _cs.lineHeight;
		if(lineHeight.indexOf("px") >= 0){
			lineHeight = parseFloat(lineHeight)/parseFloat(_cs.fontSize);
			// console.debug(lineHeight);
		}else if(lineHeight.indexOf("em")>=0){
			lineHeight = parseFloat(lineHeight);
		}else{
			lineHeight = "1.0";
		}
		return [
			"<html><head><style>",
			"body,html {",
			"	background:transparent;",
			"	padding: 0;",
			"	margin: 0;",
			"}",
			// TODO: left positioning will cause contents to disappear out of view
			//       if it gets too wide for the visible area
			"body{",
			"	top:0px; left:0px; right:0px;",
				((this.height||dojo.isOpera) ? "" : "position: fixed;"),
			"	font:", font, ";",
			// FIXME: IE 6 won't understand min-height?
			"	min-height:", this.minHeight, ";",
			"	line-height:", lineHeight,
			"}",
			"p{ margin: 1em 0 !important; }",
			(this.height ?
				"" : "body,html{overflow-y:hidden;/*for IE*/} body > div {overflow-x:auto;/*for FF to show vertical scrollbar*/}"+
				"body > div > *:first-child{ padding-top:0 !important;margin-top: 0px !important;}" + // FIXME: test firstChild nodeType
				"body > div > *:last-child {"+
				"	padding-bottom:0 !important;"+
				"	margin-bottom: 0px !important;"+
				"}"
			),
			"li > ul:-moz-first-node, li > ol:-moz-first-node{ padding-top: 1.2em; } ",
			"li{ min-height:1.2em; }",
			"</style>",
			this._applyEditingAreaStyleSheets(),
			"</head><body>"+html+"</body></html>"
		].join("");
	},

	_drawIframe: function(/*String*/html){
		// summary:
		//		Draws an iFrame using the existing one if one exists.
		//		Used by Mozilla, Safari, and Opera

		if(!this.iframe){
			var ifr = this.iframe = dojo.doc.createElement("iframe");
			// this.iframe.src = "about:blank";
			// document.body.appendChild(this.iframe);
			// console.debug(this.iframe.contentDocument.open());
			// dojo.body().appendChild(this.iframe);
			var ifrs = ifr.style;
			// ifrs.border = "1px solid black";
			ifrs.border = "none";
			ifrs.lineHeight = "0"; // squash line height
			ifrs.verticalAlign = "bottom";
//			ifrs.scrolling = this.height ? "auto" : "vertical";
			this.editorObject = this.iframe;
		}
		// opera likes this to be outside the with block
		//	this.iframe.src = "javascript:void(0)";//dojo.uri.dojoUri("src/widget/templates/richtextframe.html") + ((dojo.doc.domain != currentDomain) ? ("#"+dojo.doc.domain) : "");
		this.iframe.style.width = this.inheritWidth ? this._oldWidth : "100%";

		if(this.height){
			this.iframe.style.height = this.height;
		}else{
			this.iframe.height = this._oldHeight;
		}

		if(this.textarea){
			var tmpContent = this.srcNodeRef;
		}else{
			var tmpContent = dojo.doc.createElement('div');
			tmpContent.style.display="none";
			tmpContent.innerHTML = html;
			//append tmpContent to under the current domNode so that the margin
			//calculation below is correct
			this.editingArea.appendChild(tmpContent);
		}


		this.editingArea.appendChild(this.iframe);

		if(!this.height){
			// fix margins on tmpContent
			var c = dojo.query(">", tmpContent);
			var firstChild = c[0];
			var lastChild = c.pop();
			if(firstChild){
				firstChild.style.marginTop = "0px";
			}
			if(lastChild){
				lastChild.style.marginBottom = "0px";
			}
		}
		//do we want to show the content before the editing area finish loading here?
		//if external style sheets are used for the editing area, the appearance now
		//and after loading of the editing area won't be the same (and padding/margin
		//calculation above may not be accurate)
		//	tmpContent.style.display = "none";
		//	this.editingArea.appendChild(this.iframe);

		var _iframeInitialized = false;
		// console.debug(this.iframe);
		// var contentDoc = this.iframe.contentWindow.document;


		// note that on Safari lower than 420+, we have to get the iframe
		// by ID in order to get something w/ a contentDocument property

		var contentDoc = this.iframe.contentDocument;
		contentDoc.open();
		contentDoc.write(this._getIframeDocTxt(html));
		contentDoc.close();

		// now we wait for onload. Janky hack!
		var ifrFunc = dojo.hitch(this, function(){
			if(!_iframeInitialized){
				_iframeInitialized = true;
			}else{ return; }
			if(!this.editNode){
				try{
					if(this.iframe.contentWindow){
						this.window = this.iframe.contentWindow;
						this.document = this.iframe.contentWindow.document
					}else if(this.iframe.contentDocument){
						// for opera
						this.window = this.iframe.contentDocument.window;
						this.document = this.iframe.contentDocument;
					}
					if(!this.document.body){
						throw 'Error'; 
					}
				}catch(e){
					setTimeout(ifrFunc,500);
					_iframeInitialized = false;
					return;
				}

				dojo._destroyElement(tmpContent);
				this.document.designMode = "on";
				//	try{
				//	this.document.designMode = "on";
				// }catch(e){
				//	this._tryDesignModeOnClick=true;
				// }
				try{
					var currentDomain = (new dojo._Url(dojo.doc.location)).host;
					if(dojo.doc.domain!=currentDomain){
						this.document.domain = dojo.doc.domain;
					}
				}catch(e){}

				this.onLoad();
			}else{
				dojo._destroyElement(tmpContent);
				this.editNode.innerHTML = html;
				this.onDisplayChanged();
			}
			this._preDomFilterContent(this.editNode);
		});

		ifrFunc();
	},

	_applyEditingAreaStyleSheets: function(){
		// summary:
		//		apply the specified css files in styleSheets
		var files = [];
		if(this.styleSheets){
			files = this.styleSheets.split(';');
			this.styleSheets = '';
		}

		//empty this.editingAreaStyleSheets here, as it will be filled in addStyleSheet
		files = files.concat(this.editingAreaStyleSheets);
		this.editingAreaStyleSheets = [];

		var text='', i=0, url;
		while(url=files[i++]){
			var abstring = (new dojo._Url(dojo.global.location, url)).toString();
			this.editingAreaStyleSheets.push(abstring);
			text += '<link rel="stylesheet" type="text/css" href="'+abstring+'"/>'
		}
		return text;
	},

	addStyleSheet: function(/*dojo._Url*/uri){
		// summary:
		//		add an external stylesheet for the editing area
		// uri:	a dojo.uri.Uri pointing to the url of the external css file
		var url=uri.toString();

		//if uri is relative, then convert it to absolute so that it can be resolved correctly in iframe
		if(url.charAt(0) == '.' || (url.charAt(0) != '/' && !uri.host)){
			url = (new dojo._Url(dojo.global.location, url)).toString();
		}

		if(dojo.indexOf(this.editingAreaStyleSheets, url) > -1){
			console.debug("dijit._editor.RichText.addStyleSheet: Style sheet "+url+" is already applied to the editing area!");
			return;
		}

		this.editingAreaStyleSheets.push(url);
		if(this.document.createStyleSheet){ //IE
			this.document.createStyleSheet(url);
		}else{ //other browser
			var head = this.document.getElementsByTagName("head")[0];
			var stylesheet = this.document.createElement("link");
			with(stylesheet){
				rel="stylesheet";
				type="text/css";
				href=url;
			}
			head.appendChild(stylesheet);
		}
	},

	removeStyleSheet: function(/*dojo._Url*/uri){
		// summary:
		//		remove an external stylesheet for the editing area
		var url=uri.toString();
		//if uri is relative, then convert it to absolute so that it can be resolved correctly in iframe
		if(url.charAt(0) == '.' || (url.charAt(0) != '/' && !uri.host)){
			url = (new dojo._Url(dojo.global.location, url)).toString();
		}
		var index = dojo.indexOf(this.editingAreaStyleSheets, url);
		if(index == -1){
			console.debug("dijit._editor.RichText.removeStyleSheet: Style sheet "+url+" is not applied to the editing area so it can not be removed!");
			return;
		}
		delete this.editingAreaStyleSheets[index];
		dojo.withGlobal(this.window,'query', dojo, ['link:[href="'+url+'"]']).orphan()
	},

	disabled: false,
	_mozSettingProps: ['styleWithCSS','insertBrOnReturn'],
	setDisabled: function(/*Boolean*/ disabled){
		if(dojo.isIE || dojo.isSafari || dojo.isOpera){
			this.editNode.contentEditable=!disabled;
		}else{ //moz
			if(disabled){
				this._mozSettings=[false,this.blockNodeForEnter==='BR'];
			}
			this.document.designMode=(disabled?'off':'on');
			if(!disabled){
				dojo.forEach(this._mozSettingProps, function(s,i){
					this.document.execCommand(s,false,this._mozSettings[i]);
				},this);
			}
//			this.document.execCommand('contentReadOnly', false, disabled);
//				if(disabled){
//					this.blur(); //to remove the blinking caret
//				}
//				
		}
		this.disabled=disabled;
	},

/* Event handlers
 *****************/

	_isResized: function(){ return false; },

	onLoad: function(e){
		// summary: handler after the content of the document finishes loading
		this.isLoaded = true;
		if(this.height){
			this.editNode=this.document.body;
		}else{
			this.editNode=this.document.body.firstChild;
		}
		this.editNode.contentEditable = true; //should do no harm in FF
		this._preDomFilterContent(this.editNode);
		
		var events=this.events.concat(this.captureEvents),i=0,et;
		while(et=events[i++]){
			this.connect(this.document, et.toLowerCase(), et);
		}
		if(!this.height){
			this.connect(this, "onNormalizedDisplayChanged", "_updateHeight");
		}
		if(!dojo.isIE){
			try{ // sanity check for Mozilla
//					this.document.execCommand("useCSS", false, true); // old moz call
				this.document.execCommand("styleWithCSS", false, false); // new moz call
				//this.document.execCommand("insertBrOnReturn", false, false); // new moz call
			}catch(e2){ }
			// FIXME: when scrollbars appear/disappear this needs to be fired
		}else{ // IE contentEditable
			// give the node Layout on IE
			this.editNode.style.zoom = 1.0;
		}

		if(this.focusOnLoad){
			this.focus();
		}
		
		this.onDisplayChanged(e);
		if(this.onLoadDeferred){
			this.onLoadDeferred.callback(true);
		}
	},

	onKeyDown: function(e){
		// summary: Fired on keydown

//		 console.info("onkeydown:", e.keyCode);

		// we need this event at the moment to get the events from control keys
		// such as the backspace. It might be possible to add this to Dojo, so that
		// keyPress events can be emulated by the keyDown and keyUp detection.
		if(dojo.isIE){
			if(e.keyCode === dojo.keys.TAB){
				dojo.stopEvent(e);
				// FIXME: this is a poor-man's indent/outdent. It would be
				// better if it added 4 "&nbsp;" chars in an undoable way.
				// Unfortuantly pasteHTML does not prove to be undoable
				this.execCommand((e.shiftKey ? "outdent" : "indent"));
			}else if(e.keyCode === dojo.keys.BACKSPACE && this.document.selection.type === "Control"){
				// IE has a bug where if a non-text object is selected in the editor,
	      // hitting backspace would act as if the browser's back button was
	      // clicked instead of deleting the object. see #1069
				dojo.stopEvent(e);
				this.execCommand("delete");
			}else if(	(65 <= e.keyCode&&e.keyCode <= 90) ||
				(e.keyCode>=37&&e.keyCode<=40) // FIXME: get this from connect() instead!
			){ //arrow keys
				e.charCode = e.keyCode;
				this.onKeyPress(e);
			}
		}
	},

	onKeyUp: function(e){
		// summary: Fired on keyup
		return;
	},

	KEY_CTRL: 1,
	KEY_SHIFT: 2,

	onKeyPress: function(e){
		// summary: Fired on keypress

//		 console.info("onkeypress:", e.keyCode);

		// handle the various key events
		var modifiers = e.ctrlKey ? this.KEY_CTRL : 0 | e.shiftKey?this.KEY_SHIFT : 0;

		var key = e.keyChar||e.keyCode;
		if(this._keyHandlers[key]){
			// console.debug("char:", e.key);
			var handlers = this._keyHandlers[key], i = 0, h;
			while(h = handlers[i++]){
				if(modifiers == h.modifiers){
					if(!h.handler.apply(this,arguments)){
						e.preventDefault();
					}
					break;
				}
			}
		}

		// function call after the character has been inserted
		setTimeout(dojo.hitch(this, function(){
			this.onKeyPressed(e);
		}), 1);
	},

	addKeyHandler: function(/*String*/key, /*Int*/modifiers, /*Function*/handler){
		// summary: add a handler for a keyboard shortcut
		if(!dojo.isArray(this._keyHandlers[key])){ this._keyHandlers[key] = []; }
		this._keyHandlers[key].push({
			modifiers: modifiers || 0,
			handler: handler
		});
	},

	onKeyPressed: function(e){
		this.onDisplayChanged(/*e*/); // can't pass in e
	},

	onClick: function(e){
//			console.debug('onClick',this._tryDesignModeOnClick);
//			if(this._tryDesignModeOnClick){
//				try{
//					this.document.designMode='on';
//					this._tryDesignModeOnClick=false;
//				}catch(e){}
//			}
		this.onDisplayChanged(e); },
	_onBlur: function(e){
		var _c=this.getValue(true);
		if(_c!=this.savedContent){
			this.onChange(_c);
			this.savedContent=_c;
		}
//			console.info('_onBlur') 
	},
	_initialFocus: true,
	_onFocus: function(e){
//			console.info('_onFocus')
		// summary: Fired on focus
		if( (dojo.isMoz)&&(this._initialFocus) ){
			this._initialFocus = false;
			if(this.editNode.innerHTML.replace(/^\s+|\s+$/g, "") == "&nbsp;"){
				this.placeCursorAtStart();
//					this.execCommand("selectall");
//					this.window.getSelection().collapseToStart();
			}
		}
	},

	blur: function(){
		// summary: remove focus from this instance
		if(this.iframe){
			this.window.blur();
		}else if(this.editNode){
			this.editNode.blur();
		}
	},

	focus: function(){
		// summary: move focus to this instance
		if(this.iframe && !dojo.isIE){
			dijit.focus(this.iframe);
		}else if(this.editNode && this.editNode.focus){
			// editNode may be hidden in display:none div, lets just punt in this case
			dijit.focus(this.editNode);
		}else{
			console.debug("Have no idea how to focus into the editor!");
		}
	},

//		_lastUpdate: 0,
	updateInterval: 200,
	_updateTimer: null,
	onDisplayChanged: function(e){
		// summary:
		//		this event will be fired everytime the display context
		//		changes and the result needs to be reflected in the UI.
		// description:
		//		if you don't want to have update too often, 
		//		onNormalizedDisplayChanged should be used instead

//			var _t=new Date();
		if(!this._updateTimer){
//				this._lastUpdate=_t;
			if(this._updateTimer){
				clearTimeout(this._updateTimer);
			}
			this._updateTimer=setTimeout(dojo.hitch(this,this.onNormalizedDisplayChanged),this.updateInterval);
		}
	},
	onNormalizedDisplayChanged: function(){
		// summary:
		//		this event is fired every updateInterval ms or more
		// description:
		//		if something needs to happen immidiately after a 
		//		user change, please use onDisplayChanged instead
		this._updateTimer=null;
	},
	onChange: function(newContent){
		// summary: 
		//		this is fired if and only if the editor loses focus and 
		//		the content is changed

//			console.log('onChange',newContent);
	},
	_normalizeCommand: function(/*String*/cmd){
		// summary:
		//		Used as the advice function by dojo.connect to map our
		//		normalized set of commands to those supported by the target
		//		browser

		var command = cmd.toLowerCase();
		if(command == "formatblock"){
			if(dojo.isSafari){ command = "heading"; }
		}else if(command == "hilitecolor" && !dojo.isMoz){
			command = "backcolor";
		}

		return command;
	},

	queryCommandAvailable: function(/*String*/command){
		// summary:
		//		Tests whether a command is supported by the host. Clients SHOULD check
		//		whether a command is supported before attempting to use it, behaviour
		//		for unsupported commands is undefined.
		// command: The command to test for
		var ie = 1;
		var mozilla = 1 << 1;
		var safari = 1 << 2;
		var opera = 1 << 3;
		var safari420 = 1 << 4;

		var gt420 = dojo.isSafari;

		function isSupportedBy(browsers){
			return {
				ie: Boolean(browsers & ie),
				mozilla: Boolean(browsers & mozilla),
				safari: Boolean(browsers & safari),
				safari420: Boolean(browsers & safari420),
				opera: Boolean(browsers & opera)
			}
		}

		var supportedBy = null;

		switch(command.toLowerCase()){
			case "bold": case "italic": case "underline":
			case "subscript": case "superscript":
			case "fontname": case "fontsize":
			case "forecolor": case "hilitecolor":
			case "justifycenter": case "justifyfull": case "justifyleft":
			case "justifyright": case "delete": case "selectall":
				supportedBy = isSupportedBy(mozilla | ie | safari | opera);
				break;

			case "createlink": case "unlink": case "removeformat":
			case "inserthorizontalrule": case "insertimage":
			case "insertorderedlist": case "insertunorderedlist":
			case "indent": case "outdent": case "formatblock":
			case "inserthtml": case "undo": case "redo": case "strikethrough":
				supportedBy = isSupportedBy(mozilla | ie | opera | safari420);
				break;

			case "blockdirltr": case "blockdirrtl":
			case "dirltr": case "dirrtl":
			case "inlinedirltr": case "inlinedirrtl":
				supportedBy = isSupportedBy(ie);
				break;
			case "cut": case "copy": case "paste":
				supportedBy = isSupportedBy( ie | mozilla | safari420);
				break;

			case "inserttable":
				supportedBy = isSupportedBy(mozilla | ie);
				break;

			case "insertcell": case "insertcol": case "insertrow":
			case "deletecells": case "deletecols": case "deleterows":
			case "mergecells": case "splitcell":
				supportedBy = isSupportedBy(ie | mozilla);
				break;

			default: return false;
		}

		return (dojo.isIE && supportedBy.ie) ||
			(dojo.isMoz && supportedBy.mozilla) ||
			(dojo.isSafari && supportedBy.safari) ||
			(gt420 && supportedBy.safari420) ||
			(dojo.isOpera && supportedBy.opera);  // Boolean return true if the command is supported, false otherwise
	},

	execCommand: function(/*String*/command, argument){
		// summary: Executes a command in the Rich Text area
		// command: The command to execute
		// argument: An optional argument to the command
		var returnValue;

		//focus() is required for IE to work
		//In addition, focus() makes sure after the execution of
		//the command, the editor receives the focus as expected
		this.focus();

		command = this._normalizeCommand(command);
		if(argument != undefined){
			if(command == "heading"){
				throw new Error("unimplemented");
			}else if((command == "formatblock") && dojo.isIE){
				argument = '<'+argument+'>';
			}
		}
		if(command == "inserthtml"){
			//TODO: we shall probably call _preDomFilterContent here as well
			argument=this._preFilterContent(argument);
			if(dojo.isIE){
				var insertRange = this.document.selection.createRange();
				insertRange.pasteHTML(argument);
				insertRange.select();
				//insertRange.collapse(true);
				returnValue=true;
			}else if(dojo.isMoz && !argument.length){
				//mozilla can not inserthtml an empty html to delete current selection
				//so we delete the selection instead in this case
				dojo.withGlobal(this.window,'remove',dijit._editor.selection); // FIXME
				returnValue=true;
			}else{
				returnValue=this.document.execCommand(command, false, argument);
			}
		}else if(
			(command == "unlink")&&
			(this.queryCommandEnabled("unlink"))&&
			(dojo.isMoz || dojo.isSafari)
		){
			// fix up unlink in Mozilla to unlink the link and not just the selection

			// grab selection
			// Mozilla gets upset if we just store the range so we have to
			// get the basic properties and recreate to save the selection
			var selection = this.window.getSelection();
			//	var selectionRange = selection.getRangeAt(0);
			//	var selectionStartContainer = selectionRange.startContainer;
			//	var selectionStartOffset = selectionRange.startOffset;
			//	var selectionEndContainer = selectionRange.endContainer;
			//	var selectionEndOffset = selectionRange.endOffset;

			// select our link and unlink
			var a = dojo.withGlobal(this.window, "getAncestorElement",dijit._editor.selection, ['a']);
			dojo.withGlobal(this.window, "selectElement", dijit._editor.selection, [a]);

			returnValue=this.document.execCommand("unlink", false, null);
		}else if((command == "hilitecolor")&&(dojo.isMoz)){
//				// mozilla doesn't support hilitecolor properly when useCSS is
//				// set to false (bugzilla #279330)

//				this.document.execCommand("useCSS", false, false);
			returnValue = this.document.execCommand(command, false, argument);
//				this.document.execCommand("useCSS", false, true);

		}else if((dojo.isIE)&&( (command == "backcolor")||(command == "forecolor") )){
			// Tested under IE 6 XP2, no problem here, comment out
			// IE weirdly collapses ranges when we exec these commands, so prevent it
//				var tr = this.document.selection.createRange();
			argument = arguments.length > 1 ? argument : null;
			returnValue = this.document.execCommand(command, false, argument);

			// timeout is workaround for weird IE behavior were the text
			// selection gets correctly re-created, but subsequent input
			// apparently isn't bound to it
//				setTimeout(function(){tr.select();}, 1);
		}else{
			argument = arguments.length > 1 ? argument : null;
//				if(dojo.isMoz){
//					this.document = this.iframe.contentWindow.document
//				}

			if(argument || command!="createlink"){
				returnValue = this.document.execCommand(command, false, argument);
			}
		}

		this.onDisplayChanged();
		return returnValue;
	},

	queryCommandEnabled: function(/*String*/command){
		// summary: check whether a command is enabled or not
		command = this._normalizeCommand(command);
		if(dojo.isMoz || dojo.isSafari){
			if(command == "unlink"){ // mozilla returns true always
				// console.debug(dojo.withGlobal(this.window, "hasAncestorElement",dijit._editor.selection, ['a']));
				return dojo.withGlobal(this.window, "hasAncestorElement",dijit._editor.selection, ['a']);
			}else if (command == "inserttable"){
				return true;
			}
		}
		//see #4109
		if(dojo.isSafari)
			if(command == "copy"){
				command="cut";
			}else if(command == "paste"){
				return true;
		}

		// return this.document.queryCommandEnabled(command);
		var elem = (dojo.isIE) ? this.document.selection.createRange() : this.document;
		return elem.queryCommandEnabled(command);
	},

	queryCommandState: function(command){
		// summary: check the state of a given command
		command = this._normalizeCommand(command);
		return this.document.queryCommandState(command);
	},

	queryCommandValue: function(command){
		// summary: check the value of a given command
		command = this._normalizeCommand(command);
		if(dojo.isIE && command == "formatblock"){
			return this._local2NativeFormatNames[this.document.queryCommandValue(command)];
		}
		return this.document.queryCommandValue(command);
	},

	// Misc.

	placeCursorAtStart: function(){
		// summary:
		//		place the cursor at the start of the editing area
		this.focus();

		//see comments in placeCursorAtEnd
		var isvalid=false;
		if(dojo.isMoz){
			var first=this.editNode.firstChild;
			while(first){
				if(first.nodeType == 3){
					if(first.nodeValue.replace(/^\s+|\s+$/g, "").length>0){
						isvalid=true;
						dojo.withGlobal(this.window, "selectElement", dijit._editor.selection, [first]);
						break;
					}
				}else if(first.nodeType == 1){
					isvalid=true;
					dojo.withGlobal(this.window, "selectElementChildren",dijit._editor.selection, [first]);
					break;
				}
				first = first.nextSibling;
			}
		}else{
			isvalid=true;
			dojo.withGlobal(this.window, "selectElementChildren",dijit._editor.selection, [this.editNode]);
		}
		if(isvalid){
			dojo.withGlobal(this.window, "collapse", dijit._editor.selection, [true]);
		}
	},

	placeCursorAtEnd: function(){
		// summary:
		//		place the cursor at the end of the editing area
		this.focus();

		//In mozilla, if last child is not a text node, we have to use selectElementChildren on this.editNode.lastChild
		//otherwise the cursor would be placed at the end of the closing tag of this.editNode.lastChild
		var isvalid=false;
		if(dojo.isMoz){
			var last=this.editNode.lastChild;
			while(last){
				if(last.nodeType == 3){
					if(last.nodeValue.replace(/^\s+|\s+$/g, "").length>0){
						isvalid=true;
						dojo.withGlobal(this.window, "selectElement",dijit._editor.selection, [last]);
						break;
					}
				}else if(last.nodeType == 1){
					isvalid=true;
					if(last.lastChild){
						dojo.withGlobal(this.window, "selectElement",dijit._editor.selection, [last.lastChild]);
					}else{
						dojo.withGlobal(this.window, "selectElement",dijit._editor.selection, [last]);
					}
					break;
				}
				last = last.previousSibling;
			}
		}else{
			isvalid=true;
			dojo.withGlobal(this.window, "selectElementChildren",dijit._editor.selection, [this.editNode]);
		}
		if(isvalid){
			dojo.withGlobal(this.window, "collapse", dijit._editor.selection, [false]);
		}
	},

	getValue: function(/*Boolean?*/nonDestructive){
		// summary:
		//		return the current content of the editing area (post filters are applied)
		if(this.textarea){
			if(this.isClosed || !this.isLoaded){
				return this.textarea.value;
			}
		}

		return this._postFilterContent(null, nonDestructive);
	},

	setValue: function(/*String*/html){
		// summary:
		//		this function set the content. No undo history is preserved
		if(this.textarea && (this.isClosed || !this.isLoaded)){
			this.textarea.value=html;
		}else{
			html = this._preFilterContent(html);
			if(this.isClosed){
				this.domNode.innerHTML = html;
				this._preDomFilterContent(this.domNode);
			}else{
				this.editNode.innerHTML = html;
				this._preDomFilterContent(this.editNode);
			}
		}
	},

	replaceValue: function(/*String*/html){
		// summary:
		//		this function set the content while trying to maintain the undo stack
		//		(now only works fine with Moz, this is identical to setValue in all
		//		other browsers)
		if(this.isClosed){
			this.setValue(html);
		}else if(this.window && this.window.getSelection && !dojo.isMoz){ // Safari
			// look ma! it's a totally f'd browser!
			this.setValue(html);
		}else if(this.window && this.window.getSelection){ // Moz
			html = this._preFilterContent(html);
			this.execCommand("selectall");
			if(dojo.isMoz && !html){ html = "&nbsp;" }
			this.execCommand("inserthtml", html);
			this._preDomFilterContent(this.editNode);
		}else if(this.document && this.document.selection){//IE
			//In IE, when the first element is not a text node, say
			//an <a> tag, when replacing the content of the editing
			//area, the <a> tag will be around all the content
			//so for now, use setValue for IE too
			this.setValue(html);
		}
	},

	_preFilterContent: function(/*String*/html){
		// summary:
		//		filter the input before setting the content of the editing area
		var ec = html;
		dojo.forEach(this.contentPreFilters, function(ef){ if(ef){ ec = ef(ec); } });
		return ec;
	},
	_preDomFilterContent: function(/*DomNode*/dom){
		// summary:
		//		filter the input
		dom = dom || this.editNode;
		dojo.forEach(this.contentDomPreFilters, function(ef){
			if(ef && dojo.isFunction(ef)){
				ef(dom);
			}
		}, this);
	},

	_postFilterContent: function(/*DomNode|DomNode[]?*/dom,/*Boolean?*/nonDestructive){
		// summary:
		//		filter the output after getting the content of the editing area
		dom = dom || this.editNode;
		if(this.contentDomPostFilters.length){
			if(nonDestructive && dom['cloneNode']){
				dom = dom.cloneNode(true);
			}
			dojo.forEach(this.contentDomPostFilters, function(ef){
				dom = ef(dom);
			});
		}
		var ec = this.getNodeChildrenHtml(dom);
		if(!ec.replace(/^(?:\s|\xA0)+/g, "").replace(/(?:\s|\xA0)+$/g,"").length){ ec = ""; }

		//	if(dojo.isIE){
		//		//removing appended <P>&nbsp;</P> for IE
		//		ec = ec.replace(/(?:<p>&nbsp;</p>[\n\r]*)+$/i,"");
		//	}
		dojo.forEach(this.contentPostFilters, function(ef){
			ec = ef(ec);
		});

		return ec;
	},

	//Int: stored last time height
	_lastHeight: 0,

	_updateHeight: function(){
		// summary:
		//		Updates the height of the editor area to fit the contents.
		if(!this.isLoaded){ return; }
		if(this.height){ return; }

		// var height = dojo.marginBox(this.editNode).h;
		var height = dojo.marginBox(this.editNode).h;
		if(dojo.isOpera){
			height = this.editNode.scrollHeight;
		}

		// console.debug(this.editNode);
		// alert(this.editNode);

		//height maybe zero in some cases even though the content is not empty,
		//we try the height of body instead
		if(!height){
			height = dojo.marginBox(this.document.body).h;
		}

		if(height == 0){
			console.debug("Can not figure out the height of the editing area!");
			return; //prevent setting height to 0
		}
		if(height != this._lastHeight){
			this._lastHeight = height;
			// this.editorObject.style.height = this._lastHeight + "px";
			dojo.marginBox(this.editorObject, { h: this._lastHeight });
		}
	},

	_saveContent: function(e){
		// summary:
		//		Saves the content in an onunload event if the editor has not been closed
		var saveTextarea = dojo.byId("dijit._editor.RichText.savedContent");
		saveTextarea.value += this._SEPARATOR + this.name + ":" + this.getValue();
	},


	escapeXml: function(/*string*/str, /*boolean*/noSingleQuotes){
		//summary:
		//		Adds escape sequences for special characters in XML: &<>"'
		//		Optionally skips escapes for single quotes
		str = str.replace(/&/gm, "&amp;").replace(/</gm, "&lt;").replace(/>/gm, "&gt;").replace(/"/gm, "&quot;");
		if(!noSingleQuotes){
			str = str.replace(/'/gm, "&#39;");
		}
		return str; // string
	},

	getNodeHtml: function(node){
		switch(node.nodeType){
			case 1: //element node
				var output = '<'+node.tagName.toLowerCase();
				if(dojo.isMoz){
					if(node.getAttribute('type')=='_moz'){
						node.removeAttribute('type');
					}
					if(node.getAttribute('_moz_dirty') != undefined){
						node.removeAttribute('_moz_dirty');
					}
				}
				//store the list of attributes and sort it to have the
				//attributes appear in the dictionary order
				var attrarray = [];
				if(dojo.isIE){
					var s = node.outerHTML;
					s = s.substr(0,s.indexOf('>'));
					s = s.replace(/(?:['"])[^"']*\1/g, '');//to make the following regexp safe
					var reg = /([^\s=]+)=/g;
					var m, key;
					while((m = reg.exec(s)) != undefined){
						key=m[1];
						if(key.substr(0,3) != '_dj'){
							if(key == 'src' || key == 'href'){
								if(node.getAttribute('_djrealurl')){
									attrarray.push([key,node.getAttribute('_djrealurl')]);
									continue;
								}
							}
							if(key == 'class'){
								attrarray.push([key,node.className]);
							}else{
								attrarray.push([key,node.getAttribute(key)]);
							}
						}
					}
				}else{
					var attr, i=0, attrs = node.attributes;
					while(attr=attrs[i++]){
						//ignore all attributes starting with _dj which are
						//internal temporary attributes used by the editor
						if(attr.name.substr(0,3) != '_dj' /*&&
							(attr.specified == undefined || attr.specified)*/){
							var v = attr.value;
							if(attr.name == 'src' || attr.name == 'href'){
								if(node.getAttribute('_djrealurl')){
									v = node.getAttribute('_djrealurl');
								}
							}
							attrarray.push([attr.name,v]);
						}
					}
				}
				attrarray.sort(function(a,b){
					return a[0]<b[0]?-1:(a[0]==b[0]?0:1);
				});
				i=0;
				while(attr=attrarray[i++]){
					output += ' '+attr[0]+'="'+attr[1]+'"';
				}
				if(node.childNodes.length){
					output += '>' + this.getNodeChildrenHtml(node)+'</'+node.tagName.toLowerCase()+'>';
				}else{
					output += ' />';
				}
				break;
			case 3: //text
				// FIXME:
				var output = this.escapeXml(node.nodeValue,true);
				break;
			case 8: //comment
				// FIXME:
				var output = '<!--'+this.escapeXml(node.nodeValue,true)+'-->';
				break;
			default:
				var output = "Element not recognized - Type: " + node.nodeType + " Name: " + node.nodeName;
		}
		return output;
	},

	getNodeChildrenHtml: function(dom){
		var out = "";
		if(!dom){ return out; }
		var nodes = dom["childNodes"]||dom;
		var i=0;
		var node;
		while(node=nodes[i++]){
			out += this.getNodeHtml(node);
		}
		return out;
	},

	close: function(/*Boolean*/save, /*Boolean*/force){
		// summary:
		//		Kills the editor and optionally writes back the modified contents to the
		//		element from which it originated.
		// save:
		//		Whether or not to save the changes. If false, the changes are discarded.
		// force:
		if(this.isClosed){return false; }

		if(!arguments.length){ save = true; }
		this._content = this.getValue();
		var changed = (this.savedContent != this._content);

		// line height is squashed for iframes
		// FIXME: why was this here? if (this.iframe){ this.domNode.style.lineHeight = null; }

		if(this.interval){ clearInterval(this.interval); }

		if(this.textarea){
			with(this.textarea.style){
				position = "";
				left = top = "";
				if(dojo.isIE){
					overflow = this.__overflow;
					this.__overflow = null;
				}
			}
			if(save){
				this.textarea.value = this._content;
			}else{
				this.textarea.value = this.savedContent;
			}
			dojo._destroyElement(this.domNode);
			this.domNode = this.textarea;
		}else{
			if(save){
				//why we treat moz differently? comment out to fix #1061
//					if(dojo.isMoz){
//						var nc = dojo.doc.createElement("span");
//						this.domNode.appendChild(nc);
//						nc.innerHTML = this.editNode.innerHTML;
//					}else{
//						this.domNode.innerHTML = this._content;
//					}
				this.domNode.innerHTML = this._content;
			}else{
				this.domNode.innerHTML = this.savedContent;
			}
		}

		dojo.removeClass(this.domNode, "RichTextEditable");
		this.isClosed = true;
		this.isLoaded = false;
		// FIXME: is this always the right thing to do?
		delete this.editNode;

		if(this.window && this.window._frameElement){
			this.window._frameElement = null;
		}

		this.window = null;
		this.document = null;
		this.editingArea = null;
		this.editorObject = null;

		return changed; // Boolean: whether the content has been modified
	},

	destroyRendering: function(){}, // stub!

	destroy: function(){
		this.destroyRendering();
		if(!this.isClosed){ this.close(false); }

		dijit._editor.RichText.superclass.destroy.call(this);
	},

	_fixContentForMoz: function(html){
		// summary:
		//		Moz can not handle strong/em tags correctly, convert them to b/i
		html = html.replace(/<(\/)?strong([ \>])/gi, '<$1b$2' );
		html = html.replace(/<(\/)?em([ \>])/gi, '<$1i$2' );
		return html;
	},
	_srcInImgRegex	: /(?:(<img(?=\s).*?\ssrc=)("|')(.*?)\2)|(?:(<img\s.*?src=)([^"'][^ >]+))/gi ,
	_hrefInARegex	: /(?:(<a(?=\s).*?\shref=)("|')(.*?)\2)|(?:(<a\s.*?href=)([^"'][^ >]+))/gi ,
	_preFixUrlAttributes: function(html){
		html = html.replace(this._hrefInARegex, '$1$4$2$3$5$2 _djrealurl=$2$3$5$2') ;
		html = html.replace(this._srcInImgRegex, '$1$4$2$3$5$2 _djrealurl=$2$3$5$2') ;
		return html;
	}
});

}

if(!dojo._hasResource["dijit._Container"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._Container"] = true;
dojo.provide("dijit._Container");

dojo.declare("dijit._Contained",
	null,
	{
		// summary
		//		Mixin for widgets that are children of a container widget

		getParent: function(){
			// summary:
			//		returns the parent widget of this widget, assuming the parent
			//		implements dijit._Container
			for(var p=this.domNode.parentNode; p; p=p.parentNode){
				var id = p.getAttribute && p.getAttribute("widgetId");
				if(id){
					var parent = dijit.byId(id);
					return parent.isContainer ? parent : null;
				}
			}
			return null;
		},

		_getSibling: function(which){
			var node = this.domNode;
			do{
				node = node[which+"Sibling"];
			}while(node && node.nodeType != 1);
			if(!node){ return null; } // null
			var id = node.getAttribute("widgetId");
			return dijit.byId(id);
		},

		getPreviousSibling: function(){
			// summary:
			//		returns null if this is the first child of the parent,
			//		otherwise returns the next element sibling to the "left".

			return this._getSibling("previous");
		},

		getNextSibling: function(){
			// summary:
			//		returns null if this is the last child of the parent,
			//		otherwise returns the next element sibling to the "right".

			return this._getSibling("next");
		}
	}
);

dojo.declare("dijit._Container",
	null,
	{
		// summary
		//		Mixin for widgets that contain a list of children like SplitContainer

		isContainer: true,

		addChild: function(/*Widget*/ widget, /*int?*/ insertIndex){
			// summary:
			//		Process the given child widget, inserting it's dom node as
			//		a child of our dom node

			var containerNode = this.containerNode || this.domNode;
			if(typeof insertIndex == "undefined"){
				insertIndex = "last";
			}
			dojo.place(widget.domNode, containerNode, insertIndex);
		},

		removeChild: function(/*Widget*/ widget){
			// summary:
			//		removes the passed widget instance from this widget but does
			//		not destroy it
			var node = widget.domNode;
			node.parentNode.removeChild(node);	// detach but don't destroy
		},

		_nextElement: function(node){
			do{
				node = node.nextSibling;
			}while(node && node.nodeType != 1);
			return node;
		},

		_firstElement: function(node){
			node = node.firstChild;
			if(node && node.nodeType != 1){
				node = this._nextElement(node);
			}
			return node;
		},

		getChildren: function(){
			// summary:
			//		returns array of children widgets
			return dojo.query("> [widgetId]", this.containerNode || this.domNode).map(dijit.byNode); // Array
		},

		hasChildren: function(){
			// summary:
			//		returns true if widget has children
			var cn = this.containerNode || this.domNode;
			return !!this._firstElement(cn); // Boolean
		}
	}
);

}

if(!dojo._hasResource["dijit.Toolbar"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.Toolbar"] = true;
dojo.provide("dijit.Toolbar");





dojo.declare(
	"dijit.Toolbar",
	[dijit._Widget, dijit._Templated, dijit._Container],
{
	templateString:
		'<div class="dijit dijitToolbar" waiRole="toolbar" tabIndex="-1" dojoAttachPoint="containerNode">' +
//			'<table style="table-layout: fixed" class="dijitReset dijitToolbarTable">' + // factor out style
//				'<tr class="dijitReset" dojoAttachPoint="containerNode"></tr>'+
//			'</table>' +
		'</div>'
}
);

// Combine with dijit.MenuSeparator??
dojo.declare(
	"dijit.ToolbarSeparator",
	[ dijit._Widget, dijit._Templated ],
{
	// summary
	//	A line between two menu items
	templateString: '<div class="dijitToolbarSeparator dijitInline"></div>',
	postCreate: function(){ dojo.setSelectable(this.domNode, false); }
});

}

if(!dojo._hasResource["dijit.form._FormWidget"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.form._FormWidget"] = true;
dojo.provide("dijit.form._FormWidget");




dojo.declare("dijit.form._FormWidget", [dijit._Widget, dijit._Templated],
{
	/*
	Summary:
		FormElement widgets correspond to native HTML elements such as <input> or <button> or <select>.
		Each FormElement represents a single input value, and has a (possibly hidden) <input> element,
		to which it serializes its input value, so that form submission (either normal submission or via FormBind?)
		works as expected.

		All these widgets should have these attributes just like native HTML input elements.
		You can set them during widget construction, but after that they are read only.

		They also share some common methods.
	*/

	// baseClass: String
	//		Used to add CSS classes like FormElementDisabled
	// TODO: remove this in favor of this.domNode.baseClass?
	baseClass: "",

	// value: String
	//		Corresponds to the native HTML <input> element's attribute.
	value: "",

	// name: String
	//		Name used when submitting form; same as "name" attribute or plain HTML elements
	name: "",

	// id: String
	//		Corresponds to the native HTML <input> element's attribute.
	//		Also becomes the id for the widget.
	id: "",

	// alt: String
	//		Corresponds to the native HTML <input> element's attribute.
	alt: "",

	// type: String
	//		Corresponds to the native HTML <input> element's attribute.
	type: "text",

	// tabIndex: Integer
	//		Order fields are traversed when user hits the tab key
	tabIndex: "0",

	// disabled: Boolean
	//		Should this widget respond to user input?
	//		In markup, this is specified as "disabled='disabled'", or just "disabled".
	disabled: false,

	// intermediateChanges: Boolean
	//              Fires onChange for each value change or only on demand
	intermediateChanges: false,

	setDisabled: function(/*Boolean*/ disabled){
		// summary:
		//		Set disabled state of widget.

		this.domNode.disabled = this.disabled = disabled;
		if(this.focusNode){
			this.focusNode.disabled = disabled;
		}
		dijit.wai.setAttr(this.focusNode || this.domNode, "waiState", "disabled", disabled);
		this._setStateClass();
	},


	_onMouse : function(/*Event*/ event){
		// summary:
		//	Sets _hovering, _active, and baseClass attributes depending on mouse state,
		//	then calls setStateClass() to set appropriate CSS class for this.domNode.
		//
		//	To get a different CSS class for hover, send onmouseover and onmouseout events to this method.
		//	To get a different CSS class while mouse button is depressed, send onmousedown to this method.

		var mouseNode = event.target;

		if(!this.disabled){
			switch(event.type){
				case "mouseover" :
					this._hovering = true;
					var baseClass, node=mouseNode;
					while( node.nodeType===1 && !(baseClass=node.getAttribute("baseClass")) && node != this.domNode ){
						node=node.parentNode;
					}
					this.baseClass= baseClass || "dijit"+this.declaredClass.replace(/.*\./g,"");
					break;

				case "mouseout" :	
					this._hovering = false;	
					this.baseClass=null;
					break;

				case "mousedown" :
					this._active = true;
					// set a global event to handle mouseup, so it fires properly
					//	even if the cursor leaves the button
					var self = this;
					// #2685: use this.connect and disconnect so destroy works properly
					var mouseUpConnector = this.connect(dojo.body(), "onmouseup", function(){
						self._active = false;
						self._setStateClass();
						self.disconnect(mouseUpConnector);
					});
					break;
			}
			this._setStateClass();
		}
	},

	focus: function(){
		dijit.focus(this.focusNode);
	},

	_setStateClass: function(/*String*/ base){
		// summary:
		//	Update the visual state of the widget by changing the css class on the domnode
		//	according to widget state.
		//
		//	State will be one of:
		//		<baseClass>
		//		<baseClass> + "Disabled"	- if the widget is disabled
		//		<baseClass> + "Active"		- if the mouse (or space/enter key?) is being pressed down
		//		<baseClass> + "Hover"		- if the mouse is over the widget
		//		<baseClass> + "Focused"		- if the widget has focus
		//
		//	Note: if you don't want to change the way the widget looks on hover, then don't call
		//	this routine on hover.  Similarly for mousedown --> active
		//
		//	For widgets which can be in a checked state (like checkbox or radio),
		//	in addition to the above classes...
		//		<baseClass> + "Checked"
		//		<baseClass> + "CheckedDisabled"	- if the widget is disabled
		//		<baseClass> + "CheckedActive"		- if the mouse is being pressed down
		//		<baseClass> + "CheckedHover"		- if the mouse is over the widget
		//		<baseClass> + "CheckedFocused"		- if the widget has focus

		// get original class (non state related) specified in template
		var origClass = (this.styleNode||this.domNode).className;

		// compute list of classname representing the states of the widget
		var base = this.baseClass || this.domNode.getAttribute("baseClass") || "dijitFormWidget";
		origClass = origClass.replace(new RegExp("\\b"+base+"(Checked)?(Selected)?(Disabled|Active|Focused|Hover)?\\b\\s*", "g"), "");
		var classes = [ base ];
		
		function multiply(modifier){
			classes=classes.concat(dojo.map(classes, function(c){ return c+modifier; }));
		}

		if(this.checked){
			multiply("Checked");
		}
		if(this.selected){
			multiply("Selected");
		}
		
		// Only one of these three can be applied.
		// Active trumps Focused, Focused trumps Hover, and Disabled trumps all.
		if(this.disabled){
			multiply("Disabled");
		}else if(this._active){
			multiply("Active");
		}else if(this._focused){
			multiply("Focused");
		}else if(this._hovering){
			multiply("Hover");
		}

		(this.styleNode || this.domNode).className = origClass + " " + classes.join(" ");
	},

	onChange: function(newValue){
		// summary: callback when value is changed
	},

	postCreate: function(){
		this.setValue(this.value, true);
		this.setDisabled(this.disabled);
		this._setStateClass();
	},

	setValue: function(/*anything*/ newValue, /*Boolean, optional*/ priorityChange){
		// summary: set the value of the widget.
		this._lastValue = newValue;
		dijit.wai.setAttr(this.focusNode || this.domNode, "waiState", "valuenow", this.forWaiValuenow());
		if((this.intermediateChanges || priorityChange) && newValue != this._lastValueReported){
			this._lastValueReported = newValue;
			this.onChange(newValue);
		}
	},

	getValue: function(){
		// summary: get the value of the widget.
		return this._lastValue;
	},

	undo: function(){
		// summary: restore the value to the last value passed to onChange
		this.setValue(this._lastValueReported, false);
	},

	_onKeyPress: function(e){
		if(e.keyCode == dojo.keys.ESCAPE && !e.shiftKey && !e.ctrlKey && !e.altKey){
			var v = this.getValue();
			var lv = this._lastValueReported;
			// Equality comparison of objects such as dates are done by reference so 
			// two distinct objects are != even if they have the same data. So use 
			// toStrings in case the values are objects.
			if(lv != undefined && v.toString() != lv.toString()){	
				this.undo();
				dojo.stopEvent(e);
				return false;
			}
		}
		return true;
	},

	forWaiValuenow: function(){
		// summary: returns a value, reflecting the current state of the widget,
		//		to be used for the ARIA valuenow.
		// 		This method may be overridden by subclasses that want
		// 		to use something other than this.getValue() for valuenow
		return this.getValue();
	}
});

}

if(!dojo._hasResource["dijit.form.Button"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.form.Button"] = true;
dojo.provide("dijit.form.Button");




dojo.declare("dijit.form.Button", dijit.form._FormWidget, {
/*
 * usage
 *	<button dojoType="button" onClick="...">Hello world</button>
 *
 *  var button1 = new dijit.form.Button({label: "hello world", onClick: foo});
 *	dojo.body().appendChild(button1.domNode);
 */
	// summary
	//	Basically the same thing as a normal HTML button, but with special styling.

	// label: String
	//	text to display in button
	label: "",

	// showLabel: Boolean
	// whether or not to display the text label in button 
	showLabel: true,

	// iconClass: String
	//	class to apply to div in button to make it display an icon
	iconClass: "",

	type: "button",
	baseClass: "dijitButton",
	templateString:"<div class=\"dijit dijitLeft dijitInline dijitButton\" baseClass=\"${baseClass}\"\n\tdojoAttachEvent=\"onclick:_onButtonClick,onmouseover:_onMouse,onmouseout:_onMouse,onmousedown:_onMouse\"\n\t><div class='dijitRight'\n\t><button class=\"dijitStretch dijitButtonNode dijitButtonContents\" dojoAttachPoint=\"focusNode,titleNode\"\n\t\ttabIndex=\"${tabIndex}\" type=\"${type}\" id=\"${id}\" name=\"${name}\" waiRole=\"button\" waiState=\"labelledby-${id}_label\"\n\t\t><div class=\"dijitInline ${iconClass}\"></div\n\t\t><span class=\"dijitButtonText\" id=\"${id}_label\" dojoAttachPoint=\"containerNode\">${label}</span\n\t></button\n></div></div>\n",

	// TODO: set button's title to this.containerNode.innerText

	_onButtonClick: function(/*Event*/ e){
		// summary: callback when the user mouse clicks the button portion
		dojo.stopEvent(e);
		if(this.disabled){ return; }
		return this.onClick(e);
	},

	postCreate: function(){
		// summary:
		//	get label and set as title on button icon if necessary
		if (this.showLabel == false){
			var labelText = "";
			this.label = this.containerNode.innerHTML;
			labelText = dojo.trim(this.containerNode.innerText || this.containerNode.textContent);
			// set title attrib on iconNode
			this.titleNode.title=labelText;
			dojo.addClass(this.containerNode,"dijitDisplayNone");
		}
		dijit.form._FormWidget.prototype.postCreate.apply(this, arguments);
	},

	onClick: function(/*Event*/ e){
		// summary: callback for when button is clicked; user can override this function

		// for some reason type=submit buttons don't automatically submit the form; do it manually
		if(this.type=="submit"){
			for(var node=this.domNode; node; node=node.parentNode){
				var widget=dijit.byNode(node);
				if(widget && widget._onSubmit){
					widget._onSubmit(e);
					break;
				}
				if(node.tagName.toLowerCase() == "form"){
					node.submit();
					break;
				}
			}
		}
	},

	setLabel: function(/*String*/ content){
		// summary: reset the label (text) of the button; takes an HTML string
		this.containerNode.innerHTML = this.label = content;
		if(dojo.isMozilla){ // Firefox has re-render issues with tables
			var oldDisplay = dojo.getComputedStyle(this.domNode).display;
			this.domNode.style.display="none";
			var _this = this;
			setTimeout(function(){_this.domNode.style.display=oldDisplay;},1);
		}
		if (this.showLabel == false){
				this.titleNode.title=dojo.trim(this.containerNode.innerText || this.containerNode.textContent);
		}
	}		
});

/*
 * usage
 *	<button dojoType="DropDownButton" label="Hello world"><div dojotype=dijit.Menu>...</div></button>
 *
 *  var button1 = new dijit.form.DropDownButton({ label: "hi", dropDown: new dijit.Menu(...) });
 *	dojo.body().appendChild(button1);
 */
dojo.declare("dijit.form.DropDownButton", [dijit.form.Button, dijit._Container], {
	// summary
	//		push the button and a menu shows up

	baseClass : "dijitDropDownButton",

	templateString:"<div class=\"dijit dijitLeft dijitInline dijitDropDownButton\" baseClass=\"dijitDropDownButton\"\n\tdojoAttachEvent=\"onmouseover:_onMouse,onmouseout:_onMouse,onmousedown:_onMouse,onclick:_onArrowClick,onkeypress:_onKey\"\n\t><div class='dijitRight'>\n\t<button tabIndex=\"${tabIndex}\" class=\"dijitStretch dijitButtonNode dijitButtonContents\" type=\"${type}\" id=\"${id}\" name=\"${name}\"\n\t\tdojoAttachPoint=\"focusNode,titleNode\" waiRole=\"button\" waiState=\"haspopup-true,labelledby-${id}_label\"\n\t\t><div class=\"dijitInline ${iconClass}\"></div\n\t\t><span class=\"dijitButtonText\" \tdojoAttachPoint=\"containerNode,popupStateNode\"\n\t\tid=\"${id}_label\">${label}</span\n\t\t><span class='dijitA11yDownArrow'>&#9660;</span>\n\t</button>\n</div></div>\n",

	_fillContent: function(){
		// my inner HTML contains both the button contents and a drop down widget, like
		// <DropDownButton>  <span>push me</span>  <Menu> ... </Menu> </DropDownButton>
		// The first node is assumed to be the button content. The widget is the popup.
		if(this.srcNodeRef){ // programatically created buttons might not define srcNodeRef
			//FIXME: figure out how to filter out the widget and use all remaining nodes as button
			//	content, not just nodes[0]
			var nodes = dojo.query("*", this.srcNodeRef);
			dijit.form.DropDownButton.superclass._fillContent.call(this, nodes[0]);

			// save pointer to srcNode so we can grab the drop down widget after it's instantiated
			this.dropDownContainer = this.srcNodeRef;
		}
	},

	startup: function(){
		// the child widget from srcNodeRef is the dropdown widget.  Insert it in the page DOM,
		// make it invisible, and store a reference to pass to the popup code.
		if(!this.dropDown){
			var dropDownNode = dojo.query("[widgetId]", this.dropDownContainer)[0];
			this.dropDown = dijit.byNode(dropDownNode);
			delete this.dropDownContainer;
		}
		dojo.body().appendChild(this.dropDown.domNode);
		this.dropDown.domNode.style.display="none";
	},

	_onArrowClick: function(/*Event*/ e){
		// summary: callback when the user mouse clicks on menu popup node
		if(this.disabled){ return; }
		this._toggleDropDown();
	},

	_onKey: function(/*Event*/ e){
		// summary: callback when the user presses a key on menu popup node
		if(this.disabled){ return; }
		if(e.keyCode == dojo.keys.DOWN_ARROW){
			if(!this.dropDown || this.dropDown.domNode.style.display=="none"){
				dojo.stopEvent(e);
				return this._toggleDropDown();
			}
		}
	},

	_onBlur: function(){
		// summary: called magically when focus has shifted away from this widget and it's dropdown
		dijit.popup.closeAll();
		// don't focus on button.  the user has explicitly focused on something else.
	},

	_toggleDropDown: function(){
		// summary: toggle the drop-down widget; if it is up, close it, if not, open it
		if(this.disabled){ return; }
		dijit.focus(this.popupStateNode);
		var dropDown = this.dropDown;
		if(!dropDown){ return false; }
		if(!dropDown.isShowingNow){
			// If there's an href, then load that first, so we don't get a flicker
			if(dropDown.href && !dropDown.isLoaded){
				var self = this;
				var handler = dojo.connect(dropDown, "onLoad", function(){
					dojo.disconnect(handler);
					self._openDropDown();
				});
				dropDown._loadCheck(true);
				return;
			}else{
				this._openDropDown();
			}
		}else{
			dijit.popup.closeAll();
			this._opened = false;
		}
	},
	
	_openDropDown: function(){
		var dropDown = this.dropDown;
		var oldWidth=dropDown.domNode.style.width;
		var self = this;

		dijit.popup.open({
			parent: this,
			popup: dropDown,
			around: this.domNode,
			orient: this.isLeftToRight() ? {'BL':'TL', 'BR':'TR', 'TL':'BL', 'TR':'BR'}
				: {'BR':'TR', 'BL':'TL', 'TR':'BR', 'TL':'BL'},
			onExecute: function(){
				dijit.popup.closeAll();
				self.focus();
			},
			onCancel: function(){
				dijit.popup.closeAll();
				self.focus();
			},
			onClose: function(){
				dropDown.domNode.style.width = oldWidth;
				self.popupStateNode.removeAttribute("popupActive");
			}
		});
		if(this.domNode.offsetWidth > dropDown.domNode.offsetWidth){
			var adjustNode = null;
			if(!this.isLeftToRight()){
				adjustNode = dropDown.domNode.parentNode; 
				var oldRight = adjustNode.offsetLeft + adjustNode.offsetWidth;
			}
			// make menu at least as wide as the button
			dojo.marginBox(dropDown.domNode, {w: this.domNode.offsetWidth});
			if(adjustNode){
				adjustNode.style.left = oldRight - this.domNode.offsetWidth + "px";
			}
		}
		this.popupStateNode.setAttribute("popupActive", "true");
		this._opened=true;
		if(dropDown.focus){
			dropDown.focus();
		}
		// TODO: set this.checked and call setStateClass(), to affect button look while drop down is shown
	}
});

/*
 * usage
 *	<button dojoType="ComboButton" onClick="..."><span>Hello world</span><div dojoType=dijit.Menu>...</div></button>
 *
 *  var button1 = new dijit.form.ComboButton({label: "hello world", onClick: foo, dropDown: "myMenu"});
 *	dojo.body().appendChild(button1.domNode);
 */
dojo.declare("dijit.form.ComboButton", dijit.form.DropDownButton, {
	// summary
	//		left side is normal button, right side displays menu
	templateString:"<table class='dijit dijitReset dijitInline dijitLeft dijitComboButton'  baseClass='dijitComboButton'\n\tid=\"${id}\" name=\"${name}\" cellspacing='0' cellpadding='0'\n\tdojoAttachEvent=\"onmouseover:_onMouse,onmouseout:_onMouse,onmousedown:_onMouse\">\n\t<tr>\n\t\t<td\tclass=\"dijitStretch dijitButtonContents dijitButtonNode\"\n\t\t\ttabIndex=\"${tabIndex}\"\n\t\t\tdojoAttachEvent=\"ondijitclick:_onButtonClick\"  dojoAttachPoint=\"titleNode\"\n\t\t\twaiRole=\"button\" waiState=\"labelledby-${id}_label\">\n\t\t\t<div class=\"dijitInline ${iconClass}\"></div>\n\t\t\t<span class=\"dijitButtonText\" id=\"${id}_label\" dojoAttachPoint=\"containerNode\">${label}</span>\n\t\t</td>\n\t\t<td class='dijitReset dijitRight dijitButtonNode dijitDownArrowButton'\n\t\t\tdojoAttachPoint=\"popupStateNode,focusNode\"\n\t\t\tdojoAttachEvent=\"onmouseover:_onMouse,onmouseout:_onMouse,onmousedown:_onMouse,ondijitclick:_onArrowClick, onkeypress:_onKey\"\n\t\t\tbaseClass=\"dijitComboButtonDownArrow\"\n\t\t\ttitle=\"${optionsTitle}\"\n\t\t\ttabIndex=\"${tabIndex}\"\n\t\t\twaiRole=\"button\" waiState=\"haspopup-true\"\n\t\t><div waiRole=\"presentation\">&#9660;</div>\n\t</td></tr>\n</table>\n",

	// optionsTitle: String
	//  text that describes the options menu (accessibility)
	optionsTitle: "",

	baseClass: "dijitComboButton"
});

dojo.declare("dijit.form.ToggleButton", dijit.form.Button, {
	// summary
	//	A button that can be in two states (checked or not).
	//	Can be base class for things like tabs or checkbox or radio buttons

	baseClass: "dijitToggleButton",

	// checked: Boolean
	//		Corresponds to the native HTML <input> element's attribute.
	//		In markup, specified as "checked='checked'" or just "checked".
	//		True if the button is depressed, or the checkbox is checked,
	//		or the radio button is selected, etc.
	checked: false,

	onClick: function(/*Event*/ evt){
		this.setChecked(!this.checked);
	},

	setChecked: function(/*Boolean*/ checked){
		// summary
		//	Programatically deselect the button
		this.checked = checked;
		this._setStateClass();
		this.onChange(checked);
	}
});

}

if(!dojo._hasResource["dijit._editor._Plugin"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._editor._Plugin"] = true;
dojo.provide("dijit._editor._Plugin");




dojo.declare("dijit._editor._Plugin", null, {
	// summary
	//		This represents a "plugin" to the editor, which is basically
	//		a single button on the Toolbar and some associated code
	constructor: function(/*Object?*/args, /*DomNode?*/node){
		if(args){
			dojo.mixin(this, args);
		}
	},
	
	editor: null,
	iconClassPrefix: "dijitEditorIcon",
	button: null,
	queryCommand: null,
	command: "",
	commandArg: null,
	useDefaultCommand: true,
	buttonClass: dijit.form.Button,
	updateInterval: 200, // only allow updates every two tenths of a second
	_initButton: function(){
		if(this.command.length){
			var label = this.editor.commands[this.command];
			var className = this.iconClassPrefix + this.command.charAt(0).toUpperCase() + this.command.substr(1);
			if(!this.button){
				var props = {
					label: label,
					showLabel: false,
					iconClass: className,
					dropDown: this.dropDown
				};
				this.button = new this.buttonClass(props);
			}
		}
	},
	updateState: function(){
		var _e = this.editor;
		var _c = this.command;
		if(!_e){ return; }
		if(!_e.isLoaded){ return; }
		if(!_c.length){ return; }
		if(this.button){
			try{
				var enabled = _e.queryCommandEnabled(_c);
				this.button.setDisabled(!enabled);
				if(this.button.setChecked){
					this.button.setChecked(_e.queryCommandState(_c));
				}
			}catch(e){
				console.debug(e);
			}
		}
	},
	setEditor: function(/*Widget*/editor){
		// FIXME: detatch from previous editor!!
		this.editor = editor;

		// FIXME: prevent creating this if we don't need to (i.e., editor can't handle our command)
		this._initButton();

		// FIXME: wire up editor to button here!
		if(	(this.command.length) && 
			(!this.editor.queryCommandAvailable(this.command))
		){
			// console.debug("hiding:", this.command);
			if(this.button){ 
				this.button.domNode.style.display = "none";
			}
		}
		if(this.button && this.useDefaultCommand){
			dojo.connect(this.button, "onClick",
				dojo.hitch(this.editor, "execCommand", this.command, this.commandArg)
			);
		}
		dojo.connect(this.editor, "onNormalizedDisplayChanged", this, "updateState");
	},
	setToolbar: function(/*Widget*/toolbar){
		if(this.button){
			toolbar.addChild(this.button);
		}
		// console.debug("adding", this.button, "to:", toolbar);
	}
});

}

if(!dojo._hasResource["dijit.Editor"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.Editor"] = true;
dojo.provide("dijit.Editor");







dojo.declare(
	"dijit.Editor",
	[ dijit._editor.RichText, dijit._Container ],
	{
		// plugins:
		//		a list of plugin names (as strings) or instances (as objects)
		//		for this widget.
//		plugins: [ "dijit._editor.plugins.DefaultToolbar" ],
		plugins: null,
		preamble: function(){
			if(this.plugins){
				this.plugins=this.plugins.slice(0);
			}else{
				this.plugins=["cut","copy","paste","|","bold","italic","underline","strikethrough","|",
			"insertOrderedList","insertUnorderedList","indent","outdent"/*,"|","createlink"*/];
			}
			this._plugins=[];
		},
		toolbar: null,
		postCreate: function(){
//			try{
			dijit.Editor.superclass.postCreate.apply(this, arguments);

			this.commands = dojo.i18n.getLocalization("dijit._editor", "commands", this.lang);

			if(!this.toolbar){
				// if we haven't been assigned a toolbar, create one
				this.toolbar = new dijit.Toolbar();
				dojo.place(this.toolbar.domNode, this.editingArea, "before");
			}

			dojo.forEach(this.plugins, this.addPlugin, this);
//			}catch(e){ console.debug(e); }
		},
		destroy: function(){
			dojo.forEach(this._plugins, function(p){
				if(p.destroy){
					p.destroy();
				}
			});
			this._plugins=[];
			this.inherited('destroy',arguments);
		},
		addPlugin: function(/*String||Object*/plugin, /*Integer?*/index){
			//	summary:
			//		takes a plugin name as a string or a plugin instance and
			//		adds it to the toolbar and associates it with this editor
			//		instance. The resulting plugin is added to the Editor's
			//		plugins array. If index is passed, it's placed in the plugins
			//		array at that index. No big magic, but a nice helper for
			//		passing in plugin names via markup. 
			//	plugin: String, args object or plugin instance. Required.
			//	args: This object will be passed to the plugin constructor.
			//	index:	
			//		Integer, optional. Used when creating an instance from
			//		something already in this.plugins. Ensures that the new
			//		instance is assigned to this.plugins at that index.
			var args=dojo.isString(plugin)?{name:plugin}:plugin;
			if(!args.setEditor){
				var o={"args":args,"plugin":null};
				dojo.publish("dijit.Editor.getPlugin",[o]);
				if(!o.plugin){
					var pc = dojo.getObject(args.name);
					if(pc){
						o.plugin=new pc(args);
					}
				}
				if(!o.plugin){
					console.debug('Can not find plugin',plugin);
					return;
				}
				plugin=o.plugin;
			}
			if(arguments.length > 1){
				this._plugins[index] = plugin;
			}else{
				this._plugins.push(plugin);
			}
			plugin.setEditor(this);
			if(dojo.isFunction(plugin.setToolbar)){
				plugin.setToolbar(this.toolbar);
			}
		}
	}
);

dojo.subscribe("dijit.Editor.getPlugin",null,function(o){
	if(o.plugin){ return; }
	var args=o.args, p;
	var _p = dijit._editor._Plugin;
	switch(args.name){
		case "cut": case "copy": case "paste": case "insertOrderedList":
		case "insertUnorderedList": case "indent": case "outdent":
			p = new _p({ command: args.name });
			break;
		case "bold": case "italic": case "underline": case "strikethrough":
			//shall we try to auto require here? or require user to worry about it?
//					dojo['require']('dijit.form.Button');
			p = new _p({ buttonClass: dijit.form.ToggleButton, command: args.name });
			break;
		case "|":
			p = new _p({ button: new dijit.ToolbarSeparator() });
			break;
		case "createlink":
//					dojo['require']('dijit._editor.plugins.LinkDialog');
			p = new dijit._editor.plugins.LinkDialog();
			break;
	}
//	console.log('args.name',args.name,p);
	o.plugin=p;
});

}

if(!dojo._hasResource["dojo.regexp"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.regexp"] = true;
dojo.provide("dojo.regexp");

dojo.regexp.escapeString = function(/*String*/str, /*String?*/except){
//summary:
//	Adds escape sequences for special characters in regular expressions
// except: a String with special characters to be left unescaped

//	return str.replace(/([\f\b\n\t\r[\^$|?*+(){}])/gm, "\\$1"); // string
	return str.replace(/([\.$?*!=:|{}\(\)\[\]\\\/^])/g, function(ch){
		if(except && except.indexOf(ch) != -1){
			return ch;
		}
		return "\\" + ch;
	}); // String
}

dojo.regexp.buildGroupRE = function(/*value or Array of values*/a, /*Function(x) returns a regular expression as a String*/re,
	/*Boolean?*/nonCapture){
	// summary: Builds a regular expression that groups subexpressions
	// description: A utility function used by some of the RE generators.
	//  The subexpressions are constructed by the function, re, in the second parameter.
	//  re builds one subexpression for each elem in the array a, in the first parameter.
	//  Returns a string for a regular expression that groups all the subexpressions.
	//
	// a:  A single value or an array of values.
	// re:  A function.  Takes one parameter and converts it to a regular expression. 
	// nonCapture: If true, uses non-capturing match, otherwise matches are retained by regular expression. 

	// case 1: a is a single value.
	if(!(a instanceof Array)){
		return re(a); // String
	}

	// case 2: a is an array
	var b = [];
	for (var i = 0; i < a.length; i++){
		// convert each elem to a RE
		b.push(re(a[i]));
	}

	 // join the REs as alternatives in a RE group.
	return dojo.regexp.group(b.join("|"), nonCapture); // String
}

dojo.regexp.group = function(/*String*/expression, /*Boolean?*/nonCapture){
	// summary: adds group match to expression
	// nonCapture: If true, uses non-capturing match, otherwise matches are retained by regular expression. 
	return "(" + (nonCapture ? "?:":"") + expression + ")"; // String
}

}

if(!dojo._hasResource["dojo.number"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.number"] = true;
dojo.provide("dojo.number");






dojo.number.format = function(/*Number*/value, /*Object?*/options){
	// summary:
	//		Format a Number as a String, using locale-specific settings
	//
	// description:
	//		Create a string from a Number using a known localized pattern.
	//		Formatting patterns appropriate to the locale are chosen from the CLDR http://unicode.org/cldr
	//		as well as the appropriate symbols and delimiters.  See http://www.unicode.org/reports/tr35/#Number_Elements
	//
	// value:
	//		the number to be formatted.  If not a valid JavaScript number, return null.
	//
	// options: object {pattern: String?, type: String?, places: Number?, round: Number?, currency: String?, symbol: String?, locale: String?}
	//		pattern- override formatting pattern with this string (see dojo.number._applyPattern)
	//		type- choose a format type based on the locale from the following: decimal, scientific, percent, currency. decimal by default.
	//		places- fixed number of decimal places to show.  This overrides any information in the provided pattern.
	//		round- 5 rounds to nearest .5; 0 rounds to nearest whole (default). -1 means don't round.
	//		currency- iso4217 currency code
	//		symbol- localized currency symbol
	//		locale- override the locale used to determine formatting rules

	options = dojo.mixin({}, options || {});
	var locale = dojo.i18n.normalizeLocale(options.locale);
	var bundle = dojo.i18n.getLocalization("dojo.cldr", "number", locale);
	options.customs = bundle;
	var pattern = options.pattern || bundle[(options.type || "decimal") + "Format"];
	if(isNaN(value)){ return null; } // null
	return dojo.number._applyPattern(value, pattern, options); // String
};

//dojo.number._numberPatternRE = /(?:[#0]*,?)*[#0](?:\.0*#*)?/; // not precise, but good enough
dojo.number._numberPatternRE = /[#0,]*[#0](?:\.0*#*)?/; // not precise, but good enough

dojo.number._applyPattern = function(/*Number*/value, /*String*/pattern, /*Object?*/options){
	// summary: Apply pattern to format value as a string using options. Gives no consideration to local customs.
	// value: the number to be formatted.
	// pattern: a pattern string as described in http://www.unicode.org/reports/tr35/#Number_Format_Patterns
	// options: object {customs: Object?, places: Number?, currency: String?, round: Number?, symbol: String?}
	//  customs- a hash containing: decimal, group, ...

//TODO: support escapes
	options = options || {};
	var group = options.customs.group;
	var decimal = options.customs.decimal;

	var patternList = pattern.split(';');
	var positivePattern = patternList[0];
	pattern = patternList[(value < 0) ? 1 : 0] || ("-" + positivePattern);

	//TODO: only test against unescaped
	if(pattern.indexOf('%') != -1){
		value *= 100;
	}else if(pattern.indexOf('\u2030') != -1){
		value *= 1000; // per mille
	}else if(pattern.indexOf('\u00a4') != -1){
		group = options.customs.currencyGroup || group;//mixins instead?
		decimal = options.customs.currencyDecimal || decimal;// Should these be mixins instead?
		pattern = pattern.replace(/\u00a4{1,3}/, function(match){
			var prop = ["symbol", "currency", "displayName"][match.length-1];
			return options[prop] || options.currency || "";
		});
	}else if(pattern.indexOf('E') != -1){
		throw new Error("exponential notation not supported");
	}
	
//TODO: support @ sig figs?
	var numberPatternRE = dojo.number._numberPatternRE;
	var numberPattern = positivePattern.match(numberPatternRE);
	if(!numberPattern){
		throw new Error("unable to find a number expression in pattern: "+pattern);
	}
	return pattern.replace(numberPatternRE,
		dojo.number._formatAbsolute(value, numberPattern[0], {decimal: decimal, group: group, places: options.places}));
}

dojo.number.round = function(/*Number*/value, /*Number*/places, /*Number?*/multiple){
	// summary: Rounds the number at the given number of places
	// value: the number to round
	// places: the number of decimal places where rounding takes place
	// multiple: rounds next place to nearest multiple

	var pieces = String(value).split(".");
	var length = (pieces[1] && pieces[1].length) || 0;
	if(length > places){
		var factor = Math.pow(10, places);
		if(multiple > 0){factor *= 10/multiple;places++;} //FIXME
		value = Math.round(value * factor)/factor;

		// truncate to remove any residual floating point values
		pieces = String(value).split(".");
		length = (pieces[1] && pieces[1].length) || 0;
		if(length > places){
			pieces[1] = pieces[1].substr(0, places);
			value = Number(pieces.join("."));
		}
	}
	return value; //Number
}

dojo.number._formatAbsolute = function(/*Number*/value, /*String*/pattern, /*Object?*/options){
	// summary: 
	//		Apply numeric pattern to absolute value using options. Gives no
	//		consideration to local customs.
	// value:
	//		the number to be formatted, ignores sign
	// pattern:
	//		the number portion of a pattern (e.g. #,##0.00)
	// options:
	//		object {decimal: String?, group: String?, places: Number?}
	//  		decimal: the decimal separator
	//  		group: the group separator
	//  		places: number of decimal places
	//  		round: 5 rounds to nearest .5; 0 rounds to nearest whole (default). -1 means don't round.
	options = options || {};
	if(options.places === true){options.places=0;}
	if(options.places === Infinity){options.places=6;} // avoid a loop; pick a limit

	var patternParts = pattern.split(".");
	var maxPlaces = (options.places >= 0) ? options.places : (patternParts[1] && patternParts[1].length) || 0;
	if(!(options.round < 0)){
		value = dojo.number.round(value, maxPlaces, options.round);
	}

	var valueParts = String(Math.abs(value)).split(".");
	var fractional = valueParts[1] || "";
	if(options.places){
		valueParts[1] = dojo.string.pad(fractional.substr(0, options.places), options.places, '0', true);
	}else if(patternParts[1] && options.places !== 0){
		// Pad fractional with trailing zeros
		var pad = patternParts[1].lastIndexOf("0") + 1;
		if(pad > fractional.length){
			valueParts[1] = dojo.string.pad(fractional, pad, '0', true);
		}

		// Truncate fractional
		var places = patternParts[1].length;
		if(places < fractional.length){
			valueParts[1] = fractional.substr(0, places);
		}
	}else{
		if(valueParts[1]){ valueParts.pop(); }
	}

	// Pad whole with leading zeros
	var patternDigits = patternParts[0].replace(',', '');
	pad = patternDigits.indexOf("0");
	if(pad != -1){
		pad = patternDigits.length - pad;
		if(pad > valueParts[0].length){
			valueParts[0] = dojo.string.pad(valueParts[0], pad);
		}

		// Truncate whole
		if(patternDigits.indexOf("#") == -1){
			valueParts[0] = valueParts[0].substr(valueParts[0].length - pad);
		}
	}

	// Add group separators
	var index = patternParts[0].lastIndexOf(',');
	var groupSize, groupSize2;
	if(index != -1){
		groupSize = patternParts[0].length - index - 1;
		var remainder = patternParts[0].substr(0, index);
		index = remainder.lastIndexOf(',');
		if(index != -1){
			groupSize2 = remainder.length - index - 1;
		}
	}
	var pieces = [];
	for(var whole = valueParts[0]; whole;){
		var off = whole.length - groupSize;
		pieces.push((off > 0) ? whole.substr(off) : whole);
		whole = (off > 0) ? whole.slice(0, off) : "";
		if(groupSize2){
			groupSize = groupSize2;
			delete groupSize2;
		}
	}
	valueParts[0] = pieces.reverse().join(options.group || ",");

	return valueParts.join(options.decimal || ".");
};

dojo.number.regexp = function(/*Object?*/options){
	//
	// summary:
	//		Builds the regular needed to parse a number
	//
	// description:
	//		returns regular expression with positive and negative match, group
	//		and decimal separators
	//
	// options: object {pattern: String, type: String locale: String, strict: Boolean, places: mixed}
	//		pattern- override pattern with this string.  Default is provided based on locale.
	//		type- choose a format type based on the locale from the following: decimal, scientific, percent, currency. decimal by default.
	//		locale- override the locale used to determine formatting rules
	//		strict- strict parsing, false by default
	//		places- number of decimal places to accept: Infinity, a positive number, or a range "n,m".  By default, defined by pattern.
	return dojo.number._parseInfo(options).regexp; // String
}

dojo.number._parseInfo = function(/*Object?*/options){
	options = options || {};
	var locale = dojo.i18n.normalizeLocale(options.locale);
	var bundle = dojo.i18n.getLocalization("dojo.cldr", "number", locale);
	var pattern = options.pattern || bundle[(options.type || "decimal") + "Format"];
//TODO: memoize?
	var group = bundle.group;
	var decimal = bundle.decimal;
	var factor = 1;

	if(pattern.indexOf('%') != -1){
		factor /= 100;
	}else if(pattern.indexOf('\u2030') != -1){
		factor /= 1000; // per mille
	}else{
		var isCurrency = pattern.indexOf('\u00a4') != -1;
		if(isCurrency){
			group = bundle.currencyGroup || group;
			decimal = bundle.currencyDecimal || decimal;
		}
	}

	//TODO: handle quoted escapes
	var patternList = pattern.split(';');
	if(patternList.length == 1){
		patternList.push("-" + patternList[0]);
	}

	var re = dojo.regexp.buildGroupRE(patternList, function(pattern){
		pattern = "(?:"+dojo.regexp.escapeString(pattern, '.')+")";
		return pattern.replace(dojo.number._numberPatternRE, function(format){
			var flags = {
				signed: false,
				separator: options.strict ? group : [group,""],
				fractional: options.fractional,
				decimal: decimal,
				exponent: false};
			var parts = format.split('.');
			var places = options.places;
			if(parts.length == 1 || places === 0){flags.fractional = false;}
			else{
				if(typeof places == "undefined"){ places = parts[1].lastIndexOf('0')+1; }
				if(places && options.fractional == undefined){flags.fractional = true;} // required fractional, unless otherwise specified
				if(!options.places && (places < parts[1].length)){ places += "," + parts[1].length; }
				flags.places = places;
			}
			var groups = parts[0].split(',');
			if(groups.length>1){
				flags.groupSize = groups.pop().length;
				if(groups.length>1){
					flags.groupSize2 = groups.pop().length;
				}
			}
			return "("+dojo.number._realNumberRegexp(flags)+")";
		});
	}, true);

	if(isCurrency){
		// substitute the currency symbol for the placeholder in the pattern
		re = re.replace(/(\s*)(\u00a4{1,3})(\s*)/g, function(match, before, target, after){
			var prop = ["symbol", "currency", "displayName"][target.length-1];
			var symbol = dojo.regexp.escapeString(options[prop] || options.currency || "");
			before = before ? "\\s" : "";
			after = after ? "\\s" : "";
			if(!options.strict){
				if(before){before += "*";}
				if(after){after += "*";}
				return "(?:"+before+symbol+after+")?";
			}
			return before+symbol+after;
		});
	}

//TODO: substitute localized sign/percent/permille/etc.?

	// normalize whitespace and return
	return {regexp: re.replace(/[\xa0 ]/g, "[\\s\\xa0]"), group: group, decimal: decimal, factor: factor}; // Object
}

dojo.number.parse = function(/*String*/expression, /*Object?*/options){
	// summary:
	//		Convert a properly formatted string to a primitive Number,
	//		using locale-specific settings.
	//
	// description:
	//		Create a Number from a string using a known localized pattern.
	//		Formatting patterns are chosen appropriate to the locale.
	//		Formatting patterns are implemented using the syntax described at
	//		*URL*
	//
	// expression: A string representation of a Number
	//
	// options: 
	//		object {pattern: string, locale: string, strict: boolean}
	//		pattern:
	//			override pattern with this string
	//		type:
	//			choose a format type based on the locale from the following:
	//			decimal, scientific, percent, currency. decimal by default.
	//		locale:
	//			override the locale used to determine formatting rules
	//		strict: 
	//			strict parsing, false by default
	//		currency:
	//			object with currency information

	var info = dojo.number._parseInfo(options);
	var results = (new RegExp("^"+info.regexp+"$")).exec(expression);
	if(!results){
		return NaN; //NaN
	}
	var absoluteMatch = results[1]; // match for the positive expression
	if(!results[1]){
		if(!results[2]){
			return NaN; //NaN
		}
		// matched the negative pattern
		absoluteMatch =results[2];
		info.factor *= -1;
	}

	// Transform it to something Javascript can parse as a number.  Normalize
	// decimal point and strip out group separators or alternate forms of whitespace
	absoluteMatch = absoluteMatch.
		replace(new RegExp("["+info.group + "\\s\\xa0"+"]", "g"), "").
		replace(info.decimal, ".");
	// Adjust for negative sign, percent, etc. as necessary
	return Number(absoluteMatch) * info.factor; //Number
};

dojo.number._realNumberRegexp = function(/*Object?*/flags){
	// summary: Builds a regular expression to match a real number in exponential notation
	//
	// flags:An object
	//		flags.places:
	//			The integer number of decimal places or a range given as "n,m".
	//			If not given, the decimal part is optional and the number of
	//			places is unlimited.
	//		flags.decimal:
	//			A string for the character used as the decimal point.  Default
	//			is ".".
	//    flags.fractional:
	//			Whether decimal places are allowed.  Can be true, false, or
	//			[true, false].  Default is [true, false]
	//    flags.exponent:
	//			Express in exponential notation.  Can be true, false, or [true,
	//			false]. Default is [true, false], (i.e. will match if the
	//			exponential part is present are not).
	//    flags.eSigned:
	//			The leading plus-or-minus sign on the exponent.  Can be true,
	//			false, or [true, false].  Default is [true, false], (i.e. will
	//			match if it is signed or unsigned).  flags in regexp.integer
	//			can be applied.

	// assign default values to missing paramters
	flags = flags || {};
	if(typeof flags.places == "undefined"){ flags.places = Infinity; }
	if(typeof flags.decimal != "string"){ flags.decimal = "."; }
	if(typeof flags.fractional == "undefined" || /^0/.test(flags.places)){ flags.fractional = [true, false]; }
	if(typeof flags.exponent == "undefined"){ flags.exponent = [true, false]; }
	if(typeof flags.eSigned == "undefined"){ flags.eSigned = [true, false]; }

	// integer RE
	var integerRE = dojo.number._integerRegexp(flags);

	// decimal RE
	var decimalRE = dojo.regexp.buildGroupRE(flags.fractional,
		function(q){
			var re = "";
			if(q && (flags.places!==0)){
				re = "\\" + flags.decimal;
				if(flags.places == Infinity){ 
					re = "(?:" + re + "\\d+)?"; 
				}else{
					re += "\\d{" + flags.places + "}"; 
				}
			}
			return re;
		},
		true
	);

	// exponent RE
	var exponentRE = dojo.regexp.buildGroupRE(flags.exponent,
		function(q){ 
			if(q){ return "([eE]" + dojo.number._integerRegexp({ signed: flags.eSigned}) + ")"; }
			return ""; 
		}
	);

	// real number RE
	var realRE = integerRE + decimalRE;
	// allow for decimals without integers, e.g. .25
	if(decimalRE){realRE = "(?:(?:"+ realRE + ")|(?:" + decimalRE + "))";}
	return realRE + exponentRE; // String
};

dojo.number._integerRegexp = function(/*Object?*/flags){
	// summary: 
	//		Builds a regular expression that matches an integer
	// flags: 
	//		An object
	//		flags.signed :
	//			The leading plus-or-minus sign. Can be true, false, or [true,
	//			false]. Default is [true, false], (i.e. will match if it is
	//			signed or unsigned).
	//		flags.separator:
	//			The character used as the thousands separator. Default is no
	//			separator. For more than one symbol use an array, e.g. [",",
	//			""], makes ',' optional.
	//		flags.groupSize: group size between separators
	//		flags.groupSize2: second grouping (for India)

	// assign default values to missing paramters
	flags = flags || {};
	if(typeof flags.signed == "undefined"){ flags.signed = [true, false]; }
	if(typeof flags.separator == "undefined"){
		flags.separator = "";
	}else if(typeof flags.groupSize == "undefined"){
		flags.groupSize = 3;
	}
	// build sign RE
	var signRE = dojo.regexp.buildGroupRE(flags.signed,
		function(q) { return q ? "[-+]" : ""; },
		true
	);

	// number RE
	var numberRE = dojo.regexp.buildGroupRE(flags.separator,
		function(sep){
			if(!sep){
				return "(?:0|[1-9]\\d*)";
			}

			sep = dojo.regexp.escapeString(sep);
			if(sep == " "){ sep = "\\s"; }
			else if(sep == "\xa0"){ sep = "\\s\\xa0"; }

			var grp = flags.groupSize, grp2 = flags.groupSize2;
			if(grp2){
				var grp2RE = "(?:0|[1-9]\\d{0," + (grp2-1) + "}(?:[" + sep + "]\\d{" + grp2 + "})*[" + sep + "]\\d{" + grp + "})";
				return ((grp-grp2) > 0) ? "(?:" + grp2RE + "|(?:0|[1-9]\\d{0," + (grp-1) + "}))" : grp2RE;
			}
			return "(?:0|[1-9]\\d{0," + (grp-1) + "}(?:[" + sep + "]\\d{" + grp + "})*)";
		},
		true
	);

	// integer RE
	return signRE + numberRE; // String
}

}

if(!dojo._hasResource["dijit.ProgressBar"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.ProgressBar"] = true;
dojo.provide("dijit.ProgressBar");







dojo.declare("dijit.ProgressBar", [dijit._Widget, dijit._Templated], {
	// summary:
	// a progress widget
	//
	// usage:
	// <div dojoType="ProgressBar"
	//   places="0"
	//   progress="..." maximum="..."></div>

	// progress: String (Percentage or Number)
	// initial progress value.
	// with "%": percentage value, 0% <= progress <= 100%
	// or without "%": absolute value, 0 <= progress <= maximum
	progress: "0",

	// maximum: Float
	// max sample number
	maximum: 100,

	// places: Number
	// number of places to show in values; 0 by default
	places: 0,

	// indeterminate: Boolean
	// false: show progress
	// true: show that a process is underway but that the progress is unknown
	indeterminate: false,

	templateString:"<div class=\"dijitProgressBar dijitProgressBarEmpty\"\n\t><div waiRole=\"progressbar\" tabindex=\"0\" dojoAttachPoint=\"internalProgress\" class=\"dijitProgressBarFull\"\n\t\t><div class=\"dijitProgressBarTile\"></div\n\t\t><span style=\"visibility:hidden\">&nbsp;</span\n\t></div\n\t><div dojoAttachPoint=\"label\" class=\"dijitProgressBarLabel\">&nbsp;</div\n\t><img dojoAttachPoint=\"inteterminateHighContrastImage\" class=\"dijitProgressBarIndeterminateHighContrastImage\"\n\t></img\n></div>\n",

	_indeterminateHighContrastImagePath:
		dojo.moduleUrl("dijit", "themes/a11y/indeterminate_progress.gif"),

	// public functions
	postCreate: function(){
		dijit.ProgressBar.superclass.postCreate.apply(this, arguments);

		this.inteterminateHighContrastImage.setAttribute("src",
			this._indeterminateHighContrastImagePath);

		this.update();
	},

	update: function(/*Object?*/attributes){
		// summary: update progress information
		//
		// attributes: may provide progress and/or maximum properties on this parameter,
		//	see attribute specs for details.
		dojo.mixin(this, attributes||{});
		var percent = 1, classFunc;
		if(this.indeterminate){
			classFunc = "addClass";
			dijit.wai.removeAttr(this.internalProgress, "waiState", "valuenow");
		}else{
			classFunc = "removeClass";
			if(String(this.progress).indexOf("%") != -1){
				percent = Math.min(parseFloat(this.progress)/100, 1);
				this.progress = percent * this.maximum;
			}else{
				this.progress = Math.min(this.progress, this.maximum);
				percent = this.progress / this.maximum;
			}
			var text = this.report(percent);
			this.label.firstChild.nodeValue = text;
			dijit.wai.setAttr(this.internalProgress, "waiState", "valuenow", text);
		}
		dojo[classFunc](this.domNode, "dijitProgressBarIndeterminate");
		this.internalProgress.style.width = (percent * 100) + "%";
		this.onChange();
	},

	report: function(/*float*/percent){
		// Generates message to show; may be overridden by user
		return dojo.number.format(percent, {type: "percent", places: this.places, locale: this.lang});
	},

	onChange: function(){}
});

}

if(!dojo._hasResource["dijit.TitlePane"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.TitlePane"] = true;
dojo.provide("dijit.TitlePane");






dojo.declare(
	"dijit.TitlePane",
	[dijit.layout.ContentPane, dijit._Templated],
{
	// summary
	//		A pane with a title on top, that can be opened or collapsed.

	// title: String
	//		Title of the pane
	title: "",

	// open: Boolean
	//		Whether pane is opened or closed.
	open: true,

	// duration: Integer
	//		milliseconds to fade in/fade out
	duration: 250,

	templateString:"<div class=\"dijitTitlePane\">\n\t<div dojoAttachEvent=\"onclick:toggle,onkeypress: _onTitleKey\" tabindex=\"0\"\n\t\t\twaiRole=\"button\" class=\"dijitTitlePaneTitle\" dojoAttachPoint=\"focusNode\">\n\t\t<span dojoAttachPoint=\"arrowNode\" class=\"dijitInline dijitArrowNode\"><span dojoAttachPoint=\"arrowNodeInner\" class=\"dijit_a11y dijitArrowNodeInner\"></span></span>\n\t\t<span dojoAttachPoint=\"titleNode\" class=\"dijitInlineBox dijitTitleNode\"></span>\n\t</div>\n\t<div class=\"dijitTitlePaneContentOuter\" dojoAttachPoint=\"hideNode\">\n\t\t<div class=\"dijitReset\" dojoAttachPoint=\"wipeNode\">\n\t\t\t<div class=\"dijitTitlePaneContentInner\" dojoAttachPoint=\"containerNode\" waiRole=\"region\" tabindex=\"-1\">\n\t\t\t\t<!-- nested divs because wipeIn()/wipeOut() doesn't work right on node w/padding etc.  Put padding on inner div. -->\n\t\t\t</div>\n\t\t</div>\n\t</div>\n</div>\n",

	postCreate: function(){
		this.setTitle(this.title);
		if(!this.open){
			this.hideNode.style.display = this.wipeNode.style.display = "none";
		}
		this._setCss();
		dojo.setSelectable(this.titleNode, false);
		dijit.TitlePane.superclass.postCreate.apply(this, arguments);
		dijit.wai.setAttr(this.containerNode, "waiState", "titleledby", this.titleNode.id);
		dijit.wai.setAttr(this.focusNode, "waiState", "haspopup", "true");

		// setup open/close animations
		var hideNode = this.hideNode, wipeNode = this.wipeNode;
		this._wipeIn = dojo.fx.wipeIn({
			node: this.wipeNode,
			duration: this.duration,
			beforeBegin: function(){
				hideNode.style.display="";
			}
		});
		this._wipeOut = dojo.fx.wipeOut({
			node: this.wipeNode,
			duration: this.duration,
			onEnd: function(){
				hideNode.style.display="none";
			}
		});
	},

	setContent: function(content){
		// summary
		// 		Typically called when an href is loaded.  Our job is to make the animation smooth
		if(this._wipeOut.status() == "playing"){
			// we are currently *closing* the pane, so just let that continue
			dijit.layout.ContentPane.prototype.setContent.apply(this, content);
		}else{
			if(this._wipeIn.status() == "playing"){
				this._wipeIn.stop();
			}
			
			// freeze container at current height so that adding new content doesn't make it jump
			dojo.marginBox(this.wipeNode, {h: dojo.marginBox(this.wipeNode).h});

			// add the new content (erasing the old content, if any)
			dijit.layout.ContentPane.prototype.setContent.apply(this, arguments);
			
			// call _wipeIn.play() to animate from current height to new height
			this._wipeIn.play();
		}
	},

	toggle: function(){
		// summary: switches between opened and closed state
		dojo.forEach([this._wipeIn, this._wipeOut], function(animation){
			if(animation.status() == "playing"){
				animation.stop();
			}
		});

		this[this.open ? "_wipeOut" : "_wipeIn"].play();
		this.open =! this.open;

		// load content (if this is the first time we are opening the TitlePane
		// and content is specified as an href, or we have setHref when hidden)
		this._loadCheck();

		this._setCss();
	},

	_setCss: function(){
		var classes = ["dijitClosed", "dijitOpen"];
		var boolIndex = this.open;
		dojo.removeClass(this.focusNode, classes[!boolIndex+0]);
		this.focusNode.className += " " + classes[boolIndex+0];

		// provide a character based indicator for images-off mode
		this.arrowNodeInner.innerHTML = this.open ? "-" : "+"; 
	},

	_onTitleKey: function(/*Event*/ e){
		// summary: callback when user hits a key
		if(e.keyCode == dojo.keys.ENTER || e.charCode == dojo.keys.SPACE){
			this._onTitleClick();
		}
		else if(e.keyCode == dojo.keys.DOWN_ARROW){
			if(this.open){
				this.containerNode.focus();
				e.preventDefault();
			}
	 	}
	},

	setTitle: function(/*String*/ title){
		// summary: sets the text of the title
		this.titleNode.innerHTML=title;
	}
});

}

if(!dojo._hasResource["dijit.layout._LayoutWidget"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.layout._LayoutWidget"] = true;
dojo.provide("dijit.layout._LayoutWidget");




dojo.declare("dijit.layout._LayoutWidget",
	[dijit._Widget, dijit._Container, dijit._Contained],
	{
		// summary
		//		Mixin for widgets that contain a list of children like SplitContainer.
		//		Widgets which mixin this code must define layout() to lay out the children

		isLayoutContainer: true,

		postCreate: function(){
			dojo.addClass(this.domNode, "dijitContainer");
		},

		startup: function(){
			// summary:
			//		Called after all the widgets have been instantiated and their
			//		dom nodes have been inserted somewhere under document.body.
			//
			//		Widgets should override this method to do any initialization
			//		dependent on other widgets existing, and then call
			//		this superclass method to finish things off.
			//
			//		startup() in subclasses shouldn't do anything
			//		size related because the size of the widget hasn't been set yet.

			if(this._started){
				return;
			}
			this._started=true;

			if(this.getChildren){
				dojo.forEach(this.getChildren(), function(child){ child.startup(); });
			}

			// If I am a top level widget
			if(!this.getParent || !this.getParent()){
				// Do recursive sizing and layout of all my descendants
				// (passing in no argument to resize means that it has to glean the size itself)
				this.resize();

				// since my parent isn't a layout container, and my style is width=height=100% (or something similar),
				// then I need to watch when the window resizes, and size myself accordingly
				// (passing in no argument to resize means that it has to glean the size itself)
				this.connect(window, 'onresize', function(){this.resize();});
			}
		},
		
		resize: function(args){
			// summary:
			//		Explicitly set this widget's size (in pixels),
			//		and then call layout() to resize contents (and maybe adjust child widgets)
			//	
			// args: Object?
			//		{w: int, h: int, l: int, t: int}

			var node = this.domNode;

			// set margin box size, unless it wasn't specified, in which case use current size
			if(args){
				dojo.marginBox(node, args);

				// set offset of the node
				if(args.t){ node.style.top = args.t + "px"; }
				if(args.l){ node.style.left = args.l + "px"; }
			}
			// If either height or width wasn't specified by the user, then query node for it.
			// But note that setting the margin box and then immediately querying dimensions may return
			// inaccurate results, so try not to depend on it.
			var mb = dojo.mixin(dojo.marginBox(node), args||{});

			// Save the size of my content box.
			this._contentBox = dijit.layout.marginBox2contentBox(node, mb);

			// Callback for widget to adjust size of it's children
			this.layout();
		},

		layout: function(){
			//	summary
			//		Widgets override this method to size & position their contents/children.
			//		When this is called this._contentBox is guaranteed to be set (see resize()).
			//
			//		This is called after startup(), and also when the widget's size has been
			//		changed.
		}
	}
);

dijit.layout.marginBox2contentBox = function(/*DomNode*/ node, /*Object*/ mb){
	// summary:
	//		Given the margin-box size of a node, return it's content box size.
	//		Functions like dojo.contentBox() but is more reliable since it doesn't have
	//		to wait for the browser to compute sizes.
	var cs = dojo.getComputedStyle(node);
	var me=dojo._getMarginExtents(node, cs);
	var pb=dojo._getPadBorderExtents(node, cs);
	return {
		l: dojo._toPixelValue(node, cs.paddingLeft),
		t: dojo._toPixelValue(node, cs.paddingTop),
		w: mb.w - (me.w + pb.w),
		h: mb.h - (me.h + pb.h)
	};
};

(function(){
	var capitalize = function(word){
		return word.substring(0,1).toUpperCase() + word.substring(1);
	};

	var size = function(widget, dim){
		// size the child
		widget.resize ? widget.resize(dim) : dojo.marginBox(widget.domNode, dim);
		
		// record child's size, but favor our own numbers when we have them.
		// the browser lies sometimes
		dojo.mixin(widget, dojo.marginBox(widget.domNode));
		dojo.mixin(widget, dim);
	};

	dijit.layout.layoutChildren = function(/*DomNode*/ container, /*Object*/ dim, /*Object[]*/ children){
		/**
		 * summary
		 *		Layout a bunch of child dom nodes within a parent dom node
		 * container:
		 *		parent node
		 * dim:
		 *		{l, t, w, h} object specifying dimensions of container into which to place children
		 * children:
		 *		an array like [ {domNode: foo, layoutAlign: "bottom" }, {domNode: bar, layoutAlign: "client"} ]
		 */

		// copy dim because we are going to modify it
		dim = dojo.mixin({}, dim);

		dojo.addClass(container, "dijitLayoutContainer");

		// set positions/sizes
		dojo.forEach(children, function(child){
			var elm = child.domNode,
				pos = child.layoutAlign;

			// set elem to upper left corner of unused space; may move it later
			var elmStyle = elm.style;
			elmStyle.left = dim.l+"px";
			elmStyle.top = dim.t+"px";
			elmStyle.bottom = elmStyle.right = "auto";

			dojo.addClass(elm, "dijitAlign" + capitalize(pos));

			// set size && adjust record of remaining space.
			// note that setting the width of a <div> may affect it's height.
			if(pos=="top" || pos=="bottom"){
				size(child, { w: dim.w });
				dim.h -= child.h;
				if(pos=="top"){
					dim.t += child.h;
				}else{
					elmStyle.top = dim.t + dim.h + "px";
				}
			}else if(pos=="left" || pos=="right"){
				size(child, { h: dim.h });
				dim.w -= child.w;
				if(pos=="left"){
					dim.l += child.w;
				}else{
					elmStyle.left = dim.l + dim.w + "px";
				}
			}else if(pos=="flood" || pos=="client"){
				size(child, dim);
			}
		});
	};

})();

}

if(!dojo._hasResource["dijit.layout.StackContainer"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.layout.StackContainer"] = true;
dojo.provide("dijit.layout.StackContainer");





dojo.declare(
	"dijit.layout.StackContainer",
	dijit.layout._LayoutWidget,

	// summary
	//	A container that has multiple children, but shows only
	//	one child at a time (like looking at the pages in a book one by one).
	//
	//	Publishes topics <widgetId>-addChild, <widgetId>-removeChild, and <widgetId>-selectChild
	//
	//	Can be base class for container, Wizard, Show, etc.
{
	// doLayout: Boolean
	//  if true, change the size of my currently displayed child to match my size
	doLayout: true,

	_started: false,

	startup: function(){
		var children = this.getChildren();

		// Setup each page panel
		dojo.forEach(children, this._setupChild, this);

		// Figure out which child to initially display
		dojo.some(children, function(child){
			if(child.selected){
				this.selectedChildWidget = child;
			}
			return child.selected;
		}, this);

		// Default to the first child
		if(!this.selectedChildWidget && children[0]){
			this.selectedChildWidget = children[0];
			this.selectedChildWidget.selected = true;
		}
		if(this.selectedChildWidget){
			this._showChild(this.selectedChildWidget);
		}

		// Now publish information about myself so any StackControllers can initialize..
		dojo.publish(this.id+"-startup", [{children: children, selected: this.selectedChildWidget}]);

		dijit.layout._LayoutWidget.prototype.startup.apply(this, arguments);
		this._started = true;
	},

	_setupChild: function(/*Widget*/ page){
		// Summary: prepare the given child

		page.domNode.style.display = "none";

		// since we are setting the width/height of the child elements, they need
		// to be position:relative, or IE has problems (See bug #2033)
		page.domNode.style.position = "relative";

		return page;
	},

	addChild: function(/*Widget*/ child, /*Integer?*/ insertIndex){
		dijit._Container.prototype.addChild.apply(this, arguments);
		child = this._setupChild(child);

		var started = this._started;
		if(started){
			// in case the tab titles have overflowed from one line to two lines
			this.layout();
		}

		if(started){
			dojo.publish(this.id+"-addChild", [child]);
		}

		// if this is the first child, then select it
		if(!this.selectedChildWidget && started){
			this.selectChild(child);
		}
	},

	removeChild: function(/*Widget*/ page){

		dijit._Container.prototype.removeChild.apply(this, arguments);

		// If we are being destroyed than don't run the code below (to select another page), because we are deleting
		// every page one by one
		if(this._beingDestroyed){ return; }

		if(this._started){
			// this will notify any tablists to remove a button; do this first because it may affect sizing
			dojo.publish(this.id+"-removeChild", [page]);

			// in case the tab titles now take up one line instead of two lines
			this.layout();
		}

		if(this.selectedChildWidget === page){
			this.selectedChildWidget = undefined;
			if(this._started){
				var children = this.getChildren();
				if(children.length){
					this.selectChild(children[0]);
				}
			}
		}
	},

	selectChild: function(/*Widget*/ page){
		// summary
		//	Show the given widget (which must be one of my children)

		page = dijit.byId(page);

		if(this.selectedChildWidget != page){
			// Deselect old page and select new one
			this._transition(page, this.selectedChildWidget);
			this.selectedChildWidget = page;
			dojo.publish(this.id+"-selectChild", [page]);
		}
	},

	_transition: function(/*Widget*/newWidget, /*Widget*/oldWidget){
		if(oldWidget){
			this._hideChild(oldWidget);
		}
		this._showChild(newWidget);

		// Size the new widget, in case this is the first time it's being shown,
		// or I have been resized since the last time it was shown.
		// page must be visible for resizing to work
		if(this.doLayout && newWidget.resize){
			newWidget.resize(this._containerContentBox || this._contentBox);
		}
	},

	forward: function(){
		// Summary: advance to next page
		var children = this.getChildren();
		var index = dojo.indexOf(children, this.selectedChildWidget);
		this.selectChild(children[ (index + 1) % children.length ]);
	},

	back: function(){
		// Summary: go back to previous page
		var children = this.getChildren();
		var index = dojo.indexOf(children, this.selectedChildWidget);
		this.selectChild(children[ (index + children.length - 1) % children.length ]);
	},

	// TODO: move this logic into controller?
	_onKeyPress: function(e){
		// summary
		//	Keystroke handling for keystrokes on the tab panel itself (that were bubbled up to me)
		//	Ctrl-w: close tab
		if (e.ctrlKey){
			switch(e.keyCode){
				case dojo.keys.PAGE_DOWN:
				case dojo.keys.PAGE_UP:
				case dojo.keys.TAB:
					if ((e.keyCode == dojo.keys.PAGE_DOWN) ||
						(e.keyCode == dojo.keys.TAB && !e.shiftKey)){
						this.forward();
					}else{
						this.back();
					}
					dijit.focus(this.selectedChildWidget.domNode);
					dojo.stopEvent(e);
					return false;
					break;
				default:
					if((e.keyChar == "w") &&
						(this.selectedChildWidget.closable)){
							this.closeChild(this.selectedChildWidget);
							dojo.stopEvent(e);
					}
			}
		}
	},

	layout: function(){
		// Summary: called when any page is shown, to make it fit the container correctly
		if(this.doLayout && this.selectedChildWidget && this.selectedChildWidget.resize){
			this.selectedChildWidget.resize(this._contentBox);
		}
	},

	_showChild: function(/*Widget*/ page){
		var children = this.getChildren();
		page.isFirstChild = (page == children[0]);
		page.isLastChild = (page == children[children.length-1]);
		page.selected = true;

		page.domNode.style.display="";
		if(page._loadCheck){
			page._loadCheck(); // trigger load in ContentPane
		}
		if(page.onShow){
			page.onShow();
		}
	},

	_hideChild: function(/*Widget*/ page){
		page.selected=false;
		page.domNode.style.display="none";
		if(page.onHide){
			page.onHide();
		}
	},

	closeChild: function(/*Widget*/ page){
		// summary
		//	callback when user clicks the [X] to remove a page
		//	if onClose() returns true then remove and destroy the childd
		var remove = page.onClose(this, page);
		if(remove){
			this.removeChild(page);
			// makes sure we can clean up executeScripts in ContentPane onUnLoad
			page.destroy();
		}
	},

	destroy: function(){
		this._beingDestroyed = true;
		dijit.layout.StackContainer.superclass.destroy.apply(this, arguments);
	}
});


dojo.declare(
	"dijit.layout.StackController",
	[dijit._Widget, dijit._Templated, dijit._Container],
	{
		// summary
		//	Set of buttons to select a page in a page list.
		//	Monitors the specified StackContainer, and whenever a page is
		//	added, deleted, or selected, updates itself accordingly.

		templateString: "<span wairole='tablist' dojoAttachEvent='onkeypress' class='dijitStackController'></span>",

		// containerId: String
		//	the id of the page container that I point to
		containerId: "",

		// buttonWidget: String
		//	the name of the button widget to create to correspond to each page
		buttonWidget: "dijit.layout._StackButton",

		postCreate: function(){
			dijit.wai.setAttr(this.domNode, "waiRole", "role", "tablist");

			this.pane2button = {};		// mapping from panes to buttons
			this._subscriptions=[
				dojo.subscribe(this.containerId+"-startup", this, "onStartup"),
				dojo.subscribe(this.containerId+"-addChild", this, "onAddChild"),
				dojo.subscribe(this.containerId+"-removeChild", this, "onRemoveChild"),
				dojo.subscribe(this.containerId+"-selectChild", this, "onSelectChild")
			];
		},

		onStartup: function(/*Object*/ info){
			// summary: called after StackContainer has finished initializing
			dojo.forEach(info.children, this.onAddChild, this);
			this.onSelectChild(info.selected);
		},

		destroy: function(){
			dojo.forEach(this._subscriptions, dojo.unsubscribe);
			dijit.layout.StackController.superclass.destroy.apply(this, arguments);
		},

		onAddChild: function(/*Widget*/ page){
			// summary
			//   Called whenever a page is added to the container.
			//   Create button corresponding to the page.

			// add a node that will be promoted to the button widget
			var refNode = document.createElement("span");
			this.domNode.appendChild(refNode);
			// create an instance of the button widget
			var cls = dojo.getObject(this.buttonWidget);
			var button = new cls({label: page.title, closeButton: page.closable}, refNode);
			this.addChild(button);
			this.pane2button[page] = button;
			page.controlButton = button;	// this value might be overwritten if two tabs point to same container

			var _this = this;
			dojo.connect(button, "onClick", function(){ _this.onButtonClick(page); });
			dojo.connect(button, "onClickCloseButton", function(){ _this.onCloseButtonClick(page); });
			if(!this._currentChild){ // put the first child into the tab order
				button.focusNode.setAttribute("tabIndex","0");
				this._currentChild = page;
			}
		},

		onRemoveChild: function(/*Widget*/ page){
			// summary
			//   Called whenever a page is removed from the container.
			//   Remove the button corresponding to the page.
			if(this._currentChild === page){ this._currentChild = null; }
			var button = this.pane2button[page];
			if(button){
				// TODO? if current child { reassign }
				button.destroy();
			}
			this.pane2button[page] = null;
		},

		onSelectChild: function(/*Widget*/ page){
			// Summary
			//	Called when a page has been selected in the StackContainer, either by me or by another StackController

			if(!page){ return; }

			if(this._currentChild){
				var oldButton=this.pane2button[this._currentChild];
				oldButton.setChecked(false);
				oldButton.focusNode.setAttribute("tabIndex", "-1");
			}

			var newButton=this.pane2button[page];
			newButton.setChecked(true);
			this._currentChild = page;
			newButton.focusNode.setAttribute("tabIndex", "0");
		},

		onButtonClick: function(/*Widget*/ page){
			// summary
			//   Called whenever one of my child buttons is pressed in an attempt to select a page
			var container = dijit.byId(this.containerId);	// TODO: do this via topics?
			container.selectChild(page);
		},

		onCloseButtonClick: function(/*Widget*/ page){
			// summary
			//   Called whenever one of my child buttons [X] is pressed in an attempt to close a page
			var container = dijit.byId(this.containerId);
			container.closeChild(page);
			var b = this.pane2button[this._currentChild];
			if(b){
				dijit.focus(b.focusNode || b.domNode);
			}
		},
		
		// TODO: this is a bit redundant with forward, back api in StackContainer
		adjacent: function(/*Boolean*/ forward){
			// find currently focused button in children array
			var children = this.getChildren();
			var current = dojo.indexOf(children, this.pane2button[this._currentChild]);
			// pick next button to focus on
			var offset = forward ? 1 : children.length - 1;
			return children[ (current + offset) % children.length ];
		},

		onkeypress: function(/*Event*/ evt){
			// summary:
			//   Handle keystrokes on the page list, for advancing to next/previous button
			//   and closing the current page.

			if(this.disabled || evt.altKey || evt.shiftKey || evt.ctrlKey){ return; }
			var forward = true;
			switch(evt.keyCode){				
				case dojo.keys.LEFT_ARROW:
				case dojo.keys.UP_ARROW:
					forward=false;
					// fall through
				case dojo.keys.RIGHT_ARROW:
				case dojo.keys.DOWN_ARROW:
					this.adjacent(forward).onClick();
					dojo.stopEvent(evt);
					break;
				case dojo.keys.DELETE:
					if (this._currentChild.closable){
						this.onCloseButtonClick(this._currentChild);
						dojo.stopEvent(evt); // so we don't close a browser tab!
					}
				default:
					return;
			}
		}
	}
);

dojo.declare(
	"dijit.layout._StackButton",
	dijit.form.ToggleButton,
{
	// summary
	//	Internal widget used by StackContainer.
	//	The button-like or tab-like object you click to select or delete a page

	onClick: function(/*Event*/ evt) {
		// this is for TabContainer where the tabs are <span> rather than button,
		// so need to set focus explicitly (on some browsers)
		dijit.focus(this.focusNode);

		// ... now let StackController catch the event and tell me what to do
	},

	onClickCloseButton: function(/*Event*/ evt){
		// summary
		//	StackContainer connects to this function; if your widget contains a close button
		//	then clicking it should call this function.
		evt.stopPropagation();
	}
});

// These arguments can be specified for the children of a StackContainer.
// Since any widget can be specified as a StackContainer child, mix them
// into the base widget class.  (This is a hack, but it's effective.)
dojo.extend(dijit._Widget, {
	// title: String
	//		Title of this widget.  Used by TabContainer to the name the tab, etc.
	title: "",

	// selected: Boolean
	//		Is this child currently selected?
	selected: false,

	// closable: Boolean
	//		True if user can close (destroy) this child, such as (for example) clicking the X on the tab.
	closable: false,	// true if user can close this tab pane

	onClose: function(){
		// summary: Callback if someone tries to close the child, child will be closed if func returns true
		return true;
	}
});

}

if(!dojo._hasResource["dijit.layout.TabContainer"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.layout.TabContainer"] = true;
dojo.provide("dijit.layout.TabContainer");




dojo.declare(
	"dijit.layout.TabContainer",
	[dijit.layout.StackContainer, dijit._Templated],
{
	// summary
	//	A TabContainer is a container that has multiple panes, but shows only
	//	one pane at a time.  There are a set of tabs corresponding to each pane,
	//	where each tab has the title (aka title) of the pane, and optionally a close button.
	//
	//	Publishes topics <widgetId>-addChild, <widgetId>-removeChild, and <widgetId>-selectChild
	//	(where <widgetId> is the id of the TabContainer itself.

	// tabPosition: String
	//   Defines where tabs go relative to tab content.
	//   "top", "bottom", "left-h", "right-h"
	tabPosition: "top",

	templateString: null,	// override setting in StackContainer
	templateString:"<div class=\"dijitTabContainer\">\n\t<div dojoAttachPoint=\"tablistNode\"></div>\n\t<div class=\"dijitTabPaneWrapper\" dojoAttachPoint=\"containerNode\" dojoAttachEvent=\"onkeypress:_onKeyPress\" waiRole=\"tabpanel\"></div>\n</div>\n",

	postCreate: function(){	
		dijit.layout.TabContainer.superclass.postCreate.apply(this, arguments);
		// create the tab list that will have a tab (a.k.a. tab button) for each tab panel
		this.tablist = new dijit.layout.TabController(
			{
				id: this.id + "_tablist",
				tabPosition: this.tabPosition,
				doLayout: this.doLayout,
				containerId: this.id
			}, this.tablistNode);		
	},

	_setupChild: function(tab){
		dojo.addClass(tab.domNode, "dijitTabPane");
		dijit.layout.TabContainer.superclass._setupChild.apply(this, arguments);
		return tab;
	},

	startup: function(){
		// wire up the tablist and its tabs
		this.tablist.startup();
		dijit.layout.TabContainer.superclass.startup.apply(this, arguments);
		
		if(dojo.isSafari){
			// sometimes safari 3.0.3 miscalculates the height of the tab labels, see #4058
			setTimeout(dojo.hitch(this, "layout"), 0);
		}
	},

	layout: function(){
		// Summary: Configure the content pane to take up all the space except for where the tabs are
		if(!this.doLayout){ return; }

		// position and size the titles and the container node
		var titleAlign=this.tabPosition.replace(/-h/,"");
		var children = [
			{domNode: this.tablist.domNode, layoutAlign: titleAlign},
			{domNode: this.containerNode, layoutAlign: "client"}
		];
		dijit.layout.layoutChildren(this.domNode, this._contentBox, children);

		// Compute size to make each of my children.
		// children[1] is the margin-box size of this.containerNode, set by layoutChildren() call above
		this._containerContentBox = dijit.layout.marginBox2contentBox(this.containerNode, children[1]);

		if(this.selectedChildWidget){
			this._showChild(this.selectedChildWidget);
			if(this.doLayout && this.selectedChildWidget.resize){
				this.selectedChildWidget.resize(this._containerContentBox);
			}
		}
	},

	destroy: function(){
		this.tablist.destroy();
		dijit.layout.TabContainer.superclass.destroy.apply(this, arguments);
	}
});

//TODO: make private?
dojo.declare(
	"dijit.layout.TabController",
	dijit.layout.StackController,
	{
		// summary
		// 	Set of tabs (the things with titles and a close button, that you click to show a tab panel).
		//	Lets the user select the currently shown pane in a TabContainer or StackContainer.
		//	TabController also monitors the TabContainer, and whenever a pane is
		//	added or deleted updates itself accordingly.

		templateString: "<div wairole='tablist' dojoAttachEvent='onkeypress:onkeypress'></div>",

		// tabPosition: String
		//   Defines where tabs go relative to the content.
		//   "top", "bottom", "left-h", "right-h"
		tabPosition: "top",

		doLayout: true,

		// buttonWidget: String
		//	the name of the tab widget to create to correspond to each page
		buttonWidget: "dijit.layout._TabButton",

		postMixInProperties: function(){
			this["class"] = "dijitTabLabels-" + this.tabPosition + (this.doLayout ? "" : " dijitTabNoLayout");
			dijit.layout.TabController.superclass.postMixInProperties.apply(this, arguments);
		}
	}
);

dojo.declare(
	"dijit.layout._TabButton", dijit.layout._StackButton,
{
	// summary
	//	A tab (the thing you click to select a pane).
	//	Contains the title of the pane, and optionally a close-button to destroy the pane.
	//	This is an internal widget and should not be instantiated directly.

	baseClass: "dijitTab",

	templateString: "<div baseClass='dijitTab' dojoAttachEvent='onclick:onClick,onmouseover:_onMouse,onmouseout:_onMouse'>"
						+"<div class='dijitTabInnerDiv' dojoAttachPoint='innerDiv'>"
							+"<span dojoAttachPoint='containerNode,focusNode' tabIndex='-1' waiRole='tab'>${!label}</span>"
							+"<span dojoAttachPoint='closeButtonNode' class='closeImage'"
							+" dojoAttachEvent='onmouseover:_onMouse, onmouseout:_onMouse, onclick:onClickCloseButton'"
							+" baseClass='dijitTabCloseButton'>"
								+"<span dojoAttachPoint='closeText' class='closeText'>x</span>"
							+"</span>"
						+"</div>"
					+"</div>",

	postCreate: function(){
		if(this.closeButton){
			dojo.addClass(this.innerDiv, "dijitClosable");
		} else {
			this.closeButtonNode.style.display="none";
		}
		dijit.layout._TabButton.superclass.postCreate.apply(this, arguments);
		dojo.setSelectable(this.containerNode, false);
	}
});

}

if(!dojo._hasResource["dojo.data.util.filter"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.data.util.filter"] = true;
dojo.provide("dojo.data.util.filter");

dojo.data.util.filter.patternToRegExp = function(/*String*/pattern, /*boolean?*/ ignoreCase){
	//	summary:  
	//		Helper function to convert a simple pattern to a regular expression for matching.
	//	description:
	//		Returns a regular expression object that conforms to the defined conversion rules.
	//		For example:  
	//			ca*   -> /^ca.*$/
	//			*ca*  -> /^.*ca.*$/
	//			*c\*a*  -> /^.*c\*a.*$/
	//			*c\*a?*  -> /^.*c\*a..*$/
	//			and so on.
	//
	//	pattern: string
	//		A simple matching pattern to convert that follows basic rules:
	//			* Means match anything, so ca* means match anything starting with ca
	//			? Means match single character.  So, b?b will match to bob and bab, and so on.
	//      	\ is an escape character.  So for example, \* means do not treat * as a match, but literal character *.
	//				To use a \ as a character in the string, it must be escaped.  So in the pattern it should be 
	//				represented by \\ to be treated as an ordinary \ character instead of an escape.
	//
	//	ignoreCase:
	//		An optional flag to indicate if the pattern matching should be treated as case-sensitive or not when comparing
	//		By default, it is assumed case sensitive.

	var rxp = "^";
	var c = null;
	for(var i = 0; i < pattern.length; i++){
		c = pattern.charAt(i);
		switch (c) {
			case '\\':
				rxp += c;
				i++;
				rxp += pattern.charAt(i);
				break;
			case '*':
				rxp += ".*"; break;
			case '?':
				rxp += "."; break;
			case '$':
			case '^':
			case '/':
			case '+':
			case '.':
			case '|':
			case '(':
			case ')':
			case '{':
			case '}':
			case '[':
			case ']':
				rxp += "\\"; //fallthrough
			default:
				rxp += c;
		}
	}
	rxp += "$";
	if(ignoreCase){
		return new RegExp(rxp,"i"); //RegExp
	}else{
		return new RegExp(rxp); //RegExp
	}
	
};

}

if(!dojo._hasResource["dojo.data.util.sorter"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.data.util.sorter"] = true;
dojo.provide("dojo.data.util.sorter");

dojo.data.util.sorter.basicComparator = function(	/*anything*/ a, 
													/*anything*/ b){
	//	summary:  
	//		Basic comparision function that compares if an item is greater or less than another item
	//	description:  
	//		returns 1 if a > b, -1 if a < b, 0 if equal.
	//		undefined values are treated as larger values so that they're pushed to the end of the list.

	var ret = 0;
	if(a > b || typeof a === "undefined"){
		ret = 1;
	}else if(a < b || typeof b === "undefined"){
		ret = -1;
	}
	return ret; //int, {-1,0,1}
};

dojo.data.util.sorter.createSortFunction = function(	/* attributes array */sortSpec,
														/*dojo.data.core.Read*/ store){
	//	summary:  
	//		Helper function to generate the sorting function based off the list of sort attributes.
	//	description:  
	//		The sort function creation will look for a property on the store called 'comparatorMap'.  If it exists
	//		it will look in the mapping for comparisons function for the attributes.  If one is found, it will
	//		use it instead of the basic comparator, which is typically used for strings, ints, booleans, and dates.
	//		Returns the sorting function for this particular list of attributes and sorting directions.
	//
	//	sortSpec: array
	//		A JS object that array that defines out what attribute names to sort on and whether it should be descenting or asending.
	//		The objects should be formatted as follows:
	//		{
	//			attribute: "attributeName-string" || attribute,
	//			descending: true|false;   // Default is false.
	//		}
	//	store: object
	//		The datastore object to look up item values from.
	//
	var sortFunctions=[];   

	function createSortFunction(attr, dir){
		return function(itemA, itemB){
			var a = store.getValue(itemA, attr);
			var b = store.getValue(itemB, attr);
			//See if we have a override for an attribute comparison.
			var comparator = null;
			if(store.comparatorMap){
				if(typeof attr !== "string"){
					 attr = store.getIdentity(attr);
				}
				comparator = store.comparatorMap[attr]||dojo.data.util.sorter.basicComparator;
			}
			comparator = comparator||dojo.data.util.sorter.basicComparator; 
			return dir * comparator(a,b); //int
		};
	}

	for(var i = 0; i < sortSpec.length; i++){
		sortAttribute = sortSpec[i];
		if(sortAttribute.attribute){
			var direction = (sortAttribute.descending) ? -1 : 1;
			sortFunctions.push(createSortFunction(sortAttribute.attribute, direction));
		}
	}

	return function(rowA, rowB){
		var i=0;
		while(i < sortFunctions.length){
			var ret = sortFunctions[i++](rowA, rowB);
			if(ret !== 0){
				return ret;//int
			}
		}
		return 0; //int  
	};  //  Function
};

}

if(!dojo._hasResource["dojo.data.util.simpleFetch"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.data.util.simpleFetch"] = true;
dojo.provide("dojo.data.util.simpleFetch");


dojo.data.util.simpleFetch.fetch = function(/* Object? */ request){
	//	summary:
	//		The simpleFetch mixin is designed to serve as a set of function(s) that can
	//		be mixed into other datastore implementations to accelerate their development.  
	//		The simpleFetch mixin should work well for any datastore that can respond to a _fetchItems() 
	//		call by returning an array of all the found items that matched the query.  The simpleFetch mixin
	//		is not designed to work for datastores that respond to a fetch() call by incrementally
	//		loading items, or sequentially loading partial batches of the result
	//		set.  For datastores that mixin simpleFetch, simpleFetch 
	//		implements a fetch method that automatically handles eight of the fetch()
	//		arguments -- onBegin, onItem, onComplete, onError, start, count, sort and scope
	//		The class mixing in simpleFetch should not implement fetch(),
	//		but should instead implement a _fetchItems() method.  The _fetchItems() 
	//		method takes three arguments, the keywordArgs object that was passed 
	//		to fetch(), a callback function to be called when the result array is
	//		available, and an error callback to be called if something goes wrong.
	//		The _fetchItems() method should ignore any keywordArgs parameters for
	//		start, count, onBegin, onItem, onComplete, onError, sort, and scope.  
	//		The _fetchItems() method needs to correctly handle any other keywordArgs
	//		parameters, including the query parameter and any optional parameters 
	//		(such as includeChildren).  The _fetchItems() method should create an array of 
	//		result items and pass it to the fetchHandler along with the original request object 
	//		-- or, the _fetchItems() method may, if it wants to, create an new request object 
	//		with other specifics about the request that are specific to the datastore and pass 
	//		that as the request object to the handler.
	//
	//		For more information on this specific function, see dojo.data.api.Read.fetch()
	request = request || {};
	if(!request.store){
		request.store = this;
	}
	var self = this;

	var _errorHandler = function(errorData, requestObject){
		if(requestObject.onError){
			var scope = requestObject.scope || dojo.global;
			requestObject.onError.call(scope, errorData, requestObject);
		}
	};

	var _fetchHandler = function(items, requestObject){
		var oldAbortFunction = requestObject.abort || null;
		var aborted = false;

		var startIndex = requestObject.start?requestObject.start:0;
		var endIndex   = requestObject.count?(startIndex + requestObject.count):items.length;

		requestObject.abort = function(){
			aborted = true;
			if(oldAbortFunction){
				oldAbortFunction.call(requestObject);
			}
		};

		var scope = requestObject.scope || dojo.global;
		if(!requestObject.store){
			requestObject.store = self;
		}
		if(requestObject.onBegin){
			requestObject.onBegin.call(scope, items.length, requestObject);
		}
		if(requestObject.sort){
			items.sort(dojo.data.util.sorter.createSortFunction(requestObject.sort, self));
		}
		if(requestObject.onItem){
			for(var i = startIndex; (i < items.length) && (i < endIndex); ++i){
				var item = items[i];
				if(!aborted){
					requestObject.onItem.call(scope, item, requestObject);
				}
			}
		}
		if(requestObject.onComplete && !aborted){
			var subset = null;
			if (!requestObject.onItem) {
				subset = items.slice(startIndex, endIndex);
			}
			requestObject.onComplete.call(scope, subset, requestObject);   
		}
	};
	this._fetchItems(request, _fetchHandler, _errorHandler);
	return request;	// Object
};

}

if(!dojo._hasResource["dojo.data.ItemFileReadStore"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.data.ItemFileReadStore"] = true;
dojo.provide("dojo.data.ItemFileReadStore");





dojo.declare("dojo.data.ItemFileReadStore", null,{
	//	summary:
	//		The ItemFileReadStore implements the dojo.data.api.Read API and reads
	//		data from JSON files that have contents in this format --
	//		{ items: [
	//			{ name:'Kermit', color:'green', age:12, friends:['Gonzo', {_reference:{name:'Fozzie Bear'}}]},
	//			{ name:'Fozzie Bear', wears:['hat', 'tie']},
	//			{ name:'Miss Piggy', pets:'Foo-Foo'}
	//		]}
	//		Note that it can also contain an 'identifer' property that specified which attribute on the items 
	//		in the array of items that acts as the unique identifier for that item.
	//
	constructor: function(/* Object */ keywordParameters){
		//	summary: constructor
		//	keywordParameters: {url: String}
		//	keywordParameters: {data: jsonObject}
		//	keywordParameters: {typeMap: object)
		//		The structure of the typeMap object is as follows:
		//		{
		//			type0: function || object,
		//			type1: function || object,
		//			...
		//			typeN: function || object
		//		}
		//		Where if it is a function, it is assumed to be an object constructor that takes the 
		//		value of _value as the initialization parameters.  If it is an object, then it is assumed
		//		to be an object of general form:
		//		{
		//			type: function, //constructor.
		//			deserialize:	function(value) //The function that parses the value and constructs the object defined by type appropriately.
		//		}
	
		this._arrayOfAllItems = [];
		this._arrayOfTopLevelItems = [];
		this._loadFinished = false;
		this._jsonFileUrl = keywordParameters.url;
		this._jsonData = keywordParameters.data;
		this._datatypeMap = keywordParameters.typeMap || {};
		if(!this._datatypeMap['Date']){
			//If no default mapping for dates, then set this as default.
			//We use the dojo.date.stamp here because the ISO format is the 'dojo way'
			//of generically representing dates.
			this._datatypeMap['Date'] = {
											type: Date,
											deserialize: function(value){
												return dojo.date.stamp.fromISOString(value);
											}
										};
		}
		this._features = {'dojo.data.api.Read':true, 'dojo.data.api.Identity':true};
		this._itemsByIdentity = null;
		this._storeRefPropName = "_S";  // Default name for the store reference to attach to every item.
		this._itemNumPropName = "_0"; // Default Item Id for isItem to attach to every item.
		this._rootItemPropName = "_RI"; // Default Item Id for isItem to attach to every item.
		this._loadInProgress = false;	//Got to track the initial load to prevent duelling loads of the dataset.
		this._queuedFetches = [];
	},
	
	url: "",	// use "" rather than undefined for the benefit of the parser (#3539)

	_assertIsItem: function(/* item */ item){
		//	summary:
		//		This function tests whether the item passed in is indeed an item in the store.
		//	item: 
		//		The item to test for being contained by the store.
		if(!this.isItem(item)){ 
			throw new Error("dojo.data.ItemFileReadStore: a function was passed an item argument that was not an item");
		}
	},

	_assertIsAttribute: function(/* attribute-name-string */ attribute){
		//	summary:
		//		This function tests whether the item passed in is indeed a valid 'attribute' like type for the store.
		//	attribute: 
		//		The attribute to test for being contained by the store.
		if(typeof attribute !== "string"){ 
			throw new Error("dojo.data.ItemFileReadStore: a function was passed an attribute argument that was not an attribute name string");
		}
	},

	getValue: function(	/* item */ item, 
						/* attribute-name-string */ attribute, 
						/* value? */ defaultValue){
		//	summary: 
		//		See dojo.data.api.Read.getValue()
		var values = this.getValues(item, attribute);
		return (values.length > 0)?values[0]:defaultValue; // Anything
	},

	getValues: function(/* item */ item, 
						/* attribute-name-string */ attribute){
		//	summary: 
		//		See dojo.data.api.Read.getValues()

		this._assertIsItem(item);
		this._assertIsAttribute(attribute);
		return item[attribute] || []; // Array
	},

	getAttributes: function(/* item */ item){
		//	summary: 
		//		See dojo.data.api.Read.getAttributes()
		this._assertIsItem(item);
		var attributes = [];
		for(var key in item){
			// Save off only the real item attributes, not the special id marks for O(1) isItem.
			if((key !== this._storeRefPropName) && (key !== this._itemNumPropName) && (key !== this._rootItemPropName)){
				attributes.push(key);
			}
		}
		return attributes; // Array
	},

	hasAttribute: function(	/* item */ item,
							/* attribute-name-string */ attribute) {
		//	summary: 
		//		See dojo.data.api.Read.hasAttribute()
		return this.getValues(item, attribute).length > 0;
	},

	containsValue: function(/* item */ item, 
							/* attribute-name-string */ attribute, 
							/* anything */ value){
		//	summary: 
		//		See dojo.data.api.Read.containsValue()
		var regexp = undefined;
		if(typeof value === "string"){
			regexp = dojo.data.util.filter.patternToRegExp(value, false);
		}
		return this._containsValue(item, attribute, value, regexp); //boolean.
	},

	_containsValue: function(	/* item */ item, 
								/* attribute-name-string */ attribute, 
								/* anything */ value,
								/* RegExp?*/ regexp){
		//	summary: 
		//		Internal function for looking at the values contained by the item.
		//	description: 
		//		Internal function for looking at the values contained by the item.  This 
		//		function allows for denoting if the comparison should be case sensitive for
		//		strings or not (for handling filtering cases where string case should not matter)
		//	
		//	item:
		//		The data item to examine for attribute values.
		//	attribute:
		//		The attribute to inspect.
		//	value:	
		//		The value to match.
		//	regexp:
		//		Optional regular expression generated off value if value was of string type to handle wildcarding.
		//		If present and attribute values are string, then it can be used for comparison instead of 'value'
		var values = this.getValues(item, attribute);
		for(var i = 0; i < values.length; ++i){
			var possibleValue = values[i];
			if(typeof possibleValue === "string" && regexp){
				return (possibleValue.match(regexp) !== null);
			}else{
				//Non-string matching.
				if(value === possibleValue){
					return true; // Boolean
				}
			}
		}
		return false; // Boolean
	},

	isItem: function(/* anything */ something){
		//	summary: 
		//		See dojo.data.api.Read.isItem()
		if(something && something[this._storeRefPropName] === this){
			if(this._arrayOfAllItems[something[this._itemNumPropName]] === something){
				return true;
			}
		}
		return false; // Boolean
	},

	isItemLoaded: function(/* anything */ something){
		//	summary: 
		//		See dojo.data.api.Read.isItemLoaded()
		return this.isItem(something); //boolean
	},

	loadItem: function(/* object */ keywordArgs){
		//	summary: 
		//		See dojo.data.api.Read.loadItem()
		this._assertIsItem(keywordArgs.item);
	},

	getFeatures: function(){
		//	summary: 
		//		See dojo.data.api.Read.getFeatures()
		return this._features; //Object
	},

	getLabel: function(/* item */ item){
		//	summary: 
		//		See dojo.data.api.Read.getLabel()
		if(this._labelAttr && this.isItem(item)){
			return this.getValue(item,this._labelAttr); //String
		}
		return undefined; //undefined
	},

	getLabelAttributes: function(/* item */ item){
		//	summary: 
		//		See dojo.data.api.Read.getLabelAttributes()
		if(this._labelAttr){
			return [this._labelAttr]; //array
		}
		return null; //null
	},

	_fetchItems: function(	/* Object */ keywordArgs, 
							/* Function */ findCallback, 
							/* Function */ errorCallback){
		//	summary: 
		//		See dojo.data.util.simpleFetch.fetch()
		var self = this;
		var filter = function(requestArgs, arrayOfItems){
			var items = [];
			if(requestArgs.query){
				var ignoreCase = requestArgs.queryOptions ? requestArgs.queryOptions.ignoreCase : false; 

				//See if there are any string values that can be regexp parsed first to avoid multiple regexp gens on the
				//same value for each item examined.  Much more efficient.
				var regexpList = {};
				for(var key in requestArgs.query){
					var value = requestArgs.query[key];
					if(typeof value === "string"){
						regexpList[key] = dojo.data.util.filter.patternToRegExp(value, ignoreCase);
					}
				}

				for(var i = 0; i < arrayOfItems.length; ++i){
					var match = true;
					var candidateItem = arrayOfItems[i];
					if(candidateItem === null){
						match = false;
					}else{
						for(var key in requestArgs.query) {
							var value = requestArgs.query[key];
							if (!self._containsValue(candidateItem, key, value, regexpList[key])){
								match = false;
							}
						}
					}
					if(match){
						items.push(candidateItem);
					}
				}
				findCallback(items, requestArgs);
			}else{
				// We want a copy to pass back in case the parent wishes to sort the array. 
				// We shouldn't allow resort of the internal list, so that multiple callers 
				// can get lists and sort without affecting each other.  We also need to
				// filter out any null values that have been left as a result of deleteItem()
				// calls in ItemFileWriteStore.
				for(var i = 0; i < arrayOfItems.length; ++i){
					var item = arrayOfItems[i];
					if(item !== null){
						items.push(item);
					}
				}
				findCallback(items, requestArgs);
			}
		};

		if(this._loadFinished){
			filter(keywordArgs, this._getItemsArray(keywordArgs.queryOptions));
		}else{

			if(this._jsonFileUrl){
				//If fetches come in before the loading has finished, but while
				//a load is in progress, we have to defer the fetching to be 
				//invoked in the callback.
				if(this._loadInProgress){
					this._queuedFetches.push({args: keywordArgs, filter: filter});
				}else{
					this._loadInProgress = true;
					var getArgs = {
							url: self._jsonFileUrl, 
							handleAs: "json-comment-optional"
						};
					var getHandler = dojo.xhrGet(getArgs);
					getHandler.addCallback(function(data){
						try{
							self._getItemsFromLoadedData(data);
							self._loadFinished = true;
							self._loadInProgress = false;
							
							filter(keywordArgs, self._getItemsArray(keywordArgs.queryOptions));
							self._handleQueuedFetches();
						}catch(e){
							self._loadFinished = true;
							self._loadInProgress = false;
							errorCallback(e, keywordArgs);
						}
					});
					getHandler.addErrback(function(error){
						self._loadInProgress = false;
						errorCallback(error, keywordArgs);
					});
				}
			}else if(this._jsonData){
				try{
					this._loadFinished = true;
					this._getItemsFromLoadedData(this._jsonData);
					this._jsonData = null;
					filter(keywordArgs, this._getItemsArray(keywordArgs.queryOptions));
				}catch(e){
					errorCallback(e, keywordArgs);
				}
			}else{
				errorCallback(new Error("dojo.data.ItemFileReadStore: No JSON source data was provided as either URL or a nested Javascript object."), keywordArgs);
			}
		}
	},

	_handleQueuedFetches: function(){
		//	summary: 
		//		Internal function to execute delayed request in the store.
		//Execute any deferred fetches now.
		if (this._queuedFetches.length > 0) {
			for(var i = 0; i < this._queuedFetches.length; i++){
				var fData = this._queuedFetches[i];
				var delayedQuery = fData.args;
				var delayedFilter = fData.filter;
				if(delayedFilter){
					delayedFilter(delayedQuery, this._getItemsArray(delayedQuery.queryOptions)); 
				}else{
					this.fetchItemByIdentity(delayedQuery);
				}
			}
			this._queuedFetches = [];
		}
	},

	_getItemsArray: function(/*object?*/queryOptions){
		//	summary: 
		//		Internal function to determine which list of items to search over.
		//	queryOptions: The query options parameter, if any.
		if(queryOptions && queryOptions.deep) {
			return this._arrayOfAllItems; 
		}
		return this._arrayOfTopLevelItems;
	},

	close: function(/*dojo.data.api.Request || keywordArgs || null */ request){
		 //	summary: 
		 //		See dojo.data.api.Read.close()
	},

	_getItemsFromLoadedData: function(/* Object */ dataObject){
		//	summary:
		//		Function to parse the loaded data into item format and build the internal items array.
		//	description:
		//		Function to parse the loaded data into item format and build the internal items array.
		//
		//	dataObject:
		//		The JS data object containing the raw data to convery into item format.
		//
		// 	returns: array
		//		Array of items in store item format.
		
		// First, we define a couple little utility functions...
		
		function valueIsAnItem(/* anything */ aValue){
			// summary:
			//		Given any sort of value that could be in the raw json data,
			//		return true if we should interpret the value as being an
			//		item itself, rather than a literal value or a reference.
			// examples:
			// 		false == valueIsAnItem("Kermit");
			// 		false == valueIsAnItem(42);
			// 		false == valueIsAnItem(new Date());
			// 		false == valueIsAnItem({_type:'Date', _value:'May 14, 1802'});
			// 		false == valueIsAnItem({_reference:'Kermit'});
			// 		true == valueIsAnItem({name:'Kermit', color:'green'});
			// 		true == valueIsAnItem({iggy:'pop'});
			// 		true == valueIsAnItem({foo:42});
			var isItem = (
				(aValue != null) &&
				(typeof aValue == "object") &&
				(!dojo.isArray(aValue)) &&
				(!dojo.isFunction(aValue)) &&
				(aValue.constructor == Object) &&
				(typeof aValue._reference == "undefined") && 
				(typeof aValue._type == "undefined") && 
				(typeof aValue._value == "undefined")
			);
			return isItem;
		}
		
		var self = this;
		function addItemAndSubItemsToArrayOfAllItems(/* Item */ anItem){
			self._arrayOfAllItems.push(anItem);
			for(var attribute in anItem){
				var valueForAttribute = anItem[attribute];
				if(valueForAttribute){
					if(dojo.isArray(valueForAttribute)){
						var valueArray = valueForAttribute;
						for(var k = 0; k < valueArray.length; ++k){
							var singleValue = valueArray[k];
							if(valueIsAnItem(singleValue)){
								addItemAndSubItemsToArrayOfAllItems(singleValue);
							}
						}
					}else{
						if(valueIsAnItem(valueForAttribute)){
							addItemAndSubItemsToArrayOfAllItems(valueForAttribute);
						}
					}
				}
			}
		}

		this._labelAttr = dataObject.label;

		// We need to do some transformations to convert the data structure
		// that we read from the file into a format that will be convenient
		// to work with in memory.

		// Step 1: Walk through the object hierarchy and build a list of all items
		var i;
		var item;
		this._arrayOfAllItems = [];
		this._arrayOfTopLevelItems = dataObject.items;

		for(i = 0; i < this._arrayOfTopLevelItems.length; ++i){
			item = this._arrayOfTopLevelItems[i];
			addItemAndSubItemsToArrayOfAllItems(item);
			item[this._rootItemPropName]=true;
		}

		// Step 2: Walk through all the attribute values of all the items, 
		// and replace single values with arrays.  For example, we change this:
		//		{ name:'Miss Piggy', pets:'Foo-Foo'}
		// into this:
		//		{ name:['Miss Piggy'], pets:['Foo-Foo']}
		// 
		// We also store the attribute names so we can validate our store  
		// reference and item id special properties for the O(1) isItem
		var allAttributeNames = {};
		var key;

		for(i = 0; i < this._arrayOfAllItems.length; ++i){
			item = this._arrayOfAllItems[i];
			for(key in item){
				if (key !== this._rootItemPropName)
				{
					var value = item[key];
					if(value !== null){
						if(!dojo.isArray(value)){
							item[key] = [value];
						}
					}else{
						item[key] = [null];
					}
				}
				allAttributeNames[key]=key;
			}
		}

		// Step 3: Build unique property names to use for the _storeRefPropName and _itemNumPropName
		// This should go really fast, it will generally never even run the loop.
		while(allAttributeNames[this._storeRefPropName]){
			this._storeRefPropName += "_";
		}
		while(allAttributeNames[this._itemNumPropName]){
			this._itemNumPropName += "_";
		}

		// Step 4: Some data files specify an optional 'identifier', which is 
		// the name of an attribute that holds the identity of each item. 
		// If this data file specified an identifier attribute, then build a 
		// hash table of items keyed by the identity of the items.
		var arrayOfValues;

		var identifier = dataObject.identifier;
		this._itemsByIdentity = {};
		if(identifier){
			this._features['dojo.data.api.Identity'] = identifier;
			for(i = 0; i < this._arrayOfAllItems.length; ++i){
				item = this._arrayOfAllItems[i];
				arrayOfValues = item[identifier];
				var identity = arrayOfValues[0];
				if(!this._itemsByIdentity[identity]){
					this._itemsByIdentity[identity] = item;
				}else{
					if(this._jsonFileUrl){
						throw new Error("dojo.data.ItemFileReadStore:  The json data as specified by: [" + this._jsonFileUrl + "] is malformed.  Items within the list have identifier: [" + identifier + "].  Value collided: [" + identity + "]");
					}else if(this._jsonData){
						throw new Error("dojo.data.ItemFileReadStore:  The json data provided by the creation arguments is malformed.  Items within the list have identifier: [" + identifier + "].  Value collided: [" + identity + "]");
					}
				}
			}
		}else{
			this._features['dojo.data.api.Identity'] = Number;
		}

		// Step 5: Walk through all the items, and set each item's properties 
		// for _storeRefPropName and _itemNumPropName, so that store.isItem() will return true.
		for(i = 0; i < this._arrayOfAllItems.length; ++i){
			item = this._arrayOfAllItems[i];
			item[this._storeRefPropName] = this;
			item[this._itemNumPropName] = i;
		}

		// Step 6: We walk through all the attribute values of all the items,
		// looking for type/value literals and item-references.
		//
		// We replace item-references with pointers to items.  For example, we change:
		//		{ name:['Kermit'], friends:[{_reference:{name:'Miss Piggy'}}] }
		// into this:
		//		{ name:['Kermit'], friends:[miss_piggy] } 
		// (where miss_piggy is the object representing the 'Miss Piggy' item).
		//
		// We replace type/value pairs with typed-literals.  For example, we change:
		//		{ name:['Nelson Mandela'], born:[{_type:'Date', _value:'July 18, 1918'}] }
		// into this:
		//		{ name:['Kermit'], born:(new Date('July 18, 1918')) } 
		//
		// We also generate the associate map for all items for the O(1) isItem function.
		for(i = 0; i < this._arrayOfAllItems.length; ++i){
			item = this._arrayOfAllItems[i]; // example: { name:['Kermit'], friends:[{_reference:{name:'Miss Piggy'}}] }
			for(key in item){
				arrayOfValues = item[key]; // example: [{_reference:{name:'Miss Piggy'}}]
				for(var j = 0; j < arrayOfValues.length; ++j) {
					value = arrayOfValues[j]; // example: {_reference:{name:'Miss Piggy'}}
					if(value !== null && typeof value == "object"){
						if(value._type && value._value){
							var type = value._type; // examples: 'Date', 'Color', or 'ComplexNumber'
							var mappingObj = this._datatypeMap[type]; // examples: Date, dojo.Color, foo.math.ComplexNumber, {type: dojo.Color, deserialize(value){ return new dojo.Color(value)}}
							if(!mappingObj){ 
								throw new Error("dojo.data.ItemFileReadStore: in the typeMap constructor arg, no object class was specified for the datatype '" + type + "'");
							}else if(dojo.isFunction(mappingObj)){
								arrayOfValues[j] = new mappingObj(value._value);
							}else if(dojo.isFunction(mappingObj.deserialize)){
								arrayOfValues[j] = mappingObj.deserialize(value._value);
							}else{
								throw new Error("dojo.data.ItemFileReadStore: Value provided in typeMap was neither a constructor, nor a an object with a deserialize function");
							}
						}
						if(value._reference){
							var referenceDescription = value._reference; // example: {name:'Miss Piggy'}
							if(dojo.isString(referenceDescription)){
								// example: 'Miss Piggy'
								// from an item like: { name:['Kermit'], friends:[{_reference:'Miss Piggy'}]}
								arrayOfValues[j] = this._itemsByIdentity[referenceDescription];
							}else{
								// example: {name:'Miss Piggy'}
								// from an item like: { name:['Kermit'], friends:[{_reference:{name:'Miss Piggy'}}] }
								for(var k = 0; k < this._arrayOfAllItems.length; ++k){
									var candidateItem = this._arrayOfAllItems[k];
									var found = true;
									for(var refKey in referenceDescription){
										if(candidateItem[refKey] != referenceDescription[refKey]){ 
											found = false; 
										}
									}
									if(found){ 
										arrayOfValues[j] = candidateItem; 
									}
								}
							}
						}
					}
				}
			}
		}
	},

	getIdentity: function(/* item */ item){
		//	summary: 
		//		See dojo.data.api.Identity.getIdentity()
		var identifier = this._features['dojo.data.api.Identity'];
		if(identifier === Number){
			return item[this._itemNumPropName]; // Number
		}else{
			var arrayOfValues = item[identifier];
			if(arrayOfValues){
				return arrayOfValues[0]; // Object || String
			}
		}
		return null; // null
	},

	fetchItemByIdentity: function(/* Object */ keywordArgs){
		//	summary: 
		//		See dojo.data.api.Identity.fetchItemByIdentity()

		// Hasn't loaded yet, we have to trigger the load.
		if(!this._loadFinished){
			var self = this;
			if(this._jsonFileUrl){

				if(this._loadInProgress){
					this._queuedFetches.push({args: keywordArgs});
				}else{
					var getArgs = {
							url: self._jsonFileUrl, 
							handleAs: "json-comment-optional"
					};
					var getHandler = dojo.xhrGet(getArgs);
					getHandler.addCallback(function(data){
						var scope =  keywordArgs.scope?keywordArgs.scope:dojo.global;
						try{
							self._getItemsFromLoadedData(data);
							self._loadFinished = true;
							self._loadInProgress = false;
							var item = self._getItemByIdentity(keywordArgs.identity);
							if(keywordArgs.onItem){
								keywordArgs.onItem.call(scope, item);
							}
							self._handleQueuedFetches();
						}catch(error){
							self._loadInProgress = false;
							if(keywordArgs.onError){
								keywordArgs.onError.call(scope, error);
							}
						}
					});
					getHandler.addErrback(function(error){
						self._loadInProgress = false;
						if(keywordArgs.onError){
							var scope =  keywordArgs.scope?keywordArgs.scope:dojo.global;
							keywordArgs.onError.call(scope, error);
						}
					});
				}

			}else if(this._jsonData){
				// Passed in data, no need to xhr.
				self._getItemsFromLoadedData(self._jsonData);
				self._jsonData = null;
				self._loadFinished = true;
				var item = self._getItemByIdentity(keywordArgs.identity);
				if(keywordArgs.onItem){
					var scope =  keywordArgs.scope?keywordArgs.scope:dojo.global;
					keywordArgs.onItem.call(scope, item);
				}
			} 
		}else{
			// Already loaded.  We can just look it up and call back.
			var item = this._getItemByIdentity(keywordArgs.identity);
			if(keywordArgs.onItem){
				var scope =  keywordArgs.scope?keywordArgs.scope:dojo.global;
				keywordArgs.onItem.call(scope, item);
			}
		}
	},

	_getItemByIdentity: function(/* Object */ identity){
		//	summary:
		//		Internal function to look an item up by its identity map.
		var item = null;
		if(this._itemsByIdentity){
			item = this._itemsByIdentity[identity];
			if(item === undefined){
				item = null;
			}
		}else{
			this._arrayOfAllItems[identity];
		}
		return item; // Object
	},

	getIdentityAttributes: function(/* item */ item){
		//	summary: 
		//		See dojo.data.api.Identity.getIdentifierAttributes()
		 
		var identifier = this._features['dojo.data.api.Identity'];
		if(identifier === Number){
			// If (identifier === Number) it means getIdentity() just returns
			// an integer item-number for each item.  The dojo.data.api.Identity
			// spec says we need to return null if the identity is not composed 
			// of attributes 
			return null; // null
		}else{
			return [identifier]; // Array
		}
	},
	
	_forceLoad: function(){
		//	summary: 
		//		Internal function to force a load of the store if it hasn't occurred yet.  This is required
		//		for specific functions to work properly.  
		var self = this;
		if(this._jsonFileUrl){
				var getArgs = {
					url: self._jsonFileUrl, 
					handleAs: "json-comment-optional",
					sync: true
				};
			var getHandler = dojo.xhrGet(getArgs);
			getHandler.addCallback(function(data){
				try{
					//Check to be sure there wasn't another load going on concurrently 
					//So we don't clobber data that comes in on it.  If there is a load going on
					//then do not save this data.  It will potentially clobber current data.
					//We mainly wanted to sync/wait here.
					//TODO:  Revisit the loading scheme of this store to improve multi-initial
					//request handling.
					if (self._loadInProgress !== true && !self._loadFinished) {
						self._getItemsFromLoadedData(data);
						self._loadFinished = true;
					}
				}catch(e){
					console.log(e);
					throw e;
				}
			});
			getHandler.addErrback(function(error){
				throw error;
			});
		}else if(this._jsonData){
			self._getItemsFromLoadedData(self._jsonData);
			self._jsonData = null;
			self._loadFinished = true;
		} 
	}
});
//Mix in the simple fetch implementation to this class.
dojo.extend(dojo.data.ItemFileReadStore,dojo.data.util.simpleFetch);

}

if(!dojo._hasResource["dijit.form._DropDownTextBox"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.form._DropDownTextBox"] = true;
dojo.provide("dijit.form._DropDownTextBox");

dojo.declare(
	"dijit.form._DropDownTextBox",
	null,
	{
		// summary:
		//		Mixin text box with drop down

		templateString:"<table class=\"dijit dijitReset dijitInline dijitLeft\" baseClass=\"${baseClass}\" cellspacing=\"0\" cellpadding=\"0\"\n\tid=\"widget_${id}\" name=\"${name}\" dojoAttachEvent=\"onmouseover:_onMouse,onmouseout:_onMouse\" waiRole=\"presentation\"\n\t><tr\n\t\t><td class='dijitReset dijitStretch dijitComboBoxInput'\n\t\t\t><input class='XdijitInputField' type=\"text\" autocomplete=\"off\" name=\"${name}\"\n\t\t\tdojoAttachEvent=\"onkeypress, onkeyup, onfocus, onblur, compositionend\"\n\t\t\tdojoAttachPoint=\"textbox,focusNode\" id='${id}'\n\t\t\ttabIndex='${tabIndex}' size='${size}' maxlength='${maxlength}'\n\t\t\twaiRole=\"combobox\"\n\t\t></td\n\t\t><td class='dijitReset dijitRight dijitButtonNode dijitDownArrowButton'\n\t\t\tdojoAttachPoint=\"downArrowNode\"\n\t\t\tdojoAttachEvent=\"ondijitclick:_onArrowClick,onmousedown:_onMouse,onmouseup:_onMouse,onmouseover:_onMouse,onmouseout:_onMouse\"\n\t\t><div class=\"dijitDownArrowButtonInner\" waiRole=\"presentation\" tabIndex=\"-1\">\n\t\t\t<div class=\"dijit_a11y dijitDownArrowButtonChar\">&#9660;</div>\n\t\t</div>\n\t</td></tr>\n</table>\n",
		
		baseClass:"dijitComboBox",

		// hasDownArrow: Boolean
		// Set this textbox to have a down arrow button
		// Defaults to true
		hasDownArrow:true,

		// _popupWidget: Widget
		//	link to the popup widget created by makePopop
		_popupWidget:null,

		// _hasMasterPopup: Boolean
		//	Flag that determines if this widget should share one popup per widget prototype,
		//	or create one popup per widget instance.
		//	If true, then makePopup() creates one popup per widget prototype.
		//	If false, then makePopup() creates one popup per widget instance.
		_hasMasterPopup:false,

		// _popupClass: String
		//	Class of master popup (dijit.form._ComboBoxMenu)
		_popupClass:"",

		// _popupArgs: Object
		//	Object to pass to popup widget on initialization
		_popupArgs:{},
		
		// _hasFocus: Boolean
		// Represents focus state of the textbox
		_hasFocus:false,

		_arrowPressed: function(){
			if(!this.disabled&&this.hasDownArrow){
				dojo.addClass(this.downArrowNode, "dijitArrowButtonActive");
			}
		},

		_arrowIdle: function(){
			if(!this.disabled&&this.hasDownArrow){
				dojo.removeClass(this.downArrowNode, "dojoArrowButtonPushed");
			}
		},

		makePopup: function(){
			// summary:
			//	create popup widget on demand
			var _this=this;
			function _createNewPopup(){
				// common code from makePopup
				var node=document.createElement("div");
				document.body.appendChild(node);
				var popupProto=dojo.getObject(_this._popupClass, false);
				return new popupProto(_this._popupArgs, node);
			}
			// this code only runs if there is no popup reference
			if(!this._popupWidget){
				// does this widget have one "master" popup?
				if(this._hasMasterPopup){
					// does the master popup not exist yet?
					var parentClass = dojo.getObject(this.declaredClass, false);
					if(!parentClass.prototype._popupWidget){
						// create the master popup for the first time
						parentClass.prototype._popupWidget=_createNewPopup();
					}
					// assign master popup to local link
					this._popupWidget=parentClass.prototype._popupWidget;
				}else{
					// if master popup is not being used, create one popup per widget instance
					this._popupWidget=_createNewPopup();
				}
			}
		},

		_onArrowClick: function(){
			// summary: callback when arrow is clicked
			if(this.disabled){
				return;
			}
			this.focus();
			this.makePopup();
			if(this._isShowingNow){
				this._hideResultList();
			}else{
				// forces full population of results, if they click
				// on the arrow it means they want to see more options
				this._openResultList();
			}
		},

		_hideResultList: function(){
			if(this._isShowingNow){
				dijit.popup.close();
				this._arrowIdle();
				this._isShowingNow=false;
			}
		},

		_openResultList:function(){
			// summary:
			//	any code that needs to happen before the popup appears.
			//	creating the popupWidget contents etc.
			this._showResultList();
		},

		onfocus:function(){
			this._hasFocus=true;
		},

		onblur:function(){
			this._arrowIdle();
			this._hasFocus=false;
			// removeClass dijitInputFieldFocused
			dojo.removeClass(this.nodeWithBorder, "dijitInputFieldFocused");
			// hide the Tooltip
			this.validate(false);
		},

		onkeypress: function(/*Event*/ evt){
			// summary: generic handler for popup keyboard events
			if(evt.ctrlKey || evt.altKey){
				return;
			}
			switch(evt.keyCode){
				case dojo.keys.PAGE_DOWN:
				case dojo.keys.DOWN_ARROW:
					if(!this._isShowingNow||this._prev_key_esc){
						this.makePopup();
						this._arrowPressed();
						this._openResultList();
					}
					dojo.stopEvent(evt);
					this._prev_key_backspace = false;
					this._prev_key_esc = false;
					break;

				case dojo.keys.PAGE_UP:
				case dojo.keys.UP_ARROW:
				case dojo.keys.ENTER:
					// prevent default actions
					dojo.stopEvent(evt);
					// fall through
				case dojo.keys.ESCAPE:
				case dojo.keys.TAB:
					if(this._isShowingNow){
						this._prev_key_backspace = false;
						this._prev_key_esc = (evt.keyCode==dojo.keys.ESCAPE);
						this._hideResultList();
					}
					break;
			}
		},

		compositionend: function(/*Event*/ evt){
			// summary: When inputting characters using an input method, such as Asian
			// languages, it will generate this event instead of onKeyDown event
			this.onkeypress({charCode:-1});
		},

		_showResultList: function(){
			// Our dear friend IE doesnt take max-height so we need to calculate that on our own every time
			this._hideResultList();
			var childs = this._popupWidget.getListLength ? this._popupWidget.getItems() : [this._popupWidget.domNode];

			if(childs.length){
				var visibleCount = Math.min(childs.length,this.maxListLength);
				with(this._popupWidget.domNode.style){
					// trick to get the dimensions of the popup
					// TODO: doesn't dijit.popup.open() do this automatically?
					display="";
					width="";
					height="";
				}
				this._arrowPressed();
				// hide the tooltip
				this._displayMessage("");
				var best=this.open();
				// #3212: only set auto scroll bars if necessary
				// prevents issues with scroll bars appearing when they shouldn't when node is made wider (fractional pixels cause this)
				var popupbox=dojo.marginBox(this._popupWidget.domNode);
				this._popupWidget.domNode.style.overflow=((best.h==popupbox.h)&&(best.w==popupbox.w))?"hidden":"auto";
				dojo.marginBox(this._popupWidget.domNode, {h:best.h,w:Math.max(best.w,this.domNode.offsetWidth)});

			}
		},

		getDisplayedValue:function(){
			return this.textbox.value;
		},

		setDisplayedValue:function(/*String*/ value){
			this.textbox.value=value;
		},

		uninitialize:function(){
			if(this._popupWidget){
				this._hideResultList();
				this._popupWidget.destroy()
			};
		},

		open:function(){
			this.makePopup();
			var self=this;
			self._isShowingNow=true;
			return dijit.popup.open({
				popup: this._popupWidget,
				around: this.domNode,
				parent: this
			});
		},

		_onBlur: function(){
			// summary: called magically when focus has shifted away from this widget and it's dropdown
			this._hideResultList();
		},

		postMixInProperties:function(){
			this.baseClass=this.hasDownArrow?this.baseClass:this.baseClass+"NoArrow";
		}
	}
);

}

if(!dojo._hasResource["dijit.form.TextBox"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.form.TextBox"] = true;
dojo.provide("dijit.form.TextBox");



dojo.declare(
	"dijit.form.TextBox",
	dijit.form._FormWidget,
	{
		// summary:
		//		A generic textbox field.
		//		Serves as a base class to derive more specialized functionality in subclasses.

		//	trim: Boolean
		//		Removes leading and trailing whitespace if true.  Default is false.
		trim: false,

		//	uppercase: Boolean
		//		Converts all characters to uppercase if true.  Default is false.
		uppercase: false,

		//	lowercase: Boolean
		//		Converts all characters to lowercase if true.  Default is false.
		lowercase: false,

		//	propercase: Boolean
		//		Converts the first character of each word to uppercase if true.
		propercase: false,

		// size: String
		//		HTML INPUT tag size declaration.
		size: "20",

		// maxlength: String
		//		HTML INPUT tag maxlength declaration.
		maxlength: "999999",

		templateString:"<input dojoAttachPoint='textbox,focusNode' dojoAttachEvent='onfocus,onkeyup,onkeypress:_onKeyPress' autocomplete=\"off\"\n\tid='${id}' name='${name}' class=\"dijitInputField\" type='${type}' size='${size}' maxlength='${maxlength}' tabIndex='${tabIndex}'>\n",

		getTextValue: function(){
			return this.filter(this.textbox.value);
		},

		getValue: function(){
			return this.parse(this.getTextValue(), this.constraints);
		},

		setValue: function(value, /*Boolean, optional*/ priorityChange, /*String, optional*/ formattedValue){
			if(value == null){ value = ""; }
			value = this.filter(value);
			if(typeof formattedValue == "undefined" ){
				formattedValue = (typeof value == "undefined" || value == null || value == NaN) ? null : this.format(value, this.constraints);
			}
			if(formattedValue != null){
				var _this = this;
				// synchronous value set needed for InlineEditBox
				this.textbox.value = formattedValue;
			}
			dijit.form.TextBox.superclass.setValue.call(this, value, priorityChange);
		},

		forWaiValuenow: function(){
			return this.getTextValue();
		},

		format: function(/* String */ value, /* Object */ constraints){
			// summary: Replacable function to convert a value to a properly formatted string
			return value;
		},

		parse: function(/* String */ value, /* Object */ constraints){
			// summary: Replacable function to convert a formatted string to a value
			return value;
		},

		postCreate: function(){
			// get the node for which the background color will be updated
			if(typeof this.nodeWithBorder != "object"){
				this.nodeWithBorder = this.textbox;
			}
			// setting the value here is needed since value="" in the template causes "undefined"
			// and setting in the DOM (instead of the JS object) helps with form reset actions
			this.textbox.setAttribute("value", this.getTextValue());
			this.inherited('postCreate', arguments);
		},

		filter: function(val){
			// summary: Apply various filters to textbox value
			if(val == null){ return null; }
			if(this.trim){
				val = dojo.trim(val);
			}
			if(this.uppercase){
				val = val.toUpperCase();
			}
			if(this.lowercase){
				val = val.toLowerCase();
			}
			if(this.propercase){
				val = val.replace(/[^\s]+/g, function(word){
					return word.substring(0,1).toUpperCase() + word.substring(1);
				});
			}
			return val;
		},

		// event handlers, you can over-ride these in your own subclasses
		// TODO: this should be _onFocus (and onfocus removed from the template)
		onfocus: function(){
			dojo.addClass(this.nodeWithBorder, "dijitInputFieldFocused");
		},

		_onBlur: function(){
			dojo.removeClass(this.nodeWithBorder, "dijitInputFieldFocused");

			this.setValue(this.getValue(), true);
		},

		onkeyup: function(){
			// TODO: it would be nice to massage the value (ie: automatic uppercase, etc) as the user types
			// but this messes up the cursor position if you are typing into the middle of a word, and
			// also trimming doesn't work correctly (it prevents spaces between words too!)
			// this.setValue(this.getValue());
		}
	}
);

}

if(!dojo._hasResource["dijit.Tooltip"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.Tooltip"] = true;
dojo.provide("dijit.Tooltip");




dojo.declare(
	"dijit._MasterTooltip",
	[dijit._Widget, dijit._Templated],
	{
		// summary
		//		Internal widget that holds the actual tooltip markup,
		//		which occurs once per page.
		//		Called by Tooltip widgets which are just containers to hold
		//		the markup

		// duration: Integer
		//		Milliseconds to fade in/fade out
		duration: 200,

		templateString:"<div class=\"dijitTooltip dijitTooltipLeft\" id=\"dojoTooltip\">\n\t<div class=\"dijitTooltipContainer dijitTooltipContents\" dojoAttachPoint=\"containerNode\" waiRole='alert'></div>\n\t<div class=\"dijitTooltipConnector\"></div>\n</div>\n",

		postCreate: function(){
			dojo.body().appendChild(this.domNode);

			this.bgIframe = new dijit.BackgroundIframe(this.domNode);

			// Setup fade-in and fade-out functions.
			this.fadeIn = dojo.fadeIn({ node: this.domNode, duration: this.duration, onEnd: dojo.hitch(this, "_onShow") }),
			this.fadeOut = dojo.fadeOut({ node: this.domNode, duration: this.duration, onEnd: dojo.hitch(this, "_onHide") });

		},

		show: function(/*String*/ innerHTML, /*DomNode*/ aroundNode){
			// summary:
			//	Display tooltip w/specified contents to right specified node
			//	(To left if there's no space on the right, or if LTR==right)

			if(this.fadeOut.status() == "playing"){
				// previous tooltip is being hidden; wait until the hide completes then show new one
				this._onDeck=arguments;
				return;
			}
			this.containerNode.innerHTML=innerHTML;
			
			// Firefox bug. when innerHTML changes to be shorter than previous
			// one, the node size will not be updated until it moves.  
			this.domNode.style.top = (this.domNode.offsetTop + 1) + "px"; 

			// position the element and change CSS according to position	
			var align = this.isLeftToRight() ? {'BR': 'BL', 'BL': 'BR'} : {'BL': 'BR', 'BR': 'BL'};
			var pos = dijit.placeOnScreenAroundElement(this.domNode, aroundNode, align);
			this.domNode.className="dijitTooltip dijitTooltip" + (pos.corner=='BL' ? "Right" : "Left");
			
			// show it
			dojo.style(this.domNode, "opacity", 0);
			this.fadeIn.play();
			this.isShowingNow = true;
		},

		_onShow: function(){
			if(dojo.isIE){
				// the arrow won't show up on a node w/an opacity filter
				this.domNode.style.filter="";
			}
		},

		hide: function(){
			// summary: hide the tooltip
			if(this._onDeck){
				// this hide request is for a show() that hasn't even started yet;
				// just cancel the pending show()
				this._onDeck=null;
				return;
			}
			this.fadeIn.stop();
			this.isShowingNow = false;
			this.fadeOut.play();
		},

		_onHide: function(){
			this.domNode.style.cssText="";	// to position offscreen again
			if(this._onDeck){
				// a show request has been queued up; do it now
				this.show.apply(this, this._onDeck);
				this._onDeck=null;
			}
		}

	}
);

// Make a single tooltip markup on the page that is reused as appropriate
dojo.addOnLoad(function(){
	dijit.MasterTooltip = new dijit._MasterTooltip();
});

dojo.declare(
	"dijit.Tooltip",
	dijit._Widget,
	{
		// summary
		//		Pops up a tooltip (a help message) when you hover over a node.

		// label: String
		//		Text to display in the tooltip.
		//		Specified as innerHTML when creating the widget from markup.
		label: "",

		// showDelay: Integer
		//		Number of milliseconds to wait after hovering over/focusing on the object, before
		//		the tooltip is displayed.
		showDelay: 400,

		// connectId: String
		//		Id of domNode to attach the tooltip to.
		//		(When user hovers over specified dom node, the tooltip will appear.)
		connectId: "",

		postCreate: function(){
			this.srcNodeRef.style.display="none";

			this._connectNode = dojo.byId(this.connectId);

			dojo.forEach(["onMouseOver", "onHover", "onMouseOut", "onUnHover"], function(event){
				this.connect(this._connectNode, event.toLowerCase(), "_"+event);
			}, this);
		},

		_onMouseOver: function(/*Event*/ e){
			this._onHover(e);
		},

		_onMouseOut: function(/*Event*/ e){
			if(dojo.isDescendant(e.relatedTarget, this._connectNode)){
				// false event; just moved from target to target child; ignore.
				return;
			}
			this._onUnHover(e);
		},

		_onHover: function(/*Event*/ e){
			if(this._hover){ return; }
			this._hover=true;
			// If tooltip not showing yet then set a timer to show it shortly
			if(!this.isShowingNow && !this._showTimer){
				this._showTimer = setTimeout(dojo.hitch(this, "open"), this.showDelay);
			}
		},

		_onUnHover: function(/*Event*/ e){
			if(!this._hover){ return; }
			this._hover=false;

			if(this._showTimer){
				clearTimeout(this._showTimer);
				delete this._showTimer;
			}else{
				this.close();
			}
		},

		open: function(){
			// summary: display the tooltip; usually not called directly.
			if(this.isShowingNow){ return; }
			if(this._showTimer){
				clearTimeout(this._showTimer);
				delete this._showTimer;
			}
			dijit.MasterTooltip.show(this.label || this.domNode.innerHTML, this._connectNode);
			this.isShowingNow = true;
		},

		close: function(){
			// summary: hide the tooltip; usually not called directly.
			if(!this.isShowingNow){ return; }
			dijit.MasterTooltip.hide();
			this.isShowingNow = false;
		},

		uninitialize: function(){
			this.close();
		}
	}
);

}

if(!dojo._hasResource["dijit.form.ValidationTextBox"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.form.ValidationTextBox"] = true;
dojo.provide("dijit.form.ValidationTextBox");








dojo.declare(
	"dijit.form.ValidationTextBox",
	dijit.form.TextBox,
	{
		// summary:
		//		A subclass of TextBox.
		//		Over-ride isValid in subclasses to perform specific kinds of validation.

		// default values for new subclass properties
		// required: Boolean
		//		Can be true or false, default is false.
		required: false,
		// promptMessage: String
		//		Hint string
		promptMessage: "",
		// invalidMessage: String
		// 		The message to display if value is invalid.
		invalidMessage: "",
		// constraints: Object
		//		user-defined object needed to pass parameters to the validator functions
		constraints: {},
		// regExp: String
		//		regular expression string used to validate the input
		//		Do not specify both regExp and regExpGen
		regExp: ".*",
		// regExpGen: Function
		//		user replaceable function used to generate regExp when dependent on constraints
		//		Do not specify both regExp and regExpGen
		regExpGen: function(constraints){ return this.regExp; },

		setValue: function(){
			this.inherited('setValue', arguments);
			this.validate(false);
		},

		validator: function(value,constraints){
			// summary: user replaceable function used to validate the text input against the regular expression.
			return (new RegExp("^(" + this.regExpGen(constraints) + ")"+(this.required?"":"?")+"$")).test(value)&&(!this.required||!this._isEmpty(value));
		},

		isValid: function(/* Boolean*/ isFocused){
			// summary: Need to over-ride with your own validation code in subclasses
			return this.validator(this.textbox.value, this.constraints);
		},

		_isEmpty: function(value){
			// summary: Checks for whitespace
			return /^\s*$/.test(value); // Boolean
		},

		getErrorMessage: function(/* Boolean*/ isFocused){
			// summary: return an error message to show if appropriate
			return this.invalidMessage;
		},

		getPromptMessage: function(/* Boolean*/ isFocused){
			// summary: return a hint to show if appropriate
			return this.promptMessage;
		},

		validate: function(/* Boolean*/ isFocused){
			// summary:
			//		Called by oninit, onblur, and onkeypress.
			// description:
			//		Show missing or invalid messages if appropriate, and highlight textbox field.
			var message = "";
			var isValid = this.isValid(isFocused);
			var className = isValid ? "Normal" : "Error";
			if(!dojo.hasClass(this.nodeWithBorder, "dijitInputFieldValidation"+className)){
				dojo.removeClass(this.nodeWithBorder, "dijitInputFieldValidation"+((className=="Normal")?"Error":"Normal"));
				dojo.addClass(this.nodeWithBorder, "dijitInputFieldValidation"+className);
			}
			dijit.wai.setAttr(this.focusNode, "waiState", "invalid", (isValid? "false" : "true"));
			if(isFocused){
				if(this._isEmpty(this.textbox.value)){
					message = this.getPromptMessage(true);
				}
				if(!message && !isValid){
					message = this.getErrorMessage(true);
				}
			}
			this._displayMessage(message);
		},

		// currently displayed message
		_message: "",

		_displayMessage: function(/*String*/ message){
			if(this._message == message){ return; }
			this._message = message;
			this.displayMessage(message);
		},
		
		displayMessage: function(/*String*/ message){
			// summary:
			//		User overridable method to display validation errors/hints.
			//		By default uses a tooltip.
			if(message){
				dijit.MasterTooltip.show(message, this.domNode);
			}else{
				dijit.MasterTooltip.hide();
			}
		},

		_onBlur: function(evt){
			this.validate(false);
			this.inherited('_onBlur', arguments);
		},

		onfocus: function(evt){
			this.inherited('onfocus', arguments);
			this.validate(true);
		},

		onkeyup: function(evt){
			this.onfocus(evt);
		},

		postMixInProperties: function(){
			if(this.constraints == dijit.form.ValidationTextBox.prototype.constraints){
				this.constraints = {};
			}
			this.inherited('postMixInProperties', arguments);
			this.constraints.locale=this.lang;
			this.messages = dojo.i18n.getLocalization("dijit.form", "validate", this.lang);
			dojo.forEach(["invalidMessage", "missingMessage"], function(prop){
				if(!this[prop]){ this[prop] = this.messages[prop]; }
			}, this);
			var p = this.regExpGen(this.constraints);
			this.regExp = p;
			// make value a string for all types so that form reset works well
		}
	}
);

dojo.declare(
	"dijit.form.MappedTextBox",
	dijit.form.ValidationTextBox,
	{
		// summary:
		//		A subclass of ValidationTextBox.
		//		Provides a hidden input field and a serialize method to override

		serialize: function(val){
			// summary: user replaceable function used to convert the getValue() result to a String
			return val.toString();
		},

		toString: function(){
			// summary: display the widget as a printable string using the widget's value
			var val = this.getValue();
			return (val!=null) ? ((typeof val == "string") ? val : this.serialize(val, this.constraints)) : "";
		},

		validate: function(){
			this.valueNode.value = this.toString();
			this.inherited('validate', arguments);
		},

		postCreate: function(){
			var textbox = this.textbox;
			var valueNode = (this.valueNode = document.createElement("input"));
			valueNode.setAttribute("type", textbox.type);
			valueNode.setAttribute("value", this.toString());
			dojo.style(valueNode, "display", "none");
			valueNode.name = this.textbox.name;
			this.textbox.removeAttribute("name");

			dojo.place(valueNode, textbox, "after");

			this.inherited('postCreate', arguments);
		}
	}
);

dojo.declare(
	"dijit.form.RangeBoundTextBox",
	dijit.form.MappedTextBox,
	{
		// summary:
		//		A subclass of MappedTextBox.
		//		Tests for a value out-of-range
		/*===== contraints object:
		// min: Number
		//		Minimum signed value.  Default is -Infinity
		min: undefined,
		// max: Number
		//		Maximum signed value.  Default is +Infinity
		max: undefined,
		=====*/

		// rangeMessage: String
		//              The message to display if value is out-of-range
		rangeMessage: "",

		compare: function(val1, val2){
			// summary: compare 2 values
			return val1 - val2;
		},

		rangeCheck: function(/* Number */ primitive, /* Object */ constraints){
			// summary: user replaceable function used to validate the range of the numeric input value
			var isMin = (typeof constraints.min != "undefined");
			var isMax = (typeof constraints.max != "undefined");
			if(isMin || isMax){
				return (!isMin || this.compare(primitive,constraints.min) >= 0) &&
					(!isMax || this.compare(primitive,constraints.max) <= 0);
			}else{ return true; }
		},

		isInRange: function(/* Boolean*/ isFocused){
			// summary: Need to over-ride with your own validation code in subclasses
			return this.rangeCheck(this.getValue(), this.constraints);
		},

		isValid: function(/* Boolean*/ isFocused){
			return this.inherited('isValid', arguments) &&
				((this._isEmpty(this.textbox.value) && !this.required) || this.isInRange(isFocused));
		},

		getErrorMessage: function(/* Boolean*/ isFocused){
			if(dijit.form.RangeBoundTextBox.superclass.isValid.call(this, false) && !this.isInRange(isFocused)){ return this.rangeMessage; }
			else{ return this.inherited('getErrorMessage', arguments); }
		},

		postMixInProperties: function(){
			this.inherited('postMixInProperties', arguments);
			if(!this.rangeMessage){
				this.messages = dojo.i18n.getLocalization("dijit.form", "validate", this.lang);
				this.rangeMessage = this.messages.rangeMessage;
			}
		},

		postCreate: function(){
			this.inherited('postCreate', arguments);
			if(typeof this.constraints.min != "undefined"){
				dijit.wai.setAttr(this.domNode, "waiState", "valuemin", this.constraints.min);
			}
			if(typeof this.constraints.max != "undefined"){
				dijit.wai.setAttr(this.domNode, "waiState", "valuemax", this.constraints.max);
			}
		}
	}
);

}

if(!dojo._hasResource["dijit.form.ComboBox"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.form.ComboBox"] = true;
dojo.provide("dijit.form.ComboBox");






dojo.declare(
	"dijit.form.ComboBoxMixin",
	dijit.form._DropDownTextBox,
	{
		// summary:
		//		Auto-completing text box, and base class for FilteringSelect widget.
		//
		//		The drop down box's values are populated from an class called
		//		a data provider, which returns a list of values based on the characters
		//		that the user has typed into the input box.
		//
		//		Some of the options to the ComboBox are actually arguments to the data
		//		provider.

		// pageSize: Integer
		//		Argument to data provider.
		//		Specifies number of search results per page (before hitting "next" button)
		pageSize: 30,

		// store: Object
		//		Reference to data provider object used by this ComboBox
		store: null,

		// query: Object 
		//		A query that can be passed to 'store' to initially filter the items, 
		//		before doing further filtering based on searchAttr and the key. 
		query: {},

		// autoComplete: Boolean
		//		If you type in a partial string, and then tab out of the <input> box,
		//		automatically copy the first entry displayed in the drop down list to
		//		the <input> field
		autoComplete: true,

		// searchDelay: Integer
		//		Delay in milliseconds between when user types something and we start
		//		searching based on that value
		searchDelay: 100,

		// searchAttr: String
		//		Searches pattern match against this field
		searchAttr: "name",

		// ignoreCase: Boolean
		//		Does the ComboBox menu ignore case?
		ignoreCase: true,

		_hasMasterPopup:true,

		_popupClass:"dijit.form._ComboBoxMenu",

		getValue:function(){
			// don't get the textbox value but rather the previously set hidden value
			return dijit.form.TextBox.superclass.getValue.apply(this, arguments);
		},

		setDisplayedValue:function(/*String*/ value){
			this.setValue(value, true);
		},

		_getCaretPos: function(/*DomNode*/ element){
			// khtml 3.5.2 has selection* methods as does webkit nightlies from 2005-06-22
			if(typeof(element.selectionStart)=="number"){
				// FIXME: this is totally borked on Moz < 1.3. Any recourse?
				return element.selectionStart;
			}else if(dojo.isIE){
				// in the case of a mouse click in a popup being handled,
				// then the document.selection is not the textarea, but the popup
				// var r = document.selection.createRange();
				// hack to get IE 6 to play nice. What a POS browser.
				var tr = document.selection.createRange().duplicate();
				var ntr = element.createTextRange();
				tr.move("character",0);
				ntr.move("character",0);
				try{
					// If control doesnt have focus, you get an exception.
					// Seems to happen on reverse-tab, but can also happen on tab (seems to be a race condition - only happens sometimes).
					// There appears to be no workaround for this - googled for quite a while.
					ntr.setEndPoint("EndToEnd", tr);
					return String(ntr.text).replace(/\r/g,"").length;
				}
				catch(e){
					return 0; // If focus has shifted, 0 is fine for caret pos.
				}
			}
		},

		_setCaretPos: function(/*DomNode*/ element, /*Number*/ location){
			location = parseInt(location);
			this._setSelectedRange(element, location, location);
		},

		_setSelectedRange: function(/*DomNode*/ element, /*Number*/ start, /*Number*/ end){
			if(!end){
				end = element.value.length;
			}  // NOTE: Strange - should be able to put caret at start of text?
			// Mozilla
			// parts borrowed from http://www.faqts.com/knowledge_base/view.phtml/aid/13562/fid/130
			if(element.setSelectionRange){
				dijit.focus(element);
				element.setSelectionRange(start, end);
			}else if(element.createTextRange){ // IE
				var range = element.createTextRange();
				with(range){
					collapse(true);
					moveEnd('character', end);
					moveStart('character', start);
					select();
				}
			}else{ //otherwise try the event-creation hack (our own invention)
				// do we need these?
				element.value = element.value;
				element.blur();
				dijit.focus(element);
				// figure out how far back to go
				var dist = parseInt(element.value.length)-end;
				var tchar = String.fromCharCode(37);
				var tcc = tchar.charCodeAt(0);
				for(var x = 0; x < dist; x++){
					var te = document.createEvent("KeyEvents");
					te.initKeyEvent("keypress", true, true, null, false, false, false, false, tcc, tcc);
					element.dispatchEvent(te);
				}
			}
		},

		onkeypress: function(/*Event*/ evt){
			// summary: handles keyboard events
			if(evt.ctrlKey || evt.altKey){
				return;
			}
			var doSearch = false;
			if(this._isShowingNow){this._popupWidget.handleKey(evt);}
			switch(evt.keyCode){
				case dojo.keys.PAGE_DOWN:
				case dojo.keys.DOWN_ARROW:
					if(!this._isShowingNow||this._prev_key_esc){
						this._arrowPressed();
						doSearch=true;
					}else{
						this._announceOption(this._popupWidget.getHighlightedOption());
					}
					dojo.stopEvent(evt);
					this._prev_key_backspace = false;
					this._prev_key_esc = false;
					break;

				case dojo.keys.PAGE_UP:
				case dojo.keys.UP_ARROW:
					if(this._isShowingNow){
						this._announceOption(this._popupWidget.getHighlightedOption());
					}
					dojo.stopEvent(evt);
					this._prev_key_backspace = false;
					this._prev_key_esc = false;
					break;

				case dojo.keys.ENTER:
					// prevent submitting form if user presses enter
					// also prevent accepting the value if either Next or Previous are selected
					if(this._isShowingNow){
						// only stop event on prev/next
						var highlighted=this._popupWidget.getHighlightedOption();
						if(highlighted==this._popupWidget.nextButton){
							this._nextSearch(1);
							dojo.stopEvent(evt);
							break;
						}
						else if(highlighted==this._popupWidget.previousButton){
							this._nextSearch(-1);
							dojo.stopEvent(evt);
							break;
						}
					}
					// default case:
					// prevent submit, but allow event to bubble
					evt.preventDefault();
					// fall through

				case dojo.keys.TAB:
					if(this._isShowingNow){
						this._prev_key_backspace = false;
						this._prev_key_esc = false;
						if(this._isShowingNow&&this._popupWidget.getHighlightedOption()){
							this._popupWidget.setValue({target:this._popupWidget.getHighlightedOption()}, true);
						}else{
							this.setDisplayedValue(this.getDisplayedValue());
						}
						this._hideResultList();
					}else{
						// also allow arbitrary user input
						this.setDisplayedValue(this.getDisplayedValue());
					}
					break;

				case dojo.keys.SPACE:
					this._prev_key_backspace = false;
					this._prev_key_esc = false;
					if(this._isShowingNow && this._popupWidget.getHighlightedOption()){
						dojo.stopEvent(evt);
						this._selectOption();
						this._hideResultList();
					}
					else{doSearch=true;}
					break;

				case dojo.keys.ESCAPE:
					this._prev_key_backspace = false;
					this._prev_key_esc = true;
					this._hideResultList();
					this.setValue(this.getValue());
					break;

				case dojo.keys.DELETE:
				case dojo.keys.BACKSPACE:
					this._prev_key_esc = false;
					this._prev_key_backspace = true;
					doSearch=true;
					break;

				case dojo.keys.RIGHT_ARROW: // fall through

				case dojo.keys.LEFT_ARROW: // fall through
					this._prev_key_backspace = false;
					this._prev_key_esc = false;
					break;

				default:// non char keys (F1-F12 etc..)  shouldn't open list
					this._prev_key_backspace = false;
					this._prev_key_esc = false;
					if(evt.charCode!=0){
						doSearch=true;
					}
			}
			if(this.searchTimer){
				clearTimeout(this.searchTimer);
			}
			if(doSearch){
				// need to wait a tad before start search so that the event bubbles through DOM and we have value visible
				this.searchTimer = setTimeout(dojo.hitch(this, this._startSearchFromInput), this.searchDelay);
			}
		},

		_autoCompleteText: function(/*String*/ text){
			// summary:
			// Fill in the textbox with the first item from the drop down list, and
			// highlight the characters that were auto-completed.   For example, if user
			// typed "CA" and the drop down list appeared, the textbox would be changed to
			// "California" and "ifornia" would be highlighted.

			// IE7: clear selection so next highlight works all the time
			this._setSelectedRange(this.focusNode, this.focusNode.value.length, this.focusNode.value.length);
			// does text autoComplete the value in the textbox?
			// #3744: escape regexp so the user's input isn't treated as a regular expression.
			// Example: If the user typed "(" then the regexp would throw "unterminated parenthetical."
			// Also see #2558 for the autocompletion bug this regular expression fixes.
			if(new RegExp("^"+escape(this.focusNode.value), this.ignoreCase ? "i" : "").test(escape(text))){
				var cpos = this._getCaretPos(this.focusNode);
				// only try to extend if we added the last character at the end of the input
				if((cpos+1) > this.focusNode.value.length){
					// only add to input node as we would overwrite Capitalisation of chars
					// actually, that is ok
					this.focusNode.value = text;//.substr(cpos);
					// visually highlight the autocompleted characters
					this._setSelectedRange(this.focusNode, cpos, this.focusNode.value.length);
				}
			}else{
				// text does not autoComplete; replace the whole value and highlight
				this.focusNode.value = text;
				this._setSelectedRange(this.focusNode, 0, this.focusNode.value.length);
			}
		},

		_openResultList: function(/*Object*/ results, /*Object*/ dataObject){
			if(this.disabled||dataObject.query[this.searchAttr]!=this._lastQuery){
				return;
			}
			this._popupWidget.clearResultList();
			if(!results.length){
				this._hideResultList();
				return;
			}

			// Fill in the textbox with the first item from the drop down list, and
			// highlight the characters that were auto-completed.   For example, if user
			// typed "CA" and the drop down list appeared, the textbox would be changed to
			// "California" and "ifornia" would be highlighted.

			var zerothvalue=new String(this.store.getValue(results[0], this.searchAttr));
			if(zerothvalue&&(this.autoComplete)&&
			(!this._prev_key_backspace)&&
			// when the user clicks the arrow button to show the full list,
			// startSearch looks for "*".
			// it does not make sense to autocomplete
			// if they are just previewing the options available.
			(dataObject.query[this.searchAttr]!="*")){
				this._autoCompleteText(zerothvalue);
				// announce the autocompleted value
				dijit.wai.setAttr(this.focusNode || this.domNode, "waiState", "valuenow", zerothvalue);
			}
			this._popupWidget.createOptions(results, dataObject, dojo.hitch(this, this._getMenuLabelFromItem));

			// show our list (only if we have content, else nothing)
			this._showResultList();
		},

		onfocus:function(){
			dijit.form._DropDownTextBox.prototype.onfocus.apply(this, arguments);
			this.inherited('onfocus', arguments);
		},

		onblur:function(){ /* not _onBlur! */
			// call onblur first to avoid race conditions with _hasFocus
			dijit.form._DropDownTextBox.prototype.onblur.apply(this, arguments);
			if(!this._isShowingNow){
				// if the user clicks away from the textbox, set the value to the textbox value
				this.setDisplayedValue(this.getDisplayedValue());
			}
			// don't call this since the TextBox setValue is asynchronous
			// if you uncomment this line, when you click away from the textbox,
			// the value in the textbox reverts to match the hidden value
			//this.parentClass.onblur.apply(this, arguments);
		},

		_announceOption: function(/*Node*/ node){
			// summary:
			//	a11y code that puts the highlighted option in the textbox
			//	This way screen readers will know what is happening in the menu

			if(node==null){return;}
			// pull the text value from the item attached to the DOM node
			var newValue;
			if(node==this._popupWidget.nextButton||node==this._popupWidget.previousButton){
				newValue=node.innerHTML;
			}else{
				newValue=this.store.getValue(node.item, this.searchAttr);
			}
			// get the text that the user manually entered (cut off autocompleted text)
			this.focusNode.value=this.focusNode.value.substring(0, this._getCaretPos(this.focusNode));
			// autocomplete the rest of the option to announce change
			this._autoCompleteText(newValue);
		},

		_selectOption: function(/*Event*/ evt){
			var tgt = null;
			if(!evt){
				evt ={ target: this._popupWidget.getHighlightedOption()};
			}
				// what if nothing is highlighted yet?
			if(!evt.target){
				// handle autocompletion where the the user has hit ENTER or TAB
				this.setDisplayedValue(this.getDisplayedValue());
				return;
			// otherwise the user has accepted the autocompleted value
			}else{
				tgt = evt.target;
			}
			if(!evt.noHide){
				this._hideResultList();
				this._setCaretPos(this.focusNode, this.store.getValue(tgt.item, this.searchAttr).length);
			}
			this._doSelect(tgt);
		},

		_doSelect: function(tgt){
			this.setValue(this.store.getValue(tgt.item, this.searchAttr), true);
		},

		_onArrowClick: function(){
			// summary: callback when arrow is clicked
			if(this.disabled){
				return;
			}
			this.focus();
			this.makePopup();
			if(this._isShowingNow){
				this._hideResultList();
			}else{
				// forces full population of results, if they click
				// on the arrow it means they want to see more options
				this._startSearch("");
			}
		},

		_startSearchFromInput: function(){
			this._startSearch(this.focusNode.value);
		},

		_startSearch: function(/*String*/ key){
			this.makePopup();
			// create a new query to prevent accidentally querying for a hidden value from FilteringSelect's keyField
			var query=this.query;
			this._lastQuery=query[this.searchAttr]=key+"*";
			var dataObject=this.store.fetch({queryOptions:{ignoreCase:this.ignoreCase, deep:true}, query: query, onComplete:dojo.hitch(this, "_openResultList"), start:0, count:this.pageSize});
			function nextSearch(dataObject, direction){
				dataObject.start+=dataObject.count*direction;
				dataObject.store.fetch(dataObject);
			}
			this._nextSearch=this._popupWidget.onPage=dojo.hitch(this, nextSearch, dataObject);
		},

		_getValueField:function(){
			return this.searchAttr;
		},

		postMixInProperties: function(){
			dijit.form._DropDownTextBox.prototype.postMixInProperties.apply(this, arguments);
			if(!this.store){
				// if user didn't specify store, then assume there are option tags
				var items = dojo.query("> option", this.srcNodeRef).map(function(node){
					node.style.display="none";
					return { value: node.getAttribute("value"), name: String(node.innerHTML) };
				});
				this.store = new dojo.data.ItemFileReadStore({data: {identifier:this._getValueField(), items:items}});

				// if there is no value set and there is an option list,
				// set the value to the first value to be consistent with native Select
				if(items&&items.length&&!this.value){
					// For <select>, IE does not let you set the value attribute of the srcNodeRef (and thus dojo.mixin does not copy it).
					// IE does understand selectedIndex though, which is automatically set by the selected attribute of an option tag
					this.value=items[this.srcNodeRef.selectedIndex!=-1?this.srcNodeRef.selectedIndex:0][this._getValueField()];
				}
			}
			// instantiate query so comboboxes with different data stores and default query work together
			if(this.query==dijit.form.ComboBoxMixin.prototype.query){this.query={};}
		},

		postCreate: function(){
			// call the associated TextBox postCreate
			// ValidationTextBox for ComboBox; MappedTextBox for FilteringSelect
			this.inherited('postCreate', arguments);
		},

		_getMenuLabelFromItem:function(/*Item*/ item){
			return {html:false, label:this.store.getValue(item, this.searchAttr)};
		},

		open:function(){
			this._popupWidget.onChange=dojo.hitch(this, this._selectOption);
			// connect onkeypress to ComboBox
			this._popupWidget._onkeypresshandle=this._popupWidget.connect(this._popupWidget.domNode, "onkeypress", dojo.hitch(this, this.onkeypress));
			return dijit.form._DropDownTextBox.prototype.open.apply(this, arguments);
		}
	}
);

dojo.declare(
	"dijit.form._ComboBoxMenu",
	[dijit._Widget, dijit._Templated],

	{
		// summary:
		//	Focus-less div based menu for internal use in ComboBox

		templateString:"<div class='dijitMenu' dojoAttachEvent='onclick,onmouseover,onmouseout' tabIndex='-1' style='display:none; position:absolute; overflow:\"auto\";'>"
				+"<div class='dijitMenuItem' dojoAttachPoint='previousButton'></div>"
				+"<div class='dijitMenuItem' dojoAttachPoint='nextButton'></div>"
			+"</div>",
		_onkeypresshandle:null,
		_messages:null,
		_comboBox:null,

		postMixInProperties:function(){
			this._messages = dojo.i18n.getLocalization("dijit.form", "ComboBox", this.lang);
			this.inherited("postMixInProperties", arguments);
		},

		setValue:function(/*Object*/ value){
			this.value=value;
			this.onChange(value);
		},

		onChange:function(/*Object*/ value){},
		onPage:function(/*Number*/ direction){},

		postCreate:function(){
			// fill in template with i18n messages
			this.previousButton.innerHTML=this._messages["previousMessage"];
			this.nextButton.innerHTML=this._messages["nextMessage"];
			this.inherited("postCreate", arguments);
		},

		onClose:function(){
			this.disconnect(this._onkeypresshandle);
			this._blurOptionNode();
		},

		_createOption:function(/*Object*/ item, labelFunc){
			// summary: creates an option to appear on the popup menu
			// subclassed by FilteringSelect

			var labelObject=labelFunc(item);
			var menuitem = document.createElement("div");
			if(labelObject.html){menuitem.innerHTML=labelObject.label;}
			else{menuitem.appendChild(document.createTextNode(labelObject.label));}
			// #3250: in blank options, assign a normal height
			if(menuitem.innerHTML==""){
				menuitem.innerHTML="&nbsp;"
			}
			menuitem.item=item;
			return menuitem;
		},

		createOptions:function(results, dataObject, labelFunc){
			//this._dataObject=dataObject;
			//this._dataObject.onComplete=dojo.hitch(comboBox, comboBox._openResultList);
			// display "Previous . . ." button
			this.previousButton.style.display=dataObject.start==0?"none":"";
			// create options using _createOption function defined by parent ComboBox (or FilteringSelect) class
			// #2309: iterate over cache nondestructively
			var _this=this;
			dojo.forEach(results, function(item){
				var menuitem=_this._createOption(item, labelFunc);
				menuitem.className = "dijitMenuItem";
				_this.domNode.insertBefore(menuitem, _this.nextButton);
			});
			// display "Next . . ." button
			this.nextButton.style.display=dataObject.count==results.length?"":"none";
		},

		clearResultList:function(){
			// keep the previous and next buttons of course
			while(this.domNode.childNodes.length>2){
				this.domNode.removeChild(this.domNode.childNodes[this.domNode.childNodes.length-2]);
			}
		},

		// these functions are called in showResultList
		getItems:function(){
			return this.domNode.childNodes;
		},

		getListLength:function(){
			return this.domNode.childNodes.length-2;
		},

		onclick:function(/*Event*/ evt){
			if(evt.target === this.domNode){
				return;
			}else if(evt.target==this.previousButton){
				this.onPage(-1);
			}else if(evt.target==this.nextButton){
				this.onPage(1);
			}else{
				var tgt=evt.target;
				// while the clicked node is inside the div
				while(!tgt.item){
					// recurse to the top
					tgt=tgt.parentNode;
				}
				this.setValue({target:tgt}, true);
			}
		},

		onmouseover:function(/*Event*/ evt){
			if(evt.target === this.domNode){ return; }
			this._focusOptionNode(evt.target);
		},

		onmouseout:function(/*Event*/ evt){
			if(evt.target === this.domNode){ return; }
			this._blurOptionNode();
		},

		_focusOptionNode:function(/*DomNode*/ node){
			// summary:
			//	does the actual highlight
			if(this._highlighted_option != node){
				this._blurOptionNode();
				this._highlighted_option = node;
				dojo.addClass(this._highlighted_option, "dijitMenuItemHover");
			}
		},

		_blurOptionNode:function(){
			// summary:
			//	removes highlight on highlighted option
			if(this._highlighted_option){
				dojo.removeClass(this._highlighted_option, "dijitMenuItemHover");
				this._highlighted_option = null;
			}
		},

		_highlightNextOption:function(){
			// because each press of a button clears the menu,
			// the highlighted option sometimes becomes detached from the menu!
			// test to see if the option has a parent to see if this is the case.
			if(!this.getHighlightedOption()){
				this._focusOptionNode(this.domNode.firstChild.style.display=="none"?this.domNode.firstChild.nextSibling:this.domNode.firstChild);
			}else if(this._highlighted_option.nextSibling&&this._highlighted_option.nextSibling.style.display!="none"){
				this._focusOptionNode(this._highlighted_option.nextSibling);
			}
			dijit.scrollIntoView(this._highlighted_option);
		},


		_highlightPrevOption:function(){
			// if nothing selected, highlight last option
			// makes sense if you select Previous and try to keep scrolling up the list
			if(!this.getHighlightedOption()){
				this._focusOptionNode(this.domNode.lastChild.style.display=="none"?this.domNode.lastChild.previousSibling:this.domNode.lastChild);
			}else if(this._highlighted_option.previousSibling&&this._highlighted_option.previousSibling.style.display!="none"){
				this._focusOptionNode(this._highlighted_option.previousSibling);
			}
			dijit.scrollIntoView(this._highlighted_option);
		},

		_page:function(/*Boolean*/ up){
			var scrollamount=0;
			var oldscroll=this.domNode.scrollTop;
			var height=parseInt(dojo.getComputedStyle(this.domNode).height);
			// if no item is highlighted, highlight the first option
			if(!this.getHighlightedOption()){this._highlightNextOption();}
			while(scrollamount<height){
				if(up){
					// stop at option 1
					if(!this.getHighlightedOption().previousSibling||this._highlighted_option.previousSibling.style.display=="none"){break;}
					this._highlightPrevOption();
				}else{
					// stop at last option
					if(!this.getHighlightedOption().nextSibling||this._highlighted_option.nextSibling.style.display=="none"){break;}
					this._highlightNextOption();
				}
				// going backwards
				var newscroll=this.domNode.scrollTop;
				scrollamount+=(newscroll-oldscroll)*(up ? -1:1);
				oldscroll=newscroll;
			}
		},

		pageUp:function(){
			this._page(true);
		},

		pageDown:function(){
			this._page(false);
		},

		getHighlightedOption:function(){
			// summary:
			//	Returns the highlighted option.
			return this._highlighted_option&&this._highlighted_option.parentNode ? this._highlighted_option : null;
		},

		handleKey:function(evt){
			switch(evt.keyCode){
				case dojo.keys.DOWN_ARROW:
					this._highlightNextOption();
					break;
				case dojo.keys.PAGE_DOWN:
					this.pageDown();
					break;	
				case dojo.keys.UP_ARROW:
					this._highlightPrevOption();
					break;
				case dojo.keys.PAGE_UP:
					this.pageUp();
					break;	
			}
		}
	}
);

dojo.declare(
	"dijit.form.ComboBox",
	[dijit.form.ValidationTextBox, dijit.form.ComboBoxMixin],
	{}
);

}

if(!dojo._hasResource["dijit.form.FilteringSelect"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.form.FilteringSelect"] = true;
dojo.provide("dijit.form.FilteringSelect");



dojo.declare(
	"dijit.form.FilteringSelect",
	[dijit.form.MappedTextBox, dijit.form.ComboBoxMixin],
	{
		/*
		 * summary
		 *	Enhanced version of HTML's <select> tag.
		 *
		 *	Similar features:
		 *	  - There is a drop down list of possible values.
		 *    - You can only enter a value from the drop down list.  (You can't enter an arbitrary value.)
		 *    - The value submitted with the form is the hidden value (ex: CA),
		 *      not the displayed value a.k.a. label (ex: California)
		 *
		 *	Enhancements over plain HTML version:
		 *    - If you type in some text then it will filter down the list of possible values in the drop down list.
		 *    - List can be specified either as a static list or via a javascript function (that can get the list from a server)
		 */

		// searchAttr: String
		//		Searches pattern match against this field

		// labelAttr: String
		//		Optional.  The text that actually appears in the drop down.
		//		If not specified, the searchAttr text is used instead.
		labelAttr: "",

		// labelType: String
		//		"html" or "text"
		labelType: "text",

		_isvalid:true,

		isValid:function(){
			return this._isvalid;
		},

		_callbackSetLabel: function(/*Array*/ result, /*Object*/ dataObject){
			// summary
			//	Callback function that dynamically sets the label of the ComboBox

			// setValue does a synchronous lookup,
			// so it calls _callbackSetLabel directly, 
			// and so does not pass dataObject
			// dataObject==null means do not test the lastQuery, just continue
			if(dataObject&&dataObject.query[this.searchAttr]!=this._lastQuery){return;}
			if(!result.length){
				//#3268: do nothing on bad input
				//this._setValue("", "");
				//#3285: change CSS to indicate error
				this._isvalid=false;
				this.validate(this._hasFocus);
			}else{
				this._setValueFromItem(result[0]);
			}
		},

		_openResultList: function(/*Object*/ results, /*Object*/ dataObject){
			// #3285: tap into search callback to see if user's query resembles a match
			if(dataObject.query[this.searchAttr]!=this._lastQuery){return;}
			this._isvalid=results.length!=0;
			this.validate(true);
			dijit.form.ComboBoxMixin.prototype._openResultList.apply(this, arguments);
		},

		getValue:function(){
			// don't get the textbox value but rather the previously set hidden value
			return this.valueNode.value;
		},

		_getValueField:function(){
			// used for option tag selects
			return "value";
		},

		_setValue:function(/*String*/ value, /*String*/ displayedValue){
			this.valueNode.value = value;
			dijit.form.FilteringSelect.superclass.setValue.call(this, value, true, displayedValue);
		},

		setValue: function(/*String*/ value){
			// summary
			//	Sets the value of the select.
			//	Also sets the label to the corresponding value by reverse lookup.

			//#3347: fetchItemByIdentity if no keyAttr specified
			var self=this;
			var handleFetchByIdentity = function(item){
				if(item){
					if(self.store.isItemLoaded(item)){
						self._callbackSetLabel([item]);
					}else{
						self.store.loadItem({item:item, onItem: self._callbackSetLabel});
					}
				}else{
					self._isvalid=false;
					// prevent errors from Tooltip not being created yet
					self.validate(false);
				}
			}
			this.store.fetchItemByIdentity({identity: value, onItem: handleFetchByIdentity});
		},

		_setValueFromItem: function(/*item*/ item){
			// summary
			//	Set the displayed valued in the input box, based on a selected item.
			//	Users shouldn't call this function; they should be calling setDisplayedValue() instead
			this._isvalid=true;
			this._setValue(this.store.getIdentity(item), this.labelFunc(item, this.store));
		},

		labelFunc: function(/*item*/ item, /*dojo.data.store*/ store){
			// summary: Event handler called when the label changes
			// returns the label that the ComboBox should display
			return store.getValue(item, this.searchAttr);
		},

		onkeyup: function(/*Event*/ evt){
			// summary: internal function
			// FilteringSelect needs to wait for the complete label before committing to a reverse lookup
			//this.setDisplayedValue(this.textbox.value);
		},

		_doSelect: function(/*Event*/ tgt){
			// summary:
			//	ComboBox's menu callback function
			//	FilteringSelect overrides this to set both the visible and hidden value from the information stored in the menu

			this._setValueFromItem(tgt.item);
		},

		setDisplayedValue:function(/*String*/ label){
			// summary:
			//	Set textbox to display label
			//	Also performs reverse lookup to set the hidden value
			//	Used in InlineEditBox

			if(this.store){
				var query={};
				this._lastQuery=query[this.searchAttr]=label;
				// if the label is not valid, the callback will never set it,
				// so the last valid value will get the warning textbox
				// set the textbox value now so that the impending warning will make sense to the user
				this.textbox.value=label;
				this.store.fetch({query:query, queryOptions:{ignoreCase:this.ignoreCase, deep:true}, onComplete: dojo.hitch(this, this._callbackSetLabel)});
			}
		},

		_getMenuLabelFromItem:function(/*Item*/ item){
			// internal function to help ComboBoxMenu figure out what to display
			if(this.labelAttr){return {html:this.labelType=="html", label:this.store.getValue(item, this.labelAttr)};}
			else{
				// because this function is called by ComboBoxMenu, this.inherited tries to find the superclass of ComboBoxMenu
				return dijit.form.ComboBoxMixin.prototype._getMenuLabelFromItem.apply(this, arguments);
			}
		}
	}
);

}

if(!dojo._hasResource["dojo.cldr.supplemental"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.cldr.supplemental"] = true;
dojo.provide("dojo.cldr.supplemental");



dojo.cldr.supplemental.getFirstDayOfWeek = function(/*String?*/locale){
// summary: Returns a zero-based index for first day of the week
// description:
//		Returns a zero-based index for first day of the week, as used by the local (Gregorian) calendar.
//		e.g. Sunday (returns 0), or Monday (returns 1)

	// from http://www.unicode.org/cldr/data/common/supplemental/supplementalData.xml:supplementalData/weekData/firstDay
	var firstDay = {/*default is 1=Monday*/
		mv:5,
		ae:6,af:6,bh:6,dj:6,dz:6,eg:6,er:6,et:6,iq:6,ir:6,jo:6,ke:6,kw:6,lb:6,ly:6,ma:6,om:6,qa:6,sa:6,
		sd:6,so:6,tn:6,ye:6,
		as:0,au:0,az:0,bw:0,ca:0,cn:0,fo:0,ge:0,gl:0,gu:0,hk:0,ie:0,il:0,is:0,jm:0,jp:0,kg:0,kr:0,la:0,
		mh:0,mo:0,mp:0,mt:0,nz:0,ph:0,pk:0,sg:0,th:0,tt:0,tw:0,um:0,us:0,uz:0,vi:0,za:0,zw:0,
		et:0,mw:0,ng:0,tj:0,
		gb:0,
		sy:4
	};

	var country = dojo.cldr.supplemental._region(locale);
	var dow = firstDay[country];
	return (typeof dow == 'undefined') ? 1 : dow; /*Number*/
};

dojo.cldr.supplemental._region = function(/*String?*/locale){
	locale = dojo.i18n.normalizeLocale(locale);
	var tags = locale.split('-');
	var region = tags[1];
	if(!region){
		// IE often gives language only (#2269)
		// Arbitrary mappings of language-only locales to a country:
        region = {de:"de", en:"us", es:"es", fi:"fi", fr:"fr", hu:"hu", it:"it",
        ja:"jp", ko:"kr", nl:"nl", pt:"br", sv:"se", zh:"cn"}[tags[0]];
	}else if(region.length == 4){
		// The ISO 3166 country code is usually in the second position, unless a
		// 4-letter script is given. See http://www.ietf.org/rfc/rfc4646.txt
		region = tags[2];
	}
	return region;
}

dojo.cldr.supplemental.getWeekend = function(/*String?*/locale){
// summary: Returns a hash containing the start and end days of the weekend
// description:
//		Returns a hash containing the start and end days of the weekend according to local custom using locale,
//		or by default in the user's locale.
//		e.g. {start:6, end:0}

	// from http://www.unicode.org/cldr/data/common/supplemental/supplementalData.xml:supplementalData/weekData/weekend{Start,End}
	var weekendStart = {/*default is 6=Saturday*/
		eg:5,il:5,sy:5,
		'in':0,
		ae:4,bh:4,dz:4,iq:4,jo:4,kw:4,lb:4,ly:4,ma:4,om:4,qa:4,sa:4,sd:4,tn:4,ye:4		
	};

	var weekendEnd = {/*default is 0=Sunday*/
		ae:5,bh:5,dz:5,iq:5,jo:5,kw:5,lb:5,ly:5,ma:5,om:5,qa:5,sa:5,sd:5,tn:5,ye:5,af:5,ir:5,
		eg:6,il:6,sy:6
	};

	var country = dojo.cldr.supplemental._region(locale);
	var start = weekendStart[country];
	var end = weekendEnd[country];
	if(typeof start == 'undefined'){start=6;}
	if(typeof end == 'undefined'){end=0;}
	return {start:start, end:end}; /*Object {start,end}*/
};

}

if(!dojo._hasResource["dojo.date"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.date"] = true;
dojo.provide("dojo.date");

dojo.date.getDaysInMonth = function(/*Date*/dateObject){
	// summary: returns the number of days in the month used by dateObject
	var month = dateObject.getMonth();
	var days = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
	if(month == 1 && dojo.date.isLeapYear(dateObject)){ return 29; } // Number
	return days[month]; // Number
}

dojo.date.isLeapYear = function(/*Date*/dateObject){
	// summary:
	//	Determines if the year of the dateObject is a leap year
	//
	// description:
	//	Leap years are years with an additional day YYYY-02-29, where the year
	//	number is a multiple of four with the following exception: If a year
	//	is a multiple of 100, then it is only a leap year if it is also a
	//	multiple of 400. For example, 1900 was not a leap year, but 2000 is one.

	var year = dateObject.getFullYear();
	return !(year%400) || (!(year%4) && !!(year%100)); // Boolean
}

// FIXME: This is not localized
dojo.date.getTimezoneName = function(/*Date*/dateObject){
	// summary:
	//	Get the user's time zone as provided by the browser
	//
	// dateObject: needed because the timezone may vary with time (daylight savings)
	//
	// description:
	//	Try to get time zone info from toString or toLocaleString
	//	method of the Date object -- UTC offset is not a time zone.
	//	See http://www.twinsun.com/tz/tz-link.htm
	//	Note: results may be inconsistent across browsers.

	var str = dateObject.toString(); // Start looking in toString
	var tz = ''; // The result -- return empty string if nothing found
	var match;

	// First look for something in parentheses -- fast lookup, no regex
	var pos = str.indexOf('(');
	if(pos > -1){
		tz = str.substring(++pos, str.indexOf(')'));
	}else{
		// If at first you don't succeed ...
		// If IE knows about the TZ, it appears before the year
		// Capital letters or slash before a 4-digit year 
		// at the end of string
		var pat = /([A-Z\/]+) \d{4}$/;
		if((match = str.match(pat))){
			tz = match[1];
		}else{
		// Some browsers (e.g. Safari) glue the TZ on the end
		// of toLocaleString instead of putting it in toString
			str = dateObject.toLocaleString();
			// Capital letters or slash -- end of string, 
			// after space
			pat = / ([A-Z\/]+)$/;
			if((match = str.match(pat))){
				tz = match[1];
			}
		}
	}

	// Make sure it doesn't somehow end up return AM or PM
	return (tz == 'AM' || tz == 'PM') ? '' : tz; // String
}

// Utility methods to do arithmetic calculations with Dates

dojo.date.compare = function(/*Date*/date1, /*Date?*/date2, /*String?*/portion){
	//	summary
	//		Compare two date objects by date, time, or both.
	//
	//  description
	//  	Returns 0 if equal, positive if a > b, else negative.
	//
	//	date1
	//		Date object
	//
	//	date2
	//		Date object.  If not specified, the current Date is used.
	//
	//	portion
	//		A string indicating the "date" or "time" portion of a Date object.  Compares both "date" and "time" by default.
	//		One of the following: "date", "time", "datetime"

	// Extra step required in copy for IE - see #3112
	date1 = new Date(Number(date1));
	date2 = new Date(Number(date2 || new Date()));

	if(typeof portion !== "undefined"){
		if(portion == "date"){
			// Ignore times and compare dates.
			date1.setHours(0, 0, 0, 0);
			date2.setHours(0, 0, 0, 0);
		}else if(portion == "time"){
			// Ignore dates and compare times.
			date1.setFullYear(0, 0, 0);
			date2.setFullYear(0, 0, 0);
		}
	}
	
	if(date1 > date2){ return 1; } // int
	if(date1 < date2){ return -1; } // int
	return 0; // int
};

dojo.date.add = function(/*Date*/date, /*String*/interval, /*int*/amount){
	//	summary
	//		Add to a Date in intervals of different size, from milliseconds to years
	//
	//	date
	//		Date object to start with
	//
	//	interval
	//		A string representing the interval.  One of the following:
	//			"year", "month", "day", "hour", "minute", "second", "millisecond", "quarter", "week", "weekday"
	//
	//	amount
	//		How much to add to the date.

	var sum = new Date(Number(date)); // convert to Number before copying to accomodate IE (#3112)
	var fixOvershoot = false;
	var property = "Date";

	switch(interval){
		case "day":
			break;
		case "weekday":
			//i18n FIXME: assumes Saturday/Sunday weekend, but even this is not standard.  There are CLDR entries to localize this.
			var dayOfMonth = date.getDate();
			var days, weeks;
			var adj = 0;
			// Divide the increment time span into weekspans plus leftover days
			// e.g., 8 days is one 5-day weekspan / and two leftover days
			// Can't have zero leftover days, so numbers divisible by 5 get
			// a days value of 5, and the remaining days make up the number of weeks
			var mod = amount % 5;
			if(!mod){
				days = (amount > 0) ? 5 : -5;
				weeks = (amount > 0) ? ((amount-5)/5) : ((amount+5)/5);
			}else{
				days = mod;
				weeks = parseInt(amount/5);
			}
			// Get weekday value for orig date param
			var strt = date.getDay();
			// Orig date is Sat / positive incrementer
			// Jump over Sun
			if(strt == 6 && amount > 0){
				adj = 1;
			}else if(strt == 0 && amount < 0){
			// Orig date is Sun / negative incrementer
			// Jump back over Sat
				adj = -1;
			}
			// Get weekday val for the new date
			var trgt = strt + days;
			// New date is on Sat or Sun
			if(trgt == 0 || trgt == 6){
				adj = (amount > 0) ? 2 : -2;
			}
			// Increment by number of weeks plus leftover days plus
			// weekend adjustments
			amount = dayOfMonth + 7 * weeks + days + adj;
			break;
		case "year":
			property = "FullYear";
			// Keep increment/decrement from 2/29 out of March
			fixOvershoot = true;
			break;
		case "week":
			amount *= 7;
			break;
		case "quarter":
			// Naive quarter is just three months
			amount *= 3;
			// fallthrough...
		case "month":
			// Reset to last day of month if you overshoot
			fixOvershoot = true;
			property = "Month";
			break;
		case "hour":
		case "minute":
		case "second":
		case "millisecond":
			property = interval.charAt(0).toUpperCase() + interval.substring(1) + "s";
	}

	if(property){
		sum["set"+property](sum["get"+property]()+amount);
	}

	if(fixOvershoot && (sum.getDate() < date.getDate())){
		sum.setDate(0);
	}

	return sum; // Date
};

dojo.date.difference = function(/*Date*/date1, /*Date?*/date2, /*String?*/interval){
	//	summary
	//		Get the difference in a specific unit of time (e.g., number of months, weeks,
	//		days, etc.) between two dates, rounded to the nearest integer.
	//
	//	date1
	//		Date object
	//
	//	date2
	//		Date object.  If not specified, the current Date is used.
	//
	//	interval
	//		A string representing the interval.  One of the following:
	//			"year", "month", "day", "hour", "minute", "second", "millisecond", "quarter", "week", "weekday"
	//		Defaults to "day".

	date2 = date2 || new Date();
	interval = interval || "day";
	var yearDiff = date2.getFullYear() - date1.getFullYear();
	var delta = 1; // Integer return value

	switch(interval){
		case "quarter":
			var m1 = date1.getMonth();
			var m2 = date2.getMonth();
			// Figure out which quarter the months are in
			var q1 = Math.floor(m1/3) + 1;
			var q2 = Math.floor(m2/3) + 1;
			// Add quarters for any year difference between the dates
			q2 += (yearDiff * 4);
			delta = q2 - q1;
			break;
		case "weekday":
			var days = Math.round(dojo.date.difference(date1, date2, "day"));
			var weeks = parseInt(dojo.date.difference(date1, date2, "week"));
			var mod = days % 7;

			// Even number of weeks
			if(mod == 0){
				days = weeks*5;
			}else{
				// Weeks plus spare change (< 7 days)
				var adj = 0;
				var aDay = date1.getDay();
				var bDay = date2.getDay();

				weeks = parseInt(days/7);
				mod = days % 7;
				// Mark the date advanced by the number of
				// round weeks (may be zero)
				var dtMark = new Date(date1);
				dtMark.setDate(dtMark.getDate()+(weeks*7));
				var dayMark = dtMark.getDay();

				// Spare change days -- 6 or less
				if(days > 0){
					switch(true){
						// Range starts on Sat
						case aDay == 6:
							adj = -1;
							break;
						// Range starts on Sun
						case aDay == 0:
							adj = 0;
							break;
						// Range ends on Sat
						case bDay == 6:
							adj = -1;
							break;
						// Range ends on Sun
						case bDay == 0:
							adj = -2;
							break;
						// Range contains weekend
						case (dayMark + mod) > 5:
							adj = -2;
					}
				}else if(days < 0){
					switch(true){
						// Range starts on Sat
						case aDay == 6:
							adj = 0;
							break;
						// Range starts on Sun
						case aDay == 0:
							adj = 1;
							break;
						// Range ends on Sat
						case bDay == 6:
							adj = 2;
							break;
						// Range ends on Sun
						case bDay == 0:
							adj = 1;
							break;
						// Range contains weekend
						case (dayMark + mod) < 0:
							adj = 2;
					}
				}
				days += adj;
				days -= (weeks*2);
			}
			delta = days;
			break;
		case "year":
			delta = yearDiff;
			break;
		case "month":
			delta = (date2.getMonth() - date1.getMonth()) + (yearDiff * 12);
			break;
		case "week":
			// Truncate instead of rounding
			// Don't use Math.floor -- value may be negative
			delta = parseInt(dojo.date.difference(date1, date2, "day")/7);
			break;
		case "day":
			delta /= 24;
			// fallthrough
		case "hour":
			delta /= 60;
			// fallthrough
		case "minute":
			delta /= 60;
			// fallthrough
		case "second":
			delta /= 1000;
			// fallthrough
		case "millisecond":
			delta *= date2.getTime() - date1.getTime();
	}

	// Round for fractional values and DST leaps
	return Math.round(delta); // Number (integer)
};

}

if(!dojo._hasResource["dojo.date.locale"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dojo.date.locale"] = true;
dojo.provide("dojo.date.locale");

// Localization methods for Date.   Honor local customs using locale-dependent dojo.cldr data.







// Load the bundles containing localization information for
// names and formats


//NOTE: Everything in this module assumes Gregorian calendars.
// Other calendars will be implemented in separate modules.

(function(){
	// Format a pattern without literals
	function formatPattern(dateObject, bundle, pattern){
		return pattern.replace(/([a-z])\1*/ig, function(match){
			var s;
			var c = match.charAt(0);
			var l = match.length;
			var pad;
			var widthList = ["abbr", "wide", "narrow"];
			switch(c){
				case 'G':
//					if(l>3){/*unimplemented*/}
					s = bundle.eras[dateObject.getFullYear() < 0 ? 1 : 0];
					break;
				case 'y':
					s = dateObject.getFullYear();
					switch(l){
						case 1:
							break;
						case 2:
							s = String(s); s = s.substr(s.length - 2);
							break;
						default:
							pad = true;
					}
					break;
				case 'Q':
				case 'q':
					s = Math.ceil((dateObject.getMonth()+1)/3);
//					switch(l){
//						case 1: case 2:
							pad = true;
//							break;
//						case 3: case 4: // unimplemented
//					}
					break;
				case 'M':
				case 'L':
					var m = dateObject.getMonth();
					var width;
					switch(l){
						case 1: case 2:
							s = m+1; pad = true;
							break;
						case 3: case 4: case 5:
							width = widthList[l-3];
							break;
					}
					if(width){
						var type = (c == "L") ? "standalone" : "format";
						var prop = ["months",type,width].join("-");
						s = bundle[prop][m];
					}
					break;
				case 'w':
					var firstDay = 0;
					s = dojo.date.locale._getWeekOfYear(dateObject, firstDay); pad = true;
					break;
				case 'd':
					s = dateObject.getDate(); pad = true;
					break;
				case 'D':
					s = dojo.date.locale._getDayOfYear(dateObject); pad = true;
					break;
				case 'E':
				case 'e':
				case 'c': // REVIEW: don't see this in the spec?
					var d = dateObject.getDay();
					var width;
					switch(l){
						case 1: case 2:
							if(c == 'e'){
								var first = dojo.cldr.supplemental.getFirstDayOfWeek(options.locale);
								d = (d-first+7)%7;
							}
							if(c != 'c'){
								s = d+1; pad = true;
								break;
							}
							// else fallthrough...
						case 3: case 4: case 5:
							width = widthList[l-3];
							break;
					}
					if(width){
						var type = (c == "c") ? "standalone" : "format";
						var prop = ["days",type,width].join("-");
						s = bundle[prop][d];
					}
					break;
				case 'a':
					var timePeriod = (dateObject.getHours() < 12) ? 'am' : 'pm';
					s = bundle[timePeriod];
					break;
				case 'h':
				case 'H':
				case 'K':
				case 'k':
					var h = dateObject.getHours();
					// strange choices in the date format make it impossible to write this succinctly
					switch (c) {
						case 'h': // 1-12
							s = (h % 12) || 12;
							break;
						case 'H': // 0-23
							s = h;
							break;
						case 'K': // 0-11
							s = (h % 12);
							break;
						case 'k': // 1-24
							s = h || 24;
							break;
					}
					pad = true;
					break;
				case 'm':
					s = dateObject.getMinutes(); pad = true;
					break;
				case 's':
					s = dateObject.getSeconds(); pad = true;
					break;
				case 'S':
					s = Math.round(dateObject.getMilliseconds() * Math.pow(10, l-3));
					break;
				case 'v': // FIXME: don't know what this is. seems to be same as z?
				case 'z':
					// We only have one timezone to offer; the one from the browser
					s = dojo.date.getTimezoneName(dateObject);
					if(s){break;}
					l=4;
					// fallthrough... use GMT if tz not available
				case 'Z':
					var offset = dateObject.getTimezoneOffset();
					var tz = [
						(offset<=0 ? "+" : "-"),
						dojo.string.pad(Math.floor(Math.abs(offset)/60), 2),
						dojo.string.pad(Math.abs(offset)% 60, 2)
					];
					if(l==4){
						tz.splice(0, 0, "GMT");
						tz.splice(3, 0, ":");
					}
					s = tz.join("");
					break;
//				case 'Y': case 'u': case 'W': case 'F': case 'g': case 'A':
//					console.debug(match+" modifier unimplemented");
				default:
					throw new Error("dojo.date.locale.format: invalid pattern char: "+pattern);
			}
			if(pad){ s = dojo.string.pad(s, l); }
			return s;
		});
	}

dojo.date.locale.format = function(/*Date*/dateObject, /*Object?*/options){
	// summary:
	//		Format a Date object as a String, using locale-specific settings.
	//
	// description:
	//		Create a string from a Date object using a known localized pattern.
	//		By default, this method formats both date and time from dateObject.
	//		Formatting patterns are chosen appropriate to the locale.  Different
	//		formatting lengths may be chosen, with "full" used by default.
	//		Custom patterns may be used or registered with translations using
	//		the addCustomFormats method.
	//		Formatting patterns are implemented using the syntax described at
	//		http://www.unicode.org/reports/tr35/tr35-4.html#Date_Format_Patterns
	//
	// dateObject:
	//		the date and/or time to be formatted.  If a time only is formatted,
	//		the values in the year, month, and day fields are irrelevant.  The
	//		opposite is true when formatting only dates.
	//
	// options: object {selector: string, formatLength: string, datePattern: string, timePattern: string, locale: string}
	//		selector- choice of 'time','date' (default: date and time)
	//		formatLength- choice of long, short, medium or full (plus any custom additions).  Defaults to 'short'
	//		datePattern,timePattern- override pattern with this string
	//		am,pm- override strings for am/pm in times
	//		locale- override the locale used to determine formatting rules

	options = options || {};

	var locale = dojo.i18n.normalizeLocale(options.locale);
	var formatLength = options.formatLength || 'short';
	var bundle = dojo.date.locale._getGregorianBundle(locale);
	var str = [];
	var sauce = dojo.hitch(this, formatPattern, dateObject, bundle);
	if(options.selector == "year"){
		// Special case as this is not yet driven by CLDR data
		var year = dateObject.getFullYear();
		if(locale.match(/^zh|^ja/)){
			year += "\u5E74";
		}
		return year;
	}
	if(options.selector != "time"){
		var datePattern = options.datePattern || bundle["dateFormat-"+formatLength];
		if(datePattern){str.push(_processPattern(datePattern, sauce));}
	}
	if(options.selector != "date"){
		var timePattern = options.timePattern || bundle["timeFormat-"+formatLength];
		if(timePattern){str.push(_processPattern(timePattern, sauce));}
	}
	var result = str.join(" "); //TODO: use locale-specific pattern to assemble date + time
	return result; // String
};

dojo.date.locale.regexp = function(/*Object?*/options){
	// summary:
	//		Builds the regular needed to parse a localized date
	//
	// options: object {selector: string, formatLength: string, datePattern: string, timePattern: string, locale: string, strict: boolean}
	//		selector- choice of 'time', 'date' (default: date and time)
	//		formatLength- choice of long, short, medium or full (plus any custom additions).  Defaults to 'short'
	//		datePattern,timePattern- override pattern with this string
	//		locale- override the locale used to determine formatting rules

	return dojo.date.locale._parseInfo(options).regexp; // String
};

dojo.date.locale._parseInfo = function(/*Object?*/options){
	options = options || {};
	var locale = dojo.i18n.normalizeLocale(options.locale);
	var bundle = dojo.date.locale._getGregorianBundle(locale);
	var formatLength = options.formatLength || 'short';
	var datePattern = options.datePattern || bundle["dateFormat-" + formatLength];
	var timePattern = options.timePattern || bundle["timeFormat-" + formatLength];
	var pattern;
	if(options.selector == 'date'){
		pattern = datePattern;
	}else if(options.selector == 'time'){
		pattern = timePattern;
	}else{
		pattern = datePattern + ' ' + timePattern; //TODO: use locale-specific pattern to assemble date + time
	}

	var tokens = [];
	var re = _processPattern(pattern, dojo.hitch(this, _buildDateTimeRE, tokens, bundle, options));
	return {regexp: re, tokens: tokens, bundle: bundle};
};

dojo.date.locale.parse = function(/*String*/value, /*Object?*/options){
	// summary:
	//		Convert a properly formatted string to a primitive Date object,
	//		using locale-specific settings.
	//
	// description:
	//		Create a Date object from a string using a known localized pattern.
	//		By default, this method parses looking for both date and time in the string.
	//		Formatting patterns are chosen appropriate to the locale.  Different
	//		formatting lengths may be chosen, with "full" used by default.
	//		Custom patterns may be used or registered with translations using
	//		the addCustomFormats method.
	//		Formatting patterns are implemented using the syntax described at
	//		http://www.unicode.org/reports/tr35/#Date_Format_Patterns
	//
	// value:
	//		A string representation of a date
	//
	// options: object {selector: string, formatLength: string, datePattern: string, timePattern: string, locale: string, strict: boolean}
	//		selector- choice of 'time', 'date' (default: date and time)
	//		formatLength- choice of long, short, medium or full (plus any custom additions).  Defaults to 'short'
	//		datePattern,timePattern- override pattern with this string
	//		am,pm- override strings for am/pm in times
	//		locale- override the locale used to determine formatting rules
	//		strict- strict parsing, off by default

	var info = dojo.date.locale._parseInfo(options);
	var tokens = info.tokens, bundle = info.bundle;
	var re = new RegExp("^" + info.regexp + "$");
	var match = re.exec(value);
	if(!match){ return null; } // null

	var widthList = ['abbr', 'wide', 'narrow'];
	//1972 is a leap year.  We want to avoid Feb 29 rolling over into Mar 1,
	//in the cases where the year is parsed after the month and day.
	var result = new Date(1972, 0);
	var expected = {};
	var amPm = "";
	dojo.forEach(match, function(v, i){
		if(!i){return;}
		var token=tokens[i-1];
		var l=token.length;
		switch(token.charAt(0)){
			case 'y':
				if(l != 2){
					//interpret year literally, so '5' would be 5 A.D.
					result.setFullYear(v);
					expected.year = v;
				}else{
					if(v<100){
						v = Number(v);
						//choose century to apply, according to a sliding window
						//of 80 years before and 20 years after present year
						var year = '' + new Date().getFullYear();
						var century = year.substring(0, 2) * 100;
						var yearPart = Number(year.substring(2, 4));
						var cutoff = Math.min(yearPart + 20, 99);
						var num = (v < cutoff) ? century + v : century - 100 + v;
						result.setFullYear(num);
						expected.year = num;
					}else{
						//we expected 2 digits and got more...
						if(options.strict){
							return null;
						}
						//interpret literally, so '150' would be 150 A.D.
						//also tolerate '1950', if 'yyyy' input passed to 'yy' format
						result.setFullYear(v);
						expected.year = v;
					}
				}
				break;
			case 'M':
				if(l>2){
					var months = bundle['months-format-' + widthList[l-3]].concat();
					if(!options.strict){
						//Tolerate abbreviating period in month part
						//Case-insensitive comparison
						v = v.replace(".","").toLowerCase();
						months = dojo.map(months, function(s){ return s.replace(".","").toLowerCase(); } );
					}
					v = dojo.indexOf(months, v);
					if(v == -1){
//						console.debug("dojo.date.locale.parse: Could not parse month name: '" + v + "'.");
						return null;
					}
				}else{
					v--;
				}
				result.setMonth(v);
				expected.month = v;
				break;
			case 'E':
			case 'e':
				var days = bundle['days-format-' + widthList[l-3]].concat();
				if(!options.strict){
					//Case-insensitive comparison
					v = v.toLowerCase();
					days = dojo.map(days, "".toLowerCase);
				}
				v = dojo.indexOf(days, v);
				if(v == -1){
//					console.debug("dojo.date.locale.parse: Could not parse weekday name: '" + v + "'.");
					return null;
				}

				//TODO: not sure what to actually do with this input,
				//in terms of setting something on the Date obj...?
				//without more context, can't affect the actual date
				//TODO: just validate?
				break;
			case 'd':
				result.setDate(v);
				expected.date = v;
				break;
			case 'D':
				//FIXME: need to defer this until after the year is set for leap-year?
				result.setMonth(0);
				result.setDate(v);
				break;
			case 'a': //am/pm
				var am = options.am || bundle.am;
				var pm = options.pm || bundle.pm;
				if(!options.strict){
					var period = /\./g;
					v = v.replace(period,'').toLowerCase();
					am = am.replace(period,'').toLowerCase();
					pm = pm.replace(period,'').toLowerCase();
				}
				if(options.strict && v != am && v != pm){
//					console.debug("dojo.date.locale.parse: Could not parse am/pm part.");
					return null;
				}

				// we might not have seen the hours field yet, so store the state and apply hour change later
				amPm = (v == pm) ? 'p' : (v == am) ? 'a' : '';
				break;
			case 'K': //hour (1-24)
				if(v==24){v=0;}
				// fallthrough...
			case 'h': //hour (1-12)
			case 'H': //hour (0-23)
			case 'k': //hour (0-11)
				//TODO: strict bounds checking, padding
				if(v > 23){
//					console.debug("dojo.date.locale.parse: Illegal hours value");
					return null;
				}

				//in the 12-hour case, adjusting for am/pm requires the 'a' part
				//which could come before or after the hour, so we will adjust later
				result.setHours(v);
				break;
			case 'm': //minutes
				result.setMinutes(v);
				break;
			case 's': //seconds
				result.setSeconds(v);
				break;
			case 'S': //milliseconds
				result.setMilliseconds(v);
//				break;
//			case 'w':
//TODO				var firstDay = 0;
//			default:
//TODO: throw?
//				console.debug("dojo.date.locale.parse: unsupported pattern char=" + token.charAt(0));
		}
	});

	var hours = result.getHours();
	if(amPm === 'p' && hours < 12){
		result.setHours(hours + 12); //e.g., 3pm -> 15
	}else if(amPm === 'a' && hours == 12){
		result.setHours(0); //12am -> 0
	}

	//validate parse date fields versus input date fields
	if(expected.year && result.getFullYear() != expected.year){
//		console.debug("dojo.date.locale.parse: Parsed year: '" + result.getFullYear() + "' did not match input year: '" + expected.year + "'.");
		return null;
	}
	if(expected.month && result.getMonth() != expected.month){
//		console.debug("dojo.date.locale.parse: Parsed month: '" + result.getMonth() + "' did not match input month: '" + expected.month + "'.");
		return null;
	}
	if(expected.date && result.getDate() != expected.date){
//		console.debug("dojo.date.locale.parse: Parsed day of month: '" + result.getDate() + "' did not match input day of month: '" + expected.date + "'.");
		return null;
	}

	//TODO: implement a getWeekday() method in order to test 
	//validity of input strings containing 'EEE' or 'EEEE'...
	return result; // Date
};

function _processPattern(pattern, applyPattern, applyLiteral, applyAll){
	//summary: Process a pattern with literals in it

	// Break up on single quotes, treat every other one as a literal, except '' which becomes '
	var identity = function(x){return x;};
	applyPattern = applyPattern || identity;
	applyLiteral = applyLiteral || identity;
	applyAll = applyAll || identity;

	//split on single quotes (which escape literals in date format strings) 
	//but preserve escaped single quotes (e.g., o''clock)
	var chunks = pattern.match(/(''|[^'])+/g); 
	var literal = false;

	dojo.forEach(chunks, function(chunk, i){
		if(!chunk){
			chunks[i]='';
		}else{
			chunks[i]=(literal ? applyLiteral : applyPattern)(chunk);
			literal = !literal;
		}
	});
	return applyAll(chunks.join(''));
}

function _buildDateTimeRE(tokens, bundle, options, pattern){
	return dojo.regexp.escapeString(pattern).replace(/([a-z])\1*/ig, function(match){
		// Build a simple regexp.  Avoid captures, which would ruin the tokens list
		var s;
		var c = match.charAt(0);
		var l = match.length;
		var p2 = '', p3 = '';
		if(options.strict){
			if(l > 1){ p2 = '0' + '{'+(l-1)+'}'; }
			if(l > 2){ p3 = '0' + '{'+(l-2)+'}'; }
		}else{
			p2 = '0?'; p3 = '0{0,2}';
		}
		switch(c){
			case 'y':
				s = '\\d{2,4}';
				break;
			case 'M':
				s = (l>2) ? '\\S+' : p2+'[1-9]|1[0-2]';
				break;
			case 'D':
				s = p2+'[1-9]|'+p3+'[1-9][0-9]|[12][0-9][0-9]|3[0-5][0-9]|36[0-6]';
				break;
			case 'd':
				s = p2+'[1-9]|[12]\\d|3[01]';
				break;
			case 'w':
				s = p2+'[1-9]|[1-4][0-9]|5[0-3]';
				break;
		    case 'E':
				s = '\\S+';
				break;
			case 'h': //hour (1-12)
				s = p2+'[1-9]|1[0-2]';
				break;
			case 'k': //hour (0-11)
				s = p2+'\\d|1[01]';
				break;
			case 'H': //hour (0-23)
				s = p2+'\\d|1\\d|2[0-3]';
				break;
			case 'K': //hour (1-24)
				s = p2+'[1-9]|1\\d|2[0-4]';
				break;
			case 'm':
			case 's':
				s = '[0-5]\\d';
				break;
			case 'S':
				s = '\\d{'+l+'}';
				break;
			case 'a':
				var am = options.am || bundle.am || 'AM';
				var pm = options.pm || bundle.pm || 'PM';
				if(options.strict){
					s = am + '|' + pm;
				}else{
					s = am + '|' + pm;
					if(am != am.toLowerCase()){ s += '|' + am.toLowerCase(); }
					if(pm != pm.toLowerCase()){ s += '|' + pm.toLowerCase(); }
				}
				break;
			default:
			// case 'v':
			// case 'z':
			// case 'Z':
				s = ".*";
//				console.debug("parse of date format, pattern=" + pattern);
		}

		if(tokens){ tokens.push(match); }

		return "(" + s + ")"; // add capture
	}).replace(/[\xa0 ]/g, "[\\s\\xa0]"); // normalize whitespace.  Need explicit handling of \xa0 for IE.
}
})();

(function(){
var _customFormats = [];
dojo.date.locale.addCustomFormats = function(/*String*/packageName, /*String*/bundleName){
	// summary:
	//		Add a reference to a bundle containing localized custom formats to be
	//		used by date/time formatting and parsing routines.
	//
	// description:
	//		The user may add custom localized formats where the bundle has properties following the
	//		same naming convention used by dojo for the CLDR data: dateFormat-xxxx / timeFormat-xxxx
	//		The pattern string should match the format used by the CLDR.
	//		See dojo.date.format for details.
	//		The resources must be loaded by dojo.requireLocalization() prior to use

	_customFormats.push({pkg:packageName,name:bundleName});
};

dojo.date.locale._getGregorianBundle = function(/*String*/locale){
	var gregorian = {};
	dojo.forEach(_customFormats, function(desc){
		var bundle = dojo.i18n.getLocalization(desc.pkg, desc.name, locale);
		gregorian = dojo.mixin(gregorian, bundle);
	}, this);
	return gregorian; /*Object*/
};
})();

dojo.date.locale.addCustomFormats("dojo.cldr","gregorian");

dojo.date.locale.getNames = function(/*String*/item, /*String*/type, /*String?*/use, /*String?*/locale){
	// summary:
	//		Used to get localized strings from dojo.cldr for day or month names.
	//
	// item: 'months' || 'days'
	// type: 'wide' || 'narrow' || 'abbr' (e.g. "Monday", "Mon", or "M" respectively, in English)
	// use: 'standAlone' || 'format' (default)
	// locale: override locale used to find the names

	var label;
	var lookup = dojo.date.locale._getGregorianBundle(locale);
	var props = [item, use, type];
	if(use == 'standAlone'){
		label = lookup[props.join('-')];
	}
	props[1] = 'format';

	// return by copy so changes won't be made accidentally to the in-memory model
	return (label || lookup[props.join('-')]).concat(); /*Array*/
};

dojo.date.locale.isWeekend = function(/*Date?*/dateObject, /*String?*/locale){
	// summary:
	//	Determines if the date falls on a weekend, according to local custom.

	var weekend = dojo.cldr.supplemental.getWeekend(locale);
	var day = (dateObject || new Date()).getDay();
	if(weekend.end < weekend.start){
		weekend.end += 7;
		if(day < weekend.start){ day += 7; }
	}
	return day >= weekend.start && day <= weekend.end; // Boolean
};

// These are used only by format and strftime.  Do they need to be public?  Which module should they go in?

dojo.date.locale._getDayOfYear = function(/*Date*/dateObject){
	// summary: gets the day of the year as represented by dateObject
	return dojo.date.difference(new Date(dateObject.getFullYear(), 0, 1), dateObject) + 1; // Number
};

dojo.date.locale._getWeekOfYear = function(/*Date*/dateObject, /*Number*/firstDayOfWeek){
	if(arguments.length == 1){ firstDayOfWeek = 0; } // Sunday

	var firstDayOfYear = new Date(dateObject.getFullYear(), 0, 1).getDay();
	var adj = (firstDayOfYear - firstDayOfWeek + 7) % 7;
	var week = Math.floor((dojo.date.locale._getDayOfYear(dateObject) + adj - 1) / 7);

	// if year starts on the specified day, start counting weeks at 1
	if(firstDayOfYear == firstDayOfWeek){ week++; }

	return week; // Number
};

}

if(!dojo._hasResource["dijit._Calendar"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit._Calendar"] = true;
dojo.provide("dijit._Calendar");








dojo.declare(
	"dijit._Calendar",
	[dijit._Widget, dijit._Templated],
	{
		/*
		summary:
			A simple GUI for choosing a date in the context of a monthly calendar.

		description:
			This widget is used internally by other widgets and is not accessible
			as a standalone widget.
			This widget can't be used in a form because it doesn't serialize the date to an
			<input> field.  For a form element, use DateTextBox instead.

			Note that the parser takes all dates attributes passed in the `RFC 3339` format:
			http://www.faqs.org/rfcs/rfc3339.html (2005-06-30T08:05:00-07:00)
			so that they are serializable and locale-independent.

		usage:
			var calendar = new dijit._Calendar({}, dojo.byId("calendarNode"));
		 	-or-
			<div dojoType="dijit._Calendar"></div>
		*/
		templateString:"<table cellspacing=\"0\" cellpadding=\"0\" class=\"dijitCalendarContainer\">\n\t<thead>\n\t\t<tr class=\"dijitReset dijitCalendarMonthContainer\" valign=\"top\">\n\t\t\t<th class='dijitReset' dojoAttachEvent=\"onclick: _onDecrementMonth\">\n\t\t\t\t<span class=\"dijitInline dijitCalendarIncrementControl dijitCalendarDecrease\"><span dojoAttachPoint=\"decreaseArrowNode\" class=\"dijitA11ySideArrow dijitCalendarIncrementControl dijitCalendarDecreaseInner\">-</span></span>\n\t\t\t</th>\n\t\t\t<th class='dijitReset' colspan=\"5\">\n\t\t\t\t<div dojoAttachPoint=\"monthLabelSpacer\" class=\"dijitCalendarMonthLabelSpacer\"></div>\n\t\t\t\t<div dojoAttachPoint=\"monthLabelNode\" class=\"dijitCalendarMonth\"></div>\n\t\t\t</th>\n\t\t\t<th class='dijitReset' dojoAttachEvent=\"onclick: _onIncrementMonth\">\n\t\t\t\t<div class=\"dijitInline dijitCalendarIncrementControl dijitCalendarIncrease\"><span dojoAttachPoint=\"increaseArrowNode\" class=\"dijitA11ySideArrow dijitCalendarIncrementControl dijitCalendarIncreaseInner\">+</span></div>\n\t\t\t</th>\n\t\t</tr>\n\t\t<tr>\n\t\t\t<th class=\"dijitReset dijitCalendarDayLabelTemplate\"><span class=\"dijitCalendarDayLabel\"></span></th>\n\t\t</tr>\n\t</thead>\n\t<tbody dojoAttachEvent=\"onclick: _onDayClick\" class=\"dijitReset dijitCalendarBodyContainer\">\n\t\t<tr class=\"dijitReset dijitCalendarWeekTemplate\">\n\t\t\t<td class=\"dijitReset dijitCalendarDateTemplate\"><span class=\"dijitCalendarDateLabel\"></span></td>\n\t\t</tr>\n\t</tbody>\n\t<tfoot class=\"dijitReset dijitCalendarYearContainer\">\n\t\t<tr>\n\t\t\t<td class='dijitReset' valign=\"top\" colspan=\"7\">\n\t\t\t\t<h3 class=\"dijitCalendarYearLabel\">\n\t\t\t\t\t<span dojoAttachPoint=\"previousYearLabelNode\"\n\t\t\t\t\t\tdojoAttachEvent=\"onclick: _onDecrementYear\" class=\"dijitInline dijitCalendarPreviousYear\"></span>\n\t\t\t\t\t<span dojoAttachPoint=\"currentYearLabelNode\" class=\"dijitInline dijitCalendarSelectedYear\"></span>\n\t\t\t\t\t<span dojoAttachPoint=\"nextYearLabelNode\"\n\t\t\t\t\t\tdojoAttachEvent=\"onclick: _onIncrementYear\" class=\"dijitInline dijitCalendarNextYear\"></span>\n\t\t\t\t</h3>\n\t\t\t</td>\n\t\t</tr>\n\t</tfoot>\n</table>\t\n",

		// value: Date
		// the currently selected Date
		value: new Date(),

		// dayWidth: String
		// How to represent the days of the week in the calendar header. See dojo.date.locale
		dayWidth: "narrow",

		setValue: function(/*Date*/ value){
			// summary: set the current date and update the UI.  If the date is disabled, the selection will
			//	not change, but the display will change to the corresponding month.
			if(!this.value || dojo.date.compare(value, this.value)){
				value = new Date(value);
				this.displayMonth = new Date(value);
				if(!this.isDisabledDate(value, this.lang)){
					this.value = value;
					this.value.setHours(0,0,0,0);
					this.onChange(this.value);
				}
				this._populateGrid();
			}
		},

		_setText: function(node, text){
			while(node.firstChild){
				node.removeChild(node.firstChild);
			}
			node.appendChild(document.createTextNode(text));
		},

		_populateGrid: function(){
			var month = this.displayMonth;
			month.setDate(1);
			var firstDay = month.getDay();
			var daysInMonth = dojo.date.getDaysInMonth(month);
			var daysInPreviousMonth = dojo.date.getDaysInMonth(dojo.date.add(month, "month", -1));
			var today = new Date();
			var selected = this.value;

			var dayOffset = dojo.cldr.supplemental.getFirstDayOfWeek(this.lang);
			if(dayOffset > firstDay){ dayOffset -= 7; }

			// Iterate through dates in the calendar and fill in date numbers and style info
			dojo.query(".dijitCalendarDateTemplate", this.domNode).forEach(function(template, i){
				i += dayOffset;
				var date = new Date(month);
				var number, clazz = "dijitCalendar", adj = 0;

				if(i < firstDay){
					number = daysInPreviousMonth - firstDay + i + 1;
					adj = -1;
					clazz += "Previous";
				}else if(i >= (firstDay + daysInMonth)){
					number = i - firstDay - daysInMonth + 1;
					adj = 1;
					clazz += "Next";
				}else{
					number = i - firstDay + 1;
					clazz += "Current";
				}

				if(adj){
					date = dojo.date.add(date, "month", adj);
				}
				date.setDate(number);

				if(!dojo.date.compare(date, today, "date")){
					clazz = "dijitCalendarCurrentDate " + clazz;
				}

				if(!dojo.date.compare(date, selected, "date")){
					clazz = "dijitCalendarSelectedDate " + clazz;
				}

				if(this.isDisabledDate(date, this.lang)){
					clazz = "dijitCalendarDisabledDate " + clazz;
				}

				template.className =  clazz + "Month dijitCalendarDateTemplate";
				template.dijitDateValue = date.valueOf();
				var label = dojo.query(".dijitCalendarDateLabel", template)[0];
				this._setText(label, date.getDate());
			}, this);

			// Fill in localized month name
			var monthNames = dojo.date.locale.getNames('months', 'wide', 'standAlone', this.lang);
			this._setText(this.monthLabelNode, monthNames[month.getMonth()]);

			// Fill in localized prev/current/next years
			var y = month.getFullYear() - 1;
			dojo.forEach(["previous", "current", "next"], function(name){
				this._setText(this[name+"YearLabelNode"],
					dojo.date.locale.format(new Date(y++, 0), {selector:'year', locale:this.lang}));
			}, this);
		},

		postCreate: function(){
			dijit._Calendar.superclass.postCreate.apply(this);

			var cloneClass = dojo.hitch(this, function(clazz, n){
				var template = dojo.query(clazz, this.domNode)[0];
	 			for(var i=0; i<n; i++){
					template.parentNode.appendChild(template.cloneNode(true));
				}
			});

			// clone the day label and calendar day templates 6 times to make 7 columns
			cloneClass(".dijitCalendarDayLabelTemplate", 6);
			cloneClass(".dijitCalendarDateTemplate", 6);

			// now make 6 week rows
			cloneClass(".dijitCalendarWeekTemplate", 5);

			// insert localized day names in the header
			var dayNames = dojo.date.locale.getNames('days', this.dayWidth, 'standAlone', this.lang);
			var dayOffset = dojo.cldr.supplemental.getFirstDayOfWeek(this.lang);
			dojo.query(".dijitCalendarDayLabel", this.domNode).forEach(function(label, i){
				this._setText(label, dayNames[(i + dayOffset) % 7]);
			}, this);

			// Fill in spacer element with all the month names (invisible) so that the maximum width will affect layout
			var monthNames = dojo.date.locale.getNames('months', 'wide', 'standAlone', this.lang);
			dojo.forEach(monthNames, function(name){
				var monthSpacer = dojo.doc.createElement("div");
				this._setText(monthSpacer, name);
				this.monthLabelSpacer.appendChild(monthSpacer);
			}, this);

			this.value = null;
			this.setValue(new Date());
		},

		_adjustDate: function(/*String*/part, /*int*/amount){
			this.displayMonth = dojo.date.add(this.displayMonth, part, amount);
			this._populateGrid();
		},

		_onIncrementMonth: function(/*Event*/evt){
			// summary: handler for increment month event
			evt.stopPropagation();
			this._adjustDate("month", 1);
		},

		_onDecrementMonth: function(/*Event*/evt){
			// summary: handler for increment month event
			evt.stopPropagation();
			this._adjustDate("month", -1);
		},

		_onIncrementYear: function(/*Event*/evt){
			// summary: handler for increment year event
			evt.stopPropagation();
			this._adjustDate("year", 1);
		},

		_onDecrementYear: function(/*Event*/evt){
			// summary: handler for increment year event
			evt.stopPropagation();
			this._adjustDate("year", -1);
		},

		_onDayClick: function(/*Event*/evt){
			var node = evt.target;
			dojo.stopEvent(evt);
			while(!node.dijitDateValue){
				node = node.parentNode;
			}
			if(!dojo.hasClass(node, "dijitCalendarDisabledDate")){
				this.setValue(node.dijitDateValue);
				this.onValueSelected(this.value);
			}
		},

		onValueSelected: function(/*Date*/date){
			//summary: a date cell was selected.  It may be the same as the previous value.
		},

		onChange: function(/*Date*/date){
			//summary: called only when the selected date has changed
		},

		isDisabledDate: function(/*Date*/dateObject, /*String?*/locale){
			// summary:
			//	May be overridden to disable certain dates in the calendar e.g. isDisabledDate=dojo.date.locale.isWeekend
			return false; // Boolean
		}
	}
);

}

if(!dojo._hasResource["dijit.form._TimePicker"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.form._TimePicker"] = true;
dojo.provide("dijit.form._TimePicker");




dojo.declare("dijit.form._TimePicker",
	[dijit._Widget, dijit._Templated],
	{
		// summary:
		// A graphical time picker that TimeTextBox pops up
		// It is functionally modeled after the Java applet at http://java.arcadevillage.com/applets/timepica.htm
		// See ticket #599

		templateString:"<fieldset id=\"widget_${id}\" baseClass=\"dijitTimePicker\" class=\"dijitMenu\"\n><div dojoAttachPoint=\"upArrow\" class=\"dijitButtonNode\">&#9650;</div\n><div dojoAttachPoint=\"timeMenu\" dojoAttachEvent=\"onclick:_onOptionSelected,onmouseover,onmouseout\"\n></div\n><div dojoAttachPoint=\"downArrow\" class=\"dijitButtonNode\">&#9660;</div\n></fieldset>\n",

		// clickableIncrement: String
		//		ISO-8601 string representing the amount by which 
		//		every clickable element in the time picker increases
		//		Set in non-Zulu time, without a time zone
		//		Example: "T00:15:00" creates 15 minute increments
		//		Must divide visibleIncrement evenly
		clickableIncrement: "T00:15:00",

		// visibleIncrement: String
		//		ISO-8601 string representing the amount by which 
		//		every element with a visible time in the time picker increases
		//		Set in non Zulu time, without a time zone
		//		Example: "T01:00:00" creates text in every 1 hour increment
		visibleIncrement: "T01:00:00",

		// visibleRange: String
		//		ISO-8601 string representing the range of this TimePicker
		//		The TimePicker will only display times in this range
		//		Example: "T05:00:00" displays 5 hours of options
		visibleRange: "T05:00:00",

		// value: String
		//		Date to display.
		//		Defaults to current time and date.
		//		Can be a Date object or an ISO-8601 string
		//		If you specify the GMT time zone ("-01:00"), 
		//		the time will be converted to the local time in the local time zone.
		//		Otherwise, the time is considered to be in the local time zone.
		//		If you specify the date and isDate is true, the date is used.
		//		Example: if your local time zone is GMT -05:00, 
		//		"T10:00:00" becomes "T10:00:00-05:00" (considered to be local time), 
		//		"T10:00:00-01:00" becomes "T06:00:00-05:00" (4 hour difference),
		//		"T10:00:00Z" becomes "T05:00:00-05:00" (5 hour difference between Zulu and local time)
		//		"yyyy-mm-ddThh:mm:ss" is the format to set the date and time
		//		Example: "2007-06-01T09:00:00"
		value: new Date(),

		_refdate: null,
		_visibleIncrement:2,
		_clickableIncrement:1,
		_totalIncrements:10,
		constraints:{},

		serialize: dojo.date.stamp.toISOString,

		setValue:function(/*Date*/ date, /*Boolean*/ priority){
			// summary:
			//	Set the value of the TimePicker
			//	Redraws the TimePicker around the new date
			//dijit.form._TimePicker.superclass.setValue.apply(this, arguments);
			this.value=date;
			this._showText();
		},

		isDisabledDate: function(/*Date*/dateObject, /*String?*/locale){
			// summary:
			//	May be overridden to disable certain dates in the TimePicker e.g. isDisabledDate=dojo.date.locale.isWeekend
			return false; // Boolean
		},

		_showText:function(){
			this.timeMenu.innerHTML="";
			var fromIso = dojo.date.stamp.fromISOString;
			this._clickableIncrementDate=fromIso(this.clickableIncrement);
			this._visibleIncrementDate=fromIso(this.visibleIncrement);
			this._visibleRangeDate=fromIso(this.visibleRange);
			// get the value of the increments and the range in seconds (since 00:00:00) to find out how many divs to create
			var clickableIncrementSeconds=this._toSeconds(this._clickableIncrementDate);
			var visibleIncrementSeconds=this._toSeconds(this._visibleIncrementDate);
			var visibleRangeSeconds=this._toSeconds(this._visibleRangeDate);

			// round reference date to previous visible increment
			this._refdate=this._roundTime(this.value, visibleIncrementSeconds);

			// assume clickable increment is the smallest unit
			this._clickableIncrement=1;
			// divide the visible range by the clickable increment to get the number of divs to create
			// example: 10:00:00/00:15:00 -> display 40 divs
			this._totalIncrements=visibleRangeSeconds/clickableIncrementSeconds;
			// divide the visible increments by the clickable increments to get how often to display the time inline
			// example: 01:00:00/00:15:00 -> display the time every 4 divs
			this._visibleIncrement=visibleIncrementSeconds/clickableIncrementSeconds;
			for(var i=-this._totalIncrements/2; i<=this._totalIncrements/2; i+=this._clickableIncrement){
				var div=this._createOption(i);
				this.timeMenu.appendChild(div);
			}
		},

		postCreate:function(){
			// instantiate constraints
			if(this.constraints===dijit.form._TimePicker.prototype.constraints){
				this.constraints={};
			}
			// dojo.date.locale needs the lang in the constraints as locale
			if(!this.constraints.locale){
				this.constraints.locale=this.lang;
			}
			// assign typematic mouse listeners to the arrow buttons
			dijit.typematic.addMouseListener(this.upArrow,this,this._onArrowUp, 0.8, 500);
			dijit.typematic.addMouseListener(this.downArrow,this,this._onArrowDown, 0.8, 500);
			dijit.form._TimePicker.superclass.postCreate.apply(this, arguments);
			this.setValue(this.value);
		},

		_roundTime:function(/*Date*/ date, incrementSeconds){
			// summary:
			//	Return a time that is nearest to the previous clickable increment, as defined by:
			// new time=old time - oldtime%clickableincrement

			// find the new time in seconds
			var oldtime=this._toSeconds(date);
			var newtime=oldtime-oldtime%incrementSeconds;
			// convert back to a date
			var newdate=new Date();
			newdate.setYear(date.getFullYear());
			newdate.setMonth(date.getMonth());
			newdate.setDate(date.getDate());
			newdate.setHours(0);
			newdate.setMinutes(0);
			newdate.setSeconds(newtime);
			console.debug("Time was: "+date+". Rounding UI to: "+newdate);
			return newdate;
		},

		_toSeconds:function(/*Date*/ date){
			// summmary:
			//	Convert a date time to the number of seconds since 00:00:00
			return date.getHours()*60*60+date.getMinutes()*60+date.getSeconds();
		},

		_createOption:function(/*Number*/ index){
			// summary:
			// creates a clickable time option
			var div=document.createElement("div");
			div.date=new Date(this._refdate);
			div.index=index;
			div.date.setSeconds(div.date.getSeconds()+this._clickableIncrementDate.getSeconds()*index);
			div.date.setMinutes(div.date.getMinutes()+this._clickableIncrementDate.getMinutes()*index);
			div.date.setHours(div.date.getHours()+this._clickableIncrementDate.getHours()*index);
			if(index%this._visibleIncrement<1&&index%this._visibleIncrement>-1){
				div.innerHTML=dojo.date.locale.format(div.date, this.constraints);
				dojo.addClass(div, "dijitTimePickerItem");
			}else if(index%this._clickableIncrement==0){
				div.innerHTML="&nbsp;"
				dojo.addClass(div, "dijitTimePickerItemSmall");
			}
			if(this.isDisabledDate(div.date)){
				// set disabled
				dojo.addClass(div, "dijitTimePickerItemDisabled");
			}
			return div;
		},

		_onOptionSelected:function(/*Object*/ tgt){
			if(!tgt.target.date||this.isDisabledDate(tgt.target.date)){return;}
			this.setValue(tgt.target.date);
			this.onValueSelected(tgt.target.date);
		},
		
		onValueSelected:function(value){
		},
		
		onmouseover:function(/*Event*/ evt){
			if(evt.target === this.timeMenu){ return; }
			this._highlighted_option=evt.target;
			dojo.addClass(evt.target, "dijitMenuItemHover");
		},

		onmouseout:function(/*Event*/ evt){
			if(evt.target === this.timeMenu||this._highlighted_option==null){ return; }
			dojo.removeClass(this._highlighted_option, "dijitMenuItemHover");
		},

		_onArrowUp:function(){
			// remove the bottom time and add one to the top
			// TODO: Typematic
			var index=this.timeMenu.childNodes[0].index-1;
			var div=this._createOption(index);
			this.timeMenu.removeChild(this.timeMenu.childNodes[this.timeMenu.childNodes.length-1]);
			this.timeMenu.insertBefore(div, this.timeMenu.childNodes[0]);
		},

		_onArrowDown:function(){
			// remove the top time and add one to the bottom
			// TODO: Typematic
			var index=this.timeMenu.childNodes[this.timeMenu.childNodes.length-1].index+1;
			var div=this._createOption(index);
			this.timeMenu.removeChild(this.timeMenu.childNodes[0]);
			this.timeMenu.appendChild(div);
		}
	}
);

}

if(!dojo._hasResource["dijit.form.TimeTextBox"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.form.TimeTextBox"] = true;
dojo.provide("dijit.form.TimeTextBox");







dojo.declare(
	"dijit.form.TimeTextBox",
	dijit.form.RangeBoundTextBox,
	{
		// summary:
		//		A validating, serializable, range-bound date text box.

		// constraints object: min, max
		regExpGen: dojo.date.locale.regexp,
		compare: dojo.date.compare,
		format: function(/*Date*/ value, /*Object*/ constraints){
			if(!value || value.toString() == this._invalid){ return null; }
			return dojo.date.locale.format(value, constraints);
		},
		parse: dojo.date.locale.parse,
		serialize: dojo.date.stamp.toISOString,

		value: new Date(""),	// NaN
		_invalid: (new Date("")).toString(),	// NaN

		_popupClass: "dijit.form._TimePicker",

		postMixInProperties: function(){
			dijit.form.RangeBoundTextBox.prototype.postMixInProperties.apply(this, arguments);

			var constraints = this.constraints;
			constraints.selector = 'time';
			if(typeof constraints.min == "string"){ constraints.min = dojo.date.stamp.fromISOString(constraints.min); }
 			if(typeof constraints.max == "string"){ constraints.max = dojo.date.stamp.fromISOString(constraints.max); }
		},

		_onFocus: function(/*Event*/ evt){
			// open the calendar
			this._open();
		},

		setValue: function(/*Date*/ value, /*Boolean, optional*/ priorityChange){
			// summary:
			//	Sets the date on this textbox
			this.inherited('setValue', arguments);
			if(this._picker){
				// #3948: fix blank date on popup only
				if(!value || value.toString() == this._invalid){value=new Date();}
				this._picker.setValue(value);
			}
		},

		_open: function(){
			// summary:
			//	opens the Calendar, and sets the onValueSelected for the Calendar
			var self = this;

			if(!this._picker){
				var popupProto=dojo.getObject(this._popupClass, false);
				this._picker = new popupProto({
					onValueSelected: function(value){

						self.focus(); // focus the textbox before the popup closes to avoid reopening the popup
						setTimeout(dijit.popup.close, 1); // allow focus time to take

						// this will cause InlineEditBox and other handlers to do stuff so make sure it's last
						dijit.form.TimeTextBox.superclass.setValue.call(self, value, true);
					},
					lang: this.lang,
					constraints:this.constraints,
					isDisabledDate: function(/*Date*/ date){
						// summary:
						// 	disables dates outside of the min/max of the TimeTextBox
						return self.constraints && (dojo.date.compare(self.constraints.min,date) > 0 || dojo.date.compare(self.constraints.max,date) < 0);
					}
				});
				this._picker.setValue(this.getValue() || new Date());
			}
			if(!this._opened){
				dijit.popup.open({
					parent: this,
					popup: this._picker,
					around: this.domNode,
					onClose: function(){ self._opened=false; }
				});
				this._opened=true;
			}
		},

		_onBlur: function(){
			// summary: called magically when focus has shifted away from this widget and it's dropdown
			dijit.popup.closeAll();
			this.inherited('_onBlur', arguments);
			// don't focus on <input>.  the user has explicitly focused on something else.
		},

		getDisplayedValue:function(){
			return this.textbox.value;
		},

		setDisplayedValue:function(/*String*/ value){
			this.textbox.value=value;
		}
	}
);

}

if(!dojo._hasResource["dijit.form.DateTextBox"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.form.DateTextBox"] = true;
dojo.provide("dijit.form.DateTextBox");




dojo.declare(
	"dijit.form.DateTextBox",
	dijit.form.TimeTextBox,
	{
		// summary:
		//		A validating, serializable, range-bound date text box.
		
		_popupClass: "dijit._Calendar",
		
		postMixInProperties: function(){
			this.inherited('postMixInProperties', arguments);
			this.constraints.selector = 'date';
		}
	}
);

}

if(!dojo._hasResource["dijit.dijit"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["dijit.dijit"] = true;
dojo.provide("dijit.dijit");

// All the stuff in _base (these are the function that are guaranteed available without an explicit dojo.require)


// And some other stuff that we tend to pull in all the time anyway







}


dojo.i18n._preloadLocalizations("dojo.dojo.nls.commonWidgets", ["ROOT", "es-es", "es", "it-it", "pt-br", "de", "fr-fr", "zh-cn", "pt", "en-us", "zh", "fr", "zh-tw", "it", "en-gb", "xx", "de-de", "ko-kr", "ja-jp", "ko", "en", "ja"]);
