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

class CRM_Group_Controller extends CRM_Core_Controller {

    /**
     * class constructor
     */
    function __construct( $title = null, $action = CRM_Core_Action::NONE, $modal = true ) {
        parent::__construct( $title, $modal );

        require_once 'CRM/Group/StateMachine.php';
        $this->_stateMachine =& new CRM_Group_StateMachine( $this, $action );

        // create and instantiate the pages
        $this->addPages( $this->_stateMachine, $action );

        // hack for now, set Search to Basic mode
        $this->_pages['Basic']->setAction( CRM_Core_Action::BASIC );

        // add all the actions
        $config =& CRM_Core_Config::singleton( );
        
        //changes for custom data type File
        $uploadNames = $this->get( 'uploadNames' );
        $config =& CRM_Core_Config::singleton( );
        
        if ( is_array( $uploadNames ) && ! empty ( $uploadNames ) ) {
            $uploadArray = $uploadNames;
            $this->addActions( $config->customFileUploadDir, $uploadArray );
            $uploadNames = $this->set( 'uploadNames', null );
        } else {
            $this->addActions( );
        }
    }

    function run( ) {
        return parent::run( );
    }

    public function selectorName( ) {
        return $this->get( 'selectorName' );
    }
}

