//My top Javascripts for Designers 
//See http://www.blakems.com/archives/000087.html for more info.

//expandCollapse
function expandCollapse() {
	for (var i=0; i<expandCollapse.arguments.length; i++) {
		var element = document.getElementById(expandCollapse.arguments[i]);
		element.style.display = (element.style.display == "none") ? "block" : "none";
	}
}
//Hide Timer
var timerID;

function ShowLayer(id) {
  document.getElementById().style.display = "block"; 
}

function HideTimedLayer(id) {  
	clearTimeout(timerID);
	document.getElementById(id).style.display = "none";
}

function timedLayer(id) {
  setTimeout("HideTimedLayer(\"" + id + "\")", 5000); //5000= 5 seconds
}
//error check
function check_required(myForm) {
	var requiredFields = myForm.required.value.split("|");
	var errorString = '';
	for (var i=0; i<requiredFields.length; i++) {
		var parts = requiredFields[i].split(",");
		var field = parts[0]; var title = parts[1];
		for (var j=0; j<myForm.elements.length; j++) {
			var myElement = myForm.elements[j];
			var isNull = false;
			if (myElement.name == field && myElement.style.display != "none") {
				if (myElement.type == "select-one" || myElement.type == "select-multiple") {
					if ((myElement.options[myElement.selectedIndex].value == null || myElement.options[myElement.selectedIndex].value == '') && errorString.indexOf(title) == -1) {
						isNull = true;
					}
				} else if ((myElement.value == null || myElement.value.search(/\w/) == -1) && errorString.indexOf(title) == -1) {
					isNull = true;
				}
				
				if (isNull) {
					errorString += title + ", ";
					if (document.getElementById('label_'+myElement.name)) { document.getElementById('label_'+myElement.name).className="er"; }
					myElement.className="erInput";
				} else {
					if (document.getElementById('label_'+myElement.name)) {
						document.getElementById('label_'+myElement.name).className="s1";
					}
					myElement.className="s1";
				}
			}
		}
	}
	if (errorString != '') {
		errorString = errorString.slice(0,errorString.length-2);
		window.alert("Please fill in the following required fields before submitting this form:\n\n"+errorString)
		return false;
	}
	else {
		return true;
	}
}
//Sons of Suckerfish http://www.htmldog.com/articles/suckerfish/dropdowns/
sfHover = function() {
	var sfEls = document.getElementById("nav").getElementsByTagName("LI");
	for (var i=0; i<sfEls.length; i++) {
		sfEls[i].onmouseover=function() {
			this.className+=" sfhover";
		}
		sfEls[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" sfhover\\b"), "");
		}
	}
}
if (window.attachEvent) window.attachEvent("onload", sfHover);

//Style switcher http://www.alistapart.com/articles/alternate/
function setActiveStyleSheet(title) {
  var i, a, main;
  for(i=0; (a = document.getElementsByTagName("link")[i]); i++) {
    if(a.getAttribute("rel").indexOf("style") != -1 && a.getAttribute("title")) {
      a.disabled = true;
      if(a.getAttribute("title") == title) a.disabled = false;
    }
  }
}

function getActiveStyleSheet() {
  var i, a;
  for(i=0; (a = document.getElementsByTagName("link")[i]); i++) {
    if(a.getAttribute("rel").indexOf("style") != -1 && a.getAttribute("title") && !a.disabled) return a.getAttribute("title");
  }
  return null;
}

function getPreferredStyleSheet() {
  var i, a;
  for(i=0; (a = document.getElementsByTagName("link")[i]); i++) {
    if(a.getAttribute("rel").indexOf("style") != -1
       && a.getAttribute("rel").indexOf("alt") == -1
       && a.getAttribute("title")
       ) return a.getAttribute("title");
  }
  return null;
}

function createCookie(name,value,days) {
  if (days) {
    var date = new Date();
    date.setTime(date.getTime()+(days*24*60*60*1000));
    var expires = "; expires="+date.toGMTString();
  }
  else expires = "";
  document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for(var i=0;i < ca.length;i++) {
    var c = ca[i];
    while (c.charAt(0)==' ') c = c.substring(1,c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
  }
  return null;
}

window.onload = function(e) {
  var cookie = readCookie("style");
  var title = cookie ? cookie : getPreferredStyleSheet();
  setActiveStyleSheet(title);
}

window.onunload = function(e) {
  var title = getActiveStyleSheet();
  createCookie("style", title, 365);
}

var cookie = readCookie("style");
var title = cookie ? cookie : getPreferredStyleSheet();
setActiveStyleSheet(title);

//Get element height & width 
//See http://www.aspandjavascript.co.uk/javascript/javascript_api/get_element_width_height.asp
//get height
function getElementHeight(Elem) {
	if (ns4) {
		var elem = getObjNN4(document, Elem);
		return elem.clip.height;
	} else {
		if(document.getElementById) {
			var elem = document.getElementById(Elem);
		} else if (document.all){
			var elem = document.all[Elem];
		}
		if (op5) { 
			xPos = elem.style.pixelHeight;
		} else {
			xPos = elem.offsetHeight;
		}
		return xPos;
	} 
}

// get width
function getElementWidth(Elem) {
	if(document.getElementById) {
		var elem = document.getElementById(Elem);
	} else if (document.all){
		var elem = document.all[Elem];
	}
	if (op5) {
		xPos = elem.style.pixelWidth;
	} else {
		xPos = elem.offsetWidth;
	}
	return xPos;
}
// from http://www.quirksmode.org/dom/getstyles.html
function getOff(el) {
	if (!document.createElement)
	{
		alert('This script won\'t work in your browser.');
		return;
	}
	x = document.getElementById(el);
	return x.offsetWidth;
}

function changeOff(amount) {
	var y = getOff();
	x.style.width = y + amount;
}

function movePar(el,newEl) {
	if (!document.createElement)
	{
		alert('This script won\'t work in your browser.');
		return;
	}
	var x = document.getElementById(el);
	document.getElementById(newEl).appendChild(x);
}

function getStyle(el,styleProp) {
	var x = document.getElementById(el);
	if (window.getComputedStyle)
		var y = window.getComputedStyle(x,null).getPropertyValue(styleProp);
	else if (x.currentStyle)
		var y = eval('x.currentStyle.' + styleProp);
	return y;
}

function prepare() {
	if (!document.createElement)
	{
		alert('This script won\'t work in your browser.');
		return;
	}
	var z = document.forms[0].prop.value;
	if (z) var y = getStyle('test',z);
	alert(z + ' = ' + y);
}


function fixTableWidths() {
  
  
}