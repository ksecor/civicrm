<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
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

        require_once 'CRM/Contact/Form/Search/Criteria.php';

        CRM_Contact_Form_Search_Criteria::basic          ( $this );
        CRM_Contact_Form_Search_Criteria::location       ( $this );
        CRM_Contact_Form_Search_Criteria::activityHistory( $this );
        CRM_Contact_Form_Search_Criteria::openActivity   ( $this );
        CRM_Contact_Form_Search_Criteria::changeLog      ( $this );
        CRM_Contact_Form_Search_Criteria::task           ( $this );
        CRM_Contact_Form_Search_Criteria::relationship   ( $this );

        // add task components
        require_once 'CRM/Core/Component.php';
        CRM_Core_Component::buildSearchForm( $this );

        //relationship fields
        
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
    
    protected function setShowHide(&$groupTitle , $groupDetails = null)
    {
        $showHide =& new CRM_Core_ShowHideBlocks('','');
        
        $showHide->addHide( 'relationship' );
        $showHide->addShow( 'relationship_show' );

        $showHide->addHide( 'changelog' );
        $showHide->addShow( 'changelog_show' );

        $showHide->addHide( 'openAtcivity' );
        $showHide->addShow( 'openAtcivity_show' );

        $showHide->addHide( 'atcivityHistory' );
        $showHide->addShow( 'atcivityHistory_show' );

        $showHide->addHide( 'location' );
        $showHide->addShow( 'location_show' );
        
        if ( CRM_Core_Permission::access( 'Quest' ) ) {
            $showHide->addHide( 'task_block' );
            $showHide->addShow( 'task_show' );
        }

        CRM_Core_Component::addShowHide( $showHide );

        if ( ! empty( $groupTitle ) ) {
            foreach ($groupTitle as $key => $title) {
                if( !empty($groupDetails) ) {
                    if( $groupDetails[$key]['collapse_display'] ) {
                        $hideBlocks = $title . '_show' ;
                        $showBlocks = $title;
                    } else {
                        $showBlocks = $title . '_show' ;
                        $hideBlocks = $title;
                    }
                    
                } else {
                    $showBlocks = $title . '_show' ;
                    $hideBlocks = $title;
                }
                                
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
                $customAssignHide[] = $v . '_show';
                $customAssignShow[] = $v;
            }
            
            $customShow = '"' . implode("\",\"",$customAssignShow) . '"';
            $customHide = '"' . implode("\",\"",$customAssignHide) . '"';

            $this->assign('customShow', $customShow);
            $this->assign('customHide', $customHide);
        }

        $groupDetails = CRM_Core_BAO_CustomGroup::getGroupDetail( null, true, array( 'Contact', 'Individual', 'Household', 'Organization' ) );
        $this->assign('groupTree', $groupDetails);

        foreach ($groupDetails as $key => $group) {
            $_groupTitle[$key] = $group['name'];
            CRM_Core_ShowHideBlocks::links( $this, $group['name'], '', '');
            
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
        $this->setShowHide($_groupTitle , $groupDetails );
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
        $this->_returnProperties =& $this->returnProperties( );
        $this->postProcessCommon( );
    }

}

?>
