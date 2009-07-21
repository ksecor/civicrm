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

/**
 * This class contains all the function that are called using AJAX
 */
class CRM_Admin_Page_AJAX
{
    /**
     * Function to build menu tree     
     */    
    static function getNavigationList( ) {
        require_once 'CRM/Core/BAO/Navigation.php';
        echo CRM_Core_BAO_Navigation::buildNavigation( true );           
        exit();
    }
    
    /**
     * Function to process drag/move action for menu tree
     */
    static function menuTree( ) {
        require_once 'CRM/Core/BAO/Navigation.php';
        echo CRM_Core_BAO_Navigation::processNavigation( $_GET );           
        exit();
    }

    /**
     * Function to build status message while 
     * enabling/ disabling various objects
     */
    static function getStatusMsg( &$config ) 
    {        
        $recordID  = CRM_Utils_Type::escape( $_POST['recordID'], 'Integer' );
        $recordBAO = CRM_Utils_Type::escape( $_POST['recordBAO'], 'String' );
        $op        = CRM_Utils_Type::escape( $_POST['op'], 'String' );
        
        if ($op == 'disable-enable') {
            $status = ts('Are you sure you want to enable this record?');
        } else {
            switch ($recordBAO) {
                
            case 'CRM_Core_BAO_UFGroup':
                require_once(str_replace('_', DIRECTORY_SEPARATOR, $recordBAO) . ".php");
                $method = 'getUFJoinRecord'; 
                $result = array($recordBAO,$method);
                $ufJoin = call_user_func_array(($result), array($recordID,true));
                $status = ts('This profile is currently used for ') . implode (', ' , $ufJoin) . ts('. If you disable the profile - it will be removed from these forms and/or modules. Do you want to continue?');
                break;
                
            case 'CRM_Contribute_BAO_ManagePremiums':
                $status = ts('Are you sure you want to disable this premium? This action will remove the premium from any contribution pages that currently offer it. However it will not delete the premium record - so you can re-enable it and add it back to your contribution page(s) at a later time.');
                break;

            case 'CRM_Contribute_BAO_ContributionType':
                $status = ts('Are you sure you want to disable this contribution type?');
                break;
                
            case 'CRM_Core_BAO_PaymentProcessor':
                $status = ts('Are you sure you want to disable this payment processor? Users will no longer be able to select this value when adding or editing transaction pages.');
                break;
                
            case 'CRM_Core_BAO_LocationType':
                $status = ts('Are you sure you want to disable this location type? Users will no longer be able to select this value when adding or editing contact locations.');
                break;
                
            case 'CRM_Mailing_BAO_Component':
                $status = ts('Are you sure you want to disable this component?');
                break;
                
            case 'CRM_Core_BAO_CustomField':
                $status = ts('Are you sure you want to disable this custom data field?');
                break;
                
            case 'CRM_Core_BAO_CustomGroup':
                $status = ts('Are you sure you want to disable this custom data group? Any profile fields that are linked to custom fields of this group will be disabled.');
                break;

            case 'CRM_Core_BAO_MessageTemplates':
                $status = ts('Are you sure you want to disable this message tempate?');
                break;
                
            case 'CRM_ACL_BAO_ACL':
                $status = ts('Are you sure you want to disable this ACL?');
                break;
                
            case 'CRM_ACL_BAO_EntityRole':
                $status = ts('Are you sure you want to disable this ACL Role Assignment?');
                break;
                
            case 'CRM_Member_BAO_MembershipStatus':
                $status = ts('Are you sure you want to disable this membership status rule?');
                break;
                
            case 'CRM_Core_BAO_PriceField':
                $status = ts('Are you sure you want to disable this price field?');
                break;
                
            case 'CRM_Core_BAO_OptionGroup':
                $status = ts('Are you sure you want to disable this Option?');
                break;
                
            case 'CRM_Core_BAO_OptionValue':
                $status = ts('Are you sure you want to disable this Option Value?');
                break;

            default:
                $status = ts('Are you sure you want to disable this record?');
                break;
            }
        }
        $statusMessage['status'] = $status;
        echo json_encode( $statusMessage );
        
        exit;
    } 
}
