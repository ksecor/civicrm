<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Event/Import/Parser.php';

require_once 'api/crm.php';

/**
 * class to parse membership csv files
 */
class CRM_Event_Import_Parser_Participant extends CRM_Event_Import_Parser
{
    protected $_mapperKeys;

    private $_contactIdIndex;
    
    //private $_totalAmountIndex;
    
    private $_eventIndex;
    private $_participantStatusIndex;
    private $_participantRoleIndex;

    /**
     * Array of succesfully imported participants id's
     *
     * @array
     */
    protected $_newParticipants;

    /**
     * class constructor
     */
    function __construct( &$mapperKeys,$mapperLocType = null, $mapperPhoneType = null)
    {
        parent::__construct();
        $this->_mapperKeys =& $mapperKeys;
    }
    
    /**
     * the initializer code, called before the processing
     *
     * @return void
     * @access public
     */
    function init( )
    {
        require_once 'CRM/Event/BAO/Participant.php';
        $fields =& CRM_Event_BAO_Participant::importableFields( $this->_contactType );
        
        foreach ($fields as $name => $field) {
            $this->addField( $name, $field['title'], $field['type'], $field['headerPattern'], $field['dataPattern']);
        }
        
        $this->_newParticipants = array();
        
        $this->setActiveFields( $this->_mapperKeys );
        
        // FIXME: we should do this in one place together with Form/MapField.php
        $this->_contactIdIndex         = -1;
        $this->_eventIndex             = -1;
        $this->_participantStatusIndex = -1;
        $this->_participantRoleIndex   = -1;
        
        $index = 0;
        
        foreach ( $this->_mapperKeys as $key ) {
            switch ($key) {
            case 'contact_id':
                $this->_contactIdIndex           = $index;
                break;
            case 'event_id':
                $this->_eventIndex               = $index;
                break;
            case 'event_status_id':
                $this->_participantStatusIndex   = $index;
                break;
            case 'role_id':
                $this->_participantRoleIndex     = $index;
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
        return CRM_Event_Import_Parser::VALID;
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
        
        $errorRequired = false;
        
        if (($this->_eventIndex < 0) || ($this->_participantStatusIndex < 0) || ($this->_participantRoleIndex < 0)) {
            $errorRequired = true;
        } else {
            $errorRequired = ! CRM_Utils_Array::value($this->_eventIndex, $values) ||
                ! CRM_Utils_Array::value($this->_participantStatusIndex, $values) ||
                ! CRM_Utils_Array::value($this->_participantRoleIndex, $values);
        }
        
        if ($errorRequired) {
            array_unshift($values, ts('Missing required fields'));
            return CRM_Event_Import_Parser::ERROR;
        }
        
        $params =& $this->getActiveFieldParams( );

        require_once 'CRM/Import/Parser/Contact.php';
        $errorMessage = null;
        
        //for date-Formats
        $session =& CRM_Core_Session::singleton( );
        $dateType = $session->get( "dateTypes" );
        
        foreach ( $params as $key => $val ) {
            if( $val && ( $key == 'event_register_date' ) ) {
                CRM_Utils_Date::convertToDefaultDate( $params, $dateType, $key );
                if (! CRM_Utils_Rule::date($params[$key])) {
                    CRM_Import_Parser_Contact::addToErrorMsg('Register Date', $errorMessage);
                }
            }
        }
        //date-Format part ends
        
        //$params['contact_type'] =  $this->_contactType;
        $params['contact_type'] = 'Participant';
        //checking error in custom data
        CRM_Import_Parser_Contact::isErrorInCustomData($params, $errorMessage);
        
        if ( $errorMessage ) {
            $tempMsg = "Invalid value for field(s) : $errorMessage";
            array_unshift($values, $tempMsg);
            $errorMessage = null;
            return CRM_Import_Parser::ERROR;
        }
        
        return CRM_Event_Import_Parser::VALID;
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
        
        if ( $response != CRM_Event_Import_Parser::VALID ) {
            return $response;
        }
        
        $params =& $this->getActiveFieldParams( );
        
        $session =& CRM_Core_Session::singleton();
        $dateType = $session->get( 'dateTypes' );
        
        foreach ($params as $key => $val) {
            if( $val ) {
                if ( $key == 'event_register_date' ) {
                    CRM_Utils_Date::convertToDefaultDate( $params, $dateType, $key );
                    if (! CRM_Utils_Rule::date($params[$key])) {
                        CRM_Import_Parser_Contact::addToErrorMsg('Register Date', $errorMessage);
                    }
                }
            }
        }
        //date-Format part ends
        
        $formatted = array();
        static $indieFields = null;
        if ($indieFields == null) {
            require_once('CRM/Event/BAO/Participant.php');
            $tempIndieFields =& CRM_Event_BAO_Participant::import();
            $indieFields = $tempIndieFields;
        }
        
        foreach ($params as $key => $field) {
            if ($field == null || $field === '') {
                continue;
            }
            
            require_once 'CRM/Core/OptionGroup.php';
            if ( $key == 'event_id' ) {
                $id = CRM_Core_DAO::getFieldValue( "CRM_Event_DAO_Event", $field, 'id', 'title' );
                $formatted[$key] = $id;
            } else if ( $key == 'event_status_id' ) {
                $id = CRM_Core_OptionGroup::getValue('participant_status', $field);
                $formatted[$key] = $id;
            } else if ( $key == 'role_id' ) {
                $id = CRM_Core_OptionGroup::getValue('participant_role', $field);
                $formatted[$key] = $id;
            } else if ( substr( $key, 0, 7) == 'custom_' ) { 
                require_once 'CRM/Core/DAO/CustomField.php';
                $cusotmField = new CRM_Core_DAO_CustomField();
                $cusotmField->id = (int) substr( $key, 7 );
                $cusotmField->find(true);
                
                if ( ($cusotmField->html_type == 'CheckBox') ||
                     ($cusotmField->html_type == 'Multi-Select') ) {
                    $newMultipleValue = str_replace("|",",",$field);
                    $tmpFormatted     = explode( ',' , $newMultipleValue );
                    $custuomOption = CRM_Core_BAO_CustomOption::getCustomOption($cusotmField->id, true);
                    
                    foreach( $tmpFormatted as $k1 => $v1 ) {
                        foreach( $custuomOption as $v2 ) {
                            //CRM_Core_Error::debug( 'v1', $v1 );
                            //CRM_Core_Error::debug( 'v2', $v2 );
                            if ( strtolower($v2['label']) == strtolower(trim($v1)) ) {
                                //unset($newValueArray[$k1]);
                                $formatted[$key][$v2['value']] = 1;
                            }
                        }
                    }
                } else {
                    $formatted[$key] = $field;
                }
            } else {
                $formatted[$key] = $field;
            }
        }
        
        // CRM_Event_BAO_Participant::add() handles register_date,
        // status_id and source. So, if $formatted contains
        // participant_register_date, event_status_id or event_source,
        // convert it to register_date, status_id or source
        $changes = array('event_register_date' => 'register_date',
                         'event_source'        => 'source',
                         'event_status_id'     => 'status_id'
                         );
        
        foreach ($changes as $orgVal => $changeVal) {
            if ( isset($formatted[$orgVal]) ) {
                $formatted[$changeVal] = $formatted[$orgVal];
                unset($formatted[$orgVal]);
            }
        }
        
        if ( $this->_contactIdIndex < 0 ) {
            static $cIndieFields = null;
            if ($cIndieFields == null) {
                require_once 'CRM/Contact/BAO/Contact.php';
                $cTempIndieFields = CRM_Contact_BAO_Contact::importableFields( $this->_contactType, null );
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
                            _crm_add_formatted_param($value, $contactFormatted);
                        }
                    }
                    continue;
                }
                
                $value = array($key => $field);
                if (array_key_exists($key, $cIndieFields)) {
                    $value['contact_type'] = $this->_contactType;
                }
                _crm_add_formatted_param($value, $contactFormatted);
            }
            
            $contactFormatted['contact_type'] = $this->_contactType;
            $error = _crm_duplicate_formatted_contact($contactFormatted);
            $matchedIDs = explode(',',$error->_errors[0]['params'][0]);
            if ( self::isDuplicate($error) ) {
                if (count( $matchedIDs) >1) {
                    array_unshift($values,"Multiple matching contact records detected for this row. The participant was not imported");
                    return CRM_Event_Import_Parser::ERROR;
                } else {
                    $cid = $matchedIDs[0];
                    $formatted['contact_id'] = $cid;
                    $newParticipant = crm_create_contact_participant($formatted, $cid);
                    if ( is_a( $newParticipant, CRM_Core_Error ) ) {
                        array_unshift($values, $newParticipant->_errors[0]['message']);
                        return CRM_Event_Import_Parser::ERROR;
                    }
                    
                    $this->_newParticipants[] = $newParticipant->id;
                    return CRM_Event_Import_Parser::VALID;
                }
                
            } else {
                if ($this->_contactType == 'Individual') {
                    require_once 'CRM/Core/DAO/DupeMatch.php';
                    $dao = & new CRM_Core_DAO_DupeMatch();;
                    $dao->find(true);
                    $fieldsArray = explode('AND',$dao->rule);
                } elseif ($this->_contactType == 'Household') {
                    $fieldsArray = array('household_name', 'email');
                } elseif ($this->_contactType == 'Organization') {
                    $fieldsArray = array('organization_name', 'email');
                }
                foreach ( $fieldsArray as $value) {
                    if(array_key_exists(trim($value),$params)) {
                        $paramValue = $params[trim($value)];
                        if (is_array($paramValue)) {
                            $disp .= $params[trim($value)][0][trim($value)]." ";  
                        } else {
                            $disp .= $params[trim($value)]." ";
                        }
                    }
                }
                
                array_unshift($values,"No matching Contact found for (".$disp.")");
                return CRM_Event_Import_Parser::ERROR;
            }
            
        } else {
            $newParticipant = crm_create_contact_participant($formatted, $formatted['contact_id']);
            if ( is_a( $newParticipant, CRM_Core_Error ) ) {
                array_unshift($values, $newParticipant->_errors[0]['message']);
                return CRM_Event_Import_Parser::ERROR;
            }
            
            $this->_newParticipants[] = $newParticipant->id;
            return CRM_Event_Import_Parser::VALID;
        }
    }
    
    /**
     * Get the array of succesfully imported Participation ids
     *
     * @return array
     * @access public
     */
    function &getImportedParticipations()
    {
        return $this->_newParticipants;
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
     *  @param Object $error Avalid Error object
     *  
     *  @return ture if error is duplicate contact error 
     *  
     *  @access public 
     */
    function isDuplicate($error)
    {
        if( is_a( $error, CRM_Core_Error ) ) {
            $code = $error->_errors[0]['code'];
            if($code == CRM_Core_Error::DUPLICATE_CONTACT ) {
                return true ;
            }
        }
        return false;
    }
}
?>