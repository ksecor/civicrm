<?php
/**
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Admin/Form.php';

/**
 * This class generates form components generic to IM provider
 * 
 */
class CRM_Admin_Form_IMProvider extends CRM_Admin_Form
{
    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $this->applyFilter('__ALL__', 'trim');
        $this->add('text', 'name'       , ts('Name')       ,
                   CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_IMProvider', 'name' ) );
        $this->addRule( 'name', ts('Please enter a valid name.'), 'required' );
        $this->addRule( 'name', ts('Name already exists in Database.'), 'objectExists', array( 'CRM_Core_DAO_IMProvider', $this->_id ) );

        $this->add('checkbox', 'is_active', ts('Enabled?'));

        parent::buildQuickForm( );
    }
    
       
    /**
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        // store the submitted values in an array
        $params = $this->exportValues();
        $params['is_active'] =  CRM_Utils_Array::value( 'is_active', $params, false );
        
        // action is taken depending upon the mode
        $IMProvider               =& new CRM_Core_DAO_IMProvider( );
        $IMProvider->name         = $params['name'];
        $IMProvider->is_active    = $params['is_active'];
        $IMProvider->domain_id    = CRM_Core_Config::domainID( );
        
        if ($this->_action & CRM_Core_Action::UPDATE ) {
            $IMProvider->id = $this->_id;
        }

        $IMProvider->save( );

        CRM_Core_Session::setStatus( ts('The IM Provider "%1" has been saved.',
					array( 1 => $IMProvider->name ) ) );
    }//end of function


}

?>
