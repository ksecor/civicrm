<?php

// escape early if called directly
defined('_JEXEC') or die('No direct access allowed'); 

function com_uninstall()
{
    require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'civicrm.settings.php';

    require_once 'CRM/Core/Config.php';
    $config =& CRM_Core_Config::singleton( );

    require_once 'CRM/Core/DAO.php';
    CRM_Core_DAO::dropAllTables( );

	echo "You have uninstalled CiviCRM. All CiviCRM related tables have been dropped from the database.";
}


