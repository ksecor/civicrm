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

require_once 'CRM/Core/ShowHideBlocks.php';
require_once 'CRM/Core/PseudoConstant.php';

/**
 * Auxilary class to provide support to the Contact Form class. Does this by implementing
 * a small set of static methods
 *
 */
class CRM_Contact_Form_Edit_Individual {
    /**
     * This function provides the HTML form elements that are specific to the Individual Contact Type
     * 
     * @access public
     * @return None 
     */
    public function buildQuickForm( &$form, $action = null )
    {
        $form->applyFilter('__ALL__','trim');
        
        //prefix
        $prefix = CRM_Core_PseudoConstant::individualPrefix( );
        if ( !empty( $prefix ) ) {
            $form->addElement('select', 'prefix_id', ts('Prefix'), array('' => '') + $prefix );
        }
        
        $attributes = CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact');
        
        // first_name
        $form->addElement('text', 'first_name', ts('First Name'), $attributes['first_name'] );
        
        //middle_name
        $form->addElement('text', 'middle_name', ts('Middle Name'), $attributes['middle_name'] );
        
        // last_name
        $form->addElement('text', 'last_name', ts('Last Name'), $attributes['last_name'] );
        
        // suffix
        $siffix = CRM_Core_PseudoConstant::individualSuffix( );
        if ( $siffix ) {
            $form->addElement('select', 'suffix_id', ts('Suffix'), array('' => '') + $siffix );
        }
        
        // nick_name
        $form->addElement('text', 'nick_name', ts('Nick Name'),
                          CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'nick_name') );
      
        // job title
        $form->addElement('text', 'job_title', ts('Job title'), $attributes['job_title']);
        
        // add email and postal greeting on contact form, CRM-4575
        // the filter value for Individual contact type is set to 1
		$filter =  array( 
							'contact_type'  => 'Individual', 
							'greeting_type' => 'email_greeting'  );
        //email greeting
        $emailGreeting = CRM_Core_PseudoConstant::greeting( $filter );
        if ( !empty( $emailGreeting ) ) {
            $this->addElement('select', 'email_greeting_id', ts('Email Greeting'), 
                              array('' => ts('- select -')) + $emailGreeting, 
                              array( 'onchange' => " showCustomized(this.id);" ));
            //email greeting custom
            $this->addElement('text', 'email_greeting_custom', ts('Custom Email Greeting'), 
                              array_merge( CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'email_greeting_custom' ),
                                           array( 'onfocus' => "if (!this.value) this.value='Dear'; else return false",
                                                  'onblur'  => "if ( this.value == 'Dear') this.value=''; else return false") ) );
        }
        
        //postal greeting$
		$filter['greeting_type'] = 'postal_greeting';
        $postalGreeting = CRM_Core_PseudoConstant::greeting( $filter);
        if ( !empty( $postalGreeting ) ) {
            $this->addElement('select', 'postal_greeting_id', ts('Postal Greeting'), 
                              array('' => ts('- select -')) + $postalGreeting, 
                              array( 'onchange' => " showCustomized(this.id);") );
            //postal greeting custom
            $this->addElement('text', 'postal_greeting_custom', ts('Custom Postal Greeting'), 
                              array_merge( CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'postal_greeting_custom' ), 
                                           array( 'onfocus' => "if (!this.value) this.value='Dear'; else return false",
                                                  'onblur'  => "if ( this.value == 'Dear') this.value=''; else return false") ) );
        }
        if ( $action & CRM_Core_Action::UPDATE ) {
            $mailToHouseholdID  = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', 
                                                               $form->_contactId, 
                                                               'mail_to_household_id', 
                                                               'id' );
            $form->assign('mailToHouseholdID',$mailToHouseholdID );  
        }
       
        //Shared Address Element
        $form->addElement('checkbox', 'use_household_address', null, ts('Use Household Address') );
        $housholdDataURL = CRM_Utils_System::url( 'civicrm/ajax/search', "hh=1", false, null, false );
        $form->assign('housholdDataURL',$housholdDataURL );
        $form->add( 'text', 'shared_household', ts( 'Select Household' ) );
        $form->add( 'hidden', 'shared_household_id', '', array( 'id' => 'shared_household_id' ));
        
        //Home Url Element
        $form->addElement('text', 'home_URL', ts('Website'),
                          array_merge( CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'home_URL'),
                                       array('onfocus' => "if (!this.value) this.value='http://'; else return false",
                                             'onblur'=> "if ( this.value == 'http://') this.value=''; else return false")
                                       ));
        $form->addRule('home_URL', ts('Enter a valid web location beginning with \'http://\' or \'https://\'. EXAMPLE: http://www.mysite.org/'), 'url');
        
        //Current Employer Element
        $employerDataURL =  CRM_Utils_System::url( 'civicrm/ajax/search', 'org=1', false, null, false );
        $form->assign('employerDataURL',$employerDataURL );
        
        $form->addElement('text', 'current_employer', ts('Current Employer'), '' );
        $form->addElement('hidden', 'current_employer_id', '', array( 'id' => 'current_employer_id') );
        $form->addElement('text', 'contact_source', ts('Source'));

        //External Identifier Element
        $form->add('text', 'external_identifier', ts('External Id'), 
                   CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'external_identifier'), false);

        $form->addRule( 'external_identifier',
                        ts('External ID already exists in Database.'), 
                        'objectExists', 
                        array( 'CRM_Contact_DAO_Contact', $form->_contactId, 'external_identifier' ) );
        $config =& CRM_Core_Config::singleton();
        CRM_Core_ShowHideBlocks::links($form, 'demographics', '' , '');
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
    static function formRule( &$fields, &$files, $contactId = null ) 
    {
        $errors = array( );
        //FIXME 
        if ( CRM_Utils_Array::value( 'state_province_id', $fields['address'][1] )  == 'undefined' ) {
            $fields['address'][1]['state_province_id'] ='';
        }
        $primaryID = CRM_Contact_Form_Contact::formRule( $fields, $errors, $contactId );
        
        // check for state/country mapping
        require_once 'CRM/Contact/Form/Edit/Address.php';
        CRM_Contact_Form_Edit_Address::formRule( $fields, $errors );
        
        // make sure that firstName and lastName or a primary OpenID is set
        if ( !$primaryID && ( !CRM_Utils_Array::value( 'first_name', $fields ) ||  
                              !CRM_Utils_Array::value( 'last_name' , $fields ) ) ) {
            $errors['_qf_default'] = ts('First Name and Last Name OR an email OR an OpenID in the Primary Location should be set.'); 
        }
        
        // if this is a forced save, ignore find duplicate rule
        if ( ! CRM_Utils_Array::value( '_qf_Contact_next_duplicate', $fields ) ) {
            require_once 'CRM/Dedupe/Finder.php';
            $dedupeParams = CRM_Dedupe_Finder::formatParams($fields, 'Individual');
            $ids = CRM_Dedupe_Finder::dupesByParams($dedupeParams, 'Individual', 'Fuzzy', array( $contactId ) );
            if ( $ids ) {
                $viewUrls = array( );
                $urls     = array( );
                foreach ($ids as $id) {
                    $displayName = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $id, 'display_name' );
                    $viewUrls[] = '<a href="' . CRM_Utils_System::url( 'civicrm/contact/view', 'reset=1&cid=' . $id ) .
                        '" target="_blank">' . $displayName . '</a>';
                    $urls[] = '<a href="' . CRM_Utils_System::url( 'civicrm/contact/add', 'reset=1&action=update&cid=' . $id ) .
                        '">' . $displayName . '</a>';
                }
                $viewUrl = implode( ', ',  $viewUrls );
                $url     = implode( ', ',  $urls );
                $errors['_qf_default']  = ts('One matching contact was found.', array('count' => count($urls), 'plural' => '%count matching contacts were found.'));
                $errors['_qf_default'] .= '<br />';
                $errors['_qf_default'] .= ts('If you need to verify if this is the same contact, click here - %1 - to VIEW the existing contact in a new tab.', array(1 => $viewUrl, 'count' => count($urls), 'plural' => 'If you need to verify whether one of these is the same contact, click here - %1 - to VIEW the existing contact in a new tab.'));
                $errors['_qf_default'] .= '<br />';
                $errors['_qf_default'] .= ts('If you know the record you are creating is a duplicate, click here - %1 - to EDIT the original record instead.', array(1 => $url));
                $errors['_qf_default'] .= '<br />';
                $errors['_qf_default'] .= ts('If you are sure this is not a duplicate, click the Save Matching Contact button below.');
                
                // let smarty know that there are duplicates
                $template =& CRM_Core_Smarty::singleton( );
                $template->assign( 'isDuplicate', 1 );
            } else if ( CRM_Utils_Array::value( '_qf_Edit_refresh_dedupe', $fields ) ) {
                // add a session message for no matching contacts
                CRM_Core_Session::setStatus( 'No matching contact found.' );
            }
        }
        
        // if use_household_address option is checked, make sure 'valid household_name' is also present.
        if ( CRM_Utils_Array::value('use_household_address',$fields ) && 
             !CRM_Utils_Array::value( 'shared_household_id', $fields ) ) {
            $errors["shared_household"] = ts("Please select a household from the 'Select Household' list");
        }
        
        //if email/postal greeting type is 'Customized' 
        //then Custom greeting field must have a value. CRM-4575
        $fieldId    = null;
        $fieldValue = null;
        $elements = array( 'email_greeting'  => array( 
                                                        'greeting_type' => 'email_greeting'  ),
							'postal_greeting' => array( 
                                                        'greeting_type' => 'postal_greeting'  ) ); 
        foreach ( $elements as $key => $value ) {
            $fieldId = $key."_id";
            $filterCondition = null;
            $optionValues = CRM_Core_PseudoConstant::greeting( $value,  'name' );
            $fieldValue = array_search( 'Customized', $optionValues );
            $customizedField = $key."_custom";
            if( CRM_Utils_Array::value( $fieldId, $fields ) == $fieldValue && 
                ! CRM_Utils_Array::value( $customizedField, $fields ) ) {
                $errors[$customizedField] = ts( 'Custom  %1 is a required field if %1 is of type Customized.', 
                                            array( 1 => ucwords(str_replace('_'," ", $key) ) ) );
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
    static function copyHouseholdAddress( &$params ) 
    { 
        if ( $params['shared_household'] ) {
            $params['mail_to_household_id'] = $params['shared_household'];
        }
        
        if ( !$params['mail_to_household_id'] ) {
            CRM_Core_Error::statusBounce( ts("Shared Household-ID not found.") );
        }
        
        $locParams = array( 'contact_id' => $params['mail_to_household_id'] );
        
        require_once 'api/v2/Location.php';
        $values =& _civicrm_location_get( $locParams, $location_types );
 
        $addressFields = CRM_Core_DAO_Address::fields();
        foreach($addressFields as  $key =>$val ){
		   if( !CRM_Utils_Array::value( $key, $values[1]['address'] ) ){
                $values[1]['address'][$key]="";
            }
        }
		
        if( $values[1]['address']['country_id']=="null"){
            $values[1]['address']['country_id']=0;
        }
        if( $values[1]['address']['state_province_id']=="null"){
            $values[1]['address']['state_province_id']=0;
        }
      
        $params['address'][1] = $values['address'][1];

        // unset all the ids and unwanted fields
        $unsetFields = array( 'id', 'location_id', 'timezone', 'note' );
        foreach ( $unsetFields as $fld ) {
            unset( $params['address'][1][$fld] );
        } 
    }
    
    /**
     * Function to create a new shared household (used if create-new-household options is checked).
     *
     * @param array $params  the input form values
     *
     * @return void
     * @access public
     * @static
     */
    static function createSharedHousehold( &$params ) 
    {
        $houseHoldId = null;
        
        // if household id is passed.
        if ( is_numeric( $params['shared_household'] ) ) {
            $houseHoldId = $params['shared_household'];
        } else {
            $householdParams = array();

            $householdParams['address']['1'] = $params['address']['1'];
          
            $householdParams['household_name'] = $params['shared_household'];
            require_once 'CRM/Dedupe/Finder.php';
            $dedupeParams = CRM_Dedupe_Finder::formatParams($householdParams, 'Household');
                    
            $dupeIDs = CRM_Dedupe_Finder::dupesByParams($dedupeParams, 'Household', 'Fuzzy');
           
            if ( empty($dupeIDs) ) {
                //create new Household
                $newHousehold = array ( 'contact_type'   => 'Household',
                                        'household_name' => $params['shared_household'], 
                                        'address'        => $householdParams['address'] );
                $houseHold   = CRM_Contact_BAO_Contact::create( $newHousehold );
                $houseHoldId = $houseHold->id;
            } else {
                $houseHoldId = $dupeIDs[0];
            } 
        }
        if ( $houseHoldId ) {
            $params['mail_to_household_id'] = $houseHoldId;
            return true;
        }
        return false;
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
    static function handleSharedRelation( $contactID, &$params ) 
    {
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
                $relationshipParams['is_active']            = 1;
                
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
   

