<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

/**
 * Files required
 */
require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/Session.php';
require_once 'CRM/Core/PseudoConstant.php';
require_once 'CRM/Core/Selector/Controller.php';
require_once 'CRM/Contact/Selector.php';

/**
 * Base Search / View form for *all* listing of multiple 
 * contacts
 */
class CRM_Contact_Form_Search extends CRM_Core_Form {

    const
        SESSION_SCOPE_SEARCH   = 'CRM Shared Search';

    static $_validContext = array(
                                  'search' => 'Search',
                                  'smog'   => 'Show members of group',
                                  'amtg'   => 'Add members to group',
                                  );

    /**
     * The context that we are working on
     *
     * @var string
     */
    protected $_context;

    /**
     * the groupId retrieved from the GET vars
     *
     * @var int
     */
    protected $_groupID;

    /**
     * the Group ID belonging to Add Member to group ID
     * retrieved from the GET vars
     *
     * @var int
     */
    protected $_amtgID;

    /**
     * the saved search IDretrieved from the GET vars
     *
     * @var int
     */
    protected $_ssID;

    /**
     * Are we forced to run a search
     *
     * @var int
     */
    protected $_force;

    /**
     * name of search button
     *
     * @var string
     */
    protected $_searchButtonName;

    /**
     * name of export button
     *
     * @var string
     */
    protected $_exportButtonName;

    
    /**
     * name of export button
     *
     * @var string
     */
    protected $_actionButtonName;

    /**
     * the group elements
     *
     * @var array
     */
    protected $_group;
    protected $_groupElement;

    /**
     * the category elements
     *
     * @var array
     */
    protected $_category;
    protected $_categoryElement;

    /**
     * form values that we will be using
     *
     * @var array
     */
    protected $_formValues;
    
    /*
     * csv - common search values
     * @static
     * @access protected
     */
    static $csv = array('contact_type', 'group', 'category');

    /**
     * Build the common elements between the search/advanced form
     *
     * @access public
     * @return void
     */
    function buildQuickFormCommon( ) {

        // some tasks.. what do we want to do with the selected contacts ?
        $tasks = array( '' => '- more actions -' ) + CRM_Contact_Task::$tasks;
        if ( isset( $this->_ssID ) ) {
            $tasks = $tasks + CRM_Contact_Task::$optionalTasks;

            $savedSearchValues = array( 'id' => $this->_ssID, 'name' => CRM_Contact_BAO_SavedSearch::getName( $this->_ssID ) );
            $this->assign_by_ref( 'savedSearch', $savedSearchValues );
        }

        $actionElement = $this->add('select', 'task'   , 'Actions: '    , $tasks    );

        if ( $this->_context === 'smog' ) {
            $this->_groupElement->freeze( );
            
            // also set the group title
            $groupValues = array( 'id' => $this->_groupID, 'title' => $this->_group[$this->_groupID] );
            $this->assign_by_ref( 'group', $groupValues );

            // Set dynamic page title for 'Show Members of Group'
            CRM_Utils_System::setTitle( 'Group Members: ' . $this->_group[$this->_groupID] );
        }
        
        if ( $this->_context === 'amtg' ) {
            // Set dynamic page title for 'Add Members Group'
            CRM_Utils_System::setTitle( 'Add Members: ' . $this->_group[$this->_amtgID] );
            // also set the group title and freeze the action task with Add Members to Group
            $groupValues = array( 'id' => $this->_amtgID, 'title' => $this->_group[$this->_amtgID] );
            $this->assign_by_ref( 'group', $groupValues );
            $actionElement->freeze( );
        }

        // need to perform tasks on all or selected items ? using radio_ts(task selection) for it
        $this->addElement('radio', 'radio_ts', null, 'selected records only', 'ts_sel', array( 'checked' => null) );
        $this->addElement('radio', 'radio_ts', null, 'all', 'ts_all', array( 'onchange' => "changeCheckboxVals('mark_x_','deselect', Search ); return false;" ) );

        /*
         * add form checkboxes for each row. This is needed out here to conform to QF protocol
         * of all elements being declared in builQuickForm
         */
        $rows = $this->get( 'rows' );
        if ( is_array( $rows ) ) {
            foreach ( $rows as &$row ) {
                //$this->addElement( 'checkbox', $row['checkbox'] );
                $this->addElement( 'checkbox', $row['checkbox'], null, null, array( 'onclick' => "return checkSelectedBox('".$row[checkbox]."', '".$this->getName()."');" ) );
            }
        }

        // add buttons
        $this->addButtons( array(
                                 array ( 'type'      => 'refresh',
                                         'name'      => 'Search' ,
                                         'isDefault' => true     )
                                 )        
                           );

        $this->add('submit', $this->_searchButtonName, 'Search', array( 'class' => 'form-submit' ) );
        $this->add('submit', $this->_exportButtonName, 'Export',
                   array( 'class' => 'form-submit',
                          'onclick' => "return checkPerformAction('mark_x', '".$this->getName()."', 1);" ) );
        $this->add('submit', $this->_printButtonName, 'Print',
                   array( 'class' => 'form-submit',
                          'onclick' => "return checkPerformAction('mark_x', '".$this->getName()."', 1);" ) );
        $this->setDefaultAction( 'refresh' );

        /*
         * add the go button for the action form, note it is of type 'next' rather than of type 'submit'
         *
         */
        if ( $this->_context == 'amtg' ){
            $this->add('submit', $this->_actionButtonName, 'Add Contacts to ' . $this->_group[$this->_amtgID],
                   array( 'class' => 'form-submit',
                          'onclick' => "return checkPerformAction('mark_x', '".$this->getName()."', 1);" ) );
        } else {
            $this->add('submit', $this->_actionButtonName, 'Go',
                   array( 'class' => 'form-submit',
                          'onclick' => "return checkPerformAction('mark_x', '".$this->getName()."', 0);" ) );
        }    
    }
    
    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) 
    {
        $this->add('select', 'contact_type', 'Find... ', CRM_Core_SelectValues::$contactType);

        // add select for groups
        $group               = array('' => ts(' - any group - ')) + $this->_group;
        $this->_groupElement = $this->add('select', 'group', 'in', $group);

        // add select for categories
        $category = array('' => ' - any tag - ') + $this->_category;
        $this->_categoryElement = $this->add('select', 'category', 'Tagged', $category);

        // text for sort_name
        $this->add('text', 'sort_name', 'Name:', CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'sort_name') );

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

        $defaults['sort_name'] = $this->_formValues['sort_name'];
        foreach (self::$csv as $v) {
            $defaults[$v] = $this->_formValues['cb_' . $v] ? key($this->_formValues['cb_' . $v]) : '';
        }

        if ( $this->_context === 'amtg' ) {
            $defaults['task'] = CRM_Contact_Task::GROUP_CONTACTS;
        } else {
            $defaults['task'] = CRM_Contact_Task::PRINT_CONTACTS;
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
        $this->_group    =& CRM_Core_PseudoConstant::group   ( );
        $this->_category =& CRM_Core_PseudoConstant::category( );
        /**
         * set the button names
         */
        $this->_searchButtonName = $this->getButtonName( 'refresh', 'search' );
        $this->_exportButtonName = $this->getButtonName( 'refresh', 'export' );
        $this->_printButtonName = $this->getButtonName( 'next'    , 'print' );
        $this->_actionButtonName = $this->getButtonName( 'next'   , 'action' );

        /*
         * we allow the controller to set force/reset externally, useful when we are being
         * driven by the wizard framework
         */
        $this->_reset   = CRM_Utils_Request::retrieve( 'reset' );

        $this->_force   = CRM_Utils_Request::retrieve( 'force', $this, false );
        // we only force stuff once :)
        $this->set( 'force', false );

        $this->_groupID = CRM_Utils_Request::retrieve( 'gid'   , $this );
        $this->_amtgID  = CRM_Utils_Request::retrieve( 'amtgID', $this );
        $this->_ssID    = CRM_Utils_Request::retrieve( 'ssID'  , $this );
        if ( isset( $this->_ssID ) ) {
            $this->_formValues = CRM_Contact_BAO_SavedSearch::getFormValues( $this->_ssID );
        } else {
            // get the session variables for search scope
            $session = CRM_Core_Session::singleton( );
            $session->getVars($searchScope, CRM_Contact_Form_Search::SESSION_SCOPE_SEARCH);

            // sort_name remains same across basic/advanced search
            $this->_formValues =& $searchScope['formValues'];
        }

        /*
         * assign context to drive the template display, make sure context is valid
         */
        $this->_context = CRM_Utils_Request::retrieve( 'context', $this, false, 'search' );
        if ( ! CRM_Utils_Array::value( $this->_context, self::$_validContext ) ) {
            $this->_context = 'search';
            $this->set( 'context', $this->_context );
        }
        $this->assign( 'context', $this->_context );
        
        $fv = $this->controller->exportValues($this->_name);
        $selector = new CRM_Contact_Selector($fv, $this->_mode);
        $controller = new CRM_Core_Selector_Controller($selector , null, null, CRM_Core_Action::VIEW, $this, CRM_Core_Selector_Controller::TRANSFER );
        $controller->setEmbedded( true );
        if ( $controller->hasChanged( $this->_reset ) ||
             $this->_force ) {
            $this->postProcess( );
            /*
             * Note that we repeat this, since the search creates and stores
             * values that potentially change the controller behavior. i.e. things
             * like totalCount etc
             */
            $controller = new CRM_Core_Selector_Controller($selector , null, null, CRM_Core_Action::VIEW, $this, CRM_Core_Selector_Controller::TRANSFER );
            $controller->setEmbedded( true );
        }
        $controller->moveFromSessionToTemplate( );
    }

    /**
     * this method is called for processing a submitted search form
     *
     * @param none
     * @return void
     * @access public
     */
    function postProcess( ) {
        // get user submitted values
        $this->_formValues = $this->controller->exportValues($this->_name);

        /* after every search form is submitted we save the following in the session
         *     - type of search 'type'
         *     - submitted form values 'formValues'
         *     - QILL 'qill'
         */
        
        // hack: if this is a forced search, stuff values into FV
        if ( $this->_force ) {
            $this->_formValues['group'] = $this->_groupID;
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

        $category = CRM_Utils_Array::value( 'category', $this->_formValues );
        if ( $category ) {
            $this->_formValues['cb_category'][$category] = 1;
        }
        unset( $this->_formValues['category'] );
        return;
    }

    function postProcessCommon( ) {
        $session = CRM_Core_Session::singleton();

        $session->set('type', $this->_mode, self::SESSION_SCOPE_SEARCH);
        $session->set('formValues', $this->_formValues, self::SESSION_SCOPE_SEARCH);

        $buttonName = $this->controller->getButtonName( );
        if ( $buttonName == $this->_actionButtonName || $buttonName == $this->_printButtonName ) {
            // check actionName and if next, then do not repeat a search, since we are going to the next page
            return;
        } else {
            // do export stuff
            if ( $buttonName == $this->_exportButtonName ) {
                $output = CRM_Core_Selector_Controller::EXPORT;
            } else {
                $output = CRM_Core_Selector_Controller::SESSION;
            }

            // create the selector, controller and run - store results in session
            $selector = new CRM_Contact_Selector($this->_formValues, $this->_mode);
            $controller = new CRM_Core_Selector_Controller($selector , null, null, CRM_Core_Action::VIEW, $this, $output );
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
                if ( substr( $name, 0, self::CB_PREFIX_LEN ) == self::CB_PREFIX ) {
                    return true;
                }
            }
            return array( 'task' => 'Please select one or more checkboxes to perform the action on.' );
        }
        return true;
    }

}

?>
