<?php
class TestOfMembershipRenewal extends UnitTestCase 
{
    function setUp() 
    {
        require_once 'test/MembershipRenewal/MembershipValues.php';
    }
    
    function tearDown() 
    {
    }
    
    function testS1Data( )
    {
        echo "\n";
        echo "<br />";
        echo "=============  Testing S1 data  ===============";
        echo "\n";
        echo "<br />";
        
        $membershipType = test_MembershipRenewal_MembershipValues::getMembershipTypeData( 'S1' );
        
        $today = array( 'day'   => 06,
                        'month' => 12,
                        'year'  => 2007 );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        test_MembershipRenewal_MembershipValues::getMembershipData( $membershipType->id, 
                                                                    $originalData, 
                                                                    $renewData, $logData,
                                                                    false, $today );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            test_MembershipRenewal_MembershipValues::getCalculatedDates( $membershipType, 
                                                                         $value, 
                                                                         $calculatedDates,
                                                                         $today);
            
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
        
        $membershipType = test_MembershipRenewal_MembershipValues::getMembershipTypeData( 'S2' );
        
        $today = array( 'day'   => 06,
                        'month' => 06,
                        'year'  => 2007 );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        test_MembershipRenewal_MembershipValues::getMembershipData( $membershipType->id, 
                                                                    $originalData, 
                                                                    $renewData, $logData,
                                                                    false, $today );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            test_MembershipRenewal_MembershipValues::getCalculatedDates( $membershipType, 
                                                                         $value, 
                                                                         $calculatedDates,
                                                                         $today );
            
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
        
        $membershipType = test_MembershipRenewal_MembershipValues::getMembershipTypeData( 'S3' );
        
        $today = array( 'day'   => 12,
                        'month' => 12,
                        'year'  => 2007 );
                
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        test_MembershipRenewal_MembershipValues::getMembershipData( $membershipType->id, 
                                                                    $originalData, 
                                                                    $renewData, $logData, 
                                                                    false, $today );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            test_MembershipRenewal_MembershipValues::getCalculatedDates( $membershipType, 
                                                                         $value, 
                                                                         $calculatedDates,
                                                                         $today );
            
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
        
        $membershipType = test_MembershipRenewal_MembershipValues::getMembershipTypeData( 'S4' );
        
        $today = array( 'day'   => 12,
                        'month' => 12,
                        'year'  => 2007 );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        test_MembershipRenewal_MembershipValues::getMembershipData( $membershipType->id, 
                                                                    $originalData, 
                                                                    $renewData, $logData,
                                                                    false, $today );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            test_MembershipRenewal_MembershipValues::getCalculatedDates( $membershipType, 
                                                                         $value, 
                                                                         $calculatedDates,
                                                                         $today );
            
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
        
        $membershipType = test_MembershipRenewal_MembershipValues::getMembershipTypeData( 'S5' );
        
        $today = array( 'day'   => 15,
                        'month' => 06,
                        'year'  => 2007 );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        test_MembershipRenewal_MembershipValues::getMembershipData( $membershipType->id, 
                                                                    $originalData, 
                                                                    $renewData, $logData,
                                                                    false, $today );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            test_MembershipRenewal_MembershipValues::getCalculatedDates( $membershipType, 
                                                                         $value, 
                                                                         $calculatedDates,
                                                                         $today );
            
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
        
        $membershipType = test_MembershipRenewal_MembershipValues::getMembershipTypeData( 'S6' );
        
        $today = array( 'day'   => 27,
                        'month' => 11,
                        'year'  => 2007 );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        test_MembershipRenewal_MembershipValues::getMembershipData( $membershipType->id, 
                                                                    $originalData, 
                                                                    $renewData, $logData,
                                                                    false, $today );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            test_MembershipRenewal_MembershipValues::getCalculatedDates( $membershipType, 
                                                                         $value, 
                                                                         $calculatedDates,
                                                                         $today );
            
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
        
        $membershipType = test_MembershipRenewal_MembershipValues::getMembershipTypeData( 'S7' );
        
        $today = array( 'day'   => 31,
                        'month' => 12,
                        'year'  => 2007 );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        test_MembershipRenewal_MembershipValues::getMembershipData( $membershipType->id, 
                                                                    $originalData, 
                                                                    $renewData, $logData,
                                                                    false, $today );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            test_MembershipRenewal_MembershipValues::getCalculatedDates( $membershipType, 
                                                                         $value, 
                                                                         $calculatedDates,
                                                                         $today );
            
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
        
        $membershipType = test_MembershipRenewal_MembershipValues::getMembershipTypeData( 'S8' );
        
        $today = array( 'day'   => 21,
                        'month' => 12,
                        'year'  => 2007 );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        test_MembershipRenewal_MembershipValues::getMembershipData( $membershipType->id, 
                                                                    $originalData, 
                                                                    $renewData, $logData,
                                                                    false, $today );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            test_MembershipRenewal_MembershipValues::getCalculatedDates( $membershipType, 
                                                                         $value, 
                                                                         $calculatedDates,
                                                                         $today );
            
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
        
        $membershipType = test_MembershipRenewal_MembershipValues::getMembershipTypeData( 'S9' );
        
        $today = array( 'day'   => 21,
                        'month' => 08,
                        'year'  => 2007 );
        
        $today = null;
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        test_MembershipRenewal_MembershipValues::getMembershipData( $membershipType->id, 
                                                                    $originalData, 
                                                                    $renewData, $logData,
                                                                    false, $today );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            test_MembershipRenewal_MembershipValues::getCalculatedDates( $membershipType, 
                                                                         $value, 
                                                                         $calculatedDates,
                                                                         $today );
            
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
        $membershipType = test_MembershipRenewal_MembershipValues::getMembershipTypeData( 'S10' );
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        $today = array( 'month' => 11,
                        'day'   => 3, 
                        'year'  => 2007 );
        
        test_MembershipRenewal_MembershipValues::getMembershipData( $membershipType->id, $originalData, $renewData, $logData, false, $today );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            test_MembershipRenewal_MembershipValues::getCalculatedDates( $membershipType, $value, $calculatedDates, $today );
            
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
        
        $membershipType = test_MembershipRenewal_MembershipValues::getMembershipTypeData( 'S11' );
        
        $today = array( 'day'   => 03,
                        'month' => 06,
                        'year'  => 2007 );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        test_MembershipRenewal_MembershipValues::getMembershipData( $membershipType->id, 
                                                                    $originalData, 
                                                                    $renewData, $logData,
                                                                    false, $today );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            test_MembershipRenewal_MembershipValues::getCalculatedDates( $membershipType, 
                                                                         $value, 
                                                                         $calculatedDates,
                                                                         $today );
            
            // check : no new membership added while renewing
            echo "\n";
            echo "<br />";
            echo "Checking existing membership is updated";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $id, $renewData[$id]['id'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking membership status changed";
            echo "\n";
            echo "<br />";
            $this->assertNotEqual( $value['status_id'], $renewData[$id]['status_id'] );
            
            
            echo "\n";
            echo "<br />";
            echo "Checking Joining Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['join_date'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['join_date'] ) );
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
        
        $membershipType = test_MembershipRenewal_MembershipValues::getMembershipTypeData( 'S12' );
        
        $today = array( 'day'   => 21,
                        'month' => 05,
                        'year'  => 2007 );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        test_MembershipRenewal_MembershipValues::getMembershipData( $membershipType->id, 
                                                                    $originalData, 
                                                                    $renewData, $logData,
                                                                    false, $today );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            test_MembershipRenewal_MembershipValues::getCalculatedDates( $membershipType, 
                                                                         $value, 
                                                                         $calculatedDates,
                                                                         $today );
            
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
        
        $membershipType = test_MembershipRenewal_MembershipValues::getMembershipTypeData( 'S13' );
        
        $today = array( 'day'   => 21,
                        'month' => 09,
                        'year'  => 2007 );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        test_MembershipRenewal_MembershipValues::getMembershipData( $membershipType->id, 
                                                                    $originalData, 
                                                                    $renewData, $logData,
                                                                    false, $today );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            test_MembershipRenewal_MembershipValues::getCalculatedDates( $membershipType, 
                                                                         $value, 
                                                                         $calculatedDates,
                                                                         $today );
            
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
        
        $membershipType = test_MembershipRenewal_MembershipValues::getMembershipTypeData( 'S14' );
        
        $today = array( 'day'   => 21,
                        'month' => 05,
                        'year'  => 2007 );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        test_MembershipRenewal_MembershipValues::getMembershipData( $membershipType->id, 
                                                                    $originalData, 
                                                                    $renewData, $logData,
                                                                    false, $today );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            test_MembershipRenewal_MembershipValues::getCalculatedDates( $membershipType, 
                                                                         $value, 
                                                                         $calculatedDates,
                                                                         $today );
            
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
        
        $membershipType = test_MembershipRenewal_MembershipValues::getMembershipTypeData( 'S15' );
        
        $today = array( 'day'   => 21,
                        'month' => 06,
                        'year'  => 2007 );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        test_MembershipRenewal_MembershipValues::getMembershipData( $membershipType->id, 
                                                                    $originalData, 
                                                                    $renewData, $logData,
                                                                    false, $today );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            test_MembershipRenewal_MembershipValues::getCalculatedDates( $membershipType, 
                                                                         $value, 
                                                                         $calculatedDates,
                                                                         $today );
            
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
    
    function testS16Data( )
    {
        echo "\n";
        echo "<br />";
        echo "=============  Testing S16 data  ===============";
        echo "\n";
        echo "<br />";
        
        $membershipType = test_MembershipRenewal_MembershipValues::getMembershipTypeData( 'S16' );
        
        $today = array( 'day'   => 12,
                        'month' => 06,
                        'year'  => 2007 );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        test_MembershipRenewal_MembershipValues::getMembershipData( $membershipType->id, 
                                                                    $originalData, 
                                                                    $renewData, $logData,
                                                                    false, $today );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            test_MembershipRenewal_MembershipValues::getCalculatedDates( $membershipType, 
                                                                         $value, 
                                                                         $calculatedDates,
                                                                         $today );
            
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
        
        $membershipType = test_MembershipRenewal_MembershipValues::getMembershipTypeData( 'S17' );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        test_MembershipRenewal_MembershipValues::getMembershipData( $membershipType->id, $originalData, $renewData, $logData, true );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            test_MembershipRenewal_MembershipValues::getCalculatedDates( $membershipType, 
                                                                         $logData['update'][$id], 
                                                                         $calculatedDates );
            
            // check : no new membership added while renewing
            echo "\n";
            echo "<br />";
            echo "Checking existing membership is updated";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $id, $renewData[$id]['id'] );
            
            // Membership Status Update
            echo "\n";
            echo "<br />";
            echo "Checking Start Date is same";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['start_date'], 
                                $logData['update'][$id]['start_date'] );
            echo "\n";
            echo "<br />";
            echo "Checking End Date is same";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['end_date'], 
                                $logData['update'][$id]['end_date'] );
            echo "\n";
            echo "<br />";
            echo "Checking status is changed";
            echo "\n";
            echo "<br />";
            $this->assertNotEqual( $value['status_id'], 
                                   $logData['update'][$id]['status_id'] );
            
            $expired = false;
            if ( ! CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipStatus',
                                              $logData['update'][$id]['status_id'],
                                              'is_current_member'
                                              )  ) {
                $expired = true;
            }
            
            // Membership Renew
            echo "\n";
            echo "<br />";
            echo "Checking Joining Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['join_date'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['join_date'] ) );
            
            
            echo "\n";
            echo "<br />";
            if ( $expired ) {
                echo "Checking Start Date is Updated";
                echo "\n";
                echo "<br />";
                // Renewed Start date is same as that of logged start
                // date. Hence using logStartDate.
                $this->assertEqual( $calculatedDates['renewStartDate'],
                                    CRM_Utils_Date::mysqlToIso( $renewData[$id]['start_date'] ) );
            } else {
                echo "Checking Start Date is unchanged";
                echo "\n";
                echo "<br />";
                $this->assertEqual( $value['start_date'],
                                    CRM_Utils_Date::mysqlToIso( $renewData[$id]['start_date'] ) );
            }
            
            echo "\n";
            echo "<br />";
            echo "Checking End Date is renewed correctly";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['renewEndDate'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['end_date'] ) );
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
        
        $today = array( 'month' => 11,
                        'day'   => 12,
                        'year'  => 2007
                        );
        
        $membershipType = test_MembershipRenewal_MembershipValues::getMembershipTypeData( 'S18' );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        
        test_MembershipRenewal_MembershipValues::getMembershipData( $membershipType->id, $originalData, $renewData, $logData, true, $today );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Membership Id : $id  ===============";
            
            $calculatedDates = array( );
            test_MembershipRenewal_MembershipValues::getCalculatedDates( $membershipType, 
                                                                         $logData['update'][$id], 
                                                                         $calculatedDates,
                                                                         $today );
            
            // check : no new membership added while renewing
            echo "\n";
            echo "<br />";
            echo "Checking existing membership is updated";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $id, $renewData[$id]['id'] );
            
            // Membership Status Update
            echo "\n";
            echo "<br />";
            echo "Checking Start Date is same";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['start_date'], 
                                $logData['update'][$id]['start_date'] );
            echo "\n";
            echo "<br />";
            echo "Checking End Date is same";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['end_date'], 
                                $logData['update'][$id]['end_date'] );
            echo "\n";
            echo "<br />";
            echo "Checking status is changed";
            echo "\n";
            echo "<br />";
            $this->assertNotEqual( $value['status_id'], 
                                   $logData['update'][$id]['status_id'] );
            
            $expired = false;
            if ( ! CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipStatus',
                                              $logData['update'][$id]['status_id'],
                                              'is_current_member'
                                              )  ) {
                $expired = true;
            }
            
            // Membership Renew
            echo "\n";
            echo "<br />";
            echo "Checking Joining Date is same after renwal";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $value['join_date'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['join_date'] ) );
            
            
            echo "\n";
            echo "<br />";
            if ( $expired ) {
                echo "Checking Start Date is Updated";
                echo "\n";
                echo "<br />";
                // Renewed Start date is same as that of logged start
                // date. Hence using logStartDate.
                $this->assertEqual( $calculatedDates['renewStartDate'],
                                    CRM_Utils_Date::mysqlToIso( $renewData[$id]['start_date'] ) );
            } else {
                echo "Checking Start Date is unchanged";
                echo "\n";
                echo "<br />";
                $this->assertEqual( $value['start_date'],
                                    CRM_Utils_Date::mysqlToIso( $renewData[$id]['start_date'] ) );
            }
            
            echo "\n";
            echo "<br />";
            echo "Checking End Date is renewed correctly";
            echo "\n";
            echo "<br />";
            $this->assertEqual( $calculatedDates['renewEndDate'], 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['end_date'] ) );
            echo "\n";
            echo "<br />";
        }
    }

}
?>