<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Member/DAO/MembershipStatus.php';

class CRM_Member_BAO_MembershipStatus extends CRM_Member_DAO_MembershipStatus 
{

    /**
     * static holder for the default LT
     */
    static $_defaultMembershipStatus = null;
    

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
     * @return object CRM_Member_BAO_MembershipStatus object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) 
    {
        $membershipStatus =& new CRM_Member_DAO_MembershipStatus( );
        $membershipStatus->copyValues( $params );
        if ( $membershipStatus->find( true ) ) {
            CRM_Core_DAO::storeValues( $membershipStatus, $defaults );
            return $membershipStatus;
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
        return CRM_Core_DAO::setFieldValue( 'CRM_Member_DAO_MembershipStatus', $id, 'is_active', $is_active );
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
        $params['is_active']          =  CRM_Utils_Array::value( 'is_active', $params, false );
        $params['is_current_member']  =  CRM_Utils_Array::value( 'is_current_member', $params, false );
        $params['is_admin']           =  CRM_Utils_Array::value( 'is_admin', $params, false );
        $params['is_default']         =  CRM_Utils_Array::value( 'is_default', $params, false );
        
        if ( $params['is_default'] ) {// set all other defaults to false. 
            $query = "UPDATE civicrm_membership_status SET `is_default`= FALSE where `domain_id`=".CRM_Core_Config::domainID( );
            $dao =& new CRM_Core_DAO( );
            $dao->query( $query );
        }
        // action is taken depending upon the mode
        $membershipStatus               =& new CRM_Member_DAO_MembershipStatus( );
        $membershipStatus->domain_id    = CRM_Core_Config::domainID( );
        $membershipStatus->copyValues( $params );
        
        $membershipStatus->id = CRM_Utils_Array::value( 'membershipStatus', $ids );

        $membershipStatus->save( );
        return $membershipStatus;
    }
    

    /**
     * Function to get  membership status 
     * 
     * @param int $membershipStatusId
     * @static
     */
    function getMembershipStatus( $membershipStatusId ) 
    {
        $statusDetails = array();
        $membershipStatus             =& new CRM_Member_DAO_MembershipStatus( );
        $membershipStatus->domain_id    = CRM_Core_Config::domainID( );
        $membershipStatus->id = $membershipStatusId;
        if ( $membershipStatus->find(true) ) {
            CRM_Core_DAO::storeValues( $membershipStatus, $statusDetails );
        }
        return $statusDetails;
    }

    /**
     * Function to delete membership Types 
     * 
     * @param int $membershipStatusId
     * @static
     */
    static function del($membershipStatusId) 
    {
        //check dependencies
        //checking if any membership status is present in some other table 
        $check = false;
        
        $dependancy = array( 'Membership', 'MembershipLog' );
        foreach ($dependancy as $name) {
            require_once (str_replace('_', DIRECTORY_SEPARATOR, "CRM_Member_BAO_" . $name) . ".php");
            eval('$dao = new CRM_Member_BAO_' . $name. '();');
            $dao->status_id = $membershipStatusId;
            if ($dao->find(true)) {
                $check = true;
            }
        }
        if ($check) {
            $session =& CRM_Core_Session::singleton();
            CRM_Core_Session::setStatus( ts('This membership status can not be deleted') );
            return CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/admin/member/membershipStatus', "reset=1" ));
        }
        
        //require_once 'CRM/Member/DAO/Membership.php';
        // $query = 'DELETE FROM civicrm_membership 
        //                       WHERE status_id=' . $membershipStatusId . ';';
        //         $membership->query($query);
        

        //delete from membership Type table
        require_once 'CRM/Member/DAO/MembershipStatus.php';
        $membershipStatus =& new CRM_Member_DAO_MembershipStatus( );
        $membershipStatus->id = $membershipStatusId;
        $membershipStatus->delete();
    }

    /**
     * Function to find the membership status based on start date, end date, join date & status date. 
     * 
     * @param  date  $startDate   start date of the member whose membership status is to be calculated. 
     * @param  date  $endDate     end date of the member whose membership status is to be calculated. 
     * @param  date  $joinDate    join date of the member whose membership status is to be calculated. 
     * @param  date  $statusDate  status date of the member whose membership status is to be calculated. 
     *
     * @return 
     * @static
     */
    static function getMembershipStatusByDate( $startDate, $endDate, $joinDate, $statusDate = 'today' ) 
    {
        $membershipDetails = array();
        if ( $statusDate == 'today' ) {
            $statusDate = getDate();
            $statusDate = date('Y-m-d',mktime($statusDate['hours'], $statusDate['minutes'], $statusDate['seconds'], 
                                              $statusDate['mon'], $statusDate['mday'], $statusDate['year']));
        }

        $dates  = array('start', 'end', 'join');
        $events = array('start', 'end');

        foreach ( $dates as $dat ) {
            if (${$dat.'Date'}) {
                $date  = explode('-', ${$dat.'Date'} );
                ${$dat.'Year'}  = $date[0];
                ${$dat.'Month'} = $date[1];
                ${$dat.'Day'}   = $date[2];
            }
        }
        
        /* FIXME: query below (commented) does not work for cases where admin=NULL */
        //$query = "SELECT * FROM `civicrm_membership_status` WHERE `is_active`=1 AND `is_admin`!=1 ORDER BY weight ASC";
        $query = "SELECT * FROM `civicrm_membership_status` WHERE `is_active`=1 ORDER BY weight ASC";
        $membershipStatus =& new CRM_Core_DAO( );
        $membershipStatus->query( $query );
        while ( $membershipStatus->fetch() ) {
            $startEvent = null;
            $endEvent   = null;
            foreach ( $events as $eve ) {
                foreach ( $dates as $dat ) {
                    // calculate start-event/date and end-event/date
                    if ( ($membershipStatus->{$eve.'_event'} == $dat.'_date') && ${$dat.'Date'} ) {
                        if ( $membershipStatus->{$eve.'_event_adjust_unit'} &&  $membershipStatus->{$eve.'_event_adjust_interval'} ) {
                            if ( $membershipStatus->{$eve.'_event_adjust_unit'} == 'month' ) {//add in months
                                ${$eve.'Event'} = date('Y-m-d',mktime($hour, $minute, $second, 
                                                                      ${$dat.'Month'}+$membershipStatus->{$eve.'_event_adjust_interval'},
                                                                      ${$dat.'Day'}, 
                                                                      ${$dat.'Year'}));
                            }
                            if ( $membershipStatus->{$eve.'_event_adjust_unit'} == 'day' ) {//add in days 
                                ${$eve.'Event'} = date('Y-m-d',mktime($hour, $minute, $second, 
                                                                      ${$dat.'Month'},
                                                                      ${$dat.'Day'}+$membershipStatus->{$eve.'_event_adjust_interval'}, 
                                                                      ${$dat.'Year'}));
                            }
                            if ( $membershipStatus->{$eve.'_event_adjust_unit'} == 'year' ) {//add in years
                                ${$eve.'Event'} = date('Y-m-d',mktime($hour, $minute, $second, 
                                                                      ${$dat.'Month'},
                                                                      ${$dat.'Day'}, 
                                                                      ${$dat.'Year'}+$membershipStatus->{$eve.'_event_adjust_interval'}));
                            }
                        } else { // if no interval and unit, present
                            ${$eve.'Event'} = ${$dat.'Date'};
                        }
                    }
                }
            }

            // check if statusDate is in the range of start & end events.
            if ( $startEvent && $endEvent ) {
                if ( ($statusDate >= $startEvent) && ($statusDate <= $endEvent) ) {
                    $membershipDetails['id'] = $membershipStatus->id;
                    $membershipDetails['name'] = $membershipStatus->name;
                }
            } elseif ( $startEvent ) {
                if ( $statusDate >= $startEvent ) {
                    $membershipDetails['id'] = $membershipStatus->id;
                    $membershipDetails['name'] = $membershipStatus->name;
                }
            } elseif ( $endEvent ) {
                if ( $statusDate <= $endEvent ) {
                    $membershipDetails['id'] = $membershipStatus->id;
                    $membershipDetails['name'] = $membershipStatus->name;
                }
            }
            // returns FIRST status record for which status_date is in range.
            if ( $membershipDetails ) { 
                return $membershipDetails;
            }
        } //end fetch
        
        return $membershipDetails;
    }

    /**
     * Function that return the status ids whose is_current_member is set
     *
     * @return 
     * @static
     */
    function getMembershipStatusCurrent() 
    {
        $statusIds  = array();
        require_once 'CRM/Member/DAO/MembershipStatus.php';
        $membershipStatus =& new CRM_Member_DAO_MembershipStatus( );
        $membershipStatus->is_current_member = 1;
        $membershipStatus->find();
        while ( $membershipStatus->fetch() ) {
            $statusIds[] = $membershipStatus->id;
        }
        
        return $statusIds;
    }
    
}
?>