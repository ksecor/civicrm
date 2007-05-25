<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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

/**
 * This class generates form components for Location Type
 * 
 */
class CRM_Admin_Form_SystemConfig extends CRM_Core_Form
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
       
        $this->add('text',
                   'location_count',
                   ts('Location Blocks to display'),
                   CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_SystemConfig', 'location_count' ),
                   true );

        // add all the checkboxes
        $cbs = array(
                     'contact_summary_options' => ts( 'Contact Summary Options' ),
                     'edit_contact_options'    => ts( 'Edit Contact Options'    ),
                     'advanced_search_options' => ts( 'Advanced Search Options' ),
                     'user_dashboard_options'  => ts( 'User Dashboard Options'  ),
                     'admin_panel_options'     => ts( 'Admin Panel Options'     ),
                     );

        require_once 'CRM/Core/OptionGroup.php';
        foreach ( $cbs as $name => $title ) {
            $this->addCheckBox( $name, $title, 
                                array_flip( CRM_Core_OptionGroup::values( $name ) ),
                                null, null, null, null, '&nbsp;' );
        }

        if ($this->_action == CRM_Core_Action::VIEW ) {
            $this->freeze( );
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
    }//end of function

}

?>
