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
     * class constructor
     */
    function __construct( ) 
    {
        parent::__construct( );
    }
   
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
        $invalidRelationshipCount = 0;
        $validRelationshipCount = 0;
        $duplicateRelationshipCount = 0;
        
        $relationshipId = 0;
        $relationshipId = CRM_Utils_Array::value( 'relationship', $ids );
        if (!$relationshipId) {
            $dataExists = self::dataExists( $params );
            if ( ! $dataExists ) {
                return null;
            }
        }
        
        CRM_Core_DAO::transaction( 'BEGIN' );
        
        if (is_array($params['contact_check'])) {
            foreach ( $params['contact_check'] as $key => $value) {
                $errorsMessage = '';
                // check if the realtionship is valid between contacts.
                // step 1: check if the relationship is valid if not valid skip and keep the count
                // step 2: check the if two contacts already have a relationship if yes skip and keep the count
                // step 3: if valid relationship then add the relation and keep the count
                
                $errorsMessage = CRM_Contact_BAO_Relationship::checkValidRelationship( $params, $ids, $key ); // step 1
                if (strlen(trim($errorsMessage))) {
                    $invalidRelationshipCount++;
                } else {
                    
                    if (CRM_Contact_BAO_Relationship::checkDuplicateRelationship( CRM_Utils_Array::value( 'relationship_type_id', $params ), CRM_Utils_Array::value( 'contact', $ids ), $key )) { // step 2
                        $duplicateRelationshipCount++;
                    } else {
                        $relationship = self::add( $params, $ids, $key );
                        $validRelationshipCount++;
                    }
                }
            }
            
            if ( $validRelationshipCount ) {
                $userStatus = $validRelationshipCount.' new relationship record(s) created.';
            }
            if ( $invalidRelationshipCount ) {
                $userStatus .= $invalidRelationshipCount.' relationship record(s) not created due to invalid target contact type.';
            }
            if ( $duplicateRelationshipCount ) {
                $userStatus .= $duplicateRelationshipCount.' relationship record(s) not created - duplicate of existing relationship.';
            }
            CRM_Core_Session::setStatus( $userStatus );
            
        } else {
            
            $relationship = self::add( $params, $ids);
            
            CRM_Core_Session::setStatus( 'Your relationship record has been updated.' );
        }
        
        CRM_Core_DAO::transaction( 'COMMIT' );
       
        //return $relationship;
    }


    /**
     * This is the function that check/add if the relationship created is valid
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     * @param integer $contactId  this is contact id for adding relationship
     * @param array $ids    the array that holds all the db ids  
     * 
     * @return object CRM_Contact_BAO_Relationship 
     * @access public
     * @static
     */
    static function add ( &$params, &$ids, $contactId = 0 ) 
    {
        // create relationship object
        $relationship = new CRM_Contact_BAO_Relationship( );
        
        // get the string of relationship type
        $relationshipTypes = CRM_Utils_Array::value( 'relationship_type_id', $params );

        // expolode the string with _ to get the relationship type id and to know which contact has to be inserted in
        // contact_id_a and which one in contact_id_b
        
        $temp = explode('_',$relationshipTypes);

        // $temp[0] will contain the relationship type id.
        // if $temp[1] == b or $temp[2] == a then the current contact has to be inserted as contact_id_b
        // if $temp[1] == a or $temp[2] == b then the currnet contact has to be inserted as contact_id_a
        
        $relationship->relationship_type_id = $temp[0];        

        if ($temp[1] == 'b') {
            $contact_b = CRM_Utils_Array::value( 'contact', $ids );
            if (!$contactId) {
                // to get the other contact in the relationship
                $relObj = CRM_Contact_BAO_Relationship::getContactId(CRM_Utils_Array::value( 'relationship', $ids ) );
                
                if ($relObj->contact_id_a == $contact_b) {
                    $contact_a = $relObj->contact_id_b;
                } else {
                    $contact_a = $relObj->contact_id_a;
                }

            } else {
                $contact_a = $contactId;
                $relationship->is_active = 1;
            }

            //check if the relationship type is Head of Household then update the household's primary contact with this contact.
            if ($temp[0] == 6) {
                CRM_Contact_BAO_Household::updatePrimaryContact($contact_b, $contact_a );
            }

        } else if ($temp[1] == 'a') {

            $contact_a = CRM_Utils_Array::value( 'contact', $ids );

            if (!$contactId) {
                // to get the other contact in the relationship
                $relObj = CRM_Contact_BAO_Relationship::getContactId(CRM_Utils_Array::value( 'relationship', $ids ) );
                
                if ($relObj->contact_id_a == $contact_a) {
                    $contact_b = $relObj->contact_id_b;
                } else {
                    $contact_b = $relObj->contact_id_a;
                }

            } else {
                $contact_b = $contactId;
                $relationship->is_active = 1;
            }
            
            //check if the relationship type is Head of Household then update the household's primary contact with this contact.
            if ($temp[0] == 6) {
                CRM_Contact_BAO_Household::updatePrimaryContact($contact_a, $contact_b );
            }

        }
        
        $relationship->contact_id_b  = $contact_b;
        $relationship->contact_id_a  = $contact_a;

        //  $relationship->is_active = 1;

        $sdate = CRM_Utils_Array::value( 'start_date', $params );
        $relationship->start_date = null;
        if ( $sdate              &&
             !empty($sdate['M']) &&
             !empty($sdate['d']) &&
             !empty($sdate['Y']) ) {
            $sdate['M'] = ( $sdate['M'] < 10 ) ? '0' . $sdate['M'] : $sdate['M'];
            $sdate['d'] = ( $sdate['d'] < 10 ) ? '0' . $sdate['d'] : $sdate['d'];
            $relationship->start_date = $sdate['Y'] . $sdate['M'] . $sdate['d'];
        }
        
        $edate = CRM_Utils_Array::value( 'end_date', $params );
        $relationship->end_date = null;
        if ( $edate              &&
             !empty($edate['M']) &&
             !empty($edate['d']) &&
             !empty($edate['Y']) ) {
            $edate['M'] = ( $edate['M'] < 10 ) ? '0' . $edate['M'] : $edate['M'];
            $edate['d'] = ( $edate['d'] < 10 ) ? '0' . $edate['d'] : $edate['d'];
            $relationship->end_date = $edate['Y'] . $edate['M'] . $edate['d'];
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
            $relationship = new CRM_Contact_DAO_Relationship( );
            $relationship->id = $relationshipId;
            if ($relationship->find(true)) {
                $contact = new CRM_Contact_DAO_Contact( );
                $contact->id = ( $relationship->contact_id_a === $contactId ) ? $relationship->contact_id_b : $relationship->contact_id_a;
                if ($contact->find(true)) {
                    $otherContactType = $contact->contact_type;
                }
            }
        }

        $contact = new CRM_Contact_BAO_Contact();
        $contact->id = $contactId;
        if ( $contact->find(true) ) {
            $contactSuffix = trim( $contactSuffix );
            foreach ($allRelationshipType as $key => $value) {
                if ( $value['name_a_b']       != $value['name_b_a']       &&
                     $value['contact_type_a'] == $value['contact_type_b'] &&
                     $value['contact_type_a'] == $contact->contact_type   &&
                     ( ( ! $otherContactType ) || $value['contact_type_b'] == $otherContactType ) ) {
                    $relationshipType[ $key . '_a_b' ] = $value['name_a_b'];
                    $relationshipType[ $key . '_b_a' ] = $value['name_b_a'];
                    continue;
                }
                if ( $value['contact_type_a'] == $contact->contact_type &&
                     ( ( ! $otherContactType ) || $value['contact_type_b'] == $otherContactType ) ) {
                    if ( ! in_array( $value['name_a_b'], $relationshipType ) ) {
                        if ( $contactSuffix ) {
                            $relationshipType[ $key . '_' . $contactSuffix ] = $value[ 'name_a_b' ];
                        } else {
                            $relationshipType[ $key . '_a_b' ] = $value[ 'name_a_b' ];
                        }
                    }
                } 
                
                if ( $value['contact_type_b'] == $contact->contact_type &&
                     ( ( ! $otherContactType ) || $value['contact_type_a'] == $otherContactType ) ) {
                    if ( ! in_array( $value['name_b_a'], $relationshipType ) ) {
                        if ( $contactSuffix ) {
                            $relationshipType[ $key . '_' . $contactSuffix ] = $value[ 'name_b_a' ];
                        } else {
                            $relationshipType[ $key . '_b_a' ] = $value[ 'name_b_a' ];
                        }
                    }
                }
            }

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
        $relationship = new CRM_Contact_DAO_Relationship( );
        $relationship->id = $id;
        $relationship->delete();
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
        $relationship = new CRM_Contact_DAO_Relationship( );
        $relationship->contact_id_a = $contactId;
        $relationship->delete();

        $relationship = new CRM_Contact_DAO_Relationship( );
        $relationship->contact_id_b = $contactId;
        $relationship->delete();
    }

    /**
     * Function to get the other contact in a relationship
     *
     * @param int $id relationship id
     *
     * $returns  returns the other contact id in the realtionship
     * @access public
     * @static
     */
    static function getContactId ($id) 
    {
        $relationship = new CRM_Contact_DAO_Relationship( );

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
        $relationshipType = new CRM_Contact_DAO_RelationshipType( );
        $relationshipType->selectAdd( );
        $relationshipType->selectAdd('contact_type_a, contact_type_b');
        $relationshipType->id = $relationshipTypeId;
        $relationshipType->find(true);
        
        $relationshipTypeArray1 = array($relationshipType->contact_type_a, $relationshipType->contact_type_b );
        
        $relationshipTypeArray2[0] = CRM_Contact_BAO_Contact::getContactType($contact_a);
        $relationshipTypeArray2[1] = CRM_Contact_BAO_Contact::getContactType($contact_b);
        
        $resultArray1 = array_diff($relationshipTypeArray1 ,$relationshipTypeArray2);
        $resultArray2 = array_diff($relationshipTypeArray2 ,$relationshipTypeArray1);
        
        if ( count($resultArray1) || count($resultArray2) ) {
            return false;
        }
        return true;
    }    

    /**
     * this function does the validtion for valid relationship
     *
     * @param array   $params     this array contains the values there are subitted by the form
     * @param integer $contactId  this is contact id for adding relationship
     * @param array   $ids        the array that holds all the db ids  
     * 
     * @return
     * @access public
     * @static
     */
    static function checkValidRelationship( &$params, &$ids, $contactId = 0) 
    {
        $errors = '';

        // get the string of relationship type
        $relationshipTypes = CRM_Utils_Array::value( 'relationship_type_id', $params );

        // expolode the string with _ to get the relationship type id and to know which contact has to be inserted in
        // contact_id_a and which one in contact_id_b
        
        $temp = explode('_', $relationshipTypes);
        
        // $temp[0] will contain the relationship type id.
        // if $temp[1] == b or $temp[2] == a then the current contact has to be inserted as contact_id_b
        // if $temp[1] == a or $temp[2] == b then the currnet contact has to be inserted as contact_id_a
        
        if ($temp[1] == 'b') {
            $contact_b = CRM_Utils_Array::value( 'contact', $ids );
            if (!$contactId) {
                // to get the other contact in the relationship
                $relObj = CRM_Contact_BAO_Relationship::getContactId(CRM_Utils_Array::value( 'relationship', $ids ) );
                
                if ($relObj->contact_id_a == $contact_b) {
                    $contact_a = $relObj->contact_id_b;
                } else {
                    $contact_a = $relObj->contact_id_a;
                }

            } else {
                $contact_a = $contactId;
            }
        } else if ($temp[1] == 'a') {

            $contact_a = CRM_Utils_Array::value( 'contact', $ids );

            if (!$contactId) {
                // to get the other contact in the relationship
                $relObj = CRM_Contact_BAO_Relationship::getContactId(CRM_Utils_Array::value( 'relationship', $ids ) );
                
                if ($relObj->contact_id_a == $contact_a) {
                    $contact_b = $relObj->contact_id_b;
                } else {
                    $contact_b = $relObj->contact_id_a;
                }

            } else {
                $contact_b = $contactId;
            }
        }
    
        // function to check if the relationship selected is correct
        // i.e. employer relationship can exit between Individual and Organization (not between Individual and Individual)
        
        if (!CRM_Contact_BAO_Relationship::checkRelationshipType( $contact_a, $contact_b, $temp[0])) {
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
        $errors = '';

        // expolode the string with _ to get the relationship type id 
        // $temp[0] - relationshipType id
        $temp = explode('_',$relationshipTypeId);
                
        
        $relationship = new CRM_Contact_BAO_Relationship();
        
        $queryString = "SELECT id 
                        FROM crm_relationship 
                        WHERE relationship_type_id = ".$temp[0]."
                        AND ( (contact_id_a = ".$id." AND contact_id_b = ".$contactId.") OR 
                              (contact_id_a = ".$contactId." AND contact_id_b = ".$id.")
                             )";

        $relationship->query($queryString);
        
        $relationship->fetch();

        if ($relationship->id) {
            return true;
        }

        return false;
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
        if ($id) {
            $relationship = new CRM_Contact_DAO_Relationship( );
            $relationship->id = $id;
            $relationship->is_active = $is_active;
            return $relationship->save( );
        }
        
        return null;
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

        //$currentRelationships = CRM_Contact_BAO_Relationship::getRelationship($params['contact_id'], 'In' , 3 );
        $values['relationship']['data']       =& CRM_Contact_BAO_Relationship::getRelationship($params['contact_id'], null , 3 );
        
        // get the total count of relationships
        $values['relationship']['totalCount'] = CRM_Contact_BAO_Relationship::getRelationship($params['contact_id'], null , null, true );

        return $values;
    }


   /**
     * This is the function to get the list of relationships
     * 
     * @param int $contactId contact id
     * @param int $status 0: Current 1: Past 2: Disabled
     * @param int $numRelationship no of relationships to display (limit)
     * @param int $count get the no of relationships
     * $param int $relationshipId relationship id
     *
     * return array $values relationship records
     * @static
     * @access public
     */
    static function getRelationship( $contactId, $status = 0, $numRelationship = 0, $count = 0, $relationshipId = 0 ) {

        $relationship = new CRM_Contact_DAO_Relationship( );
        $select1 = $from1 = $where1 = $select2 = $from2 = $where2 = $order = $limit = '';
        $select1 = "( ";

        if ( $count ) {
            $select1 .= "SELECT count(DISTINCT crm_relationship.id) as cnt1, 0 as cnt2 ";
        } else { 
            $select1 .= "SELECT crm_relationship.id as crm_relationship_id,
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
                              crm_relationship_type.name_a_b as name_a_b,
                              crm_relationship_type.name_b_a as relation,
                              crm_relationship_type.id as crm_relationship_type_id";
            
            if ($relationshipId > 0) {
                $select1 .= " ,crm_relationship.start_date as start_date, crm_relationship.end_date as end_date, crm_relationship.is_active  as is_active";
            }
        }

        $from1 = " FROM crm_contact 
                        LEFT OUTER JOIN crm_location ON (crm_contact.id = crm_location.contact_id AND crm_location.is_primary = 1)
                        LEFT OUTER JOIN crm_address ON (crm_location.id = crm_address.location_id )
                        LEFT OUTER JOIN crm_phone ON (crm_location.id = crm_phone.location_id AND crm_phone.is_primary = 1)
                        LEFT OUTER JOIN crm_email ON (crm_location.id = crm_email.location_id AND crm_email.is_primary = 1)
                        LEFT OUTER JOIN crm_state_province ON (crm_address.state_province_id = crm_state_province.id)
                        LEFT OUTER JOIN crm_country ON (crm_address.country_id = crm_country.id),
                        crm_relationship,crm_relationship_type
                       ";

        // add where clause 
        $where1 = " WHERE crm_relationship.relationship_type_id = crm_relationship_type.id 
                         AND crm_relationship.contact_id_b = ".$contactId." 
                         AND crm_relationship.contact_id_a = crm_contact.id  ";

        if ($relationshipId > 0) {
            $where1 .= " AND crm_relationship.id = ".$relationshipId;
        }
        
        switch ($status) {
        case 2:
            //this case for showing disabled relationship
            $where1 .= "     AND crm_relationship.is_active = 0 ";
            break;
            
        case 1:
            //this case for showing past relationship
            $where1 .= "     AND crm_relationship.is_active = 1 ";
            $where1 .= "     AND crm_relationship.end_date < '".date("Y-m-d")."'";
            break;
            
        case 3:
            //this case for showing current relationship
            $where1 .= "     AND crm_relationship.is_active = 1 ";
            $where1 .= "     AND (crm_relationship.end_date >= '".date("Y-m-d")."' OR crm_relationship.end_date IS NULL)";
            break;

        }

        $where1 .= ") UNION ";

        $select2 = "( ";
       
        if ( $count ) {
            $select2 .= "SELECT 0 as cnt1, count(DISTINCT crm_relationship.id) as cnt2";
        } else { 
            $select2 .= "SELECT crm_relationship.id as crm_relationship_id,
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
                              crm_relationship_type.name_a_b as name_a_b,
                              crm_relationship_type.name_a_b as relation,
                              crm_relationship_type.id as crm_relationship_type_id";

            if ($relationshipId > 0) {
                $select2 .= " ,crm_relationship.start_date as start_date, crm_relationship.end_date as end_date, crm_relationship.is_active  as is_active";
            }
        }
        $from2 = " FROM crm_contact 
                        LEFT OUTER JOIN crm_location ON (crm_contact.id = crm_location.contact_id AND crm_location.is_primary = 1)
                        LEFT OUTER JOIN crm_address ON (crm_location.id = crm_address.location_id )
                        LEFT OUTER JOIN crm_phone ON (crm_location.id = crm_phone.location_id AND crm_phone.is_primary = 1)
                        LEFT OUTER JOIN crm_email ON (crm_location.id = crm_email.location_id AND crm_email.is_primary = 1)
                        LEFT OUTER JOIN crm_state_province ON (crm_address.state_province_id = crm_state_province.id)
                        LEFT OUTER JOIN crm_country ON (crm_address.country_id = crm_country.id),
                      crm_relationship,crm_relationship_type ";

        // add where clause 
        $where2 = " WHERE crm_relationship.relationship_type_id = crm_relationship_type.id 
                         AND crm_relationship.contact_id_a = ".$contactId." 
                         AND crm_relationship.contact_id_b = crm_contact.id";

        if ($relationshipId > 0) {
            $where2 .= " AND crm_relationship.id = ".$relationshipId;
        }
        
        switch ($status) {
        case 2:
            //this case for showing disabled relationship
            $where2 .= "     AND crm_relationship.is_active = 0 ";
            break;
            
        case 1:
            //this case for showing past relationship
            $where2 .= "     AND crm_relationship.is_active = 1 ";
            $where2 .= "     AND crm_relationship.end_date < '".date("Y-m-d")."'";
            break;
            
        case 3:
            //this case for showing current relationship
            $where2 .= "     AND crm_relationship.is_active = 1 ";
            $where2 .= "     AND (crm_relationship.end_date >= '".date("Y-m-d")."' OR crm_relationship.end_date IS NULL)";
        }

        $where2 .= ")";


        if (! $count ) {
            $order = ' ORDER BY crm_relationship_id ';

            if ( $numRelationship) {
                $limit = " LIMIT 0, $numRelationship";
            }
        }

        // building the query string
        $queryString = '';
        $queryString = $select1.$from1.$where1.$select2.$from2.$where2.$order.$limit;

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
            
            while ( $relationship->fetch() ) {

                $values[$relationship->crm_relationship_id]['id']         = $relationship->crm_relationship_id;
                $values[$relationship->crm_relationship_id]['cid']        = $relationship->crm_contact_id;
                $values[$relationship->crm_relationship_id]['relation']   = $relationship->relation;
                $values[$relationship->crm_relationship_id]['name']       = $relationship->sort_name;
                $values[$relationship->crm_relationship_id]['email']      = $relationship->email;
                $values[$relationship->crm_relationship_id]['phone']      = $relationship->phone;
                $values[$relationship->crm_relationship_id]['city']       = $relationship->city;
                $values[$relationship->crm_relationship_id]['state']      = $relationship->state;
                $values[$relationship->crm_relationship_id]['start_date'] = $relationship->start_date;
                $values[$relationship->crm_relationship_id]['end_date']   = $relationship->end_date;
                $values[$relationship->crm_relationship_id]['is_active']  = $relationship->is_active;

                /*
                if ($relationship->crm_contact_id == $relationship->contact_id_a ) {
                    $values[$relationship->crm_relationship_id]['contact_a'] = $relationship->contact_id_a;
                    $values[$relationship->crm_relationship_id]['contact_b'] = 0;
                } else {
                    $values[$relationship->crm_relationship_id]['contact_b'] = $relationship->contact_id_b;
                    $values[$relationship->crm_relationship_id]['contact_a'] = 0;
                }
  
                */
                
                if ($relationship->name_a_b == $relationship->relation) {
                    $values[$relationship->crm_relationship_id]['rtype'] = 'a_b';
                } else {
                    $values[$relationship->crm_relationship_id]['rtype'] = 'b_a';
                }
            }
            return $values;
        }
    }
 
}

?>