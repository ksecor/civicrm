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

require_once 'CRM/Member/Form.php';
require_once 'CRM/Member/PseudoConstant.php';
require_once "CRM/Custom/Form/CustomData.php";
require_once "CRM/Core/BAO/CustomGroup.php";

/**
 * This class generates form components for Membership Type
 * 
 */
class CRM_Member_Form_Membership extends CRM_Member_Form
{

    public function preProcess()  
    {  
        //custom data related code
        $this->_cdType     = CRM_Utils_Array::value( 'type', $_GET );
        $this->assign('cdType', false);
        if ( $this->_cdType ) {
            $this->assign('cdType', true);
            return CRM_Custom_Form_CustomData::preProcess( $this );
        }
        
        // check for edit permission
        if ( ! CRM_Core_Permission::check( 'edit memberships' ) ) {
            CRM_Core_Error::fatal( ts( 'You do not have permission to access this page' ) );
        }
        
        // action
        $this->_action    = CRM_Utils_Request::retrieve( 'action', 'String',
                                                         $this, false, 'add' );
        $this->_id        = CRM_Utils_Request::retrieve( 'id', 'Positive',
                                                         $this );
        $this->_contactID = CRM_Utils_Request::retrieve( 'cid', 'Positive',
                                                         $this );
        
        if ( $this->_id ) {
            $this->_memType = CRM_Core_DAO::getFieldValue("CRM_Member_DAO_Membership",$this->_id,"membership_type_id");
        } 
        
        //check whether membership status present or not
        if ( $this->_action & CRM_Core_Action::ADD ) {
            CRM_Member_BAO_Membership::statusAvilability($this->_contactID);
        }
        
        // when custom data is included in this page
        if ( CRM_Utils_Array::value( "hidden_custom", $_POST ) ) {
            eval( 'CRM_Custom_Form_Customdata::preProcess( $this );' );
            eval( 'CRM_Custom_Form_Customdata::buildQuickForm( $this );' );
            eval( 'CRM_Custom_Form_Customdata::setDefaultValues( $this );' );
        }
        parent::preProcess( );
    }
    
    /**
     * This function sets the default values for the form. MobileProvider that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    public function setDefaultValues( ) 
    {
        
        if ( $this->_cdType ) {
            return CRM_Custom_Form_CustomData::setDefaultValues( $this );
        }
        
        $defaults = array( );
        $defaults =& parent::setDefaultValues( );
        
        //setting default join date and receive date
        if ($this->_action == CRM_Core_Action::ADD) {
            $default_dates = array( 'join_date', 'receive_date');
            foreach ( $default_dates as $set_date ) {
                $today_date = getDate();
                $defaults[$set_date]['M'] = $today_date['mon'];
                $defaults[$set_date]['d'] = $today_date['mday'];
                $defaults[$set_date]['Y'] = $today_date['year'];
            }
            
        }
        
        if( isset($this->_groupTree) ) {
            CRM_Core_BAO_CustomGroup::setDefaults( $this->_groupTree, $defaults, false, false );
        }
        
        if (is_numeric($this->_memType)) {
            $defaults["membership_type_id"] = array();
            $defaults["membership_type_id"][0] =  
                CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipType', 
                                             $this->_memType, 
                                             'member_of_contact_id', 
                                             'id' );
            $defaults["membership_type_id"][1] = $this->_memType;
        } else {
            $defaults["membership_type_id"]    =  $this->_memType;
        }
        
        if ( CRM_Utils_Array::value( 'id' , $defaults ) ) {
            $defaults['record_contribution'] = CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipPayment', 
                                                                            $defaults['id'], 
                                                                            'contribution_id', 
                                                                            'membership_id' );
        }
        
        if ( $this->_memType ) {
            $defaults['contribution_type_id'] = CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipType', 
                                                                             $this->_memType, 
                                                                             'contribution_type_id' );
            
            $defaults['total_amount'] = CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipType', 
                                                                     $this->_memType, 
                                                                     'minimum_fee' );
        }
        
        if ( CRM_Utils_Array::value( 'record_contribution', $defaults ) ) {
            $contributionParams   = array( 'id' => $defaults['record_contribution'] );
            $contributionIds      = array( );
            
            require_once "CRM/Contribute/BAO/Contribution.php";
            CRM_Contribute_BAO_Contribution::getValues( $contributionParams, $defaults, $contributionIds );
            
            // Contribution::getValues() over-writes the membership record's source field value - so we need to restore it.
            $defaults['source'] = $defaults['membership_source'];
        }
        
        if ( $this->_action & CRM_Core_Action::UPDATE ) {
            // in this mode by default uncheck this checkbox
            unset($defaults['record_contribution']);
            $defaults['send_receipt'] = 0; 
        } elseif ( $this->_action & CRM_Core_Action::ADD ) {
            $defaults['send_receipt'] = 1; 
        }
        if ( $defaults['membership_type_id'][1] ) {
            $defaults['receipt_text_signup'] =  CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipType', 
                                                                             $defaults['membership_type_id'][1],
                                                                             'receipt_text_signup' );
        }
        
        $this->assign( "member_is_test", CRM_Utils_Array::value('member_is_test',$defaults) );
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
        if ( $this->_cdType ) {
            return CRM_Custom_Form_CustomData::buildQuickForm( $this );
        }
        
        //need to assign custom data type and subtype to the template
        $this->assign('customDataType', 'Membership');
        $this->assign('customDataSubType',  $this->_memType );
        $this->assign('entityId',  $this->_id );
        
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            $this->addButtons(array( 
                                    array ( 'type'      => 'next', 
                                            'name'      => ts('Delete'), 
                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                            'isDefault' => true   ), 
                                    array ( 'type'      => 'cancel', 
                                            'name'      => ts('Cancel') ), 
                                    ) 
                              );
            return;
        }
        
        $selOrgMemType[0][0] = $selMemTypeOrg[0] = ts('- select -');
        
        $dao =& new CRM_Member_DAO_MembershipType();
        $dao->find();
        while ($dao->fetch()) {
            if ($dao->is_active) {
                if ( !CRM_Utils_Array::value($dao->member_of_contact_id,$selMemTypeOrg) ) {
                    $selMemTypeOrg[$dao->member_of_contact_id] = 
                        CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', 
                                                     $dao->member_of_contact_id, 
                                                     'display_name', 
                                                     'id' );
                    $selOrgMemType[$dao->member_of_contact_id][0] = ts('- select -');
                }                
                if ( !CRM_Utils_Array::value($dao->id,$selOrgMemType[$dao->member_of_contact_id]) ) {
                    $selOrgMemType[$dao->member_of_contact_id][$dao->id] = $dao->name;
                }
            }
        }
        
        // show organization by default, if only one organization in
        // the list 
        if ( count($selMemTypeOrg) == 2 ) {
            unset($selMemTypeOrg[0], $selOrgMemType[0][0]);
        }
        
        $sel =& $this->addElement('hierselect', 
                                  'membership_type_id', 
                                  ts('Membership Organization and Type'), 
                                  array('onChange' => "buildCustomData( this.value );")
                                  );
        
        $sel->setOptions(array($selMemTypeOrg,  $selOrgMemType));
        
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
        
        $this->add('date', 'join_date', ts('Join Date'), CRM_Core_SelectValues::date('activityDate'), false );         
        $this->addRule('join_date', ts('Select a valid date.'), 'qfDate');
        $this->add('date', 'start_date', ts('Start Date'), CRM_Core_SelectValues::date('activityDate'), false );         
        $this->addRule('start_date', ts('Select a valid date.'), 'qfDate');
        $this->add('date', 'end_date', ts('End Date'), CRM_Core_SelectValues::date('activityDate'), false );         
        $this->addRule('end_date', ts('Select a valid date.'), 'qfDate');
        
        $this->add('text', 'source', ts('Source'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Member_DAO_Membership', 'source' ) );
        $this->add('select', 'status_id', ts( 'Status' ), 
                   array(''=>ts( '- select -' )) + CRM_Member_PseudoConstant::membershipStatus( ) );
        
        $this->addElement('checkbox', 
                          'is_override', 
                          ts('Status Override?'), 
                          null, 
                          array( 'onClick' => 'showHideMemberStatus()'));
        
        $this->addElement('checkbox', 'record_contribution', ts('Record Membership Payment?'), null, 
                          array('onclick' =>"return showHideByValue('record_contribution','','recordContribution','table-row','radio',false);"));
        
        require_once 'CRM/Contribute/PseudoConstant.php';
        $this->add('select', 'contribution_type_id', 
                   ts( 'Contribution Type' ), 
                   array(''=>ts( '- select -' )) + CRM_Contribute_PseudoConstant::contributionType( ) );
        
        $this->add('text', 'total_amount', ts('Amount'));
        $this->addRule('total_amount', ts('Please enter a valid amount.'), 'money');
        
        $this->add('date', 'receive_date', ts('Received'), CRM_Core_SelectValues::date('activityDate'), false );         
        $this->addRule('receive_date', ts('Select a valid date.'), 'qfDate');
        $this->add('select', 'payment_instrument_id', 
                   ts( 'Paid By' ), 
                   array(''=>ts( '- select -' )) + CRM_Contribute_PseudoConstant::paymentInstrument( )
                   );
        $this->add('text', 'trxn_id', ts('Transaction ID'));
        $this->addRule( 'trxn_id', ts('Transaction ID already exists in Database.'),
                        'objectExists', array( 'CRM_Contribute_DAO_Contribution', $this->_id, 'trxn_id' ) );
        $this->add('select', 'contribution_status_id',
                   ts('Payment Status'), 
                   CRM_Contribute_PseudoConstant::contributionStatus( )
                   );
        
        $this->addElement('checkbox', 
                          'send_receipt', 
                          ts('Send Confirmation and Receipt?'), null, 
                          array('onclick' =>"return showHideByValue('send_receipt','','notice','table-row','radio',false);") );
        $this->add('textarea', 'receipt_text_signup', ts('Receipt Message') );
        
        // Retrieve the name and email of the contact - this will be the TO for receipt email
        require_once 'CRM/Contact/BAO/Contact/Location.php';
        list( $this->_contributorDisplayName, 
              $this->_contributorEmail ) = CRM_Contact_BAO_Contact_Location::getEmailDetails( $this->_contactID );
        $this->assign( 'emailExists', $this->_contributorEmail );
        $this->addFormRule(array('CRM_Member_Form_Membership', 'formRule'));
        
        parent::buildQuickForm( );
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
    public function formRule( &$params ) 
    {
        $errors = array( );
        if (!$params['membership_type_id'][1]) {
            $errors['membership_type_id'] = ts('Please select a membership type.');
        }
        
        $joinDate = CRM_Utils_Date::format( $params['join_date'] );
        if ( $joinDate ) {
            // if start date is set ensure that start date is later than or same as join date
            $startDate = CRM_Utils_Date::format( $params['start_date'] );
            if ( $startDate ) {
                if ( $startDate < $joinDate ) {
                    $errors['start_date'] = ts( 'Start date must be the same or later than join date.' );
                }
            }
            
            // if end date is set, ensure that start date is also set
            // and that end date is later than start date
            // If selected membership type has duration unit as 'lifetime'
            // and end date is set, then give error
            $endDate = CRM_Utils_Date::format( $params['end_date'] );
            if ( $endDate ) {
                require_once 'CRM/Member/BAO/MembershipType.php';
                $membershipDetails = CRM_Member_BAO_MembershipType::getMembershipTypeDetails( $params['membership_type_id'][1] );
                if ( $membershipDetails['duration_unit'] == 'lifetime' ) {
                    $errors['end_date'] = ts("The selected Membership Type has a 'life time' duration. You can not specify an End Date for 'life time' memberships. Please clear the End Date OR select a different Membership Type." );
                } else {
                    if ( ! $startDate ) {
                        $errors['start_date'] = ts( 'Start date must be set if end date is set.' );
                    }
                    if ( $endDate < $startDate ) {
                        $errors['end_date'] = ts('End date must be the same or later than start date.' );
                    }
                }
            }
        } else {
            $errors['join_date'] = ts('Please enter the join date.');
        }
        
        if ( isset( $params['is_override'] ) &&
             $params['is_override']          &&
             ! $params['status_id'] ) {
            $errors['status_id'] = ts('Please enter the status.');
        }
        
        if ( isset( $params['record_contribution'] ) && 
             ! isset( $params['contribution_type_id'] ) ) {
            $errors['contribution_type_id'] = ts('Please enter the contribution.');
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
        
        $fields = array( 
                        'status_id',
                        'source',
                        'is_override'
                        );
        
        foreach ( $fields as $f ) {
            $params[$f] = CRM_Utils_Array::value( $f, $formValues );
        }
        
        $params['membership_type_id'] = $formValues['membership_type_id'][1];
               
        $joinDate  = CRM_Utils_Date::mysqlToIso(CRM_Utils_Date::format( $formValues['join_date'] ));
        $startDate = CRM_Utils_Date::mysqlToIso(CRM_Utils_Date::format( $formValues['start_date'] ));
        $endDate   = CRM_Utils_Date::mysqlToIso(CRM_Utils_Date::format( $formValues['end_date'] ));
        
       
        $calcDates = CRM_Member_BAO_MembershipType::getDatesForMembershipType($params['membership_type_id'],
                                                                              $joinDate, $startDate, $endDate);
        
        $dates = array( 'join_date',
                        'start_date',
                        'end_date',
                        'reminder_date',
                        'receive_date'
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
        
        if ( $this->_id ) {
            $ids['membership'] = $params['id'] = $this->_id;
        }
        
        $session = CRM_Core_Session::singleton();
        $ids['userId'] = $session->get('userID');
     
        //format custom data
        // get mime type of the uploaded file
        if ( !empty($_FILES) ) {
            foreach ( $_FILES as $key => $value) {
                $files = array( );
                if ( $formValues[$key] ) {
                    $files['name'] = $formValues[$key];
                }
                if ( $value['type'] ) {
                    $files['type'] = $value['type']; 
                }
                $formValues[$key] = $files;
            }
        }

        $customData = array( );
        foreach ( $formValues as $key => $value ) {
            if ( $customFieldId = CRM_Core_BAO_CustomField::getKeyID($key) ) {
                CRM_Core_BAO_CustomField::formatCustomField( $customFieldId, $customData, $value, 'Membership', null, $this->_id);
            }
        }
        
        if (! empty($customData) ) {
            $params['custom'] = $customData;
        }
        
        //special case to handle if all checkboxes are unchecked
        $customFields = CRM_Core_BAO_CustomField::getFields( 'Membership' );

        if ( !empty($customFields) ) {
            foreach ( $customFields as $k => $val ) {
                if ( in_array ( $val[3], array ('CheckBox','Multi-Select') ) &&
                     ! CRM_Utils_Array::value( $k, $params['custom'] ) ) {
                    CRM_Core_BAO_CustomField::formatCustomField( $k, $params['custom'],
                                                                 '', 'Membership', null, $this->_id);
                }
            }
        }
        if ( $formValues['record_contribution'] ) {
            $recordContribution = array(
                                        'total_amount',
                                        'contribution_type_id', 
                                        'payment_instrument_id',
                                        'trxn_id',
                                        'contribution_status_id'
                                        );

            foreach ( $recordContribution as $f ) {
                $params[$f] = CRM_Utils_Array::value( $f, $formValues );
            }
           
            // Retrieve the name and email of the current user - this will be the FROM for the receipt email
            require_once 'CRM/Contact/BAO/Contact/Location.php';
            list( $userName, $userEmail ) = CRM_Contact_BAO_Contact_Location::getEmailDetails( $ids['userId'] );
            $membershipType = CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipType',
                                                           $formValues['membership_type_id'][1] );
            $params['contribution_source'] = "{$membershipType} Membership: Offline membership signup (by {$userName})";
            
            if ( $formValues['send_receipt'] ) {
                $params['receipt_date'] = $params['receive_date'];
            }
        }
        
        $membership =& CRM_Member_BAO_Membership::create( $params, $ids );
        if ( $formValues['send_receipt'] ) {
            require_once 'CRM/Core/DAO.php';
            CRM_Core_DAO::setFieldValue( 'CRM_Member_DAO_MembershipType', 
                                         $params['membership_type_id'], 
                                         'receipt_text_signup',
                                         $formValues['receipt_text_signup'] );
        }

        $relatedContacts = array( );
        if ( ! is_a( $membership, 'CRM_Core_Error') ) {
            $relatedContacts = CRM_Member_BAO_Membership::checkMembershipRelationship( 
                                                                                      $membership->id,
                                                                                      $membership->contact_id,
                                                                                      $this->_action
                                                                                      );
        }
        
        if ( ! empty($relatedContacts) ) {
            // delete all the related membership records before creating
            CRM_Member_BAO_Membership::deleteRelatedMemberships( $membership->id );
            
            // Edit the params array
            unset( $params['id'] );
            // Reminder should be sent only to the direct membership
            unset( $params['reminder_date'] );
            // unset the custom value ids
            if ( is_array( $params['custom'] ) ) {
                foreach ( $params['custom'] as $k => $v ) {
                    unset( $params['custom'][$k]['id'] );
                }
            }
            
            foreach ( $relatedContacts as $contactId => $relationshipStatus ) {
                $params['contact_id'         ] = $contactId;
                $params['owner_membership_id'] = $membership->id;
                // set status_id as it might have been changed for
                // past relationship
                $params['status_id'          ] = $membership->status_id;
                
                if ( ( $this->_action & CRM_Core_Action::UPDATE ) && 
                     ( $relationshipStatus == CRM_Contact_BAO_Relationship::PAST ) ) {
                    // FIXME : While updating/ renewing the
                    // membership, if the relationship is PAST then
                    // the membership of the related contact must be
                    // expired. 
                    // For that, getting Membership Status for which
                    // is_current_member is 0. It works for the
                    // generated data as there is only one membership
                    // status having is_current_member = 0.
                    // But this wont work exactly if there will be
                    // more than one status having is_current_member = 0.
                    $params['status_id'] = CRM_Core_DAO::getFieldValue('CRM_Member_DAO_MembershipStatus', '0', 'id', 'is_current_member' );
                }
                $relatedMembership = CRM_Member_BAO_Membership::create( $params, CRM_Core_DAO::$_nullArray );
            }
        }

        $receiptSend = false;
        if ( $formValues['record_contribution'] && $formValues['send_receipt'] ) {
            $receiptSend = true;
            $receiptFrom = '"' . $userName . '" <' . $userEmail . '>';
            $paymentInstrument = CRM_Contribute_PseudoConstant::paymentInstrument();
            $formValues['paidBy'] = $paymentInstrument[$formValues['payment_instrument_id']];

            // retrieve custom data
            require_once "CRM/Core/BAO/UFGroup.php";
            $customFields = $customValues = array( );
            foreach ( $this->_groupTree as $groupID => $group ) {
                if ( $groupID == 'info' ) {
                    continue;
                }
                foreach ( $group['fields'] as $k => $field ) {
                    $field['title'] = $field['label'];
                    $customFields["custom_{$k}"] = $field;
                }
            }
            
            CRM_Core_BAO_UFGroup::getValues( $this->_contactID, $customFields, $customValues , false, 
                                             array( array( 'member_id', '=', $membership->id, 0, 0 ) ) );
            $this->assign( 'module', 'Membership' );
            $this->assign( 'subject', ts('Membership Confirmation and Receipt') );
            $this->assign( 'receive_date', $params['receive_date'] );            
            $this->assign( 'formValues', $formValues );
            $this->assign( 'mem_start_date', CRM_Utils_Date::customFormat($calcDates['start_date']) );
            $this->assign( 'mem_end_date', CRM_Utils_Date::customFormat($calcDates['end_date']) );
            $this->assign( 'membership_name', CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipType',
                                                                           $formValues['membership_type_id'][1] ) );
            $this->assign( 'customValues', $customValues );
            $template =& CRM_Core_Smarty::singleton( );
            $subject = trim( $template->fetch( 'CRM/Contribute/Form/ReceiptSubjectOffline.tpl' ) );
            $message = $template->fetch( 'CRM/Contribute/Form/ReceiptMessageOffline.tpl' );

            require_once 'CRM/Utils/Mail.php';
            CRM_Utils_Mail::send( $receiptFrom,
                                  $this->_contributorDisplayName,
                                  $this->_contributorEmail,
                                  $subject,
                                  $message);
        }
        
        if ( ( $this->_action & CRM_Core_Action::UPDATE ) ) {
            $statusMsg = ts('Membership for %1 has been updated.', array(1 => $this->_contributorDisplayName));
            if ( $endDate ) {
                $endDate=CRM_Utils_Date::customFormat($endDate);
                $statusMsg .= ' '.ts('The membership End Date is %1.', array(1 => $endDate));
            }
            if ( $receiptSend ) {
                $statusMsg .= ' '.ts('A confirmation for membership updation and receipt has been sent to %1.', array(1 => $this->_contributorEmail));
            }
        } elseif ( ( $this->_action & CRM_Core_Action::ADD ) ) {
            require_once 'CRM/Core/DAO.php';
            $memType = CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipType',
                                                    $params['membership_type_id'] );
            $statusMsg = ts('%1 membership for %2 has been added.', array(1 => $memType, 2 => $this->_contributorDisplayName));
            if ( $endDate ) {
                $endDate=CRM_Utils_Date::customFormat($endDate);
                $statusMsg .= ' '.ts('The new membership End Date is %1.', array(1 => $endDate));
            }
            if ( $receiptSend ) {
                 $statusMsg .= ' '.ts('A membership confirmation and receipt has been sent to %1.', array(1 => $this->_contributorEmail));
            }
        }
        CRM_Core_Session::setStatus($statusMsg);
    }
}

