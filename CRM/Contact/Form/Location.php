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
     * how many communicationBlocks should we display?
     *
     * @var int
     * @const
     */
    const BLOCKS = 1;
    
    static function &buildLocationBlock($form, $count, $showHideBlocks) 
    {
        $location = array();
        
        $showHideBlocks->addShow( 'location[1]' );
        
        if ( $count >= 2 ) {
            $showHideBlocks->addShow( 'location[2][show]' );
        }
        
        // this element is send to loop to display ($count -1) locations
        $form->assign( 'locationCount', $count + 1 );
        $form->assign( 'blockCount'   , self::BLOCKS + 1 );

        for ($locationId = 1; $locationId <= $count; $locationId++) {    
            $location[$locationId]['location_type_id'] =  $form->addElement('select'  , "location[$locationId][location_type_id]", null, CRM_SelectValues::$locationType);
            if ($count != 2 ) {
                $location[$locationId]['is_primary']       =  $form->addElement('checkbox', "location[$locationId][is_primary]", 'Primary location for this contact',  'Make this the primary location.', array('onchange' => "location_is_primary_onclick('" . $form->getName() . "', $locationId);" ) );
            }
            
            if ( $i != 1 ) {
                $showHideBlocks->addHide( "location[$locationId]" );
                $showHideBlocks->addHide( "location[$locationId][show]" );
            }

            CRM_Contact_Form_Address::buildAddressBlock($form, $location, $locationId, $showHideBlocks);

            CRM_Contact_Form_Phone::buildPhoneBlock($form, $location, $locationId, self::BLOCKS, $showHideBlocks); 
            CRM_Contact_Form_Email::buildEmailBlock($form, $location, $locationId, self::BLOCKS, $showHideBlocks); 
            CRM_Contact_Form_IM::buildIMBlock      ($form, $location, $locationId, self::BLOCKS, $showHideBlocks); 

            $showHideBlocks->linksForArray( $form, $locationId, $count, "location", '[+] another location', '[-] hide location');

        }
        return $location;
    }

}
?>