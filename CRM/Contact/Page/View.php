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

        $this->_contactId = CRM_Array::value( 'cid', $_REQUEST );
        if ( ! isset( $this->_contactId ) ) {
            $this->_contactId = $this->get( 'contact_id' );
        }
        if ( ! isset( $this->_contactId ) ) {
            CRM_Error::fatal( "Could not find a valid contact_id" );
        }

        // store the contact id
        $this->set( 'contact_id', $this->_contactId );

        $params   = array( );
        $defaults = array( );
        $ids      = array( );

        $params['id'] = $params['contact_id'] = $this->_contactId;
        $contact = CRM_Contact_BAO_Contact::retrieve( $params, $defaults, $ids );

        CRM_Contact_BAO_Contact::resolveDefaults( $defaults );
        $this->assign( $defaults );

        $this->setShowHide( $defaults );

        return parent::run( );
    }

    function setShowHide( &$defaults ) {

        $a_CommprefArray = array();
        $a_noteArray = array();
   
        if ($this->_mode == CRM_PAGE::VIEW_MODE_NONE){
            $a_CommprefArray = array('commPrefs'       => 1);
            $a_noteArray  =    array('notes'           => 1 );
        }

        if ( $this->_mode == CRM_PAGE::VIEW_MODE_NOTE){
            $a_noteArray  =    array('notes[show]'           => 1 );
        }

        /*
        $showHide = new CRM_ShowHideBlocks( array('commPrefs'       => 1,
                                                  'notes[show]'     => 1),
                                            array('notes'           => 1 ) ) ;

        */

        $showHide = new CRM_ShowHideBlocks($a_CommprefArray, $a_noteArray);

        if ($this->_mode == CRM_PAGE::VIEW_MODE_NONE){
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

}

?>