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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Member/Form.php';
require_once 'CRM/Member/PseudoConstant.php';

/**
 * This class generates form components for Membership Type
 * 
 */
class CRM_Member_Form_Membership extends CRM_Member_Form
{

    public function preProcess()  
    {  
        // check for edit permission
        if ( ! CRM_Core_Permission::check( 'edit memberships' ) ) {
            CRM_Core_Error::fatal( ts( 'You do not have permission to access this page' ) );
        }

        // action
        $this->_action = CRM_Utils_Request::retrieve( 'action', 'String',
                                                      $this, false, 'add' );
        $this->_id     = CRM_Utils_Request::retrieve( 'id', 'Positive',
                                                      $this );
        $this->_contactID = CRM_Utils_Request::retrieve( 'cid', 'Positive',
                                                         $this );
       
        $this->_memType = CRM_Utils_Request::retrieve( 'subType', 'Positive',
                                                       $this );

        if ( ! $this->_memType ) {
            if ( $this->_id ) {
                $this->_memType = CRM_Core_DAO::getFieldValue("CRM_Member_DAO_Membership",$this->_id,"membership_type_id");
            } else {
                $this->_memType = "Membership";
            }
        }     
    
        //check whether membership status present or not
        if ( $this->_action & CRM_Core_Action::ADD ) {
            CRM_Member_BAO_Membership::statusAvilability($this->_contactID);
        }

        //get the group Tree
        $this->_groupTree =& CRM_Core_BAO_CustomGroup::getTree( 'Membership', $this->_id, false,$this->_memType);
        CRM_Core_BAO_CustomGroup::buildQuickForm( $this, $this->_groupTree, 'showBlocks1', 'hideBlocks1' );

        parent::preProcess( );
    }

    /**
     * This function sets the default values for the form. MobileProvider that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    public function setDefaultValues( ) {
        $defaults = array( );
        $defaults =& parent::setDefaultValues( );
        
        //setting default join date
        if ($this->_action == CRM_Core_Action::ADD) {
            $joinDate = getDate();
            $defaults['join_date']['M'] = $joinDate['mon'];
            $defaults['join_date']['d'] = $joinDate['mday'];
            $defaults['join_date']['Y'] = $joinDate['year'];
        }
        if( isset($this->_groupTree) ) {
            CRM_Core_BAO_CustomGroup::setDefaults( $this->_groupTree, $defaults, false, false );
        }
        $defaults["membership_type_id"] =  $this->_memType;
        return $defaults;
    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        parent::buildQuickForm( );
        if ($this->_action & CRM_Core_Action::DELETE ) { 
            return;
        }

        $urlParams = "reset=1&cid={$this->_contactID}&context=membership";
        if ( $this->_id ) {
            $urlParams .= "&action=update&id={$this->_id}";
        } else {
            $urlParams .= "&action=add";
        }

        $url = CRM_Utils_System::url('civicrm/contact/view/membership',
                                     $urlParams, true, null, false ); 
        $this->assign("refreshURL",$url);

        $this->applyFilter('__ALL__', 'trim');

        $this->add('select', 'membership_type_id',ts( 'Membership Type' ), 
                   array(''=>ts( '-select-' )) + CRM_Member_PseudoConstant::membershipType( ), true,
                   array('onChange' => "if (this.value) reload(true); else return false")
                   );
               
        $this->add('date', 'join_date', ts('Join Date'), CRM_Core_SelectValues::date('manual', 20, 1), false );         
        $this->addRule('join_date', ts('Select a valid date.'), 'qfDate');
        $this->add('date', 'start_date', ts('Start Date'), CRM_Core_SelectValues::date('manual', 20, 1), false );         
        $this->addRule('start_date', ts('Select a valid date.'), 'qfDate');
        $this->add('date', 'end_date', ts('End Date'), CRM_Core_SelectValues::date('manual', 20, 5), false );         
        $this->addRule('end_date', ts('Select a valid date.'), 'qfDate');
        
        $this->add('text', 'source', ts('Source'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Member_DAO_Membership', 'source' ) );
        $this->add('select', 'status_id', ts( 'Status' ), 
                   array(''=>ts( '-select-' )) + CRM_Member_PseudoConstant::membershipStatus( ) );

        $this->addElement('checkbox', 'is_override', ts('Status Hold?'), null, array( 'onChange' => 'showHideMemberStatus()'));

        $this->addFormRule(array('CRM_Member_Form_Membership', 'formRule'));
    }

    /**
     * Function for validation
     *
     * @param array $params (ref.) an assoc array of name/value pairs
     *
     * @return mixed true or array of errors
     * @access public
     * @static
     */
    public function formRule( &$params ) {
        $errors = array( );

        if ( !($params['join_date']['M'] && $params['join_date']['d'] && $params['join_date']['Y']) ) {
            $errors['join_date'] = "Please enter the Join Date.";
        }
        if ( isset( $params['is_override'] ) &&
             $params['is_override']          &&
             ! $params['status_id'] ) {
            $errors['status_id'] = "Please enter the status.";
        }
              
        return empty($errors) ? true : $errors;
    }
       
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        require_once 'CRM/Member/BAO/Membership.php';
        require_once 'CRM/Member/BAO/MembershipType.php';
        require_once 'CRM/Member/BAO/MembershipStatus.php';

        if ( $this->_action & CRM_Core_Action::DELETE ) {
            CRM_Member_BAO_Membership::deleteRelatedMemberships( $this->_id );
            CRM_Member_BAO_Membership::deleteMembership( $this->_id );
            return;
        }
        
        
        // get the submitted form values.  
        $formValues = $this->controller->exportValues( $this->_name );
        
        $params = array( );
        $ids    = array( );

        $params['contact_id'] = $this->_contactID;
        
        $fields = array( 'membership_type_id',
                         'status_id',
                         'source',
                         'is_override'
                         );
        
        foreach ( $fields as $f ) {
            $params[$f] = CRM_Utils_Array::value( $f, $formValues );
            
        }
       
        $joinDate = CRM_Utils_Date::mysqlToIso(CRM_Utils_Date::format( $formValues['join_date'] ));
        $calcDates = CRM_Member_BAO_MembershipType::getDatesForMembershipType($params['membership_type_id'], $joinDate);
        
        $dates = array( 'join_date',
                        'start_date',
                        'end_date',
                        'reminder_date'
                        );
        $currentTime = getDate();        
        foreach ( $dates as $d ) {
            if ( isset( $formValues[$d] ) &&
                 ! CRM_Utils_System::isNull( $formValues[$d] ) ) {
                $params[$d] = CRM_Utils_Date::format( $formValues[$d] );
            } else if ( isset( $calcDates[$d] ) ) {
                $params[$d] = CRM_Utils_Date::isoToMysql($calcDates[$d]);
            }
        }

        // change reminder date if end-date present
        if ( ! CRM_Utils_System::isNull( $formValues['end_date'] ) ) {
            $membershipTypeDetails = CRM_Member_BAO_MembershipType::getMembershipTypeDetails( $formValues['membership_type_id'] );
            if ( isset( $membershipTypeDetails["renewal_reminder_day"] ) &&
                 $membershipTypeDetails["renewal_reminder_day"] ) {
                $year  = $formValues['end_date']['Y'];
                $month = $formValues['end_date']['M'];
                $day   = $formValues['end_date']['d'];
                $day = $day - $membershipTypeDetails["renewal_reminder_day"];
                $params['reminder_date'] = str_replace('-', "", date('Y-m-d',mktime($hour, $minute, $second, $month, $day-1, $year)));
            }
        }
        
        if ( !$params['is_override'] ) {
            $startDate  = CRM_Utils_Date::customFormat($params['start_date'],'%Y-%m-%d');
            $endDate    = CRM_Utils_Date::customFormat($params['end_date'],'%Y-%m-%d');
            $calcStatus = CRM_Member_BAO_MembershipStatus::getMembershipStatusByDate( $startDate, $endDate, $joinDate );
            //CRM_Core_Error::debug('calcStatus', $calcStatus);
            if (empty($calcStatus)){
                CRM_Core_Session::setStatus( ts('The membership can not be saved.<br/> No valid membership status for given dates.') );
                return CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/contact/view', "reset=1&force=1&cid={$this->_contactID}&selectedChild=member"));
            }
            $params['status_id'] = $calcStatus['id'];
            
        }
        
        $ids['membership'] = $params['id'] = $this->_id;
        
        $session = CRM_Core_Session::singleton();
        $ids['userId'] = $session->get('userID');
        
        $membership =& CRM_Member_BAO_Membership::create( $params, $ids );
        
        if ( ! is_a( $membership, 'CRM_Core_Error') ) {
            $relatedContacts = CRM_Member_BAO_Membership::checkMembershipRelationship( 
                                                                                      $membership->id,
                                                                                      $membership->contact_id
                                                                                      );
        }
        
        //delete all the related membership records before creating
        CRM_Member_BAO_Membership::deleteRelatedMemberships( $membership->id );
        
        if ( ! empty($relatedContacts) ) {
            foreach ( $relatedContacts as $contactId ) {
                $params['contact_id'         ] = $contactId;
                $params['owner_membership_id'] = $membership->id;
                unset( $params['id'] );
                
                CRM_Member_BAO_Membership::create( $params, CRM_Core_DAO::$_nullArray );
            }
        }
        
        // do the updates/inserts
        CRM_Core_BAO_CustomGroup::postProcess( $this->_groupTree, $formValues );
        CRM_Core_BAO_CustomGroup::updateCustomData($this->_groupTree, 'Membership', $membership->id);

        CRM_Core_Session::setStatus( ts('The membership information has been saved.') );
    }
}
?>