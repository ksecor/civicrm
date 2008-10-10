<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';
//require_once 'CRM/Core/BAO/PriceSet.php';
//
//require_once 'CRM/Core/BAO/CustomOption.php';
/**
 * This class generates form components for previewing custom data
 * 
 * It delegates the work to lower level subclasses and integrates the changes
 * back in. It also uses a lot of functionality with the CRM API's, so any change
 * made here could potentially affect the API etc. Be careful, be aware, use unit tests.
 *
 */
class CRM_Price_Form_Preview extends CRM_Core_Form
{
    /**
     * the group tree data
     *
     * @var array
     */
    protected $_groupTree;

    /**
     * pre processing work done here.
     * 
     * gets session variables for group or field id
     * 
     * @param null
     * 
     * @return void
     * @access public
     */
    function preProcess()
    {
       
        // get the controller vars
        $groupId  = $this->get('groupId');
        $fieldId  = $this->get('fieldId');
        
        if ($fieldId) {
            // field preview
            $defaults = array();
            $params = array('id' => $fieldId);
            
            require_once 'CRM/Core/DAO/PriceField.php';
            $fieldDAO =& new CRM_Core_DAO_PriceField();                    

            CRM_Core_DAO::commonRetrieve('CRM_Core_DAO_PriceField', $params, $defaults);
            
            $this->_groupTree = array();
            $this->_groupTree[0]['id'] = 0;
            $this->_groupTree[0]['fields'] = array();
            $this->_groupTree[0]['fields'][$fieldId] = $defaults;
            
            $this->assign('preview_type', 'field');
        } else {
            // group preview
            require_once 'CRM/Core/BAO/PriceSet.php';
            $this->_groupTree  = CRM_Core_BAO_PriceSet::getSetDetail($groupId);       
            $this->assign('preview_type', 'group');
        }
    }
    
    /**
     * Set the default form values
     * 
     * @param null
     * 
     * @return array   the default array reference
     * @access protected
     */
    function &setDefaultValues()
    {
        $defaults = array();
        
        //require_once 'CRM/Core/BAO/PriceSet.php';
        //CRM_Core_BAO_PriceSet::setDefaults( $this->_groupTree, $defaults, false, false );
        
        return $defaults;
    }
    
    /**
     * Function to actually build the form
     * 
     * @param null
     * 
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        $this->assign('groupTree', $this->_groupTree);
        
        // add the form elements
        require_once 'CRM/Core/BAO/PriceField.php';
        
        foreach ($this->_groupTree as $group) {
            if ( is_array( $group['fields'] ) && !empty( $group['fields'] ) ) {
                foreach ($group['fields'] as $field) {
                    $fieldId = $field['id'];                
                    $elementName = 'price_' . $fieldId;
                    CRM_Core_BAO_PriceField::addQuickFormElement($this, $elementName, $fieldId, false, $field['is_required']);
                }
            }
        }
        
        $this->addButtons(array(
                                array ('type'      => 'cancel',
                                       'name'      => ts('Done with Preview'),
                                       'isDefault' => true),
                                )
                          );
    }
}

