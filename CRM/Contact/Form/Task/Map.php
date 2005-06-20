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
        $this->createLocationXML( $this->_contactIds );
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
        $emailAddress = null;
        if ( $this->_single ) {
            $emailAddress = $this->controller->exportValue( 'Email', 'to' );
        }
        $subject = $this->controller->exportValue( 'Email', 'subject' );
        $message = $this->controller->exportValue( 'Email', 'message' );

        list( $total, $sent, $notSent ) = CRM_Core_BAO_EmailHistory::sendEmail( $this->_contactIds, $subject, $message, $emailAddress );

        $status = array(
                        '',
                        ts('Total Selected Contact(s): %1', array(1 => $total))
                        );
        if ( $sent ) {
            $status[] = ts('Email sent to contact(s): %1', array(1 => $sent));
        }
        if ( $notSent ) {
            $status[] = ts('Email not sent to contact(s): %1', array(1 => $notSent));
        }
        CRM_Core_Session::setStatus( $status );
        
    }//end of function

    /**
     * create the smarty variables for the location XML
     *
     * @param array $contactIds list of contact ids that we need to plot
     *
     * @return string           the location of the file we have created
     * @access protected
     */
    function createLocationXML( $contactIds ) {
        $this->assign( 'title', 'CiviCRM Google Maps'  );
        $this->assign( 'query', 'CiviCRM Search Query' );
        
        $locations =& CRM_Contact_BAO_Contact::getMapInfo( $this->_contactIds );
        $this->assign_by_ref( 'locations', $locations );

        $sumLat = $sumLng = 0;
        $maxLat = $maxLng = -400;
        $minLat = $maxLat = +400;
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

        $config =& CRM_Core_Config::singleton( );
        $fp = fopen( $config->uploadDir . 'locations.xml', 'w' );
        fwrite( $fp, $xml );
        fclose( $fp );

        $this->assign( 'xmlURL', 'http://localhost/lobo/drupal/files/civicrm/upload/locations.xml' );
    }


}

?>
