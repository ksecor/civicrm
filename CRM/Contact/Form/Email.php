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

class CRM_Contact_Form_Email 
{

    static function buildEmailBlock($form, &$location, $locationId, $count, $showHideBlocks) {
        if ($count > 1) {
            $showHideBlocks->addShow("location[$locationId][email][2][show]");
        }
        
        for ($i = 1; $i <= $count; $i++) {
            $label = ($i == 1) ? 'Email:' : 'Other Email:';

            $showHideBlocks->linksForArray( $form, $i, $count, "location[$locationId][email]", '[+] another email', '[-] hide email');
            
            $attributes =& $form->getFormAttributes('CRM_Contact_DAO_Email');
            $location[$locationId]['email'][$i]['email'] = $form->addElement('text', 
                                                                             "location[$locationId][email][$i][email]",
                                                                             $label,
                                                                             $attributes['email']);

            if ( $i != 1 ) {
                $showHideBlocks->addHide("location[$locationId][email][$i]");
                $showHideBlocks->addHide("location[$locationId][email][$i][show]");
            }

        }
    }
}


?>