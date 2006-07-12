<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Member/DAO/MembershipType.php';

class CRM_Member_BAO_MembershipType extends CRM_Member_DAO_MembershipType 
{

    /**
     * static holder for the default LT
     */
    static $_defaultMembershipType = null;
    

    /**
     * class constructor
     */
    function __construct( ) 
    {
        parent::__construct( );
    }
    
    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * contact_id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the flattened values
     *
     * @return object CRM_Member_BAO_MembershipType object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) 
    {
        $membershipType =& new CRM_Member_DAO_MembershipType( );
        $membershipType->copyValues( $params );
        if ( $membershipType->find( true ) ) {
            CRM_Core_DAO::storeValues( $membershipType, $defaults );
            return $membershipType;
        }
        return null;
    }

    /**
     * update the is_active flag in the db
     *
     * @param int      $id        id of the database record
     * @param boolean  $is_active value we want to set the is_active field
     *
     * @return Object             DAO object on sucess, null otherwise
     * @static
     */
    static function setIsActive( $id, $is_active ) 
    {
        return CRM_Core_DAO::setFieldValue( 'CRM_Member_DAO_MembershipType', $id, 'is_active', $is_active );
    }

    /**
     * function to add the membership types
     *
     * @param array $params reference array contains the values submitted by the form
     * @param array $ids    reference array contains the id
     * 
     * @access public
     * @static 
     * @return object
     */
    static function add(&$params, &$ids) 
    {
        $params['is_active'] =  CRM_Utils_Array::value( 'is_active', $params, false );
        
        // action is taken depending upon the mode
        $membershipType               =& new CRM_Member_DAO_MembershipType( );
        $membershipType->domain_id    = CRM_Core_Config::domainID( );
        
        $membershipType->copyValues( $params );
        
        $membershipType->id = CRM_Utils_Array::value( 'membershipType', $ids );
        $membershipType->member_of_contact_id = CRM_Utils_Array::value( 'memberOfContact', $ids );

        $membershipType->save( );

        return $membershipType;
    }

    /**
     * Function to delete membership Types 
     * 
     * @param int $membershipTypeId
     * @static
     */
    
    static function del($membershipTypeId) 
    {
        //check dependencies
        require_once 'CRM/Member/BAO/Membership.php';
        $membership =& new CRM_Member_BAO_Membership();
        $membership->membership_type_id = $membershipTypeId;
        if ( $membership->find() ) {
            while ( $membership->fetch() ) {
                CRM_Member_BAO_Membership::deleteMembership($membership->id);
            }
        }
        
        //delete from membership Type table
        require_once 'CRM/Member/DAO/MembershipType.php';
        $membershipType =& new CRM_Member_DAO_MembershipType( );
        $membershipType->id = $membershipTypeId;
        $membershipType->delete();
    }


    /**
     * Function to convert membership Type's 'start day' & 'rollover day' to human readable formats.
     * 
     * @param array $membershipType an array of membershipType-details.
     * @static
     */
    
    static function convertDayFormat( &$membershipType ) 
    {
        $periodDays = array(
                            'fixed_period_start_day',
                            'fixed_period_rollover_day'
                            );
        foreach ( $membershipType as $id => $details ) {
            foreach ( $periodDays as $pDay) {
                if ($details[$pDay]) {
                    $month = substr( $details[$pDay], 0, strlen($details[$pDay])-2);
                    $day   = substr( $details[$pDay],-2);    
                    $monthMap = array(
                                      '1'  => 'Jan',
                                      '2'  => 'Feb',
                                      '3'  => 'Mar',
                                      '4'  => 'Apr',
                                      '5'  => 'May',
                                      '6'  => 'Jun',
                                      '7'  => 'Jul',
                                      '8'  => 'Aug',
                                      '9'  => 'Sep',
                                      '10' => 'Oct',
                                      '11' => 'Nov',
                                      '12' => 'Dec'
                                      );
                    $membershipType[$id][$pDay] = $monthMap[$month].' '.$day; 
                }
            }
        }
    }
    
    /**
     * Function to get membership Types 
     * 
     * @param int $membershipTypeId
     * @static
     */
    static function getMembershipTypes( $public = true )
    {
        require_once 'CRM/Member/DAO/Membership.php';
        $membershipTypes = array();
        $membershipType =& new CRM_Member_DAO_MembershipType( );
        $membershipType->is_active = 1;
        if (  $public ){
            $membershipType->visibility = 'Public';
        }
        $membershipType->orderBy(' weight');
        $membershipType->find();
        while ( $membershipType->fetch() ) {
            $membershipTypes[$membershipType->id] = $membershipType->name; 
        }
        return $membershipTypes;
     }
    
    /**
     * Function to get membership Type Details 
     * 
     * @param int $membershipTypeId
     * @static
     */
    function getMembershipTypeDetails( $membershipTypeId ) 
    {
        require_once 'CRM/Member/DAO/Membership.php';
        $membershipTypeDetails = array();
        
        $membershipType =& new CRM_Member_DAO_MembershipType( );
        $membershipType->is_active = 1;
        $membershipType->id = $membershipTypeId;
        if ( $membershipType->find(true) ) {
            CRM_Core_DAO::storeValues($membershipType, $membershipTypeDetails );
            return   $membershipTypeDetails;
        } else {
            return null;
        }
    }

    /**
     * Function to calculate start date and end date for new membership 
     * 
     * @param int $membershipTypeId
     * @return Array array fo the start date, end date and join date of the membership
     * @static
     */
    function getDatesForMembershipType( $membershipTypeId, $joinDate = null ) 
    {
        $membershipTypeDetails = self::getMembershipTypeDetails( $membershipTypeId );
        $joinDate = $joinDate ? $joinDate : date('Y-m-d');

        if ( $membershipTypeDetails['period_type'] == 'rolling' ) {
            $startDate  = $joinDate;
        } else if ( $membershipTypeDetails['period_type'] == 'fixed' ) {
            $toDay  = explode('-', date('Y-m-d') );
            $month     = substr( $membershipTypeDetails['fixed_period_start_day'], 0, strlen($membershipTypeDetails['fixed_period_start_day'])-2);
            $day       = substr( $membershipTypeDetails['fixed_period_start_day'],-2);
            $year      = $toDay[0];

            if ( $membershipTypeDetails['fixed_period_rollover_day'] != null )
                {
                    $startMonth     = substr( $membershipTypeDetails['fixed_period_start_day'], 0, strlen($membershipTypeDetails['fixed_period_start_day'])-2);
                    $startDay       = substr( $membershipTypeDetails['fixed_period_start_day'],-2);
                    if ($startMonth > $toDay[1]  ) {
                        $year  = $year - 1;
                    } else if ( $startMonth == $toDay[1] && $startDay >= $toDay[2]) {
                        $year  = $year - 1;
                    }
                }
            $startDate = $year.'-'.$month.'-'.$day;
        }
       
        if ( $membershipTypeDetails['period_type'] == 'fixed' && $membershipTypeDetails['fixed_period_rollover_day'] != null ) {
            $toDay  = explode('-', date('Y-m-d'));
            $month     = substr( $membershipTypeDetails['fixed_period_rollover_day'], 0, strlen($membershipTypeDetails['fixed_period_rollover_day'])-2);
            $day       = substr( $membershipTypeDetails['fixed_period_rollover_day'],-2);
            if ( $month < $toDay[1] ) {
                $fixed_period_rollover = true;
            } else if ( $month == $toDay[1] && $day <= $toDay[2]) {
                $fixed_period_rollover = true;
            } else {
                $fixed_period_rollover = false;
            }
                
        }
               
        $date  = explode('-', $startDate );
        $year  = $date[0];
        $month = $date[1];
        $day   = $date[2];
        
        switch ( $membershipTypeDetails['duration_unit'] ) {
            
        case 'year' :
            if ( $fixed_period_rollover ) {
                $year  = $year   + 2*$membershipTypeDetails['duration_interval'];
            } else {
                $year  = $year   + $membershipTypeDetails['duration_interval'];
            }
            break;
        case 'month':
            if( $fixed_period_rollover ) {
                $month = $month  + 2*$membershipTypeDetails['duration_interval'];
            } else {
                $month = $month  + $membershipTypeDetails['duration_interval'];
            }
            break;
        case 'day':
            if ( $fixed_period_rollover ) {
                $day   = $day    + 2*$membershipTypeDetails['duration_interval'];
            } else {
                $day   = $day    + $membershipTypeDetails['duration_interval'];
            }
            break;
            
        }

        if ( $membershipTypeDetails['duration_unit'] =='lifetime' ) {
            $endDate = null;
        } else {
            $endDate = date('Y-m-d',mktime($hour, $minute, $second, $month, $day-1, $year));
        }
        $membershipDates = array();
        $membershipDates['start_date']  = CRM_Utils_Date::customFormat($startDate,'%Y%m%d');
        $membershipDates['end_date']    = CRM_Utils_Date::customFormat($endDate,'%Y%m%d');
        $membershipDates['join_date']   = CRM_Utils_Date::customFormat($joinDate,'%Y%m%d');
        return $membershipDates;
        
    }

    /**
     * Function to calculate start date and end date for renewal membership 
     * 
     * @param int $membershipId 
     *
     * @return Array array fo the start date, end date and join date of the membership
     * @static
     */
    function getRenewalDatesForMembershipType( $membershipId ) 
    {
        require_once 'CRM/Member/BAO/Membership.php';
        require_once 'CRM/Member/BAO/MembershipStatus.php';
        $param = array('id' => $membershipId);
        $membershipDetails     =  CRM_Member_BAO_Membership::getValues( $param, $values ,$ids );
        $statusID = $membershipDetails->status_id;
        $membershipTypeDetails = self::getMembershipTypeDetails( $membershipDetails->membership_type_id );
        $statusDetails  = CRM_Member_BAO_MembershipStatus::getMembershipStatus($statusID);
        if ( $statusDetails['is_current_member'] == 1 ) {
            $startDate    = $membershipDetails->start_date;
            $date         = explode('-', $membershipDetails->end_date);
            $logStartDate = date('Y-m-d',mktime($hour, $minute, $second, $date[1], $date[2]+1, $date[0]));
            $date         = explode('-', $logStartDate );
            
            $year  = $date[0];
            $month = $date[1];
            $day   = $date[2];
            
            switch ( $membershipTypeDetails['duration_unit'] ) {
            case 'year' :
                $year  = $year   + $membershipTypeDetails['duration_interval'];
                break;
            case 'month':
                $month = $month  + $membershipTypeDetails['duration_interval'];
                break;
            case 'day':
                $day   = $day    + $membershipTypeDetails['duration_interval'];
                break;
            }
            if ( $membershipTypeDetails['duration_unit'] =='lifetime') {
                $endDate = null;
            } else {
                $endDate = date('Y-m-d',mktime($hour, $minute, $second, $month, $day-1, $year));
            }
                
        } else {
            $date = $membershipDetails->end_date;
            $date         = explode('-', $date );
            $startDate = $logStartDate = date('Y-m-d',mktime($hour, $minute, $second, $date[1], $date[2]+1, $date[0]));
           
            $date         = explode('-', $startDate);
            
            $year  = $date[0];
            $month = $date[1];
            $day   = $date[2];
            switch ( $membershipTypeDetails['duration_unit'] ) {
            case 'year' :
                $year  = $year   + $membershipTypeDetails['duration_interval'];
                break;
            case 'month':
                $month = $month  + $membershipTypeDetails['duration_interval'];
                break;
            case 'day':
                $day   = $day    + $membershipTypeDetails['duration_interval'];
                break;
            }
            if ($membershipTypeDetails['duration_unit'] =='lifetime') {
                $endDate = null;
            } else {
                $endDate = date('Y-m-d',mktime($hour, $minute, $second, $month, $day-1, $year));
            }
        }
        
        $membershipDates = array();
        $membershipDates['start_date']      =  CRM_Utils_Date::customFormat($startDate,'%Y%m%d');
        $membershipDates['end_date'  ]      =  CRM_Utils_Date::customFormat($endDate,'%Y%m%d');
        $membershipDates['log_start_date' ] =  CRM_Utils_Date::customFormat($logStartDate,'%Y%m%d');
        
        return $membershipDates;
    }
}

?>
