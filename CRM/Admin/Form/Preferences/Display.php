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

require_once 'CRM/Admin/Form/Preferences.php';

/**
 * This class generates form components for the display preferences
 * 
 */
class CRM_Admin_Form_Preferences_Display extends CRM_Admin_Form_Preferences
{
    function preProcess( ) {
        parent::preProcess( );

        // add all the checkboxes
        $this->_cbs = array(
                            'contact_view_options'    => ts( 'Contact View Options'   ),
                            'contact_edit_options'    => ts( 'Contact Edit Options'   ),
                            'advanced_search_options' => ts( 'Advanced Search Options'),
                            'user_dashboard_options'  => ts( 'User Dashboard Options' ),
                            );
    }

    function setDefaultValues( ) {
        $defaults = array( );

        $defaults['location_count'] =
            $this->_config->location_count ? $this->_config->location_count : 1;

        parent::cbsDefaultValues( $defaults );

        return $defaults;
    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $this->add('text',
                   'location_count',
                   ts('Location Blocks to display'),
                   CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Preferences', 'location_count' ) );
        $this->addRule( 'location_count', ts( 'Location count has to be postive' ), 'positiveInteger' );

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
        if ( $this->_action == CRM_Core_Action::VIEW ) {
            return;
        }

        $this->_params = $this->controller->exportValues( $this->_name );

        $this->_config->location_count = $this->_params['location_count'];

        parent::postProcess( );
    }//end of function

}

?>
