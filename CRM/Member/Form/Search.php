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
 | at http://www.openngo.org/faqs/licensing.html                      |
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
require_once 'CRM/Member/Selector/Search.php';
require_once 'CRM/Core/Selector/Controller.php';


/**
 * This file is for civimember search
 */
class CRM_Member_Form_Search extends CRM_Core_Form {
    
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
     * form values that we will be using 
     * 
     * @var array 
     * @access protected 
     */ 
    protected $_formValues; 

    /**
     * the params that are sent to the query
     * 
     * @var array 
     * @access protected 
     */ 
    protected $_queryParams;

    /** 
     * have we already done this search 
     * 
     * @access protected 
     * @var boolean 
     */ 
    protected $_done; 

    /**
     * are we restricting ourselves to a single contact
     *
     * @access protected  
     * @var boolean  
     */  
    protected $_single = false;

    /** 
     * are we restricting ourselves to a single contact 
     * 
     * @access protected   
     * @var boolean   
     */   
    protected $_limit = null;

    /** 
     * what context are we being invoked from 
     *    
     * @access protected      
     * @var string 
     */      
    protected $_context = null; 

    protected $_defaults;

    /** 
     * processing needed for buildForm and later 
     * 
     * @return void 
     * @access public 
     */ 
    function preProcess( ) 
    { 
        /** 
         * set the button names 
         */ 
        $this->_searchButtonName = $this->getButtonName( 'refresh' ); 
        $this->_printButtonName  = $this->getButtonName( 'next'   , 'print' ); 
        $this->_actionButtonName = $this->getButtonName( 'next'   , 'action' ); 

        $this->_done = false;

        $this->defaults = array( );

        /* 
         * we allow the controller to set force/reset externally, useful when we are being 
         * driven by the wizard framework 
         */ 
        $this->_reset   = CRM_Utils_Request::retrieve( 'reset', 'Boolean',
                                                       CRM_Core_DAO::$_nullObject ); 
        $this->_force   = CRM_Utils_Request::retrieve( 'force', 'Boolean',
                                                       $this, false ); 
        $this->_limit   = CRM_Utils_Request::retrieve( 'limit', 'Positive',
                                                       $this );
        $this->_context = CRM_Utils_Request::retrieve( 'context', 'String',
                                                       $this );

        $this->assign( 'limit', $this->_limit );

        // get user submitted values  
        // get it from controller only if form has been submitted, else preProcess has set this  
        if ( ! empty( $_POST ) ) { 
            $this->_formValues = $this->controller->exportValues( $this->_name );  
        } else {
            $this->_formValues = $this->get( 'formValues' ); 
        } 

        if ( $this->_force ) { 
            $this->postProcess( );
            $this->set( 'force', 0 );
        }

        $sortID = null; 
        if ( $this->get( CRM_Utils_Sort::SORT_ID  ) ) { 
            $sortID = CRM_Utils_Sort::sortIDValue( $this->get( CRM_Utils_Sort::SORT_ID  ), 
                                                   $this->get( CRM_Utils_Sort::SORT_DIRECTION ) ); 
        } 

        require_once 'CRM/Contact/Form/Search.php';
        $this->_queryParams =& CRM_Contact_Form_Search::convertFormValues( $this->_formValues ); 
        $selector =& new CRM_Member_Selector_Search( $this->_queryParams,
                                                         $this->_action,
                                                         null,
                                                         $this->_single,
                                                         $this->_limit,
                                                         $this->_context ); 
        $controller =& new CRM_Core_Selector_Controller($selector ,  
                                                        $this->get( CRM_Utils_Pager::PAGE_ID ),  
                                                        $sortID,  
                                                        CRM_Core_Action::VIEW, 
                                                        $this, 
                                                        CRM_Core_Selector_Controller::TRANSFER );

        $controller->setEmbedded( true ); 
        $controller->moveFromSessionToTemplate(); 

        $this->assign( 'summary', $this->get( 'summary' ) );
    }

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) 
    {
        $this->addElement('text', 'sort_name', ts('Member'), CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'sort_name') );

        require_once 'CRM/Member/BAO/Query.php';
        CRM_Member_BAO_Query::buildSearchForm( $this );

        /* 
         * add form checkboxes for each row. This is needed out here to conform to QF protocol 
         * of all elements being declared in builQuickForm 
         */ 
        $rows = $this->get( 'rows' ); 
        if ( is_array( $rows ) ) {
            $this->addElement( 'checkbox', 'toggleSelect', null, null, array( 'onChange' => "return toggleCheckboxVals('mark_x_',this.form);" ) ); 
            
            $total = $cancel = 0;
            foreach ($rows as $row) { 
                $this->addElement( 'checkbox', $row['checkbox'], 
                                   null, null, 
                                   array( 'onclick' => "return checkSelectedBox('" . $row['checkbox'] . "', '" . $this->getName() . "');" )
                                   ); 
            }

            $this->assign( 'single', $this->_single );

            // also add the action and radio boxes
            require_once 'CRM/Member/Task.php';
            $tasks = array( '' => ts('- more actions -') ) + CRM_Member_Task::tasks( );
            $this->add('select', 'task'   , ts('Actions:') . ' '    , $tasks    ); 
            $this->add('submit', $this->_actionButtonName, ts('Go'), 
                       array( 'class' => 'form-submit', 
                              'onclick' => "return checkPerformAction('mark_x', '".$this->getName()."', 0);" ) ); 

            $this->add('submit', $this->_printButtonName, ts('Print'), 
                       array( 'class' => 'form-submit', 
                              'onclick' => "return checkPerformAction('mark_x', '".$this->getName()."', 1);" ) ); 

            
            // need to perform tasks on all or selected items ? using radio_ts(task selection) for it 
            $this->addElement('radio', 'radio_ts', null, '', 'ts_sel', array( 'checked' => null) ); 
            $this->addElement('radio', 'radio_ts', null, '', 'ts_all', array( 'onchange' => $this->getName().".toggleSelect.checked = false; toggleCheckboxVals('mark_x_',".$this->getName()."); return false;" ) );
        }
        

        // add buttons 
        $this->addButtons( array( 
                                 array ( 'type'      => 'refresh', 
                                         'name'      => ts('Search') , 
                                         'isDefault' => true     ) 
                                 )    );     
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
        if ( $this->_done ) {
            return;
        }

        $this->_done = true;

        $this->_formValues = $this->controller->exportValues($this->_name);

        $this->fixFormValues( );
        
        require_once 'CRM/Contact/Form/Search.php';
        $this->_queryParams =& CRM_Contact_Form_Search::convertFormValues( $this->_formValues ); 

        $this->set( 'formValues' , $this->_formValues  );
        $this->set( 'queryParams', $this->_queryParams );

        $buttonName = $this->controller->getButtonName( );
        if ( $buttonName == $this->_actionButtonName || $buttonName == $this->_printButtonName ) { 
            // check actionName and if next, then do not repeat a search, since we are going to the next page 
 
            // hack, make sure we reset the task values 
            $stateMachine =& $this->controller->getStateMachine( ); 
            $formName     =  $stateMachine->getTaskFormName( ); 
            $this->controller->resetPage( $formName ); 
            return; 
        }

        $sortID = null; 
        if ( $this->get( CRM_Utils_Sort::SORT_ID  ) ) { 
            $sortID = CRM_Utils_Sort::sortIDValue( $this->get( CRM_Utils_Sort::SORT_ID  ), 
                                                   $this->get( CRM_Utils_Sort::SORT_DIRECTION ) ); 
        } 

        $this->_queryParams =& CRM_Contact_Form_Search::convertFormValues( $this->_formValues );
        
        require_once "CRM/Member/Selector/Search.php";
        $selector =& new CRM_Member_Selector_Search( $this->_queryParams,
                                                         $this->_action,
                                                         null,
                                                         $this->_single,
                                                         $this->_limit,
                                                         $this->_context ); 
        $controller =& new CRM_Core_Selector_Controller($selector , 
                                                        $this->get( CRM_Utils_Pager::PAGE_ID ), 
                                                        $sortID, 
                                                        CRM_Core_Action::VIEW,
                                                        $this,
                                                        CRM_Core_Selector_Controller::SESSION );
        $controller->setEmbedded( true ); 

        $query   =& $selector->getQuery( );
        //$summary =& $query->summaryContribution( );
        //$this->set( 'summary', $summary );
        $controller->run(); 
    }


    function fixFormValues( ) {
        // if this search has been forced
        // then see if there are any get values, and if so over-ride the post values
        // note that this means that GET over-rides POST :)

        // we fix date_to here if set to be the end of the day, i.e. 23:59:59
//         if ( ! CRM_Utils_System::isNull( $this->_formValues['contribution_date_to'] ) ) {
//             $this->_formValues['contribution_date_to']['H'] = 23;
//             $this->_formValues['contribution_date_to']['i'] = 59;
//             $this->_formValues['contribution_date_to']['s'] = 59;
//         }

        if ( ! $this->_force ) {
            return;
        }

//         $status = CRM_Utils_Request::retrieve( 'status', 'String',
//                                                CRM_Core_DAO::$_nullObject );
//         if ( $status ) {
//             switch ( $status ) {
//             case 'Valid':
//             case 'Cancelled':
//             case 'All':
//                 $this->_formValues['contribution_status'] = $status;
//                 $this->_defaults['contribution_status'] = $status;
//                 break;
//             }
//         }

        $cid = CRM_Utils_Request::retrieve( 'cid', 'Positive',
                                            CRM_Core_DAO::$_nullObject );

        if ( $cid ) {
            $cid = CRM_Utils_Type::escape( $cid, 'Integer' );
            if ( $cid > 0 ) {
                $this->_formValues['contact_id'] = $cid;
                list( $display, $image ) = CRM_Contact_BAO_Contact::getDisplayAndImage( $cid );
                $this->_defaults['sort_name'] = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $cid,
                                                                             'sort_name' );
                // also assign individual mode to the template
                $this->_single = true;
            }
        }

//         $fromDate = CRM_Utils_Request::retrieve( 'start', 'Date',
//                                                  CRM_Core_DAO::$_nullObject );
//         if ( $fromDate ) {
//             $fromDate = CRM_Utils_Type::escape( $fromDate, 'Timestamp' );
//             $date = CRM_Utils_Date::unformat( $fromDate, '' );
//             $this->_formValues['contribution_date_from'] = $date;
//             $this->_defaults['contribution_date_from'] = $date;
//         }

//         $toDate= CRM_Utils_Request::retrieve( 'end', 'Date',
//                                               CRM_Core_DAO::$_nullObject );
//         if ( $toDate ) { 
//             $toDate = CRM_Utils_Type::escape( $toDate, 'Timestamp' ); 
//             $date = CRM_Utils_Date::unformat( $toDate, '' );
//             $this->_formValues['contribution_date_to'] = $date;
//             $this->_defaults['contribution_date_to'] = $date;
//             $this->_formValues['contribution_date_to']['H'] = 23;
//             $this->_formValues['contribution_date_to']['i'] = 59;
//             $this->_formValues['contribution_date_to']['s'] = 59;
//         }

        $this->_limit = CRM_Utils_Request::retrieve( 'limit', 'Positive',
                                                     $this );
    }

   
}

?>
