<?php
// $Id: postoffice_client_generated.php,v 1.2 2004/11/16 09:43:14 harryf Exp $
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
<title> PostOffice Server Demo with generated client </title>
<script type="text/javascript" src="<?php path(); ?>/postoffice_server.php?client"></script>
<script type="text/javascript">

// Simple example use - synchronous call
function add() {
    // The math client object
    var m = new math();
    
    var x = document.getElementById("x").value;
    var y = document.getElementById("y").value;
    
    // Call the remote procedure...
    var result = m.add(x,y);

    // Display the result
    if ( result ) {
        echo(result);
    }
}

// Taking some short cuts - synchronous call
function subtract() {
    var m = new math();
    var result = m.subtract(document.getElementById("x").value,document.getElementById("y").value);

    // This should be more cunning...
    if ( result || result === 0 ) {
        echo(result);
    }
}

// Define a custom error function for synchronous calls
// (by default errors will be echoed)
function divide() {
    var m = new math();
    
    // Define a custom error function for application errors
    m.applicationErrorFunc = function(e) {
        echo('Division by zero: infinity');
    }
    
    var result = m.divide(document.getElementById("x").value,document.getElementById("y").value);
    if ( result ) {
        echo(result);
    }
}

// Callback handler for asynchronous calls to remote math object
// Note the naming of functions here...
var MathHandler = {

    // Called with result of math.add() method
    add: function(result) {
        echo(result);
    },
    
    /*
    // Optionally define error handling function
    // if not defined, math.applicationErrorFunc is called instead
    addError: function(e) {
        alert(e.message);
    },
    */
    
    // Called with result of math.subtract() method
    subtract: function(result) {
        echo(result);
    },
    
    // Called with result of math.divide() method
    divide: function(result) {
        echo(result);
    },
    
    // Called on error with math.divide() method
    divideError: function(e) {
        alert(e.message);
    }
}

function addAsync() {

    // Create the math object, passing it the callback handler
    // This automatically switches to async mode
    var m = new math(MathHandler);
    
    // Set request timeout as required
    // m.timeout = 1000;
    
    var x = document.getElementById("x").value;
    var y = document.getElementById("y").value;
    
    // Simply call the method and continue
    m.add(x,y);
}

function subtractAsync() {

    // Create the math object (starts in sync mode)
    var m = new math();
    
    // The alternative approach to switch to async mode
    m.Async(MathHandler);
    
    m.subtract(document.getElementById("x").value,document.getElementById("y").value);
    
}

function divideAsync() {

    var m = new math(MathHandler);
    m.divide(document.getElementById("x").value,document.getElementById("y").value);

}

// Now use the colors client... (sync)
function listColors() {

    var c = new colors();
    var result = c.listcolors();

    if ( result ) {
        clear();
        echo ('<h2>A Short List of Colors</h2>',true);
        for (var i=0;i<result.length;i++) {
            echo ('<br>',true);
            for (prop in result[i]) {
                if ( prop == 'toPHP' || prop == 'var_dump' ) {
                    continue;
                }
                echo (result[i][prop]+' : ',true);
            }
        } 
    }
    
}

// Handler for asynchronous calls to colors
var ColorsHandler = {
    listcolors: function(result) {
        clear();
        echo ('<h2>A Short List of Colors</h2>',true);
        for (var i=0;i<result.length;i++) {
            echo ('<br>',true);
            for (prop in result[i]) {
                if ( prop == 'toPHP' || prop == 'var_dump' ) {
                    continue;
                }
                echo (result[i][prop]+' : ',true);
            }
        }
    },
    listcolorsError: function(e) {
        alert(e.message);
    }
}

// Synchronous call to colors.listcolors()...
function listColorsAsync() {
    var c = new colors();
    c.Async(ColorsHandler);
    c.listcolors();
}

function echo(string) {
    if ( !arguments[1] ) {
        clear();
    }
    document.getElementById("results").innerHTML += string;
}

function clear() {
    document.getElementById("results").innerHTML = "";
}

-->
</script>
</head>
<body>
<h1> PostOffice Server Demo with generated client </h1>
<p> Server provided by JPSpan_Server_PostOffice and Javascript client is also generated.</p>
<form id="mathForm">
X: <input id="x" type="text" value="2" size="2"><br>
Y: <input id="y" type="text" value="2" size="2"><br>
<input type="button" onClick="add()" value="Add [Sync]"> : <input type="button" onClick="addAsync()" value="Add [Async]"><br>
<input type="button" onClick="subtract()" value="Subtract [Sync]"> : <input type="button" onClick="subtractAsync()" value="Subtract [Async]"><br>
<input type="button" onClick="divide()" value="Divide [Sync]"> : <input type="button" onClick="divideAsync()" value="Divide [Async]"> (see what happens if you set Y to zero)<br>
</form>
<p>
<a href="javascript:listColors()">colors.listcolors</a> [sync] : <a href="javascript:listColorsAsync()">colors.listcolors</a> [async]
</p>
<h2>Response from Server</h2>
<div id="results">
</div>
</body>
</html>
