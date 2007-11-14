<?php
/*
 * Copyright (C) 2007 Jacob Singh, Sam Lerner
 * Licensed to CiviCRM under the Academic Free License version 3.0.
 *
 */
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>widget</title>
</head>
<body bgcolor="#ffffff">
<!--url's used in the movie-->
<!--text used in the movie-->
<!--
Arguments
Actions
Result
-->

<?php ?>
<?php $flashVars = "serviceUrl=" .urlencode('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/amfphp/gateway.php');?>
<!-- saved from url=(0013)about:internet -->
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="550" height="400" id="widget" align="middle">
<param name="allowScriptAccess" value="sameDomain" />
<param name="FlashVars" value="<?php print $flashVars?>">
<param name="movie" value="widget.swf" /><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" />
<embed flashvars="<?php print $flashVars?>" src="widget.swf" quality="high" bgcolor="#ffffff" width="220" height="220" name="widget" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>
</body>
</html>
