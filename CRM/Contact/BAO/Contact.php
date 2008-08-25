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
     * takes an associative array and creates a contact object
     *
     * the function extract all the params it needs to initialize the create a
     * contact object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params (reference ) an assoc array of name/value pairs
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

        $allNull = $contact->copyValues($params);

        $contact->id        = CRM_Utils_Array::value( 'contact_id', $params );
        
        if ( $contact->contact_type == 'Individual') {
            $allNull = false;

            //format individual fields
            require_once "CRM/Contact/BAO/Individual.php";
            CRM_Contact_BAO_Individual::format( $params, $contact );
        } else if ($contact->contact_type == 'Household') {
            if ( isset( $params['household_name'] ) ) {
                $allNull = false;
                $contact->display_name = $contact->sort_name = CRM_Utils_Array::value('household_name', $params, '');
            }
        } else if ( $contact->contact_type == 'Organization' ) {
            if ( isset( $params['organization_name'] ) ) {
                $allNull = false;
                $contact->display_name = $contact->sort_name = CRM_Utils_Array::value('organization_name', $params, '') ;
            }
        }

        // privacy block
        $privacy = CRM_Utils_Array::value('privacy', $params);
        if ( $privacy &&
             is_array( $privacy ) &&
             ! empty( $privacy ) ) {
            $allNull = false;
            foreach (self::$_commPrefs as $name) {
                $contact->$name = CRM_Utils_Array::value($name, $privacy, false);
            }
        }

        // since hash was required, make sure we have a 0 value for it, CRM-1063
        // fixed in 1.5 by making hash optional
        // only do this in create mode, not update
        if ( ( ! array_key_exists( 'hash', $contact ) || ! $contact->hash ) && ! $contact->id ) {
            $allNull = false;
            $contact->hash = md5( uniqid( rand( ), true ) );
        }

        if ( ! $allNull ) {
            $contact->save( );

            require_once 'CRM/Core/BAO/Log.php';
            CRM_Core_BAO_Log::register( $contact->id,
                                        'civicrm_contact',
                                        $contact->id );
        }

        if ( $contact->contact_type == 'Individual' &&
             array_key_exists( 'current_employer', $params ) ) {
            // create current employer
            if ( $params['current_employer'] ) {
                require_once 'CRM/Contact/BAO/Contact/Utils.php';
                CRM_Contact_BAO_Contact_Utils::createCurrentEmployerRelationship( $contact->id, 
                                                                                  $params['current_employer'] );
            } else {
                //unset if employer id exits
                if ( CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $contact->id, 'employer_id' ) ) {
                    require_once 'CRM/Contact/BAO/Contact/Utils.php';
                    CRM_Contact_BAO_Contact_Utils::clearCurrentEmployer( $contact->id );
                }
            }
        }

        //update cached employee name
        if ( $contact->contact_type == 'Organization' ) {
            require_once 'CRM/Contact/BAO/Contact/Utils.php';
            CRM_Contact_BAO_Contact_Utils::updateCurrentEmployer( $contact->id );
        }
        return $contact;
    }
    
    /**
     * Function to create contact
     * takes an associative array and creates a contact object and all the associated
     * derived objects (i.e. individual, location, email, phone etc)
     *
     * This function is invoked from within the web form layer and also from the api layer
     *
     * @param array   $params      (reference ) an assoc array of name/value pairs
     * @param boolean $fixAddress  if we need to fix address
     * @param boolean $invokeHooks if we need to invoke hooks
     *
     * @return object CRM_Contact_BAO_Contact object 
     * @access public
     * @static
     */
    static function &create(&$params, $fixAddress = true, $invokeHooks = true ) 
    {
        if ( ! $params['contact_type'] && ! CRM_Utils_Array::value( 'contact_id', $params ) ) {
            return;
        }

        $isEdit = true;
        if ( $invokeHooks ) {
            require_once 'CRM/Utils/Hook.php';
            if ( CRM_Utils_Array::value( 'contact_id', $params ) ) {
                CRM_Utils_Hook::pre( 'edit', $params['contact_type'], $params['contact_id'], $params );
            } else {
                CRM_Utils_Hook::pre( 'create', $params['contact_type'], null, $params ); 
                $isEdit = false;
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
        //get userID from session
        $session =& CRM_Core_Session::singleton( );
        $userID  = $session->get( 'userID' );
        // add notes
        if ( CRM_Utils_Array::value( 'note', $params ) ) {
            if (is_array($params['note'])) {
                foreach ($params['note'] as $note) {  
                    $contactId = $contact->id;
                    if ( isset( $note['contact_id'] ) ) {
                        $contactId = $note['contact_id'];
                    }
                    //if logged in user, overwrite contactId
                    if ( $userID ) {
                        $contactId = $userID;
                    }
                    
                    $noteParams = array(
                                        'entity_id'     => $contact->id,
                                        'entity_table'  => 'civicrm_contact',
                                        'note'          => $note['note'],
                                        'subject'       => $note['subject'],
                                        'contact_id'    => $contactId
                                        );
                    CRM_Core_BAO_Note::add($noteParams, CRM_Core_DAO::$_nullArray);
                }
            } else {
                $contactId = $contact->id;
                if ( isset( $note['contact_id'] ) ) {
                    $contactId = $note['contact_id'];
                }
                //if logged in user, overwrite contactId
                if ( $userID ) {
                    $contactId = $userID;
                }
                
                $noteParams = array(
                                    'entity_id'     => $contact->id,
                                    'entity_table'  => 'civicrm_contact',
                                    'note'          => $params['note'],
                                    'subject'       => $params['subject'],
                                    'contact_id'    => $contactId
                                    );
                CRM_Core_BAO_Note::add($noteParams, CRM_Core_DAO::$_nullArray);
            }
        }

        // update the UF user_unique_id if that has changed
        require_once 'CRM/Core/BAO/UFMatch.php';
        CRM_Core_BAO_UFMatch::updateUFName( $contact->id );

        if ( CRM_Utils_Array::value( 'custom', $params ) &&
             is_array( $params['custom'] ) ) {
            require_once 'CRM/Core/BAO/CustomValueTable.php';
            CRM_Core_BAO_CustomValueTable::store( $params['custom'], 'civicrm_contact', $contact->id );
        }

        // make a civicrm_subscription_history entry only on contact create (CRM-777)
        if ( ! CRM_Utils_Array::value( 'contact_id', $params ) ) {
            $subscriptionParams = array('contact_id' => $contact->id,
                                        'status' => 'Added',
                                        'method' => 'Admin');
            CRM_Contact_BAO_SubscriptionHistory::create($subscriptionParams);
        }

        $transaction->commit( );
        
        $contact->contact_type_display = CRM_Contact_DAO_Contact::tsEnum('contact_type', $contact->contact_type);

        // reset the group contact cache for this group
        require_once 'CRM/Contact/BAO/GroupContactCache.php';
        CRM_Contact_BAO_GroupContactCache::remove( );

        if ( $invokeHooks ) {
            if ( $isEdit ) {
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
            require_once 'CRM/Contact/BAO/Contact/Utils.php';
            $image = CRM_Contact_BAO_Contact_Utils::getImage( $dao->contact_type );

            // use email if display_name is empty
            if ( empty( $dao->display_name ) ) {
                $dao->display_name = $dao->email;
            }
            return array( $dao->display_name, $image );
        }
        return null;
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

        CRM_Utils_Array::lookupValue( $defaults, 'prefix', CRM_Core_PseudoConstant::individualPrefix(), $reverse );
        CRM_Utils_Array::lookupValue( $defaults, 'suffix', CRM_Core_PseudoConstant::individualSuffix(), $reverse );
        CRM_Utils_Array::lookupValue( $defaults, 'gender', CRM_Core_PseudoConstant::gender(), $reverse );

        if ( array_key_exists( 'location', $defaults ) ) {
            $locations =& $defaults['location'];

            foreach ($locations as $index => $location) {                
                $location =& $locations[$index];
                CRM_Utils_Array::lookupValue( $location, 'location_type', CRM_Core_PseudoConstant::locationType(), $reverse );

                // FIXME: lookupValue doesn't work for vcard_name
                $vcardNames =& CRM_Core_PseudoConstant::locationVcardName();
                if ( isset( $location['location_type_id'] ) ) {
                    $location['vcard_name'] = $vcardNames[$location['location_type_id']];
                }

                if (array_key_exists( 'address', $location ) ) {
                    if ( ! CRM_Utils_Array::lookupValue( $location['address'], 'state_province',
                                                         CRM_Core_PseudoConstant::stateProvince(), $reverse ) &&
                         $reverse ) {
                         CRM_Utils_Array::lookupValue( $location['address'], 'state_province', 
                                           CRM_Core_PseudoConstant::stateProvinceAbbreviation(), $reverse );
                    }
                    
                    if ( ! CRM_Utils_Array::lookupValue( $location['address'], 'country',
                                                         CRM_Core_PseudoConstant::country(), $reverse ) &&
                         $reverse ) {
                         CRM_Utils_Array::lookupValue( $location['address'], 'country', 
                                                       CRM_Core_PseudoConstant::countryIsoCode(), $reverse );
                    }
                    CRM_Utils_Array::lookupValue( $location['address'], 'county', 
                                                  CRM_Core_PseudoConstant::county(), $reverse );
                }

                if (array_key_exists('im', $location)) {
                    $ims =& $location['im'];
                    foreach ($ims as $innerIndex => $im) {
                        $im =& $ims[$innerIndex];
                        CRM_Utils_Array::lookupValue( $im, 'provider', 
                                                      CRM_Core_PseudoConstant::IMProvider(), $reverse );
                        unset($im);
                    }
                }
                unset($location);
            }
        }
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

        $contact = self::_getValues( $params, $defaults );
        unset($params['id']);
        
        //get the block information for this contact
        $entityBlock = array( 'contact_id' => $params['contact_id'] );
        $contact->location  =& CRM_Core_BAO_Location::getValues( $entityBlock, 
                                                                 $defaults, 
                                                                 $microformat );
        
        $contact->notes        =& CRM_Core_BAO_Note::getValues( $params, $defaults );
        $contact->relationship =& CRM_Contact_BAO_Relationship::getValues( $params, $defaults );
        $contact->groupContact =& CRM_Contact_BAO_GroupContact::getValues( $params, $defaults );
        
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
    static function displayName( $id ) 
    {
        return CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $id, 'display_name' );
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
        require_once 'CRM/Contact/BAO/Contact/Permission.php';
        if ( ! CRM_Contact_BAO_Contact_Permission::allow( $id, CRM_Core_Permission::EDIT ) ) {
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

        // delete the log entries since we dont have triggers enabled as yet
        require_once 'CRM/Core/DAO/Log.php';
        $logDAO =& new CRM_Core_DAO_Log(); 
        $logDAO->entity_table = 'civicrm_contact';
        $logDAO->entity_id    = $id;
        $logDAO->delete();
        
        $contact->delete( );

        //delete the contact id from recently view
        CRM_Utils_Recent::del($id);

        // reset the group contact cache for this group
        require_once 'CRM/Contact/BAO/GroupContactCache.php';
        CRM_Contact_BAO_GroupContactCache::remove( );

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
     * @param int $id - id of the contact whose contact type is needed
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

            // check if we can retrieve from database cache
            require_once 'CRM/Core/BAO/Cache.php'; 
            $fields =& CRM_Core_BAO_Cache::getItem( 'contact fields', "importableFields $contactType" );
                                         
            if ( ! $fields ) {
                $fields = array( );

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
                    $fields       = array_merge($fields, CRM_Core_BAO_CustomField::getFieldsForImport($contactType, $showAll) );
                    //unset the fields, which are not related to their
                    //contact type.
                    $commonValues = array ( 'Individual'   => array( 'household_name','legal_name','sic_code','organization_name' ),
                                            'Household'    => array( 'first_name','middle_name','last_name','greeting_type',
                                                                     'job_title','gender_id','birth_date','organization_name',
                                                                     'legal_name', 'legal_identifier', 'sic_code','home_URL',
                                                                     'is_deceased','deceased_date' ),
                                            'Organization' => array( 'first_name','middle_name','last_name','greeting_type',
                                                                     'job_title','gender_id','birth_date','household_name',
                                                                     'is_deceased','deceased_date' ) 
                                            );
                    foreach ( $commonValues[$contactType] as $value ) {
                        unset( $fields[$value] );
                    }
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
            
                CRM_Core_BAO_Cache::setItem( $fields, 'contact fields', "importableFields $contactType" );
            }
            
            self::$_importableFields[$contactType] = $fields;
        }

        if ( ! $status ) {
            $fields =
                array_merge( array( 'do_not_import' => array( 'title' => ts('- do not import -') ) ),
                             self::$_importableFields[$contactType] );
        } else {
            $fields =
                array_merge( array( '' => array( 'title' => ts('- Contact Fields -') ) ),
                             self::$_importableFields[$contactType] );
        }
        
        return $fields;
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

            // check if we can retrieve from database cache
            require_once 'CRM/Core/BAO/Cache.php'; 
            $fields =& CRM_Core_BAO_Cache::getItem( 'contact fields', "exportableFields $contactType $export" );

            if ( ! $fields ) {
                $fields = array( );
                $fields = array_merge($fields, CRM_Contact_DAO_Contact::export( ));
            
                // the fields are only meant for Individual contact type
                if ( $contactType == 'Individual') {
                    require_once 'CRM/Core/OptionValue.php';
                    $fields = array_merge( $fields, CRM_Core_OptionValue::getFields( ) );
                }
                
                // add current employer for individuals
                $fields = array_merge( $fields, array( 'current_employer' =>
                                                       array ( 'name'  => 'organization_name',
                                                               'title' => ts('Current Employer') )
                                                       ));
                
                $locationType = array( );
                if ($status) {
                    $locationType['location_type'] = array ('name' => 'location_type',
                                                            'where' => 'civicrm_location_type.name',
                                                            'title' => ts('Location Type'));
                }
            
                $IMProvider = array( );
                if ( $status ) {
                    $IMProvider['im_provider'] = array ('name' => 'im_provider',
                                                        'where' => 'im_provider.name',
                                                        'title' => ts('IM Provider'));
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

                $fields = array_merge( $sortArray, $fields );
            
                //unset the field which are not related to their contact type.
                if ( $contactType != 'All') { 
                    $commonValues = array ( 'Individual'   => array( 'household_name','legal_name','sic_code','organization_name' ),
                                            'Household'    => array( 'first_name','middle_name','last_name','greeting_type',
                                                                     'job_title','gender_id','birth_date','organization_name',
                                                                     'legal_name', 'legal_identifier', 'sic_code','home_URL',
                                                                     'is_deceased','deceased_date', 'current_employer' ),
                                            'Organization' => array( 'first_name','middle_name','last_name','greeting_type',
                                                                     'job_title','gender_id','birth_date','household_name',
                                                                     'is_deceased','deceased_date', 'current_employer' ) 
                                            );
                    foreach ( $commonValues[$contactType] as $value ) {
                        unset( $fields[$value] );
                    }
                }

                CRM_Core_BAO_Cache::setItem( $fields, 'contact fields', "exportableFields $contactType $export" );
            }
            self::$_exportableFields[$contactType] = $fields;
        }

        if ( ! $status ) {
            $fields = self::$_exportableFields[$contactType];
        } else {
            $fields = array_merge( array( '' => array( 'title' => ts('- Contact Fields -') ) ),
                                   self::$_exportableFields[$contactType] );
        }

        return $fields;
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
        $returnProperties['first_name'] = $returnProperties['organization_name'] = $returnProperties['household_name'] = $returnProperties['contact_type'] = 1;
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
    static function getPrimaryLocationType( $contactId ) 
    {
        $query = "
SELECT
 IF ( civicrm_email.location_type_id IS NULL,
    IF ( civicrm_address.location_type_id IS NULL, 
        IF ( civicrm_phone.location_type_id IS NULL,
           IF ( civicrm_im.location_type_id IS NULL, 
               IF ( civicrm_openid.location_type_id IS NULL, null, civicrm_openid.location_type_id)
           ,civicrm_im.location_type_id)
        ,civicrm_phone.location_type_id)
     ,civicrm_address.location_type_id)
  ,civicrm_email.location_type_id)  as locationType
FROM civicrm_contact
     LEFT JOIN civicrm_email   ON ( civicrm_email.is_primary   = 1 AND civicrm_email.contact_id = civicrm_contact.id )
     LEFT JOIN civicrm_address ON ( civicrm_address.is_primary = 1 AND civicrm_address.contact_id = civicrm_contact.id)
     LEFT JOIN civicrm_phone   ON ( civicrm_phone.is_primary   = 1 AND civicrm_phone.contact_id = civicrm_contact.id)
     LEFT JOIN civicrm_im      ON ( civicrm_im.is_primary      = 1 AND civicrm_im.contact_id = civicrm_contact.id)
     LEFT JOIN civicrm_openid  ON ( civicrm_openid.is_primary  = 1 AND civicrm_openid.contact_id = civicrm_contact.id)
WHERE  civicrm_contact.id = %1 ";

        $params = array( 1 => array( $contactId, 'Integer' ) );

        $dao =& CRM_Core_DAO::executeQuery( $query, $params );

        $locationType = null;
        if ( $dao->fetch() ) {
            $locationType = $dao->locationType;
        }
        
        if ( $locationType ) {
            return $locationType;
        } else {
            // if there is no primart contact location, then return default
            // location type of the system
            require_once 'CRM/Core/BAO/LocationType.php';
            $defaultLocationType =& CRM_Core_BAO_LocationType::getDefault();
            return $defaultLocationType->id;
        }
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
        $contactType =  self::getContactType( $id );

        // if individual
       if( $contactType == 'Individual') {
           $sql = "
   SELECT civicrm_contact.first_name, civicrm_contact.last_name,  civicrm_email.email, civicrm_contact.do_not_email, civicrm_email.on_hold
     FROM civicrm_contact, civicrm_email 
   WHERE  civicrm_contact.id = civicrm_email.contact_id AND civicrm_email.is_primary = 1
          AND civicrm_contact.id = %1";
           $params = array( 1 => array( $id, 'Integer' ) );
       } else { // for household / organization
           $sql = "
   SELECT civicrm_contact.display_name, civicrm_email.email, civicrm_contact.do_not_email, civicrm_email.on_hold
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
                   $onHold     = $row[4] ? true : false;
               } else {
                   $name       = $row[0];
                   $email      = $row[1];
                   $doNotEmail = $row[2] ? true : false;
                   $onHold     = $row[3] ? true : false;
               }
               return array( $name, $email, $doNotEmail, $onHold);
           }
       }
       return array( null, null, null, null );
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
                                          $ctype = null,
                                          $visibility = false ) 
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

        // get the contact details (hier)
        if ( $contactID ) {
            list($details, $options) = self::getHierContactDetails( $contactID, $fields );
            $contactDetails = $details[$contactID];
            $data['contact_type'] = CRM_Utils_Array::value( 'contact_type', $contactDetails );
        } else {
            //we should get contact type only if contact
            if ( $ufGroupId ) {
                require_once "CRM/Core/BAO/UFField.php";
                $data['contact_type'] = CRM_Core_BAO_UFField::getProfileType( $ufGroupId );
                
                //special case to handle profile with only contact fields
                if ( $data['contact_type'] == 'Contact' ) {
                    $data['contact_type'] = 'Individual';
                }
            } else if ( $ctype ) {
                $data['contact_type'] = $ctype;
            } else {
                $data['contact_type'] = 'Individual';
            }
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
            $primaryLocationType = self::getPrimaryLocationType($contactID);
        } else {
            require_once "CRM/Core/BAO/LocationType.php";
            $defaultLocation =& CRM_Core_BAO_LocationType::getDefault();
            $defaultLocationId = $defaultLocation->id;
        }
        
        $phoneLoc   = 0;
        $phoneReset = array( );
        $imLoc      = 0; 
        $imReset    = array( );

        foreach ($params as $key => $value) {
            $fieldName = $locTypeId = $typeId = null;
            list($fieldName, $locTypeId, $typeId) = CRM_Utils_System::explode('-', $key, 3);

            //store original location type id
            $actualLocTypeId = $locTypeId;

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
                    $imLoc    = 0;
                }
                                
                $data['location'][$loc]['location_type_id'] = $locTypeId;
                
                if ( $contactID ) {
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
                
                if ($fieldName == 'phone') {
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
                    if ( !in_array($loc, $imReset) ) {
                        $imReset[] = $loc;
                        $imLoc = 1;
                    } else {
                        $imLoc++;
                    }

                    if ( $typeId ) {
                        $data['location'][$loc]['im'][$imLoc]['provider_id'] = $value;
                        $data['location'][$loc]['im'][$imLoc]['name'       ] = $params["{$fieldName}-{$actualLocTypeId}"];
                        unset( $params["{$fieldName}-{$actualLocTypeId}"] );
                    } else {
                        $data['location'][$loc]['im'][$imLoc]['name'       ] = $value;
                        $data['location'][$loc]['im'][$imLoc]['provider_id'] = $params["{$fieldName}-{$actualLocTypeId}-provider_id"];
                        unset( $params["{$fieldName}-{$actualLocTypeId}-provider_id"] );
                    }
                    
                    if ( $imLoc == 1 ) {
                        $data['location'][$loc]['im'][$imLoc]['is_primary']  = 1;
                    }
                    
                } else if ($fieldName == 'openid') {
                    $data['location'][$loc]['openid'][1]['openid']     = $value;
                    $data['location'][$loc]['openid'][1]['is_primary'] = 1;
                } else {
                    if ($fieldName === 'state_province') {
                        // CRM-3393
                        if ( is_numeric( $value ) &&
                             ( (int ) $value ) >= 1000 ) {
                            $data['location'][$loc]['address']['state_province_id'] = $value;
                        } else {
                            $data['location'][$loc]['address']['state_province'] = $value;
                        }
                    } else if ($fieldName === 'country') {
                        // CRM-3393
                        if ( is_numeric( $value ) &&
                             ( (int ) $value ) >= 1000 ) {
                            $data['location'][$loc]['address']['country_id'] = $value;
                        } else {
                          $data['location'][$loc]['address']['country'] = $value;
                        }
                    } else if ($fieldName === 'county') {
                        $data['location'][$loc]['address']['county_id'] = $value;
                    } else {
                        if ($fieldName == 'address_name') {
                            $data['location'][$loc]['address']['name'] = $value;
                        } else {
                            $data['location'][$loc]['address'][$fieldName] = $value;
                        }
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
                    if ( $key == 'location' ){ 
                        foreach ( $value as $locationTypeId => $field ) { 
                            foreach ( $field as $block => $val ) { 
                                if ( $block == 'address' && array_key_exists('address_name', $val ) ) {
                                    $value[$locationTypeId][$block]['name']= $value[$locationTypeId][$block]['address_name'];
                                }
                            }
                        }
                    }
                    $data[$key] = $value;
                  
                }
            }
        }
       
        //make sure primary location is at first position in location array
        if ( isset( $data['location'] ) && count( $data['location'] ) > 1 ) {
            // if first location is primary skip manipulation
            if ( !isset($data['location'][1]['is_primary']) ) {
                //find the key for primary location
                foreach ( $data['location'] as $primaryLocationKey => $value ) {
                    if ( isset( $value['is_primary'] ) ) {
                        break;
                    }
                }
                
                // swap first location with primary location
                $tempLocation        = $data['location'][1];
                $data['location'][1] = $data['location'][$primaryLocationKey];
                $data['location'][$primaryLocationKey] = $tempLocation;
            }
        }

        if ( ! isset( $data['contact_type'] ) ) {
            $data['contact_type'] = 'Individual';
        }

        //get the custom fields for the contact
        $customFields = CRM_Core_BAO_CustomField::getFields( $data['contact_type'] );

        $studentFieldPresent = 0;
        // fix all the custom field checkboxes which are empty
        foreach ($fields as $name => $field ) {
            if ( CRM_Core_Permission::access( 'Quest' ) ) {
                // check if student fields present
                require_once 'CRM/Quest/BAO/Student.php';
                if ( (!$studentFieldPresent) && array_key_exists($name, CRM_Quest_BAO_Student::exportableFields()) ) {
                   $studentFieldPresent = 1;
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
        if ( $data['contact_type'] != 'Student' ) {
            $contact =& self::create( $data );
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

        // Process group and tag  
        if ( CRM_Utils_Array::value('group', $fields ) ) {
            $method = 'Admin';
            // this for sure means we are coming in via profile since i added it to fix
            // removing contacts from user groups -- lobo
            if ( $visibility ) {
                $method = 'Web';
            }
            CRM_Contact_BAO_GroupContact::create( $params['group'], $contactID, $visibility, $method );
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

        // reset the group contact cache for this group
        require_once 'CRM/Contact/BAO/GroupContactCache.php';
        CRM_Contact_BAO_GroupContactCache::remove( );

        if ( $editHook ) {
            CRM_Utils_Hook::post( 'edit'  , 'Profile', $contactID  , $params );
        } else {
            CRM_Utils_Hook::post( 'create', 'Profile', $contactID, $params ); 
        }
        return $contactID;
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
        $mail = strtolower( trim( $mail ) );
        $query = "
SELECT     civicrm_contact.id as contact_id,
           civicrm_contact.hash as hash,
           civicrm_contact.contact_type as contact_type,
           civicrm_contact.contact_sub_type as contact_sub_type
FROM       civicrm_contact
INNER JOIN civicrm_email    ON ( civicrm_contact.id = civicrm_email.contact_id )
WHERE      civicrm_email.email = %1";
        $p = array( 1 => array( $mail, 'String' ) );

       if ( $ctype ) {
           $query .= " AND civicrm_contact.contact_type = %3";
           $p[3]   = array( $ctype, 'String' );
       }

       $query .= " ORDER BY civicrm_email.is_primary DESC";
       
       $dao =& CRM_Core_DAO::executeQuery( $query, $p );

       if ( $dao->fetch() ) {
          return $dao;
       }
       return CRM_Core_DAO::$_nullObject;
    }

    /**
     * Function to find the contact details associated with an OpenID
     *
     * @param string $openId openId of the contact
     * @param string $ctype  contact type
     *
     * @return object $dao contact details
     * @static
     */
    static function &matchContactOnOpenId( $openId, $ctype = null ) 
    {
        $openId = strtolower( trim( $openId ) );
        $query  = "
SELECT     civicrm_contact.id as contact_id,
           civicrm_contact.hash as hash,
           civicrm_contact.contact_type as contact_type,
           civicrm_contact.contact_sub_type as contact_sub_type
FROM       civicrm_contact
INNER JOIN civicrm_openid    ON ( civicrm_contact.id = civicrm_openid.contact_id )
WHERE      civicrm_openid.openid = %1";
        $p = array( 1 => array( $openId, 'String' ) );

       if ( $ctype ) {
           $query .= " AND civicrm_contact.contact_type = %3";
           $p[3]   = array( $ctype, 'String' );
       }

       $query .= " ORDER BY civicrm_openid.is_primary DESC";
       
       $dao =& CRM_Core_DAO::executeQuery( $query, $p );

       if ( $dao->fetch() ) {
          return $dao;
       }
       return CRM_Core_DAO::$_nullObject;
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
        $query = "
( SELECT location_type_id FROM civicrm_email   WHERE contact_id = {$contactId} )
UNION
( SELECT location_type_id FROM civicrm_phone   WHERE contact_id = {$contactId} )
UNION
( SELECT location_type_id FROM civicrm_im      WHERE contact_id = {$contactId} )
UNION
( SELECT location_type_id FROM civicrm_address WHERE contact_id = {$contactId} )
";
        $dao      = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        $locCount = $dao->N;
        if ( $locCount &&  $locationCount < $locCount ) {
            $locationCount = $locCount;
        }

        return $locationCount;
    }


    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params input parameters to find object
     * @param array $values output values of the object
     *
     * @return CRM_Contact_BAO_Contact|null the found object or null
     * @access public
     * @static
     */
    private static function &_getValues( &$params, &$values ) 
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
                    $age =  CRM_Utils_Date::calculateAge( $birthDate );
                    $values['age']['y'] = CRM_Utils_Array::value('years',$age);
                    $values['age']['m'] = CRM_Utils_Array::value('months',$age);
                 }
            }

            $contact->contact_id = $contact->id;
            
            return $contact;
        }
        return null;
    }

    
}


