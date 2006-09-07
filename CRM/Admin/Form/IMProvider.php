<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
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

require_once 'CRM/Admin/Form.php';
require_once 'CRM/Core/BAO/OptionGroup.php';

/**
 * This class generates form components generic to IM provider
 * 
 */
class CRM_Admin_Form_IMProvider extends CRM_Admin_Form
{
    /**
     * Function to for pre-processing
     *
     * @return None
     * @access public
     */
    public function preProcess( ) 
    {
        parent::preProcess( );
        $groupParams = array( 'name' => 'instant_messenger_service' );
        $optionGroup = CRM_Core_BAO_OptionGroup::retrieve($groupParams, $dnc);
        $this->_gid = $optionGroup->id;
    }

    /**
     * Function to actually build the form
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
        $this->add('text', 'name'       , ts('Name')       ,
                   CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_OptionValue', 'name' ) );
        $this->addRule( 'name', ts('Please enter a valid name.'), 'required' );
        $this->addRule( 'name', ts('Name already exists in Database.'), 'optionExists', array( 'CRM_Core_DAO_OptionValue', $this->_id, $this->_gid ) );

        $this->add('checkbox', 'is_active', ts('Enabled?'));

        
    }
    
    /**
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        if($this->_action & CRM_Core_Action::DELETE) {
            CRM_Core_BAO_OptionValue::del($this->_id);
            CRM_Core_Session::setStatus( ts('Selected IM Provider has been deleted.') );
        } else { 
            $params = $this->exportValues();
            $groupParams = array( 'name' => 'instant_messenger_service' );
            
            require_once 'CRM/Core/OptionValue.php';
            $optionValue = CRM_Core_OptionValue::addOptionValue($params, $groupParams, $this->_action, $this->_id);
            
            CRM_Core_Session::setStatus( ts('The IM Provider "%1" has been saved.',
                                            array( 1 => $optionValue->name ) ) );
        }
    }//end of function


}

?>
