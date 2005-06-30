<?php
// $Id: vardump.php,v 1.5 2004/11/16 21:03:50 harryf Exp $
require_once '../JPSpan.php';
require_once JPSPAN . 'Include.php';

JPSpan_Include_Register('util/data.js');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title> JPSpan_Util_Data.dump() </title>
<script type="text/javascript">
<?php JPSpan_Includes_Display(); ?>

// Define it
function var_dump(data) {
    var Data = new JPSpan_Util_Data();
    return Data.dump(data);
}

// For examples
function MyObject () {
    this.x = 1;
    this.y = 2;
}

function dump1() {
    var foo = new Array();
    foo[0] = 'x';
    foo[1] = 1;
    foo[2] = 1.1;
    foo['x'] = 'foo';
    echo(var_dump(foo));
}

function dump2() {
    var resultSet = new Array();
    resultSet.push(new Array('a','b','c'));
    resultSet.push(new Array('m','n','o'));
    resultSet.push(new Array(1,1.1,0));
    echo(var_dump(resultSet));
}

function dump3() {
    var obj = new Object();
    obj.x = 1;
    obj.y = 'x';
    obj.z = obj.x;
    obj.array = new Array(1,2,3);
    echo(var_dump(obj));
}

function dump4() {
    var obj = new MyObject();
    echo(var_dump(obj));
}

function dump5() {
    try {
        doesNotExist();
    } catch (e) {
        echo(var_dump(e));
    }
}

function dump6() {
    echo(var_dump(new Date()));
}

function dump7() {
    var a = new Array('arrayA');
    var b = new Array('arrayB');
    a.push(b);
    b.push(a);
    var main = new Array(a,b);
    
    try {
        echo(var_dump(a));
    } catch (e) {
        echo(var_dump(e));
    }
}

function dump8() {
    var obj = new MyObject();
    var Data = new JPSpan_Util_Data()
    Data.Serialize.addType('MyObject', displayMyObject);
    echo(Data.dump(obj));
}

function displayMyObject(obj, Serialize, cname) {
    var msg = "MyObject serialzed the way I want it\n";
    for (var prop in obj) {
        msg += "\t->"+prop+" = "+Serialize.serialize(obj[prop]);
    }
    return msg;
}

function dump9() {
    var days = new Object();
    days.today = new Date();
    days.yesterday = new Date();
    days.yesterday.setDate(days.today.getDate()-1);
    days.tomorrow = new Date();
    days.tomorrow.setDate(days.today.getDate()+1);
    var Data = new JPSpan_Util_Data()
    Data.Serialize.addType('Date', displayDate);
    echo(Data.dump(days));
}

function displayDate(D) {
    return D.toString()
}

function echo(out) {
    document.getElementById("results").innerHTML += "<pre>"+out+"</pre>";
}

function clear() {
    document.getElementById("results").innerHTML = '';
}
-->
</script>
</head>
<body>
<h1>JPSpan_Util_Data.dump()</h1>
<p>Demonstrates the data dumper</p>
<a href="javascript:dump1()">Dump1</a> - mixed array of scalar types<br>
<a href="javascript:dump2()">Dump2</a> - array of arrays<br>
<a href="javascript:dump3()">Dump3</a> - Object<br>
<a href="javascript:dump4()">Dump4</a> - MyObject<br>
<a href="javascript:dump5()">Dump5</a> - a caught error<br>
<a href="javascript:dump6()">Dump6</a> - a Date<br>
<a href="javascript:dump7()">Dump7</a> - reference recursion<br>
<a href="javascript:dump8()">Dump8</a> - Serialization of MyObject overloaded<br>
<a href="javascript:dump9()">Dump9</a> - Serialization of Date object, overloaded<br>
<a href="javascript:clear()">clear</a> - clear results<br>
<h2>Results</h2>
<div id="results">
</div>
</body>
</html>
