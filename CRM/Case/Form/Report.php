<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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

require_once "CRM/Core/Form.php";
/**
 * This class generates form components for case report
 * 
 */
class CRM_Case_Form_Report extends CRM_Core_Form
{

    /**
     * Case Id
     */
    public $_caseID = null;

    /**
     * Client Id
     */
    public $_clientID = null;

    /**
     * activity set name
     */
    public $_activitySetName = null;
    
    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    function preProcess( ) 
    {        
        $this->_caseID           = CRM_Utils_Request::retrieve( 'id'   , 'Integer', $this, true );
        $this->_clientID         = CRM_Utils_Request::retrieve( 'cid'  , 'Integer', $this, true );
        $this->_activitySetName  = CRM_Utils_Request::retrieve( 'asn', 'String' , $this, true );
    }
    
    public function buildQuickForm( ) 
    {
        
        $includeActivites = array( 1 => ts( 'Include All Activities' ),
                                   2 => ts( 'Include Missing Activities Only' ) );
        $this->addRadio( 'include_activities',
                         null,
                         $includeActivites,
                         null,
                         '&nbsp;',
                         true );

        $this->add('checkbox',
                   'is_redact',
                   ts( 'Redact (hide) Client and Service Provider Data' ) );
                         
        $this->addButtons(array( 
                                array ( 'type'      => 'next',
                                        'name'      => ts('Generate Report'), 
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
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
    public function postProcess() 
    {
        // store the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );

        require_once 'CRM/Case/XMLProcessor/Report.php';
        $xmlProcessor = new CRM_Case_XMLProcessor_Report( );
        $xmlProcessor->run( $this->_clientID,
                            $this->_caseID,
                            $this->_activitySetName,
                            $params );

    }

}
