<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

/**
 * Files required
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/Session.php';
require_once 'CRM/Core/PseudoConstant.php';

require_once 'CRM/Utils/PagerAToZ.php';

require_once 'CRM/Contact/Selector/Controller.php';
require_once 'CRM/Contact/Selector.php';
require_once 'CRM/Contact/Task.php';

/**
 * Base Search / View form for *all* listing of multiple 
 * contacts
 */
class CRM_Contact_Form_Search extends CRM_Core_Form {
    /*
     * list of valid contexts
     *
     * @var array
     * @static
     */
    static $_validContext = null;

    /**
     * The context that we are working on
     *
     * @var string
     * @access protected
     */
    protected $_context;

    /**
     * the groupId retrieved from the GET vars
     *
     * @var int
     * @access protected
     */
    protected $_groupID;

    /**
     * the Group ID belonging to Add Member to group ID
     * retrieved from the GET vars
     *
     * @var int
     * @access protected
     */
    protected $_amtgID;

    /**
     * the saved search ID retrieved from the GET vars
     *
     * @var int
     * @access protected
     */
    protected $_ssID;

    /**
     * Are we forced to run a search
     *
     * @var int
     * @access protected
     */
    protected $_force;

    /**
     * name of search button
     *
     * @var string
     * @access protected
     */
    protected $_searchButtonName;

    /**
     * name of export button
     *
     * @var string
     * @access protected
     */
    protected $_exportButtonName;

    
    /**
     * name of export button
     *
     * @var string
     * @access protected
     */
    protected $_actionButtonName;

    /**
     * the group elements
     *
     * @var array
     * @access protected
     */
    protected $_group;
    protected $_groupElement;

    /**
     * the tag elements
     *
     * @var array 
     * @access protected
     */
    protected $_tag;
    protected $_tagElement;

    /**
     * form values that we will be using
     *
     * @var array
     * @access protected
     */
    protected $_formValues;

    /**
     * The sort by character
     * 
     * @var string
     * @access protected
     */
    protected $_sortByCharacter;

    /*
     * csv - common search values
     *
     * @var array
     * @access protected
     * @static
     */
    static $csv = array('contact_type', 'group', 'tag');

    /**
     * have we already done this search
     *
     * @access protected
     * @var boolean
     */
    protected $_done;

    /**
     * define the set of valid contexts that the search form operates on
     *
     * @return array the valid context set and the titles
     * @access protected
     * @static
     */
    static function &validContext()
    {
        if (!(self::$_validContext)) {
            self::$_validContext = array(
                'search' => 'Search',
                'smog'   => 'Show members of group',
                'amtg'   => 'Add members to group'
            );
        }
        return self::$_validContext;
    }
    
    /**
     * Build the common elements between the search/advanced form
     *
     * @access public
     * @return void
     */
    function buildQuickFormCommon()
    {

        // some tasks.. what do we want to do with the selected contacts ?
        $tasks = array( '' => ts('- more actions -') ) + CRM_Contact_Task::tasks();
        if ( isset( $this->_ssID ) ) {
            $tasks = $tasks + CRM_Contact_Task::optionalTasks();

            $savedSearchValues = array( 'id' => $this->_ssID, 'name' => CRM_Contact_BAO_SavedSearch::getName( $this->_ssID ) );
            $this->assign_by_ref( 'savedSearch', $savedSearchValues );
        }

        if ( $this->_context === 'smog' ) {
            // need to figure out how to freeze a bunch of checkboxes, hack for now
            if ( $this->_action != CRM_Core_Action::ADVANCED ) {
                $this->_groupElement->freeze( );
            }
            
            // also set the group title
            $groupValues = array( 'id' => $this->_groupID, 'title' => $this->_group[$this->_groupID] );
            $this->assign_by_ref( 'group', $groupValues );
            $cb_group_contact_status = array();
            foreach(CRM_Core_SelectValues::groupContactStatus() as $k => $v) {
                if (! empty($k)) {
                    $cb_group_contact_status[] =
                        HTML_QuickForm::createElement('checkbox', $k, null, $v);
                }
            }
            $this->addGroup($cb_group_contact_status,
                            'cb_group_contact_status', ts('Group Status'));
            $this->addGroupRule('cb_group_contact_status', ts('Please select at least one membership status.'), 'required', null, 1);
            // Set dynamic page title for 'Show Members of Group'
            CRM_Utils_System::setTitle( ts('Group Members: %1', array(1 => $this->_group[$this->_groupID])) );
        }
        
        /*
         * add the go button for the action form, note it is of type 'next' rather than of type 'submit'
         *
         */
        if ( $this->_context === 'amtg' ) {
            // Set dynamic page title for 'Add Members Group'
            CRM_Utils_System::setTitle( ts('Add Members: %1', array(1 => $this->_group[$this->_amtgID])) );
            // also set the group title and freeze the action task with Add Members to Group
            $groupValues = array( 'id' => $this->_amtgID, 'title' => $this->_group[$this->_amtgID] );
            $this->assign_by_ref( 'group', $groupValues );
            $this->add('submit', $this->_actionButtonName, ts('Add Contacts to %1', array(1 => $this->_group[$this->_amtgID])),
                       array( 'class' => 'form-submit',
                              'onclick' => "return checkPerformAction('mark_x', '".$this->getName()."', 1);" ) );
            $this->add('hidden','task', CRM_Contact_Task::GROUP_CONTACTS );

        } else {
            $this->add('select', 'task'   , ts('Actions: ')    , $tasks    );
            $this->add('submit', $this->_actionButtonName, ts('Go'),
                       array( 'class' => 'form-submit',
                          'onclick' => "return checkPerformAction('mark_x', '".$this->getName()."', 0);" ) );
        }

        // need to perform tasks on all or selected items ? using radio_ts(task selection) for it
        $this->addElement('radio', 'radio_ts', null, '', 'ts_sel', array( 'checked' => null) );
        $this->addElement('radio', 'radio_ts', null, '', 'ts_all', array( 'onchange' => "changeCheckboxVals('mark_x_','deselect', Search ); return false;" ) );

        /*
         * add form checkboxes for each row. This is needed out here to conform to QF protocol
         * of all elements being declared in builQuickForm
         */
        $rows = $this->get( 'rows' );
        if ( is_array( $rows ) ) {
            $this->addElement( 'checkbox', 'toggleSelect', null, null, array( 'onChange' => "return toggleCheckboxVals('mark_x_',this.form);" ) );
            // does not work in php4 . pls revert when done with php4
            //foreach ( $rows as &$row ) {
            foreach ($rows as $row) {
                $this->addElement( 'checkbox', $row['checkbox'],
                                   null, null,
                                   array( 'onclick' => "return checkSelectedBox('" . $row['checkbox'] . "', '" . $this->getName() . "');" ) );
            }
        }

        // add buttons
        $this->addButtons( array(
                                 array ( 'type'      => 'refresh',
                                         'name'      => ts('Search') ,
                                         'isDefault' => true     )
                                 )        
                           );

        $this->add('submit', $this->_exportButtonName, ts('Export'),
                   array( 'class' => 'form-submit',
                          'onclick' => "return checkPerformAction('mark_x', '".$this->getName()."', 1);" ) );
        $this->add('submit', $this->_printButtonName, ts('Print'),
                   array( 'class' => 'form-submit',
                          'onclick' => "return checkPerformAction('mark_x', '".$this->getName()."', 1);" ) );
        
        $this->setDefaultAction( 'refresh' );

    }
    
    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) 
    {
        $this->add('select', 'contact_type', ts('Find...'), CRM_Core_SelectValues::contactType());

        // add select for groups
        $group               = array('' => ts(' - any group - ')) + $this->_group;
        $this->_groupElement =& $this->addElement('select', 'group', ts('in'), $group);

        // add select for categories
        $tag = array('' => ts(' - any tag - ')) + $this->_tag;
        $this->_tagElement =& $this->addElement('select', 'tag', ts('Tagged'), $tag);

        // text for sort_name
        //$this->add('text', 'sort_name', ts('Name or email'), CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'sort_name'), 'onkeyup="getSearchResult(this,event, false);"  onblur="getSearchResult(this,event, false);" autocomplete="off"' );
        
        $this->add('text', 'sort_name', ts('Name or email'), 'onkeyup="getSearchResult(this,event, false);"  onblur="getSearchResult(this,event, false);" autocomplete="off"' );

        $this->buildQuickFormCommon( );
    }

    /**
     * Set the default form values
     *
     * @access protected
     * @return array the default array reference
     */
    function &setDefaultValues() {
        $defaults = array();

        $defaults['sort_name'] = CRM_Utils_Array::value( 'sort_name', $this->_formValues );
        foreach (self::$csv as $v) {
            if ( CRM_Utils_Array::value( 'cb_' . $v, $this->_formValues ) && is_array( $this->_formValues['cb_' . $v] ) ) {
                $tmpArray = array_keys( $this->_formValues['cb_' . $v] );
                $defaults[$v] = array_pop( $tmpArray );
            } else {
                $defaults[$v] = '';
            }
        }

        if ( $this->_context === 'amtg' ) {
            $defaults['task'] = CRM_Contact_Task::GROUP_CONTACTS;
        } else {
            $defaults['task'] = CRM_Contact_Task::PRINT_CONTACTS;
        }

        if ( $this->_context === 'smog' ) {
            $defaults['cb_group_contact_status[Added]'] = true;
        }

        return $defaults;
    }

    /**
     * Add local and global form rules
     *
     * @access protected
     * @return void
     */
    function addRules( ) {
        $this->addFormRule( array( 'CRM_Contact_Form_Search', 'formRule' ) );
    }

    /**
     * processing needed for buildForm and later
     *
     * @return void
     * @access public
     */
    function preProcess( ) {
        /**
         * set the varios class variables
         */
        $this->_group    =& CRM_Core_PseudoConstant::group( );
        $this->_tag      =& CRM_Core_PseudoConstant::tag  ( );
        $this->_done     =  false;

        /**
         * set the button names
         */
        $this->_searchButtonName = $this->getButtonName( 'refresh' );
        $this->_exportButtonName = $this->getButtonName( 'refresh', 'export' );
        $this->_printButtonName  = $this->getButtonName( 'next'   , 'print' );
        $this->_actionButtonName = $this->getButtonName( 'next'   , 'action' );
        
        /*
         * we allow the controller to set force/reset externally, useful when we are being
         * driven by the wizard framework
         */
        $nullObject = null;
        $this->_reset   = CRM_Utils_Request::retrieve( 'reset', $nullObject );

        $this->_force   = CRM_Utils_Request::retrieve( 'force', $this, false );

        // we only force stuff once :)
        $this->set( 'force', false );

        $this->_groupID         = CRM_Utils_Request::retrieve( 'gid'            , $this );
        $this->_amtgID          = CRM_Utils_Request::retrieve( 'amtgID'         , $this );
        $this->_ssID            = CRM_Utils_Request::retrieve( 'ssID'           , $this );
        $this->_sortByCharacter = CRM_Utils_Request::retrieve( 'sortByCharacter', $this );

        $this->_formValues = $this->get( 'formValues' );

        // we only retrieve the saved search values if out current values are null
        if ( empty( $this->_formValues ) && isset( $this->_ssID ) ) {
            $this->_formValues = CRM_Contact_BAO_SavedSearch::getFormValues( $this->_ssID );
        }

        /*
         * assign context to drive the template display, make sure context is valid
         */
        $this->_context = CRM_Utils_Request::retrieve( 'context', $this, false, 'search' );
        if ( ! CRM_Utils_Array::value( $this->_context, self::validContext() ) ) {
            $this->_context = 'search';
            $this->set( 'context', $this->_context );
        }
        $this->assign( 'context', $this->_context );

        $selector =& new CRM_Contact_Selector($this->_formValues, $this->_action);
        $controller =& new CRM_Contact_Selector_Controller($selector ,
                                                           $this->get( CRM_Utils_Pager::PAGE_ID ),
                                                           $this->get( CRM_Utils_Sort::SORT_ID  ),
                                                           CRM_Core_Action::VIEW, $this, CRM_Core_Selector_Controller::TRANSFER );
        $controller->setEmbedded( true );
        if ( $this->_force ) {

            $this->postProcess( );
            /*
             * Note that we repeat this, since the search creates and stores
             * values that potentially change the controller behavior. i.e. things
             * like totalCount etc
             */
            $sortID = null;
            if ( $this->get( CRM_Utils_Sort::SORT_ID  ) ) {
                $sortID = CRM_Utils_Sort::sortIDValue( $this->get( CRM_Utils_Sort::SORT_ID  ),
                                                       $this->get( CRM_Utils_Sort::SORT_DIRECTION ) );
            }
            $controller =& new CRM_Contact_Selector_Controller($selector ,
                                                               $this->get( CRM_Utils_Pager::PAGE_ID ),
                                                               $sortID,
                                                               CRM_Core_Action::VIEW, $this, CRM_Core_Selector_Controller::TRANSFER );
            $controller->setEmbedded( true );
        }
        
        $controller->moveFromSessionToTemplate();

    }

    /**
     * this method is called for processing a submitted search form
     *
     * @return void
     * @access public
     */
    function postProcess( ) {
        
        $session =& CRM_Core_Session::singleton();
        $session ->set('isAdvanced','0');

        // get user submitted values
        // get it from controller only if form has been submitted, else preProcess has set this
        if ( ! empty( $_POST ) ) {
            $this->_formValues = $this->controller->exportValues($this->_name);

            // also reset the sort by character
            $this->_sortByCharacter = null;
            $this->set( 'sortByCharacter', null );
        }

        /* after every search form is submitted we save the following in the session
         *     - type of search 'type'
         *     - submitted form values 'formValues'
         *     - QILL 'qill'
         */
        if ( isset( $this->_groupID ) && ! CRM_Utils_Array::value( 'group', $this->_formValues ) ) {
            $this->_formValues['group'] = $this->_groupID;

            // add group_contact_status as added if not present
            if ( ! CRM_Utils_Array::value( 'cb_group_contact_status', $this->_formValues ) ) {
                $this->_formValues['cb_group_contact_status'] = array( 'Added' => true );
            }
        } else if ( isset( $this->_ssID ) && empty( $_POST ) ) {
            // if we are editing / running a saved search and the form has not been posted
            $this->_formValues = CRM_Contact_BAO_SavedSearch::getFormValues( $this->_ssID );
        }

        $this->normalizeFormValues( );
        $this->postProcessCommon( );
    }

    /**
     * normalize the form values to make it look similar to the advanced form values
     * this prevents a ton of work downstream and allows us to use the same code for
     * multiple purposes (queries, save/edit etc)
     *
     * @return void
     * @access private
     */
    function normalizeFormValues( ) {
        $contactType = CRM_Utils_Array::value( 'contact_type', $this->_formValues );
        if ( $contactType ) {
            $this->_formValues['cb_contact_type'][$contactType] = 1;
        }
        unset( $this->_formValues['contact_type'] );

        $group = CRM_Utils_Array::value( 'group', $this->_formValues );
        if ( $group ) {
            $this->_formValues['cb_group'][$group] = 1;
        }
        unset( $this->_formValues['group'] );

        $tag = CRM_Utils_Array::value( 'tag', $this->_formValues );
        if ( $tag ) {
            $this->_formValues['cb_tag'][$tag] = 1;
        }
        unset( $this->_formValues['tag'] );

        return;
    }


    /**
     * Common post processing
     *
     * @return void
     * @access public
     */
    function postProcessCommon( ) {
        /*
         * sometime we do a postProcess early on, so we dont need to repeat it
         * this will most likely introduce some more bugs :(
         */
        if ( $this->_done ) {
            return;
        }
        $this->_done = true;

        // need to collapse the custom data groups 
        // if some values entered to search        
        if ( !empty($this->_formValues) ) {
            $customDataCriteria = array();
            foreach ($this->_formValues as $k => $v) {
                if ( substr( $k, 0, 10 ) != 'customData' ) {
                    continue;
                }
                if ( $v != '' ) {
                    list($str, $groupId, $fieldId, $elementName) = explode('_', $k, 4);

                    // Custom Group DAO
                    $cgDAO =& new CRM_Core_DAO();
                    $strQuery = "SELECT title FROM civicrm_custom_group WHERE id = $groupId";
                    $cgDAO->query($strQuery);
                    while($cgDAO->fetch()) {
                        $groupName = $cgDAO->title;
                    }
                    $customDataCriteria[] = $groupName;
                }
            }
            $this->set('customDataSearch',$customDataCriteria);
        }

        //get the button name
        $buttonName = $this->controller->getButtonName( );

        // we dont want to store the sortByCharacter in the formValue, it is more like 
        // a filter on the result set
        // this filter is reset if we click on the search button
        if ( $this->_sortByCharacter && $buttonName != $this->_searchButtonName) {
            if ( $this->_sortByCharacter == 1 ) {
                $this->_formValues['sortByCharacter'] = null;
            } else {
                $this->_formValues['sortByCharacter'] = $this->_sortByCharacter;
            }
        }
            
        // added the sorting  character to the form array
        // lets recompute the aToZ bar without the sortByCharacter
        // we need this in most cases except when just pager or sort values change, which
        // we'll ignore for now
        $aToZBar = CRM_Utils_PagerAToZ::getAToZBar( $this->_formValues, $this->_sortByCharacter );
        $this->set( 'AToZBar', $aToZBar );

        $this->set( 'type'      , $this->_action );
        $this->set( 'formValues', $this->_formValues );
        
        if ( $buttonName == $this->_actionButtonName || $buttonName == $this->_printButtonName ) {
            // check actionName and if next, then do not repeat a search, since we are going to the next page

            // hack, make sure we reset the task values
            $stateMachine =& $this->controller->getStateMachine( );
            $formName     =  $stateMachine->getTaskFormName( );
            $this->controller->resetPage( $formName );
            return;
        } else {
            // do export stuff
            if ( $buttonName == $this->_exportButtonName ) {
                $output = CRM_Core_Selector_Controller::EXPORT;
            } else {
                $output = CRM_Core_Selector_Controller::SESSION;
            }

            // create the selector, controller and run - store results in session
            $selector =& new CRM_Contact_Selector($this->_formValues, $this->_action);
            $sortID = null;
            if ( $this->get( CRM_Utils_Sort::SORT_ID  ) ) {
                $sortID = CRM_Utils_Sort::sortIDValue( $this->get( CRM_Utils_Sort::SORT_ID  ),
                                                       $this->get( CRM_Utils_Sort::SORT_DIRECTION ) );
            }
            $controller =& new CRM_Contact_Selector_Controller($selector ,
                                                               $this->get( CRM_Utils_Pager::PAGE_ID ),
                                                               $sortID,
                                                               CRM_Core_Action::VIEW, $this, $output );
            $controller->setEmbedded( true );
            $controller->run();
        }
    }


    /**
     * Add a form rule for this form. If Go is pressed then we must select some checkboxes
     * and an action
     */
    static function formRule( &$fields ) {
        // check actionName and if next, then do not repeat a search, since we are going to the next page
        
        if ( array_key_exists( '_qf_Search_next', $fields ) ) {
            if ( ! CRM_Utils_Array::value( 'task', $fields ) ) {
                return array( 'task' => 'Please select a valid action.' );
            }

            if(CRM_Utils_Array::value('task', $fields) == CRM_Contact_Task::SAVE_SEARCH) {
                // dont need to check for selection of contacts for saving search
                return true;
            }

            // if the all contact option is selected, ignore the contact checkbox validation
            if ($fields['radio_ts'] == 'ts_all') { 
                return true;
            }

            foreach ( $fields as $name => $dontCare ) {
                if ( substr( $name, 0, CRM_Core_Form::CB_PREFIX_LEN ) == CRM_Core_Form::CB_PREFIX ) {
                    return true;
                }
            }
            return array( 'task' => 'Please select one or more checkboxes to perform the action on.' );
        }
        return true;
    }

}

?>
