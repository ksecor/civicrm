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
 * @copyright CiviCRM LLC (c) 2004-2008
 * $Id$
 *
 */

class CRM_Pledge_Form_Edit {

    public $_action;

    /**
     * the id of the pledge that we are processing
     *
     * @var int
     * @protected
     */
    public $_id;

    protected $_values;

    /**
     * the id of the note 
     *
     * @var int
     * @protected
     */
    public $_noteID;

    /**
     * the id of the contact associated with this contribution
     *
     * @var int
     * @protected
     */
    public $_contactID;

    /**
     * Store the contribution Type ID
     *
     * @var array
     */
    public $_contributionTypeID;
    
    public function preProcess( ) {
        // check for edit permission
        if ( ! CRM_Core_Permission::check( 'edit pledges' ) ) {
            CRM_Core_Error::fatal( ts( 'You do not have permission to access this page' ) );
        }

        $this->_action    = CRM_Utils_Request::retrieve( 'action', 'String',
                                                         $this, false, 'add' );
        $this->assign( 'action', $this->_action );

        $this->_contactID = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this, true );
        $this->_id        = CRM_Utils_Request::retrieve( 'id' , 'Positive', $this );
        
        //set the pledge mode.
        $this->_mode = CRM_Utils_Request::retrieve( 'mode', 'String', $this );
        $this->assign( 'pledgeMode', $this->_mode );

        $this->_values = array( );
        if ( $this->_id ) {
            $idParams = array( 'id' => $this->_id );
            CRM_Core_DAO::commomRetrieve( 'CRM_Pledge_DAO_Pledge',
                                          $idParams,
                                          $this->_values );
            $this->_contributionTypeID = $this->_values['contribution_type_id'];
        }
    }

    public function setDefaultValues( ) {
        $defaults = $this->_values;
        return $defaults;
    }

    public function buildQuickForm( )   {

    }

}



