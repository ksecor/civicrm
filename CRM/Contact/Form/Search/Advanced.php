<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

/**
 * Files required
 */

require_once 'CRM/Core/ShowHideBlocks.php';
require_once 'CRM/Core/BAO/CustomGroup.php';
require_once 'CRM/Core/BAO/CustomOption.php';

require_once 'CRM/Contact/Form/Search.php';

/**
 * advanced search, extends basic search
 */
class CRM_Contact_Form_Search_Advanced extends CRM_Contact_Form_Search {

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) 
    {
        // add checkboxes for contact type
        $contact_type = array( );
        foreach (CRM_Core_SelectValues::contactType() as $k => $v) {
            if ( ! empty( $k ) ) {
                $contact_type[] = HTML_QuickForm::createElement('checkbox', $k, null, $v);
            }
        }
        $this->addGroup($contact_type, 'contact_type', ts('Contact Type(s)'), '<br />');
        
        // checkboxes for groups
        $group = array();
        foreach ($this->_group as $groupID => $groupName) {
            $this->_groupElement =& $this->addElement('checkbox', "group[$groupID]", null, $groupName);
        }

        // checkboxes for categories
        foreach ($this->_tag as $tagID => $tagName) {
            $this->_tagElement =& $this->addElement('checkbox', "tag[$tagID]", null, $tagName);
        }

        // add text box for last name, first name, street name, city
        $this->addElement('text', 'sort_name', ts('Find...'), CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'sort_name') );
        
        // add search profiles
        require_once 'CRM/Core/BAO/UFGroup.php';
        $ufGroups =& CRM_Core_BAO_UFGroup::getModuleUFGroup('Search Profile', 1);
        $searchProfiles = array ( );
        foreach ($ufGroups as $key => $var) {
            $searchProfiles[$key] = $var['title'];
        }
        
        $this->addElement('select', 'uf_group_id', ts('Search Views'),  array('' => ts('- default view -')) + $searchProfiles);

        $this->addElement('text', 'street_address', ts('Street Address'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_Address', 'street_address'));
        $this->addElement('text', 'city', ts('City'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_Address', 'city'));

        // select for state province
        $stateProvince = array('' => ts('- any state/province -')) + CRM_Core_PseudoConstant::stateProvince( );
        $this->addElement('select', 'state_province', ts('State/Province'), $stateProvince);

        // select for country
        $country = array('' => ts('- any country -')) + CRM_Core_PseudoConstant::country( );
        $this->addElement('select', 'country', ts('Country'), $country);

        // add text box for postal code
        $this->addElement('text', 'postal_code', ts('Postal Code'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_Address', 'postal_code') );
        
        $this->addElement('text', 'postal_code_low', ts('Range-From'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_Address','postal_code') );

        $this->addElement('text', 'postal_code_high', ts('To'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_Address', 'postal_code') );

        $this->addElement('text', 'location_name', ts('Location Name'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_Location', 'name') );

        // checkboxes for DO NOT phone, email, mail
        // we take labels from SelectValues
        $t = CRM_Core_SelectValues::privacy();
        $privacy[] = HTML_QuickForm::createElement('advcheckbox', 'do_not_phone', null, $t['do_not_phone']);
        $privacy[] = HTML_QuickForm::createElement('advcheckbox', 'do_not_email', null, $t['do_not_email']);
        $privacy[] = HTML_QuickForm::createElement('advcheckbox', 'do_not_mail' , null, $t['do_not_mail']);
        $privacy[] = HTML_QuickForm::createElement('advcheckbox', 'do_not_trade', null, $t['do_not_trade']);
        
        $this->addGroup($privacy, 'privacy', ts('Privacy'), '&nbsp;');

        // checkboxes for location type
        $location_type = array();
        $locationType = CRM_Core_PseudoConstant::locationType( );
        foreach ($locationType as $locationTypeID => $locationTypeName) {
            $location_type[] = HTML_QuickForm::createElement('checkbox', $locationTypeID, null, $locationTypeName);
        }
        $this->addGroup($location_type, 'location_type', ts('Location Types'), '&nbsp;');
        
        // textbox for Activity Type
        $this->addElement('text', 'activity_type', ts('Activity Type'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_ActivityHistory', 'activity_type'));

        // Date selects for activity date
        $this->add('date', 'activity_date_low', ts('Activity Dates - From'), CRM_Core_SelectValues::date('relative'));
        $this->addRule('activity_date_low', ts('Select a valid date.'), 'qfDate');

        $this->add('date', 'activity_date_high', ts('To'), CRM_Core_SelectValues::date('relative'));
        $this->addRule('activity_date_high', ts('Select a valid date.'), 'qfDate');

        require_once 'CRM/Core/Component.php';
        CRM_Core_Component::buildSearchForm( $this );

        //relationsship fileds
        
        require_once 'CRM/Contact/BAO/Relationship.php';
        require_once 'CRM/Core/PseudoConstant.php';
        $relTypeInd =  CRM_Contact_BAO_Relationship::getContactRelationshipType(null,'null',null,'Individual');
        $relTypeOrg =  CRM_Contact_BAO_Relationship::getContactRelationshipType(null,'null',null,'Organization');
        $relTypeHou =  CRM_Contact_BAO_Relationship::getContactRelationshipType(null,'null',null,'Household');
        $allRelationshipType =array();
        $allRelationshipType = array_merge(  $relTypeInd , $relTypeOrg);
        $allRelationshipType = array_merge( $allRelationshipType, $relTypeHou);
        $this->addElement('select', 'relation_type_id', ts('Relationship Type'),  array('' => ts('- select -')) + $allRelationshipType);
        $this->addElement('text', 'relation_target_name', ts('Target Contact'), CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'sort_name') );
        //Custom data Search Fields
        $this->customDataSearch();
        
        $this->buildQuickFormCommon();
    }

    /**
     * Fix what blocks to show/hide based on the default values set
     *
     * @param    array    array of Group Titles
     * @param    array    array of Group Collapse Display 
     *
     * @return   
     *
     * @access   protected
     */
    
    protected function setShowHide(&$groupTitle)
    {
        $showHide =& new CRM_Core_ShowHideBlocks('','');
        
        $showHide->addHide( 'relationship' );
        $showHide->addShow( 'relationship[show]' );
        
        CRM_Core_Component::addShowHide( $showHide );

        if ( ! empty( $groupTitle ) ) {
            foreach ($groupTitle as $key => $title) {
                $showBlocks = $title . '[show]' ;
                $hideBlocks = $title;
                
                $showHide->addShow($hideBlocks);
                $showHide->addHide($showBlocks);
            }
        }

        $showHide->addToTemplate();
    }

    /**
     * Generate the custom Data Fields based
     * on the is_searchable
     *
     * @access private
     * @return void
     */
    public function customDataSearch() {
        
        // expand on search result if criteria entered
        
        $customDataSearch = $this->get('customDataSearch');
        if ( !empty($customDataSearch)) {
            $customAssignHide = array();
            $customAssignShow = array();
            foreach(array_unique($customDataSearch) as $v) {
                $customAssignHide[] = $v . '[show]';
                $customAssignShow[] = $v;
            }
            
            $customShow = '"' . implode("\",\"",$customAssignShow) . '"';
            $customHide = '"' . implode("\",\"",$customAssignHide) . '"';

            $this->assign('customShow', $customShow);
            $this->assign('customHide', $customHide);
        }

        $groupDetails = CRM_Core_BAO_CustomGroup::getGroupDetail( null, true, array( 'Contact', 'Individual', 'Household', 'Organization' ) );
        $this->assign('groupTree', $groupDetails);

        foreach ($groupDetails as $group) {
            $_groupTitle[]           = $group['title'];
            CRM_Core_ShowHideBlocks::links( $this, $group['title'], '', '');
            
            $groupId = $group['id'];
            foreach ($group['fields'] as $field) {
                $fieldId = $field['id'];                
                $elementName = 'custom_' . $fieldId;
                
                CRM_Core_BAO_CustomField::addQuickFormElement( $this,
                                                               $elementName,
                                                               $fieldId,
                                                               false, false, true );
            }
        }

        $this->setShowHide($_groupTitle);
    }
    
    /**
     * Set the default form values
     *
     * @access protected
     * @return array the default array reference
     */
    function &setDefaultValues() {
        $defaults = $this->_formValues;

        if ( $this->_context === 'amtg' ) {
            $defaults['task'] = CRM_Contact_Task::GROUP_CONTACTS;
        } else {
            $defaults['task'] = CRM_Contact_Task::PRINT_CONTACTS;
        }

       return $defaults;
    }

    /**
     * The post processing of the form gets done here.
     *
     * Key things done during post processing are
     *      - check for reset or next request. if present, skip post procesing.
     *      - now check if user requested running a saved search, if so, then
     *        the form values associated with the saved search are used for searching.
     *      - if user has done a submit with new values the regular post submissing is 
     *        done.
     * The processing consists of using a Selector / Controller framework for getting the
     * search results.
     *
     * @param
     *
     * @return void 
     * @access public
     */
    function postProcess() 
    {
        $session =& CRM_Core_Session::singleton();
        $session->set('isAdvanced', '1');

        // get user submitted values
        // get it from controller only if form has been submitted, else preProcess has set this
        if ( ! empty( $_POST ) ) {
            $this->_formValues = $this->controller->exportValues($this->_name);
            
            // set the group if group is submitted
            if ($this->_formValues['uf_group_id']) {
                $this->set( 'id', $this->_formValues['uf_group_id'] ); 
            } else {
                $this->set( 'id', '' ); 
            }
            
            // also reset the sort by character 
            $this->_sortByCharacter = null; 
            $this->set( 'sortByCharacter', null ); 
        }

        // retrieve ssID values only if formValues is null, i.e. form has never been posted
        if ( empty( $this->_formValues ) && isset( $this->_ssID ) ) {
            $this->_formValues = CRM_Contact_BAO_SavedSearch::getFormValues( $this->_ssID );
        }

        if ( isset( $this->_groupID ) && ! CRM_Utils_Array::value( 'group', $this->_formValues ) ) {
            $this->_formValues['group'] = array( $this->_groupID => 1 );
        }
      
        $this->_params =& $this->convertFormValues( $this->_formValues );
        $this->postProcessCommon( );
    }

}

?>
