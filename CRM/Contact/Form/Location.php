<?php
/**
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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */
require_once 'CRM/Form.php';
require_once 'CRM/SelectValues.php';
require_once 'CRM/Contact/Form/Phone.php';
require_once 'CRM/Contact/Form/Email.php';
require_once 'CRM/Contact/Form/IM.php';
require_once 'CRM/Contact/Form/Address.php';

class CRM_Contact_Form_Location extends CRM_Form
{
    /**
     * BLOCKS constant determines max number of Phone, Email and IM blocks to offer
     * within the Location section.
     *
     * @var int
     * @const
     */
    const BLOCKS = 2;

    static $_commPrefs = array( 'phone', 'email', 'im' );

    static function &buildLocationBlock($form, $maxLocationBlocks) 
    {
        $location = array();
        
        for ($locationId = 1; $locationId <= $maxLocationBlocks; $locationId++) {    
            $location[$locationId]['location_type_id'] =  $form->addElement('select'  , "location[$locationId][location_type_id]", null, CRM_SelectValues::$locationType);
            //if ($maxLocationBlocks != 2 ) {
                $location[$locationId]['is_primary']       =  $form->addElement('checkbox', "location[$locationId][is_primary]", 'Primary location for this contact',  'Make this the primary location.', array('onchange' => "location_is_primary_onclick('" . $form->getName() . "', $locationId);" ) );
            //}
            
            CRM_Contact_Form_Address::buildAddressBlock($form, $location, $locationId);

            CRM_Contact_Form_Phone::buildPhoneBlock($form, $location, $locationId, self::BLOCKS); 
            CRM_Contact_Form_Email::buildEmailBlock($form, $location, $locationId, self::BLOCKS); 
            CRM_Contact_Form_IM::buildIMBlock      ($form, $location, $locationId, self::BLOCKS); 

            CRM_ShowHideBlocks::linksForArray( $form, $locationId, $maxLocationBlocks, "location", '[+] another location', '[-] hide location');

        }
        return $location;
    }

    function setShowHideDefaults( $showHide, $maxLocationBlocks ) {
        for ($locationId = 1; $locationId <= $maxLocationBlocks; $locationId++) {
            if ( $locationId == 1 ) {
                $showHide->addShow( "location[$locationId]" );
            } else {
                $showHide->addHide( "location[$locationId]" );
                if ( $locationId == 2 ) {
                    $showHide->addShow( "location[$locationId][show]" );
                } else {
                    $showHide->addHide( "location[$locationId][show]" );
                }
            }
            
            foreach ( self::$_commPrefs as $block ) {
                for ( $blockId = 1; $blockId <= self::BLOCKS; $blockId++ ) {
                    if ( $blockId != 1 ) {
                        $showHide->addHide( "location[$locationId][$block][$blockId]");
                        if ( $blockId == 2 ) {
                            $showHide->addShow( "location[$locationId][$block][$blockId][show]" );
                        } else {
                            $showHide->addHide( "location[$locationId][$block][$blockId][show]" );
                        }
                    }
                }
            }
        }
    }

    /**
     * Fix what blocks to show/hide based on the default values set
     *
     * @param CRM_ShowHideBlocks $showHide the showHide object
     * @param array @defaults the array of default values
     *
     * @return void
     */
    function updateShowHide( $showHide, &$values, $maxLocationBlocks ) {
        if ( empty( $values ) ) {
            return;
        }

        $locationKeys = array_keys( $values );
        foreach ( $locationKeys as $locationId ) {
            if ( empty( $values[$locationId] ) ) {
                continue;
            }

            $showHide->addShow( "location[$locationId]" );
            if ( $locationId != 1 ) {
                $showHide->addHide( "location[$locationId][show]" );
            }
            if ( $locationId < $maxLocationBlocks ) {
                $nextLocationId = $locationId + 1;
                $showHide->addShow( "location[$nextLocationId][show]" );
            }

            $commPrefs = array( 'phone', 'email', 'im' );
            foreach ( self::$_commPrefs as $block ) {
                self::updateShowHideSubBlocks( $showHide, $block, "location[$locationId]",
                                               CRM_Array::value( $block, $values[$locationId] ) );
            }
        }
    }

    function updateShowHideSubBlocks( $showHide, $name, $prefix, &$values ) {
        if ( empty( $values ) ) {
            return;
        }

        $blockKeys = array_keys( $values );

        foreach ( $blockKeys as $blockId ) {
            if ( empty( $values[$blockId] ) ) {
                continue;
            }

            // blockId 1 is always shows
            if ( $blockId == 1 ) {
                continue;
            }

            $showHide->addShow( "${prefix}[$name][$blockId]" );
            $showHide->addHide( "${prefix}[$name][$blockId][show]" );
            if ( $blockId < self::BLOCKS ) {
                $nextBlockId = $blockId + 1;
                $showHide->addShow( "${prefix}[$name][$nextBlockId][show]" );
            }
        }
    }

}

?>