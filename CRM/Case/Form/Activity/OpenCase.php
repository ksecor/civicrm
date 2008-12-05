<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once "CRM/Core/Form.php";
require_once "CRM/Custom/Form/CustomData.php";
/**
 * This class generates form components for OpenCase Activity
 * 
 */
class CRM_Case_Form_Activity_OpenCase
{
    static function preProcess( &$form ) 
    {   
        if ( $form->_context == 'caseActivity' ) {
            return;
        }
        $form->_createNewButtonName      = $form->getButtonName( 'next'   , 'createNew' );
        $form->_assignExistingButtonName = $form->getButtonName( 'next'   , 'assignExisting' );
    }

   /**
     * This function sets the default values for the form. For edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( &$form ) 
    {
        $defaults = array( );
        if ( $form->_context == 'caseActivity' ) {
            return $defaults;
        }

        $defaults['start_date'] = array();
        CRM_Utils_Date::getAllDefaultValues( $defaults['start_date'] );
        
        // set case status to 'ongoing'
        $defaults['status_id'] = 1;

        // set default encounter medium, location type and phone type defaults are set in DB
        require_once "CRM/Core/OptionGroup.php";
        $medium = CRM_Core_OptionGroup::values('encounter_medium', false, false, false, 'AND is_default = 1');
        if ( count($medium) == 1 ) {
            $defaults['medium_id'] = key($medium);
        }
        
        require_once 'CRM/Core/BAO/LocationType.php';
        $defaultLocationType =& CRM_Core_BAO_LocationType::getDefault();
        if ( $defaultLocationType->id ) {
            $defaults['location[1][location_type_id]'] = $defaultLocationType->id;
        }
        
        $phoneType = CRM_Core_OptionGroup::values('phone_type', false, false, false, 'AND is_default = 1');
        if ( count($phoneType) == 1 ) {
            $defaults['location[1][phone][1][phone_type_id]'] = key($phoneType);
        }
        
        return $defaults;
    }

    static function buildQuickForm( &$form ) 
    {
        if ( $form->_context == 'caseActivity' ) {
            return;
        }

        require_once 'CRM/Core/OptionGroup.php';        
        $caseType = CRM_Core_OptionGroup::values('case_type');
        $form->add('select', 'case_type_id',  ts( 'Case Type' ),  
                   $caseType , true);
        
        $caseStatus  = CRM_Core_OptionGroup::values('case_status');
        $form->add('select', 'status_id',  ts( 'Case Status' ),  
                   $caseStatus , true  );

        $form->add( 'text', 'duration', ts('Duration'),array( 'size'=> 4,'maxlength' => 8 ) );
        $form->addRule('duration', ts('Please enter the duration as number of minutes (integers only).'), 'positiveInteger');  

        require_once "CRM/Contact/BAO/Contact.php";
        if ( $form->_currentlyViewedContactId ) {
            list( $displayName ) = CRM_Contact_BAO_Contact::getDisplayAndImage( $form->_currentlyViewedContactId );
            $form->assign( 'clientName', $displayName );
        } else {
            $attributes = CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Contact' );
            $form->addElement('select', 'prefix_id', ts('Prefix'), 
                              array('' => ts('- prefix -')) + CRM_Core_PseudoConstant::individualPrefix());
            $form->addElement('select', 'suffix_id', ts('Suffix'), 
                              array('' => ts('- suffix -')) + CRM_Core_PseudoConstant::individualSuffix());
            $form->addElement('text',   'first_name',  ts('First Name'),  
                              $attributes['first_name'] );
            $form->addElement('text',   'last_name',   ts('Last Name'),   
                              $attributes['last_name'] );
            //Primary Phone 
            if ( ! $locType ) {
                $locType = CRM_Core_PseudoConstant::locationType( );
            }
            $form->addElement('select',
                              "location[1][location_type_id]", null,  array( '' => ts( '- location -' ) ) + $locType );
            if ( ! $phoneType ) {
                $phoneType = CRM_Core_PseudoConstant::phoneType( );
            }
            $form->addElement('select',
                              "location[1][phone][1][phone_type_id]",
                              $label,
                              array('' =>  ts('- type -'))+$phoneType,
                              null
                              );
            $form->addElement('text',
                              "location[1][phone][1][phone]", 
                              ts('Primary Phone'),
                              CRM_Core_DAO::getAttribute('CRM_Core_DAO_Phone',
                                                         'phone'));
            //Additional Phone 
            $form->addElement('select'  , "location[2][location_type_id]", null,  array( '' => ts( '- location -' ) ) + $locType );
            $form->addElement('select',
                              "location[2][phone][1][phone_type_id]",
                              $label,
                              array('' =>  ts('- type -'))+$phoneType,
                              null
                              );
            $form->addElement('text',
                              "location[2][phone][1][phone]", 
                              ts('Additional Phone'),
                              CRM_Core_DAO::getAttribute('CRM_Core_DAO_Phone',
                                                         'phone'));
            
            
            //Primary Email
            $form->addElement('text', 
                              "location[1][email][1][email]",
                              ts('Primary Email'),
                              CRM_Core_DAO::getAttribute('CRM_Core_DAO_Email',
                                                         'email'));
        }

        $form->add( 'date', 'start_date', ts('Case Start Date'),
                    CRM_Core_SelectValues::date('activityDate' ),
                    true);   
        $form->addRule('start_date', ts('Select a valid date.'), 'qfDate');

        $form->add('select', 'medium_id',  ts( 'Medium' ), 
                   CRM_Core_OptionGroup::values('encounter_medium'), true);

        // calling this field activity_location to prevent conflict with contact location fields
        $form->add('text', 'activity_location', ts('Location'), CRM_Core_DAO::getAttribute( 'CRM_Activity_DAO_Activity', 'location' ) );
        
        $form->add('textarea', 'activity_details', ts('Details'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Activity_DAO_Activity', 'details' ) );
        
        $form->addElement('submit', 
                          $form->_createNewButtonName,
                          ts( 'Create New Client' ) );
        
        $form->addElement('submit', 
                          $form->_assignExistingButtonName,
                          ts( 'Assign Existing Client' ) );
    }

    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function beginPostProcess( &$form, &$params ) 
    {
        if ( $form->_context == 'caseActivity' ) {
            return;
        }
        // create contact if cid not present

        $contactParams = $params;
        if ( !$form->_currentlyViewedContactId ) {
            $contactParams['location'][1]['is_primary'] = 1;
            $contactParams['contact_type']              = 'Individual';
            $contactParams['email'] = $contactParams['location'][1]['email'][1]['email'];
                
            if ( $form->controller->getButtonName( ) == $form->_assignExistingButtonName ) {
                //Dedupe couldn't recognize "email-Primary".So modify params temporary.
                require_once 'CRM/Dedupe/Finder.php';
                $dedupeParams = CRM_Dedupe_Finder::formatParams( $contactParams, 'Individual' );
                $ids          = CRM_Dedupe_Finder::dupesByParams( $dedupeParams, 'Individual' );
                
                // if we find more than one contact, use the first one
                if ( is_array($ids) ) {
                    $form->_currentlyViewedContactId = $ids[0];
                }
                if ( !$form->_currentlyViewedContactId ) {
                    CRM_Core_Error::fatal('Could not find existing client to link the case with.');
                }
            }

            if ( !$form->_currentlyViewedContactId ) {
                require_once 'CRM/Contact/BAO/Contact.php';
                $contact =& CRM_Contact_BAO_Contact::create( $contactParams, true, false );
                $form->_currentlyViewedContactId = $contact->id;
            }
            
            // unset contact params
            unset($params['location'], $params['first_name'], $params['last_name'], 
                  $params['prefix_id'], $params['suffix_id']);
        }

        // for open case start date should be set to current date
        $params['start_date'] = CRM_Utils_Date::format( $params['start_date'] );

        // rename activity_location param to the correct column name for activity DAO
        $params['location'] = $params['activity_location'];
    }

    /**
     * global validation rules for the form
     *
     * @param array $values posted values of the form
     *
     * @return array list of errors to be posted back to the form
     * @static
     * @access public
     */
    static function formRule( &$values, $files, &$form ) 
    {
        if ( $form->_context == 'caseActivity' ) {
            return true;
        }
        
        if ( CRM_Utils_Array::value( '_qf_Case_next_assignExisting', $values ) ) {
            return true;
        }

        $errors = array( );
              
        // if this is a forced save, ignore find duplicate rule
        if ( ! CRM_Utils_Array::value( '_qf_Case_next_createNew', $values ) && !$form->_currentlyViewedContactId ) {
            $contactParams = $values;
            $contactParams['location'][1]['is_primary'] = 1;
            $contactParams['contact_type']              = 'Individual';
            $contactParams['email'] = $contactParams['location'][1]['email'][1]['email'];

            require_once 'CRM/Dedupe/Finder.php';
            $dedupeParams = CRM_Dedupe_Finder::formatParams($contactParams, 'Individual');
            $ids          = CRM_Dedupe_Finder::dupesByParams($dedupeParams, 'Individual', 'Fuzzy');

            if ( $ids ) {
                $urls = array( );
                foreach ($ids as $id) {
                    $displayName = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $id, 'display_name' );
                    $urls[] = '<a href="' . CRM_Utils_System::url( 'civicrm/contact/view', 'reset=1&cid=' . $id ) .
                        '">' . $displayName . '</a>';
                }
                $url = implode( ', ',  $urls );
                $errors['_qf_default'] = ts( "One matching client was found. You may choose to link the case with existing client '%1' by clicking 'Assign Existing Client', or click Create New Client button below.", 
                                             array( 1 => $url, 'count' => count( $urls ), 'plural' => '%count matching contacts were found. You can view them here: %1, or click Create New Client button below.' ) );
                
                // let smarty know that there are duplicates
                $form->assign( 'isDuplicate', 1 );

                if ( count($ids) == 1 ) {
                    $form->assign( 'onlyOneDupe', 1 );
                }
            } 
        }

        return $errors;
    }

    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function endPostProcess( &$form, &$params ) 
    {
        if ( $form->_context == 'caseActivity' ) {
            return;
        }

        if (!$form->_currentlyViewedContactId   ||
            !$form->_currentUserId        ||
            !$params['case_id'] ||
            !$params['case_type']
            ) {
            CRM_Core_Error::fatal('Required parameter missing for OpenCase - end post processing');
        }

        // 1. create case-contact
        $contactParams = array('case_id'    => $params['case_id'],
                               'contact_id' => $form->_currentlyViewedContactId
                               );
        CRM_Case_BAO_Case::addCaseToContact( $contactParams );
        
        // 2. initiate xml processor
        $xmlProcessor = new CRM_Case_XMLProcessor_Process( );
        $xmlProcessorParams = array( 'clientID'           => $form->_currentlyViewedContactId,
                                     'creatorID'          => $form->_currentUserId,
                                     'standardTimeline'   => 1,
                                     'activityTypeName'   => 'Open Case',
                                     'dueDateTime'        => time( ),
                                     'caseID'             => $params['case_id'],
                                     'subject'            => $params['activity_subject'],
                                     'location'           => $params['location'],
                                     'activity_date_time' => $params['start_date'],
                                     'duration'           => $params['duration'],
                                     'medium_id'          => $params['medium_id'],
                                     'details'            => $params['activity_details'],
                                     );

        if ( array_key_exists('custom', $params) && is_array($params['custom']) ) {
            $xmlProcessorParams['custom'] = $params['custom'];
        }

        $xmlProcessor->run( $params['case_type'], $xmlProcessorParams );

        // status msg
        $params['statusMsg'] = ts('Case opened successfully.');
    }
}
