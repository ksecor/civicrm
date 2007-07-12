<?php
class test_MembershipRenewal_MembershipValues 
{
    static function getMembershipTypeData( $membershipTypeName )
    {
        require_once "CRM/Member/BAO/MembershipType.php";
        $membershipType =& new CRM_Member_BAO_MembershipType( );
        $membershipType->name = $membershipTypeName;
        $membershipType->find( true );
        return $membershipType;
    }
    
    static function getMembershipLogRecord( $membershipId , $stale=false)
    {
        require_once "CRM/Member/BAO/MembershipLog.php";
        $membershipLog =& new CRM_Member_BAO_MembershipLog( );
        $membershipLog->membership_id = $membershipId;
        $membershipLog->orderBy( 'id DESC' );
        
        if ( $stale ) {
            //$membershipLog->limit( '2' );
            $membershipLog->find( );
            $logArray = array( );
            while ( $membershipLog->fetch( ) ) {
                CRM_Core_DAO::storeValues( $membershipLog, $logArray[] );
            }
            return $logArray;
        }
        
        $membershipLog->find( true );
        return $membershipLog;
    }
    
    static function getCalculatedDates( &$membershipType, &$membershipValues, &$calculatedDates, $changeToday=null )
    {
        $type = $membershipType->period_type == 'rolling' ? 'rolling' : 'fixed';
        
        $current = CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipStatus',
                                                $membershipValues['status_id'],
                                                'is_current_member'
                                                );
        
        // Check if rollover day is passed and accordingly fix the
        // dates for the renewal and log
        $rolloverPassed = false;
        $today = CRM_Utils_Date::getToday( $changeToday );
        
        if ( (!$current) && ($type == 'fixed') ) {
            $rollover = date("Ymd", mktime(0, 0, 0,
                                           substr($membershipType->fixed_period_rollover_day, 0, -2),
                                           substr($membershipType->fixed_period_rollover_day, -2, 2),
                                           date("Y") ) 
                             );
            if ( CRM_Utils_Date::isoToMysql( $today ) > $rollover ) {
                $rolloverPassed = true;
            }
        }
        
        $endDate = explode( '-', $membershipValues['end_date'] );
        
        switch ($membershipType->duration_unit) {
        case 'year':
            if ( $current ) {
                // is_current_member = 1; (CURRENT Membership)
                $calculatedDates['logStartDate'] = date( 'Y-m-d', mktime( 0, 0, 0, $endDate[1], $endDate[2]+1, $endDate[0] ) );
                $tmpDate = explode( '-', $calculatedDates['logStartDate'] );
                $calculatedDates['logEndDate'] = $calculatedDates['renewEndDate'] = date( 'Y-m-d', mktime( 0, 0, 0, $tmpDate[1], $tmpDate[2]-1, $tmpDate[0]+$membershipType->duration_interval ) );
            } else {
                // is_current_member = 0; (EXPIRED Membership)
                
                // For EXPIRED memberships, first get the logStartDate
                // and renewStartDate.                
                if ( $type == 'fixed' ) {
                    $calculatedDates['logStartDate'] = 
                        $calculatedDates['renewStartDate'] = 
                        date("Y-m-d", mktime(0, 0, 0,
                                             substr($membershipType->fixed_period_start_day, 0, -2),
                                             substr($membershipType->fixed_period_start_day, -2, 2),
                                             date("Y") ) 
                             );
                    
                } else if ( $type == 'rolling' ) {
                    $calculatedDates['logStartDate'] = $calculatedDates['renewStartDate'] = $today;
                }
                // For EXPIRED memberships, first get the logStartDate
                // and renewStartDate.                
                
                // Now get the logEndDate and renewEndDate based on
                // the logStartDate
                $tmpDate = explode( '-', $calculatedDates['logStartDate'] );
                
                if ( $rolloverPassed ) {
                    $calculatedDates['logEndDate'] = 
                        $calculatedDates['renewEndDate'] = 
                        date( 'Y-m-d', mktime( 0, 0, 0, 
                                               $tmpDate[1], $tmpDate[2]-1, 
                                               $tmpDate[0]+$membershipType->duration_interval+$membershipType->duration_interval ) );
                } else {
                    $calculatedDates['logEndDate'] = $calculatedDates['renewEndDate'] = date( 'Y-m-d', mktime( 0, 0, 0, $tmpDate[1], $tmpDate[2]-1, $tmpDate[0]+$membershipType->duration_interval ) );
                }
            }
            
            break;
            
        case 'month':
            if ( $current ) {
                // is_current_member = 1; (CURRENT Membership)
                $calculatedDates['logStartDate'] = date( 'Y-m-d', mktime( 0, 0, 0, $endDate[1], $endDate[2]+1, $endDate[0] ) );
                $tmpDate = explode( '-', $calculatedDates['logStartDate'] );
                $calculatedDates['logEndDate'] = $calculatedDates['renewEndDate'] = date( 'Y-m-d', mktime( 0, 0, 0, $tmpDate[1]+$membershipType->duration_interval, $tmpDate[2]-1, $tmpDate[0] ) );
            } else {
                // is_current_member = 0; (EXPIRED Membership)
                
                // For EXPIRED memberships, first get the logStartDate
                // and renewStartDate.                
                if ( $type == 'fixed' ) {
                    $calculatedDates['logStartDate'] = 
                        $calculatedDates['renewStartDate'] = 
                        date("Y-m-d", mktime(0, 0, 0,
                                             substr($membershipType->fixed_period_start_day, 0, -2),
                                             substr($membershipType->fixed_period_start_day, -2, 2),
                                             date("Y") ) 
                             );
                    
                } else if ( $type == 'rolling' ) {
                    $calculatedDates['logStartDate'] = $calculatedDates['renewStartDate'] = $today;
                }
                
                // Now get the logEndDate and renewEndDate based on
                // the logStartDate
                $tmpDate = explode( '-', $calculatedDates['logStartDate'] );
                
                if ( $rolloverPassed ) {
                    $calculatedDates['logEndDate'] = $calculatedDates['renewEndDate'] = date( 'Y-m-d', mktime( 0, 0, 0, $tmpDate[1]+$membershipType->duration_interval+$membershipType->duration_interval, $tmpDate[2]-1, $tmpDate[0] ) );
                } else {
                    $calculatedDates['logEndDate'] = $calculatedDates['renewEndDate'] = date( 'Y-m-d', mktime( 0, 0, 0, $tmpDate[1]+$membershipType->duration_interval, $tmpDate[2]-1, $tmpDate[0] ) );
                }
            }
            
            break;
        case 'day':
            if ( $current ) {
                // is_current_member = 1; (CURRENT Membership)
                $calculatedDates['logStartDate'] = date( 'Y-m-d', mktime( 0, 0, 0, $endDate[1], $endDate[2]+1, $endDate[0] ) );
                $tmpDate = explode( '-', $calculatedDates['logStartDate'] );
                $calculatedDates['logEndDate'] = $calculatedDates['renewEndDate'] = date( 'Y-m-d', mktime( 0, 0, 0, $tmpDate[1], $tmpDate[2]+$membershipType->duration_interval-1, $tmpDate[0] ) );
            } else {
                // is_current_member = 0; (EXPIRED Membership)
                
                // For EXPIRED memberships, first get the logStartDate
                // and renewStartDate.                
                if ( $type == 'fixed' ) {
                    $calculatedDates['logStartDate'] = 
                        $calculatedDates['renewStartDate'] = 
                        date("Y-m-d", mktime(0, 0, 0,
                                             substr($membershipType->fixed_period_start_day, 0, -2),
                                             substr($membershipType->fixed_period_start_day, -2, 2),
                                             date("Y") ) 
                             );
                    
                } else if ( $type == 'rolling' ) {
                    $calculatedDates['logStartDate'] = $calculatedDates['renewStartDate'] = $today;
                }
                
                // Now get the logEndDate and renewEndDate based on
                // the logStartDate
                $tmpDate = explode( '-', $calculatedDates['logStartDate'] );
                
                if ( $rolloverPassed ) {
                    $calculatedDates['logEndDate'] = $calculatedDates['renewEndDate'] = date( 'Y-m-d', mktime( 0, 0, 0, $tmpDate[1], $tmpDate[2]+$membershipType->duration_interval+$membershipType->duration_interval-1, $tmpDate[0] ) );
                } else {
                    $calculatedDates['logEndDate'] = $calculatedDates['renewEndDate'] = date( 'Y-m-d', mktime( 0, 0, 0, $tmpDate[1], $tmpDate[2]+$membershipType->duration_interval-1, $tmpDate[0] ) );
                }
            }
            break;
        }
    }
    
    static function getMembershipData( $membershipTypeId, &$originalData, &$renewData, &$logData, $stale=false, $today=null )
    {
        require_once "CRM/Member/BAO/Membership.php";
        $membership =& new CRM_Member_BAO_Membership( );
        $membership->membership_type_id = $membershipTypeId;
        $membership->find( );
        
        while ( $membership->fetch( ) ) {
            // get the membership data before renewing
            $originalData[$membership->id] = array( );
            CRM_Core_DAO::storeValues( $membership, $originalData[$membership->id] );
            $form = null;
            // renew the membership
            $renewMembership = CRM_Member_BAO_Membership::renewMembership( $membership->contact_id, 
                                                                           $membershipTypeId,
                                                                           true, $form, $today );
            // get the memership data after renewing
            $renewData[$renewMembership->id]    = array( );
            CRM_Core_DAO::storeValues( $renewMembership, $renewData[$renewMembership->id] );
            
            //get log record
            $membershipLog = self::getMembershipLogRecord( $membership->id, $stale );
            
            if ( ! $stale ) {
                $logData[$renewMembership->id] = array( );
                CRM_Core_DAO::storeValues( $membershipLog,
                                           $logData[$renewMembership->id] );
            } else {
                // populate and return the log record created while
                // renewing.
                $logData['renew'][$renewMembership->id] = $membershipLog[0];
                
                // populate and return the log record created while
                // updaing status.
                $logData['update'][$renewMembership->id] = $membershipLog[1];
            }
        }
        
        $membership->free( );
    }
}
?>