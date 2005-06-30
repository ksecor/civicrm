<?php
require_once '../JPSpan.php';

require_once JPSPAN . 'Include.php';
JPSpan_Include_Register('util/data.js');
JPSpan_Include_Register('encode/xml.js');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title> Javascript Datatypes to JSpan XML </title>
<script type="text/javascript">
<?php JPSpan_Includes_Display(); ?>

function var_dump(data) {
    var Data = new JPSpan_Util_Data();
    return Data.dump(data);
}

function serialize(data) {
    var Encoder = new JPSpan_Encode_Xml();
    return Encoder.encode(data);
}

function MyObject () {
    this.x = 1;
    this.y = 2;
}

function serializeArray() {
    var foo = new Array();
    foo[0] = 'x';
    foo[1] = 1;
    foo[2] = 1.1;
    foo['x'] = 'foo';
    echo(foo, serialize(foo));
}

function serializeDataSet() {
    var resultset = new Array();
    resultset.push(new Array('a','b','c'));
    resultset.push(new Array('m','n','o'));
    resultset.push(new Array(1,1.1,0));
    echo(resultset, serialize(resultset));
}

function serializeObject() {
    var obj = new Object();
    obj.x = 1;
    obj.y = 'x';
    obj.z = obj.x;
    obj.array = new Array(1,2,3);
    echo(obj, serialize(obj));
}

function serializeMyObject() {
    var obj = new MyObject();
    echo(obj, serialize(obj));
}

function echo(d, s) {
    document.getElementById("results").innerHTML +=
        "<hr><h2>Var_dump</h2><pre>"+var_dump(d)+"</pre><h2>Serialized</h2>"+s.replace(/&/g, '&amp;').replace(/</g, '&lt;');
}

function clear() {
    document.getElementById("results").innerHTML = '';
}
-->
</script>
</head>
<body>
<h1> Javascript Datatypes to JSpan XML </h1>
<p>Shows the serialization of Javascript types to JSpan XML request format</p>
<a href="javascript:serializeArray()">serializeArray</a> - mixed array of scalar types<br>
<a href="javascript:serializeDataSet()">serializeDataSet</a> - serialize something like a data set<br>
<a href="javascript:serializeObject()">serializeObject</a> - Object<br>
<a href="javascript:serializeMyObject()">serializeMyObject</a> - MyObject<br>
<a href="javascript:clear()">clear</a><br>
<h2>Results</h2>
<div id="results">
</div>
</body>
</html>
