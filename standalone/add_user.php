<?php

/********************************************
 * This is currently not used; ignore.
 *
 * WSM - 12/27/07
 ********************************************/

require_once 'auth_common.php';
require_once 'CRM/Core/BAO/UFMatch.php';
$ar = CRM_Core_BAO_UFMatch::getContactIDs();
if ( ! empty( $ar[0] ) ) {
  header("Location:login.php");
  exit(0);
}
$openid = $_POST['openid_url'];
$firstname = $_POST['first_name'];
$lastname = $_POST['last_name'];
$email = $_POST['email'];
//require_once 'CRM/Utils/System/Standalone.php';
$user = array( 'openid'    => $openid,
               'firstname' => $firstname,
               'lastname'  => $lastname,
               'email'     => $email );
$session =& CRM_Core_Session::singleton();
$session->set('user', $user);
$session->set('new_install', true);

header("Location:try_auth.php?openid_url=$openid");
exit(0);

