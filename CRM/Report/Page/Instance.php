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
require_once 'CRM/Report/Utils/Report.php';
require_once 'CRM/Report/BAO/Instance.php';

/**
 * Page for invoking report instances
 */
class CRM_Report_Page_Instance extends CRM_Core_Page 
{

    /**
     * run this page (figure out the action needed and perform it).
     *
     * @return void
     */
    function run() {
        $instanceId   = CRM_Utils_Request::retrieve( 'id', 'Positive', $this, true );
        $action       = CRM_Utils_Request::retrieve( 'action', 'String', $this );
        $optionVal    = CRM_Report_Utils_Report::getValueFromUrl( $instanceId );
        $reportUrl    = CRM_Utils_System::url('civicrm/report/list', "reset=1");

        if ( $action & CRM_Core_Action::DELETE ) {
            if ( !CRM_Core_Permission::check( 'access CiviReport' ) ) {
                $statusMessage = ts( 'Your do not have permission to Delete Report.' );
                CRM_Core_Error::statusBounce( $statusMessage,
                                              $reportUrl );
            }
            CRM_Report_BAO_Instance::delete( $instanceId );

            CRM_Core_Session::setStatus( ts( 'Selected Instance has been deleted.' ) );
        } else {
            require_once 'CRM/Core/OptionGroup.php';
            $templateInfo = CRM_Core_OptionGroup::getRowValues( 'report_template', "{$optionVal}", 'value' );

            if ( strstr($templateInfo['name'], '_Form') ) {
                $instanceInfo = array( );
                CRM_Report_BAO_Instance::retrieve( array('id' => $instanceId), $instanceInfo );
                
                if ( ! empty($instanceInfo['title']) ) {
                    CRM_Utils_System::setTitle( $instanceInfo['title'] );
                    $this->assign( 'reportTitle', $instanceInfo['title'] );
                } else {
                    CRM_Utils_System::setTitle( $templateInfo['label'] );
                    $this->assign( 'reportTitle', $templateInfo['label'] );
                }

                $wrapper =& new CRM_Utils_Wrapper( );
                return $wrapper->run( $templateInfo['name'], null, null );
            }
            
            CRM_Core_Session::setStatus( ts( 'Could not find template for the instance.' ) );
        }
        return CRM_Utils_System::redirect( $reportUrl );
    }
}
