<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
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
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Admin/Form/Setting.php';

/**
 * This class generates form components for Component 
 */
class CRM_Admin_Form_Setting_Component extends  CRM_Admin_Form_Setting
{
    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        CRM_Utils_System::setTitle(ts('Settings - Enable Components'));

        $components = $this->_getComponentSelectValues( );

        $include =& $this->addElement('advmultiselect', 'enableComponents', 
                                      ts('Components') . ' ', $components,
                                      array('size' => 5, 
                                            'style' => 'width:150px',
                                            'class' => 'advmultiselect')
                                      );
        
        $include->setButtonAttributes('add', array('value' => ts('Enable >>')));
        $include->setButtonAttributes('remove', array('value' => ts('<< Disable')));     
        
        parent::buildQuickForm();
    }


    private function _getComponentSelectValues( ) 
    {
        $ret = array();
        require_once 'CRM/Core/Component.php';
        $c = CRM_Core_Component::getComponents();
        foreach( $c as $name => $object ) {
            $ret[$name] = $object->info['translatedName'];
        }

        return $ret;
    }


}

?>
