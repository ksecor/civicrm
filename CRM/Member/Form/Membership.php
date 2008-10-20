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
    protected $_memType =null;

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
            $this->_memType = CRM_Core_DAO::getFieldValue( "CRM_Member_DAO_Membership", $this->_id, 
                                                           "membership_type_id");
        } 
        $this->_mode      = CRM_Utils_Request::retrieve( 'mode', 'String', $this );
       
        if ( $this->_mode ) {
            $this->assign( 'membershipMode', $this->_mode );
            
            $this->_paymentProcessor = array( 'billing_mode' => 1 );
            $validProcessors = array( );
            $processors = CRM_Core_PseudoConstant::paymentProcessor( false, false, "billing_mode IN ( 1, 3 )" );
            
            foreach ( $processors as $ppID => $label ) {
                require_once 'CRM/Core/BAO/PaymentProcessor.php';
                require_once 'CRM/Core/Payment.php';
                $paymentProcessor =& CRM_Core_BAO_PaymentProcessor::getPayment( $ppID, $this->_mode );
                if ( $paymentProcessor['payment_processor_type'] == 'PayPal' && !$paymentProcessor['user_name'] ) {
                    continue;
                } else if ( $paymentProcessor['payment_processor_type'] == 'Dummy' && $this->_mode == 'live' ) {
                    continue;
                } else {
                    $paymentObject =& CRM_Core_Payment::singleton( $this->_mode, 'Contribute', $paymentProcessor );
                    $error = $paymentObject->checkConfig( );
                    if ( empty( $error ) ) {
                        $validProcessors[$ppID] = $label;
                    }
                    $paymentObject = null;
                }
            }
            if ( empty( $validProcessors )  ) {
                CRM_Core_Error::fatal( ts( 'Could not find valid payment processor for this page' ) );
            } else {
                $this->_processors = $validProcessors;  
            }
            // also check for billing information
            // get the billing location type
            $locationTypes =& CRM_Core_PseudoConstant::locationType( );
            $this->_bltID = array_search( 'Billing',  $locationTypes );
            if ( ! $this->_bltID ) {
                CRM_Core_Error::fatal( ts( 'Please set a location type of %1', array( 1 => 'Billing' ) ) );
            }
            $this->set   ( 'bltID', $this->_bltID );
            $this->assign( 'bltID', $this->_bltID );
                       
            $this->_fields = array( );
            
            require_once 'CRM/Core/Payment/Form.php';
            CRM_Core_Payment_Form::setCreditCardFields( $this );
        }
        
        //check whether membership status present or not
        if ( $this->_action & CRM_Core_Action::ADD ) {
            CRM_Member_BAO_Membership::statusAvilability($this->_contactID);
        }
        
        // when custom data is included in this page
        if ( CRM_Utils_Array::value( "hidden_custom", $_POST ) ) {
            CRM_Custom_Form_Customdata::preProcess( $this );
            CRM_Custom_Form_Customdata::buildQuickForm( $this );
            CRM_Custom_Form_Customdata::setDefaultValues( $this );
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
            $now = date("Y-m-d");
            $defaults['join_date'] = $now;
            $defaults['receive_date'] =$now;
        }
        
        if ( is_numeric( $this->_memType ) ) {
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
        
        if ( CRM_Utils_Array::value( 'record_contribution', $defaults ) && ! $this->_mode ) {
            $contributionParams   = array( 'id' => $defaults['record_contribution'] );
            $contributionIds      = array( );
            
            require_once "CRM/Contribute/BAO/Contribution.php";
            CRM_Contribute_BAO_Contribution::getValues( $contributionParams, $defaults, $contributionIds );
            
            // Contribution::getValues() over-writes the membership record's source field value - so we need to restore it.
            if ( CRM_Utils_Array::value( 'membership_source', $defaults ) ) {
                $defaults['source'] = $defaults['membership_source'];
            }
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
        
        $this->assign( 'membership_status_id', CRM_Utils_Array::value('status_id',$defaults) );
        
        if ( CRM_Utils_Array::value( 'is_pay_later', $defaults) ) {
            $this->assign( 'is_pay_later', true ); 
        }
        if ( $this->_mode ) {
            $fields = array( );
            
            foreach ( $this->_fields as $name => $dontCare ) {
                $fields[$name] = 1;
            }
            
            $names = array("first_name", "middle_name", "last_name" );
            foreach ($names as $name) {
                $fields[$name] = 1;
            }
            
            $fields["state_province-{$this->_bltID}"] = 1;
            $fields["country-{$this->_bltID}"       ] = 1;
            $fields["email-{$this->_bltID}"         ] = 1;
            $fields["email-Primary"                 ] = 1;
            
            require_once "CRM/Core/BAO/UFGroup.php";
            CRM_Core_BAO_UFGroup::setProfileDefaults( $this->_contactID, $fields, $this->_defaults );
                                 
            $defaultAddress = array("street_address-5","city-5", "state_province_id-5", "country_id-5","postal_code-5" );
            foreach ($defaultAddress as $name) {
                if ( ! empty( $this->_defaults[$name] ) ) {
                    $defaults[$name] = $this->_defaults[$name];
                }
            } 
            if ( empty( $this->_defaults["email-{$this->_bltID}"] ) && ! empty( $this->_defaults["email-Primary"] ) ) {
                $defaults["email-{$this->_bltID}"] = $this->_defaults["email-Primary"];
            }
            
            foreach ($names as $name) {
                if ( ! empty( $this->_defaults[$name] ) ) {
                    $defaults["billing_" . $name] = $this->_defaults[$name];
                }
            } 
        }
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
                if ( $this->_mode && ! $dao->minimum_fee ) {
                    continue;
                } else {
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
        }

        // show organization by default, if only one organization in
        // the list 
        if ( count($selMemTypeOrg) == 2 ) {
            unset($selMemTypeOrg[0], $selOrgMemType[0][0]);
        }
        
        $sel =& $this->addElement('hierselect', 
                                  'membership_type_id', 
                                  ts('Membership Organization and Type'), 
                                  array('onChange' => "buildCustomData( this.value ); setPaymentBlock( this.value );")
                                  );
        
        $sel->setOptions(array($selMemTypeOrg,  $selOrgMemType));
        
        $this->applyFilter('__ALL__', 'trim');
        
        $this->add('date', 'join_date', ts('Join Date'), CRM_Core_SelectValues::date('activityDate'), false );         
        $this->addRule('join_date', ts('Select a valid date.'), 'qfDate');
        $this->add('date', 'start_date', ts('Start Date'), CRM_Core_SelectValues::date('activityDate'), false );         
        $this->addRule('start_date', ts('Select a valid date.'), 'qfDate');
        $this->add('date', 'end_date', ts('End Date'), CRM_Core_SelectValues::date('activityDate'), false );         
        $this->addRule('end_date', ts('Select a valid date.'), 'qfDate');
        
        $this->add('text', 'source', ts('Source'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Member_DAO_Membership', 'source' ) );
        
        if ( !$this->_mode ) {
            $this->add('select', 'status_id', ts( 'Status' ), 
                       array(''=>ts( '- select -' )) + CRM_Member_PseudoConstant::membershipStatus( ) );
            $this->addElement('checkbox', 'is_override', 
                              ts('Status Override?'), null, 
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
        }
        $this->addElement('checkbox', 
                          'send_receipt', 
                          ts('Send Confirmation and Receipt?'), null, 
                          array('onclick' =>"return showHideByValue('send_receipt','','notice','table-row','radio',false);") );
        $this->add('textarea', 'receipt_text_signup', ts('Receipt Message') );
        if ( $this->_mode ) {
        
            $this->add( 'select', 'payment_processor_id',
                        ts( 'Payment Processor' ),
                        $this->_processors, true );
            require_once 'CRM/Core/Payment/Form.php';
            CRM_Core_Payment_Form::buildCreditCard( $this, true );
        }
        
        // Retrieve the name and email of the contact - this will be the TO for receipt email
        require_once 'CRM/Contact/BAO/Contact/Location.php';
        list( $this->_contributorDisplayName, 
              $this->_contributorEmail ) = CRM_Contact_BAO_Contact_Location::getEmailDetails( $this->_contactID );
        
        $this->assign( 'emailExists', $this->_contributorEmail );
        $this->addFormRule(array('CRM_Member_Form_Membership', 'formRule'), $this );
        require_once "CRM/Core/BAO/Preferences.php";
        $mailingInfo =& CRM_Core_BAO_Preferences::mailingPreferences();
        $this->assign( 'outBound_option', $mailingInfo['outBound_option'] );
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
    public function formRule( &$params, &$files, $self ) 
    {
        $errors = array( );
        if (!$params['membership_type_id'][1]) {
            $errors['membership_type_id'] = ts('Please select a membership type.');
        }
        if ( $params['membership_type_id'][1] && CRM_Utils_Array::value( 'payment_processor_id', $params ) ) {
            // make sure that credit card number and cvv are valid
            require_once 'CRM/Utils/Rule.php';
            if ( CRM_Utils_Array::value( 'credit_card_type', $params ) ) {
                if ( CRM_Utils_Array::value( 'credit_card_number', $params ) &&
                     ! CRM_Utils_Rule::creditCardNumber( $params['credit_card_number'], $params['credit_card_type'] ) ) {
                    $errors['credit_card_number'] = ts( "Please enter a valid Credit Card Number" );
                }
                
                if ( CRM_Utils_Array::value( 'cvv2', $params ) &&
                     ! CRM_Utils_Rule::cvv( $params['cvv2'], $params['credit_card_type'] ) ) {
                    $errors['cvv2'] =  ts( "Please enter a valid Credit Card Verification Number" );
                }
            }
        }
        
        $joinDate = CRM_Utils_Date::format( $params['join_date'] );
        if ( $joinDate ) {
            require_once 'CRM/Member/BAO/MembershipType.php';
            $membershipDetails = CRM_Member_BAO_MembershipType::getMembershipTypeDetails( $params['membership_type_id'][1] );
            
            $startDate = CRM_Utils_Date::format( $params['start_date'] );
            if ( $startDate && $membershipDetails['period_type'] == 'rolling' ) {
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
                if ( $membershipDetails['duration_unit'] == 'lifetime' ) {
                    $errors['end_date'] = ts('The selected Membership Type has a lifetime duration. You cannot specify an End Date for lifetime memberships. Please clear the End Date OR select a different Membership Type.');
                } else {
                    if ( ! $startDate ) {
                        $errors['start_date'] = ts( 'Start date must be set if end date is set.' );
                    }
                    if ( $endDate < $startDate ) {
                        $errors['end_date'] = ts('End date must be the same or later than start date.' );
                    }
                }
            }
            
            //CRM-3724, check for availability of valid membership status.
            if ( !CRM_Utils_Array::value( 'is_override',  $params ) ) {
                require_once 'CRM/Member/BAO/MembershipStatus.php';
                $calcStatus = CRM_Member_BAO_MembershipStatus::getMembershipStatusByDate( $startDate, 
                                                                                          $endDate, 
                                                                                          $joinDate, 
                                                                                          'today', 
                                                                                          true );
                if ( empty( $calcStatus ) ) {
                    $url = CRM_Utils_System::url( 'civicrm/admin/member/membershipStatus', 'reset=1&action=browse' );
                    $errors['_qf_default'] = ts( 'There is no valid Membership Status available for selected membership dates.' );
                    $status = ts( "Oops it looks like there is no valid Membership status available for given Membership dates. You can configure Membership Status Rules here <a href='%1'>Configure Membership Status Rules.</a>",  array( 1 => $url ) );
                    if ( !$self->_mode ) { 
                        $status .= ts( ' OR You can sign up by selecting Status Override? equal to true.' );
                    }
                    CRM_Core_Session::setStatus( $status );
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
        
        //total amount condition arise when membership type having no
        //minimum fee
        if ( isset( $params['record_contribution'] ) ) { 
            if ( ! $params['contribution_type_id'] ) {
                $errors['contribution_type_id'] = ts('Please enter the contribution Type.');
            } 
            if ( !$params['total_amount'] ) {
                $errors['total_amount'] = ts('Please enter the contribution.'); 
            }
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
        $config =& CRM_Core_Config::singleton(); 
        // get the submitted form values.  
        $this->_params = $formValues = $this->controller->exportValues( $this->_name );
              
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
        
        // fix for CRM-3724
        // when is_override false ignore is_admin statuses during membership 
        // status calculation. similarly we did fix for import in CRM-3570. 
        if ( !CRM_Utils_Array::value( 'is_override', $params ) ) {
            $params['exclude_is_admin'] = true;
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
        $customFields = CRM_Core_BAO_CustomField::getFields( 'Membership', false, false,
                                                             CRM_Utils_Array::value( 'membership_type_id', $params ) );
        
        if ( !empty($customFields) ) {
            foreach ( $customFields as $k => $val ) {
                if ( in_array ( $val[3], array ('CheckBox', 'Multi-Select', 'Radio') ) &&
                     ! CRM_Utils_Array::value( $k, $params['custom'] ) ) {
                    CRM_Core_BAO_CustomField::formatCustomField( $k, $params['custom'],
                                                                 '', 'Membership', null, $this->_id);
                }
            }
        }
        // Retrieve the name and email of the current user - this will be the FROM for the receipt email
        require_once 'CRM/Contact/BAO/Contact/Location.php';
        list( $userName, $userEmail ) = CRM_Contact_BAO_Contact_Location::getEmailDetails( $ids['userId'] );
        
        if ( CRM_Utils_Array::value( 'record_contribution', $formValues ) ) {
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
           
            $membershipType = CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipType',
                                                           $formValues['membership_type_id'][1] );
            $params['contribution_source'] = "{$membershipType} Membership: Offline membership signup (by {$userName})";
            
            if ( $formValues['send_receipt'] ) {
                $params['receipt_date'] = $params['receive_date'];
            }
        }

        if ( $this->_mode ) {
            $params['total_amount'] = $formValues['total_amount']  = 
                CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipType', 
                                             $params['membership_type_id'],'minimum_fee' );
            $params['contribution_type_id'] = CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipType', 
                                                                           $params['membership_type_id'],'contribution_type_id' );
            require_once 'CRM/Core/BAO/PaymentProcessor.php';
            $this->_paymentProcessor = CRM_Core_BAO_PaymentProcessor::getPayment( $formValues['payment_processor_id'],
                                                                                  $this->_mode );
           
            require_once "CRM/Contact/BAO/Contact.php";
            
            $now = date( 'YmdHis' );
            $fields = array( );
            
            // set email for primary location.
            $fields["email-Primary"] = 1;
            $formValues["email-5"]   = $formValues["email-Primary"] = $this->_contributorEmail;
            $params['register_date'] = $now;
            
            // now set the values for the billing location.
            foreach ( $this->_fields as $name => $dontCare ) {
                $fields[$name] = 1;
            }
            
            // also add location name to the array
            $formValues["address_name-{$this->_bltID}"] =
                CRM_Utils_Array::value( 'billing_first_name' , $formValues ) . ' ' .
                CRM_Utils_Array::value( 'billing_middle_name', $formValues ) . ' ' .
                CRM_Utils_Array::value( 'billing_last_name'  , $formValues );
            
            $formValues["address_name-{$this->_bltID}"] = trim( $formValues["address_name-{$this->_bltID}"] );
        
            $fields["address_name-{$this->_bltID}"] = 1;
            
            $fields["email-{$this->_bltID}"] = 1;
            
            $ctype = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $this->_contactID, 'contact_type' );
            
            $nameFields = array( 'first_name', 'middle_name', 'last_name' );
            
            foreach ( $nameFields as $name ) {
                $fields[$name] = 1;
                if ( array_key_exists( "billing_$name", $formValues ) ) {
                    $formValues[$name]             = $formValues["billing_{$name}"];
                    $formValues['preserveDBName'] = true;
                }
            }
            
            $contactID = CRM_Contact_BAO_Contact::createProfileContact( $formValues, $fields, $this->_contactID, null, null, $ctype );
            
            // add all the additioanl payment params we need
            $this->_params["state_province-{$this->_bltID}"] =
                CRM_Core_PseudoConstant::stateProvinceAbbreviation( $this->_params["state_province_id-{$this->_bltID}"] );
            $this->_params["country-{$this->_bltID}"] =
                CRM_Core_PseudoConstant::countryIsoCode( $this->_params["country_id-{$this->_bltID}"] );
            
            $this->_params['year'      ]     = $this->_params['credit_card_exp_date']['Y'];
            $this->_params['month'     ]     = $this->_params['credit_card_exp_date']['M'];
            $this->_params['ip_address']     = CRM_Utils_System::ipAddress( );
            $this->_params['amount'        ] = $params['total_amount'];
            $this->_params['currencyID'    ] = $config->defaultCurrency;
            $this->_params['payment_action'] = 'Sale';
            $this->_params['invoiceID']      = md5( uniqid( rand( ), true ) );
        
            // at this point we've created a contact and stored its address etc
            // all the payment processors expect the name and address to be in the 
            // so we copy stuff over to first_name etc. 
            $paymentParams = $this->_params;
           
            
            require_once 'CRM/Core/Payment/Form.php';
            CRM_Core_Payment_Form::mapParams( $this->_bltID, $this->_params, $paymentParams, true );
            
            $payment =& CRM_Core_Payment::singleton( $this->_mode, 'Contribute', $this->_paymentProcessor );
            
            $result =& $payment->doDirectPayment( $paymentParams );
                      
            if ( is_a( $result, 'CRM_Core_Error' ) ) {
                CRM_Core_Error::displaySessionError( $result );
                CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/contact/view/membership',
                                                                   "reset=1&action=add&cid={$this->_contactID}&context=&mode={$this->_mode}" ) );
            }
            
            if ( $result ) {
                $this->_params = array_merge( $this->_params, $result );
            }
            $params['contribution_status_id'] = 1;
            $params['receive_date']           = $now;
            $params['invoice_id']             = $this->_params['invoiceID'];
            $params['contribution_source']    = ts( 'Online Membership: Admin Interface' );
            $params['source']                 = $formValues['source'] ? $formValues['source'] :$params['contribution_source'];
            $params['trxn_id']                = $result['trxn_id'];
            $params['payment_instrument_id']  = 1;
            $params['is_test']                = ( $this->_mode == 'live' ) ? 0 : 1 ; 
            if ( CRM_Utils_Array::value( 'send_receipt', $this->_params ) ) {
                $params['receipt_date'] = $now;
            } else {
                $params['receipt_date'] = null;
            }
            
            $this->set( 'params', $this->_params );
            $this->assign( 'trxn_id', $result['trxn_id'] );
            $this->assign( 'receive_date',
                           CRM_Utils_Date::mysqlToIso( $params['receive_date']) );
       
            // required for creating membership for related contacts
            $params['action'] = $this->_action;
            
            $membership =& CRM_Member_BAO_Membership::create( $params, $ids );
            $contribution = new CRM_Contribute_BAO_Contribution();
            $contribution->trxn_id = $result['trxn_id'];
            if ( $contribution->find( true ) ) {
                // next create the transaction record
                $trxnParams = array(
                                    'contribution_id'   => $contribution->id,
                                    'trxn_date'         => $now,
                                    'trxn_type'         => 'Debit',
                                    'total_amount'      => $params['total_amount'],
                                    'fee_amount'        => CRM_Utils_Array::value( 'fee_amount', $result ),
                                    'net_amount'        => CRM_Utils_Array::value( 'net_amount', $result, $params['total_amount'] ),
                                    'currency'          => $config->defaultCurrency,
                                    'payment_processor' => $this->_paymentProcessor['payment_processor_type'],
                                    'trxn_id'           => $result['trxn_id'],
                                    );
                
                require_once 'CRM/Contribute/BAO/FinancialTrxn.php';
                $trxn =& CRM_Contribute_BAO_FinancialTrxn::create( $trxnParams );
            }
        } else {
            $params['action'] = $this->_action;
            $membership =& CRM_Member_BAO_Membership::create( $params, $ids );
        }
        if ( $formValues['send_receipt'] ) {
            require_once 'CRM/Core/DAO.php';
            CRM_Core_DAO::setFieldValue( 'CRM_Member_DAO_MembershipType', 
                                         $params['membership_type_id'], 
                                         'receipt_text_signup',
                                         $formValues['receipt_text_signup'] );
        }

        $receiptSend = false;
        if ( $formValues['send_receipt'] ) {
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
            $members = array( array( 'member_id', '=', $membership->id, 0, 0 ) );
            // check whether its a test drive 
            if ( $this->_mode ) {
                $members[] = array( 'member_test', '=', 1, 0, 0 ); 
            } 
            CRM_Core_BAO_UFGroup::getValues( $this->_contactID, $customFields, $customValues , false, $members );
            if( $this->_mode ) {
                if ( CRM_Utils_Array::value( 'billing_first_name', $this->_params ) ) {
                    $name = $this->_params['billing_first_name'];
                    
                }
                
                if ( CRM_Utils_Array::value( 'billing_middle_name', $this->_params ) ) {
                    $name .= " {$this->_params['billing_middle_name']}";
                }
                
                if ( CRM_Utils_Array::value( 'billing_last_name', $this->_params ) ) {
                    $name .= " {$this->_params['billing_last_name']}";
                }
                $this->assign( 'billingName', $name );
                
                // assign the address formatted up for display
                $addressParts  = array( "street_address-{$this->_bltID}",
                                        "city-{$this->_bltID}",
                                        "postal_code-{$this->_bltID}",
                                        "state_province_id-{$this->_bltID}",
                                        "country_id-{$this->_bltID}");
                $addressFields = array( );
                foreach ($addressParts as $part) {
                    list( $n, $id ) = explode( '-', $part );
                    if ( isset ( $this->_params[$part] ) ) {
                        $addressFields[$n] = $this->_params[$part];
                    }
                }
                require_once 'CRM/Utils/Address.php';
                $this->assign('address', CRM_Utils_Address::format( $addressFields ) );
                $date = CRM_Utils_Date::format( $this->_params['credit_card_exp_date'] );
                $date = CRM_Utils_Date::mysqlToIso( $date );
                $this->assign( 'credit_card_exp_date', $date );
                $this->assign( 'credit_card_number',
                               CRM_Utils_System::mungeCreditCard( $this->_params['credit_card_number'] ) );
                $this->assign( 'credit_card_type', $this->_params['credit_card_type'] );
                $this->assign( 'contributeMode', 'direct');
                $this->assign( 'isAmountzero' , 0);
                $this->assign( 'is_pay_later',0);
                $this->assign( 'isPrimary', 1 );
            }
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
            $mailSend = CRM_Utils_Mail::send( $receiptFrom,
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
            
            //get the end date from calculated dates. 
            $endDate = ( $endDate ) ? $endDate : CRM_Utils_Array::value( 'end_date', $calcDates ); 
            
            if ( $endDate ) {
                $endDate=CRM_Utils_Date::customFormat($endDate);
                $statusMsg .= ' '.ts('The new membership End Date is %1.', array(1 => $endDate));
            }
            if ( $receiptSend && $mailSend ) {
                 $statusMsg .= ' '.ts('A membership confirmation and receipt has been sent to %1.', array(1 => $this->_contributorEmail));
            }
        }
        CRM_Core_Session::setStatus($statusMsg);
    }
}

