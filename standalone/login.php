<?php

require_once 'auth_common.php';  
require_once "CRM/Core/BAO/UFMatch.php";

$ar = CRM_Core_BAO_UFMatch::getContactIDs();
if (empty($ar[0])){
  header("Location:new_install.php");
exit(0);
}

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

<head>
  <title>CiviCRM User Authentication</title>
  <style type="text/css">
    <!--
    /* TODO: Move this to a better location so it doesn't have to be repeated */
    /* OpenID logo for login / registration */
    #openid_url {
      background: #FFFFFF url('i/openid-icon-small.gif') no-repeat scroll 0pt 50%;
      padding-left: 18px;
    }
    -->
    </style>
</head>
<body>
  <h1 class="title">CiviCRM Login</h1>
    <h2>Please enter your OpenID</h2>

     <div id="verify-form">
   <form method="get" action="try_auth.php">
        Identity&nbsp;URL:
        <input type="hidden" name="action" value="verify" />
        <input id="openid_url" type="text" name="openid_url" value="" />
        <input type="submit" value="Verify" />
      </form>
    </div>
   <p>If you don't have an OpenID yet, go to <a href="http://www.myopenid.com/">MyOpenID to get one</a>.</p>
</body>
</html>