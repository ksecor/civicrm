<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 * @copyright CiviCRM LLC (c) 2004-2006
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
    private $_totalAmountIndex;
    private $_membershipTypeIndex;
    private $_membershipStatusIndex;

    //protected $_mapperLocType;
    //protected $_mapperPhoneType;
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
        //$this->_mapperLocType =& $mapperLocType;
        //$this->_mapperPhoneType =& $mapperPhoneType;
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
        
        $this->_newMemberships = array();
        
        $this->setActiveFields( $this->_mapperKeys );
        //$this->setActiveFieldLocationTypes( $this->_mapperLocType );
        //$this->setActiveFieldPhoneTypes( $this->_mapperPhoneType );
        
        // FIXME: we should do this in one place together with Form/MapField.php
        $this->_contactIdIndex        = -1;
        $this->_membershipTypeIndex   = -1;
        $this->_membershipStatusIndex = -1;
        
        $index = 0;
        foreach ( $this->_mapperKeys as $key ) {
            switch ($key) {
            case 'contact_id':
                $this->_contactIdIndex        = $index;
                break;
            case 'membership_type_id':
                $this->_membershipTypeIndex   = $index;
                break;
            case 'status_id':
                $this->_membershipStatusIndex   = $index;
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

        if (($this->_membershipTypeIndex < 0) || ($this->_membershipStatusIndex < 0)) {
            $errorRequired = true;
        } else {
            $errorRequired = ! CRM_Utils_Array::value($this->_membershipTypeIndex, $values) ||
                ! CRM_Utils_Array::value($this->_membershipStatusIndex, $values);
        }
        
        if ($errorRequired) {
            array_unshift($values, ts('Missing required fields'));
            return CRM_Event_Import_Parser::ERROR;
        }

        $params =& $this->getActiveFieldParams( );

        require_once 'CRM/Import/Parser/Contact.php';
        $errorMessage = null;
        
        //for date-Formats
        $session =& CRM_Core_Session::singleton();
        $dateType = $session->get("dateTypes");
        foreach ($params as $key => $val) {
            if( $val ) {
                switch( $key ) {
                case  'join_date': 
                    CRM_Utils_Date::convertToDefaultDate( $params, $dateType, $key );
                    if (! CRM_Utils_Rule::date($params[$key])) {
                        CRM_Import_Parser_Contact::addToErrorMsg('Join Date', $errorMessage);
                    }
                    break;
                case  'membership_start_date': 
                    CRM_Utils_Date::convertToDefaultDate( $params, $dateType, $key );
                    if (! CRM_Utils_Rule::date($params[$key])) {
                        CRM_Import_Parser_Contact::addToErrorMsg('Start Date', $errorMessage);
                    }
                    break;
                case  'membership_end_date': 
                    CRM_Utils_Date::convertToDefaultDate( $params, $dateType, $key );
                    if (! CRM_Utils_Rule::date($params[$key])) {
                        CRM_Import_Parser_Contact::addToErrorMsg('End date', $errorMessage);
                    }
                    break;
                }
            }
        }
        //date-Format part ends

        $params['contact_type'] =  $this->_contactType;

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
        $dateType = $session->get("dateTypes");
        
        foreach ($params as $key => $val) {
            if( $val ) {
                switch( $key ) {
                case  'join_date': 
                    CRM_Utils_Date::convertToDefaultDate( $params, $dateType, $key );
                    break;
                case  'membership_start_date': 
                    CRM_Utils_Date::convertToDefaultDate( $params, $dateType, $key );
                    break;
                case  'membership_end_date': 
                    CRM_Utils_Date::convertToDefaultDate( $params, $dateType, $key );
                    break;
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

            if ($key == 'membership_type_id') {
                $id = CRM_Core_DAO::getFieldValue( "CRM_Event_DAO_MembershipType", $field, 'id', 'name' );
                $formatted[$key] = $id;
            } elseif ($key == 'status_id') {
                $id = CRM_Core_DAO::getFieldValue( "CRM_Event_DAO_MembershipStatus", $field, 'id', 'name' );
                $formatted[$key] = $id;
            } else {
                $formatted[$key] = $field;
            }
        }
        
        // BAO_Membership::add() handles only start_date and end_date.
        // So if $formatted contains membership_start_date or
        // membership_end_date convert it to start_date or end_date
        // respectively. also membership_source to source
        if ( isset($formatted['membership_start_date']) ) {
            $formatted['start_date'] = $formatted['membership_start_date'];
            unset($formatted['membership_start_date']);
        } 

        if ( isset($formatted['membership_end_date'])) {
            $formatted['end_date'] = $formatted['membership_end_date'];
            unset($formatted['membership_end_date']);
        }  
        
        if ( isset($formatted['membership_source'])) {
            $formatted['source'] = $formatted['membership_source'];
            unset($formatted['membership_source']);
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
                    array_unshift($values,"Multiple matching contact records detected for this row. The membership was not imported");
                    return CRM_Event_Import_Parser::ERROR;
                } else {
                    $cid = $matchedIDs[0];
                    $formatted['contact_id'] = $cid;
                    $newMembership = crm_create_contact_membership($formatted, $cid);
                    if ( is_a( $newMembership, CRM_Core_Error ) ) {
                        array_unshift($values, $newMembership->_errors[0]['message']);
                        return CRM_Event_Import_Parser::ERROR;
                    }
                    
                    $this->_newMemberships[] = $newMembership->id;
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
            $newMembership = crm_create_contact_membership($formatted, $formatted['contact_id']);
            if ( is_a( $newMembership, CRM_Core_Error ) ) {
                array_unshift($values, $newMembership->_errors[0]['message']);
                return CRM_Event_Import_Parser::ERROR;
            }
            
            $this->_newMemberships[] = $newMembership->id;
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
        return $this->_newMemberships;
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