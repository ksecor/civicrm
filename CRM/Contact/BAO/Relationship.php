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

require_once 'CRM/Contact/DAO/Relationship.php';
require_once 'CRM/Contact/DAO/RelationshipType.php';
require_once 'CRM/Contact/BAO/Block.php';

class CRM_Contact_BAO_Relationship extends CRM_Contact_DAO_Relationship {
    /**
     * various constants to indicate different type of relationships
     *
     * @var int
     */
    const
        PAST              =  1,
        DISABLED          =  2,
        CURRENT           =  4;
   
    /**
     * takes an associative array and creates a relationship object 
     *
     *
     * @param array $params (reference ) an assoc array of name/value pairs
     * @param array $ids    the array that holds all the db ids
     *
     * @return object CRM_Contact_BAO_Relationship object 
     * @access public
     * @static
     */
    static function create( &$params, &$ids ) 
    {
        $relationshipId = CRM_Utils_Array::value( 'relationship', $ids );
        if ( ! $relationshipId ) {
            // creating a new relationship
            $dataExists = self::dataExists( $params );
            if ( ! $dataExists ) {
                return null;
            }

            $valid = $invalid = $duplicate = 0;
            foreach ( $params['contact_check'] as $key => $value) {
                $errors = '';
                // check if the realtionship is valid between contacts.
                // step 1: check if the relationship is valid if not valid skip and keep the count
                // step 2: check the if two contacts already have a relationship if yes skip and keep the count
                // step 3: if valid relationship then add the relation and keep the count
                
                $errors = CRM_Contact_BAO_Relationship::checkValidRelationship( $params, $ids, $key ); // step 1
                if ( $errors ) {
                    $invalid++;
                    continue;
                }
                
                if ( CRM_Contact_BAO_Relationship::checkDuplicateRelationship( CRM_Utils_Array::value( 'relationship_type_id',
                                                                                                       $params ),
                                                                               CRM_Utils_Array::value( 'contact', $ids ),
                                                                               $key )) { // step 2
                    $duplicate++;
                    continue;
                }
                
                $relationship = self::add( $params, $ids, $key );
                $valid++;
            }
        
            return array( $valid, $invalid, $duplicate );
        } else { //editing the relationship
            
            // check for duplicate relationship
            if ( CRM_Contact_BAO_Relationship::checkDuplicateRelationship( CRM_Utils_Array::value( 'relationship_type_id',
                                                                                                       $params ),
                                                                           CRM_Utils_Array::value( 'contact', $ids ),
                                                                               $ids['contactTarget'] )) { 
                $duplicate++;
                return array( $valid, $invalid, $duplicate );
            }

            // editing an existing relationship
            self::add( $params, $ids, $ids['contactTarget'] );
        }
    }


    /**
     * This is the function that check/add if the relationship created is valid
     *
     * @param array  $params      (reference ) an assoc array of name/value pairs
     * @param integer $contactId  this is contact id for adding relationship
     * @param array $ids          the array that holds all the db ids  
     * 
     * @return object CRM_Contact_BAO_Relationship 
     * @access public
     * @static
     */
    static function add ( &$params, &$ids, $contactId ) 
    {
        $relationshipTypes = CRM_Utils_Array::value( 'relationship_type_id', $params );

        // expolode the string with _ to get the relationship type id and to know which contact has to be inserted in
        // contact_id_a and which one in contact_id_b
        list( $type, $first, $second ) = explode( '_', $relationshipTypes );

        ${'contact_' . $first}  = CRM_Utils_Array::value( 'contact', $ids );
        ${'contact_' . $second} = $contactId;

        //check if the relationship type is Head of Household then update the household's primary contact with this contact.
        if ($type == 6) {
            CRM_Contact_BAO_Household::updatePrimaryContact($contact_b, $contact_a );
        }
        
        $relationship =& new CRM_Contact_BAO_Relationship( );
        $relationship->contact_id_b         = $contact_b;
        $relationship->contact_id_a         = $contact_a;
        $relationship->relationship_type_id = $type;
        $relationship->is_active            = 1;
        $relationship->start_date           = CRM_Utils_Date::format( CRM_Utils_Array::value( 'start_date', $params ) );
        if ( ! $relationship->start_date ) {
            $relationship->start_date = 'NULL';
        }

        $relationship->end_date             = CRM_Utils_Date::format( CRM_Utils_Array::value( 'end_date'  , $params ) );
        if ( ! $relationship->end_date ) {
            $relationship->end_date = 'NULL';
        }

        $relationship->id = CRM_Utils_Array::value( 'relationship', $ids );
        
        return  $relationship->save( );
    }


    /**
     * Check if there is data to create the object
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return boolean
     * @access public
     * @static
     */
    static function dataExists( &$params ) 
    {
        // return if no data present
        if ( ! is_array( $params['contact_check']) ) {
            return false;
        } 
        return true;
    }

    /**
     * Function to get get list of relationship type based on the contact type.
     *
     * @param int    $contactId this is the contact id of the current contact.
     * @param string $strContact it's  values are 'a or b' if value is 'a' then selected contact is the value of contac_id_a 
     *               for the relationship and if value is 'b' then selected contact is the value of contac_id_b for the relationship
     * @param string $relationshipId the id of the existing relationship if any
     * @access public
     * @static
     *
     * @return array - array reference of all relationship types with context to current contact.
     *
     */
    function getContactRelationshipType( $contactId, $contactSuffix, $relationshipId )
    {
        $allRelationshipType = array();
        $relationshipType    = array();
        $allRelationshipType = CRM_Core_PseudoConstant::relationshipType();

        $otherContactType = null;
        if ( $relationshipId ) {
            $relationship =& new CRM_Contact_DAO_Relationship( );
            $relationship->id = $relationshipId;
            if ($relationship->find(true)) {
                $contact =& new CRM_Contact_DAO_Contact( );
                $contact->id = ( $relationship->contact_id_a === $contactId ) ? $relationship->contact_id_b : $relationship->contact_id_a;
                if ($contact->find(true)) {
                    $otherContactType = $contact->contact_type;
                }
            }
        }

        $contact     =& new CRM_Contact_BAO_Contact();
        $contact->id = $contactId;
        if ( $contact->find(true) ) {
            foreach ($allRelationshipType as $key => $value) {
                // the contact type is required or matches
                if ( ( ( ! $value['contact_type_a'] ) || $value['contact_type_a'] == $contact->contact_type ) &&
                     // the other contact type is required or present or matches
                     ( ( ! $value['contact_type_b'] ) || ( ! $otherContactType ) || $value['contact_type_b'] == $otherContactType ) ) {
                    $relationshipType[ $key . '_a_b' ] = $value[ 'name_a_b' ];
                } 
                
                if ( ( ( ! $value['contact_type_b'] ) || $value['contact_type_b'] == $contact->contact_type ) &&
                     ( ( ! $value['contact_type_a'] ) || ( ! $otherContactType ) || $value['contact_type_a'] == $otherContactType ) ) {
                    $relationshipType[ $key . '_b_a' ] = $value[ 'name_b_a' ];
                }
            }

            // lets clean up the data and eliminate all duplicate values (i.e. the relationship is bi-directional)
            $relationshipType = array_unique( $relationshipType );
            return $relationshipType;
        }
        return null;
    }

    /**
     * Function to delete the relationship
     *
     * @param int $id relationship id
     *
     * @return null
     * @access public

     * @static
     */
    static function del ( $id ) 
    {
        // delete from relationship table
        $relationship =& new CRM_Contact_DAO_Relationship( );
        $relationship->id = $id;
        $relationship->delete();
        CRM_Core_Session::setStatus( ts('Selected Relationship has been Deleted Successfuly.') );
    }

    /**
     * Delete the object records that are associated with this contact
     *
     * @param  int  $contactId id of the contact to delete
     *
     * @return void
     * @access public
     * @static
     */
    static function deleteContact( $contactId ) 
    {
        $relationship =& new CRM_Contact_DAO_Relationship( );
        $relationship->contact_id_a = $contactId;
        $relationship->delete();

        $relationship =& new CRM_Contact_DAO_Relationship( );
        $relationship->contact_id_b = $contactId;
        $relationship->delete();
    }

    /**
     * Function to get the other contact in a relationship
     *
     * @param int $id relationship id
     *
     * $returns  returns the contact ids in the realtionship
     * @access public
     * @static
     */
    static function getContactIds($id) 
    {
        $relationship =& new CRM_Contact_DAO_Relationship( );

        $relationship->id = $id;
        $relationship->selectAdd( );
        $relationship->selectAdd('contact_id_a, contact_id_b');
        $relationship->find(true);
        
        return $relationship;
    }

    /**
     * Function to check if the relationship type selected between two contacts is correct
     *
     * @param int $contact_a 1st contact id 
     * @param int $contact_b 2nd contact id 
     * @param int $relationshipTypeId relationship type id
     *
     * @return boolean  true if it is valid relationship else false
     * @access public
     * @static
     */
    static function checkRelationshipType ($contact_a, $contact_b, $relationshipTypeId) 
    {
        $relationshipType     =& new CRM_Contact_DAO_RelationshipType( );
        $relationshipType->id = $relationshipTypeId;
        $relationshipType->selectAdd( );
        $relationshipType->selectAdd('contact_type_a, contact_type_b');
        $relationshipType->find(true);
        
        $contact_type_a = CRM_Contact_BAO_Contact::getContactType( $contact_a );
        $contact_type_b = CRM_Contact_BAO_Contact::getContactType( $contact_b );

        if ( ( ( ! $relationshipType->contact_type_a ) || ( $relationshipType->contact_type_a == $contact_type_a ) ) &&
             ( ( ! $relationshipType->contact_type_b ) || ( $relationshipType->contact_type_b == $contact_type_b ) ) ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * this function does the validtion for valid relationship
     *
     * @param array   $params     this array contains the values there are subitted by the form
     * @param array   $ids        the array that holds all the db ids  
     * @param integer $contactId  this is contact id for adding relationship
     * 
     * @return
     * @access public
     * @static
     */
    static function checkValidRelationship( &$params, &$ids, $contactId ) 
    {
        $errors = '';

        // get the string of relationship type
        $relationshipTypes = CRM_Utils_Array::value( 'relationship_type_id', $params );
        list( $type, $first, $second ) = explode('_', $relationshipTypes);

        ${'contact_' . $first}  = CRM_Utils_Array::value( 'contact', $ids );
        ${'contact_' . $second} = $contactId;
    
        // function to check if the relationship selected is correct
        // i.e. employer relationship can exit between Individual and Organization (not between Individual and Individual)
        if ( ! CRM_Contact_BAO_Relationship::checkRelationshipType( $contact_a, $contact_b, $type ) ) {
            $errors = 'Please select valid relationship between these two contacts.';
        }
        return $errors;
    }
  
    /**
     * this function checks for duplicate relationship
     *
     * @param string $relationshipTypeId relationship id concatinated with (a_b or b_a)
     * @param integer $id this the id of the contact whom we are adding relationship
     * @param integer $contactId  this is contact id for adding relationship
     * 
     * @return boolean true if record exists else false
     * @access public
     * @static
     */
    static function checkDuplicateRelationship( $relationshipTypeId, $id, $contactId = 0) 
    {
        list( $type, $first, $second ) = explode( '_' , $relationshipTypeId );

        $queryString = " SELECT id 
                         FROM   civicrm_relationship 
                         WHERE  relationship_type_id = $type
                                AND ( ( contact_id_a = $id        AND contact_id_b = $contactId ) OR 
                                      ( contact_id_a = $contactId AND contact_id_b = $id        )
                                    ) ";


        $relationship =& new CRM_Contact_BAO_Relationship();
        $relationship->query($queryString);
        $relationship->fetch();
        $relationship->id;

        return ( $relationship->id ) ? true : false;
    }

    /**
     * update the is_active flag in the db
     *
     * @param int      $id        id of the database record
     * @param boolean  $is_active value we want to set the is_active field
     *
     * @return Object             DAO object on success, null otherwise
     * @static
     */
    static function setIsActive( $id, $is_active ) 
    {
        return CRM_Core_DAO::setFieldValue( 'CRM_Contact_DAO_Relationship', $id, 'is_active', $is_active );
    }

    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params        input parameters to find object
     * @param array $values        output values of the object
     * @param array $ids           the array that holds all the db ids
     *
     * @return array (reference)   the values that could be potentially assigned to smarty
     * @access public
     * @static
     */
    static function &getValues( &$params, &$values, &$ids ) {
        $values['relationship']['data'] =& 
            CRM_Contact_BAO_Relationship::getRelationship($params['contact_id'], null , 3 );
        
        // get the total count of relationships
        $values['relationship']['totalCount'] =
            CRM_Contact_BAO_Relationship::getRelationship($params['contact_id'], null , null, true );

        return $values;
    }

    /**
     * helper function to form the sql for relationship retrieval
     *
     * @param int $contactId contact id
     * @param int $status (check const at top of file)
     * @param int $numRelationship no of relationships to display (limit)
     * @param int $count get the no of relationships
     * $param int $relationshipId relationship id
     * @param string $direction   the direction we are interested in a_b or b_a
     *
     * return string the query for this diretion
     * @static
     * @access public
     */
    static function makeURLClause( $contactId, $status, $numRelationship, $count, $relationshipId, $direction ) {
        $select = $from = $where = '';

        $select = '( ';
        if ( $count ) {
            if ( $direction == 'a_b' ) {
                $select .= ' SELECT count(DISTINCT civicrm_relationship.id) as cnt1, 0 as cnt2 ';
            } else {
                $select .= ' SELECT 0 as cnt1, count(DISTINCT civicrm_relationship.id) as cnt2 ';
            }
        } else {
            $select .= ' SELECT civicrm_relationship.id as civicrm_relationship_id,
                              civicrm_contact.sort_name as sort_name,
                              civicrm_address.street_address as street_address,
                              civicrm_address.city as city,
                              civicrm_address.postal_code as postal_code,
                              civicrm_state_province.abbreviation as state,
                              civicrm_country.name as country,
                              civicrm_email.email as email,
                              civicrm_phone.phone as phone,
                              civicrm_contact.id as civicrm_contact_id,
                              civicrm_contact.contact_type as contact_type,
                              civicrm_relationship.contact_id_b as contact_id_b,
                              civicrm_relationship.contact_id_a as contact_id_a,
                              civicrm_relationship_type.id as civicrm_relationship_type_id,
                              civicrm_relationship.start_date as start_date,
                              civicrm_relationship.end_date as end_date,
                              civicrm_relationship.is_active as is_active ';

            if ( $direction == 'a_b' ) {
                $select .= ', civicrm_relationship_type.name_a_b as name_a_b,
                              civicrm_relationship_type.name_b_a as relation ';
            } else {
                $select .= ', civicrm_relationship_type.name_a_b as name_a_b,
                              civicrm_relationship_type.name_a_b as relation ';
            }

        }

        $from = ' FROM civicrm_contact, civicrm_relationship, civicrm_relationship_type
                        LEFT OUTER JOIN civicrm_location ON (civicrm_contact.id = civicrm_location.contact_id AND civicrm_location.is_primary = 1)
                        LEFT OUTER JOIN civicrm_address ON (civicrm_location.id = civicrm_address.location_id )
                        LEFT OUTER JOIN civicrm_phone ON (civicrm_location.id = civicrm_phone.location_id AND civicrm_phone.is_primary = 1)
                        LEFT OUTER JOIN civicrm_email ON (civicrm_location.id = civicrm_email.location_id AND civicrm_email.is_primary = 1)
                        LEFT OUTER JOIN civicrm_state_province ON (civicrm_address.state_province_id = civicrm_state_province.id)
                        LEFT OUTER JOIN civicrm_country ON (civicrm_address.country_id = civicrm_country.id) ';

        $where = ' WHERE civicrm_relationship.relationship_type_id = civicrm_relationship_type.id ';
        if ( $direction == 'a_b' ) {
            $where .= ' AND civicrm_relationship.contact_id_b = ' . $contactId . ' AND civicrm_relationship.contact_id_a = civicrm_contact.id ';
        } else {
            $where .= ' AND civicrm_relationship.contact_id_a = ' . $contactId . ' AND civicrm_relationship.contact_id_b = civicrm_contact.id ';
        }
        if ( $relationshipId ) {
            $where .= ' AND civicrm_relationship.id = ' . $relationshipId;
        }

        $date = date( 'Y-m-d' );
        if ( $status == self::PAST ) {
            //this case for showing past relationship
            $where .= ' AND civicrm_relationship.is_active = 1 ';
            $where .= " AND civicrm_relationship.end_date < '" . $date . "'";
        } else if ( $status == self::DISABLED ) {
            // this case for showing disabled relationship
            $where .= ' AND civicrm_relationship.is_active = 0 ';
        } else if ( $status == self::CURRENT ) {
            //this case for showing current relationship
            $where .= ' AND civicrm_relationship.is_active = 1 ';
            $where .= " AND (civicrm_relationship.end_date >= '" . $date . "' OR civicrm_relationship.end_date IS NULL) ";
        }
        
        if ( $direction == 'a_b' ) {
            $where .= ' ) UNION ';
        } else {
            $where .= ' ) ';
        }

        return array( $select, $from, $where );
    }

   /**
     * This is the function to get the list of relationships
     * 
     * @param int $contactId contact id
     * @param int $status 1: Past 2: Disabled 3: Current
     * @param int $numRelationship no of relationships to display (limit)
     * @param int $count get the no of relationships
     * $param int $relationshipId relationship id
     *
     * return array $values relationship records
     * @static
     * @access public
     */
    static function getRelationship( $contactId, $status = 0, $numRelationship = 0, $count = 0, $relationshipId = 0, $links = null ) {
        list( $select1, $from1, $where1 ) = self::makeURLClause( $contactId, $status, $numRelationship, $count, $relationshipId, 'a_b' );
        list( $select2, $from2, $where2 ) = self::makeURLClause( $contactId, $status, $numRelationship, $count, $relationshipId, 'b_a' );

        $order = $limit = '';
        if (! $count ) {
            $order = ' ORDER BY civicrm_relationship_type_id, sort_name ';

            if ( $numRelationship) {
                $limit = " LIMIT 0, $numRelationship";
            }
        }

        // building the query string
        $queryString = '';
        $queryString = $select1 . $from1 . $where1 . $select2 . $from2 . $where2 . $order . $limit;

        $relationship =& new CRM_Contact_DAO_Relationship( );
        $relationship->query($queryString);
        $row = array();
        if ( $count ) {
            $relationshipCount = 0;
            while ( $relationship->fetch() ) {
                $relationshipCount += $relationship->cnt1 + $relationship->cnt2; 
            }
            return $relationshipCount;
        } else {
            $values = array( );

            if ( $links ) {
                $mask = CRM_Core_Action::VIEW | CRM_Core_Action::UPDATE | CRM_Core_Action::DELETE;
                if ( $status == self::CURRENT ) {
                    $mask |= CRM_Core_Action::DISABLE;
                } else if ( $status == self::DISABLED ) {
                    $mask |= CRM_Core_Action::ENABLE;
                }
            }

            while ( $relationship->fetch() ) {
                $rid = $relationship->civicrm_relationship_id;

                $values[$rid]['id']         = $rid;
                $values[$rid]['cid']        = $relationship->civicrm_contact_id;
                $values[$rid]['relation']   = $relationship->relation;
                $values[$rid]['name']       = $relationship->sort_name;
                $values[$rid]['email']      = $relationship->email;
                $values[$rid]['phone']      = $relationship->phone;
                $values[$rid]['city']       = $relationship->city;
                $values[$rid]['state']      = $relationship->state;
                $values[$rid]['start_date'] = $relationship->start_date;
                $values[$rid]['end_date']   = $relationship->end_date;
                $values[$rid]['is_active']  = $relationship->is_active;

                if ($relationship->name_a_b == $relationship->relation) {
                    $values[$rid]['rtype'] = 'a_b';
                } else {
                    $values[$rid]['rtype'] = 'b_a';
                }

                if ( $links ) {
                    $replace = array( 'id' => $rid, 'rtype' => $values[$rid]['rtype'], 'cid' => $contactId );
                    $values[$rid]['action'] = CRM_Core_Action::formLink( $links, $mask, $replace );
                }
            }

            return $values;
        }
    }
 
}

?>