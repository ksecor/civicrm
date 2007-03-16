<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
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
 * @copyright CiviCRM LLC (c) 2004-2007
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

        if ( $this->get( 'skipPermission' ) ) {
            $this->_skipPermission = true;
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
            $this->addFormRule( array( 'CRM_Profile_Form_Dynamic', 'formRule' ), $this );
        } else {
            $this->addFormRule( array( 'CRM_Profile_Form_Dynamic', 'formRule' ), $this );
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
    static function formRule( &$fields, &$files, &$form ) {
        $errors = array( );
        
        // if no values, return
        if ( empty( $fields ) || ! CRM_Utils_Array::value( 'edit', $fields ) ) {
            return true;
        }

        return CRM_Profile_Form::formRule( $fields, $files, $form );
    }

    /**
     * Set the default form values
     *
     * @access protected
     * @return array the default array reference
     */
    function &setDefaultValues()
    {
        return parent::setContactValues( );
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

    function getTemplateFileName() {
        if ( $this->_gid ) {
            $templateFile = "CRM/Profile/Form/{$this->_gid}/Dynamic.tpl";
            if ( CRM_Core_Form::$_template->template_exists( $templateFile ) ) {
                return $templateFile;
            }
        }
        return parent::getTemplateFileName( );
    }

}

?>
