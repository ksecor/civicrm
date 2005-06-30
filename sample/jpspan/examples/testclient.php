<?php
// $Id: testclient.php,v 1.3 2004/11/22 11:06:25 harryf Exp $
require_once '../JPSpan.php';
require_once JPSPAN . 'Include.php';
JPSpan_Include_Register('encode/php.js');
JPSpan_Include_Register('encode/xml.js');
JPSpan_Include_Register('request/get.js');
JPSpan_Include_Register('request/post.js');
JPSpan_Include_Register('request/rawpost.js');
JPSpan_Include_Register('util/data.js');

header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header( "Cache-Control: no-cache, must-revalidate" ); 
header( "Pragma: no-cache" );

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
<title> HttpClient Test Page </title>
<style type="text/css">
<!--
body {
	margin:0px;
	padding:0px;
	font-family:verdana, arial, helvetica, sans-serif;
	color:#333;
	background-color:white;
	}
h1 {
	margin:0px 0px 0px 0px;
	padding:0px;
	font-size:28px;
	line-height:28px;
	font-weight:900;
	color:#ccc;
	}
h2 {
	margin:0px 0px 0px 0px;
	padding:0px;
	font-size:18px;
	line-height:18px;
	font-weight:500;
	line-height:24px;
	}
p {
	font:11px/20px verdana, arial, helvetica, sans-serif;
	margin:0px 0px 16px 0px;
	padding:0px;
	}
#Content>p {margin:0px;}
#Content>p+p {text-indent:30px;}

input, select {
	font:11px/20px verdana, arial, helvetica, sans-serif;
	margin:0px 0px 0px 0px;
	padding:0px;
	}

a {
	color:#09c;
	font-size:11px;
	text-decoration:none;
	font-weight:600;
	font-family:verdana, arial, helvetica, sans-serif;
	}
a:link {color:#09c;}
a:visited {color:#07a;}
a:hover {background-color:#eee;}
#Header {
	margin:50px 0px 10px 0px;
	padding:17px 0px 0px 20px;
	/* For IE5/Win's benefit height = [correct height] + [top padding] + [top and bottom border widths] */
	height:33px; /* 14px + 17px + 2px = 33px */
	line-height:11px;
	voice-family: "\"}\"";
	voice-family:inherit;
	height:14px; /* the correct height */
	}
body>#Header {height:14px;}
#Content {
	margin:0px 50px 50px 300px;
	padding:10px;
	}

#Menu {
	position:absolute;
	top:100px;
	left:20px;
	width:172px;
	padding:10px;
	line-height:17px;
	font-size:11px;
	voice-family: "\"}\"";
	voice-family:inherit;
	width:250px;
	}
body>#Menu {width:250px;}
-->
</style>
<script type="text/javascript">
<?php JPSpan_Includes_Display(); ?>

var serverUrl = "<?php path();?>/printrresponse.php";
var requestUrl = "<?php path();?>/printrresponse.php?rencoding=xml";
var encoder = new JPSpan_Encode_Xml();

//-------------------------------------------------------------------------
// Utility functions for this example
function echo(string) {
    document.getElementById("results").innerHTML += string;
}

function clear() {
    document.getElementById("results").innerHTML = "";
}

function getTimeOut() {
    timeout = parseInt(document.getElementById("timeout").value);
    if ( !isNaN(timeout) ) {
        return timeout;
    }
    return 20000;
}

function setREncoding(value) {
    requestUrl = serverUrl + '?rencoding='+value;
    if ( value == 'php' ) {
        encoder = new JPSpan_Encode_PHP();
    } else {
        encoder = new JPSpan_Encode_Xml();
    }
}

function var_dump(data) {
    var Data = new JPSpan_Util_Data();
    return Data.dump(data);
}
//-------------------------------------------------------------------------
// ASYNC stuff
//-------------------------------------------------------------------------
function asyncPost() {
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_Post(encoder);
    r.timeout = getTimeOut();
    
    r.serverurl = requestUrl;
    r.addArg('method','POST');
    r.addArg('x','foo');
    r.addArg('y','bar');
    r.addArg('z',new Array(1,2,3));
    c.asyncCall(r, BasicHandler);
}

function asyncGet() {
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_Get(encoder);
    r.timeout = getTimeOut();
    r.serverurl = requestUrl;
    r.addArg('method','GET');
    r.addArg('x','foo');
    r.addArg('y','bar');
    r.addArg('z',new Array(1,2,3));
    c.asyncCall(r, BasicHandler);
}

function asyncRawPost() {
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_RawPost(encoder);
    r.timeout = getTimeOut();
    r.serverurl = requestUrl;
    r.addArg('method','RawPOST');
    r.addArg('x','foo');
    r.addArg('y','bar');
    r.addArg('z',new Array(1,2,3));
    c.asyncCall(r, BasicHandler);
}

var BasicHandler = {
    onLoad: function(result) {
        echo("<pre>"+var_dump(result)+"</pre>");
    },
    onError: function(e) {
        alert(e.name+': '+e.message+' (JPSpan Code: '+e.code+')');
    }
}

var EvalHandler = {
    onLoad: function(result) {
        try {
            result = eval(result);
            result = result();
            echo("<pre>"+var_dump(result)+"</pre>");
        } catch(e) {
            this.onError(e);
        }
    },
    onError: function(e) {
        alert(e.name+': '+e.message+"\n [File: "+e.file+', line: '+e.line+"]\n (JPSpan Code: "+e.code+')');
    }
}

//-------------------------------------------------------------------------
// SYNC Stuff
//-------------------------------------------------------------------------
function syncPost() {
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_Post(encoder);
    r.serverurl = requestUrl;
    r.addArg('method','POST');
    r.addArg('x','foo');
    r.addArg('y','bar');
    r.addArg('z',new Array(1,2,3));
    try {
        echo('<pre>'+c.call(r)+'</pre>');
    } catch ( e ) {
        echo (e.message);
    }
}

function syncGet() {
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_Get(encoder);
    r.serverurl = requestUrl;
    r.addArg('method','GET');
    r.addArg('x','foo');
    r.addArg('y','bar');
    r.addArg('z',new Array(1,2,3));
    try {
        echo('<pre>'+c.call(r)+'</pre>');
    } catch ( e ) {
        echo (e.message);
    }
}

function syncRawPost() {
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_RawPost(encoder);
    r.serverurl = requestUrl;
    r.addArg('method','RawPOST');
    r.addArg('x','foo');
    r.addArg('y','bar');
    r.addArg('z',new Array(1,2,3));
    try {
        echo('<pre>'+c.call(r)+'</pre>');
    } catch ( e ) {
        echo (e.message);
    }
}
//-------------------------------------------------------------------------

function notFound() {
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_Get(encoder);
    r.serverurl = 'http://localhost/pagenotfound_123567234.html';
    c.asyncCall(r,BasicHandler);
}

function permissionDenied() {
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_Get(encoder);
    r.serverurl = 'http://127.0.0.253/';
    try {
        c.asyncCall(r,BasicHandler);
    } catch(e) {
        BasicHandler.onError(e);
    }
}

function callInProgress() {
    var c = new JPSpan_HttpClient();

    for (var i=0; i<3; i++ ) {
    
        var r = new JPSpan_Request_Get(encoder);
        
        // Interesting... (try commenting then asyncGet then multiAsyncGet)
        r.reset();
        
        r.serverurl = requestUrl;
        r.addArg('requestNum','request number: '+i);
        try {
            c.asyncCall(r, BasicHandler);
        } catch (e) {
            alert('Request #'+i+' ['+e.code+'] '+e);
        }
    }
}

function timeout() {
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_Get(encoder);
    r.timeout = 1;
    r.serverurl= requestUrl+'&timeout=3';
    r.addArg('method','GET');
    r.addArg('x','foo');
    r.addArg('y','bar');
    r.addArg('z',new Array(1,2,3));
    c.asyncCall(r, BasicHandler);
}

function invalidParamName() {
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_Get(encoder);
    try {
        r.addArg('%50','foo');
        c.asyncCall(r, BasicHandler);
    } catch (e) {
        BasicHandler.onError(e);
    }
}

function recursion() {
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_Get(encoder);
    r.serverurl = requestUrl;
    var a = new Object();
    var b = new Object();
    a.b = b;
    b.a = a;
    try {
        r.addArg('a',a);
        c.asyncCall(r, BasicHandler);
    } catch (e) {
        BasicHandler.onError(e);
    }
}
//-------------------------------------------------------------------------
function fopen() {
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_Get(encoder);
    r.serverurl = requestUrl+'&error=native';
    c.asyncCall(r, EvalHandler);
}

function notice() {
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_Get(encoder);
    r.serverurl = requestUrl+'&error=notice';
    c.asyncCall(r, EvalHandler);
}

function warning() {
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_Get(encoder);
    r.serverurl = requestUrl+'&error=warning';
    c.asyncCall(r, EvalHandler);
}

function error() {
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_Get(encoder);
    r.serverurl = requestUrl+'&error=error';
    c.asyncCall(r, EvalHandler);
}

function exception() {
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_Get(encoder);
    r.serverurl = requestUrl+'&error=exception';
    c.asyncCall(r, EvalHandler);
}
-->
</script>
</head>
<body>
<div id="Header">
<h1><acronym title="Examples of JPSpan_HttpClient, JPSpan_Request_Get, JPSpan_Request_Post and JPSpan_Request_RawPost">Client Test Page</acronym></h1>
</div>
<div id="Content">
    <h2>Response from Server</h2>
    <div id="results">
    </div>
</div>
<div id="Menu">
<acronym title="Clean up the results"><a href="javascript:clear()">clear()</a></acronym><br>
<form>
<acronym title="Encoding for request">Encoding</acronym>: <select onChange="setREncoding(this.options[selectedIndex].value);"><option>xml</option><option>php</option></select>
</form>
<br>
<h2>Async</h2>
<acronym title="Asynchronous HTTP POST request"><a href="javascript:asyncPost()">asyncPost()</a></acronym><br>
<acronym title="Asynchronous HTTP GET request"><a href="javascript:asyncGet()">asyncGet()</a></acronym><br>
<acronym title="Asynchronous raw HTTP POST request"><a href="javascript:asyncRawPost()">asyncRawPost()</a></acronym><br>
<form>
<acronym title="Try adding a sleep() to printrresponse.php to simulate"><label>Timeout [ms]</label></acronym>: <input type="text" id="timeout" value="20000" size="5"><br>
</form>

<h2>Sync</h2>
<acronym title="Synchronous HTTP POST request"><a href="javascript:syncPost()">syncPost()</a></acronym><br>
<acronym title="Synchronous HTTP GET request"><a href="javascript:syncGet()">syncGet()</a></acronym><br>
<acronym title="Asynchronous raw HTTP POST request"><a href="javascript:syncRawPost()">syncRawPost()</a></acronym><br>
<br>
<h2>Client_Error</h2>
<acronym title="HTTP page not found"><a href="javascript:notFound()">notFound()</a></acronym><br>
<acronym title="Request to 127.0.0.253"><a href="javascript:permissionDenied()">permissionDenied()</a></acronym><br>
<acronym title="Attempt multiple calls to when request already in progress"><a href="javascript:callInProgress()">callInProgress()</a></acronym><br>
<acronym title="Request timed out before response received"><a href="javascript:timeout()">timeout()</a></acronym><br>
<acronym title="Invalid request parameter name"><a href="javascript:invalidParamName()">invalidParamName()</a></acronym><br>
<acronym title="Recursive references in request data"><a href="javascript:recursion()">recursion()</a></acronym><br>
<br>

<h2>Server_Error</h2>
<acronym title="A native PHP error resulting from failed fopen"><a href="javascript:fopen()">fopen()</a></acronym><br>
<acronym title="PHP E_USER_NOTICE from trigger_error()"><a href="javascript:notice()">notice()</a></acronym><br>
<acronym title="PHP E_USER_WARNING from trigger_error()"><a href="javascript:warning()">warning()</a></acronym><br>
<acronym title="PHP E_USER_ERROR from trigger_error()"><a href="javascript:error()">error()</a></acronym><br>
<acronym title="PHP5 Exception"><a href="javascript:exception()">exception()</a></acronym><br>
</div>
</body>
</html>
