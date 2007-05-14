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
    
    function getMembershipLogRecord( $membership )
    {
        require_once "CRM/Member/BAO/MembershipLog.php";
        $membershipLog =& new CRM_Member_BAO_MembershipLog( );
        $membershipLog->membership_id = $membership->id;
        $membershipLog->find( true );
        return $membershipLog;
    }
    
    function testS1Data( )
    {
        $membershipType = $this->getMembershipTypeData( 'S1' );
        
        require_once "CRM/Member/BAO/Membership.php";
        $membership =& new CRM_Member_BAO_Membership( );
        $membership->membership_type_id = $membershipType->id;
        $membership->find( );
        
        $originalData = array( );
        $renewData    = array( );
        $logData      = array( );
        while ( $membership->fetch( ) ) {
            // get the membership data before renewing
            $originalData[$membership->id] = array( );
            CRM_Core_DAO::storeValues( $membership, $originalData[$membership->id] );
            
            // renew the membership
            $renewMembership = CRM_Member_BAO_Membership::renewMembership( $membership->contact_id, 
                                                                           $membershipType->id,
                                                                           true );
            
            // get the memership data after renewing
            $renewData[$membership->id]    = array( );
            CRM_Core_DAO::storeValues( $renewMembership, $renewData[$renewMembership->id] );
            
            //get log record
            $membershipLog = $this->getMembershipLogRecord( $membership );
            $logData[$membership->id]      = array( );
            CRM_Core_DAO::storeValues( $membershipLog, $logData[$renewMembership->id] );
        }
        
        $membership->free( );
        $membershipType->free( );
        
        foreach( $originalData as $id => $value ) {
            echo "=============  Memberhsip Id : $id  ===============";
            
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
            $endDate = explode( '-', $value['end_date'] );
            
            $this->assertEqual( date( 'Y-m-d', mktime( 0, 0, 0, $endDate[1], $endDate[2], $endDate[0]+1 ) ), 
                                CRM_Utils_Date::mysqlToIso( $renewData[$id]['end_date'] ) );
            
            echo "\n";
            echo "<br />";
            echo "Checking start date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( date( 'Y-m-d', mktime( 0, 0, 0, $endDate[1], $endDate[2]+1, $endDate[0] ) ),
                                $logData[$id]['start_date'] );
            
            echo "\n";
            echo "<br />";
            echo "Checking end date in the log";
            echo "\n";
            echo "<br />";
            $this->assertEqual( date( 'Y-m-d', mktime( 0, 0, 0, $endDate[1], $endDate[2], $endDate[0]+1 ) ), 
                                $logData[$id]['end_date'] );
            
            
            echo "\n";
            echo "<br />";
        }
    }
}
?>