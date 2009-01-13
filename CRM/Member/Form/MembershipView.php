<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for Payment-Instrument
 * 
 */
class CRM_Member_Form_MembershipView extends CRM_Core_Form
{
    /**  
     * Function to set variables up before form is built  
     *                                                            
     * @return void  
     * @access public  
     */
    public function preProcess( ) {
        require_once 'CRM/Member/BAO/Membership.php';
        require_once 'CRM/Member/BAO/MembershipType.php';

        $values = array( ); 
        $id = $this->get( 'id' );
        if ( $id ) {
            $params = array( 'id' => $id ); 
            
            CRM_Member_BAO_Membership::retrieve( $params, $values );

            //Provide information about membership source when it is the result of a relationship (CRM-1901)
            $values['owner_membership_id']       = CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_Membership', 
                                                                                $id, 
                                                                                'owner_membership_id' );
            
            if ( isset($values['owner_membership_id']) ) {
                $values['owner_contact_id']      = CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_Membership', 
                                                                                $values['owner_membership_id'], 
                                                                                'contact_id',
                                                                                'id' );
                
                $values['owner_display_name']    = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', 
                                                                                $values['owner_contact_id'], 
                                                                                'display_name',
                                                                                'id' );

                $membershipType = CRM_Member_BAO_MembershipType::getMembershipTypeDetails($values['membership_type_id']);
                $direction =  strrev($membershipType['relationship_direction']);

                $values['relationship']          = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_RelationshipType', 
                                                                                $membershipType['relationship_type_id'], 
                                                                                "name_$direction",
                                                                                'id');
            }

            $memType = CRM_Core_DAO::getFieldValue("CRM_Member_DAO_Membership",$id,"membership_type_id");
            $groupTree =& CRM_Core_BAO_CustomGroup::getTree( 'Membership',$this, $id,0,$memType);
			CRM_Core_BAO_CustomGroup::buildCustomDataView( $this, $groupTree );
        }
        
        if ( $values['is_test'] ) {
            $values['membership_type'] .= ' (test) ';
        }

        $this->assign( $values ); 
    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $this->addButtons(array(  
                                array ( 'type'      => 'next',  
                                        'name'      => ts('Done'),  
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',  
                                        'isDefault' => true   )
                                )
                          );
    }

}


