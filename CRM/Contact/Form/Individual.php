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
    public function buildQuickForm( &$form, $action = null )
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
        
        // Declare javascript methods to be used, for use-household-address checkbox.
        // Also set label to be used for 'select-household' combo-box.
        if ( $action & CRM_Core_Action::UPDATE ) {
            $addressFlds = array('location_1_address_street_address',
                                 'location_1_address_supplemental_address_1',
                                 'location_1_address_supplemental_address_2',
                                 'location_1_address_city',
                                 'location_1_address_postal_code',
                                 'location_1_address_postal_code_suffix',
                                 'location_1_address_county_id',
                                 'location_1_address_state_province_id',
                                 'location_1_address_country_id',
                                 'location_1_address_geo_code_1',
                                 'location_1_address_geo_code_2');
            foreach ( $addressFlds as $addFld ) {
                $extraOnAddFlds = $extraOnAddFlds ? ($extraOnAddFlds . "|" . $addFld) : $addFld;
            }
            $extraOnAddFlds = "'" . $extraOnAddFlds . "'";
            
            $useHouseholdExtra = array( 'onclick' => "
showHideByValue('use_household_address',      '', 'shared_household',      'block', 'radio', false);
showHideByValue('use_household_address',      '', 'id_location_1_address', 'block', 'radio', true);
enableDisableByValue('use_household_address', '', $extraOnAddFlds,         'block', 'radio', true);
resetByValue('use_household_address',         '', $extraOnAddFlds,         'radio',  false);   " );
            
            $mailToHouseholdID = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Individual', 
                                                              $this->_contactId, 
                                                              'mail_to_household_id', 
                                                              'contact_id' );
            if ( $mailToHouseholdID ) {
                $form->add('hidden', 'old_mail_to_household_id', $mailToHouseholdID);
                $selHouseholdLabel = "Change Household"; // select-household label to be used.
            }
        } elseif ( $action & CRM_Core_Action::ADD ) {
            $useHouseholdExtra = array( 'onclick' => "
showHideByValue('use_household_address', 'true', 'id_location_1_address', 'block', 'radio', true);
showHideByValue('use_household_address', 'true', 'shared_household', 'block', 'radio', false);" );
        }
        
        // shared address element block
        $form->addElement('checkbox', 'use_household_address', null, ts('Use Household Address'), $useHouseholdExtra);
        $domainID      =  CRM_Core_Config::domainID( );   
        $attributes    = array( 'dojoType'     => 'ComboBox',
                                'mode'         => 'remote',
                                'dataUrl'      => CRM_Utils_System::url( 'civicrm/ajax/search',
                                                                         "d={$domainID}&sh=1&s=%{searchString}",
                                                                         true, null, false ),
                                );

        $attributes += CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Contact', 'sort_name' );

        $selHouseholdLabel = $selHouseholdLabel ? $selHouseholdLabel : "Select Household";
        $form->add( 'text', 'shared_household', ts( $selHouseholdLabel ), $attributes );
        // shared address element-block Ends.
        
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

        // if use_household_address option is checked, make sure 'valid household_name' is also present.
        if ( $fields['use_household_address'] && !$fields['shared_household_selected'] ) {
            if ( !$fields['old_mail_to_household_id'] || $fields['shared_household'] ) {
                $errors["shared_household"] = 
                    ts("Please select a household from the 'Select Household' list");
            }
        }
        
        return empty($errors) ? true : $errors;
    }

    /**
     * Function to Copy household address, if use_household_address option is checked.
     *
     * @param array $params  the input form values
     *
     * @return void
     * @access public
     * @static
     */
    static function copyHouseholdAddress( &$params ) {
        
        if ( $params['use_household_address'] ) {
            
            if ( $params['shared_household'] ) {
                list($householdName) = 
                    explode( ",", $params['shared_household'] );
                $params['mail_to_household_id'] = 
                    CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', trim( $householdName ), 'id', 'sort_name' );
            }
            
            if ( !$params['old_mail_to_household_id'] && !$params['mail_to_household_id'] ) {
                CRM_Core_Error::statusBounce( ts("Shared Household-ID not found.") );
            }
            
            $params['mail_to_household_id'] = 
                $params['mail_to_household_id'] ? $params['mail_to_household_id'] : $params['old_mail_to_household_id'];
            
            $locParams = array( 'contact_id' => $params['mail_to_household_id'] );
            
            require_once 'api/v2/Location.php';
            $values =& _civicrm_location_get( $locParams, $location_types );
            
            $params['location'][1]['address'] = $values[1]['address'];
            
            // unset all the ids and unwanted fields
            $unsetFields = array( 'id', 'location_id', 'timezone', 'note' );
            foreach ( $unsetFields as $fld ) {
                unset( $params['location'][1]['address'][$fld] );
            }
        } else {
            $params['mail_to_household_id'] = false;
        }
    }

    /**
     * Function to Add/Edit/Delete the relation of individual with shared-household.
     *
     * @param integer $contactID  the input form values
     * @param array   $params     the input form values
     *
     * @return void
     * @access public
     * @static
     */
    static function handleSharedRelation( $contactID, &$params ) {
        
        if ( $params['old_mail_to_household_id'] != $params['mail_to_household_id'] ) {
            require_once 'CRM/Contact/BAO/Relationship.php';
            $relID  = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_RelationshipType', 'Household Member of', 'id', 'name_a_b' );
            
            if ( $params['old_mail_to_household_id'] ) {
                $relationship =& new CRM_Contact_DAO_Relationship( );
                $relationship->contact_id_b         = $params['old_mail_to_household_id'];
                $relationship->contact_id_a         = $contactID;
                $relationship->relationship_type_id = $relID;
                if ( $relationship->find(true) ) {
                    $relationship->delete( );
                }
            }
            
            if ( $params['mail_to_household_id'] ) {
                $ids = array('contact' => $params['mail_to_household_id'] );
                
                $relationshipParams = array();
                $relationshipParams['relationship_type_id'] = $relID.'_b_a';
                
                $relationship =& new CRM_Contact_DAO_Relationship( );
                $relationship->contact_id_b         = $params['mail_to_household_id'];
                $relationship->contact_id_a         = $contactID;
                $relationship->relationship_type_id = $relID;
                // if relationship already not present, add a new one
                if ( !$relationship->find(true) ) {
                    CRM_Contact_BAO_Relationship::add( $relationshipParams, $ids, $contactID );   
                }
            }
        }
        
        return ;
    }

}
   
?>
