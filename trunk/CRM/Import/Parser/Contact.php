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

require_once 'CRM/Import/Parser.php';

require_once 'api/v2/utils.php';

/**
 * class to parse contact csv files
 */
class CRM_Import_Parser_Contact extends CRM_Import_Parser 
{
    protected $_mapperKeys;
    protected $_mapperLocType;
    protected $_mapperPhoneType;
    protected $_mapperRelated;
    protected $_mapperRelatedContactType;
    protected $_mapperRelatedContactDetails;
    protected $_mapperRelatedContactEmailType;

    protected $_emailIndex;
    protected $_firstNameIndex;
    protected $_lastNameIndex;

    protected $_householdNameIndex;
    protected $_organizationNameIndex;

    protected $_allEmails;

    protected $_phoneIndex;
    protected $_updateWithId;
    protected $_retCode;

    protected $_externalIdentifierIndex;
    protected $_allExternalIdentifiers;

    /**
     * Array of succesfully imported contact id's
     *
     * @array
     */
    protected $_newContacts;

    /**
     * Array of succesfully imported related contact id's
     *
     * @array
     */
    protected $_newRelatedContacts;

    /**
     * class constructor
     */
    function __construct( &$mapperKeys, $mapperLocType = null, 
                          $mapperPhoneType = null, $mapperRelated = null, $mapperRelatedContactType=null,
                          $mapperRelatedContactDetails = null, $mapperRelatedContactLocType = null, 
                          $mapperRelatedContactPhoneType = null) 
    {
        parent::__construct();
        $this->_mapperKeys =& $mapperKeys;
        $this->_mapperLocType =& $mapperLocType;
        $this->_mapperPhoneType =& $mapperPhoneType;
        $this->_mapperRelated =& $mapperRelated;
        $this->_mapperRelatedContactType =& $mapperRelatedContactType;
        $this->_mapperRelatedContactDetails =& $mapperRelatedContactDetails;
        $this->_mapperRelatedContactLocType =& $mapperRelatedContactLocType;
        $this->_mapperRelatedContactPhoneType =& $mapperRelatedContactPhoneType;

    }

    /**
     * the initializer code, called before the processing
     *
     * @return void
     * @access public
     */
    function init( ) 
    {
        require_once 'CRM/Contact/BAO/Contact.php';
        $fields =& CRM_Contact_BAO_Contact::importableFields( $this->_contactType );

        //Relationship importables
        $relations = CRM_Contact_BAO_Relationship::getContactRelationshipType( null, null, null, $this->_contactType );
        asort($relations);

        foreach ($relations as $key => $var) {
            list( $type ) = explode( '_', $key );
            $relationshipType[$key]['title'] = $var;
            $relationshipType[$key]['headerPattern'] = '/' . preg_quote( $var, '/' ) . '/';
            $relationshipType[$key]['import'] = true;
            $relationshipType[$key]['relationship_type_id'] = $type;
            $relationshipType[$key]['related'] = true;
        }

        if ( !empty($relationshipType) ) {
            $fields = array_merge( $fields,
                                   array( 'related' => array( 'title' => '- related contact info -') ),
                                   $relationshipType );
        }

        foreach ($fields as $name => $field) {
            $this->addField( $name,
                             $field['title'],
                             CRM_Utils_Array::value( 'type'           , $field ),
                             CRM_Utils_Array::value( 'headerPattern'  , $field ),
                             CRM_Utils_Array::value( 'dataPattern'    , $field ),
                             CRM_Utils_Array::value( 'hasLocationType', $field ) );
        }

        $this->_newContacts = array( );

        $this->setActiveFields( $this->_mapperKeys );
        $this->setActiveFieldLocationTypes( $this->_mapperLocType );
        $this->setActiveFieldPhoneTypes( $this->_mapperPhoneType );

        //related info
        $this->setActiveFieldRelated( $this->_mapperRelated );
        $this->setActiveFieldRelatedContactType( $this->_mapperRelatedContactType );
        $this->setActiveFieldRelatedContactDetails( $this->_mapperRelatedContactDetails );
        $this->setActiveFieldRelatedContactLocType( $this->_mapperRelatedContactLocType );
        $this->setActiveFieldRelatedContactPhoneType( $this->_mapperRelatedContactPhoneType );
        
        $this->_phoneIndex = -1;
        $this->_emailIndex = -1;
        $this->_firstNameIndex = -1;
        $this->_lastNameIndex = -1;
        $this->_householdNameIndex = -1;
        $this->_organizationNameIndex = -1;
        $this->_externalIdentifierIndex = -1;
        
        $index = 0 ;
        foreach ( $this->_mapperKeys as $key ) {
            if ( substr( $key, 0, 5 ) == 'email' ) {
                $this->_emailIndex = $index;
                $this->_allEmails  = array( );
            }
            if ( substr( $key, 0, 5 ) == 'phone' ) {
                $this->_phoneIndex = $index;
            }
            if ( $key == 'first_name' ) {
                $this->_firstNameIndex = $index;
            }
            if ( $key == 'last_name' ) { 
                $this->_lastNameIndex = $index;
            }
            if ( $key == 'household_name' ) { 
                $this->_householdNameIndex = $index;
            }
            if ( $key == 'organization_name' ) { 
                $this->_organizationNameIndex = $index;
            }
            
            if ( $key == 'external_identifier' ) {
                $this->_externalIdentifierIndex = $index;
                $this->_allExternalIdentifiers  = array( );
            }
            $index++;
        }

        $this->_updateWithId = false;
        if ( in_array('id',$this->_mapperKeys) || $this->_externalIdentifierIndex > 0 ) {
            $this->_updateWithId = true;
        }
    }

    /**
     * handle the values in mapField mode
     *
     * @param array $values the array of values belonging to this line
     *
     * @return boolean
     * @access public
     */
    function mapField( &$values ) 
    {
        return CRM_Import_Parser::VALID;
    }


    /**
     * handle the values in preview mode
     *
     * @param array $values the array of values belonging to this line
     *
     * @return boolean      the result of this processing
     * @access public
     */
    function preview( &$values ) 
    {
        return $this->summary($values);
    }

    /**
     * handle the values in summary mode
     *
     * @param array $values the array of values belonging to this line
     *
     * @return boolean      the result of this processing
     * @access public
     */
    function summary( &$values ) 
    {
        $response = $this->setActiveFieldValues( $values );

        $errorRequired = false;
        switch ($this->_contactType) { 

            
        case 'Individual' :
            if ( ( $this->_firstNameIndex < 0 && $this->_lastNameIndex < 0 ) ) {
                $errorRequired = true;
            } else {
                $errorRequired = 
                    ! CRM_Utils_Array::value( $this->_firstNameIndex, $values ) &&
                    ! CRM_Utils_Array::value( $this->_lastNameIndex, $values );
            }
            break;

        case 'Household' :
            if ( $this->_householdNameIndex < 0 ) {
                $errorRequired = true;
            } else {
                $errorRequired = ! CRM_Utils_Array::value($this->_householdNameIndex, $values);
            }
            break;

        case 'Organization' :
            if ( $this->_organizationNameIndex < 0 ) {
                $errorRequired = true;
            } else {
                $errorRequired = ! CRM_Utils_Array::value($this->_organizationNameIndex, $values);
            }
            break;

        }

        if ( $this->_emailIndex >= 0 ) {
            /* If we don't have the required fields, bail */
            if ($this->_contactType == 'Individual' &&! $this->_updateWithId ) {
                if ($errorRequired && ! CRM_Utils_Array::value($this->_emailIndex, $values)) {
                    array_unshift($values, ts('Missing required fields'));
                    return CRM_Import_Parser::ERROR;
                }
            }
            
            $email = CRM_Utils_Array::value( $this->_emailIndex, $values );
            if ( $email ) {
                /* If the email address isn't valid, bail */
                if (! CRM_Utils_Rule::email($email)) {
                    array_unshift($values, ts('Invalid Email address'));
                    return CRM_Import_Parser::ERROR;
                }

                /* otherwise, count it and move on */
                $this->_allEmails[$email] = $this->_lineCount;
            }
        } else if ($errorRequired && ! $this->_updateWithId) {
            array_unshift($values, ts('Missing required fields'));
            return CRM_Import_Parser::ERROR;
        }
        
        //check for duplicate external Identifier
        $externalID = CRM_Utils_Array::value( $this->_externalIdentifierIndex, $values );
        if ( $externalID ) {
            /* If it's a dupe,external Identifier  */
            if ( $externalDupe = CRM_Utils_Array::value( $externalID, 
                                                         $this->_allExternalIdentifiers ) ) {
                array_unshift($values, ts('External Identifier conflicts with record %1', 
                                          array(1 => $externalDupe)));
                return CRM_Import_Parser::ERROR;
            }
            //otherwise, count it and move on
            $this->_allExternalIdentifiers[$externalID] = $this->_lineCount;
        }

        //Checking error in custom data
        $params =& $this->getActiveFieldParams( );
        $params['contact_type'] =  $this->_contactType;
        //date-format part ends

        $errorMessage = null;
        
        //checking error in custom data
     
        $this->isErrorInCustomData($params, $errorMessage);

        //checking error in core data
        $this->isErrorInCoreData($params, $errorMessage);
        if ( $errorMessage ) {
            if ( $errorMessage != 'custom_greeting' ) { 
                $tempMsg = "Invalid value for field(s) : $errorMessage";
            } else {
                $tempMsg = "Missing required field : Greeting Type";
            }
            array_unshift($values, $tempMsg);
            $errorMessage = null;
            return CRM_Import_Parser::ERROR;
        }
        
        return CRM_Import_Parser::VALID;
    }

    /**
     * handle the values in import mode
     *
     * @param int $onDuplicate the code for what action to take on duplicates
     * @param array $values the array of values belonging to this line
     *
     * @return boolean      the result of this processing
     * @access public
     */
    function import( $onDuplicate, &$values, $doGeocodeAddress = false ) 
    {
        // first make sure this is a valid line
        //$this->_updateWithId = false;
        $response = $this->summary( $values );
        
        if ( $response != CRM_Import_Parser::VALID ) {
            return $response;
        }
        
        $params =& $this->getActiveFieldParams( );
        
        $formatted = array('contact_type' => $this->_contactType);
        
        //for date-Formats
        $session =& CRM_Core_Session::singleton();
        $dateType = $session->get("dateTypes");
        $customFields = CRM_Core_BAO_CustomField::getFields( CRM_Utils_Array::value( 'contact_type',
                                                                                     $params ) );
        foreach ( $params  as $key => $val ) {
            if ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($key)) {
                if ( $customFields[$customFieldID]['data_type'] == 'Date' ) {
                    self::formatCustomDate( $params, $formatted, $dateType, $key );
                    unset( $params[$key] );
                } else if ( $customFields[$customFieldID]['data_type'] == 'Boolean' ) {
                    $params[$key] = CRM_Utils_String::strtoboolstr( $val );
                }
            }
            
            if ( $key == 'birth_date' && $val ) {
                CRM_Utils_Date::convertToDefaultDate( $params, $dateType, $key );
            } else if ( $key == 'deceased_date' && $val ) {
                CRM_Utils_Date::convertToDefaultDate( $params, $dateType, $key );
            } else if ( $key == 'is_deceased' && $val ) {
                $params[$key] = CRM_Utils_String::strtoboolstr( $val );
            }
        }
        //date-Format part ends
        
        static $contactFields = null;
        if ( $contactFields == null) {
            require_once "CRM/Contact/DAO/Contact.php";
            $contactFields =& CRM_Contact_DAO_Contact::import( );
        }
        
        foreach ($params as $key => $field) {
           
            if ($field == null || $field === '') {
                continue;
                
            }
         
            if (is_array($field)) {
                foreach ($field as $value) {
                    $break = false;
                    if ( is_array($value) ) {
                        foreach ($value as $name => $testForEmpty) {
                            if ($name !== 'phone_type' &&
                                ($testForEmpty === '' || $testForEmpty == null)) {
                                $break = true;
                                break;
                            }
                        }
                    } else {
                        $break = true;
                    }
                    if (! $break) {                    
                        _civicrm_add_formatted_param($value, $formatted);
                    }
                }
                continue;
            }
            
            $value = array($key => $field);
           
            if ( ( $key !== 'preferred_communication_method' ) && 
                 ( array_key_exists( $key, $contactFields   ) ) ) {
                // due to merging of individual table and
                // contact table, we need to avoid
                // preferred_communication_method forcefully
                $value['contact_type'] = $this->_contactType;
            }
            
            if ( $key == 'id' && isset( $field ) ) {
                $formatted[$key] = $field;
            }
            
            _civicrm_add_formatted_param($value, $formatted);
            
            //Handling Custom Data
            if ( ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($key)) && array_key_exists($customFieldID,$customFields) ) {
                $type = $customFields[$customFieldID]['html_type'];
                if( $type == 'CheckBox' || $type == 'Multi-Select' ) {
                    $mulValues = explode( ',' , $field );
                    $customOption = CRM_Core_BAO_CustomOption::getCustomOption($customFieldID, true);
                    $formatted[$key] = array();
                    foreach( $mulValues as $v1 ) {
                        foreach( $customOption as $v2 ) {
                            if (( strtolower($v2['label']) == strtolower(trim($v1)) ) ||
                                ( strtolower($v2['value']) == strtolower(trim($v1)) )) { 
                                if ( $type == 'CheckBox' ) {
                                    $formatted[$key][$v2['value']] = 1;
                                } else {
                                    $formatted[$key][] = $v2['value'];
                                }
                            }
                        }
                    }
                } else if ( $type == 'Select' || $type == 'Radio' ) {
                    $customOption = CRM_Core_BAO_CustomOption::getCustomOption($customFieldID, true);
                    foreach( $customOption as $v2 ) {
                        if (( strtolower($v2['label']) == strtolower(trim($field)) )||
                            ( strtolower($v2['value']) == strtolower(trim($field)) )) {
                            $formatted[$key] = $v2['value'];
                        }
                    }
                }
            }
        }
        
        //check if external identifier exists in database
        if ( isset( $params['external_identifier'] ) && ($onDuplicate != CRM_Import_Parser::DUPLICATE_UPDATE) ) {
            require_once "CRM/Contact/BAO/Contact.php";
            if ( CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                              $params['external_identifier'],
                                              'id',
                                              'external_identifier' ) ) {
                array_unshift($values, ts('External Identifier already exists in database.'));
                return CRM_Import_Parser::ERROR;
            }
        }

        $relationship = false;
        // Support Match and Update Via Contact ID
        if ( $this->_updateWithId && $onDuplicate == CRM_Import_Parser::DUPLICATE_UPDATE ) {
            if ( $params['id'] &&  $params['external_identifier'] ) {
                $cid = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                                    $params['external_identifier'], 'id',
                                                    'external_identifier' );
                if ( $cid !=  $params['id'] ) {
                    $message ="Mismatched External Id for".$params['id'] ;
                    array_unshift($values, $message);
                    $this->_retCode = CRM_Import_Parser::NO_MATCH;  
                }
            } else if ( $params['external_identifier'] ) {
                $cid = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                                    $params['external_identifier'], 'id',
                                                    'external_identifier' );
                if ( $cid ) {
                    $params['id'] =  $cid; 
                } else {
                    $message ="No contact ID found for this External Identifier:".$params['external_identifier'] ;
                    array_unshift($values, $message);
                    $this->_retCode = CRM_Import_Parser::NO_MATCH; 
                }  
            } 
            $error = _civicrm_duplicate_formatted_contact($formatted);
            if ( self::isDuplicate($error) ) { 
                $matchedIDs = explode( ',', $error['error_message']['params'][0] );
                if ( count( $matchedIDs) >= 1 ) {
                    $updateflag = true;
                    foreach ($matchedIDs  as $contactId) {
                        if ($params['id'] == $contactId) {
                            $paramsValues = array('contact_id'=>$contactId);
                            $contactType = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                                                        $params['id'],
                                                                        'contact_type' );
                            if ($formatted['contact_type'] == $contactType ) {
                                $newContact = $this->createContact( $formatted, $contactFields, 
                                                                    $onDuplicate, $contactId, false );
                                $updateflag = false; 
                                $this->_retCode = CRM_Import_Parser::VALID;
                            } else {
                                $message = "Mismatched contact Types :";
                                array_unshift($values, $message);
                                $updateflag = false;
                                $this->_retCode = CRM_Import_Parser::NO_MATCH;
                            }
                        } 
                    }
                    if ( $updateflag ) {
                        $message = "Mismatched contact IDs OR Mismatched contact Types :" ;
                        array_unshift($values, $message);
                        $this->_retCode = CRM_Import_Parser::NO_MATCH;
                    }
                }
            } else {
                $contactType = null;
                if ( CRM_Utils_Array::value( 'id', $params ) ) {
                    $paramsValues = array( 'contact_id' => $params['id'] );
                    $contactType  = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                                                 $params['id'],
                                                                 'contact_type' );
                }
                if ( $contactType ) {
                    if ($formatted['contact_type'] == $contactType ) {
                        $newContact = $this->createContact( $formatted, $contactFields, 
                                                            $onDuplicate, $params['id'], false );
                        
                        $this->_retCode = CRM_Import_Parser::VALID;
                    } else {
                        $message = "Mismatched contact Types :";
                        array_unshift($values, $message);
                        $this->_retCode = CRM_Import_Parser::NO_MATCH;
                    }
                } else {
                    $message ="No contact found for this contact ID:".$params['id'] ;
                    array_unshift($values, $message);
                    $this->_retCode = CRM_Import_Parser::NO_MATCH;  
                }
            }
            
            if (is_a( $newContact, 'CRM_Contact_BAO_Contact' )) {
                $relationship = true;
            } else if (is_a( $error, 'CRM_Core_Error' )) {
                $newContact = $error;
                $relationship = true;
            }
        } else {
            $formatted['individual_prefix'] = CRM_Core_OptionGroup::getValue( 'individual_prefix', (string)$formatted['prefix'] );
            $formatted['individual_suffix'] = CRM_Core_OptionGroup::getValue( 'individual_suffix', (string)$formatted['suffix'] );
            $formatted['gender']            = CRM_Core_OptionGroup::getValue( 'gender', (string)$formatted['gender'] );
            $formatted['greeting_type']     = CRM_Core_OptionGroup::getValue( 'greeting_type', (string)$formatted['greeting'] );

            $newContact = $this->createContact( $formatted, $contactFields, $onDuplicate );
            
        }
        
        if ( is_object( $newContact ) || ( $newContact instanceof CRM_Contact_BAO_Contact ) ) { 
            $relationship = true;
            $newContact = clone( $newContact );
            $this->_newContacts[] = $newContact->id;
        } else if ( self::isDuplicate( $newContact ) ) {
            $relationship = true;
            $this->_newContacts[] = $newContact['error_message']['params'][0]; 
        }
        
        if ( $relationship ) {
            $primaryContactId = null;
            if ( self::isDuplicate($newContact) ) {
                if ( CRM_Utils_Rule::integer( $newContact['error_message']['params'][0] ) ) {
                    $primaryContactId = $newContact['error_message']['params'][0];
                }
            } else {
                $primaryContactId = $newContact->id;
            }
            
            if ( ( self::isDuplicate($newContact)  || is_a( $newContact, 'CRM_Contact_BAO_Contact' ) ) 
                 && $primaryContactId ) {
                
                //relationship contact insert
                foreach ($params as $key => $field) {
                    list($id, $first, $second) = CRM_Utils_System::explode('_', $key, 3);
                    if ( !($first == 'a' && $second == 'b') && !($first == 'b' && $second == 'a') ) {
                        continue;
                    }
                    
                    $relationType     = new CRM_Contact_DAO_RelationshipType();
                    $relationType->id = $id;
                    $relationType->find(true);
                    $name_a_b         = $relationType->name_a_b;
                    
                    $formatting   = array('contact_type' => $params[$key]['contact_type']);
                    
                    $contactFields = null;
                    $contactFields = CRM_Contact_DAO_Contact::import( );
                    
                    foreach ($field as $k => $v) {
                        if ($v == null || $v === '') {
                            continue;
                        }
                        
                        if (is_array($v)) {
                            foreach ($v as $value) {
                                $break = false;
                                foreach ($value as $testForEmpty) {
                                    if ($testForEmpty === '' || $testForEmpty == null) {
                                        $break = true;
                                        break;
                                    }                        
                                }
                                if (! $break) {
                                    _civicrm_add_formatted_param($value, $formatting);
                                }
                            }
                            continue;
                        }
                        
                        $value = array($k => $v);
                        if (array_key_exists($k, $contactFields)) {
                            $value['contact_type'] = $params[$key]['contact_type'];
                        }
                        
                        _civicrm_add_formatted_param($value, $formatting);
                    }

                    //Relation on the basis of External Identifier.
                    if ( !CRM_Utils_Array::value( 'id' , $params[$key] ) && isset ( $params[$key]['external_identifier'] ) ) {
                        $params[$key]['id'] = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact',
                                                                          $params[$key]['external_identifier'],'id',
                                                                          'external_identifier' );
                    }                    

                    //fix for CRM-1315
                    if ( $params[$key]['id'] ) {
                        $contact           = array( 'contact_id' => $params[$key]['id'] );
                        $defaults          = array( );
                        $relatedNewContact = CRM_Contact_BAO_Contact::retrieve( $contact, $defaults );
                    } else {
                        //fixed for CRM-3146
                        if ( ( $this->_contactType == 'Individual'   || 
                               $this->_contactType == 'Household'    ||
                               $this->_contactType == 'Organization' ) && $onDuplicate == CRM_Import_Parser::DUPLICATE_NOCHECK ) {
                            $onDuplicate = CRM_Import_Parser::DUPLICATE_FILL;
                        }

                        $relatedNewContact = $this->createContact( $formatting, $contactFields, 
                                                                   $onDuplicate, null, false );
                    }
                    
                    if ( is_object( $relatedNewContact ) || ( $relatedNewContact instanceof CRM_Contact_BAO_Contact ) ) {
                        $relatedNewContact = clone($relatedNewContact);
                    }
                    
                    $matchedIDs = array(  );
                    if ( is_array( $relatedNewContact ) && civicrm_error( $relatedNewContact ) ) {
                        if ( self::isDuplicate($relatedNewContact) ) {
                            $matchedIDs = explode(',',$relatedNewContact['error_message']['params'][0]);
                            //update the relative contact if dupe 
                            if ( $onDuplicate == CRM_Import_Parser::DUPLICATE_UPDATE || 
                                 $onDuplicate == CRM_Import_Parser::DUPLICATE_FILL ) {
                                $updatedContact = $this->createContact( $formatting, $contactFields, $onDuplicate, $matchedIDs[0] );
                            } 
                        } else {
                            array_unshift( $values, $relatedNewContact['error_message'] );
                            return CRM_Import_Parser::ERROR;
                        }
                    } else {
                        $matchedIDs[] = $relatedNewContact->id;
                    }
                    static $relativeContact = array( ) ;
                    if ( self::isDuplicate( $relatedNewContact ) ) {
                        if ( count( $matchedIDs ) >= 1 ) {
                            $relContactId = $matchedIDs[0];
                            //add relative contact to count during update & fill mode.
                            //logic to make count distinct by contact id.
                            if ( $this->_newRelatedContacts || ! empty( $relativeContact ) ) {
                                $reContact = array_keys( $relativeContact, $relContactId );
                                
                                if ( empty( $reContact ) ) {
                                    $this->_newRelatedContacts[] = $relativeContact[] = $relContactId;
                                }
                            } else {
                                $this->_newRelatedContacts[] = $relativeContact[] = $relContactId;
                            }
                        }
                    } else {
                        $relContactId                = $relatedNewContact->id;
                        $this->_newRelatedContacts[] = $relativeContact[] = $relContactId;
                    }
                    
                    if ( self::isDuplicate( $relatedNewContact ) ||
                         ( $relatedNewContact instanceof CRM_Contact_BAO_Contact ) ) {
                        //fix for CRM-1993.Checks for duplicate related contacts
                        if ( count( $matchedIDs ) >= 1 ) {
                            //if more than one duplicate contact
                            //found, create relationship with first contact
                            // now create the relationship record
                            $relationParams = array( );
                            $relationParams = array('relationship_type_id' => $key, 
                                                    'contact_check'        => array( $relContactId => 1),
                                                    'is_active'            => 1
                                                    );
                            
                            // we only handle related contact success, we ignore failures for now
                            // at some point wold be nice to have related counts as separate
                            $relationIds = array('contact' => $primaryContactId);
                            
                            list( $valid, $invalid, $duplicate, $saved, $relationshipIds ) = 
                                CRM_Contact_BAO_Relationship::create( $relationParams, $relationIds );
                            CRM_Contact_BAO_Relationship::relatedMemberships( $primaryContactId, 
                                                                              $relationParams,
                                                                              $relationIds );
                            //handle current employer, CRM-3532
                            if ( $valid ) {
                                require_once 'CRM/Core/PseudoConstant.php';
                                $allRelationships   = CRM_Core_PseudoConstant::relationshipType( );
                                $relationshipTypeId = str_replace( array('_a_b', '_b_a'), array('', ''),  $key );
                                $relationshipType   = str_replace( $relationshipTypeId . '_', '', $key );
                                $orgId = $individualId = null;
                                if ( $allRelationships[$relationshipTypeId]["name_{$relationshipType}"] == 'Employee of' ) {
                                    $orgId = $relContactId;
                                    $individualId = $primaryContactId;
                                } else if ( $allRelationships[$relationshipTypeId]["name_{$relationshipType}"] == 'Employer of' ) {
                                    $orgId = $primaryContactId;
                                    $individualId = $relContactId;
                                }
                                if ( $orgId && $individualId ) {
                                    $currentEmpParams[$individualId] = $orgId;
                                    require_once 'CRM/Contact/BAO/Contact/Utils.php';
                                    CRM_Contact_BAO_Contact_Utils::setCurrentEmployer( $currentEmpParams ); 
                                }
                            }
                        }
                    }
                }
            }
        }
        if( $this->_updateWithId ) {
            return $this->_retCode;
        }
        //dupe checking      
        if ( is_array( $newContact ) && civicrm_error( $newContact ) ) {
            $code = null;
            
            if ( ( $code = CRM_Utils_Array::value( 'code', $newContact['error_message'] ) ) && 
                 ( $code == CRM_Core_Error::DUPLICATE_CONTACT ) ) {
                $urls = array( );
                // need to fix at some stage and decide if the error will return an 
                // array or string, crude hack for now
                if ( is_array( $newContact['error_message']['params'][0] ) ) {
                    $cids = $newContact['error_message']['params'][0];
                } else {
                    $cids = explode( ',', $newContact['error_message']['params'][0] );
                }
                
                foreach ($cids as $cid) {
                    $urls[] = CRM_Utils_System::url('civicrm/contact/view',
                                                    'reset=1&cid=' . $cid, false);
                }
                
                $url_string = implode("\n", $urls);
                array_unshift($values, $url_string); 
                
                // If we duplicate more than one record, skip no matter what 
                if (count($cids) > 1) {
                    array_unshift($values, ts('Record duplicates multiple contacts'));
                    return CRM_Import_Parser::ERROR;
                }
                
                // Params only had one id, so shift it out 
                $contactId = array_shift( $cids );
                $cid       = null;
                                
                $vals = array( 'contact_id' => $contactId );
               
                if ($onDuplicate == CRM_Import_Parser::DUPLICATE_REPLACE) {
                    $result = civicrm_replace_contact_formatted( $contactId, $formatted, $contactFields );
                    $cid    = $result['result'];
                } else if ($onDuplicate == CRM_Import_Parser::DUPLICATE_UPDATE) {
                    $newContact = $this->createContact( $formatted, $contactFields, $onDuplicate, $contactId );
                } else if ($onDuplicate == CRM_Import_Parser::DUPLICATE_FILL) {
                    $newContact = $this->createContact( $formatted, $contactFields, $onDuplicate, $contactId );
                } // else skip does nothing and just returns an error code.
                
                if ( $cid ) {
                    $contact    = array( 'contact_id' => $cid );
                    $defaults   = array( );
                    $newContact = CRM_Contact_BAO_Contact::retrieve( $contact, $defaults );
                }
                
                if ( civicrm_error( $newContact ) ) {
                    $this->_newContacts[] = $newContact['error_message']['params'][0];
                }
                //CRM-262 No Duplicate Checking  
                if ($onDuplicate == CRM_Import_Parser::DUPLICATE_SKIP) {
                    return CRM_Import_Parser::DUPLICATE; 
                }
                
                return CRM_Import_Parser::VALID;
            } else { 
                // Not a dupe, so we had an error 
                array_unshift( $values, $newContact['error_message'] );
                return CRM_Import_Parser::ERROR;
            }
        }
        // sleep(3);
        return CRM_Import_Parser::VALID;
    }

    /**
     * Get the array of succesfully imported contact id's
     *
     * @return array
     * @access public
     */
    function &getImportedContacts() 
    {
        return $this->_newContacts;
    }
   
    /**
     * Get the array of succesfully imported related contact id's
     *
     * @return array
     * @access public
     */
    function &getRelatedImportedContacts() 
    {    
        return $this->_newRelatedContacts;
    }

    /**
     * the initializer code, called before the processing
     *
     * @return void
     * @access public
     */
    function fini( ) 
    {
    }

    /**
     *  function to check if an error is actually a duplicate contact error
     *  
     *  @param array/object $error (array of) valid Error object (values)
     *  
     *  @return true if error is duplicate contact error, false otherwise 
     *  
     *  @access public 
     */
    function isDuplicate($error)
    {
        if ( is_object( $error ) && ! ($error instanceof CRM_Core_Error ) ) {
            return false;
        }
        
        if ( is_array( $error )  && civicrm_error( $error ) ) {
            $code = $error['error_message']['code'];
            if ($code == CRM_Core_Error::DUPLICATE_CONTACT ) {
                return true ;
            }
        }
        
        return false;
    }

    /**
     *  function to check if an error in custom data
     *  
     *  @param String   $errorMessage   A string containing all the error-fields.
     *  
     *  @access public 
     */

    function isErrorInCustomData($params, &$errorMessage) 
    {
        $session =& CRM_Core_Session::singleton();
        $dateType = $session->get("dateTypes");
        $customFields = CRM_Core_BAO_CustomField::getFields( $params['contact_type'] );
        foreach ($params as $key => $value) {
            if ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($key)) {
                /* check if it's a valid custom field id */
                if ( !array_key_exists($customFieldID, $customFields)) {
                    self::addToErrorMsg('field ID', $errorMessage);
                }
                /* validate the data against the CF type */
     
                if ( $value ) {
                    if ($customFields[$customFieldID]['data_type'] == 'Date') {
                        if( CRM_Utils_Date::convertToDefaultDate( $params, $dateType, $key )) {
                            $value = $params[$key];
                            
                        } else {
                            self::addToErrorMsg($customFields[$customFieldID]['label'], $errorMessage);
                        }
                    } else if ( $customFields[$customFieldID]['label'] == 'Boolean') {
                        if (CRM_Utils_String::strtoboolstr($value) === false) {
                            self::addToErrorMsg($customFields[$customFieldID]['label'], $errorMessage);
                        }
                    }
                    // need not check for label filed import
                    $htmlType = array('CheckBox','Multi-Select','Select','Radio');
                    if ( ! in_array( $customFields[$customFieldID]['html_type'], $htmlType ) ||
                         $customFields[$customFieldID]['data_type'] =='Boolean' ) {

                        $valid = CRM_Core_BAO_CustomValue::typecheck(
                                                                     $customFields[$customFieldID]['data_type'], $value);
                        if (! $valid) {
                            self::addToErrorMsg($customFields[$customFieldID]['label'], $errorMessage);
                        }
                    }
                    
                    // check for values for custom fields for checkboxes and multiselect
                    if ( $customFields[$customFieldID]['html_type'] == 'CheckBox' || $customFields[$customFieldID]['html_type'] =='Multi-Select' ) {
                        $value = str_replace("|",",",$value);
                        $mulValues = explode( ',' , $value );
                        $customOption = CRM_Core_BAO_CustomOption::getCustomOption( $customFieldID, true );
                        foreach( $mulValues as $v1 ) {
                            $flag = false; 
                            foreach( $customOption as $v2 ) {
                                if (( strtolower(trim($v2['label'])) == strtolower(trim($v1)))||( strtolower(trim($v2['value'])) == strtolower(trim($v1)))) {
                                    $flag = true; 
                                }
                            }
                            if (! $flag ) {
                                self::addToErrorMsg($customFields[$customFieldID]['label'], $errorMessage);
                            }
                        }
                    } else if ( $customFields[$customFieldID]['html_type'] == 'Select' || 
                               ( $customFields[$customFieldID]['html_type'] =='Radio' && $customFields[$customFieldID]['data_type'] !='Boolean' ) ) {
                        $customOption = CRM_Core_BAO_CustomOption::getCustomOption( $customFieldID, true );
                        $flag = false;
                        foreach( $customOption as $v2 ) {
                            if (( strtolower(trim($v2['label'])) == strtolower(trim($value)) )||( strtolower(trim($v2['value'])) == strtolower(trim($value)) )) {
                                $flag = true; 
                            }
                        }
                        if (! $flag ) {
                            self::addToErrorMsg($customFields[$customFieldID]['label'], $errorMessage);
                        }
                    }
                }
            } else if ( is_array($params[$key]) &&
                        isset( $params[$key]["contact_type"] ) ) {
                self::isErrorInCustomData( $params[$key] ,$errorMessage );
            }
        }
    }
    
    /**
     * function to check if an error in Core( non-custom fields ) field
     *
     * @param String   $errorMessage   A string containing all the error-fields.
     *
     * @access public
     */
    function isErrorInCoreData($params, &$errorMessage) 
    {
        foreach ($params as $key => $value) {
            if ( $value ) {
                $session =& CRM_Core_Session::singleton();
                $dateType = $session->get("dateTypes");
                
                switch( $key ) {
                case 'birth_date': 
                    if( CRM_Utils_Date::convertToDefaultDate( $params, $dateType, $key  )) {
                        if (! CRM_Utils_Rule::date($params[$key])) {
                            self::addToErrorMsg('Birth Date', $errorMessage);
                        } 
                    } else {
                        self::addToErrorMsg('Birth-Date', $errorMessage); 
                    }
                    
                    break;
                case 'deceased_date': 
                    if( CRM_Utils_Date::convertToDefaultDate( $params, $dateType, $key  )) {
                        if (! CRM_Utils_Rule::date($value)) {
                            self::addToErrorMsg('Deceased Date', $errorMessage);
                        }
                    } else {
                        self::addToErrorMsg('Deceased Date', $errorMessage); 
                    }
                    break;
                case 'is_deceased': 
                    if (CRM_Utils_String::strtoboolstr($value) === false) {
                        self::addToErrorMsg('Is Deceased', $errorMessage);
                    }
                    break;
                case 'gender':    
                    if (!self::in_value($value,CRM_Core_PseudoConstant::gender())) {
                        self::addToErrorMsg('Gender', $errorMessage);
                    }
                    break;
                case 'preferred_communication_method':    
                    $preffComm = array( );
                    $preffComm = explode(',' , $value);
                    foreach ($preffComm as $v) {
                        if (!self::in_value($v, CRM_Core_PseudoConstant::pcm())) {
                            self::addToErrorMsg('Preferred Communication Method', $errorMessage);
                        }
                    }
                    break;
                    
                case 'preferred_mail_format':
                    if(!array_key_exists(strtolower($value),array_change_key_case(CRM_Core_SelectValues::pmf(), CASE_LOWER))) {
                        self::addToErrorMsg('Preferred Mail Format', $errorMessage);
                    }
                    break;
                case 'individual_prefix':
                    if (! self::in_value($value,CRM_Core_PseudoConstant::individualPrefix())) {
                        self::addToErrorMsg('Individual Prefix', $errorMessage);
                    }
                    break;
                case 'individual_suffix':
                    if (!self::in_value($value,CRM_Core_PseudoConstant::individualSuffix())) {
                        self::addToErrorMsg('Individual Suffix', $errorMessage);
                    }   
                    break;
                case 'greeting_type':
                    if ( !self::in_value($value,CRM_Core_PseudoConstant::greeting()) ) {
                        self::addToErrorMsg('Greeting Type', $errorMessage);
                    }   
                    break;     
                case 'state_province':
                    if ( ! empty( $value )) {
                        foreach($value as $stateValue ) {
                            if ( $stateValue['state_province']) {
                                if( self::in_value($stateValue['state_province'],CRM_Core_PseudoConstant::stateProvinceAbbreviation()) 
                                    || self::in_value($stateValue['state_province'], CRM_Core_PseudoConstant::stateProvince())) {
                                    continue;
                                } else {
                                    self::addToErrorMsg('State Province', $errorMessage);
                                }
                            }
                        }
                    }
                    
                    break;
                case 'country':
                    if (!empty( $value ) ) {
                        foreach($value as $stateValue ) {
                            if ( $stateValue['country'] ) {
                                CRM_Core_PseudoConstant::populate( $countryNames, 'CRM_Core_DAO_Country', 
                                                                   true, 'name', 'is_active' );
                                CRM_Core_PseudoConstant::populate( $countryIsoCodes, 
                                                                   'CRM_Core_DAO_Country',true, 
                                                                   'iso_code');
                                $config =& CRM_Core_Config::singleton();
                                $limitCodes = $config->countryLimit( );
                                //If no country is selected in
                                //localization then take all countries
                                if ( empty($limitCodes )) {
                                    $limitCodes = $countryIsoCodes; 
                                }
                              
                                if ( self::in_value($stateValue['country'], $limitCodes) || self::in_value($stateValue['country'], CRM_Core_PseudoConstant::country())) {
                                     continue;
                                } else {  
                                    if( self::in_value($stateValue['country'], $countryIsoCodes) || self::in_value($stateValue['country'], $countryNames)) {
                                        self::addToErrorMsg('country input is in table but not "available": "This Country is valid but is NOT in the list of Available Countries currently configured for your site. This can be viewed and modifed from Global Settings >> Localization." ', $errorMessage);
                                    }
                                    else {
                                        self::addToErrorMsg('country input value not in country table: "The Country value appears to be invalid. It does not match any value in CiviCRM table of countries."', $errorMessage);
                                    }
                                }
                                
                            }
                        }
                    }
                    break;
                case 'geo_code_1' :   
                    if (!empty( $value ) ) {
                        foreach($value as $codeValue ) {
                            if ( $codeValue['geo_code_1'] ) {
                                if ( CRM_Utils_Rule::numeric($codeValue['geo_code_1'])) {
                                    continue;
                                } else {
                                    self::addToErrorMsg('geo_code_1', $errorMessage);
                                }
                            }
                        }
                    }
                    break;
                case 'geo_code_2' :
                    if (!empty( $value ) ) {
                        foreach($value as $codeValue ) {
                            if ( $codeValue['geo_code_2'] ) {
                                if ( CRM_Utils_Rule::numeric($codeValue['geo_code_2'])) {
                                    continue;
                                } else {
                                    self::addToErrorMsg('geo_code_2', $errorMessage);
                                }
                            }
                        }
                    }
                    break;
                case 'custom_greeting' :
                     $greetingTypeLabel = CRM_Core_DAO::getFieldValue( 
                                                                         'CRM_Core_DAO_OptionValue', 
                                                                         'Customized', 
                                                                         'label', 
                                                                         'name'
                                                                          );
                     
                    if ( CRM_Utils_Array::value( 'greeting_type', $params ) != $greetingTypeLabel ) {
                        self::addToErrorMsg('custom_greeting', $errorMessage);
                    }
                    break;
                default : 
                    if ( is_array( $params[$key] ) && 
                         isset( $params[$key]["contact_type"] ) ) {
                        //check for any relationship data ,FIX ME
                        self::isErrorInCoreData($params[$key],$errorMessage);
                    }
                }
            }
        }
    }

    /**
     * function to ckeck a value present or not in a array
     *
     * @return ture if value present in array or retun false 
     * 
     * @access public
     */
    function in_value($value , $valueArray) 
    {
        foreach( $valueArray  as $key => $v ) {
            //fix for CRM-1514
            if ( strtolower( trim($v, "." ) ) == strtolower( trim( $value, "."  ) ) ) {
                return true;
            }
        }
        return false;
    }

    /**
     * function to build error-message containing error-fields  
     *
     * @param String   $errorName      A string containing error-field name.
     * @param String   $errorMessage   A string containing all the error-fields, where the new errorName is concatenated. 
     * 
     * @static
     * @access public
     */
    static function addToErrorMsg($errorName, &$errorMessage) 
    {
        if ($errorMessage) {
            $errorMessage .= "; $errorName";
        } else {
            $errorMessage = $errorName;
        }
    }
    
    /**
     * method for creating contact
     * 
     * 
     */
    function createContact( &$formatted, &$contactFields, $onDuplicate, $contactId = null, $requiredCheck = true )
    {
        $dupeCheck = false;
        
        $newContact = null;
        
        if ( is_null( $contactId ) && ($onDuplicate != CRM_Import_Parser::DUPLICATE_NOCHECK) ) {
            $dupeCheck = (bool)($onDuplicate);
        }
        
        //get the prefix id etc if exists
        CRM_Contact_BAO_Contact::resolveDefaults($formatted, true);

        require_once 'api/v2/Contact.php';
        // setting required check to false, CRM-2839
        // plus we do our own required check in import
        $error = civicrm_contact_check_params( $formatted, $dupeCheck, true, false );
        
        if ( ( is_null( $error )                                                ) && 
             ( civicrm_error( _civicrm_validate_formatted_contact($formatted) ) ) ) {
            $error = _civicrm_validate_formatted_contact($formatted);
        }
        
        $newContact = $error;
        
        if ( is_null( $error ) ) {
            if ( $contactId ) {
                $this->formatParams( $formatted, $onDuplicate, (int)$contactId );
            }
            
            $cid = CRM_Contact_BAO_Contact::createProfileContact( $formatted, $contactFields, 
                                                                  $contactId, null, null, 
                                                                  $formatted['contact_type'] );
            $contact    = array( 'contact_id' => $cid );
            
            $defaults   = array( );
            $newContact = CRM_Contact_BAO_Contact::retrieve($contact, $defaults );
        }
        
        return $newContact;
    }
    
    /**
     * format params for update and fill mode
     *
     * @param $params       array  referance to an array containg all the
     *                             values for import
     * @param $onDuplicate  int
     * @param $cid          int    contact id
     */
    function formatParams( &$params, $onDuplicate, $cid )
    {
        if ( $onDuplicate == CRM_Import_Parser::DUPLICATE_SKIP ) {
            return;
        }
        
        $contactParams    = array( 'contact_id' => $cid );
        
        $defaults         = array( );
        $contactObj       = CRM_Contact_BAO_Contact::retrieve( $contactParams, $defaults );
        
        $modeUpdate       = $modeFill   = false;
        
        if ( $onDuplicate == CRM_Import_Parser::DUPLICATE_UPDATE ) {
            $modeUpdate   = true;
        }
        
        if ( $onDuplicate == CRM_Import_Parser::DUPLICATE_FILL ) {
            $modeFill     = true;
        }
        
        require_once 'CRM/Core/BAO/CustomGroup.php';
        $groupTree = CRM_Core_BAO_CustomGroup::getTree($params['contact_type'],$cid,0,null);
        CRM_Core_BAO_CustomGroup::setDefaults( $groupTree, $defaults, false, false );
        
        $contact = get_object_vars( $contactObj );
        
        $location = null;
        foreach( $params as $key => $value ) {
            if ( $key == 'id' || $key == 'contact_type' ) {
                continue;
            }
                        
            if ( $key == 'location' ) {
                $location = true;
            } else if ($customFieldId = CRM_Core_BAO_CustomField::getKeyID($key)) {
                $custom = true;
            } else {
                $getValue = CRM_Utils_Array::retrieveValueRecursive($contact, $key);
                
                if ( $key == 'contact_source' ) {
                    $params['source'] = $params[$key];
                    unset( $params[$key] );
                }
                
                if ( $modeFill   &&   isset( $getValue ) ) {
                    unset( $params[$key] );
                }
            }
        }
        
        if ( $location ) {
            for ( $loc = 1; $loc <= count( $params['location'] ); $loc++ ) {
                $getValue = CRM_Utils_Array::retrieveValueRecursive($contact['location'][$loc], 'location_type_id');
                
                if ( $modeFill && isset( $getValue ) ) {
                    unset( $params['location'][$loc] );
                }
                
                if ( array_key_exists( 'address', $contact['location'][$loc] ) ) {
                    $fields = array( 'street_address', 'city', 'state_province_id', 
                                     'postal_code', 'postal_code_suffix', 'country_id' );
                    foreach( $fields as $field ) {
                        $getValue = CRM_Utils_Array::retrieveValueRecursive($contact['location'][$loc]['address'], 
                                                                           $field);
                        if ( $modeFill && isset( $getValue ) ) {
                            unset( $params['location'][$loc]['address'][$field] );
                        }
                    }
                }
                
                $fields = array( 'email' => 'email', 'phone' => 'phone', 'im' => 'name' );
                foreach( $fields as $key => $field ) {
                    if ( array_key_exists( $key, $contact['location'][$loc] ) ) {
                        for ( $c = 1; $c <= count( $params['location'][$loc][$key] ); $c++ ) {
                            $getValue = CRM_Utils_Array::retrieveValueRecursive($contact['location'][$loc][$key][$c], 
                                                                               $field);
                            if ( $modeFill && isset( $getValue ) ) {
                                unset( $params['location'][$loc][$key][$c][$field] );
                            }
                        }
                    }
                }
            }              
        }
    }
    
    /**
     * convert any given date string to default date array.
     *
     * @param array  $params     has given date-format
     * @param array  $formatted  store formatted date in this array
     * @param int    $dateType   type of date  
     * @param string $dateParam  index of params
     * @static
     */
    function formatCustomDate( &$params, &$formatted, $dateType, $dateParam ) 
    {
        //fix for CRM-2687
        CRM_Utils_Date::convertToDefaultDate( $params, $dateType, $dateParam );
        
        if ( $dateType == 1 ) {
            if ( strstr( $params[$dateParam], '-' ) ) { 
                $formatted[$dateParam] = CRM_Utils_Date::unformat( $params[$dateParam] ); 
            } else {
                $formatted[$dateParam] = CRM_Utils_Date::unformat( CRM_Utils_Date::mysqlToIso( $params[$dateParam] ) );   
            }
        } else {
            $formatted[$dateParam] = CRM_Utils_Date::unformat( CRM_Utils_Date::mysqlToIso( $params[$dateParam] ) ); 
        }
    }
}


