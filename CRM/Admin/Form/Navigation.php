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

require_once 'CRM/Admin/Form.php';

/**
 * This class generates form components for Navigation
 * 
 */
class CRM_Admin_Form_Navigation extends CRM_Admin_Form
{
    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        parent::buildQuickForm( );
       
        if ($this->_action & CRM_Core_Action::DELETE ) { 
            return;
        }
        
        $this->applyFilter('__ALL__', 'trim');
        $this->add('text',
                   'label',
                   ts('Title'),
                   CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Navigation', 'label' ),
                   true );

        if ( !$this->_id ) {
            $menuOptions = array( '1' => ts('Create New Menu'),
                                  '2' => ts('Select Existing Menu'));
            $this->add( 'select', 'menu_option', ts( 'Menu' ),  $menuOptions );
        }
        
        require_once 'CRM/Core/BAO/Navigation.php';
        $existingMenus = CRM_Core_BAO_Navigation::getMenus( );
        asort( $existingMenus );
        $this->add( 'select', 'path', ts( 'Existing Menu' ), array( '' => ts('-- select --') ) + $existingMenus );
        $this->add('text', 'url', ts('Url'), CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Navigation', 'url' ) );
        require_once 'CRM/Core/Permission.php';
        $permissions = CRM_Core_Permission::basicPermissions();
        $this->addElement('select', 'permission', ts('Permission'), $permissions, array( 'size' => 5,'multiple' ) );        
        $this->add('checkbox', 'CiviCRM_OP_OR', null, ts( 'Check to match ANY; uncheck to match ALL' ) ); 
        $parentMenu = array( );
        CRM_Core_BAO_Navigation::getNavigationList( $parentMenu );            
        
        if ( isset( $this->_id ) ) {
            unset( $parentMenu[$this->_id] );
        }
        $this->add( 'select', 'parent_id', ts( 'Parent' ), array( '' => ts('-- select --') ) + $parentMenu );
        $this->add('checkbox', 'is_active', ts('Enabled?'));
    }
    
    public function setDefaultValues() {
        $defaults = array( );
        if ( isset( $this->_id ) ) {
            $params = array( 'id' => $this->_id );
            CRM_Core_BAO_Navigation::retrieve( $params, $defaults );
            if ( $defaults['permission_operator'] === 'OR' ) {
                $defaults['permission_operator'] = 1;
            }
        }
        
        // its ok if there is no element called is_active
        $defaults['is_active'] = ( $this->_id ) ? $defaults['is_active'] : 1;
        return $defaults;
    }
       
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() {
        // get the submitted form values.  
        $params = $this->controller->exportValues( $this->_name );
        
        if ( isset( $this->_id ) ) {
            $params['id'] = $this->_id;
        }
        
        if ( $params['menu_option'] == 1 ) {
            unset( $params['path'] );
        }
        
        $navigation = CRM_Core_BAO_Navigation::add( $params );
        
        CRM_Core_Session::setStatus( ts('Menu \'%1\' has been saved.',
                                        array( 1 => $navigation->label )) );
    } //end of function

}


