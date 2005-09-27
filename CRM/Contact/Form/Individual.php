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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/SelectValues.php';
require_once 'CRM/Core/ShowHideBlocks.php';

/**
 * Auxilary class to provide support to the Contact Form class. Does this by implementing
 * a small set of static methods
 *
 */
class CRM_Contact_Form_Individual {
    /**
     * This function provides the HTML form elements that are specific to the Individual Contact Type
     * 
     * @access public
     * @return None 
     */
    public function buildQuickForm( &$form )
    {
        $form->applyFilter('__ALL__','trim');
        
        // prefix
        $form->addElement('select', 'prefix_id', ts('Prefix'), array('' => ts('-title-')) + CRM_Core_PseudoConstant::individualPrefix());

        $attributes = CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Individual');

        // first_name
        $form->addElement('text', 'first_name', ts('First Name'), $attributes['first_name'] );
        
        //middle_name
        $form->addElement('text', 'middle_name', ts('Middle Name'), $attributes['middle_name'] );
        
        // last_name
        $form->addElement('text', 'last_name', ts('Last Name'), $attributes['last_name'] );
        
        // suffix
        $form->addElement('select', 'suffix_id', ts('Suffix'), array('' => ts('-suffix-')) + CRM_Core_PseudoConstant::individualSuffix());
        
        // nick_name
        $form->addElement('text', 'nick_name', ts('Nick Name'),
                          CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'nick_name') );

        // greeting type
        $form->addElement('select', 'greeting_type', ts('Greeting'), CRM_Core_SelectValues::greeting());
        
        // job title
        $form->addElement('text', 'job_title', ts('Job title'), $attributes['job_title']);

        // radio button for gender
        $genderOptions = array( );
        $gender =CRM_Core_PseudoConstant::gender();
        foreach ($gender as $key => $var) {
            $genderOptions[$key] = HTML_QuickForm::createElement('radio', null, ts('Gender'), ts($var), $key);
        }
        $form->addGroup($genderOptions, 'gender_id', ts('Gender'));
        
        $form->addElement('checkbox', 'is_deceased', null, ts('Contact is deceased'));
        
        $form->addElement('date', 'birth_date', ts('Date of birth'), CRM_Core_SelectValues::date('birth'));
        $form->addRule('birth_date', ts('Select a valid date.'), 'qfDate');

        $form->addElement('text', 'home_URL', ts('Website'), 
            CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'home_URL') );
       
        $config =& CRM_Core_Config::singleton();
        CRM_Core_ShowHideBlocks::links($this, 'demographics', '' , '');
    }

    /**
     * global form rule
     *
     * @param array $fields  the input form values
     * @param array $files   the uploaded files if any
     * @param array $options additional user data
     *
     * @return true if no errors, else array of errors
     * @access public
     * @static
     */
    static function formRule( &$fields, &$files, $options ) {
        $errors = array( );

        $primaryEmail = CRM_Contact_Form_Edit::formRule( $fields, $errors );
        
        // check for state/country mapping
        CRM_Contact_Form_Address::formRule($fields, $errors);

        // make sure that firstName and lastName or a primary email is set
        if (! ( (CRM_Utils_Array::value( 'first_name', $fields ) && 
                 CRM_Utils_Array::value( 'last_name' , $fields )    ) ||
                ! empty( $primaryEmail ) ) ) {
            $errors['_qf_default'] = ts('First Name and Last Name OR an email in the Primary Location should be set.');
        }

        // if this is a forced save, ignore find duplicate rule
        if ( ! CRM_Utils_Array::value( '_qf_Edit_next_duplicate', $fields ) ) {
            $cid = null;
            if ( $options ) {
                $cid = (int ) $options;
            }
            $ids = CRM_Core_BAO_UFGroup::findContact( $fields, $cid, true );
            if ( $ids ) {
                $urls = array( );
                foreach ( explode( ',', $ids ) as $id ) {
                    $displayName = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $id, 'display_name' );
                    $urls[] = '<a href="' . CRM_Utils_System::url( 'civicrm/contact/view', 'reset=1&action=update&cid=' . $id ) .
                        '">' . $displayName . '</a>';
                }
                $url = implode( ', ',  $urls );
                $errors['_qf_default'] = ts( 'One matching contact was found. You can edit it here: %1', array( 1 => $url, 'count' => count( $ids ), 'plural' => '%count matching contacts were found. You can edit them here: %1' ) );

                // let smarty know that there are duplicates
                $template =& CRM_Core_Smarty::singleton( );
                $template->assign( 'isDuplicate', 1 );
            } else if ( CRM_Utils_Array::value( '_qf_Edit_refresh_dedupe', $fields ) ) {
                // add a session message for no matching contacts
                CRM_Core_Session::setStatus( 'No matching contact found.' );
            }
        }

        return empty($errors) ? true : $errors;
    }
}
   
?>
