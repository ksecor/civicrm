<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/SelectValues.php';
require_once 'CRM/Core/ShowHideBlocks.php';
require_once 'CRM/Core/BAO/UFGroup.php';

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
        $form->addElement('select', 'prefix_id', ts('Prefix'), array('' => ts('- prefix -')) + CRM_Core_PseudoConstant::individualPrefix());

        $attributes = CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Individual');

        // first_name
        $form->addElement('text', 'first_name', ts('First Name'), $attributes['first_name'] );
        
        //middle_name
        $form->addElement('text', 'middle_name', ts('Middle Name'), $attributes['middle_name'] );
        
        // last_name
        $form->addElement('text', 'last_name', ts('Last Name'), $attributes['last_name'] );
        
        // suffix
        $form->addElement('select', 'suffix_id', ts('Suffix'), array('' => ts('- suffix -')) + CRM_Core_PseudoConstant::individualSuffix());
        
        // nick_name
        $form->addElement('text', 'nick_name', ts('Nick Name'),
                          CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'nick_name') );

        // greeting type
        $form->addElement('select', 'greeting_type', ts('Greeting'), CRM_Core_SelectValues::greeting());
        
        // job title
        $form->addElement('text', 'job_title', ts('Job title'), $attributes['job_title']);

        if ( $form->_showDemographics ) {
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
        
        // shared address elements
        $form->useHouseholdChkbox =& $form->addElement('checkbox', 'use_household_address', null, ts('Use Household Address'));
        $selHouseLabel = ( $form->selHouseLabel ) ? $form->selHouseLabel : "Select Household";
        $domainID      =  CRM_Core_Config::domainID( );   
        $attributes    = array( 'dojoType'     => 'ComboBox',
                                'mode'         => 'remote',
                                'dataUrl'      => CRM_Utils_System::url( 'civicrm/ajax/search',
                                                                         "d={$domainID}&sh=1&s=%{searchString}",
                                                                         true, null, false ),
                                );
        $attributes += CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Contact', 'sort_name' );
        $form->add( 'text', 'shared_household', ts( $selHouseLabel ), $attributes );

        $form->addElement('text', 'home_URL', ts('Website'),
                          array_merge( CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'home_URL'),
                                       array('onfocus' => "if (!this.value) this.value='http://'; else return false")
                                       ));
        $form->addRule('home_URL', ts('Enter a valid web location beginning with "http://" or "https://". EXAMPLE: http://www.mysite.org'), 'url');
        
        $form->addElement('text', 'current_employer', ts('Current Employer'), CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'current_employer') );
        
        $form->addElement('text', 'contact_source', ts('Source'));
        $form->addElement('text', 'external_identifier', ts('External Id'));
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
                    $urls[] = '<a href="' . CRM_Utils_System::url( 'civicrm/contact/add', 'reset=1&action=update&cid=' . $id ) .
                        '">' . $displayName . '</a>';
                }
                $url = implode( ', ',  $urls );
                $errors['_qf_default'] = ts( 'One matching contact was found. You can edit it here: %1, or click Save Duplicate Contact button below.', array( 1 => $url, 'count' => count( $urls ), 'plural' => '%count matching contacts were found. You can edit them here: %1, or click Save Duplicate Contact button below.' ) );

                // let smarty know that there are duplicates
                $template =& CRM_Core_Smarty::singleton( );
                $template->assign( 'isDuplicate', 1 );
            } else if ( CRM_Utils_Array::value( '_qf_Edit_refresh_dedupe', $fields ) ) {
                // add a session message for no matching contacts
                CRM_Core_Session::setStatus( 'No matching contact found.' );
            }
        }

        // if use_household_address option is checked, make sure 'correct household_name' is also present.
        if ( $fields['use_household_address'] ) {
            if ( $fields['shared_household'] ) {
                list($householdName) = explode(",", $fields['shared_household']);
                $contactID = 
                    CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', trim( $householdName ), 'id', 'sort_name' );
                if ( ! $contactID ) {
                    $errors["shared_household"] = 
                        ts("Household not found. Please select a household from the 'Select Household' list");
                }
            } else {
                $errors["shared_household"] = ts('Please enter the Household name.');
            }
        }
        
        return empty($errors) ? true : $errors;
    }
}
   
?>
