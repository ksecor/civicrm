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

/**
 * This class generates form components for OpenCase Activity
 * 
 */
class CRM_Case_Form_Activity_OpenCase
{

    static function buildQuickForm( &$form ) 
    {
        require_once 'CRM/Core/OptionGroup.php';        
        $caseType = CRM_Core_OptionGroup::values('case_type');
        $form->add('select', 'case_type_id',  ts( 'Case Type' ),  
                   $caseType , true, array("size"=>"5",  "multiple"));
        
        $attributes = CRM_Core_DAO::getAttribute( 'CRM_Case_DAO_Case' );
        $form->add( 'text', 'subject', ts('Subject'), array_merge( $attributes['subject'], array('maxlength' => '128') ), true);
        $caseStatus  = CRM_Core_OptionGroup::values('case_status');
        $form->add('select', 'status_id',  ts( 'Case Status' ),  
                   $caseStatus , true  );

        if ( $form->_clientId ) {
            list( $displayName ) = CRM_Contact_BAO_Contact::getDisplayAndImage( $form->_clientId );
            $form->assign( 'clientName', $displayName );
        } else {
            $form->addElement('select', 'prefix_id', ts('Prefix'), 
                              array('' => ts('- prefix -')) + CRM_Core_PseudoConstant::individualPrefix());
            $form->addElement('text',   'first_name',  ts('First Name'),  
                              $attributes['first_name'] );
            $form->addElement('text',   'last_name',   ts('Last Name'),   
                              $attributes['last_name'] );
            //Primary Phone 
            $form->addElement('text',
                              "location[1][phone][1][phone]", 
                              ts('Primary Phone'),
                              CRM_Core_DAO::getAttribute('CRM_Core_DAO_Phone',
                                                         'phone'));
            //Primary Email
            $form->addElement('text', 
                              "location[1][email][1][email]",
                              ts('Primary Email'),
                              CRM_Core_DAO::getAttribute('CRM_Core_DAO_Email',
                                                         'email'));
        }

        $form->addButtons( array(
                                 array ( 'type'      => 'submit',
                                         'name'      => ts('Save'),
                                         'isDefault' => true   ),
                                 array ( 'type'       => 'cancel',
                                         'name'      => ts('Cancel') ) ) );
    }

    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function beginPostProcess( &$form, &$params ) 
    {
        // create contact if cid not present

        $contactParams = $params;
        if ( !$form->_clientId ) {
            $contactParams['location'][1]['is_primary'] = 1;
            $contactParams['contact_type']              = 'Individual';
            
            $contactParams['email'] = $contactParams['location'][1]['email'][1]['email'];

            //Dedupe couldn't recognize "email-Primary".So modify params temporary.
            require_once 'CRM/Dedupe/Finder.php';
            $dedupeParams = CRM_Dedupe_Finder::formatParams( $contactParams, 'Individual' );
            
            $ids = CRM_Dedupe_Finder::dupesByParams( $dedupeParams, 'Individual' );
            
            // if we find more than one contact, use the first one
            if ( is_array($ids) ) {
                $contactParams['contact_id']  = $ids[0];
            }
            
            require_once 'CRM/Contact/BAO/Contact.php';
            $contact =& CRM_Contact_BAO_Contact::create( $contactParams, true, false );
            $form->_clientId = $contact->id;
            
            // unset contact params
            unset($params['location'], $params['first_name'], $params['last_name'], $params['prefix_id']);
        }

        // for open case start date should be set to current date
        $params['start_date'] = CRM_Utils_Date::format( $params['now'] );
    }

    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function endPostProcess( &$form, &$params ) 
    {
        // 1. create case-contact
        $contactParams = array('case_id'    => $params['case_id'],
                               'contact_id' => $form->_clientId
                               );
        CRM_Case_BAO_Case::addCaseToContact( $contactParams );
        
        // 2. create activity
        
        // *FIXME*: set activity type id to be the category id of
        //'OpenCase' activity type id 
        require_once 'CRM/Activity/BAO/Activity.php';
        $params['activity_type_id']   = 1;//$form->_activityTypeId;
        $params['activity_date_time'] = CRM_Utils_Date::format( $params['now'] );
        $params['source_contact_id']  = $form->_uid;
        $activity = CRM_Activity_BAO_Activity::create( $params );

        // 3. add target/with contacts
        $targetParams = array('activity_id'       => $activity->id,
                              'target_contact_id' => $form->_clientId);
        CRM_Activity_BAO_Activity::createActivityTarget( $targetParams );

        // 4. create case activity
        $caseParams['activity_id'] = $activity->id;
        $caseParams['case_id'    ] = $params['case_id'];
        CRM_Case_BAO_Case::processCaseActivity( $caseParams );        

        // 5. create relationship
        //*FIXME* for hardcode relationship_type_id and NULL contact_id_b which is same as contact_id_a
        $ids['contact'] = $form->_uid;
        $roleParams = array( 'relationship_type_id' => '3_a_b',
                             'is_active'            => 1,
                             'case_id'              => $params['case_id']  );
        $roleParams['contact_check'][$form->_uid] = 1;
      
        require_once 'CRM/Contact/BAO/Relationship.php';
        CRM_Contact_BAO_Relationship::create( $roleParams, $ids );

        // status msg
        $params['statusMsg'] = ts('Case opened successfully.');
    }
}


