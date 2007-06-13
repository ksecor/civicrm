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
    
    function individualCreate( ) {
        require_once 'api/v2/Contact.php';
        $params = array( 'first_name'       => 'Anthony',
                         'middle_name'      => 'john',
                         'Last_name'        => 'Anderson',
                         'prefix'           => 'Mr.',
                         'suffix'           => 'Jr',
                         'email'            => 'anthony_anderson@civicrm.org',
                         'contact_type'     => 'Individual');
        
        $result = civicrm_contact_add( $params );
        if ( CRM_Utils_Array::value( 'is_error', $result ) ||
             ! CRM_Utils_Array::value( 'contact_id', $result) ) {
            CRM_Core_Error::fatal( 'Could not create test individual contact' );
        }
        
        return $result['contact_id'];
    }

    /** 
     * Function to create household
     * 
     * @return int $id of household created
     */ 
    function householdCreate( ) {
        require_once 'api/v2/Contact.php';
        $params = array( 'household_name' => 'Unit Test household',
                         'contact_type'      => 'Household' );
        $result = civicrm_contact_add( $params );
        if ( CRM_Utils_Array::value( 'is_error', $result ) ||
             ! CRM_Utils_Array::value( 'contact_id', $result ) ) {
            CRM_Core_Error::fatal( 'Could not create test household contact' );
        }
        
        return $result['contact_id'];
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

    /**
     * Function to delete Membership Type
     * 
     * @param int $membershipTypeID
     */
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
     * Function to create Participant 
     *
     * @param int $contactID
     *
     * @return int $id of participant created
     */    
    function participantCreate( $contactID ) 
    { 
        $params = array(
                        'contact_id'    => $contactID,
                        'event_id'      => 1,
                        'status_id'     => 2,
                        'role_id'       => 1,
                        'register_date' => 20070219,
                        'source'        => 'Wimbeldon',
                        'event_level'   => 'Payment'
                        );

        $result = civicrm_participant_create( $params );
        if ( CRM_Utils_Array::value( 'is_error', $result ) ) {
            CRM_Core_Error::fatal( 'Could not create participant' );
        }
        return $result['participant_id'];
    }

    /** 
     * Function to create Contribution Type
     * 
     * @return int $id of contribution type created
     */    
    function createContributeType() 
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
    function deleteContributeType($contributionTypeID) 
    {
        $del= CRM_Contribute_BAO_ContributionType::del($contributionTypeID);
    }

    
    /** 
     * Function to create Tag
     * 
     * @return int tag_id of created tag
     */    
    function tagCreate()
    {
        require_once 'api/v2/Tag.php';
        $params = array(
                        'name'        => 'New Tag3',
                        'description' => 'This is description for New Tag 03',
                        'domain_id'   => '1'
                        );
        
        $tag =& civicrm_tag_create($params);
        return $tag['tag_id'];
    }

    /**
     * Function to create contribution  
     * 
     * @param int $cID      contact_id
     * @param int $cTypeID  id of contribution type
     *
     * @return int id of created contribution
     */
    function contributionCreate($cID,$cTypeID)
    {
        $params = array(
                        'domain_id'              => 1,
                        'contact_id'             => $cID,
                        'receive_date'           => date('Ymd'),
                        'total_amount'           => 100.00,
                        'contribution_type_id'   => $cTypeID,
                        'payment_instrument_id'  => 1,
                        'non_deductible_amount'  => 10.00,
                        'fee_amount'             => 50.00,
                        'net_amount'             => 90.00,
                        'trxn_id'                => 12345,
                        'invoice_id'             => 67890,
                        'source'                 => 'SSF',
                        'contribution_status_id' => 1,
                        'note'                   => 'Donating for Nobel Cause',
                        );
        
        $contribution =& civicrm_contribution_add($params);
        return $contribution['id'];

    }
    
    /**
     * Function to delete contribution  
     * 
     * @param int $contributionId
     */
    function contributionDelete($contributionId)
    {
        $params = array( 'contribution_id' => $contributionId );
        $val =& civicrm_contribution_delete( $params );
        $this->assertEqual($val['is_error'], 0);
    }
}

?>