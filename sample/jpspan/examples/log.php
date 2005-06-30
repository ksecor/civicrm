<?php
// $Id: log.php,v 1.2 2004/11/18 20:34:20 harryf Exp $
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
<title> Logging </title>
<style type="text/css">
<!--
.logOut {
    height: 200px;
    font-family: serif;
}

#request {
    float: right;
}

#
-->
</style>
<script type="text/javascript" src="<?php path(); ?>/log_server.php?client"></script>
<script type="text/javascript">

var intervalId = false;

function startLogging() {
    switchOn();
    intervalId = window.setInterval('logUpdate()',3000);
}

function stopLogging() {
    switchOff();
    if ( intervalId ) {
        window.clearInterval(intervalId);
        intervalId = false;
    }
}

function switchOn() {
    var logSwitch = document.getElementById('logSwitch');
    logSwitch.onclick = stopLogging;
    logSwitch.value = "Stop Logging";
}

function switchOff() {
    var logSwitch = document.getElementById('logSwitch');
    logSwitch.onclick = startLogging;
    logSwitch.value = "Start Logging";
}

function logUpdate() {
    try {
        var requestreader = new logreader(LogHandler);
        requestreader.getrequesttail();
        var errorreader = new logreader(LogHandler);
        errorreader.geterrortail();
    } catch(e) {
        stopLogging();
    }
}

var LogHandler = {
    getrequesttail: function(result) {
        document.getElementById('requestTail').innerHTML = "<pre>"+result+"</pre>";
    },
    geterrortail: function(result) {
        document.getElementById('errorTail').innerHTML = "<pre>"+result+"</pre>";
    }
}

function sayHello() {
    var obj = new someclass(SomeHandler);
    obj.sayhello(document.getElementById('name').value);
}

function makePHPError() {
    var obj = new someclass(SomeHandler);
    obj.makephperror();
}

function makeAppError() {
    var obj = new someclass(SomeHandler);
    obj.makeapperror();
}

var SomeHandler = {

    sayhello: function(result) {
        alert(result);
    }
    
}


-->
</script>
</head>
<body>
<h1> Logging Example </h1>
<form>
<input id="logSwitch" type="button" onclick="startLogging()" value="Start Logging"><br><br>
<input type="text" id="name" value="Joe Foo">
<input type="button" onclick="sayHello()" value="Say Hello">
</form>
<a href="javascript:makePHPError()">Generate a PHP error</a> : <a href="javascript:makeAppError()">Generate an application error</a><br>
<hr>

<b>Request Log</b>
<div class="logOut" id="requestTail">
</div>

<b>Error Log</b>
<div class="logOut" id="errorTail">
</div>

</body>
</html>
