<?php
// $Id: sfsearch.php,v 1.2 2004/11/16 21:03:50 harryf Exp $
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
<title> Sourceforge Project Search </title>
<script type="text/javascript" src="<?php path(); ?>/sfsearch_server.php?client"></script>
<script type="text/javascript">

function getProjects(input, evt) {

    if (input.value.length == 0) {
        return;
    }
    //allow backspace to work in IE
    if (typeof input.selectionStart == 'undefined' && evt.keyCode == 8) { input.value = input.value.substr(0,input.value.length-1); }
  
    switch (evt.keyCode) {
        case 37: //left arrow
        case 39: //right arrow
        case 33: //page up  
        case 34: //page down  
        case 36: //home  
        case 35: //end
        case 13: //enter
        case 9: //tab
        case 27: //esc
        case 16: //shift  
        case 17: //ctrl  
        case 18: //alt  
        case 20: //caps lock
        case 8: //backspace  
        case 46: //delete 
        case 38: //up arrow 
        case 40: //down arrow
            return;
        break;
    }
    
    var s = new sfsearch(ProjectsHandler);
    s.timeout = 3000;
    // Ignore timeouts
    s.clientErrorFunc = function(e) {
        if ( e.code == 1003 ) {
            // Ignore...
        } else {
            alert(e);
        }
    }
    s.getprojects(input.value);
}

var ProjectsHandler = {
    getprojects: function(displayFunc) {
        displayFunc(document.getElementById('results'));
    }
}
-->
</script>
</head>
<body>
<h1> Sourceforge Project Search </h1>
<form id="projectForm">
Enter a Sourceforge Project Name (Unix style): 
<input type="text" value="" onkeyup="getProjects(this,event);" autocomplete="off"><br>
(three chars before stuff happens - obviously won't work away from Sourceforge)
</form>
<p id="results"></p>
</body>
</html>
