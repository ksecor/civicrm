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
        MODE_REL                   =   4,
        MODE_TAGS                  =   8;

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
        $this->getContactDetails( );

        if ( $this->_mode == self::MODE_NONE ) {
            $this->runModeNone( );
        } else if ( $this->_mode == self::MODE_NOTE ) {
            CRM_Contact_Page_Note::run( $this );
        } else if ( $this->_mode == self::MODE_TAGS ) {
            $this->runModeTags( );
        } 

        return parent::run( );
    }

    function getContactDetails( ) {
        // for all other tabs, we only need the displayName
        // so if the display name is cached, we can skip the other processing
        $displayName = $this->get( 'displayName' );
        if ( isset( $displayName ) && $this->_mode != self::MODE_NONE ) {
            $this->assign( 'displayName', $displayName );
            return;
        }

        $params   = array( );
        $defaults = array( );
        $ids      = array( );

        $params['id'] = $params['contact_id'] = $this->_contactId;
        $contact = CRM_Contact_BAO_Contact::retrieve( $params, $defaults, $ids );

        CRM_Contact_BAO_Contact::resolveDefaults( $defaults );

        // fix the display name for various types and store in session
        if ( $defaults['contact_type'] == 'Individual' ) {
            $displayName = $defaults['prefix'] . ' ' . $defaults['display_name'] . ' ' . $defaults['suffix'];
        } else {
            $displayName = $defaults['sort_name'];
        }
        $this->set( 'displayName', $displayName );
        
        if ( $this->_mode == self::MODE_NONE ) {
            $this->assign( $defaults );
        }
    }

    function runModeNone( ) {
        $params   = array( );
        $defaults = array( );
        $ids      = array( );

        $params['id'] = $params['contact_id'] = $this->_contactId;
        $contact = CRM_Contact_BAO_Contact::retrieve( $params, $defaults, $ids );

        CRM_Contact_BAO_Contact::resolveDefaults( $defaults );
        
        // fix the display name for various types and store in session
        if ( $defaults['contact_type'] == 'Individual' ) {
            $displayName = $defaults['prefix'] . ' ' . $defaults['display_name'] . ' ' . $defaults['suffix'];
        } else {
            $displayName = $defaults['sort_name'];
        }
        $this->assign( $defaults );
        $this->set( 'displayName', $displayName );

        $this->setShowHide( $defaults );
    }

    function runModeTags()
    {

        CRM_Error::le_method();

        $contactParam   = array();
        $defaults = array();
        $ids      = array();
        $array1 = array();

        $contactParam['id'] = $contactParam['contact_id'] = $this->_contactId;
        CRM_Error::debug_var("contactParam", $contactParam);
        $contact = CRM_Contact_BAO_Contact::retrieve($contactParam, $defaults, $ids);

        $this->assign($defaults);
        
        $contactEntityParam['entity_id'] = $this->_contactId;

        CRM_Error::debug_var("this->_contactId", $this->_contactId);
        

        $entityCategory =& CRM_Contact_BAO_EntityCategory::getCategory('crm_contact', $this->_contactId);
        
        CRM_Error::debug_var("entityCategory", $entityCategory);
       
        $this->assign('entityCategory', $entityCategory); 

        // $array1 = CRM_Contact_BAO_EntityCategory::getValues($params);        
        // $this->assign($array1);         

        $category = CRM_SelectValues::getCategory();

        foreach ($category as $categoryID => &$categoryDetail) {
            //push($categoryDetail

            CRM_Error::debug_var("categoryID", $categoryID);

            //            $checked = in_array($entityCategory, $categoryID) ? " checked " : " ";
            $checked = in_array($categoryID, $entityCategory) ? " checked " : " ";

            CRM_Error::debug_var("checked", $checked);

            $categoryDetail['checked'] = $checked;
        }

        CRM_Error::debug_var('category', $category);

        $this->assign('category', $category);
    }

    function setShowHide( &$defaults ) {
        $showHide = new CRM_ShowHideBlocks(array('commPrefs'       => 1,
                                                 'notes[show]'     => 1 ),
                                           array('notes'           => 1,
                                                 'commPrefs[show]' => 1 ) ) ;
        

        if ( $defaults['contact_type'] == 'Individual' ) {
            // is there any demographics data?
            if ( CRM_Array::value( 'gender'     , $defaults ) ||
                 CRM_Array::value( 'is_deceased', $defaults ) ||
                 CRM_Array::value( 'birth_date' , $defaults ) ) {
                $showHide->addShow( 'demographics' );
                $showHide->addHide( 'demographics[show]' );
            } else {
                $showHide->addShow( 'demographics[show]' );
                $showHide->addHide( 'demographics' );
            }
        }

        // is there any notes data?
        if ( CRM_Array::value( 'notesCount', $defaults ) ) {
            $showHide->addShow( 'notes' );
            $showHide->addHide( 'notes[show]' );
        }
            
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

        $showHide->addToTemplate( );
    }

    function getContactId( ) {
        return $this->_contactId;
    }

}

?>