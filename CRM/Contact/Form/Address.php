<?Php
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


Class CRM_Contact_Form_Address
{

    static function bab(& $loc, $form, $locid, & $start) {
        $j = $start;

        $loc[$locid][$j++] =  $form->createElement('text', 'street_address', 'Street Address:', array('size' => '47px', 'maxlength' => 96));
        $loc[$locid][$j++] =  $form->createElement('textarea', 'supplemental_address_1', 'Address:', array('cols' => '47', 'maxlength' => 96));
        $loc[$locid][$j++] =  $form->createElement('text', 'city', 'City:', array('maxlength' => 64));
        $loc[$locid][$j++] =  $form->createElement('text', 'postal_code', 'Zip / Postal Code:', array('maxlength' => 12));
        $loc[$locid][$j++] =  $form->createElement('select', 'state_province_id', 'State / Province:', CRM_SelectValues::$state);
        $loc[$locid][$j++] =  $form->createElement('select', 'country_id', 'Country:', CRM_SelectValues::$country);
        $start = $start + 6;

    }

}


?>