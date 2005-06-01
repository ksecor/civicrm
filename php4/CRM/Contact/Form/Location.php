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
define( 'CRM_CONTACT_FORM_LOCATION_BLOCKS',3);

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/PseudoConstant.php';
require_once 'CRM/Contact/Form/Address.php';
require_once 'CRM/Contact/Form/Phone.php';
require_once 'CRM/Contact/Form/Email.php';
require_once 'CRM/Contact/Form/IM.php';
require_once 'CRM/Core/ShowHideBlocks.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/SelectValues.php';
require_once 'CRM/Contact/Form/Phone.php';
require_once 'CRM/Contact/Form/Email.php';
require_once 'CRM/Contact/Form/IM.php';
require_once 'CRM/Contact/Form/Address.php';

class CRM_Contact_Form_Location extends CRM_Core_Form
{
    /**
     * BLOCKS constant determines max number of Phone, Email and IM blocks to offer
     * within the Location section.
     *
     * @var int
     * @const
     */
       static $_commPrefs = array( 'phone', 'email', 'im' );

     function &buildLocationBlock($form, $maxLocationBlocks) 
    {
        $location = array();
        
        for ($locationId = 1; $locationId <= $maxLocationBlocks; $locationId++) {    
            $location[$locationId]['location_type_id'] =  $form->addElement('select'  , "location[$locationId][location_type_id]", null, CRM_Core_PseudoConstant::locationType());
            $location[$locationId]['is_primary']       =  $form->addElement('checkbox', "location[$locationId][is_primary]", 'Primary location for this contact',  'Primary location for this contact', array('onchange' => "location_is_primary_onclick('" . $form->getName() . "', $locationId);" ) );
            
            CRM_Contact_Form_Address::buildAddressBlock($form, $location, $locationId);

            CRM_Contact_Form_Phone::buildPhoneBlock($form, $location, $locationId, CRM_CONTACT_FORM_LOCATION_BLOCKS); 
            CRM_Contact_Form_Email::buildEmailBlock($form, $location, $locationId, CRM_CONTACT_FORM_LOCATION_BLOCKS); 
            CRM_Contact_Form_IM::buildIMBlock      ($form, $location, $locationId, CRM_CONTACT_FORM_LOCATION_BLOCKS); 

            CRM_Core_ShowHideBlocks::linksForArray( $form, $locationId, $maxLocationBlocks, "location", '', '');

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
            
            foreach ( $GLOBALS['_CRM_CONTACT_FORM_LOCATION']['_commPrefs'] as $block ) {
                for ( $blockId = 1; $blockId <= CRM_CONTACT_FORM_LOCATION_BLOCKS; $blockId++ ) {
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
     * @param CRM_Core_ShowHideBlocks $showHide the showHide object
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
            foreach ( $GLOBALS['_CRM_CONTACT_FORM_LOCATION']['_commPrefs'] as $block ) {
                CRM_Contact_Form_Location::updateShowHideSubBlocks( $showHide, $block, "location[$locationId]",
                                               CRM_Utils_Array::value( $block, $values[$locationId] ) );
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
            if ( $blockId < CRM_CONTACT_FORM_LOCATION_BLOCKS) {
                $nextBlockId = $blockId + 1;
                $showHide->addShow( "${prefix}[$name][$nextBlockId][show]" );
            }
        }
    }

}

?>