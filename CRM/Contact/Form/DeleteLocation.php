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

require_once "CRM/Core/Form.php";

/**
 * This class is to build the form for Deleting Location
 */
class CRM_Contact_Form_DeleteLocation extends CRM_Core_Form
{
    
    /**
     * the contact id
     * whose location is to be deleted
     *
     * @var int
     * @protected
     */
    protected $_cid;
    
    /**
     * the id of the location
     * type that needs to be deleted
     *
     * @var int
     * @protected
     */
    protected $_ltypeid;
    
    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess();
        
        $this->_cid = CRM_Utils_Request::retrieve( 'cid', 'Positive',
                                                   $this, false );
        
        $this->_ltypeid = CRM_Utils_Request::retrieve( 'ltypeid', 'Positive',
                                                       $this, false );
    }
    
    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( )
    {
        $buttons = array(
                         array(
                               'type'      => 'next',
                               'name'      => ts('Delete Location'),
                               'isDefault' => true ),
                         array(
                               'type'       => 'cancel',       
                               'name'      => ts('Cancel') )
                         );
        $this->addButtons( $buttons );
    }
    
    /**
     * Process the form when submitted
     *
     * @return void
     * @access public
     */
    public function postProcess( )
    {
        require_once "CRM/Core/BAO/Location.php";
        CRM_Core_BAO_Location::deleteLocationBlocks( $this->_cid, $this->_ltypeid );
        
        CRM_Core_Session::setStatus( ts( 'The location has been deleted.' ) );
    }
}
