<?php
if ( !isset($_GET['launch']) || $_GET['launch'] != 'xulmail' ) {
?>
<html>
<head>
<title>Launch XULMail</title>
<script language="Javascript" type="text/javascript">
function loadXul(url,title) {
    var width=500;
    var height=400;
	// check that we're using mozilla
	// if not, ask user does he want to proceed anyway
	if (checkIsMoz() ||
	window.confirm("This application is designed for the mozilla browser. You do not appear to be using mozilla at the moment, do you want to continue anyway?")
		) {
		// set the features of the new window
		var features = "centerscreen,chrome,close,titlebar";
		// including width and height if specified
		if (width && height) {
			features += ",width=" + width + ",height=" + height;
		}
		// set the title if not set
		if (!title) {
			title = "Mozilla";
		}
		// if nn7, just open the xul window (doesn't work otherwise, for some reason)
		if (checkIsNN7()) {
			window.open(url,'xul_window',features);
		} else {
			// open new blank window 
			var xulwin = window.open('','xul_window',features);
			// set the window to display some text while the xul page is loading
			var loadingtext = "Loading...";
			xulwin.document.write("<html><head><title>" + title + "</title></head><body><br><p align='center'><strong>" + loadingtext + "</strong></p></body></html>");
			xulwin.document.close();
			// reload with xul location
			xulwin.location = url;
		}
	}
}
function checkIsMoz() {
	return (navigator.userAgent.indexOf("Gecko") != -1);
}

function checkIsNN7() {
	return (navigator.userAgent.indexOf("Netscape/7") != -1);
}
</script>
</head>
<body>
<h1>Launch XULMail</h1>
<a href="javascript:loadXul('<?php echo $_SERVER['PHP_SELF'];?>?launch=xulmail','XULMail');">Launch</a>
</body>
</html>
<?php
    die();
}
require_once '../JPSpan.php';

require_once JPSPAN . 'Include.php';
JPSpan_Include_Register('util/data.js');
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

header ( 'Content-type: application/vnd.mozilla.xul+xml' );
echo '<?xml version="1.0"?>';
?>
<window title="XULMail"
	xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul"
	xmlns:html="http://www.w3.org/1999/xhtml"
        onload="init();">
    <?php echo '<?xml-stylesheet href="xulmail.css" type="text/css"?>';?>
    <script type="application/x-javascript">
    <![CDATA[
    <?php JPSpan_Includes_Display(); ?>
    
    function var_dump(data) {
        var Data = new JPSpan_Util_Data();
        return Data.dump(data);
    }
    
    function serialize(data) {
        var Encoder = new JPSpan_Encode_Xml();
        return Encoder.encode(data);
    }
    
    var httpRequest = false;
    // Modify this
    var mailUrl = "<?php path(); ?>/mailresponder.php";
    function getRequest() {
        if ( !httpRequest ) {
            httpRequest = new XMLHttpRequest();    
        }
        return httpRequest;
    }
    function makeRequest(targetUrl) {
        var httpRequest = getRequest();
        httpRequest.open("GET", targetUrl, false, false, false);
        httpRequest.send("");
        switch ( httpRequest.status ) {
            case 200:
                return httpRequest.responseText;
            break;
            default:
                alert("Problem accessing url: "+targetUrl+" Code: "+httpRequest.status);
                return null;
            break;
        }       
    }
    function getMessages() {
        response = makeRequest(mailUrl);
        try {
            datafunc = eval(response);
        } catch (e) {
            alert("Problem fetching messages");
            return null;
        }
        try {
            result = datafunc();
            return result;
        } catch (e) {
            alert(e.name+": "+e.message);
            return null;
        }
    }
    function getMessage(mid,pid) {
        var messageUrl = mailUrl + "?mid=" + mid + "&pid=" + pid;
        response = makeRequest(messageUrl);
        try {
            datafunc = eval(response);
        } catch (e) {
            alert("Invalid response from server");
            return null;
        }
        try {
            result = datafunc();
            return result;
        } catch ( e ) {
            alert(e.name+": "+e.message);
            return null;
        }
    }
    function displayMessage(id) {
        id = id.split(':');
        message = getMessage(id[0],id[1]);
        if ( !message ) {
            alert ('Unable to display message');
            return;
        }
        subject = document.getElementById('subject');
        subject.setAttribute('value',message.subject);
        from = document.getElementById('from');
        from.setAttribute('value',message.from);
        date = document.getElementById('date');
        date.setAttribute('value',message.date);
        body = document.getElementById('body');
        body.setAttribute('value',message.body);
    }
    function init() {
        var messages = getMessages();
        var messageList = document.getElementById('messageList');
        var listhead = messageList.firstChild;
        var listitem;
        while ( listitem = listhead.nextSibling ) {
            messageList.removeChild(listitem);
        }
        for(var i=0;i<messages.length;i++) {
            messagec = document.createElement('listitem');
            messagec.setAttribute('id',messages[i].mid);
            subjectc = document.createElement('listcell');
            subjectc.setAttribute('label',messages[i].subject);
            messagec.appendChild(subjectc);
            datec = document.createElement('listcell');
            datec.setAttribute('label',messages[i].date);
            messagec.appendChild(datec);
            fromc = document.createElement('listcell');
            fromc.setAttribute('label',messages[i].from);
            messagec.appendChild(fromc);
            messagec.setAttribute('onclick',
                'displayMessage("'+messages[i].mid+':'+messages[i].pid+'");');
            messageList.appendChild(messagec);
        }
    }
    ]]>
    </script>
    <vbox>
        <hbox>
            <label id="title" value="XULMail" flex="1"/>
            <button id="refresh" label="Refresh" onclick="init();"/>
        </hbox>
        <hbox flex="1">
            <listbox flex="1" id="messageList">
                <listhead>
                    <listheader label="Subject"/>
                    <listheader label="Date"/>
                    <listheader label="From"/>
                </listhead>
            </listbox>
        </hbox>
        <vbox>
            <grid flex="1" id="messageBody">
                <columns>
                    <column/>
                    <column flex="1"/>
                </columns>
                <rows>
                    <row>
                        <label value="Subject"/>
                        <textbox id="subject" readonly="true"/>
                    </row>
                    <row>
                        <label value="From"/>
                        <textbox id="from" readonly="true"/>
                    </row>
                    <row>
                        <label value="Date"/>
                        <textbox id="date" readonly="true"/>
                    </row>
                </rows>
            </grid>
            <textbox id="body" readonly="true" multiline="true" rows="4" flex="1"/>
        </vbox>
    </vbox>
</window>