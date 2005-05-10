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
 * This class generates form components for Category
 * 
 */
class CRM_Admin_Form_Category extends CRM_Admin_Form
{
    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {

        if ($this->_mode == self::MODE_DELETE) {
            $this->addButtons( array(
                                     array ( 'type'      => 'next',
                                             'name'      => 'Delete',
                                             'isDefault' => true   ),
                                     array ( 'type'      => 'cancel',
                                             'name'      => 'Cancel' ),
                                     )
                               );
        } else {
            
            $this->add('text', 'name'       , 'Name'       ,
                       CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Category', 'name' ) );
            $this->addRule( 'name', 'Please enter a valid name.', 'required' );
            
            $this->add('text', 'description', 'Description', 
                       CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Category', 'description' ) );
            
            parent::buildQuickForm( ); 
        }
    }

       
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        $params = $ids = array();

        // store the submitted values in an array
        $params = $this->exportValues();
        $ids['category'] = $this->_id;
        
        if ($this->_mode == self::MODE_DELETE) {
            if ($this->_id  > 0 )  CRM_Contact_BAO_Category::del( $this->_id );
        } else {
            CRM_Contact_BAO_Category::add($params, $ids);
        }        
        
    }//end of function


}

?>
