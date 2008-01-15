if(!dojo._hasResource["civicrm.Tooltip"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["civicrm.Tooltip"] = true;
// Original Author: Nilesh Bansal
// Blog: http://nileshbansal.blogspot.com/
// modified for civicrm - kurund

dojo.provide("civicrm.Tooltip");
dojo.require("dijit.Tooltip");
dojo.require("dijit.Dialog");
dojo.require("dojo.parser");
dojo.require("dijit.layout.TabContainer");
dojo.require("dijit.form.TextBox");
dojo.require("dijit.form.Button");

// Adjusts the dimension of the supplied node so that it does not exceeds
// the max width and height
function adjustDimensions(/*DomNode*/node, /*int*/maxWidth, /*int*/maxHeight) {
    var sHeight = node.scrollHeight;
    var sWidth = node.scrollWidth;
    console.log("height and width are "+sHeight+" and "+sWidth+" for "+node+" with id "+node.id);
    // console.log("max height and width are "+maxHeight+" and "+maxWidth);
    if (sHeight > maxHeight) {
            console.log("resizing to max height "+maxHeight);
            node.style.height = maxHeight + "px";
            node.style.overflow = "auto";
    }
    if (sWidth > maxWidth) {
            console.log("resizing to max width "+maxWidth);
            node.style.width = maxWidth + "px";
            node.style.overflow = "auto";
    }
}

// gets the first child of the node which is a DIV
function getFirstDivChild(/*DomNode*/node) {
    var divChild = null;
    dojo.forEach(node.childNodes,
        function (child) {
            if (child.nodeName == "DIV") {
                if (divChild == null) {
                    divChild = child;
                }
            }
        });
    return divChild;
}

// This is a list of functions that need to be executed once the custom widget
// civicrm.Tooltip loads
var civicrmLoaders = [];
// This variable is set to true after civicrm.Tooltip declaration finishes loading
var civicrmTooltipLoaded = false;

// This function is similar to dojo.addOnLoad, but it ensures that the 
// custom civicrm.Tooltip widget is loaded before the function call is made
civicrmAddOnLoad = function (/*Function*/ f) {
    if (civicrmTooltipLoaded) {
        console.log("Calling the function", f);
        f();
    } else {
        console.log("Pushing the function to queue", f);
        civicrmLoaders.push(f);
    }
}

dojo.addOnLoad( function() { console.log("Will load the civicrm.Tooltip declaration now"); } );

dojo.addOnLoad( function() {
    dojo.provide("civicrm.Tooltip");
    dojo.provide("civicrm.MasterTooltip");
    // A lot of code here was originaly copied from Tooltip.js in dojo source code, and then modified.
    // Refer to dijit/Tooltip.js for more documentation and explanation
    dojo.declare(
        // Each tooltip has a master tooltip which contains the actual content
        "civicrm.MasterTooltip",
        dijit._MasterTooltip,
        {
            // the fade in fade out duration
            duration: 50,
            parentTooltip: null,
            templateString: "<div id=\"dijitTooltip\" class=\"dijitTooltip dijitTooltipBelow\">\n\t<div class=\"dijitTooltipContainer dijitTooltipContents civicrmTooltipContainer\" id=\"dijitTooltipContainer\" dojoAttachPoint=\"containerNode\" waiRole='alert'></div>\n\t<div class=\"dijitTooltipConnector civicrmTooltipConnector\"></div>\n</div>\n",
            show: function(/*DomNode*/ aroundNode){
                if(this.fadeOut.status() == "playing"){
                    // previous tooltip is being hidden; 
                    // wait until the hide completes then show new one
                    this._onDeck=arguments;
                    return;
                }
                this.containerNode.innerHTML=this.parentTooltip.label;
                // Firefox bug. when innerHTML changes to be shorter than previous
                // one, the node size will not be updated until it moves.
                this.domNode.style.top = (this.domNode.offsetTop + 1) + "px";
    
                var align = {'BL' : 'TL'};
                var pos = dijit.placeOnScreen(this.domNode, {x: this.parentTooltip.mouseX, y: this.parentTooltip.mouseY}, ["TL"]);
                this.domNode.className="dijitTooltip dijitTooltipBelow";
    
                // show it
                dojo.style(this.domNode, "opacity", 0);
                this.fadeIn.play();
                this.isShowingNow = true;
                // In our template string, we have 3 div nodes nested in one another
                // for asthetic reasons, we chose to adjust dimension of the innermost DIV
                var adjustNode = getFirstDivChild(getFirstDivChild(this.domNode));
                adjustDimensions(adjustNode, this.parentTooltip.maxWidth, this.parentTooltip.maxHeight);
            },
            // refresh the contents of the tooltip
            refresh: function() {
                this.containerNode.innerHTML=this.parentTooltip.label;
                if (this.isShowingNow == true) {
                    var adjustNode = getFirstDivChild(getFirstDivChild(this.domNode));
                    adjustDimensions(adjustNode, this.parentTooltip.maxWidth, this.parentTooltip.maxHeight);
                }
            },
            // called once after creation of the widget
            postCreate: function(){
                dojo.body().appendChild(this.domNode);
                this.bgIframe = new dijit.BackgroundIframe(this.domNode);
                // I wanted to set a unique id for the domNode, but getUniqueId does not work with IE6
                // If the line below is uncommented, it prints error in IE6
                // this.domNode.id = dijit.getUniqueId(this.declaredClass.replace(/\./g,"_"));
                // Setup fade-in and fade-out functions.
                this.fadeIn = dojo.fadeIn({ node: this.domNode, duration: this.duration, onEnd: dojo.hitch(this, "_onShow") });
                this.fadeOut = dojo.fadeOut({ node: this.domNode, duration: this.duration, onEnd: dojo.hitch(this, "_onHide") });
                // connect the event of mouse moving out
                this.connect(this.domNode, "onmouseout", "_onMouseOut");
            },
            // when mouse moves out
            _onMouseOut: function(/*Event*/ e){
                this.parentTooltip._onMouseOut(e);
            }
        }
    );
    // civicrm.Tooltip is the main widget that we will use
    dojo.declare(
        "civicrm.Tooltip",
        dijit.Tooltip,
        {
            // false if the contents are yet to be loaded from the HTTP request
            hasLoaded: false,
            // location from where to fetch the contents
            href: "",
            // max height and width of the tooltip
            maxWidth: 400,
            maxHeight: 100,
            // the position of mouse when the tooltip is created
            mouseX: 0,
            mouseY: 0,
            // contents to diplay in the tooltip. Initialized to a loading icon.
            label: "<div><img src=\"loading.gif\"> Loading...</div>",
            masterTooltip: null,
            loadContent: function() { 
                if (this.hasLoaded == false) {
                    this.hasLoaded = true;
                    dojo.xhrGet({
                        url: this.href,
                        parentTooltip: this,
                        load: function(response, ioArgs){
                            console.log("data received from ", this.url);
                            console.log("parent is "+this.parentTooltip);
                            this.parentTooltip.label = response;
                            if(this.parentTooltip.isShowingNow){
                                this.parentTooltip.masterTooltip.refresh();
                            }
                        },
                        handleAs: "text"
                    }); 
                }
            },
            open: function(){
                if (this.masterTooltip == null) {
                    // initialized the master tooltip
                    this.masterTooltip = new civicrm.MasterTooltip({parentTooltip: this});
                }
                this.loadContent();
                if(this.isShowingNow){ return; }
                if(this._showTimer){
                    clearTimeout(this._showTimer);
                    delete this._showTimer;
                }
                this.masterTooltip.show(this._connectNode);
                this.isShowingNow = true;
            },
            close: function(){
                if(!this.isShowingNow){ return; }
                if (this.masterTooltip != null) {
                    this.masterTooltip.hide();
                }
                this.isShowingNow = false;
            },
            _onMouseOut: function(/*Event*/ e){
                // currXD and Y are current position of the mouse
                var currX = e.pageX;
                var currY = e.pageY;
                // this.mouseX and this.mouseY are the top-left corners of the tooltip
                var posOffset = Math.abs(this.mouseX - e.pageX) + Math.abs(this.mouseY - e.pageY);
                // console.log("Mouse out called with ",e);
                // we allow mouse movement of 6px
                if (posOffset < 6) {
                    return;
                }
                if(dojo.isDescendant(e.relatedTarget, this._connectNode)){
                     // false event; just moved from target to target child; ignore.
                    return;
                }
                if (this.masterTooltip != null) {
                    // get coordinates and dimensions of the actual tooltip contents
                    var c = dojo.coords(this.masterTooltip.domNode);
                    console.log("Tooltip coordinates", c, "Curr", currX, currY, "MouseXY", this.mouseX, this.mouseY);
                    if (this.mouseX < currX && this.mouseX + c.w > currX && this.mouseY < currY && this.mouseY + c.h > currY) {
                        // the mouse is still on the tooltip contents, no need to close it
                        return;
                    }
                }
                // close the tooltip
                this._onUnHover(e);
            },
            _onHover: function(/*Event*/ e){
                if(this._hover){ return; }
                this._hover=true;
                // find the current mouse postion, the top-left corner of the tooltip will be here
                this.mouseX = e.pageX;
                this.mouseY = e.pageY;
                console.log("Mouse X,Y is "+this.mouseX+", "+this.mouseY);
                // If tooltip not showing yet then set a timer to show it shortly
                if(!this.isShowingNow && !this._showTimer){
                    this._showTimer = setTimeout(dojo.hitch(this, "open"), this.showDelay);
                }
            },
            _onMouseOver: function(/*Event*/ e){
                this._onHover(e);
            }
        }
    );
    // we have finished declaration of the widget
    // now exectute all functions that were waiting for the custom widget to load
    civicrmTooltipLoaded = true;
    dojo.forEach(civicrmLoaders, 
        function(f) {
            f();
        }
    )
}
);

console.log("javascript loaded");

}
