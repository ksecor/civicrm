<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Import/Parser.php';

require_once 'api/crm.php';

/**
 * class to parse contact csv files
 */
class CRM_Import_Parser_Contact extends CRM_Import_Parser {

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
                          $mapperRelatedContactPhoneType = null) {
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
    function init( ) {
        require_once 'CRM/Contact/BAO/Contact.php';
        $fields =& CRM_Contact_BAO_Contact::importableFields( $this->_contactType );
       
        //Relationship importables
        $relations = CRM_Contact_BAO_Relationship::getContactRelationshipType( null, null, null, $this->_contactType );
        
        foreach ($relations as $key => $var) {
            list( $type ) = explode( '_', $key );
            $relationshipType[$key]['title'] = $var;
            $relationshipType[$key]['headerPattern'] = '/' . $var . '/';
            $relationshipType[$key]['import'] = true;
            $relationshipType[$key]['relationship_type_id'] = $type;
            $relationshipType[$key]['related'] = true;
        }

        if ( !empty($relationshipType) ) {
            $fields = array_merge($fields, array( 'related' => array('title' => '- related contact info -')) + $relationshipType);
        }

        foreach ($fields as $name => $field) {
            $this->addField( $name, $field['title'], $field['type'], $field['headerPattern'], $field['dataPattern'], $field['hasLocationType'] );
        }

        $this->_newContacts = array();

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

        $index             = 0 ;
        foreach ( $this->_mapperKeys as $key ) {
            if ( $key == 'email' ) {
                $this->_emailIndex = $index;
                $this->_allEmails  = array( );
            }
            if ( $key == 'phone' ) {
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
            $index++;
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
    function mapField( &$values ) {
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
    function preview( &$values ) {
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
    function summary( &$values ) {
        $response = $this->setActiveFieldValues( $values );

        $errorRequired = false;
        switch ($this->_contactType) { 
        case 'Individual' :
            if ( $this->_firstNameIndex < 0 || $this->_lastNameIndex < 0) {
                $errorRequired = true;
            } else {
                $errorRequired = ! CRM_Utils_Array::value($this->_firstNameIndex, $values) &&
                    ! CRM_Utils_Array::value($this->_lastNameIndex, $values);
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
            if ($this->_contactType == 'Individual') {
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
                /* If it's a dupe, bail */
                if ( $dupe = CRM_Utils_Array::value( $email, $this->_allEmails ) ) {
                    array_unshift($values, ts('Email address conflicts with record %1', array(1 => $dupe)));
                    return CRM_Import_Parser::CONFLICT;
                }

                /* otherwise, count it and move on */
                $this->_allEmails[$email] = $this->_lineCount;
            }
        } else if ($errorRequired) {
            array_unshift($values, ts('Missing required fields'));
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
    function import( $onDuplicate, &$values) {
        // first make sure this is a valid line
        $this->_updateWithId = false;
        $response = $this->summary( $values );
        if ( $response != CRM_Import_Parser::VALID ) {
            return $response;
        }
        $params =& $this->getActiveFieldParams( );
        
        $formatted = array('contact_type' => $this->_contactType);
        
        static $indieFields = null;
        if ($indieFields == null) {
            require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_DAO_" . $this->_contactType) . ".php");
            eval('$tempIndieFields =& CRM_Contact_DAO_'.$this->_contactType.'::import();'); //modified for PHP4 issue
            $indieFields = $tempIndieFields;
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
                        _crm_add_formatted_param($value, $formatted);
                    }
                }
                continue;
            }
            
            $value = array($key => $field);
            if (array_key_exists($key, $indieFields)) {
                $value['contact_type'] = $this->_contactType;
            }

            _crm_add_formatted_param($value, $formatted);
        }
        if (in_array('id',$this->_mapperKeys)) {
            $this->_updateWithId = true;
        }
        $relationship = false;
        // Support Match and Update Via Contact ID
        if($this->_updateWithId) {
            $error = _crm_duplicate_formatted_contact($formatted);
            if (is_a( $error, CRM_Core_Error )) {
                $code = $error ->_errors[0]['code'];
                if ($code == CRM_Core_Error::DUPLICATE_CONTACT) {
                    if(count($error->_errors[0]['params']) >= 1){
                        $updateflag = true;
                        foreach ($error->_errors[0]['params'] as $contactId) {
                            if($params['id'] == $contactId) {
                                $paramsValues = array('contact_id'=>$contactId);
                                $contactExits = crm_get_contact($paramsValues);
                                if ($formatted['contact_type'] == $contactExits->contact_type) {
                                    $newContact = crm_update_contact_formatted($contactId, $formatted, true);
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
                        if ($updateflag) {
                            $message = "Mismatched contact ID :".$params['id'];
                            array_unshift($values, $message);
                            $this->_retCode = CRM_Import_Parser::NO_MATCH;
                        }
                    }
                } 
            } else {
                $paramsValues = array('contact_id'=>$params['id']);
                $contact = crm_get_contact($paramsValues);
                if (is_a( $contact,CRM_Contact_BAO_Contact )) {
                    if ($formatted['contact_type'] == $contact->contact_type) {
                        $newContact = crm_update_contact_formatted($contact->id, $formatted, true);
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
            if (is_a( $newContact,CRM_Contact_BAO_Contact )) {
                $relationship = true;
            } else if (is_a( $error,CRM_Core_Error )) {
                $newContact = $error;
                $relationship = true;
            }
            if ($newContact && ! is_a($newContact, CRM_Core_Error)) {
                    $this->_newContacts[] = $newContact->id;
            }
        } else {
            $newContact = crm_create_contact_formatted( $formatted, $onDuplicate );
            $relationship = true;
        }
        if ( $relationship ) {
            $code = '';
            if ( is_a( $newContact, CRM_Core_Error ) ) {
                $code = $newContact->_errors[0]['code'];
                foreach ($newContact->_errors[0]['params'] as $cid) {
                    $primaryContactId = $cid;
                }
            } else {
                $primaryContactId = $newContact->id;
            }
            
            if ( $code == CRM_Core_Error::DUPLICATE_CONTACT || is_a( $newContact,CRM_Contact_BAO_Contact ) ) {
                //relationship contact insert
                foreach ($params as $key => $field) {
                    
                    list($id, $first, $second) = explode('_', $key);
                    if ( !($first == 'a' && $second == 'b') && !($first == 'b' && $second == 'a') ) {
                        continue;
                    }
                
                    $formatting = array('contact_type' => $params[$key]['contact_type']);
                    
                    $contactFields = null;
                    if ($contactFields == null) {
                        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_DAO_" . $params[$key]['contact_type']) . ".php");
                        eval('$contactFields =& CRM_Contact_DAO_'.$params[$key]['contact_type'].'::import();');
                    }
                    
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
                                    _crm_add_formatted_param($value, $formatting);
                                }
                            }
                            continue;
                        }
                        
                        $value = array($k => $v);
                        if (array_key_exists($k, $contactFields)) {
                            $value['contact_type'] = $params[$key]['contact_type'];
                        }
                        _crm_add_formatted_param($value, $formatting);
                    }
                    
                    $relatedNewContact = crm_create_contact_formatted( $formatting, $onDuplicate );
                    //print_r($relatedNewContact);
                    $newCode = '' ;
                    if ( is_a( $relatedNewContact, CRM_Core_Error ) ) {
                        $newCode = $newContact->_errors[0]['code'];
                        foreach ($relatedNewContact->_errors[0]['params'] as $cid) {
                            $relContactId = $cid;
                        }
                    } else {
                        $relContactId = $relatedNewContact->id;
                    }
                    
                    if($newCode == CRM_Core_Error::DUPLICATE_CONTACT || is_a( $relatedNewContact ,CRM_Contact_BAO_Contact )) {
                        //store the related contact id for groups
                        $this->_newRelatedContacts[] = $relContactId;
                        
                        // now create the relationship record
                        $relationParams = array();
                        $relationParams = array('relationship_type_id' => $key, 
                                                'contact_check' => array( $relContactId => 1)
                                                );
                        
                        $relationIds = array('contact' => $primaryContactId);
                        CRM_Contact_BAO_Relationship::create( $relationParams, $relationIds );
                        
                        //check if the two contacts are related and of type individual
                        /*if ( $params[$key]['contact_type'] == 'Individual' && $this->_contactType  == 'Individual') {
                            $householdName = "The ".$formatting['last_name']." household";
                            $householdFormatting = array( 'contact_type' => 'Household', 'household_name' => $householdName );
                            $householdContact = crm_create_contact_formatted( $householdFormatting, $onDuplicate );
                            if ( is_a( $householdContact, CRM_Core_Error ) ) {
                                foreach ($householdContact->_errors[0]['params'] as $cid) {
                                    $householdId = $cid;
                                }
                            } else {
                                $householdId = $householdContact->id;
                            }
                            
                            //Household contact is created 
                            //for two related individual contacts waiting confirmation whether 
                            //to add it in a group
                            //$this->_newRelatedContacts[] = $householdId;
                            
                            $relationParams = array();
                            // adding household relationship
                            $relType = '7_'.$second.'_'.$first;
                            
                            $relationParams = array('relationship_type_id' => $relType,
                                                    'contact_check'        => array( $relContactId => 1,
                                                                                     $primaryContactId => 1)
                                                    );
                            $relationIds = array('contact' => $householdId);
                            CRM_Contact_BAO_Relationship::create( $relationParams, $relationIds );
                        }*/
                    }
                }
            }
        }
        if( $this->_updateWithId ) {
            return $this->_retCode;
        }
        
        //dupe checking
        if ( is_a( $newContact, CRM_Core_Error ) ) 
        {   
            $code = $newContact->_errors[0]['code'];
            if ($code == CRM_Core_Error::DUPLICATE_CONTACT) {
                $urls = array( );
                foreach ($newContact->_errors[0]['params'] as $cid) {
                    $urls[] = CRM_Utils_System::url('civicrm/contact/view',
                                                    'reset=1&cid=' . $cid, true);
                }
                
                $url_string = implode("\n", $urls);
                array_unshift($values, $url_string); 
                
                /* If we duplicate more than one record, skip no matter what */
                if (count($newContact->_errors[0]['params']) > 1) {
                    array_unshift($values, ts('Record duplicates multiple contacts'));
                    return CRM_Import_Parser::ERROR;
                }
           
                /* Params only had one id, so shift it out */
                $contactId = array_shift($newContact->_errors[0]['params']);
            
                if ($onDuplicate == CRM_Import_Parser::DUPLICATE_REPLACE) {
                    $newContact = crm_replace_contact_formatted($contactId, $formatted);
                } else if ($onDuplicate == CRM_Import_Parser::DUPLICATE_UPDATE) {
                    $newContact = crm_update_contact_formatted($contactId, $formatted, true);

                } else if ($onDuplicate == CRM_Import_Parser::DUPLICATE_FILL) {
                    $newContact = crm_update_contact_formatted($contactId, $formatted, false);
                } // else skip does nothing and just returns an error code.
            
                if ($newContact && ! is_a($newContact, CRM_Core_Error)) {
                    $this->_newContacts[] = $newContact->id;
                }
                //CRM-262 No Duplicate Checking  
                if ($onDuplicate == CRM_Import_Parser::DUPLICATE_SKIP) {
                    return CRM_Import_Parser::DUPLICATE; 
                }
                return CRM_Import_Parser::VALID;
            } else { 
                /* Not a dupe, so we had an error */
                array_unshift($values, $newContact->_errors[0]['message']);
                return CRM_Import_Parser::ERROR;
            }
        }

        if ($newContact && ! is_a($newContact, CRM_Core_Error)) {
            $this->_newContacts[] = $newContact->id;
        }
        return CRM_Import_Parser::VALID;
    }
   
    /**
     * Get the array of succesfully imported contact id's
     *
     * @return array
     * @access public
     */
    function &getImportedContacts() {
        return $this->_newContacts;
    }
   
    /**
     * Get the array of succesfully imported related contact id's
     *
     * @return array
     * @access public
     */
    function &getRelatedImportedContacts() {
        return $this->_newRelatedContacts;
    }

    /**
     * the initializer code, called before the processing
     *
     * @return void
     * @access public
     */
    function fini( ) {
    }

}

?>
