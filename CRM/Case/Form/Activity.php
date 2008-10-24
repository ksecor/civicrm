<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
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
 * This class create activities for a case
 * 
 */
class CRM_Case_Form_Activity extends CRM_Core_Form
{

    /**
     * The activity id 
     *
     * @var int
     */
    protected $_id;

    /**
     * The id of activity type 
     *
     * @var int
     */
    protected $_activityTypeId;

    /**
     * The id of case 
     *
     * @var int
     */
    protected $_caseId;


    /**
     * The id of logged in user
     *
     * @var int
     */
    protected $_uid;

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    function preProcess( ) 
    {        
        $this->_id             = CRM_Utils_Request::retrieve( 'id',    'Positive', $this );
        $this->_activityTypeId = CRM_Utils_Request::retrieve( 'atype', 'Positive', $this );
        $this->_caseId         = CRM_Utils_Request::retrieve( 'caseid', 'Positive', $this );

        $session    =& CRM_Core_Session::singleton();
        $this->_uid = $session->get('userID');
    }
    
    /**
     * This function sets the default values for the form. For edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
    }

    public function buildQuickForm( ) 
    {
    }
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
    }
}
