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
    }

    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess( &$form, &$params ) 
    {
        // 1. create contact
        if ( !$form->_clientId ) {
            $params['location'][1]['is_primary'] = 1;
            $params['contact_type']              = 'Individual';
            
            require_once 'CRM/Contact/BAO/Contact.php';
            $contact =& CRM_Contact_BAO_Contact::create($params, true, false );

            $form->_clientId = $contact->id;
        }

        // 2. create case
        $params['contact_id'  ] = $form->_clientId;
        $params['start_date'  ] = CRM_Utils_Date::format( date() );
        $params['case_type_id'] = CRM_Case_BAO_Case::VALUE_SEPERATOR.implode(CRM_Case_BAO_Case::VALUE_SEPERATOR, $params['case_type_id'] ).CRM_Case_BAO_Case::VALUE_SEPERATOR;
        
        require_once 'CRM/Case/BAO/Case.php';
        $caseObj = CRM_Case_BAO_Case::create( $params );
        
        $contactParams = array(
                               'case_id'    => $caseObj->id,
                               'contact_id' => $form->_uid
                               );
        CRM_Case_BAO_Case::addCaseToContact( $contactParams );
        
        // 3. create activity
        
        //set activity type id

        // *FIXME*: set activity type id to be the category id of
        //'OpenCase' activity type id 
        $params['activity_type_id'] = 1;//$form->_activityTypeId;

        // store the date with proper format
        $params['activity_date_time'] = CRM_Utils_Date::format( date() );

        // get ids for associated contacts
        $params['source_contact_id'] = $form->_uid;
        $activity = CRM_Activity_BAO_Activity::create( $params );
        
        $targetParams['target_contact_id'] = $form->_uid;
        CRM_Activity_BAO_Activity::createActivityTarget( $targetParams );

        // 4. create case activity
        $caseParams['activity_id'] = $activity->id;
        $caseParams['case_id'    ] = $caseObj->id;
        CRM_Case_BAO_Case::processCaseActivity( $caseParams );        

        // 5. create relationship
        
    }
}


