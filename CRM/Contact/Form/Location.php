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
require_once 'CRM/Contact/Form/Im.php';
require_once 'CRM/Contact/Form/Address.php';

class CRM_Contact_Form_Location extends CRM_Form
{

    //public static function buildLocationBlock($form)
    static function blb($form, $blocks) 
    {


        
        $form = $form;
        $blocks = $blocks;
        
        for ($i = 1; $i <= $blocks; $i++) {    

            $j = 0;

            $loc[$i][$j++] =  $form->createElement('select', 'location_type_id', null, CRM_SelectValues::$locationType);
            $loc[$i][$j++] =  $form->createElement('checkbox', 'is_primary', 'Primary location for this contact', null);

            CRM_Contact_Form_Phone::bpb($loc, $form, $i, $j, 3); 
            CRM_Contact_Form_Email::beb($loc, $form, $i, $j, 3);   
            CRM_Contact_Form_Im::bib($loc, $form, $i, $j, 3);
            CRM_Contact_Form_Address::bab($loc, $form, $i, $j);
            // total = 0 - 22
           
            if($i > 1) {
                if ($i == $blocks) {$next = $i; $prev = $i-1; $code = "return false;";} 
                else {$next = $i+1; $prev = $i+1; $code = "show('expand_loc{$next}'); return false;";}

                $form->addElement('link', 'exloc'."{$i}", null, 'location'."{$i}", '[+] another location',
                                  array( 'onclick' => "hide('expand_loc{$i}'); show('location{$i}');" . $code)); 

                $form->addElement('link', 'hideloc'."{$i}", null, 'location'."{$i}", '[-] hide location',
                                  array('onclick' => "hide('location{$i}'); show('expand_loc{$i}'); hide('expand_loc{$prev}'); return false;"));
            }
        }
        return $loc;
    }
}
?>