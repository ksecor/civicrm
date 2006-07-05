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
       
        if( $field ) {
            $fieldDAO = & new CRM_Core_DAO_UFField();
            $fieldDAO->id = $fid;
            $fieldDAO->find(true);
           
            $name = $fieldDAO->field_name;
            
            if ($fieldDAO->location_type_id) {
                $name .= '-'.$fieldDAO->location_type_id;
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
        // add the form elements
        require_once "CRM/Contribute/PseudoConstant.php";
        foreach ($this->_fields as $name => $field ) {
            $required = $field['is_required'];

            if ( substr($field['name'],0,14) === 'state_province' ) {
                $this->add('select', $name, $field['title'],
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::stateProvince(), $required);
            } else if ( substr($field['name'],0,7) === 'country' ) {
                $this->add('select', $name, $field['title'], 
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::country(), $required);
            } else if ( $field['name'] === 'birth_date' ) {  
                $this->add('date', $field['name'], $field['title'], CRM_Core_SelectValues::date('birth'), $required );  
            } else if ( $field['name'] === 'gender' ) {  
                $genderOptions = array( );   
                $gender = CRM_Core_PseudoConstant::gender();   
                foreach ($gender as $key => $var) {   
                    $genderOptions[$key] = HTML_QuickForm::createElement('radio', null, ts('Gender'), $var, $key);   
                }   
                $this->addGroup($genderOptions, $field['name'], $field['title'] );  
                if ($required) {
                    $this->addRule($field['name'], ts('%1 is a required field.', array(1 => $field['title'])) , 'required');
                }
            } else if ( $field['name'] === 'individual_prefix' ){
                $this->add('select', $name, $field['title'], 
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::individualPrefix(), $required);
            } else if ( $field['name'] === 'individual_suffix' ){
                $this->add('select', $name, $field['title'], 
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::individualSuffix(), $required);
            } else if ($field['name'] === 'preferred_communication_method') {
                $this->add('select', $name, $field['title'],
                           array('' => ts('- select -')) + CRM_Core_SelectValues::pcm());
            } else if ($field['name'] === 'preferred_mail_format') {
                $this->add('select', $name, $field['title'], 
                           array(CRM_Core_SelectValues::pcm()));
            } else if ( substr($field['name'], 0, 7) === 'do_not_' ) {  
                $this->add('checkbox', $name, $field['title'], $field['attributes'], $required );
            } else if ( $field['name'] === 'group' ) {
                require_once 'CRM/Contact/Form/GroupTag.php';
                CRM_Contact_Form_GroupTag::buildGroupTagBlock($this, $this->_id, CRM_Contact_Form_GroupTag::GROUP, false, $required, $field['title'], null );
            } else if ( $field['name'] === 'tag' ) {
                require_once 'CRM/Contact/Form/GroupTag.php';
                CRM_Contact_Form_GroupTag::buildGroupTagBlock($this, $this->_id,  CRM_Contact_Form_GroupTag::TAG, false, $required, null, $field['title'] );
            } else if ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($field['name'])) {
                CRM_Core_BAO_CustomField::addQuickFormElement($this, $name, $customFieldID, $inactiveNeeded, $required, false, $field['title']);
            } else if ( in_array($field['name'], array('receive_date', 'receipt_date', 'thankyou_date', 'cancel_date' )) ) {  
                $this->add('date', $field['name'], $field['title'], CRM_Core_SelectValues::date('manual', 3, 1), $required );  
            } else if ($field['name'] == 'payment_instrument' ) {
                $this->add('select', 'payment_instrument', ts( 'Paid By' ),
                           array(''=>ts( '-select-' )) + CRM_Contribute_PseudoConstant::paymentInstrument( ), $required );
            } else if ($field['name'] == 'contribution_type' ) {
                $this->add('select', 'contribution_type', ts( 'Contribution Type' ), 
                           array(''=>ts( '-select-' )) + CRM_Contribute_PseudoConstant::contributionType( ), $required);
            } else if ($field['name'] == 'gpa_id' ) {
                require_once 'CRM/Core/OptionGroup.php';
                $this->add('select', 'gpa_id', ts( $field['title'] ), 
                           array(''=>ts( '-select-' )) + CRM_Core_OptionGroup::values('gpa'), $required);
            } else if ($field['name'] == 'ethnicity_id_1' ) {
                require_once 'CRM/Core/OptionGroup.php';
                $this->add('select', 'ethnicity_id_1', ts( $field['title'] ),
                           array(''=>ts( '-select-' )) + CRM_Core_OptionGroup::values('ethnicity'), $required);
            } else {
                $this->add('text', $name, $field['title'], $field['attributes'], $required);
            }
        }
     
        require_once 'CRM/Core/DAO/UFGroup.php';
        $dao = new CRM_Core_DAO_UFGroup();
        $dao->id = $this->_gid;
        $dao->find(true);
        if ( $dao->add_captcha ) {
            require_once 'CRM/Utils/CAPTCHA.php';
            $captcha =& CRM_Utils_CAPTCHA::singleton( );
            $captcha->add( $this );
            $this->assign( 'addCAPTCHA' , true );
        }
        
        $this->addButtons(array(
                                array ('type'      => 'cancel',
                                       'name'      => ts('Done with Preview'),
                                       'isDefault' => true),
                                )
                          );
    }

}

?>
