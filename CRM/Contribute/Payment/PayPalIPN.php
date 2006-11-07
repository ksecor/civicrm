<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions      |
 | about the Affero General Public License or the licensing  of       |
 | CiviCRM, see the CiviCRM license FAQ at                            |
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

class CRM_Contribute_Payment_PayPalIPN {
    static function recur( ) {
    }

    static function single( ) {
        $store = null;

        require_once 'CRM/Utils/Request.php';

        // get the contribution, contact and contributionType ids from the GET params
        $contactID          = CRM_Utils_Request::retrieve( 'contactID', 'Integer', $store,
                                                           false, null, 'GET' );
        $contributionID     = CRM_Utils_Request::retrieve( 'contributionID', 'Integer', $store,
                                                           false, null, 'GET' );
        $contributionTypeID = CRM_Utils_Request::retrieve( 'contributionTypeID', 'Integer', $store,
                                                         false, null, 'GET' );
        $membershipTypeID   = CRM_Utils_Request::retrieve( '$membershipTypeID', 'Integer', $store,
                                                           false, null, 'GET' );

        if ( ! $contactID || ! $contributionID || ! $contributionTypeID ) {
            CRM_Core_Error::debug_log_message( "Could not find the right GET parameters" );
            echo "Failure: Invalid parameters<p>";
            return;
        }

        // make sure the invoice is valid and matches what we have in the contribution record
        require_once 'CRM/Contribute/DAO/Contribution.php';
        $contribution =& new CRM_Contribute_DAO_Contribution( );
        $contribution->id = $contributionID;
        if ( ! $contribution->find( true ) ) {
            CRM_Core_Error::debug_log_message( "Could not find contribution record: $contributionID" );
            echo "Failure: Could not find contribution record for $contributionID<p>";
            return;
        }
        $now = date( 'YmdHis' );
        
        require_once 'CRM/Contribute/DAO/ContributionType.php';
        $contributionType =& new CRM_Contribute_DAO_ContributionType( );
        $contributionType->id = $contributionTypeID;
        if ( ! $contributionType->find( true ) ) {
            CRM_Core_Error::debug_log_message( "Could not find contribution type record: $contributionTypeID" );
            echo "Failure: Could not find contribution type record for $contributionTypeID<p>";
            return;
        }
        
        $invoice = CRM_Utils_Request::retrieve( 'invoice', 'String', $store,
                                                false, null, 'POST' );
        if ( $contribution->invoice_id != $invoice ) {
            CRM_Core_Error::debug_log_message( "Invoice values dont match between database and IPN request" );
            echo "Failure: Invoice values dont match between database and IPN request<p>";
            return;
        }

        $amount = CRM_Utils_Request::retrieve( 'payment_gross', 'Money', $store,
                                               false, null, 'POST' );
        if ( $contribution->total_amount != $amount ) {
            CRM_Core_Error::debug_log_message( "Amount values dont match between database and IPN request" );
            echo "Failure: Amount values dont match between database and IPN request<p>";
            return;
        }

        // ok we are done with error checking, now let the real work begin
        // update the contact record with the name and address
        $params = array( );
        $lookup = array( 'first_name'     => 'first_name',
                         'last_name'      => 'last_name' ,
                         'street_address' => 'address_street',
                         'city'           => 'address_city',
                         'state'          => 'address_state',
                         'postal_code'    => 'address_zip',
                         'country'        => 'address_country_code' );
        foreach ( $lookup as $name => $paypalName ) {
            $value = CRM_Utils_Request::retrieve( $paypalName, 'String', $store,
                                                  false, null, 'POST' );
            if ( $value ) {
                $params[$name] = $value;
            }
        }
        if ( ! empty( $params ) ) {
            // update contact record
            $idParams = array( 'id' => $contactID, 'contact_id' => $contactID );
            CRM_Contact_BAO_Contact::createFlat($params, $idParams );
        }

        $contribution->receive_date = CRM_Utils_Date::isoToMysql($receive_date); // lets keep this the same

        $status = CRM_Utils_Request::retrieve( 'payment_status', 'String', $store,
                                               false, 0, 'POST' );

        if ( $status == 'Denied' || $status == 'Failed' || $status == 'Voided' ) {
            $contribution->contribution_status_id = 4;
            $contribution->save( );
            CRM_Core_DAO::transaction( 'COMMIT' );
            CRM_Core_Error::debug_log_message( "Setting contribution status to failed" );
            echo "Success: Setting contribution status to failed<p>";
            return;
        } else if ( $status == 'Pending' ) {
            CRM_Core_Error::debug_log_message( "returning since contribution status is pending" );

            echo "Success: Returning since contribution status is pending<p>";
            return;
        } else if ( $status == 'Refunded' || $status == 'Reversed' ) {
            $contribution->contribution_status_id = 3;
            $contribution->cancel_date = $now;
            $contribution->cancel_reason = CRM_Utils_Request::retrieve( 'ReasonCode', 'String', $store,
                                                                        false, null,'POST' );
            $contribution->save( );
            CRM_Core_DAO::transaction( 'COMMIT' );
            CRM_Core_Error::debug_log_message( "Setting contribution status to cancelled" );
            echo "Success: Setting contribution status to cancelled<p>";
            return;
        } else if ( $status != 'Completed' ) {
            // we dont handle this as yet
            CRM_Core_Error::debug_log_message( "returning since contribution status: $status is not handled" );
            echo "Failure: contribution status $status is not handled<p>";
            return;
        }

        // check if contribution is already completed, if so we ignore this ipn
        if ( $contribution->contribution_status_id == 1 ) {
            CRM_Core_Error::debug_log_message( "returning since contribution has already been handled" );
            echo "Success: Contribution has already been handled<p>";
            return;
        }

        CRM_Contribute_BAO_ContributionPage::setValues( $contribution->contribution_page_id, $values );
        
        $contribution->contribution_status_id  = 1;
        $contribution->is_test    = CRM_Utils_Request::retrieve( 'test_ipn', 'Integer', $store,
                                                                 false, 0, 'POST' );
        $contribution->fee_amount = CRM_Utils_Request::retrieve( 'payment_fee', 'Money', $store, 
                                                                 false, 0, 'POST' );
        $contribution->net_amount = CRM_Utils_Request::retrieve( 'settle_amount', 'Money', $store,  
                                                                 false, 0, 'POST' ); 
        $contribution->trxn_id    = CRM_Utils_Request::retrieve( 'txn_id', 'String', $store,
                                                                 false, 0, 'POST' );
        if ( $values['is_email_receipt'] ) {
            $contribution->receipt_date = $now;
        }

        CRM_Core_DAO::transaction( 'BEGIN' );

        $contribution->save( );
        
        // next create the transaction record
        $trxnParams = array(
                            'entity_table'      => 'civicrm_contribution',
                            'entity_id'         => $contribution->id,
                            'trxn_date'         => $now,
                            'trxn_type'         => 'Debit',
                            'total_amount'      => $amount,
                            'fee_amount'        => $contribution->fee_amount,
                            'net_amount'        => $contribution->net_amount,
                            'currency'          => $contribution->currency,
                            'payment_processor' => $config->paymentProcessor,
                            'trxn_id'           => $contribution->trxn_id,
                            );
        
        require_once 'CRM/Contribute/BAO/FinancialTrxn.php';
        $trxn =& CRM_Contribute_BAO_FinancialTrxn::create( $trxnParams );

        // get the title of the contribution page
        $title = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage',
                                              $contribution->contribution_page_id,
                                              'title' );

        require_once 'CRM/Utils/Money.php';
        $formattedAmount = CRM_Utils_Money::format($amount);

        // also create an activity history record
        $ahParams = array('entity_table'     => 'civicrm_contact', 
                          'entity_id'        => $contactID, 
                          'activity_type'    => $contributionType->name,
                          'module'           => 'CiviContribute', 
                          'callback'         => 'CRM_Contribute_Page_Contribution::details',
                          'activity_id'      => $contribution->id, 
                          'activity_summary' => "$formattedAmount - $title (online)",
                          'activity_date'    => $now,
                          );

        require_once 'api/History.php';
        if ( is_a( crm_create_activity_history($ahParams), 'CRM_Core_Error' ) ) { 
            CRM_Core_Error::debug_log_message( "error in updating activity" );
        }

        // TODO: membership and honor stuff

        // create membership record
        if ($membershipTypeID) {
            $template =& CRM_Core_Smarty::singleton( );
            $template->assign('membership_assign' , true );
            
            $membershipDetails = CRM_Member_BAO_MembershipType::getMembershipTypeDetails( $membershipID );
            $template->assign('membership_name',$membershipDetails['name']);
            
            $minimumFee = $membershipDetails['minimum_fee'];
            $template->assign('membership_amount'  , $minimumFee);
            
            if ($currentMembership = CRM_Member_BAO_Membership::getContactMembership($contactID,  $membershipTypeID)) {
                if ( ! $currentMembership['is_current_member'] ) {
                    require_once 'CRM/Member/BAO/MembershipStatus.php';
                    $dao = &new CRM_Member_DAO_Membership();
                    $dates = CRM_Member_BAO_MembershipType::getRenewalDatesForMembershipType( $currentMembership['id']);
                    $currentMembership['start_date'] = CRM_Utils_Date::customFormat($dates['start_date'],'%Y%m%d');
                    $currentMembership['end_date']   = CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d');
                    $currentMembership['source']     = CRM_Utils_Request::retrieve( 'item_name', 'String', $store,
                                                                                    false, 0, 'POST' );
                    $dao->copyValues($currentMembership);
                    $membership = $dao->save();
                    
                    //insert log here 
                    require_once 'CRM/Member/DAO/MembershipLog.php';
                    $dao = new CRM_Member_DAO_MembershipLog();
                    $dao->membership_id = $membership->id;
                    $dao->status_id     = $membership->status_id;
                    $dao->start_date    = CRM_Utils_Date::customFormat($dates['start_date'],'%Y%m%d');
                    $dao->end_date      = CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d'); 
                    $dao->modified_id   = $contactID;
                    $dao->modified_date = date('Ymd');
                    $dao->save();
                    
                    $template->assign('mem_start_date', CRM_Utils_Date::customFormat($dates['start_date'],'%Y%m%d'));
                    $template->assign('mem_end_date',   CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d'));
                    
                } else {
                    require_once 'CRM/Member/BAO/MembershipStatus.php';
                    $dao = &new CRM_Member_DAO_Membership();
                    $dao->id = $currentMembership['id'];
                    $dao->find(true); 
                    $membership = $dao ;
                    
                    //insert log here 
                    require_once 'CRM/Member/DAO/MembershipLog.php';
                    $dates = CRM_Member_BAO_MembershipType::getRenewalDatesForMembershipType( $membership->id);
                    $dao = new CRM_Member_DAO_MembershipLog();
                    $dao->membership_id = $membership->id;
                    $dao->status_id     = $membership->status_id;
                    $dao->start_date    = CRM_Utils_Date::customFormat($dates['log_start_date'],'%Y%m%d');
                    $dao->end_date      = CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d'); 
                    $dao->modified_id   = $contactID;
                    $dao->modified_date = date('Ymd');
                    $dao->save();

                    $template->assign('mem_start_date', CRM_Utils_Date::customFormat($dates['start_date'],'%Y%m%d'));
                    $template->assign('mem_end_date',   CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d'));
                    
                }
            }
        }

        $values = array( );
        require_once 'CRM/Contribute/BAO/ContributionPage.php';
        
        CRM_Core_Error::debug_log_message( "Contribution record updated successfully" );
        CRM_Core_DAO::transaction( 'COMMIT' );

        // add the new contribution values
        $template =& CRM_Core_Smarty::singleton( );
        $template->assign( 'amount' , $amount );
        $template->assign( 'trxn_id', $contribution->trxn_id );
        $template->assign( 'receive_date', $contribution->receive_date );
        $template->assign( 'contributeMode', 'notify' );

        CRM_Contribute_BAO_ContributionPage::sendMail( $contactID, $values );

        echo "Success: Database updated<p>";
    }

    static function main( ) {
        CRM_Core_Error::debug_var( 'GET' , $_GET , true, true );
        CRM_Core_Error::debug_var( 'POST', $_POST, true, true );

        if ( array_key_exists( 'recur', $_GET ) &&
             $_GET['recur'] ) {
            return self::recur( );
        }

        return self::single( );
    }

}

?>
