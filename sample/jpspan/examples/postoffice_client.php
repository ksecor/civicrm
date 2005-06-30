<?php
// $Id: postoffice_client.php,v 1.2 2004/11/15 21:23:15 harryf Exp $
require_once '../JPSpan.php';

// Compress the Javascript
define('JPSPAN_RENDER_FORMATTING',FALSE);

require_once JPSPAN . 'Include.php';
JPSpan_Include_Register('util/data.js');
JPSpan_Include_Register('encode/php.js');
JPSpan_Include_Register('request/post.js');

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
<title> PostOffice Server Demo </title>
<script type="text/javascript">
<?php JPSpan_Includes_Display(); ?>

var baseUrl = "<?php path();?>/postoffice_server.php";

function echo(string) {
    document.getElementById("results").innerHTML += string;
}

function clear() {
    document.getElementById("results").innerHTML = "";
}

function add() {
    clear();
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_Post(new JPSpan_Encode_PHP());
    r.serverurl = baseUrl+'/math/add/';
    r.addArg('first',document.getElementById("x").value);
    r.addArg('second',document.getElementById("y").value);
    c.asyncCall(r, ResponseHandler);
}

function subtract() {
    clear();
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_Post(new JPSpan_Encode_PHP());
    r.serverurl = baseUrl+'/math/subtract/';
    r.addArg('first',document.getElementById("x").value);
    r.addArg('second',document.getElementById("y").value);
    c.asyncCall(r, ResponseHandler);
}

function divide() {
    clear();
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_Post(new JPSpan_Encode_PHP());
    r.serverurl = baseUrl+'/math/divide/';
    r.addArg('first',document.getElementById("x").value);
    r.addArg('second',document.getElementById("y").value);
    c.asyncCall(r, ResponseHandler);
}

function listColors() {
    var c = new JPSpan_HttpClient();
    var r = new JPSpan_Request_Post(new JPSpan_Encode_PHP());
    r.serverurl = baseUrl+'/colors/listcolors/';
    try {
        var result = c.call(r);
        try { 
            var dataFunc = eval(result);
            try {
                colors = dataFunc();
                echo ('<h2>A Short List of Colors</h2>');
                for (var i=0;i<colors.length;i++) {
                    echo ("<br>");
                    for (prop in colors[i]) {
                        if ( prop == 'toPHP' ) {
                            continue;
                        }
                        echo (colors[i][prop]+" : ");
                    }
                }
            } catch (e) {
                alert ('['+e.name+'] '+e.message);
            }
        } catch (e) {
            alert ('['+e.name+'] '+e.message);
        }
    } catch (e) {
        alert(e);
    }
}

var ResponseHandler = {
    onLoad: function(result) {
        try {
            var dataFunc = eval(result);
            try {
                echo(dataFunc());
            } catch (e) {
                alert ('['+e.name+'] '+e.message);
            }
        } catch (e) {
            alert ('['+e.name+'] '+e.message);
        }
    },
    onError: function(e) {
        alert(e.message);
    }
}
-->
</script>
</head>
<body>
<h1>PostOffice Server Demo</h1>
<p>Demo of JPSpan_PostOffice with hand-coded client. All "number crunching" here happens on the server (in PHP).</p>
<p>In this example the client Javascript is hand coded (hacked ;))</p>
<form id="mathForm">
X: <input id="x" type="text" value="2" size="2"><br>
Y: <input id="y" type="text" value="2" size="2"><br>
<input type="button" onClick="add()" value="Add"><br>
<input type="button" onClick="subtract()" value="Subtract"><br>
<input type="button" onClick="divide()" value="Divide"> (see what happens if you set Y to zero)<br>
</form>
<a href="javascript:listColors()">Get a list from the server</a>
<h2>Response from Server</h2>
<div id="results">
</div>
</body>
</html>
