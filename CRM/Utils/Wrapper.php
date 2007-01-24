<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 * The Contact Wrapper is a wrapper class which is called by
 * contact.module after it parses the menu path.
 *
 * The key elements of the wrapper are the controller and the 
 * run method as explained below.
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Base.php';
require_once 'CRM/Core/Controller/Simple.php';

class CRM_Utils_Wrapper 
{
    /**
     * Simple Controller
     *
     * The controller which will handle the display and processing of this page.
     *
     * @access protected
     */
    protected $_controller;

    /**
     * Run.
     *
     * The heart of the callback processing is done by this method.
     * forms are of different type and have different operations.
     *
     * @param string $formName    name of the form processing this action
     * @param string $formLabel   label for the above form
     * @param int    $mode        mode of operation.
     * @param boolean $addSequence should we add a unique sequence number to the end of the key
     *
     * @return none.
     * @access public
     */
    function run($formName, $formLabel, $mode, $addSequence = false ) {
        $this->_controller =& new CRM_Core_Controller_Simple( $formName, $formLabel, $mode, false, $addSequence );
        $this->_controller->process();
        $this->_controller->run();
    }

}

?>
