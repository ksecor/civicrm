<?php
header ('Content-Type: text/html; charset=UTF-8');

// $Id: roundtrip.php,v 1.4 2004/11/16 21:03:50 harryf Exp $
require_once '../JPSpan.php';

// Compress the Javascript
define('JPSPAN_INCLUDE_COMPRESS',TRUE);
require_once JPSPAN . 'Include.php';
JPSpan_Include_Register('util/data.js');
JPSpan_Include_Register('encode/php.js');
JPSpan_Include_Register('encode/xml.js');

function path() {
    $basePath = explode('/',$_SERVER['SCRIPT_NAME']);
    $script = array_pop($basePath);
    $basePath = implode('/',$basePath);
    if ( isset($_SERVER['HTTPS']) ) {
        $scheme = 'https';
    } else {
        $scheme = 'http';
    }
    echo $scheme.'://'.$_SERVER['SERVER_NAME'].$basePath;
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title> Round Trips </title>
<script type="text/javascript">
<!--
<?php JPSpan_Includes_Display(); ?>

var printrURL = "<?php path();?>/printrresponse.php";
var serializedURL = "<?php path();?>/serializedresponse.php";

//------------------------------------------------------------
function MyObject() {}

//------------------------------------------------------------
function var_dump(data) {
    var Data = new JPSpan_Util_Data();
    return Data.dump(data);
}

//------------------------------------------------------------
function encode(data) {
    if ( document.getElementById("rencoding").value == 'php' ) {
        var encoder = new JPSpan_Encode_PHP();
    } else {
        var encoder = new JPSpan_Encode_Xml();
    }
    return encoder.encode(data);
}

//------------------------------------------------------------
function send(targetURL,data) {
    var httpRequest = false;
    try {
        httpRequest=new ActiveXObject("Msxml2.XMLHTTP")
    } catch (e) {
        try {
            httpRequest=new ActiveXObject("Microsoft.XMLHTTP")
        } catch (e) {
            try {
                httpRequest = new XMLHttpRequest();
            } catch (e) {
                alert ('Your browser does not support XMLHttpRequest');
                return null;
            }
        }
    }
    httpRequest.open("POST", targetURL, false, null, null);
    httpRequest.setRequestHeader("Content-Length", data.length);
    httpRequest.send(data);
    if (httpRequest.status!=200) {
        alert("Url: "+targetURL+" not found");
        return null;
    }    
    return httpRequest.responseText;   
}

//------------------------------------------------------------
function clear () {
    document.getElementById('results').innerHTML='';
}

//------------------------------------------------------------
function echo(msg, out) {
    document.getElementById("results").innerHTML += "<b>"+msg+"</b>";
    if ( document.getElementById("var_dump").checked ) {
        out = var_dump(out);
    }
    if ( document.getElementById("rencoding").value == 'xml' ) {
        out = out.replace(/&/g, '&amp;').replace(/</g, '&lt;');
    }
    document.getElementById('results').innerHTML += '<pre>'+out+'</pre>';
}

//------------------------------------------------------------
function printR() {
    var value = document.getElementById('name').value;
    value = encode(value);
    echo("Request:",value);
    var serverurl = printrURL+'?rencoding='+document.getElementById("rencoding").value;
    var response = send(serverurl,value);
    echo("Response:",response);
}

//------------------------------------------------------------
function arrayR() {
    var a = new Array();
    a[0] = 'x';
    a[1] = 'y';
    a.foo = 'bar';
    var value = encode(a);
    echo("Request:",value);
    var serverurl = printrURL+'?rencoding='+document.getElementById("rencoding").value;
    var response = send(serverurl,value);
    echo("Response:",response);
}

//------------------------------------------------------------
function serialized() {
    var value = document.getElementById('name').value;
    value = encode(value);
    echo("Request:",value);
    var serverurl = serializedURL+'?rencoding='+document.getElementById("rencoding").value;
    var response = send(serverurl,value);
    var datafunc = eval(response);
    var data = datafunc();
    echo("Response:",data);
}

//------------------------------------------------------------
function sendObject() {
    obj = new Object();
    obj.x = 'Foo';
    obj.y = 'Bar';
    obj.z = ['a','b','c'];
    var value = encode(obj);
    echo("Request:",value);
    var serverurl = serializedURL+'?rencoding='+document.getElementById("rencoding").value;
    var response = send(serverurl,value);
    var datafunc = eval(response);
    var data = datafunc();
    echo("Response",data);
}

//------------------------------------------------------------
function getError() {
    var obj = new Object();
    obj['geterror'] = 'standarderror';
    var value = encode(obj);
    echo("Request:",value);
    var serverurl = serializedURL+'?rencoding='+document.getElementById("rencoding").value;
    var response = send(serverurl,value);
    var datafunc = eval(response);
    try {
        var data = datafunc();
        echo("Response:",data);
    } catch (e) {
        alert (e.name+": "+e.message);
        echo("Error (expected):",e);
    }
}

//------------------------------------------------------------
function getMyError() {
    var obj = new Object();
    obj['geterror'] = 'customerror';
    var value = encode(obj);
    echo("Request:",value);
    var value = encode(obj);
    echo("Request:",value);
    var serverurl = serializedURL+'?rencoding='+document.getElementById("rencoding").value;
    var response = send(serverurl,value);
    var datafunc = eval(response);
    try {
        var data = datafunc();
        echo("Response",data);
    } catch (e) {
        alert (e.name+": "+e.message);
        echo("Error (expected):",e);
    }
}
-->
</script>
</head>
<body>
<h1>Round Trips</h1>
<p>Demonstrates making a request to a remote PHP script using XMLHttpRequest. Be warned requests are synchronous
- on Sourceforge with network latency / server issues this may hang your browser (esp. IE).
Side note: excuse the abuse of the acronym tag - laziness.</p>
<form>
Enter some text: <input id="name" type="text" value="Joe Bloggs (Iñtërnâtiônàlizætiøn)" size="35">
<acronym title="Text in this field will be used in some of the examples below">?</acronym>
<br>
var_dump: <input id="var_dump" type="checkbox" checked>
<acronym title="Request and response will be shown with data typing information">?</acronym>
<br>
request serialization: <select id="rencoding"><option>php</option><option selected>xml</option></select>
<acronym title="Notice the effect on the word Iñtërnâtiônàlizætiøn when using the PHP serializatation">?</acronym>
<br>
</form>
<a href="javascript:clear();">Clear</a> 
<acronym title="Clear the results">?</acronym> |
<a href="javascript:printR()">Get back what you sent</a> 
<acronym title="Server will unserialize the request then send back the result passed through PHPs print_r() function">?</acronym> |
<a href="javascript:arrayR()">Associative Array</a> 
<acronym title="Server will unserialize the request then send back the result passed through PHPs print_r() function">?</acronym> |
<a href="javascript:serialized()">Serialized Response</a> 
<acronym title="Server will return an encoded response">?</acronym> |
<a href="javascript:sendObject()">Send an Object</a>
<acronym title="Server will return an encoded response">?</acronym> |
<a href="javascript:getError()">Get Error</a> 
<acronym title="Server will return an encoded Javascript exception">?</acronym> |
<a href="javascript:getMyError()">Get Custom Error</a> 
<acronym title="Server will return an encoded Javascript exception (extending the error)">?</acronym> |
<h2>Results</h2>
<div id="results">
</div>
</body>
</html>
