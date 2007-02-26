<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | copyright CiviCRM LLC (c) 2004-2007                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions      |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://civicrm.org/licensing/                                      |
 +--------------------------------------------------------------------+
*/

session_start( );

require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';

require_once 'CRM/Utils/REST.php';
$rest =& new CRM_Utils_REST();

/* Cache the real UF, override it with the SOAP environment */
$config =& CRM_Core_Config::singleton();

$config->userFramework          = 'Soap';
$config->userFrameworkClass     = 'CRM_Utils_System_Soap';
$config->userHookClass          = 'CRM_Utils_Hook_Soap';

echo
  "<civicrm_api>\n" .
  CRM_Utils_Array::xml( $rest->run( $config ) ) .
  "</civicrm_api>\n";

?>
