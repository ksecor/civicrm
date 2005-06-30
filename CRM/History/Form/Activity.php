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
require_once 'CRM/Core/BAO/History.php';

/**
 * This class generates form components for Activity. 
 * Currently it's used only for delete
 * 
 */
class CRM_History_Form_Activity extends CRM_Core_Form
{
    /**
     * Function to build the form
     *
     * @param none
     * @return None
     * @access public
     */
    public function buildQuickForm()
    {
        // only used for delete confirmation
        $this->addButtons(array(
                                array ('type'      => 'next',
                                       'name'      => ts('Delete'),
                                       'isDefault' => true),
                                array ('type'      => 'cancel',
                                       'name'      => ts('Cancel')),
                                )
                          );

        // get values for activity date, summary and type from db
        // and set them up for smarty variables
        $id = $this->get('id');

        $params = array('id' => $id);
        $row = CRM_Core_BAO_History::getHistory($params);
        $fields = array('activity_type', 'activity_summary', 'activity_date');
        foreach ($fields as $field) {
            if ($row[$id][$field]) {
                $this->assign($field, $row[$id][$field]);
            }
        }
    }
       
    /**
     * Function to process the form
     *
     * @param none
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        CRM_Core_BAO_History::del( $this->get( 'id' ) );
    }
}

?>