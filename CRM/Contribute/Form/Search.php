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

require_once 'CRM/Contribute/PseudoConstant.php';
require_once 'CRM/Contribute/Selector/Search.php';
require_once 'CRM/Core/Selector/Controller.php';

/**
 * advanced search, extends basic search
 */
class CRM_Contribute_Form_Search extends CRM_Core_Form {

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
     * have we already done this search 
     * 
     * @access protected 
     * @var boolean 
     */ 
    protected $_done; 
 
    /** 
     * processing needed for buildForm and later 
     * 
     * @return void 
     * @access public 
     */ 
    function preProcess( ) { 
        /** 
         * set the button names 
         */ 
        $this->_searchButtonName = $this->getButtonName( 'refresh' ); 
        $this->_printButtonName  = $this->getButtonName( 'next'   , 'print' ); 
        $this->_actionButtonName = $this->getButtonName( 'next'   , 'action' ); 

        $this->_done = false;

        /* 
         * we allow the controller to set force/reset externally, useful when we are being 
         * driven by the wizard framework 
         */ 
        $nullObject = null; 
        $this->_reset   = CRM_Utils_Request::retrieve( 'reset', $nullObject ); 
 
        $this->_force   = CRM_Utils_Request::retrieve( 'force', $this, false ); 
 
        // we only force stuff once :) 
        $this->set( 'force', false ); 

        // get user submitted values  
        // get it from controller only if form has been submitted, else preProcess has set this  
        if ( ! empty( $_POST ) ) { 
            $this->_formValues = $this->controller->exportValues($this->_name);  
        } else {
            $this->_formValues = $this->get( 'formValues' ); 
        } 
 
        $selector =& new CRM_Contribute_Selector_Search( $this->_formValues, $this->_action ); 
        $controller =& new CRM_Core_Selector_Controller($selector ,  
                                                        $this->get( CRM_Utils_Pager::PAGE_ID ),  
                                                        $sortID,  
                                                        CRM_Core_Action::VIEW, 
                                                        $this, 
                                                        CRM_Core_Selector_Controller::SESSION );
        $controller->setEmbedded( true ); 
        if ( $this->_force ) { 
            $this->postProcess( );
        }
        $controller->moveFromSessionToTemplate(); 
    }

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) 
    {
        // text for sort_name 
        $this->add('text', 'sort_name', ts('Name or email')); 

        // Date selects for date 
        $this->add('date', 'contribution_from_date', ts('Contribution Dates - From'), CRM_Core_SelectValues::date('relative')); 
        $this->addRule('contribution_from_date', ts('Select a valid date.'), 'qfDate'); 
 
        $this->add('date', 'contribution_to_date', ts('To'), CRM_Core_SelectValues::date('relative')); 
        $this->addRule('contribution_to_date', ts('Select a valid date.'), 'qfDate'); 

        $this->add('text', 'contribution_min_amount', ts('Minimum Amount'), array( 'size' => 8, 'maxlength' => 8 ) ); 
        $this->addRule( 'contribution_min_amount', ts( 'Please enter a valid money value (e.g. 9.99).' ), 'money' );

        $this->add('text', 'contribution_max_amount', ts('Maximum Amount'), array( 'size' => 8, 'maxlength' => 8 ) ); 
        $this->addRule( 'contribution_max_amount', ts( 'Please enter a valid money value (e.g. 99.99).' ), 'money' );


        $this->add('select', 'contribution_type_id', 
                   ts( 'Contribution Type' ),
                   array( '' => ts( '- select -' ) ) +
                   CRM_Contribute_PseudoConstant::contributionType( ) );

        $this->add('select', 'payment_instrument_id', 
                   ts( 'Payment Instrument' ), 
                   array( '' => ts( '- select -' ) ) +
                   CRM_Contribute_PseudoConstant::paymentInstrument( ) );

        $status = array( );
        $status[] = $this->createElement( 'radio', null, null, ts( 'Valid' )    , 'Valid'     );
        $status[] = $this->createElement( 'radio', null, null, ts( 'Cancelled' ), 'Cancelled' );
        $status[] = $this->createElement( 'radio', null, null, ts( 'All' )      , 'All'       );
        
        $this->addGroup( $status, 'contribution_status', ts( 'Contribution Status' ) );

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

                if ( $row['cancel_date'] ) {
                    $cancel += $row['total_amount'];
                } else {
                    $total     += $row['total_amount'];
                }
            }

            $this->assign( 'total_amount' , $total  );
            $this->assign( 'cancel_amount', $cancel );
            $this->assign( 'num_amount'   , count( $rows ) );
        } 

         // add buttons 
        $this->addButtons( array( 
                                 array ( 'type'      => 'refresh', 
                                         'name'      => ts('Search') , 
                                         'isDefault' => true     ) 
                                 )         
                           ); 
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
        $this->set( 'formValues', $this->_formValues );

        $sortID = null; 
        if ( $this->get( CRM_Utils_Sort::SORT_ID  ) ) { 
            $sortID = CRM_Utils_Sort::sortIDValue( $this->get( CRM_Utils_Sort::SORT_ID  ), 
                                                   $this->get( CRM_Utils_Sort::SORT_DIRECTION ) ); 
        } 

        $selector =& new CRM_Contribute_Selector_Search( $this->_formValues, $this->_action );
        $controller =& new CRM_Core_Selector_Controller($selector , 
                                                        $this->get( CRM_Utils_Pager::PAGE_ID ), 
                                                        $sortID, 
                                                        CRM_Core_Action::VIEW,
                                                        $this,
                                                        CRM_Core_Selector_Controller::SESSION );
        $controller->setEmbedded( true ); 
        $controller->run(); 

    }

}

?>
