<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Core/Controller.php';

class CRM_Event_Controller_ManageEvent extends CRM_Core_Controller {

    /**
     * class constructor
     */
    function __construct( $title = null, $action = CRM_Core_Action::NONE, $modal = true ) {
        parent::__construct( $title, $modal );

        require_once 'CRM/Event/StateMachine/ManageEvent.php';
        $this->_stateMachine =& new CRM_Event_StateMachine_ManageEvent( $this, $action );

        // create and instantiate the pages
        $this->addPages( $this->_stateMachine, $action );

        $config  =& CRM_Core_Config::singleton( );
        $uploadDir = $config->uploadDir;

        require_once 'CRM/Core/BAO/File.php';
        $uploadNames = $this->get( 'uploadNames' );
        if ( ! empty( $uploadNames ) ) {
            $uploadNames = array_merge( $uploadNames,
                                        CRM_Core_BAO_File::uploadNames( ) );
        } else {
            $uploadNames = CRM_Core_BAO_File::uploadNames( );
        }

        // add all the actions
        $this->addActions( $uploadDir, $uploadNames );
    }

}


