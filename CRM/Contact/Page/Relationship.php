<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';

class CRM_Contact_Page_Relationship {

    /**
     * class constructor
     */
    function __construct( ) {
    }

    static function view( $page, $relationshipId ) {
        $relationship = new CRM_Contact_DAO_Relationship( );

        self::browse( $page, $relationshipId );
        
        $relationshipId = 0;
        self::browse( $page );
    }

    static function browse( $page, $relationshipId = 0 ) {
     
        $relationship = new CRM_Contact_DAO_Relationship( );
     
        $contactId = $page->getContactId( );
        
        $str_select1 = $str_from1 = $str_where1 = $str_select2 = $str_from2 = $str_where2 = $str_order = $str_limit = '';
        
        $str_select1 = "( SELECT crm_relationship.id as crm_relationship_id,
                              crm_contact.sort_name as sort_name,
                              crm_address.street_address as street_address,
                              crm_address.city as city,
                              crm_address.postal_code as postal_code,
                              crm_state_province.abbreviation as state,
                              crm_country.name as country,
                              crm_email.email as email,
                              crm_phone.phone as phone,
                              crm_contact.id as crm_contact_id,
                              crm_contact.contact_type as contact_type,
                              crm_relationship.contact_id_b as contact_id_b,
                              crm_relationship.contact_id_a as contact_id_a,
                              crm_relationship_type.name_b_a as relation";

        $str_from1 = " FROM crm_contact 
                        LEFT OUTER JOIN crm_location ON (crm_contact.id = crm_location.contact_id AND crm_location.is_primary = 1)
                        LEFT OUTER JOIN crm_address ON (crm_location.id = crm_address.location_id )
                        LEFT OUTER JOIN crm_phone ON (crm_location.id = crm_phone.location_id AND crm_phone.is_primary = 1)
                        LEFT OUTER JOIN crm_email ON (crm_location.id = crm_email.location_id AND crm_email.is_primary = 1)
                        LEFT OUTER JOIN crm_state_province ON (crm_address.state_province_id = crm_state_province.id)
                        LEFT OUTER JOIN crm_country ON (crm_address.country_id = crm_country.id),
                        crm_relationship,crm_relationship_type
                       ";

        // add where clause 
        $str_where1 = " WHERE crm_relationship.relationship_type_id = crm_relationship_type.id 
                         AND crm_relationship.contact_id_b = ".$contactId." 
                         AND crm_relationship.contact_id_a = crm_contact.id  ";

        if ($relationshipId > 0) {
            $str_where1 .= " AND crm_relationship.id = ".$relationshipId;
        }

        $str_where1 .= ") UNION ";

        $str_select2 = "(SELECT crm_relationship.id as crm_relationship_id,
                              crm_contact.sort_name as sort_name,
                              crm_address.street_address as street_address,
                              crm_address.city as city,
                              crm_address.postal_code as postal_code,
                              crm_state_province.abbreviation as state,
                              crm_country.name as country,
                              crm_email.email as email,
                              crm_phone.phone as phone,
                              crm_contact.id as crm_contact_id,
                              crm_contact.contact_type as contact_type,
                              crm_relationship.contact_id_b as contact_id_b,
                              crm_relationship.contact_id_a as contact_id_a,
                              crm_relationship_type.name_a_b as relation";

        $str_from2 = " FROM crm_contact 
                        LEFT OUTER JOIN crm_location ON (crm_contact.id = crm_location.contact_id AND crm_location.is_primary = 1)
                        LEFT OUTER JOIN crm_address ON (crm_location.id = crm_address.location_id )
                        LEFT OUTER JOIN crm_phone ON (crm_location.id = crm_phone.location_id AND crm_phone.is_primary = 1)
                        LEFT OUTER JOIN crm_email ON (crm_location.id = crm_email.location_id AND crm_email.is_primary = 1)
                        LEFT OUTER JOIN crm_state_province ON (crm_address.state_province_id = crm_state_province.id)
                        LEFT OUTER JOIN crm_country ON (crm_address.country_id = crm_country.id),
                      crm_relationship,crm_relationship_type ";

        // add where clause 
        $str_where2 = " WHERE crm_relationship.relationship_type_id = crm_relationship_type.id 
                         AND crm_relationship.contact_id_a = ".$contactId." 
                         AND crm_relationship.contact_id_b = crm_contact.id";

        if ($relationshipId > 0) {
            $str_where2 .= " AND crm_relationship.id = ".$relationshipId;
        }

        $str_where2 .= ")";


        //$str_order = " GROUP BY crm_relationship.id ";
        //$str_limit = "  ";

        // building the query string
        $query_string = $str_select1.$str_from1.$str_where1.$str_select2.$str_from2.$str_where2;
        $relationship->query($query_string);
        
        $ids[] = array( );
        
        while ( $relationship->fetch() ) {
            
            $values[$relationship->crm_relationship_id]['id'] = $relationship->crm_relationship_id;
            $values[$relationship->crm_relationship_id]['cid'] = $relationship->crm_contact_id;
            $values[$relationship->crm_relationship_id]['relation'] = $relationship->relation;
            $values[$relationship->crm_relationship_id]['name'] = $relationship->sort_name;
            $values[$relationship->crm_relationship_id]['email'] = $relationship->email;
            $values[$relationship->crm_relationship_id]['phone'] = $relationship->phone;
            $values[$relationship->crm_relationship_id]['city'] = $relationship->city;
            $values[$relationship->crm_relationship_id]['state'] = $relationship->state;

            if ($relationship->crm_contact_id == $relationship->contact_id_a ) {
                $values[$relationship->crm_relationship_id]['contact_a'] = $relationship->contact_id_a;
                $values[$relationship->crm_relationship_id]['contact_b'] = 0;
            } else {
                $values[$relationship->crm_relationship_id]['contact_b'] = $relationship->contact_id_b;
                $values[$relationship->crm_relationship_id]['contact_a'] = 0;
            }
            
            $relationship->storeValues( $values[$relationship->crm_relationship_id] );
        }

        if ($relationshipId > 0) {
            $page->assign( 'relationship_name', $relationship->relation );
            $page->assign( 'relationship_contact_name', $relationship->sort_name );
        } else {
            $page->assign( 'relationship', $values );
        }
    }

    static function edit( $page, $mode, $relationshipId = null ) {

        $controller = new CRM_Controller_Simple( 'CRM_Contact_Form_Relationship', 'Contact Relationships', $mode );

        // set the userContext stack
        $session = CRM_Session::singleton();
        $config  = CRM_Config::singleton();
        $session->pushUserContext( CRM_System::url('civicrm/contact/view/rel', 'action=browse' ) );
        
        /*
        if ( !$relationshipId ) {
            $relationshipId = $controller->get( 'relationshipId' );
        }

        // rtype is the variable that tells type of realationship (a_b or b_a)
        if ( !$_GET['rtype'] ) {
            $rtype = $controller->get( 'rtype' );
        } else {
            $rtype = $_GET['rtype'];
        }
        */
        $controller->reset( );
        $controller->set( 'contactId'  , $page->getContactId( ) );
        $controller->set( 'relationshipId'   , $relationshipId );
        $controller->set( 'rtype' , $rtype);
                
        $controller->process( );
        $controller->run( );
    }

    static function run( $page ) {

        $contactId = $page->getContactId( );
        $page->assign( 'contactId', $contactId );

        $action = CRM_Request::retrieve( 'action', $page, false, 'browse' );
        $page->assign( 'action', $action );

        $rid = CRM_Request::retrieve( 'rid', $page, false, 0 );

        if ( $action & CRM_Action::VIEW ) {
            self::view( $page, $rid );
        } else if ( $action & ( CRM_Action::UPDATE | CRM_Action::ADD ) ) {
            self::edit( $page, $action, $rid );
        }


        self::browse( $page );
    }
}

?>