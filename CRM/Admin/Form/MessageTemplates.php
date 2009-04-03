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
 * This class generates form components for Message templates
 * used by memberhsip email and send email
 * 
 */
class CRM_Admin_Form_MessageTemplates extends CRM_Admin_Form
{

    /**
     * This function sets the default values for the form. 
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    public function setDefaultValues( ) {
        $defaults = array( );
        $defaults =& parent::setDefaultValues( );
        return $defaults;
    }

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
        $this->add('text', 'msg_title', ts('Message Title'), CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_MessageTemplates', 'msg_title' ),true );
        
        $this->add('text', 'msg_subject', ts('Message Subject'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_MessageTemplates', 'msg_subject' ) );
        $this->add('textarea', 'msg_text', ts('Text Message'), 
                   "cols=50 rows=6" );
        $this->addWysiwyg( 'msg_html', ts('HTML Message'),
                          CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_MessageTemplates', 'msg_html' ) );
     
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
        require_once 'CRM/Core/BAO/MessageTemplates.php';
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            CRM_Core_BAO_MessageTemplates::del( $this->_id );
        } else { 
            $params = array( );
            
            // store the submitted values in an array
            $params = $this->exportValues();

            if ( $this->_action & CRM_Core_Action::UPDATE ) {
                $params['id'] = $this->_id;
            }
            
            $messageTemplate = CRM_Core_BAO_MessageTemplates::add( $params );
            CRM_Core_Session::setStatus( ts('The Message Template \'%1\' has been saved.', array( 1 => $messageTemplate->msg_title ) ) );
        }
    }
}


