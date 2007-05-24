<?php

class TestOfMembershipRenewal extends UnitTestCase 
{
    function setUp() 
    {
        
    }
    
    function tearDown() 
    {
    }
    
    function getMembershipTypeData( $membershipTypeName )
    {
        require_once "CRM/Member/BAO/MembershipType.php";
        $membershipType =& new CRM_Member_BAO_MembershipType( );
        $membershipType->name = $membershipTypeName;
        $membershipType->find( true );
        return $membershipType;
    }
    
    function getMembershipLogRecord( &$membership , $stale=false)
    {
        require_once "CRM/Member/BAO/MembershipLog.php";
        $membershipLog =& new CRM_Member_BAO_MembershipLog( );
        $membershipLog->membership_id = $membership->id;
        $membershipLog->orderBy( 'id DESC' );
        // Remove the comments below once STALE membership issue is fixed.
        /*
        if ( $stale ) {
            $membershipLog->limit( '2' );
            $membershipLog->find( );
            while ( $membershipLog->fetch( ) ) {
                $logArray[] = $membershipLog;
            }
            return $logArray;
        }
        */
        $membershipLog->find( true );
        return $membershipLog;
    }
    
    function getCalculatedDates( &$membershipType, &$membershipValues, &$calcualtedDates )
    {
        $type = $membershipType->period_type == 'rolling' ? 'rolling' : 'fixed';
        
        $current = CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipStatus',
                                                $membershipValues['status_id'],
                                                'is_current_member'
                                                );
        
        // Check if rollover day is passed and accordingly fix the
        // dates for the renewal and log
        $rolloverPassed = false;
        
        if ( (!$current) && ($type == 'fixed') ) {
            $today = date("Ymd");
            
            $rollover = date("Ymd", mktime(0, 0, 0,
                                           substr($membershipType->fixed_period_rollover_day, 0, -2),
                                           substr($membershipType->fixed_period_rollover_day, -2, 2),
                                           date("Y") ) 
                             );
            
            if ( $rollover < $today) {
                $rolloverPassed = true;
            }
        }
        
        $endDate = explode( '-', $membershipValues['end_date'] );
        switch ($membershipType->duration_unit) {
            // Case: year
        case 'year':
            if ( $current ) {
                // is_current_member = 1; (CURRENT Membership)
                $calcualtedDates['logStartDate'] = date( 'Y-m-d', mktime( 0, 0, 0, $endDate[1], $endDate[2]+1, $endDate[0] ) );
                $tmpDate = explode( '-', $calcualtedDates['logStartDate'] );
                $calcualtedDates['logEndDate'] = $calcualtedDates['renewEndDate'] = date( 'Y-m-d', mktime( 0, 0, 0, $tmpDate[1], $tmpDate[2]-1, $tmpDate[0]+$membershipType->duration_interval ) );
            } else {
                // is_current_member = 0; (EXPIRED Membership)
                if ( $type == 'fixed' ) {
                    $calcualtedDates['logStartDate'] = 
                        $calcualtedDates['renewStartDate'] = 
                        date("Y-m-d", mktime(0, 0, 0,
                                           substr($membershipType->fixed_period_start_day, 0, -2),
                                           substr($membershipType->fixed_period_start_day, -2, 2),
                                           date("Y")-1 ) 
                             );
                                                       
                } else if ( $type == 'rolling' ) {
                    $calcualtedDates['logStartDate'] = $calcualtedDates['renewStartDate'] = date( 'Y-m-d', mktime( 0, 0, 0, date("m"), date("d"), date("Y") ) );
                }
                
                $tmpDate = explode( '-', $calcualtedDates['logStartDate'] );
                
                if ( $rolloverPassed ) {
                    $calcualtedDates['logEndDate'] = $calcualtedDates['renewEndDate'] = date( 'Y-m-d', mktime( 0, 0, 0, $tmpDate[1], $tmpDate[2]-1, $tmpDate[0]+$membershipType->duration_interval+$membershipType->duration_interval ) );
                } else {
                    $calcualtedDates['logEndDate'] = $calcualtedDates['renewEndDate'] = date( 'Y-m-d', mktime( 0, 0, 0, $tmpDate[1], $tmpDate[2]-1, $tmpDate[0]+$membershipType->duration_interval ) );
                }
            }
            
            break;
            // Case: month
        case 'month':
            if ( $current ) {
                // is_current_member = 1; (CURRENT Membership)
                $calcualtedDates['logStartDate'] = date( 'Y-m-d', mktime( 0, 0, 0, $endDate[1], $endDate[2]+1, $endDate[0] ) );
                $tmpDate = explode( '-', $calcualtedDates['logStartDate'] );
                $calcualtedDates['logEndDate'] = $calcualtedDates['renewEndDate'] = date( 'Y-m-d', mktime( 0, 0, 0, $tmpDate[1]+$membershipType->duration_interval, $tmpDate[2]-1, $tmpDate[0] ) );
            } else {
                // is_current_member = 0; (EXPIRED Membership)
                if ( $type == 'fixed' ) {
                    $calcualtedDates['logStartDate'] = 
                        $calcualtedDates['renewStartDate'] = 
                        date("Y-m-d", mktime(0, 0, 0,
                                           substr($membershipType->fixed_period_start_day, 0, -2),
                                           substr($membershipType->fixed_period_start_day, -2, 2),
                                           date("Y") ) 
                             );
                                                       
                } else if ( $type == 'rolling' ) {
                    $calcualtedDates['logStartDate'] = $calcualtedDates['renewStartDate'] = date( 'Y-m-d', mktime( 0, 0, 0, date("m"), date("d"), date("Y") ) );
                }
                
                $tmpDate = explode( '-', $calcualtedDates['logStartDate'] );
                
                if ( $rolloverPassed ) {
                    $calcualtedDates['logEndDate'] = $calcualtedDates['renewEndDate'] = date( 'Y-m-d', mktime( 0, 0, 0, $tmpDate[1]+$membershipType->duration_interval+$membershipType->duration_interval, $tmpDate[2]-1, $tmpDate[0] ) );
                } else {
                    $calcualtedDates['logEndDate'] = $calcualtedDates['renewEndDate'] = date( 'Y-m-d', mktime( 0, 0, 0, $tmpDate[1]+$membershipType->duration_interval, $tmpDate[2]-1, $tmpDate[0] ) );
                }
            }
            
            break;
            // Case: day
        case 'day':
            if ( $current ) {
                // is_current_member = 1; (CURRENT Membership)
                $calcualtedDates['logStartDate'] = date( 'Y-m-d', mktime( 0, 0, 0, $endDate[1], $endDate[2]+1, $endDate[0] ) );
                $tmpDate = explode( '-', $calcualtedDates['logStartDate'] );
                $calcualtedDates['logEndDate'] = $calcualtedDates['renewEndDate'] = date( 'Y-m-d', mktime( 0, 0, 0, $tmpDate[1], $tmpDate[2]+$membershipType->duration_interval-1, $tmpDate[0] ) );
            } else {
                // is_current_member = 0; (EXPIRED Membership)
                if ( $type == 'fixed' ) {
                    $calcualtedDates['logStartDate'] = 
                        $calcualtedDates['renewStartDate'] = 
                        date("Y-m-d", mktime(0, 0, 0,
                                           substr($membershipType->fixed_period_start_day, 0, -2),
                                           substr($membershipType->fixed_period_start_day, -2, 2),
                                           date("Y") ) 
                             );
                                                       
                } else if ( $type == 'rolling' ) {
                    $calcualtedDates['logStartDate'] = $calcualtedDates['renewStartDate'] = date( 'Y-m-d', mktime( 0, 0, 0, date("m"), date("d"), date("Y") ) );
                }
                
                $tmpDate = explode( '-', $calcualtedDates['logStartDate'] );
                
                if ( $rolloverPassed ) {
                    $calcualtedDates['logEndDate'] = $calcualtedDates['renewEndDate'] = date( 'Y-m-d', mktime( 0, 0, 0, $tmpDate[1], $tmpDate[2]+$membershipType->duration_interval+$membershipType->duration_interval-1, $tmpDate[0] ) );
                } else {
                    $calcualtedDates['logEndDate'] = $calcualtedDates['renewEndDate'] = date( 'Y-m-d', mktime( 0, 0, 0, $tmpDate[1], $tmpDate[2]+$membershipType->duration_interval-1, $tmpDate[0] ) );
                }
            }
            break;
        }
    }
    
    function getMembershipData( $membershipTypeId, &$originalData, &$renewData, &$logData, $stale=false )
    {
        require_once "CRM/Member/BAO/Membership.php";
        $membership =& new CRM_Member_BAO_Membership( );
        $membership->membership_type_id = $membershipTypeId;
        $membership->find( );
        
        while ( $membership->fetch( ) ) {
            // get the membership data before renewing
            $originalData[$membership->id] = array( );
            CRM_Core_DAO::storeValues( $membership, $originalData[$membership->id] );
            
            // renew the membership
            $renewMembership = CRM_Member_BAO_Membership::renewMembership( $membership->contact_id, 
                                                                           $membershipTypeId,
                                                                           true );
            // get the memership data after renewing
            $renewData[$renewMembership->id]    = array( );
            CRM_Core_DAO::storeValues( $renewMembership, $renewData[$renewMembership->id] );
            
            //get log record
            $membershipLog = $this->getMembershipLogRecord( $membership, $stale );
            
            // Remove the comments below once STALE membership issue is fixed.
            /*
            if ( ! $stale ) {
            */
            $logData[$renewMembership->id]      = array( );
            CRM_Core_DAO::storeValues( $membershipLog,
                                       $logData[$renewMembership->id] );
            
            // Remove the comments below once STALE membership issue is fixed.
            /*
            } else {
                // populate and return the log record created while
                // updaing status.
                $logData['update'][$renewMembership->id]      = array( );
                CRM_Core_DAO::storeValues( $membershipLog[2], $logData['update'][$renewMembership->id] );
                
                // populate and return the log record created while renewing.
                $logData['renew'][$renewMembership->id]      = array( );
                CRM_Core_DAO::storeValues( $membershipLog[1], $logData['renew'][$renewMembership->id] );
            }
            */
        }
        
        $membership->free( );
    }
    
    function testS1Data( )
    {
        echo "\n";
        echo "<br />";
        echo "=============  Testing S1 data  ===============";
        echo "\n";
        echo "<br />";
        
        $membershipType = $this->getMembershipTypeData( 'S1' );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        $this->getMembershipData( $membershipType->id, $originalData, $renewData, $logData );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            $this->getCalculatedDates( $membershipType, $value, $calculatedDates );
            
            // check : no new membership added while renewing
            echo "\n";
            echo "<br />";
            echo "Checking existing membership is updated";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $id, $renewData[$id]['id'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking existing membership status is CURRENT";
            echo "\n";
            echo "<br />";
            $this->assertEqual( 'Current',
                                CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipStatus',
                                                             $value['status_id']) );
            
            echo "\n";
            echo "<br />";
            echo "Checking membership status after renewal is CURRENT";
            echo "\n";
            echo "<br />";
            $this->assertEqual( 'Current',
                                CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipStatus',
                                                             $renewData[$id]['status_id']) );
            
            echo "\n";
            echo "<br />";
            echo "Checking Joining Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['join_date'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['join_date'] ) );
            echo "\n";
            echo "<br />";
            echo "Checking Start Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['start_date'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['start_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking End Date is renewed correctly";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['renewEndDate'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['end_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking start date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logStartDate'],
                                $logData[$id]['start_date'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking end date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logEndDate'],
                                $logData[$id]['end_date'] );
            
            
            echo "\n";
            echo "<br />";
        }
    }
    
    function testS2Data( )
    {
        echo "\n";
        echo "<br />";
        echo "=============  Testing S2 data  ===============";
        echo "\n";
        echo "<br />";
        
        $membershipType = $this->getMembershipTypeData( 'S2' );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        $this->getMembershipData( $membershipType->id, $originalData, $renewData, $logData );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            $this->getCalculatedDates( $membershipType, $value, $calculatedDates );
            
            // check : no new membership added while renewing
            echo "\n";
            echo "<br />";
            echo "Checking existing membership is updated";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $id, $renewData[$id]['id'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking Joining Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['join_date'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['join_date'] ) );
            echo "\n";
            echo "<br />";
            echo "Checking Start Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['start_date'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['start_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking End Date is renewed correctly";
            echo "\n";
            echo "<br />";
                        
            $this->assertEqual( $calculatedDates['renewEndDate'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['end_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking start date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logStartDate'],
                                $logData[$id]['start_date'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking end date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logEndDate'], 
                                $logData[$id]['end_date'] );
                        
            echo "\n";
            echo "<br />";
        }
    }
    
    function testS3Data( )
    {
        echo "\n";
        echo "<br />";
        echo "=============  Testing S3 data  ===============";
        echo "\n";
        echo "<br />";
        
        $membershipType = $this->getMembershipTypeData( 'S3' );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        $this->getMembershipData( $membershipType->id, $originalData, $renewData, $logData );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            $this->getCalculatedDates( $membershipType, $value, $calculatedDates );
            
            // check : no new membership added while renewing
            echo "\n";
            echo "<br />";
            echo "Checking existing membership is updated";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $id, $renewData[$id]['id'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking Joining Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['join_date'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['join_date'] ) );
            echo "\n";
            echo "<br />";
            echo "Checking Start Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['start_date'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['start_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking End Date is renewed correctly";
            echo "\n";
            echo "<br />";
                        
            $this->assertEqual( $calculatedDates['renewEndDate'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['end_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking start date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logStartDate'],
                                $logData[$id]['start_date'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking end date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logEndDate'], 
                                $logData[$id]['end_date'] );
                        
            echo "\n";
            echo "<br />";
        }
    }
    
    function testS4Data( )
    {
        echo "\n";
        echo "<br />";
        echo "=============  Testing S4 data  ===============";
        echo "\n";
        echo "<br />";
        
        $membershipType = $this->getMembershipTypeData( 'S4' );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        $this->getMembershipData( $membershipType->id, $originalData, $renewData, $logData );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            $this->getCalculatedDates( $membershipType, $value, $calculatedDates );
            
            // check : no new membership added while renewing
            echo "\n";
            echo "<br />";
            echo "Checking existing membership is updated";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $id, $renewData[$id]['id'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking Joining Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['join_date'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['join_date'] ) );
            echo "\n";
            echo "<br />";
            echo "Checking Start Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['start_date'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['start_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking End Date is renewed correctly";
            echo "\n";
            echo "<br />";
                        
            $this->assertEqual( $calculatedDates['renewEndDate'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['end_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking start date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logStartDate'],
                                $logData[$id]['start_date'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking end date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logEndDate'], 
                                $logData[$id]['end_date'] );
                        
            echo "\n";
            echo "<br />";
        }
    }
    
    function testS5Data( )
    {
        echo "\n";
        echo "<br />";
        echo "=============  Testing S5 data  ===============";
        echo "\n";
        echo "<br />";
        
        $membershipType = $this->getMembershipTypeData( 'S5' );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        $this->getMembershipData( $membershipType->id, $originalData, $renewData, $logData );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            $this->getCalculatedDates( $membershipType, $value, $calculatedDates );
            
            // check : no new membership added while renewing
            echo "\n";
            echo "<br />";
            echo "Checking existing membership is updated";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $id, $renewData[$id]['id'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking Joining Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['join_date'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['join_date'] ) );
            echo "\n";
            echo "<br />";
            echo "Checking Start Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['start_date'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['start_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking End Date is renewed correctly";
            echo "\n";
            echo "<br />";
                        
            $this->assertEqual( $calculatedDates['renewEndDate'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['end_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking start date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logStartDate'],
                                $logData[$id]['start_date'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking end date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logEndDate'], 
                                $logData[$id]['end_date'] );
                        
            echo "\n";
            echo "<br />";
        }
    }
    
    function testS6Data( )
    {
        echo "\n";
        echo "<br />";
        echo "=============  Testing S6 data  ===============";
        echo "\n";
        echo "<br />";
        
        $membershipType = $this->getMembershipTypeData( 'S6' );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        $this->getMembershipData( $membershipType->id, $originalData, $renewData, $logData );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            $this->getCalculatedDates( $membershipType, $value, $calculatedDates );
            
            // check : no new membership added while renewing
            echo "\n";
            echo "<br />";
            echo "Checking existing membership is updated";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $id, $renewData[$id]['id'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking Joining Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['join_date'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['join_date'] ) );
            echo "\n";
            echo "<br />";
            echo "Checking Start Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['start_date'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['start_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking End Date is renewed correctly";
            echo "\n";
            echo "<br />";
                        
            $this->assertEqual( $calculatedDates['renewEndDate'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['end_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking start date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logStartDate'],
                                $logData[$id]['start_date'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking end date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logEndDate'], 
                                $logData[$id]['end_date'] );
                        
            echo "\n";
            echo "<br />";
        }
    }
    
    function testS7Data( )
    {
        echo "\n";
        echo "<br />";
        echo "=============  Testing S7 data  ===============";
        echo "\n";
        echo "<br />";
        
        $membershipType = $this->getMembershipTypeData( 'S7' );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        $this->getMembershipData( $membershipType->id, $originalData, $renewData, $logData );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            $this->getCalculatedDates( $membershipType, $value, $calculatedDates );
            
            // check : no new membership added while renewing
            echo "\n";
            echo "<br />";
            echo "Checking existing membership is updated";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $id, $renewData[$id]['id'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking Joining Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['join_date'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['join_date'] ) );
            echo "\n";
            echo "<br />";
            echo "Checking Start Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['start_date'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['start_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking End Date is renewed correctly";
            echo "\n";
            echo "<br />";
                        
            $this->assertEqual( $calculatedDates['renewEndDate'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['end_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking start date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logStartDate'],
                                $logData[$id]['start_date'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking end date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logEndDate'], 
                                $logData[$id]['end_date'] );
                        
            echo "\n";
            echo "<br />";
        }
    }
    
    function testS8Data( )
    {
        echo "\n";
        echo "<br />";
        echo "=============  Testing S8 data  ===============";
        echo "\n";
        echo "<br />";
        
        $membershipType = $this->getMembershipTypeData( 'S8' );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        $this->getMembershipData( $membershipType->id, $originalData, $renewData, $logData );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            $this->getCalculatedDates( $membershipType, $value, $calculatedDates );
            
            // check : no new membership added while renewing
            echo "\n";
            echo "<br />";
            echo "Checking existing membership is updated";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $id, $renewData[$id]['id'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking Joining Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['join_date'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['join_date'] ) );
            echo "\n";
            echo "<br />";
            echo "Checking Start Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['start_date'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['start_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking End Date is renewed correctly";
            echo "\n";
            echo "<br />";
                        
            $this->assertEqual( $calculatedDates['renewEndDate'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['end_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking start date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logStartDate'],
                                $logData[$id]['start_date'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking end date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logEndDate'], 
                                $logData[$id]['end_date'] );
                        
            echo "\n";
            echo "<br />";
        }
    }
    
    function testS9Data( )
    {
        echo "\n";
        echo "<br />";
        echo "=============  Testing S9 data  ===============";
        echo "\n";
        echo "<br />";
        
        $membershipType = $this->getMembershipTypeData( 'S9' );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        $this->getMembershipData( $membershipType->id, $originalData, $renewData, $logData );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            $this->getCalculatedDates( $membershipType, $value, $calculatedDates );
            
            // check : no new membership added while renewing
            echo "\n";
            echo "<br />";
            echo "Checking existing membership is updated";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $id, $renewData[$id]['id'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking Joining Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['join_date'], 
                                $renewData[$id]['join_date'] );
            echo "\n";
            echo "<br />";
            echo "Checking Start Date is renewed correctly";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['renewStartDate'],
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['start_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking End Date is renewed correctly";
            echo "\n";
            echo "<br />";
                        
            $this->assertEqual( $calculatedDates['renewEndDate'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['end_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking start date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logStartDate'],
                                $logData[$id]['start_date'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking end date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logEndDate'], 
                                $logData[$id]['end_date'] );
                        
            echo "\n";
            echo "<br />";
        }
    }
    
    function testS10Data( )
    {
        echo "\n";
        echo "<br />";
        echo "=============  Testing S10 data  ===============";
        echo "\n";
        echo "<br />";
        
        $membershipType = $this->getMembershipTypeData( 'S10' );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        $this->getMembershipData( $membershipType->id, $originalData, $renewData, $logData );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            $this->getCalculatedDates( $membershipType, $value, $calculatedDates );
                        
            // check : no new membership added while renewing
            echo "\n";
            echo "<br />";
            echo "Checking existing membership is updated";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $id, $renewData[$id]['id'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking Joining Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['join_date'], 
                                $renewData[$id]['join_date'] );
            echo "\n";
            echo "<br />";
            echo "Checking Start Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['renewStartDate'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['start_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking End Date is renewed correctly";
            echo "\n";
            echo "<br />";
                        
            $this->assertEqual( $calculatedDates['renewEndDate'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['end_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking start date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logStartDate'],
                                $logData[$id]['start_date'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking end date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logEndDate'], 
                                $logData[$id]['end_date'] );
                        
            echo "\n";
            echo "<br />";
        }
    }
    
    function testS11Data( )
    {
        echo "\n";
        echo "<br />";
        echo "=============  Testing S11 data  ===============";
        echo "\n";
        echo "<br />";
        
        $membershipType = $this->getMembershipTypeData( 'S11' );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        $this->getMembershipData( $membershipType->id, $originalData, $renewData, $logData );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            $this->getCalculatedDates( $membershipType, $value, $calculatedDates );
            
            // check : no new membership added while renewing
            echo "\n";
            echo "<br />";
            echo "Checking existing membership is updated";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $id, $renewData[$id]['id'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking Joining Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['join_date'], 
                                $renewData[$id]['join_date'] );
            echo "\n";
            echo "<br />";
            echo "Checking Start Date is renewed correctly";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['renewStartDate'],
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['start_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking End Date is renewed correctly";
            echo "\n";
            echo "<br />";
                        
            $this->assertEqual( $calculatedDates['renewEndDate'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['end_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking start date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logStartDate'],
                                $logData[$id]['start_date'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking end date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logEndDate'], 
                                $logData[$id]['end_date'] );
                        
            echo "\n";
            echo "<br />";
        }
    }
    
    function testS12Data( )
    {
        echo "\n";
        echo "<br />";
        echo "=============  Testing S12 data  ===============";
        echo "\n";
        echo "<br />";
        
        $membershipType = $this->getMembershipTypeData( 'S12' );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        $this->getMembershipData( $membershipType->id, $originalData, $renewData, $logData );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            $this->getCalculatedDates( $membershipType, $value, $calculatedDates );
            
            // check : no new membership added while renewing
            echo "\n";
            echo "<br />";
            echo "Checking existing membership is updated";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $id, $renewData[$id]['id'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking Joining Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['join_date'], 
                                $renewData[$id]['join_date'] );
            echo "\n";
            echo "<br />";
            echo "Checking Start Date is renewed correctly";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['renewStartDate'],
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['start_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking End Date is renewed correctly";
            echo "\n";
            echo "<br />";
                        
            $this->assertEqual( $calculatedDates['renewEndDate'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['end_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking start date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logStartDate'],
                                $logData[$id]['start_date'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking end date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logEndDate'], 
                                $logData[$id]['end_date'] );
                        
            echo "\n";
            echo "<br />";
        }
    }
    
    function testS13Data( )
    {
        echo "\n";
        echo "<br />";
        echo "=============  Testing S13 data  ===============";
        echo "\n";
        echo "<br />";
        
        $membershipType = $this->getMembershipTypeData( 'S13' );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        $this->getMembershipData( $membershipType->id, $originalData, $renewData, $logData );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            $this->getCalculatedDates( $membershipType, $value, $calculatedDates );
            
            // check : no new membership added while renewing
            echo "\n";
            echo "<br />";
            echo "Checking existing membership is updated";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $id, $renewData[$id]['id'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking Joining Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['join_date'], 
                                $renewData[$id]['join_date'] );
            echo "\n";
            echo "<br />";
            echo "Checking Start Date is renewed correctly";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['renewStartDate'],
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['start_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking End Date is renewed correctly";
            echo "\n";
            echo "<br />";
                        
            $this->assertEqual( $calculatedDates['renewEndDate'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['end_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking start date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logStartDate'],
                                $logData[$id]['start_date'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking end date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logEndDate'], 
                                $logData[$id]['end_date'] );
                        
            echo "\n";
            echo "<br />";
        }
    }
    
    function testS14Data( )
    {
        echo "\n";
        echo "<br />";
        echo "=============  Testing S14 data  ===============";
        echo "\n";
        echo "<br />";
        
        $membershipType = $this->getMembershipTypeData( 'S14' );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        $this->getMembershipData( $membershipType->id, $originalData, $renewData, $logData );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            $this->getCalculatedDates( $membershipType, $value, $calculatedDates );
            
            // check : no new membership added while renewing
            echo "\n";
            echo "<br />";
            echo "Checking existing membership is updated";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $id, $renewData[$id]['id'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking Joining Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['join_date'], 
                                $renewData[$id]['join_date'] );
            echo "\n";
            echo "<br />";
            echo "Checking Start Date is renewed correctly";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['renewStartDate'],
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['start_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking End Date is renewed correctly";
            echo "\n";
            echo "<br />";
                        
            $this->assertEqual( $calculatedDates['renewEndDate'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['end_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking start date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logStartDate'],
                                $logData[$id]['start_date'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking end date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logEndDate'], 
                                $logData[$id]['end_date'] );
                        
            echo "\n";
            echo "<br />";
        }
    }
    
    function testS15Data( )
    {
        echo "\n";
        echo "<br />";
        echo "=============  Testing S15 data  ===============";
        echo "\n";
        echo "<br />";
        
        $membershipType = $this->getMembershipTypeData( 'S15' );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        $this->getMembershipData( $membershipType->id, $originalData, $renewData, $logData );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            $this->getCalculatedDates( $membershipType, $value, $calculatedDates );
            
            // check : no new membership added while renewing
            echo "\n";
            echo "<br />";
            echo "Checking existing membership is updated";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $id, $renewData[$id]['id'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking Joining Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['join_date'], 
                                $renewData[$id]['join_date'] );
            echo "\n";
            echo "<br />";
            echo "Checking Start Date is renewed correctly";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['renewStartDate'],
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['start_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking End Date is renewed correctly";
            echo "\n";
            echo "<br />";
                        
            $this->assertEqual( $calculatedDates['renewEndDate'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['end_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking start date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logStartDate'],
                                $logData[$id]['start_date'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking end date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logEndDate'], 
                                $logData[$id]['end_date'] );
                        
            echo "\n";
            echo "<br />";
        }
    }
    
    function S16Data( )
    {
        echo "\n";
        echo "<br />";
        echo "=============  Testing S16 data  ===============";
        echo "\n";
        echo "<br />";
        
        $membershipType = $this->getMembershipTypeData( 'S16' );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        $this->getMembershipData( $membershipType->id, $originalData, $renewData, $logData );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            $this->getCalculatedDates( $membershipType, $value, $calculatedDates );
            
            // check : no new membership added while renewing
            echo "\n";
            echo "<br />";
            echo "Checking existing membership is updated";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $id, $renewData[$id]['id'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking Joining Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['join_date'], 
                                $renewData[$id]['join_date'] );
            echo "\n";
            echo "<br />";
            echo "Checking Start Date is renewed correctly";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['renewStartDate'],
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['start_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking End Date is renewed correctly";
            echo "\n";
            echo "<br />";
                        
            $this->assertEqual( $calculatedDates['renewEndDate'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['end_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking start date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logStartDate'],
                                $logData[$id]['start_date'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking end date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logEndDate'], 
                                $logData[$id]['end_date'] );
                        
            echo "\n";
            echo "<br />";
        }
    }
    
    function testS17Data( )
    {
        echo "\n";
        echo "<br />";
        echo "=============  Testing S17 data  ===============";
        echo "\n";
        echo "<br />";
        
        $membershipType = $this->getMembershipTypeData( 'S17' );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        $this->getMembershipData( $membershipType->id, $originalData, $renewData, $logData, true );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            $this->getCalculatedDates( $membershipType, $value, $calculatedDates );
            /*
            CRM_Core_Error::debug( '$calculatedDates', $calculatedDates );
            CRM_Core_Error::debug( 'Renew Start Date', $renewData[$id]['start_date'] );
            CRM_Core_Error::debug( 'Renew End Date', $renewData[$id]['end_date'] );
            CRM_Core_Error::debug( 'Log Start Date', $logData['renew'][$id]['start_date'] );
            CRM_Core_Error::debug( 'Log End Date', $logData['renew'][$id]['end_date'] );
            CRM_Core_Error::debug( 'Log Start Date', $logData['update'][$id]['start_date'] );
            CRM_Core_Error::debug( 'Log End Date', $logData['update'][$id]['end_date'] );
            */
            // check : no new membership added while renewing
            echo "\n";
            echo "<br />";
            echo "Checking existing membership is updated";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $id, $renewData[$id]['id'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking Joining Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['join_date'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['join_date'] ) );
            echo "\n";
            echo "<br />";
            echo "Checking Start Date is unchanged";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['start_date'],
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['start_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking End Date is renewed correctly";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['renewEndDate'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['end_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking start date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logStartDate'],
                                $logData[$id]['start_date'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking end date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logEndDate'], 
                                $logData[$id]['end_date'] );
                        
            echo "\n";
            echo "<br />";
        }
    }
    
    function testS18Data( )
    {
        echo "\n";
        echo "<br />";
        echo "=============  Testing S18 data  ===============";
        echo "\n";
        echo "<br />";
        
        $membershipType = $this->getMembershipTypeData( 'S18' );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        $this->getMembershipData( $membershipType->id, $originalData, $renewData, $logData, true );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            $this->getCalculatedDates( $membershipType, $value, $calculatedDates );
            /*
            CRM_Core_Error::debug( '$calculatedDates', $calculatedDates );
            CRM_Core_Error::debug( 'Renew Start Date', $renewData[$id]['start_date'] );
            CRM_Core_Error::debug( 'Renew End Date', $renewData[$id]['end_date'] );
            CRM_Core_Error::debug( 'Log Start Date', $logData['renew'][$id]['start_date'] );
            CRM_Core_Error::debug( 'Log End Date', $logData['renew'][$id]['end_date'] );
            CRM_Core_Error::debug( 'Log Start Date', $logData['update'][$id]['start_date'] );
            CRM_Core_Error::debug( 'Log End Date', $logData['update'][$id]['end_date'] );
            */
            // check : no new membership added while renewing
            echo "\n";
            echo "<br />";
            echo "Checking existing membership is updated";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $id, $renewData[$id]['id'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking Joining Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['join_date'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['join_date'] ) );
            echo "\n";
            echo "<br />";
            echo "Checking Start Date is unchanged";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['start_date'],
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['start_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking End Date is renewed correctly";
            echo "\n";
            echo "<br />";
                        
            $this->assertEqual( $calculatedDates['renewEndDate'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['end_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking start date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logStartDate'],
                                $logData[$id]['start_date'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking end date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['logEndDate'], 
                                $logData[$id]['end_date'] );
                        
            echo "\n";
            echo "<br />";
        }
    }
}
?>