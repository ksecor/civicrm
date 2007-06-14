<?php


require_once '../../CRM/Contribute/BAO/ContributionType.php';

class CiviUnitTestCase extends UnitTestCase {

    /** 
     * Generic function to create Organisation, to be used in test cases
     * 
     * @param array   parameters for civicrm_contact_add api function call
     * @return int    id of Organisation created
     */
    function organizationCreate( $params = null ) {
        if ( $params === null ) {
            $params = array( 'organization_name' => 'Unit Test Organization',
                             'contact_type'      => 'Organization' );
        }
        return $this->_contactCreate( $params );
    }
    
    /** 
     * Generic function to create Individual, to be used in test cases
     * 
     * @param array   parameters for civicrm_contact_add api function call
     * @return int    id of Individual created
     */
    function individualCreate( $params = null ) {
        if ( $params === null ) {
            $params = array( 'first_name'       => 'Anthony',
                             'middle_name'      => 'john',
                             'Last_name'        => 'Anderson',
                             'prefix'           => 'Mr.',
                             'suffix'           => 'Jr',
                             'email'            => 'anthony_anderson@civicrm.org',
                             'contact_type'     => 'Individual');
        }
        return $this->_contactCreate( $params );
    }

    /** 
     * Generic function to create Household, to be used in test cases
     * 
     * @param array   parameters for civicrm_contact_add api function call
     * @return int    id of Household created
     */
    function householdCreate( $params = null ) {
        if ( $params === null ) {    
            $params = array( 'household_name' => 'Unit Test household',
                             'contact_type'      => 'Household' );
        }
        return $this->_contactCreate( $params );
    }

    /** 
     * Private helper function for calling civicrm_contact_add
     * 
     * @param array   parameters for civicrm_contact_add api function call
     * @return int    id of Household created
     */
    private function _contactCreate( $params ) {
        require_once 'api/v2/Contact.php';
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
            CRM_Core_Error::fatal( 'Could not create membership type' );
        }
        
        return $result['id'];
    }

    function contactMembershipCreate( $params ) 
    {
        $pre = array('join_date'   => '2007-01-21',
                     'start_date'  => '2007-01-21',
                     'end_date'    => '2007-12-21',
                     'source'      => 'Payment'  );
        foreach ( $pre as $key => $val ) {
            if ( !$params[$key] ) {
                $params[$key] = $val;
            }
        }
        
        $result = civicrm_contact_membership_create( $params );
        
        if ( CRM_Utils_Array::value( 'is_error', $result ) ||
             ! CRM_Utils_Array::value( 'id', $result) ) {
            CRM_Core_Error::fatal( 'Could not create membership' );
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
   
    function membershipDelete( $membershipID )
    {
        $result = civicrm_membership_delete( $membershipID );
        if ( CRM_Utils_Array::value( 'is_error', $result ) ) {
            CRM_Core_Error::fatal( 'Could not delete membership' );
        }
        return;
    }

    function membershipStatusCreate( $name = 'test member status' ) 
    {
        $params['name'] = $name;
        $result = civicrm_membership_status_create( $params );
        if ( CRM_Utils_Array::value( 'is_error', $result ) ) {
            CRM_Core_Error::fatal( 'Could not create membership status' );
        }
        return $result['id'];
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
    function contributionTypeCreate() 
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
    function contributionTypeDelete($contributionTypeID) 
    {
        $del= CRM_Contribute_BAO_ContributionType::del($contributionTypeID);
    }
    
    /** 
     * Function to create Tag
     * 
     * @return int tag_id of created tag
     */    
    function tagCreate( $params = null )
    {
        if ( $params === null ) {
            $params = array(
                            'name'        => 'New Tag3',
                            'description' => 'This is description for New Tag 03',
                            'domain_id'   => '1'
                            );
        }
        
        require_once 'api/v2/Tag.php';
        $tag =& civicrm_tag_create($params);
        
        return $tag['tag_id'];
    }
    
    /** 
     * Function to delete Tag
     * 
     * @param  int $tagId   id of the tag to be deleted
     */    
    function tagDelete( $tagId )
    {
        require_once 'api/v2/Tag.php';
        $params['tag_id'] = $tagId;
        $result = civicrm_tag_delete( $params );
        if ( CRM_Utils_Array::value( 'is_error', $result ) ) {
            CRM_Core_Error::fatal( 'Could not delete tag' );
        }
        return;
    }
    
    /** 
     * Add entity(s) to the tag
     * 
     * @param  array  $params 
     *
     */
    
    function entityTagAdd( $params )
    {
        $result = civicrm_entity_tag_add( $params );
        
        if ( CRM_Utils_Array::value( 'is_error', $result ) ) {
            CRM_Core_Error::fatal( 'Error while creating entity tag' );
        }
        
        return;
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
        require_once 'api/v2/Contribute.php';
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
        require_once 'api/v2/Contribute.php';
        $params = array( 'contribution_id' => $contributionId );
        $val =& civicrm_contribution_delete( $params );
        $this->assertEqual($val['is_error'], 0);
    }


    /**
     * Function to delete participant 
     * 
     * @param int $participantID
     */
    
    function participantDelete( $participantID ) 
    {
        require_once 'api/v2/Participant.php';
        $result = & civicrm_participant_delete( $participantID );
        if ( CRM_Utils_Array::value( 'is_error', $result ) ) {
            CRM_Core_Error::fatal( 'Could not delete participant' );
        }
        return;
    
    }

    /**
     * Function to create participant payment
     *
     * @return int $id of created payment
     */
    
    function participantPaymentCreate( $participantID ) 
    {
        require_once 'api/v2/Participant.php';
        //Create Participant Payment record With Values
        $params = array(
                        'participant_id'       => $participantID,
                        'payment_entity_table' => 'civicrm_contribute',           
                        'payment_entity_id'    => 5
                        );
        
        $participantPayment = & civicrm_participant_payment_create( $params );
        if ( CRM_Utils_Array::value( 'is_error', $participantPayment ) ||
             ! CRM_Utils_Array::value( 'id', $participantPayment ) ) {
            CRM_Core_Error::fatal( 'Could not create participant payment' );
        }
        
        return $participantPayment['id'];
    }

    /**
     * Function to delete participant payment
     * 
     * @param int $paymentID
     */
    
    function participantPaymentDelete( $paymentID ) 
    {
        require_once 'api/v2/Participant.php';
        $result = & civicrm_participant_payment_delete( $paymentID );
        if ( CRM_Utils_Array::value( 'is_error', $result ) ) {
            CRM_Core_Error::fatal( 'Could not delete participant payment' );
        }
        return;
    
    }

    /** 
     * Function to add a Location
     * 
     * @return int location id of created location
     */    
    function locationAdd( $contactID ) 
    {
        $params['contact_id'] = $contactID;
        
        $result = civicrm_location_add( $params );
        
        if ( CRM_Utils_Array::value( 'is_error', $result ) ||
             ! CRM_Utils_Array::value( 'id', $result) ) {
            CRM_Core_Error::fatal( 'Could not create location' );
        }
        
        return $result['id'];
    }
    
    /**
     * Function to create Group for a contact
     * 
     * @param int $contactId
     */
    function contactGroupCreate( $contactId )
    {
        $params = array(
                        'contact_id.1' => $contactId,
                        'group_id'     => 1 );
        civicrm_group_contact_add( $params );
    }
    
    /**
     * Function to delete Group for a contact
     * 
     * @param array $params
     */
    function contactGroupDelete( $contactId )
    {
        $params = array(
                        'contact_id.1' => $contactId,
                        'group_id'     => 1 );
       civicrm_group_contact_remove( $params );
    }

    /**
     * Function to create Activity 
     * 
     * @param int $contactId
     */
    function activityCreate( $individualSourceID, $individualTargetID )
    {
        $params = array(
                        'source_contact_id' => $individualSourceID,
                        'target_entity_table' => 'civicrm_contact',
                        'target_entity_id' => $individualTargetID ,
                        'subject' => 'Disscussion on Apis for v2',
                        'scheduled_date_time' => date('Ymd'),
                        'duration_hours' =>30,
                        'duration_minutes' => 20,
                        'location' => 'Pensulvania',
                        'details' => 'a meeting activity',
                        'status' => 'Scheduled',
                        'parent_id' => 1, 
                        'activity_name' =>'Meeting',
                        );
        $activity =& civicrm_activity_create($params);
        return $activity;
    }

     /**
     * Function to create custom group
     * 
     * @param string $className
     * @param string $title  name of custom group
     */
    
    function customGroupCreate( $className,$title ) 
    {
        require_once 'api/v2/CustomGroup.php';
        $params = array(
                        'title'      => $title,
                        'class_name' => $className,
                        'domain_id'  => 1,                       
                        'style'      => 'Inline',
                        'is_active'  => 1
                        );

        $result =& civicrm_custom_group_create($params);
         if ( CRM_Utils_Array::value( 'is_error', $result ) ||
             ! CRM_Utils_Array::value( 'custom_group_id', $result) ) {
             CRM_Core_Error::fatal( 'Could not create Custom Group' );
        }
        
        return $result['custom_group_id'];    
    }

    /**
     * Function to delete custom group
     * 
     * @param int    $customGroupID
     */
    
    function customGroupDelete( $customGroupID ) 
    {
        $params['id'] = $customGroupID;
        $result = & civicrm_custom_group_delete($params);
        if ( CRM_Utils_Array::value( 'is_error', $result ) ) {
            CRM_Core_Error::fatal( 'Could not delete custom group' );
        }
        return;
    }

     /**
     * Function to create custom field
     * 
     * @param int    $customGroupID
     * @param string $name  name of custom field
     */
    
    function customFieldCreate( $customGroupID, $name ) 
    {
        require_once 'api/v2/CustomGroup.php';
        $params = array(
                        'label'           => $name,
                        'name'            => $name,
                        'custom_group_id' => $customGroupID,
                        'data_type'       => 'String',
                        'html_type'       => 'Text',
                        'is_searchable'   =>  1, 
                        'is_active'        => 1,
                        );

        $result =& civicrm_custom_field_create($params);
         if ( CRM_Utils_Array::value( 'is_error', $result ) ||
             ! CRM_Utils_Array::value( 'custom_field_id', $result) ) {
             CRM_Core_Error::fatal( 'Could not create Custom Field' );
        }
        
        return $result['custom_field_id'];    
    }

    /**
     * Function to delete custom field
     * 
     * @param int $customFieldID
     */
    
    function customFieldDelete( $customFieldID ) 
    {
        $params['id'] = $customFieldID;
        $result = & civicrm_custom_field_delete($params);
        if ( CRM_Utils_Array::value( 'is_error', $result ) ) {
            CRM_Core_Error::fatal( 'Could not delete custom field' );
        }
        return;
    }
}

?>
