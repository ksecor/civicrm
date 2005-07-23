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
        $form->addElement('select', 'prefix', ts('Prefix'), CRM_Core_SelectValues::prefixName());

        $attributes = CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Individual');

        // first_name
        $form->addElement('text', 'first_name', ts('First Name'), $attributes['first_name'] );
        
        //middle_name
        $form->addElement('text', 'middle_name', ts('Middle Name'), $attributes['middle_name'] );
        
        // last_name
        $form->addElement('text', 'last_name', ts('Last Name'), $attributes['last_name'] );
        
        // suffix
        $form->addElement('select', 'suffix', ts('Suffix'), CRM_Core_SelectValues::suffixName());
        
        // greeting type
        $form->addElement('select', 'greeting_type', ts('Greeting type'), CRM_Core_SelectValues::greeting());
        
        // job title
        $form->addElement('text', 'job_title', ts('Job title'), $attributes['job_title']);
        
        // radio button for gender
        $genderOptions = array( );
        $genderOptions[] = HTML_QuickForm::createElement('radio', null, ts('Gender'), ts('Female'), 'Female');
        $genderOptions[] = HTML_QuickForm::createElement('radio', null, ts('Gender'), ts('Male'), 'Male');
        $genderOptions[] = HTML_QuickForm::createElement('radio', null, ts('Gender'), ts('Transgender'), 'Transgender');
        $form->addGroup($genderOptions, 'gender', ts('Gender'));
        
        $form->addElement('checkbox', 'is_deceased', null, ts('Contact is deceased'));
        
        $form->addElement('date', 'birth_date', ts('Date of birth'), CRM_Core_SelectValues::date('birth'));
        $form->addRule('birth_date', ts('Select a valid date.'), 'qfDate');

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
                    $urls[] = '<a href="' . CRM_Utils_System::url( 'civicrm/contact/edit', 'reset=1&cid=' . $id ) .
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
