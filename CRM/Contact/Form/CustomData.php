<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/ShowHideBlocks.php';

/**
 * This class generates form components for custom data
 * 
 * It delegates the work to lower level subclasses and integrates the changes
 * back in. It also uses a lot of functionality with the CRM API's, so any change
 * made here could potentially affect the API etc. Be careful, be aware, use unit tests.
 *
 */
class CRM_Contact_Form_CustomData extends CRM_Core_Form
{
    /**
     * The table name, used when editing/creating custom data
     *
     * @var string
     */
    protected $_tableName;

    /**
     * The table id, used when editing/creating custom data
     *
     * @var int
     */
    protected $_tableId;
    
    /**
     * entity type of the table id
     *
     * @var string
     */
    protected $_entityType;

    /**
     * the group tree data
     *
     * @var array
     */
    protected $_groupTree;

    /**
     * Which blocks should we show and hide.
     *
     * @var CRM_Core_ShowHideBlocks
     */
    protected $_showHide;

    /**
     * Array group titles.
     *
     * @var array
     */
    protected $_groupTitle;

    /**
     * Array group display status.
     *
     * @var array
     */
    protected $_groupCollapseDisplay;

    /**
     * the id of the object being viewed (note/relationship etc)
     *
     * @int
     * @access protected
     */
    protected $_groupId;

    /**
     * pre processing work done here.
     *
     * gets session variables for table name, id of entity in table, type of entity and stores them.
     *
     * @param
     * @return void
     *
     * @access public
     *
     */
    function preProcess()
    {
        $this->_tableName  = $this->get('tableName');
        $this->_tableId    = $this->get('tableId');
        $this->_entityType = $this->get('entityType');
        $this->_groupId    = $this->get('groupId');

        // gets all details of group tree for entity
        $this->_groupTree  = CRM_Core_BAO_CustomGroup::getTree($this->_entityType, $this->_tableId, $this->_groupId);
    }

    /**
     * Fix what blocks to show/hide based on the default values set
     *
     * @param    array    array of Group Titles
     * @param    array    array of Group Collapse Display 
     *
     * @return   
     *
     * @access   protected
     */
    
    protected function setShowHide(&$groupTitle, &$groupCollapseDisplay)
    {
        if ( empty( $groupTitle ) ) {
            return;
        }

        $this->_showHide =& new CRM_Core_ShowHideBlocks('','');
        
        foreach ($groupTitle as $key => $title) {
          $showBlocks = $title . '[show]' ;
          $hideBlocks = $title;
           
          if ($groupCollapseDisplay[$key]) {
              $this->_showHide->addShow($showBlocks);
              $this->_showHide->addHide($hideBlocks);
          } else {
              $this->_showHide->addShow($hideBlocks);
              $this->_showHide->addHide($showBlocks);
          }
        }
        $this->_showHide->addToTemplate();
    }
    
    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        $this->assign('groupTree', $this->_groupTree);

        // u need inactive options only when editing stuff, not when displaying them
        // on a per contact basis
        $inactiveNeeded = false;
        
        // add the form elements
        foreach ($this->_groupTree as $group) {
            $_flag = 0;
            
            $this->_groupTitle[]           = $group['title'];
            $this->_groupCollapseDisplay[] = $group['collapse_display'];
            CRM_Core_ShowHideBlocks::links( $this, $group['title'], '', '');
            
            $groupId = $group['id'];
            foreach ($group['fields'] as $field) {

                $fieldId = $field['id'];                
                $elementName = $groupId . '_' . $fieldId . '_' . $field['name']; 

                CRM_Core_BAO_CustomField::addQuickFormElement($this, $elementName, $fieldId, $inactiveNeeded, true);
                
                if ($field['html_type'] == 'Select State/Province' || $field['html_type'] == 'Select Country') {
                    $_flag++;
                }
            }            
        }

        if ( $_flag == 2 ) {
            // add a form rule to check default value
            $this->addFormRule( array( 'CRM_Contact_Form_CustomData', 'formRule' ) );
        }
        $this->addButtons(array(
                                array ( 'type'      => 'next',
                                        'name'      => ts('Save'),
                                        'isDefault' => true   ),
                                array ( 'type'       => 'cancel',
                                        'name'      => ts('Cancel') ),
                                )
                          );
        

        if ($this->_action & ( CRM_Core_Action::VIEW | CRM_Core_Action::BROWSE ) ) {
            $this->freeze();
        }
        $this->setShowHide($this->_groupTitle, $this->_groupCollapseDisplay);        
    }
    
     /**
     * check for correct state / country mapping.
     *
     * @param array reference $fields - submitted form values.
     * @param array reference $errors - if any errors found add to this array. please.
     * @return true if no errors
     *         array of errors if any present.
     *
     * @access protected
     * @static
     */
    static function formRule(&$fields, &$errors )
    {
        $grpId = array();
        foreach ($fields as $k => $v) {
            list($gId, $fId, $elementName) = explode('_', $k, 3);
            if($gId)
                $grpId[] = $gId;
        }
        
        $uniGroupId = array_unique($grpId);
        $groupDetails = array();
        foreach($uniGroupId as $val) {
            $groupDetails[] = CRM_Core_BAO_CustomGroup::getGroupDetail($val);
        }
        
        $_flag = 0;
        foreach ($groupDetails as $value) {
            foreach ($value as $group) {
                $groupId = $group['id'];
                foreach ($group['fields'] as $field) {
                    
                    $fieldId = $field['id'];
                    $elementName = $groupId . '_' . $fieldId . '_' . $field['name'];
                    switch($field['html_type']) {
                case 'Select Country':
                    $country  = $elementName;
                    $_flag++;
                    break;
                    
                    case 'Select State/Province':
                        $stateProvince  = $elementName;
                        $_flag++;
                    break;
                    }
                }
            }
            if ($_flag == 2) {
                if ( array_key_exists($country, $fields) ) {
                    $countryId = $fields[$country];
                }
                
                if ( array_key_exists($stateProvince, $fields) ) {
                    $stateProvinceId = $fields[$stateProvince];
                }
                
                if ($stateProvinceId && $countryId) {
                    $stateProvinceDAO =& new CRM_Core_DAO_StateProvince();
                    $stateProvinceDAO->id = $stateProvinceId;
                    $stateProvinceDAO->find(true);
                    
                    if ($stateProvinceDAO->country_id != $countryId) {
                        // countries mismatch hence display error
                        $stateProvinces = CRM_Core_PseudoConstant::stateProvince();
                        $countries =& CRM_Core_PseudoConstant::country();
                        $errors[$stateProvince] = "State/Province " . $stateProvinces[$stateProvinceId] . " is not part of ". $countries[$countryId] . ". It belongs to " . $countries[$stateProvinceDAO->country_id] . "." ;
                    }
                }
            }
        }
        return empty($errors) ? true : $errors;
    }

    /**
     * Set the default form values
     *
     * @access protected
     * @return array the default array reference
     */
    function &setDefaultValues()
    {
        $defaults = array();
        
        // do we need inactive options ?
        if ($this->_action & ( CRM_Core_Action::VIEW | CRM_Core_Action::BROWSE ) ) {
            $inactiveNeeded = true;
            $viewMode = true;
        } else {
            $viewMode = false;
            $inactiveNeeded = false;
        }

        CRM_Core_BAO_CustomGroup::setDefaults( $this->_groupTree, $defaults, $viewMode, $inactiveNeeded );

        return $defaults;
    }
    
    /**
     * Process the user submitted custom data values.
     *
     * @access public
     * @return void
     */
    public function postProcess() 
    {
        // Get the form values and groupTree
        $fv = $this->controller->exportValues( $this->_name );

        CRM_Core_BAO_CustomGroup::postProcess( $this->_groupTree, $fv );

        // do the updates/inserts
        CRM_Core_BAO_CustomGroup::updateCustomData($this->_groupTree, $this->_entityType, $this->_tableId);
    }
}

?>
