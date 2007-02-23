<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
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
                            ts('Activity History')      => 'activityHistory',
                            ts('Scheduled Activities')  => 'openActivity'   ,
                            ts('Relationships')         => 'relationship'   ,
                            ts('Notes')                 => 'notes'          ,
                            ts('Change Log')            => 'changeLog'       );

        if ( CRM_Core_Permission::access( 'CiviContribute' ) ) {
            $paneNames[ts('Contributions')] = 'contribute';
        }
        
        if ( CRM_Core_Permission::access( 'CiviMember' ) ) {
            $paneNames[ts('Memberships')] = 'membership';
        }
        
        if ( CRM_Core_Permission::access( 'CiviEvent' ) ) {
            $paneNames[ts('Events')] = 'participant';
        }
        if ( CRM_Core_Permission::access( 'Quest' ) ) {
            $paneNames[ts('Quest')] = 'quest';
            $paneNames[ts('Task' )] = 'task';                
        }

        if ( CRM_Core_Permission::access( 'TMF' ) ) {
            $paneNames[ts('TMF')] = 'TMF';
            $paneNames[ts('Task' )] = 'task';                
        }

        foreach ( $paneNames as $name => $type ) {
            $allPanes[$name] = array( 'url' => CRM_Utils_System::url( 'civicrm/contact/search/advanced',
                                                                      "snippet=1&formType=$type" ),
                                      'open' => 'false',
                                      'id'   => $type );
            
            // see if we need to include this paneName in the current form
            if ( $this->_formType == $type ||
                 isset( $_POST[ "hidden_{$type}" ] ) ) {
                $allPanes[$name]['open'] = 'true';
                eval( 'CRM_Contact_Form_Search_Criteria::' . $type . '( $this );' );
            }
        }

        $this->assign( 'allPanes', $allPanes );
        $this->assign( 'dojoIncludes', "dojo.require('dojo.widget.TitlePane');" );

        if ( ! $this->_formType ) {
            $this->buildQuickFormCommon();
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
        $this->postProcessCommon( );
    }

}

?>
