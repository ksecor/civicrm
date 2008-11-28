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
 *
 */

/**
 * This class contains all contact related functions that are called using AJAX (jQuery)
 */
class CRM_Contact_Page_AJAX
{
    static function getContactList( &$config ) 
    {
        require_once 'CRM/Utils/Type.php';
        $name = CRM_Utils_Array::value( 's', $_GET );
        
        $query = "
SELECT sort_name, id
FROM civicrm_contact
WHERE sort_name LIKE '$name%'
AND contact_type = 'Individual'
ORDER BY sort_name ";            

        $dao = CRM_Core_DAO::executeQuery( $query );
        $contactList = null;
        while ( $dao->fetch( ) ) {
            echo $contactList = "$dao->sort_name|$dao->id\n";
        }
    }

    static function relationship( &$config ) 
    {
        // CRM_Core_Error::debug_var( 'GET' , $_GET , true, true );
        // CRM_Core_Error::debug_var( 'POST', $_POST, true, true );
        
        require_once 'CRM/Utils/Type.php';
        $relType         = CRM_Utils_Array::value( 'rel_type', $_POST );
        $relContactID    = CRM_Utils_Array::value( 'rel_contact', $_POST );
        $sourceContactID = CRM_Utils_Array::value( 'contact_id', $_POST );
        $relationshipID  = CRM_Utils_Array::value( 'rel_id', $_POST );
        $caseID          = CRM_Utils_Array::value( 'case_id', $_POST );


        $relationParams = array('relationship_type_id' => $relType .'_a_b', 
                                'contact_check'        => array( $relContactID => 1),
                                'is_active'            => 1,
                                'case_id'              => $caseID
                                );
        
        if ( $relationshipID == 'null' ) {
            $relationIds = array( 'contact'      => $sourceContactID);
        } else {
            $relationIds = array( 'contact'      => $sourceContactID, 
                                  'relationship' => $relationshipID,
                                  'contactTarget'=>  $relContactID );
        }

        require_once "CRM/Contact/BAO/Relationship.php";
        $return = CRM_Contact_BAO_Relationship::create( $relationParams, $relationIds );

		$relationshipID = $return[4][0];

		// we should return phone and email
		require_once "CRM/Case/BAO/Case.php";
        $caseRelationship = CRM_Case_BAO_Case::getCaseRoles( $sourceContactID, $caseID, $relationshipID );

		$relation           = $caseRelationship[$relationshipID];
		$relation['rel_id'] = $relationshipID;
		echo json_encode( $relation );
		exit();
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


    function groupTree( $config ) 
    {
        $gids  = CRM_Utils_Type::escape( $_GET['gids'], 'String' ); 
        require_once 'CRM/Contact/BAO/GroupNestingCache.php';
        echo CRM_Contact_BAO_GroupNestingCache::json( $gids );
    }    

    /**
     * Function for building contact combo box
     */
    function search( &$config ) 
    {
        require_once 'CRM/Utils/Type.php';
        $name      = CRM_Utils_Array::value( 'name', $_GET, '' );
        $name      = CRM_Utils_Type::escape( $name, 'String' ); 
        $whereIdClause = '';
        if ( CRM_Utils_Array::value( 'id', $_GET ) ) {
            if ( is_numeric( $_GET['id'] ) ) {
                $id  = CRM_Utils_Type::escape( $_GET['id'], 'Integer' ) ; 
                $whereIdClause = " AND civicrm_contact.id = {$id}";
            } else {
                $name = $_GET['id'];
            }
        }

        $elements = array( );
        if ( $name || isset( $id ) ) {
            $name  = str_replace( '*', '%', $name );

            //contact's based of relationhip type
            $relType = null; 
            if ( isset($_GET['rel']) ) {
                $relation = explode( '_', $_GET['rel'] );
                $relType  = CRM_Utils_Type::escape( $relation[0], 'Integer');
                $rel      = CRM_Utils_Type::escape( $relation[2], 'String');
            }

            //shared household info
            $shared = null;
            if ( isset($_GET['sh']) ) {
                $shared = CRM_Utils_Type::escape( $_GET['sh'], 'Integer');
                 if ( $shared == 1 ) {
                     $contactType = 'Household';
                     $cName = 'household_name';
                 } else {
                     $contactType = 'Organization';
                     $cName = 'organization_name';
                 }
            }

            // contacts of type household
            $hh = $addStreet = $addCity = null;
            if ( isset($_GET['hh']) ) {
                $hh = CRM_Utils_Type::escape( $_GET['hh'], 'Integer');
            }
            
            //organization info
            $organization = $street = $city = null;
            if ( isset($_GET['org']) ) {
                $organization = CRM_Utils_Type::escape( $_GET['org'], 'Integer');
            }
            
            if ( isset($_GET['org']) || isset($_GET['hh']) ) {
                if ( $splitName = explode( ' :: ', $name ) ) {
                    $contactName = trim( CRM_Utils_Array::value( '0', $splitName ) );
                    $street      = trim( CRM_Utils_Array::value( '1', $splitName ) );
                    $city        = trim( CRM_Utils_Array::value( '2', $splitName ) );
                } else {
                    $contactName = $name;
                }
                
                if ( $street ) {
                    $addStreet = "AND civicrm_address.street_address LIKE '$street%'";
                }
                if ( $city ) {
                    $addCity = "AND civicrm_address.city LIKE '$city%'";
                }
            }
            
            if ( $organization ) {
                
                $query = "
SELECT CONCAT_WS(' :: ',sort_name,LEFT(street_address,25),city) 'sort_name', 
civicrm_contact.id 'id'
FROM civicrm_contact
LEFT JOIN civicrm_address ON ( civicrm_contact.id = civicrm_address.contact_id
                                AND civicrm_address.is_primary=1
                             )
WHERE civicrm_contact.contact_type='Organization' AND organization_name LIKE '%$contactName%'
{$addStreet} {$addCity} {$whereIdClause}
ORDER BY organization_name ";

            } else if ( $shared ) {
                
                $query = "
SELECT CONCAT_WS(':::' , sort_name, supplemental_address_1, sp.abbreviation, postal_code, cc.name )'sort_name' , civicrm_contact.id 'id' , civicrm_contact.display_name 'disp' FROM civicrm_contact LEFT JOIN civicrm_address ON (civicrm_contact.id =civicrm_address.contact_id AND civicrm_address.is_primary =1 )LEFT JOIN civicrm_state_province sp ON (civicrm_address.state_province_id =sp.id )LEFT JOIN civicrm_country cc ON (civicrm_address.country_id =cc.id )WHERE civicrm_contact.contact_type ='{$contactType}' AND {$cName} LIKE '%$name%' {$whereIdClause} ORDER BY {$cName} ";

            } else if ( $hh ) {
                
                $query = "
SELECT CONCAT_WS(' :: ' , sort_name, LEFT(street_address,25),city) 'sort_name' , civicrm_contact.id 'id' FROM civicrm_contact LEFT JOIN civicrm_address ON (civicrm_contact.id =civicrm_address.contact_id AND civicrm_address.is_primary =1 )
WHERE civicrm_contact.contact_type ='Household' AND household_name LIKE '%$contactName%' {$addStreet} {$addCity} {$whereIdClause} ORDER BY household_name ";
                
            } else if ( $relType ) {
                if ( CRM_Utils_Array::value( 'case', $_GET ) ) {
                    $query = "
SELECT distinct(c.id), c.sort_name
FROM civicrm_contact c 
LEFT JOIN civicrm_relationship ON civicrm_relationship.contact_id_{$rel} = c.id
WHERE c.sort_name LIKE '%$name%'
AND civicrm_relationship.relationship_type_id = $relType
GROUP BY sort_name 
";
                } else {
                    
                    $query = "
SELECT c.sort_name, c.id
FROM civicrm_contact c, civicrm_relationship_type r
WHERE c.sort_name LIKE '%$name%'
AND r.id = $relType
AND c.contact_type = r.contact_type_{$rel} {$whereIdClause} 
ORDER BY sort_name" ;
                }
            } else {
                
                $query = "
SELECT sort_name, id
FROM civicrm_contact
WHERE sort_name LIKE '%$name'
{$whereIdClause}
ORDER BY sort_name ";            
        }

            $start = 0;
            $end   = 10;
            
            if ( isset( $_GET['start'] ) ) {
                $start = CRM_Utils_Type::escape( $_GET['start'], 'Integer' );
            }
            
            if ( isset( $_GET['count'] ) ) {
                $end   = CRM_Utils_Type::escape( $_GET['count'], 'Integer' );
            }
            
            $query .= " LIMIT {$start},{$end}";
            
            $dao = CRM_Core_DAO::executeQuery( $query );
            
            if ( $shared ) {
                while ( $dao->fetch( ) ) {
                    echo $dao->sort_name;
                    exit();
                }
            } else {  
                while ( $dao->fetch( ) ) {
                    $elements[] = array( 'name' => addslashes( $dao->sort_name ),
                                         'id'   => $dao->id );
                }
            }
        }

        if ( isset($_GET['sh']) ) {
            echo "";
            exit();
        }

        if ( empty( $elements ) ) {
            $name = str_replace( '%', '', $name );
            $elements[] = array( 'name' => $name,
                                 'id'   => $name );
        }

        require_once "CRM/Utils/JSON.php";
        echo CRM_Utils_JSON::encode( $elements );
    }

    /*                                                                                                                                                                                            
     * Function to check how many contact exits in db for given criteria, 
     * if one then return contact id else null                                                                                  
     */
    function contact( &$config )
    {
        require_once 'CRM/Utils/Type.php';
        $name      = CRM_Utils_Type::escape( $_GET['name'], 'String' );

        $query = "                                                                                                                                                                                 
SELECT id                                                                                                                                                                                          
FROM civicrm_contact                                                                                                                                                                               
WHERE sort_name LIKE '%$name%'";

        $dao = CRM_Core_DAO::executeQuery( $query );
        $dao->fetch( );

        if ( $dao->N == 1) {
            echo $dao->id;
        }
    }

}
