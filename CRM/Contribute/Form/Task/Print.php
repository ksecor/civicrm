<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Contribute/Form/Task.php';
require_once 'CRM/Contribute/Selector/Search.php';
require_once 'CRM/Core/Selector/Controller.php';

/**
 * This class provides the functionality to save a search
 * Saved Searches are used for saving frequently used queries
 */
class CRM_Contribute_Form_Task_Print extends CRM_Contribute_Form_Task {

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess()
    {
        parent::preprocess( );

        // set print view, so that print templates are called
        $this->controller->setPrint( true );

        // create the selector, controller and run - store results in session
        $fv         =  $this->get( 'formValues' );
        $selector   =& new CRM_Contribute_Selector_Search($fv, $this->_action, $this->_contributionClause );
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
                                         'name'      => ts('Print Contributions'),
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
