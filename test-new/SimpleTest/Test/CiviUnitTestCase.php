<?php

class CiviUnitTestCase extends UnitTestCase {

    function createOrganization( ) {
        require_once 'api/v2/Contact.php';
        $params = array( 'organization_name' => 'Unit Test Organization',
                         'contact_type'      => 'Organization' );
        $result = civicrm_contact_add( $params );
        if ( CRM_Utils_Array::value( 'is_error', $result ) ||
             ! CRM_Utils_Array::value( 'contact_id', $result) ) {
            CRM_Core_Error::fatal( 'Could not create test org contact' );
        }

        return $result['contact_id'];
    }

    function deleteContact( $contactID ) {
        require_once 'api/v2/Contact.php';
        $params['contact_id'] = $contactID;
        $result = civicrm_contact_delete( $params );
        if ( CRM_Utils_Array::value( 'is_error', $result ) ) {
            CRM_Core_Error::fatal( 'Could not delete contact' );
        }
        return;
    }

    function createMembershipType( $contactID, $contributionTypeID = 1 ) {
        $params = array( 'name'                 => 'General',
                         'duration_unit'        => 'year',
                         'duration_interval'    => 1,
                         'period_type'          => 'rolling',
                         'member_of_contact_id' => $contactID,
                         'contribution_type_id' => $contributionTypeID );

        $result = civicrm_membership_type_create( $params );
        if ( CRM_Utils_Array::value( 'is_error', $result ) ||
             ! CRM_Utils_Array::value( 'membership_type_id', $result) ) {
            CRM_Core_Error::debug( 'r', $r );
            CRM_Core_Error::fatal( 'Could not create membership type' );
        }

        return $result['membership_type_id'];
    }

}


?>