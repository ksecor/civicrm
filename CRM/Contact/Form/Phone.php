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


Class CRM_Contact_Form_Phone 
{

    static function bpb(& $loc, $form, $locid, & $start, $count) {
        $j = $start;
        
        for ($i = 1; $i <= $count; $i++) {
            
            if ($i > 1) {
                $label = "Other Phone:"; 


                if ($i != $count) {
                    $next = $i+1;
                    $scode = "show('expand_phone_{$locid}_{$next}'); return false;";
                    $hcode = "hide('phone_{$locid}_{$next}'); hide('expand_phone_{$locid}_{$next}'); return false;";                    
                }
                else { $scode = "return false;"; $hcode = "return false;"; }

                $form->addElement('link', "exph{$i}_{$locid}", null, 'phone_'."{$i}_{$locid}", '[+] another phone',
                                  array('onclick' => "show('phone_{$locid}_{$i}'); hide('expand_phone_{$locid}_{$i}');" . $scode));

                $form->addElement('link', "hideph{$i}_{$locid}", null, 'phone_'."{$locid}_{$i}", '[-] hide phone',
                                  array('onclick' => "hide('phone_{$locid}_{$i}'); show('expand_phone_{$locid}_{$i}');" . $hcode));


            }
            else
            {
                $label = 'Preferred Phone:';
            }
            
            $loc[$locid][$j++] = $form->createElement('select', 'phone_type_' . "{$i}", null, CRM_SelectValues::$phone);
            $loc[$locid][$j++] = $form->createElement('text', 'phone_' . "{$i}", $label, array('size' => '37px', 'maxlength' => 16));
        }

        $start += $count*2;

    }

}


?>