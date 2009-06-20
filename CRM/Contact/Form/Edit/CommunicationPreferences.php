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

        $privacy = array( );
       
        // checkboxes for DO NOT phone, email, mail
        // we take labels from SelectValues
        $t = CRM_Core_SelectValues::privacy();
        $privacy[] = HTML_QuickForm::createElement('advcheckbox', 'do_not_phone', null, $t['do_not_phone']);
        $privacy[] = HTML_QuickForm::createElement('advcheckbox', 'do_not_email', null, $t['do_not_email']);
        $privacy[] = HTML_QuickForm::createElement('advcheckbox', 'do_not_mail' , null, $t['do_not_mail']);
        $privacy[] = HTML_QuickForm::createElement('advcheckbox', 'do_not_sms' ,  null, $t['do_not_sms']);
        $privacy[] = HTML_QuickForm::createElement('advcheckbox', 'do_not_trade', null, $t['do_not_trade']);

        $form->addGroup($privacy, 'privacy', ts('Privacy'), '&nbsp;');

        // preferred communication method 
        require_once 'CRM/Core/PseudoConstant.php';
        $comm = CRM_Core_PseudoConstant::pcm(); 

        $commPreff = array();
        foreach ( $comm as $k => $v ) {
            $commPreff[] = HTML_QuickForm::createElement('advcheckbox', $k , null, $v );
        }
        $form->addGroup($commPreff, 'preferred_communication_method', ts('Method'));

        $form->add('select', 'preferred_mail_format', ts('Email Format'), CRM_Core_SelectValues::pmf());

        $form->add('checkbox', 'is_opt_out', ts( 'NO BULK EMAILS (User Opt Out)' ) );
    }
}



