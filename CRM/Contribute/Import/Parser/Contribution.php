<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Contribute/Import/Parser.php';
require_once 'api/v2/Contribute.php';

/**
 * class to parse contribution csv files
 */
class CRM_Contribute_Import_Parser_Contribution extends CRM_Contribute_Import_Parser 
{

    protected $_mapperKeys;

    private $_contactIdIndex;
    private $_totalAmountIndex;
    private $_contributionTypeIndex;

    protected $_mapperSoftCredit;
    //protected $_mapperPhoneType;
    /**
     * Array of succesfully imported contribution id's
     *
     * @array
     */
    protected $_newContributions;

    /**
     * class constructor
     */
    function __construct( &$mapperKeys, $mapperSoftCredit = null, $mapperPhoneType = null) 
    {
        parent::__construct();
        $this->_mapperKeys =& $mapperKeys;
        $this->_mapperSoftCredit =& $mapperSoftCredit;
    }

    /**
     * the initializer code, called before the processing
     *
     * @return void
     * @access public
     */
    function init( ) 
    {
        require_once 'CRM/Contribute/BAO/Contribution.php';
        $fields =& CRM_Contribute_BAO_Contribution::importableFields( $this->_contactType , false );
        
        $fields = array_merge( $fields,
                               array( 'soft_credit' => array( 'title'      => ts('Soft Credit'),
                                                              'softCredit' => true ) ) );
                                                          
        // add pledge fields only if its is enabled
        if ( CRM_Core_Permission::access( 'CiviPledge' ) ) {
            $pledgeFields = array( 'pledge_payment' => array( 'title' => ts('Pledge Payment') ),
                                   'pledge_id'      => array( 'title' => ts('Pledge ID') )
                                 );
            
            $fields = array_merge( $fields, $pledgeFields );
        }
        foreach ($fields as $name => $field) {
            $this->addField( $name, $field['title'], $field['type'], $field['headerPattern'], $field['dataPattern'] );
        }

        $this->_newContributions = array();
       
        $this->setActiveFields( $this->_mapperKeys );
        $this->setActiveFieldSoftCredit( $this->_mapperSoftCredit ); 

        // FIXME: we should do this in one place together with Form/MapField.php
        $this->_contactIdIndex        = -1;
        $this->_totalAmountIndex      = -1;
        $this->_contributionTypeIndex = -1;
        
        $index = 0;
        foreach ( $this->_mapperKeys as $key ) {
            switch ($key) {
            case 'contribution_contact_id':
                $this->_contactIdIndex        = $index;
                break;
            case 'total_amount':
                $this->_totalAmountIndex      = $index;
                break;
            case 'contribution_type':
                $this->_contributionTypeIndex = $index;
                break;
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
    function mapField( &$values ) 
    {
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
        $erroneousField = null;
        $response = $this->setActiveFieldValues( $values, $erroneousField );
        
        $params =& $this->getActiveFieldParams( );
        require_once 'CRM/Import/Parser/Contact.php';
        $errorMessage = null;
        
        //for date-Formats
        $session =& CRM_Core_Session::singleton();
        $dateType = $session->get("dateTypes");
        foreach ($params as $key => $val) {
            if( $val ) {
                switch( $key ) {
                case  'receive_date': 
                    if( CRM_Utils_Date::convertToDefaultDate( $params, $dateType, $key )) {
                        if (! CRM_Utils_Rule::date($params[$key])) {
                            CRM_Import_Parser_Contact::addToErrorMsg('Receive Date', $errorMessage);
                        }
                    } else {
                        CRM_Import_Parser_Contact::addToErrorMsg('Receive Date', $errorMessage);
                    }
                    break;
                case  'cancel_date': 
                    if( CRM_Utils_Date::convertToDefaultDate( $params, $dateType, $key )) {
                        if (! CRM_Utils_Rule::date($params[$key])) {
                            CRM_Import_Parser_Contact::addToErrorMsg('Cancel Date', $errorMessage);
                        }
                    } else {
                        CRM_Import_Parser_Contact::addToErrorMsg('Cancel Date', $errorMessage);
                    }
                    break;
                case  'receipt_date': 
                    if( CRM_Utils_Date::convertToDefaultDate( $params, $dateType, $key )) {
                        if (! CRM_Utils_Rule::date($params[$key])) {
                            CRM_Import_Parser_Contact::addToErrorMsg('Receipt date', $errorMessage);
                        }
                    } else {
                        CRM_Import_Parser_Contact::addToErrorMsg('Receipt date', $errorMessage);
                    }
                    break;
                case  'thankyou_date': 
                    if( CRM_Utils_Date::convertToDefaultDate( $params, $dateType, $key )) {
                        if (! CRM_Utils_Rule::date($params[$key])) {
                            CRM_Import_Parser_Contact::addToErrorMsg('Thankyou Date', $errorMessage);
                        }
                    } else {
                        CRM_Import_Parser_Contact::addToErrorMsg('Thankyou Date', $errorMessage);
                    }
                    break;
                    
                }
            }
        }
        //date-Format part ends

        $params['contact_type'] =  'Contribution';
        
        //checking error in custom data
        CRM_Import_Parser_Contact::isErrorInCustomData($params, $errorMessage);

        if ( $errorMessage ) {
            $tempMsg = "Invalid value for field(s) : $errorMessage";
            array_unshift($values, $tempMsg);
            $errorMessage = null;
            return CRM_Import_Parser::ERROR;
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
    function import( $onDuplicate, &$values) 
    {
        // first make sure this is a valid line
        $response = $this->summary( $values );
        if ( $response != CRM_Contribute_Import_Parser::VALID ) {
            return $response;
        }
        $noSoftCredit = false;
        $params =& $this->getActiveFieldParams( );
        
        if ( isset ( $params['pledge_payment'] ) ) {
            $pledgeParams['pledge_payment'] = $params['pledge_payment'];
            $pledgeParams['pledge_id'] = $params['pledge_id'];
        }
               
        //params for soft credit contact
        if ( !empty ( $params['soft_credit'] ) ) {
            $softParams = $params['soft_credit'];
        }
        $formatted = array();
        //find the contact to which the contribution is soft-credited
        if ( !empty ( $softParams ) ) {
            if ( isset( $softParams['contact_id'] ) ) {
                $softParams['id'] = $softParams['contact_id'];
                unset($softParams['contact_id']);
            }
            $softParams['contact_type']  = $this->_contactType;
            $formatted['soft_credit_to'] = $noSoftCredit = $this->getSoftCredit($softParams);
        }

        //for date-Formats
        $session =& CRM_Core_Session::singleton();
        $dateType = $session->get("dateTypes");
     
        $customFields = CRM_Core_BAO_CustomField::getFields( CRM_Utils_Array::value( 'contact_type',$params ) );
        
        foreach ($params as $key => $val) {
            if( $val ) {
                switch( $key ) {
                case  'receive_date': 
                    CRM_Utils_Date::convertToDefaultDate( $params, $dateType, $key );
                    break;
                case  'cancel_date': 
                    CRM_Utils_Date::convertToDefaultDate( $params, $dateType, $key );
                    break;
                case  'receipt_date': 
                    CRM_Utils_Date::convertToDefaultDate( $params, $dateType, $key );
                    break;
                case  'thankyou_date': 
                    CRM_Utils_Date::convertToDefaultDate( $params, $dateType, $key );
                    break;
                }
                if ( $customFieldID = CRM_Core_BAO_CustomField::getKeyID( $key ) ) {
                    if ( $customFields[$customFieldID]['data_type'] == 'Date' ) {
                        CRM_Import_Parser_Contact::formatCustomDate( $params, $formatted, $dateType, $key );
                        unset( $params[$key] );
                    } else if ( $customFields[$customFieldID]['data_type'] == 'Boolean' ) {
                        $params[$key] = CRM_Utils_String::strtoboolstr( $val );
                    }
                }
            }
        }
        //date-Format part ends

        static $indieFields = null;
        if ($indieFields == null) {
            require_once('CRM/Contribute/DAO/Contribution.php');
            $tempIndieFields =& CRM_Contribute_DAO_Contribution::import();
            $indieFields = $tempIndieFields;
        }
        
        $values = array();
        
        foreach ($params as $key => $field) {
            if ($field == null || $field === '') {
                continue;
            }
            $values[$key] = $field;
        }
        //import contribution record according to select contact type
        if ( $onDuplicate == CRM_Contribute_Import_Parser::DUPLICATE_SKIP && 
             ( $values['contribution_contact_id'] || $values['external_identifier'] ) ) {
            $values['contact_type'] = $this->_contactType;
        } else if( $onDuplicate == CRM_Contribute_Import_Parser::DUPLICATE_UPDATE && 
                   ( $values['contribution_id'] || $values['trxn_id'] || $values['invoice_id'] ) ) {
            $values['contact_type'] = $this->_contactType;
        }
        $formatError = _civicrm_contribute_formatted_param($values, $formatted, true);
       
        if ( $formatError ) {
            array_unshift($values, $formatError['error_message']);
            return CRM_Contribute_Import_Parser::ERROR;
        }
        
        if ( $onDuplicate != CRM_Contribute_Import_Parser::DUPLICATE_UPDATE ) {
            $formatted['custom'] = CRM_Core_BAO_CustomField::postProcess( $params,
                                                                          CRM_Core_DAO::$_nullObject,
                                                                          null,
                                                                          'Contribution' );
        } else {
            //fix for CRM-2219 - Update Contribution
            // onDuplicate == CRM_Contribute_Import_Parser::DUPLICATE_UPDATE
            if ( $values['invoice_id'] || $values['trxn_id'] || $values['contribution_id'] ) {
                require_once 'CRM/Contribute/BAO/Contribution.php';
                $dupeIds = array(
                                 'id'         => CRM_Utils_Array::value('contribution_id', $values),
                                 'trxn_id'    => CRM_Utils_Array::value('trxn_id',   $values),
                                 'invoice_id' => CRM_Utils_Array::value('invoice_id',$values)
                                 );              
                
                $ids['contribution'] = CRM_Contribute_BAO_Contribution::checkDuplicateIds( $dupeIds );                 
                if ( $ids['contribution'] ) {     
                    $formatted['id'] = $ids['contribution'];
                    $formatted['custom'] = CRM_Core_BAO_CustomField::postProcess( $params,
                                                                                  CRM_Core_DAO::$_nullObject,
                                                                                  $formatted['id'],
                                                                                  'Contribution' );
                     //process note
                    if ( $values['note'] ) {
                        $noteID = array();
                        $contactID = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_Contribution', $ids['contribution'], 'contact_id' );                       
                        require_once 'CRM/Core/BAO/Note.php';
                        $daoNote = & new CRM_Core_BAO_Note();
                        $daoNote->entity_table = 'civicrm_contribution';
                        $daoNote->entity_id    = $ids['contribution'];
                        if ( $daoNote->find(true) ) {
                            $noteID['id'] = $daoNote->id;                            
                        }
                                               
                        $noteParams = array(
                                            'entity_table' => 'civicrm_contribution', 
                                            'note'         => $values['note'], 
                                            'entity_id'    => $ids['contribution'],
                                            'contact_id'   => $contactID
                                            );
                        CRM_Core_BAO_Note::add($noteParams, $noteID);
                        unset($formatted['note']);
                    }
                    
                    $newContribution =& CRM_Contribute_BAO_Contribution::create( $formatted , $ids );
                    $this->_newContributions[] = $newContribution->id;
                    if ( !empty ( $softParams ) && $noSoftCredit === false ) {
                        array_unshift($values, "No match found for specified Soft Credit contact data. Row was skipped.");
                        return CRM_Contribute_Import_Parser::NO_MATCH;
                    } elseif ( is_numeric ( $noSoftCredit ) ) {
                        return CRM_Contribute_Import_Parser::SOFT_MATCH;
                    }
                    return CRM_Contribute_Import_Parser::VALID;
                } else {
                    $labels = array(
                                     'id'         => 'Contribution ID',
                                     'trxn_id'    => 'Transaction ID',
                                     'invoice_id' => 'Invoice ID'
                                     );              
                    foreach ( $dupeIds as $k=>$v ) {
                        if( $v ) {
                            $errorMsg[]  = "$labels[$k] $v";                
                        } 
                    }
                    $errorMsg = implode( ' AND ', $errorMsg );
                    array_unshift($values,"Matching Contribution record not found for ".$errorMsg.". Row was skipped.");
                    return CRM_Contribute_Import_Parser::ERROR; 
                }               
            }
        }
       
        if ( $this->_contactIdIndex < 0 ) {
            static $cIndieFields = null;
            if ($cIndieFields == null) {
                require_once 'CRM/Contact/BAO/Contact.php';
                $cTempIndieFields = CRM_Contact_BAO_Contact::importableFields( $this->_contactType );
                $cIndieFields = $cTempIndieFields;
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
                            _civicrm_add_formatted_param($value, $contactFormatted);
                            
                        }
                    }
                    continue;
                }
                
                $value = array($key => $field);
                if (array_key_exists($key, $cIndieFields)) {
                    $value['contact_type'] = $this->_contactType;
                }
                _civicrm_add_formatted_param($value, $contactFormatted);
            }

            $contactFormatted['contact_type'] = $this->_contactType;
            $error = _civicrm_duplicate_formatted_contact($contactFormatted);
            if ( civicrm_duplicate( $error ) ) {
                $matchedIDs = explode(',',$error['error_message']['params'][0]);        
                if (count( $matchedIDs) >1) {
                    array_unshift($values,"Multiple matching contact records detected for this row. The contribution was not imported");
                    return CRM_Contribute_Import_Parser::ERROR;
                } else {
                    $cid = $matchedIDs[0];
                    $formatted['contact_id'] = $cid;
                    $newContribution = civicrm_contribution_format_create( $formatted );
                    if ( civicrm_error( $newContribution ) ) { 
                        if ( is_array( $newContribution['error_message'] ) ) {
                            array_unshift($values, $newContribution['error_message']['message']);
                            if ( $newContribution['error_message']['params'][0] ) {
                                return CRM_Contribute_Import_Parser::DUPLICATE;
                            }
                        } else {
                            array_unshift($values, $newContribution['error_message']);
                            return CRM_Contribute_Import_Parser::ERROR;
                        }
                    }
                    
                    $this->_newContributions[] = $newContribution['id'];
                    return CRM_Contribute_Import_Parser::VALID;
                    
                }
                
            } else {
                // Using new Dedupe rule.
                $ruleParams = array(
                                    'contact_type' => $this->_contactType,
                                    'level' => 'Strict'
                                    );
                require_once 'CRM/Dedupe/BAO/Rule.php';
                $fieldsArray = CRM_Dedupe_BAO_Rule::dedupeRuleFields($ruleParams);
                
                foreach ( $fieldsArray as $value ) {
                    if(array_key_exists(trim($value),$params)) {
                        $paramValue = $params[trim($value)];
                        if (is_array($paramValue)) {
                            $disp .= $params[trim($value)][0][trim($value)]." ";  
                        } else {
                            $disp .= $params[trim($value)]." ";
                        }
                    }
                } 
                
                if ( !$disp && CRM_Utils_Array::value('external_identifier',$params) ) {
                    $disp = $params['external_identifier'];
                }
                
                array_unshift($values,"No matching Contact found for (".$disp.")");
                return CRM_Contribute_Import_Parser::ERROR;
            }
           
        } else {
            if ( $values['external_identifier'] ) {
                $checkCid = new CRM_Contact_DAO_Contact();
                $checkCid->external_identifier = $values['external_identifier'];
                $checkCid->find(true);
                if ($checkCid->id != $formatted['contact_id']) {
                    array_unshift($values, "Mismatch of External identifier :" . $values['external_identifier'] . " and Contact Id:" . $formatted['contact_id']);
                    return CRM_Contribute_Import_Parser::ERROR;
                }
            }
            $newContribution = civicrm_contribution_format_create( $formatted );
            if ( civicrm_error( $newContribution ) ) { 
                if ( is_array( $newContribution['error_message'] ) ) {
                    array_unshift($values, $newContribution['error_message']['message']);
                    if ( $newContribution['error_message']['params'][0] ) {
                        return CRM_Contribute_Import_Parser::DUPLICATE;
                    }
                } else {
                    array_unshift($values, $newContribution['error_message']);
                    return CRM_Contribute_Import_Parser::ERROR;
                }
            }
            
            $this->_newContributions[] = $newContribution['id'];
            
            //params for pledge payment assoc w/ the contribution
            if ( !empty ( $pledgeParams ) ) {
                $pledgeParams['contact_id'] = $params['contact_id'];
                $pledgeParams['scheduled_amount'] = $params['total_amount'];
                $this->matchPledgePayment($pledgeParams);
            }
            
            if ( !empty ( $softParams ) && $noSoftCredit === false ) {
                array_unshift($values, "No match found for specified Soft Credit contact data. Row was skipped.");
                return CRM_Contribute_Import_Parser::NO_MATCH;
            } elseif ( is_numeric ( $noSoftCredit ) ) {
                return CRM_Contribute_Import_Parser::SOFT_MATCH;
            }
            return CRM_Contribute_Import_Parser::VALID;
        }
        
    }

    /**
     * Get the pledge payment id against which the contribution is made
     *
     * @return array
     * @access public
     */
    function &matchPledgePayment( $params ) 
    {
        if ( CRM_Utils_String::strtoboolstr( $params['pledge_payment'] ) ) {
            if ( isset ( $params['pledge_id'] ) ) {
                require_once 'CRM/Contribute/PseudoConstant.php';
                $params['status_id'] = array_search( 'Pending', CRM_Contribute_PseudoConstant::contributionStatus( ) );
                
                require_once 'CRM/Pledge/BAO/Payment.php';
                CRM_Pledge_BAO_Payment::retrieve( $params, $defaults );
                if ( isset ( $defaults['id'] ) ) {
                    CRM_Pledge_BAO_Payment::updatePledgePaymentStatus( $params['pledge_id'], array($defaults['id']), 1  );
                    return true;
                }
                return false;
            }
        }
        return false;   
     }
    
    /**
     * Get succesfully matched contact id(for soft credits)
     *
     * @return array
     * @access public
     */
    function &getSoftCredit( $params ) 
    {
        $error = _civicrm_duplicate_formatted_contact($params);
        if ( isset( $error['error_message']['params'][0] )) {
            $matchedIDs = explode(',',$error['error_message']['params'][0]);
            return $matchedIDs[0] ? $matchedIDs[0] : false;
        }
        return false;   
     }
    
    /**
     * Get the array of succesfully imported contribution id's
     *
     * @return array
     * @access public
     */
    function &getImportedContributions() 
    {
        return $this->_newContributions;
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

}


