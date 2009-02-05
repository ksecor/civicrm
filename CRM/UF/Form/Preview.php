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

/**
 * This class generates form components 
 * for previewing Civicrm Profile Group
 * 
 */
class CRM_UF_Form_Preview extends CRM_Core_Form
{

    /** 
     * The group id that we are editing
     * 
     * @var int 
     */ 
    protected $_gid; 
 
    /** 
     * the fields needed to build this form 
     * 
     * @var array 
     */ 
    protected $_fields; 

    /**
     * pre processing work done here.
     *
     * gets session variables for group or field id
     *
     * @param
     * @return void
     *
     * @access public
     *
     */
    function preProcess()
    {     
        require_once 'CRM/Core/BAO/UFGroup.php';
        $flag = false;
        $field = CRM_Utils_Request::retrieve('field', 'Boolean',
                                             $this, true , 0);
       
        $fid             = $this->get( 'fieldId' ); 
        $this->_gid      = $this->get( 'id' );
        
        if ($field) {
            $this->_fields   = CRM_Core_BAO_UFGroup::getFields( $this->_gid, false, null, null, null, true);
        } else {
            $this->_fields   = CRM_Core_BAO_UFGroup::getFields( $this->_gid );
        }
        
        // preview for field
        $specialFields = array ('street_address','supplemental_address_1', 'supplemental_address_2', 'city', 'postal_code', 'postal_code_suffix', 'geo_code_1', 'geo_code_2', 'state_province', 'country', 'county', 'phone', 'email', 'im' );
        
        if( $field ) {
            require_once 'CRM/Core/DAO/UFField.php';
            $fieldDAO = & new CRM_Core_DAO_UFField();
            $fieldDAO->id = $fid;
            $fieldDAO->find(true);
            
            $name = $fieldDAO->field_name;
            if ($fieldDAO->location_type_id) {
                $name .= '-' . $fieldDAO->location_type_id;
            } else if ( in_array( $name, $specialFields ) ) {
                $name .= '-Primary';
            }
            
            if ($fieldDAO->phone_type) {
                $name .= '-'.$fieldDAO->phone_type;
            }
            
            $fieldArray[$name]= $this->_fields[$name];
            $this->_fields = $fieldArray;
            if (! is_array($this->_fields[$name])) {
                $flag = true;
            }
            $this->assign('previewField',true);
        }
        if ( $flag ) {
            $this->assign('viewOnly',false);
        } else {
            $this->assign('viewOnly',true);
        }
        
        $this->set('fieldId',null);
        $this->assign("fields",$this->_fields); 
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
        require_once "CRM/Profile/Form.php";
        foreach ($this->_fields as $name => $field ) {
            if ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($field['name'])) {
                CRM_Core_BAO_CustomField::setProfileDefaults( $customFieldID, $name, $defaults, null, CRM_Profile_Form::MODE_REGISTER );
            }
        }
        
        //set default for country.
        CRM_Core_BAO_UFGroup::setRegisterDefaults( $this->_fields, $defaults );
        
        return $defaults;
    }

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        require_once 'CRM/Core/BAO/UFGroup.php';
        require_once 'CRM/Profile/Form.php';
        foreach ($this->_fields as $name => $field ) {
            CRM_Core_BAO_UFGroup::buildProfile($this, $field, CRM_Profile_Form::MODE_CREATE );
        }
        
        $this->addButtons(array(
                                array ('type'      => 'cancel',
                                       'name'      => ts('Done with Preview'),
                                       'isDefault' => true),
                                )
                          );
    }
}


