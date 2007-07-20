<?php

require_once 'auth_common.php';
require_once "CRM/Core/BAO/UFMatch.php";
$ar = CRM_Core_BAO_UFMatch::getContactIDs();
if (count($ar) > 0) {
  header("Location:login.php");
  exit(0);
}
?>
<html>
<head><title>New Installation-Add User</title></head>
<body>
<h1 class = "title">CiviCRM Registration</h1>
<h2>Congratulations! It appears you've just installed CiviCRM Standalone. Let's setup your first user account. You can do that by filling out the form below.</h2>
<div id="verify-form">
<form method="post" action="add_user.php">
  Identity&nbsp;URL:
  <input type="text" name="openid_url" value="" /> (i.e. http://me.myopenid.com/ don't forget the trailing slash!) <br/><br/>
  First Name:
  <input type="text" name="first_name" value="" /><br/><br/>
  Last Name:
  <input type="text" name="last_name" value="" /><br/><br/>
  Email Address:
  <input type="text" name="email" value="" /><br/><br/>
  <input type="submit" value="Create User">
  </form>
  </div>
  <p> If you don't have an OpenID yet, go to <a href="http://www.myopenid.com/">My OpenID to get one</a>.</p>
  </body>
  </html>
