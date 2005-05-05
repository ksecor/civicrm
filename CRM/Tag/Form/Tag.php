<?php
/**
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/SelectValues.php';
require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for tags
 * 
 */
class CRM_Tag_Form_Tag extends CRM_Form
{

    /**
     * The contact id, used when add/edit tag
     *
     * @var int
     */
    protected $_contactId;
    

    /**
     * class constructor
     *
     * @param string $name        Name of the form.
     * @param string $state       The state object associated with this form
     * @param int     $mode       The mode of the form
     *
     * @return CRM_Tag_Form_Tag
     * @access public
     */
    function __construct($name, $state, $mode = self::MODE_NONE) 
    {
        parent::__construct($name, $state, $mode);
    }
    
    function preProcess( ) 
    {
        $this->_contactId   = $this->get('contactId');
    }

    /**
     * This function sets the default values for the form. Tag that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        $defaults = array( );
        $params   = array( );

        return $defaults;
    }
    

    /**
     * This function is used to add the rules for form.
     *
     * @return None
     * @access public
     */
    function addRules( )
    {
        
    }


    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        // get categories for the contact id
        $entityCategory =& CRM_Contact_BAO_EntityCategory::getCategory('crm_contact', $this->_contactId);
        
        // get the list of all the categories
        $category =& CRM_PseudoConstant::category();
        
        // need to append the array with the " checked " if contact is tagged with the category
        foreach ($category as $categoryID => $varValue) {
            $strChecked = '';
            if( in_array($categoryID, $entityCategory)) {
                $strChecked = 'checked';
            }
            $categoryChk[$categoryID] = $this->createElement('checkbox', $categoryID, '', '', $strChecked);            
        }
        
        $this->addGroup($categoryChk, 'categoryList');
        
        $this->assign('category', $category);
        
        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => 'Update Tags',
                                         'isDefault' => true   ),
                                 array ( 'type'       => 'cancel',
                                         'name'      => 'Cancel' ),
                                 )
                           );
    }

       
    /**
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        $data = $aEntityCategory = $aCategory = $params = array();
        // store the submitted values in an array
        $data = $this->exportValues();

        // get categories for the contact id
        $aEntityCategory =& CRM_Contact_BAO_EntityCategory::getCategory('crm_contact', $this->_contactId);

        // get the list of all the categories
        $aCategory =& CRM_PseudoConstant::category();

        // array contains the posted values
        // exportvalues is not used because its give value 1 of the checkbox which were checked by default, 
        // even after unchecking them before submitting them
        //  $aContactCategory = $data['categoryList'];
        $aContactCategory = $_POST['categoryList'];

        // check which values has to be inserted/deleted for contact
        foreach ($aCategory as $lngKey => $var_value) {
            $params['entity_id'] = $this->_contactId;
            $params['entity_table'] = 'crm_contact';
            $params['category_id'] = $lngKey;
            
            if (array_key_exists($lngKey, $aContactCategory) && !array_key_exists($lngKey, $aEntityCategory) ) {
                // insert a new record
                //$objName->save();
                CRM_Contact_BAO_EntityCategory::add($params);
            } else if (!array_key_exists($lngKey, $aContactCategory) && array_key_exists($lngKey, $aEntityCategory) ) {
                // delete a record for existing contact
                //$objName->delete();
                CRM_Contact_BAO_EntityCategory::del($params);
            }
            
        }

    }//end of function

}

?>
