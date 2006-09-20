<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Contact/Form/Task.php';

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
        $cid = CRM_Utils_Request::retrieve( 'cid', 'Positive',
                                            $this, false );
        $lid = CRM_Utils_Request::retrieve( 'lid', 'Positive',
                                            $this, false );
        if ( $cid ) {
            $this->_contactIds = array( $cid );
            $this->_single     = true;
        } else {
            parent::preProcess( );
        }
        self::createMapXML( $this->_contactIds, $lid, $this, true );
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
        $this->addButtons( array( 
                                 array ( 'type'      => 'done', 
                                         'name'      => ts('Done'), 
                                         'isDefault' => true   ), 
                                 ) 
                           ); 
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
     * @param int   $locationId location_id
     *
     * @return string           the location of the file we have created
     * @access protected
     */
    static function createMapXML( $contactIds, $locationId, &$page, $addBreadCrumb ) {
        $config =& CRM_Core_Config::singleton( );

        $page->assign( 'query', 'CiviCRM Search Query' );
        $page->assign( 'mapProvider', $config->mapProvider );
        $page->assign( 'mapKey', $config->mapAPIKey );
        $page->assign( 'mapGeoCoding', $config->mapGeoCoding );

        require_once 'CRM/Contact/BAO/Contact.php';
        $locations =& CRM_Contact_BAO_Contact::getMapInfo( $contactIds , $locationId );

        if ( empty( $locations ) ) {
            CRM_Utils_System::statusBounce(ts('This contact\'s primary address does not contain latitude/longitude information and can not be mapped.'));
        }

        if ( $addBreadCrumb ) {
            $session =& CRM_Core_Session::singleton(); 
            $redirect = $session->readUserContext(); 
            $additionalBreadCrumb = "<a href=\"$redirect\">" . ts('Search Results') . '</a>';
            CRM_Utils_System::appendBreadCrumb( $additionalBreadCrumb );
        }

        if ( $config->mapGeoCoding ) {
            $i=0;
            while($locations[$i]['address']) {
                self::geoCodeAddressFormat($locations[$i]);
                $i++;
            }
        }

        $page->assign_by_ref( 'locations', $locations );

        // only issue a javascript warning if we know we will not
        // mess the poor user with too many warnings
        if ( count( $locations ) <= 3 ) {
            $page->assign( 'geoCodeWarn', true );
        } else {
            $page->assign( 'geoCodeWarn', false );
        }

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
        $page->assign_by_ref( 'center', $center );
        $page->assign_by_ref( 'span'  , $span   );
    }

    /**
     * Formats the address as required by Google GeoCoder
     *
     * @access public
     * @return None
     */
    public function geoCodeAddressFormat( &$location ) {
        $location['geoCodeAddress'] = str_replace("<br />", ",", $location['address']);           
        $locs = explode(",", $location['geoCodeAddress']);
        $location['geoCodeAddress'] = ltrim($locs[0]).",".ltrim($locs[1]).",".ltrim($locs[2]);

        $dao =& new CRM_Core_DAO_Country( );
        $dao->name = ltrim($locs[4]);
        if ($dao->find(true)) {
            $location['geoCodeAddress'] = $location['geoCodeAddress'] . "," . $dao->iso_code;
        }
    }//end of function

}

?>
