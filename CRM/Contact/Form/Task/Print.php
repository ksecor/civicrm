<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Contact/Form/Task.php';

/**
 * This class provides the functionality to save a search
 * Saved Searches are used for saving frequently used queries
 */
class CRM_Contact_Form_Task_Print extends CRM_Contact_Form_Task {

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess()
    {
        // set print view, so that print templates are called
        $this->controller->setPrint( true );

        // create the selector, controller and run - store results in session
        $fv               =  $this->get( 'formValues' );
        $params           = $this->get( 'queryParams' );
        $returnProperties = $this->get( 'returnProperties' );
        $selector   =& new CRM_Contact_Selector($fv, $params, $returnPropeties, $this->_action);
        $controller =& new CRM_Core_Selector_Controller($selector , null, null, CRM_Core_Action::VIEW, $this, CRM_Core_Selector_Controller::SCREEN);
        $controller->setEmbedded( true );
        $controller->run();
    }


    /**
     * Build the form - it consists of
     *    - displaying the QILL (query in local language)
     *    - displaying elements for saving the search
     *
     * @access public
     * @return void
     */
    function buildQuickForm()
    {
        //
        // just need to add a javacript to popup the window for printing
        // 
        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Print Contact List'),
                                         'js'        => array( 'onclick' => 'window.print()' ),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'back',
                                         'name'      => ts('Done') ),
                                 )
                           );
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return void
     */
    public function postProcess()
    {
        // redirect to the main search page after printing is over
    }
}
?>
