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

require_once 'CRM/Core/Page.php';


/**
 * Main page for viewing contact.
 *
 */
class CRM_Contact_Page_View extends CRM_Core_Page {
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
        MODE_TAG                   =   8,
        MODE_CD                    =  16,
        MODE_ACTIVITY              =  32;

    /**
     * the contact id of the contact being viewed
     * @int
     * @access protected
     */
    protected $_contactId;



    /**
     * Heart of the viewing process. The runner gets all the meta data for
     * the contact and calls the appropriate type of page to view.
     *
     * @param none
     * @return none
     * @access public
     *
     */
    function run()
    {
        // get the contact id from session or store
        $this->_contactId = CRM_Utils_Request::retrieve( 'cid', $this, true );

        // check for permissions
        if ( ! CRM_Contact_BAO_Contact::permissionedContact( $this->_contactId, 'view' ) ) {
            CRM_Core_Error::fatal( "You do not have the necessary permission to view this contact." );
        }

        $this->getContactDetails();

        $prevMode = $this->get('mode');
        // if we have switched modes we set the action to browse always
        // this is primarily because we retain state across tabs but want
        // to reset the action
        if (isset($prevMode) && $prevMode != $this->_mode) {
            $this->set('action', CRM_Core_Action::BROWSE);
        }
        $this->set('mode', $this->_mode);

        // call the appropriate run method for other detailed page views.
        if ( $this->_mode == self::MODE_NONE ) {
            $this->runModeNone( );
        } else if ( $this->_mode == self::MODE_NOTE ) {
            CRM_Contact_Page_Note::run( $this );
        } else if ( $this->_mode == self::MODE_REL ) {
            CRM_Contact_Page_Relationship::run( $this );
        } else if ( $this->_mode == self::MODE_TAG ) {
            CRM_Contact_Page_Tag::run( $this );
        } else if ( $this->_mode == self::MODE_GROUP ) {
            CRM_Contact_Page_GroupContact::run( $this );
        } else if ( $this->_mode == self::MODE_CD ) {
            CRM_Contact_Page_CustomData::run( $this );
        } else if ( $this->_mode == self::MODE_ACTIVITY ) {
            CRM_Contact_Page_Activity::run( $this );
        }
        return parent::run( );
    }


    /**
     * Get meta details of the contact. (display name for one) and 
     * set it in the session for usage by other tabs.
     *
     * @param none
     * @return none
     * @access public
     */
    function getContactDetails()
    {
        $config =& CRM_Core_Config::singleton( );
        $displayName = $this->get( 'displayName' );
             
        // for all other tabs, we only need the displayName
        // so if the display name is cached, we can skip the other processing
        if ( isset( $displayName ) && $this->_mode != self::MODE_NONE ) {
            $this->assign( 'displayName', $displayName );
            $contactImage = $this->get( 'contactImage' );
            // Set dynamic page title = contactImage + displayname>
            CRM_Utils_System::setTitle( $contactImage . ' ' . $displayName );
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
            $displayName = trim( $defaults['prefix'] . ' ' . $defaults['display_name'] . ' ' . $defaults['suffix'] );
        } else {
            $displayName = $defaults['sort_name'];
        }
        $this->set( 'displayName', $displayName );
   
        // Set dynamic page title for view = contact_type img + contact displayname
        $contactImage  = '<img src="' . $config->resourceBase . 'i/contact_';
        switch ($defaults['contact_type']) {
            case 'Individual' :
                $contactImage .= 'ind.gif" alt="' . ts('Individual') . '">';
                break;
            case 'Household' :
                $contactImage .= 'house.png" alt="' . ts('Household') . '" height="16" width="16">';
                break;
            case 'Organization' :
                $contactImage .= 'org.gif" alt="' . ts('Organization') . '" height="16" width="18">';
                break;
        }
        $this->set( 'contactImage', $contactImage );
        CRM_Utils_System::setTitle( $contactImage . ' ' . $displayName );

        if ( $this->_mode == self::MODE_NONE ) {
            $this->assign( $defaults );
        }
    }

    /**
     * Main view page for a contact. 
     *
     * @param none
     * @return none
     * @access public
     */
    function runModeNone()
    {
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

        // get the list of all the categories
        $tag =& CRM_Core_PseudoConstant::tag();
        // get categories for the contact id
        $entityTag =& CRM_Contact_BAO_EntityTag::getTag('crm_contact', $this->_contactId);

        if (is_array($entityTag)){
            $strCategories = '';
            foreach ($entityTag as $lngKey ) {
                $strCategories .= $tag[$lngKey];
                $strCategories .= ", ";
            }
        }
        
        $defaults['contactTag'] = substr($strCategories, 0, (strlen(trim($strCategories))-1));
        
        $this->assign( $defaults );
        $this->set( 'displayName', $displayName );

        $this->setShowHide( $defaults );
    }



    /**
     * Show hide blocks based on default values.
     *
     * @param array (reference) $defaults
     * @return none
     * @access public
     */
    function setShowHide( &$defaults ) {
        $showHide =& new CRM_Core_ShowHideBlocks(array('commPrefs'            => 1,
                                                      'notes[show]'          => 1,
                                                      'relationships[show]'  => 1,
                                                      'groups[show]'         => 1,
                                                      'activities[show]'     => 1),
                                                array('notes'                => 1,
                                                      'commPrefs[show]'      => 1,
                                                      'relationships'        => 1,
                                                      'groups'               => 1,
                                                      'activities'           => 1));                                                      
        

        if ( $defaults['contact_type'] == 'Individual' ) {
            // is there any demographics data?
            if ( CRM_Utils_Array::value( 'gender'     , $defaults ) ||
                 CRM_Utils_Array::value( 'is_deceased', $defaults ) ||
                 CRM_Utils_Array::value( 'birth_date' , $defaults ) ) {
                $showHide->addShow( 'demographics' );
                $showHide->addHide( 'demographics[show]' );
            } else {
                $showHide->addShow( 'demographics[show]' );
                $showHide->addHide( 'demographics' );
            }
        }

        // is there any notes data?
        /*
        if ( CRM_Utils_Array::value( 'notesCount', $defaults ) ) {
            $showHide->addShow( 'notes' );
            $showHide->addHide( 'notes[show]' );
        }
        */ 

        // is there any relationships data? for now, always hide relationships by default dgg.
        /*
        if ( CRM_Utils_Array::value( 'relationshipsCount', $defaults ) ) {
            $showHide->addShow( 'relationships' );
            $showHide->addHide( 'relationships[show]' );
        }
        */

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


    /**
     * get contact id for this page.
     *
     * @param none
     * @return int - contact id
     * @access public
     */
    function getContactId()
    {
        return $this->_contactId;
    }
}

?>
