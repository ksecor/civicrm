<?php
global $skipConfigError;
$skipConfigError = true;

require_once 'auth_common.php';
require_once "CRM/Core/BAO/UFMatch.php";
$contactIds = CRM_Core_BAO_UFMatch::getContactIDs();
if ( count( $contactIds ) > 0 ) {
  header("Location:login.php");
  exit(0);
}
$session =& CRM_Core_Session::singleton( );
$session->set( 'new_install', true );
?>
<html>
<head>
  <title>CiviCRM Installation - Admin Login</title>
  <link rel="stylesheet" type="text/css" href="../install/template.css" />
</head>
<body>
<div id="All">
<h1 class="title">CiviCRM First User Setup</h1>
<h3>Congratulations! You've successfully installed CiviCRM Standalone. Let's setup your first user account (which will be the admin account). Start by entering your OpenID below.</h3>

<div id="verify-form">
 <form method="post" action="try_auth.php">
  Identity&nbsp;URL:
  <input id="openid_identifier" type="text" name="openid_identifier" value="" /> (for example: me.myopenid.com) <br/><br/>
  <input type="submit" value="Verify">
 </form>
</div>
  <p> If you don't have an OpenID yet, go to <a href="http://www.myopenid.com/">My OpenID to get one</a>.</p>

</div>
</body>
</html>
