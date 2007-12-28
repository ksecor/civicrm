<?php

require_once 'bootstrap_common.php';

/**
 * Require the OpenID consumer code.
 */
require_once "Auth/OpenID/Consumer.php";

/**
 * Require the "MySQL store" module, which we'll need to store OpenID
 * information.
 */
require_once "Auth/OpenID/MySQLStore.php";

/**
 * Setup the database store for the OpenID sessions.
 */
$dao =& new CRM_Core_DAO();
if (defined('CIVICRM_DSN')) {
    $dsn = CIVICRM_DSN;
}
$dao->init($dsn);
$connection =& $dao->getDatabaseConnection();
$settings_table = "civicrm_openid_settings";
$associations_table = "civicrm_openid_associations";
$nonces_table = "civicrm_openid_nonces";

$store = new Auth_OpenID_MySQLStore($connection,
    $associations_table,$nonces_table);

/**
 * Create a consumer object using the store object created earlier.
 */
$consumer = new Auth_OpenID_Consumer($store);

?>
