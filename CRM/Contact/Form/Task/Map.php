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

/**
 * This class provides the functionality to email a group of
 * contacts. 
 */
class CRM_Contact_Form_Task_Map  extends CRM_Contact_Form_Task {

    /**
     * Are we operating in "single mode", i.e. sending email to one
     * specific contact?
     *
     * @var boolean
     */
    protected $_single = false;

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess( ) {
        $cid = CRM_Utils_Request::retrieve( 'cid', $this, false );
        if ( $cid ) {
            $this->_contactIds = array( $cid );
            $this->_single     = true;
        } else {
            parent::preProcess( );
        }
        $this->createLocation( $this->_contactIds );
        $this->assign( 'single', $this->_single );
    }
    
    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    public function buildQuickForm()
    {
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess() {
           
    }//end of function


    /**
     * assign smarty variables to the template that will be used by google api to plot the contacts
     *
     * @param array $contactIds list of contact ids that we need to plot
     *
     * @return string           the location of the file we have created
     * @access protected
     */
    function createLocation( $contactIds ) {
        $this->assign( 'title', 'CiviCRM Google Maps'  );
        $this->assign( 'query', 'CiviCRM Search Query' );

        $this->assign( 'googleMapKey', CIVICRM_GOOGLE_MAP_API_KEY );
       
        $locations =& CRM_Contact_BAO_Contact::getMapInfo( $this->_contactIds );
        if ( empty( $locations ) ) {
            CRM_Core_Error::fatal( ts( 'The locations chosen did not have any coordinate information' ) );
        }

        $this->assign_by_ref( 'locations', $locations );

        
        $sumLat = $sumLng = 0;
        $maxLat = $maxLng = -400;
        $minLat = $minLng = +400;
        foreach ( $locations as $location ) {
            $sumLat += $location['lat'];
            $sumLng += $location['lng'];

            if ( $location['lat'] > $maxLat ) {
                $maxLat = $location['lat'];
            }
            if ( $location['lat'] < $minLat ) {
                $minLat = $location['lat'];
            }

            if ( $location['lng'] > $maxLng ) {
                $maxLng = $location['lng'];
            }
            if ( $location['lng'] < $minLng ) {
                $minLng = $location['lng'];
            }
        }

        $center = array( 'lat' => (float ) $sumLat / count( $locations ),
                         'lng' => (float ) $sumLng / count( $locations ) );
        $span   = array( 'lat' => (float ) ( $maxLat - $minLat ),
                         'lng' => (float ) ( $maxLng - $minLng ) );
        $this->assign_by_ref( 'center', $center );
        $this->assign_by_ref( 'span'  , $span   );
        
        $template =& CRM_Core_Smarty::singleton( );
        $xml = $template->fetch( 'CRM/Contact/Form/Task/GMapsInput.tpl' );
        
    }

}

?>
