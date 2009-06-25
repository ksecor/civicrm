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
 * form helper class for an Demographics object
 */
class CRM_Contact_Form_Edit_Demographics 
{
    /**
     * build the form elements for Demographics object
     *
     * @param CRM_Core_Form $form       reference to the form object
     *
     * @return void
     * @access public
     * @static
     */
    static function buildQuickForm( &$form ) {
        // radio button for gender
        $genderOptions = array( );
        $gender =CRM_Core_PseudoConstant::gender();
        foreach ($gender as $key => $var) {
            $genderOptions[$key] = HTML_QuickForm::createElement('radio', null, ts('Gender'), $var, $key);
        }
        $form->addGroup($genderOptions, 'gender_id', ts('Gender'));
        
        $form->addElement('checkbox', 'is_deceased', null, ts('Contact is deceased'), array('onclick' =>"showDeceasedDate()"));
        
        $form->addElement('date', 'deceased_date', ts('Deceased date'), CRM_Core_SelectValues::date('birth'));
        $form->addRule('deceased_date', ts('Select a valid date.'), 'qfDate');
        
        $form->addElement('date', 'birth_date', ts('Date of birth'), CRM_Core_SelectValues::date('birth'));
        $form->addRule('birth_date', ts('Select a valid date.'), 'qfDate');
    }
}


