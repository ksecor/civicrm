Sample script for show/hide nodes, from http://www.developeriq.com/articles/view_article.php?id=353

<script language="javascript" type="text/javascript">
<!--
function show(which){
m=document.getElementById("menu"); trig=m.getElementsByTagName("div").item(which).style.display;
if (trig=="block") trig="none";
else if (trig=="" || trig=="none") trig="block"; m.getElementsByTagName("div").item(trigger).style.display=trig;
var highlighttext="-";
varnormaltext="+";
t=m.getElementsByTagName("h5").item(which);
h=t.getElementsByTagName("a").item(0).firstChild;
if (trig=="none"){h.nodeValue=h.nodeValue.
replace(highlighttext,normaltext);}
else {h.nodeValue=h.nodeValue.replace(normaltext,highlighttext);} }
//-->
</script>
<style type="text/css">#menu div {display:none;}</style>
<div id="menu">
<h5><a href="javascript:show(0)">+ Test1</a></h5>
<div>Test 1</div>
<h5><a href="javascript:show(1)">+ Test2</a></h5>
<div>Test 2</div>
<h5><a href="javascript:show(2)">+ Test3</a></h5>
<div>Test3</div>
</div>

