<?php

/*
 * This file checks and updates the status of all membership records for a given domain using the calc_membership_status and 
 * update_contact_membership APIs.
 * It takes the first argument as the domain-id if specified, otherwise takes the domain-id as 1.
 *
 */

require_once '../civicrm.config.php';

require_once 'api/crm.php';
require_once 'CRM/Member/BAO/Membership.php';
require_once 'CRM/Contact/DAO/Contact.php';
require_once "CRM/Member/BAO/MessageTemplates.php";
require_once "CRM/Member/BAO/MembershipType.php";
require_once 'CRM/Member/BAO/MembershipLog.php';
require_once "CRM/Utils/Date.php";

class CRM_UpdateMembershipRecord {
    
    function __construct() 
    {
        _crm_initialize();
    }
    
    public function updateMembershipStatus( $domainID )
    {
        $membership = new CRM_Member_BAO_Membership( );
        $membership->find();
        while($membership->fetch()) {
            echo ".";
            $contact =& new CRM_Contact_DAO_Contact( );
            $contact->id = $membership->contact_id;
            $contact->find(true);
            if ( $contact->domain_id == $domainID ) {
                $newStatus = crm_calc_membership_status( $membership->id );
                if ( $newStatus && !$membership->is_override ) {
                    crm_update_contact_membership( array('id'        => $membership->id,
                                                         'status_id' => $newStatus['id']) );
                }

                
                //send remider for memnership renewal
                
                if ($membership->reminder_date && ( $membership->reminder_date <= date("Y-m-d")) ) {
                    $membershipType = $membership->membership_type_id;
                    $memType = new CRM_Member_BAO_MembershipType( );
                    $memType->id = $membershipType;
                    if ( $memType->find(true) ) {
                        if ( $memType->renewal_msg_id ) {
                            $emails     = CRM_Contact_BAO_Contact::allEmails( $contact->id );
                            if ( is_array( $emails )) {
                                foreach ( $emails as $email => $item ) {
                                    if ( $item['is_primary'] ) {
                                        $toEmail = $email;
                                    }
                                }
                            }
                            if ( $toEmail ) {
                                // NEED to be FIXED
                                $from = "admin@civicrm.org";
                                CRM_Member_BAO_MessageTemplates::sendReminder( $contact->id, $toEmail, $domainID, $memType->renewal_msg_id,$from);

                                //modify the the membership record, set remider date to NULL
                                 crm_update_contact_membership( array('id'             => $membership->id,
                                                                      'reminder_date'  => NULL ));

                                 
                                 //insert the log record.
                                 $memb = new CRM_Member_BAO_Membership( );
                                 $memb->id = $membership->id;
                                 if ( $memb->find(true) ) {
                                     $membershipLog = new CRM_Member_BAO_MembershipLog( );
                                     $membershipLog->membership_id = $memb->id;
                                     $membershipLog->status_id  = $memb->status_id;
                                     $membershipLog->start_date = CRM_Utils_Date::customFormat($memb->start_date,'%Y%m%d');
                                     $membershipLog->end_date   = CRM_Utils_Date::customFormat($memb->end_date,'%Y%m%d');
                                     $membershipLog->modified_id= $contact->id;
                                     $membershipLog->modified_date = date("Ymd");
                                     $membershipLog->renewal_reminder_date = date("Ymd");
                                     $membershipLog->save();
                                 }
                                
                            }
                        }
                    }
                }
                
                
            }
        }
        
    }
}
$domainId = $argv[1] ? $argv[1] : 1;
$obj =& new CRM_UpdateMembershipRecord();
echo "\n Updating ";
$obj->updateMembershipStatus($domainId);
echo "\n\n Membership records updated. (Done) \n";
?>
