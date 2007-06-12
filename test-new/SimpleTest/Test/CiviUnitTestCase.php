<?php


require_once '../../CRM/Contribute/BAO/ContributionType.php';

class CiviUnitTestCase extends UnitTestCase {
   
    
    function organizationCreate( ) 
    {
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

     /** 
     * Function to create Individual
     * 
     * @return int $id of individual created
     */    
    
    function individualCreate() 
    {
        $params = array(
                        'first_name'    => 'Anthony',
                        'middle_name'   => 'john',
                        'last_name'     => 'Anderson',
                        'prefix'        => 'Mr.',
                        'suffix'        => 'Jr',
                        'email'         => 'anthony_anderson@civicrm.org',
                        'contact_type'  => 'Individual'
                        );
        $contact =& civicrm_contact_add($params);
        return $contact['contact_id'];
    }

    function contactDelete( $contactID ) 
    {
        require_once 'api/v2/Contact.php';
        $params['contact_id'] = $contactID;
        $result = civicrm_contact_delete( $params );
        if ( CRM_Utils_Array::value( 'is_error', $result ) ) {
            CRM_Core_Error::fatal( 'Could not delete contact' );
        }
        return;
    }

    function membershipTypeCreate( $contactID, $contributionTypeID = 1 ) 
    {
        $params = array( 'name'                 => 'General',
                         'duration_unit'        => 'year',
                         'duration_interval'    => 1,
                         'period_type'          => 'rolling',
                         'member_of_contact_id' => $contactID,
                         'contribution_type_id' => $contributionTypeID );

        $result = civicrm_membership_type_create( $params );
        
        if ( CRM_Utils_Array::value( 'is_error', $result ) ||
             ! CRM_Utils_Array::value( 'id', $result) ) {
            CRM_Core_Error::debug( 'r', $r );
            CRM_Core_Error::fatal( 'Could not create membership type' );
        }
        
        return $result['id'];
    }
    
    function membershipTypeDelete( $membershipTypeID )
    {
        $params['id'] = $membershipTypeID;
        $result = civicrm_membership_type_delete( $params );
        if ( CRM_Utils_Array::value( 'is_error', $result ) ) {
            CRM_Core_Error::fatal( 'Could not delete membership type' );
        }
        return;
    }
   
    function membershipStatusDelete( $membershipStatusID ) 
    {
        $params['id'] = $membershipStatusID;
        $result = civicrm_membership_status_delete( $params );
        if ( CRM_Utils_Array::value( 'is_error', $result ) ) {
            CRM_Core_Error::fatal( 'Could not delete membership status' );
        }
        return;
    }

    /** 
     * Function to create Contribution Type
     * 
     * @return int $id of contribution type created
     */    
    function CreateContributeType() 
    {
        $params = array(
                        'name'            => 'Gift',
                        'description'     => 'For some worthwhile cause',
                        'accounting_code' => 1004,
                        'is_deductible'   => 0,
                        'is_active'       => 1
                        );
       
        $ids = null;
        $contributionType = CRM_Contribute_BAO_ContributionType::add($params, $ids);
        return $contributionType->id;
    }
   
     /**
     * Function to delete contribution Types 
     * 
     * @param int $contributionTypeId
     */
    function DeleteContributeType($contributionTypeID) 
    {
        $del= CRM_Contribute_BAO_ContributionType::del($contributionTypeID);
    }     

}

?>