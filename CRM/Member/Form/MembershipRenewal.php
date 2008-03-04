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

/**
 * This class generates form components for Membership Renewal
 * 
 */
class CRM_Member_Form_MembershipRenewal extends CRM_Member_Form
{

    public function preProcess()  
    {  
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
        
        $this->assign( "endDate", CRM_Utils_Date::customFormat(
                                                               CRM_Core_DAO::getFieldValue("CRM_Member_DAO_Membership",
                                                                                           $this->_id,
                                                                                           "end_date")
                                                               ) );

        $this->assign( "membershipStatus", 
                       CRM_Core_DAO::getFieldValue("CRM_Member_DAO_MembershipStatus",
                                                   CRM_Core_DAO::getFieldValue("CRM_Member_DAO_Membership",
                                                                               $this->_id,
                                                                               "status_id"
                                                                               ),
                                                   "name") );
        
        $orgId   = CRM_Core_DAO::getFieldValue("CRM_Member_DAO_MembershipType",$this->_memType,"member_of_contact_id");
        
        $this->assign( "memType",  CRM_Core_DAO::getFieldValue("CRM_Member_DAO_MembershipType",$this->_memType,"name") );
        $this->assign( "orgName",  CRM_Core_DAO::getFieldValue("CRM_Contact_DAO_Contact", $orgId,"display_name") );
        
        parent::preProcess( );
    }

    /**
     * This function sets the default values for the form.
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    public function setDefaultValues( ) 
    {
        $defaults = array( );
        $defaults =& parent::setDefaultValues( );
        $this->_memType = $defaults["membership_type_id"] ;

        $renewalDate = getDate();
        $defaults['renewal_date']['M'] = $renewalDate['mon'];
        $defaults['renewal_date']['d'] = $renewalDate['mday'];
        $defaults['renewal_date']['Y'] = $renewalDate['year'];

        if ($defaults['id']) {
            $defaults['record_contribution'] = CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipPayment', 
                                                                            $defaults['id'], 
                                                                            'contribution_id', 
                                                                            'membership_id' );
        }
        
        $defaults['contribution_type_id'] = CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipType', 
                                                                         $this->_memType, 
                                                                         'contribution_type_id' );
        
        $defaults['total_amount'] = CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipType', 
                                                                 $this->_memType, 
                                                                 'minimum_fee' );
        
        $defaults['record_contribution'] = 0;
        if ( $defaults['record_contribution'] ) {
            $contributionParams   = array( 'id' => $defaults['record_contribution'] );
            $contributionIds      = array( );
            
            require_once "CRM/Contribute/BAO/Contribution.php";
            CRM_Contribute_BAO_Contribution::getValues( $contributionParams, $defaults, $contributionIds );
        }
        
        $defaults['send_receipt'] = 0; 
        
        if ( $defaults['membership_type_id'] ) {
            $defaults['receipt_text_renewal'] =  CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipType', 
                                                                              $defaults['membership_type_id'],
                                                                              'receipt_text_renewal' );
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
        parent::buildQuickForm( );

        $this->applyFilter('__ALL__', 'trim');
        
        $this->add('date', 'renewal_date', ts('Date Renewal Entered'), CRM_Core_SelectValues::date('activityDate'), false );    
        $this->addRule('renewal_date', ts('Select a valid date.'), 'qfDate');
        
        $this->addElement('checkbox', 
                          'record_contribution', 
                          ts('Record Renewal Payment?'), null, 
                          array('onclick' =>"checkPayment();"));
        
        require_once 'CRM/Contribute/PseudoConstant.php';
        $this->add('select', 'contribution_type_id', 
                   ts( 'Contribution Type' ), 
                   array(''=>ts( '-select-' )) + CRM_Contribute_PseudoConstant::contributionType( )
                   );
        
        $this->add('text', 'total_amount', ts('Amount'));
        $this->addRule('total_amount', ts('Please enter a valid amount.'), 'money');

        $this->add('select', 'payment_instrument_id', 
                   ts( 'Paid By' ), 
                   array(''=>ts( '-select-' )) + CRM_Contribute_PseudoConstant::paymentInstrument( )
                   );
        
        $this->add('select', 'contribution_status_id',
                   ts('Payment Status'), 
                   CRM_Contribute_PseudoConstant::contributionStatus( )
                   );

        $this->addElement('checkbox', 
                          'send_receipt', 
                          ts('Send Confirmation and Receipt?'), null, 
                          array('onclick' =>"return showHideByValue('send_receipt','','notice','table-row','radio',false);") );
        $this->add('textarea', 'receipt_text_renewal', ts('Renewal Message') );
        // Retrieve the name and email of the contact - this will be the TO for receipt email
        list( $this->_contributorDisplayName, $this->_contributorEmail ) = CRM_Contact_BAO_Contact::getEmailDetails( $this->_contactID );
        $this->assign( 'email', $this->_contributorEmail );
        
        $this->addFormRule(array('CRM_Member_Form_MembershipRenewal', 'formRule'));
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
        if ( isset( $params['record_contribution'] ) && 
             ! isset( $params['contribution_type_id'] ) ) {
            $errors['contribution_type_id'] = "Please enter the contribution.";
        }  
        return empty($errors) ? true : $errors;
    }
       
    /**
     * Function to process the renewal form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        require_once 'CRM/Member/BAO/Membership.php';
        require_once 'CRM/Member/BAO/MembershipType.php';
        require_once 'CRM/Member/BAO/MembershipStatus.php'; 
 
        // get the submitted form values.  
        $formValues = $this->controller->exportValues( $this->_name );
        
        $params = array( );
        $ids    = array( );
        
        $params['contact_id']         = $this->_contactID;

        $renewalDate = null;
        
        if ( $formValues['renewal_date'] ) {
            $renewalDate = CRM_Utils_Date::format( $formValues['renewal_date'], '-' );
            $changeToday = array( );
            $changeToday['month'] = $formValues['renewal_date']['M'];
            $changeToday['day']   = $formValues['renewal_date']['d'];
            $changeToday['year']  = $formValues['renewal_date']['Y'];
            $this->set( 'renewDate', $changeToday );
        }
        
        $renewMembership = CRM_Member_BAO_Membership::renewMembership( $this->_contactID, 
                                                                       $this->_memType,
                                                                       0, $this, null );
        
        $endDate = CRM_Utils_Date::mysqlToIso( CRM_Utils_Date::format( $renewMembership->end_date ) );

        require_once 'CRM/Contact/BAO/Contact.php';
        // Retrieve the name and email of the current user - this will be the FROM for the receipt email
        $session =& CRM_Core_Session::singleton( );
        $userID  = $session->get( 'userID' );
        list( $userName, $userEmail ) = CRM_Contact_BAO_Contact::getEmailDetails( $userID );
        
        if ( $formValues['record_contribution'] ) {
            //building contribution params 
            $contributionParams = array( );
            $config =& CRM_Core_Config::singleton();
            $contributionParams['currency'             ] = $config->defaultCurrency;
            $contributionParams['contact_id'           ] = $params['contact_id'];
            $contributionParams['source'               ] = "Offline membership renewal (by {$userName})";
            $contributionParams['non_deductible_amount'] = 'null';
            $contributionParams['receive_date'         ] = date( 'Y-m-d H:i:s' );
            $contributionParams['receipt_date'         ] = $formValues['send_receipt'] ? 
                                                           $contributionParams['receive_date'] : 'null';
            
            $recordContribution = array( 'total_amount', 'contribution_type_id', 'payment_instrument_id', 'contribution_status_id' );
            foreach ( $recordContribution as $f ) {
                $contributionParams[$f] = CRM_Utils_Array::value( $f, $formValues );
            }   

            require_once 'CRM/Contribute/BAO/Contribution.php';
            $contribution =& CRM_Contribute_BAO_Contribution::create( $contributionParams, $ids );
           
            require_once 'CRM/Member/DAO/MembershipPayment.php';
            $mpDAO =& new CRM_Member_DAO_MembershipPayment();    
            $mpDAO->membership_id   = $renewMembership->id;
            $mpDAO->contribution_id = $contribution->id;
            $mpDAO->save();
        
            if ( $formValues['send_receipt'] ) {
                require_once 'CRM/Core/DAO.php';
                CRM_Core_DAO::setFieldValue( 'CRM_Member_DAO_MembershipType', 
                                             $params['membership_type_id'],
                                             'receipt_text_renewal',
                                             $formValues['receipt_text_renewal'] );
            }
        }
        
        $receiptSend = false;
        if ( $formValues['record_contribution'] && $formValues['send_receipt'] ) {
            $receiptSend = true;
            // Retrieve the name and email of the contact - this will be the TO for receipt email
            list( $this->_contributorDisplayName, $this->_contributorEmail ) = CRM_Contact_BAO_Contact::getEmailDetails( $this->_contactID );
            $receiptFrom = '"' . $userName . '" <' . $userEmail . '>';
            
            $paymentInstrument = CRM_Contribute_PseudoConstant::paymentInstrument();
            $formValues['paidBy'] = $paymentInstrument[$formValues['payment_instrument_id']];
            
            //get the group Tree
            $this->_groupTree =& CRM_Core_BAO_CustomGroup::getTree( 'Membership', $this->_id, false,$this->_memType);
            
            // retrieve custom data
            require_once "CRM/Core/BAO/UFGroup.php";
            $customFields = $customValues = $fo = array( );
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
                                             array( array( 'member_id', '=', $renewMembership->id, 0, 0 ) ) );
            
            $this->assign_by_ref( 'formValues', $formValues );
            $this->assign( 'receive_date', $renewalDate );
            $this->assign( 'module', 'Membership' );
            $this->assign( 'subject', ts('Membership Renewal Confirmation and Receipt') );
            $this->assign( 'mem_start_date', CRM_Utils_Date::customFormat( CRM_Utils_Date::format( $renewMembership->start_date ) ) );
            $this->assign( 'mem_end_date', CRM_Utils_Date::customFormat( $endDate ) );
            $this->assign( 'membership_name', CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipType',
                                                                            $renewMembership->membership_type_id ) );
            $this->assign( 'customValues', $customValues );
            
            $template =& CRM_Core_Smarty::singleton( );
            $message = $template->fetch( 'CRM/Contribute/Form/ReceiptMessageOffline.tpl' );
            $subject = trim( $template->fetch( 'CRM/Contribute/Form/ReceiptSubjectOffline.tpl' ) );
            
            require_once 'CRM/Utils/Mail.php';
            CRM_Utils_Mail::send( $receiptFrom,
                                  $this->_contributorDisplayName,
                                  $this->_contributorEmail,
                                  $subject,
                                  $message);
        }
        
        $memType = CRM_Core_DAO::getFieldValue('CRM_Member_DAO_MembershipType', $renewMembership->membership_type_id, 'name');
        $statusMsg = ts('%1 membership for %2 has been renewed.', array(1 => $memType, 2 => $this->_contributorDisplayName));
       
        $endDate = CRM_Utils_Date::customFormat( CRM_Core_DAO::getFieldValue( "CRM_Member_DAO_Membership", 
                                                                              $this->_id, 
                                                                              "end_date" ) );
        if ( $endDate ) {
            $statusMsg .= ts('The new membership End Date is %1.', array(1 => $endDate));
        }
        
        if ( $receiptSend ) {
            $statusMsg .= ts('A renewal confirmation and receipt has been sent to %1.', array(1 => $this->_contributorEmail));
        }
        
        CRM_Core_Session::setStatus($statusMsg);
    }
}

