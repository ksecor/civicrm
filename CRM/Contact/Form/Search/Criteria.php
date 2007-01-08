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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions      |
 | about the Affero General Public License or the licensing  of       |
 | CiviCRM, see the CiviCRM license FAQ at                            |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

class CRM_Contact_Form_Search_Criteria {

    static function basic( &$form ) {
        $form->addElement( 'hidden', 'hidden_basic', 1 );

        // add checkboxes for contact type
        $contact_type = array( );
        foreach (CRM_Core_SelectValues::contactType() as $k => $v) {
            if ( ! empty( $k ) ) {
                $contact_type[] = HTML_QuickForm::createElement('checkbox', $k, null, $v);
            }
        }
        $form->addGroup($contact_type, 'contact_type', ts('Contact Type(s)'), '<br />');
        
        // checkboxes for groups
        $group = array();
        foreach ($form->_group as $groupID => $groupName) {
            $form->_groupElement =& $form->addElement('checkbox', "group[$groupID]", null, $groupName);
        }

        // checkboxes for categories
        foreach ($form->_tag as $tagID => $tagName) {
            $form->_tagElement =& $form->addElement('checkbox', "tag[$tagID]", null, $tagName);
        }

        // add text box for last name, first name, street name, city
        $form->addElement('text', 'sort_name', ts('Find...'), CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'sort_name') );
        //added contact source
        $form->addElement('text', 'contact_source', ts('Contact Source'), CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'source') );
                
        // add search profiles
        require_once 'CRM/Core/BAO/UFGroup.php';
        $ufGroups =& CRM_Core_BAO_UFGroup::getModuleUFGroup('Search Profile', 1);
        $searchProfiles = array ( );
        foreach ($ufGroups as $key => $var) {
            $searchProfiles[$key] = $var['title'];
        }
        
        $form->addElement('select', 'uf_group_id', ts('Search Views'),  array('' => ts('- default view -')) + $searchProfiles);

        // checkboxes for DO NOT phone, email, mail
        // we take labels from SelectValues
        $t = CRM_Core_SelectValues::privacy();
        $privacy[] = HTML_QuickForm::createElement('advcheckbox', 'do_not_phone', null, $t['do_not_phone']);
        $privacy[] = HTML_QuickForm::createElement('advcheckbox', 'do_not_email', null, $t['do_not_email']);
        $privacy[] = HTML_QuickForm::createElement('advcheckbox', 'do_not_mail' , null, $t['do_not_mail']);
        $privacy[] = HTML_QuickForm::createElement('advcheckbox', 'do_not_trade', null, $t['do_not_trade']);
        
        $form->addGroup($privacy, 'privacy', ts('Privacy'), '&nbsp;');
    }

    static function location( &$form ) {
        $form->addElement( 'hidden', 'hidden_location', 1 );

        $attributes = CRM_Core_DAO::getAttribute('CRM_Core_DAO_Address');

        $form->addElement('text', 'street_address', ts('Street Address'),
                          $attributes['street_address'] );
        $form->addElement('text', 'city', ts('City'),
                          $attributes['city'] );

        // select for state province
        $stateProvince = array('' => ts('- any state/province -')) + CRM_Core_PseudoConstant::stateProvince( );
        $form->addElement('select', 'state_province', ts('State/Province'), $stateProvince);

        // select for country
        $country = array('' => ts('- any country -')) + CRM_Core_PseudoConstant::country( );
        $form->addElement('select', 'country', ts('Country'), $country);

        // add text box for postal code
        $form->addElement('text', 'postal_code', ts('Postal Code'),
                          $attributes['postal_code'] );
        
        $form->addElement('text', 'postal_code_low', ts('Range-From'),
                          $attributes['postal_code'] );

        $form->addElement('text', 'postal_code_high', ts('To'),
                          $attributes['postal_code'] );

        $form->addElement('text', 'location_name', ts('Location Name'),
                          CRM_Core_DAO::getAttribute('CRM_Core_DAO_Location', 'name') );

        // checkboxes for location type
        $location_type = array();
        $locationType = CRM_Core_PseudoConstant::locationType( );
        foreach ($locationType as $locationTypeID => $locationTypeName) {
            $location_type[] = HTML_QuickForm::createElement('checkbox', $locationTypeID, null, $locationTypeName);
        }
        $form->addGroup($location_type, 'location_type', ts('Location Types'), '&nbsp;');
    }

    static function activityHistory( &$form ) {
        $form->add( 'hidden', 'hidden_activityHistory', 1 );

        // textbox for Activity Type
        $form->addElement('text', 'activity_type', ts('Activity Type'),
                          CRM_Core_DAO::getAttribute('CRM_Core_DAO_ActivityHistory', 'activity_type'));

        // Date selects for activity date
        $form->add('date', 'activity_date_low', ts('Activity Dates - From'), CRM_Core_SelectValues::date('relative'));
        $form->addRule('activity_date_low', ts('Select a valid date.'), 'qfDate');

        $form->add('date', 'activity_date_high', ts('To'), CRM_Core_SelectValues::date('relative'));
        $form->addRule('activity_date_high', ts('Select a valid date.'), 'qfDate');

    }

    static function openActivity( &$form ) {
        $form->add( 'hidden', 'hidden_openActivity', 1 );

        // textbox for open Activity Type
        $form->_activityType =
            array( ''   => ' - select activity - ' ) + 
            CRM_Core_PseudoConstant::activityType( );

         $form->add('select', 'open_activity_type_id', ts('Activity Type'),
                   $form->_activityType,
                   false);
        
        // Date selects for activity date
        $form->add('date', 'open_activity_date_low', ts('Activity Dates - From'), CRM_Core_SelectValues::date('relative'));
        $form->addRule('open_activity_date_low', ts('Select a valid date.'), 'qfDate');

        $form->add('date', 'open_activity_date_high', ts('To'), CRM_Core_SelectValues::date('relative'));
        $form->addRule('open_activity_date_high', ts('Select a valid date.'), 'qfDate');
    }

    static function changeLog( &$form ) {
        $form->add( 'hidden', 'hidden_changeLog', 1 );

        // block for change log
        $form->addElement('text', 'changed_by', ts('Modified By'), null);
      
        $form->add('date', 'modified_date_low', ts('Modified Between'), CRM_Core_SelectValues::date('relative'));
        $form->addRule('modified_date_low', ts('Select a valid date.'), 'qfDate');

        $form->add('date', 'modified_date_high', ts('and'), CRM_Core_SelectValues::date('relative'));
        $form->addRule('modified_date_high', ts('Select a valid date.'), 'qfDate');
    }

    static function task( &$form ) {
        $form->add( 'hidden', 'hidden_task', 1 );

        if ( CRM_Core_Permission::access( 'Quest' ) ) {
            $form->assign( 'showTask', 1 );

            // add the task search stuff
            // we add 2 select boxes, one for the task from the task table
            $taskSelect       = array( '' => '- select -' ) + CRM_Core_PseudoConstant::tasks( );
            $form->addElement( 'select', 'task_id', ts( 'Task' ), $taskSelect );
            $form->addSelect( 'task_status', ts( 'Task Status' ) );
        }
    }

    static function relationship( &$form ) {
        $form->add( 'hidden', 'hidden_relationship', 1 );

        require_once 'CRM/Contact/BAO/Relationship.php';
        require_once 'CRM/Core/PseudoConstant.php';
        $relTypeInd =  CRM_Contact_BAO_Relationship::getContactRelationshipType(null,'null',null,'Individual');
        $relTypeOrg =  CRM_Contact_BAO_Relationship::getContactRelationshipType(null,'null',null,'Organization');
        $relTypeHou =  CRM_Contact_BAO_Relationship::getContactRelationshipType(null,'null',null,'Household');
        $allRelationshipType =array();
        $allRelationshipType = array_merge(  $relTypeInd , $relTypeOrg);
        $allRelationshipType = array_merge( $allRelationshipType, $relTypeHou);
        $form->addElement('select', 'relation_type_id', ts('Relationship Type'),  array('' => ts('- select -')) + $allRelationshipType);
        $form->addElement('text', 'relation_target_name', ts('Target Contact'), CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'sort_name') );
    }


    /**
     * Generate the custom Data Fields based
     * on the is_searchable
     *
     * @access private
     * @return void
     */
    static function custom( &$form ) {
        $form->add( 'hidden', 'hidden_custom', 1 ); 

        $groupDetails = CRM_Core_BAO_CustomGroup::getGroupDetail( null, true,
                                                                  array( 'Contact', 'Individual', 'Household', 'Organization' ) );

        $form->assign('groupTree', $groupDetails);

        foreach ($groupDetails as $key => $group) {
            $_groupTitle[$key] = $group['name'];
            CRM_Core_ShowHideBlocks::links( $form, $group['name'], '', '');
            
            $groupId = $group['id'];
            foreach ($group['fields'] as $field) {
                $fieldId = $field['id'];                
                $elementName = 'custom_' . $fieldId;
                
                CRM_Core_BAO_CustomField::addQuickFormElement( $form,
                                                               $elementName,
                                                               $fieldId,
                                                               false, false, true );
            }
        }
    }

    static function contribute( &$form ) {
        $form->add( 'hidden', 'hidden_contribute', 1 );

        require_once 'CRM/Contribute/BAO/Query.php';
        CRM_Contribute_BAO_Query::buildSearchForm( $form );
    }

    static function membership( &$form ) {
        $form->add( 'hidden', 'hidden_membership', 1 );

        require_once 'CRM/Member/BAO/Query.php';
        CRM_Member_BAO_Query::buildSearchForm( $form );
    }

    static function participant( &$form ) {
        $form->add( 'hidden', 'hidden_participant', 1 );

        require_once 'CRM/Event/BAO/Query.php';
        CRM_Event_BAO_Query::buildSearchForm( $form );
    }

    static function quest( &$form ) {
        require_once 'CRM/Quest/BAO/Query.php';
        CRM_Quest_BAO_Query::buildSearchForm( $form );
    }
}

?>