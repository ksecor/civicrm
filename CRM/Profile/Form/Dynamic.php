<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
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
 * This class generates form components for custom data
 * 
 * It delegates the work to lower level subclasses and integrates the changes
 * back in. It also uses a lot of functionality with the CRM API's, so any change
 * made here could potentially affect the API etc. Be careful, be aware, use unit tests.
 *
 */

require_once 'CRM/Profile/Form.php';

class CRM_Profile_Form_Dynamic extends CRM_Profile_Form
{
    /** 
     * pre processing work done here. 
     * 
     * @param 
     * @return void 
     * 
     * @access public 
     * 
     */ 
    function preProcess() 
    { 
        if ( $this->get( 'register' ) ) {
            $this->_mode = CRM_Profile_Form::MODE_REGISTER;
        } else {
            $this->_mode = CRM_Profile_Form::MODE_EDIT;
        }
         
        parent::preProcess( ); 
    } 

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        $this->addButtons(array(
                                array ('type'      => 'submit',
                                       'name'      => ts('Save'),
                                       'isDefault' => true)
                                )
                          );

        // also add a hidden element for to trick drupal
        $this->addElement('hidden', "edit[civicrm_dummy_field]", "CiviCRM Dummy Field for Drupal" );
        parent::buildQuickForm( ); 

        if ( $this->_mode == CRM_Profile_Form::MODE_REGISTER ) {
            $this->addFormRule( array( 'CRM_Profile_Form_Dynamic', 'formRule' ), -1 );
        } else {
            $this->addFormRule( array( 'CRM_Profile_Form_Dynamic', 'formRule' ), $this->_id );
        }
    }

    /**
     * global form rule
     *
     * @param array $fields the input form values
     * @param array $files   the uploaded files if any
     * @param array $options additional user data
     *
     * @return true if no errors, else array of errors
     * @access public
     * @static
     */
    static function formRule( &$fields, &$files, $options ) {
        $errors = array( );
        
        // if no values, return
        if ( empty( $fields ) || ! CRM_Utils_Array::value( 'edit', $fields ) ) {
            return true;
        }

        return CRM_Profile_Form::formRule( $fields, $files, $options );
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

        if ( $this->_contact ) {
            foreach ( $this->_fields as $name => $field ) {
                $objName = $field['name'];
                if ( $objName == 'state_province' ) {
                    $states =& CRM_Core_PseudoConstant::stateProvince( );
                    if ( $this->_contact->state_province ) {
                        $defaults[$name] = array_search( $this->_contact->state_province, $states );
                    }
                } else if ( $objName == 'country' ) {
                    $country =& CRM_Core_PseudoConstant::country( );
                    if ( $this->_contact->country ) {
                        $defaults[$name] = array_search( $this->_contact->country, $country );
                    }
                } else if ( $objName == 'gender' ) {
                    $defaults[$name] = $this->_contact->gender_id;
                } else if ( $objName == 'group' ) {
                    CRM_Contact_Form_GroupTag::setDefaults( $this->_id, 
                                                            $defaults,
                                                            CRM_Contact_Form_GroupTag::GROUP ); 
                } else if ( $objName == 'tag' ) { 
                    CRM_Contact_Form_GroupTag::setDefaults( $this->_id, 
                                                            $defaults,
                                                            CRM_Contact_Form_GroupTag::TAG ); 
                } else if ( $cfID = CRM_Core_BAO_CustomField::getKeyID($objName)) {
                    // make sure the custom field exists
                    $cf =& new CRM_Core_BAO_CustomField();
                    $cf->id = $cfID;
                    if ( ! $cf->find( true ) ) {
                        continue;
                    }

                    // make sure the custom value exists
                    $cv =& new CRM_Core_BAO_CustomValue();
                    $cv->custom_field_id = $cfID;
                    $cv->entity_table = 'civicrm_contact';
                    $cv->entity_id = $this->_id;
                    if ( ! $cv->find( true ) ) {
                        $defaults[$name] = $cf->default_value;
                        continue;
                    }

                    switch($cf->html_type) {

                    case "Radio":
                        $defaults[$name] = $cv->getValue(true); 
                        break;
                            
                    case "CheckBox":
                        $customOption = CRM_Core_BAO_CustomOption::getCustomOption($cf->id);    
                        $value = $cv->getValue(true);
                        $checkedData = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $value);
                        foreach($customOption as $val) {
                            $checkVal = $val['value'];
                            $checkName = $name . '[' . $val['label'] .']';
                            if (in_array($val['value'], $checkedData)) {
                                $defaults[$checkName] = 1;
                            } else {
                                $defaults[$checkName] = 0;
                            }
                        }
                        break;

                    case "Select Date":
                        $date = CRM_Utils_Date::unformat($cv->getValue(true));
                        $customValue = $date;
                        $defaults[$name] = $customValue;
                        break;

                    case 'Select State/Province':
                    case 'Select Country':
                        $defaults[$name] = $cv->int_data;
                        break;

                    default:
                        $customValue = $cv->getValue(true);
                        $defaults[$name] = $customValue;
                        break;
                    }
                } else {
                    $defaults[$name] = $this->_contact->$objName;
                }
            }
        }
        return $defaults;
    }

       
    /**
     * Process the user submitted custom data values.
     *
     * @access public
     * @return void
     */
    public function postProcess( ) 
    {
        parent::postProcess( );
    }

}

?>
