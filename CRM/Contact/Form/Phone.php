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


Class CRM_Contact_Form_Phone 
{

    static function buildPhoneBlock($form, &$location, $locationId, $count, $showHideBlocks) {
        if ($count > 1) {
            $showHideBlocks->addShow("location[$locationId][phone][2][show]");
        }

        for ($i = 1; $i <= $count; $i++) {
            $label = ($i == 1) ? 'Preferred Phone:' : 'Other Phone:';

            $showHideBlocks->linksForArray( $form, $i, $count, "location[$locationId][phone]", '[+] another phone', '[-] hide phone');
            
            $location[$locationId]['phone'][$i]['phone_type_id'] = $form->addElement('select',
                                                                                     "location[$locationId][phone][$i][phone_type_id]",
                                                                                     null,
                                                                                     CRM_SelectValues::$phone);

            $attributes =& $form->getFormAttributes('CRM_Contact_DAO_Phone');
            $location[$locationId]['phone'][$i]['phone']      = $form->addElement('text',
                                                                                  "location[$locationId][phone][$i][phone]", 
                                                                                  $label,
                                                                                  $attributes['phone']);

            if ( $i != 1 ) {
                $showHideBlocks->addHide("location[$locationId][phone][$i]");
                $showHideBlocks->addHide("location[$locationId][phone][$i][show]");
            }
        }
    }

}


?>