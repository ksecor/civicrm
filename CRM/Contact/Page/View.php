<?php
/*
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
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Page.php';

class CRM_Contact_Page_View extends CRM_Page {

    /**
     * constants for various modes that the page can operate as
     *
     * @var const int
     */
    const
        MODE_NONE                  =   0,
        MODE_NOTE                  =   1,
        MODE_GROUP                 =   2,
        MODE_REL                   =   4;

    /**
     * the contact id of the contact being viewed
     * @int
     */
    protected $_contactId;

    /**
     * class constructor
     *
     * @param string $name  name of the page
     * @param string $title title of the page
     * @param int    $mode  mode of the page
     *
     * @return CRM_Page
     */
    function __construct( $name, $title = null, $mode = null ) {
        parent::__construct($name, $title, $mode);
    }

    function run( ) {

        $this->_contactId = CRM_Request::retrieve( 'cid', $this, true );

        switch ( $this->_mode ) {
        case self::MODE_NONE:
            $this->runModeNone( );
            break;

        case self::MODE_NOTE:
            CRM_Contact_Page_Note::run( $this );
            break;
        }

        return parent::run( );
    }

    function runModeNone( ) {
        $params   = array( );
        $defaults = array( );
        $ids      = array( );

        $params['id'] = $params['contact_id'] = $this->_contactId;
        $contact = CRM_Contact_BAO_Contact::retrieve( $params, $defaults, $ids );

        CRM_Contact_BAO_Contact::resolveDefaults( $defaults );
        $this->assign( $defaults );

        $this->setShowHide( $defaults );
    }

    function setShowHide( &$defaults ) {
        $commPrefs = array();
        $notes     = array();
   
        if ( $this->_mode == self::MODE_NOTE){
            $notes  =    array('notes[show]' => 1 );
        } else {
            $commPrefs = array('commPrefs'   => 1);
            $notes  =    array('notes'       => 1 );
        }

        $showHide = new CRM_ShowHideBlocks($commPrefs, $notes);

        if ($this->_mode == self::MODE_NONE) {
            if ( $defaults['contact_type'] == 'Individual' ) {
                $showHide->addShow( 'demographics[show]' );
                $showHide->addHide( 'demographics' );
            }

            
            $showHide->addHide( 'commPrefs[show]' );
            if ( array_key_exists( 'location', $defaults ) ) {
                $numLocations = count( $defaults['location'] );
                if ( $numLocations > 0 ) {
                    $showHide->addShow( 'location[1]' );
                    $showHide->addHide( 'location[1][show]' );
                }
                for ( $i = 1; $i < $numLocations; $i++ ) {
                    $locationIndex = $i + 1;
                    $showHide->addShow( "location[$locationIndex][show]" );
                    $showHide->addHide( "location[$locationIndex]" );
                }
            }
        }

        $showHide->addToTemplate( );
    }

    function getContactId( ) {
        return $this->_contactId;
    }

}

?>