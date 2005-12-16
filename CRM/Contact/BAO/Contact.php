<?php
  /*
   +--------------------------------------------------------------------+
   | CiviCRM version 1.3                                                |
   +--------------------------------------------------------------------+
   | Copyright (c) 2005 Donald A. Lobo                                  |
   +--------------------------------------------------------------------+
   | This file is a part of CiviCRM.                                    |
   |                                                                    |
   | CiviCRM is free software; you can copy, modify, and distribute it  |
   | under the terms of the Affero General Public License Version 1,    |
   | March 2002.                                                        |
   |                                                                    |
   | CiviCRM is distributed in the hope that it will be useful, but     |
   | WITHOUT ANY WARRANTY; without even the implied warranty of         |
   | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
   | See the Affero General Public License for more details.            |
   |                                                                    |
   | You should have received a copy of the Affero General Public       |
   | License along with this program; if not, contact the Social Source |
   | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
   | questions about the Affero General Public License or the licensing |
   | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
   | at http://www.openngo.org/faqs/licensing.html                      |
   +--------------------------------------------------------------------+
  */

  /**
   *
   *
   * @package CRM
   * @author Donald A. Lobo <lobo@yahoo.com>
   * @copyright Donald A. Lobo (c) 2005
   * $Id$
   *
   */

require_once 'CRM/Core/DAO/Note.php';
require_once 'CRM/Core/Form.php';

require_once 'CRM/Contact/DAO/Contact.php';

require_once 'CRM/Core/DAO/Location.php';
require_once 'CRM/Core/DAO/Address.php';
require_once 'CRM/Core/DAO/Phone.php';
require_once 'CRM/Core/DAO/Email.php';
require_once 'CRM/Core/DAO/IM.php';
require_once 'CRM/Core/DAO/IndividualPrefix.php';
require_once 'CRM/Core/DAO/IndividualSuffix.php';
require_once 'CRM/Core/DAO/Gender.php';

require_once 'CRM/Core/BAO/CustomField.php';
require_once 'CRM/Core/BAO/CustomValue.php';
require_once 'CRM/Core/BAO/Location.php';
require_once 'CRM/Core/BAO/Note.php';

require_once 'CRM/Contact/BAO/Query.php';
require_once 'CRM/Contact/BAO/Relationship.php';
require_once 'CRM/Contact/BAO/GroupContact.php';
require_once 'CRM/Core/DAO/Meeting.php';
require_once 'CRM/Core/Permission.php';
require_once 'CRM/Mailing/Event/BAO/Subscribe.php';


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

    /**
     * static field for all the contact information that we can potentially export
     *
     * @var array
     * @static
     */
    static $_exportableFields = null;
    
    function __construct()
    {
        parent::__construct();
    }
    
    /**
     * check if the logged in user has permissions for the operation type
     *
     * @param int    $id   contact id
     * @param string $type the type of operation (view|edit)
     *
     * @return boolean true if the user has permission, false otherwise
     * @access public
     * @static
     */
    static function permissionedContact( $id, $type = CRM_Core_Permission::VIEW ) {
        $tables     = array( );
        $permission = CRM_Core_Permission::whereClause( $type, $tables );
        $from       = CRM_Contact_BAO_Query::fromClause( $tables );
        $query = "
SELECT count(DISTINCT civicrm_contact.id) 
       $from
WHERE civicrm_contact.id = " . CRM_Utils_Type::escape($id, 'Integer') . 
            " AND $permission";

        return ( CRM_Core_DAO::singleValueQuery( $query ) > 0 ) ? true : false;
    }
    
    /**
     * given an id return the relevant contact details
     *
     * @param int $id           contact id
     *
     * @return the contact object
     * @static
     * @access public
     */
    static function contactDetails( $id, &$options, $returnProperties = null) {
        if ( ! $id ) {
            return null;
        }

        $params = array( 'id' => CRM_Utils_Type::escape($id, 'Integer') );
        $query =& new CRM_Contact_BAO_Query( $params, $returnProperties, null, false, false ); 
        $options = $query->_options;

        list( $select, $from, $where ) = $query->query( ); 
        $sql = "$select $from $where"; 
        $dao = CRM_Core_DAO::executeQuery( $sql );
        if ($dao->fetch()) {
            if (isset($dao->country)) {
                // the query returns the untranslated country name
                $i18n =& CRM_Core_I18n::singleton();
                $dao->country = $i18n->translate($dao->country);
            }
            return $dao;
        } else {
            return null;
        }
    }

    /**
     * Find contacts which match the criteria
     *
     * @param string $matchClause the matching clause
     * @param  array $tables (reference ) add the tables that are needed for the select clause
     * @param int    $id          the current contact id (hence excluded from matching)
     *
     * @return string                contact ids if match found, else null
     * @static
     * @access public
     */
    static function matchContact( $matchClause, &$tables, $id = null ) {
        $config =& CRM_Core_Config::singleton( );
        $query  = "SELECT DISTINCT civicrm_contact.id as id";
        $query .= CRM_Contact_BAO_Query::fromClause( $tables );
        $query .= " WHERE $matchClause ";
        if ( $id ) {
            $query .= " AND civicrm_contact.id != " . CRM_Utils_Type::escape($id, 'Integer') ;
        }

        $ids = array( );
        $dao =& CRM_Core_DAO::executeQuery( $query );
        while ( $dao->fetch( ) ) {
            $ids[] = $dao->id;
        }
        return implode( ',', $ids );
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
SELECT email, civicrm_location_type.name as locationType, civicrm_email.is_primary as is_primary
FROM    civicrm_contact
LEFT JOIN civicrm_location ON ( civicrm_location.entity_table = 'civicrm_contact' AND
                                civicrm_contact.id = civicrm_location.entity_id )
LEFT JOIN civicrm_location_type ON ( civicrm_location.location_type_id = civicrm_location_type.id )
LEFT JOIN civicrm_email ON ( civicrm_location.id = civicrm_email.location_id )
WHERE
  civicrm_contact.id = " . CRM_Utils_Type::escape($id, 'Integer') . "
ORDER BY
  civicrm_location.is_primary DESC, civicrm_email.is_primary DESC";
        
        $emails = array( );
        $dao =& CRM_Core_DAO::executeQuery( $query );
        while ( $dao->fetch( ) ) {
            $emails[$dao->email] = array( 'locationType' => $dao->locationType,
                                          'is_primary'   => $dao->is_primary );
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
     * @param boolean  $groupContacts if true, use a single mysql group_concat statement to get the contact ids
     *
     * @return CRM_Contact_DAO_Contact 
     * @access public
     */
    function searchQuery(&$fv, $offset, $rowCount, $sort, 
                         $count = false, $includeContactIds = false, $sortByChar = false,
                         $groupContacts = false, $returnQuery = false )
    {
        $query =& new CRM_Contact_BAO_Query( $fv, null, null,
                                             $includeContactIds );
        return $query->searchQuery( $offset, $rowCount, $sort,
                                    $count, $includeContactids,
                                    $sortByChar, $groupContacts,
                                    $returnQuery );
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
    static function add(&$params, &$ids) {
        $contact =& new CRM_Contact_BAO_Contact();
        
        $contact->copyValues($params);
        
        $contact->domain_id = CRM_Utils_Array::value( 'domain' , $ids, CRM_Core_Config::domainID( ) );
        $contact->id        = CRM_Utils_Array::value( 'contact', $ids );
        
        if ($contact->contact_type == 'Individual') {
            $sortName = "";
            $firstName  = CRM_Utils_Array::value('first_name', $params, '');
            $middleName = CRM_Utils_Array::value('middle_name', $params, '');
            $lastName   = CRM_Utils_Array::value('last_name' , $params, '');
            $prefix_id  = CRM_Utils_Array::value('prefix_id'    , $params, '');
            $suffix_id  = CRM_Utils_Array::value('suffix_id'    , $params, '');
            
            // a comma should only be present if both first_name and last name are present.
            if ($firstName && $lastName) {
                $sortName = "$lastName, $firstName";
            } else {
                if (empty($firstName) || empty($lastName)) {
                    $sortName = $lastName . $firstName;
                } else {
                    $individual =& new CRM_Contact_BAO_Individual();
                    $individual->contact_id = $contact->id;
                    $individual->find();
                    while($individual->fetch()) {
                        $individualLastName = $individual->last_name;
                        $individualFirstName = $individual->first_name;
                        $individualPrefix = $individual->prefix_id;
                        $individualSuffix = $individual->suffix_id;
                        $individualMiddleName = $individual->middle_name;
                    }
                    
                    if (empty($lastName) && !empty($individualLastName)) {
                        $lastName = $individualLastName;
                    } 
                    
                    if (empty($firstName) && !empty($individualFirstName)) {
                        $firstName = $individualFirstName;
                    }
                                                            
                    if (empty($prefix_id) && !empty($individualPrefix)) {
                        $prefix = $individualPrefix;
                    }
                    
                    if (empty($middleName) && !empty($individualMiddleName)) {
                        $middleName = $individualMiddleName;
                    }
                    
                    if (empty($suffix_id) && !empty($individualSuffix)) {
                        $suffix = $individualSuffix;
                    }
                    
                    $sortName = "$lastName, $firstName";
                }
            }
            $contact->sort_name    = trim($sortName);
            
            // get prefix and suffix names
            $prefix = CRM_Core_PseudoConstant::individualPrefix();
            $suffix = CRM_Core_PseudoConstant::individualSuffix();
            
            $contact->display_name =
                trim( $prefix[$prefix_id] . ' ' . $firstName . ' ' . $middleName . ' ' . $lastName . ' ' . $suffix[$suffix_id] );
            $contact->display_name = str_replace( '  ', ' ', $contact->display_name );

            if ( CRM_Utils_Array::value( 'location', $params ) ) {
                foreach ($params['location'] as $locBlock) {
                    if (! $locBlock['is_primary']) {
                        continue;
                    }
                    $email = $locBlock['email'][1]['email'];
                    break;
                }
            }

            if (empty($contact->display_name)) {
                if (isset($email)) {
                    $contact->display_name = $email;
                }
            }
            if (empty($contact->sort_name)) {
                if (isset($email)) {
                    $contact->sort_name = $email;
                }
            }
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
    static function &getValues( &$params, &$values, &$ids ) {

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

            CRM_Contact_DAO_Contact::addDisplayEnums($values);

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
    static function &create(&$params, &$ids, $maxLocationBlocks) {
        require_once 'CRM/Utils/Hook.php';

        if ( CRM_Utils_Array::value( 'contact', $ids ) ) {
            CRM_Utils_Hook::pre( 'edit', $params['contact_type'], $ids['contact'], $params );
        } else {
            CRM_Utils_Hook::pre( 'create', $params['contact_type'], null, $params ); 
        }

        CRM_Core_DAO::transaction('BEGIN');
        
        $contact = self::add($params, $ids);

        $params['contact_id'] = $contact->id;

        // invoke the add operator on the contact_type class
        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_BAO_" . $params['contact_type']) . ".php");
        eval('$contact->contact_type_object =& CRM_Contact_BAO_' . $params['contact_type'] . '::add($params, $ids);');

        $location = array();
        for ($locationId = 1; $locationId <= $maxLocationBlocks; $locationId++) { // start of for loop for location
            $location[$locationId] = CRM_Core_BAO_Location::add($params, $ids, $locationId);
        }
        $contact->location = $location;
	
        // add notes
        if ( CRM_Utils_Array::value( 'note', $params ) ) {
            if (is_array($params['note'])) {
                foreach ($params['note'] as $note) {  
                    $noteParams = array(
                                        'entity_id'     => $contact->id,
                                        'entity_table'  => 'civicrm_contact',
                                        'note'          => $note['note']
                                        );
                    CRM_Core_BAO_Note::add($noteParams);
                }
            } else {
                $noteParams = array(
                                    'entity_id'     => $contact->id,
                                    'entity_table'  => 'civicrm_contact',
                                    'note'          => $params['note']
                                    );
                CRM_Core_BAO_Note::add($noteParams);
            }
        }

        // update the UF email if that has changed
        require_once 'CRM/Core/BAO/UFMatch.php';
        CRM_Core_BAO_UFMatch::updateUFEmail( $contact->id );

        // add custom field values
        if ( CRM_Utils_Array::value( 'custom', $params ) ) {
            foreach ($params['custom'] as $customValue) {
                $cvParams = array(
                                  'entity_table'    => 'civicrm_contact',
                                  'entity_id'       => $contact->id,
                                  'value'           => $customValue['value'],
                                  'type'            => $customValue['type'],
                                  'custom_field_id' => $customValue['custom_field_id'],
                                  );
                
                if ($customValue['id']) {
                    $cvParams['id'] = $customValue['id'];
                }
                CRM_Core_BAO_CustomValue::create($cvParams);
            }
        }
        
        $subscriptionParams = array('contact_id' => $contact->id,
                                    'status' => 'Added',
                                    'method' => 'Admin');
        CRM_Contact_BAO_SubscriptionHistory::create($subscriptionParams);

        CRM_Core_DAO::transaction('COMMIT');
        
        if ( CRM_Utils_Array::value( 'contact', $ids ) ) {
            CRM_Utils_Hook::post( 'edit', $params['contact_type'], $contact->id, $contact );
        } else {
            CRM_Utils_Hook::post( 'create', $params['contact_type'], $contact->id, $contact );
        }

        $contact->contact_type_display = CRM_Contact_DAO_Contact::tsEnum('contact_type', $contact->contact_type);

        return $contact;
    }

    /** 
     * takes an associative array and creates a contact object and all the associated 
     * derived objects (i.e. individual, location, email, phone etc) 
     * 
     * This function is invoked from within the web form layer and also from the api layer
     * primarily from the profile / contribute forms where we dont have a nice hierarchy
     * and are too lazy to create one. This function should be obsoleted at some time
     * 
     * @param array $params (reference ) an assoc array of name/value pairs 
     * @param array $ids    the array that holds all the db ids 
     * 
     * @return object CRM_Contact_BAO_Contact object  
     * @access public 
     * @static 
     */ 
    static function &createFlat( &$params, &$ids ) {
        require_once 'CRM/Utils/Hook.php';

        if ( CRM_Utils_Array::value( 'contact', $ids ) ) {
            CRM_Utils_Hook::pre( 'edit', 'Individual', $ids['contact'], $params );
        } else {
            CRM_Utils_Hook::pre( 'create', 'Individual', null, $params ); 
        }

        CRM_Core_DAO::transaction( 'BEGIN' ); 

        $params['contact_type'] = 'Individual';
        $contact = CRM_Contact_BAO_Contact::add   ( $params, $ids );

        $params['contact_id'] = $contact->id;

        require_once 'CRM/Contact/BAO/Individual.php';
        CRM_Contact_BAO_Individual::add( $params, $ids );

        require_once 'CRM/Core/BAO/LocationType.php';
        $locationType   =& CRM_Core_BAO_LocationType::getDefault( ); 
        $locationTypeId =  $locationType->id;

        $locationIds = CRM_Utils_Array::value( 'location', $ids );
        // extract the first location id
        if ( $locationIds ) {
            foreach ( $locationIds as $dontCare => $locationId ) {
                $locationIds = $locationId;
                break;
            }
        }
            
        $location =& new CRM_Core_DAO_Location( );
        $location->location_type_id = $locationTypeId;
        $location->entity_table = 'civicrm_contact';
        $location->entity_id    = $contact->id;
        $location->id = CRM_Utils_Array::value( 'id', $locationIds );

        if ( $location->find( true ) ) {
            if ( ! $location->is_primary ) {
                $location->is_primary = true;
            }
        } else {
            $location->is_primary = true;
        }
        $location->save( );
       
        
        $address =& new CRM_Core_BAO_Address();
        CRM_Core_BAO_Address::fixAddress( $params );
        
        if ( ! $address->copyValues( $params ) ) {
            $address->id = CRM_Utils_Array::value( 'address', $locationIds );
            $address->location_id = $location->id;
            $address->save( );
        }

        $phone =& new CRM_Core_BAO_Phone();
        if ( ! $phone->copyValues( $params ) ) {
            $blockIds = CRM_Utils_Array::value( 'phone', $locationIds );
            $phone->id = CRM_Utils_Array::value( 1, $blockIds );
            $phone->location_id = $location->id;
            $phone->is_primary = true;
            $phone->save( );
        }
        
        $email =& new CRM_Core_BAO_Email();
        if ( ! $email->copyValues( $params ) ) {
            $blockIds = CRM_Utils_Array::value( 'email', $locationIds );
            $email->id = CRM_Utils_Array::value( 1, $blockIds );
            $email->location_id = $location->id;
            $email->is_primary = true;
            $email->save( );
        }

        /* Process custom field values and other values */
        foreach ($params as $key => $value) {
            if ( $key == 'group' ) {
                CRM_Contact_BAO_GroupContact::create( $params['group'], $contact->id );
            } else if ( $key == 'tag' ) {
                require_once 'CRM/Core/BAO/EntityTag.php';
                CRM_Core_BAO_EntityTag::create( $params['tag'], $contact->id );
            } else if ($cfID = CRM_Core_BAO_CustomField::getKeyID($key) ) {
                $custom_field_id = $cfID;
                $cf =& new CRM_Core_BAO_CustomField();
                $cf->id = $custom_field_id;
                if ( $cf->find( true ) ) {
                    switch($cf->html_type) {
                    case 'Select Date':
                        $date = CRM_Utils_Date::format( $value );
                        if ( ! $date ) {
                            $date = '';
                        }
                        $customValue = $date;
                        break;
                    case 'CheckBox':
                        $customValue = implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, array_keys($value));
                        break;
                    //added a case for Multi-Select
                    case 'Multi-Select':
                        $customValue = implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, array_keys($value));
                        break;
                    default:
                        $customValue = $value;
                    }
                }
            
                CRM_Core_BAO_CustomValue::updateValue($contact->id, $custom_field_id, $customValue);
            }
        }

        CRM_Core_DAO::transaction( 'COMMIT' ); 

        if ( CRM_Utils_Array::value( 'contact', $ids ) ) {
            CRM_Utils_Hook::post( 'edit', 'Individual', $contact->id, $contact );
        } else {
            CRM_Utils_Hook::post( 'create', 'Individual', $contact->id, $contact );
        }

        return $contact;
    }

    /**
     * Get the display name and image of a contact
     *
     * @param int $id the contactId
     *
     * @return array the displayName and contactImage for this contact
     * @access public
     * @static
     */
    static function getDisplayAndImage( $id ) {
        $sql = "
SELECT    civicrm_contact.display_name as display_name,
          civicrm_contact.contact_type as contact_type,
          civicrm_email.email          as email       
FROM      civicrm_contact
LEFT JOIN civicrm_location ON (civicrm_location.entity_table = 'civicrm_contact' AND
                               civicrm_contact.id = civicrm_location.entity_id AND
                               civicrm_location.is_primary = 1)
LEFT JOIN civicrm_email ON (civicrm_location.id = civicrm_email.location_id AND civicrm_email.is_primary = 1)
WHERE     civicrm_contact.id = " . CRM_Utils_Type::escape($id, 'Integer');
        $dao =& new CRM_Core_DAO( );
        $dao->query( $sql );
        if ( $dao->fetch( ) ) {
            $image = self::getImage( $dao->contact_type );

            // use email if display_name is empty
            if ( empty( $dao->display_name ) ) {
                $dao->display_name = $dao->email;
            }
            return array( $dao->display_name, $image );
        }
        return null;
    }

    /**
     * given a contact type, get the contact image
     *
     * @param string $contact_type
     *
     * @return string
     * @access public
     * @static
     */
    static function getImage( $contactType ) {
        $config =& CRM_Core_Config::singleton( );
        $image = '<img src="' . $config->resourceBase . 'i/contact_';
        switch ( $contactType ) { 
        case 'Individual' : 
            $image .= 'ind.gif" alt="' . ts('Individual') . '" />'; 
            break; 
        case 'Household' : 
            $image .= 'house.png" alt="' . ts('Household') . '" height="16" width="16" />'; 
            break; 
        case 'Organization' : 
            $image .= 'org.gif" alt="' . ts('Organization') . '" height="16" width="18" />'; 
            break; 
        } 
        return $image;
    }

    /**
     *
     * Get the values for pseudoconstants for name->value and reverse.
     *
     * @param array   $defaults (reference) the default values, some of which need to be resolved.
     * @param boolean $reverse  true if we want to resolve the values in the reverse direction (value -> name)
     *
     * @return none
     * @access public
     * @static
     */
    static function resolveDefaults( &$defaults, $reverse = false ) {
        // hack for birth_date
        if ( CRM_Utils_Array::value( 'birth_date', $defaults ) ) {
            if (is_array($defaults['birth_date'])) {
                $defaults['birth_date'] = CRM_Utils_Date::format( 
                                                                 $defaults['birth_date'], '-' 
                                                                );
            }
        } 

        if ( CRM_Utils_Array::value( 'prefix', $defaults ) ) {
            self::lookupValue( $defaults, 'prefix', CRM_Core_PseudoConstant::individualPrefix(), $reverse );
        }  

        if ( CRM_Utils_Array::value( 'suffix', $defaults ) ) {
            self::lookupValue( $defaults, 'suffix', CRM_Core_PseudoConstant::individualSuffix(), $reverse );
        }  

        if ( CRM_Utils_Array::value( 'gender', $defaults ) ) {
            self::lookupValue( $defaults, 'gender', CRM_Core_PseudoConstant::gender(), $reverse );
        }

        if ( array_key_exists( 'location', $defaults ) ) {
            $locations =& $defaults['location'];

            foreach ($locations as $index => $location) {                
                $location =& $locations[$index];
                self::lookupValue( $location, 'location_type', CRM_Core_PseudoConstant::locationType(), $reverse );

                // FIXME: lookupValue doesn't work for vcard_name
                $vcardNames =& CRM_Core_PseudoConstant::locationVcardName();
                $location['vcard_name'] = $vcardNames[$location['location_type_id']];

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
        $locParams = $params + array('entity_id' => $params['contact_id'],
                                     'entity_table' => self::getTableName());
        $contact->location     =& CRM_Core_BAO_Location::getValues( $locParams, $defaults, $ids, 3 );
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
     * Given a parameter array from CRM_Contact_BAO_Contact::retrieve() and a
     * key to search for, search recursively for that key's value.
     *
     * @param array $values     The parameter array
     * @param string $key       The key to search for
     * @return mixed            The value of the key, or null.
     * @access public
     * @static
     */
    static function retrieveValue(&$params, $key) {
        if (! is_array($params)) {
            return null;
        } else if ($value = CRM_Utils_Array::value($key, $params)) {
            return $value;
        } else {
            foreach ($params as $subParam) {
                if ($value = self::retrieveValue($subParam, $key)) {
                    return $value;
                }
            }
        }
        return null;
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
     * @return array    tuple of display_name and email if found, or (null,null)
     * @static
     * @access public
     */
    static function getEmailDetails( $id ) {
        $sql = " SELECT    civicrm_contact.display_name, civicrm_email.email
                 FROM      civicrm_contact
                 LEFT JOIN civicrm_location ON (civicrm_location.entity_table = 'civicrm_contact' AND
                                                civicrm_contact.id = civicrm_location.entity_id AND
                                                civicrm_location.is_primary = 1)
                 LEFT JOIN civicrm_email ON (civicrm_location.id = civicrm_email.location_id AND civicrm_email.is_primary = 1)
                 WHERE     civicrm_contact.id = " . CRM_Utils_Type::escape($id, 'Integer');
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
  civicrm_contact.id as contact_id,
  civicrm_contact.contact_type as contact_type,
  civicrm_contact.display_name as display_name,
  civicrm_address.street_address as street_address,
  civicrm_address.city as city,
  civicrm_address.postal_code as postal_code,
  civicrm_address.postal_code_suffix as postal_code_suffix,
  civicrm_address.geo_code_1 as latitude,
  civicrm_address.geo_code_2 as longitude,
  civicrm_state_province.abbreviation as state,
  civicrm_country.name as country,
  civicrm_location_type.name as location_type
FROM      civicrm_contact
LEFT JOIN civicrm_location ON (civicrm_location.entity_table = 'civicrm_contact' AND
                               civicrm_contact.id = civicrm_location.entity_id AND
                               civicrm_location.is_primary = 1)
LEFT JOIN civicrm_address ON civicrm_location.id = civicrm_address.location_id
LEFT JOIN civicrm_state_province ON civicrm_address.state_province_id = civicrm_state_province.id
LEFT JOIN civicrm_country ON civicrm_address.country_id = civicrm_country.id
LEFT JOIN civicrm_location_type ON civicrm_location_type.id = civicrm_location.location_type_id
WHERE     civicrm_contact.id IN $idString AND civicrm_address.geo_code_1 is not null AND civicrm_address.geo_code_2 is not null";

        $dao =& new CRM_Core_DAO( );
        $dao->query( $sql );

        $locations = array( );

        $config =& CRM_Core_Config::singleton( );

        while ( $dao->fetch( ) ) {
            $location = array( );
            $location['displayName'] = $dao->display_name ;
            $location['lat'        ] = $dao->latitude;
            $location['lng'        ] = $dao->longitude;
            $address = '';
            /*CRM_Utils_String::append( $address, ', ',
                                      array( $dao->street_address, $dao->city, $dao->state, $dao->postal_code, $dao->country ) );*/
            CRM_Utils_String::append( $address, '<br />',
                                      array( $dao->street_address, $dao->city) );
            CRM_Utils_String::append( $address, ', ',
                                      array(   $dao->state, $dao->postal_code ) );
            CRM_Utils_String::append( $address, '<br /> ',
                                      array( $dao->country ) );
            $location['address'      ] = $address;
            $location['url'          ] = CRM_Utils_System::url( 'civicrm/contact/view', 'reset=1&cid=' . $dao->contact_id );
            $location['location_type'] = $dao->location_type;
            
            $contact_type    = '<img src="' . $config->resourceBase . 'i/contact_';
            switch ($dao->contact_type) {
            case 'Individual' :
                $contact_type .= 'ind_medium.gif" alt="' . ts('Individual') . '" />';
                break;
            case 'Household' :
                $contact_type .= 'house.png" alt="' . ts('Household') . '" height="25" width="25" />';
                break;
            case 'Organization' :
                $contact_type .= 'org.gif" alt="' . ts('Organization') . '" height="25" width="30" />';
                break;
            }
            $location['contactImage'] = $contact_type;
            $locations[] = $location;
        }
        return $locations;
    }

    /**
     * Delete a contact and all its associated records
     * 
     * @param  int  $id id of the contact to delete
     *
     * @return boolean true if contact deleted, false otherwise
     * @access public
     * @static
     */
    function deleteContact( $id ) {
        require_once 'CRM/Core/BAO/EmailHistory.php';
        require_once 'CRM/Core/BAO/Meeting.php';
        require_once 'CRM/Core/BAO/Phonecall.php';
        // make sure we have edit permission for this contact
        // before we delete
        if ( ! self::permissionedContact( $id, CRM_Core_Permission::EDIT ) ) {
            return false;
        }
            
        require_once 'CRM/Utils/Hook.php';

        $contact =& new CRM_Contact_DAO_Contact();
        $contact->id = $id;
        if (! $contact->find(true)) {
            return false;
        }
        $contactType = $contact->contact_type;

        CRM_Utils_Hook::pre( 'delete', $contactType, $id );

        CRM_Core_DAO::transaction( 'BEGIN' );

        // do a top down deletion
        CRM_Mailing_Event_BAO_Subscribe::deleteContact( $id );

        CRM_Contact_BAO_GroupContact::deleteContact( $id );
        CRM_Contact_BAO_SubscriptionHistory::deleteContact($id);
        
        CRM_Contact_BAO_Relationship::deleteContact( $id );

        CRM_Contribute_BAO_Contribution::deleteContact( $id );

        CRM_Core_BAO_Note::deleteContact($id);

        CRM_Core_DAO::deleteEntityContact( 'CRM_Core_DAO_CustomValue', $id );

        CRM_Core_DAO::deleteEntityContact( 'CRM_Core_DAO_ActivityHistory', $id );

        require_once 'CRM/Core/BAO/UFMatch.php';
        CRM_Core_BAO_UFMatch::deleteContact( $id );
        
        // need to remove them from email, meeting and phonecall
        CRM_Core_BAO_EmailHistory::deleteContact($id);
        CRM_Core_BAO_Meeting::deleteContact($id);
        CRM_Core_BAO_Phonecall::deleteContact($id);

        // location shld be deleted after phonecall, since fields in phonecall are
        // fkeyed into location/phone.
        CRM_Core_BAO_Location::deleteContact( $id );

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

        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_BAO_" . $contactType) . ".php");
        eval( '$object =& new CRM_Contact_BAO_' . $contactType . '( );' );
        $object->contact_id = $contact->id;
        $object->delete( );
        $contact->delete( );

        //delete the contact id from recently view
        CRM_Utils_Recent::del($id);

        CRM_Core_DAO::transaction( 'COMMIT' );

        CRM_Utils_Hook::post( 'delete', $contactType, $contact->id, $contact );

        return true;
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
     * @param int $contactType contact Type
     * @return array array of importable Fields
     * @access public
     */
    function &importableFields( $contactType = 'Individual', $status = null ) {
        // the line below is commented coz,
        // if the importableFields are once set then they do not
        // allow to set with different contactTypes

        if ( ! self::$_importableFields || ! CRM_Utils_Array::value( $contactType, self::$_importableFields ) ) {
            if ( ! self::$_importableFields ) {
                self::$_importableFields = array();
            }
            if (!$status) {
                $fields = array( 'do_not_import' => array( 'title' => ts('- do not import -') ) );
            } else {
                $fields = array( '' => array( 'title' => ts('- Contact Fields -') ) );
            }

            if ( $contactType != 'All' ) {
                require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_DAO_" . $contactType) . ".php");
                eval('$tmpFields = array_merge($fields, CRM_Contact_DAO_'.$contactType.'::import( ));');
                $fields = array_merge( $fields, $tmpFields );
            } else {
                foreach ( array( 'Individual', 'Household', 'Organization' ) as $type ) {
                    require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_DAO_" . $type) . ".php");
                    eval('$tmpFields = array_merge($fields, CRM_Contact_DAO_'.$type.'::import( ));');
                    $fields = array_merge( $fields, $tmpFields );
                }
            }

            // the fields are only meant for Individual contact type
            if ( $contactType == 'Individual') {
                $fields = array_merge( $fields, CRM_Core_DAO_IndividualPrefix::import( true ) ,
                                       CRM_Core_DAO_IndividualSuffix::import( true ) ,
                                       CRM_Core_DAO_Gender::import( true ) );                
            }
            
            $locationFields = array_merge(  CRM_Core_DAO_Address::import( ),
                                            CRM_Core_DAO_Phone::import( ),
                                            CRM_Core_DAO_Email::import( ),
                                            CRM_Core_DAO_IM::import( true )
                                            );
            foreach ($locationFields as $key => $field) {
                $locationFields[$key]['hasLocationType'] = true;
            }

            $fields = array_merge($fields, $locationFields);

            $fields = array_merge($fields,
                                  CRM_Contact_DAO_Contact::import( ) );
            $fields = array_merge($fields,
                                  CRM_Core_DAO_Note::import());
            if ( $contactType != 'All' ) { 

                $fields = array_merge($fields, CRM_Core_BAO_CustomField::getFieldsForImport($contactType, $status) );
            } else {
                foreach ( array( 'Individual', 'Household', 'Organization' ) as $type ) { 
                    $fields = array_merge($fields, 
                                          CRM_Core_BAO_CustomField::getFieldsForImport($type));
                }
            }

            self::$_importableFields[$contactType] = $fields;
        }
        return self::$_importableFields[$contactType];
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
    static function getNumOpenActivity($id) {

        // this is not sufficient way to do.
        $id = CRM_Utils_Type::escape($id, 'Integer');

        $query = "SELECT count(*) FROM civicrm_meeting 
                  WHERE (civicrm_meeting.target_entity_table = 'civicrm_contact' 
                  AND target_entity_id = $id
                  OR source_contact_id = $id)
                  AND status != 'Completed'";
        $rowMeeting = CRM_Core_DAO::singleValueQuery( $query );
        
        $query = "SELECT count(*) FROM civicrm_phonecall 
                  WHERE (civicrm_phonecall.target_entity_table = 'civicrm_contact' 
                  AND target_entity_id = $id
                  OR source_contact_id = $id)
                  AND status != 'Completed'";
        $rowPhonecall = CRM_Core_DAO::singleValueQuery( $query ); 
        
        $query = "SELECT count(*) FROM civicrm_activity,civicrm_activity_type 
                  WHERE (civicrm_activity.target_entity_table = 'civicrm_contact' 
                  AND target_entity_id = $id
                  OR source_contact_id = $id )
                  AND civicrm_activity_type.id = civicrm_activity.activity_type_id 
                  AND civicrm_activity_type.is_active = 1  AND status != 'Completed'";
        $rowActivity = CRM_Core_DAO::singleValueQuery( $query ); 

        return  $rowMeeting + $rowPhonecall + $rowActivity;
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
    static function &getOpenActivities(&$params, $offset=null, $rowCount=null, $sort=null, $type='Activity') {
        require_once 'CRM/Core/DAO/Phonecall.php';
        $dao =& new CRM_Core_DAO();
        $contactId = CRM_Utils_Type::escape( $params['contact_id'], 'Integer' );
        
        $query = "
( SELECT
    civicrm_phonecall.id as id,
    civicrm_phonecall.source_contact_id as source_contact_id, 
    civicrm_phonecall.target_entity_id as  target_contact_id,
    civicrm_phonecall.subject as subject,
    civicrm_phonecall.scheduled_date_time as date,
    civicrm_phonecall.status as status,
    source.display_name as sourceName,
    target.display_name as targetName,
    civicrm_activity_type.id  as activity_type_id,
    civicrm_activity_type.name  as activity_type
  FROM civicrm_activity_type, civicrm_phonecall, civicrm_contact source, civicrm_contact target
  WHERE
    civicrm_activity_type.id = 2 AND
    civicrm_phonecall.source_contact_id = source.id AND
    civicrm_phonecall.target_entity_table = 'civicrm_contact' AND
    civicrm_phonecall.target_entity_id = target.id AND
    ( civicrm_phonecall.source_contact_id = $contactId
    OR civicrm_phonecall.target_entity_id = $contactId )
    AND civicrm_phonecall.status != 'Completed'
 ) UNION
( SELECT   
    civicrm_meeting.id as id,
    civicrm_meeting.source_contact_id as source_contact_id,
    civicrm_meeting.target_entity_id as  target_contact_id,
    civicrm_meeting.subject as subject,
    civicrm_meeting.scheduled_date_time as date,
    civicrm_meeting.status as status,
    source.display_name as sourceName,
    target.display_name as targetName,
    civicrm_activity_type.id  as activity_type_id,
    civicrm_activity_type.name  as activity_type
  FROM civicrm_activity_type, civicrm_meeting, civicrm_contact source, civicrm_contact target
  WHERE
    civicrm_activity_type.id = 1 AND
    civicrm_meeting.source_contact_id = source.id AND
    civicrm_meeting.target_entity_table = 'civicrm_contact' AND
    civicrm_meeting.target_entity_id = target.id AND
    ( civicrm_meeting.source_contact_id = $contactId
    OR civicrm_meeting.target_entity_id = $contactId )
    AND civicrm_meeting.status != 'Completed'
) UNION
( SELECT   
    civicrm_activity.id as id,
    civicrm_activity.source_contact_id as source_contact_id,
    civicrm_activity.target_entity_id as  target_contact_id,
    civicrm_activity.subject as subject,
    civicrm_activity.scheduled_date_time as date,
    civicrm_activity.status as status,
    source.display_name as sourceName,
    target.display_name as targetName,
    civicrm_activity_type.id  as activity_type_id,
    civicrm_activity_type.name  as activity_type
  FROM civicrm_activity, civicrm_contact source, civicrm_contact target ,civicrm_activity_type
  WHERE
    civicrm_activity.source_contact_id = source.id AND
    civicrm_activity.target_entity_table = 'civicrm_contact' AND
    civicrm_activity.target_entity_id = target.id AND
    ( civicrm_activity.source_contact_id = $contactId
    OR civicrm_activity.target_entity_id = $contactId ) AND
    civicrm_activity_type.id = civicrm_activity.activity_type_id AND civicrm_activity_type.is_active = 1 AND 
    civicrm_activity.status != 'Completed'
            )
";
        $order = '';
        if ($sort) {
            $orderBy = $sort->orderBy();
            if ( ! empty( $orderBy ) ) {
                $order = " ORDER BY $orderBy";
            }
        }

        if ( empty( $order ) ) {
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
            $values[$rowCnt]['activity_type_id'] = $dao->activity_type_id;        
            $values[$rowCnt]['activity_type'] = $dao->activity_type;
            $values[$rowCnt]['id']      = $dao->id;
            $values[$rowCnt]['subject'] = $dao->subject;
            $values[$rowCnt]['date']    = $dao->date;
            $values[$rowCnt]['status']  = $dao->status;
            $values[$rowCnt]['sourceName'] = $dao->sourceName;
            $values[$rowCnt]['targetName'] = $dao->targetName;
            $values[$rowCnt]['sourceID'] = $dao->source_contact_id;
            $values[$rowCnt]['targetID'] = $dao->target_contact_id;
            $rowCnt++;
        }
        foreach ($values as $key => $array) {
            CRM_Core_DAO_Meeting::addDisplayEnums($values[$key]);
            CRM_Core_DAO_Phonecall::addDisplayEnums($values[$key]);
        }
        return $values;
    }
    
    /**
     * combine all the exportable fields from the lower levels object
     * 
     * currentlty we are using importable fields as exportable fields
     *
     * @param int $contactType contact Type
     * $param int $status  1 while exporting primary contacts
     *
     * @return array array of exportable Fields
     * @access public
     */
    function &exportableFields( $contactType = 'Individual', $status = null ) {
        
        if ( ! self::$_exportableFields || ! CRM_Utils_Array::value( $contactType, self::$_exportableFields ) ) {
            if ( ! self::$_exportableFields ) {
                self::$_exportableFields = array();
            }
            if (!$status) {
                $fields = array();
            } else {
                $fields = array( '' => array( 'title' => ts('- Contact Fields -') ) );
            }
            //$fields = array();

            if ( $contactType != 'All' ) {
                require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_DAO_" . $contactType) . ".php");
                eval('$fields = array_merge($fields, CRM_Contact_DAO_'.$contactType.'::export( ));');
            } else {
                foreach ( array( 'Individual', 'Household', 'Organization' ) as $type ) {
                    require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_DAO_" . $type) . ".php");
                    eval('$fields = array_merge($fields, CRM_Contact_DAO_'.$type.'::export( ));');
                    if ( $type == 'Individual') {
                        $fields = array_merge( $fields,
                                               CRM_Core_DAO_IndividualPrefix::export( true ) , 
                                               CRM_Core_DAO_IndividualSuffix::export( true ) , 
                                               CRM_Core_DAO_Gender::export( true ) );
                    }
                }
            }

            // the fields are only meant for Individual contact type
            if ( $contactType == 'Individual') {
                $fields = array_merge( $fields, CRM_Core_DAO_IndividualPrefix::export( true ) ,
                                       CRM_Core_DAO_IndividualSuffix::export( true ) ,
                                       CRM_Core_DAO_Gender::export( true ) );                
            }
            
            $locationType = array( );
            if ($status) {
                $locationType['location_type'] = array ('name' => 'location_type', 'where' => 'civicrm_location_type.name', 'title' => 'Location Type');
            }
            
            $IMProvider = array( );
            if ($status) {
                $IMProvider['im_provider'] = array ('name' => 'im_provider', 'where' => 'civicrm_im_provider.name', 'title' => 'IM Provider');
            }
            
            $locationFields = array_merge(  $locationType,
                                            CRM_Core_DAO_Address::export( ),
                                            CRM_Core_DAO_Phone::export( ),
                                            CRM_Core_DAO_Email::export( ),
                                            $IMProvider,
                                            CRM_Core_DAO_IM::export( true )
                                            );
            
            foreach ($locationFields as $key => $field) {
                $locationFields[$key]['hasLocationType'] = true;
            }

            $fields = array_merge($fields, $locationFields);

            $fields = array_merge($fields,
                                  CRM_Contact_DAO_Contact::export( ) );
            $fields = array_merge($fields,
                                  CRM_Core_DAO_Note::export());
            if ( $contactType != 'All' ) { 

                    $fields = array_merge($fields,
                                      CRM_Core_BAO_CustomField::getFieldsForImport($contactType, $status) );
                
            } else {
                foreach ( array( 'Individual', 'Household', 'Organization' ) as $type ) { 
                    $fields = array_merge($fields, 
                                          CRM_Core_BAO_CustomField::getFieldsForImport($type));
                }
            }

            self::$_exportableFields[$contactType] = $fields;
        }
        return self::$_exportableFields[$contactType];

    }

    /**
     * Function to get the all contact details(Hierarchical)
     *
     * @param int   $contactId contact id
     * @param array $fields fields array
     *
     * @return $values array contains the contact details
     * @static
     * @access public
     */
    static function getHierContactDetails( $contactId, &$fields ) 
    {
        $params  = array( 'id' => $contactId );
        $options = array( );
                
        $returnProperties = CRM_Contact_BAO_Query::defaultHierReturnProperties( );

        //add custom fields to the return array
        foreach ($fields as $key => $value) {
            if (substr($key, 0, 6) === 'custom') {
                $returnProperties[$key] = 1; 
            }
        }
        return list($query, $options) = CRM_Contact_BAO_Query::apiQuery( $params, $returnProperties, $options );
    }

    /**
     * Function to return the primary location type of a contact 
     * 
     * $params int $contactId contact_id
     *
     * @return int $locationType location_type_id
     * @access public
     * @static
     */
    static function getPrimaryLocationType($contactId) 
    {
        require_once 'CRM/Core/BAO/Location.php';
        $location =& new CRM_Core_DAO_Location( ); 
        $location->entity_table = 'civicrm_contact';
        $location->entity_id    = $contactId;
        $location->is_primary   = 1;
        $location->find(true);
        
        return $location->location_type_id;
    }
 
}

?>
