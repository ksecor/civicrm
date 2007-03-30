<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

session_start( );

require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';

/* Cache the real UF, override it with the SOAP environment */
$config =& CRM_Core_Config::singleton();

$config->userFramework          = 'Soap';
$config->userFrameworkClass     = 'CRM_Utils_System_Soap';
$config->userHookClass          = 'CRM_Utils_Hook_Soap';

require_once 'CRM/Utils/Array.php';
$value = CRM_Utils_Array::value( 'module', $_GET );

switch ( $value ) {
 case 'contribute':
     require_once 'CRM/Contribute/Payment/PayPalIPN.php';
     CRM_Contribute_Payment_PayPalIPN::main( );
     break;
 case 'event':
     require_once 'CRM/Event/Payment/PayPalIPN.php';
     CRM_Event_Payment_PayPalIPN::main( );
     break;
 default     :
     require_once 'CRM/Core/Error.php';
     CRM_Core_Error::debug_log_message( "PayPalIPN path not available" );
     break;
 }

?>
