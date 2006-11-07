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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Member/Form.php';

/**
 * This class generates form components for Membership Type
 * 
 */
class CRM_Member_Form_MessageTemplates extends CRM_Member_Form
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
        $this->add('text', 'msg_title', ts('Message Title'), CRM_Core_DAO::getAttribute( 'CRM_Member_DAO_MessageTemplates', 'msg_title' ) );
        $this->addRule( 'msg_title', ts('Please enter a valid Message Template name.'), 'required' );
        

        
        $this->add('text', 'msg_subject', ts('Message Subject'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Member_DAO_MessageTemplates', 'msg_subject' ) );
        $this->add('textarea', 'msg_text', ts('Text Message'), 
                   "cols=40 rows=3" );
        $this->add('textarea', 'msg_html', ts('HTML Message'), "cols=40 rows=3"
                    );

      
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
        require_once 'CRM/Member/BAO/MessageTemplates.php';
        if($this->_action & CRM_Core_Action::DELETE) {
            CRM_Member_BAO_MessageTemplates::del($this->_id);
            CRM_Core_Session::setStatus( ts('Selected message templates has been deleted.') );
        } else { 
            $params = $ids = array( );
            // store the submitted values in an array
            $params = $this->exportValues();

            if ($this->_action & CRM_Core_Action::UPDATE ) {
                $ids['messageTemplate'] = $this->_id;
            }

            $messageTemplate = CRM_Member_BAO_MessageTemplates::add($params, $ids);
            CRM_Core_Session::setStatus( ts('The Message Template "%1" has been saved.', array( 1 => $messageTemplate->msg_title )) );
        }
    }
}

?>
