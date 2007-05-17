<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * Definition of CRM API for Membership.
 * More detailed documentation can be found 
 * {@link http://objectledge.org/confluence/display/CRM/CRM+v1.0+Public+APIs
 * here}
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

/**
 * Files required for this package
 */
require_once 'api/v2/utils.php';

/**
 * Create a Membership Type
 *  
 * This API is used for creating a Membership Type
 * 
 * @param   array  $params  an associative array of name/value property values of civicrm_membership_type
 * 
 * @return array of newly created membership type property values.
 * @access public
 */
function civicrm_membership_type_create($params) 
{
    _civicrm_initialize();
    if ( ! is_array($params) ) {
        return civicrm_create_error('Params is not an array.');
    }
    
    if (!$params["name"] && ! $params['duration_unit'] && ! $params['duration_interval']) {
        return civicrm_create_error('Missing require fileds ( name, duration unit,duration interval)');
    }
    
    if ( !$params['domain_id'] ) {
        require_once 'CRM/Core/Config.php';
        $config =& CRM_Core_Config::singleton();
        $params['domain_id'] = $config->domainID();
    }
   
    $error = _civicrm_check_required_fields( $params, 'CRM_Member_DAO_MembershipType' );
    if ($error['is_error']) {
        return civicrm_create_error( $error['error_message'] );
    }
    
    $ids['membershipType']   = $params['id'];
    $ids['memberOfContact']  = $params['member_of_contact_id'];
    $ids['contributionType'] = $params['contribution_type_id'];
    
    require_once 'CRM/Member/BAO/MembershipType.php';
    $membershipTypeBAO = CRM_Member_BAO_MembershipType::add($params, $ids);

    if ( is_a( $membershipTypeBAO, 'CRM_Core_Error' ) ) {
        return civicrm_create_error( "Membership is not created" );
    } else {
        $member = array();
        _civicrm_object_to_array($membershipTypeBAO, $member);
        $values = array( );
        $values['member_id'] = $member['id'];
        $values['is_error']   = 0;
    }
    
    return $values;
}


?>
