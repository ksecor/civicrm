<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
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

require_once 'CRM/Quest/Controller/MatchApp.php';

class CRM_Quest_Controller_MatchApp_Partner extends CRM_Quest_Controller_MatchApp {

    protected $_action;

    /**
     * class constructor
     */
    function __construct( $title = null, $action = CRM_Core_Action::NONE, $modal = true ) {
        parent::__construct( $title, $action, $modal, 'Partner' );
    }

    /**
     * Create the header for the wizard from the list of pages
     * Store the created header in smarty
     *
     * @param string $currentPageName name of the page being displayed
     * @return array
     * @access public
     */
    function wizardHeader( $currentPageName ) {
        $this->_sections = array( 
                                 'Amherst'  => array( 'title'     => 'Amherst College',
                                                      'processed' => true,
                                                      'valid'     => true,
                                                      'index'     => 0 ),
                                 'Bowdoin'  => array( 'title'     => 'Bowdoin College',
                                                      'processed' => true,
                                                      'valid'     => true,
                                                      'index'     => 0 ),
                                 'Columbia'  => array( 'title'     => 'Columbia University',
                                                       'processed' => true,
                                                       'valid'     => true,
                                                       'index'     => 0 ),
                                 'Pomona'   => array( 'title'     => 'Pomona College',
                                                      'processed' => true,
                                                      'valid'     => true,
                                                      'index'     => 0 ),
                                 'Princeton'=> array( 'title'     => 'Princeton University',
                                                      'processed' => true,
                                                      'valid'     => true,
                                                      'index'     => 0 ),
                                 'Rice'     => array( 'title'     => 'Rice University',
                                                      'processed' => true,
                                                      'valid'     => true,
                                                      'index'     => 0 ),
                                 'Stanford' => array( 'title'     => 'Stanford University',
                                                      'processed' => true,
                                                      'valid'     => true,
                                                      'index'     => 0 ),
                                 'Wellesley' => array( 'title'     => 'Wellesley College',
                                                       'processed' => true,
                                                       'valid'     => true,
                                                       'index'     => 0 ),
                                 'Wheaton'  => array( 'title'     => 'Wheaton College',
                                                      'processed' => true,
                                                      'valid'     => true,
                                                      'index'     => 0 ),
                                 );
        parent::wizardHeader( $currentPageName );
    }

}

?>