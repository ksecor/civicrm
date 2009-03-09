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

require_once 'CRM/Core/Config.php';

/**
 * This class contains all the function that are called using AJAX (dojo)
 */
class CRM_Core_Page_AJAX_Location
{

    /**
     * Function to obtain the location of given contact-id. 
     * This method is used by on-behalf-of form to dynamically generate poulate the 
     * location field values for selected permissioned contact. 
     */
    function getPermissionedLocation( ) 
    {
        $cid = CRM_Utils_Type::escape( $_GET['cid'], 'Integer' );
        
        require_once 'CRM/Core/BAO/Location.php';
        $entityBlock = array( 'contact_id' => $cid );
        $loc =& CRM_Core_BAO_Location::getValues( $entityBlock, $location );

        $config =& CRM_Core_Config::singleton();
        $addressSequence = array_flip($config->addressSequence());
        
        $elements = array( "location_1_phone_1_phone" => 
                           $location['location'][1]['phone'][1]['phone'],
                           "location_1_email_1_email" => 
                           $location['location'][1]['email'][1]['email']
                           );
        
        if ( array_key_exists( 'street_address', $addressSequence) ) {
            $elements["location_1_address_street_address"] = $location['location'][1]['address']['street_address'];
        }
        if ( array_key_exists( 'supplemental_address_1', $addressSequence) ) {
            $elements['location_1_address_supplemental_address_1'] = 
                $location['location'][1]['address']['supplemental_address_1'];
        }
        if ( array_key_exists( 'supplemental_address_2', $addressSequence) ) {
            $elements['location_1_address_supplemental_address_2'] = 
                $location['location'][1]['address']['supplemental_address_2'];
        }
        if ( array_key_exists( 'city', $addressSequence) ) {
            $elements['location_1_address_city'] = $location['location'][1]['address']['city'];
        }
        if ( array_key_exists( 'postal_code', $addressSequence) ) {
            $elements['location_1_address_postal_code'] = 
                $location['location'][1]['address']['postal_code'];
            $elements['location_1_address_postal_code_suffix'] = 
                $location['location'][1]['address']['postal_code_suffix'];
        }
        if ( array_key_exists( 'country', $addressSequence) ) {
            $elements['location_1_address_country_id'] = 
                $location['location'][1]['address']['country_id'];
        }
        if ( array_key_exists( 'state_province', $addressSequence) ) {
            $elements['location_1_address_state_province_id'] = 
                $location['location'][1]['address']['state_province_id'];
        }

        echo json_encode( $elements );
        exit();
    }

    function jqState( &$config ) {
        if ( ! isset( $_GET['_value'] ) ||
        empty( $_GET['_value'] ) ) {
            exit();
        }

        require_once 'CRM/Core/PseudoConstant.php';
        $result =& CRM_Core_PseudoConstant::stateProvinceForCountry( $_GET['_value'] );

        $elements = array( array( 'name'  => ts('- select a state-'),
            'value' => '' ) );
        foreach ( $result as $id => $name ) {
            $elements[] = array( 'name'  => $name,
                'value' => $id );
        }

        require_once "CRM/Utils/JSON.php";
        echo json_encode( $elements );
        exit();
    }

    function getLocBlock( ) {
        if ( !isset($_POST['lbid']) ) {
            exit( );
        }

        // i wish i could retrieve loc block info based on loc_block_id, 
        // Anyway, lets retrieve an event which has loc_block_id set to 'lbid'.  
        $params  = array('1' => array($_POST['lbid'], 'Integer')); 
        $eventId = CRM_Core_DAO::singleValueQuery('SELECT id FROM civicrm_event WHERE loc_block_id=%1 LIMIT 1', $params);
        if ( !$eventId ) {
            exit( );
        }

        // now lets use the event-id obtained above, to retrieve loc block information.  
        $params = array( 'entity_id' => $eventId ,'entity_table' => 'civicrm_event');
        require_once 'CRM/Core/BAO/Location.php';
        // second parameter is of no use, but since required, lets use the same variable.
        $location = CRM_Core_BAO_Location::getValues($params, $params);

        // lets output only required fields. 
        $fields   = array( "location[1][address][street_address]",
                           "location[1][address][supplemental_address_1]",
                           "location[1][address][supplemental_address_2]",
                           "location[1][address][city]",
                           "location[1][address][postal_code]",
                           "location[1][address][postal_code_suffix]",
                           "location[1][address][country_id]",
                           "location[1][address][state_province_id]",
                           "location[1][address][geo_code_1]",
                           "location[1][address][geo_code_2]",
                           "location[1][email][1][email]",
                           "location[1][phone][1][phone_type_id]",
                           "location[1][phone][1][phone]" );
        $result = array( );
        foreach ( $fields as $fld ) {
            eval("\$value = \${$fld};");
            if ( $value ) {
                $result[str_replace( array('][', '[', "]"), array('_', '_', ''), $fld)] = $value;
            }
        }

        // set the message if loc block is being used by more than one event.
        require_once 'CRM/Event/BAO/Event.php';
        $result['count_loc_used'] = CRM_Event_BAO_Event::countEventsUsingLocBlockId( $_POST['lbid'] );

        echo json_encode( $result );
        exit();
    }
}
