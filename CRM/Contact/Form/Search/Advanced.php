<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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

        $this->_formType = CRM_Utils_Array::value( 'formType', $_GET );
        
        if ( ! $this->_formType || $this->_formType == 'basic' ) {
            CRM_Contact_Form_Search_Criteria::basic          ( $this );
        }

        $allPanes = array( );
        $paneNames = array( ts('Address Fields')        => 'location'       ,
                            ts('Custom Fields')         => 'custom'         ,
                            ts('Activities')            => 'activity'       ,
                            ts('Relationships')         => 'relationship'   ,
                            ts('Notes')                 => 'notes'          ,
                            ts('Change Log')            => 'changeLog'      
                            );

        //check if there are any custom data searable fields
        $groupDetails = array( );
        $extends      = array( 'Contact', 'Individual', 'Household', 'Organization' );
        $groupDetails = CRM_Core_BAO_CustomGroup::getGroupDetail( null, true,
                                                                  $extends );
        // if no searable fields unset panel
        if ( empty( $groupDetails) ) {
            unset( $paneNames[ts('Custom Fields')] );
        }

        if ( CRM_Core_Permission::access( 'CiviContribute' ) ) {
            $paneNames[ts('Contributions')] = 'contribute';
        }
        
        if ( CRM_Core_Permission::access( 'CiviMember' ) ) {
            $paneNames[ts('Memberships')] = 'membership';
        }
        
        if ( CRM_Core_Permission::access( 'CiviEvent' ) ) {
            $paneNames[ts('Events')] = 'participant';
        }

        if ( CRM_Core_Permission::access( 'CiviGrant' ) ) {
            $paneNames[ts('Grants')] = 'grant';
        }

        if ( CRM_Core_Permission::access( 'Quest' ) ) {
            $paneNames[ts('Quest')] = 'quest';
            $paneNames[ts('Task') ] = 'task';                
        }

        if ( CRM_Core_Permission::access( 'TMF' ) ) {
            $paneNames[ts('TMF')] = 'TMF';
            $paneNames[ts('Task') ] = 'task';                
        }

        if ( CRM_Core_Permission::access( 'Kabissa' ) ) {
            $paneNames[ts('Kabissa')] = 'kabissa';
        }

        require_once 'CRM/Core/BAO/Preferences.php';
        $this->_viewOptions = CRM_Core_BAO_Preferences::valueOptions( 'contact_view_options', true, null, true );

        if ( $this->_viewOptions['Cases'] ) { 
            $paneNames[ts('Cases')] = 'caseSearch';
        }

        $this->_searchOptions = CRM_Core_BAO_Preferences::valueOptions( 'advanced_search_options', true, null, true );
        foreach ( $paneNames as $name => $type ) {
            if ( ! $this->_searchOptions[$name] && $name != ts( 'User SQL' ) ) {
                continue;
            }

            $allPanes[$name] = array( 'url' => CRM_Utils_System::url( 'civicrm/contact/search/advanced',
                                                                        "snippet=1&formType=$type" ),
                                        'open' => 'false',
                                        'id'   => $type );
            
            // see if we need to include this paneName in the current form
            if ( $this->_formType == $type ||
                 CRM_Utils_Array::value( "hidden_{$type}", $_POST ) ||
                 CRM_Utils_Array::value( "hidden_{$type}", $this->_formValues ) ) {
                $allPanes[$name]['open'] = 'true';
                eval( 'CRM_Contact_Form_Search_Criteria::' . $type . '( $this );' );
            }
        }

        $this->assign( 'allPanes', $allPanes );
        $this->assign( 'dojoIncludes', "dojo.require('civicrm.TitlePane');dojo.require('dojo.parser');" );

        if ( ! $this->_formType ) {
            parent::buildQuickForm();
        } else {
            $this->assign( 'suppressForm', true );
        }
    }

    function getTemplateFileName() {
        if ( ! $this->_formType ) {
            return parent::getTemplateFileName( );
        } else {
            $name = ucfirst( $this->_formType );
            return "CRM/Contact/Form/Search/Criteria/{$name}.tpl";
        }
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
    function postProcess( ) 
    {
        $session =& CRM_Core_Session::singleton();
        $session->set('isAdvanced', '1');

        // get user submitted values
        // get it from controller only if form has been submitted, else preProcess has set this
        if ( ! empty( $_POST ) ) {
            $this->_formValues = $this->controller->exportValues( $this->_name );
            if ( array_key_exists(  'contribution_amount_low', $this->_formValues ) ) {
                foreach ( array( 'contribution_amount_low', 'contribution_amount_high' ) as $f ) {
                    $this->_formValues[$f] = CRM_Utils_Rule::cleanMoney( $this->_formValues[$f] );
                }
            }
            
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
      
        // we dont want to store the sortByCharacter in the formValue, it is more like 
        // a filter on the result set
        // this filter is reset if we click on the search button
        if ( $this->_sortByCharacter && empty( $_POST ) ) {
            if ( $this->_sortByCharacter == 1 ) {
                $this->_formValues['sortByCharacter'] = null;
            } else {
                $this->_formValues['sortByCharacter'] = $this->_sortByCharacter;
            }
        }

        require_once 'CRM/Contact/BAO/Query.php';
        $this->_params =& CRM_Contact_BAO_Query::convertFormValues( $this->_formValues );
        $this->_returnProperties =& $this->returnProperties( );
        parent::postProcess( );
    }

}


