<?php
// $Id: callstates.php,v 1.2 2004/11/23 11:06:00 harryf Exp $
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header( "Cache-Control: no-cache, must-revalidate" ); 
header( "Pragma: no-cache" );

// Just a utility to help the example work out where the server URL is...
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
<title> Callstates </title>
<script type="text/javascript" src="<?php path(); ?>/callstates_server.php?client"></script>
<script type="text/javascript">

function getLargeResult() {
    var resultHandler = new ResultHandler();
    var oSomedata = new somedata(resultHandler);
    resultHandler.somedata = oSomedata;
    oSomedata.getlargeresult();
}

// Callback handler
function ResultHandler(){}
ResultHandler.prototype = {

    somedata: null,
    
    intervalId: null,
    
    // Before XMLHttpRequest.open()
    getlargeresultInit: function() {
        var msg = getStamp()+" getlargeresultInit called (readyState=0)\n";
        echo(msg);
    },
    
    // After XMLHttpRequest.open()
    getlargeresultOpen: function() {
        var msg = getStamp()+" getlargeresultOpen called (readyState=1)\n";
        echo(msg);
    },
    
    // After XMLHttpRequest.send()
    getlargeresultSend: function() {
        var msg = getStamp()+" getlargeresultSend called (readyState=2)\n";
        echo(msg);
        this.intervalId = window.setInterval('refreshTimer();',5);
    },
    
    // Loading....
    getlargeresultProgress: function() {
        var msg = getStamp()+" getlargeresultProgress called (readyState=3)\n";
        echo(msg);
    },
    
    getlargeresult: function(result) {
        var msg = getStamp()+" getlargeresult called (readyState=4)\n";
        msg += "Response Headers:\n";
        msg += this.somedata.GetXMLHttp().getAllResponseHeaders();
        echo(msg);
        window.clearInterval(this.intervalId);
    }
    
}

function getStamp() {
    var date = new Date();
    return '['+date.getHours()+'h '+date.getMinutes()+'m '+date.getSeconds()+'s '+date.getMilliseconds()+'ms]';
}

function refreshTimer() {
    var d = new Date();
    document.getElementById( 'timer' ).innerHTML = d.getMilliseconds();
    document.getElementById( 'timerbox' ).style.width = d.getMilliseconds() / 10 + '%';
    return true;
}

function echo(msg) {
    document.getElementById('result').innerHTML+='<pre>'+msg+'</msg>';
}
-->
</script>
</head>
<body>
<h1> Callstates </h1>
<p><a href="javascript:getLargeResult()">getLargeResult</a></p>
<!--
Snatched from: http://wehrlos.strain.at/httpreq/client.html
-->
<p>The time below is started at readyState 2 and stopped at readyState 4. Mozilla does the right thing. IE not...</p>
<div id="timerbox" style="min-width:100px;color:#FFFFFF;background-color:#CCCCFF;"><b>Timer:</b> <span id="timer"></span></div>
<div id="result">
</div>
</body>
</html>
