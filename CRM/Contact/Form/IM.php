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


class CRM_Contact_Form_IM
{

    static function buildImBlock(& $loc, $form, $locid, & $start, $count) {
        $j = $start;
        
        for ($i = 1; $i <= $count; $i++) {
            $label = 'Instant Message:';

            if ($i > 1) {
                if ($i != $count) {
                    $next = $i+1;
                    $scode = "show('expand_IM_{$locid}_{$next}'); return false;";
                    $hcode = "hide('IM_{$locid}_{$next}'); hide('expand_IM_{$locid}_{$next}'); return false;";                    
                }
                else { $scode = "return false;"; $hcode = "return false;"; }

                $form->addElement('link', "exim{$i}_{$locid}", null, 'IM_'."{$i}_{$locid}", '[+] another Instant message',
                                  array('onclick' => "show('IM_{$locid}_{$i}'); hide('expand_IM_{$locid}_{$i}');" . $scode));

                $form->addElement('link', "hideim{$i}_{$locid}", null, 'IM_'."{$locid}_{$i}", '[-] hide Instant message',
                                  array('onclick' => "hide('IM_{$locid}_{$i}'); show('expand_IM_{$locid}_{$i}');" . $hcode));
            }
 
            $loc[$locid][$j++] = $form->createElement('select', 'im_service_id_' . "{$i}", $label, CRM_SelectValues::$im   );
            $loc[$locid][$j++] = $form->createElement('text', 'im_screenname_' . "{$i}", null, array('size' => '37px', 'maxlength' => 64 ));
        }
        $start += $count*2;
    }

}


?>