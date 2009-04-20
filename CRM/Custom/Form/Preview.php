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

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/BAO/CustomGroup.php';
require_once 'CRM/Core/DAO/CustomField.php';
require_once 'CRM/Core/BAO/CustomOption.php';
/**
 * This class generates form components for previewing custom data
 * 
 * It delegates the work to lower level subclasses and integrates the changes
 * back in. It also uses a lot of functionality with the CRM API's, so any change
 * made here could potentially affect the API etc. Be careful, be aware, use unit tests.
 *
 */
class CRM_Custom_Form_Preview extends CRM_Core_Form
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
            $params   = array('id' => $fieldId);
            $fieldDAO =& new CRM_Core_DAO_CustomField();                    
            CRM_Core_DAO::commonRetrieve('CRM_Core_DAO_CustomField', $params, $defaults);
            
            if ( CRM_Utils_Array::value( 'is_view', $defaults ) ) {
                CRM_Core_Error::statusBounce( ts('This field is view only so it will not display on edit form.') );
            }
            
            if ( CRM_Utils_Array::value( 'is_active', $defaults ) == 0 ) {
                CRM_Core_Error::statusBounce( ts('This field is inactive so it will not display on edit form.') );
            }
            
            $groupTree = array();
            $groupTree[$groupId]['id']     = 0;
            $groupTree[$groupId]['fields'] = array();
            $groupTree[$groupId]['fields'][$fieldId] = $defaults;
            $this->_groupTree = CRM_Core_BAO_CustomGroup::formatGroupTree( $groupTree, 1, $this );
            $this->assign('preview_type', 'field');
        } else {
            $groupTree        = CRM_Core_BAO_CustomGroup::getGroupDetail( $groupId);
            $this->_groupTree = CRM_Core_BAO_CustomGroup::formatGroupTree( $groupTree, true, $this );
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

        require_once 'CRM/Core/BAO/CustomGroup.php';
        CRM_Core_BAO_CustomGroup::setDefaults( $this->_groupTree, $defaults, false, false );

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

        //fix for calendar for date field
        //if date elements are less than 3 then don't show the date picker icon
        foreach ($this->_groupTree as $key1 => $group) { 
            foreach ( $group['fields'] as $key2 => $field) {
                if ( $field['data_type'] == 'Date' && $field['date_parts'] ) {
                    $datePart = explode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR , $field['date_parts']);
                    if ( count( $datePart ) < 3) {
                        $this->_groupTree[$key1]['fields'][$key2]['skip_calendar'] = true;
                    }
                }
            }
        }
        
        $this->assign('groupTree', $this->_groupTree);
        // add the form elements
        require_once 'CRM/Core/BAO/CustomField.php';
        foreach ($this->_groupTree as $group) {
            $groupId = $group['id'];
            foreach ($group['fields'] as $field) {
                // skip all view fields
                if ( CRM_Utils_Array::value( 'is_view', $field ) ) {
                    continue;
                }
                $fieldId = $field['id'];     
                $elementName = $field['element_name'];
                CRM_Core_BAO_CustomField::addQuickFormElement($this, $elementName, $fieldId, false, $field['is_required']);
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

