<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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

require_once 'CRM/Core/DAO/Note.php';
require_once 'CRM/Core/Form.php';

require_once 'CRM/Contact/DAO/Contact.php';

require_once 'CRM/Core/DAO/Address.php';
require_once 'CRM/Core/DAO/Phone.php';
require_once 'CRM/Core/DAO/Email.php';
require_once 'CRM/Core/DAO/IM.php';
require_once 'CRM/Core/DAO/OptionValue.php';
require_once 'CRM/Core/BAO/CustomField.php';
require_once 'CRM/Core/BAO/CustomValue.php';
require_once 'CRM/Core/BAO/Location.php';
require_once 'CRM/Core/BAO/Note.php';

require_once 'CRM/Contact/BAO/Query.php';
require_once 'CRM/Contact/BAO/Relationship.php';
require_once 'CRM/Contact/BAO/GroupContact.php';
require_once 'CRM/Core/Permission.php';
require_once 'CRM/Mailing/Event/BAO/Subscribe.php';

require_once 'CRM/Core/BAO/OptionValue.php';

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
    static function permissionedContact( $id, $type = CRM_Core_Permission::VIEW ) 
    {
        $tables     = array( );
        $temp       = array( );
        require_once 'CRM/ACL/API.php';
        $permission = CRM_ACL_API::whereClause( $type, $tables, $temp );

        $from       = CRM_Contact_BAO_Query::fromClause( $tables );
        $query = "
SELECT count(DISTINCT contact_a.id) 
       $from
WHERE contact_a.id = %1 AND $permission";
        $params = array( 1 => array( $id, 'Integer' ) );

        return ( CRM_Core_DAO::singleValueQuery( $query, $params ) > 0 ) ? true : false;
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
    static function contactDetails( $id, &$options, $returnProperties = null) 
    {
        if ( ! $id ) {
            return null;
        }
        $params[] = array( '0' => 'id' ,'2'=> CRM_Utils_Type::escape($id, 'Integer') );
        $query =& new CRM_Contact_BAO_Query( $params, $returnProperties, null, false, false ); 
        $options = $query->_options;

        list( $select, $from, $where ) = $query->query( ); 
        $sql = "$select $from $where"; 
        $dao = CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray );
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
    static function matchContact( $matchClause, &$tables, $id = null ) 
    {
        // check whether rule is empty or none
        // if not empty then matchContact other wise skip matchcontact
        // updated for CRM-974
        require_once 'CRM/Core/DAO/DupeMatch.php';
        $dupeMatchDAO = & new CRM_Core_DAO_DupeMatch();
        $dupeMatchDAO->find(true);
        if (!($dupeMatchDAO->rule == 'none')){
            $config =& CRM_Core_Config::singleton( );
            $query  = "SELECT DISTINCT contact_a.id as id";
            $query .= CRM_Contact_BAO_Query::fromClause( $tables );
            $query .= " WHERE $matchClause ";
            $params = array( );
            if ( $id ) {
                $query .= " AND contact_a.id != %1";
                $params[1] = array( $id, 'Integer' );
            }
            $ids = array( );
            $dao =& CRM_Core_DAO::executeQuery( $query, $params );
            while ( $dao->fetch( ) ) {
                $ids[] = $dao->id;
            }
            $dao->free( );
            return implode( ',', $ids );
        } else {
             return null;
        }
     }
    

    static function updatePrimaryEmail( $contactID, $email ) {
        $query = "
    UPDATE civicrm_contact
INNER JOIN civicrm_email    ON ( civicrm_contact.id = civicrm_email.contact_id )
       SET civicrm_email.email      = %1
     WHERE civicrm_contact.id       = %2 
       AND civicrm_email.is_primary = 1";
        
        $p = array( 1 => array( $email    , 'String'  ),
                    2 => array( $contactID, 'Integer' ) );
        CRM_Core_DAO::executeQuery( $query, $p );
    }

    /**
     * create and query the db for an contact search
     *
     * @param array    $params   the search input params
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
    function searchQuery(&$params, $offset, $rowCount, $sort, 
                         $count = false, $includeContactIds = false, $sortByChar = false,
                         $groupContacts = false, $returnQuery = false )
    {
        $query =& new CRM_Contact_BAO_Query( $params, null, null,
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
    static function add( &$params ) 
    {
        $contact =& new CRM_Contact_DAO_Contact();

        if ( empty($params) ) {
            return;
        }

        //fixed contact source
        if ( isset($params['contact_source']) ) {
            $params['source'] = $params['contact_source'];
        }

        //fix for preferred communication method
        $prefComm = CRM_Utils_Array::value('preferred_communication_method', $params);
        if ( $prefComm && is_array( $prefComm) ) {
            unset($params['preferred_communication_method']);
            $newPref = array();
            
            foreach ( $prefComm  as $k => $v ) {
                if ( $v ) {
                    $newPref[$k] = $v;
                }
            }
            
            $prefComm =  $newPref;
            if ( is_array($prefComm) && !empty($prefComm) ) {
                $prefComm =
                    CRM_Core_BAO_CustomOption::VALUE_SEPERATOR .
                    implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,array_keys($prefComm)) .
                    CRM_Core_BAO_CustomOption::VALUE_SEPERATOR;
                $contact->preferred_communication_method = $prefComm;
            } else {
                $contact->preferred_communication_method = '';
            }
        }

        $contact->copyValues($params);

        $contact->domain_id = CRM_Utils_Array::value( 'domain_id' , $params, CRM_Core_Config::domainID( ) );
        $contact->id        = CRM_Utils_Array::value( 'contact_id', $params );
        
        if ( $contact->contact_type == 'Individual') {
            //unset organization name 
            $contact->organization_name = 'null';

            //format individual fields
            require_once "CRM/Contact/BAO/Individual.php";
            CRM_Contact_BAO_Individual::format( $params, $contact );
        } else if ($contact->contact_type == 'Household') {
            $contact->display_name = $contact->sort_name = CRM_Utils_Array::value('household_name', $params, '');
        } else {
            $contact->display_name = $contact->sort_name = CRM_Utils_Array::value('organization_name', $params, '') ;
        }

        // privacy block
        $privacy = CRM_Utils_Array::value('privacy', $params);
        if ($privacy && is_array($privacy)) {
            foreach (self::$_commPrefs as $name) {
                $contact->$name = CRM_Utils_Array::value($name, $privacy, false);
            }
        }

        // since hash was required, make sure we have a 0 value for it, CRM-1063
        // fixed in 1.5 by making hash optional
        // only do this in create mode, not update
        if ( ( ! array_key_exists( 'hash', $contact ) || ! $contact->hash ) && ! $contact->id ) {
            $contact->hash = md5( uniqid( rand( ), true ) );
        }

        $contact->save( );

        require_once 'CRM/Core/BAO/Log.php';
        CRM_Core_BAO_Log::register( $contact->id,
                                    'civicrm_contact',
                                    $contact->id );
                           
        return $contact;
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
    static function &getValues( &$params, &$values ) 
    {
        $contact =& new CRM_Contact_BAO_Contact( );

        $contact->copyValues( $params );

        if ( $contact->find(true) ) {

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
            
            // communication Prefferance
            $preffComm = $comm = array();
            $comm =explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,$contact->preferred_communication_method);
            foreach( $comm as $value ) {
                $preffComm[$value] = 1; 
            }
            $temp  = array('preferred_communication_method' => $contact->preferred_communication_method );
            
            $names = array('preferred_communication_method' => array('newName'   => 'preferred_communication_method_display',
                                                                     'groupName' => 'preferred_communication_method'));
            
            require_once 'CRM/Core/OptionGroup.php';
            CRM_Core_OptionGroup::lookupValues( $temp, $names, false );                
                            
            $values['preferred_communication_method']          = $preffComm;
            $values['preferred_communication_method_display']  = 
                CRM_Utils_Array::value( 'preferred_communication_method_display', $temp );
            
            CRM_Contact_DAO_Contact::addDisplayEnums($values);
            
            // Calculating Year difference            
            if ( $contact->birth_date ) {
                $birthDate = CRM_Utils_Date::customFormat( $contact->birth_date,'%Y%m%d' );  
                if ( $birthDate < date( 'Ymd' ) ) {
                    $age =  self::findAge( $birthDate );
                    $values['age']['y'] = CRM_Utils_Array::value('years',$age);
                    $values['age']['m'] = CRM_Utils_Array::value('months',$age);
                 }
            }

            $contact->contact_id = $contact->id;
            
            return $contact;
        }
        return null;
    }
    
    /**
     * Function to create contact
     * takes an associative array and creates a contact object and all the associated
     * derived objects (i.e. individual, location, email, phone etc)
     *
     * This function is invoked from within the web form layer and also from the api layer
     *
     * @param array   $params      (reference ) an assoc array of name/value pairs
     * @param array   $ids         the array that holds all the db ids
     * @param boolean $fixAddress  if we need to fix address
     * @param boolean $invokeHooks if we need to invoke hooks
     *
     * @return object CRM_Contact_BAO_Contact object 
     * @access public
     * @static
     */
    static function &create(&$params, $fixAddress = true, $invokeHooks = true ) 
    {
        if (!$params['contact_type'] ) {
            return;
        }
        
        if ( $invokeHooks ) {
            require_once 'CRM/Utils/Hook.php';
            if ( CRM_Utils_Array::value( 'contact', $ids ) ) {
                CRM_Utils_Hook::pre( 'edit', $params['contact_type'], $ids['contact'], $params );
            } else {
                CRM_Utils_Hook::pre( 'create', $params['contact_type'], null, $params ); 
            }
        }

        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );

        $contact = self::add( $params );
        
        $params['contact_id'] = $contact->id;

        //add location Block data
        
        $location = array( );
        $location = CRM_Core_BAO_Location::create( $params, $fixAddress );
        
        $contact->location = $location;
	
        // add notes
        if ( CRM_Utils_Array::value( 'note', $params ) ) {
            if (is_array($params['note'])) {
                foreach ($params['note'] as $note) {  
                    $noteParams = array(
                                        'entity_id'     => $contact->id,
                                        'entity_table'  => 'civicrm_contact',
                                        'note'          => $note['note'],
                                        'contact_id'    => $contact->id
                                        );
                    CRM_Core_BAO_Note::add($noteParams, CRM_Core_DAO::$_nullArray);
                }
            } else {
                $noteParams = array(
                                    'entity_id'     => $contact->id,
                                    'entity_table'  => 'civicrm_contact',
                                    'note'          => $params['note'],
                                    'contact_id'    => $contact->id
                                    );
                CRM_Core_BAO_Note::add($noteParams, CRM_Core_DAO::$_nullArray);
            }
        }

        // update the UF user_unique_id if that has changed
        require_once 'CRM/Core/BAO/UFMatch.php';
        //DO TO: comment because of schema changes
        //CRM_Core_BAO_UFMatch::updateUFUserUniqueId( $contact->id );

        if ( CRM_Utils_Array::value( 'custom', $params ) &&
             is_array( $params['custom'] ) ) {
            require_once 'CRM/Core/BAO/CustomValueTable.php';
            CRM_Core_BAO_CustomValueTable::store( $params['custom'], 'civicrm_contact', $contact->id );
        }

        // make a civicrm_subscription_history entry only on contact create (CRM-777)
        if ( ! CRM_Utils_Array::value( 'contact', $ids ) ) {
            $subscriptionParams = array('contact_id' => $contact->id,
                                        'status' => 'Added',
                                        'method' => 'Admin');
            CRM_Contact_BAO_SubscriptionHistory::create($subscriptionParams);
        }

        $transaction->commit( );
        
        $contact->contact_type_display = CRM_Contact_DAO_Contact::tsEnum('contact_type', $contact->contact_type);

        if ( $invokeHooks ) {
            if ( CRM_Utils_Array::value( 'contact', $ids ) ) {
                CRM_Utils_Hook::post( 'edit', $params['contact_type'], $contact->id, $contact );
            } else {
                CRM_Utils_Hook::post( 'create', $params['contact_type'], $contact->id, $contact );
            }
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
    static function getDisplayAndImage( $id ) 
    {
        $sql = "
SELECT    civicrm_contact.display_name as display_name,
          civicrm_contact.contact_type as contact_type,
          civicrm_email.email          as email       
FROM      civicrm_contact
LEFT JOIN civicrm_email ON civicrm_email.contact_id = civicrm_contact.id
     AND  civicrm_email.is_primary = 1
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
    static function getImage( $contactType ) 
    {
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
    static function resolveDefaults( &$defaults, $reverse = false ) 
    {
        // hack for birth_date
        if ( CRM_Utils_Array::value( 'birth_date', $defaults ) ) {
            if (is_array($defaults['birth_date'])) {
                $defaults['birth_date'] = CRM_Utils_Date::format( 
                                                                 $defaults['birth_date'], '-' 
                                                                );
            }
        } 

        self::lookupValue( $defaults, 'prefix', CRM_Core_PseudoConstant::individualPrefix(), $reverse );
        self::lookupValue( $defaults, 'suffix', CRM_Core_PseudoConstant::individualSuffix(), $reverse );
        self::lookupValue( $defaults, 'gender', CRM_Core_PseudoConstant::gender(), $reverse );

        if ( array_key_exists( 'location', $defaults ) ) {
            $locations =& $defaults['location'];

            foreach ($locations as $index => $location) {                
                $location =& $locations[$index];
                self::lookupValue( $location, 'location_type', CRM_Core_PseudoConstant::locationType(), $reverse );

                // FIXME: lookupValue doesn't work for vcard_name
                $vcardNames =& CRM_Core_PseudoConstant::locationVcardName();
                if ( isset( $location['location_type_id'] ) ) {
                    $location['vcard_name'] = $vcardNames[$location['location_type_id']];
                }

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
                    self::lookupValue( $location['address'], 'county'        , CRM_Core_PseudoConstant::county()         , $reverse );
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
    static function lookupValue( &$defaults, $property, $lookup, $reverse ) 
    {
        $id = $property . '_id';

        $src = $reverse ? $property : $id;
        $dst = $reverse ? $id       : $property;
        
        if ( ! array_key_exists( strtolower($src), array_change_key_case( $defaults, CASE_LOWER )) ) {
            return false;
        }

        $look = $reverse ? array_flip( $lookup ) : $lookup;
        
        //trim lookup array, ignore . ( fix for CRM-1514 ), eg for prefix/suffix make sure Dr. and Dr both are valid
        $newLook = array( );
        foreach( $look as $k => $v) {
            $newLook[trim($k, ".")] = $v;
        }

        $look = $newLook;

        if(is_array($look)) {
            if ( ! array_key_exists( trim(strtolower( $defaults[strtolower($src)] ),'.'),  array_change_key_case( $look, CASE_LOWER )) ) {
                return false;
            }
        }
        
        $tempLook = array_change_key_case( $look ,CASE_LOWER);

        $defaults[$dst] = $tempLook[trim(strtolower( $defaults[strtolower($src)] ),'.')];
        return true;
    }

    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * contact_id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array   $params   (reference ) an assoc array of name/value pairs
     * @param array   $defaults (reference ) an assoc array to hold the name / value pairs
     *                        in a hierarchical manner
     * @param array   $ids      (reference) the array that holds all the db ids
     * @param boolean $microformat  for location in microformat
     *
     * @return object CRM_Contact_BAO_Contact object
     * @access public
     * @static
     */
    static function &retrieve( &$params, &$defaults, $microformat = false ) 
    {
        if ( array_key_exists( 'contact_id', $params ) ) {
            $params['id'] = $params['contact_id'];
        } else if ( array_key_exists( 'id', $params ) ) {
            $params['contact_id'] = $params['id'];
        }

        $contact = CRM_Contact_BAO_Contact::getValues( $params, $defaults );
        unset($params['id']);
        
        //DO TO: commented because of schema change

//         $locParams = $params + array('entity_id' => $params['contact_id'],
//                                      'entity_table' => self::getTableName());
       
//         $locationCount = CRM_Contact_BAO_Contact::getContactLocations( $params['contact_id'] ); 

//         require_once "CRM/Core/BAO/Preferences.php";
//         $contact->location     =& CRM_Core_BAO_Location::getValues( $locParams, 
//                                                                     $defaults, 
//                                                                     $ids, 
//                                                                     $locationCount, 
//                                                                     $microformat );

        //get the block information for this contact
        $entityBlock = array( 'contact_id' => $params['contact_id'] );
        $contact->location  =& CRM_Core_BAO_Location::getValues( $entityBlock, 
                                                                 $defaults, 
                                                                 $microformat );
        
        $contact->notes        =& CRM_Core_BAO_Note::getValues( $params, $defaults );
        $contact->relationship =& CRM_Contact_BAO_Relationship::getValues( $params, $defaults );
        $contact->groupContact =& CRM_Contact_BAO_GroupContact::getValues( $params, $defaults );
        
        //DO TO: commented because of schema change
//         $activityParam         =  array('entity_id' => $params['contact_id']);
//         require_once 'CRM/Core/BAO/History.php';
//         $contact->activity     =& CRM_Core_BAO_History::getValues($activityParam, $defaults, 'Activity');

//         $activityParam            =  array('contact_id' => $params['contact_id']);
//         $defaults['openActivity'] = array(
//                                           'data'       => self::getOpenActivities( $activityParam, 0, 3 ),
//                                           'totalCount' => self::getNumOpenActivity( $params['contact_id'] ),
//                                           );

//	DRAFTING: this should be the proper way to get open activities
//        $contact->activity     =& CRM_Activity_BAO_Activity::getValues( $params, $defaults, $ids );
        
        return $contact;
    }

    static function freeHack( &$contact ) 
    {
        unset( $contact->location     );
        unset( $contact->notes        );
        unset( $contact->relationship );
        unset( $contact->groupContact );
        unset( $contact->activity     );
        unset( $contact );
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
    static function retrieveValue(&$params, $key) 
    {
        if (! is_array($params)) {
            return null;
        } else if ($value = CRM_Utils_Array::value($key, $params)) {
            return $value;
        } else {
            foreach ($params as $subParam) {
                if ( is_array( $subParam ) &&
                     $value = self::retrieveValue( $subParam, $key ) ) {
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
    static function displayName( $id ) 
    {
        return CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $id, 'display_name' );
    }

    /**
     * function to get the display name, primary email, location type and location id of a contact
     *
     * @param  int    $id id of the contact
     *
     * @return array  of display_name, email, location type and location id if found, or (null,null,null, null)
     * @static
     * @access public
     */
    static function getEmailDetails( $id, $locationTypeID = null ) 
    {
        $locationClause = null;
        if ( $locationTypeID ) {
            $locationClause = " AND civicrm_email.location_type_id = $locationTypeID";
        }

        $sql = "
SELECT    civicrm_contact.display_name,
          civicrm_email.email,
          civicrm_email.location_type_id,
          civicrm_email.id
FROM      civicrm_contact
LEFT JOIN civicrm_email ON ( civicrm_contact.id = civicrm_email.contact_id )
    WHERE civicrm_email.is_primary = 1
      AND civicrm_contact.id = " . CRM_Utils_Type::escape($id, 'Integer');

        $dao =& new CRM_Core_DAO( );
        $dao->query( $sql );
        $result = $dao->getDatabaseResult();
        if ( $result ) {
            $row    = $result->fetchRow();
            if ( $row ) {
                return array( $row[0], $row[1], $row[2], $row[3] );
            }
        }
        return array( null, null, null, null );
    }

    /**
     * function to get the sms number and display name of a contact
     *
     * @param  int    $id id of the contact
     *
     * @return array    tuple of display_name and sms if found, or (null,null)
     * @static
     * @access public
     */
    static function getPhoneDetails( $id, $type = null ) 
    {
        if ( ! $id ) {
            return array( null, null );
        }

        $cond = null;
        if ( $type ) {
            $cond = " AND civicrm_phone.phone_type = '$type'";
        }


        $sql = "
   SELECT civicrm_contact.display_name, civicrm_phone.phone
     FROM civicrm_contact
LEFT JOIN civicrm_phone ON ( civicrm_phone.contact_id = civicrm_contact.id )
    WHERE civicrm_phone.is_primary = 1
          $cond
      AND civicrm_contact.id = " . CRM_Utils_Type::escape($id, 'Integer');

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
     * @param  array  $ids    the list of ids for which we want map info
     * $param  int    $locationTypeID
     *
     * @return null|string     display name of the contact if found
     * @static
     * @access public
     */
    static function &getMapInfo( $ids, $locationTypeID = null ) 
    {
        $idString = ' ( ' . implode( ',', $ids ) . ' ) ';
        $sql = "
   SELECT civicrm_contact.id as contact_id,
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
     FROM civicrm_contact
LEFT JOIN civicrm_address ON civicrm_address.contact_id = civicrm_contact.id
LEFT JOIN civicrm_state_province ON civicrm_address.state_province_id = civicrm_state_province.id
LEFT JOIN civicrm_country ON civicrm_address.country_id = civicrm_country.id
LEFT JOIN civicrm_location_type ON civicrm_location_type.id = civicrm_address.location_type_id
WHERE civicrm_contact.id IN $idString ";

        $params = array( );
        if (!$locationId) {
            $sql .= " AND civicrm_address.is_primary = 1";
        } else {
            $sql .= " AND civicrm_address.location_type_id = %1";
            $params[1] = array( $locationTypeID, 'Integer' );
        }

        $dao =& CRM_Core_DAO::executeQuery( $sql, $params );

        $locations = array( );
        $config =& CRM_Core_Config::singleton( );

        while ( $dao->fetch( ) ) {
            $location = array( );
            $location['contactID'  ] = $dao->contact_id;
            $location['displayName'] = $dao->display_name ;
            $location['city'       ] = $dao->city;
            $location['state'      ] = $dao->state;
            $location['postal_code'] = $dao->postal_code;
            $location['lat'        ] = $dao->latitude;
            $location['lng'        ] = $dao->longitude;
            $address = '';

            CRM_Utils_String::append( $address, '<br />',
                                      array( $dao->street_address, $dao->city) );
            CRM_Utils_String::append( $address, ', ',
                                      array(   $dao->state, $dao->postal_code ) );
            CRM_Utils_String::append( $address, '<br /> ',
                                      array( $dao->country ) );
            $location['address'       ] = $address;
            $location['displayAddress'] = str_replace( '<br />', ', ', $address );
            $location['url'           ] = CRM_Utils_System::url( 'civicrm/contact/view', 'reset=1&cid=' . $dao->contact_id );
            $location['location_type' ] = $dao->location_type;
            
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
            $location['image'] = $contact_type;
            $locations[] = $location;
        }
        return $locations;
    }

    /**
     * Delete a contact and all its associated records
     * 
     * @param  int  $id id of the contact to delete
     *
     * @userID int  $userId  the Logged-in id of the Civicrm.
     *
     * @return boolean true if contact deleted, false otherwise
     * @access public
     * @static
     */
    function deleteContact( $id, $userId = null ) 
    {
        require_once 'CRM/Activity/BAO/Activity.php';

        if ( ! $id ) {
            return false;
        }

        // make sure we have edit permission for this contact
        // before we delete
        if ( ! self::permissionedContact( $id, CRM_Core_Permission::EDIT ) ) {
            return false;
        }

        // make sure this contact_id does not have any membership types
        $membershipTypeID = CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipType',
                                                         $id,
                                                         'id',
                                                         'member_of_contact_id' );
        if ( $membershipTypeID ) {
            return false;
        }
                                                         
        require_once 'CRM/Utils/Hook.php';

        $contact =& new CRM_Contact_DAO_Contact();
        $contact->id = $id;
        if (! $contact->find(true)) {
            return false;
        }
        $contactType = $contact->contact_type;

        CRM_Utils_Hook::pre( 'delete', $contactType, $id, CRM_Core_DAO::$_nullArray );

        // start a new transaction
        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );

        // need to remove them from email, meeting , phonecall and other activities
        // FIX ME: Schema Change
        
        // CRM_Activity_BAO_Activity::deleteContact($id);

        // location shld be deleted after phonecall, since fields in phonecall are
        // fkeyed into location/phone.
        // CRM_Core_BAO_Location::deleteContact( $id );

        $contact->delete( );

        //delete the contact id from recently view
        CRM_Utils_Recent::del($id);

        $transaction->commit( );

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
     * Get contact sub type for a contact.
     *
     * @param int $id - id of the contact whose contact sub type is needed
     *
     * @return string contact_type if $id found else null ""
     *
     * @access public
     *
     * @static
     *
     */
    public static function getContactSubType($id)
    {
        return CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $id, 'contact_sub_type' );
    }

    /**
     * combine all the importable fields from the lower levels object
     *
     * The ordering is important, since currently we do not have a weight
     * scheme. Adding weight is super important and should be done in the
     * next week or so, before this can be called complete.
     *
     * @param int     $contactType contact Type
     * @param boolean $status  status is used to manipulate first title
     * @param boolean $showAll if true returns all fields (includes disabled fields)
     *
     * @return array array of importable Fields
     * @access public
     */
    function &importableFields( $contactType = 'Individual', $status = false, $showAll = false ) 
    {
        if ( empty( $contactType ) ) {
            $contactType = 'All';
        }
        
        if ( ! self::$_importableFields || ! CRM_Utils_Array::value( $contactType, self::$_importableFields ) ) {
            if ( ! self::$_importableFields ) {
                self::$_importableFields = array();
            }
            if (!$status) {
                $fields = array( 'do_not_import' => array( 'title' => ts('- do not import -') ) );
            } else {
                $fields = array( '' => array( 'title' => ts('- Contact Fields -') ) );
            }
           
            $fields = array_merge($fields, CRM_Contact_DAO_Contact::import( ));

            require_once "CRM/Core/OptionValue.php";
            // the fields are only meant for Individual contact type
            if ( ($contactType == 'Individual') || ($contactType == 'All')) {
                $fields = array_merge( $fields, CRM_Core_OptionValue::getFields( ) );                
            }
            $locationFields = array_merge( CRM_Core_DAO_Address::import( ),
                                           CRM_Core_DAO_Phone::import( ),
                                           CRM_Core_DAO_Email::import( ),
                                           CRM_Core_DAO_IM::import( true ),
                                           CRM_Core_DAO_OpenID::import( )
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

                $fields = array_merge($fields, CRM_Core_BAO_CustomField::getFieldsForImport($contactType, $showAll) );
            } else {
                foreach ( array( 'Individual', 'Household', 'Organization' ) as $type ) { 
                    $fields = array_merge($fields, CRM_Core_BAO_CustomField::getFieldsForImport($type, $showAll));
                }
            }

            //Sorting fields in alphabetical order(CRM-1507)
            foreach ( $fields as $k=>$v ) {
                $sortArray[$k] = $v['title'];
            }
            asort($sortArray);
            $fields = array_merge( $sortArray, $fields );
            
            self::$_importableFields[$contactType] = $fields;
        }
        return self::$_importableFields[$contactType];
    }
    
    /**
     * combine all the exportable fields from the lower levels object
     * 
     * currentlty we are using importable fields as exportable fields
     *
     * @param int     $contactType contact Type
     * $param boolean $status true while exporting primary contacts
     * $param boolean $export true when used during export
     *
     * @return array array of exportable Fields
     * @access public
     */
    function &exportableFields( $contactType = 'Individual', $status = false, $export = false ) 
    {
        if ( empty( $contactType ) ) {
            $contactType = 'All';
        }
        
        if ( ! self::$_exportableFields || ! CRM_Utils_Array::value( $contactType, self::$_exportableFields ) ) {
            if ( ! self::$_exportableFields ) {
                self::$_exportableFields = array();
            }
            if (!$status) {
                $fields = array();
            } else {
                $fields = array( '' => array( 'title' => ts('- Contact Fields -') ) );
            }

            $fields = array_merge($fields, CRM_Contact_DAO_Contact::export( ));
            
            // the fields are only meant for Individual contact type
            if ( $contactType == 'Individual') {
                require_once 'CRM/Core/OptionValue.php';
                $fields = array_merge( $fields, CRM_Core_OptionValue::getFields( ) );                
            }
            
            $locationType = array( );
            if ($status) {
                $locationType['location_type'] = array ('name' => 'location_type',
                                                        'where' => 'civicrm_location_type.name',
                                                        'title' => 'Location Type');
            }
            
            $IMProvider = array( );
            if ( $status ) {
                $IMProvider['im_provider'] = array ('name' => 'im_provider',
                                                    'where' => 'im_provider.name',
                                                    'title' => 'IM Provider');
            }
            
            $locationFields = array_merge(  $locationType,
                                            CRM_Core_DAO_Address::export( ),
                                            CRM_Core_DAO_Phone::export( ),
                                            CRM_Core_DAO_Email::export( ),
                                            $IMProvider,
                                            CRM_Core_DAO_IM::export( true ),
                                            CRM_Core_DAO_OpenID::export( )
                                            );
            
            foreach ($locationFields as $key => $field) {
                $locationFields[$key]['hasLocationType'] = true;
            }

            $fields = array_merge($fields, $locationFields);

            //add world region
            require_once "CRM/Core/DAO/Worldregion.php";
            $fields = array_merge($fields,
                                  CRM_Core_DAO_Worldregion::export( ) );


            $fields = array_merge($fields,
                                  CRM_Contact_DAO_Contact::export( ) );

            if ( $contactType != 'All' ) { 
                $fields = array_merge($fields,
                                      CRM_Core_BAO_CustomField::getFieldsForImport($contactType, $status) );
                
            } else {
                foreach ( array( 'Individual', 'Household', 'Organization' ) as $type ) { 
                    $fields = array_merge($fields, 
                                          CRM_Core_BAO_CustomField::getFieldsForImport($type));
                    //fix for CRM-2394
                    if ( $type == 'Individual' ) { 
                        require_once "CRM/Core/OptionValue.php";
                        $fields = array_merge( $fields,
                                               CRM_Core_OptionValue::getFields( )
                                               );
                    }
                }
            }
            
            //fix for CRM-791
            if ( $export ) {
                $fields = array_merge( $fields, array ( 'groups' => array( 'title' => ts( 'Group(s)' ) ),
                                                        'tags'   => array( 'title'  => ts( 'Tag(s)'  ) ),
                                                        'notes'  => array( 'title'  => ts( 'Note(s)' ) ) ) );
            } else { 
                $fields = array_merge( $fields, array ( 'group'  => array( 'title' => ts( 'Group(s)' ) ),
                                                        'tag'    => array( 'title'  => ts( 'Tag(s)'  ) ),
                                                        'note'   => array( 'title'  => ts( 'Note(s)' ) ) ) );
            }
            
            //Sorting fields in alphabetical order(CRM-1507)
            foreach ( $fields as $k=>$v ) {
                $sortArray[$k] = CRM_Utils_Array::value( 'title', $v );
            }
            asort($sortArray);
            $fields = array_merge( $sortArray, $fields );
            
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
        $params  = array( array( 'contact_id', '=', $contactId, 0, 0 ) ); 
        $options = array( );                

        $returnProperties =& self::makeHierReturnProperties( $fields, $contactId );

        // we dont know the contents of return properties, but we need the lower level ids of the contact
        // so add a few fields
        $returnProperties['first_name'] = $returnProperties['organization_name'] = $returnProperties['household_name'] = 1;
        return list($query, $options) = CRM_Contact_BAO_Query::apiQuery( $params, $returnProperties, $options );        
    }

    /**
     * given a set of flat profile style field names, create a hierarchy
     * for query to use and crete the right sql
     *
     * @param array $properties a flat return properties name value array
     * @param int   $contactId contact id
     * 
     * @return array a hierarchical property tree if appropriate
     * @access public
     * @static
     */
    static function &makeHierReturnProperties( $fields, $contactId = null ) 
    {
        require_once 'CRM/Core/PseudoConstant.php';
        $locationTypes = CRM_Core_PseudoConstant::locationType( );

        $returnProperties = array( );
        $locationIds = array( );
        foreach ( $fields as $name => $dontCare ) {
            if ( strpos( $name, '-' ) !== false ) {
                list( $fieldName, $id, $type ) = CRM_Utils_System::explode( '-', $name, 3 );

                if ($id == 'Primary') {
                    $locationTypeName = 1;
                } else {
                    $locationTypeName = CRM_Utils_Array::value( $id, $locationTypes );
                    if ( ! $locationTypeName ) {
                       continue;
                    }
                }

                if ( ! CRM_Utils_Array::value( 'location', $returnProperties ) ) {
                    $returnProperties['location'] = array( );
                }
                if ( ! CRM_Utils_Array::value( $locationTypeName, $returnProperties['location'] ) ) {
                    $returnProperties['location'][$locationTypeName] = array( );
                    $returnProperties['location'][$locationTypeName]['location_type'] = $id;
                }
                if ( in_array( $fieldName, array( 'phone', 'im', 'email', 'openid' ) ) ) {
                    if ( $type ) {
                        $returnProperties['location'][$locationTypeName][$fieldName . '-' . $type] = 1;
                    } else {
                        $returnProperties['location'][$locationTypeName][$fieldName . '-1'] = 1;
                    }
                } else {
                    $returnProperties['location'][$locationTypeName][$fieldName] = 1;
                }
            } else {
                $returnProperties[$name] = 1;
            }
        }

        return $returnProperties;
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
//         require_once 'CRM/Core/BAO/Location.php';
//         $location =& new CRM_Core_DAO_Location( ); 
//         $location->entity_table = 'civicrm_contact';
//         $location->entity_id    = $contactId;
//         $location->is_primary   = 1;
//         $location->find(true);
        
        //return $location->location_type_id;
        return 1;
    }


    /**
     * function to get the display name, primary email and location type of a contact
     *
     * @param  int    $id id of the contact
     *
     * @return array  of display_name, email if found, do_not_email or (null,null,null)
     * @static
     * @access public
     */
    static function getContactDetails( $id ) 
    {
        // check if the contact type
        $contactType =  CRM_Contact_BAO_Contact::getContactType( $id );

        // if individual
       if( $contactType == 'Individual') {
           $sql = "
   SELECT civicrm_contact.first_name, civicrm_contact.last_name,  civicrm_email.email, civicrm_contact.do_not_email
     FROM civicrm_contact, civicrm_email 
   WHERE  civicrm_contact.id = civicrm_email.contact_id AND civicrm_email.is_primary = 1
          AND civicrm_contact.id = %1";
           $params = array( 1 => array( $id, 'Integer' ) );
       } else { // for household / organization
           $sql = "
   SELECT civicrm_contact.display_name, civicrm_email.email, civicrm_contact.do_not_email
     FROM civicrm_contact, civicrm_email 
   WHERE civicrm_contact.id = civicrm_email.contact_id AND civicrm_email.is_primary = 1
      AND civicrm_contact.id = %1";
           $params = array( 1 => array( $id, 'Integer' ) );
        }
 
       $dao =& CRM_Core_DAO::executeQuery( $sql, $params );
       $result = $dao->getDatabaseResult();
       if ( $result ) {
           $row  = $result->fetchRow();
           if ( $row ) {
               if ($contactType == 'Individual') {
                   $name       = $row[0] . ' ' . $row[1];
                   $email      = $row[2];
                   $doNotEmail = $row[3] ? true : false;
               } else {
                   $name       = $row[0];
                   $email      = $row[1];
                   $doNotEmail = $row[2] ? true : false;
               }
               return array( $name, $email, $doNotEmail );
           }
       }
       return array( null, null, null );
    }

    /**
     * function to add/edit/register contacts through profile.
     *
     * @params  array  $params        Array of profile fields to be edited/added.
     * @params  int    $contactID     contact_id of the contact to be edited/added.
     * @params  array  $fields        array of fields from UFGroup
     * @params  int    $addToGroupID  specifies the default group to which contact is added.
     * @params  int    $ufGroupId     uf group id (profile id)
     * @param   string $ctype         contact type
     *
     * @return  int                   contact id created/edited
     * @static
     * @access public
     */
    static function createProfileContact( &$params, &$fields, $contactID = null,
                                          $addToGroupID = null, $ufGroupId = null,
                                          $ctype = null ) 
    {
        // add ufGroupID to params array ( CRM-2012 )
        if ( $ufGroupId ) {
            $params['uf_group_id'] = $ufGroupId;
        }

        require_once 'CRM/Utils/Hook.php';
        if ( $contactID ) {
            $editHook = true;
            CRM_Utils_Hook::pre( 'edit'  , 'Profile', $contactID, $params );
        } else {
            $editHook = false;
            CRM_Utils_Hook::pre( 'create', 'Profile', null, $params ); 
        }

        $data = array( );
        if ($ufGroupId) {
            require_once "CRM/Core/BAO/UFField.php";
            $data['contact_type'] = CRM_Core_BAO_UFField::getProfileType($ufGroupId);
        } else if ( $ctype ) {
            $data['contact_type'] = $ctype;
        } else {
            $data['contact_type'] = 'Individual';
        }
        
        // get the contact details (hier)
        if ( $contactID ) {
            list($details, $options) = CRM_Contact_BAO_Contact::getHierContactDetails( $contactID, $fields );
            $contactDetails = $details[$contactID];
        }

        if ( $ctype == "Organization" ) {
            $data["organization_name"] = $contactDetails["organization_name"];
        } else if ( $ctype == "Household" ) {
            $data["household_name"] = $contactDetails["household_name"];
        }

        $locationType = array( );
        $count = 1;
        
        if ( $contactID ) {
            //add contact id
            $data['contact_id'] = $contactID;
            $primaryLocationType = CRM_Contact_BAO_Contact::getPrimaryLocationType($contactID);
        } else {
            require_once "CRM/Core/BAO/LocationType.php";
            $defaultLocation =& CRM_Core_BAO_LocationType::getDefault();
            $defaultLocationId = $defaultLocation->id;
        }
        
        $phoneLoc   = 0;
        $phoneReset = array( );
        $imLoc      = 1; 
        
        foreach ($params as $key => $value) {
            $fieldName = $locTypeId = $typeId = null;
            list($fieldName, $locTypeId, $typeId) = CRM_Utils_System::explode('-', $key, 3);
                        
            if ($locTypeId == 'Primary') {
                if ( $contactID ) {
                    $locTypeId = $primaryLocationType; 
                } else {
                    $locTypeId = $defaultLocationId;
                }
            }
            if ( is_numeric($locTypeId) ) { 
                if ( ! in_array($locTypeId, $locationType) ) { 
                    $locationType[$count] = $locTypeId;
                    $count++; 
                }
                
                require_once 'CRM/Utils/Array.php';
                $loc = CRM_Utils_Array::key($locTypeId, $locationType);
                
                if ( isset($data['location']) && is_array($data['location']) && !array_key_exists($loc, $data['location']) ) {
                    $phoneLoc = 0;
                }
                                
                $data['location'][$loc]['location_type_id'] = $locTypeId;
                
                if ($contactID) {
                    //get the primary location type
                    if ($locTypeId == $primaryLocationType) {
                        $data['location'][$loc]['is_primary'] = 1;
                    } 
                } else {
                    //if ( $loc == 1 ) {
                    if  ( $locTypeId == $defaultLocationId ) {
                        $data['location'][$loc]['is_primary'] = 1;
                    }
                }
                
                if ($fieldName == 'location_name') {
                    $data['location'][$loc]['location_name'] = $value;
                } else if ($fieldName == 'phone') {
                    if ( !in_array($loc, $phoneReset) ) {
                        $phoneReset[] = $loc;
                        $phoneLoc = 1;
                    } else {
                        $phoneLoc++;
                    }
                    if ( $typeId ) {
                        $data['location'][$loc]['phone'][$phoneLoc]['phone_type'] = $typeId;
                    } else {
                        $data['location'][$loc]['phone'][$phoneLoc]['phone_type'] = '';
                        $data['location'][$loc]['phone'][$phoneLoc]['is_primary'] = 1;
                    }
                    $data['location'][$loc]['phone'][$phoneLoc]['phone'] = $value;
                } else if ($fieldName == 'email') {
                    $data['location'][$loc]['email'][1]['email'] = $value;
                    $data['location'][$loc]['email'][1]['is_primary'] = 1;
                } else if ($fieldName == 'im') {
                    if ( $typeId ) {
                        // this is im_provider_id
                        $data['location'][$loc]['im'][$imLoc]['provider_id'] = $value;
                    } else {
                        // this is name 
                        $data['location'][$loc]['im'][$imLoc]['name']        = $value;
                        
                        // FIXME: we are assuming that provider_id will
                        // always comes before name in $params
                        // array. we need some better logic here                        
                        $imLoc++;
                    }
                    
                    if ( $imLoc == 1 ) {
                        $data['location'][$loc]['im'][$imLoc]['is_primary']  = 1;
                    }
                } else if ($fieldName == 'openid') {
                    $data['location'][$loc]['openid'][1]['openid']     = $value;
                    $data['location'][$loc]['openid'][1]['is_primary'] = 1;
                } else {
                    if ($fieldName === 'state_province') {
                        if ( is_numeric( $value ) ) {
                          $data['location'][$loc]['address']['state_province_id'] = $value;
                        } else {
                          $data['location'][$loc]['address']['state_province'] = $value;
                        }
                    } else if ($fieldName === 'country') {
                        if ( is_numeric( $value ) ) {
                          $data['location'][$loc]['address']['country_id'] = $value;
                        } else {
                          $data['location'][$loc]['address']['country'] = $value;
                        }
                    } else if ($fieldName === 'county') {
                        $data['location'][$loc]['address']['county_id'] = $value;
                    } else {
                        $data['location'][$loc]['address'][$fieldName] = $value;
                    }
                }
            } else {
                if ($key === 'individual_suffix') { 
                    $data['suffix_id'] = $value;
                } else if ($key === 'individual_prefix') { 
                    $data['prefix_id'] = $value;
                } else if ($key === 'gender') { 
                    $data['gender_id'] = $value;
                } else if ($customFieldId = CRM_Core_BAO_CustomField::getKeyID($key)) {
                    CRM_Core_BAO_CustomField::formatCustomField( $customFieldId, $data['custom'], 
                                                                 $value, $data['contact_type'],
                                                                 null, $contactID );
                } else if ($key == 'edit') {
                    continue;
                } else {
                    $data[$key] = $value;
                }
            }
        }
        
        //get the custom fields for the contact
        $customFields = CRM_Core_BAO_CustomField::getFields( $data['contact_type'] );

        $studentFieldPresent = 0;
        $TMFFieldPresent     = 0;
        // fix all the custom field checkboxes which are empty
        foreach ($fields as $name => $field ) {
            if ( CRM_Core_Permission::access( 'Quest' ) ) {
                // check if student fields present
                require_once 'CRM/Quest/BAO/Student.php';
                if ( (!$studentFieldPresent) && array_key_exists($name, CRM_Quest_BAO_Student::exportableFields()) ) {
                   $studentFieldPresent = 1;
                }
            }

            if ( CRM_Core_Permission::access( 'TMF' ) ) {
                // check if student fields present
                require_once 'CRM/TMF/BAO/Student.php';
                if ( (!$TMFFieldPresent) && array_key_exists($name, CRM_TMF_BAO_Student::exportableFields()) ) {
                   $TMFFieldPresent = 1;
                   unset($data['contact_type']);
                }
            }
           
            $cfID = CRM_Core_BAO_CustomField::getKeyID($name);
            // if there is a custom field of type checkbox,multi-select and it has not been set
            // then set it to null, thanx to html protocol
            if ( $cfID &&
                 ($customFields[$cfID][3] == 'CheckBox' || $customFields[$cfID][3] == 'Multi-Select')&&
                 ! CRM_Utils_Array::value( $cfID, $data['custom'] ) ) {

                $str = "custom_value_{$cfID}_id";
                $customOptionValueId = $contactDetails[$str] ? $contactDetails[$str] : NULL;
                CRM_Core_BAO_CustomField::formatCustomField( $cfID, $data['custom'], 
                                                             '', $data['contact_type'], $customOptionValueId);
            }
        }
       
        if ($contactID) {
            $objects = array( 'contact_id'      => 'contact',
                              'individual_id'   => 'individual',
                              'household_id'    => 'household',
                              'organization_id' => 'organization',
                              'location_id'     => 'location',
                              'address_id'      => 'address'
                              );
            $ids = array( ); 
            if ( is_array($contactDetails) ) {
                foreach ($contactDetails as $key => $value) {
                    if ( array_key_exists($key, $objects) ) {
                        //add non location ids
                        $ids[$objects[$key]] = $value;
                    } else if (is_array($value)) {
                        
                        $locNo = array_search( $value['location_type_id'], $locationType );
                        if ( ! $locNo ) {
                            if ( is_numeric( $key ) ) {
                                $locNo = $key;
                            } else {
                                $locNo = array_search( $key, $locationType );
                            }
                        }
                        
                        if ( ! $locNo ) {
                            CRM_Core_Error::fatal( ts( 'Could not find location type id' ) );
                        }

                        foreach ($value as $k => $v) {
                            if ( array_key_exists($k, $objects)) {
                                if ( ! isset( $ids['location'] ) ||
                                     ! is_array( $ids['location'] ) ) {
                                   $ids['location'] = array( );
                                }
                                if ($k == 'location_id') {
                                    $ids['location'][$locNo]['id'] = $v;
                                    //store location type id
                                    $ids['location'][$locNo]['location_type_id'] = $value['location_type_id'];
                                } else {
                                    $ids['location'][$locNo][$objects[$k]] = $v;
                                }
                            } else if (is_array($v)) {
                                //build phone/email/im/openid ids
                                if ( in_array ($k, array('phone', 'email', 'im', 'openid')) ) {
                                    $no = 1;
                                    foreach ($v as $k1 => $v1) {
                                        if (substr($k1, strlen($k1) - 2, strlen($k1)) == "id") {
                                            $ids['location'][$locNo][$k][$no] = $v1;
                                            $no++;
                                        }                                    
                                    }
                                } 
                            }
                        }
                    }
                }
            }
            
        }
        
        //set the values for checkboxes (do_not_email, do_not_mail, do_not_trade, do_not_phone)
        $privacy = CRM_Core_SelectValues::privacy( );
        foreach ($privacy as $key => $value) {
            if (array_key_exists($key, $fields)) {
                if ($params[$key]) {
                    $data[$key] = $params[$key];
                } else {
                    $data[$key] = 0;
                }
            }
        }
        
        // manage is_opt_out
        if (array_key_exists('is_opt_out', $fields)) {
            $wasOptOut = $contactDetails['is_opt_out'] ? true : false;
            $isOptOut  = $params['is_opt_out']         ? true : false;
            $data['is_opt_out'] = $isOptOut;
            // on change, create new civicrm_subscription_history entry
            if (($wasOptOut != $isOptOut) && $contactDetails['contact_id'] ) {
                $shParams = array(
                                  'contact_id' => $contactDetails['contact_id'],
                                  'status'     => $isOptOut ? 'Removed' : 'Added',
                                  'method'     => 'Web',
                                  );
                CRM_Contact_BAO_SubscriptionHistory::create($shParams);
            }
        }

        require_once 'CRM/Contact/BAO/Contact.php';
        if ( $data['contact_type'] != 'Student' && $data['contact_type'] != 'TMF' ) {
            $contact =& CRM_Contact_BAO_Contact::create( $data );
        }
        
        // contact is null if the profile does not have any contact fields
        if ( $contact ) {
          $contactID = $contact->id;
        } else if ( array_key_exists( 'contact', $ids ) ) {
          $contactID = $ids['contact'];
        } 
        
        if ( ! $contactID ) {
          CRM_Core_Error::fatal( 'Cannot proceed without a valid contact id' );
        }

        if ( $data['contact_type'] == 'Individual' && 
            array_key_exists( 'organization_name', $params ) ) {
            if ( $params['organization_name'] )  {
                self::makeCurrentEmployerRelationship($contactID, $params['organization_name']);
            }
        }

        // Process group and tag  
        if ( CRM_Utils_Array::value('group', $fields )) {
            CRM_Contact_BAO_GroupContact::create( $params['group'], $contactID );
        }
        
        if ( CRM_Utils_Array::value('tag', $fields )) {
            require_once 'CRM/Core/BAO/EntityTag.php';
            CRM_Core_BAO_EntityTag::create( $params['tag'], $contactID );
        } 
        
        //to add profile in default group
        if ( is_array ($addToGroupID) ) {
            $contactIds = array($contactID);
            foreach ( $addToGroupID as $groupId ) {
                CRM_Contact_BAO_GroupContact::addContactsToGroup( $contactIds, $groupId );
            }
        } else if ( $addToGroupID ) {
            $contactIds = array($contactID);
            CRM_Contact_BAO_GroupContact::addContactsToGroup( $contactIds, $addToGroupID );
        }


        //to update student record
        if ( CRM_Core_Permission::access( 'Quest' ) && $studentFieldPresent ) {
            $ids = array();
            $dao = & new CRM_Quest_DAO_Student();
            $dao->contact_id = $contactID;
            if ($dao->find(true)) {
                $ids['id'] = $dao->id;
            }

            $ssids = array( );
            $studentSummary = & new CRM_Quest_DAO_StudentSummary();
            $studentSummary->contact_id = $contactID;
            if ($studentSummary->find(true)) {
                $ssids['id'] = $studentSummary->id;
            }

            $params['contact_id'] = $contactID;
            //fixed for check boxes
            
            $specialFields = array( 'educational_interest','college_type','college_interest','test_tutoring' );
            foreach( $specialFields as $field ) {
                if ( $params[$field] ) {
                    $params[$field] = implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,array_keys($params[$field]));
                }
            }
            
            CRM_Quest_BAO_Student::create( $params, $ids);
            CRM_Quest_BAO_Student::createStudentSummary($params, $ssids);
        }

        //to update student record
        if ( CRM_Core_Permission::access( 'TMF' ) && $TMFFieldPresent ) {
            $ids = array();
            $dao = & new CRM_TMF_DAO_Student();
            $dao->contact_id = $contactID;
            if ($dao->find(true)) {
                $ids['id'] = $dao->id;
            }
            
            $params['contact_id'] = $contactID;
            CRM_TMF_BAO_Student::create( $params, $ids);
        }
        
        if ( $editHook ) {
            CRM_Utils_Hook::post( 'edit'  , 'Profile', $contactID  , $params );
        } else {
            CRM_Utils_Hook::post( 'create', 'Profile', $contactID, $params ); 
        }
        return $contactID;
    }

    /**
     * Function to check if the contact exits in the db
     * 
     * @params $contactId contact id
     * @return $contact  CRM_Contact_DAO_Contact object if the contact id exists else null
     * @static
     */
    static function check_contact_exists($contactId)
    {
       require_once "CRM/Contact/DAO/Contact.php";
       $contact =& new CRM_Contact_DAO_Contact();
       $contact->id = $contactId;
       if ($contact->find( true )) {
          return $contact;
       } 
       return null;
    }
    
    /**
    * Function to find and get the contact details
    * 
    * @param string $uniqId  the unique id of the contact (OpenID)
    * @param string $ctype   contact type
    * 
    * @return object $dao    contact details
    * @static
    */
    static function &matchContactOnUniqId( $uniqId, $ctype = null )
    {
        $query = "
    SELECT    civicrm_contact.id as contact_id,
              civicrm_contact.domain_id as domain_id,
              civicrm_contact.hash as hash,
              civicrm_contact.contact_type as contact_type,
              civicrm_contact.contact_sub_type as contact_sub_type
    FROM      civicrm_contact
    WHERE     civicrm_contact.user_unique_id = %1 AND civicrm_contact.domain_id = %2";
        $p = array( 1 => array( $uniqId, 'String' ),
                    2 => array( CRM_Core_Config::domainID( ), 'Integer' ) );

        if ( $ctype ) {
           $query .= " AND civicrm_contact.contact_type = %3";
           $p[3]   = array( $ctype, 'String' );
        }
        
        $dao =& CRM_Core_DAO::executeQuery( $query, $p );

        if ( $dao->fetch() ) {
            return $dao;
        }
        return CRM_Core_DAO::$_nullObject;
    }
    
    /**
     * Function to find the get contact details
     *
     * @param string $mail  primary email address of the contact
     * @param string $ctype contact type
     *
     * @return object $dao contact details
     * @static
     */
    static function &matchContactOnEmail( $mail, $ctype = null ) 
    {
       $query = "
SELECT    civicrm_contact.id as contact_id,
          civicrm_contact.domain_id as domain_id,
          civicrm_contact.hash as hash,
          civicrm_contact.contact_type as contact_type,
          civicrm_contact.contact_sub_type as contact_sub_type
FROM      civicrm_contact
LEFT JOIN civicrm_email    ON ( civicrm_contact.id = civicrm_email.contact_id )
    WHERE civicrm_email.is_primary = 1
      AND civicrm_email.email = %1
      AND civicrm_contact.domain_id = %2";
       $p = array( 1 => array( $mail, 'String' ),
                   2 => array( CRM_Core_Config::domainID( ), 'Integer' ) );

       if ( $ctype ) {
           $query .= " AND civicrm_contact.contact_type = %3";
           $p[3]   = array( $ctype, 'String' );
       }
       
       $dao =& CRM_Core_DAO::executeQuery( $query, $p );

       if ( $dao->fetch() ) {
          return $dao;
       }
       return CRM_Core_DAO::$_nullObject;
    }

    /**
     * function to get the sort name of a contact
     *
     * @param  int    $id id of the contact
     *
     * @return null|string     sort name of the contact if found
     * @static
     * @access public
     */
    static function sortName( $id ) 
    {
        if ( $id ) {
            return CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $id, 'sort_name' );
        }
    }

    /**
     * function check for mix contact ids(individual+household etc...)
     *
     * @param array $contactIds array of contact ids
     *
     * @return boolen true or false true if mix contact array else fale
     *
     * @access public
     * @static
     */
    public static function checkContactType(&$contactIds)
    {
        foreach ($contactIds as $id) {
            $contactType = self::getContactType($id);
            if( ! isset($contacts[$contactType])) {
                $contacts[$contactType] = 0;
            }
            $contacts[$contactType] += 1;
        }
        
        if (count($contacts) > 1) {
            return true;
        }
        return false;
    }

    /**
     * Funtion to get primary email of the contact
     *
     * @param int $contactID contact id
     *
     * @return string $dao->email  email address if present else null
     * @static
     * @access public
     */
    public static function getPrimaryEmail( $contactID ) 
    {
        // fetch the primary email
        $query = "
   SELECT civicrm_email.email as email
     FROM civicrm_contact
LEFT JOIN civicrm_email    ON ( civicrm_contact.id = civicrm_email.contact_id )
    WHERE civicrm_email.is_primary = 1
      AND civicrm_contact.id = %1";
        $p = array( 1 => array( $contactID, 'Integer' ) );
        $dao =& CRM_Core_DAO::executeQuery( $query, $p );

        $email = null;
        if ( $dao->fetch( ) ) {
            $email = $dao->email;
        }
        $dao->free( );
        return $email;
    }
    
    /**
     * Funtion to get primary OpenID of the contact
     *
     * @param int $contactID contact id
     *
     * @return string $dao->openid   OpenID if present else null
     * @static
     * @access public
     */
    public static function getPrimaryOpenId( $contactID ) 
    {
        // fetch the primary OpenID
        $query = "
SELECT    civicrm_openid.openid as openid
FROM      civicrm_contact
LEFT JOIN civicrm_openid ON ( civicrm_contact.id = civicrm_openid.contact_id )
WHERE     civicrm_contact.id = %1
AND       civicrm_openid.is_primary = 1";
        $p = array( 1 => array( $contactID, 'Integer' ) );
        $dao =& CRM_Core_DAO::executeQuery( $query, $p );

        $openId = null;
        if ( $dao->fetch( ) ) {
            $openId = $dao->openid;
        }
        $dao->free( );
        return $openId;
    }
    
    /**
     * Function to get the uf id of civicrm contact 
     *
     * @param string $hash  hash value
     * @param string $email email address of the contact

     * @return int uf id  uf id of civicrm contact
     * @static
     */
    static function getUFIdByHash( $hash, $uniqId ) 
    {
        require_once 'CRM/Contact/BAO/Contact.php';

        $uniqId = trim( $uniqId );

        $dao =& CRM_Contact_BAO_Contact::matchContactOnUniqId( $uniqId );
        if ( ! $dao ) {
            return false;
        }

        if ( $dao->hash != $hash ) {
            return false;
        }

        require_once 'CRM/Core/BAO/UFMatch.php';
        return CRM_Core_BAO_UFMatch::getUFId( $dao->contact_id );
    }

    /**
     * Generate a checksum for a contactID
     *
     * @param int    $contactID
     * @param int    $ts         timestamp that checksum was generated
     * @param int    $live       life of this checksum in hours
     *
     * @return array ( $cs, $ts, $live )
     * @static
     * @access public
     */
    static function generateChecksum( $contactID, $ts = null, $live = null ) 
    {
        $hash = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                             $contactID, 'hash' );
        if ( ! $hash ) {
            $hash = md5( uniqid( rand( ), true ) );
            CRM_Core_DAO::setFieldValue( 'CRM_Contact_DAO_Contact',
                                         $contactID,
                                         'hash', $hash );
        }

        if ( ! $ts ) {
            $ts = time( );
        }
        
        if ( ! $live ) {
            $live = 24 * 7;
        }

        $cs = md5( "{$hash}_{$contactID}_{$ts}_{$live}" );
        return "{$cs}_{$ts}_{$live}";
        
    }

    /**
     * Make sure the checksum is valid for the passed in contactID
     *
     * @param int    $contactID
     * @param string $cs         checksum to match against
     * @param int    $ts         timestamp that checksum was generated
     * @param int    $live       life of this checksum in hours
     *
     * @return boolean           true if valid, else false
     * @static
     * @access public
     */
    static function validChecksum( $contactID, $inputCheck ) 
    {
        $input =  explode( '_', $inputCheck );
        
        $inputCS = CRM_Utils_Array::value( 0,$input);
        $inputTS = CRM_Utils_Array::value( 1,$input);
        $inputLF = CRM_Utils_Array::value( 2,$input); 

        $check = self::generateChecksum( $contactID, $inputTS, $inputLF );

        if ( $check != $inputCheck ) {
            return false;
        }

        // checksum matches so now check timestamp
        $now = time( );
        return ( $inputTS + ( $inputLF * 60 * 60 ) >= $now ) ? true : false;
    }


    /**
     * Build the Current employer relationship with the individual
     *
     * @param int    $contactID             contact id of the individual
     * @param string $organizationName      current employer name
     * 
     * @access public
     * @static
     */
    static function makeCurrentEmployerRelationship( $contactID, $organizationName ) 
    {
        require_once "CRM/Contact/DAO/Contact.php";
        $org =& new CRM_Contact_DAO_Contact( );
        $org->organization_name = $organizationName;
        $org->find();
        while ( $org->fetch( ) ) {
            $dupeIds[] = $org->id;
        }

        //get the relationship id
        require_once "CRM/Contact/DAO/RelationshipType.php";
        $relType =& new CRM_Contact_DAO_RelationshipType();
        $relType->name_a_b = "Employee of";
        $relType->find(true);
        $relTypeId = $relType->id;
                
        $relationshipParams['relationship_type_id'] = $relTypeId.'_a_b';
        $relationshipParams['is_active'           ] = 1;
        $cid = array('contact' => $contactID );
        
        if ( empty($dupeIds) ) {
            //create new organization
            $newOrg['contact_type'     ] = 'Organization';
            $newOrg['organization_name'] = $organizationName ;
            
            require_once 'CRM/Core/BAO/Preferences.php';
            $orgName = self::add( $newOrg );

            //create relationship
            $relationshipParams['contact_check'][$orgName->id] = 1;
           
            $relationship= CRM_Contact_BAO_Relationship::create($relationshipParams, $cid);
        } else {
            //if more than one matching organizations found, we
            //add relationships to all those organizations
            foreach($dupeIds as $key => $value) {
                $relationshipParams['contact_check'][$value] = 1;
                $relationship= CRM_Contact_BAO_Relationship::create($relationshipParams,
                                                                    $cid);
            }
        }
        
    }

    /**
     * Function to get the count of  contact loctions
     * 
     * @param int $contactId contact id
     *
     * @return int $locationCount max locations for the contact
     * @static
     * @access public
     */
    static function getContactLocations( $contactId )
    {
        // find the system config related location blocks
        require_once 'CRM/Core/BAO/Preferences.php';
        $locationCount = CRM_Core_BAO_Preferences::value( 'location_count' );
        
        $contactLocations = array( );

        // find number of location blocks for this contact and adjust value accordinly
        // get location type from email
        $emailQuery = "
SELECT location_type_id
FROM civicrm_email
WHERE contact_id = {$contactId}
";
        $dao = CRM_Core_DAO::executeQuery( $emailQuery, CRM_Core_DAO::$_nullArray );
        while ( $dao->fetch() ) {
            $contactLocations[ $dao->location_type_id ] = $dao->location_type_id;
        }

        // get location type from phone
        $phoneQuery = "
SELECT location_type_id
FROM civicrm_phone
WHERE contact_id = {$contactId}
";
        $dao = CRM_Core_DAO::executeQuery( $phoneQuery, CRM_Core_DAO::$_nullArray );
        while ( $dao->fetch() ) {
            $contactLocations[ $dao->location_type_id ] = $dao->location_type_id;
        }

        // get location type from im
        $imQuery = "
SELECT location_type_id
FROM civicrm_im
WHERE contact_id = {$contactId}
";
        $dao = CRM_Core_DAO::executeQuery( $imQuery, CRM_Core_DAO::$_nullArray );
        while ( $dao->fetch() ) {
            $contactLocations[ $dao->location_type_id ] = $dao->location_type_id;
        }

        // get location type from address
        $addressQuery = "
SELECT location_type_id
FROM civicrm_address
WHERE contact_id = {$contactId}
";
        $dao = CRM_Core_DAO::executeQuery( $addressQuery, CRM_Core_DAO::$_nullArray );
        while ( $dao->fetch() ) {
            $contactLocations[ $dao->location_type_id ] = $dao->location_type_id;
        }

        $locCount = count($contactLocations);
        if ( $locCount &&  $locationCount < $locCount ) {
            $locationCount = $locCount;
        }

        return $locationCount;
    }

    /**
     * Function to check if the external identifier exits in the db
     * 
     * @params mix     $externalIdentifier external identifier
     * @return booleab true if external id exist else false
     * @static
     */
    static function checkExternalIdentifierExists( $externalIdentifier )
    {
        require_once "CRM/Contact/DAO/Contact.php";
        $contact =& new CRM_Contact_DAO_Contact();
        $contact->external_identifier = $externalIdentifier;
        if ($contact->find( true )) {
            return true;
        } 
        return false;
    } 

    static function getIdByDisplayName( $displayName )
    {
        return CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                            $displayName,
                                            'id',
                                            'sort_name' );
    }

    /**
     * Function to calculate Age in Years if greater than one year else in months
     * 
     * @param date $birthDate Birth Date
     *
     * @return int array $results contains years or months
     * @access public
     */
    public function findAge($birthDate) 
    {     
        $results = array( );
        $formatedBirthDate  = CRM_Utils_Date::customFormat($birthDate,'%Y-%m-%d'); 
        
        $bDate      = explode('-',$formatedBirthDate);
        $birthYear  = $bDate[0]; 
        $birthMonth = $bDate[1]; 
        $birthDay   = $bDate[2]; 
        $year_diff  = date("Y") - $birthYear; 
        
        switch ($year_diff) {
        case 1: 
            $month = (12 - $birthMonth) + date("m");
            if ( $month < 12 ) { 
                $results['months'] =  $month;
            } elseif ( $month == 12 && (date("d") < $birthDay) ) { 
                $results['months'] = $month-1;
            } else { 
                $results['years'] =  $year_diff;
            }
            break;
        case 0:
            $month = date("m") - $birthMonth;
            $results['months'] = $month;
            break;
        default:
            $results['years'] = $year_diff;
        }
        
        return $results;
    }
    
}

?>
