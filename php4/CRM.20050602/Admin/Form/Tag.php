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
require_once 'CRM/Core/DAO.php';
require_once 'CRM/Contact/BAO/Tag.php';
require_once 'CRM/Admin/Form.php';

/**
 * This class generates form components for Tag
 * 
 */
class CRM_Admin_Form_Tag extends CRM_Admin_Form
{
    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
     function buildQuickForm( ) {

        if ($this->_action == CRM_CORE_ACTION_DELETE) {
            $this->addButtons( array(
                                     array ( 'type'      => 'next',
                                             'name'      => ts('Delete'),
                                             'isDefault' => true   ),
                                     array ( 'type'      => 'cancel',
                                             'name'      => ts('Cancel') ),
                                     )
                               );
        } else {
            $this->applyFilter('__ALL__', 'trim');
            
            $this->add('text', 'name'       , ts('Name')       ,
                       CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Tag', 'name' ) );
            $this->addRule( 'name', ts('Please enter a valid name.'), 'required' );

            $this->addRule( 'name', ts('Name already exists in Database.'), 'objectExists', array( 'CRM_Contact_DAO_Tag', $this->_id ) );

            $this->add('text', 'description', ts('Description'), 
                       CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Tag', 'description' ) );
            
            parent::buildQuickForm( ); 
        }
    }

       
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
     function postProcess() 
    {
        $params = $ids = array();

        // store the submitted values in an array
        $params = $this->exportValues();
        $ids['tag'] = $this->_id;
        
        if ($this->_action == CRM_CORE_ACTION_DELETE) {
            if ($this->_id  > 0 ) {
                CRM_Contact_BAO_Tag::del( $this->_id );
            }
        } else {
            CRM_Contact_BAO_Tag::add($params, $ids);
        }        
        
    }//end of function


}

?>
