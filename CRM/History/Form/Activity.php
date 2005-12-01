<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
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
     * @param
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        $id = $this->get('id');
        
        $url = CRM_Utils_System::url('civicrm/contact/view/activity', 'show=1&action=browse&history=1');
        if (CRM_Utils_Request::retrieve('confirmed', $form, '', '', 'GET') ) {
            CRM_Core_BAO_History::del( $id );
            CRM_Core_Session::setStatus( "Selected Activity History record has been deleted." );
            CRM_Utils_System::redirect($url);
        }
        
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
     * @param
     * @access public
     * @return void
     */
    public function postProcess() 
    {
        CRM_Core_BAO_History::del( $this->get( 'id' ) );
        CRM_Core_Session::setStatus( "Selected Activity History record has been deleted." );
    }
}

?>