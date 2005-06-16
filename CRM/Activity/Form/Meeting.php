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
 * This class generates form components for Meeting
 * 
 */
class CRM_Activity_Form_Meeting extends CRM_Admin_Form
{
    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $this->applyFilter('__ALL__', 'trim');
        $this->add('text', 'name'       , ts('Name')       ,
                   CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_LocationType', 'name' ) );
        $this->addRule( 'name', ts('Please enter a valid location type name.'), 'required' );
        $this->addRule( 'name', ts('Name already exists in Database.'), 'objectExists', array( 'CRM_Contact_DAO_LocationType', $this->_id ) );
        
        $this->add('text', 'description', ts('Description'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_LocationType', 'description' ) );

        $this->add('checkbox', 'is_active', ts('Enabled?'));
        parent::buildQuickForm( );
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
        $params = $this->exportValues();
        $params['is_active'] =  CRM_Utils_Array::value( 'is_active', $params, false );

        // action is taken depending upon the mode
        $locationType               =& new CRM_Contact_DAO_LocationType( );
        $locationType->domain_id    = CRM_Core_Config::domainID( );
        $locationType->name         = $params['name'];
        $locationType->description  = $params['description'];
        $locationType->is_active    = $params['is_active'];

        if ($this->_action & CRM_Core_Action::UPDATE ) {
            $locationType->id = $this->_id;
        }

        $locationType->save( );

        CRM_Core_Session::setStatus( ts('The location type "%1" has been saved.',
                                        array( 1 => $locationType->name )) );
    }//end of function


}

?>
