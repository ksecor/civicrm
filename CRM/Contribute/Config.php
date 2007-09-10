<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.9                                                |
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
 * Config handles all the run time configuration changes that the system needs to deal with.
 * Typically we'll have different values for a user's sandbox, a qa sandbox and a production area.
 * The default values in general, should reflect production values (minimizes chances of screwing up)
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Config.php';

class CRM_Contribute_Config {

    /**
     * Function to add additional config paramters to the core Config class
     * if CiviContribute is enabled
     *
     * Note that this config class prevent code bloat in the Core Config class,
     * however we declare all the variables assigned here, more for documentation
     * than anything else, at some point, we'll figure out how to extend a class
     * and properties dynamically in PHP (like Ruby)
     *
     * @param CRM_Core_Config (reference ) the system config object
     *
     * @return void
     * @static
     * @access public
     */
    static function add( &$config, $oldMode = false ) {
    }

    /**
     * verify that the needed parameters have been set of SMS to work
     *
     * @param CRM_Core_Config (reference ) the system config object
     *
     * @return boolean
     * @static
     * @access public
     */
    static function check( &$config ) {
        $requiredParameters = array( 'smsUsername', 'smsPassword', 'smsAPIID', 'smsAPIServer' );
        return CRM_Core_Config::check( $config, $requiredParameters );
    }

}


