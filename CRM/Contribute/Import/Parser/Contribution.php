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

require_once 'CRM/Contribute/Import/Parser.php';

require_once 'api/crm.php';

/**
 * class to parse contribution csv files
 */
class CRM_Contribute_Import_Parser_Contribution extends CRM_Contribute_Import_Parser {

    protected $_mapperKeys;

    /**
     * Array of succesfully imported contribution id's
     *
     * @array
     */
    protected $_newContributions;

    /**
     * class constructor
     */
    function __construct( &$mapperKeys ) {
        parent::__construct();
        $this->_mapperKeys =& $mapperKeys;
    }

    /**
     * the initializer code, called before the processing
     *
     * @return void
     * @access public
     */
    function init( ) {
        require_once 'CRM/Contribute/BAO/Contribution.php';
        $fields =& CRM_Contribute_BAO_Contribution::importableFields( );

        foreach ($fields as $name => $field) {
            $this->addField( $name, $field['title'], $field['type'], $field['headerPattern'], $field['dataPattern']);
        }

        $this->_newContributions = array();

        $this->setActiveFields( $this->_mapperKeys );
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
        return CRM_Contribute_Import_Parser::VALID;
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
#       switch ($this->_contactType) { 
#       case 'Individual' :
#           if ( $this->_firstNameIndex < 0 || $this->_lastNameIndex < 0) {
#               $errorRequired = true;
#           } else {
#               $errorRequired = ! CRM_Utils_Array::value($this->_firstNameIndex, $values) &&
#                   ! CRM_Utils_Array::value($this->_lastNameIndex, $values);
#           }
#           break;
#       case 'Household' :
#           if ( $this->_householdNameIndex < 0 ) {
#               $errorRequired = true;
#           } else {
#               $errorRequired = ! CRM_Utils_Array::value($this->_householdNameIndex, $values);
#           }
#           break;
#       case 'Organization' :
#           if ( $this->_organizationNameIndex < 0 ) {
#               $errorRequired = true;
#           } else {
#               $errorRequired = ! CRM_Utils_Array::value($this->_organizationNameIndex, $values);
#           }
#           break;
#       }

#       if ( $this->_emailIndex >= 0 ) {
#           /* If we don't have the required fields, bail */
#           if ($this->_contactType == 'Individual') {
#               if ($errorRequired && ! CRM_Utils_Array::value($this->_emailIndex, $values)) {
#                   array_unshift($values, ts('Missing required fields'));
#                   return CRM_Contribute_Import_Parser::ERROR;
#               }
#           }
#           
#           $email = CRM_Utils_Array::value( $this->_emailIndex, $values );
#           if ( $email ) {
#               /* If the email address isn't valid, bail */
#               if (! CRM_Utils_Rule::email($email)) {
#                   array_unshift($values, ts('Invalid Email address'));
#                   return CRM_Contribute_Import_Parser::ERROR;
#               }
#               /* If it's a dupe, bail */
#               if ( $dupe = CRM_Utils_Array::value( $email, $this->_allEmails ) ) {
#                   array_unshift($values, ts('Email address conflicts with record %1', array(1 => $dupe)));
#                   return CRM_Contribute_Import_Parser::CONFLICT;
#               }

#               /* otherwise, count it and move on */
#               $this->_allEmails[$email] = $this->_lineCount;
#           }
#       } else
        if ($errorRequired) {
            array_unshift($values, ts('Missing required fields'));
            return CRM_Contribute_Import_Parser::ERROR;
        }

        return CRM_Contribute_Import_Parser::VALID;
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
        $response = $this->summary( $values );
        if ( $response != CRM_Contribute_Import_Parser::VALID ) {
            return $response;
        }

        $params =& $this->getActiveFieldParams( );
        $formatted = array();
        
        static $indieFields = null;
        if ($indieFields == null) {
            require_once('CRM/Contribute/DAO/Contribution.php');
            $tempIndieFields =& CRM_Contribute_DAO_Contribution::import();
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
                            if (($testForEmpty === '' || $testForEmpty == null)) {
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
#               $value['contact_type'] = $this->_contactType;
            }

            _crm_add_formatted_contrib_param($value, $formatted);
        }

        $newContribution = crm_create_contribution_formatted( $formatted, $onDuplicate );

        if ( is_a( $newContribution, CRM_Core_Error ) ) {
            foreach ($newContribution->_errors[0]['params'] as $cid) {
                $contributionId = $cid;
            }
        } else {
            $contributionId = $newContribution->id;
        }
        

        //relationship contact insert
#       foreach ($params as $key => $field) {

#           list($id, $first, $second) = explode('_', $key);
#           if ( !($first == 'a' && $second == 'b') && !($first == 'b' && $second == 'a') ) {
#               continue;
#           }
#    
#           $formatting = array('contact_type' => $params[$key]['contact_type']);

#           $contactFields = null;
#           if ($contactFields == null) {
#               require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_DAO_" . $params[$key]['contact_type']) . ".php");
#               eval('$contactFields =& CRM_Contact_DAO_'.$params[$key]['contact_type'].'::import();');
#           }

#           foreach ($field as $k => $v) {
#               if ($v == null || $v === '') {
#                   continue;
#               }
#               
#               if (is_array($v)) {
#                   foreach ($v as $value) {
#                       $break = false;
#                       foreach ($value as $testForEmpty) {
#                           if ($testForEmpty === '' || $testForEmpty == null) {
#                               $break = true;
#                               break;
#                           }                        
#                       }
#                       if (! $break) {
#                           _crm_add_formatted_param($value, $formatting);
#                       }
#                   }
#                   continue;
#               }
#           
#               $value = array($k => $v);
#               if (array_key_exists($k, $contactFields)) {
#                   $value['contact_type'] = $params[$key]['contact_type'];
#               }
#               _crm_add_formatted_param($value, $formatting);
#           }

#           $relatedNewContact = crm_create_contact_formatted( $formatting, $onDuplicate );
#           //print_r($relatedNewContact);
#           if ( is_a( $relatedNewContact, CRM_Core_Error ) ) {
#               foreach ($relatedNewContact->_errors[0]['params'] as $cid) {
#                   $relContactId = $cid;
#               }
#           } else {
#               $relContactId = $relatedNewContact->id;
#           }
#           
#           //store the related contact id for groups
#           $this->_newRelatedContacts[] = $relContactId;

#           // now create the relationship record
#           $relationParams = array();
#           $relationParams = array('relationship_type_id' => $key, 
#                                   'contact_check' => array( $relContactId => 1)
#                                   );
#           
#           $relationIds = array('contact' => $primaryContactId);
#           CRM_Contact_BAO_Relationship::create( $relationParams, $relationIds );
#           
#           //check if the two contacts are related and of type individual
#           if ( $params[$key]['contact_type'] == 'Individual' && $this->_contactType  == 'Individual') {
#               $householdName = "The ".$formatting['last_name']." household";
#               $householdFormatting = array( 'contact_type' => 'Household', 'household_name' => $householdName );
#               $householdContact = crm_create_contact_formatted( $householdFormatting, $onDuplicate );
#               if ( is_a( $householdContact, CRM_Core_Error ) ) {
#                   foreach ($householdContact->_errors[0]['params'] as $cid) {
#                       $householdId = $cid;
#                   }
#               } else {
#                   $householdId = $householdContact->id;
#               }

#               //Household contact is created 
#               //for two related individual contacts waiting confirmation whether 
#               //to add it in a group
#               //$this->_newRelatedContacts[] = $householdId;
#               
#               $relationParams = array();
#               // adding household relationship
#               $relType = '7_'.$second.'_'.$first;

#               $relationParams = array('relationship_type_id' => $relType,
#                                       'contact_check'        => array( $relContactId => 1,
#                                                                        $primaryContactId => 1)
#                                       );
#               $relationIds = array('contact' => $householdId);

#               CRM_Contact_BAO_Relationship::create( $relationParams, $relationIds );
#           }
#       }

        //dupe checking
        if ( is_a( $newContribution, CRM_Core_Error ) ) 
        {    
            $code = $newContribution->_errors[0]['code'];
            if ($code == CRM_Core_Error::DUPLICATE_CONTRIBUTION) {
                $urls = array( );
            
                foreach ($newContribution->_errors[0]['params'] as $cid) {
                    $urls[] = CRM_Utils_System::url('civicrm/contribution/view',
                                                    'reset=1&cid=' . $cid, true);
                }
                
                $url_string = implode("\n", $urls);
                array_unshift($values, $url_string); 
                
                /* If we duplicate more than one record, skip no matter what */
                if (count($newContribution->_errors[0]['params']) > 1) {
                    array_unshift($values, ts('Record duplicates multiple contributions'));
                    return CRM_Contribute_Import_Parser::ERROR;
                }
           
                /* Params only had one id, so shift it out */
                $contributionId = array_shift($newContribution->_errors[0]['params']);
            
                if ($onDuplicate == CRM_Contribute_Import_Parser::DUPLICATE_REPLACE) {
                    $newContribution = crm_replace_contribution($contributionId, $formatted);
                } else if ($onDuplicate == CRM_Contribute_Import_Parser::DUPLICATE_UPDATE) {
                    $newContribution = crm_update_contribution($contributionId, $formatted, true);

                } else if ($onDuplicate == CRM_Contribute_Import_Parser::DUPLICATE_FILL) {
                    $newContribution = crm_update_contribution($contributionId, $formatted, false);
                } // else skip does nothing and just returns an error code.
            
                if (! is_a($newContribution, CRM_Core_Error)) {
                    $this->_newContributions[] = $newContribution->id;
                }
                //CRM-262 No Duplicate Checking  
                if ($onDuplicate == CRM_Contribute_Import_Parser::DUPLICATE_SKIP) {
                    return CRM_Contribute_Import_Parser::DUPLICATE; 
                }
            } else { 
                /* Not a dupe, so we had an error */
                array_unshift($values, $newContribution->_errors[0]['message']);
                return CRM_Contribute_Import_Parser::ERROR;
            }
        }
        
        $this->_newContributions[] = $newContribution->id;
        return CRM_Contribute_Import_Parser::VALID;
    }
   
    /**
     * Get the array of succesfully imported contribution id's
     *
     * @return array
     * @access public
     */
    function &getImportedContributions() {
        return $this->_newContributions;
    }
   
    /**
     * Get the array of succesfully imported related contact id's
     *
     * @return array
     * @access public
     */
#   function &getRelatedImportedContacts() {
#       return $this->_newRelatedContacts;
#   }

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
