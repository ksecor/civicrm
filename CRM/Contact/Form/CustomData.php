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

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/ShowHideBlocks.php';

/**
 * This class generates form components for custom data
 * 
 * It delegates the work to lower level subclasses and integrates the changes
 * back in. It also uses a lot of functionality with the CRM API's, so any change
 * made here could potentially affect the API etc. Be careful, be aware, use unit tests.
 *
 */
class CRM_Contact_Form_CustomData extends CRM_Core_Form
{
    /**
     * The table id, used when editing/creating custom data
     *
     * @var int
     */
    protected $_tableId;
    
    /**
     * entity type of the table id
     *
     * @var string
     */
    protected $_entityType;
    
    /**
     * entity sub type of the table id
     *
     * @var string
     * @access protected
     */
    protected $_entitySubType;
    
    
    /**
     * the group tree data
     *
     * @var array
     */
    protected $_groupTree;

    /**
     * Which blocks should we show and hide.
     *
     * @var CRM_Core_ShowHideBlocks
     */
    protected $_showHide;

    /**
     * Array group titles.
     *
     * @var array
     */
    protected $_groupTitle;

    /**
     * Array group display status.
     *
     * @var array
     */
    protected $_groupCollapseDisplay;

    /**
     * the id of the object being viewed (note/relationship etc)
     *
     * @int
     * @access protected
     */
    protected $_groupId;

    /**
     * pre processing work done here.
     *
     * gets session variables for table name, id of entity in table, type of entity and stores them.
     *
     * @param
     * @return void
     *
     * @access public
     *
     */
    function preProcess()
    {
        $this->_tableId       = CRM_Utils_Request::retrieve( 'tableId', 'Positive', $this, true );
        $this->_groupId       = CRM_Utils_Request::retrieve( 'groupId', 'Positive', $this, true );
        $this->_entityType    = CRM_Utils_Request::retrieve( 'entityType', 'String'  , CRM_Core_DAO::$_nullArray );
        if ( $this->_entityType == null ) {
            require_once 'CRM/Contact/BAO/Contact.php';
            $this->_entityType = CRM_Contact_BAO_Contact::getContactType( $this->_tableId );

            $session =& CRM_Core_Session::singleton( );
            $session->replaceUserContext( CRM_Utils_System::url( 'civicrm/contact/view',
                                                                 "reset=1&action=browse&cid={$this->_tableId}&gid={$this->_groupId}&selectedChild=custom_{$this->_groupId}" ) );

            // also set title
            list( $displayName, $contactImage ) = CRM_Contact_BAO_Contact::getDisplayAndImage( $this->_tableId );
            CRM_Utils_System::setTitle( $displayName, $contactImage . ' ' . $displayName );

            $this->assign( 'showBlockJS', 1 );
        }

        $this->_entitySubType = null;

        require_once 'CRM/Core/BAO/CustomGroup.php';
        $groupDetails = CRM_Core_BAO_CustomGroup::getGroupDetail($this->_groupId);
        if ( $groupDetails[$this->_groupId]['extends'] == 'Contact') {
            $this->_entitySubType = $this->get('entitySubType');
        }
        
        if ( is_null($this->_entitySubType) ) {
            $this->_groupTree  =
                CRM_Core_BAO_CustomGroup::getTree($this->_entityType, 
                                                  $this->_tableId,
                                                  $this->_groupId);
        } else {
            $this->_groupTree  =
                CRM_Core_BAO_CustomGroup::getTree($this->_entityType,
                                                  $this->_tableId,
                                                  $this->_groupId,
                                                  $groupDetails[$this->_groupId]['extends_entity_column_value']);
        }
    }
    
    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        CRM_Core_BAO_CustomGroup::buildQuickForm( $this, $this->_groupTree,
                                                  'showBlocks', 'hideBlocks',
                                                  false, true );

        $session = & CRM_Core_Session::singleton( );
        $uploadNames = $session->get( 'uploadNames' );
        if ( is_array( $uploadNames ) && ! empty ( $uploadNames ) ) {
            $buttonType = 'upload';
        } else {
            $buttonType = 'next';
        }
        
        $this->addButtons(array(
                                array ( 'type'      => $buttonType,
                                        'name'      => ts('Save'),
                                        'isDefault' => true   ),
                                array ( 'type'       => 'cancel',
                                        'name'      => ts('Cancel') ),
                                )
                          );
        

        if ($this->_action & ( CRM_Core_Action::VIEW | CRM_Core_Action::BROWSE ) ) {
             $this->freeze();
        }
    }
    
    /**
     * Set the default form values
     *
     * @access protected
     * @return array the default array reference
     */
    function &setDefaultValues()
    {
        $defaults = array();
        
        // do we need inactive options ?
        if ($this->_action & ( CRM_Core_Action::VIEW | CRM_Core_Action::BROWSE ) ) {
            $inactiveNeeded = true;
            $viewMode = true;
        } else {
            $viewMode = false;
            $inactiveNeeded = false;
        }

        CRM_Core_BAO_CustomGroup::setDefaults( $this->_groupTree, $defaults, $viewMode, $inactiveNeeded );

        return $defaults;
    }
    
    /**
     * Process the user submitted custom data values.
     *
     * @access public
     * @return void
     */
    public function postProcess() 
    {
        // Get the form values and groupTree
        $params = $this->controller->exportValues( $this->_name );
        require_once 'CRM/Core/BAO/CustomValueTable.php';
        CRM_Core_BAO_CustomValueTable::postProcess( $params,
                                                    $this->_groupTree[$this->_groupId]['fields'],
                                                    'civicrm_contact',
                                                    $this->_tableId,
                                                    $this->_entityType );
    }
}


