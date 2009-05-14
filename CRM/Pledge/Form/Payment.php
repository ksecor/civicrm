<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Contribute/PseudoConstant.php';

/**
 * This class generates form components for processing a pledge payment
 * 
 */
class CRM_Pledge_Form_Payment extends CRM_Core_Form
{
    /**
     * the id of the pledge payment that we are proceessing
     *
     * @var int
     * @public
     */
    public $_id;
    
    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    public function preProcess()  
    {  
        // check for edit permission
        if ( ! CRM_Core_Permission::check( 'edit pledges' ) ) {
            CRM_Core_Error::fatal( ts( 'You do not have permission to access this page' ) );
        }
        
        $this->_id  = CRM_Utils_Request::retrieve( 'ppId', 'Positive', $this );
    }
    
    /**
     * This function sets the default values for the form. 
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        $defaults = array( );
        if ( $this->_id ) {
            $params['id'] = $this->_id;
            require_once 'CRM/Pledge/BAO/Payment.php';
            CRM_Pledge_BAO_Payment::retrieve( $params, $defaults );
            $defaults['scheduled_date'] = CRM_Utils_Date::unformat($defaults['scheduled_date']);
            
            $statuses = CRM_Contribute_PseudoConstant::contributionStatus( );
            $this->assign('status', $statuses[$defaults['status_id']] );
        }

        return $defaults;
    }
    
    /** 
     * Function to build the form 
     * 
     * @return None 
     * @access public 
     */ 
    public function buildQuickForm( )  
    {   
        //add various dates
        $element =& $this->add('date', 'scheduled_date', ts('Scheduled Date'), CRM_Core_SelectValues::date('activityDate'));    
        $this->addRule('scheduled_date', ts('Select a valid Scheduled date.'), 'qfDate');
        
        $this->addButtons(array( 
                                array ( 'type'      => 'next',
                                        'name'      => ts('Save'), 
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                        'js'        => array( 'onclick' => "return verify( );" ),
                                        'isDefault' => true   ), 
                                array ( 'type'      => 'cancel', 
                                        'name'      => ts('Cancel') ), 
                                ) 
                          );
    }
    
    /** 
     * Function to process the form 
     * 
     * @access public 
     * @return None 
     */ 
    public function postProcess( )  
    {
        //get the submitted form values.  
        $formValues = $this->controller->exportValues( $this->_name );
        $params = array( );
        $formValues['scheduled_date']['H'] = '00';
        $formValues['scheduled_date']['i'] = '00';
        $formValues['scheduled_date']['s'] = '00';
        $params['scheduled_date'] = CRM_Utils_Date::format( $formValues['scheduled_date'] );
        $now = date( 'Ymd' );
        
        if ( CRM_Utils_Date::overdue( CRM_Utils_Date::customFormat( $params['scheduled_date'], '%Y%m%d'), $now ) ) {
            $params['status_id'] =  array_search( 'Overdue', CRM_Contribute_PseudoConstant::contributionStatus( )); 
        } else {
            $params['status_id'] =  array_search( 'Pending', CRM_Contribute_PseudoConstant::contributionStatus( )); 
        } 
        
        $params['id'] = $this->_id;
        $pledgeId = CRM_Core_DAO::getFieldValue( 'CRM_Pledge_DAO_Payment', $params['id'], 'pledge_id' );       
        require_once 'CRM/Pledge/BAO/Payment.php';
        CRM_Pledge_BAO_Payment::add( $params );

        //update pledge status
        CRM_Pledge_BAO_Payment::updatePledgePaymentStatus( $pledgeId );
        
        $statusMsg = ts('Pledge Payment Schedule has been updated.<br />');
        CRM_Core_Session::setStatus( $statusMsg );
    }
    
}

