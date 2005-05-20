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
    public function buildQuickForm( $form )
    {
        $form->applyFilter('__ALL__','trim');
        
        // prefix
        $form->addElement('select', 'prefix', null, CRM_Core_SelectValues::$prefixName);

        $attributes = CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Individual');

        // first_name
        $form->addElement('text', 'first_name', 'First Name', $attributes['first_name'] );
        
        // last_name
        $form->addElement('text', 'last_name', 'Last Name', $attributes['last_name'] );
        
        // suffix
        $form->addElement('select', 'suffix', null, CRM_Core_SelectValues::$suffixName);
        
        // greeting type
        $form->addElement('select', 'greeting_type', 'Greeting type :', CRM_Core_SelectValues::$greeting);
        
        // job title
        $form->addElement('text', 'job_title', 'Job title :', $attributes['job_title']);
        
        // radio button for gender
        $genderOptions = array( );
        $genderOptions[] = HTML_QuickForm::createElement('radio', 'gender', 'Gender', 'Female', 'Female');
        $genderOptions[] = HTML_QuickForm::createElement('radio', 'gender', 'Gender', 'Male', 'Male');
        $genderOptions[] = HTML_QuickForm::createElement('radio', 'gender', 'Gender', 'Transgender','Transgender');
        $form->addGroup( $genderOptions, 'gender', 'Gender' );
        
        $form->addElement('checkbox', 'is_deceased', null, 'Contact is deceased');
        
        $form->addElement('date', 'birth_date', 'Date of birth', CRM_Core_SelectValues::$date);
        $form->addRule('birth_date', 'Select a valid date.', 'qfDate' );

        $config = CRM_Core_Config::singleton( );
        CRM_Core_ShowHideBlocks::links( $this, 'demographics', '<img src="'.$config->resourceBase.'i/TreePlus.gif" class="action-icon" alt="open section">' , '<img src="'.$config->resourceBase.'i/TreeMinus.gif" class="action-icon" alt="close section">'  );
    }

    static function formRule( &$fields ) {
        $errors = array( );
        
        $primaryEmail = CRM_Contact_Form_Edit::formRule( $fields, $errors );
        
        // check for state/country mapping
        CRM_Contact_Form_Address::formRule($fields, $errors);

        // make sure that firstName and lastName or a primary email is set
        if (! ( (CRM_Utils_Array::value( 'first_name', $fields ) && 
                 CRM_Utils_Array::value( 'last_name' , $fields )    ) ||
                !empty( $primaryEmail ) ) ) {
            $errors['first_name'] = "First Name and Last Name OR an email in the Primary Location should be set.";
        }
        
        // add code to make sure that the uniqueness criteria is satisfied
        return empty($errors) ? true : $errors;
    }
}
   
?>