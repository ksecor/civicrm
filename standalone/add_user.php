<?php
require_once 'auth_common.php';
require_once "CRM/Core/BAO/UFMatch.php";
$ar = CRM_Core_BAO_UFMatch::getContactIDs();
if ( ! empty( $ar[0] ) ) {
  header("Location:login.php");
  exit(0);
}
$openid = $_POST['openid_url'];
$firstname = $_POST['first_name'];
$lastname = $_POST['last_name'];
$email = $_POST['email'];
require_once 'bootstrap_common.php';
require_once 'CRM/Core/BAO/UFMatch.php';
require_once 'user.php';
require_once 'CRM/Utils/System/Standalone.php';
$user =& new Standalone_User($openid, $email, $firstname, $lastname);

$session =& CRM_Core_Session::singleton();

CRM_Core_BAO_UFMatch::synchronize($user, true, "Standalone", "Individual", true);
$contactId = $session->get('userID');
CRM_Core_BAO_UFMatch::setAllowedToLogin($contactId, 1);
header("Location:try_auth.php?openid_url=$openid");
exit(0);
?>
