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
class CRM_Member_Form_MembershipStatus extends CRM_Member_Form
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
        $this->add('text', 'name', ts('Name'), CRM_Core_DAO::getAttribute( 'CRM_Member_DAO_MembershipStatus', 'name' ) );
        $this->addRule( 'name', ts('Please enter a valid membership status name.'), 'required' );
        $this->addRule( 'name', ts('A membership status with this name already exists. Please select another name.'), 
                        'objectExists', array( 'CRM_Member_DAO_MembershipStatus', $this->_id ) );

        $this->add('select', 'start_event', ts('Start Event'), CRM_Core_SelectValues::eventDate( ) );
        $this->add('select', 'start_event_adjust_unit', ts('Start Event Adjust'), CRM_Core_SelectValues::unitList( ) );
        $this->add('text', 'start_event_adjust_interval', ts('Start Event Adjust Interval'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Member_DAO_MembershipStatus', 'start_event_adjust_interval' ) );
        $this->add('select', 'end_event', ts('End Event'), CRM_Core_SelectValues::eventDate( ) );
        $this->add('select', 'end_event_adjust_unit', ts('End Event Adjust'), CRM_Core_SelectValues::unitList( ) );
        $this->add('text', 'end_event_adjust_interval', ts('End Event Adjust Interval'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Member_DAO_MembershipStatus', 'end_event_adjust_interval' ) );
        $this->add('checkbox', 'is_current_member', ts('Is Current Member?'));
        $this->add('checkbox', 'is_admin', ts('Is Admin?'));

        $this->add('text', 'weight', ts('Weight'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Member_DAO_MembershipStatus', 'weight' ) );
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
        require_once 'CRM/Member/BAO/MembershipStatus.php';
        if($this->_action & CRM_Core_Action::DELETE) {
            CRM_Member_BAO_MembershipStatus::del($this->_id);
            CRM_Core_Session::setStatus( ts('Selected membership status has been deleted.') );
        } else { 
            $params = $ids = array( );
            // store the submitted values in an array
            $params = $this->exportValues();

            if ($this->_action & CRM_Core_Action::UPDATE ) {
                $ids['membershipStatus'] = $this->_id;
            }

            $membershipStatus = CRM_Member_BAO_MembershipStatus::add($params, $ids);
            CRM_Core_Session::setStatus( ts('The membership status "%1" has been saved.', array( 1 => $membershipStatus->name )) );
        }
    }
}

?>
