<?php
// $Id: autocomplete.php,v 1.3 2004/11/23 14:09:58 harryf Exp $
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
<title> State - Country Autocompletion </title>
<script type="text/javascript" src="<?php path(); ?>/stateProvinceServer.php?client"></script>
<script type="text/javascript">

function getWord(input, evt) {

    if (input.value.length == 0) {
        return;
    }
    
    //allow backspace to work in IE
    if (typeof input.selectionStart == 'undefined' && evt.keyCode == 8) { input.value = input.value.substr(0,input.value.length-1); }

    // Ignore the following keystrokes
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

    // Remember the current length to allow selection
    CompletionHandler.lastLength = input.value.length;
    
    // Create the remote client
    var a = new autocomplete(CompletionHandler);
    
    // Set a timeout for responses which take too long
    a.timeout = 3000;
    
    // Ignore timeouts
    a.clientErrorFunc = function(e) {
        if ( e.code == 1003 ) {
            // Ignore...
        } else {
            alert(e);
        }
    }
          
    // Call the remote method
    a.getword(input.value);
}

// Callback handler
var CompletionHandler = {

    lastLength: 0,
    
    // Callback method
    getword: function(result) {
        if (result.length < 1 ) {
            return;
        }        

        var input = document.getElementById('state');
        input.value = result.pop();

        var input1 = document.getElementById('state_id');
        input1.value = result.length;

        var b = new autocomplete(CompletionHandlerCountry);
        b.getcountry(input1.value);

        try {
            input.setSelectionRange(this.lastLength, input.value.length);
        } catch(e) {
        }
    }

}

// Callback handler
var CompletionHandlerCountry = {

    lastLength: 0,

    // Callback method
    getcountry: function(result) {
        
        if (result.length < 1 ) {
            return;
        }
        
        var input = document.getElementById('country');
        input.value = result.pop();

        var input1 = document.getElementById('country_id');
        input1.value = result.length;

        try {
            input.setSelectionRange(this.lastLength, input.value.length);
        } catch(e) {
        }
    }
}


-->
</script>
</head>
<body>
<form id="autoCompleteForm" name="autoCompleteForm">
Enter a State: <input type="text" id="state" name="state" value="" onkeyup="getWord(this,event);" autocomplete="off" onblur="getWord(this,event);">
<!-- Note the autocomplete="off": without it you get errors like;

"Permission denied to get property XULElement.selectedIndex..."
-->
state id: <input type="text" id="state_id" name="state_id" value="">
<br>
Country :<input type="text" id="country" name ="country" READONLY>
country id: <input type="text" name="country_id" id="country_id" value="">
</form>
</body>
</html>
