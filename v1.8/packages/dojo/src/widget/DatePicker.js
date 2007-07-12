/*
	Copyright (c) 2004-2006, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/community/licensing.shtml
*/


dojo.provide("dojo.widget.DatePicker");
dojo.require("dojo.date.common");
dojo.require("dojo.date.format");
dojo.require("dojo.date.serialize");
dojo.require("dojo.widget.*");
dojo.require("dojo.widget.HtmlWidget");
dojo.require("dojo.event.*");
dojo.require("dojo.dom");
dojo.require("dojo.html.style");
dojo.widget.defineWidget("dojo.widget.DatePicker",dojo.widget.HtmlWidget,{value:"",name:"",displayWeeks:6,adjustWeeks:false,startDate:"1492-10-12",endDate:"2941-10-12",weekStartsOn:"",staticDisplay:false,dayWidth:"narrow",classNames:{previous:"previousMonth",disabledPrevious:"previousMonthDisabled",current:"currentMonth",disabledCurrent:"currentMonthDisabled",next:"nextMonth",disabledNext:"nextMonthDisabled",currentDate:"currentDate",selectedDate:"selectedDate"},templateString:"<div class=\"datePickerContainer\" dojoAttachPoint=\"datePickerContainerNode\">\n\t<table cellspacing=\"0\" cellpadding=\"0\" class=\"calendarContainer\">\n\t\t<thead>\n\t\t\t<tr>\n\t\t\t\t<td class=\"monthWrapper\" valign=\"top\">\n\t\t\t\t\t<table class=\"monthContainer\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td class=\"monthCurve monthCurveTL\" valign=\"top\"></td>\n\t\t\t\t\t\t\t<td class=\"monthLabelContainer\" valign=\"top\">\n\t\t\t\t\t\t\t\t<span dojoAttachPoint=\"increaseWeekNode\" \n\t\t\t\t\t\t\t\t\tdojoAttachEvent=\"onClick: onIncrementWeek;\" \n\t\t\t\t\t\t\t\t\tclass=\"incrementControl increase\">\n\t\t\t\t\t\t\t\t\t<img src=\"${dojoWidgetModuleUri}templates/images/incrementMonth.png\" \n\t\t\t\t\t\t\t\t\talt=\"&darr;\" style=\"width:7px;height:5px;\" />\n\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t<span \n\t\t\t\t\t\t\t\t\tdojoAttachPoint=\"increaseMonthNode\" \n\t\t\t\t\t\t\t\t\tdojoAttachEvent=\"onClick: onIncrementMonth;\" class=\"incrementControl increase\">\n\t\t\t\t\t\t\t\t\t<img src=\"${dojoWidgetModuleUri}templates/images/incrementMonth.png\" \n\t\t\t\t\t\t\t\t\t\talt=\"&darr;\"  dojoAttachPoint=\"incrementMonthImageNode\">\n\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t<span \n\t\t\t\t\t\t\t\t\tdojoAttachPoint=\"decreaseWeekNode\" \n\t\t\t\t\t\t\t\t\tdojoAttachEvent=\"onClick: onIncrementWeek;\" \n\t\t\t\t\t\t\t\t\tclass=\"incrementControl decrease\">\n\t\t\t\t\t\t\t\t\t<img src=\"${dojoWidgetModuleUri}templates/images/decrementMonth.png\" alt=\"&uarr;\" style=\"width:7px;height:5px;\" />\n\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t<span \n\t\t\t\t\t\t\t\t\tdojoAttachPoint=\"decreaseMonthNode\" \n\t\t\t\t\t\t\t\t\tdojoAttachEvent=\"onClick: onIncrementMonth;\" class=\"incrementControl decrease\">\n\t\t\t\t\t\t\t\t\t<img src=\"${dojoWidgetModuleUri}templates/images/decrementMonth.png\" \n\t\t\t\t\t\t\t\t\t\talt=\"&uarr;\" dojoAttachPoint=\"decrementMonthImageNode\">\n\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t<span dojoAttachPoint=\"monthLabelNode\" class=\"month\"></span>\n\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t<td class=\"monthCurve monthCurveTR\" valign=\"top\"></td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t</table>\n\t\t\t\t</td>\n\t\t\t</tr>\n\t\t</thead>\n\t\t<tbody>\n\t\t\t<tr>\n\t\t\t\t<td colspan=\"3\">\n\t\t\t\t\t<table class=\"calendarBodyContainer\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n\t\t\t\t\t\t<thead>\n\t\t\t\t\t\t\t<tr dojoAttachPoint=\"dayLabelsRow\">\n\t\t\t\t\t\t\t\t<td></td>\n\t\t\t\t\t\t\t\t<td></td>\n\t\t\t\t\t\t\t\t<td></td>\n\t\t\t\t\t\t\t\t<td></td>\n\t\t\t\t\t\t\t\t<td></td>\n\t\t\t\t\t\t\t\t<td></td>\n\t\t\t\t\t\t\t\t<td></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t</thead>\n\t\t\t\t\t\t<tbody dojoAttachPoint=\"calendarDatesContainerNode\" \n\t\t\t\t\t\t\tdojoAttachEvent=\"onClick: _handleUiClick;\">\n\t\t\t\t\t\t\t<tr dojoAttachPoint=\"calendarWeekTemplate\">\n\t\t\t\t\t\t\t\t<td></td>\n\t\t\t\t\t\t\t\t<td></td>\n\t\t\t\t\t\t\t\t<td></td>\n\t\t\t\t\t\t\t\t<td></td>\n\t\t\t\t\t\t\t\t<td></td>\n\t\t\t\t\t\t\t\t<td></td>\n\t\t\t\t\t\t\t\t<td></td>\n\t\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t</tbody>\n\t\t\t\t\t</table>\n\t\t\t\t</td>\n\t\t\t</tr>\n\t\t</tbody>\n\t\t<tfoot>\n\t\t\t<tr>\n\t\t\t\t<td colspan=\"3\" class=\"yearWrapper\">\n\t\t\t\t\t<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"yearContainer\">\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td class=\"curveBL\" valign=\"top\"></td>\n\t\t\t\t\t\t\t<td valign=\"top\">\n\t\t\t\t\t\t\t\t<h3 class=\"yearLabel\">\n\t\t\t\t\t\t\t\t\t<span dojoAttachPoint=\"previousYearLabelNode\"\n\t\t\t\t\t\t\t\t\t\tdojoAttachEvent=\"onClick: onIncrementYear;\" class=\"previousYear\"></span>\n\t\t\t\t\t\t\t\t\t<span class=\"selectedYear\" dojoAttachPoint=\"currentYearLabelNode\"></span>\n\t\t\t\t\t\t\t\t\t<span dojoAttachPoint=\"nextYearLabelNode\" \n\t\t\t\t\t\t\t\t\t\tdojoAttachEvent=\"onClick: onIncrementYear;\" class=\"nextYear\"></span>\n\t\t\t\t\t\t\t\t</h3>\n\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t<td class=\"curveBR\" valign=\"top\"></td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t</table>\n\t\t\t\t</td>\n\t\t\t</tr>\n\t\t</tfoot>\n\t</table>\n</div>\n",templateCssString:".datePickerContainer {\n\twidth:164px; /* needed for proper user styling */\n}\n\n.calendarContainer {\n/*\tborder:1px solid #566f8f;*/\n}\n\n.calendarBodyContainer {\n\twidth:100%; /* needed for the explode effect (explain?) */\n\tbackground: #7591bc url(\"images/dpBg.gif\") top left repeat-x;\n}\n\n.calendarBodyContainer thead tr td {\n\tcolor:#293a4b;\n\tfont:bold 0.75em Helvetica, Arial, Verdana, sans-serif;\n\ttext-align:center;\n\tpadding:0.25em;\n\tbackground: url(\"images/dpHorizLine.gif\") bottom left repeat-x;\n}\n\n.calendarBodyContainer tbody tr td {\n\tcolor:#fff;\n\tfont:bold 0.7em Helvetica, Arial, Verdana, sans-serif;\n\ttext-align:center;\n\tpadding:0.4em;\n\tbackground: url(\"images/dpVertLine.gif\") top right repeat-y;\n\tcursor:pointer;\n\tcursor:hand;\n}\n\n\n.monthWrapper {\n\tpadding-bottom:2px;\n\tbackground: url(\"images/dpHorizLine.gif\") bottom left repeat-x;\n}\n\n.monthContainer {\n\twidth:100%;\n}\n\n.monthLabelContainer {\n\ttext-align:center;\n\tfont:bold 0.75em Helvetica, Arial, Verdana, sans-serif;\n\tbackground: url(\"images/dpMonthBg.png\") repeat-x top left !important;\n\tcolor:#293a4b;\n\tpadding:0.25em;\n}\n\n.monthCurve {\n\twidth:12px;\n}\n\n.monthCurveTL {\n\tbackground: url(\"images/dpCurveTL.png\") no-repeat top left !important;\n}\n\n.monthCurveTR {\n\t\tbackground: url(\"images/dpCurveTR.png\") no-repeat top right !important;\n}\n\n\n.yearWrapper {\n\tbackground: url(\"images/dpHorizLineFoot.gif\") top left repeat-x;\n\tpadding-top:2px;\n}\n\n.yearContainer {\n\twidth:100%;\n}\n\n.yearContainer td {\n\tbackground:url(\"images/dpYearBg.png\") top left repeat-x;\n}\n\n.yearContainer .yearLabel {\n\tmargin:0;\n\tpadding:0.45em 0 0.45em 0;\n\tcolor:#fff;\n\tfont:bold 0.75em Helvetica, Arial, Verdana, sans-serif;\n\ttext-align:center;\n}\n\n.curveBL {\n\tbackground: url(\"images/dpCurveBL.png\") bottom left no-repeat !important;\n\twidth:9px !important;\n\tpadding:0;\n\tmargin:0;\n}\n\n.curveBR {\n\tbackground: url(\"images/dpCurveBR.png\") bottom right no-repeat !important;\n\twidth:9px !important;\n\tpadding:0;\n\tmargin:0;\n}\n\n\n.previousMonth {\n\tbackground-color:#6782a8 !important;\n}\n\n.previousMonthDisabled {\n\tbackground-color:#a4a5a6 !important;\n\tcursor:default !important\n}\n.currentMonth {\n}\n\n.currentMonthDisabled {\n\tbackground-color:#bbbbbc !important;\n\tcursor:default !important\n}\n.nextMonth {\n\tbackground-color:#6782a8 !important;\n}\n.nextMonthDisabled {\n\tbackground-color:#a4a5a6 !important;\n\tcursor:default !important;\n}\n\n.currentDate {\n\ttext-decoration:underline;\n\tfont-style:italic;\n}\n\n.selectedDate {\n\tbackground-color:#fff !important;\n\tcolor:#6782a8 !important;\n}\n\n.yearLabel .selectedYear {\n\tpadding:0.2em;\n\tbackground-color:#9ec3fb !important;\n}\n\n.nextYear, .previousYear {\n\tcursor:pointer;cursor:hand;\n\tpadding:0;\n}\n\n.nextYear {\n\tmargin:0 0 0 0.55em;\n}\n\n.previousYear {\n\tmargin:0 0.55em 0 0;\n}\n\n.incrementControl {\n\tcursor:pointer;cursor:hand;\n\twidth:1em;\n}\n\n.increase {\n\tfloat:right;\n}\n\n.decrease {\n\tfloat:left;\n}\n\n.lastColumn {\n\tbackground-image:none !important;\n}\n\n\n",templateCssPath:dojo.uri.moduleUri("dojo.widget","templates/DatePicker.css"),postMixInProperties:function(){
dojo.widget.DatePicker.superclass.postMixInProperties.apply(this,arguments);
if(!this.weekStartsOn){
this.weekStartsOn=dojo.date.getFirstDayOfWeek(this.lang);
}
this.today=new Date();
this.today.setHours(0,0,0,0);
if(typeof (this.value)=="string"&&this.value.toLowerCase()=="today"){
this.value=new Date();
}else{
if(this.value&&(typeof this.value=="string")&&(this.value.split("-").length>2)){
this.value=dojo.date.fromRfc3339(this.value);
this.value.setHours(0,0,0,0);
}
}
},fillInTemplate:function(_1,_2){
dojo.widget.DatePicker.superclass.fillInTemplate.apply(this,arguments);
var _3=this.getFragNodeRef(_2);
dojo.html.copyStyle(this.domNode,_3);
this.weekTemplate=dojo.dom.removeNode(this.calendarWeekTemplate);
this._preInitUI(this.value?this.value:this.today,false,true);
var _4=dojo.lang.unnest(dojo.date.getNames("days",this.dayWidth,"standAlone",this.lang));
if(this.weekStartsOn>0){
for(var i=0;i<this.weekStartsOn;i++){
_4.push(_4.shift());
}
}
var _6=this.dayLabelsRow.getElementsByTagName("td");
for(i=0;i<7;i++){
_6.item(i).innerHTML=_4[i];
}
if(this.value){
this.setValue(this.value);
}
},getValue:function(){
return dojo.date.toRfc3339(new Date(this.value),"dateOnly");
},getDate:function(){
return this.value;
},setValue:function(_7){
this.setDate(_7);
},setDate:function(_8){
if(_8==""){
this.value="";
this._preInitUI(this.curMonth,false,true);
}else{
if(typeof _8=="string"){
this.value=dojo.date.fromRfc3339(_8);
this.value.setHours(0,0,0,0);
}else{
this.value=new Date(_8);
this.value.setHours(0,0,0,0);
}
}
if(this.selectedNode!=null){
dojo.html.removeClass(this.selectedNode,this.classNames.selectedDate);
}
if(this.clickedNode!=null){
dojo.debug("adding selectedDate");
dojo.html.addClass(this.clickedNode,this.classNames.selectedDate);
this.selectedNode=this.clickedNode;
}else{
this._preInitUI(this.value,false,true);
}
this.clickedNode=null;
this.onValueChanged(this.value);
},_preInitUI:function(_9,_a,_b){
if(typeof (this.startDate)=="string"){
this.startDate=dojo.date.fromRfc3339(this.startDate);
}
if(typeof (this.endDate)=="string"){
this.endDate=dojo.date.fromRfc3339(this.endDate);
}
this.startDate.setHours(0,0,0,0);
this.endDate.setHours(24,0,0,-1);
if(_9<this.startDate||_9>this.endDate){
_9=new Date((_9<this.startDate)?this.startDate:this.endDate);
}
this.firstDay=this._initFirstDay(_9,_a);
this.selectedIsUsed=false;
this.currentIsUsed=false;
var _c=new Date(this.firstDay);
var _d=_c.getMonth();
this.curMonth=new Date(_c);
this.curMonth.setDate(_c.getDate()+6);
this.curMonth.setDate(1);
if(this.displayWeeks==""||this.adjustWeeks){
this.adjustWeeks=true;
this.displayWeeks=Math.ceil((dojo.date.getDaysInMonth(this.curMonth)+this._getAdjustedDay(this.curMonth))/7);
}
var _e=this.displayWeeks*7;
if(dojo.date.diff(this.startDate,this.endDate,dojo.date.dateParts.DAY)<_e){
this.staticDisplay=true;
if(dojo.date.diff(_c,this.endDate,dojo.date.dateParts.DAY)>_e){
this._preInitUI(this.startDate,true,false);
_c=new Date(this.firstDay);
}
this.curMonth=new Date(_c);
this.curMonth.setDate(_c.getDate()+6);
this.curMonth.setDate(1);
var _f=(_c.getMonth()==this.curMonth.getMonth())?"current":"previous";
}
if(_b){
this._initUI(_e);
}
},_initUI:function(_10){
dojo.dom.removeChildren(this.calendarDatesContainerNode);
for(var i=0;i<this.displayWeeks;i++){
this.calendarDatesContainerNode.appendChild(this.weekTemplate.cloneNode(true));
}
var _12=new Date(this.firstDay);
this._setMonthLabel(this.curMonth.getMonth());
this._setYearLabels(this.curMonth.getFullYear());
var _13=this.calendarDatesContainerNode.getElementsByTagName("td");
var _14=this.calendarDatesContainerNode.getElementsByTagName("tr");
var _15;
for(i=0;i<_10;i++){
_15=_13.item(i);
_15.innerHTML=_12.getDate();
_15.setAttribute("djDateValue",_12.valueOf());
var _16=(_12.getMonth()!=this.curMonth.getMonth()&&Number(_12)<Number(this.curMonth))?"previous":(_12.getMonth()==this.curMonth.getMonth())?"current":"next";
var _17=_16;
if(this._isDisabledDate(_12)){
var _18={previous:"disabledPrevious",current:"disabledCurrent",next:"disabledNext"};
_17=_18[_16];
}
dojo.html.setClass(_15,this._getDateClassName(_12,_17));
if(dojo.html.hasClass(_15,this.classNames.selectedDate)){
this.selectedNode=_15;
}
_12=dojo.date.add(_12,dojo.date.dateParts.DAY,1);
}
this.lastDay=dojo.date.add(_12,dojo.date.dateParts.DAY,-1);
this._initControls();
},_initControls:function(){
var d=this.firstDay;
var d2=this.lastDay;
var _1b,_1c,_1d,_1e,_1f,_20;
_1b=_1c=_1d=_1e=_1f=_20=!this.staticDisplay;
with(dojo.date.dateParts){
var add=dojo.date.add;
if(_1b&&add(d,DAY,(-1*(this._getAdjustedDay(d)+1)))<this.startDate){
_1b=_1d=_1f=false;
}
if(_1c&&d2>this.endDate){
_1c=_1e=_20=false;
}
if(_1d&&add(d,DAY,-1)<this.startDate){
_1d=_1f=false;
}
if(_1e&&add(d2,DAY,1)>this.endDate){
_1e=_20=false;
}
if(_1f&&add(d2,YEAR,-1)<this.startDate){
_1f=false;
}
if(_20&&add(d,YEAR,1)>this.endDate){
_20=false;
}
}
function enableControl(_22,_23){
dojo.html.setVisibility(_22,_23?"":"hidden");
}
enableControl(this.decreaseWeekNode,_1b);
enableControl(this.increaseWeekNode,_1c);
enableControl(this.decreaseMonthNode,_1d);
enableControl(this.increaseMonthNode,_1e);
enableControl(this.previousYearLabelNode,_1f);
enableControl(this.nextYearLabelNode,_20);
},_incrementWeek:function(evt){
var d=new Date(this.firstDay);
switch(evt.target){
case this.increaseWeekNode.getElementsByTagName("img").item(0):
case this.increaseWeekNode:
var _26=dojo.date.add(d,dojo.date.dateParts.WEEK,1);
if(_26<this.endDate){
d=dojo.date.add(d,dojo.date.dateParts.WEEK,1);
}
break;
case this.decreaseWeekNode.getElementsByTagName("img").item(0):
case this.decreaseWeekNode:
if(d>=this.startDate){
d=dojo.date.add(d,dojo.date.dateParts.WEEK,-1);
}
break;
}
this._preInitUI(d,true,true);
},_incrementMonth:function(evt){
var d=new Date(this.curMonth);
var _29=new Date(this.firstDay);
switch(evt.currentTarget){
case this.increaseMonthNode.getElementsByTagName("img").item(0):
case this.increaseMonthNode:
_29=dojo.date.add(_29,dojo.date.dateParts.DAY,this.displayWeeks*7);
if(_29<this.endDate){
d=dojo.date.add(d,dojo.date.dateParts.MONTH,1);
}else{
var _2a=true;
}
break;
case this.decreaseMonthNode.getElementsByTagName("img").item(0):
case this.decreaseMonthNode:
if(_29>this.startDate){
d=dojo.date.add(d,dojo.date.dateParts.MONTH,-1);
}else{
var _2b=true;
}
break;
}
if(_2b){
d=new Date(this.startDate);
}else{
if(_2a){
d=new Date(this.endDate);
}
}
this._preInitUI(d,false,true);
},_incrementYear:function(evt){
var _2d=this.curMonth.getFullYear();
var _2e=new Date(this.firstDay);
switch(evt.target){
case this.nextYearLabelNode:
_2e=dojo.date.add(_2e,dojo.date.dateParts.YEAR,1);
if(_2e<this.endDate){
_2d++;
}else{
var _2f=true;
}
break;
case this.previousYearLabelNode:
_2e=dojo.date.add(_2e,dojo.date.dateParts.YEAR,-1);
if(_2e>this.startDate){
_2d--;
}else{
var _30=true;
}
break;
}
var d;
if(_30){
d=new Date(this.startDate);
}else{
if(_2f){
d=new Date(this.endDate);
}else{
d=new Date(_2d,this.curMonth.getMonth(),1);
}
}
this._preInitUI(d,false,true);
},onIncrementWeek:function(evt){
evt.stopPropagation();
if(!this.staticDisplay){
this._incrementWeek(evt);
}
},onIncrementMonth:function(evt){
evt.stopPropagation();
if(!this.staticDisplay){
this._incrementMonth(evt);
}
},onIncrementYear:function(evt){
evt.stopPropagation();
if(!this.staticDisplay){
this._incrementYear(evt);
}
},_setMonthLabel:function(_35){
this.monthLabelNode.innerHTML=dojo.date.getNames("months","wide","standAlone",this.lang)[_35];
},_setYearLabels:function(_36){
var y=_36-1;
var _38=this;
function f(n){
_38[n+"YearLabelNode"].innerHTML=dojo.date.format(new Date(y++,0),{formatLength:"yearOnly",locale:_38.lang});
}
f("previous");
f("current");
f("next");
},_getDateClassName:function(_3a,_3b){
var _3c=this.classNames[_3b];
if((!this.selectedIsUsed&&this.value)&&(Number(_3a)==Number(this.value))){
_3c=this.classNames.selectedDate+" "+_3c;
this.selectedIsUsed=true;
}
if((!this.currentIsUsed)&&(Number(_3a)==Number(this.today))){
_3c=_3c+" "+this.classNames.currentDate;
this.currentIsUsed=true;
}
return _3c;
},onClick:function(evt){
dojo.event.browser.stopEvent(evt);
},_handleUiClick:function(evt){
var _3f=evt.target;
if(_3f.nodeType!=dojo.dom.ELEMENT_NODE){
_3f=_3f.parentNode;
}
dojo.event.browser.stopEvent(evt);
this.selectedIsUsed=this.todayIsUsed=false;
if(dojo.html.hasClass(_3f,this.classNames["disabledPrevious"])||dojo.html.hasClass(_3f,this.classNames["disabledCurrent"])||dojo.html.hasClass(_3f,this.classNames["disabledNext"])){
return;
}
this.clickedNode=_3f;
this.setDate(new Date(Number(dojo.html.getAttribute(_3f,"djDateValue"))));
},onValueChanged:function(_40){
},_isDisabledDate:function(_41){
if(_41<this.startDate||_41>this.endDate){
return true;
}
return this.isDisabledDate(_41,this.lang);
},isDisabledDate:function(_42,_43){
return false;
},_initFirstDay:function(_44,adj){
var d=new Date(_44);
if(!adj){
d.setDate(1);
}
d.setDate(d.getDate()-this._getAdjustedDay(d,this.weekStartsOn));
d.setHours(0,0,0,0);
return d;
},_getAdjustedDay:function(_47){
var _48=[0,1,2,3,4,5,6];
if(this.weekStartsOn>0){
for(var i=0;i<this.weekStartsOn;i++){
_48.unshift(_48.pop());
}
}
return _48[_47.getDay()];
},destroy:function(){
dojo.widget.DatePicker.superclass.destroy.apply(this,arguments);
dojo.html.destroyNode(this.weekTemplate);
}});
