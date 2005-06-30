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

//require_once 'CRM/Contact/Page/View.php';

/**
 * Main page for viewing contact.
 *
 */
class CRM_Contact_Page_View_Basic extends CRM_Contact_Page_View {

    /**
     * Heart of the viewing process. The runner gets all the meta data for
     * the contact and calls the appropriate type of page to view.
     *
     * @param none
     * @return none
     * @access public
     *
     */
    function run( )
    {
        $this->preProcess( );

        $params   = array( );
        $defaults = array( );
        $ids      = array( );

        $params['id'] = $params['contact_id'] = $this->_contactId;
        $contact = CRM_Contact_BAO_Contact::retrieve( $params, $defaults, $ids );

        CRM_Contact_BAO_Contact::resolveDefaults( $defaults );


        // get the list of all the categories
        $tag =& CRM_Core_PseudoConstant::tag();
        // get categories for the contact id
        $entityTag =& CRM_Contact_BAO_EntityTag::getTag('crm_contact', $this->_contactId);

        if ( $entityTag ) {
            $categories = array( );
            foreach ( $entityTag as $key ) {
                $categories[] = $tag[$key];
            }
            $defaults['contactTag'] = implode( ', ', $categories );
        }
        

        // enum localisation hacks
        $t = CRM_Core_SelectValues::gender();
        $defaults['gender']['gender'] = $t[$defaults['gender']['gender']];

        $t = CRM_Core_SelectValues::pcm();
        if ($defaults['preferred_communication_method'] != '') {
            $defaults['preferred_communication_method'] = $t[$defaults['preferred_communication_method']];
        }
        
        $defaults['privacy_values'] = CRM_Core_SelectValues::privacy();

        $this->assign( $defaults );

        $this->setShowHide( $defaults );

        return parent::run( );
    }



    /**
     * Show hide blocks based on default values.
     *
     * @param array (reference) $defaults
     * @return none
     * @access public
     */
    function setShowHide( &$defaults ) {
        $showHide =& new CRM_Core_ShowHideBlocks( array( 'commPrefs'           => 1,
                                                         'notes[show]'          => 1,
                                                         'relationships[show]'  => 1,
                                                         'groups[show]'         => 1,
                                                         'openActivities[show]' => 1,
                                                         'activityHx[show]'     => 1 ),
                                                  array( 'notes'                => 1,
                                                         'commPrefs[show]'      => 1,
                                                         'relationships'        => 1,
                                                         'groups'               => 1,
                                                         'openActivities'       => 1,
                                                         'activityHx'           => 1 ) );                                                      
        
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

}

?>
