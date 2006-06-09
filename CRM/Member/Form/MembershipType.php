<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Member/Form.php';

/**
 * This class generates form components for Membership Type
 * 
 */
class CRM_Member_Form_MembershipType extends CRM_Member_Form
{
    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        parent::buildQuickForm( );
        
        if ($this->_action & CRM_Core_Action::DELETE ) { 
            return;
        }

        $this->applyFilter('__ALL__', 'trim');
        $this->add('text', 'name', ts('Name'), CRM_Core_DAO::getAttribute( 'CRM_Member_DAO_MembershipType', 'name' ) );
        $this->addRule( 'name', ts('Please enter a valid membership type name.'), 'required' );
        $this->addRule( 'name', ts('A membership type with this name already exists. Please select another name.'), 
                        'objectExists', array( 'CRM_Member_DAO_MembershipType', $this->_id ) );
        $this->add('text', 'description', ts('Description'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Member_DAO_MembershipType', 'description' ) );
        $this->add('text', 'minimum_fee', ts('Minimum Fee'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Member_DAO_MembershipType', 'minimum_fee' ) );
        $this->add('select', 'duration_unit', ts('Duration Unit') . ' ', CRM_Core_SelectValues::unitList('duration'));
        $this->add('select', 'period_type', ts('Period Type') . ' ', CRM_Core_SelectValues::periodType( ));
        $this->add('text', 'duration_interval', ts('Duration Interval'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Member_DAO_MembershipType', 'duration_interval' ) );
        $this->add('text', 'fixed_period_start_day', ts('Fixed Period Start Day'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Member_DAO_MembershipType', 'fixed_period_start_day' ) );
        $this->add('text', 'fixed_period_rollover_day', ts('Fixed Period Rollover Day'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Member_DAO_MembershipType', 'fixed_period_rollover_day' ) );

        require_once 'CRM/Contribute/PseudoConstant.php';
        $this->add('select', 'contribution_type_id', ts( 'Contribution Type' ), 
                   array(''=>ts( '-select-' )) + CRM_Contribute_PseudoConstant::contributionType( ), false );

        require_once 'CRM/Contact/BAO/Relationship.php';
        $relTypeInd =  CRM_Contact_BAO_Relationship::getContactRelationshipType(null,'null',null,'Individual');
        $relTypeOrg =  CRM_Contact_BAO_Relationship::getContactRelationshipType(null,'null',null,'Organization');
        $relTypeHou =  CRM_Contact_BAO_Relationship::getContactRelationshipType(null,'null',null,'Household');
        $allRelationshipType =array();
        $allRelationshipType = array_merge( $relTypeInd , $relTypeOrg);
        $allRelationshipType = array_merge( $allRelationshipType, $relTypeHou);
        $this->add('select', 'relation_type_id', ts('Relationship Type'),  array('' => ts('- select -')) + $allRelationshipType);

        $this->add( 'select', 'visibility', ts('Visibility'), CRM_Core_SelectValues::ufVisibility( ), false );

        $this->add('checkbox', 'is_default', ts('Default?'));
        $this->add('checkbox', 'is_active', ts('Enabled?'));

    }

       
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        require_once 'CRM/Member/BAO/MembershipType.php';
        if($this->_action & CRM_Core_Action::DELETE) {
            CRM_Member_BAO_MembershipType::del($this->_id);
            CRM_Core_Session::setStatus( ts('Selected membership type has been deleted.') );
        } else { 
            $params = $ids = array( );
            // store the submitted values in an array
            $params = $this->exportValues();

            if ($this->_action & CRM_Core_Action::UPDATE ) {
                $ids['membershipType'] = $this->_id;
            }
            $ids['memberOfContact'] = 101;
            $ids['contributionType'] = 1;
            $membershipType = CRM_Member_BAO_MembershipType::add($params, $ids);
            CRM_Core_Session::setStatus( ts('The membership type "%1" has been saved.', array( 1 => $membershipType->name )) );
        }
    }
}

?>
