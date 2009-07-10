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

/**
 * form helper class for an Communication Preferences object
 */
class CRM_Contact_Form_Edit_CommunicationPreferences 
{
    /**
     * build the form elements for Communication Preferences object
     *
     * @param CRM_Core_Form $form       reference to the form object
     *
     * @return void
     * @access public
     * @static
     */
    static function buildQuickForm( &$form ) {
        // since the pcm - preferred comminication method is logically
        // grouped hence we'll use groups of HTML_QuickForm

               
        // checkboxes for DO NOT phone, email, mail
        // we take labels from SelectValues
        $privacy = array( );
        $privacyOptions = CRM_Core_SelectValues::privacy( );
        foreach ( $privacyOptions as $name => $label) {
            $privacy[] = HTML_QuickForm::createElement('advcheckbox', $name, null, $label );
        }
        $form->addGroup($privacy, 'privacy', ts('Privacy'), '&nbsp;');
        
        // preferred communication method 
        require_once 'CRM/Core/PseudoConstant.php';
        $commPreff = array();
        $comm = CRM_Core_PseudoConstant::pcm();
        foreach ( $comm as $value => $title ) {
            $commPreff[] = HTML_QuickForm::createElement('advcheckbox', $value, null, $title );
        }
        $form->addGroup($commPreff, 'preferred_communication_method', ts('Method'));
        
        //using for display purpose.
        $form->assign( 'commPreference', array( 'privacy' => $privacyOptions, 'preferred_communication_method' => $comm ) );
        
        $form->add('select', 'preferred_mail_format', ts('Email Format'), CRM_Core_SelectValues::pmf());
        $form->add('checkbox', 'is_opt_out', ts( 'NO BULK EMAILS (User Opt Out)' ) );
    }
    
    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( &$form, &$defaults ) {
    }
}



