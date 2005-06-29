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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/DAO/Note.php';
require_once 'CRM/Core/Form.php';

require_once 'CRM/Contact/DAO/Contact.php';
require_once 'CRM/Contact/DAO/Location.php';
require_once 'CRM/Contact/DAO/Address.php';
require_once 'CRM/Contact/DAO/Phone.php';
require_once 'CRM/Contact/DAO/Email.php';



/**
 * rare case where because of inheritance etc, we actually store a reference
 * to the dao object rather than inherit from it
 */

class CRM_Contact_BAO_Contact extends CRM_Contact_DAO_Contact 
{
    /**
     * the types of communication preferences
     *
     * @var array
     */
    static $_commPrefs = array( 'do_not_phone', 'do_not_email', 'do_not_mail', 'do_not_trade' );

    /**
     * static field for all the contact information that we can potentially import
     *
     * @var array
     * @static
     */
    static $_importableFields = null;

    function __construct()
    {
        parent::__construct();
    }

    static function permissionedContact( $id, $type = CRM_Core_Permission::VIEW ) {
        $query = ' SELECT count(DISTINCT crm_contact.id) ' . self::fromClause( ) .
                  ' WHERE crm_contact.id = ' . $id . ' AND ' . CRM_Core_Permission::whereClause( $type ) . ' ';

        $dao =& new CRM_Core_DAO( );
        $dao->query($query);
        
        // does not work for php4
        // $row = $dao->getDatabaseResult()->fetchRow();
        $result = $dao->getDatabaseResult();
        $row    = $result->fetchRow();
        return ( $row[0] > 0 ) ? true : false;
    }

    /**
     * given an id return the relevant contact details
     *
     * @param int $id contact id
     *
     * @return the contact object
     * @static
     * @access public
     */
    static function contactDetails( $id ) {
        if ( ! $id ) {
            return null;
        }

        $query = "
SELECT DISTINCT
  crm_contact.id as contact_id,
  crm_individual.id               as individual_id ,
  crm_location.id                 as location_id   ,
  crm_address.id                  as address_id    ,
  crm_email.id                    as email_id      ,
  crm_phone.id                    as phone_id      ,
  crm_individual.first_name       as first_name    ,
  crm_individual.last_name        as last_name     ,
  crm_address.street_address      as street_address,
  crm_address.city                as city          ,
  crm_address.postal_code         as postal_code   ,
  crm_state_province.name         as state         ,
  crm_country.name                as country       ,
  crm_email.email                 as email         ,
  crm_phone.phone                 as phone         ";
        
        $query .= self::individualFromClause( );
        $query .= " WHERE crm_contact.id = $id";

        $dao =& new CRM_Core_DAO( );
        $dao->query($query);
        if ( $dao->fetch( ) ) {
            return $dao;
        }
        return null;
    }

    /**
     * Find contacts which match the criteria
     *
     * @param string $matchClause the matching clause
     * @param int    $id          the current contact id (hence excluded from matching)
     *
     * @return string                contact ids if match found, else null
     * @static
     * @access public
     */
    static function matchContact( $matchClause, $id = null ) {
        $query  = "SELECT GROUP_CONCAT(DISTINCT crm_contact.id)";
        $query .= self::individualFromClause( );
        $query .= " WHERE $matchClause ";
        if ( $id ) {
            $query .= " AND crm_contact.id != $id ";
        }

        $dao =& new CRM_Core_DAO( );
        $dao->query($query);
        $result = $dao->getDatabaseResult();
        if ( $result ) {
            $row = $result->fetchRow();
            if ( $row ) {
                return $row[0];
            }
        }
        return null;
    }

    /**
     * Get all the emails for a specified contact_id, with the primary email being first
     *
     * @param int $id the contact id
     *
     * @return array  the array of email id's
     * @access public
     * @static
     */
    static function allEmails( $id ) {
        if ( ! $id ) {
            return null;
        }

        $query = "
SELECT email, crm_location_type.name as locationType, crm_email.is_primary as is_primary
FROM    crm_contact
LEFT JOIN crm_location ON (crm_contact.id = crm_location.contact_id)
LEFT JOIN crm_location_type ON (crm_location.location_type_id = crm_location_type.id)
LEFT JOIN crm_email ON (crm_location.id = crm_email.location_id)
WHERE
  crm_contact.id = $id
ORDER BY
  crm_location.is_primary DESC, crm_email.is_primary DESC";
        
        $dao =& new CRM_Core_DAO( );
        $dao->query($query);
        $emails = array( );
        while ( $dao->fetch( ) ) {
            $emails[$dao->email] = array( 'locationType' => $dao->locationType,
                                          'primary'      => $dao->primary );
        }
        return $emails;
    }

    /**
     * create and query the db for an contact search
     *
     * @param array    $formValues array of reference of the form values submitted
     * @param int      $action   the type of action links
     * @param int      $offset   the offset for the query
     * @param int      $rowCount the number of rows to return
     * @param boolean  $count    is this a count only query ?
     * @param boolean  $includeContactIds should we include contact ids?
     * @param boolean  $sortByChar if true returns the distinct array of first characters for search results
     * @param boolean  $groupContacts if true, use a single mysql group_contact statement to get the contact ids
     *
     * @return CRM_Contact_DAO_Contact 
     * @access public
     */
    function searchQuery(&$fv, $offset, $rowCount, $sort, 
                         $count = false, $includeContactIds = false, $sortByChar = false,
                         $groupContacts = false )
    {
        $select = $from = $where = $order = $limit = '';

        if( $count ) {
            $select = "SELECT count(DISTINCT crm_contact.id) ";
        } else if ( $sortByChar ) {
            $select = "SELECT DISTINCT UPPER(LEFT(crm_contact.sort_name, 1)) as sort_name";
        } else if ( $groupContacts ) {
            $select = "SELECT GROUP_CONCAT(DISTINCT crm_contact.id)";
        } else {
            $select = self::selectClause( );
        }

        $from = self::fromClause( );

        $where = self::whereClause( $fv, $includeContactIds );

        if ( empty( $where ) ) {
            $where = ' WHERE ' . CRM_Core_Permission::whereClause( CRM_Core_Permission::VIEW ) . ' ';
        } else {
            $where = ' WHERE ' . $where . ' AND ' . CRM_Core_Permission::whereClause( CRM_Core_Permission::VIEW ) . ' ';
        }

        if (!$count) {
            if ($sort) {
                $order = " ORDER BY " . $sort->orderBy(); 
            } else if ($sortByChar) { 
                $order = " ORDER BY LEFT(crm_contact.sort_name, 1) ";
            }
            if ( $rowCount > 0 ) {
                $limit = " LIMIT $offset, $rowCount ";
            }
        }

        // building the query string
        $queryString = $select . $from . $where . $order . $limit;

        // CRM_Core_Error::debug( 'qs', $queryString );
        $this->query($queryString);

        if ($count || $groupContacts) {
            $result = $this->getDatabaseResult();
            $row    = $result->fetchRow();
            return $row[0];
        }

        return $this;
    }

    /**
     * create the default select clause
     *
     * @return string the select clause
     * @access public
     * @static
     */
    static function selectClause( ) {
        return "
SELECT DISTINCT crm_contact.id as contact_id,
  crm_contact.sort_name as sort_name,
  crm_contact.display_name as display_name,
  crm_address.street_address as street_address,
  crm_address.city as city,
  crm_address.postal_code as postal_code,
  crm_address.geo_code_1 as latitude,
  crm_address.geo_code_2 as longitude,
  crm_state_province.abbreviation as state,
  crm_country.name as country,
  crm_email.email as email,
  crm_phone.phone as phone,
  crm_contact.contact_type as contact_type
";
    }

    /**
     * create the from clause
     *
     * @return string the from clause
     * @access public
     * @static
     */
    static function fromClause( ) {
        return " FROM crm_contact
                        LEFT JOIN crm_location ON (crm_contact.id = crm_location.contact_id AND crm_location.is_primary = 1)
                        LEFT JOIN crm_address ON crm_location.id = crm_address.location_id
                        LEFT JOIN crm_phone ON (crm_location.id = crm_phone.location_id AND crm_phone.is_primary = 1)
                        LEFT JOIN crm_email ON (crm_location.id = crm_email.location_id AND crm_email.is_primary = 1)
                        LEFT JOIN crm_state_province ON crm_address.state_province_id = crm_state_province.id
                        LEFT JOIN crm_country ON crm_address.country_id = crm_country.id
                        LEFT JOIN crm_group_contact ON crm_contact.id = crm_group_contact.contact_id
                        LEFT JOIN crm_entity_tag ON crm_contact.id = crm_entity_tag.entity_id 
                        LEFT JOIN crm_activity_history ON crm_contact.id = crm_activity_history.entity_id ";
    }

    /**
     * create the from clause for crm_individual data
     *
     * @return string the from clause
     * @access public
     * @static
     */
    static function individualFromClause( ) {
        return " FROM crm_contact
LEFT JOIN crm_individual ON (crm_contact.id = crm_individual.contact_id)
LEFT JOIN crm_location ON (crm_contact.id = crm_location.contact_id AND crm_location.is_primary = 1)
LEFT JOIN crm_address ON crm_location.id = crm_address.location_id
LEFT JOIN crm_phone ON (crm_location.id = crm_phone.location_id AND crm_phone.is_primary = 1)
LEFT JOIN crm_email ON (crm_location.id = crm_email.location_id AND crm_email.is_primary = 1)
LEFT JOIN crm_state_province ON crm_address.state_province_id = crm_state_province.id
LEFT JOIN crm_country ON crm_address.country_id = crm_country.id ";
    }

    /**
     * create the where clause for a contact search
     *
     * @param array    $formValues array of reference of the form values submitted
     * @param boolean  $includeContactIds should we include contact ids?
     *
     * @return string  the where clause without the permissions hook (important)
     * @access public
     * @static
     */
    static function whereClause( &$fv, $includeContactIds = false)
    {
        $where = '';

        /*
         * sample formValues for query 
         *
         * Get me all contacts of type individual or organization who are members of group 1 "Newsletter Subscribers"
         * and are categorized as "Non Profit" (catid 1) or "Volunteer" (catid 5) 

        $fv = Array
            (
             [cb_contact_type] => Array
             (
              [Individual] => 1
              [Organization] => 1
              )
             
             [cb_group] => Array
             (
              [1] => 1
              )
             
             [cb_tag] => Array
             (
              [1] => 1
              [5] => 1
              )
             
             [last_name] => 
             [first_name] => 
             [street_name] => 
             [city] => 
             [state_province] => 
             [country] => 
             [postal_code] => 
             [postal_code_low] => 
             [postal_code_high] => 
             )

        */


        // stores all the "AND" clauses
        $andArray = array();

        // check for contact type restriction
        if ( CRM_Utils_Array::value( 'cb_contact_type', $fv ) ) {
            $andArray['contact_type'] = "(contact_type IN (";
            foreach ($fv['cb_contact_type']  as $k => $v) {
                $andArray['contact_type'] .= "'$k',"; 
            }            
            // replace the last comma with the parentheses.
            $andArray['contact_type'] = rtrim($andArray['contact_type'], ",");
            $andArray['contact_type'] .= "))";
        }
        
        // check for group restriction
        if ( CRM_Utils_Array::value( 'cb_group', $fv ) ) {
            $andArray['group'] = "(crm_group_contact.group_id IN (" . implode( ',', array_keys($fv['cb_group']) ) . '))';
            $andArray['groupStatus'] = '(crm_group_contact.status = "In")';
        }
        
        // check for tag restriction
        if ( CRM_Utils_Array::value( 'cb_tag', $fv ) ) {
            $andArray['tag'] .= "(tag_id IN (" . implode( ',', array_keys($fv['cb_tag']) ) . '))';
        }
        
        // check for last name, as of now only working with sort name
        if ( CRM_Utils_Array::value( 'sort_name', $fv ) ) {
            $name = trim($fv['sort_name']);
            // if we have a comma in the string, search for the entire string
            if ( strpos( $name, ',' ) !== false ) {
                $cond = " LOWER(crm_contact.sort_name) LIKE '%" . strtolower(addslashes($name)) . "%'";
            } else {
                // split the string into pieces
                $pieces =  explode( ' ', $name );
                $first = true;
                $cond  = ' ( ';
                foreach ( $pieces as $piece ) {
                    if ( ! $first ) {
                        $cond .= ' OR';
                    } else {
                        $first = false;
                    }
                    $cond .= " LOWER(crm_contact.sort_name) LIKE '%" . strtolower(addslashes(trim($piece))) . "%'";
                }
                $cond .= ' ) ';
            }
            $andArray['sort_name'] = "( $cond )";
        }

        // sortByCharacter
        if ( CRM_Utils_Array::value( 'sortByCharacter', $fv ) ) {
            $name = trim($fv['sortByCharacter']);

            $cond = " LOWER(crm_contact.sort_name) LIKE '" . strtolower(addslashes($name)) . "%'";
            if ( CRM_Utils_Array::value( 'sort_name', $andArray ) ) {
                $andArray['sort_name'] = '(' . $andArray['sort_name'] . "AND ( $cond ))";
            } else {
                $andArray['sort_name'] = "( $cond )";
            }
        }

        if ( $includeContactIds ) {
            $contactIds = array( );
            foreach ( $fv as $name => $value ) {
                if ( substr( $name, 0, CRM_Core_Form::CB_PREFIX_LEN ) == CRM_Core_Form::CB_PREFIX ) {
                    $contactIds[] = substr( $name, CRM_Core_Form::CB_PREFIX_LEN );
                }
            }
            if ( ! empty( $contactIds ) ) {
                $andArray['cid'] = " ( crm_contact.id in (" . implode( ',', $contactIds ) . " ) ) ";
            }
        }
        
        $fields = array( 'street_name'=> 1, 'city' => 1, 'state_province' => 2, 'country' => 2 );
        foreach ( $fields as $field => $value ) {
            if ( CRM_Utils_Array::value( $field, $fv ) ) {
                if ( $value == 1 ) {
                    $andArray[$field] = " ( LOWER(crm_address." . $field .  ") LIKE '%" . strtolower( addslashes( $fv[$field] ) ) . "%' )";
                } else { 
                    $andArray[$field] = ' ( crm_address.' . $field .  '_id = ' . $fv[$field] . ') ';
                }
            }
        }

        // postal code processing
        if ( CRM_Utils_Array::value( 'postal_code'     , $fv ) ||
             CRM_Utils_Array::value( 'postal_code_low' , $fv ) ||
             CRM_Utils_Array::value( 'postal_code_high', $fv ) ) {

            // we need to do postal code processing
            $pcORArray   = array();
            $pcANDArray  = array();

            if ($fv['postal_code']) {
                $pcORArray[] = ' ( crm_address.postal_code = ' . $fv['postal_code'] . ' ) ';
            }
            if ($fv['postal_code_low']) {
                $pcANDArray[] = ' ( crm_address.postal_code >= ' . $fv['postal_code_low'] . ' ) ';
            }
            if ($fv['postal_code_high']) {
                $pcANDArray[] = ' ( crm_address.postal_code <= ' . $fv['postal_code_high'] . ' ) ';
            }            

            if ( ! empty( $pcANDArray ) ) {
                $pcORArray[] = ' ( ' . implode( ' AND ', $pcANDArray ) . ' ) ';
            }

            $andArray['postal_code'] = ' ( ' . implode( ' OR ', $pcORArray ) . ' ) ';
        }

        if ( CRM_Utils_Array::value( 'cb_location_type', $fv ) ) {
            // processing for location type - check if any locations checked
            $andArray['location_type'] = "(crm_location.location_type_id IN (" . implode( ',', array_keys($fv['cb_location_type']) ) . '))';
        }
        
        // processing for primary location
        if ( CRM_Utils_Array::value( 'cb_primary_location', $fv ) ) {
            $andArray['cb_primary_location'] = ' ( crm_location.is_primary = 1 ) ';
        }


        // processing activity type, from and to date
        // check for activity type
        if ( CRM_Utils_Array::value( 'activity_type', $fv ) ) {
            $name = trim($fv['activity_type']);
            // split the string into pieces
            $pieces =  explode( ' ', $name );
            $first = true;
            $cond  = ' ( ';
            foreach ( $pieces as $piece ) {
                if ( ! $first ) {
                    $cond .= ' OR';
                } else {
                    $first = false;
                }
                $cond .= " LOWER(crm_activity_history.activity_type) LIKE '%" . strtolower(addslashes(trim($piece))) . "%'";
            }
            $cond .= ' ) ';
            $andArray['activity_type'] = "( $cond )";
        }

        // from date

        if ( isset($fv['activity_from_date']) &&
             ( $activityFromDate = CRM_Utils_Date::format(array_reverse(CRM_Utils_Array::value('activity_from_date', $fv))))) {
            $andArray['activity_from_date'] = " ( crm_activity_history.activity_date >= '$activityFromDate' ) ";
        }
        if (isset($fv['activity_to_date']) &&
            ($activityToDate = (CRM_Utils_Date::format(array_reverse(CRM_Utils_Array::value('activity_to_date', $fv)))))) {            
            $andArray['activity_to_date'] = " ( crm_activity_history.activity_date <= '$activityToDate' ) ";
        }
        
        // final AND ing of the entire query.
        if ( !empty( $andArray ) ) {
            $where = ' ( ' . implode( ' AND ', $andArray ) . ' ) ';
        }

        return $where;
    }

    /**
     * takes an associative array and creates a contact object
     *
     * the function extract all the params it needs to initialize the create a
     * contact object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params (reference ) an assoc array of name/value pairs
     * @param array $ids    the array that holds all the db ids
     *
     * @return object CRM_Contact_BAO_Contact object
     * @access public
     * @static
     */
    static function add(&$params, &$ids)
    {
        $contact =& new CRM_Contact_BAO_Contact();
        
        $contact->copyValues($params);

        if ($contact->contact_type == 'Individual') {
            $sortName = "";
            $firstName  = CRM_Utils_Array::value('first_name', $params, '');
            $middleName = CRM_Utils_Array::value('middle_name', $params, '');
            $lastName   = CRM_Utils_Array::value('last_name' , $params, '');
            $prefix     = CRM_Utils_Array::value('prefix'    , $params, '');
            $suffix     = CRM_Utils_Array::value('suffix'    , $params, '');

            // a comma should only be present if both first_name and last name are present.            
            if ($firstName && $lastName) {
                $sortName = "$lastName, $firstName";
            } else {
                $sortName = $lastName . $firstName;
            }
            $contact->sort_name    = trim($sortName);
            $contact->display_name =
                trim( $prefix . ' ' . $firstName . ' ' . $middleName . ' ' . $lastName . ' ' . $suffix );
        } else if ($contact->contact_type == 'Household') {
            $contact->display_name = $contact->sort_name = CRM_Utils_Array::value('household_name', $params, '');
        } else {
            $contact->display_name = $contact->sort_name = CRM_Utils_Array::value('organization_name', $params, '') ;
        }

        // preferred communication block
        $privacy = CRM_Utils_Array::value('privacy', $params);
        if ($privacy && is_array($privacy)) {
            foreach (self::$_commPrefs as $name) {
                $contact->$name = CRM_Utils_Array::value($name, $privacy, false);
            }
        }
        $contact->domain_id = CRM_Utils_Array::value( 'domain' , $ids, CRM_Core_Config::domainID( ) );
        $contact->id        = CRM_Utils_Array::value( 'contact', $ids );

        return $contact->save();
    }

    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params input parameters to find object
     * @param array $values output values of the object
     * @param array $ids    the array that holds all the db ids
     *
     * @return CRM_Contact_BAO_Contact|null the found object or null
     * @access public
     * @static
     */
    static function getValues( &$params, &$values, &$ids ) {

        $contact =& new CRM_Contact_BAO_Contact( );

        $contact->copyValues( $params );

        if ( $contact->find(true) ) {
            $ids['contact'] = $contact->id;
            $ids['domain' ] = $contact->domain_id;

            CRM_Core_DAO::storeValues( $contact, $values );

            $privacy = array( );
            foreach ( self::$_commPrefs as $name ) {
                if ( isset( $contact->$name ) ) {
                    $privacy[$name] = $contact->$name;
                }
            }
            if ( !empty($privacy) ) {
                $values['privacy'] = $privacy;
            }
                        
            return $contact;
        }
        return null;
    }

    /**
     * takes an associative array and creates a contact object and all the associated
     * derived objects (i.e. individual, location, email, phone etc)
     *
     * This function is invoked from within the web form layer and also from the api layer
     *
     * @param array $params (reference ) an assoc array of name/value pairs
     * @param array $ids    the array that holds all the db ids
     * @param int   $maxLocationBlocks the maximum number of location blocks to process
     *
     * @return object CRM_Contact_BAO_Contact object 
     * @access public
     * @static
     */
    static function create(&$params, &$ids, $maxLocationBlocks)
    {
        CRM_Core_DAO::transaction('BEGIN');
        
        $contact = self::add($params, $ids);
        
        $params['contact_id'] = $contact->id;

        // invoke the add operator on the contact_type class
        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_BAO_" . $params['contact_type']) . ".php");
        eval('$contact->contact_type_object =& CRM_Contact_BAO_' . $params['contact_type'] . '::add($params, $ids);');

        $location = array();
        for ($locationId = 1; $locationId <= $maxLocationBlocks; $locationId++) { // start of for loop for location
            $location[$locationId] = CRM_Contact_BAO_Location::add($params, $ids, $locationId);
        }
        $contact->location = $location;

        // add notes
        $contact->note = CRM_Core_BAO_Note::add($params, $ids);

        // update the UF email if that has changed
        CRM_Core_BAO_UFMatch::updateUFEmail( $contact->id );

        CRM_Core_DAO::transaction('COMMIT');

        return $contact;
    }

    /**
     *
     * Get the values for pseudoconstants for name->value and reverse.
     *
     * @param array   $defaults (reference) the default values, some of which need to be resolved.
     * @param boolean $reverse  true if we want to resolve the values in the recerse direction (value -> name)
     *
     * @return none
     * @access public
     * @static
     */
    static function resolveDefaults( &$defaults, $reverse = false ) {
        // hack for birth_date
        
        if ( CRM_Utils_Array::value( 'birth_date', $defaults ) ) {
            $defaults['birth_date'] = CRM_Utils_Date::format( $defaults['birth_date'], '-' );
        }

        if ( array_key_exists( 'location', $defaults ) ) {
            $locations =& $defaults['location'];

            // does not work for php4
            //foreach ( $locations as $index => &$location ) {
            foreach ($locations as $index => $location) {                
                $location =& $locations[$index];
                self::lookupValue( $location, 'location_type', CRM_Core_PseudoConstant::locationType(), $reverse );

                if (array_key_exists( 'address', $location ) ) {
                    if ( ! self::lookupValue( $location['address'], 'state_province',
                                              CRM_Core_PseudoConstant::stateProvince(), $reverse ) &&
                         $reverse ) {
                        self::lookupValue( $location['address'], 'state_province', 
                                           CRM_Core_PseudoConstant::stateProvinceAbbreviation(), $reverse );
                    }
                    
                    if ( ! self::lookupValue( $location['address'], 'country',
                                              CRM_Core_PseudoConstant::country(), $reverse ) &&
                         $reverse ) {
                        self::lookupValue( $location['address'], 'country', 
                                           CRM_Core_PseudoConstant::countryIsoCode(), $reverse );
                    }
                    self::lookupValue( $location['address'], 'county'        , CRM_Core_SelectValues::county()         , $reverse );
                }

                if (array_key_exists('im', $location)) {
                    $ims =& $location['im'];
                    foreach ($ims as $innerIndex => $im) {
                        $im =& $ims[$innerIndex];
                        self::lookupValue( $im, 'provider', CRM_Core_PseudoConstant::IMProvider(), $reverse );
                        unset($im);
                    }
                }
                unset($location);
            }
        }
    }

    /**
     * This function is used to convert associative array names to values
     * and vice-versa.
     *
     * This function is used by both the web form layer and the api. Note that
     * the api needs the name => value conversion, also the view layer typically
     * requires value => name conversion
     */
    static function lookupValue( &$defaults, $property, &$lookup, $reverse ) {
        $id = $property . '_id';

        $src = $reverse ? $property : $id;
        $dst = $reverse ? $id       : $property;

        if ( ! array_key_exists( $src, $defaults ) ) {
            return false;
        }

//         CRM_Core_Error::debug('lookup',$lookup);

        $look = $reverse ? array_flip( $lookup ) : $lookup;
        
        if(is_array($look)) {
            if ( ! array_key_exists( $defaults[$src], $look ) ) {
                return false;
            }
        }
        $defaults[$dst] = $look[$defaults[$src]];
        return true;
    }

    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * contact_id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the name / value pairs
     *                        in a hierarchical manner
     * @param array $ids      (reference) the array that holds all the db ids
     *
     * @return object CRM_Contact_BAO_Contact object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults, &$ids ) {
        $contact = CRM_Contact_BAO_Contact::getValues( $params, $defaults, $ids );

        unset($params['id']);
        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_BAO_" . $contact->contact_type) . ".php");
        eval( '$contact->contact_type_object =& CRM_Contact_BAO_' . $contact->contact_type . '::getValues( $params, $defaults, $ids );' );
    
        $contact->location     =& CRM_Contact_BAO_Location::getValues( $params, $defaults, $ids, 3 );
        $contact->notes        =& CRM_Core_BAO_Note::getValues( $params, $defaults, $ids );
        $contact->relationship =& CRM_Contact_BAO_Relationship::getValues( $params, $defaults, $ids );
        $contact->groupContact =& CRM_Contact_BAO_GroupContact::getValues( $params, $defaults, $ids );

        $activityParam         =  array('entity_id' => $params['contact_id']);
        $contact->activity     =& CRM_Core_BAO_History::getValues($activityParam, $defaults, 'Activity');

        $activityParam            =  array('contact_id' => $params['contact_id']);
        $defaults['openActivity'] = array(
                                          'data'       => self::getOpenActivities( $activityParam, 0, 3 ),
                                          'totalCount' => self::getNumOpenActivity( $params['contact_id'] ),
                                          );
        return $contact;
    }

    /**
     * function to get the display name of a contact
     *
     * @param  int    $id id of the contact
     *
     * @return null|string     display name of the contact if found
     * @static
     * @access public
     */
    static function displayName( $id ) {
        return CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $id, 'display_name' );
    }

    /**
     * function to get the email and display name of a contact
     *
     * @param  int    $id id of the contact
     *
     * @return null|string     display name of the contact if found
     * @static
     * @access public
     */
    static function getEmailDetails( $id ) {
        $sql = ' SELECT    crm_contact.display_name, crm_email.email
                 FROM      crm_contact
                 LEFT JOIN crm_location ON (crm_contact.id = crm_location.contact_id AND crm_location.is_primary = 1)
                 LEFT JOIN crm_email ON (crm_location.id = crm_email.location_id AND crm_email.is_primary = 1)
                 WHERE     crm_contact.id = ' . $id;
        $dao =& new CRM_Core_DAO( );
        $dao->query( $sql );
        $result = $dao->getDatabaseResult();
        if ( $result ) {
            $row    = $result->fetchRow();
            if ( $row ) {
                return array( $row[0], $row[1] );
            }
        }
        return array( null, null );
    }

    /**
     * function to get the information to map a contact
     *
     * @param  array    $ids   the list of ids for which we want map info
     *
     * @return null|string     display name of the contact if found
     * @static
     * @access public
     */
    static function &getMapInfo( $ids ) {
        $idString = ' ( ' . implode( ',', $ids ) . ' ) ';
        $sql = "
SELECT
  crm_contact.id as contact_id,
  crm_contact.display_name as display_name,
  crm_address.street_address as street_address,
  crm_address.city as city,
  crm_address.postal_code as postal_code,
  crm_address.geo_code_1 as latitude,
  crm_address.geo_code_2 as longitude,
  crm_state_province.abbreviation as state,
  crm_country.name as country
FROM      crm_contact
LEFT JOIN crm_location ON (crm_contact.id = crm_location.contact_id AND crm_location.is_primary = 1)
LEFT JOIN crm_address ON crm_location.id = crm_address.location_id
LEFT JOIN crm_state_province ON crm_address.state_province_id = crm_state_province.id
LEFT JOIN crm_country ON crm_address.country_id = crm_country.id
WHERE     crm_contact.id IN $idString AND crm_country.id = 1228 AND crm_address.geo_code_1 is not null";

        $dao =& new CRM_Core_DAO( );
        $dao->query( $sql );

        $locations = array( );
        while ( $dao->fetch( ) ) {
            $location = array( );
            $location['displayName'] = $dao->display_name;
            $location['lat'        ] = $dao->latitude;
            $location['lng'        ] = $dao->longitude;
            $address = '';
            CRM_Utils_String::append( $address, ', ',
                                      array( $dao->street_address, $dao->city, $dao->state, $dao->postal_code, $dao->country ) );
            $location['address'    ] = $address;
            $location['url'        ] = CRM_Utils_System::url( 'civicrm/contact/view', 'reset=1&cid=' . $dao->contact_id );
            $locations[] = $location;
        }
        return $locations;
    }

    /**
     * Delete a contact and all its associated records
     * 
     * @param  int  $id id of the contact to delete
     *
     * @return void
     * @access public
     * @static
     */
    function deleteContact( $id ) {
        CRM_Core_DAO::transaction( 'BEGIN' );

        // do a top down deletion
        CRM_Contact_BAO_GroupContact::deleteContact( $id );

        CRM_Contact_BAO_Relationship::deleteContact( $id );

        // cannot use this one since we need to also delete note creator contact_id
        //CRM_Core_DAO::deleteEntityContact( 'CRM_Core_DAO_Note', $id );
        CRM_Core_BAO_Note::deleteContact($id);

        CRM_Core_DAO::deleteEntityContact( 'CRM_Core_DAO_CustomValue', $id );

        CRM_Core_DAO::deleteEntityContact( 'CRM_Core_DAO_ActivityHistory', $id );

        CRM_Core_BAO_UFMatch::deleteContact( $id );
        
        // need to remove them from email, meeting and phonecall
        CRM_Core_BAO_EmailHistory::deleteContact($id);
        CRM_Core_BAO_Meeting::deleteContact($id);
        CRM_Core_BAO_Phonecall::deleteContact($id);

        // location shld be deleted after phonecall, since fields in phonecall are
        // fkeyed into location/phone.
        CRM_Contact_BAO_Location::deleteContact( $id );

        // fix household and org primary contact ids
        static $misc = array( 'Household', 'Organization' );
        foreach ( $misc as $name ) {
            require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_DAO_" . $name) . ".php");
            eval( '$object =& new CRM_Contact_DAO_' . $name . '( );' );
            $object->primary_contact_id = $id;
            $object->find( );
            while ( $object->fetch( ) ) {
                // we need to set this to null explicitly
                $object->primary_contact_id = 'null';
                $object->save( );
            }
        }

        // get the contact type
        $contact =& new CRM_Contact_DAO_Contact();
        $contact->id = $id;
        if ($contact->find(true)) {
            require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_BAO_" . $contact->contact_type) . ".php");
            eval( '$object =& new CRM_Contact_BAO_' . $contact->contact_type . '( );' );
            $object->contact_id = $contact->id;
            $object->delete( );
            $contact->delete( );
        }

        //delete the contact id from recently view
        CRM_Utils_Recent::del($id);

        CRM_Core_DAO::transaction( 'COMMIT' );
    }


    /**
     * Get contact type for a contact.
     *
     * @param int $id - id of the contact whose contact type is needed
     *
     * @return string contact_type if $id found else null ""
     *
     * @access public
     *
     * @static
     *
     */
    public static function getContactType($id)
    {
        return CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $id, 'contact_type' );
    }


    /**
     * combine all the importable fields from the lower levels object
     *
     * The ordering is important, since currently we do not have a weight
     * scheme. Adding weight is super important and should be done in the
     * next week or so, before this can be called complete.
     *
     * @return array array of importable Fields
     * @access public
     */
    function &importableFields( ) {
        if ( ! self::$_importableFields ) {
            self::$_importableFields = array();
            
            self::$_importableFields = array_merge(self::$_importableFields,
                                                   array('' => array( 'title' => ts('-do not import-'))) );
            
            self::$_importableFields = array_merge(self::$_importableFields,
                                                   CRM_Contact_DAO_Individual::import( ) );

            self::$_importableFields = array_merge(self::$_importableFields,
                                                   CRM_Contact_DAO_Address::import( ) );
            self::$_importableFields = array_merge(self::$_importableFields,
                                                   CRM_Contact_DAO_Phone::import( ) );
            self::$_importableFields = array_merge(self::$_importableFields,
                                                   CRM_Contact_DAO_Email::import( ) );
            self::$_importableFields = array_merge(self::$_importableFields,
                                                   CRM_Contact_DAO_IM::import( true ) );
            self::$_importableFields = array_merge(self::$_importableFields,
                                                   CRM_Contact_DAO_Contact::import( ) );
            /* TODO: add in the custom data fields here */
            self::$_importableFields = array_merge(self::$_importableFields,
                                                   CRM_Core_BAO_CustomField::getFields() );
//             $customImports =& CRM_Core_Dao_CustomField::import();
            
        }
        return self::$_importableFields;
    }

    /**
     * Get total number of open activities
     *
     * @param  int $id id of the contact
     * @return int $numRow - total number of open activities    
     *
     * @static
     * @access public
     */
    static function getNumOpenActivity($id)
    {

        // this is not sufficient way to do.

        $query1 = "SELECT count(*) FROM crm_meeting WHERE (target_contact_id = $id OR source_contact_id = $id) AND status != 'Completed'";
        $dao =& new CRM_Core_DAO();
        $dao->query($query1);
        $result = $dao->getDatabaseResult();
        $row    = $result->fetchRow();
        
        $rowMeeting = $row[0];
        
        $query2 = "SELECT count(*) FROM crm_phonecall WHERE (target_contact_id = $id OR source_contact_id = $id) AND status != 'Completed'";
        $dao->query($query2);
        $result = $dao->getDatabaseResult();
        $row    = $result->fetchRow();
        $rowPhonecall = $row[0];

        return  $rowMeeting + $rowPhonecall;
    }

    /**
     * function to get the list of open Actvities
     *
     * @param array reference $params  array of parameters 
     * @param int     $offset          which row to start from ?
     * @param int     $rowCount        how many rows to fetch
     * @param object|array  $sort      object or array describing sort order for sql query.
     * @param type    $type            type of history we're interested in
     *
     * @return array (reference)      $values the relevant data object values of open activitie
     *
     * @access public
     * @static
     */
    static function &getOpenActivities(&$params, $offset=null, $rowCount=null, $sort=null, $type='Activity')
    {
        
        $dao =& new CRM_Core_DAO();
        $contactId = $params['contact_id'];
        
        $query = "
( SELECT
    crm_phonecall.id as id,
    crm_phonecall.subject as subject,
    crm_phonecall.scheduled_date_time as date,
    crm_phonecall.status as status,
    source.display_name as sourceName,
    target.display_name as targetName,
    1 as activity_type
  FROM crm_phonecall, crm_contact source, crm_contact target
  WHERE
    crm_phonecall.source_contact_id = source.id AND
    crm_phonecall.target_contact_id = target.id AND
    ( crm_phonecall.source_contact_id = $contactId OR crm_phonecall.target_contact_id = $contactId ) AND
    crm_phonecall.status != 'Completed'
) UNION
( SELECT   
    crm_meeting.id as id,
    crm_meeting.subject as subject,
    crm_meeting.scheduled_date_time as date,
    crm_meeting.status as status,
    source.display_name as sourceName,
    target.display_name as targetName,
    0 as activity_type
  FROM crm_meeting, crm_contact source, crm_contact target
  WHERE
    crm_meeting.source_contact_id = source.id AND
    crm_meeting.target_contact_id = target.id AND
    ( crm_meeting.source_contact_id = $contactId OR crm_meeting.target_contact_id = $contactId ) AND
    crm_meeting.status != 'Completed'
)";
        if ($sort) {
            $order = " ORDER BY " . $sort->orderBy(); 
        } else {
            $order = " ORDER BY date desc ";
        }
        
        if ( $rowCount > 0 ) {
            $limit = " LIMIT $offset, $rowCount ";
        }
        

        $queryString = $query . $order . $limit;

        $dao->query( $queryString );
        $values =array();
        $rowCnt = 0;
        while($dao->fetch()) {
            if ($dao->activity_type == 1) {
                $values[$rowCnt]['activity_type'] = 'Phone Call';        
            } else {
                $values[$rowCnt]['activity_type'] = 'Meeting';        
            }
            $values[$rowCnt]['id']      = $dao->id;
            $values[$rowCnt]['subject'] = $dao->subject;
            $values[$rowCnt]['date']    = $dao->date;
            $values[$rowCnt]['status']  = $dao->status;
            $values[$rowCnt]['sourceName'] = $dao->sourceName;
            $values[$rowCnt]['targetName'] = $dao->targetName;
            $rowCnt++;
        }
        return $values;

    }


}

?>
