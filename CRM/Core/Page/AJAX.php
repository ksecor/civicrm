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

        // civcrm/ajax/search -> CRM/Core/Page/Ajax/Search.php
        case 'search':
	  require_once "CRM/Core/Page/AJAX/Search.php";
	  return CRM_Core_Page_AJAX_Search::search( $config );
	  
	  // civicrm/ajax/state -> CRM/Core/Page/Ajax/Location.php
        case 'state':
	    require_once "CRM/Core/Page/AJAX/Location.php";
            return CRM_Core_Page_AJAX_Location::state( $config );

        // civicrm/ajax/state -> CRM/Core/Page/Ajax/Location.php
        case 'country':
	    require_once "CRM/Core/Page/AJAX/Location.php";
	    return CRM_Core_Page_AJAX_Location::country( $config );
            
        // civicrm/ajax/status -> CRM/Core/Page/Ajax/Import.php
        case 'status':
	    require_once "CRM/Core/Page/AJAX/Import.php";
	    return CRM_Core_Page_AJAX_Import::status( $config );
            
        // civicrm/ajax/event -> CRM/Event/Page/Ajax.php
        case 'event':
	    require_once "CRM/Event/Page/AJAX.php";
	    return CRM_Event_Page_AJAX::event( $config );
	   
        // civicrm/ajax/eventType -> CRM/Event/Page/Ajax.php
        case 'eventType':
   	    require_once "CRM/Event/Page/AJAX.php";
	    return CRM_Event_Page_AJAX::eventType( $config );

        // civicrm/ajax/eventFee -> CRM/Event/Page/Ajax.php
        case 'eventFee':
	    require_once "CRM/Event/Page/AJAX.php";
	    return CRM_Event_Page_AJAX::eventFee( $config );

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
            return $this->customField( $config );

        // civicrm/ajax/help
        case 'help':
            return $this->help( $config );

        // civicrm/ajax/contact -> Core/Page/Ajax/Search.php
        case 'contact':
	    require_once "CRM/Core/Page/AJAX/Search.php";
	    return CRM_Core_Page_AJAX_Search::contact( $config );

        // civicrm/ajax/           
        case 'employer':
            return $this->getPermissionedEmployer( $config );

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
            return $this->groupTree( $config );

        // civicrm/ajax/           
        case 'permlocation':
            return $this->getPermissionedLocation( $config );

        // civicrm/ajax/           
        case 'memType':
            return $this->getMemberTypeDefaults( $config );

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

    /**
     * Function to fetch the custom field help 
     */
    function customField( &$config ) 
    {
        require_once 'CRM/Utils/Type.php';
        $fieldId = CRM_Utils_Type::escape( $_GET['id'], 'Integer' );

        $helpPost = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomField',
                                                 $fieldId,
                                                 'help_post' );
        echo $helpPost;
    }

    
    /**
     * Function to obtain list of permissioned employer for the given contact-id.
     */
    function getPermissionedEmployer( &$config ) 
    {
        $cid       = CRM_Utils_Type::escape( $_GET['cid'], 'Integer' );
        $name      = trim(CRM_Utils_Type::escape( $_GET['name'], 'String')); 
        $name      = str_replace( '*', '%', $name );

        require_once 'CRM/Contact/BAO/Relationship.php';
        $elements = CRM_Contact_BAO_Relationship::getPermissionedEmployer( $cid, $name );

        require_once "CRM/Utils/JSON.php";
        echo CRM_Utils_JSON::encode( $elements, 'value');
    }

    /**
     * Function to obtain the location of given contact-id. 
     * This method is used by on-behalf-of form to dynamically generate poulate the 
     * location field values for selected permissioned contact. 
     */
    function getPermissionedLocation( &$config ) 
    {
        $cid = CRM_Utils_Type::escape( $_GET['cid'], 'Integer' );
        
        require_once 'CRM/Core/BAO/Location.php';
        $entityBlock = array( 'contact_id' => $cid );
        $loc =& CRM_Core_BAO_Location::getValues( $entityBlock, $location );

        $str  = "location_1_phone_1_phone::" . $location['location'][1]['phone'][1]['phone'] . ';;';
        $str .= "location_1_email_1_email::". $location['location'][1]['email'][1]['email'] . ';;';

        $addressSequence = array_flip($config->addressSequence());

        if ( array_key_exists( 'street_address', $addressSequence) ) {
            $str .= "location_1_address_street_address::" . $location['location'][1]['address']['street_address'] . ';;';
        }
        if ( array_key_exists( 'supplemental_address_1', $addressSequence) ) {
            $str .= "location_1_address_supplemental_address_1::" . $location['location'][1]['address']['supplemental_address_1'] . ';;';
        }
        if ( array_key_exists( 'supplemental_address_2', $addressSequence) ) {
            $str .= "location_1_address_supplemental_address_2::" . $location['location'][1]['address']['supplemental_address_2'] . ';;';
        }
        if ( array_key_exists( 'city', $addressSequence) ) {
            $str .= "location_1_address_city::" . $location['location'][1]['address']['city'] . ';;';
        }
        if ( array_key_exists( 'postal_code', $addressSequence) ) {
            $str .= "location_1_address_postal_code::" . $location['location'][1]['address']['postal_code'] . ';;';
            $str .= "location_1_address_postal_code_suffix::" . $location['location'][1]['address']['postal_code_suffix'] . ';;';
        }
        if ( array_key_exists( 'country', $addressSequence) || array_key_exists( 'state_province', $addressSequence) ) {
            $str .= "id_location[1][address][country_state]_0::" . $location['location'][1]['address']['country_id'] . '-' . $location['location'][1]['address']['state_province_id'] . ';;';

        }
        echo $str;
    }

    function groupTree( $config ) 
    {
        $gids  = CRM_Utils_Type::escape( $_GET['gids'], 'String' ); 
        require_once 'CRM/Contact/BAO/GroupNestingCache.php';
        echo CRM_Contact_BAO_GroupNestingCache::json( $gids );
    }

    /**
     * Function to setDefaults according to membership type
     */
    function getMemberTypeDefaults( $config ) 
    {
        require_once 'CRM/Utils/Type.php';
        $memType  = CRM_Utils_Type::escape( $_GET['mtype'], 'Integer') ; 
        
        $contributionType = CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipType', 
                                                         $memType, 
                                                         'contribution_type_id' );
        
        $totalAmount = CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipType', 
                                                    $memType, 
                                                    'minimum_fee' );

        echo $contributionType . "^A" . $totalAmount;
    }
}
