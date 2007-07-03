<?php
require_once 'auth_common.php';
require_once "CRM/Core/BAO/UFMatch.php";
$ar = CRM_Core_BAO_UFMatch::getContactIDs();
if (!empty($ar[0])){
  header("Location:login.php");
  exit(0);
 }
$ufname = $_GET['openid_url'];
$firstname = $_GET['firstname'];
$lastname = $_GET['lastname'];
$email = $_GET['email'];
require_once 'bootstrap_common.php';
require_once 'CRM/Core/BAO/UFMatch.php';
require_once 'user.php';
require_once 'CRM/Utils/System/Standalone.php';
$user =& new Standalone_User($ufname, $email, $firstname, $lastname);
CRM_Core_BAO_UFMatch::synchronize($user, true, "Standalone", "Individual");
header("Location:try_auth.php?openid_url=$ufname");
exit(0);
?>
