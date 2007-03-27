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
 * @copyright CiviCRM LLC (c) 2004-2007
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
require_once 'CRM/Contact/BAO/SavedSearch.php';

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
     * @access public
     */
    public $_groupID;

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
     * name of print button
     *
     * @var string
     * @access protected
     */
    protected $_printButtonName;
    
    /**
     * name of action button
     *
     * @var string
     * @access protected
     */
    protected $_actionButtonName;

    /**
     * the group elements
     *
     * @var array
     * @access public
     */
    public $_group;
    public $_groupElement;

    /**
     * the tag elements
     *
     * @var array 
     * @access protected
     */
    public $_tag;
    public $_tagElement;

    /**
     * form values that we will be using
     *
     * @var array
     * @access protected
     */
    protected $_formValues;

    /**
     * The params used for search
     *
     * @var array
     * @access protected
     */
    protected $_params;

    /**
     * The return properties used for search
     *
     * @var array
     * @access protected
     */
    protected $_returnProperties;

    /**
     * The sort by character
     * 
     * @var string
     * @access protected
     */
    protected $_sortByCharacter;

    /**
     * The profile group id used for display
     *
     * @var integer
     * @access protected
     */
    protected $_ufGroupID;

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
        $permission = CRM_Core_Permission::getPermission( );

        // some tasks.. what do we want to do with the selected contacts ?
        $tasks = array( '' => ts('- more actions -') ) + CRM_Contact_Task::permissionedTaskTitles( $permission );
        if ( isset( $this->_ssID ) ) {
            if ( $permission == CRM_Core_Permission::EDIT ) {
                $tasks = $tasks + CRM_Contact_Task::optionalTaskTitle();
            }

            $savedSearchValues = array( 'id' => $this->_ssID,
                                        'name' => CRM_Contact_BAO_SavedSearch::getName( $this->_ssID, 'title' ) );
            $this->assign_by_ref( 'savedSearch', $savedSearchValues );
            $this->assign( 'ssID', $this->_ssID );
        }

        if ( $this->_context === 'smog' ) {
            // need to figure out how to freeze a bunch of checkboxes, hack for now
            if ( $this->_action != CRM_Core_Action::ADVANCED ) {
                $this->_groupElement->freeze( );
            }
            
            // also set the group title
            $groupValues = array( 'id' => $this->_groupID, 'title' => $this->_group[$this->_groupID] );
            $this->assign_by_ref( 'group', $groupValues );

            // also set ssID if this is a saved search
            $ssID = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Group', $this->_groupID, 'saved_search_id' );
            $this->assign( 'ssID', $ssID );
            
            //get the saved search mapping id
            if ( $ssID ) {
                $ssMappingId = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_SavedSearch', $ssID, 'mapping_id' );
            }
            if ( $ssMappingId  ) {
                $this->assign( 'ssMappingID', $ssMappingId );
            }
            
            $group_contact_status = array();
            foreach(CRM_Core_SelectValues::groupContactStatus() as $k => $v) {
                if (! empty($k)) {
                    $group_contact_status[] =
                        HTML_QuickForm::createElement('checkbox', $k, null, $v);
                }
            }
            $this->addGroup($group_contact_status,
                            'group_contact_status', ts('Group Status'));
            $this->addGroupRule('group_contact_status', ts('Please select at least one membership status.'), 'required', null, 1);
            // Set dynamic page title for 'Show Members of Group'
            CRM_Utils_System::setTitle( ts('Group Members: %1', array(1 => $this->_group[$this->_groupID])) );

            // check if user has permission to edit members of this group
            require_once 'CRM/Contact/BAO/Group.php';
            if ( CRM_Contact_BAO_Group::checkPermission( $this->_groupID, $this->_group[$this->_groupID] ) ==
                 CRM_Core_Permission::EDIT ) {
                $this->assign( 'permissionedForGroup', true );
            } else {
                $this->assign( 'permissionedForGroup', false );
            }
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
            $this->add('select', 'task'   , ts('Actions:') . ' '    , $tasks    );
            $this->add('submit', $this->_actionButtonName, ts('Go'),
                       array( 'class' => 'form-submit',
                          'onclick' => "return checkPerformAction('mark_x', '".$this->getName()."', 0);" ) );
        }
        
        // need to perform tasks on all or selected items ? using radio_ts(task selection) for it
        $this->addElement('radio', 'radio_ts', null, '', 'ts_sel', array( 'checked' => 'checked' ) );
        
        $this->addElement('radio', 'radio_ts', null, '', 'ts_all', array( 'onclick' => $this->getName().".toggleSelect.checked = false; toggleCheckboxVals('mark_x_',".$this->getName().");" ) );

        /*
         * add form checkboxes for each row. This is needed out here to conform to QF protocol
         * of all elements being declared in builQuickForm
         */
        $rows = $this->get( 'rows' );
        if ( is_array( $rows ) ) {
            $this->addElement( 'checkbox', 'toggleSelect', null, null, array( 'onclick' => "return toggleCheckboxVals('mark_x_',this.form);" ) );
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
        $group               = array('' => ts('- any group -')) + $this->_group;
        $this->_groupElement =& $this->addElement('select', 'group', ts('in'), $group);

        // add select for categories
        $tag = array('' => ts('- any tag -')) + $this->_tag;
        $this->_tagElement =& $this->addElement('select', 'tag', ts('Tagged'), $tag);

        // text for sort_name
        $this->add('text', 'sort_name', ts('Name'));

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
            if ( CRM_Utils_Array::value( $v, $this->_formValues ) && is_array( $this->_formValues[$v] ) ) {
                $tmpArray = array_keys( $this->_formValues[$v] );
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
            $defaults['group_contact_status[Added]'] = true;
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
        $this->_reset   = CRM_Utils_Request::retrieve( 'reset', 'Boolean',
                                                       CRM_Core_DAO::$_nullObject );

        $this->_force   = CRM_Utils_Request::retrieve( 'force', 'Boolean',
                                                       CRM_Core_DAO::$_nullObject );

        $this->_groupID         = CRM_Utils_Request::retrieve( 'gid'            , 'Positive',
                                                               $this );
        $this->_amtgID          = CRM_Utils_Request::retrieve( 'amtgID'         , 'Positive',
                                                               $this );
        $this->_ssID            = CRM_Utils_Request::retrieve( 'ssID'           , 'Positive',
                                                               $this );
        $this->_sortByCharacter = CRM_Utils_Request::retrieve( 'sortByCharacter', 'String'  ,
                                                               $this );
        $this->_ufGroupID       = CRM_Utils_Request::retrieve( 'id'             , 'Positive',
                                                               $this );

        // get user submitted values 
        // get it from controller only if form has been submitted, else preProcess has set this 
        if ( ! empty( $_POST ) ) {
            $this->_formValues = $this->controller->exportValues($this->_name); 
            $this->normalizeFormValues( );
            $this->_params =& CRM_Contact_BAO_Query::convertFormValues( $this->_formValues );
            $this->_returnProperties =& $this->returnProperties( );

            // also reset the sort by character  
            $this->_sortByCharacter = null;  
            $this->set( 'sortByCharacter', null );

            // also get the uf group id directly from the post value
            $this->_ufGroupID = CRM_Utils_Array::value( 'uf_group_id', $_POST, $this->_ufGroupID );
            $this->_formValues['uf_group_id'] = $this->_ufGroupID;
            $this->set( 'id', $this->_ufGroupID );
        } else {
            $this->_formValues = $this->get( 'formValues' );
            $this->_params =& CRM_Contact_BAO_Query::convertFormValues( $this->_formValues );
            $this->_returnProperties =& $this->returnProperties( );
        }

        if ( empty( $this->_formValues ) ) {
            
            //check if group is a smart group (fix for CRM-1255)
            if ($this->_groupID) {
                if ($ssId = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Group', $this->_groupID, 'saved_search_id' ) ) {
                    $this->_ssID = $ssId;
                }
            }

            if ( isset( $this->_ssID ) ) {
                // we only retrieve the saved search values if out current values are null
                $this->_formValues = CRM_Contact_BAO_SavedSearch::getFormValues( $this->_ssID );
                
                //fix for CRM-1505
                if (CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_SavedSearch', $this->_ssID, 'mapping_id' ) ) {
                    $this->_params =& CRM_Contact_BAO_SavedSearch::getSearchParams( $this->_ssID );
                } else {
                    $this->_params =& CRM_Contact_BAO_Query::convertFormValues( $this->_formValues );
                }

                $this->_returnProperties =& $this->returnProperties( );
                
            } else if ( isset( $this->_ufGroupID ) ) {
                // also set the uf group id if not already present
                $this->_formValues['uf_group_id'] = $this->_ufGroupID;
            }
        }
        $this->assign( 'id', CRM_Utils_Array::value( 'uf_group_id', $this->_formValues ) );

        /*
         * assign context to drive the template display, make sure context is valid
         */
        $this->_context = CRM_Utils_Request::retrieve( 'context', 'String',
                                                       $this, false, 'search' );
        if ( ! CRM_Utils_Array::value( $this->_context, self::validContext() ) ) {
            $this->_context = 'search';
            $this->set( 'context', $this->_context );
        }
        $this->assign( 'context', $this->_context );

        //CRM_Core_Error::debug( 'f', $this->_formValues );
        //CRM_Core_Error::debug( 'p', $this->_params );
        $selector =& new CRM_Contact_Selector( $this->_formValues, $this->_params,
                                               $this->_returnProperties,
                                               $this->_action );
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

    function &getFormValues( ) {
        return $this->_formValues;
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
        $session ->set('isSearchBuilder','0');

        // get user submitted values
        // get it from controller only if form has been submitted, else preProcess has set this
        if ( ! empty( $_POST ) ) {
            $this->_formValues = $this->controller->exportValues($this->_name);
            $this->normalizeFormValues( );

            // also reset the sort by character
            $this->_sortByCharacter = null;
            $this->set( 'sortByCharacter', null );
        }

        if ( isset( $this->_groupID ) && ! CRM_Utils_Array::value( 'group', $this->_formValues ) ) {
            $this->_formValues['group'][$this->_groupID] = 1;
        } else if ( isset( $this->_ssID ) && empty( $_POST ) ) {
            // if we are editing / running a saved search and the form has not been posted
            $this->_formValues = CRM_Contact_BAO_SavedSearch::getFormValues( $this->_ssID );

            //fix for CRM-1505
            if (CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_SavedSearch', $this->_ssID, 'mapping_id' ) ) {
                $this->_params =& CRM_Contact_BAO_SavedSearch::getSearchParams( $this->_ssID );
            }
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
        
        $this->_params =& CRM_Contact_BAO_Query::convertFormValues( $this->_formValues );
        $this->_returnProperties =& $this->returnProperties( );
        
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
        if ( $contactType && ! is_array( $contactType ) ) {
            unset( $this->_formValues['contact_type'] );
            $this->_formValues['contact_type'][$contactType] = 1;
        }

        $group = CRM_Utils_Array::value( 'group', $this->_formValues );
        if ( $group && ! is_array( $group ) ) {
            unset( $this->_formValues['group'] );
            $this->_formValues['group'][$group] = 1;
        }

        $tag = CRM_Utils_Array::value( 'tag', $this->_formValues );
        if ( $tag && ! is_array( $tag ) ) {
            unset( $this->_formValues['tag'] );
            $this->_formValues['tag'][$tag] = 1;
        }

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
        
        //get the button name
        $buttonName = $this->controller->getButtonName( );

        if ( isset( $this->_ufGroupID ) && ! CRM_Utils_Array::value( 'uf_group_id', $this->_formValues ) ) { 
            $this->_formValues['uf_group_id'] = $this->_ufGroupID;
        }
        
        $this->set( 'type'            , $this->_action );
        $this->set( 'formValues'      , $this->_formValues );
        $this->set( 'queryParams'     , $this->_params );
        $this->set( 'returnProperties', $this->_returnProperties );
        
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
                return CRM_Utils_System::redirect( CRM_Utils_System::url('civicrm/export/contact') );
            } else {
                $output = CRM_Core_Selector_Controller::SESSION;
            }

            // create the selector, controller and run - store results in session
            $selector =& new CRM_Contact_Selector($this->_formValues, $this->_params,
                                                  $this->_returnProperties, $this->_action);

            // added the sorting  character to the form array
            // lets recompute the aToZ bar without the sortByCharacter
            // we need this in most cases except when just pager or sort values change, which
            // we'll ignore for now
            $config =& CRM_Core_Config::singleton( );
            if ( $config->includeAlphabeticalPager ) {
                $query =& $selector->getQuery( );
                if ($this->_reset || !$this->_sortByCharacter) {
                    $aToZBar = CRM_Utils_PagerAToZ::getAToZBar( $query, $this->_sortByCharacter );
                    $this->set( 'AToZBar', $aToZBar );
                }
            }

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

    function getTitle( ) {
        return ts( 'Find Contacts' );
    }

    public function &returnProperties( ) {
        return CRM_Core_DAO::$_nullObject;
    }

}

?>
