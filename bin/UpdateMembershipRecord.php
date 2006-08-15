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
