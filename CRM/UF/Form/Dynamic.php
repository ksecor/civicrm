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

require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for custom data
 * 
 * It delegates the work to lower level subclasses and integrates the changes
 * back in. It also uses a lot of functionality with the CRM API's, so any change
 * made here could potentially affect the API etc. Be careful, be aware, use unit tests.
 *
  */
class CRM_UF_Form_Dynamic extends CRM_Core_Form
{
    /**
     * the group tree data
     *
     * @var array
     */
    protected $_groupTree;

    /**
     * pre processing work done here.
     *
     * gets session variables for table name, id of entity in table, type of entity and stores them.
     *
     * @param none
     * @return none
     *
     * @access public
     *
     */
    function preProcess()
    {
        // gets all details of group tree for entity
        $this->_fields  = CRM_Core_BAO_UFGroup::getFields( $this->get( 'title' ) );
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm()
    {
        $this->assign( 'fields', $this->_fields );

        // add the form elements
        foreach ($this->_fields as $name => $field ) {
            $this->add('text', $name, $field['title'], $field['attributes'], $field['is_required'] );
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
        return $defaults;
    }

       
    /**
     * Process the user submitted custom data values.
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        // Get the form values and groupTree
        $fv = $this->exportValues();
        CRM_Core_Error::debug( 'fv', $fv );
    }
}

?>
