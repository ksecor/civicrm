<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';

/**
 * Page for invoking report templates
 */
class CRM_Report_Page_Report extends CRM_Core_Page 
{

    /**
     * run this page (figure out the action needed and perform it).
     *
     * @return void
     */
    function run() {
        $config =& CRM_Core_Config::singleton( );
        $args   = explode( '/', $_GET[$config->userFrameworkURLVar] );

        // remove 'civicrm/report' from args
        array_shift($args);
        array_shift($args);

        // put rest of arguement back in the form of url, which is how value 
        // is stored in option value table
        $optionVal = implode( '/', $args );

        require_once 'CRM/Core/OptionGroup.php';
        $templateInfo = CRM_Core_OptionGroup::getRowValues( 'report_list', "{$optionVal}", 'value' );

        if ( strstr($templateInfo['name'], '_Form') ) {
            CRM_Utils_System::setTitle( $templateInfo['label'] );

            $wrapper =& new CRM_Utils_Wrapper( );
            return $wrapper->run( $templateInfo['name'], null, null );
        }

        CRM_Core_Session::setStatus( ts( 'Could not find the report template. Make sure the report template is registered and / or url is correct.' ) );
        return CRM_Utils_System::redirect( CRM_Utils_System::url('civicrm/report/list', "reset=1") );
    }
}
