<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';

/**
 * This class contains all the function that are called using AJAX (dojo)
 */
class CRM_Core_Page_AJAX extends CRM_Core_Page 
{
    /**
     * Run the page
     */
    function run( &$args ) 
    {
        $this->invoke( $args );
        exit( );
    }
    
    /**
     * Invoke function that redirects to respective functions
     */
    function invoke( &$args ) 
    {
        // intialize the system
        $config =& CRM_Core_Config::singleton( );
        
        if ( $args[0] != 'civicrm' && $args[1] != 'ajax' ) {
            exit( );
        }
        
        switch ( $args[2] ) {

        // civicrm/ajax/status -> CRM/Core/Page/Ajax/Import.php
        case 'status':
	    require_once "CRM/Core/Page/AJAX/Import.php";
	    return CRM_Core_Page_AJAX_Import::status( $config );
	    
        // civicrm/ajax/pledgeName -> CRM/Pledge/Page/Ajax.php                       
        case 'pledgeName':
	    require_once "CRM/Pledge/Page/AJAX.php";
	    return CRM_Pledge_Page_AJAX::pledgeName( $config );

        // civicrm/ajax/caseSubject -> CRM/HRDCase/Page/Ajax.php
        case 'caseSubject':
	    require_once "CRM/HRDCase/Page/AJAX.php";
	    return CRM_HRDCase_Page_AJAX::caseSubject( $config );

        // civicrm/ajax/template -> CRM/Mail/Page/Ajax.php
        case 'template':
	  require_once "CRM/Mailing/Page/AJAX.php";
	  return CRM_Mailing_Page_AJAX::template( $config );

        // civicrm/ajax/custom -> CRM/           
        case 'custom':
            require_once "CRM/Core/Page/AJAX/Contact.php";
            return CRM_Core_Page_AJAX_Contact::customField( $config );

        // civicrm/ajax/contact -> Core/Page/Ajax/Search.php
        case 'contact':
	    require_once "CRM/Core/Page/AJAX/Search.php";
	    return CRM_Core_Page_AJAX_Search::contact( $config );

        // civicrm/ajax/           
        case 'employer':
            require_once "CRM/Core/Page/AJAX/Contact.php";
            return CRM_Core_Page_AJAX_Contact::getPermissionedEmployer( $config );

        // civicrm/ajax/           
        case 'mapper':
            require_once 'CRM/Core/Page/AJAX/Mapper.php';
            $method = array( 'CRM_Core_Page_AJAX_Mapper',
                             $args[3] );

            if ( is_callable( $method ) ) {
                return eval( "return CRM_Core_Page_AJAX_Mapper::{$args[3]}( " . ' $config ); ' );
            }
            exit( );

        // civicrm/ajax/           
        case 'groupTree':
            require_once "CRM/Core/Page/AJAX/Contact.php";
            return CRM_Core_Page_AJAX_Contact::groupTree( $config );

        // civicrm/ajax/           
        case 'permlocation':
            require_once "CRM/Core/Page/AJAX/Location.php";        
            return CRM_Core_Page_AJAX_Location::getPermissionedLocation( $config );

        // civicrm/ajax/           
        case 'activity':
            require_once 'CRM/Core/Page/AJAX/Activity.php';
            return CRM_Core_Page_AJAX_Activity::getCaseActivity( $config );

        // civicrm/ajax/           
        case 'activitytypelist':
            require_once 'CRM/Core/Page/AJAX/Activity.php';
            return CRM_Core_Page_AJAX_Activity::getActivityTypeList( $config );

        // civicrm/ajax/           
        case 'contactlist':
            require_once 'CRM/Core/Page/AJAX/Contact.php';
            return CRM_Core_Page_AJAX_Contact::getContactList( $config );

        // civicrm/ajax/                       
        case 'relation':
            require_once 'CRM/Core/Page/AJAX/Contact.php';
            return CRM_Core_Page_AJAX_Contact::relationship( $config );

        default:
	  return;
	}
    }




}
