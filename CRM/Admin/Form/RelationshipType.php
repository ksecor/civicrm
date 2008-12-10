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

require_once 'CRM/Admin/Form.php';

/**
 * This class generates form components for Relationship Type
 * 
 */
class CRM_Admin_Form_RelationshipType extends CRM_Admin_Form
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
  
        $this->add('text', 'name_a_b'       , ts('Relationship Label-A to B')       ,
                   CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_RelationshipType', 'name_a_b' ),true );
        $this->addRule( 'name_a_b', ts('Name already exists in Database.'), 'objectExists', array( 'CRM_Contact_DAO_RelationshipType', $this->_id, 'name_a_b' ) );

        $this->add('text', 'name_b_a'       , ts('Relationship Label-B to A')       ,
                   CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_RelationshipType', 'name_b_a' ) );

        $this->addRule( 'name_b_a', ts('Name already exists in Database.'), 'objectExists', array( 'CRM_Contact_DAO_RelationshipType', $this->_id, 'name_b_a' ) );

      
        // add select for contact type
        $this->add('select', 'contact_type_a', ts('Contact Type A') . ' ', CRM_Core_SelectValues::contactType());
        $this->add('select', 'contact_type_b', ts('Contact Type B') . ' ', CRM_Core_SelectValues::contactType());

        $this->add('text', 'description', ts('Description'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_RelationshipType', 'description' ) );
        $this->add('checkbox', 'is_active', ts('Enabled?'));

        if ( $this->_action & CRM_Core_Action::VIEW ) {
            $this->freeze( );
            $url = CRM_Utils_System::url('civicrm/admin/reltype&reset=1'); 
            $location  = "window.location='$url'";
            $this->addElement('button', 'done', ts('Done'), array('onclick' => $location));
        }
  
    }
       
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        if($this->_action & CRM_Core_Action::DELETE) {
            CRM_Contact_BAO_RelationshipType::del($this->_id);
            CRM_Core_Session::setStatus( ts('Selected Relationship type has been deleted.') );
        } else {
            $params = array();
            $ids    = array();
            
            // store the submitted values in an array
            $params = $this->exportValues();
            $params['is_active'] =  CRM_Utils_Array::value( 'is_active', $params, false );
            
            if ($this->_action & CRM_Core_Action::UPDATE ) {
                $ids['relationshipType'] = $this->_id;
            }    
        
            CRM_Contact_BAO_RelationshipType::add($params, $ids);

            CRM_Core_Session::setStatus( ts('The Relationship Type has been saved.') );
        }
    }//end of function
}


