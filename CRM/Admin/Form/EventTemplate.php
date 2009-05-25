<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.3                                                |
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
require_once 'CRM/Event/BAO/Event.php';

/**
 * This class handles event templates.
 * 
 */
class CRM_Admin_Form_EventTemplate extends CRM_Admin_Form
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
        
        //get the attributes.
        $attributes = CRM_Core_DAO::getAttribute( 'CRM_Event_DAO_Event' );
        
        require_once 'CRM/Event/PseudoConstant.php';
        
        //build form
        $this->add( 'text', 'template_title', ts('Title'), $attributes['template_title'], true );
        
        $this->add( 'select', 'event_type_id', 
                    ts( 'Event Type' ),
                    array('' => ts('- select -')) + CRM_Event_PseudoConstant::eventType( ),
                    true );
        
        $this->add( 'select', 'default_role_id', 
                    ts( 'Participant Role' ),
                    array('' => ts('- select -')) + CRM_Event_PseudoConstant::participantRole( ),
                    true );
        
        $this->add( 'select', 'participant_listing_id', 
                    ts( 'Participant Listing' ),
                    array('' => ts('- select -')) + CRM_Event_PseudoConstant::participantListing( ) );
        
        $this->add('checkbox', 'is_public', ts('Public Event?') ); 
        
        $this->add('checkbox', 'is_monetary', ts('Paid Event?') ); 
        
        $this->add('checkbox', 'is_online_registration', ts('Allow Online Registration?') ); 
        
        $this->add('checkbox', 'is_active', ts('Is This Event Active?')); 
        
    }
    
    /** 
     * Function to process the form 
     * 
     * @access public 
     * @return None 
     */ 
    function postProcess( ) 
    {
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            CRM_Event_BAO_Event::del( $this->_id );
            CRM_Core_Session::setStatus( ts('Selected Event Template has been deleted.') );
            return;
        }
        
        //get the submitted form values.  
        $formValues = $this->controller->exportValues( $this->_name );
        
        //form fields.
        $fields = array( 'template_title', 
                         'event_type_id',
                         'default_role_id',
                         'participant_listing_id',
                         'is_public',
                         'is_monetary',
                         'is_online_registration',
                         'is_active'
                         );
        
        $params = array( );
        foreach ( $fields as $f ) {
            if ( in_array( $f, array( 'is_public', 'is_monetary', 'is_online_registration', 'is_active' ) ) ) {
                $params[$f] = CRM_Utils_Array::value( $f, $formValues, false );
            } else {
                $params[$f] = CRM_Utils_Array::value( $f, $formValues );
            }
        }
        $params['is_template'] = true;
        
        // assign id only in update mode
        $status = ts( 'Your New  Event Template has been saved.' );
        if ( $this->_action & CRM_Core_Action::UPDATE ) { 
            $params['id'] = $this->_id;
            $status = ts( 'Your Event Template have been updated.' );
        }
        
        //ceate event template.
        $eventTemplate = CRM_Event_BAO_Event::create( $params );
        
        if ( $eventTemplate->id ) {
            CRM_Core_Session::setStatus( $status );
        } else {
            CRM_Core_Session::setStatus( ts( 'Your changes are not saved.') ); 
        }
    }
}
