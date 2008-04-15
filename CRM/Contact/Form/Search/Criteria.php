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
        foreach ($form->_groupIterator as $groupID => $group) {
            $indentLevel = $form->_groupIterator->getCurrentNestingLevel( );
            $indent = '';
            while ( $indentLevel-- ) {
                $indent .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            }
            $groupLabel = "$group";
            $groupLabel = $indent . $groupLabel;
            $form->_groupElement =& $form->addElement('checkbox', "group[$groupID]", null, $groupLabel);
        }

        // checkboxes for categories
        foreach ($form->_tag as $tagID => $tagName) {
            $form->_tagElement =& $form->addElement('checkbox', "tag[$tagID]", null, $tagName);
        }

        // add text box for last name, first name, street name, city
        $form->addElement('text', 'sort_name', ts('Find...'), CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'sort_name') );

        // add text box for last name, first name, street name, city
        $form->add('text', 'email', ts('Contact Email'), CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'sort_name') );

        //added contact source
        $form->add('text', 'contact_source', ts('Contact Source'), CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'source') );

        // add checkbox for cms users only
        if (CIVICRM_UF != 'Standalone'){
          $form->addYesNo( 'uf_user', ts( 'CMS User?' ) );
        }
        // add search profiles
        require_once 'CRM/Core/BAO/UFGroup.php';
        $types = array( 'Participant', 'Contribution' );

        // get component profiles
        $componentProfiles = array( );
        $componentProfiles = CRM_Core_BAO_UFGroup::getProfiles($types);

        $ufGroups           =& CRM_Core_BAO_UFGroup::getModuleUFGroup('Search Profile', 1);
        $accessibleUfGroups = CRM_Core_Permission::ufGroup( CRM_Core_Permission::VIEW );

        $searchProfiles = array ( );
        foreach ($ufGroups as $key => $var) {
            if ( ! array_key_exists($key, $componentProfiles) && in_array($key, $accessibleUfGroups) ) {
                $searchProfiles[$key] = $var['title'];
            }
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

        require_once 'CRM/Core/BAO/Preferences.php';
        $addressOptions = CRM_Core_BAO_Preferences::valueOptions( 'address_options', true, null, true );
        
        $attributes = CRM_Core_DAO::getAttribute('CRM_Core_DAO_Address');
 
        $elements = array( 
                          'street_address'         => array( ts('Street Address')    ,  $attributes['street_address'], null ),
                          'city'                   => array( ts('City')              ,  $attributes['city'] , null ),
                          'postal_code'            => array( ts('Zip / Postal Code') ,  $attributes['postal_code'], null ),
                          'county'              => array( ts('County')            ,  $attributes['county_id'], 'county' ),
                          'state_province'      => array( ts('State / Province')  ,  $attributes['state_province_id'],'stateProvince' ),
                          'country'             => array( ts('Country')           ,  $attributes['country_id'], 'country' ), 
                           );
 
        foreach ( $elements as $name => $v ) {
            list( $title, $attributes, $select ) = $v;
            
            if ( ! $addressOptions[$title] ) {
                continue;
            }
 
            if ( ! $attributes ) {
                $attributes = $attributes[$name];
            }

            if ( $select ) {
                $selectElements = array( '' => ts('- select -') ) + CRM_Core_PseudoConstant::$select( );
                $form->addElement('select', $name, $title, $selectElements );
            } else {
                $form->addElement('text', $name, $title, $attributes );
            }

            // select for state province
            $stateProvince = array('' => ts('- any state/province -')) + CRM_Core_PseudoConstant::stateProvince( );
            
        }

        $worldRegions =  array('' => ts('- any region -')) + CRM_Core_PseudoConstant::worldRegion( );
        $form->addElement('select', 'world_region', ts('World Region'), $worldRegions);

        // checkboxes for location type
        $location_type = array();
        $locationType = CRM_Core_PseudoConstant::locationType( );
        foreach ($locationType as $locationTypeID => $locationTypeName) {
            $location_type[] = HTML_QuickForm::createElement('checkbox', $locationTypeID, null, $locationTypeName);
        }
        $form->addGroup($location_type, 'location_type', ts('Location Types'), '&nbsp;');
    }

    static function activity( &$form ) 
    {
        $form->add( 'hidden', 'hidden_activity', 1 );

        // textbox for Activity Type
        $form->_activityType =
            array( ''   => ' - select activity - ' ) + 
            CRM_Core_PseudoConstant::activityType( );

        // we need to remove some activity types
        CRM_Utils_Array::crmArraySplice( $form->_activityType, 4, 9);

        $form->add('select', 'activity_type_id', ts('Activity Type'),
                   $form->_activityType,
                   false);
        
        // Date selects for activity date
        $form->add('date', 'activity_date_low', ts('Activity Dates - From'), CRM_Core_SelectValues::date('relative'));
        $form->addRule('activity_date_low', ts('Select a valid date.'), 'qfDate');

        $form->add('date', 'activity_date_high', ts('To'), CRM_Core_SelectValues::date('relative'));
        $form->addRule('activity_date_high', ts('Select a valid date.'), 'qfDate');
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

        if ( CRM_Core_Permission::access( 'Quest' ) || CRM_Core_Permission::access( 'TMF' )) {
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
        $relStatusOption  = array( ts('Active '), ts('Inactive '), ts('All') );
        $form->addRadio( 'relation_status', ts( 'Relationship Status' ), $relStatusOption);
        $form->setDefaults(array('relation_status' => 0));
        
    }
    
    static function demographics( &$form ) {
        $form->add( 'hidden', 'hidden_demographics', 1 );
        // radio button for gender
        $genderOptions = array( );
        $gender =CRM_Core_PseudoConstant::gender();
        foreach ($gender as $key => $var) {
            $genderOptions[$key] = HTML_QuickForm::createElement('radio', null, ts('Gender'), $var, $key);
        }
        $form->addGroup($genderOptions, 'gender', ts('Gender'));
         

        // Date selects for birth date
        $form->add('date', 'birth_date_low', ts('Birth Dates - From'), CRM_Core_SelectValues::date('birth'));
        $form->addRule('birth_date_low', ts('Select a valid date.'), 'qfDate');

        $form->add('date', 'birth_date_high', ts('To'), CRM_Core_SelectValues::date('birth'));
        $form->addRule('birth_date_high', ts('Select a valid date.'), 'qfDate');


        // Date selects for deceased date
        $form->add('date', 'deceased_date_low', ts('Deceased Dates - From'), CRM_Core_SelectValues::date('birth'));
        $form->addRule('deceased_date_low', ts('Select a valid date.'), 'qfDate');

        $form->add('date', 'deceased_date_high', ts('To'), CRM_Core_SelectValues::date('birth'));
        $form->addRule('deceased_date_high', ts('Select a valid date.'), 'qfDate');
    
    }
    
    static function notes( &$form ) {
        $form->add( 'hidden', 'hidden_notes', 1 );

        $form->addElement('text', 'note', ts('Note Text'), CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'sort_name') );
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
        $extends      = array( 'Contact', 'Individual', 'Household', 'Organization' );
        $groupDetails = CRM_Core_BAO_CustomGroup::getGroupDetail( null, true,
                                                                  $extends );

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
        $form->add( 'hidden', 'hidden_quest', 1 );
        require_once 'CRM/Quest/BAO/Query.php';
        CRM_Quest_BAO_Query::buildSearchForm( $form );
    }

    static function tmf( &$form ) {
        $form->add( 'hidden', 'hidden_TMF', 1 );
        require_once 'CRM/TMF/BAO/Query.php';
        CRM_TMF_BAO_Query::buildSearchForm( $form );
    }

    static function kabissa( &$form ) {
        $form->add( 'hidden', 'hidden_kabissa', 1 );
        require_once 'CRM/Kabissa/BAO/Query.php';
        CRM_Kabissa_BAO_Query::buildSearchForm( $form );
    }

    static function caseSearch( &$form ) {
        $form->add( 'hidden', 'hidden_caseSearch', 1 );
        require_once 'CRM/Case/BAO/Query.php';
        CRM_Case_BAO_Query::buildSearchForm( $form );
    }

    static function grant( &$form ) {
        $form->add( 'hidden', 'hidden_grant', 1 );
        require_once 'CRM/Grant/BAO/Query.php';
        CRM_Grant_BAO_Query::buildSearchForm( $form );
    }
    
}


