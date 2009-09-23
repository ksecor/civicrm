<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.0                                                |
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
        
        $this->add('text', 'label_a_b', ts('Relationship Label-A to B') ,
                   CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_RelationshipType', 'label_a_b' ), true );
        $this->addRule( 'label_a_b', ts('Label already exists in Database.'), 
                        'objectExists', array( 'CRM_Contact_DAO_RelationshipType', $this->_id, 'label_a_b' ) );
        
        $this->add('text', 'label_b_a', ts('Relationship Label-B to A'),
                   CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_RelationshipType', 'label_b_a' ) );
        
        $this->addRule( 'label_b_a', ts('Label already exists in Database.'), 
                        'objectExists', array( 'CRM_Contact_DAO_RelationshipType', $this->_id, 'label_b_a' ) );
        
        $this->add('text', 'description', ts('Description'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_RelationshipType', 'description' ) );
        

        require_once 'CRM/Contact/BAO/ContactType.php';

        $topLevelTypes = CRM_Contact_BAO_ContactType::getContactType( );
        $contactTypes = array('' => ts('- select -') );
        $subTypes     = array('' => ts('- select -') );
        foreach ( $topLevelTypes as $name => $value ) {
            $contactTypes[$value['name']] = $value['label'];
            $subTypes[$value['name']] = array('' => ts('- select -') );

            $secondLevelTypes = CRM_Contact_BAO_ContactType::getSubType( $value['name'] );
            foreach ( $secondLevelTypes as $sName => $sValue ) {
                $subTypes[$value['name']][$sValue['name']] = $sValue['label'];
            }
        }

        // add select for contact type
        $contactTypesA =& $this->add('hierselect', 'contact_types_a', ts('Contact Type A') . ' ');
        $contactTypesB =& $this->add('hierselect', 'contact_types_b', ts('Contact Type B') . ' ');

        $contactTypesA->setOptions( array( $contactTypes, $subTypes ) );
        $contactTypesB->setOptions( array( $contactTypes, $subTypes ) );

        $isActive     =& $this->add('checkbox', 'is_active', ts('Enabled?'));
        
        //only selected field should be allow for edit, CRM-4888
        if ( $this->_id &&
             CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_RelationshipType', $this->_id, 'is_reserved' ) ) {
            foreach ( array( 'contactTypeA', 'contactTypeB', 'isActive' ) as $field ) $$field->freeze( );
        }
        
        if ( $this->_action & CRM_Core_Action::VIEW ) {
            $this->freeze( );
            $url = CRM_Utils_System::url('civicrm/admin/reltype&reset=1'); 
            $location  = "window.location='$url'";
            $this->addElement('button', 'done', ts('Done'), array('onclick' => $location));
        }
        
    }

    function setDefaultValues( ) {
        if ( $this->_action != CRM_Core_Action::DELETE &&
             isset( $this->_id ) ) {
            $defaults = $params = array( );
            $params = array( 'id' => $this->_id );
            require_once(str_replace('_', DIRECTORY_SEPARATOR, $this->_BAOName) . ".php");
            eval( $this->_BAOName . '::retrieve( $params, $defaults );' );

            $defaults['contact_types_a'] = array( $defaults['contact_type_a'],
                                                  $defaults['contact_sub_type_a'] );

            $defaults['contact_types_b'] = array( $defaults['contact_type_b'],
                                                  $defaults['contact_sub_type_b'] );
            return $defaults;
        } else {
            return parent::setDefaultValues( );
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

            $params['contact_type_a'] = $params['contact_types_a'][0];
            $params['contact_type_b'] = $params['contact_types_b'][0];

            if ( ! empty( $params['contact_types_a'][1] ) ) {
                $params['contact_sub_type_a'] = $params['contact_types_a'][1];
            } else {
                $params['contact_sub_type_a'] = 'NULL';
            }

            if ( ! empty( $params['contact_types_b'][1] ) ) {
                $params['contact_sub_type_b'] = $params['contact_types_b'][1];
            } else {
                $params['contact_sub_type_b'] = 'NULL';
            }

            CRM_Contact_BAO_RelationshipType::add($params, $ids);

            CRM_Core_Session::setStatus( ts('The Relationship Type has been saved.') );
        }
    }//end of function
}


